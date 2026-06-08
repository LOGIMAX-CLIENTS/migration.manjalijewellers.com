<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once(APPPATH . 'libraries/techprocess/TransactionRequestBean.php');
require_once(APPPATH . 'libraries/techprocess/TransactionResponseBean.php');
require_once(APPPATH . 'libraries/hdfc.php');
require_once(APPPATH . 'libraries/dompdf/autoload.inc.php');
use Dompdf\Dompdf;
class Admin_payment extends CI_Controller
{
    const PAY_MODEL = 'payment_model';
    const PAY_VIEW = "payment/";
    const SET_VIEW = "scheme/settlement/";
    const API_MODEL = 'syncapi_model';
    const CHITAPI_MODEL = 'chitapi_model';
    const ACC_MODEL = 'account_model';
    const SET_MODEL = 'admin_settings_model';
    const SMS_MODEL = 'admin_usersms_model';
    const ADM_MODEL = "chitadmin_model";
    const LOG_MODEL = "log_model";
    const MAIL_MODEL = "email_model";
    const WALL_MODEL = "Wallet_model";
    const WALL_API_MOD = 'sync_walletapi_model';
    const CUS_MODEL = 'customer_model';
    const CHIT_MODEL = 'chitadmin_model';
    function __construct()
    {
        parent::__construct();
        ini_set('date.timezone', 'Asia/Calcutta');
        $this->load->helper('metal_wgt_digit');
        $this->load->model(self::PAY_MODEL);
        $this->load->model(self::API_MODEL);
        $this->load->model(self::CHITAPI_MODEL);
        $this->load->model(self::WALL_API_MOD);
        $this->load->model(self::ACC_MODEL);
        $this->load->model(self::SET_MODEL);
        $this->load->model(self::SMS_MODEL);
        $this->load->model(self::ADM_MODEL);
        $this->load->model(self::LOG_MODEL);
        $this->load->model(self::MAIL_MODEL);
        $this->load->model(self::WALL_MODEL);
        $this->load->model(self::CUS_MODEL);
        $this->load->model(self::CHIT_MODEL);
        $this->load->model("sms_model");
        $this->load->model("digigold_modal");
        $this->load->model("chit_series_model");
        $this->load->model('chit_transaction_model');     //AB-2407
        $this->employee = $this->session->userdata('uid');
        $this->company = $this->admin_settings_model->get_company();
        $this->branch_settings = $this->session->userdata('branch_settings');
        if (!$this->session->userdata('is_logged')) {
            redirect('admin/login');
        }
        $this->id_log = $this->session->userdata('id_log');
        $this->payment_status = array(
            'pending' => 7,
            'awaiting' => 2,
            'success' => 1,
            'failure' => 3,
            'cancel' => 4,
            'refund' => 6
        );
        $this->log_dir = 'log/' . date("Y-m-d");
        if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0777, TRUE);
        }
    }
    function ajax_form_data($id = "")
    {
        $acc = self::ACC_MODEL;
        $set = self::SET_MODEL;
        $pay = self::PAY_MODEL;
        //	$data['account']=$this->$acc->getAmountSchemeAccounts(($id!=NULL?$id:''));
        $data['mode'] = $this->$set->paymodeDB('get', ($id != NULL ? $id : ''));
        $data['bank'] = $this->$set->bankDB('get', ($id != NULL ? $id : ''));
        $data['payment_status'] = $this->$pay->get_payment_status();
        $data['drawee'] = $this->$set->draweeDB('get', ($id != NULL ? $id : ''));
        echo json_encode($data);
    }
    public function get_settings()
    {
        $sql = "select * from chit_settings";
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    function generate_receipt_no($id_scheme, $branch)
    {
        $model = self::PAY_MODEL;
        $rcpt_no = "";
        $rcpt = $this->$model->get_receipt_no($id_scheme, $branch);
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
        //print_r($rcpt_no);exit;
        return $rcpt_no;
    }
    function ajax_account_detail($id, $date = '')
    {
        $model = self::PAY_MODEL;
        // print_r($id);exit;
        $data['account'] = $this->$model->get_paymentContent($id, $date);
        //echo "<pre>"; print_r($data); echo "<pre>";exit;
        echo json_encode($data);
    }
    function ajax_payment_status()
    {
        $model = self::PAY_MODEL;
        $data['payment_status'] = $this->$model->get_payment_status();
        echo json_encode($data);
    }
    function ajax_customer_schemes($id_customer)
    {
        $model = self::PAY_MODEL;
        $data['accounts'] = $this->$model->get_customer_schemes($id_customer);
        //print_r($this->db->last_query());exit;
        $data['wallet_balance'] = $this->$model->wallet_balance($id_customer);
        //echo "<pre>";print_r($data);echo "</pre>";exit;		
        echo json_encode($data);
    }
    function ajax_customer_schemes_amount($id_customer)
    {
        $model = self::PAY_MODEL;
        $data = $this->$model->get_customer_schemes_amount($id_customer);
        echo json_encode($data);
    }
    function ajax_payment_stat()
    {
        $model = self::PAY_MODEL;
        $payment_stat = $this->$model->total_payments();
        echo json_encode($payment_stat);
    }
    function ajax_payment_range()
    {
        $model = self::PAY_MODEL;
        $set_model = self::SET_MODEL;
        $access = $this->$set_model->get_access('payment/list');
        if (!empty($_POST)) {
            $range['from_date'] = $this->input->post('from_date');
            $range['to_date'] = $this->input->post('to_date');
            $range['type'] = $this->input->post('type');
            $range['limit'] = $this->input->post('limit');
            $items = $this->$model->payment_list_range($range['from_date'], $range['to_date'], $range['type'], $range['limit'], '');
        } else {
            $items = $this->$model->payment_list(($id != NULL ? $id : ''), 10);
        }
        $payment = array(
            'access' => $access,
            'data' => $items
        );
        echo json_encode($payment);
    }
    public function ajax_get_scheme()
    {
        $id_arr = $this->input->post('id_scheme');
        $data = array();
        $model = self::PAY_MODEL;
        foreach ($id_arr as $id) {
            $result = $this->$model->get_payment_by_scheme($id);
            $data = array_merge($result, $data);
        }
        echo json_encode($data);
    }
    function ajax_postpayment_data()
    {
        $model = self::PAY_MODEL;
        $detail = $this->input->post('payment');
        //$data['payments'] = $this->$model->pdc_report_detail($detail['filter'],strtoupper($detail['mode']),$detail['status']);
        $data['payments'] = $this->$model->pdc_detail_all($detail['status']);
        $data['payment_status'] = $this->$model->get_payment_status();
        echo json_encode($data);
    }
    function postdate_payment_form($type = "", $id)
    {
        $model = self::PAY_MODEL;
        $sms_model = self::SMS_MODEL;
        $set_model = self::SET_MODEL;
        switch ($type) {
            case 'Edit':
                $data['payment'] = $this->$model->postdated_paymentDB('get', $id, "");
                $data['main_content'] = self::PAY_VIEW . "postdated/entry_form";
                $this->load->view('layout/template', $data);
                break;
            case 'Update':
                $payment = $this->input->post('payment');
                $pay_array = array(
                    'charges' => (isset($payment['charges']) ? $payment['charges'] : 0),
                    'payment_status' => (isset($payment['payment_status']) ? $payment['payment_status'] : 7)
                );
                if ($pay_array['payment_status'] == 2) {
                    $pay_array['date_presented'] = (isset($payment['date_presented']) ? date('Y-m-d', strtotime(str_replace("/", "-", $payment['date_presented']))) : NULL);
                } else {
                    $pay_array['date_presented'] = NULL;
                }
                $status = $this->$model->postdated_paymentDB("update", $id, $pay_array);
                if ($status) {
                    if ($pay_array['payment_status'] == 1) {
                        $acdata = $this->$model->isAcnoAvailable($payment['id_scheme_account']);
                        $scheme_acc_no = $this->$set_model->accno_generatorset();
                        if ($acdata['status'] && ($scheme_acc_no['status'] == 1 && $scheme_acc_no['schemeacc_no_set'] == 0)) {
                            // Lucky draw - Update Group code in scheme_account table based on settings
                            $ac_group_code = NULL;
                            if ($acdata['is_lucky_draw'] == 1) {
                                // Update Group code in scheme_account table 
                                $updCode = $this->payment_model->updateGroupCode($payment['id_scheme_account']);
                                $ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
                            }
                            $scheme_acc_number = $this->account_model->account_number_generator($acdata['id_scheme'], $acdata['branch'], $ac_group_code);
                            if ($scheme_acc_number != NULL) {
                                $updateData['scheme_acc_number'] = $scheme_acc_number;
                            }
                            $updSchAc = $this->account_model->update_account($updateData, $payment['id_scheme_account']);
                        }
                    }
                    $pay_status_array = array(
                        'id_post_payment' => (isset($status['updateID']) ? $status['updateID'] : NULL),
                        'id_status_msg' => (isset($pay_array['payment_status']) ? $pay_array['payment_status'] : NULL),
                        'charges' => (isset($pay_array['charges']) ? $pay_array['charges'] : NULL),
                        'id_employee' => $this->session->userdata('uid'),
                        'date_upd' => date('Y-m-d H:i:s')
                    );
                    $ppsm = $this->$model->payment_statusDB("insert", "", $pay_status_array);
                    $this->session->set_flashdata('chit_alert', array('message' => 'Payment updated successfully', 'class' => 'success', 'title' => 'Post Dated Payment'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Scheme Payment'));
                }
                $check_status = $this->$model->postdated_paymentDB("get", $id);
                if ($pay_array['payment_status'] != 1) {
                    if ($check_status['id_payment_status'] != $pay_array['payment_status']) {
                        $serviceID = 6;
                        $service = $this->$set_model->get_service($serviceID);
                        $mail_model = self::MAIL_MODEL;
                        $company = $this->$set_model->get_company();
                        $id = $status['updateID'];
                        $data = $this->$sms_model->get_SMS_data($serviceID, '', $id);
                        $mobile = $data['mobile'];
                        $message = $data['message'];
                        if ($service['serv_sms'] == 1) {
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
                            $smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $params];
                            $this->sms_model->send_whatsApp_message($mobile, $smsData);
                        }
                        $payData = $this->$model->getPostpayment_data($status['updateID']);
                        if ($service['serv_email'] == 1 && $payData['email'] != '') {
                            $data['payData'] = $payData;
                            $data['company_details'] = $company;
                            $data['type'] = 3;
                            $to = $payData['email'];
                            $subject = "Reg- " . $company['company_name'] . " saving scheme payment details";
                            $message = $this->load->view('include/emailPayment', $data, true);
                            $sendEmail = $this->$mail_model->send_email($to, $subject, $message);
                        }
                    }
                    $this->session->set_flashdata('chit_alert', array('message' => 'Post-dated Payment updated successfully', 'class' => 'success', 'title' => 'Post Dated Payment'));
                    redirect('postdated/payment/list');
                } else {
                    $pay = $this->$model->postdated_paymentByID($id);
                    // $receipt_no = $this->generate_receipt_no();
                    if ($this->$model->get_rptnosettings() == 0) {
                        $receipt_no = $this->generate_receipt_no();
                    } else {
                        $receipt_no;
                    }
                    $txnid = uniqid(time());
                    $ins_pay_array = array(
                        'id_scheme_account' => (isset($pay['id_scheme_account']) ? $pay['id_scheme_account'] : NULL),
                        'id_transaction' => $txnid,
                        'date_payment' => (isset($pay['date_payment']) ? date('Y-m-d', strtotime(str_replace("/", "-", $pay['date_payment']))) : NULL),
                        'payment_type' => "Manual",
                        'payment_mode' => (isset($pay['pay_mode']) ? $pay['pay_mode'] : NULL),
                        'payment_amount' => (isset($pay['amount']) ? $pay['amount'] : 0.00),
                        'metal_rate' => (isset($pay['metal_rate']) ? $pay['metal_rate'] : 0.00),
                        'metal_weight' => (isset($pay['weight']) ? $pay['weight'] : 0.000),
                        'payment_ref_number' => (isset($pay['payment_ref_number']) ? $pay['payment_ref_number'] : NULL),
                        'id_post_payment' => (isset($pay['id_post_payment']) ? $pay['id_post_payment'] : NULL),
                        'cheque_no' => (isset($pay['cheque_no']) ? $pay['cheque_no'] : NULL),
                        'bank_acc_no' => (isset($pay['payee_acc_no']) ? $pay['payee_acc_no'] : NULL),
                        'bank_name' => (isset($pay['payee_bank']) ? $pay['payee_bank'] : NULL),
                        'bank_branch' => (isset($pay['payee_branch']) ? $pay['payee_branch'] : NULL),
                        'bank_ifsc' => (isset($pay['payee_ifsc]']) ? $pay['payee_ifsc]'] : NULL),
                        'id_drawee' => (isset($pay['id_drawee']) ? $pay['id_drawee'] : NULL),
                        'remark' => (isset($pay['remark']) ? $pay['remark'] : NULL),
                        'payment_status' => (isset($pay['id_payment_status']) ? $pay['id_payment_status'] : 1),
                        'approval_date' => (isset($pay['id_payment_status']) == 1 ? date('Y-m-d H:i:s') : null),
                        'receipt_no' => ($pay['id_payment_status'] == 1 ? $receipt_no : NULL)
                    );
                    $status = $this->$model->paymentDB("insert", "", $ins_pay_array);
                    $serviceID = 6;
                    $service = $this->$set_model->get_service($serviceID);
                    $mail_model = self::MAIL_MODEL;
                    $company = $this->$set_model->get_company();
                    if ($status) {
                        if ($this->config->item('integrationType') == 1) {
                            $this->insert_common_data_jil($status['insertID']);
                        } else if ($this->config->item('integrationType') == 2) {
                            $this->insert_common_data($status['insertID']);
                        }
                        $pay_status_array = array(
                            'id_payment' => (isset($status['insertID']) ? $status['insertID'] : NULL),
                            'id_status_msg' => (isset($pay['id_payment_status']) ? $pay['id_payment_status'] : NULL),
                            'charges' => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                            'id_employee' => $this->session->userdata('uid'),
                            'date_upd' => date('Y-m-d H:i:s')
                        );
                        $ppsm = $this->$model->payment_statusDB("insert", "", $pay_status_array);
                        $payData = $this->$model->getPpayment_data($pay_status_array['id_payment']);
                        if ($ppsm['status'] == 1 && $check_status['id_payment_status'] != $pay_array['payment_status']) {
                            $id = $ins_pay_array['id_post_payment'];
                            $data = $this->$sms_model->get_SMS_data($serviceID, '', $id);
                            $mobile = $data['mobile'];
                            $message = $data['message'];
                            if ($service['serv_sms'] == 1) {
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
                                $smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $params];
                                $this->sms_model->send_whatsApp_message($mobile, $smsData);
                            }
                            if ($service['serv_email'] == 1 && $payData['email'] != '') {
                                $data['payData'] = $payData;
                                $data['company_details'] = $company;
                                $data['type'] = 3;
                                $to = $payData['email'];
                                $subject = "Reg- " . $company['company_name'] . " saving scheme payment details";
                                $message = $this->load->view('include/emailPayment', $data, true);
                                $sendEmail = $this->$mail_model->send_email($to, $subject, $message);
                            }
                        }
                        $this->session->set_flashdata('chit_alert', array('message' => 'Payment added successfully', 'class' => 'success', 'title' => 'Scheme Payment'));
                    } else {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Scheme Payment'));
                    }
                    redirect('payment/list');
                }
                break;
            case 'Status':
                $data['payment'] = $this->$model->postdated_paymentDB('get', $id, "");
                $data['status_log'] = $this->$model->post_payment_log($id);
                $data['main_content'] = self::PAY_VIEW . "postdated/payment_status";
                $this->load->view('layout/template', $data);
                break;
        }
    }
    function payment($type, $id = "")
    {
        $model = self::PAY_MODEL;
        $set_model = self::SET_MODEL;
        $sms_model = self::SMS_MODEL;
        $log_model = self::LOG_MODEL;
        $accountmodel = self::ACC_MODEL;
        $cus_model = self::CUS_MODEL;
        switch ($type) {
            /* !!!! Chit general advance for all schemes !!!!  Date : 8/09/2023 By: Abi  */
            case 'general_advance':
                $pay = $this->input->post('pay');
                $generic = $this->input->post('generic');
                $payment_amount = $generic['payment_amount'];
                if ($payment_amount > 0) {
                    $form_secret = (isset($generic["form_secret"]) ? $generic["form_secret"] : '');
                    if (verify_form_secret($form_secret)) {
                        //	 print_r($_POST);exit;
                        // Multi-mode
                        $cus_pay_mode = $this->input->post('cus_pay_mode');
                        $card_pay_details = json_decode($cus_pay_mode['card_pay'], true);
                        $cheque_details = json_decode($cus_pay_mode['chq_pay'], true);
                        $net_banking_details = json_decode($cus_pay_mode['net_bank_pay'], true);
                        $adv_adj = json_decode($cus_pay_mode['adv_adj'], true);
                        $adv_adj_details = $adv_adj[0];
                        //$advan_amout = json_decode($pay['adv']['advance_muliple_receipt'][0],true);//Commented and replaced by Durga 19.05.2023
                        $advan_amout = json_decode($_POST['adv']['advance_muliple_receipt'][0], true);
                        //echo '<pre>'; print_r(sizeof($advan_amout));exit;
                        if ($cus_pay_mode['cash_payment'] > 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0) {
                            $payment_mode = 'CSH';
                        } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) > 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0) {
                            foreach ($card_pay_details as $card_pay) {
                                $mode = ($card_pay['card_type'] == 1 ? 'CC' : 'DC');
                                if ($payment_mode == '') {
                                    $payment_mode = $mode;
                                } else if ($payment_mode != $mode) {
                                    $payment_mode = 'MULTI';
                                }
                            }
                        } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) > 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0) {
                            $payment_mode = 'CHQ';
                        } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) > 0 && sizeof($advan_amout) == 0) {
                            $payment_mode = 'NB';
                        } else if (
                            ($cus_pay_mode['cash_payment'] > 0 && sizeof($card_pay_details) > 0) || ($cus_pay_mode['cash_payment'] > 0 && sizeof($cheque_details) > 0) ||
                            ($cus_pay_mode['cash_payment'] > 0 && sizeof($net_banking_details) > 0) || (sizeof($net_banking_details) > 0 && sizeof($cheque_details) > 0) ||
                            (sizeof($net_banking_details) > 0 && sizeof($card_pay_details) > 0) || (sizeof($cheque_details) > 0 && sizeof($card_pay_details) > 0) ||
                            ($cus_pay_mode['cash_payment'] > 0 && sizeof($advan_amout) > 0) || (sizeof($card_pay_details) > 0 && sizeof($advan_amout) > 0) ||
                            (sizeof($cheque_details) > 0 && sizeof($advan_amout) > 0) || (sizeof($net_banking_details) > 0 && sizeof($advan_amout) > 0)
                        ) {
                            $payment_mode = 'MULTI';
                        } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) > 0) {
                            $payment_mode = 'ADV_ADJ';
                        }
                        // CREATE LOG
                        if (!is_dir($this->log_dir . '/general_advance')) {
                            mkdir($this->log_dir . '/general_advance', 0777, true);
                        }
                        $log_path = $this->log_dir . '/general_advance/create_payment_' . date("Y-m-d") . '.txt';
                        $lg_data = "\n CP --" . date('Y-m-d H:i:s') . " -- : " . json_encode($_POST);
                        file_put_contents($log_path, $lg_data, FILE_APPEND | LOCK_EX);
                        $pdc = $this->input->post('pdc');
                        $submit_type = $this->input->post('type');
                        // echo"<pre>"; print_r($generic);
                        $otp = $this->session->userdata('pay_OTP');
                        $data = $this->$accountmodel->select_otp($otp);
                        $id_otp = (isset($data['id_otp']) ? $data['id_otp'] : NULL);
                        $send_notif = $this->$sms_model->check_noti_settings();
                        //print_r($send_notif);exit;
                        $cusData = $this->$accountmodel->get_customer_acc($generic['id_scheme_account']);
                        $used_wallet = FALSE;
                        $is_use_wallet = (isset($generic['is_use_wallet']) ? 1 : 0);
                        $redeemed_amount = 0.00;
                        $redeem_request = (isset($generic['redeem_request']) ? floor($generic['redeem_request']) : 0);
                        //	$amount =0;
                        $metal_wgt = 0;
                        //	$date_payment = (isset($generic['date_payment'])? date('Y-m-d h:i:s',strtotime(str_replace("/","-",$generic['date_payment']))):NULL);
                        $date_payment = (isset($generic['date_payment']) ? date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $generic['date_payment']))) : NULL);
                        $custom_entry_date = $cusData['custom_entry_date'];
                        $totalamount = ($amount * $generic['installments']);
                        // Wallet
                        $walletData = $this->$model->wallet_balance($generic['id_customer']);
                        if ($is_use_wallet == 1) {
                            $allowed_redeem = ($totalamount * ($walletData['redeem_percent'] / 100));
                            if ($allowed_redeem > $walletData['wal_balance']) {
                                $can_redeem = $walletData['wal_balance'];
                            } else {
                                $can_redeem = $allowed_redeem;
                            }
                            $used_wallet = TRUE;
                            $redeemed_amount = floor($redeem_request <= $can_redeem ? $redeem_request : $can_redeem);
                        }
                        for ($i = 1; $i <= $generic['installments']; $i++) {
                            $metal_wgt = '';
                            //echo '<pre>';print_r($generic);exit;     
                            /* !!!! GST & METAL WEIGHT CALCULATION START FOR INCLUSIVE, EXCLUSIVE WITH DISCOUNT IF APPLICABLE !!!!  */
                            $pay_amt = $payment_amount;   //15000
                            $gst = $generic['gst'];  // 3 %    
                            $gst_type = $generic['gst_type'];    // 1 - inclusive type
                            /*$disc_amt   = $generic['discountedAmt'];   // 250
                            //Subtracting the dicount amount from the total payment amount. So the actual amount received from customer arrived...
                            $actual_pay = ($disc_amt > 0 ?  ($pay_amt - $disc_amt) : $pay_amt);    // 15000 - 250 = 14750 */
                            $actual_pay = $pay_amt;
                            if ($gst_type == 1) {
                                //FOR EXCLUSIVE GST TYPE...
                                $gst_amt = $actual_pay * ($gst / 100); // 14750 * (3/100)  = 442.5
                            } else {
                                //FOR INCLUSIVE GST TYPE...
                                $gst_amt = $actual_pay - ($actual_pay * (100 / (100 + $gst))); // 14750 - (14750 * (100 / (100 + 3)))  = 429.61
                            }
                            //METAL WEIGHT CALC STARTS...
                            $metal_wgt = '';
                            if ($generic['fix_weight'] == 2) //scheme_type is assigned to fix weight
                            {
                                $amt = ($sch_data['gst_type'] == 0 ? ($generic['sch_amt'] - $gst_amt) : $generic['sch_amt']);
                                $data = array(
                                    'sch_amt' => $amt,
                                    'metal_rate' => $generic['metal_rate']
                                );
                                $metal_wgt = $this->amount_to_weight($data);
                            } else if ($generic['fix_weight'] == 3) // scheme type
                            {
                                $amt = ($sch_data['gst_type'] == 0 ? ($pay_amt - $gst_amt) : $pay_amt);
                                // for  inclusive ---> 15000 - 429.61 = 14571
                                // for  exclusive ---> 15000 
                                if ($generic['flexible_sch_type'] == 3 || $generic['flexible_sch_type'] == 4 || $generic['flexible_sch_type'] == 7 || $generic['flexible_sch_type'] == 8 || ($generic['flexible_sch_type'] == 2 && $generic['wgt_convert'] != 2) || ($generic['flexible_sch_type'] == 5 && $generic['wgt_store_as'] == 1)) {
                                    $data = array('sch_amt' => $amt, 'metal_rate' => $generic['metal_rate']);
                                    $metal_wgt = $this->amount_to_weight($data);
                                    // for  inclusive ---> (14571 / 5685) = 2.563 grams
                                    // for  exclusive ---> (15000 / 5685) = 2.638 grams
                                }
                            } else {
                                $metal_wgt = (isset($generic['metal_weight']) ? $generic['metal_weight'] : 0.000);
                            }
                            /* GST & METAL WEIGHT CALCULATION END */
                            if ($this->session->userdata('branch_settings') == 1) {
                                if ($this->session->userdata('is_branchwise_cus_reg') == 1 && $this->config->item('payOtherBranch') == 0) {
                                    $id_branch = $cusData['cus_reg_branch'];
                                } else if ($this->session->userdata('branchWiseLogin') == 1 && $this->config->item('payOtherBranch') == 0) {
                                    //$id_branch  = (isset($sch_acc['id_branch'])?$sch_acc['id_branch']:(isset($cusData['sch_join_branch'])?$cusData['sch_join_branch']: NULL));
                                    //old line commented and new line added by Durga 15.05.2023
                                    $id_branch = $cusData['branch'];
                                }
                                //line added by Durga 15.05.2023 starts here
                                else if ($this->config->item('payOtherBranch') == 0) {
                                    $id_branch = $cusData['branch'];
                                }
                                //line added by Durga 15.05.2023 ends here
                                else if ($this->session->userdata('branchWiseLogin') == 1 && $this->config->item('payOtherBranch') == 1 && $this->session->userdata('empLog_branch') != 'N')    //11-01-2023 #AB branch auto store based on emp log -->
                                {
                                    $id_branch = ($this->session->userdata('empLog_branch') != '' ? $this->session->userdata('empLog_branch') : NULL);
                                } else {
                                    $id_branch = ($generic['id_branch'] != '' ? $generic['id_branch'] : NULL);
                                }
                            } else {
                                $id_branch = NULL;
                            }
                            $custom_entry_date = $this->customer_model->get_entrydate($id_branch);    //pr day close code update 19/06/2023
                            $start_year = $this->$accountmodel->get_financialYear();
                            $this->db->trans_begin();
                            if ($this->$model->get_rptnosettings() == 1) {
                                $receipt_no = $this->generate_receipt_no($generic['id_scheme'], $id_branch, 'general_advance_payment');
                            } else {
                                $receipt_no = null;
                            }
                            $get_curPaid_insNo = $this->$model->get_curPaid_insNo($generic['id_scheme_account']);
                            $pay_array = array(
                                'gst_type' => (isset($generic['gst_type']) ? $generic['gst_type'] : 0),
                                'gst' => (isset($generic['gst']) ? $generic['gst'] : 0),
                                'id_scheme_account' => (isset($generic['id_scheme_account']) ? $generic['id_scheme_account'] : NULL),
                                'id_employee' => $this->session->userdata('uid'),
                                'date_payment' => $date_payment,
                                'custom_entry_date' => ($custom_entry_date['edit_custom_entry_date'] == 1 ? $custom_entry_date['custom_entry_date'] : NULL),       ////pr day close code update 19/06/2023
                                'id_branch' => $id_branch,
                                'due_type' => 'GA',
                                'payment_type' => $redeemed_amount == $totalamount && $is_use_wallet == 1 ? 'Wallet Payment' : (isset($generic['payment_type']) ? $generic['payment_type'] : NULL),
                                'payment_mode' => $redeemed_amount == $totalamount && $is_use_wallet == 1 ? 'Wallet' : $payment_mode,
                                'payment_amount' => $payment_amount,
                                'act_amount' => $amount,
                                'metal_rate' => (isset($generic['metal_rate']) ? $generic['metal_rate'] : 0),
                                'metal_weight' => $metal_wgt,
                                'payment_ref_number' => (isset($generic['payment_ref_number']) ? $generic['payment_ref_number'] : NULL),
                                'remark' => (isset($generic['remark']) ? $generic['remark'] : NULL),
                                'payment_status' => $redeemed_amount == $totalamount ? 1 : (isset($generic['payment_status']) ? $generic['payment_status'] : 1),
                                'receipt_no' => $receipt_no,
                                'cheque_no' => (isset($pdc['cheque_no']) ? $pdc['cheque_no'] : ''),
                                'cheque_date' => (isset($pdc['date_payment']) ? date('Y-m-d', strtotime(str_replace("/", "-", $pdc['date_payment']))) : NULL),
                                'bank_name' => (isset($pdc['payee_bank']) ? $pdc['payee_bank'] : ''),
                                'bank_branch' => (isset($pdc['payee_branch']) ? $pdc['payee_branch'] : ''),
                                'bank_IFSC' => (isset($pdc['payee_ifsc']) ? $pdc['payee_ifsc'] : ''),
                                'bank_acc_no' => (isset($pdc['payee_acc_no']) ? $pdc['payee_acc_no'] : ''),
                                'added_by' => 0,
                                'date_upd' => date('Y-m-d H:i:s'),
                                "receipt_year" => $start_year,
                                'form_secret' => (isset($generic['form_secret']) ? $generic['form_secret'] : NULL),
                                'installment' => $get_curPaid_insNo, // paying general advance for which main installment no will store here
                            );
                            $status = $this->$model->paymentDB("general_advance_insert", "", $pay_array);
                            if ($status) {
                                if ($cus_pay_mode['cash_payment'] > 0) {
                                    //Added by Durga 19.05.2023 starts here
                                    if ($generic['installments'] > 1) {
                                        $pay_amt = $cus_pay_mode['cash_payment'] / $generic['installments'];
                                    } else {
                                        $pay_amt = $cus_pay_mode['cash_payment'];
                                    }
                                    //Added by Durga 19.05.2023 ends here
                                    $arrayCashPay = array(
                                        'id_adv_payment' => $status['insertID'],
                                        //	'payment_amount'    => $cus_pay_mode['cash_payment'],//Commented and replaced by Durga 19.05.2023
                                        'payment_amount' => $pay_amt,
                                        'payment_mode' => 'CSH',
                                        'payment_status' => $generic['payment_status'],
                                        'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                        'created_time' => date("Y-m-d H:i:s"),
                                        'created_by' => $this->session->userdata('uid')
                                    );
                                    if (!empty($arrayCashPay)) {
                                        $cashPayInsert = $this->$model->insertData($arrayCashPay, 'general_advance_mode_detail');
                                    }
                                }
                                if (sizeof($card_pay_details) > 0) {
                                    $pay_mode_array = [];
                                    if ($generic['installments'] > 1) {
                                        $pay_amt = $card_pay_details[0]['card_amt'] / $generic['installments'];
                                        $pay_mode_array[] = array(
                                            'id_adv_payment' => $status['insertID'],
                                            'card_type' => $card_pay_details[0]['card_name'],
                                            'payment_amount' => $pay_amt,
                                            'payment_mode' => ($card_pay_details[0]['card_type'] == 1 ? 'CC' : 'DC'),
                                            'card_no' => ($card_pay_details[0]['card_no'] != '' ? $card_pay_details[0]['card_no'] : NULL),
                                            'payment_ref_number' => ($card_pay_details[0]['ref_no'] != '' ? $card_pay_details[0]['ref_no'] : NULL),
                                            'id_pay_device' => ($card_pay_details[0]['id_device'] != '' ? $card_pay_details[0]['id_device'] : NULL),
                                            'payment_status' => $generic['payment_status'],
                                            'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                            'created_time' => date("Y-m-d H:i:s"),
                                            'created_by' => $this->session->userdata('uid')
                                        );
                                        if (!empty($pay_mode_array)) {
                                            $cardPayInsert = $this->$model->insertBatchData($pay_mode_array, 'general_advance_mode_detail');
                                        }
                                    } else {
                                        foreach ($card_pay_details as $card_pay) {
                                            $arrayCardPay[] = array(
                                                'id_adv_payment' => $status['insertID'],
                                                'card_type' => $card_pay['card_name'],
                                                'payment_amount' => $card_pay['card_amt'],
                                                'payment_mode' => ($card_pay['card_type'] == 1 ? 'CC' : 'DC'),
                                                'card_no' => ($card_pay['card_no'] != '' ? $card_pay['card_no'] : NULL),
                                                'payment_ref_number' => ($card_pay['ref_no'] != '' ? $card_pay['ref_no'] : NULL),
                                                'id_pay_device' => ($card_pay['id_device'] != '' ? $card_pay['id_device'] : NULL),
                                                'payment_status' => $generic['payment_status'],
                                                'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                                'created_time' => date("Y-m-d H:i:s"),
                                                'created_by' => $this->session->userdata('uid')
                                            );
                                        }
                                        if (!empty($arrayCardPay)) {
                                            $cardPayInsert = $this->$model->insertBatchData($arrayCardPay, 'general_advance_mode_detail');
                                        }
                                    }
                                }
                                if (sizeof($cheque_details) > 0) {
                                    $pay_mode_array = [];
                                    if ($generic['installments'] > 1) {
                                        $pay_amt = $cheque_details[0]['payment_amount'] / $generic['installments'];
                                        $pay_mode_array[] = array(
                                            'id_adv_payment' => $status['insertID'],
                                            'payment_amount' => $pay_amt,
                                            'payment_status' => $generic['payment_status'],
                                            'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                            'cheque_date' => date("Y-m-d H:i:s"),
                                            'payment_mode' => 'CHQ',
                                            'bank_IFSC' => ($cheque_details[0]['bank_IFSC'] != '' ? $cheque_details[0]['bank_IFSC'] : NULL),
                                            'cheque_no' => ($cheque_details[0]['cheque_no'] != '' ? $cheque_details[0]['cheque_no'] : NULL),
                                            'bank_name' => ($cheque_details[0]['bank_name'] != '' ? $cheque_details[0]['bank_name'] : NULL),
                                            'bank_branch' => ($cheque_details[0]['bank_branch'] != '' ? $cheque_details[0]['bank_branch'] : NULL),
                                            'bank_IFSC' => ($cheque_details[0]['bank_IFSC'] != '' ? $$cheque_details[0]['bank_IFSC'] : NULL),
                                            'created_time' => date("Y-m-d H:i:s"),
                                            'created_by' => $this->session->userdata('uid')
                                        );
                                        if (!empty($pay_mode_array)) {
                                            $cardPayInsert = $this->$model->insertBatchData($pay_mode_array, 'general_advance_mode_detail');
                                        }
                                    } else {
                                        foreach ($cheque_details as $chq_pay) {
                                            $arraychqPay[] = array(
                                                'id_adv_payment' => $status['insertID'],
                                                'payment_amount' => $chq_pay['payment_amount'],
                                                'payment_status' => $generic['payment_status'],
                                                'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                                'cheque_date' => date("Y-m-d H:i:s"),
                                                'payment_mode' => 'CHQ',
                                                'bank_IFSC' => ($chq_pay['bank_IFSC'] != '' ? $chq_pay['bank_IFSC'] : NULL),
                                                'cheque_no' => ($chq_pay['cheque_no'] != '' ? $chq_pay['cheque_no'] : NULL),
                                                'bank_name' => ($chq_pay['bank_name'] != '' ? $chq_pay['bank_name'] : NULL),
                                                'bank_branch' => ($chq_pay['bank_branch'] != '' ? $chq_pay['bank_branch'] : NULL),
                                                'bank_IFSC' => ($chq_pay['bank_IFSC'] != '' ? $chq_pay['bank_IFSC'] : NULL),
                                                'created_time' => date("Y-m-d H:i:s"),
                                                'created_by' => $this->session->userdata('uid')
                                            );
                                        }
                                        if (!empty($arraychqPay)) {
                                            $chqPayInsert = $this->$model->insertBatchData($arraychqPay, 'general_advance_mode_detail');
                                        }
                                    }
                                }
                                if (sizeof($net_banking_details) > 0) {
                                    $pay_mode_array = [];
                                    if ($generic['installments'] > 1) {
                                        $pay_amt = $net_banking_details[0]['amount'] / $generic['installments'];
                                        $pay_mode_array[] = array(
                                            'id_adv_payment' => $status['insertID'],
                                            'payment_amount' => $pay_amt,
                                            'payment_status' => $generic['payment_status'],
                                            'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                            'payment_mode' => 'NB',
                                            'payment_ref_number' => ($net_banking_details[0]['ref_no'] != '' ? $net_banking_details[0]['ref_no'] : NULL),
                                            'net_banking_date' => ($net_banking_details[0]['nb_date'] != '' ? $net_banking_details[0]['nb_date'] : NULL),
                                            'NB_type' => ($net_banking_details[0]['nb_type'] != '' ? $net_banking_details[0]['nb_type'] : NULL),
                                            'id_pay_device' => ($net_banking_details[0]['nb_type'] == 3 ? $net_banking_details[0]['id_device'] : NULL),
                                            'id_bank' => ($net_banking_details[0]['nb_type'] == 1 || $net_banking_details[0]['nb_type'] == 2 ? $net_banking_details[0]['id_bank'] : NULL),
                                            'created_time' => date("Y-m-d H:i:s"),
                                            'created_by' => $this->session->userdata('uid')
                                        );
                                        if (!empty($pay_mode_array)) {
                                            $cardPayInsert = $this->$model->insertBatchData($pay_mode_array, 'general_advance_mode_detail');
                                        }
                                    } else {
                                        foreach ($net_banking_details as $nb_pay) {
                                            $arrayNBPay[] = array(
                                                'id_adv_payment' => $status['insertID'],
                                                'payment_amount' => $nb_pay['amount'],
                                                'payment_status' => $generic['payment_status'],
                                                'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                                'payment_mode' => 'NB',
                                                'payment_ref_number' => ($nb_pay['ref_no'] != '' ? $nb_pay['ref_no'] : NULL),
                                                'net_banking_date' => ($nb_pay['nb_date'] != '' ? $nb_pay['nb_date'] : NULL),
                                                'NB_type' => ($nb_pay['nb_type'] != '' ? $nb_pay['nb_type'] : NULL),
                                                'id_pay_device' => ($nb_pay['nb_type'] == 3 ? $nb_pay['id_device'] : NULL),
                                                'id_bank' => ($nb_pay['nb_type'] == 1 || $nb_pay['nb_type'] == 2 ? $nb_pay['id_bank'] : NULL),
                                                //'bank_acc_no'           =>($nb_pay['nb_type']==1 || $nb_pay['nb_type']==2? $nb_pay['acc_no']:NULL),
                                                'created_time' => date("Y-m-d H:i:s"),
                                                'created_by' => $this->session->userdata('uid')
                                            );
                                        }
                                        if (!empty($arrayNBPay)) {
                                            $NbPayInsert = $this->$model->insertBatchData($arrayNBPay, 'general_advance_mode_detail');
                                        }
                                    }
                                }
                                $advan_amout = $_POST['adv']['advance_muliple_receipt'][0];
                                if (count($advan_amout) > 0 && $advan_amout != '') {
                                    //	echo "<pre>";print_r($_POST);exit;echo "</pre>";
                                    $advance_amount_adj = json_decode($advan_amout);
                                    //print_r($advance_amount_adj);exit;
                                    $advance_amt = 0;
                                    $adv_adj_array = [];
                                    if ($generic['installments'] > 1) {
                                        $data_adv_amount = array(
                                            'id_issue_receipt' => $advance_amount_adj[0]->id_issue_receipt,
                                            'id_adv_payment' => $status['insertID'],
                                            'adjusted_for' => 2, // Adjusted in CRM
                                            'utilized_amt' => $advance_amount_adj[0]->adj_amount / $generic['installments'],
                                            'cash_utilized_amt' => $advance_amount_adj[0]->cash_pay,
                                        );
                                        //print_r($data_adv_amount);exit;
                                        $insId_adv_amount = $this->$model->insertData($data_adv_amount, 'ret_advance_utilized');
                                        $adv_adj_array = array(
                                            'id_adv_payment' => $status['insertID'],
                                            'payment_amount' => $advance_amount_adj[0]->adj_amount / $generic['installments'],
                                            'payment_status' => $generic['payment_status'],
                                            'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                            'payment_mode' => 'ADV_ADJ',
                                            'created_time' => date("Y-m-d H:i:s"),
                                            'created_by' => $this->session->userdata('uid')
                                        );
                                        $this->$model->insertData($adv_adj_array, 'general_advance_mode_detail');
                                    } else {
                                        foreach ($advance_amount_adj as $obj) {
                                            $advance_amt += $obj->adj_amount;
                                            $data_adv_amount = array(
                                                'id_issue_receipt' => $obj->id_issue_receipt,
                                                'id_adv_payment' => $status['insertID'],
                                                'adjusted_for' => 2, // Adjusted in CRM
                                                'utilized_amt' => $obj->adj_amount,
                                                'cash_utilized_amt' => $obj->cash_pay,
                                            );
                                            $insId_adv_amount = $this->$model->insertData($data_adv_amount, 'ret_advance_utilized');
                                            $array_adj_pay = array(
                                                'id_adv_payment' => $status['insertID'],
                                                'payment_amount' => $obj->adj_amount,
                                                'payment_status' => $generic['payment_status'],
                                                'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                                'payment_mode' => 'ADV_ADJ',
                                                'created_time' => date("Y-m-d H:i:s"),
                                                'created_by' => $this->session->userdata('uid')
                                            );
                                            $this->$model->insertData($array_adj_pay, 'general_advance_mode_detail');
                                        }
                                    }
                                }
                                //send sms/mail to Customer 
                                $mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";
                                $mailtype = 2;
                                $this->sendSMSMail('3', $payData, $mailSubject, $mailtype, $status['insertID']);
                            } else {
                                //send sms/mail to Customer 
                                //$payData =  $this->$model->getPpayment_data($status['insertID']);  
                                //$mailSubject = "Reg- ".$this->company['company_name']." saving scheme payment details";
                                // $mailtype= 3;
                                // $this->sendSMSMail('7',$payData,$mailSubject,$mailtype,$status['insertID']);
                            }
                        }
                        if ($this->db->trans_status() === TRUE) {
                            $this->db->trans_commit();
                            $this->session->set_flashdata('chit_alert', array('message' => 'Payment added successfully', 'class' => 'success', 'title' => 'Scheme Payment'));
                            $data['payid'] = $payid;
                            $data['type'] = $submit_type;
                            $data['payment_status'] = $generic['payment_status'];
                        } else {
                            $this->db->trans_rollback();
                        }
                    } else {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Unable To Proceed Your Request.Invalid Form Submit', 'class' => 'danger', 'title' => 'Scheme Payment'));
                    }
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Invalid Payment Amount, try again', 'class' => 'danger', 'title' => 'Scheme Payment'));
                }
                $model = self::PAY_MODEL;
                $id = $generic['id_scheme_account'];
                $payment_details = $this->$model->getPayDetails($id);
                $data['payment_details'] = $payment_details;
                echo json_encode($data);
                break;
            case 'List':
                //$data['payment']=$this->$model->menuDB('get',($id!=NULL?$id:''));
                $data['main_content'] = self::PAY_VIEW . "list";
                $this->load->view('layout/template', $data);
                break;
            case 'Edit_payment':
                $data = $this->$model->edit_payment($id);
                echo json_encode($data);
                break;
            case 'Update_payment':
                $pay = $_POST;
                //	print_r($pay);exit;
                $id = $pay['id_payment'];
                /* Array ( [prev_pay_mode] => CSH [prev_pay_status] => 1 [paymentstatus] => 1 [payment_date] => 2022-10-14 00:00:00 [metal_rate] => 4456.00 [metal_weight] => 0.000 
                [payment_mode] => CSH [cus_pay_mode] => Array ( [card_pay] => [chq_pay] => 
                [net_bank_pay] => [{"nb_type":"1","id_bank":"1","nb_date":"2022-10-16","amount":"1000","ref_no":"123123"}] 
                [cash_payment] => 16824 [adv_adj] => [order_adv_adj] => ) [adv] => Array ( [advance_muliple_receipt] => Array ( [0] => ) [excess_adv_amt] => Array ( [0] => ) ) 
                [remark] => aaaa ) 
                Array ( [0] => Array ( [nb_type] => 1 [id_bank] => 1 [nb_date] => 2022-10-16 [amount] => 1000 [ref_no] => 123123 ) )
                */
                $cus_pay_mode = $pay['cus_pay_mode'];
                $card_pay_details = json_decode($cus_pay_mode['card_pay'], true);
                $cheque_details = json_decode($cus_pay_mode['chq_pay'], true);
                $net_banking_details = json_decode($cus_pay_mode['net_bank_pay'], true);
                $adv_adj = json_decode($cus_pay_mode['adv_adj'], true);
                //voucher
                // $vch_details = json_decode(html_entity_decode($cus_pay_mode['vch_pay']),true);
                $vch_details = json_decode($cus_pay_mode['vch_pay'], true);
                $adv_adj_details = $adv_adj[0];
                $advan_amout = json_decode($pay['adv']['advance_muliple_receipt'][0], true);
                $payment_mode = "";
                if ($cus_pay_mode['cash_payment'] > 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) == 0) {
                    $payment_mode = 'CSH';
                } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) > 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) == 0) {
                    foreach ($card_pay_details as $card_pay) {
                        $mode = ($card_pay['card_type'] == 1 ? 'CC' : 'DC');
                        if ($payment_mode == '') {
                            $payment_mode = $mode;
                        } else if ($payment_mode != $mode) {
                            $payment_mode = 'MULTI';
                        }
                    }
                } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) > 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) == 0) {
                    $payment_mode = 'CHQ';
                } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) > 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) == 0) {
                    $payment_mode = 'NB';
                } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) > 0) {
                    $payment_mode = 'VCH';
                }
                /*	else if(($cus_pay_mode['cash_payment'] > 0 && sizeof($card_pay_details) > 0) || ($cus_pay_mode['cash_payment'] > 0 && sizeof($cheque_details) > 0) || 
                    ($cus_pay_mode['cash_payment'] > 0 && sizeof($net_banking_details) > 0) || (sizeof($net_banking_details) > 0 && sizeof($cheque_details) > 0) || 
                    (sizeof($net_banking_details) > 0 && sizeof($card_pay_details) > 0) || (sizeof($cheque_details) > 0 && sizeof($card_pay_details) > 0) || 
                    ($cus_pay_mode['cash_payment'] > 0 && sizeof($advan_amout) > 0) || (sizeof($card_pay_details) > 0 && sizeof($advan_amout) > 0) || 
                    (sizeof($cheque_details) > 0 && sizeof($advan_amout) > 0) || (sizeof($net_banking_details) > 0 && sizeof($advan_amout) > 0)){
                        $payment_mode = 'MULTI';
                    }*/ else if (
                    ($cus_pay_mode['cash_payment'] > 0 && sizeof($card_pay_details) > 0) ||
                    ($cus_pay_mode['cash_payment'] > 0 && sizeof($cheque_details) > 0) ||
                    ($cus_pay_mode['cash_payment'] > 0 && sizeof($cheque_details) > 0) ||
                    ($cus_pay_mode['cash_payment'] > 0 && sizeof($net_banking_details) > 0) ||
                    ($cus_pay_mode['cash_payment'] > 0 && sizeof($advan_amout) > 0) ||
                    ($cus_pay_mode['cash_payment'] > 0 && sizeof($vch_details) > 0) ||
                    (sizeof($net_banking_details) > 0 && sizeof($cheque_details) > 0) ||
                    (sizeof($net_banking_details) > 0 && sizeof($card_pay_details) > 0) ||
                    (sizeof($net_banking_details) > 0 && sizeof($advan_amout) > 0) ||
                    (sizeof($net_banking_details) > 0 && sizeof($vch_details) > 0) ||
                    (sizeof($cheque_details) > 0 && sizeof($card_pay_details) > 0) ||
                    (sizeof($cheque_details) > 0 && sizeof($advan_amout) > 0) ||
                    (sizeof($cheque_details) > 0 && sizeof($vch_details) > 0) ||
                    (sizeof($vch_details) > 0 && sizeof($advan_amout) > 0) ||
                    (sizeof($vch_details) > 0 && sizeof($card_pay_details) > 0)
                ) {
                    $payment_mode = 'MULTI';
                } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) > 0 && sizeof($vch_details) == 0) {
                    $payment_mode = 'ADV_ADJ';
                }
                //Step 1: Update edited payment mode in payment table
                $data = array(
                    'payment_status' => $pay['payment_status'],
                    'date_payment' => $pay['payment_date'],
                    'metal_rate' => isset($pay['metal_rate']) ? $pay['metal_rate'] : NULL,
                    'metal_weight' => $pay['metal_weight'],
                    'payment_mode' => $payment_mode,
                    'remark' => $pay['remark'],
                    "date_upd" => date('Y-m-d H:i:s'),
                    "last_update" => date('Y-m-d H:i:s'),
                    "id_employee" => isset($pay['generic']['id_employee1']) ? ($pay['generic']['id_employee1']) : $this->session->userdata('uid'),
                    // 'id_employee' 		    => (isset($generic['id_employee1'])?$generic['id_employee1']:  $this->session->userdata('uid')),
                );
                $this->db->trans_begin();
                $status = $this->$model->paymentDB("update", $id, $data);
                if ($status['status'] == 1) {
                    /*** Update payment mode details table if previous mode is not same as current edited mode.... 
                         1. single to single
                         2. single to multi
                         3. multi to single    ***/
                    //delete record from ret_advance_utilized table....
                    $this->db->trans_begin();
                    $query = $this->$model->benifit($id, $data);
                    $deleteUtilized = $this->$model->deleteUtilized($id);
                    if ($pay['prev_pay_mode'] != $payment_mode) {
                        // old : Step 2: update payment status as cancel in payment_mode_details table for that id_payment....
                        //new : update isactive=0 for that id_payment and adding new entry for that id_payment with is_active=1
                        //payment_status is saved in payment_mode_details (payment status that saved in payment table)
                        $update_pay = array(
                            //'payment_status'=> 4, // Cancelled
                            'is_active' => 0,
                            'updated_by' => $this->session->userdata('uid'),
                            "updated_time" => date('Y-m-d H:i:s')
                        );
                        //$updated = $this->$model->update_data($update_pay,'id_payment',$id,'payment_mode_details');
                        //$updated line updates is_active=0 for old data for that id_payment
                        $updated = $this->$model->update_modestatus_data($update_pay, $id);
                        // Step 3: Insert new records in payment_mode_details table (both any single-mode to multi-mode or multi-mode to any single-mode)....
                        if ($cus_pay_mode['cash_payment'] > 0) {
                            $arrayCashPay = array(
                                'id_payment' => $id,
                                'payment_amount' => $cus_pay_mode['cash_payment'],
                                'payment_mode' => 'CSH',
                                'payment_status' => $pay['payment_status'],
                                // 'payment_date'		=> date("Y-m-d H:i:s"),
                                'payment_date' => $pay['payment_date'],
                                'created_time' => date("Y-m-d H:i:s"),
                                'created_by' => $this->session->userdata('uid'),
                                'remark' => $pay['remark']
                            );
                            if (!empty($arrayCashPay)) {
                                $cashPayInsert = $this->$model->insertData($arrayCashPay, 'payment_mode_details');
                            }
                        }
                        if (sizeof($card_pay_details) > 0) {
                            foreach ($card_pay_details as $card_pay) {
                                $arrayCardPay[] = array(
                                    'id_payment' => $id,
                                    'card_type' => $card_pay['card_name'],
                                    'payment_amount' => $card_pay['card_amt'],
                                    'payment_mode' => ($card_pay['card_type'] == 1 ? 'CC' : 'DC'),
                                    'card_no' => ($card_pay['card_no'] != '' ? $card_pay['card_no'] : NULL),
                                    'payment_ref_number' => ($card_pay['ref_no'] != '' ? $card_pay['ref_no'] : NULL),
                                    'id_pay_device' => ($card_pay['id_device'] != '' ? $card_pay['id_device'] : NULL),
                                    'payment_status' => $pay['payment_status'],
                                    // 'payment_date'		=> date("Y-m-d H:i:s"),
                                    'payment_date' => $pay['payment_date'],
                                    'created_time' => date("Y-m-d H:i:s"),
                                    'created_by' => $this->session->userdata('uid'),
                                    'remark' => $pay['remark']
                                );
                            }
                            if (!empty($arrayCardPay)) {
                                $cardPayInsert = $this->$model->insertBatchData($arrayCardPay, 'payment_mode_details');
                            }
                        }
                        if (sizeof($cheque_details) > 0) {
                            foreach ($cheque_details as $chq_pay) {
                                $arraychqPay[] = array(
                                    'id_payment' => $id,
                                    'payment_amount' => $chq_pay['payment_amount'],
                                    'payment_status' => $pay['payment_status'],
                                    // 'payment_date'	=> date("Y-m-d H:i:s"),
                                    'payment_date' => $pay['payment_date'],
                                    'cheque_date' => $cheque_details[0]['cheque_date'] != '' && isset($cheque_details[0]['cheque_date']) ? date('Y-m-d H:i:s', strtotime($cheque_details[0]['cheque_date'])) : date("Y-m-d H:i:s"),  // esakki 11-11
                                    'payment_mode' => 'CHQ',
                                    'cheque_no' => ($chq_pay['cheque_no'] != '' ? $chq_pay['cheque_no'] : NULL),
                                    'bank_name' => ($chq_pay['bank_name'] != '' ? $chq_pay['bank_name'] : NULL),
                                    'bank_branch' => ($chq_pay['bank_branch'] != '' ? $chq_pay['bank_branch'] : NULL),
                                    'bank_IFSC' => ($chq_pay['bank_IFSC'] != '' ? $chq_pay['bank_IFSC'] : NULL),
                                    'created_time' => date("Y-m-d H:i:s"),
                                    'created_by' => $this->session->userdata('uid'),
                                    'remark' => $pay['remark']
                                );
                            }
                            if (!empty($arraychqPay)) {
                                $chqPayInsert = $this->$model->insertBatchData($arraychqPay, 'payment_mode_details');
                            }
                        }
                        if (sizeof($net_banking_details) > 0) {
                            foreach ($net_banking_details as $nb_pay) {
                                $arrayNBPay[] = array(
                                    'id_payment' => $id,
                                    'payment_amount' => $nb_pay['amount'],
                                    'payment_status' => $pay['payment_status'],
                                    // 'payment_date'		=> date("Y-m-d H:i:s"),
                                    'payment_date' => $pay['payment_date'],
                                    'payment_mode' => 'NB',
                                    'payment_ref_number' => ($nb_pay['ref_no'] != '' ? $nb_pay['ref_no'] : NULL),
                                    'net_banking_date' => ($nb_pay['nb_date'] != '' ? $nb_pay['nb_date'] : NULL),
                                    'NB_type' => ($nb_pay['nb_type'] != '' ? $nb_pay['nb_type'] : NULL),
                                    'id_pay_device' => ($nb_pay['nb_type'] == 3 ? $nb_pay['id_device'] : NULL),
                                    'id_bank' => ($nb_pay['nb_type'] == 1 || $nb_pay['nb_type'] == 2 ? $nb_pay['id_bank'] : NULL),
                                    //'bank_acc_no'           =>($nb_pay['nb_type']==1 || $nb_pay['nb_type']==2? $nb_pay['acc_no']:NULL),
                                    'created_time' => date("Y-m-d H:i:s"),
                                    'created_by' => $this->session->userdata('uid'),
                                    'remark' => $pay['remark']
                                );
                            }
                            if (!empty($arrayNBPay)) {
                                $NbPayInsert = $this->$model->insertBatchData($arrayNBPay, 'payment_mode_details');
                            }
                        }
                        //Advance Adjusement
                        $advan_amout = $pay['adv']['advance_muliple_receipt'][0];
                        if (count($advan_amout) > 0) {
                            $advance_amount_adj = json_decode($advan_amout);
                            $advance_amt = 0;
                            foreach ($advance_amount_adj as $obj) {
                                $advance_amt += $obj->adj_amount;
                                $data_adv_amount = array(
                                    'id_issue_receipt' => $obj->id_issue_receipt,
                                    'id_payment' => $id,
                                    'adjusted_for' => 2, // Adjusted in CRM
                                    'utilized_amt' => $obj->adj_amount,
                                    'cash_utilized_amt' => $obj->cash_pay,
                                );
                                $insId_adv_amount = $this->$model->insertData($data_adv_amount, 'ret_advance_utilized');
                                $array_adj_pay = array(
                                    'id_payment' => $id,
                                    'payment_amount' => $obj->adj_amount,
                                    'payment_status' => $pay['payment_status'],
                                    // 'payment_date'		=> date("Y-m-d H:i:s"),
                                    'payment_date' => $pay['payment_date'],
                                    'payment_mode' => 'ADV_ADJ',
                                    'created_time' => date("Y-m-d H:i:s"),
                                    'created_by' => $this->session->userdata('uid'),
                                    'remark' => $pay['remark']
                                );
                                $this->$model->insertData($array_adj_pay, 'payment_mode_details');
                            }
                        }
                        //Advance Adjusement
                        //voucher  starts
                        if (sizeof($vch_details) > 0) {
                            foreach ($vch_details as $vch_pay) {
                                $arrayVchPay[] = array(
                                    'id_payment' => $id,
                                    'payment_amount' => $vch_pay['payment_amount'],
                                    'payment_mode' => 'VCH',
                                    'card_no' => ($vch_pay['card_no'] != '' ? $vch_pay['card_no'] : NULL),
                                    'payment_status' => $pay['payment_status'],
                                    // 'payment_date'		=> date("Y-m-d H:i:s"),
                                    'payment_date' => $pay['payment_date'],
                                    'created_time' => date("Y-m-d H:i:s"),
                                    'created_by' => $this->session->userdata('uid'),
                                    'remark' => $pay['remark']
                                );
                            }
                            if (!empty($arrayVchPay)) {
                                $vchPayInsert = $this->$model->insertBatchData($arrayVchPay, 'payment_mode_details');
                            }
                        }
                        //voucher ends
                    }
                    /*** Update payment mode details table if previous mode is same as current edited mode.... ***/ else if ($pay['prev_pay_mode'] == $payment_mode) {
                        // old : Step 2: update payment status as cancel in payment_mode_details table for that id_payment....
                        //new : update isactive=0 for that id_payment and adding new entry for that id_payment with is_active=1
                        //payment_status is saved in payment_mode_details (payment status that saved in payment table)
                        $update_pay = array(
                            //'payment_status'=> 4, // Cancelled
                            'is_active' => 0,
                            'updated_by' => $this->session->userdata('uid'),
                            "updated_time" => date('Y-m-d H:i:s')
                        );
                        //$updated = $this->$model->update_data($update_pay,'id_payment',$id,'payment_mode_details');
                        //$updated line updates is_active=0 for old data for that id_payment
                        $updated = $this->$model->update_modestatus_data($update_pay, $id);
                        if ($cus_pay_mode['cash_payment'] > 0) {
                            $arrayCashPay = array(
                                'id_payment' => $id,
                                'payment_amount' => $cus_pay_mode['cash_payment'],
                                'payment_mode' => 'CSH',
                                'payment_status' => $pay['payment_status'],
                                // 'payment_date'		=> date("Y-m-d H:i:s"),
                                'payment_date' => $pay['payment_date'],
                                'created_time' => date("Y-m-d H:i:s"),
                                'created_by' => $this->session->userdata('uid'),
                                'remark' => $pay['remark']
                            );
                            if (!empty($arrayCashPay)) {
                                $cashPayInsert = $this->$model->insertData($arrayCashPay, 'payment_mode_details');
                            }
                        }
                        if (sizeof($card_pay_details) > 0) {
                            foreach ($card_pay_details as $card_pay) {
                                $arrayCardPay[] = array(
                                    'id_payment' => $id,
                                    'card_type' => $card_pay['card_name'],
                                    'payment_amount' => $card_pay['card_amt'],
                                    'payment_mode' => ($card_pay['card_type'] == 1 ? 'CC' : 'DC'),
                                    'card_no' => ($card_pay['card_no'] != '' ? $card_pay['card_no'] : NULL),
                                    'payment_ref_number' => ($card_pay['ref_no'] != '' ? $card_pay['ref_no'] : NULL),
                                    'id_pay_device' => ($card_pay['id_device'] != '' ? $card_pay['id_device'] : NULL),
                                    'payment_status' => $pay['payment_status'],
                                    // 'payment_date'		=> date("Y-m-d H:i:s"),
                                    'payment_date' => $pay['payment_date'],
                                    'created_time' => date("Y-m-d H:i:s"),
                                    'created_by' => $this->session->userdata('uid'),
                                    'remark' => $pay['remark']
                                );
                            }
                            if (!empty($arrayCardPay)) {
                                $cardPayInsert = $this->$model->insertBatchData($arrayCardPay, 'payment_mode_details');
                            }
                        }
                        if (sizeof($cheque_details) > 0) {
                            foreach ($cheque_details as $chq_pay) {
                                $arraychqPay[] = array(
                                    'id_payment' => $id,
                                    'payment_amount' => $chq_pay['payment_amount'],
                                    'payment_status' => $pay['payment_status'],
                                    // 'payment_date'	=> date("Y-m-d H:i:s"),
                                    'payment_date' => $pay['payment_date'],
                                    'cheque_date' => $cheque_details[0]['cheque_date'] != '' && isset($cheque_details[0]['cheque_date']) ? date('Y-m-d H:i:s', strtotime($cheque_details[0]['cheque_date'])) : date("Y-m-d H:i:s"),  // esakki 11-11
                                    'payment_mode' => 'CHQ',
                                    'bank_IFSC' => ($chq_pay['bank_IFSC'] != '' ? $chq_pay['bank_IFSC'] : NULL),
                                    'cheque_no' => ($chq_pay['cheque_no'] != '' ? $chq_pay['cheque_no'] : NULL),
                                    'bank_name' => ($chq_pay['bank_name'] != '' ? $chq_pay['bank_name'] : NULL),
                                    'bank_branch' => ($chq_pay['bank_branch'] != '' ? $chq_pay['bank_branch'] : NULL),
                                    'bank_IFSC' => ($chq_pay['bank_IFSC'] != '' ? $chq_pay['bank_IFSC'] : NULL),
                                    'created_time' => date("Y-m-d H:i:s"),
                                    'created_by' => $this->session->userdata('uid'),
                                    'remark' => $pay['remark']
                                );
                            }
                            if (!empty($arraychqPay)) {
                                $chqPayInsert = $this->$model->insertBatchData($arraychqPay, 'payment_mode_details');
                            }
                        }
                        if (sizeof($net_banking_details) > 0) {
                            foreach ($net_banking_details as $nb_pay) {
                                $arrayNBPay[] = array(
                                    'id_payment' => $id,
                                    'payment_amount' => $nb_pay['amount'],
                                    'payment_status' => $pay['payment_status'],
                                    // 'payment_date'		=> date("Y-m-d H:i:s"),
                                    'payment_date' => $pay['payment_date'],
                                    'payment_mode' => 'NB',
                                    'payment_ref_number' => ($nb_pay['ref_no'] != '' ? $nb_pay['ref_no'] : NULL),
                                    'net_banking_date' => ($nb_pay['nb_date'] != '' ? $nb_pay['nb_date'] : NULL),
                                    'NB_type' => ($nb_pay['nb_type'] != '' ? $nb_pay['nb_type'] : NULL),
                                    'id_pay_device' => ($nb_pay['nb_type'] == 3 ? $nb_pay['id_device'] : NULL),
                                    'id_bank' => ($nb_pay['nb_type'] == 1 || $nb_pay['nb_type'] == 2 ? $nb_pay['id_bank'] : NULL),
                                    //'bank_acc_no'           =>($nb_pay['nb_type']==1 || $nb_pay['nb_type']==2? $nb_pay['acc_no']:NULL),
                                    'created_time' => date("Y-m-d H:i:s"),
                                    'created_by' => $this->session->userdata('uid'),
                                    'remark' => $pay['remark']
                                );
                            }
                            if (!empty($arrayNBPay)) {
                                $NbPayInsert = $this->$model->insertBatchData($arrayNBPay, 'payment_mode_details');
                            }
                        }
                        //Advance Adjusement
                        $advan_amout = $pay['adv']['advance_muliple_receipt'][0];
                        if (count($advan_amout) > 0) {
                            //delete record from ret_advance_utilized table....
                            $deleteUtilized = $this->$model->deleteUtilized($id);
                            $advance_amount_adj = json_decode($advan_amout);
                            $advance_amt = 0;
                            foreach ($advance_amount_adj as $obj) {
                                $advance_amt += $obj->adj_amount;
                                $data_adv_amount = array(
                                    'id_issue_receipt' => $obj->id_issue_receipt,
                                    'id_payment' => $id,
                                    'adjusted_for' => 2, // Adjusted in CRM
                                    'utilized_amt' => $obj->adj_amount,
                                    'cash_utilized_amt' => $obj->cash_pay,
                                );
                                $insId_adv_amount = $this->$model->insertData($data_adv_amount, 'ret_advance_utilized');
                                $array_adj_pay = array(
                                    'adv_receipt_no' => $obj->id_issue_receipt,
                                    'id_payment' => $id,
                                    'payment_amount' => $obj->adj_amount,
                                    'payment_status' => $pay['payment_status'],
                                    // 'payment_date'		=> date("Y-m-d H:i:s"),
                                    'payment_date' => $pay['payment_date'],
                                    'payment_mode' => 'ADV_ADJ',
                                    'created_time' => date("Y-m-d H:i:s"),
                                    'created_by' => $this->session->userdata('uid'),
                                    'remark' => $pay['remark']
                                );
                                $this->$model->insertData($array_adj_pay, 'payment_mode_details');
                            }
                        }
                        //Advance Adjusement
                        //voucher starts
                        if (sizeof($vch_details) > 0) {
                            foreach ($vch_details as $vch_pay) {
                                $arrayVchPay[] = array(
                                    'id_payment' => $id,
                                    'payment_amount' => $vch_pay['payment_amount'],
                                    'payment_mode' => 'VCH',
                                    'card_no' => ($vch_pay['card_no'] != '' ? $vch_pay['card_no'] : NULL),
                                    'payment_status' => $pay['payment_status'],
                                    // 'payment_date'		=> date("Y-m-d H:i:s"),
                                    'payment_date' => $pay['payment_date'],
                                    'created_time' => date("Y-m-d H:i:s"),
                                    'created_by' => $this->session->userdata('uid'),
                                    'remark' => $pay['remark']
                                );
                            }
                            if (!empty($arrayVchPay)) {
                                $vchPayInsert = $this->$model->insertBatchData($arrayVchPay, 'payment_mode_details');
                            }
                        }
                        //voucher ends
                    }
                    // esakki 19-11	
                    $check_status = $this->$model->paymentDB("get", $id);
                    $cusData = $this->$accountmodel->get_customer_acc($check_status['id_scheme_account']);
                    /*if($pay['payment_status'] != 1){
                        $updateData['total_paid_ins'] = $cusData['total_paid_ins'] - 1;
                    }else if($check_status['payment_status'] != $pay['payment_status']){
                        $updateData['total_paid_ins'] = $cusData['total_paid_ins'] + 1;
                    }  
                    // 	print_r($pay['payment_status']);exit;
                    if($updateData){
                        $updSchAc  = $this->account_model->update_account($updateData,$check_status['id_scheme_account']);
                    }*/
                    //update paid installments against account...
                    $paid = $this->payment_model->getPaidInsData($check_status['id_scheme_account']);
                    if (sizeof($paid) > 0) {
                        $paid_ins = array('total_paid_ins' => $paid['paid_installments']);
                        $this->payment_model->updData($paid_ins, 'id_scheme_account', $check_status['id_scheme_account'], 'scheme_account');
                    }
                }
                // Payment Status Changed
                /*	if($pay['prev_pay_status'] != $pay['payment_status'])
                    {
                         $pm_details = $this->payment_model->getAllsubPayments($pay['id_payment']);
                        foreach($pm_details as $pm){
                            $update_pay = array(
                                                'payment_status'=> $pay['payment_status'],
                                                'updated_by'	=> $this->session->userdata('uid'),
                                                "updated_time"  => date('Y-m-d H:i:s')
                                            );
                            $updated = $this->$model->update_data($update_pay,'id_pay_mode_details',$pm['id_pay_mode_details'],'payment_mode_details');
                        }
                    }*/
                //	print_r($cardPayInsert);exit;	
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $log_data = array(
                        'id_log' => $this->id_log,
                        'event_date' => date("Y-m-d H:i:s"),
                        'module' => 'Payment',
                        'operation' => 'Edit Payment',
                        'record' => $id,
                        'remark' => 'Payment Updated successfully'
                    );
                    $log_status = $this->$log_model->log_detail('insert', '', $log_data);
                    $this->session->set_flashdata('chit_alert', array('message' => 'Payment updated successfully', 'class' => 'success', 'title' => 'Scheme Payment'));
                } else {
                    $log_status = '';
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Scheme Payment'));
                }
                echo json_encode($log_status);
                break;
            case 'View':
                if ($id != NULL) {
                    $data['pay'] = $this->$model->paymentDB('get', $id);
                    $data['main_content'] = self::PAY_VIEW . "edit_form";
                } else {
                    $data['pay'] = $this->$model->paymentDB();
                    // Metal rate back date entry esakki-20250729
                    $profile_settings = $this->chitadmin_model->get_profile_settings($this->session->userdata('profile'));
                    $data['pay']['metalrate_edit'] = $profile_settings['metalrate_edit'];
                    $data['pay']['metal_rate_datelimit'] = $profile_settings['metal_rate_datelimit'];
                    $data['pay']['min_pan_amt'] = $this->$model->get_ret_settings('min_pan_amt');
                    $data['pay']['validate_cash_amt'] = $this->$model->get_ret_settings('validate_cash_amt');
                    $data['pay']['max_cash_amt'] = $this->$model->get_ret_settings('max_cash_amt');
                    $data['cus'] = $this->$cus_model->ajax_get_all_customers();
                    //echo "<pre>";print_r($data);echo "</pre>";exit;
                    $data['main_content'] = self::PAY_VIEW . "form";
                }
                $this->load->view('layout/template', $data);
                break;
            case 'Status':
                $data['pay'] = $this->$model->paymentDB('get', $id);
                $data['status_log'] = $this->$model->payment_log($id);
                $data['main_content'] = self::PAY_VIEW . "status_form";
                $this->load->view('layout/template', $data);
                break;
            case 'SaveAll':
                $pay = $this->input->post('pay');
                $generic = $this->input->post('generic');
                if ($generic['installments'] > 1) {
                    $amount = ($generic['payment_amount'] + $generic['discountedAmt']) / $generic['installments'];
                    $payment_amount = $generic['payment_amount'] / $generic['installments'];
                } else {
                    $amount = $generic['payment_amount'] + $generic['discountedAmt'];
                    $payment_amount = $generic['payment_amount'];
                }
                // esakki 23-09
                if ($generic['sch_type'] != 1) {
                    if ($generic['sch_type'] == 3 && $generic['flexible_sch_type'] != 8) {
                        if ($generic['gst_type'] == 0) {
                            $payment_amount = $payment_amount - $generic['gst_amount'];
                        }
                    }
                }
                if ($payment_amount > 0) {
                    $form_secret = (isset($generic["form_secret"]) ? $generic["form_secret"] : '');
                    if (verify_form_secret($form_secret)) {
                        $is_use_wallet = (!empty($generic['is_use_wallet']) ? 1 : 0);
                        //  print_r($generic);exit;
                        // Multi-mode
                        $cus_pay_mode = $this->input->post('cus_pay_mode');
                        $card_pay_details = json_decode($cus_pay_mode['card_pay'], true);
                        $cheque_details = json_decode($cus_pay_mode['chq_pay'], true);
                        $net_banking_details = json_decode($cus_pay_mode['net_bank_pay'], true);
                        $adv_adj = json_decode($cus_pay_mode['adv_adj'], true);
                        $adv_adj_details = $adv_adj[0];
                        $vch_details = json_decode($cus_pay_mode['vch_pay'], true);
                        //$advan_amout = json_decode($pay['adv']['advance_muliple_receipt'][0],true);//Commented and replaced by Durga 19.05.2023
                        $advan_amout = json_decode($_POST['adv']['advance_muliple_receipt'][0], true);
                        //echo '<pre>'; print_r(sizeof($advan_amout));exit;
                        if ($cus_pay_mode['cash_payment'] > 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) == 0 && ($generic['redeem_request'] == 0)) {
                            $payment_mode = 'CSH';
                        } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) > 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) == 0) {
                            foreach ($card_pay_details as $card_pay) {
                                $mode = ($card_pay['card_type'] == 1 ? 'CC' : 'DC');
                                if ($payment_mode == '') {
                                    $payment_mode = $mode;
                                } else if ($payment_mode != $mode) {
                                    $payment_mode = 'MULTI';
                                }
                            }
                        } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) > 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) == 0 && ($generic['redeem_request'] == 0)) {
                            $payment_mode = 'CHQ';
                        } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) > 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) == 0 && ($generic['redeem_request'] == 0)) {
                            $payment_mode = 'NB';
                        } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($vch_details) > 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0 && ($generic['redeem_request'] == 0)) {
                            $payment_mode = 'VCH';
                        } else if (($is_use_wallet == 1 && $generic['redeem_request'] > 0) && $cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) == 0 && sizeof($vch_details) == 0) {
                            $payment_mode = 'REF_WALLET';
                        }
                        /*	else if(($cus_pay_mode['cash_payment'] > 0 && sizeof($card_pay_details) > 0) || ($cus_pay_mode['cash_payment'] > 0 && sizeof($cheque_details) > 0) || 
                            ($cus_pay_mode['cash_payment'] > 0 && sizeof($net_banking_details) > 0) || (sizeof($net_banking_details) > 0 && sizeof($cheque_details) > 0) || 
                            (sizeof($net_banking_details) > 0 && sizeof($card_pay_details) > 0) || (sizeof($cheque_details) > 0 && sizeof($card_pay_details) > 0) || 
                            ($cus_pay_mode['cash_payment'] > 0 && sizeof($advan_amout) > 0) || (sizeof($card_pay_details) > 0 && sizeof($advan_amout) > 0) || 
                            (sizeof($cheque_details) > 0 && sizeof($advan_amout) > 0) || (sizeof($net_banking_details) > 0 && sizeof($advan_amout) > 0)){
                                $payment_mode = 'MULTI';
                            }*/ else if (
                            ($cus_pay_mode['cash_payment'] > 0 && sizeof($card_pay_details) > 0) ||
                            ($cus_pay_mode['cash_payment'] > 0 && sizeof($cheque_details) > 0) ||
                            ($cus_pay_mode['cash_payment'] > 0 && sizeof($net_banking_details) > 0) ||
                            ($cus_pay_mode['cash_payment'] > 0 && sizeof($vch_details) > 0) ||
                            ($cus_pay_mode['cash_payment'] > 0 && sizeof($advan_amout) > 0) ||
                            ($cus_pay_mode['cash_payment'] > 0 && ($is_use_wallet == 1 && $generic['redeem_request'] > 0)) ||
                            (sizeof($net_banking_details) > 0 && sizeof($cheque_details) > 0) ||
                            (sizeof($net_banking_details) > 0 && sizeof($card_pay_details) > 0) ||
                            (sizeof($net_banking_details) > 0 && sizeof($vch_details) > 0) ||
                            (sizeof($net_banking_details) > 0 && sizeof($advan_amout) > 0) ||
                            (sizeof($net_banking_details) > 0 && ($is_use_wallet == 1 && $generic['redeem_request'] > 0)) ||
                            (sizeof($cheque_details) > 0 && sizeof($card_pay_details) > 0) ||
                            (sizeof($cheque_details) > 0 && sizeof($vch_details) > 0) ||
                            (sizeof($cheque_details) > 0 && sizeof($advan_amout) > 0) ||
                            (sizeof($cheque_details) > 0 && ($is_use_wallet == 1 && $generic['redeem_request'] > 0)) ||
                            (sizeof($card_pay_details) > 0 && sizeof($advan_amout) > 0) ||
                            (sizeof($card_pay_details) > 0 && sizeof($vch_details) > 0) ||
                            (sizeof($card_pay_details) > 0 && ($is_use_wallet == 1 && $generic['redeem_request'] > 0)) ||
                            (sizeof($vch_details) > 0 && sizeof($advan_amout) > 0) ||
                            (sizeof($vch_details) > 0 && sizeof($card_pay_details) > 0) ||
                            (sizeof($vch_details) > 0 && ($is_use_wallet == 1 && $generic['redeem_request'] > 0))
                        ) {
                            $payment_mode = 'MULTI';
                        } else if ($cus_pay_mode['cash_payment'] == 0 && sizeof($card_pay_details) == 0 && sizeof($cheque_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($net_banking_details) == 0 && sizeof($advan_amout) > 0 && sizeof($vch_details) == 0 && ($generic['redeem_request'] == 0)) {
                            $payment_mode = 'ADV_ADJ';
                        }
                        // CREATE LOG
                        if (!is_dir($this->log_dir . '/manual')) {
                            mkdir($this->log_dir . '/manual', 0777, true);
                        }
                        $log_path = $this->log_dir . '/manual/create_payment_' . date("Y-m-d") . '.txt';
                        $lg_data = "\n CP --" . date('Y-m-d H:i:s') . " -- : " . json_encode($_POST);
                        file_put_contents($log_path, $lg_data, FILE_APPEND | LOCK_EX);
                        $pdc = $this->input->post('pdc');
                        $submit_type = $this->input->post('type');
                        // echo"<pre>"; print_r($generic);
                        $otp = $this->session->userdata('pay_OTP');
                        $data = $this->$accountmodel->select_otp($otp);
                        $id_otp = (isset($data['id_otp']) ? $data['id_otp'] : NULL);
                        $send_notif = $this->$sms_model->check_noti_settings();
                        //print_r($send_notif);exit;
                        $cusData = $this->$accountmodel->get_customer_acc($generic['id_scheme_account']);
                        $used_wallet = FALSE;
                        $is_use_wallet = (isset($generic['is_use_wallet']) ? 1 : 0);
                        $redeemed_amount = 0.00;
                        $redeem_request = (isset($generic['redeem_request']) ? floor($generic['redeem_request']) : 0);
                        //		$amount =0;
                        $metal_wgt = 0;
                        //	$date_payment = (isset($generic['date_payment'])? date('Y-m-d h:i:s',strtotime(str_replace("/","-",$generic['date_payment']))):NULL);
                        $date_payment = (isset($generic['date_payment']) ? date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $generic['date_payment']))) : NULL);
                        $custom_entry_date = $cusData['custom_entry_date'];
                        //	 echo"<pre>"; print_r($generic);exit;
                        //GST calculation
                        $sch_data = $this->$model->get_schgst($generic['id_scheme_account']);
                        /*	$gst_val = 0;
                            $gst_amt = 0;
                            if($sch_data['gst'] > 0 ){
                                 if($sch_data['gst_type'] == 1){
                                      $gst_val = $amount*($sch_data['gst']/100);	
                                    $gst_amt = $gst_val;
                                 }	
                                else	
                                {
                                     $gst_val = $amount-($amount*(100/(100+$sch_data['gst'])));
                                     $gst_amt = $gst_val;
                                }					
                            }*/
                        $totalamount = ($amount * $generic['installments']);
                        // Wallet
                        $walletData = $this->$model->wallet_balance($generic['id_customer']);
                        if ($is_use_wallet == 1) {
                            $allowed_redeem = ($walletData['redeem_percent'] > 0 ? ($totalamount * ($walletData['redeem_percent'] / 100)) : $walletData['wal_balance']);
                            if ($allowed_redeem > $totalamount) {
                                $can_redeem = $totalamount;
                            } else {
                                $can_redeem = $allowed_redeem;
                            }
                            /* if ($allowed_redeem > $walletData['wal_balance']) {
                                $can_redeem = $walletData['wal_balance'];
                            } else {
                                $can_redeem = $allowed_redeem;
                            } */
                            $used_wallet = TRUE;
                            $redeemed_amount = floor($redeem_request <= $can_redeem ? $redeem_request : $can_redeem);
                        }
                        $payid = array();
                        for ($i = 1; $i <= $generic['installments']; $i++) {
                            /* !!!! GST & METAL WEIGHT CALCULATION START FOR INCLUSIVE, EXCLUSIVE WITH DISCOUNT IF APPLICABLE !!!!  Date : 20/12/2023 By: Abi  */
                            $pay_amt = $payment_amount;   //15000
                            $gst = $generic['gst'];  // 3 %    
                            $gst_type = $generic['gst_type'];    // 1 - inclusive type
                            $disc_amt = $generic['discountedAmt'];   // 250
                            //Subtracting the dicount amount from the total payment amount. So the actual amount received from customer arrived...
                            $actual_pay = ($disc_amt > 0 ? ($pay_amt - $disc_amt) : $pay_amt);    // 15000 - 250 = 14750
                            if ($gst_type == 1) {
                                //FOR EXCLUSIVE GST TYPE...
                                $gst_amt = $actual_pay * ($gst / 100); // 14750 * (3/100)  = 442.5
                            } else {
                                //FOR INCLUSIVE GST TYPE...
                                $gst_amt = $actual_pay - ($actual_pay * (100 / (100 + $gst))); // 14750 - (14750 * (100 / (100 + 3)))  = 429.61
                            }
                            //METAL WEIGHT CALC STARTS...
                            $metal_wgt = '';
                            if ($generic['fix_weight'] == 2) //scheme_type is assigned to fix weight
                            {
                                $amt = ($sch_data['gst_type'] == 0 ? ($generic['sch_amt'] - $gst_amt) : $generic['sch_amt'] - $gst_amt);
                                $data = array(
                                    'sch_amt' => $amt,
                                    'metal_rate' => $generic['metal_rate']
                                );
                                $metal_wgt = $this->amount_to_weight($data);
                            } else if ($generic['fix_weight'] == 3) // scheme type
                            {
                                $amt = ($sch_data['gst_type'] == 0 ? ($pay_amt - $gst_amt) : $pay_amt - $gst_amt);
                                // for  inclusive ---> 15000 - 429.61 = 14571
                                // for  exclusive ---> 15000 
                                if ($generic['flexible_sch_type'] == 3 || $generic['flexible_sch_type'] == 4 || $generic['flexible_sch_type'] == 7 || $generic['flexible_sch_type'] == 8 || ($generic['flexible_sch_type'] == 2 && $generic['wgt_convert'] != 2) || ($generic['flexible_sch_type'] == 5 && $generic['wgt_store_as'] == 1)) {
                                    $data = array('sch_amt' => $amt, 'metal_rate' => $generic['metal_rate']);
                                    $metal_wgt = $this->amount_to_weight($data);
                                    // for  inclusive ---> (14571 / 5685) = 2.563 grams
                                    // for  exclusive ---> (15000 / 5685) = 2.638 grams
                                }
                            } else {
                                $metal_wgt = (isset($generic['metal_weight']) ? $generic['metal_weight'] : 0.000);
                            }
                            /* GST & METAL WEIGHT CALCULATION END */
                            /*$metal_wgt = ''; 
                            if($generic['fix_weight']==2)//scheme_type is assigned to fix weight
                            {	
                                $amt = ($sch_data['gst_type'] == 0 ? ($generic['sch_amt'] - $gst_amt) :$generic['sch_amt'] );			
                                $data = array ('sch_amt'=>$amt,
                                               'metal_rate'=>$generic['metal_rate']);
                                $metal_wgt = $this->amount_to_weight($data);
                            }
                            else if($generic['fix_weight']==3) // scheme type
                            {
                                if(($generic['flexible_sch_type']==4 || $generic['flexible_sch_type']==5 || $generic['flexible_sch_type']==8) && $generic['is_otp_scheme']==1 && $generic['wgt_store_as']== 1){
                                    $amt = $generic['payment_amount'];
                                    $data = array ('sch_amt'=>$amt,
                                    'metal_rate'=>$generic['metal_rate']);
                                    $metal_wgt = $this->amount_to_weight($data);
                                }
                                else if(($generic['flexible_sch_type']==2 || $generic['flexible_sch_type']==3 || $generic['flexible_sch_type']==4 || $generic['flexible_sch_type']==5 || $generic['flexible_sch_type']==8) && $generic['wgt_convert']==0)
                                {
                                    $amt = $generic['payment_amount'];
                                    $data = array ('sch_amt'=>$amt,
                                    'metal_rate'=>$generic['metal_rate']);
                                    $metal_wgt = $this->amount_to_weight($data);
                                }
                            }
                            else
                            {	
                                $metal_wgt = (isset($generic['metal_weight'])?$generic['metal_weight']: 0.000);
                            }*/
                            //		print_r($metal_wgt);exit;		
                            // ND - normal, PN - pending & normal, AN - adv & normal, PD pending due ,AD-adv due
                            $dueType = '';
                            if ($generic['due_type'] == 'PN') {
                                $dueType = ($i == 1 ? 'ND' : 'PD');
                            } else if ($generic['due_type'] == 'AN') {
                                $dueType = ($i == 1 ? 'ND' : 'AD');
                            } else {
                                $dueType = $generic['due_type'];
                            }
                            //	print_r($dueType);exit;
                            if ($this->session->userdata('branch_settings') == 1) {
                                if ($this->session->userdata('is_branchwise_cus_reg') == 1 && $this->config->item('payOtherBranch') == 0) {
                                    $id_branch = $cusData['cus_reg_branch'];
                                } else if ($this->session->userdata('branchWiseLogin') == 1 && $this->config->item('payOtherBranch') == 0) {
                                    //$id_branch  = (isset($sch_acc['id_branch'])?$sch_acc['id_branch']:(isset($cusData['sch_join_branch'])?$cusData['sch_join_branch']: NULL));
                                    //old line commented and new line added by Durga 15.05.2023
                                    $id_branch = $cusData['branch'];
                                }
                                //line added by Durga 15.05.2023 starts here
                                else if ($this->config->item('payOtherBranch') == 0) {
                                    $id_branch = $cusData['branch'];
                                }
                                //line added by Durga 15.05.2023 ends here
                                else if ($this->session->userdata('branchWiseLogin') == 1 && $this->config->item('payOtherBranch') == 1 && $this->session->userdata('empLog_branch') != 'N')    //11-01-2023 #AB branch auto store based on emp log -->
                                {
                                    $id_branch = ($this->session->userdata('empLog_branch') != '' ? $this->session->userdata('empLog_branch') : NULL);
                                } else {
                                    $id_branch = ($generic['id_branch'] != '' ? $generic['id_branch'] : NULL);
                                }
                            } else {
                                $id_branch = NULL;
                            }
                            $custom_entry_date = $this->customer_model->get_entrydate($id_branch);    //pr day close code update 19/06/2023
                            $start_year = $this->$accountmodel->get_financialYear();
                            $metal_wgt = formatMetalWeight($metal_wgt);
                            //$receipt_no = $this->generate_receipt_no();
                            $this->db->trans_begin();
                            /* [COMMENTED - Receipt now generated via chit_series_model->onPayTranStream]
                            if ($this->$model->get_rptnosettings() == 1) {
                                $receipt_no = $this->generate_receipt_no($generic['id_scheme'], $id_branch);
                            } else {
                                $receipt_no = null;
                            }
                            */
                            $receipt_no = null; // Receipt will be generated post-insert via onPayTranStream
                            // AB - digi_benefits & weight...
                            $dg_saved_benefit = 0;
                            $dg_benefit_value = 0;
                            $dg_benefit_type = 0;
                            $dg_saved_benefit_amt = 0;
                            $dg_other_benefit_wgt = 0;
                            $dg_other_benefit_amt = 0;
                            if ($sch_data['is_digi'] == 1) {
                                $metal_wgt = formatMetalWeight($payment_amount / $generic['metal_rate']);
                                if ($sch_data['interest'] == 1) {
                                    $digi_account = $this->digigold_modal->digiGold_account($cusData['id_customer']);
                                    $digi_benefitData = $this->digigold_modal->get_digi_benefit($digi_account);
                                    if (sizeof($digi_benefitData) > 0) {
                                        $is_other_benefit = 0;
                                        foreach ($digi_benefitData as $digi_benefit) {
                                            if ($digi_benefit['commodity'] == $sch_data['id_metal']) {
                                                if ($digi_benefit['interest_type'] == 0) {
                                                    $dg_saved_benefit_amt = $payment_amount * ($digi_benefit['interest_value'] / 100);
                                                } else {
                                                    $dg_saved_benefit_amt = $digi_benefit['interest_value'];
                                                }
                                                // $dg_saved_benefit = number_format(($dg_saved_benefit_amt / $generic['metal_rate']),3) ;
                                                // $dg_saved_benefit = number_format(floor(($dg_saved_benefit_amt / $generic['metal_rate']) * 1000) / 1000, 3, '.', '');
                                                //echo '<pre>';print_r($dg_saved_benefit);exit; 
                                                $dg_saved_benefit = formatMetalWeight($dg_saved_benefit_amt / $generic['metal_rate']);
                                                $dg_benefit_value = $digi_benefit['interest_value'];
                                                $dg_benefit_type = $digi_benefit['interest_type']; // 0-percent, 1- amount
                                            } else {
                                                $other_metal_rate = $this->$model->get_metalrate_by_branch($id_branch, $digi_benefit['commodity'], ''); //For branchwise rate
                                                if ($digi_benefit['interest_type'] == 0) {
                                                    $dg_other_benefit_wgt = $metal_wgt * ($digi_benefit['interest_value'] / 100);
                                                    $dg_other_benefit_amt = $dg_other_benefit_wgt * $other_metal_rate;
                                                } else {
                                                    $dg_other_benefit_amt = $digi_benefit['interest_value'];
                                                    $dg_other_benefit_wgt = $dg_other_benefit_amt * $other_metal_rate;
                                                }
                                                // $dg_saved_benefit = number_format(($dg_saved_benefit_amt / $generic['metal_rate']),3) ;
                                                $dg_other_benefit_wgt = number_format(floor(($dg_other_benefit_wgt) * 1000) / 1000, 3, '.', '');
                                                //echo '<pre>';print_r($dg_saved_benefit);exit; 
                                            }
                                        }
                                    }
                                }
                            }
                            $pay_array = array(
                                'id_scheme' => (isset($generic['id_scheme']) ? $generic['id_scheme'] : NULL),
                                'gst_type' => (isset($generic['gst_type']) ? $generic['gst_type'] : 0),
                                'gst' => (isset($generic['gst']) ? $generic['gst'] : 0),
                                'gst_amount' => (!empty($generic['gst_amount']) ? $generic['gst_amount'] : 0),        // esakki 19-09				       			 
                                'id_scheme_account' => (isset($generic['id_scheme_account']) ? $generic['id_scheme_account'] : NULL),
                                'is_editing_enabled' => (empty($generic['rate_edit_enable']) ? 0 : $generic['rate_edit_enable']),
                                // 'id_employee' 		    =>  $this->session->userdata('uid'),
                                'id_employee' => (isset($generic['id_employee']) ? $generic['id_employee'] : NULL),
                                'id_otp' => (isset($id_otp) ? $id_otp : NULL),
                                //	'id_transaction'	  =>  $txnid,
                                //		'date_payment'        =>  ($i > 1 ? date('Y-m-d',strtotime (($generic['total_paid']>0 ?'-':'+').($i-1).' month' , strtotime ( date('Y-m-d',strtotime($date_payment)) ))): $date_payment ), 	
                                // 			'date_payment'        =>  $date_payment,
                                'date_payment' => date('Y-m-d H:i:s'),
                                //'custom_entry_date'   =>  ($cusData['edit_custom_entry_date']==1 ? $custom_entry_date:NULL),
                                'custom_entry_date' => ($custom_entry_date['edit_custom_entry_date'] == 1 ? $custom_entry_date['custom_entry_date'] : NULL),       ////pr day close code update 19/06/2023
                                'id_branch' => $id_branch,
                                'due_type' => $dueType,
                                'payment_type' => $redeemed_amount == $totalamount && $is_use_wallet == 1 ? 'Wallet Payment' : (isset($generic['payment_type']) ? $generic['payment_type'] : NULL),
                                'payment_mode' => $payment_mode,
                                'payment_amount' => $payment_amount,
                                'act_amount' => $amount,
                                'metal_rate' => (isset($generic['metal_rate']) ? $generic['metal_rate'] : 0),
                                'metal_weight' => ($metal_wgt > 0 ? $metal_wgt : 0.000),
                                'payment_ref_number' => (isset($generic['payment_ref_number']) ? $generic['payment_ref_number'] : NULL),
                                'id_post_payment' => (isset($generic['id_post_payment']) ? $generic['id_post_payment'] : NULL),
                                'id_drawee' => (isset($generic['id_drawee']) ? $generic['id_drawee'] : NULL),
                                'remark' => (isset($generic['remark']) ? $generic['remark'] : NULL),
                                'payment_status' => $redeemed_amount == $totalamount ? 1 : (isset($generic['payment_status']) ? $generic['payment_status'] : 1),
                                'receipt_no' => $receipt_no,
                                'cheque_no' => (isset($pdc['cheque_no']) ? $pdc['cheque_no'] : ''),
                                'cheque_date' => (isset($pdc['date_payment']) ? date('Y-m-d', strtotime(str_replace("/", "-", $pdc['date_payment']))) : NULL),
                                'bank_name' => (isset($pdc['payee_bank']) ? $pdc['payee_bank'] : ''),
                                'bank_branch' => (isset($pdc['payee_branch']) ? $pdc['payee_branch'] : ''),
                                'bank_IFSC' => (isset($pdc['payee_ifsc']) ? $pdc['payee_ifsc'] : ''),
                                'bank_acc_no' => (isset($pdc['payee_acc_no']) ? $pdc['payee_acc_no'] : ''),
                                'id_drawee' => (isset($pdc['id_drawee']) ? $pdc['id_drawee'] : NULL),
                                'added_by' => 0,
                                'date_upd' => date('Y-m-d H:i:s'),
                                'approval_date' => date('Y-m-d H:i:s'),
                                "redeemed_amount" => (isset($redeemed_amount) ? $redeemed_amount : 0.00),
                                "old_metal_amount" => (isset($generic['est_amt']) ? ($generic['est_amt'] != '' ? $generic['est_amt'] : 0) : 0.00),
                                "receipt_year" => $start_year,
                                'form_secret' => (isset($generic['form_secret']) ? $generic['form_secret'] : NULL),
                                'counter_id' => (!empty($this->session->userdata('counter_id')) ? $this->session->userdata('counter_id') : NULL),
                                "saved_benefits" => $dg_saved_benefit,   //digi scheme benefit weight
                                "benefit_value" => $dg_benefit_value,
                                "benefit_type" => $dg_benefit_type,
                                "saved_benefit_amt" => $dg_saved_benefit_amt,  //digi scheme benefit amount,
                                'metalrate_edit_date' => (!empty($generic['metalrate_edit_date']) ? $generic['metalrate_edit_date'] : NULL),
                                'dg_other_benefit_wgt' => (!empty($dg_other_benefit_wgt) ? $dg_other_benefit_wgt : NULL),
                                'dg_other_benefit_amt' => (!empty($dg_other_benefit_amt) ? $dg_other_benefit_amt : NULL),
                            );
                            //   echo '<pre>';print_r($pay_array);exit;
                            if ($generic['discount_installment'] == $i || $generic['discount_type'] == 0) {
                                $pay_array['discountAmt'] = $generic['discountedAmt'];
                            }
                            //$this->db->trans_begin();	
                            $status = $this->$model->paymentDB("insert", "", $pay_array);
                            /*	$this->db->query('UNLOCK TABLES');
                                $lg_data = "\n CP Tables unlocked at --".date('d-m-Y H:i:s');
                                file_put_contents($log_path,$lg_data,FILE_APPEND | LOCK_EX); */
                            //rate fixed at the time of scheme join
                            if ($sch_data['one_time_premium'] == 1 && $sch_data['rate_fix_by'] == 0 && $sch_data['rate_select'] == 1) {
                                $gold_rate = $generic['metal_rate'];
                                if ($gold_rate != 0) {
                                    $isRateFixed = $this->$model->isRateFixed($generic['id_scheme_account']);
                                    if ($isRateFixed['status'] == 0) {
                                        $updData = array(
                                            "fixed_wgt" => ($metal_wgt > 0 ? $metal_wgt : $payment_amount / $gold_rate),
                                            "firstPayment_amt" => $payment_amount,
                                            "fixed_metal_rate" => $gold_rate,
                                            "rate_fixed_in" => 0,     //admin
                                            "fixed_rate_on" => date("Y-m-d H:i:s")
                                        );
                                        $ratestatus = $this->$model->updFixedRate($updData, $generic['id_scheme_account']);
                                    } else {
                                        $data = array('is_valid' => TRUE, 'success' => TRUE, 'msg' => "Rate already fixed !!");
                                    }
                                }
                            }
                            if ($status) {
                                if ($cus_pay_mode['cash_payment'] > 0) {
                                    //Added by Durga 19.05.2023 starts here
                                    if ($generic['installments'] > 1) {
                                        $pay_amt = $cus_pay_mode['cash_payment'] / $generic['installments'];
                                    } else {
                                        $pay_amt = $cus_pay_mode['cash_payment'];
                                    }
                                    //Added by Durga 19.05.2023 ends here
                                    $arrayCashPay = array(
                                        'id_payment' => $status['insertID'],
                                        //	'payment_amount'    => $cus_pay_mode['cash_payment'],//Commented and replaced by Durga 19.05.2023
                                        'payment_amount' => $pay_amt,
                                        'payment_mode' => 'CSH',
                                        'payment_status' => $generic['payment_status'],
                                        'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                        'created_time' => date("Y-m-d H:i:s"),
                                        'created_by' => $this->session->userdata('uid')
                                    );
                                    if (!empty($arrayCashPay)) {
                                        $cashPayInsert = $this->$model->insertData($arrayCashPay, 'payment_mode_details');
                                    }
                                }
                                // Wallet mode Added by Esakkiraja 31.07.2025 starts here
                                if ($is_use_wallet == 1 && $generic['redeem_request'] > 0) {
                                    $arrayCashPay = array(
                                        'id_payment' => $status['insertID'],
                                        'payment_amount' => $generic['redeem_request'],
                                        'payment_mode' => 'REF_WALLET',
                                        'payment_status' => $generic['payment_status'],
                                        'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                        'created_time' => date("Y-m-d H:i:s"),
                                        'created_by' => $this->session->userdata('uid')
                                    );
                                    if (!empty($arrayCashPay)) {
                                        $cashPayInsert = $this->$model->insertData($arrayCashPay, 'payment_mode_details');
                                    }
                                }
                                //Added by Durga 19.05.2023 starts here
                                if (sizeof($card_pay_details) > 0) {
                                    $pay_mode_array = [];
                                    if ($generic['installments'] > 1) {
                                        $pay_amt = $card_pay_details[0]['card_amt'] / $generic['installments'];
                                        $pay_mode_array[] = array(
                                            'id_payment' => $status['insertID'],
                                            'card_type' => $card_pay_details[0]['card_name'],
                                            'payment_amount' => $pay_amt,
                                            'payment_mode' => ($card_pay_details[0]['card_type'] == 1 ? 'CC' : 'DC'),
                                            'card_no' => ($card_pay_details[0]['card_no'] != '' ? $card_pay_details[0]['card_no'] : NULL),
                                            'payment_ref_number' => ($card_pay_details[0]['ref_no'] != '' ? $card_pay_details[0]['ref_no'] : NULL),
                                            'id_pay_device' => ($card_pay_details[0]['id_device'] != '' ? $card_pay_details[0]['id_device'] : NULL),
                                            'payment_status' => $generic['payment_status'],
                                            'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                            'created_time' => date("Y-m-d H:i:s"),
                                            'created_by' => $this->session->userdata('uid')
                                        );
                                        if (!empty($pay_mode_array)) {
                                            $cardPayInsert = $this->$model->insertBatchData($pay_mode_array, 'payment_mode_details');
                                        }
                                    } else {
                                        foreach ($card_pay_details as $card_pay) {
                                            $arrayCardPay[] = array(
                                                'id_payment' => $status['insertID'],
                                                'card_type' => $card_pay['card_name'],
                                                'payment_amount' => $card_pay['card_amt'],
                                                'payment_mode' => ($card_pay['card_type'] == 1 ? 'CC' : 'DC'),
                                                'card_no' => ($card_pay['card_no'] != '' ? $card_pay['card_no'] : NULL),
                                                'payment_ref_number' => ($card_pay['ref_no'] != '' ? $card_pay['ref_no'] : NULL),
                                                'id_pay_device' => ($card_pay['id_device'] != '' ? $card_pay['id_device'] : NULL),
                                                'payment_status' => $generic['payment_status'],
                                                'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                                'created_time' => date("Y-m-d H:i:s"),
                                                'created_by' => $this->session->userdata('uid')
                                            );
                                        }
                                        if (!empty($arrayCardPay)) {
                                            $cardPayInsert = $this->$model->insertBatchData($arrayCardPay, 'payment_mode_details');
                                        }
                                    }
                                }
                                //voucher starts
                                if (sizeof($vch_details) > 0) {
                                    $pay_mode_array = [];
                                    if ($generic['installments'] > 1) {
                                        $pay_amt = $vch_details[0]['payment_amount'] / $generic['installments'];
                                        $pay_mode_array[] = array(
                                            'id_payment' => $status['insertID'],
                                            'payment_amount' => $pay_amt,
                                            'payment_mode' => 'VCH',
                                            'card_no' => ($vch_details[0]['card_no'] != '' ? $vch_details[0]['card_no'] : NULL),
                                            'payment_status' => $generic['payment_status'],
                                            'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                            'created_time' => date("Y-m-d H:i:s"),
                                            'created_by' => $this->session->userdata('uid')
                                        );
                                        if (!empty($pay_mode_array)) {
                                            $cardPayInsert = $this->$model->insertBatchData($pay_mode_array, 'payment_mode_details');
                                        }
                                    } else {
                                        foreach ($vch_details as $vch_pay) {
                                            $arrayvchPay[] = array(
                                                'id_payment' => $status['insertID'],
                                                'payment_amount' => $vch_pay['payment_amount'],
                                                'payment_mode' => 'VCH',
                                                'card_no' => ($vch_pay['card_no'] != '' ? $vch_pay['card_no'] : NULL),
                                                'payment_status' => $generic['payment_status'],
                                                'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                                'created_time' => date("Y-m-d H:i:s"),
                                                'created_by' => $this->session->userdata('uid')
                                            );
                                        }
                                        if (!empty($arrayvchPay)) {
                                            $cardPayInsert = $this->$model->insertBatchData($arrayvchPay, 'payment_mode_details');
                                        }
                                    }
                                }
                                //voucher ends
                                //Added by Durga 19.05.2023 starts here
                                if (sizeof($cheque_details) > 0) {
                                    $pay_mode_array = [];
                                    if ($generic['installments'] > 1) {
                                        $pay_amt = $cheque_details[0]['payment_amount'] / $generic['installments'];
                                        $pay_mode_array[] = array(
                                            'id_payment' => $status['insertID'],
                                            'payment_amount' => $pay_amt,
                                            'payment_status' => $generic['payment_status'],
                                            'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                            'cheque_date' => $cheque_details[0]['cheque_date'] != '' && isset($cheque_details[0]['cheque_date']) ? date('Y-m-d H:i:s', strtotime($cheque_details[0]['cheque_date'])) : date("Y-m-d H:i:s"),  // esakki 11-11
                                            'payment_mode' => 'CHQ',
                                            'bank_IFSC' => ($cheque_details[0]['bank_IFSC'] != '' ? $cheque_details[0]['bank_IFSC'] : NULL),
                                            'cheque_no' => ($cheque_details[0]['cheque_no'] != '' ? $cheque_details[0]['cheque_no'] : NULL),
                                            'bank_name' => ($cheque_details[0]['bank_name'] != '' ? $cheque_details[0]['bank_name'] : NULL),
                                            'bank_branch' => ($cheque_details[0]['bank_branch'] != '' ? $cheque_details[0]['bank_branch'] : NULL),
                                            'bank_IFSC' => ($cheque_details[0]['bank_IFSC'] != '' ? $$cheque_details[0]['bank_IFSC'] : NULL),
                                            'created_time' => date("Y-m-d H:i:s"),
                                            'created_by' => $this->session->userdata('uid')
                                        );
                                        // print_r($pay_mode_array);exit;
                                        if (!empty($pay_mode_array)) {
                                            $cardPayInsert = $this->$model->insertBatchData($pay_mode_array, 'payment_mode_details');
                                        }
                                    } else {
                                        foreach ($cheque_details as $chq_pay) {
                                            $arraychqPay[] = array(
                                                'id_payment' => $status['insertID'],
                                                'payment_amount' => $chq_pay['payment_amount'],
                                                'payment_status' => $generic['payment_status'],
                                                'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                                'cheque_date' => $chq_pay['cheque_date'] != '' && isset($chq_pay['cheque_date']) ? date('Y-m-d H:i:s', strtotime($chq_pay['cheque_date'])) : date("Y-m-d H:i:s"),   // esakki 11-11
                                                'payment_mode' => 'CHQ',
                                                'bank_IFSC' => ($chq_pay['bank_IFSC'] != '' ? $chq_pay['bank_IFSC'] : NULL),
                                                'cheque_no' => ($chq_pay['cheque_no'] != '' ? $chq_pay['cheque_no'] : NULL),
                                                'bank_name' => ($chq_pay['bank_name'] != '' ? $chq_pay['bank_name'] : NULL),
                                                'bank_branch' => ($chq_pay['bank_branch'] != '' ? $chq_pay['bank_branch'] : NULL),
                                                'bank_IFSC' => ($chq_pay['bank_IFSC'] != '' ? $chq_pay['bank_IFSC'] : NULL),
                                                'created_time' => date("Y-m-d H:i:s"),
                                                'created_by' => $this->session->userdata('uid')
                                            );
                                        }
                                        // print_r($arraychqPay);exit;
                                        if (!empty($arraychqPay)) {
                                            $chqPayInsert = $this->$model->insertBatchData($arraychqPay, 'payment_mode_details');
                                        }
                                    }
                                }
                                if (sizeof($net_banking_details) > 0) {
                                    $pay_mode_array = [];
                                    if ($generic['installments'] > 1) {
                                        $pay_amt = $net_banking_details[0]['amount'] / $generic['installments'];
                                        $pay_mode_array[] = array(
                                            'id_payment' => $status['insertID'],
                                            'payment_amount' => $pay_amt,
                                            'payment_status' => $generic['payment_status'],
                                            'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                            'payment_mode' => 'NB',
                                            'payment_ref_number' => ($net_banking_details[0]['ref_no'] != '' ? $net_banking_details[0]['ref_no'] : NULL),
                                            'net_banking_date' => ($net_banking_details[0]['nb_date'] != '' ? $net_banking_details[0]['nb_date'] : NULL),
                                            'NB_type' => ($net_banking_details[0]['nb_type'] != '' ? $net_banking_details[0]['nb_type'] : NULL),
                                            'id_pay_device' => ($net_banking_details[0]['nb_type'] == 3 ? $net_banking_details[0]['id_device'] : NULL),
                                            'id_bank' => ($net_banking_details[0]['nb_type'] == 1 || $net_banking_details[0]['nb_type'] == 2 ? $net_banking_details[0]['id_bank'] : NULL),
                                            'created_time' => date("Y-m-d H:i:s"),
                                            'created_by' => $this->session->userdata('uid')
                                        );
                                        if (!empty($pay_mode_array)) {
                                            $cardPayInsert = $this->$model->insertBatchData($pay_mode_array, 'payment_mode_details');
                                        }
                                    } else {
                                        foreach ($net_banking_details as $nb_pay) {
                                            $arrayNBPay[] = array(
                                                'id_payment' => $status['insertID'],
                                                'payment_amount' => $nb_pay['amount'],
                                                'payment_status' => $generic['payment_status'],
                                                'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                                'payment_mode' => 'NB',
                                                'payment_ref_number' => ($nb_pay['ref_no'] != '' ? $nb_pay['ref_no'] : NULL),
                                                'net_banking_date' => ($nb_pay['nb_date'] != '' ? $nb_pay['nb_date'] : NULL),
                                                'NB_type' => ($nb_pay['nb_type'] != '' ? $nb_pay['nb_type'] : NULL),
                                                'id_pay_device' => ($nb_pay['nb_type'] == 3 ? $nb_pay['id_device'] : NULL),
                                                'id_bank' => ($nb_pay['nb_type'] == 1 || $nb_pay['nb_type'] == 2 ? $nb_pay['id_bank'] : NULL),
                                                //'bank_acc_no'           =>($nb_pay['nb_type']==1 || $nb_pay['nb_type']==2? $nb_pay['acc_no']:NULL),
                                                'created_time' => date("Y-m-d H:i:s"),
                                                'created_by' => $this->session->userdata('uid')
                                            );
                                        }
                                        if (!empty($arrayNBPay)) {
                                            $NbPayInsert = $this->$model->insertBatchData($arrayNBPay, 'payment_mode_details');
                                        }
                                    }
                                }
                                //Added by Durga 19.05.2023 ends here
                                //Added by Durga 19.05.2023 starts here
                                $advan_amout = $_POST['adv']['advance_muliple_receipt'][0];
                                if (count($advan_amout) > 0 && $advan_amout != '') {
                                    //	echo "<pre>";print_r($_POST);exit;echo "</pre>";
                                    $advance_amount_adj = json_decode($advan_amout);
                                    //print_r($advance_amount_adj);exit;
                                    $advance_amt = 0;
                                    $adv_adj_array = [];
                                    if ($generic['installments'] > 1) {
                                        $data_adv_amount = array(
                                            'id_issue_receipt' => $advance_amount_adj[0]->id_issue_receipt,
                                            'id_payment' => $status['insertID'],
                                            'adjusted_for' => 2, // Adjusted in CRM
                                            'utilized_amt' => $advance_amount_adj[0]->adj_amount / $generic['installments'],
                                            'cash_utilized_amt' => $advance_amount_adj[0]->cash_pay,
                                        );
                                        //print_r($data_adv_amount);exit;
                                        $insId_adv_amount = $this->$model->insertData($data_adv_amount, 'ret_advance_utilized');
                                        $adv_adj_array = array(
                                            'id_payment' => $status['insertID'],
                                            'adv_receipt_no' => $advance_amount_adj[0]->id_issue_receipt,
                                            'payment_amount' => $advance_amount_adj[0]->adj_amount / $generic['installments'],
                                            'payment_status' => $generic['payment_status'],
                                            'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                            'payment_mode' => 'ADV_ADJ',
                                            'created_time' => date("Y-m-d H:i:s"),
                                            'created_by' => $this->session->userdata('uid')
                                        );
                                        $this->$model->insertData($adv_adj_array, 'payment_mode_details');
                                    } else {
                                        foreach ($advance_amount_adj as $obj) {
                                            $advance_amt += $obj->adj_amount;
                                            $data_adv_amount = array(
                                                'id_issue_receipt' => $obj->id_issue_receipt,
                                                'id_payment' => $status['insertID'],
                                                'adjusted_for' => 2, // Adjusted in CRM
                                                'utilized_amt' => $obj->adj_amount,
                                                'cash_utilized_amt' => $obj->cash_pay,
                                            );
                                            $insId_adv_amount = $this->$model->insertData($data_adv_amount, 'ret_advance_utilized');
                                            $array_adj_pay = array(
                                                'id_payment' => $status['insertID'],
                                                'payment_amount' => $obj->adj_amount,
                                                'adv_receipt_no' => $obj->id_issue_receipt,
                                                'payment_status' => $generic['payment_status'],
                                                'payment_date' => $date_payment, //date("Y-m-d H:i:s"),
                                                'payment_mode' => 'ADV_ADJ',
                                                'created_time' => date("Y-m-d H:i:s"),
                                                'created_by' => $this->session->userdata('uid')
                                            );
                                            $this->$model->insertData($array_adj_pay, 'payment_mode_details');
                                        }
                                    }
                                }
                                //Added by Durga 19.05.2023 ends here
                                //onpay success common func esakki 10102025
                                $this->chit_transaction_model->onPayTranStream($status['insertID']);
                                //Advance Adjusement
                                //purchase_status-2 Chit Deposit
                                if ($generic['chit_deposit'] == 1 && !empty($est_details)) {
                                    $est_details = $_POST['estimation'];
                                    if (!empty($est_details)) {
                                        foreach ($est_details['estimation_id'] as $key => $val) {
                                            $old_metal_data = array('id_payment' => $status['insertID'], 'est_id' => $est_details['estimation_id'][$key]);
                                            $old_metal_insert = $this->$model->insertData($old_metal_data, 'payment_old_metal');
                                            if ($old_metal_insert) {
                                                $this->$model->update_data(array('purchase_status' => 2, 'bill_id' => NULL), 'est_id', $est_details['estimation_id'][$key], 'ret_estimation_old_metal_sale_details');
                                            }
                                        }
                                    }
                                }
                                //Check Rate Fixing Settings
                                $scheme_settings = $this->$model->get_scheme_details($generic['id_scheme_account']);
                                if ($scheme_settings['otp_price_fix_type'] == 1 && $scheme_settings['one_time_premium'] == 1) {
                                    $this->$model->update_data(array('firstPayment_amt' => $amount + $generic['est_amt']), 'id_scheme_account', $generic['id_scheme_account'], 'scheme_account');
                                }
                                // update gift status as issued when payment status is success...
                                $temp = 0;
                                $gift_data = $this->account_model->get_gift_issued($generic['id_scheme_account']);
                                if (count($gift_data) > 0) {
                                    foreach ($gift_data as $gift) {
                                        if ($gift['type'] === 'GIFT') {
                                            if ($gift['gift_status'] == 0) {
                                                $updated_status = $this->account_model->update_gift_master($gift['quantity'], $gift['id_gift']);
                                                $temp = 1;
                                            }
                                        }
                                    }
                                    if ($temp == 1) {
                                        $upd_data = array('status' => 1);
                                        $upd_gift_status = $this->$model->upd_gift_status($upd_data, $generic['id_scheme_account']);
                                    }
                                }
                                //agent benefits credit
                                if ($generic['id_agent'] != '' && $generic['agent_code'] != '' && $generic['agent_refferal'] == 1) {
                                    $type = 2; //1- employee 2- agent
                                    $credit_for = 0; // credit while new scheme join with payment
                                    $agent_refral = $this->$model->get_Incentivedata($generic['id_scheme'], $generic['id_scheme_account'], $type, $status['insertID'], $credit_for);
                                    if ($agent_refral > 0) {
                                        foreach ($agent_refral as $ag) {
                                            if ($ag['referal_amount'] > 0 && $emp['credit_for'] == 0) {
                                                $res = $this->insertAgentIncentive($ag, $generic['id_scheme_account'], $ag['id_payment'], $generic['id_agent']);
                                            }
                                        }
                                    }
                                }
                                $payid = array();
                                $pay_status_array = array(
                                    'id_payment' => (isset($status['insertID']) ? $status['insertID'] : NULL),
                                    'id_status_msg' => (isset($generic['payment_status']) ? $generic['payment_status'] : NULL),
                                    'charges' => $amount,
                                    'id_employee' => $this->session->userdata('uid'),
                                    'date_upd' => date('Y-m-d H:i:s')
                                );
                                $payid[] = $status['insertID'];
                                //print_r($payid); exit; 
                                $ppsm = $this->$model->payment_statusDB("insert", "", $pay_status_array);
                                if ($generic['payment_status'] == 1) {
                                    if ($this->config->item('integrationType') == 1) {
                                        $this->insert_common_data_jil($status['insertID']);
                                    } else if ($this->config->item('integrationType') == 2) {
                                        $this->insert_common_data($status['insertID']);
                                    } else if ($this->config->item('integrationType') == 5) {
                                        //echo "<pre>";print_r($_POST);
                                        $AccDt = array(
                                            "id_payment" => $status['insertID'],
                                            "reference_no" => $generic['reference_no'],
                                            "sync_scheme_code" => $generic['sync_scheme_code'],
                                            "amount" => $pay_array['payment_amount'],
                                            "nominee_name" => $generic['nominee_name'],
                                            "nominee_relationship" => $generic['nominee_relationship'],
                                            "nominee_address1" => $generic['nominee_address1'],
                                            "nominee_address2" => $generic['nominee_address2'],
                                            "nominee_mobile" => $generic['nominee_mobile'],
                                            "emp_name" => $generic['emp_name'],
                                            "referal_code" => $generic['referal_code'],
                                            "scheme_acc_number" => $acdata['scheme_acc_number']
                                        );
                                        //echo "<pre>";print_r($AccDt);exit;
                                        $offline_tran_uniqueid = $this->generateTranUniqueId($AccDt, $pay_array['payment_amount']);
                                        $payDt = array(
                                            "id_payment" => $status['insertID'],
                                            "id_scheme_account" => $pay_array['id_scheme_account'],
                                            "scheme_acc_number" => $acdata['scheme_acc_number'],
                                            "offline_tran_uniqueid" => $offline_tran_uniqueid,
                                            "warehouse" => $this->payment_model->getWarehouse($pay_array['id_branch']),
                                            "payment_amount" => $pay_array['payment_amount'],
                                            "id_transaction" => "ADM_REF_" . $status['insertID'],
                                            "payment_type" => $pay_array['payment_type'],
                                            "payment_mode" => $pay_array['payment_mode']
                                        );
                                        $this->genKhimjiAcNoOrReceiptNo($payDt);
                                        // Write log in case of API call failure
                                        if (!is_dir($this->log_dir . '/acme')) {
                                            mkdir($this->log_dir . '/acme', 0777, true);
                                        }
                                        $log_path = $this->log_dir . '/acme/' . date("Y-m-d") . '.txt';
                                        $logData = "\n" . date('d-m-Y H:i:s') . "\n ADMIN : Account and payment data to acme \n Account Post : " . json_encode($AccDt, true) . "\n Payment Post : " . json_encode($payDt, true);
                                        file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
                                    }
                                    //$payData =  $this->$model->getPpayment_data($status['insertID']);  
                                    // insert wallet transactions and update intermediate wallet tables
                                    if ($used_wallet) {
                                        if ($redeemed_amount > 0) {
                                            $transData = array();
                                            $pay = $this->$model->getWalletPaymentContent($generic['id_scheme_account']);
                                            if ($redeemed_amount > 0) {
                                                $transData = array(
                                                    'mobile' => $pay['mobile'],
                                                    'actual_trans_amt' => $totalamount,
                                                    'available_points' => ($pay['isAvail'] == 0 ? 0 : $pay['available_points']),
                                                    'isAvail' => ($pay['isAvail'] == 0 ? 0 : 1),
                                                    'redeemed_amount' => $redeemed_amount,
                                                    'txnid' => time() . '-' . $generic['id_scheme_account'] . '-ADM-D',
                                                    'branch' => $pay['branch'],
                                                    'walletIntegration' => $pay['walletIntegration'],
                                                    'wallet_points' => $pay['wallet_points'],
                                                    'wallet_amt_per_points' => $pay['wallet_amt_per_points'],
                                                    'wallet_balance_type' => $pay['wallet_balance_type']
                                                );
                                                if (!empty($transData)) {
                                                    $this->insertWalletTrans($transData);
                                                }
                                            }
                                            $submitpay_flag = FALSE;
                                            $this->session->set_flashdata('successMsg', 'Payment successful');
                                        }
                                    }
                                    //send sms/mail to Customer 
                                    $mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";
                                    $mailtype = 2;
                                    $this->sendSMSMail('3', $payData, $mailSubject, $mailtype, $status['insertID']);
                                } else {
                                    //send sms/mail to Customer 
                                    //$payData =  $this->$model->getPpayment_data($status['insertID']);  
                                    //$mailSubject = "Reg- ".$this->company['company_name']." saving scheme payment details";
                                    // $mailtype= 3;
                                    // $this->sendSMSMail('7',$payData,$mailSubject,$mailtype,$status['insertID']);
                                }
                            }

                            // NOTE: Due date update is already handled by onPayTranStream() -> updatedue_details()
                            // at chit_transaction_model.php:603. Calling get_due_date again here caused a duplicate
                            // call that picked the NEXT unpaid due (e.g., May instead of April) because the first call
                            // already saved the correct due_date, making the NOT IN subquery skip it.
                            // 
                            // If topup_slab_data is needed for topup schemes (installment_cycle == 4), 
                            // that logic should be added to updatedue_details() in chit_transaction_model instead.

                            
                            //RHR scheme starts...	// esakki 11-11	
                            // $dt_pay = date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $date_payment)));
                            // $ins_cycle = $this->payment_model->get_due_date($dueType, $dt_pay, $generic['id_scheme_account']);
                            // print_r($ins_cycle);exit;
                            // // print_r($this->db->last_query());exit;
                            // if (sizeof($ins_cycle[0]) > 0) {
                            //     $cycle_data = array(
                            //         'due_date' => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
                            //         'due_date_to' => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
                            //         'grace_date' => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
                            //         'installment' => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
                            //         'is_limit_exceed' => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
                            //     );
                            //     if ($sch_data['is_topup_scheme'] == 1 && $sch_data['installment_cycle'] == 4) {
                            //         $cycle_data['due_type'] = (!empty($ins_cycle[0]['due_type']) ? $ins_cycle[0]['due_type'] : 'ND');
                            //         $cycle_data['topup_slab_data'] = json_encode(
                            //             array(
                            //                 'slab_day_start' => (isset($ins_cycle[0]['range_from']) ? $ins_cycle[0]['range_from'] : 0),
                            //                 'slab_day_end' => (isset($ins_cycle[0]['range_to']) ? $ins_cycle[0]['range_to'] : 0),
                            //                 'slab_value' => (isset($ins_cycle[0]['payable_value']) ? $ins_cycle[0]['payable_value'] : 0),
                            //                 'slab_type' => (isset($ins_cycle[0]['payable_type']) ? $ins_cycle[0]['payable_type'] : 0),
                            //             )
                            //         );
                            //     }
                            //     // echo '<pre>';print_r($cycle_data);exit;
                            //     $this->payment_model->updData($cycle_data, 'id_payment', $status['insertID'], 'payment');
                            //     }
                            //RHR scheme ends....
                        }
                        //update paid installments against account...
                        $paid = $this->payment_model->getPaidInsData($generic['id_scheme_account']);
                        //print_r($this->db->last_query());exit;
                        if (sizeof($paid) > 0) {
                            $paid_ins = array('total_paid_ins' => $paid['paid_installments']);
                            $this->payment_model->updData($paid_ins, 'id_scheme_account', $generic['id_scheme_account'], 'scheme_account');
                        }
                        //update due_monthyear....
                        $upd_duemonthyear = $this->update_dueYear($generic['id_scheme_account']);
                        // 			var_dump($this->db->trans_status());exit;
                        if ($this->db->trans_status() === TRUE)
                        //  if($status['insertID'] > 0)
                        {
                            $this->db->trans_commit();
                            //send sms/mail to Customer 
                            $mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";
                            $mailtype = 2;
                            $this->sendSMSMail('3', $payData, $mailSubject, $mailtype, $status['insertID']);
                            $this->session->set_flashdata('chit_alert', array('message' => 'Payment added successfully', 'class' => 'success', 'title' => 'Scheme Payment'));
                            $data['payid'] = $payid;
                            $data['type'] = $submit_type;
                            $data['payment_status'] = $generic['payment_status'];
                            $data['is_topup_scheme'] = $sch_data['is_topup_scheme'];
                            $data['id_scheme_account'] = $generic['id_scheme_account'];
                        } else {
                            $this->db->trans_rollback();
                            // CREATE LOG
                            if (!is_dir($this->log_dir . '/manual_rollback')) {
                                mkdir($this->log_dir . '/manual_rollback', 0777, true);
                            }
                            $log_path = $this->log_dir . '/manual_rollback/create_payment_rollback' . date("Y-m-d") . '.txt';
                            $lg_data = "\n CP --" . date('Y-m-d H:i:s') . " -- : " . json_encode($_POST);
                            $lg_data .= "\n CP --" . date('Y-m-d H:i:s') . " -- : " . json_encode($this->db->_error_message());
                            file_put_contents($log_path, $lg_data, FILE_APPEND | LOCK_EX);
                        }
                    } else {
                        $this->session->set_flashdata('chit_alert', array('message' => 'Unable To Proceed Your Request.Invalid Form Submit', 'class' => 'danger', 'title' => 'Scheme Payment'));
                    }
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Invalid Payment Amount, try again', 'class' => 'danger', 'title' => 'Scheme Payment'));
                }
                $model = self::PAY_MODEL;
                $id = $generic['id_scheme_account'];
                $payment_details = $this->$model->getPayDetails($id);
                $data['payment_details'] = $payment_details;
                //lines added by Durga (Gopal task) 15.05.2023 ends here
                //print_r($data);exit;
                echo json_encode($data);
                break;
            case 'Save':
                $pay = $this->input->post('pay');
                // $receipt_no = $this->generate_receipt_no();
                if ($this->$model->get_rptnosettings() == 0) {
                    $receipt_no = $this->generate_receipt_no();
                } else {
                    $receipt_no;
                }
                $txnid = uniqid(time());
                $pay_array = array(
                    'id_scheme_account' => (isset($pay['id_scheme_account']) ? $pay['id_scheme_account'] : NULL),
                    'id_employee' => $this->session->userdata('uid'),
                    'id_transaction' => $txnid,
                    'date_payment' => (isset($pay['date_payment']) ? date('Y-m-d', strtotime(str_replace("/", "-", $pay['date_payment']))) : NULL),
                    'custom_entry_date' => (isset($pay['custom_entry_date']) ? date('Y-m-d', strtotime(str_replace("/", "-", $pay['custom_entry_date']))) : NULL),
                    'payment_type' => (isset($pay['payment_type']) ? $pay['payment_type'] : NULL),
                    'payment_mode' => (isset($pay['payment_mode']) ? $pay['payment_mode'] : NULL),
                    'payment_amount' => (isset($pay['payment_amount']) ? $pay['payment_amount'] : 0),
                    'metal_rate' => (isset($pay['metal_rate']) ? $pay['metal_rate'] : 0),
                    'metal_weight' => (isset($pay['metal_weight']) ? $pay['metal_weight'] : 0.000),
                    'payment_ref_number' => (isset($pay['payment_ref_number']) ? $pay['payment_ref_number'] : NULL),
                    'id_post_payment' => (isset($pay['id_post_payment']) ? $pay['id_post_payment'] : NULL),
                    'id_drawee' => (isset($pay['id_drawee']) ? $pay['id_drawee'] : NULL),
                    'remark' => (isset($pay['remark']) ? $pay['remark'] : NULL),
                    'payment_status' => (isset($pay['payment_status']) ? $pay['payment_status'] : 1),
                    'receipt_no' => ($pay['payment_status'] == 1 ? $receipt_no : NULL)
                );
                $status = $this->$model->paymentDB("insert", "", $pay_array);
                if ($status) {
                    $pay_status_array = array(
                        'id_payment' => (isset($status['insertID']) ? $status['insertID'] : NULL),
                        'id_status_msg' => (isset($pay['payment_status']) ? $pay['payment_status'] : NULL),
                        'charges' => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                        'id_employee' => $this->session->userdata('uid'),
                        'date_upd' => date('Y-m-d H:i:s')
                    );
                    $ppsm = $this->$model->payment_statusDB("insert", "", $pay_status_array);
                    if ($pay['payment_status'] == 1) {
                        //send sms/mail to Customer if success
                        $payData = $this->$model->getPpayment_data($status['insertID']);
                        $mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";
                        $mailtype = 2;
                        $this->sendSMSMail('3', $payData, $mailSubject, $mailtype, $status['insertID']);
                    } else {
                        //send pay status sms/mail to Customer 
                        $payData = $this->$model->getPpayment_data($status['insertID']);
                        $mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";
                        $mailtype = 3;
                        $this->sendSMSMail('7', $payData, $mailSubject, $mailtype, $status['insertID']);
                    }
                    $log_data = array(
                        'id_log' => $this->id_log,
                        'event_date' => date("Y-m-d H:i:s"),
                        'module' => 'Payment',
                        'operation' => 'Add',
                        'record' => $status['insertID'],
                        'remark' => 'Payment added successfully'
                    );
                    $this->$log_model->log_detail('insert', '', $log_data);
                    $this->session->set_flashdata('chit_alert', array('message' => 'Payment added successfully', 'class' => 'success', 'title' => 'Scheme Payment'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Scheme Payment'));
                }
                redirect('payment/list');
                break;
            case 'Update':
                $pay = $this->input->post('pay');
                $cusData = $this->$accountmodel->get_customer_acc($pay['id_scheme_account']);
                $check_status = $this->$model->paymentDB("get", $id);
                $submit_type = $this->input->post('type1');
                // $receipt_no = $this->generate_receipt_no();
                if ($this->$model->get_rptnosettings() == 1) {
                    $receipt_no = $this->generate_receipt_no($cusData['id_scheme'], $cusData['branch']);
                } else {
                    $receipt_no = NULL;
                }
                $pay_array = array(
                    'id_scheme_account' => (isset($pay['id_scheme_account']) ? $pay['id_scheme_account'] : NULL),
                    'id_employee' => $this->session->userdata('uid'),
                    // 			'date_payment'		=>	(isset($pay['date_payment'])? date('Y-m-d',strtotime(str_replace("/","-",$pay['date_payment']))): NULL), 	
                    'payment_type' => (isset($pay['payment_type']) ? $pay['payment_type'] : NULL),
                    'payment_mode' => (isset($pay['payment_mode']) ? $pay['payment_mode'] : NULL),
                    'payment_amount' => (isset($pay['payment_amount']) ? $pay['payment_amount'] : 0),
                    'metal_rate' => (isset($pay['metal_rate']) ? $pay['metal_rate'] : 0),
                    'metal_weight' => (isset($pay['metal_weight']) ? $pay['metal_weight'] : 0.000),
                    'payment_ref_number' => (isset($pay['payment_ref_number']) ? $pay['payment_ref_number'] : 0.000),
                    'remark' => (isset($pay['remark']) ? $pay['remark'] : NULL),
                    'payment_status' => (isset($pay['payment_status']) ? $pay['payment_status'] : 2),
                    //'receipt_no'    =>  ($pay['payment_status']==1?($check_status['receipt_no'] == '' ? $receipt_no : $check_status['receipt_no']):''),
                    'receipt_no' => ($pay['payment_status'] == 1 && $check_status['receipt_no_set'] == 1 && ($pay['receipt_no'] == 0 || $pay['receipt_no'] == NULL) ? $receipt_no : ($pay['payment_status'] == 1 ? ($check_status['receipt_no'] == '' ? $receipt_no : $check_status['receipt_no']) : ''))
                );
                // delete data in customer_reg and transaction if changed from success to other status
                if ($check_status['id_payment_status'] == 1 && $pay['payment_status'] !== 1) {
                    if ($pay['payment_status'] == 4) { // Canceled
                        if ($this->config->item('integrationType') == 2) {
                            $pay['id_payment'] = $id;
                            $this->syncapi_model->updPayStatusInTrans($pay);
                        }
                    }
                    // $this->deleteCusandPaydata($id);
                }
                $status = $this->$model->paymentDB("update", $id, $pay_array);
                //print_r($this->db->last_query());exit;
                $scheme_acc_no = $this->$set_model->accno_generatorset();
                if (($status['status'] == 1 && $pay['scheme_acc_number'] == 'Not Allocated' && $pay['payment_status'] == 1) && ($scheme_acc_no['status'] == 1 && $scheme_acc_no['schemeacc_no_set'] == 0)) {
                    $acdata = $this->$model->isAcnoAvailable($pay_array['id_scheme_account']);
                    $scheme_acc_number = $this->account_model->account_number_generator($acdata['id_scheme'], $acdata['branch'], '');
                    if ($scheme_acc_number != NULL) {
                        $updateData['scheme_acc_number'] = $scheme_acc_number;
                    }
                    if ($scheme_acc_no['gent_clientid'] == 1 && $scheme_acc_number != NULL) {
                        $updateData['ref_no'] = $this->config->item('cliIDcode') . "/" . $cusData['group_code'] . "/" . $scheme_acc_number;
                    }
                    if ($scheme_acc_no['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2) { //upd client id & acc no cus reg table based on the settings//
                        $updateDatacus['scheme_acc_number'] = $scheme_acc_number;
                        //	$updateDatacus['ref_no'] = $this->config->item('cliIDcode')."/".$cusData['group_code']."/".$scheme_acc_number;
                        $updateDatacus['sync_scheme_code'] = $cusData['sync_scheme_code'];
                    }
                    if ($scheme_acc_no['gent_clientid'] == 1 && $scheme_acc_no['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2) {
                        $updateDatacus['ref_no'] = $this->config->item('cliIDcode') . "/" . $cusData['group_code'] . "/" . $scheme_acc_number;
                    }
                    $updSchAc = $this->account_model->update_account($updateData, $pay['id_scheme_account']);
                    $updcusreg = $this->account_model->update_cusreg($updateDatacus, $generic['id_scheme_account']); //acc no & clientid upd to cus reg tab//HH
                    $updtrans = $this->account_model->update_trans($updateDatacus, $generic['id_scheme_account']); //Client Id upd to trans tab//
                }
                if ($status) {
                    if (in_array($check_status['due_type'], array('S', 'P', 'A')) && $check_status['id_payment_status'] != $pay['payment_status'] && $pay['payment_type'] == 'Payu Checkout') {
                        $getPayments = $this->$model->getData_matchedRefno($pay['payment_ref_number']);
                        $array = array('payment_status' => $pay_array['payment_status'], 'receipt_no' => '');
                        $stat = $this->$model->paymentDB("update", $getPayments['parentId'], $array);
                        if ($stat) {
                            foreach ($getPayments['splittedId'] as $payid) {
                                $delete = $this->$model->paymentDB("delete", $payid['id_payment']);
                                if ($delete) {
                                    $log_data = array(
                                        'id_log' => $this->id_log,
                                        'event_date' => date("Y-m-d H:i:s"),
                                        'module' => 'Payment',
                                        'operation' => 'Delete',
                                        'record' => $payid['id_payment'],
                                        'remark' => 'Splitted Payment deleted successfully'
                                    );
                                    $this->$log_model->log_detail('insert', '', $log_data);
                                }
                            }
                        }
                    }
                    $log_data = array(
                        'id_log' => $this->id_log,
                        'event_date' => date("Y-m-d H:i:s"),
                        'module' => 'Payment',
                        'operation' => 'Edit',
                        'record' => $status['updateID'],
                        'remark' => 'Payment updated successfully'
                    );
                    $this->$log_model->log_detail('insert', '', $log_data);
                    $paymentid = array();
                    $pay_status_array = array(
                        'id_payment' => (isset($status['updateID']) ? $status['updateID'] : NULL),
                        'id_status_msg' => (isset($pay['payment_status']) ? $pay['payment_status'] : NULL),
                        'charges' => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                        'id_employee' => $this->session->userdata('uid'),
                        'date_upd' => date('Y-m-d H:i:s')
                    );
                    //print_r($this->db->last_query());exit;
                    $paymentid[] = $status['updateID'];
                    $ppsm = $this->$model->payment_statusDB("insert", "", $pay_status_array);
                    if ($check_status['id_payment_status'] != $pay['payment_status']) {
                        if ($pay['payment_status'] == 1) {
                            if ($this->config->item('integrationType') == 1) {
                                $this->insert_common_data_jil($status['updateID']);
                            } else if ($this->config->item('integrationType') == 2) {
                                $this->insert_common_data($status['updateID']);
                            }
                            //send sms/mail to Customer if success
                            $payData = $this->$model->getPpayment_data($status['updateID']);
                            $mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";
                            $mailtype = 2;
                            $this->sendSMSMail('3', $payData, $mailSubject, $mailtype, $status['updateID']);
                        } else {
                            //send pay status sms/mail to Customer 
                            $payData = $this->$model->getPpayment_data($status['updateID']);
                            $mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";
                            $mailtype = 3;
                            $this->sendSMSMail('7', $payData, $mailSubject, $mailtype, $status['updateID']);
                        }
                    }
                    $data['paymentid'] = $paymentid;
                    $data['id_scheme_account'] = $pay['id_scheme_account'];
                    $data['type1'] = $submit_type;
                    $data['payment_status'] = $pay['payment_status'];
                    echo json_encode($data);
                    // $this->session->set_flashdata('chit_alert', array('message' => 'Payment updated successfully','class' => 'success','title'=>'Scheme Payment'));
                } else {
                    $data['paymentid'] = $paymentid;
                    $data['type1'] = 0;
                    $data['id_scheme_account'] = $pay['id_scheme_account'];
                    $data['payment_status'] = $pay['payment_status'];
                    echo json_encode($data);
                }
                //redirect('payment/list');
                break;
            case 'Delete':
                $status = $this->$model->paymentDB("delete", $id);
                if ($status) {
                    $log_data = array(
                        'id_log' => $this->id_log,
                        'event_date' => date("Y-m-d H:i:s"),
                        'module' => 'Payment',
                        'operation' => 'Delete',
                        'record' => $id,
                        'remark' => 'Payment deleted successfully'
                    );
                    $this->$log_model->log_detail('insert', '', $log_data);
                    $this->session->set_flashdata('chit_alert', array('message' => 'Payment deleted successfully', 'class' => 'success', 'title' => 'Scheme Payment'));
                } else {
                    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Scheme Payment'));
                }
                redirect('payment/list');
                break;
            default:
                $set_model = self::SET_MODEL;
                $access = $this->$set_model->get_access('payment/list');
                $profile = $this->session->userdata('profile');
                if (!empty($_POST)) {
                    $range['from_date'] = $this->input->post('from_date');
                    $range['to_date'] = $this->input->post('to_date');
                    $date_type = $this->input->post('date_type');
                    $items = $this->$model->payment_list_range($range['from_date'], $range['to_date'], '', '', $date_type);
                } else {
                    $items = $this->$model->payment_list(($id != NULL ? $id : ''), 50);
                }
                $payment = array(
                    'access' => $access,
                    'data' => $items,
                    'profile' => $profile,
                );
                echo json_encode($payment);
        }
    }
    function send_notification($mobile, $serv_code, $id)
    {
        $model = self::PAY_MODEL;
        $sms_model = self::SMS_MODEL;
        $noti = $this->$model->get_notiContent('SCH_CMP', $id);
        $data['token'] = $this->$model->getnotificationids($mobile);
        $i = 1;
        foreach ($data['token'] as $row) {
            if (sizeof($row['token']) > 0) {
                $targetUrl = '#/app/notification';
                $arraycontent = array(
                    'token' => $row['token'],
                    'notification_service' => 12,
                    'noti_code' => 'SCH_CMP',
                    'header' => $noti['header'],
                    'message' => $noti['message'],
                    'mobile' => $row['mobile'],
                    'footer' => $noti['footer'],
                    'id_customer' => $row['id_customer'],
                    'targerURL' => $targetUrl,
                    'noti_img' => ''
                );
                //print_r($arraycontent);exit;
                $res = $this->send_singlealert_notification($arraycontent);
                //print_r($res);exit;
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
        $registrationIds = array();
        $registrationIds[0] = $alertdetails['token'];
        $content = array(
            "en" => $alertdetails['message']
        );
        $targetUrl = '#/app/notification';
        $fields = array(
            'app_id' => $this->config->item('app_id'),
            'include_player_ids' => $registrationIds,
            'contents' => $content,
            'headings' => array("en" => $alertdetails['header']),
            'subtitle' => array("en" => $alertdetails['footer']),
            'data' => array('targetUrl' => $targetUrl, 'noti_service' => $alertdetails['notification_service'], 'noti_code' => $alertdetails['noti_code'], 'mobile' => $alertdetails['mobile']),
            'big_picture' => (isset($alertdetails['noti_img']) ? $alertdetails['noti_img'] : " ")
        );
        $auth_key = $this->config->item('authentication_key');
        //print_r($auth_key);exit;
        $fields = json_encode($fields);
        //print_r($fields);exit;
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
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    function amount_to_weight($to_pay)
    {
        $converted_metal_wgt = $to_pay['sch_amt'] / $to_pay['metal_rate'];
        return $converted_metal_wgt;
    }
    function postdate_payment($type, $id = "")
    {
        $model = self::PAY_MODEL;
        $log_model = self::LOG_MODEL;
        $set_model = self::SET_MODEL;
        $sms_model = self::SMS_MODEL;
        $company = $this->$set_model->get_company();
        $mail_model = self::MAIL_MODEL;
        $total_inserted = array();
        switch ($type) {
            case 'List':
                $data['pay'] = $this->$model->paymentDB();
                $data['main_content'] = self::PAY_VIEW . "postdated/list";
                $access = $this->$set_model->get_access('postdated/payment/list');
                $this->load->view('layout/template', $data);
                break;
            case 'View':
                $data['main_content'] = self::PAY_VIEW . "postdated/form";
                $this->load->view('layout/template', $data);
                break;
            case 'Save':
                $payments = $this->input->post('pay');
                /* echo "<pre>";
                 print_r($payments); echo "</pre>";exit;*/
                foreach ($payments as $pay) { //print_r($pay);print_r($pay['id_scheme_account']);exit;
                    $pay_array = array(
                        'date_payment' => (isset($pay['date_payment']) ? date('Y-m-d', strtotime(str_replace("/", "-", $pay['date_payment']))) : NULL),
                        'cheque_no' => (isset($pay['cheque_no']) ? $pay['cheque_no'] : ''),
                        'id_employee' => $this->session->userdata('uid'),
                        'id_scheme_account' => (isset($pay['id_scheme_account']) ? $pay['id_scheme_account'] : ''),
                        'pay_mode' => (isset($pay['pay_mode']) ? $pay['pay_mode'] : ''),
                        'payee_bank' => (isset($pay['payee_bank']) ? $pay['payee_bank'] : ''),
                        'payee_branch' => (isset($pay['payee_branch']) ? $pay['payee_branch'] : ''),
                        'payee_ifsc' => (isset($pay['payee_ifsc']) ? $pay['payee_ifsc'] : ''),
                        'payee_acc_no' => (isset($pay['payee_acc_no']) ? $pay['payee_acc_no'] : ''),
                        'id_drawee' => (isset($pay['id_drawee']) ? $pay['id_drawee'] : ''),
                        'amount' => (isset($pay['amount']) ? $pay['amount'] : ''),
                        'payment_status' => 7
                    );
                    //print_r($pay_array);exit;
                    if ($pay_array['id_scheme_account'] != '') {
                        $status = $this->$model->postdated_paymentDB("insert", "", $pay_array);
                        $log_data = array(
                            'id_log' => $this->id_log,
                            'event_date' => date("Y-m-d H:i:s"),
                            'module' => 'Post-Dated Payment',
                            'operation' => 'Add',
                            'record' => $status['updateID'],
                            'remark' => 'Post-Dated Payment added successfully'
                        );
                        $this->$log_model->log_detail('insert', '', $log_data);
                        $pay_status_array = array(
                            'id_post_payment' => (isset($status['insertID']) ? $status['insertID'] : NULL),
                            'id_status_msg' => 7,
                            'id_employee' => $this->session->userdata('uid'),
                            'date_upd' => date('Y-m-d H:i:s')
                        );
                        $ppsm = $this->$model->payment_statusDB("insert", "", $pay_status_array);
                        $total_inserted[] = $ppsm['insertID'];
                    }
                }
                $serviceID = 5;
                $service = $this->$set_model->get_service($serviceID);
                $payData = $this->$model->post_paymentlist($status['insertID']);
                $company = $this->$set_model->get_company();
                $mail_model = self::MAIL_MODEL;
                $id = $status['insertID'];
                $data = $this->$sms_model->get_SMS_data($serviceID, '', $id);
                $mobile = $data['mobile'];
                $message = $data['message'];
                if ($service['serv_sms'] == 1) {
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
                    $smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $params];
                    $this->sms_model->send_whatsApp_message($mobile, $smsData);
                }
                if ($service['serv_email'] == 1 && $payData['email'] != '') {
                    $data['payData'] = $payData;
                    $data['company_details'] = $company;
                    $data['type'] = 4;
                    $data['total_chq'] = count($total_inserted);
                    $to = $payData['email'];
                    ;
                    $subject = "Reg- " . $company['company_name'] . " saving scheme payment details";
                    $message = $this->load->view('include/emailPayment', $data, true);
                    $sendEmail = $this->$mail_model->send_email($to, $subject, $message);
                }
                $this->session->set_flashdata('chit_alert', array('message' => 'Post dated payment added successfully', 'class' => 'success', 'title' => 'Post dated payment'));
                redirect('postdated/payment/list');
                break;
            case 'Update':
                //to update presented data in dashboard
                $payments = (array) json_decode($this->input->post("postpay_data"));
                foreach ($payments as $pay) {
                    $update_array = array('payment_status' => $pay->payment_status);
                    $id_post_payment = $pay->id_payment;
                    $ppay_insert = $this->$model->postdated_paymentDB("update", $id_post_payment, $update_array);
                    //insert status Table
                    $log_data = array(
                        'id_log' => $this->id_log,
                        'event_date' => date("Y-m-d H:i:s"),
                        'module' => 'Post-Dated Payment',
                        'operation' => 'Edit',
                        'record' => $id_post_payment,
                        'remark' => 'Post-Dated Payment edited successfully'
                    );
                    $this->$log_model->log_detail('insert', '', $log_data);
                    $pay_status_array = array(
                        'id_post_payment' => (isset($pay->id_payment) ? $pay->id_payment : NULL),
                        'id_status_msg' => (isset($pay->payment_status) ? $pay->payment_status : NULL),
                        'id_employee' => $this->session->userdata('uid'),
                        'date_upd' => date('Y-m-d H:i:s')
                    );
                    $ppsm = $this->$model->payment_statusDB("insert", "", $pay_status_array);
                    //insert in payment Table on success status
                    if ($pay->payment_status == 1) {
                        //$receipt_no = $this->generate_receipt_no();
                        if ($this->$model->get_rptnosettings() == 0) {
                            $receipt_no = $this->generate_receipt_no();
                        } else {
                            $receipt_no;
                        }
                        $pay_array = array(
                            'id_scheme_account' => (isset($pay->id_scheme_account) ? $pay->id_scheme_account : NULL),
                            'date_payment' => (isset($pay->date_payment) ? date('Y-m-d', strtotime(str_replace("/", "-", $pay->date_payment))) : NULL),
                            'payment_type' => "PDC/ECS",
                            'payment_mode' => (isset($pay->pay_mode) ? $pay->pay_mode : NULL),
                            'payment_amount' => (isset($pay->payment_amount) ? $pay->payment_amount : 0.00),
                            'metal_weight' => (isset($pay->weight) ? $pay->weight : 0.000),
                            'metal_rate' => (isset($pay->metal_rate) ? $pay->metal_rate : 0.000),
                            'cheque_no' => (isset($pay->cheque_no) ? $pay->cheque_no : NULL),
                            'bank_acc_no' => (isset($pay->bank_acc_no) ? $pay->bank_acc_no : NULL),
                            'bank_name' => (isset($pay->bank_name) ? $pay->bank_name : NULL),
                            'payment_ref_number' => (isset($pay->payment_ref_number) ? $pay->payment_ref_number : NULL),
                            'payment_status' => (isset($pay->payment_status) ? $pay->payment_status : NULL),
                            'remark' => (isset($pay->remark) ? $pay->remark : NULL),
                            'receipt_no' => $receipt_no
                        );
                        $pay_insert = $this->$model->paymentDB("insert", "", $pay_array);
                        if ($pay_insert['status']) {
                            if ($this->config->item('integrationType') == 1) {
                                $this->insert_common_data_jil($pay_insert['insertID']);
                            } else if ($this->config->item('integrationType') == 2) {
                                $this->insert_common_data($pay_insert['insertID']);
                            }
                            $acdata = $this->$model->isAcnoAvailable($pay_array['id_scheme_account']);
                            if ($acdata['status']) {
                                $scheme_acc_number = $this->account_model->account_number_generator($acdata['id_scheme'], $acdata['branch'], '');
                                if ($scheme_acc_number != NULL) {
                                    $updateData['scheme_acc_number'] = $scheme_acc_number;
                                }
                                $updSchAc = $this->account_model->update_account($updateData, $pay_array['id_scheme_account']);
                            }
                            $payData = $this->$model->getPpayment_data($pay_insert['insertID']);
                            $mailSubject = $subject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";
                            $mailtype = 3;
                            $this->sendSMSMail('6', $payData, $mailSubject, $mailtype, $pay_status_array['id_post_payment']);
                        }
                        //log entry
                        $log_data = array(
                            'id_log' => $this->id_log,
                            'event_date' => date("Y-m-d H:i:s"),
                            'module' => 'Payment',
                            'operation' => 'Add',
                            'record' => $pay_insert['insertID'],
                            'remark' => 'Payment added successfully'
                        );
                        $this->$log_model->log_detail('insert', '', $log_data);
                    }
                    if ($ppay_insert['status']) {
                        $payData = $this->$model->getPostpayment_data($pay_status_array['id_post_payment']);
                        $mailSubject = $subject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";
                        $this->sendSMSMail('6', $payData, $mailSubject, '3', $pay_status_array['id_post_payment']);
                    }
                }
                echo "Status updated successfully";
                break;
            case 'Delete':
                break;
            default:
                $set_model = self::SET_MODEL;
                $access = $this->$set_model->get_access('postdated/payment/list');
                if (!empty($_POST)) {
                    $range['from_date'] = $this->input->post('from_date');
                    $range['to_date'] = $this->input->post('to_date');
                    $items = $this->$model->post_paymentlist_range($range['from_date'], $range['to_date']);
                } else {
                    $items = $this->$model->post_paymentlist(($id != NULL ? $id : ''));
                }
                $payment = array(
                    'access' => $access,
                    'data' => $items
                );
                echo json_encode($payment);
        }
    }
    public function generateInvoice($payment_no, $id_scheme_account, $page_orient = "")
    {
        $model = self::PAY_MODEL;
        $set = self::SET_MODEL;
        $data['is_print_taken'] = 1;
        $result = $this->$model->update_payment_status($payment_no, $data);
        $data['records'] = $this->$model->get_invoiceData($payment_no, $id_scheme_account);
        $data['records_sch'] = $this->$model->get_paymentContent($data['records'][0]['id_scheme_account']);
        $data['gstSplitup'] = $this->$model->get_gstSplitupData($data['records'][0]['id_scheme'], $data['records'][0]['date_add']);
        $data['receipt'] = $this->$set->receipt_type();
        $data['qrcode']['app_qrcode'] = $this->config->item('base_url') . "mobile_app_qrcode/skj_app_qrcode.png";
        $data['qrcode']['playstore'] = $this->config->item('base_url') . "mobile_app_qrcode/playstore.png";
        //print_r($this->branch_settings);exit;
        if ($this->branch_settings == 1) {
            $data['comp_details'] = $this->$set->get_branchcompany($data['records'][0]['id_branch']);
            $data['comp_details']['company_name'] = $data['comp_details']['name'];
        } else {
            $data['comp_details'] = $this->$set->get_company();
        }
        $data['records'][0]['amount_in_words'] = $this->no_to_words($data['records'][0]['payment_amount']);
        $schemeType = (int) $data['records_sch']['scheme_type'];
        $flexType = (int) $data['records_sch']['flexible_sch_type'];
        $wgtConvert = (int) $data['records_sch']['wgt_convert'];
        if ($schemeType == 0 || ($schemeType == 3 && (in_array($flexType, [1, 6], true) || ($flexType == 2 && $wgtConvert == 2)))) {
            $data['records_sch']['is_Weight'] = 0;
        } elseif (in_array($schemeType, [1, 2], true) || ($schemeType == 3 && (($flexType == 2 && in_array($wgtConvert, [0, 1], true)) || in_array($flexType, [3, 4, 5, 7, 8], true)))) {
            $data['records_sch']['is_Weight'] = ($flexType == 2 && $wgtConvert == 2) ? 0 : 1;
        }
        //echo "<pre>";print_r($data);echo "</pre>";exit;
        //create PDF receipt
        //print_r($data['comp_details']);exit;
        if ($data['records_sch']['is_topup_scheme'] == 1) {
            $this->load->helper(array('dompdf', 'file'));
            $dompdf = new DOMPDF();
            $html = $this->load->view('include/topup_scheme_receipt', $data, true);
            $dompdf->load_html($html); //echo $html;exit;
            $dompdf->set_paper("a4", "portriat");
            $dompdf->render();
            while(ob_get_level()) ob_end_clean();
            $dompdf->stream("receipt1.pdf", array('Attachment' => 0));
        } else if ($data['receipt']['receipt'] == 0) {
            $this->load->helper(array('dompdf', 'file'));
            $dompdf = new DOMPDF();
            //$html = $this->load->view('include/receipt1', $data,true);
            $html = $this->load->view('include/receipt_temp', $data, true);
            //echo $html;exit;
            $dompdf->load_html($html);
            $dompdf->set_paper("a4", "portriat");
            $dompdf->render();
            while(ob_get_level()) ob_end_clean();
            $dompdf->stream("receipt1.pdf", array('Attachment' => 0));
        } else if ($data['receipt']['receipt'] == 1) {
            $this->load->helper(array('dompdf', 'file'));
            $dompdf = new DOMPDF();
            //$html = $this->load->view('include/receipt1', $data,true);
            $html = $this->load->view('include/receipt_custom', $data, true);
            //echo $html;exit;
            $dompdf->load_html($html);
            $customPaper = array(0, 0, 210, 450);
            if ($page_orient == 'A5') {
                $dompdf->set_paper('a5', "portriat");
            } else {
                $dompdf->set_paper($customPaper, "portriat");
            }
            $dompdf->render();
            while(ob_get_level()) ob_end_clean();
            $dompdf->stream("receipt1.pdf", array('Attachment' => 0));
        } else if ($data['receipt']['receipt'] == 2) {
            $this->load->helper(array('dompdf', 'file'));
            $dompdf = new DOMPDF();
            //$html = $this->load->view('include/receipt1', $data,true);
            $html = $this->load->view('include/receipt_thermal', $data, true);
            $dompdf->load_html($html);
            $customPaper = array(0, 0, 25, 15);
            $dompdf->set_paper($customPaper, "portrait");
            $dompdf->render();
            while(ob_get_level()) ob_end_clean();
            $dompdf->stream("receipt1.pdf", array('Attachment' => false));
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
            //$val = $val1 . " Rupees";
            $val = $val1;
        }
        if (isset($nos[1]) && $nos[1] != 0) {
            $val2 = $this->no_to_words1($nos[1]);
            if (isset($val2))
                //$val = $val1 . " Rupees and" . " " . $val2 . " Paisa";
                $val = $val1 . " & " . $val2 . " Paisa";
        }
        return $val;
    }
    function no_to_words1($nos1 = "")
    {
        $words = array('0' => '', '1' => 'One', '2' => 'Two', '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six', '7' => 'Seven', '8' => 'Eight', '9' => 'Nine', '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve', '13' => 'Thirteen', '14' => 'Fouteen', '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen', '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty', '30' => 'Thirty', '40' => 'Fourty', '50' => 'Fifty', '60' => 'Sixty', '70' => 'Seventy', '80' => 'Eighty', '90' => 'Ninty', '100' => 'Hundred', '1000' => 'Thousand', '100000' => 'Lakh', '10000000' => 'Crore');
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
    function online_payment_list()
    {
        $data['main_content'] = self::PAY_VIEW . "online_payments";
        $this->load->view('layout/template', $data);
    }
    function verify_payment_view()
    {
        $SETT_MOD = "admin_settings_model";
        $data['main_content'] = self::PAY_VIEW . "verify_payment";
        $data['access'] = $this->$SETT_MOD->get_access('verify/online/payment');
        $this->load->view('layout/template', $data);
    }
    // settled pay show in payment apprval page with filter//HH
    function ajax_onlinePayments()
    {
        $model = self::PAY_MODEL;
        $set_model = self::SET_MODEL;
        if (!empty($_POST['from_date'])) {
            $range['from_date'] = $this->input->post('from_date');
            $range['to_date'] = $this->input->post('to_date');
            $range['type'] = $this->input->post('type');
            $range['limit'] = $this->input->post('limit');
            $date_type = $this->input->post('date_type');
            $range['settle'] = $this->input->post('settle');
            $data['data'] = $this->$model->onlinePayments_range($range['from_date'], $range['to_date'], $range['limit'], $date_type, $range['settle']);
        } else {
            $range['settle'] = $this->input->post('settle');
            $data['data'] = $this->$model->onlinePayments('', $range['settle']);
        }
        echo json_encode($data);
    }
    // settled pay show in payment apprval page with filter//	
    // for payment approval status update 
    function update_pay_status()
    {
        $model = self::PAY_MODEL;
        $log_model = self::LOG_MODEL;
        $set_model = self::SET_MODEL;
        $p_status = $this->input->post('pay_status');
        $pay_ids = $this->input->post('pay_id');
        $transData = array();
        if (!empty($pay_ids) && count($pay_ids) > 0 && $p_status != NULL) {
            $pay_status = array('payment_status' => $p_status);
            $ischkref = FALSE;
            foreach ($pay_ids as $id_payment) {
                $update = $this->$model->update_payment_status($id_payment, $pay_status);
                /*For success pay: insert data in inter tables.Based on settings generate a/c no & receipt no*/
                if ($p_status == 1) {
                    if ($this->config->item('integrationType') == 1) {
                        $this->insert_common_data_jil($id_payment);
                    } else if ($this->config->item('integrationType') == 2) {
                        $this->insert_common_data($id_payment);
                    }
                    $pay = $this->$model->paymentDB("get", $id_payment);
                    // Referral Code :- allow_referral - 0 => No , 1 => Yes
                    if ($pay['allow_referral'] == 1) {
                        $ref_data = $this->$model->get_refdata($pay['id_scheme_account']);
                        $ischkref = $this->$model->get_ischkrefamtadd($pay['id_scheme_account']);
                        if ($ref_data['ref_benifitadd_ins_type'] == 1 && $ref_data['referal_code'] != '' && ($ref_data['ref_benifitadd_ins'] == $ref_data['paid_installments']) && $ischkref == TRUE) {
                            $this->insert_referral_data($ref_data['id_scheme_account']);
                        } else if ($ref_data['ref_benifitadd_ins_type'] == 0 && $ref_data['referal_code'] != '' && $ischkref == TRUE) {
                            $this->insert_referral_data($ref_data['id_scheme_account']);
                        }
                    }
                    // Account number :- schemeacc_no_set - 0 => generate a/c no ,  0 => manual a/c no , 2 => integration
                    if ($pay['schemeacc_no_set'] == 0) {
                        // Generate a/c no
                        // if ($pay['acc_no'] == '' || $pay['acc_no'] == null) {
                        if (empty($pay['acc_no'])) {
                            $scheme_acc_number = $this->account_model->account_number_generator($pay['id_scheme'], $pay['branch'], '');
                            if ($scheme_acc_number != NULL) {
                                $updateData['scheme_acc_number'] = $scheme_acc_number;
                            }
                            //$updSchAc = $this->account_model->update_account($updateData,$pay['id_scheme_account']);
                        }
                        /*if($pay['gent_clientid'] ==1 && $scheme_acc_number != NULL){        //upd client id & acc no cus reg table based on the settings//
                                $updateData['ref_no'] = $this->config->item('cliIDcode')."/".$pay['group_code'].'/'.$scheme_acc_number;
                            }
                            if($pay['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2){   
                                $updateDatacus['scheme_acc_number'] = $scheme_acc_number;
                            //	$updateDatacus['ref_no'] = $this->config->item('cliIDcode')."/".$pay['group_code'].'/'.$scheme_acc_number;
                                $updateDatacus['sync_scheme_code'] =$pay['sync_scheme_code'];
                            }
                                if($pay['gent_clientid'] ==1 && $pay['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2){	
                                            $updateDatacus['ref_no'] = $this->config->item('cliIDcode')."/".$pay['group_code']."/".$scheme_acc_number;
                                    }*/
                        $updSchAc = $this->account_model->update_account($updateData, $pay['id_scheme_account']);
                        //$updcusreg = $this->account_model->update_cusreg($updateDatacus,$generic['id_scheme_account']); //acc no & clientid upd to cus reg tab//HH
                        //$updtrans = $this->account_model->update_trans($updateDatacus,$generic['id_scheme_account']); //Client Id upd to trans tab//
                    }
                    // Receipt Number :-  receipt_no_set - 0 => Donot generate , 1 => generate
                    if ($pay['receipt_no_set'] == 1) {
                        $receipt_no = $this->generate_receipt_no($pay['id_scheme'], $pay['branch']);
                        $pay_array = array('receipt_no' => $receipt_no, 'approval_date' => date("Y-m-d H:i:s"));
                        $result = $this->$model->paymentDB("update", $id_payment, $pay_array);
                    }
                    if ($pay['edit_custom_entry_date'] == 1) {
                        $pay_array = array('custom_entry_date' => $pay['custom_entry_date']);
                        $result = $this->$model->paymentDB("update", $id_payment, $pay_array);
                    }
                    if ($pay['firstPayamt_as_payamt'] == 1 || $pay['firstPayamt_payable'] == 1) {
                        $pay_array = array('firstPayment_amt' => $pay['payment_amount']);
                        $result = $this->account_model->update_account($pay_array, $pay['id_scheme_account']);
                    }
                }
                // Update log and send sms and email on successful updation
                if ($update == 1) {
                    $log_data = array(
                        'id_log' => $this->id_log,
                        'event_date' => date("Y-m-d H:i:s"),
                        'module' => 'Payment',
                        'operation' => 'Update',
                        'record' => $id_payment,
                        'remark' => 'Online Payment updated successfully'
                    );
                    $this->$log_model->log_detail('insert', '', $log_data);
                    $pay_data = $this->$model->getPpayment_data($id_payment);
                    $mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme account payment details";
                    $mailtype = 3;
                    $this->sendSMSMail('7', $pay_data, $mailSubject, $mailtype, $id_payment);
                }
            }
            $this->session->set_flashdata('chit_alert', array('message' => count($pay_ids) . ' Payment record updated as successfully...', 'class' => 'success', 'title' => 'Payment Approval'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation...', 'class' => 'danger', 'title' => 'Payment Approval'));
        }
    }
    function ajax_get_payment($id_payment)
    {
        $model = self::PAY_MODEL;
        $payment = $this->$model->get_online_payment($id_payment);
        //echo "<pre>";print_r($payment);echo "</pre>";exit;
        echo json_encode($payment);
    }
    function ajax_online_payment()
    {
        $model = self::PAY_MODEL;
        $set_model = self::SET_MODEL;
        $range['pg_code'] = $this->input->post('pg_code');
        if ($_POST['from_date'] != '' && $_POST['to_date'] != '') {
            $range['from_date'] = $this->input->post('from_date');
            $range['to_date'] = $this->input->post('to_date');
            $range['type'] = $this->input->post('type');
            $range['limit'] = $this->input->post('limit');
            $range['pg_code'] = $this->input->post('pg_code');
            $data['data'] = $this->$model->payment_online_range($range['from_date'], $range['to_date'], $range['limit'], $range['pg_code']);
        } else {
            $data['data'] = $this->$model->payment_online($range['pg_code']);
        }
        echo json_encode($data);
    }
    function split_payment($id_payment)
    {
        //    $serv_model= self::SERV_MODEL;
        $payment = $this->payment_model->getPaymentByID($id_payment);
        if (!empty($payment)) {
            $date_paid = $payment['date_payment'];
            $txnid = $payment['id_transaction'];
            $act_amt = $payment['act_amount'];
            $dues = $payment['no_of_dues'] - 1;
            for ($i = 1; $i <= $dues; $i++) {
                //   $paid_date = date('Y-m-d H:i:s', strtotime($date_paid.' +'.$i.' months'));
                //$receipt_no 		 = $this->generate_receipt_no();
                if ($this->$model->get_rptnosettings() == 0) {
                    $receipt_no = $this->generate_receipt_no();
                } else {
                    $receipt_no;
                }
                $insertData = array(
                    "id_scheme_account" => $payment['id_scheme_account'],
                    "id_transaction" => $txnid . "-S" . $i,
                    "payment_amount" => $payment['payment_amount'],
                    "payment_type" => "Payu Checkout",
                    "due_type" => "S",
                    "date_payment" => $date_paid,
                    "act_amount" => $act_amt,
                    "metal_rate" => (isset($payment['metal_rate']) ? $payment['metal_rate'] : '0.00'),
                    "metal_weight" => (isset($payment['metal_weight']) ? $payment['metal_weight'] : '0.00'),
                    "bank_name" => (isset($payment['issuing_bank']) ? $payment['issuing_bank'] : NULL),
                    "payment_mode" => (isset($payment['payment_mode']) ? $payment['payment_mode'] : NULL),
                    "card_no" => (isset($payment['card_no']) ? $payment['card_no'] : NULL),
                    "card_holder" => (isset($payment['card_holder']) ? $payment['card_holder'] : NULL),
                    "payment_ref_number" => (isset($payment['payment_ref_number']) ? $payment['payment_ref_number'] : NULL),
                    "remark" => ' Splitted from transactionid ' . $txnid . ' paid on ' . $date_paid,
                    "payment_status" => 1,
                    "receipt_no" => $receipt_no,
                    'approval_date' => date("Y-m-d H:i:s")
                );
                $this->db->trans_begin();
                $split_data = $this->payment_model->insert_payment($insertData);
                /*
                $serviceID =7;
                $service = $this->services_modal->checkService($serviceID);
                if($split_data['status'] == true && isset($split_data['insertID']))
                {
                    $id=$split_data['insertID'];
                    if($service['sms'] == 1)
                    {
                        $data =$this->$serv_model->get_SMS_data($serviceID,'',$id);
                        $mobile =$data['mobile'];
                        $message = $data['message'];
                        $this->send_sms($mobile,$message);
                    }
                    $invoiceData = $this->payment_modal->get_paymenMailData($id);
                    if($service['email'] == 1 && isset($invoiceData[0]['email']) && $invoiceData[0]['email'] != '')
                    { 
                        $to = $invoiceData[0]['email'];
                        $subject = "Reg - ".$this->comp['company_name'].($payment['due_type'] =='A'?' advance ':' pending ')." payment for the saving scheme";
                        $data['payData'] = $invoiceData[0];
                        $data['type'] = 3;
                        $data['company_details'] = $this->comp;
                        $message = $this->load->view('include/emailPayment',$data,true);
                        $sendEmail = $this->email_model->send_email($to,$subject,$message);	
                    }		
                }	 */
            }
            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                return TRUE;
            } else {
                return FALSE;
            }
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
        curl_close($ch);
        return json_decode($output);
    }
    function monthly_rate($by)
    {
        $model = self::PAY_MODEL;
        $data['rate'] = $this->$model->monthly_rate($by);
        echo json_encode($data);
    }
    function update_settlement()
    {
        $model = self::PAY_MODEL;
        $scheme = array();
        $scheme = $_POST['scheme'];
        $set_data = array(
            'schemes' => count($scheme),
            'id_employee' => $this->employee
        );
        $set_status = $this->$model->weight_settlementDB('insert', '', $set_data);
        foreach ($scheme as $scheme) {
            $payments = $this->$model->payments_by_scheme($scheme['id']);
            if (!empty($payments)) {
                foreach ($payments as $pay) {
                    $rate = $scheme['rate'];
                    if ($rate != null && $rate != '') {
                        $weight = $pay['payment_amount'] / $rate;
                        //update payment				
                        $pay_array = array(
                            'metal_rate' => $rate,
                            'metal_weight' => number_format((float) $weight, 3, '.', ''),
                            'fix_weight' => 1
                        );
                        $pay_status = $this->$model->paymentDB("update", $pay['id_payment'], $pay_array);
                        //send sms/mail to Customer 
                        $pay_data = $this->$model->getPpayment_data($pay['id_payment']);
                        $mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme account settlement details";
                        $this->sendSMSMail('14', $pay_data, $mailSubject, '5', $pay['id_payment']);
                        //insert settlement detail    
                        if ($pay_status['status']) {
                            $set_det = array(
                                'id_settlement' => $set_status['insertID'],
                                'id_payment' => $pay['id_payment'],
                                'id_scheme_account' => $pay['id_scheme_account'],
                                'metal_rate' => $rate,
                                'metal_weight' => number_format((float) $weight, 3, '.', ''),
                                'type' => $scheme['type'],
                                'adjust_by' => $scheme['adjust_by']
                            );
                            $setDet_status = $this->$model->insert_settlement_detail($set_det);
                        }
                        //update flag
                        $upd_set = array('success' => 1);
                        $status = $this->$model->weight_settlementDB('update', $set_status['insertID'], $upd_set);
                    }
                }
            }
        }
        echo "Metal weight updated successfully";
    }
    function weight_settlement($type = "", $id = "")
    {
        $model = self::PAY_MODEL;
        switch ($type) {
            case 'View':
                $data['set'] = $this->$model->weight_settlementDB();
                $data['rate'] = $this->$model->monthly_rate_variation();/*Added by ARVK*/
                $data['main_content'] = self::SET_VIEW . 'form';
                $this->load->view('layout/template', $data);
                break;
            case 'List':
                $data['main_content'] = self::SET_VIEW . 'list';
                $this->load->view('layout/template', $data);
                break;
            default:
                $data['set'] = $this->$model->weight_settlementDB('get');
                echo json_encode($data);
                break;
        }
    }
    function weight_settlement_detail($type = "", $id = "")
    {
        $model = self::PAY_MODEL;
        switch ($type) {
            case 'List':
                $data['main_content'] = self::SET_VIEW . 'detail_list';
                $this->load->view('layout/template', $data);
                break;
            case 'get':
                $data['set'] = $this->$model->view_settlement_detail($id);
                return $data;
                break;
            default:
                $data['set'] = $this->$model->view_settlement_detail($id);
                echo json_encode($data);
                break;
        }
    }
    function send_sms($mobile, $message, $type = '', $dtl_te_id)
    {
        $model = self::ADM_MODEL;
        if ($this->config->item('sms_gateway') == '1') {
            $this->sms_model->sendSMS_MSG91($mobile, $message, '', $dtl_te_id);
        } elseif ($this->config->item('sms_gateway') == '2') {
            $this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
        } elseif ($this->config->item('sms_gateway') == '3') {
            $this->sms_model->sendSMS_SpearUC($mobile, $message, '', $dtl_te_id);
        } elseif ($this->config->item('sms_gateway') == '4') {
            $this->sms_model->sendSMS_Asterixt($mobile, $message, '', $dtl_te_id);
        } elseif ($this->config->item('sms_gateway') == '5') {
            $this->sms_model->sendSMS_Qikberry($mobile, $message, '', $dtl_te_id);
        }
    }
    function sendSMSMail($serviceID, $data, $subject, $type, $id)
    {
        $ser_model = self::SET_MODEL;
        $mail_model = self::MAIL_MODEL;
        $service = $this->$ser_model->get_service($serviceID);
        $email = $data['email'];
        $sms_model = self::SMS_MODEL;
        if ($service['serv_email'] == 1 && $email != '') {
            $data['payData'] = $data;
            $data['company_details'] = $this->company;
            $data['type'] = $type;
            $to = $email;
            $message = $this->load->view('include/emailPayment', $data, true);
            $sendEmail = $this->$mail_model->send_email($to, $subject, $message);
        }
        $data = $this->$sms_model->get_SMS_data($serviceID, '', $id);
        $mobile_number = $data['mobile'];
        $message = $data['message'];
        if ($service['serv_sms'] == 1) {
            $this->send_sms($mobile_number, $message, '', $service['dlt_te_id']);
        }
        if ($service['serv_whatsapp'] == 1) {
            $smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $params];
            $this->sms_model->send_whatsApp_message($mobile_number, $smsData);
        }
        return true;
    }
    function getMetalRateBydate()
    {
        $model = self::PAY_MODEL;
        $date_pay = $this->input->post('date_pay');
        // Metal rate back date entry esakki-20250729
        $type = $this->input->post('type');
        $data = $this->$model->getMetalRateBydate($date_pay, $type);
        echo json_encode($data);
    }
    // offline datas transaction_details
    //To insert payment and registration details in intermediate table
    function insert_common_data_jil($id_payment)
    {
        $model = self::CHITAPI_MODEL;
        $this->load->model($model);
        //getting payment detail
        $pay_data = $this->$model->getPaymentByID($id_payment);
        //storing temp values
        $trans_date = $pay_data[0]['trans_date'];
        $approval_no = $pay_data[0]['approval_no'];
        $ref_no = $pay_data[0]['ref_no'];
        $id_scheme_account = $pay_data[0]['id_scheme_account'];
        //getting customer detail to post registration again
        $reg = $this->$model->getCustomerByID($id_scheme_account, $id_payment);
        //echo $this->db->last_query();
        $isExists = $this->$model->checkTransExists($trans_date, $approval_no, $ref_no);
        if (!$isExists) {
            //insert payment detail
            $status = $this->$model->insert_transaction($pay_data[0]);
            //echo $this->db->last_query();exit;
            if ($status) {
                //insert registration detail
                if ($reg) {
                    $reg[0]['transfer_jil'] = 'N';
                    $reg[0]['transfer_date'] = NULL;
                    $reg[0]['ref_no'] = $ref_no;
                    $status = $this->$model->insert_CustomerReg($reg[0]);
                }
            }
        }
        return true;
    }
    //To insert payment and registration details in intermediate table
    function insert_common_data($id_payment)
    {
        $model = self::API_MODEL;
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
        
        $isCusRegExists = $this->$model->checkCusRegExists($id_scheme_account, $ref_no);
        if (!$isCusRegExists['status']) {
            $reg = $this->$model->getCustomerByID($id_scheme_account);
            //insert customer registration detail
            if ($reg) {
                $reg[0]['record_to'] = 1;
                $reg[0]['id_branch'] = ($reg[0]['id_branch'] == 0 ? NULL : $reg[0]['id_branch']);
                $reg[0]['is_registered_online'] = 2;  // 2 - online record
                $reg[0]['ref_no'] = $ref_no;
                // Skip clientid if gent_clientid is disabled
                if(empty($chit_settings['gent_clientid']) || $chit_settings['gent_clientid'] == 0){
                    $reg[0]['clientid'] = NULL;
                }
                $status = $this->$model->insert_CustomerReg($reg[0]);
            }
        }
        $isTranExists = $this->$model->checkTransExists($ref_no);
        if (!$isTranExists) {
            //insert payment detail
            $pay_data[0]['record_to'] = 1;
            $pay_data[0]['id_branch'] = ($pay_data[0]['id_branch'] == 0 ? NULL : $pay_data[0]['id_branch']);
            $pay_data[0]['payment_type'] = 1;    // 1 - online
            // echo "<pre>";print_r($pay_data[0]);echo "<pre>";exit;
            $status = $this->$model->insert_transaction($pay_data[0]);
            // echo $this->db->_error_message();
        }
        return true;
    }
    public function insertTransInPayment()
    {
        //get offline payment records which are not updated in payment table
        $model = self::CHITAPI_MODEL;
        $getPayments = $this->$model->getofflinePaymentsbyStatus('N');
        $total_rec = 0;
        $add_rec = 0;
        $upd_rec = 0;
        foreach ($getPayments as $payData) {
            $id_sch_ac = $this->$model->getIdschemeAC($payData);
            if ($id_sch_ac) {
                if (!empty($payData['payment_mode'])) {
                    $expArray = explode('-', $payData['payment_mode']);
                    $pay_mode = $expArray[0];
                } else {
                    $pay_mode = 'Offline';
                }
                $isCancelled = (substr($payData['payment_amount'], 0, 1) == '-' ? TRUE : FALSE);
                if (!$isCancelled) {
                    $pay_array = array(
                        "id_scheme_account" => $id_sch_ac,
                        "date_payment" => $payData['date_payment'],
                        //	"id_metal" 			=> $payData['id_metal'],
                        "metal_rate" => $payData['metal_rate'],
                        "payment_amount" => $payData['payment_amount'],
                        "metal_weight" => $payData['metal_weight'],
                        "payment_mode" => $pay_mode,
                        "payment_status" => 1,
                        "payment_type" => "Offline",
                        "instalment" => $payData['instalment'],
                        "receipt_no" => $payData['receipt_jil'],
                        "remark" => $payData['remark'],
                        "discountAmt" => $payData['discountAmt'],
                        "payment_ref_number" => $payData['brefno'],
                        "date_upd" => date('Y-m-d H:i:s')
                    );
                    $insPayment = $this->$model->insertPayment($pay_array);
                    if ($insPayment) {
                        $total_rec++;
                        $add_rec++;
                    }
                    //   echo $insPayment;exit;
                } else {
                    //update if offline record is with cancelled status
                    $upd_array = array(
                        "payment_status" => 2,
                        "receipt_jil" => $payData['receipt_jil'],
                        "remark" => $payData['remark'],
                        "date_upd" => date('Y-m-d H:i:s'),
                        "payment_ref_number" => $payData['brefno']
                    );
                    $updPayment = $this->$model->updatePayment($upd_array);
                    if ($updPayment) {
                        $total_rec++;
                        $upd_rec++;
                    }
                }
            }
        }
        if ($total_rec > 0) {
            $this->session->set_flashdata('chit_alert', array('message' => 'Total ' . $total_rec . ' records affected.Added  ' . $add_rec . ' payment records and updated ' . $upd_rec . ' payment records. ', 'class' => 'success', 'title' => 'Update Client Details'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'No updates to proceed', 'class' => 'danger', 'title' => 'Update Client Details'));
        }
        redirect('payment/list');
    }
    function deleteCusandPaydata($id_payment)
    {
        $model = self::API_MODEL;
        $delete = $this->$model->deletePayandCus($id_payment);
        return $delete;
    }
    // offline datas transaction_details
    function manual_receiptnumber()
    {
        $model = self::PAY_MODEL;
        $payment = $this->input->post('selected');
        $upd_rec = 0;
        if (!empty($payment) && count($payment) > 0 && $payment != NULL) {
            $this->db->trans_begin();
            foreach ($payment as $data) {
                $pay_account = array('receipt_no' => $data['receipt_no']);
                $update = $this->$model->update_payment_status($data['id_payment'], $pay_account);
                if ($update) {
                    $this->db->trans_commit();
                    $upd_rec++;
                } else {
                    $this->db->trans_rollback();
                }
            }
            echo $upd_rec;
            if ($upd_rec > 0) {
                $this->session->set_flashdata('chit_alert', array('message' => $upd_rec . ' Receipt number record updated as successfully...', 'class' => 'success', 'title' => 'Receipt number generated'));
            }
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation...', 'class' => 'danger', 'title' => 'Receipt number generate'));
        }
    }
    // referral data insert wallet transaction//
    /* function insert_referral_data($id_scheme_account)
     {
         $log_model    = self::LOG_MODEL;
         $model        = self::PAY_MODEL;
         $model_name   = self::WALL_MODEL;
         $sms_model    = self::SMS_MODEL; 
         $ser_model    = self::SET_MODEL;
         $status=FALSE;			
         $serviceID=16;
         $data = $this->$model->get_referrals_datas($id_scheme_account);	
         if(!empty($data))
         {			
             if($data['referal_code']!=''&& $data['referal_value']!='' && $data['id_customer']!=''&& ($data['cus_refferal']==1 || $data['emp_refferal']==1)){
             // insert wallet transaction data //
                             $wallet_data = array(
                             'id_wallet_account' => $data['id_wallet_account'],
                             'date_transaction' =>  date("Y-m-d H:i:s"),
                             'id_employee'      =>  $this->session->userdata('uid'),
                             'transaction_type' =>  0,
                             'value'            => $data['referal_value'],
                             'description'      => 'Referral Benefits - '.$data['cusname'].''
                             );
                 $status =$this->$model_name->wallet_transactionDB('insert','',$wallet_data);
                  }			
                   if($status)
                   {
                         $log_data = array(
                                         'id_log'     => $this->id_log,
                                         'event_date' => date("Y-m-d H:i:s"),
                                         'module'     => 'Wallet Transaction',
                                         'operation'  => 'Delete',
                                         'record'     => $status['insertID'],  
                                         'remark'     => 'Wallet Transaction Insert successfully'
                                      );
                     $this->$log_model->log_detail('insert','',$log_data);							  
                   }
                   $service = $this->$ser_model->get_service($serviceID);
                   if($service['serv_sms'] == 1 && $data['mobile']){
                        $sms_data =$this->$sms_model->get_SMS_data($serviceID,'',$data['id_scheme_account']);					
                        $this->send_sms($sms_data['mobile'],$sms_data['message']);
                   }
         }
     } */
    function insert_referral_data($id_scheme_account)
    {
        $log_model = self::LOG_MODEL;
        $model = self::PAY_MODEL;
        $model_name = self::WALL_MODEL;
        $sms_model = self::SMS_MODEL;
        $set_model = self::SET_MODEL;
        $cusmodel = self::CUS_MODEL;
        $status = FALSE;
        $serviceID = 16;
        $chkreferral = $this->$model->get_referral_code($id_scheme_account);
        if ($chkreferral['referal_code'] != '' && $chkreferral['is_refferal_by'] == 1) {
            $data = $this->$model->get_empreferrals_datas($id_scheme_account);
        } else if ($chkreferral['referal_code'] != '' && $chkreferral['is_refferal_by'] == 0) {
            $data = $this->$model->get_cusreferrals_datas($id_scheme_account);
        }
        if (!empty($data)) {
            if ($data['referal_code'] != '' && $data['referal_value'] != '' && $data['id_wallet_account'] != '' && $data['id_wallet_account'] != '') {
                // insert wallet transaction data //
                $wallet_data = array(
                    'id_wallet_account' => $data['id_wallet_account'],
                    'id_sch_ac' => $id_scheme_account,
                    'date_transaction' => date("Y-m-d H:i:s"),
                    'id_employee' => $this->session->userdata('uid'),
                    'transaction_type' => 0,
                    'value' => $data['referal_value'],
                    'description' => 'Referral Benefits - ' . $data['cusname'] . ''
                );
                //	echo"<pre>"; print_r($wallet_data);exit;
                $status = $this->$model_name->wallet_transactionDB('insert', '', $wallet_data);
                if ($status) {
                    // Update credit flag in customer table
                    /* is_refbenefit_crt = 0 -> already  benefit credited  & 1-> yet to credit benefits' */
                    if ($chkreferral['is_refferal_by'] == 0 && $data['cusbenefitscrt_type'] == 0 && ($data['schrefbenifit_secadd'] == 0 || $data['schrefbenifit_secadd'] == 1)) {
                        // customer referal - single  
                        $this->$cusmodel->update_customer_only(array('is_refbenefit_crt_cus' => 0), $chkreferral['id_customer']);
                    } else if ($chkreferral['is_refferal_by'] == 0 && $data['cusbenefitscrt_type'] == 1 && $data['schrefbenifit_secadd'] == 1) {
                        // customer referal - multiple  
                        $this->$cusmodel->update_customer_only(array('is_refbenefit_crt_cus' => 1), $chkreferral['id_customer']);
                    } else if ($chkreferral['is_refferal_by'] == 1 && $data['empbenefitscrt_type'] == 0 && ($data['schrefbenifit_secadd'] == 0 || $data['schrefbenifit_secadd'] == 1)) {
                        // emp referal - single  					
                        $this->$cusmodel->update_customer_only(array('is_refbenefit_crt_emp' => 0), $chkreferral['id_customer']);
                    } else if ($chkreferral['is_refferal_by'] == 1 && $data['empbenefitscrt_type'] == 1 && $data['schrefbenifit_secadd'] == 1) {
                        // emp referal - single  			
                        $this->$cusmodel->update_customer_only(array('is_refbenefit_crt_emp' => 1), $chkreferral['id_customer']);
                    }
                    $log_data = array(
                        'id_log' => $this->id_log,
                        'event_date' => date("Y-m-d H:i:s"),
                        'module' => 'Wallet Transaction',
                        'operation' => 'Delete',
                        'record' => $status['insertID'],
                        'remark' => 'Wallet Transaction Insert successfully'
                    );
                    $this->$log_model->log_detail('insert', '', $log_data);
                }
                $service = $this->$set_model->get_service($serviceID);
                $sms_data = $this->$sms_model->get_SMS_data($serviceID, '', $data['id_scheme_account']);
                if ($service['serv_sms'] == 1 && $data['mobile']) {
                    $this->send_sms($sms_data['mobile'], $sms_data['message'], '', '');
                }
                if ($service['serv_whatsapp'] == 1 && $data['mobile']) {
                    $smsData = ["message" => $sms_data['message'], "template_name" => $service['template_name'], "params" => $params];
                    $this->sms_model->send_whatsApp_message($sms_data['mobile'], $smsData);
                }
            }
        }
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
                "remark" => 'Debited for saving scheme payment ' . $tran['txnid'],
                'ref_no' => $tran['txnid'],
                'trans_points' => $redeemed_amount
            );
            if ($tran['walletIntegration'] == 0) { // 0 - No integration, 1 - Req integration as like SSS
                $transDetailData['trans_type'] = 2;
                $updwallet = $this->payment_model->updwallet($transDetailData, $tran['mobile']);
                $r = array("status" => $updwallet);
                return $r;
            } else {
                $cat_code = 'SS';
                $wal_cat_settings = $this->payment_model->getWcategorySettings($cat_code);
                // Begin the transaction
                $this->db->trans_begin();
                if ($wal_cat_settings) {
                    $newRecord = array(
                        "bill_date" => date('Y-m-d H:i:s'),
                        "bill_no" => $tran['txnid'],
                        "bill_amount" => $tran['actual_trans_amt'],
                        "cat_amt" => $tran['actual_trans_amt'],
                        "cat_code" => $cat_code,
                        "mobile" => $tran['mobile'],
                        "id_wcat_settings" => $wal_cat_settings['id_wcat_settings'],
                        "redeem_req_pts" => $redeemed_amount,
                        "debit_points" => $redeemed_amount,
                        "cat_cr_pts" => 0,
                        "bill_avail_wal_pt" => $tran['available_points'],
                        "avail_wal_pt" => $tran['available_points'],
                        "id_branch" => $tran['branch'],
                        "date_add" => date('Y-m-d H:i:s'),
                        "record_type" => 2, // 1 - offline , 2 - online
                    );
                    $wallAccount = $this->payment_model->getInterWalletCustomer($tran['mobile']);
                    $allow = FALSE;
                    if ($wallAccount['status']) {
                        $upd_data = array(
                            "available_points" => ($tran['available_points'] - $redeemed_amount),
                            "last_update" => date('Y-m-d H:i:s'),
                            "mobile" => $tran['mobile'],
                        );
                        $w_status = $this->payment_model->updInterWalletAcc($upd_data);
                        if ($w_status) {
                            $allow = TRUE;
                        }
                    }
                    if ($allow) {
                        $t_status = $this->payment_model->insertData($newRecord, 'inter_wallet_bills');
                        if ($t_status['status']) {
                            $transDetailData['trans_type'] = 2;
                            $updwallet = $this->payment_model->updwallet($transDetailData, $tran['mobile']);
                            // Bill wise point debit :: FIFO based on expiry 
                            $interData = array(
                                "mobile" => $tran['mobile'],
                                "debit_points" => $redeemed_amount,
                                "date_add" => date("Y-m-d H:i:s")
                            );
                            $this->payment_model->insertData($interData, 'inter_walsync_debit');
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
                            $isExist = $this->payment_model->getSyncWalletByMobile($tran['mobile']);
                            if ($isExist) {
                                $syncWalData['last_update'] = date('Y-m-d H:i:s');
                                $syncWalData['type'] = 1; // Online Redeem
                                $this->payment_model->updateSyncWal($syncWalData);
                            } else {
                                $syncWalData['date_add'] = date('Y-m-d H:i:s');
                                $syncWalData['type'] = 1; // Online Redeem
                                $this->payment_model->insertData($syncWalData, 'inter_sync_wallet');
                            }
                            $verifcode = "";
                            $msg = 'Thanks for shopping at ' . $this->comp['company_name'] . '.Your Wallet Balance ' . number_format($tran['available_points'], '0', '.', '') . ' pts.Redeemed ' . number_format($redeemed_amount, '2', '.', '') . ' pts.New Wallet Balance ' . number_format(($tran['available_points'] - $redeemed_amount), '0', '.', '') . ' pts.' . $verifcode;
                            if ($updwallet) {
                                $smsData[] = array(
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
                /* echo $this->db->_error_message();
                 echo $this->db->last_query();
                exit;*/
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
    function send_sms_wallet($smsData)
    {
        $set_model = self::SET_MODEL;
        $serviceID = 17;
        $service = $this->$set_model->get_service($serviceID);
        $company = $this->$set_model->get_company();
        if ($service['serv_sms'] == 1) {
            foreach ($smsData as $data) {
                //$data =$this->$sms_model->get_SMS_data($serviceID,'',$id);
                $mobile = $data['mobile'];
                $message = $data['message'];
                $model = self::ADM_MODEL;
                $this->$model->send_sms($mobile, $message);
            }
        }
        return TRUE;
    }
    // To revert payment status
    function revertApproval_jil()
    {
        ;
        $apimodel = self::CHITAPI_MODEL;
        $log_model = self::LOG_MODEL;
        $status = FALSE;
        $payData = $this->input->post('payData');
        $reverted_ids = 0;
        if (!empty($payData) && count($payData) > 0) {
            foreach ($payData as $pay) {
                $revert_pay = $this->$apimodel->revertPayment($pay['id_payment']);
                if ($revert_pay) {
                    // delete data in transaction and customer_reg tables
                    $status = $this->$apimodel->deletePayandCus($pay['id_payment']);
                    if ($status) {
                        //array_push($reverted_ids,$pay['id_payment']);
                        $reverted_ids += 1;
                    }
                }
            }
            if (($reverted_ids) > 0) {
                // update in log table
                $log_data = array(
                    'id_employee' => $this->session->userdata('uid'),
                    'event_date' => date("Y-m-d H:i:s"),
                    'module' => 'Payment',
                    'operation' => 'Revert',
                    'record' => $reverted_ids,
                    'remark' => 'Payment reverted successfully'
                );
                $this->$log_model->log_detail('insert', '', $log_data);
                $this->session->set_flashdata('chit_alert', array('message' => ($reverted_ids) . ' Payment record updated as successfully...', 'class' => 'success', 'title' => 'Revert Approval'));
            } else {
                $this->session->set_flashdata('chit_alert', array('message' => 'No records to proceed the requested operation...', 'class' => 'danger', 'title' => 'Revert Approval'));
            }
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'No records to proceed your request ....', 'class' => 'danger', 'title' => 'Revert Approval'));
        }
    }
    function revertApproval()
    {
        $model = self::CHITAPI_MODEL;
        $log_model = self::LOG_MODEL;
        $payData = $this->input->post('payData');
        //  print_r($payData);exit;
        $reverted_ids = 0;
        if (!empty($payData) && count($payData) > 0) {
            foreach ($payData as $pay) {
                // check whether customer registration exists for this payment_id (ref_no)
                $isCusRegExists = $this->$model->checkCusRegExists('', $pay['id_payment']);
                if ($isCusRegExists['status'] == true) {
                    $tranCount = $this->$model->checkTransCount($isCusRegExists['id_scheme_account']);
                    if ($tranCount == 1) {
                        $this->db->trans_begin();
                        $this->$model->revertPayment($pay['id_payment']);
                        $delete = $this->$model->deletePayandCus($pay['id_payment']);
                        $logData = array(
                            "delete_customer_reg" => 1, // 1 - delete customer reg record 0 - no
                            "id_branch" => ($pay['id_branch'] == 'null' ? NULL : $pay['id_branch']),
                            "ref_no" => $pay['id_payment'],
                            "clientid" => ($pay['clientid'] == 'null' ? NULL : $pay['clientid']),
                            'id_employee' => $this->session->userdata('uid'),
                        );
                        $log = $this->$model->revert_approve_log($logData);
                        if ($this->db->trans_status() === TRUE) {
                            $this->db->trans_commit();
                            $reverted_ids += 1;
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    }
                } else {
                    $this->db->trans_begin();
                    $this->$model->revertPayment($pay['id_payment']);
                    /*$delete  = $this->$model->deleteTrans($pay['id_payment']);	
                    $logData = array("delete_customer_reg" 	=> 0, // 1 - delete customer reg record 0 - no
                                     "id_branch" 			=> ($pay['id_branch'] == 'null' ? NULL:$pay['id_branch']),
                                     "ref_no" 				=> $pay['id_payment'],
                                     "clientid" 			=> ($pay['clientid'] == 'null' ? NULL:$pay['clientid']),
                                     'id_employee' 		=>  $this->session->userdata('uid'),
                                    );
                    $log  = $this->$model->revert_approve_log($logData);*/
                    if ($this->db->trans_status() === TRUE) {
                        $this->db->trans_commit();
                        $reverted_ids += 1;
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                }
            }
            $this->session->set_flashdata('chit_alert', array('message' => count($reverted_ids) . ' Payment record updated as successfully...', 'class' => 'success', 'title' => 'Revert Approval'));
        } else {
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation...', 'class' => 'danger', 'title' => 'Revert Approval'));
        }
    }
    function ajax_load_account()
    {
        $id_payment = $_GET['id_payment'];
        $id_scheme_account = $_GET['id_sch_ac'];
        $model = self::PAY_MODEL;
        $data['account'] = $this->$model->get_load_account($id_payment, $id_scheme_account);
        echo json_encode($data);
    }
    function free_payment_data($sch_data, $sch_acc_id)
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
            "due_type" => 'D',
            "no_of_dues" => '1',
            "metal_rate" => $gold_rate,
            "metal_weight" => ($sch_data['scheme_type'] == 2 ? $converted_wgt : ($sch_data['scheme_type'] == 1 ? $fxd_wgt : 0.000)),
            "remark" => "Paid by " . $this->company['company_name'],
            "payment_status" => '1'
        );
        return $insertData;
    }
    function verify_payment()
    {
        $txns = $_POST;
        if ($txns['pg_code'] == 3) {
            $this->verifyWithTechProcess($txns);
        } else if ($txns['pg_code'] == 2) {
            $this->verify_hdfcpayment($txns);
        } else if ($txns['pg_code'] == 1) {
            $this->verify_PayUpayments($txns);
        } else if ($txns['pg_code'] == 4) {
            $this->verify_cashfreepayment($txns);
        } else if ($txns['pg_code'] == 7) {
            $this->verifyRazorPayments($txns);
        } else if ($txns['pg_code'] == 8) {
            $this->verify_easebuzzpayment($txns);
        }
    }
    /*  CASHFREE - STATUS API
*
*   Document Links : 
*       1. https://docs.cashfree.com/pg/restapi
*       2. https://docs.cashfree.com/docs/rest/guide/#get-status
*		3. https://docs.cashfree.com/docs/resources/#response-status
*
*   REQUEST PARAMETERS :
*        Parameter	Required	Description
*        appId	    Yes	        Your app id
*        secretKey	Yes	        Your Secret Key
*        orderId	Yes	        Order/Invoice Id*
*
*	RESPONSE PARAMETERS :
*       Parameter	                Description
*       status	                    Status of API call. Values are - OK and ERROR
*       paymentLink	                link of payment page for that order. Returned when status is OK
*       reason	                    reason of failure when status is ERROR
*       txStatus	                transaction status, if a payment has been attempted
*       txTime	                    transaction time, if payment has been attempted
*       txMsg	                    transaction message, if payment has been attempted
*       referenceId	                transaction reference id, if payment has been attempted
*       paymentMode	                payment mode of transaction, if payment has been attempted
*       orderCurrency	            currency of the order
*       paymentDetails.paymentMode	payment mode of transaction, if payment has been attempted
*       paymentDetails.bankName	    Name of the bank if payment has been attempted (only in case of Netbanking)
*       paymentDetails.cardNumber	Masked card number if payment has been attempted (only in case of Debit & Credit Cards)
*       paymentDetails.cardCountry	Country code of the card if payment has been attempted (only in case of Debit & Credit Cards)
*       paymentDetails.cardScheme	Scheme of the card (eg: VISA) if payment has been attempted (only in case of Debit & Credit Cards)
*   
*   PAYMENT STATUS
*       Case	                    event.name	        event.status
*       Successful Payment	        PAYMENT_RESPONSE	SUCCESS
*       Payment Failed	            PAYMENT_RESPONSE	FAILED
*       Pending Payment	            PAYMENT_RESPONSE	PENDING
*       Payment cancelled by user	PAYMENT_RESPONSE	CANCELLED
*       Payment successful but kept 
        on hold by risk system	    PAYMENT_RESPONSE	FLAGGED
*       Invalid inputs	            VALIDATION_ERROR	-
*/
    // .CashFree
    function verify_cashfreepayment($data)
    {
        $model = self::PAY_MODEL;
        $set_model = self::SET_MODEL;
        $gateway_info = $this->$model->getBranchGatewayData($data['id_branch'], $data['pg_code']);
        $secretKey = $gateway_info['param_1'];
        $appId = $gateway_info['param_3'];
        $api_url = $gateway_info['api_url'];
        //$transData      = $data['transData']; 
        $transData = $data['txn_ids'];
        $vCount = 0;
        if (sizeof($transData) > 0) {
            foreach ($transData as $tran) {
                if (!empty($tran)) {
                    //$postData = "appId=".$appId."&secretKey=".$secretKey."&orderId=".$tran['txn_id'];
                    $postData = "appId=" . $appId . "&secretKey=" . $secretKey . "&orderId=" . $tran;
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => $api_url . "/" . $tran . "/payments",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 8,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_HTTPHEADER => array(
                            'x-client-id: ' . $appId,
                            'x-client-secret: ' . $secretKey,
                            'x-api-version: 2022-09-01'
                        ),
                    ));
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);
                    if ($err) {
                        echo "cURL Error #:" . $err;
                    } else {
                        $response = json_decode($response);
                        // print_r($response);exit;
                        // RAHUL 9/7
                        $log_path = $this->log_dir . '/cashfree/mob_response_' . date("Y-m-d") . '.txt';
                        $data = "\n ----- \n MANUAL VERIFY CURL RESPONSE " . date("Y-m-d H:i:s") . ": " . json_encode($response);
                        $data .= "\nPOST DATA: " . json_encode($postData);
                        file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
                        // print_r(is_array($response));
                        if (isset($response) && is_array($response)) { // OK - Api call success  - ERROR Failed
                            //echo "<pre>";print_r($response);
                            /* $status_code = $response->txStatus ; // SUCCESS,
                            $txn_id      = $tran;
                            if($txn_id != "" && $status_code != 'PENDING' && $status_code != 'FLAGGED' && $status_code != '')
                            {
                                $updateGateData = array( 
                                    "payu_id"           => $response->referenceId, // referenceId
                                    "payment_ref_number"=> $response->referenceId, 
                                    "payment_mode"      => ($response->paymentMode == "CREDIT_CARD" ? "CC":($response->paymentMode == "DEBIT_CARD" ? "DC":($response->paymentMode == "NET_BANKING" ? "NB":(!empty($response->paymentMode) ? $response->paymentMode : 'UPI')))), 
                                    "remark"            => "Manual verify(Cash Free) on ".date("Y-m-d H:i:s"),
                                    "payment_status"    => ($status_code == 'SUCCESS' ? 1:($status_code == 'CANCELLED' || $status_code == 'USER_DROPPED'?$this->payment_status['cancel']:($status_code == 'FAILED'?$this->payment_status['failure']:($status_code == 'REFUND'?$this->payment_status['refund']:$this->payment_status['pending']))))
                                ); 	 */
                            $cf_response = $response[0]; // By default set first array as cf_response[which is last transaction data for the ref_trans_id]
                            if (sizeof($response) > 0) { // Check size of response and update cf_response data if payment is success
                                while (list(, $val) = each($response)) {
                                    if ($val->payment_status == 'SUCCESS') {
                                        $cf_response = $val;
                                        break;
                                    }
                                }
                            }
                            $status_code = $cf_response->payment_status;
                            // 	$status_code = $response->txStatus ; // SUCCESS,
                            $status_code = strtolower($cf_response->payment_status);
                            $paymentMode = strtolower($cf_response->payment_group);
                            $txn_id = $tran;
                            if ($txn_id != "" && $status_code != 'PENDING' && $status_code != 'FLAGGED' && $status_code != '') {
                                $updateGateData = array(
                                    "payu_id" => $cf_response->referenceId, // referenceId
                                    "payment_ref_number" => $cf_response->referenceId,
                                    "payment_mode" => (($paymentMode) == "credit_card" ? "CC" : (($paymentMode) == "debit_card" ? "DC" : (($paymentMode) == "net_banking" ? "NB" : (($paymentMode) == "na" ? "NA" : $this->payment_model->update_paymentMode($paymentMode))))),
                                    "remark" => $cf_response->txMsg . " - manual verify on " . date('Y-m-d H:i:s'),
                                    "payment_status" => ($status_code == 'success' || $status_code == 'captured' ? (($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2) ? 1 : 2) : ($status_code == 'usercancelled' || $status_code == 'user_dropped' || $status_code == 'dropped' || $status_code == 'bounced' ? 4 : ($status_code == 'failed' ? 3 : ($status_code == 'refund' ? 6 : 7))))
                                );
                                $this->db->trans_begin();
                                $result = $this->$model->updateGatewayResponse($updateGateData, $txn_id);
                                if ($status_code == 'success') {
                                    $payIds = $this->$model->getPayIds($txn_id);
                                    if (sizeof($payIds) > 0) {
                                        // Wallet Debit :: Haritha's code altered by Pavithra on 27-8-2024 [Debit splitup]
                                        $actual_trans_amt = $payIds[0]['actual_trans_amt'];
                                        $act_wal_redeemed = $payIds[0]['redeemed_amount'];
                                        $remaining_amt = 0;
                                        $remaining_wal_amt = 0;
                                        if ($act_wal_redeemed > 0) {
                                            // Update payment table - payment mode as multi
                                            $this->payment_model->updPayModeBRefTranID(["payment_mode" => 'MULTI'], $txn_id);
                                        }
                                        foreach ($payIds as $py) {
                                            $tax = 0;
                                            $pay_amount = 0;
                                            if ($pay['gst_type'] == 1) { // Exclusive
                                                $tax = $py['gst_amount'];
                                            }
                                            $pay_amount = ($py['payment_amount'] - $py['discountAmt'] + $tax);
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
                                                    'payment_date' => $pay['date_payment'],
                                                    'created_time' => date("Y-m-d H:i:s"),
                                                    /* "payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":(!empty($paymentMode) ? $paymentMode : 'UPI')))),  */
                                                    // "payment_mode" => (strtolower($cf_response->paymentMode) == "credit_card" ? "CC" : (strtolower($cf_response->paymentMode) == "debit_card" ? "DC" : (strtolower($cf_response->paymentMode) == "net_banking" ? "NB" : (strtolower($cf_response->paymentMode) == "na" ? "-" : "UPI")))),
                                                    "payment_mode" => $updateGateData['payment_mode'],
                                                    "remark" => "Manual verify(Cash Free) on " . date("Y-m-d H:i:s"),
                                                    "payment_ref_number" => $py['payment_ref_number'],
                                                    "payment_status" => $updateGateData['payment_status']
                                                );
                                                if ($py['due_type'] == 'GA') {
                                                    $arrayPayMode['id_adv_payment'] = $py['id_payment'];
                                                    $payModeInsert = $this->payment_model->insertData($arrayPayMode, 'general_advance_mode_detail');
                                                } else {
                                                    $update_pmd = array(
                                                        'payment_status' => 9, // Cancelled
                                                        "updated_time" => date('Y-m-d H:i:s'),
                                                        "remark" => "Removed while manual verify " . date('Y-m-d H:i:s'),
                                                    );
                                                    $update_existing_pmd = $this->$model->update_data($update_pmd, 'id_payment', $py['id_payment'], 'payment_mode_details');
                                                    $arrayPayMode['id_payment'] = $py['id_payment'];
                                                    $payModeInsert = $this->payment_model->insertData($arrayPayMode, 'payment_mode_details');
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
                                                    "payment_ref_number" => $py['payment_ref_number'],
                                                    "payment_status" => 1
                                                );
                                                if ($py['due_type'] == 'GA') {
                                                    $arrayPayMode['id_adv_payment'] = $py['id_payment'];
                                                    $payModeInsert = $this->payment_model->insertData($arrayPayMode, 'general_advance_mode_detail');
                                                } else {
                                                    $update_pmd = array(
                                                        'payment_status' => 9, // Cancelled
                                                        "updated_time" => date('Y-m-d H:i:s'),
                                                        "remark" => "Removed while manual verify " . date('Y-m-d H:i:s'),
                                                    );
                                                    $update_existing_pmd = $this->$model->update_data($update_pmd, 'id_payment', $py['id_payment'], 'payment_mode_details');
                                                    $arrayPayMode['id_payment'] = $py['id_payment'];
                                                    $payModeInsert = $this->payment_model->insertData($arrayPayMode, 'payment_mode_details');
                                                }
                                                $WalletinsData = array(
                                                    'id_wallet_account' => $py['id_wallet_account'],
                                                    'transaction_type' => 1, //0-Credit,1-Debit
                                                    'type' => 0, //CRM
                                                    'id_sch_ac' => $py['id_scheme_account'],
                                                    'value' => $pay_amount,
                                                    'description' => 'Chit Redeem',
                                                    'date_transaction' => date("Y-m-d H:i:s"),
                                                    'id_employee' => NULL,
                                                    'date_add' => date("Y-m-d H:i:s"),
                                                    'credit_for' => 'Redeem',
                                                    'id_payment' => $py['id_payment']
                                                );
                                                $this->payment_model->insertData($WalletinsData, 'wallet_transaction');
                                            }
                                            // Generate Installment Number
                                            /*$installmentNo = $this->genInstallmentNo($py['id_scheme_account']);
                                        $this->$model->updatePayData(array("installment"=>$installmentNo),'id_payment',$py['id_payment'],'payment');
                                        */
                                            /*For success pay: insert data in inter tables.Based on settings generate a/c no & receipt no*/
                                            /*if($this->config->item('integrationType') == 1){
                                            $this->insert_common_data_jil($py['id_payment']);
                                        }else if($this->config->item('integrationType') == 2){
                                            $this->insert_common_data($py['id_payment']);
                                        }*/
                                            $pay = $this->$model->paymentDB("get", $py['id_payment']);
                                            // Multi mode payment
                                            /*if($pay['payment_mode']!= NULL)
                                         {
                                             $arrayPayMode=array(
                                                            'id_payment'         => $pay['id_payment'],
                                                            'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                                                            'payment_date'         => date("Y-m-d H:i:s"),
                                                            'created_time'         => date("Y-m-d H:i:s"),
                                                            "payment_mode"       => $pay['payment_mode'],
                                                            "remark"             => $response->txMsg." - manual verif",
                                                            "payment_ref_number" => $pay['payment_ref_number'],
                                                            "payment_status"     => 1
                                                            );
                                            if(!empty($arrayPayMode)){
                                                $cashPayInsert = $this->$model->insertData($arrayPayMode,'payment_mode_details'); 
                                            }
                                         }*/
                                            // Referral Code :- allow_referral - 0 => No , 1 => Yes
                                            if ($pay['allow_referral'] == 1) {
                                                $ref_data = $this->$model->get_refdata($pay['id_scheme_account']);
                                                $ischkref = $this->$model->get_ischkrefamtadd($pay['id_scheme_account']);
                                                if ($ref_data['ref_benifitadd_ins_type'] == 1 && $ref_data['referal_code'] != '' && ($ref_data['ref_benifitadd_ins'] == $ref_data['paid_installments']) && $ischkref == TRUE) {
                                                    $this->insert_referral_data($ref_data['id_scheme_account']);
                                                } else if ($ref_data['ref_benifitadd_ins_type'] == 0 && $ref_data['referal_code'] != '' && $ischkref == TRUE) {
                                                    $this->insert_referral_data($ref_data['id_scheme_account']);
                                                }
                                            }
                                            // Account number :- schemeacc_no_set - 0 => generate a/c no ,  0 => manual a/c no , 2 => integration , 3 => Integration Auto
                                            if ($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set'] == 3) {
                                                // Generate a/c no
                                                // if ($pay['acc_no'] == '' || $pay['acc_no'] == null) {
                                                if (empty($pay['acc_no'])) {
                                                    $scheme_acc_number = $this->account_model->account_number_generator($pay['id_scheme'], $pay['branch'], '');
                                                    if ($scheme_acc_number != NULL) {
                                                        $updateData['scheme_acc_number'] = $scheme_acc_number;
                                                    }
                                                    $updSchAc = $this->account_model->update_account($updateData, $pay['id_scheme_account']);
                                                    if ($pay['gent_clientid'] == 1 && $scheme_acc_number != NULL) {        //upd client id & acc no cus reg table based on the settings//
                                                        $updateData['ref_no'] = $this->config->item('cliIDcode') . "/" . $pay['group_code'] . '/' . $scheme_acc_number;
                                                    }
                                                    if ($pay['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2) {
                                                        $updateDatacus['scheme_acc_number'] = $scheme_acc_number;
                                                        //	$updateDatacus['ref_no'] = $this->config->item('cliIDcode')."/".$pay['group_code'].'/'.$scheme_acc_number;
                                                        $updateDatacus['sync_scheme_code'] = $py['sync_scheme_code'];
                                                    }
                                                    if ($pay['gent_clientid'] == 1 && $pay['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2) {
                                                        $updateDatacus['ref_no'] = $this->config->item('cliIDcode') . "/" . $pay['group_code'] . "/" . $scheme_acc_number;
                                                    }
                                                    $updSchAc = $this->account_model->update_account($updateData, $pay['id_scheme_account']);
                                                }
                                            }
                                            // Receipt Number :-  receipt_no_set - 0 => Donot generate , 1 => generate
                                            if ($pay['receipt_no_set'] == 1 && empty($pay['receipt_no'])) {
                                                $receipt_no = $this->generate_receipt_no($pay['id_scheme'], $pay['branch']);
                                                $pay_array = array('receipt_no' => $receipt_no, 'approval_date' => date("Y-m-d H:i:s"));
                                                $result = $this->$model->paymentDB("update", $pay['id_payment'], $pay_array);
                                            }
                                            if ($pay['edit_custom_entry_date'] == 1) {
                                                $pay_array = array('custom_entry_date' => $pay['custom_entry_date']);
                                                $result = $this->$model->paymentDB("update", $pay['id_payment'], $pay_array);
                                            }
                                            if ($pay['firstPayamt_as_payamt'] == 1 || $pay['firstPayamt_maxpayable'] == 1) {
                                                $pay_array = array('firstPayment_amt' => $pay['payment_amount']);
                                                $result = $this->account_model->update_account($pay_array, $pay['id_scheme_account']);
                                            }
                                            $dt_pay = date('Y-m-d H:i:s', strtotime($pay['date_payment']));
                                            $ins_cycle = $this->payment_model->get_due_date($pay['due_type'], $dt_pay, $pay['id_scheme_account']);
                                            if (sizeof($ins_cycle[0]) > 0) {
                                                $cycle_data = array(
                                                    'due_date' => (isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL),
                                                    'due_date_to' => (isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL),
                                                    'grace_date' => (isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL),
                                                    'installment' => (isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL),
                                                    'is_limit_exceed' => (isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0),
                                                );
                                                $this->payment_model->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');
                                            }
                                            //update paid installments against account...
                                            $paid = $this->payment_model->getPaidInsData($pay['id_scheme_account']);
                                            if (sizeof($paid) > 0) {
                                                $paid_ins = array('total_paid_ins' => $paid['paid_installments']);
                                                $this->payment_model->updData($paid_ins, 'id_scheme_account', $pay['id_scheme_account'], 'scheme_account');
                                            }
                                        }
                                        if ($this->config->item('auto_pay_approval') == 2 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)) {
                                            if ($this->config->item('integrationType') == 1) {
                                                $this->insert_common_data_jil($pay['id_payment']);
                                            } else if ($this->config->item('integrationType') == 2) {
                                                $this->insert_common_data($pay['id_payment']);
                                            }
                                        }
                                    }
                                }
                                if ($this->db->trans_status() === TRUE) {
                                    $payres_array = array('dev_remark' => $status_code . "   " . $txn_id . " " . date('Y-m-d H:i:s'));
                                    $result = $this->$model->paymentDB("updatestatus", $txn_id, $payres_array);
                                    $vCount = $vCount + 1;
                                    $this->db->trans_commit();
                                    $this->session->set_flashdata('chit_alert', array('message' => count($transData) . ' Payment record updated as successfully...', 'class' => 'success', 'title' => 'Verify Payment'));
                                } else {
                                    $this->db->trans_rollback();
                                }
                            } else {
                                $payres_array = array('dev_remark' => "No respone from gateway " . $txn_id . " " . date('Y-m-d H:i:s'));
                                $result = $this->$model->paymentDB("updatestatus", $txn_id, $payres_array);
                                //$response_msg[] = array ( 'msg' => $status_msg , 'Transaction ID' => $txn_id 	);        	
                            }
                        } else {
                            echo $response->code . "   " . $txn_id . ", </br>";
                        }
                    }
                }
                if ($vCount > 0) {
                    echo $vCount . " payment records verified successfully.";
                } else {
                    echo " No records to verify.";
                }
            }
        } else {
            echo "Select Payments to verify.";
        }
    }
    function verifyRazorPayments($data)
    {
        $model = self::PAY_MODEL;
        $set_model = self::SET_MODEL;
        $acc_model = self::ACC_MODEL;
        $gateway_info = $this->$model->getBranchGatewayData($data['id_branch'], $data['pg_code']);
        $publicKey = $gateway_info['param_3'];
        $secretKey = $gateway_info['param_1'];
        $transData = $data['txn_ids'];
        $vCount = 0;
        if (sizeof($transData) > 0) {
            foreach ($transData as $tran) {
                $order_id = $this->$model->getRazorOrderid($tran);
                //echo "<pre>"; print_r($order_id);exit;
                if ($order_id != '' && $order_id != NULL) {
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.razorpay.com/v1/orders/" . $order_id . "/payments",
                        CURLOPT_HTTPHEADER => array(
                            "Content-Type: application/json"
                        ),
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
                        CURLOPT_USERPWD => "$publicKey:$secretKey",
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_CUSTOMREQUEST => "GET",
                    ));
                    $response = curl_exec($curl);
                    $err = curl_error($curl);
                    curl_close($curl);
                    if ($err) {
                        echo "cURL Error #:" . $err;
                    } else {
                        $response = json_decode($response);
                        //echo "<pre>"; print_r($response);exit;
                        if (!empty($response->items) && $response->entity == 'collection' && $response->count > 0) {
                            /* $rp_response = array();// By default set first array as cf_response[which is last transaction data for the ref_trans_id]
                            if(sizeof($response->items) > 0){ // Check size of response and update cf_response data if payment is success
                                while (list(, $val) = each($response->items)) {
                                    // print_r($val);exit;
                                    if ($val->status == 'captured' || $val->status == 'success') {
                                        $rp_response[] = $val;
                                        break;   
                                    }
                                }
                            } */
                            $rp_response = []; // Default to empty
                            if (!empty($response->items)) {
                                // Filter for 'captured' or 'success' statuses
                                $captured_items = array_filter($response->items, function ($item) {
                                    return $item->status === 'captured' || $item->status === 'success';
                                });
                                if (!empty($captured_items)) {
                                    // Convert each captured stdClass object to array
                                    foreach ($captured_items as $item) {
                                        $rp_response[] = $item;
                                    }
                                } else {
                                    // Default to the first item as array
                                    $rp_response[] = $response->items[0];
                                }
                            }
                            foreach ($rp_response as $item) {
                                $updateData = array(
                                    "payment_mode" => ($item->method == "CREDIT_CARD" ? "CC" : ($item->method == "DEBIT_CARD" ? "DC" : ($item->method == "netbanking" ? "NB" : (!empty($item->method) ? $item->method : 'UPI')))),
                                    "payu_id" => $item->id,
                                    "remark" => "Manual - Verify by Razor pay on " . date("Y-m-d H:i:s"),
                                    "payment_ref_number" => $item->order_id,
                                    // "payment_status"     => 1
                                    "payment_status" => (($item->status == 'success' || $item->status == 'captured') ? 1 : ($item->status == 'cancelled' ? 4 : ($item->status == 'failed' ? 3 : ($item->status == 'refunded' ? 6 : 7))))
                                );
                                $payment = $this->$model->updData($updateData, 'payment_ref_number', $order_id, 'payment');
                                if ($item->status == "captured") {
                                    $payIds = $this->$model->getOrderIds($order_id);
                                    if (sizeof($payIds) > 0) {
                                        foreach ($payIds as $pay) {
                                            // Multi mode payment
                                            if ($updateData['payment_mode'] != NULL) {
                                                $update_pmd = array(
                                                    'payment_status' => 9, // Cancelled
                                                    "updated_time" => date('Y-m-d H:i:s'),
                                                    "remark" => "Removed while manaul verify " . date('Y-m-d H:i:s'),
                                                );
                                                $update_existing_pmd = $this->$model->update_data($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');
                                                $arrayPayMode = array(
                                                    'id_payment' => $pay['id_payment'],
                                                    'payment_amount' => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                                                    'payment_date' => date("Y-m-d H:i:s"),
                                                    'created_time' => date("Y-m-d H:i:s"),
                                                    "payment_mode" => ($item->method == "CREDIT_CARD" ? "CC" : ($item->method == "DEBIT_CARD" ? "DC" : ($item->method == "netbanking" ? "NB" : (!empty($item->method) ? $item->method : 'UPI')))),
                                                    "payment_ref_number" => $item->order_id,
                                                    "payment_status" => 1,
                                                    "remark" => "Manual - Verify by Razor pay on " . date("Y-m-d H:i:s"),
                                                );
                                                $cashPayInsert = $this->$model->insertData($arrayPayMode, 'payment_mode_details');
                                            }
                                            $schData = [];
                                            $cusRegData = [];
                                            $transData = [];
                                            // Generate account  number  
                                            if ($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set'] == 3) {
                                                // if ($pay['scheme_acc_number'] == '' || $pay['scheme_acc_number'] == null || $pay['scheme_acc_number'] == 0) {
                                                if (empty($pay['scheme_acc_number'])) {
                                                    $ac_group_code = NULL;
                                                    // Lucky draw
                                                    if ($pay['is_lucky_draw'] == 1) { // Based on scheme settings 
                                                        // Update Group code in scheme_account table 
                                                        $updCode = $this->$model->updateGroupCode($pay['id_scheme_account']);
                                                        $ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
                                                    }
                                                    $scheme_acc_number = $this->$acc_model->account_number_generator($pay['id_scheme'], $pay['branch'], $ac_group_code);
                                                    if ($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0) {
                                                        $schData['scheme_acc_number'] = $scheme_acc_number;
                                                        $cusRegData['scheme_acc_number'] = $scheme_acc_number;
                                                    }
                                                }
                                            }
                                            if ($pay['id_scheme_account'] > 0) {
                                                if (sizeof($schData) > 0) { // Update scheme account
                                                    $this->$model->update_account($schData, $pay['id_scheme_account']);
                                                }
                                            }
                                            // Generate receipt number
                                            if ($pay['receipt_no_set'] == 1) {
                                                $receipt['receipt_no'] = $this->generate_receipt_no($pay['id_scheme'], $pay['branch']);
                                                $payment['status'] = $this->$model->update_receipt($pay['id_payment'], $receipt);
                                            }
                                            //Update First Payment Amount In Scheme Account
                                            $approval_type = $this->config->item('auto_pay_approval');
                                            if (($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable'] == 1 || $pay['firstPayamt_as_payamt'] == 1) && ($pay['firstPayment_amt'] == '' || $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0)) {
                                                if ($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "")) { // Fix First payable as weight
                                                    $fixPayable = array('firstPayment_wgt' => $pay['metal_weight']);
                                                } else {
                                                    $fixPayable = array('firstPayment_amt' => $pay['payment_amount']);
                                                }
                                                $status = $this->$model->update_account($fixPayable, $pay['id_scheme_account']);
                                            }
                                        }
                                    }
                                    $payres_array = array('dev_remark' => $item->status . "   " . $tran . " " . date('Y-m-d H:i:s'));
                                    $result = $this->$model->paymentDB("updatestatus", $tran, $payres_array);
                                    $res[] = "<pre>" . $tran . " - " . $item->status;
                                } else {
                                    $payres_array = array('dev_remark' => $item->status . "   " . $tran . " " . date('Y-m-d H:i:s'));
                                    $result = $this->$model->paymentDB("updatestatus", $tran, $payres_array);
                                    // echo "<pre>".$tran." - ".$item->status;
                                    $res[] = "<pre>" . $tran . " - " . $item->status;
                                }
                            }
                        }
                    }
                } else {
                    $payres_array = array(
                        'dev_remark' => "No response from razorpay " . $tran . " " . date('Y-m-d H:i:s'),
                        "remark" => "Manual - Verify by Razor pay - " . date('Y-m-d H:i:s'),
                    );
                    $result = $this->$model->update_data($payres_array, 'ref_trans_id', $tran, 'payment');
                    $res[] = "Order Id does not exist";
                    //echo "Order Id does not exist";
                }
            }
            if ($vCount >= 0) {
                echo json_encode($res);
                //echo " payment records verified successfully."; 	
            } else {
                echo json_encode($res);
                // echo " No records to verify. Message ". print_r($response); 
                //echo " No records to verify."; 
            }
        } else {
            echo "Select Payments to verify.";
        }
    }
    function verify_easebuzzpayment($data)
    {
        $model = self::PAY_MODEL;
        $set_model = self::SET_MODEL;
        $acc_model = self::ACC_MODEL;
        $gateway_info = $this->$model->getBranchGatewayData($data['id_branch'], $data['pg_code']);
        $key = $gateway_info['param_1'];
        $salt = $gateway_info['param_3'];
        $api_url = $gateway_info['api_url'];
        //$transData      = $data['transData']; 
        $transData = $data['txn_ids'];
        $vCount = 0;
        if (sizeof($transData) > 0) {
            foreach ($transData as $tr) {
                $tran = $this->$model->getPayData($tr); //print_r($tran);exit;
                $amount = number_format((float) round($tran[0]['pay_amt']), 1, '.', '');
                //hash format - 'key|txnid|amount|email|phone|salt'
                $hash_sequence = trim($key) . '|' . trim($tr) . '|' . trim($amount) . '|' . trim($tran[0]['email']) . '|' . trim($tran[0]['mobile']) . '|' . trim($salt);
                //echo "<pre>"; print_r($hash_sequence);exit;
                $hash_value = strtolower(hash('sha512', $hash_sequence));
                //echo "<pre>"; print_r($hash_value);exit;
                $postData = "txnid=" . trim($tr) . "&key=" . trim($key) . "&amount=" . trim($amount) . "&email=" . trim($tran[0]['email']) . "&phone=" . trim($tran[0]['mobile']) . "&hash=" . trim($hash_value);
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $api_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $postData,
                    // Getting  server response parameters //
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/x-www-form-urlencoded"
                    ),
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    echo "cURL Error #:" . $err;
                } else {
                    $response = json_decode($response);
                    //print_r($response);exit;
                    // RAHUL 7/7
                    $log_path = $this->log_dir . '/easebuzz/mob_response_' . date("Y-m-d") . '.txt';
                    $data = "\n ----- \n MANUAL VERIFY CURL RESPONSE " . date("Y-m-d H:i:s") . ": " . json_encode($response);
                    $data .= "\nPOST DATA: " . json_encode($postData);
                    file_put_contents($log_path, $data, FILE_APPEND | LOCK_EX);
                    if ($response->status == 1) {
                        $trans_id = $response->msg->txnid;
                        $paymentMode = $response->msg->mode;
                        $referenceId = $response->msg->easepayid;
                        $status_code = $response->msg->status;
                        if (!empty($trans_id) && $trans_id != NULL) {
                            $updateData = array(
                                //"payment_mode" => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "netbanking" ? "NB" : (!empty($paymentMode) ? $paymentMode : 'UPI')))),
                                "payment_mode" => ((strtolower($paymentMode) == "credit_card") || (strtolower($paymentMode) == "cc") ? "CC" : ((strtolower($paymentMode) == "debit_card") || (strtolower($paymentMode) == "dc") ? "DC" : ((strtolower($paymentMode) == "netbanking") || (strtolower($paymentMode) == "nb") ? "NB" : (strtolower($paymentMode) == "na" ? "NA" : $this->update_paymentMode($paymentMode))))),
                                "payu_id" => $referenceId,
                                "remark" => "Manual verify by Easebuzz on " . date("Y-m-d H:i:s"),
                                "payment_ref_number" => $referenceId,
                                // "payment_status" => ($status_code == 'success' ? 1 : ($status_code == 'userCancelled' ? 4 : ($status_code == 'failure' ? 3 : ($status_code == 'refund' ? 6 : 7))))
                                "payment_status" => (strtolower($status_code) == 'success' ? 1 : (strtolower($status_code) == 'cancelled' || strtolower($status_code) == 'user_dropped' || strtolower($status_code) == 'userCancelled' || strtolower($status_code) == 'dropped' || strtolower($status_code) == 'bounced' ? 4 : (strtolower($status_code) == 'failed' || strtolower($status_code) == 'failure' ? 3 : (strtolower($status_code) == 'refund' ? 6 : 7))))
                            );
                            $payment = $this->$model->updateGatewayResponse($updateData, $trans_id);
                            if ($status_code == "success") {
                                $payIds = $this->$model->getPayIds($trans_id);
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
                                                'payment_date' => $pay['date_payment'],
                                                'created_time' => date("Y-m-d H:i:s"),
                                                // "payment_mode" => ($paymentMode == "CREDIT_CARD" ? "CC" : ($paymentMode == "DEBIT_CARD" ? "DC" : ($paymentMode == "NET_BANKING" ? "NB" : (!empty($paymentMode) ? $paymentMode : 'UPI')))),
                                                "payment_mode" => $updateData['payment_mode'],
                                                "remark" => "Manual verify by Easebuzz on " . date("Y-m-d H:i:s"),
                                                "payment_ref_number" => $pay['payment_ref_number'],
                                                "payment_status" => $updateData['payment_status']
                                            );
                                            if ($pay['due_type'] == 'GA') {
                                                $arrayPayMode['id_adv_payment'] = $pay['id_payment'];
                                                $payModeInsert = $this->payment_model->insertData($arrayPayMode, 'general_advance_mode_detail');
                                            } else {
                                                $update_pmd = array(
                                                    'payment_status' => 9, // Cancelled
                                                    "updated_time" => date('Y-m-d H:i:s'),
                                                    "remark" => "Removed while manual verify " . date('Y-m-d H:i:s'),
                                                );
                                                $update_existing_pmd = $this->$model->update_data($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');
                                                $arrayPayMode['id_payment'] = $pay['id_payment'];
                                                $payModeInsert = $this->payment_model->insertData($arrayPayMode, 'payment_mode_details');
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
                                                'payment_date' => $pay['date_payment'],
                                                'created_time' => date("Y-m-d H:i:s"),
                                                "payment_mode" => "REF_WALLET",
                                                "remark" => "Wallet Utilized",
                                                "payment_ref_number" => $pay['payment_ref_number'],
                                                "payment_status" => 1
                                            );
                                            if ($pay['due_type'] == 'GA') {
                                                $arrayPayMode['id_adv_payment'] = $pay['id_payment'];
                                                $payModeInsert = $this->payment_model->insertData($arrayPayMode, 'general_advance_mode_detail');
                                            } else {
                                                $update_pmd = array(
                                                    'payment_status' => 9, // Cancelled
                                                    "updated_time" => date('Y-m-d H:i:s'),
                                                    "remark" => "Removed while manaul verify " . date('Y-m-d H:i:s'),
                                                );
                                                $update_existing_pmd = $this->$model->update_data($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');
                                                $arrayPayMode['id_payment'] = $pay['id_payment'];
                                                $payModeInsert = $this->payment_model->insertData($arrayPayMode, 'payment_mode_details');
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
                                            $this->payment_model->insertData($WalletinsData, 'wallet_transaction');
                                        }
                                        /* //Redeem amount in wallet credit for customer - by haritha 19-1-24
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
                                                                        $this->$model->insertData($WalletinsData,'wallet_transaction');
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
                                                    $cashPayInsert = $this->$model->insertData($arrayPayMode,'payment_mode_details'); 
                                        }
                                        //ends
                                        // Multi mode payment
                                        if($updateData['payment_mode']!= NULL)
                                         {
                                             $payment_amt = $pay['redeemed_amount'] > 0 && $pay['redeemed_amount'] != NULL ? ($pay['payment_amount'] - $pay['redeemed_amount']) : $pay['payment_amount'];
                                             $arrayPayMode=array(
                                                            'id_payment'         => $pay['id_payment'],
                                                            'payment_amount'     => (isset($payment_amt) ? $payment_amt : NULL),
                                                            'payment_date'		 => date("Y-m-d H:i:s"),
                                                            'created_time'	     => date("Y-m-d H:i:s"),
                                                            "payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "netbanking" ? "NB":$paymentMode))),
                                                            "remark"             => "manual verify-Easebuzz",
                                                            "payment_ref_number" => $referenceId,
                                                            "payment_status"    => 1
                                                            );
                                            $cashPayInsert = $this->$model->insertData($arrayPayMode,'payment_mode_details'); 
                                         }*/
                                        $schData = [];
                                        // Generate account  number  
                                        if ($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set'] == 3) {
                                            // if ($pay['scheme_acc_number'] == '' || $pay['scheme_acc_number'] == null || $pay['scheme_acc_number'] == 0) {
                                            if (empty($pay['scheme_acc_number'])) {
                                                $ac_group_code = NULL;
                                                // Lucky draw
                                                if ($pay['is_lucky_draw'] == 1) { // Based on scheme settings 
                                                    // Update Group code in scheme_account table 
                                                    $updCode = $this->$model->updateGroupCode($pay);
                                                    $ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
                                                }
                                                $scheme_acc_number = $this->$acc_model->account_number_generator($pay['id_scheme'], $pay['branch'], $ac_group_code);
                                                if ($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0) {
                                                    $schData['scheme_acc_number'] = $scheme_acc_number;
                                                    if ($pay['id_scheme_account'] > 0) {
                                                        if (sizeof($schData) > 0) { // Update scheme account
                                                            $this->$acc_model->update_account($schData, $pay['id_scheme_account']);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        // Generate receipt number
                                        if ($pay['receipt_no_set'] == 1 && ($this->config->item('auto_pay_approval') == 1 || $this->config->item('auto_pay_approval') == 2)) {
                                            $receipt['receipt_no'] = $this->generate_receipt_no($pay['id_scheme'], $pay['branch']);
                                            $payment['status'] = $this->$model->update_receipt($pay['id_payment'], $receipt);
                                        }
                                        //Update First Payment Amount In Scheme Account
                                        $approval_type = $this->config->item('auto_pay_approval');
                                        if (($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable'] == 1 || $pay['firstPayamt_as_payamt'] == 1) && ($pay['firstPayment_amt'] == '' || $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0)) {
                                            if ($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "")) { // Fix First payable as weight
                                                $fixPayable = array('firstPayment_wgt' => $pay['metal_weight']);
                                            } else {
                                                $fixPayable = array('firstPayment_amt' => $pay['payment_amount']);
                                            }
                                            $status = $this->$acc_model->update_account($fixPayable, $pay['id_scheme_account']);
                                        }
                                    }
                                }
                            }
                        }
                        $payres_array = array('dev_remark' => $status_code . "   " . $trans_id . " " . date('Y-m-d H:i:s'));
                        $result = $this->$model->paymentDB("updatestatus", $trans_id, $payres_array);
                        $res[] = $trans_id . "- Status : " . $status_code;
                    } else {
                        $payres_array = array('dev_remark' => "No respone from gateway " . $tr . " " . date('Y-m-d H:i:s'));
                        $result = $this->$model->paymentDB("updatestatus", $tr, $payres_array);
                        $res[] = $tr . "- Status : " . $response->msg;
                    }
                }
            }
            if ($vCount > 0) {
                //echo $vCount." payment records verified successfully."; 
                echo json_encode($res);
            } else {
                //echo " No records to verify"; 
                echo json_encode($res);
            }
        } else {
            echo "Select Payments to verify.";
        }
    }
    function verify_PayUpayments($txns)
    {
        $model = self::PAY_MODEL;
        $set_model = self::SET_MODEL;
        $txn_sequence = '';
        if ($txns) {
            $gateway_info = $this->$set_model->gateway_settingsDB('get_default');
            $key = $gateway_info[0]['key'] . '|verify_payment';
            foreach ($txns['txn_ids'] as $txn) {
                $txn_sequence = $txn_sequence . $txn_sequence . '|' . $txn;
            }
            $hash_sequence = $key . $txn_sequence . '|' . $gateway_info[0]['salt'];
            $hash_value = strtolower(hash('sha512', $hash_sequence));
            $url = $gateway_info[0]['api_url'];
            $data = array(
                'key' => $gateway_info[0]['key'],
                'command' => 'verify_payment',
                'hash' => $hash_value,
                'var1' => ltrim($txn_sequence, '|'),
                'salt' => $gateway_info[0]['salt']
            );
            $response = array();
            $response = $this->httpPost($url, $data);
            if ($response->status == 1) {
                $vCount = 0;
                foreach ($response->transaction_details as $key => $trans) {
                    $trans_id = $key;
                    $record = array(
                        "bank_name" => (isset($trans->issuing_bank) ? $trans->issuing_bank : NULL),
                        "payment_mode" => (isset($trans->mode) ? $trans->mode : NULL),
                        "card_no" => (isset($trans->mode) && ($trans->mode == 'CC' || $trans->mode == 'DC') ? $trans->card_no : NULL),
                        "payu_id" => (isset($trans->mihpayid) ? $trans->mihpayid : NULL),
                        "remark" => (isset($trans->field9) ? $trans->field9 : NULL),
                        "payment_ref_number" => (isset($trans->bank_ref_num) ? $trans->bank_ref_num : NULL),
                        "payment_status" => (isset($trans->status) ? ($trans->status == 'success' ? 2 : ($trans->status == 'pending' ? 7 : ($trans->status == 'failure' ? 3 : NULL))) : NULL)
                    );
                    $this->db->trans_begin();
                    $result = $this->$model->updateGatewayResponse($record, $trans_id);
                    if ($result['id_payment'] != '' && $record['payment_status'] == 2) {
                        $payContent = $this->$model->getWalletPaymentContent($result['id_scheme_account']);
                        if ($result['redeemed_amount'] > 0) {
                            $transData = array(
                                'mobile' => $payContent['mobile'],
                                'actual_trans_amt' => $payContent['actual_trans_amt'],
                                'available_points' => ($payContent['isAvail'] == 0 ? 0 : $payContent['available_points']),
                                'isAvail' => ($payContent['isAvail'] == 0 ? 0 : 1),
                                'redeemed_amount' => $result['redeemed_amount'],
                                //'is_point_credited'=> $pay['is_point_credited'], 
                                'txnid' => $payContent['ref_trans_id'] . ' - D',
                                'branch' => $payContent['branch'],
                                'walletIntegration' => $payContent['walletIntegration'],
                                'wallet_balance_type' => $payContent['wallet_balance_type'],
                                'wallet_points' => $payContent['wallet_points'],
                                'wallet_amt_per_points' => $payContent['wallet_amt_per_points'],
                                'wallet_balance_type' => $pay['wallet_balance_type']
                            );
                            if (!empty($transData)) {
                                $this->insertWalletTrans($transData);
                            }
                        }
                    }
                    if ($this->db->trans_status() === TRUE) {
                        $this->db->trans_commit();
                        $pay_data = $this->$model->getPpayment_data($result['id_payment']);
                        $mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";
                        $this->sendSMSMail('7', $pay_data, $mailSubject, '3', $result['id_payment']);
                        $vCount = $vCount + 1;
                    } else {
                        $this->db->trans_rollback();
                    }
                }
                echo $vCount . " payment records verified successfully..";
            } else {
                echo "No records to verify";
            }
        }
    }
    function verify_hdfcpayment($txns_details)
    {
        $model = self::PAY_MODEL;
        $set_model = self::SET_MODEL;
        $gateway_info = $this->$set_model->gateway_settingsDB('get_default');
        $working_key = $gateway_info[1]['key'];   //Shared by CCAVENUES
        $access_code = $gateway_info[1]['param_1'];   //Shared by CCAVENUES 
        $vCount = 0;
        foreach ($txns_details as $txt) {
            $merchant_json_data = array(
                'order_no' => $txt['txn_ids'],
                'reference_no' => $txt['ref_no']
            );
            $merchant_data = json_encode($merchant_json_data);
            $encrypted_data = encrypt($merchant_data, $working_key);
            $postData = "request_type=JSON&access_code=" . $access_code . "&command=orderStatusTracker&response_type=string&enc_request=" . $encrypted_data;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://login.ccavenue.com/apis/servlet/DoWebTrans");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            // Get server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            $information = explode('&', $result);
            $dataSize = sizeof($information);
            $status = (explode('=', $information[0]));
            if ($status[1] == 0) { // 0 - API Call success , 1 - API call Failed
                $info_value = explode('=', $information[1]);
                if ($info_value[0] == 'enc_response') {
                    $res = decrypt($info_value[1], $working_key);
                    $trans = explode('|', $res);
                    $trans_id = $trans[22];
                    if ($trans_id != '') {
                        $record = array(
                            "payu_id" => (isset($trans[2]) ? $trans[2] : NULL),
                            "remark" => (isset($trans[1]) ? $trans[1] . '' . (isset($trans[4]) ? '-' . $trans[4] : '') : NULL),
                            "payment_ref_number" => (isset($trans[3]) ? $trans[3] : NULL),
                        );
                        /* NOTES :
                        FORMAT : status|order_status|reference_no|order_bank_ref_no|order_bank_response|order_bill_name|order_bill_email|order_bill_address|order_bill_city|order_bill_state|order_bill_country|order_bill_telephone_no|order_bill_city_zip|order_card_name|order_currency|order_date_time|order_delivery_details|order_device_type|order_fraud_status|order_gateway_id|order_iP|order_no|order_notes|order_option_type|order_shiping_name|order_ship_email|order_ship_address|order_ship_city|order_ship_state|order_ship_country|order_ship_telephone_no|order_ship_zip|order_status_date_time|order_TDS|order_amount|order_capture_amount|order_discount|order_fee_flat|order_fee_perc|order_fee_perc_value|order_gross_amount|order_tax
                        SAMPLE : 0|Unsuccessful|108530298835|000000|NOT CAPTURED|Julia Martha|||||9710881620||Visa|INR|2019-03-05 08:17:51.027||MOB|NA||HDFC|157.51.238.218||SST15517540655c7de35188ff8||OPTCRDC||||||||Unsuccessful|2019-03-05 08:19:24.233|0.0|||1554.0|0.0|0.0|0.0|27.97|1554.0|0.0|0 */
                        /*HDFC Payment Status :-
                       Aborted (transaction is cancelled by the User)
                       Awaited (transaction is processed from billing shipping page but no response is received)
                       Cancelled (transaction is cancelled by merchant )
                       Chargeback
                       Auto-Cancelled(transaction has not confirmed within 12 days hence auto cancelled by system)
                       Auto-Reversed (two identical transactions for same order number, both were successful at bank's end but we got response for only one of them, then next day during reconciliation we mark one of the transaction as auto reversed )
                       Invalid(Transaction sent to HDFC with Invalid parameters, hence could not be processed further)
                       Fraud (we update this during recon,the amount is different at bankâ™s end and at HDFC due to tampering)
                       Initiated (transaction just arrived on billing shipping page and not processed further )
                       Refunded (Transaction is refunded.)
                       Shipped (transaction is confirmed)
                       Successful
                       Unsuccessful*/
                        if ($trans[1] == 'Successful' || $trans[1] == 'Aborted' || $trans[1] == 'Awaited' || $trans[1] == 'Initiated' || $trans[1] == 'Shipped' || $trans[1] == 'Unsuccessful' || $trans[1] == 'Cancelled' || $trans[1] == 'Refunded') {
                            if ($trans[1] == 'Aborted' || $trans[1] == 'Awaited' || $trans[1] == 'Initiated' || $trans[1] == 'Unsuccessful' || $trans[1] == 'Cancelled') {
                                $record['payment_status'] = -1; // Failed
                            } else if ($trans[1] == 'Successful' || $trans[1] == 'Shipped') {
                                $record['payment_status'] = 0; // pending
                            } else if ($trans[1] == 'Refunded') {
                                $record['payment_status'] = 2; // Rejected
                            }
                        }
                        $this->db->trans_begin();
                        $result = $this->$model->updateGatewayResponse($record, $trans_id);
                        if ($result['id_payment'] != '' && $record['payment_status'] == 2) {
                            $payContent = $this->$model->getWalletPaymentContent($result['id_scheme_account']);
                            if ($result['redeemed_amount'] > 0) {
                                $transData = array(
                                    'mobile' => $payContent['mobile'],
                                    'actual_trans_amt' => $payContent['actual_trans_amt'],
                                    'available_points' => ($payContent['isAvail'] == 0 ? 0 : $payContent['available_points']),
                                    'isAvail' => ($payContent['isAvail'] == 0 ? 0 : 1),
                                    'redeemed_amount' => $result['redeemed_amount'],
                                    //'is_point_credited'=> $pay['is_point_credited'], 
                                    'txnid' => $payContent['ref_trans_id'] . ' - D',
                                    'branch' => $payContent['branch'],
                                    'walletIntegration' => $payContent['walletIntegration'],
                                    'wallet_balance_type' => $payContent['wallet_balance_type'],
                                    'wallet_points' => $payContent['wallet_points'],
                                    'wallet_amt_per_points' => $payContent['wallet_amt_per_points'],
                                    'wallet_balance_type' => $pay['wallet_balance_type']
                                );
                                if (!empty($transData)) {
                                    $this->insertWalletTrans($transData);
                                }
                            }
                        }
                        if ($this->db->trans_status() === TRUE) {
                            $this->db->trans_commit();
                            $pay_data = $this->$model->getPpayment_data($result['id_payment']);
                            $mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";
                            $this->sendSMSMail('7', $pay_data, $mailSubject, '3', $result['id_payment']);
                            $vCount = $vCount + 1;
                        } else {
                            $this->db->trans_rollback();
                        }
                    }
                }
            } else {
                //print_r($information);
            }
        }
        echo $vCount . ' payments verified.';
    }
    function verifyWithTechProcess($data)
    {
        $model = self::PAY_MODEL;
        $set_model = self::SET_MODEL;
        $transData = $data['transData'];
        $response_msg = [];
        foreach ($transData as $trans) {
            $payData = array(
                'phone' => $trans['mobile'],
                'firstname' => $trans['name'],
                'txnid' => $trans['txn_id'],
                'amount' => $trans['amount'],
                'tpsl_txn_id' => $trans['payu_id'],
                'txnDate' => $trans['date_payment']
            );
            // $gateway_info = $this->$set_model->gateway_settingsDB('get_default');
            $gateway_info = $this->$model->getBranchGatewayData($data['id_branch'], $data['pg_code']);
            $iv = $gateway_info['param_4'];
            $key = $gateway_info['param_1'];
            $mrctCode = $gateway_info['param_3'];
            //print_r($mrctCode);exit;
            /* $mrctCode = $gateway_info[2]['m_code'];  
             $key = $gateway_info[2]['key'];   
             $iv = $gateway_info[2]['param_1']; */
            $ClientMetaData = $payData['phone'];
            $reqType = 'O';
            $currency = 'INR';
            $returnURL = site_url('paymt/techProcessResponseURL');
            $ShoppingCartDetails = 'FIRST_' . $payData['amount'] . '_0.0';
            $locatorURL = "https://www.tpsl-india.in/PaymentGateway/TransactionDetailsNew.wsdl";  // LIVE 
            $timeOut = '60000';
            $transactionRequestBean = new TransactionRequestBean();
            //Set all values here
            $transactionRequestBean->setMerchantCode($mrctCode);
            $transactionRequestBean->setITC($ClientMetaData);
            $transactionRequestBean->setRequestType($reqType);
            $transactionRequestBean->setCurrencyCode($currency);
            $transactionRequestBean->setReturnURL($returnURL);
            $transactionRequestBean->setShoppingCartDetails($ShoppingCartDetails); // conditional
            $transactionRequestBean->setTxnDate($payData['txnDate']);
            $transactionRequestBean->setKey($key);
            $transactionRequestBean->setIv($iv);
            $transactionRequestBean->setWebServiceLocator($locatorURL);
            $transactionRequestBean->setTimeOut($timeOut);
            $transactionRequestBean->setCustomerName($payData['firstname']);
            $transactionRequestBean->setMerchantTxnRefNumber($payData['txnid']);
            $transactionRequestBean->setAmount($payData['amount']);
            if ($reqType == "R") {
                $transactionRequestBean->setTPSLTxnID($payData['tpsl_txn_id']);
            }
            $responseDetails = $transactionRequestBean->getTransactionToken();
            $responseDetails = (array) $responseDetails;
            $response = $responseDetails[0];
            // print_r($response);exit;
            $payData = explode("|", $response);
            $status_code = "";
            $status_msg = "";
            $err_msg = "";
            $txn_id = "";
            $payu_id = "";
            $vCount = 0;
            foreach ($payData as $pay) {
                $r = explode("=", $pay);
                if ($r[0] === "txn_status")
                    $status_code = $r[1];
                if ($r[0] === "txn_msg")
                    $status_msg = $r[1];
                if ($r[0] === "txn_err_msg")
                    $err_msg = $r[1];
                if ($r[0] === "clnt_txn_ref")
                    $txn_id = $r[1];
                if ($r[0] === "tpsl_txn_id")
                    $payu_id = $r[1];
            }
            if ($txn_id != "" && $status_code != 9999) {
                $updateData = array(
                    "payu_id" => (isset($payu_id) ? $payu_id : NULL), // tpsl_txn_id
                    "remark" => $status_msg . ' - ' . $status_code . ' - ' . ($err_msg != 'NA' ? $err_msg : ''),
                    "payment_status" => ($status_code == '0300' ? $this->payment_status['awaiting'] : ($status_code == '0392' ? $this->payment_status['cancel'] : ($status_code == '0399' ? $this->payment_status['failure'] : ($status_code == '0400' ? $this->payment_status['refund'] : $this->payment_status['failure']))))
                );
                $this->db->trans_begin();
                $result = $this->$model->updateGatewayResponse($updateData, $txn_id);
                if ($result['id_payment'] != '' && $updateData['payment_status'] == 2) {
                    $payContent = $this->$model->getWalletPaymentContent($result['id_scheme_account']);
                    if ($result['redeemed_amount'] > 0) {
                        $transData = array(
                            'mobile' => $payContent['mobile'],
                            'actual_trans_amt' => $payContent['actual_trans_amt'],
                            'available_points' => ($payContent['isAvail'] == 0 ? 0 : $payContent['available_points']),
                            'isAvail' => ($payContent['isAvail'] == 0 ? 0 : 1),
                            'redeemed_amount' => $result['redeemed_amount'],
                            //'is_point_credited'=> $pay['is_point_credited'], 
                            'txnid' => $payContent['ref_trans_id'] . ' - D',
                            'branch' => $payContent['branch'],
                            'walletIntegration' => $payContent['walletIntegration'],
                            'wallet_balance_type' => $payContent['wallet_balance_type'],
                            'wallet_points' => $payContent['wallet_points'],
                            'wallet_amt_per_points' => $payContent['wallet_amt_per_points'],
                            'wallet_balance_type' => $pay['wallet_balance_type']
                        );
                        if (!empty($transData)) {
                            $this->insertWalletTrans($transData);
                        }
                    }
                }
                if ($this->db->trans_status() === TRUE) {
                    $this->db->trans_commit();
                    $pay_data = $this->$model->getPpayment_data($result['id_payment']);
                    $mailSubject = "Reg- " . $this->company['company_name'] . " saving scheme payment details";
                    $this->sendSMSMail('7', $pay_data, $mailSubject, '3', $result['id_payment']);
                    $vCount = $vCount + 1;
                } else {
                    $this->db->trans_rollback();
                }
            } else {
                $response_msg[] = array('msg' => $status_msg, 'clnt_txn_ref' => $txn_id);
            }
        }
        if ($vCount > 0) {
            echo $vCount . " payment records verified successfully...";
        } else {
            echo " No records to verify. Message " . print_r($response_msg);
        }
    }
    function PaymentGateway()
    {
        $model = self::PAY_MODEL;
        $data = $this->$model->get_payment_gateway();
        echo json_encode($data);
    }
    public function thermal_invoice($id, $type, $date = "")
    {
        $model = self::PAY_MODEL;
        $set = self::SET_MODEL;
        $cus = self::CUS_MODEL;
        $account = self::ACC_MODEL;
        $wallet = self::WALL_MODEL;
        //echo $id;exit;
        $data['records'] = $this->$model->get_invoiceData($id, "");
        if ($type == 'Payment') {
            $data['records_sch'] = $this->$model->get_paymentContent($data['records'][0]['id_scheme_account']);
            $data['gstSplitup'] = $this->$model->get_gstSplitupData($data['records'][0]['id_scheme'], $data['records'][0]['date_add']);
            $paidinstll = $this->$model->get_paidinstallmentcount($data['records'][0]['id_scheme_account']);
            $i = 1;
            foreach ($paidinstll as $x => $x_value) {
                if ($x_value['id_payment'] == $id) {
                    $data['records'][0]['installment'] = $i;
                }
                $i++;
            }
            if ($this->branch_settings == 1) {
                $data['comp_details'] = $this->$set->get_branchcompany($data['records'][0]['id_branch']);
            } else {
                $data['comp_details'] = $this->$set->get_company();
            }
            $data['records'][0]['amount_in_words'] = $this->no_to_words($data['records'][0]['payment_amount']);
            //   $html = $this->load->view('include/receipt_thermal', $data,true);
            //   print_r($html);exit;
            $receipt_content = $this->load->view('include/receipt_thermal_prn', $data, true);
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="receipt_' . date('Ymd_His') . '_lmx.prn"');
            header('Content-Length: ' . strlen($receipt_content));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Expires: 0');
            echo $receipt_content;
            exit;
        } else if ($type == 'Customer') {
            $data['customer'] = $this->$cus->get_cust($id);
            $html = $this->load->view('include/cusdetails_thermal', $data, true);
        } else if ($type == 'CloseAccount') {
            if ($this->branch_settings == 1) {
                $data['comp_details'] = $this->$set->get_branchcompany($data['records'][0]['id_branch']);
            } else {
                $data['comp_details'] = $this->$set->get_company();
            }
            $data['account'] = $this->$account->get_closed_account_by_id($id);
            $html = $this->load->view('include/schemeaccount', $data, true);
            $this->load->helper(array('dompdf', 'file'));
            $dompdf = new DOMPDF();
            $dompdf->load_html($html);
            $dompdf->set_paper("a4", "portriat");
            $dompdf->render();
            while(ob_get_level()) ob_end_clean();
            $dompdf->stream("receipt1.pdf", array('Attachment' => 0));
            //die();
        } else if ($type == 'WalletTransaction') {
            if ($this->branch_settings == 1) {
                $data['comp_details'] = $this->$set->get_branchcompany($data['records'][0]['id_branch']);
            } else {
                $data['comp_details'] = $this->$set->get_company();
            }
            $data['wallet'] = $this->$wallet->wallet_transactionDB('get', $id, '', $date, $date);
            $html = $this->load->view('include/wallet_thermal', $data, true);
        }
        $this->load->helper(array('dompdf', 'file'));
        $dompdf = new DOMPDF();
        $dompdf->load_html($html);
        $customPaper = array(0, 0, 55, 50);
        $dompdf->set_paper($customPaper, "portriat");
        $dompdf->render();
        while(ob_get_level()) ob_end_clean();
        $dompdf->stream("receipt1.pdf", array('Attachment' => 0));
    }
    function generateotp()
    {
        $model = self::PAY_MODEL;
        $account = self::ACC_MODEL;
        $chit_model = self::CHIT_MODEL;
        $mail_model = self::MAIL_MODEL;
        $id_customer = $this->input->post('id_customer');
        $data = $this->$model->get_customer($id_customer);
        $payOTP_exp = $this->$model->payOTP_exp();
        $mobile = $data['mobile'];
        $firstname = $data['firstname'];
        $OTP = mt_rand(100001, 999999);
        //$OTP = 111111;  
        $this->session->set_userdata('pay_OTP', $OTP);
        //$duration = $this->config->item('payOTP_exp'); // in seconds
        $duration = $payOTP_exp; // in seconds
        //$duration = 10; // in seconds
        $this->session->set_userdata('pay_OTP_expiry', time() + $duration);
        //$message="Dear ".$firstname.", Your OTP for  Saving Scheme Payment is ".$OTP." Will expire within ". $duration." Sec. ";
        $message = "Dear " . $firstname . ", Your OTP for Saving Scheme Payment is " . $OTP . ". Will expire within " . $duration . " Sec. Regards, SRI KRISHNA NAGAI MALIGAI.";
        if ($this->config->item('sms_gateway') == '1') {
            $this->sms_model->sendSMS_MSG91($mobile, $message, '', 1007833095459825327);
        } elseif ($this->config->item('sms_gateway') == '2') {
            $this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
        } elseif ($this->config->item('sms_gateway') == '3') {
            $this->sms_model->sendSMS_SpearUC($mobile, $message, '', '');
        } elseif ($this->config->item('sms_gateway') == '4') {
            $this->sms_model->sendSMS_Asterixt($mobile, $message, '', '');
        } elseif ($this->config->item('sms_gateway') == '5') {
            $this->sms_model->sendSMS_Qikberry($mobile, $message, '', '');
        }
        $otp['otp_gen_time'] = date("Y-m-d H:i:s");
        $otp['otp_code'] = $OTP;
        if ($data['email'] != '') {
            $edata['company_details'] = $this->company;
            $edata['type'] = 4;
            $edata['otp'] = $OTP;
            $edata['duration'] = $duration;
            $to = $data['email'];
            $subject = "Reg - OTP for " . $this->comp['company_name'] . " saving scheme payment";
            $emessage = $this->load->view('include/emailAccount', $edata, true);
            $sendEmail = $this->$mail_model->send_email($to, $subject, $emessage);
        }
        $status = $this->$account->otp_insert($otp);
        $data = array('result' => 3, 'msg' => '"OTP Sent Successfully', 'otp' => $OTP);
        echo json_encode($data);
    }
    function update_otp()
    {
        $accountmodel = self::ACC_MODEL;
        $payment = self::PAY_MODEL;
        $otp = $this->input->post('otp');
        $data = $this->$accountmodel->select_otp($otp);
        if ($otp == $this->session->userdata('pay_OTP')) {
            if (time() >= $this->session->userdata('pay_OTP_expiry')) {
                $this->session->unset_userdata('pay_OTP');
                $this->session->unset_userdata('pay_OTP_expiry');
                $data = array('result' => 5, 'msg' => 'OTP has been expired');
            } else if ($otp = $this->session->userdata('pay_OTP')) {
                $data['is_verified'] = '1';
                $data['verified_time'] = date("Y-m-d H:i:s");
                $status = $this->$accountmodel->otp_update_payment($data, $data['id_otp']);
                $data = array('result' => 1, 'msg' => 'OTP updated successfully');
            }
        } else {
            $data = array('result' => 6, 'msg' => 'Invalid OTP');
        }
        echo json_encode($data);
    }
    function resend_otp()
    {
        $model = self::PAY_MODEL;
        $account = self::ACC_MODEL;
        $chit_model = self::CHIT_MODEL;
        $mail_model = self::MAIL_MODEL;
        $id_customer = $this->input->post('id_customer');
        $data = $this->$model->get_customer($id_customer);
        $payOTP_exp = $this->$model->payOTP_exp();
        $mobile = $data['mobile'];
        $firstname = $data['firstname'];
        $OTP = mt_rand(100001, 999999);
        $this->session->set_userdata('pay_OTP', $OTP);
        $duration = $payOTP_exp; // in seconds 
        $this->session->set_userdata('pay_OTP_expiry', time() + $duration);
        //$message="Dear ".$firstname.", Your OTP for   Saving Scheme Payment is ".$OTP." Will expire within ". $duration." Sec. ";
        $message = "Dear " . $firstname . ", Your OTP for Saving Scheme Payment is " . $OTP . ". Will expire within " . $duration . " Sec. Regards, SRI KRISHNA NAGAI MALIGAI.";
        if ($this->config->item('sms_gateway') == '1') {
            $this->sms_model->sendSMS_MSG91($mobile, $message, '', 1007833095459825327);
        } elseif ($this->config->item('sms_gateway') == '2') {
            $sendSMS = $this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
        } elseif ($this->config->item('sms_gateway') == '3') {
            $sendSMS = $this->sms_model->sendSMS_SpearUC($mobile, $message, '', '');
        } elseif ($this->config->item('sms_gateway') == '4') {
            $sendSMS = $this->sms_model->sendSMS_Asterixt($mobile, $message, '', '');
        } elseif ($this->config->item('sms_gateway') == '5') {
            $sendSMS = $this->sms_model->sendSMS_Qikberry($mobile, $message, '', '');
        }
        if ($data['email'] != '') {
            $edata['company_details'] = $this->company;
            $edata['type'] = 4;
            $edata['otp'] = $OTP;
            $edata['duration'] = $duration;
            $to = $data['email'];
            $subject = "Reg - OTP for " . $this->comp['company_name'] . " saving scheme payment";
            $message = $this->load->view('include/emailAccount', $edata, true);
            $sendEmail = $this->$mail_model->send_email($to, $subject, $message);
        }
        $otp['otp_gen_time'] = date("Y-m-d H:i:s");
        $otp['otp_code'] = $OTP;
        $status = $this->$account->otp_insert($otp);
        $data = array('result' => 3, 'msg' => '"OTP Sent Successfully', 'otp' => $OTP);
        echo json_encode($data);
    }
    // payment data uSING Trans Id//HH
    public function payments_data()
    {
        $model = self::PAY_MODEL;
        $data['main_content'] = self::PAY_VIEW . 'paymentdata/pay_list';
        //print_r($data);exit;
        $this->load->view('layout/template', $data);
    }
    function payments_data_list()
    {
        $model = self::PAY_MODEL;
        $ref_trans_id = $_POST['ref_trans_id'];
        //$id_branch=$_POST['id_branch'];
        //	print_r($ref_trans_id);exit;
        $data = $this->$model->get_payments_data_list($ref_trans_id);
        echo json_encode($data);
    }
    /* Settled Payment functions Begins */
    /**
     *  PAYU get_settlement_details :
     *  To retrieve Settlement Details for the merchant. The input is the date for which Settlement Details are required.
     *  HASH formula :  sha512(key|command|var1|salt)
     *  RESPONSE DATA :-
     *  Array
        (
             [status] => 1
             [msg] => 6565 transactions settled on 2015-08-01
             [Txn_details] => Array
             (
                 [0] => Array
                 (
                     [payuid] => 204131224
                     [txnid] => GOFLCF519911416076450
                     [txndate] => 2014-11-16 00:08:40
                     [mode] => DC
                     [amount] => 2580.00
                     [requestid] => 262698935
                     [requestdate] => 2015-08-01 17:43:25
                     [requestaction] => capture
                     [requestamount] => 186.00
                     [mer_utr] => CITIH15213701843
                     [mer_service_fee] => 0.00000
                     [mer_service_tax] => 0.00000
                     [mer_net_amount] => 186.00000
                     [bank_name] => VISA
                     [issuing_bank] => BOB
                     [merchant_subvention_amount] => 0.00
                     [cgst] => 0.00000
                     [igst] => 0.00000
                     [sgst] => 0.00000
                  )
             )
        )
     * 
     * 
     */
    function gtway_settlement($type)
    {
        $SETT_MOD = "admin_settings_model";
        switch ($type) {
            case 'sync_view':
                $data['main_content'] = self::PAY_VIEW . 'sync_settled_txns';
                $data['access'] = $this->$SETT_MOD->get_access('admin_payment/gtway_settlement/sync_view');
                $this->load->view('layout/template', $data);
        }
    }
    function fetch_settled_payments()
    {
        /*$file    = "../api/payusettled.txt";
        $isUnsyncAvail = json_decode(file_get_contents($file));*/
        $records = array();
        $model = self::PAY_MODEL;
        $date = date('Y-m-d', strtotime($_POST['request_date']));
        if ($_POST['id_gateway'] == 2) {
            $res = $this->hdfcSettlement($_POST['request_date']);
            echo $res;
        } else if ($_POST['id_gateway'] == 4) { // CashFree 
            $response = $this->cashfreeSettlement($_POST);
            echo $response;
        } else {
            /* if(!$isUnsyncAvail){*/
            if ($date) {
                $key = $this->config->item('key') . '|get_settlement_details';
                /*hash formula :  sha512(key|command|var1|salt)*/
                $hash_sequence = $key . '|' . $date . '|' . $this->config->item('salt');
                $hash_value = strtolower(hash('sha512', $hash_sequence));
                $url = $this->config->item('verify_url');
                $data = array(
                    'key' => $this->config->item('key'),
                    'command' => 'get_settlement_details',
                    'hash' => $hash_value,
                    'var1' => $date,
                    'salt' => $this->config->item('salt')
                );
                $response = array();
                $response = $this->httpPost($url, $data);
                if ($response->status == 1) {
                    $vCount = 0;
                    /*foreach($response->Txn_details as $trans)
                    { 
                        $trans_id = $trans->txnid;
                        $gateway_id = $trans->payuid;
                        $record = array("is_settled" => 1);
                        $result =	$this->$model->updateSettledPayments($record,$trans_id,$gateway_id);		
                        if($result['status'] ==1)
                        {
                            $vCount = $vCount + 1;
                        }	
                    }*/
                    //print_r($response->Txn_details);exit;
                    foreach ($response->Txn_details as $trans) {
                        $records[] = array(
                            "is_settled" => 1,
                            'gateway_id' => $trans->txnid,
                            'payuid' => $trans->payuid,
                            'amount' => $trans->amount,
                            'requestdate' => $trans->requestdate,
                            'requestaction' => $trans->requestaction
                        );
                        $insData = array(
                            'txnid' => $trans->txnid,
                            'id_payGateway' => 1,
                            'gateway_id' => $trans->payuid,
                            'payment_date' => $trans->requestdate,
                            'mer_net_amount' => $trans->mer_net_amount,
                            'mer_service_fee' => $trans->mer_service_fee,
                            'cgst' => $trans->cgst,
                            'igst' => $trans->igst,
                            'sgst' => $trans->sgst,
                            'gateway_requestaction' => $trans->requestaction
                        );
                        //	print_r($this->db->last_query());exit;
                        $this->$model->insertSettledPay($insData);
                    }
                    /*if($response->Txn_details){
                        $content = json_encode($response,TRUE);
                        file_put_contents($file,$content);	
                    }*/
                }
                echo json_encode(array('transactions' => $records, 'msg' => $response->msg));
                // redirect('settled_payments/sync');
            }
            /*}else{
                echo json_encode(array('transactions'=>$records,'msg'=>'Records pending to update !!'));
            }*/
        }
    }
    /**
     * 
     * CashFree - Settlement API
     * 
     * DOCUMENT LINKS :
     * 	1.https://docs.cashfree.com/docs/rest/guide/#fetch-all-settlements
     * 
     * REQUEST PARAMETERS
     *	Parameter	Required	Description
     *	appId		Yes			Your app id
     *	secretKey	Yes			Your Secret Key
     *	startDate	Yes			Date(in the format of YYYY-MM-DD), from which you want the data
     *	endDate		Yes			Date till you want the data (this date is included)
     *	lastId		No			Use it for paginated response. Settlements having id greater than this value will be returned
     *	count		No			Number of settlements you want to receive. Default is 20 and max is 50.
     * 
     * RESPONSE PARAMETERS
     *	Parameter	Description
     *	status		Status of API call. Values are - OK and ERROR
     *	settlements	List of settlements
     *	message		response message (will have the reason when status is sent as ERROR)
     *	lastId		ID of the last transaction returned. Use it in your next request if current one didn€t return all the transactions
     * 
     * SETTLEMENT ARRAY
     *	Parameter			Description
     *	id					Settlement Id (use it to fetch transactions that are part of this settlement)
     *	totalTxAmount		Total transactions amount
     *	settlementAmount	Amount after deducting the TDR
     *	adjustment			Any adjustments (because of refunds OR disputes)
     *	amountSettled		Amount settled after including the adjustments
     *	transactionFrom		transaction included from this day
     *	transactionTill		transactions included till this day
     *	utr	Bank 			Reference number
     *	settledOn			Time of settlement (this could be different than credit date shown on the account statement)
     *  
     */
    function cashFreeCurl($postData, $api_url)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $curl = curl_init();
        if ($err) {
            echo "cURL Error #:" . $err;
            // print_r($err);exit;
        } else {
            //echo "<pre>";print_r($response);exit;  
            return json_decode($response);
        }
    }
    function cashfreeSettlement($req_data)
    {
        $model = self::PAY_MODEL;
        $gateway_info = $this->$model->getBranchGatewayData($req_data['id_branch'], 4);
        $secretKey = $gateway_info['param_1'];
        $appId = $gateway_info['param_3'];
        if ($req_data['request_date'] != $this->session->userdata('settlement_date') || $this->session->userdata('id_branch') != $req_data['id_branch']) {
            $last_settlement_id = '';
            $last_trans_setl_id = '';
        } else {
            $last_settlement_id = (!$this->session->userdata('last_settlement_sync') ? '' : $this->session->userdata('last_settlement_sync'));
            $last_trans_setl_id = (!$this->session->userdata('last_trans_setl_id') ? '' : $this->session->userdata('last_trans_setl_id'));
        }
        $vCount = 0;
        $records = [];
        //echo "<pre>";print_r($this->session->all_userdata());exit;
        // FETCH SETTLEMENTS
        $postData = "appId=" . $appId . "&secretKey=" . $secretKey . "&startDate=" . date('Y-m-d', strtotime($req_data['request_date'])) . "&endDate=" . date('Y-m-d', strtotime($req_data['request_date'])) . "&lastId=" . $last_settlement_id . "&count=50";
        $api = $gateway_info['api_url'] . "api/v1/settlements";
        /* echo $postData;
        echo $this->db->last_query();
        echo "<pre>";print_r($gateway_info);
        echo "<pre>";print_r($result);exit;  
        echo $result->message;*/
        $result = $this->cashFreeCurl($postData, $api);
        $api_msg = $result->message;
        if ($result->status == "OK") {
            $last_settlement_id = $result->lastId;
            foreach ($result->settlements as $settlement) {
                // FETCH SETTLEMENT TRANSACTION DETAILS
                $trans_det_postData = "appId=" . $appId . "&secretKey=" . $secretKey . "&settlementId=" . $settlement->id . "&lastId=" . $last_trans_setl_id . "&count=50";
                $trans_det_api = $gateway_info['api_url'] . "api/v1/settlement";
                $trans_detail = $this->cashFreeCurl($trans_det_postData, $trans_det_api);
                if ($trans_detail->status == "OK" && $trans_detail->reason == '') {
                    $last_trans_setl_id = $trans_detail->lastId;
                    foreach ($trans_detail->transactions as $trans) {
                        $records[] = array(
                            "is_settled" => 1,
                            'gateway_id' => $trans->referenceId,
                            'txnid' => $trans->orderId,
                            'amount' => $trans->txAmount,
                            'requestdate' => $trans->txTime,
                            'requestaction' => 'Settled'
                        );
                        $insData = array(
                            'txnid' => $trans->orderId,
                            'id_payGateway' => 1,
                            'gateway_id' => $trans->referenceId,
                            'payment_date' => $trans->txTime,
                            'mer_net_amount' => $trans->mer_net_amount,
                            'mer_service_fee' => $trans->serviceCharge,
                            'cgst' => $trans->cgst,
                            'igst' => $trans->serviceTax,
                            'sgst' => $trans->sgst,
                            'gateway_requestaction' => 'Settled'
                        );
                        $this->$model->insertSettledPay($insData);
                    }
                } else {
                    echo $trans_detail->reason;
                }
            }
        }
        $last_settlement_sync = array(
            'settlement_date' => $req_data['request_date'],
            'last_settlement_id' => $last_settlement_id,
            'last_trans_setl_id' => $last_trans_setl_id,
            'id_branch' => $req_data['id_branch']
        );
        $this->session->set_userdata($last_settlement_sync);
        if (sizeof($records) > 0) {
            $msg = sizeof($records) . " transactions settled on " . $req_data['request_date'];
        } else {
            $msg = "No pending transaction records.";
        }
        return json_encode(array('transactions' => $records, 'msg' => $msg, 'api_msg' => $api_msg));
    }
    // .CashFree Settlement //
    function hdfcApiCurl($post_data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://login.ccavenue.com/apis/servlet/DoWebTrans");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        // Get server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
    function hdfcSettlement($settl_date)
    {
        $model = self::PAY_MODEL;
        $gateway_info = $this->$set_model->gateway_settingsDB('get_default');
        $working_key = $gateway_info[1]['key'];   //Shared by CCAVENUES
        $access_code = $gateway_info[1]['param_1'];   //Shared by CCAVENUES 
        $vCount = 0;
        $records = [];
        $err_msg = "";
        $setl_date = array(
            'settlement_date' => $settl_date
        );
        $enc_data = json_encode($setl_date);
        $encrypted_data = encrypt($enc_data, $working_key);
        $postData = "request_type=JSON&access_code=" . $access_code . "&command=payoutSummary&response_type=JSON&enc_request=" . $encrypted_data . "&version=1.2";
        // echo "Post data : ".$postData."<br/>";
        $result = $this->hdfcApiCurl($postData);
        $information = explode('&', $result);
        $dataSize = sizeof($information);
        $status = (explode('=', $information[0]));
        if ($status[1] == 0) { // 0 - API Call success , 1 - API call Failed
            $info_value = explode('=', $information[1]);
            if ($info_value[0] == 'enc_response') {
                $res = decrypt(trim($info_value[1]), $working_key);
                $resData = json_decode($res);
                //echo "<b>Payout Summary : </b>\n<pre>";print_r($resData); 
                $summary = $resData->Payout_Summary_Result->payout_summary_list->payout_summary_details;
                $errdesc = $resData->Payout_Summary_Result->error_desc;
                if (!empty($errdesc)) {
                    $err_arr = explode(':', $errdesc);
                    $err_msg = $err_arr[1];
                }
                $hdfc_pay_Id = $summary->pay_Id;
                if (!empty($hdfc_pay_Id)) {
                    $det_data = array(
                        'settlement_date' => $settl_date,
                        'pay_id' => $hdfc_pay_Id
                    );
                    $det_enc_data = json_encode($det_data);
                    $det_encrypted_data = encrypt($det_enc_data, $working_key);
                    $postData = "request_type=JSON&access_code=" . $access_code . "&command=payIdDetails&response_type=JSON&enc_request=" . $det_encrypted_data . "&version=1.2";
                    // echo "Post data : ".$postData."<br/>";
                    $detRes = $this->hdfcApiCurl($postData);
                    $details = explode('&', $detRes);
                    $resp = (explode('=', $details[0]));
                    if ($status[1] == 0) { // 0 - API Call success , 1 - API call Failed
                        $value = explode('=', $details[1]);
                        if ($value[0] == 'enc_response') {
                            $decrypted = decrypt(trim($value[1]), $working_key);
                            $detData = json_decode($decrypted);
                            //echo "<b>Pay id Detail Summary : </b>\n<pre>";print_r($detData); 
                            $txn_details = $detData->pay_id_details_Result->pay_id_txn_details_list->pay_id_txn_details;
                            foreach ($txn_details as $trans) {
                                $records[] = array(
                                    "is_settled" => 1,
                                    'txnid' => $trans->order_no,
                                    'gateway_id' => $trans->ccavenue_ref_no,
                                    'amount' => $trans->amount,
                                    'requestdate' => $trans->date_time,
                                    'requestaction' => $trans->txn_type
                                );
                                $insData = array(
                                    'txnid' => $trans->order_no,
                                    'id_payGateway' => 2,
                                    'gateway_id' => $trans->ccavenue_ref_no,
                                    'payment_date' => $trans->date_time,
                                    'mer_net_amount' => $trans->amt_payable,
                                    'mer_service_fee' => $trans->fees,
                                    'cgst' => 0.00,
                                    'igst' => $trans->tax,
                                    'sgst' => 0.00,
                                    'gateway_requestaction' => $trans->txn_type
                                );
                                $this->$model->insertSettledPay($insData);
                            }
                        }
                    }
                }
            }
        } else {
            echo "API Call Failed";
        }
        return json_encode(array('transactions' => $records, 'msg' => ($err_msg == "" ? sizeof($records) . " transactions settled on " . $settl_date : $err_msg)));
    }
    function updateGtwaySettlement()
    {
        /*$file    = "../api/payusettled.txt";
        $transactions = json_decode(file_get_contents($file),true);*/
        $model = self::PAY_MODEL;
        $transactions = $this->$model->settledTxnsToUpdate();
        $vCount = 0;
        $i = 0;
        $pending_avail = 0;
        if ($transactions) {
            $this->db->trans_begin();
            foreach ($transactions as $key => $trans) {
                if ($i < 100) {
                    $trans_id = $trans['txnid'];
                    $gateway_id = $trans['gateway_id'];
                    $record = array(
                        "is_settled" => 1,
                        'mer_net_amount' => $trans['mer_net_amount'],
                        'mer_service_fee' => $trans['mer_service_fee'],
                        'cgst' => $trans['cgst'],
                        'igst' => $trans['igst'],
                        'sgst' => $trans['sgst'],
                        'gateway_requestaction' => $trans['gateway_requestaction']
                    );
                    $result = $this->$model->updateSettledPayments($record, $trans_id, $gateway_id);
                    if ($result) {
                        $vCount = $vCount + 1;
                        $this->$model->updatePayuSettledTrans(array('is_updated' => 1), $trans_id, $gateway_id);
                        /*unset($transactions['Txn_details'][$key]);*/
                    }
                } else {
                    $pending_avail = 1;
                }
                $i++;
            }
            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
            } else {
                $this->db->trans_rollback();
            }
            /*if($transactions['Txn_details']){
                $content = json_encode($transactions,TRUE);
                file_put_contents($file,$content);	
                $pending_avail = 1;
            }else{
                file_put_contents($file,'');	
            }*/
            echo json_encode(array('msg' => 'Updated transactions.', 'pending_avail' => $pending_avail));
        } else {
            echo json_encode(array('msg' => 'No transactions to update', 'pending_avail' => $pending_avail));
        }
    }
    /*End of Settled Payment functions */
    //offline date insert manual
    function insert_trans_record()
    {
        $data['main_content'] = self::PAY_VIEW . "insertTrans";
        $this->load->view('layout/template', $data);
    }
    public function instrans_post()
    {
        $model = self::PAY_MODEL;
        $ins_tran = $this->input->post('instran');
        //echo"<pre>";print_r($_POST);	
        $instran_info = array(
            'client_id' => (isset($ins_tran['client_id']) ? $ins_tran['client_id'] : NULL),
            'payment_date' => (isset($ins_tran['payment_date']) ? $ins_tran['payment_date'] : NULL),
            'custom_entry_date' => (isset($ins_tran['payment_date']) ? $ins_tran['payment_date'] : NULL),
            'amount' => (isset($ins_tran['amount']) ? $ins_tran['amount'] : NULL),
            'weight' => (isset($ins_tran['weight']) ? $ins_tran['weight'] : NULL),
            'rate' => (isset($ins_tran['rate']) ? $ins_tran['rate'] : NULL),
            'metal' => 1,
            'record_to' => 2,
            'payment_type ' => 2,
            'payment_mode' => (isset($ins_tran['payment_mode']) ? $ins_tran['payment_mode'] : NULL),
            'ref_no' => (isset($ins_tran['ref_no']) ? $ins_tran['ref_no'] : NULL),
            'new_customer' => (isset($ins_tran['active']) ? $ins_tran['active'] : NULL),
            'discountAmt' => (isset($ins_tran['discountAmt']) ? $ins_tran['discountAmt'] : 0),
            'id_branch' => (isset($ins_tran['id_branch']) ? $ins_tran['id_branch'] : NULL),
            'payment_status' => (isset($ins_tran['payment_status']) ? $ins_tran['payment_status'] : NULL),
            'receipt_no' => (isset($ins_tran['receipt_no']) ? $ins_tran['receipt_no'] : NULL),
            'installment_no' => (isset($ins_tran['installment_no']) ? $ins_tran['installment_no'] : NULL),
            'remarks' => (isset($ins_tran['remarks']) ? $ins_tran['remarks'] : NULL),
            //'emp_code'			=>(isset($ins_tran['emp_code'])?$ins_tran['emp_code']:NULL)
            'transfer_date' => date("Y-m-d H:i:s"),
            'emp_code' => $this->session->userdata('uid')
        );
        $this->db->trans_begin();
        $res = $this->$model->instrans_rec($instran_info);
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            $this->session->set_flashdata('chit_alert', array('message' => 'Payment inserted successfully', 'class' => 'success', 'title' => 'Offline payment Status'));
            redirect('payment/list');
        } else {
            $this->db->trans_rollback();
            $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Offline payment Status'));
            redirect('payment/list');
        }
    }
    //offline date insert manual
    //Chit Deposit
    function get_EstimationDetails()
    {
        $model = self::PAY_MODEL;
        $data = $this->$model->get_EstimationDetails($_POST);
        echo json_encode($data);
    }
    //Chit Deposit
    //AT Verify Payment
    function At_verify_payment()
    {
        $model = self::PAY_MODEL;
        $transData = $_POST['req_data'];
        $gateway_info = $this->$model->getBranchGatewayData('', 4);
        $secretKey = $gateway_info['param_1'];
        $appId = $gateway_info['param_3'];
        $vCount = 0;
        $return_data = array();
        if (sizeof($transData) > 0) {
            foreach ($transData as $tran) {
                //$postData = "appId=".$appId."&secretKey=".$secretKey."&orderId=".$tran['txn_id'];
                $postData = "appId=" . $appId . "&secretKey=" . $secretKey . "&orderId=" . $tran['id_transaction'];
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $gateway_info['api_url'] . 'api/v1/order/info/status',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $postData,
                    // Getting  server response parameters //
                    CURLOPT_HTTPHEADER => array(
                        "cache-control: no-cache",
                        "content-type: application/x-www-form-urlencoded"
                    ),
                ));
                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                if ($err) {
                    echo "cURL Error #:" . $err;
                } else {
                    $response = json_decode($response);
                    if ($response->status == "OK") { // OK - Api call success  - ERROR Failed
                        $status_code = $response->txStatus; // SUCCESS,
                        $txn_id = $tran['id_transaction'];
                        if ($txn_id != "" && $status_code != 'PENDING' && $status_code != 'FLAGGED' && $status_code != '') {
                            $updateData = array(
                                "payu_id" => $response->referenceId, // referenceId
                                "payment_ref_number" => $response->referenceId,
                                "payment_mode" => ($response->paymentMode == "CREDIT_CARD" ? "CC" : ($response->paymentMode == "DEBIT_CARD" ? "DC" : ($response->paymentMode == "NET_BANKING" ? "NB" : $response->paymentMode))),
                                "remark" => $response->txMsg,
                                "payment_status" => ($status_code == 'SUCCESS' ? $this->payment_status['success'] : ($status_code == 'CANCELLED' ? $this->payment_status['cancel'] : ($status_code == 'FAILED' ? $this->payment_status['failure'] : ($status_code == 'REFUND' ? $this->payment_status['refund'] : $this->payment_status['pending']))))
                            );
                            $this->db->trans_begin();
                            //$result =	$this->$model->updateGatewayResponse($updateData,$txn_id);
                            $result = $this->$model->updateAtData($updateData, 'ref_trans_id', $txn_id, 'purchase_payment');
                            if ($this->db->affected_rows() == '1') {
                                //print_r($this->db->last_query());exit;
                                $vCount = $vCount + 1;
                                $this->db->trans_commit();
                            } else {
                                $this->db->trans_rollback();
                            }
                        } else {
                            //$response_msg[] = array ( 'msg' => $status_msg , 'Transaction ID' => $txn_id 	);        	
                        }
                    }
                }
            }
            if ($vCount > 0) {
                $return_data = array('status' => true, 'message' => $vCount . " payment records verified successfully.");
            } else {
                $return_data = array('status' => false, 'message' => "No records to verify");
            }
        } else {
            $return_data = array('status' => false, 'message' => "Select Payments to verify");
        }
        echo json_encode($return_data);
    }
    //AT Verify Payment
    function genInstallmentNo($id_sch_ac)
    {
        $installmentNo = $this->payment_model->genInstallmentNo($id_sch_ac);
        return $installmentNo;
    }
    //Employee , Agent Incentive    
    function insertAgentIncentive($data, $id_sch_acc, $id_payment, $id_agent)
    {
        $model = self::PAY_MODEL;
        $checkRefExist = $this->$model->checkReferalExist($id_payment, $id_sch_acc);
        if ($checkRefExist == 0) {
            $insert_array = array(
                "ly_trans_type" => 3,
                "cus_loyal_cus_id" => $data['cus_loyal_cus_id'],
                "id_agent" => $id_agent,
                "id_scheme_account" => $id_sch_acc,
                "id_payment" => $id_payment,
                "cash_point" => $data['referal_amount'],
                "status" => 1,
                "tr_cus_type" => 4,
                "cr_based_on" => 3,
                "unsettled_cash_pts" => $data['referal_amount'],
                "date_add" => date('Y-m-d H:i:s'),
                "credit_for" => $data['credit_remark']
            );
            $status = $this->$model->insert_agent_transaction($insert_array);
            $this->$model->updateAgentCash($id_agent, $data['referal_amount']);
            $ag_data = array("id_agent" => $id_agent);
            $this->$model->updData($ag_data, 'id_payment', $id_payment, 'payment');
            return $status;
        } else {
            return 0;
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
    function old_passbook($id_payment, $id_scheme_account)
    {
        $model = self::PAY_MODEL;
        $acc_model = self::ACC_MODEL;
        //$data['customer'] = $this->$acc_model->get_account_detail($id_scheme_account);
        $data['customer'] = $this->$model->get_invoiceData($id_payment, $id_scheme_account);
        //echo "<pre>";print_r($data);exit;
        $this->load->helper(array('dompdf', 'file'));
        $dompdf = new DOMPDF();
        $html = $this->load->view('scheme/print/old_passbook', $data, true);
        $dompdf->load_html($html);
        $customPaper = array(0, 0, 440, 300);
        $dompdf->set_paper($customPaper, "portriat");
        $dompdf->render();
        while(ob_get_level()) ob_end_clean();
        $dompdf->stream("Passbook.pdf", array('Attachment' => 0));
    }
    function payment_modes()
    {
        $set = self::SET_MODEL;
        $id = '';
        $data = $this->$set->paymodeDB('get', ($id != NULL ? $id : ''));
        echo json_encode($data);
    }
    /**	* 
     *	Khimji integration API call functions 
     * 	
     *	1. Generate  tranUniqueId
     * 	2. Generate AcountNo Or ReceiptNo
     * 
     */
    function generateTranUniqueId($chit, $amount)
    {
        $this->load->model("integration_model");
        $postData = array(
            "isKycValidationCheck" => false,
            "customerCode" => $chit['reference_no'],
            "transactionType" => 1,
            "schemeCode" => $chit['sync_scheme_code'],
            "amount" => $amount,
            "date" => date("Y-m-d"),
            "narration" => "Requested from admin"
        );
        $is_new_ac = ($chit['scheme_acc_number'] != "" && $chit['scheme_acc_number'] != NULL ? FALSE : TRUE);
        if ($is_new_ac) { // 1st Installment 
            $postData["action"] = 1;
            $postData["narration"] = $chit['nominee_address1'] . "," . $chit['nominee_address2'];
            /*$postData["narration2"]  =  $chit['nominee_relationship'];//"Nominee Relation"
            $postData["narration3"]  =  $chit['nominee_name']; //"Nominee Name"
            $postData["narration4"]  =  "";//Nominee DOB"
            $postData["narration5"]  =  $chit['nominee_mobile']; //"Nominee MobNo"*/
            $postData["salesmanName"] = $chit['emp_name'];
            $postData["employeeId"] = $chit['referal_code'];
            $postData["nominee"] = array(
                "nomineeName" => $chit['nominee_name'],
                "relation" => $chit['nominee_relationship'],
                "address1" => $chit['nominee_address1'],
                "address2" => $chit['nominee_address2'],
                "dateOfBirth" => NULL,
                "mobileNo" => $chit['nominee_mobile']
            );
        } else {
            $postData["action"] = 2;
            $postData["orderNo"] = $chit['scheme_acc_number'];
        }
        $response = $this->integration_model->khimji_curl('app/v1/saveSchemeOrInstallmentDetails', $postData);
        /*echo "<pre>";print_r($postData);
        echo "<pre>";print_r($response);exit;*/
        if ($response['status'] == TRUE) {
            $resData = $response['data']->data;
            if ($resData->status == TRUE && $resData->errorCode == 0) {
                $pData = array(
                    'offline_tran_uniqueid' => $resData->result->tranUniqueId,
                    'date_upd' => date("Y-m-d H:i:s")
                );
                $this->integration_model->updateData($pData, 'id_payment', $chit['id_payment'], 'payment');
                return $resData->result->tranUniqueId;
            } else if ($resData->errorCode == 1001) {
                $payData = array(
                    'offline_error_msg' => date("Y-m-d H:i:s") . " ID Gen Error : " . $resData->errorMsg,
                );
                $this->integration_model->updateData($payData, 'id_payment', $pay['id_payment'], 'payment');
            }
        }
        // Write log in case of API call failure
        if (!is_dir($this->log_dir . '/khimji')) {
            mkdir($this->log_dir . '/khimji', 0777, true);
        }
        $log_path = $this->log_dir . '/khimji/' . date("Y-m-d") . '.txt';
        $logData = "\n" . date('d-m-Y H:i:s') . "\n ADMIN : app/v1/saveSchemeOrInstallmentDetails \n Post : " . json_encode($postData, true) . "\n Response : " . json_encode($response, true);
        file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
        /*$this->db->trans_rollback();
        $this->session->set_flashdata('errMsg','Unable to proceed your payment,please try after sometime or contact customer care.');
        redirect("/paymt");*/
    }
    function genKhimjiAcNoOrReceiptNo($pay)
    {
        if (!is_dir($this->log_dir . '/khimji')) {
            mkdir($this->log_dir . '/khimji', 0777, true);
        }
        $log_path = $this->log_dir . '/khimji/' . date("Y-m-d") . '.txt';
        // $payData = $this->payment_model->getPayDataById();
        if (sizeof($pay) > 0) {
            //  echo "<pre>";print_r($pay);
            $this->load->model("integration_model");
            //foreach ($payData as $pay){
            $postData = array(
                "isKycValidationCheck" => false,
                "transactionType" => 2,
                "tranUniqueId" => $pay['offline_tran_uniqueid'],
                "branchCode" => $pay['warehouse'],
                "paymentDetail" => array(
                    "paymentType" => 1,
                    "paymentTypeName" => "Cash",
                    "amount" => $pay['payment_amount'],
                    "authorizationNo" => $pay['id_transaction'],
                    "narration" => $pay['payment_type'] . "-" . $pay['payment_mode'],
                    "originalAmt" => $pay['payment_amount'],
                    "transationAmt" => $pay['payment_amount'],
                    "marchantCharges" => 0.00,
                    "cardChargesAmount" => 0.00,
                    "cardChargesPercentage" => 0
                )
            );
            $is_new_ac = ($pay['scheme_acc_number'] != "" && $pay['scheme_acc_number'] != NULL ? FALSE : TRUE);
            if (!$is_new_ac) { // Account number already generated
                $postData["orderNo"] = $pay['scheme_acc_number'];
            }
            //echo "<p style='color:red'>STEP 2 (saveSchemeOrInstallmentDetails): \n POST DATA : </p><pre>";print_r($postData);exit;
            $response = $this->integration_model->khimji_curl('app/v1/saveSchemeOrInstallmentDetails', $postData);
            //print_r($response);exit;
            if ($response['status'] == TRUE) {
                $resData = $response['data']->data;
                if ($resData->errorCode == 0) { // $resData->status == TRUE && 
                    if (isset($resData->result->orderNo)) {
                        $acData = array(
                            'scheme_acc_number' => $resData->result->orderNo,
                            'date_upd' => date("Y-m-d H:i:s")
                        );
                        $this->integration_model->updateData($acData, 'id_scheme_account', $pay['id_scheme_account'], 'scheme_account');
                        $payData = array(
                            'receipt_no' => $resData->result->orderNo,
                            'date_upd' => date("Y-m-d H:i:s")
                        );
                        $this->integration_model->updateData($payData, 'id_payment', $pay['id_payment'], 'payment');
                        return true;
                    }
                    if (isset($resData->result->installmentNo)) {
                        $payData = array(
                            'receipt_no' => $resData->result->installmentNo,
                            'date_upd' => date("Y-m-d H:i:s")
                        );
                        $this->integration_model->updateData($payData, 'id_payment', $pay['id_payment'], 'payment');
                        return true;
                    }
                } else if ($resData->errorCode == 1001 && $resData->errorMsg == "Document Already Saved In Padm.") {
                    if (isset($resData->result->orderNo)) {
                        $payData = array(
                            'receipt_no' => $resData->result->orderNo,
                            'date_upd' => date("Y-m-d H:i:s")
                        );
                        $this->integration_model->updateData($payData, 'id_payment', $pay['id_payment'], 'payment');
                        return true;
                    }
                } else if ($resData->errorCode == 1001) {
                    $payData = array(
                        'offline_error_msg' => date("Y-m-d H:i:s") . " Acc or Receipt Error : " . $resData->errorMsg,
                    );
                    $this->integration_model->updateData($payData, 'id_payment', $pay['id_payment'], 'payment');
                }
            } else {
                $subject = "Khimji - Payment API Error";
                $message = json_encode($response, true);
                //$this->sendEmail($subject,$message);
                return false;
            }
            // Write log in case of API call failure 
            $logData = "\n" . date('d-m-Y H:i:s') . "\n ADMIN : app/v1/saveSchemeOrInstallmentDetails \n Post Data : " . json_encode($postData) . " \n Response : " . json_encode($response, true);
            file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
            /* echo "<p style='color:red'>CUSTOMER : ".$pay['mobile']." ".$pay['firstname']." | AC No. : ".$pay['scheme_acc_number'].' </p>';
             echo "<p style='color:red'>RESPONSE : <pre>";print_r($response).'  </p>';*/
            // 			}
        } else {
            // Write log in case of API call failure 
            $logData = "\n" . date('d-m-Y H:i:s') . "\n ADMIN : app/v1/saveSchemeOrInstallmentDetails - No Pending data available !!";
            file_put_contents($log_path, $logData, FILE_APPEND | LOCK_EX);
            //echo "No Pending data available !!";
            return true;
        }
    }
    function retryAccOrReceiptGen($page = 'pay_list', $id_payment)
    {
        $offline_tran_uniqueid = "";
        if ($this->config->item('integrationType') == 5) {
            $this->load->model("integration_model");
            $sql = $this->db->query("SELECT c.reference_no, nominee_name, nominee_relationship,nominee_address1,nominee_address2,nominee_mobile,
    	                                    sync_scheme_code,sa.scheme_acc_number,ep.firstname as emp_name,sa.referal_code,
    	                                    p.payment_amount,p.payment_type, p.payment_mode, sa.id_scheme_account, p.id_payment,p.id_branch,
    	                                    c.mobile,c.firstname,p.id_transaction,p.ref_trans_id, b.warehouse
    	                              FROM payment p  
    	                                LEFT JOIN scheme_account sa on sa.id_scheme_account = p.id_scheme_account
    	                                LEFT JOIN branch b ON b.id_branch = sa.id_branch
    	                                LEFT JOIN customer c on sa.id_customer = c.id_customer
    	                                LEFT JOIN scheme s on s.id_scheme = sa.id_scheme
    	                                LEFT JOIN employee e on sa.referal_code = e.emp_code
    	                              LEFT JOIN employee ep on ep.id_employee = p.id_employee
    	                              WHERE payment_status=1 and receipt_no is null and p.id_payment =" . $id_payment . " group by id_payment");
            //   echo $this->db->last_query();exit;
            $chit = $sql->row_array();
            // 	echo "<pre>";print_r($chit);exit;
            if ($sql->num_rows() == 1) {
                $postData = array(
                    "isKycValidationCheck" => false,
                    "customerCode" => $chit['reference_no'],
                    "transactionType" => 1,
                    "schemeCode" => $chit['sync_scheme_code'],
                    "amount" => $chit['payment_amount'],
                    "date" => date("Y-m-d"),
                    "narration" => "Requested from Web app"
                );
                $is_new_ac = ($chit['scheme_acc_number'] != "" && $chit['scheme_acc_number'] != NULL ? FALSE : TRUE);
                if ($is_new_ac) { // 1st Installment 
                    $postData["action"] = 1;
                    $postData["narration"] = $chit['nominee_address1'] . "," . $chit['nominee_address2'];
                    $postData["narration2"] = $chit['nominee_relationship']; //"Nominee Relation"
                    $postData["narration3"] = $chit['nominee_name']; //"Nominee Name"
                    $postData["narration4"] = ""; //Nominee DOB"
                    $postData["narration5"] = $chit['nominee_mobile']; //"Nominee MobNo"
                    $postData["salesmanName"] = $chit['emp_name'];
                    $postData["employeeId"] = $chit['referal_code'];
                    $postData["nominee"] = array(
                        "nomineeName" => $chit['nominee_name'],
                        "relation" => $chit['nominee_relationship'],
                        "address1" => $chit['nominee_address1'],
                        "address2" => $chit['nominee_address2']
                    );
                } else {
                    $postData["action"] = 2;
                    $postData["orderNo"] = $chit['scheme_acc_number'];
                }
                $response = $this->integration_model->khimji_curl('app/v1/saveSchemeOrInstallmentDetails', $postData);
                // echo "<pre>";print_r($postData);exit;
                //echo "<pre>";print_r($response);exit;
                if ($response['status'] == TRUE) {
                    $resData = $response['data']->data;
                    if ($resData->status == TRUE && $resData->errorCode == 0) {
                        $pData = array(
                            'offline_tran_uniqueid' => $resData->result->tranUniqueId,
                            'date_upd' => date("Y-m-d H:i:s")
                        );
                        $this->integration_model->updateData($pData, 'id_payment', $chit['id_payment'], 'payment');
                        $offline_tran_uniqueid = $resData->result->tranUniqueId;
                    } else if ($resData->errorCode == 1001) {
                        $payData = array(
                            'offline_error_msg' => date("Y-m-d H:i:s") . " ID Gen Error : " . $resData->errorMsg,
                        );
                        // 		echo "<pre>";print_r($payData);exit;	 
                        $this->integration_model->updateData($payData, 'id_payment', $chit['id_payment'], 'payment');
                    }
                }
            }
            if ($offline_tran_uniqueid != '' && strlen($offline_tran_uniqueid) > 0) {
                $payDt = array(
                    "id_payment" => $id_payment,
                    "id_scheme_account" => $chit['id_scheme_account'],
                    "scheme_acc_number" => $chit['scheme_acc_number'],
                    "offline_tran_uniqueid" => $offline_tran_uniqueid,
                    "warehouse" => $this->payment_model->getWarehouse($chit['id_branch']),
                    "payment_amount" => $chit['payment_amount'],
                    "id_transaction" => "ADM_REF_" . $id_payment,
                    "payment_type" => $chit['payment_type'],
                    "payment_mode" => $chit['payment_mode']
                );
                $this->genKhimjiAcNoOrReceiptNo($payDt);
                $this->session->set_flashdata('chit_alert', array('message' => 'Retry completed successfully', 'class' => 'info', 'title' => 'Scheme Payment'));
                if ($page = "pay_list") {
                    redirect("payment/list");
                }
            } else {
                $this->session->set_flashdata('chit_alert', array('message' => $offline_tran_uniqueid . 'Error in generating offline unique id, kindly check log for more details..', 'class' => 'failure', 'title' => 'Scheme Payment'));
                if ($page = "pay_list") {
                    redirect("payment/list");
                }
            }
        }
    }
    function update_dueYear($id_scheme_account)
    {
        $upd_duemonthyear = $this->payment_model->update_dueMonYear($id_scheme_account);
        return $upd_duemonthyear;
    }
    /* QR code scan option in payment form - by Clinton*/
    function ajax_account_detail_qr()
    {
        $model = self::PAY_MODEL;
        $id_scheme_account = $this->input->post('id_scheme_account');
        $data['account'] = $this->$model->get_paymentContent($id_scheme_account);
        if (empty($data['account'])) {
            $data = [];
        } else {
            $id_customer = $data['account']['id_customer'];
            $data['custmer_account'] = $this->$model->get_customer_schemes($id_customer);
            $data['wallet_balance'] = $this->$model->wallet_balance($id_customer);
        }
        echo json_encode($data);
    }
    //ends	
    function updateReceipt()
    {
        if ($this->payment_model->get_rptnosettings() == 1) {
            //$this->db->query('LOCK TABLES payment WRITE');
            //$lg_data1 = "\n CP payment table locked at --".date('d-m-Y H:i:s');
            $receipt_no = $this->generate_receipt_no($generic['id_scheme'], 7);
            $this->load->model("integration_model");
            $this->integration_model->updateData(array("receipt_no" => $receipt_no), 'id_payment', 248, 'payment');
            $lg_data1 = "\n" . " receipt no updated at --" . date('d-m-Y H:i:s');
            //	$this->db->query('UNLOCK TABLES');
            //	$lg_data1 = $lg_data1."\n"."CP payment table unlocked at --".date('d-m-Y H:i:s');
            //file_put_contents($log_path,$lg_data1,FILE_APPEND | LOCK_EX);
            echo $lg_data1;
        }
    }
    function acmeCheck()
    {
        $this->load->model("integration_model");
        $postData = array(
            "isKycValidationCheck" => false,
            "customerCode" => "178",
            "transactionType" => 1,
            "schemeCode" => "8",
            "amount" => 1000,
            "date" => "2023-09-30",
            "narration" => "bbjj,hjbjjk",
            "action" => 1,
            "salesmanName" => "Support",
            "employeeId" => "",
            "nominee" => array(
                "nomineeName" => "barath",
                "relation" => "Brother",
                "address1" => "bbjj",
                "address2" => "hjbjjk",
                "dateOfBirth" => null,
                "mobileNo" => "7778887778"
            )
        );
        $response = $this->integration_model->khimji_curl_test('app/v1/saveSchemeOrInstallmentDetails', $postData);
        //$api = 'app/v1/saveSchemeOrInstallmentDetails';
        //$url = $this->config->item('khimji-baseURL')."".$api;
        //echo "<pre>";print_r($url);
        echo "<pre>";
        print_r(json_encode($postData));
        echo "<pre>";
        print_r(json_encode($response));
        exit;
    }
    //function to get ref_number from pmd table
    function get_ref_num()
    {
        $model = self::PAY_MODEL;
        $status = $this->$model->get_ref_num();
        echo json_encode($status);
    }
    function update_remark()
    {
        $data = array(
            'remark' => $_POST['remark']
        );
        $remark_update = $this->payment_model->update_data($data, 'id_payment', $_POST['id_payment'], 'payment');
        echo json_encode($remark_update);
    }
    function get_status()
    {
        $sql = "select * from payment_status_message";
        $data = $this->db->query($sql)->result_array();
        echo json_encode($data);
    }
    public function get_scheme_cash_total()
    {
        $model = self::PAY_MODEL;
        $id_scheme_acc = $this->input->post('id_scheme_account');
        $id_branch = $this->input->post('id_branch');
        $data = $this->$model->get_scheme_cash_total($id_scheme_acc, $id_branch);
        echo json_encode($data);
    }
    // esakki 17-09
    function get_chq_num()
    {
        $model = self::PAY_MODEL;
        $status = $this->$model->get_chq_num();
        echo json_encode($status);
    }
    function getFormattedYear($dt)
    {
        $date = new DateTime($dt);
        $month = (int) $date->format('m');
        $year = (int) $date->format('y');
        // If the month is January, February, or March, subtract 1 from the year
        if ($month >= 1 && $month <= 3) {
            $year -= 1;
        }
        return str_pad($year, 2, '0', STR_PAD_LEFT); // Ensuring two-digit format
    }
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
                'mode_name' => $formatted_mode_name,
                'short_code' => $short_code,
                'status' => 1,
                'sort_order' => 0,
                'show_in_pay' => 0
            );
            $this->db->insert('payment_mode', $data);
        }
        return $short_code;
    }

}
