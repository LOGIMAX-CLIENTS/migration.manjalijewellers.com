<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
use Zend\Barcode\Barcode;
use Dompdf\Dompdf;
class Admin_manage extends CI_Controller
{
    const ACC_MODEL = "account_model";
    const EMP_MODEL = "employee_model";
    const ADM_MODEL = "chitadmin_model";
    const API_MODEL = "syncapi_model";
    const CHITAPI_MODEL = "chitapi_model";
    const SET_MODEL = "Admin_settings_model";
    const PAY_MODEL = "payment_model";
    const CUS_MODEL = "customer_model";
    const MAIL_MODEL = "email_model";
    const SMS_MODEL = "admin_usersms_model";
    const LOG_MODEL = "log_model";
    const ACC_VIEW = "scheme/opening/";
    const REG_VIEW = "registration/";
    const ACC_CLOSE_VIEW = "scheme/closing/";
    const MAIL_VIEW = 'mails/';
    const REP_VIEW = 'reports/';
    const EXV_VIEW = "scheme/requests/";
    const ENQUIRY_VIEW = "scheme/enquiry/";
    const GROUP_VIEW = "scheme/group/";
    const CLOSED_VIEW = 'dashboard/';
    const WALL_MODEL = "Wallet_model";
    const SCH_CLOSEING_PATH = 'assets/img/scheme_closing/';           //webcam upload, #AD On:20-12-2022,Cd:CLin,Up:AB
    function __construct()
    {
        parent::__construct();
        ini_set('date.timezone', 'Asia/Calcutta');
        $this->load->model(self::ACC_MODEL);
        $this->load->helper('metal_wgt_digit');
        $this->load->model(self::CUS_MODEL);
        $this->load->model(self::EMP_MODEL);
        $this->load->model(self::CHITAPI_MODEL);
        $this->load->model(self::API_MODEL);
        $this->load->model(self::ADM_MODEL);
        $this->load->model(self::SET_MODEL);
        $this->load->model(self::PAY_MODEL);
        $this->load->model(self::MAIL_MODEL);
        $this->load->model(self::SMS_MODEL);
        $this->load->model(self::LOG_MODEL);
        $this->load->model(self::WALL_MODEL);
        $this->load->model("sms_model");
        $this->company = $this->admin_settings_model->get_company();  // esakki 11-11
        if (!$this->session->userdata('is_logged')) {
            redirect('admin/login');
        } elseif ($this->session->userdata('access_time_from') != NULL && $this->session->userdata('access_time_from') != "") {
            $now = time();
            $from = $this->session->userdata('access_time_from');
            $to = $this->session->userdata('access_time_to');
            $allowedAccess = ($now > $from && $now < $to) ? TRUE : FALSE;
            if ($allowedAccess == FALSE) {
                $this->session->set_flashdata('login_errMsg', 'Exceeded allowed access time!!');
                redirect('chit_admin/logout');
            }
        }
        $this->id_log = $this->session->userdata('id_log');
        $this->branch_settings = $this->session->userdata('branch_settings');
    }
    public function index()
    {
        $this->open_account();
    }
    public function ajax_get_pay_detail($id)
    {
        $model = self::ACC_MODEL;
        $pay = $this->$model->get_pay_detail($id);
        echo json_encode($pay);
    }
    public function ajax_get_account_list($id = "")
    {
        $set_model = self::SET_MODEL;
        $access = $this->$set_model->get_access('account/new');
        $close = $this->session->userdata('profile');
        $model = self::ACC_MODEL;
        if (!empty($_POST)) {
            $range['from_date'] = $this->input->post('from_date');
            $range['to_date'] = $this->input->post('to_date');
            $date_type = $this->input->post('type');
            $range['id_metal'] = $this->input->post('id_metal');
            $items = $this->$model->get_all_account_by_range($range['from_date'], $range['to_date'], $range['id_metal'], $date_type);
        } else {
            $items = $this->$model->get_all_scheme_account();
        }
        $closed_account = array(
            'access' => $access,
            'data' => $items,
            'close_acc' => $close
        );
        //				print_r($this->db->last_query());exit;
        echo json_encode($closed_account);
    }
    public function ajax_get_closed_account_list($id = "")
    {
        $set_model = self::SET_MODEL;
        $access = $this->$set_model->get_access('account/new');
        $close = $this->session->userdata('profile');
        $model = self::ACC_MODEL;
        if (!empty($_POST)) {
            $range['from_date'] = $this->input->post('from_date');
            $range['to_date'] = $this->input->post('to_date');
            $items = $this->$model->get_closed_acc_by_range($range['from_date'], $range['to_date']);
        } else {
            $items = $this->$model->get_all_closed_account();
        }
        $account = array(
            'access' => $access,
            'data' => $items,
            'close_acc' => $close
        );
        echo json_encode($account);
    }
    public function ajax_get_scheme_account($id = "")
    {
        $model = self::ACC_MODEL;
        $account = $this->$model->getActiveAccounts(($id != NULL ? $id : ''));
        echo json_encode($account);
    }
    public function get_customer_accounts($id)
    {
        $cmodel = self::CUS_MODEL;
        $model = self::ACC_MODEL;
        $data['customer'] = $this->$cmodel->get_customer($id);
        $data['accounts'] = $this->$model->get_customer_accounts($id);
        $data['main_content'] = self::ACC_VIEW . 'customerschemelist';
        $this->load->view('layout/template', $data);
    }

    public function ajax_get_dynamic_kyc($id_scheme = 0, $id_customer = 0, $pay_amount = 0, $id_scheme_account = 0)
    {
        $this->load->model('kyc_model');
        $data = $this->kyc_model->get_unified_dynamic_kyc_data($id_scheme, $id_customer, $pay_amount, $id_scheme_account);
        echo $this->load->view('master/customer/kyc_tab', $data, TRUE);
    }

    public function ajax_get_dynamic_kyc_for_customer($id_customer = 0)
    {
        $this->load->model('kyc_model');
        $data = $this->kyc_model->get_unified_dynamic_kyc_data(0, $id_customer, 0, 0);
        if (!empty($data['kyc_master_list'])) {
            echo $this->load->view('master/customer/kyc_tab', $data, TRUE);
        } else {
            echo "";
        }
    }

    public function ajax_save_kyc_dynamic($id_customer, $id_scheme = 0, $pay_amount = 0, $id_scheme_account = 0) {
        $kyc_dynamic = $this->input->post('kyc_dynamic');
        $this->load->model('kyc_model');
        $res = $this->kyc_model->save_scheme_account_kyc($id_customer, $id_scheme, $pay_amount, is_array($kyc_dynamic) ? $kyc_dynamic : array(), $id_scheme_account);
        echo json_encode($res);
    }

    public function open_account()
    {
        $model = self::ACC_MODEL;
        $data['accounts'] = $this->$model->get_all_account();
        $data['main_content'] = self::ACC_VIEW . 'list';
        //print_r($data);exit;
        $this->load->view('layout/template', $data);
    }
    public function close_account()
    {
        $data['main_content'] = self::ACC_CLOSE_VIEW . 'list';
        $this->load->view('layout/template', $data);
    }
    public function account_form($type = "", $id = "")
    {
        $model = self::ACC_MODEL;
        $cus_model = self::CUS_MODEL;
        switch ($type) {
            case 'Add':
                /*-- Coded by ARVK --*/
                $set_model = self::SET_MODEL;
                $limit = $this->$set_model->limitDB('get', '1');
                $count = $this->$model->sch_acc_count();
                //print_r($count);exit;
                if ($limit['limit_sch_acc'] == 1) {
                    if ($count < $limit['sch_acc_max_count']) {
                        $data['scheme'] = $this->$model->account_empty_record();
                        $data['cus'] = $this->$cus_model->ajax_get_all_customers();
                        $data['main_content'] = self::ACC_VIEW . "form";
                        $this->load->view('layout/template', $data);
                    } else {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Account creation limit exceeded, Kindly contact Super Admin...', 'class' => 'danger', 'title' => 'Account creation'));
                        redirect('account/new');
                    }
                } else {
                    $data['scheme'] = $this->$model->account_empty_record();
                    $data['cus'] = $this->$cus_model->ajax_get_all_customers();
                    $data['main_content'] = self::ACC_VIEW . "form";
                    $this->load->view('layout/template', $data);
                }
                /*--/ Coded by ARVK --*/
                break;
            case 'Edit':
                $acc = $this->$model->get_account_open($id);
                $entry_date = $this->admin_settings_model->settingsDB('get', '', '');
                $customer_kyc = $this->$model->get_customer_kycDetails($acc['id_customer']);
                $data['gift_data'] = $this->$model->get_gift_issued_form($acc['id_scheme_account']);
                //echo"<pre>";	 print_r($acc);exit;
                $data['scheme'] = array(
                    'id_scheme_account' => (isset($acc['id_scheme_account']) ? $acc['id_scheme_account'] : 0),
                    'pan_no' => (strtoupper(isset($customer_kyc['pan_no']) ? $customer_kyc['pan_no'] : null)),
                    'id_branch' => (isset($acc['id_branch']) ? $acc['id_branch'] : 0),
                    'referal_code' => (!empty($acc['referal_code']) ? $acc['referal_code'] : NULL),
                    'is_refferal_by' => (!empty($acc['referal_code']) ? $acc['is_refferal_by'] : 1),
                    'customer' => (isset($acc['customer']) ? $acc['customer'] : NULL),
                    'mobile' => (isset($acc['mobile']) ? $acc['mobile'] : NULL),
                    'address1' => (isset($acc['cus_address']) ? $acc['cus_address'] : ''),
                    'passwd' => (isset($acc['passwd']) ? $acc['passwd'] : NULL),
                    'scheme_type' => (isset($acc['scheme_type']) ? $acc['scheme_type'] : 0),
                    'id_scheme' => (isset($acc['id_scheme']) ? $acc['id_scheme'] : 0),
                    'id_customer' => (isset($acc['id_customer']) ? $acc['id_customer'] : 0),
                    'acc_number' => (isset($acc['acc_number']) ? $acc['acc_number'] : NULL),
                    'id_employee' => (isset($acc['id_employee']) ? $acc['id_employee'] : 0),
                    'phone' => (isset($acc['phone']) ? $acc['phone'] : 0),
                    //'account_name' 	 => (isset($acc['account_name'])?$acc['account_name']:$acc['customer']),
                    'account_name' => (isset($entry_date[0]['cusName_edit']) ? ($entry_date[0]['cusName_edit'] == 0 ? $acc['cus_name'] : $acc['account_name']) : NULL), // A/c name edit option in admin based on the settings //HH
                    'ref_no' => (isset($acc['ref_no']) && $acc['ref_no'] != '' ? $acc['ref_no'] : ''),
                    'paid_installments' => (isset($acc['previous_paid']) ? $acc['previous_paid'] : 0),
                    'start_date' => (isset($acc['start_date']) ? date('d-m-Y', strtotime($acc['start_date'])) : NULL),
                    'maturity_date' => (isset($acc['maturity_date']) ? date('d-m-Y', strtotime($acc['maturity_date'])) : NULL),
                    'employee_approved' => (isset($acc['employee_approved']) ? $acc['employee_approved'] : 0),
                    'remark_open' => (isset($acc['remark_open']) ? $acc['remark_open'] : NULL),
                    'code' => (isset($acc['code']) ? $acc['code'] : NULL),
                    'scheme_acc_number' => (isset($acc['scheme_acc_number']) ? $acc['scheme_acc_number'] : NULL),
                    'is_opening' => (isset($acc['is_opening']) ? $acc['is_opening'] : 0),
                    'balance_amount' => (isset($acc['balance_amount']) ? $acc['balance_amount'] : '0.00'),
                    'balance_weight' => (isset($acc['balance_weight']) ? $acc['balance_weight'] : '0.00'),
                    'last_paid_date' => (isset($acc['last_paid_date']) ? date('d-m-Y', strtotime($acc['last_paid_date'])) : NULL),
                    'last_paid_weight' => (isset($acc['last_paid_weight']) ? $acc['last_paid_weight'] : '0.00'),
                    'last_paid_chances' => (isset($acc['last_paid_chances']) ? $acc['last_paid_chances'] : 0),
                    'total_paid_ins' => (isset($acc['total_paid_ins']) ? $acc['total_paid_ins'] : 0),
                    // 'is_new'	         => (isset($acc['is_new'])?$acc['is_new']:'Y'),		
                    // 'active'	         => (isset($acc['active'])?$acc['active']:0),						
                    'disable_payment' => (isset($acc['disable_payment']) ? $acc['disable_payment'] : 0),
                    'show_gift_article' => (isset($acc['show_gift_article']) ? $acc['show_gift_article'] : 0),
                    'schemeacc_no_set' => (isset($acc['schemeacc_no_set']) ? $acc['schemeacc_no_set'] : NULL),
                    'firstPayment_amt' => (isset($acc['firstPayment_amt']) ? $acc['firstPayment_amt'] : NULL),
                    'disable_pay_reason' => (isset($acc['disable_pay_reason']) ? $acc['disable_pay_reason'] : NULL),
                    'get_amt_in_schjoin' => $acc['get_amt_in_schjoin'],
                    'group_code' => (isset($acc['group_code']) ? $acc['group_code'] : NULL),
                    'pan_name' => (isset($customer_kyc['pan_name']) ? $customer_kyc['pan_name'] : NULL),
                    'aadhaar_no' => (isset($customer_kyc['aadhaar_no']) ? $customer_kyc['aadhaar_no'] : NULL),
                    'aadhaar_name' => (isset($customer_kyc['aadhaar_name']) ? $customer_kyc['aadhaar_name'] : NULL),
                    'schemeaccNo_displayFrmt' => (isset($acc['schemeaccNo_displayFrmt']) ? $acc['schemeaccNo_displayFrmt'] : 0),
                    'scheme_wise_acc_no' => (isset($acc['scheme_wise_acc_no']) ? $acc['scheme_wise_acc_no'] : 0),
                    'acc_branch' => (isset($acc['acc_branch']) ? $acc['acc_branch'] : ''),
                    'start_year' => (isset($acc['start_year']) ? $acc['start_year'] : ''),
                    'voucher_code' => (isset($acc['voucher_code']) ? $acc['voucher_code'] : ''),
                    'voucher_value' => (isset($acc['voucher_value']) ? $acc['voucher_value'] : ''),
                    'voucher_img' => (isset($acc['voucher_img']) ? $acc['voucher_img'] : ''),
                    'duplicate_passbook_issued' => (isset($acc['duplicate_passbook_issued']) ? $acc['duplicate_passbook_issued'] : 2),
                    'lump_joined_weight' => ($acc['is_lumpSum'] == 1 && $acc['lump_joined_weight'] > 0 ? $acc['lump_joined_weight'] : '0.000'),
                    'lump_payable_weight' => ($acc['is_lumpSum'] == 1 && $acc['lump_payable_weight'] > 0 ? $acc['lump_payable_weight'] : '0.000'),
                    //'cus_name'         => $acc['cus_name']
                    /* TopUp Scheme Esakki_20250802 */
                    'topup_rate' => !empty($acc['topup_rate']) ? $acc['topup_rate'] : 0.00,
                    'topup_amount' => !empty($acc['topup_amount']) ? $acc['topup_amount'] : 0.00,
                    'topup_weight' => !empty($acc['topup_weight']) ? $acc['topup_weight'] : 0.000,
                    'is_topup_scheme' => !empty($acc['is_topup_scheme']) ? $acc['is_topup_scheme'] : 0,
                    'topup_booking_type' => !empty($acc['topup_booking_type']) ? $acc['topup_booking_type'] : 0,
                );
                $cus_kyc = $this->$cus_model->get_kyc_byid($acc['id_customer'], 2);
                if (sizeof($cus_kyc) > 0) {
                    $data['scheme']['pan_no'] = $cus_kyc[0]['number'];
                }
                $data['main_content'] = self::ACC_VIEW . "form";
                $data['cus'] = $this->$cus_model->ajax_get_all_customers();
                $this->load->view('layout/template', $data);
                break;
        }
    }
    function free_payment_data($sch_data, $sch_acc_id, $id_branch)  // esakki 11-11
    {
        $metal_rate = $this->payment_model->getMetalRate();
        $gold_rate = number_format((float) $metal_rate['goldrate_22ct'], 2, '.', '');
        $gst_amt = 0;
        if ($sch_data['gst'] > 0) {
            if ($sch_data['gst_type'] == 0) {
                $gst_amt = $sch_data['amount'] - ($sch_data['amount'] * (100 / (100 + $sch_data['gst'])));
                $converted_wgt = number_format((float) (($sch_data['amount'] - $gst_amt) / $gold_rate), 3, '.', '');
            } else {
                $gst_amt = $sch_data['amount'] * ($sch_data['gst'] / 100);
                $converted_wgt = number_format((float) ($sch_data['amount'] / $gold_rate), 3, '.', '');
            }
        } else {
            $converted_wgt = number_format((float) ($sch_data['amount'] / $gold_rate), 3, '.', '');
        }
        $fxd_wgt = $sch_data['max_weight'];
        $insertData = array(
            "id_scheme_account" => $sch_acc_id,
            "gst" => $sch_data['gst'],
            "gst_type" => $sch_data['gst_type'],
            "id_employee" => $this->session->userdata('uid'),
            "date_payment" => date('Y-m-d H:i:s'),
            "payment_type" => "Cost free payment",
            "payment_mode" => "FP",
            "act_amount" => $sch_data['amount'],
            "payment_amount" => $sch_data['amount'],
            "due_type" => 'ND',
            "no_of_dues" => '1',
            "metal_rate" => $gold_rate,
            "metal_weight" => ($sch_data['scheme_type'] == 2 ? $converted_wgt : ($sch_data['scheme_type'] == 1 ? $fxd_wgt : 0.000)),
            "remark" => "Paid by " . $sch_data['company_name'],
            "payment_status" => ($sch_data['approvalReqForFP'] == 1 ? 2 : 1),
            "id_branch" => $id_branch,
            "added_by" => 0,
            "id_scheme" => $sch_data['id_scheme'],
        );
        return $insertData;
    }
    function generate_receipt_no($data, $id_branch)  // esakki 11-11
    {
        // print_r($data);exit;
        $rcpt_no = "";
        $rcpt = $this->payment_model->get_receipt_no($data['id_scheme'], $id_branch);
        if ($rcpt != NULL) {
            if ($this->config->item('receipTcode') != '') {          // based on the config settings to removed comp shortcode front of recp num //HH
                $temp = explode($this->company['short_code'], $rcpt);
                if (isset($temp)) {
                    $number = (int) $temp[1];
                    $number++;
                    $rcpt_no = $this->company['short_code'] . str_pad($number, 7, '0', STR_PAD_LEFT);
                    //print_r($rcpt_no);exit;
                }
            } else {
                $number = (int) $rcpt;
                $number++;
                $rcpt_no = str_pad($number, 7, '0', STR_PAD_LEFT);
                //print_r($rcpt_no);exit;
            }
        } else {
            if ($this->config->item('receipTcode') != '') {
                $rcpt_no = $this->company['short_code'] . "000001";
            } else {
                $rcpt_no = "000001";
            }
        }
        /*	if($rcpt!=NULL)
            {
                  $temp = explode($data['short_code'],$rcpt);
                     if(isset($temp))
                     {
                        $number = (int) $temp[1];
                        $number++;
                        $rcpt_no =$data['short_code'].str_pad($number, 6, '0', STR_PAD_LEFT);
                    }		   
            }
            else
            {
                     $rcpt_no =$data['short_code']."000001";
            }  */
        return $rcpt_no;
    }
    function set_image($id)
    {
        $data = array();
        //voucher image starts here
        if ($_FILES['voucher_img']['name']) {
            //	print_r($_FILES);exit;
            $path = 'assets/img/voucher/' . $id . '/';
            if (!is_dir($path)) {
                mkdir($path, 0777, TRUE);
            } else {
                $file = $path . "voucher.jpg";
                chmod($path, 0777);
                unlink($file);
            }
            $img = str_replace(' ', '_', $_FILES['voucher_img']['tmp_name']);
            $filename = str_replace(' ', '_', $_FILES['voucher_img']['name']);
            //     $img=$_FILES['voucher_img']['tmp_name'];
            // $filename = $_FILES['voucher_img']['name'];
            $voucher_img_name = 'voucher.png';
            $imgpath = 'assets/img/voucher/' . $id . '/' . $voucher_img_name;
            //print_r($imgpath);exit;
            $upload = $this->upload_img('voucher_img', $imgpath, $img);
            $data['img_url'] = base_url() . $imgpath;
            $update_giftcard = $this->account_model->update_giftcard($data, $id);
        }
        if ($_FILES['panFile']['name']) {
            $path = "assets/img/customer/" . $id . "/";
            if (!is_dir($path)) {
                mkdir($path, 0777, TRUE);
            } else {
                $file = $path . 'pan_' . $id . ".png";
                chmod($path, 0777);
                unlink($file);
            }
            $img = $_FILES['panFile']['tmp_name'];
            $filename = $_FILES['panFile']['name'];
            $pan_img_name = 'pan_' . $id . '.png';
            $imgpath = $path . $pan_img_name;
            //print_r($imgpath);exit;
            $upload = $this->upload_img('panFile', $imgpath, $img);
        }
        //webcam upload, #AD On:20-12-2022,Cd:CLin,Up:AB   ---> starts...    
        if ($_FILES['closing_img']['name']) {
            $path = self::SCH_CLOSEING_PATH . $id . '/';
            if (!is_dir($path)) {
                mkdir($path, 0777, TRUE);
            }
            $img = $_FILES['closing_img']['tmp_name'];
            $filename = $_FILES['closing_img']['name'];
            $image_name = $id . '.png';
            $imgpath = self::SCH_CLOSEING_PATH . $id . '/' . $image_name;
            $upload = $this->upload_img('scheme_closing', $imgpath, $img);
        }
        //webcam upload ends...    
    }
    function upload_img($outputImage, $dst, $img)
    {
        //print_r(getimagesize($img));exit;    //Array ( [0] => 512 [1] => 288 [2] => 3 [3] => width="512" height="288" [bits] => 8 [mime] => image/png )
        if (($img_info = getimagesize($img)) === FALSE) {
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
        imagejpeg($tmp, $dst, 60);
    }
    /* / Coded by ARVK*/
    public function account_post($type = "", $id = "")
    {
        $model = self::ACC_MODEL;
        $emp_model = self::EMP_MODEL;
        $cus_model = self::CUS_MODEL;
        $set_model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        $this->load->model(self::EMP_MODEL);
        $employee = $this->session->userdata('uid');
        switch ($type) {
            case 'Add':
                $sch_acc = $this->input->post('scheme');
                //echo '<pre>';print_r($sch_acc);exit;
                $is_refferal_by = NULL;
                $id_agent = NULL;
                $agent_code = NULL;
                if ($sch_acc['agent_code'] != '') {
                    $agent_code = $this->$model->verifyAgentCode($sch_acc['agent_code']);
                    if ($agent_code['status'] == 1) {
                        $id_agent = $agent_code['agent']['id_agent'];
                        $agent_code = $agent_code['agent']['agent_code'];
                    }
                }
                if ($sch_acc['referal_code'] != '') {
                    $ref_code = $this->$model->veriflyreferral_code($sch_acc['referal_code']);
                    if ($ref_code['status'] == 1) {
                        $is_refferal_by = (strtoupper($ref_code['user']) == 'CUS' ? 0 : (strtoupper($ref_code['user']) == 'EMP' ? 1 : NULL));
                    }
                }
                $cus = $this->$cus_model->get_cust($sch_acc['id_customer']);
                $settings = $this->admin_settings_model->settingsDB('get', '', '');
                $entry_date = $this->customer_model->get_entrydate($cus['id_branch']); // Taken from ret_day_closing  table branch wise //HH
                //echo '<pre>';print_r($sch_acc);exit;
                if ($this->session->userdata('branch_settings') == 1) {
                    if ($this->session->userdata('branchWiseLogin') == 1 || $this->session->userdata('is_branchwise_cus_reg') == 1) {
                        $id_branch = (!empty($sch_acc['id_branch']) ? $sch_acc['id_branch'] : (!empty($cus['id_branch']) ? $cus['id_branch'] : NULL));
                    } else {
                        $id_branch = (!empty($sch_acc['id_branch']) ? $sch_acc['id_branch'] : NULL);
                    }
                } else {
                    $id_branch = NULL;
                }
                //  print_r($id_branch);exit;
                // 	1 - Flexible[Can pay installments and close], 2 - Fixed Maturity, 3 - Fixed Flexible[Increase maturity if has Default]
                $maturity_date = ($sch_acc['maturity_type'] == 2 ? ($sch_acc['maturity_days'] > 0 ? date('Y-m-d', strtotime(date('Y-m-d') . '+' . $sch_acc['maturity_days'] . ' days')) : NULL) : ($sch_acc['maturity_type'] == 3 ? date('Y-m-d', strtotime(date('Y-m-d') . '+' . $sch_acc['total_installments'] . ' months')) : NULL));
                $start_year = $this->$model->get_financialYear();
                $account = array(
                    'id_scheme' => (isset($sch_acc['id_scheme']) ? $sch_acc['id_scheme'] : 0),
                    'pan_no' => (strtoupper(isset($sch_acc['pan_no']) ? $sch_acc['pan_no'] : NULL)),
                    'is_refferal_by' => (!empty($sch_acc['referal_code']) ? $sch_acc['referal_by'] : NULL),
                    'referal_code' => (!empty($sch_acc['referal_code']) ? $sch_acc['referal_code'] : NULL),
                    'id_customer' => (isset($sch_acc['id_customer']) ? $sch_acc['id_customer'] : 0),
                    'account_name' => (isset($settings[0]['cusName_edit']) ? ($settings[0]['cusName_edit'] == 0 ? $sch_acc['cus_name'] : $sch_acc['account_name']) : NULL),
                    'ref_no' => (isset($sch_acc['ref_no']) ? $sch_acc['ref_no'] : NULL),
                    'group_code' => (isset($sch_acc['group_code']) ? $sch_acc['group_code'] : NULL),
                    // 'paid_installments'	=> (isset($sch_acc['paid_installments'])?$sch_acc['paid_installments']:0),	
                    // 'is_opening'		=> (isset($sch_acc['is_opening'])?$sch_acc['is_opening']:0),
                    // 'balance_amount'	=> (isset($sch_acc['balance_amount'])?$sch_acc['balance_amount']:"0.00"),
                    // 'balance_weight'	=> (isset($sch_acc['balance_weight'])?$sch_acc['balance_weight']:"0.000"),
                    // 'last_paid_weight'	=> (isset($sch_acc['last_paid_weight'])?$sch_acc['last_paid_weight']:"0.00"),
                    // 'last_paid_chances'	=>(isset($sch_acc['last_paid_chances'])?$sch_acc['last_paid_chances']:"0"),
                    // 'last_paid_date'	=> (isset($sch_acc['last_paid_date'])?date('Y-m-d H:i:s', strtotime($sch_acc['last_paid_date'])):NULL),
                    // 'start_date'   		=> (isset($sch_acc['start_date']) && $sch_acc['start_date'] != '' ?date('Y-m-d H:i:s', strtotime($sch_acc['start_date'])):date('Y-m-d H:i:s')),	
                    'start_date' => date('Y-m-d H:i:s'),
                    'maturity_date' => $maturity_date, // Create Maturity Date based on start date during add
                    //'maturity_date'   => (!empty($sch_acc['maturity_date'])?date('Y-m-d H:i:s', strtotime($sch_acc['maturity_date'])):NULL),
                    'employee_approved' => ($employee ? $employee : 0),
                    'id_employee' => ($this->session->userdata('branch_settings') == 1 && $this->session->userdata('id_branch') == '' ? $sch_acc['id_employee'] : $employee),
                    'remark_open' => (isset($sch_acc['remark_open']) ? $sch_acc['remark_open'] : NULL),
                    'active' => 1,
                    //  'active'	        => (!empty($sch_acc['active'])?$sch_acc['active']:0),
                    //  'active'	        => (isset($sch_acc['active'])  && $sch_acc['sch_approval'] == 1 ? 2 :(isset($sch_acc['active']) && $sch_acc['active']!='' ? $sch_acc['active'] : 1 )),
                    'disable_payment' => (isset($sch_acc['disable_payment']) ? $sch_acc['disable_payment'] : 0),
                    'show_gift_article' => (isset($sch_acc['show_gift_article']) ? $sch_acc['show_gift_article'] : 0),
                    'scheme_acc_number' => (isset($sch_acc['scheme_acc_number']) ? $sch_acc['scheme_acc_number'] : NULL),
                    // 'is_new'	        => (isset($sch_acc['is_new'])?$sch_acc['is_new']:'Y'),		
                    'date_add' => date("Y-m-d H:i:s"),
                    //  'custom_entry_date' => ($entry_date[0]['edit_custom_entry_date']==1 ? $entry_date[0]['custom_entry_date']:NULL),
                    'custom_entry_date' => ($entry_date['edit_custom_entry_date'] == 1 ? $entry_date['custom_entry_date'] : NULL),
                    'added_by' => 1,
                    'id_branch' => $id_branch,
                    'scheme_acc_number' => NULL,
                    'agent_code' => $agent_code,
                    'id_agent' => $id_agent,
                    'start_year' => $start_year,
                    'firstPayment_amt' => ($sch_acc['get_amt_in_schjoin'] == 1 ? $sch_acc['firstPayment_amt'] : NULL), // firstPayment_amt get from customer based on the scheme settings//HH
                    'duplicate_passbook_issued' => (isset($sch_acc['duplicate_passbook_issued']) ? $sch_acc['duplicate_passbook_issued'] : 2),
                    'lump_joined_weight' => ($sch_acc['is_lumpSum'] == 1 && $sch_acc['lump_joined_weight'] > 0 ? $sch_acc['lump_joined_weight'] : '0.000'),
                    'lump_payable_weight' => ($sch_acc['is_lumpSum'] == 1 && $sch_acc['lump_joined_weight'] > 0 ? ($sch_acc['lump_joined_weight'] / $sch_acc['total_installments']) : '0.000'),
                    /* TopUp Scheme Esakki_20250802 */
                    'topup_rate' => !empty($sch_acc['topup_rate']) ? $sch_acc['topup_rate'] : 0.00,
                    'topup_amount' => !empty($sch_acc['topup_amount']) ? round($sch_acc['topup_amount']) : 0.00,
                    'topup_weight' => !empty($sch_acc['topup_weight']) ? $sch_acc['topup_weight'] : 0.000,
                );
                //echo "<pre>";print_r($account);exit;
                /*if($sch_acc['get_amt_in_schjoin']==1)
                   {						
                       $firstPayment_amt=array(
                       'firstPayment_amt'=>$sch_acc['firstPayment_amt']);
                      // print_r($firstPayment_amt);exit;
                       $status = $this->$model->update_account($firstPayment_amt,$sch_acc['id_scheme_account']); 
                   } */
                if ($account['id_customer'] > 0 && $account['id_scheme'] > 0 && ($this->session->userdata('branch_settings') != 1 || !empty($id_branch))) {
                    if ($sch_acc['pan_no'] != null) {
                        $insData = array(
                            'id_customer' => (isset($account['id_customer']) ? $account['id_customer'] : NULL),
                            'kyc_type' => 2,
                            'number' => (isset($account['pan_no']) ? $account['pan_no'] : NULL),
                            'name' => (isset($account['pan_name']) ? $account['pan_name'] : NULL),
                            'status' => 0,
                            'date_add' => date('Y-m-d H:i:s')
                        );
                        $this->$model->insert_kyc($insData);
                    }
                    if ($sch_acc['aadhaar_no'] != null) {
                        $insaadharData = array(
                            'id_customer' => (isset($account['id_customer']) ? $account['id_customer'] : NULL),
                            'kyc_type' => 3,
                            'number' => (isset($account['aadhaar_no']) ? $account['aadhaar_no'] : NULL),
                            'name' => (isset($account['aadhaar_name']) ? $account['aadhaar_name'] : NULL),
                            'status' => 0,
                            'date_add' => date('Y-m-d H:i:s')
                        );
                        $this->$model->insert_kyc($insaadharData);
                    }
                    $this->db->trans_begin();
                    $insert_status = $this->$model->insert_account($account);
                    // Client id Generation
                    // Skip clientid if gent_clientid is disabled
                    $chit_settings = $this->db->query("SELECT gent_clientid FROM chit_settings LIMIT 1")->row_array();
                    
                    if ($insert_status['insertID'] > 0 && !empty($chit_settings['gent_clientid']) && $chit_settings['gent_clientid'] > 0) {
                        $cusData = $this->$model->get_customer_acc($insert_status['insertID']);
                        $updateData['ref_no'] = $this->config->item('cliIDcode') . "/" . $cusData['code'] . "/" . $insert_status['insertID'];
                        $this->$model->update_account($updateData, $insert_status['insertID']);
                    }
                    //lines created by Durga 13.02.2023 starts here 
                    // getting gift data from gift table in form(gift_desc and quantity)
                    $gift_data = $this->input->post('gift_list_data');
                    $quantity = $this->input->post('gift_quantity');
                    $barcode = $this->input->post('gift_barcode');
                    // getting number of rows of gift table 
                    $gift_table_length = $this->input->post('gift_table_length');
                    //inserting in gift_issued table as gift type
                    if ($gift_table_length >= 1) {
                        $gift_type = 1;
                        for ($i = 1; $i <= $gift_table_length; $i++) {
                            if ($gift_data[$i] != '') {
                                $gift_name[$i] = $this->db->query("SELECT gift_name FROM gifts WHERE id_gift=" . $gift_data[$i])->row('gift_name');
                                $data = array(
                                    'type' => $gift_type,
                                    'id_gift' => $gift_data[$i],
                                    'gift_desc' => $gift_name[$i],
                                    'quantity' => $quantity[$i] != '' ? $quantity[$i] : '',
                                    'barcode' => $barcode[$i] != '' ? $barcode[$i] : NULL,
                                    'id_scheme_account' => $insert_status['insertID'],
                                    'id_employee' => $sch_acc['id_employee'],
                                    'date_issued' => date("Y-m-d H:i:s")
                                );
                                $insert_gift = $this->$model->insert_gift_issued($data);
                                //print_r($insert_gift);exit;
                                //$update_status ---> quantity is updated as outstock in gift master
                                //$updated_status=$this->$model->update_gift_master($quantity[$i],$gift_data[$i]);
                                //print_r($updated_status);exit;
                            }
                        }
                    }
                    //getting prize details from prize text area
                    $gift = $this->input->post('gift');
                    //inserting prize details in gift_issued table as prize type 
                    if ($gift[1]['prize'] != '') {
                        foreach ($gift as $key => $val) {
                            if ($val['prize'] != '') {
                                $gift_type = 2;
                                $gift_desc = $val['prize'];
                            }
                            if ($gift_desc != '') {
                                $giftData = array(
                                    'type' => $gift_type,
                                    'gift_desc' => $gift_desc,
                                    'id_scheme_account' => $insert_status['insertID'],
                                    'id_employee' => $sch_acc['id_employee'],
                                    'date_issued' => date("Y-m-d H:i:s")
                                );
                                $insert_gift = $this->$model->insert_gift_issued($giftData);
                            }
                        }
                    }
                    //lines created by Durga 13.02.2023 ends here 
                    $get_pan_file = $_FILES['panFile']['name'];
                    if ($get_pan_file != '') {
                        $this->set_image($account['id_customer']);
                    }
                    $reff_settings = $this->$set_model->settingsDB('get', '');
                    //kyc
                    if ($sch_acc['referal_code'] != '' && ($is_refferal_by == 0 && $reff_settings[0]['cusbenefitscrt_type'] == 0) || ($is_refferal_by == 1 && $reff_settings[0]['empbenefitscrt_type'] == 0)) {
                        if ($is_refferal_by == 0) {
                            $data = array(
                                'id_customer' => (isset($sch_acc['id_customer']) ? $sch_acc['id_customer'] : 0),
                                'referal_code' => (isset($sch_acc['referal_code']) ? $sch_acc['referal_code'] : NULL),
                                'is_refferal_by' => $is_refferal_by,
                                'cus_single' => $cus_single
                            );
                        } else {
                            $data = array(
                                'id_customer' => (isset($sch_acc['id_customer']) ? $sch_acc['id_customer'] : 0),
                                'referal_code' => (isset($sch_acc['referal_code']) ? $sch_acc['referal_code'] : NULL),
                                'is_refferal_by' => $is_refferal_by,
                                'emp_single' => $emp_single
                            );
                        }
                        $available = $this->$model->available_refcode($data);
                    } else if ($sch_acc['referal_code'] != '' && ($is_refferal_by == 0 && $reff_settings[0]['cusbenefitscrt_type'] == 1) || ($is_refferal_by == 1 && $reff_settings[0]['empbenefitscrt_type'] == 1)) {
                        $data = array(
                            'id_customer' => (isset($sch_acc['id_customer']) ? $sch_acc['id_customer'] : 0),
                            'is_refferal_by' => $is_refferal_by,
                            'cus_single' => $cus_single,
                            'emp_single' => $emp_single,
                            'emp_ref_code' => NULL,
                            'cus_ref_code' => NULL
                        );
                        $available = $this->$model->available_refcode($data);
                    }
                    // refrreal code check and update //
                    $scheme_acc_no = $this->$set_model->accno_generatorset();
                    /* Coded by ARVK*/
                    if ($insert_status['sch_data']['free_payment'] == 1 && $account['is_opening'] == 0) {
                        // esakki starts 11-11
                        $pay_insert_data = $this->free_payment_data($insert_status['sch_data'], $insert_status['insertID'], $id_branch);
                        $fn_year = $this->$model->get_financialYear();
                        $pay_insert_data['receipt_year'] = $fn_year;
                        if ($insert_status['sch_data']['receipt_no_set'] == 1) {
                            $pay_insert_data['receipt_no'] = $this->generate_receipt_no($insert_status['sch_data'], $id_branch);
                        }
                        // $pay_add_status = $this->payment_model->insert_payment($pay_insert_data);
                        $pay_add_status = $this->payment_model->paymentDB("insert", "", $pay_insert_data);
                        //RHR scheme starts...	
                        $dt_pay = $pay_insert_data['date_payment'];
                        $ins_cycle = $this->payment_model->get_due_date($pay_insert_data['due_type'], $dt_pay, $insert_status['insertID']);
                        if (sizeof($ins_cycle[0]) > 0) {
                            $cycle_data = array(
                                'due_date' => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
                                'due_date_to' => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
                                'grace_date' => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
                                'installment' => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
                                'is_limit_exceed' => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
                            );
                            $this->payment_model->updData($cycle_data, 'id_payment', $pay_add_status['insertID'], 'payment');
                        }
                        //RHR scheme ends....
                        $paid = $this->payment_model->getPaidInsData($insert_status['insertID']);
                        if (sizeof($paid) > 0) {
                            $paid_ins = array('total_paid_ins' => $paid['paid_installments']);
                            $this->payment_model->updData($paid_ins, 'id_scheme_account', $insert_status['insertID'], 'scheme_account');
                        }
                        $pay_mode_array[] = array(
                            'id_payment' => $pay_add_status['insertID'],
                            'payment_amount' => $pay_insert_data['payment_amount'],
                            'payment_mode' => 'FP',
                            'payment_status' => $pay_insert_data['payment_status'],
                            'payment_date' => $dt_pay, //date("Y-m-d H:i:s"),
                            'created_time' => date("Y-m-d H:i:s"),
                            'created_by' => $this->session->userdata('uid')
                        );
                        if (!empty($pay_mode_array)) {
                            $cardPayInsert = $this->payment_model->insertBatchData($pay_mode_array, 'payment_mode_details');
                        }
                        // esakki ends 11-11
                        if ($scheme_acc_no['status'] == 1 && $scheme_acc_no['schemeacc_no_set'] == 0) {
                            $scheme_acc_number = $this->$model->account_number_generator($account['id_scheme'], $id_branch, '');
                            if ($scheme_acc_number != NULL) {
                                $updateData['scheme_acc_number'] = $scheme_acc_number;
                            }
                            $updSchAc = $this->$model->update_account($updateData, $insert_status['insertID']);
                        }
                    }
                    /* / Coded by ARVK*/
                    //voucher insert in gift_card table starts here
                    if ($insert_status) {
                        $voucher_code = $sch_acc['voucher_code'];
                        $voucher_value = $sch_acc['voucher_value'];
                        if (!empty($voucher_code) && !empty($voucher_value)) {
                            $voucher_data = array(
                                'free_card' => 4,
                                'gift_for' => 2,
                                //'id_branch' 				=> (!empty($login_branch))?$login_branch:$sch_acc['id_branch'],
                                'id_scheme_account' => $insert_status['insertID'],
                                //'emp_created'         		=> $sch_acc['id_employee'],
                                'code' => $voucher_code,
                                'amount' => $voucher_value,
                                'status' => 0,
                                //'type'         				=> 2,
                                'date_add' => date("Y-m-d H:i:s"),
                                'valid_from' => date("Y-m-d H:i:s")
                            );
                            $insert_gift_card = $this->$model->insert_gift_card($voucher_data);
                            $get_voucher_file = $_FILES['voucher_img']['name'];
                            if ($get_voucher_file != '') {
                                $this->set_image($insert_status['insertID']);
                            }
                        }
                        //print_r($voucher_data);exit;
                    }
                    //voucher insert in gift_card table ends here
                    if ($this->db->trans_status() === TRUE) {
                        $this->db->trans_commit();
                        $cus_data = $this->$model->get_customer_acc($insert_status['insertID']);
                        $id = $insert_status['insertID'];
                        $this->account_join_message($id, $cus_data);
                        $log_data = array(
                            'id_log' => $this->id_log,
                            'event_date' => date("Y-m-d H:i:s"),
                            'module' => 'Scheme Account',
                            'operation' => 'Add',
                            'record' => $insert_status['insertID'],
                            'remark' => 'Scheme Account added successfully'
                        );
                        $scheme_details = $this->payment_model->get_scheme_details($id);
                        $this->$log_model->log_detail('insert', '', $log_data);
                        $this->session->set_flashdata('chit_alert', array('message' => 'Scheme account <' . $sch_acc['account_name'] . '> created successfully', 'class' => 'success', 'title' => 'Create Scheme Account'));
                        $this->session->set_userdata(array('id_scheme_account' => $id, 'cus_id' => $sch_acc['id_customer'], 'cus_name' => $cus_data['firstname'], 'cus_mobile' => $cus_data['mobile'], 'id_scheme' => $sch_acc['id_scheme'], 'scheme_name' => $scheme_details['scheme_name']));
                        //   echo '<pre>';print_r($sch_acc); exit;
                       if(isset($sch_acc['sch_approval']) && $sch_acc['sch_approval']==1 && $sch_acc['active']==2){
							$this->session->set_flashdata('Approval_alert', array('message' => 'Please Approve Your Account For Proceeding with Payment !!','class' => 'warning'));
							$status = true; $message = 'Account created. Approval required.'; $type = 2;
						} else {
							$status = true; $message = 'Scheme account <'.$sch_acc['account_name'].'> created successfully'; $type = 1;
						}

                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Create Scheme Account'));
                       $status = false; $message = 'Unable to proceed your request'; $type = 2;
                        // redirect('account/new');
                    }
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request, Please fill the required fields.', 'class' => 'danger', 'title' => 'Create Scheme Account'));
                     $status = false; $message = 'Unable to proceed your request, Please fill the required fields.'; $type = 2;
                    // redirect('account/new');
                }
                	$data = array('type' => $type, 'status' => $status, 'message' => $message);
				   echo json_encode($data); 
                exit;
                break;
            case 'Edit':
                //   print_r($_POST);exit;
                $sch_acc = $this->input->post('scheme');
                //		echo '<pre>';print_r($sch_acc);exit;
                $entry_date = $this->admin_settings_model->settingsDB('get', '', '');
                $acc = $this->$model->get_account_open($id);
                if ($acc['has_voucher'] == 1) {
                    //voucher update block starts here 
                    $voucher_code = $sch_acc['voucher_code'];
                    $voucher_value = $sch_acc['voucher_value'];
                    $existing_gift_card = $this->$model->get_gift_card_byschid($id);
                    if (count($existing_gift_card) > 0) {
                        $update_giftcard = array(
                            'status' => 5,
                        );
                        $update_giftcard_table = $this->$model->update_giftcard($update_giftcard, $id);
                    }
                    $voucher_data = array(
                        'free_card' => 4,
                        'gift_for' => 2,
                        //'id_branch' 				=> (!empty($login_branch))?$login_branch:$sch_acc['id_branch'],
                        'id_scheme_account' => $id,
                        //'emp_created'         		=> $sch_acc['id_employee'],
                        'code' => $voucher_code,
                        'amount' => $voucher_value,
                        'status' => 0,
                        //'type'         				=> 2,
                        'date_add' => date("Y-m-d H:i:s"),
                        'valid_from' => date("Y-m-d H:i:s")
                    );
                    $insert_gift_card = $this->$model->insert_gift_card($voucher_data);
                    $get_voucher_file = $_FILES['voucher_img']['name'];
                    if ($get_voucher_file != '') {
                        $this->set_image($id);
                    } else {
                        if (!empty($voucher_code) && !empty($voucher_value)) {
                            $voucher_data = array(
                                'img_url' => $acc['voucher_img'],
                            );
                            $update_giftcard = $this->$model->update_giftcard($voucher_data, $id);
                        }
                    }
                    //voucher update block ends here 
                }
                if (!empty($sch_acc['referal_code']) && empty($acc['referal_code'])) {
                    //new ref code updated... transaction type : credit entry to be inserted...   //ref_edit_type : 2
                    $ref_edit_remark = 'Refferal code newly Updated by id_employee : ' . $sch_acc['id_employee'];
                    $ref_edit_type = "2";
                } else if (!empty($sch_acc['referal_code']) && (!empty($acc['referal_code'])) && $acc['referal_code'] != $sch_acc['referal_code']) {
                    //old ref code updated... transaction type : update credit entry with new emp details... //ref_edit_type : 1
                    $ref_edit_remark = 'Refferal code [OLD:' . $acc['referal_code'] . '] Edited by id_employee : ' . $sch_acc['id_employee'];
                    $ref_edit_type = "1";
                } else if (empty($sch_acc['referal_code']) && (!empty($acc['referal_code']))) {
                    //old ref code removed... transaction type : debit entry to be inserted... //ref_edit_type : 3
                    $ref_edit_remark = 'Refferal code [OLD:' . $acc['referal_code'] . '] removed by id_employee : ' . $sch_acc['id_employee'];
                    $ref_edit_type = "3";
                }
                $cus = $this->$cus_model->get_cust($sch_acc['id_customer']);
                if ($this->session->userdata('branch_settings') == 1) {
                    if ($this->session->userdata('branchWiseLogin') == 1) {
                        $id_branch = (isset($sch_acc['id_branch']) ? $sch_acc['id_branch'] : NULL);
                    }
                    if ($this->session->userdata('is_branchwise_cus_reg') == 1) {
                        $id_branch = (isset($sch_acc['id_branch']) ? $sch_acc['id_branch'] : (isset($cus['id_branch']) ? $cus['id_branch'] : NULL));
                    } else {
                        $id_branch = (isset($sch_acc['id_branch']) ? $sch_acc['id_branch'] : NULL);
                    }
                } else {
                    $id_branch = NULL;
                }
                $account = array(
                    'id_scheme' => (isset($acc['id_scheme']) ? $acc['id_scheme'] : 0),
                    'pan_no' => (strtoupper(isset($sch_acc['pan_no']) ? $sch_acc['pan_no'] : null)),
                    'id_customer' => (isset($sch_acc['id_customer']) ? $sch_acc['id_customer'] : 0),
                    'is_refferal_by' => (!empty($sch_acc['referal_code']) ? $sch_acc['referal_by'] : NULL),
                    'referal_code' => (!empty($sch_acc['referal_code']) ? $sch_acc['referal_code'] : NULL),
                    'id_branch' => ($id_branch ? $id_branch : NULL),
                    'account_name' => (isset($entry_date[0]['cusName_edit']) ? ($entry_date[0]['cusName_edit'] == 0 ? $sch_acc['cus_name'] : $sch_acc['account_name']) : NULL),
                    'group_code' => (isset($sch_acc['group_code']) ? $sch_acc['group_code'] : NULL),
                    'maturity_date' => (!empty($sch_acc['maturity_date']) ? date('Y-m-d H:i:s', strtotime($sch_acc['maturity_date'])) : NULL), // Just update maturity date received in post data
                    'employee_approved' => ($employee ? $employee : 0),
                    'active' => (isset($acc['active']) ? $acc['active'] : 0),
                    'disable_payment' => (isset($sch_acc['disable_payment']) ? $sch_acc['disable_payment'] : 0),
                    'disable_pay_reason' => (isset($sch_acc['disable_pay_reason']) ? $sch_acc['disable_pay_reason'] : NULL),
                    'show_gift_article' => (isset($acc['show_gift_article']) ? $acc['show_gift_article'] : 0),
                    'firstPayment_amt' => (isset($sch_acc['firstPayment_amt']) ? $sch_acc['firstPayment_amt'] : NULL),
                    //   'is_new'	        => (isset($sch_acc['is_new'])?$sch_acc['is_new']:'Y'),							
                    'remark_open' => (isset($sch_acc['remark_open']) ? $sch_acc['remark_open'] : NULL),
                    'date_upd' => date("Y-m-d H:i:s"),
                    'id_employee' => (isset($sch_acc['id_employee']) ? $sch_acc['id_employee'] : NULL),
                    'duplicate_passbook_issued' => (isset($sch_acc['duplicate_passbook_issued']) ? $sch_acc['duplicate_passbook_issued'] : 2),
                    'lump_joined_weight' => ($sch_acc['is_lumpSum'] == 1 && $sch_acc['lump_joined_weight'] > 0 ? $sch_acc['lump_joined_weight'] : '0.000'),
                    'lump_payable_weight' => ($sch_acc['is_lumpSum'] == 1 && $sch_acc['lump_joined_weight'] > 0 ? ($sch_acc['lump_joined_weight'] / $sch_acc['total_installments']) : '0.000'),
                    /* TopUp Scheme Esakki_20250802 */
                    'topup_rate' => !empty($sch_acc['topup_rate']) ? $sch_acc['topup_rate'] : 0.00,
                    'topup_amount' => !empty($sch_acc['topup_amount']) ? ROUND($sch_acc['topup_amount']) : 0.00,
                    'topup_weight' => !empty($sch_acc['topup_weight']) ? $sch_acc['topup_weight'] : 0.000,
                );
                //echo "<pre>"; print_r($account);exit;
                if ($account['id_customer'] > 0 && $account['id_scheme'] > 0) {
                    $this->db->trans_begin();
                    $this->$model->update_account($account, $id);
                    //lines created by Durga 13.02.2023 starts here 
                    $gift_data = $this->input->post('gift_list_data');
                    //getting data from gift_issued table by id_scheme_account
                    // get the count 
                    $count = count($gift_data);
                    //outstock quantity is deducted from gift master
                    $gift_master_stock = $this->$model->get_gift_master_details($id);
                    // delete the entry for the scheme account in gift_issued table
                    //if issued deleting old data from gift_issued table
                    $delete_gift = $this->$model->delete_gift_issued($id);
                    // insert the posted gifts from gift table as gift type 
                    $quantity = $this->input->post('gift_quantity');
                    $barcode = $this->input->post('gift_barcode');
                    $gift_table_length = $this->input->post('gift_table_length');
                    //print_r($gift_table_length);exit;
                    if ($gift_table_length >= 1) {
                        $gift_type = 1;
                        for ($i = 1; $i <= $gift_table_length; $i++) {
                            /*if($gift_data[$i]!='')
                            {
                            $data=array(
                                'type'=> $gift_type,
                                'gift_desc'           => $gift_data[$i],
                                'quantity'			  => $quantity[$i]!=''?$quantity[$i]:'',	
                                'id_scheme_account'   => $id,
                                'id_employee'         => $sch_acc['id_employee'],
                                'date_issued'         => date("Y-m-d H:i:s")
                                );
                                $insert_gift = $this->$model->insert_gift_issued($data);
                            }*/
                            if ($gift_data[$i] != '') {
                                $gift_name[$i] = $this->db->query("SELECT gift_name FROM gifts WHERE id_gift=" . $gift_data[$i])->row('gift_name');
                                //print_r($gift_data[$i]);exit;
                                $data = array(
                                    'type' => $gift_type,
                                    'id_gift' => $gift_data[$i],
                                    'gift_desc' => $gift_name[$i],
                                    'quantity' => $quantity[$i] != '' ? $quantity[$i] : '',
                                    'barcode' => $barcode[$i] != '' ? $barcode[$i] : NULL,
                                    'id_scheme_account' => $id,
                                    'id_employee' => $sch_acc['id_employee'],
                                    'date_issued' => date("Y-m-d H:i:s")
                                );
                                $insert_gift = $this->$model->insert_gift_issued($data);
                                //$update_status ---> quantity is updated as outstock in gift master
                                //$updated_status=$this->$model->update_gift_master($quantity[$i],$gift_data[$i]);
                                $payment_details = $this->db->query("Select id_payment from payment where id_scheme_account=" . $id . " and payment_status=1")->result_array();
                                if (count($payment_details) > 0) {
                                    $gift_details = $this->account_model->get_gift_issued_form($id);
                                    foreach ($gift_details as $gift) {
                                        if ($gift['gift_status'] == 0) {
                                            $update_gift_master = $this->account_model->update_gift_master($gift['quantity'], $gift['id_gift']);
                                        }
                                    }
                                    $upd_data = array('status' => 1);
                                    $upd_gift_status = $this->payment_model->upd_gift_status($upd_data, $id);
                                }
                            }
                        }
                    }
                    // insert the posted prize from prize text area as prize type 
                    $gift = $this->input->post('gift');
                    if ($gift[1]['prize'] != '') {
                        foreach ($gift as $key => $val) {
                            if ($val['prize'] != '') {
                                $gift_type = 2;
                                $gift_desc = $val['prize'];
                            }
                            if ($gift_desc != '') {
                                $giftData = array(
                                    'type' => $gift_type,
                                    'gift_desc' => $gift_desc,
                                    'id_scheme_account' => $id,
                                    'id_employee' => $sch_acc['id_employee'],
                                    'date_issued' => date("Y-m-d H:i:s")
                                );
                                $insert_gift = $this->$model->insert_gift_issued($giftData);
                            }
                        }
                        $payment_details = $this->db->query("Select id_payment from payment where id_scheme_account=" . $id . " and payment_status=1")->result_array();
                        if (count($payment_details) > 0) {
                            $upd_data = array('status' => 1);
                            $upd_gift_status = $this->payment_model->upd_gift_status($upd_data, $id);
                        }
                    }
                    /*WALLET TRANSACTION CASES BASED REFERRAL CODE EDITS IN ACCOUNT..... date added : 19-06-2024 By: Abi */
                    $wallet_data = $this->account_model->getwalletdata($id);
                    /* print_r($ref_edit_type);
                    print_r($ref_edit_remark);
                    echo "<pre>";print_r($wallet_data);exit; */
                    //case : 2 new ref code update ....insert credit entry
                    if ($sch_acc['referal_by'] != null && $ref_edit_type == '2' && $ref_edit_remark != '' && $wallet_data == 0) {
                        //insert wallet transaction entry
                        if ($sch_acc['referal_by'] == 1) {
                            $id_employee = $this->session->userdata('uid');
                            $type = 1; //1- employee 2- agent
                            $credit_for = 0; // credit while new scheme join with payment
                            $emp_refral = $this->payment_model->get_Incentivedata($acc['id_scheme'], $acc['id_scheme_account'], $type, $acc['firstPayID'], $credit_for);
                            if ($emp_refral > 0) {
                                foreach ($emp_refral as $emp) {
                                    if ($emp['is_refferal_by'] == 1) {
                                        if ($emp['referal_amount'] > 0) {
                                            $res = $this->insertEmployeeIncentive($emp, $acc['id_scheme_account'], $acc['firstPayID']);
                                            if ($emp['credit_for'] == 1) {
                                                $this->customerIncentive($emp, $acc['id_scheme_account'], $acc['firstPayID']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //customer benefits credit  22-10
                        if ($sch_acc['referal_by'] == 0) {
                            $id_employee = $this->session->userdata('uid');
                            $type = 1; //1- employee 2- agent
                            $credit_for = 1; // credit while new scheme join with payment
                            $emp_refral = $this->payment_model->get_Incentivedata($acc['id_scheme'], $acc['id_scheme_account'], $type, $acc['firstPayID'], $credit_for);
                            // echo '<pre>';print_r($emp_refral);exit;
                            if ($emp_refral > 0) {
                                foreach ($emp_refral as $emp) {
                                    if ($emp['is_refferal_by'] == 0) {
                                        if ($emp['referal_amount'] > 0) {
                                            if ($emp['credit_for'] == 1) {
                                                $this->customerIncentive($emp, $acc['id_scheme_account'], $acc['firstPayID']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //case : 1 old refcode update ..... update wallet details
                    if ($sch_acc['referal_by'] != null && $ref_edit_type == '1' && $ref_edit_remark != '' && $wallet_data != 0) {
                        $exist_wallet_data = $this->account_model->get_referer_bycode($sch_acc['referal_code'], $sch_acc['referal_by']);
                        // print_r($exist_wallet_data);exit;
                        if (!empty($exist_wallet_data)) {
                            $wallet_update_data = array(
                                'id_wallet_account' => $exist_wallet_data['id_wallet_account'],
                            );
                        }
                        //print_r($wallet_update_data);exit;
                        $wallet_update = $this->account_model->updateData($wallet_update_data, 'id_sch_ac', $id, 'wallet_transaction');
                    }
                    //case : 3  old ref code removed.... insert debit entry
                    /*	print_r($ref_edit_type);
                    print_r($ref_edit_remark);
                    echo "<pre>";print_r($wallet_data);*/
                    if ($ref_edit_type == '3' && $ref_edit_remark != '' && $wallet_data != 0) {
                        $exist_wallet_data = $this->account_model->get_referer_bycode($acc['referal_code'], $acc['is_refferal_by']);
                        if (!empty($exist_wallet_data)) {
                            $debitAr = array(
                                'id_wallet_account' => $exist_wallet_data['id_wallet_account'],
                                'transaction_type' => 1, //0-Credit,1-Debit
                                'type' => 0, //CRM
                                'id_sch_ac' => $wallet_data[0]['id_sch_ac'],
                                'id_payment' => $wallet_data[0]['id_payment'],
                                'value' => $wallet_data[0]['value'],
                                'description' => 'Chit Referral Debit ' . $ref_edit_remark,
                                'date_transaction' => date("Y-m-d H:i:s"),
                                'id_employee' => $this->session->userdata('uid'),
                                'date_add' => date("Y-m-d H:i:s"),
                            );
                            //echo "<pre>";print_r($debitAr);
                            $this->payment_model->insertData($debitAr, 'wallet_transaction');
                            //echo "<pre>";print_r($this->db->last_query());exit;
                        }
                    }
                    /*/*WALLET TRANSACTION CASES BASED REFERRAL CODE EDITS IN ACCOUNT ENDS../.*/
                    //lines created by Durga 13.02.2023 ends here 
                    if ($this->db->trans_status() === TRUE) {
                        $this->db->trans_commit();
                        $log_data = array(
                            'id_log' => $this->id_log,
                            'event_date' => date("Y-m-d H:i:s"),
                            'module' => 'Scheme Account',
                            'operation' => 'Edit',
                            'record' => $id,
                            'remark' => 'Scheme Account updated successfully'
                        );
                        $this->$log_model->log_detail('insert', '', $log_data);
                        $this->session->set_flashdata('chit_alert', array('message' => 'Scheme account "' . $account['account_name'] . '" edited successfully', 'class' => 'success', 'title' => 'Edit Scheme Account'));
                        redirect('account/new');
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request ', 'class' => 'danger', 'title' => 'Edit Scheme Account'));
                    }
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Edit Scheme Account'));
                    redirect('account/new');
                }
                break;
            case 'Delete':
                $this->db->trans_begin();
                $data = $this->$model->delete_account($id);
                if ($data['status'] == 1) {
                    $this->db->trans_commit();
                    $log_data = array(
                        'id_log' => $this->id_log,
                        'event_date' => date("Y-m-d H:i:s"),
                        'module' => 'Scheme Account',
                        'operation' => 'Delete',
                        'record' => $id,
                        'remark' => 'Scheme Account deleted successfully'
                    );
                    $this->$log_model->log_detail('insert', '', $log_data);
                    $this->session->set_flashdata('chit_alert', array('message' => 'Scheme account deleted successfully', 'class' => 'success', 'title' => 'Delete Scheme Account'));
                    redirect('account/new');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => $data['msg'], 'class' => 'danger', 'title' => 'Delete Scheme Account'));
                    redirect('account/new');
                }
                break;
        }
    }
    public function account_registration($id_customer, $id_scheme, $id_register)
    {
        $model = self::ACC_MODEL;
        $data['scheme'] = $this->$model->set_registration_record($id_customer, $id_scheme, $id_register);
        $data['main_content'] = self::REG_VIEW . "form";
        $this->load->view('layout/template', $data);
    }
    function registration_form_post($id)
    {
        $model = self::ACC_MODEL;
        $sch_acc = $this->input->post('scheme');
        $account = array(
            'id_scheme' => (isset($sch_acc['id_scheme']) ? $sch_acc['id_scheme'] : 0),
            'id_customer' => (isset($sch_acc['id_customer']) ? $sch_acc['id_customer'] : 0),
            'account_name' => (isset($sch_acc['account_name']) ? $sch_acc['account_name'] : NULL),
            'ref_no' => (isset($sch_acc['ref_no']) ? $sch_acc['ref_no'] : NULL),
            'paid_till' => (isset($sch_acc['paid_till']) ? $sch_acc['paid_till'] : 0),
            'start_date' => (isset($sch_acc['start_date']) ? date('Y-m-d H:i:s', strtotime($sch_acc['start_date'])) : NULL),
            'employee_approved' => $employee['id_employee'],
            'remark_open' => (isset($sch_acc['remark_open']) ? $sch_acc['remark_open'] : NULL),
            'active' => 1,
            'date_add' => date("Y-m-d H:i:s")
        );
        $this->db->trans_begin();
        $status = $this->$model->insert_account($account);
        if ($status) {
            $reg = array('is_approved' => 1);
            $this->$model->update_reg_status($reg, $id);
        }
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $this->session->set_flashdata('chit_alert', array('message' => 'Scheme account <' . $sch_acc['account_name'] . '> created successfully', 'class' => 'success', 'title' => 'Create Scheme Account'));
            redirect('account/new');
        } else {
            $this->db->trans_rollback();
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Create Scheme Account'));
        }
    }
    public function close_account_list()
    {
        $model = self::ACC_MODEL;
        // $data['accounts'] = $this->$model->get_all_closed_account();
        //echo "<pre>";print_r($data);echo "</pre>";exit;
        $data['main_content'] = self::ACC_CLOSE_VIEW . 'list';
        $this->load->view('layout/template', $data);
    }
    /*-- Coded by ARVK --*/
    public function closed_acc_detail($id)
    {
        $model = self::ACC_MODEL;
        $account = $this->$model->get_closed_account_by_id($id);
        $account['img_path'] = file_exists(self::SCH_CLOSEING_PATH . $id . "/" . $id . ".png") ? self::SCH_CLOSEING_PATH . $id . "/" . $id . ".png" : NULL;           //webcam upload, #AD On:20-12-2022,Cd:CLin,Up:AB
        //echo "<pre>";print_r($account);echo "</pre>";exit;	
        echo json_encode($account);
    }
    public function acc_fetch_otp($id, $otp_entered)
    {
        $model = self::ACC_MODEL;
        $otp_data = $this->$model->otp_code_select($id);
        //echo "<pre>";print_r($account);echo "</pre>";exit;
        if ($otp_data['otp_code'] == $otp_entered) {
            $data['is_verified'] = '1';
            $data['verified_time'] = date("Y-m-d H:i:s");
            $verify = $this->$model->otp_update($data, $id);
            if ($verify == 1) {
                echo 1;
            }
        } else {
            echo 2;
        }
    }
    public function acc_close_otp($mobile, $id_customer, $cus_name = "")
    {
        //print_r($_POST);exit;
        $mob_no_len = $this->session->userdata('mob_no_len');
        $account = $_POST;
        $set_model = self::SET_MODEL;
        $model = self::ACC_MODEL;
        $pay_model = self::PAY_MODEL;
        $this->comp = $this->$model->company_details();
        $data = $this->$pay_model->get_customer($id_customer);
        if (strlen(trim($mobile)) == $mob_no_len) {
            $this->session->unset_userdata("OTP");
            $OTP = mt_rand(100001, 999999);
            $this->session->set_userdata('OTP', $OTP);
            $account['otp_code'] = $OTP;
            $account['otp_gen_time'] = date("Y-m-d H:i:s"); //echo "<pre>";print_r($account);echo "</pre>";//exit;
            //lines added by durga 22/12/2022 starts from here
            //loading otp details in $otpdata to insert in otp table in database
            $otpdata['otp_code'] = $OTP;
            $otpdata['otp_gen_time'] = date("Y-m-d H:i:s");
            $otpdata['send_resend'] = $account['send_resend'];
            $otpdata['id_sch_acc'] = $account['id_sch_acc'];
            $otpdata['id_emp'] = $account['id_emp'];
            //lines added by durga 22/12/2022 Ends  here
            $service = $this->$set_model->get_service(25);
            $dlt_id = $service['dlt_te_id'];
            $params = [];
            $sms_data = array(
                "fname" => $_POST['name'],
                "otp" => $OTP
            );
            $field_name = explode('@@', $service['sms_msg']);
            $sms_msg = $service['sms_msg'];
            for ($i = 1; $i < count($field_name); $i += 2) {
                $field = $field_name[$i];
                if (isset($sms_data[$field])) {
                    $params[] = ["type" => "text", "text" => $data->$field];
                    $sms_msg = str_replace("@@" . $field . "@@", $sms_data[$field], $sms_msg);
                }
            }
            // print_r($sms_msg);exit;
            // $message = "Dear ".$cus_name.", OTP for your saving scheme account closing is: ".$OTP.". Regards, SRI GANESH JEWELLERY MART.";
            $this->send_sms($mobile, $sms_msg, $dlt_id);
            if ($service['serv_whatsapp'] == 1) {
                $smsData = ["message" => $sms_msg, "template_name" => $service['template_name'], "params" => $params];
                $this->sms_model->send_whatsApp_message($mobile, $smsData);
            }
            if ($data['email'] != '') {
                $to = $data['email'];
                $data['type'] = 3;
                $data['name'] = str_replace('%20', ' ', $cus_name);
                $data['otp'] = $OTP;
                $data['company'] = $this->comp;
                $subject = "Reg: " . $this->comp['company_name'] . " saving scheme closing";
                $message = $this->load->view('include/emailAccount', $data, true);
                $this->load->model('email_model');
                $sendEmail = $this->email_model->send_email($to, $subject, $message);
            }
            $select = $this->$model->otp_select($account['id_sch_acc']);
            if ($select == TRUE) {
                $id = $account['id_sch_acc'];
                //$status = $this->$model->otp_update($account,$id);
                $status = $this->$model->otp_update($otpdata, $id);
            } else {
                $status = $this->$model->otp_insert($otpdata);
            }
            if ($status) {
                //lines altered by durga 22/12/2022
                $result = array('result' => 1, 'msg' => 'success', 'otp' => $OTP);
                echo json_encode($result);
                //echo 1;
            }
        }
    }
    /*-- / Coded by ARVK --*/
    public function close_account_form($type = "", $id = "")
    {
        $model = self::ACC_MODEL;
        $log_model = self::LOG_MODEL;
        $set_model = self::SET_MODEL;
        $pay_model = self::PAY_MODEL;
        $employee = $this->session->userdata('uid');
        switch ($type) {
            case 'Close':
                $account = $this->$model->get_close_account($id);
                $data['gift_data'] = $this->$model->get_gift_issued($id); //line added by Durga 15.02.2023
                $tot_paid_GA_amt = 0;
                $tot_paid_GA_wgt = 0;
                $tot_bonus_GA_wgt = 0;
                $voucher_deduct = 0;
                //voucher mode deduction if preclose starts here
                if ($account['paid_installments'] < $account['total_installments']) {
                    $voucher_mode_data = $this->$model->get_voucher_mode_detail($id);
                    //print_r($voucher_mode_data);exit;
                    $voucher_deduct = $voucher_mode_data['payment_amount'];
                }
                //voucher mode deduction if preclose ends here  
                //set bebefit and deduction calculation type based on scheme settings...//RHR
                if ($account['is_digi'] == 1) {
                    $account['calculate_by'] = 1; // benefit and deduction based on digi gold formula
                } else if ($account['calculation_type'] == 2 && $account['maturity_days'] > 0) {
                    $account['calculate_by'] = 2; // benefit and deduction based on maturity days formula
                } else {
                    $account['calculate_by'] = 0; // common calculation for benefit and deduction 
                }
                //defined whether its amount scheme or weight scheme
                if ($account['sch_typ'] == 0 || $account['sch_typ'] == 3 && ($account['flexible_sch_type'] == 1 || $account['flexible_sch_type'] == 6 || ($account['flexible_sch_type'] == 2 && $account['wgt_convert'] == 2))) {
                    $is_weight = 0;
                } elseif ($account['sch_typ'] == 1 || $account['sch_typ'] == 2 || ($account['sch_typ'] == 3 && (($account['flexible_sch_type'] == 2 && ($account['wgt_convert'] == 0 || $account['wgt_convert'] == 1)) || $account['flexible_sch_type'] == 3 || $account['flexible_sch_type'] == 4 || $account['flexible_sch_type'] == 5 || $account['flexible_sch_type'] == 7 || $account['flexible_sch_type'] == 8))) {
                    if (($account['flexible_sch_type'] == 2 && ($account['wgt_convert'] == 0 || $account['wgt_convert'] == 1)) || $account['flexible_sch_type'] != 2) {
                        $is_weight = 1;
                    } elseif ($account['flexible_sch_type'] == 2 && $account['wgt_convert'] == 2) {
                        $is_weight = 0;
                    } else {
                        $is_weight = 1;
                    }
                }
                //	$metal_rate = $this->$set_model->metal_ratesDB('last','','');	
                $metal_rate = $this->payment_model->get_metalrate_by_branch($account['id_branch'], $account['id_metal'], $account['id_purity']);
                //RAHUL 24_11_25
                // $allow_benefit = ($account['pre_close_payments'] >= 1 ? ($account['preclose_benefits'] == 1 ? 1 : 0) : 1);
                $data['gift_data'] = $this->$model->get_gift_issued($id); //line added by Durga 15.02.2023
                $debit = 0;
                $reached_maturity_date = 0;
                $closing_maturity_date = NULL;
                $debited_closing_amt = NULL;
                $paid_amt = ($account['closing_amount'] === NULL ? '0.00' : $account['closing_amount']);
                $interest = ($account['firstPayamt_as_payamt'] == 1 ? ($account['firstPayment_amt'] > 0 ? $account['firstPayment_amt'] : 0) : 0);
                //RAHUL 24_11_25
                // $allow_benefit = ($account['pre_close_payments'] >= 1 ? ($account['preclose_benefits'] == 1 ? 1 : 0) : 1);
                $cur_date = date('Y-m-d');  // today's date
                $maturity_date = $account['maturity_date'];
                // Usage
                $allow_benefit = $this->calculateAllowBenefit($account, $cur_date);
                /*  $allow_benefit = ($cur_date > $maturity_date ?($account['allow_preclose'] == 1 && $account['preclose_months'] >= 1 &&
               ( $account['preclose_type'] == 1 ? $account['paid_installments'] >  $account['preclose_months'] : $cur_date > $account['start_date'] +  $account['preclose_months']) && $account['preclose_benefits'] == 1 ? 1 : 0):1); */
                $empref_data = '';
                $agentref_data = '';
                //check employee , agent benefit and debit the amount if customer preclose
                if ($account['paid_installments'] != $account['total_installments'] && $account['allow_preclose'] == 0) {
                    if ($account['emp_refferal'] == 1 && $account['emp_deduct_ins'] > $account['paid_installments']) {
                        $empref_data = $this->$model->getEmpBenefit($account['id_scheme_account']);
                    }
                    if ($account['agent_refferal'] == 1 && $account['agent_deduct_ins'] > $account['paid_installments']) {
                        $agentref_data = $this->$model->getAgentBenefit($account['id_scheme_account']);
                    }
                }
                // Check Maturity Date
                if ($account['closing_maturity_days'] != NULL) {
                    if ($account['paid_installments'] == $account['total_installments']) {
                        $closing_maturity_date = date('Y-m-d', strtotime($account['last_paid_date'] . ' + ' . $account['closing_maturity_days'] . ' days'));
                        $account['maturity_date'] = $closing_maturity_date;
                        $reached_maturity_date = (strtotime($closing_maturity_date) <= time() ? 1 : 0);
                    }
                } else if ($account['maturity_date'] != NULL) {
                    $reached_maturity_date = (strtotime($account['maturity_date']) <= time() ? 1 : 0);
                }
                $giftAmt_issued = $this->$model->get_giftValue($account['id_scheme_account']);
                $giftAmt_assigned = $this->$model->get_assigned_gift_value($account['id_scheme']);
                $pending_gift_value = ($account['total_installments'] == $account['paid_installments'] ? ($giftAmt_issued != $giftAmt_assigned ? ($giftAmt_assigned - $giftAmt_issued) : $giftAmt_issued) : 0);
                //RHR scheme : benefit calculation code starts 20-09-2023 #AB....
                if ($account['apply_benefit_by_chart'] == 1 && $account['sch_int_setting'] == 1) {
                    $interestData = $this->$model->getAccBenefitDeduction($account);
                    $interestData['id_scheme_account'] = $account['id_scheme_account'];
                    $interestData['calculate_by'] = $account['calculate_by'];
                    $interestData['maturity_days'] = $account['maturity_days'];
                    $bonus_available = $this->$model->is_bonus_available($account['id_scheme']);
                    $interestData['is_weight'] = $is_weight;   // 0 - amount, 1 - weight
                    // 	echo '<pre>';print_r($account);exit;
                    if ($account['calculate_by'] == 1) {
                        //digi gold scheme benefit calc....
                        $acc_interest = $this->$model->getPaymentData($interestData);
                        $interest_val = ($interestData['interest_value'] != '' ? ($interestData['interest_value'] == 0 ? 'INR ' . $interestData['interest_value'] . '' : $interestData['interest_value'] . ' %') : '');
                    } else if ($account['calculate_by'] == 2) {
                        //scheme benefit calc for  fixed maturity settings....
                        if ($bonus_available == 1 && $account['is_limit_exceed'] == 0 && $account['paid_installments'] >= $account['total_installments']) {
                            //take installment amount given in interest chart...
                            $acc_interest = $this->$model->getBonusInsAmt($account);
                            $interest_val = 'Bonus';
                        } else {
                            $acc_interest = $this->$model->getPaymentData($interestData);
                            $interest_val = ($interestData['interest_value'] != '' ? ($interestData['interest_type'] == 1 ? 'INR ' . $interestData['interest_value'] . '' : $interestData['interest_value'] . ' %') : '');        // esakki 10-10
                        }
                    } else {
                        //common benefit calculation...
                        if ($account['pending_installments'] > 0 || $reached_maturity_date == 0) { // Has pending installement or Maturity date not reached
                            if (sizeof($interestData) > 0) {
                                if ($interestData['interest_type'] == 0) { // Percent
                                    //$acc_interest = $account['interest'] * ($interestData['interest_value'] /100);
                                    $acc_interest = $this->$model->getPaymentData($interestData);
                                } else if ($interestData['interest_type'] == 1) { // Amount
                                    $acc_interest = $interestData['interest_value'];      //bug line
                                }
                                $interest_val = ($interestData['interest_value'] != '' ? ($interestData['interest_type'] == 1 ? 'INR ' . $interestData['interest_value'] . '' : $interestData['interest_value'] . ' %') : '');        // esakki 10-10
                            }
                        } else { // Completed installment and reached Maturity date
                            $acc_interest = ($allow_benefit == 1 ? ($account['interest'] === NULL ? $interest : number_format($account['interest'], 2, '.', '')) : 0.00);
                            $interest_val = ($interestData['interest_value'] != '' ? ($interestData['interest_type'] == 1 ? 'INR ' . $interestData['interest_value'] . '' : $interestData['interest_value'] . ' %') : '');   // esakki 10-10
                        }
                    }
                }
                //RAHUL 24_11_25
                $acc_interest = $allow_benefit ? $acc_interest : 0;
                //benefit calculation code ends 20-09-2023 #AB....
                //scheme benefit deduction code starts 20-09-2023 #AB...
                if ($account['apply_debit_on_preclose'] == 1) {
                    $debitArr = $this->$model->getAccBlcDebitSettings($account);
                    $debitdata['id_scheme_account'] = $account['id_scheme_account'];
                    if ($account['calculate_by'] == 1 || $account['calculate_by'] == 2) {
                        //scheme deduction calc for fixed maturity days scheme && digi gold....
                        $debitdata['interest_value'] = $debitArr['deduction_value'];
                        $debitdata['interest_type'] = $debitArr['deduction_type'];
                        $debit = $this->$model->getPaymentData($debitdata);
                        $debit_val = $debitArr['deduction_value'];
                    } else {
                        //common scheme deduction calc...
                        if ($debitArr['deduction_type'] == 0) { // Percent
                            $debit = $paid_amt * ($debitArr['deduction_value'] / 100);
                            $debit_val = $debitArr['deduction_value'];
                        } else if ($debitArr['deduction_type'] == 1) { // Amount
                            $debit = $debitArr['deduction_value'];
                            $debit_val = $debitArr['deduction_value'];
                        }
                        // NOTE :: For weight & amount to weight scheme, apply debit in terms of cash
                    }
                    /* GIFT DEDUCT CALCULATION STARTS...*/
                    // $gift_DebtArr = $this->$model->get_giftDebitSettings($account);
                    $gift_DebtArr = [];
                    //echo '<pre>';print_r($gift_DebtArr);exit;
                    if ($gift_DebtArr['deduct_in'] == 1) {
                        if ($giftAmt_issued > 0) {
                            if ($gift_DebtArr['deduction_type'] == 0) { // Percent
                                $gift_debt = $giftAmt_issued * ($gift_DebtArr['deduction_value'] / 100);
                                $gift_debt_val = $gift_DebtArr['deduction_value'];
                                $gift_debt_set = $gift_DebtArr['deduction_value'] . ' %';
                            } else if ($gift_DebtArr['deduction_type'] == 1) { // Amount
                                $gift_debt = $debitArr['deduction_value'];
                                $gift_debt_val = $gift_DebtArr['deduction_value'];
                                $gift_debt_set = 'INR ' . $gift_DebtArr['deduction_value'];
                            }
                        }
                    }
                    /* GIFT DEDUCT CALCULATION ENDS...*/
                }
                //scheme benefit deduction code ends 20-09-2023 #AB...
                if ($account['one_time_premium'] == 1 && $account['otp_price_fixing'] == 1 && $account['fixed_metal_rate'] != NULL) {
                    $discount_amount = $this->$model->getDiscountByjoin($account['id_scheme_account'], $account['id_scheme'], $account['start_date']);
                    if ($discount_amount != 0) {
                        if ($discount_amount['interest_type'] == 0)  //based on int percentage
                        {
                            $acc_interest = round($account['fixed_metal_rate'] * ($discount_amount['interest_value'] / 100));
                            $discount_gold_rate = $account['fixed_metal_rate'] - $acc_interest;
                            $discount_weight = round($account['firstPayment_amt'] / $discount_gold_rate, 3);
                        } else if ($discount_amount['interest_type'] == 1)   //based on int amount
                        {
                            $acc_interest = $discount_amount['interest_value'];
                            $discount_gold_rate = $account['fixed_metal_rate'] - $acc_interest;
                            $discount_weight = round($account['firstPayment_amt'] / $discount_gold_rate, 3);
                        }
                    }
                }
                //echo " allow benefit - ".$allow_benefit."<br/> Interest - ".$interest."<br/> Pending Ins - ".$account['pending_installments']."<br/> Interest - ".$acc_interest."<br/> Maturity - ".$closing_maturity_date;
                //as amount
                if ($is_weight == 0) {
                    $closingBal = $account['closing_paidAmt'];
                    $acc_int_amount = $acc_interest;
                    //as weight        
                } else if ($is_weight == 1) {
                    if (($account['flexible_sch_type'] == 2 && $account['wgt_convert'] == 0)) {
                        $closingBal = $account['closing_paidWgt'];
                        $acc_int_amount = $acc_interest / $metal_rate;
                    } elseif ($account['flexible_sch_type'] == 2 && $account['wgt_convert'] == 1) {
                        $closingBal = $account['closing_paidAmt'] / $metal_rate;
                        $acc_int_amount = $acc_interest / $metal_rate;
                    } else {
                        $closingBal = $account['closing_paidWgt'];
                        $acc_int_amount = $acc_interest / $metal_rate;
                    }
                }
                //tkv Chit General Advance settings (GA) block with separate benefit settings .... Dt Added : 09-11-2023, By: #AB
                //echo '<pre>';print_r($account);exit;
                $genAdv_show_bonus = $this->db->query("SELECT sg.installment_from, sg.installment_to,sg.interest_type,sg.interest_value, 
	                                DATE_ADD(date(sa.start_date), INTERVAL + (sg.installment_from - 1) month) as calculate_date_from, 
	                                DATE_ADD(date(sa.start_date), INTERVAL + (sg.installment_to - 1) month) as calculate_date_to 
	                                FROM scheme_account sa 
	                                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme) 
	                                LEFT JOIN `scheme_general_advance_benefit_settings` sg ON (sg.id_scheme = s.id_scheme) 
	                                WHERE s.id_scheme = " . $account['id_scheme'] . " and sa.id_scheme_account = " . $account['id_scheme_account'] . "
	                                and EXTRACT( YEAR_MONTH FROM (DATE_ADD(date(sa.start_date), INTERVAL + sg.installment_to month))) <= EXTRACT( YEAR_MONTH FROM '2024-10-07')
                ")->result_array();
                if ($account['allow_general_advance'] == 1 && $account['apply_adv_benefit'] == 1 && sizeof($genAdv_show_bonus) > 0) {
                    foreach ($genAdv_show_bonus as $sb) {
                        $value = $sb['interest_value'];
                        $type = $sb['interest_type'];
                        $calc_date_from = $sb['calculate_date_from'];
                        $calc_date_to = $sb['calculate_date_to'];
                        $range = $sb['installment_from'] . ' - ' . $sb['installment_to'] . ' months';
                        $ben_value = ($type == 0 ? $value . ' %' : 'INR ' . $value);
                        $ga_data = $this->$model->get_generalAdv_BonusData($account['id_scheme_account'], $value, $type, $calc_date_from, $calc_date_to);
                        $tot_bonus_GA += $ga_data['adv_bonus'];
                        $tot_bonus_GA_wgt += $ga_data['adv_bonus_wgt'];
                        $tot_paid_GA_amt += $ga_data['tot_adv_amt'];
                        $tot_paid_GA_wgt += $ga_data['tot_adv_wgt'];
                        $bonus_GA[] = array('range' => $range, 'bonus' => $ga_data['adv_bonus'], 'value' => $ben_value, 'bonus_paid_amt' => $ga_data['tot_adv_amt'], 'bonus_paid_wgt' => $ga_data['tot_adv_wgt']);
                    }
                } else {
                    $tot_bonus_GA = '0';
                    $bonus_GA = [];
                }
                //tkv ends...        
                $purchase_discount = $this->$model->is_MCVA_purchaseDiscount_available($account);
                //kovai NPR digi gold scheme benefit calc...
                $dg_benefit_amount = 0;
                $dg_benefit_weight = 0;
                if ($account['is_digi'] == 1 && $account['apply_benefit_by_chart'] == 1 && $account['sch_int_setting'] == 1) {
                    if ($account['maturity_date'] <= date('Y-m-d')) {
                        //full matured 
                        $dg_saved_benefit_amount = $account['dg_saved_benefit_amount'];
                        $dg_saved_benefit_weight = $account['dg_saved_benefit_weight'];
                        $dg_benefit_amount = $account['dg_saved_benefit_amount'] + $account['dg_other_benefit_amount'];
                        $dg_benefit_weight = $account['dg_saved_benefit_weight'] + $account['dg_other_benefit_weight'];
                        $dg_other_benefit_amt = $account['dg_other_benefit_amount'];
                        $dg_other_benefit_wgt = $account['dg_other_benefit_weight'];
                        // $acc_int_amount = $dg_saved_benefit_amount;
                    } else {
                        //pre matured
                    }
                    $account['interest_val'] = '';
                }
                $data['emp_referal'] = $empref_data;
                $data['agent_referal'] = $agentref_data;
                $data['account'] = array(
                    'dg_saved_benefit_amount' => round($dg_saved_benefit_amount, 2),
                    'dg_saved_benefit_weight' => round($dg_saved_benefit_weight, 4),
                    'dg_other_benefit_amt' => round($dg_other_benefit_amt, 2),
                    'dg_other_benefit_wgt' => round($dg_other_benefit_wgt, 3),
                    'joined_date_diff' => (!empty($account['joined_date_diff']) ? $account['joined_date_diff'] : 1),
                    'is_digi' => $account['is_digi'],
                    'gift_debt_amt' => $gift_debt,
                    'gift_debt_set' => $gift_debt_set,
                    'gift_issued_value' => $giftAmt_issued,
                    'pending_gift_value' => $pending_gift_value,
                    'giftAmt_assigned' => $giftAmt_assigned,
                    'has_gift' => $account['has_gift'],
                    'bonus_percent' => $purchase_discount,
                    //TKV starts    
                    'gen_adv_bonus' => $bonus_GA,
                    'tot_gen_adv_bonus' => round($tot_bonus_GA),
                    'bonus_paid_amt' => $tot_paid_GA_amt,
                    'bonus_paid_wgt' => $tot_paid_GA_wgt,
                    'tot_gen_adv_bonus_wgt' => $tot_bonus_GA_wgt,
                    //TKV ends    
                    'id_scheme_account' => $account['id_scheme_account'],
                    'close_date' => date('d-m-Y'),
                    'account_name' => $account['account_name'],
                    'id_customer' => $account['id_customer'],
                    'name' => $account['name'],
                    'cus_img' => ($account['cus_img'] === NULL ? '../default.png' : $account['id_customer'] . "/" . $account['cus_img']),
                    'nominee_name' => $account['nominee_name'],
                    'nominee_mobile' => $account['nominee_mobile'],
                    'charges' => $account['charges'],
                    'ref_no' => $account['ref_no'],
                    'code' => $account['code'],
                    'scheme_acc_number' => $account['scheme_acc_number'],
                    'mobile' => $account['mobile'],
                    'email' => $account['email'],
                    'scheme_name' => $account['scheme_name'],
                    'amount' => $account['amount'],
                    'scheme_type' => $account['scheme_type'],
                    'flexible_sch_type' => $account['flexible_sch_type'],
                    'sch_typ' => $account['sch_typ'],
                    'start_date' => date('d-m-Y', strtotime($account['start_date'])),
                    'total_installments' => $account['total_installments'],
                    'paid_installments' => $account['paid_installments'],
                    'unapproved_payment' => $account['unapproved_payment'],
                    'pending_installments' => $account['pending_installments'],
                    'bank_chgs' => ($account['bank_chgs'] === NULL ? '0.00' : $account['bank_chgs']),
                    'reached_maturity_date' => $reached_maturity_date,
                    'interest' => ($account['firstPayamt_as_payamt'] == 1 ? $interest : number_format($acc_int_amount, '3', '.', '')),    //if 1st installment is set as payable means that amount should be the discount given during scheme closing//HH
                    'debit' => ($debit > 0 && $debit != NULL ? $debit : '0.00'),
                    'apply_debit_on_preclose' => $account['apply_debit_on_preclose'],
                    'apply_benefit_by_chart' => $account['apply_benefit_by_chart'],
                    'tax' => ($account['tax'] === NULL ? '0.00' : number_format($account['tax'], 2, '.', '')),
                    'closing_amount' => ($account['closing_amount'] === NULL ? '0.00' : ($account['closing_amount'])),
                    'closing_balance' => ($account['closing_balance'] === NULL ? '0.00' : $closingBal),
                    'employee_closed' => ($employee ? $employee : 0),
                    'closed_by' => 0,
                    'rep_name' => NULL,
                    'rep_mobile' => NULL,
                    'remark_close' => ($account['remark_close'] === NULL ? NULL : $account['remark_close']),
                    'allow_preclose' => $account['allow_preclose'],
                    'preclose_months' => $account['preclose_months'],
                    'preclose_benefits' => $account['allow_preclose'],
                    'enable_closing_otp' => $account['enable_closing_otp'],
                    'add_benefits' => $account['additional_benefits'],
                    'add_charges' => $account['closing_add_chgs'],
                    'wgt_convert' => $account['wgt_convert'],
                    //'metal_rate'			=>  $metal_rate['goldrate_22ct'],
                    //'metal_rate'			=>  $account['id_metal']==1 ? $metal_rate['goldrate_22ct'] : $metal_rate['silverrate_1gm'],
                    'metal_rate' => $metal_rate,
                    'one_time_premium' => $account['one_time_premium'],
                    'totalunpaid' => $account['totalunpaid'],
                    'flexible_sch_type' => $account['flexible_sch_type'],
                    'firstPayment_amt' => $account['firstPayment_amt'],
                    'discount_installment' => $account['discount_installment'],
                    'maturity_date' => $account['maturity_date'],
                    'fixed_metal_rate' => $account['fixed_metal_rate'],
                    'discount_metal' => $discount_gold_rate,
                    'discount_weight' => $discount_weight,
                    'gift_exist' => $account['gift_acc'],
                    'issued_gift' => $account['gift_desc'],
                    'closing_paid_amt' => $account['cus_paid_amount'],
                    'interest_val' => $interest_val,
                    'debit' => $debit,
                    'debit_val' => $debit_val,    //DGS-DCNM
                    'schemeaccNo_displayFrmt' => (isset($account['schemeaccNo_displayFrmt']) ? $account['schemeaccNo_displayFrmt'] : 0),
                    'scheme_wise_acc_no' => (isset($account['scheme_wise_acc_no']) ? $account['scheme_wise_acc_no'] : 0),
                    'acc_branch' => (isset($account['acc_branch']) ? $account['acc_branch'] : ''),
                    'start_year' => (isset($account['start_year']) ? $account['start_year'] : ''),
                    'acc_interest' => $acc_int_amount,
                    'voucher_deduct' => $voucher_deduct,
                    'closing_paidWgt' => $account['closing_paidWgt'] + $dg_saved_benefit_weight,
                    'is_weight' => $is_weight,
                    'store_closing_balance' => $account['store_closing_balance'],
                    'joined_date_diff' => (!empty($account['joined_date_diff']) ? $account['joined_date_diff'] : 1),
                    //'tot_adv_amt_paid'	=> $tot_paid_GA_amt,
                    //'tot_adv_wgt_paid'	=> $tot_paid_GA_wgt,
                    //'tot_adv_benefit'	=> $tot_bonus_GA,
                );
                // echo "<pre>";print_r($data); exit;
                if ($is_weight == 0) {
                    if ($account['pending_installments'] != 0) {
                        $data['account']['closing_balance'] = number_format($closingBal - $account['tax'] - $debit - $account['bank_chgs'] - $account['charges'] + $acc_int_amount + $tot_paid_GA_amt + $tot_bonus_GA + $dg_saved_benefit_amount, 2, '.', '');
                        $data['account']['closing_amount'] = number_format($data['account']['closing_paid_amt'] - $account['tax'] - $debit - $account['bank_chgs'] - $account['charges'] + $acc_interest + $tot_paid_GA_amt + $tot_bonus_GA + $dg_benefit_amount, 2, '.', '');
                    } else {
                        $data['account']['closing_amount'] = number_format($data['account']['closing_paid_amt'] - $account['tax'] - $debit - $account['bank_chgs'] - $account['charges'] + $acc_interest + $tot_paid_GA_amt + $tot_bonus_GA + $dg_benefit_amount, 2, '.', '');
                        $data['account']['closing_balance'] = number_format($closingBal + $interest + $acc_int_amount - $account['tax'] - $debit - $account['bank_chgs'] - $account['charges'] + $tot_paid_GA_amt + $tot_bonus_GA + $dg_saved_benefit_amount, 2, '.', '');
                    }
                } else {
                    // if($is_weight == 1){
                    //         $data['account']['closing_amount']	= number_format($data['account']['closing_paid_amt'] - $account['tax']-$debit-$account['bank_chgs']-$account['charges']+$acc_interest + $tot_paid_GA_amt + $tot_bonus_GA, 2, '.', '') ;
                    //     	$data['account']['closing_weight']  = $data['account']['closing_balance'];
                    // }else{
                    // print_r($closingBal);exit;
                    if ($account['pending_installments'] != 0) {
                        $data['account']['closing_balance'] = formatMetalWeight($account['cus_paid_weight'] - $account['tax'] - ($debit / $metal_rate) - $account['bank_chgs'] - $account['charges'] + $acc_int_amount + $tot_paid_GA_amt + $tot_bonus_GA + $dg_saved_benefit_weight);
                        $data['account']['closing_amount'] = number_format($data['account']['closing_paid_amt'] - $account['tax'] - $debit - $account['bank_chgs'] - $account['charges'] + $acc_interest + $tot_paid_GA_amt + $tot_bonus_GA + $dg_benefit_amount, 2, '.', '');
                    } else {
                        $data['account']['closing_amount'] = number_format($data['account']['closing_paid_amt'] - $account['tax'] - $debit - $account['bank_chgs'] - $account['charges'] + $acc_interest + $tot_paid_GA_amt + $tot_bonus_GA + $dg_benefit_amount, 2, '.', '');
                        /*  $data['account']['closing_balance'] = number_format(($data['account']['closing_paid_amt'] + $interest + $acc_int_amount - $account['tax'] - $debit - $account['bank_chgs'] - $account['charges'] + $tot_paid_GA_amt + $tot_bonus_GA), 2, '.', ''); */
                        $data['account']['closing_balance'] = formatMetalWeight($account['cus_paid_weight'] + $interest + $acc_int_amount - $account['tax'] - ($debit / $metal_rate) - $account['bank_chgs'] - $account['charges'] + $tot_paid_GA_amt + $tot_bonus_GA + $dg_saved_benefit_weight);
                    }
                    // 	 }
                }
                $data['main_content'] = self::ACC_CLOSE_VIEW . "form";
                //echo "<pre>";print_r($data);echo "</pre>";exit;
                $this->load->view('layout/template', $data);
                break;
            case 'Save':
                $account = $this->input->post('account');
                $accounts = $this->$model->get_close_account($id);
                //  print_r($account);exit;
                $data = array(
                    'bonus_percent' => (isset($account['bonus_percent']) && $account['bonus_percent'] > 0 ? $account['bonus_percent'] : 0),
                    'closing_date' => date('Y-m-d H:i:s', time()),
                    'closing_balance' => $account['closing_balance'],
                    // 'closing_weight'        => ($account['sch_typ']== 1 || $account['sch_typ']== 2 || ($account['sch_typ']==3 && (($account['flexible_sch_type']==2 && ($account['wgt_convert'] == 0 || $account['wgt_convert'] == 1)) || $account['flexible_sch_type']==3 || $account['flexible_sch_type']==4 || $account['flexible_sch_type']==5 || $account['flexible_sch_type']==7 || $account['flexible_sch_type']==8)) ?$account['closing_balance'] :0) ,
                    // 'closing_weight'        => ($account['is_weight']== 1 && $account['store_closing_balance_as']== 1  ? $account['closing_balance'] :0) ,
                    /*"closing_weight" => $account["store_closing_balance_as"] == 1 || ($account["is_weight"] == 1 && $account["store_closing_balance_as"] == 0)
                        ? $account["closing_balance"]
                        : 0,*/
                    "closing_weight" => $account["store_closing_balance_as"] == 1 || ($account["is_weight"] == 1 && $account["store_closing_balance"] == 0)
                        ? $account["closing_balance"]
                        : 0,
                    // 'closing_amount'        => ($account['sch_typ']==0 || ($account['sch_typ']==3 && ($account['flexible_sch_type'] == 1 || ($account['flexible_sch_type'] == 2 && $account['wgt_convert'] == 2) || $account['flexible_sch_type'] == 6 )) ?$account['closing_balance'] :$account['closing_wgt_amount']) ,
                    'closing_amount' => $account['closing_amount'],
                    'Closing_id_branch' => $account['id_branch'],
                    //'closed_by'             => $this->session->userdata('uid'), old 05-12-2022
                    'closed_by' => (!empty($account['closed_by']) ? $account['closed_by'] : 0), // New code 05-12-2022
                    'employee_closed' => ($employee ? $employee : 0),
                    'rep_name' => ($account['closedBy'] == 1 ? $account['nominee_name'] : ''),
                    'rep_mobile' => ($account['closedBy'] == 1 ? $account['nominee_mobile'] : ''),
                    'closing_add_chgs' => ($account['add_charges'] ? $account['add_charges'] : '0.00'),
                    // 'additional_benefits'   => ($account['additional_benefits']!='' ? $account['additional_benefits']:'0.00'),
                    // 'closing_benefits' => ($account['interest'] != '' ? $account['interest'] : '0.00'),
                    // 'closing_benefits' => ($account['interest'] != '' ? number_format((float) $account['interest'], 3, '.', '') : '0.00'),
                    'closing_benefits' => ($account['interest'] != '' ? formatMetalWeight($account['interest']) : '0.00'),
                    'closing_deductions' => ($account['debit'] != '' ? $account['debit'] : '0.00'),
                    //'closing_deductions'    => ($account['closing_deductions']!='' ? $account['closing_deductions']:'0.00'),
                    'closing_paid_amt' => ($account['closing_paid_amt'] != '' ? $account['closing_paid_amt'] : '0.00'),
                    'remark_close' => ($account['remark_close'] ? $account['remark_close'] : ''),
                    'is_closed' => 1,
                    'active' => 0,
                    'additional_benefits' => ($account['add_benefits'] ? $account['add_benefits'] : '0.00'),
                    'add_ben_type' => (!empty($account['add_benefixed']) ? $account['add_benefixed'] : '0'),
                    //'closing_interest_val' => $account['interest_val'],
                    'closing_interest_val' => number_format((float) $account['interest_val'], 3, '.', ''),
                    'store_closing_balance_as' => $account['store_closing_balance_as'] != '' ? $account['store_closing_balance_as'] : NULL,
                    'dg_other_benefit_amt' => $account['dg_other_benefit_amt'],
                    'dg_other_benefit_wgt' => $account['dg_other_benefit_wgt'],
                    //     'tot_adv_amt_paid'	=> $account['tot_adv_amt_paid'],
                    // 	'tot_adv_wgt_paid'	=> $account['tot_adv_wgt_paid'],
                    // 	'tot_adv_benefit'	=> $account['tot_adv_benefit']
                );
                //	echo "<pre>";print_r($data);echo "</pre>";exit;
                $this->db->trans_begin();
                $status = $this->$model->update_account($data, $id);
                //print_r($this->db->last_query());exit;
                if ($status) {
                    $scheme_settings = $this->$model->get_closed_account_by_id($id);
                    //echo "<pre>"; print_r($scheme_settings);exit;
                    if ($scheme_settings['emp_incentive_closing'] == 1) {
                        $sch_close_benefits = $this->$model->checkSchemeCloseBeiefits($scheme_settings['id_scheme']);
                        if ($scheme_settings['closing_incentive_based_on'] == 2 && ($scheme_settings['apply_benefit_min_ins'] <= $scheme_settings['paid_installments'])) // Based on Weight
                        {
                            foreach ($sch_close_benefits as $acc) {
                                if (($acc['incentive_from'] <= $data['closing_weight']) && ($data['closing_weight'] <= $acc['incentive_to'])) {
                                    if ($acc['type'] == 2) // in Percentage
                                    {
                                        $metal_rates = $this->$model->getMetalRates();
                                        $credit_amt = ((($data['closing_weight'] * $acc['value']) / 100) * $metal_rates['goldrate_22ct']);
                                    }
                                }
                            }
                        } else if ($scheme_settings['closing_incentive_based_on'] == 1) {
                            foreach ($sch_close_benefits as $acc) {
                                if (($acc['incentive_from'] <= $scheme_settings['paid_installments']) && ($scheme_settings['paid_installments'] <= $acc['incentive_to'])) {
                                    $credit_amt = $acc['value'];
                                }
                            }
                        }
                        // echo "<pre>"; print_r($data['closing_weight']);exit;
                        if ($credit_amt > 0) {
                            $wallet_acc = $this->$pay_model->get_wallet_account($scheme_settings['id_employee']); // Check Wallet Acc Exists
                            if ($wallet_acc['status']) {
                                $WalletinsData = array(
                                    'id_wallet_account' => $wallet_acc['id_wallet_account'],
                                    'transaction_type' => 0, //0-Credit,1-Debit
                                    'type' => 0, //CRM
                                    'id_sch_ac' => $id,
                                    'value' => $credit_amt,
                                    'description' => 'Chit Referral Credit',
                                    'date_transaction' => date("Y-m-d H:i:s"),
                                    'id_employee' => $this->session->userdata('uid'),
                                    'date_add' => date("Y-m-d H:i:s"),
                                );
                                //echo "<pre>";print_r($WalletinsData);exit;
                                $this->$pay_model->insertData($WalletinsData, 'wallet_transaction');
                            } else {
                                $wallet_acc_no = $this->$model->get_wallet_acc_number();
                                $walletAcc = array(
                                    'idemployee' => $scheme_settings['id_employee'],
                                    'id_employee' => $this->session->userdata('uid'),
                                    'wallet_acc_number' => $wallet_acc_no,
                                    'issued_date' => date('y-m-d H:i:s'),
                                    'remark' => "Credits",
                                    'active' => 1
                                );
                                $id_wallet_acc = $this->$pay_model->insertData($walletAcc, 'wallet_account');
                                // print_r($this->db->last_query());exit;
                                if ($id_wallet_acc) {
                                    $WalletinsData = array(
                                        'id_wallet_account' => $id_wallet_acc['insertID'],
                                        'transaction_type' => 0, //0-Credit,1-Debit
                                        'type' => 0, //CRM
                                        'id_sch_ac' => $id,
                                        'value' => $credit_amt,
                                        'description' => 'Chit Referral Credit',
                                        'date_transaction' => date("Y-m-d H:i:s"),
                                        'id_employee' => $this->session->userdata('uid'),
                                        'date_add' => date("Y-m-d H:i:s"),
                                    );
                                    $this->$pay_model->insertData($WalletinsData, 'wallet_transaction');
                                }
                            }
                        }
                    }
                    // print_r($account);exit;
                    $empref_data = '';
                    //check employee , agent benefit and debit the amount if customer preclose
                    if ($accounts['paid_installments'] != $accounts['total_installments']) {
                        //check employee ref data and do debit transaction 
                        if ($accounts['emp_refferal'] == 1 && $accounts['emp_deduct_ins'] > $accounts['paid_installments']) {
                            $empref_data = $this->$model->getEmpBenefit($accounts['id_scheme_account']);
                            if ($empref_data != 0) {
                                $WalletinsData = array(
                                    'id_wallet_account' => $empref_data['id_wallet_account'],
                                    'transaction_type' => 1, //0-Credit,1-Debit
                                    'type' => 0, //CRM
                                    'id_sch_ac' => $id,
                                    'value' => $empref_data['value'],
                                    'description' => 'Chit Referral Debit on preclose',
                                    // 'date_transaction' => date("Y-m-d H:i:s"),
                                    'date_transaction' => $scheme_settings['closed_date'],
                                    'id_employee' => $this->session->userdata('uid'),
                                    'date_add' => date("Y-m-d H:i:s"),
                                    'credit_for' => 'Chit Referral Debit on preclose',
                                    'id_payment' => $empref_data['id_payment']
                                );
                                $this->$pay_model->insertData($WalletinsData, 'wallet_transaction');
                            }
                            //Customer intro scheme detection
                            if ($accounts['is_refferal_by'] == 0 && $accounts['is_refferal_by'] != null && $accounts['referal_code'] != '') {
                                $cus_data = $this->$model->getCustomerByCode($accounts['referal_code']);
                                if ($cus_data > 0) {
                                    $empref_data = $this->$model->getEmpBenefit($cus_data['id_scheme_account']);
                                    if ($empref_data != 0) {
                                        $WalletinsData = array(
                                            'id_wallet_account' => $empref_data['id_wallet_account'],
                                            'transaction_type' => 1, //0-Credit,1-Debit
                                            'type' => 0, //CRM
                                            'id_sch_ac' => $cus_data['id_scheme_account'],
                                            'value' => $empref_data['value'],
                                            'description' => 'Chit Referral Debit on preclose',
                                            //'date_transaction' => date("Y-m-d H:i:s"),
                                            'date_transaction' => $scheme_settings['closed_date'],
                                            'id_employee' => $empref_data['id_employee'],
                                            'date_add' => date("Y-m-d H:i:s"),
                                            'credit_for' => 'Chit Referral Debit on preclose',
                                            'id_payment' => $empref_data['id_payment']
                                        );
                                        $this->$pay_model->insertData($WalletinsData, 'wallet_transaction');
                                    }
                                }
                            }
                        }
                        //agent preclose deduction
                        if ($accounts['agent_refferal'] == 1 && $accounts['agent_deduct_ins'] > $accounts['paid_installments']) {
                            $agent_refdata = $this->$model->getAgentBenefit($accounts['id_scheme_account']);
                            if ($agent_refdata > 0) {
                                $agentDebitData = array(
                                    "ly_trans_type" => 3,
                                    "ly_issue_type" => 0, //0->debit,1->credit
                                    "cus_loyal_cus_id" => $agent_refdata['cus_loyal_cus_id'],
                                    "id_agent" => $agent_refdata['id_agent'],
                                    "id_scheme_account" => $agent_refdata['id_scheme_account'],
                                    "cash_point" => $agent_refdata['cash_pts'],
                                    "status" => 1,
                                    "tr_cus_type" => 4,
                                    "cr_based_on" => 3,
                                    "unsettled_cash_pts" => 0,
                                    //"date_add"       => date('Y-m-d H:i:s'),
                                    "date_add" => $scheme_settings['closed_date'],
                                    "credit_for" => "Incentive debit on Preclose",
                                    "id_payment" => $agent_refdata['id_payment'],
                                );
                                if ($agent_refdata['id_agent'] != '') {
                                    $status = $this->$pay_model->insert_agent_transaction($agentDebitData);
                                    $this->$pay_model->updateDebitCash($agent_refdata['id_agent'], $agent_refdata['cash_pts']);
                                }
                            }
                        }
                    }
                }
                //webcam upload, #AD On:20-12-2022,Cd:CLin,Up:AB ---> starts	    
                $p_ImgData = json_decode(rawurldecode($account['image_closeing']));
                $precious = $p_ImgData[0];
                if (sizeof($p_ImgData) > 0) {
                    $_FILES['closing_img'] = array();
                    $imgFile = $this->base64ToFile($precious->src);
                    $_FILES['closing_img'] = $imgFile;
                }
                if (isset($_FILES['closing_img']['name']) || isset($_FILES['pan_proof']['name']) || isset($_FILES['voterid_proof']['name']) || isset($_FILES['rationcard_proof']['name'])) {
                    $this->set_image($id);
                }
                //webcam upload ends....
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $accounts['cls_details'] = $data;
                    $log_data = array(
                        'id_log' => $this->id_log,
                        'event_date' => date("Y-m-d H:i:s"),
                        'module' => 'Scheme Account',
                        'operation' => 'Close',
                        'record' => $id,
                        'remark' => 'Scheme Account closed successfully'
                    );
                    $this->$log_model->log_detail('insert', '', $log_data);
                    $this->account_close_message($id, $accounts);
                    if (isset($account['saveNprint'])) {
                        redirect('account/close/scheme_history/' . $id);
                    }
                    $this->session->set_flashdata('chit_alert', array('message' => 'Scheme account <' . ($account['scheme_acc_number'] != NULL ? $account['scheme_acc_number'] : $account['account_name']) . '> closed successfully', 'class' => 'warning', 'title' => 'Close Scheme Account'));
                    redirect('account/close');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Close Scheme Account'));
                }
                break;
            case 'Revert':
                $data = array('is_closed' => 0, 'active' => 1);
                $this->db->trans_begin();
                $sch_acc = $this->$model->isDigiAcc($id);
                if($sch_acc){
                    $this->db->trans_rollback();
                    $this->session->set_flashdata("chit_alert", [
                        "message" => "Active Digi scheme account exist for the customer ".$sch_acc["customer_name"],
                        "class" => "danger",
                        "title" => "Revert Closed Account",
                    ]);
                    redirect("account/close");
                }
                $this->$model->update_account($data, $id);
                if ($this->db->trans_status() === TRUE) {
                    //check Incentive Amount
                    $acc_details = $this->$model->get_ClosedBenefitsDetails($id);
                    if ($acc_details['status']) {
                        $wallet_acc = $acc_details['wallet_details'];
                        //echo"<pre>";    print_r($wallet_acc);exit;
                        $WalletinsData = array(
                            'id_wallet_account' => $wallet_acc['id_wallet_account'],
                            'transaction_type' => 1, //0-Credit,1-Debit
                            'type' => 0, //CRM
                            'id_sch_ac' => $id,
                            'value' => $wallet_acc['value'],
                            'description' => 'CHIT Incentive Debit Amount(Accounted Reverted)',
                            'date_transaction' => date("Y-m-d H:i:s"),
                            'id_employee' => $this->session->userdata('uid'),
                            'date_add' => date("Y-m-d H:i:s"),
                        );
                        //echo "<pre>";print_r($WalletinsData);exit;
                        $this->$pay_model->insertData($WalletinsData, 'wallet_transaction');
                    }
                    //check Incentive Amount
                    $this->db->trans_commit();
                    $log_data = array(
                        'id_log' => $this->id_log,
                        'event_date' => date("Y-m-d H:i:s"),
                        'module' => 'Scheme Account',
                        'operation' => 'Revert',
                        'record' => $id,
                        'remark' => 'Scheme Account close reverted successfully'
                    );
                    $this->$log_model->log_detail('insert', '', $log_data);
                    $data['msg'] = $this->$model->get_close_account($id);
                    $this->account_revert_message($data);
                    $this->session->set_flashdata('chit_alert', array('message' => 'Closed Scheme account (ID = ' . $id . ' ) reverted successfully', 'class' => 'success', 'title' => 'Revert Closed Account'));
                    redirect('account/new');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Revert Closed Account'));
                    redirect('account/close');
                }
                break;
        }
    }
    //for closing request
    public function closing_request()
    {
        $model = self::ACC_MODEL;
        $data['accounts'] = $this->$model->get_closing_request();
        $data['main_content'] = self::MAIL_VIEW . 'closing_request';
        $this->load->view('layout/template', $data);
    }
    public function registration_list()
    {
        $model = self::ACC_MODEL;
        $data['registrations'] = $this->$model->get_registration_details();
        $data['main_content'] = self::REG_VIEW . 'list';
        $this->load->view('layout/template', $data);
    }
    public function send_login()
    {
        $model = self::ACC_MODEL;
        $data['accounts'] = $this->$model->get_all_account();
        $data['main_content'] = self::ACC_VIEW . 'send_login';
        $this->load->view('layout/template', $data);
    }
    public function send_login_detail()
    {
        $model = self::ACC_MODEL;
        $accounts = $this->input->post('account_id');
        $total = count($accounts);
        if ($total > 0) {
            $this->load->model($model);
            foreach ($accounts as $account) {
                $accounts = $this->$model->get_account_open($account);
                $logins[] = array(
                    'id_scheme_account' => $accounts['id_scheme_account'],
                    'name' => $accounts['customer'],
                    'mobile' => $accounts['mobile'],
                    'passwd' => $accounts['passwd']
                );
            }
            if (count($logins) > 0) {
                foreach ($logins as $login) {
                    $sms_status = $this->login_sms($login);
                }
            }
            if ($sms_status) {
                $this->session->set_flashdata('chit_alert', array('message' => 'Login details has been sent successfully to ' . count($logins) . (count($logins) > 1 ? ' customers' : ' customer'), 'class' => 'success', 'title' => 'Send Login'));
            }
            redirect('account/send/login');
        }
        $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Send Login'));
        redirect('account/send/logint');
    }
    function send_sms($mobile, $message, $dtl_te_id)
    {
        $model = self::ADM_MODEL;
        if ($this->config->item('sms_gateway') == '1') {
            $this->sms_model->sendSMS_MSG91($mobile, $message, '', $dtl_te_id);
        } elseif ($this->config->item('sms_gateway') == '2') {
            $this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
        } elseif ($this->config->item('sms_gateway') == '3') {
            $this->sms_model->sendSMS_SpearUC($mobile, $message, '', $dtl_te_id);
        } elseif ($this->config->item('sms_gateway') == '4') {
            $sendSMS = $this->sms_model->sendSMS_Asterixt($mobile, $message, '', $dtl_te_id);
        } elseif ($this->config->item('sms_gateway') == '5') {
            $sendSMS = $this->sms_model->sendSMS_Qikberry($mobile, $message, '', $dtl_te_id);
        }
    }
    function login_sms($data)
    {
        $mobile_number = $data['mobile'];
        $password = $data['passwd'];
        $cus_name = $data['name'];
        $user_name = $data['mobile'];
        $message = "Dear " . ucfirst($cus_name) . " , Your Savings Scheme login details, user name : " . $user_name . " and password : " . $password;
        $this->send_sms($mobile_number, $message, '');
    }
    //get scheme account id by payment id
    function getSchemeAccID($id_payment)
    {
        $model = self::ACC_MODEL;
        $account = $this->$model->getSchemeAccountByPayment($id_payment);
        if ($account) {
            return $account['id_scheme_account'];
        } else {
            return null;
        }
    }
    /* //updating the data from intermediate table
    function update_client()
    {
        $this->load->database('common_db',true);
        $model = self::API_MODEL;  
        $acc_model = self::ACC_MODEL;
        $pay_model = self::PAY_MODEL;
        $client_data = $this->$model->getNewCustomerByTranStatus('N'); 
        if($client_data)
        {
          $acc_rec = 0;
          $trans_rec = 0;
          $records = count($client_data);
          $acc_id="";
          $pay_id="";
          foreach($client_data as $client)
          {
              $this->load->database('default',true);			
              $id_payment = $client['ref_no']; //payment id	
              $id_scheme_account         = $this->getSchemeAccID($id_payment); //scheme_account
              $isClientID =  $this->$acc_model->clientid_exists($id_scheme_account);
              $inter_id = $client['id_new_customer'];
              $trans_data = array('receipt_jil' =>  $client['receipt_jil'], 
                                  'date_upd'	=> date("Y-m-d H:i:s"));
                    $acc_data = array(
                                'ref_no'                  => $client['clientid'], 
                                'group_code'              => $client['group_code'],
                                'msno'                    => $client['msno'],
                                'scheme_acc_number'       => $client['msno'],
                                'date_upd'	 => date("Y-m-d H:i:s")								
                            );					  
            //update payment records in main db 			  
            $trans_status = $this->$pay_model->update_payment_status($id_payment,$trans_data);	
            //update client details in main db	if not exists
            if(!$isClientID['status'])
            {
                $acc_status = $this->$acc_model->update_account($acc_data,$id_scheme_account);	
                if($acc_status)
                {
                    $acc_rec +=1;				
                    $acc_id .=$id_scheme_account.'|';
                }	
            }	
            if($trans_status)
            {
                //update new customer in intermediate db
                $this->load->database('common_db',true);
                $inter_data = array('transfer' => 'Y', 'transfer_date' => date('Y-m-d') );
                $this->$model->update_newCustomer($inter_data,$inter_id);
                $trans_rec += 1;
                $pay_id .=$id_payment.'|';
            }		
          }
          //$this->session->set_flashdata('chit_alert', array('message' => $records.' records updated','class' => 'success','title'=>'Update Client Details'));
          $remark = array("acc" => $acc_id,	"pay" => $pay_id);
          $sync_data = array(
                                "total_records"   => $records,
                                "scheme_accounts" => $acc_rec,
                                "payments"		  => $trans_rec,	
                                "sync_date"		  => date('Y-m-d H:i:s'),	
                                "remark"          => json_encode($remark)
                            );  
          $this->load->database('default',true);				
          $this->$acc_model->insert_sync($sync_data);
          $this->session->set_flashdata('chit_alert', array('message' => 'Total '.$records.' records affected '.$acc_rec.' scheme accounts and '.$trans_rec.' payments records. ','class' => 'success','title'=>'Update Client Details'));
        }
        else
        {
            $this->session->set_flashdata('chit_alert', array('message' => 'No updates to proceed','class' => 'danger','title'=>'Update Client Details'));
        }			
        $this->load->database('default',true);	
        redirect('account/new');		
    } */
    // offline data trans details
    function update_client_jil()
    {
        $model = self::CHITAPI_MODEL;
        $acc_model = self::ACC_MODEL;
        $pay_model = self::PAY_MODEL;
        $client_data = $this->$model->getNewCustomerByTranStatus('N');
        if ($client_data) {
            $acc_rec = 0;
            $trans_rec = 0;
            $records = count($client_data);
            $acc_id = "";
            $pay_id = "";
            foreach ($client_data as $client) {
                //print_r($client);
                $id_payment = $client['ref_no']; //payment id	
                $id_scheme_account = $this->getSchemeAccID($id_payment); //scheme_account
                if ($id_scheme_account != null) {
                    $isClientID = $this->$acc_model->clientid_exists($id_scheme_account);
                    $inter_id = $client['id_new_customer'];
                    $trans_data = array(
                        'receipt_no' => $client['receipt_jil'],
                        'date_upd' => date("Y-m-d H:i:s")
                    );
                    $acc_data = array(
                        'ref_no' => $client['clientid'],
                        'group_code' => $client['group_code'],
                        'msno' => $client['msno'],
                        'scheme_acc_number' => $client['msno'],
                        'date_upd' => date("Y-m-d H:i:s")
                    );
                    //update payment records in main db 			  
                    $trans_status = $this->$pay_model->update_payment_status($id_payment, $trans_data);
                    //update client details in main db	if not exists
                    if (!$isClientID['status']) {
                        $acc_status = $this->$acc_model->update_account($acc_data, $id_scheme_account);
                        if ($acc_status) {
                            $acc_rec += 1;
                            $acc_id .= $id_scheme_account . '|';
                        }
                    }
                    if ($trans_status) {
                        //update new customer in intermediate db
                        $inter_data = array('transfer' => 'Y', 'transfer_date' => date('Y-m-d'));
                        $this->$model->update_newCustomer($inter_data, $inter_id);
                        $trans_rec += 1;
                        $pay_id .= $id_payment . '|';
                    }
                }
            }
            //$this->session->set_flashdata('chit_alert', array('message' => $records.' records updated','class' => 'success','title'=>'Update Client Details'));
            $remark = array("acc" => $acc_id, "pay" => $pay_id);
            $sync_data = array(
                "total_records" => $records,
                "scheme_accounts" => $acc_rec,
                "payments" => $trans_rec,
                "sync_date" => date('Y-m-d H:i:s'),
                "remark" => json_encode($remark)
            );
            $this->$acc_model->insert_sync($sync_data);
            $this->session->set_flashdata('chit_alert', array('message' => 'Total ' . $records . ' records affected ' . $acc_rec . ' scheme accounts and ' . $trans_rec . ' payments records. ', 'class' => 'success', 'title' => 'Update Client Details'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => $this->db->last_query(), 'class' => 'danger', 'title' => 'Update Client Details'));
        }
        $this->load->database('default', true);
        redirect('account/new');
    }
    // For SKTM Group (SCM,TKTM only)
    function syncInterData()
    {
        $this->load->model("sktm_syncapi_model");
        $api_model = "sktm_syncapi_model";
        $acc_model = "account_model";
        $record_to = 2; // 2 - Online 
        $branch_id = null;
        $acc_id = "";
        $acc_rec = 0;
        $trans_rec = 0;
        $records = 0;
        $pay_id = "";
        $rejected_pay_id = "";
        $rejected_acc_id = "";
        $cus_reg_data = $this->$api_model->getcustomerByStatus('N', $branch_id, $record_to);
        if ($cus_reg_data) {
            $records += count($cus_reg_data);
            foreach ($cus_reg_data as $client) {
                // is_registered_online -> 0 - No, 1- Yes , 2 - online record
                if ($client['is_modified'] == 1 && $client['is_registered_online'] >= 1) {
                    $this->db->trans_begin();
                    // $isClientID =  $this->$api_model->checkClientID($client['id_scheme_account'],"");
                    $acc_data = array(
                        'closed_by' => $client['closed_by'],
                        'closing_date' => $client['closing_date'],
                        'closing_balance' => $client['closing_amount'],
                        'closing_weight' => $client['closing_weight'],
                        'closing_add_chgs' => $client['closing_add_chgs'],
                        //	'additional_benefits'=> $client['additional_benefits'],
                        'remark_close' => $client['remark_close'],
                        'is_closed' => $client['is_closed'],
                        'active' => ($client['is_closed'] == 1 ? 0 : 1),
                        'scheme_acc_number' => $client['scheme_ac_no'],
                        'ref_no' => $client['clientid'],
                        'date_upd' => date("Y-m-d H:i:s")
                    );
                    if ($client['id_scheme_account'] != null) {
                        $acc_status = $this->$api_model->update_account($acc_data, $client['id_scheme_account'], $client['id_customer_reg']);
                        $id = "ID" . '' . $client['id_scheme_account']; // Online record
                    } else {
                        $acc_status = $this->$api_model->update_accountByClientId($acc_data, $client['clientid'], $client['id_customer_reg']);
                        $id = "CID" . '' . $client['clientid']; // Offline record
                    }
                    if ($this->db->trans_status() == TRUE) {
                        $this->db->trans_commit();
                        $acc_rec += 1;
                        $acc_id .= $id . '|';
                    } else {
                        $this->db->trans_rollback();
                        $rejected_acc_id .= $id . '|';
                    }
                }
            }
        }
        //  echo $this->db->last_query();exit;
        $trans_data = $this->$api_model->getTransactionByStatus('N', $branch_id, $record_to);
        if ($trans_data) {
            $records += count($trans_data);
            foreach ($trans_data as $trans) {
                // payment_type -> 1- Online , 2 - Offine
                if ($trans['payment_type'] == 1) {
                    // to update online record
                    // check whether scheme a/c data updated
                    $isClientID = $this->$api_model->checkClientID($trans['id_scheme_account'], "");
                    if ($isClientID['status'] && $trans['is_modified'] == 1 && $trans['payment_status'] == 1) {
                        $this->db->trans_begin();
                        $trans_data = array(
                            'receipt_no' => $trans['receipt_no'],
                            'payment_ref_number' => $trans['ref_no'],
                            "payment_status" => 1,
                            'date_upd' => date("Y-m-d H:i:s")
                        );
                        $updPayment = $this->$api_model->updatePayment($trans_data, $trans['payment_type'], $trans['id_scheme_account'], $trans['payment_date']);
                        $trans_rec += 1;
                        if ($this->db->trans_status() == TRUE) {
                            $this->db->trans_commit();
                            $trans_rec += 1;
                            $pay_id .= $trans['ref_no'] . '|';
                        } else {
                            $this->db->trans_rollback();
                            $rejected_pay_id .= $trans['ref_no'] . '|';
                        }
                    }
                } else if ($trans['payment_type'] == 2 && ($trans['client_id'] != null || $trans['client_id'] != '')) {
                    // to update offline record
                    $isClientID = $this->$api_model->checkClientID("", $trans['client_id']);
                    if ($isClientID['status']) {
                        $this->db->trans_begin();
                        if ($trans['payment_status'] == 1) {
                            $id_branch = $this->$api_model->get_branchid($trans['branch_code']);
                            $pay_array = array(
                                "id_scheme_account" => $isClientID['id_scheme_account'],
                                "date_payment" => $trans['payment_date'],
                                //	"id_metal" 			=> $trans['metal'],
                                "metal_rate" => $trans['rate'],
                                "payment_amount" => $trans['amount'],
                                "metal_weight" => $trans['weight'],
                                "payment_mode" => $trans['payment_mode'],
                                "payment_status" => 1,
                                "payment_type" => "Offline",
                                "due_type" => $trans['due_type'],
                                "installment" => $trans['installment_no'],
                                "receipt_no" => $trans['receipt_no'],
                                "remark" => $trans['remarks'],
                                "discountAmt" => $trans['discountAmt'],
                                "payment_ref_number" => $trans['ref_no'],
                                "date_upd" => date('Y-m-d H:i:s'),
                                "id_branch" => $id_branch
                            );
                            $insPayment = $this->$api_model->insertPayment($pay_array);
                        } else {
                            //update if offline record is with cancelled status
                            $upd_array = array(
                                "payment_status" => 2,
                                "receipt_no" => $trans['receipt_no'],
                                "remark" => $trans['remarks'],
                                "date_upd" => date('Y-m-d H:i:s'),
                                "payment_ref_number" => $trans['ref_no']
                            );
                            $updPayment = $this->$api_model->updatePayment($upd_array, $trans['payment_type'], $isClientID['id_scheme_account'], $trans['payment_date']);
                        }
                        if ($this->db->trans_status() == TRUE) {
                            $this->db->trans_commit();
                            $trans_rec += 1;
                            $pay_id .= $trans['ref_no'] . '|';
                        } else {
                            "Rollback!!";
                            $this->db->trans_rollback();
                            $this->db->_error_message();
                            exit;
                            $rejected_pay_id .= $trans['ref_no'] . '|';
                        }
                    }
                }
            }
        }
        if ($acc_id > 0 || $pay_id > 0 || $rejected_acc_id != "" || $rejected_pay_id != "") {
            $remark = array("acc" => $acc_id, "pay" => $pay_id, "ac_error" => $rejected_acc_id, "pay_error" => $rejected_pay_id);
            $sync_data = array(
                "total_records" => $records,
                "scheme_accounts" => $acc_rec,
                "payments" => $trans_rec,
                "sync_date" => date('Y-m-d H:i:s'),
                "remark" => json_encode($remark)
            );
            $this->$acc_model->insert_sync($sync_data);
            $result = array('message' => 'Total ' . $records . ' records affected ' . $acc_rec . ' scheme accounts and ' . $trans_rec . ' payments records. Error Records : Account = ' . $rejected_acc_id . ' Payment =' . $rejected_pay_id, 'class' => 'success', 'title' => 'Update Client Details');
        } else {
            $result = array('message' => 'No records to proceed ', 'class' => 'info', 'title' => 'Update Client Details');
        }
        $this->session->set_flashdata('chit_alert', $result);
        $this->load->database('default', true);
        redirect('account/new');
    }
    // to update scheme ac and payments
    function update_client()
    {
        $api_model = self::API_MODEL;
        $acc_model = self::ACC_MODEL;
        $sms_model = self::SMS_MODEL;
        $set_model = self::SET_MODEL;
        $service = $this->$set_model->get_service(31);
        $record_to = 2; // 2 - Online 
        $branch_id = (isset($_POST['sync_branch_id']) ? $_POST['sync_branch_id'] : NULL);
        $trans_date = (isset($_POST['sync_trans_date']) ? $_POST['sync_trans_date'] : date('Y-m-d'));
        $acc_id = "";
        $acc_rec = 0;
        $trans_rec = 0;
        $records = 0;
        $pay_id = "";
        // echo $this->session->userdata('id_branch');exit;
        $cus_reg_data = $this->$api_model->getcustomerByStatus('N', $branch_id, $record_to, $trans_date);
        if ($cus_reg_data) {
            $records += count($cus_reg_data);
            foreach ($cus_reg_data as $client) {
                // is_registered_online -> 0 - No, 1- Yes , 2 - online record
                if ($client['is_modified'] == 1 && $client['is_registered_online'] >= 1) {
                    if ($client['clientid'] != null) {
                        $isClientID = $this->$api_model->checkClientID("", $client['clientid']);
                        if ($isClientID['status']) {
                            $acc_data = array(
                                'closed_by' => $client['closed_by'],
                                'closing_date' => $client['closing_date'],
                                'closing_amount' => $client['closing_amount'],
                                'closing_weight' => $client['closing_weight'],
                                'closing_add_chgs' => $client['closing_add_chgs'],
                                'additional_benefits' => $client['additional_benefits'],
                                'remark_close' => $client['remark_close'],
                                'is_closed' => $client['is_closed'],
                                'active' => ($client['is_closed'] == 1 ? 0 : 1),
                                'date_upd' => date("Y-m-d H:i:s")
                            );
                            $acc_status = $this->$api_model->update_closed_ac($acc_data, $client['clientid'], $client['id_customer_reg']);
                        } else {
                            $acc_data = array(
                                'scheme_acc_number' => $client['scheme_ac_no'],
                                'ref_no' => $client['clientid'],
                                'date_upd' => date("Y-m-d H:i:s")
                            );
                            $acc_status = $this->$api_model->update_account($acc_data, $client['id_scheme_account'], $client['id_customer_reg']);
                            $acc_datas = $this->$acc_model->getaccount_data($client['id_scheme_account']);   //Based on SMS settings to a/c num generated sms //HH.
                            if ($service['serv_sms'] == 1 && $acc_datas['scheme_acc_number'] != '' && $acc_datas['ref_no'] != NULL) {
                                $msgdata = $this->$sms_model->get_SMS_data(31, '', $client['id_scheme_account']);
                                $this->send_sms($acc_datas['mobile'], $msgdata['message'], $service['dlt_te_id']);
                                //print_r($msgdata['message']);exit;
                            }
                        }
                    }
                    if ($acc_status) {
                        $acc_rec += 1;
                        $acc_id .= $client['id_scheme_account'] . '|';
                        $inter_data = array('is_transferred' => 'Y', 'is_modified' => 'N', 'transfer_date' => date('Y-m-d'), 'ref_no' => $client['ref_no']);
                        $this->$api_model->updateData($inter_data, $branch, 'customer_reg');
                    }
                }
            }
        }
        $trans_data = $this->$api_model->getRegisteredAccTransactions('N', $branch_id, $record_to, $trans_date);
        // echo "<pre>";print_r($trans_data);exit;
        if ($trans_data) {
            $records += count($trans_data);
            foreach ($trans_data as $trans) {
                // payment_type -> 1- Online , 2 - Offine
                if ($trans['payment_type'] == 1) {
                    // to update online record
                    // check whether scheme a/c data updated
                    $isClientID = $this->$acc_model->checkClientID($trans['id_scheme_account'], "");
                    if ($isClientID['status'] && $trans['is_modified'] == 1 && $trans['payment_status'] == 1) {
                        $trans_data = array(
                            'receipt_no' => $trans['receipt_no'],
                            'payment_ref_number' => $trans['ref_no'],
                            "payment_status" => 1,
                            'date_upd' => date("Y-m-d H:i:s")
                        );
                        $updPayment = $this->$api_model->updatePayment($trans_data, $trans['payment_type'], $trans['id_scheme_account'], $trans['payment_date']);
                        $trans_rec += 1;
                        $pay_id .= $trans['ref_no'] . '|';
                    }
                } else if ($trans['payment_type'] == 2 && ($trans['client_id'] != null || $trans['client_id'] != '')) {
                    // to update offline record
                    $isClientID = $this->$api_model->checkClientID("", $trans['client_id']);
                    if ($isClientID['status']) {
                        if ($trans['payment_status'] == 1) {
                            $pay_array = array(
                                "id_scheme_account" => $isClientID['id_scheme_account'],
                                "id_branch" => $branch_id,
                                "date_payment" => $trans['payment_date'],
                                "date_add" => $trans['payment_date'],
                                //            			   						"id_metal" 			=> $trans['id_metal'],
                                "metal_rate" => $trans['rate'],
                                "payment_amount" => $trans['amount'],
                                "actual_trans_amt" => $trans['amount'],
                                "metal_weight" => $trans['weight'],
                                "payment_mode" => $trans['payment_mode'],
                                "payment_status" => 1,
                                "payment_type" => "Offline",
                                "due_type" => $trans['due_type'],
                                "installment" => $trans['installment_no'],
                                "receipt_no" => $trans['receipt_no'],
                                "remark" => $trans['remarks'],
                                "discountAmt" => $trans['discountAmt'],
                                "payment_ref_number" => $trans['ref_no'],
                                "date_upd" => date('Y-m-d H:i:s')
                            );
                            $insPayment = $this->$api_model->insertPayment($pay_array);
                            if ($insPayment) {
                                $trans_rec += 1;
                                $pay_id .= $trans['ref_no'] . '|';
                            }
                        } else {
                            //update if offline record is with cancelled status
                            $upd_array = array(
                                "payment_status" => 2,
                                "receipt_no" => $trans['receipt_no'],
                                "remark" => $trans['remarks'],
                                "date_upd" => date('Y-m-d H:i:s'),
                                "payment_ref_number" => $trans['ref_no']
                            );
                            $updPayment = $this->$api_model->updatePayment($upd_array, $trans['payment_type'], $isClientID['id_scheme_account'], $trans['payment_date']);
                            if ($updPayment) {
                                $trans_rec += 1;
                                $pay_id .= $trans['ref_no'] . '|';
                            }
                        }
                    }
                }
            }
        }
        //$this->session->set_flashdata('chit_alert', array('message' => $records.' records updated','class' => 'success','title'=>'Update Client Details'));
        if ($acc_id != '' || $pay_id != '') {
            $remark = array("acc" => $acc_id, "pay" => $pay_id);
            $sync_data = array(
                "total_records" => $records,
                "scheme_accounts" => $acc_rec,
                "payments" => $trans_rec,
                "sync_date" => date('Y-m-d H:i:s'),
                "remark" => json_encode($remark)
            );
            $this->$acc_model->insert_sync($sync_data);
            $this->session->set_flashdata('chit_alert', array('message' => 'Total ' . $records . ' records .Updated ' . $acc_rec . ' scheme accounts and ' . $trans_rec . ' payments records. ', 'class' => 'success', 'title' => 'Update Client Details'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'No updates to proceed', 'class' => 'danger', 'title' => 'Update Client Details'));
        }
        $this->load->database('default', true);
        redirect('account/new');
    }
    // offline data trans details
    function close_account_history_form($id_scheme_account)
    {
        $acc_model = self::ACC_MODEL;
        $pay_model = self::PAY_MODEL;
        $this->load->model($acc_model);
        $this->load->model($pay_model);
        $account['customer'] = $this->$acc_model->get_account_detail($id_scheme_account);
        if ($account['customer']['one_time_premium'] == 1 && $account['customer']['fixed_metal_rate'] != NULL) {
            $discount_amount = $this->$acc_model->getDiscountByjoin($id_scheme_account, $account['customer']['id_scheme'], $account['customer']['sch_join_date']);
            if ($discount_amount != 0) {
                if ($discount_amount['interest_type'] == 0) {
                    $acc_interest = round($account['customer']['fixed_metal_rate'] * ($discount_amount['interest_value'] / 100));
                    $discount_gold_rate = $account['customer']['fixed_metal_rate'] - $acc_interest;
                    $discount_weight = round($account['customer']['firstPayment_amt'] / $discount_gold_rate, 3);
                } else if ($discount_amount['interest_type'] == 1) {
                    $acc_interest = $discount_amount['interest_value'];
                    $discount_gold_rate = $account['customer']['fixed_metal_rate'] - $acc_interest;
                    $discount_weight = round($account['customer']['firstPayment_amt'] / $discount_gold_rate, 3);
                }
            }
        } else {
            $acc_interest = 0;
            $discount_gold_rate = 0;
            $discount_weight = 0;
        }
        $account['payment'] = $this->$pay_model->get_account_payment($id_scheme_account);
        $data['account'] = $account;
        $data['discount_gold_rate'] = $discount_gold_rate;
        $data['discount_weight'] = $discount_weight;
        //print_r($data['discount_weight']);exit;
        $data['main_content'] = self::REP_VIEW . 'payment_history';
        $this->load->view('layout/template', $data);
    }
    //coded by vishnu	
    function invoice_history_form($id_scheme_account)
    {
        $set = self::SET_MODEL;
        $acc_model = self::ACC_MODEL;
        $pay_model = self::PAY_MODEL;
        $data['receipt'] = $this->$set->receipt_type();
        $data['comp_details'] = $this->$set->get_company();
        $account['customer'] = $this->$acc_model->get_account_detail($id_scheme_account);
        $account['payment'] = $this->$pay_model->get_account_payment($id_scheme_account);
        $data['account'] = $account;
        //echo "<pre>";print_r($data);exit;
        $this->load->helper(array('dompdf', 'file'));
        $dompdf = new DOMPDF();
        $html = $this->load->view('include/history_payment', $data, true);
        $dompdf->load_html($html);
        $dompdf->set_paper("a4", "portriat");
        $dompdf->render();
        while(ob_get_level()) ob_end_clean();
        $dompdf->stream("receipt1.pdf", array('Attachment' => 0));
        $data['main_content'] = self::REP_VIEW . 'history_payment';
    }
    function invoice_his_custom($id_scheme_account)
    {
        $accmodel = self::ACC_MODEL;
        $set = self::SET_MODEL;
        $data['receipt'] = $this->$set->receipt_type();
        $account['acc'] = $this->$accmodel->get_closed_account_by_id($id_scheme_account);
        if ($this->branch_settings == 1) {
            $data['comp_details'] = $this->$set->get_branchcompany($account['acc']['id_branch']);
        } else {
            $data['comp_details'] = $this->$set->get_company();
        }
        $data['account'] = $account;
        $this->load->helper(array('dompdf', 'file'));
        $dompdf = new DOMPDF();
        $html = $this->load->view('include/receipt_cls_custom', $data, TRUE);
        $dompdf->load_html($html);
        $customPaper = array(0, 0, 210, 400);
        $dompdf->set_paper($customPaper, "portriat");
        $dompdf->render();
        while(ob_get_level()) ob_end_clean();
        $dompdf->stream("receipt1.pdf", array('Attachment' => 0));
    }
    //activate deactive account	
    function account_status($status, $id)
    {
        $data = array('active' => $status);
        $model = self::ACC_MODEL;
        $status = $this->$model->update_account($data, $id);
        if ($status) {
            $this->session->set_flashdata('chit_alert', array('message' => 'Account status updated as ' . ($status ? 'active' : 'inactive') . ' successfully.', 'class' => 'success', 'title' => 'Account Status'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Account Status'));
        }
        redirect('account/new');
    }
    //scheme joining sms and email
    function account_join_message($id, $data)
    {
        $ser_model = self::SET_MODEL;
        $mail_model = self::MAIL_MODEL;
        $sms_model = self::SMS_MODEL;
        $serviceID = 2;
        $service = $this->$ser_model->get_service($serviceID);
        $company = $this->$ser_model->get_company();
        $email = $data['email'];
        if ($service['serv_email'] == 1 && $email != '') {
            $data['schData'] = $data;
            $data['company'] = $company;
            $data['type'] = 1;
            $to = $email;
            $subject = "Reg- " . $company['company_name'] . " saving scheme account joining";
            $message = $this->load->view('include/emailscheme', $data, true);
            $sendEmail = $this->$mail_model->send_email($to, $subject, $message);
        }
        $data = $this->$sms_model->get_SMS_data($serviceID, $id);
        $mobile_number = $data['mobile'];
        $message = $data['message'];
        if ($service['serv_sms'] == 1) {
            $this->send_sms($mobile_number, $message, $service['dlt_te_id']);
        }
        if ($service['serv_whatsapp'] == 1) {
            $smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $params];
            $this->sms_model->send_whatsApp_message($mobile_number, $smsData);
        }
    }
    //scheme closing sms and email			
    function account_close_message($id, $acc)
    {
        $ser_model = self::SET_MODEL;
        $mail_model = self::MAIL_MODEL;
        $sms_model = self::SMS_MODEL;
        $serviceID = 4;
        $service = $this->$ser_model->get_service($serviceID);
        $company = $this->$ser_model->get_company();
        $email = $acc['email'];
        //print_r($acc);exit;
        if ($service['serv_email'] == 1 && $email != '') {
            $data['schData'] = $acc;
            $data['company'] = $company;
            $data['type'] = 2;
            $to = $email;
            $subject = "Reg- " . $company['company_name'] . " saving scheme account closing";
            $message = $this->load->view('include/emailscheme', $data, true);
            $sendEmail = $this->$mail_model->send_email($to, $subject, $message);
        }
        $data = $this->$sms_model->get_SMS_data($serviceID, $id);
        $mobile_number = $data['mobile'];
        $message = $data['message'];
        if ($service['serv_sms'] == 1) {
            $this->send_sms($mobile_number, $message, $service['dlt_te_id']);
        }
        if ($service['serv_whatsapp'] == 1) {
            $smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $params];
            $this->sms_model->send_whatsApp_message($mobile_number, $smsData);
        }
    }
    //scheme revert sms and email			
    function account_revert_message($data)
    {
        $ser_model = self::SET_MODEL;
        $mail_model = self::MAIL_MODEL;
        $serviceID = 13;
        $service = $this->$ser_model->get_service(4);
        $company = $this->$ser_model->get_company();
        $email = $data['msg']['email'];
        $sms_model = self::SMS_MODEL;
        if ($service['serv_email'] == 1 && $email != '') {
            $data['schData'] = $data['msg'];
            $data['company'] = $company;
            $data['type'] = 3;
            $to = $email;
            $subject = "Reg- " . $company['company_name'] . " saving scheme account reverting";
            $message = $this->load->view('include/emailscheme', $data, true);
            $sendEmail = $this->$mail_model->send_email($to, $subject, $message);
        }
        $id = $data['msg']['id_scheme_account'];
        $data = $this->$sms_model->get_SMS_data($serviceID, $id);
        $mobile_number = $data['mobile'];
        $message = $data['message'];
        if ($service['serv_sms'] == 1) {
            $this->send_sms($mobile_number, $message, $service['dlt_te_id']);
        }
        if ($service['serv_whatsapp'] == 1) {
            $smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $params];
            $this->sms_model->send_whatsApp_message($mobile_number, $smsData);
        }
    }
    public function checkreferalcode()
    {
        $referal_code = $this->input->post('referal_code');
        if ($referal_code != '') {
            $available = $this->account_model->check_referrals($referal_code);
        } else {
            $available = FALSE;
        }
        if ($available) {
            echo TRUE;
        } else {
            echo 0;
        }
    }
    //this function for common branch & closing branch name//HH
    //   branch_name
    public function get_branch_name()
    {
        $model_name = self::ACC_MODEL;
        $set_model = self::SET_MODEL;
        $data['profile'] = $this->session->userdata('profile');
        $data['branch'] = $this->$model_name->branchname_list();
        echo json_encode($data);
    }
    //   branch_name
    // scheme account number generate//
    function manual_schemeaccount()
    {
        $model = self::ACC_MODEL;
        $schaccount = $this->input->post('selected');
        $upd_rec = 0;
        if (!empty($schaccount) && count($schaccount) > 0 && $schaccount != NULL) {
            $this->db->trans_begin();
            foreach ($schaccount as $data) {
                $sch_account = array('scheme_acc_number' => $data['scheme_acc_number']);
                $update = $this->$model->update_schemeaccno($data['id_scheme_account'], $sch_account);
                if ($update) {
                    $this->db->trans_commit();
                    $upd_rec++;
                } else {
                    $this->db->trans_rollback();
                }
            }
            echo $upd_rec;
            if ($upd_rec > 0) {
                $this->session->set_flashdata('chit_alert', array('message' => $upd_rec . ' Scheme account number record updated as successfully...', 'class' => 'success', 'title' => 'Scheme account generated'));
            }
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation...', 'class' => 'danger', 'title' => 'Scheme account number generate'));
        }
    }
    // scheme account number generate//
    // referrals code chk validate //
    /*  public function referralcode_check()
     {
       $code=$this->input->post('referal_code');
       if($code!=''){
           $status = $this->account_model->checkreferral_code($code);
           if($status){
           echo 1;
          }else{
         echo 0;
          } 
       }else{
         echo 0;
       } 
     }  */
    /*  public function referralcode_check()
    {
            $referal_code = $this->input->post('referal_code');	
            $ref_codes = explode('-',$referal_code);
     if(!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $referal_code))
        {
            $available = $this->account_model->check_cusrefcode($referal_code);			
        }
        else if(strtoupper($ref_codes[0]) == 'EMP'){	
            $available = $this->account_model->check_emprefcode($ref_codes[1]);	
        }
        else{
            $available = FALSE;
        }
        if($available)
        {	
            echo TRUE;	
        }
        else
        {
            echo 0;
        }
    } */
    /*public function referralcode_check()
    {
         $referal_code = $this->input->post('referal_code');
         $id_customer = $this->input->post('id_customer');
         if(!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $referal_code))
            {
                $available = $this->account_model->check_refcode($referal_code,$id_customer);			
            }
            else{
                $available = FALSE;
            }
            if($available)
            {	
                echo TRUE;	
            }
            else
            {
                echo 0;
            }
    } */
    /* public function referralcode_check()
    {
        $referal_code = $this->input->post('referal_code');
        $id_customer = $this->input->post('id_customer');
        if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $referal_code)) {
            $data = $this->account_model->check_refcode($referal_code, $id_customer);
            echo json_encode($data);
        } else {
            echo 0;
        }
    } */
    public function referralcode_check()
    {
        $referal_code = $this->input->post('referal_code');
        $ref_by = $this->input->post('ref_by');
        if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $referal_code)) {
            $data = $this->account_model->check_refcode($referal_code, $ref_by);
            echo json_encode($data);
        } else {
            echo 0;
        }
    }
    // referrals code chk validate // 
    //schemegroup_list
    function scheme_group()
    {
        $SETT_MOD = "admin_settings_model";
        $data['main_content'] = self::GROUP_VIEW . 'scheme_group';
        $data['access'] = $this->$SETT_MOD->get_access('admin_manage/scheme_group/list');
        $this->load->view('layout/template', $data);
    }
    function ajax_scheme_group_list($id = "")
    {
        $model = self::ACC_MODEL;
        $setmodel = self::SET_MODEL;
        if (!empty($_POST)) {
            $range['from_date'] = $this->input->post('from_date');
            $range['to_date'] = $this->input->post('to_date');
            $range['id_branch'] = $this->input->post('id_branch');
            if ($range['id_branch'] != "") {
                $data['requests'] = $this->$model->get_schemegroup($range['id_branch']);
            } else {
                $data['requests'] = $this->$model->get_schemegroup();
            }
        } else {
            $data['requests'] = $this->$model->get_schemegroup();
        }
        //	$data['branches']=$this->$setmodel->get_branches();
        $data['access'] = $this->$setmodel->get_access('account/scheme_group/list');
        echo json_encode($data);
    }
    //schemegroup_list
    // KVP - Existing scheme reg approval functions
    function schemereg_list()
    {
        $data['main_content'] = self::EXV_VIEW . 'scheme_reg';
        $this->load->view('layout/template', $data);
    }
    function ajax_requests_list()
    {
        $model = self::ACC_MODEL;
        $setmodel = self::SET_MODEL;
        if (!empty($_POST)) {
            $range['from_date'] = $this->input->post('from_date');
            $range['to_date'] = $this->input->post('to_date');
            $range['id_branch'] = $this->input->post('id_branch');
            $range['status'] = $this->input->post('status');
            if ($range['id_branch'] != "") {
                $data['requests'] = $this->$model->get_requests_byBranch($range['id_branch'], $range['status']);
                //var_dump($data['requests']);exit;
            } else if ($range['from_date'] != "") {
                $data['requests'] = $this->$model->get_requests_range($range['from_date'], $range['to_date'], $range['status']);
            } else {
                $data['requests'] = $this->$model->get_existingSchRequests($range['status']);
            }
        } else {
            $data['requests'] = $this->$model->get_existingSchRequests($range['status']);
        }
        $data['branches'] = $this->$setmodel->get_branches();
        $data['schemes'] = $this->$model->get_schemes();
        $data['groups'] = $this->$model->get_groups();
        echo json_encode($data);
    }
    function update_request()
    {
        $model = self::ACC_MODEL;
        $sms_model = self::SMS_MODEL;
        $set_model = self::SET_MODEL;
        $mail_model = self::MAIL_MODEL;
        $status = $this->input->post('status');
        $reqdata = $this->input->post('req_data');
        $updatedRec = 0;
        $insRecord = 0;
        $delRecord = 0;
        if (!empty($reqdata) && sizeof($reqdata) > 0 && $status != NULL) {
            $apprSmsService = $this->$set_model->get_service(18);
            $rejSmsService = $this->$set_model->get_service(20);
            $send_notif = $this->$sms_model->check_noti_settings();
            foreach ($reqdata as $data) {
                $employee = $this->session->userdata('uid');
                if ($status == 1 || ($status == 2 && ($data['remark'] != '' || $data['remark'] != NULL))) {
                    $updatedata = array(
                        "status" => $status,
                        "remark" => $data['remark'],
                        "id_scheme" => $data['id_scheme'],
                        "id_branch" => ($data['id_branch'] != '' && $data['id_branch'] != 0 ? $data['id_branch'] : NULL),
                        "scheme_acc_number" => $data['scheme_acc_number'],
                        "ac_name" => $data['ac_name'],
                        "id_employee" => $employee,
                        "firstPayment_amt" => (isset($data['firstPayment_amt']) ? $data['firstPayment_amt'] : NULL),
                        "balance_amount" => (isset($data['balance_amount']) ? $data['balance_amount'] : NULL),
                        "balance_weight" => (isset($data['balance_weight']) ? $data['balance_weight'] : NULL),
                        "last_paid_weight" => (isset($data['last_paid_weight']) ? $data['last_paid_weight'] : NULL),
                        "last_paid_chances" => (isset($data['last_paid_chances']) ? $data['last_paid_chances'] : NULL),
                        "last_paid_date" => (isset($data['last_paid_date']) ? $data['last_paid_date'] . '' . date("H:i:s") : NULL),
                        "paid_installments" => (isset($data['paid_installments']) ? $data['paid_installments'] : NULL),
                        "is_opening" => 1
                    );
                    $updStat = $this->$model->updateRequest($updatedata, $data['id_reg_request']);
                    if ($updStat) {
                        $updatedRec += 1;
                        if ($status == 1) {
                            $account = array(
                                "id_scheme" => $data['id_scheme'],
                                "id_branch" => $data['id_branch'],
                                "scheme_acc_number" => $data['scheme_acc_number'],
                                "account_name" => $data['ac_name'],
                                "id_customer" => $data['id_customer'],
                                "added_by" => $data['added_by'],
                                "group_code" => ($data['scheme_group_code'] != 'null' ? $data['scheme_group_code'] : ($data['group_code'] != 'null' ? $data['group_code'] : NULL)),
                                'start_date' => date("Y-m-d H:i:s"),
                                'is_registered' => 1,
                                'employee_approved' => ($employee ? $employee : 0),
                                'remark_open' => 'Existing scheme registration approved by admin.',
                                // 'active' 	   		=> 1,
                                // 'is_new'	        => 'N',
                                'date_add' => date("Y-m-d H:i:s"),
                                'pan_no' => ($data['pan_no'] != '' ? strtoupper($data['pan_no']) : null),
                                "firstPayment_amt" => (isset($data['firstPayment_amt']) ? $data['firstPayment_amt'] : NULL),
                                "paid_installments" => (isset($data['paid_installments']) ? $data['paid_installments'] : NULL),
                                "balance_amount" => (isset($data['balance_amount']) ? $data['balance_amount'] : NULL),
                                "balance_weight" => (isset($data['balance_weight']) ? $data['balance_weight'] : NULL),
                                "last_paid_weight" => (isset($data['last_paid_weight']) ? $data['last_paid_weight'] : NULL),
                                "last_paid_chances" => (isset($data['last_paid_chances']) ? $data['last_paid_chances'] : NULL),
                                "last_paid_date" => (isset($data['last_paid_date']) ? $data['last_paid_date'] . '' . date("H:i:s") : NULL),
                            );
                            $insert_status = $this->$model->insert_account($account);
                            if ($insert_status) {
                                $insRecord += 1;
                                $msgdata = $this->$sms_model->get_SMS_data(18, '');
                                if ($apprSmsService['serv_sms'] == 1) {
                                    $this->send_sms($data['mobile'], $msgdata['message']);
                                }
                                if ($apprSmsService['serv_whatsapp'] == 1) {
                                    $smsData = ["message" => $msgdata['message'], "template_name" => $apprSmsService['template_name'], "params" => $params];
                                    $this->sms_model->send_whatsApp_message($data['mobile'], $smsData);
                                }
                                if ($send_notif == 1) {
                                    $this->send_notification($data['mobile'], 10);
                                }
                                $company = $this->$set_model->get_company();
                                if ($apprSmsService['serv_email'] == 1 && $data['email'] != '') {
                                    $maildata['status'] = $status;
                                    $maildata['company'] = $company;
                                    $to = $data['email'];
                                    $subject = "Reg- " . $company['company_name'] . " existing scheme registration request";
                                    $message = $this->load->view('include/emailExisRequestStatus', $maildata, true);
                                    $sendEmail = $this->$mail_model->send_email($to, $subject, $message);
                                    //print_r($sendEmail);exit;
                                }
                            }
                        } else if ($status == 2) { // reject
                            $msgdata = $this->$sms_model->get_SMS_data(20, '');
                            if ($rejSmsService['serv_sms'] == 1) {
                                $this->send_sms($data['mobile'], $msgdata['message']);
                            }
                            if ($rejSmsService['serv_whatsapp'] == 1) {
                                $smsData = ["message" => $msgdata['message'], "template_name" => $rejSmsService['template_name'], "params" => $params];
                                $this->sms_model->send_whatsApp_message($data['mobile'], $smsData);
                            }
                            if ($send_notif == 1) {
                                $this->send_notification($data['mobile'], 11);
                            }
                            $company = $this->$set_model->get_company();
                            if ($rejSmsService['serv_email'] == 1 && $data['email'] != '') {
                                $maildata['status'] = $status;
                                $maildata['company'] = $company;
                                $to = $data['email'];
                                $subject = "Reg- " . $company['company_name'] . " existing scheme registration request";
                                $message = $this->load->view('include/emailExisRequestStatus', $maildata, true);
                                $sendEmail = $this->$mail_model->send_email($to, $subject, $message);
                            }
                        }
                    }
                } else if ($status == 3) { // revert -> approve to reject
                    $isPaymentExist = $this->$model->isPaymentExist($data);
                    if ($isPaymentExist['status'] == false) {
                        $updatedata = array(
                            "status" => 2,
                            "remark" => $data['remark'],
                            "id_scheme" => $data['id_scheme'],
                            "id_branch" => ($data['id_branch'] != '' && $data['id_branch'] != 0 ? $data['id_branch'] : NULL),
                            "scheme_acc_number" => $data['scheme_acc_number'],
                            "ac_name" => $data['ac_name'],
                            "id_employee" => $employee,
                        );
                        $updStat = $this->$model->updateRequest($updatedata, $data['id_reg_request']);
                        if ($updStat && $isPaymentExist['id_scheme_account'] != NULL) {
                            $updatedRec += 1;
                            $account = array(
                                "id_scheme" => $data['id_scheme'],
                                "id_branch" => $data['id_branch'],
                                "scheme_acc_number" => $data['scheme_acc_number'],
                                "id_customer" => $data['id_customer']
                            );
                            $deleteAcc = $this->$model->deleteAcc($account, $isPaymentExist['id_scheme_account']);
                            if ($deleteAcc) {
                                $delRecord += 1;
                                // 				if($smsService['serv_sms'] == 1)
                                // 				{	
                                // 					$msgdata =$this->$sms_model->get_SMS_data(18,'');
                                // 					//$message = "Hi, Your Saravana Stores Thanganagai Maligai existing scheme has been activated you can use our web or mobile app to make the payment any time ";
                                // 					$this->send_sms( $data['mobile'],$msgdata['message']);
                                // 				}
                                // 				$company = $this->$set_model->get_company();
                                // 				if($send_notif == 1){
                                // 				    $this->send_notification($data['mobile']);
                                // }
                            }
                            $company = $this->$set_model->get_company();
                            if ($smsService['serv_email'] == 1 && $data['email'] != '') {
                                $maildata['status'] = 2;
                                $maildata['company'] = $company;
                                $to = $data['email'];
                                $subject = "Reg- " . $company['company_name'] . " existing scheme registration request";
                                $message = $this->load->view('include/emailExisRequestStatus', $maildata, true);
                                $sendEmail = $this->$mail_model->send_email($to, $subject, $message);
                                //print_r($sendEmail);exit;
                            }
                        }
                    }
                }
            }
            if ($status == 1) {
                $title = "Registration Request Approval";
                $class = "success";
            } else if ($status == 3) {
                $class = "info";
                $title = "Revert Registration Request ";
            } else if ($status == 2) {
                $class = "danger";
                $title = "Reject Registration Request ";
            }
            if ($insRecord > 0) {
                $msg = $updatedRec . ' records updated and ' . $insRecord . ' records created successfully';
            } else {
                if ($delRecord > 0) {
                    $msg = $delRecord . ' records reverted' . ($delRecord > 0 ? ' successfully' : '');
                } else {
                    $msg = $updatedRec . ' records updated' . ($updatedRec > 0 ? ' successfully' : '');
                }
            }
            $this->session->set_flashdata('chit_alert', array('message' => $msg, 'class' => $class, 'title' => $title));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation...', 'class' => 'danger', 'title' => 'Registration Request Approval'));
        }
    }
    function send_notification($mobile)
    {
        $model = self::ACC_MODEL;
        $sms_model = self::SMS_MODEL;
        $noti = $this->$model->get_notiContent(10);
        $data['token'] = $this->$model->getnotificationids($mobile);
        $i = 1;
        foreach ($data['token'] as $row) {
            if (sizeof($row['token']) > 0) {
                $targetUrl = '#/app/notification';
                $arraycontent = array(
                    'token' => $row['token'],
                    'notification_service' => 10,
                    'header' => $noti['header'],
                    'message' => $noti['data'],
                    'mobile' => $row['mobile'],
                    'footer' => $noti['footer'],
                    'id_customer' => $row['id_customer'],
                    'targerURL' => $targetUrl,
                    'noti_img' => ''
                );
                $res = $this->send_singlealert_notification($arraycontent);
                $r = json_decode($res);
                if ($r->recipients > 0) {
                    $this->$sms_model->insert_sent_notification($arraycontent);
                }
                $result['noti'][$i] = $res;
                $i++;
            }
        }
        //		}
        return true;
    }
    function send_singlealert_notification($alertdetails = array())
    {
        $this->load->helper('push_notification');
        if (notify_platform_is('fcm')) {
            return $this->send_singlealert_notification_fcm($alertdetails);
        }
        return $this->send_singlealert_notification_onesignal($alertdetails);
    }
    function send_singlealert_notification_fcm($alertdetails = array())
    {
        $this->load->library('fcm_service');
        return $this->fcm_service->sendToTokens(
            (isset($alertdetails['token']) ? (array) $alertdetails['token'] : array()),
            (isset($alertdetails['header']) ? $alertdetails['header'] : ''),
            (isset($alertdetails['message']) ? $alertdetails['message'] : ''),
            array(
                'targetUrl'    => '#/app/notification',
                'noti_service' => (isset($alertdetails['notification_service']) ? $alertdetails['notification_service'] : ''),
                'mobile'       => (isset($alertdetails['mobile']) ? $alertdetails['mobile'] : '')
            ),
            (isset($alertdetails['noti_img']) ? $alertdetails['noti_img'] : '')
        );
    }
    function send_singlealert_notification_onesignal($alertdetails = array())
    {
        $this->load->helper('push_notification');
        $onesignal = notify_cred_for('onesignal');
        $registrationIds = array();
        // Accept either one token or a batch -- OneSignal takes many player ids per request.
        $registrationIds = (isset($alertdetails['token']) && is_array($alertdetails['token'])
            ? array_values($alertdetails['token'])
            : array(isset($alertdetails['token']) ? $alertdetails['token'] : ''));
        $content = array(
            "en" => $alertdetails['message']
        );
        $targetUrl = '#/app/notification';
        $fields = array(
            'app_id' => $onesignal['id'],
            'include_player_ids' => $registrationIds,
            'contents' => $content,
            'headings' => array("en" => $alertdetails['header']),
            'subtitle' => array("en" => $alertdetails['footer']),
            'data' => array('targetUrl' => $targetUrl, 'noti_service' => $alertdetails['notification_service'], 'mobile' => $alertdetails['mobile']),
            'big_picture' => (isset($alertdetails['noti_img']) ? $alertdetails['noti_img'] : " ")
        );
        $auth_key = $onesignal['key'];
        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . $auth_key
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        // Without timeouts one stalled connection hangs the page forever.
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    function customer_enquiry()
    {
        $data['main_content'] = self::ENQUIRY_VIEW . 'customer_enquiry';
        $this->load->view('layout/template', $data);
    }
    function ajax_enquiry_list()
    {
        $model = self::ACC_MODEL;
        $setmodel = self::SET_MODEL;
        if (!empty($_POST)) {
            $data['requests'] = $this->$model->get_customerenquiry();
        }
    }
    //schemegroup //
    public function schemegroup_form($type = "", $id = "")
    {
        $model = self::ACC_MODEL;
        $set_model = self::SET_MODEL;
        $log_model = self::LOG_MODEL;
        switch ($type) {
            case 'View':
                $set_model = self::SET_MODEL;
                $data['group'] = $this->$model->group_empty();
                $data['main_content'] = self::GROUP_VIEW . 'form';
                $this->load->view('layout/template', $data);
                break;
            case 'Edit':
                $data['group'] = $this->$model->get_groupaccount_details($id);
                //   print_r($data);exit;
                $data['main_content'] = self::GROUP_VIEW . 'form';
                $this->load->view('layout/template', $data);
                break;
            case "Save":
                //get form values
                $scheme_group = $this->input->post('group');
                //formatting form values
                $insertData = array(
                    'id_scheme' => (isset($scheme_group['id_scheme']) ? $scheme_group['id_scheme'] : NULL),
                    'id_branch' => (isset($scheme_group['id_branch']) ? $scheme_group['id_branch'] : NULL),
                    'group_code' => (isset($scheme_group['group_code']) ? $scheme_group['group_code'] : NULL),
                    'start_date' => (isset($scheme_group['start_date']) && $scheme_group['start_date'] != '' ? date('Y-m-d', strtotime(str_replace("/", "-", $scheme_group['start_date']))) : NULL),
                    'end_date' => (isset($scheme_group['end_date']) && $scheme_group['end_date'] != '' ? date('Y-m-d', strtotime(str_replace("/", "-", $scheme_group['end_date']))) : NULL),
                    'last_update' => date("Y-m-d H:i:s"),
                    'date_add' => date("Y-m-d H:i:s")
                );
                $status = $this->$model->insert_groupaccount($insertData);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Scheme Group <' . ($account['scheme_acc_number'] != NULL ? $account['scheme_acc_number'] : $account['account_name']) . '> created successfully', 'class' => 'success', 'title' => 'Scheme Group'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Close Scheme Account'));
                }
                redirect('account/scheme_group/list');
                break;
            case "Update":
                //get form values
                $scheme_group = $this->input->post('group');
                //formatting form values
                $insertData = array(
                    'id_scheme' => (isset($scheme_group['id_scheme']) ? $scheme_group['id_scheme'] : NULL),
                    'id_branch' => (isset($scheme_group['id_branch']) ? $scheme_group['id_branch'] : NULL),
                    'group_code' => (isset($scheme_group['group_code']) ? $scheme_group['group_code'] : NULL),
                    'start_date' => (isset($scheme_group['start_date']) && $scheme_group['start_date'] != '' ? date('Y-m-d', strtotime(str_replace("/", "-", $scheme_group['start_date']))) : NULL),
                    'end_date' => (isset($scheme_group['end_date']) && $scheme_group['end_date'] != '' ? date('Y-m-d', strtotime(str_replace("/", "-", $scheme_group['end_date']))) : NULL),
                    'last_update' => date("Y-m-d H:i:s"),
                    'date_add' => date("Y-m-d H:i:s")
                );
                $status = $this->$model->update_groupaccount($insertData, $id);
                if ($status) {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Scheme Group <' . ($account['scheme_acc_number'] != NULL ? $account['scheme_acc_number'] : $account['account_name']) . '>Edit successfully', 'class' => 'success', 'title' => 'Scheme Group'));
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Close Scheme Account'));
                }
                redirect('account/scheme_group/list');
                break;
            case 'Delete':
                $this->db->trans_begin();
                $data = $this->$model->get_groupaccount_details($id);
                $status = $this->$model->delete_group($data, $id);
                if ($status == TRUE) {
                    //print_r(  $status);exit;
                    $this->db->trans_commit();
                    $log_data = array(
                        'id_log' => $this->id_log,
                        'event_date' => date("Y-m-d H:i:s"),
                        'module' => 'Scheme group',
                        'operation' => 'Delete',
                        'record' => $id,
                        'remark' => 'Scheme group deleted successfully'
                    );
                    $this->$log_model->log_detail('insert', '', $log_data);
                    $this->session->set_flashdata('chit_alert', array('message' => 'Scheme account deleted successfully', 'class' => 'success', 'title' => 'Delete Scheme group'));
                    redirect('account/scheme_group/list');
                } else {
                    //  $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Delete Scheme Account'));
                    redirect('account/scheme_group/list');
                }
                break;
        }
    }
    function check_group()
    {
        $model = self::ACC_MODEL;
        $group_code = $this->input->post('group_code');
        $available = $this->$model->code_available($group_code);
        if ($available) {
            echo TRUE;
        } else {
            echo FALSE;
        }
    }
    public function open_account_list()
    {
        $model = self::ACC_MODEL;
        //$data['accounts']=$this->$model->get_all_account();
        //print_r($data);exit;
        $data['main_content'] = self::ACC_VIEW . 'acc_list';
        //print_r($data);exit;
        $this->load->view('layout/template', $data);
    }
    public function get_all_scheme_account()
    {
        $model = self::ACC_MODEL;
        $set_model = self::SET_MODEL;
        $mobile = $_POST['mobile'];
        $access = $this->$set_model->get_access('account/close');
        $close = $this->session->userdata('profile');
        $items = $this->$model->get_all_scheme_account_list($mobile);
        $closed_account = array(
            'access' => $access,
            'data' => $items,
            'close_acc' => $close
        );
        echo json_encode($closed_account);
    }
    public function ajax_close_account_list($id_branch = "", $from_date = "", $to_date = "", $type = "")
    {
        $model = self::ACC_MODEL;
        $data['accounts'] = $this->$model->get_scheme_type_closed_account($id_branch, $from_date, $to_date, $type);
        $data['main_content'] = self::CLOSED_VIEW . 'closed_list';
        //print_r($data);exit;
        $this->load->view('layout/template', $data);
    }
    // get_group for group filter //
    public function get_groups()
    {
        $model = self::ACC_MODEL;
        //  $id_scheme = $this->input->post('id_scheme');
        $scheme = $this->$model->get_group();
        echo json_encode($scheme);
    }
    //gift Issue & Prize count option HH//	
    function generate_giftotp()
    {
        $model = self::PAY_MODEL;
        $account = self::ACC_MODEL;
        $id_customer = $this->input->post('id_customer');
        $data = $this->$model->get_customer($id_customer);
        $entry_date = $this->admin_settings_model->settingsDB('get', '', '');
        //$payOTP_exp= $this->$model->payOTP_exp();
        $mobile = $data['mobile'];
        $firstname = $data['firstname'];
        $OTP = mt_rand(100001, 999999);
        //$OTP = 111111;  
        $this->session->set_userdata('pay_OTP', $OTP);
        //$duration = $this->config->item('payOTP_exp'); // in seconds
        // $duration = $payOTP_exp; // in seconds
        //$duration = 10; // in seconds
        $this->session->set_userdata('pay_OTP_expiry', time() + $duration);
        if ($entry_date[0]['req_gift_issue_otp'] == 1) {
            $message = "Dear " . $firstname . ", Your OTP for Issue Gift is " . $OTP . "  ";
        } else if ($entry_date[0]['req_prize_issue_otp'] == 1) {
            $message = "Dear " . $firstname . ", Your OTP for Issue Prize is " . $OTP . "  ";
        }
        if ($this->config->item('sms_gateway') == '1') {
            $this->sms_model->sendSMS_MSG91($mobile, $message);
        } elseif ($this->config->item('sms_gateway') == '2') {
            $this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
        } elseif ($this->config->item('sms_gateway') == '3') {
            $this->sms_model->sendSMS_SpearUC($mobile, $message, '', '');
        } elseif ($this->config->item('sms_gateway') == '4') {
            $sendSMS = $this->sms_model->sendSMS_Asterixt($mobile, $message, '');
        } elseif ($this->config->item('sms_gateway') == '5') {
            $sendSMS = $this->sms_model->sendSMS_Qikberry($mobile, $message, '');
        }
        $otp['otp_gen_time'] = date("Y-m-d H:i:s");
        $otp['otp_code'] = $OTP;
        $status = $this->$account->otp_insert($otp);
        $data = array('result' => 3, 'msg' => "OTP Sent Successfully", 'otp' => $OTP);
        echo json_encode($data);
    }
    /* function update_giftotp()
     {
          $accountmodel=	self::ACC_MODEL;
          $payment =	self::PAY_MODEL;
          $otp  = $this->input->post('otp');
          $data = $this->$accountmodel->select_otp($otp);	
           if($otp=$this->session->userdata('pay_OTP'))
          {
              $data['is_verified']	= '1';
              $data['verified_time']= date("Y-m-d H:i:s");
              $status=$this->$accountmodel->otp_update_payment($data,$data['id_otp']);
              $data=array('result'=>1 ,'msg'=>'OTP updated successfully');
          }
          else
          {
              $data=array('result'=>6 ,'msg'=>'Invalid OTP');
          }
          echo json_encode($data);
      }*/
    function gift_verify_otp()
    {
        //print_r($this->input->post('otp'));exit;
        if ($this->session->userdata('pay_OTP') == $this->input->post('otp')) {
            $data = array('result' => 1, 'msg' => 'OTP Verified successfully');
        } else {
            $data = array('result' => 6, 'msg' => 'Invalid OTP');
        }
        echo json_encode($data);
    }
    function resend_giftotp()
    {
        $model = self::PAY_MODEL;
        $account = self::ACC_MODEL;
        $id_customer = $this->input->post('id_customer');
        $data = $this->$model->get_customer($id_customer);
        $entry_date = $this->admin_settings_model->settingsDB('get', '', '');
        $mobile = $data['mobile'];
        $firstname = $data['firstname'];
        $OTP = mt_rand(100001, 999999);
        $this->session->set_userdata('pay_OTP', $OTP);
        if ($entry_date[0]['req_gift_issue_otp'] == 1) {
            $message = "Dear " . $firstname . ", Your OTP for Issue Gift is " . $OTP . "  ";
        } else if ($entry_date[0]['req_prize_issue_otp'] == 1) {
            $message = "Dear " . $firstname . ", Your OTP for Issue Prize is " . $OTP . "  ";
        }
        if ($this->config->item('sms_gateway') == '1') {
            $sendSMS = $this->sms_model->sendSMS_MSG91($mobile, $message);
        } elseif ($this->config->item('sms_gateway') == '2') {
            $sendSMS = $this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
        } elseif ($this->config->item('sms_gateway') == '3') {
            $sendSMS = $this->sms_model->sendSMS_SpearUC($mobile, $message, '', '');
        } elseif ($this->config->item('sms_gateway') == '4') {
            $sendSMS = $this->sms_model->sendSMS_Asterixt($mobile, $message, '', $dtl_te_id);
        } elseif ($this->config->item('sms_gateway') == '5') {
            $sendSMS = $this->sms_model->sendSMS_Qikberry($mobile, $message, '', $dtl_te_id);
        }
        $otp['otp_gen_time'] = date("Y-m-d H:i:s");
        $otp['otp_code'] = $OTP;
        $status = $this->$account->otp_insert($otp);
        $data = array('result' => 3, 'msg' => '"OTP Sent Successfully', 'otp' => $OTP);
        echo json_encode($data);
    }
    public function gift_issue()
    {
        $model = self::ACC_MODEL;
        $insArr = array(
            "id_scheme_account" => $this->input->post('id_sch_acc'),
            "gift_desc" => $this->input->post('issue_entered'),
            "id_employee" => $this->session->userdata('uid'),
            "date_issued" => date("Y-m-d H:i:s")
        );
        $issue = $this->$model->add_gift($insArr);
        echo $issue;
    }
    //gift issued list hh//	
    public function get_gift_issued_list()
    {
        $model = self::ACC_MODEL;
        $id = $this->input->get('id_scheme_account');
        $data = $this->$model->get_gift_issued($id);
        //print_r($this->db->last_query());exit;
        echo json_encode($data);
    }
    public function get_gift_issued_Dropdown()
    {
        $model = self::ACC_MODEL;
        $id = $this->input->get('id_scheme_account');
        $data = $this->$model->get_gift_issued_form($id);
        //print_r($this->db->last_query());exit;
        echo json_encode($data);
    }
    //gift issued list hh//	
    public function getCustomersBySearch()
    {
        $model = self::ACC_MODEL;
        $data = $this->$model->getAvailableCustomers($_POST['searchTxt']);
        echo json_encode($data);
    }
    // Rate Fixing based on the Otp  verify//HH
    function rateFixing_otp()
    {
        $mob_no_len = $this->session->userdata('mob_no_len');
        $id_scheme_account = $this->input->post('id_scheme_account');
        $mobile = $this->input->post('mobile');
        //print_r($mobile);exit;
        $model = self::ACC_MODEL;
        $pay_model = self::PAY_MODEL;
        $this->comp = $this->$model->company_details();
        $data = $this->$pay_model->get_customer_mob($mobile);
        if (strlen(trim($mobile)) == $mob_no_len) {
            $this->session->unset_userdata("OTP");
            $OTP = mt_rand(100000, 999999);
            $this->session->set_userdata('OTP', $OTP);
            $this->session->set_userdata('rate_fixing_otp_exp', time() + 100);
            //	$message = $OTP." is the verification code from ".$this->comp['company_name'].".Please use this code to fix rate.";
            $message = "Hi, " . $OTP . " is the OTP from " . $this->comp['company_name'] . " .Please use this code to verify your mobile number.";
            $this->send_sms($mobile, $message, '1207161521518063977');
        }
        $result = array('result' => 3, 'msg' => '"OTP Sent Successfully', 'otp' => $OTP);
        echo json_encode($result);
    }
    function submit_ratefix()
    {
        $pay_model = self::PAY_MODEL;
        $settings = $this->$pay_model->get_settings();
        $data = $this->$pay_model->get_customer_mob($_POST['mobile']);
        if ($this->session->userdata('OTP') == $_POST['otp']) {
            $rate = $_POST['metal_rate'];
            if ($this->config->item("integrationType") == 0) {
                $isRateFixed = $this->$pay_model->isRateFixed($_POST['id_scheme_account']);
                if ($isRateFixed['status'] == 0) {
                    $metal_wgt = $isRateFixed['firstPayment_amt'] / $rate;
                    $updData = array(
                        "fixed_wgt" => $metal_wgt,
                        "fixed_metal_rate" => $rate,
                        "rate_fixed_in" => 0,
                        "fixed_rate_on" => date("Y-m-d H:i:s")
                    );
                    //print_r($updData);exit;
                    $status = $this->$pay_model->updFixedRate($updData, $_POST['id_scheme_account']);
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
                    $rate = $this->$pay_model->getGold22ct($settings['is_branchwise_rate'], $data['id_branch']);
                    if ($rate != 0) {
                        $postData = array((array("SchemeAccountNo" => trim($_POST["sch_ac_no"]), "MetalRate" => $rate, "FixRequestFrom" => 2)));
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
                                    $status = $this->$pay_model->updFixedRate($updData, $data['id_sch_ac']);
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
            $result = array('success' => false, 'msg' => 'Invalid OTP');
        }
        echo json_encode($result);
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
    // Rate Fixing based on the Otp  verify//
    //Scheme Receipt
    function get_scheme_receipt($id_scheme_account)
    {
        $model = self::PAY_MODEL;
        $data['scheme'] = $this->$model->get_scheme_details($id_scheme_account);
        //echo "<pre>"; print_r($data);exit;
        $this->load->helper(array('dompdf', 'file'));
        $dompdf = new DOMPDF();
        $html = $this->load->view('scheme/opening/scheme_account_receipt', $data, true);
        $dompdf->load_html($html);
        $dompdf->set_paper("a4", "portriat");
        $dompdf->render();
        while(ob_get_level()) ob_end_clean();
        $dompdf->stream("Receipt.pdf", array('Attachment' => 0));
    }
    //Scheme Receipt
    function get_metal_name()
    {
        $model = self::ACC_MODEL;
        $data = $this->$model->get_metal_name();
        echo json_encode($data);
    }
    function passbook_print($page, $id_scheme_account, $id_payment = "")
    {
        //15-12-2022 Immanuvel passbook_print
        /*$acc_model = self::ACC_MODEL;		
        $data['customer'] = $this->$acc_model->get_account_detail($id_scheme_account);
        //echo "<pre>";print_r($data);exit;
        $this->load->helper(array('dompdf', 'file'));
        $dompdf = new DOMPDF();
        if($page == 'B' && $data['customer']['is_closed']!=1){
                $data['payment']  = $this->$acc_model->get_ac_paid_details($id_scheme_account);
                $html = $this->load->view('scheme/print/passbook_back', $data,true);
                   $dompdf->load_html($html);
                $customPaper = array(0,0,529.13,1020.5);
                $dompdf->set_paper($customPaper, "portriat" );
                $dompdf->render();
                while(ob_get_level()) ob_end_clean();
                $dompdf->stream("Passbook.pdf",array('Attachment'=>0));
                // Update as print taken
                //foreach($data['payment'] as $pay){
                   //$this->$acc_model->updateData(array('is_print_taken'=>1),'id_payment',$pay['id_payment'],'payment');
                //
            }*/
        $acc_model = self::ACC_MODEL;
        $data['customer'] = $this->$acc_model->get_account_detail($id_scheme_account);
        $data['acc'] = $this->$acc_model->get_closed_account_by_id($id_scheme_account);
        $data['payment'] = $this->$acc_model->get_ac_paid_details($id_scheme_account);
        $data['company'] = $this->$acc_model->get_company_details();
        //  echo "<pre>";print_r($data);exit;
        $this->load->helper(array('dompdf', 'file'));
        $dompdf = new DOMPDF();
        if ($page == 'B' && $data['acc']['is_closed'] != 1 && $data['customer']['classification_name'] == 'SRINIDHI') {
            // print_r($data['customer']['classification_name']);exit;
            //$html = $this->load->view('scheme/print/passbook_scheme', $data,true);
            $html = $this->load->view('scheme/print/passbook_back', $data, true);
            $dompdf->load_html($html);
            $customPaper = array(0, 0, 529.13, 1020.5);
            $dompdf->set_paper($customPaper, "portriat");
            $dompdf->render();
            while(ob_get_level()) ob_end_clean();
            $dompdf->stream("Passbook.pdf", array('Attachment' => 0));
            foreach ($data['payment'] as $pay) {
                $this->$acc_model->updateData(array('is_print_taken' => 1), 'id_payment', $pay['id_payment'], 'payment');
            }
        } else if ($page == 'B' && $data['acc']['is_closed'] != 1 && $data['customer']['classification_name'] != 'SRINIDHI') {
            if ($id_payment > 0) {
                $datapay = [];
                foreach ($data['payment'] as $pay) {
                    if ($pay['id_payment'] == $id_payment) {
                        $pay['is_print_taken'] = 0;
                    } else {
                        $pay['is_print_taken'] = 1;
                    }
                    $datapay[] = $pay;
                }
                $data['payment'] = $datapay;
            }
            $html = $this->load->view('scheme/print/passbook_back', $data, true);
            // This is only for coswan
            // $html = $this->load->view('scheme/print/passbook_backcoswan', $data, true);
            $dompdf->load_html($html);
            $customPaper = array(0, 0, 529.13, 1035.6);
            // This is for coswan only
            // $customPaper = array(0, 0, 481.89, 481.89);
            $dompdf->set_paper($customPaper, "portriat");
            $dompdf->render();
            while(ob_get_level()) ob_end_clean();
            $dompdf->stream("Passbook.pdf", array('Attachment' => 0));
            // Update as print taken
            foreach ($data['payment'] as $pay) {
                //   $this->$acc_model->updateData(array('is_print_taken'=>1),'id_payment',$pay['id_payment'],'payment');  // esakki 11-11
            }
        } else if ($page == 'B' && $data['acc']['is_closed'] == 1) {
            $data['payment'] = $this->$acc_model->get_ac_paid_details($id_scheme_account);
            $html = $this->load->view('scheme/print/passbook_close', $data, true);
            $dompdf->load_html($html);
            $customPaper = array(0, 0, 529.13, 1020.5);
            $dompdf->set_paper($customPaper, "portriat");
            $dompdf->render();
            while(ob_get_level()) ob_end_clean();
            $dompdf->stream("Passbook.pdf", array('Attachment' => 0));
        } else if ($page == 'F') {
            $html = $this->load->view('scheme/print/passbook_front', $data, true);
            $dompdf->load_html($html);
            if ($data['customer']['one_time_premium'] == 1) {
                $dompdf->set_paper("a5", "portriat");
            } else {
                $customPaper = array(0, 0, 690, 350);
                $dompdf->set_paper($customPaper, "portriat");
            }
            $dompdf->render();
            while(ob_get_level()) ob_end_clean();
            $dompdf->stream("Passbook.pdf", array('Attachment' => 0));
        } else if ($page == 'PAY' && !empty($id_payment) && $data['customer']['classification_name'] != 'SRINIDHI') {
            // Decode base64-encoded id_payment string
            $decoded = base64_decode($id_payment);  // "20937,20938,20939"
            $payment_ids = explode(',', $decoded);
            $datapay = [];
            $all_payments = $this->$acc_model->get_ac_paid_details($id_scheme_account);
            foreach ($all_payments as $pay) {
                $pay['is_print_taken'] = 1;
                if (in_array($pay['id_payment'], $payment_ids)) {
                    $pay['is_print_taken'] = 0;
                }
                $datapay[] = $pay;
            }
            $data['payment'] = $datapay;
            // Load the view for the passbook back
            $html = $this->load->view('scheme/print/passbook_back', $data, true);
            $dompdf->loadHtml($html);
            $dompdf->setPaper([0, 0, 529.13, 1035.6], 'portrait');
            $dompdf->render();
            while(ob_get_level()) ob_end_clean();
            $dompdf->stream("Passbook.pdf", ['Attachment' => 0]);
        }
        // This is old code, commented out for reference
        //  else if ($page == 'PAY' && $id_payment != "" && $data['customer']['classification_name'] != 'SRINIDHI') {
        //     $datapay = [];
        //     $payment = $this->$acc_model->get_ac_paid_details($id_scheme_account);
        //     foreach ($payment as $pay) {
        //         $pay['is_print_taken'] = 1;
        //         if ($pay['id_payment'] == $id_payment) {
        //             $pay['is_print_taken'] = 0;
        //         }
        //         $datapay[] = $pay;
        //     }
        //     $data['payment'] = $datapay;
        //     $html = $this->load->view('scheme/print/passbook_back', $data, true);
        //     $dompdf->load_html($html);
        //     $customPaper = array(0, 0, 529.13, 1035.6);
        //     $dompdf->set_paper($customPaper, "portriat");
        //     $dompdf->render();
        //     $dompdf->stream("Passbook.pdf", array('Attachment' => 0));
        // } 
        else if ($page == 'PAY' && $id_payment != "" && $data['customer']['classification_name'] == 'SRINIDHI') {
            $datapay = [];
            $payment = $this->$acc_model->get_ac_paid_details($id_scheme_account);
            foreach ($payment as $pay) {
                $pay['is_print_taken'] = 1;
                if ($pay['id_payment'] == $id_payment) {
                    $pay['is_print_taken'] = 0;
                }
                $datapay[] = $pay;
            }
            $data['payment'] = $datapay;
            $html = $this->load->view('scheme/print/passbook_scheme', $data, true);
            $dompdf->load_html($html);
            $customPaper = array(0, 0, 529.13, 1020.5);
            $dompdf->set_paper($customPaper, "portriat");
            $dompdf->render();
            while(ob_get_level()) ob_end_clean();
            $dompdf->stream("Passbook.pdf", array('Attachment' => 0));
        } else if ($page == 'bond' && $id_payment != "" && $id_scheme_account != '') {
            $paymentModel = self::PAY_MODEL;
            // Gold Rate
            $goldMetalData = $this->$paymentModel->get_metalrate_by_branch(2, 1, 1, $data['payment'][0]['date_payment']);
            // Silver Rate
            $silverMetalData = $this->$paymentModel->get_metalrate_by_branch(2, 2, 16, $data['payment'][0]['date_payment']);
            $customerPan = !empty($data['customer']['pan_no']) ? $data['customer']['pan_no'] : '-';
            $customerAddress2 = empty($data['customer']['address2']) ? $data['customer']['city'] : $data['customer']['address2'];
            $customerAddress2 = $customerAddress2 . ' - ' . $data['customer']['pincode'];
            // echo("<pre>");
            // print_r($data['acc']);
            // print_r($data['payment']);
            // print_r($data['customer']);
            // exit;
            $purityDetails = $this->$acc_model->getPurityName($data['customer']['id_purity']);
            // print_r($data['customer']['id_metal']);exit;
            $metalName = $this->$acc_model->getMetalName($data['customer']['id_metal']);
            $paymentModeFullName = $this->$acc_model->paymentModeName($data['payment'][0]['payment_mode']);
            $dateStr = $data['payment'][0]['date_payment'];
            $date = DateTime::createFromFormat('d-m-y', $dateStr);
            $data['paymentDate'] = $date ? $date->format('d-m-Y') : '';
            // print_r($data['paymentDate']);exit;
            // print_r($paymentModeFullName);exit;
            // print_r($purityDetails);exit;
            $data = [
                'paymentDate' => $data['paymentDate'],
                'customerName' => $data['customer']['customer_name'],
                'customerAddress1' => $data['customer']['address1'],
                'customerAddress2' => $customerAddress2,
                'city' => $data['customer']['city'],
                'customerMobile' => $data['customer']['mobile'],
                'customerPan' => $customerPan,
                'schemeName' => $data['customer']['scheme_name'],
                'paymentMode' => strtoupper($paymentModeFullName['mode_name']),
                'paymentWeight' => $data['payment'][0]['metal_weight'],
                'schemeMasterPurity' => $purityDetails['purity'],
                'maturity' => $data['customer']['maturity_installment'],
                'companyName' => $purityDetails['company_name'],
                'metalName' => $metalName['metal'],
                'maturityDate' => $data['customer']['maturity_date'],
                'payment_ref_number' => $data['payment'][0]['payment_ref_number'],
                'payment_amount' => $data['payment'][0]['payment_amount'],
                'goldRate' => $goldMetalData,
                'silverRate' => $silverMetalData
            ];
            // print_r($data);exit;
            $this->load->helper(array('dompdf', 'file'));
            $dompdf = new DOMPDF();
            $html = $this->load->view('scheme/print/bond_print', $data, true);
            $dompdf->load_html($html);
            // $customPaper = array(0, 0, 595, 841); 
            $dompdf->set_paper('A4', 'portrait');
            $dompdf->render();
            while(ob_get_level()) ob_end_clean();
            $dompdf->stream("Bond_Receipt.pdf", array("Attachment" => 0));
        }
    }
    //15-12-2022 Immanuvel customer sch detail print start
    function receipt_account_ForBarCOde($id_scheme_account)
    {
        $acc_model = self::ACC_MODEL;
        $data['customer'] = $this->$acc_model->get_account_open($id_scheme_account);
        $this->load->helper(array('dompdf', 'file'));
        $dompdf = new DOMPDF();
        $this->load->library('phpqrcode/qrlib');
        $SERVERFILEPATH = FCPATH . 'assets/img/account';
        $file_name = $SERVERFILEPATH . '/' . $id_scheme_account . ".png";
        if (!file_exists($file_name)) {
            if (!is_dir($SERVERFILEPATH)) {
                mkdir($SERVERFILEPATH, 0777, TRUE);
            }
            QRcode::png($data['customer']['mobile'], $file_name);  //Passing QR data
        }
        $data['customer']["qr_code"] = base_url() . 'assets/img/account/' . $id_scheme_account . ".png";
        //echo $data['customer']["qr_code"];
        $html = $this->load->view('scheme/print/receipt_account', $data, true);
        $html = preg_replace('/>\s+</', "><", $html); //Remove Blank page
        $dompdf->load_html($html);
        //	$customPaper = array(0,0,190,95);
        $customPaper = array(0, 0, 125, 60);
        $dompdf->set_paper($customPaper, "portriat");
        $dompdf->render();
        while(ob_get_level()) ob_end_clean();
        $dompdf->stream("Passbook.pdf", array('Attachment' => 0));
    }
    //customer sch detail print End
    //for QR code.....
    /*function receipt_account($id_scheme_account)
     {
          $acc_model = self::ACC_MODEL;
          $data['customer'] = $this->$acc_model->get_account_open($id_scheme_account);
          $this->load->helper(array('dompdf', 'file'));
          $dompdf = new DOMPDF();
          //$this->load->library('phpqrcode/qrlib');
          //load library
          $this->load->library('zend');
          //load in folder Zend
          $this->zend->load('Zend/Barcode');
          //$barcodeimage=Zend_Barcode::render('code128', 'image', array('text'=>$data['customer']['id_scheme_account']), array())->draw();
          $barcode_options=array('text'=>$data['customer']['id_scheme_account'],
          'drawText' => false,
          'barHeight'=> 30,
                 );
          $barcodeimage=Zend_Barcode::factory('code128', 'image',$barcode_options, array())->draw();
          $SERVERFILEPATH = FCPATH. 'assets/img/account';
          $file_name = $SERVERFILEPATH.'/'.$id_scheme_account.".png";
          if(!file_exists($file_name)){
          if (!is_dir($SERVERFILEPATH)) {  
          mkdir($SERVERFILEPATH, 0777, TRUE);
          }
          imagepng($barcodeimage, $file_name);
          //QRcode::png($data['customer']['id_scheme_account'],$file_name);  //Passing QR data
          //generate barcode
          //
          }
          //$data['customer']["qr_code"] = base_url().'assets/img/account/'.$id_scheme_account.".png";
          $data['customer']["bar_code"] = base_url().'assets/img/account/'.$id_scheme_account.".png";
          //print_r($data);exit;
          $html = $this->load->view('scheme/print/receipt_account', $data,true);
            $html = preg_replace('/>\s+</', "><", $html); //Remove Blank page
          $dompdf->load_html($html);
      //	$customPaper = array(0,0,190,95);
          //$customPaper = array(0,0,125,60);
         // $customPaper = array(0,0,205,57);
          $customPaper = array(0,0,0,0);
          $dompdf->set_paper($customPaper, "portriat" );
          $dompdf->render();
          while(ob_get_level()) ob_end_clean();
          $dompdf->stream("Passbook.pdf",array('Attachment'=>0));
     }*/
    public function receipt_account($id_scheme_account)
    {
        $acc_model = self::ACC_MODEL;
        $data['customer'] = $this->$acc_model->get_account_open($id_scheme_account);
        $this->load->helper(array('dompdf', 'file'));
        $this->load->library('phpqrcode/qrlib');
        $SERVERFILEPATH = FCPATH . 'assets/img/account';
        $file_name = $SERVERFILEPATH . '/' . $id_scheme_account . ".png";
        if (!file_exists($file_name)) {
            if (!is_dir($SERVERFILEPATH)) {
                mkdir($SERVERFILEPATH, 0777, true);
            }
            // Create QR code image file directly
            QRcode::png($data['customer']['id_scheme_account'], $file_name);
        }
        $data['customer']["qr_code"] = base_url() . 'assets/img/account/' . $id_scheme_account . ".png";
        // echo '<pre>';print_r($data);echo '</pre>';exit;
        $receipt_content = $this->load->view('scheme/print/receipt_account_qr_prn', $data, true);
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="account_' . date('Ymd_His') . '_lmx.prn"');
        header('Content-Length: ' . strlen($receipt_content));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Expires: 0');
        echo $receipt_content;
        exit;
        /* $html = $this->load->view('scheme/print/receipt_account_qr', $data, true);
    $dompdf = new DOMPDF();
    $dompdf->load_html($html);
    $dompdf->setPaper('custom', [72 * 0.984, 72 * 0.787]); // for 2.5cm x 2cm
        $dompdf->render();
    ob_end_clean(); // Clear any previous output
        $dompdf->stream("Passbook.pdf", array('Attachment' => false)); */
    }
    function passbook_reprint()
    {
        $status = false;
        $acc_model = self::ACC_MODEL;
        $id_sch_acc = $_POST['id_scheme_account'];
        $this->$acc_model->updateAcc($id_sch_acc);
        foreach ($_POST['pay_ids'] as $pay_id) {
            $status = $this->$acc_model->updateData(array('is_print_taken' => 0), 'id_payment', $pay_id, 'payment');
        }
        echo $status;
    }
    function loadGiftData()
    {
        $model = self::ACC_MODEL;
        $data = $this->$model->get_gifts_name();
        echo json_encode($data);
    }
    function is_agent_exist()
    {
        $acc_model = self::ACC_MODEL;
        $agent_code = $_POST['agent_code'];
        $agent = $this->$acc_model->is_agent_exist($agent_code);
        echo json_encode($agent);
    }
    public function update_gift_status()
    {
        $acc_model = self::ACC_MODEL;
        $sch_id = $_POST['id_sch_acc'];
        $data = array('status' => 2);
        $gift = $this->$acc_model->update_gift_issued($data, $sch_id);
        echo json_encode($gift);
    }
    public function chit_detail_report($id_scheme_account)
    { //RHR-new	
        $model = self::ACC_MODEL;
        $set = self::SET_MODEL;
        $data['account'] = $this->$model->get_close_account($id_scheme_account);
        $data['sch'] = $this->$model->get_chit_data($id_scheme_account);
        //set bebefit and deduction calculation type based on scheme settings...//RHR
        if ($data['account']['is_digi'] == 1) {
            $data['account']['calculate_by'] = 1; // benefit and deduction based on digi gold formula
        } else if ($data['account']['calculation_type'] == 2 && $data['account']['maturity_days'] > 0) {
            $data['account']['calculate_by'] = 2; // benefit and deduction based on maturity days formula
        } else {
            $data['account']['calculate_by'] = 0; // common calculation for benefit and deduction 
        }
        if ($this->branch_settings == 1) {
            $data['comp_details'] = $this->$set->get_branchcompany($data['sch']['id_branch']);
        } else {
            $data['comp_details'] = $this->$set->get_company();
        }
        if ($data['account']['calculate_by'] == 1) {   //for digi gold print
            $intData = $this->$model->get_chit_int($data['sch']);
            $intData['id_scheme_account'] = $data['sch']['id_scheme_account'];
            $data['interest'] = $intData;
            $data['payData'] = $this->$model->chit_detail_report($intData);
            $this->benefit_report_print('scheme/print/chit_detail_report', $data);
        } else {   //for all scheme type except digi scheme
            $data['benefit'] = $this->$model->getAccBenefitDeduction($data['account']);
            $interest_val = ($data['benefit']['interest_value'] != '' ? ($data['benefit']['interest_value'] == 0 ? 'INR ' . $data['benefit']['interest_value'] . '' : $data['benefit']['interest_value'] . ' %') : '');
            $data['benefit']['interest_val'] = $interest_val;
            if ($data['account']['is_limit_exceed'] == 0 && $data['account']['paid_installments'] >= $data['account']['total_installments']) {
                //take installment amount given in interest chart...
                $data['benefit']['type'] = '1';    // benefit for all payments
                $data['benefit']['interest_val'] = $this->$model->getBonusInsAmt($data['account']);
            } else {
                $data['benefit']['type'] = '2';    // benefit for all payments
            }
            $data['payData'] = $this->$model->chit_detail_report($data);
            //echo '<pre>';print_r($data);exit;
            $this->benefit_report_print('scheme/print/benefit_report', $data);
        }
    }
    function benefit_report_print($view, $data)
    {
        //RHR-new	
        $this->load->helper(array('dompdf', 'file'));
        $dompdf = new DOMPDF();
        $html = $this->load->view($view, $data, true);
        $dompdf->load_html($html);
        $dompdf->set_paper("a4", "portriat");
        $dompdf->render();
        while(ob_get_level()) ob_end_clean();
        $dompdf->stream("chit_detail.pdf", array('Attachment' => 0));
    }
    function digi_wallet_screen()
    {
        $model = self::ACC_MODEL;
        $id_scheme_account = $this->input->post('id_sch_acc');
        $data = $this->$model->get_chit_data($id_scheme_account);
        $intData = $this->$model->get_chit_int($data);
        echo json_encode($intData);
    }
    function accountRemarks()
    {
        $data['main_content'] = self::REP_VIEW . 'pending_collection_report';
        $this->load->view('layout/template', $data);
    }
    function getRemarkPayments()
    {
        $model = self::ACC_MODEL;
        $data = $this->$model->get_remark_data($_POST['from_date'], $_POST['to_date']);
        echo json_encode($data);
    }
    //webcam upload, #AD On:20-12-2022,Cd:CLin,Up:AB	
    public function base64ToFile($imgBase64)
    {
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgBase64)); // might not work on some systems, specify your temp path if system temp dir is not writeable
        $temp_file_path = tempnam(sys_get_temp_dir(), 'tempimg');
        file_put_contents($temp_file_path, $data);
        $image_info = getimagesize($temp_file_path);
        $imgFile = array(
            'name' => uniqid() . '.' . preg_replace('!\w+/!', '', $image_info['mime']),
            'tmp_name' => $temp_file_path,
            'size' => filesize($temp_file_path),
            'error' => UPLOAD_ERR_OK,
            'type' => $image_info['mime'],
        );
        return $imgFile;
    }
    // created by RK-13/12/2022
    /*
   public function sendotp_gift()
    {   
        //print_r($_POST['mobile']);
        $mob_no_len = $this->session->userdata('mob_no_len');
        $account = $_POST;
        $set_model=self::SET_MODEL;	
        $model=	self::ACC_MODEL;
        $pay_model =	self::PAY_MODEL;
        $this->comp = $this->$model->company_details();
        $data= $this->$pay_model->get_customer($_POST['id_cust']);
        //print_r($data);
        //print_r($mob_no_len);
        if(strlen(trim($_POST['mobile'])) == $mob_no_len)
        {
            $this->session->unset_userdata("OTP");
            $OTP = mt_rand(100001,999999);
            $this->session->set_userdata('OTP',$OTP);
            $account['otp_code'] = $OTP;
            $account['otp_gen_time'] = date("Y-m-d H:i:s");//echo "<pre>";print_r($account);echo "</pre>";//exit;
           // $service = $this->$set_model->get_service(25);
            $giftOTP_exp= $this->$pay_model->gift_expotp();
            $duration = $giftOTP_exp; // in seconds
              //$duration = 10; // in seconds
            //$message="Dear Customer your OTP for Gift issue is ".$OTP;
            $this->session->set_userdata('gift_OTP_expiry',time()+$duration);
            $status=$this->send_sms($_POST['mobile'],$message,'1007096226527802824');
            $message = "Dear ".$data['firstname'].", OTP for your giftissue is: "
            .$OTP.". Regards, SRI KRISHNA NAGAI MALIGAI.Will expire within ". $duration." Sec.";
            //echo json_encode($status);
            //return $status;
            //print_r($message);
            $otp['otp_gen_time'] = date("Y-m-d H:i:s");
            $otp['otp_code'] = $OTP;
            $data=array('result'=>3 ,'msg'=>'"OTP Sent Successfully','otp'=>$OTP);
            $ss=$this->$model->otp_insert($otp);
            echo json_encode($data);
        }
    }*/
    // public function sendotp_scheme_join()
    // {   
    // 	$mob_no_len = $this->session->userdata('mob_no_len');
    // 	$account = $_POST;
    // 	$set_model=self::SET_MODEL;	
    // 	$model=	self::ACC_MODEL;
    // 	$pay_model =	self::PAY_MODEL;
    // 	$this->comp = $this->$model->company_details();
    // 	$data= $this->$pay_model->get_customer($_POST['id_cust']);
    // 	if(strlen(trim($_POST['mobile'])) == $mob_no_len)
    // 	{
    // 		$this->session->unset_userdata("OTP_scheme_join");
    // 		$OTP = mt_rand(100001,999999);
    // 		$this->session->set_userdata('OTP_scheme_join',$OTP);
    // 		$account['otp_code'] = $OTP;
    // 		$account['otp_gen_time'] = date("Y-m-d H:i:s");//echo "<pre>";print_r($account);echo "</pre>";//exit;
    // 		$service = $this->$set_model->get_service(45);
    // 		$duration = 60; // in seconds
    // 		$this->session->unset_userdata('sche_join_otp_expiry');
    // 		$this->session->set_userdata('sche_join_otp_expiry',time()+$duration);
    // 		// $message = "Dear ".$data['firstname'].", OTP for your joining new scheme: "
    // 		// .$OTP.". Regards, SRI KRISHNA NAGAI MALIGAI.Will expire within ". $duration." Sec.";
    // 		$status=$this->send_sms($_POST['mobile'],$message,$service['dlt_te_id']);
    // 		$otp['otp_gen_time'] = date("Y-m-d H:i:s");
    // 		$otp['otp_code'] = $OTP;
    // 		$datas=array("result"=>4 ,"msg"=>"OTP Sent Successfully",'otp'=>$OTP);
    // 		$ss=$this->$model->otp_insert($otp);
    // 		echo json_encode($datas);  
    // 	}
    // }
    public function sendotp_scheme_join()
    {
        //  print_r($_POST); exit;
        $mob_no_len = $this->session->userdata('mob_no_len');
        $account = $_POST;
        $set_model = self::SET_MODEL;
        $model = self::ACC_MODEL;
        $pay_model = self::PAY_MODEL;
        $this->comp = $this->$model->company_details();
        $data = $this->$pay_model->get_customer($_POST['id_cust']);
        if (strlen(trim($_POST['mobile'])) == $mob_no_len) {
            $customer_data = [];
            $this->session->unset_userdata("OTP_scheme_join");
            $OTP = mt_rand(100001, 999999);
            $this->session->set_userdata('OTP_scheme_join', $OTP);
            $account['otp_code'] = $OTP;
            $account['otp_gen_time'] = date("Y-m-d H:i:s"); //echo "<pre>";print_r($account);echo "</pre>";//exit;
            $service = $this->$set_model->get_service(45);
            // print_r($service);exit;
            $customer_data['fname'] = $data['firstname'];
            $customer_data['otp'] = $OTP;
            $mobile = $data['mobile'];
            //print_r($customer_data);exit;
            //Generating Message content starts here
            $field_name = explode('@@', $service['sms_msg']);
            //print_r($field_name);exit;
            $sms_msg = $service['sms_msg'];
            for ($i = 1; $i < count($field_name); $i += 2) {
                $field = $field_name[$i];
                if (isset($customer_data[$field])) {
                    $sms_msg = str_replace("@@" . $field . "@@", $customer_data[$field], $sms_msg);
                }
            }
            //Generating Message content ends here
            $duration = 60; // in seconds
            // print_r($sms_msg);exit;/
            $this->session->unset_userdata('sche_join_otp_expiry');
            $this->session->set_userdata('sche_join_otp_expiry', time() + $duration);
            $status = $this->send_sms($mobile, $sms_msg, $service['dlt_te_id']);
            /*	$message = "Dear ".$data['firstname'].", OTP for your joining new scheme: "
                .$OTP.". Regards,	K LAKSHMANA ACHARI SON JEWELLERS PVT LTD ..otp Will expire within ". $duration." Sec.";*/
            $otp['otp_gen_time'] = date("Y-m-d H:i:s");
            $otp['otp_code'] = $OTP;
            $data = array('result' => 3, 'msg' => '"OTP Sent Successfully', 'otp' => $OTP);
            $ss = $this->$model->otp_insert($otp);
            echo json_encode($data);
        }
    }
    public function verifyotp_scheme_join()
    {
        $accountmodel = self::ACC_MODEL;
        $payment = self::PAY_MODEL;
        $otp = $this->input->post('otp');
        $data = $this->$accountmodel->select_otp($otp);
        if ($otp == $this->session->userdata('OTP_scheme_join')) {
            if (time() >= $this->session->userdata('sche_join_otp_expiry')) {
                $this->session->unset_userdata('OTP_scheme_join');
                $this->session->unset_userdata('sche_join_otp_expiry');
                $result = array('result' => 5, 'msg' => 'OTP has been expired');
            } else if ($otp == $this->session->userdata('OTP_scheme_join')) {
                $data['is_verified'] = '1';
                $data['verified_time'] = date("Y-m-d H:i:s");
                $status = $this->$accountmodel->otp_update_payment($data, $data['id_otp']);
                $result = array('result' => 1, 'msg' => 'OTP updated successfully');
            }
        } else {
            $result = array('result' => 6, 'msg' => 'Invalid OTP');
        }
        echo json_encode($result);
    }
    /*	public function verifyotp_gift()
        {
            $accountmodel=    self::ACC_MODEL;
                $payment =    self::PAY_MODEL;
                $otp  = $this->input->post('otp');
                $data = $this->$accountmodel->select_otp($otp);   
                if($otp==$this->session->userdata('OTP'))
                {
                    if(time() >= $this->session->userdata('gift_OTP_expiry'))
                {
                    $this->session->unset_userdata('OTP');
                    $this->session->unset_userdata('gift_OTP_expiry');
                    $result=array('result'=>5 ,'msg'=>'OTP has been expired');
                }
                else if($otp=$this->session->userdata('OTP'))
                {
                    $data['is_verified']    = '1';
                    $data['verified_time']= date("Y-m-d H:i:s");
                    $status=$this->$accountmodel->otp_update_payment($data,$data['id_otp']);
                    $result=array('result'=>1 ,'msg'=>'OTP updated successfully');
                }
                }
                else
                {
                    $result=array('result'=>6 ,'msg'=>'Invalid OTP');
                }
                echo json_encode($result);
        }*/
    /*	function get_gift_bystock()
        {
            $model=	self::ACC_MODEL;
            $data = $this->$model->get_gift_bystock();
            echo json_encode($data);
        }*/
    public function set_remarks_byid()
    {
        $scheme_id = $this->input->post('schemeid');
        $remark_data = $this->input->post('remarkdata');
        //print_r($remark_data);
        $model = self::ACC_MODEL;
        $this->$model->set_remarks_byid($scheme_id, $remark_data);
        $data['message'] = 'success';
        echo json_encode($data);
    }
    // coded by Jothika on 16-10-2025 [disable payment block]
    public function blk_payment_byid()
    {
        $scheme_id = $this->input->post('schemeid');
        $stop_pay = $this->input->post('stop_payment');
        $reason = $this->input->post('reasondata');
        // print_r($_POST);
        $model = self::ACC_MODEL;
        $result = $this->$model->blk_payment_byid($scheme_id, $stop_pay, $reason);
        if ($result > 0) {
            $data['message'] = 'success';
        } else {
            $data['message'] = 'failure';
        }
        echo json_encode($data);
    }
    function update_sche_acc_sts()
    {
        $model = self::ACC_MODEL;
        $sch_data = $_POST['sch_data'];
        $update_status = $this->$model->update_schacc_status($sch_data);
        echo json_encode($update_status);
    }
    /*CHIT INVENTORY BASED GIFT MODULE STARTS....*/
    // created by RK-13/12/2022
    public function sendotp_gift()
    {
        //print_r($_POST['mobile']);
        $mob_no_len = $this->session->userdata('mob_no_len');
        $account = $_POST;
        $set_model = self::SET_MODEL;
        $model = self::ACC_MODEL;
        $pay_model = self::PAY_MODEL;
        $this->comp = $this->$model->company_details();
        $data = $this->$pay_model->get_customer($_POST['id_cust']);
        //print_r($data);
        //print_r($mob_no_len);
        if (strlen(trim($_POST['mobile'])) == $mob_no_len) {
            $this->session->unset_userdata("OTP");
            $OTP = mt_rand(100001, 999999);
            $this->session->set_userdata('OTP', $OTP);
            $account['otp_code'] = $OTP;
            $account['otp_gen_time'] = date("Y-m-d H:i:s"); //echo "<pre>";print_r($account);echo "</pre>";//exit;
            // $service = $this->$set_model->get_service(25);
            $giftOTP_exp = $this->$pay_model->gift_expotp();
            $duration = $giftOTP_exp; // in seconds
            //$duration = 10; // in seconds
            //$message="Dear Customer your OTP for Gift issue is ".$OTP;
            $this->session->set_userdata('gift_OTP_expiry', time() + $duration);
            $ser_model = self::SET_MODEL;
            $company = $this->$ser_model->get_company();
            //	$message = "Dear valued customer, Your OTP ".$OTP." for saving scheme is valid till ". $duration.". For queries contact customer care ".$company['mobile'].". Regards, ".$company['company_name'].".";
            $message = "Dear " . $data['firstname'] . ", OTP for Gift Issue is " . $OTP . ". Regards, KARPGAM JEWELS.";
            $status = $this->send_sms($_POST['mobile'], $message, '1307166764288938594');
            //echo json_encode($message);exit;
            //return $status;
            //print_r($message);
            $otp['otp_gen_time'] = date("Y-m-d H:i:s");
            $otp['otp_code'] = $OTP;
            $data = array('result' => 3, 'msg' => '"OTP Sent Successfully', 'otp' => $OTP);
            $ss = $this->$model->otp_insert($otp);
            echo json_encode($data);
        }
    }
    public function verifyotp_gift()
    {
        $accountmodel = self::ACC_MODEL;
        $payment = self::PAY_MODEL;
        $otp = $this->input->post('otp');
        $data = $this->$accountmodel->select_otp($otp);
        if ($otp == $this->session->userdata('OTP')) {
            if (time() >= $this->session->userdata('gift_OTP_expiry')) {
                $this->session->unset_userdata('OTP');
                $this->session->unset_userdata('gift_OTP_expiry');
                $result = array('result' => 5, 'msg' => 'OTP has been expired');
            } else if ($otp = $this->session->userdata('OTP')) {
                $data['is_verified'] = '1';
                $data['verified_time'] = date("Y-m-d H:i:s");
                $status = $this->$accountmodel->otp_update_payment($data, $data['id_otp']);
                $result = array('result' => 1, 'msg' => 'OTP updated successfully');
            }
        } else {
            $result = array('result' => 6, 'msg' => 'Invalid OTP');
        }
        echo json_encode($result);
    }
    function get_gift_bystock()
    {
        $model = self::ACC_MODEL;
        $data = $this->$model->get_gift_bystock();
        echo json_encode($data);
    }
    //Adding scheme map for gift code starts
    function get_gift_account()
    {
        $model = self::ACC_MODEL;
        $data = $this->$model->get_gift_account();
        echo json_encode($data);
    }
    function save_giftissued()
    {
        /*echo '<pre>'; print_r($_POST['gift']);exit;*/
        $model = self::ACC_MODEL;
        $id_scheme_account = $this->input->post('id_scheme_account');
        $login_branch = $this->session->userdata('id_branch');
        $id_branch = $this->input->post('id_branch');
        /*$id_gift=$this->input->post('id_gift');
        $gift_name=$this->input->post('gift_name');
        $gift_amount=$this->input->post('gift_amount');
        $item_ref_no=$this->input->post('item_ref_no');*/
        //echo '<pre>';print_r($_POST);exit;
        $gift_data = $_POST['gift'];
        foreach ($gift_data as $gift) {
            if ($gift['ref_no'] != '') {
                $ref_no = $gift['ref_no'];
            } else {
                //get ref no by id gift
                //".($login_branch > 0 ? 'and current_branch='.$login_branch : '')."
                $ref_no = $this->db->query("SELECT item_ref_no FROM ret_other_inventory_purchase_items_details 
		                                where status = 0  and other_invnetory_item_id = " . $gift['id_gift'] . " 
		                                " . ($login_branch > 0 ? 'and current_branch=' . $login_branch : '') . "
		                                order by pur_item_detail_id ASC limit 1")->row()->item_ref_no;
            }
            $data = array(
                'type' => 1,
                'id_gift' => $gift['id_gift'],
                'gift_desc' => $gift['gift_name'],
                'item_ref_no' => $ref_no,
                'gift_amount' => $gift['gift_amount'],
                'status' => 1,
                'quantity' => 1,
                'id_scheme_account' => $id_scheme_account,
                'id_employee' => $this->session->userdata('uid'),
                'id_branch' => $login_branch > 0 ? $login_branch : $id_branch,
                'date_issued' => date("Y-m-d H:i:s"),
                'paid_installments' => $_POST['paid_installments'],
            );
            //print_r($data);exit;
            $insert_gift = $this->$model->insert_gift_issued($data);
            if ($insert_gift) {
                $logData = array(
                    'item_id' => $gift['id_gift'],
                    'no_of_pieces' => 1,
                    'amount' => $gift['gift_amount'],
                    'date' => date("Y-m-d H:i:s"),
                    'status' => 1,
                    'from_branch' => $login_branch > 0 ? $login_branch : $id_branch,
                    'to_branch' => $login_branch > 0 ? $login_branch : $id_branch,
                    'created_on' => date("Y-m-d H:i:s"),
                    'created_by' => $this->session->userdata('uid'),
                );
                //	echo '<pre>';print_r($logData);exit;
                $this->$model->insertData($logData, 'ret_other_inventory_purchase_items_log');
                $gift_data = array(
                    'status' => 1
                );
                $this->$model->updateData($gift_data, 'item_ref_no', $ref_no, 'ret_other_inventory_purchase_items_details');
            }
        }
        //	print_r($this->db->last_query());exit;
        echo json_encode($insert_gift);
    }
    public function get_gift_issued_byaccount()
    {
        $model = self::ACC_MODEL;
        $id = $this->input->get('id_scheme_account');
        $data = $this->$model->get_gift_issued_byaccount($id);
        //print_r($this->db->last_query());exit;
        echo json_encode($data);
    }
    public function get_gift_validation()
    {
        $model = self::ACC_MODEL;
        $validation_data = $this->$model->get_gift_validation();
        echo json_encode($validation_data);
    }
    //Adding scheme map for gift code ends
    public function get_gifts_from_inv()
    {
        $model = self::ACC_MODEL;
        $gifts = $this->$model->gifts_from_inv();
        echo json_encode($gifts);
    }
    public function gift_issue_form()
    {
        $data['setting'] = $this->admin_settings_model->settingsDB('get', '', '');
        //  echo '<pre>';print_r($data['setting'][0]['isOTPReqToGift']);exit;
        $data['main_content'] = self::ACC_VIEW . 'gift_issue_form';
        $this->load->view('layout/template', $data);
    }
    function cancel_giftissued()
    {
        $model = self::ACC_MODEL;
        $id_gift_issued = $this->input->post('deduct_id_gift_issued');
        $txt_deduct_remarks = $this->input->post('txt_deduct_remarks');
        $deduct_id_employee = $this->input->post('deduct_id_employee');
        $login_branch = $this->session->userdata('id_branch');
        $gift = $this->$model->get_gift_issued_byID($id_gift_issued);
        $gift_data = array(
            'status' => 2,  //deducted
            'deducted_date' => date('Y-m-d H:i:s'),
            'deducted_by' => $deduct_id_employee,
            'deduct_remark' => $txt_deduct_remarks
        );
        $cancel_gift = $this->$model->updateData($gift_data, 'id_gift_issued', $id_gift_issued, 'gift_issued');
        $login_branch = $this->session->userdata('id_branch');
        $id_branch = $this->input->post('id_branch');
        if ($cancel_gift) {
            $logData = array(
                'item_id' => $gift[0]['id_gift'],
                'no_of_pieces' => 1,
                'amount' => $gift[0]['gift_amount'],
                'date' => date("Y-m-d H:i:s"),
                'status' => 0,     //1 -outward,  0- inward
                'from_branch' => $gift[0]['id_branch'],
                'to_branch' => $gift[0]['id_branch'],
                'created_on' => date("Y-m-d H:i:s"),
                'created_by' => $this->session->userdata('uid'),
            );
            $this->$model->insertData($logData, 'ret_other_inventory_purchase_items_log');
            $gift_data = array(
                'status' => 0
            );
            $ref_no = $gift[0]['item_ref_no'];
            if (!empty($ref_no)) {
                $this->$model->updateData($gift_data, 'item_ref_no', $ref_no, 'ret_other_inventory_purchase_items_details');
            } else {
                $sql = $this->db->query("UPDATE `ret_other_inventory_purchase_items_details` SET `status` = '0' WHERE other_invnetory_item_id = " . $gift[0]['id_gift'] . " order by pur_item_detail_id DESC LIMIT 1");
            }
        }
        //	print_r($this->db->last_query());exit;
        echo json_encode($cancel_gift);
    }
    public function getGiftByRef()
    {
        $model = self::ACC_MODEL;
        $gifts = $this->$model->getGiftByRef();
        echo json_encode($gifts);
    }
    /*GIFT MODULE ENDS*/
    function getBranchDetails()
    {
        $set_model = self::SET_MODEL;
        $data = $this->$set_model->getBranchDetails();
        echo json_encode($data);
    }
    // API function to get employee by br	anch
    function getEmployeeByBranch()
    {
        $branchId = $this->input->post('id_branch');
        $empModel = self::EMP_MODEL;
        $data = $this->$empModel->getEmployeeByBranch($branchId);
        echo json_encode($data);
    }
    //RAHUL 24_11_25
    function calculateAllowBenefit($account, $cur_date)
    {
        // Convert all to date format (without time)
        $cur_date_only = date('Y-m-d', strtotime($cur_date));
        $maturity_date_only = date('Y-m-d', strtotime($account['maturity_date']));
        //1 - installment ,2 - maturity
        if ($account['calculation_type'] == 1) {
            if ($account['paid_installments'] >= $account['total_installments'] && $account['pending_installments'] == 0) {
                return 1;
            }
        } elseif ($account['calculation_type'] == 2) {
            // Check if maturity date crossed
            if ($cur_date_only > $maturity_date_only) {
                return 1;
            }
        }
        // Check pre-close conditions
        return $this->isPreCloseEligible($account, $cur_date_only);
        // return $this->checkPreCloseThreshold($account, $cur_date_only);
    }
    function isPreCloseEligible($account, $cur_date_only)
    {
        return (($account['allow_preclose'] == 1 &&
            $account['preclose_months'] >= 1 &&
            $account['preclose_benefits'] == 1) ? $this->checkPreCloseThreshold($account, $cur_date_only) : 0);
    }
    function checkPreCloseThreshold($account, $cur_date_only)
    {
        if ($account['preclose_type'] == 1) {
            // Installment-based
            return ($account['paid_installments'] >= $account['preclose_months']) ? 1 : 0;
        } else {
            // Days-based pre-close - convert start_date to date only and add days
            $start_date_only = date('Y-m-d', strtotime($account['start_date']));
            $preclose_date = date('Y-m-d', strtotime($start_date_only . ' + ' . $account['preclose_months'] . ' days'));
            // Compare dates directly
            return ($cur_date_only > $preclose_date) ? 1 : 0;
        }
    }
    function insertEmployeeIncentive($refdata, $id_scheme_account, $id_payment)
    {
        $model = self::PAY_MODEL;
        $model_name = self::WALL_MODEL;
        $status = FALSE;
        $chkreferral = $this->$model->get_referral_code($id_scheme_account);
        $data = array();
        $checkCreditExist = $this->$model->checkCreditTransExist($id_scheme_account, $id_payment);
        if ($checkCreditExist == 0) {
            if ($chkreferral['referal_code'] != '' && $chkreferral['is_refferal_by'] == 1) {
                $data = $this->$model->get_empreferrals_datas($id_scheme_account);
            }
        }
        if (!empty($data) && $chkreferral['is_refferal_by'] == 1 && $checkCreditExist == 0) {
            if ($data['referal_code'] != '' && $refdata['referal_amount'] != '' && $data['id_wallet_account'] != '' && $data['id_wallet_account'] > 0) {
                // insert wallet transaction data //
                $wallet_data = array(
                    'id_wallet_account' => $data['id_wallet_account'],
                    'id_sch_ac' => $id_scheme_account,
                    'date_transaction' => date("Y-m-d H:i:s"),
                    'id_employee' => $data['idemployee'],
                    'transaction_type' => 0,
                    'value' => $refdata['referal_amount'],
                    'id_payment' => $id_payment,
                    'credit_for' => $refdata['credit_remark'],
                    'description' => 'Referral Benefits - ' . $data['cusname'] . ''
                );
                //	echo"<pre>"; print_r($wallet_data);exit;
                $status = $this->$model_name->wallet_transactionDB('insert', '', $wallet_data);
            }
        }
        return true;
    }
    function customerIncentive($refdata, $id_scheme_account, $id_payment)
    {
        $cusmodel = self::CUS_MODEL;
        $model_name = self::WALL_MODEL;
        $model = self::PAY_MODEL;
        $chkreferral = $this->$model->get_referral_code($id_scheme_account);
        //credit customer introduce staff incentive
        if ($chkreferral['is_refferal_by'] == 0) {
            // customer referal - multiple  
            $this->$cusmodel->update_customer_only(array('is_refbenefit_crt_cus' => 1), $chkreferral['id_customer']);
            //check customer 
            $isEmpRef = $this->$model->get_empRefExist_datas($id_scheme_account);
            //echo '<pre>';print_r($isEmpRef);exit;
            if (sizeof($isEmpRef) > 0) {
                //print_r($refdata);exit;
                if ($refdata['referal_amount'] != '' && $isEmpRef['id_wallet_account'] != null) {
                    // insert wallet transaction data //
                    $wallet_data = array(
                        'id_wallet_account' => $isEmpRef['id_wallet_account'],
                        // 'id_sch_ac' => NULL,
                        'id_sch_ac' => $id_scheme_account,
                        'date_transaction' => date("Y-m-d H:i:s"),
                        'id_employee' => $this->session->userdata('uid'),
                        'transaction_type' => 0,
                        'value' => $refdata['referal_amount'],
                        'id_payment' => $id_payment,
                        'credit_for' => 'Customer Intro Scheme Incentive',
                        'description' => 'Customer Intro Referral Benefits - ' . $isEmpRef['cusname'] . ''
                    );
                    //	echo"<pre>"; print_r($wallet_data);exit;
                    $status = $this->$model_name->wallet_transactionDB('insert', '', $wallet_data);
                    //echo $this->db->last_query();exit;
                }
            }
        }
    }

}
