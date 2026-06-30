<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
//header('Content-Type: image/png');
require(APPPATH . 'libraries/REST_Controller.php');
require_once(APPPATH . 'libraries/payu.php');
function payment_success()
{
    $userObj = new Paymt();
    $result = $userObj->successURL();
}
function payment_failure()
{
    $userObj = new Paymt();
    $result = $userObj->failureURL();
}
class Mobile_api extends REST_Controller
{
    const MOD_MOB = "mobileapi_model";
    const SCH_MOD = "scheme_modal";
    const API_MODEL = 'syncapi_model';
    const PAYU_KEY = 'gWrBuQ';
    const PAYU_SALT = 'qHs42ie1';
    const CUS_IMG_PATH = 'admin/assets/img/customer/';
    const CUS_ADHAR_PATH = 'assets/aadhar_file/';
    const KYC_PAN_PATH = 'admin/assets/kyc/pan/';
    const KYC_AADHAR_PATH = 'admin/assets/kyc/aadhar/';
    //const PAY_URL     = base_url('index.php/mobile_api/');c 
    function __construct()
    {
        parent::__construct();
        $this->response->format = 'json';
        $this->load->model(self::MOD_MOB);
        $this->load->helper('metal_wgt_digit');
        $this->load->model('email_model');
        $this->load->model('services_modal');
        $this->load->model('login_model');
        $this->load->model('registration_model');
        $this->load->model('scheme_modal');
        $this->load->model('payment_modal');
        $this->load->model('sms_model');
        $this->load->model('digigold_modal');      // AB - digi
        ini_set('date.timezone', 'Asia/Calcutta');
        //NOTE:set maintenance mode in index page to put maintenance for both mobile,web app
        //$this->maintenance_mode = '1';	//0-YES , 1-NO
        //$this->maintenance_text= "Text here";
        $this->current_android_version = "1.0.0";
        $this->new_android_version = "1.0.1";
        $this->current_ios_version = "1.0.0";
        $this->new_ios_version = "1.0.1";
        /*	$this->current_android_version= "1.0.0";
            $this->current_ios_version = "0.0.1";
            $this->new_android_version = "1.0.1";*/
        $this->upgrade_text = "New version available in play store,Upgrade now!!"; // Version upgrade alert text
        $this->comp = $this->mobileapi_model->company_details();
        $this->sms_data = $this->services_modal->sms_info();
        $this->payment_status = array(
            'pending' => 7,
            'awaiting' => 2,
            'success' => 1,
            'failure' => 3,
            'cancel' => 4
        );
        $this->log_dir = 'log/' . date("Y-m-d");
        if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0777, TRUE);
        }
    }
    public function payment_gateway($id_branch, $id_pg)
    {
        /*param_1 => key
        param_2 => salt
        param_3 => Access code for ccavenue/ Merchant code for Techpr…
        param_4 => merchant_id for ccavenue / iv for techprocess*/
        //$data = $this->payment_modal->getBranchGateways($id_branch);
        $data = $this->mobileapi_model->getBranchGatewayData($id_branch, $id_pg);
        return $data;
    }
    //encrypt
    public function __encrypt($str)
    {
        return base64_encode($str);
    }
    //decrypt
    public function __decrypt($str)
    {
        return base64_decode($str);
    }
    /**
     * General functions   
     */
    //funtion to get post values
    function get_values()
    {
        return (array) json_decode(file_get_contents('php://input'));
    }
    // Otp message
    function otp_sms($otpStr)
    {
        /*-- Coded by ARVK --*/
        $model = self::MOD_MOB;
        $company = $this->$model->company_details();
        $expiry = date("d-m-Y H:i:s", strtotime($otpStr['expiry']));
        $msg = "Your OTP " . $otpStr['otp'] . " for " . $this->comp['company_name'] . " purchase plan is valid till " . $expiry . "";
        //$msg ="Your OTP ".$otpStr['otp']." for ".$this->comp['company_name']." purchase plan is valid till ".$expiry.". For queries contact customer care ".$company['mobile'];//
        return $msg;
        /*-- / Coded by ARVK --*/
    }
    //check customer has active scheme_account without payment entry
    function checkNotPaidAcc($id_customer)
    {
        $model = self::MOD_MOB;
        return $this->$model->notPaidAccounts($id_customer);
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
    /**
     * API functions 
     */
    function getMetalrate_get()
    {
        $filename = 'api/rate.txt';
        $data = file_get_contents($filename);
        $metalrates = (array) json_decode($data);
        $this->response($metalrates);
    }
    function getChitMetals_get()
    {
        $model = self::MOD_MOB;
        $metals = $this->$model->get_chit_metals();
        $this->response($metals, 200);
    }
    function getWeights_get()
    {
        $model = self::MOD_MOB;
        $weights = array();
        $min_weight = $this->get('min_weight') ? (float)$this->get('min_weight') : 0;
        $max_weight = $this->get('max_weight') ? (float)$this->get('max_weight') : 0;
        $weights_data = $this->$model->get_weights($min_weight, $max_weight);
        $filename = 'api/rate.txt';
        $file_data = file_get_contents($filename);
        $metalrates = (array) json_decode($file_data);
        foreach ($weights_data as $weight) {
            $rate = (float) $metalrates['goldrate_22ct'] * (float) $weight['weight'];
            $weights[] = array(
                'id_weight' => $weight['id_weight'],
                'weight' => $weight['weight'],
                'rate' => number_format($rate, 2, '.', '')
            );
        }
        $result = array('rates' => $metalrates, 'weights' => $weights);
        $this->response($result, 200);
    }
    function getSchemes_get()
    {
        $model = self::MOD_MOB;
        $schemes['schemes'] = $this->$model->get_schemesAll();
        $schemes['chit_settings'] = $this->$model->get_settings();
        $this->response($schemes, 200);
    }
    public function get_groups_get()
    {
        $model = self::MOD_MOB;
        $result = $this->scheme_modal->get_groups($this->get('id_scheme'));
        $this->response($result, 200);
    }
    // to get visible and active true schemes
    function getActiveSchemes_get()
    {
        $model = self::MOD_MOB;
        $id_branch = ($this->get('id_branch') == 'null' ? '' : $this->get('id_branch'));
        $schemes = $this->$model->get_activeSchemes($id_branch);
        $this->response($schemes, 200);
    }
    /*function getScheme_get()
    {
        $model = self::MOD_MOB;
        $scheme = $this->$model->get_scheme($this->get('id_scheme'),$this->get('id_customer'));	
        $allow_unpaid =$this->scheme_modal->allowUnpaid();	//allow customer not paid single installment		
        $allow_multiple = $this->scheme_modal->allowMultipleChits(); //allow multiple chits for customer		
        $allowNewsch = $this->scheme_modal->allowNewscheme_join(); //allow New scheme join to customer 
        $unpaid = $this->scheme_modal->check_unpaid_schemes($this->get('id_customer')); 
        $unClosedAcc = $this->scheme_modal->hasUnclosedAccounts($this->get('id_customer')); 
        $result ='';
    if($allowNewsch == TRUE){
        if($allow_multiple == TRUE )
        {
            if($allow_unpaid == TRUE)
            {
                $allow_join = array('status'=> TRUE);
            }
            else
            {
                if($unpaid == TRUE)
                {
                    $allow_join = array('status'=> FALSE, 'msg' => 'You can\'t join now, as you have scheme accounts without single payment, make payments for unpaid before joining new scheme' );
                }
                else
                {
                    $allow_join = array('status'=> TRUE);
                }
            }
        }
        else
        {
            if($unClosedAcc == TRUE)
            {
                $allow_join = array('status'=> FALSE, 'msg' => 'You have unclosed chits, kindly contact customer care and close to join new scheme.' );
            }
            else
            {
                $allow_join = array('status'=> TRUE);
            }
        }
    }else
            {
                $allow_join = array('status'=> FALSE, 'msg' => 'Kindly visit our showroom for new scheme enrollment....' ); 
            }
        $weights = array();
        $weights_data = $this->$model->get_weights();
        foreach($weights_data as $weight)
        {
            $weights[]=array(
                                'weight'    => $weight['weight']	
                            );
        }
        $result = array('scheme' => $scheme,'allow_join' => $allow_join,'weights' => $weights);
        $this->response($result,200);
    }*/
    function getScheme_get()
    {
        $model = self::MOD_MOB;
        $scheme = $this->$model->get_scheme($this->get('id_scheme'), $this->get('id_customer'));
        //	$scheme['show_referral']=1;
        $scheme['gifts'] = $this->$model->getGiftData();
        $cus_single = $scheme['cusbenefitscrt_type'];
        $emp_single = $scheme['empbenefitscrt_type'];
        $cus_ref_code = $scheme['cus_ref_code'];
        $emp_ref_code = $scheme['emp_ref_code'];
        if ($cus_single == 0 && $emp_single == 0) {
            if ($cus_ref_code != '' && $emp_ref_code != '') {
                $scheme['show_referral'] = 0;
            } else if ($cus_ref_code == '' && $emp_ref_code == '') {
                $scheme['show_referral'] = 1;
            } else if ($cus_ref_code != '' || $emp_ref_code != '') {
                $scheme['show_referral'] = 1;
            } else {
                return $scheme['show_referral'];
            }
        }
        $groups = $this->scheme_modal->get_groups($this->get('id_scheme'), $this->get('id_branch'));
        $allow_unpaid = $this->scheme_modal->allowUnpaid();    //allow customer not paid single installment		
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
                        $allow_join = array('status' => FALSE, 'msg' => 'You can\'t join now, as you have purchase plan without single payment, make payments for unpaid before joining new scheme');
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
        } else {
            $allow_join = array('status' => FALSE, 'msg' => 'Kindly visit our showroom for new scheme enrollment....');
        }
        $weights = array();
        $weights_data = $this->$model->get_weights();
        foreach ($weights_data as $weight) {
            $weights[] = array(
                'weight' => $weight['weight']
            );
        }
        if ($allow_join['status'] && $scheme['active_accounts'] > 0) {
            $allow_join = array('status' => TRUE, 'show_pay_msg' => TRUE, 'msg' => 'You have already ' . $scheme['active_accounts'] . ' Live accounts are you still want to join new plan ?');
        }
        $result = array('scheme' => $scheme, 'allow_join' => $allow_join, 'weights' => $weights, 'groups' => $groups);
        $result['wallet_balance'] = $this->$model->wallet_balance($this->get('id_customer'));
        $this->response($result, 200);
    }
    function getCountry_get()
    {
        $model = self::MOD_MOB;
        $schemes = $this->$model->get_country();
        $this->response($schemes, 200);
    }
    function getState_get()
    {
        $model = self::MOD_MOB;
        $schemes = $this->$model->get_state($this->get('id_country'));
        $this->response($schemes, 200);
    }
    function getCity_get()
    {
        $model = self::MOD_MOB;
        $schemes = $this->$model->get_city($this->get('id_state'));
        $this->response($schemes, 200);
    }
    function getAllLocations_get()
    {
        $model = self::MOD_MOB;
        $result['countries'] = $this->$model->get_country();
        $result['states'] = $this->$model->get_cityAll();
        $result['cities'] = $this->$model->get_stateAll();
        $this->response($result, 200);
    }
    //to get customer schemes
    function customerSchemes_get()
    {
        $model = self::MOD_MOB;
        //Sync Existing Data					
        if ($this->config->item("integrationType") == 2) {
            $syncData = $this->sync_existing_data($this->get('mobile'), $this->get('id_customer'), '');
        }
        $schemeAcc = $this->$model->get_payment_details($this->get('id_customer'), '', '');
        $schemes['allowDelete'] = $this->scheme_modal->deleteUnpaid();
        $schemes['chits'] = $this->array_sort($schemeAcc['chits'], 'allow_pay', SORT_DESC);
        $schemes['wallet_balance'] = $this->$model->wallet_balance($this->get('id_customer'));
        $this->response($schemes, 200);
    }
    function paymentHistory_get()
    {
        // AB - hide_digi
        $is_digi = ($this->get('is_digi') != null && $this->get('is_digi') != '' ? $this->get('is_digi') : 0);
        $model = self::MOD_MOB;
        $branchWiseLogin = $this->$model->get_branchWiseLogin();
        //$schemeAcc = $this->$model->get_scheme_accounts($this->get('id_customer'));
		$payments = $this->$model->get_paymenthistory($this->get('mobile'), $branchWiseLogin, $is_digi, $this->get('id_scheme'));
        //echo"<pre>";print_r($payments);exit;
        $this->response($payments, 200);
    }
    //to get individual scheme detail
    function getSchemeDetail_get()
    {
        $model = self::MOD_MOB;
        $chit = $this->$model->chit_scheme_detail($this->get('id_scheme_account'));
        $this->response($chit, 200);
    }
    //To get customer detail
    function getCustomer_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $customer = $this->$model->get_customerProfile($data['id_customer']);
        $this->response($customer, 200);
    }
    function getCustomer_get()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $customer = $this->$model->get_customerProfile(15);
        $this->response($customer, 200);
    }
    //to update customer by mobile
    function old_updateCustomerByMobile_post()
    {
        $result = array();
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $customer = array('passwd' => $this->__encrypt($data['passwd']));
        $flag = $this->$model->update_customerByMobile($customer, $data['mobile']);
        if ($flag) {
            $result = array('status' => TRUE, 'msg' => 'Password updated successfully...');
        } else {
            $result = array('status' => FALSE, 'msg' => 'Unable to proceed the request');
        }
        $this->response($result, 200);
    }
    function updateCustomerByMobile_post()
    {
        $result = array();
        $model  = self::MOD_MOB;
        $data   = $this->get_values();
        $newPass = $this->__encrypt($data['passwd']);
        // Check existing password for this mobile
        $existing = $this->db->select('passwd')
            ->from('customer')
            ->where('mobile', $data['mobile'])
            ->get()
            ->row();
        if ($existing && $existing->passwd == $newPass) {
            // New password is same as old
            $result = array('status' => FALSE, 'msg' => 'New password should not be the same as previous password');
        } else {
            $customer = array('passwd' => $newPass);
            $flag = $this->$model->update_customerByMobile($customer, $data['mobile']);
            if ($flag) {
                $result = array('status' => TRUE, 'msg' => 'Password updated successfully...');
            } else {
                $result = array('status' => FALSE, 'msg' => 'Unable to proceed with the request');
            }
        }
        $this->response($result, 200);
    }
    //to update customer
    function updateCustomer_post()
    {
        $result = array();
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $customer = array('passwd' => $this->__encrypt($data['passwd']));
        $flag = $this->$model->update_customer($customer, $data['id_customer']);
        if ($flag) {
            $result = array('status' => TRUE, 'msg' => 'Password updated successfully...');
        } else {
            $result = array('status' => FALSE, 'msg' => 'Unable to proceed the request');
        }
        $this->response($result, 200);
    }
    function sendFeedback_mail_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $type = gettype($data['type']);
        if ($type == "string") {  //Types Records Single	
            //$data['type'] = is_array($data['type'])== true ? $data['type'][0]:$data['type'];
            $title = ($data['type'] == 1 ? 'Enquiry' : ($data['type'] == 2 ? 'Suggestion' : ($data['type'] == 3 ? 'Complaint' : ($data['type'] == 4 ? 'Others' : ($data['type'] == 5 ? 'DTH' : 'Experience Center')))));
            $insData = array(
                'mobile' => $data['mobile'],
                'name' => $data['name'],
                'title' => $title,
                'id_customer' => (isset($data['id_customer']) ? $data['id_customer'] : NULL),
                'address' => (isset($data['address']) ? $data['address'] : NULL),
                'date_of_birth' => (isset($data['date_of_birth']) ? $data['date_of_birth'] : NULL),
                'date_of_wed' => (isset($data['date_of_wed']) ? $data['date_of_wed'] : NULL),
                'profession' => (isset($data['profession']) ? $data['profession'] : NULL),
                'email' => (isset($data['email']) ? $data['email'] : NULL),
                'date_add' => date('Y-m-d H:i:s'),
                'type' => $data['type'], // Feedback type
                'pincode' => $data['pincode'],
                'enq_from' => 2, // mobile app
                'comments' => $data['message']
            );
            // print_r($data);exit;
            if ($data['type'] == 3) {
                $ticketno = $this->$model->genTicketNo();
                $insData['ticket_no'] = $ticketno;
                // print_r($ticketno);exit;
            }
            $result = $this->$model->insCusFeedback($insData);
            //print_r($this->db->last_query());exit;
        } else if ($type == "array") //Both DTH and Experience Center
        {
            foreach ($data['type'] as $type) {
                $title = ($type == 5 ? 'DTH' : 'Experience Center');
                $insData = array(
                    'mobile' => $data['mobile'],
                    'name' => $data['name'],
                    'title' => $title,
                    'id_customer' => (isset($data['id_customer']) ? $data['id_customer'] : NULL),
                    //'address'      => (isset($data['address'])?$data['address']:NULL),
                    //'date_of_birth'=> (isset($data['date_of_birth'])?$data['date_of_birth']:NULL),
                    // 'date_of_wed'  => (isset($data['date_of_wed'])?$data['date_of_wed']:NULL),
                    // 'profession'   => (isset($data['profession'])?$data['profession']:NULL),
                    'email' => (isset($data['email']) ? $data['email'] : NULL),
                    'date_add' => date('Y-m-d H:i:s'),
                    'type' => $type, // Feedback type
                    // 'pincode'      => $data['pincode'],
                    'enq_from' => 2, // mobile app
                    'comments' => $data['message']
                );
                $result = $this->$model->insCusFeedback($insData);
            }
            if (count($data['type']) > 1) {
                $records_detalis = 'DTH and Experience Center';
                $insData['title'] = $records_detalis;
            }
        }
        if ($result['status'] == true) {
            $to = $this->comp['email'];
            //  $cc = array("pavithra@vikashinfosolutions.com");	
            $cc = "";
            $subject = "Reg - " . $this->comp['company_name'] . " customer enquiry";
            $message = $this->load->view('include/emailContact', $insData, true);
            $sendEmail = $this->email_model->send_email($to, $subject, $message, $cc, "");
            $this->response(array('status' => true, 'msg' => 'Thanks for registering... Our executive will contact you soon.' . $ticketno));
        } else {
            $this->response(array('status' => false, 'msg' => 'Please try after sometime'));
        }
    }
    function sendFeedback_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $type = gettype($data['type']);
        if ($type == "string") {  //Types Records Single	
            //$data['type'] = is_array($data['type'])== true ? $data['type'][0]:$data['type'];
            $title = ($data['type'] == 1 ? 'Enquiry' : ($data['type'] == 2 ? 'Suggestion' : ($data['type'] == 3 ? 'Complaint' : ($data['type'] == 4 ? 'Others' : ($data['type'] == 5 ? 'DTH' : 'Experience Center')))));
            $insData = array(
                'mobile' => $data['mobile'],
                'name' => $data['name'],
                'title' => $title,
                'id_customer' => (isset($data['id_customer']) ? $data['id_customer'] : NULL),
                'address' => (isset($data['address']) ? $data['address'] : NULL),
                'date_of_birth' => (isset($data['date_of_birth']) ? $data['date_of_birth'] : NULL),
                'date_of_wed' => (isset($data['date_of_wed']) ? $data['date_of_wed'] : NULL),
                'profession' => (isset($data['profession']) ? $data['profession'] : NULL),
                'email' => (isset($data['email']) ? $data['email'] : NULL),
                'date_add' => date('Y-m-d H:i:s'),
                'type' => $data['type'], // Feedback type
                'pincode' => $data['pincode'],
                'enq_from' => 2, // mobile app
                'comments' => $data['message']
            );
            // print_r($data);exit;
            if ($data['type'] == 3) {
                $ticketno = $this->$model->genTicketNo();
                $insData['ticket_no'] = $ticketno;
                // print_r($ticketno);exit;
            }
            $result = $this->$model->insCusFeedback($insData);
            //print_r($this->db->last_query());exit;
        } else if ($type == "array") //Both DTH and Experience Center
        {
            foreach ($data['type'] as $type) {
                $title = ($type == 5 ? 'DTH' : 'Experience Center');
                $insData = array(
                    'mobile' => $data['mobile'],
                    'name' => $data['name'],
                    'title' => $title,
                    'id_customer' => (isset($data['id_customer']) ? $data['id_customer'] : NULL),
                    'address' => (isset($data['address']) ? $data['address'] : NULL),
                    'date_of_birth' => (isset($data['date_of_birth']) ? $data['date_of_birth'] : NULL),
                    'date_of_wed' => (isset($data['date_of_wed']) ? $data['date_of_wed'] : NULL),
                    'profession' => (isset($data['profession']) ? $data['profession'] : NULL),
                    'email' => (isset($data['email']) ? $data['email'] : NULL),
                    'date_add' => date('Y-m-d H:i:s'),
                    'type' => $type, // Feedback type
                    'pincode' => $data['pincode'],
                    'enq_from' => 2, // mobile app
                    'comments' => $data['message']
                );
                $result = $this->$model->insCusFeedback($insData);
            }
            if (count($data['type']) > 1) {
                $records_detalis = 'DTH and Experience Center';
                $insData['title'] = $records_detalis;
            }
        }
        if ($result['status'] == true) {
            $to = $this->comp['email'];
            //  $cc = array("pavithra@vikashinfosolutions.com");	
            $cc = "";
            $subject = "Reg - " . $this->comp['company_name'] . " customer enquiry";
            $message = $this->load->view('include/emailContact', $insData, true);
            $sendEmail = $this->email_model->send_email($to, $subject, $message, $cc, "");
            $this->response(array('status' => true, 'msg' => 'Thanks for registering... Our executive will contact you soon.' . $ticketno));
        } else {
            $this->response(array('status' => false, 'msg' => 'Please try after sometime'));
        }
    }
    function sendVendorEnquiry_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $to = $this->comp['email'];
        //	$bcc ="pavithra@vikashinfosolutions.com";
        $bcc = "";
        $subject = "Reg - " . $this->comp['company_name'] . " vendor enquiry";
        $message = $this->load->view('include/emailVendorEnquiry', $data, true);
        $sendEmail = $this->email_model->send_email($to, $subject, $message, $bcc, "");
        echo $sendEmail;
    }
    //to update profile
    function updateProfile_post()
    {
        $result = array();
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $id_customer = $data['id_customer'];
        $myDate = date('Y-m-d H:i:s');
        $customer = array(
            'firstname' => ucwords($data['firstname']),
            'lastname' => ucwords($data['lastname']),
            'title' => $data['title'],
            'email' => strtolower($data['email']),
            'pan' => $data['pan'],
            'date_of_birth' => $data['date_of_birth'],
            'date_of_wed' => $data['date_of_wed'],
            'nominee_name' => isset($data['nominee_name']) ? $data['nominee_name'] : NULL,
            'nominee_relationship' => isset($data['nominee_relationship']) ? $data['nominee_relationship'] : NULL,
            'nominee_mobile' => isset($data['nominee_mobile']) ? $data['nominee_mobile'] : NULL,
            'nominee_address1' => isset($data['nominee_address1']) ? $data['nominee_address1'] : NULL,
            'nominee_address2' => isset($data['nominee_address2']) ? $data['nominee_address2'] : NULL,
            'nominee_pan' => (!empty($data['nominee_pan']) ? $data['nominee_pan'] : NULL),
        );
        //print_r($customer);exit;
        $customer['date_upd'] = $myDate;
        $address = array(
            'address1' => ucfirst($data['address1']),
            'address2' => ucfirst($data['address2']),
            'id_country' => $data['id_country'],
            'id_state' => $data['id_state'],
            'id_city' => $data['id_city'],
            //'city' 	  => $data['city'],
            'pincode' => $data['pincode']
        );
        $cflag = $this->$model->update_customer($customer, $id_customer);
        //print_r($cflag);exit;
        $isExists = $this->$model->isAddressExist($id_customer);
        if ($isExists) {
            $address['date_upd'] = $myDate;
            $aflag = $this->$model->update_customerAdd($address, $id_customer);
        } else {
            $address['id_customer'] = $id_customer;
            $address['date_add'] = $myDate;
            $aflag = $this->$model->insert_customerAdd($address);
        }
        if ($cflag) {
            $result = array('status' => TRUE, 'msg' => "Your profile updated successfully");
        } else {
            $result = array('status' => FALSE, 'msg' => 'Unable to proceed the request');
        }
        $this->response($result, 200);
    }
    function getDashboard_get()
    {
        $model = self::MOD_MOB;
        $customer = $this->$model->get_customer_dashboard($this->get('id_customer'));
        $customer['showcomingsoon'] = 0; // to show or hide scheme account and payment block		
        $this->response($customer, 200);
    }
    function getMatchingCountry_get()
    {
        $model = self::MOD_MOB;
        $countries = $this->$model->getMatchingCountry($this->get('query'));
        $this->response($countries, 200);
    }
    function getMatchingState_get()
    {
        $model = self::MOD_MOB;
        $states = $this->$model->getMatchingState($this->get('query'), $this->get('id_country'));
        $this->response($states, 200);
    }
    function getMatchingCity_get()
    {
        $model = self::MOD_MOB;
        $cities = $this->$model->getMatchingCity($this->get('query'), $this->get('id_state'));
        $this->response($cities, 200);
    }
    //to check the mobile number already registered
    function isNumberRegistered_get()
    {
        $model = self::MOD_MOB;
        $mobile = $this->get('mobile');
        $email = $this->get('email');
        $m_exist = $this->$model->isMobileExists($mobile);
        if (!empty($email) && $email != NULL) {
            $e_exist = $this->$model->clientEmail($email);
        } else {
            $e_exist = FALSE;
        }
        $limit = $this->services_modal->limitDB('get', '1');
        $count = $this->services_modal->customer_count();
        $limit_exceed = 0;
        if ($limit['limit_cust'] == 1 && $count >= $limit['cust_max_count']) {
            $limit_exceed = 1;
        }
        $is_reg = ($m_exist ? TRUE : ($e_exist ? TRUE : FALSE));
        $result = array(
            'mobile' => $mobile,
            'is_reg' => $is_reg,
            'msg' => ($is_reg ? ($m_exist && $e_exist) ? "Mobile and Email already registered" : ($m_exist ? "Mobile already registered" : "E-mail already Registered") : ($limit_exceed == 1 ? 'Temporarily New user registration is unavailable, Kindly contact Customer care...' : FALSE))
        );
        $this->response($result, 200);
    }
    //to check the mobile registered
    function checkMobileReg_get()
    {
        $model = self::MOD_MOB;
        $mobile = $this->get('mobile');
        $status = $this->$model->isMobileExists($mobile);
        $result = array(
            'mobile' => $mobile,
            'is_reg' => ($status ? $status : FALSE)
        );
        $this->response($result, 200);
    }
    function get_invoiceData($payment_no, $mobile)
    {
        $records = array();
        $query_invoice = $this->db->query("SELECT pay.id_scheme_account as id_scheme_account, sch_acc.ref_no as scheme_acc_number, DATE_FORMAT(pay.date_payment,'%d-%m-%Y') as date_payment, sch.scheme_name as scheme_name, pay.payment_amount as payment_amount,cus.firstname as firstname, cus.lastname as lastname, cus.mobile, addr.address1 as address1,email,if(payment_mode='CC','Credit Card',if(payment_mode='NB','Net Banking',if(payment_mode='CD','Cheque or DD',if(payment_mode='CO','Cash Pick Up',if(payment_mode='OP','Other',''))))) as payment_mode,trans_id,payment_ref_number,pay.receipt_no,bank_name,bank_acc_no,bank_branch,metal_weight,scheme_type
							FROM payment as pay
							LEFT JOIN scheme_account sch_acc ON sch_acc.id_scheme_account = pay.id_scheme_account
							LEFT JOIN scheme sch ON sch.id_scheme = sch_acc.id_scheme
							LEFT JOIN customer as cus ON cus.id_customer = sch_acc.id_customer
							LEFT JOIN address as addr ON addr.id_customer = cus.id_customer WHERE id_payment = '" . $payment_no . "' AND mobile='" . $mobile . "'");
        if ($query_invoice->num_rows() > 0) {
            foreach ($query_invoice->result() as $row) {
                $records[] = array('id_scheme_account' => $row->id_scheme_account, 'scheme_acc_number' => $row->scheme_acc_number, 'date_payment' => $row->date_payment, 'scheme_name' => $row->scheme_name, 'payment_amount' => $row->payment_amount, 'firstname' => $row->firstname, 'lastname' => $row->lastname, 'id_payment' => $payment_no, 'address1' => $row->address1, 'email' => $row->email, 'mobile' => $row->mobile, 'payment_mode' => $row->payment_mode, 'trans_id' => $row->trans_id, 'payment_ref_number' => $row->payment_ref_number, 'receipt_no' => $row->receipt_no, 'bank_name' => $row->bank_name, 'bank_acc_no' => $row->bank_acc_no, 'bank_branch' => $row->bank_branch, 'metal_weight' => $row->metal_weight, 'scheme_type' => $row->scheme_type);
            }
        }
        return $records;
    }
    //to validate user login
    function authenticate_post()
    {
        $model = self::MOD_MOB;
        $sch_model = self::SCH_MOD;
        $data = $this->get_values();
        $res = $this->$model->isValidLogin($data['mobile'], $this->__encrypt($data['passwd']));     //changed variable
        $result = array(
            'mobile' => $data['mobile'],
            'is_valid' => (sizeof($res) > 0 ? TRUE : FALSE)     //changed whole line
        );
        if ($result['is_valid'] === TRUE && $res['active'] == 1)           //this condition fully
        {
            $result['customer'] = $this->$model->get_customerByMobile($data['mobile']);
            /*$result['accounts']        = $this->$model->countSchemes($result['customer']['id_customer']);
            $result['payments']        = $this->$model->countPayments($result['customer']['id_customer']);
            $result['wallet']          = $this->$model->countWallets($result['customer']['id_customer']);
            $result['notification']    = $this->$model->get_cus_noti_settings($result['customer']['id_customer']);*/
            if ($data['token'] != 'null') {
                //insert device token , uuid , device type
                $device_data = array(
                    'token' => (!empty($data['token']) ? $data['token'] : ''),
                    'uuid' => (!empty($data['uuid']) ? $data['uuid'] : ''),
                    'device_type' => (!empty($data['device_type']) ? $data['device_type'] : ''),
                    'id_customer' => $result['customer']['id_customer']
                );
                $insertData = $this->$model->update_deviceData($device_data, $device_data['id_customer']);
            }
        } else if ($result['is_valid'] === FALSE) {
            $result['message'] = 'Invalid Username or Password...';
        } else if ($result['is_valid'] === TRUE && $res['active'] == 0) {
            $result['is_valid'] = FALSE;
            $result['message'] = 'Your account is currently inactive...Kindly contact administrator..';
        }
        $id_branch = (isset($result['customer']['id_branch']) ? $result['customer']['id_branch'] : 0);
        $currency = $this->$model->get_currency($id_branch);
        $result['currency'] = $currency;
        //Sync Existing Data					
        if ($this->config->item("integrationType") == 2) {
            $syncData = $this->sync_existing_data($data['mobile'], $result['customer']['id_customer'], $id_branch);
        }
        $this->response($result, 200);
    }
    //to generate otp and send message to customer
    /*function generateOTP_get()
    {			
        $model = self::MOD_MOB;
        $otp   = $this->$model->generateOTP();
        $this->$model->send_sms($this->get('mobile'),$this->otp_sms($otp));
        $this->response($otp,200);		
    }*/
    // reg & Forgot pw otp For Cus email added//
    function generateOTP_get()
    {
        $model = self::MOD_MOB;
        $email = $this->get('email');
        $mail = $this->$model->forgetUser($this->get('mobile'));
        //print_r($mail);exit;
        $otp = $this->$model->generateOTP();
        if ($email != '' && $email != 'undefined') {
            $to = $email;
            $data['otp'] = $otp['otp'];
            $data['type'] = 4;
            $data['company_details'] = $this->comp;
            $data['name'] = $mail['firstname'];
            $subject = "Reg: " . $this->comp['company_name'] . " Purchase plan";
            $message = $this->load->view('include/emailAccount', $data, true);
            $this->load->model('email_model');
            $sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
            $service = $this->services_modal->checkService(23);
            $sms_data = array(
                "fname" => "",
                "otp" => $otp['otp'],
                "expiry" => $otp['expiry']
            );
            $smsData = $this->services_modal->get_SMS_OTPdata(23, $sms_data);
            $message = $smsData['message'];
            if ($this->config->item('sms_gateway') == '1') {
                $this->sms_model->sendSMS_MSG91($this->get('mobile'), $message, '', $service['dlt_te_id']);
                // $this->sms_model->sendSMS_MSG91($this->get('mobile'),$this->otp_sms($otp));		
            } elseif ($this->config->item('sms_gateway') == '2') {
                $this->sms_model->sendSMS_Nettyfish($this->get('mobile'), $message, 'trans');
            } elseif ($this->config->item('sms_gateway') == '3') {
                $this->sms_model->sendSMS_SpearUC($this->get('mobile'), $message, '', $service['dlt_te_id']);
            } elseif ($this->config->item('sms_gateway') == '4') {
                $this->sms_model->sendSMS_Asterixt($this->get('mobile'), $message, '', $service['dlt_te_id']);
            } elseif ($this->config->item('sms_gateway') == '5') {
                $this->sms_model->sendSMS_Qikberry($this->get('mobile'), $message, '', $service['dlt_te_id']);
            }
            if ($service['serv_whatsapp'] == 1) {
                $smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                $this->sms_model->send_whatsApp_message($mobile, $smsData);
            }
            $otp['status'] = true;
            $otp['msg'] = "OTP send successfully";
            $this->response($otp, 200);
        } else {
            $mail = $this->$model->forgetUser($this->get('mobile'));
            $to = $mail['email'];
            $data['otp'] = $otp['otp'];
            $data['type'] = 0;
            $data['company_details'] = $this->comp;
            $data['name'] = $mail['firstname'];
            $subject = "Reg: " . $this->comp['company_name'] . " purchase plan forgot password";
            $message = $this->load->view('include/emailAccount', $data, true);
            $this->load->model('email_model');
            $sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
            $service = $this->services_modal->checkService(23);
            $sms_data = array(
                "fname" => "",
                "otp" => $otp['otp'],
                "expiry" => $otp['expiry']
            );
            $smsData = $this->services_modal->get_SMS_OTPdata(23, $sms_data);
            /* $message = $smsData; */
            $message = $smsData['message'];
            if ($this->config->item('sms_gateway') == '1') {
                $this->sms_model->sendSMS_MSG91($this->get('mobile'), $message, '', $service['dlt_te_id']);
                // $this->sms_model->sendSMS_MSG91($this->get('mobile'),$this->otp_sms($otp));		
            } elseif ($this->config->item('sms_gateway') == '2') {
                $this->sms_model->sendSMS_Nettyfish($this->get('mobile'), $this->otp_sms($otp), 'trans');
            } elseif ($this->config->item('sms_gateway') == '3') {
                $this->sms_model->sendSMS_SpearUC($this->get('mobile'), $message, '', $service['dlt_te_id']);
            } elseif ($this->config->item('sms_gateway') == '4') {
                $this->sms_model->sendSMS_Asterixt($this->get('mobile'), $message, '', $service['dlt_te_id']);
            } elseif ($this->config->item('sms_gateway') == '5') {
                $this->sms_model->sendSMS_Qikberry($this->get('mobile'), $message, '', $service['dlt_te_id']);
            }
            if ($service['serv_whatsapp'] == 1) {
                $smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                $this->sms_model->send_whatsApp_message($this->get('mobile'), $smsData);
            }
            $this->response($otp, 200);
        }
    }
    //check otp from db
    function verifyOTP_post()
    {
        $model = self::MOD_MOB;
        $result = "";
        $data = $this->get_values();
        $last_otp = $this->$model->get_lastOTP($data['id_customer']);
        $otp_time = date('Y-m-d H:i:s');
        if ($data['otp'] == $last_otp['last_generated_otp']) {
            if ($otp_time <= date('Y-m-d H:i:s', strtotime($last_otp['last_otp_expiry']))) {
                $result = array('is_valid' => TRUE, 'msg' => 'Success');
            } else {
                $result = array('is_valid' => FALSE, 'msg' => 'Your OTP expired');
            }
        } else {
            $result = array('is_valid' => FALSE, 'msg' => 'Your OTP is invalid');
        }
        $this->response($result, 200);
    }
    //Check registration otp
    function check_regOTP_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $otp_time = date('Y-m-d H:i:s');
        if ($data['sysotp'] == $data['userotp']) {
            if ($otp_time <= date('Y-m-d H:i:s', strtotime($data['last_otp_expiry']))) {
                //#Direct_Pay updated function to get $customer data for direct pay option...
                if ($data['mobile'] != '' && $data['mobile'] != null) {
                    $customer = $this->$model->get_customerByMobile($data['mobile']);
                }
                $result = array('is_valid' => TRUE, 'msg' => 'Success', 'customer' => $customer);
            } else {
                $result = array('is_valid' => FALSE, 'msg' => 'Your OTP expired');
            }
        } else {
            $result = array('is_valid' => FALSE, 'msg' => 'Your OTP is invalid');
        }
        $this->response($result, 200);
    }
    //change password
    function resetPassword_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $result = "";
        $status = $this->$model->update_customer(array('passwd' => $this->__encrypt($data['passwd'])), $data['id_customer']);
        if ($status) {
            $result = array("status" => TRUE, "msg" => "Your password updated successfully");
        } else {
            $result = array("status" => FALSE, "msg" => "Unable to proceed your request");
        }
        $this->response($result, 200);
    }
    //Register customer	
    function createCustomer_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $no_deviceData = FALSE;
        $result = '';
        $id_branch = isset($data['id_branch']) ? $data['id_branch'] : NULL;
        $custom_entry_date = $this->mobileapi_model->get_entrydate($id_branch);
        $customer = array(
            'info' => array(
                'firstname' => ucwords($data['firstname']),
                'lastname' => ucfirst($data['lastname']),
                'mobile' => $data['mobile'],
                'title' => (isset($data['title']) ? $data['title'] : NULL),
                'email' => (isset($data['email']) ? $data['email'] : NULL),
                'passwd' => $this->__encrypt($data['passwd']),
                'active' => 1,
                'date_add' => date('Y-m-d H:i:s'),
                'added_by' => 2,
                'id_village' => (isset($data['id_village']) && $data['id_village'] != '' ? $data['id_village'] : NULL),
                'id_branch' => (isset($data['id_branch']) ? $data['id_branch'] : NULL),
                'date_of_birth' => (isset($data['dob']) ? $data['dob'] : NULL),
                "custom_entry_date"   => ($custom_entry_date['edit_custom_entry_date'] == 1 ? $custom_entry_date['custom_entry_date'] : NULL),
                //	'cus_type' =>(isset($data['custype'])?$data['custype']:1),    //
                'gender' => (isset($data['gender']) ? $data['gender'] : null),
                'nominee_name' => (!empty($data['nominee_name']) ? $data['nominee_name'] : NULL),
                'nominee_relationship' => (!empty($data['nominee_relationship']) ? $data['nominee_relationship'] : NULL),
                'nominee_mobile' => (!empty($data['nominee_mobile']) ? $data['nominee_mobile'] : NULL),
                'nominee_pan' => (!empty($data['nominee_pan']) ? $data['nominee_pan'] : NULL),
                'nominee_address1' => (!empty($data['nominee_address1']) ? $data['nominee_address1'] : NULL),
                'nominee_address2' => (!empty($data['nominee_address2']) ? $data['nominee_address2'] : NULL),
            ),
            'address' => array(
                'address1' => (isset($data['address1']) ? $data['address1'] : NULL),
                'address2' => (isset($data['address2']) ? $data['address2'] : NULL),
                'id_country' => (isset($data['id_country']) ? $data['id_country'] : NULL),
                'id_state' => (isset($data['id_state']) ? $data['id_state'] : NULL),
                'id_city' => (isset($data['id_city']) ? $data['id_city'] : NULL),
                'pincode' => (isset($data['pincode']) ? $data['pincode'] : NULL)
            )
        );
        $status = $this->$model->insert_customer($customer);
        if ($status['status'] == TRUE) {
            //Sync Existing Data 				
            if ($this->config->item("integrationType") == 2) {
                $syncData = $this->sync_existing_data($data['mobile'], $status['insertID'], $customer['id_branch']);
            }
            $wallet_acc = $this->registration_model->wallet_accno_generator();
            if ($wallet_acc['wallet_account_type'] == 1) {
                $this->wallet_account_create($status['insertID'], $data['mobile']);
            }
            $id = $status['insertID'];
            $deviceData = array(
                'token' => $data['token'],
                'uuid' => $data['uuid'],
                'device_type' => $data['device_type'],
                'created_on' => date('Y-m-d H:i:s'),
                'id_customer' => $id
            );
            if ($data['token'] != null) {
                $insStatus = $this->$model->insert_deviceData($deviceData);
                if ($insStatus) {
                    $cus = array('notification' => 1);
                    $flag = $this->$model->update_customer($cus, $id);
                }
            } else {
                $insStatus['status'] = FALSE;
                $no_deviceData = TRUE;
            }
            if ($insStatus['status'] == TRUE || $no_deviceData = TRUE) {
                $serviceID = 1;
                $company = $this->$model->company_details();
                $service = $this->services_modal->checkService($serviceID);
                $data = $this->services_modal->get_SMS_data($serviceID, $id);
                $mobile = $data['mobile'];
                $message = $data['message'];
                if ($service['sms'] == 1) {
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
                if ($service['email'] == 1 && $customer['email'] != '') {
                    $to = $customer['email'];
                    $data['name'] = $customer['firstname'];
                    $data['mobile'] = $customer['mobile'];
                    $data['passwd'] = $this->__decrypt($customer['passwd']);
                    $data['company_details'] = $company;
                    $data['type'] = 3;
                    $subject = "Reg: " . $this->comp['company_name'] . " purchase plan registration";
                    $message = $this->load->view('include/emailAccount', $data, true);
                    $sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
                }
                $result = array('status' => TRUE, "id_customer" => $status['insertID'], 'mobile' => $data['mobile'], 'msg' => 'Your number ' . $data['mobile'] . ' registered successfully.');
            } else {
                $result = array("status" => FALSE, "msg" => "Unable to proceed your request,please try again..");
            }
        } else {
            $result = array("status" => FALSE, "msg" => "Unable to proceed your request");
        }
        $this->response($result, 200);
    }
    //get currency info
    function currency_get()
    {
        $model = self::MOD_MOB;
        $id_branch = $this->get('id_branch');
        $currency = $this->$model->get_currency($id_branch);
        $this->response($currency, 200);
    }
    //company info
    function company_get()
    {
        $model = self::MOD_MOB;
        $company = $this->$model->company_details();
        // $company['whatsapp_url'] = "";
        // $company['insta_url'] = "";
        // $company['fb_url'] = "";
        // $company['youtube_url'] = "";
        // $company['web_url'] = "";
        /* $company['insta_url'] = "";
		$company['fb_url'] = "";
		$company['web_url'] = "";
		$company['whatsapp_url'] = ""; */
        $this->response($company, 200);
    }
    //Wallet
    function get_customerWallets_get()
    {
        $model = self::MOD_MOB;
        $id_customer = $this->get('id_customer');
        $id_wal_trans = $this->get('lastIdWalTrans');
        $wallets = $this->$model->get_wallet_accounts($id_customer);
        //    $balance   = ($wallets[0]['wallet_balance_type'] == 0 ? $this->comp['currency_symbol'].' '.$wallets[0]['balance'] : $wallets[0]['balance']);
        $balance = $wallets[0]['balance'];
        $transactions = $this->$model->get_wallet_transactions($id_customer, $id_wal_trans);
        $result = array('wallets' => $wallets, 'balance' => $balance, 'transactions' => $transactions);
        $this->response($result, 200);
    }
    //Delete scheme Account
    function deleteScheme_get()
    {
        //$model = self::MOD_MOB;
        $model = self::SCH_MOD;
        $id_scheme_account = $this->get('id_scheme_account');
        $status = $this->$model->delete_scheme_account($id_scheme_account);
        if ($status['status'] == '1') {
            $result = array('status' => true, 'msg' => 'Your purchase plan deleted successfully');
        } else {
            $result = array('status' => false, 'msg' => 'Unable to proceed your request');
        }
        $this->response($result, 200);
    }
    //join scheme
    function createAccount_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $flag = FALSE;
        $scheme_acc_number = '';
        $result = '';
        $is_referral_by = NULL;
        $cus = $this->$model->get_customerByID($data['id_customer']);
        $settings = $this->$model->is_branchwise_cus_reg();
        $idemployee = (!empty($data['login_type']) && $data['login_type'] == 'EMP' && !empty($data['id_employee']) ? $data['id_employee'] : NULL);
        $added_through = (!empty($data['id_employee']) && $data['id_employee'] > 0 ? 3 : 2); // 2- custmer mobile app, 3 - Admin mobile app
        if ($settings['branch_settings'] == 1) {
            if ($settings['is_branchwise_cus_reg'] == 1) {
                $id_branch = $cus['id_branch'];
            } else if ($settings['branchWiseLogin'] == 1) {
                $id_branch = $data['id_branch'];
            } else {
                $id_branch = (isset($data['id_branch']) ? $data['id_branch'] : NULL);
            }
        } else {
            $id_branch = NULL;
        }
        $schDetail = $this->$model->get_scheme($data['id_scheme'], $data['id_customer']);
        $start_year = $this->payment_modal->get_financialYear();
        if (isset($data['referal_code']) && $data['referal_code'] != '' && preg_match('/^[0-9]{10}+$/', $data['referal_code'])) {
            //customer mobile as referral code
            $is_referral_by = '0';
        } else {
            //employee referral code
            $is_referral_by = '1';
        }
        //  print_r($settings);exit;
        $custom_entry_date = $this->mobileapi_model->get_entrydate($id_branch);
        $schAcc = array(
            'id_customer' => $data['id_customer'],
            'id_scheme' => $data['id_scheme'],
            'start_date' => date('Y-m-d H:i:s'),
            'maturity_date' => ($schDetail['maturity_days'] > 0 ? date('Y-m-d', strtotime(date('Y-m-d') . '+' . $schDetail['maturity_days'] . ' days')) : ($schDetail['total_installments'] > 0 ? date('Y-m-d', strtotime(date('Y-m-d') . '+' . $schDetail['total_installments'] . ' months')) : NULL)),
            // 'group_code' => $data['group_code'],
            'group_code' => $schDetail['is_lucky_draw'] == 1 ? $data['group_code'] : '',
            'scheme_acc_number' => ($data['is_new'] == 'N' ? $data['scheme_acc_number'] : NULL),
            'account_name' => ucwords($data['account_name']),
            'is_new' => $data['is_new'],
            'active' => ($data['sch_approval'] == 1 ? 2 : 1),
            'date_add' => date('Y-m-d H:i:s'),
            'added_by' => 2,
            'id_branch' => $id_branch,
            // "is_referral_by" 	 => $is_referral_by,	
            'firstPayment_amt' => (isset($data['payable']) ? $data['payable'] : NULL),
            'referal_code' => (isset($data['referal_code']) ? $data['referal_code'] : NULL),
            'pan_no' => ($data['pan_no'] != null ? strtoupper($data['pan_no']) : NULL),
            'issue_self_giftBonus' => (isset($data['issue_self_giftBonus']) ? $data['issue_self_giftBonus'] : '2'),
            'start_year' => $start_year,
            "custom_entry_date"   => ($custom_entry_date['edit_custom_entry_date'] == 1 ? $custom_entry_date['custom_entry_date'] : NULL),
            //rk lump scheme fields 
            'lump_joined_weight' => ($schDetail['is_lumpSum'] == 1 && $data['lump_joined_weight'] > 0 ? $data['lump_joined_weight'] : '0.000'),
            'lump_payable_weight' => ($schDetail['is_lumpSum'] == 1 && $data['lump_joined_weight'] > 0 ? ($data['lump_joined_weight'] / $schDetail['total_installments']) : '0.000'),
            "added_by" => $added_through,
            "id_employee" => $idemployee,
        );
        if (isset($data['is_new']) && $data['is_new'] == 'N') {
            if ($data['regExistingReqOtp'] == 0) {
                $schAcc['id_scheme_group'] = (!empty($data['id_scheme_group']) ? $data['id_scheme_group'] : '');
                $isAccNoExist = $this->scheme_modal->verify_existing($schAcc);
                if (!$isAccNoExist) {
                    $scheme_acc = array(
                        "id_scheme" => ($data['id_scheme'] != '' ? $data['id_scheme'] : 0),
                        "id_customer" => $data['id_customer'],
                        "scheme_acc_number" => ($data['scheme_acc_number'] != '' ? $data['scheme_acc_number'] : NULL),
                        "ac_name" => ($data['account_name'] != '' ? $data['account_name'] : NULL),
                        "id_branch" => $id_branch,
                        "id_employee" => NULL,
                        'added_by' => 2,
                        "date_add" => date('Y-m-d H:i:s'),
                        "id_scheme_group" => (isset($data['id_scheme_group']) ? $data['id_scheme_group'] : NULL),
                        'pan_no' => ($data['pan_no'] != null ? strtoupper($data['pan_no']) : NULL),
                        "status" => 0 // processing
                    );
                    $status = $this->scheme_modal->join_existing($scheme_acc);
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
            //RAHUL 
			if($schDetail['is_digi'] == 1){
				$acc_exist = $this->$model->check_has_digi_account($data['id_customer'], $data['id_scheme']);
				if($acc_exist->num_rows() > 0){
                    $msg = 'Your account already exist, Please contact customer care to proceed your request';
                    $result = array("status" => FALSE, "msg" => $msg);
                    $this->response($result, 200);
                } else {
                    $status = $this->$model->insert_schemeAcc($schAcc);
                }
            } else {
                $status = $this->$model->insert_schemeAcc($schAcc);
            }
            // $status = $this->$model->insert_schemeAcc($schAcc);
            if ($status['insertID'] > 0) {
                $cusData = $this->$model->get_customer_acc($status['insertID']);
                $updateData['ref_no'] = $this->config->item('cliIDcode') . "/" . $cusData['group_code'] . "/" . $status['insertID'];
                $this->$model->update_account($updateData, $status['insertID']);

                /* PAN required chit_settings and scheme based - Esakkiraja_20260109 starts */
				$panExist = $this->kyc_exists($data['id_customer']);
				if (!$panExist && $data['pan_no'] != null) {
					$insData = array(
						'id_customer'	=> (isset($data['id_customer']) ? $data['id_customer'] : NULL),
						'kyc_type'      => 2,
						'img_url' 		=>  NULL,
						'number'        => (isset($data['pan_no']) ? $data['pan_no'] : NULL),
						'name'          => NULL,
						'status'        => 0,
						'date_add' 	    => date('Y-m-d H:i:s')
					);
					$this->$model->insert_kyc($insData);
					/* PAN required chit_settings and scheme based - Esakkiraja_20260109 ends */
				}
            }

            $flash_msg = '';
            if ($status['sch_data']['free_payment'] == 1) {
                // esakki 11-11
                $pay_insert_data = $this->mobileapi_model->free_payment_data($status['sch_data'], $status['insertID'], $id_branch);
                $fn_year = $this->payment_modal->get_financialYear();
                $pay_insert_data['receipt_year'] = $fn_year;
                $pay_insert_data['id_branch'] = $id_branch;
                if ($status['sch_data']['receipt_no_set'] == 1) {
                    // $this->db->query('LOCK TABLES payment WRITE');
                    $pay_insert_data['receipt_no'] = $this->payment_modal->generate_receipt_no($data['id_scheme'], $id_branch);
                }
                $pay_add_status = $this->payment_modal->addPayment($pay_insert_data);
                /* if($this->db->trans_status()===TRUE)
            {
                    $this->db->trans_commit();
            }
            $this->db->trans_begin(); */
                //RHR scheme starts...	
                //     echo '<pre>';print_r($status['insertID']);exit;                    	
                $dt_pay = $pay_insert_data['date_payment'];
                //RHR starts....
                //Update 1 ins date in acc start_date and calculate maturity date
                //Update maturity date if maturity type is fixed
                $settings = $this->payment_modal->getSchemeData($status['insertID']);
                // 		print_r($settings);exit;
                if ($settings['maturity_days'] != null && $settings['maturity_days'] > 0) {
                    $start_date = $settings['start_date'];
                    $maturity_date = date('Y-m-d', strtotime($start_date . ' + ' . $settings['maturity_days'] . ' days'));
                    $maturity_data = array('maturity_date' => $settings['calc_maturity_date']);
                    $status_maturity = $this->payment_modal->updData($maturity_data, 'id_scheme_account', $status['insertID'], 'scheme_account');
                }
                //RHR ends...  
                // print_r($status['insertID']);exit;
                $ins_cycle = $this->payment_modal->get_due_date($pay_insert_data['due_type'], $dt_pay, $status['insertID']);
                if (sizeof($ins_cycle[0]) > 0) {
                    $cycle_data = array(
                        'due_date' => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
                        'due_date_to' => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
                        'grace_date' => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
                        'installment' => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
                        'is_limit_exceed' => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
                    );
                    $this->payment_modal->updData($cycle_data, 'id_payment', $pay_add_status['insertID'], 'payment');
                }
                //RHR scheme ends....
                $settings = $this->payment_modal->getSchemeData($status['insertID']);
                if ($settings['maturity_type'] == 4) {
                    $this->payment_modal->calculate_maturityLapse_Date($status['insertID']);
                }
                $paid = $this->payment_modal->getPaidInsData($status['insertID']);
                if (sizeof($paid) > 0) {
                    $paid_ins = array('total_paid_ins' => $paid['paid_installments']);
                    $this->payment_modal->updData($paid_ins, 'id_scheme_account', $status['insertID'], 'scheme_account');
                }
                $pay_mode_array[] = array(
                    'id_payment' => $pay_add_status['insertID'],
                    'payment_amount' => $pay_insert_data['payment_amount'],
                    'payment_mode' => 'FP',
                    'payment_status' => $pay_insert_data['payment_status'],
                    'payment_date' => $dt_pay, //date("Y-m-d H:i:s"),
                    'created_time' => date("Y-m-d H:i:s"),
                    // 'created_by'	    => 2
                );
                // print_r($pay_mode_array);exit;
                if (!empty($pay_mode_array)) {
                    $cardPayInsert = $this->payment_modal->insertBatchData($pay_mode_array, 'payment_mode_details');
                }
                // esakki ends 11-11
                // $this->db->query('UNLOCK TABLES');
                $flash_msg = 'As free installment offer, 1st installment of your scheme credited successfully';
                $scheme_acc_no = $this->$model->accno_generatorset();
                if ($scheme_acc_no['status'] == 1 && $scheme_acc_no['schemeacc_no_set'] == 0) {
                    // $this->db->query('LOCK TABLES scheme_account WRITE');
                    $scheme_acc_number = $this->payment_modal->account_number_generator($schAcc['id_scheme'], $schAcc['id_branch'], '');
                    if ($scheme_acc_number != NULL) {
                        $updateData['scheme_acc_number'] = $scheme_acc_number;
                    }
                    $updSchAc = $this->$model->update_account($updateData, $status['insertID']);
                    // $this->db->query('UNLOCK TABLES');
                }
            }
            if ($schDetail['is_digi'] == 1) {
                //update customer data...
                $id_customer = $data['id_customer'];
                $myDate = date('Y-m-d H:i:s');
                $customer = array(
                    'nominee_name'        => (!empty($data['nominee_name']) ? $data['nominee_name'] : NULL),
                    'nominee_relationship'        => (!empty($data['nominee_relationship']) ? $data['nominee_relationship'] : NULL),
                    'nominee_mobile'    => (!empty($data['nominee_mobile']) ? $data['nominee_mobile'] : NULL),
                );
                $customer['date_upd'] = $myDate;
                $address = array(
                    'address1'       => ucfirst($data['address1']),
                );
                $cflag = $this->$model->update_customer($customer, $id_customer);
                $isExists = $this->$model->isAddressExist($id_customer);
                if ($isExists) {
                    $address['date_upd'] = $myDate;
                    $aflag = $this->$model->update_customerAdd($address, $id_customer);
                } else {
                    $address['id_customer'] = $id_customer;
                    $address['date_add'] = $myDate;
                    $aflag = $this->$model->insert_customerAdd($address);
                }
				$acc_data = $this->digigold_modal->digiGold_account($data['id_customer'], $data['id_scheme']);
				$acc_data['benefit_calc'] = $this->digigold_modal->get_digi_benefit($acc_data);
            }
            // print_r($this->db->last_query());exit;
            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $schData1 = $this->$model->get_schemeaccount_detail($status['insertID']);
                /* if($data['type'] == "DigiGold"){
                     $schData1[0]['payable'] = $data['payable'];
                 } */
                $this->load->model("scheme_modal");
                $schData = $this->scheme_modal->getJoinedScheme($status['insertID']);
                $serviceID = 2;
                $service = $this->services_modal->checkService($serviceID);
                $company = $this->$model->company_details();
                if ($service['sms'] == 1) {
                    $id = $status['insertID'];
                    $data = $this->services_modal->get_SMS_data($serviceID, $id);
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
                if ($service['email'] == 1 && isset($schData[0]['email']) && $schData[0]['email'] != '') {
                    $data['schData'] = $schData[0];
                    $data['company'] = $company;
                    $data['type'] = 1;
                    $to = $schData[0]['email'];
                    $subject = "Reg.  " . $this->comp['company_name'] . " scheme joining";
                    $message = $this->load->view('include/emailScheme', $data, true);
                    $sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
                }
                //rk scheme approval
                if ($data['sch_approval'] == 1) {
                    $message = 'Purchase plan added to your account. Please wait for admin approval';
                } else {
                    $message = 'Purchase plan added to your account.';
                }
				if ($schData1[0]['is_digi'] == 1) {
					$result = array('status' => TRUE, 'msg' => $message, 'chit' => $schData1[0], 'digi_acc' => $acc_data, 'free_pay' => $flash_msg);
				} else {
                $result = array('status' => TRUE, 'msg' => $message, 'chit' => $schData1[0], 'free_pay' => $flash_msg);
				}
            } else {
                $this->db->trans_rollback();
                $result = array("status" => FALSE, "msg" => "Unable to proceed your request");
            }
        }
        $this->response($result, 200);
    }
    //chit payment
    function chitPayment_post()
    {
        $payData = $this->get_values();
        $pay_flag = FALSE;
        $model = self::MOD_MOB;
        $rcvdAmount = (float) $payData['amount'];
        if (isset($payData)) {
            if ($payData['scheme_type'] === 1) {
                $metal_rate = $this->$model->getMetalRate();
                $gold_rate = (float) $metal_rate['goldrate_22ct'];
                $actAmount = $gold_rate * $payData['udf2'];
                $pay_flag = ($rcvdAmount >= $actAmount ? TRUE : FALSE);
            } else {
                $chit = $this->$model->get_schemeByChit($payData['udf1']);
                $pay_flag = ($rcvdAmount >= $chit['amount'] ? TRUE : FALSE);
            }
        }
        if ((isset($payData) && $payData['udf1'] != '' && $payData['amount'] && $pay_flag == TRUE) || (isset($_POST) && $_POST['txnid'] && $_POST['udf1'])) //cross check received and actual amount
        {
            if (isset($payData) && $payData['udf1'] != '') {
                //generate unique id
                $txnid = uniqid('VSD');
                //set insert data
                $insertData = array(
                    "id_scheme_account" => (isset($payData['udf1']) ? $payData['udf1'] : NULL),
                    "payment_amount" => (isset($payData['amount']) ? $payData['amount'] : NULL),
                    "bank_charges" => (isset($payData['udf5']) && $payData['ud5'] != '' ? $payData['udf5'] : 0.00),
                    "date_payment" => date('Y-m-d H:i:s'),
                    "metal_rate" => (isset($payData['udf3']) && $payData['udf3'] != '' ? $payData['udf3'] : 0.00),
                    "metal_weight" => (isset($payData['udf2']) && $payData['udf2'] != '' ? $payData['udf2'] : 0.000),
                    "trans_id" => (isset($txnid) ? $txnid : NULL),
                    "added_by" => 2,
                    "payment_status" => -1 //status - 0 (pending), will change to 1 after approved at backend
                );
                //inserting pay_data before gateway
                $payment = $this->$model->insert_payment($insertData);
            }
            $data = array(
                'key' => $this->config->item('key'),
                'txnid' => $txnid,
                'amount' => (isset($payData['amount']) ? $payData['amount'] : ''),
                'firstname' => (isset($payData['firstname']) ? $payData['firstname'] : ''),
                'lastname' => (isset($payData['lastname']) ? $payData['lastname'] : ''),
                'email' => (isset($payData['email']) ? $payData['email'] : ''),
                'phone' => (isset($payData['phone']) ? $payData['phone'] : ''),
                'productinfo' => (isset($payData['productinfo']) ? $payData['productinfo'] : ''),
                'address1' => (isset($payData['address1']) ? $payData['address1'] : ''),
                'address2' => (isset($payData['address2']) ? $payData['address2'] : ''),
                'city' => (isset($payData['city']) ? $payData['city'] : ''),
                'state' => (isset($payData['state']) ? $payData['state'] : ''),
                'country' => (isset($payData['country']) ? $payData['country'] : ''),
                'zipcode' => (isset($payData['zipcode']) ? $payData['zipcode'] : ''),
                'udf1' => (isset($payData['udf1']) ? $payData['udf1'] : ''),
                'udf2' => (isset($payData['udf2']) ? $payData['udf2'] : ''),
                'udf3' => (isset($payData['udf3']) ? $payData['udf3'] : ''),
                'udf4' => (isset($payData['udf4']) ? $payData['udf4'] : ''),
                'udf5' => (isset($payData['udf5']) ? $payData['udf5'] : '')
            );
            $hash_sequence = Misc::get_hash($data, $this->config->item('salt'));
            //$data['surl'] = 'payment_success'; 
            //$data['furl'] = 'payment_failure';
            $data['surl'] = base_url('index.php/paymt/payview_success');
            $data['furl'] = base_url('index.php/paymt/payview_failure');
            $data['hash'] = $hash_sequence;
            $pay = new Payment($this->config->item('salt'), 'test');
            $status = $pay->pay($data);
            // $status = $pay->pay($data);
            // $pay_status = Misc::show_reponse($status);
            $this->response($status, 200);
        }
    }
    function successURL_post()
    {
        //echo "Payment Success" . "<pre>" . print_r( $_POST, true ) . "</pre>";
        $trans_id = $_POST['txnid'];
        $pay_status = array();
        $model = self::MOD_MOB;
        $updateData = array(
            "bank_name" => (isset($_POST['issuing_bank']) ? $_POST['issuing_bank'] : NULL),
            "payment_mode" => (isset($_POST['mode']) ? $_POST['mode'] : NULL),
            "card_no" => (isset($_POST['mode']) && ($_POST['mode'] == 'CC' || $_POST['mode'] == 'DC') ? $_POST['cardnum'] : NULL),
            "payu_id" => (isset($_POST['mihpayid']) ? $_POST['mihpayid'] : NULL),
            "remark" => (isset($_POST['field9']) ? $_POST['field9'] : NULL),
            "payment_ref_number" => (isset($_POST['bank_ref_num']) ? $_POST['bank_ref_num'] : NULL),
            "payment_status" => 0 //status - 0 (pending), will change to 1 after approved at backend
        );
        $payment = $this->$model->updateGatewayResponse($updateData, $trans_id);
        $serviceID = 3;
        $service = $this->services_modal->checkService($serviceID);
        $payData = $this->$model->get_invoiceData(isset($payment['id_payment']) ? $payment['id_payment'] : '');
        if ($payment['status'] == true) {
            if ($service['sms'] == 1) {
                $mobile = $payData[0]['mobile'];
                $message = "Hi " . $payData[0]['firstname'] . ", Thanks for your payment with " . $this->comp['company_name'] . ". Your payment amount Rs. " . $payData[0]['payment_amount'] . " (sub. to realisation) is processed successfully.";
                $senderid = $this->config->item('sms_senderid');
                $arr = array("@customer_mobile@" => $mobile, "@message@" => $message, "@senderid@" => $senderid);
                $url = $this->config->item('sms_url');
                $user_sms_url = strtr($url, $arr);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $user_sms_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                $result = curl_exec($ch);
                curl_close($ch);
                unset($ch);
            }
            if ($service['email'] == 1 && isset($payData[0]['email']) && $payData[0]['email'] != '') {
                $to = $payData[0]['email'];
                $subject = "Reg - " . $this->comp['company_name'] . " payment for the chitscheme";
                $data['payData'] = $payData[0];
                $data['type'] = 2;
                $message = $this->load->view('include/emailPayment', $data, true);
                $sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
            }
            $pay_status = array('status' => TRUE, 'msg' => 'Payment successful. Thanks for your payment with' . $this->comp['company_name']);
            //$this->response($pay_status,200);
            redirect('paymt/payview_success');
        } else {
            $pay_status = array('status' => FALSE, 'msg' => 'Error in updating the database.Please contact administrator at your earliest convenience.');
            $this->response($pay_status, 200);
        }
    }
    /* Payment failed - logic goes here. */
    function failureURL_post()
    {
        //echo "Payment Failure" . "<pre>" . print_r( $_POST, true ) . "</pre>";
        $trans_id = $_POST['txnid'];
        $pay_status = array();
        $updateData = array(
            "bank_name" => (isset($_POST['issuing_bank']) ? $_POST['issuing_bank'] : NULL),
            "payment_mode" => (isset($_POST['mode']) ? $_POST['mode'] : NULL),
            "card_no" => (isset($_POST['mode']) && ($_POST['mode'] == 'CC' || $_POST['mode'] == 'DC') ? $_POST['cardnum'] : NULL),
            "payu_id" => (isset($_POST['mihpayid']) ? $_POST['mihpayid'] : NULL),
            "remark" => (isset($_POST['field9']) ? $_POST['field9'] : NULL),
            "payment_ref_number" => (isset($_POST['bank_ref_num']) ? $_POST['bank_ref_num'] : NULL),
            "payment_status" => -1 //status - 0 (pending), will change to 1 after approved at backend
        );
        $payment = $this->$model->updateGatewayResponse($updateData, $trans_id);
        $payData = $this->$model->get_invoiceData(isset($payment['id_payment']) ? $payment['id_payment'] : '');
        //$payData = $this->registration_model->get_cusData($this->session->userdata('username'));
        $serviceID = 3;
        $service = $this->$model->checkService($serviceID);
        if ($service['sms'] == 1) {
            $mobile = $payData[0]['mobile'];
            $message = "Hi " . $payData[0]['firstname'] . ", Error occured in processing your payment. Please contact administrator.";
            $senderid = $this->config->item('sms_senderid');
            $arr = array("@customer_mobile@" => $mobile, "@message@" => $message, "@senderid@" => $senderid);
            $url = $this->config->item('sms_url');
            $user_sms_url = strtr($url, $arr);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $user_sms_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            $result = curl_exec($ch);
            curl_close($ch);
            unset($ch);
        }
        if ($service['email'] == 1 && isset($payData[0]['email']) && $payData[0]['email'] != '') {
            $to = $payData[0]['email'];
            $subject = "Reg - " . $this->comp['company_name'] . " payment for the chitscheme";
            $data['payData'] = $payData[0];
            $data['type'] = -1;
            $message = $this->load->view('include/emailPayment', $data, true);
            $sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
        }
        $scheme_failure = array("errMsg" => 'Payment failure.Please try again later');
        redirect('paymt/payview_failure');
    }
    //generate hash
    function generateHash_post()
    {
        $payData = $this->get_values();
        //generate unique id
        $txnid = uniqid('VSD');
        $data = array(
            'key' => $this->config->item('key'),
            'txnid' => $txnid,
            'amount' => (isset($payData['amount']) ? $payData['amount'] : ''),
            'firstname' => (isset($payData['firstname']) ? $payData['firstname'] : ''),
            'lastname' => (isset($payData['lastname']) ? $payData['lastname'] : ''),
            'email' => (isset($payData['email']) ? $payData['email'] : ''),
            'phone' => (isset($payData['phone']) ? $payData['phone'] : ''),
            'productinfo' => (isset($payData['productinfo']) ? $payData['productinfo'] : ''),
            'address1' => (isset($payData['address1']) ? $payData['address1'] : ''),
            'address2' => (isset($payData['address2']) ? $payData['address2'] : ''),
            'city' => (isset($payData['city']) ? $payData['city'] : ''),
            'state' => (isset($payData['state']) ? $payData['state'] : ''),
            'country' => (isset($payData['country']) ? $payData['country'] : ''),
            'zipcode' => (isset($payData['zipcode']) ? $payData['zipcode'] : ''),
            'udf1' => (isset($payData['udf1']) ? $payData['udf1'] : ''),
            'udf2' => (isset($payData['udf2']) ? $payData['udf2'] : ''),
            'udf3' => (isset($payData['udf3']) ? $payData['udf3'] : ''),
            'udf4' => (isset($payData['udf4']) ? $payData['udf4'] : ''),
            'udf5' => (isset($payData['udf5']) ? $payData['udf5'] : '')
        );
        $hash_sequence = Misc::get_hash($data, $this->config->item('salt'));
        $userpay = array('paydata' => $data, 'hash' => $hash_sequence, 'txnid' => $txnid);
        $this->response($userpay, 200);
    }
    //to get offers
    function offers_get()
    {
        $model = self::MOD_MOB;
        $data['offers_banners'] = $this->$model->get_offersAndBanners();
        $data['offers'] = $this->$model->get_offers();
        $data['banners'] = $this->$model->get_banners();
        $this->response($data, 200);
    }
    //to get new arrivals
    function new_arrivals_get()
    {
        $model = self::MOD_MOB;
        $data['new_arrivals'] = $this->$model->get_new_arrivals();
        $this->response($data, 200);
    }
    //to get particular data
    function newArrDetail_get()
    {
        $id = $this->get('id_new_arrival');
        $model = self::MOD_MOB;
        $result = $this->$model->get_newArrdetail($id);
        $this->response($result, 200);
    }
    //to get particular data
    function offerDetail_get()
    {
        $id = $this->get('id_offer');
        $model = self::MOD_MOB;
        $result = $this->$model->get_offerdetail($id);
        $this->response($result, 200);
    }
    function gift_items_get()
    {
        $model = self::MOD_MOB;
        $data['gift_items'] = $this->$model->get_gift_items();
        $this->response($data, 200);
    }
    function giftItemDetail_get()
    {
        $id = $this->get('id_new_arrival');
        $model = self::MOD_MOB;
        $data['gift_items'] = $this->$model->get_giftItemDetail($id);
        $this->response($data, 200);
    }
    //to chit_settings data
    function getSettings_get()
    {
        $model = self::MOD_MOB;
        $result['chit_settings'] = $this->$model->get_settings();
        $this->response($result, 200);
    }
    //to noti enable/disable by customer
    function getcusSettings_get()
    {
        $id = $this->get('id_customer');
        $model = self::MOD_MOB;
        $result = $this->$model->get_cus_noti_settings($id);
        $this->response($result, 200);
    }
    //to noti enable/disable by customer
    function updatecusSettings_post()
    {
        $data = $this->get_values();
        $model = self::MOD_MOB;
        $updData = array(
            "id_customer" => $data['id_customer'],
            "notification" => $data['notification']
        );
        $deviceData = array(
            'token' => $data['token'],
            'uuid' => $data['uuid'],
            'device_type' => $data['device_type'],
            'id_customer' => $data['id_customer']
        );
        $this->db->trans_begin();
        $insStatus = $this->$model->update_cusnotisettings($updData);
        if ($insStatus) {
            if ($data['notification'] == 1) {
                if ($data['token'] != null) {
                    $res = $this->$model->insert_deviceData($deviceData);
                    if ($this->db->trans_status() === TRUE) {
                        $this->db->trans_commit();
                        $status['status'] = $res['status'];
                    } else {
                        $this->db->trans_rollback();
                        $status['status'] = FALSE;
                    }
                } else {
                    $this->db->trans_rollback();
                    $status['status'] = FALSE;
                }
            } else {
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $status['status'] = TRUE;
                } else {
                    $this->db->trans_rollback();
                    //$status['status'] = FALSE;
                }
            }
        } else {
            $this->db->trans_rollback();
            $status['status'] = FALSE;
        }
        /*}*/
        $this->response($status, 200);
    }
    function closedAcc_get()
    {
        $cus_id = $this->get('id_customer');
        $model = self::MOD_MOB;
        $closed = $this->$model->get_closed_account($cus_id);
        $result = array('closed_acc' => $closed, 'msg' => sizeof($closed) > 0 ? '' : 'Closed schemes not found');
        $this->response($result, 200);
    }
    function getClassificationAll_get()
    {
        $model = self::MOD_MOB;
        $result = $this->$model->getClassification();
        $this->response($result, 200);
    }
    function getClassification_get()
    {
        $model = self::MOD_MOB;
        $id = $this->get('id_scheme');
        $result = $this->$model->getClassification($id);
        $this->response($result, 200);
    }
    function getVisClass_get()
    {
        $id_branch = ($this->get('id_branch') == 'null' ? '' : $this->get('id_branch'));
        // Get all classifications
        $result = $this->scheme_modal->get_classifications();
        // Filter by active schemes only when branchwise_scheme is enabled and id_branch is provided
        $cs = $this->db->query("SELECT branchwise_scheme FROM chit_settings LIMIT 1")->row_array();
        if ($cs['branchwise_scheme'] == 1 && $id_branch != '') {
            $model = self::MOD_MOB;
            $activeSchemes = $this->$model->get_activeSchemes($id_branch);
            $activeClassIds = array();
            if (!empty($activeSchemes)) {
                foreach ($activeSchemes as $sch) {
                    if (!empty($sch['id_classification'])) {
                        $activeClassIds[$sch['id_classification']] = true;
                    }
                }
            }
            if (!empty($activeClassIds) && isset($result['classification'])) {
                $filtered = array();
                foreach ($result['classification'] as $cls) {
                    if (isset($activeClassIds[$cls['id_classification']])) {
                        $filtered[] = $cls;
                    }
                }
                $result['classification'] = $filtered;
            } else if (empty($activeClassIds)) {
                $result['classification'] = array();
            }
        }
        $this->response($result, 200);
    }
    //Branch Wise Show Scheme Classify //
    /* function getVisClass_get()
     {
       $model = self::MOD_MOB;
       $result = $this->$model->getVisClass($this->get('id_branch'));
       $this->response($result,200);
      }*/
    // check maintenance mode and version_compare
    /*	function getVersion_get()
   {
  $version['android'] = $this->current_android_version;
  $version['newver_android'] = $this->new_android_version;
  $version['playPackage'] = $this->config->item('aPackage');
  $version['aMsg'] = "New version available at play store.";// version alert message
  $version['ios'] = $this->current_ios_version;
  $version['newver_ios'] = $this->new_ios_version;
  $version['iPackage'] = $this->config->item('iPackage');
  $version['iMsg'] = "New version available at app store.";// version alert message
  $version['comp'] = $this->comp;
  $version['mode'] = $version['comp']['maintenance_mode'];
  $version['text'] = $version['comp']['maintenance_text']; // maintenance text
  $version['title'] = "Update Available";
  $version['comp']['playstore_url'] = $this->config->item('paystore_url');
  $version['sms_sender_id'] = $this->sms_data['sms_sender_id'];
  $popup = $this->mobileapi_model->getPopup();
  $version['showpopup'] = !empty($popup) ? 1 : 0;
  $version['popupimg'] = $popup;
  $this->response($version,200);
  }*/
    // check maintenance mode and version_compare
    function getVersion_get()
    {
        $config_data = $this->mobileapi_model->getConfigData();
        $version['android'] = $config_data['current_android_version'];
        $version['newver_android'] = $config_data['new_android_version'];
        $version['playPackage'] = $config_data['app_a_pack']; //$this->config->item('aPackage');
        $version['aMsg'] = "New version available at play store."; // version alert message
        $version['ios'] = $config_data['current_ios_version'];
        $version['newver_ios'] = $config_data['new_ios_version'];
        $version['iPackage'] = $config_data['app_i_pack'];  //$this->config->item('iPackage');
        $version['iMsg'] = "New version available at app store."; // version alert message
        $version['comp'] = $this->comp;
        $version['mode'] = $version['comp']['maintenance_mode'];
        $version['text'] = $version['comp']['maintenance_text']; // maintenance text
        $version['title'] = "Update Available";
        $version['comp']['playstore_url'] = $config_data['play_str_url']; //$this->config->item('paystore_url');
        $version['sms_sender_id'] = $this->sms_data['sms_sender_id'];
        $popup = $this->mobileapi_model->getPopup();
        $version['showpopup'] = !empty($popup) ? 1 : 0;
        $version['popupimg'] = $popup;
        $version['show'] = 1;
        if (($_GET['platform'] == 'android' || $_GET['platform'] == 'web') && $_GET['app_version'] == $config_data['new_android_version']) {
            $version['mode'] = 0;
        } else if (($_GET['platform'] == 'ios' || $_GET['platform'] == 'web') && $_GET['app_version'] == $config_data['new_ios_version']) {
            $version['mode'] = 0;
        } else {
            $version['mode'] = $version['comp']['maintenance_mode'];
        }
        $this->response($version, 200);
    }
    /*function getVersion_get()
    { 
           $version['android'] = $this->current_android_version;
           $version['new_android_ver'] = $this->new_android_version;
        $version['ios'] = $this->current_ios_version;
        $version['comp'] = $this->comp;
        $version['mode'] = $version['comp']['maintenance_mode'];
        $version['text'] = $version['comp']['maintenance_text']; // maintenance text
        $version['msg'] = "New version available at store.";// version alert message
        $version['comp']['playstore_url'] =$this->config->item('paystore_url');
        $version['sms_sender_id'] = $this->sms_data['sms_sender_id'];
        $popup = $this->mobileapi_model->getPopup();
        $version['showpopup'] = !empty($popup) ? 1 : 0; 
        $version['popupimg'] = $popup; 
        $this->response($version,200);
    }*/
    // payview page	
    function getActivecardBrands_get()
    {
        $model = self::MOD_MOB;
        $result['cc'] = $this->$model->getActivecardBrands(1);
        $result['dc'] = $this->$model->getActivecardBrands(2);
        $this->response($result, 200);
    }
    function getsavedCards_get()
    {
        $model = self::MOD_MOB;
        $mobile = $this->get('mobile');
        $id_customer = $this->get('id_customer');
        $data = $this->$model->get_customerByID($id_customer);
        $gateway = $this->$model->getBranchGatewayData($data['id_branch'], 1);
        if ($mobile && $gateway) {
            $key = $gateway['param_1'];
            $var1 = $gateway['param_1'] . ':' . $mobile;
            $command = 'payment_related_details_for_mobile_sdk';
            /*Security parameter– SHA512(key|command|var1|salt)*/
            $hash_sequence = $key . '|' . $command . '|' . $var1 . '|' . $gateway['param_2'];
            $hash_value = strtolower(hash('sha512', $hash_sequence));
            $url = $gateway['api_url'];
            $data = array(
                'key' => $gateway['param_1'],
                'command' => $command,
                'hash' => $hash_value,
                'var1' => $var1,
                'salt' => $gateway['param_2']
            );
            $response = array();
            $response = $this->httpPost($url, $data);
            if ($response != '') {
                $this->response($response, 200);
            } else {
                $res = [];
                $this->response($res, 200);
            }
        }
    }
    /*Delete saved card*/
    function deleteSavedCard_get()
    {
        $mobile = $this->get('mobile');
        $var2 = $this->get('card_token');
        if ($mobile) {
            $data = $this->payment_gateway('1', '1');
            $key = $data['param_1'];
            $var1 = $data['param_1'] . ':' . $mobile;
            $command = 'delete_user_card';
            /*Security parameter– SHA512(key|command|var1|salt)*/
            $hash_sequence = $key . '|' . $command . '|' . $var1 . '|' . $data['param_2'];
            $hash_value = strtolower(hash('sha512', $hash_sequence));
            $url = $data['api_url'];
            $data = array(
                'key' => $data['param_1'],
                'command' => $command,
                'hash' => $hash_value,
                'var1' => $var1,
                'var2' => $var2
            );
            $response = array();
            $response = $this->httpPost($url, $data);
            $this->response($response, 200);
        }
    }
    function httpPost($url, $params)
    {
        $postData = '';
        //create name value pairs seperated by &
        foreach ($params as $k => $v) {
            $postData .= $k . '=' . $v . '&';
        }
        $postData = rtrim($postData, '&');
        $output = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, count($postData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        $output = curl_exec($ch);
        /*curl_close($ch);
        var_dump($output);exit;*/
        return json_decode($output);
    }
    // functions to register existing scheme with validation
    /*function checkAccData_post()
    {
       $result = array();
       $model = self::MOD_MOB;
       $data = $this->get_values();
       if(isset($data['mobile'])){
            $array_data = array(
                            'mobile' 		=> $data['mobile']
                            //'id_branch' 		=> $data['id_branch']
                        );
       }
       else{
          $array_data = array(
                    //'id_branch' 		=> $data['id_branch'],
                    'group_name' 		=> strtoupper($data['group_name']),
                    'scheme_acc_number' => $data['scheme_acc_number']
                );
       }
       $res = $this->$model->isAccExist($array_data);
       if($res['status'])
       {
               $result = array('status' => TRUE,'mobile'=>$res['mobile'],'msg'=>$res['msg']);
       }
       else
       {
               $result = array('status' => FALSE,'msg'=>$res['msg']);
       }
       $this->response($result,200);			
    }
    function insertExisAcc_post()
    {
       $result = array();
       $model = self::MOD_MOB;
       $data = $this->get_values();
       $this->db->trans_begin();
       $res = $this->$model->insertExisAccData($data);
       echo $this->db->last_query();
       if($res['status'])
       {
               $payData = $this->$model->insert_paymentData($res['data']);
               if($payData['status']){
                $update = $this->$model->updateOfflineData($res['data']);
                   if($this->db->trans_status()===TRUE)
                {
                    $this->db->trans_commit();
                       $result = array('status' => $update);		   		
                   }
                   else{
                    $this->db->trans_rollback();
                    $result = array('status' => $update,'msg'=>"Unable to proceed your request.");
                }
            }
             else
               {
                       $this->db->trans_rollback();
                       $result = array('status' => FALSE,'msg'=>"Unable to proceed your request");
               }
       }
       else
       {
               $this->db->trans_rollback();
               $result = array('status' => FALSE,'msg'=>"Unable to proceed your request,contact customer care.");
       }
       $this->response($result,200);			
    }*/
    // END OF -- functions to register existing scheme with validation
    // branch name list
    public function get_branch_get()
    {
        $model = self::MOD_MOB;
        $id_scheme = $this->input->get('id_scheme');
        // print_r($id_scheme);exit;
        $data = $this->get_branch($id_scheme);
        // print_r($this->db->last_query());exit;
        $this->response($data, 200);
    }
    function viewall_parentcategory_get()
    {
        $model = self::MOD_MOB;
        $data = $this->get_category();
        //echo json_encode($data);
        $this->response($data, 200);
    }
    function oldget_branch()
    {
        $sql = "SELECT b.id_branch, b.name, b.active, b.short_name,
	b.id_employee,b.address1, b.address2, b.id_country, b.id_state, 
	b.id_city, b.phone,b.mobile, b.cusromercare, b.pincode,
	b.metal_rate_type,c.branch_settings FROM branch b
	join chit_settings c  where active =1 and (show_to_all = 1) order by b.sort";
        $branch = $this->db->query($sql)->result_array();
        return $branch;
    }
    function get_branch($id_scheme = '')
    {
        // 1. If no scheme → return all branches
        if (empty($id_scheme)) {
            $sql = "SELECT 
                        b.id_branch, 
                        b.name, 
                        b.active, 
                        b.short_name,
                        b.id_employee,
                        b.address1, 
                        b.address2, 
                        b.id_country, 
                        b.id_state, 
                        b.id_city, 
                        b.phone,
                        b.mobile, 
                        b.cusromercare, 
                        b.pincode,
                        b.metal_rate_type,
                        c.branch_settings 
                    FROM branch b
                    JOIN chit_settings c  /* NO ON — CROSS JOIN */
                    WHERE b.show_to_all IN (1,2) and  active =1 
                    ORDER BY b.sort";
            return $this->db->query($sql)->result_array();
        }
        // SCHEME FILTER
        $branch_ids = $this->db->select('id_branch')
            ->from('scheme_branch')
            ->where('id_scheme', $id_scheme)
            ->get()
            ->result_array();
        if (empty($branch_ids)) {
            return [];
        }
        $branch_ids = array_column($branch_ids, 'id_branch');
        $ids = implode(',', $branch_ids);
        // 2. FILTERED DIRECT SQL (CROSS JOIN)
        $sql = "SELECT 
                    b.id_branch, 
                    b.name, 
                    b.active, 
                    b.short_name,
                    b.id_employee,
                    b.address1, 
                    b.address2, 
                    b.id_country, 
                    b.id_state, 
                    b.id_city, 
                    b.phone,
                    b.mobile, 
                    b.cusromercare, 
                    b.pincode,
                    b.metal_rate_type,
                    c.branch_settings
                FROM branch b
                JOIN chit_settings c  /* CROSS JOIN - NO ON CLAUSE */
                WHERE  active =1 and b.id_branch IN ($ids)
                  AND b.show_to_all IN (1,2)
                ORDER BY b.sort";
        return $this->db->query($sql)->result_array();
    }
    function get_category()
    {
        //CONCAT('".base_url()."/admin/assets/img/category/','',catimage)
        $sql = "SELECT id_category, catimage as image ,categoryname as category_name,id_parent, ifnull(description, '') as description, if(active = 1,'Active','Inactive') as active
		FROM category cat where id_parent='1' and cat.active=1";
        $category = $this->db->query($sql)->result_array();
        return $category;
    }
    function view_subparent_category_get()
    {
        $category = $this->get('id_category');
        $sql = "SELECT id_category,catimage as image ,categoryname as category_name,id_parent, ifnull(description, '') as description, if(active = 1,'Active','Inactive') as active
		FROM category cat where cat.active=1 and id_parent='" . $category . "'";
        $result = $this->db->query($sql);
        if ($result->num_rows() > 0) {
            $resposeData = array('type' => 'subcat', 'responseData' => $this->db->query($sql)->result_array(), 'msg' => 'Sub-category fetched successfully');
        } else {
            $sql = "SELECT id_product,productname as product_name,proimage
		      FROM products pro
			  LEFT JOIN category as cat ON cat.id_category =pro.id_category
			  where pro. active=1 and pro.id_category='" . $category . "'";
            if ($this->db->query($sql)->num_rows() > 0) {
                $resposeData = array('type' => 'product', 'responseData' => $this->db->query($sql)->result_array(), 'msg' => 'Products fetched successfully');
            } else {
                $resposeData = array('type' => 'product', 'responseData' => array(), 'msg' => 'No Products found');
            }
        }
        //echo json_encode($resposeData);
        $this->response($resposeData, 200);
    }
    function productDetail_post()
    {
        $data = $this->get_values();
        $sql = "SELECT id_product,pro.id_category as id_category,productname as product_name, ifnull(pro.description, '') as description, if(pro.active = 1,'Active','Inactive') as active,proimage,weight,size,type,price,code,purity
			      FROM products pro 
				  where pro.id_product=" . $data['id_product'];
        $product = $this->db->query($sql)->row_array();
        // echo json_encode($product);
        $this->response($product, 200);
    }
    // branch name list 
    // wallet work  //
    function wallet_account_create($cus_id, $mobile)
    {
        $this->load->model('services_modal');
        $this->load->model('email_model');
        $model = self::MOD_MOB;
        $wallet_acc_no = $this->services_modal->get_wallet_acc_number();
        $insertData = array(
            'id_customer' => (isset($cus_id) && $cus_id != '' ? $cus_id : NULL),
            'id_employee' => 2,
            'wallet_acc_number' => (isset($wallet_acc_no) ? $wallet_acc_no : NULL),
            'issued_date' => date('y-m-d H:i:s'),
            'remark' => "Credits",
            'active' => 1
        );
        //inserting data                  
        $status = $this->services_modal->walletacc_insert($insertData);
        $wallAcc = $this->services_modal->get_walletacc($status['insertID']);
        // $this->mobileapi_model->insChitwallet($status['insertID'],$mobile,$cus_id);
        if ($status) {
            $serviceID = 8;
            $service = $this->services_modal->checkService($serviceID);
            $id = $status['insertID'];
            $data = $this->services_modal->get_SMS_data($serviceID, $status['insertID']);
            $mobile = $data['mobile'];
            $message = $data['message'];
            if ($service['sms'] == 1) {
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
                $this->sms_model->send_whatsApp_message($message, $smsData);
            }
        }
    }
    // wallet work  //
    function referral_linksend_get()
    {
        $mobile = $this->get('mobile');
        $isReferral = $this->get('allow_referral');
        $serviceID = ($isReferral == 1 ? 15 : 19);
        if ($mobile != '') {
            $data = $this->services_modal->get_SMS_data($serviceID, $mobile);
            $link = $this->shortenurl($this->config->item('paystore_url'));
            //  print_r($data['message']);exit;
            $message = str_replace('##', $link, $data['message']);
            $result = array('status' => TRUE, 'message' => $message);
        } else {
            $result = array('status' => FALSE, 'msg' => "Unable to proceed your request,contact customer care.");
        }
        $this->response($result, 200);
    }
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
    // Netbanking list  
    function getNetbankingStatus_get()
    {
        $model = self::MOD_MOB;
        $var1 = 'default';
        $command = 'getNetbankingStatus';
        $id_customer = $this->get('id_customer');
        $data = $this->$model->get_customerByID($id_customer);
        $payData = $this->$model->getBranchGatewayData($data['id_branch'], 1);
        $hash_sequence = $payData['param_1'] . '|' . $command . '|' . $var1 . '|' . $payData['param_2'];
        $hash_value = strtolower(hash('sha512', $hash_sequence));
        $url = $payData['api_url'];
        $data = array(
            'key' => $payData['param_1'],
            'command' => $command,
            'hash' => $hash_value,
            'var1' => $var1,
            'salt' => $payData['param_2']
        );
        //print_r( $this->payment_gateway);exit;
        $response = array();
        $response = $this->httpPost($url, $data);
        $this->response($response, 200);
    }
    // Existing scheme Registration functions
    function join_existing_byacc($data)
    {
        $scheme_acc = array(
            "group_code" => ($data['group_code'] != '' ? $data['group_code'] : ''),
            "scheme_acc_number" => ($data['scheme_acc_number'] != '' ? $data['scheme_acc_number'] : ''),
            "account_name" => ($data['account_name'] != '' ? $data['account_name'] : ''),
            "id_branch" => (isset($data['id_branch']) ? ($data['id_branch'] == '' ? NULL : $data['id_branch']) : NULL)
        );
        $acc = $this->scheme_modal->isAccExist($scheme_acc);
        //  echo $this->db->last_query();
        if ($acc['status']) {
            $model = self::MOD_MOB;
            $otp = $this->$model->generateOTP();
            $service = $this->services_modal->checkService(24);
            $sms_data = array(
                "fname" => "",
                "otp" => $otp
            );
            $smsData = $this->services_modal->get_SMS_OTPdata(24, $sms_data);
            $message = $smsData['message'];
            if ($this->config->item('sms_gateway') == '1') {
                $sendotp = $this->sms_model->sendSMS_MSG91($acc['mobile'], $message, '', $dlt_te_id);
                // $sendotp = $this->sms_model->sendSMS_MSG91($acc['mobile'],$this->otp_sms($otp));		
            } elseif ($this->config->item('sms_gateway') == '2') {
                $sendotp = $this->sms_model->sendSMS_Nettyfish($acc['mobile'], $this->otp_sms($otp), 'trans');
            } elseif ($this->config->item('sms_gateway') == '3') {
                $this->sms_model->sendSMS_SpearUC($acc['mobile'], $message, '', $service['dlt_te_id']);
            } elseif ($this->config->item('sms_gateway') == '4') {
                $this->sms_model->sendSMS_Asterixt($acc['mobile'], $message, '', $service['dlt_te_id']);
            } elseif ($this->config->item('sms_gateway') == '5') {
                $this->sms_model->sendSMS_Qikberry($acc['mobile'], $message, '', $service['dlt_te_id']);
            }
            if ($service['serv_whatsapp'] == 1) {
                $smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $smsData['params']];
                $sendotp = $this->sms_model->send_whatsApp_message($acc['mobile'], $smsData);
            }
            $this->send_mail($acc['mobile'], $acc['email'], $acc['name'], $otp['otp']);
            if ($sendotp) {
                // return array('status' => TRUE,'otpDetail'=>$otp, 'msg' => 'OTP has been sent to registered mobile number.');
                return array('status' => TRUE, 'otpDetail' => $otp, 'msg' => 'OTP has been sent to registered mobile number.', 'register_mobile' => $acc['mobile']);
            }
        } else {
            //$this->session->set_flashdata('successMsg','Unable to proceed your request');
            //return array('status' => FALSE, 'msg' => $acc['msg']);
            return array('status' => FALSE, 'msg' => $acc['msg'], 'register_mobile' => $acc['mobile']);
        }
    }
    public function send_mail($mobile, $email = "", $name = "", $OTP = "")
    {
        $to = $email;
        $data['otp'] = $OTP;
        $data['type'] = 20;
        $data['company_details'] = $this->comp;
        $data['name'] = $name;
        $subject = "Reg: " . $this->comp['company_name'] . " Existing OTP Verification";
        $message = $this->load->view('include/emailAccount', $data, true);
        $this->load->model('email_model');
        $sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
        return TRUE;
    }
    public function submit_schregOtp_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        if ($data['sysotp'] == $data['userotp']) {
            $otp_time = date('Y-m-d H:i:s');
            if ($otp_time <= date('Y-m-d H:i:s', strtotime($data['last_otp_expiry']))) {
                $this->db->trans_begin();
                $res = $this->scheme_modal->insertExisAccData($data);
                if ($res['status']) {
                    $payData = $this->scheme_modal->insert_paymentData($res['data'], $res['insertID']);
                    if ($payData['status']) {
                        $this->scheme_modal->updateInterTableStatus($res['data'], $res['insertID'], $payData['suceedIds']);
                        if ($this->db->trans_status() === TRUE) {
                            $this->db->trans_commit();
                            $response = array('status' => TRUE, 'msg' => 'Purchase plan registered successfully.');
                            $this->response($response, 200);
                        } else {
                            $this->db->trans_rollback();
                            $response = array('status' => FALSE, 'msg' => 'Unable to proceed your request,contact customer care.');
                            $this->response($response, 200);
                        }
                    } else {
                        $this->db->trans_rollback();
                        $response = array('status' => FALSE, 'msg' => 'Unable to proceed your request,contact customer care.');
                        $this->response($response, 200);
                    }
                } else {
                    $response = array('status' => FALSE, 'msg' => 'Unable to proceed your request,contact customer care.');
                    $this->response($response, 200);
                }
            } else {
                $result = array('status' => FALSE, 'msg' => 'Your OTP expired');
            }
        } else {
            $result = array('status' => FALSE, 'msg' => 'Your OTP is invalid');
        }
        $this->response($response, 200);
    }
    function existingRegReq_get()
    {
        $model = self::MOD_MOB;
        $data = $this->$model->get_exisRegReq($this->get('id_customer'));
        $this->response($data, 200);
    }
    function uploadCusimage_post()
    {
        $model = self::MOD_MOB;
        $data = (array) json_decode(file_get_contents("php://input"));
        $parts = explode(";base64,", $data['fileName']);
        $addressimagebase64 = base64_decode($parts[1]);
        $cus_id = $data['id_customer'];
        $img_path = self::CUS_IMG_PATH . "/" . $cus_id;
        if (!is_dir($img_path)) {
            mkdir($img_path, 0777, TRUE);
        }
        $path = self::CUS_IMG_PATH . $cus_id . "/customer.jpg";
        $filename = "customer.jpg";
        $file = $path;
        file_put_contents($file, $addressimagebase64);
        /*	$status =  $this->$model->update_customer(array('cus_img'=>$filename ),$cus_id);  
          if($status){
              $this->response(array('status' => true ,  'path' => base_url().$path.'?nocache='.time()),200);
          }else{
               $this->response(array('status' => false   ,'path' => null),200);
          }*/
        if ($data['fileName'] != null) {
            $status = $this->$model->update_customer(array('cus_img' => $filename), $cus_id);
            if ($status) {
                $this->response(array('status' => true, 'path' => base_url() . $path . '?nocache=' . time()), 200);
            } else {
                $this->response(array('status' => false, 'path' => null), 200);
            }
        } else {
            // 	print_r($data['fileName']);exit;
            // If filename is null, delete the existing file
            if (file_exists($path)) {
                unlink($path);
            }
            // Update database to remove image reference
            $status = $this->$model->update_customer(array('cus_img' => null), $cus_id);
            if ($status) {
                $this->response(array('status' => true, 'path' => ""), 200);
            } else {
                $this->response(array('status' => false, 'path' => null), 200);
            }
        }
    }
    function getAllPaymentGateways_get()
    {
        $model = self::MOD_MOB;
        $id_customer = $this->get('id_customer');
        $total_installments = $this->get('total_installments');
        $id_branch = $this->get('id_branch');
        $id_metal = $this->get('id_metal') ?? '';// optional: commodity filter for cost_center=4
        $data = $this->$model->get_customerByID($id_customer);
        // $result['pgData'] = $this->$model->getBranchGateways($data['id_branch']);
        $result['pgData'] = $this->$model->getBranchGateways($id_branch, $id_metal);
        //$result['pgData'] = $this->$model->getAllPG();
        $result['cc'] = $this->$model->getActivecardBrands(1);
        $result['dc'] = $this->$model->getActivecardBrands(2);
        $result['msg'] = "";
        $this->response($result, 200);
    }
    /*modified fun mobile_FullPayredeem_post by - Haritha 26-1-24*/
    function mobile_FullPayredeem_post()
    {
        $payData = $this->get_values();
        $pay_flag = TRUE;
        $allow_flag = FALSE;
        $submit_pay = FALSE;
        $cusData = $this->payment_modal->get_customer($payData['phone']);
        $redeemed_amount = $payData['redeemed_amount'];
        //generate txnid
        $txnid = uniqid(time());
        $i = 1;
        $udf1 = "";
        $udf2 = "";
        $udf3 = "";
        $productinfo = "";
        $this->db->trans_begin();
        foreach ($payData['pay_arr'] as $pay) {
            if ($cusData['branch_settings'] == 1) {
                if ($cusData['is_branchwise_cus_reg'] == 1) {
                    $id_branch = $cusData['id_branch'];
                } else {
                    $id_branch = $pay->id_branch;
                    //$id_branch  = $branch['sch_join_branch'];
                }
            } else {
                $id_branch = NULL;
            }
            //validate amount
            $chit = $this->payment_modal->get_schemeByChit($pay->udf1);    //print_r($chit);exit;
            if ($pay->scheme_type == 1) {
                $metal_rate = $this->payment_modal->getMetalRate('');
                $gold_rate = (float) $metal_rate['goldrate_22ct'];
                $amt = $gold_rate * $pay->udf2;
                $allow_flag = ($pay->amount >= $amt ? TRUE : FALSE);
            } else {
                $allow_flag = ($pay->amount >= $chit['amount'] ? TRUE : TRUE);
            }
            if ($pay->scheme_type == 2 || ($pay->scheme_type == 3 && $chit['wgt_convert'] == 0)) {
                $data = array('metal_rate' => $pay->udf3, 'amount' => $pay->udf4);
                $metal_wgt = $this->amount_to_weight($data);
            } else {
                $metal_wgt = (isset($pay->udf2) && $pay->udf2 != '' ? $pay->udf2 : 0.000);
            }
            //rate fixed at the time of scheme join
            if ($chit['one_time_premium'] == 1 && $chit['rate_fix_by'] == 0 && $chit['rate_select'] == 1) {
                $metal_rate = $this->payment_modal->getMetalRate('');
                $gold_rate = (float) $metal_rate['goldrate_22ct'];
                if ($gold_rate != 0) {
                    $isRateFixed = $this->mobileapi_model->isRateFixed($pay->udf1);
                    if ($isRateFixed['status'] == 0) {
                        $updData = array(
                            "fixed_wgt" => $pay->amount / $gold_rate,
                            "firstPayment_amt" => $pay->amount,
                            "fixed_metal_rate" => $gold_rate,
                            "rate_fixed_in" => 1,
                            "fixed_rate_on" => date("Y-m-d H:i:s")
                        );
                        //print_r($updData);exit; 
                        $ratestatus = $this->mobileapi_model->updFixedRate($updData, $pay->udf1);
                    } else {
                        $data = array('is_valid' => TRUE, 'success' => TRUE, 'msg' => "Rate already fixed !!");
                    }
                }
            }
            //RHR starts....
            //Update 1 ins date in acc start_date and calculate maturity date
            if ($chit['paid_installments'] == 0) {
                $start_date = array('start_date' => date('Y-m-d H:i:s'));
                $updfirstPayamt = $this->payment_modal->update_account($start_date, $pay->udf1);
                //Update maturity date if maturity type is fixed
                $settings = $this->payment_modal->getSchemeData($pay->udf1);
                if ($settings['maturity_type'] == 2 && $settings['maturity_days'] != null && $settings['maturity_days'] > 0) {
                    $start_date = $settings['start_date'];
                    $maturity_date = date('Y-m-d', strtotime($start_date . ' + ' . $settings['maturity_days'] . ' days'));
                    $maturity_data = array('maturity_date' => $settings['calc_maturity_date']);
                    $mat_sts = $this->payment_modal->updateAtData($maturity_data, 'id_scheme_account', $pay->udf1, 'scheme_account');
                }
            }
            //RHR ends...            					
            if ($allow_flag) {
                //set insert data
                $start_year = $this->payment_modal->get_financialYear();
                $insertData = array(
                    "id_scheme_account" => (isset($pay->udf1) ? $pay->udf1 : NULL),
                    "payment_amount" => (isset($pay->amount) ? $pay->amount + ($pay->discount == "" ? 0.00 : 0.00) : NULL),
                    "gst" => (isset($chit['gst']) ? $chit['gst'] : 0),
                    "gst_type" => (isset($chit['gst_type']) ? $chit['gst_type'] : 0),
                    "payment_type" => "Wallet Redeem",
                    "payment_mode" => "REF_WALLET",
                    "due_type" => $pay->due_type,
                    "no_of_dues" => 1,
                    "actual_trans_amt" => 0,
                    "date_payment" => date('Y-m-d H:i:s'),
                    "metal_rate" => (isset($pay->udf3) && $pay->udf3 != '' ? $pay->udf3 : 0.00),
                    "metal_weight" => $metal_wgt,
                    "id_transaction" => (isset($txnid) ? $txnid . '-' . $i : NULL),
                    "ref_trans_id" => (isset($txnid) ? $txnid : NULL), // to update pay status after trans complete.
                    "remark" => "Wallet Redeem",
                    "added_by" => 2,
                    "add_charges" => (isset($pay->charge) ? $pay->charge : NULL),
                    "discountAmt" => ($pay->discount != "" ? $pay->discount : 0.00),
                    "payment_status" => 1,
                    "id_payGateway" => 0,
                    'id_branch' => $id_branch,
                    "receipt_year" => $start_year,
                    "pay_email" => $pay_email,
                    "redeemed_amount" => (isset($payData['redeemed_amount']) ? $payData['redeemed_amount'] : 0.00),
                    #Direct_Pay
                    'paidBy_id_customer' => NULL, //$payData['paidBy_id_customer'],
                    'paidBy_mobile' => NULL //$payData['paidBy_mobile'],
                );
                $payment = $this->payment_modal->addPayment($insertData);
                $ins_cycle = $this->payment_modal->get_due_date($pay->due_type, date('Y-m-d H:i:s'), $pay->udf1);
                if (sizeof($ins_cycle[0]) > 0) {
                    $cycle_data = array(
                        'due_date' => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
                        'due_date_to' => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
                        'grace_date' => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
                        'installment' => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
                        'is_limit_exceed' => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
                    );
                    $this->payment_modal->updData($cycle_data, 'id_payment', $payment['insertID'], 'payment');
                }
                $paid = $this->payment_modal->getPaidInsData($pay->udf1);
                if (sizeof($paid) > 0) {
                    $paid_ins = array('total_paid_ins' => $paid['paid_installments']);
                    $this->payment_modal->updData($paid_ins, 'id_scheme_account', $pay->udf1, 'scheme_account');
                }
                $i++;
            }
        }
        if ($this->db->trans_status() === TRUE && $allow_flag) {
            $this->db->trans_commit();
            $transData = array();
            //by haritha - 26-1-24
            if ($payment['insertID'] > 0) {
                $payIds = $this->payment_modal->getPayIds($txnid);
                if (sizeof($payIds) > 0) {
                    // Wallet Debit :: Haritha's code altered by Pavithra on 27-8-2024 [Debit splitup]
                    $actual_trans_amt = $payIds[0]['actual_trans_amt'];
                    $act_wal_redeemed = $payIds[0]['redeemed_amount'];
                    $remaining_amt = 0;
                    $remaining_wal_amt = 0;
                    foreach ($payIds as $pay) {
                        $tax = 0;
                        $pay_amount = 0;
                        if ($pay['gst_type'] == 1) { // Exclusive
                            $tax = $pay['gst_amount'];
                        }
                        $pay_amount = ($pay['payment_amount'] - $pay['discountAmt'] + $tax);
                        $mode_amount = $pay_amount;
                        if ($actual_trans_amt > 0) { // Having Gateway balance, add gateway mode
                            if ($actual_trans_amt > $pay_amount) {
                                $actual_trans_amt -= $pay_amount;
                            } else if ($actual_trans_amt == $pay_amount) {
                                $actual_trans_amt -= $pay_amount;
                            } else if ($actual_trans_amt < $pay_amount) {
                                $mode_amount = $actual_trans_amt;
                                $remaining_amt = abs($actual_trans_amt - $pay_amount);
                                $actual_trans_amt = 0;
                                $pay_amount = $remaining_amt;
                            }
                            /*echo "Having Gateway balance, add gateway mode.<br/>";
                            echo "actual_trans_amt : ".$actual_trans_amt."<br/>";
                            echo "remaining_amt : ".$remaining_amt."<br/>";*/
                            // Insert payment Mode details               
                            $arrayPayMode = array(
                                'payment_amount' => $mode_amount,
                                'payment_date' => date("Y-m-d H:i:s"),
                                'created_time' => date("Y-m-d H:i:s"),
                                "payment_mode" => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "NET_BANKING" ? "NB" : $paymentMode))),
                                "remark" => $txMsg . "[" . $txTime . "] mbl-status",
                                "payment_ref_number" => $referenceId,
                                "payment_status" => $updateData['payment_status']
                            );
                            if ($pay['due_type'] == 'GA') {
                                $arrayPayMode['id_adv_payment'] = $pay['id_payment'];
                                $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'general_advance_mode_detail');
                            } else {
                                $arrayPayMode['id_payment'] = $pay['id_payment'];
                                $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'payment_mode_details');
                            }
                        }
                        if (($remaining_amt > 0 || $actual_trans_amt == 0) && $act_wal_redeemed > 0) { // Having wallet redemption: add wallet mode & add debit transaction 
                            if ($act_wal_redeemed > $pay_amount) {
                                $act_wal_redeemed -= $pay_amount;
                            } else if ($act_wal_redeemed == $pay_amount) {
                                $act_wal_redeemed -= $pay_amount;
                            }
                            /*else if($act_wal_redeemed < $pay_amount){
                                $remaining_amt		   = abs($act_wal_redeemed-$pay_amount);
                                $act_wal_redeemed   = 0;
                            }*/
                            /*echo "Having wallet redemption : add wallet mode & add debit transaction.<br/>";
                            echo "act_wal_redeemed : ".$act_wal_redeemed."<br/>";*/
                            // Insert payment Mode details               
                            $arrayPayMode = array(
                                'payment_amount' => $pay_amount,
                                'payment_date' => date("Y-m-d H:i:s"),
                                'created_time' => date("Y-m-d H:i:s"),
                                "payment_mode" => "REF_WALLET",
                                "remark" => "Wallet Utilized",
                                "payment_ref_number" => NULL,
                                "payment_status" => 1
                            );
                            if ($pay['due_type'] == 'GA') {
                                $arrayPayMode['id_adv_payment'] = $pay['id_payment'];
                                $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'general_advance_mode_detail');
                            } else {
                                $arrayPayMode['id_payment'] = $pay['id_payment'];
                                $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'payment_mode_details');
                            }
                            $WalletinsData = array(
                                'id_wallet_account' => $pay['id_wallet_account'],
                                'transaction_type' => 1, //0-Credit,1-Debit
                                'type' => 0, //CRM
                                'id_sch_ac' => $pay['id_scheme_account'],
                                'value' => $pay_amount,
                                'description' => 'Chit Redeem',
                                'date_transaction' => date("Y-m-d H:i:s"),
                                'id_employee' => NULL,
                                'date_add' => date("Y-m-d H:i:s"),
                                'credit_for' => 'Redeem',
                                'id_payment' => $pay['id_payment']
                            );
                            $this->payment_modal->insertData($WalletinsData, 'wallet_transaction');
                        }
                        //Redeem amount in wallet credit for customer - by haritha 19-1-24
                        /*if($pay['redeemed_amount'] > 0 && $pay['redeemed_amount'] != NULL && $pay['id_wallet_account'] != NULL)
                        {
                            $WalletinsData=array(
                                                                    'id_wallet_account'=>$pay['id_wallet_account'],
                                                                    'transaction_type' =>1, //0-Credit,1-Debit
                                                                    'type'             =>0, //CRM
                                                                    'id_sch_ac'        =>$pay['id_scheme_account'],
                                                                    'value'            =>$pay['redeemed_amount'],
                                                                    'description'      =>'Chit Redeem',
                                                                    'date_transaction' => date("Y-m-d H:i:s"),
                                                                    'id_employee'      =>NULL,
                                                                    'date_add'	       => date("Y-m-d H:i:s"),
                                                                    'credit_for'      =>'Redeem',
                                                                    'id_payment'      => $pay['id_payment']
                                                                    );
                                                        $this->payment_modal->insertData($WalletinsData,'wallet_transaction');
                              // Multi mode payment	                        
                             $arrayPayMode=array(
                                            'id_payment'         => $pay['id_payment'],
                                            'payment_amount'     => (isset($pay['redeemed_amount']) ? $pay['redeemed_amount'] : NULL),
                                            'payment_date'		 => date("Y-m-d H:i:s"),
                                            'created_time'	     => date("Y-m-d H:i:s"),
                                            "payment_mode"       => "REF_WALLET",
                                            "remark"             => $txMsg."[".$txTime."] mbl-status - Wallet Utilized",
                                            "payment_ref_number" => $referenceId,
                                            "payment_status"     => 1
                                            );
                                    $cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details'); 
                        }*/
                        //ends
                        $schData = [];
                        $cusRegData = [];
                        $transData = [];
                        if ($pay['due_type'] != 'GA') {
                            //13-09-2022 Coded by haritha 
                            //employee incentive credits based on installment settings
                            if ($pay['referal_code'] != '' && $pay['emp_refferal'] == 1) {
                                $type = 1; //1- employee 2- agent
                                $emp_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'], $pay['id_scheme_account'], $type, $pay['id_payment']);
                                if ($emp_refral > 0) {
                                    $res = $this->insertEmployeeIncentive($emp_refral, $pay['id_scheme_account'], $pay['id_payment']);
                                    foreach ($emp_refral as $emp) {
                                        if ($emp['referal_amount'] > 0) {
                                            //$res = $this->insertEmployeeIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
                                            if ($emp['credit_for'] == 1) {
                                                $this->customerIncentive($emp, $pay['id_scheme_account'], $pay['id_payment']);
                                            }
                                        }
                                    }
                                }
                            }
                            // Generate account  number  
                            if ($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set'] == 3) {
                                if ($pay['scheme_acc_number'] == '' || $pay['scheme_acc_number'] == null || $pay['scheme_acc_number'] == 0) {
                                    $ac_group_code = NULL;
                                    // Lucky draw
                                    if ($pay['is_lucky_draw'] == 1) { // Based on scheme settings 
                                        // Update Group code in scheme_account table 
                                        $updCode = $this->payment_modal->updateGroupCode($pay);
                                        $ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
                                    }
                                    // $this->db->query('LOCK TABLES scheme_account WRITE');
                                    $scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'], $pay['branch'], $ac_group_code);
                                    if ($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0) {
                                        $schData['scheme_acc_number'] = $scheme_acc_number;
                                        $cusRegData['scheme_acc_number'] = $scheme_acc_number;
                                    } else {
                                        // $this->db->query('UNLOCK TABLES');
                                    }
                                }
                            }
                            // Generate receipt number
                            if ($pay['receipt_no_set'] == 1 && ($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2)) {
                                // $this->db->query('LOCK TABLES payment WRITE');
                                $receipt['receipt_no'] = $this->payment_modal->generate_receipt_no($pay['id_scheme'], $pay['branch']);
                                $payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'], $receipt);
                                // $this->db->query('UNLOCK TABLES');
                            }
                            if ($pay['id_scheme_account'] > 0) {
                                if (sizeof($schData) > 0) { // Update scheme account
                                    $this->payment_modal->update_account($schData, $pay['id_scheme_account']);
                                    // $this->db->query('UNLOCK TABLES');
                                }
                                if (sizeof($cusRegData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                    $this->payment_modal->update_cusreg($cusRegData, $pay['id_scheme_account']); // Update Customer reg - Client ID, Ac No
                                }
                                if (sizeof($transData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                    $this->payment_modal->update_trans($transData, $pay['id_scheme_account']); // Update Transaction - Client ID
                                }
                            }
                            //Update First Payment Amount In Scheme Account
                            $approval_type = $this->config->item('auto_pay_approval');
                            if (($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable'] == 1 || $pay['firstPayamt_as_payamt'] == 1) && ($pay['firstPayment_amt'] == '' || $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0)) {
                                if ($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "")) { // Fix First payable as weight
                                    $fixPayable = array('firstPayment_wgt' => $pay['metal_weight']);
                                } else {
                                    $fixPayable = array('firstPayment_amt' => $pay['payment_amount']);
                                }
                                $status = $this->payment_modal->update_account($fixPayable, $pay['id_scheme_account']);
                            }
                            if ($this->config->item('auto_pay_approval') == 2 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                if ($this->config->item('integrationType') == 1) {
                                    $this->insert_common_data_jil($pay['id_payment']);
                                } else if ($this->config->item('integrationType') == 2) {
                                    $this->mobileapi_model->insert_common_data($pay['id_payment']);
                                }
                            }
                        }
                    }
                    $service = $this->services_modal->checkService($serviceID);
                    if ($service['sms'] == 1) {
                        $id = $payment['id_payment'];
                        $data = $this->services_modal->get_SMS_data($serviceID, $id);
                        $mobile = $data['mobile'];
                        $message = $data['message'];
                        $this->mobileapi_model->send_sms($mobile, $message, '', $service['dlt_te_id']);
                    }
                }
            }
            $this->response(array('status' => TRUE, 'msg' => 'Paid Successfully'), 200);
        } else {
            $this->db->trans_rollback();
            $this->response(array('status' => FALSE, 'msg' => 'Couldn\'t proceed your request,try after sometime.'), 200);
        }
    }
    function amount_to_weight($to_pay)
    {
        $converted_metal_wgt = $to_pay['amount'] / $to_pay['metal_rate'];
        return $converted_metal_wgt;
    }
    function insertWalletTrans($tran)
    {
        if ($tran) {
            $redeemed_amount = $tran['redeemed_amount'];
            if ($tran['wallet_balance_type'] == 1) {
                $redeemed_amount = (($redeemed_amount / $tran['wallet_amt_per_points']) * $tran['wallet_points']);
            }
            $transDetailData = array(
                "amount" => $tran['actual_trans_amt'],
                "date_add" => date('Y-m-d H:i:s'),
                "remark" => 'Debited for purchase plan payment ' . $tran['txnid'],
                'trans_points' => $redeemed_amount
            );
            if ($tran['walletIntegration'] == 0) { // 0 - No integration, 1 - Req integration as like SSS
                $transDetailData['trans_type'] = 2;
                $updwallet = $this->payment_modal->updwallet($transDetailData, $tran['mobile']);
                $r = array("status" => $updwallet);
                return $r;
            } else {
                $cat_code = 'SS';
                $wal_cat_settings = $this->payment_modal->getWcategorySettings($cat_code);
                // Begin the transaction
                $this->db->trans_begin();
                if ($wal_cat_settings) {
                    $transData = array(
                        "record_to" => 1, //1 - offline , 2 - online
                        "entry_date" => date('Y-m-d H:i:s'),
                        "bill_no" => $tran['txnid'],
                        "id_branch" => $tran['branch'],
                        "trans_type" => 2,
                        "mobile" => $tran['mobile'],
                        "is_transferred" => 'N',
                        "is_modified" => 0,
                        "date_add" => date('Y-m-d H:i:s'),
                        'actual_redeemed' => $redeemed_amount,
                        "use_points" => 1,
                        "redeem_req_pts" => $redeemed_amount,
                        "record_type" => 2 // 1 - offline , 2 - online
                    );
                    $transDetailData['id_wcat_settings'] = $wal_cat_settings['id_wcat_settings'];
                    $transDetailData['category_code'] = $cat_code;
                    $wallAccount = $this->payment_modal->getInterWalletCustomer($transData['mobile']);
                    $allow = FALSE;
                    if ($wallAccount['status']) {
                        $upd_data = array(
                            "available_points" => ($tran['available_points'] - $redeemed_amount),
                            "last_update" => date('Y-m-d H:i:s'),
                            "mobile" => $tran['mobile'],
                        );
                        $w_status = $this->payment_modal->updInterWalletAcc($upd_data);
                        if ($w_status) {
                            $allow = TRUE;
                        }
                    } else {
                        $transDetailData['trans_type'] = 2;
                        $transDetailData['bill_no'] = $tran['txnid'];
                        $transDetailData['id_branch'] = $tran['branch'];
                        $updwallet = $this->payment_modal->updwallet($transDetailData, $tran['mobile']);
                        $msg = 'Thank you for using ' . $this->comp['company_name'] . ' wallet. Redeemed ' . number_format($redeemed_amount, '2', '.', '') . ' points for scheme payment.';
                        if ($updwallet) {
                            $smsData = array(
                                'mobile' => $tran['mobile'],
                                'message' => $msg
                            );
                            $r = array(
                                'message' => 'Sucessfully updated',
                                "status" => TRUE
                            );
                        } else {
                            $r = array(
                                'message' => $this->db->_error_message(),
                                "query" => $this->db->last_query(),
                                "status" => false
                            );
                        }
                    }
                    if ($allow) {
                        $t_status = $this->payment_modal->insertData($transData, 'inter_wallet_trans');
                        if ($t_status['status']) {
                            $transDetailData['id_inter_wallet_trans'] = $t_status['insertID'];
                            $td_status = $this->payment_modal->insertData($transDetailData, 'inter_wallet_trans_detail');
                            if ($td_status['status']) {
                                /*if(!$wallAccount['status']){
                                     $ac_data = array(	
                                          "available_points"  => 0,
                                          "date_add"          => date('Y-m-d H:i:s'),
                                          "mobile" 	 	 	=> $tran['mobile'],
                                      );
                                     $w_status  = $this->payment_modal->insertData($ac_data,'inter_wallet_account');  
                                     if($w_status['status']){
                                      $allow = TRUE;
                                  }
                             }else{
                                     $upd_data = array(	
                                              "available_points"  => (float)($tran['available_points']-$tran['redeemed_amount']),
                                              "last_update"       => date('Y-m-d H:i:s'),
                                              "mobile" 	 	 	=> $tran['mobile'],
                                          );
                                     $w_status  = $this->payment_modal->updInterWalletAcc($upd_data);
                                     if($w_status){
                                      $allow = TRUE;
                                  }
                             } */
                                $transDetailData['trans_type'] = 2;
                                $transDetailData['bill_no'] = $tran['txnid'];
                                $transDetailData['id_branch'] = $tran['branch'];
                                $updwallet = $this->payment_modal->updwallet($transDetailData, $tran['mobile']);
                                $syncWalData = array(
                                    "points" => (float) ($tran['available_points'] - $redeemed_amount),
                                    "mobile" => $tran['mobile'],
                                );
                                if ($tran['branch'] != '') {
                                    foreach ($this->branch as $bran) {
                                        $syncWalData['branch_' . $bran] = 0;
                                    }
                                } else {
                                    $syncWalData['branch_1'] = 0;
                                }
                                $isExist = $this->payment_modal->getSyncWalletByMobile($tran['mobile']);
                                if ($isExist) {
                                    $syncWalData['last_update'] = date('Y-m-d H:i:s');
                                    $this->payment_modal->updateSyncWal($syncWalData);
                                } else {
                                    $syncWalData['date_add'] = date('Y-m-d H:i:s');
                                    $this->payment_modal->insertData($syncWalData, 'inter_sync_wallet');
                                }
                                //	$msg = 'Thank you for purchasing at Saravana Selvarathnam Retail Pvt Ltd . Your Wallet Balance '.number_format($tran['available_points']+$tran['redeemed_amount'],'2','.','').' points. Redeemed '.number_format($tran['redeemed_amount'],'2','.','').' points. New Wallet Balance '.number_format($tran['available_points'],'2','.','').' Points.'; 
                                $msg = 'Thank you for purchasing at ' . $this->comp['company_name'] . ' . Your Wallet Balance ' . number_format($tran['available_points'], '2', '.', '') . ' points. Redeemed ' . number_format($redeemed_amount, '2', '.', '') . ' points. New Wallet Balance ' . number_format(($tran['available_points'] - $redeemed_amount), '2', '.', '') . ' Points.';
                                if ($updwallet) {
                                    $smsData = array(
                                        'mobile' => $tran['mobile'],
                                        'message' => $msg
                                    );
                                }
                            } else {
                                $r = array(
                                    'message' => $this->db->_error_message(),
                                    "query" => $this->db->last_query(),
                                    "status" => false
                                );
                            }
                        } else {
                            $r = array(
                                'message' => $this->db->_error_message(),
                                "query" => $this->db->last_query(),
                                "status" => false
                            );
                        }
                    } else {
                        $r = array(
                            'message' => $this->db->_error_message(),
                            "query" => $this->db->last_query(),
                            "status" => false
                        );
                    }
                } else {
                    $r = array(
                        'message' => $this->db->_error_message(),
                        "query" => $this->db->last_query(),
                        "status" => false
                    );
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $this->send_sms_wallet($smsData);
                    $r = array(
                        'message' => 'Sucessfully updated',
                        "status" => TRUE,
                        "query" => $this->db->last_query(),
                    );
                } else {
                    $this->db->trans_rollback();
                    $r = array(
                        'message' => 'Something went worng',
                        "status" => false,
                        "query" => $this->db->last_query(),
                    );
                }
                return $r;
            }
        }
        $r = array(
            'message' => 'No records found',
            "status" => false
        );
        return $r;
    }
    /*function insertWalletTrans($tran){
      if($tran){
         if($tran['wallet_balance_type']==1) // wallet_balance_type : 1 -> amount
         {
            $redeemed_amt = (($tran['redeemed_amount']/$tran['wallet_amt_per_points'])*$tran['wallet_points']);
         }else{
             $redeemed_amt = $tran['redeemed_amount'];
         }
          $transDetailData = array(	
                                    "amount" 		 => $tran['amount'],
                                    "date_add"       => date('Y-m-d H:i:s'),
                                    "remark" 		 => 'Debited for purchase plan payment',
                                    'trans_points' 	 => $redeemed_amt
                                ); 
          if($tran['walletIntegration'] == 0){ // 0 - No integration, 1 - Req integration as like SSS
              $transDetailData['trans_type'] = 2;
            $updwallet = $this->payment_modal->updwallet($transDetailData,$tran['mobile']);
            $r = array("status" => $updwallet);
            return $r;
        }else{
                  $cat_code = 'SS';
                  $wal_cat_settings = $this->payment_modal->getWcategorySettings($cat_code);
                  // Begin the transaction
                  $this->db->trans_begin();
               if($wal_cat_settings){
                      $transData = array(	
                                    "record_to" 	 =>  1, //1 - offline , 2 - online
                                    "entry_date" 	 =>  date('Y-m-d H:i:s'), 
                                    "bill_no" 	 	 =>  $tran['txnid'],
                                    "id_branch" 	 =>  $tran['branch'],
                                    "trans_type"     =>  2,
                                    "mobile" 	 	 =>  $tran['mobile'],
                                    "is_transferred" =>  'N',
                                    "is_modified"    =>  0,
                                    "date_add"       =>  date('Y-m-d H:i:s'), 
                                    "record_type"	 =>  2// 1 - offline , 2 - online
                                );
                   $transDetailData['id_wcat_settings'] = $wal_cat_settings['id_wcat_settings'];
                      $transDetailData['category_code'] = $cat_code;
                    $t_status  = $this->payment_modal->insertData($transData,'inter_wallet_trans');
                       if($t_status['status']){
                           $transDetailData['id_inter_wallet_trans'] = $t_status['insertID'];
                           $td_status = $this->payment_modal->insertData($transDetailData,'inter_wallet_trans_detail');
                           if($td_status['status']){
                              $wallAccount = $this->payment_modal->getInterWalletCustomer($transData['mobile']); 
                              $allow = FALSE; 
                           if($wallAccount['status']){
                                   $upd_data = array(	
                                            "available_points"  => ($tran['available_points']-$redeemed_amt),
                                            "last_update"       => date('Y-m-d H:i:s'),
                                            "mobile" 	 	 	=> $tran['mobile'],
                                        );
                                   $w_status  = $this->payment_modal->updInterWalletAcc($upd_data);
                                   if($w_status){
                                    $allow = TRUE;
                                }
                           }else{
                                   $transDetailData['trans_type'] = 2;
                                   $transDetailData['bill_no'] = $tran['txnid'];
                                   $transDetailData['id_branch'] = $tran['branch'];
                                   $updwallet = $this->payment_modal->updwallet($transDetailData,$tran['mobile']);
                                   $msg = 'Thank you for using '.$this->comp['company_name'].' wallet. Redeemed '.number_format($redeemed_amt,'2','.','').' points for scheme payment.'; 	
                                if($updwallet){
                                    $smsData[] = array('mobile' => $tran['mobile'],
                                                       'message'=> $msg
                                    ); 
                                    $r = array('message' 	=> 'Sucessfully updated',
                                                "status" 	=> TRUE  
                                                );
                                }else{
                                    $r = array('message' 	=> $this->db->_error_message(),
                                                "query" 	=> $this->db->last_query(),
                                                "status" 	=> false
                                                );
                                }
                           }
                           if($allow){
                                   $transDetailData['trans_type'] = 2;
                                   $transDetailData['bill_no'] = $tran['txnid'];
                                   $transDetailData['id_branch'] = $tran['branch'];
                                   $updwallet = $this->payment_modal->updwallet($transDetailData,$tran['mobile']);
                                   $syncWalData = array(
                                                    "points"  	=> ($tran['available_points']-$redeemed_amt),
                                                    "mobile" 	=> $tran['mobile'],
                                                    );
                                if($tran['branch'] != ''){
                                    foreach($this->branch as $bran){
                                        if($tran['branch'] != $bran){
                                            $syncWalData['branch_'.$bran] = 0; // unsynced
                                        }else{
                                            $syncWalData['branch_'.$bran] = 1; // synced
                                        }								
                                    }
                                }else{
                                    $syncWalData['branch_1'] = 0;
                                }
                                $isExist = $this->payment_modal->getSyncWalletByMobile($tran['mobile']);
                                if($isExist){
                                    $syncWalData['last_update'] = date('Y-m-d H:i:s');
                                    $syncWalData['id_inter_sync_wallet'] = $isExist['id_inter_sync_wallet']; 
                                    $this->payment_modal->updateSyncWal($syncWalData);
                                }else{
                                    $syncWalData['date_add'] = date('Y-m-d H:i:s');
                                    $this->payment_modal->insertData($syncWalData,'inter_sync_wallet');
                                }
                            $msg = 'Thank you for purchasing at '.$this->comp['company_name'].' . Your Wallet Balance '.number_format($tran['available_points'],'2','.','').' points. Redeemed '.number_format($redeemed_amt,'2','.','').' points. New Wallet Balance '.number_format(($tran['available_points']-$redeemed_amt),'2','.','').' Points.'; 	
                                if($updwallet){
                                    $smsData[] = array('mobile' => $tran['mobile'],
                                                       'message'=> $msg
                                    );
                                }
                           }else{
                                $r = array('message' 	=> $this->db->_error_message(),
                                "query" 	=> $this->db->last_query(),
                                            "status" 	=> false
                                            );
                            }
                           }else{
                            $r = array('message' 	=> $this->db->_error_message(),
                            "query" 	=> $this->db->last_query(),
                                        "status" 	=> false
                                        );
                        }
                    }else{
                        $r = array('message' 	=> $this->db->_error_message(),
                        "query" 	=> $this->db->last_query(),
                                    "status" 	=> false
                                    );
                    }
               }else{
                       $r = array('message' 	=> $this->db->_error_message(),
                                "query" 	=> $this->db->last_query(),
                                "status" 	=> false
                                );
               } 
                if( $this->db->trans_status() === TRUE ){
                  $this->db->trans_commit();
                  $this->send_sms_wallet($smsData);
                  $r = array('message' 	=> 'Sucessfully updated',
                            "status" 	=> TRUE  
                            );
              }else{
                  $this->db->trans_rollback();
                   $r = array('message' 	=> 'Something went worng',
                            "status" 	=> false  
                            );
              }
               return $r;
           }
       }
       $r = array('message' 	=> 'No records found',
                    "status" 	=> false  
                    );
       return $r;
    } */
    function send_sms_wallet($data)
    {
        $serviceID = 17;
        $service = $this->services_modal->checkService($serviceID);
        $mobile = $data['mobile'];
        $message = $data['message'];
        if ($service['sms'] == 1) {
            $this->load->model('sms_model');
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
        return TRUE;
    }
    /*  function getNotifications_get(){ 
        $model = self::MOD_MOB; 
        $data = array();
        if($this->get('id_sent_noti') != '')
        {
            $this->$model->inActiveNotifications($this->get('id_sent_noti'));
            //echo $this->db->last_query();exit;
        }
        if($this->get('cusID') != ''){
            $data = $this->$model->getNotifications($this->get('cusID'),$this->get('lastNotiID'));
        }
        $this->response($data,200);
    }  */
    function getNotifications_get()
    {
        $model = self::MOD_MOB;
        $data = array();
        if ($this->get('cusID') != '' && $this->get('id_sent_noti') > 0) {
            // echo '1';exit;
            $delArray = array(
                'id_notification' => $this->get('id_sent_noti'),
                'id_customer' => $this->get('cusID'),
                'date_add' => date('Y-m-d H:i:s'),
            );
            $del = $this->payment_modal->insertData($delArray, 'delete_notifications');
        } else if ($this->get('cusID') != '' && $this->get('id_sent_noti') != '' && $this->get('id_sent_noti') == 0) {
            // echo '2';exit;
            $data = $this->$model->getNotifications($this->get('cusID'), $this->get('lastNotiID'));
            foreach ($data as $noti) {
                // print_r($noti);exit;
                $delArray = array(
                    'id_notification' => $noti['id_sent_noti'],
                    'id_customer' => $this->get('cusID'),
                    'date_add' => date('Y-m-d H:i:s'),
                );
                $del = $this->payment_modal->insertData($delArray, 'delete_notifications');
            }
        }
        if ($this->get('cusID') != '') {
            $data = $this->$model->getNotifications($this->get('cusID'), $this->get('lastNotiID'));
        }
        $this->response($data, 200);
    }
    function ajax_getproducts_post()
    {
        $model = self::MOD_MOB;
        $id_product = $this->input->post('id_product');
        $data = $this->$model->get_products($id_product);
        $this->response($data, 200);
    }
    function productenquriy_post()
    {
        $model = self::MOD_MOB;
        $payData = $this->get_values();
        $insData = array(
            'mobile' => $payData['mobile'],
            'first_name' => $payData['firstname'],
            'last_name' => $payData['lastname'],
            'email' => $payData['email'],
            'id_product' => $payData['productcode'],
            'date_add' => date('Y-m-d H:i:s'),
            'message' => $payData['message']
        );
        $result = $this->$model->insProduct_enquiry($insData);
        if ($result['status'] == true) {
            $to = $this->comp['email'];
            //$to = "karthik@vikashinfosolutions.com"; 
            //$bcc ="pavithra@vikashinfosolutions.com";	
            $bcc = "";
            $subject = "Reg - " . $this->comp['company_name'] . " customer enquiry";
            $insData['product_name'] = $payData['productname'];
            //print_r($insdata);exit;
            $message = $this->load->view('include/emailEnquiry', $insData, true);
            $sendEmail = $this->email_model->send_email($to, $subject, $message, $bcc, "");
            $this->response(array('status' => true, 'msg' => 'Thanks for your Enquiry'));
        } else {
            $this->response(array('status' => false, 'msg' => 'Please try after sometime'));
        }
    }
    public function getRegBranches_get()
    {
        $sd_playURL = "https://play.google.com/store/apps/details?id=com.sktm.gss.savingscheme&hl=en";
        $sd_itunes = "https://play.google.com/store/apps/details?id=com.sktm.gss.savingscheme&hl=en";
        $sd_msg = "Kindly install Swarna Dharaa app to register with selected branch.";
        $scm_playURL = "https://play.google.com/store/apps/details?id=com.scm.winchit";
        $scm_itunes = "https://itunes.apple.com/us/app/tktm/id1229733970?ls=1&mt=8";
        $scm_msg = "Kindly install SKTM Mayil app to register with selected branch.";
        $tktm_playURL = "https://play.google.com/store/apps/details?id=com.sktm.winchit";
        $tktm_itunes = "https://itunes.apple.com/us/app/tktm/id1229733970?ls=1&mt=8";
        $tktm_msg = "Kindly install TKTM app to register with selected branch.";
        $otherBranches = array(
            array(
                "name" => 'Karur',
                "playstore" => $tktm_playURL,
                "itunes" => $tktm_itunes,
                "msg" => $tktm_msg,
                "group" => "TKTM",
            ),
            array(
                "name" => 'Ooty',
                "playstore" => $tktm_playURL,
                "itunes" => $tktm_itunes,
                "msg" => $tktm_msg,
                "group" => "TKTM",
            ),
            array(
                "name" => 'Trichy',
                "playstore" => $tktm_playURL,
                "itunes" => $tktm_itunes,
                "msg" => $tktm_msg,
                "group" => "TKTM",
            ),
            array(
                "name" => 'Vellore',
                "playstore" => $tktm_playURL,
                "itunes" => $tktm_itunes,
                "msg" => $tktm_msg,
                "group" => "TKTM",
            ),
            array(
                "name" => 'Kumbakonam',
                "playstore" => $tktm_playURL,
                "itunes" => $tktm_itunes,
                "msg" => $tktm_msg,
                "group" => "TKTM",
            ),
            array(
                "name" => 'Thanjavur',
                "playstore" => $tktm_playURL,
                "itunes" => $tktm_itunes,
                "msg" => $tktm_msg,
                "group" => "TKTM",
            ),
            array(
                "name" => 'Pudukottai',
                "playstore" => $tktm_playURL,
                "itunes" => $tktm_itunes,
                "msg" => $tktm_msg,
                "group" => "TKTM",
            ),
            array(
                "name" => 'Villupuram',
                "playstore" => $scm_playURL,
                "itunes" => $scm_itunes,
                "msg" => $scm_msg,
                "group" => "SCM",
            ),
            array(
                "name" => 'Puducherry',
                "playstore" => $scm_playURL,
                "itunes" => $scm_itunes,
                "msg" => $scm_msg,
                "group" => "SCM",
            ),
            array(
                "name" => 'Chennai - Chrompet',
                "playstore" => $sd_playURL,
                "itunes" => $sd_itunes,
                "msg" => $sd_msg,
                "group" => "SD",
            ),
            array(
                "name" => 'Chennai - Velachery',
                "playstore" => $sd_playURL,
                "itunes" => $sd_itunes,
                "msg" => $sd_msg,
                "group" => "SD",
            ),
            array(
                "name" => 'Covai',
                "playstore" => $sd_playURL,
                "itunes" => $sd_itunes,
                "msg" => $sd_msg,
                "group" => "SD",
            ),
            array(
                "name" => 'Covai - Vivagaa',
                "playstore" => $sd_playURL,
                "itunes" => $sd_itunes,
                "msg" => $sd_msg,
                "group" => "SD",
            ),
            array(
                "name" => 'Erode',
                "playstore" => $sd_playURL,
                "itunes" => $sd_itunes,
                "msg" => $sd_msg,
                "group" => "SD",
            ),
            array(
                "name" => 'HYD - Kukatpally',
                "playstore" => $sd_playURL,
                "itunes" => $sd_itunes,
                "msg" => $sd_msg,
                "group" => "SD",
            ),
            array(
                "name" => 'HYD - Mehdipatnam',
                "playstore" => $sd_playURL,
                "itunes" => $sd_itunes,
                "msg" => $sd_msg,
                "group" => "SD",
            ),
            array(
                "name" => 'Madurai',
                "playstore" => $sd_playURL,
                "itunes" => $sd_itunes,
                "msg" => $sd_msg,
                "group" => "SD",
            ),
            array(
                "name" => 'Namakkal',
                "playstore" => $sd_playURL,
                "itunes" => $sd_itunes,
                "msg" => $sd_msg,
                "group" => "SD",
            ),
            array(
                "name" => 'Rajapalayam',
                "playstore" => $sd_playURL,
                "itunes" => $sd_itunes,
                "msg" => $sd_msg,
                "group" => "SD",
            ),
            array(
                "name" => 'Salem',
                "playstore" => $sd_playURL,
                "itunes" => $sd_itunes,
                "msg" => $sd_msg,
                "group" => "SD",
            ),
            array(
                "name" => 'Thiruvallur',
                "playstore" => $sd_playURL,
                "itunes" => $sd_itunes,
                "msg" => $sd_msg,
                "group" => "SD",
            ),
            array(
                "name" => 'Udumalaipet',
                "playstore" => $sd_playURL,
                "itunes" => $sd_itunes,
                "msg" => $sd_msg,
                "group" => "SD",
            ),
        );
        $model = self::MOD_MOB;
        $data['branch'] = $this->get_branch();
        $data['otherbr'] = $otherBranches;
        echo json_encode($data);
    }
    function giftIssuedByCusId_get()
    {
        $model = self::MOD_MOB;
        $id_customer = $this->get('id_customer');
        $result = $this->$model->get_giftsAccwise($id_customer);
        $this->response($result, 200);
    }
    function giftIssuedByAcId_get()
    {
        $model = self::MOD_MOB;
        $id_scheme_ac = $this->get('id_scheme_ac');
        $result = $this->$model->get_giftsListAccwise($id_scheme_ac);
        $this->response($result, 200);
    }
    function getModules_get()
    {
        $model = self::MOD_MOB;
        $result['modules'] = $this->$model->getModules();
        $this->response($result, 200);
    }
    // complaint listing
    function custComplaints_get()
    {
        $model = self::MOD_MOB;
        $result = $this->$model->get_custComplaints($this->get('id_customer'));
        $this->response($result, 200);
    }
    function custComplaintStatus_get()
    {
        $model = self::MOD_MOB;
        $result = $this->$model->get_custComplaintStatus($this->get('id_enquiry'));
        $this->response($result, 200);
    }
    // DTH listing
    function custDTHRequests_get()
    {
        $model = self::MOD_MOB;
        $result = $this->$model->get_custDTHRequests($this->get('id_customer'));
        $this->response($result, 200);
    }
    function custDTHStatus_get()
    {
        $model = self::MOD_MOB;
        $result = $this->$model->get_custComplaintStatus($this->get('id_enquiry'));
        $this->response($result, 200);
    }
    function rate_history_post()
    {
        $model = self::MOD_MOB;
        $postData = $this->get_values();
        $result = array();
        $LMX_ratehistory = array();
        $EJ_ratehistory = array();
        $from_date = $postData['from'];
        $to_date = $postData['to'];
        if (strtotime($from_date) < strtotime('01/14/2020') && strtotime($to_date) < strtotime('01/14/2020')) {
            $EJ_ratehistory = $this->$model->ej_rate_history($postData['id_branch'], $postData['from'], $postData['to'], $postData['branch_settings'], 'ej');
        } elseif (strtotime($from_date) < strtotime('01/14/2020') && strtotime($to_date) >= strtotime('01/14/2020')) {
            $LMX_ratehistory = $this->$model->ej_rate_history($postData['id_branch'], $postData['from'], $postData['to'], $postData['branch_settings'], 'lmx');
            $EJ_ratehistory = $this->$model->ej_rate_history($postData['id_branch'], $postData['from'], $postData['to'], $postData['branch_settings'], 'ej');
        } elseif (strtotime($from_date) >= strtotime('01/14/2020')) {
            $LMX_ratehistory = $this->$model->ej_rate_history($postData['id_branch'], $postData['from'], $postData['to'], $postData['branch_settings'], 'lmx');
        }
        $goldrate_22ct = array();
        $silverrate_1gm = array();
        $platinum_1g = array();
        if (sizeof($EJ_ratehistory) > 0) {
            foreach ($EJ_ratehistory as $row) {
                if ($row['goldrate_22ct'] > 0) {
                    $goldrate_22ct[] = array(
                        'updatetime' => $row['updatetime'],
                        'goldrate_22ct' => $row['goldrate_22ct']
                    );
                }
            }
            foreach ($EJ_ratehistory as $row) {
                if ($row['silverrate_1gm'] > 0) {
                    $silverrate_1gm[] = array(
                        'silverrate_1gm' => $row['silverrate_1gm']
                    );
                }
            }
            foreach ($EJ_ratehistory as $row) {
                if ($row['platinum_1g'] > 0) {
                    $platinum_1g[] = array(
                        'platinum_1g' => $row['platinum_1g']
                    );
                }
            }
            $gold_rate_length = count($goldrate_22ct);
            for ($i = 0; $i < $gold_rate_length; $i++) {
                if (isset($platinum_1g[$i]['platinum_1g'])) {
                    $previous_platinum = $platinum_1g[$i]['platinum_1g'];
                }
                $result[$i]['updatetime'] = $goldrate_22ct[$i]['updatetime'];
                $result[$i]['goldrate_22ct'] = $goldrate_22ct[$i]['goldrate_22ct'];
                $result[$i]['silverrate_1gm'] = $silverrate_1gm[$i]['silverrate_1gm'];
                $result[$i]['platinum_1g'] = (isset($platinum_1g[$i]['platinum_1g']) ? $platinum_1g[$i]['platinum_1g'] : $previous_platinum);
            }
        }
        $response = array_merge($LMX_ratehistory, $result);
        //$result['q'] = $this->db->last_query();
        $this->response($response, 200);
    }
    function uploadAadhar_post()
    {
        $this->load->helper(array('form', 'url'));
        $data = json_decode(json_encode($arrdata), FALSE);
        $cus_id = $data->id_customer;
        $file_path = self::CUS_ADHAR_PATH;
        if (!is_dir($file_path)) {
            mkdir($file_path, 0777, TRUE);
        }
        $config['encrypt_name'] = FALSE;
        // set path to store uploaded files
        $config['upload_path'] = $file_path;
        // set allowed file types
        $config['allowed_types'] = 'pdf';
        // set upload limit, set 0 for no limit
        $config['max_size'] = 20000;
        // load upload library with custom config settings
        $this->load->library('upload', $config);
        $this->upload->initialize($config);
        // if upload failed , display errors
        if (!$this->upload->do_upload('file')) {
            //print_r($this->upload->display_errors());exit;.
            $res = array('status' => false, 'file_name' => null);
            $this->response($res, 200);
        } else {
            $result = $this->upload->data();
            $res = array('status' => true, 'file_name' => $result['file_name']);
            $this->response($res, 200);
        }
    }
    function insertkyc_post()
    {
        $kycData = $this->get_values();
        /*if(sizeof($kycData) == 0){ // Verify aadhaar without storing pdf in server
            $kycData = json_decode(json_encode($_POST), FALSE); 
        } */
        $model = self::MOD_MOB;
        $approval_type = "Manual";
        if ($kycData['kyc_type'] != '') {
            $insData = array(
                'id_customer' => (isset($kycData['id_customer']) ? $kycData['id_customer'] : NULL),
                'kyc_type' => (isset($kycData['kyc_type']) ? $kycData['kyc_type'] : NULL),
                'number' => (isset($kycData['number']) ? $kycData['number'] : NULL),
                'name' => (isset($kycData['name']) ? $kycData['name'] : NULL),
                'bank_ifsc' => (isset($kycData['bank_ifsc']) ? $kycData['bank_ifsc'] : NULL),
                'status' => 0,
                'emp_verified_by' => (isset($kycData['emp_verified_by']) ? $kycData['emp_verified_by'] : NULL),
                'date_add' => date('Y-m-d H:i:s'),
                'dob' => (isset($kycData['dob']) ? $kycData['dob'] : NULL),
                'bank_branch' => (isset($kycData['bank_branch']) ? $kycData['bank_branch'] : NULL),
                'type' => (isset($kycData['type']) ? $kycData['type'] : 1),
                'nominee_mobile' => (isset($kycData['mobile']) ? $kycData['mobile'] : NULL),
                'added_by' => 2
            );
            if ($kycData['kyc_type'] == 1) {
                if ($approval_type == "Auto") {
                    $response = $this->zoopapiCurl("verify-bank", array("Account" => $kycData['number'], "IFSC" => $kycData['bank_ifsc']));
                    if (isset($response->statusCode)) {
                        $data['kyc_status'] = 0;
                        $data['status'] = FALSE;
                        $data['msg'] = $response->message;
                    } else {
                        if ($response->transaction_status == 1) {
                            if ($response->data->Status == "VERIFIED") {
                                $insData['status'] = 2;
                                $insData['name'] = $response->data->BeneName;
                                $insData['verification_type'] = 2; // 1-Manual,2-Auto
                                $insData['added_by'] = 2;
                                $data = $this->$model->insert_kyc($insData);
                                if ($data['status'] == TRUE) {
                                    $this->load->model("mobileapi_model");
                                    $data['kyc_status'] = $this->mobileapi_model->checkCusKYC($insData['id_customer']);
                                    $data['msg'] = "Bank Account verified successfully";
                                }
                            } else {
                                $data['kyc_status'] = 0;
                                $data['status'] = FALSE;
                                $data['msg'] = "Invalid Bank Details ";
                            }
                        } elseif ($response->transaction_status == 2) {
                            $data['kyc_status'] = 0;
                            $data['status'] = FALSE;
                            if (isset($response->data->Remark)) {
                                $data['msg'] = $response->data->Remark;
                            } else {
                                $data['msg'] = $response->response_message;
                            }
                        }
                    }
                } else {
                    $insData['verification_type'] = 1; // 1-Manual,2-Auto
                    $insData['added_by'] = 2;
                    $data = $this->$model->insert_kyc($insData);
                    if ($data['status'] == TRUE) {
                        $this->load->model("mobileapi_model");
                        $data['kyc_status'] = $this->mobileapi_model->checkCusKYC($insData['id_customer']);
                        $data['msg'] = "Bank Account details submitted successfully. Verification in progress. Keep in touch with us. ";
                    } else {
                        $data['kyc_status'] = 0;
                        $data['status'] = FALSE;
                        $data['err'] = $this->db->_error_message();
                        $data['qry'] = $this->db->last_query();
                        $data['msg'] = "Unable to submit KYC. Kindly contact Administrator.";
                    }
                }
            } else if ($kycData['kyc_type'] == 2) {
                if ($approval_type == "Auto") {
                    $response = $this->zoopapiCurl("pan", array("pan" => $insData['number']));
                    if (isset($response->statusCode)) {
                        $data['kyc_status'] = 0;
                        $data['status'] = FALSE;
                        $data['msg'] = $response->message;
                    } elseif ($response->response_code == 1) {
                        $res = $response->data[0];
                        if ($res->pan_status == "VALID") {
                            $insData['name'] = $res->first_name . ' ' . $res->last_name;
                            $insData['status'] = 2;
                            $insData['added_by'] = 2;
                            $insData['verification_type'] = 2; // 1-Manual,2-Auto
                            $data = $this->$model->insert_kyc($insData);
                            if ($data['status'] == TRUE) {
                                $this->load->model("mobileapi_model");
                                $data['kyc_status'] = $this->mobileapi_model->checkCusKYC($insData['id_customer']);
                                $data['msg'] = "PAN Card verified successfully";
                            }
                        } else {
                            $data['status'] = FALSE;
                            $data['msg'] = "Invalid PAN Details ";
                        }
                    }
                } else {
                    $insData['verification_type'] = 1; // 1-Manual,2-Auto
                    $panBase64 = '';
                    if (isset($kycData['file_name']) && $kycData['file_name'] != '') {
                        $panBase64 = $kycData['file_name'];
                    } else if (isset($kycData['pan_img']) && $kycData['pan_img'] != '') {
                        $panBase64 = $kycData['pan_img'];
                    } else if (isset($kycData['pan_image']) && $kycData['pan_image'] != '') {
                        $panBase64 = $kycData['pan_image'];
                    }

                    if ($panBase64 != '') {
                        $base64String = $panBase64;
                        if (strpos($base64String, ';base64,') !== false) {
                            $parts = explode(";base64,", $base64String);
                    $addressimagebase64 = base64_decode($parts[1]);
                        } else {
                            $addressimagebase64 = base64_decode($base64String);
                        }
                        
                    $file_path = self::KYC_PAN_PATH . "" . $kycData['id_customer'];
                    if (!is_dir($file_path)) {
                        mkdir($file_path, 0777, TRUE);
                    }
                        $file_type = $this->get_image_typ_from_base64($panBase64);
                    $path = self::KYC_PAN_PATH . "" . $kycData['id_customer'] . "/pan." . $file_type;
                        
                    file_put_contents($path, $addressimagebase64);
                        $insData['img_url'] = base_url() . "" . $path;
                    }
                    $insData['added_by'] = 2;
                    $data = $this->$model->insert_kyc($insData);
                    $this->$model->update_customer(array('pan' => $insData['number']), $kycData['id_customer']);
                    if ($data['status'] == TRUE) {
                        $this->load->model("mobileapi_model");
                        $data['kyc_status'] = $this->mobileapi_model->checkCusKYC($insData['id_customer']);
                        $data['msg'] = "PAN Card details submitted successfully. Verification in progress. Keep in touch with us. ";
                    } else {
                        $data['kyc_status'] = 0;
                        $data['status'] = FALSE;
                        $data['msg'] = "Invalid PAN Card Details ";
                    }
                }
            } else if ($kycData['kyc_type'] == 3) // Aadhar
            {
                if ($approval_type == "Auto") {
                    $response = $this->zoopapiCurl("extract-aadhaar-data", array("mode" => "pdf", "file" => $b64Doc, "password" => $kycData['pdf_password'], "purpose" => 'For Purchase Plan KYC verification', "request_consent" => 'Y'));
                    //	var_dump($response);
                    if (isset($response->statusCode)) {
                        $data['status'] = FALSE;
                        $data['msg'] = $response->message;
                        $data['kyc_status'] = 0;
                    } elseif ($response->transaction_status == 5) {
                        $res = $response->result->BasicInfo;
                        $date = str_replace('/', '-', $res->DOB);
                        $dob = date('Y-m-d', strtotime($date));
                        $insData['name'] = $res->Name;
                        $insData['number'] = $response->result->AadhaarInfo;
                        $insData['dob'] = $dob;
                        $insData['status'] = 2;
                        $insData['added_by'] = 2;
                        $insData['verification_type'] = 2; // 1-Manual,2-Auto
                        $data = $this->$model->insert_kyc($insData);
                        if ($data['status'] == TRUE) {
                            $this->load->model("mobileapi_model");
                            $data['kyc_status'] = $this->mobileapi_model->checkCusKYC($insData['id_customer']);
                            $data['msg'] = "Aadhaar Details verified successfully";
                        }
                    } else {
                        $data['kyc_status'] = 0;
                        $data['status'] = FALSE;
                        $data['msg'] = "There was some issue, please try after sometime ..";
                    }
                    unlink($file_path . '' . $kycData['file_name']);
                } else {
                    if (!isset($kycData['number']) || $kycData['number'] == '') {
                        if (isset($kycData['aadhar_no'])) $insData['number'] = $kycData['aadhar_no'];
                        else if (isset($kycData['adhar_no'])) $insData['number'] = $kycData['adhar_no'];
                        else if (isset($kycData['aadhaar_no'])) $insData['number'] = $kycData['aadhaar_no'];
                    }

                    $frontBase64 = '';
                    if (isset($kycData['file_name']) && $kycData['file_name'] != '') {
                        $frontBase64 = $kycData['file_name'];
                    } else if (isset($kycData['front_img']) && $kycData['front_img'] != '') {
                        $frontBase64 = $kycData['front_img'];
                    } else if (isset($kycData['front_image']) && $kycData['front_image'] != '') {
                        $frontBase64 = $kycData['front_image'];
                    } else if (isset($kycData['aadhar_front']) && $kycData['aadhar_front'] != '') {
                        $frontBase64 = $kycData['aadhar_front'];
                    }
                    
                    if ($frontBase64 != '') {
                        $base64String = $frontBase64;
                        if (strpos($base64String, ';base64,') !== false) {
                            $parts = explode(";base64,", $base64String);
                        $addressimagebase64 = base64_decode($parts[1]);
                        } else {
                            $addressimagebase64 = base64_decode($base64String);
                        }

                        $file_path = self::KYC_AADHAR_PATH . "" . $kycData['id_customer'];
                        if (!is_dir($file_path)) {
                            mkdir($file_path, 0777, TRUE);
                        }
                        $file_type = $this->get_image_typ_from_base64($frontBase64);
                        if ($insData['type'] == 1) {
                            $front_img_path = self::KYC_AADHAR_PATH . "" . $kycData['id_customer'] . "/aadhar_front." . $file_type;
                        } else if ($insData['type'] == 2) {
                            $front_img_path = self::KYC_AADHAR_PATH . "" . $kycData['id_customer'] . "/nominee_aadhar_front." . $file_type;
                        } else {
                            $front_img_path = self::KYC_AADHAR_PATH . "" . $kycData['id_customer'] . "/aadhar_front." . $file_type;
                        }
                        //print_r($path);exit;
                        if (isset($front_img_path)) {
                        file_put_contents($front_img_path, $addressimagebase64);
                            $insData['img_url'] = base_url() . "" . $front_img_path;
                    }
                    }

                    $backBase64 = '';
                    if (isset($kycData['back_img']) && $kycData['back_img'] != '') {
                        $backBase64 = $kycData['back_img'];
                    } else if (isset($kycData['back_image']) && $kycData['back_image'] != '') {
                        $backBase64 = $kycData['back_image'];
                    } else if (isset($kycData['aadhar_back']) && $kycData['aadhar_back'] != '') {
                        $backBase64 = $kycData['aadhar_back'];
                    }

                    if ($backBase64 != '') {
                        $base64String = $backBase64;
                        if (strpos($base64String, ';base64,') !== false) {
                            $parts = explode(";base64,", $base64String);
                        $addressimagebase64 = base64_decode($parts[1]);
                        } else {
                            $addressimagebase64 = base64_decode($base64String);
                        }

                        $file_path = self::KYC_AADHAR_PATH . "" . $kycData['id_customer'];
                        if (!is_dir($file_path)) {
                            mkdir($file_path, 0777, TRUE);
                        }
                        $file_type = $this->get_image_typ_from_base64($backBase64);
                        if ($insData['type'] == 1) {
                            $back_img_path = self::KYC_AADHAR_PATH . "" . $kycData['id_customer'] . "/aadhar_back." . $file_type;
                        } else if ($insData['type'] == 2) {
                            $back_img_path = self::KYC_AADHAR_PATH . "" . $kycData['id_customer'] . "/nominee_aadhar_back." . $file_type;
                        } else {
                             $back_img_path = self::KYC_AADHAR_PATH . "" . $kycData['id_customer'] . "/aadhar_back." . $file_type;
                        }
                        //print_r($path);exit;
                        if (isset($back_img_path)) {
                        file_put_contents($back_img_path, $addressimagebase64);
                            $insData['back_img_url'] = base_url() . "" . $back_img_path;
                    }
                    }

                    if (isset($kycData['adhar_pdf']) && $kycData['adhar_pdf'] != '') {
                        $base64String = $kycData['adhar_pdf'];
                        if (strpos($base64String, ';base64,') !== false) {
                            $parts = explode(";base64,", $base64String);
                        $addressimagebase64 = base64_decode($parts[1]);
                        } else {
                            $addressimagebase64 = base64_decode($base64String);
                        }

                        $file_path = self::KYC_AADHAR_PATH . "" . $kycData['id_customer'];
                        if (!is_dir($file_path)) {
                            mkdir($file_path, 0777, TRUE);
                        }
                        $file_type = $this->get_image_typ_from_base64($kycData['adhar_pdf']);
                        if ($insData['type'] == 1) {
                            $pdf_path = self::KYC_AADHAR_PATH . "" . $kycData['id_customer'] . "/cus_aadhar." . $file_type;
                        } else if ($insData['type'] == 2) {
                            $pdf_path = self::KYC_AADHAR_PATH . "" . $kycData['id_customer'] . "/nominee_aadhar." . $file_type;
                        } else {
                            $pdf_path = self::KYC_AADHAR_PATH . "" . $kycData['id_customer'] . "/cus_aadhar." . $file_type;
                        }
                        //print_r($path);exit;
                        if (isset($pdf_path)) {
                        file_put_contents($pdf_path, $addressimagebase64);
                            $insData['document_url'] = base_url() . "" . $pdf_path;
                        }
                    }
                    $insData['added_by'] = 2;
                    $data = $this->$model->insert_kyc($insData);
                    //echo $this->db->last_query();exit;
                    $this->$model->update_customer(array('aadharid' => $insData['number']), $kycData['id_customer']);
                    if ($data['status'] == TRUE) {
                        $this->load->model("mobileapi_model");
                        $data['kyc_status'] = $this->mobileapi_model->checkCusKYC($insData['id_customer']);
                        $data['msg'] = "Aadhaar details submitted successfully. Verification in progress. Keep in touch with us. ";
                    } else {
                        $data['kyc_status'] = 0;
                        $data['status'] = FALSE;
                        $data['msg'] = "Invalid Aadhaar Details ";
                    }
                }
            } else if ($kycData['kyc_type'] == 4) { // Driving Licence
                if ($approval_type == "Auto") {
                    /*$date = date_create($insData['dob']);
                    $dob = date_format($date,"d-m-Y");*/
                    $response = $this->zoopapiCurl("verify-dl-advance/v2", array("dl_no" => $insData['number'], "consent" => "Y", "consent_text" => "For Purchase Plan KYC verification"));
                    if (isset($response->statusCode)) {
                        $data['kyc_status'] = 0;
                        $data['status'] = FALSE;
                        $data['msg'] = $response->message;
                    } else {
                        if ($response->transaction_status == 1) {
                            if ($response->response_msg == "Success") {
                                $insData['status'] = 2;
                                $insData['number'] = $response->result->dlNumber;
                                $insData['dob'] = date("Y-m-d", strtotime($response->result->dob));
                                $insData['address'] = $response->result->address[0]->completeAddress;
                                $insData['verification_type'] = 2; // 1-Manual,2-Auto
                                $insData['added_by'] = 2;
                                $data = $this->$model->insert_kyc($insData);
                                $data['query'] = $this->db->last_query();
                                if ($data['status'] == TRUE) {
                                    $this->load->model("mobileapi_model");
                                    $data['kyc_status'] = $this->mobileapi_model->checkCusKYC($insData['id_customer']);
                                    $data['msg'] = "Driving Licence verified successfully";
                                }
                            } else {
                                $data['kyc_status'] = 0;
                                $data['status'] = FALSE;
                                $data['msg'] = "Invalid Driving Licence ";
                            }
                        } elseif ($response->transaction_status == 0) {
                            $data['kyc_status'] = 0;
                            $data['status'] = FALSE;
                            if (isset($response->error_message)) {
                                $data['msg'] = $response->data->error_message;
                            } else {
                                $data['msg'] = $response->response_msg;
                            }
                        }
                    }
                } else {
                    $insData['verification_type'] = 1; // 1-Manual,2-Auto
                    $insData['added_by'] = 2;
                    $data = $this->$model->insert_kyc($insData);
                    if ($data['status'] == TRUE) {
                        $this->load->model("mobileapi_model");
                        $data['kyc_status'] = $this->mobileapi_model->checkCusKYC($insData['id_customer']);
                        $data['msg'] = "Driving Licence details submitted successfully. Verification in progress. Keep in touch with us. ";
                    } else {
                        $data['kyc_status'] = 0;
                        $data['status'] = FALSE;
                        $data['msg'] = "Invalid Driving Licence Details ";
                    }
                }
            }
            $data['kyc_type_status'] = ($insData['status'] == 0 ? 'Pending' : ($insData['status'] == 2 ? 'Verified' : ($insData['status'] == 3 ? 'Rejected' : ($insData['status'] == 1 ? 'In Progress' : ''))));
            $data['color'] = ($insData['status'] == 0 ? 'medium' : ($insData['status'] == 2 ? 'success' : ($insData['status'] == 3 ? 'danger' : ($insData['status'] == 1 ? 'warning' : ''))));
            /*$data['qry'] = $this->db->last_query();
            $data['err'] = $this->db->_error_message();*/
            $this->response($data);
        } else {
            $this->response(array('status' => false, 'msg' => 'KYC type is empty', 'kyc_status' => 0));
        }
    }
    public function get_image_typ_from_base64($image_url)
    {
        $base64String = urldecode($image_url);
        if (strpos($base64String, ';base64,') !== false) {
        $parts_base64 = explode(";base64,", $base64String);
        $image_type = explode("/", $parts_base64[0]);
            return implode("", array_slice($image_type, 1)); // handles e.g. ["data:image", "jpeg"]
        }
        return 'jpg'; // Default extension if pure base64
    }
    /**
     * 	ZOOP API CURL Call
     * 	Documentation : https://docs.aadhaarapi.com/?version=latest
     * 
     * @return
     */
    function zoopapiCurl($api, $postData)
    {
        //	   print_r(json_encode($postData));
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->config->item('zoop_url') . "" . $api,
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
                "qt_agency_id: " . $this->config->item('agency_id'),
                "qt_api_key: " . $this->config->item('api_key')
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        //exit;
        if ($err) {
            echo "cURL Error #:" . $err;
            exit;
        } else {
            //   echo $response;exit;
            return json_decode($response);
        }
    }
    public function readKyc_get()
    {
        $model = self::MOD_MOB;
        $id_customer = $this->get('id_customer');
        $kycRecords = $this->$model->get_kycstatus($id_customer);
        
        $result['data'] = $kycRecords;
        $result['aadhaarlink'] = 'https://eaadhaar.uidai.gov.in/#/';
        $result['kyc_status'] = !empty($kycRecords) ? $kycRecords[0]['kyc_status'] : 0;
        
        // Add top-level image mappings for the primary/latest KYC record
        if (!empty($kycRecords)) {
            $primary = $kycRecords[0];
            $result['front_img_url'] = $primary['front_img_url'];
            $result['back_img_url'] = $primary['back_img_url'];
            $result['document_url'] = $primary['document_url'];
            $result['front_img'] = $primary['front_img_url'];
            $result['back_img'] = $primary['back_img_url'];
            
            // Specifically check for Aadhar if multiple exist
            foreach ($kycRecords as $rec) {
                if ($rec['kyc_type'] == 3) { // Aadhaar
                    $result['aadhar_front'] = $rec['front_img_url'];
                    $result['aadhar_back'] = $rec['back_img_url'];
                    $result['aadhar_img_front_url'] = $rec['front_img_url'];
                    $result['aadhar_img_back_url'] = $rec['back_img_url'];
                    break;
                }
            }
        } else {
            $result['front_img_url'] = null;
            $result['back_img_url'] = null;
            $result['document_url'] = null;
        }

        $this->response($result, 200);
    }
    // Store Locator Listing
    function getAllBranchDetail_get()
    {
        $model = self::MOD_MOB;
        // $result = $this->$model->branch_details();
        $result['stores'] = $this->$model->branch_details();
        $result['whatsapp_url'] = "";
        $result['insta_url'] = "";
        $result['fb_url'] = "";
        $result['youtube_url'] = "";
        $this->response($result, 200);
    }
    function testCURL_get()
    {
        $this->load->model('sms_model');
        $send = $this->sms_model->sendSMS_Nettyfish("7010198473", "Welcome to jewelone.", 'trans');
        var_dump($send);
    }
    function sch_enquiry_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $sch_data = array(
            'id_customer' => $data['id_customer'],
            'id_scheme' => $data['id_scheme'],
            'intresred_amt' => (isset($data['intresred_amt']) ? $data['intresred_amt'] : 0),
            'intrested_wgt' => (isset($data['intrested_wgt']) ? $data['intrested_wgt'] : 0),
            'message' => (isset($data['message']) ? $data['message'] : 0),
            'enquiry_date' => date('Y-m-d H:i:s'),
        );
        $customer = $this->$model->insert_sch_enquiry($sch_data);
        if ($customer['status'] == true) {
            $cus_data = $this->$model->get_customerProfile($data['id_customer']);
            $to = $this->comp['email'];
            $sch['type'] = 3;
            $sch['name'] = $cus_data['firstname'];
            $sch['amount'] = $data['intresred_amt'];
            $data['weight'] = $data['interseted_weight'] . ' ' . 'Grams';
            $cc = 'karthik@vikashinfosolutions.com';
            $data['company'] = $this->comp;
            $subject = "Reg.  " . $this->comp['company_name'] . " scheme Enquiry";
            $message = $this->load->view('include/emailScheme', $sch, true);
            $sendEmail = $this->email_model->send_email($to, $subject, $message, $cc, "");
        }
        $this->response($customer, 200);
    }
    function sync_existing_data($mobile, $id_customer, $id_branch)
    {
        $post = array('m' => $mobile, 'id' => $id_customer, 'br' => $id_branch);  //Array([m] => 7639711990 [id] => 6390 [br] => 2)
        if (!is_dir($this->log_dir . '/existing')) {
            mkdir($this->log_dir . '/existing', 0777, true);
        }
        $log_path = $this->log_dir . '/existing/' . date("Y-m-d") . '.txt';
        $allow_sync = false;
        $last_sync_time = $this->registration_model->getLastSyncTime($mobile);
        if (!empty($last_sync_time)) {
            $fifteen_min_earlier = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) - (15 * 60));
            if (strtotime($last_sync_time) <= strtotime($fifteen_min_earlier)) {
                $allow_sync = true;
            } else {
                $allow_sync = false;
            }
        } else {
            $allow_sync = true;
        }
        if ($allow_sync) {    //1
            $data['id_customer'] = $id_customer;
            $data['id_branch'] = $id_branch;
            $data['branch_code'] = ($id_branch > 0 ? $this->registration_model->getBranchCode($id_branch) : NULL);
            $data['branchWise'] = 0;
            $data['mobile'] = $mobile;
            $this->db->trans_begin();
            $this->registration_model->updateLastSyncTime($id_customer);
            //  $data -> Array(    [id_customer] => 6390    [id_branch] => 2    [branch_code] => 40    [branchWise] => 0    [mobile] => 7639711990)
            $res = $this->registration_model->insExisAcByMobile($data);
            if (sizeof($res) > 0) {
                //$this->db->trans_begin();
                $payData = $this->registration_model->syncPayData($res);
                if (sizeof($payData['succeedIds']) > 0 || $payData['no_records'] > 0) {
                    $status = $this->registration_model->updateInterTableStatus($res, $payData['succeedIds']);
                    if ($status === TRUE && $this->db->trans_status() === TRUE) {
                        $this->db->trans_commit();
                        /*echo $this->db->_error_message();
                        echo $this->db->last_query();*/
                        $TESTRes = array("status" => "On ENTER", "e" => $this->db->_error_message(), "q" => $this->db->last_query(), "res" => $res, "data" => $data);
                        $logData = "\n" . date('d-m-Y H:i:s') . "\n API : mobile_api \n Response : " . json_encode($TESTRes, true);
                        file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
                        return array("status" => TRUE, "msg" => "Purchase Plan registered successfully");
                    } else {
                        $this->db->trans_rollback();
                        $response = array("status" => FALSE, "e" => $this->db->_error_message(), "q" => $this->db->last_query(), "msg" => "Error in updating intermediate tables");
                        $logData = "\n" . date('d-m-Y H:i:s') . "\n API : mobile_api \n Response : " . json_encode($response, true);
                        file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
                        return $response;
                    }
                } else {
                    $response = array("status" => FALSE, "e" => $this->db->_error_message(), "q" => $this->db->last_query(), "msg" => "Error in updating payment tables, kindly check payment data.");
                    $logData = "\n" . date('d-m-Y H:i:s') . "\n API : mobile_api \n Response : " . json_encode($response, true);
                    file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
                    $this->db->trans_rollback();
                    return $response;
                }
            } else {
                $response = array("status" => FALSE, "e" => $this->db->_error_message(), "q" => $this->db->last_query(), "msg" => "No records to update in scheme account tables");
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                } else {
                    echo $this->db->_error_message();
                    $this->db->trans_rollback();
                }
                $logData = "\n" . date('d-m-Y H:i:s') . "\n API : mobile_api \n Response : " . json_encode($response, true);
                file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
                return $response;
            }
        } else {
            $logData = "\n" . date('d-m-Y H:i:s') . "\n API : mobile_api \n sync called less than 15 min";
            file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
        }
    }
    // To Get Bearer Token for ERP api call
    public function getBearerToken()
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->config->item('erp_baseURL') . "loginRequest",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "UserName=" . $this->config->item('ejUserName') . "&Password=" . $this->config->item('ejPassword') . "&grant_type=password",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "cache-control: no-cache"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
            return false;
        } else {
            $result = json_decode($response);
            return $result->access_token;
        }
    }
    public function get_settings()
    {
        $sql = "select * from chit_settings";
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    function verifyRateFixOTP_post()
    {
        $model = self::MOD_MOB;
        $result = "";
        $data = $this->get_values();
        //print_r($data);exit;
        $otp_time = date('Y-m-d H:i:s');
        if ($data['sysotp'] == $data['userotp']) {
            //echo 'Before Bearer '.date('d-m-Y H:i:s');
            //echo 'After Bearer '.date('d-m-Y H:i:s');exit;
            //  print_r($otp_time);exit;
            if ($otp_time <= date('Y-m-d H:i:s', strtotime($data['last_otp_expiry']))) {
                if ($this->config->item("integrationType") == 0) {
                    $settings = $this->get_settings();
                    $rate = $this->$model->getGold22ct($settings['is_branchwise_rate'], $data['id_branch']);
                    $isRateFixed = $this->$model->isRateFixed($data['id_sch_ac']);
                    if ($isRateFixed['status'] == 0) {
                        $metal_wgt = $isRateFixed['firstPayment_amt'] / $rate;
                        $updData = array(
                            "fixed_wgt" => $metal_wgt,
                            "fixed_metal_rate" => $rate,
                            "rate_fixed_in" => 2,
                            "fixed_rate_on" => date("Y-m-d H:i:s")
                        );
                        $status = $this->$model->updFixedRate($updData, $data['id_sch_ac']);
                        if ($status) {
                            $result = array('is_valid' => TRUE, 'status' => TRUE, 'msg' => 'Rate Fixed successfully');
                        } else {
                            $result = array('is_valid' => TRUE, 'status' => FALSE, 'msg' => 'Sorry for the inconvenience, we are unable to fix rate at the moment. Try again later..');
                        }
                    } else {
                        $result = array('is_valid' => TRUE, 'status' => TRUE, 'msg' => 'Rate already fixed !!');
                    }
                } else {
                    $bearer = $this->getBearerToken();
                    if ($bearer) {
                        $rate = $this->$model->getGold22ct(1, $data['id_branch']);
                        if ($rate != 0) {
                            $postData = array((array("SchemeAccountNo" => trim($data["sch_ac_no"]), "MetalRate" => $rate, "FixRequestFrom" => 2)));
                            $curl = curl_init();
                            curl_setopt_array($curl, array(
                                CURLOPT_URL => $this->config->item('erp_baseURL') . "RateFixing",
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 300,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "POST",
                                CURLOPT_POSTFIELDS => json_encode($postData),
                                CURLOPT_HTTPHEADER => array(
                                    //"Authorization: Basic ".$this->config->item('erpAuthKey'),
                                    "Authorization: Bearer " . $bearer,
                                    "Content-Type: application/json",
                                    "cache-control: no-cache"
                                ),
                            ));
                            $response = curl_exec($curl);
                            $err = curl_error($curl);
                            curl_close($curl);
                            if ($err) {
                                echo "cURL Error #:" . $err;
                                $result = array('is_valid' => TRUE, 'status' => FALSE, 'msg' => 'Sorry for the inconvenience, we are unable to fix rate at the moment. Try again later.');
                            } else {
                                $res = json_decode($response);
                                if (gettype($res) == "array") {
                                    if ($res[0]->Flag == TRUE) {
                                        $updData = array(
                                            "fixed_wgt" => $res[0]->BookedWeight,
                                            "fixed_metal_rate" => $res[0]->MetalRate,
                                            "rate_fixed_in" => $res[0]->FixRequestFrom,
                                            "fixed_rate_on" => date("Y-m-d H:i:s", strtotime($res[0]->BookedDate))
                                        );
                                        $status = $this->$model->updFixedRate($updData, $data['id_sch_ac']);
                                        if ($status) {
                                            $result = array('is_valid' => TRUE, 'status' => TRUE, 'msg' => $res[0]->Status);
                                        } else {
                                            $result = array('is_valid' => TRUE, 'status' => FALSE, 'msg' => 'Sorry for the inconvenience, we are unable to fix rate at the moment. Try again later..');
                                        }
                                    } else {
                                        $result = array('is_valid' => TRUE, 'status' => FALSE, 'msg' => $res[0]->Status);
                                    }
                                } else {
                                    $result = array('is_valid' => TRUE, 'status' => FALSE, 'msg' => 'Sorry for the inconvenience, we are unable to fix rate at the moment. Try again later...' . $response);
                                }
                            }
                        } else {
                            $result = array('is_valid' => FALSE, 'status' => FALSE, 'msg' => 'Sorry for the inconvenience, we are unable to fix rate at the moment. Try again later....');
                        }
                    } else {
                        $result = array('is_valid' => FALSE, 'status' => FALSE, 'msg' => 'Sorry we are unable to fix rate at the moment. Try again later....');
                    }
                }
            } else {
                $result = array('is_valid' => FALSE, 'status' => FALSE, 'msg' => 'Your OTP expired');
            }
        } else {
            $result = array('is_valid' => FALSE, 'status' => FALSE, 'msg' => 'Your OTP is Invalid');
        }
        $this->response($result, 200);
    }
    function getMyGifts_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $result = $this->$model->getMyGifts($data['id_customer'], $data['mobile']);
        $this->response($result, 200);
    }
    function getGiftedCards_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $result = $this->$model->getGiftedCards($data['id_customer'], $data['mobile']);
        $this->response($result, 200);
    }
    function getGiftcardstatus_get()
    { //gift card payment status
        $model = self::MOD_MOB;
        $result = $this->$model->getGiftcardstatus($this->get('id_customer'));
        $this->response($result, 200);
    }
    function terms_and_conditions_get()
    {
        $model = self::MOD_MOB;
        $result[] = $this->$model->get_terms_and_conditions();
        $result['general_terms'] = "For enrolling online saving plan payment system, a customer should have valid mobile number
        Installments, Duration, Benefits and Terms would be varied in accordance with the plan. To know about each plans kindly visit respective sections
        Online payment status would be updated to the customer, once settlement done from payment gateway and such delay varies based on payment mode and concern banks
        Online payment system users can get the membership card at showroom
        On completion of plan, customer can purchase Jewellery at showroom only
        The company reserves the right to alter, amend, add or delete part or whole of the privileges of the plan without prior notice";
        $result['about'] = " Every jewel has a tale to tell, so does the jeweller. Leading forefront in the heart of Madurai’s gold bazaar is our Jewelry brand offering fine, contemporary gold jewellery and silverware. Our brand builds on a legacy of over 3 decades of craftmanship with a beautifully designed huge collection of gold and diamond ornaments. Our entire family is passionate about preserving our traditional gold business. Our mission is to provide a personalised experience (or service) to every single customer. Antique and vintage designs are our speciality with over hundreds of on-trend jewels ready to enthral you at first sight.
        Our quality standards are etched in stone. Every single piece of our jewellery conforms with the Indian Standards of gold 916 BIS HALLMARK. 92.6% Sterling silverware and jewellery are also our trademarks. Certified, branded and guaranteed Diamond jewellery that is so vogue is part of our exquisite diamond collection. Our valuations are fair, on the spot and billing is transparent.
        The services we offer match our brand’s reputation by creating incredible experiences like video calling and smart car parking. Bringing you the latest trends from India’s certified best vendors to satiate your desire for fine gold is our greatest strength. We are located in 3 branches in Madurai and currently expanding as an effort to serve you better and to reach out to more jewel lovers out there.";
        $this->response($result, 200);
    }
    function getMatchingVillage_get()
    {
        $model = self::MOD_MOB;
        $villages = $this->$model->getMatchingVillage($this->get('query'));
        $this->response($villages, 200);
    }
    /* Video Shopping Appointment - STARTS */
    function fetchAvailableSlots_get()
    {
        $result = [];
        $this->load->model('vs_model');
        $result['slots'] = $this->vs_model->getAvailableSlots();
        if (sizeof($result['slots']) == 0) {
            $result['status'] = FALSE;
            $result['msg'] = "Sorry!! Currently no appointment slots available..";
        } else {
            $result['status'] = TRUE;
            $result['calOptions'] = array(
                "defaultDate" => date("Y-m-d"),
                "minDate" => date('Y-m-d', strtotime("-1 days")),
                "maxDate" => date('Y-m-d', strtotime("+60 days")),
                "disabledDates" => [],
            );
            $result['msg'] = "";
        }
        $this->response($result, 200);
    }
    function fetchApptBookings_post()
    {
        $this->load->model('vs_model');
        $data = $this->get_values();
        $result = $this->vs_model->getUserVSAppts($data['mobile']);
        $this->response($result, 200);
    }
    function fetchApptBookDetail_post()
    {
        $this->load->model('vs_model');
        $data = $this->get_values();
        $result = $this->vs_model->getApptDetail($data['id_appt_request']);
        $this->response($result, 200);
    }
    function bookVSAppt_post()
    {
        $this->load->model('vs_model');
        $data = $this->get_values();
        $settings = $this->vs_model->getSettings();
        $msg = "";
        $result = array('status' => FALSE, 'msg' => "");
        $insData = array(
            'name' => $data['name'],
            'email' => (isset($data['email']) ? $data['email'] : NULL),
            'mobile' => (isset($data['mobile']) ? $data['mobile'] : NULL),
            'location' => (isset($data['location']) ? $data['location'] : NULL),
            'whats_app_no' => (isset($data['whats_app_no']) ? $data['whats_app_no'] : NULL),
            'pref_category' => (isset($data['pref_category']) ? $data['pref_category'] : NULL),
            'pref_item' => (isset($data['pref_item']) ? $data['pref_item'] : NULL),
            'preferred_slot' => (isset($data['preferred_slot']) ? $data['preferred_slot'] : NULL),
            'description' => (isset($data['description']) ? $data['description'] : NULL),
            'created_on' => date('Y-m-d H:i:s')
        );
        $this->db->trans_begin();
        $alreadyRequested = $this->vs_model->isPrevRequested($data['preferred_slot'], $data['mobile']);
        if ($alreadyRequested) {
            $result = array('status' => FALSE, 'title' => "Booking Failed", 'msg' => "You have already requested this slot..");
        } else {
            $isAvail = $this->vs_model->isSlotAvailable($data['preferred_slot']);
            if ($isAvail) {
                if ($settings['appt_auto_assign'] == 1) {
                    $insData['status'] = 1;
                    $insData['alloted_slot'] = (isset($data['pref_slot']) ? $data['pref_slot'] : NULL);
                    $msg = 'Video Shopping Appointment has been placed successfully..';
                    $remark = 'Appointment Created';
                } else {
                    $msg = 'Your request for Video Shopping Appointment has been placed successfully.. We will contact you shortly.';
                    $remark = 'Appointment Requested';
                }
                $ins = $this->vs_model->insertData($insData, 'appt_request');
                $logdata = array(
                    "id_appt_request" => $ins['insertID'],
                    "status" => (isset($insData['status']) ? $insData['status'] = 1 : 0),
                    "event_date" => date('Y-m-d H:i:s'),
                    "id_employee" => NULL,
                    "remark" => $remark
                );
                $this->vs_model->insertData($logdata, 'appt_request_log');
                $result = array('status' => TRUE, 'title' => "Appointment Booked", 'msg' => $msg, "id_appt_request" => $ins['insertID']);
            } else {
                $result = array('status' => FALSE, 'title' => "Booking Failed", 'msg' => "Sorry!! Requested slot not available, please choose someother slot..");
            }
            if ($this->db->trans_status() === TRUE && $result['status'] == TRUE) {
                $this->db->trans_commit();
                /* Send Alert to Customer SMS,Email */
                $cusServData = $this->sms_model->SMS_dataByServCode('VS_REQST', $ins['insertID']);
                if ($cusServData['serv_sms'] == 1 && isset($data['mobile']) && $data['mobile'] != '') {
                    if ($this->config->item('sms_gateway') == '1') {
                        $this->sms_model->sendSMS_MSG91($data['mobile'], $cusServData['message'], '', $cusServData['dlt_te_id']);
                    } elseif ($this->config->item('sms_gateway') == '2') {
                        $this->sms_model->sendSMS_Nettyfish($data['mobile'], $cusServData['message'], 'trans');
                    } elseif ($this->config->item('sms_gateway') == '3') {
                        $this->sms_model->sendSMS_SpearUC($data['mobile'], $cusServData['message'], '', $cusServData['dlt_te_id']);
                    } elseif ($this->config->item('sms_gateway') == '4') {
                        $this->sms_model->sendSMS_Asterixt($data['mobile'], $cusServData['message'], '', $cusServData['dlt_te_id']);
                    } elseif ($this->config->item('sms_gateway') == '5') {
                        $this->sms_model->sendSMS_Qikberry($data['mobile'], $cusServData['message'], '', $cusServData['dlt_te_id']);
                    }
                }
                if ($cusServData['serv_email'] == 1 && isset($data['email']) && $data['email'] != '') {
                    $to = $data['email'];
                    $subject = "Reg - " . $this->comp['company_name'] . " video shopping appointment";
                    $data['mailData'] = $cusServData['cus_data'];
                    $data['type'] = 1;
                    $data['company_details'] = $this->comp;
                    $message = $this->load->view('include/vs_appt', $data, true);
                    $sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
                }
                /* Send Alert to Admin SMS,Email */
                if ($settings['vs_send_sms_to'] != '' && strlen($settings['vs_send_sms_to']) == 10) {
                    $adm_msg = "New VS appointment received.";
                    if ($this->config->item('sms_gateway') == '1') {
                        $this->sms_model->sendSMS_MSG91($settings['vs_send_sms_to'], $adm_msg, '', '');
                    } elseif ($this->config->item('sms_gateway') == '2') {
                        $this->sms_model->sendSMS_Nettyfish($settings['vs_send_sms_to'], $adm_msg, 'trans');
                    } elseif ($this->config->item('sms_gateway') == '3') {
                        $this->sms_model->sendSMS_SpearUC($settings['vs_send_sms_to'], $adm_msg, '', '');
                    } elseif ($this->config->item('sms_gateway') == '4') {
                        $this->sms_model->sendSMS_Asterixt($settings['vs_send_sms_to'], $adm_msg, '', '');
                    } elseif ($this->config->item('sms_gateway') == '5') {
                        $this->sms_model->sendSMS_Qikberry($settings['vs_send_sms_to'], $adm_msg, '', '');
                    }
                }
                if ($settings['vs_send_mail_to'] != '') {
                    $to = $settings['vs_send_mail_to'];
                    $subject = "Reg - " . $this->comp['company_name'] . " video shopping appointment";
                    $data['mailData'] = $cusServData['cus_data'];
                    $data['type'] = 1;
                    $data['company_details'] = $this->comp;
                    $message = $this->load->view('include/vs_appt', $data, true);
                    $sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
                }
            } else {
                $this->db->trans_rollback();
                $result = array('status' => FALSE, 'title' => "Booking Failed", 'msg' => "Unable to proceed your request. Please contact admin..");
            }
        }
        $this->response($result, 200);
    }
    function updVSFeedback_post()
    {
        $data = $this->get_values();
        $this->load->model('vs_model');
        $updData = array(
            'customer_feedback' => $data['customer_feedback'],
            'updated_on' => date('Y-m-d H:i:s'),
            'updated_by' => NULL,
            "status" => 4 // Closed
        );
        $this->db->trans_begin();
        $this->vs_model->updateData($updData, 'id_appt_request', $data['id_appt_request'], 'appt_request');
        $logdata = array(
            "id_appt_request" => $data['id_appt_request'],
            "status" => 4, // Closed
            "event_date" => date('Y-m-d H:i:s'),
            "id_employee" => NULL,
            "remark" => "Feedback Given."
        );
        $this->vs_model->insertData($logdata, 'appt_request_log');
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $result = array('status' => TRUE, 'msg' => "Feedback updated successfully..");
        } else {
            $this->db->trans_rollback();
            $result = array('status' => TRUE, 'msg' => "Unable to updated your feedback. Please contact admin..");
        }
        $this->response($result, 200);
    }
    function generateVsOTP_get()
    {
        $model = self::MOD_MOB;
        $otp = $this->$model->generateOTP();
        $expiry = date("d-m-Y H:i:s", strtotime($otp['expiry']));
        $msg = "Your OTP " . $otp['otp'] . " for " . $this->comp['company_name'] . " video shopping reg is valid till " . $expiry . "";
        if ($this->config->item('sms_gateway') == '1') {
            $this->sms_model->sendSMS_MSG91($this->get('mobile'), $msg, '', '');
        } elseif ($this->config->item('sms_gateway') == '2') {
            $this->sms_model->sendSMS_Nettyfish($this->get('mobile'), $msg, 'trans');
        } elseif ($this->config->item('sms_gateway') == '3') {
            $this->sms_model->sendSMS_SpearUC($this->get('mobile'), $msg, '', '');
        } elseif ($this->config->item('sms_gateway') == '4') {
            $this->sms_model->sendSMS_Asterixt($this->get('mobile'), $msg, '', '');
        } elseif ($this->config->item('sms_gateway') == '5') {
            $this->sms_model->sendSMS_Qikberry($this->get('mobile'), $msg, '', '');
        }
        $this->response($otp, 200);
    }
    /* Video Shopping Appointment - ENDS */
    function payDuesData_get()
    {
        $model = self::MOD_MOB;
        // Currency
        $result['currency'] = $this->$model->get_currency($this->get('id_branch'));
        // Weights
        $weights = array();
        $weights_data = $this->$model->get_weights();
        $metalrates = $result['currency']['metal_rates'];
        if (empty($weights_data)) {
            $result['weights'] = [];
        } else {
            foreach ($weights_data as $weight) {
                $rate = (float) $metalrates['goldrate_22ct'] * (float) $weight['weight'];
                $result['weights'][] = array(
                    'id_weight' => $weight['id_weight'],
                    'weight' => $weight['weight'],
                    'rate' => number_format($rate, 2, '.', '')
                );
            }
        }
        // Pending Dues
        if ($this->get('id_branch')) {
            if ($this->get('id_branch') != 'undefined') {
                $id_branch = $this->get('id_branch');
            } else {
                $id_branch = $this->$model->getCusBranch($this->get('id_customer'));
            }
        } else {
            $id_branch = $this->$model->getCusBranch($this->get('id_customer'));
        }
        // $this->load->model("commonapi_model");
        $schemeAcc = $this->$model->get_payment_details($this->get('id_customer'));
        if (!empty($schemeAcc)) {
            $result['chits'] = $schemeAcc['chits'];
        } else {
            $result['chits'] = [];
        }
        $result['overall_amount'] = (int) $schemeAcc['over_all_amount'];
        //	$result['chits'] = $this->array_sort($schemeAcc['chits'], 'allow_pay',SORT_DESC);
        $result['wallet_balance'] = $this->$model->wallet_balance($this->get('id_customer'));
        $customr_jonedbranch = $this->$model->getCusSchejoinedbranch($this->get('id_customer'));
        //Sync Existing Data					
        if ($this->config->item("integrationType") == 2 || $this->config->item("autoSyncExisting") == 1) {
            $syncData = $this->sync_existing_data($this->get('mobile'), $this->get('id_customer'), $id_branch);
        }
        // Get Branch Name and ID 
        if ($result['currency']['currency']['cost_center'] == 3) {
            if (count($result['chits']) > 0) {
                $result['branches'] = $this->$model->branchesData($customr_jonedbranch);
            }
            // $result['branches'] = $this->$model->branchesData(); 
        }
        $result['kyc'] = $this->$model->get_kycstatus($this->get('id_customer'));
        $cusLastPayData = $this->$model->getCusData($this->get('id_customer'));
        $result['customer'] = $cusLastPayData;
        if (!empty($cusLastPayData['last_payment_on'])) {
            $block_pay_mins = 0;
            $last_pay_sett = strtotime($cusLastPayData['last_payment_on']) + (60 * $block_pay_mins);
            //$tm = time() - (60*$result['currency']['currency']['block_pay_mins']);
            if ($last_pay_sett < time()) {
                $result['show_timer'] = false;
                $result['block_pay_mins'] = $block_pay_mins;
                $result['last_pay_sett'] = $last_pay_sett;
            } else {
                $result['show_timer'] = true;
                $sec = $last_pay_sett - time();
                $result['remaining_sec'] = $sec;
                $result['remaining_min'] = gmdate("i:s", $sec);
                $result['timer_desc'] = "You Can Retry The Payment after " . $block_pay_mins . " mins";
            }
        } else {
            $result['show_timer'] = false;
        }
        $filename = base_url() . 'api/language.txt';
        $result['langData'] = file_get_contents($filename);
        $this->response($result, 200);
    }
    // Cashfree SDK API's :: START
    function random_strings($length)
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($str_result), 0, $length);
    }
    function mobile_payment_post()
    {
        $postData = $this->get_values();
        $id_employee = isset($postData['id_employee'])?$postData['id_employee']:NULL;
        $payData = array(
            'firstname' => $postData['firstname'],
            'lastname' => $postData['lastname'],
            'phone' => $postData['phone'],
            'id_branch' => $postData['id_branch'],
            'amount' => $postData['amount'],
            'redeemed_amount' => $postData['redeemed_amount'],
            'productinfo' => $postData['productinfo'],
            'email' => $postData['email'],
            'gateway' => $postData['gateway'],
            'pg_code' => $postData['pg_code'],
            'pay_arr' => $postData['pay_arr'],
            'id_employee' => $id_employee,
            #Direct_Pay
            'paidBy_id_customer' => $postData['paidBy_id_customer'],
            'paidBy_mobile' => $postData['paidBy_mobile'],
        );
        if (!is_dir($this->log_dir . '/cashfree')) {
            mkdir($this->log_dir . '/cashfree', 0777, true);
        }
        $log_path = $this->log_dir . '/cashfree/mob_postData_' . date("Y-m-d") . '.txt';
        $ldata = "\n" . date('d-m-Y H:i:s') . " \n Mobile : " . json_encode($postData, true);
        file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
        $sch_payment = json_decode(urldecode($payData['pay_arr']));
        $sum_of_payArr_pay = array_sum(array_column($sch_payment, 'pay_amt'));
        //  echo '<pre>';print_r($sum_of_payArr_pay);exit;
        $sum_of_payArr_dis = array_sum(array_column($sch_payment, 'discount'));
        $amt = $postData['redeemed_amount'] + $payData['amount'] + $sum_of_payArr_dis;
        if (!empty($payData['phone']) && $amt > 0 && $amt == $sum_of_payArr_pay) {
            //get the values posted from mobile in array 
            $pay_flag = TRUE;
            $allow_flag = FALSE;
            $submit_pay = FALSE;
            $actAmount = (float) $payData['amount'];  // sum of all chit amount
            $cusData = $this->payment_modal->get_customer($payData['phone']);
            /*		   $pay_email = (!empty($cusData['email']) && $cusData['email'] != '') ? $cusData['email'] : $cusData['pg_email'];*/
            $gateway = (isset($payData['gateway']) ? ($payData['gateway'] == 'undefined' ? 1 : $payData['gateway']) : 1);
            if ($cusData['branch_settings'] == 1) {
                if ($cusData['is_branchwise_cus_reg'] == 1) {
                    $id_branch = $cusData['id_branch'];
                } else {
                    $id_branch = $pay->id_branch;
                    //$id_branch  = $branch['sch_join_branch'];
                }
            } else {
                $id_branch = NULL;
            }
            $paycred = $this->payment_gateway($id_branch, $payData['gateway']);
            //print_r($this->db->last_query());exit;
            $start_year = $this->payment_modal->get_financialYear();
            /*$pay_email = str_replace(' ', '', ((!empty($cusData['email']) && $cusData['email'] != '') ? $cusData['email'] : $cusData['pg_email'])) ;     //N
            $email_validation = $this->_email_validation($pay_email);
            if ($email_validation !== true){
                $pay_email = $cusData['pg_email'];
            }*/
            //customer mail validation....
            $email_regx = "/^([\w\.-]+)@([\w-]+)\.([\w]{2,8})(\.[\w]{2,8})?$/";
            if (preg_match($email_regx, $cusData['email']) && !empty($cusData['email'])) {
                $pay_email = str_replace(' ', '', ($cusData['email']));
            } else {
                $pay_email = str_replace(' ', '', ($cusData['pg_email']));
            }
            //check pay_flag
            if ($pay_flag) {
                //generate txnid
                $txnid = uniqid(time());
                $i = 1;
                // $sch_payment = json_decode($payData['pay_arr']);
                //	 $sch_payment =  json_decode(urldecode($payData['pay_arr'])) ;          // decode format for post method....  
                $udf1 = "";
                $udf2 = "";
                $udf3 = "";
                $productinfo = "";
                $this->db->trans_begin();
                $id_payments = [];
                foreach ($sch_payment as $pay) {
                    // ND - normal, PN - pending & normal, AN - adv & normal, PD pending due ,AD-adv due
                    $dueType = '';
                    if ($pay->due_type == 'PN') {
                        $dueType = ($i == 1 ? 'ND' : 'PD');
                    } else if ($pay->due_type == 'AN') {
                        $dueType = ($i == 1 ? 'ND' : 'AD');
                    } else {
                        $dueType = $pay->due_type;
                    }
                    if ($cusData['branch_settings'] == 1) {
                        if ($cusData['is_branchwise_cus_reg'] == 1) {
                            $id_branch = $cusData['id_branch'];
                        } else {
                            $id_branch = $pay->id_branch;
                            //$id_branch  = $branch['sch_join_branch'];
                        }
                    } else {
                        $id_branch = NULL;
                    }
                    //validate amount
                    $chit = $this->payment_modal->get_schemeByChit($pay->udf1);    //print_r($chit);exit;
                    if ($pay->scheme_type == 1) {
                        $metal_rate = $this->payment_modal->getMetalRate('');
                        $gold_rate = (float) $metal_rate['goldrate_22ct'];
                        $amt = $gold_rate * $pay->udf2;
                        $allow_flag = ($pay->amount >= $amt ? TRUE : FALSE);
                    } else {
                        $allow_flag = ($pay->amount >= $chit['amount'] ? TRUE : TRUE);
                    }
                    /* !!!! GST & METAL WEIGHT CALCULATION START FOR INCLUSIVE, EXCLUSIVE WITH DISCOUNT IF APPLICABLE !!!!  */
                    $pay_amt = $pay->amount;   //15000
                    $gst = $chit['gst'];  // 3 %    
                    $gst_type = $chit['gst_type'];    // 1 - inclusive type
                    if ($pay->due_type == 'GA' && $pay->allow_general_advance == 1) {
                        $actual_pay = $pay_amt;   //15000
                    } else {
                        $disc_amt = $pay->discount;   // 250
                        //Subtracting the dicount amount from the total payment amount. So the actual amount received from customer arrived...
                        $actual_pay = ($disc_amt > 0 ? ($pay_amt - $disc_amt) : $pay_amt);    // 15000 - 250 = 14750
                    }
                    if ($gst_type == 1) {
                        //FOR EXCLUSIVE GST TYPE...
                        $gst_amt = $actual_pay * ($gst / 100); // 14750 * (3/100)  = 442.5
                    } else {
                        //FOR INCLUSIVE GST TYPE...
                        $gst_amt = $actual_pay - ($actual_pay * (100 / (100 + $gst))); // 14750 - (14750 * (100 / (100 + 3)))  = 429.61
                    }
                    //METAL WEIGHT CALC STARTS...
                    $metal_wgt = '';
                    if ($chit['scheme_type'] == 2) //scheme_type is assigned to fix weight
                    {
                        $amt = ($chit['gst_type'] == 0 ? ($pay_amt - $gst_amt) : $pay_amt);
                        $data = array('amount' => $amt, 'metal_rate' => $pay->udf3);
                        // $metal_wgt = $this->amount_to_weight($data);
                          $settings = $this->get_settings();
                        $metal_wgt =formatMetalWeight($this->amount_to_weight($data));
                    } else if ($chit['scheme_type'] == 3) // scheme type
                    {
                        $amt = ($chit['gst_type'] == 0 ? ($pay_amt - $gst_amt) : $pay_amt);
                        // for  inclusive ---> 15000 - 429.61 = 14571
                        // for  exclusive ---> 15000 
                        if ($chit['flexible_sch_type'] == 3 || $chit['flexible_sch_type'] == 4 || $chit['flexible_sch_type'] == 7 || $chit['flexible_sch_type'] == 8 || ($chit['flexible_sch_type'] == 2 && $chit['wgt_convert'] != 2) || ($chit['flexible_sch_type'] == 5 && $chit['wgt_store_as'] == 1)) {
                            $data = array('amount' => $amt, 'metal_rate' => $pay->udf3);
                            // $metal_wgt = $this->amount_to_weight($data);
                            $metal_wgt = formatMetalWeight($this->amount_to_weight($data));
                            // for  inclusive ---> (14571 / 5685) = 2.563 grams
                            // for  exclusive ---> (15000 / 5685) = 2.638 grams
                        }
                    } else {
                        // $metal_wgt = (isset($pay->udf2) && $pay->udf2 != '' ? $pay->udf2 : 0.000);
                        $metal_wgt = (isset($pay->udf2) && $pay->udf2 != '' ? formatMetalWeight($pay->udf2) : 0);
                    }
                    //print_r($metal_wgt);exit;
                    /* GST & METAL WEIGHT CALCULATION END */
                    /*if($pay->scheme_type == 2 || ($pay->scheme_type == 3 && $chit['wgt_convert']==0)){
                            $data = array('metal_rate'=>$pay->udf3,'amount'=>$pay->udf4);
                         $metal_wgt = $this->amount_to_weight($data);
                    }
                    else{	
                         $metal_wgt = (isset($pay->udf2) && $pay->udf2 !='' ? $pay->udf2 : 0.000);
                    }*/
                    //rate fixed at the time of scheme join
                    if ($chit['one_time_premium'] == 1 && $chit['rate_fix_by'] == 0 && $chit['rate_select'] == 1) {
                        $metal_rate = $this->payment_modal->getMetalRate('');
                        $gold_rate = (float) $metal_rate['goldrate_22ct'];
                        if ($gold_rate != 0) {
                            $isRateFixed = $this->mobileapi_model->isRateFixed($pay->udf1);
                            if ($isRateFixed['status'] == 0) {
                                $updData = array(
                                    "fixed_wgt" => $pay->amount / $gold_rate,
                                    "firstPayment_amt" => $pay->amount,
                                    "fixed_metal_rate" => $gold_rate,
                                    "rate_fixed_in" => 2,
                                    "fixed_rate_on" => date("Y-m-d H:i:s")
                                );
                                //print_r($updData);exit; 
                                $ratestatus = $this->mobileapi_model->updFixedRate($updData, $pay->udf1);
                            } else {
                                $data = array('is_valid' => TRUE, 'success' => TRUE, 'msg' => "Rate already fixed !!");
                            }
                        }
                    }
                    //RHR starts....
                    //Update 1 ins date in acc start_date and calculate maturity date
                    if ($chit['paid_installments'] == 0) {
                        $start_date = array('start_date' => date('Y-m-d H:i:s'));
                        $updfirstPayamt = $this->payment_modal->update_account($start_date, $pay->udf1);
                        //Update maturity date if maturity type is fixed
                        $settings = $this->payment_modal->getSchemeData($pay->udf1);
                        if ($settings['maturity_type'] == 2 && $settings['maturity_days'] != null && $settings['maturity_days'] > 0) {
                            $start_date = $settings['start_date'];
                            $maturity_date = date('Y-m-d', strtotime($start_date . ' + ' . $settings['maturity_days'] . ' days'));
                            $maturity_data = array('maturity_date' => $settings['calc_maturity_date']);
                            $mat_sts = $this->payment_modal->updateAtData($maturity_data, 'id_scheme_account', $pay->udf1, 'scheme_account');
                        }
                    }
                    //RHR ends...            					
                    $custom_entry_date = $this->mobileapi_model->get_entrydate($id_branch);    //pr day close code update 19/06/2023			
                    // AB - digi_benefits & weight...
                    $dg_saved_benefit = 0;
                    $dg_benefit_value = 0;
                    $dg_benefit_type = 0;
                    $dg_saved_benefit_amt = 0;
                    $dg_other_benefit_wgt = 0;
                    $dg_other_benefit_amt = 0;
                     $settings = $this->get_settings();
                    if ($chit['is_digi'] == 1) {
                        $data = array('amount' => $pay->amount, 'metal_rate' => $pay->udf3);
                        $metal_wgt = formatMetalWeight($this->amount_to_weight($data));
                        if ($chit['interest'] == 1) {
                            $digi_account = $this->digigold_modal->digiGold_account($cusData['id_customer'], $chit['id_scheme']);
                            $digi_benefitData = $this->digigold_modal->get_digi_benefit($digi_account);
                            // echo("<pre>");
                            // print_r($digi_benefitData);
                            // print_r($chit['id_metal']);
                            if (sizeof($digi_benefitData) > 0) {
                                $is_other_benefit = 0;
                                foreach ($digi_benefitData as $digi_benefit) {
                                    if ($digi_benefit['commodity'] == $chit['id_metal']) {
                                        if ($digi_benefit['interest_type'] == 0) {
                                            $ben = $pay->amount * ($digi_benefit['interest_value'] / 100);
                                        } else {
                                            $ben = $digi_benefit['interest_value'];
                                        }
                                        $dg_saved_benefit_amt = $ben;
                                        $data = array('amount' => $dg_saved_benefit_amt, 'metal_rate' => $pay->udf3);
                                        $dg_saved_benefit = formatMetalWeight($this->amount_to_weight($data));
                                        $dg_benefit_value = $digi_benefit['interest_value'];
                                        $dg_benefit_type = $digi_benefit['interest_type']; // 0-percent, 1- amount
                                    } else {
                                        $other_metal_rate = $this->payment_modal->get_metalrate_by_branch($id_branch, $digi_benefit['commodity'], ''); //For branchwise rate
                                        if ($digi_benefit['interest_type'] == 0) {
                                            $dg_other_benefit_wgt = $metal_wgt * ($digi_benefit['interest_value'] / 100);
                                            $dg_other_benefit_amt = $dg_other_benefit_wgt * $other_metal_rate;
                                        } else {
                                            $dg_other_benefit_amt = $digi_benefit['interest_value'];
                                            $dg_other_benefit_wgt = $dg_other_benefit_amt * $other_metal_rate;
                                        }
                                        // $dg_saved_benefit = number_format(($dg_saved_benefit_amt / $generic['metal_rate']),3) ;
                                        $dg_other_benefit_wgt = formatMetalWeight(($dg_other_benefit_wgt * 1000) / 1000);
                                        //echo '<pre>';print_r($dg_saved_benefit);exit; 
                                    }
                                }
                            }
                        }
                    }
                    if ($allow_flag) {
                        //set insert data
                        //Rahul - 2/4/2025
                        //$ins_cycle = $this->mobileapi_model->get_due_date($pay->due_type, date('Y-m-d'),$pay->udf1); 
                        $insertData = array(
                            "id_scheme_account" => (isset($pay->udf1) ? $pay->udf1 : NULL),
                            "id_scheme" => (isset($chit['id_scheme']) ? $chit['id_scheme'] : 0),
                            "payment_amount" => (isset($pay->amount) ? $pay->amount + ($pay->discount == "" ? 0.00 : $pay->discount) : NULL),
                            "gst" => (isset($chit['gst']) ? $chit['gst'] : 0),
                            "gst_type" => (isset($chit['gst_type']) ? $chit['gst_type'] : 0),
                            "payment_type" => (isset($paycred) ? ($paycred['pg_name']) : ''),
                            "due_type" => $dueType,
                            "no_of_dues" => 1,
                            "actual_trans_amt" => (isset($actAmount) ? $actAmount : 0.00),
                            "date_payment" => date('Y-m-d H:i:s'),
                            "custom_entry_date" => ($custom_entry_date['edit_custom_entry_date'] == 1 ? $custom_entry_date['custom_entry_date'] : NULL),
                            "metal_rate" => (isset($pay->udf3) && $pay->udf3 != '' ? $pay->udf3 : 0.00),
                            "metal_weight" => !empty($metal_wgt) ? $metal_wgt : 0.000,
                            "id_transaction" => (isset($txnid) ? $txnid . '-' . $i : NULL),
                            "ref_trans_id" => (isset($txnid) ? $txnid : NULL), // to update pay status after trans complete.
                            "remark" => 'Paid for ' . $pay->udf5 . ($pay->udf5 > 1 ? 'months' : 'month'),
                            "added_by" => 2,
                            "add_charges" => (isset($pay->charge) ? $pay->charge : NULL),
                            "discountAmt" => ($pay->discount != "" ? $pay->discount : 0.00),
                            "payment_status" => $this->payment_status['pending'],
                            "id_payGateway" => (isset($gateway) ? $gateway : 1),
                            'id_branch' => $id_branch,
                            "receipt_year" => $start_year,
                            "pay_email" => $pay_email,
                            "redeemed_amount" => (isset($payData['redeemed_amount']) ? $payData['redeemed_amount'] : 0.00),
                            "id_employee" => $id_employee,
                            #Direct_Pay
                            'paidBy_id_customer' => $payData['paidBy_id_customer'],
                            'paidBy_mobile' => $payData['paidBy_mobile'],
                            //Rahul - 2/4/2025
                            /*'due_date' 		  =>  (isset($ins_cycle[0]['due_date_from'])?$ins_cycle[0]['due_date_from']: NULL),
                            'grace_date' 		  =>  (isset($ins_cycle[0]['grace_date'])?$ins_cycle[0]['grace_date']: NULL),
                            'installment' 		  =>  (isset($ins_cycle[0]['installment'])?$ins_cycle[0]['installment']: NULL),
							'is_limit_exceed' 		  =>  (isset($ins_cycle[0]['is_limit_exceed'])?$ins_cycle[0]['is_limit_exceed']: 0), */
                            "saved_benefits" => $dg_saved_benefit,   //digi scheme benefit weight
                            "benefit_value" => $dg_benefit_value,
                            "benefit_type" => $dg_benefit_type,
                            "saved_benefit_amt" => $dg_saved_benefit_amt,  //digi scheme benefit amount
                            'dg_other_benefit_wgt' => (!empty($dg_other_benefit_wgt) ? $dg_other_benefit_wgt : NULL),
                            'dg_other_benefit_amt' => (!empty($dg_other_benefit_amt) ? $dg_other_benefit_amt : NULL),
                        );
                        $udf1 = $udf1 . " " . $pay->udf1;
                        $udf2 = $udf2 . " " . $pay->udf2;
                        $udf3 = $udf3 . " " . $pay->udf3;
                        $productinfo = $productinfo . " " . $pay->chit_number;
                        //inserting pay_data before gateway process
                        //	echo "<pre>";print_r($insertData);echo "</pre>";exit;
                        if ($pay->due_type == 'GA' && $pay->allow_general_advance == 1) {
                            $payment = $this->payment_modal->add_GA_Payment($insertData);
                        } else {
                            $payment = $this->payment_modal->addPayment($insertData);
                        }
                        if (isset($payment['insertID'])) {
                            $id_payments[] = $payment['insertID'];
                        }
                        $i++;
                    }
                }
                if ($this->db->trans_status() === TRUE && $allow_flag) {
                    $this->db->trans_commit();
                    $submit_pay = TRUE;
                } else {
                    $this->db->trans_rollback();
                    $submit_pay = FALSE;
                }
                if ($submit_pay) {
                    if ($chit['is_digi'] == 0) {
                        $updData = array("last_payment_on" => date("Y-m-d H:i:s"));
                        $this->payment_modal->updData($updData, 'id_customer', $cusData['id_customer'], 'customer');
                    }
                    $secretKey = $paycred['param_1']; //Shared by Cashfree  
                    $appId = $paycred['param_3'];
                    //set data for hash generation
                    $data['pay'] = array(
                        'key' => $secretKey,
                        'txnid' => $txnid,
                        'amount' => (isset($payData['amount']) ? $payData['amount'] : ''),
                        'productinfo' => (isset($productinfo) ? $productinfo : ''),
                        'firstname' => (isset($cusData['firstname']) ? $cusData['firstname'] : ''),
                        'lastname' => (isset($cusData['lastname']) ? $cusData['lastname'] : ''),
                        'email' => (isset($cusData['email']) ? $cusData['email'] : ''),
                        'phone' => (isset($cusData['mobile']) ? $cusData['mobile'] : ''),
                        'address1' => (isset($cusData['address1']) ? $cusData['address1'] : ''),
                        'address2' => (isset($cusData['address2']) ? $cusData['address2'] : ''),
                        'city' => (isset($cusData['city']) ? $cusData['city'] : ''),
                        'state' => (isset($cusData['state']) ? $cusData['state'] : ''),
                        'country' => (isset($cusData['country']) ? $cusData['country'] : ''),
                        'zipcode' => (isset($cusData['pincode']) ? $cusData['pincode'] : ''),
                        'udf1' => (isset($udf1) ? $udf1 : ''),
                        'udf2' => (isset($udf2) ? $udf2 : ''),
                        'udf3' => (isset($udf3) ? $udf3 : ''),
                        'udf4' => (isset($payData['udf4']) ? $payData['udf4'] : ''),
                        'udf5' => (isset($payData['udf5']) ? $payData['udf5'] : '')
                    );
                    $gen_email = $this->random_strings(8) . '@gmail.com';
                    if ($payData['pg_code'] == 4) {   // cash free 
                        $data['cashfreepay'] = array(
                            'appId' => $appId,  //Shared by Cashfree
                            'orderId' => $txnid,
                            'orderAmount' => (string) $actAmount,
                            'orderCurrency' => 'INR',
                            'orderNote' => 'Purchase plan payment from mobile app',
                            'customerName' => (isset($payData['firstname']) ? (isset($payData['lastname']) ? $payData['firstname'] . ' ' . $payData['lastname'] : $payData['firstname']) : ''),
                            'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                            'customerPhone' => (isset($payData['phone']) ? $payData['phone'] : ''),
                            "notifyUrl" => $this->config->item('base_url') . "index.php/services/cashfreeStatusNotify/" . $payData['gateway'],
                            "stage" => $paycred['type'] == 0 ? "TEST" : 'PROD', //"TEST/PROD",
                            "id_customer" => $cusData['id_customer'],
                            "id_payments" => $id_payments
                        );
                        //$token = $this->generateCFtoken($txnid,$actAmount,$data['cashfreepay'],$paycred);
                        $token = $this->generateCashfreeSession($txnid, $actAmount, $data['cashfreepay'], $paycred);
                        $this->response($token, 200);
                    } else if ($payData['pg_code'] == 6) {   // Ippo Gateway
                        $data['ippo'] = array(
                            'publicKey' => $paycred['param_3'],  //Shared by Ippo gateway
                            'secretKey' => $paycred['param_1'], //Shared by Ippo gateway
                            'transaction' => $txnid,
                            'orderAmount' => $actAmount,
                            'orderCurrency' => 'INR',
                            'orderNote' => 'Online Money Transaction',
                            'customerName' => (isset($cusData['firstname']) ? (isset($cusData['lastname']) ? $cusData['firstname'] . ' ' . $cusData['lastname'] : $cusData['firstname']) : ''),
                            'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                            'customerPhone' => (isset($cusData['phone']) ? $cusData['phone'] : ''),
                            "returnUrl" => $this->config->item('base_url') . "index.php/paymt/ipporesponseURL"
                        );
                        $orderData = $this->generateOrderData($txnid, $actAmount, $data['ippo'], $paycred);
                        $this->response($orderData, 200);
                        // $this->load->view('ippo/ippo_payment',$data);
                    } else if ($payData['pg_code'] == 7) // Razor pay
                    {
                        $idPayments = implode(',', $id_payments);
                        $data['razor'] = array(
                            'publicKey' => $paycred['param_3'],  //Shared by Razor gateway
                            'secretKey' => $paycred['param_1'], //Shared by Razor gateway
                            'transaction' => $txnid,
                            'orderAmount' => $actAmount,
                            'orderCurrency' => 'INR',
                            'orderNote' => 'Online Money Transaction',
                            'customerName' => (isset($cusData['firstname']) ? (isset($cusData['lastname']) ? $cusData['firstname'] . ' ' . $cusData['lastname'] : $cusData['firstname']) : ''),
                            'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                            'customerPhone' => (isset($cusData['phone']) ? $cusData['phone'] : ''),
                            'id_payments'        => $idPayments
                            // 'id_payments'		=> $id_payments,
                        );
                        $orderData = $this->generateRazorOrderData($txnid, $actAmount, $data['razor'], $paycred);
                        if ($payment['insertID'] > 0) {
                            // $orderdet = array("payu_id" => $orderData['order_id']);
                            $orderdet = array(
                                "payu_id" => $orderData['order_id'],
                                "payment_ref_number" => $orderData['order_id']
                            );
                            $this->payment_modal->updData($orderdet, 'ref_trans_id', $txnid, 'payment');
                        }
                        $this->response($orderData, 200);
                    } else if ($payData['pg_code'] == 8) { // Easebuzz gateway
                        $amount = $actAmount . ".00";
                        $env = $paycred['type'] == 0 ? "test" : 'prod';
                        $key = $paycred['param_1'];
                        $salt = $paycred['param_3'];
                        $productinfo = 'Online Money Transaction';
                        $email = $pay_email;
                        $idPayments = implode(',', $id_payments);
                        // $hash_sequence = trim($key) . '|' . trim($txnid) . '|' . trim($amount) . '|' . trim($productinfo) . '|' . trim($cusData['firstname']) . '|' . trim($email) . '|||||||||||' . trim($salt);
                        $hash_sequence = trim($key) . '|' . trim($txnid) . '|' . trim($amount) . '|' . trim($productinfo) . '|' . trim($cusData['firstname']) . '|' . trim($email) . '|||||||' . trim($idPayments) . '||||' . trim($salt);
                        $hash_value = strtolower(hash('sha512', $hash_sequence));
                        $easebuzz = array(
                            'salt' => $salt,  //Shared by Easebuzz gateway
                            'Key' => $key, //Shared by Easebuzz gateway
                            'txnid' => $txnid,
                            'amount' => $amount,
                            'productinfo' => $productinfo,
                            'firstname' => (isset($cusData['firstname']) ? (isset($cusData['lastname']) ? $cusData['firstname'] . ' ' . $cusData['lastname'] : $cusData['firstname']) : ''),
                            'email' => $email,
                            'phone' => (isset($cusData['mobile']) ? $cusData['mobile'] : ''),
                            'surl' => $this->config->item('base_url') . "index.php/mobile_api/easebuzzSuccessResponse/",
                            'furl' => $this->config->item('base_url') . "index.php/mobile_api/easebuzzFailedResponse/",
                            'udf7'        => $idPayments,
                            'hash' => $hash_value
                        );
                        $response['easebuzzData'] = $this->_payment($easebuzz, '', $key, $salt, $env);
                        $response['pay_mode'] = $env == 'prod' ? 'production' : 'test';
                        $this->response($response, 200);
                    }
                } else {
                    $result['status'] = false;
                    $result['message'] = 'Unable to proceed your request...';
                    $this->response($result, 200);
                }
            }
        } else {
            $result['status'] = false;
            $result['message'] = 'Invalid payment request...';
            $this->response($result, 200);
        }
    }
    /*easbuzz functions - by haritha */
    function _payment($params, $redirect, $merchant_key, $salt, $env)
    {
        $postedArray = '';
        $URL = '';
        // argument validation
        $argument_validation = $this->_checkArgumentValidation($params, $merchant_key, $salt, $env);
        if (is_array($argument_validation) && $argument_validation['status'] === 0) {
            return $argument_validation;
        }
        // push merchant key into $params array.
        $params['key'] = $merchant_key;
        // remove white space, htmlentities(converts characters to HTML entities), prepared $postedArray.
        $postedArray = $this->_removeSpaceAndPreparePostArray($params);
        // empty validation
        $empty_validation = $this->_emptyValidation($postedArray, $salt);
        if (is_array($empty_validation) && $empty_validation['status'] === 0) {
            return $empty_validation;
        }
        // check amount should be float or not 
        if (preg_match("/^([\d]+)\.([\d]?[\d])$/", $postedArray['amount'])) {
            $postedArray['amount'] = (float) $postedArray['amount'];
        }
        // type validation
        $type_validation = $this->_typeValidation($postedArray, $salt, $env);
        if ($type_validation !== true) {
            return $type_validation;
        }
        // again amount convert into string
        $diff_amount_string = abs(strlen($params['amount']) - strlen("" . $postedArray['amount'] . ""));
        $diff_amount_string = ($diff_amount_string === 2) ? 1 : 2;
        $postedArray['amount'] = sprintf("%." . $diff_amount_string . "f", $postedArray['amount']);
        // email validation
        /* $email_validation = $this->_email_validation($postedArray['email']);
         if ($email_validation !== true)
             return $email_validation;*/
        // get URL based on enviroment like ($env = 'test' or $env = 'prod')
        $URL = $this->_getURL($env);
        // process to start pay
        $pay_result = $this->_pay($postedArray, $redirect, $salt, $URL);
        return $pay_result;
    }
    function _pay($params_array, $redirect, $salt_key, $url)
    {
        $hash_key = '';
        // generate hash key and push into params array.
        $hash_key = $this->_getHashKey($params_array, $salt_key);
        $params_array['hash'] = $hash_key;
        // call curl_call() for initiate pay link
        $curl_result = $this->_curlCall($url . 'payment/initiateLink', http_build_query($params_array));
        //  print_r($curl_result);
        //  die;
        $accesskey = ($curl_result->status === 1) ? $curl_result->data : null;
        if (empty($accesskey)) {
            return $curl_result;
        } else {
            if ($redirect == true) {
                $curl_result->data = $url . 'pay/' . $accesskey;
            } else {
                $curl_result->data = $accesskey;
                // return $accesskey;
            }
            return $curl_result;
        }
    }
    function _curlCall($url, $params_array)
    {
        // Initializes a new session and return a cURL.
        $cURL = curl_init();
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        // Set multiple options for a cURL transfer.
        curl_setopt_array(
            $cURL,
            array(
                CURLOPT_URL => $url,
                CURLOPT_POSTFIELDS => $params_array,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36',
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            )
        );
        // Perform a cURL session
        $result = curl_exec($cURL);
        // check there is any error or not in curl execution.
        if (curl_errno($cURL)) {
            $cURL_error = curl_error($cURL);
            if (empty($cURL_error))
                $cURL_error = 'Server Error';
            return array(
                'curl_status' => 0,
                'error' => $cURL_error
            );
        }
        $result = trim($result);
        $result_response = json_decode($result);
        return $result_response;
    }
    function _getHashKey($posted, $salt_key)
    {
        $hash_sequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
        // make an array or split into array base on pipe sign.
        $hash_sequence_array = explode('|', $hash_sequence);
        $hash = null;
        // prepare a string based on hash sequence from the $params array.
        foreach ($hash_sequence_array as $value) {
            $hash .= isset($posted[$value]) ? $posted[$value] : '';
            $hash .= '|';
        }
        $hash .= $salt_key;
        #echo $hash;
        #echo " ";
        #echo strtolower(hash('sha512', $hash));
        // generate hash key using hash function(predefine) and return
        return strtolower(hash('sha512', $hash));
    }
    function _getURL($env)
    {
        $url_link = '';
        switch ($env) {
            case 'test':
                $url_link = "https://testpay.easebuzz.in/";
                break;
            case 'prod':
                $url_link = 'https://pay.easebuzz.in/';
                break;
            case 'local':
                $url_link = 'http://localhost:8005/';
                break;
            case 'dev':
                $url_link = 'https://devpay.easebuzz.in/';
                break;
            default:
                $url_link = "https://testpay.easebuzz.in/";
        }
        return $url_link;
    }
    function _checkArgumentValidation($params, $merchant_key, $salt, $env)
    {
        $args = func_get_args();
        $argsc = count($args);
        if ($argsc !== 4) {
            return array(
                'status' => 0,
                'data' => 'Invalid number of arguments.'
            );
        }
        return 1;
    }
    function _email_validation($email)
    {
        $email_regx = "/^([\w\.-]+)@([\w-]+)\.([\w]{2,8})(\.[\w]{2,8})?$/";
        if (!preg_match($email_regx, $email)) {
            return array(
                'status' => 0,
                'data' => 'Email invalid, Please enter valid email.'
            );
        }
        return true;
    }
    function _removeSpaceAndPreparePostArray($params)
    {
        $temp_array = array();
        foreach ($params as $key => $value) {
            if (array_key_exists($key, $params) and !empty($key)) {
                if ($key != "split_payments") {
                    $temp_array[$key] = trim(htmlentities($value, ENT_QUOTES));
                } else {
                    $temp_array[$key] = trim($value);
                }
            }
        }
        return $temp_array;
    }
    function _emptyValidation($params, $salt)
    {
        $empty_value = false;
        if (empty($params['key']))
            $empty_value = 'Merchant Key';
        if (empty($params['txnid']))
            $empty_value = 'Transaction ID';
        if (empty($params['amount']))
            $empty_value = 'Amount';
        if (empty($params['firstname']))
            $empty_value = 'First Name';
        if (empty($params['email']))
            $empty_value = 'Email';
        if (empty($params['phone']))
            $empty_value = 'Phone';
        if (!empty($params['phone'])) {
            if (strlen((string) $params['phone']) != 10) {
                $empty_value = 'Phone number must be 10 digit and ';
            }
        }
        if (empty($params['productinfo']))
            $empty_value = 'Product Infomation';
        if (empty($params['surl']))
            $empty_value = 'Success URL';
        if (empty($params['furl']))
            $empty_value = 'Failure URL';
        if (empty($salt))
            $empty_value = 'Merchant Salt Key';
        if ($empty_value !== false) {
            return array(
                'status' => 0,
                'data' => 'Mandatory Parameter ' . $empty_value . ' can not empty'
            );
        }
        return true;
    }
    function _typeValidation($params, $salt, $env)
    {
        $type_value = false;
        if (!is_string($params['key']))
            $type_value = "Merchant Key should be string";
        if (!is_float($params['amount']))
            $type_value = "The amount should float up to two or one decimal.";
        if (!is_string($params['productinfo']))
            $type_value = "Product Information should be string";
        if (!is_string($params['firstname']))
            $type_value = "First Name should be string";
        if (!is_string($params['phone']))
            $type_value = "Phone Number should be number";
        if (!is_string($params['email']))
            $type_value = "Email should be string";
        if (!is_string($params['surl']))
            $type_value = "Success URL should be string";
        if (!is_string($params['furl']))
            $type_value = "Failure URL should be string";
        if ($type_value !== false) {
            return array(
                'status' => 0,
                'data' => $type_value
            );
        }
        return true;
    }
    function oldeasebuzzResponse_post()
    {
        /* {"status":1,"url":"http:\/\/localhost\/paywitheasebuzz-php-lib-master\/response.php","data":{"name_on_card":"Test","bank_ref_num":"NA","udf3":"",
        "hash":"18597796bb6944d8a43de378b23a443b1fe38b376f006693e3b0b95d692fb05c5ced14e24d6eb8f00a73605f54520ca1c23ac6be03d860c3ca3b56c3aae70bf3","firstname":"Haritha",
        "net_amount_debit":"43.0","payment_source":"Easebuzz","surl":"http:\/\/localhost\/paywitheasebuzz-php-lib-master\/response.php",
        "error_Message":"ResponseCode : BNF (Bin not found)","issuing_bank":"NA","cardCategory":"NA","phone":"9025384947","easepayid":"E230512TQYBDMR",
        "cardnum":"XXXXXXXXXXXX1111","key":"ZJFVJEL714","udf8":"","unmappedstatus":"NA","PG_TYPE":"NA","addedon":"2023-05-12 07:19:19","cash_back_percentage":"50.0",
        "status":"failure","card_type":"Credit Card","merchant_logo":"NA","udf6":"","udf10":"","upi_va":"NA","txnid":"333","productinfo":"Laptop","bank_name":"NA",
        "furl":"http:\/\/localhost\/paywitheasebuzz-php-lib-master\/response.php","udf1":"","amount":"43.0","udf2":"","udf5":"","mode":"CC","udf7":"","error":"ResponseCode : BNF (Bin not found)","udf9":"","bankcode":"NA","deduction_percentage":"1.95","email":"haritha@vikashinfosolutions.com","udf4":""}}*/
        $postData = $this->get_values();     //print_r($postData);exit;
        $trans_id = $postData['response']->txnid;
        $txStatus = $postData['response']->status;
        $orderAmount = isset($postData['response']->amount) ? $postData['response']->amount : NULL;
        $referenceId = isset($postData['response']->easepayid) ? $postData['response']->easepayid : NULL;
        $paymentMode = $postData['response']->mode;
        $txMsg = "Online Money Transaction";
        $txTime = NULL;
        // $computedSignature = base64_encode($hash_hmac); 
        // Write log 
        if (!is_dir($this->log_dir . '/easebuzz')) {
            mkdir($this->log_dir . '/easebuzz', 0777, true);
        }
        $log_path = $this->log_dir . '/easebuzz/mob_response_' . date("Y-m-d") . '.txt';
        $ldata = "\n" . date('d-m-Y H:i:s') . " \n Mobile : " . json_encode($postData, true);
        file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
        if (!empty($trans_id) && $trans_id != NULL) {
            $updateData = array(
                // "payment_mode" => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "NET_BANKING" ? "NB" : (!empty($paymentMode) ? $paymentMode : 'UPI')))),
                "payment_mode"       => ((strtolower($paymentMode) == "credit_card") || (strtolower($paymentMode) == "cc") ? "CC" : ((strtolower($paymentMode) == "debit_card") || (strtolower($paymentMode) == "dc") ? "DC" : ((strtolower($paymentMode) == "netbanking") || (strtolower($paymentMode) == "nb") ? "NB" : (strtolower($paymentMode) == "na" ? "NA" : $this->update_paymentMode($paymentMode))))),
                "payu_id" => $referenceId,
                "remark" => $txMsg . "[" . $txTime . "] mbl-status",
                "payment_ref_number" => $referenceId,
                "payment_status" => ($txStatus === "success" ? ($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success'] : $this->payment_status['awaiting']) : ($txStatus === "userCancelled" ? $this->payment_status['cancel'] : ($txStatus === "failure" ? $this->payment_status['failure'] : $this->payment_status['pending'])))
            );
            $payment = $this->payment_modal->updateGatewayResponse($updateData, $trans_id);
            if ($payment['status'] == true) {
                $serviceID = 0;
                $pay_msg = "Your payment has been " . $txStatus;
                if ($txStatus === "success") {
                    $serviceID = 3;
                    $type = 2;
                    $pay_msg = 'Transaction ID: ' . $trans_id . '. Payment amount INR. ' . $orderAmount . ' is paid successfully. Thanks for your payment with ' . $this->comp['company_name'];
                } else if ($txStatus === "failure") {
                    $serviceID = 7;
                    $type = -1;
                    $pay_msg = 'Your payment has been failed. Please try again. Transaction ID: ' . $trans_id;
                } else if ($txStatus === "userCancelled") {
                    $serviceID = 7;
                    $type = 3;
                    $pay_msg = 'Your payment has been cancelled.';
                }
                if ($serviceID > 0) {
                    if ($txStatus === "success") {
                        $payIds = $this->payment_modal->getPayIds($trans_id);
                        if (sizeof($payIds) > 0) {
                            // Wallet Debit :: Haritha's code altered by Pavithra on 27-8-2024 [Debit splitup]
                            $actual_trans_amt = $payIds[0]['actual_trans_amt'];
                            $act_wal_redeemed = $payIds[0]['redeemed_amount'];
                            $remaining_amt = 0;
                            $remaining_wal_amt = 0;
                            $updData = array("last_payment_on" => NULL);
                            $this->payment_modal->updData($updData, 'id_customer', $payIds[0]['id_customer'], 'customer');
                            if ($act_wal_redeemed > 0) {
                                // Update payment table - payment mode as multi
                                $this->payment_modal->updPayModeBRefTranID(["payment_mode" => 'MULTI'], $trans_id);
                            }
                            foreach ($payIds as $pay) {
                                $tax = 0;
                                $pay_amount = 0;
                                if ($pay['gst_type'] == 1) { // Exclusive
                                    $tax = $pay['gst_amount'];
                                }
                                $pay_amount = ($pay['payment_amount'] - $pay['discountAmt'] + $tax);
                                $mode_amount = $pay_amount;
                                if ($actual_trans_amt > 0) { // Having Gateway balance, add gateway mode
                                    if ($actual_trans_amt > $pay_amount) {
                                        $actual_trans_amt -= $pay_amount;
                                    } else if ($actual_trans_amt == $pay_amount) {
                                        $actual_trans_amt -= $pay_amount;
                                    } else if ($actual_trans_amt < $pay_amount) {
                                        $mode_amount = $actual_trans_amt;
                                        $remaining_amt = abs($actual_trans_amt - $pay_amount);
                                        $actual_trans_amt = 0;
                                        $pay_amount = $remaining_amt;
                                    }
                                    /*echo "Having Gateway balance, add gateway mode.<br/>";
                                    echo "actual_trans_amt : ".$actual_trans_amt."<br/>";
                                    echo "remaining_amt : ".$remaining_amt."<br/>";*/
                                    // Insert payment Mode details               
                                    $arrayPayMode = array(
                                        'payment_amount' => $mode_amount,
                                        // 'payment_date' => date("Y-m-d H:i:s"),
                                        'payment_date'         => $pay['date_payment'],
                                        'created_time' => date("Y-m-d H:i:s"),
                                        // "payment_mode" => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "NET_BANKING" ? "NB" : (!empty($paymentMode) ? $paymentMode : 'UPI')))),
                                        "payment_mode"      => $updateData['payment_mode'],
                                        "remark" => $txMsg . "[" . $txTime . "] mbl-status",
                                        "payment_ref_number" => $referenceId,
                                        "payment_status" => $updateData['payment_status']
                                    );
                                    if ($pay['due_type'] == 'GA') {
                                        $arrayPayMode['id_adv_payment'] = $pay['id_payment'];
                                        $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'general_advance_mode_detail');
                                    } else {
                                        $update_pmd = array(
                                            'payment_status' => 9, // Cancelled
                                            "updated_time" => date('Y-m-d H:i:s'),
                                            "remark" => "Removed while direct success on easebuzz " . date('Y-m-d H:i:s'),
                                        );
                                        $update_existing_pmd = $this->payment_modal->updData($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');
                                        $arrayPayMode['id_payment'] = $pay['id_payment'];
                                        $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'payment_mode_details');
                                    }
                                }
                                if (($remaining_amt > 0 || $actual_trans_amt == 0) && $act_wal_redeemed > 0) { // Having wallet redemption: add wallet mode & add debit transaction 
                                    if ($act_wal_redeemed > $pay_amount) {
                                        $act_wal_redeemed -= $pay_amount;
                                    } else if ($act_wal_redeemed == $pay_amount) {
                                        $act_wal_redeemed -= $pay_amount;
                                    }
                                    /*else if($act_wal_redeemed < $pay_amount){
                                        $remaining_amt		   = abs($act_wal_redeemed-$pay_amount);
                                        $act_wal_redeemed   = 0;
                                    }*/
                                    /*echo "Having wallet redemption : add wallet mode & add debit transaction.<br/>";
                                    echo "act_wal_redeemed : ".$act_wal_redeemed."<br/>";*/
                                    // Insert payment Mode details               
                                    $arrayPayMode = array(
                                        'payment_amount' => $pay_amount,
                                        // 'payment_date' => date("Y-m-d H:i:s"),
                                        'payment_date'         => $pay['date_payment'],
                                        'created_time' => date("Y-m-d H:i:s"),
                                        "payment_mode" => "REF_WALLET",
                                        "remark" => "Wallet Utilized",
                                        "payment_ref_number" => NULL,
                                        "payment_status" => 1
                                    );
                                    if ($pay['due_type'] == 'GA') {
                                        $arrayPayMode['id_adv_payment'] = $pay['id_payment'];
                                        $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'general_advance_mode_detail');
                                    } else {
                                        $update_pmd = array(
                                            'payment_status' => 9, // Cancelled
                                            "updated_time" => date('Y-m-d H:i:s'),
                                            "remark" => "Removed while direct success on easebuzz " . date('Y-m-d H:i:s'),
                                        );
                                        $update_existing_pmd = $this->payment_modal->updData($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');
                                        $arrayPayMode['id_payment'] = $pay['id_payment'];
                                        $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'payment_mode_details');
                                    }
                                    $WalletinsData = array(
                                        'id_wallet_account' => $pay['id_wallet_account'],
                                        'transaction_type' => 1, //0-Credit,1-Debit
                                        'type' => 0, //CRM
                                        'id_sch_ac' => $pay['id_scheme_account'],
                                        'value' => $pay_amount,
                                        'description' => 'Chit Redeem',
                                        'date_transaction' => date("Y-m-d H:i:s"),
                                        'id_employee' => NULL,
                                        'date_add' => date("Y-m-d H:i:s"),
                                        'credit_for' => 'Redeem',
                                        'id_payment' => $pay['id_payment']
                                    );
                                    $this->payment_modal->insertData($WalletinsData, 'wallet_transaction');
                                }
                                /*//Redeem amount in wallet credit for customer - by haritha 19-1-24
                                if($pay['redeemed_amount'] > 0 && $pay['redeemed_amount'] != NULL && $pay['id_wallet_account'] != NULL)
                                {
                                    $WalletinsData=array(
                                                                            'id_wallet_account'=>$pay['id_wallet_account'],
                                                                            'transaction_type' =>1, //0-Credit,1-Debit
                                                                            'type'             =>0, //CRM
                                                                            'id_sch_ac'        =>$pay['id_scheme_account'],
                                                                            'value'            =>$pay['redeemed_amount'],
                                                                            'description'      =>'Chit Redeem',
                                                                            'date_transaction' => date("Y-m-d H:i:s"),
                                                                            'id_employee'      =>NULL,
                                                                            'date_add'	       => date("Y-m-d H:i:s"),
                                                                            'credit_for'      =>'Redeem',
                                                                            'id_payment'      => $pay['id_payment']
                                                                            );
                                                                $this->payment_modal->insertData($WalletinsData,'wallet_transaction');
                                     $arrayPayMode=array(
                                                    'id_payment'         => $pay['id_payment'],
                                                    'payment_amount'     => (isset($pay['redeemed_amount']) ? $pay['redeemed_amount'] : NULL),
                                                    'payment_date'		 => date("Y-m-d H:i:s"),
                                                    'created_time'	     => date("Y-m-d H:i:s"),
                                                    "payment_mode"       => "REF_WALLET",
                                                    "remark"             => $txMsg."[".$txTime."] mbl-status - Wallet Utilized",
                                                    "payment_ref_number" => $referenceId,
                                                    "payment_status"     => 1
                                                    );
                                            $cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details'); 
                                }
                                //ends
                                // Multi mode payment
                                if($updateData['payment_mode']!= NULL)
                                 {
                                        if($pay['due_type'] == 'GA'){
                                            $arrayPayMode=array(
                                                    'id_adv_payment'         => $pay['id_payment'],
                                                    'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                                                    'payment_date'		 => date("Y-m-d H:i:s"),
                                                    'created_time'	     => date("Y-m-d H:i:s"),
                                                    "payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))),
                                                    "remark"             => $txMsg."[".$txTime."] mbl-status",
                                                    "payment_ref_number" => $referenceId,
                                                    "payment_status"     => 1
                                                    );
                                            $cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'general_advance_mode_detail');
                                        }else{
                                            $payment_amt = $pay['redeemed_amount'] > 0 && $pay['redeemed_amount'] != NULL ? ($pay['payment_amount'] - $pay['redeemed_amount']) : $pay['payment_amount'];
                                            $arrayPayMode=array(
                                                    'id_payment'         => $pay['id_payment'],
                                                    'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                                                    'payment_date'		 => date("Y-m-d H:i:s"),
                                                    'created_time'	     => date("Y-m-d H:i:s"),
                                                    "payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))),
                                                    "remark"             => $txMsg."[".$txTime."] mbl-status",
                                                    "payment_ref_number" => $referenceId,
                                                    "payment_status"     => 1
                                                    );
                                            $cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details'); 
                                        }
                                 }*/
                                $schData = [];
                                $cusRegData = [];
                                $transData = [];
                                if ($pay['due_type'] != 'GA') {
                                    //13-09-2022 Coded by haritha 
                                    //employee incentive credits based on installment settings
                                    if ($pay['referal_code'] != '' && $pay['emp_refferal'] == 1) {
                                        $type = 1; //1- employee 2- agent
                                        $emp_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'], $pay['id_scheme_account'], $type, $pay['id_payment']);
                                        if ($emp_refral > 0) {
                                            $res = $this->insertEmployeeIncentive($emp_refral, $pay['id_scheme_account'], $pay['id_payment']);
                                            foreach ($emp_refral as $emp) {
                                                if ($emp['referal_amount'] > 0) {
                                                    //$res = $this->insertEmployeeIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
                                                    if ($emp['credit_for'] == 1) {
                                                        $this->customerIncentive($emp, $pay['id_scheme_account'], $pay['id_payment']);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    // Generate account  number  
                                    if ($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set'] == 3) {
                                        if ($pay['scheme_acc_number'] == '' || $pay['scheme_acc_number'] == null || $pay['scheme_acc_number'] == 0) {
                                            $ac_group_code = NULL;
                                            // Lucky draw
                                            if ($pay['is_lucky_draw'] == 1) { // Based on scheme settings 
                                                // Update Group code in scheme_account table 
                                                $updCode = $this->payment_modal->updateGroupCode($pay);
                                                $ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
                                            }
                                            // $this->db->query('LOCK TABLES scheme_account WRITE');
                                            $scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'], $pay['branch'], $ac_group_code);
                                            if ($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0) {
                                                $schData['scheme_acc_number'] = $scheme_acc_number;
                                                $cusRegData['scheme_acc_number'] = $scheme_acc_number;
                                            } else {
                                                // $this->db->query('UNLOCK TABLES');
                                            }
                                        }
                                    }
                                    // Generate receipt number
                                    if ($pay['receipt_no_set'] == 1 && ($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2)) {
                                        // $this->db->query('LOCK TABLES payment WRITE');
                                        $receipt['receipt_no'] = $this->payment_modal->generate_receipt_no($pay['id_scheme'], $pay['branch']);
                                        $payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'], $receipt);
                                        // $this->db->query('UNLOCK TABLES');
                                    }
                                    if ($pay['id_scheme_account'] > 0) {
                                        if (sizeof($schData) > 0) { // Update scheme account
                                            $this->payment_modal->update_account($schData, $pay['id_scheme_account']);
                                            // $this->db->query('UNLOCK TABLES');
                                        }
                                        if (sizeof($cusRegData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                            $this->payment_modal->update_cusreg($cusRegData, $pay['id_scheme_account']); // Update Customer reg - Client ID, Ac No
                                        }
                                        if (sizeof($transData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                            $this->payment_modal->update_trans($transData, $pay['id_scheme_account']); // Update Transaction - Client ID
                                        }
                                    }
                                    //Update First Payment Amount In Scheme Account
                                    $approval_type = $this->config->item('auto_pay_approval');
                                    if (($approval_type == 1 || $approval_type == 2 || $approval_type == 3)) {
                                        if (($pay['flexible_sch_type'] == 4 || $pay['flexible_sch_type'] == 8) && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "") && $pay['firstPayment_as_wgt'] == 1) { // Fix First payable as weight
                                            $fixPayable = array('firstPayment_wgt' => $pay['metal_weight']);
                                        } else if (($pay['firstPayamt_maxpayable'] == 1 || $pay['firstPayamt_as_payamt'] == 1) && ($pay['firstPayment_amt'] == '' || $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0)) {
                                            $fixPayable = array('firstPayment_amt' => $pay['payment_amount']);
                                        }
                                        $status = $this->payment_modal->update_account($fixPayable, $pay['id_scheme_account']);
                                    }
                                    if ($this->config->item('auto_pay_approval') == 2 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                        if ($this->config->item('integrationType') == 1) {
                                            $this->insert_common_data_jil($pay['id_payment']);
                                        } else if ($this->config->item('integrationType') == 2) {
                                            $this->mobileapi_model->insert_common_data($pay['id_payment']);
                                        }
                                    }
                                }
                                $service = $this->services_modal->checkService($serviceID);
                                if ($service['sms'] == 1) {
                                    $id = $pay['id_payment'];
                                    $data = $this->services_modal->get_SMS_data($serviceID, $id);
                                    $mobile = $data['mobile'];
                                    $message = $data['message'];
                                    $this->mobileapi_model->send_sms($mobile, $message, '', $service['dlt_te_id']);
                                }
                            }
                        }
                    }
                }
                //redirect('paymt/hdfcTransStatus/success/'.$resData['txnid'].'/'.$resData['amount']);
                $response = array("status" => TRUE, "title" => "Transaction " . $txStatus, "msg" => $pay_msg);
            } else {
                //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
                $response = array("status" => TRUE, "title" => "Transaction " . $txStatus, "msg" => "Unable to proceed your payment..");
            }
        } else {
            //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
            $response = array("status" => TRUE, "title" => "Transaction " . $txStatus, "msg" => "Your payment has been Cancelled");
        }
        $this->response($response, 200);
    }
    //ends
    function mobile_payment_get()
    {
        $payData = array(
            'firstname' => $this->get('firstname'),
            'lastname' => $this->get('lastname'),
            'phone' => $this->get('phone'),
            'id_branch' => $this->get('id_branch'),
            'amount' => $this->get('amount'),
            'redeemed_amount' => $this->get('redeemed_amount'),
            'productinfo' => $this->get('productinfo'),
            'email' => $this->get('email'),
            'gateway' => $this->get('gateway'),
            'pg_code' => $this->get('pg_code'),
            'pay_arr' => $this->get('pay_arr')
        );
        // print_r($payData);exit;
        $sch_payment = json_decode($payData['pay_arr']);
        $sum_of_payArr_pay = array_sum(array_column($sch_payment, 'pay_amt'));   //Whole amount & payarray sum amunt mismatch bug fix....On: 30-01-2024,code upd by :Abi
        if (!empty($payData['phone']) && $payData['amount'] > 0 && $payData['amount'] == $sum_of_payArr_pay) {
            //get the values posted from mobile in array 
            $pay_flag = TRUE;
            $allow_flag = FALSE;
            $submit_pay = FALSE;
            $actAmount = (float) $payData['amount'];  // sum of all chit amount
            $cusData = $this->payment_modal->get_customer($payData['phone']);
            $gateway = (isset($payData['gateway']) ? ($payData['gateway'] == 'undefined' ? 1 : $payData['gateway']) : 1);
            $paycred = $this->payment_gateway($cusData['id_branch'], $payData['gateway']);
            //check pay_flag
            if ($pay_flag) {
                //generate txnid
                $txnid = uniqid(time());
                $i = 1;
                //	 $sch_payment = json_decode($payData['pay_arr']);
                $udf1 = "";
                $udf2 = "";
                $udf3 = "";
                $productinfo = "";
                // $this->db->trans_begin();	
                foreach ($sch_payment as $pay) {
                    if ($cusData['branch_settings'] == 1) {
                        if ($cusData['is_branchwise_cus_reg'] == 1) {
                            $id_branch = $cusData['id_branch'];
                        } else {
                            $id_branch = $pay->id_branch;
                            //$id_branch  = $branch['sch_join_branch'];
                        }
                    } else {
                        $id_branch = NULL;
                    }
                    //validate amount
                    $chit = $this->payment_modal->get_schemeByChit($pay->udf1);
                    if ($pay->scheme_type == 1) {
                        $metal_rate = $this->payment_modal->getMetalRate('');
                        $gold_rate = (float) $metal_rate['goldrate_22ct'];
                        $amt = $gold_rate * $pay->udf2;
                        $allow_flag = ($pay->amount >= $amt ? TRUE : FALSE);
                    } else {
                        $allow_flag = ($pay->amount >= $chit['amount'] ? TRUE : FALSE);
                    }
                    if ($pay->scheme_type == 2 || ($pay->scheme_type == 3 && $chit['wgt_convert'] == 0)) {
                        $data = array('metal_rate' => $pay->udf3, 'amount' => $pay->udf4);
                        $metal_wgt = $this->amount_to_weight($data);
                    } else {
                        $metal_wgt = (isset($pay->udf2) && $pay->udf2 != '' ? $pay->udf2 : 0.000);
                    }
                    //rate fixed at the time of scheme join
                    if ($chit['one_time_premium'] == 1 && $chit['rate_fix_by'] == 0 && $chit['rate_select'] == 1) {
                        $metal_rate = $this->payment_modal->getMetalRate('');
                        $gold_rate = (float) $metal_rate['goldrate_22ct'];
                        if ($gold_rate != 0) {
                            $isRateFixed = $this->mobileapi_model->isRateFixed($pay->udf1);
                            if ($isRateFixed['status'] == 0) {
                                $updData = array(
                                    "fixed_wgt" => $pay->amount / $gold_rate,
                                    "firstPayment_amt" => $pay->amount,
                                    "fixed_metal_rate" => $gold_rate,
                                    "rate_fixed_in" => 2,
                                    "fixed_rate_on" => date("Y-m-d H:i:s")
                                );
                                //print_r($updData);exit; 
                                $ratestatus = $this->mobileapi_model->updFixedRate($updData, $pay->udf1);
                            } else {
                                $data = array('is_valid' => TRUE, 'success' => TRUE, 'msg' => "Rate already fixed !!");
                            }
                        }
                    }
                    // AB - digi_benefits & weight...
                    $dg_saved_benefit = 0;
                    $dg_benefit_value = 0;
                    $dg_benefit_type = 0;
                    if ($chit['is_digi'] == 1) {
                        $metal_wgt = formatMetalWeight(($pay->amount / $pay->udf3));
                        if ($chit['interest'] == 1) {
                            $digi_account = $this->digigold_modal->digiGold_account($cusData['id_customer']);
                            $digi_benefit = $this->digigold_modal->get_digi_benefit($digi_account);
                            if (sizeof($digi_benefit) > 0) {
                                if ($digi_benefit['interest_type'] == 0) {
                                    $ben = $pay->amount * ($digi_benefit['interest_value'] / 100);
                                } else {
                                    $ben = $digi_benefit['interest_value'];
                                }
                                $dg_saved_benefit = formatMetalWeight(($ben / $pay->udf3)); //echo '<pre>';print_r($dg_saved_benefit);exit; 
                                $dg_benefit_value = $digi_benefit['interest_value'];
                                $dg_benefit_type = $digi_benefit['interest_type']; // 0-percent, 1- amount
                            }
                        }
                    }
                    if ($allow_flag) {
                        //  print_r($pay);exit;
                        //set insert data					
                        $insertData = array(
                            "id_scheme_account" => (isset($pay->udf1) ? $pay->udf1 : NULL),
                            "id_scheme" => (isset($chit['id_scheme']) ? $chit['id_scheme'] : 0),
                            "payment_amount" => (isset($pay->amount) ? $pay->amount + ($pay->discount == "" ? 0.00 : $pay->discount) : NULL),
                            "gst" => (isset($chit['gst']) ? $chit['gst'] : 0),
                            "gst_type" => (isset($chit['gst_type']) ? $chit['gst_type'] : 0),
                            "payment_type" => (isset($paycred) ? ($paycred['pg_name']) : ''),
                            "due_type" => $pay->due_type,
                            "no_of_dues" => 1,
                            "actual_trans_amt" => (isset($actAmount) ? $actAmount : 0.00),
                            "date_payment" => date('Y-m-d H:i:s'),
                            "metal_rate" => (isset($pay->udf3) && $pay->udf3 != '' ? $pay->udf3 : 0.00),
                            "metal_weight" => $metal_wgt,
                            "id_transaction" => (isset($txnid) ? $txnid . '-' . $i : NULL),
                            "ref_trans_id" => (isset($txnid) ? $txnid : NULL), // to update pay status after trans complete.
                            "remark" => 'Paid for ' . $pay->udf5 . ($pay->udf5 > 1 ? 'months' : 'month'),
                            "added_by" => 2,
                            "add_charges" => (isset($pay->charge) ? $pay->charge : NULL),
                            "discountAmt" => ($pay->discount != "" ? $pay->discount : 0.00),
                            "payment_status" => $this->payment_status['pending'],
                            "id_payGateway" => (isset($gateway) ? $gateway : 1),
                            'id_branch' => $id_branch,
                            "redeemed_amount" => (isset($payData['redeemed_amount']) ? $payData['redeemed_amount'] : 0.00),
                            "saved_benefits" => $dg_saved_benefit,
                            "benefit_value" => $dg_benefit_value,
                            "benefit_type" => $dg_benefit_type,
                            // 	    "is_point_credited"   => 1
                            //status - 0 (pending), will change to 1 after approved at backend
                        );
                        $udf1 = $udf1 . " " . $pay->udf1;
                        $udf2 = $udf2 . " " . $pay->udf2;
                        $udf3 = $udf3 . " " . $pay->udf3;
                        $productinfo = $productinfo . " " . $pay->chit_number;
                        //inserting pay_data before gateway process
                        /*echo "<pre>";print_r($insertData);echo "</pre>";	*/
                        $payment = $this->payment_modal->addPayment($insertData);
                        $i++;
                    }
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $submit_pay = TRUE;
                } else {
                    $this->db->trans_rollback();
                    $submit_pay = FALSE;
                }
                if ($submit_pay) {
                    $updData = array("last_payment_on" => date("Y-m-d H:i:s"));
                    $this->payment_modal->updData($updData, 'id_customer', $cusData['id_customer'], 'customer');
                    $secretKey = $paycred['param_1']; //Shared by Cashfree  
                    $appId = $paycred['param_3'];
                    //set data for hash generation
                    $data['pay'] = array(
                        'key' => $secretKey,
                        'txnid' => $txnid,
                        'amount' => (isset($payData['amount']) ? $payData['amount'] : ''),
                        'productinfo' => (isset($productinfo) ? $productinfo : ''),
                        'firstname' => (isset($cusData['firstname']) ? $cusData['firstname'] : ''),
                        'lastname' => (isset($cusData['lastname']) ? $cusData['lastname'] : ''),
                        'email' => (isset($cusData['email']) ? $cusData['email'] : ''),
                        'phone' => (isset($cusData['mobile']) ? $cusData['mobile'] : ''),
                        'address1' => (isset($cusData['address1']) ? $cusData['address1'] : ''),
                        'address2' => (isset($cusData['address2']) ? $cusData['address2'] : ''),
                        'city' => (isset($cusData['city']) ? $cusData['city'] : ''),
                        'state' => (isset($cusData['state']) ? $cusData['state'] : ''),
                        'country' => (isset($cusData['country']) ? $cusData['country'] : ''),
                        'zipcode' => (isset($cusData['pincode']) ? $cusData['pincode'] : ''),
                        'udf1' => (isset($udf1) ? $udf1 : ''),
                        'udf2' => (isset($udf2) ? $udf2 : ''),
                        'udf3' => (isset($udf3) ? $udf3 : ''),
                        'udf4' => (isset($payData['udf4']) ? $payData['udf4'] : ''),
                        'udf5' => (isset($payData['udf5']) ? $payData['udf5'] : '')
                    );
                    if ($payData['pg_code'] == 4) {   // cash free 
                        $gen_email = $this->random_strings(8) . '@gmail.com';
                        $data['cashfreepay'] = array(
                            'appId' => $appId,  //Shared by Cashfree
                            'orderId' => $txnid,
                            'orderAmount' => (string) $actAmount,
                            'orderCurrency' => 'INR',
                            'orderNote' => 'Purchase plan payment from mobile app',
                            'customerName' => (isset($payData['firstname']) ? (isset($payData['lastname']) ? $payData['firstname'] . ' ' . $payData['lastname'] : $payData['firstname']) : ''),
                            'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                            'customerPhone' => (isset($payData['phone']) ? $payData['phone'] : ''),
                            "notifyUrl" => $this->config->item('base_url') . "index.php/services/cashfreeStatusNotify/" . $payData['gateway'],
                            "stage" => $paycred['type'] == 0 ? "TEST" : 'PROD', //"TEST/PROD",
                        );
                        //$token = $this->generateCFtoken($txnid,$actAmount,$data['cashfreepay'],$paycred); 
                        $token = $this->generateCashfreeSession($txnid, $actAmount, $data['cashfreepay'], $paycred);
                        $this->response($token, 200);
                    } else if ($payData['pg_code'] == 6) {   // Ippo Gateway
                        $gen_email = $this->random_strings(8) . '@gmail.com';
                        $data['ippo'] = array(
                            'publicKey' => $paycred['param_3'],  //Shared by Ippo gateway
                            'secretKey' => $paycred['param_1'], //Shared by Ippo gateway
                            'transaction' => $txnid,
                            'orderAmount' => $actAmount,
                            'orderCurrency' => 'INR',
                            'orderNote' => 'Online Money Transaction',
                            'customerName' => (isset($cusData['firstname']) ? (isset($cusData['lastname']) ? $cusData['firstname'] . ' ' . $cusData['lastname'] : $cusData['firstname']) : ''),
                            'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                            'customerPhone' => (isset($cusData['phone']) ? $cusData['phone'] : ''),
                            "returnUrl" => $this->config->item('base_url') . "index.php/paymt/ipporesponseURL"                      //$this->config->item('base_url')."index.php/paymt/cashfreeresponseURL"
                        );
                        $orderData = $this->generateOrderData($txnid, $actAmount, $data['ippo'], $paycred);
                        $this->response($orderData, 200);
                        // $this->load->view('ippo/ippo_payment',$data);
                    } else if ($payData['pg_code'] == 7) {
                        $gen_email = $this->random_strings(8) . '@gmail.com';
                        $data['razor'] = array(
                            'publicKey' => $paycred['param_3'],  //Shared by Razor gateway
                            'secretKey' => $paycred['param_1'], //Shared by Razor gateway
                            'transaction' => $txnid,
                            'orderAmount' => $actAmount,
                            'orderCurrency' => 'INR',
                            'orderNote' => 'Online Money Transaction',
                            'customerName' => (isset($cusData['firstname']) ? (isset($cusData['lastname']) ? $cusData['firstname'] . ' ' . $cusData['lastname'] : $cusData['firstname']) : ''),
                            'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                            'customerPhone' => (isset($cusData['phone']) ? $cusData['phone'] : '')
                        );
                        $orderData = $this->generateRazorOrderData($txnid, $actAmount, $data['razor'], $paycred);
                        $this->response($orderData, 200);
                    }
                }
            }
        } else {
            $result['status'] = false;
            $result['message'] = 'Invalid payment request...';
            $this->response($result, 200);
        }
    }
    function generateCFtoken($txnid, $orderAmount, $params, $paycred)
    {
        $secretKey = $paycred['param_1']; //Shared by Cashfree  
        $appId = $paycred['param_3'];
        $postData = array(
            "orderId" => $txnid,
            "orderAmount" => $orderAmount,
            "orderCurrency" => "INR"
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $paycred['api_url'] . 'api/v2/cftoken/order',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "Content-Type: application/json",
                "x-client-id: " . $appId,
                "x-client-secret: " . $secretKey
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            //echo "cURL Error #:" . $err;
            $result = array('status' => false, "message" => 'Sorry for inconvenience, we are unable to proceed your request. Kindly contact customer care..');
            return $result;
        } else {
            $tokenData = json_decode($response);
            if ($tokenData->status == 'OK') {
                $gen_email = $this->random_strings(8) . '@gmail.com';
                $result['status'] = true;
                $result['params'] = $params;
                $result['params']['tokenData'] = $tokenData->cftoken;
            } else if ($tokenData->status == 'ERROR') {
                $result['status'] = false;
                $result['message'] = 'Sorry for inconvenience, we are unable to proceed your request. Kindly contact customer care...';
            }
            return $result;
        }
    }
    function generateOrderData($txnid, $orderAmount, $params, $paycred)
    {
        $publicKey = $params['publicKey'];                                                               //'pk_live_MXZi5igA2W4Y';
        $secretKey = $params['secretKey'];
        $data = "{
            \"amount\": " . $orderAmount . ",
            \"currency\": \"INR\",
            \"payment_modes\": \"cc,dc,nb,upi\",
            \"return_url\": \"" . $params['returnUrl'] . "\",
            \"customer\": {
                \"name\": \"" . $params['customerName'] . "\",
                \"email\": \"" . $params['customerEmail'] . "\",
                \"phone\": {
                    \"country_code\": \"91\" ,
                    \"national_number\": \"" . $params['customerPhone'] . "\"
                }
            }
        }";
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://" . $publicKey . ":" . $secretKey . "@api.ippopay.com/v1/order/create",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            //echo "cURL Error #:" . $err;exit;
            $result['status'] = false;
            $result['message'] = 'Sorry for inconvenience, we are unable to proceed your request. Kindly contact customer care...';
        } else {
            $array = json_decode($response, true);
            $result['status'] = true;
            $result['order_id'] = $array['data']['order']['order_id'];
            $result['params'] = $params;
            $result['params']['ippoData'] = $array;
        }
        return $result;
    }
    public function generateRazorOrderData($txnid, $orderAmount, $params, $paycred)
    {
        $publicKey = $params['publicKey'];                                                               //'pk_live_MXZi5igA2W4Y';
        $secretKey = $params['secretKey'];
        /*$order_tags = [];
        foreach ($params['id_payments'] as $index => $id) {
            $order_tags["payment_".($index+1)] = (string)$id;
        }*/
        $data = json_encode(array("amount" => $orderAmount * 100, "currency" => "INR", "receipt" => $txnid, "notes" => array("id_transaction" => $txnid, "notes_key_1" => "test2", "id_payments" => $params['id_payments'])));
        // print_r($data);exit;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.razorpay.com/v1/orders",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "$publicKey:$secretKey",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            //echo "cURL Error #:" . $err;exit;
            $result['status'] = false;
            $result['message'] = 'Sorry for inconvenience, we are unable to proceed your request. Kindly contact customer care...';
        } else {
            $array = json_decode($response, true);
            $result['status'] = true;
            $result['params'] = $params;
            $result['params']['razorData'] = $array;
            $result['order_id'] = $array['id'];
        }
        return $result;
    }
    public function cf_payment_status_post()
    {
        /*{"paymentMode":"CREDIT_CARD","orderId":"16305780766130a59c7beaf","txTime":"2021-09-02 15:51:38","referenceId":"1043610","type":"CashFreeResponse","txMsg":"Transaction Successful","signature":"MT1j+VSkIjS5hg2zGKt\/xmZjHWoz70dKm4\/w8LK6qbg=","orderAmount":"1000.00","txStatus":"SUCCESS"}
        {"paymentMode":"CREDIT_CARD","orderId":"16305785416130a76de4d2a","txTime":"2021-09-02 15:59:17","referenceId":"1043658","type":"CashFreeResponse","txMsg":"Your transaction has failed.","signature":"QRgBU7lujaC0qc5SMoxPczO4qpWAI1zWJI6CKoinroI=","orderAmount":"1000.00","txStatus":"FAILED"}
        {"orderId":"16305784926130a73c56f7b","type":"CashFreeResponse","txStatus":"CANCELLED"}*/
        $postData = $this->get_values();
        $trans_id = $postData["orderId"];
        $txStatus = $postData["txStatus"];
        $orderAmount = isset($postData["orderAmount"]) ? $postData["orderAmount"] : NULL;
        $referenceId = isset($postData["referenceId"]) ? $postData["referenceId"] : NULL;
        $paymentMode = isset($postData["paymentMode"]) ? $postData["paymentMode"] : NULL;
        $txMsg = isset($postData["txMsg"]) ? $postData["txMsg"] : NULL;
        $txTime = isset($postData["txTime"]) ? $postData["txTime"] : NULL;
        // $computedSignature = base64_encode($hash_hmac); 
        // Write log 
        if (!is_dir($this->log_dir . '/cashfree')) {
            mkdir($this->log_dir . '/cashfree', 0777, true);
        }
        $log_path = $this->log_dir . '/cashfree/mob_response_' . date("Y-m-d") . '.txt';
        $ldata = "\n" . date('d-m-Y H:i:s') . " \n Mobile : " . json_encode($postData, true);
        file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
        /* // Signature Verification
        $data = $orderId.$orderAmount.$referenceId.$txStatus.$paymentMode.$txMsg.$txTime;
        $hash_hmac = hash_hmac('sha256', $data, $secretkey, true) ;
        $computedSignature = base64_encode($hash_hmac);
        if ($signature == $computedSignature) {
            // Proceed
        } else {
            // Reject this call
        }
        */
        if (!empty($trans_id) && $trans_id != NULL) {
            $pay = $this->payment_modal->getWalletPaymentContent($trans_id);
            $updateData = array(
                "payment_mode" => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "NET_BANKING" ? "NB" : $paymentMode))),
                "payu_id" => $referenceId,
                "remark" => $txMsg . "[" . $txTime . "] mbl-status",
                "payment_ref_number" => $referenceId,
                "payment_status" => ($txStatus === "SUCCESS" ? ($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success'] : $this->payment_status['awaiting']) : ($txStatus === "CANCELLED" ? $this->payment_status['cancel'] : ($txStatus === "FAILED" ? $this->payment_status['failure'] : $this->payment_status['pending'])))
            );
            $payment = $this->payment_modal->updateGatewayResponse($updateData, $trans_id);
            if ($payment['status'] == true) {
                $serviceID = 0;
                $pay_msg = "Your payment has been " . $txStatus;
                if ($txStatus === "SUCCESS") {
                    $serviceID = 3;
                    $type = 2;
                    $pay_msg = 'Transaction ID: ' . $trans_id . '. Payment amount INR. ' . $orderAmount . ' is paid successfully. Thanks for your payment with ' . $this->comp['company_name'];
                } else if ($txStatus === "FAILED") {
                    $serviceID = 7;
                    $type = -1;
                    $pay_msg = 'Your payment has been failed. Please try again. Transaction ID: ' . $trans_id;
                } else if ($txStatus === "CANCELLED") {
                    $serviceID = 7;
                    $type = 3;
                    $pay_msg = 'Your payment has been cancelled.';
                }
                if ($serviceID > 0) {
                    if ($txStatus === "SUCCESS") {
                        $payIds = $this->payment_modal->getPayIds($trans_id);
                        if (sizeof($payIds) > 0) {
                            $updData = array("last_payment_on" => NULL);
                            $this->payment_modal->updData($updData, 'id_customer', $payIds[0]['id_customer'], 'customer');
                            // Wallet Debit :: Haritha's code altered by Pavithra on 27-8-2024 [Debit splitup]
                            $actual_trans_amt = $payIds[0]['actual_trans_amt'];
                            $act_wal_redeemed = $payIds[0]['redeemed_amount'];
                            $remaining_amt = 0;
                            $remaining_wal_amt = 0;
                            $updData = array("last_payment_on" => NULL);
                            $this->payment_modal->updData($updData, 'id_customer', $payIds[0]['id_customer'], 'customer');
                            if ($act_wal_redeemed > 0) {
                                // Update payment table - payment mode as multi
                                $this->payment_modal->updPayModeBRefTranID(["payment_mode" => 'MULTI'], $trans_id);
                            }
                            foreach ($payIds as $pay) {
                                $tax = 0;
                                $pay_amount = 0;
                                if ($pay['gst_type'] == 1) { // Exclusive
                                    $tax = $pay['gst_amount'];
                                }
                                $pay_amount = ($pay['payment_amount'] - $pay['discountAmt'] + $tax);
                                $mode_amount = $pay_amount;
                                if ($actual_trans_amt > 0) { // Having Gateway balance, add gateway mode
                                    if ($actual_trans_amt > $pay_amount) {
                                        $actual_trans_amt -= $pay_amount;
                                    } else if ($actual_trans_amt == $pay_amount) {
                                        $actual_trans_amt -= $pay_amount;
                                    } else if ($actual_trans_amt < $pay_amount) {
                                        $mode_amount = $actual_trans_amt;
                                        $remaining_amt = abs($actual_trans_amt - $pay_amount);
                                        $actual_trans_amt = 0;
                                        $pay_amount = $remaining_amt;
                                    }
                                    /*echo "Having Gateway balance, add gateway mode.<br/>";
                                    echo "actual_trans_amt : ".$actual_trans_amt."<br/>";
                                    echo "remaining_amt : ".$remaining_amt."<br/>";*/
                                    // Insert payment Mode details               
                                    $arrayPayMode = array(
                                        'payment_amount' => $mode_amount,
                                        'payment_date' => date("Y-m-d H:i:s"),
                                        'created_time' => date("Y-m-d H:i:s"),
                                        "payment_mode" => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "NET_BANKING" ? "NB" : $paymentMode))),
                                        "remark" => $txMsg . "[" . $txTime . "] mbl-status",
                                        "payment_ref_number" => $referenceId,
                                        "payment_status" => $updateData['payment_status']
                                    );
                                    if ($pay['due_type'] == 'GA') {
                                        $arrayPayMode['id_adv_payment'] = $pay['id_payment'];
                                        $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'general_advance_mode_detail');
                                    } else {
                                        $arrayPayMode['id_payment'] = $pay['id_payment'];
                                        $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'payment_mode_details');
                                    }
                                }
                                if (($remaining_amt > 0 || $actual_trans_amt == 0) && $act_wal_redeemed > 0) { // Having wallet redemption: add wallet mode & add debit transaction 
                                    if ($act_wal_redeemed > $pay_amount) {
                                        $act_wal_redeemed -= $pay_amount;
                                    } else if ($act_wal_redeemed == $pay_amount) {
                                        $act_wal_redeemed -= $pay_amount;
                                    }
                                    /*else if($act_wal_redeemed < $pay_amount){
                                        $remaining_amt		   = abs($act_wal_redeemed-$pay_amount);
                                        $act_wal_redeemed   = 0;
                                    }*/
                                    /*echo "Having wallet redemption : add wallet mode & add debit transaction.<br/>";
                                    echo "act_wal_redeemed : ".$act_wal_redeemed."<br/>";*/
                                    // Insert payment Mode details               
                                    $arrayPayMode = array(
                                        'payment_amount' => $pay_amount,
                                        'payment_date' => date("Y-m-d H:i:s"),
                                        'created_time' => date("Y-m-d H:i:s"),
                                        "payment_mode" => "REF_WALLET",
                                        "remark" => "Wallet Utilized",
                                        "payment_ref_number" => NULL,
                                        "payment_status" => 1
                                    );
                                    if ($pay['due_type'] == 'GA') {
                                        $arrayPayMode['id_adv_payment'] = $pay['id_payment'];
                                        $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'general_advance_mode_detail');
                                    } else {
                                        $arrayPayMode['id_payment'] = $pay['id_payment'];
                                        $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'payment_mode_details');
                                    }
                                    $WalletinsData = array(
                                        'id_wallet_account' => $pay['id_wallet_account'],
                                        'transaction_type' => 1, //0-Credit,1-Debit
                                        'type' => 0, //CRM
                                        'id_sch_ac' => $pay['id_scheme_account'],
                                        'value' => $pay_amount,
                                        'description' => 'Chit Redeem',
                                        'date_transaction' => date("Y-m-d H:i:s"),
                                        'id_employee' => NULL,
                                        'date_add' => date("Y-m-d H:i:s"),
                                        'credit_for' => 'Redeem',
                                        'id_payment' => $pay['id_payment']
                                    );
                                    $this->payment_modal->insertData($WalletinsData, 'wallet_transaction');
                                }
                                /*// Multi mode payment
                                if($updateData['payment_mode']!= NULL)
                                 {
                                     $arrayPayMode=array(
                                                    'id_payment'         => $pay['id_payment'],
                                                    'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                                                    'payment_date'		 => date("Y-m-d H:i:s"),
                                                    'created_time'	     => date("Y-m-d H:i:s"),
                                                    "payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))),
                                            // 		"payu_id"            => $referenceId,
                                                    "remark"             => $txMsg."[".$txTime."] mbl-status",
                                                    "payment_ref_number" => $referenceId,
                                                    "payment_status"     => ($txStatus==="SUCCESS" ? ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']: $this->payment_status['awaiting']) : ($txStatus==="CANCELLED" ? $this->payment_status['cancel'] :($txStatus==="FAILED" ? $this->payment_status['failure'] : $this->payment_status['pending']) ))
                                                    );
                                    if(!empty($arrayPayMode)){
                                        $cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details'); 
                                    }
                                 }*/
                                $schData = [];
                                $cusRegData = [];
                                $transData = [];
                                /* if($pay['allow_referral'] == 1){
                                     $ref_data	=	$this->payment_modal->get_refdata($pay['id_scheme_account']);
                                     $ischkref	=	$this->payment_modal->get_ischkrefamtadd($pay['id_scheme_account']);	
                                      if($ref_data['ref_benifitadd_ins_type'] == 1 && $ref_data['referal_code'] != '' && ($ref_data['ref_benifitadd_ins'] == $ref_data['paid_installments']) && $ischkref == TRUE){	
                                         $this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
                                     }else if($ref_data['ref_benifitadd_ins_type'] == 0 && $ref_data['referal_code'] != '' && $ischkref == TRUE){
                                         $this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
                                     }
                                  }*/
                                /*13-09-2022 Coded by haritha 
                                employee incentive credits based on installment settings*/
                                if ($pay['referal_code'] != '' && $pay['emp_refferal'] == 1) {
                                    $type = 1; //1- employee 2- agent
                                    $emp_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'], $pay['id_scheme_account'], $type, $pay['id_payment']);
                                    if ($emp_refral > 0) {
                                        $res = $this->insertEmployeeIncentive($emp_refral, $pay['id_scheme_account'], $pay['id_payment']);
                                        foreach ($emp_refral as $emp) {
                                            if ($emp['referal_amount'] > 0) {
                                                //$res = $this->insertEmployeeIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
                                                if ($emp['credit_for'] == 1) {
                                                    $this->customerIncentive($emp, $pay['id_scheme_account'], $pay['id_payment']);
                                                }
                                            }
                                        }
                                    }
                                }
                                // Generate receipt number
                                if ($pay['receipt_no_set'] == 1 && ($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2)) {
                                    // $this->db->query('LOCK TABLES payment WRITE');
                                    $receipt['receipt_no'] = $this->payment_modal->generate_receipt_no($pay['id_scheme'], $pay['branch']);
                                    $payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'], $receipt);
                                    // $this->db->query('UNLOCK TABLES');
                                }
                                // Generate Client ID
                                if ($pay['gent_clientid'] == 1 && $scheme_acc_number != NULL && empty($pay['ref_no'])) {
                                    $cliData = array(
                                        "cliID_short_code" => $this->config->item('cliIDcode'),
                                        "sync_scheme_code" => $pay['sync_scheme_code'],
                                        "code" => $pay['group_code'],
                                        "ac_no" => $scheme_acc_number
                                    );
                                    $schData['ref_no'] = $this->payment_modal->generateClientID($cliData);
                                    $cusRegData['ref_no'] = $schData['ref_no'];
                                    $transData['ref_no'] = $schData['ref_no'];
                                    $cusRegData['group_code'] = $pay['group_code'];
                                }
                                // Generate account  number  
                                if ($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set'] == 3) {
                                    if ($pay['scheme_acc_number'] == '' || $pay['scheme_acc_number'] == null || $pay['scheme_acc_number'] == 0) {
                                        $ac_group_code = NULL;
                                        // Lucky draw
                                        if ($pay['is_lucky_draw'] == 1) { // Based on scheme settings 
                                            // Update Group code in scheme_account table 
                                            $updCode = $this->payment_modal->updateGroupCode($pay);
                                            $ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
                                        }
                                        // $this->db->query('LOCK TABLES scheme_account WRITE');
                                        $scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'], $pay['branch'], $ac_group_code);
                                        if ($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0) {
                                            $schData['scheme_acc_number'] = $scheme_acc_number;
                                            $cusRegData['scheme_acc_number'] = $scheme_acc_number;
                                        } else {
                                            // $this->db->query('UNLOCK TABLES');
                                        }
                                    }
                                }
                                if ($pay['id_scheme_account'] > 0) {
                                    if (sizeof($schData) > 0) { // Update scheme account
                                        $this->payment_modal->update_account($schData, $pay['id_scheme_account']);
                                        // $this->db->query('UNLOCK TABLES');
                                    }
                                    if (sizeof($cusRegData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                        $this->payment_modal->update_cusreg($cusRegData, $pay['id_scheme_account']); // Update Customer reg - Client ID, Ac No
                                    }
                                    if (sizeof($transData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                        $this->payment_modal->update_trans($transData, $pay['id_scheme_account']); // Update Transaction - Client ID
                                    }
                                }
                                //Update First Payment Amount In Scheme Account
                                $approval_type = $this->config->item('auto_pay_approval');
                                if (($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable'] == 1 || $pay['firstPayamt_as_payamt'] == 1) && ($pay['firstPayment_amt'] == '' || $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0)) {
                                    if ($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "")) { // Fix First payable as weight
                                        $fixPayable = array('firstPayment_wgt' => $pay['metal_weight']);
                                    } else {
                                        $fixPayable = array('firstPayment_amt' => $pay['payment_amount']);
                                    }
                                    $status = $this->payment_modal->update_account($fixPayable, $pay['id_scheme_account']);
                                }
                                if ($this->config->item('auto_pay_approval') == 2 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                    if ($this->config->item('integrationType') == 1) {
                                        $this->insert_common_data_jil($pay['id_payment']);
                                    } else if ($this->config->item('integrationType') == 2) {
                                        $this->mobileapi_model->insert_common_data($pay['id_payment']);
                                    }
                                }
                                $service = $this->services_modal->checkService($serviceID);
                                if ($service['sms'] == 1) {
                                    $id = $pay['id_payment'];
                                    $data = $this->services_modal->get_SMS_data($serviceID, $id);
                                    $mobile = $data['mobile'];
                                    $message = $data['message'];
                                    $this->mobileapi_model->send_sms($mobile, $message, '', $service['dlt_te_id']);
                                }
                            }
                        }
                    }
                    /*if($service['email'] == 1 && isset($payData['email']) && $payData['email'] != '')
                    { 
                        $invoiceData = $this->payment_modal->get_invoiceDataM(isset($payment['id_payment'])?$payment['id_payment']:'');
                        $to = $payData['email'];
                        $subject = "Reg - ".$this->comp['company_name']." payment for the saving scheme";
                        $data['payData'] = $invoiceData[0];
                        $data['type'] = $type;
                        $data['company_details'] = $this->comp;
                        $message = $this->load->view('include/emailPayment',$data,true);
                        $sendEmail = $this->email_model->send_email($to,$subject,$message);	
                    } */
                }
                //redirect('paymt/hdfcTransStatus/success/'.$resData['txnid'].'/'.$resData['amount']);
                $response = array("status" => TRUE, "title" => "Transaction Completed", "msg" => $pay_msg);
            } else {
                //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
                $response = array("status" => TRUE, "title" => "Transaction Completed", "msg" => "Payment amount of INR " . $resData['amount'] . " paid successfully");
            }
        } else {
            //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
            $response = array("status" => TRUE, "title" => "Transaction Completed", "msg" => "Payment amount of INR " . $resData['amount'] . " paid successfully");
        }
        $this->response($response, 200);
    }
    // Cashfree SDK API's :: ENDS
    function insert_referral_data($id_scheme_account, $referral_data)
    {
        $status = FALSE;
        $serviceID = 16;
        if ($referral_data['referal_code'] != '' && $referral_data['is_refferal_by'] == 1) {
            $data = $this->payment_modal->get_empreferrals_datas($id_scheme_account);
        } else if ($referral_data['referal_code'] != '' && $referral_data['is_refferal_by'] == 0) {
            $data = $this->payment_modal->get_cusreferrals_datas($id_scheme_account);
        }
        if (!empty($data)) {
            if ($data['referal_code'] != '' && $data['referal_value'] != '' && $data['id_wallet_account'] != '') {
                // insert wallet transaction data //
                $wallet_data = array(
                    'id_wallet_account' => $data['id_wallet_account'],
                    'id_sch_ac' => $id_scheme_account,
                    'date_transaction' => date("Y-m-d H:i:s"),
                    'transaction_type' => 0,
                    'value' => $data['referal_value'],
                    'description' => 'Referral Benefits - ' . $data['cusname'] . ' ref no. ' . $data['id_scheme_account']
                );
                $status = $this->payment_modal->wallet_transactionDB($wallet_data);
                if ($status) {
                    // Update credit flag in customer table
                    /* is_refbenefit_crt = 0 -> already  benefit credited  & 1-> yet to credit benefits' */
                    if ($referral_data['is_refferal_by'] == 0 && $data['cusbenefitscrt_type'] == 0 && ($data['schrefbenifit_secadd'] == 0 || $data['schrefbenifit_secadd'] == 1)) {
                        // customer referal - single  
                        $this->payment_modal->update_customer_only(array('is_refbenefit_crt_cus' => 0), $chkreferral['id_customer']);
                    } else if ($referral_data['is_refferal_by'] == 0 && $data['cusbenefitscrt_type'] == 1 && $data['schrefbenifit_secadd'] == 1) {
                        // customer referal - multiple  
                        $this->payment_modal->update_customer_only(array('is_refbenefit_crt_cus' => 1), $chkreferral['id_customer']);
                    } else if ($referral_data['is_refferal_by'] == 1 && $data['empbenefitscrt_type'] == 0 && ($data['schrefbenifit_secadd'] == 0 || $data['schrefbenifit_secadd'] == 1)) {
                        // emp referal - single  					
                        $this->payment_modal->update_customer_only(array('is_refbenefit_crt_emp' => 0), $chkreferral['id_customer']);
                    } else if ($referral_data['is_refferal_by'] == 1 && $data['empbenefitscrt_type'] == 1 && $data['schrefbenifit_secadd'] == 1) {
                        // emp referal - single  			
                        $this->payment_modal->update_customer_only(array('is_refbenefit_crt_emp' => 1), $chkreferral['id_customer']);
                    }
                }
                $service = $this->services_modal->checkService($serviceID);
                if ($service['sms'] == 1 && $data['mobile']) {
                    $sms_data = $this->services_modal->get_SMS_data($serviceID, $data['id_scheme_account']);
                    if ($this->config->item('sms_gateway') == '1') {
                        $this->sms_model->sendSMS_MSG91($sms_data['mobile'], $sms_data['message'], '', $service['dlt_te_id']);
                    } elseif ($this->config->item('sms_gateway') == '2') {
                        $this->sms_model->sendSMS_Nettyfish($sms_data['mobile'], $sms_data['message'], 'trans');
                    } elseif ($this->config->item('sms_gateway') == '3') {
                        $this->sms_model->sendSMS_SpearUC($sms_data['mobile'], $sms_data['message'], '', $service['dlt_te_id']);
                    } elseif ($this->config->item('sms_gateway') == '4') {
                        $this->sms_model->sendSMS_Asterixt($sms_data['mobile'], $sms_data['message'], '', $service['dlt_te_id']);
                    } elseif ($this->config->item('sms_gateway') == '5') {
                        $this->sms_model->sendSMS_Qikberry($sms_data['mobile'], $sms_data['message'], '', $service['dlt_te_id']);
                    }
                }
            }
        }
    }
    function bookVideoSAppt_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $msg = "";
        $result = array('status' => FALSE, 'msg' => "");
        $prefered_lang = "";
        if (isset($data['video_lang'])) {
            if (sizeof($data['video_lang']) > 0) {
                $video_langs = [];
                foreach ($data['video_lang'] as $lang) {
                    array_push($video_langs, $lang->lan);
                }
                $prefered_lang = implode(',', $video_langs);
            } else {
                $prefered_lang = $data['video_lang'];
            }
        }
        if ($data['message'] == 'Gold Jewellery') {
            $pref_category = 1;
        } else if ($data['message'] == 'Silver Jewellery') {
            $pref_category = 2;
        } else if ($data['message'] == 'Platinum Jewellery') {
            $pref_category = 3;
        } else if ($data['message'] == 'Diamond Jewellery') {
            $pref_category = 4;
        } else {
            $pref_category = NULL;
        }
        $insData = array(
            'name' => $data['name'],
            'booking_date_iso' => (isset($data['booking_dateiso']) ? $data['booking_dateiso'] : NULL),
            'booking_date' => (isset($data['booking_date']) ? $data['booking_date'] : NULL),
            'email' => (isset($data['from']) ? $data['from'] : NULL),
            'mobile' => (isset($data['phone']) ? $data['phone'] : NULL),
            'location' => (isset($data['city']) ? $data['city'] : NULL),
            //'pref_category' => (isset($data['message']) && $data['message'] =='Gold Jewellery'? '1': $data['message'] =='Silver Jewellery'? '2' : $data['message'] == 'Diamond Jewellery' ? '4' : $data['message'] == 'Platinum Jewellery' ? '3' : NULL),
            'pref_category' => $pref_category,
            'prefered_lang' => (isset($data['video_lang']) ? $data['video_lang'] : NULL),
            'created_on' => date('Y-m-d H:i:s'),
            'pref_item' => (isset($data['additional_info']) ? $data['additional_info'] : NULL),
            'whats_app_no' => (isset($data['phone']) ? $data['phone'] : NULL),
            'booking_time' => (isset($data['booking_time']) ? $data['booking_time'] : NULL),
            'id_customer' => (isset($data['customer_id']) ? $data['customer_id'] : NULL),
        );
        $this->db->trans_begin();
        $insId = $this->$model->insertData($insData, 'appt_request');
        //print_r($insId);exit;
        $logdata = array(
            "id_appt_request" => $insId,
            "status" => (isset($insData['status']) ? $insData['status'] = 1 : 0),
            "event_date" => date('Y-m-d H:i:s'),
            "id_employee" => NULL,
            "remark" => NULL
        );
        $this->$model->insertData($logdata, 'appt_request_log');
        if ($this->db->trans_status() === TRUE) {
            //$this->load->model("admin_usersms_model");
            $this->db->trans_commit();
            $serv_code = 'VS_REQST';
            $id_service = '26';
            $service = $this->services_modal->checkService($id_service);
            if ($service['sms'] == 1) {
                $sms_data = $this->sms_model->SMS_dataByServCode('VS_REQST', $insId);
                $message = $sms_data['message'];
                if ($this->config->item('sms_gateway') == '1') {
                    $sms = $this->sms_model->sendSMS_MSG91($sms_data['mobile'], $message, '', $service['dlt_te_id']);
                } elseif ($this->config->item('sms_gateway') == '2') {
                    $sms = $this->sms_model->sendSMS_Nettyfish($sms_data['mobile'], $message, 'trans', $service['dlt_te_id']);
                } elseif ($this->config->item('sms_gateway') == '3') {
                    $sms = $this->sms_model->sendSMS_SpearUC($sms_data['mobile'], $message, '', $service['dlt_te_id']);
                } elseif ($this->config->item('sms_gateway') == '4') {
                    $sms = $this->sms_model->sendSMS_Asterixt($sms_data['mobile'], $message, '', $service['dlt_te_id']);
                } elseif ($this->config->item('sms_gateway') == '5') {
                    $sms = $this->sms_model->sendSMS_Qikberry($sms_data['mobile'], $message, '', $service['dlt_te_id']);
                }
            }
            //ADMIN ALERT
            $adm_serv_code = 'ADM_VIDEO_ALERT';
            $id_service = '42';
            $adm_service = $this->services_modal->checkService($id_service);
            if ($adm_service['sms'] == 1) {
                $adm_sms_data = $this->sms_model->SMS_dataByServCode('ADM_VIDEO_ALERT', $insId);
                $adm_message = $adm_sms_data['message'];
                if ($this->config->item('sms_gateway') == '1') {
                    $sms = $this->sms_model->sendSMS_MSG91($adm_sms_data['admin_mobile'], $adm_message, '', $adm_service['dlt_te_id']);
                } elseif ($this->config->item('sms_gateway') == '2') {
                    $sms = $this->sms_model->sendSMS_Nettyfish($adm_sms_data['admin_mobile'], $adm_message, 'trans', $adm_service['dlt_te_id']);
                } elseif ($this->config->item('sms_gateway') == '3') {
                    $sms = $this->sms_model->sendSMS_SpearUC($adm_sms_data['admin_mobile'], $adm_message, '', $adm_service['dlt_te_id']);
                } elseif ($this->config->item('sms_gateway') == '4') {
                    $sms = $this->sms_model->sendSMS_Asterixt($adm_sms_data['admin_mobile'], $adm_message, '', $adm_service['dlt_te_id']);
                } elseif ($this->config->item('sms_gateway') == '5') {
                    $sms = $this->sms_model->sendSMS_Qikberry($adm_sms_data['admin_mobile'], $adm_message, '', $adm_service['dlt_te_id']);
                }
            }
            if ($data['from'] != '') {
                //	$this->load->model('sms_model');
                $cusServData = $this->sms_model->SMS_dataByServCode('VS_REQST', $insId);
                // print_r($cusServData);exit;
                $to = $data['from'];
                $subject = "Reg - " . $this->comp['company_name'] . " video shopping appointment";
                $email_data['mailData'] = $cusServData['cus_data'];
                $email_data['type'] = 1;
                $email_data['company_details'] = $this->comp;
                $message = $this->load->view('include/vs_appt', $email_data, true);
                //	print_r($message);exit;
                $sendEmail = $this->email_model->send_email($to, $subject, $message, "", "");
            }
            $remark = 'Appointment Requested';
            $msg = 'Thanks for requesting Video Shopping, our team will contact you shortly.';
            $result = array('status' => TRUE, 'title' => "Appointment Booked", 'msg' => $msg, "id_appt_request" => $insId);
        } else {
            $this->db->trans_rollback();
            $result = array('status' => FALSE, 'title' => "Booking Failed", 'msg' => "Unable to proceed your request. Please contact admin..");
        }
        $this->response($result, 200);
    }
    function CustomerOrderDetails_post()
    {
        $model = self::MOD_MOB;
        $postData = $this->get_values();
        if ($postData['id_customer'] != '') {
            $res = $this->$model->get_customer_order($postData['id_customer']);
            $responsedata = array('status' => true, 'orders' => $res);
            $this->response($responsedata, 200);
        } else {
            $responsedata = array("status" => FALSE, "msg" => "Please Select Customer..");
            $this->response($responsedata, 200);
        }
    }
    /*Incentive Functions  Coded by Haritha - 13-9-22*/
    function insertEmployeeIncentive($refdata, $id_scheme_account, $id_payment)
    {
        $status = FALSE;
        $chkreferral = $this->payment_modal->get_referral_code($id_scheme_account);
        $data = array();
        foreach ($refdata as $ref) {
            $checkCreditExist = $this->payment_modal->checkCreditTransExist($id_scheme_account, $id_payment);
            if ($checkCreditExist == 0) {
                if ($chkreferral['referal_code'] != '' && $chkreferral['is_refferal_by'] == 1) {
                    $data = $this->payment_modal->get_empreferrals_datas($id_scheme_account);
                }
            }
            if (!empty($data) && $chkreferral['is_refferal_by'] == 1 && $checkCreditExist == 0) {
                if ($data['referal_code'] != '' && $ref['referal_amount'] != '' && $data['id_wallet_account'] != '' && $data['id_wallet_account'] > 0) {
                    // insert wallet transaction data //
                    $wallet_data = array(
                        'id_wallet_account' => $data['id_wallet_account'],
                        'id_sch_ac' => $id_scheme_account,
                        'date_transaction' => date("Y-m-d H:i:s"),
                        //	'id_employee'      =>  $this->session->userdata('uid'),
                        'id_employee' => $data['idemployee'],
                        'transaction_type' => 0,
                        'value' => $ref['referal_amount'],
                        'id_payment' => $id_payment,
                        'credit_for' => $ref['credit_remark'],
                        'description' => 'Referral Benefits - ' . $data['cusname'] . ''
                    );
                    //	echo"<pre>"; print_r($wallet_data);exit;
                    $status = $this->payment_modal->wallet_transactionDB($wallet_data);
                }
            }
        }
        return true;
    }
    function customerIncentive($refdata, $id_scheme_account, $id_payment)
    {
        $chkreferral = $this->payment_modal->get_referral_code($id_scheme_account);
        //credit customer introduce staff incentive
        if ($chkreferral['is_refferal_by'] == 0) {
            // customer referal - multiple  
            $this->payment_modal->update_customer_only(array('is_refbenefit_crt_cus' => 1), $chkreferral['id_customer']);
            //check customer intro staff incentive
            $isEmpRef = $this->payment_modal->get_empRefExist_datas($id_scheme_account);
            if (sizeof($isEmpRef) > 0) {
                //print_r($refdata);exit;
                if ($refdata['referal_amount'] != '' && $isEmpRef['id_wallet_account'] != null) {
                    // insert wallet transaction data //
                    $wallet_data = array(
                        'id_wallet_account' => $isEmpRef['id_wallet_account'],
                        'id_sch_ac' => NULL,
                        'date_transaction' => date("Y-m-d H:i:s"),
                        'id_employee' => null,
                        'transaction_type' => 0,
                        'value' => $refdata['referal_amount'],
                        'id_payment' => $id_payment,
                        'credit_for' => 'Customer Intro Scheme Incentive',
                        'description' => 'Customer Intro Referral Benefits - ' . $isEmpRef['cusname'] . ''
                    );
                    //	echo"<pre>"; print_r($wallet_data);exit;
                    $status = $this->payment_modal->wallet_transactionDB($wallet_data);
                    //echo $this->db->last_query();exit;
                }
            }
        }
    }
    /*ends*/
    public function oldrazorResponse_post()
    {
        /*Sample Response 
        {"status":true,"params":{"publicKey":"rzp_test_dUtzlnBZmd6X89","secretKey":"AWmcyW0TCyyX2wW2Z3YXMeQY","transaction":"166868340963761691e67c5",
        "orderAmount":1000000,"orderCurrency":"INR","orderNote":"Online Money Transaction","customerName":"Krishna G","customerEmail":"krishna@vikashinfosolutions.com",
        "customerPhone":"","razorData":{"id":"order_Kh5IRyTcQl9niG","entity":"order","amount":1000000,"amount_paid":0,"amount_due":1000000,
        "currency":"INR","receipt":"166868340963761691e67c5","offer_id":null,"status":"created","attempts":0,
        "notes":{"notes_key_1":"test2"},"created_at":1668683411}}}
        */
        $postData = $this->get_values();
        $publicKey = $postData['params']->publicKey;                                                               //'pk_live_MXZi5igA2W4Y';
        $secretKey = $postData['params']->secretKey;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.razorpay.com/v1/payments/" . $postData['payment_id'],
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => "$publicKey:$secretKey",
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => $data,
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            //echo "cURL Error #:" . $err;exit;
        } else {
            $gatewayResponse = json_decode($response, true);
        }
        $trans_id = $postData['params']->transaction;
        $txStatus = $postData['params']->razorData->status;
        $orderAmount = isset($postData['params']->razorData->amount) ? ($postData['params']->razorData->amount) / 100 : NULL;
        $referenceId = isset($postData['params']->razorData->id) ? $postData['params']->razorData->id : NULL;
        $paymentMode = $gatewayResponse['method'];
        $txMsg = "Online Money Transaction";
        $txTime = isset($postData['params']->razorData->created_at) ? $postData['params']->razorData->created_at : NULL;
        // $computedSignature = base64_encode($hash_hmac); 
        // Write log 
        if (!is_dir($this->log_dir . '/cashfree')) {
            mkdir($this->log_dir . '/cashfree', 0777, true);
        }
        $log_path = $this->log_dir . '/cashfree/mob_response_' . date("Y-m-d") . '.txt';
        $ldata = "\n" . date('d-m-Y H:i:s') . " \n Mobile : " . json_encode($postData, true);
        file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
        if (!empty($trans_id) && $trans_id != NULL) {
            $pay = $this->payment_modal->getWalletPaymentContent($trans_id);
            $updateData = array(
                "payment_mode" => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "NET_BANKING" ? "NB" : (!empty($paymentMode) ? $paymentMode : 'UPI')))),
                "payu_id" => $postData['payment_id'],
                "remark" => $txMsg . "[" . $txTime . "] mbl-status",
                "payment_ref_number" => $referenceId,
                "payment_status" => ($txStatus === "SUCCESS" ? ($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success'] : $this->payment_status['awaiting']) : ($txStatus === "CANCELLED" ? $this->payment_status['cancel'] : ($txStatus === "FAILED" ? $this->payment_status['failure'] : $this->payment_status['pending'])))
            );
            $payment = $this->payment_modal->updateGatewayResponse($updateData, $trans_id);
            if ($payment['status'] == true) {
                $serviceID = 0;
                $pay_msg = "Your payment has been " . $txStatus;
                if ($txStatus === "SUCCESS") {
                    $serviceID = 3;
                    $type = 2;
                    $pay_msg = 'Transaction ID: ' . $trans_id . '. Payment amount INR. ' . $orderAmount . ' is paid successfully. Thanks for your payment with ' . $this->comp['company_name'];
                } else if ($txStatus === "FAILED") {
                    $serviceID = 7;
                    $type = -1;
                    $pay_msg = 'Your payment has been failed. Please try again. Transaction ID: ' . $trans_id;
                } else if ($txStatus === "CANCELLED") {
                    $serviceID = 7;
                    $type = 3;
                    $pay_msg = 'Your payment has been cancelled.';
                }
                if ($serviceID > 0) {
                    if ($txStatus === "SUCCESS") {
                        $payIds = $this->payment_modal->getPayIds($trans_id);
                        if (sizeof($payIds) > 0) {
                            $updData = array("last_payment_on" => NULL);
                            $this->payment_modal->updData($updData, 'id_customer', $payIds[0]['id_customer'], 'customer');
                            // Wallet Debit :: Haritha's code altered by Pavithra on 27-8-2024 [Debit splitup]
                            $actual_trans_amt = $payIds[0]['actual_trans_amt'];
                            $act_wal_redeemed = $payIds[0]['redeemed_amount'];
                            $remaining_amt = 0;
                            $remaining_wal_amt = 0;
                            $updData = array("last_payment_on" => NULL);
                            $this->payment_modal->updData($updData, 'id_customer', $payIds[0]['id_customer'], 'customer');
                            if ($act_wal_redeemed > 0) {
                                // Update payment table - payment mode as multi
                                $this->payment_modal->updPayModeBRefTranID(["payment_mode" => 'MULTI'], $trans_id);
                            }
                            foreach ($payIds as $pay) {
                                $tax = 0;
                                $pay_amount = 0;
                                if ($pay['gst_type'] == 1) { // Exclusive
                                    $tax = $pay['gst_amount'];
                                }
                                $pay_amount = ($pay['payment_amount'] - $pay['discountAmt'] + $tax);
                                $mode_amount = $pay_amount;
                                if ($actual_trans_amt > 0) { // Having Gateway balance, add gateway mode
                                    if ($actual_trans_amt > $pay_amount) {
                                        $actual_trans_amt -= $pay_amount;
                                    } else if ($actual_trans_amt == $pay_amount) {
                                        $actual_trans_amt -= $pay_amount;
                                    } else if ($actual_trans_amt < $pay_amount) {
                                        $mode_amount = $actual_trans_amt;
                                        $remaining_amt = abs($actual_trans_amt - $pay_amount);
                                        $actual_trans_amt = 0;
                                        $pay_amount = $remaining_amt;
                                    }
                                    /*echo "Having Gateway balance, add gateway mode.<br/>";
                                    echo "actual_trans_amt : ".$actual_trans_amt."<br/>";
                                    echo "remaining_amt : ".$remaining_amt."<br/>";*/
                                    // Insert payment Mode details               
                                    $arrayPayMode = array(
                                        'payment_amount' => $mode_amount,
                                        'payment_date' => date("Y-m-d H:i:s"),
                                        'created_time' => date("Y-m-d H:i:s"),
                                        "payment_mode" => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "NET_BANKING" ? "NB" : (!empty($paymentMode) ? $paymentMode : 'UPI')))),
                                        "remark" => $txMsg . "[" . $txTime . "] mbl-status",
                                        "payment_ref_number" => $referenceId,
                                        "payment_status" => $updateData['payment_status']
                                    );
                                    if ($pay['due_type'] == 'GA') {
                                        $arrayPayMode['id_adv_payment'] = $pay['id_payment'];
                                        $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'general_advance_mode_detail');
                                    } else {
                                        $update_pmd = array(
                                            'payment_status' => 9, // Cancelled
                                            "updated_time" => date('Y-m-d H:i:s'),
                                            "remark" => "Removed while direct success on razorpay " . date('Y-m-d H:i:s'),
                                        );
                                        $update_existing_pmd = $this->payment_modal->updData($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');
                                        $arrayPayMode['id_payment'] = $pay['id_payment'];
                                        $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'payment_mode_details');
                                    }
                                }
                                if (($remaining_amt > 0 || $actual_trans_amt == 0) && $act_wal_redeemed > 0) { // Having wallet redemption: add wallet mode & add debit transaction 
                                    if ($act_wal_redeemed > $pay_amount) {
                                        $act_wal_redeemed -= $pay_amount;
                                    } else if ($act_wal_redeemed == $pay_amount) {
                                        $act_wal_redeemed -= $pay_amount;
                                    }
                                    /*else if($act_wal_redeemed < $pay_amount){
                                        $remaining_amt		   = abs($act_wal_redeemed-$pay_amount);
                                        $act_wal_redeemed   = 0;
                                    }*/
                                    /*echo "Having wallet redemption : add wallet mode & add debit transaction.<br/>";
                                    echo "act_wal_redeemed : ".$act_wal_redeemed."<br/>";*/
                                    // Insert payment Mode details               
                                    $arrayPayMode = array(
                                        'payment_amount' => $pay_amount,
                                        'payment_date' => date("Y-m-d H:i:s"),
                                        'created_time' => date("Y-m-d H:i:s"),
                                        "payment_mode" => "REF_WALLET",
                                        "remark" => "Wallet Utilized",
                                        "payment_ref_number" => NULL,
                                        "payment_status" => 1
                                    );
                                    if ($pay['due_type'] == 'GA') {
                                        $arrayPayMode['id_adv_payment'] = $pay['id_payment'];
                                        $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'general_advance_mode_detail');
                                    } else {
                                        $update_pmd = array(
                                            'payment_status' => 9, // Cancelled
                                            "updated_time" => date('Y-m-d H:i:s'),
                                            "remark" => "Removed while direct success on razorpay " . date('Y-m-d H:i:s'),
                                        );
                                        $update_existing_pmd = $this->payment_modal->updData($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');
                                        $arrayPayMode['id_payment'] = $pay['id_payment'];
                                        $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'payment_mode_details');
                                    }
                                    $WalletinsData = array(
                                        'id_wallet_account' => $pay['id_wallet_account'],
                                        'transaction_type' => 1, //0-Credit,1-Debit
                                        'type' => 0, //CRM
                                        'id_sch_ac' => $pay['id_scheme_account'],
                                        'value' => $pay_amount,
                                        'description' => 'Chit Redeem',
                                        'date_transaction' => date("Y-m-d H:i:s"),
                                        'id_employee' => NULL,
                                        'date_add' => date("Y-m-d H:i:s"),
                                        'credit_for' => 'Redeem',
                                        'id_payment' => $pay['id_payment']
                                    );
                                    $this->payment_modal->insertData($WalletinsData, 'wallet_transaction');
                                }
                                /*// Multi mode payment
                                if($updateData['payment_mode']!= NULL)
                                 {
                                     $arrayPayMode=array(
                                                    'id_payment'         => $pay['id_payment'],
                                                    'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                                                    'payment_date'		 => date("Y-m-d H:i:s"),
                                                    'created_time'	     => date("Y-m-d H:i:s"),
                                                    "payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))),
                                                     "payu_id"            => $postData['payment_id'],
                                                    "remark"             => $txMsg."[".$txTime."] mbl-status",
                                                    "payment_ref_number" => $referenceId,
                                                    "payment_status"     => ($txStatus==="SUCCESS" ? ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']: $this->payment_status['awaiting']) : ($txStatus==="CANCELLED" ? $this->payment_status['cancel'] :($txStatus==="FAILED" ? $this->payment_status['failure'] : $this->payment_status['pending']) ))
                                                    );
                                    if(!empty($arrayPayMode)){
                                        $cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details'); 
                                    }
                                 }*/
                                $schData = [];
                                $cusRegData = [];
                                $transData = [];
                                /*13-09-2022 Coded by haritha 
                                employee incentive credits based on installment settings*/
                                if ($pay['referal_code'] != '' && $pay['emp_refferal'] == 1) {
                                    $type = 1; //1- employee 2- agent
                                    $emp_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'], $pay['id_scheme_account'], $type, $pay['id_payment']);
                                    if ($emp_refral > 0) {
                                        $res = $this->insertEmployeeIncentive($emp_refral, $pay['id_scheme_account'], $pay['id_payment']);
                                        foreach ($emp_refral as $emp) {
                                            if ($emp['referal_amount'] > 0) {
                                                //$res = $this->insertEmployeeIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
                                                if ($emp['credit_for'] == 1) {
                                                    $this->customerIncentive($emp, $pay['id_scheme_account'], $pay['id_payment']);
                                                }
                                            }
                                        }
                                    }
                                }
                                // Generate receipt number
                                if ($pay['receipt_no_set'] == 1 && ($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2)) {
                                    // $this->db->query('LOCK TABLES payment WRITE');
                                    $receipt['receipt_no'] = $this->payment_modal->generate_receipt_no($pay['id_scheme'], $pay['branch']);
                                    $payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'], $receipt);
                                    // $this->db->query('UNLOCK TABLES');
                                }
                                // Generate account  number  
                                if ($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set'] == 3) {
                                    if ($pay['scheme_acc_number'] == '' || $pay['scheme_acc_number'] == null || $pay['scheme_acc_number'] == 0) {
                                        $ac_group_code = NULL;
                                        // Lucky draw
                                        if ($pay['is_lucky_draw'] == 1) { // Based on scheme settings 
                                            // Update Group code in scheme_account table 
                                            $updCode = $this->payment_modal->updateGroupCode($pay);
                                            $ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
                                        }
                                        // $this->db->query('LOCK TABLES scheme_account WRITE');
                                        $scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'], $pay['branch'], $ac_group_code);
                                        if ($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0) {
                                            $schData['scheme_acc_number'] = $scheme_acc_number;
                                            $cusRegData['scheme_acc_number'] = $scheme_acc_number;
                                        } else {
                                            // $this->db->query('UNLOCK TABLES');
                                        }
                                    }
                                }
                                // Generate Client ID
                                if ($pay['gent_clientid'] == 1 && $scheme_acc_number != NULL && empty($pay['ref_no'])) {
                                    $cliData = array(
                                        "cliID_short_code" => $this->config->item('cliIDcode'),
                                        "sync_scheme_code" => $pay['sync_scheme_code'],
                                        "code" => $pay['group_code'],
                                        'id_branch' => $pay['branch'],
                                        "ac_no" => $scheme_acc_number
                                    );
                                    $schData['ref_no'] = $this->payment_modal->generateClientID($cliData);
                                    $cusRegData['ref_no'] = $schData['ref_no'];
                                    $transData['ref_no'] = $schData['ref_no'];
                                    $cusRegData['group_code'] = $pay['group_code'];
                                }
                                if ($pay['id_scheme_account'] > 0) {
                                    if (sizeof($schData) > 0) { // Update scheme account
                                        $this->payment_modal->update_account($schData, $pay['id_scheme_account']);
                                        // $this->db->query('UNLOCK TABLES');
                                    }
                                    if (sizeof($cusRegData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                        $this->payment_modal->update_cusreg($cusRegData, $pay['id_scheme_account']); // Update Customer reg - Client ID, Ac No
                                    }
                                    if (sizeof($transData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                        $this->payment_modal->update_trans($transData, $pay['id_scheme_account']); // Update Transaction - Client ID
                                    }
                                }
                                //Update First Payment Amount In Scheme Account
                                $approval_type = $this->config->item('auto_pay_approval');
                                if (($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable'] == 1 || $pay['firstPayamt_as_payamt'] == 1) && ($pay['firstPayment_amt'] == '' || $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0)) {
                                    if ($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "")) { // Fix First payable as weight
                                        $fixPayable = array('firstPayment_wgt' => $pay['metal_weight']);
                                    } else {
                                        $fixPayable = array('firstPayment_amt' => $pay['payment_amount']);
                                    }
                                    $status = $this->payment_modal->update_account($fixPayable, $pay['id_scheme_account']);
                                }
                                if ($this->config->item('auto_pay_approval') == 2 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                    if ($this->config->item('integrationType') == 1) {
                                        $this->insert_common_data_jil($pay['id_payment']);
                                    } else if ($this->config->item('integrationType') == 2) {
                                        $this->mobileapi_model->insert_common_data($pay['id_payment']);
                                    }
                                }
                                $service = $this->services_modal->checkService($serviceID);
                                if ($service['sms'] == 1) {
                                    $id = $pay['id_payment'];
                                    $data = $this->services_modal->get_SMS_data($serviceID, $id);
                                    $mobile = $data['mobile'];
                                    $message = $data['message'];
                                    //$this->mobileapi_model->send_sms($mobile,$message,'',$service['dlt_te_id']);
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
                            }
                        }
                    }
                }
                //redirect('paymt/hdfcTransStatus/success/'.$resData['txnid'].'/'.$resData['amount']);
                $response = array("status" => TRUE, "title" => "Transaction " . $txStatus . "", "msg" => $pay_msg);
            } else {
                //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
                $response = array("status" => TRUE, "title" => "Transaction " . $txStatus . "", "msg" => "Payment amount of INR " . $orderAmount . " paid successfully");
            }
        } else {
            //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
            $response = array("status" => TRUE, "title" => "Transaction " . $txStatus . "", "msg" => "Payment amount of INR " . $orderAmount . " paid successfully");
        }
        $this->response($response, 200);
    }
    // Cashfree SDK API's :: ENDS
    function digidata_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $chit = $this->$model->getdigidata(array('mobile' => $data['mobile'], 'id_customer' => $data['id_customer']));
        $this->response($chit, 200);
    }
    function oldcreatePin_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $cus = array('pin_no' => $data["pin"]);
        $result = $this->$model->updData($cus, 'id_customer', $data['id_customer'], 'customer');
        if ($result > 0) {
            $message = array("status" => TRUE, "msg" => "PIN generated Successfully..");
        } else {
            $message = array("status" => FALSE, "msg" => "Unable to porceed your request");
        }
        $this->response($message, 200);
    }
    function createPin_post()
    {
        $model = self::MOD_MOB;
        $data  = $this->get_values();
        $newPin = $data["pin"];
        // Check existing pin for the customer
        $existing = $this->db->select('pin_no')
            ->from('customer')
            ->where('id_customer', $data['id_customer'])
            ->get()
            ->row();
        if ($existing && $existing->pin_no == $newPin) {
            // Same as old pin
            $message = array("status" => FALSE, "msg" => "New PIN should not be same as previous PIN");
        } else {
            // Update only if new pin is different
            $cus    = array('pin_no' => $newPin);
            $result = $this->$model->updData($cus, 'id_customer', $data['id_customer'], 'customer');
            if ($result > 0) {
                $message = array("status" => TRUE, "msg" => "PIN generated Successfully..");
            } else {
                $message = array("status" => FALSE, "msg" => "Unable to proceed your request");
            }
        }
        $this->response($message, 200);
    }
    function getvalidate_pin_post()
    {
        $model = self::MOD_MOB;
        $data = $this->get_values();
        $status = $this->$model->isValidPin($data["pin"], $data["id_customer"], $data["mobile"]);
        $result = array(
            'mobile' => $data['mobile'],
            'is_valid' => ($status ? $status : FALSE),
            'pin' => TRUE
            //'is_pin_required' => 
        );
        if ($result['is_valid']) {
            $result['customer'] = $this->$model->get_customerByMobile($data['mobile']);
            $result['accounts'] = $this->$model->countSchemes($result['customer']['id_customer']);
            $result['payments'] = $this->$model->countPayments($result['customer']['id_customer']);
            $result['wallet'] = $this->$model->countWallets($result['customer']['id_customer']);
            $result['notification'] = $this->$model->get_cus_noti_settings($result['customer']['id_customer']);
            $result["msg"] = "Login Success..";
        } else {
            $result["pin"] = FALSE;
            $result["msg"] = "Invalid PIN no..";
        }
        $this->response($result, 200);
    }
    function getVillage_get()
    {
        $model = self::MOD_MOB;
        $village = $this->$model->get_allvillage($this->get('id_city'));
        $this->response($village, 200);
    }
    function delete_customer_post()
    {
        $model = self::MOD_MOB;
        $postData = $this->get_values();
        $cus_id = $postData['id_customer'];
        $data = $this->$model->inActiveCustomer($cus_id);
        $this->response($data, 200);
    }
    //DCNM-DGS   
    public function chit_detail_report_get($id_scheme_account)
    {
        $this->comp = $this->login_model->company_details();
        $this->load->helper(array('dompdf', 'file'));
        $data['sch'] = $this->scheme_modal->get_chit_data($id_scheme_account);
        $intData = $this->scheme_modal->get_chit_int($data['sch']);
        $intData['id_scheme_account'] = $data['sch']['id_scheme_account'];
        $data['interest'] = $intData;
        $data['payData'] = $this->scheme_modal->chit_detail_report($intData);
        if ($this->branch_settings == 1) {
            $data['comp_details'] = $this->login_model->get_branchcompany($data['sch']['id_branch']);
        } else {
            $data['comp_details'] = $this->scheme_modal->get_company();
        }
        $dompdf = new DOMPDF();
        $html = $this->load->view('include/chit_detail_report', $data, true);
        $dompdf->load_html($html);
        $dompdf->set_paper("a4", "portriat");
        $dompdf->render();
        $dompdf->stream("chit_detail_report.pdf", array('Attachment' => 0));
    }
    function getLanguage_get()
    {
        $path = base_url() . 'api/language.json';
        // Read the JSON file  
        $json = file_get_contents($path);
        // Decode the JSON file 
        $json_data = json_decode($json, true);
        // Display data 
        echo json_encode($json_data);
        //$this->response($json_data,200);
    }
    function pinsearchArea_post()
    {
        $model = self::MOD_MOB;
        $postData = $this->get_values();
        $pincode = $postData['pincode'];
        $data = $this->$model->getAreabypincode($pincode);
        $this->response($data, 200);
    }
    public function appContentLangWise_get()
    {
        $path = base_url() . 'api/language.json';
        // Read the JSON file  
        $json = file_get_contents($path);
        // Decode the JSON file 
        $json_data = json_decode($json, true);
        // Display data 
        $this->response($json_data, 200);
    }
    /* Cash Free version4 .... new api's... coded by : haritha _____ Date Updated : 30-01-2024, Updated By:AB*/
    public function generateCashfreeSession($txnid, $orderAmount, $params, $paycred)
    {
        $secretKey = $paycred['param_1']; //Shared by Cashfree  
        $appId = $paycred['param_3'];
        $order_tags = [];
        foreach ($params['id_payments'] as $index => $id) {
            $order_tags["payment_" . ($index + 1)] = (string)$id;
        }
        $postData = array(
            "customer_details" => array(
                "customer_id" => $params['id_customer'],
                "customer_email" => $params['customerEmail'],
                "customer_phone" => $params['customerPhone'],
                "customer_name" => $params['customerName']
            ),
            "order_tags" => $order_tags,
            "order_id" => $txnid,
            "order_amount" => number_format($orderAmount, 2, '.', ''),
            "order_currency" => "INR"
        );
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $paycred['api_url'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "content-type: application/json",
                "x-api-version: 2023-08-01",
                "x-client-id: " . $appId,
                "x-client-secret: " . $secretKey
            ],
        ]);
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            //return json_decode($response);
            $result['response'] = json_decode($response);
            $result['environment'] = $paycred['type'] == 0 ? 'SANDBOX' : 'PRODUCTION';
            return $result;
        }
    }
    // 	public function cashfreeResponse_post()
    // 	{
    // 	    $postData = $this->get_values();
    // 	    // CREATE LOG
    // if (!is_dir($this->log_dir.'/cashfree')) {
    //     mkdir($this->log_dir.'/cashfree', 0777, true);
    // }
    // $log_path = $this->log_dir.'/cashfree/mob_response.txt';
    // $lg_data = "\n Received DATA --".date('Y-m-d H:i:s')." -- : ".json_encode($postData)." \n";
    // file_put_contents($log_path,$lg_data,FILE_APPEND | LOCK_EX);
    // 	    $pg_data = $this->mobileapi_model->getPgdataBycode(4);
    // 	    //echo '<pre>';print_r($pg_data);exit;
    // 	    $curl = curl_init();
    //         curl_setopt_array($curl, [
    //           CURLOPT_URL => $pg_data['api_url']."pg/orders/".$postData['orderID']."/payments",
    //           CURLOPT_RETURNTRANSFER => true,
    //           CURLOPT_ENCODING => "",
    //           CURLOPT_MAXREDIRS => 10,
    //           CURLOPT_TIMEOUT => 30,
    //           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //           CURLOPT_CUSTOMREQUEST => "GET",
    //           CURLOPT_HTTPHEADER => [
    //             "accept: application/json",
    //             "content-type: application/json",
    //             "x-api-version: 2023-08-01",
    //             "x-client-id: ".$pg_data['param_3'],
    //             "x-client-secret: ".$pg_data['param_1']
    //           ],
    //         ]);
    //         $g_response = curl_exec($curl);
    //         $err = curl_error($curl);
    //         curl_close($curl);
    //         if ($err) {
    //           $response = "cURL Error #:" . $err;
    //         } else {
    //                            $result = json_decode($g_response);
    //                            $res = $result[0];
    //                            $trans_id      = $postData["orderID"];
    //                         $txStatus      = $res->payment_status; 
    //                         $orderAmount   = isset($res->order_amount) ? $res->order_amount : NULL;
    //                         $referenceId   = isset($res->cf_payment_id) ? $res->cf_payment_id : NULL;
    //                         $paymentMode   = isset($res->payment_group) ? $res->payment_group : NULL;
    //                         $txMsg         = isset($res->payment_message) ? $res->payment_message : NULL;
    //                         $txTime        = isset($res->payment_completion_time) ? $res->payment_completion_time : NULL;
    //         if(!empty($trans_id) && $trans_id != NULL)
    //         {
    //     	    $updateData = array( 
    //     						"payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":($paymentMode == "NA" ? "-":$paymentMode)))),
    //     						"payu_id"            => $referenceId,
    //     						"remark"             => $txMsg."[".$txTime."] mbl-status",
    //     						"payment_ref_number" => $referenceId,
    //     						"payment_status"     => (strtolower($txStatus)==="success" ? ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']: $this->payment_status['awaiting']) : (strtolower($txStatus)==="userCancelled" ? $this->payment_status['cancel'] :($txStatus==="failure" ? $this->payment_status['failure'] : $this->payment_status['pending']) ))
    //     					    ); 
    // 			$payment = $this->payment_modal->updateGatewayResponse($updateData,$trans_id);   
    //     		if($payment['status'] == true)
    //     		{
    //     		    $serviceID = 0;
    //     		    $pay_msg = "Your payment has been ".$txStatus;
    //         		if(strtolower($txStatus) === "success")
    //         		{
    //         		    $serviceID = 3; 
    //         		    $type = 2;
    //         		    $pay_msg = 'Transaction ID: '.$trans_id.'. Payment amount INR. '.$orderAmount.' is paid successfully. Thanks for your payment with '.$this->comp['company_name'];
    //         	    }
    //         		else if(strtolower($txStatus) === "failure")
    //         		{ 
    //         		    $serviceID = 7; 
    //         		    $type = -1;
    //         		    $pay_msg = 'Your payment has been failed. Please try again. Transaction ID: '.$trans_id;
    //         		}
    //         		else if(strtolower($txStatus) === "userCancelled")
    //         		{ 
    //         		    $serviceID = 7; 
    //         		    $type = 3;
    //         		    $pay_msg = 'Your payment has been cancelled.';
    //         		}
    //         		if($serviceID > 0){
    //         		    if(strtolower($txStatus)==="success")
    //         		    {   
    //         		        $payIds = $this->payment_modal->getPayIds($trans_id);
    //     					if(sizeof($payIds) > 0)
    //     					{
    //     						foreach ($payIds as $pay)
    //     						{
    //     						    // Multi mode payment
    //     						    if($updateData['payment_mode']!= NULL)
    //                  				{
    //                 					    if($pay['due_type'] == 'GA'){
    //                 					        $arrayPayMode=array(
    //                     								'id_adv_payment'         => $pay['id_payment'],
    //                 							        'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
    //                     								'payment_date'		 => date("Y-m-d H:i:s"),
    //                     								'created_time'	     => date("Y-m-d H:i:s"),
    //                     								"payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))),
    //                             						"remark"             => $txMsg."[".$txTime."] mbl-status",
    //                             						"payment_ref_number" => $referenceId,
    //                             						"payment_status"     => 1
    //                             					    );
    //                 					        $cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'general_advance_mode_detail');
    //                 					    }else{
    //                 					        $arrayPayMode=array(
    //                     								'id_payment'         => $pay['id_payment'],
    //                 							        'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
    //                     								'payment_date'		 => date("Y-m-d H:i:s"),
    //                     								'created_time'	     => date("Y-m-d H:i:s"),
    //                     								"payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))),
    //                             						"remark"             => $txMsg."[".$txTime."] mbl-status",
    //                             						"payment_ref_number" => $referenceId,
    //                             						"payment_status"     => 1
    //                             					    );
    //                 						    $cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details'); 
    //                 					    }
    //                  				}
    //     						    $schData = [];
    //     						    $cusRegData = [];
    //     						    $transData = [];
    //                 			if($pay['due_type'] != 'GA'){
    //                 			 	//13-09-2022 Coded by haritha 
    //                 			 	//employee incentive credits based on installment settings
    //                                 if($pay['referal_code']!='' && $pay['emp_refferal'] == 1)
    //                                 {
    //                                     $type=1; //1- employee 2- agent
    //                                     $emp_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'],$pay['id_scheme_account'],$type,$pay['id_payment']);
    //                 		            if($emp_refral > 0)
    //                 		            {
    //                 		                $res = $this->insertEmployeeIncentive($emp_refral,$pay['id_scheme_account'],$pay['id_payment']);
    //                 		                foreach($emp_refral as $emp)
    //                 		                {
    //                         			 	    if($emp['referal_amount'] > 0)
    //                         			 	    {
    //                         			 	        //$res = $this->insertEmployeeIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
    //                         			 	        if($emp['credit_for'] == 1)
    //                         			 	        {
    //                         			 	            $this->customerIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
    //                         			 	        }
    //                     			 	         }
    //                 		                }
    //                 		            }
    //                                 }
    //     							// Generate account  number  
    //     							if($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set']==3)
    //     							{
    //     								if($pay['scheme_acc_number'] == '' ||  $pay['scheme_acc_number'] == null || $pay['scheme_acc_number'] == 0)
    //     								{
    //     								    $ac_group_code = NULL;
    //     									// Lucky draw
    //     									if($pay['is_lucky_draw'] == 1 ){ // Based on scheme settings 
    //     										// Update Group code in scheme_account table 
    //     										$updCode = $this->payment_modal->updateGroupCode($pay); 
    //     										$ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
    //     									}
    //     									// $this->db->query('LOCK TABLES scheme_account WRITE');
    //     									$scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'],$pay['branch'],$ac_group_code); 
    //     									if($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0){
    //     										$schData['scheme_acc_number'] = $scheme_acc_number;
    //     										$cusRegData['scheme_acc_number'] = $scheme_acc_number;
    //     									}else{
    // 											// $this->db->query('UNLOCK TABLES');
    // 										}
    //     								}
    //     							}
    //         						// Generate receipt number
    //     							if($pay['receipt_no_set'] == 1 && ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2) )
    //     							{ 
    //     								// $this->db->query('LOCK TABLES payment WRITE');
    //     								$receipt['receipt_no'] = $this->payment_modal->generate_receipt_no($pay['id_scheme'],$pay['branch']);
    //     								$payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'],$receipt);
    //     								// $this->db->query('UNLOCK TABLES');
    //     							}
    //     							if($pay['id_scheme_account'] > 0){
    //         							if(sizeof($schData) > 0){ // Update scheme account
    //         								$this->payment_modal->update_account($schData,$pay['id_scheme_account']);
    //         								// $this->db->query('UNLOCK TABLES');
    //         							}
    //         							if(sizeof($cusRegData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)){ 
    //                                         $this->payment_modal->update_cusreg($cusRegData,$pay['id_scheme_account']); // Update Customer reg - Client ID, Ac No
    //         							}
    //         							if(sizeof($transData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)){ 
    //                                         $this->payment_modal->update_trans($transData,$pay['id_scheme_account']); // Update Transaction - Client ID
    //         							}
    //     							}
    //     							//Update First Payment Amount In Scheme Account
    //     							$approval_type = $this->config->item('auto_pay_approval');
    //     						//	print_r($approval_type);exit;
    //         						if(($approval_type == 1 || $approval_type == 2 || $approval_type == 3))
    //         						{
    //         							if(($pay['flexible_sch_type'] == 4 || $pay['flexible_sch_type'] == 8) && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "") && $pay['firstPayment_as_wgt'] == 1)
    //         							{ // Fix First payable as weight
    //     									$fixPayable = array('firstPayment_wgt'  =>  $pay['metal_weight']);
    //     								}
    //     								else if(($pay['firstPayamt_maxpayable']==1 || $pay['firstPayamt_as_payamt']==1) && ($pay['firstPayment_amt'] == '' ||  $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0))
    //     								{
    //     									$fixPayable = array('firstPayment_amt'  =>  $pay['payment_amount'] );
    //     								}
    //         							if(sizeof($fixPayable) > 0){    //AB
    //         								$status = $this->payment_modal->update_account($fixPayable,$pay['id_scheme_account']);
    //         							}
    //         						}
    //     							if( $this->config->item('auto_pay_approval') == 2 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3))
    //     							{ 	 
    //                                     if($this->config->item('integrationType') == 1){
    //                                         $this->insert_common_data_jil($pay['id_payment']);
    //                                     }else if($this->config->item('integrationType') == 2){
    //                                         $this->mobileapi_model->insert_common_data($pay['id_payment']);
    //                                     }  		            
    //         		        		}
    //                 			}	
    //                 			 $service = $this->services_modal->checkService($serviceID); 
    //                 			if($service['sms'] == 1)
    //                 			{
    //                 				$id=$pay['id_payment'];
    //                 				$data =$this->services_modal->get_SMS_data($serviceID,$id);
    //                 				$mobile =$data['mobile'];
    //                 				$message = $data['message'];
    //                 				$this->mobileapi_model->send_sms($mobile,$message,'',$service['dlt_te_id']);
    //                 			}
    //     						}
    //     					}
    //         		    }
    //         		}  
    // 			    //redirect('paymt/hdfcTransStatus/success/'.$resData['txnid'].'/'.$resData['amount']);
    //         	    $response = array("status" => TRUE, "title" => "Transaction ".$txStatus, "msg" => $pay_msg);
    //             }else{ 
    //                 //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
    //                 $response = array("status" => TRUE, "title" => "Transaction ".$txStatus, "msg" => "Unable to proceed your payment.."); 
    //             } 
    //         }else{ 
    //             //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
    //             $response = array("status" => TRUE, "title" => "Transaction ".$txStatus, "msg" => "Your payment has been Cancelled");
    //         }
    //         }
    //         $this->response($response,200); 
    // 	}   
    public function old_cashfreeResponse_post()
    {
        $postData = $this->get_values();
        /*$id_branch = $this->mobileapi_model->getBranchByOrderId($postData['orderID']);
        $pg_data = $this->mobileapi_model->getPgdataBycode(4,$id_branch['id_branch']);*/
        //$getBranchByOrderID = $this->db->query("SELECT id_branch from payment where id_transaction like '%".$postData['orderID']."%'")->row()->id_branch;
        $getidPaymentOrderID = $this->db->query("SELECT id_payGateway from payment where id_transaction like '%" . $postData['orderID'] . "%'")->row()->id_payGateway;
        $pg_data = $this->mobileapi_model->getPgdataBycode(4, $getidPaymentOrderID);
        // CREATE LOG
        if (!is_dir($this->log_dir . '/cashfree')) {
            mkdir($this->log_dir . '/cashfree', 0777, true);
        }
        $log_path = $this->log_dir . '/cashfree/mob_response.txt';
        $lg_data = "\n Received DATA --" . date('Y-m-d H:i:s') . " -- : " . json_encode($postData) . " \n";
        file_put_contents($log_path, $lg_data, FILE_APPEND | LOCK_EX);
        //$pg_data = $this->mobileapi_model->getPgdataBycode(4);
        //echo '<pre>';print_r($pg_data);exit;
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $pg_data['api_url'] . $postData['orderID'] . "/payments",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "content-type: application/json",
                "x-api-version: 2023-08-01",
                "x-client-id: " . $pg_data['param_3'],
                "x-client-secret: " . $pg_data['param_1']
            ],
        ]);
        $g_response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            $response = "cURL Error #:" . $err;
        } else {
            // $result = json_decode($g_response);
            $result = (array) json_decode($g_response);
            $res = $result[0]; //echo '<pre>';print_r($result);exit;
            $trans_id = $postData["orderID"];
            $txStatus = (!empty($res->payment_status) ? $res->payment_status : 'Cancelled');
            $orderAmount = isset($res->order_amount) ? $res->order_amount : NULL;
            $referenceId = isset($res->cf_payment_id) ? $res->cf_payment_id : NULL;
            $paymentMode = isset($res->payment_group) ? $res->payment_group : NULL;
            $txMsg = isset($res->payment_message) ? $res->payment_message : NULL;
            $txTime = isset($res->payment_completion_time) ? $res->payment_completion_time : NULL;
            if (!empty($trans_id) && $trans_id != NULL) {
                $updateData = array(
                    //"payment_mode" => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "NET_BANKING" ? "NB" : (!empty($paymentMode) ? $paymentMode : 'UPI')))),
                    "payment_mode"       => (strtolower($paymentMode) == "credit_card" ? "CC" : (strtolower($paymentMode) == "debit_card" ? "DC" : (strtolower($paymentMode) == "net_banking" ? "NB" : (strtolower($paymentMode) == "na" ? "NA" : $this->update_paymentMode($paymentMode))))),
                    "payu_id" => $referenceId,
                    "remark" => $txMsg . "[" . $txTime . "] mbl-status",
                    "payment_ref_number" => $referenceId,
                    "payment_status" => (strtolower($txStatus) === "success" ? ($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success'] : $this->payment_status['awaiting']) : (strtolower($txStatus) === "userCancelled" ? $this->payment_status['cancel'] : ($txStatus === "failure" ? $this->payment_status['failure'] : $this->payment_status['pending'])))
                );
                $payment = $this->payment_modal->updateGatewayResponse($updateData, $trans_id);
                if ($payment['status'] == true) {
                    $serviceID = 0;
                    $pay_msg = "Your payment has been " . $txStatus;
                    //print_r($txStatus);exit;
                    if (strtolower($txStatus) === "success") {
                        $serviceID = 3;
                        $type = 2;
                        $pay_msg = 'Payment of INR. ' . $orderAmount . ' successfull. Txn ID: ' . $trans_id . '.  Thank you - ' . $this->comp['company_name'];
                    } else if (strtolower($txStatus) === "failure") {
                        $serviceID = 7;
                        $type = -1;
                        $pay_msg = 'Your payment has been failed. Please try again. Transaction ID: ' . $trans_id;
                    } else if (strtolower($txStatus) === "userCancelled") {
                        $serviceID = 7;
                        $type = 3;
                        $pay_msg = 'Your payment has been cancelled.';
                    }
                    if ($serviceID > 0) {
                        if (strtolower($txStatus) === "success") {
                            $payIds = $this->payment_modal->getPayIds($trans_id);
                            if (sizeof($payIds) > 0) {
                                // Wallet Debit :: Haritha's code altered by Pavithra on 27-8-2024 [Debit splitup]
                                $actual_trans_amt = $payIds[0]['actual_trans_amt'];
                                $act_wal_redeemed = $payIds[0]['redeemed_amount'];
                                $remaining_amt = 0;
                                $remaining_wal_amt = 0;
                                if ($act_wal_redeemed > 0) {
                                    // Update payment table - payment mode as multi
                                    $this->payment_modal->updPayModeBRefTranID(["payment_mode" => 'MULTI'], $trans_id);
                                }
                                foreach ($payIds as $pay) {
                                    $tax = 0;
                                    $pay_amount = 0;
                                    if ($pay['gst_type'] == 1) { // Exclusive
                                        $tax = $pay['gst_amount'];
                                    }
                                    $pay_amount = ($pay['payment_amount'] - $pay['discountAmt'] + $tax);
                                    $mode_amount = $pay_amount;
                                    if ($actual_trans_amt > 0) { // Having Gateway balance, add gateway mode
                                        if ($actual_trans_amt > $pay_amount) {
                                            $actual_trans_amt -= $pay_amount;
                                        } else if ($actual_trans_amt == $pay_amount) {
                                            $actual_trans_amt -= $pay_amount;
                                        } else if ($actual_trans_amt < $pay_amount) {
                                            $mode_amount = $actual_trans_amt;
                                            $remaining_amt = abs($actual_trans_amt - $pay_amount);
                                            $actual_trans_amt = 0;
                                            $pay_amount = $remaining_amt;
                                        }
                                        /*echo "Having Gateway balance, add gateway mode.<br/>";
                                        echo "actual_trans_amt : ".$actual_trans_amt."<br/>";
                                        echo "remaining_amt : ".$remaining_amt."<br/>";*/
                                        // Insert payment Mode details               
                                        $arrayPayMode = array(
                                            'payment_amount' => $mode_amount,
                                            // 'payment_date' => date("Y-m-d H:i:s"),
                                            'payment_date'         => $pay['date_payment'],
                                            "payment_mode"       => $updateData['payment_mode'],
                                            'created_time' => date("Y-m-d H:i:s"),
                                            /* "payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":(!empty($paymentMode) ? $paymentMode : 'UPI')))), */
                                            //"payment_mode" => (strtolower($paymentMode) == "credit_card" ? "CC" : (strtolower($paymentMode) == "debit_card" ? "DC" : (strtolower($paymentMode) == "net_banking" ? "NB" : (strtolower($paymentMode) == "na" ? "-" : "UPI")))),
                                            "remark" => $txMsg . "[" . $txTime . "] mbl-status",
                                            "payment_ref_number" => $referenceId,
                                            "payment_status" => $updateData['payment_status']
                                        );
                                        if ($pay['due_type'] == 'GA') {
                                            $arrayPayMode['id_adv_payment'] = $pay['id_payment'];
                                            $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'general_advance_mode_detail');
                                        } else {
                                            $update_pmd = array(
                                                'payment_status' => 9, // Cancelled
                                                "updated_time" => date('Y-m-d H:i:s'),
                                                "remark" => "Removed while direct success on cashfree " . date('Y-m-d H:i:s'),
                                            );
                                            $update_existing_pmd = $this->payment_modal->updData($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');
                                            $arrayPayMode['id_payment'] = $pay['id_payment'];
                                            $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'payment_mode_details');
                                        }
                                    }
                                    if (($remaining_amt > 0 || $actual_trans_amt == 0) && $act_wal_redeemed > 0) { // Having wallet redemption: add wallet mode & add debit transaction 
                                        if ($act_wal_redeemed > $pay_amount) {
                                            $act_wal_redeemed -= $pay_amount;
                                        } else if ($act_wal_redeemed == $pay_amount) {
                                            $act_wal_redeemed -= $pay_amount;
                                        }
                                        /*else if($act_wal_redeemed < $pay_amount){
                                            $remaining_amt		   = abs($act_wal_redeemed-$pay_amount);
                                            $act_wal_redeemed   = 0;
                                        }*/
                                        /*echo "Having wallet redemption : add wallet mode & add debit transaction.<br/>";
                                        echo "act_wal_redeemed : ".$act_wal_redeemed."<br/>";*/
                                        // Insert payment Mode details               
                                        $arrayPayMode = array(
                                            'payment_amount' => $pay_amount,
                                            // 'payment_date' => date("Y-m-d H:i:s"),
                                            'payment_date'         => $pay['date_payment'],
                                            'created_time' => date("Y-m-d H:i:s"),
                                            "payment_mode" => "REF_WALLET",
                                            "remark" => "Wallet Utilized",
                                            "payment_ref_number" => NULL,
                                            "payment_status" => 1
                                        );
                                        if ($pay['due_type'] == 'GA') {
                                            $arrayPayMode['id_adv_payment'] = $pay['id_payment'];
                                            $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'general_advance_mode_detail');
                                        } else {
                                            $update_pmd = array(
                                                'payment_status' => 9, // Cancelled
                                                "updated_time" => date('Y-m-d H:i:s'),
                                                "remark" => "Removed while direct success on cashfree " . date('Y-m-d H:i:s'),
                                            );
                                            $update_existing_pmd = $this->payment_modal->updData($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');
                                            $arrayPayMode['id_payment'] = $pay['id_payment'];
                                            $payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'payment_mode_details');
                                        }
                                        $WalletinsData = array(
                                            'id_wallet_account' => $pay['id_wallet_account'],
                                            'transaction_type' => 1, //0-Credit,1-Debit
                                            'type' => 0, //CRM
                                            'id_sch_ac' => $pay['id_scheme_account'],
                                            'value' => $pay_amount,
                                            'description' => 'Chit Redeem',
                                            'date_transaction' => date("Y-m-d H:i:s"),
                                            'id_employee' => NULL,
                                            'date_add' => date("Y-m-d H:i:s"),
                                            'credit_for' => 'Redeem',
                                            'id_payment' => $pay['id_payment']
                                        );
                                        $this->payment_modal->insertData($WalletinsData, 'wallet_transaction');
                                    }
                                    /*
                                    // Multi mode payment
                                    if($updateData['payment_mode']!= NULL)
                                     {
                                            if($pay['due_type'] == 'GA'){
                                                $arrayPayMode=array(
                                                        'id_adv_payment'         => $pay['id_payment'],
                                                        'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                                                        'payment_date'		 => date("Y-m-d H:i:s"),
                                                        'created_time'	     => date("Y-m-d H:i:s"),
                                                        "payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))),
                                                        "remark"             => $txMsg."[".$txTime."] mbl-status",
                                                        "payment_ref_number" => $referenceId,
                                                        "payment_status"     => 1
                                                        );
                                                $cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'general_advance_mode_detail');
                                            }else{
                                                $arrayPayMode=array(
                                                        'id_payment'         => $pay['id_payment'],
                                                        'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                                                        'payment_date'		 => date("Y-m-d H:i:s"),
                                                        'created_time'	     => date("Y-m-d H:i:s"),
                                                        "payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))),
                                                        "remark"             => $txMsg."[".$txTime."] mbl-status",
                                                        "payment_ref_number" => $referenceId,
                                                        "payment_status"     => 1
                                                        );
                                                $cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details'); 
                                            }
                                     }*/
                                    $schData = [];
                                    $cusRegData = [];
                                    $transData = [];
                                    if ($pay['due_type'] != 'GA') {
                                        //13-09-2022 Coded by haritha 
                                        //employee incentive credits based on installment settings
                                        if ($pay['referal_code'] != '' && $pay['emp_refferal'] == 1) {
                                            $type = 1; //1- employee 2- agent
                                            $emp_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'], $pay['id_scheme_account'], $type, $pay['id_payment']);
                                            if ($emp_refral > 0) {
                                                $res = $this->insertEmployeeIncentive($emp_refral, $pay['id_scheme_account'], $pay['id_payment']);
                                                foreach ($emp_refral as $emp) {
                                                    if ($emp['referal_amount'] > 0) {
                                                        //$res = $this->insertEmployeeIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
                                                        if ($emp['credit_for'] == 1) {
                                                            $this->customerIncentive($emp, $pay['id_scheme_account'], $pay['id_payment']);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        // Generate account  number  
                                        if ($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set'] == 3) {
                                            if ($pay['scheme_acc_number'] == '' || $pay['scheme_acc_number'] == null || $pay['scheme_acc_number'] == 0) {
                                                $ac_group_code = NULL;
                                                // Lucky draw
                                                if ($pay['is_lucky_draw'] == 1) { // Based on scheme settings 
                                                    // Update Group code in scheme_account table 
                                                    $updCode = $this->payment_modal->updateGroupCode($pay);
                                                    $ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
                                                }
                                                // $this->db->query('LOCK TABLES scheme_account WRITE');
                                                $scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'], $pay['branch'], $ac_group_code);
                                                if ($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0) {
                                                    $schData['scheme_acc_number'] = $scheme_acc_number;
                                                    $cusRegData['scheme_acc_number'] = $scheme_acc_number;
                                                } else {
                                                    // $this->db->query('UNLOCK TABLES');
                                                }
                                            }
                                        }
                                        // Generate receipt number
                                        if ($pay['receipt_no_set'] == 1 && ($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2)) {
                                            // $this->db->query('LOCK TABLES payment WRITE');
                                            $receipt['receipt_no'] = $this->payment_modal->generate_receipt_no($pay['id_scheme'], $pay['branch']);
                                            $payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'], $receipt);
                                            // $this->db->query('UNLOCK TABLES');
                                        }
                                        if ($pay['id_scheme_account'] > 0) {
                                            if (sizeof($schData) > 0) { // Update scheme account
                                                $this->payment_modal->update_account($schData, $pay['id_scheme_account']);
                                                // $this->db->query('UNLOCK TABLES');
                                            }
                                            if (sizeof($cusRegData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                                $this->payment_modal->update_cusreg($cusRegData, $pay['id_scheme_account']); // Update Customer reg - Client ID, Ac No
                                            }
                                            if (sizeof($transData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                                $this->payment_modal->update_trans($transData, $pay['id_scheme_account']); // Update Transaction - Client ID
                                            }
                                        }
                                        //Update First Payment Amount In Scheme Account
                                        $approval_type = $this->config->item('auto_pay_approval');
                                        //	print_r($approval_type);exit;
                                        if (($approval_type == 1 || $approval_type == 2 || $approval_type == 3)) {
                                            if (($pay['flexible_sch_type'] == 4 || $pay['flexible_sch_type'] == 8) && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "") && $pay['firstPayment_as_wgt'] == 1) { // Fix First payable as weight
                                                $fixPayable = array('firstPayment_wgt' => $pay['metal_weight']);
                                            } else if (($pay['firstPayamt_maxpayable'] == 1 || $pay['firstPayamt_as_payamt'] == 1) && ($pay['firstPayment_amt'] == '' || $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0)) {
                                                $fixPayable = array('firstPayment_amt' => $pay['payment_amount']);
                                            }
                                            if (sizeof($fixPayable) > 0) {    //AB
                                                $status = $this->payment_modal->update_account($fixPayable, $pay['id_scheme_account']);
                                            }
                                        }
                                        //RHR scheme starts...		
                                        $dt_pay = date('Y-m-d', strtotime(str_replace("/", "-", $pay['date_payment'])));
                                        $ins_cycle = $this->mobileapi_model->get_due_date($pay['due_type'], $dt_pay, $pay['id_scheme_account']);
                                        //	print_r($this->db->last_query());exit;
                                        if (sizeof($ins_cycle[0]) > 0) {
                                            $cycle_data = array(
                                                'due_date' => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
                                                'due_date_to' => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
                                                'grace_date' => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
                                                'installment' => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
                                                'is_limit_exceed' => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
                                            );
                                            $this->mobileapi_model->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');
                                        }
                                        //RHR scheme ends....
                                        //update paid installments against account...
                                        $paid = $this->payment_modal->getPaidInsData($pay['id_scheme_account']);
                                        if (sizeof($paid) > 0) {
                                            $paid_ins = array('total_paid_ins' => $paid['paid_installments']);
                                            $this->payment_modal->updData($paid_ins, 'id_scheme_account', $pay['id_scheme_account'], 'scheme_account');
                                        }
                                        if ($this->config->item('auto_pay_approval') == 2 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                            if ($this->config->item('integrationType') == 1) {
                                                $this->insert_common_data_jil($pay['id_payment']);
                                            } else if ($this->config->item('integrationType') == 2) {
                                                $this->mobileapi_model->insert_common_data($pay['id_payment']);
                                            }
                                        }
                                    }
                                    $service = $this->services_modal->checkService($serviceID);
                                    if ($service['sms'] == 1) {
                                        $id = $pay['id_payment'];
                                        $data = $this->services_modal->get_SMS_data($serviceID, $id);
                                        $mobile = $data['mobile'];
                                        $message = $data['message'];
                                        if ($this->config->item('sms_gateway') == '4') {
                                            $this->sms_model->sendSMS_Asterixt($mobile, $message, '', $dtl_te_id);
                                        }
                                        // $this->mobileapi_model->send_sms($mobile,$message,'',$service['dlt_te_id']);
                                    }
                                }
                            }
                        }
                    }
                    //redirect('paymt/hdfcTransStatus/success/'.$resData['txnid'].'/'.$resData['amount']);
                    $response = array("status" => TRUE, "title" => "Transaction " . $txStatus, "msg" => $pay_msg);
                } else {
                    //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
                    $response = array("status" => TRUE, "title" => "Transaction " . $txStatus, "msg" => "Unable to proceed your payment..");
                }
            } else {
                //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
                $response = array("status" => TRUE, "title" => "Transaction " . $txStatus, "msg" => "Your payment has been Cancelled");
            }
        }
        $this->response($response, 200);
    }
    /* Cash Free version4 ends */
    public function generateInvoice_get($payment_no, $id_scheme_account)
    {
        $model = self::MOD_MOB;
        $data['records'] = $this->$model->get_ReceiptPrintData($payment_no, $id_scheme_account);
        //print_r($this->branch_settings);exit;
        if ($this->branch_settings == 1) {
            $data['comp_details'] = $this->login_model->get_branchcompany($data['records'][0]['id_branch']);
        } else {
            $data['comp_details'] = $this->scheme_modal->get_company();
        }
        $data['records'][0]['amount_in_words'] = $this->no_to_words($data['records'][0]['payment_amount']);
        //print_r($data);exit;
        //for custom receipt type
        $this->load->helper(array('dompdf', 'file'));
        $dompdf = new DOMPDF();
        //$html = $this->load->view('include/receipt1', $data,true);
        $html = $this->load->view('include/receipt_pdf', $data, true);
        //  $html = $this->load->view('include/chit_detail_report', $data,true);
        echo $html;
        exit;
        $dompdf->load_html($html);
        $customPaper = array(0, 0, 210, 400);
        $dompdf->set_paper($customPaper, "portriat");
        $dompdf->render();
        $dompdf->stream("receipt1.pdf", array('Attachment' => 0));
    }
    function no_to_words1($nos1 = "")
    {
        $words = array('0' => '', '1' => 'One', '2' => 'Two', '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six', '7' => 'Seven', '8' => 'Eight', '9' => 'Nine', '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve', '13' => 'Thirteen', '14' => 'Fouteen', '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen', '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty', '30' => 'Thirty', '40' => 'Fourty', '50' => 'Fifty', '60' => 'Sixty', '70' => 'Seventy', '80' => 'Eighty', '90' => 'Ninty', '100' => 'Hundred &', '1000' => 'Thousand', '100000' => 'Lakh', '10000000' => 'Crore');
        $nos[0] = $nos1;
        if ($nos[0] == 0)
            return '';
        else {
            $novalue = '';
            $highno = $nos[0];
            $remainno = 0;
            $value = 100;
            $value1 = 1000;
            $temp = '';
            while ($nos[0] >= 100) {
                if (($value <= $nos[0]) && ($nos[0] < $value1)) {
                    $novalue = $words["$value"];
                    $highno = (int) ($nos[0] / $value);
                    $remainno = $nos[0] % $value;
                    break;
                }
                $value = $value1;
                $value1 = $value * 100;
            }
            if (array_key_exists("$highno", $words)) {
                return $words["$highno"] . " " . $novalue . " " . $this->no_to_words1($remainno);
            } else {
                $unit = $highno % 10;
                $ten = (int) ($highno / 10) * 10;
                return $words["$ten"] . " " . $words["$unit"] . " " . $novalue . " " . $this->no_to_words1($remainno);
            }
        }
    }
    function no_to_words($no = "")
    {
        $nos = explode('.', $no);
        $val1 = "";
        $val2 = "";
        $val = "";
        if (isset($nos[0])) {
            $val1 = $this->no_to_words1($nos[0]);
            $val = $val1 . " Rupees";
        }
        if (isset($nos[1]) && $nos[1] != 0) {
            $val2 = $this->no_to_words1($nos[1]);
            if (isset($val2))
                $val = $val1 . " Rupees and" . " " . $val2 . " Paisa";
        }
        return $val;
    }
    public function joinTime_weight_slabs_get()
    {
        $model = self::MOD_MOB;
        $id_scheme = $this->get('id_scheme');
        $data = $this->$model->get_joinTime_weight_slabs($id_scheme);
        $this->response($data, 200);
    }
    public function get_settings_get()
    {
        $sql = "SELECT CONCAT(SUBSTRING_INDEX(vs_booking_time, '-', 1), 'AM') AS start_time, CONCAT(SUBSTRING_INDEX(vs_booking_time, '-', -1), 'PM') AS end_time FROM chit_settings;";
        $result = $this->db->query($sql);
        $result = $result->row_array();
        $this->response($result, 200);
    }
    function videoshopComplaints_get()
    {
        $model = self::MOD_MOB;
        $result = $this->$model->get_vshopComplaints($this->get('id_customer'));
        $this->response($result, 200);
    }
    function videoshopComplaints_status_get()
    {
        $model = self::MOD_MOB;
        $result = $this->$model->videoshopComplaints_status($this->get('id_appt_request'));
        $this->response($result, 200);
    }
    function old_insert_common_data($id_payment)
    {
        $model = self::API_MODEL;
        $this->load->model($model);
        //getting payment detail
        $pay_data = $this->$model->getPaymentByID($id_payment);
        //storing temp values
        $ref_no = $pay_data[0]['ref_no'];
        $id_scheme_account = $pay_data[0]['id_scheme_account'];
        $isCusRegExists = $this->$model->checkCusRegExists($id_scheme_account, $ref_no);
        if (!$isCusRegExists['status']) {
            $reg = $this->$model->getCustomerByID($id_scheme_account);
            //insert customer registration detail
            if ($reg) {
                $reg[0]['record_to'] = 1;
                $reg[0]['is_registered_online'] = 2;  // 2 - online record
                $reg[0]['ref_no'] = $ref_no;
                $status = $this->$model->insert_CustomerReg($reg[0]);
                //echo $this->db->last_query();exit;
            }
            //echo $this->db->last_query();exit;
        }
        $isTranExists = $this->$model->checkTransExists($ref_no);
        if (!$isTranExists) {
            //insert payment detail
            $pay_data[0]['record_to'] = 1;
            $pay_data[0]['payment_type'] = 1;    // 1 - online 
            $status = $this->$model->insert_transaction($pay_data[0]);
        }
        //echo $this->db->last_query();exit;
        return true;
    }
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

    function test_get()
    {
        $serviceID = 1;
        $id = 1;
        /*$sms_data = array(
                    "fname"     => "Pavithra",
                    "otp"       => 123456,
                    "expiry"    => "12-12-2024 11:10:22"
                    ); 
        //$smsData = $this->services_modal->get_SMS_OTPdata(23,$sms_data);
        //$smsData = ["message" => "", "template_name", "params" => ""];*/
        $smsData = $this->services_modal->get_SMS_data($serviceID, $id);
        $mobile = 7010198473;
        //echo "<pre>";print_r($smsData); 
        $this->services_modal->send_whatsApp_message($mobile, $smsData);
    }
    //RAHUl CASH FREE NEW 24/07
    function update_paymentMode($mode)
    {
        $mode = strtolower(trim($mode));
        // Check if mode_name or short_code already matches (case-insensitive)
        $this->db->from('payment_mode');
        $this->db->where('LOWER(short_code)', $mode);
        $query = $this->db->get();
        $short_code = strtoupper($mode); // e.g., upi_payment -> UPI_PAYMENT
        if ($query->num_rows() == 0 && !empty($mode)) {
            // Convert mode to proper format
            $formatted_mode_name = ucwords(str_replace('_', ' ', $mode)); // e.g., upi_payment -> Upi Payment
            // Insert new payment mode
            $data = array(
                'mode_name'   => $formatted_mode_name,
                'short_code'  => $short_code,
                'status'      => 1,
                'sort_order'  => 0,
                'show_in_pay' => 0
            );
            $this->db->insert('payment_mode', $data);
        }
        return $short_code;
    }
    public function cashfreeResponse_post()
    {
        $response = array("status" => TRUE, "title" => "PAYMENT PROCESS", "msg" => "Your payment is currently being processed, please wait for some time, thank you for your patience...");
        $this->response($response, 200);
    }
    public function easebuzzResponse_post()
    {
        $response = array("status" => TRUE, "title" => "PAYMENT PROCESS", "msg" => "Your payment is currently being processed, please wait for some time, thank you for your patience...");
        $this->response($response,200); 
	}
    public function razorResponse_post()
    {
        $response = array("status" => TRUE, "title" => "PAYMENT PROCESS", "msg" => "Your payment is currently being processed, please wait for some time, thank you for your patience...");
        $this->response($response,200); 
    }
    function sendtoDirectApi($api, $postData)
    {
        $url = $this->config->item('directAPIurl') . $api;
        $payLoad[] = $postData;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payLoad),
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

    function commodityForDigi_get(){
        $model = self::MOD_MOB;
        $data = $this->$model->commodityForDigi();
        $this->response($data, 200);
    }

    public function kyc_exists($cus_id) {
        $this->db->from('kyc');
        $this->db->where('id_customer', $cus_id);
        $this->db->where('kyc_type', 2);
		$sql = $this->db->get();
        return $sql->num_rows() > 0;
    }

	/* ═══════════════════════════════════════════════════════════════
	 * Cashfree Auto-Debit Subscription (New API — v2025-01-01)
	 * 
	 * POST mobile_api/cf_subscription
	 * Payload: {type: 1|2, id_sch_ac: INT, plan_amount (optional for weight-based)}
	 * type 1 = Subscribe, type 2 = Unsubscribe
	 * 
	 * Uses scheme_modal model methods:
	 *   get_plan_detail(), get_subscriptionData(), insertData(), updateData()
	 * ═══════════════════════════════════════════════════════════════ */

	function cf_subscription_post()
	{
		$post_data = $this->get_values();
		$type      = isset($post_data['type']) ? $post_data['type'] : '';
		$id_sch_ac = isset($post_data['id_sch_ac']) ? $post_data['id_sch_ac'] : '';

		if (empty($type) || empty($id_sch_ac)) {
			echo json_encode(array('status' => false, 'msg' => 'Missing required parameters (type, id_sch_ac)'));
			return;
		}

		switch ($type) {
			case '1': // ─── Subscribe ───────────────────────────────
				$planDetail = $this->scheme_modal->get_plan_detail($id_sch_ac);

				if (empty($planDetail) || sizeof($planDetail) == 0) {
					echo json_encode(array('status' => false, 'msg' => 'No record found...'));
					return;
				}

				// Guard: Already subscribed
				if ($planDetail['id_auto_debit_subscription'] > 0) {
					echo json_encode(array(
						'status' => false,
						'msg'    => 'Already subscription created, Kindly check the authorization status...'
					));
					return;
				}

				$pendingIns = $planDetail['total_installments'] - $planDetail['paid_installments'];

				if ($pendingIns <= 0) {
					echo json_encode(array('status' => false, 'msg' => 'You have already paid all your installments...'));
					return;
				}

				// Determine plan amount based on scheme_type
				// scheme_type: 0=Amount, 1=Weight, 2=Amount to Weight, 3=Flexible Amount
				$plan_amount = 0;
				if ($planDetail['scheme_type'] == 0 || $planDetail['scheme_type'] == 2) {
					$plan_amount = floatval($planDetail['scheme_amount']);
				} elseif ($planDetail['scheme_type'] == 3) {
					$plan_amount = floatval($planDetail['min_amount']);
				} else {
					// Weight-based — amount from POST (app calculates based on rate × weight)
					$plan_amount = isset($post_data['plan_amount']) ? floatval($post_data['plan_amount']) : 0;
				}

				if ($plan_amount <= 0) {
					echo json_encode(array('status' => false, 'msg' => 'Invalid plan amount'));
					return;
				}

				// Determine plan type: PERIODIC or ON_DEMAND
				// auto_debit_plan_type: 0=Periodic, 1=On Demand
				$plan_type = ($planDetail['auto_debit_plan_type'] == 1) ? 'ON_DEMAND' : 'PERIODIC';

				// Generate unique subscription_id
				$subscription_id = 'SUB_' . $id_sch_ac . '_' . time();

				// Return URL — cf_autodebit callback on front-end
				$return_url = $this->config->item('base_url') . 'index.php/cf_autodebit/autoDebitRURL/M/' . $id_sch_ac;

				// First charge date (1st of next month for PERIODIC)
				$first_charge_date = date('Y-m-d', strtotime('first day of next month'));

				$gen_email  = $this->_cf_random_strings(8) . '@noemail.com';

				// Build new Cashfree API payload (v2025-01-01 format)
				$cf_payload = array(
					'subscription_id'     => $subscription_id,
					'customer_details'    => array(
						'customer_name'  => trim($planDetail['cus_name']),
						'customer_email' => !empty($planDetail['email']) ? $planDetail['email'] : $gen_email,
						'customer_phone' => $planDetail['mobile']
					),
					'plan_details' => array(
						'plan_name'             => $planDetail['scheme_name'] . ' - ' . $planDetail['code'],
						'plan_type'             => $plan_type,
						'plan_currency'         => 'INR',
						'plan_recurring_amount' => $plan_amount,
						'plan_max_amount'       => $plan_amount,
						'plan_max_cycles'       => intval($planDetail['total_installments'])
					),
					'subscription_meta' => array(
						'return_url' => $return_url
					),
					'authorization_details' => array(
						'authorization_amount' => 1  // Re 1 authorization charge (refundable)
					)
				);

				// Add periodic-specific fields
				if ($plan_type == 'PERIODIC') {
					$cf_payload['plan_details']['plan_recurring_interval'] = 1;
					$cf_payload['plan_details']['plan_recurring_interval_type'] = 1; // 1 = Month
					$cf_payload['subscription_meta']['first_charge_date'] = $first_charge_date;
				}

				// Insert local subscription record
				$insSubscription = array(
					'id_scheme_account' => $id_sch_ac,
					'subscription_id'   => $subscription_id,
					'plan_id'           => $planDetail['sync_scheme_code'],
					'first_charge_delay' => 1,
					'expires_on'        => date('Y-m-d H:i:s', strtotime('+' . $pendingIns . ' months')),
					'status'            => 0,
					'created_on'        => date('Y-m-d H:i:s'),
					'added_by'          => 1,  // 1 = Mobile App
				);
				$ins = $this->scheme_modal->insertData($insSubscription, 'auto_debit_subscription');

				if (!$ins['status']) {
					echo json_encode(array('status' => false, 'msg' => 'Error in subscription process, try again later...'));
					return;
				}

				// Call Cashfree API (new endpoint: {base_domain}/pg/subscriptions)
				// Strip any path from api_url — server may have /pg/orders/ in it
				$parsed_url = parse_url($planDetail['api_url']);
				$base_domain = $parsed_url['scheme'] . '://' . $parsed_url['host'];
				$api_url = $base_domain . '/pg/subscriptions';
				$res = $this->_cf_api_call($api_url, $cf_payload, $planDetail['param_3'], $planDetail['param_1']);

				// Log request and response
				$this->_cf_logToFile('cf_subscription', array(
					'action'    => 'subscribe',
					'id_sch_ac' => $id_sch_ac,
					'api_url'   => $api_url,
					'request'   => $cf_payload,
					'response'  => $res
				));

				if (isset($res['cf_subscription_id'])) {
					// Success — update local subscription record
					$updSubscription = array(
						'sub_reference_id' => isset($res['subscription_id']) ? $res['subscription_id'] : null,
						'auth_status'      => 1, // INITIALIZED
						'auth_link'        => isset($res['subscription_session_id']) ? $res['subscription_session_id'] : null,
						'message'          => isset($res['subscription_status']) ? $res['subscription_status'] : 'INITIALIZED',
						'status'           => 1,
						'last_update'      => date('Y-m-d H:i:s')
					);
					$this->scheme_modal->updateData(
						$updSubscription,
						'id_auto_debit_subscription',
						$ins['insertID'],
						'auto_debit_subscription'
					);

					// Update scheme_account auto_debit_status = 1 (Pending)
					$updSchAc = array(
						'auto_debit_status' => 1,
						'date_upd'          => date('Y-m-d H:i:s')
					);
					$this->scheme_modal->updateData(
						$updSchAc,
						'id_scheme_account',
						$id_sch_ac,
						'scheme_account'
					);

					echo json_encode(array(
						'status'    => true,
						'msg'       => 'Subscription created successfully. Kindly do the authorization process.',
						'auth_link' => isset($res['subscription_session_id']) ? $res['subscription_session_id'] : '',
						'sub_status' => isset($res['subscription_status']) ? $res['subscription_status'] : 'INITIALIZED'
					));
				} else {
					// API error
					$error_msg = isset($res['message']) ? $res['message'] : 'Failed to create subscription';
					echo json_encode(array(
						'status' => false,
						'msg'    => $error_msg,
						'data'   => $res
					));
				}
				break;

			case '2': // ─── Unsubscribe ────────────────────────────
				$planDetail = $this->scheme_modal->get_subscriptionData($id_sch_ac);

				if (empty($planDetail)) {
					echo json_encode(array('status' => false, 'msg' => 'No active subscription found...'));
					return;
				}

				// Cancel via new API: POST {api_url}/subscriptions/{subscription_id}/cancel
				$subData = $this->db->query("SELECT subscription_id FROM auto_debit_subscription 
											 WHERE id_auto_debit_subscription = " . intval($planDetail['id_auto_debit_subscription']))
									->row_array();

				if (empty($subData)) {
					echo json_encode(array('status' => false, 'msg' => 'Subscription record not found'));
					return;
				}

				// Strip any path from api_url — server may have /pg/orders/ in it
				$parsed_url = parse_url($planDetail['api_url']);
				$base_domain = $parsed_url['scheme'] . '://' . $parsed_url['host'];
				$api_url = $base_domain . '/pg/subscriptions/' . $subData['subscription_id'] . '/cancel';
				$res = $this->_cf_api_call($api_url, array(), $planDetail['param_3'], $planDetail['param_1']);

				// Log
				$this->_cf_logToFile('cf_subscription', array(
					'action'    => 'unsubscribe',
					'id_sch_ac' => $id_sch_ac,
					'api_url'   => $api_url,
					'response'  => $res
				));

				// Mark cancelled regardless of API response (matching Autodebit.php pattern)
				$updSubscription = array(
					'auth_status' => 5,  // CANCELLED
					'status'      => 0,
					'message'     => isset($res['status']) ? $res['status'] : 'CANCELLED',
					'last_update' => date('Y-m-d H:i:s')
				);
				$this->scheme_modal->updateData(
					$updSubscription,
					'id_auto_debit_subscription',
					$planDetail['id_auto_debit_subscription'],
					'auto_debit_subscription'
				);

				// Update scheme_account status
				$updSchAc = array(
					'auto_debit_status' => 5,  // CANCELLED
					'date_upd'          => date('Y-m-d H:i:s')
				);
				$this->scheme_modal->updateData(
					$updSchAc,
					'id_scheme_account',
					$id_sch_ac,
					'scheme_account'
				);

				echo json_encode(array(
					'status' => true,
					'msg'    => 'You are successfully unsubscribed from cashfree auto-debit process.'
				));
				break;

			default:
				echo json_encode(array(
					'status' => false,
					'msg'    => 'Invalid type. Use 1 for subscribe, 2 for unsubscribe.'
				));
				break;
		}
	}

	/**
	 * New Cashfree API call (v2025-01-01)
	 * Matches admin/Autodebit.php::_cashfreeApiCall()
	 */
	private function _cf_api_call($url, $payload, $clientId, $secretKey, $method = 'POST')
	{
		$curl = curl_init();

		$options = array(
			CURLOPT_URL            => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => $method,
			CURLOPT_HTTPHEADER     => array(
				'Content-Type: application/json',
				'x-client-id: ' . $clientId,
				'x-client-secret: ' . $secretKey,
				'x-api-version: 2025-01-01'
			)
		);

		if ($method == 'POST' && !empty($payload)) {
			$options[CURLOPT_POSTFIELDS] = json_encode($payload);
		}

		curl_setopt_array($curl, $options);

		$response = curl_exec($curl);
		$err      = curl_error($curl);
		curl_close($curl);

		if ($err) {
			return array('status' => false, 'message' => 'cURL Error: ' . $err);
		}

		return json_decode($response, true); // Returns associative array
	}

	/**
	 * Generate random alphanumeric string (for dummy email generation)
	 */
	private function _cf_random_strings($length)
	{
		$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		return substr(str_shuffle($str_result), 0, $length);
	}

	/**
	 * Log auto-debit subscription actions to file
	 */
	private function _cf_logToFile($folder, $data)
	{
		$log_dir = 'log/' . $folder;
		if (!is_dir($log_dir)) {
			mkdir($log_dir, 0777, true);
		}
		$log_path = $log_dir . '/' . date('Y-m-d') . '.txt';
		$log_entry = date('Y-m-d H:i:s') . ' | ' . json_encode($data) . "\n";
		file_put_contents($log_path, $log_entry, FILE_APPEND);
	}
}

