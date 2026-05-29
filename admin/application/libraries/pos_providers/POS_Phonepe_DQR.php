<?php
require_once(dirname(__FILE__) . '/POS_Provider_Interface.php');

/**
 * PhonePe DQR (Dynamic QR) POS Provider Plugin
 * Generates QR codes for UPI payments — customer scans to pay
 */
class POS_Phonepe_DQR implements POS_Provider_Interface {

    public function getProviderCode() { return 'phonepe_dqr'; }
    public function getProviderName() { return 'PhonePe DQR'; }

    /** Build X-VERIFY header */
    private function _buildXVerify($data, $apiEndpoint, $posDetails)
    {
        $saltKey   = $posDetails['salt_key'];
        $saltIndex = !empty($posDetails['salt_index']) ? $posDetails['salt_index'] : '1';
        $hash      = hash('sha256', $data . $apiEndpoint . $saltKey);
        return $hash . '###' . $saltIndex;
    }

    /** Build standard PhonePe headers */
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
        $transno = 'TX' . $addData['cusid'] . '_' . time() . '_' . random_int(1000, 9999);

        // No credentials check
        if(empty($posDetails['salt_key'])){
            // LIVE mode + no credentials = BLOCK (prevent fake payments)
            if(!empty($posDetails['is_env_live']) && $posDetails['is_env_live'] == 1){
                log_message('error', 'POS PhonePe DQR: LIVE mode but salt_key missing! Payment blocked.');
                return array('responsecode' => -1, 'resmessage' => 'PhonePe DQR is not configured. Please enter Salt Key and Merchant ID in POS Settings to activate payments.', 'refid' => null, 'qrdata' => null, 'provider' => 'phonepe_dqr');
            }
            // UAT mode + no credentials = DEMO mode
            log_message('info', 'POS PhonePe DQR: Running in DEMO mode (UAT + no credentials)');
            $amountRs = intval($addData['amount']);
            $mockQR = 'upi://pay?pa=test.merchant@phonepe&pn=TestMerchant&am=' . $amountRs . '&tr=' . $transno . '&cu=INR&mc=1234';
            $paydevicedata = array(
                'pos_trans_no' => $transno, 'pos_store_pos_code' => isset($posDetails['store_id']) ? $posDetails['store_id'] : 'MOCK',
                'pos_req_amount' => $amountRs * 100, 'pos_usr_id' => $CI->session->userdata('uid'),
                'pos_mer_id' => isset($posDetails['merchantid']) ? $posDetails['merchantid'] : 'MOCK',
                'pos_req_createdby' => $CI->session->userdata('uid'), 'pos_req_bill_cusid' => $addData['cusid'],
                'pos_req_bill_id' => isset($addData['billid']) && $addData['billid'] !== '' ? intval($addData['billid']) : null,
                'pos_req_est_id' => isset($addData['est_id']) && $addData['est_id'] !== '' ? intval($addData['est_id']) : (isset($addData['billid']) && $addData['billid'] !== '' ? intval($addData['billid']) : null),
                'pos_req_bill_type' => isset($addData['bill_type']) ? intval($addData['bill_type']) : null,
                'pos_req_source_ref' => isset($addData['source_ref']) && $addData['source_ref'] !== '' ? $addData['source_ref'] : null,
                'pos_req_id_branch' => isset($addData['id_branch']) ? intval($addData['id_branch']) : ($CI->session->userdata('id_branch') ? intval($CI->session->userdata('id_branch')) : null),
                'id_provider' => $posDetails['id_provider'],
                'id_device' => intval($addData['deviceId']),
                'provider_code' => 'phonepe_dqr',
                'payment_mode' => 'DQR',
                'pos_req_status' => 0,
                'pos_req_payload' => json_encode(array('mock' => true, 'amount' => $amountRs)),
                'pos_res_ref_id' => $transno, 'pos_qr_string' => $mockQR,
                'pos_res_trans_data' => json_encode(array('mock' => true, 'created_at' => time())),
                'idempotency_key' => isset($addData['idempotency_key']) ? $addData['idempotency_key'] : null,
            );
            $CI->$model->insertData($paydevicedata, 'ret_pos_requests');
            return array('responsecode' => 0, 'resmessage' => 'QR generated successfully', 'refid' => $transno, 'qrdata' => $mockQR, 'provider' => 'phonepe_dqr', 'supports_cancel' => true);
        }

        // LIVE MODE
        $apiUrl = $CI->$model->getPOSApiUrl($posDetails, 'init');
        $apiEndpoint = '/v3/qr/init';
        $payloadArray = array(
            'merchantId' => $posDetails['merchantid'], 'transactionId' => $transno,
            'merchantOrderId' => $transno, 'amount' => intval($addData['amount']) * 100,
            'storeId' => $posDetails['store_id'], 'terminalId' => $posDetails['terminal_id'],
            'expiresIn' => 120
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
            'provider_code' => 'phonepe_dqr',
            'payment_mode' => 'DQR',
            'pos_req_status' => 0,
            'pos_req_payload' => json_encode($payloadArray),
            'idempotency_key' => isset($addData['idempotency_key']) ? $addData['idempotency_key'] : null,
        );

        if($response && isset($response['success']) && $response['success'] === true){
            $qrString = isset($response['data']['qrString']) ? $response['data']['qrString'] : '';
            $paydevicedata['pos_res_ref_id'] = $transno;
            $paydevicedata['pos_qr_string'] = $qrString;
            $paydevicedata['pos_res_payload'] = json_encode($response);
            $CI->$model->insertData($paydevicedata, 'ret_pos_requests');
            log_message('info', 'POS DQR Init OK: txn=' . $transno . ' amt=' . ($addData['amount'] ?? '') . ' cusid=' . ($addData['cusid'] ?? '') . ' bill_type=' . ($addData['bill_type'] ?? '') . ' branch=' . ($CI->session->userdata('id_branch') ?: '') . ' uid=' . $CI->session->userdata('uid'));
            return array('responsecode' => 0, 'resmessage' => $response['message'], 'refid' => $transno, 'qrdata' => $qrString, 'provider' => 'phonepe_dqr', 'supports_cancel' => true);
        } else {
            $errMsg = isset($response['message']) ? $response['message'] : 'PhonePe DQR Init failed';
            $errCode = isset($response['code']) ? $response['code'] : 'UNKNOWN_ERROR';
            $paydevicedata['pos_res_trans_data'] = json_encode($response);
            $CI->$model->insertData($paydevicedata, 'ret_pos_requests');
            log_message('error', 'POS DQR Init FAILED: txn=' . $transno . ' code=' . $errCode . ' msg=' . $errMsg . ' uid=' . $CI->session->userdata('uid'));
            return array('responsecode' => -1, 'resmessage' => $errMsg . ' [' . $errCode . ']', 'refid' => null, 'qrdata' => null, 'provider' => 'phonepe_dqr', 'supports_cancel' => true);
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
                return array('responsecode' => -1, 'resmessage' => 'PhonePe DQR is not configured. Enter credentials in POS Settings.', 'transdata' => null, 'provider' => 'phonepe_dqr', 'paymentState' => 'CONFIG_ERROR');
            }
            $CI->db->where('pos_res_ref_id', $transactionId);
            $mockTxn = $CI->db->get('ret_pos_requests')->row_array();
            $elapsed = 999;
            if($mockTxn && !empty($mockTxn['pos_res_trans_data'])){
                $mockData = json_decode($mockTxn['pos_res_trans_data'], true);
                if(isset($mockData['mock']) && isset($mockData['created_at'])) $elapsed = time() - $mockData['created_at'];
            }
            if($elapsed < 15){
                $qrString = isset($mockTxn['pos_qr_string']) ? $mockTxn['pos_qr_string'] : '';
                return array('responsecode' => 2, 'resmessage' => 'Payment pending — waiting for customer to scan QR...', 'transdata' => null, 'provider' => 'phonepe_dqr', 'paymentState' => 'PAYMENT_PENDING', 'qrdata' => $qrString);
            } else {
                $mockUtr = 'MOCK' . date('YmdHis') . random_int(1000,9999);
                $CI->$model->updateData(array('pos_req_status' => 1, 'pos_utr' => $mockUtr, 'pos_success_at' => date('Y-m-d H:i:s'), 'pos_success_by' => $CI->session->userdata('uid')), 'pos_res_ref_id', $transactionId, 'ret_pos_requests');
                $transdata = array(
                    array('Tag' => 'Payment State', 'Value' => 'COMPLETED'),
                    array('Tag' => 'Provider Ref', 'Value' => 'MOCK_' . $transactionId),
                    array('Tag' => 'UTR', 'Value' => $mockUtr),
                    array('Tag' => 'Amount', 'Value' => isset($mockTxn['pos_req_amount']) ? ($mockTxn['pos_req_amount']/100) : '0'),
                    array('Tag' => 'Payment Mode', 'Value' => 'UPI'),
                );
                $mapped = array(
                    'payment_mode' => 'UPI', 'utr' => $mockUtr, 'card_name' => '', 'card_no' => '',
                    'bank_name' => 'Mock Bank', 'approval_no' => $mockUtr,
                    'paid_amount' => isset($mockTxn['pos_req_amount']) ? ($mockTxn['pos_req_amount']/100) : 0,
                    'id_pay_device' => !empty($posDetails['id_pay_device']) ? $posDetails['id_pay_device'] : null,
                );
                return array('responsecode' => 0, 'resmessage' => 'Payment successful!', 'transdata' => $transdata, 'provider' => 'phonepe_dqr', 'paymentState' => 'COMPLETED', 'utr' => $mockUtr, 'mapped_data' => $mapped);
            }
        }

        // LIVE MODE
        $statusUrlTemplate = $CI->$model->getPOSApiUrl($posDetails, 'status');
        $statusUrl = str_replace(array('{merchantId}', '{transactionId}'), array($merchantId, $transactionId), $statusUrlTemplate);
        $apiEndpoint = '/v3/transaction/' . $merchantId . '/' . $transactionId . '/status';
        $xVerify = $this->_buildXVerify('', $apiEndpoint, $posDetails);
        $headers = $this->_buildHeaders($xVerify, $posDetails);

        $response = $CI->phonepeCurlRequest($statusUrl, 'GET', null, $headers);

        if($response && isset($response['success']) && $response['success'] === true){
            $paymentState = isset($response['data']['paymentState']) ? $response['data']['paymentState'] : '';
            $utr = ''; $customerBank = ''; $providerRef = '';
            if(isset($response['data']['paymentModes']) && is_array($response['data']['paymentModes'])){
                foreach($response['data']['paymentModes'] as $pm){
                    if(isset($pm['utr'])) $utr = $pm['utr'];
                    if(isset($pm['bankId'])) $customerBank = $pm['bankId'];
                }
            }
            // providerReferenceId is the real PhonePe/bank UTR for DQR payments
            if(isset($response['data']['providerReferenceId'])) $providerRef = $response['data']['providerReferenceId'];
            // Use providerReferenceId as UTR if paymentModes UTR is empty
            if(empty($utr) && !empty($providerRef)) $utr = $providerRef;
            $updateData = array('pos_res_trans_data' => json_encode($response['data']), 'pos_utr' => $utr);
            if($paymentState == 'COMPLETED') {
                $updateData['pos_req_status'] = 1;
                $updateData['pos_success_at'] = date('Y-m-d H:i:s');
                $updateData['pos_success_by'] = $CI->session->userdata('uid');
            } elseif($paymentState == 'FAILED' || $paymentState == 'DECLINED') {
                $updateData['pos_req_status'] = 3;
            }
            $CI->$model->updateData($updateData, 'pos_res_ref_id', $transactionId, 'ret_pos_requests');
            log_message('info', 'POS DQR Status: txn=' . $transactionId . ' state=' . $paymentState . ' utr=' . $utr . ' amount=' . (isset($response['data']['amount']) ? $response['data']['amount']/100 : 0) . ' uid=' . $CI->session->userdata('uid'));

            $paidAmount = isset($response['data']['amount']) ? floatval($response['data']['amount'])/100 : 0;
            $transdata = array(
                array('Tag' => 'Payment State', 'Value' => $paymentState),
                array('Tag' => 'Provider Ref', 'Value' => isset($response['data']['providerReferenceId']) ? $response['data']['providerReferenceId'] : ''),
                array('Tag' => 'UTR', 'Value' => $utr),
                array('Tag' => 'Amount', 'Value' => $paidAmount),
            );
            if(isset($response['data']['paymentModes'])){
                foreach($response['data']['paymentModes'] as $pm){ $transdata[] = array('Tag' => 'Payment Mode', 'Value' => $pm['mode']); }
            }

            // Mapped data for auto-fill
            $mapped = array(
                'payment_mode' => 'UPI', 'utr' => $utr, 'card_name' => '', 'card_no' => '',
                'bank_name' => $customerBank, 'approval_no' => $utr, 'paid_amount' => $paidAmount,
                'provider_ref' => $providerRef,
                'id_pay_device' => !empty($posDetails['id_pay_device']) ? $posDetails['id_pay_device'] : null,
                'payment_date' => date('Y-m-d') // Fill today's date for accounting
            );

            // Amount verification
            $requestedPaise = isset($addData['amount']) ? floatval($addData['amount']) : 0;
            if($paidAmount > 0 && $requestedPaise > 0 && abs($paidAmount - ($requestedPaise/100)) > 0.01){
                $mapped['amount_mismatch'] = true;
                $mapped['amount_warning'] = 'Sent: Rs'.($requestedPaise/100).', Paid: Rs'.$paidAmount;
            }

            // Fix: responsecode 0 must STRICTLY mean success for the billing JS
            $respCode = ($paymentState == 'COMPLETED') ? 0 : (($paymentState == 'PAYMENT_PENDING') ? 2 : -1);

            $qrdata = '';
            if($respCode == 2){
                $oldDebug = $CI->db->db_debug; $CI->db->db_debug = FALSE;
                $txn = $CI->db->query("SELECT pos_qr_string FROM ret_pos_requests WHERE pos_res_ref_id = ? LIMIT 1", array($transactionId))->row_array();
                $CI->db->db_debug = $oldDebug;
                $qrdata = ($txn && !empty($txn['pos_qr_string'])) ? $txn['pos_qr_string'] : '';
            }

            return array('responsecode' => $respCode, 'resmessage' => $response['message'], 'transdata' => $transdata, 'provider' => 'phonepe_dqr', 'paymentState' => $paymentState, 'utr' => $utr, 'mapped_data' => $mapped, 'qrdata' => $qrdata);
        } else {
            $code = isset($response['code']) ? $response['code'] : 'UNKNOWN';
            $msg = isset($response['message']) ? $response['message'] : 'Status check failed';
            $respCode = ($code == 'PAYMENT_PENDING') ? 2 : -1;
            return array('responsecode' => $respCode, 'resmessage' => $msg . ' [' . $code . ']', 'transdata' => null, 'provider' => 'phonepe_dqr', 'paymentState' => $code);
        }
    }

    public function cancelPayment($addData, $posDetails, $CI)
    {
        $model = "pos_model";
        $merchantId = $posDetails['merchantid'];
        $transactionId = $addData['refcode'];

        $cancelUrlTemplate = $CI->$model->getPOSApiUrl($posDetails, 'cancel');
        $cancelUrl = str_replace(array('{merchantId}', '{transactionId}'), array($merchantId, $transactionId), $cancelUrlTemplate);
        $apiEndpoint = '/v3/charge/' . $merchantId . '/' . $transactionId . '/cancel';
        $xVerify = $this->_buildXVerify('', $apiEndpoint, $posDetails);
        $headers = $this->_buildHeaders($xVerify, $posDetails);

        $response = $CI->phonepeCurlRequest($cancelUrl, 'POST', '{}', $headers);

        if($response && isset($response['success']) && $response['success'] === true){
            $CI->$model->updateData(array('pos_req_status' => 2, 'pos_res_trans_data' => json_encode($response)), 'pos_res_ref_id', $transactionId, 'ret_pos_requests');
            return array('responsecode' => 0, 'resmessage' => 'Payment cancelled', 'transdata' => null, 'provider' => 'phonepe_dqr');
        } else {
            $code = isset($response['code']) ? $response['code'] : '';
            $msg = isset($response['message']) ? $response['message'] : 'Cancel failed';
            // Log failed cancel attempt to DB for audit trail
            $CI->$model->updateData(
                array('pos_res_trans_data' => json_encode(array('cancel_failed' => true, 'code' => $code, 'message' => $msg, 'response' => $response, 'cancelled_at' => date('Y-m-d H:i:s')))),
                'pos_res_ref_id', $transactionId, 'ret_pos_requests'
            );
            log_message('error', 'POS DQR Cancel FAILED: refId=' . $transactionId . ' code=' . $code . ' msg=' . $msg);
            return array('responsecode' => -1, 'resmessage' => $msg . ' [' . $code . ']', 'transdata' => null, 'provider' => 'phonepe_dqr');
        }
    }
}
