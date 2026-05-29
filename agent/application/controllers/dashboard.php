<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard extends CI_Controller {
	const VIEW_FOLDER = 'pages/';
	const SERV_MODEL  = 'services_modal';
	public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->m_mode=$this->user_model->site_mode();
        if( $this->m_mode['maintenance_mode'] == 1) {
         	redirect("/user/maintenance");
	    }
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		ini_set('date.timezone', 'Asia/Kolkata');
		$this->load->model('email_model');
		$this->load->model('registration_model');
		$this->load->model('dashboard_model');
		$this->load->model('services_modal');
		$this->load->model('sms_model');
		$this->comp = $this->user_model->company_details();
	    $this->sms_data = $this->services_modal->sms_info();	
		$this->sms_chk = $this->services_modal->otp_smsavilable();
    }
    
	public function index()
	{
	    $pageType = array('page' => 'dashboard','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);		
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;

		$data['content'] = array(
							//	'records'			=> $records,
							);
		$data['fileName'] = self::VIEW_FOLDER.'dashboard';
		$this->load->view('layout/template', $data);
	}
	
	
    function getCusLoyaltyTrans()
	{
	    $_POST['id_agent'] = $this->session->userdata("cus_id");
		$resultArr = $this->dashboard_model->getCusLoyaltyTrans($_POST);
		$msg = sizeof($resultArr) > 0 ? "Transactions retrieved successfully" : "No ".$_POST['type']." record found !";
		$result = array("success" => true, "message" => $msg, 'data' => $resultArr);
		echo json_encode($result);
	}
	
	function getAgentReferralsList()
	{
	    $_POST['id_agent'] = $this->session->userdata("cus_id");
		$resultArr = $this->dashboard_model->getAgentReferralsList($_POST);
		$msg = sizeof($resultArr) > 0 ? "Transactions retrieved successfully" : "No ".$_POST['type']." record found !";
		$result = array("success" => true, "message" => $msg, 'data' => $resultArr);
		echo json_encode($result);
	}
	
	function getInfSettledData()
	{
	    $_POST['id_agent'] = $this->session->userdata("cus_id");
		$resultArr = $this->dashboard_model->getInfSettledData($_POST);
		$msg = sizeof($resultArr) > 0 ? "Transactions retrieved successfully" : "No record found !";
		//$result = array("success" => true, "message" => $msg, 'data' => $resultArr, 'query' => $this->db->last_query());
		$result = array("success" => true, "message" => $msg, 'data' => $resultArr);
		echo json_encode($result);
	}
	
	function getInfSetlSummmary()
	{
	    $_POST['id_agent'] = $this->session->userdata("cus_id");
		$resultArr = $this->dashboard_model->getInfSetlSummmary($_POST['id_agent']);
		$msg = sizeof($resultArr) > 0 ? "Summary retrieved successfully" : "No record found !";
		$result = array("success" => true, "message" => $msg, 'data' => $resultArr);
		echo json_encode($result);
	} 
	
	function getInvitedCusList()
	{	
	    $_POST['id_agent'] = $this->session->userdata("cus_id");
		$resultArr = $this->dashboard_model->getInvitedCusList($_POST['id_agent'],$_POST['last_id'],"list",$_POST['from_date'],$_POST['to_date']);
		$msg = sizeof($resultArr) > 0 ? "Records retrieved successfully" : "No referal record found !";
		$result = array("success" => true, "message" => $msg, 'data' => $resultArr);
		echo json_encode($result);
	}
	
	function getDashboard()
    {
        $resultArr = array();
        try 
        {
            $_POST['id_agent'] = $this->session->userdata("cus_id");
            $postData = $_POST;
           // $this->form_validation->set_rules('id_agent','Customer','trim|required|numeric');
            //if ($this->form_validation->run() == TRUE)
            //{	
            	$success = true;
                $message = "Dashboard data retrieved successfully..";
                $cusdata = array();
                $invited_cus = array();
                $earned = $this->dashboard_model->getEarnedPts($postData);
                $referrals = $this->dashboard_model->getAgentReferrals($postData);
                $conversions = $this->dashboard_model->getConversions($postData);
                $unpaid = $this->dashboard_model->getUnpaids($postData);
                $resultArr = array(
                    'referrals' => $referrals, 
                    'conversions'  => $conversions,
                    "tot_earned" 	=> $earned,
                    "unpaid" 	=> $unpaid
                );
            //}
           /* else
            {
                $errArr  = $this->form_validation->error_array();
                $success = false;
                $message = reset($errArr);
            }*/
        }
        catch(Exception $e) 
        {
            $success = false;
            $message = $e->getMessage();
        }
        $result = array("success" => $success, "message" => $message, 'data' => $resultArr);
        echo json_encode($result);
	}
	
	public function generateOTP($mobile,$email="",$name="")
	{
		   if(strlen(trim($mobile)) == 10)
		   {
					$this->session->unset_userdata("OTP");
					$OTP = mt_rand(100000, 999999);
					$this->session->set_userdata('OTP',$OTP);
					$this->session->set_userdata('rate_fixing_otp_exp',time()+10);
					$message = $OTP." is the verification code from ".$this->comp['company_name']." saving scheme.Please use this code to verify your mobile number";
					if($this->config->item('sms_gateway') == '1'){
    		    		$this->sms_model->sendSMS_MSG91($mobile,$message,'','');		
		    		}
		    		elseif($this->config->item('sms_gateway') == '2'){
		    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
		    		}	
		    		if($service['serv_whatsapp'] == 1)
		    		{
                        $this->services_modal->send_whatsApp_message($mobile,$message); 
                    }
				//	$this->send_mail($mobile,$email,$name,$OTP);
					return TRUE;	
			}
			else
			{
			    	return FALSE;
			}
			
	}
    
    public function send_mail($mobile,$email="",$name="",$OTP="")
	{
						 $to = $email;	
						$data['otp']=$OTP;
						$data['type'] = 20;
						$data['company_details'] = $this->comp;
						$data['name'] = $name;
						$subject = "Reg: ".$this->comp['company_name']." Existing OTP Verification";
						$message = $this->load->view('include/emailAccount',$data,true);
						$this->load->model('email_model');
						$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
						return TRUE;
	}
	
    //Promotion sms and otp setting
	function send_sms($mobile,$message)
	{
		$url = $this->sms_data['sms_url'];
		$senderid  = $this->sms_data['sms_sender_id'];
        if(($this->sms_chk['debit_sms']!=0 )){
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
            }
            return FALSE;
        }else{
            return FALSE;
        }
    }
  function update_otp()
  {
		$query_validate=$this->db->query('UPDATE sms_api_settings SET debit_sms = debit_sms - 1 
				WHERE id_sms_api =1 and debit_sms > 0');  			
	         if($query_validate>0)
			{
				return true;
			}else{
				return false;
			}
  }
  
    function update_smscount()
      {
    		$query_validate=$this->db->query('UPDATE sms_api_settings SET debit_sms = debit_sms - 1 
    				WHERE id_sms_api =1 and debit_sms > 0');  			
    	         if($query_validate>0)
    			{
    				return true;
    			}else{
    				return false;
    			}
      }
      
	//GG bank pan and aadhar details  view
	function kyc_form()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
			
			$kyc = $this->scheme_modal->get_kyc_details();
            //print_r($kyc);
			$data = array('kyc' => $kyc);
			
			$pageType = array('page' => 'kyc_form','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);

			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['content'] = $data;
			$data['fileName'] = self::VIEW_FOLDER.'kyc_form';
			$this->load->view('layout/template', $data);
		}
	}
	function kyc_details(){
		$cus_id = $this->session->userdata('cus_id');
		$approval_type = "Auto";
	
		if($_POST['type']=='add'){  
			if($_POST['form_type']==1){
				$bank = $_POST;
				$kyc_detail = array(
					'id_customer'    	 => $cus_id,
					'kyc_type'    	 	 => $bank['form_type'],	
					'number'    	 	 => (isset($bank['bank_acc_no'])?$bank['bank_acc_no']:NULL),
//					'name'    	 		 => (isset($bank['name'])?$bank['name']:NULL),
//					'bank_branch'    	 => (isset($bank['bank_branch'])?$bank['bank_branch']:NULL),
					'bank_ifsc'    		 => (isset($bank['ifsc'])?$bank['ifsc']:NULL),
					'date_add'			 =>  date('Y-m-d H:i:s')
					);
				if($approval_type == "Auto"){
					$response = $this->zoopapiCurl("verify-bank",array("Account"=>$bank['bank_acc_no'],"IFSC"=>$bank['ifsc']));
					if(isset($response->statusCode)){ 
						$data['status'] = FALSE; 
						$data['msg'] = $response->message;
						$data['kyc_status'] = 0 ;
					}
					else{
						if($response->transaction_status == 1){
							if($response->data->Status == "VERIFIED"){
								$kyc_detail['status'] = 2;
								$kyc_detail['name'] = $response->data->BeneName;
								$kyc_detail['verification_type'] = 2; // 1-Manual,2-Auto
								$data = $this->scheme_modal->kyc_insert($kyc_detail); 
								if($data['status'] == TRUE){
									$this->load->model("mobileapi_model");
									$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
									$data['msg'] = "Bank Account verified successfully";
								} 
							}else{
								$data['kyc_status'] = 0 ;
								$data['status'] = FALSE; 
								$data['msg'] = "Invalid Bank Details "; 
							}
						}elseif($response->transaction_status == 2){
							$data['status'] = FALSE; 
							$data['kyc_status'] = 0 ;
							if(isset($response->data->Remark)){
								$data['msg'] = $response->data->Remark; 
							}else{
								$data['msg'] = $response->response_message;
							}
							
						}
						
					}	
				}else{
				    $kyc_detail['verification_type'] = 1; // 1-Manual,2-Auto
					$data = $this->scheme_modal->kyc_insert($kyc_detail);
					if($data['status'] == TRUE){
						$this->load->model("mobileapi_model");
						$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
						$data['msg'] = "Bank Account details submitted successfully. Verification in progress. Keep in touch with us.";
					}else{
						$data['kyc_status'] = 0 ;
						$data['status'] = FALSE; 
						$data['msg'] = "Invalid Bank Details "; 
					}  
				}
			}else if($_POST['form_type']==2){
				$pan = $_POST;
				$kyc_detail = array(
					'id_customer'    	 => $cus_id,
					'kyc_type'    	 	 => $pan['form_type'],	
					'number'    	 	 => (isset($pan['pan_no'])?$pan['pan_no']:NULL),
					'name'    	 		 => (isset($pan['pan_card_name'])?$pan['pan_card_name']:NULL),
					'date_add'			 =>  date('Y-m-d H:i:s')
					);
				if($approval_type == "Auto"){
					$response = $this->zoopapiCurl("pan",array("pan" => $pan['pan_no']));
					if(isset($response->statusCode)){
						$data['status'] = FALSE; 
						$data['msg'] = $response->message;
						$data['kyc_status'] = 0 ;
					}
					elseif($response->response_code == 1){
						$res = $response->data[0];
						if($res->pan_status == "VALID"){
							$kyc_detail['name'] = $res->first_name.' '.$res->last_name;
							$kyc_detail['status'] = 2;
							$kyc_detail['verification_type'] = 2; // 1-Manual,2-Auto
							$data = $this->scheme_modal->kyc_insert($kyc_detail); 
							if($data['status'] == TRUE){
								$this->load->model("mobileapi_model");
								$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
								$data['msg'] = "PAN Card verified successfully";
							} 
						}else{
							$data['kyc_status'] = 0 ;
							$data['status'] = FALSE; 
							$data['msg'] = "Invalid PAN Details "; 
						}
					} 
				}else{
				    $kyc_detail['verification_type'] = 1; // 1-Manual,2-Auto
					$data = $this->scheme_modal->kyc_insert($kyc_detail);
					if($data['status'] == TRUE){
						$this->load->model("mobileapi_model");
						$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
						$data['msg'] = "PAN Card details submitted successfully. Verification in progress. Keep in touch with us.";
					}else{
						$data['kyc_status'] = 0 ;
						$data['status'] = FALSE; 
						$data['msg'] = "Invalid PAN Card Details "; 
					} 
				}
			}else if($_POST['form_type']==3) // Aadhaar
			{
				$aadhar = $_POST; 
				$kyc_detail = array(
					'id_customer'    	 => $cus_id,
					'kyc_type'    	 	 => $aadhar['form_type'],	
					'number'    	 	 => (isset($aadhar['aadhar_number'])?$aadhar['aadhar_number']:NULL),
					'name'    	 		 => (isset($aadhar['aadhar_cardname'])?$aadhar['aadhar_cardname']:NULL),
					'dob'    	 		 => (isset($aadhar['dob'])?date("Y-m-d",strtotime($aadhar['dob'])):NULL),
					'date_add'			 =>  date('Y-m-d H:i:s')
					);  
				if($approval_type == "Auto"){
					$response = $this->zoopapiCurl("extract-aadhaar-data",array( "mode" => "pdf","file" => $aadhar['file'],"password" => $aadhar['password'],"purpose" => 'For Purchase Plan KYC verification',"request_consent" => 'Y'));
					
					if(isset($response->statusCode)){
						$data['status'] = FALSE; 
						$data['msg'] = $response->message;
						$data['kyc_status'] = 0 ;
					}
					elseif($response->transaction_status == 5){
						$res = $response->result->BasicInfo; 
						$date = str_replace('/', '-', $res->DOB);
                        $dob = date('Y-m-d', strtotime($date));
						$kyc_detail['name'] = $res->Name;
						$kyc_detail['number'] = $response->result->AadhaarInfo;
						$kyc_detail['dob'] = $dob;
						$kyc_detail['status'] = 2;
						$kyc_detail['verification_type'] = 2; // 1-Manual,2-Auto
						$data = $this->scheme_modal->kyc_insert($kyc_detail); 
						if($data['status'] == TRUE){
							$this->load->model("mobileapi_model");
							$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
							$data['msg'] = "Aadhaar Details verified successfully";
						} 
					}else{
						$data['kyc_status'] = 0 ;
						$data['status'] = FALSE; 
						$data['msg'] = "There was some issue, please try after sometime .."; 
					}
				}else{
				    $kyc_detail['verification_type'] = 1; // 1-Manual,2-Auto
					$data = $this->scheme_modal->kyc_insert($kyc_detail);
					if($data['status'] == TRUE){
						$this->load->model("mobileapi_model");
						$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
						$data['msg'] = "Aadhaar details submitted successfully. Verification in progress. Keep in touch with us. ";
					}else{
						$data['kyc_status'] = 0 ;
						$data['status'] = FALSE; 
						$data['msg'] = "Invalid Aadhaar Details "; 
					} 
				}
			}
			else if($_POST['form_type'] == 4){ // Driving Licence
			    $dl = $_POST;
				$kyc_detail = array(
					'id_customer'    	 => $cus_id,
					'kyc_type'    	 	 => $dl['form_type'],	
					'number'    	 	 => (isset($dl['dl_number'])?$dl['dl_number']:NULL),
					'dob'    	 		 => (isset($dl['dob'])?date("Y-m-d",strtotime($dl['dob'])):NULL),
					'date_add'			 =>  date('Y-m-d H:i:s')
					);
				if($approval_type == "Auto"){
				    /*$date = date_create($kyc_detail['dob']);
                    $dob = date_format($date,"d-m-Y");*/
                    $postData = array("dl_no" => $kyc_detail['number'],"consent" => "Y" , "consent_text" => "For Purchase Plan KYC verification");
					$response = $this->zoopapiCurl("verify-dl-advance/v2",$postData);
					if(isset($response->statusCode)){
					    $data['kyc_status'] = 0;
						$data['status'] = FALSE; 
						$data['msg'] = $response->message;
					}
					else{
						if($response->transaction_status == 1){
							if($response->response_msg == "Success"){
								$kyc_detail['status'] = 2;
								$kyc_detail['number'] = $response->result->dlNumber;
								$kyc_detail['dob'] = date("Y-m-d",strtotime($response->result->dob));
								$kyc_detail['address'] = $response->result->address[0]->completeAddress;
								$kyc_detail['verification_type'] = 2; // 1-Manual,2-Auto
								$data = $this->scheme_modal->kyc_insert($kyc_detail); 
								if($data['status'] == TRUE){
									$this->load->model("mobileapi_model");
									$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
									$data['msg'] = "Driving Licence verified successfully";
								} 
							}else{
								$data['kyc_status'] = 0;
								$data['status'] = FALSE; 
								$data['msg'] = "Invalid Driving Licence "; 
							}
						}elseif($response->transaction_status == 0){
							$data['kyc_status'] = 0;
							$data['status'] = FALSE; 
							if(isset($response->error_message)){
								$data['msg'] = $response->data->error_message; 
							}else{
								$data['msg'] = $response->response_msg;
							}
						}
						
					}
				}else{
				    $kyc_detail['verification_type'] = 1; // 1-Manual,2-Auto
					$data = $this->scheme_modal->kyc_insert($kyc_detail); 
				//	echo $this->db->_error_message();
					if($data['status'] == TRUE){
						$this->load->model("mobileapi_model");
						$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
						$data['msg'] = "Driving Licence details submitted successfully. Verification in progress. Keep in touch with us. ";
					}else{
						$data['kyc_status'] = 0 ;
						$data['status'] = FALSE; 
						$data['msg'] = "Invalid Driving Licence Details .."; 
					} 
				} 
			}
			if($data['kyc_status'] == 1 && $approval_type == "Auto"){
			  $this->session->set_flashdata('successMsg',$data['msg']);
			}
			$data['approval_type'] = $approval_type;
			echo json_encode($data);
		}
		else{	// EDIT													  
			if($_POST['form_type']==1){
				$bank = $_POST;
				$kyc_detail = array(
				'id_customer'    	 => $cus_id,
				'kyc_type'    	 	 => $bank['form_type'],	
				'number'    	 	 => (isset($bank['bank_acc_no'])?$bank['bank_acc_no']:NULL),
				'name'    	 		 => (isset($bank['name'])?$bank['name']:NULL),
				'bank_branch'    	 => (isset($bank['bank_branch'])?$bank['bank_branch']:NULL),
				'bank_ifsc'    		 => (isset($bank['ifsc'])?$bank['ifsc']:NULL),
				'last_update'		 =>  date('Y-m-d H:i:s'),
				'status'			=> 0
				);
			}else if($_POST['form_type']==2){
			$pan = $_POST;
				$kyc_detail = array(
				'number'    	 	 => (isset($pan['pan_no'])?$pan['pan_no']:NULL),
				'name'    	 		 => (isset($pan['pan_card_name'])?$pan['pan_card_name']:NULL),
				'last_update'		 =>  date('Y-m-d H:i:s'),
				'status'			=> 0
				);
			}else if($_POST['form_type']==3)
			{
				$aadhar = $_POST;
				$kyc_detail = array(	
				'number'    	 	 => (isset($aadhar['aadhar_number'])?$aadhar['aadhar_number']:NULL),
				'name'    	 		 => (isset($aadhar['aadhar_cardname'])?$aadhar['aadhar_cardname']:NULL),
				'dob'    	 		 => (isset($aadhar['dob'])?date("Y-m-d",strtotime($aadhar['dob'])):NULL),
				'last_update'		 =>  date('Y-m-d H:i:s'),
				'status'			=> 0
				);
			}
			else if($_POST['form_type']==4)
			{
				$dl = $_POST;
				$kyc_detail = array(	
				'number'    	 	 => (isset($dl['dl_number'])?$dl['dl_number']:NULL),
				'dob'    	 		 => (isset($dl['dob'])?date("Y-m-d",strtotime($dl['dob'])):NULL),
				'last_update'		 =>  date('Y-m-d H:i:s'),
				'status'			=> 0
				);
			}
			$data=$this->scheme_modal->kyc_update($kyc_detail,$cus_id,$_POST['form_type']);
			echo json_encode($data);
		}
	}
	
	function verify_pan(){
		if($this->config->item('zoop_enabled') == 0){
			$data['status'] = TRUE; 
			$data['msg'] = "PAN verified";
		}else{ 
			$pan = $_POST;
			$response = $this->zoopapiCurl("pan",array("pan" => $pan['pan_no'],"consent" => "Y","consent_text" => "I agree to validate my PAN Number"));
			//$response = $this->zoopapiCurl("pan-lite",array("pan" => $pan['pan_no'],"consent" => "Y","consent_text" => "I agree to validate my PAN Number"));
			if(isset($response->statusCode)){
				$data['status'] = FALSE; 
				$data['msg'] = $response->message;
			}
			elseif($response->response_code == 101){
				$res = $response->data;
				if($res->pan_status == "VALID"){
					$data['status'] = TRUE;
					$data['msg'] = $res->pan_status; 
				}else{
					$data['status'] = FALSE; 
					$data['msg'] = "Invalid PAN Details "; 
				}
			}else{
			   $data['status'] = FALSE; 
					$data['msg'] = "Invalid PAN Number "; 
			} 
		}
		echo json_encode($data);
	}
	
	/**
	* 	ZOOP API CURL Call
	* 	Documentation : https://docs.aadhaarapi.com/?version=latest
	* 
	* @return
	*/
		
	
	function zoopapiCurl($api,$postData){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->config->item('zoop_url')."".$api,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 60,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => json_encode($postData),
		  CURLOPT_HTTPHEADER => array(
		    "Content-Type: application/json",
		    "cache-control: no-cache",
		    "qt_agency_id: ".$this->config->item('agency_id'),
		    "qt_api_key: ".$this->config->item('api_key')
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;exit;
		} else {
		  return json_decode($response);
		}
	}
	
	function get_kyc_details(){
		$kyc = $this->scheme_modal->get_kyc_details();
		echo $kyc;
	}
	
    function random_strings($length)
    {
         $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
         return substr(str_shuffle($str_result), 0, $length);
    }
    
    public function agent_transactions()
	{
	    
		$pageType = array('page' => 'dashboard','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);		
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content'] = array(
								
							);
		$data['fileName'] = self::VIEW_FOLDER.'my_transaction';
		$this->load->view('layout/template', $data);
		
	}
	
	public function conversionList()
	{
		$pageType = array('page' => 'agent_conversions','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
        $data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content'] = '';
		$data['fileName'] = self::VIEW_FOLDER.'agent_conversions';
		$this->load->view('layout/template', $data);
	}
	
	function getConversionData()
	{
	    $_POST['id_agent'] = $this->session->userdata("cus_id");
		$resultArr = $this->dashboard_model->getAllConversion($_POST['id_agent'],$_POST['from_date'],$_POST['to_date']);
		$msg = sizeof($resultArr) > 0 ? "Conversion History retrieved successfully" : "No records found.... !";
		$result = array("success" => true, "message" => $msg, 'conversion' => $resultArr);
		echo json_encode($result);
	} 
	
    public function referralList()
	{
		$pageType = array('page' => 'agent_referrals','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
        $data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content'] = '';
		$data['fileName'] = self::VIEW_FOLDER.'agent_referrals';
		$this->load->view('layout/template', $data);
	}
	
	function getReferralData()
	{
	   
	    $_POST['id_agent'] = $this->session->userdata("cus_id");
		$resultArr = $this->dashboard_model->getAllReferrals($_POST['id_agent'],$_POST['from_date'],$_POST['to_date']);
		$msg = sizeof($resultArr) > 0 ? "Referral History retrieved successfully" : "No records found.... !";
		$result = array("success" => true, "message" => $msg, 'referrals' => $resultArr);
		echo json_encode($result);
	} 
	
	public function unpaidList()
	{
		$pageType = array('page' => 'agent_unpaid','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
        $data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content'] = '';
		$data['fileName'] = self::VIEW_FOLDER.'agent_unpaid';
		$this->load->view('layout/template', $data);
	}
	
	function getUnpaidData()
	{
	   
	    $_POST['id_agent'] = $this->session->userdata("cus_id");
		$resultArr = $this->dashboard_model->getAllUnpaidData($_POST['id_agent'],$_POST['from_date'],$_POST['to_date']);
		$msg = sizeof($resultArr) > 0 ? "Unpaid History retrieved successfully" : "No records found.... !";
		$result = array("success" => true, "message" => $msg, 'unpaid' => $resultArr);
		echo json_encode($result);
	
	} 
	
	public function settlementList()
	{
		$pageType = array('page' => 'agent_settlement','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
        $data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content'] = '';
		$data['fileName'] = self::VIEW_FOLDER.'agent_settlement';
		$this->load->view('layout/template', $data);
	}
	
	function getsettlementData()
	{
	    $_POST['id_agent'] = $this->session->userdata("cus_id");
		$resultArr = $this->dashboard_model->getAllSettlementData($_POST['id_agent'],$_POST['from_date'],$_POST['to_date']);
		$msg = sizeof($resultArr) > 0 ? "Settlements retrieved successfully" : "No record found !";
		$result = array("success" => true, "message" => $msg, 'settlements' => $resultArr);
		echo json_encode($result);
	}
    
    
    
}