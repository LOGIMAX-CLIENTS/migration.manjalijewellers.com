<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Jwl_adv_pay extends CI_Controller {
	const VIEW_FOLDER = 'jwl_adv_pay/';
	public function __construct()
    {
        parent::__construct();
		ini_set('date.timezone', 'Asia/Kolkata');
	    $this->load->model('login_model');
		$this->load->model('jwl_adv_pay_model'); 
		$this->load->model('sms_model');
		$this->load->model('email_model');
        $this->comp 	= $this->login_model->company_details();
        $this->m_mode 	= $this->login_model->site_mode();
        if( $this->m_mode['maintenance_mode'] == 1) {
        	$this->maintenance();
	    } 
	    //default payment status 
		$this->payment_status = array(
										'pending'   => 7,
										'awaiting'  => 2,
										'success'   => 1,
										'failure'   => 3,
										'cancel'    => 4
									  );
		
		$this->branch = '';
		
		$this->payment_type = 'Cash Free';
		$this->pg_code = '4';
		$this->id_payGateway = '7';
    }  
	
	public function index()
	{ 		
		redirect('jwl_adv_pay/form');
	}
	
	function form()
	{	     
		if($this->session->userdata('mobile') != '' || $this->session->userdata('jap_mob_verified') == 1){
			$settings = $this->jwl_adv_pay_model->getSettings('JAP'); 
			if($settings['m_active'] == 1){ // 1 - Enable
			    
				if($this->session->userdata('is_logged_in') == TRUE){
				    $check_mobileno = $this->jwl_adv_pay_model->check_mobileno($this->session->userdata('mobile'));
		  	  	    if($check_mobileno['status']){ // Not registered
    		  	  	    $cusData = $this->jwl_adv_pay_model->getCusByMob($this->session->userdata('mobile'));
    		  	  	    $insertData = array('mobile'    => $this->session->userdata('mobile'), 
    		  	  	                        'title'	    => $cusData['title'], 
                            				'firstname'	=> $cusData['firstname'], 
                            				'email'     => $cusData['email'],
                            				'module_code'=> 'JAP',
                            				'id_branch' => $cusData['id_branch']
            				                );
        				$this->jwl_adv_pay_model->insertData($insertData,'purchase_customer');
		  	  	    }else{
		  	  	        $cusData = $check_mobileno['data'];
		  	  	    }
		            $setData = array('jap_mobile'   => $this->session->userdata('mobile'), 
                    				'jap_cus_name'	=> $cusData['title'].' '.$cusData['firstname'], 
                    				'email'        	=> $cusData['email']
    				                );
    				        
    			    if($cusData['id_branch'] != ''){
    				     $branch = $this->jwl_adv_pay_model->get_branch_by_id($cusData['id_branch']); 
    				     $setData['jap_id_branch'] = $cusData['id_branch'];
    				     $setData['branch_name'] = $branch['name'];
    				}  
    				$this->session->set_userdata($setData);
				}
				$rate = $this->jwl_adv_pay_model->get_currency($this->session->userdata('jap_id_branch')); 
				$data['offers'] = array(
									"min_offer_wgt" => 40,
									"data"			=> array(
														"1" => array(
															array("name" => "No Offer","min_wgt" =>1,"max_wgt" =>39.999,"adv"  => 10,"disc" =>  [0]),array("name" => "Offer 1","min_wgt" =>40,"max_wgt" =>1000,"adv"  => 30,"disc" =>  [60,80]),array("name" => "Offer 2","min_wgt" =>40,"max_wgt" =>1000,"adv"  => 10,"disc" => [50,70])
														),
														"2" => array(
															array("name" => "No Offer","min_wgt" =>1,"max_wgt" =>39.999,"adv"  => 10,"disc" =>  [0]),array("name" => "Offer 1","min_wgt" =>40,"max_wgt" =>1000,"adv"  => 50,"disc" =>  [60,80]),array("name" => "Offer 2","min_wgt" =>40,"max_wgt" =>1000,"adv"  => 10,"disc" => [50,70])
														),
														"3" => array(
															array("name" => "No Offer","min_wgt" =>1,"max_wgt" =>39.999,"adv"  => 10,"disc" =>  [0]),array("name" => "Offer 1","min_wgt" =>40,"max_wgt" =>1000,"adv"  => 65,"disc" =>  [60,80]),array("name" => "Offer 2","min_wgt" =>40,"max_wgt" =>1000,"adv"  => 10,"disc" => [50,70])
														),
														"4" => array(
															array("name" => "No Offer","min_wgt" =>1,"max_wgt" =>39.999,"adv"  => 10,"disc" =>  [0]),array("name" => "Offer 1","min_wgt" =>40,"max_wgt" =>1000,"adv"  => 75,"disc" =>  [60,80]),array("name" => "Offer 2","min_wgt" =>40,"max_wgt" =>1000,"adv"  => 10,"disc" => [50,70])
														),
														"5" => array(
															array("name" => "No Offer","min_wgt" =>1,"max_wgt" =>39.999,"adv"  => 20,"disc" =>  [0]),array("name" => "Offer 1","min_wgt" =>40,"max_wgt" =>1000,"adv"  => 85,"disc" =>  [60,80]),array("name" => "Offer 2","min_wgt" =>40,"max_wgt" =>1000,"adv"  => 20,"disc" => [50,70])
														),
														"6" => array(
															array("name" => "Offer 1","min_wgt" =>40,"max_wgt" =>1000,"adv"  => 90,"disc" =>  [60,80]))
														)
														
									);
									
				// echo "<pre>";print_r($data['offers']['data']);exit;
				$data['history'] = $this->jwl_adv_pay_model->getCustomPurchasePlans(''); 
                $data['gold_rate'] = (float) $rate['metal_rates']['goldrate_22ct'];
                $data['silver_rate'] = (float) $rate['metal_rates']['silverrate_1gm'];
                $data['branch']=$this->jwl_adv_pay_model->get_branch();
				$data['content']['cusData'] = []; 
				$pageType = array('page' => 'jwl_adv_pay','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
	            $data['header_data'] = $pageType;
				$data['footer_data'] = $pageType;
				$data['fileName'] = self::VIEW_FOLDER.'form'; 
				$this->load->view('layout/template', $data);
			}else{ 
				$pageType = array('page' => 'jwl_adv_pay','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
	            $data['header_data'] = $pageType;
				$data['footer_data'] = $pageType;
				$data['fileName'] = "<div>Online Advance Booking not available.</div>";
				$this->load->view('layout/template', $data);
			}
		}else{
			$pageType = array('page' => 'jwl_adv_pay','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
			$data['content'] = array(); 
            $data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER.'jap_register'; 
			$this->load->view('layout/template', $data);
		} 
	}
	
	function send_sms($mobile,$message)
    {		
    	$url = $this->sms_data['sms_url'];
    	$senderid  = $this->sms_data['sms_sender_id'];
    		
    	 if(($this->sms_chk['debit_sms']!=0)){		
    		$arr = array("@customer_mobile@" => $mobile,"@message@" => str_replace(array("\n","\r"), '', $message),"@senderid@" => $senderid);	
    		$user_sms_url = strtr($url,$arr);
    		$ch = curl_init();
    		curl_setopt($ch, CURLOPT_URL, $user_sms_url);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
    		$result = curl_exec($ch);
    		curl_close($ch);
    		unset($ch);		
    		$status=$this->update_otp();		
    		if($status==1){		
    			return TRUE;
    		}else{
    			return FALSE;
    		}
    	}else{
    		return FALSE;
    	}
    } 
    
    function update_otp()
    {
		$query_validate=$this->db->query('UPDATE sms_api_settings SET debit_sms = debit_sms - 1	WHERE id_sms_api =1 and debit_sms > 0');  			
        if($query_validate>0)
        {
            return true;
        }else{
            return false;
        }
    }
	 
	/*public function generateOTP($mobile,$type,$cus_name='')
	{
		   
		   if(strlen(trim($mobile)) == 10)
		   { 
				$this->session->unset_userdata("OTP");
				$OTP = mt_rand(100000, 999999);
				$this->session->set_userdata('OTP',$OTP);
				 
				$message = $OTP." is the verification code from ".$this->comp['company_name']." Please use this code to verify your mobile number."; 
				          
				//$this->send_sms($mobile,$message);
				if($this->config->item('sms_gateway') == '1'){
	    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'','1607100000000098899');		
	    		}
	    		elseif($this->config->item('sms_gateway') == '2'){
	    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
	    		}
			
				echo 1; 
			}
			else
				echo 2;
	}*/
	public function check_email() 
	{
			echo $this->jwl_adv_pay_model->clientEmail($_POST["email"]);
	}	
	 
	public function logout()
	{
		$this->session->unset_userdata("jap_mobile");
		$this->session->sess_destroy();
		redirect("/jwl_adv_pay/form");
	}	
		
	public function submitPay()
	{
		if(!$this->session->userdata('jap_mobile'))
		{
			redirect("/jwl_adv_pay/form");
		}
		else
		{ 
			if($_POST['amount'] < 1000){
				$scheme_failure = array("errMsg" => 'Invalid Payment Amount');  
				$this->session->set_flashdata($scheme_failure);
				redirect("/jwl_adv_pay/form");
			}
			else{ 
				//echo "<pre>";print_r($_POST);exit;
				$post_amount 	= $_POST['amount'];
				$updData = array();
    		    if(($this->session->userdata("jap_cus_name") == '' || $this->session->userdata("jap_cus_name") == ' ' ) && !empty($_POST['firstname'])){
    		        $updData['firstname'] = ucfirst($_POST['firstname']);
    		        $updData['title']   = $_POST['title'];
    		        $cus_name = $_POST['title'].' '.$_POST['firstname'];
                    $this->session->set_userdata('jap_cus_name',$cus_name); 
    		    }
    		    if($this->session->userdata("branch_name") == '' && !empty($_POST['id_branch'])){
    		        $updData['id_branch'] = $_POST['id_branch'];
    		        $branch=$this->jwl_adv_pay_model->get_branch_by_id($_POST['id_branch']);
    		    	$this->session->set_userdata('jap_id_branch',$_POST['id_branch']); 
    		    	$this->session->set_userdata('branch_name',$branch['name']); 
    		    }
    		    if($this->session->userdata("email") == '' && !empty($_POST['email'])){
    		        $updData['email'] = $_POST['email'];
    		        $this->session->set_userdata('email',$_POST['email']); 
    		    }
    		    if($this->session->userdata("alter_mobile") == '' && !empty($_POST['alter_mobile'])){
    		        $updData['alter_mobile'] = $_POST['alter_mobile'];
    		        $this->session->set_userdata('alter_mobile',$_POST['alter_mobile']); 
    		    } 
    		    if(sizeof($updData) > 0){
    		        $updData["updated_on"] = date('Y-m-d H:i:s');
    			    $this->jwl_adv_pay_model->updateData($updData,'mobile',$this->session->userdata("jap_mobile"),'purchase_customer');
    		    } 
				$proceed_pay = FALSE;
				$get_rate = $this->jwl_adv_pay_model->get_currency($_POST['id_branch']);   
            	$g_rate = (float) $get_rate['metal_rates']['goldrate_22ct']; 
            	$s_rate = (float) $get_rate['metal_rates']['silverrate_1gm']; 
            	$amount = ($g_rate*$_POST['weight'])*($_POST['adv_percent']/100);          	
				
				
				if(round($amount) == round($post_amount)){ 
					$metal_rate 		= $_POST['rate'];
					$metal_wgt 			= $_POST['weight'];  
					$txnid 				= uniqid(time()); 
					$productinfo		= "Online Advance Booking"; 
					$this->session->set_userdata('jap_txn_id',$txnid);
		            $this->session->set_userdata('jap_amount',$amount);
		            
		            $paycred = $this->jwl_adv_pay_model->getBranchGatewayData($_POST['id_branch'],$this->pg_code);  
		            //echo $this->db->last_query(); print_r($paycred);exit;
					$insertData = array( 
									"mobile" 	     	 => $this->session->userdata("jap_mobile"),  
									"module_code"     	 => "JAP",  // Jewellery Advance Payment
									"payment_type" 	     => $this->payment_type,  
									"type" 	     		 => 3,  // Jewellery Advance Payment
									"otp"	 	     	 => $this->session->userdata("otp"),  
									"payment_amount"	 => $amount,
									"actual_trans_amt"   => $amount,
									"gst"				 => 3,
									"gst_type"			 => 2,
									"gst_amt"	         => NULL,
									"date_add"   	 	 => date('Y-m-d H:i:s'),
									"metal_rate"         => $metal_rate,
									"metal_weight"       => ($metal_wgt !='' ? $metal_wgt : 0.000),
									"id_transaction"     => (isset($txnid) ? $txnid.'-1': NULL), 
									"ref_trans_id"       => (isset($txnid) ? $txnid: NULL), 
									"added_by"			 =>  1, 
									"payment_status"     =>  $this->payment_status['pending'], 
									"id_payGateway"      =>  $paycred['id_pg'],
    						        "pan_no"             => isset($_POST['pan']) ? $_POST['pan'] : NULL,
    						        "offer_name"		 => $_POST['offer_name'],
    						        "disc_mc_percent"	 => $_POST['mc_disc_percent'],
    						        "no_of_month"	 	 => $_POST['no_of_month'],
    						        "adv_paid_percent" 	 => $_POST['adv_percent'],
								 );
					$this->db->trans_begin();
		    		$payment = $this->jwl_adv_pay_model->insertData($insertData,'purchase_payment');		
		    		//echo "<pre>";print_r($_POST);echo "<pre>";print_r($insertData);// echo $this->db->last_query();exit;
		    		if($this->db->trans_status() === TRUE){
		    			$this->db->trans_commit();
						 
						$data = array (
										'key' 			=> $paycred['param_1'],
										'txnid' 		=> $txnid,
										'amount' 		=> $amount,
										'firstname' 	=> $this->session->userdata("jap_cus_name"),
										'lastname' 		=> '',
									    'email' 		=> $this->comp['email'],
										'phone' 		=> $this->session->userdata("jap_mobile"),
										'productinfo'	=> (isset($productinfo)? $productinfo: ''),
										'address1'		=> '',
										'address2'		=> '',
										'city'			=> '',
										'state'			=> '',
										'country'		=> '',
										'zipcode'		=> '',
										'm_code'		=> $paycred['param_3'],
										'iv'			=> $paycred['param_4'],
										'udf1' 			=> (isset($udf1)    ? $udf1 :''),
										'udf2' 			=> (isset($metal_wgt)  ? $metal_wgt :''),
										'udf3'			=> (isset($metal_rate) ? $metal_rate :''),
										'udf4'			=> (isset($amount)    ? $amount :''),
										'udf5' 			=> (isset($udf5)    ? $udf5 :'')
										); 
									
						if($this->pg_code == 1){ // Payu
				            $hash_sequence = Misc::get_hash($data,$paycred['param_2']);	
				            $datas['pay']=	array (
								'key' 			=> $paycred['param_1'],
								'txnid' 		=> $txnid,
								'amount' 		=> $amount,
								'firstname' 	=> $this->session->userdata("jap_cus_name"),
								'lastname' 		=> '',
							    'email' 		=> $this->comp['email'],
								'phone' 		=> $this->session->userdata("jap_mobile"),
								'productinfo'	=> (isset($productinfo)? $productinfo: ''),
								'address1'		=> '',
								'address2'		=> '',
								'city'			=> '',
								'state'			=> '',
								'country'		=> '',
								'zipcode'		=> '',
								'm_code'		=> $paycred['param_3'],
								'iv'			=> $paycred['param_4'],
								'udf1' 			=> (isset($udf1)    ? $udf1 :''),
								'udf2' 			=> (isset($metal_wgt)  ? $metal_wgt :''),
								'udf3'			=> (isset($metal_rate) ? $metal_rate :''),
								'udf4'			=> (isset($amount)    ? $amount :''),
								'udf5' 			=> (isset($udf5)    ? $udf5 :''),
								'surl'			=> 'payment_success',
								'furl' 			=> 'payment_failure',
								'curl' 			=> site_url('jwl_adv_pay/payment_cancel'),
								'user_credentials' 	=> $paycred['param_1'].':'.$this->session->userdata("jap_mobile"),
								'hash' 			=> $hash_sequence
							);
						   if($hash_sequence!='' && $txnid !='')
							{ 
								$datas['pay']['hash'] 	=  $hash_sequence;  
								$datas['pay']['curl']    =  $this->config->item('base_url')."index.php/jwl_adv_pay/payment_cancel"; 
								$datas['pay']['furl']    =  $this->config->item('base_url')."index.php/jwl_adv_pay/payment_failure"; 
								$datas['pay']['surl']    =  $this->config->item('base_url')."index.php/jwl_adv_pay/payment_success";
								$datas['pay']['user_credentials'] =  $paycred['param_1'].':'.$this->session->userdata("jap_mobile");
								//echo"<pre>"; print_r($datas);exit;
								$this->load->view('web/payment',$datas);
							}
						} 
						elseif($this->pg_code == 2){ // HDFC
							$merchant_data ="";
					        $merchant_id   = $paycred['param_4'];
					        $working_key   = $paycred['param_1'];//Shared by CCAVENUES
					        $access_code   = $paycred['param_3'] ;//Shared by CCAVENUES 
					        $data['hdfcpay'] =	array (
					        'tid' 		    =>  (rand(10,100).''.time()), // Should contain numbers only
					        'merchant_id'   =>  $merchant_id,
					        'order_id'      =>  $txnid,
					        'amount' 		=>  $amount,
					        'productinfo'	=> (isset($productinfo)? $productinfo: ''),
					        'currency'	    => 'INR',
					        'redirect_url'  =>  $this->config->item('base_url')."index.php/jwl_adv_pay/responseURL",
					        'language'      => 'EN',
					        'id_payment'    => $payment['insertID'],
					        'firstname' 	=> $this->session->userdata("jap_cus_name"),
					        'lastname' 		=> '',
					        'email' 		=> '',
					        'phone' 		=> $this->session->userdata("jap_mobile"),
					        'address1'		=> '', 
					        'address2'		=> '', 
					        'city'			=> '', 
					        'state'			=> '', 
					        'country'		=> '', 
					        'zipcode'		=> '', 
					        'merchant_param1' => (isset($udf1) ? $udf1 :''),
					        'merchant_param2' => (isset($udf2) ? $udf2 :''),
					        'merchant_param3' => (isset($udf3) ? $udf3 :''),
					        'merchant_param4' => (isset($payment['insertID']) ? $payment['insertID'] : ''),
					        'merchant_param5' => (isset($udf5) ? $udf5:'') 
					        );
					        
					        foreach ($data['hdfcpay'] as $key => $value){ 
					            $merchant_data.=$key.'='.urlencode($value).'&';
					        }   
					        
					        $encrypted_data=encrypt($merchant_data,$working_key);
					        
					        //Generate Encrypted Datas
					        if($encrypted_data!='' && $txnid !='')
					        {
					            $data['hdfcpay']['encRequest']   = $encrypted_data;
					            $data['hdfcpay']['access_code']  = $access_code; 
					            //echo "<pre>";print_r($data);exit;
					            $this->load->view('hdfc/payment',$data);
					        }
						}
						elseif($this->pg_code == 3){ // TECH PROCESS
							$this->submitToTechProcess($data,'web','SS');
						}
						else if($this->pg_code == 4){   // CASH FREE 
							$secretKey       = $paycred['param_1']; //Shared by Cashfree
		                    $data['cashfreepay'] =	array (
			                    'appId'         => $paycred['param_3'],  //Shared by Cashfree
			                    'orderId'       =>  $txnid,
			                    'orderAmount' 	=>  $amount, 
			                    'orderCurrency'	=> 'INR',
			                    'orderNote'	    => 'Online Advance Payment',
			                    'customerName' 	=> $this->session->userdata("jap_cus_name"),
			                    'customerEmail' => $this->comp['email'],
			                    'customerPhone' => $this->session->userdata("jap_mobile"),
								"returnUrl"     => $this->config->item('base_url')."index.php/jwl_adv_pay/cashfreeresponseURL",
			                    "notifyUrl"     => ''	
			                     //"notifyUrl"     => $this->config->item('base_url')."index.php/services/cashfreeStatusNotify/".$this->session->userdata('id_pg')	
		                     ); 
		                    // get secret key from your config
		                    ksort($data['cashfreepay']); 
		                    $signatureData = "";
		                    foreach ($data['cashfreepay'] as $key => $value){ 
		                           $signatureData .= $key.$value;
		                      }
		                    //  echo $signatureData.'<br/>';
							$signature       = hash_hmac('sha256', $signatureData,$secretKey,true);
		                    $signature       = base64_encode($signature);
						    // print_r($signature);exit;
							//Generate Encrypted Datas
		                    if($signature!='' && $secretKey !='' && $txnid !='')
		                    {
		                        $data['cashfreepay']['signature']   = $signature; 
		                       //print_r($signature);exit;
		                        $this->load->view('cashsfree/payment',$data);
		                    }
							
						}
					    
					}else{
						/*echo $this->db->_error_message();
						echo $this->db->last_query();exit;*/
						$this->db->trans_rollback();
						$scheme_failure = array("errMsg" => 'Unable to proceed payment');
						$this->session->set_flashdata($scheme_failure);
						redirect("/jwl_adv_pay/form");
					}
				}
				else{ 
					$this->db->trans_rollback();
					$err = '';
					if($this->session->userdata("jap_mobile") == 8526737799){
						echo "rollback";
        				echo round($amount);
        				echo round($post_amount);
        				exit;
					}
					$scheme_failure = array("errMsg" => 'Sorry.. We are unable to proceed your payment.Please try after sometime.'.$err);
					$this->session->set_flashdata($scheme_failure);
					redirect("/jwl_adv_pay/form");
				}
    		} 
		}
	} 
	
	// Cash Free
	public function cashfreeresponseURL()
   	{
    	 $paymentgateway = $this->jwl_adv_pay_model->getBranchGatewayData($this->session->userdata('jap_id_branch'),$this->pg_code); 
    	 $secretkey  = $paymentgateway['param_1'];	//secret Key should be provided here.
		 //print_r($secretkey);exit;
    	 
		 $orderId       = $_POST["orderId"];
		 $orderAmount   = (float) $_POST["orderAmount"];
		 $referenceId   = $_POST["referenceId"];
		 $txStatus      = $_POST["txStatus"];
		 $paymentMode   = $_POST["paymentMode"];
		 $txMsg         = $_POST["txMsg"];
		 $txTime        = $_POST["txTime"];
		 $signature     = $_POST["signature"];
		 //print_r($signature);exit;
		 $data = $orderId.$orderAmount.$referenceId.$txStatus.$paymentMode.$txMsg.$txTime; 
		 $hash_hmac = hash_hmac('sha256', $data, $secretkey, true) ; 
		 $computedSignature = base64_encode($hash_hmac); 
		                                
	 	 $updateData = array("mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))), 
        					"cardnum"	  => NULL ,
        					"field9"      => $txMsg."[".$txTime."]", // remark
        					"bank_ref_num"=> $referenceId ,
        					"txnid"       => $orderId,
        					"amount"      => $orderAmount,
        					"Signature"   => $signature
            				);  
			 // print_r($updateData);exit;
        if((bccomp($orderAmount, $this->session->userdata('jap_amount'), 2) == 0) && $orderId == $this->session->userdata('jap_txn_id'))
        {
    			$this->session->unset_userdata('jap_amount');
    			$this->session->unset_userdata('jap_txn_id'); 
            	if($txStatus === "SUCCESS")
    			{ 
    				$this->payment_success($updateData,4);
    			}
    			else if($txStatus === "FAILED")
    			{ 
    			    $this->payment_failure($updateData,4);			
    			}
    				else if($txStatus === "CANCELLED")
    			{ 
    			    $this->payment_cancel($updateData,4);			
    			}
    		
    			else
    			{
    				$scheme_failure = array("errMsg" => 'Sorry, signature Verification failed!! Please contact admin if amount debited from your bank account..');
    				$this->session->set_flashdata($scheme_failure);
    				redirect("/jwl_adv_pay/form");
    			}
        } 
    	else
    	{
    	    
           $scheme_failure = array("errMsg" => 'Sorry, signature Verification failed!! Please contact admin if amount debited from your bank account...');
    		// Write log	
    		$logData = array("c1" => (bccomp($orderAmount, $this->session->userdata('jap_amount'), 2) == 0),"orderAmount"=>$orderAmount,"jap_amount"=>$this->session->userdata('jap_amount'),"orderId"=>$orderId,"jap_txn_id"=>$this->session->userdata('jap_txn_id'));
    		$log_path = 'log/cashfree/AT_'.date("Y-m-d").'.txt';   
			$ldata = "\n".date('d-m-Y H:i:s')." \n Result : ".json_encode($logData,true);
			file_put_contents($log_path,$ldata,FILE_APPEND | LOCK_EX);
    		$this->session->set_flashdata($scheme_failure);
    		redirect("/jwl_adv_pay/form");
    	}

   	} 
	// Cash Free Ends
	
	// Techprocess
	function submitToTechProcess($payData,$type,$payFor)
	{    
		$mrctCode =$payData['m_code'];  
        $key = $payData['key'];   
	    $iv = $payData['iv'];  

	    //$ClientMetaData = $payData['productinfo'].' - param1'.$payData['udf1'].' - param2'.$payData['udf2'].' - param3'.$payData['udf3'].' - param4'.$payData['udf4'];
	    $ClientMetaData=$payData['phone'];
	    //$reqType = ($type == 'web' ? 'T': $payData['pg'] == 'NB' ? 'T' : 'TRC' );
	    $reqType = 'T';
	    $currency = 'INR';
		
		$rMblURL = ($payFor == "G" ? "gPaytechProMblResponseURL" : "techProcessMobileResponseURL");
		$rwebURL = ($payFor == "G" ? "gPaytechProWebResponseURL" : "techProcessResponseURL");
		$returnURL = ($type == 'web' ? site_url('jwl_adv_pay/'.$rwebURL) : site_url('jwl_adv_pay/'.$rMblURL.'?'.$payData['id_branch'].'&'.$payData['id_pg'])); 		
		
	    $ShoppingCartDetails = 'FIRST_'.$payData['amount'].'_0.0';
	    $txnDate  = date('d-m-Y'); 
	    $locatorURL = "https://www.tpsl-india.in/PaymentGateway/TransactionDetailsNew.wsdl";  // LIVE 
	    $timeOut = '60000'; 
	    $bankCode = '1180'; // for TRC only
    
        
        $transactionRequestBean = new TransactionRequestBean();
    
        //Set all values here
        $transactionRequestBean->setMerchantCode($mrctCode);
        $transactionRequestBean->setITC($ClientMetaData);
        $transactionRequestBean->setRequestType($reqType);
        $transactionRequestBean->setCurrencyCode($currency);
        $transactionRequestBean->setReturnURL($returnURL);
        $transactionRequestBean->setShoppingCartDetails($ShoppingCartDetails); // conditional
        $transactionRequestBean->setTxnDate($txnDate); 
        $transactionRequestBean->setKey($key);
        $transactionRequestBean->setIv($iv);
        $transactionRequestBean->setWebServiceLocator($locatorURL);
        $transactionRequestBean->setTimeOut($timeOut);
        
        //$transactionRequestBean->setMobileNumber($payData['phone']); //mobile registerd with bank
        $transactionRequestBean->setCustomerName($payData['firstname']);
        $transactionRequestBean->setMerchantTxnRefNumber($payData['txnid']);
        $transactionRequestBean->setAmount($payData['amount']);
        $transactionRequestBean->setCustId($payData['phone']); // unique number
        
        if($reqType == 'TRC'){
    	    $transactionRequestBean->setCardName($payData['ccname']);
            $transactionRequestBean->setCardNo($payData['ccnum']);
            $transactionRequestBean->setCardCVV($payData['ccvv']);
            $transactionRequestBean->setCardExpMM($payData['ccexpmon']);
            $transactionRequestBean->setCardExpYY($payData['ccexpyr']);
            $transactionRequestBean->setBankCode($bankCode);
    	}
        
        //$transactionRequestBean->setBankCode($payData['bankcode']);
        //$transactionRequestBean->setCardId($val['cardID']); 
        //$transactionRequestBean->setMMID($val['mmid']);
        //$transactionRequestBean->setOTP($val['otp']);
        //$transactionRequestBean->setAccountNo($val['tpvAccntNo']); 
        
        
    
       // $url = $transactionRequestBean->getTransactionToken(); 
        
        $responseDetails = $transactionRequestBean->getTransactionToken();
        $responseDetails = (array)$responseDetails;
        $response = $responseDetails[0]; 
      /*  echo "<pre>";
        print_r($transactionRequestBean);
        print_r($response);exit; */
    
        if(is_string($response) && preg_match('/^msg=/',$response)){
            $outputStr = str_replace('msg=', '', $response);
            $outputArr = explode('&', $outputStr);
            $str = $outputArr[0];
    
            $transactionResponseBean = new TransactionResponseBean();
            $transactionResponseBean->setResponsePayload($str);
            $transactionResponseBean->setKey($key);
            $transactionResponseBean->setIv($iv);
   
            $response = $transactionResponseBean->getResponsePayload();
            if($type == 'web'){
                $msg = array("errMsg" => 'Unable to proceed your request please try again later or contact admin...');
			    $this->session->set_flashdata($msg);
			    redirect("/jwl_adv_pay");
            }else{
                $updateData = array(   
                		"remark"            => $response,
                	    "payment_status"    => $this->payment_status['failure']
                	 //status - 0 (pending), will change to 1 after approved at backend
                	);
        		if($payFor == "SS"){
            		$payment = $this->jwl_adv_pay_model->updateGatewayResponse($updateData,$payData['txnid']);
            		redirect("/jwl_adv_pay/failureMURL");
        		}
        		/*elseif($payFor == "G"){
        		    $payment = $this->jwl_adv_pay_model->updateGcardPay($updateData,$payData['txnid']);
        		    redirect("/jwl_adv_pay/gPayResponseMURL/f");
        		}*/
            }
			
        }elseif(is_string($response) && preg_match('/^txn_status=/',$response)){
            if($type == 'web'){
                $msg = array("errMsg" => 'Unable to proceed your request please try again later or contact admin...');
			    $this->session->set_flashdata($msg);
			    redirect("/jwl_adv_pay");
            }else{ 
                $updateData = array(   
                		"remark"            => $response,
                	    "payment_status"    => $this->payment_status['failure']
                	 //status - 0 (pending), will change to 1 after approved at backend
                	);
        		if($payFor == "SS"){
            		$payment = $this->jwl_adv_pay_model->updateGatewayResponse($updateData,$payData['txnid']);
            		redirect("/jwl_adv_pay/failureMURL");
        		}
        		/*elseif($payFor == "G"){
        		    $payment = $this->jwl_adv_pay_model->updateGcardPay($updateData,$payData['txnid']);
        		    redirect("/jwl_adv_pay/gPayResponseMURL/f");
        		}*/
            }
    	}
    	
    	$parseURL = parse_url($response);
        if($parseURL['path'] == "/PaymentGateway/txnreq.pg" || $parseURL['path'] == "/PaymentGateway/txnreqcardver2.pg"){
            echo "<script>window.location = '".$response."'</script>";   
        }else{
            if($type == 'web'){
                $msg = array("errMsg" => 'Unable to proceed your request please try again later or contact admin...');
			    $this->session->set_flashdata($msg);
			    redirect("/jwl_adv_pay");
            }else{
                $updateData = array(   
                		"remark"            => $response,
                	    "payment_status"    => $this->payment_status['failure']
                	 //status - 0 (pending), will change to 1 after approved at backend
                	);
        		if($payFor == "SS"){
            		$payment = $this->jwl_adv_pay_model->updateGatewayResponse($updateData,$payData['txnid']);
            		redirect("/jwl_adv_pay/payment_failure");
        		}
        		/*elseif($payFor == "G"){
        		    $payment = $this->jwl_adv_pay_model->updateGcardPay($updateData,$payData['txnid']);
        		    redirect("/jwl_adv_pay/gPayResponseMURL/f");
        		}*/
            } 
        }
    }
    
    function techProcessResponseURL($payData = "")
    {
	     
	   /* $mrctCode = $this->payment_gateway[2]['m_code'];  
        $key = $this->payment_gateway[2]['key'];   
	    $iv = $this->payment_gateway[2]['param_1']; */

 		$paymentgateway = $this->jwl_adv_pay_model->getBranchGatewayData($this->branch,$this->pg_code);
	
	 	$iv = $paymentgateway['param_4'];
	  	$key = $paymentgateway['param_1'];
	  	$mrctCode = $paymentgateway['param_3'];
	  	
        $response = $_POST;

        if(is_array($response)){
            $str = $response['msg'];
        }else if(is_string($response) && strstr($response, 'msg=')){
            $outputStr = str_replace('msg=', '', $response);
            $outputArr = explode('&', $outputStr);
            $str = $outputArr[0];
        }else {
            $str = $response;
        }
    
        $transactionResponseBean = new TransactionResponseBean();
    
        $transactionResponseBean->setResponsePayload($str);
        $transactionResponseBean->setKey($key);
        $transactionResponseBean->setIv($iv);
    
        $response = $transactionResponseBean->getResponsePayload();
        /*echo "<pre>";
        print_r($response);
        echo "<br><br><br><br>";  exit;*/
        
        /*txn_status=0300|txn_msg=success|txn_err_msg=NA|clnt_txn_ref=15420175985be9523e2b822|tpsl_bank_cd=470|tpsl_txn_id=643573574|txn_amt=10.00|
        clnt_rqst_meta={custname:Pavithra}|tpsl_txn_time=12-11-2018 15:43:36|tpsl_rfnd_id=NA|bal_amt=NA|rqst_token=f5b01fa2-59b9-4fd6-9fa4-590341178f14|
        hash=3538400ed021fc95755b03f3454ea9385b5f03e2*/
         
        $transData = array(); 
		$payData = explode("|", $response); 
        $status_code = "";
        $status_msg = "";
        $err_msg = "";
        $txn_id = "";
        $payu_id = "";
        $mode = "";
        
        foreach($payData as $pay){ 
        	$r = explode("=", $pay); 
        	if($r[0] === "txn_status") $status_code = $r[1];
        	if($r[0] === "txn_msg") $status_msg = $r[1];
        	if($r[0] === "txn_err_msg") $err_msg = $r[1];
        	if($r[0] === "clnt_txn_ref") $txn_id = $r[1]; 
        	if($r[0] === "tpsl_txn_id") $payu_id = $r[1];
        		
        }
        
        $mode = ($mode == '137'? 'RuPay':($mode == '127' || $mode == '82'? 'CC':($mode == '128' || $mode == '118'? 'DC': ($mode == 'NA' ? 'NA':'NB') ) ) );
        
        $updateData = array(
        		"payment_mode"      => (isset($mode) ?$mode : NULL),
        		"payu_id"           => (isset($payu_id) ? $payu_id : NULL),
        		"remark"            => 'Bank Code :'.$mode.'.'.$status_msg.' - '.$status_code.' - '.($err_msg != 'NA' ? $err_msg : ''),
        	    "payment_status"    => ($status_code == '0300' ? ( $this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']:$this->payment_status['awaiting']):($status_code == '0392'?$this->payment_status['cancel']:$this->payment_status['failure']))
        	 //status - 0 (pending), will change to 1 after approved at backend
        	); 
		$payment = $this->jwl_adv_pay_model->updateGatewayResponse($updateData,$txn_id); 
	    
		if($payment['status'] == true)
		{
			/*if($service['sms'] == 1)
			{
				$id=$payment['id_payment'];
				$data =$this->services_modal->get_SMS_data($serviceID,$id);
				$mobile =$data['mobile'];
				$message = $data['message']; 
				if($this->config->item('sms_gateway') == '1'){
	    		    $this->sms_model->sendSMS_MSG91($mobile,$message);		
	    		}
	    		elseif($this->config->item('sms_gateway') == '2'){
	    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
	    		}
			}
			if($service['email'] == 1 && isset($payData[0]['email']) && $payData[0]['email'] != '')
			{
				$to = $payData[0]['email'];
				$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
				$data['payData'] = $payData[0];
				$data['type'] = 3;
				$data['company_details'] = $this->comp;
				$message = $this->load->view('include/emailPayment',$data,true);
				$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
			}*/
			if($status_code == '0300'){							
			    $scheme_success = array("successMsg" => 'Payment '.$status_msg.'. Thanks for your payment with '.$this->comp['company_name'].'');
			}else{
			    $scheme_success = array("errMsg" => 'Payment '.$status_msg);
			}
			
			$this->session->set_flashdata($scheme_success);
			redirect("/jwl_adv_pay/form");
		}
		else
		{
			$msg = array("errMsg" => 'Error in updating the database.Please contact administrator at your earliest convenience.');
			$this->session->set_flashdata($msg);
			redirect("/jwl_adv_pay/form");
		}
    } 
    // Techprocess Ends
    
    
	// HDFC
    public function responseURL()
    {
		$paymentgateway = $this->jwl_adv_pay_model->getBranchGatewayData(1,2); 
		$workingKey  = $paymentgateway['param_1'];	//Working Key should be provided here.
		$encResponse = $_POST["encResp"];			//This is the response sent by the CCAvenue Server
		$rcvdString  = decrypt($encResponse,$workingKey);		//Crypto Decryption used as per the specified working key.
		 
		$decryptValues = explode('&', $rcvdString);
	    $txnid = "";
	    $issuing_bank = "";
	    $tracking_id = "";
	    $order_status = "";
		$payment_mode = "";
		$bank_ref_no = "";
		$amount = "";
		$mer_amount = "";
		$merchant_param4 = "";
		 
		$dataSize = sizeof($decryptValues);
	
			for($i = 0; $i < $dataSize; $i++) 
			{
				$information=explode('=',$decryptValues[$i]);
				if($i==0)	$txnid        = $information[1];
				if($i==1)	$tracking_id  = $information[1];
				if($i==2)	$bank_ref_no   = $information[1];
				if($i==3)	$order_status = $information[1] ;
				if($i==5)	$payment_mode = ($information[1] == "Net Banking" ? "NB" : ($information[1] == "Credit Card" ? "CC":($information[1] == "Debit Card" ? "DC":"")) );
				if($i==10)	$amount       = $information[1];
				if($i==29)	$merchant_param4  = $information[1];
				if($i==35)	$mer_amount   = $information[1]; 
			}
			

			
			/*$payment_amount = $this->jwl_adv_pay_model->getpayment_amount($merchant_param4);
		     
			if($payment_amount['id_payment'] !='') { 
		 
			if($order_status==="Success" && $mer_amount == $payment_amount['actual_trans_amt'])*/
			$updateData = array("issuing_bank"=> $issuing_bank,
            					"mode"        => $payment_mode,
            					"cardnum"	  => NULL ,
            					"mihpayid"    => $tracking_id,
            					"remark"      => $order_status,
            					"field9"      => $order_status, // remark
            					"bank_ref_num"=> $bank_ref_no ,
            					"txnid"       => $txnid,
            					"amount"      => $mer_amount
            				  );  
            
            if($mer_amount == $this->session->userdata('jap_amount') && $txnid == $this->session->userdata('jap_txn_id'))
            {
            			$this->session->unset_userdata('jap_amount');
            			$this->session->unset_userdata('jap_txn_id');
                    	if($order_status==="Success")
            			{ 
            				$this->payment_success($updateData,2);
            			}
            			else if($order_status==="Aborted")
            			{
            			    $this->payment_cancel($updateData,2);
            			}
            			else if($order_status==="Failure")
            			{ 
            			    $this->payment_failure($updateData,2);			
            			}
            			else
            			{
            				$scheme_failure = array("errMsg" => 'Security Error. Illegal access detected');
            				$this->session->set_flashdata($scheme_failure);
            				redirect("/jwl_adv_pay/form");
            			}
            }   
			else
			{
				$scheme_failure = array("errMsg" => 'Security Error. Illegal access detected');
				$this->session->set_flashdata($scheme_failure);
				redirect("/jwl_adv_pay/form");
			}
	  /* }*/
   }
   
    public function payment_success($payData = "", $gateway = "")
    {
        if($gateway == ""){
            $payData = $_POST;
        }  
	    if(!empty($payData['txnid']) && $payData['txnid'] != NULL){
    	    $amount =0;
       	    $remark =''; 
    		
            $id_transaction = $payData['txnid']; 
            $transData = array(); 
			$approval_type = $this->config->item('auto_pay_approval');
    		$updateData = array(
    						"bank_name"			 =>	(isset($payData['issuing_bank']) ? $payData['issuing_bank'] : NULL),
    						"payment_mode"       => (isset($payData['mode']) ? $payData['mode'] : NULL),
    						"card_no"			 => (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? $payData['cardnum'] :NULL ),
    						"payu_id"             => (isset($payData['mihpayid']) ? $payData['mihpayid'] : NULL),
    						"payment_ref_number" => (isset($payData['bank_ref_num']) ? $payData['bank_ref_num'] : NULL ),
    						"remark"             => $payData['field9'],
    						"payment_status"     => ( $approval_type == 1 ||  $approval_type == 2 ? $this->payment_status['success']:$this->payment_status['awaiting']) //status - 0 (pending), will change to 1 after approved at backend
    					); 
    		
    		$payment = $this->jwl_adv_pay_model->updateGatewayResponse($updateData,$id_transaction);
    	     
    		if($payment['status'] == true)
    		{  
    		    $bookingData =  $this->jwl_adv_pay_model->get_ATmailData($id_transaction); 
    			if(isset($bookingData['payment_amount']) && isset($bookingData['metal_weight'])){
    		        $message = "Hi, Thanks for your online advance payment for the Amount Rs.".$bookingData['payment_amount']." with us.You can purchase your Gold Jewellery / Coin & avail discount on MC.";
    		    }
    		    else{
    		        $message = "Hi, Thanks for your online advance payment with us.You can purchase your Gold Jewellery / Coin & avail discount on MC."; 
    		    }
    		    if($this->config->item('sms_gateway') == '1'){
	    		    $this->sms_model->sendSMS_MSG91($this->session->userdata('jap_mobile'),$message);		
	    		}
	    		elseif($this->config->item('sms_gateway') == '2'){
	    	        $this->sms_model->sendSMS_Nettyfish($this->session->userdata('jap_mobile'),$message,'trans');	
	    		}
	    		
                if($bookingData['email'] != ''){
            	    $mailSubject = "Reg- ".$this->comp['company_name']." online advance booking confirmation";
                    $data['mailData'] = $bookingData;
                    $data['company_name'] = $this->comp['company_name'];
                    $data['tollfree1'] = $this->comp['tollfree1'];
            		$to = $bookingData['email'];
            		$message = $this->load->view('jwl_adv_pay/include/mailAck',$data,true);
            		$sendmail = $this->email_model->send_email($to,$mailSubject,$message,'','');
            		//echo $message;
                }
				if($gateway == 2){
					$scheme_success = array("successMsg" => 'Transaction ID: '.$payData['txnid'].' for the amount INR. '.$payData['amount'].' is paid successfully. Thanks for your payment with '.$this->comp['company_name'].'');
				}else{
					$scheme_success = array("successMsg" => 'Payment successful. Thanks for your payment with '.$this->comp['company_name'].'');
				}
    			$this->session->set_flashdata($scheme_success);
    			redirect("/jwl_adv_pay/form");
    		}
    		else
    		{
    			$scheme_success = array("errMsg" => 'Error in updating the database.Please contact administrator at your earliest convenience.');
    			$this->session->set_flashdata($scheme_success);
    			redirect("/jwl_adv_pay/form");
    		} 
	    }
    }
    
    public function payment_failure($payData = "", $gateway = "")
    {
        if($gateway == ""){
            $payData = $_POST;
        }  
        if(!empty($payData['txnid']) && $payData['txnid'] != NULL){
    		 $updateData = array(
    						"bank_name"			 =>	(isset($payData['issuing_bank']) ? $payData['issuing_bank'] : NULL),
    						"payment_mode"       => (isset($payData['mode']) ? $payData['mode'] : NULL),
    						"card_no"			 => (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? $payData['cardnum'] :NULL ),
    						"payu_id"             => (isset($payData['mihpayid']) ? $payData['mihpayid'] : NULL),
    						"remark"             => (isset($payData['field9']) ? $payData['field9'] : NULL),
    						"payment_ref_number" => (isset($payData['bank_ref_num']) ? $payData['bank_ref_num'] : NULL ),
    						"payment_status"     => $this->payment_status['failure']
    					);
    	
    		$payment = $this->jwl_adv_pay_model->updateGatewayResponse($updateData,$payData['txnid']); 
    		$scheme_failure = array("errMsg" => 'Payment failure.Please try again later');
    		$this->session->set_flashdata($scheme_failure);
    		redirect("/jwl_adv_pay/form");
        }else{
            $scheme_success = array("errMsg" => 'Payment failure.Error in updating the database.Please contact administrator for status.');
			$this->session->set_flashdata($scheme_success);
			redirect("/jwl_adv_pay/form");
        }
	}
    
	public function payment_cancel($payData = "", $gateway = "")
    {
        if($gateway == ""){
            $payData = $_POST;
        }  
        
        $updateData = array(
        			"bank_name"			 =>	(isset($payData['issuing_bank']) ? $payData['issuing_bank'] : NULL),
        			"payment_mode"       => (isset($payData['mode']) ? $payData['mode'] : NULL),
        			"card_no"			 => (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? $payData['cardnum'] :NULL ),
        			"payu_id"            => (isset($payData['mihpayid']) ? $payData['mihpayid'] : NULL),
        			"remark"             => (isset($payData['field9']) ? $payData['field9'] : NULL),
        			"payment_ref_number" => (isset($payData['bank_ref_num']) ? $payData['bank_ref_num'] : NULL ),
        			"payment_status"     => $this->payment_status['cancel'] 
        		);
        
        if(!empty($payData['txnid']) && $payData['txnid'] != NULL){
            $payment = $this->jwl_adv_pay_model->updateGatewayResponse($updateData,$payData['txnid']); 
            $scheme_failure = array("errMsg" => 'Payment failure.Please try again later');
            $this->session->set_flashdata($scheme_failure);
        }
        redirect("/jwl_adv_pay/form");
	}
	
	
    function get_branch_by_id($id_branch)
    {		
	    $branch=$this->db->query("SELECT b.name,b.id_branch FROM branch b where b.id_branch=".$id_branch."");
		return $branch->row_array();	
	}
	
	public function get_branch()
	{
	    $data=$this->jwl_adv_pay_model->get_branch();
	    echo json_encode($data);
	}
	
	public function metalrateByBranch()
	{
	    //print_r($_POST);exit;
	    $data=$this->jwl_adv_pay_model->get_currency($_POST['id_branch']); 
	    echo json_encode($data);
	}
	
	public function get_acknowladge($id_jap_payment)
    {
    	$content['history'] = $this->jwl_adv_pay_model->getCustomPurchasePlans($id_jap_payment);
    	$data['content'] = $content;
    	$pageType = array('page' => 'jwl_adv_pay','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone1'=>$this->comp['phone1'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
    	$data['comp_details'] = $this->comp;
    	
    	$this->load->helper(array('dompdf', 'file'));
    	$dompdf = new DOMPDF();
    	$html = $this->load->view('jwl_adv_pay/include/ack_pdf', $data,true);
    	$dompdf->load_html($html);
    	$customPaper = array(0,0,220,400);
    	$dompdf->set_paper($customPaper, "portriat" );
    	$dompdf->render();
    	$dompdf->stream("receipt1.pdf",array('Attachment'=>0));
    }
    
    public function generateOTP($mobile)
	{
		   
	   if(strlen(trim($mobile)) == 10)
	   { 
			$this->session->unset_userdata("jap_OTP");
			$OTP = mt_rand(100000, 999999);
			$this->session->set_userdata('jap_OTP',$OTP);  
			$this->session->set_userdata('jap_mobile',$mobile); 
			$this->session->set_userdata('jap_mob_verified',0); 
			$message = $OTP." is the verification code from ".$this->comp['company_name']." .Please use this code to verify your mobile number";
			if($this->config->item('sms_gateway') == '1'){
			    $this->sms_model->sendSMS_MSG91($mobile,$message);		
			}
			elseif($this->config->item('sms_gateway') == '2'){
		        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
			} 
			echo 1; 
		}
		else
			echo 2;
	}
	
	public function verifyOTP()
	{ 
		$mobile = $this->session->userdata('jap_mobile');
		if(strlen(trim($this->input->post('otp'))) == 6)
		{			 		
			if($this->session->userdata('jap_OTP') == $this->input->post('otp'))
			{	
				$check_mobileno = $this->jwl_adv_pay_model->check_mobileno($mobile);
		  	  	if($check_mobileno['status']){ // Not registered
			  	  if(strlen(trim($mobile)) == 10)
				  {				    
						$this->session->unset_userdata('OTP');
						$this->db->trans_begin();
						$insertData = array(	
											"module_code" 	=> "JAP",
											"mobile" 		=> trim($mobile),
											"verified_otp" 	=> $this->input->post('otp'),  
											"created_on" 	=> date('Y-m-d H:i:s'), 
										) ;
						$this->jwl_adv_pay_model->insert_data($insertData);	
				             
						if($this->db->trans_status() === TRUE)
						{	
							$this->db->trans_commit();
							$this->session->set_userdata('otp',$_POST['otp']);
							$this->session->set_flashdata('successMsg','Verification successful.');
						}else{
							$this->session->set_flashdata('successMsg','Error in verification, please try again later.');
						}	
				   }
		  	  	}else{ // Already Registered
    		  		$this->session->unset_userdata('OTP'); 
    				$res = $check_mobileno['data'];
    				$data = array('jap_mobile'      => $mobile, 
                    				'jap_cus_name'	=> $res['title'].' '.$res['firstname'], 
                    				'email'        	=> $res['email'], 
                    				'alter_mobile'  => $res['alter_mobile'], 
                    				'otp'		    => $_POST['otp']
    				        );
    				        
    			    if($res['id_branch'] != ''){
    				     $branch = $this->jwl_adv_pay_model->get_branch_by_id($res['id_branch']); 
    				     $data['jap_id_branch'] = $res['id_branch'];
    				     $data['branch_name'] = $branch['name'];
    				}  
    				$this->session->set_userdata($data);	    		  	
			  	}	    
				$this->session->unset_userdata('jap_OTP');  
				$this->session->set_userdata('jap_mob_verified',1);
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 0;
		}
	}
	
	

}