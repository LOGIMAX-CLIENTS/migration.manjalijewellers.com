<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Admin_reports extends CI_Controller
{
	const PAY_MODEL	= "payment_model";
	const DAS_MODEL	= "dashboard_model";
	const ACC_MODEL	= "account_model";
	const LOG_MODEL	= "log_model";
	const MAIL_MODEL = "email_model";
	const ADM_MODEL = "chitadmin_model";
	const SMS_MODEL = "admin_usersms_model";
	const SET_MODEL	= "admin_settings_model";
	const REP_VIEW	= "reports/";
	const LOG_VIEW	= "log/";
	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->helper('metal_wgt_digit');
		$this->load->model(self::PAY_MODEL);
		$this->load->model(self::LOG_MODEL);
		$this->load->model(self::ACC_MODEL);
		$this->load->model(self::DAS_MODEL);
		$this->load->model(self::MAIL_MODEL);
		$this->load->model(self::SET_MODEL);
		$this->load->model(self::ADM_MODEL);
		$this->load->model(self::SMS_MODEL);
		$this->load->model("admin_report_model");
		$this->load->model('sms_model');
		$this->load->helper('lmx/functions/export_helper');
		if (!$this->session->userdata('is_logged')) {
			redirect('admin/login');
		}
		$this->branch_settings =  $this->session->userdata('branch_settings');
	}
	//payment status 0 -> pending, 1 -> success, 2 -> rejected, -1 -> failure
	function payment_due_list()
	{
		$model =	self::PAY_MODEL;
		$data['accounts'] = $this->$model->get_payment_dues_details();
		$data['main_content'] = self::REP_VIEW . 'payment_due';
		$this->load->view('layout/template', $data);
	}
	//unpaid report starts here 
	function payment_details()
	{
		$model =	self::PAY_MODEL;
		//$data['accounts']=$this->$model->get_payment_report();			
		$data['main_content'] = self::REP_VIEW . 'payment_report';
		$this->load->view('layout/template', $data);
	}
	function ajax_customer_payment_details()
	{
		$id_scheme = $this->input->post('id_scheme');
		$model =	self::PAY_MODEL;
		$data['accounts'] = $this->$model->get_payment_report();
		echo json_encode($data);
	}
	//unpaid reports ends here
	function payment_employee_wise()
	{
		$model =	self::PAY_MODEL;
		$data['accounts'] = $this->$model->get_payment_employee();
		$data['main_content'] = self::REP_VIEW . 'employee_report';
		//echo "<pre>"; print_r($data);exit; echo "<pre>";
		$this->load->view('layout/template', $data);
	}
	function payment_employee()
	{
		$model =	self::PAY_MODEL;
		$id_branch = $this->input->post('id_branch');
		$data['employee'] = $this->$model->get_employee_name($id_branch);
		echo json_encode($data);
	}
	function ajax_payment_list()
	{
		$id_emp = $this->input->post('id_emp');
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$id_branch = $this->input->post('id_branch');
		$model =	self::PAY_MODEL;
		$data['payments'] = $this->$model->get_payment_list($from_date, $to_date, $id_branch, $id_emp); //hh
		echo json_encode($data);
	}
	function payment_schemewise()
	{
		$model =	self::PAY_MODEL;
		//$data['payments']=$this->$model->total_paid_unpaid();
		$data['main_content'] = self::REP_VIEW . 'payment_schemewise';
		$this->load->view('layout/template', $data);
	}
	function payment_schemewise_detail()
	{
		$id_branch = $this->input->post('id_branch');
		$from = date('Y-m-d', strtotime($this->input->post('from_date')));
		$to = date('Y-m-d', strtotime($this->input->post('to_date')));
		$model =	self::PAY_MODEL;
		$data['payments'] = $this->$model->total_paid_unpaid($from, $to, $id_branch);
		echo json_encode($data);
	}
	/* Coded by ARVK */
	function payment_datewise()
	{
		$model =	self::PAY_MODEL;
		$payments = $this->$model->payment_datewise(date('Y-m-d'));
		$data['payments'] = $payments['collection_report'];
		$data['opening_balance'] = ($payments['collection_total'] ? $payments['collection_total'] : 0.00);
		$data['main_content'] = self::REP_VIEW . 'payment_date_wise';
		//echo "<pre>";print_r($data);echo "</pre>";
		$this->load->view('layout/template', $data);
	}
	function payment_datewise_ajax()
	{
		$model =	self::PAY_MODEL;
		$date = date('Y-m-d', strtotime(str_replace("/", "-", $this->input->post('date'))));
		//echo "<pre>";print_r($date);echo "</pre>";
		$payments = $this->$model->payment_datewise($date);
		$data['payments'] = $payments['collection_report'];
		$data['opening_balance'] = ($payments['collection_total'] ? $payments['collection_total'] : 0.00);
		echo json_encode($data);
	}
	/* / Coded by ARVK */
	function accounts_schemewise()
	{
		$model =	self::DAS_MODEL;
		$this->load->model($model);
		//$data['accounts']=$this->$model->schemewise_accounts();			
		$data['main_content'] = self::REP_VIEW . 'accounts_schemewise';
		$this->load->view('layout/template', $data);
	}
	function accounts_schemewise_detail()
	{
		$id_branch = $this->input->post('id_branch');
		$model =	self::DAS_MODEL;
		$this->load->model($model);
		$data['accounts'] = $this->$model->schemewise_accounts($id_branch);
		echo json_encode($data);
	}
	function scheme_account_report($id_scheme_account)
	{
		$acc_model = self::ACC_MODEL;
		$set_model = self::SET_MODEL;
		$pay_model = self::PAY_MODEL;
		$this->load->model($acc_model);
		$this->load->model($pay_model);
		$account['customer'] = $this->$acc_model->get_account_detail($id_scheme_account);
		$account['payment']  = $this->$pay_model->get_account_payment($id_scheme_account);
		$access = $this->$set_model->get_access('account/close');
		$data['account'] = $account;
		// echo "<pre>"; print_r($data);exit; echo "<pre>";
		$data['main_content'] = self::REP_VIEW . 'payment_accountwise';
		$this->load->view('layout/template', $data);
	}
	function payment_by_range()
	{
		$model =	self::PAY_MODEL;
		$data['accounts'] = $this->$model->get_payment_dues_details();
		$data['main_content'] = self::REP_VIEW . 'payment_list';
		$this->load->view('layout/template', $data);
	}
	function failed_payments()
	{
		$data['main_content'] = self::REP_VIEW . 'payment_failures';
		$this->load->view('layout/template', $data);
	}
	function failed_data()
	{
		$model =	self::PAY_MODEL;
		$this->load->model($model);
		$data = $this->$model->failed_payments();
		echo json_encode($data);
	}
	function payment_date_range()
	{
		$from = date('Y-m-d', strtotime($this->input->post('from_date')));
		$to = date('Y-m-d', strtotime($this->input->post('to_date')));
		$status = $this->input->post('p_status');
		$mode = $this->input->post('p_mode');
		$model =	self::PAY_MODEL;
		$this->load->model($model);
		$data = $this->$model->paymentByDateRange($from, $to, $status, $mode);
		echo json_encode($data);
	}
	//payment modewise report
	function payment_modewise()
	{
		$model =	self::PAY_MODEL;
		$this->load->model($model);
		$data['modewise'] = $this->$model->get_payment_modewise();
		$data['main_content'] = self::REP_VIEW . 'payment_modewise';
		$this->load->view('layout/template', $data);
	}
	//log report
	function log($type = "", $id = "")
	{
		$model = self::LOG_MODEL;
		$set = self::SET_MODEL;
		switch ($type) {
			case 'List':
				$data['main_content'] = self::LOG_VIEW . 'list';
				$this->load->view('layout/template', $data);
				break;
			case 'View':
				$data['main_content'] = self::LOG_VIEW . 'view_list';
				$this->load->view('layout/template', $data);
				break;
			case 'Detail':
				if (!empty($_POST)) {
					$range['from_date']  = date('Y-m-d', strtotime($this->input->post('from_date')));
					$range['to_date']  = date('Y-m-d', strtotime($this->input->post('to_date')));
					$logs 	= $this->$model->get_log_detail_range($range['from_date'], $range['to_date']);
				} else {
					$logs 	= $this->$model->log_detail('get', '', '');
				}
				$data	=	array(
					'logs' => $logs
				);
				echo json_encode($data);
				break;
			default:
				$access = $this->$set->get_access('log/list');
				if (!empty($_POST)) {
					$range['from_date'] = $this->input->post('from_date');
					$range['to_date']   = $this->input->post('to_date');
					$logs 				= $this->$model->get_log_range($range['from_date'], $range['to_date']);
				} else {
					$logs 	= $this->$model->log('get', '', '');
				}
				$data	=	array(
					'access' => $access,
					'logs' => $logs
				);
				//	echo $this->db->last_query();
				echo json_encode($data);
				break;
		}
	}
	//  new reports   
	//   payment_by_daterange
	function payment_by_daterange()
	{
		$data['main_content'] = self::REP_VIEW . 'payment_daterange';
		$this->load->view('layout/template', $data);
	}
	function payment_list_daterange()
	{
		$model =	self::PAY_MODEL;
		$set_model = self::SET_MODEL;
		if (!empty($_POST)) {
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$range['type']  = $this->input->post('type');
			$range['limit']  = $this->input->post('limit');
			$range['id']  = $this->input->post('id');
			$range['id_employee']  = $this->input->post('id_employee');
			$range['acc']  = $this->input->post('acc');
			$payment_list = $this->$model->payment_list_daterange($range['from_date'], $range['to_date'], $range['type'], $range['limit'], $range['id'], $range['id_employee'], $range['acc']);
			$data = array();
			$i = 1;
			foreach ($payment_list as $payment) {
				$sgst = sprintf("%.3f", $payment['sgst']);
				$cgst = sprintf("%.3f", $payment['cgst']);
				$total_gst = sprintf("%.3f", $sgst + $cgst);
				if ($payment['gst_type'] == 0 && $payment['gst_setting'] == 1) {
					$pay = $payment['payment_amount'] - $total_gst;
				}
				if ($payment['discountAmt'] != 0.00) {
					$pay = $payment['payment_amount'] - $payment['discountAmt'];
				} else {
					$pay = $payment['payment_amount'];
				}
				$data['account'][] = array(
					'id_payment' 			=> (isset($payment['id_payment']) ? $payment['id_payment'] : 0),
					'sno' 	=> (isset($i) ? $i : 0),
					'account_name' 	=> (isset($payment['account_name']) ? $payment['account_name'] : 0),
					'act_amount' 	=> (isset($payment['act_amount']) ? $payment['act_amount'] : 0),
					'name' 	=> (isset($payment['name']) ? $payment['name'] : null),
					'payment_ref_number' 	=> (isset($payment['payment_ref_number']) ? $payment['payment_ref_number'] : '-'),
					'id_transaction' 	=> (isset($payment['id_transaction']) ? $payment['id_transaction'] : '-'),
					'card_no' 	=> (isset($payment['card_no']) ? $payment['card_no'] : '-'),
					'scheme_acc_number' 	=> (isset($payment['scheme_acc_number']) ? $payment['scheme_acc_number'] : 0),
					'group_code' 	=> (isset($payment['group_code']) ? $payment['group_code'] : 0),
					'has_lucky_draw' 	=> (isset($payment['has_lucky_draw']) ? $payment['has_lucky_draw'] : 0),
					'is_lucky_draw' 	=> (isset($payment['is_lucky_draw']) ? $payment['is_lucky_draw'] : 0),
					'due_type' 	=> (isset($payment['due_type']) ? $payment['due_type'] : null),
					'code' 	=> (isset($payment['code']) ? $payment['code'] : 0),
					'scheme_type' 	=> (isset($payment['scheme_type']) ? $payment['scheme_type'] : null),
					'payment_amount' => (($payment['gst_type'] == 0 && $payment['gst_setting'] == 1) ? $pay : $pay),
					'discountAmt' =>    $payment['discountAmt'],
					'amount' => (isset($payment['amount']) ? $payment['amount'] : 0),
					'incentive' 	=> (isset($payment['incentive']) ? $payment['incentive'] : '-'),
					'metal_rate' 	=> (isset($payment['metal_rate']) ? $payment['metal_rate'] : 0),
					'metal_weight' 	=> (isset($payment['metal_weight']) ? $payment['metal_weight'] : '-'),
					'date_payment' 	=> (isset($payment['date_payment']) ? $payment['date_payment'] : 0),
					'emp_code' 	=> (isset($payment['emp_code']) ? $payment['emp_code'] : 0),
					'payment_type' 	=> (isset($payment['payment_type']) ? $payment['payment_type'] : 0),
					'paid_installments' => (isset($payment['paid_installments']) ? $payment['paid_installments'] : 0),
					'gst_type'      => (isset($payment['gst_type']) ? $payment['gst_type'] : 0),
					'gst' 	        => (isset($payment['gst']) ? $payment['gst'] : 0),
					'gst_setting' 	=> (isset($payment['gst_setting']) ? $payment['gst_setting'] : 0),
					'payment_mode' 	=> (isset($payment['payment_mode']) ? $payment['payment_mode'] : null),
					'bank_name' 	=> (isset($payment['bank_name']) ? $payment['bank_name'] : 0),
					'receipt_no' 	=> (isset($payment['receipt_no']) ? $payment['receipt_no'] : null),
					'payment_status' => (isset($payment['payment_status']) ? $payment['payment_status'] : 0),
					'id_status' 	=> (isset($payment['id_status']) ? $payment['id_status'] : 0),
					'status_color' 	=> (isset($payment['status_color']) ? $payment['status_color'] : 0),
					'sgst' 	=> (isset($sgst) ? $sgst : 0),
					'cgst' 	=> (isset($cgst) ? $cgst : 0),
					'total_gst' => (isset($total_gst) ? $total_gst : 0)
				);
				$i++;
			}
		}
		if (count($data) > 0) {
			$data['gst_number'] = $payment_list[0]['gst_number'];
			echo json_encode($data);
		} else {
			echo json_encode($data);
		}
	}
	// paymode_wise_list
	function payment_modewise_data()
	{
		$data['main_content'] = self::REP_VIEW . 'payment_modewise_list';
		$this->load->view('layout/template', $data);
	}
	function payment_modewise_list()
	{
		$model =	self::PAY_MODEL;
		$this->load->model($model);
		if (!empty($_POST)) {
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$range['type']  = $this->input->post('type');
			$range['limit']  = $this->input->post('limit');
			$range['id']  = $this->input->post('id');
			$paymodewise = $this->$model->get_modewise_list($range['from_date'], $range['to_date'], $range['type'], $range['limit'], $range['id']);
			$data = array();
			$i = 1;
			foreach ($paymodewise as $payment) {
				$sgst = sprintf("%.3f", $payment['sgst']);
				$cgst = sprintf("%.3f", $payment['cgst']);
				$total_gst = sprintf("%.3f", $sgst + $cgst);
				if ($payment['gst_type'] == 0 && $payment['gst_setting'] == 1) {
					$pay = $payment['payment_amount'] - $total_gst;
				}
				$data['account'][] = array(
					'sno' 			 => $i,
					'payment_amount' => (($payment['gst_type'] == 0 && $payment['gst_setting'] == 1) ? $pay : $payment['payment_amount']),
					'mode_name' 	 => (isset($payment['mode_name']) ? $payment['mode_name'] : null),
					'gst_setting' 	 => (isset($payment['gst_setting']) ? $payment['gst_setting'] : null),
					'sgst' 	         => (isset($sgst) ? $sgst : 0),
					'cgst' 			 => (isset($cgst) ? $cgst : 0),
					'total_gst' 	 => (isset($total_gst) ? $total_gst : 0),
				);
				$i++;
			}
		}
		if (count($data) > 0) {
			$data['gst_number'] = $paymodewise[0]['gst_number'];
			echo json_encode($data);
		} else {
			echo json_encode($data);
		}
	}
	// payment_datewise_schemedata
	function payment_datewise_data()
	{
		$data['main_content'] = self::REP_VIEW . 'paymentschem_datewise';
		$this->load->view('layout/template', $data);
	}
	function payment_datewise_list()
	{
		$model =	self::PAY_MODEL;
		$date = date('Y-m-d', strtotime(str_replace("/", "-", $this->input->post('date'))));
		$paydatewise = $this->$model->payment_datewise_list($date);
		$data = array();
		foreach ($paydatewise as $payment) {
			$sgst  = sprintf("%.3f", $payment['sgst']);
			$cgst  = sprintf("%.3f", $payment['cgst']);
			$total_gst = sprintf("%.3f", $sgst + $cgst);
			if ($payment['gst_type'] == 0 && $payment['gst_setting'] == 1) {
				$pay = $payment['payment_amount'] - $total_gst;
			}
			$data['account'][] = array(
				'date_payment' 	=> (isset($payment['date_payment']) ? $payment['date_payment'] : 0),
				'code' 			=> (isset($payment['code']) ? $payment['code'] : 0),
				'payment_mode' 	 => (isset($payment['payment_mode']) ? $payment['payment_mode'] : null),
				'branch_name' 	 => (isset($payment['name']) ? $payment['name'] : null),
				'id_transaction' 	 => (isset($payment['id_transaction']) ? $payment['id_transaction'] : null),
				'receipt' 	=> (isset($payment['receipt']) ? $payment['receipt'] : 0),
				'payment_amount' => (($payment['gst_type'] == 0 && $payment['gst_setting'] == 1) ? $pay : $payment['payment_amount']),
				'gst_setting' 	=> (isset($payment['gst_setting']) ? $payment['gst_setting'] : null),
				'sgst' 	         => (isset($sgst) ? $sgst : 0),
				'cgst' 			 => (isset($cgst) ? $cgst : 0),
				'total_gst' 	 => (isset($total_gst) ? $total_gst : 0)
			);
		}
		//echo "<pre>";print_r($data);echo "</pre>";exit;
		$data['mode_wise'] = $this->$model->payment_datewise_by_mode($date);
		if (count($data) > 0) {
			$data['gst_number'] = $paydatewise[0]['gst_number'];
			echo json_encode($data);
		} else {
			echo json_encode($data);
		}
	}
	//   paydatewise_schcoll_data
	function paydatewise_schemecoll_data()
	{
		$data['main_content'] = self::REP_VIEW . 'payment_datewise_schcoll';
		$this->load->view('layout/template', $data);
	}
	function paydatewise_schemecoll_list()
	{
		$model =	self::PAY_MODEL;
		$date = date('Y-m-d', strtotime(str_replace("/", "-", $this->input->post('date'))));
		$items = $this->$model->paydatewise_schemecoll($date);
		//echo "<pre>";print_r($items);echo "</pre>";exit;		
		$data = array();
		foreach ($items as $payment) {
			$data['account'][] = array(
				'scheme_name' => ($payment['scheme_name'] ? $payment['scheme_name'] : 0.00),
				'branch_name' => ($payment['branch'] ? $payment['branch'] : "-"),
				'group_code' => ($payment['group_code'] ? $payment['group_code'] : 0.00),
				'has_lucky_draw' => ($payment['has_lucky_draw'] ? $payment['has_lucky_draw'] : 0.00),
				'is_lucky_draw' => ($payment['is_lucky_draw'] ? $payment['is_lucky_draw'] : 0.00),
				'opening_bal' => ($payment['opening_bal'] ? $payment['opening_bal'] : 0.00),
				'collection' => ($payment['collection'] ? $payment['collection'] : 0.00),
				'incentive' => ($payment['incentive'] ? $payment['incentive'] : 0.00),
				'paid' => ($payment['paid'] ? $payment['paid'] : 0),
				'cancel_payment' => ($payment['cancel_payment'] ? $payment['cancel_payment'] : 0),
				'charge' => ($payment['charge'] ? $payment['charge'] : 0.00),
				'closing_balance' => ($payment['closing_balance'] ? $payment['closing_balance'] : 0.00),
				'gst_setting' => ($payment['gst_setting'] ? $payment['gst_setting'] : 0.00)
			);
		}
		//echo "<pre>";print_r($data);echo "</pre>";exit;			
		if (count($data) > 0) {
			$data['gst_number'] = $items[0]['gst_number'];
			echo json_encode($data);
		} else {
			echo json_encode($data);
		}
	}
	// payment outstanding 
	function payment_outstanding()
	{
		$data['main_content'] = self::REP_VIEW . 'payment_outstanding';
		$this->load->view('layout/template', $data);
	}
	function payment_outstanding_list()
	{
		$model =	self::PAY_MODEL;
		$set_model = self::SET_MODEL;
		if (!empty($_POST)) {
			$date = date('Y-m-d', strtotime(str_replace("/", "-", $this->input->post('date'))));
			$payment_list = $this->$model->payment_outlist($date);
			//echo "<pre>";print_r($payment_list);echo "</pre>";exit;
			$data = array();
			$i = 1;
			foreach ($payment_list as $payment) {
				$due_count = $payment['total_installments'] - $payment['paid_installments'];
				$data['account'][] = array(
					'sno' 	=> (isset($i) ? $i : 0),
					'code' 	=> (isset($payment['code']) ? $payment['code'] : 0),
					'group_code' 	=> (isset($payment['group_code']) ? $payment['group_code'] : 0),
					'is_lucky_draw' 	=> (isset($payment['is_lucky_draw']) ? $payment['is_lucky_draw'] : 0),
					'has_lucky_draw' 	=> (isset($payment['has_lucky_draw']) ? $payment['has_lucky_draw'] : 0),
					'scheme_acc_number' 	=> (isset($payment['scheme_acc_number']) ? $payment['scheme_acc_number'] : 0),
					'name' 	=> (isset($payment['name']) ? $payment['name'] : null),
					'total_installments' => (isset($payment['total_installments']) ? $payment['total_installments'] : null),
					'paid_installments' => (isset($payment['paid_installments']) ? $payment['paid_installments'] : null),
					'total_paid_amount' => (isset($payment['total_paid_amount']) ? $payment['total_paid_amount'] : 0.00),
					'total_paid_weight' => (isset($payment['total_paid_weight']) ? $payment['total_paid_weight'] : 0.00),
					'amount' 	     => (isset($payment['amount']) ? $payment['amount'] : 0),
					'joined_date' => (isset($payment['joined_date']) ? $payment['joined_date'] : null),
					'due_count' => (isset($due_count) ? $due_count : 0),
					'mobile' 	=> (isset($payment['mobile']) ? $payment['mobile'] : 0),
					'last_paid_date' 	=> (isset($payment['last_paid_date']) ? $payment['last_paid_date'] : 0),
					'gst_setting' 	=> (isset($payment['gst_setting']) ? $payment['gst_setting'] : 0)
				);
				$i++;
			}
		}
		//echo "<pre>";print_r($data);echo "</pre>";exit;
		if (count($data) > 0) {
			$data['gst_number'] = $payment_list[0]['gst_number'];
			echo json_encode($data);
		} else {
			echo json_encode($data);
		}
	}
	//refferal report starts here
	function employee_ref_success()
	{
		$model =	self::PAY_MODEL;
		$data['main_content'] = self::REP_VIEW . 'employee_ref_success';
		$this->load->view('layout/template', $data);
	}
	function employee_ref_success_list($id = "")
	{
		$model =	self::PAY_MODEL;
		if (!empty($_POST)) {
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$data['accounts'] = $this->$model->get_empreff_report_by_range($range['from_date'], $range['to_date'], '');
		} else {
			$data['accounts'] = $this->$model->get_empreff_report();
		}
		echo json_encode($data);
	}
	function emp_referral_account($referal_code)
	{
		$acc_model = self::ACC_MODEL;
		$pay_model = self::PAY_MODEL;
		$data['accounts']  = $this->$pay_model->empreferral_account($referal_code);
		$data['main_content'] = self::REP_VIEW . 'refferal_report';
		$this->load->view('layout/template', $data);
	}
	function get_referral_code_byId()
	{
		$pay_model = self::PAY_MODEL;
		$id = $_POST['emp_code'];
		//print_r($_POST);
		if (!empty($_POST['from_date'] && !empty($_POST['to_date']))) {
			//print_r($_POST);
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$from_date = $range['from_date'];
			$to_date = $range['to_date'];
			$data['accounts'] = $this->$pay_model->empreferral_account_by_range($range['from_date'], $range['to_date'], $id);
		} else {
			//print_r($_POST);
			$data['accounts']  = $this->$pay_model->empreferral_account($id);
		}
		echo json_encode($data);
	}
	//refferal report ends here
	//cus_reff_begin
	function cus_ref_success()
	{
		$model =	self::PAY_MODEL;
		$data['main_content'] = self::REP_VIEW . 'cus_reff_report';
		$this->load->view('layout/template', $data);
	}
	function cus_ref_success_list()
	{
		$model =	self::PAY_MODEL;
		if (!empty($_POST)) {
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$data['accounts'] = $this->$model->get_cusreff_report_by_range($range['from_date'], $range['to_date']);
		} else {
			$data['accounts'] = $this->$model->get_cus_ref_success();
		}
		echo json_encode($data);
	}
	function cus_refferl_account($mobile)
	{
		$acc_model = self::ACC_MODEL;
		$pay_model = self::PAY_MODEL;
		$data['accounts']  = $this->$pay_model->cus_refferl_account($mobile);
		$data['main_content'] = self::REP_VIEW . 'cus_refferal_report_rec';
		$this->load->view('layout/template', $data);
	}
	function getscheme_name()
	{
		$model =	self::PAY_MODEL;
		$data = $this->$model->get_scheme_list();
		echo json_encode($data);
	}
	// Employee Referral report
	function get_employee_details()
	{
		$model =	self::PAY_MODEL;
		$ids = $this->input->post('emp');
		$refrecord = array();
		if (!empty($ids) && count($ids) > 0 && $ids != NULL) {
			foreach ($ids as $id_employee) {
				$refrecord[] = $this->$model->get_empreport($id_employee);
			}
			$data['records'] = $refrecord;
			$this->load->helper(array('dompdf', 'file'));
			$dompdf = new DOMPDF();
			$html = $this->load->view('include/report_referral', $data, true);
			$dompdf->load_html($html);
			$dompdf->set_paper("a4", "portriat");
			$dompdf->render();
			while(ob_get_level()) ob_end_clean();
			$dompdf->stream("referral.pdf", array('Attachment' => 0));
		} else {
			$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation...', 'class' => 'danger', 'title' => 'Scheme account number generate'));
		}
	}
	function customer_enquiry()
	{
		$data['main_content'] = self::REP_VIEW . 'customer_enquiry';
		$this->load->view('layout/template', $data);
	}
	function ajax_enquiry_list()
	{
		$set = self::SET_MODEL;
		if (!empty($_POST)) {
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$range['status']  = $this->input->post('status');
			$range['type']  = $this->input->post('type');
			$data['enquiry'] = $this->admin_report_model->get_customerenquiry_by_date($range['from_date'], $range['to_date'], $range['status'], $range['type']);
		} else {
			$data['enquiry'] = $this->admin_report_model->get_customerenquiry();
		}
		$data['query'] = $this->db->last_query();
		$data['access'] = $this->$set->get_access('reports/customer_enquiry');
		echo json_encode($data);
	}
	function enquiry($type = "", $id = "", $status = "")
	{
		switch ($type) {
			case 'UpdateStatus':
				$data = $this->admin_report_model->update_enqStatus($_POST);
				echo json_encode($data);
				break;
			case 'View':
				$data = $this->admin_report_model->get_custEnqStatus($id);
				echo json_encode($data);
				break;
			default:
				break;
		}
	}
	function interWalletTrans_list()
	{
		$data['main_content'] = self::REP_VIEW . 'interWalletTrans';
		$this->load->view('layout/template', $data);
	}
	/*public function ajax_interWallet_trans()
	{
		$model_name = self::SET_MODEL; 
		if(!empty($_POST))
		{
			$data['trans'] = $this->$model_name->get_interWallet_trans_by_Filter($this->input->post('from_date'),$this->input->post('to_date'),$this->input->post('searchTerm'),$this->input->post('filterBy'));
		}
		else
		{
			$data['trans'] = $this->$model_name->get_interWallet_trans(); 
		}		
		echo json_encode($data);
	}*/
	public function ajax_interWallet_trans()
	{
		$model_name = self::SET_MODEL;
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$searchTerm = $this->input->post('searchTerm');
		$filterBy = $this->input->post('filterBy');
		$id_branch = $this->input->post('id_branch');
		if ($from_date != '' || $to_date != '' || $searchTerm != '' || $filterBy != '' || $id_branch != '') {
			$data['trans'] = $this->$model_name->get_interWallet_trans_by_Filter($from_date, $to_date, $searchTerm, $filterBy, $id_branch);
		} else {
			$id_branch = $this->input->post('id_branch');
			$data['trans'] = $this->$model_name->get_interWallet_trans($id_branch);
		}
		echo json_encode($data);
	}
	function cancel_payment()
	{
		$pay_model = self::PAY_MODEL;
		$txns = $_POST;
		for ($i = 0; $i < sizeof($txns['id_payment']); $i++) {
			$id_payment = $txns['id_payment'][$i];
			$pay_status_array = array(
				'payment_status'	=>  4
			);
			$status  = $this->$pay_model->payment_cancel('update', $id_payment, $pay_status_array);
			$payment  = $this->$pay_model->paymentDB('get', $id_payment);
			if ($status) {
				if ($pay_status_array['payment_status'] == 4) { // Update payment as Canceled in transacition table
					if ($this->config->item('integrationType') == 2) {
						$pay_status_array['id_payment'] = $status['updateID'];
						$this->load->model('syncapi_model');
						$this->syncapi_model->updPayStatusInTrans($pay_status_array);
					}
				}
				$pay_status_array = array(
					'id_payment'	=> (isset($status['updateID']) ? $status['updateID'] : NULL),
					'id_status_msg' 	=> 4,
					'charges' 			=> (isset($payment['payment_amount']) ? $payment['payment_amount'] : NULL),
					'id_employee' 		=>  $this->session->userdata('uid'),
					'date_upd'			=>  date('Y-m-d H:i:s')
				);
				$status  = $this->$pay_model->payment_statusDB('insert', $id_payment, $pay_status_array);
			}
		}
		echo TRUE;
	}
	public function employee_account()
	{
		$model =	self::ACC_MODEL;
		$data['accounts'] = $this->$model->get_all_account();
		$data['main_content'] = self::REP_VIEW . 'employee_account_wise';
		//echo "<pre>"; print_r($data);exit;
		$this->load->view('layout/template', $data);
	}
	public function ajax_get_emp_account_list($id = "")
	{
		$set_model = self::SET_MODEL;
		$model =	self::PAY_MODEL;
		if (!empty($_POST)) {
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$range['id_branch']  = $this->input->post('id_branch');
			$range['id_employee']  = $this->input->post('id_employee');
			$items = $this->$model->get_all_emp_account_by_range($range['from_date'], $range['to_date'], $range['id_branch'], $range['id_employee'], '', '');
		}
		echo json_encode($items);
	}
	//end of new reports
	//Employee wise payment summary
	function employee_wise_summary()
	{
		$model =	self::PAY_MODEL;
		$data['main_content'] = self::REP_VIEW . 'employee_summary';
		//echo "<pre>"; print_r($data);exit; echo "<pre>";
		$this->load->view('layout/template', $data);
	}
	function employee_collection()
	{
		$model =	self::PAY_MODEL;
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$id_branch = $this->input->post('id_branch');
		$id_emp = $this->input->post('id_emp');
		$data['payments'] = $this->$model->payment_employee_summary($from_date, $to_date, $id_branch, $id_emp);
		echo json_encode($data);
		//  echo"<pre>";	print_r($data);exit;
	}
	//Employee wise payment summary
	//mob no,ref no,clientid,sch A/c no wise filter & change options in inter table Data's // 
	// Customer Reg& transaction records  // HH		
	public function inter_table()
	{
		$model =	self::PAY_MODEL;
		$data['main_content'] = self::REP_VIEW . 'inter_table_rep/inter_table';
		//print_r($data);exit;
		$this->load->view('layout/template', $data);
	}
	function intertable_list()
	{
		$model =	self::PAY_MODEL;
		$range['cus']  = $this->input->post('cus');
		$mobile = $_POST['mobile'];
		$clientid = $_POST['clientid'];
		$ref_no = $_POST['ref_no'];
		$group_code = $_POST['group_code'];
		// $scheme_ac_no=$_POST['scheme_ac_no'];
		$data = $this->$model->get_intertable_list($mobile, $clientid, $ref_no, $group_code, $range['cus']);
		echo json_encode($data);
	}
	function intertable_translist()
	{
		$model =	self::PAY_MODEL;
		$range['cus']  = $this->input->post('cus');
		$client_id = $_POST['client_id'];
		$ref_no = $_POST['ref_no'];
		$data = $this->$model->get_intertable_translist($client_id, $ref_no, $range['cus']);
		//echo"<pre>";	print_r($data);exit;
		echo json_encode($data);
	}
	function update_cusdatas()
	{
		$model = self::PAY_MODEL;
		$postData = $_POST['postData'];
		$res = 0;
		foreach ($postData as $data) {
			//$result = $this->$model->update_cusdata($data['id_customer_reg'],$data['mobile'],$data['scheme_ac_no'],$data['group_code']);
			$result = $this->$model->update_cusdata($data['id_customer_reg'], $data['mobile'], $data['scheme_ac_no'], $data['group_code'], $data['is_transferred']);
			$this->session->set_flashdata('chit_alert', array('message' => count($res) . ' Customer Datas is updated successfully...', 'class' => 'success', 'title' => 'Customer Reg'));
			if ($result == TRUE) {
				$res = $res + 1;
			} else {
				$this->session->set_flashdata('chit_alert', array('message' => 'Not Updated Check Your Mobile Number...', 'class' => 'danger', 'title' => 'Customer Reg'));
			}
		}
		echo json_encode($res);
	}
	//created by durga 28/12/2022 starts here
	function update_transdatas()
	{
		//print_r($_POST['postData']);exit;
		$model = self::PAY_MODEL;
		$postData = $_POST['postData'];
		//print_r($postData);exit;
		$res = 0;
		foreach ($postData as $data) {
			$result = $this->$model->update_transdata($data['id_transaction'], $data['is_transferred']);
			$this->session->set_flashdata('chit_alert', array('message' => count($res) . ' Transaction Data  updated successfully...', 'class' => 'success', 'title' => 'Transaction'));
			if ($result == TRUE) {
				$res = $res + 1;
			} else {
				$this->session->set_flashdata('chit_alert', array('message' => 'Not Updated Check Your Data...', 'class' => 'danger', 'title' => 'Customer Reg'));
			}
		}
		echo json_encode($res);
	}
	//created by durga 28/12/2022 ends here
	//mob no,ref no,clientid,sch A/c no wise filter & change options in inter table Data's // 
	// Customer Reg& transaction records  // HH	
	/** msg 91 report functions
	 * Reference : https://docs.msg91.com/collection/msg91-api-integration/5/pages/139
	 * 1 - Promotional route , 4 - transactional route
	 */
	function msg91_log()
	{
		$data['main_content'] = self::REP_VIEW . 'msg91_purchase_report';
		$this->load->view('layout/template', $data);
	}
	function getCreditHistory()
	{
		$authkey = $this->admin_report_model->getmsg91AuthKey();
		if ($authkey != NULL) {
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://control.msg91.com/api/credit_history.php?authkey=" . $authkey,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => 0,
			));
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			if ($err) {
				echo "cURL Error #:" . $err;
			} else {
				echo $response;
			}
		}
	}
	function checkBalance($type)
	{
		$authkey = $this->admin_report_model->getmsg91AuthKey();
		//$type = 1;
		if ($authkey != NULL) {
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://control.msg91.com/api/balance.php?authkey=" . $authkey . "&type=" . $type,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => 0,
			));
			$response = curl_exec($curl);
			$err = curl_error($curl);
			curl_close($curl);
			if ($err) {
				echo "cURL Error #:" . $err;
			} else {
				echo $response;
			}
		}
	}
	function msg91_delivReport($type = "")
	{
		switch ($type) {
			case 'List':
				$data['main_content'] = self::REP_VIEW . 'msg91_delivery_report';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax_report':
				$resut = $this->admin_report_model->getmsg91DelivryStat($_POST['from_date'], $_POST['to_date']);
				echo json_encode($resut);
				break;
		}
	}
	//end of msg91 reports
	//Kyc Approval Data status filter with date picker//hh
	/*	function kycdata_list() 
	{
	   $data['main_content'] = self::REP_VIEW.'kyc_table_data/kyc_data';
        $this->load->view('layout/template', $data); 
	}	
       function kycapproval_data(){
            $model=	self::PAY_MODEL;
            $setmodel=self::SET_MODEL;
		if(!empty($_POST)){
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$range['status']  = $this->input->post('status');
			$range['type']  = $this->input->post('type');
            if($range['from_date']!=""){
           	    $data=$this->$model->get_kycdata_range($range['from_date'],$range['to_date'],$range['status'],$range['type']);
            }else{
    			$data=$this->$model->get_kycdata($range['status'],$range['type']);
    			echo json_encode($data);
            }
		}
    }*/
	/*function kycdata_list()
	{
		$data['main_content'] = self::REP_VIEW . 'kyc_table_data/kyc_data';
		$this->load->view('layout/template', $data);
	}
	function kycapproval_data()
	{
		$model =	self::PAY_MODEL;
		$setmodel = self::SET_MODEL;
		if (!empty($_POST)) {
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$range['status']  = $this->input->post('status');
			$range['type']  = $this->input->post('type');
			$range['list_type']  = $this->input->post('list_type');
			//if($range['from_date']!=""){
			$data = $this->$model->get_kycdata_range($range['from_date'], $range['to_date'], $range['status'], $range['type'], $range['list_type']);
			echo json_encode($data);
			
		}
	}
	function update_kyc()
	{
		$model = self::PAY_MODEL;
		$kycdata   = $this->input->post('kyc_data');
		$kyc_type = $this->input->post('kyc_type');
		$res = 0;
		foreach ($kycdata as $data) {
			$employee = $this->session->userdata('uid');
			$updatedata = array(
				"status"          => $data['status'],
				"emp_verified_by" => $employee,
				"last_update"     => date('Y-m-d H:i:s'),
			);
			if ($kyc_type == 1) {
				$result = $this->$model->updatekyc($updatedata, $data['id_kyc'], $data['cus']);
				if ($result['verified_kycs'] == 1) {
					$update = array("kyc_status"      => 1);
					$result = $this->$model->updatekyccus($update, $data['cus']);
				}
				if ($result == TRUE) {
					$res = $res + 1;
				}
			} else if ($kyc_type == 2) {
				$result = $this->$model->updateAgentkyc($updatedata, $data['id_kyc'], $data['cus']);
				if ($result['verified_kycs'] == 1) {
					$update = array("kyc_status" => 1,);
					$result = $this->$model->updatekycAgentStatus($update, $data['cus']);
				}
				if ($result == TRUE) {
					$res = $res + 1;
				}
			}
		}
		if ($kycdata[0]['status'] == 3) {
			$this->session->set_flashdata('chit_alert', array('message' => count($res) . ' KYC records Rejected...', 'class' => 'danger', 'title' => 'Kyc Data'));
		} else if ($kycdata[0]['status'] == 2) {
			$this->session->set_flashdata('chit_alert', array('message' => count($res) . ' KYC records Verified successfully...', 'class' => 'warning', 'title' => 'Kyc Data'));
		} else {
			$this->session->set_flashdata('chit_alert', array('message' => count($res) . 'KYC records updated successfully...', 'class' => 'success', 'title' => 'Kyc Data'));
		}
		echo json_encode($res);
	}
	function getkycdata_byid()
	{
		$model = self::PAY_MODEL;
		$data = $this->$model->getkycdata_byid();
		echo json_encode($data);
	}*/
	//Kyc Approval Data status filter with date picker//hh
	//Plan 2 and Plan 3 Scheme Enquiry Data with date picker//hh
	function sch_enquirt_list()
	{
		$data['main_content'] = self::REP_VIEW . 'sch_enquiry_list/sch_enquiry';
		$this->load->view('layout/template', $data);
	}
	function schenquiry_list()
	{
		$model =	self::PAY_MODEL;
		if (!empty($_POST)) {
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$data = $this->$model->get_sch_enq_list_by_date($range['from_date'], $range['to_date']);
		} else {
			$data = $this->$model->get_sch_enq_list();
		}
		//print_r($data);exit;
		echo json_encode($data);
	}
	//Plan 2 and Plan 3 Scheme Enquiry Data with date picker// 
	//Purchase Payment - Akshaya Thiruthiyai Spl updt//HH
	function get_purchase_payment()
	{
		$data['main_content'] = self::REP_VIEW . 'purchase_history';
		$this->load->view('layout/template', $data);
	}
	public function ajax_get_customers_list()
	{
		$mobile = $this->input->post('mobile');
		$model_name = self::PAY_MODEL;
		$cus_data = $this->$model_name->ajax_get_customers_list($mobile);
		echo json_encode($cus_data);
	}
	function ajax_get_purchase_payment()
	{
		$model =	self::PAY_MODEL;
		$from_date  = $this->input->post('from_date');
		$to_date     = $this->input->post('to_date');
		$id_purch_customer     = $this->input->post('id_purch_customer');
		$mobile     = $this->input->post('mobile');
		$data = $this->$model->ajax_get_purchase_payment($from_date, $to_date, $id_purch_customer, $mobile);
		echo json_encode($data);
	}
	function generateotp()
	{
		$model 	  =	self::PAY_MODEL;
		$account  = self::ACC_MODEL;
		$this->comp = $this->$account->company_details();
		$mobile     = $this->input->post('mobile');
		$id_purch_customer     = $this->input->post('id_purch_customer');
		$chkmobno   = $this->$model->get_purchasecustomer($mobile);
		$mobile		= $chkmobno['mobile'];
		$firstname	= $chkmobno['firstname'];
		$OTP = mt_rand(100000, 999999);
		$this->session->set_userdata('OTP', $OTP);
		$message = "Hi " . $firstname . ", OTP for Akshaya Tritiya booking closure is :  " . $OTP . "  from " . $this->comp['company_name'] . "";
		if ($this->config->item('sms_gateway') == '1') {
			$sms_data = $this->sms_model->sendSMS_MSG91($mobile, $message, '', '');
		} elseif ($this->config->item('sms_gateway') == '2') {
			$sms_data = $this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
		} elseif ($this->config->item('sms_gateway') == '3') {
			$sms_data = $this->sms_model->sendSMS_SpearUC($mobile, $message, '', '');
		}
		$chkmobno = array('result' => 3, 'msg' => '"OTP Sent Successfully', 'otp' => $OTP);
		echo json_encode($chkmobno);
	}
	function verify_otp()
	{
		if ($this->session->userdata('OTP') == $this->input->post('otp')) {
			$data = array('result' => 1, 'msg' => 'OTP Verified successfully');
		} else {
			$data = array('result' => 6, 'msg' => 'Invalid OTP');
		}
		echo json_encode($data);
	}
	public function purch_delivered()
	{
		$model =	self::PAY_MODEL;
		$id_purch_payment     = $this->input->post('id_purch_payment');
		$value = $this->$model->get_purchase_pay($id_purch_payment);
		$id_purch_payment = $value['id_purch_payment'];
		if ($this->session->userdata('OTP') == $this->input->post('otp')) {
			$this->session->unset_userdata('OTP');
			$insArr = array(
				"delivery_remark"    => $this->input->post('delivery_remark'),
				"is_delivered"	     => 1,
				'delivery_verif_otp' => $_POST['otp']
			);
			$status = $this->$model->add_remark($insArr, $value['id_purch_payment']);
		}
	}
	//otp when purchase the jewel for AT special//
	//Purchase Payment - Akshaya Thiruthiyai Spl updt//
	// Payment Online/offline collection // HH
	function payments_on_off_collection_data()
	{
		$data['main_content'] = self::REP_VIEW . 'payment_off_on_collection';
		$this->load->view('layout/template', $data);
	}
	function payments_on_off_collection_list()
	{
		$model =	self::PAY_MODEL;
		$date = date('Y-m-d', strtotime(str_replace("/", "-", $this->input->post('date'))));
		$paydatewise = $this->$model->payments_on_off_collection_list($date);
		$data = array();
		foreach ($paydatewise as $payment) {
			$sgst  = sprintf("%.3f", $payment['sgst']);
			$cgst  = sprintf("%.3f", $payment['cgst']);
			$total_gst = sprintf("%.3f", $sgst + $cgst);
			if ($payment['gst_type'] == 0 && $payment['gst_setting'] == 1) {
				$pay = $payment['payment_amount'] - $total_gst;
			}
			$data['account'][] = array(
				'date_payment' 	=> (isset($payment['date_payment']) ? $payment['date_payment'] : 0),
				'code' 			=> (isset($payment['code']) ? $payment['code'] : 0),
				'payment_mode' 	 => (isset($payment['payment_mode']) ? $payment['payment_mode'] : null),
				'receipt' 	=> (isset($payment['receipt']) ? $payment['receipt'] : 0),
				'payment_amount' => (($payment['gst_type'] == 0 && $payment['gst_setting'] == 1) ? $pay : $payment['payment_amount']),
				'payment_type' 	 => (isset($payment['payment_type']) ? $payment['payment_type'] : null),
				'gst_setting' 	=> (isset($payment['gst_setting']) ? $payment['gst_setting'] : null),
				'sgst' 	         => (isset($sgst) ? $sgst : 0),
				'cgst' 			 => (isset($cgst) ? $cgst : 0),
				'total_gst' 	 => (isset($total_gst) ? $total_gst : 0)
			);
		}
		//echo "<pre>";print_r($data);echo "</pre>";exit;		
		if (count($data) > 0) {
			$data['gst_number'] = $paydatewise[0]['gst_number'];
			echo json_encode($data);
		} else {
			echo json_encode($data);
		}
	}
	// Payment Online/offline collection //
	//Autodebit subscription Status Report//HH
	function get_autodebit_subscription()
	{
		$data['main_content'] = self::REP_VIEW . 'autodebit_subscription_report';
		$this->load->view('layout/template', $data);
	}
	public function ajax_get_customers_lists()
	{
		$mobile = $this->input->post('mobile');
		$model_name = self::PAY_MODEL;
		$cus_data = $this->$model_name->ajax_get_customers_lists($mobile);
		echo json_encode($cus_data);
	}
	function ajax_get_autodebit_subscription()
	{
		$this->load->model('autodebit_model');
		$from_date  = $this->input->post('from_date');
		$to_date     = $this->input->post('to_date');
		$id_customer    = $this->input->post('id_customer');
		$mobile     = $this->input->post('mobile');
		$data = $this->autodebit_model->ajax_get_autodebit_subscription($from_date, $to_date, $id_customer, $mobile);
		echo json_encode($data);
	}
	//Autodebit subscription Status Report//
	//Get Branch wise emp name in Scheme Join Page admin //HH
	function branchwise_employee()
	{
		$model =	self::PAY_MODEL;
		$set_model = self::SET_MODEL;
		$data['profile'] = $this->session->userdata('profile');
		$branch = $this->session->userdata('id_branch');
		//	print_r($branch);exit;
		$data['employee'] = $this->$model->get_branchwise_emp($branch);
		echo json_encode($data);
	}
	//Scheme Wise Opening and closing
	function collection_report()
	{
		$data['main_content'] = self::REP_VIEW . 'collection_report';
		$this->load->view('layout/template', $data);
	}
	function scheme_daily_collection_details()
	{
		$model =	self::PAY_MODEL;
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$id_branch = $this->input->post('id_branch');
		$schemes = $this->$model->get_active_scheme();
		foreach ($schemes as $scheme) {
			$preBlc = $this->$model->getScheme_Opening_blc_details($scheme['id_scheme'], $from_date, $id_branch);
			if (sizeof($today['collection']) == 0) {
				$op_blc_amt = 0;
				$op_blc_weight = 0;
				$op_blc_bonus = 0;
			} else {
				//echo "<pre>";print_r($preBlc);exit;
				$op_blc_amt = $preBlc['collection']['today_collection_amt'] + $preBlc['previous_blc']['balance_amount'] - $preBlc['closed']['closing_paid_amt'] + $preBlc['closed']['closing_add_chgs'];
				$op_blc_weight = $preBlc['closing_balance_wgt'] + $preBlc['collection']['today_collection_wgt'] + $preBlc['previous_blc']['balance_weight'] - ($preBlc['closed']['scheme_type'] == 2 || $preBlc['closed']['scheme_type'] == 3 ? $preBlc['closed']['closing_balance'] : 0);
				$op_blc_bonus = $preBlc['closing_bonus_amt'] + $preBlc['collection']['today_bonus_amt'] - $preBlc['closed']['today_bonus_detuction'] - $preBlc['closed']['closing_benefits'];
			}
			$today = $this->$model->get_today_collection_details($from_date, $to_date, $scheme['id_scheme'], $id_branch);
			//echo "<pre>";print_r($today);exit;
			if (sizeof($today['collection']) == 0) {
				$today['collection']['today_collection_amt'] = 0;
				$today['collection']['today_bonus_amt'] = 0;
				$today['collection']['today_collection_wgt'] = 0;
			}
			/* if(sizeof($today['closed'])==0)
                {
                    $today['closed']['today_closing_amount'] = 0;
                    $today['closed']['today_closing_weight'] = 0;
                    $today['closed']['today_bonus_detuction'] = 0;
                }*/
			$closing_balance_amt = $op_blc_amt + $today['collection']['today_collection_amt'] + $today['previous_blc']['balance_amount'] - $today['closed']['closing_paid_amt'] + $today['closed']['closing_add_chgs'];
			$closing_balance_wgt = $op_blc_weight + $today['collection']['today_collection_wgt'] + $today['previous_blc']['balance_weight'] - ($today['closed']['scheme_type'] == 2 || $today['closed']['scheme_type'] == 3 ? $today['closed']['closing_balance'] : 0);
			$closing_bonus_amt = $op_blc_bonus + $today['collection']['today_bonus_amt'] - $today['closed']['today_bonus_detuction'] - $today['closed']['closing_benefits'];
			$data[] = array(
				'opening_blc_amt' 	    => $op_blc_amt,
				'opening_blc_wgt' 	    => number_format($op_blc_weight, 3, '.', ''),
				'opening_bonus_amt' 	=> $op_blc_bonus,
				'today_collection_amt' 	=> $today['collection']['today_collection_amt'],
				'today_bonus_amt' 	    => $today['collection']['today_bonus_amt'],
				'today_collection_wgt' 	=> $today['collection']['today_collection_wgt'],
				'today_closed_amount' 	=> number_format($today['closed']['closing_paid_amt'], 2, '.', ''),
				'today_closed_weight' 	=> number_format(($today['closed']['scheme_type'] == 2 || $today['closed']['scheme_type'] == 3 ? $today['closed']['closing_balance'] : 0), 3, '.', ''),
				'today_bonus_deduction' => number_format($today['closed']['today_bonus_detuction'], 2, '.', ''),
				'closing_balance_amt'	=> number_format($closing_balance_amt, 2, '.', ''),
				'closing_balance_wgt'	=> number_format($closing_balance_wgt, 3, '.', ''),
				'closing_bonus_amt'	    => number_format($closing_bonus_amt, 2, '.', ''),
				'id_scheme'             => $scheme['id_scheme'],
				'scheme_name'           => $scheme['scheme_name'],
			);
		}
		//echo "<pre>";print_r($data);exit;
		echo json_encode($data);
	}
	//Scheme Wise Opening and closing
	//closed A/C report with date picker, cost center based branch fillter//HH
	//closed account report starts here
	function closed_account_list()
	{
		$data['main_content'] = self::REP_VIEW . 'closed_acc_report';
		$this->load->view('layout/template', $data);
	}
	function closedaccount_list()
	{
		$model =	self::ACC_MODEL;
		$model =	self::PAY_MODEL;
		$range['from_date']  = $this->input->post('from_date');
		$range['to_date']  = $this->input->post('to_date');
		$range['id_branch']  = $this->input->post('id_branch');
		$range['id_employee']  = $this->input->post('id_employee');
		$range['close_id_branch']  = $this->input->post('close_id_branch');
		//$data = $this->$model->get_all_closed_account_by_date($range['from_date'],$range['to_date'],$range['id_employee'],$range['close_id_branch']);
		$data['closed_summary'] = $this->$model->get_closed_summary_by_date($range['from_date'], $range['to_date'], $range['id_employee'], $range['close_id_branch']);
		$data['accounts'] = $this->$model->get_all_closed_account_by_date($range['from_date'], $range['to_date'], $range['id_employee'], $range['close_id_branch']);
		echo json_encode($data);
	}
	//closed account report ends here
	//closed A/C report with date picker, cost center based branch fillter//
	function customer_account_details($type = "")
	{
		$model =	self::PAY_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::REP_VIEW . 'customer_account_details';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$from_date = $this->input->post('from_date');
				$to_date = $this->input->post('to_date');
				$data = $this->$model->get_customer_account_details($from_date, $to_date);
				echo json_encode($data);
				break;
		}
	}
	//Online Payment Report
	function online_payment_report($type = "")
	{
		$data['main_content'] = self::REP_VIEW . 'online_payment_report';
		$this->load->view('layout/template', $data);
	}
	function get_online_payment_report()
	{
		$model =	self::PAY_MODEL;
		$range['from_date'] = $this->input->post("from_date");
		$range['to_date'] = $this->input->post("to_date");
		$list = $this->$model->get_online_payment_report_date($_POST);
		echo json_encode($list);
	}
	function get_payment_status()
	{
		$model =	self::PAY_MODEL;
		$list = $this->$model->get_payment_status();
		echo json_encode($list);
	}
	function old_metal_report($type = "")
	{
		$model =	self::PAY_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::REP_VIEW . 'old_metal_report';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->get_old_metal_report($_POST);
				$access = $this->admin_settings_model->get_access('reports/old_metal_report');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}
	function payment_cancel_list()
	{
		$data['main_content'] = self::REP_VIEW . 'payment_cancel_report';
		$this->load->view('layout/template', $data);
	}
	function paymentcancel_list()
	{
		$model =	self::PAY_MODEL;
		if (!empty($_POST)) {
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$data = $this->$model->paymentcancel_list_range($range['from_date'], $range['to_date']);
			//print_r($data);exit;
		} else {
			$data = $this->$model->get_cancel_payment();
		}
		//print_r($data);exit;
		echo json_encode($data);
	}
	// Scheme source wise report  --- scheme wise payment details report with mode wise + online & showroom collection report
	function scheme_payment_daterange()
	{
		$data['main_content'] = self::REP_VIEW . 'scheme_payment_daterange';
		$this->load->view('layout/template', $data);
	}
	function scheme_payment_list_daterange()
	{
		$data = array();
		$model =	self::PAY_MODEL;
		$set_model = self::SET_MODEL;
		if (!empty($_POST)) {
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$range['id_classfication']  = $this->input->post('id_classfication');
			$range['id_scheme']  = $this->input->post('id_scheme');
			$range['pay_mode']  = $this->input->post('pay_mode');
			$range['id_branch']  = $this->input->post('id_branch');
			$range['mode']  = $this->input->post('mode');
			$range['metal_date_type'] = $this->input->post('metal_date_type');
			$data['schemes'] = $this->$model->sheme_payment_list_daterange($range['from_date'], $range['to_date'], $range['id_classfication'], $range['id_scheme'], $range['pay_mode'], $range['id_branch'], $range['mode'], $range['metal_date_type']);
			$data['mode_wise'] = $this->$model->get_Scheme_Payment_ModeWiseummaryDetails($range['from_date'], $range['to_date'], $range['id_classfication'], $range['id_scheme'], $range['pay_mode'], $range['id_branch'], $range['mode'], $range['metal_date_type']);
			$data['mode_wise_sum'] = $this->$model->payment_summary_modewise_data($range['from_date'], $range['to_date'], $range['id_classfication'], $range['id_scheme'], $range['pay_mode'], $range['id_branch'], $range['mode'], $range['metal_date_type']);
			//    $data['scheme_data']=$this->$model->get_schemewise_data($range['from_date'],$range['to_date'],$range['id_branch'],$range['id_scheme'],$range['pay_mode'],$range['mode']);//added by durga 10/01/2023
			foreach ($data['mode_wise_sum']['admin_app'] as $key => $value) {
				$admin_app[] = $value['admin_app_amt'];
			}
			$data['admin_app_total'] = round(array_sum($admin_app), 2);
			foreach ($data['mode_wise_sum']['offline'] as $key => $value) {
				$offline[] = $value['offline_amt'];
			}
			$data['offline_total'] = round(array_sum($offline), 2);
			foreach ($data['mode_wise_sum']['online'] as $key => $value) {
				$online[] = $value['online_amt'];
			}
			$data['online_total'] = round(array_sum($online), 2);
		}
		//  print_r($data);exit;
		echo json_encode($data);
	}
	// function payment_summary_modewise(){
	//     exit('sdfdsf');
	//     $model =	self::PAY_MODEL;
	//     $data=array();
	//    // print_r($_POST);exit;
	//     if(!empty($_POST))
	//     {
	//     $range['from_date']  = $this->input->post('from_date');
	//     $range['to_date']  = $this->input->post('to_date');
	//     $range['id_classfication']  = $this->input->post('id_classfication');
	//     $range['id_scheme']  = $this->input->post('id_scheme');
	//     $range['pay_mode']  = $this->input->post('pay_mode');
	//     $range['id_branch']  = $this->input->post('id_branch');
	//      $range['mode']  = $this->input->post('mode');
	//     $day_close=$this->admin_report_model->get_chit_settings();
	//     $data['mode_wise'] = $this->$model->payment_summary_modewise_data($range['from_date'],$range['to_date'],$range['id_classfication'],$range['id_scheme'],$range['pay_mode'],$range['id_branch'],$range['mode'],$day_close['edit_custom_entry_date']);		
	//     foreach($data[mode_wise]['offline'] as $key=>$value){
	//         $offline[] = $value['offline_amt'];
	//     }
	//     $data['offline_total'] = array_sum($offline);
	//     foreach($data[mode_wise]['online'] as $key=>$value){
	//         $online[] = $value['online_amt'];
	//     }
	//     $data['online_total'] = array_sum($online);
	//     foreach($data[mode_wise]['admin_app'] as $key=>$value){
	//         $admin_app[] = $value['admin_app_amt'];
	//     }
	//     $data['admin_app_total'] = array_sum($admin_app);
	//     }
	//     echo json_encode($data);
	// }
	// function payment_summary_modewise(){
	//     $model =	self::PAY_MODEL;
	//     $data=array();
	//    // print_r($_POST);exit;
	//     if(!empty($_POST))
	//     {
	//     $range['from_date']  = $this->input->post('from_date');
	//     $range['to_date']  = $this->input->post('to_date');
	//     $range['id_classfication']  = $this->input->post('id_classfication');
	//     $range['id_scheme']  = $this->input->post('id_scheme');
	//     $range['pay_mode']  = $this->input->post('pay_mode');
	//     $range['id_branch']  = $this->input->post('id_branch');
	//      $range['mode']  = $this->input->post('mode');
	//     $data['mode_wise'] = $this->$model->payment_summary_modewise_data($range['from_date'],$range['to_date'],$range['id_classfication'],$range['id_scheme'],$range['pay_mode'],$range['id_branch'],$range['mode']);		
	//     foreach($data[mode_wise]['offline'] as $key=>$value){
	//         $offline[] = $value['offline_amt'];
	//     }
	//     $data['offline_total'] = array_sum($offline);
	//     foreach($data[mode_wise]['online'] as $key=>$value){
	//         $online[] = $value['online_amt'];
	//     }
	//     $data['online_total'] = array_sum($online);
	//     foreach($data[mode_wise]['admin_app'] as $key=>$value){
	//         $admin_app[] = $value['admin_app_amt'];
	//     }
	//     $data['admin_app_total'] = array_sum($admin_app);
	//     }
	//     echo json_encode($data);
	// }
	function ajax_getPayModeList()
	{
		$this->load->model("payment_model");
		$data = $this->payment_model->ajax_getPayModeList();
		echo json_encode($data);
	}
	//function altered by Durga 13.03.2023
	/* function scheme_payment_list_daterange()
    {
        $data=array();
        $model =	self::PAY_MODEL;
        $set_model=self::SET_MODEL;
        if(!empty($_POST))
        {
        $range['from_date']  = $this->input->post('from_date');
        $range['to_date']  = $this->input->post('to_date');
        $range['id_classfication']  = $this->input->post('id_classfication');
        $range['id_scheme']  = $this->input->post('id_scheme');
        $range['pay_mode']  = $this->input->post('pay_mode');
        $range['id_branch']  = $this->input->post('id_branch');
        $range['mode']  = $this->input->post('mode');
        $data['schemes']=$this->$model->sheme_payment_list_daterange($range['from_date'],$range['to_date'],$range['id_classfication'],$range['id_scheme'],$range['pay_mode'],$range['id_branch'],$range['mode']);
		$sno=0;
		foreach($data['schemes'] as $key=>$value){
			$sno++;
            $amount=[];
			$weight=[];
			foreach($value as $key1=>$value1){
				// print_r($value1);exit;
				//$amount[] = $value1['amount'];
				$amount[] = $value1['payment_amount'];
				$weight[] = $value1['metal_weight'];
			}
			$data['schemes_sum'][$key]['count'] = count($value);
			$data['schemes_sum'][$key]['total_amount'] = round(array_sum($amount),2);
			$data['schemes_sum'][$key]['total_weight'] = round(array_sum($weight),3);
			$data['schemes_sum'][$key]['scheme_list'] = $value;
			$data['schemes_sum'][$key]['sno'] = $sno;
			$data['schemes_sum'][$key]['scheme_name'] = $value1[scheme_name];
        }		
        $data['mode_wise']=$this->$model->get_Scheme_Payment_ModeWiseummaryDetails($range['from_date'],$range['to_date'],$range['id_classfication'],$range['id_scheme'],$range['pay_mode'],$range['id_branch'],$range['mode']);		
        $data['mode_wise_sum'] = $this->$model->payment_summary_modewise_data($range['from_date'],$range['to_date'],$range['id_classfication'],$range['id_scheme'],$range['pay_mode'],$range['id_branch'],$range['mode']);		
        foreach($data[mode_wise_sum]['offline'] as $key=>$value){
            $offline[] = $value['offline_amt'];
        }
        $data['offline_total'] = round(array_sum($offline),2);
        foreach($data[mode_wise_sum]['online'] as $key=>$value){
            $online[] = $value['online_amt'];
        }
        $data['online_total'] = round(array_sum($online),2);
        }
        echo json_encode($data);
    } 
    function payment_summary_modewise(){
        $model =	self::PAY_MODEL;
        $data=array();
       // print_r($_POST);exit;
        if(!empty($_POST))
        {
        $range['from_date']  = $this->input->post('from_date');
        $range['to_date']  = $this->input->post('to_date');
        $range['id_classfication']  = $this->input->post('id_classfication');
        $range['id_scheme']  = $this->input->post('id_scheme');
        $range['pay_mode']  = $this->input->post('pay_mode');
        $range['id_branch']  = $this->input->post('id_branch');
         $range['mode']  = $this->input->post('mode');
        $data['mode_wise'] = $this->$model->payment_summary_modewise_data($range['from_date'],$range['to_date'],$range['id_classfication'],$range['id_scheme'],$range['pay_mode'],$range['id_branch'],$range['mode']);		
		$get_count_mode= $this->$model->get_count_mode($range['from_date'],$range['to_date'],$range['id_classfication'],$range['id_scheme'],$range['pay_mode'],$range['id_branch'],$range['mode']); 
		foreach($data[mode_wise]['offline'] as $key=>$value){
			$data['mode_summary']['offline'][$value['payment_mode_name']][]=$value;
        }
		foreach($data[mode_wise]['online'] as $key=>$value){
			$data['mode_summary']['online'][$value['payment_mode_name']][]=$value;
        }
		foreach($data[mode_wise]['admin_app'] as $key=>$value){
			$data['mode_summary']['admin_app'][$value['payment_mode_name']][]=$value;
        }
		foreach($get_count_mode as $key=>$value){
			$mode_count[$value['payment_through'].'_count']=$value['payment_count'];
			$mode_count[$value['payment_through'].'_total']=$value['payment_amount'];
        }
		$data['online_count'] = $mode_count['online_count'] != NULL ? $mode_count['online_count'] : 0 ;
		$data['offline_count'] = $mode_count['offline_count'] != NULL ? $mode_count['offline_count'] : 0 ;
		$data['admin_app_count'] = $mode_count['admin_app_count'] != NULL ? $mode_count['admin_app_count'] : 0 ;
		$data['online_total'] = $mode_count['online_total'] != NULL ? $mode_count['online_total'] : 0 ;
		$data['offline_total'] = $mode_count['offline_total'] != NULL ? $mode_count['offline_total'] : 0 ;
		$data['admin_app_total'] = $mode_count['admin_app_total'] != NULL ? $mode_count['admin_app_total'] : 0 ;
        }
        echo json_encode($data);
    }
	 */
	// Scheme source wise report  --- scheme wise payment details report with mode wise + online & showroom collection report  -->END
	// gift issued report -->START
	function get_gift_report()
	{
		$data['main_content'] = self::REP_VIEW . 'gift_report';
		$this->load->view('layout/template', $data);
	}
	function ajax_gift_report()
	{
		$this->load->model("admin_report_model");
		$this->load->model('customer_model');
		//print_r($_POST);exit;
		if (!empty($_POST)) {
			$from_date = $this->input->post('from_date');
			$to_date = $this->input->post('to_date');
			$id_branch  = $this->input->post('id_branch');
			$id_metal = $this->input->post('id_metal');
			$id_scheme = $this->input->post('id_scheme');
			$id_gift  = $this->input->post('id_gift');
			$data['gift'] = $this->admin_report_model->get_gift_list($from_date, $to_date, $id_branch, $id_metal, $id_scheme, $id_gift);
			// print_r($data['gift']);exit;
			$data['summary'] = $this->admin_report_model->gift_summary($from_date, $to_date, $id_branch, $id_metal, $id_scheme, $id_gift);
		}
		echo json_encode($data);
	}
	//gift issued report  -->END
	/* SCHEME WISE OUTSTANDING REPORT STARTS */
	function scheme_customer_daterange()
	{
		$data['main_content'] = self::REP_VIEW . 'scheme_customer_daterange';
		$this->load->view('layout/template', $data);
	}
	function scheme_customer_list_daterange()
	{
		$data = array();
		$model = self::ACC_MODEL;
		$details = $this->$model->get_all_scheme_account_by_range();
		$data['schemes'] = $details;
		echo json_encode($data);
	}
	/*function scheme_summary(){
        $model = self::ACC_MODEL;
        $data=array();
        if(!empty($_POST))
        {
        $details = $this->$model->scheme_summary_data();
		foreach($details as $r)
        {
           if($r['is_lucky_draw']){
			$r['group_scheme']=$this->$model->scheme_group_summary_data($r['id_scheme']);
			$return_data[$r['scheme_name']]=$r;
		   }else{
			$return_data[$r['scheme_name']]=$r;
		   }
        }   
		$data= $return_data;
		}
	   return $data;
    }*/
	function scheme_summary()
	{
		$model = self::ACC_MODEL;
		$data = array();
		$details = $this->$model->scheme_summary_data();
		foreach ($details as $r) {
			if ($r['is_lucky_draw']) {
				$r['group_scheme'] = $this->$model->scheme_group_summary_data($r['id_scheme']);
				//data returned schemewise
				//$return_data[$r['scheme_name']]=$r;
				//getting data based on classification 
				$return_data[$r['code']][] = $r;
			} else {
				//data returned schemewise
				//$return_data[$r['scheme_name']]=$r;
				//getting data based on classification 
				$return_data[$r['code']][] = $r;
			}
		}
		$data['scheme_summary'] = $return_data;
		echo json_encode($data);
	}
	function is_luckly_draw_scheme()
	{
		$id = $this->input->post('id_scheme');
		$model = self::ACC_MODEL;
		$scheme = $this->$model->is_luckly_draw_scheme($id);
		if ($scheme['is_lucky_draw'] == 1) {
			$group_scheme = $this->$model->get_group_scheme_code($id);
		} else {
			$group_scheme = [];
		}
		$data = $scheme;
		$data['group_scheme'] = $group_scheme;
		echo json_encode($data);
	}
	function exl_rep_outstanding($type)
	{
		switch ($type) {
			case "export_excel":
				$filename = 'outstanding_report.xls'; // The file name you want any resulting file to be called.
				$title = "Outstanding Report";
				#2nd header as an array of columns
				$header = array("S NO", "MOBILE", "CUS NAME", "ADDRESS1", "ADDRESS2", "AREA", "CITY", "STATE", "PINCODE", "CODE", "REFERRED EMPLOYEE", "MS NO", "ACC NAME", "JOINING DATE", "MATURITY DATE", "ARRIVED OUTSTANDING(INR)", "RECIEVED WEIGHT(IN GMS)", "LASTPAID DATE", "SCHEME TYPE", "JOINED THORUGH", "EMPLOYEE NAME");
				$rows = $this->exl_rep_outstanding("get_data");
				// print_r($rows);     exit;/
				if (sizeof($rows) > 0) {
					$export = $this->download_excel_($filename, $title, $header, $rows);
					echo json_encode($export);
				} else {
					echo json_encode(array("status" => false, "title" => "Warning!", "msg" => "No data to export!"));
				}
				break;
			case "get_data":
				$model = self::ACC_MODEL;
				$data = $this->$model->get_all_scheme_account_by_range();
				$row_data = array();
				if (sizeof($data) > 0) {
					# Prepare excel rows
					foreach ($data as $sch_clsfy => $rows) {
						$row_data[] = array(0 => $sch_clsfy);
						foreach ($rows as $row) {
							$row_order = array(
								"0"	=> isset($row["sno"]) ? $row["sno"] : "",
								"1"	=> isset($row["mobile"]) ? $row["mobile"] : "",
								"2"	=> isset($row["name"]) ? $row["name"] : "",
								"3"	=> isset($row["address1"]) ? $row["address1"] : "",
								"4"	=> isset($row["address2"]) ? $row["address2"] : "",
								"5"	=> isset($row["address3"]) ? $row["address3"] : "",
								"6"	=> isset($row["city"]) ? $row["city"] : "",
								"7"	=> isset($row["state"]) ? $row["state"] : "",
								"8"	=> isset($row["pincode"]) ? $row["pincode"] : "",
								"9"	=> isset($row["code"]) ? $row["code"] : "",
								"10"	=> isset($row["referred_employee"]) ? $row["referred_employee"] : "",
								"11"	=> isset($row["scheme_acc_number"]) ? $row["scheme_acc_number"] : "",
								"12"	=> isset($row["account_name"]) ? $row["account_name"] : "",
								"13"	=> isset($row["start_date"]) ? $row["start_date"] : "",
								"14"	=> isset($row["maturity_date"]) ? $row["maturity_date"] : "",
								"15"	=> isset($row["totalpay_amount"]) ? $row["totalpay_amount"] : "",
								"16"	=> isset($row["total_wgt"]) ? $row["total_wgt"] : "",
								"17"    => isset($row["last_paid_date"]) ? $row["last_paid_date"] : "",
								"18"	=> isset($row["scheme_type"]) ? $row["scheme_type"] : "",
								"19"	=> isset($row["joined_thru"]) ? $row["joined_thru"] : "",
								"20"	=> isset($row["joined_emp"]) ? $row["joined_emp"] : ""
							);
							/*$row_order = array(
                                                        "sno"	=> isset($row["sno"]) ? $row["sno"] : "",
                                                        "mobile"	=> isset($row["mobile"]) ? $row["mobile"] : "",
                                                        "name"	=> isset($row["name"]) ? $row["name"] : "",
                                                        "address1"	=> isset($row["address1"]) ? $row["address1"] : "",
                                                        "address2"	=> isset($row["address2"]) ? $row["address2"] : "",
                                                        "address3"	=> isset($row["address3"]) ? $row["address3"] : "",
                                                        "city"	=> isset($row["city"]) ? $row["city"] : "",
                                                        "state"	=> isset($row["state"]) ? $row["state"] : "",
                                                        "pincode"	=> isset($row["pincode"]) ? $row["pincode"] : "",
                                                        "scheme_acc_number"	=> isset($row["scheme_acc_number"]) ? $row["scheme_acc_number"] : "",
                                                        "account_name"	=> isset($row["account_name"]) ? $row["account_name"] : "",
                                                        "start_date"	=> isset($row["start_date"]) ? $row["start_date"] : "",
                                                        "maturity_date"	=> isset($row["maturity_date"]) ? $row["maturity_date"] : "",
                                                        "totalpay_amount"	=> isset($row["totalpay_amount"]) ? $row["totalpay_amount"] : "",
                                                        "total_wgt"	=> isset($row["total_wgt"]) ? $row["total_wgt"] : "",
                                                        "last_paid_date"    =>isset($row["last_paid_date"]) ? $row["last_paid_date"] : "", 
                                                        "scheme_type"	=> isset($row["scheme_type"]) ? $row["scheme_type"] : "",
                                                        "joined_thru"	=> isset($row["joined_thru"]) ? $row["joined_thru"] : "",
                                                        "joined_emp"	=> isset($row["joined_emp"]) ? $row["joined_emp"] : ""
                                                        );*/
							array_push($row_data, $row_order);
						}
					}
				}
				return $row_data;
				break;
		}
	}
	/* SCHEME WISE OUTSTANDING REPORT ENDS */
	/* ends */
	/*Created by RK - 16/12/2022
	 Function to load customer bday /weddingday page*/
	/*	public function customer_wishes()
	{
		$data['main_content'] = self::REP_VIEW . 'cus_wishes_list';
		$this->load->view('layout/template', $data);
	}
	public function get_cus_birthwed()
	{
		$model =	self::DAS_MODEL;
		if (!empty($_POST['from_date'] && !empty($_POST['to_date']))) {
			$from_date  = $this->input->post('from_date');
			$end_date  = $this->input->post('to_date');
			$data['accounts'] = $this->$model->cus_wishes_list_bydate($from_date, $end_date);
		}
		echo json_encode($data);
	}
 */
	/*edit payment and acc function starts*/
	public function edit_acc_pay()
	{
		$data['main_content'] = self::REP_VIEW . 'editable_settings/acc_pay_form';
		$this->load->view('layout/template', $data);
	}
	public function editAccOrPayments($func_type)
	{
		$model =	self::PAY_MODEL;
		switch ($func_type) {
			case 'get_acc_byId':
				$id_scheme_account = $this->input->post('id_scheme_account');
				$data = $this->$model->getSchAccByID($id_scheme_account);
				echo json_encode($data);
				break;
			case 'get_pay_byId':
				$id_payment = $this->input->post('id_payment');
				$data = $this->$model->getPaymentDataByID($id_payment);
				if (!empty($data['id_scheme_account'])) {
					$acc_data = $this->db->query("SELECT DATE_FORMAT(sa.start_date, '%Y-%m-%d') as acc_start_date, 
						IFNULL(s.is_digi, 0) as is_digi,
						IFNULL(s.installment_cycle, 0) as installment_cycle,
						IFNULL(s.ins_days_duration, 0) as ins_days_duration,
						IFNULL(s.payment_chances, 0) as payment_chances,
						IFNULL(s.scheme_type, 0) as scheme_type,
						IFNULL(s.flexible_sch_type, 0) as flexible_sch_type
						FROM scheme_account sa 
						LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme 
						WHERE sa.id_scheme_account = " . $data['id_scheme_account'])->row();
					$data['acc_start_date'] = isset($acc_data->acc_start_date) ? $acc_data->acc_start_date : '';
					$data['is_digi'] = isset($acc_data->is_digi) ? $acc_data->is_digi : 0;
					$data['installment_cycle'] = isset($acc_data->installment_cycle) ? intval($acc_data->installment_cycle) : 0;
					$data['ins_days_duration'] = isset($acc_data->ins_days_duration) ? intval($acc_data->ins_days_duration) : 0;
					$data['payment_chances'] = isset($acc_data->payment_chances) ? intval($acc_data->payment_chances) : 0;
					$data['scheme_type'] = isset($acc_data->scheme_type) ? intval($acc_data->scheme_type) : 0;
					$data['flexible_sch_type'] = isset($acc_data->flexible_sch_type) ? intval($acc_data->flexible_sch_type) : 0;
				}
				echo json_encode($data);
				break;
			case 'get_acc_start_date':
				$id_scheme_account = intval($this->input->post('id_scheme_account'));
				$acc_data = $this->db->query("SELECT DATE_FORMAT(sa.start_date, '%Y-%m-%d') as acc_start_date FROM scheme_account sa WHERE sa.id_scheme_account = " . $id_scheme_account)->row();
				echo json_encode(array('acc_start_date' => isset($acc_data->acc_start_date) ? $acc_data->acc_start_date : ''));
				break;
			case 'get_digi_benefit':
				$id_scheme_account = intval($this->input->post('id_scheme_account'));
				$date_payment = $this->input->post('date_payment');
				$payment_amount = floatval($this->input->post('payment_amount'));
				$metal_rate = floatval($this->input->post('metal_rate'));
				$metal_weight = floatval($this->input->post('metal_weight'));
				
				$result = array('is_digi' => 0, 'saved_benefits' => 0, 'saved_benefit_amt' => 0);
				
				$sch_data = $this->db->query("SELECT s.is_digi, s.interest, s.id_metal, sa.start_date, sa.id_branch, sa.id_scheme
					FROM scheme_account sa 
					LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme 
					WHERE sa.id_scheme_account = " . $id_scheme_account)->row();
				
				if (!empty($sch_data) && $sch_data->is_digi == 1) {
					$result['is_digi'] = 1;
					if ($sch_data->interest == 1) {
						$acc_start = date('Y-m-d', strtotime($sch_data->start_date));
						$date_diff = max(1, (strtotime($date_payment) - strtotime($acc_start)) / 86400);
						if ($date_payment == $acc_start) $date_diff = 1;
						
						$benefit_data = $this->db->query("SELECT interest_type, interest_value, commodity 
							FROM scheme_benefit_deduct_settings 
							WHERE ('" . $date_diff . "' BETWEEN installment_from AND installment_to) 
							AND id_scheme = " . $sch_data->id_scheme)->result_array();
						
						if (!empty($benefit_data)) {
							foreach ($benefit_data as $benefit) {
								if ($benefit['commodity'] == $sch_data->id_metal) {
									if ($benefit['interest_type'] == 0) {
										$result['saved_benefit_amt'] = $payment_amount * ($benefit['interest_value'] / 100);
									} else {
										$result['saved_benefit_amt'] = $benefit['interest_value'];
									}
									$result['saved_benefits'] = ($metal_rate > 0 ? formatMetalWeight($result['saved_benefit_amt'] / $metal_rate) : 0);
								}
							}
						}
					}
				}
				echo json_encode($result);
				break;
		}
	}
	function updatePaymentDetails()
	{
		$model =    self::PAY_MODEL;
		if (!empty($_POST)) {
			// Write log 
			$log_path = 'log/payment' . date("Y-m-d") . '.txt';
			$ldata = "\n" . date('d-m-Y H:i:s') . " \n Edit Payment : " . json_encode($_POST, true);
			file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
			// print_r($_POST); exit;
			$data = $this->$model->updatePaymentdata($_POST);
			echo json_encode($data);
		}
	}
	function updateAccountDetails()
	{
		$model =    self::PAY_MODEL;
		// print_r($_POST);exit;
		if (!empty($_POST)) {
			// Write log 
			$log_path = 'log/account' . date("Y-m-d") . '.txt';
			$ldata = "\n" . date('d-m-Y H:i:s') . " \n Edit Account : " . json_encode($_POST, true);
			file_put_contents($log_path, $ldata, FILE_APPEND | LOCK_EX);
			$cusmobile_exist = $this->$model->cusexist($_POST['mobile'], 'mobile', 'customer');
			if ($cusmobile_exist) {
				$inp_data = array('id_customer' => $cusmobile_exist['id_customer']);
				$setStatus = $this->checkCommonSettings($_POST);
				if($setStatus['success']){
					// Update customer mapping on scheme_account
					$data = $this->$model->updateDatacus($inp_data, 'id_scheme_account', $_POST['id_scheme_account'], 'scheme_account');
					
					// Update start_date and recalculate maturity_date if start_date is provided
					$date_updated = false;
					if (!empty($_POST['start_date'])) {
						$new_start_date = date('Y-m-d', strtotime($_POST['start_date']));
						$new_maturity_date = $this->$model->calcMaturityDate($new_start_date, $_POST['id_scheme_account']);
						$date_data = array(
							'start_date' => $new_start_date,
							'maturity_date' => $new_maturity_date
						);
						$date_updated = $this->$model->updData($date_data, 'id_scheme_account', $_POST['id_scheme_account'], 'scheme_account');
					}

					if ($data == true || $date_updated) {
						$result = array("status" => TRUE, "msg" => "Account details updated successfully.");
					} else {
						$result = array("status" => FALSE, "msg" => "No changes detected or update failed.");
					}
				}else{
					$result = array("status" => $setStatus['success'], "msg" => $setStatus['message']);
				}
				
			} else {
				$result = array("status" => FALSE, "msg" => "Customer mobile number is not available.");
			}
			echo json_encode($result);
		}
	}
	function generateTransUniqId()
	{
		$this->load->model("payment_model");
		if (!empty($_POST)) {
			$updData = array('offline_tran_uniqueid' => null);
			$data = $this->payment_model->updData($updData, 'id_payment', $_POST['id_payment'], 'payment');
			// print_r($this->db->last_query());exit;
			if ($data > 0) {
				$gen = $this->generateTranUniqueIdManually($_POST['id_payment']);
				//$gen = 'khimji response';
				$result =  "RESPONSE: <pre>" . json_encode($gen);
			} else {
				$result =  "Unable to proceed your request";
			}
			$this->session->set_flashdata('chit_alert', array('message' => $result, 'class' => 'success', 'title' => "Result"));
			echo json_encode(true);
		}
	}
	function generateTranUniqueIdManually($id_payment)
	{
		$this->load->model("integration_model");
		$sql = $this->db->query("SELECT c.reference_no, nominee_name, nominee_relationship,nominee_address1,nominee_address2,nominee_mobile,
	                                    sync_scheme_code,sa.scheme_acc_number,e.firstname as emp_name,sa.referal_code,
	                                    p.payment_amount
	                              FROM payment p  
	                                LEFT JOIN scheme_account sa on sa.id_scheme_account = p.id_scheme_account
	                                LEFT JOIN customer c on sa.id_customer = c.id_customer
	                                LEFT JOIN scheme s on s.id_scheme = sa.id_scheme
	                                LEFT JOIN employee e on sa.referal_code = e.emp_code
	                              WHERE (receipt_no is null or receipt_no = 'NaN') and p.offline_tran_uniqueid is null and p.id_payment =" . $id_payment . " group by id_payment");
		$chit = $sql->row_array();
		// 	echo $this->db->last_query();
		//	echo "<pre>";print_r($chit);exit;
		if ($sql->num_rows() == 1) {
			$postData = array(
				"isKycValidationCheck" => false,
				"customerCode"  => $chit['reference_no'],
				"transactionType" => 1,
				"schemeCode"    => $chit['sync_scheme_code'],
				"amount"        => $chit['payment_amount'],
				"date"          => date("Y-m-d H:i:s"),
				"narration"     => "Requested from Web app"
			);
			$is_new_ac = ($chit['scheme_acc_number'] != "" && $chit['scheme_acc_number'] != NULL ? FALSE : TRUE);
			if ($is_new_ac) { // 1st Installment 
				$postData["action"] = 1;
				$postData["narration"]   =  $chit['nominee_address1'] . "," . $chit['nominee_address2'];
				$postData["narration2"]  =  $chit['nominee_relationship']; //"Nominee Relation"
				$postData["narration3"]  =  $chit['nominee_name']; //"Nominee Name"
				$postData["narration4"]  =  ""; //Nominee DOB"
				$postData["narration5"]  =  $chit['nominee_mobile']; //"Nominee MobNo"
				$postData["salesmanName"] =  $chit['emp_name'];
				$postData["employeeId"]  =  $chit['referal_code'];
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
			//echo "<pre>";print_r($postData);
			//echo "<pre>";print_r($response);exit;
			if ($response['status'] == TRUE) {
				$resData = $response['data']->data;
				if ($resData->status == TRUE && $resData->errorCode == 0) {
					$pData = array(
						'offline_tran_uniqueid' => $resData->result->tranUniqueId,
						'date_upd'			    => date("Y-m-d H:i:s")
					);
					$this->integration_model->updateData($pData, 'id_payment', $id_payment, 'payment');
					//return $resData->result->tranUniqueId;
					$id = "Trans Unique Id" . $resData->result->tranUniqueId;
				} else if ($resData->errorCode == 1001) {
					$payData = array(
						'offline_error_msg'	=> date("Y-m-d H:i:s") . " ID Gen Error : " . $resData->errorMsg,
					);
					$this->integration_model->updateData($payData, 'id_payment', $id_payment, 'payment');
					$id = "ID Gen Error :" . $resData->errorMsg;
				}
			} else {
				$id = "Unable to Proceed your request";
			}
			//echo "Error : ";
		}
		//echo "Response : ";
		//echo "<pre>".$id_payment;
		//echo "<pre>".$id;
		return $response;
	}
	//Scheme wise mode wise report starts here
	// Created by Durga 29.06.2023 starts here 
	function payment_modeandgroupwise_data()
	{
		$data['main_content'] = self::REP_VIEW . 'payment_mode_groupwise_list';
		$this->load->view('layout/template', $data);
	}
	function payment_modeandgroupwise_list()
	{
		$model = self::PAY_MODEL;
		$range['from_date']  = $this->input->post('from_date');
		$range['to_date']  = $this->input->post('to_date');
		$range['id']  = $this->input->post('id');
		$items = $this->$model->get_group_modewise_list($range['from_date'], $range['to_date'], $range['id']);
		//print_r($items);exit;
		echo json_encode($items);
	}
	function giftname_list()
	{
		$acc_model = self::ACC_MODEL;
		$data = $this->$acc_model->giftname_list();
		// print_r($data);exit;
		echo json_encode($data);
	}
	function get_online_gift_summary()
	{
		$acc_model = self::ACC_MODEL;
		$data = $this->$acc_model->get_online_gift_summary();
		echo json_encode($data);
	}
	function get_online_gift_report()
	{
		$acc_model = self::ACC_MODEL;
		$list = $this->$acc_model->get_online_gift_report($_POST);
		echo json_encode($list);
	}
	// Created by Durga 29.06.2023 ends here 
	//Scheme wise mode wise report ends here
	//   Member Report starts here
	//Function to view 
	function member_report()
	{
		$data['main_content'] = self::REP_VIEW . 'member_report';
		$this->load->view('layout/template', $data);
	}
	//function to return member report data invoked from report.js
	function getMemberReport()
	{
		$data = $this->payment_model->getMemberReport();
		echo json_encode($data);
	}
	function get_joined_through()
	{
		$joined_through_data = $this->payment_model->get_joined_through();
		echo json_encode($joined_through_data);
	}
	function get_city()
	{
		$city_data = $this->payment_model->get_city();
		echo json_encode($city_data);
	}
	function get_area()
	{
		$term = $this->input->get('term');
		$area_data = $this->payment_model->search_area($term);
		echo json_encode($area_data);
	}
	//   Member Report ends here
	//general advance reports starts here
	function general_advance_view()
	{
		$data['main_content'] = self::REP_VIEW . 'general_adv_payment_list';
		$this->load->view('layout/template', $data);
	}
	function general_advance_list()
	{
		$data['accounts'] = $this->payment_model->general_advance_list();
		//print_r($data);exit;
		echo json_encode($data);
	}
	//general advance reports ends here
	function general_advance_list_byid()
	{
		$data['accounts'] = $this->payment_model->general_advance_list_byid();
		echo json_encode($data);
	}
	//monthly chit reports starts here
	function monthly_report_view()
	{
		$data['main_content'] = self::REP_VIEW . 'monthly_chit_report';
		$this->load->view('layout/template', $data);
	}
	function monthly_report_data()
	{
		$data['accounts'] = $this->payment_model->monthly_report_data();
		echo json_encode($data);
	}
	//monthly chit reports ends here
	/*ends*/
	private function download_excel_($filename, $title, $header, $rows)
	{
		// Load the Excel library if not autoloaded
		$this->load->library('excel');
		// Create a new PHPExcel object
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
		// Set the width of column B to 20 units
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		// Add title
		$objPHPExcel->getActiveSheet()->setCellValue('A1', $title);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:' . PHPExcel_Cell::stringFromColumnIndex(count($header) - 1) . '1');
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true)->setSize(16);
		// Add header
		foreach (range(0, count($header) - 1) as $colIndex) {
			$colName = PHPExcel_Cell::stringFromColumnIndex($colIndex);
			$objPHPExcel->getActiveSheet()->setCellValue($colName . '2', $header[$colIndex]);
		}
		// Add data rows
		foreach ($rows as $rowIndex => $row) {
			foreach (range(0, count($row) - 1) as $colIndex) {
				$colName = PHPExcel_Cell::stringFromColumnIndex($colIndex);
				$objPHPExcel->getActiveSheet()->setCellValue($colName . ($rowIndex + 3), $row[$colIndex]);
			}
		}
		ob_end_clean();
		// Set the content type and header for the download
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=UTF-8');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		// Save the Excel file to PHP output
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		// Exit to prevent any additional output
		exit();
	}
	//Maturity report ---start
	function maturity_report_view()
	{
		$data['main_content'] = self::REP_VIEW . 'maturity_report';
		$this->load->view('layout/template', $data);
	}
	function maturity_report_data()
	{
		$data['accounts'] = $this->payment_model->maturity_report_data();
		echo json_encode($data);
	}
	//Maturity report ---End
	function form_logger($type = "", $id = "", $from_date = "", $to_date = "")
	{
		$model = self::LOG_MODEL;
		$set = self::SET_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = "log/form_logger";
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->get_form_logger_log_list($id, $from_date, $to_date);
				$access = $this->$set->get_access('admin_ret_tagging/bulk_tag_edit_log/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
			case 'get_form_log_data':
				$log_data = $this->$model->get_form_log_data($id);
				echo json_encode($log_data);
				break;
		}
	}
	function payment_summary_modewise()
	{
		$model =	self::PAY_MODEL;
		$data = array();
		// print_r($_POST);exit;
		if (!empty($_POST)) {
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$range['id_classfication']  = $this->input->post('id_classfication');
			$range['id_scheme']  = $this->input->post('id_scheme');
			$range['pay_mode']  = $this->input->post('pay_mode');
			$range['id_branch']  = $this->input->post('id_branch');
			$range['mode']  = $this->input->post('mode');
			$range['metal_date_type'] = $this->input->post('metal_date_type');
			$day_close = $this->admin_report_model->get_chit_settings();
			$data['mode_wise'] = $this->$model->payment_summary_modewise_data($range['from_date'], $range['to_date'], $range['id_classfication'], $range['id_scheme'], $range['pay_mode'], $range['id_branch'], $range['mode'], $day_close['edit_custom_entry_date'], $range['metal_date_type']);
			foreach ($data['mode_wise']['offline'] as $key => $value) {
				$offline[] = $value['offline_amt'];
			}
			$data['offline_total'] = array_sum($offline);
			foreach ($data['mode_wise']['online'] as $key => $value) {
				$online[] = $value['online_amt'];
			}
			$data['online_total'] = array_sum($online);
			foreach ($data['mode_wise']['admin_app'] as $key => $value) {
				$admin_app[] = $value['admin_app_amt'];
			}
			$data['admin_app_total'] = array_sum($admin_app);

			// --- Authoritative subtotals from payment table (matches Total Payments box) ---
			$date_type = $this->input->post('date_type');
			$id_branch = $range['id_branch'];
			$from_date = date('Y-m-d', strtotime($range['from_date']));
			$to_date   = date('Y-m-d', strtotime($range['to_date']));
			$date_col  = ($date_type != '' ? ($date_type == 2 ? 'COALESCE(p.custom_entry_date, p.date_payment)' : 'p.date_payment') : 'p.date_payment');
			$branch_sql = ($id_branch != '' && $id_branch > 0 ? " AND p.id_branch=" . (int)$id_branch : '');

			$base_sql = "SELECT IFNULL(SUM(p.payment_amount), 0) as auth_total
			             FROM payment p
			             WHERE p.payment_status = 1
			             AND (date({$date_col}) BETWEEN '{$from_date}' AND '{$to_date}')
			             {$branch_sql}";

			$data['offline_auth_total']   = $this->db->query($base_sql . " AND p.added_by IN (0,6)")->row()->auth_total;
			$data['online_auth_total']    = $this->db->query($base_sql . " AND p.added_by IN (1,2)")->row()->auth_total;
			$data['admin_app_auth_total'] = $this->db->query($base_sql . " AND p.added_by IN (3)")->row()->auth_total;
			// ------------------------------------------------------------------------------
		}
		echo json_encode($data);
	}
	function get_yet_to_issue()
	{
		$data['main_content'] = self::REP_VIEW . 'gift_yet_to_issue';
		$this->load->view('layout/template', $data);
	}
	/*Actual + renewal + live accounts by date range report starts*/
	function renewal_live_report()
	{
		$data['main_content'] = self::REP_VIEW . 'renewal_live_report';
		$this->load->view('layout/template', $data);
	}
	function getRenewalLive_arlData()
	{
		$result['arlData'] = $this->payment_model->renewalLive_arlData();
		echo json_encode($result);
	}
	/*ARL report ends*/
	public function customer_wishes()
	{
		$data['main_content'] = self::REP_VIEW . 'celeb_days';
		$this->load->view('layout/template', $data);
	}
	function cus_celeb_dates()
	{
		$data = array();
		$errCode = 0;
		$success = false;
		$message = "";
		try {
			$postData = $_POST;
			$details = $this->admin_report_model->get_all_cus_celeb_dates($postData);
			if (!empty($details)) {
				$data = $details;
				$success = true;
			} else {
				throw new Exception("No details found for the given date range.");
			}
		} catch (Exception $e) {
			$success = false;
			$message = $e->getMessage();
		}
		$result = array(
			"success" => $success,
			"errCode" => $errCode,
			"message" => $message,
			"data" => $data
		);
		//$this->response($result, 200);
		echo json_encode($result);
	}

    //Kyc Approval Data status filter with date picker//hh
    function kycdata_list() 
	{
	   $data['main_content'] = self::REP_VIEW.'kyc_table_data/kyc_data';
        $this->load->view('layout/template', $data); 
	}	

   function kycapproval_data(){
        $model=	self::PAY_MODEL;
        $setmodel=self::SET_MODEL;
		if(!empty($_POST)){
			$range['from_date']  = $this->input->post('from_date');
			$range['to_date']  = $this->input->post('to_date');
			$range['status']  = $this->input->post('status');
			$range['type']  = $this->input->post('type');
			$range['list_type']  = $this->input->post('list_type');
			$range['id_customer']  = $this->input->post('id_customer');
			$range['mobile']  = $this->input->post('mobile');

       	    $data=$this->$model->get_kycdata_range($range['from_date'],$range['to_date'],$range['status'],$range['type'],$range['list_type'],$range['id_customer'],$range['mobile']);
            echo json_encode($data);
		}
    }

	function update_kyc()
	{
        $model = self::PAY_MODEL;
        $kycdata   = $this->input->post('kyc_data');
        $kyc_type = $this->input->post('kyc_type');
        $res = 0;
        foreach($kycdata as $data){
            $employee= $this->session->userdata('uid');
            $updatedata = array(  "status"          => $data['status'],
                                  "emp_verified_by" => $employee,
                                  "last_update"     => date('Y-m-d H:i:s'),
                                );
            if($kyc_type == 1)
            {
                    $result = $this->$model->updatekyc($updatedata,$data['id_kyc'],$data['cus']);
                    if($result['verified_kycs']==1){
                      $update = array("kyc_status"      =>1);
                      $result = $this->$model->updatekyccus($update,$data['cus']);
                 	}
                    if($result == TRUE){
                        $res = $res+1;
                    }
            }else if($kyc_type == 2){
                $result = $this->$model->updateAgentkyc($updatedata,$data['id_kyc'],$data['cus']);
                    if($result['verified_kycs']==1){
                      $update = array("kyc_status" =>1,);
                      $result = $this->$model->updatekycAgentStatus($update,$data['cus']);
                 	}
                    if($result == TRUE){
                        $res = $res+1;
                    }
            }
        }
        if($kycdata[0]['status']==3)
        {
           $this->session->set_flashdata('chit_alert',array('message'=> count($res).' KYC records Rejected...','class'=>'danger','title'=>'Kyc Data'));
        }
        else if($kycdata[0]['status']==2){
            $this->session->set_flashdata('chit_alert',array('message'=> count($res).' KYC records Verified successfully...','class'=>'warning','title'=>'Kyc Data'));
        }
        else
        {
            $this->session->set_flashdata('chit_alert',array('message'=> count($res).'KYC records updated successfully...','class'=>'success','title'=>'Kyc Data'));
        }
        echo json_encode($res);
    }
	
	function getkycdata_byid()
	{
		$model = self::PAY_MODEL;
		$data=$this->$model->getkycdata_byid();
		echo json_encode($data);
	}
    //Kyc Approval Data status filter with date picker//hh

	function checkCommonSettings($data){
		$branchSet = $this->payment_model->getBranchwiseSettings();
		$getBranch = $this->payment_model->getBranchData($data);
		
		$prevBranch = $getBranch['prev_cus_branch'] ?? 0;
		$accBranch  = $getBranch['acc_branch'] ?? 0;
		$cusBranch  = $getBranch['cus_branch'] ?? 0;

		// 1️⃣ Check customer registration branch rule
		if ($branchSet['is_branchwise_cus_reg'] == 1) {

			if ($prevBranch != $cusBranch) {
				return [
					"success" => false,
					"message" => "Branch mismatch: The previous customer and the updated customer are assigned to different branches."
				];
			}
		}

		// 2️⃣ Check scheme branch rule
		if ($branchSet['branchwise_scheme'] == 1) {

			if ($accBranch != $cusBranch) {
				return [
					"success" => false,
					"message" => "Branch mismatch: Customer branch differs from scheme branch."
				];
			}
		}

		return [
			"success" => true,
			"message" => "Branch validation successful"
		];
	}
}
