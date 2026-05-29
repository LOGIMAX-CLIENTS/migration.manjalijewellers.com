<?php
require_once(dirname(__FILE__) . '/POS_Provider_Interface.php');

/**
 * Pine Labs POS Provider Plugin
 * Handles UploadBilledTransaction, GetCloudBasedTxnStatus, CancelTransaction
 */
class POS_Pinelabs implements POS_Provider_Interface {

    public function getProviderCode() { return 'pinelabs'; }
    public function getProviderName() { return 'Pine Labs'; }

    /**
     * Init Payment — UploadBilledTransaction
     */
    public function initPayment($addData, $posDetails, $CI)
    {
        $model = "ret_billing_model";
        $cusmob = $CI->$model->getcusLastMobile($addData['cusid']);
        $transno = $addData['cusid']."_".random_int(100000, 999999)."_".$cusmob;
        
        log_message('info', 'POS Pine Labs Init: cusid='.$addData['cusid'].' amount='.($addData['amount']*100).' transno='.$transno.' device='.$posDetails['id_device']);
        
        // Pine Labs API payload
        $requestData = array(
            "TransactionNumber"  => $transno,   
            "SequenceNumber"     => intval($addData['seqno']),                            
            // AllowedPaymentMode: Pine Labs API uses numeric codes for payment type
            // JS sends paytype: 1 (card section) or 10 (UPI section)
            // Pine Labs API: 1 = Card, 10 = UPI, etc.
            // Note: $addData['paySection'] ('card'/'upi') is also available for future use
            "AllowedPaymentMode" => intval($addData['paytype']),                              
            "MerchantStorePosCode" => $posDetails['poscode'],
            "Amount"       => intval($addData['amount'] * 100),                                     
            "UserID"       => $CI->session->userdata('uid'),                 
            "MerchantID"   => $posDetails['merchantid'],                                
            "SecurityToken"=> $posDetails['securitytoken'],
            "IMEI"         => $posDetails['imei'],
            "AutoCancelDurationInMinutes" => 2
        );
        
        // DB record
        $paydevicedata = array(
            'pos_trans_no'       => $transno,  
            'pos_store_pos_code' => $posDetails['poscode'],
            'pos_req_amount'     => $addData['amount'] * 100,  
            'pos_usr_id'         => $CI->session->userdata('uid'),  
            'pos_mer_id'         => $posDetails['merchantid'], 
            'pos_imie'           => $posDetails['imei'],
            'pos_req_createdby'  => $CI->session->userdata('uid'), 
            'pos_req_bill_cusid' => $addData['cusid'],
            'pos_req_bill_id'    => isset($addData['billid']) ? intval($addData['billid']) : null,
            'id_provider'        => $posDetails['id_provider'],
            'id_device'          => intval($addData['deviceId']),
            'provider_code'      => 'pinelabs',
            'payment_mode'       => ($addData['paytype'] == 10) ? 'UPI' : 'CARD',
            'pos_req_status'     => 0, // Initialize status as PENDING (0)
            'pos_req_payload'    => json_encode($requestData),
        );
        
        // Get API URL
        $apiUrl = $CI->$model->getPOSApiUrl($posDetails, 'init');
        if(empty($apiUrl)) $apiUrl = $CI->config->item('pos_api_request_url');
        
        $posrequest = $CI->postcurlPOSRequests($requestData, $apiUrl);
        
        if($posrequest['ResponseCode'] == 0){
            $paydevicedata['pos_res_ref_id'] = $posrequest['PlutusTransactionReferenceID'];
            $paydevicedata['pos_res_payload'] = json_encode($posrequest);
            $insId = $CI->$model->insertData($paydevicedata, 'ret_pos_requests');
            log_message('info', 'POS Pine Labs Init SUCCESS: refId='.$posrequest['PlutusTransactionReferenceID'].' dbId='.$insId);
            return array("responsecode" => $posrequest['ResponseCode'], "resmessage" => $posrequest['ResponseMessage'], "refid" => $posrequest['PlutusTransactionReferenceID'], 'supports_cancel' => true);
        } else {
            $paydevicedata['pos_req_status'] = 3;
            $paydevicedata['pos_res_payload'] = json_encode($posrequest);
            $CI->$model->insertData($paydevicedata, 'ret_pos_requests');
            log_message('error', 'POS Pine Labs Init FAILED: code='.$posrequest['ResponseCode'].' msg='.$posrequest['ResponseMessage']);
            return array("responsecode" => $posrequest['ResponseCode'], "resmessage" => $posrequest['ResponseMessage'], "refid" => isset($posrequest['PlutusTransactionReferenceID']) ? $posrequest['PlutusTransactionReferenceID'] : null, 'supports_cancel' => true);
        }
    }

    /**
     * Check Status — GetCloudBasedTxnStatus
     */
    public function checkStatus($addData, $posDetails, $CI)
    {
        $model = "ret_billing_model";
        
        log_message('info', 'POS Pine Labs Status: refId='.$addData['refcode'].' device='.$posDetails['id_device']);
        
        $requestData = array(
            "MerchantID"    => $posDetails['merchantid'],                                
            "SecurityToken" => $posDetails['securitytoken'],
            "IMEI"          => $posDetails['imei'],
            "MerchantStorePosCode"           => $posDetails['poscode'],
            "PlutusTransactionReferenceID"   => $addData['refcode'],
        );
        
        $apiUrl = $CI->$model->getPOSApiUrl($posDetails, 'status');
        if(empty($apiUrl)) $apiUrl = $CI->config->item('pos_api_trans_status_url');
                    
        $posrequest = $CI->postcurlPOSRequests($requestData, $apiUrl);
        
        // Parse TransactionData for card/bank details
        $mapped = array(
            'card_name'    => '',
            'card_type'    => '',
            'card_no'      => '',
            'bank_name'    => '',
            'acquirer_name'=> '',
            'approval_no'  => '',
            'rrn'          => '',
            'paid_amount'  => 0,
            'id_pay_device'=> !empty($posDetails['id_pay_device']) ? $posDetails['id_pay_device'] : null,
        );
        
        if($posrequest['ResponseCode'] == 0 && !empty($posrequest['TransactionData'])){
            // Pine Labs returns TransactionData in two possible formats:
            // Format 1 (Key-Value object): {"ApprovalCode":"867386","CardNumber":"****1727",...}
            // Format 2 (Tag-Value array):  [{"Tag":"Approval Code","Value":"867386"},...]
            $txnData = is_string($posrequest['TransactionData']) ? json_decode($posrequest['TransactionData'], true) : $posrequest['TransactionData'];
            if(is_array($txnData)){
                // Detect format: Tag/Value array vs Key-Value object
                $isTagValueArray = (isset($txnData[0]) && isset($txnData[0]['Tag']));
                
                if($isTagValueArray){
                    // Format 2: Tag/Value array — convert to key-value for uniform handling
                    $kvData = array();
                    foreach($txnData as $item){
                        if(isset($item['Tag']) && isset($item['Value'])){
                            $kvData[$item['Tag']] = $item['Value'];
                        }
                    }
                    // Map Tag names (exact names from Pine Labs live API response)
                    $mapped['card_name']     = isset($kvData['Card Type']) ? $kvData['Card Type'] : '';
                    $mapped['card_no']       = isset($kvData['Card Number']) ? substr($kvData['Card Number'], -4) : '';
                    $mapped['bank_name']     = isset($kvData['Acquirer Name']) ? $kvData['Acquirer Name'] : '';
                    $mapped['acquirer_name'] = isset($kvData['Acquirer Name']) ? $kvData['Acquirer Name'] : '';
                    // Pine Labs returns 'ApprovalCode' (no space) in Tag/Value format
                    $mapped['approval_no']   = isset($kvData['ApprovalCode']) ? $kvData['ApprovalCode'] : (isset($kvData['Approval Code']) ? $kvData['Approval Code'] : '');
                    $mapped['rrn']           = isset($kvData['RRN']) ? $kvData['RRN'] : '';
                    $mapped['paid_amount']   = isset($kvData['AmountInPaisa']) ? floatval($kvData['AmountInPaisa']) / 100 : (isset($kvData['Amount']) ? floatval($kvData['Amount']) : 0);
                    $mapped['payer_vpa']     = isset($kvData['Customer VPA']) ? $kvData['Customer VPA'] : '';
                    $mapped['payment_mode']  = isset($kvData['PaymentMode']) ? $kvData['PaymentMode'] : '';
                } else {
                    // Format 1: Key-Value object
                    $mapped['card_name']     = isset($txnData['CardType']) ? $txnData['CardType'] : (isset($txnData['CardName']) ? $txnData['CardName'] : '');
                    $mapped['card_no']       = isset($txnData['CardNumber']) ? substr($txnData['CardNumber'], -4) : '';
                    $mapped['bank_name']     = isset($txnData['BankName']) ? $txnData['BankName'] : '';
                    $mapped['acquirer_name'] = isset($txnData['AcquirerName']) ? $txnData['AcquirerName'] : '';
                    $mapped['approval_no']   = isset($txnData['ApprovalCode']) ? $txnData['ApprovalCode'] : '';
                    $mapped['rrn']           = isset($txnData['Rrn']) ? $txnData['Rrn'] : (isset($txnData['RRN']) ? $txnData['RRN'] : '');
                    $mapped['paid_amount']   = isset($txnData['TransactionAmount']) ? floatval($txnData['TransactionAmount']) / 100 : 0;
                    $mapped['payer_vpa']     = isset($txnData['PayerVpa']) ? $txnData['PayerVpa'] : '';
                }
                
                // UPI fallback: UPI has dummy Approval Codes, always use RRN
                // Card: approval_no = ApprovalCode (e.g., 867386)
                // UPI:  approval_no = RRN (e.g., 6614618G6864)
                $isUPI = (isset($mapped['payment_mode']) && stripos($mapped['payment_mode'], 'UPI') !== false) || !empty($mapped['payer_vpa']);
                if($isUPI && !empty($mapped['rrn'])){
                    $mapped['approval_no'] = $mapped['rrn'];
                } else if(empty($mapped['approval_no']) && !empty($mapped['rrn'])){
                    $mapped['approval_no'] = $mapped['rrn'];
                }
                
                // Card type: VISA/MC/RUPAY → CC or DC
                $cardType = strtoupper($mapped['card_name']);
                if(in_array($cardType, array('VISA','MASTERCARD','MC','RUPAY','AMEX','DINERS'))) {
                    $mapped['card_type'] = 'CC'; // Default to CC, actual CC/DC depends on BIN
                }
                
                log_message('info', 'POS Pine Labs TransactionData parsed: approval='.$mapped['approval_no'].' rrn='.$mapped['rrn'].' card='.$mapped['card_no'].' vpa='.$mapped['payer_vpa']);
            }
            
            // Amount verification
            $requestedAmountPaise = isset($addData['amount']) ? floatval($addData['amount']) : 0;
            if($mapped['paid_amount'] > 0 && $requestedAmountPaise > 0 && abs($mapped['paid_amount'] - ($requestedAmountPaise / 100)) > 0.01){
                $mapped['amount_mismatch'] = true;
                $mapped['amount_warning'] = 'Sent: ₹'.($requestedAmountPaise/100).', Paid: ₹'.$mapped['paid_amount'];
                log_message('error', 'POS Pine Labs AMOUNT MISMATCH: refId='.$addData['refcode'].' '.$mapped['amount_warning']);
            }
            
            // Ensure TransactionData is stored as JSON string, not PHP array
            $transDataForDB = is_array($posrequest['TransactionData']) ? json_encode($posrequest['TransactionData']) : $posrequest['TransactionData'];
            
            $CI->$model->updateData(array(
                'pos_res_trans_data' => $transDataForDB, 
                'pos_req_status'     => 1, 
                'pos_res_payload'    => json_encode($posrequest), 
                'card_last4'         => $mapped['card_no'], 
                'approval_code'      => $mapped['approval_no'],
                'pos_utr'            => $mapped['rrn'],
                'pos_success_at'     => date('Y-m-d H:i:s'),
                'pos_success_by'     => $CI->session->userdata('uid')
            ), 'pos_res_ref_id', $addData['refcode'], 'ret_pos_requests');
            log_message('info', 'POS Pine Labs Status SUCCESS: refId='.$addData['refcode'].' approval='.$mapped['approval_no'].' rrn='.$mapped['rrn']);
        } else {
            // Handle expired/invalid transactions — Pine Labs auto-cancelled after timeout
            $isExpired = ($posrequest['ResponseCode'] == 1 && 
                (stripos($posrequest['ResponseMessage'], 'INVALID') !== false ||
                 stripos($posrequest['ResponseMessage'], 'NOT FOUND') !== false));
            
            if($isExpired){
                // Mark as failed/expired in our DB so it stops blocking new payments
                $CI->$model->updateData(
                    array('pos_req_status' => 3, 'pos_res_payload' => json_encode($posrequest)), 
                    'pos_res_ref_id', $addData['refcode'], 'ret_pos_requests'
                );
                log_message('info', 'POS Pine Labs Status: TXN expired/auto-cancelled by gateway. refId='.$addData['refcode']);
                
                return array(
                    "responsecode" => -3, // Special code: transaction expired
                    "resmessage"   => 'Transaction expired — POS device did not process in time. You can retry.',
                    "transdata"    => null,
                    "mapped_data"  => $mapped,
                    "expired"      => true
                );
            }
            
            log_message('warning', 'POS Pine Labs Status: refId='.$addData['refcode'].' code='.$posrequest['ResponseCode'].' msg='.$posrequest['ResponseMessage']);
        }
        
        return array(
            "responsecode" => $posrequest['ResponseCode'],
            "resmessage"   => $posrequest['ResponseMessage'],
            "transdata"    => $posrequest['TransactionData'],
            "mapped_data"  => $mapped
        );
    }

    /**
     * Cancel — CancelTransaction
     */
    public function cancelPayment($addData, $posDetails, $CI)
    {
        $model = "ret_billing_model";
        
        log_message('info', 'POS Pine Labs Cancel: refId='.$addData['refcode'].' amount='.$addData['amount'].' device='.$posDetails['id_device']);
        
        $requestData = array(
            "MerchantID"    => $posDetails['merchantid'],                                
            "SecurityToken" => $posDetails['securitytoken'],
            "IMEI"          => $posDetails['imei'],
            "MerchantStorePosCode"           => $posDetails['poscode'],
            "PlutusTransactionReferenceID"   => $addData['refcode'],
            "Amount"        => $addData['amount'],
        );
        
        $apiUrl = $CI->$model->getPOSApiUrl($posDetails, 'cancel');
        if(empty($apiUrl)) $apiUrl = $CI->config->item('pos_api_request_cancel_url');
                    
        $posrequest = $CI->postcurlPOSRequests($requestData, $apiUrl);
        
        if($posrequest['ResponseCode'] == 0){
            $CI->$model->updateData(array('pos_req_status'=> 2, 'pos_res_payload'=> json_encode($posrequest)), 'pos_res_ref_id', $addData['refcode'], 'ret_pos_requests');
            log_message('info', 'POS Pine Labs Cancel SUCCESS: refId='.$addData['refcode']);
        } else {
            // If transaction already expired/not found at gateway, treat as cancelled
            // (Pine Labs returns: TRANSACTION NOT FOUND, INVALID PLUTUS TXN REF ID, INVALID INPUT)
            $isAlreadyGone = (stripos($posrequest['ResponseMessage'], 'NOT FOUND') !== false ||
                              stripos($posrequest['ResponseMessage'], 'INVALID') !== false);
            
            if($isAlreadyGone){
                $CI->$model->updateData(array('pos_req_status'=> 2, 'pos_res_payload'=> json_encode($posrequest)), 'pos_res_ref_id', $addData['refcode'], 'ret_pos_requests');
                log_message('info', 'POS Pine Labs Cancel: TXN already expired at gateway, marked as cancelled. refId='.$addData['refcode']);
                // Return success so JS cleans up the row properly
                return array("responsecode" => 0, "resmessage" => 'Transaction cancelled (expired at POS).', "transdata" => null);
            }
            
            log_message('error', 'POS Pine Labs Cancel FAILED: refId='.$addData['refcode'].' code='.$posrequest['ResponseCode'].' msg='.$posrequest['ResponseMessage']);
        }
        
        return array("responsecode" => $posrequest['ResponseCode'], "resmessage" => $posrequest['ResponseMessage'], "transdata" => isset($posrequest['TransactionData']) ? $posrequest['TransactionData'] : null);
    }
}
