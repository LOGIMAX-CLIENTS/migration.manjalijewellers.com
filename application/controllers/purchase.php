<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH.'libraries/payu.php');
require_once (APPPATH.'libraries/techprocess/TransactionRequestBean.php');
require_once (APPPATH.'libraries/techprocess/TransactionResponseBean.php');
require_once(APPPATH.'libraries/hdfc.php');
class Purchase extends CI_Controller {
	const VIEW_FOLDER = 'purchase/';  
		
	public function __construct()
    {
        parent::__construct();
		ini_set('date.timezone', 'Asia/Kolkata');
		
	    $this->load->model('login_model');
        $this->comp = $this->login_model->company_details();
        $this->m_mode=$this->login_model->site_mode();		
		
        if( $this->m_mode['maintenance_mode'] == 1) {
        	$this->maintenance();
	    }
	   	    
		$this->load->model('services_modal');
		$this->load->model('purchase_model');
		$this->load->model('email_model');
		$this->load->model('sms_model'); 
		
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
		if(!$this->session->userdata('purch_mbl'))	{
			redirect('purchase/register_add');
		}else{
			redirect('purchase/p_list');
		} 
	}
	public function register_add()
	{ 		
		if(!$this->session->userdata('purch_mbl'))	{
			$records = $this->purchase_model->empty_record();
			$pageType = array('page' => 'purchase','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mob_no_len'=>$this->comp['mob_no_len'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['content'] = $records;
			//print_r($data);exit;
			$data['fileName'] = self::VIEW_FOLDER.'register';
			$this->load->view('purchase/layout/template', $data);
		}else{
			redirect('purchase/p_list');
		} 
	}
	 
	 
	public function generateOTP($mobile,$type,$cus_name='')
	{
		   
		   if(strlen(trim($mobile)) == 10)
		   { 
				$this->session->unset_userdata("OTP");
				$OTP = mt_rand(100000, 999999);
				$this->session->set_userdata('OTP',$OTP);
				 
				$message = $OTP." is the verification code from ".$this->comp['company_name']." .Please use this code to verify your mobile number"; 
				if($this->config->item('sms_gateway') == '1'){
	    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'','');		
	    		}
	    		elseif($this->config->item('sms_gateway') == '2'){
	    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
	    		}
	    		elseif($this->config->item('sms_gateway') == '3')
				{
            	    $this->sms_model->sendSMS_SpearUC($mobile,$message,'','');	
        	    }

				/*$to = $email;
				$data['type'] = 4;
				$data['name'] = str_replace('%20', ' ', $cus_name);
				$data['otp'] = $OTP;
				$data['company_details']=$this->comp;
				$subject = "Reg: ".$this->comp['company_name']." registration";
				$message = $this->load->view('include/emailAccount',$data,true);
				$this->load->model('email_model');
				$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");*/
			
				echo 1; 
			}
			else
				echo 2;
	}
	public function check_email() 
	{
			echo $this->purchase_model->clientEmail($_POST["email"]);
	}	 
	
	function DB_controller($type,$cus_id='')	
	{		
	  if($type == 'add')
	  {	  
	  	  $check_mobileno = $this->purchase_model->check_mobileno($this->input->post('mobile'));
	  	  if($check_mobileno['status']){ // Not registered
		  	if(strlen(trim($this->input->post('mobile'))) == 10)
			  {					
			  	if($this->session->userdata('OTP') == $this->input->post('otp'))
				{			    
					$this->session->unset_userdata('OTP');
					$this->db->trans_begin();
					$insertData = $this->purchase_model->insert_data();	
			             
						if($this->db->trans_status() === TRUE)
						{
							$this->db->trans_commit();
							$data = array('purch_mbl'       => $_POST['mobile'],  
										  'email'        	=> $_POST['email'], 
										  'otp'		        => $_POST['otp']
										);
									
							$this->session->set_userdata($data);
							$mobile = $data['mobile'];
							$message = "Registered Successfully.";
							if($this->config->item('sms_gateway') == '1'){
				    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'','');		
				    		}
				    		elseif($this->config->item('sms_gateway') == '2'){
				    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
				    		}
				    		elseif($this->config->item('sms_gateway') == '3')
            				{
                        	    $this->sms_model->sendSMS_SpearUC($mobile,$message,'','');	
                    	    }
				    		
							/*if($service['email'] == 1 && $this->input->post('email') != '')
							{
								$to = $this->input->post('email');
								$data['name'] = $this->input->post('firstname');
								$data['mobile'] = $this->input->post('mobile');
								$data['passwd'] = $this->input->post('passwd');
								$data['company_details']=$this->comp;
								$data['type'] = 3;
								$subject = "Reg: ".$this->comp['company_name']." registration";
								$message = $this->load->view('include/emailAccount',$data,true);
								$this->load->model('email_model');
								$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
							}*/
							 
							$this->session->set_flashdata('successMsg','Verification successful.');
							redirect('/purchase/p_list');
						}else{
							echo $this->db->last_query();
							echo $this->db->_error_message();//exit;
							$this->session->set_flashdata('successMsg','Error in verification, please try again later.');
							redirect('/purchase/register_add');
						} 
					}
					else
					{
						$this->session->set_flashdata('errMsg','OTP provided by you is not valid.');
						redirect('/purchase/register_add');
					}
				}
				
		  }else{ // Already Registered
		  	if($this->session->userdata('OTP') == $this->input->post('otp'))
    		  	{
    		  		$this->session->unset_userdata('OTP');
    				$update = $this->purchase_model->update_data('','',$_POST['otp'],$this->input->post('mobile'));	 
    				$res = $check_mobileno['data'];
    				$data = array('purch_mbl'       => $_POST['mobile'], 
    				'purch_cus_name' => $res['title'].' '.$res['firstname'], 
    				// 'email'        	=> $_POST['email'], 
    				'otp'		        => $_POST['otp']
    				);
    
    				$this->session->set_userdata($data);
    				redirect('/purchase/p_list');
    		  	}
    		  	else
    			{
    				$this->session->set_flashdata('errMsg','OTP provided by you is not valid.');
    				redirect('/purchase/register_add');
    			}
		  }
		  
	   }
	   
	}  
	 
	public function logout()
	{
		$this->session->unset_userdata("purch_mbl");
		$this->session->sess_destroy();
		redirect("/purchase/register_add");
	}
	 
	function shortenurl($url)  {
		$ch = curl_init();  
		$timeout = 5;  
		curl_setopt($ch,CURLOPT_URL,'https://tinyurl.com/api-create.php?url='.$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);  
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);  
		$data = curl_exec($ch);  
		curl_close($ch);  
		return $data;  
		
	}
	
	public function p_list()
	{
		if(!$this->session->userdata('purch_mbl'))
		{
			redirect("/purchase/register_add");
		}
		else
		{
            $content['history'] = $this->purchase_model->getCustomPurchasePlans(); 
            $content['denominations'] = array('1000','2000','3000','4000','5000','10000'); 
            $gold = $this->purchase_model->get_currency($this->branch);  
            $content['gold_rate'] = (float) $gold['metal_rates']['goldrate_22ct']; 
            $data['content'] = $content; 
            $pageType = array('page' => 'purchase','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
            $data['header_data'] = $pageType;
            $data['footer_data'] = $pageType;
            $data['fileName'] = self::VIEW_FOLDER.'purchase';
            $this->load->view('purchase/layout/template', $data);
		}
	}	
	
	
		
	public function submitHDFCPay()
	{
		if(!$this->session->userdata('purch_mbl'))
		{
			redirect("/user/login");
		}
		else
		{ 
			$type 				= $_POST['type'];
			$amount 			= ($_POST['type'] == 1 ? (isset($_POST['planA_amt'])?$_POST['planA_amt']:5000) : $_POST['amount']);
			$delivery_pref 		= $_POST['type'] == 1 ? $_POST['delivery_preference']: $_POST['delivery_preference'] ;
			$metal_rate 		= $_POST['rate'];
			$metal_wgt 			= $_POST['weight'];  
			$txnid 				= uniqid(time()); 
			$productinfo		= "Gold Purchase";
		//echo "<pre>";print_r($_POST);exit;
			$this->session->set_userdata('purch_txn_id',$txnid);
            $this->session->set_userdata('purch_amount',($amount));
                
            if($this->session->userdata("purch_cus_name") == ''){
				$update = $this->purchase_model->update_data($_POST['title'],$_POST['firstname'],'',$this->session->userdata("purch_mbl"));	 
	            $cus_name = $_POST['title'].' '.$_POST['firstname'];
	            $this->session->set_userdata('purch_cus_name',$cus_name); 
			}
              
			$insertData = array( 
							"mobile" 	     	 => $this->session->userdata("purch_mbl"),  
							"payment_type" 	     => $this->payment_type,  
							"type" 	     		 => $type,  
							"delivery_preference"=> $delivery_pref,  
							"otp"	 	     	 => $this->session->userdata("otp"),  
							"payment_amount"	 => $amount,
							"actual_trans_amt"   => $amount,
							"date_add"   	 	 => date('Y-m-d H:i:s'),
							"metal_rate"         => $metal_rate,
							"metal_weight"       => ($metal_wgt !='' ? $metal_wgt : 0.000),
							"id_transaction"     => (isset($txnid) ? $txnid.'-1': NULL), 
							"ref_trans_id"       => (isset($txnid) ? $txnid: NULL), 
							"added_by"			 =>  1, 
							"payment_status"     =>  $this->payment_status['pending'], 
							"id_payGateway"      =>  $this->id_payGateway
						 );
			$this->db->trans_begin();
    		$payment = $this->purchase_model->insertData($insertData,'purchase_payment');						// echo $this->db->last_query();exit;
    		if($this->db->trans_status() === TRUE){
    			$this->db->trans_commit();
				$paycred = $this->purchase_model->getBranchGatewayData($this->branch,$this->pg_code);  
				
				$data = array (
								'key' 			=> $paycred['param_1'],
								'txnid' 		=> $txnid,
								'amount' 		=> $amount,
								'firstname' 	=> $this->session->userdata("purch_cus_name"),
								'lastname' 		=> '',
							    'email' 		=> $this->comp['email'],
								'phone' 		=> $this->session->userdata("purch_mbl"),
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
						'firstname' 	=> $this->session->userdata("purch_cus_name"),
						'lastname' 		=> '',
					    'email' 		=> $this->comp['email'],
						'phone' 		=> $this->session->userdata("purch_mbl"),
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
						'curl' 			=> site_url('purchase/payment_cancel'),
						'user_credentials' 	=> $paycred['param_1'].':'.$this->session->userdata("purch_mbl"),
						'hash' 			=> $hash_sequence
					);
				   if($hash_sequence!='' && $txnid !='')
					{ 
						$datas['pay']['hash'] 	=  $hash_sequence;  
						$datas['pay']['curl']    =  $this->config->item('base_url')."index.php/purchase/payment_cancel"; 
						$datas['pay']['furl']    =  $this->config->item('base_url')."index.php/purchase/payment_failure"; 
						$datas['pay']['surl']    =  $this->config->item('base_url')."index.php/purchase/payment_success";
						$datas['pay']['user_credentials'] =  $paycred['param_1'].':'.$this->session->userdata("purch_mbl");
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
			        'redirect_url'  =>  $this->config->item('base_url')."index.php/purchase/responseURL",
			        'language'      => 'EN',
			        'id_payment'    => $payment['insertID'],
			        'firstname' 	=> $this->session->userdata("purch_cus_name"),
			        'lastname' 		=> '',
			        'email' 		=> '',
			        'phone' 		=> $this->session->userdata("purch_mbl"),
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
                    'orderNote'	    => 'Online Money Transaction',
                    'customerName' 	=> $this->session->userdata("purch_cus_name"),
                    'customerEmail' => $this->comp['email'],
                    'customerPhone' => $this->session->userdata("purch_mbl"),
					"returnUrl"     => $this->config->item('base_url')."index.php/purchase/cashfreeresponseURL",	
                     "notifyUrl"     => ''	
                     //"notifyUrl"     => $this->config->item('base_url')."index.php/services/cashfreeStatusNotify/".$this->session->userdata('id_pg')	
                     ); 
                     //print_r($data['cashfreepay']);exit;
                     // get secret key from your config
                    ksort($data['cashfreepay']); 
                    $signatureData = "";
                    foreach ($data['cashfreepay'] as $key => $value){ 
                           $signatureData .= $key.$value;
                      }
					$signature       = hash_hmac('sha256', $signatureData,$secretKey,true);
                    $signature       = base64_encode($signature);
				     //print_r($signature);exit;
					//Generate Encrypted Datas
                    if($signature!='' && $secretKey !='' && $txnid !='')
                    {
                        $data['cashfreepay']['signature']   = $signature; 
                       //print_r($signature);exit;
                        $this->load->view('cashsfree/payment',$data);
                    }
					
				}
			    
			}else{
				echo $this->db->_error_message();
				echo $this->db->last_query();exit;
				$this->db->trans_rollback();
				$scheme_failure = array("errMsg" => 'Unable to proceed payment');
				$this->session->set_flashdata($scheme_failure);
				redirect("/purchase/p_list");
			}
    		 
		}
	} 
	
	// Cash Free
	public function cashfreeresponseURL()
   	{
    	 $paymentgateway = $this->purchase_model->getBranchGatewayData($this->branch,$this->pg_code); 
    	 $secretKey  = $paymentgateway['param_1'];	//secret Key should be provided here.
		 //print_r($secretKey);exit;
    	 
		 $orderId       = $_POST["orderId"];
		 $orderAmount   = $_POST["orderAmount"];
		 $referenceId   = $_POST["referenceId"];
		 $txStatus      = $_POST["txStatus"];
		 $paymentMode   = $_POST["paymentMode"];
		 $txMsg         = $_POST["txMsg"];
		 $txTime        = $_POST["txTime"];
		 $signature     = $_POST["Signature"];
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
        if($orderAmount == $this->session->userdata('purch_amount') && $orderId == $this->session->userdata('purch_txn_id'))
        {
    			$this->session->unset_userdata('purch_amount');
    			$this->session->unset_userdata('purch_txn_id'); 
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
    				$scheme_failure = array("errMsg" => 'Signature Verification failed');
    				$this->session->set_flashdata($scheme_failure);
    				redirect("/purchase/p_list");
    			}
        } 
    	else
    	{
           $scheme_failure = array("errMsg" => 'Signature Verification failed.');
    		$this->session->set_flashdata($scheme_failure);
    		redirect("/purchase/p_list");
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
		$returnURL = ($type == 'web' ? site_url('purchase/'.$rwebURL) : site_url('purchase/'.$rMblURL.'?'.$payData['id_branch'].'&'.$payData['id_pg'])); 		
		
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
			    redirect("/purchase");
            }else{
                $updateData = array(   
                		"remark"            => $response,
                	    "payment_status"    => $this->payment_status['failure']
                	 //status - 0 (pending), will change to 1 after approved at backend
                	);
        		if($payFor == "SS"){
            		$payment = $this->purchase_model->updateGatewayResponse($updateData,$payData['txnid']);
            		redirect("/purchase/failureMURL");
        		}
        		/*elseif($payFor == "G"){
        		    $payment = $this->purchase_model->updateGcardPay($updateData,$payData['txnid']);
        		    redirect("/purchase/gPayResponseMURL/f");
        		}*/
            }
			
        }elseif(is_string($response) && preg_match('/^txn_status=/',$response)){
            if($type == 'web'){
                $msg = array("errMsg" => 'Unable to proceed your request please try again later or contact admin...');
			    $this->session->set_flashdata($msg);
			    redirect("/purchase");
            }else{ 
                $updateData = array(   
                		"remark"            => $response,
                	    "payment_status"    => $this->payment_status['failure']
                	 //status - 0 (pending), will change to 1 after approved at backend
                	);
        		if($payFor == "SS"){
            		$payment = $this->purchase_model->updateGatewayResponse($updateData,$payData['txnid']);
            		redirect("/purchase/failureMURL");
        		}
        		/*elseif($payFor == "G"){
        		    $payment = $this->purchase_model->updateGcardPay($updateData,$payData['txnid']);
        		    redirect("/purchase/gPayResponseMURL/f");
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
			    redirect("/purchase");
            }else{
                $updateData = array(   
                		"remark"            => $response,
                	    "payment_status"    => $this->payment_status['failure']
                	 //status - 0 (pending), will change to 1 after approved at backend
                	);
        		if($payFor == "SS"){
            		$payment = $this->purchase_model->updateGatewayResponse($updateData,$payData['txnid']);
            		redirect("/purchase/payment_failure");
        		}
        		/*elseif($payFor == "G"){
        		    $payment = $this->purchase_model->updateGcardPay($updateData,$payData['txnid']);
        		    redirect("/purchase/gPayResponseMURL/f");
        		}*/
            } 
        }
    }
    
    function techProcessResponseURL($payData = "")
    {
	     
	   /* $mrctCode = $this->payment_gateway[2]['m_code'];  
        $key = $this->payment_gateway[2]['key'];   
	    $iv = $this->payment_gateway[2]['param_1']; */

 		$paymentgateway = $this->purchase_model->getBranchGatewayData($this->branch,$this->pg_code);
	
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
		$payment = $this->purchase_model->updateGatewayResponse($updateData,$txn_id); 
	    
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
			redirect("/purchase/p_list");
		}
		else
		{
			$msg = array("errMsg" => 'Error in updating the database.Please contact administrator at your earliest convenience.');
			$this->session->set_flashdata($msg);
			redirect("/purchase/p_list");
		}
    } 
    // Techprocess Ends
    
    
	// HDFC
    public function responseURL()
    {
		$paymentgateway = $this->purchase_model->getBranchGatewayData(1,2); 
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
			

			
			/*$payment_amount = $this->purchase_model->getpayment_amount($merchant_param4);
		     
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
            
            if($mer_amount == $this->session->userdata('purch_amount') && $txnid == $this->session->userdata('purch_txn_id'))
            {
            			$this->session->unset_userdata('purch_amount');
            			$this->session->unset_userdata('purch_txn_id');
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
            				redirect("/purchase/p_list");
            			}
            }   
			else
			{
				$scheme_failure = array("errMsg" => 'Security Error. Illegal access detected');
				$this->session->set_flashdata($scheme_failure);
				redirect("/purchase/p_list");
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
    		
    		$payment = $this->purchase_model->updateGatewayResponse($updateData,$id_transaction);
    	     
    		if($payment['status'] == true)
    		{  
    			if(isset($payment['payData']['payment_amount']) && $payment['payData']['type'] == 1){
    		        $message = "Hi, Thanks for booking Gold for the Amount Rs.".$payment['payData']['payment_amount']." with us.You can purchase your Jewellery / Coin after lockdown.";
    		    }
    		    elseif(isset($payment['payData']['metal_weight']) && $payment['payData']['type'] == 2){
    		        $message = "Hi, Thanks for booking Gold for the Amount Rs.".$payment['payData']['payment_amount'].", Gold ".$payment['payData']['metal_weight']."g, at Rate ".$payment['payData']['metal_rate']." /g. with us.You can purchase your Jewellery / Coin after lockdown.";
    		    }
    		    else{
    		        $message = "Hi, Thanks for booking Gold with us.You can purchase your Jewellery / Coin after lockdown."; 
    		    }
    		    if($this->config->item('sms_gateway') == '1'){
	    		    $this->sms_model->sendSMS_MSG91($this->session->userdata('purch_mbl'),$message,'','');		
	    		}
	    		elseif($this->config->item('sms_gateway') == '2'){
	    	        $this->sms_model->sendSMS_Nettyfish($this->session->userdata('purch_mbl'),$message,'trans');	
	    		}
	    		elseif($this->config->item('sms_gateway') == '3')
				{
            	    $this->sms_model->sendSMS_SpearUC($this->session->userdata('purch_mbl'),$message,'','');	
        	    }
    			/*if($service['sms'] == 1)
    			{
    				$id=$payment['id_payment'];
    				$data =$this->$serv_model->get_SMS_data($serviceID,$id);
    				$mobile =$data['mobile'];
    				$message = $data['message'];
    				if($this->config->item('sms_gateway') == '1'){
		    		    $this->sms_model->sendSMS_MSG91($mobile,$message);		
		    		}
		    		elseif($this->config->item('sms_gateway') == '2'){
		    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
		    		}
    			}
    			if($service['email'] == 1 && isset($payDatas[0]['email']) && $payDatas[0]['email'] != '')
    			{
    				$to = $payDatas[0]['email'];
    				$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
    				$data['payData'] = $payDatas[0];
    				$data['type'] = 3;
    				$data['company_details'] = $this->comp;
    				$message = $this->load->view('include/emailPayment',$data,true);
    				$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
    			}*/
				if($gateway == 2){
					$scheme_success = array("successMsg" => 'Transaction ID: '.$payData['txnid'].' for the amount INR. '.$payData['amount'].' is paid successfully. Thanks for your payment with '.$this->comp['company_name'].'');
				}else{
					$scheme_success = array("successMsg" => 'Payment successful. Thanks for your payment with '.$this->comp['company_name'].'');
				}
    			$this->session->set_flashdata($scheme_success);
    			redirect("/purchase/p_list");
    		}
    		else
    		{
    			$scheme_success = array("errMsg" => 'Error in updating the database.Please contact administrator at your earliest convenience.');
    			$this->session->set_flashdata($scheme_success);
    			redirect("/purchase/p_list");
    		} 
	    }
    }
    
    public function payment_failure($payData = "", $gateway = "")
    {
        if($gateway == ""){
            $payData = $_POST;
        }  
        if(!empty($payData['txnid']) && $payData['txnid'] != NULL){
	
    	    // $serv_model= self::SERV_MODEL;
    		  
    		 $updateData = array(
    						"bank_name"			 =>	(isset($payData['issuing_bank']) ? $payData['issuing_bank'] : NULL),
    						"payment_mode"       => (isset($payData['mode']) ? $payData['mode'] : NULL),
    						"card_no"			 => (isset($payData['mode']) && ($payData['mode'] == 'CC' || $payData['mode'] == 'DC') ? $payData['cardnum'] :NULL ),
    						"payu_id"             => (isset($payData['mihpayid']) ? $payData['mihpayid'] : NULL),
    						"remark"             => (isset($payData['field9']) ? $payData['field9'] : NULL),
    						"payment_ref_number" => (isset($payData['bank_ref_num']) ? $payData['bank_ref_num'] : NULL ),
    						"payment_status"     => $this->payment_status['failure']
    					);
    	
    		$payment = $this->purchase_model->updateGatewayResponse($updateData,$payData['txnid']); 
    		/*if($service['sms'] == 1)
    		{
    				$id=$payment['id_payment'];
    				$data =$this->$serv_model->get_SMS_data($serviceID,$id);
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
    		$scheme_failure = array("errMsg" => 'Payment failure.Please try again later');
    		$this->session->set_flashdata($scheme_failure);
    		redirect("/purchase/p_list");
        }else{
            $scheme_success = array("errMsg" => 'Payment failure.Error in updating the database.Please contact administrator for status.');
			$this->session->set_flashdata($scheme_success);
			redirect("/purchase/p_list");
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
        
            $payment = $this->purchase_model->updateGatewayResponse($updateData,$payData['txnid']); 
            /*if($service['sms'] == 1)
            {
            		$id=$payment['id_payment'];
            		$data =$this->$serv_model->get_SMS_data($serviceID,$id);
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
            	$data['type'] = -1;
            	$data['company_details'] = $this->comp;
            	$message = $this->load->view('include/emailPayment',$data,true);
            	$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");	
            }*/
            $scheme_failure = array("errMsg" => 'Payment failure.Please try again later');
            $this->session->set_flashdata($scheme_failure);
        }
        redirect("/purchase/p_list");
	}
	
	function terms()
	{
		$pageType = array('page' => 'terms','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content'] = '';
		$data['fileName'] = self::VIEW_FOLDER.'terms';
		$this->load->view('purchase/layout/template', $data);		
	}
		
		  
}