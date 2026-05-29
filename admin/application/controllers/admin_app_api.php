<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Retail Admin app api's
 */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
require(APPPATH . 'libraries/REST_Controller.php');
require_once(APPPATH . 'libraries/dompdf/autoload.inc.php');

use Dompdf\Dompdf;

class Admin_app_api extends REST_Controller
{
	const ADM_MODEL = "ret_app_api_model";
	const EST_MODEL = "ret_estimation_model";
	const EST_PATH  = 'assets/estimation/';
	const BT_PATH  = 'assets/branch_transfer/';
	const CUS_IMG_PATH = 'assets/img/customer/';
	const WISH_IMG_PATH = 'assets/img/wishlist/';
	const TAGIMG_PATH  = 'assets/img/tag/';
	const SUB_DES_IMG_PATH  = 'assets/img/';
	const DES_IMG_PATH  = 'assets/img/';
	const ENQ_IMG_PATH  = 'assets/img/enquiry/';
	const HORIZONTAL_LINE = array(
		'HR_58MM' =>  '================================================',
		'HR2_58MM' => '************************************************',
		'HR3_58MM' => '------------------------------------------------',
	);
	function __construct()
	{
		parent::__construct();
		$this->response->format = 'json';
		$this->load->model(self::ADM_MODEL);
		$this->load->model('services_model');
		$this->load->model('email_model');
		$this->load->model('sms_model');
		$this->load->model('ret_app_api_model');
		$this->load->model('ret_estimation_model');
		$this->load->model('admin_settings_model');
		$this->load->model('customer_model');
		$this->load->model('ret_wishlist_model');
		$this->load->model('ret_reports_model');
		$this->load->model("ret_order_model");
		$this->load->model("ret_catalog_model");
		$this->load->model("ret_billing_model");
		$this->load->model("ret_brntransfer_model");


		ini_set('date.timezone', 'Asia/Calcutta');
		// Android
		$this->current_android_version = "1.0.0";
		$this->new_android_version = "1.0.1";
		// iOS
		$this->current_ios_version = "1.0.0";
		$this->new_ios_version = "1.0.1";
	}

	/**
	 * General functions
	 */
	//funtion to get post values
	function get_values()
	{
		return (array)json_decode(file_get_contents('php://input'));
	}

	//array sorting
	function array_sort($array, $on, $order = SORT_ASC)
	{
		$new_array = array();
		$sortable_array = array();
		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}
			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}
			foreach ($sortable_array as $k => $v) {
				//$new_array[$k] = $array[$k];
				$new_array[] = $array[$k];
			}
		}
		return $new_array;
	}

	public function __encrypt($str)
	{
		return base64_encode($str);
	}

	/**
	 * CRM Admin app api's
	 */

	function getVersion_get()
	{
		$version['android'] = $this->current_android_version;
		$version['new_android_ver'] = $this->new_android_version;
		$version['ios'] = $this->current_ios_version;
		$version['new_ios_ver'] = $this->new_ios_version;
		$version['comp'] = $this->ret_app_api_model->company_details();
		$version['settings'] = $this->ret_app_api_model->getChitSettings();
		$version['mode'] = $version['comp']['maintenance_mode'];
		$version['text'] = $version['comp']['maintenance_text']; //maintaince text
		$version['msg'] =  "New version available."; //New version text
		$version['showpopup'] = 0;
		$version['popupimg'] = "";
		// $version['maintain_ver'] = "v3.1.0.1";

		$this->response($version, 200);
	}


	/* Start of Master Function */
	function getBranchList_get()
	{
		$model = self::ADM_MODEL;
		$data = $this->$model->getBranches("list");
		$all_branch[] = [
			'id_branch' => 0,'name' => 'ALL','is_default' => 1,'is_ho' => 0
	   ];
	   $result = array_merge($all_branch,$data);
		$this->response($result, 200);
	}

	function getBranchEmployees_get()
	{
		$model = self::ADM_MODEL;
		$result = $this->$model->getBranchEmployees($this->get('id_branch'));
		echo json_encode($result);
	}

	function getCustBySearch_post()
	{
		$model = self::ADM_MODEL;
		$postdata = $this->get_values();
		$data = $this->$model->getAvailableCustomers($postdata['searchTxt']);
		echo json_encode($data);
	}

	function getCurrencyAndSettings_post()
	{
		$model = self::ADM_MODEL;
		$postdata = $this->get_values();
		$id_metalrates = isset($postdata['id_metalrates']) ? $postdata['id_metalrates'] : 0;
		$result = $this->$model->currencyAndSettings($postdata['id_branch'], $id_metalrates);
		$this->response($result, 200);
	}
	/* End of Master Function */


	/* Start Of Login Functions	*/

	function checkDevice()
	{
		// RETURNS 0 for desktop, 1 for mobile, 2 for tablets
		if (is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "mobile"))) {
			return is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "tablet")) ? 2 : 1;
		} else {
			return 0;
		}
	}

	function verifyLoginOTP_post()
	{
		$model = self::ADM_MODEL;
		$this->load->model("log_model");
		$this->load->model("chitadmin_model");
		$this->load->model("admin_settings_model");
		$postdata = $this->get_values();
		$username 	= $postdata['username'];
		$userpwd  	= $postdata['password'];
		$input_otp  = $postdata['input_otp'];
		$id_branch  = $postdata['id_branch'];
		$employee 	= $this->$model->get_emp_by_username($username);
		$company 	= $this->admin_settings_model->get_company();
		$branch_set = $this->chitadmin_model->branch_settings();

		if ($postdata['input_otp'] ==  $postdata['sys_otp']) {
			if (time() >= $postdata['sys_otp_exp']) {
				$result = array('result' => FALSE, 'msg' => 'OTP has been expired');
			} else {
				$otp 		= $this->$model->select_otp($input_otp);
				$login_data['is_verified']	= '1';
				$login_data['verified_time'] = date("Y-m-d H:i:s");
				$status 	= $this->$model->otp_update($login_data, $otp['id_otp']);
				if ($id_branch > 0) {
					$branch_data  = $this->admin_settings_model->get_branch_by_id($id_branch);
					$branch_name = $branch_data['name'];
				} else {
					$branch_name = "";
				}
				$deviceType = $this->checkDevice();
				$menus = $this->$model->getUserMenus($employee['id_employee']);
				$emp_setting = $this->ret_estimation_model->get_employee_settings($employee['id_employee']);
				$log_data = array(
					'id_employee'   => $employee['id_employee'],
					'login_on'      => date("Y-m-d H:i:s"),
					'id_otp'		=> $otp['id_otp'],
					'ip_address'    => NULL,
					'device_type'   => ($deviceType == 0 ? "DESKTOP" : ($deviceType == 1 ? "MOBILE" : ($deviceType == 2 ? "TABLET" : "DESKTOP")))
				);
				$log = $this->log_model->log("insert", "", $log_data);
				$data = array(
					'username'  		=> $employee['username'],
					'uid'       		=> $employee['id_employee'],
					'profile'   		=> $employee['id_profile'],
					'emp_code'   		=> $employee['emp_code'],
					'is_logged' 		=> true,
					'id_log'    		=> $log['insertID'],
					'logged_on' 		=> date("Y-m-d H:i:s"),
					'currency_symbol'	=> $company['currency_symbol'],
					'mob_code'			=> $company['mob_code'],
					'mob_no_len'		=> $company['mob_no_len'],
					'branch_settings'   => $branch_set['branch_settings'],
					'filerbybranch'	    => ($branch_set['branch_settings'] == 1 ? $id_branch == 0 ? '0' : '1' : 'nobranch'),
					'id_branch'   		=> $id_branch,
					'branch_name'  		=> $branch_name,
					'branchWiseLogin'   => $branch_set['branchWiseLogin'],
					'is_branchwise_cus_reg' => $branch_set['is_branchwise_cus_reg'],
					'is_branchwise_rate' => $branch_set['is_branchwise_rate'],
					'menus' 			=> $menus,
					'emp_setting' 		=> $emp_setting
				);
				$result = array('result' => TRUE, 'type' => 'logged', 'msg' => 'Logged In successfully...', 'empdata' => $data);
				$this->response($result, 200);
			}
		} else {
			$result = array('result' => FALSE, 'msg' => 'Invalid OTP');
		}
		$this->response($result, 200);
	}

	//to check the mobile number already registered
	function isNumberRegistered($data)
	{
		$model = self::ADM_MODEL;
		$mobile = $data['mobile'];
		$email =  $data['email'];
		$m_exist =	$this->$model->isMobileExists($mobile);
		//$e_exist =	$this->$model->clientEmail($email);
		$e_exist =	FALSE;
		$limit = $this->services_model->limitDB('get', '1');
		$count = $this->services_model->customer_count();
		$limit_exceed = 0;
		if ($limit['limit_cust'] == 1 && $count >= $limit['cust_max_count']) {
			$limit_exceed = 1;
		}
		$is_reg = ($m_exist ? TRUE : ($e_exist ? TRUE : FALSE));

		$result = array(
			'is_reg' => $is_reg,
			'msg'	 => ($is_reg ? ($m_exist && $e_exist) ? "Mobile and Email already registered" : ($m_exist ? "Mobile already registered" : "E-mail already Registered") : ($limit_exceed == 1 ? 'Temporarily New user registration is unavailable, Kindly contact Customer care...' : FALSE))
		);
		return $result;
	}

	public function authenticate_post()
	{
		$this->load->model("log_model");
		$this->load->model("chitadmin_model");
		$this->load->model("admin_settings_model");
		$model = self::ADM_MODEL;
		$postdata = $this->get_values();
		$username = $postdata['username'];
		$userpwd  = $postdata['password'];
		$id_branch = $postdata['id_branch'];
		$device_id = trim($postdata['device']);
		$device_type = $postdata['device_type'];
		$user_agent =  $_SERVER['HTTP_USER_AGENT'];
		//$device_id != "" && 
		if ($username != '' && $userpwd != '') {
			//$has_device = $this->$model->check_device_exists($username, $device_id, $device_type, $user_agent);
			$has_device['status'] = true;
			if ($has_device['status']==1 && $has_device['status']!='') {

				$employee	= $this->$model->isValidLogin($username, $this->__encrypt($userpwd));
				/*$emp_id   = $this->$model->authenticate_user_id($username,$userpwd);
					/$employee = $this->$model->get_emp_by_username($username);*/
				$company 	= $this->admin_settings_model->get_company();
				$branch_set = $this->chitadmin_model->branch_settings();
				$finance    = $this->admin_settings_model->get_financial_data();
				$deviceType = $this->checkDevice();
				//Login Branches
				$branch_id  = array(explode(',', $employee['login_branches']));
				$branch_login = true;
				if ($branch_set['login_branch'] == 1) {
					if ($branch_id[0][0] == 0) {
						$branch_login = true;
					} else {
						foreach ($branch_id[0] as $branch) {
							if ($branch == $id_branch) {
								$branch_login = true;
								break;
							} else {
								$branch_login = FALSE;
							}
						}
					}
				}
				if ($id_branch > 0) {
					$branch_data  = $this->admin_settings_model->get_branch_by_id($id_branch);
					$branch_name = $branch_data['name'];
				} else {
					$branch_name = "";
				}

				//Login Branches

				if ($branch_login) {
					if ($employee['is_valid']) {
						if ($employee['id_profile'] == 1 || $employee['id_profile'] == 2 || $employee['id_profile'] == 3) {
							if ($employee['req_otplogin'] == 1) { // Profile wise OTP required
								$loginOTP_exp = $this->$model->prof_wise_loginotp_exp();
								$mobile	  =	$employee['mobile'];
								$firstname =	$employee['firstname'];
								$OTP = mt_rand(100001, 999999);
								$message = "Your OTP for Admin Panel Login is " . $OTP . "  Will expire within " . $loginOTP_exp . " Sec. ";
								$this->chitadmin_model->send_sms($mobile, $message);
								$otp['otp_gen_time'] = date("Y-m-d H:i:s");
								$otp['otp_code'] = $OTP;
								$status = $this->ret_app_api_model->insertData($otp, 'otp');
								$otp['sys_otp_exp'] = time() + $loginOTP_exp;
								$result = array('result' => TRUE, 'type' => 'otp', 'msg' => 'OTP has been sent', 'otp' => $otp, 'mobile' => $mobile);
								$this->response($result, 200);
							} else {
								$menus = $this->$model->getUserMenus($employee['id_employee']);
								$emp_setting = $this->ret_estimation_model->get_employee_settings($employee['id_employee']);
								$log_data = array(
									'id_employee'   => $employee['id_employee'],
									'id_branch'   	=> $id_branch == 0 ? NULL : $id_branch,
									'login_on'      => date("Y-m-d H:i:s"),
									'ip_address'    => $this->session->userdata('ip_address'),
									'device_type'   => ($deviceType == 0 ? "DESKTOP" : ($deviceType == 1 ? "MOBILE" : ($deviceType == 2 ? "TABLET" : "DESKTOP")))
								);
								$log = $this->log_model->log("insert", "", $log_data);

								$data = array(
									'fin_year_code'    	=> $finance['fin_year_code'],
									'username'  		=> $employee['username'],
									'uid'       		=> $employee['id_employee'],
									'profile'   		=> $employee['id_profile'],
									'emp_code'   		=> $employee['emp_code'],
									'is_logged' 		=> true,
									'id_log'    		=> $log['insertID'],
									'logged_on' 		=> date("Y-m-d H:i:s"),
									'currency_symbol'	=> $company['currency_symbol'],
									'mob_code'			=> $company['mob_code'],
									'mob_no_len'		=> $company['mob_no_len'],
									'branch_settings'   => $branch_set['branch_settings'],
									'filerbybranch'	    => ($branch_set['branch_settings'] == 1 ? $employee['id_branch'] == 0 ? '0' : '1' : 'nobranch'),
									'id_branch'   		=> $employee['id_branch'],
									'branch_name'  		=> $branch_name,
									'branch_id'         => $employee['login_branches'],
									'branchWiseLogin'   	=> $branch_set['branchWiseLogin'],
									'is_branchwise_cus_reg' => $branch_set['is_branchwise_cus_reg'],
									'is_branchwise_rate'   	=> $branch_set['is_branchwise_rate'],
									'menus' => $menus,
									'emp_setting' => $emp_setting
								);
								$result = array('result' => TRUE, 'type' => 'logged', 'msg' => 'Logged In successfully...', 'empdata' => $data);
								$this->response($result, 200);
							}
						} else {
							if ($branch_set['isOTPReqToLogin'] == 1) {
								$loginOTP_exp = $this->$model->loginOTP_exp();
								$mobile	  =	$employee['mobile'];
								$firstname =	$employee['firstname'];
								$OTP = mt_rand(100001, 999999);
								$message = "Your OTP for Admin Panel Login is " . $OTP . "  Will expire within " . $loginOTP_exp . " Sec. ";
								$this->chitadmin_model->send_sms($mobile, $message);
								$otp['otp_gen_time'] = date("Y-m-d H:i:s");
								$otp['otp_code'] = $OTP;
								$status = $this->ret_app_api_model->insertData($otp, 'otp');
								$result = array('result' => TRUE, 'type' => 'otp', 'msg' => 'OTP has been sent', 'otp' => $otp, 'mobile' => $mobile);
								$this->response($result, 200);
							} else {
								$menus = $this->$model->getUserMenus($employee['id_employee']);
								$emp_setting = $this->ret_estimation_model->get_employee_settings($employee['id_employee']);
								$log_data = array(
									'id_employee'   => $employee['id_employee'],
									'id_branch'   	=> $id_branch == 0 ? NULL : $id_branch,
									'login_on'      => date("Y-m-d H:i:s"),
									'ip_address'    => $this->session->userdata('ip_address'),
									'device_type'   => ($deviceType == 0 ? "DESKTOP" : ($deviceType == 1 ? "MOBILE" : ($deviceType == 2 ? "TABLET" : "DESKTOP")))
								);
								$log = $this->log_model->log("insert", "", $log_data);
								$data = array(
									'username'  		=> $employee['username'],
									'uid'       		=> $employee['id_employee'],
									'profile'   		=> $employee['id_profile'],
									'emp_code'   		=> $employee['emp_code'],
									'is_logged' 		=> true,
									'id_log'    		=> $log['insertID'],
									'logged_on' 		=> date("Y-m-d H:i:s"),
									'currency_symbol'	=> $company['currency_symbol'],
									'mob_code'			=> $company['mob_code'],
									'mob_no_len'		=> $company['mob_no_len'],
									'branch_settings'   => $branch_set['branch_settings'],
									'filerbybranch'	    => ($branch_set['branch_settings'] == 1 ? $employee['id_branch'] == 0 ? '0' : '1' : 'nobranch'),
									'id_branch'   		=> $employee['id_branch'],
									'branch_id'         => $employee['login_branches'],
									'branch_name'  		=> $branch_name,
									'branchWiseLogin'   	=> $branch_set['branchWiseLogin'],
									'is_branchwise_cus_reg' => $branch_set['is_branchwise_cus_reg'],
									'is_branchwise_rate'   	=> $branch_set['is_branchwise_rate'],
									'menus' => $menus,
									'emp_setting' => $emp_setting
								);
								$result = array('result' => TRUE, 'type' => 'logged', 'msg' => 'Logged In successfully...', 'empdata' => $data);
								$this->response($result, 200);
							}
						}
					} else {
						$result = array('result' => FALSE, 'msg' => 'Invalid Username or Password');
						$this->response($result, 200);
					}
				} else {
					$result = array('result' => FALSE, 'msg' => 'Invalid Branch');
					$this->response($result, 200);
				}
			} else {
				$result = array('result' => FALSE, 'msg' => $has_device['message']);
				$this->response($result, 200);
			}
		} else {
			$message = "";

			if ($device_id == "") {

				$message = "Device ID required. Please contact administrator";
			} else {

				$message = "Username and Password required";
			}

			$result = array('result' => FALSE, 'msg' => $message);

			$this->response($result, 200);
		}
	}
	// End of Login Functions

	/* Start of Customer Master functions */
	function createCustomer_post()
	{
		$model = self::ADM_MODEL;

		$data = $this->get_values();

		$datetime = date('Y-m-d H:i:s');

		$result = '';

		$is_already_reg = $this->isNumberRegistered($data);

		if ($is_already_reg['is_reg']) { // Already registered

			$res = array("msg" => $is_already_reg['msg'], "status" => FALSE);

			$this->response($res, 200);
			return;
		}

		$pan_avail = $this->check_pan($data);

		if (!$pan_avail['status']) { // Pan already registered
			$res = array("msg" => $pan_avail['msg'], "status" => FALSE);
				$this->response($res, 200);
			return;
		}

		$aadhar_avail = $this->check_aadhar($data);

		if (!$aadhar_avail['status']) { // Aadhar already registered
			$res = array("msg" => $aadhar_avail['msg'], "status" => FALSE);
				$this->response($res, 200);
			return;
		}

		$gst_avail = $this->check_gst($data);

		if (!$gst_avail['status']) { // GST already registered
			$res = array("msg" => $gst_avail['msg'], "status" => FALSE);
				$this->response($res, 200);
			return;
		}

		$customer = array(
			'info' => array(
				'firstname' => ucwords($data['firstname']),
				'lastname'  => ucfirst($data['lastname']),
				'gender'	=> $data['gender'],
				'cus_type'  => !empty($data['cus_type']) ? $data['cus_type'] : 1,
				'mobile'    => $data['mobile'],
				'username'    => $data['mobile'],
				'email'     => $data['email'],
				'passwd'    => $this->__encrypt($data['mobile']),
				'active'    => 1,
				'date_add'  => date('Y-m-d H:i:s'),
				'added_by'  => 4,	// Retail App
				'id_employee' 	=> (!empty($data['id_employee']) ? $data['id_employee'] : NULL),
				'id_branch' 	=> (!empty($data['id_branch']) ? $data['id_branch'] : NULL),
				'id_village' 	=> (!empty($data['id_village']) ? $data['id_village'] : NULL),
				'date_of_birth' => (isset($data['date_of_birth']) ? $this->convertDateFormat($data['date_of_birth'], 'd-m-Y', 'Y-m-d') : NULL),
				'date_of_wed' 	=> (isset($data['date_of_wed']) ? $this->convertDateFormat($data['date_of_wed'], 'd-m-Y', 'Y-m-d') : NULL),
				'send_promo_sms' => (!empty($data['send_promo_sms']) ? $data['send_promo_sms'] : 0),
				'is_vip' 		=> (!empty($data['is_vip']) ? $data['is_vip'] : 0),
				'religion' 		=> (!empty($data['religion']) ? $data['religion'] : 0),
				'pan'	 		=> (!empty($data['pan']) ? $data['pan'] : NULL),
				'aadharid'	 		=> (!empty($data['aadharid']) ? $data['aadharid'] : NULL),
				'gst_number'	=> (!empty($data['gst_number']) ? $data['gst_number'] : NULL),
				'nominee_name'	=> (!empty($data['nominee_name']) ? $data['nominee_name'] : NULL),
				'nominee_relationship'	=> (!empty($data['nominee_relationship']) ? $data['nominee_relationship'] : NULL),
				'nominee_mobile' => (!empty($data['nominee_mobile']) ? $data['nominee_mobile'] : NULL),
				'voterid'		=> (!empty($data['voterid']) ? $data['voterid'] : NULL),
				'rationcard'	=> (!empty($data['rationcard']) ? $data['rationcard'] : NULL),
				'comments'		=> (!empty($data['comments']) ? $data['comments'] : NULL),
				'spouse_name'		=> (!empty($data['spouse_name']) ? $data['spouse_name'] : NULL),
				'spouse_dob'		=> (!empty($data['spouse_dob']) ? $data['spouse_dob'] : NULL),
				'refer_person'		=> (!empty($data['refperson']) ? $data['refperson'] : NULL),
				'refer_mobile'		=> (!empty($data['refmobile']) ? $data['refmobile'] : NULL),
				'passport'			=> (!empty($data['passport']) ? $data['passport'] : NULL),
				'dl_number'			=> (!empty($data['dl_number']) ? $data['dl_number'] : NULL),
			),
			'address' => array(
				'address1'			=> (!empty($data['address1']) ? $data['address1'] : NULL),
				'address2'			=> (!empty($data['address2']) ? $data['address2'] : NULL),
				'id_country'		=> (!empty($data['id_country']) ? $data['id_country'] : NULL),
				'id_state'			=> (!empty($data['id_state']) ? $data['id_state'] : NULL),
				'id_city'			=> (!empty($data['id_city']) ? $data['id_city'] : NULL),
				'pincode'			=> (!empty($data['pincode']) ? $data['pincode'] : NULL)
			)
		);

		$this->db->trans_begin();

		$status = $this->$model->insert_customer($customer);

		if ($status['status']) {

			$insertID = $status['insertID'];

			$childrendata = $data['childrendata'];

			foreach ($childrendata as $child) {

				$child = (array)$child;

				$childrens = [

					"cus_id"		=> $insertID,

					"children_name" => $child["child_name"],

					"children_dob"  => $child["child_dob"]

				];

				$this->$model->insertData($childrens, 'customer_childrens');
			}
		}

		if ($this->db->trans_status() == TRUE) {
			$customer['info']['id_customer'] = $status['insertID'];
			$wallet_acc =  $this->$model->wallet_accno_generator();
			if ($wallet_acc['wallet_account_type'] == 1) {
				$this->wallet_account_create($status['insertID'], $data['mobile']);
			}
			$id = $status['insertID'];
			if ($this->db->trans_status() == TRUE) {
				$log_data = array(
					'id_log'     => $data['id_log'],
					'event_date' => date("Y-m-d H:i:s"),
					'module'     => 'Customer',
					'operation'  => 'Add',
					'record'     => $status['insertID'],
					'remark'     => 'Customer added successfully',
					'event_through'     => 3, // 1 - Web admin, 2 - CRM mobile admin, 3 - Retail mobile admin
				);

				$this->$model->insertData($log_data, 'log_detail');
				$this->db->trans_commit();
				$serviceID = 1;
				$company = $this->$model->company_details();
				$service = $this->services_model->checkService($serviceID);
				if ($service['sms'] == 1) {
					$data = $this->services_model->get_SMS_data($serviceID, $id);
					$mobile = $data['mobile'];
					$message = $data['message'];
					if ($this->config->item('sms_gateway') == '1') {
						//$this->sms_model->sendSMS_MSG91($mobile,$message);
					} elseif ($this->config->item('sms_gateway') == '2') {
						$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
					} elseif ($this->config->item('sms_gateway') == '3') {
						$this->sms_model->sendSMS_SpearUC($mobile, $message, '', '');
					}
				}
				if ($service['serv_whatsapp'] == 1) {
					$this->services_model->send_whatsApp_message($mobile, $message);
				}
				if ($service['email'] == 1 && $customer['email'] != '') {
					$to = $customer['email'];
					$data['name'] = $customer['firstname'];
					$data['mobile'] = $customer['mobile'];
					$data['passwd'] = $this->__decrypt($customer['passwd']);
					$data['company_details'] = $company;
					$data['type'] = 3;
					$subject = "Reg: " . $this->comp['company_name'] . " saving scheme registration";
					$message = $this->load->view('include/emailAccount', $data, true);
					$sendEmail = $this->email_model->send_email($to, $subject, $message);
				}
				$result = array('status' => TRUE, 'msg' => 'Customer registered successfully.', 'response' => $customer['info']);
			} else {
				$this->db->trans_rollback();
				$result = array("status" => FALSE, "msg" => "Unable to proceed your request,please try again..", 'response' => []);
			}
		} else {
			$result = array("status" => FALSE, "msg" => "Unable to proceed your request", 'response' => []);
		}

		$this->response($result, 200);
	}



	function sync_existing_data($mobile, $id_customer, $id_branch)
	{
		$this->load->model('registration_model');
		$data['id_customer'] = $id_customer;
		$data['id_branch'] = $id_branch;
		$data['branchWise'] = 0;
		$data['mobile'] = $mobile;
		$res = $this->registration_model->insExisAcByMobile($data);
		if (sizeof($res) > 0) {
			$payData = $this->registration_model->syncPayData($res);
			if (sizeof($payData['succeedIds']) > 0 || $payData['no_records'] > 0) {
				$status = $this->registration_model->updateInterTableStatus($res, $payData['succeedIds']);
				if ($status === TRUE) {
					/*echo $this->db->_error_message();
					echo $this->db->last_query();*/
					return array("status" => TRUE, "msg" => "Purchase Plan registered successfully");
				} else {
					return array("status" => FALSE, "msg" => "Error in updating intermediate tables");
				}
			} else {
				return array("status" => FALSE, "msg" => "Error in updating payment tables");
			}
		} else {
			return array("status" => FALSE, "msg" => "No records to update in scheme account tables");
		}
	}

	function updateCustomer_post()
	{
		$result = array();
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		$id_customer = $data['id_customer'];
		$myDate = date('Y-m-d H:i:s');
		$customer = array(
			'firstname' => ucwords($data['firstname']),
			'lastname'  => ucfirst($data['lastname']),
			'title'  	=> $data['title'],
			'gender'	=> $data['gender'],
			'cus_type'  => $data['cus_type'],
			'email'     => $data['email'],
			'passwd'    => $this->__encrypt($data['mobile']),
			//'active'    => $data['active'],
			'date_upd'  => date('Y-m-d H:i:s'),
			'id_employee' 	=> (isset($data['id_employee']) ? $data['id_employee'] : NULL),
			'id_branch' 	=> (isset($data['id_branch']) ? $data['id_branch'] : NULL),
			'id_village' 	=> (isset($data['id_village']) ? $data['id_village'] : NULL),
			'date_of_birth' => (isset($data['date_of_birth']) ? $data['date_of_birth'] : NULL),
			'date_of_wed' 	=> (isset($data['date_of_wed']) ? $data['date_of_wed'] : NULL),
			'send_promo_sms' => (isset($data['send_promo_sms']) ? $data['send_promo_sms'] : 0),
			'is_vip' 		=> (isset($data['is_vip']) ? $data['is_vip'] : 0),
			'religion' 		=> (isset($data['religion']) ? $data['religion'] : 0),
			'pan'	 		=> (isset($data['pan']) ? $data['pan'] : NULL),
			'gst_number'	=> (isset($data['gst_number']) ? $data['gst_number'] : NULL),
			'nominee_name'	=> (isset($data['nominee_name']) ? $data['nominee_name'] : NULL),
			'nominee_relationship'	=> (isset($data['nominee_relationship']) ? $data['nominee_relationship'] : NULL),
			'nominee_mobile' => (isset($data['nominee_mobile']) ? $data['nominee_mobile'] : NULL),
			'voterid'		=> (isset($data['voterid']) ? $data['voterid'] : NULL),
			'rationcard'	=> (isset($data['rationcard']) ? $data['rationcard'] : NULL),
			'comments'		=> (isset($data['comments']) ? $data['comments'] : NULL),
		);
		$customer['date_upd'] = $myDate;
		$address = array(
			'address1' 	  => ucfirst($data['address1']),
			'address2' 	  => ucfirst($data['address2']),
			'id_country'  => $data['id_country'],
			'id_state'    => $data['id_state'],
			'id_city' 	  => $data['id_city'],
			'pincode' 	  => $data['pincode']
		);
		$this->db->trans_begin();
		$cflag = $this->$model->updateData($customer, "id_customer", $id_customer, "customer");
		$isExists = $this->$model->isAddressExist($id_customer);
		if ($isExists) {
			$address['date_upd'] = $myDate;
			$aflag = $this->$model->updateData($address, "id_customer", $id_customer, "address");
		} else {
			$address['id_customer'] = $id_customer;
			$address['date_add'] = $myDate;
			$aflag = $this->$model->insertData($address, "address");
		}
		
		if ($cflag) {
			if ($this->db->trans_status() == TRUE) {
				$log_data = array(
					'id_log'     => $data['id_log'],
					'event_date' => date("Y-m-d H:i:s"),
					'module'     => 'Customer',
					'operation'  => 'Edit',
					'record'     => $id_customer,
					'remark'     => 'Customer edited successfully',
					'event_through'     => 3, // 1 - Web admin, 2 - CRM mobile admin, 3 - Retail mobile admin
				);
				$this->$model->insertData($log_data, 'log_detail');
				$this->db->trans_commit();
				$result = array('status' => TRUE, 'msg' => "Customer updated successfully");
			} else {
				$this->db->trans_rollback();
				$result = array('status' => FALSE, 'msg' => 'Unable to proceed the request');
			}
			$this->response($result, 200);
		}
	}

	function estcustomerDetails_post()
	{
		$model = "ret_billing_model";
		$this->load->model($model);
		$data = $this->get_values();
		$cusId      = $data['id_cus'];
		$cusdetails = $this->$model->get_customer($cusId);
		$this->response(array('status' => true, 'cusdetails' => $cusdetails), 200);
	}

	public function getVillages_get()
	{
		$model = self::ADM_MODEL;
		$id_village = "";
		$result = $this->$model->getVillageData($id_village);
		//$this->response($result,200);
		echo json_encode($result);
	}

	function getCountry_get()
	{
		$model = self::ADM_MODEL;
		$result = $this->$model->get_country();
		$this->response($result, 200);
		//echo json_encode($result);
	}

	function getState_get()
	{
		$model = self::ADM_MODEL;
		$result = $this->$model->get_state($this->get('id_country'));
		$this->response($result, 200);
		//echo json_encode($result);
	}

	function getCity_get()
	{
		$model = self::ADM_MODEL;
		$result = $this->$model->get_city($this->get('id_state'));
		//$this->response($result,200);
		echo json_encode($result);
	}

	function getAllTaxGroupItems_get()
	{
		$model = self::ADM_MODEL;
		$data = $this->$model->getAllTaxGroupItems();
		echo json_encode($data);
	}

	/* End of Customer Master functions */

	function get_supplier_items_post()
	{
		$resultArr = array();

		$model = self::ADM_MODEL;

		try {
			$postData = $this->get_values();

			$resultArr    = $this->$model->get_supplier_items($postData, 'getData');

			$no_records   = $this->$model->get_supplier_items($postData, 'getCount');

			$total_records    = count($no_records);

			if (sizeof($resultArr) > 0) {

				$success = true;

				$message = "Records retrieved successfully..";
			} else {

				$success = true;

				$message = "No records found..";
			}
		} catch (Exception $e) {

			$success = false;

			$message = $e->getMessage();
		}

		$result = array("Status" => $success, "Description" => $message, 'total_records' => $total_records, 'ReturnObject' => $resultArr);

		$this->response($result, 200);
	}


	/* Start of Catalog functions */
	function getCategories_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		if ($data['supplier']) {
			$data['type'] = "cat";
			$result = $this->$model->get_supplier_items($data, 'getData');
		} else {
			$result = $this->$model->getCategories($data['type'], $data['last_id']);
		}
		$this->response($result, 200);
	}

	function getProducts_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		$imgOnly = isset($data['imgOnly']) ? $data['imgOnly'] : false;
		if ($data['supplier']) {
			$data['type'] = "pr";
			$result    = $this->$model->get_supplier_items($data, 'getData');
		} else {
			$result = $this->$model->getProducts($data['type'], $data['id_category'], $data['last_id'], $imgOnly);
		}
		//$this->response($result,200);
		echo json_encode($result);
	}

	function getDesigns_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		$imgOnly = isset($data['imgOnly']) ? $data['imgOnly'] : false;
		if ($data['supplier']) {
			$data['type'] = "ds";
			$result    = $this->$model->get_supplier_items($data, 'getData');
		} else {
			$result = $this->$model->getDesigns($data['type'], $data['id_product'], $data['last_id'], $imgOnly);
		}
		//$this->response($result,200);
		echo json_encode($result);
	}
	function getSubDesigns_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		$result = $this->$model->getSubDesigns($data['type'], $data['id_product'], $data['design_no'], $data['last_id']);
		$this->response($result, 200);
	}

	function getSubDesignById_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		if ($data['id_category'] == '') {
			$result = array("success" => FALSE, "message" => "Please Select The Category..");
		} else if ($data['id_product'] == '') {
			$result = array("success" => FALSE, "message" => "Please Select The Product..");
		} else if ($data['design_no'] == '') {
			$result = array("success" => FALSE, "message" => "Please Select The Design..");
		} else if ($data['id_sub_design'] == '') {
			$result = array("success" => FALSE, "message" => "Please Select The Sub Design..");
		} else {
			$result = $this->$model->getSubDesignDetail($data);
		}

		echo json_encode($result);
		//$this->response($result,200);
	}

	/* End of Catalog functions */


	/* Start of Estimation functions */
	function getTaggingBySearch_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		if (isset($data['searchTxt']) && trim($data['searchTxt']) != "") {
			$tagData = $this->$model->getTaggingBySearch($data['searchTxt'], $data['searchField'], $data['id_branch']);
		} else {
			$tagData = [];
		}
		$result['tagData'] = $tagData;
		if (sizeof($tagData) > 0 && isset($tagData['tag_status']) && $tagData['tag_status'] == 0) {
			$result['msg'] =  "";
			$result['status'] =  true;
		} else {
			$data = $this->ret_estimation_model->get_tag_status_details($data);

			if (sizeof($data) > 0) {
				$result['msg'] = "Tag is " . $data['status'];
			} else {
				$result['msg'] = 'Invalid Tag';
			}

			$result['status'] =  false;
		}
		$this->response($result, 200);
	}

	function getNonTagItems_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		$result = $this->$model->getNonTagItems($data['id_branch']);
		$this->response($result, 200);
	}

	function getHomeStock_post()
	{ // Partly Sold Item balance
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		$homeStock = $this->$model->getHomeStock($data['type'], $data['searchTxt'], $data['searchField'], $data['id_branch']);
		if ($data['type'] == 'all') {
			$result = $homeStock;
		} else {
			$result['homeStock'] = $homeStock;
			if (sizeof($homeStock) > 0) {
				if ($homeStock['tag_status'] == 0) {
					$result['msg'] =  "";
					$result['status'] =  true;
				} else {
					$tagCode = $homeStock['tag_code'];
					$result['msg'] =  $tagCode . " Sold Out";
					$result['status'] =  false;
				}
			} else {
				$result['msg'] =  ($data['searchField'] == "tag_code" ? "Tag code " . $data['searchTxt'] : "Tag") . " Not found...";
				$result['status'] =  false;
			}
		}
		$this->response($result, 200);
	}

	function getStoneTypes_get()
	{

		$stone_types = array();

		$model = self::ADM_MODEL;

		$stones = $this->$model->getStones($this->get('type'));

		foreach ($stones as $d) {

			array_push($stone_types, array("stone_type" => $d['stone_type'], "stone_type_name" => $d['stone_type_name']));
		}

		$stone_types_unique = array_values(array_unique($stone_types, SORT_REGULAR));
		$stoneratesettings = $this->$model->getStoneRateSettings();
		$combined = array();
		foreach ($stone_types_unique as $type) {
			$combined_rates = array();
			foreach ($stoneratesettings as $rate) {
				if ($rate['stone_type'] == $type['stone_type']) {
					$combined_rates[] = $rate;
				}
			}
			$combined[] = array(
				"stone_type" => $type['stone_type'],
				"stone_type_name" => $type['stone_type_name'],
				"stone_rate_masters" => $combined_rates
			);
		}
		$this->response($combined, 200);
	}


	function getStones_get()
	{
		$model = self::ADM_MODEL;

		$result = $this->$model->getStones($this->get('type'), $this->get('stone_type'));

		$this->response($result, 200);
	}

	function createEstimation_post()
	{

		$model = self::ADM_MODEL;

		$est_model = self::EST_MODEL;

		$this->load->model("admin_settings_model");

		$addData = $this->get_values();

		$current_datetime = date("Y-m-d H:i:s");

		//echo "<pre>"; print_r($addData); exit;

		$estimation_id = isset($addData['estimation']->estimation_id) && $addData['estimation']->estimation_id > 0 ? $addData['estimation']->estimation_id : 0;

		$is_update = $estimation_id > 0 ? 1 : 0;

		$dCData = $this->$model->getBranchDayClosingData($addData['id_branch']);

		$fin_year = $this->$model->get_FinancialYear();

		if (sizeof($dCData) > 0) {

			$this->db->trans_begin();

			$estimation_datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date'] . ' ' . date("H:i:s"));

			if ($is_update == 0) {

				$esti_no = $this->$est_model->generateEstiNo($dCData['entry_date'], $addData['id_branch']);

				if (isset($addData['esti_for'])){
					$esti_for = $addData['esti_for'];
				} else {
					$esti_for = 1;
					if(!empty($addData['tag']) && is_array($addData['tag'])){
						foreach($addData['tag'] as $tag){
							if($tag->cus_type == 2){
								$esti_for = 3;
								break;
							}
						}
					}
				}
			} else {

				$esti_no = isset($addData['estimation']->esti_no) && $addData['estimation']->esti_no > 0 ? $addData['estimation']->esti_no : 0;

				$esti_for = isset($addData['estimation']->esti_for) ? $addData['estimation']->esti_for : 0;
			}

			$data = array(

				'estimation_datetime'	=> $estimation_datetime,

				'fin_year_code'		    => $fin_year['fin_year_code'],

				'esti_no'				=> $esti_no,

				'esti_for'				=> $esti_for,

				'cus_id'				=> (!empty($addData['cus_id']) ? $addData['cus_id'] : NULL),

				'has_converted_order'   => 0,

				'discount'				=> (!empty($addData['discount']) ? $addData['discount'] : 0),

				'gift_voucher_amt'		=> (!empty($addData['gift_voucher_amt']) ? $addData['gift_voucher_amt'] : 0),

				'total_cost'			=> (!empty($addData['total_cost']) ? $addData['total_cost'] : 0),

				'est_date'	  		    => date($estimation_datetime),

				'added_through'      	=> 2,

				'id_branch'      		=> $addData['id_branch'],

				'is_eda'                => isset($addData['is_eda']) ? $addData['is_eda'] : 0,

				'goldrate_22ct'         => isset($addData['goldrate_22ct']) ? $addData['goldrate_22ct'] : NULL,

				'silverrate_1gm'        => isset($addData['silverrate_1gm']) ? $addData['silverrate_1gm'] : NULL,

			);

			$id_employee =  $addData['id_employee'];

			if ($is_update == 0) {

				$data['created_by'] 	= $id_employee;

				$data['created_time'] 	= $current_datetime;
			} else {

				$data['updated_by'] 	= $id_employee;

				$data['updated_time'] 	= $current_datetime;
			}

			$insId = 0;

			$old_tagged_items = $old_nt_details = $old_hm_details = $old_om_details = array();

			if ($is_update == 0) {

				$insId = $this->$model->insertData($data, 'ret_estimation');
			} else {

				$insId = $this->$model->updateData($data, 'estimation_id', $estimation_id, 'ret_estimation');

				$old_tagged_items = $this->$est_model->get_est_tag_details($estimation_id);

				$old_nt_details   = $this->$est_model->est_non_tag_items($estimation_id);

				$old_hm_details   = $this->$est_model->est_home_bill($estimation_id);

				$old_om_details   = $this->$est_model->old_metal($estimation_id);

				/*echo "<pre>";
				print_r($old_tagged_items);
				print_r($old_nt_details);
				print_r($old_hm_details);
				print_r($old_om_details);exit;*/
			}

			if ($insId) {

				// Tag

				$updated_tag_ids = array();

				if ($addData['is_tag']  && sizeof($addData['tag']) > 0) {

					$arrayEstTags = array();

					foreach ($addData['tag'] as $estTag) {

						$est_item_id = 0;

						$is_item_update = 0;

						if ($is_update == 1) {

							$est_item_id = isset($estTag->est_item_id) ? $estTag->est_item_id : 0;

							if ($est_item_id > 0) {

								$is_item_update = 1;

								array_push($updated_tag_ids, $est_item_id);
							}
						}

						$arrayEstTags = array(

							'esti_id'              => $insId,

							'tag_id'               => $estTag->tag_id,

							'item_type'            => 0,

							'product_id'           => $estTag->product_id,

							'design_id'			   => $estTag->design_id,

							'id_sub_design'        => $estTag->id_sub_design,

							'purity'               => $estTag->purity,

							//'size'                 => ($estTag->size != '' ? $estTag->size : NULL),

							'piece'                => $estTag->piece,

							'less_wt'              => (!empty($estTag->less_wt) ? $estTag->less_wt : NULL),

							'net_wt'               => $estTag->net_wt,

							'gross_wt'             => (isset($estTag->gross_wt) ? (!empty($estTag->gross_wt) ? $estTag->gross_wt : NULL) : $estTag->gross_wt),

							'calculation_based_on' => $estTag->calculation_based_on,

							'wastage_percent'      => $estTag->retail_max_wastage_percent,

							'mc_value'             => ($estTag->mc_value != '' ? $estTag->mc_value : NULL),

							'mc_type'              => $estTag->mc_type,

							'id_collecion_maping_det' => $estTag->id_mapping_details != '' ? $estTag->id_mapping_details : NULL,

							'item_cost'            => $estTag->sales_value,

							'item_total_tax'       => $estTag->tax_price,

							'market_rate_cost'     => $estTag->market_sales_value,

							'market_rate_tax'      =>  $estTag->market_tax_price,

							'is_partial'           => ($estTag->is_partial != '' ? $estTag->is_partial : 0),

							'id_orderdetails'      => ($estTag->id_orderdetails != '' ? $estTag->id_orderdetails : NULL),

							'orderno'              => ($estTag->order_no != '' ? $estTag->order_no : NULL),

							'est_rate_per_grm'     => ($estTag->rate_per != '' ? $estTag->rate_per : 0),

							'esti_purchase_cost'   => !empty($estTag->purchase_cost) ? $estTag->purchase_cost : NULL,

							'discount'             => !empty($estTag->discount_amount) ? $estTag->discount_amount : 0,

							'item_emp_id'     	   => ($estTag->id_employee != '' ? $estTag->id_employee : 0),

							'search_field'     	   => ($estTag->searchField != '' ? ($estTag->searchField == "tag_code" ? 1 : 2) : 1),

							'tag_remark'     	   => isset($estTag->remark) ? $estTag->remark : 1

						);

						if (!empty($arrayEstTags)) {

							$tagInsert = 0;

							if ($is_item_update == 1) {

								$tagInsert = $this->$model->updateData($arrayEstTags, 'est_item_id', $est_item_id, 'ret_estimation_items');
							} else {

								$tagInsert = $this->$model->insertData($arrayEstTags, 'ret_estimation_items');
							}
						}

						if ($is_item_update == 1 && $tagInsert > 0) {

							$this->$model->deleteData('est_item_id', $tagInsert, 'ret_estimation_other_charges');
						}

						$charges_details = $this->$model->get_charges($estTag->tag_id);

						if (sizeof($charges_details) > 0 && $tagInsert > 0) {
							foreach ($charges_details as $charge) {
								$charge_data = array(

									'est_item_id'  => $tagInsert,

									'id_charge'    => $charge['charge_id'],

									'amount'       => $charge['charge_value'],

								);

								$stoneInsert = $this->$model->insertData($charge_data, 'ret_estimation_other_charges');
								//print_r($this->db->last_query());exit;
							}
						}

						if ($is_item_update == 1 && $tagInsert > 0) {

							$this->$model->deleteData('est_item_id', $tagInsert, 'ret_estimation_item_stones');
						}

						//$stone_details = $this->$model->getTagStoneDetails($estTag->tag_id);

						$stone_details = $estTag->stone_details;

						foreach ($stone_details as $stone) {
							$stone = (array)$stone;

							$stone_data = array(

								'est_id'        => $insId,

								'est_item_id'   => $tagInsert,

								'pieces'        => $stone['pieces'],

								'wt'            => $stone['wt'],

								'uom_id'      	=> $stone['uom_id'],

								'stone_id'      => $stone['stone_id'],

								'price'         => $stone['amount'],

								'is_apply_in_lwt' => $stone['is_apply_in_lwt'],

								'stone_cal_type' => $stone['stone_cal_type'],

								'rate_per_gram'  => $stone['rate_per_gram'],

							);

							$stoneInsert = $this->$model->insertData($stone_data, 'ret_estimation_item_stones');
						}

						if ($is_item_update == 1 && $tagInsert > 0) {

							$this->$model->deleteData('est_item_id', $tagInsert, 'ret_est_other_metals');
						}

						$other_metal_details = $estTag->other_metal_details;

						if ($other_metal_details) {

							foreach ($other_metal_details as $oth_m) {

								$oth_m = (array)$oth_m;

								$est_other_metals = array(

									'est_item_id'               => $tagInsert,

									'tag_other_itm_metal_id'    => $oth_m['tag_other_itm_metal_id'],

									'tag_other_itm_pur_id'      => $oth_m['tag_other_itm_pur_id'],

									'tag_other_itm_grs_weight'  => $oth_m['tag_other_itm_grs_weight'],

									'tag_other_itm_wastage'     => $oth_m['tag_other_itm_wastage'],

									'tag_other_itm_uom'         => $oth_m['tag_other_itm_uom'],

									'tag_other_itm_cal_type'    => $oth_m['tag_other_itm_cal_type'],

									'tag_other_itm_mc'          => $oth_m['tag_other_itm_mc'],

									'tag_other_itm_rate'        => $oth_m['tag_other_itm_rate'],

									'tag_other_itm_pcs'         => $oth_m['tag_other_itm_pcs'],

									'tag_other_itm_amount'      => $oth_m['tag_other_itm_amount'],

								);

								$this->$model->insertData($est_other_metals, 'ret_est_other_metals');
							}
						}
					}
				}

				if ($is_update == 1) {

					if (isset($old_tagged_items['item_details']) && count($old_tagged_items['item_details']) > 0) {

						foreach ($old_tagged_items['item_details'] as $old_tags) {

							$del_est = 1;

							$old_est_id = $old_tags['est_item_id'];

							foreach ($updated_tag_ids as $upd_tag_id) {

								if ($old_est_id == $upd_tag_id) {

									$del_est = 0;

									break;
								}
							}

							if ($del_est == 1) {

								$this->$model->deleteData('est_item_id', $old_est_id, 'ret_estimation_items');
							}
						}
					}
				}

				// Non Tag

				$updated_nt_ids = array();

				if ($addData['is_non_tag']  && sizeof($addData['non_tag']) > 0) {

					$arrayestNonTag = array();

					foreach ($addData['non_tag'] as $estNonTag) {

						$est_item_id = 0;

						$is_item_update = 0;

						if ($is_update == 1) {

							$est_item_id = isset($estNonTag->est_item_id) ? $estNonTag->est_item_id : 0;

							if ($est_item_id > 0) {

								$is_item_update = 1;

								array_push($updated_nt_ids, $est_item_id);
							}
						}

						$arrayestNonTag =  array(

							'esti_id' 			   => $insId,

							'design_id'            => $estNonTag->design->design_no,

							'item_type' 		   => 1,

							'id_section'           => !empty($estNonTag->id_section) && $estNonTag->id_section != '' ? $estNonTag->id_section : '',

							'product_id'           => ($estNonTag->pro_id != '' ? $estNonTag->pro_id : ''),

							'id_sub_design'        => !empty($estNonTag->sub_design->id_sub_design) && $estNonTag->sub_design->id_sub_design != '' ? $estNonTag->sub_design->id_sub_design : NULL,

							'purity'    		   => $estNonTag->purity,

							'size'                 => ($estNonTag->size != '' ? $estNonTag->size : NULL),

							'piece'                => ($estNonTag->piece != '' ? $estNonTag->piece : NULL),

							'less_wt'              => ($estNonTag->less_wt != '' ? $estNonTag->less_wt : NULL),

							'net_wt'               => $estNonTag->net_wt,

							'gross_wt'             => $estNonTag->gross_wt,

							'mc_type'              => ($estNonTag->mc_type != '' ?  $estNonTag->mc_type : 1),

							'calculation_based_on' => ($estNonTag->calculation_based_on != '' ? $estNonTag->calculation_based_on : NULL),

							'wastage_percent'      => $estNonTag->retail_max_wastage_percent,

							'mc_value'             => ($estNonTag->mc_value != '' ? $estNonTag->mc_value : NULL),

							'item_cost'            => $estNonTag->sales_value,

							'is_non_tag'           => 1,

							'item_total_tax'       => $estNonTag->tax_price,

							'market_rate_cost'     => $estNonTag->market_sales_value,

							'market_rate_tax'      => $estNonTag->market_tax_price,

							'est_rate_per_grm'     => ($estNonTag->rate_per != '' ? $estNonTag->rate_per : 0),

							'item_emp_id'     	   => !empty($estNonTag->id_employee) && $estNonTag->id_employee != '' ? $estNonTag->id_employee : 0

						);

						if (!empty($arrayestNonTag)) {

							$tagInsert = 0;

							if ($is_item_update == 1) {

								$tagInsert = $this->$model->updateData($arrayestNonTag, 'est_item_id', $est_item_id, 'ret_estimation_items');
							} else {

								$tagInsert = $this->$model->insertData($arrayestNonTag, 'ret_estimation_items');
							}

							if ($is_item_update == 1 && $tagInsert > 0) {

								$this->$model->deleteData('est_item_id', $tagInsert, 'ret_estimation_item_stones');
							}

							if ($estNonTag->stone_details) {

								foreach ($estNonTag->stone_details as $stone) {

									$stone = (array)$stone;

									$stone_data = array(

										'est_id'        => $insId,

										'est_item_id'   => $tagInsert,

										'pieces'        => $stone['stone_pcs'],

										'wt'            => $stone['stone_wt'],

										'uom_id'      	=> $stone['uom_id'],

										'stone_id'      => $stone['stone_id'],

										'price'         => $stone['stone_price'],

										'is_apply_in_lwt' => $stone['is_apply_in_lwt'],

										'stone_cal_type' => $stone['stone_cal_type'],

										'rate_per_gram'  => $stone['rate'],

									);

									$stoneInsert = $this->$model->insertData($stone_data, 'ret_estimation_item_stones');
								}
							}
						}
					}
				}

				if ($is_update == 1) {

					if (isset($old_nt_details['item_details']) && count($old_nt_details['item_details']) > 0) {

						foreach ($old_nt_details['item_details'] as $old_nts) {

							$del_est = 1;

							$old_est_id = $old_nts['est_item_id'];

							foreach ($updated_nt_ids as $upd_nt_id) {

								if ($old_est_id == $upd_nt_id) {

									$del_est = 0;

									break;
								}
							}

							if ($del_est == 1) {

								$this->$model->deleteData('est_item_id', $old_est_id, 'ret_estimation_items');
							}
						}
					}
				}

				// Home Bill

				$updated_hm_ids = array();

				if ($addData['is_home_bill']  && sizeof($addData['home_bill']) > 0) {

					$arrayhome_bill = array();

					foreach ($addData['home_bill'] as $home_bill) {

						$est_item_id = 0;

						$is_item_update = 0;

						if ($is_update == 1) {

							$est_item_id = isset($home_bill->est_item_id) ? $home_bill->est_item_id : 0;

							if ($est_item_id > 0) {

								$is_item_update = 1;

								array_push($updated_hm_ids, $est_item_id);
							}
						}

						$arrayhome_bill = array(

							'esti_id'               => $insId,

							'item_type'             => 2,

							'id_section'           => !empty($home_bill->id_section) && $home_bill->id_section != '' ? $home_bill->id_section : null,

							'design_id'            	=> $home_bill->design_no,

							'id_sub_design'         => isset($home_bill->sub_design_no) ? $home_bill->sub_design_no : NULL,

							'product_id'            => $home_bill->pro_id,

							'tag_id'                => ($home_bill->tag_id != '' ? $home_bill->tag_id : NULL),

							'purity'                => ($home_bill->purity != '' ? $home_bill->purity : NULL),

							'size'                  => ($home_bill->size != '' ? $home_bill->size : NULL),

							'piece'                 => ($home_bill->piece != '' ? $home_bill->piece : NULL),

							'less_wt'               => ($home_bill->less_wt != '' ? $home_bill->less_wt : NULL),

							'net_wt'                => $home_bill->net_wt,

							'mc_type'               => ($home_bill->mc_type != '' ?  $home_bill->mc_type : 1),

							'gross_wt'              => $home_bill->gross_wt,

							'calculation_based_on'  => ($home_bill->calculation_based_on != '' ? $home_bill->calculation_based_on : NULL),

							'wastage_percent'       => $home_bill->retail_max_wastage_percent,

							'mc_value'              => ($home_bill->mc_value != '' ? $home_bill->mc_value : NULL),

							'item_cost'             => $home_bill->sales_value,

							'item_total_tax'      	=> $home_bill->tax_price,

							'market_rate_cost'      => $home_bill->market_sales_value,

							'market_rate_tax'       => $home_bill->market_tax_price,

							'est_rate_per_grm'     => ($home_bill->rate_per != '' ? $home_bill->rate_per : 0),

							'id_division'           => !empty($home_bill->id_charge) ? $home_bill->id_charge : 0,

							'item_emp_id'     	    => !empty($home_bill->id_employee) && $home_bill->id_employee != '' ? $home_bill->id_employee : 0

						);

						if (!empty($arrayhome_bill)) {

							$tagInsert = 0;

							if ($is_item_update == 1) {

								$tagInsert = $this->$model->updateData($arrayhome_bill, 'est_item_id', $est_item_id, 'ret_estimation_items');
							} else {

								$tagInsert = $this->$model->insertData($arrayhome_bill, 'ret_estimation_items');
							}

							if ($is_item_update == 1 && $tagInsert > 0) {

								$this->$model->deleteData('est_item_id', $tagInsert, 'ret_estimation_other_charges');
							}

							$charges_details = $home_bill->charges;

							if (sizeof($charges_details) > 0 && $tagInsert > 0) {
								foreach ($charges_details as $charge) {
									$charge_data = array(

										'est_item_id'  => $tagInsert,

										'id_charge'    => $charge->id_charge,

										'amount'       => $charge->charge_value,

									);

									$chargesInsert = $this->$model->insertData($charge_data, 'ret_estimation_other_charges');
									//print_r($this->db->last_query());exit;
								}
							}

							if ($is_item_update == 1 && $tagInsert > 0) {

								$this->$model->deleteData('est_item_id', $tagInsert, 'ret_estimation_item_stones');
							}

							if ($home_bill->stone_details) {

								foreach ($home_bill->stone_details as $stone) {

									$stone_data = array(

										'est_id'        => $insId,

										'est_item_id'   => $tagInsert,

										'pieces'        => $stone->stone_pcs,

										'wt'            => $stone->stone_wt,

										'uom_id'      	=> $stone->uom_id,

										'stone_id'      => $stone->stone_id,

										'price'         => $stone->stone_price,

										'is_apply_in_lwt' => $stone->is_apply_in_lwt,

										'stone_cal_type'  => $stone->stone_cal_type,

										'rate_per_gram'   => $stone->rate,
									);

									$stoneInsert = $this->$model->insertData($stone_data, 'ret_estimation_item_stones');
								}
							}
						}
					}
				}

				if ($is_update == 1) {

					if (isset($old_hm_details['item_details']) && count($old_hm_details['item_details']) > 0) {

						foreach ($old_hm_details['item_details'] as $old_hbs) {

							$del_est = 1;

							$old_est_id = $old_hbs['est_item_id'];

							foreach ($updated_hm_ids as $upd_hb_id) {

								if ($old_est_id == $upd_hb_id) {

									$del_est = 0;

									break;
								}
							}

							if ($del_est == 1) {

								$this->$model->deleteData('est_item_id', $old_est_id, 'ret_estimation_items');
							}
						}
					}
				}

				// Old Metal

				$updated_om_ids = array();

				if ($addData['is_old_metal'] && sizeof($addData['old_metal']) > 0) {

					$arrayOldMatel = array();

					foreach ($addData['old_metal'] as $oldMetal) {

						$old_metal_sale_id = 0;

						$is_item_update = 0;

						if ($is_update == 1) {

							$old_metal_sale_id = isset($oldMetal->old_metal_sale_id) ? $oldMetal->old_metal_sale_id : 0;

							if ($old_metal_sale_id > 0) {

								$is_item_update = 1;

								array_push($updated_om_ids, $old_metal_sale_id);
							}
						}

						$arrayOldMatel = array(

							'est_id'            => $insId,

							'id_category'       => $oldMetal->id_category,

							'id_old_metal_type' => $oldMetal->id_old_metal_type,

							'id_old_metal_category' => $oldMetal->id_old_metal_cat_type,

							'purpose'           => $oldMetal->id_purpose,

							'piece'				=> $oldMetal->old_pcs,

							'gross_wt'          => $oldMetal->gross_wt,

							'old_metal_prod_id' => !empty($oldMetal->old_metal_prod_id) && $oldMetal->old_metal_prod_id != "" ? $oldMetal->old_metal_prod_id : NULL,

							'dust_wt'           => (!empty($oldMetal->dust_wt) ? $oldMetal->dust_wt : NULL),

							'stone_wt'          => (!empty($oldMetal->stone_wt) ? $oldMetal->stone_wt : NULL),

							'net_wt'            => $oldMetal->net_wt,

							'wastage_percent'   => (!empty($oldMetal->wastage) ? $oldMetal->wastage : 0),

							'wastage_wt'   		=> (!empty($oldMetal->wastage_wt) ? $oldMetal->wastage_wt : 0),

							'rate_per_gram'     => $oldMetal->rate,

							'amount'            => $oldMetal->amount,

							'purity'            => $oldMetal->purity,

							'touch'				=> !empty($oldMetal->touch) ? $oldMetal->touch : 100,

							'remark'			=> $oldMetal->remark,

							'old_stone_set'     => isset($oldMetal->old_stone_settings) ? $oldMetal->old_stone_settings : 1,

						);

						if (!empty($arrayOldMatel)) {
							$tagInsert = 0;

							if ($is_item_update == 1) {

								$tagInsert = $this->$model->updateData($arrayOldMatel, 'old_metal_sale_id', $old_metal_sale_id, 'ret_estimation_old_metal_sale_details');
							} else {

								$tagInsert = $this->$model->insertData($arrayOldMatel, 'ret_estimation_old_metal_sale_details');
							}

							if ($is_item_update == 1 && $tagInsert > 0) {

								$this->$model->deleteData('est_old_metal_sale_id', $tagInsert, 'ret_esti_old_metal_stone_details');
							}

							if ($oldMetal->stone_details) {

								foreach ($oldMetal->stone_details as $stone) {

									$stone_data = array(

										'est_id'        => $insId,

										'est_old_metal_sale_id'   => $tagInsert,

										'pieces'        => $stone->stone_pcs,

										'wt'            => $stone->stone_wt,

										'uom_id'      	=> $stone->uom_id,

										'stone_id'      => $stone->stone_id,

										'price'         => $stone->stone_price,

										'is_apply_in_lwt' => $stone->is_apply_in_lwt,

										'stone_cal_type'  => $stone->stone_cal_type,

										'rate_per_gram'   => $stone->rate,

									);

									$stoneInsert = $this->$model->insertData($stone_data, 'ret_esti_old_metal_stone_details');
								}
							}
						}
					}
				}

				if ($is_update == 1) {

					if (isset($old_om_details['old_matel_details']) && count($old_om_details['old_matel_details']) > 0) {

						foreach ($old_om_details['old_matel_details'] as $old_oms) {

							$del_est = 1;

							$old_est_id = $old_oms['old_metal_sale_id'];

							foreach ($updated_om_ids as $upd_om_id) {

								if ($old_est_id == $upd_om_id) {

									$del_est = 0;

									break;
								}
							}

							if ($del_est == 1) {

								$this->$model->deleteData('old_metal_sale_id', $old_est_id, 'ret_estimation_old_metal_sale_details');
							}
						}
					}
				}

				if ($is_update == 1) {

					$this->$model->deleteData('est_id', $insId, 'ret_est_chit_utilization');
				}

				if ($addData['chit_details']  && sizeof($addData['chit_details']) > 0) {

					if (!empty($addData['chit_details'])) {

						$arrayChit = array();

						foreach ($addData['chit_details'] as $estChit) {

							if ($estChit->slip_no != "" && $estChit->slip_amt > 0) {

								$arrayChit = array(

									'est_id'                    => $insId,

									'scheme_account_id'         => $estChit->slip_no,

									'utl_amount'                => $estChit->slip_amt,

									'closing_weight'            => ($estChit->closing_weight != '' ? $estChit->closing_weight : 0),

									'wastage_per'               => ($estChit->wastage_per != '' ? $estChit->wastage_per : 0),

									'savings_in_wastage'        => ($estChit->savings_in_wastage != '' ? $estChit->savings_in_wastage : 0),

									'mc_value'                  => ($estChit->mc_value != '' ? $estChit->mc_value : ''),

									'savings_in_making_charge'  => ($estChit->savings_in_mcvalue != '' ? $estChit->savings_in_mcvalue : 0),
								);

								$this->$model->insertData($arrayChit, 'ret_est_chit_utilization');
							}
						}
					}
				}
			}

			if ($this->db->trans_status() === TRUE) {

				$this->db->trans_commit();

				$remark = "";

				$return_msg = "";

				if ($is_update == 0) {

					$remark = 'Record added successfully';

					$return_msg = 'Estimation No ' . $esti_no . ' created successfully';
				} else {

					$remark = 'Record updated successfully';

					$return_msg = 'Estimation No ' . $esti_no . ' updated successfully';
				}

				$log_data = array(

					'id_log'        => $addData['id_log'],

					'event_date'    => date("Y-m-d H:i:s"),

					'module'        => 'Estimation',

					'operation'     => 'Add',

					'record'        =>  $insId,

					'remark'        =>  $remark

				);

				//$log = $this->log_model->log("insert","",$log_data);

				$uploadPdf = $this->generate_invoice($insId);

				$return_data = array(

					'status'	=> true,

					'msg'		=> $return_msg,

					"printURL"  => $uploadPdf['url'],

					"esti_name" => $uploadPdf['file_name'],

					"esti_id" 	=> $insId,

					"est_printable" =>  $this->getEstimationData($insId)

				);
			} else {

				echo $this->db->_error_message();
				echo $this->db->last_query();
				exit;


				$this->db->trans_rollback();

				$return_data = array('msg' => 'Unable to proceed the requested process', 'status' => false, 'type' => 1, 'id' => '');
			}

			echo json_encode($return_data);
		} else {

			$return_data = array('msg' => 'Kindly update Day closing data to add estimation', 'status' => false, 'type' => 1, 'id' => '');

			echo json_encode($return_data);
		}
	}

	public function editEstimation_get($id)
	{

		$model = "ret_estimation_model";

		$app_model = self::ADM_MODEL;

		$data = array();

		$tag_details = array();

		$non_tag_details = array();

		$est_home_bill = array();

		$old_metal = array();

		$estimation = $this->$model->get_entry_records($id);

		if (!($estimation['estbillid'] > 0)) {

			$data['estimation'] = $estimation;

			//echo "<pre>";print_r($data['estimation']);exit;

			//$data['estimation']['employee'] =$this->$model->get_employee($data['estimation']['id_branch']);

			$data['uom'] = $this->$model->getUOMDetails();

			//$data['est_other_item'] = $this->$model->getOtherEstimateItemsDetails($id);

			$tagged_items = $this->$model->get_est_tag_details($id);

			foreach ($tagged_items['item_details'] as $tagged_items) {

				$tagged_items['other_metal_total_price'] = 0;

				$tagged_items['other_metal_total_wt'] = 0;

				$tagged_items['sales_value'] = $tagged_items['item_cost'] - $tagged_items['item_total_tax'];

				$tagged_items['retail_max_wastage_percent'] = $tagged_items['wastage_percent'];

				unset($tagged_items['wastage_percent']);

				$stone_details = array();

				foreach ($tagged_items['stone_details'] as $stone) {

					$stone['amount'] = $stone['price'];

					$stone['lwt']    = $stone['is_apply_in_lwt'] == 1 ? true : false;

					unset($stone['price']);

					$stone_details[] = $stone;
				}

				unset($tagged_items['stone_details']);

				$tagged_items['stone_details'] = $stone_details;

				$tag_details[] = $tagged_items;
			}

			$nt_details = $this->$model->est_non_tag_items($id);

			$nonTagItems = $this->$app_model->getNonTagItems($data['estimation']['id_branch']);

			foreach ($nt_details['item_details'] as $nt_items) {

				$pro_id = $nt_items['product_id'];

				$nt_items['AVgross_wt'] = 0;

				$nt_items['AVpiece'] = 0;

				$nt_items['id_nontag_item'] = 0;

				if ($nonTagItems['nt_items'] && count($nonTagItems['nt_items']) > 0) {

					if (isset($nonTagItems['nt_items'])) {

						foreach ($nonTagItems['nt_items'] as $nt) {

							if ($nt['product'] == $pro_id) {

								$nt_items['AVgross_wt'] = $nt['AVgross_wt'];

								$nt_items['AVpiece'] = $nt['AVpiece'];

								$nt_items['id_nontag_item'] = $nt['id_nontag_item'];
							}
						}
					}
				}

				$nt_items['sales_value'] 				= $nt_items['item_cost'] - $nt_items['item_total_tax'];

				$nt_items['retail_max_wastage_percent'] = $nt_items['wastage_percent'];

				$nt_items['pro_id'] 					= $pro_id;

				$nt_items['design'] 					= $nt_items['design_id'];

				$nt_items['sub_design_id'] 				= $nt_items['id_sub_design'];

				$nt_items['purity'] 					= $nt_items['purid'];

				$stone_details = array();

				foreach ($nt_items['stone_details'] as $nt_stone) {

					$nt_stone['stone_pcs'] = $nt_stone['pieces'];

					$nt_stone['stone_wt'] = $nt_stone['wt'];

					$nt_stone['rate'] = $nt_stone['rate_per_gram'];

					$nt_stone['stone_price'] = $nt_stone['price'];

					$nt_stone['lwt']    = $nt_stone['is_apply_in_lwt'] == 1 ? true : false;

					unset($nt_stone['pieces'], $nt_stone['wt'], $nt_stone['rate_per_gram'], $nt_stone['price']);

					$stone_details[] = $nt_stone;
				}

				unset($nt_items['stone_details']);

				$nt_items['stone_details'] = $stone_details;

				unset($nt_items['wastage_percent'], $nt_items['product_id'], $nt_items['design_id'], $nt_items['id_sub_design'], $nt_items['purid']);

				$non_tag_details[] = $nt_items;
			}

			$hm_details = $this->$model->est_home_bill($id);

			foreach ($hm_details['item_details'] as $hm_items) {

				$hm_items['sales_value'] 				= $hm_items['item_cost'] - $hm_items['item_total_tax'];

				$hm_items['retail_max_wastage_percent'] = $hm_items['wastage_percent'];

				$hm_items['pro_id'] 					= $hm_items['product_id'];

				$hm_items['design_no'] 					= $hm_items['design_id'];

				$hm_items['sub_design_no'] 				= $hm_items['id_sub_design'];

				$hm_items['purity'] 					= $hm_items['purid'];

				$charges = array();

				foreach ($hm_items['charges'] as $hm_charge) {

					$hm_charge['charge_value'] = $hm_charge['amount'];

					$hm_charge['chargecode'] = $hm_charge['code_charge'];

					unset($hm_charge['amount'], $hm_charge['code_charge']);

					$charges[] = $hm_charge;
				}

				unset($hm_items['charges']);

				$hm_items['charges'] = $charges;


				$stone_details = array();

				foreach ($hm_items['stone_details'] as $hm_stone) {

					$hm_stone['stone_pcs'] = $hm_stone['pieces'];

					$hm_stone['stone_wt'] = $hm_stone['wt'];

					$hm_stone['rate'] = $hm_stone['rate_per_gram'];

					$hm_stone['stone_price'] = $hm_stone['price'];

					$hm_stone['lwt'] = $hm_stone['is_apply_in_lwt'] == 1 ? true : false;

					unset($hm_stone['pieces'], $hm_stone['wt'], $hm_stone['rate_per_gram'], $hm_stone['price']);

					$stone_details[] = $hm_stone;
				}

				unset($hm_items['stone_details']);

				$hm_items['stone_details'] = $stone_details;

				unset($hm_items['wastage_percent'], $hm_items['product_id'], $hm_items['design_id'], $hm_items['id_sub_design'], $hm_items['purid']);

				$est_home_bill[] = $hm_items;
			}

			$om_details = $this->$model->old_metal($id);

			foreach ($om_details['old_matel_details'] as $om_items) {

				$om_items['id_old_metal_cat_type'] = $om_items['id_old_metal_category'];

				$om_items['old_sub_design_id'] = $om_items['old_metal_prod_id'];

				$om_items['sub_design_name'] = $om_items['old_metal_prod_name'];

				$om_items['wastage'] = $om_items['wastage_percent'];

				$om_items['old_pcs'] = $om_items['piece'];

				$om_items['rate'] = $om_items['rate_per_gram'];

				$om_items['purity'] = $om_items['purid'];

				$om_items['id_purpose'] = $om_items['purpose'];

				$stone_details = array();

				foreach ($om_items['stone_details'] as $om_stone) {

					$om_stone['stone_pcs'] = $om_stone['pieces'];

					$om_stone['stone_wt'] = $om_stone['wt'];

					$om_stone['rate'] = $om_stone['rate_per_gram'];

					$om_stone['stone_price'] = $om_stone['price'];

					$om_stone['lwt']    = $om_stone['is_apply_in_lwt'] == 1 ? true : false;

					unset($om_stone['pieces'], $om_stone['wt'], $om_stone['rate_per_gram'], $om_stone['price']);

					$stone_details[] = $om_stone;
				}

				unset($om_items['stone_details']);

				$om_items['stone_details'] = $stone_details;

				unset($om_items['id_old_metal_category'], $om_items['old_metal_prod_id'], $om_items['wastage_percent'], $om_items['piece'], $om_items['rate_per_gram'], $om_items['purid'], $om_items['purpose']);


				$old_metal[] = $om_items;
			}

			$data['tag_details'] 		= $tag_details;

			$data['non_tag_details'] 	= $non_tag_details;

			$data['est_home_bill'] 		= $est_home_bill;

			$data['old_metal'] 			= $old_metal;

			$data['chit_details']		= $this->$model->get_chit_details($id);
		}

		echo json_encode($data);
	}

	public function productenquriy_post()
    {
        $model = self::ADM_MODEL;
		$company = $this->$model->company_details();
        $payData = $this->get_values();
        $insData = array(
            'mobile' => $payData['mobile'],
            'first_name' => $payData['firstname'],
            'last_name' => $payData['lastname'],
            'email' => $payData['email'],
            'id_product' => $payData['id_product'],
            'date_add' => date('Y-m-d H:i:s'),
            'message' => $payData['message'],
			"id_customer" => $payData['id_customer']
        );
        $result = $this->$model->insProduct_enquiry($insData);
        if ($result['status'] == true) {
            $to = $company['email'];
            //$to = "gokul@logimaxindia.com";	
            $bcc = "";
            $subject = "Reg - " . $company['company_name'] . " customer enquiry";
            $insData['product_name'] = $payData['productname'];
            //print_r($insdata);exit;
            $message = $this->load->view('include/emailEnquiry', $insData, true);
            $sendEmail = $this->email_model->send_email($to, $subject, $message, $bcc, "");
            $this->response(array('status' => true, 'msg' => 'Thanks for your Enquiry'));
        } else {
            $this->response(array('status' => false, 'msg' => 'Please try after sometime'));
        }
    }
	public function generate_invoice($est_id)
	{

		$model = "ret_estimation_model";

		$data['estimation'] = $this->$model->get_entry_records($est_id);

		$data['estimation']['app_qrcode'] = "";

		$data['estimation']['playstore'] = "";

		$data['est_other_item'] = $this->$model->getOtherEstimateItemsDetails($est_id);

		$data['metal_rates'] = $this->$model->get_branchwise_rate($data['estimation']['id_branch']);

		$data['comp_details'] = $this->$model->getCompanyDetails($data['estimation']['id_branch']);
		$data['cusdetails'] = $this->$model->getCustomerDet('', $data['estimation']['cus_id']);
		//$data['ac_details'] = $this->$model->getAccDetail($data['estimation']['cus_id']);

		/*QR code Generation*/

		$this->load->library('phpqrcode/qrlib');

		$SERVERFILEPATH = 'esti_qrcode/' . $data['estimation']['estimation_id'];

		if (!is_dir($SERVERFILEPATH)) {

			mkdir($SERVERFILEPATH, 0777, TRUE);
		}

		$folder = $SERVERFILEPATH;

		$file_name1 = $data['estimation']['estimation_id'] . ".png";

		$file_name = $SERVERFILEPATH . '/' . $file_name1;

		QRcode::png($data['estimation']['esti_no'], $file_name);  //Passing QR data

		$data['filename'] = base_url('esti_qrcode/' . $data['estimation']['estimation_id'] . '/' . $file_name1);

		//echo "<pre>"; print_r($data); echo "</pre>"; exit;
		$html = $this->load->view('estimation/print/est_print_2', $data, true);

		$this->load->helper(array('dompdf', 'file'));

		$dompdf = new DOMPDF();

		$dompdf->load_html($html);

		$dompdf->set_paper('A4', "portriat");

		$dompdf->render();

		$output = $dompdf->output();

		if ($data['estimation']['id_branch'] != '') {

			$folder =  self::EST_PATH . '' . $data['estimation']['id_branch'] . '/' . $data['estimation']['est_date'];
		} else {

			$folder =  self::EST_PATH . '' . $data['estimation']['est_date'];
		}

		if (!is_dir($folder)) {

			mkdir($folder, 0777, TRUE);
		}

		$file_name = "E_" . $data['estimation']['estimation_id'] . "_" . time() . ".pdf";

		$file = $folder . '/' . $file_name;

		// Delete the file if it already exists
		if (file_exists($file)) {

			unlink($file);
		}

		file_put_contents($file, $output);

		return array("url" => base_url() . $file, "file_name" => $file_name);
	}











	//join scheme

	function createAccount_post()
	{
		$model = "mobileapi_model";
		$this->load->model($model);
		$data = $this->get_values();
		$flag = FALSE;
		$scheme_acc_number  = '';
		$result = '';
		$is_referral_by = NULL;
		/*$cus = $this->$model->get_customerByID($data['id_customer']);
        $settings = $this->$model->is_branchwise_cus_reg();

			if($settings['branch_settings']==1)
			{
				if($settings['is_branchwise_cus_reg']==1)
				{
					$id_branch  = $cus['id_branch'];

				}
				else if($settings['branchWiseLogin']==1)
				{
					$id_branch=$this->config->item('pay_branchId');
				}
				else
				{
					$id_branch  = (isset($data['id_branch'])?$data['id_branch']:NULL);
				}

			}
		else{
			$id_branch =NULL;
		}*/
		$id_branch  = (isset($data['id_branch']) ? $data['id_branch'] : NULL);
		$schAcc = array(
			'id_customer'       => $data['id_customer'],
			'id_scheme'         => $data['id_scheme'],
			'start_date'        => date('Y-m-d H:i:s'),
			'group_code'        => (isset($data['group_code']) ? $data['group_code'] : ''),
			'scheme_acc_number' => ($data['is_new'] == 'N' ? $data['scheme_acc_number'] : NULL),
			'account_name'      => ucwords($data['account_name']),
			'is_new'		     => $data['is_new'],
			'active'            => 1,
			'date_add'          => date('Y-m-d H:i:s'),
			'added_by' 		 => 3,
			'id_branch'         => $id_branch,
			// "is_referral_by" 	 => $is_referral_by,
			'referal_code' 	 => (isset($data['referal_code']) ? $data['referal_code'] : NULL),
			'pan_no'		     => ($data['pan_no'] != null ? strtoupper($data['pan_no']) : NULL)
		);

		if (isset($data['is_new']) &&  $data['is_new'] == 'N') {
			if ($data['regExistingReqOtp'] == 0) {
				$schAcc['id_scheme_group'] = (!empty($data['id_scheme_group']) ? $data['id_scheme_group'] : '');
				$isAccNoExist = $this->scheme_modal->verify_existing($schAcc);
				if (!$isAccNoExist) {
					$scheme_acc  = array(
						"id_scheme" => ($data['id_scheme'] != '' ? $data['id_scheme'] : 0),
						"id_customer" =>  $data['id_customer'],
						"scheme_acc_number" => ($data['scheme_acc_number'] != '' ? $data['scheme_acc_number'] : NULL),
						"ac_name" => ($data['account_name'] != '' ? $data['account_name'] : NULL),
						"id_branch" => $id_branch,
						"id_employee" => NULL,
						'added_by' 		 => 2,
						"date_add" => date('Y-m-d H:i:s'),
						"id_scheme_group" => (isset($data['id_scheme_group']) ? $data['id_scheme_group'] : NULL),
						'pan_no'		     => ($data['pan_no'] != null ? strtoupper($data['pan_no']) : NULL),
						"status" => 0 // processing
					);
					$status =	$this->scheme_modal->join_existing($scheme_acc);
					if ($status['status']) {
						$result = array("status" => TRUE, "msg" => 'Kindly wait for your scheme activation, once activated you will be notified');
					} else {
						$result = array("status" => FALSE, "msg" => 'Unable to proceed your request..');
					}
				} else {
					if ($isAccNoExist['table'] == 'scheme_account') {
						$msg = 'Your account already exist, Please contact customer care to proceed your request';
					} else {
						$msg = 'You have already sent request for this account... Please wait until we verify your account. Check dashboard for status.';
					}
					$result = array("status" => FALSE, "msg" => $msg);
				}
			} else {
				$result = $this->join_existing_byacc($schAcc);
			}
			$this->response($result, 200);
		} else { // New account joining
			$this->db->trans_begin();

			if ($data['referal_code'] != '') {

				if ($data['referal_code'] != $data['mobile']) {
					$referral = $this->scheme_modal->checkreferral_code($data['referal_code'], $data['id_customer']);

					if ($referral['status'] == false) {
						$this->db->trans_rollback();
						$result = array('status' => FALSE, 'msg' => $referral['msg']);
						$this->response($result, 200);
					} else {
						$is_referral_by = (strtoupper($referral['is_referral_by']) == 'CUS' ? 0 : (strtoupper($referral['is_referral_by']) == 'EMP' ? 1 : NULL));
					}
				} else {
					$result = array('status' => FALSE, 'msg' => 'Invalid Referral code');
					$this->response($result, 200);
				}
			}

			$schAcc['is_refferal_by'] = $is_referral_by;
			$status = $this->$model->insert_schemeAcc($schAcc);
			$flash_msg = '';



			if ($status['sch_data']['free_payment'] == 1) {
				$pay_insert_data = $this->mobileapi_model->free_payment_data($status['sch_data'], $status['insertID']);
				if ($status['sch_data']['receipt_no_set'] == 1) {
					$pay_insert_data['receipt_no'] = $this->generate_receipt_no();
				}

				$pay_add_status = $this->payment_modal->addPayment($pay_insert_data);
				$flash_msg = 'As Free Installment offer, 1st installment of your scheme credited successfully. Kindly pay your 2nd installment';

				$scheme_acc_no = $this->$model->accno_generatorset();
				if ($scheme_acc_no['status'] == 1 && $scheme_acc_no['schemeacc_no_set'] == 0) {
					$scheme_acc_number = $this->$model->account_number_generator($schAcc['id_scheme']);
					if ($scheme_acc_number != NULL) {
						$updateData['scheme_acc_number'] = $scheme_acc_number;
					}
					$updSchAc = $this->$model->update_account($updateData, $status['insertID']);
				}
			}


			if ($this->db->trans_status() === TRUE) {
				$this->db->trans_commit();
				$schData1 = $this->$model->get_schemeaccount_detail($status['insertID']);
				$this->load->model("scheme_modal");
				$schData = $this->scheme_modal->getJoinedScheme($status['insertID']);

				$serviceID = 2;
				$service = $this->services_model->checkService($serviceID);

				$company = $this->$model->company_details();

				if ($service['sms'] == 1) {
					$id = $status['insertID'];
					$data = $this->services_model->get_SMS_data($serviceID, $id);
					$mobile = $data['mobile'];
					$message = $data['message'];
					if ($this->config->item('sms_gateway') == '1') {
						//$this->sms_model->sendSMS_MSG91($mobile,$message);
					} elseif ($this->config->item('sms_gateway') == '2') {
						$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
					} elseif ($this->config->item('sms_gateway') == '3') {
						$this->sms_model->sendSMS_SpearUC($mobile, $message, '', '');
					}
				}
				if ($service['serv_whatsapp'] == 1) {
					$this->services_model->send_whatsApp_message($mobile, $message);
				}

				if ($service['email'] == 1 && isset($schData[0]['email']) && $schData[0]['email'] != '') {
					$data['schData'] = $schData[0];
					$data['company'] = $company;
					$data['type'] = 1;
					$to = $schData[0]['email'];
					$subject = "Reg.  " . $this->comp['company_name'] . " scheme joining";
					$message = $this->load->view('include/emailScheme', $data, true);
					$sendEmail = $this->email_model->send_email($to, $subject, $message);
				}

				$result = array('status' => TRUE, 'msg' => 'Saving Scheme added to your account.', 'chit' => $schData1[0], 'free_pay' => $flash_msg);
			} else {
				$this->db->trans_rollback();
				$result = array("status" => FALSE, "msg" => "Unable to proceed your request");
			}
		}


		$this->response($result, 200);
	}

	function wallet_account_create($cus_id, $mobile)
	{
		$this->load->model('services_model');
		$this->load->model('email_model');
		$model = self::ADM_MODEL;
		$wallet_acc_no =  $this->services_model->get_wallet_acc_number();
		$insertData = array(
			'id_customer' 	   => (isset($cus_id) && $cus_id != '' ? $cus_id : NULL),
			'id_employee' 	   =>  2,
			'wallet_acc_number' => (isset($wallet_acc_no) ? $wallet_acc_no : NULL),
			'issued_date' 	   => date('y-m-d H:i:s'),
			'remark' 		    => "Credits",
			'active'		        => 1
		);
		//inserting data
		$status = $this->services_model->walletacc_insert($insertData);
		$wallAcc = $this->services_model->get_walletacc($status['insertID']);
		//$this->$model->insChitwallet($status['insertID'],$mobile,$cus_id);
		if ($status) {
			$serviceID = 8;
			$service =  $this->services_model->checkService($serviceID);
			$id = $status['insertID'];
			$data = $this->services_model->get_SMS_data($serviceID, $status['insertID']);
			$mobile = $data['mobile'];
			$message = $data['message'];
			if ($service['sms'] == 1) {
				if ($this->config->item('sms_gateway') == '1') {
					$this->sms_model->sendSMS_MSG91($mobile, $message);
				} elseif ($this->config->item('sms_gateway') == '2') {
					$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
				} elseif ($this->config->item('sms_gateway') == '3') {
					$this->sms_model->sendSMS_SpearUC($mobile, $message, '', '');
				}
			}
			if ($service['serv_whatsapp'] == 1) {
				$this->services_model->send_whatsApp_message($mobile, $message);
			}
		}
	}


	//to get customer data
	function customerSchemes_post()
	{
		$data = $this->get_values();
		$model = self::ADM_MODEL;
		$result['cusSchemes'] = array();
		$result['isValid'] = FALSE;
		$res = $this->$model->get_customerByMobile($data['cusmobile'], $data['emp_branch'], $data['branch_settings']);
		$result['customer'] = $res;
		if (sizeof($res) > 0) {
			$result['isValid'] = TRUE;
			$schemeAcc = $this->$model->get_payment_details($res['id_customer'], $res['id_branch']);
			$result['cusSchemes'] = $this->array_sort($schemeAcc['chits'], 'allow_pay', SORT_DESC);
		}
		$this->response($result, 200);
	}

	//to get customer data
	function getCusByMobile_post()
	{
		$data = $this->get_values();
		$res = array();
		$model = self::ADM_MODEL;
		$res['cus'] = $this->$model->cusByMobileBranchWise($data['cusmobile'], $data['emp_branch'], $data['branch_settings']);
		if (!empty($res['cus'])) {
			$res['status'] = TRUE;
		} else {
			$res['status'] = FALSE;
		}
		$this->response($res, 200);
	}

	function getScheme_get()
	{
		$show_referral = true;
		$model = self::ADM_MODEL;
		$scheme = $this->$model->get_scheme($this->get('id_scheme'), $this->get('id_customer'));
		$cus_single = $scheme['cusbenefitscrt_type'];
		$emp_single = $scheme['empbenefitscrt_type'];
		$cus_ref_code = $scheme['cus_ref_code'];
		$emp_ref_code = $scheme['emp_ref_code'];

		if ($cus_single == 0 && $emp_single == 0) {
			if ($cus_ref_code != '' && $emp_ref_code != '') {

				$show_referral = false;
			} else if ($cus_ref_code == '' && $emp_ref_code == '') {

				$show_referral = true;
			} else if ($cus_ref_code != '' || $emp_ref_code != '') {
				$show_referral = true;
			} else {
				return $show_referral;
			}
		}


		$groups = $this->scheme_modal->get_groups($this->get('id_scheme'));
		$allow_unpaid = $this->scheme_modal->allowUnpaid();	//allow customer not paid single installment
		$allow_multiple = $this->scheme_modal->allowMultipleChits(); //allow multiple chits for customer

		$allowNewsch = $this->scheme_modal->allowNewscheme_join(); //allow New scheme join to customer
		$unpaid = $this->scheme_modal->check_unpaid_schemes($this->get('id_customer'));
		$unClosedAcc = $this->scheme_modal->hasUnclosedAccounts($this->get('id_customer'));
		$result = '';
		if ($allowNewsch == TRUE) {
			if ($allow_multiple == TRUE) {
				if ($allow_unpaid == TRUE) {
					$allow_join = array('status' => TRUE);
				} else {
					if ($unpaid == TRUE) {
						$allow_join = array('status' => FALSE, 'msg' => 'You can\'t join now, as you have scheme accounts without single payment, make payments for unpaid before joining new scheme');
					} else {
						$allow_join = array('status' => TRUE);
					}
				}
			} else {
				if ($unClosedAcc == TRUE) {
					$allow_join = array('status' => FALSE, 'msg' => 'You have unclosed chits, kindly contact customer care and close to join new scheme.');
				} else {
					$allow_join = array('status' => TRUE);
				}
			}
			$allow_join = array('status' => TRUE);
		} else {
			$allow_join = array('status' => FALSE, 'msg' => 'Kindly visit our showroom for new scheme enrollment....');
		}

		$weights = array();
		$weights_data = $this->$model->get_weights();

		foreach ($weights_data as $weight) {
			$weights[] = array(
				'weight'    => $weight['weight']
			);
		}
		$result = array('scheme' => $scheme, 'show_referral' => $show_referral, 'allow_join' => $allow_join, 'weights' => $weights, 'groups' => $groups);
		$this->response($result, 200);
	}

	function paymentHistory_get()
	{
		$model = self::ADM_MODEL;
		$payments = $this->$model->get_paymenthistory($this->get('mobile'), $this->get('id_employee'));
		$this->response($payments, 200);
	}


	// branch name list
	public function get_branch_get()
	{
		$model = self::ADM_MODEL;
		$data = $this->$model->get_branch($this->get('emp_branch'), $this->get('id_profile'));
		echo json_encode($data);
	}

	function get_ret_dashboard_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		$id_branch	= $data['id_branch'];
		$id_emp 	= 0; //$data['id_employee'];
		$from_date	= date("Y-m-d");
		//$from_date = date('Y-m-d', strtotime('-29 day', strtotime($from_date)));

		$to_date	= date("Y-m-d");
		$result['esti_sale'] = $this->$model->get_dash_esti_sale($from_date, $to_date, $id_emp, $id_branch);
		$result['esti_old_metal'] = $this->$model->get_dash_esti_old_metal($from_date, $to_date, $id_emp, $id_branch);
		$this->response($result, 200);
	}
	/* Start of Customer Master functions */
	public function createQuickCustomer_post()
	{
		$model = "ret_app_api_model";
		$postData = $this->get_values();
		if (!empty($postData['firstname']) && !empty($postData['mobile']) && !empty($postData['id_branch'])) {
			$isCusExist = $this->$model->isCusExist($postData['mobile']);
			if (!$isCusExist['status']) {

				$this->db->trans_begin();

				$insData = array(
					"firstname" => $postData['firstname'],
					"mobile"    => $postData['mobile'],
					'username'    => $postData['mobile'],
					'passwd'    => $this->__encrypt($postData['mobile']),
					"id_branch" => $postData['id_branch'],
					"id_village" => isset($postData['id_village']) ? $postData['id_village'] : NULL,
					"cus_type"  => empty($postData['cus_type']) ? 1 : $postData['cus_type'],
					"date_add"  => date("Y-m-d H:i:s"),
					"active"    => 1,
					"added_by"  => 4, // Retail App
				);

				$ins = $this->$model->insertData($insData, "customer");

				if ($ins > 0) {

					$insAddrData = array(
						"id_country"  => $postData['id_country'],
						"id_state"    => $postData['id_state'],
						'id_city'     => $postData['id_city'],
						'id_customer' => $ins,
						"address1"    => $postData['address1'],
						"address2"    => $postData['address2'],
						"address3"    => isset($postData['address3']) ? $postData['address3'] : NULL,
						"pincode"     => isset($postData['pincode']) ? $postData['pincode'] : NULL,
						"date_add"  => date("Y-m-d H:i:s"),
						"active"    => 1
					);

					$this->$model->insertData($insAddrData, "address");

					if ($this->db->trans_status() == TRUE) {
						$this->db->trans_commit();

						$insData['id_customer'] = $ins;
						$insData['label'] = $postData['firstname'];

						$result = array("success" => TRUE, "response" => $insData, "message" => "Customer created successfully");
					} else {

						$this->db->trans_rollback();
						$result = array("success" => FALSE, "response" => array(), "message" => "Unable to proceed your request");
					}
				} else {
					$result = array("success" => FALSE, "response" => array(), "message" => "Unable to proceed your request");
				}
				$this->response($result, 200);
			} else {
				$this->response(array("success" => FALSE, "response" => array(), "message" => $isCusExist['message']), 200);
			}
		} else {
			$this->response(array("success" => FALSE, "response" => array(), "message" => "Please fill all the required fields"), 200);
		}
	}
	function getEstiPrintData_post()
	{

		$model = self::ADM_MODEL;

		$postData = $this->get_values();

		if (!empty($postData['esti_no'])) {

			$dCData = $this->$model->getBranchDayClosingData($postData['id_branch']);

			if (sizeof($dCData) > 0) {

				if ($dCData['entry_date'] != "") {

					$est_id = $this->$model->getEstiID($postData['esti_no'], $dCData['entry_date'], $postData['id_branch']);

					if ($est_id > 0) {

						$esti_name = 'E_' . $est_id;

						$date = date_create($dCData['entry_date']);

						$est_date = date_format($date, "d-m-Y");

						$folder = "";

						if ($postData['id_branch'] > 0) {

							$folder = self::EST_PATH . '' . $postData['id_branch'] . '/' . $est_date . '/';
						} else {

							$folder = self::EST_PATH . '' . $est_date . '/';
						}

						$actual_file_name = "";

						$files = scandir($folder); // Get a list of files in the folder

						foreach ($files as $fileName) {

							if (strpos($fileName, $esti_name) !== false) {

								$actual_file_name = $fileName;

								break;
							}
						}

						$file = $folder . $actual_file_name;

						if (file_exists($file)) {

							unlink($file);
						}

						$uploadPdf = $this->generate_invoice($est_id);

						$result = array("status" => TRUE, "msg" => "Estimation retrieved successfully", "printURL" => $uploadPdf['url'], "esti_name" => $uploadPdf['file_name']);
					} else {

						$result = array("status" => FALSE, "msg" => "Invalid Estimation No.");
					}
				} else {

					$result = array("status" => FALSE, "msg" => "Invalid Estimation date.");
				}
			}
		} else {

			$result = array("status" => FALSE, "msg" => "Estimation No. is required");
		}

		$this->response($result, 200);
	}
	function getOldMetalType_get()
	{
		$model = self::ADM_MODEL;
		$result = $this->$model->getOldMetalType();
		$this->response($result, 200);
	}
	function getAllPurities_get()
	{
		$model = self::ADM_MODEL;
		$result = $this->$model->getAllPurities($this->get('id_product'));
		$this->response($result, 200);
	}
	function getOldMetalCategory_get()
	{
		$model = self::EST_MODEL;
		$result = $this->$model->get_old_metal_category();
		$this->response($result, 200);
	}
	public function getAllOldMetalRates_get()
	{
		$model = self::EST_MODEL;
		$result = $this->$model->get_all_old_metal_rates();
		$this->response($result, 200);
	}

	function getCurrentDayEstimationsByBranch_post()
	{
		$model = "ret_app_api_model";
		$this->load->model($model);
		$data = $this->get_values();
		$id_branch  = (isset($data['id_branch']) ? $data['id_branch'] : '');
		$empId      = 0; //(isset($data['id_emp'])?$data['id_emp']:'');
		$type      = (isset($data['type']) ? $data['type'] : '');
		if ($id_branch != "") {
			$estdetails = $this->$model->getCurrentDayEstimationsByBranch($id_branch, $empId, $type);
			foreach ($estdetails as $ekey => $eval) {
				$estdetails[$ekey]['est_printable'] = ''; //$this->getEstimationData($eval['estimation_id']);
			}
			$this->response(array('status' => true, 'msg' => 'Records retrieved successfully', 'estdetails' => $estdetails), 200);
		} else {
			$this->response(array('status' => false, 'msg' => 'Branch Id required.',  'estdetails' => []), 200);
		}
	}

	function getEstimationData($est_id)
	{
		$this->load->model("ret_estimation_model");
		$model = "ret_estimation_model";
		$estimation     = $this->$model->get_entry_records($est_id);
		$est_other_item = $this->$model->getOtherEstimateItemsDetails($est_id);
		$metal_rates     = $this->$model->get_branchwise_rate($estimation['id_branch']);
		$estmaionstring = "";
		$estmaionstring .= "  \t \x1b\x45\x01  Estimation - " . $estimation['esti_no'] . ($estimation['short_name'] != '' ? '-' . $estimation['short_name'] : '') . "\x1b\x45\x00 \r\n";
		$estmaionstring .= "\x1b\x45\x01" . $estimation['customer_name'] . ($estimation['village_name'] != '' ? ' / ' . $estimation['village_name'] : '') . ' / ' . $estimation['mobile'] . "\x1b\x45\x00 \r\n";
		$estmaionstring .= $estimation['estimation_datetime'] . " \r\n";
		$estmaionstring .= "Gold  \t" . number_format($metal_rates['goldrate_22ct'], 2, '.', '') . "\t SILVER  \t" . $metal_rates['silverrate_1gm'] . " \r\n";
		if (sizeof($est_other_item['item_details'])) {
			$estmaionstring .= self::HORIZONTAL_LINE['HR3_58MM'] . "\r\n";
			$estmaionstring .= " GWT \t NWT \t VA(%) \t MC \t VALUE\r\n";
			$estmaionstring .= self::HORIZONTAL_LINE['HR3_58MM'] . "\r\n";



			$tot_payable = 0;

			$tot_purchase = 0;

			$market_rate_cost = 0;

			$total_wt = 0;

			$net_wt = 0;

			$total_piece = 0;

			$total_gwt = 0;

			$total_net_wt = 0;

			$tag_net_wt = 0;

			$making_charge = 0;

			$total_tax = 0;

			$sub_total = 0;

			$paid_advance   = 0;

			$paid_weight    = 0;

			$wt_amt         = 0;

			$tot_adv_paid   = 0;

			if (sizeof($est_other_item['advance_details']) > 0) {

				foreach ($est_other_item['advance_details'] as $advance) {

					$paid_advance += $advance['paid_advance'];

					$paid_weight += $advance['paid_weight'];

					$wt_amt += ($advance['paid_weight']);
				}

				$tot_adv_paid = number_format(($paid_advance + $wt_amt), 2, '.', '');
			}

			$chit_amount   = 0;
			if (sizeof($est_other_item['chit_details']) > 0) {
				foreach ($est_other_item['chit_details'] as $advance) {
					$chit_amount += $advance['utl_amount'];
				}
			}





			$item_no = 1;

			foreach ($est_other_item['item_details'] as $items) {

				$making_charge = 0;

				$stone_price = 0;

				$stone_weight = 0;

				$stone_piece = 0;

				$charge_price = 0;



				$certification_cost = 0;



				$total_piece += $items['piece'];



				$total_gwt += $items['gross_wt'];

				$total_wt += $items['net_wt'];


				$tot_payable += $items['item_cost'];



				$market_rate_cost += $items['market_rate_cost'];

				$payable_without_tax = $items['item_cost'] - $items['item_total_tax'];
				$sub_total += $payable_without_tax;

				$total_tax += $items['item_total_tax'];



				if ($items['is_partial'] == 1) {

					$net_wt += $items['net_wt'];

					$tag_net_wt += $items['tag_net_wt'];
				}

				if ($items['calculation_based_on'] == 0) {

					$wast_wgt = number_format((($items['gross_wt']) * ($items['wastage_percent'] / 100)), 2, '.', '');



					$making_charge = ($items['mc_type'] == 2 ? $items['gross_wt'] * $items['mc_value'] : $items['mc_value']);
				} else if ($items['calculation_based_on'] == 1) {

					$wast_wgt = number_format((($items['net_wt']) * ($items['wastage_percent'] / 100)), 2, '.', '');



					$making_charge = ($items['mc_type'] == 2 ? $items['net_wt'] * $items['mc_value'] : $items['mc_value']);
				} else if ($items['calculation_based_on'] == 2) {

					$wast_wgt = number_format((($items['net_wt']) * ($items['wastage_percent'] / 100)), 3, '.', '');



					$making_charge = ($items['mc_type'] == 2 ? $items['gross_wt'] * $items['mc_value'] : $items['mc_value']);
				}

				foreach ($items['stone_details'] as $stone) {

					$stone_price += $stone['price'];

					$stone_weight += $stone['wt'];

					$stone_piece += $stone['pieces'];

					$certification_cost += $stone['certification_cost'];
				}

				foreach ($items['charges'] as $charge) {
					$charge_price += $charge['amount'];
				}
				$estmaionstring .= $item_no . " ) " . substr($items['sub_design_name'], 0, 15) . "(" . $items['tag_code'] . ")\r\n";
				$estmaionstring .= "";
				$estmaionstring .= $items['gross_wt'] . " \t " . $items['net_wt'] . "  " . $items['wastage_percent'] . " \t " . $making_charge . "  " . ($making_charge == 0 ? " "  : '') . " " . $this->moneyFormatIndia(number_format($items['item_cost'] - $charge_price - $items['item_total_tax'] - $stone_price, 2, '.', '')) . " \r\n";
				if (sizeof($items['stone_details']) > 0) {
					foreach ($items['stone_details'] as $stone) {
						$estmaionstring .= substr($stone['stone_name'], 0, 8) . "\t " . $stone['pieces'] . " \t " . $stone['wt'] . " \t " . $stone['price'] . " \r\n";
					}
				}

				foreach ($items['charges'] as $charge) {
					$estmaionstring .= $charge['code_charge'] . "\t\t\t\t" . number_format($charge_price, 2, '.', '') . "\r\n";
				}

				//$estmaionstring .= "VA \t" .$items['wastage_percent']."\r\n";

				$item_no++;
			}
			$estmaionstring .= self::HORIZONTAL_LINE['HR3_58MM'] . "\r\n";

			$estmaionstring .= "SUB TOTAL \t\t\t" . $this->moneyFormatIndia($sub_total) . "\r\n";

			$estmaionstring .= "CGST " . ($items['tax_percentage'] / 2) . " (%) \t\t\t" . $this->moneyFormatIndia(number_format($total_tax / 2, 2, '.', '')) . "\r\n";

			$estmaionstring .= "SGST " . ($items['tax_percentage'] / 2) . " (%) \t\t\t" . $this->moneyFormatIndia(number_format($total_tax / 2, 2, '.', '')) . "\r\n";

			$estmaionstring .= self::HORIZONTAL_LINE['HR3_58MM'] . "\r\n";

			if ($tot_adv_paid > 0) {
				$estmaionstring .=  " Adv Paid \t\t\t" . $this->moneyFormatIndia(number_format($tot_adv_paid, 2, '.', '')) . "\r\n";
			}

			if ($chit_amount > 0) {
				$estmaionstring .=  " CHIT ADJ \t\t\t" . $this->moneyFormatIndia(number_format($chit_amount, 2, '.', '')) . "\r\n";
			}

			$estmaionstring .=  "\x1b\x45\x01 Total" . " " . $total_piece . "\t" . number_format($total_gwt, 3, '.', '') . " " . "\t" . number_format($total_wt, 3, '.', '') . "\t" . $this->moneyFormatIndia(number_format($tot_payable - $tot_adv_paid - $chit_amount, 2, '.', '')) . "\x1b\x45\x00 \r\n";

			$estmaionstring .= self::HORIZONTAL_LINE['HR3_58MM'] . "\r\n";

			if ($tag_net_wt != 0) {
				$estmaionstring .= "PARTLY " . number_format($tag_net_wt, 3, '.', '') . '-' . number_format($net_wt, 3, '.', '') . number_format(($tag_net_wt - $net_wt), 3, '.', '') . "\r\n";
			}
		}

		if (sizeof($est_other_item['old_matel_details']) > 0) {
			$estmaionstring .= "Purchase Items" . "\r\n";

			$estmaionstring .= self::HORIZONTAL_LINE['HR3_58MM'] . "\r\n";

			$estmaionstring .= " METAL\t GR WT\t V.A \t  RATE \t VALUE \r\n";

			$estmaionstring .= self::HORIZONTAL_LINE['HR3_58MM'] . "\r\n";

			$gross_wt = 0;

			$total_va = 0;

			$amount = 0;

			foreach ($est_other_item['old_matel_details'] as $data) {

				$gross_wt += $data['gross_wt'];

				$total_va += $data['wastage_wt'];

				$amount += $data['amount'];

				$estmaionstring .=  $data['old_metal_type'] . "  " . $data['gross_wt'] . "  " . $data['wastage_wt'] . "  " . $data['rate_per_gram'] . "  " . $this->moneyFormatIndia($data['amount']) . "\r\n";
			}
			$estmaionstring .= self::HORIZONTAL_LINE['HR3_58MM'] . "\r\n";

			$estmaionstring .= "\t  " . number_format($gross_wt, 3, '.', '') . "  " . number_format($total_va, 3, '.', '') . "\t\t  " . $this->moneyFormatIndia(number_format($amount, 2, '.', '')) . "\r\n";

			$estmaionstring .= self::HORIZONTAL_LINE['HR3_58MM'] . "\r\n";

			$estmaionstring .= "\x1b\x61\x02 \x1b\x45\x01 Sales : " . $this->moneyFormatIndia(number_format(($tot_payable - $tot_adv_paid - $chit_amount), 2, '.', '')) . "\x1b\x45\x00 \r\n";
			$tot_purchase = number_format($amount, 2, '.', '');
			$estmaionstring .= "\x1b\x61\x02 \x1b\x45\x01 Purchase : " . $this->moneyFormatIndia(number_format(($tot_purchase), 2, '.', '')) . "\x1b\x45\x00 \r\n";
			$estmaionstring .= "\x1b\x61\x02 \x1b\x45\x01 Total : " . $this->moneyFormatIndia(number_format(($tot_payable - $tot_purchase - $tot_adv_paid - $chit_amount), 2, '.', '')) . "\x1b\x45\x00 \r\n";
		}
		$estmaionstring .= "\n\n\r\r\r\r\n";

		$estmaionstring .= "\x1b\x61\x02 \x1b\x45\x01 Rs. " . $this->moneyFormatIndia(number_format(($tot_payable - $tot_purchase - $tot_adv_paid - $chit_amount), 2, '.', '')) . "\x1b\x45\x00 \x1b\x61\x02 \n";

		//$estmaionstring .= "EMP-ID : ".$estimation['id_employee'].' / '.$estimation['emp_name'].' / '.date('h:i A', strtotime(date('d-m-Y H:i:s')))."\r\n";
		$estmaionstring .= "\x1b\x61\x00 EMP-ID : " . $estimation['id_employee'] . ' / ' . $estimation['emp_name'] . ' / ' . date('h:i A', strtotime($estimation['estimation_datetime'])) . "\r\n\n\n";

		$estmaionstring .= '\n';

		//echo "<pre>";print_r($estmaionstring);exit;
		return $estmaionstring;
	}

	function moneyFormatIndia($num)
	{
		return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
	}

	function getRetCharges_get()
	{
		$responsedata = array();
		$model = self::ADM_MODEL;
		$res = $this->$model->get_retCharges();
		$responsedata = array('status' => true, 'charges' => $res);
		$this->response($responsedata, 200);
	}

	function importTagData_get()
	{
		$model = self::ADM_MODEL;
		$this->db->trans_begin();
		$res = $this->$model->importTagData();
		if ($this->db->trans_status() == TRUE) {
			$this->db->trans_commit();
			$responsedata = array('status' => true, 'respose' => $res);
			$this->response($responsedata, 200);
		} else {
			$this->db->trans_rollback();
			$responsedata = array("status" => FALSE, "msg" => "Unable to proceed your request,please try again..");
			$this->response($responsedata, 200);
		}
	}

	function importTagCharges_get()
	{
		$model = self::ADM_MODEL;
		$this->db->trans_begin();
		$res = $this->$model->importTagCharges();
		if ($this->db->trans_status() == TRUE) {
			$this->db->trans_commit();
			$responsedata = array('status' => true, 'respose' => $res);
			$this->response($responsedata, 200);
		} else {
			$this->db->trans_rollback();
			$responsedata = array("status" => FALSE, "msg" => "Unable to proceed your request,please try again..");
			$this->response($responsedata, 200);
		}
	}
	function importTagStones_get()
	{
		$model = self::ADM_MODEL;
		$this->db->trans_begin();
		$res = $this->$model->importTagStones();
		if ($this->db->trans_status() == TRUE) {
			$this->db->trans_commit();
			$responsedata = array('status' => true, 'respose' => $res);
			$this->response($responsedata, 200);
		} else {
			$this->db->trans_rollback();
			$responsedata = array("status" => FALSE, "msg" => "Unable to proceed your request,please try again..");
			$this->response($responsedata, 200);
		}
	}

	function importCustomerBalance_get()
	{
		$model = self::ADM_MODEL;
		$this->db->trans_begin();
		$res = $this->$model->importCustomerBalance();
		if ($this->db->trans_status() == TRUE) {
			$this->db->trans_commit();
			$responsedata = array('status' => true, 'respose' => $res);
			$this->response($responsedata, 200);
		} else {
			$this->db->trans_rollback();
			$responsedata = array("status" => FALSE, "msg" => "Unable to proceed your request,please try again..");
			$this->response($responsedata, 200);
		}
	}

	function importCustomer_get()
	{
		$model = self::ADM_MODEL;
		$this->db->trans_begin();
		$res = $this->$model->importCustomer();
		if ($this->db->trans_status() == TRUE) {
			$this->db->trans_commit();
			$responsedata = array('status' => true, 'respose' => $res);
			$this->response($responsedata, 200);
		} else {
			$this->db->trans_rollback();
			$responsedata = array("status" => FALSE, "msg" => "Unable to proceed your request,please try again..");
			$this->response($responsedata, 200);
		}
	}

	function importOldTagData_get()
	{
		$model = self::ADM_MODEL;
		$this->db->trans_begin();
		$res = $this->$model->importOldTagData();
		if ($this->db->trans_status() == TRUE) {
			$this->db->trans_commit();
			$responsedata = array('status' => true, 'respose' => $res);
			$this->response($responsedata, 200);
		} else {
			$this->db->trans_rollback();
			$responsedata = array("status" => FALSE, "msg" => "Unable to proceed your request,please try again..");
			$this->response($responsedata, 200);
		}
	}

	function previousRates_get()
	{
		$model = self::EST_MODEL;
		$result = $this->$model->get_current_metal_rates();
		$this->response($result, 200);
	}

	public function getProductSubDesignBySearch_post()
	{

		$model = "ret_estimation_model";

		$postdata = $this->get_values();

		$searchTxt = $postdata['searchTxt'];

		$id_branch = (isset($postdata['id_branch']) ? $postdata['id_branch'] : '');

		$ProCode = (isset($postdata['ProCode']) ? $postdata['ProCode'] : '');

		$data = $this->$model->getProductSubDesignBySearch($searchTxt, $ProCode, $id_branch);

		echo json_encode($data);
	}

	public function getProductBySearch_post()
	{

		$return_data = array();

		$model = "ret_estimation_model";

		$postdata = $this->get_values();

		$searchTxt = (isset($postdata['searchTxt']) ? $postdata['searchTxt'] : '');

		$id_branch = (isset($postdata['id_branch']) ? $postdata['id_branch'] : '');

		$products = $this->$model->getProductBySearch($searchTxt, 0, 0, $id_branch, 0);

		foreach ($products as $prod) {

			$return_data[] = array(

				'old_metal_prod_id' => $prod['value'],

				'old_metal_prod_name' => $prod['label']

			);
		}

		echo json_encode($return_data);
	}


	function set_alignment($no_chars, $align, $word)
	{
		$return_word = "";
		$word_count = strlen($word);
		if ($align == 1) {
			$no_space = $no_chars - $word_count;
			$spaces = "";
			while ($no_space > 0) {
				$spaces = $spaces . " ";
				$no_space = $no_space - 1;
			}
			$return_word = $return_word . $word . $spaces;
		} else if ($align == 2) {
			$no_space = $no_chars - $word_count;
			$spaces = "";
			while ($no_space > 0) {
				$spaces = $spaces . " ";
				$no_space = $no_space - 1;
			}
			$return_word = $return_word . $spaces . $word;
		} else if ($align == 3) {
			$no_space = $no_chars - $word_count;

			$no_left_space = ceil($no_space / 2);
			$no_right_space = floor($no_space / 2);

			$left_spaces = "";
			$right_spaces = "";

			while ($no_left_space > 0) {
				$left_spaces = $left_spaces . " ";
				$no_left_space = $no_left_space - 1;
			}

			while ($no_right_space > 0) {
				$right_spaces = $right_spaces . " ";
				$no_right_space = $no_right_space - 1;
			}

			$return_word = $return_word . $left_spaces . $word . $right_spaces;
		}

		return $return_word;
	}

	function printbill_get($billcode)
	{
		ini_set('memory_limit', '512M');
		set_time_limit(300);

		$this->load->model('ret_billing_model');
		$model = "ret_billing_model";
		$id = $billcode;
		$data['billing'] = $this->$model->getBillingDetails($id);
		$data['payment'] = $this->$model->getPaymentDetails($id);
		$data['metal_rate'] = $this->$model->getBillingMetalrate($data['billing']['id_branch'], $data['billing']['bill_date']);
		$data['metal_rate'] = $this->$model->get_branchwise_rate($data['billing']['id_branch']);
		$data['est_other_item'] = $this->$model->getOtherEstimateItemsDetails($id, $data['billing']['bill_type']);
		$data['comp_details'] = $this->$model->getCompanyDetails($data['billing']['id_branch']);
		$data['settings'] = $this->$model->get_retSettings();
		$data['qrfilename'] = $id;

		$data['invoicesplit'] = array();

		foreach ($data['est_other_item']['item_details'] as $key => $item) {

			$data['invoicesplit'][$item['invoiceno']][$key] = $item;
		}

		ksort($data['invoicesplit'], SORT_NUMERIC);

		$data['oldmetalsplit'] = array();

		foreach ($data['est_other_item']['old_matel_details'] as $key => $item) {

			$data['oldmetalsplit'][$item['pur_refId']][$key] = $item;
		}

		ksort($data['oldmetalsplit'], SORT_NUMERIC);

		$this->load->helper(array('dompdf', 'file'));
		
		$dompdf = new Dompdf();
		$html = $this->load->view('billing/print/bill_format_2', $data, true);
		$html = preg_replace('/>\s+</', "><", $html);
		$dompdf->load_html($html);
		$dompdf->set_paper("a4", "portriat");
		$dompdf->render();
		while(ob_get_level()) ob_end_clean();
		$dompdf->stream("Receipt.pdf", array('Attachment' => 1));
		exit;
	}

	function get_filter_get()
	{

		$supplier = isset($_GET['supplier']) ? json_decode($_GET['supplier']) : false;

		$resultArr = array();
		$model = self::ADM_MODEL;
		try {
			if ($supplier == true) {
				$resultArr    = $this->$model->getSuppFilters();
			} else {
				$resultArr    = $this->$model->getFilters();
			}
			if (sizeof($resultArr) > 0) {
				$success = true;
				$message = "Filters retrieved successfully..";
			} else {
				$success = true;
				$message = "No filters available..";
			}
		} catch (Exception $e) {
			$success = false;
			$message = $e->getMessage();
		}
		$result = array("Status" => $success, "Description" => $message, 'ReturnObject' => $resultArr);
		//$this->response($result, 200);
		echo json_encode($result);
	}

	function applyFilter_post()
	{
		$resultArr = array();
		$model = self::ADM_MODEL;
		try {
			$postData = $this->get_values();

			$supplier = isset($postData['supplier']) ? $postData['supplier'] : false;

			if ($supplier == true) {
				$resultArr    = $this->$model->applySuppFilter($postData, 'getData');
			} else {
				$resultArr    = $this->$model->applyFilter($postData, 'getData');
			}

			$total_records    = count($resultArr);
			if (sizeof($resultArr) > 0) {
				$success = true;
				$message = "Records retrieved successfully..";
			} else {
				$success = true;
				$message = "No records found..";
			}
		} catch (Exception $e) {
			$success = false;
			$message = $e->getMessage();
		}
		$result = array("Status" => $success, "Description" => $message, 'total_records' => "", 'ReturnObject' => $resultArr);
		//$this->response($result, 200);
		echo json_encode($result);
	}

	function deletetagimage_post()

	{

		$data = $this->get_values();

		$deletetagimage = $this->deleteTagImage($data['tagid'], $data['imgid']);

		if ($deletetagimage['status'] == 1) {

			$result = array('success' => TRUE, 'msg' => $deletetagimage['message']);
		} else {

			$result = array('success' => FALSE, 'msg' => $deletetagimage['message']);
		}

		$this->response($result, 200);
	}

	function deleteTagImage($tag_id, $imgid)
	{

		if ($tag_id != "" && $imgid != "") {

			$model = self::ADM_MODEL;

			$tagimages = $this->$model->get_img_by_id($imgid, $tag_id);

			foreach ($tagimages as $tkey => $tvalimage) {

				if ($this->$model->deleteData('id_tag_img', $imgid, 'ret_taging_images')) {

					unlink(self::TAGIMG_PATH . "/" . $tvalimage['image']);
				}
			}

			return array('status' => 1, 'message' => 'Tag image deleted!..');
		} else {

			return array('status' => 0, 'message' => 'Required tag id and image id to delete!..');
		}
	}

	function uploadTagimage_post()
{
    $model = self::ADM_MODEL;
    $data  = $this->get_values();

    $tag_id = $data['tagid'] ?? null;

    if (!$tag_id || empty($data['images']) || !is_array($data['images'])) {
        $this->response([
            'status' => false,
            'msg'    => 'Invalid input data'
        ], 200);
        return;
    }

    $img_path = self::TAGIMG_PATH;

    if (!is_dir($img_path)) {
        mkdir($img_path, 0777, TRUE);
    }

    // Reset default images ONCE if any image is default
    foreach ($data['images'] as $imgObj) {
        if (!empty($imgObj->default) && $imgObj->default == 1) {
            $this->$model->updateData(['is_default' => 0], 'tag_id', $tag_id, 'ret_taging_images');
            break;
        }
    }

    $uploaded = [];

    foreach ($data['images'] as $index => $imgObj) {

        if (empty($imgObj->image)) {
            continue;
        }

        $base64_image = $imgObj->image;

        // Decode base64
        $decoded_image = base64_decode($base64_image);
        if ($decoded_image === false) {
            continue;
        }

        $src = imagecreatefromstring($decoded_image);
        if ($src === false) {
            continue;
        }

        $width  = imagesx($src);
        $height = imagesy($src);

        // Use filename from app OR generate fallback
        $filename = !empty($imgObj->fileName)
            ? $tag_id . '_' . time() . '_' . $imgObj->fileName
            : $tag_id . '_' . mt_rand(100000, 999999) . '.jpg';

        $path = $img_path . '/' . $filename;

        $tmp = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($tmp, 255, 255, 255);
        imagefill($tmp, 0, 0, $white);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);

        if (imagejpeg($tmp, $path, 90)) {

            $insert = [
                'tag_id'     => $tag_id,
                'image'      => $filename,
                'is_default' => (!empty($imgObj->default) && $imgObj->default == 1) ? 1 : 0
            ];

            $imageid = $this->$model->insertData($insert, 'ret_taging_images');

            if ($imageid) {
                $uploaded[] = [
                    'imageid' => $imageid,
                    'path'    => base_url() . $path . '?nocache=' . time()
                ];
            } else {
                unlink($path);
            }
        }

        imagedestroy($src);
        imagedestroy($tmp);
    }

    if (!empty($uploaded)) {
        $this->response([
            'status' => true,
            'data'   => $uploaded,
            'msg'    => 'Image(s) uploaded successfully'
        ], 200);
    } else {
        $this->response([
            'status' => false,
            'msg'    => 'No images uploaded'
        ], 200);
    }
}
	/* function uploadTagimage_post()
	{

		//$file = $this->request->getFile('image');

		//$profile_image = $file->getName();

		//$this->response(array('status' => true   ,'msg' => $_POST['imgid'] ),200);



		$model = self::ADM_MODEL;

		$arrdata = $_POST;

		//$data = json_decode(json_encode($arrdata), FALSE);

		$tag_id = $_POST['tagid'];

		$default = $_POST['default'];

		//$image_id = $_POST['imgid'];

		if(!empty($tag_id) && $tag_id != null && $tag_id != 'null'){ // Delete Older image

            //$delImg = $this->deleteTagImage($tag_id , $image_id);

			$model = "ret_tag_model";

			$this->load->model($model);

            $tagimages = $this->$model->get_img_by_id($tag_id);

            //$this->response(array('status' => true   ,'msg' => $tagimages ),200);

			$is_img_deleted = true;

            foreach($tagimages as $tkey => $tvalimage){

                //if($tvalimage['is_default'] == 1){

        	        if($this->$model->deleteData('id_tag_img', $tvalimage['id_tag_img'], 'ret_taging_images')){

        	            unlink(self::TAGIMG_PATH."/".$tvalimage['image']);

        	        } else {

						$is_img_deleted = false;

						break;

					}

               // }

            }

            if(!$is_img_deleted){

                $this->response(array('status' => false , 'filename' => null, 'msg' => "Unable to delete older images" ) ,200);

            }

        }

		//make other images
		if ($default == 1) {

			$imgData = array(

				'is_default' => 0

			);

			$this->$model->updateData($imgData, "tag_id", $tag_id, "ret_taging_images");
		}

		$img_path = self::TAGIMG_PATH;

		if ($tag_id && $_FILES) {

			if (!is_dir($img_path)) {

				mkdir($img_path, 0777, TRUE);
			}

			if ($_FILES['name']['name']) {

				$img = $_FILES['name']['tmp_name'];

				$filename =  $tag_id . "_" . mt_rand(100001, 999999) . ".jpg";

				$path = self::TAGIMG_PATH . "/" . $filename;



				if (($img_info = getimagesize($img)) === FALSE) {

					// die("Image not found or not an image");

					$this->response(array('status' => false, 'filename' => null, 'msg' => "Image not found"), 200);
				}

				$width = $img_info[0];

				$height = $img_info[1];



				switch ($img_info[2]) {

					case IMAGETYPE_GIF:
						$src = imagecreatefromgif($img);

						$tmp = imagecreatetruecolor($width, $height);

						$kek = imagecolorallocate($tmp, 255, 255, 255);

						imagefill($tmp, 0, 0, $kek);

						break;

					case IMAGETYPE_JPEG:
						$src = imagecreatefromjpeg($img);

						$tmp = imagecreatetruecolor($width, $height);

						break;

					case IMAGETYPE_PNG:
						$src = imagecreatefrompng($img);

						$tmp = imagecreatetruecolor($width, $height);

						$kek = imagecolorallocate($tmp, 255, 255, 255);

						imagefill($tmp, 0, 0, $kek);

						break;

					default: //die("Unknown filetype");

						$this->response(array('status' => false, 'path' => null), 200);
				}



				imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);

				imagejpeg($tmp, $path);



				$arrayimg_tag = array(

					'tag_id'   => $tag_id,

					'image'    => $filename,

					'date_add' => date("Y-m-d  H:i:s"),

					'is_default' => $default

				);

				$status = $this->$model->insertData($arrayimg_tag, 'ret_taging_images');



				if ($status) {

					$this->response(array('status' => true, 'imageid' => $status, 'path' => base_url() . $path . '?nocache=' . time(), 'msg' => "Image uploaded successfully"), 200);
				} else {

					$this->response(array('status' => false, 'path' => null, 'msg' => "Error in image upload"), 200);
				}
			}
		}
	} */

	function createSubDesignImage_post()
	{

		$model = self::ADM_MODEL;
		$this->db->trans_begin();
		$img_path = self::SUB_DES_IMG_PATH;
		$is_default = 0;
		if ($_FILES) {

			$img_path =  self::SUB_DES_IMG_PATH . "sub_design/" . $_POST['id_sub_design_mapping'];
			if (!is_dir($img_path)) {
				mkdir($img_path, 0777, TRUE);
			}


			if ($_FILES['name']['name']) {
				$img = $_FILES['name']['tmp_name'];
				$id_sub_design_mapping = $_POST['id_sub_design_mapping'];
				$filename =  $id_sub_design_mapping . "_" . mt_rand(100001, 999999) . ".jpg";
				$path = self::SUB_DES_IMG_PATH . "sub_design/" . $id_sub_design_mapping . '/' . $filename;

				if (($img_info = getimagesize($img)) === FALSE) {
					// die("Image not found or not an image");
					$this->response(array('status' => false, 'filename' => null, 'msg' => "Image not found"), 200);
				}
				$width = $img_info[0];
				$height = $img_info[1];

				switch ($img_info[2]) {
					case IMAGETYPE_GIF:
						$src = imagecreatefromgif($img);
						$tmp = imagecreatetruecolor($width, $height);
						$kek = imagecolorallocate($tmp, 255, 255, 255);
						imagefill($tmp, 0, 0, $kek);
						break;
					case IMAGETYPE_JPEG:
						$src = imagecreatefromjpeg($img);
						$tmp = imagecreatetruecolor($width, $height);
						break;
					case IMAGETYPE_PNG:
						$src = imagecreatefrompng($img);
						$tmp = imagecreatetruecolor($width, $height);
						$kek = imagecolorallocate($tmp, 255, 255, 255);
						imagefill($tmp, 0, 0, $kek);
						break;
					default: //die("Unknown filetype");
						$this->response(array('status' => false, 'path' => null), 200);
				}

				imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
				imagejpeg($tmp, $path);

				if ($_POST['default'] == 1) {

					$status = $this->$model->checksub_design_mapping_images($id_sub_design_mapping);

					if (!$status) {
						$imgData = array(
							'is_default' => 0
						);

						$this->$model->updateData($imgData, "id_sub_design_mapping", $id_sub_design_mapping, "ret_sub_design_mapping_images");
					}
				}

				$imgData = array(
					'is_default'            => $_POST['default'],
					'id_sub_design_mapping' => $id_sub_design_mapping,
					'image_name'            => $filename
				);
				$ins_id = $this->$model->insertData($imgData, 'ret_sub_design_mapping_images');

				if ($this->db->trans_status() === TRUE) {
					$return_data = array('status' => true, 'imageid' => $ins_id, 'path' => base_url() . $path . '?nocache=' . time(), 'msg' => "Image uploaded successfully");
					$this->db->trans_commit();
				} else {
					$return_data = array('status' => false, 'path' => null, 'msg' => "Error in image upload");
					//echo $this->db->_error_message();
					$this->db->trans_rollback();
				}
				$this->response($return_data, 200);
			}
		}
	}

	function deletesubdesignimage_post()
	{
		$data = $this->get_values();
		//print_r($data);exit;
		$deletetagimage = $this->deleteSubDesignImage($data['id_sub_design_mapping'], $data['id_design_img']);
		if ($deletetagimage['status'] == 1) {
			$result = array('success' => TRUE, 'msg' => $deletetagimage['message']);
		} else {
			$result = array('success' => FALSE, 'msg' => $deletetagimage['message']);
		}
		$this->response($result, 200);
	}

	function deleteSubDesignImage($id_sub_design_mapping, $id_design_img)
	{
		if ($id_sub_design_mapping != "" && $id_design_img != "") {
			$model = self::ADM_MODEL;
			//$this->response(array('status' => true   ,'msg' => $tagimages ),200);
			$img_data = $this->$model->get_sub_design_iamges_by_id($id_design_img);
			if ($this->$model->deleteData('id_design_img', $id_design_img, 'ret_sub_design_mapping_images')) {
				unlink(self::SUB_DES_IMG_PATH . "/sub_design/" . $id_sub_design_mapping . '/' . $img_data['image_name']);
			}
			return array('status' => 1, 'message' => 'Image delete!..');
		} else {
			return array('status' => 0, 'message' => 'Required  image id to delete!..');
		}
	}

	function updateDefaultSubDesignImage_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		$returnData = array();
		if ($data['id_design_img'] != '') {

			$this->$model->updateData(array('is_default' => 0), "id_sub_design_mapping", $data['id_sub_design_mapping'], "ret_sub_design_mapping_images");

			$status = $this->$model->updateData(array('is_default' => $data['is_default']), "id_design_img", $data['id_design_img'], "ret_sub_design_mapping_images");
			if ($status) {
				$returnData = array('status' => TRUE, 'message' => 'Default Image Uploaded Successfully..');
			}
		} else {
			$returnData = array('status' => FALSE, 'message' => 'Required  image id to delete!..');
		}
		$this->response($returnData, 200);
	}

	//Design Image Upload

	function createDesignImage_post()
	{

		$model = self::ADM_MODEL;
		$this->db->trans_begin();
		$img_path = self::DES_IMG_PATH;
		$is_default = 0;
		if ($_FILES) {

			$img_path =  self::DES_IMG_PATH . "design/" . $_POST['id_design_mapping'];
			if (!is_dir($img_path)) {
				mkdir($img_path, 0777, TRUE);
			}


			if ($_FILES['name']['name']) {
				$img = $_FILES['name']['tmp_name'];
				$id_design_mapping = $_POST['id_design_mapping'];
				$filename =  $id_design_mapping . "_" . mt_rand(100001, 999999) . ".jpg";
				$path = self::DES_IMG_PATH . "design/" . $id_design_mapping . '/' . $filename;

				if (($img_info = getimagesize($img)) === FALSE) {
					// die("Image not found or not an image");
					$this->response(array('status' => false, 'filename' => null, 'msg' => "Image not found"), 200);
				}
				$width = $img_info[0];
				$height = $img_info[1];

				switch ($img_info[2]) {
					case IMAGETYPE_GIF:
						$src = imagecreatefromgif($img);
						$tmp = imagecreatetruecolor($width, $height);
						$kek = imagecolorallocate($tmp, 255, 255, 255);
						imagefill($tmp, 0, 0, $kek);
						break;
					case IMAGETYPE_JPEG:
						$src = imagecreatefromjpeg($img);
						$tmp = imagecreatetruecolor($width, $height);
						break;
					case IMAGETYPE_PNG:
						$src = imagecreatefrompng($img);
						$tmp = imagecreatetruecolor($width, $height);
						$kek = imagecolorallocate($tmp, 255, 255, 255);
						imagefill($tmp, 0, 0, $kek);
						break;
					default: //die("Unknown filetype");
						$this->response(array('status' => false, 'path' => null), 200);
				}

				imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
				imagejpeg($tmp, $path);

				if ($_POST['default'] == 1) {

					$status = $this->$model->check_design_mapping_images($id_design_mapping);

					if (!$status) {
						$imgData = array(
							'is_default' => 0
						);

						$this->$model->updateData($imgData, "id_design_mapping", $id_design_mapping, "ret_design_mapping_images");
					}
				}
				$imgData = array(
					'is_default'            => $_POST['default'],
					'id_design_mapping' 	=> $id_design_mapping,
					'image_name'            => $filename
				);
				$ins_id = $this->$model->insertData($imgData, 'ret_design_mapping_images');

				if ($this->db->trans_status() === TRUE) {
					$return_data = array('status' => true, 'imageid' => $ins_id, 'path' => base_url() . $path . '?nocache=' . time(), 'msg' => "Image uploaded successfully");
					$this->db->trans_commit();
				} else {
					$return_data = array('status' => true, 'path' => null, 'msg' => "Error in image upload");
					//echo $this->db->_error_message();
					$this->db->trans_rollback();
				}
				$this->response($return_data, 200);
			}
		}
	}

	function deletedesignimage_post()
	{
		$data = $this->get_values();
		//print_r($data);exit;
		$deletetagimage = $this->deleteDesignImage($data['id_design_mapping'], $data['id_desmap_img']);
		if ($deletetagimage['status'] == 1) {
			$result = array('success' => TRUE, 'msg' => $deletetagimage['message']);
		} else {
			$result = array('success' => FALSE, 'msg' => $deletetagimage['message']);
		}
		$this->response($result, 200);
	}

	function deleteDesignImage($id_design_mapping, $id_desmap_img)
	{
		if ($id_design_mapping != "" && $id_desmap_img != "") {
			$model = self::ADM_MODEL;
			//$this->response(array('status' => true   ,'msg' => $tagimages ),200);
			$img_data = $this->$model->get_design_iamges_by_id($id_desmap_img);
			if ($this->$model->deleteData('id_desmap_img', $id_desmap_img, 'ret_design_mapping_images')) {
				unlink(self::DES_IMG_PATH . "/design/" . $id_design_mapping . '/' . $img_data['image_name']);
			}
			return array('status' => 1, 'message' => 'Image delete!..');
		} else {
			return array('status' => 0, 'message' => 'Required  image id to delete!..');
		}
	}

	function updateDefaultDesignImage_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		$returnData = array();
		if ($data['id_desmap_img'] != '') {

			$this->$model->updateData(array('is_default' => 0), "id_design_mapping", $data['id_design_mapping'], "ret_design_mapping_images");

			$status = $this->$model->updateData(array('is_default' => $data['is_default']), "id_desmap_img", $data['id_desmap_img'], "ret_design_mapping_images");
			if ($status) {
				$returnData = array('status' => TRUE, 'message' => 'Default Image Uploaded Successfully..');
			}
		} else {
			$returnData = array('status' => FALSE, 'message' => 'Required  image id to delete!..');
		}
		$this->response($returnData, 200);
	}


	//Design Image Upload


	function uploadImg_post()
	{
		$imgpath = 'assets/img/orders/';
		$file_name = '';
		$name = '';
		$data = array();
		$files = $_FILES['file']['name'];

		/*foreach($_FILES['file']['name'] as $file_key => $files)
    	{
        */
		if (!is_dir($imgpath)) {
			mkdir($imgpath, 0777, TRUE);
		}
		if ($files) {
			$name = $_FILES['file']['name'];
		}
		$img = $_FILES['file']['tmp_name'];
		$imgpath = 'assets/img/orders/' . $name;
		$result = $this->upload_img('orderimg', $imgpath, $img);
		//}
		$data = array('msg' => true, 'name' => $name);
		echo json_encode($data);
	}

	function upload_img($outputImage, $dst, $img)
	{

		if (($img_info = getimagesize($img)) === FALSE) {
			// die("Image not found or not an image");
			return false;
		}
		$width = $img_info[0];
		$height = $img_info[1];

		switch ($img_info[2]) {
			case IMAGETYPE_GIF:
				$src = imagecreatefromgif($img);
				$tmp = imagecreatetruecolor($width, $height);
				$kek = imagecolorallocate($tmp, 255, 255, 255);
				imagefill($tmp, 0, 0, $kek);
				break;
			case IMAGETYPE_JPEG:
				$src = imagecreatefromjpeg($img);
				$tmp = imagecreatetruecolor($width, $height);
				break;
			case IMAGETYPE_PNG:
				$src = imagecreatefrompng($img);
				$tmp = imagecreatetruecolor($width, $height);
				$kek = imagecolorallocate($tmp, 255, 255, 255);
				imagefill($tmp, 0, 0, $kek);
				break;
			default: //die("Unknown filetype");
				return false;
		}

		imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
		imagejpeg($tmp, $dst);

		return true;
	}

	function uploadVideo_post()
	{

		$returnStatus = false;

		$fileName = "";

		$returnMsg = "Error occured. Please try again later.";

		$upload_file_size = 1024 * 1024 * 20;

		$uploadPath =	'assets/video/orders/';

		$allowedExts = array("mp3", "mp4", "wma");

		$extension = pathinfo($_FILES['name']['name'], PATHINFO_EXTENSION);

		if (($_FILES["name"]["type"] == "multipart/form-data") && ($_FILES["name"]["size"] < $upload_file_size) && in_array($extension, $allowedExts)) {

			if ($_FILES["name"]["error"] > 0) {

				$returnMsg = "Error occured in uploading video. Error : " . $_FILES["name"]["error"];
			} else {
				if (!is_dir($uploadPath)) {

					mkdir($uploadPath, 0777, TRUE);
				}

				if (file_exists($uploadPath . $_FILES["name"]["name"])) {

					$returnMsg = $_FILES["name"]["name"] . " already exists. Try with different file name.";
				} else {

					if (move_uploaded_file($_FILES["name"]["tmp_name"], $uploadPath . $_FILES["name"]["name"])) {

						$fileName = $_FILES["name"]["name"];

						$returnMsg = "File upload successful!";

						$returnStatus = true;
					} else {

						$returnMsg = "File upload failed. Please try again.";
					}
				}
			}
		} else {
			if ($_FILES["name"]["type"] != "multipart/form-data" || !(in_array($extension, $allowedExts)))
				$returnMsg = "Invalid file. Please try again.";
			else if (!($_FILES["name"]["size"] < $upload_file_size))
				$returnMsg = "File size should not be greater the 20 MB . Please try again.";
		}

		$returnData	=	array('msg' => $returnStatus, 'name' => $fileName, 'returnMsg' => $returnMsg);

		$this->response($returnData, 200);
	}

	function createWishlist_post()
	{

		$model  	= self::ADM_MODEL;
		$addData 	= $this->get_values();

		$date = date("Y-m-d H:i:s");

		$type = "";

		$tag_id = NULL;

		$tag_code = NULL;

		$product_id	= NULL;

		$design_id	= NULL;

		$sub_design_id	= NULL;

		$id_supp_catalogue = isset($addData['id_supp_catalogue']) && $addData['id_supp_catalogue'] > 0 ? $addData['id_supp_catalogue'] : NULL;

		if ($id_supp_catalogue > 0) {

			$type = "supp_Wishlist";

			$product_id	= $addData['product_id'];

			$design_id	= $addData['design_id'];

			$sub_design_id	= $addData['id_sub_design'];
		} else {

			$type = "tag_Wishlist";

			$tag_id	 = $addData['tag_id'];

			$tag_code = $addData['tag_code'];

			$product_id	= $addData['product_id'];

			$design_id	= $addData['design_id'];

			$sub_design_id	= $addData['id_sub_design'];
		}

		$customer_name		= $addData['customer_name'];
		$mobile				= $addData['mobile'];
		$customer_id		= $addData['customer_id'];
		$emp_login_branch	= $addData['branch_id'];
		$id_supp_catalogue	= $addData['id_supp_catalogue'];
		$created_by			= $addData['employee_id'];
		$created_on			= $date;

		$wishlist = array(
			'customer_name' 	=> $customer_name,
			'mobile' 			=> $mobile,
			'customer_id' 		=> $customer_id,
			'tag_id' 			=> $tag_id,
			'tag_code' 			=> $tag_code,
			'emp_login_branch' 	=> $emp_login_branch,
			'id_supp_catalogue'	=> $id_supp_catalogue,
			'product_id'		=> $product_id,
			'design_id'			=> $design_id,
			'sub_design_id'		=> $sub_design_id,
			'type'				=> $type,
			'created_by' 		=> $created_by,
			'created_on' 		=> $created_on
		);

		//print_r($wishlist);exit;

		$this->db->trans_begin();

		$insOrder = $this->$model->insertData($wishlist, 'ret_wishlist_enquiry');

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$return_data = array('status' => TRUE, 'msg' => 'Wishlist Created successfully..');
		} else {

			$this->db->trans_rollback();

			//echo $this->db->_error_message();
			//echo $this->db->last_query(); exit;

			$return_data = array('status' => FALSE, 'msg' => "Error occured. Duplicate Entry.");
		}

		$this->response($return_data, 200);
	}

	function getCheckStockAvail_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		if ($data['id_branch'] == '' || $data['id_branch'] == 0) {
			$result = array("success" => FALSE, "message" => "Branch Required.");
		} else if ($data['id_product'] == '') {
			$result = array("success" => FALSE, "message" => "Please Select The Product..");
		} else if ($data['design_no'] == '') {
			$result = array("success" => FALSE, "message" => "Please Select The Design..");
		} else if ($data['id_sub_design'] == '') {
			$result = array("success" => FALSE, "message" => "Please Select The Sub Design..");
		} else if ($data['id_size'] == '' && $data['id_weight'] == '') {
			$result = array("success" => FALSE, "message" => "Please Select Weight or Size..");
		} else {
			$total_pcs = $this->$model->CheckStockAvail($data);
			$result = array("success" => TRUE, "avail_pcs" => $total_pcs);
		}
		$this->response($result, 200);
	}

	function get_enq_fields_get()
	{

		$model = self::ADM_MODEL;
		$result = $this->$model->getEnqFields();
		echo json_encode($result);
	}

	function enquiry_post()
	{

		$model  	= self::ADM_MODEL;
		$addData 	= $this->get_values();

		/*echo "<pre>";
		print_r($addData);exit;*/
		$datetime = date("Y-m-d H:i:s");

		$validate_record = true;
		$message = "";

		$customer_name		= isset($addData['name']) ? $addData['name'] : NULL;
		$mobile				= isset($addData['phone']) ? $addData['phone'] : NULL;
		$customer_id		= $addData['id_customer'];
		$area 				= isset($addData['id_village']) ? $addData['id_village'] : NULL;
		$emp_login_branch	= isset($addData['id_branch']) ? $addData['id_branch'] : NULL;
		$type 				= $addData['type'];
		$created_by			= isset($addData['id_employee']) ? $addData['id_employee'] : NULL;
		$created_on			= $datetime;
		$tag_id				= NULL;
		$tag_code			= NULL;
		$esti_id			= NULL;
		$reasons_for_leaving 	= NULL;
		$due_days 				= NULL;
		$price_range 			= NULL;
		$weight_range 			= NULL;
		$weight		 			= NULL;
		$description 			= NULL;
		$product_id				= NULL;
		$design_id				= NULL;
		$sub_design_id			= NULL;
		$enq_product			= NULL;

		$this->db->trans_begin();

		if ($type == "FactSheet") {

			$prod_details = $addData['whole'];

			$esti_id = isset($addData['estimation_id']) ? $addData['estimation_id'] : NULL;

			foreach ($prod_details as $details) {

				$img_arr = isset($details->images) ? $details->images : [];

				$images = "";

				$imgExists = false;

				foreach ($img_arr as $imgs) {

					$imgExists = $this->$model->checkEnqImgExists($imgs->name);

					if (!$imgExists) {

						$images = $images . $imgs->name . ",";
					} else {

						break;
					}
				}

				if (!$imgExists) {

					$images = rtrim($images, ',');


					$product_id	 	= $details->pro_id;

					$design_id	 	= $details->design_no;

					$sub_design_id	= $details->sub_design_no;

					$weight_range = isset($details->id_weight) && trim($details->id_weight) != "" ? $details->id_weight : NULL;

					$weight		  = isset($details->weight) && $details->weight > 0 ? $details->weight : (isset($details->weight_description) ? $details->weight_description : NULL);

					$reasons_for_leaving = isset($details->reason) ? $details->reason : NULL;

					/*$product_id			= isset($addData['pro_id']) && trim($addData['pro_id']) != "" ? $addData['pro_id'] : NULL;

					$design_id			= isset($addData['design_no']) && trim($addData['design_no']) != "" ? $addData['design_no'] : NULL;

					$sub_design_id		= isset($addData['sub_design_no']) && trim($addData['sub_design_no']) != "" ? $addData['sub_design_no'] : NULL;*/

					/*$product_id			= NULL;

					$design_id			= NULL;

					$sub_design_id		= NULL;*/

					$wishlist = array(
						'customer_name' 	=> $customer_name,
						'mobile' 			=> $mobile,
						'customer_id' 		=> $customer_id,
						'tag_id' 			=> $tag_id,
						'tag_code' 			=> $tag_code,
						'esti_id' 			=> $esti_id,
						'product_id' 		=> $product_id,
						'design_id' 		=> $design_id,
						'sub_design_id' 	=> $sub_design_id,
						'area' 				=> $area,
						'images' 			=> $images,
						'reasons_for_leaving' => $reasons_for_leaving,
						'price_range' 		  => $price_range,
						'weight_range' 		  => $weight_range,
						'weight'	 		  => $weight,
						'description' 		=> $description,
						'due_days' 			=> $due_days,
						'enq_product'		=> $enq_product,
						'type' 				=> $type,
						'emp_login_branch' 	=> $emp_login_branch,
						'created_by' 		=> $created_by,
						'created_on' 		=> $created_on
					);

					$insId = $this->$model->insertData($wishlist, 'ret_wishlist_enquiry');
				} else {

					$validate_record = false;

					$message = "Image already exists. Please try again with different name";

					break;
				}
			}
		}

		if ($type == "Wishlist" || $type == "Enquiry") {

			$img_arr = isset($addData['images']) ? $addData['images'] : [];

			$images = "";

			$imgExists = false;

			foreach ($img_arr as $imgs) {

				$imgExists = $this->$model->checkEnqImgExists($imgs->name);

				if (!$imgExists) {

					$images = $images . $imgs->name . ",";
				} else {

					break;
				}
			}

			if (!$imgExists) {

				$description = isset($addData['sample_details']) ? $addData['sample_details'] : NULL;

				if ($type == "Wishlist") {

					$tag_id				= isset($addData['tag_id']) && trim($addData['tag_id']) != "" ? $addData['tag_id'] : NULL;

					$tag_code			= isset($addData['tag_code']) ? $addData['tag_code'] : NULL;

					$due_days = isset($addData['due_date']) && $addData['due_date'] > 0 ? $addData['due_date'] : 0;

					$product_id			= isset($addData['pro_id']) && trim($addData['pro_id']) != "" ? $addData['pro_id'] : NULL;

					$design_id			= isset($addData['design_no']) && trim($addData['design_no']) != "" ? $addData['design_no'] : NULL;

					$sub_design_id		= isset($addData['sub_design_no']) && trim($addData['sub_design_no']) != "" ? $addData['sub_design_no'] : NULL;
				}

				if ($type == "Enquiry") {

					$weight_range = isset($addData['id_weight']) && trim($addData['id_weight']) != "" ? $addData['id_weight'] : NULL;

					$weight		  = isset($addData['weight']) && $addData['weight'] > 0 ? $addData['weight'] : (isset($addData['weight_description']) ? $addData['weight_description'] : NULL);

					$price_range = isset($addData['price']) ? $addData['price'] : NULL;

					$enq_product = isset($addData['customerproduct']) ? $addData['customerproduct'] : NULL;
				}

				$wishlist = array(
					'customer_name' 	=> $customer_name,
					'mobile' 			=> $mobile,
					'customer_id' 		=> $customer_id,
					'tag_id' 			=> $tag_id,
					'tag_code' 			=> $tag_code,
					'esti_id' 			=> $esti_id,
					'product_id' 		=> $product_id,
					'design_id' 		=> $design_id,
					'sub_design_id' 	=> $sub_design_id,
					'area' 				=> $area,
					'images' 			=> $images,
					'reasons_for_leaving' => $reasons_for_leaving,
					'price_range' 		  => $price_range,
					'weight_range' 		  => $weight_range,
					'weight'	 		  => $weight,
					'description' 		=> $description,
					'due_days' 			=> $due_days,
					'enq_product'		=> $enq_product,
					'type' 				=> $type,
					'emp_login_branch' 	=> $emp_login_branch,
					'created_by' 		=> $created_by,
					'created_on' 		=> $created_on
				);

				$insId = $this->$model->insertData($wishlist, 'ret_wishlist_enquiry');
			} else {

				$validate_record = false;

				$message = "Image already exists. Please try again with different name";
			}
		}

		if ($validate_record) {

			if ($this->db->trans_status() === TRUE) {

				$this->db->trans_commit();

				$msg = "";

				if ($type == "FactSheet") {

					$msg = "FactSheet Created successfully..";
				} else if ($type == "Enquiry") {

					$msg = "Enquiry Created successfully..";
				} else if ($type == "Wishlist") {

					$msg = "Wishlist Created successfully..";
				}

				$return_data = array('status' => TRUE, 'msg' => $msg);
			} else {

				$this->db->trans_rollback();

				//echo $this->db->_error_message();
				//echo $this->db->last_query();

				$return_data = array('status' => FALSE, 'msg' => "Error occured. Duplicate Entry");
			}
		} else {

			$return_data = array('status' => FALSE, 'msg' => $message);
		}

		$this->response($return_data, 200);
	}

	function createenquiryImage_post()
	{

		$imgpath = self::ENQ_IMG_PATH;

		$file_name = '';

		$name = '';

		$data = array();

		$files = $_FILES['name']['name'];

		/*foreach($_FILES['file']['name'] as $file_key => $files)
    	{
        */
		if (!is_dir($imgpath)) {

			mkdir($imgpath, 0777, TRUE);
		}
		if ($files) {

			$name = $_FILES['name']['name'];
		}

		$img = $_FILES['name']['tmp_name'];

		$imgpath = $imgpath . $name;

		if (!file_exists($imgpath)) {

			$result = $this->upload_img('orderimg', $imgpath, $img);

			$data = array('msg' => true, 'name' => $name);
		} else {

			$data = array('msg' => false, 'name' => '');
		}

		//}



		echo json_encode($data);
	}

	function getRefBySearch_post()
	{

		$model = self::ADM_MODEL;

		$esti_details = array();

		$postdata = $this->get_values();

		$id_branch = $postdata['id_branch'];

		$esti_no = $postdata['searchTxt'];

		if ($esti_no > 0 && $id_branch > 0) {

			$esti_details = $this->$model->getEstimationByNo($esti_no, $id_branch);
		}

		echo json_encode($esti_details);
	}

	function updateDefaultTagImage_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		$returnData = array();
		if ($data['imgid'] != '') {

			$this->$model->updateData(array('is_default' => 0), "tag_id", $data['tagid'], "ret_taging_images");

			$status = $this->$model->updateData(array('is_default' => $data['is_default']), "id_tag_img", $data['imgid'], "ret_taging_images");
			if ($status) {
				$returnData = array('status' => TRUE, 'message' => 'Default Image Uploaded Successfully..');
			}
		} else {
			$returnData = array('status' => FALSE, 'message' => 'Required  image id to delete!..');
		}
		$this->response($returnData, 200);
	}

	function logout_post()
	{

		/*$returnData = array();

		$model = self::ADM_MODEL;

		$data = $this->get_values();

		if($this->$model->update_device_login_status($data['username'], '', 0)) {

			$returnData = array("status" => TRUE, "message" => "Signed out successfully");

		} else {

			$returnData = array("status" => FALSE, "message" => "Error occured in signing out.Please try again later.");

		}*/

		$returnData = array("status" => TRUE, "message" => "Signed out successfully");

		$this->response($returnData, 200);
	}

	function getCustomerDet_post()
	{

		$returnData = array();

		$model = self::ADM_MODEL;

		$data = $this->get_values();

		if ($data['id_customer'] > 0) {

			$returnData = $this->ret_estimation_model->getCustomerDet($this->session->userdata('id_branch'),$data['id_customer']);

			$returnData['cus_reviews'] = $this->$model->get_customer_review($data['id_customer']);

			$returnData['last_review'] = isset($returnData['cus_reviews'][0]) ? $returnData['cus_reviews'][0] : NULL;

			$returnData['cus_review_avg'] = $this->$model->get_cus_review_avg($data['id_customer']);
		}

		echo json_encode($returnData);
	}

	function getCusReviewOptions_get()
	{

		$returnData = array();

		$model = self::ADM_MODEL;

		$customer_review_options = "";

		$setting_array = ['customer_review_options'];

		$settings = $this->$model->get_ret_settings($setting_array);

		foreach ($settings as $setting) {

			if ($setting['name'] == 'customer_review_options') {

				$customer_review_options = $setting['value'];
			}
		}

		if ($customer_review_options != "") {

			$customer_review_options = explode(',', $customer_review_options);
		} else {

			$customer_review_options = [];
		}

		echo json_encode($customer_review_options);
	}

	function updateCusReview_post()
	{

		$datetime = date("Y-m-d H:i:s");

		$returnData = array();

		$model = self::ADM_MODEL;

		$data = $this->get_values();

		$branch_id			= $data['branch_id'];
		$cus_id				= $data['cus_id'];
		$esti_id			= $data['esti_id'];
		$rating				= $data['rating'];
		$review				= $data['review'];
		$suggestion			= $data['suggestion'];
		$created_on			= $datetime;
		$created_by			= $data['employee_id'];

		$ins_array = array(
			"branch_id"		=>	$branch_id,
			"cus_id"		=>  $cus_id,
			"esti_id"		=>  $esti_id,
			"rating"		=>	$rating,
			"review"		=>	$review,
			"suggestion" 	=> 	$suggestion,
			"created_on"	=>	$created_on,
			"created_by"	=>	$created_by
		);

		if ($this->$model->insertData($ins_array, "ret_customer_review")) {

			$returnData	=	array('status'	=>	FALSE,	'message' => 'Customer review updated successfully.');
		} else {

			$returnData	=	array('status'	=>	FALSE,	'message' => 'Error occured. Please try again later.');
		}

		$this->response($returnData, 200);
	}

	function createOrder_post()
	{

		$model 		= "ret_order_model";

		$api_model  = self::ADM_MODEL;

		$addData 	= $this->get_values();

		$orderDet	= $addData['order'];
		$ordertype	= ($orderDet->order_type == 1 ? 2 : 5);

		$orderItem	= $addData['o_item'];

		/*echo "<pre>";
		print_r($orderDet);
		print_r($orderDet);
		echo "</pre>";
		exit;*/

		$order_no	= NULL;

		$pur_no		= NULL;

		$return_data = array();

		$order_from = (isset($orderDet->order_from) ? (!empty($orderDet->order_from) ? $orderDet->order_from : NULL) : NULL);

		$fin_year       = $this->$model->get_FinancialYear();

		if ($orderDet->order_for == 2) {

			$order_no = $this->$model->generateOrderNo($order_from, $ordertype);
		}

		$dCData = $this->admin_settings_model->getBranchDayClosingData($orderDet->id_branch);

		$order_datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

		$rates = $this->$api_model->get_rates();

		$rate_fields = $this->$api_model->get_metal_rate_fields();

		$tax_details 	= $this->$api_model->get_tax_details();

		$branch_details = $this->admin_settings_model->get_branch_by_id($order_from);

		$cus_details   = $this->customer_model->get_customer($orderDet->order_to);

		$order = array(
			'fin_year_code'     => $fin_year['fin_year_code'],

			'order_type'		=> isset($orderDet->order_type) ? ($orderDet->order_type == 2 ? 5 : 2) : 2,

			'order_from'		=> $order_from,

			'order_no'          => $order_no,

			'order_for'			=> (!empty($orderDet->order_for) ? $orderDet->order_for : NULL),

			'rate_calc_from'	=> 1,

			'order_date'		=> $order_datetime,

			'order_to'			=> ($orderDet->order_for == 1 ? (!empty($orderDet->order_to_br) ? 1 : 1) : $orderDet->order_to), // Branch Id OR Customer Id

			'balance_type'		=> isset($orderDet->balance_type) ? $orderDet->balance_type : 1,

			'createdon'         => date("Y-m-d H:i:s"),

			'order_taken_by'    => $orderDet->id_employee,

			'rate_type'			=> isset($orderDet->rate_type) ? $orderDet->rate_type : 1,

			'added_through'     => 2, //Estimation app
		);

		$this->db->trans_begin();

		$insOrder = $this->$model->insertData($order, 'customerorder');

		$i = 1;

		/*echo "<pre>"; print_r($order);
		echo "<pre>";print_r($orderItem);
		exit;*/
		foreach ($orderItem as $items) {

			$product_details = $this->$api_model->get_product($items->id_product);

			$smith_remainder_date 	= (!empty($d['smith_remainder_date']) ? date('Y-m-d', strtotime(str_replace("/", "-", $d['smith_remainder_date']))) : NULL);

			$row['MetalCode'] = $product_details[0]['metalcode'];

			$row['purity_id'] = $items->id_purity;

			$row['stone_price'] = $items->stone_price;

			$row['certification_cost'] = 0;

			$row['charge_value'] = $items->charge_value;

			$row['calculation_based_on'] = $items->calculation_based_on;

			$row['GrossWt'] = $items->weight;

			$row['net_wt'] = $items->net_wt;

			$row['retail_max_wastage_percent'] = $items->wast_percent;

			$row['mc_value'] = $items->mc;

			$row['mc_type'] = $items->id_mc_type;

			$row['sales_value'] = $items->sales_value;

			$row['Pieces'] = $items->pcs;

			$row['tgrp_id'] = $items->tgrp_id;

			/*echo "<pre>";
				print_r($row);
				print_r($rates);
				print_r($rate_fields);
				print_r($tax_details);
				echo "</pre>";

				$total = $this->$api_model->get_totalprice($row, $rates, $rate_fields, $tax_details);

				$order_rate = $total['rate'];

				$total_price = $total['total_price'];

				$total_tax_rate = $total['total_tax_rate'];*/

			$order_rate = $items->order_rate;

			$total_price = $items->sales_value;

			$total_tax_rate = $items->total_tax_rate;

			$cmp_state = $branch_details['id_state'];

			$cus_state = $cus_details['id_state'];

			$cgst = $sgst = $igst = 0;

			if ($cus_state == $cmp_state) {

				$cgst = round($total_tax_rate / 2, 2);

				$sgst = $cgst;
			} else {

				$igst = $total_tax_rate;
			}

			$orderDetails = array(

				'orderno'		   => ($order_no != '' && $order_no != NULL ? $order_no : $pur_no) . "-" . $i,

				'ortertype'       => 2,

				'id_product'      => $items->id_product,

				'design_no'       => $items->design_no,

				'id_sub_design'   => $items->id_sub_design,

				'id_purity'       => ($items->id_purity != '' ? $items->id_purity : NULL),

				'rate_per_gram'   => $order_rate,

				'totalitems'      => $items->pcs,

				'weight'          => $items->weight,

				'less_wt'		   => (!empty($items->less_wt) && $items->less_wt != "" ? $items->less_wt : 0),

				'net_wt'		   => (!empty($items->net_wt) ? $items->net_wt : 0),

				'mc'			   => (!empty($items->mc) ? $items->mc : 0),

				'id_mc_type'	   => (!empty($items->id_mc_type) ? $items->id_mc_type : 2),

				'wast_percent'	   => (!empty($items->wast_percent) && $items->wast_percent != "" ? $items->wast_percent : 0),

				'stn_amt'		   => (!empty($items->stone_price) ? $items->stone_price : 0),

				'charge_value'	   => (!empty($items->charge_value) ? $items->charge_value : 0),

				'rate'				=> $total_price,

				'total_sgst'		=> $sgst,

				'total_cgst'		=> $cgst,

				'total_igst'		=> $igst,

				'size'            => ($items->id_size != '' && $items->id_size != null ? $items->id_size : NULL),

				'order_date'		=> $order_datetime,

				'cus_due_date'     => ($items->due_date != '' ? $items->due_date  : NULL),

				'smith_remainder_date' => (!empty($items->smith_remainder_date) ? $smith_remainder_date : NULL),

				'smith_due_date'	=> ($items->smith_due_date != '' ? date('Y-m-d', (strtotime('+' . ($items->smith_due_date . ' day'))))  : NULL),

				'description'      => ($items->sample_details != '' ? $items->sample_details : NULL),

				'orderstatus'		=> (isset($ordertype) && $ordertype == 5 ? 4 : 0),

				'tag_id'			=> (!empty($items->tag_id) ? $items->tag_id : NULL),

				'tag_name'			=> (!empty($items->tag_code) ? $items->tag_code : NULL),

				'id_supp_catalogue' => (!empty($items->id_supp_catalogue) ? $items->id_supp_catalogue : NULL),

			);


			if ($insOrder > 0) {

				$orderDetails['id_customerorder'] = $insOrder;

				$insOrderDet = $this->$model->insertData($orderDetails, 'customerorderdetails');

				if ($insOrderDet > 0) {

					if ($ordertype == 5 && !empty($items->tag_id)) {
						$this->db->where('tag_id', $items->tag_id);
						$this->db->update('ret_taging', ['id_orderdetails' => $insOrderDet]);
					}

					$stone_details = $items->stones;

					if (sizeof($stone_details) > 0) {

						foreach ($stone_details as $stone) {
							$stone = (array)$stone;

							$stone_data = array(

								'order_id'        	=>	$insOrder,

								'order_item_id'   	=>	$insOrderDet,

								'stone_type'		=>  $stone['stone_type'],

								'pieces'        	=>	isset($stone['pieces']) ? $stone['pieces'] : $stone['stone_pcs'],

								'wt'            	=>	isset($stone['wt']) ? $stone['wt'] : $stone['stone_wt'],

								'stone_id'      	=>	$stone['stone_id'],

								'uom_id'			=>  $stone['uom_id'],

								'price'         	=>	isset($stone['amount']) ? $stone['amount'] : $stone['stone_price'],

								'is_apply_in_lwt'	=>	$stone['is_apply_in_lwt'],

								'stone_cal_type' 	=>	isset($stone['stone_cal_type']) && $stone['stone_cal_type'] > 0 ? $stone['stone_cal_type'] : 1,

								'rate_per_gram'  	=>	isset($stone['rate']) ? $stone['rate'] : (isset($stone['rate_per_gram']) && $stone['rate_per_gram'] > 0 ? $stone['rate_per_gram'] : 0)

							);

							$stoneInsert = $this->$model->insertData($stone_data, 'ret_order_item_stones');
						}
					}

					$charges_details = $items->charges;

					if (sizeof($charges_details) > 0) {

						foreach ($charges_details as $charge) {

							$charge = (array)$charge;

							if ($charge['charge_value'] > 0) {

								$charge_data = array(

									'order_id'  	=>	$insOrder,

									'order_item_id'	=>	$insOrderDet,

									'id_charge'    	=>	$charge['id_charge'],

									'amount'       	=>	$charge['charge_value'],

								);

								$this->$model->insertData($charge_data, 'ret_order_other_charges');
							}
						}
					}

					$other_metal_details = $items->other_metal_details;

					if ($other_metal_details) {

						foreach ($other_metal_details as $oth_m) {

							$oth_m = (array)$oth_m;

							$est_other_metals = array(

								'order_detail_id'             => $insOrderDet,

								'tag_other_itm_metal_id'    => $oth_m['tag_other_itm_metal_id'],

								'tag_other_itm_pur_id'      => $oth_m['tag_other_itm_pur_id'],

								'tag_other_itm_grs_weight'  => $oth_m['tag_other_itm_grs_weight'],

								'tag_other_itm_wastage'     => $oth_m['tag_other_itm_wastage'],

								'tag_other_itm_uom'         => $oth_m['tag_other_itm_uom'],

								'tag_other_itm_cal_type'    => $oth_m['tag_other_itm_cal_type'],

								'tag_other_itm_mc'          => $oth_m['tag_other_itm_mc'],

								'tag_other_itm_rate'        => $oth_m['tag_other_itm_rate'],

								'tag_other_itm_pcs'         => $oth_m['tag_other_itm_pcs'],

								'tag_other_itm_amount'      => $oth_m['tag_other_itm_amount'],

							);

							$this->$model->insertData($est_other_metals, 'ret_order_other_metals');
						}
					}

					//$sample_images= array(explode('##',$items->sample_images));

					foreach ($items->sample_images as $image) {
						if ($image != "") {
							$filename = basename($image);
							$clean_image = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
							$insImageId = $this->$model->insertData(array('id_orderdetails' => $insOrderDet, 'image' => $clean_image), 'customer_order_image');
						}
					}

					foreach ($items->sample_videos as $video) {
						if ($video != "") {
							$clean_video = basename($video);
							$insImageId = $this->$model->insertData(array('id_orderdetails' => $insOrderDet, 'video' => $clean_video), 'customer_order_video');
						}
					}
				}
			}
			$i++;
		}

		if ($this->db->trans_status() === TRUE) {
			if ($insOrder) {

				$return_data = array('status' => TRUE, 'msg' => 'Order Created successfully..', 'id_customerorder' => $insOrder);
			}
			$this->db->trans_commit();
		} else {
			echo $this->db->_error_message();
			echo $this->db->last_query();
			exit;

			$this->db->trans_rollback();

			$return_data = array('status' => FALSE, 'msg' => 'Unable to proceed the requested process');
		}
		$this->response($return_data, 200);
	}


	function check_is_factsheet_item_post()
	{

		$tag_data = array();

		$this->load->model('ret_tag_model');

		$this->load->model('ret_wishlist_model');

		$model = "ret_tag_model";

		$tag_id = $_POST['tag_id'];

		$items_data = $this->$model->get_tag_details_by_tag_id($tag_id);

		if (count($items_data) > 0) {

			$tag_product 	= $items_data['product_id'];

			$tag_design 	= $items_data['design_id'];

			$tag_subdesign 	= $items_data['id_sub_design'];

			$tag_grosswt 	= $items_data['gross_wt'];

			$factsheet_details = $this->ret_wishlist_model->get_factsheet_for_sms($tag_product, $tag_design, $tag_subdesign, $tag_grosswt, "FactSheet");

			foreach ($factsheet_details as $details) {

				$mobile = $details['mobile'];
			}
		}
	}

	function getorderBySearch_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values();
		$tagData = $this->$model->getorderBySearch($data['searchTxt'], $data['id_branch'], $data['fin_year']);
		$result['tagData'] = $tagData;
		$result['advance_amount'] = $this->$model->advance_details_order_no($data['searchTxt'], $data['id_branch'], $data['fin_year']);
		if (sizeof($tagData) > 0) {
			if ($tagData['tag_status'] == 0) {
				$result['msg'] =  "";
				$result['status'] =  true;
				$result['rate_type'] = $tagData[0]['rate_type'];
			} else {
				$tagCode = $tagData['tag_code'];
				$result['msg'] =  ($tagData['tag_status'] == 1 ? $tagCode . " Sold Out" : ($tagData['tag_status'] == 2 ? $tagCode . " Deleted" : ($tagData['tag_status'] == 3 ? $tagCode . " marked as Other Issue" : ($tagData['tag_status'] == 4 ? $tagCode . " is in-transit" : ($tagData['tag_status'] == 5 ? $tagCode . " Removed from stock" : "")))));
				$result['status'] =  false;
			}
		} else {
			$result['msg'] =  ($data['searchField'] == "tag_code" ? "Tag code " . $data['searchTxt'] : "Tag") . " Not found...";
			$result['status'] =  false;
		}
		$this->response($result, 200);
	}

	function getFinancialYear_get()
	{
		$model = self::ADM_MODEL;

		$data = $this->$model->getFinancialYear();

		echo json_encode($data);
	}

	function get_wishlist_get()
	{

		$model = "ret_wishlist_model";

		$type = isset($_GET['type']) ? $_GET['type'] : 0;

		$status = isset($_GET['status']) ? $_GET['status'] : 0;

		$date_from 	= date('Y-m-d', strtotime('-10 years'));

		$date_to 	= date("Y-m-d");

		$data = $this->$model->get_wishlist_data($type, $date_from, $date_to, "", $status);

		echo json_encode($data);
	}

	function delete_wishlist_post()
	{

		$imgpath = self::ENQ_IMG_PATH;

		$wishlist_model = "ret_wishlist_model";

		$model = self::ADM_MODEL;

		$postdata = $this->get_values();

		$wishlist_id = isset($postdata['wishlist_id']) && $postdata['wishlist_id'] > 0 ? $postdata['wishlist_id'] : 0;

		if ($wishlist_id > 0) {

			$followup_data = $this->$wishlist_model->getFollowupData($wishlist_id);

			if (count($followup_data) == 0) {

				$date_from 	= date('Y-m-d', strtotime('-10 years'));

				$date_to 	= date("Y-m-d");

				$wishlist_data = $this->$wishlist_model->get_wishlist_data(5, $date_from, $date_to, $wishlist_id);

				if (count($wishlist_data) > 0) {

					$wl_data = $wishlist_data[0];

					$images = $wl_data['images'];

					$img_arr = array();

					if ($images != "") {

						$img_arr = explode(",", $images);
					}

					$status = $this->$model->deleteData('id_wishlist', $wishlist_id, 'ret_wishlist_enquiry');

					if ($status) {

						foreach ($img_arr as $img) {

							if (trim($img) != "") {

								unlink($imgpath . $img);
							}
						}

						$result = array("status" => TRUE, "msg" => "Wishlist successfully deleted.");
					} else {

						/*echo $this->db->_error_message();
						echo $this->db->last_query();*/

						$result = array("status" => FALSE, "msg" => "Error occured. Please try again later.");
					}
				}
			} else {

				$result = array("status" => FALSE, "msg" => "Cannot delete this wishlist.Having followup entries.");
			}
		} else {

			$result = array("status" => FALSE, "msg" => "Wishlist Id required.");
		}

		$this->response($result, 200);
	}

	function applyFilter_new_post()
	{
		$resultArr = array();
		$model = self::ADM_MODEL;
		try {
			$postData = $this->get_values();

			$resultArr    = $this->$model->applyFilter_new($postData, 'getData');

			$total_records    = count($resultArr);
			if (sizeof($resultArr) > 0) {
				$success = true;
				$message = "Records retrieved successfully..";
			} else {
				$success = true;
				$message = "No records found..";
			}
		} catch (Exception $e) {
			$success = false;
			$message = $e->getMessage();
		}
		$result = array("Status" => $success, "Description" => $message, 'total_records' => $total_records, 'ReturnObject' => $resultArr);
		$this->response($result, 200);
	}

	function slipdetails_post()
	{

		$data = array();

		$message = "";

		$model = "ret_billing_model";

		$success = "";

		$this->load->model($model);

		$postdata = $this->get_values();

		$id_customer = trim($postdata['id_customer']);

		$id_scheme_account = trim($postdata['slip_no']);


		if ($id_customer > 0 && $id_scheme_account > 0) {

			$scheme_details = $this->$model->get_closed_accounts("", $id_customer, $id_scheme_account);

			$returnData = array('status' => TRUE, 'result' => $scheme_details);
		} else {

			if (!($id_customer > 0)) {

				$message = "Customer Id required";
			} else if (!($id_scheme_account > 0)) {

				$message = "Scheme Account Id required";
			}

			$returnData = array('status' => FALSE, 'msg' => $message);
		}

		$result = $returnData;

		$this->response($result, 200);
	}

	function get_mc_va_limit_post()
	{

		$this->load->model('ret_tag_model');

		$model = "ret_billing_model";

		$this->load->model($model);

		$postdata = $this->get_values();

		$id_branch = $postdata['id_branch'];

		$id_product = $postdata['id_product'];

		$id_design = $postdata['id_design'];

		$id_sub_design = isset($postdata['id_sub_design']) && $postdata['id_sub_design'] != "" ? $postdata['id_sub_design'] : 0;

		$gross_wt = isset($postdata['gross_wt']) && $postdata['gross_wt'] != "" ? $postdata['gross_wt'] : 0;

		$type = isset($postdata['type']) && $postdata['type'] != "" ? $postdata['type'] : 0; // 1 -> Tag, 2 -> Non Tag, 3 -> Home bill

		$lot_no = 0;

		if ($type == 1) {

			$lot_no = $postdata['lot_no'];
		}

		if ($id_product > 0 && $id_design > 0 && $id_branch > 0 && $type > 0 && ($type == 1 ? $gross_wt > 0 && $id_sub_design > 0 && $lot_no > 0 : true)) {

			$mc_va_limit = $this->$model->get_mc_va_limit($id_product, $id_design, $id_sub_design, $gross_wt, $lot_no, $id_branch);

			$returnData = array('status' => TRUE, 'msg' => "Records retrieved successfully.", 'data' => $mc_va_limit);
		} else {

			if (!($id_product > 0)) {

				$message = "Product Id required";
			} else if (!($id_design > 0)) {

				$message = "Design Id required";
			} else if (!($type > 0)) {

				$message = "Type required";
			} else if (!($gross_wt > 0)) {

				$message = "Gross wt required";
			} else if ($type == 1 ? !($id_sub_design > 0) : false) {

				$message = "Subdesign Id required";
			} else if ($type == 1 ? !($lot_no > 0) : false) {

				$message = "Lot No required";
			}

			$returnData = array('status' => FALSE, 'msg' => $message, 'data' => []);
		}

		$result = $returnData;

		$this->response($result, 200);
	}

	function getAllMetalDetails_get()
	{
		$model = self::ADM_MODEL;
		$result = $this->$model->getAllActiveMetalDetails();
		echo json_encode($result);
	}
	function getAllMetalProductDetails_get()
	{
		$model = self::ADM_MODEL;
		$result = $this->$model->getAllActiveMetalProductDetails($this->get('id_metal'));
		echo json_encode($result);
	}
	function getAllProductDesignDetails_get()
	{
		$model = self::ADM_MODEL;
		$result = $this->$model->getAllActiveProductDesignDetails($this->get('id_product'));
		echo json_encode($result);
	}
	function getAllItemMatchingList_get()
	{
		$model = self::ADM_MODEL;
		$result = $this->$model->getAllItemMatchingListDetails($this->get('id_metal'), $this->get('id_product'), $this->get('id_design'));
		echo json_encode($result);
	}
	function updateTagScanDetails_post()
	{
		$model = self::ADM_MODEL;
		$addData = $this->get_values();
		/*echo "<pre>";
		print_r($addData);
		echo "</pre>";exit;*/
		$result = $this->$model->updateRFIDTagScan($addData);
		echo json_encode($result);
	}

	function get_uomdetails_get()
	{

		$model = "ret_estimation_model";

		$uom = $this->$model->getUOMDetails();

		echo json_encode($uom);
	}

	function getOrderedList_post()
	{

		$data = array();

		$data = $this->get_values();

		$result = $this->ret_reports_model->order_status($data);

		$this->response($result, 200);
	}

	function getOrderFilters_get()
	{

		$order_status = $this->ret_reports_model->order_status_message();

		$filters = array("Order No", "Order Date", "Customer Delivered Date", "Customer Due Date", "Karigar Due Date", "Customer Over Due Date", "Karigar Over Due Date", "Karigar Delivered Date");

		$result = array("order_status" => $order_status, "filters" => $filters);

		$this->response($result, 200);
	}

	function getSections_get($branch)
	{

		$data['id_branch'] = $branch;

		$result = $this->ret_estimation_model->get_sectionBranchwise($data);

		echo json_encode($result);
	}

	function getProductSize_get()
	{

		$data = $this->ret_order_model->get_product_size($_GET['id_product']);

		echo json_encode($data);
	}

	function otp_app_approval_get()
	{

		$data = $this->ret_billing_model->otp_app_approval();
		$this->response($data, 200);
	}

	//Mobile App Approval Starts here

	function getApprovalTypes_get()
	{
		$apprl_data = $this->ret_app_api_model->get_apprl_data();

		$data['approvaltypes']	 = [
			[
				"approval_type" => 1,
				"approval_name" => 'DISCOUNT',
				"approval_count" =>  $apprl_data['discount'],
			],
			[
				"approval_type" => 2,
				"approval_name" => 'CR BILL',
				"approval_count" =>  $apprl_data['cr'],
			],
			[
				"approval_type" => 3,
				"approval_name" => 'BT',
				"approval_count" =>  $apprl_data['Bt'],

			],
			/* [
				"approval_type" => 4,
				"approval_name" => 'RATE FIX',
				"approval_count" =>  $apprl_data['rate_fix'],

			],
			[
				"approval_type" => 5,
				"approval_name" => 'KARIGAR APPROVAL',
				"approval_count" =>  $apprl_data['karigar_wast'],

			], */
		];
		$this->response($data, 200);
	}

/*
	function getApprovalTypes_get()
	{

		$data['approvaltypes']	 = [
			[
				"approval_type" => 1,
				"approval_name" => 'DISCOUNT'
			],
			[
				"approval_type" => 2,
				"approval_name" => 'CR BILL'
			],
			[
				"approval_type" => 3,
				"approval_name" => 'BT'
			],
			[
				"approval_type" => 4,
				"approval_name" => 'RATE FIX'
			],
			[
				"approval_type" => 5,
				"approval_name" => 'KARIGAR APPROVAL'
			],
		];
		$this->response($data, 200);
	}

*/
	function getApprovallists_post()
	{

		$post = $this->get_values();

		if($post['approval_type']== 1 || $post['approval_type']== 2 || $post['approval_type']== 3 ){

			$arraydata = $this->ret_app_api_model->getApprovallists($post);
			// print_r($arraydata);exit;
			$return_data = [];

			foreach ($arraydata as $key => $data) {

				$chit_data = [ 'chit_bill_details' => $data['chit_data'],
							'chit_details'  =>  ['closing_amount' => $data['closing_amount'] ,'closing_weight' => $data['closing_weight']]];
				$crdate ='';
				if(isset($data['credit_due_date'])){
					// $crdate=date_create($data['credit_due_date']);
					// $crdate = date_format($date,"Y/m/d");
					$crdate = date('d-m-Y',strtotime($data['credit_due_date']));
				}

				$return_data[] = [
					'bill_discount'        => $data['bill_discount'],
					'apprl_bt_id'          => $data['apprl_bt_id'],
					'apprl_disp_message'   => $data['apprl_disp_message'],
					'apprl_tot_bill_amount'=> $data['apprl_tot_bill_amount'],
					'apprl_cr_paid_amt'    => $data['apprl_cr_paid_amt'],
					'apprl_status'         => $data['apprl_status'],
					'approval_id'          => $data['approval_id'],
					'id_customer'          => $data['id_customer'],
					'cus_name'             => $data['cus_name'],
					'cus_mobile'           => $data['cus_mobile'],
					'emp_mobile'           => $data['emp_mobile'],
					'apprl_requested_by'   => $data['apprl_requested_by'],
					'bt_details'           => $data['bt_details'],
					'is_disc_modified'     => $data['is_disc_modified'],
					'estids'   			   => $data['estids'],
					'bt_codes'   		   => $data['bt_details'],
					'purchase_details'     => $data['purchase_details'],
					'sales_details'   	   => $data['sales_details'],
					'chit_details'   	   => $data['chit_details'],
					'profit_amount'   	   => $data['profit_amount'],
					'profit_percentage'    => $data['profit_percentage'],
					'brn_short_name'   	   => $data['brn_short_name'],
					'branch_name'          => $data['branch_name'],
					'is_credit'            => $data['is_credit'],
					'Bt_approval_type'     => $data['Bt_approval_type'],
					'transfer_item_type'   => $data['transfer_item_type'],
					'due_amount'           => $data['apprl_tot_bill_amount'] - $data['apprl_cr_paid_amt'],
					'req_time'   		   => date('h:i a'),
					'req_date'   		   => '',
					'credit_due_date'     =>  $crdate,
					//'chit_data'   		   =>  $chit_data,
					'cus_outstanding'	   =>  $this->ret_app_api_model->getCustomerDet($this->session->userdata('id_branch'),$data['id_customer']),
					'tot_net_wt'           => $data['bt_net_wt'],
					'tot_grs_wt'           => $data['bt_gross_wt'],
					'tot_dia_wt'           => $data['bt_dia_wt'],
					'approved_discount'   => $data['approved_discount'],
					'apprl_status_name'    			=> $data['apprl_status_name'],
					'req_time'    			=> $data['req_time'],
					'approved_by'    			=> $data['approved_by'],
					'approved_on'    			=> $data['approved_on'],



				];
			}

		}elseif($post['approval_type']==4){

			$return_data = $this->ret_app_api_model->get_approval_rate_fix_list($post);

		}elseif($post['approval_type']==5){

			//$post['status']=0;

			if($post['approval_for']== 0){
				$return_data = $this->ret_app_api_model->get_karigar_approvalWastage_list($post);
			}else{
				$return_data = $this->ret_app_api_model->get_karigar_approvalStones_list($post);
			}



		}
		$this->response($return_data, 200);
	}

	function getApprovaldetails_post()
	{
		$post = $this->get_values();
		$data = $this->ret_app_api_model->getApprovaldetails($post['approval_id']);

		$this->response($data, 200);
	}

	function getApprovalStatus_post()
	{
		$post = $this->get_values();

		$apprl_type = $post['apprl_type'];

		if($apprl_type == 1 ||$apprl_type == 2 ||$apprl_type == 3 ){

			$approval_status      = $post['apprl_status'];
			$approval_id          = $post['approval_id'];
			$apprl_approved_by    = $post['apprl_approved_by'];

			$disc_amt             = $post['disc_amt'];
			$is_disc_modified     = $post['is_disc_modified'];

			$status = $this->ret_app_api_model->get_approval_status($approval_id);

			if($status['apprl_status']== 0){

				$updata = [
					'apprl_status'       => $approval_status,
					'apprl_id'           => $approval_id,
					'apprl_approved_by'  => $apprl_approved_by,
					'disc_amt'           => $disc_amt,
					'is_disc_modified'   => $is_disc_modified,
					'apprl_approved_on'  => date("Y-m-d H:i:s")

				];
				$result = $this->ret_app_api_model->updateData($updata, "apprl_id", $approval_id, "ret_admin_approval_status");

				if ($result) {

					$postupdate['approvedata'] = [
						'clientid'           => 'LMX',
						'clientcode'         => $this->config->item('cliID_code'),
						'approval_id'        => $approval_id,
						'apprl_approved_by'  => $apprl_approved_by,
						'status'             => $approval_status,
						'apprl_type'         => $apprl_type,
						'disc_amt'         	 => $disc_amt,
						'is_disc_modified'   => $is_disc_modified,


					];

					$url = $this->config->item('socketurl');
					// print_r($postupdate);exit;
					if ($url != '') {
						$field_string = http_build_query($postupdate);
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						curl_setopt($ch, CURLOPT_HEADER, false);
						curl_setopt($ch, CURLOPT_POST, 1);
						curl_setopt($ch, CURLOPT_POSTFIELDS, $field_string);
						$result = curl_exec($ch);


					}

				}

				if($approval_status == 1){ //Approved
					$return_data = ['status' => true , 'message' => 'Approved Successfully'];
				$this->response($return_data, 200);
				}else if($approval_status == 2){ //Rejected
					$return_data = ['status' => false , 'message' => 'Rejected Successfully'];
					$this->response($return_data, 200);
				}


			}else{
				$return_data = ['status' => false , 'message' => 'Approval Request Already Has Been Processed '];
				$this->response($return_data, 200);
			}

		}elseif($apprl_type == 4){

			$insert_data=$post['req_data'];

			$approved_status=$post['apprl_status'];

			$apprl_approved_by  = $post['apprl_approved_by'];

			$cancel_reason  = $post['cancel_reason'];

			$is_updated = 0;

			$this->db->trans_begin();

			//print_r($insert_data);exit;

			foreach ($insert_data as $value)

			{


				$status= $this->ret_app_api_model->get_ratefix_status($value->rate_fix_id);



					if($status==0){

						if($approved_status == 1){

							$Data=array(

							'approved_through'  => 2,

							'is_approved' 	 => 1,

							'approved_by' 	 => $apprl_approved_by,

							'approved_date'  => date("Y-m-d H:i:s"),

							);

						}else{

							$Data=array(

								'cancel_through'  => 2,

								'bill_status'       => 2,

								'cancel_reason'     => $cancel_reason ,

								'cancelled_on'      => date("Y-m-d H:i:s"),

								'cancelled_by'      => $this->session->userdata('uid')

								);

						}

						$rate_fix_id = $this->ret_catalog_model->updateData($Data,'rate_fix_id',$value->rate_fix_id,'ret_po_rate_fix');

						$is_updated = 1;

					}

			}

			if($this->db->trans_status()===TRUE)

			{

				 $this->db->trans_commit();

				 if($approved_status == 1 && $is_updated == 1 ){

					$response_data=array('status'=>TRUE,'message'=>'Approved Successfully.');

				 }elseif($approved_status == 2 && $is_updated == 1 ){

					$response_data=array('status'=>TRUE,'message'=>'Canceled Successfully.');

				 }else{
					$response_data=array('status'=>FALSE,'message'=>'Unable to proceed Your Request.');
				 }





			}

			else

			{

				$this->db->trans_rollback();

				$response_data=array('status'=>FALSE,'message'=>'Unable to proceed Your Request.');

			}

			$this->response($response_data, 200);

		}elseif($apprl_type == 5){

			$status = $post['apprl_status'];

			$approval_for = $post['approval_for'];

			$apprl_approved_by  = $post['apprl_approved_by'];

			$req_data = (array)$post['req_data'];



			$this->db->trans_begin();

			$model = "ret_catalog_model";

			if($approval_for==1 && $status==1)

			{

				foreach($req_data as $key => $val){

					$val = (array)  $val;

					$this->$model->update_karigar_stones($val['id_karigar'],$val['stone_type_id'],$val['stone_id'],$val['quality_id'],$val['from_wt'],$val['to_wt']);

				}

			}



			foreach( $req_data as $key => $val)

			{

				$val = (array)  $val;

				if($status==1) // For Status Approved

				{

					if($approval_for==0) // Karigar Wastages Update

					{
						//list of approved wastages

						$wastage_list = $this->$model->get_karigar_wastages($val['id_karigar']);


						foreach($wastage_list as $itm)
						{
							if($val['stone_type']==0)  // For Ornaments Product
							{
								// if Post category is All
								if($val['cat_id']==0)
								{
									$this->$model->update_karigar_wastages($val['id_karigar']);
								}

								//if All category in list
								if($itm['id_category'] == 0)
								{
									$this->$model->update_karigar_wastages($val['id_karigar'],$itm['id_category']);
								}


								//Same category , // Same Purity

								if(($val['cat_id'] == $itm['id_category']) && ($val['id_purity'] == $itm['id_purity']))
								{
									//All product in list
									if($itm['id_product']==0)
									{
										$this->$model->update_karigar_wastages($val['id_karigar'],$val['cat_id'],$itm['id_purity'],$itm['id_product']);
									}

									// If post product is All
									if($val['pro_id']==0)
									{
										$this->$model->update_karigar_wastages($val['id_karigar'],$val['cat_id'],$val['id_purity']);
									}
								}



								//Same category,Same purity,Same product
								if(($val['cat_id'] == $itm['id_category']) && ($val['id_purity'] == $itm['id_purity']) && ($val['pro_id'] == $itm['id_product']))
								{
									//All Design in List
									if($itm['id_design']==0)
									{
										$this->$model->update_karigar_wastages($val['id_karigar'],$val['cat_id'],$val['id_purity'],$val['pro_id'],$itm['id_design']);
									}
									//if post Design is All
									if($val['des_id']==0)
									{
										$this->$model->update_karigar_wastages($val['id_karigar'],$val['cat_id'],$val['id_purity'],$val['pro_id']);

									}
								}



								// Same category , Same purity, Same product , Same Design
								if(($val['cat_id'] == $itm['id_category']) && ($val['id_purity'] == $itm['id_purity']) && ($val['pro_id'] == $itm['id_product']) && ($val['des_id'] == $itm['id_design']))
								{
									// All sub design in list
									if($itm['id_sub_design']==0)
									{
										$this->$model->update_karigar_wastages($val['id_karigar'],$val['cat_id'],$val['id_purity'],$val['pro_id'],$val['des_id'],$itm['id_sub_design']);
									}
									// If post subdesign is All
									if($val['sub_des_id']==0)
									{
										$this->$model->update_karigar_wastages($val['id_karigar'],$val['cat_id'],$val['id_purity'],$val['pro_id'],$val['des_id']);
									}
								}


								// Same category , Same purity, Same product , Same Design , Same Sub Design

								if(($val['cat_id'] == $itm['id_category']) && ($val['id_purity'] == $itm['id_purity']) && ($val['pro_id'] == $itm['id_product']) && ($val['des_id'] == $itm['id_design']) && ($val['sub_des_id'] == $itm['id_sub_design']))
								{
									$this->$model->update_karigar_wastages($val['id_karigar'],$val['cat_id'],$val['id_purity'],$val['pro_id'],$val['des_id'],$val['sub_des_id']);

								}
							}
							else if($val['stone_type']==2)  // For Diamond Product
							{
								if(($val['cat_id'] == $itm['id_category'])  && ($val['pro_id'] == $itm['id_product']) && ($val['des_id'] == $itm['id_design']) && ($val['sub_des_id'] == $itm['id_sub_design']) && ($val['quality_id'] == $itm['quality_id']) && (($val['from_wt'] >= $itm['from_wt'] && $val['from_wt'] <= $itm['to_wt'])|| $itm['from_wt'] >= $val['from_wt'] && $itm['from_wt'] <= $val['to_wt']))
								{
									$this->$model->update_karigar_wastages($val['id_karigar'],$val['cat_id'],'',$val['pro_id'],$val['des_id'],$val['sub_des_id'],$val['quality_id'],$val['from_wt'],$val['to_wt']);

								}
							}
							else  // For Stone Product
							{
								if(($val['cat_id'] == $itm['id_category'])  && ($val['pro_id'] == $itm['id_product']) && ($val['des_id'] == $itm['id_design']) && ($val['sub_des_id'] == $itm['id_sub_design']))
								{
									$this->$model->update_karigar_wastages($val['id_karigar'],$val['cat_id'],'',$val['pro_id'],$val['des_id'],$val['sub_des_id']);

								}
							}
						}


						$statusData=array(

							'status'                   => 1,

							'active'                   => 1,

							'approved_through'         => 2,

							'approved_by'              => $apprl_approved_by,

							'approved_on'              => date("Y-m-d H:i:s")

						);

						$id_karikar_wast = $this->$model->updateData($statusData,'id_karikar_wast',$val['id_karikar_wast'],'ret_karikar_items_wastage');

						/*

						$this->$model->deleteData('id_karikar_wast',$val['id_karikar_wast'],'ret_karigar_charges');

						$charges_details = json_decode($val['charges_details'],true);

						if(sizeof($charges_details)>0)

						{

							foreach($charges_details as $charge)

							{

								$charge_data = array(

										'id_karigar'         =>$val['id_karigar'],

										'id_karikar_wast'    =>$val['id_karikar_wast'],

										'charge_id'          =>$charge['charge_id'],

										'calc_type'          =>$charge['calc_type'],

										'charge_value'       =>$charge['charge_value'],

										'active'             =>0,

										'updated_on'	     =>date("Y-m-d H:i:s"),

										'updated_by'         =>$this->session->userdata('uid')

									);

									$this->$model->insertData($charge_data,'ret_karigar_charges');

							}

						}

						*/

					}

					else if($approval_for==1) // Karigar stones Update

					{

						$this->$model->update_karigar_stones($val['id_karigar'],$val['stone_type_id'],$val['stone_id'],$val['quality_id'],$val['from_wt'],$val['to_wt']);



						$statusData=array('status' => 1,'active' => 1,'approved_by' =>  $apprl_approved_by,'approved_on'  => date("Y-m-d H:i:s"));



						$this->$model->updateData($statusData,'id_karigar_stone',$val['id_karigar_stone'],'ret_karigar_stones');



					}







				}



				else if($status==3)  // For Status Hold

				{

					$statusData =array(

						'status'      => 3,



						'active'      => 1,


						'holded_through' => 2,



						'holded_by'   => $apprl_approved_by,



						'holded_on'   => date("Y-m-d H:i:s"),

					);



					if($approval_for==1) // Karigar Stone Update

					{

						$this->$model->updateData($statusData,'id_karigar_stone',$val['id_karigar_stone'],'ret_karigar_stones');

					}

					else

					{

						$this->$model->updateData($statusData,'id_karikar_wast',$val['id_karikar_wast'],'ret_karikar_items_wastage');

					}

				}



				else  // Status Rejected

				{

					//print_r($post);exit;

					$statusData=array(



						'status' => 2,



						'active' => 0,


						'rejected_through' => 2,


						'rejected_by' => $apprl_approved_by,



						'rejected_on'  => date("Y-m-d H:i:s"),



					);



					if($approval_for==1) // Karigar stones Update

					{

						$this->$model->updateData($statusData,'id_karigar_stone',$val['id_karigar_stone'],'ret_karigar_stones');

					}else{

						$this->$model->updateData($statusData,'id_karikar_wast',$val['id_karikar_wast'],'ret_karikar_items_wastage');

					}








				}

				if($this->db->trans_status()===TRUE)

				{

					 $this->db->trans_commit();



					 $response_data=array('status'=>TRUE,'message'=>'Updated Successfully.');

				}

				else

				{

					$this->db->trans_rollback();

					$response_data=array('status'=>FALSE,'message'=>'Approval Failed Please try again later');

				}


				$this->response($response_data, 200);



			}

		}

	}



	//Mobile Approval For Branch Transfer
	function bt_approval_otp_req_post()
	{

		if ($_POST != '') {

			$this->db->trans_begin();
			$approval_type = $_POST['OTP_aprvl_type'];
			$uid = $_POST['apprl_requested_by'];
			$trans_data = $_POST['BT_trans_id'];
			$transIds = implode(',', $trans_data);
			$trans_code = $_POST['BT_trans_code'];
			$transfer_item_type = $_POST['transfer_item_type'];
			$BT_approval_type = $_POST['BT_approval_type'];
			$gross_wt = $_POST['gross_wt'];
			$net_wt = $_POST['net_wt'];


			$transcodes = implode(',', $trans_code);


			$data = [
				'apprl_type'         => $approval_type,
				'apprl_requested_by' => $uid,
				'apprl_bt_id'        => $transIds,
				'apprl_bt_code'      => $transcodes,
				'transfer_item_type' => $transfer_item_type,
				'BT_approval_type'   => $BT_approval_type,
				'bt_gross_wt'  		 => $gross_wt,
				'bt_net_wt'  		 => $net_wt,
				'apprl_requested_date'=>  date("Y-m-d H:i:s"),
				'updated_on'			=> date("Y-m-d H:i:s"),


			];

			// print_r($data);exit;
			$this->db->trans_begin();

			$ins_id =  $this->ret_app_api_model->insertData($data, 'ret_admin_approval_status');

			if ($this->db->trans_status() === TRUE) {
				$this->db->trans_commit();


				$arraycontent = array(
					'apprl_type'  =>   3,
					'header'      =>  'Branch Transfer',
					'message'     =>  'BT Request',
				);
				$this->create_pushnotification($arraycontent);

				$return_data = array('status' => TRUE, 'message' => 'Approval Request Sent Successfully' , 'approval_id' => $ins_id ,'approval_type' => $approval_type );
				echo json_encode($return_data);
			} else {
				$this->db->trans_rollback();

				$return_data = array('status' => FALSE, 'message' => 'Unable to Send your Request', 'approval_id' => $ins_id ,'approval_type' => $approval_type);
				echo json_encode($return_data);
			}
		}
	}
	// End Of Mobile Approval For Branch Transfer
	//Mobile Approval For Bill Discount
	function bill_disc_app_approval_post()
	{
		if ($_POST != '') {
			$this->db->trans_begin();
			$item_details      = $_POST['item_details'];
			$bill_cus_id       = $_POST['bill_cus_id'];
			$bill_cus_name     = $_POST['bill_cus_name'];
			$id_branch         = $_POST['id_branch'];
			$id_emp            = $_POST['id_emp'];
			$bill_discount     = $_POST['disc_amt'];
			$approval_type     = $_POST['OTP_aprvl_type'];
			$ApprovalMessage   = $_POST['ApprovalMessage'];
			$total_bill_amt    = $_POST['total_bill_amt'];
			$est_ids           = $_POST['id_estimations'];
			$id_estimations    = implode(',', $est_ids);

			$est_details = [];

			foreach ($est_ids as $key => $value) {
				$est_details['est_url'][] = $this->EstiPrintData($value);
			}

			$data = [
				'id_branch'             => $id_branch,
				'apprl_bill_discount'   => $bill_discount,
				'apprl_type'            => $approval_type,
				'apprl_requested_by'    => $id_emp,
				'apprl_disp_message'    => $ApprovalMessage,
				'apprl_tot_bill_amount' => $total_bill_amt,
				'apprl_cus_id'          => $bill_cus_id,
				'apprl_esti_id'         => $id_estimations,
				'items'                 => $item_details,
				'apprl_requested_date'=>  date("Y-m-d H:i:s"),
				'updated_on'			=> date("Y-m-d H:i:s"),
			];
			$this->db->trans_begin();
			$ins_id =  $this->ret_app_api_model->insertData($data, 'ret_admin_approval_status');
			if ($this->db->trans_status() === TRUE) {
				$this->db->trans_commit();
				$arraycontent = [
					'apprl_type'  =>   1,
					'header'      =>  'Bill Discount',
					'message'     =>  'Bill Discount Request',
				];
				$this->create_pushnotification($arraycontent);
				$return_data = array('status' => TRUE, 'message' => 'Approval Request Sent Successfully' , 'approval_id' => $ins_id ,'approval_type' => $approval_type);
				echo json_encode($return_data);
			} else {
				$this->db->trans_rollback();

				$return_data = array('status' => FALSE, 'message' => 'Unable to Send your Request' , 'approval_id' => $ins_id ,'approval_type' => $approval_type);
				echo json_encode($return_data);
			}
		}
	}
	//End of Mobile Approval For Bill Discount


	//Mobile Approval For Bill Credit Bill
	function credit_bill_app_approval_post()
	{
		if ($_POST != '') {
			$this->db->trans_begin();
			$item_details       = $_POST['item_details'];
			$bill_cus_id        = $_POST['bill_cus_id'];
			$bill_cus_name      = $_POST['bill_cus_name'];
			$id_branch          = $_POST['id_branch'];
			$id_emp             = $_POST['id_emp'];
			$bill_discount      = $_POST['disc_amt'];
			$approval_type      = $_POST['OTP_aprvl_type'];
			$ApprovalMessage    = $_POST['ApprovalMessage'];
			$total_bill_amt     = $_POST['total_bill_amt'];
			$est_ids            = $_POST['id_estimations'];
			$cr_paid_amt        = $_POST['cr_paid_amt'];
			$iscredit           = $_POST['iscredit'];
			$id_estimations     = implode(',', $est_ids);

			$est_details = [];
			foreach ($est_ids as $key => $value) {
				$est_details['est_url'][] = $this->EstiPrintData($value);
			}

			$data = [
				'apprl_bill_discount'    => $bill_discount,
				'id_branch'              => $id_branch,
				'apprl_type'             => $approval_type,
				'apprl_requested_by'     => $id_emp,
				'apprl_disp_message'     => $ApprovalMessage,
				'apprl_tot_bill_amount'  => $total_bill_amt,
				'apprl_cus_id'           => $bill_cus_id,
				'apprl_esti_id'          => $id_estimations,
				'items'                  => $item_details,
				'apprl_cr_paid_amt'      => $cr_paid_amt,
				'is_credit'      		 => $iscredit,
				'apprl_requested_date'=>  date("Y-m-d H:i:s"),
				'updated_on'			=> date("Y-m-d H:i:s"),
			];
			$this->db->trans_begin();
			$ins_id =  $this->ret_app_api_model->insertData($data, 'ret_admin_approval_status');
			if ($this->db->trans_status() === TRUE) {
				$this->db->trans_commit();
				$arraycontent = array(
					'apprl_type'  =>   2,
					'header'      =>  'Credit Bill',
					'message'     =>  'Credit Bill Request',
				);
				$this->create_pushnotification($arraycontent);
				$return_data = array('status' => TRUE, 'message' => 'Approval Request Sent Successfully', 'approval_id' => $ins_id ,'approval_type' => $approval_type );
				echo json_encode($return_data);
			} else {
				$this->db->trans_rollback();

				$return_data = array('status' => FALSE, 'message' => 'Unable to Send your Request' , 'approval_id' => $ins_id ,'approval_type' => $approval_type);
				echo json_encode($return_data);
			}
		}
	}
	//End of Mobile Approval For Credit Bill



	function create_pushnotification($alertdetails = array())
	{

		$content = [
			"en" => $alertdetails['message']
		];
		$hashes_array = [];
		$fields = [
			'app_id' => $this->config->item('admin_app_id'),
			'included_segments' => ['All'],
			'data' => [
				"nav" => "1",
				"apprl_type" => $alertdetails['apprl_type'],
			],
			'contents' => $content,
			'headings' => ["en" => $alertdetails['header']],
			'web_buttons' => $hashes_array
		];
		$fields = json_encode($fields);


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8',
			'Authorization: Basic ' . $this->config->item('admin_onesingalapi')
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		// var_dump($response);exit;
		curl_close($ch);
	}

	function get_est_print_details_post(){

		$post = $this->get_values();
			$est_url = array();
			$est_url[] = $this->EstiPrintData($post);
			$return_data = ['est_url' => $est_url];

		return $return_data;

	}


	function EstiPrintData($postData = '')
	{

		$model = self::ADM_MODEL;
		if (!empty($postData['esti_id'])) {
			$est_id = $postData['esti_id'];
			if ($est_id > 0) {
				$esti_name = 'E_' . mt_rand(000, 999) . '_' . $est_id;
				$est_date = date("d-m-Y");
				$folder = "";
				if ($postData['id_branch'] > 0) {
					$folder = self::EST_PATH . '' . $postData['id_branch'] . '/' . $est_date . '/';
				} else {
					$folder = self::EST_PATH . '/';
				}
				$actual_file_name = "";
				$files = scandir($folder); // Get a list of files in the folder
				foreach ($files as $fileName) {
					if (strpos($fileName, $esti_name) !== false) {
						$actual_file_name = $fileName;
						break;
					}
				}
				$file = $folder . $actual_file_name;
				if (file_exists($file)) {
					unlink($file);
				}
				$uploadPdf = $this->generate_invoice($est_id);
				$result = array("status" => TRUE, "msg" => "Estimation retrieved successfully", "url" => $uploadPdf['url'], "file_name" => $uploadPdf['file_name']);
			}
		} else {
			$result = array("status" => FALSE, "msg" => "Estimation No. is required");
		}
		return $result;
	}


	public function bt_invoice($trans_code,$s_type)
	{

		$model = "ret_brntransfer_model";
		$data['type'] = 1; //1- Summary Print ,2-Detailed Print
		$data['btrans'] = $this->$model->getBTransData($trans_code, $s_type, 1);
		$data['btrans_summary'] = ($s_type == 1 ? $this->$model->getBTransDataSummary($trans_code, $s_type, 1) : []);
		$data['purchase_item_details'] = $this->$model->get_purchase_items_details($trans_code, $s_type, 1);
		$data['comp_details'] = $this->admin_settings_model->get_company();

		/*QR code Generation*/


		$html = $this->load->view('branch_transfer/print', $data, true);

		$this->load->helper(array('dompdf', 'file'));
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('A4', "portriat");
		$dompdf->render();
		$output = $dompdf->output();

			$folder =  self::BT_PATH . '' . $trans_code;
		if (!is_dir($folder)) {
			mkdir($folder, 0777, TRUE);
		}
		$file_name = "B_" . $trans_code . "_" . time() . ".pdf";
		$file = $folder . '/' . $file_name;
		// Delete the file if it already exists
		if (file_exists($file)) {
			unlink($file);
		}
		file_put_contents($file, $output);
		return array("url" => base_url() . $file, "file_name" => $file_name);
	}

	function get_village_by_pincode_post()
	{
		$input = json_decode(file_get_contents('php://input'), true);			// To Get the input as an associative array
		$pincode = isset($input['pin_code']) ? $input['pin_code'] : null;

		if (empty($pincode)) {
			echo json_encode([
				'status' => false,
				'message' => 'Pincode is not provided'
			]);
			return;
		}

		$model = "ret_app_api_model";
		$data = $this->$model->get_village_by_pincode($pincode);
		echo json_encode($data);
	}
	function check_pan()
	{
		$input = json_decode(file_get_contents('php://input'), true);
		$pan_number = isset($input['pan']) ? $input['pan'] : null;
		$id_customer = isset($input['id_customer']) ? $input['id_customer'] : null;
		if (!$pan_number) {
			return array(
				'status' => true,
				'msg' => 'PAN number is not available'
			);
		}
		else {
			$model = "ret_app_api_model";
			$result = $this->$model->pan_available($pan_number, $id_customer);
			if($result) {
				return array(
					'status' => true,
					'msg' => 'Valid PAN number'
				);
			}
			else {
			return array(
				'status' => false,
				'msg' => 'PAN number already exists'
			);
			}
		}

	}
	function check_aadhar()
	{
		$input = json_decode(file_get_contents('php://input'), true);
		$aadhar_number = isset($input['aadharid']) ? $input['aadharid'] : null;
		$id_customer = isset($input['id_customer']) ? $input['id_customer'] : null;
		if (!$aadhar_number) {
			return array(
				'status' => true,
				'msg' => 'AADHAR number is not available'
			);
		}
		else {
			$model = "ret_app_api_model";
			$result = $this->$model->aadhar_available($aadhar_number, $id_customer);
			if($result) {
				return array(
					'status' => true,
					'msg' => 'Valid AADHAR number'
				);
			}
			else {
			return array(
				'status' => false,
				'msg' => 'AADHAR number already exists'
			);
			}
		}

	}
	function check_gst()
	{
		$input = json_decode(file_get_contents('php://input'), true);
		$gst_number = isset($input['gst_number']) ? $input['gst_number'] : null;
		$id_customer = isset($input['id_customer']) ? $input['id_customer'] : null;
		if (!$gst_number) {
			return array(
				'status' => true,
				'msg' => 'GST number is not available'
			);
		}
		else {
			$model = "ret_app_api_model";
			$result = $this->$model->gst_available($gst_number, $id_customer);
			if($result) {
				return array(
					'status' => true,
					'msg' => 'Valid GST number'
				);
			}
			else {
			return array(
				'status' => false,
				'msg' => 'GST number already exists'
			);
			}
		}

	}

	function get_est_url_post(){
		$post = $this->get_values();

		$data = json_decode(json_encode($post), true);
		$est_urls = [];

		if($data!=''){
			$ids = is_array($data['id']) ? $data['id'] : [$data['id']];
			$id_branch = isset($data['id_branch']) ? $data['id_branch'] : 0;
			foreach ($ids as $key => $id) {
				$est_urls['est_id'][] = $this->EstiPrintData(['esti_id' => $id, 'id_branch' => $id_branch]);
			}
		}

		$this->response($est_urls, 200);

	}


	function get_approval_status_post(){
		$approval_id =  $_POST['approval_id'];
		$result = $this->ret_app_api_model->get_approval_status($approval_id);
		echo json_encode($result);

	}


	function get_bran_trans_url_post(){
		$post = $this->get_values();

		$data          = json_decode(json_encode($post), true);
		$trans_codes   = $data['bt_codes']['trans_code'];
		$s_type        = $data['bt_codes']['s_type'];


		$trans_code = explode(',',$trans_codes);
		$res_data = [];
		foreach ($trans_code as $key => $value) {
			$res_data['bt_receipt'][] =$this->bt_invoice($value,$s_type);
		}
		$this->response($res_data, 200);

	}

	function update_aprvl_status_post(){
		$approval_id = $_POST['active_aprvl_id'];
		$approval_status = $_POST['approval_status'];

		$updatedata = [
			'apprl_status'	=> $approval_status,
			'updated_on'	=> date('Y-m-d H:i:s'),
		];
		$this->db->trans_begin();
		$result = $this->ret_app_api_model->updateData($updatedata, "apprl_id", $approval_id, "ret_admin_approval_status");
		if ($this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
			if($approval_status == 3){ //cancelled
				$return_data = array('status' => TRUE, 'msg' => 'Approval Request Cancelled Successfully' , 'approval_status' => $approval_status);
			}else if($approval_status == 4){ //Expired
				$return_data = array('status' => TRUE, 'msg' => 'Approval Request Expired' , 'approval_status' => $approval_status);
			}
			echo json_encode($return_data);
		} else {
			$this->db->trans_rollback();
			if($approval_status == 3){
				$return_data = array('status' => FALSE, 'msg' => 'Unable to Cancel your Request' , 'approval_status' => $approval_status);
			}else if($approval_status == 4){
				$return_data = array('status' => FALSE, 'msg' => 'Unable to proceed your request' , 'approval_status' => $approval_status);
			}
			echo json_encode($return_data);
		}



	}


	function get_active_karigar_post(){
		$post = $this->get_values();
		if($post['report_type'] == 1){
			$data = $this->ret_app_api_model->get_WastApprvKarigars($post['approval_for']);
		}else{
			$data = $this->ret_catalog_model->get_WastApprvKarigars($post['approval_for']);
		}

		$this->response($data, 200);
	}

	function get_Financial_Year_get()

	{

		$model=	self::ADM_MODEL;

		$data=$this->$model->get_Financial_Year();

		$this->response(array('status'=> true,'responsedata' => $data), 200);

	}


	function add_cover_up_post(){

		$_POST = $this->get_values();

		$this->load->library('form_validation');
        $this->form_validation->set_rules('metal','Metal','required|trim');
        $this->form_validation->set_rules('weight','Weight','required|trim');

		if($this->form_validation->run()){

			$data = ['id_metal'=>$_POST['metal'],'weight'=>$_POST['weight'],'created_on'=> date("Y-m-d H:i:s"),'created_by'=>$_POST['created_by']];

			$status=$this->ret_catalog_model->insertData($data,'ret_cover_up');

			$this->response(['status'=> TRUE,'message'=>" Added Successfully"]);
		}else{

			$this->response(['status'=> FALSE,'message'=>($this->form_validation->error_string())]);
		}




	}

	function get_metal_get()

	{


		$this->response(array('status'=> true,'responsedata' => [['name'=>'Gold','id_metal'=>1],['name'=>'Silver','id_metal'=>2],['name'=>'Diamond','id_metal'=>3]]), 200);

	}

	//Mobile App Approval Ends here

	function convertDateFormat(?string $inputDate, string $currentFormat, string $desiredFormat): ?string {
		// return null if input is null, empty, or only whitespace
		if (is_null($inputDate) || trim($inputDate) === '') {
			return null;
		}

		$date = DateTime::createFromFormat($currentFormat, $inputDate);
		$errors = DateTime::getLastErrors();

		if ($date && $errors['warning_count'] === 0 && $errors['error_count'] === 0) {
			return $date->format($desiredFormat);
		}
	
		return null;
	}

}
