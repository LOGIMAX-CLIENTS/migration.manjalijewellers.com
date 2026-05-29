<?php
require_once(dirname(__FILE__) . '/POS_Provider_Interface.php');

/**
 * PhonePe IEDC (Intelligent EDC) POS Provider Plugin
 * Pushes payment to physical EDC terminal — supports card swipe + QR
 */
class POS_Phonepe_IEDC implements POS_Provider_Interface {

    public function getProviderCode() { return 'phonepe_iedc'; }
    public function getProviderName() { return 'PhonePe IEDC'; }

    private function _buildXVerify($data, $apiEndpoint, $posDetails)
    {
        $saltKey   = $posDetails['salt_key'];
        $saltIndex = !empty($posDetails['salt_index']) ? $posDetails['salt_index'] : '1';
        $hash      = hash('sha256', $data . $apiEndpoint . $saltKey);
        return $hash . '###' . $saltIndex;
    }

    private function _buildHeaders($xVerify, $posDetails)
    {
        $headers = array('Content-Type: application/json', 'X-VERIFY: ' . $xVerify);
        if(!empty($posDetails['provider_id'])) $headers[] = 'X-PROVIDER-ID: ' . $posDetails['provider_id'];
        if(!empty($posDetails['callback_url'])) $headers[] = 'X-CALLBACK-URL: ' . $posDetails['callback_url'];
        return $headers;
    }

    public function initPayment($addData, $posDetails, $CI)
    {
        $model = "pos_model";
        $transno = 'TXEDC' . $addData['cusid'] . '_' . time() . '_' . random_int(1000, 9999);
        $apiUrl = $CI->$model->getPOSApiUrl($posDetails, 'init');

        // No credentials check
        if(empty($posDetails['salt_key'])){
            // LIVE mode + no credentials = BLOCK (prevent fake payments)
            if(!empty($posDetails['is_env_live']) && $posDetails['is_env_live'] == 1){
                log_message('error', 'POS PhonePe IEDC: LIVE mode but salt_key missing! Payment blocked.');
                return array('responsecode' => -1, 'resmessage' => 'PhonePe IEDC is not configured. Please enter Salt Key and Merchant ID in POS Settings to activate payments.', 'refid' => null, 'provider' => 'phonepe_iedc');
            }
            // UAT mode + no credentials = DEMO mode
            log_message('info', 'POS PhonePe IEDC: Running in DEMO mode (UAT + no credentials)');
            $amountRs = intval($addData['amount']);
            $paydevicedata = array(
                'pos_trans_no' => $transno, 'pos_store_pos_code' => isset($posDetails['store_id']) ? $posDetails['store_id'] : 'DEMO',
                'pos_req_amount' => $amountRs * 100, 'pos_usr_id' => $CI->session->userdata('uid'),
                'pos_mer_id' => isset($posDetails['merchantid']) ? $posDetails['merchantid'] : 'DEMO',
                'pos_req_createdby' => $CI->session->userdata('uid'), 'pos_req_bill_cusid' => $addData['cusid'],
                'pos_req_bill_id' => isset($addData['billid']) && $addData['billid'] !== '' ? intval($addData['billid']) : null,
                'pos_req_est_id' => isset($addData['est_id']) && $addData['est_id'] !== '' ? intval($addData['est_id']) : (isset($addData['billid']) && $addData['billid'] !== '' ? intval($addData['billid']) : null),
                'pos_req_bill_type' => isset($addData['bill_type']) ? intval($addData['bill_type']) : null,
                'pos_req_source_ref' => isset($addData['source_ref']) && $addData['source_ref'] !== '' ? $addData['source_ref'] : null,
                'pos_req_id_branch' => isset($addData['id_branch']) ? intval($addData['id_branch']) : ($CI->session->userdata('id_branch') ? intval($CI->session->userdata('id_branch')) : null),
                'id_provider' => $posDetails['id_provider'],
                'id_device' => intval($addData['deviceId']),
                'provider_code' => 'phonepe_iedc',
                'payment_mode' => 'CARD',
                'pos_req_status' => 0,
                'pos_req_payload' => json_encode(array('demo_mode' => true, 'amount' => $amountRs)),
                'pos_res_ref_id' => $transno,
                'pos_res_trans_data' => json_encode(array('demo_mode' => true, 'created_at' => time())),
                'idempotency_key' => isset($addData['idempotency_key']) ? $addData['idempotency_key'] : null,
            );
            $CI->$model->insertData($paydevicedata, 'ret_pos_requests');
            return array('responsecode' => 0, 'resmessage' => 'Payment sent to EDC terminal — awaiting customer action', 'refid' => $transno, 'provider' => 'phonepe_iedc', 'supports_cancel' => false);
        }

        // LIVE MODE
        $apiEndpoint = '/v1/edc/transaction/init';
        // Translate generic paySection (from billing JS) into PhonePe-specific paymentModes
        // JS sends:  paySection = 'card' | 'upi' | (absent)
        // PhonePe expects: paymentModes = ['CARD'] | ['DQR'] | ['CARD','DQR']
        // This translation lives HERE (in the plugin), not in the JS, so if
        // Razorpay/PayTM comes, they translate paySection their own way.
        $paySection = isset($addData['paySection']) ? $addData['paySection'] : '';
        if($paySection === 'card'){
            $paymentModes = array('CARD');
        } elseif($paySection === 'upi'){
            $paymentModes = array('DQR');
        } else {
            $paymentModes = array('CARD', 'DQR'); // default: both
        }
        
        $payloadArray = array(
            'merchantId' => $posDetails['merchantid'], 'storeId' => $posDetails['store_id'],
            'orderId' => $transno, 'terminalId' => $posDetails['terminal_id'],
            'transactionId' => $transno, 'amount' => intval($addData['amount']) * 100,
            'paymentModes' => $paymentModes,
            'timeAllowedForHandoverToTerminalSeconds' => 120,
            'integrationMappingType' => 'ONE_TO_ONE'
        );
        $base64Payload = base64_encode(json_encode($payloadArray));
        $xVerify = $this->_buildXVerify($base64Payload, $apiEndpoint, $posDetails);
        $headers = $this->_buildHeaders($xVerify, $posDetails);

        $response = $CI->phonepeCurlRequest($apiUrl, 'POST', json_encode(array('request' => $base64Payload)), $headers);

        $paydevicedata = array(
            'pos_trans_no' => $transno, 'pos_store_pos_code' => $posDetails['store_id'],
            'pos_req_amount' => intval($addData['amount']) * 100, 'pos_usr_id' => $CI->session->userdata('uid'),
            'pos_mer_id' => $posDetails['merchantid'], 'pos_req_createdby' => $CI->session->userdata('uid'),
            'pos_req_bill_cusid' => $addData['cusid'],
            'pos_req_bill_id' => isset($addData['billid']) && $addData['billid'] !== '' ? intval($addData['billid']) : null,
            'pos_req_est_id' => isset($addData['est_id']) && $addData['est_id'] !== '' ? intval($addData['est_id']) : (isset($addData['billid']) && $addData['billid'] !== '' ? intval($addData['billid']) : null),
            'pos_req_bill_type' => isset($addData['bill_type']) ? intval($addData['bill_type']) : null,
            'pos_req_source_ref' => isset($addData['source_ref']) && $addData['source_ref'] !== '' ? $addData['source_ref'] : null,
            'pos_req_id_branch' => isset($addData['id_branch']) ? intval($addData['id_branch']) : ($CI->session->userdata('id_branch') ? intval($CI->session->userdata('id_branch')) : null),
            'id_provider' => $posDetails['id_provider'],
            'id_device' => intval($addData['deviceId']),
            'provider_code' => 'phonepe_iedc',
            'payment_mode' => ($paySection === 'card') ? 'CARD' : 'DQR',
            'pos_req_status' => 0,
            'pos_req_payload' => json_encode($payloadArray),
            'idempotency_key' => isset($addData['idempotency_key']) ? $addData['idempotency_key'] : null,
        );

        if($response && isset($response['success']) && $response['success'] === true){
            $paydevicedata['pos_res_ref_id'] = $transno;
            $paydevicedata['pos_res_payload'] = json_encode($response);
            $CI->$model->insertData($paydevicedata, 'ret_pos_requests');
            log_message('info', 'POS IEDC Init OK: txn=' . $transno . ' amt=' . ($addData['amount'] ?? '') . ' cusid=' . ($addData['cusid'] ?? '') . ' bill_type=' . ($addData['bill_type'] ?? '') . ' branch=' . ($CI->session->userdata('id_branch') ?: '') . ' uid=' . $CI->session->userdata('uid'));
            return array('responsecode' => 0, 'resmessage' => isset($response['message']) ? $response['message'] : 'Payment pushed to terminal', 'refid' => $transno, 'provider' => 'phonepe_iedc', 'supports_cancel' => false);
        } else {
            $errMsg = isset($response['message']) ? $response['message'] : 'IEDC Init failed';
            $errCode = isset($response['code']) ? $response['code'] : 'UNKNOWN_ERROR';
            $paydevicedata['pos_res_trans_data'] = json_encode($response);
            $CI->$model->insertData($paydevicedata, 'ret_pos_requests');
            log_message('error', 'POS IEDC Init FAILED: txn=' . $transno . ' code=' . $errCode . ' msg=' . $errMsg . ' uid=' . $CI->session->userdata('uid'));
            return array('responsecode' => -1, 'resmessage' => $errMsg . ' [' . $errCode . ']', 'refid' => null, 'provider' => 'phonepe_iedc', 'supports_cancel' => false);
        }
    }

    public function checkStatus($addData, $posDetails, $CI)
    {
        $model = "pos_model";
        $merchantId = $posDetails['merchantid'];
        $transactionId = $addData['refcode'];

        // No credentials check
        if(empty($posDetails['salt_key'])){
            // LIVE mode + no credentials = BLOCK
            if(!empty($posDetails['is_env_live']) && $posDetails['is_env_live'] == 1){
                return array('responsecode' => -1, 'resmessage' => 'PhonePe IEDC is not configured. Enter credentials in POS Settings.', 'transdata' => null, 'provider' => 'phonepe_iedc', 'paymentState' => 'CONFIG_ERROR');
            }
            $CI->db->where('pos_res_ref_id', $transactionId);
            $mockTxn = $CI->db->get('ret_pos_requests')->row_array();
            $elapsed = 999;
            if($mockTxn && !empty($mockTxn['pos_res_trans_data'])){
                $mockData = json_decode($mockTxn['pos_res_trans_data'], true);
                if(isset($mockData['demo_mode']) && isset($mockData['created_at'])) $elapsed = time() - $mockData['created_at'];
            }
            if($elapsed < 10){
                return array('responsecode' => 2, 'resmessage' => 'Waiting for customer to complete payment on terminal...', 'transdata' => null, 'provider' => 'phonepe_iedc', 'paymentState' => 'PAYMENT_PENDING');
            } else {
                $mockUtr = 'UTR' . date('YmdHis') . random_int(1000,9999);
                $mockApproval = 'AP' . random_int(100000, 999999);
                $CI->$model->updateData(array('pos_req_status' => 1, 'pos_utr' => $mockUtr), 'pos_res_ref_id', $transactionId, 'ret_pos_requests');
                $transdata = array(
                    array('Tag' => 'Payment State', 'Value' => 'COMPLETED'),
                    array('Tag' => 'Card Number', 'Value' => 'XXXX XXXX XXXX 4242'),
                    array('Tag' => 'Card Type', 'Value' => 'VISA DEBIT'),
                    array('Tag' => 'Approval No', 'Value' => $mockApproval),
                    array('Tag' => 'UTR', 'Value' => $mockUtr),
                    array('Tag' => 'Amount', 'Value' => isset($mockTxn['pos_req_amount']) ? ($mockTxn['pos_req_amount']/100) : '0'),
                    array('Tag' => 'Payment Mode', 'Value' => 'DEBIT_CARD'),
                );
                $mapped = array(
                    'card_name' => 'VISA', 'card_type' => 'DC', 'card_no' => '4242',
                    'bank_name' => '', 'approval_no' => $mockApproval, 'utr' => $mockUtr,
                    'paid_amount' => isset($mockTxn['pos_req_amount']) ? ($mockTxn['pos_req_amount']/100) : 0,
                    'payment_mode' => 'CARD',
                    'id_pay_device' => !empty($posDetails['id_pay_device']) ? $posDetails['id_pay_device'] : null,
                );
                return array('responsecode' => 0, 'resmessage' => 'Payment successful!', 'transdata' => $transdata, 'provider' => 'phonepe_iedc', 'paymentState' => 'COMPLETED', 'utr' => $mockUtr, 'mapped_data' => $mapped);
            }
        }

        // LIVE MODE
        $statusUrlTemplate = $CI->$model->getPOSApiUrl($posDetails, 'status');
        $statusUrl = str_replace(array('{merchantId}', '{transactionId}'), array($merchantId, $transactionId), $statusUrlTemplate);
        // IEDC uses its own status endpoint (different from DQR):
        // Official PhonePe docs: /v1/edc/transaction/{merchantId}/{transactionId}/status
        // X-VERIFY: SHA256("/v1/edc/transaction/{merchantId}/{transactionId}/status" + saltKey) + "###" + saltIndex
        // URL: mercury-t2.phonepe.com/v1/edc/transaction/{merchantId}/{transactionId}/status (prod)
        $apiEndpoint = '/v1/edc/transaction/' . $merchantId . '/' . $transactionId . '/status';
        $xVerify = $this->_buildXVerify('', $apiEndpoint, $posDetails);
        $headers = $this->_buildHeaders($xVerify, $posDetails);

        // LIVE API TEST (13-Apr-2026): POST returns 405 Method Not Allowed
        // GET returns valid response (400 INVALID_TRANSACTION_ID for test txn = endpoint works)
        // PhonePe IEDC StatusCheck uses GET (same as DQR), not POST as initially documented
        $response = $CI->phonepeCurlRequest($statusUrl, 'GET', null, $headers);

        if($response && isset($response['success']) && $response['success'] === true){
            $paymentState = isset($response['data']['paymentState']) ? $response['data']['paymentState'] : '';
            // IEDC uses 'status' field instead of 'paymentState' in some response variants
            if(empty($paymentState) && isset($response['data']['status'])) {
                $paymentState = $response['data']['status'];
            }
            $utr = ''; $upiTxnId = ''; $cardType = ''; $cardNo = ''; $bankName = ''; $cardNetwork = ''; $paymentMode = '';
            
            // Priority 1: data.referenceNumber — matches "REF. NO" on physical receipt
            // Verified from real responses:
            //   CARD: referenceNumber = "612610447738" = receipt REF.NO
            //   UPI:  referenceNumber = "612616543907" = receipt REF.NO
            if(isset($response['data']['referenceNumber'])){
                $utr = $response['data']['referenceNumber'];
            }
            
            // Extract card/UPI details from paymentInstruments
            if(isset($response['data']['paymentInstruments']) && is_array($response['data']['paymentInstruments'])){
                foreach($response['data']['paymentInstruments'] as $pi){
                    if(isset($pi['last4Digits'])) $cardNo = $pi['last4Digits'];
                    if(isset($pi['cardNetwork'])) $cardNetwork = $pi['cardNetwork'];
                    if(isset($pi['cardType'])) $cardType = $pi['cardType'];
                    if(isset($pi['upiTransactionId'])) $upiTxnId = $pi['upiTransactionId'];
                    if(isset($pi['type'])) $paymentMode = $pi['type'];
                    // paymentInstruments[].utr also matches receipt REF.NO for UPI
                    if(isset($pi['utr']) && empty($utr)) $utr = $pi['utr'];
                }
            }
            // Also check paymentModes (DQR-on-EDC variant uses this)
            if(isset($response['data']['paymentModes']) && is_array($response['data']['paymentModes'])){
                foreach($response['data']['paymentModes'] as $pm){
                    if(isset($pm['utr']) && empty($utr)) $utr = $pm['utr'];
                    if(isset($pm['cardType']) && empty($cardType)) $cardType = $pm['cardType'];
                    if(isset($pm['cardNumber']) && empty($cardNo)) $cardNo = substr($pm['cardNumber'], -4);
                    if(isset($pm['bankId']) && empty($bankName)) $bankName = $pm['bankId'];
                }
            }
            // Last fallback: upiTransactionId (PhonePe internal ID, NOT receipt REF.NO)
            if(empty($utr) && !empty($upiTxnId)){
                $utr = $upiTxnId;
            }
            // Top-level paymentMode
            if(empty($paymentMode) && isset($response['data']['paymentMode'])){
                $paymentMode = $response['data']['paymentMode'];
            }
            
            $updateData = array('pos_res_trans_data' => json_encode($response['data']), 'pos_utr' => $utr, 'card_last4' => $cardNo, 'approval_code' => $utr);
            // Also extract providerReferenceId as fallback UTR (especially for UPI-on-EDC)
            $providerRef = '';
            if(isset($response['data']['providerReferenceId'])) $providerRef = $response['data']['providerReferenceId'];
            if(empty($utr) && !empty($providerRef)) {
                $utr = $providerRef;
                $updateData['pos_utr'] = $utr;
                $updateData['approval_code'] = $utr;
            }
            if($paymentState == 'COMPLETED' || $paymentState == 'SUCCESS') {
                $updateData['pos_req_status'] = 1;
                $updateData['pos_success_at'] = date('Y-m-d H:i:s');
                $updateData['pos_success_by'] = $CI->session->userdata('uid');
            } elseif($paymentState == 'FAILED' || $paymentState == 'DECLINED') {
                $updateData['pos_req_status'] = 3;
            }
            $CI->$model->updateData($updateData, 'pos_res_ref_id', $transactionId, 'ret_pos_requests');
            log_message('info', 'POS IEDC Status: txn=' . $transactionId . ' state=' . $paymentState . ' utr=' . $utr . ' mode=' . $paymentMode . ' card=' . $cardNo . ' amount=' . (isset($response['data']['amount']) ? $response['data']['amount']/100 : 0) . ' uid=' . $CI->session->userdata('uid'));

            $paidAmount = isset($response['data']['amount']) ? floatval($response['data']['amount'])/100 : 0;
            $transdata = array(
                array('Tag' => 'Payment State', 'Value' => $paymentState),
                array('Tag' => 'Provider Ref', 'Value' => isset($response['data']['referenceNumber']) ? $response['data']['referenceNumber'] : (isset($response['data']['providerReferenceId']) ? $response['data']['providerReferenceId'] : '')),
                array('Tag' => 'UTR', 'Value' => $utr),
                array('Tag' => 'Amount', 'Value' => $paidAmount),
                array('Tag' => 'Payment Mode', 'Value' => $paymentMode),
            );
            if(!empty($cardNetwork)) $transdata[] = array('Tag' => 'Card Network', 'Value' => $cardNetwork);
            if(!empty($cardNo)) $transdata[] = array('Tag' => 'Card Last 4', 'Value' => $cardNo);
            if(!empty($cardType)) $transdata[] = array('Tag' => 'Card Type', 'Value' => $cardType);

            // Mapped data for auto-fill
            $mapped = array(
                'card_name' => !empty($cardNetwork) ? $cardNetwork : $cardType,
                'card_type' => (stripos($cardType, 'DEBIT') !== false) ? 'DC' : 'CC',
                'card_no' => $cardNo, 'bank_name' => $bankName, 'approval_no' => $utr,
                'utr' => $utr, 'paid_amount' => $paidAmount,
                'payment_mode' => $paymentMode, 'provider_ref' => $providerRef,
                'id_pay_device' => !empty($posDetails['id_pay_device']) ? $posDetails['id_pay_device'] : null,
            );

            // Amount verification
            $requestedPaise = isset($addData['amount']) ? floatval($addData['amount']) : 0;
            if($paidAmount > 0 && $requestedPaise > 0 && abs($paidAmount - ($requestedPaise/100)) > 0.01){
                $mapped['amount_mismatch'] = true;
                $mapped['amount_warning'] = 'Sent: Rs'.($requestedPaise/100).', Paid: Rs'.$paidAmount;
            }

            // responsecode: 0=success, 2=pending (keep polling), -1=failed
            // IEDC returns "PENDING" or "SUCCESS"; DQR returns "PAYMENT_PENDING" or "COMPLETED"
            if($paymentState == 'COMPLETED' || $paymentState == 'SUCCESS') {
                $respCode = 0;
            } elseif($paymentState == 'PENDING' || $paymentState == 'PAYMENT_PENDING' || $paymentState == 'INITIATED') {
                $respCode = 2;
            } else {
                $respCode = -1; // FAILED / DECLINED / unknown
            }

            return array('responsecode' => $respCode, 'resmessage' => $response['message'], 'transdata' => $transdata, 'provider' => 'phonepe_iedc', 'paymentState' => $paymentState, 'utr' => $utr, 'mapped_data' => $mapped);
        } else {
            $code = isset($response['code']) ? $response['code'] : 'UNKNOWN';
            $msg = isset($response['message']) ? $response['message'] : 'Status check failed';
            // IEDC may return code "PENDING" or "PAYMENT_PENDING"
            $respCode = ($code == 'PAYMENT_PENDING' || $code == 'PENDING' || $code == 'INITIATED') ? 2 : -1;
            // Log failed status checks for debugging
            log_message('error', 'POS IEDC Status check failed: refId=' . $transactionId . ' code=' . $code . ' msg=' . $msg);
            return array('responsecode' => $respCode, 'resmessage' => $msg . ' [' . $code . ']', 'transdata' => null, 'provider' => 'phonepe_iedc', 'paymentState' => $code);
        }
    }

    public function cancelPayment($addData, $posDetails, $CI)
    {
        $model = "pos_model";
        $transactionId = $addData['refcode'];

        // IEDC has NO server-side cancel API.
        // The physical EDC terminal handles cancellation locally:
        // - If customer hasn't tapped/swiped: terminal auto-expires after timeout (120s)
        // - If payment is mid-processing: must be cancelled on the terminal itself
        // - We mark our DB record as cancelled and stop the JS polling loop

        // DEMO MODE — just update DB
        if(empty($posDetails['salt_key'])){
            if(!empty($posDetails['is_env_live']) && $posDetails['is_env_live'] == 1){
                return array('responsecode' => -1, 'resmessage' => 'PhonePe IEDC is not configured.', 'transdata' => null, 'provider' => 'phonepe_iedc');
            }
        }

        // Mark as cancelled in DB (both DEMO and LIVE)
        $CI->$model->updateData(array(
            'pos_req_status' => 2, 
            'pos_res_payload' => json_encode(array(
                'cancel_note' => 'Cancelled by user from ERP. IEDC terminal will auto-expire if payment is pending.',
                'cancelled_at' => date('Y-m-d H:i:s'),
                'cancelled_by' => $CI->session->userdata('uid')
            ))
        ), 'pos_res_ref_id', $transactionId, 'ret_pos_requests');

        log_message('info', 'POS PhonePe IEDC: Transaction '.$transactionId.' cancelled by user. Terminal will auto-expire.');

        return array(
            'responsecode' => 0, 
            'resmessage' => 'Cancelled. If payment is pending on the terminal, it will auto-expire.', 
            'transdata' => null, 
            'provider' => 'phonepe_iedc'
        );
    }
}
