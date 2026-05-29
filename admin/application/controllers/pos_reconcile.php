<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * POS Reconciliation Controller
 * 
 * Background job that recovers "lost" payments — where the provider 
 * reports SUCCESS but our ERP still shows PENDING.
 * 
 * Can be called:
 *   1. Via cron:   php /path/to/index.php pos_reconcile run
 *   2. Via AJAX:   POST admin_pos/runReconciliation (manual trigger from Settlement page)
 */
class Pos_reconcile extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        ini_set('date.timezone', 'Asia/Calcutta');
        $this->load->model('pos_model');
        $this->load->model('ret_billing_model');
    }

    /**
     * Main reconciliation entry point (cron-safe)
     */
    function run()
    {
        $isCli = $this->input->is_cli_request();
        $results = $this->_reconcilePendingPayments();
        
        if($isCli){
            echo "=== POS Reconciliation Run: " . date('Y-m-d H:i:s') . " ===\n";
            echo "Checked: " . $results['checked'] . " pending sessions\n";
            echo "Recovered: " . $results['recovered'] . " payments\n";
            echo "Failed: " . $results['failed'] . " payments\n";
            echo "Expired: " . $results['expired'] . " sessions\n";
            if(!empty($results['details'])){
                foreach($results['details'] as $d){
                    echo "  Session #{$d['session_id']}: {$d['action']} ({$d['provider']})\n";
                }
            }
            echo "=== Done ===\n";
        } else {
            echo json_encode($results);
        }
    }

    /**
     * Core reconciliation logic
     */
    private function _reconcilePendingPayments()
    {
        $results = array(
            'checked' => 0,
            'recovered' => 0,
            'failed' => 0,
            'expired' => 0,
            'details' => array(),
            'timestamp' => date('Y-m-d H:i:s')
        );
        
        // Step 1: Expire stale sessions first
        $this->pos_model->expireStaleSessions();
        
        // Step 2: Get PENDING sessions older than 30 seconds
        $pendingSessions = $this->pos_model->getPendingSessionsForReconciliation();
        $results['checked'] = count($pendingSessions);
        
        if(empty($pendingSessions)){
            return $results;
        }
        
        // Step 3: For each pending session, check provider status
        require_once(APPPATH . 'libraries/POS_Provider_Loader.php');
        $loader = new POS_Provider_Loader();
        
        foreach($pendingSessions as $session){
            $detail = array(
                'session_id' => $session['session_id'],
                'provider' => $session['provider_code'],
                'amount' => $session['amount_paise'],
                'action' => 'SKIPPED'
            );
            
            $providerCode = $session['provider_code'];
            if(empty($providerCode)){
                $detail['action'] = 'SKIP_NO_PROVIDER';
                $results['details'][] = $detail;
                continue;
            }
            
            $provider = $loader->getProvider($providerCode);
            if(!$provider){
                $detail['action'] = 'SKIP_PROVIDER_NOT_FOUND';
                $results['details'][] = $detail;
                continue;
            }
            
            // Get full device details for status check
            $posDetails = $this->pos_model->getPOSDeviceDetails($session['device_id']);
            if(!$posDetails){
                $detail['action'] = 'SKIP_DEVICE_NOT_FOUND';
                $results['details'][] = $detail;
                continue;
            }
            
            // Find the corresponding pos_request to get refid
            $posReqId = $session['pos_req_id'];
            $refId = '';
            if($posReqId){
                $txn = $this->pos_model->getPOSTransactionById($posReqId);
                if($txn) $refId = isset($txn['pos_res_ref_id']) ? $txn['pos_res_ref_id'] : '';
            }
            
            if(empty($refId)){
                $detail['action'] = 'SKIP_NO_REFID';
                $results['details'][] = $detail;
                continue;
            }
            
            // Call provider status API
            try {
                $statusData = array(
                    'deviceId' => $session['device_id'],
                    'refcode' => $refId,
                    'cusid' => $session['customer_id']
                );
                
                $statusResult = $provider->checkStatus($statusData, $posDetails, $this);
                
                if(isset($statusResult['responsecode'])){
                    if($statusResult['responsecode'] == 1){
                        // SUCCESS — recover the payment!
                        $this->pos_model->updateSessionStatus($session['session_id'], 'SUCCESS');
                        
                        // Also update ret_pos_requests if needed
                        if($posReqId){
                            $this->pos_model->updateData(
                                array('pos_req_status' => 1),
                                'pos_req_id', $posReqId, 'ret_pos_requests'
                            );
                        }
                        
                        $detail['action'] = 'RECOVERED_SUCCESS';
                        $results['recovered']++;
                        
                        log_message('info', "[POS RECONCILE] Payment RECOVERED: session #{$session['session_id']}, ref=$refId, amount={$session['amount_paise']}");
                        
                    } elseif($statusResult['responsecode'] == 2 || $statusResult['responsecode'] == 3 || $statusResult['responsecode'] == -1){
                        // FAILED/CANCELLED — mark session
                        $this->pos_model->updateSessionStatus($session['session_id'], 'FAILED');
                        
                        if($posReqId){
                            $currentTxn = $this->pos_model->getPOSTransactionById($posReqId);
                            if($currentTxn && $currentTxn['pos_req_status'] == 0){
                                $this->pos_model->updateData(
                                    array('pos_req_status' => 3),
                                    'pos_req_id', $posReqId, 'ret_pos_requests'
                                );
                            }
                        }
                        
                        $detail['action'] = 'MARKED_FAILED';
                        $results['failed']++;
                        
                        log_message('info', "[POS RECONCILE] Payment FAILED: session #{$session['session_id']}, ref=$refId");
                        
                    } else {
                        // Still pending at provider — leave as is
                        $detail['action'] = 'STILL_PENDING';
                    }
                } else {
                    $detail['action'] = 'STATUS_UNKNOWN';
                }
                
            } catch(Exception $e) {
                $detail['action'] = 'ERROR: ' . $e->getMessage();
                log_message('error', "[POS RECONCILE] Error checking session #{$session['session_id']}: " . $e->getMessage());
            }
            
            $results['details'][] = $detail;
        }
        
        return $results;
    }

    /**
     * cURL helper — delegated from provider plugins
     */
    function postcurlPOSRequests($postData, $requrl)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $requrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
            CURLOPT_SSL_VERIFYPEER => false
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    function postcurlPhonePeRequests($postData, $requrl, $xVerify, $contentType = 'application/json')
    {
        $curl = curl_init();
        $headers = array(
            'Content-Type: ' . $contentType,
            'X-VERIFY: ' . $xVerify
        );
        curl_setopt_array($curl, array(
            CURLOPT_URL => $requrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => is_array($postData) ? json_encode($postData) : $postData,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    function getcurlPhonePeRequests($requrl, $xVerify)
    {
        $curl = curl_init();
        $headers = array(
            'Content-Type: application/json',
            'X-VERIFY: ' . $xVerify
        );
        curl_setopt_array($curl, array(
            CURLOPT_URL => $requrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPGET => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}
