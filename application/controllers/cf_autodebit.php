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
	
     public function autoDebitRURL($id_sch_ac){
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
					$url = base_url()."index.php/cf_authRedirect/cf_authRURL/success";
					$color = "green";
				}
				else if($_POST['cf_status']){
					$status_flag = ($_POST['cf_status'] == "INITIALIZED" ? 1 : ($_POST['cf_status'] == "BANK_APPROVAL_PENDING" ? 2 : 2));
					$url = base_url()."index.php/cf_authRedirect/cf_authRURL/pending/".$status_flag;
					$color = "red";
				}				
				echo "<h5 style='margin-top:150px;font-size :50px;text-align : center;color:".$color."'>".$_POST['cf_message']."</h5>";  
				echo "<h5 align='center'><a class='btn btn-info' style='text-decoration: none;background: #5bc0de;text-align: center; border: 1px solid #46b8da;padding: 12px;color: #fff;font-size:18px;' href='".$url."'>Back To App</a></h5>";
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
	
	function cf_authRedirect($status){  
		echo "<h5 style='margin-top:150px;font-size :50px;text-align : center;'>Please wait  </h5>";
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
    
    function cf_retry($type,$id_sch_ac)
    {
        $approval_type = $this->config->item('auto_pay_approval');
        $response = [];
    	if($id_sch_ac > 0){ 
    	    if(!$this->session->userdata('username') && $type = 1)
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
    											"auth_status"		=>	$this->auth_status[$response->subStatus],
    											"last_update"		=>	date('Y-m-d H:i:s')
    											);
    					$upd = $this->scheme_modal->updateData($updSubscription,'id_auto_debit_subscription',$planDetail['id_auto_debit_subscription'],'auto_debit_subscription');
    					$updSchAc = array(
    									"auto_debit_status"	=>	$this->auth_status[$response->subStatus],
    									"date_upd"			=>	date('Y-m-d H:i:s')
    									);
    					$this->scheme_modal->updateData($updSchAc,'id_scheme_account',$id_sch_ac,'scheme_account');
    					
    					// Payment
    				    $isPaymentAlreadyExist = $this->scheme_modal->isPaymentAlreadyExist($response->payment->paymentId);
    					if($isPaymentAlreadyExist){ // Check whether payment already added
    						$response = array("status" => FALSE, "message" => "Payment_ref_number : ".$response->payment->paymentId." already exist", "time" => date('d-m-Y H:i:s')); 
    					}else{
    						$metal_rates = $this->payment_modal->getMetalRate($planDetail['id_branch']);
    						$rate_fields = $this->payment_modal->getRateFields($planDetail['id_metal']); 
    	                    $rate_field = sizeof($rate_fields) == 1 ? $rate_fields['rate_field'] : NULL;
    	                    $metal_rate   = (float) ( $rate_field == null ? null : $metal_rates[$rate_field] );
    	                    if($planDetail['scheme_type'] == 2 || ( $planDetail['scheme_type'] == 4 && ($planDetail['flexible_sch_type'] == 2 || $planDetail['flexible_sch_type'] == 3))){
    							$weight = $response->payment->amount/$metal_rate;
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
        									"payment_amount" 	 => $response->payment->amount, 
                                            "payment_type" 		 => "Cash Free",
                                            "payment_mode" 		 => "Subscription",
        									"gst" 	             => 0,
        									"gst_type" 	   		 => 0,
        									"gst_amount" 	   	 => NULL,
        									"no_of_dues" 	     => 1,
        									"act_amount" 	     => $response->payment->amount,
        									"actual_trans_amt"   => $response->payment->amount,
        									"date_payment"   	 => date('Y-m-d H:i:s'),
        									"metal_rate"         => $metal_rate == 0 ? NULL : $metal_rate,
        									"metal_weight"       => $metal_weight,	
        									"payment_ref_number" => $response->payment->paymentId,
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
						$this->insert_common_data($ins['insertID']);
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
	function insert_common_data($id_payment)
	{
		$model = "syncapi_model";
		$this->load->model($model);
		//getting payment detail
		$pay_data = $this->$model->getPaymentByID($id_payment);	
		//storing temp values
		$ref_no = $pay_data[0]['ref_no'];
		$id_scheme_account = $pay_data[0]['id_scheme_account']; 
		$isCusRegExists = $this->$model->checkCusRegExists($id_scheme_account,$ref_no);
		if(!$isCusRegExists['status']){
		     $reg = $this->$model->getCustomerByID($id_scheme_account);	
             //insert customer registration detail
             if($reg)
             {
            	$reg[0]['record_to']= 1 ;
            	$reg[0]['is_registered_online']= 2 ;  // 2 - online record
            	$reg[0]['ref_no']		= $ref_no;
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