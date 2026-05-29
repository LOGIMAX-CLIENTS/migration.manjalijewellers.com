<?php 
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Chit_admin extends CI_Controller
{
	const VIEW_FOLDER = 'authenticate/';	
	const EMP_MODEL	= "employee_model";
	const SET_MODEL	= "admin_settings_model";
	const LOG_MODEL	= "log_model";
	const CHIT_MODEL = "chitadmin_model";
	const SERV_MODEL = "admin_usersms_model";
	const ACC_MODEL = "account_model";
	public function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->model(self::EMP_MODEL);
		$this->load->model(self::SET_MODEL);
		$this->load->model(self::LOG_MODEL);
		$this->load->model(self::CHIT_MODEL);
		$this->load->model(self::SERV_MODEL);
		$this->load->model(self::ACC_MODEL);
		$this->load->model("sms_model");
		$this->load->library('encrypt');
		$this->load->library('session');
		$this->comp = $this->admin_settings_model->get_company();
	}
	public function index()
	{
		 $this->login();
	}
	public function login()
	{
		if($this->session->userdata('is_logged'))
        {
			redirect('admin/dashboard');
		}
		elseif($this->session->userdata('access_time_from') != NULL && $this->session->userdata('access_time_from') != "")
		{
			$now = time(); 
			$from = $this->session->userdata('access_time_from'); 
			$to = $this->session->userdata('access_time_to');  
			$allowedAccess = ($now > $from && $now < $to) ? TRUE : FALSE ;
			if($allowedAccess == FALSE){
				$this->session->set_flashdata('login_errMsg','Exceeded allowed access time!!');
				redirect('chit_admin/logout');	
			}			
		}
		else
		{
			$this->load->view(self::VIEW_FOLDER.'form');
			//$this->load->view('layout/footer');
			$this->load->view('layout/footerLogin');
		}
	}
	//onProcess
	public function forgetAdmin()
	{
		$this->load->view(self::VIEW_FOLDER.'forgetAdmin');
		$this->load->view('layout/footerLogin');
	}
	public function otpConformation(){
		$this->load->view(self::VIEW_FOLDER.'otpVerify');
		$this->load->view('layout/footerLogin');
	}	
	public function forgetUser(){
		if($this->session->userdata('OTP') == $this->input->post('otp'))
		{
			$this->session->unset_userdata('OTP');
			$email=$this->input->post('email');
			$mobile=$this->input->post('mobile');
			$data['content'] = array('mble'=>$mobile,'email'=>$email);
			$this->load->view(self::VIEW_FOLDER.'forgotAdmin_pswd_reset', $data);
			$this->load->view('layout/footerLogin', $data);
		}
		else
		{
			$this->session->set_flashdata('errMsg','OTP provided by you is not valid.');
			redirect('/chit_admin/forgetAdmin');	
		}
	}
	public function forgetUser_OTP($email,$mobile)
	{
		$return = $this->chitadmin_model->forgetUser($email,$mobile);
		if($return == 1)
		{
			echo 1;
		}
		else
		{
			echo 2;
		}
	}
	public function forgot_pswd()
	{
  		$mobile = $this->input->post('mobile');
  		$email = $this->input->post('email');
  		$serv_model=self::SERV_MODEL;
			$this->db->trans_begin();
			$insertData = $this->chitadmin_model->forgot_pswd_reset($mobile);print_r($insertData);
			$cus = $this->chitadmin_model->customer_data($mobile);print_r($cus);
			if($this->db->trans_status() === TRUE)
			{
                $this->db->trans_commit();
                $message="Dear ". $cus['firstname'].", Your password has been changed successfully for ".$this->comp['company_name']." Employee login.";
                $this->send_sms($mobile,$message);
                $to =$email;
                $data['type'] = 2;
                $data['company'] = $this->comp;
                $data['name'] = $cus['firstname'];
                $subject = "Reg: ".$this->comp['company_name']." Employee forgot password";
                $message = $this->load->view('include/emailAccount',$data,true);
                $this->load->model('email_model');
                $sendEmail = $this->email_model->send_email($to,$subject,$message);
                $this->session->set_flashdata('successMsg','Now you can login with your new password.');
                redirect('/chit_admin/login');
			}
			else
			{
					$serviceID = 10;
					$service = $this->admin_usersms_model->checkService($serviceID);
					if($service['sms'] == 1)
					{
    					$id=$mobile;
    					$data =$this->$serv_model->get_SMS_data($serviceID,$id);
    					$this->send_sms($mobile,$message);
					}
					if($service['email'] == 1 && $email != '')
					{
						$to = $email;
						$data['name'] = $cus['firstname'];
						$data['type'] = -3;
						$data['company_details'] = $this->comp;
						$subject = "Reg: ".$this->comp['company_name']." Employee reset password";
						$message = $this->load->view('include/emailAccount',$data,true);
						$this->load->model('email_model');
						$sendEmail = $this->email_model->send_email($to,$subject,$message);
					}
					$this->db->trans_rollback();
					$this->session->set_flashdata('errMsg','Error in reset password.Please try again later...');
			}
		redirect('/chit_admin/login');
	}
	function send_sms($mobile,$message,$dlt_id) 
	{ 
	    $send = FALSE;
		if($this->config->item('sms_gateway') == '1'){
		    $send = $this->sms_model->sendSMS_MSG91($mobile,$message,'',$dlt_id);		
		}
		elseif($this->config->item('sms_gateway') == '2'){
	        $send = $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
		}
        return $send;
	} 
	public function logout()
	{
		$log_model = self::LOG_MODEL;
		$id_log   = $this->session->userdata('id_log');
		if($id_log!=NULL)
		{			
			$log_data = array('logout_on' => date("Y-m-d H:i:s"));
			$log = $this->$log_model->log("update",$id_log,$log_data);
		}
		$this->session->userdata = array();
        $this->session->sess_destroy();
        redirect('admin/login', 'refresh');
	}
	/* public function authenticate()
	 {
        $model     = self::EMP_MODEL;
        $log_model = self::LOG_MODEL;
        $set_model = self::SET_MODEL;
        $chit_model = self::CHIT_MODEL;
	 	$this->form_validation->set_rules('username', 'Username', 'required');
	 	$this->form_validation->set_rules('password', 'Password', 'required');
          if ($this->form_validation->run())
	 	{
	 	$username = $this->input->post('username');
	 	$userpwd  = $this->input->post('password');
         $valid    = $this->$model->authenticate_user($username,$userpwd);
	 	if($valid){
	 		$employee = $this->$model->get_emp_by_username($username);
	 		$company = $this->$set_model->get_company();
	 		$branch_set = $this->$chit_model->branch_settings();
	 		$log_data = array(
	 							'id_employee'   => $employee['id_employee'],
	 		          			'login_on'      => date("Y-m-d H:i:s")
	 						 );
	 		$log = $this->$log_model->log("insert","",$log_data);
	 		$data = array(
	 			'username'  		=> $employee['username'],
	 			'uid'       		=> $employee['id_employee'],
	 			'profile'   		=> $employee['id_profile'],
	 			'is_logged' 		=> true,
	 			'id_log'    		=> $log['insertID'],
	 			'logged_on' 		=> date("Y-m-d H:i:s"),
	 			'currency_symbol'	=> $company['currency_symbol'],
	 			'mob_code'			=> $company['mob_code'],
	 			'mob_no_len'		=> $company['mob_no_len'],
	 			'branch_settings'   => $branch_set['branch_settings'],
	 			'filerbybranch'	    =>($branch_set['branch_settings']==1 ?$employee['id_branch']==0 ?'0' :'1' :'nobranch'),
	 			'id_branch'   		=> $employee['id_branch'],
	 			'branchWiseLogin'   		=> $branch_set['branchWiseLogin']
	 		);
	 		$this->session->set_userdata($data);
	 		redirect('admin/dashboard');
	 	}
	 	else
	 	{
	 		$data['login_error'] = true;
	 		$this->load->view('authenticate/form',$data);
	 		$this->load->view('layout/footerLogin');
	 	}
	  }	
	  else
	  {
	 		$data['login_error'] = true;
	 		$this->load->view('authenticate/form',$data);
	 		$this->load->view('layout/footerLogin');
	 	}
	 }*/
	function checkDevice() {
	// checkDevice() : checks if user device is phone, tablet, or desktop
	// RETURNS 0 for desktop, 1 for mobile, 2 for tablets
	  if (is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "mobile"))) {
	    return is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "tablet")) ? 2 : 1 ;
	  } else {
	    return 0;
	  }
	} 
	public function authenticate()
	{
		$model     = self::EMP_MODEL;
		$log_model = self::LOG_MODEL;
		$set_model = self::SET_MODEL;
		$chit_model = self::CHIT_MODEL;
		$account = self::ACC_MODEL;
		$deviceType = $this->checkDevice();	  
		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run())
		{
			$username = $this->input->post('username');
			$userpwd  = $this->input->post('password');
			$id_branch  = $this->input->post('id_branch'); 
			$token_id  = $this->input->post('token_id'); 
			//$valid    = $this->$model->authenticate_user($username,$userpwd);
			$emp_id   = $this->$model->authenticate_user_id($username,$userpwd);
			$emp_company = $this->$model->get_emp_by_company($this->input->post('id_company'), $username);
			// echo "<pre>";print_r($emp_company);exit;
			// $id_branch = $emp_company['login_branches'];
			$system_fp_id   = $this->input->post('system_fp_id'); 
			//$counter_id     = $this->$chit_model->get_counter($system_fp_id);
			$counter_id     = NULL;
			$device_id     = NULL;
			$valid 	  = (isset($emp_id['status']) ? ($emp_id['status'] == 1 ? TRUE :FALSE) : TRUE); 
			if($valid){
				$allowedAccess = $this->$model->checkAccessTime($emp_id['id_employee']); 
				if($allowedAccess['status'] == FALSE){
					$adm_model = self::CHIT_MODEL ;
					$ia_data = array(
								'id_employee'   => $emp_id['id_employee'],
						        'id_branch'   	=> $id_branch == 0 ? NULL : $id_branch, 
								'created_on'    => date("Y-m-d H:i:s"),
								'ip_address'    => $this->session->userdata('ip_address'),
								'device_type'   => ($deviceType==0 ? "DESKTOP":($deviceType==1 ? "MOBILE":($deviceType==2 ? "TABLET":"DESKTOP")))
							 );
					$log = $this->$adm_model->insertData($ia_data,'log_inactive_hour_attempt');
					$data = array('result' => 2 ,'msg' => $allowedAccess['msg']);
					echo json_encode($data);
					exit;
				}
			} 
			$employee = $this->$model->get_emp_by_username($username);
			$company = $this->$set_model->get_company();
			$branch_set = $this->$chit_model->branch_settings();
			$finance    = $this->$set_model->get_financial_data();
			$emp_company = $this->$model->get_emp_by_company($this->input->post('id_company'), $username);
				//One signal Based Login
			if($valid){
			    $profile_settings = $this->$chit_model->get_profile_settings($employee['id_profile']);
    			if($profile_settings['device_wise_login']==1)
    			{
    			    $device_login=$this->$chit_model->get_device_details($token_id);
    			    //print_r($device_login);exit;
    			    if($device_login['status'])
    			    {
    			        $counter_id=$device_login['result']['id_counter'];
    			        $device_id=$device_login['result']['id_device'];
    			    }else{
    			        $adm_model = self::CHIT_MODEL;
					    $ia_data = array(
								'id_employee'   => $emp_id['id_employee'],
						        'id_branch'   	=> $id_branch == 0 ? NULL : $id_branch, 
								'created_on'    => date("Y-m-d H:i:s"),
								'ip_address'    => $this->session->userdata('ip_address'),
								'device_type'   => ($deviceType==0 ? "DESKTOP":($deviceType==1 ? "MOBILE":($deviceType==2 ? "TABLET":"DESKTOP")))
							 );
					    $log = $this->$adm_model->insertData($ia_data,'log_inactive_hour_attempt');
    			        $data = array('result' => 2 ,'msg' => $device_login['message']);
    					echo json_encode($data);
    					exit;
    			    }
    			}
			}
			//One signal Based Login
			//Login Branches
			$branch_login=true;
			if($branch_set['login_branch']==1)
			{
			        if($employee['login_branches']!='' && $employee['login_branches']!=null)
			        {
    			    	$branch_id     = array(explode(',',$employee['login_branches']));
                        if($branch_id[0][0] == 0)
                        {
                            $branch_login=true;
                        }
                        else
                        {
                            foreach($branch_id[0] as $branch)
                            {
                                if($branch==$id_branch)
                                {
                                    $branch_login=true;
                                    break;
                                }
                                else
                                {
                                    $branch_login=FALSE;
                                }
                            }
                        }
			        }else{
			            $branch_login=FALSE;
			        }
			}
			//Company check
			$comp_login = true;
			$emp_branch_cmp = true;
			//print_r($emp_company);
			if($branch_set['company_settings'] == 1)
			{
			    if($emp_company['id_company'] == $this->input->post('id_company') || $emp_company['id_company'] == 0)
                                {
                                    $comp_login = true;
                                }
                                else
                                {
                                    $comp_login = FALSE;
                                }
                 //to check company branch   
                $emp_branch_cmp = $this->$model->get_empBranch_company($this->input->post('id_company'),$this->input->post('id_branch'),$username);
               //print_r($emp_branch_cmp);exit;
             /*   $comp_branch_login = true;
                if($this->input->post('id_branch') > 0)
                {
    			    if($emp_branch_cmp['id_branch'] == $this->input->post('id_branch'))
                                    {
                                        $comp_branch_login=true;
                                    }
                                    else
                                    {
                                        $comp_branch_login=FALSE;
                                    }
                }else{
                    $comp_branch_login=true;
                }   */
			}
			if($id_branch > 0){
				$branch_data  = $this->admin_settings_model->get_branch_by_id($id_branch);
				$branch_name = $branch_data['name'];
				$branch_otp_mobile = $branch_data['otp_verif_mobileno'];     
			} else{
				$branch_name = "";
				$branch_otp_mobile = "";
			}
		    //var_dump($emp_branch_cmp);exit;
			//Login Branches
			if($branch_login && $comp_login && $emp_branch_cmp)
			{
				if($valid)
				{ 
					if($emp_id['req_otplogin'] == 1){ // Profile wise OTP required
						$loginOTP_exp= $this->$chit_model->prof_wise_loginotp_exp();
						$mobile=$emp_id['mobile'];
						$firstname=$emp_id['firstname'];
						$OTP = mt_rand(100001,999999);  
						$this->session->set_userdata('login_OTP',$OTP);
						$this->session->set_userdata('loginOTP_exp',time()+$loginOTP_exp);
						$message=" Your OTP for Admin Panel Login is ".$OTP."  Will expire within ". $loginOTP_exp." Sec. ";
						$dlt_id='1207161486717494221';
						$this->send_sms($mobile,$message,$dlt_id);
						$this->admin_usersms_model->send_whatsApp_message($mobile,$message);
						$otp['otp_gen_time'] = date("Y-m-d H:i:s");
						$otp['otp_code'] = $OTP;
						$status=$this->$account->otp_insert($otp);
						$data=array('result'=>3 ,'msg'=>'OTP has been sent successfully');
						echo json_encode($data);
					}
					else if($branch_set['isOTPReqToLogin'] == 1 )
					{
						$loginOTP_exp= $this->$model->loginOTP_exp();
						$mobile=$emp_id['mobile'];
						$firstname=$emp_id['firstname'];
						$OTP = mt_rand(100001,999999);  
						$this->session->set_userdata('login_OTP',$OTP);
						$this->session->set_userdata('loginOTP_exp',time()+$loginOTP_exp);
						$message=" Your OTP for Admin Panel Login is ".$OTP."  Will expire within ". $loginOTP_exp." Sec. ";
						$dlt_id='1207161486717494221';
						$this->send_sms($mobile,$message,$dlt_id);
						$this->admin_usersms_model->send_whatsApp_message($mobile,$message);
						$otp['otp_gen_time'] = date("Y-m-d H:i:s");
						$otp['otp_code'] = $OTP;
						$status=$this->$account->otp_insert($otp);
						$data=array('result'=>3 ,'msg'=>'OTP has been sent','otp'=>$OTP);
						echo json_encode($data);
					}
					else{
						$log_data = array(
							'id_employee'   => $employee['id_employee'],
					        'id_branch'   	=> $id_branch == 0 ? NULL:$id_branch, 
							'login_on'      => date("Y-m-d H:i:s"),
							'ip_address'    => $this->session->userdata('ip_address'),
							'device_type'   => ($deviceType==0 ? "DESKTOP":($deviceType==1 ? "MOBILE":($deviceType==2 ? "TABLET":"DESKTOP")))
						 );
						$log = $this->$log_model->log("insert","",$log_data);
						$data = array(
						'fin_year_code'    => $finance['fin_year_code'],
						'username'  		=> $employee['username'],
						'uid'       		=> $employee['id_employee'],
						'profile'   		=> $employee['id_profile'],
						'is_logged' 		=> true,
						'id_log'    		=> $log['insertID'],
						'logged_on' 		=> date("Y-m-d H:i:s"),
						'currency_symbol'	=> $company['currency_symbol'],
						'mob_code'			=> $company['mob_code'],
						'mob_no_len'		=> $company['mob_no_len'],
						'branch_settings'   => $branch_set['branch_settings'],
						'branchwise_scheme'   => $branch_set['branchwise_scheme'],
						'filerbybranch'	    =>($branch_set['branch_settings']==1 ?$employee['id_branch']==0 ?'0' :'1' :'nobranch'),
						'id_branch'   		=> $id_branch == 0 ? '':$id_branch, 
						'branch_name'  		=> $branch_name,  
						'branchWiseLogin'   	=> $branch_set['branchWiseLogin'],
						'is_branchwise_cus_reg' => $branch_set['is_branchwise_cus_reg'],
						'is_branchwise_rate'   	=> $branch_set['is_branchwise_rate'],
						'login_branch'          => $branch_set['login_branch'],
						'access_time_from'      => $allowedAccess['access_time_from'],
						'access_time_to'      	=> $allowedAccess['access_time_to'],
						'counter_id'            =>$counter_id,
						'company_settings'      => $branch_set['company_settings'],
						'company_name'          => $emp_company['company_name'],
						'id_company'            => $emp_company['id_company'],
						'empLog_branch'         => ($branch_set['branch_settings']==1 && $branch_set['branchWiseLogin'] == 1 ? ($id_branch != 0 ? $id_branch : 'N') :'N'),     // 11-01-2023 #AB branch auto store based on emp log 
						'branch_otp_mobile'     => $branch_otp_mobile,
						'currencyFormat'	=> $company['currency_format'],  // esakki 11-11
						'currency_decimal'	=> $company['currency_decimal'],   // esakki 11-11
						'enable_old_sch_acc_no'   => $branch_set['enable_old_sch_acc_no'],
						'metal_wgt_decimal' 	=> $company['metal_wgt_decimal'],   // Karthikai Kumaran 20/11/25
						'metal_wgt_roundoff' 	=> $company['metal_wgt_roundoff'],   // Karthikai Kumaran 20/11/25
						);
						$this->session->set_userdata($data);
						//print_r($this->session->all_userdata());exit;
						//redirect('admin/dashboard');
						$data = array('result'=>1 ,'msg'=>'correct');
						echo json_encode($data);
					}  
				}
				else
				{
					$data=array('result'=>2 ,'msg'=>'Invalid Username or Password');
					echo json_encode($data);
				}
			}
			else if($branch_login == FALSE)
			{
				$data=array('result'=>2 ,'msg'=>'Invalid Branch');
				echo json_encode($data);
			}else if($comp_branch_login == FALSE)
			{
			    $data=array('result'=>4 ,'msg'=>'Invalid Company Branch');
				echo json_encode($data);
			}
			else{
			    $data=array('result'=>4 ,'msg'=>'Invalid Company');
				echo json_encode($data);
			}
		}	
		else if($this->input->post('username') == '' || $this->input->post('password') == '')
		{
			$data=array('result'=>2 ,'msg'=>'Enter Username and Password');
			echo json_encode($data);
		}
		else
		{
		    $data=array('result'=>5 ,'msg'=>'Select Company');
			echo json_encode($data);
		}
	}
	function update_otp()
	{
		$model     = self::EMP_MODEL;
		$log_model = self::LOG_MODEL;
		$set_model = self::SET_MODEL;
		$chit_model = self::CHIT_MODEL;
		$accountmodel = self::ACC_MODEL;
		$username 	= $this->input->post('username');
		$userpwd  	= $this->input->post('password');
		$input_otp  = $this->input->post('input_otp');
		$employee 	= $this->$model->get_emp_by_username($username);
		$company 	= $this->$set_model->get_company();
		$branch_set = $this->$chit_model->branch_settings();
		$otp = $this->$accountmodel->select_otp($input_otp);
		$login_data['is_verified']	= '1';
		$login_data['verified_time']= date("Y-m-d H:i:s");
		$status = $this->$accountmodel->otp_update_payment($login_data,$otp['id_otp']);
		$system_fp_id   = $this->input->post('system_fp_id'); 
		$counter_id     = $this->$chit_model->get_counter($system_fp_id);
		if($employee['id_branch'] > 0){
			$branch_data  = $this->admin_settings_model->get_branch_by_id($employee['id_branch']);
			$branch_name = $branch_data['name'];
		} else{
			$branch_name = "";
		}
		if($input_otp == $this->session->userdata('login_OTP'))
		{ 
			if(time() >= $this->session->userdata('loginOTP_exp'))
			{
				$this->session->unset_userdata('login_OTP');
				$this->session->unset_userdata('loginOTP_exp');
				$data = array('result'=>5 ,'msg'=>'OTP has been expired');
			}
			else
			{				
				$allowedAccess = $this->$model->checkAccessTime($employee['id_employee']); 
				if($allowedAccess['status'] == FALSE){
					$adm_model = self::CHIT_MODEL; 
					$deviceType = $this->checkDevice();	  
					$ia_data = array(
								'id_employee'   => $employee['id_employee'],
						        'id_branch'   	=> $employee['id_branch'] == 0 ? NULL : $employee['id_branch'], 
								'created_on'    => date("Y-m-d H:i:s"),
								'ip_address'    => $this->session->userdata('ip_address'),
								'device_type'   => ($deviceType==0 ? "DESKTOP":($deviceType==1 ? "MOBILE":($deviceType==2 ? "TABLET":"DESKTOP")))
							 );
					$log = $this->$adm_model->insertData($ia_data,'log_inactive_hour_attempt');
					$data = array('result' => 2 ,'msg' => $allowedAccess['msg']);
					echo json_encode($data);
					exit;
				}
				$log_data = array(
									'id_employee'   => $employee['id_employee'],
									'login_on'      => date("Y-m-d H:i:s"),
									'id_otp'		=> $otp['id_otp'],
									'ip_address'    => $this->session->userdata('ip_address')
								 );
				$log = $this->$log_model->log("insert","",$log_data);
				$data = array(
					'username'  		=> $employee['username'],
					'uid'       		=> $employee['id_employee'],
					'profile'   		=> $employee['id_profile'],
					'is_logged' 		=> true,
					'id_log'    		=> $log['insertID'],
					'logged_on' 		=> date("Y-m-d H:i:s"),
					'currency_symbol'	=> $company['currency_symbol'],
					'mob_code'			=> $company['mob_code'],
					'mob_no_len'		=> $company['mob_no_len'],
					'branch_settings'   => $branch_set['branch_settings'],
					'filerbybranch'	    =>($branch_set['branch_settings']==1 ?$employee['id_branch']==0 ?'0' :'1' :'nobranch'),
					'id_branch'   		=> $employee['id_branch'],
					'branch_name'  		=> $branch_name, 
					'branchWiseLogin'   => $branch_set['branchWiseLogin'],
					'is_branchwise_cus_reg' => $branch_set['is_branchwise_cus_reg'],
					'is_branchwise_rate'   	=> $branch_set['is_branchwise_rate'],
					'login_branch'          => $branch_set['login_branch'],
					'access_time_from'      => $allowedAccess['access_time_from'],
					'access_time_to'      	=> $allowedAccess['access_time_to'],
					'counter_id'            => $counter_id
				);
				$this->session->set_userdata($data);
				$data=array('result'=>2 ,'msg'=>'successfully updated');
			}
		}
		else
		{
			$data=array('result'=>7 ,'msg'=>'Invalid OTP');
		}
		echo json_encode($data);
	}
	function resendotp()
	{
        $model     = self::EMP_MODEL;
        $log_model = self::LOG_MODEL;
        $set_model = self::SET_MODEL;
        $chit_model = self::CHIT_MODEL;
        $account = self::ACC_MODEL;
        $username = $this->input->post('username');
        $userpwd  = $this->input->post('password');
        $emp_id   = $this->$model->authenticate_user_id($username,$userpwd);
        if($emp_id['req_otplogin'] == 1){ // Profile wise OTP required
            $loginOTP_exp= $this->$chit_model->prof_wise_loginotp_exp();
        }else{ 
            $loginOTP_exp= $this->$model->loginOTP_exp();
        }
        $mobile=$emp_id['mobile'];
        $firstname=$emp_id['firstname'];
        $OTP = mt_rand(100001,999999);  
        //$OTP = 111111;  
        $this->session->set_userdata('login_OTP',$OTP);
        $this->session->set_userdata('loginOTP_exp',time()+$loginOTP_exp);
        $message=" Your OTP for Admin Panel Login is ".$OTP."  Will expire within ". $loginOTP_exp." Sec. ";
        $dlt_id='1207161486717494221';
        $this->send_sms($mobile,$message,$dlt_id);
        $this->admin_usersms_model->send_whatsApp_message($mobile,$message);
        $otp['otp_gen_time'] = date("Y-m-d H:i:s");
        $otp['otp_code'] = $OTP;
        $status=$this->$account->otp_insert($otp);
        $data=array('result'=>3 ,'msg'=>'OTP Sent Successfully','uid'=>$emp_id['id_employee']);
        echo json_encode($data);
	}
  	public function branchname_list()
	{
		$model_name=self::ACC_MODEL;
		$data['branch']=$this->$model_name->branchname_list();
		echo json_encode($data);
	}
	public function companyname_list()
	{
		$model_name=self::CHIT_MODEL;
		$data['company']=$this->$model_name->companyname_list();
		echo json_encode($data);
	}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */