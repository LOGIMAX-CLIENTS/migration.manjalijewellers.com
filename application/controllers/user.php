<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
class User extends CI_Controller
{
	const VIEW_FOLDER = 'chitscheme/';
	const API_MODEL   = 'chitapi_model';
	const SERV_MODEL = 'services_modal';
	const EMAIL_MODEL = 'email_model';
	const CUS_IMG_PATH = 'admin/assets/img/customer/';
	const BASIC_FOLDER = 'basicpages/';
	public function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Kolkata');
		$this->load->model('login_model');
		$this->comp = $this->login_model->company_details();
		$this->m_mode = $this->login_model->site_mode();
		// referal_code //
		$this->refcode = $this->login_model->__encrypt("ref/" . $this->session->userdata("username") . "/" . md5(uniqid(mt_rand(), true) . microtime(true)) . "");
		$this->url = base_url() . 'index.php/user/register_add/' . $this->refcode . '';
		// referal_code//
		if ($this->m_mode['maintenance_mode'] == 1) {
			$this->maintenance();
		}
		$this->load->model('services_modal');
		$this->load->model('scheme_modal');
		$this->load->model('registration_model');
		$this->load->model('mobileapi_model');
		$this->load->model('email_model');
		$this->load->model('sms_model');
		$this->scheme_status = $this->scheme_modal->scheme_status();
		$this->sms_data = $this->services_modal->sms_info();
		$this->pro_data = $this->services_modal->promotion_info();
		$this->pro_chk =  $this->services_modal->promotion_smsavilable();
		$this->sms_chk =  $this->services_modal->otp_smsavilable();
		$this->log_dir = 'log/' . date("Y-m-d");
		if (!is_dir($this->log_dir)) {
			mkdir($this->log_dir, 0777, TRUE);
		}
	}
	public function login()
	{
		if ($this->session->userdata('username')) {
			redirect("/chitscheme");
		} else {
			$records = $this->login_model->empty_record();
			$data['content'] = $records;
			$pageType = array('page' => 'login', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'tollfree1' => $this->comp['tollfree1'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER . 'login';
			$this->load->view('layout/template', $data);
		}
	}
	public function offers()
	{
		$records = $this->services_modal->offers();
		$data['content'] = $records;
		$pageType = array('page' => 'offers', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER . 'offers';
		$this->load->view('layout/template', $data);
	}
	public function newarrivals()
	{
		$records = $this->services_modal->newarrivals();
		$data['content'] = $records;
		$pageType = array('page' => 'newarrivals', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER . 'newarrivals';
		$this->load->view('layout/template', $data);
	}
	function offer_description($id_offer)
	{
		$records = $this->services_modal->offer_details($id_offer);
		//	echo "<pre>";print_r($records );echo "</pre>";exit;
		$data['content'] = $records;
		$pageType = array('page' => 'offer_description', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER . 'offer_description';
		$this->load->view('layout/template', $data);
	}
	function newarrivals_description($id_new_arrivals)
	{
		$records = $this->services_modal->newarrival_detail($id_new_arrivals);
		$data['content'] = $records;
		$pageType = array('page' => 'newarrivals_description', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER . 'newarrivals_description';
		$this->load->view('layout/template', $data);
	}
	function gift_artical_description($id_new_arrivals)
	{
		$records = $this->services_modal->gift_artical_detail($id_new_arrivals);
		$data['content'] = $records;
		$pageType = array('page' => 'gift_articals_description', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER . 'gift_articals_description';
		$this->load->view('layout/template', $data);
	}
	public function gift_artical()
	{
		$records = $this->services_modal->gift_artical();
		$data['content'] = $records;
		$pageType = array('page' => 'gift_articals', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER . 'gift_articals';
		$this->load->view('layout/template', $data);
	}
	public function forget()
	{
		$data['content'] = '';
		$pageType = array('page' => 'forgot', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER . 'forget';
		$this->load->view('layout/template', $data);
	}
	public function forgetUser_OTP($mobile)
	{
		$return = $this->login_model->forgetUser($mobile);
		if ($return == 1) {
			echo 1;
		} else {
			echo 2;
		}
	}
	public function forgetUser()
	{
		if ($this->session->userdata('OTP') == $this->input->post('otp')) {
			$this->session->unset_userdata('OTP');
			$email = $this->input->post('email');
			$mobile = $this->input->post('mobile');
			$data['content'] = array('mble' => $mobile, 'email' => $email);
			$pageType = array('page' => 'forgot', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER . 'forgot_pswd_reset';
			$this->load->view('layout/template', $data);
		} else {
			$this->session->set_flashdata('errMsg', 'OTP provided by you is not valid.');
			redirect('/user/forget');
		}
	}
	public function forgot_pswd()
	{
		$mobile = $this->input->post('mobile');
		$email = $this->input->post('email');
		$this->db->trans_begin();
		$insertData = $this->login_model->forgot_pswd_reset($mobile);
		$cus = $this->login_model->customer_data($mobile);
		if ($this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
			$serviceID = 1;
			$service = $this->services_modal->checkService($serviceID);
			if ($service['sms'] == 1) {
				$message = "Dear " . $cus['firstname'] . ", Your password has been changed successfully for " . $this->comp['company_name'] . ".";
				if ($this->config->item('sms_gateway') == '1') {
					$this->sms_model->sendSMS_MSG91($mobile, $message, '', '');
				} elseif ($this->config->item('sms_gateway') == '2') {
					$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
				} elseif ($this->config->item('sms_gateway') == '3') {
					$this->sms_model->sendSMS_SpearUC($mobile, $message, '', '');
				} elseif ($this->config->item('sms_gateway') == '4') {
					$this->sms_model->sendSMS_Asterixt($mobile, $message, '');
				} elseif ($this->config->item('sms_gateway') == '5') {
					$this->sms_model->sendSMS_Qikberry($mobile, $message, '');
				}
			}
			if ($service['serv_whatsapp'] == 1) {
				$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
				$this->sms_model->send_whatsApp_message($mobile, $smsData);
			}
			if ($service['email'] == 1 && $email != '') {
				$to = $email;
				$data['type'] = 2;
				$data['company_details'] = $this->comp;
				$data['name'] = $cus['firstname'];
				$subject = "Reg: " . $this->comp['company_name'] . " purchase plan forgot password";
				$message = $this->load->view('include/emailAccount', $data, true);
				$this->load->model('email_model');
				$sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
			}
			$this->db->trans_commit();
			$this->session->set_flashdata('successMsg', 'Now you can login with your new password.');
			redirect('/user/login');
		} else {
			$serviceID = 10;
			$service = $this->services_modal->checkService($serviceID);
			if ($service['sms'] == 1) {
				$id = $mobile;
				$data = $this->$serv_model->get_SMS_data($serviceID, $id);
				if ($this->config->item('sms_gateway') == '1') {
					$this->sms_model->sendSMS_MSG91($mobile, $message, '', $service['dlt_te_id']);
				} elseif ($this->config->item('sms_gateway') == '2') {
					$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
				} elseif ($this->config->item('sms_gateway') == '3') {
					$this->sms_model->sendSMS_SpearUC($mobile, $message, '', $service['dlt_te_id']);
				} elseif ($this->config->item('sms_gateway') == '4') {
					$this->sms_model->sendSMS_Asterixt($mobile, $message, '', $service['dlt_te_id']);
				} elseif ($this->config->item('sms_gateway') == '5') {
					$this->sms_model->sendSMS_Qikberry($mobile, $message, '', $service['dlt_te_id']);
				}
			}
			if ($service['serv_whatsapp'] == 1) {
				$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
				$this->sms_model->send_whatsApp_message($mobile, $smsData);
			}
			if ($service['email'] == 1 && $email != '') {
				$to = $email;
				$data['name'] = $cus['firstname'];
				$data['type'] = -3;
				$data['company_details'] = $this->comp;
				$subject = "Reg: " . $this->comp['company_name'] . " purchase plan reset password";
				$message = $this->load->view('include/emailAccount', $data, true);
				$this->load->model('email_model');
				$sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
			}
			$this->db->trans_rollback();
			$this->session->set_flashdata('errMsg', 'Error in reset password.Please try again later...');
		}
		redirect('/user/login');
	}
	public function validateUser()
	{
		$return = $this->login_model->validateUser();
		if ($return['status'] == 1) {
			$branch_set = $this->login_model->branch_settings();
			$ref = $this->services_modal->get_data();
			//	$cus_data= $this->scheme_modal->get_cusname($return['cus_id']); //customer name by default in account name while joining scheme//hh 
			$data = array(
				'username'        => $_POST['username'],
				'mobile'          => $_POST['username'],
				'display_name'    => $return['display_name'],
				//'cus_name'        =>$cus_data['name'],
				'cus_id'          => $return['cus_id'],
				'is_logged_in'    => true,
				'allow_referral'  => $ref['allow_referral'],
				'is_kyc_required' => $branch_set['is_kyc_required'],
				'branch_settings'	=> $branch_set['branch_settings'],
				'id_branch'	    => $return['id_branch'],
				'branch_name'	    => $return['branch_name'],
				'cost_center'	    => $branch_set['cost_center'],
				'branchwise_scheme'    => $branch_set['branchwise_scheme'],
				'is_branchwise_cus_reg' => $branch_set['is_branchwise_cus_reg'],
				'branchWiseLogin'	     => $branch_set['branchWiseLogin'],
				'reference_no'    => $return['reference_no'],
				'metal_wgt_decimal'    => $branch_set['metal_wgt_decimal'],
				'metal_wgt_roundoff'    => $branch_set['metal_wgt_roundoff']
			);
			$this->session->set_userdata($data);
			if ($this->input->post('remember_me')) {
				$data['new_expiration'] = 60 * 60 * 24 * 30; //30 days
				$this->session->sess_expiration = $data['new_expiration'];
				$this->session->set_userdata($data);
			}
			//Sync Existing Data					
			if ($this->config->item("integrationType") == 3 || $this->config->item("autoSyncExisting") == 1) {
				$syncData = $this->sync_existing_data($_POST['username'], $return['cus_id'], $return['id_branch'], 'validateUser');
			}
			//print_r($return);exit;
			if ($branch_set['is_kyc_required'] == 1) {
				if ($return['kyc_status'] == 1) {
					redirect("/chitscheme");
				} else {
					redirect("/chitscheme/kyc_form");
				}
			} else {
				redirect("/chitscheme");
			}
		} else {
			$notActive = $this->login_model->validateActiveUser();
			if ($notActive) {
				$this->session->set_flashdata('errMsg', 'Your account is not yet activated!');
				redirect("/user/login");
			}
		}
		$this->session->set_flashdata('errMsg', 'Invalid username or password');
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
		$serv_model = self::SERV_MODEL;
		$limit = $this->$serv_model->limitDB('get', '1');
		$count = $this->$serv_model->customer_count();
		if ($limit['limit_cust'] == 1) {
			if ($count < $limit['cust_max_count']) {
				$records = $this->registration_model->empty_record();
				$pageType = array('page' => 'signup', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mob_no_len' => $this->comp['mob_no_len'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
				$data['header_data'] = $pageType;
				$data['footer_data'] = $pageType;
				$data['content'] = $records;
				$data['fileName'] = self::VIEW_FOLDER . 'signup';
				$this->load->view('layout/template', $data);
			} else {
				$this->session->set_flashdata('errMsg', 'Temporarily New user registration service unavailable, Kindly contact Customer care...');
				redirect('/user/login');
			}
		} else {
			$records = $this->registration_model->empty_record();
			$pageType = array('page' => 'signup', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mob_no_len' => $this->comp['mob_no_len'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['content'] = $records;
			//print_r($data);exit;
			$data['fileName'] = self::VIEW_FOLDER . 'signup';
			$this->load->view('layout/template', $data);
		}
	}
	public function register_update()
	{
		if (!$this->session->userdata('username')) {
			redirect("/user/login");
		} else {
			$profileData = $this->registration_model->get_entryRecord();
			//print_r($profileData);
			$data['content'] = $profileData;
			$pageType = array('page' => 'profile', 'currency_symbol' => $this->comp['currency_symbol'], 'scheme_status' =>  $this->scheme_status, 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER . 'profile';
			$this->load->view('layout/template', $data);
		}
	}
	public function reset_passwd()
	{
		if (!$this->session->userdata('username')) {
			redirect("/user/login");
		} else {
			$data['content'] = '';
			$pageType = array('page' => 'reset_pass', 'currency_symbol' => $this->comp['currency_symbol'], 'scheme_status' =>  $this->scheme_status, 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER . 'reset_passwd';
			$this->load->view('layout/template', $data);
		}
	}
	public function resetPass_submit()
	{
		if (!$this->session->userdata('username')) {
			redirect("/user/login");
		} else {
			if ($this->registration_model->reset_passwd()) {
				$this->session->set_flashdata('successMsg', 'Your password changed successfully...');
				redirect("/dashboard");
			} else {
				$this->session->set_flashdata('errMsg', 'Error in changing your password.Please try again later');
				redirect("/user/reset_passwd");
			}
		}
	}
	public function change_mobile()
	{
		if (!$this->session->userdata('username')) {
			redirect("/user/login");
		} else {
			$data['content'] = '';
			$pageType = array('page' => 'changeUser', 'scheme_status' =>  $this->scheme_status, 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER . 'change_mobile';
			$this->load->view('layout/template', $data);
		}
	}
	public function update_mobile()
	{
		if (!$this->session->userdata('username')) {
			redirect("/user/login");
		} else {
			if ($this->session->userdata('OTP') == $this->input->post('otp')) {
				$mobile = trim($this->input->post('mobile'));
				if (strlen($mobile) == 10) {
					if ($this->registration_model->update_mobile($mobile)) {
						$data = array(
							'username' => $mobile
						);
						$this->session->set_userdata($data);
						$this->session->set_flashdata('successMsg', 'Your Mobile No. changed successfully...');
						redirect("/dashboard");
					} else {
						$this->session->set_flashdata('errMsg', 'Error in changing your Mobile No.Please try again later');
						redirect("/user/change_mobile");
					}
				} else {
					$this->session->set_flashdata('errMsg', 'Mobile Number should be exactly 10 digits.');
					redirect("/user/change_mobile");
				}
			} else {
				$this->session->set_flashdata('errMsg', 'Not a valid OTP.Please try again.');
				redirect("/user/change_mobile");
			}
		}
	}
	public function generateOTP($mobile, $type, $email, $cus_name = '')
	{
		if (strlen(trim($mobile)) == 10) {
			$check_mobileno = $this->registration_model->check_mobileno($mobile);
			if ($check_mobileno) {
				$this->session->unset_userdata("OTP");
				$OTP = mt_rand(100000, 999999);
				$this->session->set_userdata('OTP', $OTP);
				if ($type == 0) {
					$message = "Your OTP for  " . $this->comp['company_name'] . "  enrollment is :  " . $OTP . " ";
					if ($this->config->item('sms_gateway') == '1') {
						$this->sms_model->sendSMS_MSG91($mobile, $message, '', '');
					} elseif ($this->config->item('sms_gateway') == '2') {
						$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
					} elseif ($this->config->item('sms_gateway') == '3') {
						$this->sms_model->sendSMS_SpearUC($mobile, $message, '', '');
					} elseif ($this->config->item('sms_gateway') == '4') {
						$this->sms_model->sendSMS_Asterixt($mobile, $message, '', '');
					} elseif ($this->config->item('sms_gateway') == '5') {
						$this->sms_model->sendSMS_Qikberry($mobile, $message, '', '');
					}
					if ($service['serv_whatsapp'] == 1) {
						$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
						$this->sms_model->send_whatsApp_message($mobile, $smsData);
					}
				} else {
					$message = $OTP . " is the verification code from " . $this->comp['company_name'] . " .Please use this code to verify your mobile number";
					if ($this->config->item('sms_gateway') == '1') {
						$this->sms_model->sendSMS_MSG91($mobile, $message, '', '');
					} elseif ($this->config->item('sms_gateway') == '2') {
						$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
					} elseif ($this->config->item('sms_gateway') == '3') {
						$this->sms_model->sendSMS_SpearUC($mobile, $message, '', '');
					} elseif ($this->config->item('sms_gateway') == '4') {
						$this->sms_model->sendSMS_Asterixt($mobile, $message, '', '');
					} elseif ($this->config->item('sms_gateway') == '5') {
						$this->sms_model->sendSMS_Qikberry($mobile, $message, '', '');
					}
					if ($service['serv_whatsapp'] == 1) {
						$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
						$this->sms_model->send_whatsApp_message($mobile, $smsData);
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
				$data['company_details'] = $this->comp;
				$subject = "Reg: " . $this->comp['company_name'] . " registration";
				$message = $this->load->view('include/emailAccount', $data, true);
				$this->load->model('email_model');
				$sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
				echo 1;
			} else
				echo 0;
		} else
			echo 2;
	}
	public function check_email()
	{
		echo $this->registration_model->clientEmail($_POST["email"]);
	}
	public function get_scheme()
	{
		if (!$this->session->userdata('username')) {
			redirect("/user/login");
		} else {
			echo $this->registration_model->get_scheme();
		}
	}
	public function get_country()
	{
		if (!$this->session->userdata('username')) {
			redirect("/user/login");
		} else {
			$data = $this->registration_model->get_country();
			echo $data;
		}
	}
	public function get_country_list()
	{
		$data = $this->registration_model->get_country();
		echo $data;
	}
	/*	public function get_state($id)
	{
		echo $this->registration_model->get_state($id);
	}*/
	public function get_state($id)
	{
		if ($this->config->item('custom_fields')['country'] == 0) {
			echo $this->registration_model->get_state(101);
		} else {
			echo $this->registration_model->get_state($id);
		}
	}
	public function get_city($id)
	{
		echo $this->registration_model->get_city($id);
	}
	function DB_controller($type, $cus_id = '')
	{
		if ($type == 'add') {
			if (strlen(trim($this->input->post('mobile'))) == 10) {
				$serv_model = self::SERV_MODEL;
				if ($this->session->userdata('OTP') == $this->input->post('otp')) {
					$this->session->unset_userdata('OTP');
					$this->db->trans_begin();
					$refcode = $this->input->post('referal_code');
					if ($refcode != '') {
						$status = $this->checkreferalcode();
						if ($status['status'] == 1 && $status['referal_code'] != 'NULL') {
							$insertData = $this->registration_model->insert_data($status['referal_code']);
						} else if ($status['status'] == '0' && $status['referal_code'] == 'NULL') {
							$this->db->trans_rollback();
							$this->session->set_flashdata('errMsg', 'your registration referral code  Not availabe there!...Please use another referral code...');
							redirect('/user/register_add');
						}
					} else {
						$insertData = $this->registration_model->insert_data();
						if ($insertData) {
							$wallet_acc =  $this->registration_model->wallet_accno_generator();
							if ($wallet_acc['wallet_account_type'] == 1) {
								$this->wallet_account_create($insertData['insertID'], $this->input->post('mobile'));
							}
						}
					}
					//Sync Existing Data
					if ($insertData['status']) {
						if ($this->config->item("integrationType") == 5) { // Do customer registration in offline
							$this->load->model("integration_model");
							$postData = array(
								"appCustomerCode"	=> 0,
								"custName" 			=> ucfirst($this->input->post('firstname')),
								"mobileNo" 			=> trim($this->input->post('mobile')),
								"emailId" 			=> $this->input->post('email'),
								"preferdBranch" 	=> "",
								"branchCode" 		=> "",
								"custdetails" 		=> array("city" => "Coimbatore", "state" => "Tamil Nadu")
							);
							$response = $this->integration_model->khimji_curl('registerCustomerWithoutValidateOtp', $postData);
							if ($response['status'] == 1) {
								$resData = $response['data'];
								if ($resData->status == 1 && $resData->errorCode == 0 && $resData->result[0]->mobileNo == $this->input->post('mobile')) {
									$updCus = array("reference_no" => $resData->result[0]->customerCode, "date_upd" => date('Y-m-d H:i:s'));
									$this->integration_model->updateData($updCus, 'mobile', $resData->result[0]->mobileNo, 'customer');
									$this->session->set_userdata("reference_no", $resData->result[0]->customerCode);
								} else {
									if (!is_dir($this->log_dir . '/khimji')) {
										mkdir($this->log_dir . '/khimji', 0777, true);
									}
									$log_path = $this->log_dir . '/khimji/' . date("Y-m-d") . '.txt';
									$logData = "\n" . date('d-m-Y H:i:s') . "\n API : registerCustomerWithoutValidateOtp \n Response : " . json_encode($resData, true);
									file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
								}
							}
						} else if ($this->config->item("integrationType") == 3 || $this->config->item("autoSyncExisting") == 1) {
							$syncData = $this->sync_existing_data($this->input->post('mobile'), $insertData['insertID'], $this->input->post('id_branch'), 'SignUp');
						}
					}
					if ($this->db->trans_status() === TRUE) {
						$this->db->trans_commit();
						$serviceID = 1;
						$service = $this->services_modal->checkService($serviceID);
						if ($service['sms'] == 1) {
							$id = $insertData['insertID'];
							$data = $this->$serv_model->get_SMS_data($serviceID, $id);
							$mobile = $data['mobile'];
							$message = $data['message'];
							if ($this->config->item('sms_gateway') == '1') {
								$this->sms_model->sendSMS_MSG91($mobile, $message, '', $service['dlt_te_id']);
							} elseif ($this->config->item('sms_gateway') == '2') {
								$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
							} elseif ($this->config->item('sms_gateway') == '3') {
								$this->sms_model->sendSMS_SpearUC($mobile, $message, '', $service['dlt_te_id']);
							} elseif ($this->config->item('sms_gateway') == '4') {
								$this->sms_model->sendSMS_Asterixt($mobile, $message, '', $service['dlt_te_id']);
							} elseif ($this->config->item('sms_gateway') == '5') {
								$this->sms_model->sendSMS_Qikberry($mobile, $message, '', $service['dlt_te_id']);
							}
						}
						if ($service['serv_whatsapp'] == 1) {
							$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
							$this->sms_model->send_whatsApp_message($mobile, $smsData);
						}
						if ($service['email'] == 1 && $this->input->post('email') != '') {
							$to = $this->input->post('email');
							$data['name'] = $this->input->post('firstname');
							$data['mobile'] = $this->input->post('mobile');
							$data['passwd'] = $this->input->post('passwd');
							$data['company_details'] = $this->comp;
							$data['type'] = 3;
							$subject = "Reg: " . $this->comp['company_name'] . " registration";
							$message = $this->load->view('include/emailAccount', $data, true);
							$this->load->model('email_model');
							$sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
						}
						$this->session->set_flashdata('successMsg', 'Registration successful.');
						redirect('/user/login');
					} else {
						echo $this->db->last_query();
						echo $this->db->_error_message(); //exit;
						$this->session->set_flashdata('successMsg', 'Error in registration, please contact admin.');
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
    		            elseif($this->config->item('sms_gateway') == '3')
				            {
            	                 $this->sms_model->sendSMS_SpearUC($mobile,$message,'','');	
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
				}*/
				} else {
					$this->session->set_flashdata('errMsg', 'OTP provided by you is not valid.');
				}
			}
			redirect('/user/register_add');
		} else if ($type == 'update') {
			if (!$this->session->userdata('username')) {
				redirect("/user/login");
			} else {
				$this->load->library('upload');
				$this->db->trans_begin();
				$insertData = $this->registration_model->update_data($cus_id);
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					if ($this->session->userdata('pan_msg') == 2) {
						$this->session->unset_userdata("pan_msg");
						$this->session->set_flashdata('successMsg', 'Pan No. updated successfully. Please proceed for payment.');
						redirect("/paymt");
					} else {
						$this->session->set_flashdata('successMsg', 'Your profile has been updated successfully');
					}
				} else {
					$this->db->trans_rollback();
					$this->session->set_flashdata('errMsg', 'Error in updation.Please try again later...');
				}
				redirect('/user/register_update');
			}
		}
	}
	function exitingCustomer()
	{
		$schemes = $this->scheme_modal->no_avail_schemes();
		$pageType = array('page' => 'cust_enquiry', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content'] = $schemes;
		$data['fileName'] = self::VIEW_FOLDER . 'cust_enquiry';
		$this->load->view('layout/template', $data);
	}
	function exitingCusRegister()
	{
		session_start();
		if (strlen(trim($this->input->post('mobile'))) == 10) {
			//  if($this->input->post('scheme_code') != -1 || trim($this->input->post('acc1')) != '' || trim($this->input->post('acc2')) != '' || trim($this->input->post('acc3')) != '' || trim($this->input->post('acc4')) != '' || trim($this->input->post('acc5')) != '' || trim($this->input->post('acc6')) != '') 
			if ($this->input->post('scheme_code') != -1) {
				if ($this->session->userdata('OTP') == $this->input->post('otp')) {
					$this->session->unset_userdata('OTP');
					$this->db->trans_begin();
					$insertData = $this->registration_model->exitingCusRegister();
					if ($this->db->trans_status() === TRUE) {
						//$this->insert_Registration($insertData['acc_id']);
						$this->db->trans_commit();
						$serviceID = 1;
						$service = $this->services_modal->checkService($serviceID);
						if ($service['sms'] == 1) {
							$mobile = $this->input->post('mobile');
							$data['type'] = 4;
							$data['name'] = $this->input->post('firstname');
							$data['comp_name'] = $this->comp['company_name'];
							$message = $this->load->view('include/smsUser', $data, true);
							$senderid = $this->config->item('sms_senderid');
							$arr = array("@customer_mobile@" => $mobile, "@message@" => $message, "@senderid@" => $senderid);
							$url = $this->config->item('sms_url');
							$user_sms_url = strtr($url, $arr);
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $user_sms_url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
							$result = curl_exec($ch);
							curl_close($ch);
							unset($ch);
						}
						if ($service['email'] == 1 && $this->input->post('email') != '') {
							$to = $this->input->post('email');
							$data['firstname'] = $this->input->post('firstname');
							$data['mobile'] = $this->input->post('mobile');
							$data['passwd'] = $this->input->post('passwd');
							$data['type'] = 1;
							$data['company_details'] = $this->comp;
							$subject = "Reg:" . $this->comp['company_name'] . " online purchase plan activation";
							$message = $this->load->view('include/emailAccount', $data, true);
							$this->load->model('email_model');
							$sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
						}
						$this->db->trans_commit();
						$this->session->set_flashdata('successMsg', 'Online purchase plan account has been activated. Please login and pay your installments.');
						redirect('/user/login');
					} else {
						$serviceID = 1;
						$service = $this->services_modal->checkService($serviceID);
						if ($service['sms'] == 1) {
							$mobile = $this->input->post('mobile');
							$data['type'] = -4;
							$data['name'] = $this->input->post('firstname');
							$data['comp_name'] = $this->comp['company_name'];
							$message = $this->load->view('include/smsUser', $data, true);
							$senderid = $this->config->item('sms_senderid');
							$arr = array("@customer_mobile@" => $mobile, "@message@" => $message, "@senderid@" => $senderid);
							$url = $this->config->item('sms_url');
							$user_sms_url = strtr($url, $arr);
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $user_sms_url);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
							curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
							$result = curl_exec($ch);
							curl_close($ch);
							unset($ch);
						}
						if ($service['email'] == 1 && $this->input->post('email') != '') {
							$to = $this->input->post('email');
							$data['firstname'] = $this->input->post('firstname');
							$data['mobile'] = $this->input->post('mobile');
							$data['passwd'] = $this->input->post('passwd');
							$data['type'] = -1;
							$data['company_details'] = $this->comp;
							$subject = "Reg: " . $this->comp['company_name'] . " online purchase plan account activation";
							$message = $this->load->view('include/emailAccount', $data, true);
							$this->load->model('email_model');
							$sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
						}
						$this->db->trans_rollback();
						$this->session->set_flashdata('errMsg', 'Error in creating an account.Please try again later...');
					}
				} else {
					$this->session->set_flashdata('errMsg', 'OTP provided by you is not valid.');
				}
			} else {
				$this->session->set_flashdata('errMsg', 'Please fill all required fields.');
			}
		}
		redirect('/user/exitingCustomer');
	}
	function check_captcha()
	{
		session_start();
		if ($_POST['captcha_answer'] != $_SESSION['6_letters_code']) {
			$captcha_flag = 0;
			//redirect("C_userregistration/open_entryform/userregistration_model/add_new/0/Invalid Captcha");
		} else {
			$captcha_flag = 1;
		}
		echo $captcha_flag;
	}
	function contactSubmit()
	{
		session_start();
		if ($_POST['captchaAns'] == $_SESSION['captchaContact']) {
			$id_customer = $this->session->userdata('cus_id');
			$this->load->model('mobileapi_model');
			$data = $_POST;
			$title = ($data['reg'] == 1 ? 'Enquiry' : ($data['reg'] == 2 ? 'Suggestion' : ($data['reg'] == 3 ? 'Complaint' : 'Others')));
			$insData = array(
				'mobile'       => $data['custMobile'],
				'name'         => $data['custName'],
				'title'  	    => $title,
				'id_customer'  => ($id_customer != NULL ? $id_customer : NULL),
				'address'      => (isset($data['address']) ? $data['address'] : NULL),
				'date_of_birth' => (isset($data['date_of_birth']) ? $data['date_of_birth'] : NULL),
				'date_of_wed'  => (isset($data['date_of_wed']) ? $data['date_of_wed'] : NULL),
				'profession'   => (isset($data['profession']) ? $data['profession'] : NULL),
				'email'  	    => (isset($data['custEmail']) ? $data['custEmail'] : NULL),
				'date_add'     => date('Y-m-d H:i:s'),
				'type'         => $data['reg'], // Feedback type
				'enq_from'     => 1, // web app
				'comments'  	=> $data['custMessage']
			);
			if ($data['reg'] == 3) {
				$ticketno = $this->mobileapi_model->genTicketNo();
				$insData['ticket_no'] = $ticketno;
			}
			$result = $this->mobileapi_model->insCusFeedback($insData);
			if ($result['status'] == true) {
				$to = $this->comp['email'];
				// $cc = array("pavithra@vikashinfosolutions.com");	
				$cc = "";
				$subject = "Reg - " . $this->comp['company_name'] . " customer enquiry";
				$message = $this->load->view('include/emailContact', $insData, true);
				$sendEmail = $this->email_model->send_email($to, $subject, $message, $cc, "", "");
				if ($sendEmail)
					echo 1;
				else
					echo 2;
			} else {
				echo 2;
			}
		} else {
			echo 0;
		}
	}
	function terms()
	{
		$pageType = array('page' => 'terms', 'currency_symbol' => $this->comp['currency_symbol'], 'scheme_status' =>  $this->scheme_status, 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content'] = '';
		$data['fileName'] = self::VIEW_FOLDER . 'terms';
		$this->load->view('layout/template', $data);
	}
	function privacy()
	{
		$pageType = array('page' => 'privacy', 'currency_symbol' => $this->comp['currency_symbol'], 'scheme_status' =>  $this->scheme_status, 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content'] = '';
		$data['fileName'] = self::VIEW_FOLDER . 'privacy';
		$this->load->view('layout/template', $data);
	}
	function savingscheme()
	{
		$pageType = array('page' => 'terms', 'currency_symbol' => $this->comp['currency_symbol'], 'scheme_status' =>  $this->scheme_status, 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content'] = '';
		$data['fileName'] = self::VIEW_FOLDER . 'savingscheme';
		$this->load->view('layout/template', $data);
	}
	function insert_Registration($id_scheme_account)
	{
		$model = self::API_MODEL;
		$this->load->model($model);
		$account = $this->$model->getCustomerByID($id_scheme_account);
		if ($account) {
			$account[0]['transfer_jil'] = 'N';
			$account[0]['transfer_date'] = NULL;
			$account[0]['new_customer'] = 'N';
			$status = $this->$model->insert_CustomerReg($account[0]);
		}
		$this->load->database('default', true);
		return $status;
	}
	function send_sms($mobile, $message, $data = "")
	{
		if ($data == 1) {
			$url = $this->pro_data['promotion_url'];
			$senderid  = $this->pro_data['promotion_sender_id'];
		} else {
			$url = $this->sms_data['sms_url'];
			$senderid  = $this->sms_data['sms_sender_id'];
		}
		if (($this->pro_chk['debit_promotion'] != 0 && $data == 1)) {
			$arr = array("@customer_mobile@" => $mobile, "@message@" => str_replace(array("\n", "\r"), '', $message), "@senderid@" => $senderid);
			$user_sms_url = strtr($url, $arr);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $user_sms_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
			$result = curl_exec($ch);
			curl_close($ch);
			unset($ch);
			$status = $this->update_prosms();
			if ($status == 1) {
				return TRUE;
			}
			return FALSE;
		} else if (($this->sms_chk['debit_sms'] != 0)) {
			$arr = array("@customer_mobile@" => $mobile, "@message@" => str_replace(array("\n", "\r"), '', $message), "@senderid@" => $senderid);
			$user_sms_url = strtr($url, $arr);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $user_sms_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
			$result = curl_exec($ch);
			curl_close($ch);
			unset($ch);
			$status = $this->update_otp();
			if ($status == 1) {
				return TRUE;
			}
			return FALSE;
		} else {
			return FALSE;
		}
	}
	///otp debit_sms count
	function update_otp()
	{
		$query_validate = $this->db->query('UPDATE sms_api_settings SET debit_sms = debit_sms - 1 
				WHERE id_sms_api =1 and debit_sms > 0');
		if ($query_validate > 0) {
			return true;
		} else {
			return false;
		}
	}
	function update_prosms()
	{
		$query_validate = $this->db->query('UPDATE promotion_api_settings SET debit_promotion = debit_promotion - 1 WHERE id_promotion_api =1 and debit_promotion > 0');
		if ($query_validate > 0) {
			return true;
		} else {
			return false;
		}
	}
	///otp debit_sms count
	function maintenance()
	{
		if ($this->m_mode['maintenance_mode'] == 1) {
			$pageType = array('page' => 'terms', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['m_text'] = $this->m_mode['maintenance_text'];
			$this->load->view('maintenance', $data);
		} else {
			redirect('chitscheme');
		}
	}
	function remove_cus_img($file)
	{
		$id = $this->session->userdata('cus_id');
		$path = self::CUS_IMG_PATH . $id . "/" . $file . ".jpg";
		chmod(self::CUS_IMG_PATH, 0777);
		unlink($path);
		echo "Picture removed successfully";
	}
	function insertpan()
	{
		$panNo = array("pan" => $this->input->post('pan'));
		$data = $this->registration_model->updateCustomer($panNo);
		if ($data == 1) {
			$this->session->set_flashdata('successMsg', 'Please proceed for the payment.');
			redirect("/paymt");
		} else {
			$this->session->set_flashdata('errMsg', 'Unable to proceed your request.Please try after sometime..');
			redirect("/paymt");
		}
	}
	public function home()
	{
		$data['content'] = '';
		$pageType = array('page' => 'home', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::BASIC_FOLDER . 'home';
		$this->load->view('layout/template', $data);
	}
	public function aboutus()
	{
		$data['content'] = '';
		$pageType = array('page' => 'aboutus', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::BASIC_FOLDER . 'aboutus';
		$this->load->view('layout/template', $data);
	}
	public function faq()
	{
		$data['content'] = '';
		$pageType = array('page' => 'faq', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = 'chitscheme/faq';
		$this->load->view('layout/template', $data);
	}
	public function faq_mobile()
	{
		$data['content'] = '';
		$pageType = array('page' => 'faq', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$this->load->view('chitscheme/faq', $data);
	}
	public function termscond_mobile()
	{
		$pageType = array('page' => 'terms', 'currency_symbol' => $this->comp['currency_symbol'], 'scheme_status' =>  $this->scheme_status, 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content'] = '';
		//$data['fileName'] = self::VIEW_FOLDER.'terms';//hh
		$this->load->view('chitscheme/terms', $data);
	}
	/*	 public function termsconditions()
	{
		$data['content'] = '';
		$pageType = array('page' => 'termsconditions','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::BASIC_FOLDER.'terms';
		$this->load->view('layout/template', $data);
	}
	 public function privacypolicy()
	{
		$data['content'] = '';
		$pageType = array('page' => 'privacypolicy','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::BASIC_FOLDER.'privacypolicy';
		$this->load->view('layout/template', $data);
	}
	 public function contactus()
	{
		$data['content'] = '';
		$pageType = array('page' => 'contactus','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::BASIC_FOLDER.'contactus';
		$this->load->view('layout/template', $data);
	}
	public function faq()
	{
		$data['content'] = '';
		$pageType = array('page' => 'faq','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::BASIC_FOLDER.'faq';
		$this->load->view('layout/template', $data);
	}
    public function plans()
	{
		$data['content'] = '';
		$pageType = array('page' => 'plans','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::BASIC_FOLDER.'plans';
		$this->load->view('layout/template', $data);
	}
public function cancellation()    	{		$data['content'] = '';		$pageType = array('page' => 'cancellation','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);				$data['header_data'] = $pageType;		$data['footer_data'] = $pageType;		$data['fileName'] = self::BASIC_FOLDER.'cancellation';		$this->load->view('layout/template', $data);	}
public function refundpolicy()    	{		$data['content'] = '';		$pageType = array('page' => 'refundpolicy','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);				$data['header_data'] = $pageType;		$data['footer_data'] = $pageType;		$data['fileName'] = self::BASIC_FOLDER.'refundpolicy';		$this->load->view('layout/template', $data);	}
public function deliveryshipping()    	{		$data['content'] = '';		$pageType = array('page' => 'deliveryshipping','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);				$data['header_data'] = $pageType;		$data['footer_data'] = $pageType;		$data['fileName'] = self::BASIC_FOLDER.'deliveryshipping';		$this->load->view('layout/template', $data);	}
public function disclaimer()    	{		$data['content'] = '';		$pageType = array('page' => 'disclaimer','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);				$data['header_data'] = $pageType;		$data['footer_data'] = $pageType;		$data['fileName'] = self::BASIC_FOLDER.'disclaimer';		$this->load->view('layout/template', $data);	}
public function mobileapp()    	{		$data['content'] = '';		$pageType = array('page' => 'mobileapp','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);				$data['header_data'] = $pageType;		$data['footer_data'] = $pageType;		$data['fileName'] = self::BASIC_FOLDER.'mobileapp';		$this->load->view('layout/template', $data);	}
*/
	function referral_linksend()
	{
		$mobile = $this->input->post('mobile');
		$email = $this->input->post('email');
		$serv_model = self::SERV_MODEL;
		$email_model = self::EMAIL_MODEL;
		$serviceID = 15;
		// promotion sms //
		$promotion = 1;
		if ($mobile != '') {
			$id = $this->input->post('mobile');
			$data = $this->$serv_model->get_SMS_data($serviceID, $id, $serv_email, $serv_sms);
			$message = str_replace('##', $this->url, $data['message']);
			if ($data['serv_sms'] == 1) {
				//	$status= $this->send_sms($mobile,$message,$promotion);	
				if ($this->config->item('sms_gateway') == '1') {
					$status =  $this->sms_model->sendSMS_MSG91($mobile, $message, $promotion, $data['dlt_te_id']);
				} elseif ($this->config->item('sms_gateway') == '2') {
					$status =  $this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
				} elseif ($this->config->item('sms_gateway') == '3') {
					$this->sms_model->sendSMS_SpearUC($mobile, $message, '', $data['dlt_te_id']);
				} elseif ($this->config->item('sms_gateway') == '4') {
					$this->sms_model->sendSMS_Asterixt($mobile, $message, '', $data['dlt_te_id']);
				} elseif ($this->config->item('sms_gateway') == '5') {
					$this->sms_model->sendSMS_Qikberry($mobile, $message, '', $data['dlt_te_id']);
				}
			}
			if ($service['serv_whatsapp'] == 1) {
				$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
				$this->sms_model->send_whatsApp_message($mobile, $smsData);
			}
		}
		if ($email != '') {
			$data['type'] = 15;
			$data['company_details'] = $this->comp;
			$mbi = $this->input->post('mobile');
			$id = ($mbi != '' ? $mbi : $this->session->userdata("username"));
			$data['referral'] = $id;
			$data['name'] = $id;
			$datas = $this->$serv_model->get_SMS_data($serviceID, $id);
			$data['message'] = str_replace('##', $this->url, $datas['message']);
			$subject = "Reg: " . $this->comp['company_name'] . " Invite Referrals ";
			$message = $this->load->view('include/emailAccount', $data, true);
			$status = $this->$email_model->send_email($email, $subject, $message, "", "");
		}
		if ($status) {
			echo json_encode(array('status' => true));
		} else {
			echo json_encode(array('status' => false));
		}
	}
	// referalcode verification //
	public function checkreferalcode()
	{
		$referalcode = $this->registration_model->__decrypt($this->input->post('referal_code'));
		$referal_code = explode('/', $referalcode);
		$ref_code = explode('-', $referal_code[1]);
		if ($ref_code[0] != '') {
			$available = $this->scheme_modal->checkreferral_code($ref_code[0]);
		} else {
			array('status' => '0', 'referal_code' => 'NULL');
		}
		if ($available) {
			return array('status' => TRUE, 'referal_code' => $ref_code[0]);
		} else {
			return array('status' => '0', 'referal_code' => 'NULL');
		}
	}
	function referrals_page()
	{
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::BASIC_FOLDER . 'referral';
		$this->load->view('layout/template', $data);
	}
	function referral_linkshare()
	{
		$referral = $this->input->post('referral_id');
		$type = $this->input->post('referral_by');
		$serv_model = self::SERV_MODEL;
		$email_model = self::EMAIL_MODEL;
		$serviceID = 15;
		if ($referral != '' && $type == 1) {	// promotion sms //
			$otp_promotion = 1;
			$mobile = $this->input->post('referral_id');
			$id = $this->session->userdata("username");
			$data = $this->$serv_model->get_SMS_data($serviceID, $id);
			//$ullink=$this->shortenurl($this->url);
			$applink = $this->shortenurl($this->config->item('paystore_url'));
			$message = str_replace('##', $applink, $data['message']);
			//	$status= $this->send_sms($mobile,$message,$otp_promotion);	
			if ($this->config->item('sms_gateway') == '1') {
				$status =  $this->sms_model->sendSMS_MSG91($mobile, $message, $otp_promotion, '');
			} elseif ($this->config->item('sms_gateway') == '2') {
				$status =  $this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
			} elseif ($this->config->item('sms_gateway') == '3') {
				$status =  $this->sms_model->sendSMS_SpearUC($mobile, $message, '', '');
			} elseif ($this->config->item('sms_gateway') == '4') {
				$status =  $this->sms_model->sendSMS_Asterixt($mobile, $message, '');
			} elseif ($this->config->item('sms_gateway') == '5') {
				$status =  $this->sms_model->sendSMS_Qikberry($mobile, $message, '');
			}
		}
		if ($referral != '' &&  $type == 2) {
			$data['type'] = 15;
			$data['company_details'] = $this->comp;
			$to = $this->input->post('referral_id');
			$id = $this->session->userdata("username");
			$data['referral'] = $id;
			$data['name'] = "";
			$content = $this->$serv_model->get_SMS_data($serviceID, $id);
			$weblink = $this->shortenurl($this->url);
			$applink = $this->shortenurl($this->config->item('paystore_url'));
			$data['weblink'] = $weblink;
			$data['applink'] = $applink;
			$subject = "Reg: " . $this->comp['company_name'] . " Invite Referrals ";
			$message = $this->load->view('include/emailAccount', $data, true);
			$status = $this->$email_model->send_email($to, $subject, $message, "", "");
		}
		if ($status) {
			echo $status;
		} else {
			echo 0;
		}
	}
	// referalcode verification //
	function shortenurl($url)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, 'https://tinyurl.com/api-create.php?url=' . $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	function wallet_account_create($cus_id, $mobile)
	{
		$wallet_acc_no =  $this->services_modal->get_wallet_acc_number();
		$insertData = array(
			'id_customer' 	   => (isset($cus_id) && $cus_id != '' ? $cus_id : NULL),
			'id_employee' 	   =>  2,
			'wallet_acc_number' => (isset($wallet_acc_no) ? $wallet_acc_no : NULL),
			'issued_date' 	   => date('y-m-d H:i:s'),
			'remark' 		    => "Credits",
			'active'		        => 1
		);
		//inserting data                  
		$status = $this->services_modal->walletacc_insert($insertData);
		$wallAcc = $this->services_modal->get_walletacc($status['insertID']);
		$this->registration_model->insChitwallet($status['insertID'], $mobile, $cus_id);
		if ($status) {
			$serviceID = 8;
			$service =  $this->services_modal->checkService($serviceID);
			if ($service['sms'] == 1) {
				$id = $status['insertID'];
				$data = $this->services_modal->get_SMS_data($serviceID, $status['insertID']);
				$mobile = $data['mobile'];
				$message = $data['message'];
				if ($this->config->item('sms_gateway') == '1') {
					$this->sms_model->sendSMS_MSG91($mobile, $message, '', $service['dlt_te_id']);
				} elseif ($this->config->item('sms_gateway') == '2') {
					$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
				} elseif ($this->config->item('sms_gateway') == '3') {
					$this->sms_model->sendSMS_SpearUC($mobile, $message, '', $service['dlt_te_id']);
				} elseif ($this->config->item('sms_gateway') == '4') {
					$this->sms_model->sendSMS_Asterixt($mobile, $message, '', $service['dlt_te_id']);
				} elseif ($this->config->item('sms_gateway') == '5') {
					$this->sms_model->sendSMS_Qikberry($mobile, $message, '', $service['dlt_te_id']);
				}
			}
			if ($service['serv_whatsapp'] == 1) {
				$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
				$this->sms_model->send_whatsApp_message($mobile, $smsData);
			}
		}
	}
	public function get_branch()      // Branch wise Cus Reg In User //hh
	{
		$data = $this->scheme_modal->get_branch();
		echo json_encode($data);
	}
	// User complaint listing //hh
	function custComplaints()
	{
		$pageType = array('page' => 'cuscomplaints', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$this->load->model('mobileapi_model');
		$data['content'] = $this->mobileapi_model->get_custComplaints($this->session->userdata('cus_id'));
		//	print_r($data);
		$data['fileName'] = self::VIEW_FOLDER . 'cus_complaints';
		$this->load->view('layout/template', $data);
	}
	function custComplaintStatus($id_enquiry, $ticket_no)
	{
		$pageType = array('page' => 'cuscomplaints', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$this->load->model('mobileapi_model');
		$data['content'] = $this->mobileapi_model->get_custComplaintStatus($id_enquiry);
		$data['ticket_no'] = $ticket_no;
		//print_r($data);
		$data['fileName'] = self::VIEW_FOLDER . 'cus_comp_details';
		$this->load->view('layout/template', $data);
	}
	// User complaint listing //hh
	//DTH Request Form and Listing, Status//hh	
	function dth_form()
	{
		if (!$this->session->userdata('username')) {
			redirect("/user/login");
		} else {
			$dth = $this->registration_model->get_dthEmpryRecord();
			//  print_r($dth);exit;
			$data['content'] = $dth;
			$pageType = array('page' => 'DTH Appointment', 'currency_symbol' => $this->comp['currency_symbol'], 'scheme_status' =>  $this->scheme_status, 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER . 'dth';
			$this->load->view('layout/template', $data);
		}
	}
	public function dth()
	{
		$id_customer = $this->session->userdata('cus_id');
		$this->load->model('registration_model');
		$data = $_POST['dth_form'];
		//  print_r($data);exit;
		if ($data['type_DTH'] == '5') {
			//print_r('Dth');exit;
			$insData = array(
				'mobile'       => $data['mobiledth'],
				'name'         => $data['namedth'],
				'email'  	    => (isset($data['emaildth']) ? $data['emaildth'] : NULL),
				'id_customer'  => ($id_customer != NULL ? $id_customer : NULL),
				'address'  	=> (isset($data['addressdth']) ? $data['addressdth'] : NULL),
				'comments'  	=> (isset($data['commentsdth']) ? $data['commentsdth'] : NULL),
				'type'         => 5,
				'title'        => DTH,
				'enq_from'     => 1,
				'pincode'      => $data['pincodedth'],
				'date_add'     => date('Y-m-d H:i:s')
			);
			$result = $this->registration_model->insertdata($insData);
		}
		if ($data['type_EC'] == '6') {
			//  print_r('EC');exit;
			$insData = array(
				'mobile'       => $data['mobiledth'],
				'name'         => $data['namedth'],
				'email'  	    => (isset($data['emaildth']) ? $data['emaildth'] : NULL),
				'id_customer'  => ($id_customer != NULL ? $id_customer : NULL),
				'address'  	=> (isset($data['addressdth']) ? $data['addressdth'] : NULL),
				'comments'  	=> (isset($data['commentsdth']) ? $data['commentsdth'] : NULL),
				'type'         => 6,
				'title'        => 'Experience Center',
				'enq_from'     => 1,
				'pincode'      => $data['pincodedth'],
				'date_add'     => date('Y-m-d H:i:s')
			);
			$result = $this->registration_model->insertdata($insData);
			//print_r($result);exit;
		}
		//print_r($result);exit;
		$to = $this->comp['email'];
		$cc = array("hari@vikashinfosolutions.com");
		$subject = "Reg - " . $this->comp['company_name'] . " customer enquiry";
		$message = $this->load->view('include/emailContact', $insData, true);
		//print_r($result);exit;
		$sendEmail = $this->email_model->send_email($to, $subject, $message, $cc, "");    //DTH Request enquiry mail //hh
		$this->session->set_flashdata('successMsg', 'Thanks for making an appointment with us...');
		redirect("/dashboard");
	}
	function custDth()
	{
		$pageType = array('page' => 'custDth', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$this->load->model('mobileapi_model');
		$data['content'] = $this->mobileapi_model->get_custDth($this->session->userdata('cus_id'));
		//	print_r($data);
		$data['fileName'] = self::VIEW_FOLDER . 'cus_dth';
		$this->load->view('layout/template', $data);
	}
	function custDthStatus($id_enquiry)
	{
		$pageType = array('page' => 'custDth', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$this->load->model('mobileapi_model');
		$data['content'] = $this->mobileapi_model->get_custDthStatus($id_enquiry);
		//	$data['ticket_no'] = $ticket_no;
		//print_r($data);
		$data['fileName'] = self::VIEW_FOLDER . 'cus_dth_details';
		$this->load->view('layout/template', $data);
	}
	//DTH Request Form and Listing, Status//hh	    
	// Store Locatore based branch master in admin//hh
	function store_locatore()
	{
		$pageType = array('page' => 'Store Locator', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$this->load->model('scheme_modal');
		$data['content'] = $this->scheme_modal->storeLocatorBranches();
		$data['branch'] = $this->scheme_modal->get_storeLocatorBranches($record['id_branch']);   //branch wise search options in user//hh
		//echo"<pre>"; print_r($data['branch']);exit;
		$data['fileName'] = self::VIEW_FOLDER . 'store_locatore';
		$this->load->view('layout/template', $data);
	}
	// Store Locatore based branch master in admin//hh   	  
	//GG
	public function get_branch_rate()
	{
		$data['metal_rate'] = $this->login_model->metal_ratess($_POST['id_branch']);
		$data['discSettings'] = $this->login_model->discSettings();
		echo json_encode($data);
	}
	function sync_existing_data($mobile, $id_customer, $id_branch, $page)
	{
		if ($id_customer > 0 && strlen($mobile) == 10) {
			if (!is_dir($this->log_dir . '/existing')) {
				mkdir($this->log_dir . '/existing', 0777, true);
			}
			$log_path = $this->log_dir . '/existing/' . date("Y-m-d") . '.txt';
			$allow_sync = false;
			/*$last_sync_time = $this->registration_model->getLastSyncTime($mobile);
	       if(!empty($last_sync_time)){
	           $fifteen_min_earlier = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) - (15 * 60));
	           if(strtotime($last_sync_time) <= strtotime($fifteen_min_earlier)){
	               $allow_sync = true;
	           }else{
	               $allow_sync = false;
	           }
	       }else{
	           $allow_sync = true;
	       }
	       $this->registration_model->updateLastSyncTime($id_customer);*/
			if ($allow_sync) {
				$data['id_customer'] = $id_customer;
				$data['id_branch'] = $id_branch;
				$data['branch_code'] = ($id_branch > 0 && $this->config->item("integrationType") == 4 ? $this->registration_model->getBranchCode($id_branch) : NULL);
				$data['branchWise'] = 0;
				$data['mobile'] = $mobile;
				$data['added_by'] = 2;
				$res = $this->registration_model->insExisAcByMobile($data);
				$TESTRes = array("status" => "On ENTER", "e" => $this->db->_error_message(), "q" => $this->db->last_query(), "res" => $res, "data" => $data);
				$logData = "\n" . date('d-m-Y H:i:s') . "\n API : user \n Response : " . json_encode($TESTRes, true);
				file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
				if (sizeof($res) > 0) {
					$payData = $this->registration_model->syncPayData($res);
					if ((sizeof($payData['succeedIds']) > 0 || $payData['no_records'] > 0) && $this->db->trans_status() === TRUE) {
						$status = $this->registration_model->updateInterTableStatus($res, $payData['succeedIds']);
						if ($status === TRUE && $this->db->trans_status() === TRUE) {
							$this->db->trans_commit();
							/*echo $this->db->_error_message();
	    					echo $this->db->last_query();*/
							return array("status" => TRUE, "msg" => "Purchase Plan registered successfully");
						} else {
							$this->db->trans_rollback();
							$response = array("status" => FALSE, "e" => $this->db->_error_message(), "q" => $this->db->last_query(), "msg" => "Error in updating intermediate tables");
							$logData = "\n" . date('d-m-Y H:i:s') . "\n API : user \n Response : " . json_encode($response, true);
							file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
							return $response;
						}
					} else {
						$response = array("status" => FALSE, "e" => $this->db->_error_message(), "q" => $this->db->last_query(), "msg" => "Error in updating payment tables");
						$logData = "\n" . date('d-m-Y H:i:s') . "\n API : user \n Response : " . json_encode($response, true);
						file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
						$this->db->trans_rollback();
						return $response;
					}
				} else {
					$response = array("status" => FALSE, "id_customer" => $id_customer, "mobile" => $mobile, "branch_code" => $data['branch_code'], "msg" => "No records to update in scheme account tables");
					if ($this->db->trans_status() === TRUE) {
						$this->db->trans_commit();
					} else {
						echo $this->db->_error_message();
						$this->db->trans_rollback();
					}
					$logData = "\n" . date('d-m-Y H:i:s') . "\n API : user \n Response : " . json_encode($response, true);
					file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
					return $response;
				}
			} else {
				$logData = "\n" . date('d-m-Y H:i:s') . "\n API : user \n sync called less than 15 min. CUS ID : " . $id_customer . " | " . $mobile . " | BRN ID :" . $id_branch . " | " . $page;
				file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
				return $logData;
			}
		} else {
			return array("status" => FALSE, "msg" => "Invalid customer data");
		}
	}
	// GiftedCards list //hh 
	public function list_form()
	{
		$this->load->model('mobileapi_model');
		$mygifts = $this->mobileapi_model->getMyGifts($this->session->userdata('cus_id'), $this->session->userdata('mobile'));
		$mygifts = $this->mobileapi_model->getGiftedCards($this->session->userdata('cus_id'), $this->session->userdata('mobile'));
		$data['content'] = $mygifts;
		$pageType = array('page' => 'list', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER . 'gift_form';
		$this->load->view('layout/template', $data);
	}
	/*	public function gift_form()
	{
	    $this->load->model('mobileapi_model');
    	$mygifts = $this->mobileapi_model->getGiftedCards($this->session->userdata('cus_id'),$this->session->userdata('mobile'));
		$data['content'] = $mygifts;
		$pageType = array('page' => 'gifted_list','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);						
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER.'gifted_list';
		$this->load->view('layout/template',$data);	
	}*/
	function gifts_form()
	{
		if (!$this->session->userdata('username')) {
			redirect("/user/login");
		} else {
			$gift = $this->registration_model->get_giftEmpryRecords();
			//  print_r($dth);exit;
			$data['content'] = $gift;
			$pageType = array('page' => 'gift', 'currency_symbol' => $this->comp['currency_symbol'], 'scheme_status' =>  $this->scheme_status, 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER . 'gift';
			$this->load->view('layout/template', $data);
		}
	}
	public function gifts()
	{
		$id_customer = $this->session->userdata('cus_id');
		$this->load->model('registration_model');
		$data = $_POST['gifts_form'];
		// print_r($data);exit;
		$insData = array(
			'id_gift_card'  => $data['id_gift_card'],
			'trans_to_mobile'  => $data['trans_to_mobile'],
			'trans_from'         => ($id_customer != NULL ? $id_customer : NULL),
			'trans_to_email'  	    => (isset($data['trans_to_email']) ? $data['trans_to_email'] : NULL),
			'message'  	=> (isset($data['message']) ? $data['message'] : NULL)
		);
		// print_r($insData);exit;
		$result = $this->registration_model->insertdatas($insData);
		//print_r($result);exit;
		$this->session->set_flashdata('successMsg', 'Thanks for making an Gifts...');
		redirect("/user/list_form");
	}
	// GiftedCards list //hh 
	function getMyGifts()
	{
		$this->load->model('mobileapi_model');
		$result = $this->mobileapi_model->getMyGifts($this->session->userdata('cus_id'), $this->session->userdata('mobile'));
		$this->response($result, 200);
	}
	function getGiftedCards()
	{
		$this->load->model('mobileapi_model');
		$result = $this->mobileapi_model->getGiftedCards($this->session->userdata('cus_id'), $this->session->userdata('mobile'));
		$this->response($result, 200);
	}
	//Coin enquiry Form//HH
	function coin_enq_form()
	{
		if (!$this->session->userdata('username')) {
			$data['content'] = $coin_enquiry;
			$pageType = array('page' => 'Coin Enquiry ', 'currency_symbol' => $this->comp['currency_symbol'], 'scheme_status' =>  $this->scheme_status, 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER . 'coin_enquiry';
			$this->load->view('layout/template', $data);
		} else {
			$coin_enquiry = $this->registration_model->get_coinenq_EmpryRecord();
			//  print_r($coin_enquiry);exit;
			$data['content'] = $coin_enquiry;
			$pageType = array('page' => 'Coin Enquiry ', 'currency_symbol' => $this->comp['currency_symbol'], 'scheme_status' =>  $this->scheme_status, 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER . 'coin_enquiry';
			$this->load->view('layout/template', $data);
		}
	}
	public function coin_enquiry()
	{
		$email = $this->registration_model->get_coinenq_EmpryRecord();
		$id_customer = $this->session->userdata('cus_id');
		$this->load->model('registration_model');
		$data = $_POST['coin_enq_form'];
		//print_r($data);exit;
		$insData = array(
			'name'         => $data['name'],
			'mobile'       => $data['mobile'],
			'id_customer'  => ($id_customer != NULL ? $id_customer : NULL),
			'comments'  	=> (isset($data['comments']) ? $data['comments'] : NULL),
			'email'  	    => (isset($email['email']) ? $email['email'] : NULL),
			'date_add'     => date('Y-m-d H:i:s'),
			'title'         => 'Coin Enquiry',
			'type'         => 7, // Feedback type
			'enq_from'     => 1
		);
		//print_r($insData);exit;
		$result = $this->registration_model->insCusFeedback($insData);
		if ($result['status']) {
			$data = $_POST['coin_enq_form'];
			$insData = array(
				'id_enquiry'    => $result['insertID'],
				'product_name'  => 'Coin',
				'gram'        => $data['gram'],
				'coin_type'   => $data['coin_type'],
				'qty'          => $data['qty']
			);
			$result = $this->registration_model->insert_coin_data($insData);
		}
		if ($result['status']) {
			$to = $this->comp['email'];
			// $to = array("hari@vikashinfosolutions.com");	
			//$cc = array("hari@vikashinfosolutions.com");		
			$cc = "";
			$subject = "Reg - " . $this->comp['company_name'] . " customer enquiry";
			$message = $this->load->view('include/emailContact', $insData, true);
			$sendEmail = $this->email_model->send_email($to, $subject, $message, $cc, "");
			$this->session->set_flashdata('successMsg', 'Thanks for registering... Our executive will contact you soon.');
		} else {
			$this->session->set_flashdata('successMsg', 'Please try after sometime.');
		}
		redirect("user/coin_enq_form");
	}
	//Coin Enquiry View Details with status //HH
	function coin_enq_details($mobile)
	{
		$pageType = array('page' => 'coin_enq_details', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$this->load->model('mobileapi_model');
		$data['content'] = $this->mobileapi_model->get_custCoinEnq($mobile, $this->session->userdata('cus_id'));
		//print_r($data);
		$data['fileName'] = self::VIEW_FOLDER . 'coin_enq_details';
		$this->load->view('layout/template', $data);
	}
	function coin_enq_status($id_enquiry)
	{
		$pageType = array('page' => 'custDth', 'currency_symbol' => $this->comp['currency_symbol'], 'company_name' => $this->comp['company_name'], 'mobile' => $this->comp['mobile'], 'phone' => $this->comp['phone'], 'email' => $this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$this->load->model('mobileapi_model');
		$data['content'] = $this->mobileapi_model->get_custCoinEnqStatus($id_enquiry);
		//print_r($data);
		$data['fileName'] = self::VIEW_FOLDER . 'coin_enq_status';
		$this->load->view('layout/template', $data);
	}
	//Coin Enquiry View Details with status //
	// Khimji : Fetch user's offline data using ACME api & insert it in our database
	public function getDataFromOffline()
	{
		$cus_reference_no = $this->session->userdata('reference_no');
		$id_customer = $this->session->userdata('cus_id');
		$result = [];
		if ($this->config->item("integrationType") == 5 && $cus_reference_no > 0) {
			if (!is_dir($this->log_dir . '/khimji')) {
				mkdir($this->log_dir . '/khimji', 0777, true);
			}
			$log_path = $this->log_dir . '/khimji/' . date("Y-m-d") . '.txt';
			$this->load->model("integration_model");
			$postData = array(
				"customerCode"	=> $cus_reference_no,
				"uniqueCode" 	=> ""
			);
			$response = $this->integration_model->khimji_curl('user/getSchemeDetails', $postData);
			if ($response['status']) {
				$resData = $response['data'];
				if ($resData->status == 1 && $resData->errorCode == 0) {
					foreach ($resData->schemes as $data) {
						$acData = $data->schemeDetails;
						$isAccExist = $this->integration_model->isAccExist($acData);
						$id_scheme_account = '';
						$this->db->trans_begin();
						$cusData = $this->integration_model->getAccInsData($acData, $id_customer);
						if ($cusData['id_scheme'] > 0) {
							if ($isAccExist['status'] == FALSE) { // Account Not Exist
								// Insert Scheme Account Data
								$insAcc = array(
									'id_customer'       => $id_customer,
									'id_scheme'         => $cusData['id_scheme'],
									'start_date'        => date('Y-m-d', strtotime(str_replace('/', '-', $acData->joiningDate))),
									'maturity_date'     => date('Y-m-d', strtotime(str_replace('/', '-', $acData->maturityDate))),
									'scheme_acc_number' =>	$acData->schemeNo,
									'account_name'      => $cusData['ac_name'],
									'is_new'		     => 'N',
									'active'            => 1,
									'date_add'          => date('Y-m-d H:i:s'),
									'added_by' 		 => 0,
									'id_branch'         => $cusData['id_branch'],
									'firstPayment_amt'  => (isset($acData->installmentAmount) ? $acData->installmentAmount : NULL),
									'pan_no'		     => (isset($acData->panNo) ? $acData->panNo : NULL),
								);
								$id_scheme_account = $this->integration_model->insertData($insAcc, 'scheme_account');
								if ($id_scheme_account > 0) {
									$result["SchAc_Success"][] = "Created successfully ID : " . $id_scheme_account;
								} else {
									$result["SchAc_Error"][] = $this->db->_error_message();
								}
							} else {
								$id_scheme_account = $isAccExist['id_scheme_account'];
								$result["SchAc_Error"][] = "Scheme account Already Exist ID : " . $id_scheme_account . " scheme No: " . $acData->schemeNo;
							}
							if ($id_scheme_account > 0) {
								foreach ($data->installmentList as $pay) {
									$isPayExist = $this->integration_model->isPayExist($pay, $id_customer);
									if ($isPayExist['status'] == false) {
										$paid_on = explode("/", $pay->date);
										$insPay = array(
											"id_scheme_account"	 => $id_scheme_account,
											"payment_amount" 	 => (isset($pay->amount) ? $pay->amount : NULL),
											"gst" 	    		 => 0,
											"gst_type" 	    	 => 0,
											"gst_amount"    	 => 0,
											"payment_type" 	     => 'Offline',
											"payment_mode" 	     => 'OFL',
											"is_offline"	     => 1,
											"no_of_dues" 	     => 1,
											"actual_trans_amt"   => (isset($pay->amount) ? $pay->amount : NULL),
											"act_amount"         => (isset($pay->amount) ? $pay->amount : NULL),
											"receipt_no"         => (isset($pay->voucherNo) ? $pay->voucherNo : NULL),
											"date_payment"       => date('Y-m-d', strtotime(str_replace('/', '-', $pay->date))),
											"date_add" 		     => date('Y-m-d H:i:s'),
											"added_by"			 =>  0,
											"payment_status"     => 1,
											"id_branch"    		 => $cusData['id_branch'],
											"due_type"           => 'ND',
											"due_month"          => $paid_on[1],
											"due_year"           => $paid_on[2]
										);
										$id_payment = $this->integration_model->insertData($insPay, 'payment');
										$updData = array("last_sync_time" => date('Y-m-d H:i:s'));
										$this->integration_model->updateData($updData, 'id_customer', $id_customer, 'customer');
										if ($id_payment > 0) {
											$result["Pay_Success"][] =  "Payment created id : " . $id_payment;
										} else {
											$result["Pay_Error"][] =  $this->db->_error_message();
										}
									} else {
										$result["Pay_Error"][] =   "Payment Already Exist ID : " . $isPayExist['id_payment'] . " Voucher No: " . $pay->voucherNo . "<br/>";
									}
									//echo "<pre>";print_r($pay);
								}
							} else {
								$result["SchAc_Error"][] = "Invalid scheme account id";
							}
						} else {
							$result["SchAc_Error"][] = "Invalid scheme code :" . $acData->schemeCode;
						}
						if ($this->db->trans_status() === TRUE) {
							$this->db->trans_commit();
						} else {
							$this->db->trans_rollback();
						}
					}
				} else {
					$logData = "\n" . date('d-m-Y H:i:s') . "\n API : user/getSchemeDetails \n Response : " . json_encode($resData, true) . " \n Result : " . json_encode($result, true);
					file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
				}
			} else {
				$logData = "\n" . date('d-m-Y H:i:s') . "\n API : user/getSchemeDetails (Curl Failed) \n Response : " . json_encode($response, true) . " \n Result : " . json_encode($result, true);
				file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
			}
		}
		$result["CUS_REF_NO"] =  "CUS REF NO : " . $cus_reference_no;
		echo json_encode($result);
	}
	// .Khimji : Fetch user's offline data using ACME api & insert it in our database
}
