<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * POS Controller — All POS-related endpoints
 * Separated from admin_ret_billing as part of POS Module Architecture
 * 
 * This handles:
 *   - Payment Init/Status/Cancel (via plugin architecture)
 *   - POS Settings CRUD (devices + providers)
 *   - Transaction Log & Audit Trail
 *   - PhonePe S2S Callback
 */
class Admin_pos extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        ini_set('date.timezone', 'Asia/Calcutta');
        $this->load->model('pos_model');
        // Also load ret_billing_model for shared helpers like insertData/updateData
        $this->load->model('ret_billing_model');
        // Required by layout/header.php for menu, company details, metal rates etc.
        $this->load->model('admin_settings_model');

        // Session & login guard (same as admin_ret_billing)
        if(!$this->session->userdata('is_logged'))
        {
            redirect('admin/login');
        }
        elseif($this->session->userdata('access_time_from') != NULL && $this->session->userdata('access_time_from') != "")
        {
            $now = time();
            $from = $this->session->userdata('access_time_from');
            $to = $this->session->userdata('access_time_to');
            $allowedAccess = ($now > $from && $now < $to) ? TRUE : FALSE;
            if($allowedAccess == FALSE){
                $this->session->set_flashdata('login_errMsg','Exceeded allowed access time!!');
                redirect('chit_admin/logout');
            }
        }
    }

    // ==========================================
    // MODULE CHECK
    // ==========================================

    private function _isPOSModuleEnabled()
    {
        return $this->pos_model->isPOSModuleEnabled();
    }

    /**
     * Admin Guard — uses the existing menu-based Access Rights system.
     * Checks if the current user's profile has 'view' permission for POS Settings
     * in the access table (manageable from Settings > Permission in the UI).
     * No hardcoded bypasses — the User Access page is the single source of truth.
     */
    private function _requireSuperAdmin($isAjax = false)
    {
        // Check the existing ACL system — POS Settings menu permission
        $access = $this->admin_settings_model->get_access('admin_pos/posSettings');
        $hasAccess = (!empty($access) && isset($access['view']) && $access['view'] == 1);

        // Debug log — remove after ACL is verified working
        $profile = $this->session->userdata('profile');
        log_message('debug', 'POS ACL CHECK: profile=' . $profile . ' access=' . json_encode($access) . ' hasAccess=' . ($hasAccess?'YES':'NO'));

        if(!$hasAccess){
            if($isAjax){
                echo json_encode(array('status' => 'error', 'message' => 'Unauthorized — You do not have access to POS Settings'));
            } else {
                $data['company'] = $this->admin_settings_model->get_company();
                $this->load->view('layout/header', $data);
                $this->load->view('errors/access_denied', $data);
                $this->load->view('layout/footer');
            }
            return false;
        }
        return true;
    }

    // ==========================================
    // PAYMENT ROUTERS — Dynamic Plugin Loading
    // ==========================================

    /** INIT PAYMENT — delegates to provider plugin via POS_Provider_Loader */
    function UploadBilledTransaction()
    {
        // Module check
        if(!$this->_isPOSModuleEnabled()){
            echo json_encode(array("responsecode" => -1, "resmessage" => "POS module is disabled", "refid" => null));
            return;
        }
        // Auth check
        if(!$this->session->userdata('uid')){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Unauthorized: Please login", "refid" => null));
            return;
        }
        
        $addData = $this->input->post('payTransData');
        if(!$addData || !isset($addData['deviceId'])){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Invalid request data", "refid" => null));
            return;
        }
        
        // Amount validation
        $amount = isset($addData['amount']) ? floatval($addData['amount']) : 0;
        if($amount <= 0){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Invalid amount", "refid" => null));
            return;
        }
        
        // Note: billid may be 0 for direct billing (bill not yet saved)
        // The frontend guards (total_cost > 0, balance > 0) handle validation
        
        $posDetails = $this->pos_model->getPOSDeviceDetails($addData['deviceId']);
        
        // Idempotency check (parameterized query to prevent SQL injection)
        if(!empty($addData['idempotency_key'])){
            $oldDebug = $this->db->db_debug; $this->db->db_debug = FALSE;
            $idemResult = $this->db->query("SELECT pos_req_id, pos_res_ref_id, pos_req_status FROM ret_pos_requests WHERE idempotency_key = ? LIMIT 1", array($addData['idempotency_key']));
            $this->db->db_debug = $oldDebug;
            if($idemResult && $existing = $idemResult->row_array()){
                echo json_encode(array("responsecode" => 0, "resmessage" => "Payment already initiated (idempotency)", "refid" => $existing['pos_res_ref_id']));
                return;
            }
        }
        
        // Auto-expire stale pending transactions (older than 3 minutes)
        // Pine Labs auto-cancels after AutoCancelDurationInMinutes (2 min), so 3 min is safe
        $this->pos_model->expireStalePOSTransactions($addData['cusid'], 3);
        
        // Duplicate payment protection — Auto-fail older pending transactions to allow retry
        $existingTxn = $this->pos_model->getActivePOSTransaction($addData['cusid']);
        if($existingTxn && $existingTxn['id_device'] == $addData['deviceId']) {
            $fullTxn = $this->pos_model->getPOSTransactionById($existingTxn['pos_req_id']);
            if($fullTxn) {
                $createdTime = strtotime($fullTxn['pos_req_createdon']);
                // If the pending transaction is older than 10 seconds, cashier is starting a new one; auto-fail the old one
                if((time() - $createdTime) > 10) {
                    $oldDebug = $this->db->db_debug; $this->db->db_debug = FALSE;
                    $this->db->query("UPDATE ret_pos_requests SET pos_req_status = 3 WHERE pos_req_id = ?", array($existingTxn['pos_req_id']));
                    $this->db->db_debug = $oldDebug;
                    log_message('info', 'POS: Auto-failed older pending transaction ID '.$existingTxn['pos_req_id'].' on device '.$addData['deviceId'].' to allow new payment.');
                    $existingTxn = null; // Unblocked!
                }
            }
        }
        
        if($existingTxn && $existingTxn['pos_req_amount'] == ($amount * 100) && $existingTxn['id_device'] == $addData['deviceId']) {
            echo json_encode(array("responsecode" => -2, "resmessage" => "An identical payment of Rs. ".$amount." is already in progress on this device (Ref: ".$existingTxn['pos_res_ref_id'].")", "refid" => null));
            return;
        }
        
        // --- SESSION LOCK: Check if bill is already locked ---
        $invoiceId = isset($addData['billid']) ? $addData['billid'] : '';
        $providerCode = !empty($posDetails['provider_code']) ? $posDetails['provider_code'] : 'pinelabs';
        
        if($invoiceId){
            $activeSession = $this->pos_model->getActiveSession($invoiceId);
            // Relax session lock: allow multiple sessions if they have different sequence numbers (distinct rows)
            // But we still block if the bill is 'critically' locked (e.g., final submission phase)
            // For now, allowing multiple rows to have parallel sessions.
            /*
            if($activeSession){
                echo json_encode(array("responsecode" => -2, "resmessage" => "Bill is locked — another payment is in progress (Session #".$activeSession['session_id'].")", "refid" => null));
                return;
            }
            */
            // Create session lock
            $sessionId = $this->pos_model->createPaymentSession(
                $invoiceId,
                $addData['cusid'],
                $addData['deviceId'],
                $providerCode,
                $amount * 100, // convert to paise
                $this->session->userdata('uid')
            );
        }
        
        // Dynamic provider loading
        require_once(APPPATH . 'libraries/POS_Provider_Loader.php');
        $loader = new POS_Provider_Loader();
        $provider = $loader->getProvider($providerCode);
        
        if($provider){
            $result = $provider->initPayment($addData, $posDetails, $this);
            
            // Link session to pos_req_id if we got a refid
            if($invoiceId && isset($sessionId) && $sessionId && isset($result['refid']) && $result['refid']){
                // Find the pos_req_id from the ref
                $txn = $this->pos_model->getPOSTransactionByRef($result['refid']);
                if($txn) $this->pos_model->updateSessionStatus($sessionId, 'PENDING', $txn['pos_req_id']);
            }
            
            // If init failed, release session lock
            if(isset($result['responsecode']) && $result['responsecode'] < 0 && $invoiceId && isset($sessionId) && $sessionId){
                $this->pos_model->updateSessionStatus($sessionId, 'FAILED');
            }
            
            echo json_encode($result);
            return;
        }
        
        // Provider not found — release lock
        if($invoiceId && isset($sessionId) && $sessionId){
            $this->pos_model->updateSessionStatus($sessionId, 'FAILED');
        }
        echo json_encode(array("responsecode" => -1, "resmessage" => "Unknown POS provider: $providerCode. Available: " . implode(', ', $loader->getRegisteredProviders()), "refid" => null));
    }

    /** CHECK STATUS — delegates to provider plugin */
    function getTransactionStatus()
    {
        if(!$this->session->userdata('uid')){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Unauthorized", "transdata" => null));
            return;
        }
        
        $addData = $this->input->post('payTransData');
        if(!$addData || !isset($addData['deviceId'])){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Invalid request data", "transdata" => null));
            return;
        }
        $posDetails = $this->pos_model->getPOSDeviceDetails($addData['deviceId']);
        
        // Audit trail
        if(!empty($addData['refcode'])){
            log_message('info', 'POS Status Check: refId='.$addData['refcode'].' by uid='.$this->session->userdata('uid'));
            $oldDebug = $this->db->db_debug; $this->db->db_debug = FALSE;
            $this->db->query("UPDATE ret_pos_requests SET pos_last_checked_by = ?, pos_last_checked_at = NOW() WHERE pos_res_ref_id = ?", array($this->session->userdata('uid'), $addData['refcode']));
            $this->db->db_debug = $oldDebug;
        }
        
        $providerCode = !empty($posDetails['provider_code']) ? $posDetails['provider_code'] : 'pinelabs';
        require_once(APPPATH . 'libraries/POS_Provider_Loader.php');
        $loader = new POS_Provider_Loader();
        $provider = $loader->getProvider($providerCode);
        
        if($provider){
            $result = $provider->checkStatus($addData, $posDetails, $this);
            
            // --- SESSION LOCK: On success, update session ---
            // providers return responsecode=0 for SUCCESS, 2 for PENDING, -1 for FAIL, -3 for EXPIRED
            if(isset($result['responsecode']) && $result['responsecode'] == 0){
                $billId = isset($addData['billid']) ? $addData['billid'] : '';
                if($billId){
                    $this->pos_model->updateActiveSessionByInvoice($billId, 'SUCCESS');
                }
            }
            // --- DB SYNC: On expired/failed, mark the DB record as FAILED so Retry is unblocked ---
            elseif(isset($result['responsecode']) && ($result['responsecode'] == -3 || (isset($result['expired']) && $result['expired']))){
                if(!empty($addData['refcode'])){
                    $oldDebug = $this->db->db_debug; $this->db->db_debug = FALSE;
                    $this->db->query("UPDATE ret_pos_requests SET pos_req_status = 3 WHERE pos_res_ref_id = ? AND pos_req_status = 0", array($addData['refcode']));
                    $this->db->db_debug = $oldDebug;
                    log_message('info', 'POS Expired: Marked refId='.$addData['refcode'].' as FAILED(3) in DB');
                }
                $billId = isset($addData['billid']) ? $addData['billid'] : '';
                if($billId){
                    $this->pos_model->updateActiveSessionByInvoice($billId, 'EXPIRED');
                }
            }
            
            echo json_encode($result);
            return;
        }
        
        echo json_encode(array("responsecode" => -1, "resmessage" => "Unknown POS provider", "transdata" => null));
    }

    /** CANCEL PAYMENT — delegates to provider plugin */
    function cancelTransactionRequest()
    {
        if(!$this->session->userdata('uid')){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Unauthorized"));
            return;
        }
        
        $addData = $this->input->post('payTransData');
        if(!$addData || !isset($addData['deviceId'])){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Invalid request data"));
            return;
        }
        $posDetails = $this->pos_model->getPOSDeviceDetails($addData['deviceId']);
        
        // Block cancel on completed transactions
        if(!empty($addData['refcode'])) {
            $txn = $this->pos_model->getPOSTransactionByRef($addData['refcode']);
            if($txn && $txn['pos_req_status'] == 1) {
                echo json_encode(array("responsecode" => -1, "resmessage" => "Cannot cancel a completed transaction"));
                return;
            }
        }
        
        // Audit trail
        if(!empty($addData['refcode'])){
            log_message('info', 'POS Cancel Request: refId='.$addData['refcode'].' by uid='.$this->session->userdata('uid'));
            $oldDebug = $this->db->db_debug; $this->db->db_debug = FALSE;
            $this->db->query("UPDATE ret_pos_requests SET pos_cancelled_by = ?, pos_cancelled_at = NOW() WHERE pos_res_ref_id = ?", array($this->session->userdata('uid'), $addData['refcode']));
            $this->db->db_debug = $oldDebug;
        }
        
        $providerCode = !empty($posDetails['provider_code']) ? $posDetails['provider_code'] : 'pinelabs';
        require_once(APPPATH . 'libraries/POS_Provider_Loader.php');
        $loader = new POS_Provider_Loader();
        $provider = $loader->getProvider($providerCode);
        
        if($provider){
            $result = $provider->cancelPayment($addData, $posDetails, $this);
            
            // --- SESSION LOCK: Release on cancel ---
            $billId = isset($addData['billid']) ? $addData['billid'] : '';
            if($billId){
                $this->pos_model->updateActiveSessionByInvoice($billId, 'CANCELLED');
            }
            
            // --- LOCAL OVERRIDE: If cancel fails (e.g. device off), mark locally as FAILED to unblock retry ---
            if(isset($result['responsecode']) && $result['responsecode'] != 0){
                if(!empty($addData['refcode'])){
                    $txn = $this->pos_model->getPOSTransactionByRef($addData['refcode']);
                    if($txn && $txn['pos_req_status'] == 0){
                        $oldDebug = $this->db->db_debug; $this->db->db_debug = FALSE;
                        $this->db->query("UPDATE ret_pos_requests SET pos_req_status = 3 WHERE pos_res_ref_id = ?", array($addData['refcode']));
                        $this->db->db_debug = $oldDebug;
                        log_message('info', 'POS Cancel failed: Marked refId='.$addData['refcode'].' as FAILED(3) in DB to unblock');
                        
                        // Override result to let UI transition smoothly
                        $result['responsecode'] = 0;
                        $result['resmessage'] = 'Cancelled locally (device offline/unreachable)';
                    }
                }
            }
            
            echo json_encode($result);
            return;
        }
        
        echo json_encode(array("responsecode" => -1, "resmessage" => "Unknown POS provider", "transdata" => null));
    }

    /** Get device list for billing dropdown */
    function getposdevicelists()
    {
        $model = "pos_model";
        echo json_encode($this->$model->getPOSDeviceList());
    }

    // ==========================================
    // CURL HELPERS — Used by provider plugins
    // ==========================================

    /** Pine Labs cURL (plain JSON POST) */
    function postcurlPOSRequests($postData, $requrl)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $requrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true); 
    }

    /** PhonePe cURL (GET/POST with X-VERIFY headers) */
    function phonepeCurlRequest($url, $method = 'POST', $body = null, $headers = array())
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_URL => $url, CURLOPT_RETURNTRANSFER => true, CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10, CURLOPT_TIMEOUT => 60, CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers, CURLOPT_SSL_VERIFYPEER => true,
        );
        if($method == 'POST' && $body !== null) $options[CURLOPT_POSTFIELDS] = $body;
        curl_setopt_array($curl, $options);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err = curl_error($curl);
        curl_close($curl);
        
        if($err){
            log_message('error', 'PhonePe cURL Error: ' . $err . ' | URL: ' . $url);
            return array('success' => false, 'code' => 'CURL_ERROR', 'message' => $err);
        }
        $decoded = json_decode($response, true);
        if($decoded === null){
            log_message('error', 'PhonePe invalid JSON: ' . substr($response, 0, 500) . ' | HTTP: ' . $httpCode);
            return array('success' => false, 'code' => 'INVALID_RESPONSE', 'message' => 'Invalid response');
        }
        return $decoded;
    }

    // ==========================================
    // PHONEPE S2S CALLBACK
    // ==========================================

    function phonepeCallback()
    {
        $rawBody = file_get_contents('php://input');
        log_message('info', 'PhonePe Callback RAW: ' . substr($rawBody, 0, 2000));
        $data = json_decode($rawBody, true);
        
        if(!$data || !isset($data['response'])){
            log_message('error', 'PhonePe Callback: Missing response field in body');
            http_response_code(400);
            echo json_encode(array('status' => 'error', 'message' => 'Invalid callback data'));
            return;
        }
        
        // Verify X-VERIFY signature
        // PhonePe S2S Callback docs: X-VERIFY = SHA256(response + saltKey) + "###" + saltIndex
        // Note: NO path/endpoint in the hash — only response + saltKey
        $xVerifyHeader = isset($_SERVER['HTTP_X_VERIFY']) ? $_SERVER['HTTP_X_VERIFY'] : '';
        $saltKeyData = $this->pos_model->getPhonePeSaltKeyWithIndex();
        $saltKey = $saltKeyData['salt_key'];
        $saltIndex = $saltKeyData['salt_index'];
        
        if(!empty($saltKey) && !empty($xVerifyHeader)) {
            $expectedHash = hash('sha256', $data['response'] . $saltKey) . '###' . $saltIndex;
            if($xVerifyHeader !== $expectedHash) {
                log_message('error', 'PhonePe Callback: X-VERIFY mismatch | Expected: ' . $expectedHash . ' | Got: ' . $xVerifyHeader);
                http_response_code(401);
                echo json_encode(array('status' => 'error', 'message' => 'Invalid signature'));
                return;
            }
            log_message('info', 'PhonePe Callback: X-VERIFY signature verified successfully');
        } else {
            log_message('warn', 'PhonePe Callback: X-VERIFY skipped — saltKey or header missing');
        }
        
        $responsePayload = json_decode(base64_decode($data['response']), true);
        if(!$responsePayload || !isset($responsePayload['data']['transactionId'])){
            log_message('error', 'PhonePe Callback: Invalid payload after base64 decode');
            http_response_code(400);
            echo json_encode(array('status' => 'error', 'message' => 'Invalid response payload'));
            return;
        }
        
        $transactionId = $responsePayload['data']['transactionId'];
        $paymentState = isset($responsePayload['data']['paymentState']) ? $responsePayload['data']['paymentState'] : '';
        $code = isset($responsePayload['code']) ? $responsePayload['code'] : '';
        $utr = '';
        $cardLast4 = '';
        
        // DQR callbacks use paymentModes
        if(isset($responsePayload['data']['paymentModes']) && is_array($responsePayload['data']['paymentModes'])){
            foreach($responsePayload['data']['paymentModes'] as $pm){
                if(isset($pm['utr'])) $utr = $pm['utr'];
            }
        }
        // IEDC callbacks use paymentInstruments
        if(isset($responsePayload['data']['paymentInstruments']) && is_array($responsePayload['data']['paymentInstruments'])){
            foreach($responsePayload['data']['paymentInstruments'] as $pi){
                if(isset($pi['upiTransactionId']) && empty($utr)) $utr = $pi['upiTransactionId'];
                if(isset($pi['last4Digits'])) $cardLast4 = $pi['last4Digits'];
            }
        }
        
        $updateData = array(
            'pos_callback_data' => $rawBody,
            'pos_res_trans_data' => json_encode($responsePayload['data']),
            'pos_utr' => $utr,
            'pos_callback_at' => date('Y-m-d H:i:s'),
        );
        if(!empty($cardLast4)) $updateData['card_last4'] = $cardLast4;
        
        // Map ALL terminal states — not just COMPLETED
        if($paymentState == 'COMPLETED' || $code == 'PAYMENT_SUCCESS') {
            $updateData['pos_req_status'] = 1;
            $updateData['pos_success_at'] = date('Y-m-d H:i:s');
            // pos_success_by = NULL for S2S callback (no user session)
        } elseif($paymentState == 'FAILED' || $paymentState == 'DECLINED' || $code == 'PAYMENT_ERROR' || $code == 'PAYMENT_DECLINED') {
            $updateData['pos_req_status'] = 3;
        } elseif($paymentState == 'CANCELLED' || $code == 'PAYMENT_CANCELLED') {
            $updateData['pos_req_status'] = 2;
        }
        
        $this->pos_model->updateData($updateData, 'pos_res_ref_id', $transactionId, 'ret_pos_requests');
        
        // Cross-check amount (PhonePe recommendation)
        if(isset($responsePayload['data']['amount'])){
            $callbackAmount = intval($responsePayload['data']['amount']);
            $dbTxn = $this->pos_model->getPOSTransactionByRef($transactionId);
            if($dbTxn && intval($dbTxn['pos_req_amount']) != $callbackAmount){
                log_message('error', 'PhonePe Callback AMOUNT MISMATCH: txn=' . $transactionId . ' requested=' . $dbTxn['pos_req_amount'] . ' callback=' . $callbackAmount);
            }
        }
        
        http_response_code(200);
        echo json_encode(array('status' => 'success'));
        log_message('info', 'PhonePe Callback: txn=' . $transactionId . ' state=' . $paymentState . ' code=' . $code . ' utr=' . $utr);
    }

    // ==========================================
    // POS SETTINGS PAGE — CRUD
    // ==========================================

    function posSettings()
    {
        if(!$this->_requireSuperAdmin()) return;
        $data['providers'] = $this->pos_model->getAllPOSProviders();
        $data['devices']   = $this->pos_model->getAllPOSDevicesWithProvider();
        $data['pay_devices'] = $this->db->query("SELECT id_device, device_name FROM ret_bill_pay_device WHERE status=1 ORDER BY device_name")->result_array();
        $this->load->view('layout/header', $data);
        $this->load->view('billing/pos_settings', $data);
        $this->load->view('layout/footer');
    }

    function getPOSDeviceById()
    {
        if(!$this->_requireSuperAdmin(true)) return;
        echo json_encode($this->pos_model->getPOSDeviceById($this->input->post('id_device')));
    }

    function savePOSDevice()
    {
        if(!$this->_requireSuperAdmin(true)) return;
        $data = array(
            'dispname' => $this->input->post('dispname'), 'id_provider' => $this->input->post('id_provider'),
            'merchantid' => $this->input->post('merchantid'), 'devicetype' => $this->input->post('devicetype'),
            'is_default' => $this->input->post('is_default'), 'securitytoken' => $this->input->post('securitytoken'),
            'imei' => $this->input->post('imei'), 'poscode' => $this->input->post('poscode'),
            'salt_key' => $this->input->post('salt_key'), 'salt_index' => $this->input->post('salt_index'),
            'provider_id' => $this->input->post('provider_id'), 'store_id' => $this->input->post('store_id'),
            'terminal_id' => $this->input->post('terminal_id'), 'callback_url' => $this->input->post('callback_url'),
            'id_pay_device' => $this->input->post('id_pay_device') ? intval($this->input->post('id_pay_device')) : null,
            'is_active' => 1,
        );
        $id = $this->pos_model->savePOSDevice($data);
        echo json_encode(array('status' => 'success', 'message' => 'Device saved successfully', 'id' => $id));
    }

    function updatePOSDevice()
    {
        if(!$this->_requireSuperAdmin(true)) return;
        $deviceId = $this->input->post('id_device');
        $data = array(
            'dispname' => $this->input->post('dispname'), 'id_provider' => $this->input->post('id_provider'),
            'merchantid' => $this->input->post('merchantid'), 'devicetype' => $this->input->post('devicetype'),
            'is_default' => $this->input->post('is_default'), 'securitytoken' => $this->input->post('securitytoken'),
            'imei' => $this->input->post('imei'), 'poscode' => $this->input->post('poscode'),
            'salt_key' => $this->input->post('salt_key'), 'salt_index' => $this->input->post('salt_index'),
            'provider_id' => $this->input->post('provider_id'), 'store_id' => $this->input->post('store_id'),
            'terminal_id' => $this->input->post('terminal_id'), 'callback_url' => $this->input->post('callback_url'),
            'id_pay_device' => $this->input->post('id_pay_device') ? intval($this->input->post('id_pay_device')) : null,
        );
        $this->pos_model->updatePOSDevice($deviceId, $data);
        echo json_encode(array('status' => 'success', 'message' => 'Device updated successfully'));
    }

    function deletePOSDevice()
    {
        if(!$this->_requireSuperAdmin(true)) return;
        $this->pos_model->deletePOSDevice($this->input->post('id_device'));
        echo json_encode(array('status' => 'success', 'message' => 'Device deactivated'));
    }

    function setDefaultPOSDevice()
    {
        if(!$this->_requireSuperAdmin(true)) return;
        $this->pos_model->setDefaultPOSDevice($this->input->post('id_device'));
        echo json_encode(array('status' => 'success', 'message' => 'Default device updated'));
    }

    function toggleProviderEnv()
    {
        if(!$this->_requireSuperAdmin(true)) return;
        $this->pos_model->toggleProviderEnv($this->input->post('id_provider'), $this->input->post('is_env_live'));
        echo json_encode(array('status' => 'success', 'message' => 'Environment toggled'));
    }

    function getPOSProviderById()
    {
        if(!$this->_requireSuperAdmin(true)) return;
        echo json_encode($this->pos_model->getPOSProviderById($this->input->post('id_provider')));
    }

    function savePOSProvider()
    {
        if(!$this->_requireSuperAdmin(true)) return;
        $data = array(
            'provider_name' => $this->input->post('provider_name'), 'provider_code' => $this->input->post('provider_code'),
            'auth_type' => $this->input->post('auth_type'),
            'api_url_uat_init' => $this->input->post('api_url_uat_init'), 'api_url_uat_status' => $this->input->post('api_url_uat_status'),
            'api_url_uat_cancel' => $this->input->post('api_url_uat_cancel'), 'api_url_live_init' => $this->input->post('api_url_live_init'),
            'api_url_live_status' => $this->input->post('api_url_live_status'), 'api_url_live_cancel' => $this->input->post('api_url_live_cancel'),
            'is_env_live' => $this->input->post('is_env_live') ? 1 : 0, 'has_qr_display' => $this->input->post('has_qr_display') ? 1 : 0,
            'has_callback' => $this->input->post('has_callback') ? 1 : 0, 'is_active' => $this->input->post('is_active') ? 1 : 0,
        );
        $id = $this->pos_model->savePOSProvider($data);
        echo json_encode(array('status' => 'success', 'message' => 'Provider saved', 'id' => $id));
    }

    function updatePOSProvider()
    {
        if(!$this->_requireSuperAdmin(true)) return;
        $providerId = $this->input->post('id_provider');
        $data = array(
            'provider_name' => $this->input->post('provider_name'), 'provider_code' => $this->input->post('provider_code'),
            'auth_type' => $this->input->post('auth_type'),
            'api_url_uat_init' => $this->input->post('api_url_uat_init'), 'api_url_uat_status' => $this->input->post('api_url_uat_status'),
            'api_url_uat_cancel' => $this->input->post('api_url_uat_cancel'), 'api_url_live_init' => $this->input->post('api_url_live_init'),
            'api_url_live_status' => $this->input->post('api_url_live_status'), 'api_url_live_cancel' => $this->input->post('api_url_live_cancel'),
            'is_env_live' => $this->input->post('is_env_live') ? 1 : 0, 'has_qr_display' => $this->input->post('has_qr_display') ? 1 : 0,
            'has_callback' => $this->input->post('has_callback') ? 1 : 0, 'is_active' => $this->input->post('is_active') ? 1 : 0,
        );
        $this->pos_model->updatePOSProvider($providerId, $data);
        echo json_encode(array('status' => 'success', 'message' => 'Provider updated'));
    }

    // ==========================================
    // POS TRANSACTION LOG
    // ==========================================

    function posTransactions()
    {
        $dateInput = $this->input->get_post('date');
        $dateToInput = $this->input->get_post('date_to');
        
        // Helper: parse dd-mm-yyyy to yyyy-mm-dd
        $parseDMY = function($raw) {
            if(!$raw) return null;
            $cleaned = str_replace('/', '-', trim($raw));
            if(preg_match('/^(\d{2})-(\d{2})-(\d{4})$/', $cleaned, $m)){
                return $m[3].'-'.$m[2].'-'.$m[1];
            }
            return date('Y-m-d', strtotime($cleaned));
        };

        $dateStr = $dateInput ? $parseDMY($dateInput) : date('Y-m-d');
        $dateEndStr = $dateToInput ? $parseDMY($dateToInput) : null;

        log_message('debug', 'POS Report: dateInput='.$dateInput.' dateToInput='.$dateToInput.' => dateStr='.$dateStr.' dateEndStr='.$dateEndStr);
        $data['report'] = $this->pos_model->getPaymentReport($dateStr, $dateEndStr);
        $data['report_date'] = date('d-m-Y', strtotime($dateStr));
        $data['report_date_to'] = $dateEndStr ? date('d-m-Y', strtotime($dateEndStr)) : '';
        // Load branches from master branch table
        $data['master_branches'] = $this->db->query("SELECT id_branch, name, short_name FROM branch WHERE active = 1 ORDER BY name")->result_array();
        // Load company for branding
        $this->load->model('admin_settings_model');
        $data['company'] = $this->admin_settings_model->get_company();
        $this->load->view('layout/header', $data);
        $this->load->view('billing/pos_transactions', $data);
        $this->load->view('layout/footer');
    }

    function getPOSTransactionDetail()
    {
        echo json_encode($this->pos_model->getPOSTransactionById($this->input->post('id')));
    }

    function keepAlive()
    {
        echo json_encode(array('status' => 'ok', 'time' => date('Y-m-d H:i:s')));
    }

    function getPaymentReportAjax()
    {
        if(!$this->session->userdata('uid')){
            echo json_encode(array('status' => 'error', 'message' => 'Unauthorized'));
            return;
        }
        $dateStr = $this->input->post('date') ? date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('date')))) : date('Y-m-d');
        $report = $this->pos_model->getPaymentReport($dateStr);
        echo json_encode(array('status' => 'success', 'data' => $report));
    }

    function posSettlementSummary()
    {
        if(!$this->session->userdata('uid')){
            echo json_encode(array('status' => false, 'message' => 'Unauthorized'));
            return;
        }
        try {
            $fromDate = $this->input->post('from_date') ? date('Y-m-d', strtotime($this->input->post('from_date'))) : date('Y-m-d');
            $toDate   = $this->input->post('to_date')   ? date('Y-m-d', strtotime($this->input->post('to_date')))   : date('Y-m-d');
            $data = $this->pos_model->getPOSSettlementSummary($fromDate, $toDate);
            
            $totals = array('total_count'=>0,'success_count'=>0,'failed_count'=>0,'pending_count'=>0,'cancelled_count'=>0,'total_amount'=>0,'success_amount'=>0,'failed_amount'=>0,'pending_amount'=>0);
            foreach($data as $row){
                $count = intval($row['txn_count']); $amount = floatval($row['total_paise'])/100;
                $totals['total_count'] += $count; $totals['total_amount'] += $amount;
                switch(intval($row['pos_req_status'])){
                    case 1: $totals['success_count'] += $count; $totals['success_amount'] += $amount; break;
                    case 2: $totals['cancelled_count'] += $count; break;
                    case 3: $totals['failed_count'] += $count; $totals['failed_amount'] += $amount; break;
                    default: $totals['pending_count'] += $count; $totals['pending_amount'] += $amount; break;
                }
            }
            echo json_encode(array('status' => true, 'breakdown' => $data, 'totals' => $totals, 'from_date' => $fromDate, 'to_date' => $toDate));
        } catch(Exception $e) {
            echo json_encode(array('status' => false, 'message' => 'Server error: '.$e->getMessage()));
        }
    }

    function posAuditTrail()
    {
        if(!$this->session->userdata('uid')){
            echo json_encode(array('status' => false, 'message' => 'Unauthorized'));
            return;
        }
        try {
            $fromDate = $this->input->post('from_date') ? date('Y-m-d', strtotime($this->input->post('from_date'))) : date('Y-m-d');
            $toDate   = $this->input->post('to_date')   ? date('Y-m-d', strtotime($this->input->post('to_date')))   : date('Y-m-d');
            $data = $this->pos_model->getPOSAuditTrail($fromDate, $toDate);
            echo json_encode(array('status' => true, 'data' => $data));
        } catch(Exception $e) {
            echo json_encode(array('status' => false, 'message' => 'Server error: '.$e->getMessage()));
        }
    }
    function posSettlementPage()
    {
        // Unified into Payment Report page
        redirect('admin_pos/posTransactions');
    }

    function getEODData()
    {
        if(!$this->session->userdata('uid')){
            echo json_encode(array('status' => 'error', 'message' => 'Unauthorized'));
            return;
        }
        try {
            $dateStr = $this->input->post('date') ? date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('date')))) : date('Y-m-d');
            
            $result = $this->pos_model->getEODBreakdown($dateStr);
            
            // Build summary
            $summary = array('total' => 0, 'success_amount' => 0, 'failed' => 0, 'pending' => 0);
            foreach($result['breakdown'] as $row){
                $summary['total'] += $row['total'];
                $summary['success_amount'] += $row['success_amount'];
                $summary['failed'] += $row['failed'];
                $summary['pending'] += $row['pending'];
            }
            
            echo json_encode(array(
                'status' => 'success',
                'summary' => $summary,
                'breakdown' => $result['breakdown'],
                'transactions' => $result['transactions']
            ));
        } catch(Exception $e) {
            echo json_encode(array('status' => 'error', 'message' => 'Server error: '.$e->getMessage()));
        }
    }
    // ==========================================
    // SESSION LOCK CHECK (AJAX — called by billing JS)
    // ==========================================

    function checkSessionLock()
    {
        if(!$this->session->userdata('uid')){
            echo json_encode(array('locked' => false));
            return;
        }
        
        $invoiceId = $this->input->post('invoice_id');
        $customerId = $this->input->post('customer_id');
        
        $session = null;
        if($invoiceId){
            $session = $this->pos_model->getActiveSession($invoiceId);
        } elseif($customerId){
            $session = $this->pos_model->getActiveSessionByCustomer($customerId);
        }
        
        if($session){
            echo json_encode(array(
                'locked' => true,
                'session_id' => $session['session_id'],
                'device_id' => $session['device_id'],
                'provider_code' => $session['provider_code'],
                'amount_paise' => $session['amount_paise'],
                'status' => $session['status'],
                'created_at' => $session['created_at'],
                'expires_at' => $session['expires_at']
            ));
        } else {
            echo json_encode(array('locked' => false));
        }
    }

    // ==========================================
    // MANUAL RECONCILIATION (AJAX trigger)
    // ==========================================

    function runReconciliation()
    {
        if(!$this->session->userdata('uid')){
            echo json_encode(array('status' => 'error', 'message' => 'Unauthorized'));
            return;
        }
        
        // Load and run the reconciliation controller logic inline
        require_once(APPPATH . 'libraries/POS_Provider_Loader.php');
        
        // Expire stale sessions
        $this->pos_model->expireStaleSessions();
        
        // Get pending sessions
        $pendingSessions = $this->pos_model->getPendingSessionsForReconciliation();
        $results = array('checked' => count($pendingSessions), 'recovered' => 0, 'failed' => 0, 'details' => array());
        
        $loader = new POS_Provider_Loader();
        
        foreach($pendingSessions as $session){
            $providerCode = $session['provider_code'];
            if(empty($providerCode)) continue;
            
            $provider = $loader->getProvider($providerCode);
            if(!$provider) continue;
            
            $posDetails = $this->pos_model->getPOSDeviceDetails($session['device_id']);
            if(!$posDetails) continue;
            
            $refId = '';
            if($session['pos_req_id']){
                $txn = $this->pos_model->getPOSTransactionById($session['pos_req_id']);
                if($txn) $refId = isset($txn['pos_res_ref_id']) ? $txn['pos_res_ref_id'] : '';
            }
            if(empty($refId)) continue;
            
            try {
                $statusData = array('deviceId' => $session['device_id'], 'refcode' => $refId, 'cusid' => $session['customer_id']);
                $statusResult = $provider->checkStatus($statusData, $posDetails, $this);
                
                if(isset($statusResult['responsecode']) && $statusResult['responsecode'] == 1){
                    $this->pos_model->updateSessionStatus($session['session_id'], 'SUCCESS');
                    $results['recovered']++;
                    $results['details'][] = array('session' => $session['session_id'], 'action' => 'RECOVERED');
                } elseif(isset($statusResult['responsecode']) && in_array($statusResult['responsecode'], array(-1, 2, 3))){
                    $this->pos_model->updateSessionStatus($session['session_id'], 'FAILED');
                    $results['failed']++;
                    $results['details'][] = array('session' => $session['session_id'], 'action' => 'FAILED');
                }
            } catch(Exception $e) {
                log_message('error', "[POS RECONCILE] Error: " . $e->getMessage());
            }
        }
        
        echo json_encode(array('status' => 'success', 'results' => $results));
    }

    // ==========================================
    // ORPHAN DETECTION (Phase 3)
    // ==========================================

    /**
     * Run orphan detection and return results
     * Called from admin dashboard or cron
     */
    function orphanDetection()
    {
        if(!$this->session->userdata('uid')){
            echo json_encode(array('status' => 'error', 'message' => 'Unauthorized'));
            return;
        }
        
        $daysBack = $this->input->post('days_back') ? intval($this->input->post('days_back')) : 7;
        
        $oldDebug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        
        try {
            // Type 1: POS succeeded but no billing payment record
            $orphanPayments = $this->pos_model->findOrphanPayments($daysBack);
            
            // Type 2: Billing payment has pos_req_id but no matching POS record
            $orphanBilling = $this->pos_model->findOrphanBillingPayments($daysBack);
            
            // Type 3: Sessions stuck past expiry
            $staleSessions = $this->pos_model->getStaleExpiredSessions(50);
            
            // Auto-expire stuck sessions
            $this->pos_model->expireStaleSessions();
            
            echo json_encode(array(
                'status' => 'success',
                'days_back' => $daysBack,
                'orphan_payments' => $orphanPayments,
                'orphan_payments_count' => count($orphanPayments),
                'orphan_billing' => $orphanBilling,
                'orphan_billing_count' => count($orphanBilling),
                'stale_sessions' => $staleSessions,
                'stale_sessions_count' => count($staleSessions),
                'total_issues' => count($orphanPayments) + count($orphanBilling) + count($staleSessions)
            ));
        } catch(Exception $e) {
            echo json_encode(array('status' => 'error', 'message' => 'Error: '.$e->getMessage()));
        }
        
        $this->db->db_debug = $oldDebug;
    }

    /**
     * Get unsettled transactions for reconciliation
     */
    function getUnsettledTransactions()
    {
        if(!$this->session->userdata('uid')){
            echo json_encode(array('status' => 'error', 'message' => 'Unauthorized'));
            return;
        }
        
        $daysOld = $this->input->post('days_old') ? intval($this->input->post('days_old')) : 2;
        $unsettled = $this->pos_model->getUnsettledTransactions($daysOld);
        
        $totalAmount = 0;
        foreach($unsettled as $txn) { $totalAmount += floatval($txn['amount_rs']); }
        
        echo json_encode(array(
            'status' => 'success',
            'transactions' => $unsettled,
            'count' => count($unsettled),
            'total_amount_rs' => $totalAmount
        ));
    }

}
