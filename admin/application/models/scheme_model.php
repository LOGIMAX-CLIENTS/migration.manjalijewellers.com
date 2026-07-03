<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Scheme_model extends CI_Model
{
    const SCH_TABLE = 'scheme';
    const SCH_BENEFIT_TABLE = 'scheme_benefit_deduct_settings';
    const SCH_BENEFIT_DEDUCT__TABLE = 'scheme_debit_settings';
    const BRANCH_TABLE = 'scheme_branch';
    const SCH_AGENT_BENEFIT__TABLE = 'scheme_agent_benefit';             // agent benefit
    function __construct()
    {
        parent::__construct();
    }
    function get_metals()
    {
        $this->db->select('id_metal,metal');
        $metals = $this->db->get('metal');
        $data[] = array('id' => "", 'name' => "-- Select --");
        foreach ($metals->result() as $metal) {
            $data[] = array(
                'id' => $metal->id_metal,
                'name' => $metal->metal
            );
        }
        return $data;
    }
    function get_chit_metals()
    {
        $this->db->select('id_metal, metal, display_name');
        $this->db->where('show_chit', 1);
        $this->db->where('metal_status', 1);
        $metals = $this->db->get('metal');
        return $metals->result_array();
    }
    // public function insertData($data,$table)
    // {
    // 	$insert_flag = 0;
    // 	$insert_flag = $this->db->insert($table,$data);
    //     return ($insert_flag == 1 ? $this->db->insert_id(): 0);
    // }
    public function deleteData($id_field, $id_value, $table)
    {
        $this->db->where($id_field, $id_value);
        $status = $this->db->delete($table);
        return $status;
    }
    function get_classifications()
    {
        $this->db->select('*');
        $this->db->where('active', '1');
        $classifications = $this->db->get('sch_classify');
        /*$data[]=array('id'=>0,'name'=>"-- Select --");*/
        foreach ($classifications->result() as $classification) {
            $data[] = array(
                'id' => $classification->id_classification,
                'name' => $classification->classification_name
            );
        }
        return $data;
    }
    public function get_all_schemes()
    {
        $this->db->select('id_scheme,scheme_name,code,scheme_type,amount,max_weight,payment_chances,flx_denomintion,min_amount,max_amount,total_installments,stop_payment_installment,interest,description,active,date_add,agent_refferal,agent_credit_type,allow_advance,advance_months');
        $data = $this->db->get(self::SCH_TABLE);
        return $data->result_array();
    }
    /*	public function get_schemes()
        {
            $this->db->select('id_scheme,scheme_name as name,is_pan_required,active,flexible_sch_type,one_time_premium');
            $this->db->where('active','1');
            $this->db->where('visible','1');
            if($this->session->userdata('uid')!=1)
            {
                $this->db->where('active','1');
            }
            $data=$this->db->get(self::SCH_TABLE);
            //print_r($this->db->last_query());exit;
            return $data->result_array();
        }*/
    // scheme join page - scheme list showed active/visible based //HH
    public function get_schemes()
    {
        /*if($this->session->userdata('uid')!=1){
           $sql ="select id_scheme,scheme_name as name,is_pan_required,pan_req_amt,is_nominee_required,set_as_min_from,set_as_max_from,disable_sch_payment,get_amt_in_schjoin,firstPayamt_as_payamt,firstPayamt_maxpayable,active,total_installments,maturity_type,has_gift,is_nominee_required
         From scheme where active = 1  AND (visible = 1 OR visible = 2)";
        }
        else{
            $sql ="select id_scheme,scheme_name as name,is_pan_required,pan_req_amt,is_nominee_required,disable_sch_payment,get_amt_in_schjoin,firstPayamt_as_payamt,firstPayamt_maxpayable,active,total_installments,maturity_type,has_gift,is_nominee_required
         From scheme where active = 1 AND (visible = 1 OR visible = 2)";
        }  */
        $branch_settings = $this->session->userdata('branch_settings');
        $is_branchwise_cus_reg = $this->session->userdata('is_branchwise_cus_reg');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $branchwise_scheme = $this->session->userdata('branchwise_scheme');
        $customerId = $this->input->get('customerId');
        //RAHUL 03_12_25
        $selected_branch = $this->input->get('id_branch');
        if (empty($branch) && !empty($selected_branch) && $branchwise_scheme == 1) {
            $branch =  $selected_branch;
        }
        if ($this->session->userdata('uid') == 1) {
            if (!empty($customerId)) {
                $sql = "select ifnull(s.amount,0) as amount,s.id_scheme,s.scheme_name as name,s.is_pan_required,ifnull(s.pan_req_amt,0) as pan_req_amt,s.is_nominee_required,s.set_as_min_from,s.set_as_max_from,s.disable_sch_payment,s.get_amt_in_schjoin,s.firstPayamt_as_payamt,s.firstPayamt_maxpayable,s.active,s.total_installments,s.maturity_type,s.has_gift,s.is_nominee_required,s.scheme_type 
				 From scheme s	
                 " . ($branch_settings == 1 && $branchwise_scheme == 1 ? 'LEFT JOIN scheme_branch sb on (sb.id_scheme = s.id_scheme) LEFT JOIN branch b on (b.id_branch = sb.id_branch)' : '') . "
				WHERE 
					-- s.is_digi = 0 AND 
					s.active = 1  AND (s.visible = 1 OR s.visible = 2)
                    " . ($branch_settings == 1 && $branchwise_scheme == 1 && $branch != 0 && $branch != '' ? "and sb.id_branch=" . $branch . "" : '') . " 
					AND s.id_scheme NOT IN (
						SELECT sa.id_scheme
						FROM scheme_account sa
						JOIN scheme s1 ON sa.id_scheme = s1.id_scheme
						WHERE sa.id_customer =" . $customerId . " AND s1.is_digi = 1 AND sa.active=1 AND sa.is_closed=0
					) group by s.id_scheme";
            } else {
                $sql = "select ifnull(s.amount,0) as amount,s.id_scheme,s.scheme_name as name,s.is_pan_required,ifnull(s.pan_req_amt,0) as pan_req_amt,s.is_nominee_required,s.set_as_min_from,s.set_as_max_from,s.disable_sch_payment,s.get_amt_in_schjoin,s.firstPayamt_as_payamt,s.firstPayamt_maxpayable,s.active,s.total_installments,s.maturity_type,s.has_gift,s.is_nominee_required,s.scheme_type 
		 From scheme s
         " . ($branch_settings == 1 && $branchwise_scheme == 1 ? 'LEFT JOIN scheme_branch sb on (sb.id_scheme = s.id_scheme) LEFT JOIN branch b on (b.id_branch = sb.id_branch)' : '') . "
		 where s.active = 1  AND (visible = 1 OR visible = 2) " . ($branch_settings == 1 && $branchwise_scheme == 1 && $branch != 0 && $branch != '' ? "and sb.id_branch=" . $branch . "" : '') . " 
         group by s.id_scheme ";
            }
        } else {
            if (!empty($customerId)) {
                $sql = "select ifnull(s.amount,0) as amount,s.id_scheme,s.scheme_name as name,s.is_pan_required,ifnull(s.pan_req_amt,0) as pan_req_amt,s.is_nominee_required,s.set_as_min_from,s.set_as_max_from,s.disable_sch_payment,s.get_amt_in_schjoin,s.firstPayamt_as_payamt,s.firstPayamt_maxpayable,s.active,s.total_installments,s.maturity_type,s.has_gift,s.is_nominee_required,s.scheme_type 
				 From scheme s	
                 " . ($branch_settings == 1 && $branchwise_scheme == 1 ? 'LEFT JOIN scheme_branch sb on (sb.id_scheme = s.id_scheme) LEFT JOIN branch b on (b.id_branch = sb.id_branch)' : '') . "
				WHERE 
					-- s.is_digi = 0 AND 
					s.active = 1  AND (s.visible = 1 OR s.visible = 2)
                    " . ($branch_settings == 1 && $branchwise_scheme == 1 && $branch != 0 && $branch != '' ? "and sb.id_branch=" . $branch . "" : '') . " 
					AND s.id_scheme NOT IN (
						SELECT sa.id_scheme
						FROM scheme_account sa
						JOIN scheme s1 ON sa.id_scheme = s1.id_scheme
						WHERE sa.id_customer =" . $customerId . " AND s1.is_digi = 1 AND sa.active=1 AND sa.is_closed=0
					) group by s.id_scheme";
            } else {
                $sql = "select ifnull(s.amount,0) as amount,s.id_scheme,s.scheme_name as name,s.is_pan_required,ifnull(s.pan_req_amt,0) as pan_req_amt,s.is_nominee_required,s.disable_sch_payment,s.get_amt_in_schjoin,s.firstPayamt_as_payamt,s.firstPayamt_maxpayable,s.active,s.total_installments,s.maturity_type,s.has_gift,s.is_nominee_required,s.scheme_type 
		 From scheme s
		  " . ($branch_settings == 1 && $branchwise_scheme == 1 ? 'LEFT JOIN scheme_branch sb on (sb.id_scheme = s.id_scheme) LEFT JOIN branch b on (b.id_branch = sb.id_branch)' : '') . "
		 where s.active = 1 AND (visible = 1 OR visible = 2) " . ($branch_settings == 1 && $branchwise_scheme == 1 && $branch != 0 && $branch != '' ? "and sb.id_branch=" . $branch . "" : '') . "  group by s.id_scheme ";
            }
        }
        //print_r($sql);exit;
        $data = $this->db->query($sql);
        return $data->result_array();
    }
    function get_chitsettings()
    {
        $sql = "SELECT * from  chit_settings";
        $result = $this->db->query($sql)->row_array();
        return $result;
    }
    public function get_schemes_type($id)
    {
        $this->db->select('id_scheme,scheme_type,scheme_name as name');
        $this->db->where('id_scheme', $id);
        $this->db->where('active', '1');
        $data = $this->db->get(self::SCH_TABLE);
        return $data->result_array();
    }
    public function get_scheme($id)
    {
        //DGS-DCNM --> s.daily_pay_limit,s.restrict_payment,s.chit_detail_days,s.total_days_to_pay,is_digi
        $sql = "SELECT s.is_lumpSum,
	   s.apply_adv_benefit,s.adv_denomination,s.adv_max_amt,s.adv_min_amt,s.allow_general_advance,s.avg_calc_by,s.sch_approval,
	   s.calculation_type,s.installment_cycle,s.grace_days,s.ins_days_duration,s.firstPayment_as_wgt,s.id_purity,s.amt_restrict_by,
	   s.has_gift,s.has_voucher,s.amt_based_on,s.is_partial_payment,s.daily_pay_limit,s.restrict_payment,s.chit_detail_days,s.total_days_to_pay,s.is_digi,s.apply_interest_by,
	   s.disable_pay,s.disable_pay_amt,s.logo,sb.id_branch,s.gst_type,s.gst,s.hsn_code, s.id_scheme, s.scheme_name, 	s.code,s.wgt_convert,sync_scheme_code,s.ref_benifitadd_ins_type, s.ref_benifitadd_ins,
		m.metal, s.id_metal,s.cus_ref_values,s. Emp_ref_values,s.min_amount,s.max_amount,max_amt_chance,min_amt_chance,pay_duration,allowSecondPay,approvalReqForFP,
		s.scheme_type,s.charge_head,s.charge,s.charge_type,s.cus_refferal,s.cus_refferal_by,sbds.installment_from,sbds.installment_to,sbds.interest_type,sbds.interest_value,sds.installment_from,sds.installment_to,sds.deduction_type,sds.deduction_value,
		s.cus_refferal_value,s.emp_refferal_value,s.emp_refferal_by,s.emp_refferal, s.id_classification, sc.classification_name,s.interest_weight, s.amount,s.wgt_store_as,
		s.total_installments,s.payment_chances, s.min_chance, s.max_chance,s.min_weight,s.allow_unpaid,s.unpaid_months,s.unpaid_weight_limit, s.allow_advance,
		s.advance_months,s.advance_weight_limit, s.allow_preclose,s.preclose_months,s.preclose_benefits, s.max_weight,s.interest, s.interest_by,s.active,s.has_gift,
		s.visible, s.interest_value, s.total_interest, s.tax,s.tax_by,s.tax_value,s.scheme_type as sch_type,s.disable_sch_payment,s.stop_payment_installment,cs.branchwise_scheme,
		s.total_tax,s.is_pan_required,s.description,s.auto_debit_plan_type, s.is_enquiry,
		s.setlmnt_type as type, s.setlmnt_adjust_by as adjust_by, s.free_payment,s.free_payInstallments,s.has_free_ins,s.firstPayDisc,s.firstPayamt_as_payamt,s.firstPayamt_maxpayable,s.get_amt_in_schjoin,maturity_type,maturity_installment,s.is_lucky_draw,s.max_members,s.has_prize,s.apply_benefit_by_chart,s.apply_debit_on_preclose,
		s.firstPayDisc_by,s.firstPayDisc_value,s.sch_limit_value,s.all_pay_disc,s.allpay_disc_by,s.allpay_disc_value,s.discount_installment,s.discount_type,s.discount,otp_price_fixing,otp_price_fix_type,one_time_premium,flexible_sch_type,maturity_days,closing_maturity_days,flx_denomintion,flexible_sch_type,
		noti_msg,s.interest_type,s.rate_fix_by,s.rate_select,s.is_aadhaar_required,s.aadhaar_required_amt,s.is_nominee_required,s.agent_refferal,s.agent_credit_type,s.min_installments,s.no_of_dues,s.emp_deduct_ins,s.agent_deduct_ins,s.cus_deduct_ins,s.show_ins_type,s.set_as_min_from,s.set_as_max_from,s.pan_req_amt,
		s.emp_incentive_closing,s.closing_incentive_based_on,is_lumpSum,joinTime_weight_slabs,s.store_closing_balance,s.allow_advance_in, s.allow_unpaid_in, s.total_days_to_pay,
        s.is_topup_scheme,s.topup_booking_type,s.topup_min_value,s.topup_max_value,s.topup_denomination, s.topup_store_slab,s.display_payable, s.key_benifits_description
        FROM (scheme s)
		LEFT JOIN metal m ON s.id_metal=m.id_metal
		left join scheme_branch sb on s.id_scheme=sb.id_scheme
		left join scheme_benefit_deduct_settings sbds on sbds.id_scheme=s.id_scheme
		left join scheme_debit_settings sds on sds.id_scheme=s.id_scheme
		LEFT JOIN sch_classify sc ON s.id_classification=sc.id_classification 
		JOIN chit_settings cs
		WHERE s.id_scheme =$id";
        $data = $this->db->query($sql);
        $resdata = $data->row_array();
        $allowadvanceValues = $resdata['allow_advance_in']; // Comma-separated string from the database
        $resdata['allow_advance_in'] = explode(',', $allowadvanceValues); // Convert to an array
        $allowunpaidValues = $resdata['allow_unpaid_in']; // Comma-separated string from the database
        $resdata['allow_unpaid_in'] = explode(',', $allowunpaidValues); // Convert to an array
        //print_r($this->db->last_query());exit;
        return $resdata;
    }
    public function get_scheme1($id)
    {
        $sql = $this->db->query("SELECT sb.id_branch as id_branch FROM scheme s
LEFT JOIN metal m ON s.id_metal=m.id_metal
left join scheme_branch sb on s.id_scheme=sb.id_scheme
LEFT JOIN sch_classify sc ON s.id_classification=sc.id_classification WHERE s.id_scheme =$id");
        $id_branch = array_map(function ($value) {
            return $value['id_branch'];
        }, $sql->result_array());
        return $id_branch;
    }
    public function get_scheme_active($id)
    {
        $sql = "SELECT
				    s.amt_based_on,s.id_scheme, s.scheme_name, s.code, m.metal, s.id_metal, s.scheme_type, s.min_amount,s.max_amount,s.set_as_min_from,s.set_as_max_from,
				    s.amount, s.total_installments,s.payment_chances,s.allow_unpaid,s.unpaid_months,s.allow_advance,s.advance_months,s.advance_weight_limit,
				    s.allow_preclose,s.preclose_months,s.preclose_benefits,s.flx_denomintion,s.emp_deduct_ins,s.agent_deduct_ins,s.cus_deduct_ins,
				     s.min_chance,s.max_chance,s.min_weight, s.max_weight,s.interest,s.interest_by,s.active,s.firstPayamt_as_payamt,s.is_lucky_draw,s.max_members,s.has_prize,s.apply_benefit_by_chart,s.apply_debit_on_preclose,s.firstPayamt_maxpayable,s.get_amt_in_schjoin,
				    s.interest_value, s.total_interest, s.tax,s.tax_by,s.tax_value, s.disable_sch_payment,s.stop_payment_installment,
				    s.total_tax,s.is_pan_required,s.description,pan_req_amt,s.is_aadhaar_required,s.aadhaar_required_amt,s.is_nominee_required,s.agent_refferal,s.agent_credit_type,s.min_installments,s.no_of_dues
				FROM (scheme s)
				JOIN metal m ON s.id_metal=m.id_metal
				WHERE `id_scheme` =" . $id . "  
				AND `active` =  1";
        $data = $this->db->query($sql);
        return $data->row_array();
    }
    public function get_scheme_id($scheme_code)
    {
        $this->db->select('id_scheme');
        $this->db->where('code', $scheme_code);
        $id_scheme = $this->db->get(self::SCH_TABLE);
        return ($id_scheme->num_rows == 1 ? $id_scheme->row()->id_scheme : 0);
    }
    public function get_weight_scheme_id($type, $scheme_code)
    {
        $this->db->select('id_scheme');
        $this->db->where('scheme_type', $type);
        $this->db->where('code', $scheme_code);
        $id_scheme = $this->db->get(self::SCH_TABLE);
        return ($id_scheme->num_rows == 1 ? $id_scheme->row()->id_scheme : 0);
    }
    public function empty_record()
    {
        $data = array(
            'id_scheme' => 0,
            'scheme_name' => NULL,
            'code' => NULL,
            'id_metal' => 0,
            'id_purity' => NULL,
            'id_classification' => 1,
            'scheme_type' => 0,
            'amount' => 0.00,
            'total_installments' => NULL,
            'payment_chances' => 0,
            'min_chance' => 1,
            'allow_unpaid' => 0,
            'unpaid_months' => 0,
            'unpaid_weight_limit' => '0.000',
            'allow_advance' => 0,
            'advance_months' => 0,
            'advance_weight_limit' => '0.000',
            'allow_preclose' => 0,
            'preclose_months' => 0,
            'preclose_benefits' => 0,
            'max_chance' => 1,
            'min_weight' => '0',
            'max_weight' => '0',
            'avg_calc_ins' => NULL,
            'apply_benefit_min_ins' => NULL,
            'max_amt_chance' => NULL,
            'min_amt_chance' => NULL,
            'min_amount' => NULL,
            'max_amount' => NULL,
            'interest' => 0,
            'interest_by' => 0,
            'interest_value' => '0.00',
            'total_interest' => '0.00',
            'tax' => 0,
            'tax_by' => 0,
            'tax_value' => '0.00',
            'total_tax' => 0.00,
            'description' => NULL,
            'pay_duration' => 0,
            'is_pan_required' => 0,
            'pan_req_amt' => NULL,
            'active' => 1,
            'visible' => 1,
            'disable_sch_payment' => 0,
            'stop_payment_installment' => NULL,
            'type' => 1,
            'adjust_by' => 1,
            'rate' => '0.00',
            'charge_type' => '0',
            'charge' => '0.00',
            'firstPayDisc' => 0,
            'firstPayDisc_by' => 0,
            'firstPayDisc_value' => 0.00,
            'firstPayDisc_total' => 0.00,
            'all_pay_disc' => 0,
            'allpay_disc_by' => 0,
            'allpay_disc_value' => 0.00,
            'charge_head' => 'Convenience fees',
            'free_payInstallments' => NULL,
            'free_payment' => 0,
            'has_free_ins' => 0,
            'interest_weight' => '0.00',
            'gst_type' => 0,
            'gst' => 0,
            'hsn_code' => NULL,
            'cus_refferal' => 0,
            'cus_refferal_by' => 0,
            'cus_refferal_value' => 0,
            'emp_refferal_value' => 0,
            'emp_refferal_by' => 0,
            'Emp_ref_values' => NULL,
            'cus_ref_values' => NULL,
            'id_branch' => NULL,
            'pay_duration' => 0,
            'min_amt_chance' => NULL,
            'max_amt_chance' => NULL,
            'max_amt_chance' => NULL,
            'max_amount' => NULL,
            'min_amount' => NULL,
            'emp_refferal' => 0,
            'wgt_convert' => 2,
            'ref_benifitadd_ins_type' => 0,
            'ref_benifitadd_ins' => NULL,
            'sync_scheme_code' => NULL,
            'allowSecondPay' => 0,
            'approvalReqForFP' => 0,
            'discount_type' => 0,
            'discount_installment' => NULL,
            'discount' => 0,
            'otp_price_fixing' => 0,
            'otp_price_fix_type' => 1,
            'sch_limit' => $this->sch_limit(),
            'one_time_premium' => 0,
            'flexible_sch_type' => '',
            'noti_msg' => NULL,
            'avg_calc_ins' => NULL,
            'apply_benefit_min_ins' => NULL,
            'get_amt_in_schjoin' => 0,
            'firstPayamt_as_payamt' => NULL,
            'firstPayamt_payable' => NULL,
            'firstPayamt_maxpayable' => NULL,
            'flx_denomintion' => NULL,
            'maturity_type' => 1,
            'maturity_installment' => NULL,
            'is_lucky_draw' => 0,
            'max_members' => NULL,
            'has_prize' => 0,
            'apply_benefit_by_chart' => 0,
            'installment_from' => 0,
            'installment_to' => 0,
            'deduction_type' => 0,
            'deduction_value' => 0,
            'apply_debit_on_preclose' => 0,
            'has_gift' => 0,
            'is_enquiry' => 0,
            'auto_debit_plan_type' => 0,
            //	'branchwise_scheme'     => $chitSettings['branchwise_scheme']
            'branchwise_scheme' => $this->branchwise_scheme(),
            'interest_type' => NULL,
            'rate_fix_by' => 2,
            'rate_select' => 1,
            'is_aadhaar_required' => 0,
            'aadhaar_required_amt' => NULL,
            'is_nominee_required' => 0,
            'max_total_installments' => NULL,
            'agent_refferal' => 0,
            'agent_credit_type' => 1,
            'min_installments' => NULL,
            'no_of_dues' => NULL,
            'emp_deduct_ins' => NULL,
            'agent_deduct_ins' => NULL,
            'cus_deduct_ins' => NULL,
            'show_ins_type' => 1,
            'closing_maturity_days' => NULL,
            'disable_pay' => 0,
            'disable_pay_amt' => NULL,
            //DGS_DCNM	
            'daily_pay_limit' => NULL,
            'restrict_payment' => 0,
            'chit_detail_days' => NULL,
            'total_days_to_pay' => NULL,
            'is_digi' => 0,
            'is_partial_payment' => 0,
            'amt_based_on' => 0,
            'firstPayment_as_wgt' => 0,
            //DGS_DCNM
            //RHR scheme
            'grace_days' => null,
            'ins_days_duration' => null,
            'installment_cycle' => 0,
            'calculation_type' => 1,
            'amt_restrict_by' => 1,
            //TKV double bonus scheme
            'allow_general_advance' => 0,
            'adv_min_amt' => null,
            'adv_max_amt' => null,
            'adv_denomination' => null,
            'apply_adv_benefit' => 0,
        );
        return $data;
    }
    function sch_limit()
    {
        $sql = "Select c.sch_limit FROM chit_settings c where c.id_chit_settings = 1";
        return $this->db->query($sql)->row()->sch_limit;
    }
    function branchwise_scheme()
    {
        $sql = "Select branchwise_scheme FROM chit_settings c where c.id_chit_settings = 1";
        return $this->db->query($sql)->row()->branchwise_scheme;
    }
    function getChitSettings()
    {
        $sql = "Select c.sch_limit,branchwise_scheme FROM chit_settings c where c.id_chit_settings = 1";
        return $this->db->query($sql)->row_array();
    }
    /*-- Coded by ARVK --*/
    function scheme_count()
    {
        $sql = "SELECT id_scheme FROM scheme";
        return $this->db->query($sql)->num_rows();
    }
    /*-- / Coded by ARVK --*/
    /* public function insert_scheme($data)
    {
        $status=$this->db->insert(self::SCH_TABLE,$data);
        return array('status'=>$status,'id_scheme'=>$this->db->insert_id());
    }
     */
    /*   public function insert_scheme($data)
    {
        if($this->session->userdata('branch_settings')==1)
        {
        $sch_id=0;
        $insert_flag=0;
        $sch_info=$this->db->insert(self::SCH_TABLE,$data['info']);
        $sch_id=$this->db->insert_id();
        if($sch_info)
        {	
            $branch = $data['branch']['id_branch'];	
            $id_branch=explode(',',$branch);
            foreach($id_branch as $b)
            {
                $data['branch']['id_branch']=$b;
                $data['branch']['id_scheme']=$sch_id;
            $insert_flag=$this->db->insert(self::BRN_TABLE,$data['branch']);
            }
        }
                return array($insert_flag==1?$cus_id:0);
        }
        else
        {
            $status=$this->db->insert(self::SCH_TABLE,$data);
            return array('status'=>$status,'id_scheme'=>$this->db->insert_id());
        }
    } */
    //  public function insert_scheme($data)
    // {
    // 	$status=$this->db->insert(self::SCH_TABLE,$data);
    // 	return array('status'=>$status,'id_scheme'=>$this->db->insert_id());
    // }
    // public function update_scheme($data,$id)
    // {
    // 	$this->db->where('id_scheme',$id);  
    // 	$status=$this->db->update(self::SCH_TABLE,$data);
    // 	return array('status'=>$status,'id_scheme'=>$id);
    // }	
    public function insert_scheme($data)
    {
        $query = $this->db->query("SHOW COLUMNS FROM `" . self::SCH_TABLE . "`");
        $columns = $query->result_array();
        $default_values = [];
        foreach ($columns as $column) {
            if (!is_null($column['Default'])) {
                $default_values[$column['Field']] = $column['Default'];
            } else {
                // If no default value, use an empty string or null based on column nullability
                $default_values[$column['Field']] = $column['Null'] === 'YES' ? null : '';
            }
        }
        foreach ($data as $field => $value) {
            // If the value is empty, set it to the default value
            if ((empty($value) || $value == 'null')) {
                // $data[$field] = $default_values[$field];
                if ($value === 0 || $value === '0') {
                    $data[$field] = 0;
                } else {
                    $data[$field] = $default_values[$field];
                }
            }
        }
        $status = $this->db->insert(self::SCH_TABLE, $data);
        return array('status' => $status, 'id_scheme' => $this->db->insert_id());
    }
    public function update_scheme($data, $id)
    {
        $query = $this->db->query("SHOW COLUMNS FROM `" . self::SCH_TABLE . "`");
        $columns = $query->result_array();
        $default_values = [];
        foreach ($columns as $column) {
            if (!is_null($column['Default'])) {
                $default_values[$column['Field']] = $column['Default'];
            } else {
                // If no default value, use an empty string or null based on column nullability
                $default_values[$column['Field']] = $column['Null'] === 'YES' ? null : '';
            }
        }
        foreach ($data as $field => $value) {
            // If the value is empty, set it to the default value
            if ((empty($value) || $value == 'null')) {
                if ($value === 0 || $value === '0') {
                    $data[$field] = 0;
                } else {
                    $data[$field] = $default_values[$field];
                }
            }
        }
        $this->db->where('id_scheme', $id);
        $status = $this->db->update(self::SCH_TABLE, $data);
        return array('status' => $status, 'id_scheme' => $id);
    }
    public function update_scheme_free_payment($data)
    {
        $this->db->where('free_payment', 1);
        $status = $this->db->update(self::SCH_TABLE, $data);
        return $status;
    }
    public function delete_scheme($id)
    {
        $status = false;
        $this->db->where('id_scheme', $id);
        $child = $this->db->delete('gst_splitup_detail');
        if ($child) {
            $this->db->where('id_scheme', $id);
            $status = $this->db->delete(self::SCH_TABLE);
        }
        return $status;
    }
    function check_acc_records($id)
    {
        $sql = "select * from scheme_account where  id_scheme = " . $id;
        $status = $this->db->query($sql);
        if ($status->num_rows() > 0) {
            return TRUE;
        }
    }
    function get_fixweight_schemes()/* -- edited by ARVK -- */
    {
        $sql = "SELECT
				    s.id_scheme, s.scheme_name, s.code, m.metal, s.id_metal, s.scheme_type,s.flx_denomintion,
				    s.amount, s.total_installments,s.payment_chances, s.min_chance,s.min_amount,s.max_amount,
				    s.max_chance,s.min_weight,s.allow_unpaid,s.unpaid_months,s.unpaid_weight_limit,
				    s.allow_advance,s.advance_months,s.advance_weight_limit,
				    s.allow_preclose,s.preclose_months,s.preclose_benefits,
				    s.max_weight,s.interest, s.interest_by,s.active,
				    s.interest_value, s.total_interest, s.tax,s.tax_by,s.tax_value,
				    s.total_tax,s.is_pan_required,s.description ,count(sa.id_scheme_account) as accounts,
				    count(p.id_payment) as payments,
            if(s.setlmnt_type=1,'Monthly','Purchase')as type,s.setlmnt_type,
            if(s.setlmnt_adjust_by=1,'Highest rate',if(s.setlmnt_adjust_by=2,'Lowest rate',
            if(s.setlmnt_adjust_by=3,'Average rate','Manual')))as adjust_by,s.setlmnt_adjust_by,
            if(s.setlmnt_adjust_by=1,(SELECT max(mr.goldrate_22ct) from metal_rates mr
                                      where month(mr.updatetime) = month(curdate()) and year(mr.updatetime) = year(curdate())),
            if(s.setlmnt_adjust_by=2,(SELECT min(mr.goldrate_22ct) from metal_rates mr
                                      where month(mr.updatetime) = month(curdate()) and year(mr.updatetime) = year(curdate())),
            if(s.setlmnt_adjust_by=3,(SELECT Round(Avg(mr.goldrate_22ct),2) from metal_rates mr
                                      where month(mr.updatetime) = month(curdate()) and year(mr.updatetime) = year(curdate())),'0.00'))) as rate,s.agent_refferal,s.agent_credit_type
            FROM scheme s
			LEFT JOIN metal m ON (s.id_metal=m.id_metal)
			LEFT JOIN scheme_account sa ON (s.id_scheme=sa.id_scheme and sa.active=1)
			LEFT JOIN payment p ON (sa.id_scheme_account=p.id_scheme_account and p.fix_weight=0 and p.payment_status=1)
			WHERE s.active=1 AND s.scheme_type=2 and s.setlmnt_type!=3
			GROUP BY s.id_scheme";
        return $this->db->query($sql)->result_array();
    }
    function getFreeInsBySchId($id)
    {
        $sql = $this->db->query("select free_payInstallments,discount_installment,stop_payment_installment,ref_benifitadd_ins,set_as_min_from,set_as_max_from,avg_calc_ins,interest_ins from scheme where  id_scheme='$id'");
        return $sql->row_array();
    }
    function get_branch_edit($id)
    {
        $sql = $this->db->query("select * from scheme_branch sb left join scheme s on(sb.id_scheme=s.id_scheme) where s.id_scheme='$id'");
        $id_branch = array_map(function ($value) {
            return $value['id_branch'];
        }, $sql->result_array());
        return $id_branch;
        /* return $sql->result_array(); */
    }
    /* function get_branch_edit_mrate($id){
        $sql = $this->db->query("select * from scheme_branch sb left join scheme s on(sb.id_scheme=s.id_scheme) where s.id_scheme='$id'");
                 $id_branch = array_map(function ($value) {
        return  $value['id_branch'];
        }, $sql->result_array()); 
return $id_branch; */
    /* return $sql->result_array(); */
    public function get_gstSplitupData($id)
    {
        $sql = "SELECT * FROM gst_splitup_detail WHERE status=1 and `id_scheme` =" . $id;
        $data = $this->db->query($sql);
        return $data->result_array();
    }
    public function insert_gstSplitup($data)
    {
        $status = $this->db->insert('gst_splitup_detail', $data);
        return $status;
    }
    public function update_gstSplitup($id)
    {
        $data['status'] = 0;
        $this->db->where('id_scheme', $id);
        $status = $this->db->update('gst_splitup_detail', $data);
        //echo $this->db->last_query();exit;
        return $status;
    }
    // to add gst splitup data for existing schemes
    public function insetrgstsplitup()
    {
        $sql = "SELECT id_scheme FROM scheme s";
        $count = 0;
        $res = $this->db->query($sql);
        foreach ($res->result_array() as $row) {
            ++$count;
            $insQuery = $this->db->query("INSERT INTO `gst_splitup_detail` (`id_gst_splitup`, `id_scheme`, `splitup_name`, `percentage`, `status`, `type`, `effective_date`, `date_upd`) VALUES (NULL, " . $row['id_scheme'] . ", 'GST', '3.00', '1', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
            $insQuery = $this->db->query("INSERT INTO `gst_splitup_detail` (`id_gst_splitup`, `id_scheme`, `splitup_name`, `percentage`, `status`, `type`, `effective_date`, `date_upd`) VALUES (NULL, " . $row['id_scheme'] . ", 'SGST', '1.50', '1', 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
            $insQuery = $this->db->query("INSERT INTO `gst_splitup_detail` (`id_gst_splitup`, `id_scheme`, `splitup_name`, `percentage`, `status`, `type`, `effective_date`, `date_upd`) VALUES (NULL, " . $row['id_scheme'] . ", 'CGST', '1.50', '1', 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
            $insQuery = $this->db->query("INSERT INTO `gst_splitup_detail` (`id_gst_splitup`, `id_scheme`, `splitup_name`, `percentage`, `status`, `type`, `effective_date`, `date_upd`) VALUES (NULL, " . $row['id_scheme'] . ", 'IGST', '3.00', '1', 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
        }
        return $count;
    }
    function get_branches()
    {
        $this->db->select('*');
        $this->db->where('active', '1');
        $classifications = $this->db->get('branch');
        /*$data[]=array('id'=>0,'name'=>"-- Select --");*/
        $data = array();
        foreach ($classifications->result() as $classification) {
            $data[] = array(
                'id_branch' => $classification->id_branch,
                'name' => $classification->name
            );
        }
        return $data;
    }
    /*public function scheme_branch($data)
        {
            $status=$this->db->insert(self::BRANCH_TABLE,$data);
            return array('status'=>$status,'id_scheme'=>$this->db->insert_id());
        }
         */
    public function scheme_branch($data)
    {
        $status = $this->db->insert(self::BRANCH_TABLE, $data);
        return array('status' => $status, 'id_scheme' => $this->db->insert_id());
    }
    public function delete_scheme_branch($id)
    {
        $this->db->where('id_scheme', $id);
        $status = $this->db->delete(self::BRANCH_TABLE);
    }
    public function get_branch_data($id)
    {
        $sql = "SELECT id_scheme, id_branch FROM scheme_branch WHERE scheme_active=1 and `id_scheme` =" . $id;
        $data = $this->db->query($sql);
        return $data->result_array();
    }
    function get_scheme_count($id_scheme)
    {
        $sql = "SELECT id_scheme FROM scheme_account where id_scheme=" . $id_scheme . "";
        return $this->db->query($sql)->num_rows();
    }
    //Scheme Benefits
    public function get_benfit_rdeduct_data($id)
    {
        $sql = "SELECT * FROM scheme_benefit_deduct_settings WHERE  `id_scheme` =" . $id;
        $data = $this->db->query($sql);
        return $data->result_array();
    }
    public function delete_benfit_rdeduct($id)
    {
        $this->db->where('id_scheme', $id);
        $status = $this->db->delete(self::SCH_BENEFIT_TABLE);
    }
    //Pre-close settings benefit deduction based on scheme chart //HH
    // 	public function insertDatas($data,$table)
    // {
    // 	$insert_flag = 0;
    // 	$insert_flag = $this->db->insert($table,$data);
    //     return ($insert_flag == 1 ? $this->db->insert_id(): 0);
    // }
    public function insertData($data, $table)
    {
        $query = $this->db->query("SHOW COLUMNS FROM `$table`");
        $columns = $query->result_array();
        $default_values = [];
        foreach ($columns as $column) {
            if (!is_null($column['Default'])) {
                $default_values[$column['Field']] = $column['Default'];
            } else {
                // If no default value, use an empty string or null based on column nullability
                $default_values[$column['Field']] = $column['Null'] === 'YES' ? null : '';
            }
        }
        foreach ($data as $field => $value) {
            // If the value is empty, set it to the default value
            if ((empty($value) || $value == 'null')) {
                // $data[$field] = $default_values[$field];
                if ($value === 0 || $value === '0') {
                    $data[$field] = 0;
                } else {
                    $data[$field] = $default_values[$field];
                }
            }
        }
        $insert_flag = $this->db->insert($table, $data);
        return ($insert_flag == 1 ? $this->db->insert_id() : 0);
    }
    public function get_closing_scheme_data($id)
    {
        $sql = "SELECT * FROM emp_closing_incentive WHERE  `id_scheme` =" . $id;
        $data = $this->db->query($sql);
        return $data->result_array();
    }
    public function delete_benfit_rdeduct_preclose($id)
    {
        $this->db->where('id_scheme', $id);
        $status = $this->db->delete(self::SCH_BENEFIT_DEDUCT__TABLE);
    }
    public function get_benfit_rdeduct_preclose__data($id)
    {
        $sql = "SELECT * FROM scheme_debit_settings WHERE  `id_scheme` =" . $id;
        $data = $this->db->query($sql);
        return $data->result_array();
    }
    public function delete_agent_benefit($id)
    {
        //print_r($id);exit;
        $this->db->where('id_scheme', $id);
        $status = $this->db->delete(self::SCH_AGENT_BENEFIT__TABLE);
    }
    public function get_agent_benefit__data($id)
    {
        $sql = "SELECT * FROM scheme_agent_benefit WHERE  `id_scheme` =" . $id;
        $data = $this->db->query($sql);
        return $data->result_array();
    }
    //Scheme Benefits
    // Start of employee & agent incentive benefit 
    public function get_incentive_data($id)
    {
        $sql = "SELECT * FROM scheme_incentive_settings WHERE  `id_scheme` =" . $id;
        $data = $this->db->query($sql);
        return $data->result_array();
    }
    public function delete_incentive_benefit($id)
    {
        //print_r($id);exit;
        $this->db->where('id_scheme', $id);
        $status = $this->db->delete('scheme_incentive_settings');
    }
    public function get_flexible_ins_data($id)
    {
        $sql = "SELECT * FROM scheme_flexi_settings WHERE  `id_scheme` =" . $id;
        $data = $this->db->query($sql);
        return $data->result_array();
    }
    // End of employee & agent incentive benefit 
    function getActivePuritiesByMetal($id_metal)
    {
        $sql = $this->db->query("SELECT p.id_purity,p.purity,p.status
                                FROM `ret_metal_purity_rate` mp 
                                LEFT JOIN ret_purity p on p.id_purity = mp.id_purity
                                where mp.id_metal = " . $id_metal . " and p.status = 1");
        return $sql->result_array();
    }
    //TKV double bonus scheme....
    public function get_adv_benefit_data($id)
    {
        $sql = "SELECT * FROM scheme_general_advance_benefit_settings WHERE  `id_scheme` =" . $id;
        $data = $this->db->query($sql);
        return $data->result_array();
    }
    //lump scheme starts
    function get_joinTime_weight_slabs($id_scheme)
    {
        $wgts = $this->db->query("select joinTime_weight_slabs from scheme where id_scheme = " . $id_scheme)->row()->joinTime_weight_slabs;
        $slabs = $this->db->query("SELECT * FROM weight where active = 1 and id_weight IN (" . $wgts . ")")->result_array();
        return $slabs;
    }
    //lump scheme ends
    // get weight from min and max weight--start
    function get_weight_list($min, $max)
    {
        if ($min == '0' && $max == '0') {
            $sql = "SELECT * FROM weight where active=1";
        } else {
            $sql = "SELECT * FROM weight where active=1 and weight between " . $min . " and " . $max;
        }
        $data = $this->db->query($sql);
        return $data->result_array();
    }
    // get weight from min and max weight--end
	function enableDigiGold($id = '', $id_metal)
    {
        if ($id == '') {
			$sql = "SELECT IF(EXISTS(SELECT 1 FROM scheme WHERE is_digi = 1 AND active = 1 AND id_metal = {$id_metal}), 1, 0) AS is_digi_gold";
            $data = $this->db->query($sql);
            $result = $data->row_array();
        } else {
            // print_r($id);exit;
			$sql = "SELECT IF(EXISTS(SELECT 1 FROM scheme WHERE id_scheme != " . $id . " AND is_digi = 1 AND active = 1  AND id_metal = {$id_metal}), 1, 0) AS is_digi_gold";
            // print_r($sql);exit;
            $data = $this->db->query($sql);
            $result = $data->row_array();
        }
        return $result;
    }
    // get weight from min and max weight--end
    // Top up scheme chart 
    function getTopUpSchemeChart($schemeId)
    {
        $this->db->select('payable_by, range_from, range_to, payable_type, payable_value');
        $this->db->from('scheme_custom_payable_settings');
        $this->db->where('id_scheme', $schemeId);
        $this->db->where('range_status', 1);
        $data = $this->db->get()->result_array();
        return $data;
    }
    function topUpSchemeChartEditProcess($schemeId, $chartData)
    {
        // Update a old chart status to 0 ;
        $data['range_status'] = 0;
        $employeeId = $this->session->userdata('uid');
        $this->db->where('id_scheme', $schemeId);
        $status = $this->db->update('scheme_custom_payable_settings', $data);
        if ($status) {
            foreach ($chartData as $row) {
                $topUpChartInsertData[] = [
                    'id_scheme' => $schemeId,
                    'payable_by' => $row['payable_by'],
                    'range_from' => $row['range_from'],
                    'range_to' => $row['range_to'],
                    'payable_type' => $row['payable_type'],
                    'payable_value' => $row['payable_value'],
                    'created_by' => '',
                    'created_on' => '',
                    'updated_on' => date('Y-m-d H:i:s'),
                    'updated_by' => $employeeId
                ];
            }
            $status = $this->db->insert_batch('scheme_custom_payable_settings', $topUpChartInsertData);
        }
        return $status;
    }
}
