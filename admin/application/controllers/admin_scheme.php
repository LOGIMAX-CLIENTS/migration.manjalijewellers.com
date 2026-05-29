<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_scheme extends CI_Controller
{
	const SCH_MODEL	= "scheme_model";
	const ACC_MODEL	= "account_model";
	const SCH_VIEW = "master/scheme/";
	const SET_MODEL = "admin_settings_model";
	const PAY_MODEL = "payment_model";
	const LOG_MODEL = "log_model";
	function __construct()
	{
		parent::__construct();
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
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->model(self::SCH_MODEL);
		$this->load->model(self::SET_MODEL);
		$this->load->model(self::PAY_MODEL);
		$this->load->model(self::LOG_MODEL);
		$this->load->model(self::ACC_MODEL);
		$this->id_log =  $this->session->userdata('id_log');
	}
	public function index()
	{
		$this->sch_list();
	}
	public function ajax_get_schemes_list()
	{
		$set_model = self::SET_MODEL;
		$access = $this->$set_model->get_access('scheme');
		$model =	self::SCH_MODEL;
		$items = $this->$model->get_all_schemes();
		$scheme = array(
			'access' => $access,
			'data'   => $items
		);
		echo json_encode($scheme);
	}
	public function ajax_fixweight_schemes()
	{
		$model =	self::SCH_MODEL;
		$scheme = $this->$model->get_fixweight_schemes();
		echo json_encode($scheme);
	}
	/* public function ajax_fixweight_schemes()
	{
		$model=	self::SCH_MODEL;
		$scheme=$this->$model->gst_settings();
		echo json_encode($scheme);
	}    */
	public function sch_list($msg = "")
	{
		$model_name = self::SCH_MODEL;
		$setmodel =	self::SET_MODEL;
		$data['message'] = $msg;
		$sch_data = $this->$model_name->get_all_schemes();
		if ($sch_data) {
			$data['schemes'] = $sch_data;
		}
		$data['main_content'] = self::SCH_VIEW . 'list';
		$data['access'] = $this->$setmodel->get_access('account/scheme_group/list');
		$this->load->view('layout/template', $data);
	}
	/* public function sch_form($type="",$id="")
	{
		$model= self::SCH_MODEL;
		$pay_model = self::PAY_MODEL;
		switch($type)
		{
			case 'Add':
						$set_model=self::SET_MODEL;
						$limit= $this->$set_model->limitDB('get','1');
						$discount= $this->$set_model->discount_db('get','1');
						$count= $this->$model->scheme_count();
						$gst_data=$this->$set_model->get_gstsettings();
						//print_r($count);exit;
						if($limit['limit_sch']==1)
						{
							if($count < $limit['sch_max_count'])
							{
								 	$data['sch'] = $this->$model->empty_record();
								 	$data['discount'] = $discount;
								 	$data['gst'] = $gst_data;
				   					$data['main_content'] = self::SCH_VIEW."form" ;
				   					$this->load->view('layout/template', $data);
							}else
							{
						 	$this->session->set_flashdata('sch_info', array('message' => 'Scheme creation limit exceeded, Kindly contact Super Admin...','class' => 'danger','title'=>'Scheme creation'));
						 	redirect('scheme');
							}
						}else
						{
								 	$data['sch']=$this->$model->empty_record();
								 	$data['discount'] = $discount;
								 	$data['gst'] = $gst_data;
				   					$data['main_content'] = self::SCH_VIEW."form" ;
				   					$this->load->view('layout/template', $data);
						}
				break;
			case 'Edit':
					    $set_model=self::SET_MODEL;					
					    $datas        =  $this->$model->get_scheme1($id);						
						$gst_data=$this->$set_model->get_gstsettings();
						$branch=implode(',',$datas);
					    $data['sch']     =  $this->$model->get_scheme($id);
						$data['sch']['id_branch']=$branch;
						$data['gst'] = $gst_data;
						$data['gst_data']=$this->$model->get_gstSplitupData($id);
							$data['discount']= $this->admin_settings_model->discount_db('get','1');	
							$data['main_content'] = self::SCH_VIEW."form" ;
							//
							$this->load->view('layout/template', $data);
						//}
				break;
		}
	}
	 */
	public function sch_form($type = "", $id = "")
	{
		$model = self::SCH_MODEL;
		$pay_model = self::PAY_MODEL;
		switch ($type) {
			case 'Add':
				/*-- Coded by ARVK --*/
				$set_model = self::SET_MODEL;
				$limit = $this->$set_model->limitDB('get', '1');
				$discount = $this->$set_model->discount_db('get', '1');
				$count = $this->$model->scheme_count();
				$gst_data = $this->$set_model->get_gstsettings();
				//print_r($count);exit;
				if ($limit['limit_sch'] == 1) {
					if ($count < $limit['sch_max_count']) {
						$data['sch'] = $this->$model->empty_record();
						$data['discount'] = $discount;
						$data['gst'] = $gst_data;
						$data['chit_metals'] = $this->$model->get_chit_metals();
						$data['main_content'] = self::SCH_VIEW . "form";
						$this->load->view('layout/template', $data);
					} else {
						$this->session->set_flashdata('sch_info', array('message' => 'Scheme creation limit exceeded, Kindly contact Super Admin...', 'class' => 'danger', 'title' => 'Scheme creation'));
						redirect('scheme');
					}
				} else {
					$data['sch'] = $this->$model->empty_record();
					$data['discount'] = $discount;
					$data['gst'] = $gst_data;
					$data['chit_metals'] = $this->$model->get_chit_metals();
					// $data['enableDigi'] = $this->$model->enableDigiGold('',);
					$data['main_content'] = self::SCH_VIEW . "form";
					$this->load->view('layout/template', $data);
				}
				/*--/ Coded by ARVK --*/
				break;
			case 'Edit':
				$set_model = self::SET_MODEL;
				$gst_data = $this->$set_model->get_gstsettings();
				$data['sch'] = $this->$model->get_scheme($id);
				$data['adv_benefit_data'] = $this->$model->get_adv_benefit_data($id);   //TKV double bonus
				//		echo '<pre>';print_r($data['sch']);exit;
				$data['flex_sch_data'] = $this->$model->get_flexible_ins_data($id);
				//echo "<pre>";print_r($data['sch']);echo "</pre>";exit;
				$data['chartData'] = $this->$model->get_benfit_rdeduct_data($id);
				$data['preclosechartdata'] = $this->$model->get_benfit_rdeduct_preclose__data($id);
				$data['agentbenefitchart'] = $this->$model->get_agent_benefit__data($id);                        //agent benefit
				$data['incentive_chart'] = $this->$model->get_incentive_data($id);   // employee & agent incentive benefit settings
				//echo "<pre>";print_r($data['chartData']);echo "</pre>";exit;
				$data['gst_data'] = $this->$model->get_gstSplitupData($id);
				$data['branch_data'] = json_encode($this->scheme_model->get_branch_edit($id));
				$data['gst'] = $gst_data;
				$data['discount'] = $this->admin_settings_model->discount_db('get', '1');
				$data['closing_data'] = $this->$model->get_closing_scheme_data($id);
				$data['enableDigi'] = $this->$model->enableDigiGold($id, $data['sch']['id_metal']);
				$data['chit_metals'] = $this->$model->get_chit_metals();
				//echo "<pre>";print_r($data);echo "</pre>";exit;
				// Chart data for top up scheme 
				$data['topUpSchemeChart'] = $this->$model->getTopUpSchemeChart($id);
				$data['main_content'] = self::SCH_VIEW . "form";
				$data['service_list'] = array(
					array(
						'value'     	=> 'time',
						'text'			=> 'time'
					),
					array(
						'value'     	=> 'rate',
						'text'			=> 'rate'
					)
				);
				$this->load->view('layout/template', $data);
				break;
		}
	}
	public function sch_post($type = "", $id = "")
	{
		$model = self::SCH_MODEL;
		$log_model	 = self::LOG_MODEL;
		switch ($type) {
			case 'Add':
				$sch_data = $this->input->post("sch");
				$gst_data = $this->input->post("gst_data");
				$branch_data = $this->input->post("branch_data");
				$topUpSchemeChart = $this->input->post("topUpSchemeChart");
				//echo"<pre>";  print_r($sch_data);exit;
				$allowAdvanceInStr = implode(',', $sch_data['allow_advance_in']); // Converts the array to a comma-separated string
				$allowUnpaidInStr = implode(',', $sch_data['allow_unpaid_in']);
				$sch_info = array(
					'min_amount'			=> number_format((isset($sch_data['min_amount']) ? ($sch_data['min_amount'] != '' ? $sch_data['min_amount'] : 0) : 0), 2, ".", ""),
					'max_amount'			=> number_format((isset($sch_data['max_amount']) ? ($sch_data['max_amount'] != '' ? $sch_data['max_amount'] : 0) : 0), 2, ".", ""),
					'max_amt_chance'		=> number_format((isset($sch_data['max_amt_chance']) ? $sch_data['max_amt_chance'] : 0), 2, ".", ""),
					'min_amt_chance'		=> number_format((isset($sch_data['min_amt_chance']) ? $sch_data['min_amt_chance'] : 0), 2, ".", ""),
					'pay_duration'			=> (isset($sch_data['pay_duration']) ? $sch_data['pay_duration'] : NULL),
					'wgt_convert'			=> (isset($sch_data['wgt_convert']) ? $sch_data['wgt_convert'] : 2),
					'scheme_name'			=> (isset($sch_data['scheme_name']) ? $sch_data['scheme_name'] : NULL),
					'code'					=> (isset($sch_data['code']) ? $sch_data['code'] : NULL),
					'sync_scheme_code'		=> (isset($sch_data['sync_scheme_code']) ? $sch_data['sync_scheme_code'] : NULL),
					'id_metal'				=> (isset($sch_data['id_metal']) ? $sch_data['id_metal'] : 0),
					'id_purity'				=> (isset($sch_data['id_purity']) ? $sch_data['id_purity'] : NULL),
					'id_classification'		=> (isset($sch_data['id_classification']) ? $sch_data['id_classification'] : 0),
					'scheme_type'			=> (isset($sch_data['scheme_type']) ? $sch_data['scheme_type'] : 0),
					'noti_msg'			    => (isset($sch_data['noti_msg']) ? $sch_data['noti_msg'] : NULL),
					'amount'				=> number_format((isset($sch_data['amount']) ? $sch_data['amount'] : 0), 2, ".", ""),
					'total_installments'	=> (isset($sch_data['total_installments']) ? $sch_data['total_installments'] : 0),
					//05-12-2022
					'maturity_installment'	=> (isset($sch_data['maturity_installment']) ? $sch_data['maturity_installment'] : NULL),
					'maturity_type'	        => (isset($sch_data['maturity_type']) ? $sch_data['maturity_type'] : 1),
					'payment_chances'		=> (isset($sch_data['payment_chances']) ? $sch_data['payment_chances'] : 0),
					'min_chance'			=> (isset($sch_data['min_chance']) ? $sch_data['min_chance'] : 0),
					'max_chance'			=> (isset($sch_data['max_chance']) ? $sch_data['max_chance'] : 0),
					//'min_chance'	        =>($sch_data['payment_chances']==0?1:(isset($sch_data['min_chance'])?$sch_data['min_chance']:0)),
					//'max_chance'	        =>($sch_data['payment_chances']==0?1:(isset($sch_data['max_chance'])?$sch_data['max_chance']:0)),
					'sch_limit_value'	    => (isset($sch_data['sch_limit_value']) ? $sch_data['sch_limit_value'] : NULL),
					'min_weight'			=> number_format((!empty($sch_data['min_weight']) ? $sch_data['min_weight'] : 0), 3, ".", ""),
					'max_weight'			=> number_format((!empty($sch_data['max_weight']) ? $sch_data['max_weight'] : 0), 3, ".", ""),
					'allow_unpaid'			=> (!empty($sch_data['allow_unpaid']) ? $sch_data['allow_unpaid'] : 0),
					'unpaid_months'			=> (!empty($sch_data['unpaid_months']) ? $sch_data['unpaid_months'] : 0),
					'unpaid_weight_limit'	=> (!empty($sch_data['unpaid_weight_limit']) ? $sch_data['unpaid_weight_limit'] : 0),
					'allow_advance'			=> (!empty($sch_data['allow_advance']) ? $sch_data['allow_advance'] : 0),
					'advance_months'		=> (!empty($sch_data['advance_months']) ? $sch_data['advance_months'] : 0),
					'advance_weight_limit'	=> (!empty($sch_data['advance_weight_limit']) ? $sch_data['advance_weight_limit'] : 0),
					'allow_preclose'	    => (!empty($sch_data['allow_preclose']) ? $sch_data['allow_preclose'] : 0),
					'preclose_months'	    => (!empty($sch_data['preclose_months']) ? $sch_data['preclose_months'] : 0),
					'preclose_benefits'	    => (!empty($sch_data['preclose_benefits']) ? $sch_data['preclose_benefits'] : 0),
					'interest'				=> (!empty($sch_data['interest']) ? $sch_data['interest'] : 0),
					'interest_by'			=> (!empty($sch_data['interest_by']) ? $sch_data['interest_by'] : 0),
					'interest_value'		=> number_format((isset($sch_data['interest_value']) ? $sch_data['interest_value'] : 0), 2, ".", ""),
					'interest_weight'		=> number_format((isset($sch_data['interest_weight']) ? $sch_data['interest_weight'] : 0), 3, ".", ""),
					'total_interest'		=> number_format((isset($sch_data['total_interest']) ? $sch_data['total_interest'] : 0), 2, ".", ""),
					'tax'					=> (isset($sch_data['tax']) ? $sch_data['tax'] : 0),
					'tax_by'				=> (isset($sch_data['tax_by']) ? $sch_data['tax_by'] : 0),
					'tax_value'				=> number_format((isset($sch_data['tax_value']) ? $sch_data['tax_value'] : 0), 2, ".", ""),
					'total_tax'				=> number_format((isset($sch_data['total_tax']) ? $sch_data['total_tax'] : 0), 2, ".", ""),
					'description'			=> (isset($sch_data['description']) ? $sch_data['description'] : NULL),
					'is_pan_required'		=> (isset($sch_data['is_pan_required']) ? $sch_data['is_pan_required'] : 0),
					'pan_req_amt'		    => (isset($sch_data['pan_req_amt']) && $sch_data['pan_req_amt'] != '' ? $sch_data['pan_req_amt'] : NULL),
					/*'fix_weight'			=>(isset($sch_data['fix_weight'])?$sch_data['fix_weight']:0),*/
					'setlmnt_type'			=> (isset($sch_data['type']) ? $sch_data['type'] : 3),
					'setlmnt_adjust_by'		=> (isset($sch_data['adjust_by']) ? $sch_data['adjust_by'] : 2),
					'free_payment'		=> (isset($sch_data['free_payment']) ? $sch_data['free_payment'] : 0),
					'allowSecondPay'		=> (isset($sch_data['allowSecondPay']) ? $sch_data['allowSecondPay'] : 0),
					'approvalReqForFP'		=> (isset($sch_data['approvalReqForFP']) ? $sch_data['approvalReqForFP'] : 0),
					'free_payInstallments'	=> (isset($sch_data['free_payInstallments']) ? $sch_data['free_payInstallments'] : NULL),
					'has_free_ins'	=> (isset($sch_data['has_free_ins']) ? $sch_data['has_free_ins'] : 0),
					'active'				=> (isset($sch_data['active']) ? $sch_data['active'] : 0),
					'visible'				=> (isset($sch_data['visible']) ? $sch_data['visible'] : 0),
					'disable_sch_payment'	 => (isset($sch_data['disable_sch_payment']) ? $sch_data['disable_sch_payment'] : 0),
					'stop_payment_installment'		  => (isset($sch_data['stop_payment_installment']) ? $sch_data['stop_payment_installment'] : 0),
					'firstPayDisc_value'	=> number_format((isset($sch_data['firstPayDisc_value']) ? $sch_data['firstPayDisc_value'] : 0), 2, ".", ""),
					'firstPayDisc'			=> (!empty($sch_data['firstPayDisc']) ? $sch_data['firstPayDisc'] : 0),
					'firstPayDisc_by'		=> (!empty($sch_data['firstPayDisc_by']) ? $sch_data['firstPayDisc_by'] : 0),
					'all_pay_disc'			=> (!empty($sch_data['all_pay_disc']) ? $sch_data['all_pay_disc'] : 0),
					'allpay_disc_value'	    => number_format((!empty($sch_data['allpay_disc_value']) ? $sch_data['allpay_disc_value'] : 0), 2, ".", ""),
					'allpay_disc_by'		=> (isset($sch_data['allpay_disc_by']) ? $sch_data['allpay_disc_by'] : 0),
					'charge_head'			=> (!empty($sch_data['charge_head']) ? $sch_data['charge_head'] : NULL),
					'charge_type'			=> (!empty($sch_data['charge_type']) ? $sch_data['charge_type'] : 0),
					'charge'				=> (!empty($sch_data['charge']) ? $sch_data['charge'] : 0.00),
					'gst_type'				=> (!empty($sch_data['gst_type']) ? $sch_data['gst_type'] : 0),
					'hsn_code'				=> (!empty($sch_data['hsn_code']) ? $sch_data['hsn_code'] : NULL),
					'date_add'				=> date("Y-m-d H:i:s"),
					'cus_ref_values'		=> (!empty($sch_data['cus_ref_values']) ? $sch_data['cus_ref_values'] : 0),
					'cus_refferal_value'	=> (isset($sch_data['cus_refferal']) ? (($sch_data['cus_refferal_by'] == 0 && ($sch_data['scheme_type'] == 0 || $sch_data['scheme_type'] == 2)) ? $sch_data['amount'] * $sch_data['cus_ref_values'] / 100 : $sch_data['cus_ref_values']) : 0),
					'emp_refferal_value'	=> (isset($sch_data['emp_refferal']) ? (($sch_data['emp_refferal_by'] == 0 && ($sch_data['scheme_type'] == 0 || $sch_data['scheme_type'] == 2)) ? $sch_data['amount'] * $sch_data['Emp_ref_values'] / 100 : $sch_data['Emp_ref_values']) : 0),
					'Emp_ref_values'		=> (!empty($sch_data['Emp_ref_values']) ? $sch_data['Emp_ref_values'] : 0),
					'cus_refferal_by'		=> (!empty($sch_data['cus_refferal_by']) ? $sch_data['cus_refferal_by'] : 0),
					'emp_refferal_by'		=> (!empty($sch_data['emp_refferal_by']) ? $sch_data['emp_refferal_by'] : 0),
					'cus_refferal'			=> (!empty($sch_data['cus_refferal']) ? $sch_data['cus_refferal'] : 0),
					'maturity_days'			=> (isset($sch_data['maturity_days']) ? ($sch_data['maturity_days'] != '' ? $sch_data['maturity_days'] : NULL) : NULL),
					'emp_refferal'			=> (!empty($sch_data['emp_refferal']) ? $sch_data['emp_refferal'] : 0),
					'ref_benifitadd_ins_type' => (!empty($sch_data['ref_benifitadd_ins_type']) ? $sch_data['ref_benifitadd_ins_type'] : 0),
					'ref_benifitadd_ins'	=> (!empty($sch_data['ref_benifitadd_ins']) ? $sch_data['ref_benifitadd_ins'] : 0),
					'discount_type'	        => (!empty($sch_data['discount_type']) ? $sch_data['discount_type'] : 0),
					'discount_installment'	=> (isset($sch_data['discount_installment']) && $sch_data['discount_installment'] > 0 ? $sch_data['discount_installment'] : NULL),
					'discount'		        => (!empty($sch_data['discount']) ? $sch_data['discount'] : 0),
					'otp_price_fixing'		=> (!empty($sch_data['otp_price_fixing']) ? $sch_data['otp_price_fixing'] : 0),
					'otp_price_fix_type'	=> (!empty($sch_data['otp_price_fix_type']) ? $sch_data['otp_price_fix_type'] : 1),
					'one_time_premium'		=> (!empty($sch_data['one_time_premium']) ? $sch_data['one_time_premium'] : 0),
					'flexible_sch_type'		=> (!empty($sch_data['flexible_sch_type']) ? $sch_data['flexible_sch_type'] : NULL),
					'avg_calc_ins'		  	=> (isset($sch_data['avg_calc_ins']) ? ($sch_data['avg_calc_ins'] != '' ? $sch_data['avg_calc_ins'] : NULL) : NULL),
					'apply_benefit_min_ins'	=> (isset($sch_data['apply_benefit_min_ins']) ? ($sch_data['apply_benefit_min_ins'] != '' ? $sch_data['apply_benefit_min_ins'] : NULL) : NULL),
					//	'firstPayamt_as_payamt'	 => (isset($sch_data['firstPayamt_as_payamt'])?$sch_data['firstPayamt_as_payamt']:0),
					//	'firstPayamt_maxpayable'	 => (isset($sch_data['firstPayamt_maxpayable'])?$sch_data['firstPayamt_maxpayable']:0),
					//  'get_amt_in_schjoin'	 => (isset($sch_data['get_amt_in_schjoin'])?$sch_data['get_amt_in_schjoin']:0),
					'firstPayamt_as_payamt'	 => ($sch_data['get_amt_in_schjoin'] == 1 && isset($sch_data['firstPayamt_as_payamt']) && $sch_data['firstPayamt_as_payamt'] == 1 ? $sch_data['firstPayamt_as_payamt'] : 0),
					'firstPayamt_maxpayable'	 => ($sch_data['get_amt_in_schjoin'] == 1 && isset($sch_data['firstPayamt_maxpayable']) && $sch_data['firstPayamt_maxpayable'] == 1 ? $sch_data['firstPayamt_maxpayable'] : 0),
					'get_amt_in_schjoin'	 => (isset($sch_data['get_amt_in_schjoin']) && $sch_data['get_amt_in_schjoin'] == 1 && ($sch_data['firstPayamt_maxpayable'] != 0 || $sch_data['firstPayamt_as_payamt'] != 0) ? $sch_data['get_amt_in_schjoin'] : 0),
					'flx_denomintion'				=> number_format((isset($sch_data['flx_denomintion']) ? $sch_data['flx_denomintion'] : 0), 2, ".", ""),
					'is_lucky_draw'    	 => (isset($sch_data['is_lucky_draw']) ? $sch_data['is_lucky_draw'] : 0),
					'max_members'	=> (isset($sch_data['max_members']) ? $sch_data['max_members'] : NULL),
					'has_prize'    	 => (isset($sch_data['has_prize']) ? $sch_data['has_prize'] : 0),
					'apply_benefit_by_chart'				=> (isset($sch_data['apply_benefit_by_chart']) ? $sch_data['apply_benefit_by_chart'] : 0),
					'apply_debit_on_preclose'				=> (isset($sch_data['apply_debit_on_preclose']) ? $sch_data['apply_debit_on_preclose'] : 0),
					'has_gift'				=> (isset($sch_data['has_gift']) ? $sch_data['has_gift'] : 0),
					'is_enquiry'				=> (isset($sch_data['is_enquiry']) ? $sch_data['is_enquiry'] : 0),
					'closing_maturity_days' => (isset($sch_data['closing_maturity_days']) ? ($sch_data['closing_maturity_days'] > 0 ? $sch_data['closing_maturity_days'] : NULL) : NULL),
					'auto_debit_plan_type'    	  => (isset($sch_data['auto_debit_plan_type']) ? $sch_data['auto_debit_plan_type'] : 0),
					'set_as_min_from'		  	=> (isset($sch_data['set_as_min_from']) ? ($sch_data['set_as_min_from'] != '' ? $sch_data['set_as_min_from'] : NULL) : NULL),
					'set_as_max_from'		  	=> (isset($sch_data['set_as_max_from']) ? ($sch_data['set_as_max_from'] != '' ? $sch_data['set_as_max_from'] : NULL) : NULL),
					'interest_type'	        => (isset($sch_data['interest_type']) ? $sch_data['interest_type'] : NULL),
					'interest_ins'	        => (isset($sch_data['interest_ins']) ? $sch_data['interest_ins'] : NULL),
					'rate_fix_by'		  => (isset($sch_data['rate_fix_by']) ? $sch_data['rate_fix_by'] : NULL),
					'rate_select'		  => (isset($sch_data['rate_select']) ? $sch_data['rate_select'] : 0),
					'is_aadhaar_required'		=> (isset($sch_data['is_aadhaar_required']) ? $sch_data['is_aadhaar_required'] : 0),
					'aadhaar_required_amt'		    => (isset($sch_data['aadhaar_required_amt']) && $sch_data['aadhaar_required_amt'] != '' ? $sch_data['aadhaar_required_amt'] : NULL),
					'is_nominee_required'    => (isset($sch_data['is_nominee_required']) ? $sch_data['is_nominee_required'] : 0),
					'max_total_installments'		    => (isset($sch_data['max_total_installments']) ? $sch_data['max_total_installments'] : NULL),
					'agent_refferal'				=> (isset($sch_data['agent_refferal']) ? $sch_data['agent_refferal'] : 0),
					'agent_credit_type'				=> (isset($sch_data['agent_credit_type']) ? $sch_data['agent_credit_type'] : 1),    // agent benefit
					'min_installments'		    => (isset($sch_data['min_installments']) ? $sch_data['min_installments'] : NULL),
					'no_of_dues'		    => (isset($sch_data['no_of_dues']) && $sch_data['no_of_dues'] != '' ? $sch_data['no_of_dues'] : NULL),
					'emp_deduct_ins'		    => (isset($sch_data['emp_deduct_ins']) && $sch_data['emp_deduct_ins'] != '' ? $sch_data['emp_deduct_ins'] : 0),
					'agent_deduct_ins'		    => (isset($sch_data['agent_deduct_ins']) && $sch_data['agent_deduct_ins'] != '' ? $sch_data['agent_deduct_ins'] : 0),
					'cus_deduct_ins'		    => (isset($sch_data['cus_deduct_ins']) && $sch_data['cus_deduct_ins'] != '' ? $sch_data['cus_deduct_ins'] : 0),
					'show_ins_type'				=> (isset($sch_data['show_ins_type']) && $sch_data['show_ins_type'] != '' ? $sch_data['show_ins_type'] : 1),
					'wgt_store_as'				=> (isset($sch_data['wgt_store_as']) && $sch_data['wgt_store_as'] != '' ? $sch_data['wgt_store_as'] : 0),
					'disable_pay'               => (isset($sch_data['disable_pay']) && $sch_data['disable_pay'] != '' ? $sch_data['disable_pay'] : 0),
					'disable_pay_amt'               => (isset($sch_data['disable_pay_amt']) ? $sch_data['disable_pay_amt'] : NULL),
					//DGS-DCNM			
					'daily_pay_limit'				=> (isset($sch_data['daily_pay_limit']) && $sch_data['is_digi'] == 1 ? $sch_data['daily_pay_limit'] : NULL),
					'restrict_payment'				=> (isset($sch_data['restrict_payment']) && $sch_data['is_digi'] == 1 ? $sch_data['restrict_payment'] : 0),
					'chit_detail_days'				=> (isset($sch_data['chit_detail_days']) && $sch_data['is_digi'] == 1 ? $sch_data['chit_detail_days'] : NULL),
					'total_days_to_pay'				=> (isset($sch_data['total_days_to_pay']) && $sch_data['is_digi'] == 1 ? $sch_data['total_days_to_pay'] : NULL),
					'is_digi'						=> (isset($sch_data['is_digi']) && $sch_data['is_digi'] == 1 ? $sch_data['is_digi'] : 0),
					//DGS-DCNM	
					'amt_based_on'						=> (isset($sch_data['amt_based_on']) ? $sch_data['amt_based_on'] : 0),
					'has_voucher'						=> (isset($sch_data['has_voucher']) ? $sch_data['has_voucher'] : 0),
					'has_gift'						=> (isset($sch_data['has_gift']) ? $sch_data['has_gift'] : 0),
					//chinannan wgt sch
					'firstPayment_as_wgt'	 => (isset($sch_data['firstPayment_as_wgt']) && $sch_data['firstPayment_as_wgt'] == 1 ? $sch_data['firstPayment_as_wgt'] : 0),
					//RHR scheme (akshaya)
					'installment_cycle'						=> (isset($sch_data['installment_cycle']) ? $sch_data['installment_cycle'] : 0),
					'ins_days_duration'						=> (isset($sch_data['ins_days_duration']) && $sch_data['installment_cycle'] == 2 ? $sch_data['ins_days_duration'] : NULL),
					'grace_days'						=> (isset($sch_data['grace_days']) ? $sch_data['grace_days'] : NULL),
					'calculation_type'						=> (isset($sch_data['calculation_type']) ? $sch_data['calculation_type'] : 1),
					'amt_restrict_by'						=> (isset($sch_data['amt_restrict_by']) ? $sch_data['amt_restrict_by'] : 1),
					//TKV
					'allow_general_advance'				=> (isset($sch_data['allow_general_advance']) ? $sch_data['allow_general_advance'] : 0),
					'adv_min_amt'						=> empty($sch_data['adv_min_amt']) ? 0 : $sch_data['adv_min_amt'],
					'adv_max_amt'						=> empty($sch_data['adv_max_amt']) ? 0 : $sch_data['adv_max_amt'],
					'adv_denomination'					=> empty($sch_data['adv_denomination']) ? 0 : $sch_data['adv_denomination'],
					'apply_adv_benefit'					=> empty($sch_data['apply_adv_benefit']) ? 0 : $sch_data['apply_adv_benefit'],
					'emp_incentive_closing'				=> empty($sch_data['emp_incentive_closing']) ? 0 : $sch_data['emp_incentive_closing'],
					'emp_incentive_closing'						=> (!empty($sch_data['emp_incentive_closing']) ? $sch_data['emp_incentive_closing'] : 0),
					'avg_calc_by'				=> (!empty($sch_data['avg_calc_by']) ? $sch_data['avg_calc_by'] : 0),
					'sch_approval'			=> (!empty($sch_data['sch_approval']) ? $sch_data['sch_approval'] : 0),
					'is_lumpSum'            => (!empty($sch_data['is_lumpSum']) ? $sch_data['is_lumpSum'] : 0),
					'joinTime_weight_slabs'  => (!empty($sch_data['joinTime_weight_slabs']) ? implode(',', $sch_data['joinTime_weight_slabs']) : NULL),
					'store_closing_balance'           => (!empty($sch_data['store_closing_balance']) ? $sch_data['store_closing_balance'] : 0),
					'allow_advance_in'  => ($sch_data['allow_advance_in'] != '' ? $allowAdvanceInStr : NULL),
					'allow_unpaid_in'  => ($sch_data['allow_unpaid_in'] != '' ? $allowUnpaidInStr : NULL),
					//RAHUL 02_09_25
					'display_payable'						=> (isset($sch_data['display_payable']) ? $sch_data['display_payable'] : 0),
					//  For top up scheme settings
					'is_topup_scheme' => (isset($sch_data['is_topup_scheme']) ? $sch_data['is_topup_scheme'] : 0),
					'topup_booking_type' => (isset($sch_data['topup_booking_type']) && $sch_data['topup_booking_type'] != '' ? $sch_data['topup_booking_type'] : 1),
					'topup_min_value' => (isset($sch_data['topup_min_value']) && $sch_data['topup_min_value'] != '' ? $sch_data['topup_min_value'] : 0),
					'topup_max_value' => (isset($sch_data['topup_max_value']) && $sch_data['topup_max_value'] != '' ? $sch_data['topup_max_value'] : 0),
					'topup_denomination' => (isset($sch_data['topup_denomination']) && $sch_data['topup_denomination'] != '' ? $sch_data['topup_denomination'] : 0),
					'topup_store_slab'		=> (isset($sch_data['topup_store_slab']) ? $sch_data['topup_store_slab'] : 1),
					'key_benifits_description'		=> (isset($sch_data['key_benifits_description']) ? $sch_data['key_benifits_description'] : 1),
					//RAHUL 24_11_2025
					'preclose_type'		=> (isset($sch_data['preclose_type']) ? $sch_data['preclose_type'] : 1),
				);
				$this->db->trans_begin();
				$res = $this->$model->insert_scheme($sch_info);
				//echo $this->db->last_query(); echo $this->db->_error_message();
				if (isset($_FILES['sch']['name']['edit_sch_img'])) {
					if ($res['id_scheme'] > 0) {
						$result = $this->set_scheme_image($res['id_scheme']);
					}
				}
				if ($res['status']) {
					// Branch-wise scheme
					if ($this->session->userdata('branch_settings') == 1 && $branch_data != '') {
						$branch_list       = implode(",", $branch_data);
						$branch_id         = array(explode(',', $branch_list));
						foreach ($branch_id[0] as $branch) {
							$data = array(
								'id_scheme'	  => (isset($res['id_scheme']) ? $res['id_scheme'] : NULL),
								'id_branch'	  => (isset($branch) ? $branch : NULL),
								'scheme_active' => 1,
								'date_add'		=> date("Y-m-d H:i:s")
							);
							$status = $this->$model->scheme_branch($data);
						}
					}
					// GST Split-up Detail
					foreach ($gst_data as $data) {
						$gstData = array(
							'splitup_name'  => (isset($data['splitup_name']) ? $data['splitup_name'] : NULL),
							'percentage'	  => (isset($data['percentage']) ? $data['percentage'] : 0),
							'type'	      => ((isset($data['type']) && $data['type'] != '') ? $data['type'] : NULL),
							'id_scheme'	  => (isset($res['id_scheme']) ? $res['id_scheme'] : NULL)
						);
						$this->$model->insert_gstSplitup($gstData);
						if ($gstData['type'] == NULL) {
							$schGst['gst'] = $gstData['percentage'];
							$this->$model->update_scheme($schGst, $res['id_scheme']);
						}
					}
					// Interest deduction cart [ On pre-close ] 
					if ($sch_data['apply_benefit_by_chart'] == 1 && $sch_data['apply_debit_on_preclose'] == 0) {
						$postData = $_POST;
						foreach ($postData['installmentchart'] as $chartdata) {
							//RHR
							$insArrdata = array(
								'id_scheme'	            => (isset($res['id_scheme']) ? $res['id_scheme'] : NULL),
								'int_calc_on'           => (isset($chartdata['int_calc_on']) ? $chartdata['int_calc_on'] : 1),
								'installment_no'           => (isset($chartdata['installment_no']) ? $chartdata['installment_no'] : 0),
								'interest_by'           => (isset($chartdata['interest_by']) ? $chartdata['interest_by'] : 0),
								'installment_from'           => (isset($chartdata['installment_from']) ? $chartdata['installment_from'] : 0),
								'installment_to'           => (isset($chartdata['installment_to']) ? $chartdata['installment_to'] : 0),
								'interest_mode'           => (isset($chartdata['interest_mode']) ? $chartdata['interest_mode'] : 1),
								'interest_type'           => (isset($chartdata['interest_type']) ? $chartdata['interest_type'] : 0),
								'interest_value'           => (isset($chartdata['interest_value']) ? $chartdata['interest_value'] : 0),
								'created_by'            => $this->session->userdata('uid'),
								'interest_mode'         => 1,
								'date_add'              => date('Y-m-d H:i:s'),
								'commodity'           => (isset($chartdata['commodityIdForChart']) ? $chartdata['commodityIdForChart'] : 0),
							);
							$data = $this->$model->insertData($insArrdata, 'scheme_benefit_deduct_settings');
						}
					}
					$flexi_data =	$this->input->post("scheme_flexible");
					if ($flexi_data != '') {
						foreach ($flexi_data as $chartdata) {
							$flexiinsArrdata = array(
								'id_scheme'	            => (isset($res['id_scheme']) ? $res['id_scheme'] : NULL),
								'ins_from'				=> $chartdata['ins_from'],
								'ins_to'				=> $chartdata['ins_to'],
								'min_value'				=> $chartdata['min_value'],
								'max_value'				=> $chartdata['max_value'],
								'created_by'            => $this->session->userdata('uid'),
								'created_on'              => date('Y-m-d H:i:s'),
								'updated_by'            => $this->session->userdata('uid'),
								'updated_on'              => date('Y-m-d H:i:s'),
							);
							$data = $this->$model->insertData($flexiinsArrdata, 'scheme_flexi_settings');
						}
					}
					// Start of employee & agent incentive benefit 			
					if ($sch_data['emp_refferal'] == 1 || $sch_data['cus_refferal'] == 1 || $sch_data['agent_refferal'] == 1)    // 22-10
					{
						foreach ($_POST['incentive_chart'] as $chart_data) {
							foreach ($chart_data as $incData) {
								$insArrdata = array(
									'id_scheme'	    => (isset($res['id_scheme']) ? $res['id_scheme'] : NULL),
									'credit_to'	    => $incData['credit_to'],
									'credit_for'	=> $incData['credit_for'],
									'from_range' 	=> $incData['credit_from_range'],
									'to_range'		=> $incData['credit_to_range'],
									'credit_type'	=> $incData['credit_type'],
									'credit_value'	=> $incData['credit_value'],
									'date_add'      => date('Y-m-d H:i:s'),
									'date_upd'      => date('Y-m-d H:i:s'),
								);
								$data = $this->$model->insertData($insArrdata, 'scheme_incentive_settings');
							}
						}
					}
					// End of employee & agent incentive benefit 		
					// Closing Balance debit chart [ On pre-close ]
					if ($sch_data['apply_benefit_by_chart'] == 0 && $sch_data['apply_debit_on_preclose'] == 1) {
						$post_Data = $_POST;
						foreach ($post_Data['installmentpreclosechart'] as $preclosechartdata) {
							$insArrdata = array(
								'id_scheme'	            => (isset($res['id_scheme']) ? $res['id_scheme'] : NULL),
								'installment_from'	    => $preclosechartdata['installment_from'],
								'installment_to'		=> $preclosechartdata['installment_to'],
								'deduction_type' 	    => $preclosechartdata['deduction_type'],
								'deduction_value'		=> $preclosechartdata['deduction_value'],
								'created_by'            => $this->session->userdata('uid'),
								'date_add'              => date('Y-m-d H:i:s')
							);
							$data = $this->$model->insertData($insArrdata, 'scheme_debit_settings');
						}
					}
					// agent benefit
					if ($sch_data['agent_refferal'] == 1) {
						$post_Data = $_POST;
						foreach ($post_Data['agent_benefit_chart'] as $agent_benefit_chart_data) {
							$insArrdata = array(
								'id_scheme'	            => (isset($res['id_scheme']) ? $res['id_scheme'] : NULL),
								'installment_from'	    => $agent_benefit_chart_data['installment_from'],
								'installment_to'		=> $agent_benefit_chart_data['installment_to'],
								'benefit_type' 	    => $agent_benefit_chart_data['benefit_type'],
								'benefit_value'		=> $agent_benefit_chart_data['benefit_value'],
								'created_by'            => $this->session->userdata('uid'),
								'date_add'              => date('Y-m-d H:i:s')
							);
							$data = $this->$model->insertData($insArrdata, 'scheme_agent_benefit');
						}
					}
					//Employee Incentive
					if ($_POST['emp_refferal_incentive'] == 1) {
						$scheme_array = $_POST['installmentchart_closing'];
						foreach ($scheme_array as $value) {
							$scheme_data = array(
								'id_scheme'         => $id,
								'incentive_from'    => $value['incentive_from'],
								'incentive_to'      => $value['incentive_to'],
								'type'              => $value['type'],
								'value'             => $value['value'],
								'date_add'          => date('Y-m-d H:i:s')
							);
							$status = $this->$model->insertData($scheme_data, 'emp_closing_incentive');
						}
					}
					//tkv Chit General Advance settings (GA) block with separate benefit settings .... Dt Added : 06-11-2023, By: #AB
					if ($res && ($sch_data['apply_adv_benefit'] == 1)) {
						$this->$model->deleteData('id_scheme', $id, 'scheme_general_advance_benefit_settings');
						$postData = $_POST;
						//RHR
						foreach ($postData['adv_chart'] as $advdata) {
							$insArrdata = array(
								'id_scheme'	            => (isset($res['id_scheme']) ? $res['id_scheme'] : NULL),
								'interest_by'           => (isset($advdata['interest_by']) ? $advdata['interest_by'] : 0),
								'installment_from'           => (isset($advdata['installment_from']) ? $advdata['installment_from'] : 0),
								'installment_to'           => (isset($advdata['installment_to']) ? $advdata['installment_to'] : 0),
								'interest_type'           => (isset($advdata['interest_type']) ? $advdata['interest_type'] : 0),
								'interest_value'           => (isset($advdata['interest_value']) ? $advdata['interest_value'] : 0),
								'created_by'            => $this->session->userdata('uid'),
								'date_add'              => date('Y-m-d H:i:s')
							);
							//echo '<pre>';print_r($insArrdata);exit;
							$data = $this->$model->insertData($insArrdata, 'scheme_general_advance_benefit_settings');
						}
					}
					//Tkv - Chit GA ends
				}
				// Chart For Top up scheme insertion 
				if (!empty($topUpSchemeChart) && count(array_filter($topUpSchemeChart)) > 0 && $sch_data['is_topup_scheme'] == 1) {
					$topUpChartInsertData = [];
					$employeeId = $this->session->userdata('uid');
					if (!empty($topUpSchemeChart)) {
						foreach ($topUpSchemeChart as $row) {
							$topUpChartInsertData[] = [
								'id_scheme' => $res['id_scheme'],
								'payable_by' => $row['payable_by'],
								'range_from' => $row['range_from'],
								'range_to' => $row['range_to'],
								'payable_type' => $row['payable_type'],
								'payable_value' => $row['payable_value'],
								'created_by' => $employeeId,
								'created_on' => date('Y-m-d H:i:s'),
							];
						}
						// print_r($topUpChartInsertData);
						$this->db->insert_batch('scheme_custom_payable_settings', $topUpChartInsertData);
					}
					// exit;
				}
				//scheme log details
				$log_data = array(
					'id_log'     => $this->id_log,
					'event_date' => date("Y-m-d H:i:s"),
					'module'     => 'Scheme',
					'operation'  => 'Add',
					'record'     => $res['id_scheme'],
					'remark'     => 'Scheme added successfully',
					'form_data' => json_encode($sch_info)
				);
				// print_r($log_data );exit;
				$this->$log_model->log_detail('insert', '', $log_data);
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$this->session->set_flashdata('sch_info', array('message' => 'Scheme created successfully', 'class' => 'success', 'title' => 'Create Scheme'));
					redirect('scheme');
				} else {
					$this->db->trans_rollback();
					/*echo $this->db->last_query();
				     echo $this->db->_error_message();
				   	 echo 1;exit;*/
					$this->session->set_flashdata('sch_info', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Create Scheme'));
					redirect('scheme');
				}
				break;
			case 'Edit':
				$sch_data = $this->input->post("sch");
				$gst_data = $this->input->post("gst_data");
				$branch_data = $this->input->post("branch_data");
				$topUpSchemeChart = $this->input->post("topUpSchemeChart");
				$allowAdvanceInStr = implode(',', $sch_data['allow_advance_in']); // Converts the array to a comma-separated string
				$allowUnpaidInStr = implode(',', $sch_data['allow_unpaid_in']);
				// 	echo"<pre>"; print_r($sch_data);exit;
				$sch_info = array(
					'min_amount'			=> number_format((isset($sch_data['min_amount']) ? $sch_data['min_amount'] : 0), 2, ".", ""),
					'max_amount'			=> number_format((isset($sch_data['max_amount']) ? $sch_data['max_amount'] : 0), 2, ".", ""),
					'max_amt_chance'			=> number_format((isset($sch_data['max_amt_chance']) ? $sch_data['max_amt_chance'] : 0), 2, ".", ""),
					'min_amt_chance'			=> number_format((isset($sch_data['min_amt_chance']) ? $sch_data['min_amt_chance'] : 0), 2, ".", ""),
					'pay_duration'					=> (isset($sch_data['pay_duration']) ? $sch_data['pay_duration'] : NULL),
					'wgt_convert'					=> (isset($sch_data['wgt_convert']) ? $sch_data['wgt_convert'] : 2),
					'noti_msg'			=> (isset($sch_data['noti_msg']) ? $sch_data['noti_msg'] : NULL),
					'scheme_name'			=> (isset($sch_data['scheme_name']) ? $sch_data['scheme_name'] : NULL),
					'code'					=> (isset($sch_data['code']) ? $sch_data['code'] : NULL),
					'sync_scheme_code'		=> (isset($sch_data['sync_scheme_code']) ? $sch_data['sync_scheme_code'] : NULL),
					'id_metal'				=> (isset($sch_data['id_metal']) ? $sch_data['id_metal'] : 0),
					'id_purity'				=> (isset($sch_data['id_purity']) ? $sch_data['id_purity'] : NULL),
					'id_classification'		=> (isset($sch_data['id_classification']) ? $sch_data['id_classification'] : 0),
					'scheme_type'			=> (isset($sch_data['scheme_type']) ? $sch_data['scheme_type'] : 0),
					'noti_msg'			    => (isset($sch_data['noti_msg']) ? $sch_data['noti_msg'] : NULL),
					'amount'				=> number_format((isset($sch_data['amount']) ? $sch_data['amount'] : 0), 2, ".", ""),
					'total_installments'	=> (isset($sch_data['total_installments']) ? $sch_data['total_installments'] : 0),
					//05-12-2022
					'maturity_installment' => (isset($sch_data['maturity_installment']) ? $sch_data['maturity_installment'] : NULL),
					'payment_chances'		=> (isset($sch_data['payment_chances']) ? $sch_data['payment_chances'] : 0),
					'min_chance'			=> (isset($sch_data['min_chance']) ? $sch_data['min_chance'] : 0),
					'max_chance'			=> (isset($sch_data['max_chance']) ? $sch_data['max_chance'] : 0),
					//'min_chance'	        =>($sch_data['payment_chances']==0?1:(isset($sch_data['min_chance'])?$sch_data['min_chance']:0)),
					//'max_chance'	        =>($sch_data['payment_chances']==0?1:(isset($sch_data['max_chance'])?$sch_data['max_chance']:0)),
					'sch_limit_value'	    => (isset($sch_data['sch_limit_value']) ? $sch_data['sch_limit_value'] : NULL),
					'min_weight'			=> number_format((!empty($sch_data['min_weight']) ? $sch_data['min_weight'] : 0), 3, ".", ""),
					'max_weight'			=> number_format((!empty($sch_data['max_weight']) ? $sch_data['max_weight'] : 0), 3, ".", ""),
					'allow_unpaid'			=> (!empty($sch_data['allow_unpaid']) ? $sch_data['allow_unpaid'] : 0),
					'unpaid_months'			=> (!empty($sch_data['unpaid_months']) ? $sch_data['unpaid_months'] : 0),
					'unpaid_weight_limit'	=> (!empty($sch_data['unpaid_weight_limit']) ? $sch_data['unpaid_weight_limit'] : 0),
					'allow_advance'			=> (!empty($sch_data['allow_advance']) ? $sch_data['allow_advance'] : 0),
					'advance_months'		=> (!empty($sch_data['advance_months']) ? $sch_data['advance_months'] : 0),
					'advance_weight_limit'	=> (!empty($sch_data['advance_weight_limit']) ? $sch_data['advance_weight_limit'] : 0),
					'allow_preclose'	    => (!empty($sch_data['allow_preclose']) ? $sch_data['allow_preclose'] : 0),
					'preclose_months'	    => (!empty($sch_data['preclose_months']) ? $sch_data['preclose_months'] : 0),
					'preclose_benefits'	    => (!empty($sch_data['preclose_benefits']) ? $sch_data['preclose_benefits'] : 0),
					'interest'				=> (!empty($sch_data['interest']) ? $sch_data['interest'] : 0),
					'interest_by'			=> (!empty($sch_data['interest_by']) ? $sch_data['interest_by'] : 0),
					'interest_value'		=> number_format((isset($sch_data['interest_value']) ? $sch_data['interest_value'] : 0), 2, ".", ""),
					'interest_weight'		=> number_format((isset($sch_data['interest_weight']) ? $sch_data['interest_weight'] : 0), 3, ".", ""),
					'total_interest'		=> number_format((isset($sch_data['total_interest']) ? $sch_data['total_interest'] : 0), 2, ".", ""),
					'tax'					=> (isset($sch_data['tax']) ? $sch_data['tax'] : 0),
					'tax_by'				=> (!empty($sch_data['tax_by']) ? $sch_data['tax_by'] : 0),
					'tax_value'				=> number_format((!empty($sch_data['tax_value']) ? $sch_data['tax_value'] : 0), 2, ".", ""),
					'total_tax'				=> number_format((!empty($sch_data['total_tax']) ? $sch_data['total_tax'] : 0), 2, ".", ""),
					'description'			=> (!empty($sch_data['description']) ? $sch_data['description'] : NULL),
					'is_pan_required'		=> (!empty($sch_data['is_pan_required']) ? $sch_data['is_pan_required'] : 0),
					'pan_req_amt'		    => (isset($sch_data['pan_req_amt']) && $sch_data['pan_req_amt'] != '' ? $sch_data['pan_req_amt'] : NULL),
					/*'fix_weight'			=>(!empty($sch_data['fix_weight'])?$sch_data['fix_weight']:0),*/
					'setlmnt_type'			=> (!empty($sch_data['type']) ? $sch_data['type'] : 3),
					'setlmnt_adjust_by'		=> (!empty($sch_data['adjust_by']) ? $sch_data['adjust_by'] : 2),
					'free_payment'		    => (!empty($sch_data['free_payment']) ? $sch_data['free_payment'] : 0),
					'allowSecondPay'		=> (!empty($sch_data['allowSecondPay']) ? $sch_data['allowSecondPay'] : 0),
					'approvalReqForFP'		=> (!empty($sch_data['approvalReqForFP']) ? $sch_data['approvalReqForFP'] : 0),
					'free_payInstallments'	=> (!empty($sch_data['free_payInstallments']) ? $sch_data['free_payInstallments'] : NULL),
					'has_free_ins'	        => (!empty($sch_data['has_free_ins']) ? $sch_data['has_free_ins'] : 0),
					'active'				=> (!empty($sch_data['active']) ? $sch_data['active'] : 0),
					'visible'				=> (!empty($sch_data['visible']) ? $sch_data['visible'] : 0),
					'firstPayDisc_value'	=> number_format((isset($sch_data['firstPayDisc_value']) ? $sch_data['firstPayDisc_value'] : 0), 2, ".", ""),
					'firstPayDisc'			=> (!empty($sch_data['firstPayDisc']) ? $sch_data['firstPayDisc'] : 0),
					'firstPayDisc_by'		=> (!empty($sch_data['firstPayDisc_by']) ? $sch_data['firstPayDisc_by'] : 0),
					'all_pay_disc'			=> (!empty($sch_data['all_pay_disc']) ? $sch_data['all_pay_disc'] : 0),
					'allpay_disc_value'	    => number_format((!empty($sch_data['allpay_disc_value']) ? $sch_data['allpay_disc_value'] : 0), 2, ".", ""),
					'allpay_disc_by'		=> (isset($sch_data['allpay_disc_by']) ? $sch_data['allpay_disc_by'] : 0),
					'charge_head'			=> (!empty($sch_data['charge_head']) ? $sch_data['charge_head'] : NULL),
					'charge_type'			=> (!empty($sch_data['charge_type']) ? $sch_data['charge_type'] : 0),
					'charge'				=> (!empty($sch_data['charge']) ? $sch_data['charge'] : 0.00),
					'gst_type'				=> (!empty($sch_data['gst_type']) ? $sch_data['gst_type'] : 0),
					'hsn_code'				=> (!empty($sch_data['hsn_code']) ? $sch_data['hsn_code'] : NULL),
					'cus_ref_values'		=> (!empty($sch_data['cus_ref_values']) ? $sch_data['cus_ref_values'] : 0),
					'cus_refferal_value'	=> (isset($sch_data['cus_refferal']) ? (($sch_data['cus_refferal_by'] == 0 && ($sch_data['scheme_type'] == 0 || $sch_data['scheme_type'] == 2)) ? $sch_data['amount'] * $sch_data['cus_ref_values'] / 100 : $sch_data['cus_ref_values']) : 0),
					'emp_refferal_value'	=> (isset($sch_data['emp_refferal']) ? (($sch_data['emp_refferal_by'] == 0 && ($sch_data['scheme_type'] == 0 || $sch_data['scheme_type'] == 2)) ? $sch_data['amount'] * $sch_data['Emp_ref_values'] / 100 : $sch_data['Emp_ref_values']) : 0),
					'cus_refferal_by'		=> (isset($sch_data['cus_refferal_by']) ? $sch_data['cus_refferal_by'] : 0),
					'emp_refferal_by'		=> (isset($sch_data['emp_refferal_by']) ? $sch_data['emp_refferal_by'] : 0),
					'cus_refferal'			=> (isset($sch_data['cus_refferal']) ? $sch_data['cus_refferal'] : 0),
					'emp_refferal'			=> (isset($sch_data['emp_refferal']) ? $sch_data['emp_refferal'] : 0),
					'date_upd'				=> date("Y-m-d H:i:s"),
					'ref_benifitadd_ins_type'	  => (isset($sch_data['ref_benifitadd_ins_type']) ? $sch_data['ref_benifitadd_ins_type'] : 0),
					'ref_benifitadd_ins'		  => (isset($sch_data['ref_benifitadd_ins']) ? $sch_data['ref_benifitadd_ins'] : 0),
					'discount_type'	        => (isset($sch_data['discount_type']) ? $sch_data['discount_type'] : 0),
					'discount_installment'	=> (isset($sch_data['discount_installment']) && $sch_data['discount_installment'] > 0 ? $sch_data['discount_installment'] : NULL),
					'discount'		         => (isset($sch_data['discount']) ? $sch_data['discount'] : 0),
					'otp_price_fixing'		  => (isset($sch_data['otp_price_fixing']) ? $sch_data['otp_price_fixing'] : 0),
					'otp_price_fix_type'		  => (isset($sch_data['otp_price_fix_type']) ? $sch_data['otp_price_fix_type'] : 1),
					'one_time_premium'		  => (isset($sch_data['one_time_premium']) ? $sch_data['one_time_premium'] : 0),
					'flexible_sch_type'		  => (isset($sch_data['flexible_sch_type']) ? $sch_data['flexible_sch_type'] : ''),
					//'maturity_days'				=>(isset($sch_data['maturity_days'])?$sch_data['maturity_days']:NULL),
					'maturity_days' => (isset($sch_data['maturity_days']) ? ($sch_data['maturity_days'] > 0 ? $sch_data['maturity_days'] : NULL) : NULL),
					'avg_calc_ins'		  	=> (isset($sch_data['avg_calc_ins']) ? ($sch_data['avg_calc_ins'] != '' ? $sch_data['avg_calc_ins'] : NULL) : NULL),
					'apply_benefit_min_ins'	=> (isset($sch_data['apply_benefit_min_ins']) ? ($sch_data['apply_benefit_min_ins'] != '' ? $sch_data['apply_benefit_min_ins'] : NULL) : NULL),
					//	'firstPayamt_as_payamt'	 => (isset($sch_data['firstPayamt_as_payamt'])?$sch_data['firstPayamt_as_payamt']:0),
					//	'firstPayamt_maxpayable'	 => (isset($sch_data['firstPayamt_maxpayable'])?$sch_data['firstPayamt_maxpayable']:0),
					'firstPayamt_as_payamt'	 => ($sch_data['get_amt_in_schjoin'] == 1 && isset($sch_data['firstPayamt_as_payamt']) && $sch_data['firstPayamt_as_payamt'] == 1 ? $sch_data['firstPayamt_as_payamt'] : 0),
					'firstPayamt_maxpayable'	 => ($sch_data['get_amt_in_schjoin'] == 1 && isset($sch_data['firstPayamt_maxpayable']) && $sch_data['firstPayamt_maxpayable'] == 1 ? $sch_data['firstPayamt_maxpayable'] : 0),
					'get_amt_in_schjoin'	 => (isset($sch_data['get_amt_in_schjoin']) && $sch_data['get_amt_in_schjoin'] == 1 && ($sch_data['firstPayamt_maxpayable'] != 0 || $sch_data['firstPayamt_as_payamt'] != 0) ? $sch_data['get_amt_in_schjoin'] : 0),
					'flx_denomintion'				=> number_format((isset($sch_data['flx_denomintion']) ? $sch_data['flx_denomintion'] : 0), 2, ".", ""),
					'is_lucky_draw'    	 => (isset($sch_data['is_lucky_draw']) ? $sch_data['is_lucky_draw'] : 0),
					'max_members'	=> (isset($sch_data['max_members']) ? $sch_data['max_members'] : NULL),
					'has_prize'    	 => (isset($sch_data['has_prize']) ? $sch_data['has_prize'] : 0),
					'apply_benefit_by_chart'				=> (isset($sch_data['apply_benefit_by_chart']) ? $sch_data['apply_benefit_by_chart'] : 0),
					'apply_debit_on_preclose'				=> (isset($sch_data['apply_debit_on_preclose']) ? $sch_data['apply_debit_on_preclose'] : 0),
					'has_gift'				=> (isset($sch_data['has_gift']) ? $sch_data['has_gift'] : 0),
					'is_enquiry'				=> (isset($sch_data['is_enquiry']) ? $sch_data['is_enquiry'] : 0),
					'closing_maturity_days' => (isset($sch_data['closing_maturity_days']) ? ($sch_data['closing_maturity_days'] > 0 ? $sch_data['closing_maturity_days'] : NULL) : NULL),
					'auto_debit_plan_type'    	  => (isset($sch_data['auto_debit_plan_type']) ? $sch_data['auto_debit_plan_type'] : 0),
					'set_as_min_from'		  	=> (isset($sch_data['set_as_min_from']) ? ($sch_data['set_as_min_from'] != '' ? $sch_data['set_as_min_from'] : NULL) : NULL),
					'set_as_max_from'		  	=> (isset($sch_data['set_as_max_from']) ? ($sch_data['set_as_max_from'] != '' ? $sch_data['set_as_max_from'] : NULL) : NULL),
					'interest_type'	        => (isset($sch_data['interest_type']) ? $sch_data['interest_type'] : NULL),
					'interest_ins'	        => (isset($sch_data['interest_ins']) ? $sch_data['interest_ins'] : NULL),
					'rate_fix_by'		  => (!empty($sch_data['rate_fix_by']) ? $sch_data['rate_fix_by'] : NULL),
					'rate_select'		  => (isset($sch_data['rate_select']) ? $sch_data['rate_select'] : 0),
					'is_aadhaar_required'		=> (isset($sch_data['is_aadhaar_required']) ? $sch_data['is_aadhaar_required'] : 0),
					'aadhaar_required_amt'		    => (isset($sch_data['aadhaar_required_amt']) && $sch_data['aadhaar_required_amt'] != '' ? $sch_data['aadhaar_required_amt'] : NULL),
					'is_nominee_required'    => (isset($sch_data['is_nominee_required']) ? $sch_data['is_nominee_required'] : 0),
					'max_total_installments'		    => (isset($sch_data['max_total_installments']) ? $sch_data['max_total_installments'] : NULL),
					'agent_refferal'				=> (isset($sch_data['agent_refferal']) ? $sch_data['agent_refferal'] : 0),
					'agent_credit_type'				=> (isset($sch_data['agent_credit_type']) ? $sch_data['agent_credit_type'] : 1),  // agent benefit
					'min_installments'		    => (isset($sch_data['min_installments']) ? $sch_data['min_installments'] : NULL),
					'no_of_dues'		    => (isset($sch_data['no_of_dues']) && $sch_data['no_of_dues'] != '' ? $sch_data['no_of_dues'] : NULL),
					'emp_deduct_ins'		    => (isset($sch_data['emp_deduct_ins']) && $sch_data['emp_deduct_ins'] != '' ? $sch_data['emp_deduct_ins'] : 0),
					'agent_deduct_ins'		    => (isset($sch_data['agent_deduct_ins']) && $sch_data['agent_deduct_ins'] != '' ? $sch_data['agent_deduct_ins'] : 0),
					'cus_deduct_ins'		    => (isset($sch_data['cus_deduct_ins']) && $sch_data['cus_deduct_ins'] != '' ? $sch_data['cus_deduct_ins'] : 0),
					'show_ins_type'				=> (isset($sch_data['show_ins_type']) && $sch_data['show_ins_type'] != '' ? $sch_data['show_ins_type'] : 1),
					'wgt_store_as'				=> (isset($sch_data['wgt_store_as']) && $sch_data['wgt_store_as'] != '' ? $sch_data['wgt_store_as'] : 0),
					'disable_pay'               => (isset($sch_data['disable_pay']) && $sch_data['disable_pay'] != '' ? $sch_data['disable_pay'] : 0),
					'disable_pay_amt'               => (isset($sch_data['disable_pay_amt']) ? $sch_data['disable_pay_amt'] : NULL),
					//DGS-DCNM			
					'daily_pay_limit'				=> (isset($sch_data['daily_pay_limit']) && $sch_data['is_digi'] == 1 ? $sch_data['daily_pay_limit'] : NULL),
					'restrict_payment'				=> (isset($sch_data['restrict_payment']) && $sch_data['is_digi'] == 1 ? $sch_data['restrict_payment'] : 0),
					'chit_detail_days'				=> (isset($sch_data['chit_detail_days']) && $sch_data['is_digi'] == 1 ? $sch_data['chit_detail_days'] : NULL),
					'total_days_to_pay'				=> (isset($sch_data['total_days_to_pay']) && $sch_data['is_digi'] == 1 ? $sch_data['total_days_to_pay'] : NULL),
					'is_digi'						=> (isset($sch_data['is_digi']) && $sch_data['is_digi'] == 1 ? $sch_data['is_digi'] : 0),
					//DGS-DCNM
					'amt_based_on'						=> (isset($sch_data['amt_based_on']) ? $sch_data['amt_based_on'] : 0),
					'has_voucher'						=> (isset($sch_data['has_voucher']) ? $sch_data['has_voucher'] : 0),
					'has_gift'						=> (isset($sch_data['has_gift']) ? $sch_data['has_gift'] : 0),
					//chinannan wgt sch
					'firstPayment_as_wgt'	 => (isset($sch_data['firstPayment_as_wgt']) && $sch_data['firstPayment_as_wgt'] == 1 ? $sch_data['firstPayment_as_wgt'] : 0),
					//RHR scheme (akshaya)
					'installment_cycle'						=> (isset($sch_data['installment_cycle']) ? $sch_data['installment_cycle'] : 0),
					'ins_days_duration'						=> (isset($sch_data['ins_days_duration']) && $sch_data['installment_cycle'] == 2 ? $sch_data['ins_days_duration'] : NULL),
					'grace_days'						=> (isset($sch_data['grace_days']) ? $sch_data['grace_days'] : NULL),
					'calculation_type'						=> (isset($sch_data['calculation_type']) ? $sch_data['calculation_type'] : 1),
					'amt_restrict_by'						=> (isset($sch_data['amt_restrict_by']) ? $sch_data['amt_restrict_by'] : 1),
					'maturity_type'	        => (isset($sch_data['maturity_type']) ? $sch_data['maturity_type'] : 1),
					//TKV
					'allow_general_advance'						=> (isset($sch_data['allow_general_advance']) ? $sch_data['allow_general_advance'] : 0),
					'adv_min_amt'						=> (isset($sch_data['adv_min_amt']) ? $sch_data['adv_min_amt'] : NULL),
					'adv_max_amt'						=> (isset($sch_data['adv_max_amt']) ? $sch_data['adv_max_amt'] : NULL),
					'adv_denomination'						=> (isset($sch_data['adv_denomination']) ? $sch_data['adv_denomination'] : null),
					'apply_adv_benefit'						=> (isset($sch_data['apply_adv_benefit']) ? $sch_data['apply_adv_benefit'] : 0),
					'emp_incentive_closing'						=> (isset($sch_data['emp_incentive_closing']) ? $sch_data['emp_incentive_closing'] : 0),
					'avg_calc_by'				=> (isset($sch_data['avg_calc_by']) ? $sch_data['avg_calc_by'] : 0),
					'sch_approval'			=> (isset($sch_data['sch_approval']) && ($sch_data['sch_approval'] != '' ? $sch_data['sch_approval'] : 0)),
					'is_lumpSum'            => (isset($sch_data['is_lumpSum']) && $sch_data['is_lumpSum'] != '' ? $sch_data['is_lumpSum'] : 0),
					'joinTime_weight_slabs'  => (isset($sch_data['joinTime_weight_slabs']) && $sch_data['joinTime_weight_slabs'] != '' ? implode(',', $sch_data['joinTime_weight_slabs']) : NULL),
					'store_closing_balance'           => (!empty($sch_data['store_closing_balance']) ? $sch_data['store_closing_balance'] : 0),
					'allow_advance_in'  => ($sch_data['allow_advance_in'] != '' ? $allowAdvanceInStr : NULL),
					'allow_unpaid_in'  => ($sch_data['allow_unpaid_in'] != '' ? $allowUnpaidInStr : NULL),
					//RAHUL 02_09_25
					'display_payable'						=> (isset($sch_data['display_payable']) ? $sch_data['display_payable'] : 0),
					// For Top up scheme
					'is_topup_scheme' => (isset($sch_data['is_topup_scheme']) ? $sch_data['is_topup_scheme'] : 0),
					'topup_booking_type' => (isset($sch_data['topup_booking_type']) && $sch_data['topup_booking_type'] != '' ? $sch_data['topup_booking_type'] : 1),
					'topup_min_value' => (isset($sch_data['topup_min_value']) && $sch_data['topup_min_value'] != '' ? $sch_data['topup_min_value'] : 0),
					'topup_max_value' => (isset($sch_data['topup_max_value']) && $sch_data['topup_max_value'] != '' ? $sch_data['topup_max_value'] : 0),
					'topup_denomination' => (isset($sch_data['topup_denomination']) && $sch_data['topup_denomination'] != '' ? $sch_data['topup_denomination'] : 0),
					'topup_store_slab'		=> (isset($sch_data['topup_store_slab']) ? $sch_data['topup_store_slab'] : 1),
					'key_benifits_description'		=> (isset($sch_data['key_benifits_description']) ? $sch_data['key_benifits_description'] : 1),
					//RAHUL 24_11_2025
					'preclose_type'		=> (isset($sch_data['preclose_type']) ? $sch_data['preclose_type'] : 1),
				);
				//print_r($sch_info);echo '<pre>';exit;
				$this->db->trans_begin();
				$res = $this->$model->update_scheme($sch_info, $id);
				if (isset($_FILES['sch']['name']['edit_sch_img'])) {
					if ($res['id_scheme'] > 0) {
						$result = $this->set_scheme_image($res['id_scheme']);
					}
				}
				// agent benefit
				if ($sch_data['agent_refferal'] == 1) {
					$post_Data = $_POST;
					$this->$model->delete_agent_benefit($id);
					foreach ($post_Data['agent_benefit_chart'] as $agent_benefit_chart_data) {
						$insArrdata = array(
							'id_scheme'	            => (isset($res['id_scheme']) ? $res['id_scheme'] : NULL),
							'installment_from'	    => $agent_benefit_chart_data['installment_from'],
							'installment_to'		=> $agent_benefit_chart_data['installment_to'],
							'benefit_type' 	    => $agent_benefit_chart_data['benefit_type'],
							'benefit_value'		=> $agent_benefit_chart_data['benefit_value'],
							'created_by'            => $this->session->userdata('uid'),
							'date_add'              => date('Y-m-d H:i:s')
						);
						$data = $this->$model->insertData($insArrdata, 'scheme_agent_benefit');
					}
				}
				// Start of employee & agent incentive benefit 
				if ($sch_data['emp_refferal'] == 1 || $sch_data['cus_refferal'] == 1 || $sch_data['agent_refferal'] == 1)   // 22-10
				{
					$this->$model->delete_incentive_benefit($id);
					foreach ($_POST['incentive_chart'] as $chart_data) {
						foreach ($chart_data as $incData) {
							$insArrdata = array(
								'id_scheme'	  => (isset($id) ? $id : NULL),
								'credit_to'	    => $incData['credit_to'],
								'credit_for'	=> $incData['credit_for'],
								'from_range' 	=> $incData['credit_from_range'],
								'to_range'		=> $incData['credit_to_range'],
								'credit_type'	=> $incData['credit_type'],
								'credit_value'	=> $incData['credit_value'],
								'date_add'      => date('Y-m-d H:i:s'),
								'date_upd'      => date('Y-m-d H:i:s'),
							);
							$data = $this->$model->insertData($insArrdata, 'scheme_incentive_settings');
						}
					}
				}
				// End of employee & agent incentive benefit 			
				if (($this->session->userdata('branch_settings') == 1 && $branch_data != '' && $res)) {
					$branch_list       = implode(",", $branch_data);
					$branch_id         = array(explode(',', $branch_list));
					$this->$model->delete_scheme_branch($id);
					foreach ($branch_id[0] as $branch) {
						if ($branch != '') {
							$data = array(
								'id_scheme'	  => (isset($id) ? $id : NULL),
								'id_branch'	  => (!empty($branch) ? $branch : NULL),
								'scheme_active' => 1,
								'date_add'		=> date("Y-m-d H:i:s")
							);
							$status = $this->$model->scheme_branch($data);
						}
					}
				}
				if ($res && (isset($sch_data['update_gst']) ? $sch_data['update_gst'] == 1 : TRUE)) {
					$result = $this->$model->update_gstSplitup($id);
					if ($result) {
						foreach ($gst_data as $data) {
							$gstData = array(
								'splitup_name'  => (isset($data['splitup_name']) ? $data['splitup_name'] : NULL),
								'percentage'	  => (isset($data['percentage']) ? $data['percentage'] : 0),
								'type'	      => ((isset($data['type']) && $data['type'] != '') ? $data['type'] : NULL),
								'id_scheme'	  => $id
							);
							$this->$model->insert_gstSplitup($gstData);
							if ($gstData['type'] == NULL) {
								$schGst['gst'] = $gstData['percentage'];
								$this->$model->update_scheme($schGst, $id);
							}
						}
					}
				}
				// Closing Balance debit chart [ On pre-close ]
				if ($res && ($sch_data['apply_benefit_by_chart'] == 1)) {
					$this->$model->deleteData('id_scheme', $id, 'scheme_benefit_deduct_settings');
					$postData = $_POST;
					foreach ($postData['installmentchart'] as $chartdata) {
						//RHR
						$insArrdata = array(
							'id_scheme'	            => (isset($res['id_scheme']) ? $res['id_scheme'] : NULL),
							'int_calc_on'           => (isset($chartdata['int_calc_on']) ? $chartdata['int_calc_on'] : 1),
							'installment_no'           => (isset($chartdata['installment_no']) ? $chartdata['installment_no'] : 0),
							'interest_by'           => (isset($chartdata['interest_by']) ? $chartdata['interest_by'] : 0),
							'installment_from'           => (isset($chartdata['installment_from']) ? $chartdata['installment_from'] : 0),
							'installment_to'           => (isset($chartdata['installment_to']) ? $chartdata['installment_to'] : 0),
							'interest_mode'           => (isset($chartdata['interest_mode']) ? $chartdata['interest_mode'] : 1),
							'interest_type'           => (isset($chartdata['interest_type']) ? $chartdata['interest_type'] : 0),
							'interest_value'           => (isset($chartdata['interest_value']) ? $chartdata['interest_value'] : 0),
							'created_by'            => $this->session->userdata('uid'),
							'interest_mode'         => 1,
							'date_add'              => date('Y-m-d H:i:s'),
							'commodity'           => (isset($chartdata['commodityIdForChart']) ? $chartdata['commodityIdForChart'] : 0),
						);
						$data = $this->$model->insertData($insArrdata, 'scheme_benefit_deduct_settings');
					}
				}
				$flexi_data =	$this->input->post("scheme_flexible");
				if ($flexi_data != '') {
					$this->$model->deleteData('id_scheme', $id, 'scheme_flexi_settings');
					foreach ($flexi_data as $chartdata) {
						$flexiinsArrdata = array(
							'id_scheme'	            => $id,
							'ins_from'				=> $chartdata['ins_from'],
							'ins_to'				=> $chartdata['ins_to'],
							'min_value'				=> $chartdata['min_value'],
							'max_value'				=> $chartdata['max_value'],
							'created_by'            => $this->session->userdata('uid'),
							'created_on'              => date('Y-m-d H:i:s'),
							'updated_by'            => $this->session->userdata('uid'),
							'updated_on'              => date('Y-m-d H:i:s')
						);
						$data = $this->$model->insertData($flexiinsArrdata, 'scheme_flexi_settings');
					}
				}
				// Closing Balance debit chart [ On pre-close ]
				if ($sch_data['apply_debit_on_preclose'] == 1) {
					$post_Data = $_POST;
					$this->$model->delete_benfit_rdeduct_preclose($id);
					foreach ($post_Data['installmentpreclosechart'] as $preclosechartdata) {
						$insArrdata = array(
							'id_scheme'	            => (isset($res['id_scheme']) ? $res['id_scheme'] : NULL),
							'installment_from'	    => $preclosechartdata['installment_from'],
							'installment_to'		=> $preclosechartdata['installment_to'],
							'deduction_type' 	    => $preclosechartdata['deduction_type'],
							'deduction_value'		=> $preclosechartdata['deduction_value'],
							'created_by'           => $this->session->userdata('uid'),
							'date_add'              => date('Y-m-d H:i:s')
						);
						$data = $this->$model->insertData($insArrdata, 'scheme_debit_settings');
					}
				}
				if ($sch_data['emp_incentive_closing'] == 1) {
					$this->$model->deleteData('id_scheme', $id, 'emp_closing_incentive');
					$scheme_array = $_POST['installmentchart_closing'];
					if (sizeof($scheme_array) > 0) {
						foreach ($scheme_array as $value) {
							$scheme_data = array(
								'id_scheme'         => $id,
								'incentive_from'    => $value['incentive_from'],
								'incentive_to'      => $value['incentive_to'],
								'type'              => $value['type'],
								'value'             => $value['value'],
								'date_add'          => date('Y-m-d H:i:s')
							);
							$status = $this->$model->insertData($scheme_data, 'emp_closing_incentive');
						}
					}
				}
				//tkv Chit General Advance settings (GA) block with separate benefit settings .... Dt Added : 06-11-2023, By: #AB	
				if ($res && ($sch_data['apply_adv_benefit'] == 1)) {
					$this->$model->deleteData('id_scheme', $id, 'scheme_general_advance_benefit_settings');
					$postData = $_POST;
					//RHR
					foreach ($postData['adv_chart'] as $advdata) {
						$insArrdata = array(
							'id_scheme'	            => (isset($res['id_scheme']) ? $res['id_scheme'] : NULL),
							'interest_by'           => (isset($advdata['interest_by']) ? $advdata['interest_by'] : 0),
							'installment_from'           => (isset($advdata['installment_from']) ? $advdata['installment_from'] : 0),
							'installment_to'           => (isset($advdata['installment_to']) ? $advdata['installment_to'] : 0),
							'interest_type'           => (isset($advdata['interest_type']) ? $advdata['interest_type'] : 0),
							'interest_value'           => (isset($advdata['interest_value']) ? $advdata['interest_value'] : 0),
							'created_by'            => $this->session->userdata('uid'),
							'date_add'              => date('Y-m-d H:i:s')
						);
						//		echo '<pre>';print_r($insArrdata);exit;
						$data = $this->$model->insertData($insArrdata, 'scheme_general_advance_benefit_settings');
					}
				}
				//Tkv - Chit GA ends	
				// Top up scheme chart edit;
				if (!empty($topUpSchemeChart) && count(array_filter($topUpSchemeChart)) > 0 && $sch_data['is_topup_scheme'] == 1) {
					$chartInsertStatus = $this->$model->topUpSchemeChartEditProcess($id, $topUpSchemeChart);
					if ($chartInsertStatus) {
						$this->db->trans_commit();
					} else {
						$this->db->trans_rollback();
					}
				}
				//scheme log details
				$log_data = array(
					'id_log'     => $this->id_log,
					'event_date' => date("Y-m-d H:i:s"),
					'module'     => 'Scheme',
					'operation'  => 'Edit',
					'record'     => $id,
					'remark'     => 'Scheme edited successfully',
					'form_data' => json_encode($sch_info)
				);
				// print_r($log_data );exit;
				$this->$log_model->log_detail('insert', '', $log_data);
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$this->session->set_flashdata('sch_info', array('message' => 'Scheme modified successfully', 'class' => 'success', 'title' => 'Edit Scheme'));
					redirect('scheme');
				} else {
					$this->db->trans_rollback();
					$this->session->set_flashdata('sch_info', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Edit Scheme'));
				}
				break;
			case 'Delete':
				$acc = $this->$model->check_acc_records($id);
				$log_data = array(																//scheme log details
					'id_log'     => $this->id_log,
					'event_date' => date("Y-m-d H:i:s"),
					'module'     => 'Scheme',
					'operation'  => 'Delete',
					'record'     => $id,
					'remark'     => 'Scheme Deleted successfully'
				);
				// print_r($log_data );exit;
				$this->$log_model->log_detail('insert', '', $log_data);
				if ($acc == TRUE) {
					$this->session->set_flashdata('sch_info', array('message' => 'Unable to proceed your request, Check whether customers exist in this savings scheme.', 'class' => 'danger', 'title' => 'Delete Scheme'));
					redirect('scheme');
				} else {
					$this->db->trans_begin();
					$this->$model->delete_scheme($id);
					if ($this->db->trans_status() === TRUE) {
						$this->db->trans_commit();
						$this->session->set_flashdata('sch_info', array('message' => 'Scheme deleted successfully', 'class' => 'success', 'title' => 'Delete Scheme'));
						redirect('scheme');
					} else {
						$this->db->trans_rollback();
						$this->session->set_flashdata('sch_info', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Delete Scheme'));
					}
				}
				break;
		}
	}
	public function get_metals()
	{
		$model = self::SCH_MODEL;
		$metals = $this->$model->get_metals();
		echo json_encode($metals);
	}
	public function get_classifications()
	{
		$model = self::SCH_MODEL;
		$classifications = $this->$model->get_classifications();
		echo json_encode($classifications);
	}
	public function get_units()
	{
		$model = self::SCH_MODEL;
		$units = $this->$model->get_installment_amount();
		echo json_encode($units);
	}
	public function scheme_business($id)
	{
		$scheme = array();
		$model = self::SCH_MODEL;
		$pay_model = self::PAY_MODEL;
		$sch = $this->$model->get_scheme($id);
		//lines added by Durga 11.05.2023 starts here (Gopal Task)
		$scheme['get_amt_in_schjoin'] = $sch['get_amt_in_schjoin'];
		$scheme['firstPayamt_as_payamt'] = $sch['firstPayamt_as_payamt'];
		$scheme['firstPayamt_maxpayable'] = $sch['firstPayamt_maxpayable'];
		//lines added by Durga 11.05.2023 ends here (Gopal Task)
		$scheme['id_scheme']		= $sch['id_scheme'];
		$scheme['scheme_name']		= $sch['scheme_name'];
		$scheme['code']				= $sch['code'];
		$scheme['metal']			= $sch['metal'];
		$scheme['sch_approval']     = $sch['sch_approval'];
		$scheme['sch_limit_value']	= $sch['sch_limit_value'];
		$scheme['flx_denomintion']	= $sch['flx_denomintion'];
		$scheme['flexible_sch_type']	= $sch['flexible_sch_type'];
		$scheme['sch_type']	= $sch['sch_type'];
		$scheme['min_amount']	= $sch['min_amount'];
		$scheme['max_amount']	= $sch['max_amount'];
		$scheme['is_pan_required']	= $sch['is_pan_required'];
		$scheme['pan_req_amt']	= $sch['pan_req_amt'];
		$scheme['sch_joined_acc'] = $this->$model->get_scheme_count($scheme['id_scheme']);
		$scheme['sch_limit'] = $this->$model->sch_limit();
		$scheme['metal_rate'] = $this->$pay_model->get_metalrate_by_branch($sch['id_branch'], $sch['id_metal'], $sch['id_purity']); //For branchwise rate
		$scheme['is_topup_scheme']	= $sch['is_topup_scheme'];
		$scheme['topup_booking_type']	= $sch['topup_booking_type'];
		$scheme['topup_min_value']	= $sch['topup_min_value'];
		$scheme['topup_max_value']	= $sch['topup_max_value'];
		$scheme['topup_denomination']	= $sch['topup_denomination'];
		if ($sch['scheme_type'] == 1) {
			$scheme['scheme_type']	= 'Weight';
			$scheme['min_weight']	= $sch['min_weight'];
			$scheme['max_weight']	= $sch['max_weight'];
		} else if ($sch['scheme_type'] == 2) {
			$scheme['scheme_type'] 	= 'Amount to Weight';
			$scheme['amount']	= $sch['amount'];
		} else if ($sch['scheme_type'] == 3) {
			$scheme['scheme_type'] 	= 'FLXIBLE_AMOUNT';
		} else {
			$scheme['scheme_type'] 	= 'Amount';
			$scheme['amount']	= $sch['amount'];
		}
		if ($sch['payment_chances'] == 0) {
			$scheme['payment_type'] = 'Single';
			$scheme['max_chance']	= 1;
		} else {
			$scheme['payment_type'] = 'Multiple';
			$scheme['min_chance']	= $sch['min_chance'];
			$scheme['max_chance']	= $sch['max_chance'];
		}
		$scheme['total_installments']	=  $sch['total_installments'];
		$amount = $sch['amount'] *  $sch['total_installments'];
		if (isset($sch['interest']) && $sch['interest'] == 1) {
			$scheme['interest'] = ($sch['interest_by'] == 1 ? $sch['interest_value'] : ($amount * ($sch['interest_value'] / 100)));
		}
		if (isset($sch['tax']) && $sch['interest'] == 1) {
			$scheme['tax']	= ($sch['tax'] == 1 ? $sch['tax_value'] : ($amount * ($sch['tax_value'] / 100)));
		}
		$scheme['description'] = $sch['description'];
		$scheme['has_voucher'] = $sch['has_voucher'];
		$scheme['has_gift'] = $sch['has_gift'];
		//lump scheme starts...
		$scheme['lumpwgt_slabs'] = [];
		$scheme['is_lumpSum'] = $sch['is_lumpSum'];   //0 - disabled , 1 - enabled
		if ($sch['is_lumpSum'] == 1) {
			$scheme['lumpwgt_slabs'] = $this->$model->get_joinTime_weight_slabs($sch['id_scheme']);
		}
		//lump scheme ends...
		return $scheme;
	}
	public function ajax_get_schemes()
	{
		$model = self::SCH_MODEL;
		$schemes = $this->$model->get_schemes();
		echo json_encode($schemes);
	}
	public function getSchemeTypeByID($id)
	{
		$model = self::SCH_MODEL;
		$schemes = $this->$model->get_schemes();
		echo json_encode($schemes);
	}
	public function ajax_get_scheme($id)
	{
		$scheme = $this->scheme_business($id);
		echo json_encode($scheme);
	}
	public function getFreeInsBySchId($id)
	{
		$scheme = $this->scheme_model->getFreeInsBySchId($id);
		echo json_encode($scheme);
	}
	public function get_branch_edit($id)
	{
		//
		$scheme = $this->scheme_model->get_branch_edit($id);
		/* $branch=implode(',',$scheme); */
		//echo "<pre>";print_r($scheme);echo "</pre>";exit;
		echo json_encode($scheme);
	}
	// to add gst splitup data for existing schemes
	public function gstsplitupinsert()
	{
		$scheme = $this->scheme_model->insetrgstsplitup();
		echo json_encode($scheme);
	}
	public function get_branches()
	{
		$model = self::SCH_MODEL;
		$branches = $this->$model->get_branches();
		echo json_encode($branches);
	}
	function set_scheme_image($id)
	{
		$data = array();
		$model = self::SCH_MODEL;
		if ($_FILES['sch']['name']['edit_sch_img']) {
			$path = 'assets/img/sch_image/';
			$file_name = $_FILES['sch']['name']['edit_sch_img'];
			if (!is_dir($path)) {
				mkdir($path, 0777, TRUE);
			} else {
				$file = $path . $file_name;
				chmod($path, 0777);
				unlink($file);
			}
			$img = $_FILES['sch']['tmp_name']['edit_sch_img'];
			$filename = $_FILES['sch']['name']['edit_sch_img'];
			$imgpath = 'assets/img/sch_image/' . $filename;
			$upload = $this->upload_img('logo', $imgpath, $img);
			$data['logo'] = $filename;
			$status = $this->$model->update_scheme($data, $id);
			return $status;
		}
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
			default:
				die("Unknown filetype");
				return false;
		}
		imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
		imagejpeg($tmp, $dst, 60);
	}
	function getActivePuritiesByMetal()
	{
		$model      = self::SCH_MODEL;
		$purities   = $this->$model->getActivePuritiesByMetal($_POST['id_metal']);
		echo json_encode($purities);
	}
	//lump scheme starts
	public function joinTime_weight_slabs()
	{
		$model = self::ACC_MODEL;
		$id_scheme = $_POST['id_scheme'];
		$data = $this->$model->get_joinTime_weight_slabs($id_scheme);
		echo json_encode($data);
	}
	//lump scheme ends
	function get_weight_list()
	{
		$scheme_modal = self::SCH_MODEL;
		$records = $this->$scheme_modal->get_weight_list($_POST['wgt_min'], $_POST['wgt_max']);
		echo json_encode($records);
	}

	public function checkDigiAvailability()
	{
		$model = self::SCH_MODEL;
		$id_metal = $this->input->get('id_metal');
		// $id_scheme = $this->input->post('id_scheme'); // for edit
		$result = $this->$model->enableDigiGold('', $id_metal);
		echo json_encode($result);
	}
}
