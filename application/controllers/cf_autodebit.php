<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
ob_start();
class Cf_autodebit extends CI_Controller {
	const VIEW_FOLDER = 'cashsfree/';
	const SERV_MODEL = 'services_modal';
	public function __construct()
    {
        parent::__construct();
		ini_set('date.timezone', 'Asia/Kolkata');  
		$this->load->model('scheme_modal');
		$this->load->model('login_model');
		$this->load->model('services_modal');
		$this->load->model('sms_model');
		$this->load->model('mobileapi_model');
		$this->comp = $this->login_model->company_details();
		$this->payment_status = array(
										'pending'   => 7,
										'awaiting'  => 2,
										'success'   => 1,
										'failure'   => 3,
										'cancel'    => 4
									  );
		$this->auth_status = array(
									'INITIALIZED'	=> 1,
									'BANK_APPROVAL_PENDING' => 2,
									'ACTIVE'		=> 3,
									'ON_HOLD'		=> 4,
									'CANCELLED'    	=> 5,
									'COMPLETED'    	=> 6
								  );
		$this->log_dir = 'log/'.date("Y-m-d").'/cf_hook';
    	if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0777, TRUE); 
        }
    }
    
    /**  Cashfree Subscription Functions 	** 
	* 
	* Subscription ReturnURL REsponse DATA :
    	Array
		(
		    [cf_authAmount] => 1.00
		    [cf_message] => Subscription Activated successfully
		    [cf_orderId] => SUB_162281687260ba38682436f_AUTH_46715
		    [cf_referenceId] => 907582
		    [cf_status] => ACTIVE
		    [cf_subReferenceId] => 43225
		    [cf_subscriptionId] => 162281687260ba38682436f
		    [signature] => VfPBjwwokjls4Vh9CpQsC5vq0KWB0FCT1fAvw05PjoY=
		)		    
	    Array
		(
		    [cf_authAmount] => 1.00
		    [cf_message] => Subscription has already been authorized
		    [cf_status] => ACTIVE
		    [cf_subReferenceId] => 43225
		    [cf_subscriptionId] => 162281687260ba38682436f
		    [signature] => 3KhvbKkyIu1dwajoPULpvgoRcW6cg+2IfxTbjKtV8IM=
		)		
	*/
	
	function isMobile(){
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
		return true;
		else 
		return false;
	}
	
     public function autoDebitRURL($param1 = '', $param2 = ''){
		// Handle different URL patterns: 
		// 1. /autoDebitRURL/ID (Web)
		// 2. /autoDebitRURL/M/ID (Mobile)
		if (is_numeric($param1)) {
			$id_sch_ac = $param1;
		} else {
			$id_sch_ac = $param2;
		}

    	if (!is_dir('log/cf_subscription')) {
            mkdir('log/cf_subscription', 0777, true);
        }
        $log_path = 'log/cf_subscription/'.date("Y-m-d").'.txt';  
		$postdata = "\n".date('d-m-Y H:i:s')." \n POST : ".json_encode($_POST,true);
		file_put_contents($log_path,$postdata,FILE_APPEND | LOCK_EX);
    	if(!empty($_POST) && $_POST['cf_subscriptionId'] != '' ){ 
    		 $response	= array();
    		 $subStatus = $this->scheme_modal->getSubscriptionStatus($_POST['cf_subscriptionId']);
    		 if($subStatus <= 2){    		 	
			 	$updSubscription = array(
									"message"		=>	$_POST['cf_message'],
									"auth_status"	=>	$this->auth_status[$_POST['cf_status']],
									"status"		=>	1,
									"last_update"	=>	date('Y-m-d H:i:s')
									);
				$this->scheme_modal->updateData($updSubscription,'subscription_id',$_POST['cf_subscriptionId'],'auto_debit_subscription');
				$updSchAc = array(
								"auto_debit_status"	=>	$this->auth_status[$_POST['cf_status']],
								"date_upd"			=>	date('Y-m-d H:i:s')
								);
				$upd = $this->scheme_modal->updateData($updSchAc,'id_scheme_account',$id_sch_ac,'scheme_account');
				if($upd > 0){
					$response = array("status" => TRUE, "message" => $_POST['cf_message']); 
				}
			 }else{
			 	$response = array("status" => FALSE, "message" => 'Current Status of subscription is INITIALIZED..'.$_POST['cf_message']); 
			 }
			 if($this->isMobile()){ // Mobile browser || Inapp Browser
			 	if($_POST['cf_status'] == 'ACTIVE'){
					$url = base_url()."index.php/cf_autodebit/cf_authRedirect/success";
					$color = "green";
				}
				else if($_POST['cf_status']){
					$status_flag = ($_POST['cf_status'] == "INITIALIZED" ? 1 : ($_POST['cf_status'] == "BANK_APPROVAL_PENDING" ? 2 : 2));
					$url = base_url()."index.php/cf_autodebit/cf_authRedirect/pending/".$status_flag;
					$color = "red";
				}				
				// Proper UI: Redirect automatically for mobile
				redirect($url);
			 }
			 else{ // Web app
				if($this->session->set_userdata('CF_subscriptionId') == $_POST['cf_subscriptionId'] || $this->session->userdata('username')){
				 	$this->session->unset_userdata('CF_subscriptionId');
				 	redirect("/chitscheme/scheme_account_report/".$id_sch_ac);
				 }else{
					$data['content'] = $_POST;
					$data['content']['app'] = 'web_app';
					$pageType = array('page' => 'home','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'tollfree1'=>$this->comp['tollfree1'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
					$data['header_data'] = $pageType;
					$data['footer_data'] = $pageType;
					$data['fileName'] = self::VIEW_FOLDER.'cf_subscription';
					$this->load->view('layout/template', $data);
				 } 
			 }
		    // Write log	
			$ldata = "\n".date('d-m-Y H:i:s')." \n Result : ".json_encode($response,true);
			file_put_contents($log_path,$ldata,FILE_APPEND | LOCK_EX);
			//echo json_encode($response);
		}else{
		    // Write log	 
			$ldata = "\n".date('d-m-Y H:i:s')." \n Result : Empty Post Data ";
			file_put_contents($log_path,$ldata,FILE_APPEND | LOCK_EX);
			//echo "Empty Post Data";
		}
	}
	
	/**
	 * Subscription Authorization Page
	 * 
	 * Replaces the old cfre.in direct URL flow.
	 * The mobile app opens this URL in a WebView.
	 * This page loads the Cashfree JS SDK and auto-initiates subscriptionsCheckout().
	 * 
	 * URL: /cf_autodebit/authorize/{id_scheme_account}
	 */
	function authorize($id_scheme_account)
	{
		// Get the subscription session_id from DB
		$sub = $this->scheme_modal->get_subsDetail('sa.id_scheme_account', $id_scheme_account);
		
		if(empty($sub) || empty($sub['auth_link'])) {
			echo "<h5 style='margin-top:150px;font-size:24px;text-align:center;color:red;'>Authorization session not found or expired. Please create a new subscription.</h5>";
			return;
		}
		
		// Determine CF environment from gateway api_url
		$gateway = $this->db->select('api_url')
			->from('gateway')
			->where('id_branch', $sub['id_branch'])
			->where('pg_code', 4)
			->where('is_default', 1)
			->limit(1)
			->get()->row_array();
		
		$cf_mode = 'production'; // default to production
		if(!empty($gateway['api_url'])) {
			if(strpos($gateway['api_url'], 'sandbox') !== false || strpos($gateway['api_url'], 'test') !== false) {
				$cf_mode = 'sandbox';
			}
		}
		
		$data['session_id'] = $sub['auth_link'];
		$data['cf_mode'] = $cf_mode;
		$this->load->view('cashsfree/cf_authorize', $data);
	}
	
	function cf_authRedirect($status, $flag = ''){ 
		$app_url = $this->config->item('app_url') . "autodebit?status=" . $status;
		
		echo "<!DOCTYPE html>
		<html>
		<head>
			<meta charset='utf-8'>
			<meta name='viewport' content='width=device-width, initial-scale=1.0'>
			<title>Authorization Complete</title>
			<style>
				body { 
					font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; 
					display: flex; 
					justify-content: center; 
					align-items: center; 
					height: 100vh; 
					margin: 0; 
					background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
					color: #2c3e50;
					text-align: center;
				}
				.card {
					background: white;
					padding: 40px;
					border-radius: 20px;
					box-shadow: 0 15px 35px rgba(0,0,0,0.1);
					max-width: 90%;
					width: 400px;
				}
				.loader {
					border: 5px solid #f3f3f3;
					border-top: 5px solid #3498db;
					border-radius: 50%;
					width: 50px;
					height: 50px;
					animation: spin 1s linear infinite;
					margin: 0 auto 20px;
				}
				@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
				.status-icon {
					font-size: 60px;
					margin-bottom: 20px;
				}
				h2 { margin: 0 0 10px; font-weight: 600; }
				p { color: #7f8c8d; margin-bottom: 25px; }
				.btn {
					display: inline-block;
					padding: 12px 25px;
					background-color: #3498db;
					color: white;
					text-decoration: none;
					border-radius: 30px;
					font-weight: 600;
					transition: transform 0.2s;
				}
				.btn:active { transform: scale(0.95); }
			</style>
		</head>
		<body>
			<div class='card'>
				<div class='loader'></div>
				<h2>Processing...</h2>
				<p>Returning you to the application</p>
				<a href='" . $app_url . "' class='btn'>Click to Return</a>
			</div>
			<script type='text/javascript'>
				// Fast redirect to app
				setTimeout(function() {
					window.location.href = '" . $app_url . "';
				}, 500);
			</script>
		</body>
		</html>";
	}
	
	
	/* 
		Reference URL : https://dev.cashfree.com/subscriptions-api#webhook-support
		WEB HOOK SAMPLE RESPONSE : 
		
		* SUBSCRIPTION_STATUS_CHANGE	-> {"cf_event":"SUBSCRIPTION_STATUS_CHANGE","cf_merchantId":"37030","cf_subReferenceId":"1","cf_status":"COMPLETED","cf_lastStatus":"ACTIVE","cf_eventTime":"2021-06-09 17:14:53","signature":"TzXitELQq77QIYSVciDRnJrgHx6xMHwnqee0i3SLc20="}
		* SUBSCRIPTION_NEW_PAYMENT		-> {"cf_event":"SUBSCRIPTION_NEW_PAYMENT", "cf_merchantId":"37030", "cf_subReferenceId":"44075", "cf_paymentId":"1", "cf_amount":"1000", "cf_eventTime":"2021-06-2310:38:47", "cf_referenceId":"123", "signature":"UqqNmlTzh+gMOXWS2zm+3YCF713YKTZspZscJ4Fstow="}
		* SUBSCRIPTION_PAYMENT_DECLINED -> {"cf_event":"SUBSCRIPTION_PAYMENT_DECLINED","cf_merchantId":"37030","cf_subReferenceId":"1","cf_paymentId":"1","cf_amount":"10","cf_reasons":"Insufficient amount","cf_eventTime":"2021-06-23 15:43:27","cf_referenceId":"1234","signature":"E7\/7Wc7FMfdqBPxmyVgdbsjbmmI7P94c0u1ZO5ilgyY="}
	*/
	
	function wh_response()
    {
        // New subscriptions API (v2025-01-01, used by the mobile app) posts a JSON body with "type"
        if(empty($_POST)){
            $raw_body = file_get_contents('php://input');
            $json = json_decode($raw_body, true);
            if(is_array($json) && isset($json['type'])){
                $this->_wh_response_v2($raw_body, $json);
                return;
            }
        }
        $approval_type = $this->config->item('auto_pay_approval');
        $response = [];
    	if(!empty($_POST)){
	    	if($_POST['cf_event'] == 'SUBSCRIPTION_STATUS_CHANGE'){
				$log_path = $this->log_dir.'/'.date("Y-m-d").'_status_change.txt';
				$planDetail	= $this->scheme_modal->get_subsDetail('sub_reference_id',$_POST['cf_subReferenceId']);
				if(sizeof($planDetail) > 0){
					if($planDetail['auth_status'] == $this->auth_status[$_POST['cf_status']]){
						$response = array("status" => FALSE, "message" => "Status already updated", "time" => date('d-m-Y H:i:s')); 
					}else{
						$updSubscription = array(
											"auth_status"	=>	$this->auth_status[$_POST['cf_status']],
											"status"		=>	1,
											"last_update"	=>	date('Y-m-d H:i:s')
											);
						$this->scheme_modal->updateData($updSubscription,'sub_reference_id',$_POST['cf_subReferenceId'],'auto_debit_subscription');
						$updSchAc = array(
									"auto_debit_status"	=>	$this->auth_status[$_POST['cf_status']],
									"date_upd"			=>	date('Y-m-d H:i:s')
									);
						$upd = $this->scheme_modal->updateData($updSchAc,'id_scheme_account',$planDetail['id_scheme_account'],'scheme_account');
						if($upd > 0){
							$response = array("status" => TRUE, "message" => "Status changed to ".$_POST['cf_status'], "time" => date('d-m-Y H:i:s')); 
						}					
					}
				}else{
					$response = array("status" => FALSE, "message" => "sub_reference_id not found", "time" => date('d-m-Y H:i:s')); 
				}				
			}
			else if($_POST['cf_event'] == 'SUBSCRIPTION_NEW_PAYMENT'){ 
				$this->load->model('payment_modal');
				$log_path = $this->log_dir.'/'.date("Y-m-d").'_new_payment.txt'; 
				$planDetail	= $this->scheme_modal->get_subsDetail('sub_reference_id',$_POST['cf_subReferenceId']);
				if(sizeof($planDetail) > 0){
				    $isPaymentAlreadyExist = $this->scheme_modal->isPaymentAlreadyExist($_POST['cf_paymentId']);
					if($isPaymentAlreadyExist){ // Check whether payment already added
						$response = array("status" => FALSE, "message" => "Payment_ref_number : ".$_POST['cf_paymentId']." already exist", "time" => date('d-m-Y H:i:s')); 
					}else{
						$metal_rates = $this->payment_modal->getMetalRate($planDetail['id_branch']);
						$rate_fields = $this->payment_modal->getRateFields($planDetail['id_metal']); 
	                    $rate_field = sizeof($rate_fields) == 1 ? $rate_fields['rate_field'] : NULL;
	                    $metal_rate   = (float) ( $rate_field == null ? null : $metal_rates[$rate_field] );
	                    if($planDetail['scheme_type'] == 2 || ( $planDetail['scheme_type'] == 4 && ($planDetail['flexible_sch_type'] == 2 || $planDetail['flexible_sch_type'] == 3))){
							$weight = $_POST['cf_amount']/$metal_rate;
							// metal_wgt_decimal = 2 means only 2 decimals are allowed for metal wgt, hence bcdiv() is used to make the weight to 2 decimals and 0 is appended as last digit.
			    			$decimal = $planDetail['metal_wgt_decimal'];   
			                $round_off = $planDetail['metal_wgt_roundoff']; 
			                $metal_weight =  ($round_off == 0 ? bcdiv($weight,1,$decimal) : $weight );
						}else{
							$metal_weight = NULL;
						}
	                    // Create due month and year
						$dueType = "ND";
					    $dueData = $this->payment_modal->generateDueDate($planDetail['id_scheme_account'],$dueType);
						// set due data
						$month      = $dueData['due_month'];
						$due_year   = $dueData['due_year'];
						$insertData = array(
    									"id_scheme_account"	 => $planDetail['id_scheme_account'],
    									"payment_amount" 	 => $_POST['cf_amount'], 
                                        "payment_type" 		 => "Cash Free",
                                        "payment_mode" 		 => "Subscription",
    									"gst" 	             => 0,
    									"gst_type" 	   		 => 0,
    									"gst_amount" 	   	 => NULL,
    									"no_of_dues" 	     => 1,
    									"act_amount" 	     => $_POST['cf_amount'],
    									"actual_trans_amt"   => $_POST['cf_amount'],
    									"date_payment"   	 => date('Y-m-d H:i:s'),
    									"metal_rate"         => $metal_rate == 0 ? NULL : $metal_rate,
    									"metal_weight"       => $metal_weight,	
    									"payment_ref_number" => $_POST['cf_paymentId'],
    									"remark"             => "Payment done through cashfree subscription",
    									"added_by"			 => 4, // Cashfree Subscription
    									"add_charges"		 => 0,
    									"payment_status"     => ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']:$this->payment_status['awaiting']),
    									"id_payGateway"      => (isset($planDetail['id_pg']) ? $planDetail['id_pg']: NULL),
    									"id_branch"          => $planDetail['id_branch'],
    									"due_month"          => $month,
								        "due_year"           => $due_year,
								        "due_type"           => $dueType
    								 );	 
						$response = $this->createPayment($insertData,$planDetail);
					}
				}else{
					$response = array("status" => FALSE, "message" => "sub_reference_id not found", "time" => date('d-m-Y H:i:s')); 
				}				
			}
			else if($_POST['cf_event'] == 'SUBSCRIPTION_PAYMENT_DECLINED'){ 
				$this->load->model('payment_modal');
				$log_path = $this->log_dir.'/'.date("Y-m-d").'_pay_declined.txt';
				$planDetail	= $this->scheme_modal->get_subsDetail('sub_reference_id',$_POST['cf_subReferenceId']);
				if(sizeof($planDetail) > 0){
				    $isPaymentAlreadyExist = $this->scheme_modal->isPaymentAlreadyExist($_POST['cf_paymentId']);
					if($isPaymentAlreadyExist){ // Check whether payment already added
						$response = array("status" => FALSE, "message" => "Payment_ref_number : ".$_POST['cf_paymentId']." already exist", "time" => date('d-m-Y H:i:s')); 
					}else{
					    $metal_rate = NULL;
					    $metal_weight = NULL;
	                    // Create due month and year
						$dueType = "ND";
					    $dueData = $this->payment_modal->generateDueDate($planDetail['id_scheme_account'],$dueType);
						// set due data
						$month      = $dueData['due_month'];
						$due_year   = $dueData['due_year'];
						$insertData = array(
    									"id_scheme_account"	 => $planDetail['id_scheme_account'],
    									"payment_amount" 	 => $_POST['cf_amount'], 
                                        "payment_type" 		 => "Cash Free",
                                        "payment_mode" 		 => "Subscription",
    									"gst" 	             => 0,
    									"gst_type" 	   		 => 0,
    									"gst_amount" 	   	 => NULL,
    									"no_of_dues" 	     => 1,
    									"act_amount" 	     => $_POST['cf_amount'],
    									"actual_trans_amt"   => $_POST['cf_amount'],
    									"date_payment"   	 => date('Y-m-d H:i:s'),
    									"metal_rate"         => $metal_rate == 0 ? NULL : $metal_rate,
    									"metal_weight"       => $metal_weight,	
    									"payment_ref_number" => $_POST['cf_paymentId'],
    									"remark"             => $_POST['cf_reasons'],
    									"added_by"			 => 4, // Cashfree Subscription
    									"add_charges"		 => 0,
    									"payment_status"     => $this->payment_status['failure'],
    									"id_payGateway"      => (isset($planDetail['id_pg']) ? $planDetail['id_pg']: NULL),
    									"id_branch"          => $planDetail['id_branch'],
    									"due_month"          => $month,
								        "due_year"           => $due_year,
								        "due_type"           => $dueType
    								 );	 
						$ins = $this->scheme_modal->insertData($insertData,'payment');
						/*print_r($insertData);
						echo $this->db->last_query();
						print_r($ins);*/
						if($ins['insertID'] > 0){
						    $serviceID = 7;
                    		$service = $this->services_modal->checkService($serviceID);
                    		if($service['sms'] == 1)
                    		{
                				$data = $this->services_modal->get_SMS_data($serviceID,$ins['insertID']);
                				$mobile =$data['mobile'];
                				$message = $data['message'];
                				if($this->config->item('sms_gateway') == '1'){
            		    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
            		    		}
            		    		elseif($this->config->item('sms_gateway') == '2'){
            		    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
            		    		}
                    		}
                    		/*if($service['email'] == 1 && isset($payData[0]['email']) && $payData[0]['email'] != '')
                    		{
                    			$to = $payData[0]['email'];
                    			$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
                    			$data['payData'] = $payData[0];
                    			$data['type'] = 3;
                    			$data['company_details'] = $this->comp;
                    			$message = $this->load->view('include/emailPayment',$data,true);
                    			$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");	
                    		}*/
							$response = array("status" => TRUE, "message" => "Failed payment inserted successfully..", "time" => date('d-m-Y H:i:s')); 
						}else{
						    $response = array("status" => FALSE, "message" => "Unable to create payment", "time" => date('d-m-Y H:i:s')); 
						}					
					}
				}else{
					$response = array("status" => FALSE, "message" => "sub_reference_id not found", "time" => date('d-m-Y H:i:s')); 
				}
			}
    	}
		else{
			$log_path = $this->log_dir.'/'.date("Y-m-d").'_empty_post.txt';
			$response = array("status" => FALSE, "message" => "Empty POST Data", "time" => date('d-m-Y H:i:s')); 
		}
		
		echo json_encode($response);
    	$logData = "\n".date('d-m-Y H:i:s')." \n POST : ".json_encode($_POST,true)."\n Result : ".json_encode($response,true);
		file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
    }

	/*
		Webhook for the new subscriptions API (v2025-01-01) — subscriptions created from the mobile app.
		Same URL as wh_response; configure it in Cashfree dashboard under the Subscriptions webhook tab.
		Reference URL : https://www.cashfree.com/docs/api-reference/payments/latest/subscription/webhooks

		* SUBSCRIPTION_STATUS_CHANGED  -> {"data":{"subscription_details":{"subscription_id":"SUB_..","subscription_status":"ACTIVE",..},..},"type":"SUBSCRIPTION_STATUS_CHANGED"}
		* SUBSCRIPTION_PAYMENT_SUCCESS -> {"data":{"payment_id":"..","cf_payment_id":"49914526","subscription_id":"SUB_..","payment_type":"CHARGE","payment_amount":399.00,"payment_status":"SUCCESS",..},"type":"SUBSCRIPTION_PAYMENT_SUCCESS"}
		* SUBSCRIPTION_PAYMENT_FAILED  -> same as success with "payment_status":"FAILED","failure_details":{"failure_reason":".."}
	*/
	function _wh_response_v2($raw_body, $json)
	{
		$type = $json['type'];
		$data = isset($json['data']) ? $json['data'] : array();
		$log_path = $this->log_dir.'/'.date("Y-m-d").'_v2_'.strtolower($type).'.txt';

		// subscription_id is our SUB_<id_sch_ac>_<time>, saved as sub_reference_id by mobile_api::cf_subscription_post()
		if($type == 'SUBSCRIPTION_STATUS_CHANGED'){
			$sub_id = isset($data['subscription_details']['subscription_id']) ? $data['subscription_details']['subscription_id'] : '';
		}else{
			$sub_id = isset($data['subscription_id']) ? $data['subscription_id'] : '';
		}
		// A debit result can arrive after the subscription was closed (COMPLETED processed first, or unsubscribed
		// while the debit was running) — the money is already taken, so look up closed subscriptions too.
		// Status messages stay open-only so a late message cannot reopen a closed subscription.
		$is_debit_result = in_array($type, array('SUBSCRIPTION_PAYMENT_SUCCESS', 'SUBSCRIPTION_PAYMENT_FAILED')) && isset($data['payment_type']) && $data['payment_type'] == 'CHARGE';
		$planDetail = ($sub_id != '' ? $this->scheme_modal->get_subsDetail('sub_reference_id', $this->db->escape($sub_id), !$is_debit_result) : array());
		if(!empty($planDetail)){
			// Same Cashfree gateway as mobile_api used to create the subscription (secret is needed for the signature)
			$gw = $this->scheme_modal->get_cf_gateway($planDetail['id_scheme_account']);
			$planDetail['param_1'] = isset($gw['param_1']) ? $gw['param_1'] : '';
			$planDetail['param_3'] = isset($gw['param_3']) ? $gw['param_3'] : '';
			$planDetail['api_url'] = isset($gw['api_url']) ? $gw['api_url'] : '';
			$planDetail['id_pg']   = isset($gw['id_pg']) ? $gw['id_pg'] : NULL;
		}

		if(empty($planDetail)){
			$response = array("status" => FALSE, "message" => "subscription_id not found", "time" => date('d-m-Y H:i:s'));
		}
		else if(!$this->_cf_verify_signature($raw_body, $planDetail['param_1'])){
			$this->output->set_status_header(400);
			$response = array("status" => FALSE, "message" => "Invalid signature", "time" => date('d-m-Y H:i:s'));
		}
		else if($type == 'SUBSCRIPTION_STATUS_CHANGED'){
			$cf_status = isset($data['subscription_details']['subscription_status']) ? $data['subscription_details']['subscription_status'] : '';
			$response = $this->_cf_apply_status($planDetail, $cf_status);
		}
		else if(in_array($type, array('SUBSCRIPTION_AUTH_STATUS', 'SUBSCRIPTION_PAYMENT_SUCCESS', 'SUBSCRIPTION_PAYMENT_FAILED')) && isset($data['payment_type']) && $data['payment_type'] == 'AUTH'){
			// Rs.1 authorization result — SUCCESS enables auto pay for the plan (mobileapi_model::get_autopay_block)
			$response = $this->_cf_record_auth($planDetail, $data);
		}
		else if($type == 'SUBSCRIPTION_PAYMENT_NOTIFICATION_INITIATED' && isset($data['payment_type']) && $data['payment_type'] == 'CHARGE'){
			// Pre-debit notice: the debit happens on payment_schedule_date (~1 day later).
			$response = $this->_cf_check_upcoming_debit($planDetail, $sub_id, $data);
		}
		else if(($type == 'SUBSCRIPTION_PAYMENT_SUCCESS' || $type == 'SUBSCRIPTION_PAYMENT_FAILED') && isset($data['payment_type']) && $data['payment_type'] == 'CHARGE'){
			$response = $this->_cf_record_monthly($planDetail, $data, $type == 'SUBSCRIPTION_PAYMENT_SUCCESS');
			if($response === NULL){
				$response = $this->_cf_record_charge($planDetail, $data, $type == 'SUBSCRIPTION_PAYMENT_SUCCESS');
			}
		}
		else{
			$response = array("status" => TRUE, "message" => "Event ".$type." ignored", "time" => date('d-m-Y H:i:s'));
		}

		echo json_encode($response);
		$logData = "\n".date('d-m-Y H:i:s')." \n BODY : ".$raw_body."\n Result : ".json_encode($response,true);
		file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
	}

	/* ═══════════════════════════════════════════════════════════════
	 * Daily reconciliation with Cashfree (cron)
	 *
	 * URL : {base_url}index.php/cf_autodebit/cron_reconcile
	 *       optional ?key=... when config item 'cf_cron_key' is set
	 * CLI : php index.php cf_autodebit cron_reconcile
	 *
	 * Cashfree never resends a missed webhook. This job reads Cashfree directly and repairs anything missed:
	 *  1. Subscription status   (Active / On Hold / Cancelled / Completed / Expired) -> plan status
	 *  2. Rs.1 authorization    -> auto pay enabled flag
	 *  3. Debits (CHARGE)       -> success / failed payments not yet saved are saved (same code as webhook)
	 *  4. Upcoming debits       -> cancelled when that month is already paid by any channel (same check as pre-debit webhook)
	 * Open subscriptions get all four; subscriptions closed in the last 7 days get only step 3 (late debits).
	 * Safe to run any number of times: every step skips what is already saved.
	 * ═══════════════════════════════════════════════════════════════ */
	function cron_reconcile()
	{
		@set_time_limit(0);
		$started = microtime(true);
		$log_path = $this->log_dir.'/'.date("Y-m-d").'_cron_reconcile.txt';
		$summary = array(
			'status' => TRUE, 'started' => date('d-m-Y H:i:s'), 'subscriptions' => 0,
			'status_updated' => 0, 'auth_updated' => 0, 'payments_saved' => 0, 'debits_cancelled' => 0, 'errors' => 0, 'details' => array()
		);

		// Optional shared key so the URL cannot be triggered by others (set $config['cf_cron_key'] to enable)
		$cron_key = $this->config->item('cf_cron_key');
		if(!$this->input->is_cli_request() && !empty($cron_key) && !hash_equals((string)$cron_key, (string)$this->input->get('key'))){
			$this->output->set_status_header(403);
			echo json_encode(array('status' => FALSE, 'message' => 'Invalid key'));
			return;
		}

		// Only one run at a time (a slow run must not overlap the next one)
		$lock = $this->db->query("SELECT GET_LOCK('cf_autodebit_cron_reconcile', 0) as got")->row();
		if(empty($lock) || $lock->got != 1){
			echo json_encode(array('status' => FALSE, 'message' => 'Already running'));
			return;
		}

		try{
			$subs = $this->db->query("SELECT id_auto_debit_subscription, sub_reference_id, status
									FROM auto_debit_subscription
									WHERE sub_reference_id LIKE 'SUB\\_%' /* new API (mobile) subscriptions only — old web v1 ones are not on this API */
									  AND (status = 1 OR (status = 0 AND auth_status IN (5, 6) AND last_update >= DATE_SUB(NOW(), INTERVAL 7 DAY)))
									ORDER BY id_auto_debit_subscription")->result_array();
			$summary['subscriptions'] = count($subs);

			foreach($subs as $sub){
				$detail = array('subscription' => $sub['sub_reference_id']);
				try{
					$detail = array_merge($detail, $this->_cf_reconcile_one($sub, $summary));
				}catch(Exception $e){
					$summary['errors']++;
					$detail['error'] = $e->getMessage();
				}catch(Error $e){ // PHP 7 engine errors (e.g. bad data) — skip this subscription, continue with the rest
					$summary['errors']++;
					$detail['error'] = $e->getMessage();
				}
				$summary['details'][] = $detail;
			}

			// Completed auto debit payments without an installment number (whatever their subscription's state,
			// e.g. paid at authorization and then unsubscribed): set it and refresh the plan's paid installments
			$this->load->model('cf_autodebit_model');
			// Auto debit payments = saved by auto debit (added_by 4, also the older path without a transaction id)
			// or created through mobile_payment for an authorization / monthly debit (linked transaction id)
			$missing = $this->db->query("SELECT p.id_payment
										FROM payment p
										WHERE p.payment_status IN (1, 2) AND (p.installment IS NULL OR p.installment = '')
										  AND p.id_scheme_account IN (SELECT id_scheme_account FROM auto_debit_subscription)
										  AND (p.added_by = 4
										       OR p.ref_trans_id IN (SELECT auth_txnid FROM auto_debit_subscription WHERE auth_txnid IS NOT NULL
										                             UNION SELECT txnid FROM auto_debit_charge WHERE txnid IS NOT NULL))
										LIMIT 500")->result_array();
			$ids = array();
			foreach($missing as $m){
				$ids[] = (int)$m['id_payment'];
			}
			$this->cf_autodebit_model->sync_installment_ids($ids);
			$summary['installments_fixed'] = count($ids);
		}catch(Exception $e){
			$summary['status'] = FALSE;
			$summary['errors']++;
			$summary['message'] = $e->getMessage();
		}
		$this->db->query("SELECT RELEASE_LOCK('cf_autodebit_cron_reconcile')");

		$summary['seconds'] = round(microtime(true) - $started, 1);
		echo json_encode($summary);
		file_put_contents($log_path, "\n".date('d-m-Y H:i:s')." \n".json_encode($summary), FILE_APPEND | LOCK_EX);
	}

	/**
	 * Reconcile one subscription with Cashfree. Updates $summary counters and returns what was done.
	 */
	function _cf_reconcile_one($sub, &$summary)
	{
		$done = array();
		$is_open = ($sub['status'] == 1);
		$planDetail = $this->scheme_modal->get_subsDetail('ad.id_auto_debit_subscription', (int)$sub['id_auto_debit_subscription'], false);
		if(empty($planDetail)){
			return array('skipped' => 'scheme account not found');
		}
		$gw = $this->scheme_modal->get_cf_gateway($planDetail['id_scheme_account']);
		if(empty($gw) || trim($gw['api_url']) == ''){
			$summary['errors']++;
			return array('error' => 'gateway not configured');
		}
		$planDetail['param_1'] = $gw['param_1'];
		$planDetail['param_3'] = $gw['param_3'];
		$planDetail['api_url'] = $gw['api_url'];
		$planDetail['id_pg']   = $gw['id_pg'];
		$sub_id = $planDetail['sub_reference_id'];
		$base   = $this->_cf_base_domain($planDetail['api_url']).'/pg/subscriptions/'.rawurlencode($sub_id);

		// 1. Subscription status (open subscriptions only — a closed one is never reopened)
		if($is_open){
			$live = $this->_cf_api_call_v2($base, array(), $planDetail['param_3'], $planDetail['param_1'], 'GET');
			if(!is_array($live) || !isset($live['subscription_status'])){
				$summary['errors']++;
				return array('error' => 'status fetch failed: '.(is_array($live) && isset($live['message']) ? $live['message'] : 'no response'));
			}
			$res = $this->_cf_apply_status($planDetail, $live['subscription_status']);
			if($res['status']){
				$summary['status_updated']++;
				$done['status'] = $res['message'];
			}
		}

		// 1b. Plan closed (pre-closed / matured / closed at the showroom, by ERP sync or in billing): stop auto debit.
		//     Every closing path ends up here and in _cf_debit_not_needed(), so no closing screen needs to know
		//     about auto debit. Cancelling the mandate also cancels its pending debits at Cashfree.
		if($is_open && $this->_cf_plan_closed($planDetail['id_scheme_account'])){
			$already_ended = isset($live['subscription_status']) && in_array($live['subscription_status'], array('CANCELLED', 'CUSTOMER_CANCELLED', 'COMPLETED', 'EXPIRED'));
			$cancel = $already_ended ? array() : $this->_cf_api_call_v2($base.'/manage', array('subscription_id' => $sub_id, 'action' => 'CANCEL'), $planDetail['param_3'], $planDetail['param_1']);
			$cancelled = $already_ended || (is_array($cancel) && !isset($cancel['message']) && (isset($cancel['subscription_id']) || isset($cancel['subscription_status'])));
			if($cancelled){
				$this->scheme_modal->updateData(array('status' => 0, 'auth_status' => 5, 'message' => 'PLAN_CLOSED', 'last_update' => date('Y-m-d H:i:s')), 'id_auto_debit_subscription', $planDetail['id_auto_debit_subscription'], 'auto_debit_subscription');
				$this->scheme_modal->updateData(array('auto_debit_status' => 5, 'date_upd' => date('Y-m-d H:i:s')), 'id_scheme_account', $planDetail['id_scheme_account'], 'scheme_account');
				$summary['plans_closed'] = (isset($summary['plans_closed']) ? $summary['plans_closed'] : 0) + 1;
				$done['plan_closed'] = 'Auto debit cancelled: plan is closed';
				$is_open = false; // no debit requests / reactivation for this subscription any more
			}else{
				// Still open at Cashfree: _cf_debit_not_needed() now returns "Plan is closed", so no new debit is
				// requested and pending ones are cancelled below; the cancel is tried again on the next run.
				$summary['errors']++;
				$done['plan_closed_error'] = 'Cancel failed: '.(is_array($cancel) && isset($cancel['message']) ? $cancel['message'] : 'no response');
			}
		}

		// 2-4. Payments of the subscription
		$payments = $this->_cf_api_call_v2($base.'/payments', array(), $planDetail['param_3'], $planDetail['param_1'], 'GET');
		if(!is_array($payments) || isset($payments['message'])){
			$summary['errors']++;
			$done['error'] = 'payments fetch failed: '.(is_array($payments) && isset($payments['message']) ? $payments['message'] : 'no response');
			return $done;
		}

		// 2. Rs.1 authorization: a SUCCESS wins, otherwise the latest attempt
		if($is_open){
			$auth = null;
			foreach($payments as $pay){
				if(!is_array($pay) || !isset($pay['payment_type'], $pay['payment_status']) || $pay['payment_type'] != 'AUTH'){
					continue;
				}
				if($auth == null || $pay['payment_status'] == 'SUCCESS' || ($auth['payment_status'] != 'SUCCESS' && strtotime($pay['payment_initiated_date']) > strtotime($auth['payment_initiated_date']))){
					$auth = $pay;
				}
			}
			if($auth != null && in_array($auth['payment_status'], array('SUCCESS', 'FAILED'))){
				$res = $this->_cf_record_auth($planDetail, $auth);
				if($res['status']){
					$summary['auth_updated']++;
					$done['auth'] = $res['message'];
				}
			}
		}

		// 5. ON_HOLD whose failed month was paid manually (allowed while on hold): no Cashfree payment will
		//    succeed for that month, so ask Cashfree to resume debits from the next debit day.
		//    Needs sandbox confirmation that ACTIVATE is accepted from ON_HOLD; if refused, it is only logged —
		//    the plan stays On Hold and the customer keeps paying manually (never a double payment).
		if($is_open && isset($live['subscription_status']) && $live['subscription_status'] == 'ON_HOLD'){
			$failed = null;
			foreach($payments as $pay){
				if(is_array($pay) && isset($pay['payment_type'], $pay['payment_status']) && $pay['payment_type'] == 'CHARGE' && $pay['payment_status'] == 'FAILED'
					&& ($failed == null || strtotime($pay['payment_initiated_date']) > strtotime($failed['payment_initiated_date']))){
					$failed = $pay;
				}
			}
			$failed_on = ($failed != null ? strtotime(!empty($failed['payment_schedule_date']) ? $failed['payment_schedule_date'] : $failed['payment_initiated_date']) : 0);
			if($failed_on && $this->_cf_debit_not_needed($planDetail['id_scheme_account'], (int)date('n', $failed_on), (int)date('Y', $failed_on)) != ''){
				// ON_DEMAND subscriptions resume on the customer's own debit day; older PERIODIC ones on the 5th
				$dd = $this->db->query("SELECT debit_day FROM auto_debit_subscription WHERE id_auto_debit_subscription = ".(int)$planDetail['id_auto_debit_subscription'])->row_array();
				$next_debit = $this->_cf_next_debit_date(!empty($dd['debit_day']) ? (int)$dd['debit_day'] : 5);
				$act = $this->_cf_api_call_v2($base.'/manage',
					array('subscription_id' => $sub_id, 'action' => 'ACTIVATE', 'action_details' => array('next_scheduled_time' => $next_debit.'T10:00:00+05:30')),
					$planDetail['param_3'], $planDetail['param_1']);
				if(is_array($act) && isset($act['subscription_status']) && !isset($act['message'])){
					$this->_cf_apply_status($planDetail, $act['subscription_status']);
					$summary['reactivated'] = (isset($summary['reactivated']) ? $summary['reactivated'] : 0) + 1;
					$done['reactivated'] = 'Resumed from '.date('d-m-Y', strtotime($next_debit)).' ('.date('M-Y', $failed_on).' paid manually)';
				}else{
					$summary['errors']++;
					$done['reactivate_error'] = is_array($act) && isset($act['message']) ? $act['message'] : 'no response';
				}
			}
		}

		foreach($payments as $pay){
			if(!is_array($pay) || !isset($pay['payment_type'], $pay['payment_status']) || $pay['payment_type'] != 'CHARGE' || empty($pay['cf_payment_id'])){
				continue;
			}
			// 3. Debit results not saved yet (duplicates are skipped inside _cf_record_charge)
			if($pay['payment_status'] == 'SUCCESS' || $pay['payment_status'] == 'FAILED'){
				$res = $this->_cf_record_monthly($planDetail, $pay, $pay['payment_status'] == 'SUCCESS');
				if($res === NULL){
					$res = $this->_cf_record_charge($planDetail, $pay, $pay['payment_status'] == 'SUCCESS');
				}
				if(!empty($res['status'])){
					$summary['payments_saved']++;
					$done['payments'][] = $pay['cf_payment_id'].' '.$pay['payment_status'];
				}
			}
			// 4. Upcoming debit (open subscriptions): cancel when that month is already paid
			else if($is_open && in_array($pay['payment_status'], array('INITIALIZED', 'PENDING')) && !empty($pay['payment_schedule_date']) && strtotime($pay['payment_schedule_date']) > time()){
				$res = $this->_cf_check_upcoming_debit($planDetail, $sub_id, $pay);
				if(!empty($res['cancelled'])){
					$summary['debits_cancelled']++;
				}
				if(!empty($res['cancelled']) || !$res['status']){
					$done['upcoming'][] = $res['message'];
				}
			}
		}

		// 6. ON_DEMAND subscriptions: request this month's / next month's installment from Cashfree a few days
		//    before the customer's debit day (Cashfree debits nothing by itself for ON_DEMAND).
		if($is_open && isset($live['subscription_status']) && $live['subscription_status'] == 'ACTIVE'){
			$row = $this->db->query("SELECT id_auto_debit_subscription, mandate_type, debit_day, installment_amount, first_charge_on, expires_on, auth_payment_status
									FROM auto_debit_subscription WHERE id_auto_debit_subscription = ".(int)$planDetail['id_auto_debit_subscription'])->row_array();
			if(!empty($row) && $row['mandate_type'] == 'ON_DEMAND' && $row['auth_payment_status'] == 'SUCCESS' && $row['debit_day'] > 0 && $row['installment_amount'] > 0){
				foreach(self::cf_due_debits(date('Y-m-d'), (int)$row['debit_day'], $row['first_charge_on'], $row['expires_on']) as $target){
					$msg = $this->_cf_raise_debit($planDetail, $row, $target, $payments, $summary);
					if($msg != ''){
						$done['requested'][] = $msg;
					}
				}
			}
		}
		return $done;
	}

	/**
	 * ON_DEMAND schedule rules (pure — $today is a parameter so they can be tested for any date).
	 * Returns the installment months whose debit should be requested today:
	 *  - debit date = debit_day of this month and of next month, not before first_charge_on, not after expiry
	 *  - requested 2-4 days before the debit date (Cashfree: T+2 .. T+14 allowed for every method; UPI needs
	 *    a 25 hr pre-debit notice). The job runs daily, so a month gets 3 chances.
	 *  - missed (job did not run): requested for today + 2 days, up to 15 days after the debit date.
	 */
	public static function cf_due_debits($today, $debit_day, $first_charge_on, $expires_on)
	{
		$out = array();
		$t = strtotime($today);
		for($i = 0; $i <= 1; $i++){
			$m = strtotime(date('Y-m-01', $t).' +'.$i.' month');
			$d = strtotime(date('Y-m-', $m).sprintf('%02d', $debit_day));
			if(!empty($first_charge_on) && $d < strtotime(date('Y-m-d', strtotime($first_charge_on)))){
				continue;
			}
			if(!empty($expires_on) && $d > strtotime(date('Y-m-d', strtotime($expires_on)))){
				continue;
			}
			$lead = (int)round(($d - $t) / 86400);
			if($lead > 4 || $lead < -15){
				continue;
			}
			$out[] = array(
				'month'    => (int)date('n', $m),
				'year'     => (int)date('Y', $m),
				'schedule' => ($lead >= 2 ? date('Y-m-d', $d) : date('Y-m-d', strtotime($today.' +2 days')))
			);
		}
		return $out;
	}

	/**
	 * Request one installment month's debit from Cashfree (ON_DEMAND). Never twice for the same month:
	 * the auto_debit_charge row (UNIQUE subscription + month) is written BEFORE calling Cashfree, and the
	 * payment_id is fixed per month, so a retry after a timeout is rejected by Cashfree as a duplicate.
	 * Returns a message for the job summary ('' when nothing was done).
	 */
	function _cf_raise_debit($planDetail, $row, $target, $payments, &$summary)
	{
		$id_sub = (int)$row['id_auto_debit_subscription'];
		$label  = date('M-Y', mktime(0, 0, 0, $target['month'], 1, $target['year']));

		$payment_id = 'CHG_'.$id_sub.'_'.sprintf('%04d%02d', $target['year'], $target['month']);
		$existing = $this->db->query("SELECT status, txnid FROM auto_debit_charge WHERE id_auto_debit_subscription = ".$id_sub." AND due_year = ".(int)$target['year']." AND due_month = ".(int)$target['month'])->row_array();

		// Already paid through any channel, or all installments done -> nothing to request
		// (a pending payment made for an earlier, not-yet-sent request is closed)
		if($this->_cf_debit_not_needed($planDetail['id_scheme_account'], $target['month'], $target['year']) != ''){
			if(!empty($existing['txnid']) && $existing['status'] != 'REQUESTED'){
				$this->load->model('cf_autodebit_model');
				$this->cf_autodebit_model->cancel_auth_txn($existing['txnid'], 'month already paid');
			}
			return '';
		}

		// REQUESTED: waiting for Cashfree / PAID / DEBIT_FAILED: result known (a failed month is retried by the customer)
		if(!empty($existing) && in_array($existing['status'], array('REQUESTED', 'SKIPPED', 'PAID', 'DEBIT_FAILED'))){
			return '';
		}
		if(empty($existing)){
			$this->db->query("INSERT IGNORE INTO auto_debit_charge (id_auto_debit_subscription, id_scheme_account, due_month, due_year, payment_id, payment_amount, schedule_date, status, created_on)
							VALUES (".$id_sub.", ".(int)$planDetail['id_scheme_account'].", ".(int)$target['month'].", ".(int)$target['year'].", ".$this->db->escape($payment_id).", ".(float)$row['installment_amount'].", ".$this->db->escape($target['schedule']).", 'REQUESTING', NOW())");
			if($this->db->affected_rows() < 1){
				return ''; // another run inserted it at the same moment
			}
		}

		// This month's PENDING payment, created the same way as a manual (mobile_payment) payment, before Cashfree
		// is asked. Created once per month and reused if the request has to be tried again.
		if(empty($existing['txnid'])){
			$this->load->model('cf_autodebit_model');
			$txn = $this->cf_autodebit_model->create_installment_txn($planDetail['id_scheme_account'], (float)$row['installment_amount'], $target['month'], $target['year'], $target['schedule'], $planDetail['id_pg']);
			if(!$txn['status']){
				$this->db->query("UPDATE auto_debit_charge SET status = 'FAILED_REQUEST', message = ".$this->db->escape('Payment not created: '.$txn['message']).", last_update = NOW() WHERE payment_id = ".$this->db->escape($payment_id));
				$summary['errors']++;
				return $label.' debit request FAILED: '.$txn['message'];
			}
			$this->db->query("UPDATE auto_debit_charge SET txnid = ".$this->db->escape($txn['txnid']).", last_update = NOW() WHERE payment_id = ".$this->db->escape($payment_id));
		}

		// A previous run may have reached Cashfree and then timed out: the payment is already there
		foreach($payments as $pay){
			if(is_array($pay) && isset($pay['payment_id']) && $pay['payment_id'] == $payment_id){
				$this->db->query("UPDATE auto_debit_charge SET status = 'REQUESTED', message = 'found at Cashfree', last_update = NOW() WHERE payment_id = ".$this->db->escape($payment_id));
				return '';
			}
		}

		$res = $this->_cf_api_call_v2($this->_cf_base_domain($planDetail['api_url']).'/pg/subscriptions/pay', array(
				'subscription_id'       => $planDetail['sub_reference_id'],
				'payment_id'            => $payment_id,
				'payment_amount'        => (float)$row['installment_amount'],
				'payment_schedule_date' => $target['schedule'].'T10:00:00+05:30',
				'payment_remarks'       => 'Installment '.$label,
				'payment_type'          => 'CHARGE'
			), $planDetail['param_3'], $planDetail['param_1']);
		$ok  = is_array($res) && (isset($res['cf_payment_id']) || (isset($res['payment_id']) && !isset($res['message'])));
		$dup = !$ok && is_array($res) && isset($res['message']) && preg_match('/already|duplicate|exist/i', $res['message']);
		$status = ($ok || $dup) ? 'REQUESTED' : 'FAILED_REQUEST'; // FAILED_REQUEST is tried again on the next run
		$message = $ok ? 'requested' : (is_array($res) && isset($res['message']) ? $res['message'] : 'no response');
		$this->db->query("UPDATE auto_debit_charge SET status = '".$status."', message = ".$this->db->escape(substr($message, 0, 250)).", last_update = NOW() WHERE payment_id = ".$this->db->escape($payment_id));

		if($status == 'REQUESTED'){
			$summary['debits_requested'] = (isset($summary['debits_requested']) ? $summary['debits_requested'] : 0) + 1;
			return $label.' debit of Rs.'.(0 + $row['installment_amount']).' requested for '.date('d-m-Y', strtotime($target['schedule']));
		}
		$summary['errors']++;
		return $label.' debit request FAILED: '.$message;
	}

	/**
	 * Next monthly debit date (Y-m-d): the next debit day (5th — keep same as mobile_api::CF_DEBIT_DAY)
	 * that is at least 2 days away (Cashfree T+1/T+2 + 25 hr pre-debit notice).
	 */
	function _cf_next_debit_date($debit_day = 5)
	{
		$earliest = strtotime(date('Y-m-d').' +2 days');
		$month = strtotime(date('Y-m-01'));
		for($i = 0; $i < 3; $i++){
			$m = strtotime(date('Y-m-01', $month).' +'.$i.' month');
			$d = strtotime(date('Y-m-', $m).sprintf('%02d', min($debit_day, (int)date('t', $m))));
			if($d >= $earliest){
				return date('Y-m-d', $d);
			}
		}
		return date('Y-m-d', $earliest);
	}

	/**
	 * Apply a Cashfree subscription_status to the plan. Unknown statuses are reported, not saved.
	 */
	function _cf_apply_status($planDetail, $cf_status)
	{
		// Cancelled from any side, and never-authorized subscriptions past expiry, all mean "not subscribed any more"
		$status_map = array_merge($this->auth_status, array('CUSTOMER_CANCELLED' => 5, 'EXPIRED' => 5, 'LINK_EXPIRED' => 5));
		if(!isset($status_map[$cf_status])){
			return array("status" => FALSE, "message" => "Status ".$cf_status." not mapped", "time" => date('d-m-Y H:i:s'));
		}
		if($planDetail['auth_status'] == $status_map[$cf_status]){
			return array("status" => FALSE, "message" => "Status already updated", "time" => date('d-m-Y H:i:s'));
		}
		$updSubscription = array(
							"auth_status"	=>	$status_map[$cf_status],
							"message"		=>	$cf_status,
							// Cancelled / Completed subscriptions are closed so the customer can subscribe again
							"status"		=>	($status_map[$cf_status] == 5 || $status_map[$cf_status] == 6 ? 0 : 1),
							"last_update"	=>	date('Y-m-d H:i:s')
							);
		$this->scheme_modal->updateData($updSubscription,'id_auto_debit_subscription',$planDetail['id_auto_debit_subscription'],'auto_debit_subscription');
		$updSchAc = array(
					"auto_debit_status"	=>	$status_map[$cf_status],
					"date_upd"			=>	date('Y-m-d H:i:s')
					);
		$upd = $this->scheme_modal->updateData($updSchAc,'id_scheme_account',$planDetail['id_scheme_account'],'scheme_account');
		return array("status" => ($upd > 0), "message" => "Status changed to ".$cf_status, "time" => date('d-m-Y H:i:s'));
	}

	/**
	 * Save the Rs.1 AUTH payment result. SUCCESS is final — a later failed attempt must not switch auto pay off.
	 */
	function _cf_record_auth($planDetail, $pay)
	{
		$auth_status   = isset($pay['payment_status']) ? $pay['payment_status'] : '';
		$cf_payment_id = isset($pay['cf_payment_id']) ? $pay['cf_payment_id'] : NULL;
		$cur = $this->db->query("SELECT auth_payment_status, auth_is_installment, auth_txnid FROM auto_debit_subscription WHERE id_auto_debit_subscription=".(int)$planDetail['id_auto_debit_subscription'])->row_array();

		// ON_DEMAND subscriptions created while this month was unpaid: the authorization payment is this month's
		// installment. The app created it through mobile_api/mobile_payment (pending rows, ref_trans_id = auth_txnid):
		// complete those like a manual payment. Runs even if the auth status was already saved (the app's status check
		// can get there first); nothing is completed twice. Without auth_txnid (older app) it is saved directly.
		$installment = array();
		if($auth_status == 'SUCCESS' && !empty($cur['auth_is_installment'])){
			if(!empty($cur['auth_txnid'])){
				$this->load->model('cf_autodebit_model');
				$installment = $this->cf_autodebit_model->complete_auth_txn($cur['auth_txnid'], $cf_payment_id, $this->cf_autodebit_model->cf_payment_mode($pay));
			}
			else if(!empty($cf_payment_id) && isset($pay['payment_amount']) && $pay['payment_amount'] > 1){
				$installment = $this->_cf_record_charge($planDetail, $pay, TRUE);
			}
		}

		if(isset($cur['auth_payment_status']) && $cur['auth_payment_status'] == 'SUCCESS'){
			return array("status" => !empty($installment['status']), "message" => "Authorization already successful".(!empty($installment['message']) ? "; installment: ".$installment['message'] : ""), "time" => date('d-m-Y H:i:s'));
		}
		if(isset($cur['auth_payment_status']) && $cur['auth_payment_status'] == $auth_status){
			return array("status" => FALSE, "message" => "Authorization already ".$auth_status, "time" => date('d-m-Y H:i:s'));
		}
		$updAuth = array(
					"auth_payment_status" => $auth_status,
					"auth_payment_id"     => $cf_payment_id,
					"last_update"         => date('Y-m-d H:i:s')
					);
		if($auth_status == 'SUCCESS'){
			$updAuth['auth_paid_on'] = date('Y-m-d H:i:s');
		}
		$this->scheme_modal->updateData($updAuth,'id_auto_debit_subscription',$planDetail['id_auto_debit_subscription'],'auto_debit_subscription');
		return array("status" => TRUE, "message" => "Authorization ".$auth_status.(!empty($installment['message']) ? "; installment: ".$installment['message'] : ""), "time" => date('d-m-Y H:i:s'));
	}

	/**
	 * Upcoming debit check: cancel this one debit when its installment month is already paid through any
	 * channel (app, counter, collection agent, ERP sync, advance payment) or all installments are done.
	 */
	function _cf_check_upcoming_debit($planDetail, $sub_id, $pay)
	{
		$sched  = !empty($pay['payment_schedule_date']) ? strtotime($pay['payment_schedule_date']) : time();
		$reason = $this->_cf_debit_not_needed($planDetail['id_scheme_account'], (int)date('n', $sched), (int)date('Y', $sched));
		if($reason == ''){
			return array("status" => TRUE, "cancelled" => FALSE, "message" => "Debit on ".date('d-m-Y', $sched)." allowed", "time" => date('d-m-Y H:i:s'));
		}
		$cancel = $this->_cf_api_call_v2(
			$this->_cf_base_domain($planDetail['api_url']).'/pg/subscriptions/'.rawurlencode($sub_id).'/payments/'.rawurlencode($pay['payment_id']).'/manage',
			array('subscription_id' => $sub_id, 'payment_id' => $pay['payment_id'], 'action' => 'CANCEL'),
			$planDetail['param_3'], $planDetail['param_1']
		);
		$cancelled = is_array($cancel) && ((isset($cancel['payment_status']) && strtoupper($cancel['payment_status']) == 'CANCELLED') || (isset($cancel['payment_id']) && !isset($cancel['message'])));
		if($cancelled && !empty($pay['payment_id'])){
			// The month's pending payment (made when the debit was requested) is closed too
			$chg = $this->db->query("SELECT txnid FROM auto_debit_charge WHERE payment_id = ".$this->db->escape($pay['payment_id']))->row_array();
			if(!empty($chg['txnid'])){
				$this->load->model('cf_autodebit_model');
				$this->cf_autodebit_model->cancel_auth_txn($chg['txnid'], $reason);
				$this->db->query("UPDATE auto_debit_charge SET status = 'SKIPPED', message = ".$this->db->escape(substr($reason, 0, 250)).", last_update = NOW() WHERE payment_id = ".$this->db->escape($pay['payment_id']));
			}
		}
		return array("status" => $cancelled, "cancelled" => $cancelled, "message" => ($cancelled ? "Debit on ".date('d-m-Y', $sched)." cancelled: " : "Debit cancel FAILED: ").$reason, "cf_response" => $cancel, "time" => date('d-m-Y H:i:s'));
	}

	/**
	 * Monthly debit result for a month our daily job requested: update that month's pending payment
	 * (created like a manual payment) — success: completed like a manual payment; failed: marked failed;
	 * success after the plan was closed: kept for refund. Returns NULL when the debit has no such payment
	 * (older PERIODIC subscriptions) so the caller falls back to _cf_record_charge().
	 */
	function _cf_record_monthly($planDetail, $data, $is_success)
	{
		if(empty($data['payment_id'])){
			return NULL;
		}
		$chg = $this->db->query("SELECT txnid FROM auto_debit_charge WHERE payment_id = ".$this->db->escape($data['payment_id']))->row_array();
		if(empty($chg['txnid'])){
			return NULL;
		}
		$this->load->model('cf_autodebit_model');
		$cf_payment_id = isset($data['cf_payment_id']) ? $data['cf_payment_id'] : NULL;
		if($is_success){
			if($this->_cf_plan_closed($planDetail['id_scheme_account'])){
				$res = $this->cf_autodebit_model->refund_installment_txn($chg['txnid'], $cf_payment_id);
			}else{
				$res = $this->cf_autodebit_model->complete_auth_txn($chg['txnid'], $cf_payment_id, $this->cf_autodebit_model->cf_payment_mode($data),
						'Payment done through cashfree subscription', array('date_payment' => date('Y-m-d H:i:s')));
			}
			$charge_status = 'PAID';
		}else{
			$reason = isset($data['failure_details']['failure_reason']) ? $data['failure_details']['failure_reason'] : 'Auto debit failed';
			$res = $this->cf_autodebit_model->fail_installment_txn($chg['txnid'], $reason);
			$charge_status = 'DEBIT_FAILED';
		}
		// A late FAILED never overwrites PAID (the payment row itself is protected the same way)
		$this->db->query("UPDATE auto_debit_charge SET status = '".$charge_status."', message = ".$this->db->escape(substr($res['message'], 0, 250)).", last_update = NOW()
						WHERE payment_id = ".$this->db->escape($data['payment_id']).($is_success ? "" : " AND status != 'PAID'"));
		return array("status" => $res['status'], "message" => $res['message'], "time" => date('d-m-Y H:i:s'));
	}

	/**
	 * Save one Cashfree CHARGE result (success or failed) as a payment of the plan. Shared by the webhook
	 * and the daily reconciliation, so both save exactly the same way. Duplicates (same cf_payment_id) are skipped.
	 */
	function _cf_record_charge($planDetail, $data, $is_success)
	{
		$response = array();
		// AUTH payments (Re 1 mandate authorization) are not installments, only CHARGE payments are recorded
		$this->load->model('payment_modal');
		// $is_success: SUBSCRIPTION_PAYMENT_SUCCESS (true) / SUBSCRIPTION_PAYMENT_FAILED (false)
		$ref_no     = $data['cf_payment_id'];
		$amount     = $data['payment_amount'];
		if($is_success){
			// A retried charge can carry the same cf_payment_id as its failed attempt, so only a non-failed row is a duplicate
			$isPaymentAlreadyExist = $this->db->where('payment_ref_number', $ref_no)->where('payment_status !=', $this->payment_status['failure'])->count_all_results('payment') > 0;
		}else{
			$isPaymentAlreadyExist = $this->scheme_modal->isPaymentAlreadyExist($ref_no);
		}
		if($isPaymentAlreadyExist){
			$response = array("status" => FALSE, "message" => "Payment_ref_number : ".$ref_no." already exist", "time" => date('d-m-Y H:i:s'));
		}else{
			$metal_rate   = NULL;
			$metal_weight = NULL;
			if($is_success){
				$metal_rates = $this->payment_modal->getMetalRate($planDetail['id_branch']);
				$rate_fields = $this->payment_modal->getRateFields($planDetail['id_metal']);
				$rate_field  = sizeof($rate_fields) == 1 ? $rate_fields['rate_field'] : NULL;
				$metal_rate  = (float) ( $rate_field == null ? null : $metal_rates[$rate_field] );
				if($metal_rate > 0 && ($planDetail['scheme_type'] == 2 || ( $planDetail['scheme_type'] == 4 && ($planDetail['flexible_sch_type'] == 2 || $planDetail['flexible_sch_type'] == 3)))){
					$weight = $amount/$metal_rate;
					$decimal = $planDetail['metal_wgt_decimal'];
					$round_off = $planDetail['metal_wgt_roundoff'];
					$metal_weight =  ($round_off == 0 ? bcdiv($weight,1,$decimal) : $weight );
				}
			}
			// Create due month and year
			$dueType = "ND";
			$dueData = $this->payment_modal->generateDueDate($planDetail['id_scheme_account'],$dueType);
			$insertData = array(
							"id_scheme_account"	 => $planDetail['id_scheme_account'],
							"payment_amount" 	 => $amount,
							"payment_type" 		 => "Cash Free",
							"payment_mode" 		 => "Subscription",
							"gst" 	             => 0,
							"gst_type" 	   		 => 0,
							"gst_amount" 	   	 => NULL,
							"no_of_dues" 	     => 1,
							"act_amount" 	     => $amount,
							"actual_trans_amt"   => $amount,
							"date_payment"   	 => date('Y-m-d H:i:s'),
							"metal_rate"         => $metal_rate == 0 ? NULL : $metal_rate,
							"metal_weight"       => $metal_weight,
							"payment_ref_number" => $ref_no,
							"remark"             => ($is_success ? "Payment done through cashfree subscription" : (isset($data['failure_details']['failure_reason']) ? $data['failure_details']['failure_reason'] : 'Payment failed')),
							"added_by"			 => 4, // Cashfree Subscription
							"add_charges"		 => 0,
							"payment_status"     => ($is_success ? ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']:$this->payment_status['awaiting']) : $this->payment_status['failure']),
							"id_payGateway"      => (isset($planDetail['id_pg']) ? $planDetail['id_pg']: NULL),
							"id_branch"          => $planDetail['id_branch'],
							"due_month"          => $dueData['due_month'],
							"due_year"           => $dueData['due_year'],
							"due_type"           => $dueType
						 );
			if($is_success && $this->_cf_plan_closed($planDetail['id_scheme_account'])){
				// Money taken after the plan was closed (debit was already on its way): keep it visible for staff
				// to verify and refund — awaiting approval, no receipt / account number / SMS.
				$insertData['payment_status'] = $this->payment_status['awaiting'];
				$insertData['remark'] = 'Auto debit received after the plan was closed - verify and refund';
				$ins = $this->scheme_modal->insertData($insertData,'payment');
				$response = array("status" => ($ins['insertID'] > 0), "message" => ($ins['insertID'] > 0 ? "Payment for closed plan saved as awaiting (refund)" : "Unable to create payment"), "time" => date('d-m-Y H:i:s'));
			}else if($is_success){
				$response = $this->createPayment($insertData,$planDetail);
				// Installment number + plan's paid installment count (same as the other payment completions)
				$saved = $this->db->query("SELECT id_payment FROM payment WHERE payment_ref_number = ".$this->db->escape($ref_no)." AND id_scheme_account = ".(int)$planDetail['id_scheme_account']." AND payment_status IN (1, 2)")->result_array();
				if(!empty($saved)){
					$this->load->model('cf_autodebit_model');
					$this->cf_autodebit_model->sync_installment_ids(array_map(function ($r) { return (int)$r['id_payment']; }, $saved));
				}
			}else{
				$ins = $this->scheme_modal->insertData($insertData,'payment');
				if($ins['insertID'] > 0){
					$serviceID = 7;
					$service = $this->services_modal->checkService($serviceID);
					if($service['sms'] == 1)
					{
						$smsData = $this->services_modal->get_SMS_data($serviceID,$ins['insertID']);
						if($this->config->item('sms_gateway') == '1'){
							$this->sms_model->sendSMS_MSG91($smsData['mobile'],$smsData['message'],'',$service['dlt_te_id']);
						}
						elseif($this->config->item('sms_gateway') == '2'){
							$this->sms_model->sendSMS_Nettyfish($smsData['mobile'],$smsData['message'],'trans');
						}
					}
					$response = array("status" => TRUE, "message" => "Failed payment inserted successfully..", "time" => date('d-m-Y H:i:s'));
				}else{
					$response = array("status" => FALSE, "message" => "Unable to create payment", "time" => date('d-m-Y H:i:s'));
				}
			}
		}

		return $response;
	}

	/**
	 * Reason why the auto debit of installment month $month/$year is not needed ('' = debit is needed).
	 * Counts payments from every channel (app, counter, collection agent, ERP sync) — success or awaiting approval.
	 * Installment month = due_month/due_year (advance payments carry the future month); date_payment when not set.
	 */
	function _cf_debit_not_needed($id_scheme_account, $month, $year)
	{
		if($this->_cf_plan_closed($id_scheme_account)){
			return 'Plan is closed';
		}
		$plan = $this->scheme_modal->get_plan_detail((int)$id_scheme_account);
		if(!empty($plan) && $plan['total_installments'] > 0 && $plan['paid_installments'] >= $plan['total_installments']){
			return 'All installments are already paid';
		}
		$paid = $this->db->query("SELECT COUNT(id_payment) as cnt FROM payment
								WHERE id_scheme_account = ".(int)$id_scheme_account." AND payment_status IN (1, 2)
								AND ( (due_month = ".(int)$month." AND due_year = ".(int)$year.")
									OR ((due_month IS NULL OR due_month = '' OR due_month = 0) AND MONTH(date_payment) = ".(int)$month." AND YEAR(date_payment) = ".(int)$year.") )")->row()->cnt;
		if($paid > 0){
			return 'Installment for '.date('M-Y', mktime(0, 0, 0, $month, 1, $year)).' is already paid';
		}
		return '';
	}

	/**
	 * Is the scheme account closed (pre-closed / matured / closed) or no longer active? Auto debit must stop.
	 */
	function _cf_plan_closed($id_scheme_account)
	{
		$sa = $this->db->query("SELECT is_closed, active FROM scheme_account WHERE id_scheme_account = ".(int)$id_scheme_account)->row_array();
		return !empty($sa) && ((string)$sa['is_closed'] === '1' || (isset($sa['active']) && (string)$sa['active'] === '0'));
	}

	// https://api.cashfree.com from a gateway api_url such as https://api.cashfree.com/pg/orders/ (scheme added if missing)
	function _cf_base_domain($api_url)
	{
		$api_url = trim($api_url);
		if(!preg_match('#^https?://#i', $api_url)){
			$api_url = 'https://'.$api_url;
		}
		$parsed_url = parse_url($api_url);
		return $parsed_url['scheme'].'://'.$parsed_url['host'];
	}

	// Cashfree subscriptions API (same headers as mobile_api::_cf_api_call)
	function _cf_api_call_v2($url, $payload, $clientId, $secretKey, $method = 'POST')
	{
		$curl = curl_init();
		$options = array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_CUSTOMREQUEST  => $method,
			CURLOPT_HTTPHEADER     => array(
				'Content-Type: application/json',
				'x-client-id: '.$clientId,
				'x-client-secret: '.$secretKey,
				'x-api-version: 2025-01-01'
			)
		);
		if($method == 'POST' && !empty($payload)){
			$options[CURLOPT_POSTFIELDS] = json_encode($payload);
		}
		curl_setopt_array($curl, $options);
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);
		if($err){
			return array('status' => false, 'message' => 'cURL Error: '.$err);
		}
		return json_decode($response, true);
	}

	// Cashfree webhook signature: base64( HMAC-SHA256( x-webhook-timestamp + raw body, secret key ) )
	function _cf_verify_signature($raw_body, $secret_key)
	{
		$timestamp = isset($_SERVER['HTTP_X_WEBHOOK_TIMESTAMP']) ? $_SERVER['HTTP_X_WEBHOOK_TIMESTAMP'] : '';
		$signature = isset($_SERVER['HTTP_X_WEBHOOK_SIGNATURE']) ? $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'] : '';
		if($timestamp == '' || $signature == '' || $secret_key == ''){
			return false;
		}
		$computed = base64_encode(hash_hmac('sha256', $timestamp.$raw_body, $secret_key, true));
		return hash_equals($computed, $signature);
	}

    function cf_retry($type,$id_sch_ac)
    {
        $approval_type = $this->config->item('auto_pay_approval');
        $response = [];
    	if($id_sch_ac > 0){ 
    	    if(!$this->session->userdata('username') && $type == 1)
    		{
    			redirect("/user/login");
    		}
    		else{
    	        $planDetail	= $this->scheme_modal->get_subsDetail('sa.id_scheme_account',$id_sch_ac);
    			$post_data	= array("subReferenceId" => $planDetail['sub_reference_id']);
    			$res 		= $this->cf_curl($planDetail['sub_reference_id'].'/charge-retry',$post_data,$planDetail);
                if($res['status'] == FALSE) {
                    $this->session->set_flashdata('errMsg','Error in processing request...');
                    redirect("/chitscheme/scheme_report");
                } 
                else{ 
                	$result = $res['result'];
                    //echo "<pre>";print_r($res);exit;
                    if($result->status == "OK"){ // OK - Api call success  - ERROR Failed
                        /*{
                        	"status": "OK",
                        	"subStatus": "ACTIVE",
                        	"payment": 
                        	{
                        		"paymentId": 123456,
                        		"amount": 2,
                        		"status": "SUCCESS",
                        		"addedOn": "2021-02-26 13:35:12",
                        		"retryAttempts": 1
                        	}
                        }*/
                    	$updSubscription = array(
    											"auth_status"		=>	$this->auth_status[$result->subStatus],
    											"last_update"		=>	date('Y-m-d H:i:s')
    											);
    					$upd = $this->scheme_modal->updateData($updSubscription,'id_auto_debit_subscription',$planDetail['id_auto_debit_subscription'],'auto_debit_subscription');
    					$updSchAc = array(
    									"auto_debit_status"	=>	$this->auth_status[$result->subStatus],
    									"date_upd"			=>	date('Y-m-d H:i:s')
    									);
    					$this->scheme_modal->updateData($updSchAc,'id_scheme_account',$id_sch_ac,'scheme_account');
    					
    					// Payment
    				    $isPaymentAlreadyExist = $this->scheme_modal->isPaymentAlreadyExist($result->payment->paymentId);
    					if($isPaymentAlreadyExist){ // Check whether payment already added
    						$response = array("status" => FALSE, "message" => "Payment_ref_number : ".$result->payment->paymentId." already exist", "time" => date('d-m-Y H:i:s')); 
    					}else{
    						$metal_rates = $this->payment_modal->getMetalRate($planDetail['id_branch']);
    						$rate_fields = $this->payment_modal->getRateFields($planDetail['id_metal']); 
    	                    $rate_field = sizeof($rate_fields) == 1 ? $rate_fields['rate_field'] : NULL;
    	                    $metal_rate   = (float) ( $rate_field == null ? null : $metal_rates[$rate_field] );
    	                    if($planDetail['scheme_type'] == 2 || ( $planDetail['scheme_type'] == 4 && ($planDetail['flexible_sch_type'] == 2 || $planDetail['flexible_sch_type'] == 3))){
    							$weight = $result->payment->amount/$metal_rate;
    							// metal_wgt_decimal = 2 means only 2 decimals are allowed for metal wgt, hence bcdiv() is used to make the weight to 2 decimals and 0 is appended as last digit.
    			    			$decimal = $planDetail['metal_wgt_decimal'];   
    			                $round_off = $planDetail['metal_wgt_roundoff']; 
    			                $metal_weight =  ($round_off == 0 ? bcdiv($weight,1,$decimal) : $weight );
    						}else{
    							$metal_weight = NULL;
    						}
    	                    // Create due month and year
    						$dueType = "ND";
    					    $dueData = $this->payment_modal->generateDueDate($planDetail['id_scheme_account'],$dueType);
    						// set due data
    						$month      = $dueData['due_month'];
    						$due_year   = $dueData['due_year'];
    						$insertData = array(
        									"id_scheme_account"	 => $planDetail['id_scheme_account'],
        									"payment_amount" 	 => $result->payment->amount, 
                                            "payment_type" 		 => "Cash Free",
                                            "payment_mode" 		 => "Subscription",
        									"gst" 	             => 0,
        									"gst_type" 	   		 => 0,
        									"gst_amount" 	   	 => NULL,
        									"no_of_dues" 	     => 1,
        									"act_amount" 	     => $result->payment->amount,
        									"actual_trans_amt"   => $result->payment->amount,
        									"date_payment"   	 => date('Y-m-d H:i:s'),
        									"metal_rate"         => $metal_rate == 0 ? NULL : $metal_rate,
        									"metal_weight"       => $metal_weight,	
        									"payment_ref_number" => $result->payment->paymentId,
        									"remark"             => "Payment done through cashfree subscription",
        									"added_by"			 => 4, // Cashfree Subscription
        									"add_charges"		 => 0,
        									"payment_status"     => ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']:$this->payment_status['awaiting']),
        									"id_payGateway"      => (isset($planDetail['id_pg']) ? $planDetail['id_pg']: NULL),
        									"id_branch"          => $planDetail['id_branch'],
        									"due_month"          => $month,
    								        "due_year"           => $due_year,
    								        "due_type"           => $dueType
        								 );
    					    $response = $this->createPayment($insertData,$planDetail);
    					    if($response['status']){
    					        if($type == 1){
        				            $this->session->set_flashdata('successMsg','Payment Successful..');
        							redirect("/chitscheme/scheme_account_report/".$id_sch_ac);
    					        }
    						}else{
    						    if($type == 1){
        							$this->session->set_flashdata('errMsg','Error in retry payment, kindly contact admin...');
        							redirect("/chitscheme/scheme_report");
    						    }
    						}
    					}
                    }else{
                        if($type == 1){
        					$this->session->set_flashdata('errMsg',$result->message);
        					redirect("/chitscheme/scheme_report");
                        }
    				}       
                }
    	    }
    	}else{
    	    $response = array("status" => FALSE, "message" => "Invalid account data", "time" => date('d-m-Y H:i:s')); 
    	    if($type == 1){
				$this->session->set_flashdata('errMsg','Invalid account data...');
				redirect("/chitscheme/scheme_report");
            }
    	}
        echo json_encode($response);
    }
    
    function cf_curl($subsc_api,$postData,$gateway)
    {
    	$curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $gateway['api_url'].'api/v2/subscriptions/'.$subsc_api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postData),
            // Getting  server response parameters //
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
                "X-Client-Id: ".$gateway['param_3'],
                "X-Client-Secret:".$gateway['param_1']
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return array('status' => FALSE, 'result' => $err);
        } 
        else {
        	/*echo $gateway['api_url'].'api/v2/subscriptions/'.$subsc_api;
        	echo "<pre>";print_r($postData);
        	echo "<pre>";print_r($response);exit;*/
        	return array('status' => TRUE, 'result' => json_decode($response));
        }	
    }	
    
    function createPayment($insertData,$planDetail){
        $ins = $this->scheme_modal->insertData($insertData,'payment');
		// print_r($insertData);echo $this->db->last_query();print_r($ins);
		if($ins['insertID'] > 0){
		    $data = array();
		    $approval_type = $this->config->item('auto_pay_approval');
			if( $approval_type == 1 ||  $approval_type == 2 || $approval_type == 3){
				// Referral
		        if($planDetail['allow_referral'] == 1){
				    $ref_data	=	$this->payment_modal->get_refdata($planDetail['id_scheme_account']);
					$ischkref	=	$this->payment_modal->get_ischkrefamtadd($planDetail['id_scheme_account']);
			 		if($ref_data['ref_benifitadd_ins_type'] == 1 && $ref_data['referal_code'] != '' && ($ref_data['ref_benifitadd_ins'] == $ref_data['paid_installments']) && $ischkref == TRUE){	
						$this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
					}else if($ref_data['ref_benifitadd_ins_type'] == 0 && $ref_data['referal_code'] != '' && $ischkref == TRUE){
						$this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
					}
			 	}			    			 	
			    // Generate receipt number
				if($planDetail['receipt_no_set'] == 1 && ($approval_type == 1 ||  $approval_type == 2 || $approval_type == 3) )
				{  
					if($planDetail['scheme_wise_receipt'] == 1)
					{
					    $data['receipt_no'] = $this->generate_receipt_no($planDetail['id_scheme']);
					}
					else
					{
					    $data['receipt_no'] = $this->generate_receipt_no();
					}
					$this->payment_modal->update_receipt($ins['insertID'],$data);
				}
				// Generate account  number  based on one more settings Integ Auto//hh
				if($planDetail['schemeacc_no_set'] == 0 || $planDetail['schemeacc_no_set']==3)
				{  
					if($planDetail['scheme_acc_number'] == '' ||  $planDetail['scheme_acc_number'] == null)
					{
					    $ac_group_code = NULL;
						// Lucky draw
						if($planDetail['is_lucky_draw'] == 1 ){ // Based on scheme settings 
							// Update Group code in scheme_account table 
							$updCode = $this->payment_modal->updateGroupCode($planDetail); 
							$ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
						}
						$scheme_acc_number = $this->payment_modal->account_number_generator($planDetail['id_scheme'],$planDetail['id_branch'],$ac_group_code);  //Branch wise account number generation based on settings //HH
						if($scheme_acc_number != NULL){
							$schData['scheme_acc_number'] = $scheme_acc_number; 
						}
						if($planDetail['gent_clientid'] ==1 && $scheme_acc_number != NULL){  
							$cliData = array(
											 "cliID_short_code"	=> $this->config->item('cliIDcode'),
											 "sync_scheme_code"	=> $planDetail['sync_scheme_code'],
											 "ac_no"			=> $scheme_acc_number
											);											
							$schData['ref_no'] = $this->payment_modal->generateClientID($cliData);
						}
						if($planDetail['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2){ 	//upd client id & acc no cus reg table//
							$cliData = array(
											 "cliID_short_code"	=> $this->config->item('cliIDcode'),
											 "sync_scheme_code"	=> $planDetail['sync_scheme_code'],
											 "ac_no"			=> $scheme_acc_number
											);											
							$schDatacus['ref_no'] = $this->payment_modal->generateClientID($cliData);
							$schDatacus['scheme_acc_number'] = $scheme_acc_number;
							$schDatacus['group_code'] =$planDetail['code'];
						}
						$updSchAc = $this->payment_modal->update_account($schData,$planDetail['id_scheme_account']);
						 $updcusreg = $this->payment_modal->update_cusreg($schDatacus,$planDetail['id_scheme_account']); //acc no upd to cus reg tab//hh
						 $updtrans = $this->payment_modal->update_trans($schDatacus,$planDetail['id_scheme_account']); //Client Id upd to trans tab//
					}
				}
				//Update First Payment Amount In Scheme Account
				 if(($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($planDetail['firstPayamt_maxpayable']==1 || $planDetail['firstPayamt_as_payamt']==1) && ($planDetail['firstPayment_amt'] == '' ||  $planDetail['firstPayment_amt'] == null || $planDetail['firstPayment_amt'] == 0))
				 {
					if($planDetail['flexible_sch_type'] == 4 && ($planDetail['firstPayment_wgt'] == null || $planDetail['firstPayment_wgt'] == "") ){ // Fix First payable as weight
						$fixPayable = array('firstPayment_wgt'  =>  $insertData['metal_weight']);
					}else{
						$fixPayable = array('firstPayment_amt'  =>  $insertData['payment_amount']);
					}
					$status = $this->payment_modal->update_account($fixPayable,$planDetail['id_scheme_account']);	 
				 }
				// Insert Data in Intermediate table
				 if($approval_type == 2 || $approval_type == 3 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3))
				 {
					if($this->config->item('integrationType') == 1){
						$this->insert_common_data_jil($ins['insertID']);
					}else if($this->config->item('integrationType') == 2){
						$this->mobileapi_model->insert_common_data($ins['insertID']);
					}
				 }
		    }
		    $serviceID = 7;
			$service = $this->services_modal->checkService($serviceID);
			if($service['sms'] == 1)
			{
				$data =$this->services_modal->get_SMS_data($serviceID,$ins['insertID']);
				$mobile =$data['mobile'];
				$message = $data['message'];
				if($this->config->item('sms_gateway') == '1'){
	    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
	    		}
	    		elseif($this->config->item('sms_gateway') == '2'){
	    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
	    		}
			}
			/*if($service['email'] == 1 && isset($planDetail['email']) && $planDetail['email'] != '')
			{
				//$payData = $this->payment_modal->get_paymenMailData(isset($payment['id_payment'])?$payment['id_payment']:'');
				$to = $planDetail['email'];
				$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
				$data['payData'] = $planDetail;
				$data['type'] = 3;
				$data['company_details'] = $this->comp;
				$message = $this->load->view('include/emailPayment',$data,true);
				$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
			}*/
			$response = array("status" => TRUE, "message" => "New payment inserted successfully..", "time" => date('d-m-Y H:i:s')); 
		}else{
		    $response = array("status" => FALSE, "message" => "Unable to create payment", "time" => date('d-m-Y H:i:s')); 
		}
		return $response;
    }
    
    
    //To insert payment and registration details in intermediate table
	function insert_common_data_jil($id_payment)
	{
		$model = "chitapi_model";
		$this->load->model($model);
		//getting payment detail
		$pay_data = $this->$model->getPaymentByID($id_payment);	
		//storing temp values
		$trans_date = $pay_data[0]['trans_date'];
		$approval_no = $pay_data[0]['approval_no'];
		$ref_no = $pay_data[0]['ref_no'];
		$id_scheme_account = $pay_data[0]['id_scheme_account'];
		//getting customer detail to post registration again
		 $reg = $this->$model->getCustomerByID($id_scheme_account,$id_payment);		 
		//echo $this->db->last_query();
		 $isExists = $this->$model->checkTransExists($trans_date,$approval_no,$ref_no);
		if(!$isExists)
		{
			//insert payment detail
			$status =	$this->$model->insert_transaction($pay_data[0]);
			//echo $this->db->last_query();exit;
			  if($status)
			  {
				  //insert registration detail
				  if($reg)
				 {
					$reg[0]['transfer_jil']	= 'N';
					$reg[0]['transfer_date']= NULL ;
					$reg[0]['ref_no']		= $ref_no;
					$status = $this->$model->insert_CustomerReg($reg[0]);
						//echo $this->db->last_query();exit;
				 }	 
			  }	
		}	
			return true;
	}
	//To insert payment and registration details in intermediate table
	function insert_common_data_old($id_payment)
	{
		$model = "syncapi_model";
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
            	//echo $this->db->last_query();exit;
             }	
             //echo $this->db->last_query();exit;
		}
		$isTranExists = $this->$model->checkTransExists($ref_no);
		if(!$isTranExists)
		{
            //insert payment detail
            $pay_data[0]['record_to'] = 1;	
            $pay_data[0]['payment_type'] = 1;	// 1 - online 
            $status =	$this->$model->insert_transaction($pay_data[0]); 
		}
		//echo $this->db->last_query();exit;
		return true;
	}
	
	function generate_receipt_no($id_scheme='')
	{
		$rcpt_no = "";
		$rcpt = $this->payment_modal->get_receipt_no($id_scheme);
		//$company = $this->payment_modal->get_company();
		if($rcpt!=NULL)
		{
		  	$temp = explode($this->comp['short_code'],$rcpt);
			 	if(isset($temp))
			 	{
					$number = (int) $temp[1];
					$number++;
					$rcpt_no =$this->comp['short_code'].str_pad($number, 6, '0', STR_PAD_LEFT);
				}		   
		}
		else
		{
			 	$rcpt_no =$this->comp['short_code']."000001";
		}
			return $rcpt_no;
	}
	
	function insert_referral_data($id_scheme_account,$referral_data)
	{ 
		$status=FALSE;			
		$serviceID=16;
		if($referral_data['referal_code']!='' && $referral_data['is_refferal_by']==1){			
		  $data = $this->payment_modal->get_empreferrals_datas($id_scheme_account);
		}else if($referral_data['referal_code']!='' && $referral_data['is_refferal_by']==0){			
			$data = $this->payment_modal->get_cusreferrals_datas($id_scheme_account);
		}	
		if(!empty($data))
		{			
			if($data['referal_code']!='' && $data['referal_value']!=''  &&  $data['id_wallet_account']!=''){
			// insert wallet transaction data //
							$wallet_data = array(
							'id_wallet_account' => $data['id_wallet_account'],
							'id_sch_ac'         => $id_scheme_account,
							'date_transaction' =>  date("Y-m-d H:i:s"),
							'transaction_type' =>  0,
							'value'            => $data['referal_value'],
							'description'      => 'Referral Benefits - '.$data['cusname'].' ref no. '.$data['id_scheme_account']
							);
				$status =$this->payment_modal->wallet_transactionDB($wallet_data);
				  if($status)
				  {
				  		// Update credit flag in customer table
					  	/* is_refbenefit_crt = 0 -> already  benefit credited  & 1-> yet to credit benefits' */					 		
					  	if($referral_data['is_refferal_by']==0 && $data['cusbenefitscrt_type']==0 && ($data['schrefbenifit_secadd']==0 || $data['schrefbenifit_secadd']==1) ){
							// customer referal - single  
							$this->payment_modal->update_customer_only(array('is_refbenefit_crt_cus'=>0),$chkreferral['id_customer']);
						}else if($referral_data['is_refferal_by']==0 && $data['cusbenefitscrt_type']==1 && $data['schrefbenifit_secadd']==1){
							// customer referal - multiple  
							$this->payment_modal->update_customer_only(array('is_refbenefit_crt_cus'=>1),$chkreferral['id_customer']);
						}else if($referral_data['is_refferal_by']==1 && $data['empbenefitscrt_type']==0 && ($data['schrefbenifit_secadd']==0 || $data['schrefbenifit_secadd']==1)){	
							 // emp referal - single  					
							$this->payment_modal->update_customer_only(array('is_refbenefit_crt_emp'=>0),$chkreferral['id_customer']);
						}else if($referral_data['is_refferal_by']==1 && $data['empbenefitscrt_type']==1 && $data['schrefbenifit_secadd']==1){	
							// emp referal - single  			
							$this->payment_modal->update_customer_only(array('is_refbenefit_crt_emp'=>1),$chkreferral['id_customer']);
						}
				  } 
				  $service = $this->services_modal->checkService($serviceID);
				  if($service['sms'] == 1 && $data['mobile']){
				       $sms_data =$this->services_modal->get_SMS_data($serviceID,$data['id_scheme_account']); 
				       if($this->config->item('sms_gateway') == '1'){
			    		  $this->sms_model->sendSMS_MSG91($sms_data['mobile'],$sms_data['message'],'',$service['dlt_te_id']);		
			    	   }
			    	   elseif($this->config->item('sms_gateway') == '2'){
			    	      $this->sms_model->sendSMS_Nettyfish($sms_data['mobile'],$sms_data['message'],'trans');	
			    	   }
				  }
				 }
		}
	}
    
} 
?>