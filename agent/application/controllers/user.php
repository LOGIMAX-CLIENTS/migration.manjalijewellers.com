<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User extends CI_Controller {
	const VIEW_FOLDER = 'pages/';
	const API_MODEL   = 'chitapi_model';
	const SERV_MODEL = 'services_modal';
	const EMAIL_MODEL = 'email_model';
	const CUS_IMG_PATH = 'admin/assets/img/customer/';
	const BASIC_FOLDER = 'basicpages/';
	public function __construct()
    {
        parent::__construct();
		ini_set('date.timezone', 'Asia/Kolkata');
	    $this->load->model('user_model');
        $this->comp = $this->user_model->company_details();
        $this->m_mode=$this->user_model->site_mode();
		// referal_code //
		$this->refcode=$this->user_model->__encrypt("ref/".$this->session->userdata("username")."/".md5(uniqid(mt_rand(),true).microtime(true))."");
        $this->url=base_url().'index.php/user/register_add/'.$this->refcode.'';
		// referal_code//
        if( $this->m_mode['maintenance_mode'] == 1) {
        	$this->maintenance();
	    }
		$this->load->model('services_modal');
		$this->load->model('scheme_modal');
		$this->load->model('registration_model');
		//$this->load->model('mobileapi_model');
		$this->load->model('email_model');
		$this->load->model('sms_model');
		$this->scheme_status = $this->scheme_modal->scheme_status();
		$this->sms_data = $this->services_modal->sms_info();
		$this->pro_data = $this->services_modal->promotion_info();
		$this->pro_chk =  $this->services_modal->promotion_smsavilable();
		$this->sms_chk =  $this->services_modal->otp_smsavilable(); 
    }
	public function login()
	{
			$records = $this->user_model->empty_record();
			$data['content'] = $records;
			$pageType = array('page' => 'login','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'tollfree1'=>$this->comp['tollfree1'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER.'login';
			$this->load->view('layout/template', $data);
	   
	}
	public function forget()
	{
		$data['content'] = '';
		$pageType = array('page' => 'forgot','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER.'forget';
		$this->load->view('layout/template', $data);
	}
	public function forgetUser_OTP($mobile)
	{
		$return = $this->user_model->forgetUser($mobile);
		if($return == 1)
		{
			echo 1;
		}
		else
		{
			echo 2;
		}
	}
	public function forgetUser(){
		if($this->session->userdata('OTP') == $this->input->post('otp'))
			{
				$this->session->unset_userdata('OTP');
				$email=$this->input->post('email');
				$mobile=$this->input->post('mobile');
				$data['content'] = array('mble'=>$mobile,'email'=>$email);
				$pageType = array('page' => 'forgot','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
				$data['header_data'] = $pageType;
				$data['footer_data'] = $pageType;
				$data['fileName'] = self::VIEW_FOLDER.'forgot_pswd_reset';
				$this->load->view('layout/template', $data);
			}
		else
			{
				$this->session->set_flashdata('errMsg','OTP provided by you is not valid.');
				redirect('/user/forget');	
			}
	}
	public function forgot_pswd()
	{
	  		$mobile = $this->input->post('mobile');
	  		$email = $this->input->post('email');
				$this->db->trans_begin();
				$insertData = $this->user_model->forgot_pswd_reset($mobile);
				$cus = $this->user_model->customer_data($mobile);
				if($this->db->trans_status() === TRUE)
				{
						$this->db->trans_commit();
						$serviceID = 1;
						$service = $this->services_modal->checkService($serviceID);
						if($service['sms'] == 1)
						{
						 $message="Dear ". $cus['firstname'].", Your password has been changed successfully for ".$this->comp['company_name'].".";
							if($this->config->item('sms_gateway') == '1'){
								$this->sms_model->sendSMS_MSG91($mobile,$message,'','');		
							}
							elseif($this->config->item('sms_gateway') == '2'){
								$this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
							}
						}
						if($service['serv_whatsapp'] == 1)
						{
            	            $this->services_modal->send_whatsApp_message($mobile,$message); 
                        }
					if($service['email'] == 1 && $email != '')
						{
							$to =$email;
							$data['type'] = 2;
							$data['company_details'] = $this->comp;
							$data['name'] = $cus['firstname'];
							$subject = "Reg: ".$this->comp['company_name']." purchase plan forgot password";
							$message = $this->load->view('include/emailAccount',$data,true);
							$this->load->model('email_model');
							$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
						}
					$this->db->trans_commit();
					$this->session->set_flashdata('successMsg','Now you can login with your new password.');
					redirect('/user/login');
				}
				else
				{
						$serviceID = 10;
						$service = $this->services_modal->checkService($serviceID);
						if($service['sms'] == 1)
						{
						$id=$mobile;
						$data =$this->$serv_model->get_SMS_data($serviceID,$id);
						if($this->config->item('sms_gateway') == '1'){
			    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
			    		}
			    		elseif($this->config->item('sms_gateway') == '2'){
			    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
			    		}
						}
						if($service['serv_whatsapp'] == 1)
			    		{
                            $this->services_modal->send_whatsApp_message($mobile,$message); 
                        }
						if($service['email'] == 1 && $email != '')
						{
							$to = $email;
							$data['name'] = $cus['firstname'];
							$data['type'] = -3;
							$data['company_details'] = $this->comp;
							$subject = "Reg: ".$this->comp['company_name']." purchase plan reset password";
							$message = $this->load->view('include/emailAccount',$data,true);
							$this->load->model('email_model');
							$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
						}
						$this->db->trans_rollback();
						$this->session->set_flashdata('errMsg','Error in reset password.Please try again later...');
				}
			redirect('/user/login');
	}
	public function validateUser()
	{
		$return = $this->user_model->validateUser();
			//print_r($return);exit;
		if($return['status'] == 1)
		{
    		$branch_set = $this->user_model->branch_settings();
    		$ref = $this->services_modal->get_data(); 
    	//	$cus_data= $this->scheme_modal->get_cusname($return['cus_id']); //customer name by default in account name while joining scheme//hh 
			$data = array('username'        => $_POST['username'],
			              'mobile'          => $_POST['username'],
						  'display_name'    => $return['display_name'], 
						//'cus_name'        =>$cus_data['name'],
						  'cus_id'          => $return['cus_id'],
						  'is_logged_in'    => true,
						  'allow_referral'  => $ref['allow_referral'],
						  'is_kyc_required' => $branch_set['is_agent_kyc_required'],
						  'branch_settings'	=> $branch_set['branch_settings'],
						  'id_branch'	    => $return['id_branch'],
						  'branch_name'	    => $return['branch_name'],
						  'cost_center'	    => $branch_set['cost_center'],
						  'branchwise_scheme'    => $branch_set['branchwise_scheme'],
						  'is_branchwise_cus_reg'=> $branch_set['is_branchwise_cus_reg'],
						  'branchWiseLogin'	     => $branch_set['branchWiseLogin'],
						  'reference_no'    => $return['reference_no'],
						  'kyc_status'     => $return['kyc_status'],
						  'kyc_count'      => $return['kyc_count']
					);
				
			$this->session->set_userdata($data);
			if($this->input->post('remember_me'))
			{
				$data['new_expiration'] = 60*60*24*30;//30 days
        		$this->session->sess_expiration = $data['new_expiration'];
				$this->session->set_userdata($data);
			}
		
			//print_r($return);exit;
			if($branch_set['is_agent_kyc_required'] == 1){
			    if($return['kyc_status'] == 1 && $return['kyc_count'] == 3){
			        redirect("/dashboard");
			    }else if($return['kyc_count'] == 3 && $return['kyc_status'] == 0)
			    {
			        redirect("/user/kyc_msg");
			    }
			    else{
    			    redirect("/user/kyc_form");
    			}
			}else{
			    redirect("/dashboard");
			}
			
		}
		else {
		    $notActive = $this->user_model->validateActiveUser();
    		if($notActive)
    		{
    			$this->session->set_flashdata('errMsg','Your account is not yet activated!');
    			redirect("/user/login");
    		}
		}
		$this->session->set_flashdata('errMsg','Invalid username or password');
		redirect("/user/login");
	}
	public function logout()
	{
		$this->session->unset_userdata("username");
		$this->session->unset_userdata("vs_mobile");
		$this->session->sess_destroy();
		redirect("/user/login");
	}
	public function register_add()
	{ 			
		$serv_model=self::SERV_MODEL;
		$limit= $this->$serv_model->limitDB('get','1');
		$count= $this->$serv_model->customer_count();
		if($limit['limit_cust']==1)
		{			
			if($count < $limit['cust_max_count'])
			{			
				$records = $this->registration_model->empty_record();
				$pageType = array('page' => 'signup','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mob_no_len'=>$this->comp['mob_no_len'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
				$data['header_data'] = $pageType;
				$data['footer_data'] = $pageType;
				$data['content'] = $records;				
				$data['fileName'] = self::VIEW_FOLDER.'signup';				
				$this->load->view('layout/template', $data);				
			}else
			{									 	
			 	$this->session->set_flashdata('errMsg','Temporarily New user registration service unavailable, Kindly contact Customer care...');
			 	redirect('/user/login');		 	
			}			
		}else
		{
			$records = $this->registration_model->empty_record();
			$pageType = array('page' => 'signup','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mob_no_len'=>$this->comp['mob_no_len'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['content'] = $records;
			//print_r($data);exit;
			$data['fileName'] = self::VIEW_FOLDER.'signup';
			$this->load->view('layout/template', $data);
		} 
	}
	public function register_update()
	{
	  	if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
			$profileData = $this->registration_model->get_entryRecord();
			//print_r($profileData);
			$data['content'] = $profileData;
			$pageType = array('page' => 'profile','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER.'profile';
			$this->load->view('layout/template', $data);
		}
	}
	public function reset_passwd()
	{
	   	if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
			$data['content'] = '';
			$pageType = array('page' => 'reset_pass','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER.'reset_passwd';
			$this->load->view('layout/template', $data);
		}
	}
	public function resetPass_submit()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
		if($this->registration_model->reset_passwd())
			{
				$this->session->set_flashdata('successMsg','Your password changed successfully...');
				redirect("/dashboard");
			}
			else
			{
				$this->session->set_flashdata('errMsg','Error in changing your password.Please try again later');
				redirect("/user/reset_passwd");
			}
		}
	}
	public function change_mobile()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
			$data['content'] = '';
			$pageType = array('page' => 'changeUser','scheme_status' =>  $this->scheme_status,'currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER.'change_mobile';
			$this->load->view('layout/template', $data);
		}
	}
	public function update_mobile()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
		  if($this->session->userdata('OTP') == $this->input->post('otp'))
		  {
			  $mobile = trim($this->input->post('mobile'));
			  if(strlen($mobile) == 10)
			  {
					if($this->registration_model->update_mobile($mobile))
					{
						$data = array('username' => $mobile
								   );
						$this->session->set_userdata($data);
						$this->session->set_flashdata('successMsg','Your Mobile No. changed successfully...');
						redirect("/dashboard");
					}
					else
					{
						$this->session->set_flashdata('errMsg','Error in changing your Mobile No.Please try again later');
						redirect("/user/change_mobile");
					}
			   }
			   else
			   {
					$this->session->set_flashdata('errMsg','Mobile Number should be exactly 10 digits.');
					redirect("/user/change_mobile");
			   }
			}
			else
			{
				$this->session->set_flashdata('errMsg','Not a valid OTP.Please try again.');
				redirect("/user/change_mobile");
			}
		}
	}
	public function generateOTP($mobile,$type,$email,$cus_name='')
	{
		   if(strlen(trim($mobile)) == 10)
		   {
				$check_mobileno = $this->registration_model->check_mobileno($mobile);
				if($check_mobileno)
				{
					$this->session->unset_userdata("OTP");
					$OTP = mt_rand(100000, 999999);
					$this->session->set_userdata('OTP',$OTP);
					if($type == 0)
					{
						 $message="Your OTP for  ".$this->comp['company_name']."  enrollment is :  ".$OTP." ";
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
						 }
					else{
						 $message = $OTP." is the verification code from ".$this->comp['company_name']." .Please use this code to verify your mobile number";
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
						}
						/*if($this->config->item('sms_gateway') == '1'){
			    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'','');		
			    		}
			    		elseif($this->config->item('sms_gateway') == '2'){
			    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
			    		}*/
						$to = $email;
						$data['type'] = 4;
						$data['name'] = str_replace('%20', ' ', $cus_name);
						$data['otp'] = $OTP;
						$data['company_details']=$this->comp;
						$subject = "Reg: ".$this->comp['company_name']." registration";
						$message = $this->load->view('include/emailAccount',$data,true);
						$this->load->model('email_model');
						$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
					echo 1;
				}
				else
					echo 0;
			}
			else
				echo 2;
	}
	public function check_email() 
	{
			echo $this->registration_model->clientEmail($_POST["email"]);
	}	
	public function get_scheme()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
			echo $this->registration_model->get_scheme();
		}
	}
	public function get_country()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
			$data= $this->registration_model->get_country();
			  echo $data;
		}
	}
	public function get_country_list()
	{
			$data= $this->registration_model->get_country();
			echo $data;
	}
/*	public function get_state($id)
	{
		echo $this->registration_model->get_state($id);
	}*/
	 public function get_state($id)
	{
	if($this->config->item('custom_fields')['country']==0) 
	{
		echo $this->registration_model->get_state(101);
	}
	else{
	    echo $this->registration_model->get_state($id);
	}
	}
	public function get_city($id)
	{
		echo $this->registration_model->get_city($id);
	}
	function DB_controller($type,$cus_id='')	
	{		
	 /* if($type == 'add')
	  {	  
	  if(strlen(trim($this->input->post('mobile'))) == 10)
	  {			
		$serv_model=self::SERV_MODEL;			
	  	if($this->session->userdata('OTP') == $this->input->post('otp'))
		{			    
			$this->session->unset_userdata('OTP');
			$this->db->trans_begin();
			$refcode=$this->input->post('referal_code');
				if($refcode!=''){				    
				    $status= $this->checkreferalcode();
				    if($status['status']==1 && $status['referal_code']!='NULL'){
            		   $insertData = $this->registration_model->insert_data($status['referal_code']);
            		}else if($status['status']=='0' && $status['referal_code']=='NULL'){
            		    $this->db->trans_rollback();
						$this->session->set_flashdata('errMsg','your registration referral code  Not availabe there!...Please use another referral code...');
						redirect('/user/register_add');
            		}
				}else{
        		   $insertData = $this->registration_model->insert_data();
        		   if($insertData){        		   	   
        		       $wallet_acc =  $this->registration_model->wallet_accno_generator(); 
					   if($wallet_acc['wallet_account_type']==1){
						  $this->wallet_account_create($insertData['insertID'],$this->input->post('mobile'));
					   }
        		   } 
	            }	
	            
	            //print_r($insertData['status']);exit;
	            //Sync Existing Data
	            if($insertData['status']){	
	               if($this->config->item("integrationType") == 5){ // Do customer registration in offline
	                   $this->load->model("integration_model");
                       $postData = array(
                						"appCustomerCode"	=> 0 ,
                						"custName" 			=> ucfirst($this->input->post('firstname')),
                						"mobileNo" 			=> trim($this->input->post('mobile')),
                						"emailId" 			=> $this->input->post('email'),
                						"preferdBranch" 	=> "",
                						"branchCode" 		=> "",
                						"custdetails" 		=> array("city" => "Coimbatore" , "state" => "Tamil Nadu")
                					);
	                   $response = $this->integration_model->khimji_curl('registerCustomerWithoutValidateOtp',$postData);
	                   if($response['status'] == 1){
		                   	$resData = $response['data'];
		                   	if($resData->status == 1 && $resData->errorCode == 0 && $resData->result[0]->mobileNo == $this->input->post('mobile')){
		                       $updCus = array("reference_no" => $resData->result[0]->customerCode, "date_upd" => date('Y-m-d H:i:s'));
		                       $this->integration_model->updateData($updCus,'mobile',$resData->result[0]->mobileNo,'customer');		
		                       $this->session->set_userdata("reference_no",$resData->result[0]->customerCode);
		                    }else{
								if (!file_exists('log/khimji')) {
					                mkdir('log/khimji', 0777, true);
					            }
					            $log_path = 'log/khimji/'.date("Y-m-d").'.txt';  
					            $logData = "\n".date('d-m-Y H:i:s')."\n API : registerCustomerWithoutValidateOtp \n Response : ".json_encode($resData,true);
							    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
							}
	                   }
	               }
    		   	   else if($this->config->item("integrationType") == 3 || $this->config->item("autoSyncExisting") == 1){
					  $syncData = $this->sync_existing_data($this->input->post('mobile'),$insertData['insertID'],$this->input->post('id_branch'),'SignUp');
				   }
				}
				if($this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					$serviceID = 1;
					$service = $this->services_modal->checkService($serviceID);
					if($service['sms'] == 1)
					{
						$id=$insertData['insertID'];
						$data =$this->$serv_model->get_SMS_data($serviceID,$id);
						$mobile =$data['mobile'];
						$message = $data['message'];
						if($this->config->item('sms_gateway') == '1'){
			    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
			    		}
			    		elseif($this->config->item('sms_gateway') == '2'){
			    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
			    		} 
					}
					if($service['serv_whatsapp'] == 1)
					{
                        	$this->services_modal->send_whatsApp_message($mobile,$message); 
                    }
					if($service['email'] == 1 && $this->input->post('email') != '')
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
					}
					$this->session->set_flashdata('successMsg','Registration successful.');
					redirect('/user/login');
				}else{
					echo $this->db->last_query();
					echo $this->db->_error_message();//exit;
					$this->session->set_flashdata('successMsg','Error in registration, please contact admin.');
				}
			/*	else
				{
						$serviceID = 10;
						$service = $this->services_modal->checkService($serviceID);
						if($service['sms'] == 1)
						{
						$id=$this->input->post('mobile');
						$data =$this->$serv_model->get_SMS_data($serviceID,$id);
						$mobile=$id;
						if($this->config->item('sms_gateway') == '1'){
    		    $this->sms_model->sendSMS_MSG91($mobile,$message);		
    		}
    		elseif($this->config->item('sms_gateway') == '2'){
    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
    		}
						}
						if($service['email'] == 1 && $this->input->post('email') != '')
						{
							$to = $this->input->post('email');
							$data['firstname'] = $this->input->post('firstname');
							$data['mobile'] = $this->input->post('mobile');
							$data['passwd'] = $this->input->post('passwd');
							$data['type'] = -1;
							$data['company_details']=$this->comp;
							$subject = "Reg: ".$this->comp['company_name']." purchase plan registration";
							$message = $this->load->view('include/emailAccount',$data,true);
							$this->load->model('email_model');
							$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
						}
						$this->db->trans_rollback();
						$this->session->set_flashdata('errMsg','Error in registration.Please try again later...');
				}
			}
			else
			{
				$this->session->set_flashdata('errMsg','OTP provided by you is not valid.');
			}
		}
			redirect('/user/register_add');
	   }   */ 
	   
    if($type == 'add'){	  
	    if(strlen(trim($this->input->post('mobile'))) == 10){			
		    $serv_model=self::SERV_MODEL;			
    	  	if($this->session->userdata('OTP') == $this->input->post('otp'))
    		{			    
    			$this->session->unset_userdata('OTP');
    			$this->db->trans_begin();
            	$insertData = $this->registration_model->insert_data();
			
			    if($insertData['status'] == 1){
    				$this->db->trans_commit();
    				$serviceID = 1;
    				$service = $this->services_modal->checkService($serviceID);
    				if($service['sms'] == 1){
    					$id=$insertData['insertID'];
    					$data =$this->$serv_model->get_SMS_data($serviceID,$id);
    					$mobile =$data['mobile'];
    					$message = $data['message'];
    					if($this->config->item('sms_gateway') == '1'){
    						$this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
    			    	}
    			    	elseif($this->config->item('sms_gateway') == '2'){
    			    	    $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
    			    	} 
    				}
    				
    				if($service['serv_whatsapp'] == 1)
    				{
                        $this->services_modal->send_whatsApp_message($mobile,$message); 
                    }
                    
    				if($service['email'] == 1 && $this->input->post('email') != '')
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
				    }
				    
        		$this->session->set_flashdata('successMsg','Registration successful.');
        		redirect("/user/login");
        	    }else{
        			echo $this->db->last_query();
        			echo $this->db->_error_message();//exit;
        			$this->session->set_flashdata('successMsg','Error in registration, please contact admin.');
        		}
    	    }
        	else
        	{
        		$this->session->set_flashdata('errMsg','OTP provided by you is not valid.');
        	}
        }
    	redirect('/user/register_add');
    	
    }else if($type == 'update') {
			if(!$this->session->userdata('username'))
			{
				redirect("/user/login");
			}
			else
			{
				$this->load->library('upload');
				$this->db->trans_begin();
				$insertData = $this->registration_model->update_data($cus_id);
				if($this->db->trans_status() === TRUE)
				{
					$this->db->trans_commit();
					if($this->session->userdata('pan_msg') == 2)
					{
						$this->session->unset_userdata("pan_msg");
						$this->session->set_flashdata('successMsg','Pan No. updated successfully. Please proceed for payment.');
						redirect("/paymt");
					}
					else
					{
						$this->session->set_flashdata('successMsg','Your profile has been updated successfully');
					}
				}
				else
				{
					$this->db->trans_rollback();
					$this->session->set_flashdata('errMsg','Error in updation.Please try again later...');
				}
				redirect('/user/register_update');
			}
	    }
	}
	function exitingCustomer()
	{
		$schemes = $this->scheme_modal->no_avail_schemes();
			$pageType = array('page' => 'cust_enquiry','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content'] = $schemes;
		$data['fileName'] = self::VIEW_FOLDER.'cust_enquiry';
		$this->load->view('layout/template', $data);		
	}
	function exitingCusRegister()
	{
	 	session_start();
		 if(strlen(trim($this->input->post('mobile'))) == 10)
			{
			 //  if($this->input->post('scheme_code') != -1 || trim($this->input->post('acc1')) != '' || trim($this->input->post('acc2')) != '' || trim($this->input->post('acc3')) != '' || trim($this->input->post('acc4')) != '' || trim($this->input->post('acc5')) != '' || trim($this->input->post('acc6')) != '') 
			   if($this->input->post('scheme_code') != -1) 
			   {
				if($this->session->userdata('OTP') == $this->input->post('otp'))
				{
					$this->session->unset_userdata('OTP');
					$this->db->trans_begin();
					$insertData = $this->registration_model->exitingCusRegister();
					if($this->db->trans_status() === TRUE)
					{
						    //$this->insert_Registration($insertData['acc_id']);
							$this->db->trans_commit();
							$serviceID = 1;
							$service = $this->services_modal->checkService($serviceID);
							if($service['sms'] == 1)
							{
								$mobile = $this->input->post('mobile');
								 $data['type']=4;
								 $data['name']=$this->input->post('firstname');
								 $data['comp_name']=$this->comp['company_name'];
								 $message = $this->load->view('include/smsUser',$data,true);
									$senderid = $this->config->item('sms_senderid');
								$arr = array("@customer_mobile@" => $mobile,"@message@" => $message,"@senderid@" => $senderid);
								$url = $this->config->item('sms_url');
								$user_sms_url = strtr($url,$arr);
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $user_sms_url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
								curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
								$result = curl_exec($ch);
								curl_close($ch);
								unset($ch);
							}
							if($service['email'] == 1 && $this->input->post('email') != '')
							{
								$to = $this->input->post('email');
								$data['firstname'] = $this->input->post('firstname');
								$data['mobile'] = $this->input->post('mobile');
								$data['passwd'] = $this->input->post('passwd');
								$data['type'] = 1;
								$data['company_details']=$this->comp;
								$subject = "Reg:".$this->comp['company_name']." online purchase plan activation";
								$message = $this->load->view('include/emailAccount',$data,true);
								$this->load->model('email_model');
								$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
							}
						$this->db->trans_commit();
						$this->session->set_flashdata('successMsg','Online purchase plan account has been activated. Please login and pay your installments.');
						redirect('/user/login');
					}
					else
					{
							$serviceID = 1;
							$service = $this->services_modal->checkService($serviceID);
							if($service['sms'] == 1)
							{
								$mobile = $this->input->post('mobile');
								 $data['type']=-4;
								 $data['name']=$this->input->post('firstname');
								 $data['comp_name']=$this->comp['company_name'];
								 $message = $this->load->view('include/smsUser',$data,true);
									$senderid = $this->config->item('sms_senderid');
								$arr = array("@customer_mobile@" => $mobile,"@message@" => $message,"@senderid@" => $senderid);
								$url = $this->config->item('sms_url');
								$user_sms_url = strtr($url,$arr);
								$ch = curl_init();
								curl_setopt($ch, CURLOPT_URL, $user_sms_url);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
								curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
								curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
								$result = curl_exec($ch);
								curl_close($ch);
								unset($ch);
							}
							if($service['email'] == 1 && $this->input->post('email') != '')
							{
								$to = $this->input->post('email');
								$data['firstname'] = $this->input->post('firstname');
								$data['mobile'] = $this->input->post('mobile');
								$data['passwd'] = $this->input->post('passwd');
								$data['type'] = -1;
								$data['company_details']=$this->comp;
								$subject = "Reg: ".$this->comp['company_name']." online purchase plan account activation";
								$message = $this->load->view('include/emailAccount',$data,true);
								$this->load->model('email_model');
								$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
							}
							$this->db->trans_rollback();
							$this->session->set_flashdata('errMsg','Error in creating an account.Please try again later...');
					}
				}
				else
				{
					$this->session->set_flashdata('errMsg','OTP provided by you is not valid.');
				}
			   }
			   else
			   {
			   		$this->session->set_flashdata('errMsg','Please fill all required fields.');
			   }
			}
				redirect('/user/exitingCustomer');
	}
	function check_captcha()
	{
		session_start();
		if($_POST['captcha_answer']!=$_SESSION['6_letters_code'])
		  {
			$captcha_flag=0;		
			//redirect("C_userregistration/open_entryform/userregistration_model/add_new/0/Invalid Captcha");
		  }
		 else
		{
			$captcha_flag=1;
		}
		echo $captcha_flag;
	}
	function contactSubmit()
	{
		session_start();
		if($_POST['captchaAns'] == $_SESSION['captchaContact'])
		{
		    $id_customer = $this->session->userdata('cus_id');   
	        $this->load->model('mobileapi_model');
		    $data = $_POST;
		    $title = ($data['reg'] == 1 ? 'Enquiry' : ($data['reg'] == 2 ?'Suggestion' : ($data['reg'] == 3 ? 'Complaint':'Others' )) );
            $insData = array('mobile'       => $data['custMobile'],
                             'name'         => $data['custName'],
                             'title'  	    => $title,
                             'id_customer'  => ($id_customer != NULL ?$id_customer:NULL),
                             'address'      => (isset($data['address'])?$data['address']:NULL),
                             'date_of_birth'=> (isset($data['date_of_birth'])?$data['date_of_birth']:NULL),
                             'date_of_wed'  => (isset($data['date_of_wed'])?$data['date_of_wed']:NULL),
                             'profession'   => (isset($data['profession'])?$data['profession']:NULL),
                             'email'  	    => (isset($data['custEmail'])?$data['custEmail']:NULL),
                             'date_add'     => date('Y-m-d H:i:s'),
                             'type'         => $data['reg'], // Feedback type
                             'enq_from'     => 1, // web app
                             'comments'  	=> $data['custMessage']);
            if($data['reg'] == 3 ){
                $ticketno = $this->mobileapi_model->genTicketNo();
                $insData['ticket_no'] = $ticketno;
            } 
            $result = $this->mobileapi_model->insCusFeedback($insData); 
            if($result['status'] == true){
                $to = $this->comp['email']; 
               // $cc = array("pavithra@vikashinfosolutions.com");	
               $cc="";
                $subject = "Reg - ".$this->comp['company_name']." customer enquiry"; 
                $message = $this->load->view('include/emailContact',$insData,true); 
                $sendEmail = $this->email_model->send_email($to,$subject,$message,$cc,"",""); 
                if($sendEmail)
				echo 1;
			    else
				echo 2; 
            } else {
                echo 2;
            } 
		}
		else
		{
			echo 0;
		}
	}
	
	function send_sms($mobile,$message,$data="")
	{	
		if($data==1){
			$url = $this->pro_data['promotion_url'];
			$senderid  = $this->pro_data['promotion_sender_id'];
			}else{
				$url = $this->sms_data['sms_url'];
				$senderid  = $this->sms_data['sms_sender_id'];
			 }
	if(($this->pro_chk['debit_promotion']!=0 && $data==1)) 
	{
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
		$status=$this->update_prosms();		
		if($status==1){		
		return TRUE;}
		return FALSE;
	}
	else if(($this->sms_chk['debit_sms']!=0)){
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
///otp debit_sms count
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
  function update_prosms()
  {
		$query_validate=$this->db->query('UPDATE promotion_api_settings SET debit_promotion = debit_promotion - 1 WHERE id_promotion_api =1 and debit_promotion > 0');  			
	         if($query_validate>0)
			{
				return true;
			}else{
				return false;
			}
  }
///otp debit_sms count
	function maintenance()
	{
		if($this->m_mode['maintenance_mode'] == 1) {
			$pageType = array('page' => 'terms','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['m_text'] = $this->m_mode['maintenance_text'];
			$this->load->view('maintenance',$data);
		}
		else
		{
			redirect('dashboard');
		}
	}
	function remove_cus_img($file) {
		$id = $this->session->userdata('cus_id');
		$path = self::CUS_IMG_PATH.$id."/".$file.".jpg" ;
			chmod(self::CUS_IMG_PATH,0777);
	         unlink($path);
	         echo "Picture removed successfully";
	}
	function insertpan(){
		$panNo = array("pan" => $this->input->post('pan'));
		$data = $this->registration_model->updateCustomer($panNo);
		if($data ==1)
		{
				$this->session->set_flashdata('successMsg','Please proceed for the payment.');
				redirect("/paymt");
		}
		else
		{
				$this->session->set_flashdata('errMsg','Unable to proceed your request.Please try after sometime..');
				redirect("/paymt");
		}
	}
	
	public function aboutus()
	{
		$data['content'] = '';
		$pageType = array('page' => 'aboutus','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER.'aboutus';
		$this->load->view('layout/template', $data);
	}
	
   function referral_linksend(){
		$mobile=$this->input->post('mobile');
		$email=$this->input->post('email');
	    $serv_model=self::SERV_MODEL;
	    $email_model=self::EMAIL_MODEL;
	    $serviceID = 15;
	    // promotion sms //
	    $promotion=1;
				 if($mobile!='')  
				{	
					$id=$this->input->post('mobile');
					$data =$this->$serv_model->get_SMS_data($serviceID,$id,$serv_email,$serv_sms);
					$message=str_replace('##',$this->url,$data['message']);
					if($data['serv_sms'] == 1)	{
				//	$status= $this->send_sms($mobile,$message,$promotion);	
				    if($this->config->item('sms_gateway') == '1'){
					    		  $status=  $this->sms_model->sendSMS_MSG91($mobile,$message,$promotion,'');		
					    		}
					    		elseif($this->config->item('sms_gateway') == '2'){
					    	      $status=  $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
					    		}
					}
					if($service['serv_whatsapp'] == 1)
					{
                    	$this->services_modal->send_whatsApp_message($mobile,$message); 
                    }
				}	
			  if($email != '')
			 {			
			     $data['type'] =15;
				 $data['company_details'] = $this->comp;
				 $mbi=$this->input->post('mobile');
				 $id=($mbi!=''?$mbi:$this->session->userdata("username")); 
				 $data['referral'] = $id;				 
				 $data['name'] = $id; 				 
		         $datas =$this->$serv_model->get_SMS_data($serviceID,$id);
				 $data['message']=str_replace('##',$this->url,$datas['message']);			 
				 $subject = "Reg: ".$this->comp['company_name']." Invite Referrals ";
				 $message = $this->load->view('include/emailAccount',$data,true);
			     $status = $this->$email_model->send_email($email,$subject,$message,"",""); 
			} 
		   if($status){
				echo json_encode(array('status'=>true));
			}else{
				echo json_encode(array('status'=>false));
			}
       }
// referalcode verification //
	 public function checkreferalcode()
	{
	 $referalcode = $this->registration_model->__decrypt($this->input->post('referal_code'));
		$referal_code = explode('/',$referalcode);
		$ref_code = explode('-',$referal_code[1]);
     	if($ref_code[0]!=''){
				$available = $this->scheme_modal->checkreferral_code($ref_code[0]);
			}else{				
				array('status'=>'0','referal_code'=>'NULL');
			}
		if($available){
			return array('status'=>TRUE,'referal_code'=>$ref_code[0]);	
		}else{
			return array('status'=>'0','referal_code'=>'NULL');
		}
	}
	function refer_earn(){
	    $pageType = array('page' => 'refer_earn','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'tollfree1'=>$this->comp['tollfree1'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
        $isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));	
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['page_content']=$this->user_model->pageContent();
        $data['agent'] = $this->user_model->agent_details(); 
        $data['device_type'] = $isMob ? "1": "0" ;
        $data['message']['whatsapp'] = "Hi, ". $data['agent']['firstname'] ." has sent you a special gift 🎁 " ."\r\n". "

                            Save gold 🎊 using the code: ". $data['agent']['agent_code']."  " ."\r\n". "

                            Join gold saving scheme and shop Best Quality gold jewels.  " ."\r\n". "
                            
                            Join now 👇: https://shop.swarnatarajewellers.com/test_swtemi/";
        
        $data['message']['sms'] =  $data['message']['whatsapp'];                    
		$data['fileName'] = self::VIEW_FOLDER.'referral';
		$this->load->view('layout/template', $data); 
	}
    function referral_linkshare(){
		$referral=$this->input->post('referral_id');
		$type=$this->input->post('referral_by');
	    $serv_model=self::SERV_MODEL;
	    $email_model=self::EMAIL_MODEL;
	    $serviceID = 15;		
				 if($referral!='' && $type==1)  
				{	// promotion sms //
					$otp_promotion=1;
					$mobile=$this->input->post('referral_id');
					$id=$this->session->userdata("username");
					$data =$this->$serv_model->get_SMS_data($serviceID,$id);
					//$ullink=$this->shortenurl($this->url);
					$applink=$this->shortenurl($this->config->item('paystore_url'));
					$message=str_replace('##',$applink,$data['message']);
				//	$status= $this->send_sms($mobile,$message,$otp_promotion);	
				if($this->config->item('sms_gateway') == '1'){
					    		  $status=  $this->sms_model->sendSMS_MSG91($mobile,$message,$otp_promotion,'');		
					    		}
					    		elseif($this->config->item('sms_gateway') == '2'){
					    	      $status=  $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
					    		}
				}	
				  if($referral!='' &&  $type==2)
				 {			
					 $data['type'] =15;
					 $data['company_details'] = $this->comp;
					 $to=$this->input->post('referral_id');
					 $id=$this->session->userdata("username"); 
					 $data['referral'] = $id;				 
					 $data['name'] = ""; 				 
					 $content =$this->$serv_model->get_SMS_data($serviceID,$id);
					 $weblink=$this->shortenurl($this->url);
					 $applink=$this->shortenurl($this->config->item('paystore_url'));
					 $data['weblink']=$weblink;		 
					 $data['applink']=$applink;		 
					 $subject = "Reg: ".$this->comp['company_name']." Invite Referrals ";
					 $message = $this->load->view('include/emailAccount',$data,true);
					 $status = $this->$email_model->send_email($to,$subject,$message,"",""); 
				 }
			   if($status){
					echo $status;					
				}else{
					echo 0;
				}
       }
    // referalcode verification //
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
    
    public function contactus()
	{ 			
		$data['content'] = '';
		$pageType = array('page' => 'contactus','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER.'contactus';
		$this->load->view('layout/template', $data);
	}  
	
    function contactForm()
	{
		
		//  Array ( [cfcaptchaAns] => zwdjvp [cfName] => a [cfMobile] => 9874563215 [cfMessage] => test [cfreg] => 2 )
		session_start();
		print_r($_POST['cfcaptchaAns']);
		echo '<br>';
		print_r($_SESSION['captchaContact']);
		if($_POST['cfcaptchaAns'] == $_SESSION['captchaContact'])
		{
			
		    $id_customer = $this->session->userdata('cus_id');   
	        $this->load->model('user_model');
		    $data = $_POST;
		    $title = ($data['cfreg'] == 1 ? 'Enquiry' : ($data['cfreg'] == 2 ?'Suggestion' : ($data['cfreg'] == 3 ? 'Complaint':'Others' )) );
            $insData = array('mobile'       => $data['cfMobile'],
                             'name'         => $data['cfName'],
                             'title'  	    => $title,
                             'id_customer'  => ($id_customer != NULL ?$id_customer:NULL),
                             'address'      => (isset($data['address'])?$data['address']:NULL),
                             'date_of_birth'=> (isset($data['date_of_birth'])?$data['date_of_birth']:NULL),
                             'date_of_wed'  => (isset($data['date_of_wed'])?$data['date_of_wed']:NULL),
                             'profession'   => (isset($data['profession'])?$data['profession']:NULL),
                             'email'  	    => (isset($data['custEmail'])?$data['custEmail']:NULL),
                             'date_add'     => date('Y-m-d H:i:s'),
                             'type'         => $data['cfreg'], // Feedback type
                             'enq_from'     => 1, // web app
                             'comments'  	=> $data['cfMessage']);
							 
							
            if($data['cfreg'] == 3 ){
                $ticketno = $this->user_model->genTicketNo();
                $insData['ticket_no'] = $ticketno;
            } 
				
            $result = $this->user_model->insCusFeedback($insData); 
            if($result['status'] == true){
                $to = $this->comp['email']; 
               // $cc = array("pavithra@vikashinfosolutions.com");	
               $cc="";
                $subject = "Reg - ".$this->comp['company_name']." customer enquiry"; 
                $message = $this->load->view('include/emailContact',$insData,true); 
                $sendEmail = $this->email_model->send_email($to,$subject,$message,$cc,"",""); 
                if($sendEmail)
				echo 1;
			    else
				echo 2; 
            } else {
                echo 2;
            } 
		}
		else
		{
			echo 0;
		}
	}
	
	
	function kyc_form()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
			
			$kyc = $this->scheme_modal->get_kyc_details();
            //print_r($kyc);exit;
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
		$approval_type = "Manual";
//	print_r($_POST);exit;
		if($_POST['type']=='add'){  
			if($_POST['form_type']==1){
				$bank = $_POST;
				$kyc_detail = array(
					'id_agent'    	 => $cus_id,
					'kyc_type'    	 	 => $bank['form_type'],	
					'number'    	 	 => (isset($bank['bank_acc_no'])?$bank['bank_acc_no']:NULL),
					'name'    	 		 => (isset($bank['acc_holder_name'])?$bank['acc_holder_name']:NULL),
			    	'bank_branch'    	 => (isset($bank['bank_name'])?$bank['bank_name']:NULL),
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
									//$this->load->model("mobileapi_model");
									$data['kyc_status'] = $this->scheme_modal->checkCusKYC($cus_id); 
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
						//$this->load->model("mobileapi_model");
						$data['kyc_status'] = $this->scheme_modal->checkCusKYC($cus_id); 
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
					'id_agent'    	 => $cus_id,
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
								//$this->load->model("mobileapi_model");
								$data['kyc_status'] = $this->scheme_modal->checkCusKYC($cus_id); 
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
						//$this->load->model("mobileapi_model");
						$data['kyc_status'] = $this->scheme_modal->checkCusKYC($cus_id); 
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
					'id_agent'    	 => $cus_id,
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
							//$this->load->model("mobileapi_model");
							$data['kyc_status'] = $this->scheme_modal->checkCusKYC($cus_id); 
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
						//$this->load->model("mobileapi_model");
						$data['kyc_status'] = $this->scheme_modal->checkCusKYC($cus_id); 
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
					'id_agent'    	 => $cus_id,
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
									//$this->load->model("mobileapi_model");
									$data['kyc_status'] = $this->scheme_modal->checkCusKYC($cus_id); 
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
						//$this->load->model("mobileapi_model");
						$data['kyc_status'] = $this->scheme_modal->checkCusKYC($cus_id); 
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
				'id_agent'    	 => $cus_id,
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
	
		
	public function myaccount()
	{
	  	if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
			$agent = $this->user_model->agent_details();
			$data['content'] = '';
			$pageType = array('page' => 'myaccount','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['agent'] = $agent;
			$data['fileName'] = self::VIEW_FOLDER.'myaccount';
			$this->load->view('layout/template', $data);
		}
	}
function set_image($id)
 {
 	    $data=array();

   	 if($_FILES['agentimage']['name'])
   	 { 
   	 	$path='assets/img/agent/';
	    if (!is_dir($path)) {
		  mkdir($path, 0777, TRUE);
		}
		else{
			$file = $path.$id.".jpg" ;
			chmod($path,0777);
	        unlink($file);
		}
   	 	$img=$_FILES['agentimage']['tmp_name'];
		$filename = $_FILES['agentimage']['name'];
		$imgname = 'agent_profile_'.$id.'.'.substr($_FILES['agentimage']['type'],6);
   	 	$imgpath = 'assets/img/agent/'.$imgname;
   	 	
		//print_r($imgpath);exit;
	 	$upload=$this->upload_img('agentimage',$imgpath,$img);	
	
   	 
	 	
	 }
	 
	 if($_FILES['bankFile']['name']){
	     $path='assets/img/agent/bank/';
	    if (!is_dir($path)) {
		  mkdir($path, 0777, TRUE);
		}
		else{
			$file = $path.$id.".jpg" ;
			chmod($path,0777);
	        unlink($file);
		}
   	 	$img=$_FILES['bankFile']['tmp_name'];
		$filename = $_FILES['bankFile']['name'];
		$bank_img_name = 'agent_bank_'.$id.'.'.substr($_FILES['bankFile']['type'],6);	
   	 	$imgpath = 'assets/img/agent/bank/'.$bank_img_name;
		//print_r($imgpath);exit;
	 	$upload=$this->upload_img('bankFile',$imgpath,$img);	
	 }
 }
 function upload_img( $outputImage,$dst, $img)
	{	
		//print_r(getimagesize($img));exit;    //Array ( [0] => 512 [1] => 288 [2] => 3 [3] => width="512" height="288" [bits] => 8 [mime] => image/png )
		if (($img_info = getimagesize($img)) === FALSE)
		{
			return false;
		}
	$width = $img_info[0];
	$height = $img_info[1];
	
	switch ($img_info[2]) {
	  case IMAGETYPE_GIF  : $src = imagecreatefromgif($img);
	  						$tmp = imagecreatetruecolor($width, $height);
	  						$kek=imagecolorallocate($tmp, 255, 255, 255);
				      		imagefill($tmp,0,0,$kek);
	  						break;
	  case IMAGETYPE_JPEG : $src = imagecreatefromjpeg($img); 
	  						$tmp = imagecreatetruecolor($width, $height);
	 						break;
	  case IMAGETYPE_PNG  : $src = imagecreatefrompng($img);
						    $tmp = imagecreatetruecolor($width, $height);
	  						$kek=imagecolorallocate($tmp, 255, 255, 255);
				     		imagefill($tmp,0,0,$kek);
				     		break;
	  default : //die("Unknown filetype");	
	  return false;
	  }		
	 
	  imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
	  imagejpeg($tmp, $dst, 60);
	}
	
    public function updateMyAccount(){
		$id_agent = $_POST['agentid'];
//Array ( [agentimage] => Array ( [name] => 960x425 logimax banner.jpg [type] => image/jpeg [tmp_name] => /tmp/phpVNEHfe [error] => 0 [size] => 239252 ) 
//[bankFile] => Array ( [name] => [type] => [tmp_name] => [error] => 4 [size] => 0 ) )		
		$filename = $_FILES['agentimage']['name']; 
		$get_bank_file = $_FILES['bankFile']['name'];
   	 	$imgpath = 'agent_profile_'.$id_agent.'.'.substr($_FILES['agentimage']['type'],6);
   	 	$bank_img_path = 'agent_bank_'.$id_agent.'.'.substr($_FILES['bankFile']['type'],6);
		$agentData = $this->user_model->agent_details();
		if($filename != ''){
			$image = $imgpath;
		}else{
			$image = $agentData['image'];
		}
		
		if($get_bank_file != ''){
			$bank_image = $bank_img_path;
		}else{
			$bank_image = $agentData['bank_image'];
		}
		
		
	

		$agentAddress = array(
							  "id_agent" => $this->input->post('agentid'),
							  "address1" => $this->input->post('address1'),
							  "address2" => $this->input->post('address2'),
							  "id_country" => $this->input->post('id_country'),
							  "id_state" => $this->input->post('id_state'),
							  "id_city" => $this->input->post('id_city'),
							  "pincode" => $this->input->post('pincode'),
							  "date_add" => date('Y-m-d',strtotime(str_replace("/","-",$this->input->post('date_add'))))
							  );
							  
		$agentData  = array(
							"title" => $this->input->post('title'),
							"firstname" => $this->input->post('firstname'),
							"lastname" => $this->input->post('lastname'),
							//"gender" => $this->input->post('gender'),
							"date_of_birth" => strlen($this->input->post('bday')) ? date("Y-m-d", strtotime($this->input->post('bday'))) : NULL,
							"date_of_wed" => strlen($this->input->post('wed')) ? date("Y-m-d", strtotime($this->input->post('wed'))) : NULL,
							//"email" => $this->input->post('email'),
							"image" => $image,
							"date_upd" => date('Y-m-d H:i:s'),
							"website_url" => $this->input->post('webLink'),
							"facebook_url" => $this->input->post('fbLink'),
							"instagram_url" => $this->input->post('igLink'),
							"twitter_url" => $this->input->post('twLink'),
							"youtube_url" => $this->input->post('ytLink'),
							"bank_account_number" => $this->input->post('bankAccNo'),
							"ifsc_code" => $this->input->post('ifsc'),
							"bank_name" => $this->input->post('bankName'),
							"bank_acc_holder_name" => $this->input->post('bankAccName'),
							"preferred_mode" => $this->input->post('preferred_mode'),
							"bank_image" => $bank_image
							);
							
		$this->db->trans_begin(); 
		//print_r($_POST);exit;
		$updAgent = $this->user_model->update_agent($agentData,$this->input->post('agentid')); 
			
		if($updAgent>0){
			$this->db->trans_begin();
			$updAgentAddress = $this->user_model->update_agent_address($agentAddress,$updAgent); 
			if($updAgentAddress>0){
				$this->db->trans_commit();
				$this->set_image($this->input->post('agentid'));
				$this->session->set_flashdata('successMsg','Your profile has been updated successfully');
				redirect('user/myaccount');
			}/* else{
				$this->db->trans_rollback();						 	
				$this->session->set_flashdata('errMsg','Error in updation.Please try again later...');
				redirect('user/myaccount');
			}	*/	
		}
		else{
				$this->db->trans_rollback();						 	
				$this->session->set_flashdata('errMsg','Error in updation.Please try again later...');
				redirect('user/myaccount');
			}	 
		

}
	
	function req_settlement($page){
    	switch ($page) {
    	  case "ajax"  : 
    	        
    	  		break;
    	  case 'submit' :
            		$id_agent = $this->session->userdata('cus_id');
            		$reqData = array(
            							  "id_agent"        => $id_agent,
            							  "requested_amt"   => $this->input->post('requested_amt'),
            							  "request_status"  => 0,
            							  "created_on"      => date('Y-m-d H:i:s'),
            							  "requested_on"    => date('Y-m-d H:i:s')
            							  );
            		$this->db->trans_begin();
            		$insID = $this->user_model->insertData($id_agent,$reqData);
            		if($this->db->trans_status() === TRUE){
            			$this->db->trans_commit();
        				$this->session->set_flashdata('successMsg','Your profile has been updated successfully');
        				redirect('user/req_settlement');		
            		}
            		else{
        				$this->db->trans_rollback();						 	
        				$this->session->set_flashdata('errMsg','Error in updation.Please try again later...');
        				redirect('user/req_settlement');
            	    }		
    	 		break;
    	  default   : 
                    $pageType = array('page' => 'req_settlement','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'tollfree1'=>$this->comp['tollfree1'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
                    $data['header_data'] = $pageType;
                    $data['footer_data'] = $pageType;
                    $data['fileName'] = self::VIEW_FOLDER.'settlmt_req';
                    $this->load->view('layout/template', $data);
    			break;
    	  return false;
        }		
	}
	
	
	function kyc_msg()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['content'] = $data;
			$data['fileName'] = self::VIEW_FOLDER.'kyc_msg';
			$this->load->view('layout/template', $data);
		}
	}
	
	
}