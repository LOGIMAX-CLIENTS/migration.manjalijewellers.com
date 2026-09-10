<?php

if (! defined('BASEPATH')) exit('No direct script access allowed');

class Chit_transaction extends CI_Controller

{

	const MODEL	= "admin_usersms_model";

	const CUS_MODEL	= "customer_model";

	const VIEW = 'sms/';

	const GRP = 'sms/group/';

	const SMS_VIEW = 'sms/smsService/';

	const NOTI_VIEW = 'notification/';

	const SET_MODEL = 'admin_settings_model';

	const EMAIL_MODEL = 'email_model';

	const ADM_MODEL = 'chitadmin_model';

	const PAY_MODEL = 'payment_model';

	const PAY_VIEW  = "payment/";

	const SET_VIEW  = "scheme/settlement/";

	const API_MODEL = 'chitapi_model';

	const ACC_MODEL = 'account_model';

	const SMS_MODEL = 'admin_usersms_model';

	const LOG_MODEL = "log_model";

	const MAIL_MODEL = "email_model";

	const SERV_MODEL = 'services_model';

	const TEST_VIEW = "settings/";

	const SYN_MODEL = "syncapi_model";

	const OFF_DATA_FILE_PATH = "assets/offline_data/2021-03-08/cus-reg/";

	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model(self::MODEL);

		$this->load->model(self::SERV_MODEL);

		$this->load->model(self::SET_MODEL);

		$this->load->model(self::CUS_MODEL);

		$this->load->model(self::EMAIL_MODEL);

		$this->load->model(self::ADM_MODEL);

		$this->load->model(self::PAY_MODEL);

		$this->load->model(self::ACC_MODEL);

		$this->load->model(self::SMS_MODEL);

		$this->load->model(self::LOG_MODEL);

		$this->load->model(self::MAIL_MODEL);

		$this->load->model("sms_model");

		// $this->load->model("sktm_syncapi_model");
		$this->load->model("chit_transaction_model");

		$this->load->model(self::SYN_MODEL);

		$this->employee =  $this->session->userdata('uid');

		$this->company = $this->admin_settings_model->get_company();

		$this->chit_set = $this->admin_settings_model->get_settings();

		$this->log_dir = 'log/'.date("Y-m-d");

		if (!is_dir($this->log_dir)) {

			mkdir($this->log_dir, 0777, TRUE);
		}
	}

    //AFTER IMPORT UPDATES ENDS<<<<
	//NEW FLOW STARTS/////
    function easebuzz_webhook()
    {
             /*$data =
			  key=SKJ4CE8Q6H&furl=https%3A%2F%2Fwww.chinnannanjewellery.com%2Fcnjemi%2Findex.php%2Fmobile_api%2FeasebuzzFailedResponse%2F&hash=6e5b602c6ca8fb284cb18437e2a88f86e4236e6f2928b87ecadbfb0b7524690c7cfe4528e749f1e13181fefdd443aa9446859054a64e5f3875e1216118d61b0f&mode=UPI&surl=https%3A%2F%2Fwww.chinnannanjewellery.com%2Fcnjemi%2Findex.php%2Fmobile_api%2FeasebuzzSuccessResponse%2F&udf1=&udf2=&udf3=&udf4=&udf5=&udf6=&udf7=&udf8=&udf9=&email=manik19899781%40gmail.com&error=Successful+Transaction&phone=8344221989&txnid=17617386916901ffc306f18&udf10=&amount=2000.0&status=success&upi_va=manik19899781-1%40okaxis&PG_TYPE=NA&addedon=2025-10-29+11%3A51%3A31.000000&cardnum=NA&bankcode=NA&auth_code=&bank_name=NA&card_type=NA&easepayid=E2510290ODWHRH&firstname=KOWSALYA+M&productinfo=Online+Money+Transaction&service_tax=1.08&auth_ref_num=NA&bank_ref_num=566815965652&cardCategory=NA&issuing_bank=NA&name_on_card=NA&discount_code=NA&error_Message=Successful+Transaction&merchant_logo=NA&payment_source=Easebuzz&service_charge=6.0&unmappedstatus=NA&discount_amount=0.0&net_amount_debit=2000.0&payment_category=DEFAULT&settlement_amount=1992.92&cancellation_reason=NA&cash_back_percentage=50.0&deduction_percentage=0.3 */

            // CREATE LOG
    		if (!is_dir($this->log_dir.'/easebuzz')) {
                mkdir($this->log_dir.'/easebuzz', 0777, true);
            }
            $log_path = $this->log_dir.'/easebuzz/web_hook_' . date("Y-m-d") . ".txt";
            // $data = $this->get_values(); 
            $data = file_get_contents('php://input');
            parse_str($data, $outputArray);

            // Step 2: Convert array to JSON
            $json = json_encode($outputArray, JSON_UNESCAPED_SLASHES);
    	    
    		$lg_data = "\n WebHook EASEBUZZ--" . date('Y-m-d H:i:s') . " -- : " .($json);
    		

    		file_put_contents($log_path, $lg_data, FILE_APPEND | LOCK_EX);

			/* $json= {"key":"SKJ4CE8Q6H","furl":"https://www.chinnannanjewellery.com/cnjemi/index.php/mobile_api/easebuzzFailedResponse/","hash":"1198ec8a72ad36cef333fe0cc8710adf7bebeabce140cf08d393f777f2156f27edcf9c45d05d5aeec60bd495d762465ba679d0a984b31ac7d7ec2fe465af03ac","mode":"UPI","surl":"https://www.chinnannanjewellery.com/cnjemi/index.php/mobile_api/easebuzzSuccessResponse/","udf1":"","udf2":"","udf3":"","udf4":"","udf5":"","udf6":"","udf7":"24673","udf8":"","udf9":"","email":"chinnannanjewellery6@gmail.com","error":"Successful Transaction","phone":"9942679193","txnid":"176180975069031556bda94","udf10":"","amount":"1000.0","status":"success","upi_va":"mkshajahanuvais-2@okhdfcbank","PG_TYPE":"NA","addedon":"2025-10-30 07:35:50.000000","cardnum":"NA","bankcode":"NA","auth_code":"","bank_name":"NA","card_type":"NA","easepayid":"E2510300OF5S1O","firstname":"SAJAHAN","productinfo":"Online Money Transaction","service_tax":"0.54","auth_ref_num":"NA","bank_ref_num":"113383091256","cardCategory":"NA","issuing_bank":"NA","name_on_card":"NA","discount_code":"NA","error_Message":"Successful Transaction","merchant_logo":"NA","payment_source":"Easebuzz","service_charge":"3.0","unmappedstatus":"NA","discount_amount":"0.0","net_amount_debit":"1000.0","payment_category":"DEFAULT","settlement_amount":"996.46","cancellation_reason":"NA","cash_back_percentage":"50.0","deduction_percentage":"0.3"}*/
    	
    }

	function manualVerify()
	{
		$txns = $_POST;

		$pg_map = [
			1 => 'verify_PayUpayments',
			2 => 'verify_hdfcpayment',
			3 => 'verifyWithTechProcess',
			4 => 'payment_verify',
			7 => 'payment_verify',
			8 => 'payment_verify'
		];

		$pg_code = $txns['pg_code'] ?? null;

		if (isset($pg_map[$pg_code])) {
			$method = $pg_map[$pg_code];
			if ($pg_code == 4) {
				$this->$method(3,4,$txns); // special case for Cashfree manual verification
			}else if ($pg_code == 8) {
				$this->$method(3,8,$txns); // special case for Easebuzz manual verification
			}else if ($pg_code == 7) {
				$this->$method(3,7,$txns); // special case for Razorpay manual verification
			}else {
				$this->$method($txns);
			}
		} else {
			log_message('error', "Invalid PG code: {$pg_code}");
		}
	}
	function payment_verify($type = '',$gateway, $data = '')
	{
	    if($gateway == 4){   //CASHFREE
			$word = "Cashfree";
		}
		else if ($gateway == 8){  //EASEBUZZ
			$word = "Easebuzz";
		}
		else if ($gateway == 7){  //Razorpay
			$word = "Razorpay";
		}
		switch ($type) {
			case 1: // Webhook

				/*cashfree -  {"data":{"order":{"order_id":"176077114968f33c4d60933","order_amount":2500,"order_currency":"INR","order_tags":{"payment_1":"10057","payment_2":"10058"}},"payment":{"cf_payment_id":"5114921577349","payment_status":"USER_DROPPED","payment_amount":2500,"payment_currency":"INR","payment_message":"Simulated response message","payment_time":"2025-10-18T12:35:53+05:30","bank_reference":"5114921577349","auth_id":null,"payment_method":{"app":{"channel":null,"upi_id":null,"provider":null}},"payment_group":"wallet","international_payment":null,"payment_surcharge":{"payment_surcharge_service_charge":0,"payment_surcharge_service_tax":0}},"customer_details":{"customer_name":"RAHUL J","customer_id":"55821","customer_email":"08VOL9XT@gmail.com","customer_phone":"9789498476"},"payment_gateway_details":{"gateway_name":"CASHFREE","gateway_order_id":null,"gateway_payment_id":null,"gateway_status_code":null,"gateway_order_reference_id":null,"gateway_settlement":null,"gateway_reference_name":null},"payment_offers":null,"terminal_details":null},"event_time":"2025-10-18T12:36:06+05:30","type":"PAYMENT_USER_DROPPED_WEBHOOK"} */

			/* easeuzz = {"key":"SKJ4CE8Q6H","furl":"https://www.chinnannanjewellery.com/cnjemi/index.php/mobile_api/easebuzzFailedResponse/","hash":"1198ec8a72ad36cef333fe0cc8710adf7bebeabce140cf08d393f777f2156f27edcf9c45d05d5aeec60bd495d762465ba679d0a984b31ac7d7ec2fe465af03ac","mode":"UPI","surl":"https://www.chinnannanjewellery.com/cnjemi/index.php/mobile_api/easebuzzSuccessResponse/","udf1":"","udf2":"","udf3":"","udf4":"","udf5":"","udf6":"","udf7":"24673","udf8":"","udf9":"","email":"chinnannanjewellery6@gmail.com","error":"Successful Transaction","phone":"9942679193","txnid":"176180975069031556bda94","udf10":"","amount":"1000.0","status":"success","upi_va":"mkshajahanuvais-2@okhdfcbank","PG_TYPE":"NA","addedon":"2025-10-30 07:35:50.000000","cardnum":"NA","bankcode":"NA","auth_code":"","bank_name":"NA","card_type":"NA","easepayid":"E2510300OF5S1O","firstname":"SAJAHAN","productinfo":"Online Money Transaction","service_tax":"0.54","auth_ref_num":"NA","bank_ref_num":"113383091256","cardCategory":"NA","issuing_bank":"NA","name_on_card":"NA","discount_code":"NA","error_Message":"Successful Transaction","merchant_logo":"NA","payment_source":"Easebuzz","service_charge":"3.0","unmappedstatus":"NA","discount_amount":"0.0","net_amount_debit":"1000.0","payment_category":"DEFAULT","settlement_amount":"996.46","cancellation_reason":"NA","cash_back_percentage":"50.0","deduction_percentage":"0.3"}*/

			/* razorpay = {"entity":"event","account_id":"acc_BFQ7uQEaa7j2z7","event":"order.paid","contains":["payment","order"],"payload":{"payment":{"entity":{"id":"pay_DEStK8twGApHtW","entity":"payment","amount":100,"currency":"INR","status":"captured","order_id":"order_DESso0U9bpuzQc","invoice_id":null,"international":false,"method":"wallet","amount_refunded":0,"refund_status":null,"captured":true,"description":null,"card_id":null,"bank":null,"wallet":"payzapp","vpa":null,"email":"gaurav.kumar@example.com","contact":"+919876543210","notes":[],"fee":2,"tax":0,"error_code":null,"error_description":null,"created_at":1567675034}},"order":{"entity":{"id":"order_DESso0U9bpuzQc","entity":"order","amount":100,"amount_paid":100,"amount_due":0,"currency":"INR","receipt":"rcptid #1","offer_id":null,"status":"paid","attempts":1,"notes":[],"created_at":1567675004}}},"created_at":1567675037} */

				$response = file_get_contents('php://input');
				// 
				if($gateway == 4 || $gateway ==7 ){   //CASHFREE || RAZORPAY
					$data = json_decode($response);
				}
				else if ($gateway == 8){  //EASEBUZZ
            		parse_str($response, $string);
					$data = json_encode($string, JSON_UNESCAPED_SLASHES);
				}
				
				$this->logWebhook($data,$gateway,$word);
				$this->webhook($data,$gateway,$word);
				break;

			case 2: // Auto verification
			case 3: // Manual verification
				$this->verifyTxns($type,$gateway, $data, $word);
				break;

			default:
				log_message('error', "Invalid payment verification type: {$type}");
		}
	}	
	function webhook($data,$gateway='',$word)
	{
		$this->load->model("chit_transaction_model");

		// Identify which parser to use
		if ($gateway == 4) {
			$parsed = $this->parseCashfreeWebhook($data);
		} elseif ($gateway == 8) {
			$parsed = $this->parseEasebuzzWebhook($data);
		} elseif ($gateway == 7) {
			$parsed = $this->parseRazorpayWebhook($data);
		} else {
			log_message('error', 'Unknown Webhook Format: ' . json_encode($data));
			return false;
		}

		if (!$parsed) {
			return false; // parsing failed
		}

		extract($parsed); 
		/**
		 * Now we have:
		 * $transactionId ,  //ref_trans_id, id_transation
		 * $transStatus,  	 //payment_status
		 * $paymentMode,  	 //payment_mode
		 * $payId,			 //payu_id
		 * $id_payment, 	 //id_payment
		 * $txTime, 
		 * $order_id	//payment_ref_number
		 */

		$transactionDetail = $this->checkTransExist($transactionId, $transStatus, $id_payment);
		$transactionValid = $transactionDetail['status'] ?? false;

		if (!empty($transactionId) && $transactionValid) {
			$gatewayResponse = $this->prepareGatewayResponse($paymentMode, $payId, $transStatus, $order_id, "$word WebHook update");
			$payment = $this->chit_transaction_model->updateGatewayResponse($gatewayResponse, $transactionDetail);
			$this->payment_update($payment,$gatewayResponse);
		}

		return true;
	}
    private function parseCashfreeWebhook($data)
	{
		try {
			$order_res   = $data->data->order;
			$payment_res = $data->data->payment;

			return [
				'transactionId' => $order_res->order_id,
				'transStatus'   => strtolower($payment_res->payment_status),
				'paymentMode'   => strtolower($payment_res->payment_group ?? ''),
				'payId'         => $payment_res->cf_payment_id ?? '',
				'order_id' 		=> $payment_res->cf_payment_id ?? '',
				'id_payment'    => array_values((array)$order_res->order_tags),
				'txTime'        => $data->event_time ?? date('Y-m-d H:i:s')
			];
		} catch (Exception $e) {
			log_message('error', 'Cashfree Parse Error: ' . $e->getMessage());
			return false;
		}
	}
	private function parseEasebuzzWebhook($data)
	{
		try {
			$id_payment = [];
			$data = json_decode($data);
			// Example: udf7 may contain "24673" or "24673,24674"
			if (!empty($data->udf7)) {
				$id_payment = strpos($data->udf7, ',') !== false ? 
							explode(',', $data->udf7) : 
							[$data->udf7];
			}

			return [
				'transactionId' => $data->txnid,
				'transStatus'   => strtolower($data->status),
				'paymentMode'   => strtolower($data->mode ?? ''),
				'payId'         => $data->easepayid ?? '',
				'id_payment'    => $id_payment,
				'order_id' => $data->easepayid ?? '',
				'txTime'        => $data->addedon ?? date('Y-m-d H:i:s')
			];
		} catch (Exception $e) {
			log_message('error', 'Easebuzz Parse Error: ' . $e->getMessage());
			return false;
		}
	}
	private function parseRazorpayWebhook($data)
	{
		try {

			if (
				!isset($data->payload->payment->entity)
				|| !isset($data->event)
			) {
				throw new Exception('Invalid Razorpay payload');
			}

			$payment = $data->payload->payment->entity;
			$paymentMode = strtolower($payment->method ?? '');

			if ($paymentMode === 'card' && !empty($payment->card->type)) {
				// debit_card / credit_card
				$paymentMode = strtolower($payment->card->type . '_card');
			}

			return [				
				'transactionId' => $payment->notes->id_transaction ?? '', //ref_trans_id, id_transation
				// payment status (captured, failed, authorized)
				'transStatus'   => strtolower($payment->status ?? ''), //payment_status
				'paymentMode'   => $paymentMode,   //payment_mode
				'payId'         => $payment->id ?? '', //payu_id
				'id_payment'    => isset($payment->notes->id_payments)
					? explode(',', $payment->notes->id_payments)
					: [], //id_payment
				// Razorpay order id
				'order_id' => $payment->order_id ?? '', //payment_ref_number
				'txTime'        => isset($data->created_at)
					? date('Y-m-d H:i:s', $data->created_at)
					: date('Y-m-d H:i:s')
			];

		} catch (Exception $e) {
			log_message('error', 'Razorpay Parse Error: ' . $e->getMessage());
			return false;
		}
	}

	function oldcheckTransExist($transactionId, $transStatus, $id_payments = []) 
	{
		// Base SQL
		$sql = "SELECT id_payment, payu_id, ref_trans_id 
				FROM payment 
				WHERE ref_trans_id = ? 
				AND payment_status != 1";

		$bindings = [$transactionId];
		$id_payments = is_array($id_payments) ? $id_payments : explode(',', $id_payments);

		$id_payments = array_filter($id_payments); // removes "", null, false, 0

		// If id_payments is not empty, add IN condition
		if (!empty($id_payments)) {
			$placeholders = implode(',', array_fill(0, count($id_payments), '?'));
			$sql .= " AND id_payment IN ($placeholders)";
			$bindings = array_merge($bindings, $id_payments);
		}

		$query = $this->db->query($sql, $bindings);

		return [
			'status' => $query->num_rows() > 0,
			'rows'   => $query->result_array()
		];
	}
    function checkTransExist($transactionId, $transStatus, $id_payments = []) 
    {
        $sql = "SELECT id_payment, payu_id, ref_trans_id 
                FROM payment 
                WHERE ref_trans_id = ? 
                AND payment_status != 1";
    
        $bindings = [$transactionId];
    
        // Normalize to flat array
        if (!is_array($id_payments)) {
            $id_payments = explode(',', $id_payments);
        } else {
            // If array contains comma-separated values, expand them
            $temp = [];
            foreach ($id_payments as $v) {
                $temp = array_merge($temp, explode(',', $v));
            }
            $id_payments = $temp;
        }
    
        // Remove empty values
        $id_payments = array_filter($id_payments, 'strlen');
    
        if (!empty($id_payments)) {
            $placeholders = implode(',', array_fill(0, count($id_payments), '?'));
            $sql .= " AND id_payment IN ($placeholders)";
            $bindings = array_merge($bindings, $id_payments);
        }
    
        $query = $this->db->query($sql, $bindings);
    
    	return [
    			'status' => $query->num_rows() > 0,
    			'rows'   => $query->result_array()
    		];
    }
	function verifyTxns($type, $gateway, $data = '', $word) {
		$model = self::PAY_MODEL;
		$transData = array();
		
		// Initialize common variables
		$gateway_info = $this->initializeVerification($type, $gateway, $data);
		if (!$gateway_info) {
			echo "No payment records to verify.";
			return;
		}

		$payData = $gateway_info['payData'];
		$remark = $gateway_info['remark'];
		$secretKey = $gateway_info['param_1'];
		$appId = $gateway_info['param_3'];
		$api_url = $gateway_info['api_url'];
		$headers = $this->prepareHeaders($gateway_info);

		// Process all transactions
        // 		print_r($payData);exit;
		$result = $this->processAllTxns($payData, $gateway, $type, $appId, $secretKey, $api_url, $headers, $remark, $word);
		
		// Output results
		echo implode("<br>", $result['output']) . "<br><br>";
		echo "Total Records: " . $result['total'] . "<br>";    
		echo "Verified: " . $result['verified'];
	}
	private function initializeVerification($type, $gateway, $data) {
		if ($type == 2) {
			return $this->initializeAutoVerification($gateway,$data);
		} else if ($type == 3) {
			return $this->initializeManualVerification($data,$gateway);
		}
		return null;
	}
	private function initializeAutoVerification($gateway,$id_branch) {
		$remark = "Auto Verification";
		$pg_code = $gateway;
		// $id_branch = '';
		$previousDay = date('Y-m-d', strtotime("-3 days"));
		$currentDay = date('Y-m-d', strtotime(" 0 days"));
		
		$gateway_info = $this->chit_transaction_model->getBranchGatewayData($id_branch, $pg_code);
		//print_r($gateway_info);exit;
		if (!$gateway_info) return null;
		
		$gateway_info['payData'] = $this->chit_transaction_model->getPendpayment_Data($previousDay, $currentDay, $id_branch, $gateway_info['id_pg']);
		$gateway_info['remark'] = $remark;
		
		return $gateway_info;
	}
	private function initializeManualVerification($data,$gateway) {
		$gateway_info = $this->chit_transaction_model->getBranchGatewayData($data['id_branch'], $data['pg_code']);
		if (!$gateway_info) return null;
		
		$remark = "Manual Verification";
		$gateway_info['remark'] = $remark;

		// Prepare payment data
		if (isset($data['txn_ids']) && is_array($data['txn_ids'])) {
			$tran = [];
			foreach ($data['txn_ids'] as $txn) {
				//  $tran[] = $this->chit_transaction_model->getPayData($txn);
				/* $tran[] = [
                        'txn_ids' => $txn,
                        'pg_code' => $data['pg_code'],
                        'id_branch' => $data['id_branch']
                    ]; */
				if ($gateway == 8){
					$tran[] = $this->chit_transaction_model->getPayData($txn);
				}
				else{
					$tran[] = [
                        			'txn_ids' => $txn,
                        			'pg_code' => $data['pg_code'],
                        			'id_branch' => $data['id_branch']
                   			];
				}
			}
		} else {
			$tran = [$data];
		}
		
		$gateway_info['payData'] = $tran;
		return $gateway_info;
	}
	private function prepareHeaders($gateway_info) {
		return [
			'x-client-id: ' . $gateway_info['param_3'],
			'x-client-secret: ' . $gateway_info['param_1'],
			'x-api-version: 2022-09-01'
		];
	}
	private function processAllTxns($payData, $gateway, $type, $appId, $secretKey, $api_url, $headers, $remark, $word) {
		$vCount = 0;
		$result = [];
		$total = count($payData);

		foreach ($payData as $tran) {
			

			if ($gateway == 4) {
				$txn_id = $tran['txn_ids'] ?? '';
				if (empty($txn_id)) continue;
				$vCount += $this->processCashfreeTxns($word,$tran, $txn_id, $type, $appId, $secretKey, $api_url, $headers, $remark, $result);
			} else if ($gateway == 8) {
				if($type == 2){
					$txn_id = $tran['txn_ids'] ?? '';
					if (empty($txn_id)) continue;
					$vCount += $this->processEasebuzzTxns($word,$tran, $txn_id, $type, $appId, $secretKey, $api_url, $remark, $result);

				}else{
				foreach ($tran as $data) {
					$txn_id = $data['txn_ids'] ?? '';
					if (empty($txn_id)) continue;
					$vCount += $this->processEasebuzzTxns($word,$data, $txn_id, $type, $appId, $secretKey, $api_url, $remark, $result);
				}
			}
		} else if ($gateway == 7) {
				$vCount += $this->processRazorpyTxns($word,$tran, $txn_id, $type, $appId, $secretKey, $api_url,$headers, $remark, $result);
			}
		}

		return [
			'output' => $result,
			'total' => $total,
			'verified' => $vCount
		];
	}
	// Optimized gateway processors (minimal function calls)
	private function processCashfreeTxns($word,$tran, $txn_id, $type, $appId, $secretKey, $api_url, $headers, $remark, &$result) {
		$postData = "appId=" . $appId . "&secretKey=" . $secretKey . "&orderId=" . $tran['txn_ids'];
		
		$response = $this->curlRequest("$api_url/{$tran['txn_ids']}/payments", $headers, $word, $appId, $secretKey);
		$this->debugLog("PAYMENT RESPONSE", $txn_id, $response, $type);
		
		if (is_array($response) && isset($response['error'])) {
			echo "cURL Error: " . $response['error'];
			return 0;
		}
		
		$ord_response = $this->curlRequest("$api_url/{$tran['txn_ids']}", $headers, $word, $appId, $secretKey);
		$this->debugLog("ORDER RESPONSE", $txn_id, $ord_response, $type);

		$order_tags = isset($ord_response->order_tags) ? (array)$ord_response->order_tags : [];
		$id_payment = array_values($order_tags);
		$this->logGatewayResponse($word,$remark, $txn_id, $response, $ord_response, $postData);
		
		// Handle empty or not found response
		if (empty($response) || ($response->code ?? '') == 'order_not_found') {
			return $this->handleNotFoundTxns($txn_id, $type, $response, $id_payment, $result);
		}
		
		// Extract and process payment response
		$cf_response = $this->extractPaymentResponse($response);
		if (!$cf_response) return 0;
		
		return $this->processCashfreePayment($cf_response, $txn_id,$type, $id_payment, $remark, $result);
	}
	private function processEasebuzzTxns($word,$tran, $txn_id, $type, $appId, $secretKey, $api_url, $remark, &$result) {
		$tran['email'] = $tran['email'] ?? 'support@logimaxindia.com';
		$amount = number_format((float) round($tran['payment_amount']), 1, '.', '');
		
		$hash_sequence = trim($secretKey) . '|' . trim($tran['txn_ids']) . '|' . trim($amount) . '|' . 
						trim($tran['email']) . '|' . trim($tran['mobile']) . '|' . trim($appId);
		$hash_value = strtolower(hash('sha512', $hash_sequence));
		
		$postData = "txnid=" . trim($tran['txn_ids']) . "&key=" . trim($secretKey) . "&amount=" . 
					trim($amount) . "&email=" . trim($tran['email']) . "&phone=" . 
					trim($tran['mobile']) . "&hash=" . trim($hash_value);
		
		$response = $this->curlPostRequest($api_url, $postData);
		$this->debugLog("PAYMENT RESPONSE", $txn_id, $response, $type);
		$this->logGatewayResponse($word,$remark, $txn_id, $response, '', $postData);
		
		if (empty($response) || ($response->msg ?? '') == 'Transaction not found.') {
			return $this->handleNotFoundTxns($txn_id, $type, $response, [], $result);
		}
		
		if (empty($response->status)) return 0;
		
		$trans_id = $response->msg->txnid ?? '';
		$paymentMode = strtolower($response->msg->mode ?? '');
		$referenceId = $response->msg->easepayid ?? '';
		$status_code = strtolower($response->msg->status ?? '');
		$order_tags = isset($response->msg->udf7) ? (array)$response->msg->udf7 : [];
		$id_payment = array_values($order_tags);
		
		return $this->processEasebuzzPayment($trans_id, $paymentMode, $referenceId, $status_code, $id_payment, $remark, $result);
	}
	private function processRazorpyTxns($word,$tran, $txn_id, $type, $appId, $secretKey, $api_url, $headers, $remark, &$result) {
		$postData = "appId=" . $appId . "&secretKey=" . $secretKey . "&orderId=" . $tran['txn_ids'];
		$order_id = $this->chit_transaction_model->getRazorOrderid($tran['txn_ids']);
		//print_r($order_id);exit;
		$response = $this->curlRequest("$api_url/{$order_id}/payments", $headers, $word, $appId, $secretKey);
		$this->debugLog("PAYMENT RESPONSE", $txn_id, $response, $type);
		
		if (is_array($response) && isset($response['error'])) {
			echo "cURL Error: " . $response['error'];
			return 0;
		}
		
		$ord_response = $this->curlRequest("$api_url/{$order_id}", $headers, $word, $appId, $secretKey);
		$this->debugLog("ORDER RESPONSE", $txn_id, $ord_response, $type);
        
		$order_tags = isset($ord_response->notes->id_payments) ? (array)$ord_response->notes->id_payments : [];
		$id_payment = array_values($order_tags);
		$this->logGatewayResponse($word,$remark, $txn_id, $response, $ord_response, $postData);
		
		// Handle empty or not found response
		if (empty($response) || isset($response->error)) {
		      $error = isset($response->error) ? $response->error : null;
			return $this->handleNotFoundTxns($txn_id, $type, $error, $id_payment, $result);
		}
		// Extract and process payment response
		$payment = null;

        foreach ($response->items as $item) {
            if (($item->status ?? '') === 'captured') {
                $payment = $item;
                break;
            }
        }

        if (!$payment && !empty($response->items)) {
            $payment = end($response->items);
        }

        $payment = $payment ?? ($response->items[0] ?? null);
        
        if (!$payment) {
            return 0; // no valid payment
        }
        
        

		if (!$payment) return 0;
		
		return $this->processRazorpayPayment($payment, $txn_id,$type, $id_payment, $remark, $result);
	}
	// Helper methods
	private function hasCurlError($response) {
		return is_array($response) && isset($response['error']);
	}
	private function extractPaymentResponse($response) {
		$cf_response = $response[0] ?? null;
		
		foreach ($response as $r) {
            if (($r->payment_status ?? '') === 'SUCCESS') {
				return $r;
			}
		}
		
		return $cf_response;
	}
	private function handleNotFoundTxns($txn_id, $type, $response='', $id_payment, &$result) {
		$gwData = [
			"remark" => ($type == 2 ? "AUTO" : "MANUAL") . " VERIFIED - " . date('Y-m-d_H-i-s'),
			"is_gateway_verified" => 1,
			"payment_status" => 4,
			"dev_remark" => $response->code ?? "No response"
		];

		$transactionDetail = $this->checkTransExist($txn_id, 'cancelled', $id_payment);
		if (!empty($transactionDetail['status'])) {
			$this->chit_transaction_model->updateGatewayResponse($gwData, $transactionDetail);
			$result[] = $txn_id . " - cancelled";
			// $vCount++;
		}
	}
	private function processCashfreePayment($cf_response, $txn_id, $type, $id_payment, $remark, &$result) {
		$status_code = strtolower($cf_response->payment_status);
		$payment_mode = strtolower($cf_response->payment_group);
		
		$transactionDetail = $this->checkTransExist($txn_id, $status_code, $id_payment);
		$transactionValid = $transactionDetail['status'];
		$payu_id = $cf_response->cf_payment_id;
		$order_id = $cf_response->cf_payment_id;

		if (!empty($txn_id) && $transactionValid && !in_array($status_code, ['pending', 'flagged', ''])) {
			$updateData = $this->prepareGatewayResponse($payment_mode, $payu_id, $status_code, $order_id, $remark);
			$payment = $this->chit_transaction_model->updateGatewayResponse($updateData, $transactionDetail);
			$result[] = $txn_id . " - " . $status_code;
			$this->payment_update($payment, $updateData);
			// $vCount++;
		}
	}
	private function processEasebuzzPayment($trans_id, $paymentMode, $referenceId, $status_code, 
												 $id_payment, $remark, &$result) {
		$transactionDetail = $this->checkTransExist($trans_id, $status_code, $id_payment);
		$transactionValid = $transactionDetail['status'];
		$order_id = $referenceId;

		if (!empty($trans_id) && $trans_id != NULL && $transactionValid && 
			!in_array($status_code, ['pending', 'flagged', ''])) {
			$updateData = $this->prepareGatewayResponse($paymentMode, $referenceId, $status_code,$order_id, $remark);
			$payment = $this->chit_transaction_model->updateGatewayResponse($updateData, $transactionDetail);
			$result[] = $trans_id . " - " . $status_code;
			$this->payment_update($payment, $updateData);
			// $vCount++;
		}
	}
	private function processRazorpayPayment($payment, $txn_id, $type, $id_payment, $remark, &$result) {
		// Extract values
		$status_code  = $payment->status ?? '';
        $payment_mode = $payment->method ?? '';
		
		$transactionDetail = $this->checkTransExist($txn_id, $status_code, $id_payment);

		if ($payment_mode === 'card' && !empty($payment->card->type)) {
			// debit_card / credit_card
			$payment_mode = strtolower($payment->card->type . '_card');
		}

		//print_r($transactionDetail);exit;
		$transactionValid = $transactionDetail['status'];
		$payu_id = $payment->id ?? '';
		$order_id = $payment->order_id ?? '';

		if (!empty($txn_id) && $transactionValid && !in_array($status_code, ['pending', 'flagged', ''])) {
			$updateData = $this->prepareGatewayResponse($payment_mode, $payu_id, $status_code, $order_id, $remark);
			
			$payment = $this->chit_transaction_model->updateGatewayResponse($updateData, $transactionDetail);
			$result[] = $txn_id . " - " . $status_code;
			$this->payment_update($payment, $updateData);
			// $vCount++;
		}
	}
	private function debugLog($label, $txn_id, $data, $type) {
		if ($type == 2) {
			echo "<pre>$label - $txn_id<br>";
			print_r($data);
			echo "</pre><br>";
		}
	}
    function payment_update($payment){
		if ($payment['status'] == true && sizeof($payment['payids']) > 0) 
		{
			foreach ($payment['payids'] as $payid) {
				//perform on pay success functionalities
				if ($payid['payment_status'] == 1) {
					$this->chit_transaction_model->onPayTranStream($payid['id_payment']);
					$serviceID = 3;
				} else {
					$serviceID = 7;
				}

				//send notifications
				$service = $this->services_model->checkService($serviceID);
				if ($service['sms'] == 1) {
					$id = $payid['id_payment'];
					$data = $this->services_model->get_SMS_data($serviceID, $id);
					$mobile = $data['mobile'];
					$message = $data['message'];
					//$this->mobileapi_model->send_sms($mobile,$message,'',$service['dlt_te_id']);
					if ($this->config->item('sms_gateway') == '1') {
						$this->sms_model->sendSMS_MSG91($mobile, $message, '', $service['dlt_te_id']);
					} elseif ($this->config->item('sms_gateway') == '2') {
						$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
					}
				}
			}
		}
	}
	private function getGatewayHeaders($gateway_info)
	{
		return [
			'x-client-id: ' . $gateway_info['param_3'],
			'x-client-secret: ' . $gateway_info['param_1'],
			'x-api-version: 2022-09-01'
		];
	}
	private function logWebhook($data,$gateway,$word)
	{
        if($gateway == 4 || $gateway == 7){
             $data = json_encode($data);
        }
		// print_r($data);exit;
		$dir = $this->log_dir . '/'.$word.'';
		if (!is_dir($dir)) mkdir($dir, 0777, true);
		$log_path = "$dir/$word web_hook" . date("Y-m-d") . ".txt";
		$lg_data = "\n '.$word.' WebHook --" . date('Y-m-d H:i:s') . " -- : " . $data;
		file_put_contents($log_path, $lg_data, FILE_APPEND | LOCK_EX);
	}
	private function ensureLogDirectory($folder)
	{
		$path = $this->log_dir . date("Y-m-d") . '/' . $folder;
		if (!is_dir($path)) mkdir($path, 0777, true);
	}
	private function prepareGatewayResponse($paymentMode, $payId, $transStatus, $order_id, $remarkPrefix = "Update"){
		return [
			"payment_mode"       => $this->mapPaymentMode($paymentMode),
			"payu_id"            => $payId,
			"remark"             => "$remarkPrefix on " . date('Y-m-d H:i:s'),
			"payment_ref_number" => $order_id,
			"payment_status"     => $this->mapPaymentStatus($transStatus)
		];
	}
	private function logGatewayResponse($word,$remark, $txn_id, $response, $ord_response='', $postData) {
		$dir = $this->log_dir . '/' . $word;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $log_path = "$dir/$word mob_response_" . date("Y-m-d") . ".txt";
        $data  = "\n-----\n$word $remark CURL RESPONSE - $txn_id " . date("Y-m-d H:i:s");
        $data .= ": " . json_encode($response);
        $data .= !empty($ord_response) ? " " . json_encode($ord_response) : "";
        $data .= "\nPOST DATA: " . json_encode($postData);
        
        file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);

	}
	private function mapPaymentMode($mode){
		$mode = strtolower(trim($mode));
		switch($mode){
			case 'credit_card': return 'CC';
			case 'debit_card':  return 'DC';
			case 'net_banking':
			case 'netbanking':	 return 'NB';
			case 'na':          return 'NA';
			default:            return $this->chit_transaction_model->update_paymentMode($mode);
		}
	}
	private function mapPaymentStatus($status){
		$status = strtolower($status);
		switch($status){
			case 'success':
			case 'captured':
				return 1;
			case 'usercancelled':
			case 'user_dropped':
			case 'not_attempted':
			case 'dropped':
			case 'bounced':
			case 'failure':
				return 4;
			case 'failed':
				return 3;
			case 'refund':
				return 6;
			default:
				return 7;
				// return $this->update_paymentStatus($status);
		}
	}
	private function curlRequest($url, $headers = [], $word, $appId, $secretKey) {
	    
	    
		$curl = curl_init();
		
		$curlOptions = [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 8,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => $headers,
        ];
        // Add Basic Auth ONLY for Razorpay
        if ($word === 'Razorpay') {
            $curlOptions[CURLOPT_USERPWD] = $appId.':'.$secretKey;
        }
        
        curl_setopt_array($curl, $curlOptions);

		//print_r($url);exit;
		$response = curl_exec($curl);
		$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		print_r($httpCode);
		$err = curl_error($curl);
		curl_close($curl);
		if ($err) return ['error' => $err];
		return json_decode($response);
	}
	private function curlPostRequest($url, $postData = [], $headers = [])
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => is_array($postData) ? http_build_query($postData) : $postData,
            CURLOPT_HTTPHEADER => $headers ?: [
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return ['error' => $err];
        }
        return json_decode($response);
    }
	function update_paymentStatus($status){

		$status = strtolower(trim($status));

		// Check if mode_name or short_code already matches (case-insensitive)
		$this->db->from('payment_status_message');
		 $this->db->where('LOWER(payment_status)', $status);
		$query = $this->db->get();

		$short_code = strtoupper($status); // e.g., upi_payment -> UPI_PAYMENT

		if ($query->num_rows() == 0 && !empty($status)) {
			// Convert mode to proper format
			$formatted_mode_name = ucwords(str_replace('_', ' ', $status)); // e.g., upi_payment -> Upi Payment
			

			// Insert new payment mode
			$data = array(
				'payment_status'   => $formatted_mode_name,
				'remark'      => $formatted_mode_name,
				'color'  => ''
			);
			$this->db->insert('payment_status_message', $data);
		}
		return $short_code;
	}

	function integration_update()
	{
		$model = self::SYN_MODEL;
		$this->load->model($model);

		$acc_upd = $this->$model->get_acc_data();

		if (!empty($acc_upd)) {
			foreach ($acc_upd as $acc) {
				$this->chit_transaction_model->insert_common_data($acc['ref_no']);
			}
		}else {
			echo "No Results found";
		}
		
		
	}

	 //To insert payment and registration details in intermediate table

	function insert_common_data_old($id_payment)

	{


		$model = 'syncapi_model';

		$this->load->model($model);

		

		//getting payment detail

		$pay_data = $this->$model->getPaymentByID($id_payment);	
	

		//storing temp values

		$ref_no = $pay_data[0]['ref_no'];

		$id_scheme_account = $pay_data[0]['id_scheme_account']; 

		// Validate gent_clientid setting — skip client_id if disabled
		$chit_settings = $this->db->query("SELECT gent_clientid FROM chit_settings LIMIT 1")->row_array();
		if(empty($chit_settings['gent_clientid']) || $chit_settings['gent_clientid'] == 0){
			$pay_data[0]['client_id'] = '';
		}

		$isCusRegExists = $this->$model->checkCusRegExists($id_scheme_account,$ref_no);

		if(!$isCusRegExists['status']){

		     $reg = $this->$model->getCustomerByID($id_scheme_account);	

             //insert customer registration detail

             if($reg)

             {

            	$reg[0]['record_to']= 1 ;

            	$reg[0]['is_registered_online']= 2 ;  // 2 - online record

            	$reg[0]['ref_no']		= $ref_no;

            	// Skip clientid if gent_clientid is disabled
            	if(empty($chit_settings['gent_clientid']) || $chit_settings['gent_clientid'] == 0){
            		$reg[0]['clientid'] = NULL;
            	}

            	$status = $this->$model->insert_CustomerReg($reg[0]);

             }	

		}

		$isTranExists = $this->$model->checkTransExists($ref_no);


		if(!$isTranExists['status'])

		{

            //insert payment detail

            $pay_data[0]['record_to'] = 1;	

            $pay_data[0]['payment_type'] = 1;	// 1 - online 


            $status =	$this->$model->insert_transaction($pay_data[0]); 

		}

		//echo $this->db->last_query();exit;

		return true;

	}

	function insert_common_data($id_payment)
	{
		$model = self::SYN_MODEL;

		$this->load->model($model);

		//getting payment detail

		$pay_data = $this->$model->getPaymentByID($id_payment);

		//storing temp values
		$ref_no = $pay_data[0]['ref_no'];
		$id_scheme_account = $pay_data[0]['id_scheme_account']; 

		$isCusRegExists = $this->$model->checkCusRegExists($id_scheme_account,$ref_no);

		$reg = $this->$model->getCustomerByID($id_scheme_account);

		$reg_1 = $this->$model->getCustomerDet($id_scheme_account);

		$reg[0]['record_to']= 1 ;

		$reg[0]['is_registered_online']= 2 ;  // 2 - online record

		$reg[0]['ref_no']		= $ref_no;
		
		// Skip clientid if gent_clientid is disabled
        $chit_settings = $this->db->query("SELECT gent_clientid FROM chit_settings LIMIT 1")->row_array();
			
		if(empty($chit_settings['gent_clientid']) || $chit_settings['gent_clientid'] == 0){
            $reg[0]['clientid'] = '';
        }
		
		if(!$isCusRegExists['status']) {

            
			//insert customer registration detail
			$status = $this->$model->insert_CustomerReg($reg[0]);

			$runDirectAPI = true;

		} 
       /*  elseif($isCusRegExists['status'] && $isCusRegExists['clientid'] == null && $isCusRegExists['is_transferred'] == 'N' && $chit_settings['gent_clientid'] == 1) {

			$reg_data = array(
				'clientid' => "ON-".$id_scheme_account
			);
			$this->$model->update_CustomerReg($reg_data, $isCusRegExists['id_customer_reg']);

			$runDirectAPI = true;

		}  */
        elseif ($isCusRegExists['status'] && $isCusRegExists['is_transferred'] == 'N') {

			$runDirectAPI = true;
		} else {

			$runDirectAPI = false; // for all other cases
		}

		if($runDirectAPI && $this->config->item('directAPI') == '1') {

            if (!empty($reg[0]['maturity_date'])) {

                $maturitydate = $reg[0]['maturity_date'];
            } else {

                $total_installment = $reg_1[0]['total_installment'];
                    $maturitydate = date('Y-m-d', strtotime("+" . $reg_1[0]['maturity_days'] . " days" , strtotime($reg[0]['reg_date'])));

                $maturitydate = date('Y-m-d', strtotime("+" . $total_installment . " months", strtotime($reg[0]['reg_date']))); 
                if($reg_1[0]['maturity_type'] == 2){
                    $maturitydate = date('Y-m-d', strtotime("+" . ($reg_1[0]['maturity_days'] + $reg_1[0]['closing_maturity_days']) . " days", strtotime($reg[0]['reg_date'])));

                }
                else{
                    $maturitydate = date('Y-m-d', strtotime("+" .$reg_1[0]['closing_maturity_days'] . " days", strtotime($reg[0]['reg_date'])));
                } 
            }
		
			$account = array(
				'customer' => array(
					'customerid'    => $reg_1[0]['id_customer'],
					'customerName'  => $reg[0]['firstname'],
					'mobileNo'      => $reg[0]['mobile'],
					'branch'        => (int) $reg[0]['id_branch'],
					'insert_update' => 1,
					'cardnumber'    => $reg[0]['mobile'],
					'nomineeMobile' => $reg_1[0]['nomineeMobile'],
					'doorNo'        => 'NA',
					'street'        => 'NA',
					'area'          => $reg_1[0]['area'],
					'taluk'         => 'NA',
					'city'          => $reg_1[0]['city'],
					'pinCode'       => $reg_1[0]['pincode'],
					'state'         => $reg_1[0]['state']
				),
				'joining' => array(
					'clientid'     => $reg[0]['clientid'],
					'schemeCode'   => $reg[0]['sync_scheme_code'],
					'schemeid'     => (int) $reg_1[0]['id_scheme'],
					'schemerefid'  => (int) $id_scheme_account,
					'schemeamount' => (int) $pay_data[0]['amount'],
					'cardnumber'   => $reg[0]['mobile'],
					'groupname'    => $reg[0]['sync_scheme_code'],
					'startdate'    => $reg[0]['reg_date'],
					'enddate'      => $maturitydate,
					'branch'       => (int) $reg[0]['id_branch'],
					'digi'         => (int) $reg[0]['is_digi']
				)
			);
            
			$response = $this->sendtoDirectApi('/scheme-joining-insertion/insert',$account);

			if($response->success == true) {
				$cus_reg_data = $this->$model->getCustomerRegbyID($id_scheme_account);
				
				$acc_data = array(
					'scheme_acc_number' => $response->data->softwareJoinNo,
					'ref_no'            => $response->data->clientid,
					'date_upd'          => date("Y-m-d H:i:s")
				);

				if (!empty($response->data->softwareJoinNo)) {
					$acc_status = $this->$model->update_account(
						$acc_data, 
						$cus_reg_data[0]['id_scheme_account'], 
						$cus_reg_data[0]['id_customer_reg']
					);
				}
				
			}

			// Log response
			if (!is_dir($this->log_dir.'/directAPI')) {
				mkdir($this->log_dir.'/directAPI', 0777, true);
			}

			$log_path = $this->log_dir.'/directAPI/response'.date("Y-m-d").'.txt';
			$ldata = "\n".date('d-m-Y H:i:s')
				." \n Acc Postdata : ".json_encode($account,true)
				." \n Acc Response :".json_encode($response,true);

			file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
		}

		$isTranExists = $this->$model->checkTransExists($ref_no);
	
		$payID_data = $this->$model->getPayIDdet($id_payment);

        $pay_data[0]['client_id'] = $payID_data[0]['clientid'] ;
        

		if(!$isTranExists['status'])
		{

			//insert payment detail

			$pay_data[0]['record_to'] = 1;	

			$pay_data[0]['payment_type'] = 1;	// 1 - online 

			$status =	$this->$model->insert_transaction($pay_data[0]); 

			if(!empty($payID_data[0]['scheme_acc_number'])){
			    $runPayDirect = true;
            }

		} 
        elseif ($isTranExists['status'] && ($isTranExists['client_id'] == null || $isTranExists['client_id'] == '')) {
			$trans_data = array(
				'client_id' => $pay_data[0]['client_id']
			);
			$this->$model->update_transaction($trans_data,$isTranExists['id_transaction']);

			$runPayDirect = true;
		} 
        else if ($isTranExists['status'] && $isTranExists['is_transferred'] == 'N' && !empty($payID_data[0]['scheme_acc_number'])) {

			$runPayDirect = true;

		}else {
			
			$runPayDirect = false;
		}

			//For online payments Send in direct API and update receipt no , ref no 

		if ($runPayDirect && $this->config->item('directAPI') == '1') {
				$payment = array(
                    "payment" => array(
                        "paymentbranch" => (int)$pay_data[0]['id_branch'],
                        "schemeid" => (int)$payID_data[0]['id_scheme'],
                        "schemename" => $payID_data[0]['scheme_name'],
                        "schemeamount" => (float)$pay_data[0]['amount'],
                        "groupno" => ($payID_data[0]['scheme_acc_number'] ? $payID_data[0]['scheme_acc_number'] : ''),
                        "groupname" => ($payID_data[0]['group_code'] != "" ? $payID_data[0]['group_code'] : ''),
                        "customermobile" => (int) $pay_data[0]['mobile'],
                        "cardnumber" => $pay_data[0]['mobile'],
                        "customerid" => (int)$payID_data[0]['id_customer'],
                        "monthyear" => $pay_data[0]['payment_date'],
                        "saved_weight" => (float)$pay_data[0]['weight'],
                        "saved_benefitswt" => (float)$pay_data[0]['saved_benefits_wgt'],
                        "saved_benefit_amt" => (float)$pay_data[0]['saved_benefit_amt'],
                        "installment" => (int) $payID_data[0]['installment'],
                        "benefit_value" => $pay_data[0]['benefit_value'],
                        "benefit_type" => (int) $pay_data[0]['benefit_type'],
                        "is_digi" => (int) $pay_data[0]['is_digi'],
                        "goldrate" => (int)$pay_data[0]['rate'],
                        "customername" => $payID_data[0]['customername'],
                        "onlinepaymentrefid" => ($pay_data[0]['pay_trans_id'] ? $pay_data[0]['pay_trans_id'] : ''),
                        "onlinepayment" => ($payID_data[0]['added_by'] == 2 || $payID_data[0]['added_by'] == 4) ? 1 : 0,
                        "onlineamount" => (float)$pay_data[0]['amount'],
                        "clientid" => ($payID_data[0]['clientid'] ? $payID_data[0]['clientid'] : ''),
                        "schemerefid" => (string) $id_payment
                    )
			);
				
				$response = $this->sendtoDirectApi('/scheme-payment-entry-insertion/insert',$payment);
						
				if ($response->success == true) {
					$isClientID =  $this->$model->checkClientID($pay_data[0]['id_scheme_account'],$response->data->clientid);

					if (!empty($response->data->softwareVchNo)) {
						if ($isClientID['status']) {
							$pay_array = array(
								'receipt_no' => $response->data->softwareVchNo,
								'date_upd'	 => date("Y-m-d H:i:s")
							);

							$pay_status = $this->$model->updatedirPayment($pay_array,$pay_data[0]['ref_no']);

						}
					}
					
				}

				if (!is_dir($this->log_dir.'/directAPI')) 
				{
					mkdir($this->log_dir.'/directAPI', 0777, true);
				}
				$log_path = $this->log_dir.'/directAPI/response'.date("Y-m-d").'.txt';
				$ldata = "\n".date('d-m-Y H:i:s')." \n Postdata : ".json_encode($payment,true)." \n Response :".json_encode($response,true);
				file_put_contents($log_path,$ldata,FILE_APPEND | LOCK_EX);
		}
		
		//echo $this->db->last_query();exit;

		return true;

	}


	function sendtoDirectApi($api,$postData)
	{
		$url = $this->config->item('directAPIurl').$api;

		// print_r(json_encode($postData));exit;

		/* $response = [
			"success" => true,
			"message" => "string",
			"data" => [
				"softwarePaymentId" => "5000098",
				"softwareVchNo" => "1",
				"clientid" => "12345",
			]
		]; */


		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => json_encode($postData),
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache",
				"content-type: application/json"
			),
		));

		$response = curl_exec($curl);

		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return false;
		} else {
			return json_decode($response);
		}

	}

	function resync_receipt()
	{
		$model = self::SYN_MODEL;
		$id_payment = $this->input->post('id_payment');
		$id_scheme_account = $this->input->post('id_scheme_account');

		$this->db->trans_begin();

		// Step 1: Get current state of customer_reg + transaction for this payment
		$cus_reg = $this->$model->check_receipt($id_payment, $id_scheme_account);

		// Step 2: If scheme_acc_number is still empty, reset customer_reg transfer flag
		// so the Direct API will re-push the customer registration
		if(!empty($cus_reg) && empty($cus_reg['scheme_acc_number'])){
			$cus_reg_upd = array(
				'is_transferred' => 'N',
				'date_update' => date("Y-m-d H:i:s")
			);
			$this->$model->update_CustomerReg($cus_reg_upd, $cus_reg['id_customer_reg']);
		}

		// Step 3: Reset transaction transfer flag so the Direct API will re-push the payment
		if(!empty($cus_reg)){
			$pay_data = array(
				'is_transferred' => 'N',
				'date_upd' => date("Y-m-d H:i:s")
			);
			$this->$model->update_transaction($pay_data, $cus_reg['id_transaction']);
		}

		// Step 4: Re-trigger the full Direct API push (customer + payment)
		$this->insert_common_data($id_payment);

		// Step 5: Check if the receipt number and scheme account number were successfully updated/generated
		$updated_cus_reg = $this->$model->check_receipt($id_payment, $id_scheme_account);
		$receipt_generated = !empty($updated_cus_reg['receipt_no']) && $updated_cus_reg['receipt_no'] != '-';
		$account_generated = !empty($updated_cus_reg['scheme_acc_number']) && $updated_cus_reg['scheme_acc_number'] != '-';

		if ($this->db->trans_status() === TRUE && $receipt_generated && $account_generated) {
			$this->db->trans_commit();
			$this->session->set_flashdata('chit_alert', array(
				'message' => 'Receipt number generated successfully for the payment id ' . $id_payment,
				'class' => 'success',
				'title' => 'Scheme Payment'
			));
			echo json_encode(['status' => true]);
		} else {
			$this->db->trans_rollback();
			$this->session->set_flashdata('chit_alert', array(
				'message' => 'Receipt number not generated',
				'class' => 'danger',
				'title' => 'Scheme Payment'
			));
			echo json_encode(['status' => false]);
		}
	}

	function resync_by_date_range()
	{
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');

		// Fetch all eligible online payments in the date range that need resync
		$sql = "SELECT id_payment, id_scheme_account
				FROM payment
				WHERE DATE(date_payment) BETWEEN ? AND ?
				  AND payment_status = 1
				  AND (receipt_no IS NULL OR receipt_no = '' OR receipt_no = '-')
				  AND (is_offline = 0 OR is_offline IS NULL)";
		$query = $this->db->query($sql, array($from_date, $to_date));
		$payments = $query->result_array();

		if (empty($payments)) {
			$this->session->set_flashdata('chit_alert', array(
				'message' => 'No pending resync payments found in the selected date range.',
				'class' => 'warning',
				'title' => 'Bulk Resync'
			));
			echo json_encode([
				'status' => true,
				'success_count' => 0,
				'fail_count' => 0
			]);
			return;
		}

		$success_count = 0;
		$fail_count = 0;
		$model = self::SYN_MODEL;

		foreach ($payments as $pay) {
			$id_payment = $pay['id_payment'];
			$id_scheme_account = $pay['id_scheme_account'];

			$this->db->trans_begin();

			// Step 1: Get current state of customer_reg + transaction for this payment
			$cus_reg = $this->$model->check_receipt($id_payment, $id_scheme_account);

			// Step 2: If scheme_acc_number is still empty, reset customer_reg transfer flag
			if(!empty($cus_reg) && empty($cus_reg['scheme_acc_number'])){
				$cus_reg_upd = array(
					'is_transferred' => 'N',
					'date_update' => date("Y-m-d H:i:s")
				);
				$this->$model->update_CustomerReg($cus_reg_upd, $cus_reg['id_customer_reg']);
			}

			// Step 3: Reset transaction transfer flag
			if(!empty($cus_reg)){
				$pay_data = array(
					'is_transferred' => 'N',
					'date_upd' => date("Y-m-d H:i:s")
				);
				$this->$model->update_transaction($pay_data, $cus_reg['id_transaction']);
			}

			// Step 4: Re-trigger the full Direct API push
			$this->insert_common_data($id_payment);

			// Step 5: Check if the receipt number and scheme account number were successfully updated
			$updated_cus_reg = $this->$model->check_receipt($id_payment, $id_scheme_account);
			$receipt_generated = !empty($updated_cus_reg['receipt_no']) && $updated_cus_reg['receipt_no'] != '-';
			$account_generated = !empty($updated_cus_reg['scheme_acc_number']) && $updated_cus_reg['scheme_acc_number'] != '-';

			if ($this->db->trans_status() === TRUE && $receipt_generated && $account_generated) {
				$this->db->trans_commit();
				$success_count++;
			} else {
				$this->db->trans_rollback();
				$fail_count++;
			}
		}

		if ($fail_count == 0) {
			$this->session->set_flashdata('chit_alert', array(
				'message' => "Successfully resynced all {$success_count} payments in the selected date range.",
				'class' => 'success',
				'title' => 'Bulk Resync'
			));
		} else {
			$this->session->set_flashdata('chit_alert', array(
				'message' => "Resync completed: {$success_count} payments succeeded, {$fail_count} failed.",
				'class' => 'warning',
				'title' => 'Bulk Resync'
			));
		}

		echo json_encode([
			'status' => true,
			'success_count' => $success_count,
			'fail_count' => $fail_count
		]);
	}

}
?>