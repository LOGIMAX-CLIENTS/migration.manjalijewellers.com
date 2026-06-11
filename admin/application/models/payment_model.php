<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Payment_model extends CI_Model
{
    const ACC_TABLE = "scheme_account";
    const CUS_TABLE = "customer";
    const SCH_TABLE = "scheme";
    const SCH_GROUP_TABLE = "scheme_group";
    const PAY_TABLE = "payment";
    const CUS_REG_TABLE = "customer_reg";
    const MOD_TABLE = "payment_mode";
    const DC_TABLE = "daily_collection";
    //const STAT_TABLE		= "payment_status";
    const SETT_TABLE = "settlement";
    const SETT_DET_TABLE = "settlement_detail";
    const PAY_STATUS = "payment_status_message";
    const BRANCH = "branch";
    const EMPLOYEE_TABLE = "employee";
    const PURCH_CUS_TABLE = "purchase_customer";
    const TRANS_TABLE = "transaction";
    function __construct()
    {
        parent::__construct();
        $this->load->model("customer_model");
        $this->log_dir = 'log/' . date("Y-m-d");
        if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0777, TRUE);
        }
    }
    function get_receipt_no($id_scheme = '', $branch = '')
    {
        $data = $this->get_settings();
        $id_company = $this->session->userdata('id_company');
        $company_settings = $this->session->userdata('company_settings');
        if ($data['scheme_wise_receipt'] == 5 || $data['scheme_wise_receipt'] == 6 || $data['scheme_wise_receipt'] == 7) {
            $res = $this->db->query("SELECT date(fin_year_from) as fin_date,fin_year_code FROM `ret_financial_year` where fin_status = 1");
            $financial_year = $res->row()->fin_year_code;
            $financial_date = $res->row()->fin_date;
        }
        $lg_data1 = "";
        /*	$this->db->query('LOCK TABLES payment WRITE, scheme_account WRITE, customer WRITE, scheme WRITE');
        $lg_data1 = "\n CP payment,scheme_account,customer,scheme table locked at --".date('d-m-Y H:i:s');   */
        $log_path = $this->log_dir . '/manual/create_payment_' . date("Y-m-d") . '.txt';
        file_put_contents($log_path, $lg_data1, FILE_APPEND | LOCK_EX);
        if ($data['branch_settings'] == 1) { // Branch Enabled
            if ($data['scheme_wise_receipt'] == 2 && $branch > 0) {  // 2 - branch wise Receipt number
                $sql = "Select max(receipt_no) as receipt_no
    				From payment 
    				left join scheme_account on scheme_account.id_scheme_account=payment.id_scheme_account
    				left join customer on customer.id_customer= scheme_account.id_customer
    				Where   payment.id_branch=" . $branch . " 
    				" . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " group by payment.id_branch ";
            } else if ($data['scheme_wise_receipt'] == 4) { // 4 - Scheme-wise with branch wise Receipt number
                $sql = "Select max(receipt_no) as receipt_no
    				From payment 
        				left join scheme_account on scheme_account.id_scheme_account=payment.id_scheme_account
        				left join customer on customer.id_customer= scheme_account.id_customer
        				left join scheme on scheme.id_scheme=scheme_account.id_scheme
    				Where   scheme_account.id_scheme=" . $id_scheme . " AND payment.id_branch=" . $branch . "
    				" . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " group by scheme_account.id_scheme,payment.id_branch ";
            } else if ($data['scheme_wise_receipt'] == 5) { // financial year  wise
                $sql = "Select receipt_no
    				From payment
    				left join scheme_account  on scheme_account.id_scheme_account=payment.id_scheme_account
        			left join customer on customer.id_customer= scheme_account.id_customer
    				Where  date(date_payment) BETWEEN '" . $financial_date . "' AND DATE(CURDATE()) " . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " order by receipt_no desc limit 0,1 ";
            } else if ($data['scheme_wise_receipt'] == 6) { // 6 - financial year  with Scheme-wise & branch wise Receipt number
                $sql = "Select max(receipt_no) as receipt_no
    				From payment
        				left join scheme_account  on scheme_account.id_scheme_account=payment.id_scheme_account
        				left join customer on customer.id_customer= scheme_account.id_customer
        				left join scheme on scheme.id_scheme=scheme_account.id_scheme
    				Where  date(date_payment) BETWEEN '" . $financial_date . "' AND DATE(CURDATE())  and scheme_account.id_scheme=" . $id_scheme . " AND payment.id_branch=" . $branch . "
    				" . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " group by scheme_account.id_scheme,payment.id_branch ";
            } else if ($data['scheme_wise_receipt'] == 7) { // 7 - financial year with branch wise Receipt number
                $sql = "Select max(receipt_no) as receipt_no
    				From payment
        				left join scheme_account  on scheme_account.id_scheme_account=payment.id_scheme_account
        				left join customer on customer.id_customer= scheme_account.id_customer
    				Where  date(date_payment) BETWEEN '" . $financial_date . "' AND DATE(CURDATE())  AND payment.id_branch=" . $branch . "
    				" . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " group by payment.receipt_year,payment.id_branch order by receipt_no desc limit 0,1";
            } else if ($data['scheme_wise_receipt'] == 3) {   // 3 - Scheme-wise Receipt number
                $sql = "Select max(receipt_no) as receipt_no
    				From payment
        				left join scheme_account on scheme_account.id_scheme_account=payment.id_scheme_account
        				left join customer on customer.id_customer= scheme_account.id_customer
        				left join scheme on scheme.id_scheme=scheme_account.id_scheme
    				Where   scheme_account.id_scheme=" . $id_scheme . " " . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " group by scheme_account.id_scheme ";
            } else  // If other cases fails,generate 1 - common Receipt number
            {
                $sql = "Select receipt_no
    				From payment
    				left join scheme_account on scheme_account.id_scheme_account=payment.id_scheme_account
        			left join customer on customer.id_customer= scheme_account.id_customer
    				Where  1 " . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " order by receipt_no desc limit 0,1 ";
            }
        } else {
            if ($data['scheme_wise_receipt'] == 3) {   // 3 - Scheme-wise Receipt number
                $sql = "Select max(receipt_no) as receipt_no
    				From payment
        				left join scheme_account on scheme_account.id_scheme_account=payment.id_scheme_account
        				left join customer on customer.id_customer= scheme_account.id_customer
        				left join scheme on scheme.id_scheme=scheme_account.id_scheme
    				Where  scheme_account.id_scheme=" . $id_scheme . " " . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " group by scheme_account.id_scheme ";
            } else if ($data['scheme_wise_receipt'] == 5) { // financial year wise
                $sql = "Select receipt_no
    				From payment
    				left join scheme_account on scheme_account.id_scheme_account=payment.id_scheme_account
        			left join customer on customer.id_customer= scheme_account.id_customer
    				Where  date(date_payment) BETWEEN '" . $financial_date . "' AND DATE(CURDATE())  " . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " order by receipt_no desc limit 0,1 ";
            } else  // If other cases fails,generate 1 - common Receipt number
            {
                $sql = "Select receipt_no
    				From payment
    				left join scheme_account on scheme_account.id_scheme_account=payment.id_scheme_account
        			left join customer  on customer.id_customer= scheme_account.id_customer
    				Where  1 " . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " order by receipt_no desc limit 0,1 ";
            }
        }
        // 		print_r($sql);exit;
        return $this->db->query($sql)->row()->receipt_no;
    }
    function get_receipt_no_settings()
    {
        $sql = "Select c.scheme_wise_receipt FROM chit_settings c where c.id_chit_settings = 1";
        return $this->db->query($sql)->row()->scheme_wise_receipt;
    }
    function entry_date_settings()
    {
        $sql = "Select c.edit_custom_entry_date FROM chit_settings c where c.id_chit_settings = 1";
        return $this->db->query($sql)->row()->edit_custom_entry_date;
    }
    function payment_on_off_get($from_date, $to_date, $id)
    {
        if ($id == 0 && ($id != null || $id != "")) {
            $sql = "SELECT * FROM transaction where payment_type=2 and date_add between '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "'";
            $user = $this->db->query($sql);
            $users = $user->result();
            if (!empty($users)) {
                foreach ($users as $row) {
                    $data[] = array(
                        'id_scheme_account' => $row->id_scheme_account,
                        'reference_no' => $row->ref_no,
                        'receipt_no' => $row->receipt_no,
                        'payment_date' => $row->payment_date,
                        'amount' => $row->amount,
                        'weight' => $row->weight,
                        'rate' => $row->rate,
                        'payment_mode' => $row->payment_mode,
                        'payment_transaction_id' => $row->pay_trans_id,
                        'paid_through' => $row->paid_through,
                        'payment_type' => $row->payment_type,
                        'on_off' => $row->payment_type,
                        'payment_status' => $row->payment_status
                    );
                }
                return $data;
            }
        } else if ($id == 1 && ($id != null || $id != "")) {
            $sql = "SELECT * FROM payment where is_offline=0 AND date_payment between '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "'";
            $user = $this->db->query($sql);
            $users = $user->result();
            if (!empty($users)) {
                foreach ($users as $row) {
                    $data[] = array(
                        'id_scheme_account' => $row->id_scheme_account,
                        'reference_no' => $row->payment_ref_number,
                        'receipt_no' => $row->receipt_no,
                        'payment_date' => $row->date_payment,
                        'amount' => $row->payment_amount,
                        'weight' => $row->metal_weight,
                        'rate' => $row->metal_rate,
                        'payment_mode' => $row->payment_mode,
                        'payment_transaction_id' => $row->ref_trans_id,
                        'paid_through' => $row->added_by,
                        'payment_type' => $row->payment_type,
                        'on_off' => $row->is_offline,
                        'payment_status' => $row->payment_status
                    );
                }
                return $data;
            }
        } else if ($id == null && $id == "") {
            $sql = "SELECT * FROM payment where date_payment between '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "'";
            $user = $this->db->query($sql);
            $users = $user->result();
            if (!empty($users)) {
                foreach ($users as $row) {
                    $data[] = array(
                        'id_scheme_account' => $row->id_scheme_account,
                        'reference_no' => $row->payment_ref_number,
                        'receipt_no' => $row->receipt_no,
                        'payment_date' => $row->date_payment,
                        'amount' => $row->payment_amount,
                        'weight' => $row->metal_weight,
                        'rate' => $row->metal_rate,
                        'payment_mode' => $row->payment_mode,
                        'payment_transaction_id' => $row->ref_trans_id,
                        'paid_through' => $row->added_by,
                        'payment_type' => $row->payment_type,
                        'on_off' => $row->is_offline,
                        'payment_status' => $row->payment_status
                    );
                }
                return $data;
            }
        }
    }
    function payment_list($id = "", $limit = "", $type = "")
    {
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $id_branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        if ($id != NULL) {
            $sql = "SELECT  s.show_ins_type,s.total_installments,sa.fixed_wgt,
					cs.has_lucky_draw,s.code,IFNULL(sa.group_code,'')as scheme_group_code,
					  p.id_payment,p.is_offline,sa.id_branch,sa.ref_no,sa.id_scheme_account,
					  sa.account_name,p.payment_amount,p.id_branch,
					  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,c.lastname,c.firstname,
					  c.mobile,c.email,b.name as payment_branch,
					  IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,p.due_type,p.act_amount,p.added_by,
					  s.code,s.scheme_name,
					  p.id_employee,IFNULL(e.emp_code,'-')as emp_code,
                      if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee,IFNULL(e.emp_code,'-')as emp_code,
					  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
					  IFNULL(p.payment_amount,'-') as payment_amount,
					  IFNULL(if(p.metal_rate=0,'-',p.metal_rate), '-') as metal_rate,
					  IFNULL(if(p.metal_weight=0,'-',p.metal_weight), '-') as metal_weight,
					  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
			          p.payment_type,
					  IFNULL(p.payment_mode,'-') as payment_mode,
					  IFNULL(sa.scheme_acc_number,'') as msno,
					  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
					  IFNULL(p.bank_name,'-')as bank_name,
					  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
					  IFNULL(p.bank_branch,'-') as bank_branch,
					  IFNULL(cs.receipt_no_set,'-') as receipt_no_set,
					  IFNULL(p.id_transaction,'-') as id_transaction,
					  IFNULL(p.payu_id,'-') as payu_id ,
					  IFNULL(p.card_no,'-') as card_no,
					  psm.payment_status as payment_status,
					  p.payment_status as id_status,
					  psm.color as status_color,
					  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
					if(cs.receipt_no_set=1 && p.receipt_no is null,'',p.receipt_no) as receipt_no,
					  IFNULL(p.remark,'-') as remark,cs.currency_name,cs.currency_symbol,p.id_payGateway,sa.is_closed,sa.active ,
					  IFNULL(IF(p.saved_benefits = 0 OR p.saved_benefits = '', '-', p.saved_benefits), '-') AS saved_benefits,
					  IFNULL(IF(p.saved_benefit_amt = 0 OR p.saved_benefit_amt = '', '-', p.saved_benefit_amt), '-') AS saved_benefit_amt,p.id_agent,IFNULL(a.agent_code,'-')as agent_code,if(a.lastname is null,a.firstname,concat(a.firstname,' ',a.lastname)) as agent
				FROM payment p
				join chit_settings cs
				left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
				Left Join employee e On (e.id_employee=p.id_employee)
                Left Join agent a On (a.id_agent=p.id_agent)
				Left Join customer c on (sa.id_customer=c.id_customer)
				left join scheme s on(sa.id_scheme=s.id_scheme)
			    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)		
			    Left Join branch b on (p.id_branch=b.id_branch)		
			    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)" . ($id != NULL ? ' Where p.id_payment=' . $id : '') . " 
				 ORDER BY p.date_payment DESC ";
            $payment = $this->db->query($sql);
            return $payment->row_array();
        } else {
            $sql = "SELECT
					cs.has_lucky_draw,s.code,IFNULL(sa.group_code,'')as scheme_group_code,
					  p.id_payment,p.is_offline,sa.id_branch,sa.ref_no,sa.id_scheme_account,
					  sa.account_name,p.payment_amount,
					  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,c.lastname,c.firstname,
					  c.mobile,c.email,
					  IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,p.due_type,p.act_amount,
					  s.code,s.scheme_name,
					  p.id_employee,IFNULL(e.emp_code,'-')as emp_code,
                      if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee, 
					  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
					  IFNULL(p.payment_amount,'-') as payment_amount,
					  IFNULL(if(p.metal_rate=0,'-',p.metal_rate), '-') as metal_rate,
					  IFNULL(if(p.metal_weight=0,'-',p.metal_weight), '-') as metal_weight,
					  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
			          p.payment_type,
					  IFNULL(p.payment_mode,'-') as payment_mode,
					  IFNULL(sa.scheme_acc_number,'') as msno,
					  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
					  IFNULL(p.bank_name,'-')as bank_name,
					  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
					  IFNULL(p.bank_branch,'-') as bank_branch,
					  IFNULL(cs.receipt_no_set,'-') as receipt_no_set,
					  IFNULL(p.id_transaction,'-') as id_transaction,
					  IFNULL(p.payu_id,'-') as payu_id ,
					  IFNULL(p.card_no,'-') as card_no,
					  psm.payment_status as payment_status,
					  p.payment_status as id_status,
					  psm.color as status_color,
					  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
					if(cs.receipt_no_set=1 && p.receipt_no is null,'',p.receipt_no) as receipt_no,
					  IFNULL(p.remark,'-') as remark,cs.currency_name,cs.currency_symbol,p.id_payGateway,sa.is_closed,sa.active 
				FROM payment p
				join chit_settings cs
				left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
				Left Join employee e On (e.id_employee=p.id_employee)
				Left Join customer c on (sa.id_customer=c.id_customer)
				left join scheme s on(sa.id_scheme=s.id_scheme)
			    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)		
			    Left Join branch b on (sa.id_branch=b.id_branch)		
			    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
			    " . ($uid != 1 ? ($branchWiseLogin == 1 ? ($id_branch != '' ? "  Where p.id_branch=" . $id_branch . " or b.show_to_all=1" : '') : '') : '') . "
				 ORDER BY p.date_payment DESC ";
            $payment = $this->db->query($sql);
            return $payment->result_array();
        }
    }
    function payment_list_range($from_date, $to_date, $type = "", $limit = "", $date_type)
    {
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $branch_settings = $this->session->userdata('branch_settings');
        //print_r($branch_settings);exit;
        $is_branchwise_cus_reg = $this->session->userdata('is_branchwise_cus_reg');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $id_employee = $this->input->post('id_employee');
        $id_status = $_POST['id_status'];
        $id_customer = $_POST['id_customer'];
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = '';
        }
        //      $sql = $this->db->query("SELECT s.show_ins_type,s.total_installments,IFNULL(sa.start_year,'') as start_year,IFNULL(p.receipt_year,'') as receipt_year,
        // 	(select b.short_name from branch b where b.id_branch = sa.id_branch) as acc_branch, 
        // 	(select b.short_name from branch b where b.id_branch = p.id_branch) as payment_branch, b.short_name,
        // 	s.code,cs.schemeaccNo_displayFrmt,cs.receiptNo_displayFrmt,s.is_lucky_draw,ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
        // 	p.receipt_no,cs.scheme_wise_receipt,cs.scheme_wise_acc_no,cs.scheme_wise_acc_no,
        // 				  p.id_payment,b.name as branch_name,p.is_offline,sa.id_branch,sa.ref_no,sa.id_scheme_account,p.id_branch as pay_branch,
        // 				  cs.has_lucky_draw,sa.fixed_wgt,s.one_time_premium,
        // 				  sa.account_name,p.act_amount,p.installment,
        // 				  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,p.added_by,
        // 				  c.mobile,
        // 				   IFNULL(sa.group_code,'-') as scheme_group_code,(select IFNULL(short_name,'') from branch where id_branch=sa.id_branch) as branch_code,
        // 				  IFNULL(concat(if(cs.scheme_wise_acc_no=4 or scheme_wise_acc_no=5, IFNULL(concat(sa.start_year,'-'),'') ,''),sa.scheme_acc_number),'Not Allocated') as old_scheme_acc_number1,IFNULL(sa.old_scheme_acc_number,'-') as old_scheme_acc_number,
        // 				  if(cs.scheme_wise_receipt=2,p.receipt_no,concat(ifnull(concat(p.receipt_year,'-'),''),p.receipt_no)) as old_receipt_no,
        // 				  cs.receipt_no_set,p.receipt_year,
        // 				  p.due_type,
        // 				  s.code,b.name as payment_branch,
        // 				  p.id_employee,IFNULL(e.emp_code,'-')as emp_code,
        //                   if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee, 
        // 				  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
        // 				  IFNULL(p.payment_amount,'-') as payment_amount,
        // 				  p.metal_rate, p.is_editing_enabled, Date_Format(p.metalrate_edit_date, '%d-%m-%Y %r') as metalrate_edit_date,
        // 				  IFNULL(p.metal_weight, '-') as metal_weight,
        // 				  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y %H:%i:%s'),'-') as date_payment,
        // 				 IFNULL((select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 and s.payment_chances=1) , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=p.id_scheme_account group by pay.id_scheme_account),0) as paid_installments1,
        // 				 ifnull(sa.total_paid_ins,0) as paid_installments,
        // 		          p.payment_type,p.is_print_taken,
        // 				  if(p.added_by=3,p.payment_type,p.payment_mode) as oldpayment_mode,
        // 				   ifnull(p.payment_mode,'-') as payment_mode,
        // 				  IFNULL(sa.scheme_acc_number,'') as msno,
        // 				  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
        // 				  IFNULL(p.bank_name,'-')as bank_name,
        // 				  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
        // 				  IFNULL(p.bank_branch,'-') as bank_branch,
        // 				  IFNULL(p.id_transaction,'-') as id_transaction,
        // 				  IFNULL(p.payu_id,'-') as payu_id ,
        // 				  IFNULL(p.card_no,'-') as card_no,
        // 				  psm.payment_status as payment_status,
        // 				  p.payment_status as id_status,
        // 				  psm.color as status_color,
        // 				  IFNULL(p.payment_ref_number,IFNULL(p.card_no,'-')) as payment_ref_number,
        // 				  IFNULL(p.remark,'-') as remark,
        // 				 IFNULL(cs.receipt_no_set,'-') as receipt_no_set, IFNULL(Date_format(p.custom_entry_date,'%d-%m%-%Y'),'-') as entry_Date,cs.edit_custom_entry_date,
        // 				 cs.scheme_wise_acc_no,b.short_name,p.id_payGateway,sa.is_closed,sa.active,
        // 				 IFNULL(IF(p.saved_benefits = 0 OR p.saved_benefits = '', '-', p.saved_benefits), '-') AS saved_benefits,
        // 				 IFNULL(IF(p.saved_benefit_amt = 0 OR p.saved_benefit_amt = '', '-', p.saved_benefit_amt), '-') AS saved_benefit_amt,
        // 				 IFNULL(p.dg_other_benefit_wgt,'-') as dg_other_benefit_wgt,p.id_agent,IFNULL(a.agent_code,'-')as agent_code,if(a.lastname is null,a.firstname,concat(a.firstname,' ',a.lastname)) as agent
        // 			FROM payment p
        // 			 join  chit_settings cs
        // 			left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
        // 			Left Join employee e On (e.id_employee=p.id_employee)
        //             Left Join agent a On (a.id_agent=p.id_agent)
        // 			Left Join customer c on (sa.id_customer=c.id_customer)
        // 			left join scheme s on(sa.id_scheme=s.id_scheme)
        // 			 Left Join branch b on (p.id_branch=b.id_branch)
        // 		    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)		
        // 		    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
        //    Where   (date(" . ($date_type != '' ? ($date_type == 2 ? "p.custom_entry_date" : "p.date_payment") : "p.date_payment") . ") BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') " . ($id_employee != NULL || $id_employee != '' ? ' and p.id_employee =' . $id_employee : '') . "
        //     	" . ($type != '' ? "  And p.payment_type=" . $type : '') . " 
        //     	" . ($uid != 1 ? ($branch_settings == 1 ? ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : " and (b.show_to_all=1 or b.show_to_all=3)") : '') : ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : '')) . "
        // 		   " . ($id_company != '' && $company_settings == 1 ? " and s.id_company='" . $id_company . "'" : '') . "
        // 		   " . ($id_status != '' ? " and psm.id_status_msg='" . $id_status . "'" : '') . "
        //            " . ($id_customer != '' ? "and c.id_customer=" . $id_customer . "" : '') . "
        // 		   ORDER BY p.id_payment DESC " . ($limit != NULL ? " LIMIT " . $limit . " OFFSET " . $limit : " "));
        // Metal rate back date entry esakki-20250729
        $this->db->select("s.show_ins_type, s.total_installments, IFNULL(sa.start_year,'') as start_year, IFNULL(p.receipt_year,'') as receipt_year", FALSE);
        $this->db->select("(select b.short_name from branch b where b.id_branch = sa.id_branch) as acc_branch", FALSE);
        $this->db->select("(select b.short_name from branch b where b.id_branch = p.id_branch) as payment_branch, b.short_name", FALSE);
        $this->db->select("s.code, cs.schemeaccNo_displayFrmt, cs.receiptNo_displayFrmt, s.is_lucky_draw, ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number", FALSE);
        $this->db->select("p.receipt_no, cs.scheme_wise_receipt, cs.scheme_wise_acc_no", FALSE);
        $this->db->select("p.id_payment, b.name as branch_name, p.is_offline, sa.id_branch, sa.ref_no, sa.id_scheme_account, p.id_branch as pay_branch", FALSE);
        $this->db->select("cs.has_lucky_draw, sa.fixed_wgt, s.one_time_premium", FALSE);
        $this->db->select("sa.account_name, p.act_amount, p.installment", FALSE);
        $this->db->select("if(c.lastname is null, c.firstname, concat(c.firstname,' ',c.lastname)) as name, p.added_by, c.mobile", FALSE);
        $this->db->select("IFNULL(sa.group_code,'-') as scheme_group_code, (select IFNULL(short_name,'') from branch where id_branch=sa.id_branch) as branch_code", FALSE);
        $this->db->select("IFNULL(concat(if(cs.scheme_wise_acc_no=4 or scheme_wise_acc_no=5, IFNULL(concat(sa.start_year,'-'),'') ,''),sa.scheme_acc_number),'Not Allocated') as old_scheme_acc_number1, IFNULL(sa.old_scheme_acc_number,'-') as old_scheme_acc_number", FALSE);
        $this->db->select("if(cs.scheme_wise_receipt=2, p.receipt_no, concat(ifnull(concat(p.receipt_year,'-'),''),p.receipt_no)) as old_receipt_no", FALSE);
        $this->db->select("cs.receipt_no_set, p.receipt_year, p.due_type, s.code, b.name as payment_branch", FALSE);
        $this->db->select("p.id_employee, IFNULL(e.emp_code,'-')as emp_code", FALSE);
        $this->db->select("if(e.lastname is null, e.firstname, concat(e.firstname,' ',e.lastname)) as employee", FALSE);
        $this->db->select("if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type", FALSE);
        $this->db->select("IFNULL(p.payment_amount,'-') as payment_amount", FALSE);
        $this->db->select("p.metal_rate, p.is_editing_enabled, Date_Format(p.metalrate_edit_date, '%d-%m-%Y %r') as metalrate_edit_date", FALSE);
        $this->db->select("IFNULL(p.metal_weight, '-') as metal_weight", FALSE);
        $this->db->select("IFNULL(Date_format(p.date_payment,'%d-%m-%Y %H:%i:%s'),'-') as date_payment", FALSE);
        $paid_ins_sub = "IFNULL((select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 and s.payment_chances=1) , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=p.id_scheme_account group by pay.id_scheme_account),0)";
        $this->db->select("$paid_ins_sub as paid_installments1", FALSE);
        $this->db->select("ifnull(sa.total_paid_ins,0) as paid_installments, p.payment_type, p.is_print_taken", FALSE);
        $this->db->select("if(p.added_by=3, p.payment_type, p.payment_mode) as oldpayment_mode, ifnull(p.payment_mode,'-') as payment_mode", FALSE);
        $this->db->select("IFNULL(sa.scheme_acc_number,'') as msno", FALSE);
        $this->db->select("IFNULL(p.bank_acc_no,'-') as bank_acc_no, IFNULL(p.bank_name,'-')as bank_name, IFNULL(p.bank_IFSC,'-') as bank_IFSC, IFNULL(p.bank_branch,'-') as bank_branch", FALSE);
        $this->db->select("IFNULL(p.id_transaction,'-') as id_transaction, IFNULL(p.payu_id,'-') as payu_id, IFNULL(p.card_no,'-') as card_no", FALSE);
        $this->db->select("psm.payment_status as payment_status, p.payment_status as id_status, psm.color as status_color", FALSE);
        $this->db->select("IF(length(trim(p.payment_ref_number)) > 0, p.payment_ref_number, IFNULL((SELECT GROUP_CONCAT(pmd.payment_ref_number ORDER BY pmd.id_pay_mode_details SEPARATOR ', ') FROM payment_mode_details pmd WHERE pmd.id_payment = p.id_payment AND pmd.payment_ref_number IS NOT NULL AND pmd.payment_ref_number != '' AND IFNULL(pmd.is_active,1) = 1), IFNULL(p.card_no,'-'))) as payment_ref_number", FALSE);
        $this->db->select("IFNULL((SELECT GROUP_CONCAT(CONCAT(CASE
            WHEN pmd.payment_mode = 'CSH' THEN CONCAT(pmd.payment_mode, '//$$//', pmd.payment_amount, '//$$//', '-', '//$$//', DATE_FORMAT(pmd.payment_date, '%d-%m-%Y'))
            WHEN pmd.payment_mode IN ('CC', 'DC') THEN CONCAT(pmd.payment_mode, '//$$//', pmd.payment_amount, '//$$//', pmd.payment_ref_number, '//$$//', DATE_FORMAT(pmd.payment_date, '%d-%m-%Y'))
            WHEN pmd.payment_mode = 'UPI' THEN CONCAT(pmd.payment_mode, '//$$//', pmd.payment_amount, '//$$//', IFNULL(pmd.payment_ref_number, '-'), '//$$//', DATE_FORMAT(pmd.payment_date, '%d-%m-%Y'))
            WHEN pmd.payment_mode = 'NB' AND pmd.NB_type = 2 THEN CONCAT(pmd.payment_mode, '- IMPS ', '//$$//', pmd.payment_amount, '//$$//', IFNULL(pmd.payment_ref_number, '-'), '//$$//', DATE_FORMAT(pmd.net_banking_date, '%d-%m-%Y'))
            WHEN pmd.payment_mode = 'NB' AND pmd.NB_type = 1 THEN CONCAT(pmd.payment_mode, '- RTGS ', '//$$//', pmd.payment_amount, '//$$//', IFNULL(pmd.payment_ref_number, '-'), '//$$//', DATE_FORMAT(pmd.net_banking_date, '%d-%m-%Y'))
            WHEN pmd.payment_mode = 'NB' AND pmd.NB_type = 3 THEN CONCAT(pmd.payment_mode, '- UPI-Transfer ', '//$$//', pmd.payment_amount, '//$$//', IFNULL(pmd.payment_ref_number, '-'), '//$$//', DATE_FORMAT(pmd.net_banking_date, '%d-%m-%Y'))
            WHEN pmd.payment_mode = 'NB' AND pmd.NB_type = 4 THEN CONCAT(pmd.payment_mode, '- NEFT ', '//$$//', pmd.payment_amount, '//$$//', IFNULL(pmd.payment_ref_number, '-'), '//$$//', DATE_FORMAT(pmd.net_banking_date, '%d-%m-%Y'))
            WHEN pmd.payment_mode = 'NB' AND pmd.NB_type = 5 THEN CONCAT(pmd.payment_mode, '- UPI Scanner ', '//$$//', pmd.payment_amount, '//$$//', IFNULL(pmd.payment_ref_number, '-'), '//$$//', DATE_FORMAT(pmd.net_banking_date, '%d-%m-%Y'))
            WHEN pmd.payment_mode = 'CHQ' THEN CONCAT(pmd.payment_mode, '//$$//', pmd.payment_amount, '//$$//', IFNULL(pmd.cheque_no, '-'), '//$$//', DATE_FORMAT(pmd.cheque_date, '%d-%m-%Y'))
            WHEN pmd.payment_mode = 'VCH' THEN CONCAT(pmd.payment_mode, '//$$//', pmd.payment_amount, '//$$//', IFNULL(pmd.card_no, '-'), '//$$//', DATE_FORMAT(pmd.payment_date, '%d-%m-%Y'))
            ELSE CONCAT(pmd.payment_mode, '//$$//', pmd.payment_amount, '//$$//', IFNULL(pmd.payment_ref_number, '-'), '//$$//', DATE_FORMAT(pmd.payment_date, '%d-%m-%Y'))
        END) SEPARATOR '||') FROM payment_mode_details pmd WHERE pmd.id_payment = p.id_payment AND IFNULL(pmd.is_active,1) = 1), '-') as payment_ref_details", FALSE);
        $this->db->select("IFNULL(p.remark,'-') as remark", FALSE);
        $this->db->select("IFNULL(cs.receipt_no_set,'-') as receipt_no_set, IFNULL(Date_format(p.custom_entry_date,'%d-%m-%Y'),'-') as entry_Date, cs.edit_custom_entry_date", FALSE);
        $this->db->select("cs.scheme_wise_acc_no, b.short_name, p.id_payGateway, sa.is_closed, sa.active", FALSE);
        $this->db->select("IFNULL(IF(p.saved_benefits = 0 OR p.saved_benefits = '', '-', p.saved_benefits), '-') AS saved_benefits", FALSE);
        $this->db->select("IFNULL(IF(p.saved_benefit_amt = 0 OR p.saved_benefit_amt = '', '-', p.saved_benefit_amt), '-') AS saved_benefit_amt", FALSE);
        $this->db->select("IFNULL(p.dg_other_benefit_wgt,'-') as dg_other_benefit_wgt, p.id_agent, IFNULL(a.agent_code,'-')as agent_code, if(a.lastname is null, a.firstname, concat(a.firstname,' ',a.lastname)) as agent", FALSE);
        $this->db->from("payment p Join chit_settings cs");
        // $this->db->join('chit_settings cs', '1=1', 'inner', false);
        $this->db->join("scheme_account sa", "p.id_scheme_account=sa.id_scheme_account", "left");
        $this->db->join("employee e", "e.id_employee=p.id_employee", "left");
        $this->db->join("agent a", "a.id_agent=p.id_agent", "left");
        $this->db->join("customer c", "sa.id_customer=c.id_customer", "left");
        $this->db->join("scheme s", "sa.id_scheme=s.id_scheme", "left");
        $this->db->join("branch b", "p.id_branch=b.id_branch", "left");
        $this->db->join("payment_mode pm", "p.payment_mode=pm.id_mode", "left");
        $this->db->join("payment_status_message psm", "p.payment_status=psm.id_status_msg", "left");
        $date_col = ($date_type != '' ? ($date_type == 2 ? "p.custom_entry_date" : "p.date_payment") : "p.date_payment");
        $this->db->where("(date($date_col) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')", NULL, FALSE);
        if ($id_employee != NULL && $id_employee != '') {
            $this->db->where("p.id_employee", $id_employee);
        }
        if ($type != '') {
            $this->db->where("p.payment_type", $type);
        }
        if ($uid != 1) {
            if ($branch_settings == 1) {
                if ($id_branch != 0 && $id_branch != '') {
                    $this->db->where("p.id_branch", $id_branch);
                } else {
                    $this->db->where("(b.show_to_all=1 or b.show_to_all=3)", NULL, FALSE);
                }
            }
        } else {
            if ($id_branch != 0 && $id_branch != '') {
                $this->db->where("p.id_branch", $id_branch);
            }
        }
        if ($id_company != '' && $company_settings == 1) {
            $this->db->where("s.id_company", $id_company);
        }
        if ($id_status != '') {
            $this->db->where("psm.id_status_msg", $id_status);
        }
        if ($id_customer != '') {
            $this->db->where("c.id_customer", $id_customer);
        }
        $this->db->order_by("p.id_payment", "DESC");
        if ($limit != NULL) {
            $this->db->limit($limit, $limit);
        }
        $sql = $this->db->get();
        //  echo $id_branch;
        //  var_dump($id_branch);exit;
        //	print_r($sql);exit;
        $result = [];
        $payment = $sql->result_array();
        if ($sql->num_rows() > 0) {
            foreach ($payment as $rcpt) {
                $rcpt['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $rcpt['id_scheme_account']);
                $rcpt['receipt_no'] = $this->customer_model->format_accRcptNo('Receipt', $rcpt['id_payment']);
                $result[] = $rcpt;
            }
        }
        return $result;
    }
    function payment_online_range($from_date, $to_date, $limit = "", $pg_code = "")
    {
        $branch_settings = $this->session->userdata('branch_settings');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = '';
        }
        $sql = "SELECT
					  p.id_payment,p.ref_trans_id,
					  sa.account_name,
					  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
					  c.mobile,
					  IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
					  s.code,p.is_print_taken,
					  p.id_employee,IFNULL(e.emp_code,'-')as emp_code,
                      if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee, 
					  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
					  if(p.due_type='A' or p.due_type='P',sum(p.act_amount),sum(p.payment_amount)) as payment_amount,
					  p.metal_rate,
					  IFNULL(sum(p.metal_weight), '-') as metal_weight,
					  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
			          p.payment_type,
					  p.payment_mode as payment_mode,
					  IFNULL(sa.scheme_acc_number,'') as msno,
					  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
					  IFNULL(p.bank_name,'-')as bank_name,
					  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
					  IFNULL(p.bank_branch,'-') as bank_branch,
					  IFNULL(p.ref_trans_id,'-') as id_transaction,
					  IFNULL(p.payu_id,'-') as payu_id ,
					  IFNULL(p.card_no,'-') as card_no,
					  psm.payment_status as payment_status,
					  p.payment_status as id_status,
					  psm.color as status_color,
					  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
					  IFNULL(p.remark,'-') as remark,
					  IFNULL(p.add_charges,'-') as bank_charges
				FROM payment p
				left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
				Left Join employee e On (e.id_employee=p.id_employee)
				Left Join customer c on (sa.id_customer=c.id_customer)
				left join scheme s on(sa.id_scheme=s.id_scheme)
			    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)		
			    Left Join gateway g on (g.id_pg=p.id_payGateway)
			    Left Join branch b on (b.id_branch=sa.id_branch)
			    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
       Where (date(p.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')  
       AND p.date_payment <= DATE_SUB('" . date('Y-m-d H:i:s') . "', INTERVAL 3 MINUTE) 
       " . ($id_company != 0 && $company_settings == 1 ? "and c.id_company=" . $id_company . "" : '') . "
       AND p.id_payGateway > 0 and g.pg_code=" . $pg_code . " AND p.payment_type!='Manual' And (p.payment_status='3' OR p.payment_status='7' OR p.payment_status='4') 
       " . ($uid != 1 ? ($branch_settings == 1 ? ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : " ") : '') : ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : '')) . "
			   GROUP BY p.ref_trans_id ORDER BY p.id_payment DESC " . ($limit != NULL ? " LIMIT " . $limit . " OFFSET " . $limit : " ");
        //print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    function payment_online($pg_code = "")
    {
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $is_branchwise_cus_reg = $this->session->userdata('is_branchwise_cus_reg');
        $id_branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $sql = "SELECT
					  p.id_payment,p.ref_trans_id,
					  sa.account_name,
					  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
					  c.mobile,
					IFNULL(sa.group_code,'')as group_code,cs.has_lucky_draw, is_lucky_draw,
					 IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
					  s.code,
					  p.id_employee,IFNULL(e.emp_code,'-')as emp_code,
                      if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee, 
					  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
					  if(p.due_type='A' or p.due_type='P',sum(p.act_amount),sum(p.payment_amount)) as payment_amount,
					  p.metal_rate,
					  IFNULL(sum(p.metal_weight), '-') as metal_weight,
					   IFNULL(p.add_charges,'-') as bank_charges,
					  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
			          p.payment_type,
					  p.payment_mode as payment_mode,
					  IFNULL(sa.scheme_acc_number,'') as msno,
					  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
					  IFNULL(p.bank_name,'-')as bank_name,
					  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
					  IFNULL(p.bank_branch,'-') as bank_branch,
					  IFNULL(p.ref_trans_id,'-') as id_transaction,
					  IFNULL(p.payu_id,'') as payu_id ,
					  IFNULL(p.card_no,'-') as card_no,
					  psm.payment_status as payment_status,
					  p.payment_status as id_status,
					  psm.color as status_color,
					  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
					  IFNULL(p.remark,'-') as remark
				FROM payment p
				left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
				Left Join employee e On (e.id_employee=p.id_employee)
				Left Join customer c on (sa.id_customer=c.id_customer)
				left join scheme s on(sa.id_scheme=s.id_scheme)
			    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)		
			    Left Join branch b on (b.id_branch=sa.id_branch)		
			    Left Join gateway g on (g.id_pg=p.id_payGateway)		
			    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
			    join chit_settings cs
       Where g.pg_code=" . $pg_code . " And (p.payment_status='3' OR p.payment_status='7' OR p.payment_status='4') " . ($uid != 1 ? ($branchWiseLogin == 1 || $is_branchwise_cus_reg == 1 ? ($id_branch != '' ? "and b.id_branch=" . $id_branch . " or  b.show_to_all=1 " : '') : '') : '') . "
			   GROUP BY p.ref_trans_id ORDER BY p.id_payment DESC ";
        // ORDER BY p.id_payment DESC ".($limit!=NULL? " LIMIT ".$limit." OFFSET ".$limit : " ");
        return $this->db->query($sql)->result_array();
    }
    function onlinePayments_range($from_date, $to_date, $limit = "", $date_type, $settle = "")
    {
        $branch_settings = $this->session->userdata('branch_settings');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = '';
        }
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $sql = "SELECT
					s.code,IFNULL(sa.group_code,'')as scheme_group_code,cs.has_lucky_draw,  s.is_lucky_draw,
					  p.id_payment,p.ref_trans_id,if(sa.ref_no='' ,null ,ifnull(sa.ref_no,null)) as client_id,s.free_payment,sa.is_new,
					  sa.account_name,
					  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
					  c.mobile,p.mer_net_amount as net_amt,p.mer_service_fee as service_fee,p.igst,is_settled,gateway_requestaction,
					  IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,p.act_amount,p.no_of_dues,
					  s.code,
					  p.id_employee,IFNULL(e.emp_code,'-')as emp_code,
                      if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee, 
					  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
					  IFNULL(p.payment_amount,'-') as payment_amount,
					  p.metal_rate,
					  IFNULL(p.metal_weight, '-') as metal_weight,
					  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
					  (select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or s.scheme_type=3, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=p.id_scheme_account group by pay.id_scheme_account)
					   as paid_installments,
			          p.payment_type,
					  p.payment_mode as payment_mode,
					  IFNULL(sa.scheme_acc_number,'') as msno,
					  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
					  IFNULL(p.bank_name,'-')as bank_name,
					  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
					  IFNULL(p.bank_branch,'-') as bank_branch,
					  IFNULL(p.id_transaction,'-') as id_transaction,
					  IFNULL(p.payu_id,'-') as payu_id ,
					  IFNULL(p.card_no,'-') as card_no,
					  psm.payment_status as payment_status,
					  p.payment_status as id_status,
					  psm.color as status_color,
					  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
					  IFNULL(p.remark,'-') as remark
				FROM payment p
				join chit_settings cs
				left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
				Left Join employee e On (e.id_employee=p.id_employee)
				Left Join customer c on (sa.id_customer=c.id_customer)
				left join scheme s on(sa.id_scheme=s.id_scheme)
			    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)		
			    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
				Left join branch b on (b.id_branch=p.id_branch)
                   WHERE (DATE(" . ($date_type != '' ? ($date_type == 2 ? "p.custom_entry_date" : "p.date_payment") : "p.date_payment") . ") BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
					" . ($settle == 1 ? " AND p.payment_type != 'Manual' AND (p.payment_status = 2 OR p.payment_status = 4) AND p.is_settled = 1" : ($settle == 2 ? " AND p.payment_type != 'Manual' AND p.payment_status = 2 AND p.is_settled = 0" : '')) . "
					" . ($uid != 1 ? ($branch_settings == 1 ? ($id_branch != 0 && $id_branch != '' ? "AND p.id_branch = " . $id_branch : " AND ( b.show_to_all = 1 OR b.show_to_all = 3)") : '') : ($id_branch != 0 && $id_branch != '' ? "AND p.id_branch = " . $id_branch : '')) . "
					" . ($id_company != 0 && $company_settings == 1 ? "AND c.id_company = " . $id_company : '') . "
				ORDER BY p.id_payment DESC " . ($limit != NULL ? " LIMIT " . $limit . " OFFSET " . $limit : " ");
        return $this->db->query($sql)->result_array();
    }
    function onlinePayments($limit = "", $settle = "")
    {
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $uid = $this->session->userdata('uid');
        $id_branch = $this->session->userdata('id_branch');
        $sql = "SELECT
					s.code,IFNULL(sa.group_code,'')as scheme_group_code,cs.has_lucky_draw,
					  p.id_payment,p.ref_trans_id,if(sa.ref_no='',null ,ifnull(sa.ref_no,null)) as client_id,s.free_payment,sa.is_new,
					  sa.account_name,
					  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
					  c.mobile,p.mer_net_amount as net_amt,p.mer_service_fee as service_fee,p.igst,is_settled,gateway_requestaction,
					   IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
					  s.code,
					  p.id_employee,p.act_amount,p.no_of_dues,
                      if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee,IFNULL(e.emp_code,'-')as emp_code,
					  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
					  IFNULL(p.payment_amount,'-') as payment_amount,
					  p.metal_rate,
					  IFNULL(p.metal_weight, '-') as metal_weight,
					  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
					  (select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or s.scheme_type=3, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=p.id_scheme_account group by pay.id_scheme_account)
					   as paid_installments,
			          p.payment_type,
					  p.payment_mode as payment_mode,
					  IFNULL(sa.scheme_acc_number,'') as msno,
					  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
					  IFNULL(p.bank_name,'-')as bank_name,
					  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
					  IFNULL(p.bank_branch,'-') as bank_branch,
					  IFNULL(p.id_transaction,'-') as id_transaction,
					  IFNULL(p.payu_id,'-') as payu_id ,
					  IFNULL(p.card_no,'-') as card_no,
					  psm.payment_status as payment_status,
					  p.payment_status as id_status,
					  psm.color as status_color,
					  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
					  IFNULL(p.remark,'-') as remark
				FROM payment p
				join chit_settings cs
				left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
				Left Join employee e On (e.id_employee=p.id_employee)
				Left Join customer c on (sa.id_customer=c.id_customer)
				left join scheme s on(sa.id_scheme=s.id_scheme)
			    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)
			       Left Join branch b on (sa.id_branch=b.id_branch)			
			    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
       Where     " . ($settle == 1 ? "  p.payment_type!='Manual' And (p.payment_status=2 or p.payment_status=4) and p.is_settled=1  " : ($settle == 2 ? " p.payment_type!='Manual' And p.payment_status=2  and p.is_settled=0  " : '')) . " 
       " . ($uid != 1 ? ($branch_settings == 1 ? ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : " and (p.id_branch=" . $id_branch . " or b.show_to_all=1 or b.show_to_all=3)") : '') : ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : '')) . "
			   ORDER BY p.id_payment DESC " . ($limit != NULL ? " LIMIT " . $limit . " OFFSET " . $limit : " ");
        //	print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    // settled pay show in payment apprval page with filter//HH	     
    function getData_matchedRefno($refno)
    {
        $sql = "SELECT id_payment from payment where due_type='S' and  payment_type='Payu Checkout' and payment_ref_number=" . $refno;
        $splitted = $this->db->query($sql)->result_array();
        $query = "SELECT id_payment from payment where due_type!='S' and  payment_type='Payu Checkout' and payment_ref_number=" . $refno;
        $origPay = $this->db->query($query)->row('id_payment');
        return array('splittedId' => $splitted, 'parentId' => $origPay);
    }
    function paymentDB($type = "", $id = "", $pay_array = "")
    {
        switch ($type) {
            case 'get':
                $sql = "Select
			      		s.code,IFNULL(sa.group_code,'')as scheme_group_code,cs.has_lucky_draw, s.is_lucky_draw,allow_referral,s.id_scheme,p.redeemed_amount,
						  p.id_payment,s.gst,s.gst_type,is_point_credited,iwa.available_points,sa.id_branch as branch,s.code as group_code,s.sync_scheme_code,
						  ifnull(iwa.mobile,0) as isAvail,c.mobile,redeemed_amount,actual_trans_amt,s.get_amt_in_schjoin,
						  p.id_scheme_account,cs.scheme_wise_acc_no,
						concat(if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,sa.group_code,s.code),' ', IFNULL(sa.scheme_acc_number,'Not Allocated')) as scheme_acc_number,
						  sa.account_name,p.receipt_no,
						  p.id_transaction,
						  p.payu_id,p.due_type,
						  p.id_post_payment,p.act_amount,if(p.payment_type='Payu Checkout' and (p.due_type='A' or p.due_type='P') and p.payment_status!=1,'Y','N' ) as showPaid,
						  p.id_drawee,
						  da.account_no as drawee_acc_no,
						  da.account_name as drawee_account_name,
						  Date_format(date_payment,'%d-%m-%Y') as date_payment,
						  Date_format(p.custom_entry_date,'%Y-%m-%d') as custom_entry_date,
						  p.payment_type,p.no_of_dues,
						  p.payment_mode,
						  p.payment_amount,
						  p.add_charges,(select charges FROM postdate_payment WHERE id_post_payment=p.id_post_payment) as charges,
						  p.metal_rate,
						  p.metal_weight,
						  p.cheque_date,
						  p.cheque_no,
						  p.bank_acc_no,
						  p.bank_name,
						  p.bank_branch,
						  p.bank_IFSC,
						  p.card_no,
						  p.card_holder,
						  p.cvv,
						  p.exp_date,
						  p.payment_ref_number,
						  p.payment_status as id_payment_status,
						  p.remark,
						  psm.payment_status as payment_status,
						  psm.color as status_color,
						  cs.receipt_no_set,cs.schemeacc_no_set,IFNULL(sa.scheme_acc_number,'') as acc_no,
						   p.remark,if((cs.receipt_no_set= 1 && p.payment_status =1 ),p.receipt_no,if((cs.receipt_no_set= 0 && p.payment_status =1),p.receipt_no,'')) as receipt_no,
						   cs.edit_custom_entry_date,cs.custom_entry_date,s.firstPayamt_as_payamt,s.firstPayamt_maxpayable as firstPayamt_payable
						From payment p
						Join chit_settings cs
						Left Join scheme_account sa on (p.id_scheme_account=sa.id_scheme_account)
						Left Join scheme s on (s.id_scheme=sa.id_scheme)
						Left Join drawee_account da on (p.id_drawee=p.id_drawee)
						Left Join payment_mode pm on (p.payment_mode=pm.id_mode)
						Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
Left Join customer c on (c.id_customer=sa.id_customer)
						LEFT JOIN inter_wallet_account iwa on iwa.mobile=c.mobile
						Where p.id_payment=" . $id;
                //print_r($sql);exit;
                return $this->db->query($sql)->row_array();
                break;
            case 'insert':
                $status = $this->db->insert(self::PAY_TABLE, $pay_array);
                // print_r($this->db->last_query());exit;
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'general_advance_insert':
                $status = $this->db->insert('general_advance_payment', $pay_array);
                // print_r($this->db->last_query());exit;
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $this->db->where("id_payment", $id);
                $status = $this->db->update(self::PAY_TABLE, $pay_array);
                return array('status' => $status, 'updateID' => $id);
                break;
            case 'updatestatus':
                $this->db->where("ref_trans_id", $id);
                $status = $this->db->update(self::PAY_TABLE, $pay_array);
                return array('status' => $status, 'updateID' => $id);
                break;
            case 'delete':
                $this->db->where("id_payment", $id);
                $status = $this->db->delete(self::PAY_TABLE);
                return array('status' => $status, 'DeleteID' => $id);
                break;
            default:
                return array(
                    'id_payment' => NULL,
                    'id_scheme_account' => NULL,
                    'date_payment' => date('Y-m-d'),
                    'payment_type' => "Manual",
                    'payment_mode' => NULL,
                    'payment_status' => NULL,
                    'payment_amount' => 0.00,
                    'metal_rate' => 0.00,
                    'metal_weight' => 0.000,
                    'payment_ref_number' => NULL,
                    'remark' => NULL,
                    'edit_addpay_page' => $this->checkSettings(),
                    'allow_wallet' => $this->allow_wallet(),
                    'useWalletForChit' => $this->wallet(),
                    'isOTPRegForPayment' => $this->isOTPRegForPayment(),
                    'allow_preclose' => 0,
                    'pdc' => array(
                        'date_payment' => NULL,
                        'cheque_no' => NULL,
                        'payee_bank' => NULL,
                        'payee_branch' => NULL,
                        'payee_ifsc' => NULL,
                    )
                );
        }
    }
    function cost_center()
    {
        $sql = "Select cost_center from chit_settings where id_chit_settings = 1";
        return $this->db->query($sql)->row()->cost_center;
    }
    function checkSettings()
    {
        $sql = "Select edit_addpay_page from chit_settings where id_chit_settings = 1";
        return $this->db->query($sql)->row()->edit_addpay_page;
    }
    function allow_wallet()
    {
        $sql = "Select allow_wallet from chit_settings where id_chit_settings = 1";
        return $this->db->query($sql)->row()->allow_wallet;
    }
    function wallet()
    {
        $sql = "Select useWalletForChit from chit_settings where id_chit_settings = 1";
        return $this->db->query($sql)->row()->useWalletForChit;
    }
    function payment_statusDB($type = "", $id = "", $pay_array = "")
    {
        switch ($type) {
            case 'get':
                $sql = "Select * from payment_status where id_payment_status=" . $id;
                return $this->db->query($sql)->row_array();
                break;
            case 'insert':
                $status = $this->db->insert("payment_status", $pay_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $this->db->where("id_payment_status", $id);
                $status = $this->db->update("payment_status", $pay_array);
                return array('status' => $status, 'updateID' => $id);
                break;
            case 'delete':
                $this->db->where("id_payment_status", $id);
                $status = $this->db->delete("payment_status");
                return array('status' => $status, 'DeleteID' => $id);
                break;
        }
    }
    function post_paymentlist($id = "", $limit = "")
    {
        $sql = "SELECT
			      pp.id_post_payment,s.code,
			      IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
			      sa.id_scheme_account,
			      sa.account_name,
			      c.mobile,c.email,c.lastname,c.firstname,s. scheme_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,
			      pp.pay_mode,
			      Date_format(pp.date_payment,'%d-%m%-%Y') as date_payment,
			      pp.cheque_no,
			      pp.payee_acc_no,
			      pb.bank_name as payee_bank,
                  pb.short_code as payee_short_code,
                  pp.payee_branch,
                  pp.payee_ifsc,
			      pp.id_drawee,
                  da.account_no as drawee_acc_no,
                  da.account_name as drawee_account_name,
                  db.bank_name as drawee_bank,
                  db.short_code as drawee_short_code,
                  da.branch as drawee_branch,
                  da.ifsc_code as drawee_ifsc,
			      pp.amount,
			      pp.payment_status as id_payment_status,
			      psm.payment_status,
			      psm.color as status_color,cs.currency_symbol,cs.currency_name
			FROM postdate_payment pp
			Left Join scheme_account sa on (pp.id_scheme_account=sa.id_scheme_account)
			Left Join payment_status_message psm on (pp.payment_status=psm.id_status_msg)
            Left Join drawee_account da on (pp.id_drawee=da.id_drawee)
            Left Join bank db on (da.id_bank=db.id_bank)
            Left Join bank pb on (pp.payee_bank=pb.id_bank)
            Left Join customer c on (sa.id_customer=c.id_customer)
            Left join scheme s on(sa.id_scheme=s.id_scheme)
            join chit_settings cs 
			Where pp.payment_status!=1 " . ($id != null ? ' and pp.id_post_payment=' . $id : '') . " 
			ORDER BY pp.date_payment DESC " . ($limit != NULL ? " LIMIT " . $limit : " ");
        $payment = $this->db->query($sql);
        if ($id != NULL) {
            return $payment->row_array();
        } else {
            return $payment->result_array();
        }
    }
    function pdc_detail_all($status)
    {
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $sql = "SELECT
			      pp.id_post_payment,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,
			      IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
			      IFNULL(sa.group_code,'Not Allocated')as scheme_group_code,
			      cs.has_lucky_draw, s.is_lucky_draw,s.code,
			      sa.id_scheme_account,
			      sa.account_name,
			      pp.pay_mode,
			      Date_format(pp.date_payment,'%d-%m%-%Y') as date_payment,
			      pp.cheque_no,
			      pp.payee_acc_no,
			      pb.bank_name as payee_bank,
            	  pb.short_code as payee_short_code,
                  pp.payee_branch,
                  pp.payee_ifsc,
			      pp.id_drawee,
                  da.account_no as drawee_acc_no,
            	  da.account_name as drawee_account_name,
            	  db.bank_name as drawee_bank,
            	  db.short_code as drawee_short_code,
                  da.branch as drawee_branch,
            	  da.ifsc_code as drawee_ifsc,
			      pp.amount,
			      pp.payment_status as id_payment_status,
			      psm.payment_status,
			      psm.color as status_color,
			      pp.charges
			FROM postdate_payment pp
			Left Join scheme_account sa on (pp.id_scheme_account=sa.id_scheme_account)
			left join scheme s on(s.code=sa.group_code)
			Left Join customer c on (c.id_customer=sa.id_customer)
			Left Join payment_status_message psm on (pp.payment_status=psm.id_status_msg)
		    Left Join drawee_account da on (pp.id_drawee=da.id_drawee)
		    Left Join bank db on (da.id_bank=db.id_bank)
		    Left Join bank pb on (pp.payee_bank=pb.id_bank)
		    join chit_settings cs
		    Where pp.payment_status='" . $status . "' 
		     And Date(pp.`date_payment`) <= CURDATE() 
		     " . ($id_company != '' && $company_settings == 1 ? " and s.id_company='" . $id_company . "'" : '') . "
		    Order By pp.pay_mode ASC";
        $r = $this->db->query($sql);
        return $r->result_array();
    }
    function pdc_report_detail($filterBy, $mode, $status)
    {
        $sql = "SELECT
			      pp.id_post_payment,
			      IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
			      sa.id_scheme_account,
			      sa.account_name,
			      pp.pay_mode,
			      Date_format(pp.date_payment,'%d-%m%-%Y') as date_payment,
			      pp.cheque_no,
			      pp.payee_acc_no,
			      pb.bank_name as payee_bank,
            	  pb.short_code as payee_short_code,
                  pp.payee_branch,
                  pp.payee_ifsc,
			      pp.id_drawee,
                  da.account_no as drawee_acc_no,
            	  da.account_name as drawee_account_name,
            	  db.bank_name as drawee_bank,
            	  db.short_code as drawee_short_code,
                  da.branch as drawee_branch,
            	  da.ifsc_code as drawee_ifsc,
			      pp.amount,
			      pp.payment_status as id_payment_status,
			      psm.payment_status,
			      psm.color as status_color,
			      pp.charges
			FROM postdate_payment pp
			Left Join scheme_account sa on (pp.id_scheme_account=sa.id_scheme_account)
			Left Join payment_status_message psm on (pp.payment_status=psm.id_status_msg)
		    Left Join drawee_account da on (pp.id_drawee=da.id_drawee)
		    Left Join bank db on (da.id_bank=db.id_bank)
		    Left Join bank pb on (pp.payee_bank=pb.id_bank)
		    Where pp.pay_mode='" . $mode . "' And pp.payment_status=" . $status;
        switch (strtoupper($filterBy)) {
            case 'Y': //yesterday
                $sql = $sql . " And (Date(pp.`date_payment`) = (CURDATE() - INTERVAL 1 DAY)) ";
                break;
            case 'T': //Today
                $sql = $sql . " And (Date(pp.`date_payment`) = CURDATE())";
                break;
            case 'TT': //Till today
                $sql = $sql . " And Date(pp.`date_payment`) <= CURDATE() ";
                break;
            case 'LW': //Last Week
                $sql = $sql . " And (Date(`date_payment`) BETWEEN ((CURDATE() - INTERVAL DAYOFWEEK(CURDATE()) + 6 DAY) AND (CURDATE() - INTERVAL DAYOFWEEK(CURDATE()) - 1 DAY)))";
                break;
            case 'TW': //This Week
                $sql = $sql . " And (pp.Date(`date_payment`) BETWEEN DATE_ADD(CURDATE(), INTERVAL 1-DAYOFWEEK(CURDATE()) DAY) AND DATE_ADD(CURDATE(), INTERVAL 7 - DAYOFWEEK(CURDATE()) DAY))";
                break;
            case 'TM': //This Month
                $sql = $sql . " And (pp.Date(`date_payment`) BETWEEN DATE_SUB(CURDATE(),INTERVAL (DAY(CURDATE())-1) DAY) AND LAST_DAY(NOW())";
                break;
            case 'ALL':
                $sql = $sql . " ";
                break;
        }
        $sql = $sql . " Order By pp.date_payment ASC ";
        $r = $this->db->query($sql);
        return $r->result_array();
    }
    function post_payment_statuslog($id)
    {
        $sql = "Select
			      ps.id_post_payment,
			      psm.payment_status,
					  psm.color as status_color,
			      if(e.lastname=null,concat(e.firstname,' ',e.lastname),e.firstname) as username,
			      ps.charges,
			      ps.date_upd
			From payment_status ps
			Left join employee e on(ps.id_employee=e.id_employee)
			Left Join payment_status_message psm on (ps.id_status_msg=psm.id_status_msg)
			Where ps.id_post_payment=" . $id;
        return $this->db->query($sql)->result_array();
    }
    function post_paymentlist_range($from_date, $to_date)
    {
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = '';
        }
        $sql = "SELECT
			      pp.id_post_payment,
			     IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
			      sa.id_scheme_account,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,
			      sa.account_name,s.code,
			      pp.pay_mode,
			      Date_format(pp.date_payment,'%d-%m%-%Y') as date_payment,
			      pp.cheque_no,
			      pp.payee_acc_no,
			      pb.bank_name as payee_bank,
           		  pb.short_code as payee_short_code,
            	  pp.payee_branch,
                  pp.payee_ifsc,
			      pp.id_drawee,
                             da.account_no as drawee_acc_no,
                  da.account_name as drawee_account_name,
            	 db.bank_name as drawee_bank,
            	 db.short_code as drawee_short_code,
           	 	 da.branch as drawee_branch,
            	  da.ifsc_code as drawee_ifsc,
			      pp.amount,
			      pp.payment_status as id_payment_status,
			      psm.payment_status,
			      psm.color as status_color
			FROM postdate_payment pp
			Left Join scheme_account sa on (pp.id_scheme_account=sa.id_scheme_account)
			Left Join scheme s on (sa.id_scheme = s.id_scheme)
			Left Join payment_status_message psm on (pp.payment_status=psm.id_status_msg)
      Left Join drawee_account da on (pp.id_drawee=da.id_drawee)
        Left Join customer c on (sa.id_customer=c.id_customer)
      Left Join bank db on (da.id_bank=db.id_bank)
      Left Join bank pb on (pp.payee_bank=pb.id_bank)
			Where pp.payment_status!=1 and(date(pp.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')  
			" . ($id_branch != NULL ? ' and sa.id_branch =' . $id_branch : '') . "
			   ORDER BY pp.id_post_payment ASC";
        return $this->db->query($sql)->result_array();
    }
    function get_postpayment($id_scheme_account)
    {
        $sql = "SELECT
			      pp.id_post_payment,
			     IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
			      sa.id_scheme_account,
			      sa.account_name,
			      pp.pay_mode,
			      Date_format(pp.date_payment,'%d-%m%-%Y') as date_payment,
			      pp.cheque_no,
			      pp.payee_acc_no,
			      pb.bank_name as payee_bank,
           		  pb.short_code as payee_short_code,
            	  pp.payee_branch,
                  pp.payee_ifsc,
			      pp.id_drawee,
                  da.account_no as drawee_acc_no,
                  da.account_name as drawee_account_name,
            	 db.bank_name as drawee_bank,
            	 db.short_code as drawee_short_code,
           	 	 da.branch as drawee_branch,
            	 da.ifsc_code as drawee_ifsc,
			     pp.amount,
			     pp.metal_rate,
			      pp.weight,
			     pp.payment_status as id_payment_status,
			     psm.payment_status,
			     psm.color as status_color,
			     pp.charges
			FROM postdate_payment pp
			Left Join scheme_account sa on (pp.id_scheme_account=sa.id_scheme_account)
			Left Join payment_status_message psm on (pp.payment_status=psm.id_status_msg)
		    Left Join drawee_account da on (pp.id_drawee=da.id_drawee)
		    Left Join bank db on (da.id_bank=db.id_bank)
		    Left Join bank pb on (pp.payee_bank=pb.id_bank)
			Where pp.payment_status!=1 and date(pp.date_payment) <= date(curdate()) and  pp.id_scheme_account='" . $id_scheme_account . "'";
        $payment = $this->db->query($sql);
        $data = array(
            'total' => $payment->num_rows(),
            'data' => $payment->result_array()
        );
        return $data;
    }
    function postdated_paymentByID($id)
    {
        $sql = "SELECT
			      pp.id_post_payment,
			     IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
			      sa.id_scheme_account,
			      sa.account_name,
			      pp.pay_mode,
			      Date_format(pp.date_payment,'%d-%m%-%Y') as date_payment,
			      pp.cheque_no,
			      pp.payee_acc_no,
			      pb.bank_name as payee_bank,
           		  pb.short_code as payee_short_code,
            	  pp.payee_branch,
                  pp.payee_ifsc,
			      pp.id_drawee,
                             da.account_no as drawee_acc_no,
            da.account_name as drawee_account_name,
            	 db.bank_name as drawee_bank,
            	 db.short_code as drawee_short_code,
           	 	 da.branch as drawee_branch,
            	  da.ifsc_code as drawee_ifsc,
			      pp.amount,
			      pp.metal_rate,
			      pp.weight,
			        pp.payment_status as id_payment_status,
			      psm.payment_status,
			      psm.color as status_color,
			      pp.charges,
			      pp.date_presented
			FROM postdate_payment pp
			Left Join scheme_account sa on (pp.id_scheme_account=sa.id_scheme_account)
			Left Join payment_status_message psm on (pp.payment_status=psm.id_status_msg)
      Left Join drawee_account da on (pp.id_drawee=da.id_drawee)
      Left Join bank db on (da.id_bank=db.id_bank)
      Left Join bank pb on (pp.payee_bank=pb.id_bank)
      Where  pp.id_post_payment=" . $id;
        return $this->db->query($sql)->row_array();
    }
    function postdated_paymentDB($type, $id = "", $pay_array = "")
    {
        switch ($type) {
            case 'get':
                $sql = "SELECT
			      pp.id_post_payment,
			      IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
			      sa.id_scheme_account,
			      sa.account_name,
			      pp.pay_mode,cs.scheme_wise_acc_no,s.is_lucky_draw,
			      Date_format(pp.date_payment,'%d-%m%-%Y') as date_payment,
			      pp.cheque_no,
			      pp.payee_acc_no,
			      pb.bank_name as payee_bank,
           		  pb.short_code as payee_short_code,
            	  pp.payee_branch,
                  pp.payee_ifsc,
			      pp.id_drawee,
                  da.account_no as drawee_acc_no,
                  da.account_name as drawee_account_name,
            	 db.bank_name as drawee_bank,
            	 db.short_code as drawee_short_code,
           	 	 da.branch as drawee_branch,
            	  da.ifsc_code as drawee_ifsc,
			      pp.amount,
			      pp.metal_rate,
			      pp.weight,
			        pp.payment_status as id_payment_status,
			      psm.payment_status,
			      psm.color as status_color,
			      pp.charges,
			      pp.date_presented
			FROM postdate_payment pp
			Join chit_settings cs
			Left Join scheme_account sa on (pp.id_scheme_account=sa.id_scheme_account)
			Left Join payment_status_message psm on (pp.payment_status=psm.id_status_msg)
			Left Join scheme s on (s.id_scheme=sa.id_scheme)
      Left Join drawee_account da on (pp.id_drawee=da.id_drawee)
      Left Join bank db on (da.id_bank=db.id_bank)
      Left Join bank pb on (pp.payee_bank=pb.id_bank)
						Where pp.payment_status!=1 " . ($id != NULL ? ' And  pp.id_post_payment=' . $id : '');
                if ($id != NULL) {
                    return $this->db->query($sql)->row_array();
                } else {
                    return $this->db->query($sql)->result_array();
                }
                break;
            case 'insert':
                //print_r($pay_array);exit;
                $status = $this->db->insert("postdate_payment", $pay_array);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $this->db->where("id_post_payment", $id);
                $status = $this->db->update("postdate_payment", $pay_array);
                return array('status' => $status, 'updateID' => $id);
                break;
            case 'delete':
                $this->db->where("id_post_payment", $id);
                $status = $this->db->delete("postdate_payment");
                return array('status' => $status, 'DeleteID' => $id);
                break;
        }
    }
    function payment_log($id)
    {
        $sql = "Select
			      ps.id_post_payment,
			      psm.payment_status,
					  psm.color as status_color,
			      if(e.lastname=null,concat(e.firstname,' ',e.lastname),e.firstname) as username,
			      ps.charges,
			      Date_Format(ps.date_upd,'%d-%m-%Y %H:%m:%s') as date_upd
			From payment_status ps
			Left join employee e on(ps.id_employee=e.id_employee)
			Left Join payment_status_message psm on (ps.id_status_msg=psm.id_status_msg)
			Where ps.id_payment=" . $id . " Order By ps.id_post_payment Desc";
        return $this->db->query($sql)->result_array();
    }
    function post_payment_log($id)
    {
        $sql = "Select
			      ps.id_post_payment,
			      psm.payment_status,
					  psm.color as status_color,
			      if(e.lastname=null,concat(e.firstname,' ',e.lastname),e.firstname) as username,
			      ps.charges,
			      Date_Format(ps.date_upd,'%d-%m-%Y %H:%m:%s') as date_upd
			From payment_status ps
			Left join employee e on(ps.id_employee=e.id_employee)
			Left Join payment_status_message psm on (ps.id_status_msg=psm.id_status_msg)
			Where ps.id_post_payment=" . $id . " Order By ps.id_post_payment Desc";
        return $this->db->query($sql)->result_array();
    }
    function get_payment_status()
    {
        return $this->db->query("Select * from payment_status_message")->result_array();
    }
    function get_customer_schemes($id_customer)
    {
        //old code 05-12-2022 $sql = "Select sa.id_scheme_account, if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,concat(concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Not Allocated')),' - ',s.code ),concat(s.code,' ',ifnull(sa.scheme_acc_number,'Not Allcoated')))as scheme_acc_number From scheme_account sa Left join scheme s on sa.id_scheme=s.id_scheme Left join branch  b on b.id_branch=sa.id_branch join chit_settings cs Where sa.active=1 and sa.is_closed=0 and sa.id_customer=".$id_customer;
        //New code 05-12-2022 $sql = "Select sa.id_scheme_account,if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,concat(concat(ifnull(sa.group_code,''),' ',ifnull( concat(sa.start_year,'-',sa.scheme_acc_number),'Not Allocated')),' - ',s.code ),concat(s.code,' ',ifnull(concat(sa.start_year,'-',sa.scheme_acc_number),'Not Allcoated')))as scheme_acc_number From scheme_account sa Left join scheme s on sa.id_scheme=s.id_scheme Left join branch  b on b.id_branch=sa.id_branch join chit_settings cs Where sa.active=1 and sa.is_closed=0 and sa.id_customer=".$id_customer;
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $branchwise_scheme = $this->session->userdata('branchwise_scheme');
        $id_branch = $this->session->userdata('id_branch');
        $id_employee = $this->session->userdata('uid');
        //RAHUL 04_12_25 To prevent payment for other branch accounts
        $where = '';
        if ($branchwise_scheme == 1 && $branchWiseLogin == 1 && $id_employee > 0 && $this->config->item('payOtherBranch') != 1 && $id_branch != 0) {
            /* $id_branches = $this->db->query("select login_branches from employee where id_employee = ".$id_employee)->row_array();
            $where = " and sa.id_branch in (".$id_branches['login_branches'].") "; */
            $id_branches = $this->db->query("SELECT login_branches FROM employee WHERE id_employee = " . $id_employee)->row_array();
            $login_branches = trim($id_branches['login_branches']);
            if (empty($login_branches)) {
                $where = '';
            } else {
                // Convert to array
                $branches = array_filter(array_map('trim', explode(',', $login_branches)));
                // If the array contains 0 → then no filter (return empty where)
                if (in_array('0', $branches)) {
                    $where = '';
                } else {
                    // Otherwise → apply the IN condition
                    $where = " AND sa.id_branch IN (" . implode(',', $branches) . ") ";
                }
            }
        }
        $sql = $this->db->query("Select s.has_gift,sa.id_scheme_account,
				if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,
                	concat(concat(ifnull(sa.group_code,''),' ',ifnull( concat(ifnull(concat(sa.start_year,'-'),''),sa.scheme_acc_number),'Not Allocated')),' - ',s.code ),
                	concat(s.code,' ',ifnull(concat(ifnull(concat(sa.start_year,'-'),''),sa.scheme_acc_number),'Not Allcoated'))
                )as old_scheme_acc_number,
				IFNULL(sa.scheme_acc_number,'NOT ALLOCATED') as scheme_acc_number,
				IFNULL(sa.start_year,'') as start_year,
				cs.scheme_wise_acc_no,
				s.code,
				(select br.short_name from branch br where br.id_branch = sa.id_branch) as acc_branch,
				cs.schemeaccNo_displayFrmt,
				s.is_lucky_draw,
				IFNULL(sa.group_code,'') as group_code
        		From scheme_account sa
        		Left join scheme s on sa.id_scheme=s.id_scheme
				Left join branch  b on b.id_branch=sa.id_branch
        		join chit_settings cs 
				Where sa.active=1 and sa.is_closed=0 and sa.id_customer=" . $id_customer . $where);
        $result = [];
        $payment = $sql->result_array();
        if ($sql->num_rows() > 0) {
            foreach ($payment as $rcpt) {
                $rcpt['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $rcpt['id_scheme_account']);
                //$rcpt['receipt_no'] = $this->customer_model->format_accRcptNo('Receipt',$rcpt['id_payment']);
                $result[] = $rcpt;
            }
        }
        return $result;
    }
    function get_customer_schemes_amount($id_customer)
    {
        $sql = "Select id_scheme_account,IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,s.scheme_type,s.code
		       From   scheme_account sa
		        left join scheme s on(sa.id_scheme=s.id_scheme)
		       Where  sa.active=1 and sa.is_closed=0 and s.scheme_type=0 and sa.id_customer=" . $id_customer;
        return $this->db->query($sql)->result_array();
    }
    function get_account_detail($id)
    {
        $accounts = $this->db->query("Select
										IFNULL(concat(s.code,'-',sa.start_year,'-',sa.scheme_acc_number),'Not Allocated') as scheme_acc_number,
										s.id_scheme,
										c.id_customer,IFNULL(sa.fixed_wgt,0) as fixed_wgt,
										IF(c.lastname IS NULL,c.firstname,CONCAT(c.firstname,' ',c.lastname)) customer_name,
										IFNULL(sa.account_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,
										c.mobile,
										s.scheme_name,
										if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) AS scheme_type,
										s.code,
										IFNULL(s.min_chance,0) as min_chance,
										IFNULL(s.max_chance,0) as max_chance,
										Format(IFNULL(s.max_weight,0),3) as max_weight,
										Format(IFNULL(s.min_weight,0),3) as min_weight,
										Date_Format(sa.start_date,'%d-%m-%Y')start_date,
										IF(s.scheme_type=1,s.max_weight,s.amount) as payable,
										s.total_installments,
										IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
										as paid_installments,
										IFNULL(IF(sa.is_opening=1,IFNULL(balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0)
										as total_paid_amount,
										IFNULL(IF(sa.is_opening=1,IFNULL(balance_weight,0)+IFNULL(SUM(p.metal_weight),0),IFNULL(SUM(p.metal_weight),0)),0.000)
										as total_paid_weight,
										ROUND(IFNULL(cp.total_amount,0),2) as  current_paid_amount,
				    					ROUND(IFNULL(cp.total_weight,0),3) as  current_paid_weight,
				    					IFNULL(cp.paid_installment,0)       as  current_paid_installments,
				    					IFNULL(cp.chances,0)                as  current_chances_used,
										s.is_pan_required,
										 if(s.allow_unpaid=1,s.unpaid_months,0) as allow_unpaid_month,
										Date_Format(max(p.date_payment),'%d-%m-%Y') as last_paid_date,
										TIMESTAMPDIFF(MONTH, max(Date(p.date_payment)), Date(Current_Date())) as duration,
										sa.active as chit_active,
										sa.is_closed as is_closed,
										count(pp.id_post_payment) as pdc,
										Date_Format(max(pp.date_payment),'%d-%m-%Y') as last_pdc_date
									From scheme_account sa
									Left Join scheme s On (sa.id_scheme=s.id_scheme)
									Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and p.payment_status=1)
									Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)
										Left Join
													(	Select
														  sa.id_scheme_account,
														  COUNT(Distinct Date_Format(p.date_payment,'%Y%m')) as paid_installment,
														  COUNT(Date_Format(p.date_payment,'%Y%m')) as chances,
														  SUM(p.payment_amount) as total_amount,
														  SUM(p.metal_weight) as total_weight
														From payment p
														Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)
														Where p.payment_status=1 and  Date_Format(Current_Date(),'%Y%m')=Date_Format(p.date_payment,'%Y%m')
														Group By sa.id_scheme_account
													) cp On (sa.id_scheme_account=cp.id_scheme_account)
									 Left Join postdate_payment pp on (sa.id_scheme_account=pp.id_scheme_account AND pp.payment_status!=1)				
									Where sa.active=1 and sa.is_closed = 0 and sa.id_scheme_account=" . $id . "
									Group By sa.id_scheme_account");
        return $accounts->row_array();
    }
    function get_paymentContent($id_scheme_account, $payment_edit_date = '')   // esakki 11-11
    {
        //DGS-DCNM -->(SELECT SUM(p.payment_amount) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND date(p.date_payment) = curdate()) as curday_total_paid,s.daily_pay_limit, s.total_days_to_pay,DATEDIFF(CURDATE(),date(sa.start_date)) joined_date_diff, 
        //c.firstname new code 05-12-2022 is_pan
        $date = date('Y-m-d');
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $uid = $this->session->userdata('uid');
        $due_date_from = NULL;
        $due_date_to = NULL;
        $topup_slab_endday = 0;
        $topup_slab_startday = 0;
        $topup_slab_value = 0;
        $topup_slab_type = NULL;
        $topup_estimated_paid_amount = 0;
        $schemeAcc = array();
        $sql = "Select sa.topup_amount,sa.topup_weight,sa.topup_rate,s.is_topup_scheme, s.topup_booking_type,
        s.is_lumpSum,sa.lump_joined_weight,sa.lump_payable_weight,s.firstPayment_as_wgt,sa.firstpayment_wgt ,
		s.pay_duration,s.allow_general_advance,s.adv_min_amt,s.adv_max_amt,s.adv_denomination,s.installment_cycle,s.grace_days,s.avg_calc_by,
		date_format(date_add(date(sa.start_date),interval + (s.total_installments-1) month),'%Y%m') as daily_sch_allow_pay_till,
		DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY) as dg_allow_pay_till,
		s.maturity_type,s.interest,
		IFNULL((SELECT count(p.id_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1 AND date(p.date_payment) = curdate()),0) as curday_total_paid_count,
		IFNULL((SELECT count(p.id_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1 AND p.due_type = 'AD'),0) as total_adv_paid,
		IFNULL((SELECT count(p.id_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1 AND p.due_type = 'PD'),0) as total_pend_paid,
		s.firstPayment_as_wgt,s.amt_based_on,c.firstname, s.is_digi,IFNULL((SELECT SUM(p.payment_amount) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1 AND date(p.date_payment) = curdate()),0) as curday_total_paid,
		s.daily_pay_limit, s.total_days_to_pay,
		DATEDIFF(CURDATE(),date(sa.start_date)) + 1 as joined_date_diff,
		s.restrict_payment,
		c.reference_no, s.sync_scheme_code, c.nominee_name, c.nominee_relationship,c.nominee_address1,c.nominee_address2,c.nominee_mobile,(SELECT e.firstname FROM employee e where e.id_employee ='$uid') as emp_name,sa.referal_code,c.reference_no, s.sync_scheme_code,
		s.disable_pay,s.one_time_premium,s.wgt_store_as,s.disable_pay_amt,s.agent_refferal,s.emp_refferal,s.cus_refferal,s.set_as_min_from,s.set_as_max_from,s.no_of_dues as dues_count,sa.id_agent,sa.agent_code,s.one_time_premium,
					maturity_type,maturity_installment,disable_pay_reason,sg.group_code as scheme_group_code, UNIX_TIMESTAMP(Date_Format(sg.start_date,'%Y-%m-%d')) as group_start_date,  UNIX_TIMESTAMP(Date_Format(sg.end_date,'%Y-%m-%d')) as  group_end_date,  cs.has_lucky_draw,s.is_lucky_draw,s.firstPayamt_maxpayable,sa.firstPayment_amt,sa.firstpayment_wgt,sa.is_registered,s.flx_denomintion,s.flexible_sch_type,
					s.allowSecondPay,s.free_payment,s.get_amt_in_schjoin,s.firstPayamt_as_payamt,sa.maturity_date,s.maturity_days,b.name as name,sa.id_branch as id_branch,cs.cost_center,
					if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,
                    (s.total_installments - COUNT(payment_amount)),
                    ifnull((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) - SUM(p.no_of_dues),if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,s.total_installments,(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))))))
                    as missed_ins,sa.avg_payable,s.avg_calc_ins,
					PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')) as months_from_startdate,PERIOD_DIFF(if(YEAR(sa.maturity_date) != '0000',Date_Format(sa.maturity_date,'%Y%m'),Date_Format(sa.start_date,'%Y%m')),Date_Format(curdate(),'%Y%m')) as tot_ins,
					s.min_amt_chance,s.max_amt_chance,s.code,s.min_amount,c.mobile,
				   	sa.id_scheme_account,s.gst,s.gst_type,s.max_amount,
					sa.id_scheme,s.wgt_convert,if(s.cus_refferal=1 || s.emp_refferal=1,sa.referal_code,'')as referal_code,
					s.ref_benifitadd_ins_type,s.ref_benifitadd_ins,
					c.id_customer,sa.is_refferal_by,
					CONCAT(s.code,'-',IFNULL(sa.scheme_acc_number,'Not Allocated')) as chit_number,
					IFNULL(sa.account_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,
					s.scheme_name,s.discount_type,s.discount_installment,s.discount,s.firstPayDisc_value,s.firstPayDisc_by,
					s.scheme_type,
					if(scheme_type=3,if(s.max_amount!='',s.max_amount * s.total_installments,s.max_weight),s.amount)as scheme_overall_amount,
					IFNULL(s.min_chance,0) as min_chance,
					IFNULL(s.max_chance,0) as max_chance,
					Format(IFNULL(s.max_weight,0),3) as max_weight,
					Format(IFNULL(s.min_weight,0),3) as min_weight,
					Date_Format(sa.start_date,'%d-%m-%Y') as start_date,
					(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1) as metal_rate,
                    IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,if(flexible_sch_type = 3 ,  s.max_weight,if(sa.firstPayment_amt > 0,sa.firstPayment_amt ,s.min_amount)),0))) as payable,					s.total_installments,
					 IFNULL((select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 and s.payment_chances=1) , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=p.id_scheme_account group by pay.id_scheme_account),0) as oldpaid_installments,
					 ifnull(sa.total_paid_ins,0) as paid_installments,
                    IFNULL(IF(sa.is_opening=1,IFNULL(balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0) as total_paid_amount,IFNULL(SUM(p.saved_benefits), 0)as total_saved_benefit,
                    IFNULL(IF(sa.is_opening=1 and s.scheme_type!=0,IFNULL(balance_weight,0)+IFNULL(SUM(p.metal_weight),0),IFNULL(SUM(p.metal_weight),0)),0.000) as total_paid_weight,
                    if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,   (s.total_installments - if(sa.is_opening = 1,(count(DISTINCT((Date_Format(p.date_payment,'%Y%m'))))+sa.paid_installments),count(DISTINCT((Date_Format(p.date_payment,'%Y%m')))))),ifnull(((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))+1) - IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(COUNT(Distinct Date_Format(p.date_payment,'%Y%m')),0), COUNT(Distinct Date_Format(p.date_payment,'%Y%m'))),if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,s.total_installments,(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))))) as totalunpaid,
                    IFNULL(if(Date_Format(max(p.date_payment),'%Y%m') = Date_Format(curdate(),'%Y%m'), (select SUM(ip.no_of_dues) from payment ip where Date_Format(ip.date_payment,'%Y%m') = Date_Format(curdate(),'%Y%m') and sa.id_scheme_account = ip.id_scheme_account),IF(sa.is_opening=1, if(Date_Format(sa.last_paid_date,'%Y%m') = Date_Format(curdate(),'%Y%m'), 1,0),0)),0) as currentmonthpaycount,
                    (select count(pay.no_of_dues) from payment pay where pay.id_scheme_account= sa.id_scheme_account and pay.due_type='AD' and (pay.payment_status=1 or pay.payment_status=2)) as currentmonth_adv_paycount,
                    (select count(pay.no_of_dues) from payment pay where pay.id_scheme_account= sa.id_scheme_account and pay.due_type='PD' and (pay.payment_status=1 or pay.payment_status=2)) as currentmonth_pend_paycount,
                    IF(s.scheme_type =1 and s.max_weight !=s.min_weight,true,false) as is_flexible_wgt,p.payment_status,
						if(scheme_type=3,IFNULL(cp.total_amount,0),Format(IFNULL(cp.total_amount,0),2)) as  current_total_amount_old,
						if(scheme_type=3 and s.amt_restrict_by=1,Format(IFNULL(cp.total_amount,0),2),Format(IFNULL(sp.total_amount,0),2)) as  current_total_amount,
					Format(IFNULL(cp.total_weight,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_weight),0) ,3) as  current_total_weight,
					IFNULL(cp.paid_installment,0)       as  current_paid_installments,
						IFNULL(cp.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0) as  current_chances_used_old,
						if(s.scheme_type=3 && s.pay_duration=0 ,IFNULL(sp.chance,0) + IF(Date_Format(Current_Date(),'%d%m')=Date_Format(sa.last_paid_date,'%d%m'),(sa.last_paid_chances),0),IFNULL(cp.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0)) as  current_chances_used,
				IFNULL(sp.chance,0)as dd,
					s.is_pan_required as sch_pan_req,Date_Format(max(p.date_payment),'%Y-%m-%d') as pay_date,s.pan_req_amt as sch_pan_amt,
					IF(sa.is_opening = 1 and s.scheme_type = 0,
					IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),false,true),
					true) AS previous_amount_eligible,
					count(pp.id_scheme_account) as cur_month_pdc,
					IFNULL(Date_Format(max(p.date_payment),'%d-%m-%Y'),IFNULL(IF(sa.is_opening=1,Date_Format(sa.last_paid_date,'%d-%m-%Y'),'')  ,0)) as last_paid_date,
					IFNULL(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'),Date_Format(max(p.date_add),'%Y%m')),IF(sa.is_opening=1,PERIOD_DIFF(Date_Format(curdate(),'%Y%m'),Date_Format(sa.last_paid_date,'%Y%m')),0)) as last_paid_duration,
				IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),1,0) as  previous_paid,	
					sa.disable_payment,
				s.allow_unpaid,
				if(s.allow_unpaid=1,s.unpaid_months,0) as allow_unpaid_months,
				s.allow_advance,
				if(s.allow_advance=1,s.advance_months,0) as advance_months,
					if(s.allow_unpaid=1,s.unpaid_weight_limit,0) as unpaid_weight_limit,
					s.allow_advance,
					if(s.allow_advance=1,s.advance_weight_limit,0) as advance_weight_limit,
					s.allow_preclose,
					if(s.allow_preclose=1,s.preclose_months,0) as preclose_months,
					if(s.allow_preclose=1,s.preclose_benefits,0) as preclose_benefits,cs.currency_symbol,s.payment_chances,s.otp_price_fixing, IFNULL(cshpay.cash_pay,0) as cash_pay,
					s.id_metal,s.id_purity,s.allow_advance_in,s.allow_unpaid_in,
					cs.pan_required_by,cs.pan_req_amt, 
					IFNULL((SELECT SUM(py.payment_amount) FROM payment py LEFT JOIN scheme_account sch_acc on (sch_acc.id_scheme_account = py.id_scheme_account) where sch_acc.active = 1 and sch_acc.is_closed = 0 and py.payment_status = 1 and sch_acc.id_customer = c.id_customer) ,0) as cus_overall_amount,
					DATE_FORMAT(
                        IF(
                            s.installment_cycle = 0,  
                            LAST_DAY(DATE_ADD(sa.start_date, INTERVAL (s.total_installments - 1) MONTH)),
                            IF(
                                s.installment_cycle = 1,  
                                DATE_ADD(sa.start_date, INTERVAL (s.total_installments - 1) DAY),
                                IF(
                                    s.installment_cycle = 2,  
                                    DATE_ADD(
                                        sa.start_date,
                                        INTERVAL ((s.ins_days_duration * s.total_installments) - 1) DAY
                                    ),
                                    NULL
                                )
                            )
                        ),
                        '%Y-%m-%d'
                    ) AS due_cycle_end
				From scheme_account sa
				Left Join scheme s On (sa.id_scheme=s.id_scheme)
				Left Join branch b On (b.id_branch=sa.id_branch)
				Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=2 or p.payment_status=1))
				LEFT JOIN (SELECT SUM(IFNULL(pmd.payment_amount,0)) AS cash_pay, id_payment FROM `payment_mode_details` AS pmd WHERE pmd.payment_mode = 'CSH' AND (pmd.payment_status = 1 and pmd.is_active=1) GROUP BY id_payment) AS cshpay ON cshpay.id_payment = p.id_payment
				Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)
				Left Join scheme_group sg On (sa.group_code = sg.group_code )
				Left Join
					(	Select
						  sa.id_scheme_account,
						  COUNT(Date_Format(p.date_payment,'%Y%m')) as paid_installment,
						  COUNT(Date_Format(p.date_payment,'%Y%m')) as chances,	
						  SUM(p.payment_amount) as total_amount,
						  SUM(p.metal_weight) as total_weight
						From payment p
						Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)
						Where  (p.payment_status=2 or p.payment_status=1) and  Date_Format(Current_Date(),'%Y%m')=Date_Format(p.date_payment,'%Y%m')
						Group By sa.id_scheme_account
					) cp On (sa.id_scheme_account=cp.id_scheme_account)
				left join
				(
    				Select sa.id_scheme_account, COUNT(Date_Format(p.date_payment,'%d%m')) as paid_installment,
    				COUNT(Date_Format(p.date_payment,'%d%m')) as chance,
        			SUM(p.payment_amount) as total_amount,
        			SUM(p.metal_weight) as total_weight
        			From payment p
        			Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)
        			Where  (p.payment_status=2 or p.payment_status=1) and  Date_Format(Current_Date(),'%d%m')=Date_Format(p.date_payment,'%d%m')
        		    Group By sa.id_scheme_account
		        )sp on(sa.id_scheme_account=sp.id_scheme_account)
				 Left Join postdate_payment pp On (sa.id_scheme_account=pp.id_scheme_account and (pp.payment_status=2 or pp.payment_status=7) and (Date_Format(pp.date_payment,'%Y%m')=Date_Format(curdate(),'%Y%m')))	
				 join chit_settings cs 
				Where sa.active=1 " . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . " and sa.is_closed = 0 and sa.id_scheme_account='$id_scheme_account'
				Group By sa.id_scheme_account";
        //  print_r($sql);exit;
        $records = $this->db->query($sql);
        //if(Date_Format(max(p.date_add),'%Y%m') = Date_Format(curdate(),'%Y%m'), SUM(p.no_of_dues),0)  as currentmonthpaycount,	 
        if ($records->num_rows() > 0) {
            $record = $records->row();
            //  echo '<pre>';  print_r($record	);exit;
            $max_chance = ($record->max_chance == 0 ? 1 : $record->max_chance);
            $current_installments = ($record->current_paid_installments == 0 ? $record->paid_installments + 1 : $record->paid_installments);
            if (($record->get_amt_in_schjoin == 1 && $record->firstPayment_amt > 0) && ($record->set_as_min_from > 0 || $record->set_as_max_from > 0)) {
                if ($current_installments >= $record->set_as_min_from) {
                    $record->min_amount = $record->firstPayment_amt;
                }
                if ($current_installments >= $record->set_as_max_from) {
                    $record->max_amount = $record->firstPayment_amt;
                }
            }
            $current_installments = ($record->current_paid_installments == 0 ? $record->paid_installments + 1 : $record->paid_installments + 1);
            $set_average = 'Y';
            if ($record->payment_chances == 1) {
                $set_average = 'N';
                /* if (($record->curmonth_total_paidCount != 0 && $record->paid_installments >= $record->avg_calc_ins) || ($record->paid_installments >= $record->avg_calc_ins && $record->curmonth_total_paidCount < $max_chance) && $record->avg_calc_ins > 0) {
                    $set_average = 'Y';
                } */
                //    echo '<pre>';  print_r($record	);exit;
                if (($record->current_paid_installments != 0 && $record->paid_installments >= $record->avg_calc_ins) || ($record->paid_installments >= $record->avg_calc_ins && $record->current_paid_installments < $max_chance) && $record->avg_calc_ins > 0) {
                    $set_average = 'Y';
                }
            }
            //	echo '<pre>';print_r($record);exit;
            if ($set_average == 'Y' && (($record->scheme_type == 1 && $record->is_flexible_wgt == 1) || $record->scheme_type == 3) && $record->avg_calc_ins > 0) {
                // Previous Ins == Average calc installment
                if ($record->avg_calc_by == 0) {
                    if (($current_installments > $record->avg_calc_ins || $record->avg_payable > 0) && $record->avg_calc_ins > 0) {
                        //30-05-2023 #AB : avg payable not storing if already in 0.000
                        if ($record->avg_payable > 0 || ($record->avg_payable !== NULL && $record->avg_payable !== '0' && $record->avg_payable !== '0.000')) {
                            // Already Average calculated, just set the value
                            if ($record->scheme_type == 1 && $record->is_flexible_wgt == 1) { // Weight - Flexible weight scheme
                                // Set max payable
                            } else if ($record->scheme_type == 3) {
                                if ($record->flexible_sch_type == 2) { // Flexible - Amount to weight [amount based]
                                    // Set max payable
                                    $record->max_amount = number_format($record->avg_payable, 2, '.', '');
                                    ;
                                    //echo $record->max_amount;exit;
                                    $record->payable = number_format($record->avg_payable, 2, '.', '');
                                    ;
                                } elseif ($record->flexible_sch_type == 3) { // Flexible - Amount to weight [weight based]
                                    $record->max_weight = $record->avg_payable;
                                }
                            }
                        } else {
                            // Calculate Average , set the value and updte it in schemme_account table
                            $paid_sql = $this->db->query("SELECT sum(metal_weight) as paid_wgt,sum(payment_amount) as paid_amt FROM `payment` 
        							WHERE payment_status=1 and id_scheme_account=" . $record->id_scheme_account . " 
        							GROUP BY YEAR(date_payment), MONTH(date_payment)
        							limit " . $record->avg_calc_ins);
                            $paid_wgt = 0;
                            $paid_amt = 0;
                            $paid = $paid_sql->result_array();
                            // echo '<pre>';print_r($paid);exit;
                            foreach ($paid as $p) {
                                $paid_wgt = $paid_wgt + $p['paid_wgt'];
                                $paid_amt = $paid_amt + $p['paid_amt'];
                            }
                            if ($record->scheme_type == 1 && $record->is_flexible_wgt == 1) { // Weight - Flexible weight scheme
                                // Set max payable
                            } else if ($record->scheme_type == 3) {
                                if ($record->flexible_sch_type == 2) { // Flexible - Amount to weight [amount based]
                                    // Set max payable
                                    $avg_payable = $paid_amt / $record->avg_calc_ins;
                                    $record->max_amount = number_format($avg_payable, 2, '.', '');
                                } elseif ($record->flexible_sch_type == 3) { // Flexible - Amount to weight [weight based]
                                    $avg_payable = number_format($paid_wgt / $record->avg_calc_ins, 3);
                                    $record->max_weight = $avg_payable;
                                }
                            }
                            //	print_r($avg_payable);exit;
                            $updData = array("avg_payable" => $avg_payable, "date_upd" => date("Y-m-d"));
                            $this->db->where('id_scheme_account', $record->id_scheme_account);
                            $this->db->update("scheme_account", $updData);
                            $record->avg_payable = $avg_payable;
                            //	print_r($this->db->last_query());exit;
                        }
                    }
                } else if ($record->avg_calc_by == 1 && $record->avg_calc_ins > 0) {
                    //calculate average by scheme joining date
                    $d1 = $record->start_date;
                    $d2 = date("d-m-Y");
                    $avg_months = $record->avg_calc_ins - 1;
                    $no_of_months = (int) abs((strtotime($d1) - strtotime($d2)) / (60 * 60 * 24 * 30));
                    $join_date = date('Y-m-d', strtotime($record->start_date));
                    $diffDate = date('Y-m-d', strtotime("+" . $avg_months . " months", strtotime($join_date)));
                    $endDate = date('Y-m-t', strtotime($diffDate));
                    //echo $endDate; exit;
                    if ($no_of_months >= $record->avg_calc_ins) {
                        if ($record->avg_payable > 0) { // Already Average calculated, just set the value
                            if ($record->scheme_type == 3) {
                                if ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 1) { // Flexible - Amount to weight [amount based]
                                    // Set max payable
                                    // $record->max_amount = $record->avg_payable;
                                    $record->max_amount = number_format($record->avg_payable, 2, '.', '');
                                    //echo $record->max_amount;exit;
                                    $record->payable = number_format($record->avg_payable, 2, '.', '');
                                } elseif ($record->flexible_sch_type == 3) { // Flexible - Amount to weight [weight based]
                                    $record->max_weight = $record->avg_payable;
                                }
                            }
                        } else { // Calculate Average , set the value and updte it in schemme_account table
                            //$paid_sql = $this->db->query("SELECT sum(metal_weight) as paid_wgt,sum(payment_amount) as paid_amt FROM `payment` WHERE payment_status=1 and id_scheme_account=".$record->id_scheme_account." GROUP BY YEAR(date_payment), MONTH(date_payment)");
                            $paid_sql = $this->db->query("SELECT sum(metal_weight) as paid_wgt,sum(payment_amount) as paid_amt FROM `payment` WHERE payment_status=1 and id_scheme_account=" . $record->id_scheme_account . " and date(date_payment) BETWEEN '" . $join_date . "' and '" . $endDate . "'");
                            //echo $this->db->last_query();exit;
                            $paid_wgt = 0;
                            $paid_amt = 0;
                            $paid = $paid_sql->result_array();
                            foreach ($paid as $p) {
                                $paid_wgt = $paid_wgt + $p['paid_wgt'];
                                $paid_amt = $paid_amt + $p['paid_amt'];
                            }
                            if ($record->scheme_type == 1 && $record->is_flexible_wgt == 1) { // Weight - Flexible weight scheme
                                // Set max payable
                            } else if ($record->scheme_type == 3) {
                                if ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 1) { // Flexible - Amount to weight [amount based]
                                    // Set max payable
                                    $avg = $paid_amt / $record->avg_calc_ins;
                                    //if average amount is less than 1st ins amt set 1st ins amount
                                    $sql = $this->db->query("SELECT SUM(payment_amount) as payment_amount from payment where payment_status=1 and id_scheme_account=" . $record->id_scheme_account . " GROUP BY YEAR(date_payment), MONTH(date_payment) order by id_payment ASC limit 1");
                                    $payamt = $sql->row_array();
                                    if ($avg >= $payamt['payment_amount']) {
                                        $avg_payable = $avg;
                                    } else {
                                        $avg_payable = $payamt['payment_amount'];
                                    }
                                    $record->max_amount = $avg_payable;
                                } elseif ($record->flexible_sch_type == 3) { // Flexible - Amount to weight [weight based]
                                    $avg_payable = number_format($paid_wgt / $record->avg_calc_ins, 3);
                                    $record->max_weight = $avg_payable;
                                }
                            }
                            $updData = array("avg_payable" => $avg_payable, "date_upd" => date("Y-m-d"));
                            $this->db->where('id_scheme_account', $record->id_scheme_account);
                            $this->db->update("scheme_account", $updData);
                            $record->avg_payable = $avg_payable;
                        }
                    }
                }
            }
            $allowed_due = 0;
            $due_type = '';
            $checkDues = TRUE;
            $allowSecondPay = FALSE;
            //$metal_rates=$this->get_metalrate_by_branch($record->id_branch);//For branchwise rate // KVP :: For RHR Metal Purity wise rate
            $metal_rate = $this->get_metalrate_by_branch($record->id_branch, $record->id_metal, $record->id_purity); //For branchwise rate
            $maturity_date = strtotime($record->maturity_date);
            $today = time();
            $difference = $today - $maturity_date;
            $days = (abs(floor($difference / 86400)));
            if ($record->has_lucky_draw == 1 && $record->is_lucky_draw == 1) {
                if ($record->group_start_date == NULL && $record->paid_installments > 1) { // block 2nd payment if scheme_group_code is not updated 
                    $checkDues = FALSE;
                } else if ($record->group_start_date != NULL) { // block  payment after end date
                    if ($record->group_end_date >= time() && $record->group_start_date <= time()) {
                        $checkDues = TRUE;
                    } else {
                        $checkDues = FALSE;
                    }
                }
            }
            if ($record->maturity_days != null && $record->maturity_date != '') {
                $current_date = date("Y-m-d");
                $maturity_date = $record->maturity_date;
                if (strtotime($current_date) <= strtotime($maturity_date)) {
                    $checkDues = TRUE;
                    if (($record->missed_ins + $record->paid_installments) <= $record->total_installments) {
                        $checkDues = TRUE;
                    } else {
                        $checkDues = FALSE;
                    }
                } else {
                    $checkDues = FALSE;
                }
            }
            /*	// Update Maturity Date in scheme_account table if maturity date is flexible
                if($record->maturity_type == 3){  // 1 - Flexible[Can pay installments and close], 2 - Fixed Maturity, 3 - Fixed Flexible[Increase maturity if has Default]
                    $paid_sql = $this->db->query("SELECT due_month,due_year FROM `payment` WHERE ( payment_status=1 or payment_status=2 ) and id_scheme_account=".$record->id_scheme_account." GROUP BY due_month, due_year order by due_year,due_month");
                    $paidByMonth = $paid_sql->result_array();
                    $skipped_months = 0;
                    for($i = 0; $i >= 0 ;$i++){
                        // $date = date('Y-m-d', strtotime("+".$i." months", strtotime($record->start_date)));
                        // $Ym = date('Y-m', strtotime("+".$i." months", strtotime($record->start_date)));
                        $Ym = date('Y-m', $this->add_months_to_date($i,$record->start_date));
                        if($Ym != date("Y-m")){
                            $isPaid = $this->isPaid($paidByMonth,$Ym);
                            $skipped_months = $skipped_months + ($isPaid ? 0 : 1);
                            //echo $Ym."--".date("Y-m")."--".$skipped_months."<br/>";
                        }
                        else if($Ym == date("Y-m")){ // Quit Loop
                            $i = -2;
                        }
                    }
                    $maturity =  date('Y-m-d', strtotime("+".($record->total_installments+$skipped_months)." months", strtotime($record->start_date)));
                    if($record->maturity_date != $maturity){
                        $updData = array( "maturity_date" => $maturity, "date_upd" => date("Y-m-d") );
                        $this->db->where('id_scheme_account',$record->id_scheme_account);
                         $this->db->update("scheme_account",$updData);
                    }
                }*/
            if ($checkDues) {
                if ($record->paid_installments > 0 || $record->totalunpaid > 0) {
                    if ($record->currentmonthpaycount == 0) {  // current month not paid (allowed pending due + current due)
                        if ($record->allow_unpaid == 1) {
                            if ($record->allow_unpaid_months > 0 && ($record->total_installments - $record->paid_installments) >= $record->allow_unpaid_months && $record->totalunpaid > 0) {
                                if (($record->total_installments - $record->paid_installments) == $record->allow_unpaid_months) {
                                    $allowed_due = ($record->totalunpaid < $record->allow_unpaid_months ? $record->totalunpaid : $record->allow_unpaid_months);
                                    $due_type = 'PD'; //  pending
                                } else {
                                    $allowed_due = ($record->totalunpaid < $record->allow_unpaid_months ? $record->totalunpaid : $record->allow_unpaid_months) + 1;
                                    $due_type = 'PN'; // normal and pending
                                }
                            } else {
                                $allowed_due = 1;
                                $due_type = 'ND'; // normal due
                            }
                        } else {
                            // current month not paid (allowed advance due + current due)
                            if ($record->allow_advance == 1) { // check allow advance
                                if ($record->advance_months > 0 && $record->advance_months <= ($record->total_installments - $record->paid_installments)) {
                                    if (($record->total_installments - $record->paid_installments) == $record->advance_months) {
                                        $allowed_due = $record->advance_months;
                                        $due_type = 'AN'; // advance and normal
                                    } else {
                                        $allowed_due = $record->advance_months + 1;
                                        $due_type = 'AN'; // advance and normal
                                    }
                                } else {
                                    $allowed_due = 1;
                                    $due_type = 'ND'; // normal due
                                }
                            } else {
                                $allowed_due = 1;
                                $due_type = 'ND'; // normal due
                            }
                        }
                    } else {     //current month paid
                        if ($record->free_payment == 1 && $record->allowSecondPay == 1 && $record->paid_installments == 1) {
                            $allowed_due = 1;
                            $due_type = 'AD'; // adv due
                            $allowSecondPay = TRUE;
                        } else {
                            if ($record->allow_unpaid == 1 && $record->allow_unpaid_months > 0 && $record->totalunpaid > 0 && ($record->currentmonthpaycount - 1) < $record->allow_unpaid_months) {
                                // can pay previous pending dues if attempts available 
                                if ($record->totalunpaid > $record->allow_unpaid_months) {
                                    $allowed_due = $record->allow_unpaid_months;
                                    $due_type = 'PD'; // pending due
                                } else {
                                    $allowed_due = $record->totalunpaid;
                                    $due_type = 'PD'; // pending due
                                }
                            } else {  // check allow advance
                                if (($record->allow_advance == 1) && ($record->advance_months > 0) && (($record->currentmonth_adv_paycount) < $record->advance_months)) {
                                    if (($record->advance_months + $record->paid_installments) <= $record->total_installments) {
                                        $allowed_due = ($record->advance_months - ($record->currentmonth_adv_paycount));
                                        $due_type = 'AD'; // advance due
                                    } else {
                                        $allowed_due = ($record->total_installments - $record->paid_installments);
                                        $due_type = 'AD'; // advance due
                                    }
                                } else { // have to check
                                    $allowed_due = 0;
                                    $due_type = ''; // normal due
                                }
                            }
                        }
                    }
                } else {  // check allow advance and add due with currect month (allowed advance due + current due)
                    if ($record->allow_advance == 1) { // check allow advance
                        if ($record->advance_months > 0 && $record->advance_months <= ($record->total_installments - $record->paid_installments)) {
                            if (($record->total_installments - $record->paid_installments) == $record->advance_months) {
                                $allowed_due = $record->advance_months;
                                $due_type = 'AN'; // advance and normal
                            } else {
                                $allowed_due = $record->advance_months + 1;
                                $due_type = 'AN'; // advance and normal
                            }
                        } else {
                            $allowed_due = 1;
                            $due_type = 'ND'; // normal due
                        }
                    } else {
                        $allowed_due = 1;
                        $due_type = 'ND'; // normal due
                    }
                }
            }
            if ($record->maturity_date != NULL && $record->maturity_date != '') {
                $due = $record->tot_ins - $record->paid_ins;
                if ($record->advance_months > $due) {
                    $allow_due = $record->advance_months;
                } else {
                    $allow_due = $due;
                }
            }
            if (!empty($record->maturity_days) && $record->allow_unpaid == 0) // ** Advance Only. No Pending allowed. ** //
            {
                if ($record->advance_months > 0) {
                    if ($record->current_paid_installments == 0)  // Current month not Paid (Current+Advance)
                    {
                        $allowed_due = $record->total_installments - $record->current_pay_installemnt;
                        $due_type = 'AN';
                    } else // Current month Paid (Advance)
                    {
                        $allowed_due = $record->total_installments - ($record->current_pay_installemnt + $record->current_paid_installments);
                        $due_type = 'AD';
                    }
                }
            }
            $pdc_det = $this->get_pending_pdc($record->id_scheme_account);
            $dates = date('d-m-Y');
            if ($record->set_as_min_from > 0 && $record->set_as_max_from > 0 && $record->paid_installments > 0 && $record->amt_based_on == 0) {
                if ($record->dues_count > 0 && $record->paid_installments >= $record->set_as_min_from && $record->paid_installments <= $record->set_as_max_from) {
                    $res = $this->db->query("select p.payment_amount,sa.id_scheme_account from payment p 
    			                 left join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
    			                 where p.payment_status=1 and p.id_scheme_account = '" . $record->id_scheme_account . "' order by id_payment asc limit 1");
                    $payamount = $res->row_array();
                    if ($payamount['payment_amount'] > 0) {
                        //$record->min_amount = $record->dues_count * $payamount['payment_amount'];
                        $record->max_amount = $record->dues_count * $payamount['payment_amount'];
                        $record->min_amount = $payamount['payment_amount'];
                        if ($record->currentmonthpaycount != 0) {
                            if ($record->paid_installments > 0 && $record->currentmonthpaycount == 1) {
                                $record->current_total_amount = 0;
                            } else {
                                $month_first_day = date('Y-m-01');
                                $res1 = $this->db->query("select SUM(p.payment_amount) as payment_amount,sa.id_scheme_account from payment p 
        			                        left join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
        			                        where p.payment_status=1 and p.due_type != 'ND' and date(p.date_payment) between '" . $month_first_day . "' and '" . $record->pay_date . "' and p.id_scheme_account = '" . $record->id_scheme_account . "' ");
                                //echo $this->db->last_query();exit;
                                $amt = $res1->row_array();
                                $record->current_total_amount = $amt['payment_amount'];
                            }
                        } else {
                            $record->min_amount = $record->min_amount;
                            $record->max_amount = $record->dues_count * $payamount['payment_amount'];
                            $record->current_total_amount = 0;
                        }
                    } else {
                        $record->min_amount = $record->min_amount;
                        $record->max_amount = $record->max_amount;
                    }
                }
            }
            /*4-5-2023 last 5 month payment customer wants to pay first installment amount - by Haritha */
            if ($record->set_as_min_from > 0 && $record->set_as_max_from > 0 && $record->paid_installments > 0 && $record->amt_based_on == 1) {
                if ($record->paid_installments + 1 >= $record->set_as_min_from && $record->paid_installments + 1 <= $record->set_as_max_from) {
                    $res = $this->db->query("select p.payment_amount,sa.id_scheme_account from payment p 
    			                 left join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
    			                 where p.payment_status=1 and p.id_scheme_account = '" . $record->id_scheme_account . "' order by id_payment asc limit 1");
                    $payamount = $res->row_array();
                    if ($payamount['payment_amount'] > 0) {
                        $record->max_amount = $payamount['payment_amount'];
                        $record->min_amount = $payamount['payment_amount'];
                    } else {
                        $record->min_amount = $record->min_amount;
                        $record->max_amount = $record->max_amount;
                    }
                }
            }
            // Allow Pay
            if ($record->scheme_type == 3) {
                if ($record->one_time_premium == 0) {
                    if ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 3 || $record->flexible_sch_type == 4 || $record->flexible_sch_type == 8) {
                        $allow_pay = ($record->disable_payment != 1 && $record->payment_status != 2 && $record->paid_installments < $record->total_installments ? ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 3 ? ($record->current_total_weight >= $record->max_weight && $record->current_chances_used >= $max_chance ? 'N' : 'Y') : ($record->flexible_sch_type == 1 || $record->flexible_sch_type == 5 ? ($record->payment_chances == 0 ? (($record->current_chances_used == 0 && ($record->max_amount - $record->current_total_amount) > 0) ? 'Y' : 'N') : ($record->payment_chances == 1 ? (($max_chance > $record->current_chances_used) && (($record->max_amount - $record->current_total_amount) > 0) ? 'Y' : 'N') : 'N')) : ($record->flexible_sch_type == 4 || $record->flexible_sch_type == 8 ? ($record->payment_chances == 0 ? ($record->current_chances_used == 0 ? 'Y' : 'N') : ($record->payment_chances == 1 ? (($max_chance > $record->current_chances_used) && (($record->max_weight - $record->current_total_weight) > 0) ? 'Y' : 'N') : 'N')) : 'N'))) : 'N');
                    } else {
                        $allow_pay = ($record->disable_payment != 1 && $record->payment_status != 2 && $record->paid_installments < $record->total_installments ? ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 3 ? ($record->current_total_weight >= $record->max_weight && $record->current_chances_used >= $max_chance ? 'N' : 'Y') : ($record->flexible_sch_type == 1 || $record->flexible_sch_type == 5 ? ($record->payment_chances == 0 ? (($record->current_chances_used == 0 && ($record->max_amount - $record->current_total_amount) > 0) ? 'Y' : 'N') : ($record->payment_chances == 1 ? (($max_chance > $record->current_chances_used) && (($record->max_amount - $record->current_total_amount) > 0) ? 'Y' : 'N') : 'N')) : ($record->flexible_sch_type == 4 ? ($record->payment_chances == 0 ? ($record->current_chances_used == 0 ? 'Y' : 'N') : ($record->payment_chances == 1 ? (($max_chance > $record->current_chances_used) && (($record->max_weight - $record->current_total_weight) > 0) ? 'Y' : 'N') : 'N')) : 'N'))) : 'N');
                    }
                } else {
                    $allow_pay = ($record->disable_payment != 1 && $record->payment_status != 2 && $record->paid_installments == 0 && $record->is_enquiry == 0 ? ($record->flexible_sch_type == 1 || $record->flexible_sch_type == 5 ? ($record->current_total_amount >= $record->max_amount || $record->current_chances_used >= $max_chance ? 'N' : 'Y') : ($record->flexible_sch_type == 3 || $record->flexible_sch_type == 4 ? ($record->payment_chances == 0 ? ($record->current_chances_used == 0 ? 'Y' : 'N') : ($record->payment_chances == 1 ? (($max_chance > $record->current_chances_used) && (($record->max_weight - $record->current_total_weight) > 0) ? 'Y' : 'N') : ($record->flexible_sch_type == 8 ? ($record->current_total_weight >= $record->max_weight && $record->paid_installments >= $record->total_installments ? 'N' : 'Y') : 'N'))) : 'N')) : 'N');
                }
            } else {
                $allow_pay = ($record->disable_payment != 1 && ($record->payment_status != 2) ? ($record->cur_month_pdc < 1 ? ($record->paid_installments < $record->total_installments ? ($record->is_flexible_wgt ? ($record->current_total_weight >= $record->max_weight || $record->current_chances_used >= $max_chance ? 'N' : 'Y') : ($record->paid_installments < $record->total_installments ? ($record->allow_unpaid == 1 && $record->totalunpaid > 0 && ($record->currentmonthpaycount - 1) < $record->allow_unpaid_months ? 'Y' : ($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount - 1) < $record->advance_months ? 'Y' : ($record->currentmonthpaycount == 0 ? 'Y' : 'N'))) : 'N')) : 'N') : 'N') : 'N');
                //print_r($allow_pay);exit;
            }
            // echo $record;exit;
            //DGS-DCNM restrict payment by days & daily payment limit allow pay settings....	
            if ($record->is_digi == 1) {
                if ($record->daily_pay_limit != null) {
                    if ($record->curday_total_paid != 0 && $record->curday_total_paid >= $record->daily_pay_limit) {
                        $allow_pay = 'N';
                    } else {
                        $allow_pay = 'Y';
                    }
                }
                if ($record->restrict_payment == 1) {
                    if ($record->total_days_to_pay != null && ($record->joined_date_diff >= $record->total_days_to_pay)) {
                        $allow_pay = 'N';
                    } else {
                        $allow_pay = 'Y';
                    }
                }
            }
            //DGS-DCNM end...
            //allow pay for advance & pending settings...
            $allowed_due = ($record->is_flexible_wgt == 1 ? 1 : $allowed_due);
            // 			print_r($allowed_due);exit;
            if ($record->allow_advance == 1 && $record->advance_months > 0 && $allowed_due != 0 && ($allowed_due <= $record->advance_months) && $record->paid_installments < $record->total_installments) {
                $allow_advancePay = 'Y';
            }
            if ($record->allow_unpaid == 1 && $record->allow_unpaid_months > 0 && $allowed_due != 0 && ($allowed_due <= $record->allow_unpaid_months) && $record->paid_installments < $record->total_installments) {
                $allow_pendingPay = 'Y';
            }
            if ($allow_advancePay == 'Y' || $allow_pendingPay == 'Y') {
                $allow_pay = 'Y';
            }
            //adv/pending allow_pay ends....
            // print_r($allow_pay);exit;
            //RHR schemes : by 30 days payment cycle with advance , pending flow.. 20-09-2023 #ABI...
            /*
    1. ALLOW PAY : (Y/N)
    2. ALLOWED DUES : no of dues count can pay
    3. DUE TYPE	:
        allow pay for days duration wise instalment cycle starts...
        ND- only normal due , allowed_due : 1
        AD - only advance due, allowed_due : 1
        PD - only pending due, allowed_due : 1
        AN - advance + normal, allowed_due : no.of.adv + 1
        PN - pending + normal, allowed_due : no.of.pend + 1
        APN - Advance + pending + normal, allowed_due : no.of.adv + no.of.pend + 1
    */
            if ($record->installment_cycle == 3 || $record->installment_cycle == 2 || $record->installment_cycle == 1 || $record->installment_cycle == 0) {
                //RHR schemes :starts 20-09-2023 #ABI...
                /*
                1. ALLOW PAY : (Y/N)
                2. ALLOWED DUES : no of dues count can pay
                3. DUE TYPE	:
                    allow pay for days duration wise instalment cycle starts...
                    ND- only normal due , allowed_due : 1
                    AD - only advance due, allowed_due : 1
                    PD - only pending due, allowed_due : 1
                    AN - advance + normal, allowed_due : no.of.adv + 1
                    PN - pending + normal, allowed_due : no.of.pend + 1
                    APN - Advance + pending + normal, allowed_due : no.of.adv + no.of.pend + 1
                    RHR schemes : Installment cycle type 2 (by days duration) by 30 days payment cycle with advance , pending flow.. 20-09-2023 #ABI...
                    RHR schemes : Installment cycle type 1 (daily)  daily payment scheme with advance and pending...updatedOn:12-10-2023, By:AB
                    worked for only single payment chance type till 12-10-2023
                    need to work for multiple payment chances type further
                */
                //due_type , allowed_dues
                $allowed_due = 0;
                $date = date('Y-m-d');
                $paid_normal_due = 0;
                $paid_advance_due = 0;
                $paid_pending_due = 0;
                $paid_due = 0;
                $remaining_normal_due = 0;
                $remaining_advance_due = 0;
                $remaining_pending_due = 0;
                $remaining_due = 0;
                $paid_multiple_chance = 0;
                $chances_allowed_due = 0;
                $max_chance = $record->max_chance;
                //get range for current date....
                $range = $this->get_due_date('current_range', $date, $record->id_scheme_account);
                //take the no of paid dues with due_type customer paid already...
                /*$paid_dueData = $this->db->query("SELECT due_type as due_name, COUNT(due_type) as dues_count 
                                            FROM payment where payment_status = 1 and id_scheme_account = " . $record->id_scheme_account . " 
                                            and date(date_payment) BETWEEN '" . $range[0]['due_date_from'] . "' AND '" . $range[0]['due_date_to'] . "'
                                            group by due_type")->result_array();
                // print_r($paid_dueData);exit;
                foreach ($paid_dueData as $due) {
                    if ($due['due_name'] == 'ND') {
                        $paid_normal_due = $due['dues_count'];
                    } else if ($due['due_name'] == 'AD') {
                        $paid_advance_due = $due['dues_count'];
                    } else if ($due['due_name'] == 'PD') {
                        $paid_pending_due = $due['dues_count'];
                    } else {
                        $paid_multiple_chance = $due['dues_count'];
                    }
                }*/
                if (!empty($range)) {
                    //take the no of paid dues with due_type customer paid already...
                    $paid_dueData = $this->db->query("SELECT due_type as due_name, COUNT(due_type) as dues_count 
												FROM payment where payment_status = 1 and id_scheme_account = " . $record->id_scheme_account . " 
												and date(date_payment) BETWEEN '" . $range[0]['due_date_from'] . "' AND '" . $range[0]['due_date_to'] . "'
												group by due_type")->result_array();
                    // print_r($paid_dueData);exit;
                    foreach ($paid_dueData as $due) {
                        if ($due['due_name'] == 'ND') {
                            $paid_normal_due = $due['dues_count'];
                        } else if ($due['due_name'] == 'AD') {
                            $paid_advance_due = $due['dues_count'];
                        } else if ($due['due_name'] == 'PD') {
                    $paid_pending_due = $due['dues_count'];
                        } else {
                            $paid_multiple_chance = $due['dues_count'];
                        }
                    }
                }
                if ($record->maturity_type != 2 && (date('Y-m-d') > $record->maturity_date || date('Y-m-d') > $record->due_cycle_end) && $record->paid_installments > 0 ) {
                    $dueCounts = array_count_values(array_column($range, 'due_type'));
                    $paid_normal_due = $dueCounts['ND'] ?? 0;
                    $paid_advance_due = $dueCounts['AD'] ?? 0;
                    $paid_pending_due = $dueCounts['PD'] ?? 0;
                    /* multiple chance = all OTHER due types */
                    $paid_multiple_chance = 0;
                    foreach ($dueCounts as $type => $count) {
                        if (!in_array($type, ['ND', 'PD', 'AD'], true)) {
                            $paid_multiple_chance += $count;
                        }
                    }
                    $remaining_pending_due = $record->total_installments - $record->paid_installments;
                } else {
                //take the no of remaining dues with due_type customer want to pay..
                $remaining_dueData = $this->get_due_date('allow_pay', $date, $record->id_scheme_account);
                //print_r($this->db->last_query());exit;
                foreach ($remaining_dueData as $due) {
                    if ($due['due_name'] == 'ND') {
                        $remaining_normal_due = $due['dues_count'];
                    } else if ($due['due_name'] == 'AD') {
                        $remaining_advance_due = $due['dues_count'];
                    } else if ($due['due_name'] == 'PD') {
                        $remaining_pending_due = $due['dues_count'];
                    } else {
                        $remaining_due = $due['dues_count'];
                        }
                    }
                }
                //calculate can pay advance due and pending dues...
                $chances_allowed_due = $max_chance - ($paid_multiple_chance + $paid_normal_due);
                $allow_advance_in = explode(',', $record->allow_advance_in);
                $allow_unpaid_in = explode(',', $record->allow_unpaid_in);
                //var_dump($record->allow_advance_in);exit;
                if (
                    ($chances_allowed_due <= 1 &&
                    (
                        ($record->allow_advance == 1 && $record->advance_months > 0 && (in_array('1', $allow_advance_in) || in_array('4', $allow_advance_in)) && $remaining_advance_due > 0)
                        ||
                        ($record->allow_unpaid == 1 && $record->allow_unpaid_months > 0
                            && (in_array('1', $allow_unpaid_in) || in_array('4', $allow_unpaid_in))
                            && $remaining_pending_due > 0
                        )
                        )) ||
                        (
                        $remaining_pending_due > 0 && $record->maturity_type != 2 && ((!empty($record->maturity_date) && date('Y-m-d') > $record->maturity_date) || (!empty($record->due_cycle_end) && date('Y-m-d') > $record->due_cycle_end))
                    )
                ) {
                    //advance..
                    //advance..
                    $sch_advance = ($record->allow_advance == 1) ? $record->advance_months : 0;
                    $cur_advance = ($remaining_advance_due > 0 ? ($sch_advance < $remaining_advance_due && $remaining_advance_due > 0 && $paid_advance_due < $sch_advance ? $sch_advance : ($sch_advance > $paid_advance_due ? ($sch_advance - $paid_advance_due) : 0)) : 0);
                    $canPay_advance = ($remaining_advance_due < $cur_advance ? $remaining_advance_due : $cur_advance);
                    //pending
                    if ($record->allow_unpaid == 1 && (in_array('1', $allow_unpaid_in))) {
                        $record->allow_unpaid_months = $record->allow_unpaid_months;
                    } else {
                        $record->allow_unpaid_months = 0;
                    }
                    $sch_unpaid = ($record->allow_unpaid == 1 && $remaining_pending_due > 0 && $record->maturity_type != 2 && (date('Y-m-d') > $record->maturity_date || date('Y-m-d') > $record->due_cycle_end) ? ($record->allow_unpaid_months + 1) : $record->allow_unpaid_months);
                    $cur_unpaid = ($remaining_pending_due > 0 ? ($sch_unpaid > $paid_pending_due ? ($sch_unpaid - $paid_pending_due) : 0) : 0);
                    $canPay_pending = ($remaining_pending_due > $cur_unpaid ? $cur_unpaid : $remaining_pending_due);
                    //print_r($remaining_normal_due);exit;
                    if ($remaining_normal_due == 0 && $canPay_pending > 0) {            //only pending
                        $due_type = 'PD';
                        $allowed_due = $canPay_pending;
                    } else if ($remaining_normal_due == 0 && $canPay_advance > 0) {        //only advance
                        $due_type = 'AD';
                        $allowed_due = $canPay_advance;
                    } else if ($remaining_normal_due > 0 && $canPay_pending > 0) {        //normal + pending
                        $due_type = 'PN';
                        $allowed_due = $remaining_normal_due + $canPay_pending;
                    } else if ($remaining_normal_due > 0 && $canPay_advance > 0) {        //normal + advance
                        $due_type = 'AN';
                        $allowed_due = $remaining_normal_due + $canPay_advance;
                    }
                } else {
                    if ($chances_allowed_due != 0 && $paid_normal_due != 0 && $max_chance > 1 && sizeof($range) > 0 && $max_chance > $chances_allowed_due) {
                        $due_type = 'MND';
                        $allowed_due = $chances_allowed_due;
                    } else {
                        if ($record->currentmonthpaycount > 0 && $record->allow_advance == 0) {
                            $allowed_due = 0;
                            $due_type = '';
                        } else {
                            $due_type = 'ND';
                            $allowed_due = $remaining_normal_due;
                        }
                    }
                }
                //allow pay , discuss for APN and multiple payment chances
                if ($allowed_due > 0 && $record->paid_installments < $record->total_installments) {
                    $allow_pay = 'Y';
                } else {
                    $allow_pay = 'N';
                }
            }
            if ($record->maturity_type == 2 && date('Y-m-d') > $record->maturity_date && !empty($record->maturity_date)) {
                $allow_pay = 'N';
            }
            //  print_r($allow_pay);exit;
            //restrict CASH payments based on limit settings
            $disable_acc_payments = '';
            $csh_payments = 0;
            if ($record->disable_pay == 1 && $record->disable_pay_amt > 0) {
                $res = $this->db->query("SELECT sum(pmd.payment_amount) as total_csh from payment_mode_details pmd 
                left join payment p on p.id_payment = pmd.id_payment 
                where pmd.payment_mode = 'CSH' and p.id_scheme_account =" . $record->id_scheme_account);
                $csh_payments = $res->row()->total_csh;
                if ($csh_payments >= $record->disable_pay_amt) {
                    $disable_acc_payments = 'Y';
                    $allow_cash_limit = 0;
                } else {
                    $disable_acc_payments = 'N';
                    $allow_cash_limit = abs($record->disable_pay_amt - $csh_payments);
                    // print_r($allow_cash_limit);exit;
                }
            }
            //allow pay for general advance settings if enabled //TKV 
            $d1 = $record->start_date;
            $d2 = date("d-m-Y");
            $no_of_months = (int) abs((strtotime($d1) - strtotime($d2)) / (60 * 60 * 24 * 30));
            $flx_denomintion = $record->flx_denomintion;
            if ($record->avg_payable > 0 && $record->avg_calc_ins > 0 && (($record->avg_calc_by == 1 && $no_of_months >= $record->avg_calc_ins) || ($record->avg_calc_by == 0 && $record->paid_installments >= $record->avg_calc_ins))) {
                $max_amount = number_format($record->avg_payable, 2, '.', '');
                ;
                $flx_denomintion = 1;
            } else {
                $max_amount = round(($record->scheme_type == 3 && $record->max_amount != 0 && $record->max_amount != '' ? (($record->firstPayamt_maxpayable == 1 || $record->firstPayamt_as_payamt == 1) && ($record->paid_installments > 0 || $record->get_amt_in_schjoin == 1) ? $record->firstPayment_amt : ($record->max_amount - str_replace(',', '', $record->current_total_amount))) : ($record->scheme_type == 3 && $record->max_weight != 0 && $record->max_weight != '' ? (($record->max_weight - $record->current_total_weight) * $metal_rate) : $record->max_amount)));
            }
            // esakki
            // $min_amount = round(($record->scheme_type == 3 && $record->min_amount != 0 && $record->min_amount != '' ? ((($record->firstPayamt_maxpayable == 1 || $record->firstPayamt_as_payamt == 1) && $record->firstPayment_amt != NULL) && ($record->paid_installments > 0 || $record->get_amt_in_schjoin == 1) ? $record->firstPayment_amt : $record->min_amount) : ($record->scheme_type == 3 && $record->min_weight != 0 && $record->min_weight != '' ? (($record->min_weight) * $metal_rate) : $record->min_amount)));
            $min_amount = round(($record->scheme_type == 3 && $record->min_amount != 0 && $record->min_amount != '' ? (($record->firstPayamt_as_payamt == 1 && $record->firstPayment_amt != NULL) && ($record->paid_installments > 0 || $record->get_amt_in_schjoin == 1) ? $record->firstPayment_amt : $record->min_amount) : ($record->scheme_type == 3 && $record->min_weight != 0 && $record->min_weight != '' ? (($record->min_weight) * $metal_rate) : $record->min_amount)));
            $payable = (int) (($record->scheme_type == 3 && ($record->paid_installments > 0 || $record->get_amt_in_schjoin == 1) && ($record->firstPayamt_as_payamt == 1 || $record->is_registered == 1)) ? $record->firstPayment_amt : ($record->firstPayment_as_wgt == 1 && $record->firstpayment_wgt > 0 ? ($record->firstpayment_wgt * $metal_rate) : $record->payable));
            if ($allow_pay == 'N' && $record->allow_general_advance == 1 && date('Ym') <= $record->daily_sch_allow_pay_till) {
                $allow_pay = 'Y';
                $due_type = 'GEN_ADV';
                $allowed_due = 1;
                $min_amount = $record->adv_min_amt;
                $max_amount = $record->adv_max_amt;
                $flx_denomintion = $record->adv_denomination;
                $payable = $record->adv_min_amt;
            }
            //TKV gen advance ends...
            /* Restrict payment and direct to customer kyc based on pan required amount and required by starts...#Dt_add:25-01-2024, #AB */
            $direct_to_kyc = 'N';
            $kyc_pan_number = '';
            $get_all_kycData = $this->db->query("SELECT number,kyc_type FROM `kyc` where id_customer = " . $record->id_customer)->result_array();
            if (sizeof($get_all_kycData) > 0) {
                foreach ($get_all_kycData as $kyc) {
                    //for other types can add code here
                    if ($kyc['kyc_type'] == 2) {
                        $kyc_pan_number = $kyc['number'];
                    }
                }
            }
            if (!empty($record->pan_required_by) && !empty($record->pan_req_amt) && empty($kyc_pan_number)) {
                if (($record->pan_required_by == 2 && $record->total_paid_amount >= $record->pan_req_amt) || ($record->pan_required_by == 1 && $record->cus_overall_amount >= $record->pan_req_amt)) {
                    $direct_to_kyc = 'Y';
                }
            } else if (!empty($record->is_pan_required) && !empty($record->sch_pan_req_amt) && empty($kyc_pan_number)) {
                if (($record->is_pan_required == 1 && ($record->total_paid_amount >= $record->sch_pan_req_amt)) || ($record->is_pan_required == 2 && ($record->cus_overall_amount >= $record->sch_pan_req_amt))) {
                    $direct_to_kyc = 'Y';
                }
            }
            /* further if conditions can be checked for other types in future here..... Redirect to customer kyc ends*/
            //lump scheme starts...
            if ($record->is_lumpSum == 1 && $record->lump_joined_weight > 0 && $record->lump_payable_weight > 0 && $record->flexible_sch_type == 4 && $record->firstPayment_as_wgt == 1) {
                $min_amount = number_format((float) round($record->lump_payable_weight * $metal_rate), 2, '.', '');
                $max_amount = number_format((float) round($record->lump_payable_weight * $metal_rate), 2, '.', '');
                $payable = number_format((float) round($record->lump_payable_weight * $metal_rate), 2, '.', '');
                $record->min_weight = $record->lump_payable_weight;
                $record->max_weight = $record->lump_payable_weight;
                $eligible_wgt = $record->lump_payable_weight;
            }
            if ($record->is_digi == 1) {
                if ($date <= $record->dg_allow_pay_till) {
                    if($record->curday_total_paid_count != 0 && $record->curday_total_paid_count >= $record->max_chance)
                    {
                        $allow_pay = 'N';
                        $allowed_due = 0;
                    }else{
                        $allow_pay = 'Y';
                        $allowed_due = 1;
                    }
                } else {
                    $allow_pay = 'N';
                    $allowed_due = 0;
                }
                if ($record->interest == 1) {
                    $res = array(
                        'id_scheme' => $record->id_scheme,
                        'date_difference' => (!empty($record->joined_date_diff) ? $record->joined_date_diff : 1),
                    );
                    $dg_benefitData = $this->get_digi_benefit($res);
                    $is_other_benefit = 0;
                    foreach ($dg_benefitData as $dg_benefit) {
                        if ($dg_benefit['commodity'] == $record->id_metal) {
                            $dg_benefit_value = $dg_benefit['interest_value'];
                            $dg_benefit_type = $dg_benefit['interest_type'];
                            $dg_benefit_symbol = $dg_benefit['int_symbol'];
                            if ($dg_benefit_type == 0) {
                                $dg_benefit_content = $dg_benefit_value . $dg_benefit_symbol . ' Benefit on Day ' . $res['date_difference'];
                            } else {
                                $dg_benefit_content = $dg_benefit_symbol . $dg_benefit_value . ' Benefit on Day ' . $res['date_difference'];
                            }
                        } else {
                            $other_metal_rate = $this->get_metalrate_by_branch($record->id_branch, $dg_benefit['commodity'], ''); //For branchwise rate
                            $is_other_benefit = 1;
                            $dg_other_benefit_value = $dg_benefit['interest_value'];
                            $dg_other_benefit_type = $dg_benefit['interest_type'];
                            $dg_metal = $dg_benefit['metal'];
                            $dg_benefit_symbol = $dg_benefit['int_symbol'];
                            if ($dg_other_benefit_type == 0) {
                                $dg_other_benefit_content = $dg_other_benefit_value . $dg_benefit_symbol . ' ' . $dg_metal . ' Benefit (1GM : INR ' . $other_metal_rate . ')';
                            } else {
                                $dg_other_benefit_content = $dg_benefit_symbol . $dg_other_benefit_value . ' ' . $dg_metal . ' Benefit (1GM : INR ' . $other_metal_rate . ')';
                            }
                        }
                    }
                }
            }
            $pan_no = '';
            $adhar_no = '';
            $get_sch_pan = FALSE;
            $kycData = $this->get_CusKycData($record->id_customer);
            if (sizeof($kycData) > 0) {
                foreach ($kycData as $kd) {
                    if ($kd['kyc_type'] == 2) {
                        $pan_no = $kd['masked_doc_number'];
                    }
                    if ($kd['kyc_type'] == 3) {
                        $adhar_no = $kd['masked_doc_number'];
                    }
                }
            }
            if ($record->sch_pan_req == 1 || $record->sch_pan_req == 2) {
                if (empty($pan_no)) {
                    $get_sch_pan = TRUE;
                    $record->sch_pan_req = 1;
                }
            }
            if ($record->is_topup_scheme == 1 && $record->installment_cycle == 4) {
                $acc_custom_slab_data = $this->get_due_date('custom', $date, $record->id_scheme_account);
                //print_r($this->db->last_query());exit;
                if (sizeof($acc_custom_slab_data) > 0) {
                    $min_amount = number_format((float) $acc_custom_slab_data[0]['minimum_payable_amount'], 2, '.', '');
                    $max_amount = number_format((float) $acc_custom_slab_data[0]['maximum_payable_amount'], 2, '.', '');
                    $payable = $acc_custom_slab_data[0]['minimum_payable_amount'];
                    $due_type = $acc_custom_slab_data[0]['due_type'];
                    $allowed_due = 1;
                    $allow_pay = 'Y';
                    $due_date_from = $acc_custom_slab_data[0]['due_date_from'];
                    $due_date_to = $acc_custom_slab_data[0]['due_date_to'];
                    $topup_slab_endday = $acc_custom_slab_data[0]['range_to'];
                    $topup_slab_startday = $acc_custom_slab_data[0]['range_from'];
                    $topup_slab_value = $acc_custom_slab_data[0]['payable_value'];
                    $topup_slab_type = ($acc_custom_slab_data[0]['payable_value'] == 0 ? 'INR' : '%');
                    $topup_estimated_paid_amount = number_format((float) $acc_custom_slab_data[0]['estimated_paid_amount'], 2, '.', '');
                } else {
                    $allow_pay = 'N';
                }
            }
            if (empty($record->maturity_date) && $record->paid_installments > 0) {
                $pay_arr = [
                    'maturity_type' => $record->maturity_type,
                    'maturity_days' => $record->maturity_days,
                    'start_date' => $record->start_date,
                    'id_scheme_account' => $record->id_scheme_account
                ];
                $this->upd_maturityDate($pay_arr);
            }
            if ($allow_pay == 'Y' && $record->maturity_type == 1 && $record->maturity_date != NULL && $record->maturity_date != '') {
                $today = date('Y-m-d');
                if ($today < $record->maturity_date) {
                    $allow_pay = 'Y';
                } else {
                    $allow_pay = 'N';
                }
            }
            //allow pay , discuss for APN and multiple payment chances
            if ($allowed_due > 0 && $allow_pay == 'Y' && $record->paid_installments < $record->total_installments && $record->disable_payment != 1) {
                $allow_pay = 'Y';
            } else {
                $allow_pay = 'N';
            }
            $kyc_settings = $this->db->get('kyc_settings')->row_array();
            if (isset($kyc_settings['kyc_required']) && $kyc_settings['kyc_required'] == 1 &&
                isset($kyc_settings['kyc_allow_type']) && $kyc_settings['kyc_allow_type'] == 1) {
                
                $this->db->where('id_customer', $record->id_customer);
                $this->db->where_in('status', array(0, 1));
                $pending_count = $this->db->count_all_results('kyc');
                
                if ($pending_count > 0) {
                    $kyc_mode = isset($kyc_settings['kyc_mode']) ? $kyc_settings['kyc_mode'] : 0;
                    $is_any_one_cust = (isset($kyc_settings['kyc_customer_rule_logic']) && $kyc_settings['kyc_customer_rule_logic'] == 1);
                    $is_any_one_sch = (isset($kyc_settings['kyc_scheme_rule_logic']) && $kyc_settings['kyc_scheme_rule_logic'] == 1);
                    $is_any_one = ($kyc_mode == 1) ? $is_any_one_cust : $is_any_one_sch;
                    
                    if ($is_any_one) {
                        $this->db->where('id_customer', $record->id_customer);
                        $this->db->where('status', 2);
                        $verified_count = $this->db->count_all_results('kyc');
                        if ($verified_count == 0) {
                            $allow_pay = 'N';
                        }
                    } else {
                        $allow_pay = 'N';
                    }
                }
            }

            $schemeAcc = array(
                'toup_pay_data' => $acc_custom_slab_data,
                'is_topup_scheme' => $record->is_topup_scheme,
                'topup_booking_type' => $record->topup_booking_type,
                'topup_amount' => $record->topup_amount,
                'topup_weight' => $record->topup_weight,
                'topup_rate' => $record->topup_rate,
                'topup_balance_pay' => number_format((float) $record->topup_amount - $record->total_paid_amount, 2, '.', ''),
                'due_date_from' => $due_date_from,
                'due_date_to' => $due_date_to,
                'topup_slab_endday' => $topup_slab_endday,
                'topup_slab_startday' => $topup_slab_startday,
                'topup_slab_value' => $topup_slab_value,
                'topup_slab_type' => $topup_slab_type,
                'topup_estimated_paid_amount' => $topup_estimated_paid_amount,
                'installment_cycle' => $record->installment_cycle,
                'is_digi' => $record->is_digi,
                'dg_benefit_value' => $dg_benefit_value,
                'dg_benefit_type' => $dg_benefit_type,
                'dg_benefit_symbol' => $dg_benefit_symbol,
                'dg_benefit_content' => $dg_benefit_content,
                // Other Benefit Digi Gold - Raja(2025-09-29)
                'dg_other_benefit_value' => $dg_other_benefit_value,
                'dg_other_benefit_type' => $dg_other_benefit_type,
                'is_other_benefit' => $is_other_benefit,
                'metal_name' => $dg_metal,
                'other_metal_rate' => $other_metal_rate,
                'dg_other_benefit_content' => $dg_other_benefit_content,
                'min_amount' => $min_amount,
                'max_amount' => $max_amount,
                'min_weight' => $record->min_weight,
                'max_weight' => $record->max_weight,
                'payable' => $payable,
                'eligible_weight' => abs(number_format((float) $eligible_wgt, 3, '.', '')),
                'firstPayment_as_wgt' => $record->firstPayment_as_wgt,
                'is_lumpSum' => $record->is_lumpSum,
                'lump_payable_weight' => $record->lump_payable_weight,
                'sch_pan_amt' => $record->sch_pan_amt,
                'sch_pan_req' => $record->sch_pan_req,
                'sch_pan_get' => $get_sch_pan,
                'pan_required_by' => $record->pan_required_by,
                'pan_req_amt' => $record->pan_req_amt,
                'sch_pan_req_amt' => $record->sch_pan_req_amt,
                'cus_overall_amount' => $record->cus_overall_amount,
                'direct_to_kyc' => $direct_to_kyc,
                'disable_pay' => $record->disable_pay,
                'allow_cash_limit' => $allow_cash_limit,
                'allow_general_advance' => $record->allow_general_advance,
                'flx_denomintion' => $flx_denomintion,
                'due_type' => $due_type,
                'allowed_dues' => $allowed_due,
                //'metal_rate'                    => $record->id_metal ==1 ? $metal_rates['goldrate_22ct'] : $metal_rates['silverrate_1gm'], // KVP :: For RHR Metal Purity wise rate
                'metal_rate' => $metal_rate,
                'allow_advance' => $record->allow_advance,
                'advance_months' => $record->advance_months,
                'currentmonth_adv_paycount' => $record->currentmonth_adv_paycount,
                'min' => $record->min_amount,
                'max' => $record->max_amount,
                //'min_amount' 				    => (($record->scheme_type==3) && ($record->paid_installments>0 || $record->get_amt_in_schjoin==1) && ($record->firstPayamt_as_payamt==1 &&($record->flexible_sch_type==1 ||$record->flexible_sch_type==2 )) ?$record->firstPayment_amt :$record->min_amount),
                //'max_amount'                      =>( ($record->scheme_type==3 && ($record->paid_installments>0||$record->get_amt_in_schjoin==1) && $record->firstPayamt_payable==1 ||$record->firstPayamt_as_payamt==1 || $record->is_registered==1) ?$record->firstPayment_amt :$record->max_amount),
                'firstname' => $record->firstname, // New code 05-12-2022
                'is_registered' => $record->is_registered,
                'min_amt_chance' => $record->min_amt_chance,
                'max_amt_chance' => $record->max_amt_chance,
                'gst' => $record->gst,
                'firstPayamt_as_payamt' => $record->firstPayamt_as_payamt,
                'flexible_sch_type' => $record->flexible_sch_type,
                'get_amt_in_schjoin' => $record->get_amt_in_schjoin,
                'firstPayamt_maxpayable' => $record->firstPayamt_maxpayable,
                'scheme_overall_amount' => $record->scheme_overall_amount,
                'id_customer' => $record->id_customer,
                'allow_unpaid' => $record->allow_unpaid,
                'gst_type' => $record->gst_type,
                'currentmonth_adv_paycount' => $record->currentmonth_adv_paycount,
                'currentmonthpaycount' => $record->currentmonthpaycount,
                'mobile' => $record->mobile,
                'current_date' => $dates,
                'totalunpaid' => $record->totalunpaid,
                'id_scheme_account' => $record->id_scheme_account,
                'start_date' => $record->start_date,
                'chit_number' => $record->chit_number,
                'account_name' => $record->account_name,
                'discount_type' => $record->discount_type,
                'discount_installment' => $record->discount_installment,
                'firstPayDisc_value' => ($record->firstPayDisc_by == 1 ? $record->firstPayDisc_value : (($record->scheme_overall_amount * $record->firstPayDisc_value) / 100)),
                'discount' => $record->discount,
                'scheme_name' => $record->scheme_name,
                'code' => $record->code,
                'scheme_type' => $record->scheme_type,
                'currency_symbol' => $record->currency_symbol,
                'wgt_convert' => $record->wgt_convert,
                'total_installments' => $record->total_installments,
                'paid_installments' => $record->paid_installments,
                'total_paid_amount' => $record->total_paid_amount,
                'total_saved_benefit' => $record->total_saved_benefit,
                'total_paid_weight' => $record->total_paid_weight,
                'current_total_amount' => $record->current_total_amount,
                'current_paid_installments' => $record->current_paid_installments,
                'current_chances_used' => $record->current_chances_used,
                'current_chances_use' => $record->current_chances_use,
                'current_total_weight' => $record->current_total_weight,
                'last_paid_duration' => $record->last_paid_duration,
                'last_paid_date' => $record->last_paid_date,
                'is_pan_required' => $record->is_pan_required,
                'last_transaction' => $this->getLastTransaction($record->id_scheme_account),
                'isPaymentExist' => $this->isPaymentExist($record->id_scheme_account),
                'previous_amount_eligible' => $record->previous_amount_eligible,
                'cur_month_pdc' => $record->cur_month_pdc,
                'is_flexible_wgt' => $record->is_flexible_wgt,
                'max_chance' => $max_chance,
                'ref_benifitadd_ins' => $record->ref_benifitadd_ins,
                'ref_benifitadd_ins_type' => $record->ref_benifitadd_ins_type,
                'referal_code' => $record->referal_code,
                /*'allow_pay'                   => ($record->scheme_type==3  &&$record->paid_installments <  $record->total_installments  && $record->current_chances_use < $record->max_chance &&$record-> current_total_amount < $record-> max_amount?'Y':($record->disable_payment != 1 && ($record->payment_status !=2) ? ($record->cur_month_pdc < 1 ? ($record->paid_installments <= $record->total_installments ?($record->is_flexible_wgt?($record->current_total_weight >= $record->max_weight || $record->current_chances_used >= $record->max_chance ?'N':'Y'):($record->paid_installments <  $record->total_installments ?($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'N')):'N'):'N'):'N')),*/
                'allow_pay' => $allow_pay,
                // 'allow_pay'                  => ($checkDues ? ($allowSecondPay == FALSE ? ($record->scheme_type==3  && $record->paid_installments <= $record->total_installments  && $record->current_chances_use < $record->max_chance && ($record-> current_total_amount < $record-> max_amount || $record-> current_total_weight < $record-> max_weight ) ?'Y':($record->disable_payment != 1 && ($record->payment_status !=2) ? ($record->cur_month_pdc < 1 ? ($record->paid_installments <= $record->total_installments ?($record->is_flexible_wgt?($record->current_total_weight >= $record->max_weight || $record->current_chances_used >= $record->max_chance ?'N':($record->paid_installments == $record->total_installments && $record->currentmonthpaycount == 0 ? 'N':'Y')):($record->paid_installments <  $record->total_installments ?($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'N')):'N'):'N'):'N')):'Y' ) : 'N'),
                // 'allowed_dues'  			    =>$allowed_due,
                'allow_preclose' => ($record->currentmonthpaycount == 1 ? ($record->allow_preclose == 1 ? ($record->total_installments - $record->paid_installments == $record->preclose_months ? 1 : 0) : 0) : 0),
                'pdc_payments' => ($record->cur_month_pdc > 0 ? $this->get_postdated_payment($record->id_scheme_account) : 0),
                'total_pdc' => (isset($pdc_det) && $pdc_det != '' ? $pdc_det : 0),
                'weights' => ($record->scheme_type == '1' || $record->flexible_sch_type == 8 || $record->firstPayment_as_wgt == 1 ? $this->getWeights($record->min_weight, $record->max_weight) : ''),
                'preclose' => ($record->allow_preclose == 1 ? $record->preclose_months : 0),
                'preclose_benefits' => ($record->allow_preclose == 1 ? $record->preclose_benefits : 0),
                'payment_chances' => $record->payment_chances,
                'otp_price_fixing' => $record->otp_price_fixing,
                'cash_pay' => $record->cash_pay,
                'id_branch' => $record->id_branch,
                'cost_center' => $record->cost_center,
                'name' => $record->name,
                'months_from_startdate' => $record->months_from_startdate,
                'maturity_type' => $record->maturity_type,
                'missed_ins' => $record->missed_ins,
                'maturity_installment' => $record->maturity_installment,
                'firstPayment_amt' => $record->firstPayment_amt,
                'firstpayment_wgt' => $record->firstpayment_wgt,
                'one_time_premium' => $record->one_time_premium,
                'get_amt_in_schjoin' => $record->get_amt_in_schjoin,
                'set_as_min_from' => $record->set_as_min_from,
                'set_as_max_from' => $record->set_as_max_from,
                'allowed_dues' => ($record->is_flexible_wgt == 1 ? 1 : $allowed_due),
                'id_agent' => $record->id_agent,
                'agent_code' => $record->agent_code,
                'id_scheme' => $record->id_scheme,
                'emp_refferal' => $record->emp_refferal,
                'cus_refferal' => $record->cus_refferal,   // 22-10
                'agent_refferal' => $record->agent_refferal,
                'current_ins' => $record->paid_installments + 1,
                'disable_acc_payments' => $disable_acc_payments,
                'disable_pay' => $record->disable_pay,
                'disable_pay_amt' => $record->disable_pay_amt,
                'csh_payments' => $csh_payments,
                'is_otp_scheme' => $record->one_time_premium,
                'wgt_store_as' => $record->wgt_store_as,
                'reference_no' => (isset($record->reference_no) && ($record->reference_no != '') ? $record->reference_no : ''),
                'sync_scheme_code' => (isset($record->sync_scheme_code) && ($record->sync_scheme_code != '') ? $record->sync_scheme_code : ''),
                'nominee_name' => (isset($record->nominee_name) && ($record->nominee_name != '') ? $record->nominee_name : ''),
                'nominee_relationship' => (isset($record->nominee_relationship) && ($record->nominee_relationship != '') ? $record->nominee_relationship : ''),
                'nominee_address1' => (isset($record->nominee_address1) && ($record->nominee_address1 != '') ? $record->nominee_address1 : ''),
                'nominee_address2' => (isset($record->nominee_address2) && ($record->nominee_address2 != '') ? $record->nominee_address2 : ''),
                'nominee_mobile' => (isset($record->nominee_mobile) && ($record->nominee_mobile != '') ? $record->nominee_mobile : ''),
                'emp_name' => (isset($record->emp_name) && ($record->emp_name != '') ? $record->emp_name : ''),
                'referal_code' => (isset($record->referal_code) && ($record->referal_code != '') ? $record->referal_code : ''),
                'daily_pay_limit' => (isset($record->daily_pay_limit) && ($record->daily_pay_limit != '') ? $record->daily_pay_limit : ''),   //DGS-DCNM
                'curday_total_paid' => (isset($record->curday_total_paid) && $record->curday_total_paid != '' ? $record->curday_total_paid : 0),       //DGS-DCNM
                'restrict_payment' => (isset($record->restrict_payment) && ($record->restrict_payment != '') ? $record->restrict_payment : 0),   //DGS-DCNM
                'joined_date_diff' => (!empty($record->joined_date_diff) ? $record->joined_date_diff : 1),    //DGS-DCNM,
                'total_days_to_pay' => (isset($record->total_days_to_pay) && $record->total_days_to_pay != '' ? $record->total_days_to_pay : ''),       //DGS-DCNM
                //added by Durga 22.05.2023
                'payOtherBranch' => $this->config->item('payOtherBranch'),
                'avg_payable' => $record->avg_payable
            );
        }
        return $schemeAcc;
    }
    function get_pending_pdc($id_scheme_account)
    {
        $sql = "Select
				    id_scheme_account,
				    Count(id_post_payment) as total_pdc,
				    Max(date_payment) as last_paid_date
				From postdate_payment
				Where (payment_status=2 or payment_status =7) and id_scheme_account='" . $id_scheme_account . "'
				Group By id_scheme_account";
        return $this->db->query($sql)->row('total_pdc');
    }
    function get_allpostdated_payment($id_scheme_account)
    {
        $sql = "Select
				   pp.id_scheme_account,
				   date(pp.date_payment) as date_payment,
				   pp.pay_mode,
				   pp.cheque_no,
				   pp.payee_acc_no,
				   b.bank_name,
				   b.short_code,
				   pp.payee_branch,
				   pp.payee_ifsc,
				   pp.amount
			From postdate_payment pp
			Left Join bank b On (pp.payee_bank=b.id_bank)
			Where  (pp.payment_status = 2 or pp.payment_status=7)
			       And pp.id_scheme_account='" . $id_scheme_account . "'";
        return $this->db->query($sql)->row_array();
    }
    function get_postdated_payment($id_scheme_account)
    {
        $sql = "Select
				   pp.id_scheme_account,
				   date(pp.date_payment) as date_payment,
				   pp.pay_mode,
				   pp.cheque_no,
				   pp.payee_acc_no,
				   b.bank_name,
				   b.short_code,
				   pp.payee_branch,
				   pp.payee_ifsc,
				   pp.amount
			From postdate_payment pp
			Left Join bank b On (pp.payee_bank=b.id_bank)
			Where (Date_Format(Current_Date(),'%Y%m')=Date_Format(pp.date_payment,'%Y%m'))
			       And (pp.payment_status = 2 or pp.payment_status=7)
			       And pp.id_scheme_account='" . $id_scheme_account . "'";
        return $this->db->query($sql)->row_array();
    }
    function getWeights($min_weight = 0, $max_weight = 0)
    {
        //esakki(order by)
        if ($min_weight > 0 && $max_weight > 0) {
            $sql = "Select * from weight where active = 1 and weight between " . (float)$min_weight . " and " . (float)$max_weight . " order by weight";
        } else {
            $sql = "Select * from weight order by weight";
        }
        return $this->db->query($sql)->result_array();
    }
    //get last paid entry
    function getLastTransaction($id_scheme_account)
    {
        $sql = "Select * from payment			
			  Where payment_status=1
			  And id_scheme_account='$id_scheme_account'";
        return $this->db->query($sql)->row_array();
    }
    function isPaymentExist($id_scheme_account)
    {
        $sql = "Select
					  sa.id_scheme_account,c.mobile
				From payment p
				Left Join scheme_account sa On (p.id_scheme_account = sa.id_scheme_account)
				Left Join customer c on (sa.id_customer = c.id_customer)
				Where (p.payment_status = 1) And sa.id_scheme_account= '" . $id_scheme_account . "' ";
        $records = $this->db->query($sql);
        if ($records->num_rows() > 0) {
            return TRUE;
        }
    }
    function get_payment_details()
    {
        $sql = "Select
				sa.id_scheme_account,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,c.mobile,flexible_sch_type,
				s.code,if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,s.amount,s.total_installments,
				sa.ref_no,IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,sa.start_date,
				if(sa.balance_amount is null,0,sa.balance_amount) as balance_amount,
				(month(now()) - month(sa.start_date)) as duration,
				if(sa.paid_installments is null,0,sa.paid_installments) as paid_installments,
				if(count(distinct month(p.date_payment)) is null,0,count(distinct month(p.date_payment))) as paid,
				(if(sa.paid_installments is null,0,sa.paid_installments) + if(count(distinct month(p.date_payment)) is null,0,count(distinct month(p.date_payment)))) as total_paid,
				(if(s.total_installments is null,0,s.total_installments) -     (if(sa.paid_installments is null,0,sa.paid_installments) + if(count(distinct month(p.date_payment)) is null,0,count(distinct month(p.date_payment))))
				 ) as pending_installments,
				 (if(max(distinct month(p.date_payment)) is null,0,max(distinct month(p.date_payment)))) as last_paid_month,
				(if(if(max(distinct month(p.date_payment)) is null,0,max(distinct month(p.date_payment))) < month(now()),'Un Paid','Paid'))  as status
			From " . self::ACC_TABLE . " sa
			Left Join " . self::PAY_TABLE . " p On (sa.id_scheme_account=p.id_scheme_account)
			Left Join " . self::CUS_TABLE . " c On (sa.id_customer=c.id_customer)
			Left Join " . self::SCH_TABLE . " s On (sa.id_scheme=s.id_scheme)
			Where sa.active = 1 and is_closed = 0
			Group By sa.id_scheme_account
			Having pending_installments > 0 and total_paid <= s.total_installments";
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    function get_payment_dues_details()
    {
        $sql = "Select
					sa.id_scheme_account,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,c.mobile,
					s.code,if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,s.amount,s.total_installments,
					sa.ref_no,IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,sa.start_date,
					if(sa.balance_amount is null,0,sa.balance_amount) as balance_amount,
					(month(now()) - month(sa.start_date)) as duration,
					if(sa.paid_installments is null,0,sa.paid_installments) as paid_installments,
					if(count(distinct month(p.date_payment)) is null,0,count(distinct month(p.date_payment))) as paid,
					(if(sa.paid_installments is null,0,sa.paid_installments) + if(count(distinct month(p.date_payment)) is null,0,count(distinct month(p.date_payment)))) as total_paid,
					(if(s.total_installments is null,0,s.total_installments) -     (if(sa.paid_installments is null,0,sa.paid_installments) + if(count(distinct month(p.date_payment)) is null,0,count(distinct month(p.date_payment))))
					 ) as pending_installments,
					 (if(max(distinct month(p.date_payment)) is null,0,max(distinct month(p.date_payment)))) as last_paid_month,
					(if(if(max(distinct month(p.date_payment)) is null,0,max(distinct month(p.date_payment))) < month(now()),'Un Paid','Paid'))  as status
				From " . self::ACC_TABLE . " sa
				Left Join " . self::PAY_TABLE . " p On (sa.id_scheme_account=p.id_scheme_account)
				Left Join " . self::CUS_TABLE . " c On (sa.id_customer=c.id_customer)
				Left Join " . self::SCH_TABLE . " s On (sa.id_scheme=s.id_scheme)
				Where sa.active = 1 and is_closed = 0
				Group By sa.id_scheme_account
				Having pending_installments > 0 and total_paid <= s.total_installments and status!='Paid'";
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    function get_payment_employee()
    {
        $sql = "Select   
			sa.id_scheme_account,p.id_employee,concat(emp.firstname,' ',emp.lastname)as  employee_name,
			if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,c.mobile,
			IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,  sa.ref_no,sa.scheme_acc_number,
		   if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
			  p.payment_amount,p.payment_status
			From  payment p
			Left Join scheme_account sa on (p.id_scheme_account=sa.id_scheme_account)
			Left Join  scheme s on (sa.id_scheme=s.id_scheme)
			Left Join customer c on (sa.id_customer=c.id_customer)
            Left Join employee emp on (p.id_employee=emp.id_employee)
			LEFT Join payment_status_message psm ON (p.payment_status=psm.id_status_msg)
			Where p.payment_status =1";
        $payments = $this->db->query($sql)->result_array();
        // $sql="select  concat(emp.firstname,' ',emp.lastname)as  employee_name,emp.id_employee from employee emp";
        // $employee = $this->db->query($sql)->result_array();
        return $payments;
    }
    function get_payment_list($from_date, $to_date, $id_branch, $id_emp)
    {
        $date_type = $this->input->post('date_type');
        $branch_settings = $this->session->userdata('branchWiseLogin');
        $log_branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        //print_r($date_type);exit;
        $sql = "Select   
			sa.id_scheme_account,p.id_employee,concat(emp.firstname,' ',emp.lastname) as  employee_name,
			if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,c.mobile,
			IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,  sa.ref_no,sa.scheme_acc_number,
		   if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
			  p.payment_amount,p.payment_status,date(p.date_payment) as date_payment
			From  payment p
			Left Join scheme_account sa on (p.id_scheme_account=sa.id_scheme_account)
			Left Join  scheme s on (sa.id_scheme=s.id_scheme)
			Left Join customer c on (sa.id_customer=c.id_customer)
            left  join branch b on (b.id_branch=p.id_branch)
            Left Join employee emp on (emp.id_employee=p.id_employee)
			LEFT Join payment_status_message psm ON (p.payment_status=psm.id_status_msg)
			Where p.id_employee is not null and p.payment_status =1 " . ($from_date != '' && $to_date != '' ? " and (date(" . ($date_type != '' ? ($date_type == 2 ? "p.custom_entry_date" : "p.date_payment") : "p.date_payment") . ") BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')" : '') . "  " . ($id_emp != '' ? "and p.id_employee=" . $id_emp . "" : '') . " 
			" . ($uid != 1 ? ($branch_settings == 1 ? ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : " and (p.id_branch=" . $log_branch . " or b.show_to_all=1 or b.show_to_all=3)") : '') : ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : '')) . " 
			" . ($id_company != '' && $company_settings == 1 ? " and emp.id_company='" . $id_company . "'" : '') . "";
        //print_r($sql);exit;
        $payments = $this->db->query($sql)->result_array();
        return $payments;
    }
    function get_employee_name($id)
    {
        $company_settings = $this->session->userdata('company_settings');
        /* $sql="select  concat(emp.firstname,' ',emp.lastname)as  employee_name,emp.id_employee from employee emp where emp.active=1 
        ".($id_branch!=0 ?" and emp.login_branches=".$id_branch."" :'').""; */
        $sql = "select  concat(emp.firstname,' ',emp.lastname)as  employee_name,emp.id_employee from employee emp where emp.active=1 
        " . ($id != '' && $company_settings == 1 ? " and emp.id_company='" . $id . "'" : ($id != 0 ? " and emp.login_branches=" . $id . "" : '')) . " 
         ";
        //	print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    /*	function get_payment_report()
    {
        $branchWiseLogin=$this->session->userdata('branchWiseLogin');
        $id_branch=$this->session->userdata('id_branch');
        $uid=$this->session->userdata('uid');	 
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');	
        $id_branch=$this->input->post('id_branch');
        $from_date=$this->input->post('from_date');
        $to_date=$this->input->post('to_date');
        $id_scheme=$this->input->post('id_scheme');
        //AND (date(sa.start_date)) between '".$from_date."' and '".$to_date."' 
            //and Date_Format(dt.date_range,'%M,%Y') >= Date_Format(date(sa.start_date),'%M,%Y') 	
        $sql = $this->db->query("SELECT IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,s.min_amount,0))) as payable,IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,s.min_amount,0))) as payable, s.scheme_name,ifnull(sa.account_name,'-') as account_name,
                CONCAT(s.code,'-',IFNULL(sa.scheme_acc_number,'Not Allocated')) as old_scheme_acc_number,
                IFNULL(sa.scheme_acc_number,'NOT ALLOCATED') as scheme_acc_number,
                IFNULL(sa.start_year,'') as start_year,
                chit.scheme_wise_acc_no,
                chit.receiptNo_displayFrmt,
                chit.scheme_wise_receipt,
                  (select br.short_name from branch br where br.id_branch = sa.id_branch) as acc_branch,
                  chit.schemeaccNo_displayFrmt,
                   s.is_lucky_draw,
                   IFNULL(sa.group_code,'') as group_code,
                s.code,
        c.id_customer,CONCAT(c.firstname,' ',IFNULL(c.lastname,'')) as customer_name,c.mobile,s.code,s.scheme_name,Date_Format(sa.start_date,'%d-%m-%Y') as start_date,s.total_installments,ifnull(res.unpaid_month,Date_Format(sa.start_date,'%M,%Y')) as unpaid_month,sa.id_scheme_account,
                IFNULL((SELECT Date_Format(max(p.date_payment),'%d-%m-%Y') from payment p where p.id_scheme_account = sa.id_scheme_account AND  p.payment_status IN (1,2)),'-') as last_paid_date,
                (SELECT SUM(p.payment_amount) from payment p where p.id_scheme_account = sa.id_scheme_account AND  p.payment_status IN (1,2)) as paid_amount,
                (SELECT COUNT(Distinct Date_Format(p.date_payment,'%Y%m')) from payment p where p.id_scheme_account = sa.id_scheme_account AND  p.payment_status IN (1,2)) as oldpaid_ins,
                IFNULL((select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 AND s.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=sa.id_scheme_account group by pay.id_scheme_account),0) as paid_ins
                FROM scheme_account sa 
                LEFT JOIN customer c ON (c.id_customer = sa.id_customer)
                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                LEFT JOIN branch b ON (b.id_branch = sa.id_branch)
                join chit_settings chit
                join (SELECT @months:= 0) months 
                LEFT JOIN (select Date_Format(date_range,'%Y-%m') AS res_range ,Date_Format(date_range,'%d-%m-%Y') as result_date, Date_Format(date_range,'%M,%Y') as unpaid_month,sa.id_scheme_account
                        FROM scheme_account sa
                        JOIN (select (date_add('".$from_date."' - INTERVAL 1 MONTH, INTERVAL (@months := @months +1 ) month)) as date_range  from customer) dt
                        where dt.date_range between '".$from_date."' and '".$to_date."'   
                ) res on res.id_scheme_account = sa.id_scheme_account
                WHERE s.total_installments != (SELECT COUNT(Distinct Date_Format(p.date_payment,'%Y%m')) from payment p where p.id_scheme_account = sa.id_scheme_account) and sa.active=1 and sa.is_closed = 0 AND res.res_range NOT IN (SELECT p.due_monthyear FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND  p.due_monthyear  is not null and p.payment_status IN (1,2))
                ".($id_company!='' &&  $company_settings == 1? " and c.id_company='".$id_company."'":'')." 
                ".($branch!=0 ? " and sa.id_branch=".$branch :'')."
                ".($id_scheme!=0 ? " and s.id_scheme=".$id_scheme :'')."
                ".($uid!=1 ? ($branchWiseLogin==1? ($id_branch!='' ?  " and( sa.id_branch=".$id_branch." or b.show_to_all=1 )":''):''):'')."
                ");	
    //	print_r($sql);exit;		
        $result = []; 
        $unpaid = $sql->result_array();
        if($sql->num_rows() > 0){
            foreach($unpaid as $rcpt){
                $rcpt['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account',$rcpt['id_scheme_account']);
                //$rcpt['receipt_no'] = $this->customer_model->format_accRcptNo('Receipt',$rcpt['id_payment']);
                $result[] = $rcpt;
            } 
        }
        foreach($result as $r){
            $payment_dues[$r['scheme_name']][]=$r;
        }
        $summary=array();
        foreach($result as $r){
          $summary[$r['scheme_name']][]=$r;
        }
        $return_data = array('list' => $payment_dues,'summary' => $summary);
      return $return_data;
    }*/
    //unpaid report starts here 
    function get_payment_report()
    {
        $payment_dues = [];
        $summary = [];
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $id_branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $branch = $this->input->post('id_branch');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $id_scheme = $this->input->post('id_scheme');
        $id_branch = ($this->session->userdata('id_branch') > 0) ? $this->session->userdata('id_branch') : $this->input->post('id_branch');
        //and Date_Format(dt.date_range,'%M,%Y') >= Date_Format(date(sa.start_date),'%M,%Y') 	
        // PERIOD_DIFF('" . date('Ym') . "',date_format(start_date,'%Y%m'))+1  as ins_till_date,
        $sql = "SELECT cls.classification_name,IFNULL(sa.group_code,'-')as group_code,
                    IF(
                        s.installment_cycle = 0,
                        PERIOD_DIFF('" . date('Ym') . "', DATE_FORMAT(sa.start_date, '%Y%m')) + 1,
                        IF(
                            s.installment_cycle = 1,
                            DATEDIFF('" . date('Ymd') . "', DATE_FORMAT(sa.start_date, '%Y%m%d')) + 1,
                            IF(
                                s.installment_cycle = 2,
                                CEIL((DATEDIFF('" . date('Ymd') . "', DATE_FORMAT(sa.start_date, '%Y%m%d')) + 1) / s.ins_days_duration),
                                '-'
                            )
                        )
                    ) AS ins_till_date,
		            c.id_customer,CONCAT(c.firstname,' ',IFNULL(c.lastname,'')) as customer_name,c.mobile,s.code,s.scheme_name,Date_Format(sa.start_date,'%d-%m-%Y') as start_date,s.total_installments,
		            0 as unpaid_month,IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,s.min_amount,0))) as payable, 
		            sa.id_scheme_account,ifnull((select br.name from branch br where br.id_branch = sa.id_branch),'-') as acc_branch,
		            CONCAT(s.code,'-',IFNULL(sa.scheme_acc_number,'Not Allocated')) as scheme_acc_number,ifnull(sa.account_name,'-') as account_name,
                    IFNULL((SELECT Date_Format(max(p.date_payment),'%d-%m-%Y') from payment p where p.id_scheme_account = sa.id_scheme_account AND  p.payment_status IN (1,2)),'-') as last_paid_date,
                    IFNULL((SELECT SUM(p.payment_amount) from payment p where p.id_scheme_account = sa.id_scheme_account AND  p.payment_status IN (1,2)),0) as paid_amount,
                    (SELECT COUNT(Distinct Date_Format(p.date_payment,'%Y%m')) from payment p where p.id_scheme_account = sa.id_scheme_account AND  p.payment_status IN (1,2)) as oldpaid_ins,
                    IFNULL((select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 AND s.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=sa.id_scheme_account group by pay.id_scheme_account),0) as paid_ins,
                    IFNULL((select concat(IFNULL(e.firstname,''),' ',IFNULL(e.lastname,''),'-',IFNULL(e.emp_code,'')) from employee e left join scheme_account ssa on ssa.referal_code=e.emp_code WHERE " . ($id_company != '' && $company_settings == 1 ? " c.id_company = e.id_company and" : '') . " ssa.id_scheme_account=sa.id_scheme_account and ssa.referal_code is not null and ssa.referal_code!='' and ssa.is_refferal_by is not null and ssa.is_refferal_by=1),'-') as referred_employee,
                    IFNULL((select concat(IFNULL(e.firstname,''),' ',IFNULL(e.lastname,''),'-',IFNULL(e.emp_code,'')) from employee e left join scheme_account ssa on ssa.id_employee=e.id_employee WHERE ssa.id_scheme_account=sa.id_scheme_account),'-') as employee_created
                FROM scheme_account sa 
                    LEFT JOIN customer c ON (c.id_customer = sa.id_customer)
                    LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                    LEFT JOIN branch b ON (b.id_branch = sa.id_branch)
                    left join sch_classify cls on (s.id_classification = cls.id_classification)
                WHERE s.active=1 and s.total_installments != (SELECT COUNT(Distinct Date_Format(p.date_payment,'%Y%m')) from payment p where p.id_scheme_account = sa.id_scheme_account) and sa.active=1 and sa.is_closed = 0 AND
                (date(sa.start_date)) between '" . date('Y-m-d', strtotime($from_date)) . "' and '" . date('Y-m-d', strtotime($to_date)) . "' 
                " . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . " 
                " . ($id_branch != 0 ? " and sa.id_branch=" . $id_branch : '') . "
                " . ($id_scheme > 0 ? " and sa.id_scheme=" . $id_scheme : '') . "
				" . ($uid != 1 ? ($branchWiseLogin == 1 ? ($id_branch != '' ? " and( sa.id_branch=" . $id_branch . " or b.show_to_all=1 )" : '') : '') : '') . "
				group by sa.id_scheme_account
				";
        //print_r($sql);exit;	
        $unpaid_details = [];
        //$unpaid_details=$this->db->query($sql)->result_array();
        $unpaid = $this->db->query($sql)->result_array();
        if ($this->db->query($sql)->num_rows() > 0) {
            foreach ($unpaid as $rcpt) {
                $rcpt['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $rcpt['id_scheme_account']);
                //$rcpt['receipt_no'] = $this->customer_model->format_accRcptNo('Receipt',$rcpt['id_payment']);
                $unpaid_details[] = $rcpt;
            }
        } else {
            $unpaid_details = $this->db->query($sql)->result_array();
        }
        /*foreach($unpaid_details as $r){
            $return_data[$r['scheme_name']][]=$r;
        }*/
        foreach ($unpaid_details as $r) {
            //$ins_till_date = $this->getMonthsDiff($r['start_date']);
            if ($r['ins_till_date'] >= $r['total_installments']) {
                $r["unpaid_month"] = ($r['total_installments'] - $r['paid_ins']);
            } else if ($r['ins_till_date'] < $r['total_installments']) {
                if ($r['ins_till_date'] > $r['paid_ins']) {
                    $r["unpaid_month"] = ($r['ins_till_date'] - $r['paid_ins']);
                } else {
                    $r["unpaid_month"] = 0;
                }
            } else {
                $r["unpaid_month"] = 0;
            }
            // $payment_dues[$r['scheme_name']][]=$r;
            if ($r["unpaid_month"] > 0) { // Returning if unpaid month greater than 0
                //print_r($r);exit;
                $payment_dues[$r['scheme_name']][] = $r;
                $summary[$r['classification_name']][$r['scheme_name']][] = $r;
            }
        }
        $return_data = array('list' => $payment_dues, 'summary' => $summary);
        return $return_data;
    }
    //unpaid report ends here 
    function get_payment_reportold($branch)
    {
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $id_branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        /*$sql="Select
                  sa.id_scheme_account,
                  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
                  c.mobile,
                  IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
                  sa.ref_no,
                  sa.scheme_acc_number,
                  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
                  if(s.scheme_type=0,s.amount,if(s.scheme_type=1,'-',s.amount)) as amount,
                   if(s.scheme_type=1,s.max_weight,'-') as Max_weight,
                  s.code,
                  sa.start_date,
                  s.total_installments,
                    IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
  as total_paid,
(s.total_installments -  IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0))
        as remaining,
                  (select count(date_payment) from payment where month(date_payment)=month(curdate()) and id_scheme_account=sa.id_scheme_account) as Payment_attempts,
                  (select count(date_payment) from payment where month(date_payment)=month(curdate()) and payment_status=1 and id_scheme_account=sa.id_scheme_account) as payment_pending,
                  round(if(sa.balance_amount is null,'0.00',sa.balance_amount)+  (select if(sum(p.payment_amount) is null,0.00,sum(p.payment_amount)) from payment p where payment_status=1 and id_scheme_account=sa.id_scheme_account  ),2) as balance_amount,
                  round((if(sa.balance_weight is null,0.00,sa.balance_weight) + (select if(sum(p.metal_weight) is null,0.00,sum(p.metal_weight))from payment p where payment_status=1 and id_scheme_account=sa.id_scheme_account  )),3) as balance_weight,
                  MONTHNAME(STR_TO_DATE(if(max(distinct month(p.date_payment)) is null,month(sa.last_paid_date),max(distinct month(p.date_payment))), '%m')) as last_paid_month,
                  if(max(distinct month(p.date_payment)) is null,if(max(distinct year(sa.last_paid_date)) < year(now()) , 'Unpaid',if(max(distinct month(sa.last_paid_date)) < month(now()),'Not Paid','Paid')) , if(max(distinct year(p.date_payment)) < year(now()) , 'Unpaid',if(max(distinct month(p.date_payment)) < month(now()),'Not Paid','Paid'))) as current_due,
                psm.payment_status as pay_status
            From ".self::PAY_TABLE." p
            Left Join ".self::ACC_TABLE." sa on (p.id_scheme_account=sa.id_scheme_account)
            Left Join ".self::BRANCH." b on (b.id_branch=sa.id_branch)
            Left Join ".self::SCH_TABLE." s on (sa.id_scheme=s.id_scheme)
            Left Join ".self::CUS_TABLE." c on (sa.id_customer=c.id_customer)
            LEFT Join payment_status_message psm ON (p.payment_status=psm.id_status_msg)
            Where p.payment_status =1 ".($branch!='' ? " and sa.id_branch=".$branch :'')." ".($uid!=1 ? ($branchWiseLogin==1? ($id_branch!='' ?  " and( sa.id_branch=".$id_branch." or b.show_to_all=1 )":''):''):'')."
            Group By sa.id_scheme_account,sa.id_scheme,s.scheme_type
            Having total_paid <= s.total_installments";*/
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $sql = "
	  		SELECT
					c.id_customer,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,c.mobile,
					s.total_installments,s.code,sa.id_scheme_account,IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,sa.disable_payment,
					IFNULL(sa.account_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,					
					if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
					if(s.scheme_type=0,s.amount,if(s.scheme_type=1,'-',s.amount)) as amount,
					if(s.scheme_type=1,s.max_weight,'-') as Max_weight,
					Date_Format(sa.start_date,'%d-%m-%Y') as start_date,
  					IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,s.min_amount,0))) as payable,
IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or s.scheme_type=3 , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
  as paid_installments,
   					IFNULL(12 * (YEAR(sa.start_date) - YEAR(CURRENT_DATE)) + (MONTH(sa.start_date) - MONTH(CURRENT_DATE)),0) AS months_count, 
   					IFNULL(if(Date_Format(max(p.date_add),'%Y%m') = Date_Format(curdate(),'%Y%m'), (select SUM(ip.no_of_dues) from payment ip where Date_Format(ip.date_add,'%Y%m') = Date_Format(curdate(),'%Y%m') and sa.id_scheme_account = ip.id_scheme_account),IF(sa.is_opening=1, if(Date_Format(sa.last_paid_date,'%Y%m') = Date_Format(curdate(),'%Y%m'), 1,0),0)),0) as currentmonthpaycount,
   					'Unpaid' as current_due,
					p.payment_status,
					IFNULL(Date_Format(max(p.date_add),'%d-%m-%Y'),IFNULL(IF(sa.is_opening=1,Date_Format(sa.last_paid_date,'%d-%m-%Y'),'')  ,0)) as last_paid_date
				FROM scheme_account sa
				LEFT JOIN scheme s On (sa.id_scheme=s.id_scheme)
				LEFT JOIN branch b on (b.id_branch=sa.id_branch)
				LEFT JOIN payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=2 or p.payment_status=1))
				LEFT JOIN customer c On (sa.id_customer=c.id_customer and c.active=1)
				LEFT JOIN postdate_payment pp On (sa.id_scheme_account=pp.id_scheme_account and (pp.payment_status=2 or pp.payment_status=7) and (Date_Format(pp.date_payment,'%Y%m')=Date_Format(curdate(),'%Y%m')))	
				WHERE sa.active=1 and sa.is_closed = 0 " . ($branch != 0 ? " and sa.id_branch=" . $branch : '') . "
				" . ($uid != 1 ? ($branchWiseLogin == 1 ? ($id_branch != '' ? " and( sa.id_branch=" . $id_branch . " or b.show_to_all=1 )" : '') : '') : '') . "
				" . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . "
				" . ($id_company != '' && $company_settings == 1 ? " and s.id_company='" . $id_company . "'" : '') . "
				GROUP BY sa.id_scheme_account
				HAVING currentmonthpaycount = 0 and paid_installments > 0 and paid_installments < total_installments";
        //print_r($sql);exit;
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    function get_payment_detail($id)
    {
        $sql = "Select
			  IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,concat(c.firstname,' ',c.lastname) as name,sa.account_name,sa.ref_no,c.mobile,
			  s.scheme_name,s.code,if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,sa.start_date,s.amount,s.total_installments,
			  count(distinct month(p.date_payment)) as total_paid, if(max(month(p.date_payment))<Now(),'Pay','Paid') as  status
		From  " . self::ACC_TABLE . " sa
		Left Join " . self::CUS_TABLE . " c on (sa.id_customer=c.id_customer)
		Left Join " . self::SCH_TABLE . " s on (sa.id_scheme=s.id_scheme)
		Left Join " . self::PAY_TABLE . " p on (sa.id_scheme_account = p.id_scheme_account)
		Group By sa.id_scheme_account
		Having total_paid<=s.total_installments and status!='Paid'
		Where id_scheme=" . $id;
        $payments = $this->db->query($sql);
        return $payments->row_array();
    }
    // Metal rate back date entry esakki-20250729
    function get_account_payment($id_scheme_account)
    {
        $sql = $this->db->query("SELECT
				  p.id_payment,p.gst,p.gst_type, p.saved_benefits,p.saved_benefit_amt ,ifnull(p.saved_benefit_amt,0) as saved_benefit_amt,
				  DATE_FORMAT(p.date_payment,'%d-%m-%Y') as date_payment,
				  p.id_scheme_account,
				  p.metal_rate, p.is_editing_enabled, Date_Format(p.metalrate_edit_date, '%d-%m-%Y %r') as metalrate_edit_date, 
				  p.installment,
				  (IFNULL(p.payment_amount,0)+IFNULL(p.old_metal_amount,0)) as payment_amount,
				  if(p.added_by=3,p.payment_type,p.payment_mode) as oldpayment_mode,p.metal_weight,s.firstPayDisc_value as discountAmt,
				  GROUP_CONCAT(pmd.payment_mode,'-',pmd.payment_amount) as payment_mode,
				   IFNULL(
                    (
                        SELECT GROUP_CONCAT(
                            CONCAT(
                                CASE 
                                    WHEN pmd.payment_mode = 'CSH' THEN CONCAT(pmd.payment_mode, '//$$//', pmd.payment_amount,'//$$//', '-','//$$//', DATE_FORMAT(pmd.payment_date, '%d-%m-%Y'))
                                    WHEN pmd.payment_mode IN ('CC', 'DC') THEN CONCAT(pmd.payment_mode,  '//$$//', pmd.payment_amount,  '//$$//',pmd.payment_ref_number,'//$$//', DATE_FORMAT(pmd.payment_date, '%d-%m-%Y'))
                                    WHEN pmd.payment_mode = 'UPI' THEN CONCAT(pmd.payment_mode,  '//$$//', pmd.payment_amount, '//$$//',IFNULL(pmd.payment_ref_number, '-'), '//$$//',DATE_FORMAT(pmd.payment_date, '%d-%m-%Y'))
                                    WHEN pmd.payment_mode = 'NB' AND pmd.NB_type = 2 THEN CONCAT(pmd.payment_mode, '- IMPS ', '//$$//', pmd.payment_amount,  '//$$//', IFNULL(pmd.payment_ref_number, '-'), '//$$//', DATE_FORMAT(pmd.net_banking_date, '%d-%m-%Y'))
                                    WHEN pmd.payment_mode = 'NB' AND pmd.NB_type = 1 THEN CONCAT(pmd.payment_mode, '- RTGS ', '//$$//',  pmd.payment_amount,  '//$$//', IFNULL(pmd.payment_ref_number, '-'),  '//$$//', DATE_FORMAT(pmd.net_banking_date, '%d-%m-%Y'))
                                    WHEN pmd.payment_mode = 'NB' AND pmd.NB_type = 3 THEN CONCAT(pmd.payment_mode, '- UPI-Transfer ', '//$$//', pmd.payment_amount, '//$$//', IFNULL(pmd.payment_ref_number, '-'),  '//$$//', DATE_FORMAT(pmd.net_banking_date, '%d-%m-%Y'))
                                    WHEN pmd.payment_mode = 'NB' AND pmd.NB_type = 4 THEN CONCAT(pmd.payment_mode, '- NEFT ', '//$$//', pmd.payment_amount,  '//$$//', IFNULL(pmd.payment_ref_number, '-'), '//$$//', DATE_FORMAT(pmd.net_banking_date, '%d-%m-%Y'))
                                    WHEN pmd.payment_mode = 'NB' AND pmd.NB_type = 5 THEN CONCAT(pmd.payment_mode, '- UPI Scanner ',  '//$$//' , pmd.payment_amount,  '//$$//', IFNULL(pmd.payment_ref_number, '-'),  '//$$//', DATE_FORMAT(pmd.net_banking_date, '%d-%m-%Y'))
                                    WHEN pmd.payment_mode = 'CHQ' THEN CONCAT(pmd.payment_mode,  '//$$//' , pmd.payment_amount,  '//$$//', IFNULL(pmd.cheque_no, '-'), '//$$//', DATE_FORMAT(pmd.cheque_date, '%d-%m-%Y'))
                                    WHEN pmd.payment_mode = 'VCH' THEN CONCAT(pmd.payment_mode,  '//$$//' , pmd.payment_amount,  '//$$//', IFNULL(pmd.card_no, '-'), '//$$//', DATE_FORMAT(pmd.payment_date, '%d-%m-%Y'))
                                    WHEN pmd.payment_mode = 'ADV_ADJ' THEN CONCAT(
                                        '<b>', pmd.payment_mode, '//$$//', pmd.payment_amount, '//$$//' ,
                                        (SELECT GROUP_CONCAT(ir.bill_no) as bill_no
                                         FROM ret_advance_utilized ra
                                         LEFT JOIN ret_issue_receipt ir ON (ir.id_issue_receipt = ra.id_issue_receipt)
                                         WHERE ra.utilized_amt = pmd.payment_amount AND ra.id_payment = p.id_payment
                                        )
                                    )
                                END
                            )
                            SEPARATOR ','
                        )
                        FROM payment_mode_details pmd
                        LEFT JOIN ret_advance_utilized ra ON (ra.id_payment = pmd.id_payment)        
                        LEFT JOIN ret_issue_receipt ir ON (ir.id_issue_receipt = ra.id_issue_receipt)
                        WHERE pmd.payment_status = 1 
                        AND pmd.is_active = 1 
                        AND pmd.id_payment = p.id_payment
                    ),
                    '-'
                ) AS ref_number,
				psm.payment_status as payment_status,
				p.remark, p.dg_other_benefit_wgt
				FROM " . self::PAY_TABLE . " p
				left join payment_mode_details pmd on (pmd.id_payment = p.id_payment and pmd.payment_status = 1 and pmd.is_active = 1)
				LEFT Join payment_status_message psm ON (p.payment_status=psm.id_status_msg)
				left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
				left join scheme s on s.id_scheme=sa.id_scheme
				 WHERE p.id_scheme_account = '$id_scheme_account' AND (p.payment_status=1 or p.payment_status=2  ) and pmd.payment_status = 1 and pmd.is_active = 1
				 group by p.id_payment,pmd.id_payment
				 order by date(p.date_payment) ASC");
        //  print_r($this->db->last_query());exit;
        $result = [];
        $payment = $sql->result_array();
        if ($sql->num_rows() > 0) {
            foreach ($payment as $rcpt) {
                $rcpt['receipt_no'] = $this->customer_model->format_accRcptNo('Receipt', $rcpt['id_payment']);
                $result[] = $rcpt;
            }
        }
        return $result;
    }
    function get_payment_modewise()
    {
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $sql = "Select pm.id_mode,pm.mode_name,pm.short_code as mode,
						 Count(Case When p.payment_status=1 Then 1 End) as success,
						 Count(Case When p.payment_status=2 Then 1 End) as awaiting,
						 Count(Case When p.payment_status=3 Then 1 End) as failed,
						 Count(Case When p.payment_status=4 Then 1 End) as cancelled,
						 Count(Case When p.payment_status=5 Then 1 End) as returned,
						 Count(Case When p.payment_status=6 Then 1 End) as refund,
						 Count(Case When p.payment_status=7 Then 1 End) as pending
						FROM  " . self::MOD_TABLE . " pm 
						Left Join payment_mode_details pmd on(pmd.payment_mode=pm.short_code AND pmd.is_active=1)
						Left Join " . self::PAY_TABLE . " p on(p.id_payment=pmd.id_payment)
						" . ($id_company != '' && $company_settings == 1 ? " 
						Left Join scheme_account sa on(sa.id_scheme_account=p.id_scheme_account)
                        LEFT JOIN customer c ON (c.id_customer = sa.id_customer)
                        WHERE c.id_company = " . $id_company . " " : '') . "
						GROUP BY pm.mode_name";
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    function ajax_get_payments($status = "", $from_date = "", $to_date = "", $header = "")
    {
        $sql = "select
		  p.id_payment,sa.ref_no,s.code,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,c.mobile,DATE_FORMAT(p.date_payment, '%d-%m-%Y') as date,p.payment_amount,p.payment_mode,p.bank_acc_no,
		  p.bank_name,p.bank_branch,p.bank_IFSC,id_transaction,if(p.payment_status=1,'Approved','Pending') as payment_status
		from " . self::PAY_TABLE . " p
		left join " . self::ACC_TABLE . " sa on (p.id_scheme_account=sa.id_scheme_account)
		left join " . self::CUS_TABLE . " c on (sa.id_customer=c.id_customer)
		left join " . self::SCH_TABLE . " s on (sa.id_scheme=s.id_scheme) ";
        switch ($status) {    //status ALL
            case 0:
                //only from date  
                if ($from_date != NULL and $to_date != NULL) {
                    $sql = $sql . " where (date(date_payment) BETWEEN '" . $from_date . "' AND '" . $to_date . "')";
                } else {
                    $sql = $sql . " where date(date_payment) ='" . $from_date . "'";
                }
                break;
            case 1:
                if ($from_date != NULL and $to_date != NULL) {
                    $sql = $sql . " where ( date(date_payment)  BETWEEN '" . $from_date . "' AND '" . $to_date . "') AND payment_status=1";
                } else {
                    $sql = $sql . " where date(date_payment) ='" . $from_date . "' AND payment_status=1";
                }
                break;
            case 2:
                if ($from_date != NULL and $to_date != NULL) {
                    $sql = $sql . " where (date(date_payment) BETWEEN '" . $from_date . "' AND '" . $to_date . "') AND payment_status=0";
                } else {
                    $sql = $sql . " where date(date_payment) ='" . $from_date . "' AND payment_status=0";
                }
                break;
        }
        return $this->db->query($sql)->result_array();
    }
    //get payments other than failure
    function get_online_payments()
    {
        $sql = "SELECT
		  p.id_payment,
		  sa.account_name,
		  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
		  c.mobile,
		  sa.ref_no,
		  s.code,
		  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
		  IFNULL(p.payment_amount,'-') as payment_amount,
		  p.metal_rate,
		  IFNULL(p.metal_weight, '-') as metal_weight,
		  IFNULL(p.date_payment,'-') as date_payment,
		  IFNULL(p.payment_mode,'-') as payment_mode,
		  IFNULL(sa.scheme_acc_number,'') as msno,
		  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
		  IFNULL(p.bank_name,'-')as bank_name,
		  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
		  IFNULL(p.bank_branch,'-') as bank_branch,
		  IFNULL(p.id_transaction,'-') as id_transaction,
		  IFNULL(p.payu_id,'-') as payu_id ,
		  IFNULL(p.card_no,'-') as card_no,		  
		  if(payment_status=1,'Approved',if(payment_status=1,'Rejected','Pending')) as payment_status,
		  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
		  IFNULL(p.remark,'-') as remark
		 FROM " . self::PAY_TABLE . " p
		 left join " . self::ACC_TABLE . " sa on(p.id_scheme_account=sa.id_scheme_account)
		 Left Join " . self::CUS_TABLE . " c on (sa.id_customer=c.id_customer)
		 left join " . self::SCH_TABLE . " s on(sa.id_scheme=s.id_scheme)
		 Where p.payment_status <> -1
		 ORDER BY p.date_payment DESC ";
        $online_pay = $this->db->query($sql);
        return $online_pay->result_array();
    }
    function getPaymentByID($id_payment)
    {
        $this->db->select('*');
        $this->db->where('id_payment', $id_payment);
        $r = $this->db->get('payment');
        return $r->row_array();
    }
    function insert_payment($data)
    {
        $status = $this->db->insert(self::PAY_TABLE, $data);
        return $status;
    }
    function add_payment()
    {
        $_POST['pay']['payment_mode'] = 'MANUAL';
        $_POST['pay']['date_payment'] = date('Y-m-d H:i:s');
        $status = $this->db->insert(self::PAY_TABLE, $_POST['pay']);
        return $status;
    }
    function update_payment_status($id, $data)
    {
        $this->db->where('id_payment', $id);
        $status = $this->db->update(self::PAY_TABLE, $data);
        return $status;
    }
    function total_payments()
    {
        $sql = "Select 'total_payments',count(id_payment) as total,
				 COUNT(CASE WHEN payment_type='Manual' or  payment_type='PDC/ECS' THEN 1 END) as manual,
				 COUNT(CASE WHEN payment_type='Payu Checkout' THEN 1 END) as online
				  from payment
				Where payment_status=1";
        return $this->db->query($sql)->row_array();
    }
    function total_paid_unpaid($from, $to, $id_branch = "")
    {
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $log_branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $sql = "SELECT sb.id_scheme_branch,sb.id_scheme,sb.id_branch,sb.scheme_active,sb.date_add,b.name,s.scheme_name,s.id_scheme,b.name, s.code,COUNT(CASE WHEN IFNULL(cp.paid_installment,0) >0 THEN 1 END) as paid, COUNT(CASE WHEN IFNULL(cp.paid_installment,0) =0 THEN 1 END) as unpaid
                FROM scheme_branch sb
                LEFT JOIN branch b on b.id_branch = sb.id_branch
                LEFT JOIN scheme s on sb.id_scheme = s.id_scheme
                Left Join scheme_account sa on(s.id_scheme=sa.id_scheme) 
                Left Join payment p on(sa.id_scheme_account=p.id_scheme_account)
                Left Join (Select sa.id_scheme_account, COUNT(Distinct Date_Format(p.date_payment,'%Y%m')) as paid_installment, COUNT(Date_Format(p.date_payment,'%Y%m')) as chances, SUM(p.payment_amount) as total_amount, SUM(p.metal_weight) as total_weight 
                           From payment p 
                           Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0) 
                           Left Join branch b on(sa.id_branch=b.id_branch)
                           Where p.payment_status =1 and Date_Format(Current_Date(),'%Y%m')=Date_Format(p.date_payment,'%Y%m') " . ($id_branch != '' && $id_branch != 0 ? " and p.id_branch=" . $id_branch : '') . "
                           Group By sa.id_scheme_account) cp On(sa.id_scheme_account=cp.id_scheme_account)
                WHERE s.active=1 and sa.active=1 and sa.is_closed=0 and date(p.date_payment) BETWEEN '$from' and '$to' " . ($id_branch != '' && $id_branch != 0 ? " and sb.id_branch=" . $id_branch : "and sb.id_branch!=0") . " 
                " . ($id_company != '' && $company_settings == 1 ? " and s.id_company='" . $id_company . "'" : '') . "
                GROUP BY sb.id_scheme_branch";
        // 		print_r($sql);
        // 		exit;
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    /* Coded by ARVK */
    function payment_datewise($date)
    {
        $sql_1 = "select sc.id_classification, sc.classification_name, 
				sum(p.payment_amount) as classification_total
			    FROM sch_classify sc
			      LEFT JOIN scheme s ON (sc.id_classification = s.id_classification)
			      LEFT JOIN scheme_account sa ON (s.id_scheme = sa.id_scheme)
			      LEFT JOIN payment p ON (sa.id_scheme_account = p.id_scheme_account)
			    WHERE sc.active=1 AND p.payment_status=1 AND p.id_scheme_account IS NOT NULL 
			    		AND p.payment_mode!='FP' AND p.due_type!='D'
			    		AND date(p.date_payment)='$date'
			      GROUP BY sc.id_classification";
        $payments['collection_report'] = $this->db->query($sql_1)->result_array();
        /*$sql_2="SELECT IFNULL(SUM(p.payment_amount),0.00) as opening_bal
                FROM payment p
            WHERE p.payment_status=1
                        AND p.payment_mode!='FP' AND p.due_type!='D'
                        AND DATE(p.date_payment)=DATE_SUB('$date', INTERVAL 1 DAY)";*/
        $sql_2 = "SELECT dc.closing_balance_amt as opening_bal
                    FROM daily_collection dc
                  WHERE dc.date= DATE_SUB('$date', INTERVAL 1 DAY)";
        $payments['collection_total'] = $this->db->query($sql_2)->row('opening_bal');
        return $payments;
    }
    function yesterday_collection($type = "", $date = "", $data = "")
    {
        switch ($type) {
            case 'get':
                $sql = "select IFNULL(sum(p.payment_amount),0.00) as total_collection,
					 				(SELECT dc.closing_balance
			                    	FROM daily_collection dc
			                  		WHERE dc.date= DATE_SUB('$date', INTERVAL 1 DAY))as old_balance
						    FROM payment p
			            WHERE p.payment_status=1
						    		AND p.payment_mode!='FP' AND p.due_type!='D'
						    		AND date(p.date_payment)='$date'";
                $payments = $this->db->query($sql);
                return $payments->row_array();
                break;
            case 'insert':
                $status = $this->db->insert(self::DC_TABLE, $data);
                return $status;
                break;
        }
    }
    /* / Coded by ARVK */
    function failed_payments()
    {
        $sql = "SELECT
		          p.id_payment,
				  IFNULL(p.id_transaction,'') as id_transaction,
				  IFNULL(p.payu_id,'') as payu_id,
                  Date_Format(p.date_payment,'%d-%m-%Y') as trans_date ,
				  IFNULL(p.receipt_no,'') as receipt_no,
				  IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
				  c.mobile as mobile,
				  IFNULL(sa.ref_no,'')as client_id,
				  IF(sa.scheme_acc_number is null,'',concat(s.code,'-',sa.scheme_acc_number)) as chit_number,
				  IFNULL(s.code,'') as group_code,
				  IFNULL(p.payment_amount,'0.00') as amount,
				  IFNULL(p.metal_weight,'0.00') as weight,
				  IFNULL(p.payment_mode,'') as payment_mode,
				  IFNULL(p.bank_name,'') as bank_name,
				  IFNULL(p.bank_branch,'') as  branch_name,
				  IFNULL(s.id_metal,'') as metal,
				  IFNULL(p.card_no,'')    as card_no,
				  IFNULL(p.payment_ref_number,'') as approval_no,
				  IFNULL(sa.id_scheme_account,'') as id_scheme_account,
				  IFNULL(p.id_payment,'') as ref_no,
				  IFNULL(sa.is_new,'') as new_customer,
				  IFNULL(p.metal_rate,'') as rate,
				  IFNULL(p.remark,'') as remark,
				  IF(p.payment_status = 1,'Success',if(p.payment_status = 1,'Rejected',if(p.payment_status = 0,'Pending','Failed')) ) as pay_status
				FROM
					payment p
				LEFT JOIN scheme_account sa ON (p.id_scheme_account = sa.id_scheme_account)
				LEFT JOIN customer c ON (sa.id_customer = c.id_customer)
				LEFT JOIN scheme s ON (sa.id_scheme = s.id_scheme)
			   WHERE (p.payment_status='-1' or p.payment_status is null) AND p.payu_id is null
			   ORDER BY p.date_payment DESC";
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    function paymentByDateRange($from, $to, $status, $mode)
    {
        $sql = "SELECT
		          p.id_payment,
				  IFNULL(p.id_transaction,'') as id_transaction,
				  IFNULL(p.payu_id,'') as payu_id,
                  Date_Format(p.date_payment,'%d-%m-%Y') as trans_date ,
				  IFNULL(p.receipt_no,'') as receipt_no,
				  IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
				  c.mobile as mobile,
				  IFNULL(sa.ref_no,'')as client_id,
				  IFNULL(sa.scheme_acc_number,'') as msno,
				  IFNULL(s.code,'') as group_code,
				  IFNULL(p.payment_amount,'0.00') as amount,
				  IFNULL(p.metal_weight,'0.00') as weight,
				  IFNULL(p.payment_mode,'') as payment_mode,
				  IFNULL(p.bank_name,'') as bank_name,
				  IFNULL(p.bank_branch,'') as  branch_name,
				  IFNULL(s.id_metal,'') as metal,
				  IFNULL(p.card_no,'')    as card_no,
				  IFNULL(p.payment_ref_number,'') as approval_no,
				  IFNULL(sa.id_scheme_account,'') as id_scheme_account,
				  IFNULL(p.id_payment,'') as ref_no,
				  IFNULL(sa.is_new,'') as new_customer,
				  IFNULL(p.metal_rate,'') as rate,
				  IFNULL((p.payment_amount),'0.00') as paid_amt,
				  IFNULL(p.remark,'') as remark,
				  psm.payment_status as pay_status
				FROM
					payment p
				LEFT JOIN scheme_account sa ON (p.id_scheme_account = sa.id_scheme_account)
				LEFT JOIN customer c ON (sa.id_customer = c.id_customer)
				LEFT JOIN scheme s ON (sa.id_scheme = s.id_scheme)
				LEFT Join payment_status_message psm ON (p.payment_status=psm.id_status_msg)
			  WHERE (p.date_payment BETWEEN '$from'  AND '$to') ";
        if ($mode != 'ALL') {
            $sql .= " AND (p.payment_mode ='$mode' OR p.id_payment IN (SELECT pmd.id_payment FROM payment_mode_details pmd WHERE pmd.payment_mode = '$mode' AND pmd.payment_status = 1 AND pmd.is_active = 1))  ";
        }
        if ($status != 'ALL') {
            $sql .= " AND p.payment_status = '$status' ";
        }
        $sql .= " ORDER BY p.date_payment DESC ";
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    //update gateway response
    function updateGatewayResponse($data, $txnid)
    {
        $this->db->where('ref_trans_id', $txnid);
        $status = $this->db->update('payment', $data);
        //print_r($this->db->last_query());exit;
        $data = $this->get_lastUpdateID($txnid);
        $result = array(
            'status' => $status,
            'id_payment' => (isset($data['id_payment']) ? $data['id_payment'] : ''),
            'id_scheme_account' => (isset($data['id_scheme_account']) ? $data['id_scheme_account'] : ''),
            'redeemed_amount' => (isset($data['redeemed_amount']) ? $data['redeemed_amount'] : 0)
        );
        return $result;
    }
    function get_lastUpdateID($txnid)
    {
        $this->db->select('id_payment,id_scheme_account,redeemed_amount');
        $this->db->where('ref_trans_id', $txnid);
        $payid = $this->db->get('payment');
        return $payid->row_array();
    }
    function get_invoiceData($payment_no, $id_scheme_account)
    {
        $login_emp = $this->session->userdata('uid');
        //if(c.has_lucky_draw=1 && sch.is_lucky_draw = 1,concat(ifnull(sch_acc.group_code,''),'  ',ifnull(sch_acc.scheme_acc_number,'Not Allocated')),concat(sch.code,'  ',IFNULL(sch_acc.scheme_acc_number,'Not Allocated')))as scheme_acc_number,
        $records = array();
        $query_invoice = $this->db->query("SELECT IF(pay.installment > 0, pay.installment, IFNULL(IF(sch_acc.is_opening = 1, sch_acc.paid_installments, 0), 0) + IF(sch.is_digi = 1, (SELECT COUNT(DISTINCT DATE(p2.date_payment)) FROM payment p2 WHERE p2.id_scheme_account = pay.id_scheme_account AND p2.payment_status = 1 AND p2.id_payment <= pay.id_payment), (SELECT COUNT(DISTINCT DATE_FORMAT(p2.date_payment, '%Y%m')) FROM payment p2 WHERE p2.id_scheme_account = pay.id_scheme_account AND p2.payment_status = 1 AND p2.id_payment <= pay.id_payment))) as emiNumber,sch.id_purity,IFNULL(cus.nominee_mobile,'') as nominee_mobile,IFNULL(cus.nominee_name,'') as nominee_name,IFNULL(cus.nominee_relationship,'') as nominee_relationship,IFNULL(cus.nominee_address1,'') as nominee_address1,IFNULL(cus.nominee_address2,'') as nominee_address2,
                            sch_acc.topup_weight,sch_acc.topup_amount,sch_acc.topup_rate,IFNULL(DATE_FORMAT(sch_acc.start_date,'%d-%m-%Y'),'') as start_date,sch.maturity_installment,sch.maturity_days,sch.closing_maturity_days,sch.maturity_type,
                            pay.topup_slab_data,IFNULL(pay.remark,'-') as pay_remarks,IFNULL(DATE_FORMAT(pay.due_date_to,'%d-%m-%Y'),'') as due_date_to,IFNULL(s.state_code,'') as state_code,IFNULL(cus.pan,'') as cus_pan_no,IFNULL(m.metal,'Metal') as metal_name,pay.id_payment,sch_acc.id_scheme_account,IFNULL(sch_acc.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sch_acc.id_branch) as acc_branch,sch.code,c.schemeaccNo_displayFrmt,sch.is_lucky_draw,ifnull(sch_acc.scheme_acc_number,'Not Allocated') as scheme_acc_number,c.scheme_wise_acc_no,
		                    IFNULL(pay.receipt_year,'') as receipt_year, (select b.name from branch b where b.id_branch = pay.id_branch) as payment_branch, sch.code,c.receiptNo_displayFrmt,ifnull(pay.receipt_no,'') as receipt_no,c.scheme_wise_receipt,
		                    pay.id_scheme_account as id_scheme_account, sch_acc.account_name,
                            if(c.has_lucky_draw=1 && sch.is_lucky_draw = 1,concat(ifnull(sch_acc.group_code,''),'  ',ifnull(sch_acc.scheme_acc_number,'Not Allocated')),
							if(c.scheme_wise_acc_no=3,concat(b.short_name,sch.code,'-',IFNULL(sch_acc.scheme_acc_number,'Not Allocated')),concat(sch.code,'  ',IFNULL(sch_acc.scheme_acc_number,'Not Allocated'))))as oldscheme_acc_number1,IFNULL(sch_acc.old_scheme_acc_number,'-') as old_scheme_acc_number,
							gst_amount,flexible_sch_type,DATE_FORMAT(sch_acc.maturity_date,'%d-%m-%Y') as maturity_date,DATE_FORMAT(pay.date_payment,'%d-%m-%Y %h:%i %p') as date_payment, sch.scheme_name as scheme_name, sch_acc.id_branch,avg_calc_ins,avg_payable,
							sch.code as sch_code,sch.hsn_code,pay.payment_amount as payment_amount,
							IF(cus.lastname is null,cus.firstname,concat(cus.firstname,' ',cus.lastname)) as name,
							cus.firstname,cus.lastname,pay.is_print_taken,pay.discountAmt,
							 if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee,	
							 IF(payment_mode = 'MULTI', (SELECT group_concat(concat(payment_mode,'-',payment_amount)) as multi_mode FROM `payment_mode_details` where id_payment=" . $payment_no . " and is_active=1),'' )as oldmulti_modes,
							(SELECT group_concat(concat(payment_mode,'-',payment_amount)) as multi_mode FROM `payment_mode_details` where id_payment=" . $payment_no . " and is_active=1) as old_multi_modes,
                            addr.address1 as address1,addr.address2 as address2,addr.address3 as address3,ct.name as city,
							addr.pincode,cus.email,if(payment_mode='CC','Credit Card',if(payment_mode='NB','Net Banking',if(payment_mode='CD','Cheque or DD',if(payment_mode='CO','Cash Pick Up',if(payment_mode='FP','Free Payment',pm.mode_name))))) as payment_mode,id_transaction,payment_ref_number,
							if((c.receipt_no_set= 1 && pay.payment_status =1 && pay.receipt_no is null ),pay.receipt_no,if((c.receipt_no_set=1 && pay.payment_status =1 && pay.receipt_no!=''),pay.receipt_no,pay.receipt_no)) as oldreceipt_no,bank_name,bank_acc_no,bank_branch,if(sch.scheme_type=0,'-',metal_weight) as metal_weight, pay.metal_rate,scheme_type,
							IF(sch_acc.is_opening=1,IFNULL(sch_acc.paid_installments,0)+ COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')),COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')))  as installment,
							DATE_FORMAT(Date_add(date_payment,Interval 1 month),'%b %Y') as next_due,pay.id_transaction as trans_id,cus.mobile, pay.due_type,ifnull(pay.add_charges,0.00) as add_charges,pay.payment_type,sch.charge_head,sch.id_scheme,pay.gst,pay.gst_type,pay.date_add,addr.id_state,s.name as state,con.name as country,
							(select SUM(pa.metal_weight)  from payment pa left
join scheme_account sch_acc on sch_acc.id_scheme_account=pa.id_scheme_account
left join scheme sch on sch.id_scheme=sch_acc.id_scheme
where pa.id_payment<='" . $payment_no . "' and pa.id_scheme_account='" . $id_scheme_account . "' and  pa.payment_status=1)as total_weight,
							(select IFNULL(IF(sch_acc.is_opening=1,IFNULL(sch_acc.paid_installments,0)+ IFNULL(if(sch.scheme_type = 1 and sch.min_weight != sch.max_weight , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sch.scheme_type = 1 and sch.min_weight != sch.max_weight or sch.scheme_type=3 , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay left
 join scheme_account sch_acc on sch_acc.id_scheme_account=pay.id_scheme_account
 left join scheme sch on sch.id_scheme=sch_acc.id_scheme
 where pay.id_payment<='" . $payment_no . "' and pay.id_scheme_account='" . $id_scheme_account . "' and  pay.payment_status=1)
  as paid_installments,sch.total_installments,IFNULL(v.village_name,'') as village_name,IFNULL(pay.old_metal_amount,0) as old_metal_amount,
  IFNULL((select emp_code from employee where id_employee=" . $login_emp . "),'') as login_employee, IF(pay.installment > 0, pay.installment, IFNULL(IF(sch_acc.is_opening = 1, sch_acc.paid_installments, 0), 0) + IF(sch.is_digi = 1, (SELECT COUNT(DISTINCT DATE(p2.date_payment)) FROM payment p2 WHERE p2.id_scheme_account = pay.id_scheme_account AND p2.payment_status = 1 AND p2.id_payment <= pay.id_payment), (SELECT COUNT(DISTINCT DATE_FORMAT(p2.date_payment, '%Y%m')) FROM payment p2 WHERE p2.id_scheme_account = pay.id_scheme_account AND p2.payment_status = 1 AND p2.id_payment <= pay.id_payment))) as installment_no ,sch.is_digi,pay.saved_benefits,pay.saved_benefit_amt,pay.id_employee
								   FROM payment as pay								
									   JOIN  chit_settings c
									   	Left Join employee e On (e.id_employee=pay.id_employee)
										LEFT JOIN scheme_account sch_acc ON sch_acc.id_scheme_account = pay.id_scheme_account
										LEFT JOIN scheme sch ON sch.id_scheme = sch_acc.id_scheme
                                        left join branch b on sch_acc.id_branch=b.id_branch
										LEFT JOIN customer as cus ON cus.id_customer = sch_acc.id_customer
										LEFT JOIN address as addr ON addr.id_customer = cus.id_customer 
										LEFT JOIN city as ct ON addr.id_city = ct.id_city
										LEFT JOIN state as s ON addr.id_state = s.id_state 
							            LEFT JOIN country as con ON addr.id_country = con.id_country 
							            LEFT JOIN village v on v.id_village=cus.id_village
							            LEFT JOIN metal m on m.id_metal = sch.id_metal
										LEFT JOIN payment_mode pm ON (pay.payment_mode = pm.short_code)
									    WHERE pay.payment_status=1 AND id_payment = '" . $payment_no . "'");
        $sqlForTotalAmt = "SELECT SUM(p.payment_amount) as total_amt, SUM(p.metal_weight) as total_weight from payment p JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account where p.id_scheme_account = '" . $id_scheme_account . "' AND p.payment_status = 1 AND sa.active = 1 AND sa.is_closed = 0";
        // print_r($sqlForTotalAmt);exit;
        $totalCalculation = $this->db->query($sqlForTotalAmt);
        foreach ($totalCalculation->result() as $row) {
            $records['total_amt'] = $row->total_amt;
            $records['total_weight'] = $row->total_weight;
        }
        if ($query_invoice->num_rows() > 0) {
            foreach ($query_invoice->result() as $row) {
                $scheme_acc_number = $this->customer_model->format_accRcptNo('Account', $row->id_scheme_account);
                $receipt_no = $this->customer_model->format_accRcptNo('Receipt', $row->id_payment);
                $purity = $this->db->query("SELECT description from ret_purity where id_purity=" . $row->id_purity)->row()->description;
                $multi_mode = $this->db->query("SELECT GROUP_CONCAT(IFNULL(if(p.added_by = 0, if(pmd.payment_mode='FP','Free payment', if(pmd.payment_mode = 'NB' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' ,CONCAT( IF(pmd.payment_mode = 'NB' && pmd.NB_type = 3,'UPI',pm.mode_name),'-', IFNULL(IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)')),if(pmd.NB_type = 1,'RTGS',if(pmd.NB_type = 2,'IMPS',if(pmd.NB_type = 3,'UPI',if(pmd.NB_type = 4,'NEFT','')))))),pm.mode_name ) ) ,pm.mode_name),p.payment_mode)) as multi_modes
                                                FROM payment p
                                                LEFT JOIN payment_mode_details pmd on pmd.id_payment = p.id_payment
                                                Left Join payment_mode pm on (pm.short_code = pmd.payment_mode)
                                                left join bank bk on(bk.id_bank=pmd.id_bank)
                                                left join ret_bill_pay_device dev on(dev.id_device = pmd.id_pay_device)
                                                WHERE p.id_payment = " . $payment_no)->row()->multi_modes;
                $referred_employee = $this->db->query("select concat(IFNULL(e.firstname,''),' ',IFNULL(e.lastname,''),'-',IFNULL(e.emp_code,'')) as ref_employee
                                                        from employee e 
                                                        left join scheme_account sa on sa.referal_code=e.emp_code 
                                                        WHERE sa.id_scheme_account=" . $row->id_scheme_account . " and sa.referal_code is not null 
                                                        and sa.referal_code!='' ")->row()->ref_employee;
                $records[] = array(
                    'referred_employee' => !empty($referred_employee) ? $referred_employee : '-',
                    'maturity_installment' => $row->maturity_installment,
                    'maturity_days' => $row->maturity_days,
                    'closing_maturity_days' => $row->closing_maturity_days,
                    'maturity_type' => $row->maturity_type,
                    'maturity_date' => $row->maturity_date,
                    'old_scheme_acc_number' => $row->old_scheme_acc_number,
                    'nominee_name' => $row->nominee_name,
                    'nominee_relationship' => $row->nominee_relationship,
                    'nominee_address1' => $row->nominee_address1,
                    'nominee_address2' => $row->nominee_address2,
                    'nominee_mobile' => $row->nominee_mobile,
                    'purity' => $purity,
                    'topup_weight' => $row->topup_weight,
                    'topup_amount' => $row->topup_amount,
                    'topup_rate' => $row->topup_rate,
                    'start_date' => $row->start_date,
                    'id_employee' => $row->id_employee,
                    'topup_slab_data' => json_decode($row->topup_slab_data, true),
                    'pay_remarks' => $row->pay_remarks,
                    'due_date_to' => $row->due_date_to,
                    'cus_pan_no' => $row->cus_pan_no,
                    'state_code' => $row->state_code,
                    'schemeaccNo_displayFrmt' => $row->schemeaccNo_displayFrmt,
                    'scheme_wise_acc_no' => $row->scheme_wise_acc_no,
                    'scheme_acc_number' => $scheme_acc_number,
                    'acc_branch' => $row->acc_branch,
                    'code' => $row->code,
                    'start_year' => $row->start_year,
                    'receiptNo_displayFrmt' => $row->receiptNo_displayFrmt,
                    'receipt_no' => $receipt_no,
                    'payment_branch' => $row->payment_branch,
                    'receipt_year' => $row->receipt_year,
                    'scheme_wise_receipt' => $row->scheme_wise_receipt,
                    'id_scheme_account' => $row->id_scheme_account,
                    'date_payment' => $row->date_payment,
                    'scheme_name' =>
                        $row->scheme_name,
                    'sch_code' => $row->sch_code,
                    'payment_amount' =>
                        $row->payment_amount,
                    'name' => $row->name,
                    'village_name' => $row->village_name,
                    $row->payment_amount,
                    'firstname' => $row->firstname,
                    'avg_calc_ins' => $row->avg_calc_ins,
                    'avg_payable' => $row->avg_payable,
                    $row->payment_amount,
                    'lastname' => $row->lastname,
                    'multi_modes' => $multi_mode,
                    'id_payment' => $payment_no,
                    'address1' => $row->address1,
                    'address2' => $row->address2,
                    'address3' => $row->address3,
                    'city' => $row->city,
                    'pincode' => $row->pincode,
                    'email' => $row->email,
                    'payment_mode' => $row->payment_mode,
                    'id_transaction' => $row->id_transaction,
                    'payment_ref_number' => $row->payment_ref_number,
                    'bank_name' => $row->bank_name,
                    'bank_acc_no' => $row->bank_acc_no,
                    'id_branch' => $row->id_branch,
                    'bank_branch' => $row->bank_branch,
                    'metal_weight' => $row->metal_weight,
                    'metal_rate' => $row->metal_rate,
                    'scheme_type' => $row->scheme_type,
                    'installment' => $row->installment,
                    'next_due' => $row->next_due,
                    'trans_id' => $row->trans_id,
                    'account_name' => $row->account_name,
                    'mobile' => $row->mobile,
                    'due_type' => $row->due_type,
                    'add_charges' => $row->add_charges,
                    'charge_head' => $row->charge_head,
                    'payment_type' => $row->payment_type,
                    'id_scheme' => $row->id_scheme,
                    'gst_type' => $row->gst_type,
                    'gst' => $row->gst,
                    'hsn_code' => $row->hsn_code,
                    'date_add' => $row->date_add,
                    'id_state' => $row->id_state,
                    'is_print_taken ' => $row->is_print_taken,
                    'state' => $row->state,
                    'country' => $row->country,
                    'paid_installments' => $row->paid_installments,
                    'total_installments' => $row->total_installments,
                    'total_weight' => $row->total_weight,
                    'employee' => $row->employee,
                    'discountAmt' => $row->discountAmt,
                    'old_metal_amount' => $row->old_metal_amount,
                    'login_employee' => $row->login_employee,
                    'metal_name' => ucfirst(strtolower($row->metal_name)),
                    'installment_no' => $row->installment_no,
                    'is_digi' => $row->is_digi,
                    'saved_benefits' => $row->saved_benefits,
                    'saved_benefit_amt' => $row->saved_benefit_amt,
                );
            }
        }
        return $records;
    }
    function monthly_rate($adjust_by)
    {
        $sql = " Select ";
        switch ($adjust_by) {
            case 1:
                $sql = $sql . " max(goldrate_22ct) as rate ";
                break;
            case 2:
                $sql = $sql . " min(goldrate_22ct) as rate ";
                break;
            case 3:
                $sql = $sql . " Round(Avg(goldrate_22ct),2) as rate ";
                break;
        }
        $sql = $sql . " From metal_rates
		            Where month(updatetime)=month(curdate())  ";
        return $this->db->query($sql)->row('rate');
    }
    function monthly_rate_variation()
    {
        $sql = "Select max(goldrate_22ct) as max_rate, min(goldrate_22ct) as min_rate, Round(Avg(goldrate_22ct),2) as avg_rate, count(id_metalrates) as count From metal_rates Where month(updatetime) = month(curdate())";
        return $this->db->query($sql)->result_array();
    }
    function purchase_rate($adjust_by, $date)
    {
        $sql = " Select ";
        switch ($adjust_by) {
            case 0:
                $sql = $sql . " max(goldrate_22ct) as rate ";
                break;
            case 1:
                $sql = $sql . " min(goldrate_22ct) as rate ";
                break;
            case 2:
                $sql = $sql . " Round(Avg(goldrate_22ct),2) as rate ";
                break;
        }
        $sql = $sql . " From metal_rates
		            Where date(updatetime)=date('" . $date . "')  ";
        return $this->db->query($sql)->row('rate');
    }
    function scheme_rate($adjust_by, $start_date, $end_date)
    {
        $sql = " Select ";
        switch ($adjust_by) {
            case 0:
                $sql = $sql . " max(goldrate_22ct) as rate ";
                break;
            case 1:
                $sql = $sql . " min(goldrate_22ct) as rate ";
                break;
            case 2:
                $sql = $sql . " Round(Avg(goldrate_22ct),2) as rate ";
                break;
        }
        $sql = $sql . " From metal_rates
		            Where date(updatetime) between date('" . $start_date . "')and date('" . $end_date . "')  ";
        return $this->db->query($sql)->row('rate');
    }
    function get_payment_by_scheme($id)
    {
        $sql = "SELECT
				    p.id_payment,
            		IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
			 		if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
            		sa.scheme_acc_number,
            		c.mobile,
            		IFNULL(p.payment_amount,'-') as payment_amount
            	FROM payment p
				    left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
				    Left Join customer c on (sa.id_customer=c.id_customer)
				    left join scheme s on(sa.id_scheme=s.id_scheme)
			    WHERE sa.id_scheme='" . $id . "' and p.payment_status=1 and p.fix_weight=0";
        return $this->db->query($sql)->result_array();
    }
    /* Coded by ARVK*/
    function getMetalRate()
    {
        $sql = "SELECT * FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1";
        return $this->db->query($sql)->row_array();
    }
    /* / Coded by ARVK*/
    function payments_by_scheme($id_scheme)
    {
        $sql = "Select
			          p.id_payment,
							  IFNULL(p.id_transaction,'') as id_transaction,
							  IFNULL(p.payu_id,'') as payu_id,
			          		  Date_Format(p.date_payment,'%d-%m-%Y') as trans_date ,
							  IFNULL(p.receipt_no,'') as receipt_no,
							  IFNULL(sa.ref_no,'')as client_id,
							  IFNULL(sa.scheme_acc_number,'') as msno,
							  IFNULL(s.code,'') as group_code,
							  IFNULL(p.payment_amount,'0.00') as payment_amount,
							  IFNULL(p.metal_weight,'0.00') as weight,
							  IFNULL(p.payment_mode,'') as payment_mode,
							  IFNULL(p.bank_name,'') as bank_name,
							  IFNULL(p.bank_branch,'') as  branch_name,
							  IFNULL(s.id_metal,'') as metal,
							  IFNULL(p.card_no,'')    as card_no,
							  IFNULL(p.payment_ref_number,'') as approval_no,
							  IFNULL(sa.id_scheme_account,'') as id_scheme_account,
							  IFNULL(p.id_payment,'') as ref_no,
							  IFNULL(sa.is_new,'') as new_customer,
							  IFNULL(p.metal_rate,'') as rate,					
							  IFNULL(p.remark,'') as remark,
			          m.goldrate_22ct as purchase_rate,
			          m.updatetime,
			          s.id_scheme
			From scheme s
			Left Join scheme_account sa on(s.id_scheme=sa.id_scheme)
			Left Join payment p on (sa.id_scheme_account=p.id_scheme_account and p.payment_status=1)
			Left Join metal_rates m on (date(updatetime) = date(p.date_payment))
			Where s.scheme_type=2 and p.fix_weight=0 and s.id_scheme='" . $id_scheme . "'
			group by id_payment";
        return $this->db->query($sql)->result_array();
    }
    function insert_settlement_detail($data)
    {
        $status = $this->db->insert(self::SETT_DET_TABLE, $data);
        return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
    }
    function view_settlement_detail($id_settlement)
    {
        $sql = " SELECT
							  p.id_payment,
							  sa.account_name,
							  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
							  c.mobile,
							  sa.scheme_acc_number,
							  s.code,
							  p.id_employee,
		                      if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee,
							  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
							  IFNULL(p.payment_amount,'-') as payment_amount,
							  p.metal_rate,
							  IFNULL(p.metal_weight, '-') as metal_weight,
							  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
					          p.payment_type,
							  p.payment_mode as payment_mode,
							  IFNULL(sa.scheme_acc_number,'') as msno,
							  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
							  IFNULL(p.bank_name,'-')as bank_name,
							  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
							  IFNULL(p.bank_branch,'-') as bank_branch,
							  IFNULL(p.id_transaction,'-') as id_transaction,
							  IFNULL(p.payu_id,'-') as payu_id ,
							  IFNULL(p.card_no,'-') as card_no,
							  psm.payment_status as payment_status,
							  p.payment_status as id_status,
							  psm.color as status_color,
							  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
							  IFNULL(p.remark,'-') as remark,
                if(sd.`type`=1,'Monthly','Purchase') as set_type,
                if(sd.`adjust_by`=1,'Highest rate',if(sd.`adjust_by`=2,'Lowest rate',if(sd.`adjust_by`=3,'Average rate','Manual rate'))) as adjust_by
		FROM settlement_detail sd
		    Left Join payment p On(sd.id_payment=p.id_payment)
				Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
				Left Join employee e On (e.id_employee=p.id_employee)
				Left Join customer c on (sa.id_customer=c.id_customer)
				Left join scheme s on(sa.id_scheme=s.id_scheme)
				Left Join payment_mode pm on (p.payment_mode=pm.id_mode)
				Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
		Where sd.id_settlement=" . $id_settlement;
        return $this->db->query($sql)->result_array();
    }
    function weight_settlementDB($type = "", $id = "", $data = "")
    {
        switch ($type) {
            case 'get':
                $sql = "Select
						        s.id_settlement,
						        schemes,
						        s.date_upd,
						        concat(e.firstname,' ',e.lastname) as employee,
						        s.success,
						        (SELECT COUNT(sd.id_settlement)
						        		FROM settlement_detail sd
						        		WHERE sd.id_settlement = s.id_settlement)as acc_count
						From settlement s
						Left Join employee e On (s.id_employee=e.id_employee)";
                return $this->db->query($sql)->result_array();
                break;
            case 'insert':
                $status = $this->db->insert(self::SETT_TABLE, $data);
                return array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
                break;
            case 'update':
                $this->db->where('id_settlement', $id);
                $status = $this->db->update(self::SETT_TABLE, $data);
                return array('status' => $status, 'updateID' => $id);
                break;
            case 'delete':
                $this->db->where('id_settlement', $id);
                $status = $this->db->delete(self::SETT_TABLE, $data);
                return array('status' => $status, 'deleteID' => $id);
                break;
            default:
                return array(
                    'id_settlement' => NULL,
                    'type' => 1,
                    'adjust_by' => 1,
                    'rate' => '0.00'
                );
                break;
        }
    }
    //fetch data to send email
    public function getPpayment_data($id)
    {
        $sql = "Select
						  p.id_payment,s.code,
						  p.id_scheme_account,c.firstname,c.lastname,c.email,c.mobile,s.scheme_name,s.total_installments,
						  IFNULL((select IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ COUNT(Distinct Date_Format(date_payment,'%Y%m')), if(s.scheme_type = 1 or s.scheme_type=3, COUNT(Distinct Date_Format(date_payment,'%Y%m')), SUM(no_of_dues))) from payment where id_scheme_account=sa.id_scheme_account and payment_status=1 group by id_scheme_account),0)as paid_installments,
						  if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,concat(ifnull(sa.group_code,''),'  ',ifnull(sa.scheme_acc_number,'Not allocated')),concat(ifnull(s.code,''),' ',ifnull(sa.scheme_acc_number,'Not allocated')))as scheme_acc_number,
					  sa.account_name,
						   IFNULL(p.id_transaction,'-') as id_transaction, 
						  p.payu_id,
						  p.id_post_payment,
						  p.id_drawee,
						  da.account_no as drawee_acc_no,
						  da.account_name as drawee_account_name,
						  Date_format(date_payment,'%d-%m-%Y') as date_payment,
						  p.payment_type,
						  p.payment_mode,
						  p.payment_amount,
						  p.add_charges,
						  p.metal_rate,
						  p.metal_weight,
						  p.cheque_date,
						   IFNULL(p.cheque_no,'-') as cheque_no,  
						  p.bank_acc_no,
						  p.bank_name,
						  p.bank_branch,
						  p.bank_IFSC,
						  p.card_no,
						  p.card_holder,
						  p.cvv,
						  p.exp_date,p.act_amount,
						  p.payment_ref_number,
						  p.payment_status as id_payment_status,
						  p.remark,
						  psm.payment_status as payment_status,
						  psm.color as status_color,cs.currency_symbol,cs.currency_name,cs.walletIntegration
						From payment p
						Left Join scheme_account sa on (p.id_scheme_account=sa.id_scheme_account)
						Left Join customer c on (sa.id_customer=c.id_customer)
						Left Join drawee_account da on (p.id_drawee=p.id_drawee)
						Left Join payment_mode pm on (p.payment_mode=pm.id_mode)
						Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
						Left join scheme s on(sa.id_scheme=s.id_scheme)
						join chit_settings cs
						Where p.id_payment=" . $id;
        return $this->db->query($sql)->row_array();
    }
    //fetch data to send email
    public function getPostpayment_data($id)
    {
        $sql = "SELECT
			      pp.id_post_payment,s.code,
			      IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,c.email,c.mobile,c.firstname,c.lastname,s.scheme_name,
			      sa.id_scheme_account,
			      sa.account_name, pp.amount as act_amount,
			      pp.metal_rate,
			      pp.weight,
			      pp.pay_mode as payment_mode,
			      Date_format(pp.date_payment,'%d-%m%-%Y') as date_payment,
			      pp.cheque_no,
			      pp.payment_status as id_payment_status,
			      psm.payment_status,
			      psm.color as status_color,
			      pp.charges,
			      pp.date_presented,cs.currency_symbol
			FROM postdate_payment pp
			Left Join scheme_account sa on (pp.id_scheme_account=sa.id_scheme_account)
			Left Join customer c on (c.id_customer=sa.id_customer)
			Left Join payment_status_message psm on (pp.payment_status=psm.id_status_msg)
      Left Join drawee_account da on (pp.id_drawee=da.id_drawee)
      Left Join bank db on (da.id_bank=db.id_bank)
      Left Join bank pb on (pp.payee_bank=pb.id_bank)
      Left join scheme s on(sa.id_scheme=s.id_scheme)
      join chit_settings cs
      Where pp.payment_status!=1 And  pp.id_post_payment=" . $id;
        return $this->db->query($sql)->row_array();
    }
    //for online payment trans details
    /*function get_online_payment($id_payment)
    {
        $sql="SELECT
          p.id_payment,p.gst,p.gst_type,ifnull(p.discountAmt,0.00) as discount,
          sa.account_name,
          if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
          c.mobile,
          sa.ref_no,
          s.code,
          if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
          IFNULL(p.payment_amount,'-') as payment_amount,
          p.metal_rate,p.act_amount,p.no_of_dues,
          IFNULL(p.metal_weight, '-') as metal_weight,
          IFNULL(p.date_payment,'-') as date_payment,
          IF(payment_mode = 'MULTI', concat('MULTI | ',(SELECT group_concat(concat(payment_mode,'-',payment_amount)) as multi_mode FROM `payment_mode_details` where id_payment=".$id_payment.")),IFNULL(p.payment_mode,'-') )as payment_mode,
          sa.scheme_acc_number,
          IFNULL(p.bank_acc_no,'-') as bank_acc_no,
          IFNULL(p.bank_name,'-')as bank_name,
          IFNULL(p.bank_IFSC,'-') as bank_IFSC,
          IFNULL(p.add_charges,0.00) as bank_charges,
          IFNULL(p.bank_branch,'-') as bank_branch,
          IFNULL(p.id_transaction,'-') as trans_id,
          IFNULL(p.payu_id,'-') as payu_id ,
          IFNULL(p.card_no,'-') as card_no,		  
          p.payment_status as id_payment_status,
          psm.payment_status as payment_status,
          IFNULL(p.payment_ref_number,'-') as payment_ref_number,
          IFNULL(SUBSTRING(p.remark,45,36),'-') as remark,cs.currency_symbol,IFNULL(p.receipt_no,'-') as receipt_no
         FROM ".self::PAY_TABLE." p
         left join ".self::ACC_TABLE." sa on(p.id_scheme_account=sa.id_scheme_account)
         Left Join ".self::CUS_TABLE." c on (sa.id_customer=c.id_customer)
         left join ".self::SCH_TABLE." s on(sa.id_scheme=s.id_scheme)
         Left Join ".self::PAY_STATUS." psm On (p.payment_status=psm.id_status_msg)
         join chit_settings cs
         Where p.id_payment=".$id_payment." 
         ORDER BY p.date_payment DESC ";
         $online_pay=$this->db->query($sql);
         return $online_pay->row_array();
    }
*/
    function get_online_payment($id_payment)
    {
        //IFNULL(SUBSTRING(p.remark,45,36),'-') as remark,
        $sql = "SELECT 
		  p.id_payment,p.gst,p.gst_type,ifnull(p.discountAmt,0.00) as discount,
		  sa.account_name,
		  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
		  c.mobile,
		  sa.ref_no,
		  s.code,
		  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
		  IFNULL(p.payment_amount,'-') as payment_amount,
		  p.metal_rate,p.act_amount,p.no_of_dues,
		  IFNULL(p.metal_weight, '-') as metal_weight,
		  IFNULL(p.date_payment,'-') as date_payment,
		  IF(payment_mode = 'MULTI', concat('MULTI | ',(SELECT group_concat(concat(payment_mode,'-',payment_amount)) as multi_mode FROM `payment_mode_details` where id_payment=" . $id_payment . " and payment_status=1 and is_active=1)),IFNULL(p.payment_mode,'-') )as payment_mode,
		  IFNULL(sa.scheme_acc_number,'NOT ALLOCATED') as scheme_acc_number,
		  IFNULL(sa.start_year,'') as start_year,
		  cs.scheme_wise_acc_no,
		  cs.receiptNo_displayFrmt,
		  cs.scheme_wise_receipt,
		  (select br.short_name from branch br where br.id_branch = sa.id_branch) as acc_branch,
		   cs.schemeaccNo_displayFrmt,
		   s.is_lucky_draw,
		   IFNULL(sa.group_code,'') as group_code,
		  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
		  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
		  IFNULL(p.add_charges,0.00) as bank_charges,
		  IFNULL(p.bank_branch,'-') as bank_branch,
		  IFNULL(p.id_transaction,'-') as trans_id,
		  IFNULL(p.payu_id,'-') as payu_id ,
		  p.payment_status as id_payment_status,
		  psm.payment_status as payment_status,
		  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
		  IFNULL(if(p.remark!='',p.remark,'-'),'-') as remark,
		  cs.currency_symbol,IFNULL(p.receipt_no,'-') as receipt_no,
		  IFNULL((select  
			GROUP_CONCAT(ifnull(pay.bank_name,IFNULL(b.bank_name,''))) 
			from payment_mode_details pay
			left join bank b on b.id_bank=pay.id_bank 
			where (pay.bank_name is not null or pay.id_bank  is not null) and pay.id_payment = p.id_payment and pay.payment_status=1 and pay.is_active=1),'-') as bank_name,
			IFNULL((select GROUP_CONCAT(IFNULL(card_no,'')) from payment_mode_details where (card_no is not null) and id_payment=p.id_payment and payment_status=1 and is_active=1),'-') as card_no,
			IFNULL(sa.id_scheme_account,'-') as id_scheme_account
		 FROM " . self::PAY_TABLE . " p
		 left join " . self::ACC_TABLE . " sa on(p.id_scheme_account=sa.id_scheme_account)
		 Left Join " . self::CUS_TABLE . " c on (sa.id_customer=c.id_customer)
		 left join " . self::SCH_TABLE . " s on(sa.id_scheme=s.id_scheme)
		 Left Join " . self::PAY_STATUS . " psm On (p.payment_status=psm.id_status_msg)
		 join chit_settings cs
		 Where p.id_payment=" . $id_payment . " 
		 ORDER BY p.date_payment DESC ";
        //print_r($sql);exit;
        $online_pay = $this->db->query($sql);
        //return $online_pay->row_array();
        $result = [];
        if ($online_pay->num_rows() > 0) {
            $account = $online_pay->row_array();
            //echo "<pre>";print_r($account);exit;
            $account['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $account['id_scheme_account']);
            $account['receipt_no'] = $this->customer_model->format_accRcptNo('Receipt', $account['id_payment']);
            $result = $account;
        }
        //echo "<pre>";print_r($account);exit;
        return $result;
        //return $online_pay->row_array();
    }
    //scheme free payments installments
    /*function get_freepaycontents()
    {
                $sql="SELECT
                            s.id_scheme, s.code, s.scheme_type, s.total_installments, 
                            s.allow_advance,s.advance_months,  s.has_free_ins,s.free_payInstallments 
                                    FROM scheme s 
                                        where 
                                            s.has_free_ins=1 and s.visible=1 and s.active=1";
                 return $this->db->query($sql)->result_array();   
    }*/
    function get_freepaycust()
    {
        $sql = "select sc.gst_type,sc.gst,
					sc.id_scheme, sc.code, sc.scheme_type, sc.total_installments, sc.amount,sc.free_payment,cs.receipt_no_set,
					sc.allow_advance,sc.advance_months,  sc.has_free_ins,sc.free_payInstallments ,s.id_scheme_account,sc.min_weight, sc.max_weight,
					IFNULL(IF(s.is_opening=1,IFNULL(s.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0)
  as paid_installments,
					cm.company_name, cm.short_code,
					IFNULL(Date_Format(max(pay.date_add),'%m-%Y'),IFNULL(IF(s.is_opening=1,Date_Format(s.last_paid_date,'%m-%Y'),'')  ,0))                 as last_paid_month				
				from scheme_account s
					left join customer c on (s.id_customer=c.id_customer)
					left join scheme sc on (sc.id_scheme=s.id_scheme)
					left join payment pay on (pay.id_scheme_account=s.id_scheme_account  and (pay.payment_status=2 or pay.payment_status=1))
					join chit_settings cs
					join company cm
				Where s.is_closed=0 and sc.has_free_ins=1 and sc.active=1
				group by s.id_scheme_account";
        return $this->db->query($sql)->result_array();
    }
    /*function get_freepayamount($data)
    {
        $sql_scheme = $this->db->query("select s.free_payment, s.amount, s.scheme_type,s.free_payInstallments, s.min_weight, s.max_weight, c.company_name, c.short_code 
                from scheme s join company c
                where s.id_scheme=".$data);
              $sch_data = $sql_scheme->row_array();
            return $sch_data;
    }*/
    // scheme free payments installments
    function isAcnoAvailable($id)
    {
        $sql = "SELECT sa.id_scheme,sa.id_branch as branch,sa.scheme_acc_number,s.is_lucky_draw,sa.ref_no FROM scheme_account sa
		Left Join scheme s on (s.id_scheme=sa.id_scheme)
		where sa.id_scheme_account=" . $id;
        $result = $this->db->query($sql);
        if ($result->num_rows() > 0) {
            //	if($result->row()->scheme_acc_number =='' || $result->row()->scheme_acc_number ==NULL){
            if (empty($result->row()->scheme_acc_number)) {
                return array('status' => TRUE, 'id_scheme' => $result->row()->id_scheme, 'branch' => $result->row()->branch, 'ref_no' => $result->row()->ref_no);
            } else {
                return array('status' => false);
            }
        } else {
            return array('status' => false);
        }
    }
    // Metal rate back date entry esakki-20250729
    function getMetalRateBydate($date, $type = "")
    {
        //$sql="SELECT max(updatetime) as date,goldrate_22ct FROM metal_rates where date_format(updatetime,'%Y-%m-%d %H:%i:%s')<= date_format('".$date."','%Y-%m-%d %H:%i:%s')" ;
        if (!empty($type) && $type == 2) {
            $sql = "SELECT CONCAT(goldrate_22ct ,' [', DATE_FORMAT(updatetime, '%H:%i:%s'), ']') AS goldrate,goldrate_22ct,updatetime FROM metal_rates where date(updatetime) = '" . $date . "' GROUP BY goldrate_22ct";
            // echo $sql;exit;
            $result = $this->db->query($sql);
            if ($result->num_rows() > 0) {
                return $result->result_array();
            } else {
                $updtime = $this->db->query("SELECT DATE_FORMAT(updatetime, '%Y-%m-%d') as updatetime FROM metal_rates where date(updatetime) < '" . $date . "' ORDER BY id_metalrates DESC LIMIT 1");
                // print_r($this->db->last_query());exit;
                return $result = $this->getMetalRateBydate($updtime->row()->updatetime, 2);
            }
        } else {
            $sql = "SELECT goldrate_22ct FROM metal_rates where date_format(updatetime,'%Y-%m-%d %H:%i:%s')<= date_format('" . $date . "','%Y-%m-%d %H:%i:%s') ORDER BY id_metalrates DESC LIMIT 1";
            //echo $sql;exit;
            $result = $this->db->query($sql);
            if ($result->num_rows() > 0) {
                return $result->row()->goldrate_22ct;
            }
        }
    }
    //new reports  
    //  payment_by_daterange chked&updtd emp login branchwise  data show//HH
    function payment_list_daterange($from_date, $to_date, $id_classfication, $id_scheme, $pay_mode, $id_branch)
    {
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $branch_settings = $this->session->userdata('branch_settings');
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $date_type = $this->input->post('date_type');
        $cus_branch = $this->input->post('cus_branch');
        $return_data = array();
        $sql = $this->db->query("SELECT  IFNULL(e.emp_code,'-') as emp_code,p.id_payment,compy.gst_number,p.is_offline,sa.id_scheme_account,
    sa.account_name,p.act_amount,sa.id_branch,sa.ref_no,sch_classify.classification_name,sch_classify.id_classification,b.name as branch_name,
    if(p.receipt_no!='',p.receipt_no ,p.id_payment)as receipt_no,
    if(c.lastname is null,c.firstname,c.firstname) as name,p.id_branch,p.added_by as added_by,
    c.mobile,b.name as pay_branch,IFNULL(sa.group_code,'') as scheme_group_code,IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,p.due_type,s.code,p.id_employee,
    if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee,if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
    IF(p.payment_mode='FP','0',p.payment_amount) as payment_amount,
    IF(p.payment_mode='FP',p.payment_amount,'0') as incentive,
    s.firstPayDisc_value as discountAmt,
    IF(s.gst_type=0,(p.payment_amount-				
    (p.payment_amount*(100/(100+s.gst))))/2,((p.payment_amount*(s.gst/100))/2)) as sgst,
    IF(s.gst_type=0,(p.payment_amount-			
    (p.payment_amount*(100/(100+s.gst))))/2,((p.payment_amount*(s.gst/100))/2)) as cgst,
    IFNULL(p.payment_amount, '0.00') as amount,
    p.metal_rate,IF(p.metal_weight!='0' && p.metal_weight!='' ,p.metal_weight,'0') as metal_weight,
    IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
    IFNULL(Date_format(p.approval_date,'%d-%m%-%Y'),'-') as approval_date,
    IFNULL(e.emp_code,'-') as emp_code,
    p.payment_type,sa.is_closed, sa.active,
    IFNULL((select IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ COUNT(Distinct Date_Format(date_payment,'%Y%m')), if(s.scheme_type = 1 or s.scheme_type=3, COUNT(Distinct Date_Format(date_payment,'%Y%m')), SUM(no_of_dues))) from payment where id_scheme_account=sa.id_scheme_account and payment_status=1 group by id_scheme_account),0)as paid_installments,s.gst_type, s.gst,
    if(p.added_by=3,p.payment_type,p.payment_mode) as payment_mode,
    IFNULL(p.bank_acc_no,'-') as bank_acc_no,
    IFNULL(p.bank_name,'-')as bank_name,
    IFNULL(p.bank_IFSC,'-') as bank_IFSC,
    IFNULL(p.bank_branch,'-') as bank_branch,
    IFNULL(p.id_transaction,'-') as id_transaction,
    IFNULL(p.payu_id,'-') as payu_id ,
    IFNULL(p.card_no,'-') as card_no,
    psm.payment_status as payment_status,
    p.payment_status as id_status,
    psm.color as status_color,chit.gst_setting,
    IFNULL(p.payment_ref_number,'-') as payment_ref_number,
    IFNULL(p.remark,'-') as remark,sa.active as active,sa.is_closed as is_closed,concat(s.scheme_name,'-',s.code) as scheme_name,if(p.added_by=0,'Admin',if(p.added_by=1,'Web App',if(p.added_by=2,'Mobile App','Collection App'))) as payment_through,p.payment_type
    FROM payment p
    join company compy 
    join chit_settings chit 
    left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
    Left Join employee e On (e.id_employee=p.id_employee)
    Left Join branch b On (b.id_branch=p.id_branch)
    Left Join customer c on (sa.id_customer=c.id_customer)
    left join village v on (v.id_village= c.id_village)
    left join scheme s on(sa.id_scheme=s.id_scheme)
    left join sch_classify sch_classify on(s.id_classification=sch_classify.id_classification)
    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)		
    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
    Where (date(p.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') And p.payment_status=1
    " . ($uid != 1 ? ($branchWiseLogin == 1 ? ($branch != '' ? " and (p.id_branch=" . $branch . " or b.show_to_all=1)" : '') : '') : '') . " 
    " . ($id_classfication != '' ? " and s.id_classification=" . $id_classfication . "" : '') . "
    " . ($id_scheme != '' ? " and s.id_scheme=" . $id_scheme . "" : '') . "
    " . ($pay_mode != '' ? " and p.added_by=" . $pay_mode . "" : '') . "
    " . ($id_branch != '' && $id_branch > 0 ? " and p.id_branch=" . $id_branch . "" : '') . "
    " . ($id_company != '' && $company_settings == 1 ? " and s.id_company='" . $id_company . "'" : '') . "
    ORDER BY p.date_payment asc,s.code asc");
        //print_r($this->db->last_query());exit;
        $return_data = $sql->result_array();
        /*foreach($pay_details as $r)
    {
        $return_data[$r['scheme_name']][]=$r;
    }*/
        return $return_data;
    }
    function getSchemeWiseSummaryDetails($from_date, $to_date, $id_branch)
    {
        $return_data = array();
        $sql = $this->db->query("select SUM(p.payment_amount-IFNULL(s.firstPayDisc_value,0)) as received_amt,IFNULL(SUM(s.firstPayDisc_value),0) as discountAmt,p.payment_mode,s.code,
    IFNULL(SUM(p.metal_weight),0) as paid_weight,sch.classification_name,s.scheme_name,s.id_scheme
    FROM payment p
    left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
    Left Join branch b On (b.id_branch=p.id_branch) 
    left join scheme s on(sa.id_scheme=s.id_scheme) 
    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)	
     left join sch_classify sch on(s.id_classification=sch.id_classification)
    Where (date(p.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') And p.payment_status=1
    " . ($id_branch != '' && $id_branch > 0 ? " and p.id_branch=" . $id_branch . "" : '') . "
    GROUP by sa.id_scheme
    order by s.id_scheme ASC");
        $pay_details = $sql->result_array();
        foreach ($pay_details as $pay) {
            $opening_blc_details = $this->getSchemeWiseSummaryOpeningDetails($from_date, $to_date, $id_branch, $pay['id_scheme']);
            $return_data[] = array(
                'classification_name' => $pay['classification_name'],
                'code' => $pay['code'],
                'discountAmt' => $pay['discountAmt'],
                'paid_weight' => $pay['paid_weight'],
                'payment_mode' => $pay['payment_mode'],
                'received_amt' => $pay['received_amt'],
                'scheme_name' => $pay['scheme_name'],
                'balance_amt' => (isset($opening_blc_details['balance_amt']) ? $opening_blc_details['balance_amt'] : 0),
                'balance_weight' => (isset($opening_blc_details['balance_weight']) ? $opening_blc_details['balance_weight'] : 0),
            );
        }
        return $return_data;
    }
    function getSchemeWiseSummaryOpeningDetails($from_date, $to_date, $id_branch, $id_scheme)
    {
        $sql = $this->db->query("SELECT IFNULL(SUM(sa.balance_amount),0) as balance_amt,IFNULL(SUM(sa.balance_weight),0) as balance_weight,s.scheme_name
    FROM scheme_account sa 
    left join scheme s on(sa.id_scheme=s.id_scheme) 
    WHERE sa.is_opening=1
    and (date(sa.start_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
    " . ($id_scheme != '' && $id_scheme > 0 ? " and sa.id_scheme=" . $id_scheme . "" : '') . "
    " . ($id_branch != '' && $id_branch > 0 ? " and sa.id_branch=" . $id_branch . "" : '') . "
    GROUP by sa.id_scheme");
        return $sql->row_array();
    }
    function getModeWiseummaryDetails($from_date, $to_date, $id_branch)
    {
        $return_data = array();
        $sql = $this->db->query("select SUM(p.payment_amount-IFNULL(s.firstPayDisc_value,0)) as received_amt,IFNULL(SUM(s.firstPayDisc_value),0) as discountAmt,p.payment_mode,s.code,
    IFNULL(SUM(p.metal_weight),0) as paid_weight,sch.classification_name,s.scheme_name
    FROM payment p
    join company compy 
    left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
    Left Join branch b On (b.id_branch=p.id_branch) 
    left join scheme s on(sa.id_scheme=s.id_scheme) 
     left join sch_classify sch on(s.id_classification=sch.id_classification)
    Where (date(p.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') And p.payment_status=1
    " . ($id_branch != '' && $id_branch > 0 ? " and p.id_branch=" . $id_branch . "" : '') . "
    GROUP by p.payment_mode");
        $pay_details = $sql->result_array();
        return $pay_details;
    }
    function getPaymentSummary($from_date, $to_date)
    {
        $branch_settings = $this->session->userdata('branch_settings');
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $date_type = $this->input->post('date_type');
        $cus_branch = $this->input->post('cus_branch');
        $data = array();
        $sql = $this->db->query("select SUM(p.payment_amount) as received_amt,SUM(s.firstPayDisc_value) as discountAmt,p.payment_mode,s.code,
    IFNULL(SUM(p.metal_weight),0) as paid_weight,sch.classification_name,s.scheme_name
    FROM payment p
    join company compy 
    left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
    Left Join branch b On (b.id_branch=p.id_branch) 
    left join scheme s on(sa.id_scheme=s.id_scheme) 
    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)	
     left join sch_classify sch on(s.id_classification=sch.id_classification)
    Where (date(p.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') And p.payment_status=1
    " . ($id_branch != '' && $id_branch > 0 ? " and p.id_branch=" . $id_branch . "" : '') . "
    GROUP by sa.id_scheme,p.payment_mode");
        $pay_details = $sql->result_array();
        foreach ($pay_details as $r) {
            $data[$r['scheme_name']][] = $r;
        }
        return $data;
    }
    // paymode_wise_list chked&updtd emp login branchwise  data show//HH
    function get_modewise_list($from_date, $to_date, $type = "", $limit = "", $id = "")
    {
        $result = array();
        $branch_settings = $this->session->userdata('branch_settings');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = '';
        }
        $date_type = $this->input->post('date_type');
        $sql_pay = "SELECT @a:=@a+1 as sno,sum(p.payment_amount) as payment_amount,
			  s.gst_type, s.gst,compy.gst_number,cs.gst_setting,			  
			  IF(s.gst_type=0,(p.payment_amount-(p.payment_amount*(100/(100+s.gst))))/2,((p.payment_amount*(s.gst/100))/2)) as sgst,					
			  IF(s.gst_type=0,(p.payment_amount-(p.payment_amount*(100/(100+s.gst))))/2,((p.payment_amount*(s.gst/100))/2)) as cgst,	
			  p.payment_mode
			  FROM payment p 
			  join chit_settings cs
			  join company compy
			  left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
			  left join scheme s on(sa.id_scheme=s.id_scheme)
				Where (date(" . ($date_type != '' ? ($date_type == 2 ? "p.custom_entry_date" : "p.date_payment") : "p.date_payment") . ") BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
				" . ($id != '' ? "  And s.id_scheme=" . $id : '') . " 
				" . ($type != '' ? "  And p.payment_type=" . $type : '') . " 				
					And p.payment_status=1   " . ($uid != 1 ? ($branch_settings == 1 ? ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : ($branch != '' ? "and (p.id_branch=" . $branch . " or b.show_to_all=2)" : '')) : '') : ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : '')) . "
			     group by p.payment_mode ORDER BY p.date_payment DESC" . ($limit != NULL ? " LIMIT " . $limit . " OFFSET " . $limit : " ");
        $payments_1 = $this->db->query($sql_pay)->result_array();
        $sql_pay_mode = "SELECT @a:=@a+1 as sno,sum(pmd.payment_amount) as payment_amount, s.gst_type, s.gst,compy.gst_number,cs.gst_setting, IF(s.gst_type=0,(pmd.payment_amount-(pmd.payment_amount*(100/(100+s.gst))))/2,((pmd.payment_amount*(s.gst/100))/2)) as sgst, IF(s.gst_type=0,(pmd.payment_amount-(pmd.payment_amount*(100/(100+s.gst))))/2,((pmd.payment_amount*(s.gst/100))/2)) as cgst, if(pmd.payment_mode='FP','Free payment',pm.mode_name)as mode_name,pmd.payment_mode 
                FROM payment p 
                    join chit_settings cs 
                    join company compy 
                    left join payment_mode_details pmd on(pmd.id_payment=p.id_payment) 
                    left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account) 
                    left join scheme s on(sa.id_scheme=s.id_scheme) 
                    Left Join payment_mode pm on (pmd.payment_mode=pm.short_code) 
                    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
                Where (date(" . ($date_type != '' ? ($date_type == 2 ? "p.custom_entry_date" : "p.date_payment") : "p.date_payment") . ") BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
				" . ($id != '' ? "  And s.id_scheme=" . $id : '') . " 
				" . ($type != '' ? "  And p.payment_type=" . $type : '') . " 				
					And p.payment_status=1   " . ($uid != 1 ? ($branch_settings == 1 ? ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : ($branch != '' ? "and (p.id_branch=" . $branch . " or b.show_to_all=2)" : '')) : '') : ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : '')) . "
			     group by pmd.payment_mode ORDER BY p.date_payment DESC" . ($limit != NULL ? " LIMIT " . $limit . " OFFSET " . $limit : " ");
        $payments_2 = $this->db->query($sql_pay_mode)->result_array();
        //echo $sql_pay;exit;
        $modes = $this->db->query("SELECT pm.mode_name,short_code FROM payment_mode pm")->result_array();
        foreach ($modes as $m) {
            /*foreach($payments_1 as $r1){
                if($m['short_code'] == $r1['payment_mode'] ){
                    if(isset($result[$m['short_code']])){
                        $result[$m['short_code']]['payment_amount'] = $result[$m['short_code']]['payment_amount'] + $r1['payment_amount'];
                    }else{
                        $result[$m['short_code']]['payment_amount'] = $r1['payment_amount'];
                        $result[$m['short_code']]['mode_name'] = $m['mode_name'];
                        $result[$m['short_code']]['gst_number'] = $r1['gst_number'];
                    }
                }
            }*/
            foreach ($payments_2 as $r2) {
                if ($m['short_code'] == $r2['payment_mode']) {
                    if (isset($result[$m['short_code']])) {
                        $result[$m['short_code']]['payment_amount'] = $result[$m['short_code']]['payment_amount'] + $r2['payment_amount'];
                    } else {
                        $result[$m['short_code']]['payment_amount'] = $r2['payment_amount'];
                        $result[$m['short_code']]['mode_name'] = $m['mode_name'];
                        $result[$m['short_code']]['gst_number'] = $r2['gst_number'];
                    }
                }
            }
        }
        // 		echo "<pre>";print_r($result);
        return $result;
    }
    // payment_datewise_schemedata chked&updtd emp login branchwise  data show//HH
    function payment_datewise_list($date)
    {
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = '';
        }
        $branch_settings = $this->session->userdata('branch_settings');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $id_employee = $this->input->post('id_employee');
        $date_type = $this->input->post('date_type');
        $added_by = $this->input->post('added_by');
        $sql_1 = "select  s.code,
					sum(p.payment_amount) as payment_amount,s.gst_type, s.gst,IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,					
					COUNT(CASE WHEN  (p.receipt_no is not null  || p.receipt_no is null ) and p.payment_status=1 THEN 1 END) as receipt,
					compy.gst_number,cs.gst_setting,b.name,p.id_transaction,
					if(p.payment_mode='FP','FP',p.payment_mode)as payment_mode,
					if(p.payment_mode='CC' || p.payment_mode='DC','Card',p.payment_mode)as payment_mode,					
					IF(s.gst_type=0,(p.payment_amount-(p.payment_amount*(100/(100+s.gst))))/2,((p.payment_amount*(s.gst/100))/2)) as sgst,					
					IF(s.gst_type=0,(p.payment_amount-(p.payment_amount*(100/(100+s.gst))))/2,((p.payment_amount*(s.gst/100))/2)) as cgst					
					FROM sch_classify sc
					 join company compy
					 join chit_settings cs
					LEFT JOIN scheme s ON (sc.id_classification = s.id_classification)
					  LEFT JOIN scheme_account sa ON (s.id_scheme = sa.id_scheme)
					  LEFT JOIN payment p ON (sa.id_scheme_account = p.id_scheme_account)
					  Left Join branch b On (b.id_branch=p.id_branch)
					  LEFT JOIN postdate_payment pp ON (sa.id_scheme_account = pp.id_scheme_account)
						WHERE sc.active=1 AND (p.payment_status=1 or pp.payment_status=1)
							and date(" . ($date_type != '' ? ($date_type == 2 ? "p.custom_entry_date" : "p.date_payment") : "p.date_payment") . ")='$date' " . ($id_employee != NULL ? ' and p.id_employee =' . $id_employee : '') . " 
							" . ($added_by != '' ? ($added_by == 0 ? " and p.added_by=" . $added_by . "" : " and(p.added_by=1 or p.added_by=2)") : '') . " 
						" . ($uid != 1 ? ($branch_settings == 1 ? ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : ($branch != '' ? "and (p.id_branch=" . $branch . " or b.show_to_all=2)" : '')) : '') : ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : '')) . "
						GROUP BY s.code,p.payment_mode";
        //	print_r($sql_1);exit;
        $payments = $this->db->query($sql_1)->result_array();
        return $payments;
    }
    function payment_datewise_by_mode($date)
    {
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = '';
        }
        $branch_settings = $this->session->userdata('branch_settings');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $id_employee = $this->input->post('id_employee');
        $date_type = $this->input->post('date_type');
        $added_by = $this->input->post('added_by');
        $result = array();
        $sql_pay_mode = "SELECT @a:=@a+1 as sno,show_to_all,sum(pmd.payment_amount) as received_amt, s.gst_type, s.gst,compy.gst_number,cs.gst_setting, IF(s.gst_type=0,(pmd.payment_amount-(pmd.payment_amount*(100/(100+s.gst))))/2,((pmd.payment_amount*(s.gst/100))/2)) as sgst, IF(s.gst_type=0,(pmd.payment_amount-(pmd.payment_amount*(100/(100+s.gst))))/2,((pmd.payment_amount*(s.gst/100))/2)) as cgst, if(pmd.payment_mode='FP','Free payment',pm.mode_name)as mode_name,pmd.payment_mode,p.id_transaction,pm.short_code
                FROM payment p 
                    join chit_settings cs 
                    join company compy 
                    left join branch b on b.id_branch = p.id_branch
                    left join payment_mode_details pmd on(pmd.id_payment=p.id_payment) 
                    left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account) 
                    left join scheme s on(sa.id_scheme=s.id_scheme) 
                    Left Join payment_mode pm on (pmd.payment_mode=pm.short_code) 
                    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
                WHERE  p.payment_status=1  
							and date(" . ($date_type != '' ? ($date_type == 2 ? "p.custom_entry_date" : "p.date_payment") : "p.date_payment") . ")='$date' " . ($id_employee != NULL ? ' and p.id_employee =' . $id_employee : '') . " 
							" . ($added_by != '' ? ($added_by == 0 ? " and p.added_by=" . $added_by . "" : " and(p.added_by=1 or p.added_by=2)") : '') . " 
						" . ($uid != 1 ? ($branch_settings == 1 ? ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : ($branch != '' ? "and (p.id_branch=" . $branch . " or b.show_to_all=2)" : '')) : '') : ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : '')) . "
			     group by pmd.payment_mode ORDER BY p.date_payment DESC" . ($limit != NULL ? " LIMIT " . $limit . " OFFSET " . $limit : " ");
        $payments = $this->db->query($sql_pay_mode)->result_array();
        return $payments;
    }
    //paydatewise_schcoll_data chked &updtd emp login branchwise  data show//HH
    function paydatewise_schemecoll($date)
    {
        $branch_settings = $this->session->userdata('branch_settings');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = '';
        }
        $sql_1 = "select  
				b.name as branch,s.scheme_name,s.gst_type, s.gst,compy.gst_number,
				 COUNT(CASE WHEN  Date_format(p.date_payment,'%Y-%m-%d') ='$date' and p.payment_mode ='FP' and p.payment_status=1 THEN 1 END) as incentive,
				  COUNT(CASE WHEN  Date_format(p.date_payment,'%Y-%m-%d') ='$date' and p.payment_status=4 THEN 1 END) as cancel_payment,
				 COUNT(CASE WHEN  Date_format(p.date_payment,'%Y-%m-%d') ='$date'  and p.payment_status=1 THEN 1 END) as paid,
				 IFNULL(sum(CASE WHEN  Date_format(p.date_payment,'%Y-%m-%d') ='$date' 
				  and p.payment_status=1 THEN p.payment_amount END),0) as collection,				  
				  IFNULL((CASE WHEN  Date_format(p.date_payment,'%Y-%m-%d') ='$date' 
				  and sa.is_closed=1 THEN sa.closing_balance END),0) as closing_balance,				  
				 IFNULL(sum(CASE WHEN  Date_format(p.date_payment,'%Y-%m-%d') ='$date' and p.payment_mode ='FP'
				 and p.payment_status=1 THEN p.payment_amount END) ,0)as incentive_amt ,
				 IFNULL(SUM(CASE WHEN Date_format(p.date_payment,'%Y-%m-%d') ='$date' and p.payment_status=1 THEN p.add_charges ELSE 0 END+CASE WHEN Date_format(p.date_payment,'%Y-%m-%d') ='$date' and pp.payment_status=1 THEN pp.charges ELSE 0 END),0) as charge,cs.gst_setting,
					(select sum(pay.payment_amount) from payment pay
LEFT JOIN scheme_account sca ON (sca.id_scheme_account = pay.id_scheme_account)
LEFT JOIN scheme sh ON (sca.id_scheme = sh.id_scheme)
where s.id_scheme = sh.id_scheme and Date_format(pay.date_payment,'%Y-%m-%d')<= DATE_SUB('$date', INTERVAL 1 DAY)
and pay.payment_status=1 group by s.id_scheme)  as opening_bal
					from payment p
					join company compy
					 join chit_settings cs
					LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
					LEFT JOIN postdate_payment pp ON (p.id_scheme_account = pp.id_scheme_account) and pp.payment_status=1
					LEFT JOIN scheme s ON (sa.id_scheme = s.id_scheme)
					Left Join branch b On (b.id_branch=p.id_branch)
					LEFT JOIN sch_classify sc ON (sc.id_classification = s.id_classification)
					WHERE p.id_scheme_account IS NOT NULL AND Date_format(p.date_payment,'%Y-%m-%d')<='$date' and (p.payment_status=1 or p.payment_status=4)  
					" . ($uid != 1 ? ($branch_settings == 1 ? ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : ($branch != '' ? "and (p.id_branch=" . $branch . " or b.show_to_all=2)" : '')) : '') : ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : '')) . "
					group by s.id_scheme";
        //	print_r($sql_1);exit;
        $payments = $this->db->query($sql_1)->result_array();
        return $payments;
    }
    // payment outstanding chked &updtd emp login branchwise  data show//HH 
    function payment_outlist($date)
    {
        $branch_settings = $this->session->userdata('branch_settings');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = '';
        }
        $sql_1 = "SELECT s.id_scheme,s.code,s.total_installments, IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number, IF(c.lastname is null,c.firstname,concat(c.firstname,'',c.lastname))as name,compy.gst_number,
					count(d.next_due) as due_count,sa.id_branch,
					IFNULL(Date_format(sa.start_date,'%d-%m%-%Y'),'-')  as joined_date,c.mobile,
					IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight ,
					COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or scheme_type=3 ,
					COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
					 as paid_installments,cs.gst_setting,
					 if(payment_type=1,p.metal_weight,s.amount) as amount,
					CASE WHEN sa.is_opening='1' AND p.date_add is null
					THEN Date_add(sa.last_paid_date,Interval 1 month) when p.date_add is null and sa.is_opening='0' then
					sa.date_add ELSE Date_add(max(p.date_add),Interval 1 month) END AS next_due,
					IFNULL(IF(sa.is_opening=1,IFNULL(balance_amount,0)+
					IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0)as total_paid_amount,
					IFNULL(IF(sa.is_opening=1,IFNULL(balance_weight,0)+IFNULL(SUM(p.metal_weight),0),IFNULL(SUM(p.metal_weight),0)),0.000)as total_paid_weight,
					IFNULL(Date_Format(max(p.date_add),'%d-%m-%Y'),IFNULL(IF(sa.is_opening=1,Date_Format(sa.last_paid_date,'%d-%m-%Y'),'')  ,0))as last_paid_date
					FROM scheme s
					join company compy
					join chit_settings cs					
					left join scheme_account sa on(s.id_scheme=sa.id_scheme)
					left join customer c on(sa.id_customer=c.id_customer)
					left join payment p on (sa.id_scheme_account=p.id_scheme_account)
					Left Join (Select sa.id_scheme_account, CASE WHEN sa.is_opening='1' AND p.date_payment is null
					THEN Date_add(sa.last_paid_date,Interval 1 month) when p.date_payment is null and sa.is_opening='0' then
					sa.date_add ELSE Date_add(max(p.date_payment),Interval 1 month) END AS next_due
					From scheme_account sa
					Left Join branch b On (b.id_branch=sa.id_branch)
					Left Join payment p on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and
					sa.is_closed=0 and p.payment_status='1') Group By sa.id_scheme_account)d on(d.id_scheme_account=sa.id_scheme_account)
					where Date_format(d.next_due,'%Y-%m-%d')='$date'
						" . ($uid != 1 ? ($branch_settings == 1 ? ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : ($branch != '' ? "and (p.id_branch=" . $branch . ")" : '')) : '') : ($id_branch != 0 && $id_branch != '' ? "and p.id_branch=" . $id_branch . "" : '')) . "
					Group By sa.id_scheme_account";
        $payments = $this->db->query($sql_1)->result_array();
        return $payments;
    }
    //end of new reports	
    public function get_gstSplitupData($id, $date_add)
    {
        //NOTE : type with NULL value is GST
        $sql = "SELECT splitup_name,percentage,type FROM gst_splitup_detail WHERE status=1 and type is not null and `id_scheme` =" . $id;
        $data = $this->db->query($sql);
        return $data->result_array();
    }
    public function get_schgst($id)
    {
        $sql = "SELECT s.is_topup_scheme,s.installment_cycle,s.interest,s.is_digi,s.gst,s.gst_type,s.amount,s.max_weight,s.scheme_type,s.one_time_premium,s.rate_fix_by,s.rate_select,s.id_metal from scheme s
				 LEFT JOIN scheme_account sa on sa.id_scheme = s.id_scheme		
				where sa.id_scheme_account =" . $id;
        return $this->db->query($sql)->row_array();
    }
    function avg_applicable($id, $data)
    {
        $this->db->where('id_scheme_account', $id);
        $status = $this->db->update(self::ACC_TABLE, $data);
        return $status;
    }
    // function get_scheme_list()
    // {
    //     $branch_settings=$this->session->userdata('branch_settings');
    //     $is_branchwise_cus_reg=$this->session->userdata('is_branchwise_cus_reg');
    //     $branch=$this->session->userdata('id_branch');
    //     $uid=$this->session->userdata('uid');
    //     $branchwise_scheme=$this->session->userdata('branchwise_scheme');
    // 	$sql="SELECT s.id_scheme, s.scheme_name, s.code 
    // 	FROM scheme s 
    //     ".($uid!=1 ? ($branch_settings==1 && $branchwise_scheme == 1 ? 'LEFT JOIN scheme_branch sb on (sb.id_scheme = s.id_scheme) LEFT JOIN branch b on (b.id_branch = sb.id_branch)' :'') : '')."
    // 	".($uid!=1 ? ($branch_settings==1 && $branchwise_scheme == 1 && $branch!=0 && $branch!='' ? "where  sb.id_branch=".$branch."" :'') : '')."
    // 	";
    // 	return $this->db->query($sql)->result_array();
    // }
    function get_scheme_list()
    {
        $branch_settings = $this->session->userdata('branch_settings');
        $is_branchwise_cus_reg = $this->session->userdata('is_branchwise_cus_reg');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $branchwise_scheme = $this->session->userdata('branchwise_scheme');
        /*$sql="SELECT s.id_scheme, s.scheme_name, s.code 
        FROM scheme s 
        ".($uid!=1 ? ($branch_settings==1 && $branchwise_scheme == 1 ? 'LEFT JOIN scheme_branch sb on (sb.id_scheme = s.id_scheme) LEFT JOIN branch b on (b.id_branch = sb.id_branch)' :'') : '')."
        where s.active=1
        ".($uid!=1 ? ($branch_settings==1 && $branchwise_scheme == 1 && $branch!=0 && $branch!='' ? "and sb.id_branch=".$branch."" :'') : '')."
        ";*/
        $sql = "SELECT s.id_scheme, s.scheme_name, s.code,s.has_gift,s.active 
		FROM scheme s 
	    " . ($uid != 1 ? ($branch_settings == 1 && $branchwise_scheme == 1 ? 'LEFT JOIN scheme_branch sb on (sb.id_scheme = s.id_scheme) LEFT JOIN branch b on (b.id_branch = sb.id_branch)' : '') : '') . "
		" . ($uid != 1 ? ($branch_settings == 1 && $branchwise_scheme == 1 && $branch != 0 && $branch != '' ? " where sb.id_branch=" . $branch . "" : '') : '') . "
		";
        $scheme_list = $this->db->query($sql)->result_array();
        // print_r($this->db->last_query());exit;
        return $scheme_list;
    }
    //referral report starts here
    //emp reff_report
    function get_empreff_report()
    {
        $sql = "select e.id_employee,e.emp_code  as emp_code,e.mobile,
		concat(e.firstname,'',e.lastname)as name,
		count(fp.ref_code)as refferal_count,fp.is_refferal_by,fp.id_scheme,
		sum(fp.referal_value) as benifits from employee e
		LEFT JOIN (select s.id_scheme,FORMAT(if(chit.empplan_type=1,if(ws.type=0,ws.value,((s.amount*ws.value)/100)),
		if(s.emp_refferal=1 && chit.empplan_type=0,s.emp_refferal_value,'')),0) as referal_value,sa.referal_code as ref_code,p.id_scheme_account,sa.is_refferal_by,
		count(id_payment) from payment p 
		left join scheme_account sa on sa.id_scheme_account =p.id_scheme_account
		left join scheme s on sa.id_scheme =s.id_scheme 
		join wallet_settings ws
		join chit_settings chit
		where payment_status =1 and sa.is_refferal_by=1 and s.emp_refferal=1 and sa.is_closed=0 and ws.active=1 and ws.id_wallet=2
		group by sa.id_scheme_account) fp on fp.ref_code=e.emp_code	
		where fp.is_refferal_by=1    group by id_employee";
        $payments = $this->db->query($sql);
        //print_r($this->db->last_query());exit;
        return $payments->result_array();
    }
    function old_empreferral_account($id)
    {
        $sql = "select cp.receipt_no,wt.credit_for,if(wt.transaction_type=0,'Credit','Debit') as issue_type,wt.value as benefit,s.code,p.payment_amount,e.emp_code, IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,if(sa.scheme_acc_number is null ,s.scheme_name,concat(s.scheme_name,'-',sa.scheme_acc_number))as scheme_acc_number,
	  c.id_customer,if(c.lastname is null ,c.firstname,concat(c.firstname,'',c.lastname))as cus_name from customer c
		left join scheme_account sa on(sa.id_customer=c.id_customer)
		left join wallet_transaction wt on wt.id_sch_ac = sa.id_scheme_account
		left join payment p on (p.id_scheme_account=sa.id_scheme_account)
		left join scheme s on (s.id_scheme=sa.id_scheme)
		left join (
					        SELECT pay.id_payment,if(pay.receipt_no is null,'',concat(IFNULL(concat(pay.receipt_year,'-'),''),pay.receipt_no)) as receipt_no from wallet_transaction wt  
					            LEFT JOIN payment pay on pay.id_payment = wt.id_payment
					) cp on cp.id_payment = wt.id_payment
		left join employee e on(e.emp_code=sa.referal_code) where p.due_type='ND' and e.emp_code='$id' and payment_status=1 and sa.is_refferal_by=1 GROUP BY wt.id_payment";
        //	echo $sql;exit;
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    function empreferral_account($id)
    {
        $sql = "select cp.receipt_no,wt.credit_for,if(wt.transaction_type=0,'Credit','Debit') as issue_type,wt.value as benefit,s.code,p.payment_amount,e.emp_code, IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,if(sa.scheme_acc_number is null ,s.scheme_name,concat(s.scheme_name,'-',sa.scheme_acc_number))as scheme_acc_number,
	  c.id_customer,if(c.lastname is null ,c.firstname,concat(c.firstname,'',c.lastname))as cus_name from wallet_transaction wt
		left join scheme_account sa on(sa.id_scheme_account=wt.id_sch_ac)
		left join payment p on p.id_payment = wt.id_payment
		left join customer c on (c.id_customer=sa.id_customer)
		left join scheme s on (s.id_scheme=sa.id_scheme)
		left join (
					        SELECT pay.id_payment,if(pay.receipt_no is null,'',concat(IFNULL(concat(pay.receipt_year,'-'),''),pay.receipt_no)) as receipt_no from wallet_transaction wt  
					            LEFT JOIN payment pay on pay.id_payment = wt.id_payment
					) cp on cp.id_payment = wt.id_payment
		left join employee e on(e.id_employee=wt.id_employee) where e.emp_code='$id' and payment_status=1 GROUP BY wt.id_wallet_transaction";
        //echo $sql;exit;
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    function empreferral_account_by_range($from_date, $to_date, $id)
    {
        $acc_type = $this->input->post('acc_type');
        //print_r($acc_type);exit;
        /*	$sql = "select cp.receipt_no,cp.receipt_year,cp.id_payment,wt.credit_for,if(wt.transaction_type=0,'Credit','Debit') as issue_type,wt.value as benefit,s.code,p.payment_amount,e.emp_code, IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,if(sa.scheme_acc_number is null ,s.scheme_name,concat(s.scheme_name,'-',sa.scheme_acc_number))as oldscheme_acc_number,
            c.id_customer,if(c.lastname is null ,c.firstname,concat(c.firstname,'',c.lastname))as cus_name,
            IFNULL(sa.scheme_acc_number,'NOT ALLOCATED') as scheme_acc_number,
            cs.scheme_wise_acc_no,cs.scheme_wise_receipt,
            (select IFNULL(br.short_name,'-') as acc_branch from branch br where br.id_branch = sa.id_branch) as acc_branch,
             IFNULL(sa.start_year,'') as start_year,
            if(wt.transaction_type=0,wt.value,0) as credit_amount,
            if(wt.transaction_type=1,wt.value,0) as debit_amount,
            IFNULL(Date_format(wt.date_transaction,'%d-%m%-%Y'),'-') as date_transaction,
            sa.id_scheme_account
            from customer c
              left join scheme_account sa on(sa.id_customer=c.id_customer)
              left join wallet_transaction wt on wt.id_sch_ac = sa.id_scheme_account
              left join payment p on (p.id_scheme_account=sa.id_scheme_account)
              left join scheme s on (s.id_scheme=sa.id_scheme)
              join chit_settings cs
              left join (
                                  SELECT pay.id_payment,if(pay.receipt_no is null,'',concat(IFNULL(concat(pay.receipt_year,'-'),''),pay.receipt_no)) as oldreceipt_no,
                                  IFNULL(pay.receipt_no,'') as receipt_no,
                                  IFNULL(pay.receipt_year,'')as receipt_year
                                  from wallet_transaction wt  
                                      LEFT JOIN payment pay on pay.id_payment = wt.id_payment
                          ) cp on cp.id_payment = wt.id_payment
              left join employee e on(e.emp_code=sa.referal_code) where p.due_type='ND'
               and e.emp_code='$id' and payment_status=1 
               And (date(wt.date_transaction) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')"; */
        $sql = "select cp.receipt_no, IF(sa.is_new='Y','New Customer','Existing Customer') as is_new,  p.receipt_year,cp.id_payment,wt.credit_for,if(wt.transaction_type=0,'Credit','Debit') as issue_type,
        wt.value as credit_amount,s.code,p.payment_amount as old_paymentamt, e.emp_code, IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,if(sa.scheme_acc_number is null ,s.scheme_name,concat(s.scheme_name,'-',sa.scheme_acc_number))as oldscheme_acc_number,
        c.id_customer,if(c.lastname is null ,c.firstname,concat(c.firstname,'',c.lastname))as cus_name,
        IFNULL(sa.scheme_acc_number,'NOT ALLOCATED') as scheme_acc_number,
        cs.scheme_wise_acc_no,cs.scheme_wise_receipt,
        (select IFNULL(br.short_name,'-') as acc_branch from branch br where br.id_branch = sa.id_branch) as acc_branch,
         IFNULL(sa.start_year,'') as start_year,
        if(wt.transaction_type=0,wt.value,0) as credit_amount,
        if(wt.transaction_type=1,wt.value,0) as debit_amount,
        IFNULL(Date_format(wt.date_transaction,'%d-%m%-%Y'),'-') as date_transaction,
        sa.id_scheme_account,
        IFNULL((select sum(pay.payment_amount) from payment pay left join scheme_account ssa on ssa.id_scheme_account=pay.id_scheme_account left join wallet_transaction wta on ssa.id_scheme_account=wta.id_sch_ac where ssa.id_scheme_account=wt.id_sch_ac and payment_status=1 ),'') as payment_amount
        from customer c
          left join scheme_account sa on(sa.id_customer=c.id_customer)
          left join employee e on(e.emp_code=sa.referal_code) 
          left join wallet_transaction wt on wt.id_sch_ac = sa.id_scheme_account
          left join payment p on (p.id_scheme_account=sa.id_scheme_account)
          left join scheme s on (s.id_scheme=sa.id_scheme)
          join chit_settings cs
          left join (
                              SELECT pay.id_payment,if(pay.receipt_no is null,'',concat(IFNULL(concat(pay.receipt_year,'-'),''),pay.receipt_no)) as oldreceipt_no,
                              IFNULL(pay.receipt_no,'') as receipt_no,
                              IFNULL(pay.receipt_year,'')as receipt_year
                              from wallet_transaction wt  
                                  LEFT JOIN payment pay on pay.id_payment = wt.id_payment
                      ) cp on cp.id_payment = wt.id_payment
          where sa.referal_code='$id' and p.payment_status=1 and sa.is_refferal_by=1 and wt.type= 0 and wt.transaction_type=0";
        if ($from_date != '' && $to_date != '') {
            $sql = $sql . " And (date(wt.date_transaction) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')";
        }
        if ($acc_type == '0') {
            $sql = $sql . " And wt.transaction_type=0 ";
        } else if ($acc_type == '1') {
            $sql = $sql . " And wt.transaction_type=1 ";
        }
        $sql = $sql . " GROUP BY wt.id_wallet_transaction";
        //echo $sql;exit;
        $payments = $this->db->query($sql);
        $result = [];
        if ($payments->num_rows() > 0) {
            $sno = 1;
            foreach ($payments->result_array() as $rcpt) {
                $rcpt['sno'] = $sno;
                //$rcpt['scheme_acc_number'] = $this->account_model->getScheme_Acc_number($rcpt['acc_branch'],$rcpt['code'],$rcpt['scheme_acc_number'],$rcpt['start_year'],$rcpt['scheme_wise_acc_no']);
                //$rcpt['receipt_no'] = $this->account_model->getReceipt_number($rcpt['acc_branch'],$rcpt['code'],$rcpt['receipt_no'],$rcpt['receipt_year'],$rcpt['scheme_wise_receipt']);
                $rcpt['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $rcpt['id_scheme_account']);
                $rcpt['receipt_no'] = $this->customer_model->format_accRcptNo('Receipt', $rcpt['id_payment']);
                $return_data[] = $rcpt;
                $result = $return_data;
                $sno++;
            }
        }
        //return $payments->result_array();
        return $result;
    }
    function get_empreff_report_by_range($from_date, $to_date, $id_branch)
    {
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = '';
        }
        $sql = "SELECT e.id_employee,if(e.lastname is not null ,concat(e.firstname,' ',e.lastname),e.firstname)as name, e.emp_code,e.mobile,sa.id_scheme_account,
		        s.code,
        		IFNULL(sa.scheme_acc_number,'NOT ALLOCATED') as scheme_acc_number,
        		cs.scheme_wise_acc_no,
        		(select IFNULL(br.short_name,'-') as acc_branch from branch br where br.id_branch = sa.id_branch) as acc_branch,
        		 IFNULL(sa.start_year,'') as start_year,
                sum(case when wt.transaction_type=0 then wt.value end) as benifits, 
                sum(case when wt.transaction_type=0 then wt.value end) as credit_benifits, 
                sum(case when wt.transaction_type=1 then wt.value end) as debit_benifits, 
                count(case when wt.transaction_type=0 then wt.value end) as refferal_count,e.id_branch,
				if(wt.transaction_type=0,'Credit','Debit') as issue_type,SUM(p.payment_amount) as total_amount 
                FROM wallet_account wa 
                left join employee e on (wa.idemployee=e.id_employee) 
                left join wallet_transaction wt on(wa.id_wallet_account=wt.id_wallet_account) 
                left join scheme_account sa on sa.id_scheme_account=wt.id_sch_ac 
                left join payment p on p.id_scheme_account = sa.id_scheme_account 
                left join scheme  s on s.id_scheme =sa.id_scheme
                join chit_settings cs
                where  wa.active=1 and wa.idemployee is not null" . ($id_branch != 0 && $id_branch != '' ? ' and e.id_branch =' . $id_branch : '') . "
                " . ($from_date != '' ? " And (date(wt.date_transaction) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')" : '') . " and p.id_payment = wt.id_payment  group by e.id_employee";
        $payments = $this->db->query($sql);
        //print_r($this->db->last_query());exit; 
        $result = [];
        if ($payments->num_rows() > 0) {
            $sno = 1;
            foreach ($payments->result_array() as $rcpt) {
                $rcpt['sno'] = $sno;
                // $rcpt['scheme_acc_number'] = $this->account_model->getScheme_Acc_number($rcpt['acc_branch'],$rcpt['code'],$rcpt['scheme_acc_number'],$rcpt['start_year'],$rcpt['scheme_wise_acc_no']);
                $rcpt['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $rcpt['id_scheme_account']);
                $return_data[] = $rcpt;
                $result = $return_data;
                $sno++;
            }
        }
        // return $payments->result_array();  
        return $result;
    }
    //referral report ends here
    function oldget_cus_ref_success()
    {
        $sql = "select c.id_customer,c.mobile as cus_referalcode,if(c.lastname is null,c.firstname,concat(c.firstname,'',c.lastname)) as name, count(fp.ref_code)as refferal_count,fp.is_refferal_by,fp.id_scheme, sum(fp.referal_value) as benifits,c.mobile from customer c LEFT JOIN (select s.id_scheme,FORMAT(if(chit.cusplan_type=1,if(ws.type=0,ws.value,((s.amount*ws.value)/100)),
		if(s.cus_refferal=1 && chit.cusplan_type=0,s.cus_refferal_value,'')),0) as referal_value,sa.referal_code as ref_code,p.id_scheme_account,sa.is_refferal_by, count(id_payment) 
			from payment p
			join wallet_settings ws
			join chit_settings chit
		    left join scheme_account sa on sa.id_scheme_account =p.id_scheme_account 
		    left join scheme s on sa.id_scheme =s.id_scheme 
			where payment_status =1 and sa.is_refferal_by=0  and ws.id_wallet=1 group by sa.id_scheme_account) fp on fp.ref_code=c.mobile 			
			where fp.is_refferal_by=0  group by c.id_customer";
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    function cus_refferl_account($mobile)
    {
        $sql = "select IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,s.scheme_name as scheme_name,sa.id_customer,
			sa.referal_code,if(c.lastname is null ,c.firstname,concat(c.firstname,'',c.lastname))as name,c.firstname
			from scheme_account sa
			left join payment p on (sa.id_scheme_account=p.id_scheme_account)
			left join scheme s on (s.id_scheme=sa.id_scheme)
			left join customer c on(c.id_customer=sa.id_customer and  c.id_customer=sa.id_customer)
			where sa.referal_code=" . $mobile . " and p.payment_status=1 and sa.is_refferal_by=0 and s.emp_refferal=1  group by sa.id_scheme_account";
        //print_r($sql);exit;
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    /* function cus_refferl_account($id)	
    {
      $sql="select IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,s.scheme_name as scheme_name,sa.id_customer,
            sa.referal_code,if(c.lastname is null ,c.firstname,concat(c.firstname,'',c.lastname))as name,c.firstname
            from scheme_account sa
            left join payment p on (sa.id_scheme_account=p.id_scheme_account)
            left join scheme s on (s.id_scheme=sa.id_scheme)
            left join customer c on(c.id_customer=sa.id_customer and  c.id_customer=sa.id_customer)
            where sa.referal_code=".$id." and p.payment_status=1 and sa.is_refferal_by=0 and s.cus_refferal=1  group by sa.id_scheme_account"; 
            //print_r($sql);exit;
      $payments=$this->db->query($sql); 
      return $payments->result_array();
    } */
    function oldget_cusreff_report_by_range($from_date, $to_date)
    {
        $sql = " select c.id_customer,IFNULL(c.mobile,'') as cus_referalcode,
		if(c.lastname is null ,c.firstname,concat(c.firstname,'',c.lastname))as name,
		count(fp.ref_code)as refferal_count,fp.is_refferal_by,fp.id_scheme,
		sum(fp.referal_value) as benifits from customer c
		LEFT JOIN (select s.id_scheme,FORMAT(if(chit.cusplan_type=1,if(ws.type=0,ws.value,((s.amount*ws.value)/100)),
		if(s.emp_refferal=1 && chit.cusplan_type=0,s.emp_refferal_value,'')),0) as referal_value,sa.referal_code as ref_code,p.id_scheme_account,sa.is_refferal_by,
		count(id_payment) from payment p 
		join chit_settings chit
		join wallet_settings ws
		left join scheme_account sa on sa.id_scheme_account =p.id_scheme_account
		left join scheme s on sa.id_scheme =s.id_scheme 
		where payment_status =1 and sa.is_refferal_by=0 and cus_refferal=1 and sa.is_closed=0 and
		 ( date(sa.start_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
		group by sa.id_scheme_account) fp on fp.ref_code=c.mobile		
		where fp.is_refferal_by=0 group by id_customer";
        //print_r($sql);exit;
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    function get_gstsettings()
    {
        $sql = "Select c.gst_setting FROM chit_settings c where c.id_chit_settings = 1";
        return $this->db->query($sql)->row()->gst_setting;
    }
    function get_rptnosettings()
    {
        $sql = "Select c.receipt_no_set FROM chit_settings c where c.id_chit_settings = 1";
        return $this->db->query($sql)->row()->receipt_no_set;
    }
    /* function get_referrals_datas($id_scheme_account){
         $sql=("SELECT sa.id_scheme_account, sa.id_scheme,
                    s.code,if(cus.lastname is null,cus.firstname,concat(cus.firstname,'',cus.lastname)) as cusname, s.scheme_name,if(s.cus_refferal=1,if(ws.type=0,ws.value,((s.amount*ws.value)/100)),'') as referal_value,
                    sa.is_refferal_by,sa.referal_code,s.cus_refferal,
                    s.emp_refferal,s.emp_refferal_value,s.cus_refferal_value,ref.name,
                    ref.mobile,ref.id_customer as id_customer,ref.id_wallet_account					
                    FROM scheme_account sa
                    left join scheme s on (sa.id_scheme =s.id_scheme)
                    left join(SELECT w.id_customer,w.id_wallet_account,if(c.lastname is null,c.firstname,concat(c.firstname,'',c.lastname)) as name,
                    c.mobile, c.referal_code
                    FROM customer c
                    left join wallet_account w on (c.id_customer=w.id_customer and w.active=1)
                    ) ref on ref.mobile= sa.referal_code					
                    join wallet_settings ws
                    left join customer cus on (cus.id_customer=sa.id_customer)
                    where sa.id_scheme_account=".$id_scheme_account." and ws.active=1 and ws.id_wallet=1");	   
        $result= $this->db->query($sql)->row_array();
        return $result;
     }  */
    function get_referrals_datas($id_scheme_account)
    {
        $sql = ("SELECT sa.id_scheme_account, sa.id_scheme,
					s.code,if(cus.lastname is null,cus.firstname,concat(cus.firstname,'',cus.lastname)) as cusname, s.scheme_name,if(s.cus_refferal=1,s.cus_refferal_value,'') as referal_value,
					sa.is_refferal_by,sa.referal_code,s.cus_refferal,
					s.emp_refferal,s.emp_refferal_value,s.cus_refferal_value,ref.name,
					ref.mobile,ref.id_customer as id_customer,ref.id_wallet_account					
					FROM scheme_account sa
					left join scheme s on (sa.id_scheme =s.id_scheme)
					left join(SELECT w.id_customer,w.id_wallet_account,if(c.lastname is null,c.firstname,concat(c.firstname,'',c.lastname)) as name,
					c.mobile, c.referal_code
					FROM customer c
					left join wallet_account w on (c.id_customer=w.id_customer and w.active=1)
					) ref on ref.mobile= sa.referal_code					
					join wallet_settings ws
					left join customer cus on (cus.id_customer=sa.id_customer)
					where sa.id_scheme_account=" . $id_scheme_account . " ");
        $result = $this->db->query($sql)->row_array();
        return $result;
    }
    function get_refdata($id_scheme_account)
    {
        $sql = ("SELECT sa.referal_code,sa.id_scheme_account,s.ref_benifitadd_ins,
					IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight ,
					COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0),
					if(s.scheme_type = 1 and s.min_weight != s.max_weight or s.scheme_type=3 , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
					as paid_installments,s.ref_benifitadd_ins_type
					     FROM scheme_account sa
					    left join scheme s on (sa.id_scheme=s.id_scheme)
					    left join payment p on (sa.id_scheme_account=p.id_scheme_account) 
						where sa.id_scheme_account=" . $id_scheme_account . " and  p.payment_status=1 group by sa.id_scheme_account");
        $result = $this->db->query($sql)->row_array();
        return $result;
    }
    function get_schemeacountID($id_payment)
    {
        $sql = "Select p.id_scheme_account FROM payment p where p.id_payment=" . $id_payment . "";
        return $this->db->query($sql)->row()->id_scheme_account;
    }
    function get_referral_code($id_scheme_account)
    {
        $sql = "SELECT s.is_refferal_by, s.referal_code,s.id_customer FROM scheme_account s where s.id_scheme_account=" . $id_scheme_account . "";
        return $this->db->query($sql)->row_array();
    }
    public function get_settings()
    {
        $sql = "select * from chit_settings";
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    function get_empreferrals_datas($id_scheme_account)
    {
        $data = $this->get_settings();
        $sql = ("SELECT sa.id_scheme_account, sa.id_scheme,
				s.code,if(cus.lastname is null,cus.firstname,concat(cus.firstname,'',cus.lastname)) as cusname, s.scheme_name,
				FORMAT(if(chit.empplan_type=1,if(ws.type=0,ws.value,((s.amount*ws.value)/100)),
				if(s.emp_refferal=1 && chit.empplan_type=0,s.emp_refferal_value,'')),0) as referal_value,
				sa.is_refferal_by,sa.referal_code,chit.empplan_type,chit.cusbenefitscrt_type,
				chit.empbenefitscrt_type,chit.schrefbenifit_secadd,
				s.emp_refferal,s.emp_refferal_value,ref.name,
				ref.mobile,ref.emp_code,ref.idemployee as idemployee,ref.id_wallet_account					
				FROM scheme_account sa
				left join scheme s on (sa.id_scheme =s.id_scheme)
				left join(SELECT w.id_employee,w.id_wallet_account,if(emp.lastname is null,emp.firstname,concat(emp.firstname,'',emp.lastname)) as name,w.idemployee,
				emp.mobile,emp.emp_code
				FROM employee emp
				left join wallet_account w on (emp.id_employee=w.idemployee and w.active=1)
				) ref on " . ($data['emp_ref_by'] == 1 ? " ref.mobile= sa.referal_code" : "ref.emp_code= sa.referal_code") . " 					
				join wallet_settings ws
				join chit_settings chit
				left join customer cus on (cus.id_customer=sa.id_customer)
				where sa.id_scheme_account=" . $id_scheme_account . "");
        //	print_r($sql);exit;
        $result = $this->db->query($sql)->row_array();
        return $result;
    }
    function get_cusreferrals_datas($id_scheme_account)
    {
        $sql = ("SELECT sa.id_scheme_account, sa.id_scheme,
					s.code,if(cus.lastname is null,cus.firstname,concat(cus.firstname,'',cus.lastname)) as cusname, s.scheme_name,FORMAT(if(chit.cusplan_type=1,if(ws.type=0,ws.value,((s.amount*ws.value)/100)),
				    if(s.cus_refferal=1 && chit.cusplan_type=0,s.cus_refferal_value,'')),0) as referal_value,
					sa.is_refferal_by,sa.referal_code,chit.cusplan_type,chit.schrefbenifit_secadd,
		            chit.empbenefitscrt_type,chit.cusbenefitscrt_type,
					s.cus_refferal,s.cus_refferal_value,ref.name,
					ref.mobile,ref.id_customer as id_customer,ref.id_wallet_account					
					FROM scheme_account sa
					left join scheme s on (sa.id_scheme =s.id_scheme)
					left join(SELECT w.id_customer,w.id_wallet_account,if(c.lastname is null,c.firstname,concat(c.firstname,'',c.lastname)) as name,
					c.mobile, c.cus_ref_code
					FROM customer c
					left join wallet_account w on (c.id_customer=w.id_customer and w.active=1)
					) ref on ref.mobile= sa.referal_code					
					join wallet_settings ws
					join chit_settings chit
					left join customer cus on (cus.id_customer=sa.id_customer)
					where sa.id_scheme_account=" . $id_scheme_account . " and ws.active=1 and ws.id_wallet=1");
        $result = $this->db->query($sql)->row_array();
        return $result;
    }
    function get_ischkrefamtadd($id_scheme_account)
    {
        $record = $this->db->query("SELECT sa.id_scheme,sa.id_customer,sa.is_refferal_by,chit.schrefbenifit_secadd,
		 chit.empbenefitscrt_type,chit.cusbenefitscrt_type,sa.referal_code 
		 FROM scheme_account sa
 		 join chit_settings chit
		 where sa.id_scheme_account=" . $id_scheme_account . "");
        if ($record->num_rows() > 0) {
            if ($record->row()->is_refferal_by != null) {
                if (
                    $record->row()->is_refferal_by == 0 && $record->row()->cusbenefitscrt_type == 0 && ($record->row()->schrefbenifit_secadd == 0 ||
                        $record->row()->schrefbenifit_secadd == 1)
                ) {
                    $status = $this->get_chkrefschcus_joincount($record->row()->id_customer);
                    if ($status) {
                        return true;
                    }
                } else if (
                    $record->row()->is_refferal_by == 0 && $record->row()->cusbenefitscrt_type == 1 &&
                    $record->row()->schrefbenifit_secadd == 1
                ) {
                    $status = $this->get_chkrefschrefcode_joincount($record->row()->id_customer, $record->row()->id_scheme, $record->row()->referal_code);
                    if ($status) {
                        return true;
                    }
                } else if (
                    $record->row()->is_refferal_by == 0 && $record->row()->cusbenefitscrt_type == 1 &&
                    $record->row()->schrefbenifit_secadd == 0
                ) {
                    return true;
                } else if (
                    $record->row()->is_refferal_by == 1 && $record->row()->empbenefitscrt_type == 0 && ($record->row()->schrefbenifit_secadd == 0 ||
                        $record->row()->schrefbenifit_secadd == 1)
                ) {
                    $status = $this->get_chkrefschcus_joincount($record->row()->id_customer);
                    if ($status) {
                        return true;
                    }
                } else if (
                    $record->row()->is_refferal_by == 1 && $record->row()->empbenefitscrt_type == 1 &&
                    $record->row()->schrefbenifit_secadd == 1
                ) {
                    $status = $this->get_chkrefschrefcode_joincount($record->row()->id_customer, $record->row()->id_scheme, $record->row()->referal_code);
                    if ($status) {
                        return true;
                    }
                } else if (
                    $record->row()->is_refferal_by == 1 && $record->row()->empbenefitscrt_type == 1 &&
                    $record->row()->schrefbenifit_secadd == 0
                ) {
                    return true;
                }
            }
            return false;
        }
    }
    function get_chkrefschcus_joincount($id_customer)
    {
        $sql = "SELECT sa.id_scheme_account,count(p.id_payment)as payment,sa.id_scheme,
	         sa.is_refferal_by,c.is_refbenefit_crt_cus,c.is_refbenefit_crt_emp
	        FROM  scheme_account sa			
				left join scheme s on(s.id_scheme=sa.id_scheme)
				left join customer c on(sa.id_customer=c.id_customer)
				Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and p.payment_status=1)
			where sa.id_customer=" . $id_customer . "";
        $records = $this->db->query($sql);
        if ($records->num_rows() > 0) {
            if ($records->row()->is_refferal_by == 0 && $records->row()->payment >= 0 && $records->row()->is_refbenefit_crt_cus == 1) {
                return true;
            } else if ($records->row()->is_refferal_by == 1 && $records->row()->payment >= 0 && $records->row()->is_refbenefit_crt_emp == 1) {
                return true;
            }
        }
        return false;
    }
    function get_chkrefschrefcode_joincount($id_customer, $id_scheme, $referalcode)
    {
        $sql = "SELECT count(sa.id_scheme_account)as scheme_account,sa.id_scheme,sa.id_customer,
	         sa.is_refferal_by,c.is_refbenefit_crt
	        FROM  scheme_account sa			
				left join scheme s on(s.id_scheme=sa.id_scheme)
				left join customer c on(sa.id_customer=c.id_customer)
				Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and p.payment_status=1)
			where sa.id_customer=" . $id_customer . " and sa.id_scheme=" . $id_scheme . " and sa.referal_code=" . $referalcode . "";
        //	echo $sql;
        $records = $this->db->query($sql);
        if ($records->num_rows() > 0) {
            $record = $records->row();
            if ($record->is_refferal_by == 0 && $record->scheme_account <= 1) {
                return true;
            }
            if ($record->is_refferal_by == 1 && $record->scheme_account <= 1) {
                return true;
            }
            return false;
        }
    }
    function get_empreport($id_employee = "")
    {
        $records = array();
        $sql = "select e.id_employee,e.mobile,e.emp_code,
      concat(e.firstname,' ',e.lastname)as name,
      count(fp.ref_code)as refferal_count,fp.is_refferal_by,fp.id_scheme,fp.code,fp.payment_amount,
      sum(fp.referal_value) as benifits from employee e
      LEFT JOIN (select  s.code,p.payment_amount,s.id_scheme,FORMAT(if(chit.empplan_type=1,if(ws.type=0,ws.value,((s.amount*ws.value)/100)),
      if(s.emp_refferal=1 && chit.empplan_type=0,s.emp_refferal_value,'')),0) as referal_value,sa.referal_code as ref_code,p.id_scheme_account,sa.is_refferal_by,
      count(id_payment) from payment p 
      left join scheme_account sa on sa.id_scheme_account =p.id_scheme_account
      left join scheme s on sa.id_scheme =s.id_scheme
      join wallet_settings ws
      join chit_settings chit	
      where payment_status =1 and sa.is_refferal_by=1 and s.emp_refferal=1 and sa.is_closed=0
		group by sa.id_scheme_account) fp on fp.ref_code=e.emp_code where fp.is_refferal_by=1 " . ($id_employee != '' ? "and e.id_employee=" . $id_employee : "") . "
		group by id_employee";
        //print_r($sql);exit;
        $payments = $this->db->query($sql);
        if ($payments->num_rows() > 0) {
            $record = $payments->row();
            $records = array(
                'id_employee' => $record->id_employee,
                'emp_code' => $record->emp_code,
                'name' => $record->name,
                'refferal_count' => $record->refferal_count,
                'is_refferal_by' => $record->is_refferal_by,
                'id_scheme' => $record->id_scheme,
                'benifits' => $record->benifits,
                'code' => $record->code,
                'payment_amount' => $record->payment_amount,
                'referral_deatils' => $this->empreferral_account_details($record->id_employee)
            );
        }
        return $records;
    }
    function empreferral_account_details($id)
    {
        $sql = "select s.code,p.payment_amount, IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,if(sa.scheme_acc_number is null ,s.scheme_name,concat(s.scheme_name,'-',sa.scheme_acc_number))as scheme_acc_number,c.id_customer,if(c.lastname is null ,c.firstname,concat(c.firstname,'',c.lastname))as cus_name 
	     from customer c
		left join scheme_account sa on(sa.id_customer=c.id_customer)
		left join payment p on (sa.id_scheme_account=p.id_scheme_account)
		left join scheme s on (s.id_scheme=sa.id_scheme)
		left join employee e on(e.emp_code=sa.referal_code) where e.id_employee='$id' and payment_status=1 and sa.is_refferal_by=1 group by sa.id_scheme_account";
        //print_r($sql);exit;
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    function get_load_account($id_payment, $id_scheme_account)
    {
        //DGS-DCNM --> curday_total_paid,
        $schemeAcc = array();
        $sql = "Select (SELECT SUM(p.payment_amount) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND date(p.date_payment) = curdate()) as curday_total_paid,s.daily_pay_limit, s.total_days_to_pay,DATEDIFF(CURDATE(),date(sa.start_date)) + 1 as joined_date_diff,
					sg.group_code as scheme_group_code, s.one_time_premium,s.wgt_store_as,UNIX_TIMESTAMP(Date_Format(sg.start_date,'%Y-%m-%d')) as group_start_date,  UNIX_TIMESTAMP(Date_Format(sg.end_date,'%Y-%m-%d')) as  group_end_date,  cs.has_lucky_draw,
					s.min_amt_chance,s.max_amt_chance,s.code,s.min_amount,s.show_ins_type,
				   	sa.id_scheme_account,s.gst,s.gst_type,s.max_amount,
					s.id_scheme,s.wgt_convert,if(s.cus_refferal=1 || s.emp_refferal=1,sa.referal_code,'')as referal_code,
					s.ref_benifitadd_ins_type,s.ref_benifitadd_ins,
					c.id_customer,
					CONCAT(s.code,'-',IFNULL(sa.scheme_acc_number,'Not Allocated')) as chit_number,
					IFNULL(sa.account_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,
					s.scheme_name,
					s.scheme_type,
					if(scheme_type=3,if(s.max_amount!='',s.max_amount * s.total_installments,s.max_weight),s.amount)as scheme_overall_amount,
					IFNULL(s.min_chance,0) as min_chance,
					IFNULL(s.max_chance,0) as max_chance,
					Format(IFNULL(s.max_weight,0),3) as max_weight,
					Format(IFNULL(s.min_weight,0),3) as min_weight,
					Date_Format(sa.start_date,'%d-%m-%Y') as start_date,
					(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1) as metal_rate,
  IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,s.min_amount,0))) as payable,  
					s.total_installments,
IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or s.scheme_type=3 , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
  as paid_installments,
IFNULL(IF(sa.is_opening=1,IFNULL(balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0)
  as total_paid_amount,
IFNULL(IF(sa.is_opening=1 and s.scheme_type!=0,IFNULL(balance_weight,0)+IFNULL(SUM(p.metal_weight),0),
IFNULL(SUM(p.metal_weight),0)),0.000) 
 as total_paid_weight,
  if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments, 
(s.total_installments - COUNT(payment_amount)), 
ifnull((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) - SUM(p.no_of_dues),if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,s.total_installments,(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))))) 
  as totalunpaid_1, 
  if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,   (s.total_installments - if(sa.is_opening = 1,(COUNT(payment_amount)+sa.paid_installments),COUNT(payment_amount))),ifnull(((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))+1) - IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))),if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,s.total_installments,(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa
.start_date,'%Y%m')))))) as totalunpaid,   
   IFNULL(if(Date_Format(max(p.date_add),'%Y%m') = Date_Format(curdate(),'%Y%m'), (select SUM(ip.no_of_dues) from payment ip where Date_Format(ip.date_add,'%Y%m') = Date_Format(curdate(),'%Y%m') and sa.id_scheme_account = ip.id_scheme_account),IF(sa.is_opening=1, if(Date_Format(sa.last_paid_date,'%Y%m') = Date_Format(curdate(),'%Y%m'), 1,0),0)),0) as currentmonthpaycount, 
  (select SUM(pay.no_of_dues) from payment pay where pay.id_scheme_account= sa.id_scheme_account and pay.due_type='AD' and (pay.payment_status=1 or pay.payment_status=2)) as currentmonth_adv_paycount,
  (select SUM(pay.no_of_dues) from payment pay where pay.id_scheme_account= sa.id_scheme_account and pay.due_type='PD' and (pay.payment_status=1 or pay.payment_status=2)) as currentmonth_pend_paycount,
IF(s.scheme_type =1 and s.max_weight !=s.min_weight,true,false) as is_flexible_wgt,p.payment_status,
					if(scheme_type=3,IFNULL(cp.total_amount,0),Format(IFNULL(cp.total_amount,0),2)) as  current_total_amount,
					Format(IFNULL(cp.total_weight,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_weight),0) ,3) as  current_total_weight,
					IFNULL(cp.paid_installment,0)       as  current_paid_installments,
						IFNULL(cp.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0) as  current_chances_used,
						if(s.scheme_type=3 && s.pay_duration=0 ,IFNULL(sp.chance,0) + IF(Date_Format(Current_Date(),'%d%m')=Date_Format(sa.last_paid_date,'%d%m'),(sa.last_paid_chances),0),IFNULL(cp.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0)) as  current_chances_use,
				IFNULL(sp.chance,0)as dd,
					s.is_pan_required,
					IF(sa.is_opening = 1 and s.scheme_type = 0,
					IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),false,true),
					true) AS previous_amount_eligible,
					count(pp.id_scheme_account) as cur_month_pdc,
					IFNULL(Date_Format(max(p.date_add),'%d-%m-%Y'),IFNULL(IF(sa.is_opening=1,Date_Format(sa.last_paid_date,'%d-%m-%Y'),'')  ,0))                 as last_paid_date,
					IFNULL(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'),Date_Format(max(p.date_add),'%Y%m')),IF(sa.is_opening=1,PERIOD_DIFF(Date_Format(curdate(),'%Y%m'),Date_Format(sa.last_paid_date,'%Y%m')),0)) as last_paid_duration,
				IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),1,0) as  previous_paid,	
					sa.disable_payment,
				s.allow_unpaid,
				if(s.allow_unpaid=1,s.unpaid_months,0) as allow_unpaid_months,
				s.allow_advance,
				if(s.allow_advance=1,s.advance_months,0) as advance_months,
					if(s.allow_unpaid=1,s.unpaid_weight_limit,0) as unpaid_weight_limit,
					s.allow_advance,
					if(s.allow_advance=1,s.advance_weight_limit,0) as advance_weight_limit,
					s.allow_preclose,
					if(s.allow_preclose=1,s.preclose_months,0) as preclose_months,
					if(s.allow_preclose=1,s.preclose_benefits,0) as preclose_benefits,cs.currency_symbol
				From scheme_account sa
				Left Join scheme s On (sa.id_scheme=s.id_scheme)
				Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=2 or p.payment_status=1))
				Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)
				Left Join scheme_group sg On (sa.group_code = sg.group_code )
				Left Join
					(	Select
						  sa.id_scheme_account,
						  COUNT(Distinct Date_Format(p.date_add,'%Y%m')) as paid_installment,
						  COUNT(Date_Format(p.date_add,'%Y%m')) as chances,	
						  SUM(p.payment_amount) as total_amount,
						  SUM(p.metal_weight) as total_weight
						From payment p
						Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)
						Where  (p.payment_status=2 or p.payment_status=1) and  Date_Format(Current_Date(),'%Y%m')=Date_Format(p.date_add,'%Y%m')
						Group By sa.id_scheme_account
					) cp On (sa.id_scheme_account=cp.id_scheme_account)
					left join(Select sa.id_scheme_account, COUNT(Distinct Date_Format(p.date_add,'%d%m')) as paid_installment,
				COUNT(Date_Format(p.date_add,'%d%m')) as chance,
			SUM(p.payment_amount) as total_amount,
			SUM(p.metal_weight) as total_weight
			From payment p
			Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)
			Where  (p.payment_status=2 or p.payment_status=1) and  Date_Format(Current_Date(),'%d%m')=Date_Format(p.date_add,'%d%m')
		Group By sa.id_scheme_account)sp on(sa.id_scheme_account=sp.id_scheme_account)
				 Left Join postdate_payment pp On (sa.id_scheme_account=pp.id_scheme_account and (pp.payment_status=2 or pp.payment_status=7) and (Date_Format(pp.date_payment,'%Y%m')=Date_Format(curdate(),'%Y%m')))	
				 join chit_settings cs 
				Where sa.active=1 and sa.is_closed = 0 and p.id_payment<='$id_payment' and sa.id_scheme_account='$id_scheme_account'
				Group By sa.id_scheme_account";
        //	echo $sql;exit;
        $records = $this->db->query($sql);
        //if(Date_Format(max(p.date_add),'%Y%m') = Date_Format(curdate(),'%Y%m'), SUM(p.no_of_dues),0)  as currentmonthpaycount,	
        if ($records->num_rows() > 0) {
            $record = $records->row();
            $allowed_due = 0;
            $due_type = '';
            $checkDues = TRUE;
            if ($record->has_lucky_draw == 1) {
                if ($record->group_start_date == NULL && $record->paid_installments > 1) { // block 2nd payment if scheme_group_code is not updated 
                    $checkDues = FALSE;
                } else if ($record->group_start_date != NULL) { // block  payment after end date
                    if ($record->group_end_date >= time() && $record->group_start_date <= time()) {
                        $checkDues = TRUE;
                    } else {
                        $checkDues = FALSE;
                    }
                }
            }
            if ($checkDues) {
                if ($record->paid_installments > 0 || $record->totalunpaid > 0) {
                    if ($record->currentmonthpaycount == 0) {  // current month not paid (allowed pending due + current due)
                        if ($record->allow_unpaid == 1) {
                            if ($record->allow_unpaid_months > 0 && ($record->total_installments - $record->paid_installments) >= $record->allow_unpaid_months && $record->totalunpaid > 0) {
                                if (($record->total_installments - $record->paid_installments) == $record->allow_unpaid_months) {
                                    $allowed_due = ($record->totalunpaid < $record->allow_unpaid_months ? $record->totalunpaid : $record->allow_unpaid_months);
                                    $due_type = 'PD'; //  pending
                                } else {
                                    $allowed_due = ($record->totalunpaid < $record->allow_unpaid_months ? $record->totalunpaid : $record->allow_unpaid_months) + 1;
                                    $due_type = 'PN'; // normal and pending
                                }
                            } else {
                                $allowed_due = 1;
                                $due_type = 'ND'; // normal due
                            }
                        } else {
                            $allowed_due = 1;
                            $due_type = 'ND'; // normal due
                        }
                    } else {     //current month paid
                        if ($record->allow_unpaid == 1 && $record->allow_unpaid_months > 0 && $record->totalunpaid > 0 && ($record->currentmonthpaycount - 1) < $record->allow_unpaid_months) {
                            // can pay previous pending dues if attempts available 
                            if ($record->totalunpaid > $record->allow_unpaid_months) {
                                $allowed_due = $record->allow_unpaid_months;
                                $due_type = 'PD'; // pending due
                            } else {
                                $allowed_due = $record->totalunpaid;
                                $due_type = 'PD'; // pending due
                            }
                        } else {  // check allow advance
                            if ($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonth_adv_paycount) < $record->advance_months) {
                                if (($record->advance_months + $record->paid_installments) <= $record->total_installments) {
                                    $allowed_due = ($record->advance_months - ($record->currentmonth_adv_paycount));
                                    $due_type = 'AD'; // advance due
                                } else {
                                    $allowed_due = ($record->total_installments - $record->paid_installments);
                                    $due_type = 'AD'; // advance due
                                }
                            } else { // have to check
                                $allowed_due = 0;
                                $due_type = ''; // normal due
                            }
                        }
                    }
                } else {  // check allow advance and add due with currect month (allowed advance due + current due)
                    if ($record->allow_advance == 1) { // check allow advance
                        if ($record->advance_months > 0 && $record->advance_months <= ($record->total_installments - $record->paid_installments)) {
                            if (($record->total_installments - $record->paid_installments) == $record->advance_months) {
                                $allowed_due = $record->advance_months;
                                $due_type = 'AN'; // advance and normal
                            } else {
                                $allowed_due = $record->advance_months + 1;
                                $due_type = 'AN'; // advance and normal
                            }
                        } else {
                            $allowed_due = 1;
                            $due_type = 'ND'; // normal due
                        }
                    } else {
                        $allowed_due = 1;
                        $due_type = 'ND'; // normal due
                    }
                }
            }
            $pdc_det = $this->get_pending_pdc($record->id_scheme_account);
            $dates = date('d-m-Y');
            $schemeAcc = array(
                'metal_rate' => $record->metal_rate,
                'min_amount' => $record->min_amount,
                'max_amount' => $record->max_amount,
                'min_amt_chance' => $record->min_amt_chance,
                'max_amt_chance' => $record->max_amt_chance,
                'gst' => $record->gst,
                'scheme_overall_amount' => $record->scheme_overall_amount,
                'gst_type' => $record->gst_type,
                'currentmonth_adv_paycount' => $record->currentmonth_adv_paycount,
                'currentmonthpaycount' => $record->currentmonthpaycount,
                'current_date' => $dates,
                'totalunpaid' => $record->totalunpaid,
                'id_scheme_account' => $record->id_scheme_account,
                'start_date' => $record->start_date,
                'chit_number' => $record->chit_number,
                'account_name' => $record->account_name,
                'payable' => $record->payable,
                'scheme_name' => $record->scheme_name,
                'code' => $record->code,
                'scheme_type' => $record->scheme_type,
                'currency_symbol' => $record->currency_symbol,
                'min_weight' => $record->min_weight,
                'max_weight' => $record->max_weight,
                'wgt_convert' => $record->wgt_convert,
                'total_installments' => $record->total_installments,
                'paid_installments' => $record->paid_installments,
                'total_paid_amount' => $record->total_paid_amount,
                'total_paid_weight' => $record->total_paid_weight,
                'current_total_amount' => $record->current_total_amount,
                'current_paid_installments' => $record->current_paid_installments,
                'current_chances_used' => $record->current_chances_used,
                'current_chances_use' => $record->current_chances_use,
                'current_total_weight' => $record->current_total_weight,
                'last_paid_duration' => $record->last_paid_duration,
                'last_paid_date' => $record->last_paid_date,
                'is_pan_required' => $record->is_pan_required,
                'last_transaction' => $this->getLastTransaction($record->id_scheme_account),
                'isPaymentExist' => $this->isPaymentExist($record->id_scheme_account),
                'previous_amount_eligible' => $record->previous_amount_eligible,
                'cur_month_pdc' => $record->cur_month_pdc,
                'is_flexible_wgt' => $record->is_flexible_wgt,
                'max_chance' => $record->max_chance,
                'ref_benifitadd_ins' => $record->ref_benifitadd_ins,
                'ref_benifitadd_ins_type' => $record->ref_benifitadd_ins_type,
                'referal_code' => $record->referal_code,
                /*'allow_pay'  => ($record->scheme_type==3  &&$record->paid_installments <  $record->total_installments  && $record->current_chances_use < $record->max_chance &&$record-> current_total_amount < $record-> max_amount?'Y':($record->disable_payment != 1 && ($record->payment_status !=2) ? ($record->cur_month_pdc < 1 ? ($record->paid_installments <= $record->total_installments ?($record->is_flexible_wgt?($record->current_total_weight >= $record->max_weight || $record->current_chances_used >= $record->max_chance ?'N':'Y'):($record->paid_installments <  $record->total_installments ?($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'N')):'N'):'N'):'N')),*/
                'allow_pay' => ($checkDues ? ($record->scheme_type == 3 && $record->paid_installments <= $record->total_installments && $record->current_chances_use < $record->max_chance && ($record->current_total_amount < $record->max_amount || $record->current_total_weight < $record->max_weight) ? 'Y' : ($record->disable_payment != 1 && ($record->payment_status != 2) ? ($record->cur_month_pdc < 1 ? ($record->paid_installments <= $record->total_installments ? ($record->is_flexible_wgt ? ($record->current_total_weight >= $record->max_weight || $record->current_chances_used >= $record->max_chance ? '1' : 'Y') : ($record->paid_installments < $record->total_installments ? ($record->allow_unpaid == 1 && $record->totalunpaid > 0 && ($record->currentmonthpaycount - 1) < $record->allow_unpaid_months ? 'Y' : ($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount - 1) < $record->advance_months ? 'Y' : ($record->currentmonthpaycount == 0 ? 'Y' : 'N'))) : 'N')) : 'N') : 'N') : 'N')) : 'N'),
                'allowed_dues' => ($record->is_flexible_wgt == 1 || $record->scheme_type == 3 ? 1 : $allowed_due),
                'due_type' => ($record->is_flexible_wgt == 1 ? 'ND' : $due_type),
                'allow_preclose' => ($record->currentmonthpaycount == 1 ? ($record->allow_preclose == 1 ? ($record->total_installments - $record->paid_installments == $record->preclose_months ? 1 : 0) : 0) : 0),
                'pdc_payments' => ($record->cur_month_pdc > 0 ? $this->get_postdated_payment($record->id_scheme_account) : 0),
                'total_pdc' => (isset($pdc_det) && $pdc_det != '' ? $pdc_det : 0),
                'weights' => ($record->scheme_type == '1' ? $this->getWeights() : ''),
                'preclose' => ($record->allow_preclose == 1 ? $record->preclose_months : 0),
                'preclose_benefits' => ($record->allow_preclose == 1 ? $record->preclose_benefits : 0),
                'is_otp_scheme' => $record->one_time_premium,
                'wgt_store_as' => $record->wgt_store_as
            );
        }
        //	print_r($schemeAcc); exit;
        return $schemeAcc;
    }
    function wallet_balance($id_cus)
    {
        $data = array();
        $sql = "Select
								  wa.id_wallet_account,
								  c.id_customer,
								  Concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')) as name,
								  c.mobile,
								  Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_name,
								  wa.wallet_acc_number,
								  Date_Format(wa.issued_date,'%d-%m-%Y') as issued_date,
								  wa.remark,
								  wa.active,
								  SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) as  issues,
								  SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END) as redeem,
								  (SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) -   SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END)) as balance,
								  cs.wallet_amt_per_points,cs.wallet_balance_type,cs.wallet_points
							From wallet_account wa
								Left Join customer c on (wa.id_customer=c.id_customer)
								Left Join employee e on (wa.id_employee=e.id_employee)
								Left Join wallet_transaction wt on (wa.id_wallet_account=wt.id_wallet_account)
								join chit_settings cs
								where c.id_customer =" . $id_cus;
        $result = $this->db->query($sql);
        if ($result->num_rows() > 0) {
            $sql1 = "SELECT w.redeem_percent FROM wallet_category_settings w where w.id_category=1";
            $record = $this->db->query($sql1);
            if ($record->num_rows() > 0) {
                $balance = ($result->row()->wallet_balance_type == 1 ? (($result->row()->balance / $result->row()->wallet_points) * $result->row()->wallet_amt_per_points) : $result->row()->balance);
                //$data=(($result->row()->balance*$record->row()->redeem_percent)/100);
                $balance = is_nan($balance) ? 0 : $balance;
                $data = array('redeem_percent' => $record->row()->redeem_percent, 'wal_balance' => floor($balance));
            }
        }
        return $data;
    }
    function getWcategorySettings($cat_code = "")
    {
        if ($cat_code == "") {
            $sql = $this->db->query("SELECT id_wcat_settings,`value`,`point`,`id_category`,`redeem_percent`,ws.`active`,`code`,ws.`active` FROM wallet_category_settings ws 
        		LEFT JOIN wallet_category wc on  wc.id_wallet_category = ws.id_category and wc.active=1
        WHERE ws.active=1");
            return $sql->result_array();
        } else {
            $sql = $this->db->query("SELECT id_wcat_settings,`value`,`point`,`id_category`,`redeem_percent`,ws.`active`,`code`,ws.`active` FROM wallet_category_settings ws 
        		LEFT JOIN wallet_category wc on  wc.id_wallet_category = ws.id_category and wc.active=1
        WHERE ws.active=1 and wc.code='" . $cat_code . "'");
            return $sql->row_array();
        }
        //  echo $this->db->last_query();
    }
    function getWalletPaymentContent($id_scheme_account)
    {
        $sql = "Select iwa.available_points,sa.id_branch as branch, ifnull(iwa.mobile,0) as isAvail,c.mobile,cs.walletIntegration,cs.wallet_points,cs.wallet_amt_per_points,cs.wallet_balance_type
	    From scheme_account sa 
	    Left Join customer c on (c.id_customer=sa.id_customer) 
	    LEFT JOIN inter_wallet_account iwa on iwa.mobile=c.mobile 
	    join chit_settings cs 
	    Where sa.id_scheme_account='" . $id_scheme_account . "'";
        return $this->db->query($sql)->row_array();
    }
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
    public function updateData($data, $id_field, $id_value, $table)
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
                if ($value === 0 || $value === '0') {
                    $data[$field] = 0;
                } else {
                    $data[$field] = $default_values[$field];
                }
            }
        }
        $edit_flag = 0;
        $this->db->where($id_field, $id_value);
        $edit_flag = $this->db->update($table, $data);
        return ($edit_flag == 1 ? $id_value : 0);
    }
    // function insertData($data, $table)
    // {
    // 	$status = $this->db->insert($table, $data);
    // 	return	array('status' => $status, 'insertID' => ($status == TRUE ? $this->db->insert_id() : ''));
    // }
    public function insertBatchData($data, $table)
    {
        $insert_flag = 0;
        $insert_flag = $this->db->insert_batch($table, $data);
        if ($this->db->affected_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    function updateAtData($data, $id_field, $id_value, $table)
    {
        $edit_flag = 0;
        $this->db->where($id_field, $id_value);
        $status = $this->db->update($table, $data);
        return ($edit_flag == 1 ? 1 : 0);
    }
    // function updateData($data, $tran, $table)
    // {
    // 	$this->db->where('bill_no', $tran['bill_no']);
    // 	if ($tran['id_branch'] == '') {
    // 		$this->db->where('id_branch', null);
    // 	} else {
    // 		$this->db->where('id_branch', $tran['id_branch']);
    // 	}
    // 	$status = $this->db->update($table, $data);
    // 	return $status;
    // }
    function updateTransDetailData($data, $id)
    {
        $this->db->where('id_inter_waltransdetail', $id);
        $status = $this->db->update('inter_wallet_trans_detail', $data);
        return $status;
    }
    function getInterWalletCustomer($mobile)
    {
        $sql = $this->db->query("SELECT * FROM  inter_wallet_account WHERE mobile=" . $mobile);
        if ($sql->num_rows() > 0) {
            return array('status' => true, 'data' => $sql->row_array());
        } else {
            return array('status' => false, 'data' => '');
        }
    }
    function updInterWalletAcc($data)
    {
        $this->db->where('mobile', $data['mobile']);
        $status = $this->db->update('inter_wallet_account', array('available_points' => $data['available_points'], 'last_update' => date('Y-m-d H:i:s')));
        return $status;
    }
    function updwallet($trans, $mobile)
    {
        $sql = $this->db->query("select c.id_customer,id_wallet_account from customer  c left join wallet_account wa on wa.id_customer = c.id_customer where mobile=" . $mobile);
        if ($sql->num_rows() > 0) {
            $id_wallet_ac = $sql->row('id_wallet_account');
            // print_r($trans);exit;
            $data = array(
                'id_wallet_account' => $id_wallet_ac,
                'date_transaction' => date('Y-m-d H:i:s'),
                'transaction_type' => ($trans['trans_type'] == 1 ? 0 : 1),
                'value' => $trans['trans_points'],
                'description' => $trans['remark'],
            );
            $status = $this->db->insert('wallet_transaction', $data);
            //var_dump($status);exit; 
            return $status;
        } else {
            return TRUE;
        }
    }
    function getSyncWalletData($id_branch)
    {
        $sql = "SELECT * FROM  inter_sync_wallet WHERE branch_" . $id_branch . "= 0";
        return $this->db->query($sql)->result_array();
    }
    function getSyncWalletByMobile($mobile)
    {
        $sql = "SELECT * FROM  inter_sync_wallet WHERE mobile=" . $mobile;
        return $this->db->query($sql)->row_array();
    }
    function updateSyncWal($data)
    {
        $this->db->where('mobile', $data['mobile']);
        $status = $this->db->update('inter_sync_wallet', $data);
        return $status;
    }
    function get_payment_gateway()
    {
        $sql = "SELECT g.id_pg, IF( g.id_branch > 0, CONCAT(b.name, ' ', g.pg_name), g.pg_name ) AS pg_name, g.pg_code, g.TYPE FROM gateway g JOIN branch b ON b.id_branch = g.id_branch
        WHERE 
        -- type=1
        g.is_default = 1 AND g.active = 1";
        return $this->db->query($sql)->result_array();
    }
    function get_paidinstallmentcount($id)
    {
        $sql = "SELECT  p.id_payment,p.id_scheme_account,s.id_scheme
				FROM scheme_account s
				left join payment p on(p.id_scheme_account = s.id_scheme_account) 
				where s.id_scheme_account=" . $id . " and p.payment_status=1 order by p.id_payment Asc";
        return $this->db->query($sql)->result_array();
    }
    function get_customer($id_customer)
    {
        $this->db->where('id_customer', $id_customer);
        $r = $this->db->get(self::CUS_TABLE);
        if ($r->num_rows == 1) {
            $result = $r->row_array();
            return $result;
        } else {
            return array('status' => 2, 'msg' => 'Invalid');
        }
    }
    function otp_update($data, $id)
    {
        $this->db->where('id_sch_acc', $id);
        $status = $this->db->update(self::OTP_TABLE, $data);
        return $status;
    }
    function isOTPRegForPayment()
    {
        $sql = "Select isOTPRegForPayment from chit_settings where id_chit_settings = 1";
        return $this->db->query($sql)->row()->isOTPRegForPayment;
    }
    function payOTP_exp()
    {
        $sql = "Select payOTP_exp from chit_settings where id_chit_settings = 1";
        return $this->db->query($sql)->row()->payOTP_exp;
    }
    function isOTPReqToLogin()
    {
        $sql = "Select isOTPReqToLogin from chit_settings where id_chit_settings = 1";
        return $this->db->query($sql)->row()->isOTPReqToLogin;
    }
    function payment_cancel($type = "", $id = "", $pay_array = "")
    {
        switch ($type) {
            case 'update':
                $this->db->where("id_payment", $id);
                $status = $this->db->update("payment", $pay_array);
                return array('status' => $status, 'updateID' => $id);
                break;
        }
    }
    function firstPayamt_payable()
    {
        $sql = "Select c.firstPayamt_payable FROM chit_settings c where c.id_chit_settings = 1";
        return $this->db->query($sql)->row()->firstPayamt_payable;
    }
    function getBranchGatewayData($branch_id, $pg_code)
    {
        $sql = "SELECT id_pg,param_1,param_2,param_3,param_4,pg_code,api_url,id_pg from gateway where is_default=1 and active=1 and  pg_code=" . $pg_code . " " . ($branch_id != '' ? "and id_branch=" . $branch_id . "" : '') . "";
        $result = $this->db->query($sql)->row_array();
        //	print_r($sql);exit;
        return $result;
    }
    // get payment data//HH
    function get_payments_data_list($ref_trans_id)
    {
        //echo $id;
        $sql = ("SELECT
					cs.has_lucky_draw,s.code,IFNULL(sa.group_code,'')as scheme_group_code,
					  p.id_payment,p.is_offline,sa.id_branch,sa.ref_no,sa.id_scheme_account,
					  sa.account_name,p.payment_amount,
					  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname, ' ',c.mobile)) as name,c.lastname,c.firstname,
					  c.mobile,c.email,
					  IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,p.due_type,p.act_amount,
					  s.code,s.scheme_name,
					  p.id_employee,IFNULL(e.emp_code,'-')as emp_code,
                      if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee, 
					  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
					  IFNULL(p.payment_amount,'-') as payment_amount,
				      IFNULL(if(p.metal_rate=0,'- ',p.metal_rate), '- ') as metal_rate,
					  IFNULL(if(p.metal_weight=0,'-',p.metal_weight), '-') as metal_weight,
					  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
					  IFNULL(Date_format(p.last_update,'%d-%m%-%Y'),'-') as last_update,
			          p.payment_type,concat(p.payment_type, ' ',p.payment_mode) as payment_type,
					  IFNULL(sa.scheme_acc_number,'') as msno,
					  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
					  IFNULL(p.bank_name,'-')as bank_name,
					  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
					  IFNULL(p.bank_branch,'-') as bank_branch,
					  IFNULL(cs.receipt_no_set,'-') as receipt_no_set,
					  IFNULL(p.id_transaction,'-') as id_transaction,
					  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
					  IFNULL(p.act_amount, '-') as act_amount,
					  IFNULL(p.payu_id,'-') as payu_id ,
					  IFNULL(b.name,'') as id_branch ,
					  IFNULL(p.card_no,'-') as card_no,
					  IFNULL(p.ref_trans_id,'-') as ref_trans_id,
					  psm.payment_status as payment_status,
					  p.payment_status as id_status,
					  psm.color as status_color,
					if(cs.receipt_no_set=1 && p.receipt_no is null,'',p.receipt_no) as receipt_no,
					  IFNULL(p.remark,'-') as remark,cs.currency_name,cs.currency_symbol
				FROM payment p
				join chit_settings cs
				left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
				Left Join employee e On (e.id_employee=p.id_employee)
				Left Join customer c on (sa.id_customer=c.id_customer)
				left join scheme s on(sa.id_scheme=s.id_scheme)
			    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)		
			    Left Join branch b on (p.id_branch=b.id_branch)		
			    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
		 where p.ref_trans_id='" . $ref_trans_id . "'
				 ORDER BY p.date_payment DESC");
        //print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    // get payment data//
    //emp scheme account report
    function get_all_emp_account_by_range($from_date, $to_date, $id_branch, $id_employee, $id_scheme, $acc_number)
    {
        $branch_settings = $this->session->userdata('branchWiseLogin');
        $log_branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $accounts = $this->db->query("select concat(e.firstname,' ',e.lastname)as  employee_name,s.id_employee,s.employee_approved,e.login_branches,b.name as branch_name,
		IFNULL(s.pan_no,'-') as pan_no,cs.has_lucky_draw,
		                          IFNULL(s.group_code,'') as group_code, s.added_by,
                                 if(s.show_gift_article=1,'Issued','Not Issueed')as gift_article,
							  s.id_scheme_account,IFNULL(s.scheme_acc_number,'Not Allocated') as scheme_acc_number ,IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,s.ref_no,s.account_name,DATE_FORMAT(s.start_date,'%d-%m-%Y') as start_date,c.is_new,s.added_by,concat('C','',c.id_customer) as id_customer,cs.schemeacc_no_set,
							  sc.scheme_name,if(s.is_new ='Y','New','Existing') as is_new,sc.code,if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,sc.total_installments,sc.max_chance,sc.max_weight,sc.amount,c.mobile,if(s.active =1,'Active','Inactive') as active,s.date_add,cs.currency_symbol,sc.scheme_type  as scheme_types,
		if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight',if(sc.scheme_type=2,'Amount to Weight',if(sc.flexible_sch_type=2,'Flexible Amount',IF(sc.flexible_sch_type = 3 , 'Flexible Weight','Flexible'))))) as scheme_type,flexible_sch_type,
		IF(sc.scheme_type=0 OR sc.scheme_type=2,sc.amount,IF(sc.scheme_type=1 ,sc.max_weight,if(sc.scheme_type=3,if(flexible_sch_type = 3 ,  sc.max_weight,if(sc.firstPayamt_as_payamt=1,s.firstPayment_amt ,sc.min_amount)),0))) as payable,
		IFNULL(IF(s.is_opening=1,IFNULL(s.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 and sc.payment_chances=1) , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0)as paid_installments,
		concat(emp.firstname,' ',emp.lastname) as acc_created_by
							from
							  " . self::ACC_TABLE . " s
							left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
							left join " . self::BRANCH . " b on (b.id_branch=s.id_branch)
							left join " . self::EMPLOYEE_TABLE . " 	e on (e.id_employee=s.id_employee)
							left join " . self::EMPLOYEE_TABLE . " 	emp on (emp.id_employee=s.employee_approved)
							left join " . self::PAY_TABLE . " pay on (pay.id_scheme_account=s.id_scheme_account  and (pay.payment_status=2 or pay.payment_status=1))
							join chit_settings cs
							 Where s.scheme_acc_number is NOT NULL and s.is_closed=0 
							 " . ($id_employee != '' ? "and s.id_employee=" . $id_employee . "" : '') . "
							 " . ($id_branch != '' && $id_branch != 0 ? "and s.id_branch=" . $id_branch . "" : '') . "
							 " . ($id_scheme != '' ? "and s.id_scheme=" . $id_scheme . "" : '') . "
							 " . ($acc_number != '' ? "and s.scheme_acc_number=" . $acc_number . "" : '') . "
							 " . ($from_date != '' ? " And (date(pay.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')" : '') . "
							group by s.id_scheme_account
							Having paid_installments>0");
        //print_r($this->db->last_query()); exit;
        return $accounts->result_array();
    }
    //offline date insert manual
    public function instrans_rec($data)
    {
        $empid = $this->session->userdata('uid');
        $sql = $this->db->query("select emp_code from employee e where " . $empid . "= e.id_employee ");
        if ($sql->num_rows() > 0) {
            $emp_code = $sql->row('emp_code');
            $instran_info = array('emp_code' => $emp_code);
            $status = $this->db->insert(self::TRANS_TABLE, $data);
            return $status;
        } else {
            return TRUE;
        }
    }
    //offline date insert manual
    //Employee wise summary
    function payment_employee_summary($from_date, $to_date, $id_branch, $id_emp)
    {
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = '';
        }
        //print_r($id_emp);exit;
        $sql_1 = "select  s.code,p.id_employee,e.firstname,s.code,IFNULL(b.name,'')as name,
					sum(p.payment_amount) as payment_amount,s.gst_type, s.gst,IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,					
					COUNT(CASE WHEN  (p.receipt_no is not null  || p.receipt_no is null ) and p.payment_status=1 THEN 1 END) as receipt,
					compy.gst_number,cs.gst_setting,
					if(p.payment_mode='FP','FP',p.payment_mode)as payment_mode,
					if(p.payment_mode='CC' || p.payment_mode='DC','Card',p.payment_mode)as payment_mode					
					FROM sch_classify sc
					 join company compy
					 join chit_settings cs
					LEFT JOIN scheme s ON (sc.id_classification = s.id_classification)
					  LEFT JOIN scheme_account sa ON (s.id_scheme = sa.id_scheme)
					  LEFT JOIN payment p ON (sa.id_scheme_account = p.id_scheme_account)
					  Left JOIN branch b on(b.id_branch=p.id_branch)
					  LEFT JOIN employee e ON (e.id_employee=p.id_employee)
					  LEFT JOIN postdate_payment pp ON (sa.id_scheme_account = pp.id_scheme_account)
						WHERE p.id_employee is not null and sc.active=1 AND (p.payment_status=1 or pp.payment_status=1) " . ($id_emp != 0 && $id_emp != '' ? ' and p.id_employee =' . $id_emp : '') . "
						    " . ($from_date != '' ? " And (date(p.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')" : '') . " " . ($id_branch != 0 && $id_branch != '' ? ' and p.id_branch IN (' . $id_branch . ')' : '') . "
						GROUP BY p.id_employee,s.code";
        //print_r($sql_1);exit;
        $payments = $this->db->query($sql_1)->result_array();
        return $payments;
    }
    //Employee wise summary
    //mob no,ref no,clientid,sch A/c no wise filter & change options in inter table Data's // 
    // Customer Reg& transaction records  // HH	
    function get_intertable_list($mobile, $clientid, $ref_no, $group_code, $cus = "")
    {
        $sql = "SELECT * FROM `customer_reg` WHERE 1=1 " .
            ($mobile != '' ? "AND mobile = '" . $mobile . "' " : "") .
            ($clientid != '' ? "AND clientid = '" . $clientid . "' " : "") .
            ($ref_no != '' ? "AND ref_no = '" . $ref_no . "' " : "") .
            ($group_code != '' ? "AND group_code = '" . $group_code . "' " : "");
        $this->load->database('default', true);
        //print_r($sql);exit;  
        return $this->db->query($sql)->result_array();
    }
    function get_intertable_translist($client_id, $ref_no, $cus = "")
    {
        $sql = "SELECT * FROM `transaction` where 1=1 " .
            ($client_id != '' ? " AND client_id = '" . $client_id . "' " : "") .
            ($ref_no != '' ? " AND ref_no = '" . $ref_no . "' " : "");
        $this->load->database('default', true);
        //print_r($sql);exit;  
        return $this->db->query($sql)->result_array();
    }
    function update_cusdata($id, $mobile, $scheme_ac_no, $group_code, $is_transferred)
    {
        $data['mobile'] = $mobile;
        $data['scheme_ac_no'] = $scheme_ac_no;
        $data['group_code'] = $group_code;
        $data['is_transferred'] = $is_transferred;
        //$this->db->where('id_customer_reg',$id);
        //$res = $this->db->update(self::CUS_REG_TABLE,$data);
        if ($data['is_transferred'] != '') {
            $this->db->where('id_customer_reg', $id);
            $res = $this->db->update(self::CUS_REG_TABLE, $data);
        } else {
            $res = false;
        }
        //$res = $this->db->update(self::CUS_REG_TABLE,$data);
        //print_r($this->db->last_query());exit;
        return $res;
    }
    // created by durga 28/12/2022 starts here 
    function update_transdata($id, $is_transferred)
    {
        $data['is_transferred'] = $is_transferred;
        if ($is_transferred != '') {
            $this->db->where('id_transaction', $id);
            $res = $this->db->update(self::TRANS_TABLE, $data);
        } else {
            $res = false;
        }
        //print_r($res);exit;
        //print_r($this->db->last_query());exit;
        return $res;
    }
    // created by durga 28/12/2022 ends here 
    //mob no,ref no,clientid,sch A/c no wise filter & change options in inter table Data's // 
    // Customer Reg& transaction records  // 		
    /*function get_kycdata_range($from_date,$to_date,$status,$type)
    {
          if($type ==1)
          {
           $sql =("SELECT id_kyc,c.id_customer as cus, IF(kyc_type = 1, 'Bank Account', IF(kyc_type = 2, 'PAN Card', IF(kyc_type = 3, 'Aadhaar', ''))) as kyc_type,number,name,bank_ifsc ,bank_branch,IF(status = 0, 'Pending', IF(status = 1, 'In Progress', IF(status = 2, 'Verified', IF(status = 3, 'Rejected', '')))) as status ,IF(verification_type = 1, 'Manual',IF(verification_type = 2,'Auto', '')) as verification_type,last_update,kyc.date_add,Date_format(kyc.dob, '%d-%m-%Y') as dob,
                Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_verified_by,
                kyc.emp_verified_by as id_employee ,c.firstname as id_customer,c.mobile,
                kyc.img_url,kyc.added_by
                FROM `kyc` kyc
                Left Join employee e on (kyc.emp_verified_by=e.id_employee)
                LEFT JOIN customer c ON (c.id_customer=kyc.id_customer) WHERE   (date(kyc.date_add) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($status!=4 ? " and kyc.status=".$status."":'')."");
          }else if(type == 2)
          {
              $sql =("SELECT id_kyc,ag.id_agent as cus, IF(kyc_type = 1, 'Bank Account', IF(kyc_type = 2, 'PAN Card', IF(kyc_type = 3, 'Aadhaar', ''))) as kyc_type,number,name,bank_ifsc ,bank_branch,IF(status = 0, 'Pending', IF(status = 1, 'In Progress', IF(status = 2, 'Verified', IF(status = 3, 'Rejected', '')))) as status ,IF(verification_type = 1, 'Manual',IF(verification_type = 2,'Auto', '')) as verification_type,last_update,kyc.date_add,Date_format(kyc.dob, '%d-%m-%Y') as dob,
                Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_verified_by,
                kyc.emp_verified_by as id_employee ,ag.firstname as id_customer
                FROM `kyc` kyc
                Left Join employee e on (kyc.emp_verified_by=e.id_employee)
                LEFT JOIN agent ag ON (ag.id_agent=kyc.id_agent) WHERE   (date(kyc.date_add) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($status!=4 ? " and kyc.status=".$status."":'')."");
          }
           return $this->db->query($sql)->result_array();
     }
         function get_kycdata($status,$type)
    {
        if($type == 1)
        {
            $sql=$this->db->query("SELECT id_kyc,c.id_customer as cus, IF(kyc_type = 1, 'Bank Account', IF(kyc_type = 2, 'PAN Card', IF(kyc_type = 3, 'Aadhaar', ''))) as kyc_type,number,name,bank_ifsc ,bank_branch,IF(status = 0, 'Pending', IF(status = 1, 'In Progress', IF(status = 2, 'Verified', IF(status = 3, 'Rejected', '')))) as status ,IF(verification_type = 1, 'Manual',IF(verification_type = 2,'Auto', '')) as verification_type,last_update,kyc.date_add,Date_format(kyc.dob, '%d-%m-%Y') as dob,
            Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_verified_by,
            kyc.emp_verified_by as id_employee ,c.firstname as id_customer,c.mobile,
                kyc.img_url,kyc.added_by
            FROM `kyc` kyc
            Left Join employee e on (kyc.emp_verified_by=e.id_employee)
            LEFT JOIN customer c ON (c.id_customer=kyc.id_customer)
            ".($status!='4'?"  WHERE kyc.id_customer is not null and kyc.id_customer != 0 and kyc.status =".$status :"  ")." WHERE kyc.id_customer is not null and kyc.id_customer != 0");
        }else if($type == 2)
        {
                $sql=$this->db->query("SELECT id_kyc,ag.id_agent as cus, IF(kyc_type = 1, 'Bank Account', IF(kyc_type = 2, 'PAN Card', IF(kyc_type = 3, 'Aadhaar', ''))) as kyc_type,number,name,bank_ifsc ,bank_branch,IF(status = 0, 'Pending', IF(status = 1, 'In Progress', IF(status = 2, 'Verified', IF(status = 3, 'Rejected', '')))) as status ,IF(verification_type = 1, 'Manual',IF(verification_type = 2,'Auto', '')) as verification_type,last_update,kyc.date_add,Date_format(kyc.dob, '%d-%m-%Y') as dob,
            Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_verified_by,
            kyc.emp_verified_by as id_employee ,ag.firstname as id_customer
            FROM `kyc` kyc
            Left Join employee e on (kyc.emp_verified_by=e.id_employee)
            LEFT JOIN agent ag ON (ag.id_agent=kyc.id_agent)
            ".($status!='4'?"  WHERE kyc.id_agent is not null and kyc.id_agent != 0 and kyc.status =".$status :"  ")."  WHERE kyc.id_agent is not null and kyc.id_agent != 0");
        }
        return $sql->result_array();
    }*/
	function get_kycdata_range($from_date, $to_date, $status, $type=1, $list_type=1, $id_customer="", $mobile="")
    {
          if($type == 1 || $type == 0 || $list_type == 1)
          {
		   $sql =("SELECT id_kyc,c.id_customer as cus, km.name as kyc_type,kyc.number,kyc.name,kyc.bank_ifsc ,kyc.bank_branch,IF(kyc.status = 0, 'Pending', IF(kyc.status = 1, 'In Progress', IF(kyc.status = 2, 'Verified', IF(kyc.status = 3, 'Rejected', '')))) as status ,IF(verification_type = 1, 'Manual',IF(verification_type = 2,'Auto', '')) as verification_type,last_update,kyc.date_add,Date_format(kyc.dob, '%d-%m-%Y') as dob,
        	    Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_verified_by,
        	    kyc.emp_verified_by as id_employee ,c.firstname as id_customer, c.mobile, kyc.img_url, kyc.back_img_url, kyc.document_url, kyc.added_by
        	    FROM `kyc` kyc
        	    LEFT JOIN kyc_master km ON (km.id_mas_kyc=kyc.kyc_type)
        	    Left Join employee e on (kyc.emp_verified_by=e.id_employee)
        	    LEFT JOIN customer c ON (c.id_customer=kyc.id_customer) WHERE   (date(kyc.date_add) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($status!=4 ? " and kyc.status=".$status."":'').($id_customer!='' && $id_customer!=0 ? " and kyc.id_customer=".$id_customer."":'').($mobile!='' ? " and c.mobile like '%".$mobile."%'":'')."");
          }else if($type == 2 || $list_type == 2)
          {
              $sql =("SELECT id_kyc,ag.id_agent as cus, km.name as kyc_type,kyc.number,kyc.name,kyc.bank_ifsc ,kyc.bank_branch,IF(kyc.status = 0, 'Pending', IF(kyc.status = 1, 'In Progress', IF(kyc.status = 2, 'Verified', IF(kyc.status = 3, 'Rejected', '')))) as status ,IF(verification_type = 1, 'Manual',IF(verification_type = 2,'Auto', '')) as verification_type,last_update,kyc.date_add,Date_format(kyc.dob, '%d-%m-%Y') as dob,
        	    Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_verified_by,
        	    kyc.emp_verified_by as id_employee ,ag.firstname as id_customer, ag.mobile, kyc.img_url, kyc.back_img_url, kyc.document_url, kyc.added_by
        	    FROM `kyc` kyc
        	    LEFT JOIN kyc_master km ON (km.id_mas_kyc=kyc.kyc_type)
        	    Left Join employee e on (kyc.emp_verified_by=e.id_employee)
        	    LEFT JOIN agent ag ON (ag.id_agent=kyc.id_agent) WHERE   (date(kyc.date_add) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($status!=4 ? " and kyc.status=".$status."":'').($mobile!='' ? " and ag.mobile like '%".$mobile."%'":'')."");
          }
        //   print_r($sql);exit;
	       return $this->db->query($sql)->result_array();
     }
     	function get_kycdata($status, $type=1, $mobile="")
	{
        if ($type == 1 || $type == 0)
        {
    	    $sql=$this->db->query("SELECT id_kyc,c.id_customer as cus, km.name as kyc_type,kyc.number,kyc.name,kyc.bank_ifsc ,kyc.bank_branch,IF(kyc.status = 0, 'Pending', IF(kyc.status = 1, 'In Progress', IF(kyc.status = 2, 'Verified', IF(kyc.status = 3, 'Rejected', '')))) as status ,IF(verification_type = 1, 'Manual',IF(verification_type = 2,'Auto', '')) as verification_type,last_update,kyc.date_add,Date_format(kyc.dob, '%d-%m-%Y') as dob,
    	    Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_verified_by,
    	    kyc.emp_verified_by as id_employee ,c.firstname as id_customer, c.mobile, kyc.img_url, kyc.back_img_url, kyc.document_url
    	    FROM `kyc` kyc
    	    LEFT JOIN kyc_master km ON (km.id_mas_kyc=kyc.kyc_type)
    	    Left Join employee e on (kyc.emp_verified_by=e.id_employee)
    	    LEFT JOIN customer c ON (c.id_customer=kyc.id_customer)
    	    ".($status!='4'?"  WHERE kyc.id_customer is not null and kyc.id_customer != 0 and kyc.status =".$status :"  WHERE kyc.id_customer is not null and kyc.id_customer != 0").($mobile!='' ? " and c.mobile like '%".$mobile."%'":''));
        }else if($type == 2)
        {
                $sql=$this->db->query("SELECT id_kyc,ag.id_agent as cus, km.name as kyc_type,kyc.number,kyc.name,kyc.bank_ifsc ,kyc.bank_branch,IF(kyc.status = 0, 'Pending', IF(kyc.status = 1, 'In Progress', IF(kyc.status = 2, 'Verified', IF(kyc.status = 3, 'Rejected', '')))) as status ,IF(verification_type = 1, 'Manual',IF(verification_type = 2,'Auto', '')) as verification_type,last_update,kyc.date_add,Date_format(kyc.dob, '%d-%m-%Y') as dob,
    	    Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_verified_by,
    	    kyc.emp_verified_by as id_employee ,ag.firstname as id_customer, ag.mobile, kyc.img_url, kyc.back_img_url, kyc.document_url
    	    FROM `kyc` kyc
    	    LEFT JOIN kyc_master km ON (km.id_mas_kyc=kyc.kyc_type)
    	    Left Join employee e on (kyc.emp_verified_by=e.id_employee)
    	    LEFT JOIN agent ag ON (ag.id_agent=kyc.id_agent)
    	    ".($status!='4'?"  WHERE kyc.id_agent is not null and kyc.id_agent != 0 and kyc.status =".$status :"  WHERE kyc.id_agent is not null and kyc.id_agent != 0").($mobile!='' ? " and ag.mobile like '%".$mobile."%'":''));
        }
		//print_r($this->db->last_query());exit;
		return $sql->result_array();
	}
    function updatekyc($data, $id, $id_cus)
    {
        $this->db->where('id_kyc', $id);
        $status = $this->db->update('kyc', $data);
        $sql = $this->db->query("SELECT * FROM `kyc` WHERE id_customer=" . $id_cus . " AND status=2");
        return array('status' => $status, 'verified_kycs' => $sql->num_rows());
    }
    function updatekyccus($data, $id)
    {
        $this->db->where('id_customer', $id);
        $res = $this->db->update('customer', $data);
    }
    function getkycdata_byid()
    {
        $id_kyc = $this->input->post('id_kyc');
        $sql = "select * from kyc where id_kyc=" . $id_kyc;
        $res = $this->db->query($sql);
        return $res->row_array();
    }
    function updateAgentkyc($data, $id, $id_cus)
    {
        $this->db->where('id_kyc', $id);
        $status = $this->db->update('kyc', $data);
        $sql = $this->db->query("SELECT * FROM `kyc` WHERE id_agent=" . $id_cus . " AND status=2");
        return array('status' => $status, 'verified_kycs' => $sql->num_rows());
    }
    function updatekycAgentStatus($data, $id)
    {
        $this->db->where('id_agent', $id);
        $res = $this->db->update('agent', $data);
    }
    //Kyc Approval Data status filter with date picker//	
    /*Functions for pay settled payments Begins */
    function updateSettledPayments($data, $txnid, $payuid)
    {
        $this->db->where('ref_trans_id', $txnid);
        $this->db->where('payu_id', $payuid);
        $status = $this->db->update(self::PAY_TABLE, $data);
        return $status;
    }
    function insertSettledPay($data)
    {
        $status = $this->db->insert('gateway_settled_payments', $data);
        return $status;
    }
    function settledTxnsToUpdate()
    {
        $data = $this->db->query('select * from gateway_settled_payments where  is_updated=0');
        return $data->result_array();
    }
    function updatePayuSettledTrans($data, $txnid, $gateway_id)
    {
        $this->db->where('txnid', $txnid);
        $this->db->where('gateway_id', $gateway_id);
        $status = $this->db->update('gateway_settled_payments', $data);
        return $status;
    }
    /*Functions for pay settled payments Ends*/
    //Plan 2 and Plan 3 Scheme Enquiry Data with date picker//HH
    function get_sch_enq_list()
    {
        $sql = ("SELECT sch_enquiry.id_sch_enquiry,mobile,c.title, if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as id_customer,sch_enquiry.intresred_amt,sch_enquiry.message,sch_enquiry.intrested_wgt, IFNULL(Date_format(enquiry_date,'%d-%m%-%Y'),'-') as enquiry_date FROM `sch_enquiry` 
            LEFT JOIN customer c on c.id_customer=sch_enquiry.id_customer");
        $this->load->database('default', true);
        // print_r($sql);exit;  
        return $this->db->query($sql)->result_array();
    }
    function get_sch_enq_list_by_date($from_date, $to_date)
    {
        $sql = ("SELECT sch_enquiry.id_sch_enquiry,mobile,c.title, if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as id_customer,sch_enquiry.intresred_amt,sch_enquiry.message,sch_enquiry.intrested_wgt, IFNULL(Date_format(enquiry_date,'%d-%m%-%Y'),'-') as enquiry_date FROM `sch_enquiry` 
            LEFT JOIN customer c on c.id_customer=sch_enquiry.id_customer where (date(sch_enquiry.enquiry_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')");
        $this->load->database('default', true);
        // print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    //Plan 2 and Plan 3 Scheme Enquiry Data with date picker//
    function get_metalrate_by_branch($id_branch, $id_metal, $id_purity, $date = "")
    {
        $today = date('Y-m-d H:i:s');
        $rate_field = '';
        if ($id_purity > 0) {
            $rf_sql = $this->db->query("SELECT rate_field,market_rate_field FROM `ret_metal_purity_rate` where id_metal=" . $id_metal . " and id_purity=" . $id_purity . "");
            if ($rf_sql->num_rows() > 0) {
                $rate_field = $rf_sql->row("rate_field");
            }
        }
        if (!empty($rate_field) && $id_purity > 0) {
            $rate_field = $rf_sql->row("rate_field");
        } else if ($id_metal > 0) {
            $rate_field = ($id_metal == 2 ? "silverrate_1gm" : "goldrate_22ct");
        }
        //   print_r($rate_field);exit;
        if ($rate_field != '') {
            $data = $this->get_settings();
            if ($data['is_branchwise_rate'] == 1 && $id_branch != '' && $id_branch != NULL) {
                $sql = "select " . $rate_field . " from metal_rates m
    	   		left join branch_rate br on m.id_metalrates=br.id_metalrate 
    	   		where br.id_branch=" . $id_branch . " order by  br.id_metalrate desc limit 1";
                //echo $sql;exit;
            } else if ($data['is_branchwise_rate'] == 1) {
                $sql = "select " . $rate_field . " from metal_rates 
    			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates 
    			where br.status=1";
            } else {
                if ($date != '') {
                    $date_obj = DateTime::createFromFormat('d-m-y', $date);
                    $formatted_date = $date_obj->format('Y-m-d');
                    $sql = "select " . $rate_field . " from metal_rates 
					left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates
					where date(add_date) = '" . $formatted_date . "'";
                    // print_r($sql);exit;
                } else {
                    $sql = "select " . $rate_field . " from metal_rates 
    			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates order by id_metalrates desc limit 1";
                }
            }
            $result = $this->db->query($sql);    //echo $sql;exit;
            if ($result->num_rows() > 0) {
                return $result->row($rate_field);
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }
    //Purchase Payment - Akshaya Thiruthiyai Spl updt//HH
    function ajax_get_customers_list($param)
    {
        $customers = $this->db->query("select c.id_purch_customer, IFNULL(c.mobile,'')as mobile,c.firstname
				from  purchase_customer c  
									where  (c.firstname LIKE '$param%'  OR c.mobile LIKE '$param%')");
        // print_r($this->db->last_query());exit;
        return $customers->result_array();
    }
    function ajax_get_purchase_payment($from_date, $to_date, $id_purch_customer)
    {
        $sql = "select id_purch_payment, c.firstname as name,c.mobile,c.id_purch_customer,if(p.type=1,'Amount','Weight')as type,
         IF(delivery_preference = 1, 'Ornament', IF(delivery_preference = 2, 'Coin', '')) as delivery_preference,
         p.payment_amount,if(p.type=2,p.metal_weight,'-') as metal_weight,psm.payment_status as payment_status,
         date_format(p.date_add,'%d-%m-%Y') as date_add,iFNULL(p.id_transaction,'-') as id_transaction,is_delivered,p.payment_status as pay_status,p.ref_trans_id
         from purchase_customer c
         left join purchase_payment p on p.mobile=c.mobile
         Left Join payment_status_message psm On p.payment_status=psm.id_status_msg
         Where (date(p.date_add) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')  
         " . ($id_purch_customer != '' ? " and c.id_purch_customer=" . $id_purch_customer . "" : '') . "
         order by p.id_purch_payment desc";
        // print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    //otp verify aftr upd pay Tabl when purchase - the jewel for AT special//
    function get_purchasecustomer($mobile)
    {
        $this->db->where('mobile', $mobile);
        $r = $this->db->get(self::PURCH_CUS_TABLE);
        //print_r($this->db->last_query());exit;
        if ($r->num_rows == 1) {
            $result = $r->row_array();
            return $result;
        } else {
            return array('status' => 2, 'msg' => 'Invalid');
        }
    }
    function get_purchase_pay($id_purch_payment)
    {
        $this->db->where('id_purch_payment', $id_purch_payment);
        $r = $this->db->get(self::PURCH_PAY_TABLE);
        // print_r($this->db->last_query());exit;
        if ($r->num_rows == 1) {
            $result = $r->row_array();
            return $result;
        } else {
            return array('status' => 2, 'msg' => 'Invalid');
        }
    }
    function add_remark($data, $id)
    {
        $this->db->where('id_purch_payment', $id);
        $status = $this->db->update(self::PURCH_PAY_TABLE, $data);
        //print_r($this->db->last_query());exit;
        return $status;
    }
    //otp verify aftr upd pay Tabl when purchase - the jewel for AT special//
    //Purchase Payment - Akshaya Thiruthiyai Spl updt//
    //closed A/C report with date picker, cost center based branch fillter//HH
    // closed acc report starts here
    function get_all_closed_account()
    {
        $accounts = $this->db->query("select
							  s.id_scheme_account,sc.code,IFNULL(s.group_code,'')as scheme_group_code,IFNULL(s.scheme_acc_number,'NOT Allocated')as scheme_acc_number,cs.has_lucky_draw,
							  concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,if(s.id_branch= 1, 'Pennadam', if(s.id_branch = 2, 'Thittakudi', if(s.id_branch = 3, 'Raamanaththam', ''))) as id_branch,
							  s.ref_no, s.closing_add_chgs, s.account_name,if(s.Closing_id_branch= 1, 'Pennadam', if(s.Closing_id_branch = 2, 'Thittakudi', if(s.Closing_id_branch = 3, 'Raamanaththam', ''))) as Closing_id_branch,
							  IFNULL(Date_format(s.start_date,'%d-%m%-%Y'),'-') as start_date,
							  IFNULL(Date_format(s.closing_date,'%d-%m%-%Y'),'-') as closing_date,
							  if(sc.scheme_type=0,s.closing_balance,s.closing_balance) as closing_balance,
					          e.firstname as employee_closed,
                                c.added_by,sc.scheme_name,sc.code,
							  sc.scheme_type as scheme_types,if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight',if(sc.scheme_type=2,'Amount to Weight',if(sc.flexible_sch_type=2,'Flexible Amount',IF(sc.flexible_sch_type = 3 , 'Flexible Weight','Flexible'))))) as scheme_type,
							  FORMAT(if(sc.scheme_type=1,CONCAT('max ',sc.max_weight,' g/month'),if(sc.scheme_type=3 && sc.max_amount!=0,sc.max_amount,if(sc.scheme_type=3 && sc.max_amount=0,(sc.max_weight*(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)),sc.amount))),2) as amount,sc.total_installments,sc.max_chance,c.mobile,
							  IF(sc.scheme_type=1,sc.max_weight,sc.amount) as total_payamt,sc.free_payment,
							  IF(sc.scheme_type=0 OR sc.scheme_type=2,sc.amount,IF(sc.scheme_type=1 ,sc.max_weight,if(sc.scheme_type=3,if(flexible_sch_type = 3 ,  sc.max_weight,if(cs.firstPayamt_as_payamt=1,s.firstPayment_amt ,sc.min_amount)),0))) as payable,
							  IFNULL(IF(s.is_opening=1,IFNULL(s.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 and sc.payment_chances=1) , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as paid_installments,
                            sum(p.payment_amount) as pay_amount,sum(p.act_amount) as act_amount,s.additional_benefits,s.closing_add_chgs,IFNULL(p.discountAmt,0)as discountAmt,s.closing_add_chgs
							from
							  " . self::ACC_TABLE . " s
                            left join employee e ON (e.id_employee = s.employee_closed) 
							left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
					    	left join " . self::PAY_TABLE . " p on (p.id_scheme_account=s.id_scheme_account)
							left join " . self::BRANCH . " b on (b.id_branch=s.id_branch)
							join chit_settings cs
							where s.active=0 and s.is_closed=1  group by s.id_scheme_account");
        // print_r($this->db->last_query());exit;
        return $accounts->result_array();
    }
    function get_all_closed_account_by_date($from_date, $to_date, $id_employee, $close_id_branch)
    {
        $id_scheme = $this->input->post('id_scheme');
        $account_type = $this->input->post('account_type');
        $id_company = $this->session->userdata('id_company');
        $company_settings = $this->session->userdata('company_settings');
        $sql = $this->db->query("select
        s.id_scheme_account,sc.code,IFNULL(s.group_code,'')as scheme_group_code,IFNULL(s.scheme_acc_number,'NOT Allocated')as scheme_acc_number,
		IFNULL(s.start_year,'') as start_year,
		 IFNULL((select IFNULL(br.name,'-') as acc_branch from branch br where br.id_branch = s.id_branch),'-') as acc_branch,
		   sc.is_lucky_draw,
		   IFNULL(s.group_code,'') as group_code,
		sc.code,
		cs.has_lucky_draw,
        concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,
        s.ref_no, s.closing_add_chgs, s.account_name,
        IFNULL(Date_format(s.start_date,'%d-%m%-%Y'),'-') as start_date,
        IFNULL(Date_format(s.closing_date,'%d-%m%-%Y %H:%i'),'-') as closing_date,
        if(sc.scheme_type=0,s.closing_balance,s.closing_balance) as closing_balance,
        e.firstname as employee_closed,
        c.added_by,sc.scheme_name,sc.code,
        cs.scheme_wise_acc_no,
        (select IFNULL(short_name,'') from branch where id_branch=s.id_branch) as branch_code,
        sc.scheme_type as scheme_types,if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight',if(sc.scheme_type=2,'Amount to Weight',if(sc.flexible_sch_type=2,'Flexible Amount',IF(sc.flexible_sch_type = 3 , 'Flexible Weight','Flexible'))))) as scheme_type,
        FORMAT(if(sc.scheme_type=1,CONCAT('max ',sc.max_weight,' g/month'),if(sc.scheme_type=3 && sc.max_amount!=0,sc.max_amount,if(sc.scheme_type=3 && sc.max_amount=0,(sc.max_weight*(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)),sc.amount))),2) as amount,sc.total_installments,sc.max_chance,c.mobile,
        IF(sc.scheme_type=1,sc.max_weight,sc.amount) as total_payamt,sc.free_payment,
        IF(sc.scheme_type=0 OR sc.scheme_type=2,sc.amount,IF(sc.scheme_type=1 ,sc.max_weight,if(sc.scheme_type=3,if(flexible_sch_type = 3 ,  sc.max_weight,if(sc.firstPayamt_as_payamt=1,s.firstPayment_amt ,sc.min_amount)),0))) as payable,
        IFNULL(IF(s.is_opening=1,IFNULL(s.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 and sc.payment_chances=1) , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as paid_installments,
        sum(p.payment_amount) as pay_amount,sum(p.act_amount) as act_amount,s.additional_benefits,s.closing_add_chgs,IFNULL(p.discountAmt,0)as discountAmt,s.closing_add_chgs,
        sc.firstPayDisc_value,s.closing_amount,b.name as closing_branch,IFNULL(s.closing_paid_amt,0) as closing_paid_amt,IFNULL(s.closing_benefits,0) as closing_benefits,
        IFNULL(s.balance_amount,0) as balance_amount,IFNULL(s.balance_weight,0) as balance_weight,
        IFNULL(bill_acc.bill_no,'') as bill_no,bill_acc.bill_id,
        IFNULL(s.closing_weight,0)as closing_weight,s.store_closing_balance_as,
		IFNULL(d.paid_installments,'-') as paid_installments,sc.total_installments,
		IFNULL((select concat(IFNULL(e.firstname,''),' ',IFNULL(e.lastname,''),'-',IFNULL(e.emp_code,'')) from employee e left join scheme_account ssa on ssa.referal_code=e.emp_code WHERE " . ($id_company != '' && $company_settings == 1 ? " c.id_company = e.id_company and" : '') . " ssa.id_scheme_account=s.id_scheme_account and ssa.referal_code is not null and ssa.referal_code!='' and ssa.is_refferal_by is not null and ssa.is_refferal_by=1),'-') as referred_employee
        from " . self::ACC_TABLE . " s
        left join employee e ON (e.id_employee = s.employee_closed) 
        left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
        left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
        left join " . self::PAY_TABLE . " p on (p.id_scheme_account=s.id_scheme_account)
		left join (select IFNULL(IF(s.is_opening=1,IFNULL(s.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 AND sc.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) as paid_installments,
		s.id_scheme_account
		from scheme_account s
		left join payment pay on s.id_scheme_account=pay.id_scheme_account 
		left join scheme sc on s.id_scheme=sc.id_scheme
		where pay.payment_status=1 and pay.id_scheme_account=s.id_scheme_account group by pay.id_scheme_account )d
		on d.id_scheme_account=s.id_scheme_account
        left join " . self::BRANCH . " b on (b.id_branch=s.Closing_id_branch)
        " . ($company_settings == 1 ? "left join  company compy on compy.id_company=c.id_company" : '') . "
        LEFT JOIN (SELECT chit.scheme_account_id,bill.bill_no,bill.bill_id
          FROM ret_billing_chit_utilization chit
          LEFT JOIN ret_billing bill ON bill.bill_id = chit.bill_id
          GROUP by chit.scheme_account_id) as bill_acc ON bill_acc.scheme_account_id = s.id_scheme_account 
        join chit_settings cs
        Where (s.active=0 and s.is_closed=1 and  date(s.closing_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') and p.payment_status=1
        " . ($close_id_branch != NULL ? ' and s.Closing_id_branch =' . $close_id_branch : '') . "
        " . ($id_employee != NULL ? "and s.employee_closed=" . $id_employee . "" : '') . " 
        " . ($id_scheme != NULL && $id_scheme != '' ? "and s.id_scheme=" . $id_scheme . "" : '') . "
		" . ($account_type != NULL && $account_type != '' && $account_type == 1 ? " and d.paid_installments<sc.total_installments" : '') . "
		" . ($account_type != NULL && $account_type != '' && $account_type == 2 ? " and d.paid_installments=sc.total_installments" : '') . "
		" . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . "
        group by s.id_scheme_account");
        $result = [];
        $payment = $sql->result_array();
        //print_r($this->db->last_query());exit;
        if ($sql->num_rows() > 0) {
            foreach ($payment as $rcpt) {
                $rcpt['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $rcpt['id_scheme_account']);
                //$rcpt['receipt_no'] = $this->customer_model->format_accRcptNo('Receipt',$rcpt['id_payment']);
                $result[] = $rcpt;
            }
        }
        foreach ($result as $r) {
            $return_data[$r['scheme_name']][] = $r;
        }
        return $return_data;
    }
    function get_closed_summary_by_date($from_date, $to_date, $id_employee, $close_id_branch)
    {
        $id_scheme = $this->input->post('id_scheme');
        $account_type = $this->input->post('account_type');
        $id_company = $this->session->userdata('id_company');
        $company_settings = $this->session->userdata('company_settings');
        $sql = "select s.scheme_name,
		IFNULL(count(sa.id_scheme_account),'-') as acc_count,
		IFNULL(sum(sa.closing_amount),0) as closing_amount,
		IFNULL(sum(sa.closing_weight),0) as closing_weight,
		d.paid_installments,s.total_installments
		from
		scheme_account sa 
		left join scheme s on s.id_scheme=sa.id_scheme
         left join " . self::CUS_TABLE . " c on (sa.id_customer=c.id_customer)
		left join (select IFNULL(IF(s.is_opening=1,IFNULL(s.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 AND sc.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) as paid_installments,
		s.id_scheme_account,sc.total_installments
		from scheme_account s
		left join payment pay on s.id_scheme_account=pay.id_scheme_account 
		left join scheme sc on s.id_scheme=sc.id_scheme
		where pay.payment_status=1 and pay.id_scheme_account=s.id_scheme_account group by pay.id_scheme_account )d
		on d.id_scheme_account=sa.id_scheme_account
		Where (sa.active=0 and sa.is_closed=1 
		and  date(sa.closing_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') 
        " . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . "
        " . ($close_id_branch != NULL ? ' and sa.Closing_id_branch =' . $close_id_branch : '') . "
        " . ($id_employee != NULL ? "and sa.employee_closed=" . $id_employee . "" : '') . "
        " . ($id_scheme != NULL && $id_scheme != '' ? "and sa.id_scheme=" . $id_scheme . "" : '') . "
		" . ($account_type != NULL && $account_type != '' && $account_type == 1 ? " and d.paid_installments < s.total_installments" : '') . "
		" . ($account_type != NULL && $account_type != '' && $account_type == 2 ? " and d.paid_installments = s.total_installments" : '') . "
		" . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . "
		group by s.id_scheme
		order by s.id_scheme";
        //print_r($sql);exit;
        $summary = $this->db->query($sql);
        return $summary->result_array();
    }
    // closed acc report ends here
    //closed A/C report with date picker, cost center based branch fillter//
    //Scheme wise pending report// 
    function get_scheme_wise_pending($from_date, $to_date, $id_branch, $id_scheme)
    {
        $return_data = array('balance_details' => array(), 'closed_details' => array(), 'chit_details' => array());
        $closed_details = $this->db->query("SELECT sa.id_scheme_account,date_format(sa.start_date,'%d-%m-%y') as start_date,sa.closing_balance,sa.closing_add_chgs,
	     sa.closing_amount,concat(s.code,' ',sa.scheme_acc_number) as scheme_acc_number,
	     date_format(sa.closing_date,'%d-%m-%y') as closing_date,concat(c.firstname,' ',ifnull(c.lastname,''))as cus_name,c.mobile,b.name as branch_name,s.scheme_type,
	     (if(sa.balance_amount IS NULL,0,sa.balance_amount))+if(sum(p.payment_amount) is null,0,sum(p.payment_amount)) as total_paid,sa.additional_benefits,s.firstPayDisc_value,s.allpay_disc_value,
	     s.discount_type,s.total_installments,s.free_payment,s.amount,
	     IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
        as paid_installments
	     from scheme_account sa
	     left join payment p on p.id_scheme_account=sa.id_scheme_account
	     left join scheme s on s.id_scheme=sa.id_scheme
	     left join customer c on c.id_customer=sa.id_customer
	     left join branch b on b.id_branch=p.id_branch
	     where sa.is_closed=1 " . ($id_scheme != '' ? " and sa.id_scheme=" . $id_scheme . "" : '') . " and (date(sa.closing_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
	     and p.payment_mode!='FP' " . ($id_branch != '' && $id_branch != 0 ? " and p.id_branch=" . $id_branch . "" : '') . " GROUP by p.id_scheme_account");
        //print_r($this->db->last_query());exit;
        $return_data['closed_details'] = $closed_details->result_array();
        $chit_details = $this->db->query("SELECT sa.id_scheme_account,date_format(sa.start_date,'%d-%m-%y') as start_date,sa.closing_balance,sa.closing_add_chgs,
	     sa.closing_amount,concat(s.code,' ',sa.scheme_acc_number) as scheme_acc_number,
	     date_format(sa.closing_date,'%d-%m-%y') as closing_date,concat(c.firstname,' ',ifnull(c.lastname,''))as cus_name,c.mobile,b.name as branch_name,sa.is_closed,
	     (if(sa.balance_amount IS NULL,0,sa.balance_amount))+if(sum(p.payment_amount) is null,0,sum(p.payment_amount)) as total_paid,
	     sa.additional_benefits,s.firstPayDisc_value,s.allpay_disc_value,
	     s.discount_type,s.total_installments,s.free_payment,s.amount,
	     IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
         as paid_installments
	     from scheme_account sa
	     left join payment p on p.id_scheme_account=sa.id_scheme_account
	     left join scheme s on s.id_scheme=sa.id_scheme
	     left join customer c on c.id_customer=sa.id_customer
	     left join branch b on b.id_branch=p.id_branch
	     where (date(p.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
	     " . ($id_scheme != '' ? " and sa.id_scheme=" . $id_scheme . "" : '') . " and p.payment_mode!='FP' " . ($id_branch != '' && $id_branch != 0 ? " and p.id_branch=" . $id_branch . "" : '') . " GROUP by p.id_scheme_account");
        $return_data['chit_details'] = $chit_details->result_array();
        $balance_details = $this->db->query("SELECT sa.id_scheme_account,date_format(sa.start_date,'%d-%m-%y') as start_date,sa.closing_balance,sa.closing_add_chgs,
	     sa.closing_amount,concat(s.code,' ',sa.scheme_acc_number) as scheme_acc_number,
	     date_format(sa.closing_date,'%d-%m-%y') as closing_date,concat(c.firstname,' ',ifnull(c.lastname,''))as cus_name,c.mobile,b.name as branch_name,sa.is_closed,
	     (if(sa.balance_amount IS NULL,0,sa.balance_amount))+if(sum(p.payment_amount) is null,0,sum(p.payment_amount)) as total_paid,
	     sa.additional_benefits,s.firstPayDisc_value,s.allpay_disc_value,
	     s.discount_type,s.total_installments,s.free_payment,s.amount,
	     IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
        as paid_installments
	     from scheme_account sa
	     left join payment p on p.id_scheme_account=sa.id_scheme_account
	     left join scheme s on s.id_scheme=sa.id_scheme
	     left join customer c on c.id_customer=sa.id_customer
	     left join branch b on b.id_branch=p.id_branch
	     where (date(sa.start_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
	     " . ($id_scheme != '' ? " and sa.id_scheme=" . $id_scheme . "" : '') . " and p.payment_mode!='FP' " . ($id_branch != '' && $id_branch != 0 ? " and p.id_branch=" . $id_branch . "" : '') . " GROUP by p.id_scheme_account");
        //print_r($this->db->last_query());exit;
        $return_data['balance_details'] = $balance_details->result_array();
        return $return_data;
    }
    //Get sch classify name//HH
    function get_classify_list()
    {
        $sql = "SELECT sch_classify.id_classification,sch_classify.classification_name FROM `sch_classify` where sch_classify.active=1";
        return $this->db->query($sql)->result_array();
    }
    //Get sch classify name//
    function paymentcancel_list_range($from_date, $to_date)
    {
        $branch_settings = $this->session->userdata('branch_settings');
        $is_branchwise_cus_reg = $this->session->userdata('is_branchwise_cus_reg');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $id_employee = $this->input->post('id_employee');
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = '';
        }
        $sql = "SELECT
					  p.id_payment,p.is_offline,sa.id_branch,sa.ref_no,sa.id_scheme_account,p.id_branch as pay_branch,
					  cs.has_lucky_draw,IF(p.payment_status = 4, 'Canceled', '') as payment_status,p.id_employee,
					  sa.account_name,p.act_amount,if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee,
					  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
					  c.mobile,
					   IFNULL(sa.group_code,'') as scheme_group_code,
					  IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,p.due_type,
					  s.code,
					  p.id_employee,IFNULL(e.emp_code,'-')as emp_code,
                      if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee, 
					  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
					  IFNULL(p.payment_amount,'-') as payment_amount,
					  p.metal_rate,
					  IFNULL(p.metal_weight, '-') as metal_weight,
					  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
					  (select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or s.scheme_type=3, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=p.id_scheme_account group by pay.id_scheme_account)
					as paid_installments,
			          p.payment_type,p.is_print_taken,
					  p.payment_mode as payment_mode,p.approval_date,
					  IFNULL(sa.scheme_acc_number,'') as msno,
					  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
					  IFNULL(p.bank_name,'-')as bank_name,
					  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
					  IFNULL(p.bank_branch,'-') as bank_branch,
					  IFNULL(p.id_transaction,'-') as id_transaction,
					  IFNULL(p.payu_id,'-') as payu_id ,
					  IFNULL(p.card_no,'-') as card_no,
					  psm.payment_status as payment_status,
					  p.payment_status as id_status,
					  psm.color as status_color,
					  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
					  IFNULL(p.remark,'-') as remark,
					  if(cs.receipt_no_set=1 && p.receipt_no is null,'',p.receipt_no) as receipt_no,
					 IFNULL(cs.receipt_no_set,'-') as receipt_no_set, IFNULL(Date_format(p.custom_entry_date,'%d-%m%-%Y'),'-') as entry_Date,cs.edit_custom_entry_date
				FROM payment p
				 join  chit_settings cs
				left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
				Left Join employee e On (e.id_employee=p.id_employee)
				Left Join customer c on (sa.id_customer=c.id_customer)
				left join scheme s on(sa.id_scheme=s.id_scheme)
				 Left Join branch b on (sa.id_branch=b.id_branch)
			    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)		
			    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
       Where p.payment_status=4 " . ($id_branch != '' && $id_branch > 0 ? " and b.id_branch=" . $id_branch . "" : '') . " and (date(p.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') " . ($id_employee != NULL || $id_employee != '' ? ' and p.id_employee =' . $id_employee : '') . " ";
        //print_r($sql);exit;
        $payment = $this->db->query($sql);
        return $payment->result_array();
    }
    function get_cancel_payment()
    {
        $sql = ("SELECT
					cs.has_lucky_draw,s.code,IFNULL(sa.group_code,'')as scheme_group_code,IF(p.payment_status = 4, 'Canceled', '') as payment_status,
					  p.id_payment,p.is_offline,sa.id_branch,sa.ref_no,sa.id_scheme_account as id_scheme_account,
					  sa.account_name,p.payment_amount,p.id_employee,if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee,
					  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,c.lastname,c.firstname,
					  c.mobile,c.email,
					  IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,p.due_type,p.act_amount,
					  s.code,s.scheme_name,
					  p.id_employee,IFNULL(e.emp_code,'-')as emp_code,
                      if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee,IFNULL(e.emp_code,'-')as emp_code,
					  if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
					  IFNULL(p.payment_amount,'-') as payment_amount,
					  IFNULL(if(p.metal_rate=0,'-',p.metal_rate), '-') as metal_rate,
					  IFNULL(if(p.metal_weight=0,'-',p.metal_weight), '-') as metal_weight,
					  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
			          p.payment_type,
					  IFNULL(p.payment_mode,'-') as payment_mode,p.approval_date,
					  IFNULL(sa.scheme_acc_number,'') as msno,
					  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
					  IFNULL(p.bank_name,'-')as bank_name,
					  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
					  IFNULL(p.bank_branch,'-') as bank_branch,
					  IFNULL(cs.receipt_no_set,'-') as receipt_no_set,
					  IFNULL(p.id_transaction,'-') as id_transaction,
					  IFNULL(p.payu_id,'-') as payu_id ,
					  IFNULL(p.card_no,'-') as card_no,
					   psm.payment_status as payment_status,
					  p.payment_status as id_status,
					  psm.color as status_color,
					  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
					if(cs.receipt_no_set=1 && p.receipt_no is null,'',p.receipt_no) as receipt_no,
					  IFNULL(p.remark,'-') as remark,cs.currency_name,cs.currency_symbol
				FROM payment p
				join chit_settings cs
				left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
				Left Join employee e On (e.id_employee=p.id_employee)
				Left Join customer c on (sa.id_customer=c.id_customer)
				left join scheme s on(sa.id_scheme=s.id_scheme)
			    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)		
			    Left Join branch b on (sa.id_branch=b.id_branch)
			    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
			     Where p.payment_status=4");
        //print_r($sql);exit;
        $payment = $this->db->query($sql);
        return $payment->result_array();
    }
    function get_branchwise_emp($branch)
    {
        $branch = $this->session->userdata('id_branch');
        //$sql="select  concat(emp.firstname,' ',emp.lastname)as  employee_name,emp.id_employee,emp.login_branches from employee emp ".($branch!=0 ?" where emp.login_branches=".$branch."" :'')."";
        $sql = "select  concat(emp.firstname,' ',emp.lastname)as  employee_name,emp.id_employee,emp.login_branches from employee emp";
        //print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    /*	function get_branchwise_emp($branch) 
    {
        $branch=$this->session->userdata('id_branch');
        if($branch !='' && $branch !=0)
        {
        $sql="select  concat(emp.firstname,' ',emp.lastname)as  employee_name,emp.id_employee,emp.login_branches from employee emp where emp.login_branches=".$branch."";
        //print_r($sql);exit;
        }
        else
        {
            $sql="select  concat(emp.firstname,' ',emp.lastname)as  employee_name,emp.id_employee from employee emp";
        //print_r($sql);exit;
        }
        return $sql->result_array();		
    }*/
    function get_customer_account_details($from_date, $to_date)
    {
        $return_data = array();
        $sql = $this->db->query("SELECT
        c.id_customer,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,c.mobile,
        s.total_installments,s.code,sa.id_scheme_account,IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
        IFNULL(sa.account_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,					
        if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
        if(s.scheme_type=0,s.amount,if(s.scheme_type=1,'-',s.amount)) as amount,
        if(s.scheme_type=1,s.max_weight,'-') as Max_weight,
        Date_Format(sa.start_date,'%d-%m-%Y') as start_date,
        IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,s.min_amount,0))) as payable,
        IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or s.scheme_type=3 , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
        as paid_installments,IFNULL(if(Date_Format(max(p.date_add),'%Y%m') = Date_Format(curdate(),'%Y%m'), (select SUM(ip.no_of_dues) from payment ip where Date_Format(ip.date_add,'%Y%m') = Date_Format(curdate(),'%Y%m') and sa.id_scheme_account = ip.id_scheme_account),IF(sa.is_opening=1, if(Date_Format(sa.last_paid_date,'%Y%m') = Date_Format(curdate(),'%Y%m'), 1,0),0)),0) as currentmonthpaycount,
        p.payment_status,
        IFNULL(Date_Format(max(p.date_add),'%d-%m-%Y'),IFNULL(IF(sa.is_opening=1,Date_Format(sa.last_paid_date,'%d-%m-%Y'),'')  ,0)) as last_paid_date,IF(sa.is_closed=0,Date_Format(DATE_ADD(max(p.date_add), INTERVAL 31 DAY),'%d-%m-%Y'),'-')as next_due_date,
        if(sa.is_closed=1,date_format(sa.closing_date,'%d-%m-%Y'),'') as closing_date,sa.active,sa.is_closed,cus.tot_acc,TIMESTAMPDIFF(month, max(p.date_add), current_date()) as month_ago,
        ifnull(acctive_acc.tot_acc,0) as active_acc
        FROM scheme_account sa
        LEFT JOIN scheme s On (sa.id_scheme=s.id_scheme)
        LEFT JOIN branch b on (b.id_branch=sa.id_branch)
        LEFT JOIN (select c.id_customer,count(sa.id_scheme_account) as tot_acc From customer c left join scheme_account sa on sa.id_customer=c.id_customer where sa.is_closed=0 group by sa.id_customer) as acctive_acc on acctive_acc.id_customer=sa.id_customer
        LEFT JOIN (select c.id_customer,count(sa.id_scheme_account) as tot_acc From customer c left join scheme_account sa on sa.id_customer=c.id_customer group by sa.id_customer) as cus on cus.id_customer=sa.id_customer
        LEFT JOIN payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=2 or p.payment_status=1))
        LEFT JOIN customer c On (sa.id_customer=c.id_customer and c.active=1)
        WHERE  (date(sa.start_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
        " . ($_POST['id_scheme'] != '' ? " and sa.id_scheme=" . $_POST['id_scheme'] . "" : '') . "
        " . ($_POST['id_branch'] != '' && $_POST['id_branch'] > 0 ? " and sa.id_branch=" . $_POST['id_branch'] . "" : '') . "
        GROUP BY sa.id_scheme_account order by sa.id_customer DESC");
        //print_r($this->db->last_query());exit;
        $accounts = $sql->result_array();
        foreach ($accounts as $acc) {
            if ($acc['paid_installments'] > 0) {
                $return_data[] = $acc;
            }
        }
        return $return_data;
    }
    function get_active_scheme()
    {
        $sql = $this->db->query("SELECT id_scheme,scheme_name FROM scheme order by id_scheme DESC");
        return $sql->result_array();
    }
    function get_opening_blc_details($id_scheme, $date, $id_branch)
    {
        $op_date = date('Y-m-d', (strtotime('-1 day', strtotime($date))));
        $sql = $this->db->query("SELECT IFNULL(SUM(s.today_collection_amt),0) as today_collection_amt,IFNULL(SUM(s.today_collection_wgt),0) as today_collection_wgt,IFNULL(SUM(s.today_bonus_amt),0) as today_bonus_amt,IFNULL(SUM(s.closing_balance_amt),0) as closing_balance_amt,IFNULL(SUM(s.closing_balance_wgt),0) as closing_balance_wgt,
        IFNULL(SUM(s.closing_bonus_amt),0) as closing_bonus_amt
        FROM daily_collection_scheme_wise s
        where s.date='" . $op_date . "' and s.id_scheme=" . $id_scheme . " " . ($id_branch != '' && $id_branch > 0 ? " and s.id_branch=" . $id_branch . "" : '') . " ");
        //print_r($this->db->last_query());exit;
        return $sql->row_array();
    }
    /*	function get_today_collection_details($from_date,$to_date,$id_scheme,$id_branch)
    {
        $return_data=array();
        $sql=$this->db->query("select IFNULL(SUM(p.payment_amount-IFNULL(s.firstPayDisc_value,0)),0) as today_collection_amt,IFNULL(SUM(s.firstPayDisc_value),0) as today_bonus_amt,
        IFNULL(SUM(p.metal_weight),0) as today_collection_wgt,s.scheme_name
        FROM payment p
        join company compy 
        left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
        Left Join branch b On (b.id_branch=p.id_branch) 
        left join scheme s on(sa.id_scheme=s.id_scheme) 
        Left Join payment_mode pm on (p.payment_mode=pm.id_mode)	
        left join sch_classify sch on(s.id_classification=sch.id_classification)
        Where (date(p.date_payment) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') And p.payment_status=1
        ".($id_scheme!='' && $id_scheme>0 ? " and sa.id_scheme=".$id_scheme."" :'')."
        ".($id_branch!='' && $id_branch>0 ? " and p.id_branch=".$id_branch."" :'')."
        ");
        //print_r($this->db->last_query());exit;
        $return_data['collection']=$sql->row_array();
        $closing=$this->db->query("SELECT IFNULL(sa.closing_amount,0) as closing_amount,IFNULL(sa.closing_weight,0) as closing_weight,IFNULL(sa.closing_balance,0) as closing_balance,IFNULL(sa.closing_add_chgs,0) as closing_add_chgs ,s.firstPayDisc_value,pay.paid_installments,sa.id_scheme_account,s.total_installments,
        s.scheme_type,s.scheme_name
        FROM scheme_account sa 
        LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme
        LEFT JOIN (select sa.id_scheme_account,IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as paid_installments
        FROM payment p
        left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
        left join scheme s on s.id_scheme=sa.id_scheme
        WHERE p.payment_status=1
        GROUP BY sa.id_scheme_account) as pay ON pay.id_scheme_account=sa.id_scheme_account
        WHERE sa.is_closed=1 AND (date(sa.closing_date) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."')
        ".($id_scheme!='' && $id_scheme>0 ? " and sa.id_scheme=".$id_scheme."" :'')."
        ".($id_branch!='' && $id_branch>0 ? " and sa.Closing_id_branch=".$id_branch."" :'')."
        GROUP BY sa.id_scheme_account");
        //print_r($this->db->last_query());exit;
        $closing_deails=$closing->result_array();
        $bonus_deduction=0;
        $closing_weight=0;
        $closing_amount=0;
        if(sizeof($closing_deails)>0)
        {
             foreach($closing_deails as $clc)
            {   
                if($clc['total_installments']!=$clc['paid_installments'])
                {
                    $bonus_deduction+=($clc['paid_installments']*$clc['firstPayDisc_value']);
                    $closing_weight+=$clc['closing_weight'];
                }
                if($clc['scheme_type']==0)
                {
                    $closing_amount+=$clc['closing_balance'];
                }
                else if($clc['scheme_type']==2 || $clc['scheme_type']==3)
                {
                    $closing_weight+=$clc['closing_balance'];
                    $closing_amount+=$clc['closing_amount'];
                }
            }
        }
        $return_data['closed']=array(
                                    'today_closing_amount'=>$closing_amount,
                                    'today_closing_weight'=>$closing_weight,
                                    'today_bonus_detuction'=>$bonus_deduction,
                                    );
        return $return_data;
    }*/
    //Online Payment Report
    function get_online_payment_report_date($data)
    {
        $responseData = array();
        $limit = '';
        $from_date = !empty($data['from_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $data['from_date']))) : date('Y-m-d');
        $to_date = !empty($data['to_date']) ? date('Y-m-d', strtotime(str_replace('/', '-', $data['to_date']))) : date('Y-m-d');
        $id_branch = isset($data['id_branch']) ? $data['id_branch'] : '';
        $id_status_msg = isset($data['id_status_msg']) ? $data['id_status_msg'] : '';
        $sql = "SELECT
        p.id_payment,p.is_offline,sa.id_branch,sa.ref_no,sa.id_scheme_account,p.id_branch as pay_branch,
        cs.has_lucky_draw,
        sa.account_name,p.act_amount,
        if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,p.added_by,
        c.mobile,
        IFNULL(sa.group_code,'') as scheme_group_code,
        IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,p.due_type,
        s.code,b.name as payment_branch,
        p.id_employee,IFNULL(e.emp_code,'-')as emp_code,
        if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee, 
        if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight')) as scheme_type,
        IFNULL(p.payment_amount,'-') as payment_amount,
        p.metal_rate,
        IFNULL(p.metal_weight, '-') as metal_weight,
        IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
        (select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or s.scheme_type=3, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=p.id_scheme_account group by pay.id_scheme_account)
        as paid_installments,
        p.payment_type,p.is_print_taken,
        p.payment_mode as payment_mode,
        IFNULL(sa.scheme_acc_number,'') as msno,
        IFNULL(p.bank_acc_no,'-') as bank_acc_no,
        IFNULL(p.bank_name,'-')as bank_name,
        IFNULL(p.bank_IFSC,'-') as bank_IFSC,
        IFNULL(p.bank_branch,'-') as bank_branch,
        IFNULL(p.id_transaction,'-') as id_transaction,
        IFNULL(p.payu_id,'-') as payu_id ,
        IFNULL(p.card_no,'-') as card_no,
        psm.payment_status as payment_status,
        p.payment_status as id_status,
        psm.color as status_color,
        IFNULL(p.payment_ref_number,'-') as payment_ref_number,
        IFNULL(p.remark,'-') as remark,
        if(cs.receipt_no_set=1 && p.receipt_no is null,'',p.receipt_no) as receipt_no,
        IFNULL(cs.receipt_no_set,'-') as receipt_no_set, IFNULL(Date_format(p.custom_entry_date,'%d-%m%-%Y'),'-') as entry_Date,cs.edit_custom_entry_date,
        IFNULL(b.name,'') as branch_name,if(s.discount=1,s.firstPayDisc_value,0) as discountAmt
        FROM payment p
        join  chit_settings cs
        left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
        Left Join employee e On (e.id_employee=p.id_employee)
        Left Join customer c on (sa.id_customer=c.id_customer)
        left join scheme s on(sa.id_scheme=s.id_scheme)
        Left Join branch b on (p.id_branch=b.id_branch)
        Left Join payment_mode pm on (p.payment_mode=pm.id_mode)		
        Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
        Where (date(p.date_payment) BETWEEN '" . $from_date . "' AND '" . $to_date . "') 
        and (p.added_by = 1 or p.added_by = 2)
        " . ($id_branch != '' && $id_branch > 0 ? " and p.id_branch=" . $id_branch . "" : '') . "
        " . ($id_status_msg != '' ? " and p.payment_status=" . $id_status_msg . "" : '') . "
        ORDER BY p.id_branch,p.id_payment DESC " . ($limit != NULL ? " LIMIT " . $limit . " OFFSET " . $limit : " ");
        $result = $this->db->query($sql)->result_array();
        foreach ($result as $r) {
            if ($r['branch_name'] != '') {
                $responseData[$r['branch_name']][] = $r;
            }
        }
        return $responseData;
    }
    //Online Payment Report
    //Chit Deposit
    function get_EstimationDetails($data)
    {
        $response_data = array();
        $entry_date = date("Y-m-d H:i:s");
        if ($data['id_branch'] != '' && $data['id_branch'] != null) {
            $dCData = $this->getBranchDayClosingData($data['id_branch']);
            $entry_date = $dCData['entry_date'];
        }
        $sql = $this->db->query("SELECT e.esti_no,IFNULL(SUM(s.amount),0) as tot_amt,IFNULL(SUM(s.net_wt),0) as total_wt,e.estimation_id
            FROM ret_estimation e 
            LEFT JOIN ret_estimation_old_metal_sale_details s ON s.est_id=e.estimation_id
            WHERE e.esti_no=" . $data['est_no'] . " and s.purchase_status=1 AND date(e.estimation_datetime)='" . $entry_date . "' 
            " . ($data['id_branch'] != '' ? " and e.id_branch=" . $data['id_branch'] . "" : '') . "");
        // print_r($this->db->last_query());exit;
        if ($sql->row()->tot_amt > 0 && $sql->row()->total_wt > 0) {
            $response_data = array('status' => TRUE, 'total_amt' => $sql->row()->tot_amt, 'total_weight' => $sql->row()->total_wt, 'estimation_id' => $sql->row()->estimation_id, 'esti_no' => $sql->row()->esti_no);
        } else {
            $response_data = array('staus' => FALSE, 'message' => 'No Record Found');
        }
        return $response_data;
    }
    function getBranchDayClosingData($id_branch)
    {
        $sql = $this->db->query("SELECT id_branch,is_day_closed,entry_date from ret_day_closing where id_branch=" . $id_branch);
        return $sql->row_array();
    }
    public function update_data($data, $id_field, $id_value, $table)
    {
        $edit_flag = 0;
        $this->db->where($id_field, $id_value);
        $edit_flag = $this->db->update($table, $data);
        return ($edit_flag == 1 ? $id_value : 0);
    }
    //Chit Deposit
    function get_customer_mob($mobile)
    {
        $this->db->where('mobile', $mobile);
        $r = $this->db->get(self::CUS_TABLE);
        //print_r($this->db->last_query());exit;
        if ($r->num_rows == 1) {
            $result = $r->row_array();
            return $result;
        } else {
            return array('status' => 2, 'msg' => 'Invalid');
        }
    }
    function getGold22ct($is_branchwise_rate, $id_branch)
    {
        if ($is_branchwise_rate == 1 && $id_branch != '' && $id_branch != 0) {
            $sql = "SELECT m.goldrate_22ct FROM metal_rates m 
			LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates
				  " . ($id_branch != '' ? " WHERE br.id_branch=" . $id_branch . "" : '') . " ORDER by br.id_metalrate desc LIMIT 1";
        } else {
            $sql = "SELECT m.goldrate_22ct FROM metal_rates m 
			WHERE m.id_metalrates=( SELECT max(m.id_metalrates) FROM metal_rates m )";
        }
        $data = $this->db->query($sql);
        if ($data->num_rows() > 0) {
            return $data->row()->goldrate_22ct;
        } else {
            return 0;
        }
    }
    function updFixedRate($data, $id_sch_ac)
    {
        $this->db->where('id_scheme_account', $id_sch_ac);
        $status = $this->db->update("scheme_account", $data);
        //	print_r($this->db->last_query());exit;
        return $status; //($this->db->affected_rows() >0 ?TRUE:FALSE);
    }
    function isRateFixed($id_sch_ac)
    {
        $sql = $this->db->query("Select firstPayment_amt,fixed_wgt from scheme_account where id_scheme_account=" . $id_sch_ac);
        $res = array(
            "status" => $sql->row()->fixed_wgt > 0 ? 1 : 0,
            "firstPayment_amt" => $sql->row()->firstPayment_amt
        );
        return $res;
    }
    function get_scheme_details($id_scheme_account)
    {
        $sql = $this->db->query("SELECT s.topup_store_slab,s.total_installments,s.grace_days,s.installment_cycle,s.ins_days_duration,
		(SELECT MIN(date(p.date_payment)) FROM payment p WHERE p.payment_status = 1 and p.id_scheme_account = sa.id_scheme_account) as first_payment_date,p.payment_mode,sa.maturity_date,s.maturity_installment,s.maturity_days,s.closing_maturity_days,s.maturity_type,	
	    s.scheme_name,c.firstname as cus_name,IFNULL(c.lastname,'') as lastname,c.mobile,sa.account_name,concat(s.code,'-',sa.scheme_acc_number) as scheme_acc_number,sa.firstPayment_amt,sa.received_wgt,sa.fixed_metal_rate,sa.fixed_wgt,sa.fixed_rate_on,sa.maturity_date,s.otp_price_fix_type,s.one_time_premium,
	    date_format(sa.start_date,'%d-%m-%Y') as start_date,date_format(sa.fixed_rate_on,'%d-%m-%Y') as fixed_rate_on,IFNULL(s.description,'') as description,
	    s.emp_refferal,s.emp_incentive_closing,s.ref_benifitadd_ins_type,s.ref_benifitadd_ins,sa.id_employee,s.firstPayamt_as_payamt,
	    s.emp_refferal_value,s.maturity_type,
        IFNULL(IF(sa.is_opening=1,IFNULL(sa.balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0) as total_paid_amount,
        if(s.installment_cycle = 0 || s.installment_cycle = 3,
                               PERIOD_DIFF(date_format(CURDATE(),'%Y%m'),date_format(sa.start_date,'%Y%m'))+1,
                               if(s.installment_cycle = 1,
                                  DATEDIFF(date_format(CURDATE(),'%Y%m%d'),date_format(sa.start_date,'%Y%m%d'))+1,
                                  if(s.installment_cycle = 2,
                                  CEIL((DATEDIFF(date_format(CURDATE(),'%Y%m%d'),date_format(sa.start_date,'%Y%m%d'))+1) /s.ins_days_duration),'-'
                                    )
                                 )
                              ) 
                              as till_days_ins,
                              	DATE_FORMAT(
                        IF(
                            s.installment_cycle = 0,  
                            LAST_DAY(DATE_ADD(sa.start_date, INTERVAL (s.total_installments - 1) MONTH)),
                            IF(
                                s.installment_cycle = 1,  
                                DATE_ADD(sa.start_date, INTERVAL (s.total_installments - 1) DAY),
                                IF(
                                    s.installment_cycle = 2,  
                                    DATE_ADD(
                                        sa.start_date,
                                        INTERVAL ((s.ins_days_duration * s.total_installments) - 1) DAY
                                    ),
                                    NULL
                                )
                            )
                        ),
                        '%Y-%m-%d'
                    ) AS due_cycle_end, sa.total_paid_ins
        FROM scheme_account sa 
        LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme
        LEFT JOIN customer c ON c.id_customer=sa.id_customer
        LEFT JOIN payment p on p.id_scheme_account = sa.id_scheme_account AND p.payment_status IN (1,2)
        WHERE sa.id_scheme_account=" . $id_scheme_account . "");
        //print_r($this->db->last_query());exit;
        return $sql->row_array();
    }
    function get_old_metal_report($data)
    {
        $sql = $this->db->query("SELECT pay.id_payment,pay.id_scheme_account,s.amount as old_metal_amount,est.esti_no,concat(e.firstname,' ',ifnull(e.emp_code,'')) as emp_name,s.gross_wt,s.net_wt,emp.firstname as pay_emp,c.firstname as cus_name,est.estimation_id,br.name as branch_name,concat(sc.code,' ',sa.scheme_acc_number) as acc_number,sa.account_name, date_format(pay.date_payment,'%d-%m-%Y') as payment_date
        FROM payment_old_metal p 
        LEFT JOIN payment pay ON pay.id_payment=p.id_payment 
        LEFT JOIN ret_billing b ON b.bill_id = p.bill_id
        LEFT JOIN ret_bill_old_metal_sale_details d ON d.bill_id = b.bill_id
        LEFT JOIN ret_estimation_old_metal_sale_details s ON s.old_metal_sale_id = d.esti_old_metal_sale_id
        LEFT JOIN ret_estimation est ON est.estimation_id = s.est_id
        LEFT JOIN employee e ON e.id_employee=est.created_by 
        LEFT JOIN employee emp ON emp.id_employee=pay.id_employee 
        LEFT JOIN scheme_account sa ON sa.id_scheme_account=pay.id_scheme_account 
        LEFT JOIN customer c ON c.id_customer=sa.id_customer 
        LEFT JOIN branch br ON br.id_branch=pay.id_branch 
        LEFT JOIN scheme sc on sc.id_scheme=sa.id_scheme
        WHERE pay.payment_status=1 and (date(pay.date_payment) BETWEEN '" . date('Y-m-d', strtotime($data['from_date'])) . "' AND '" . date('Y-m-d', strtotime($data['to_date'])) . "') 
        " . ($data['id_branch'] != '' && $data['id_branch'] > 0 ? " and pay.id_branch=" . $data['id_branch'] . "" : '') . "
        ");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function get_wallet_account($id_employee)
    {
        $return_data = array();
        $sql = $this->db->query("SELECT * FROM wallet_account WHERE idemployee=" . $id_employee);
        if ($sql->num_rows() > 0) {
            $return_data = array('status' => true, 'message' => 'Account Already Exist', 'id_wallet_account' => $sql->row()->id_wallet_account);
        } else {
            $return_data = array('status' => false);
        }
        return $return_data;
    }
    function get_wallet_acc_number()
    {
        $query = $this->db->query("SELECT LPAD(round(rand() * 10000000),8,0) as myCode
								FROM wallet_account
								HAVING myCode NOT IN (SELECT wallet_acc_number FROM wallet_account) limit 0,1");
        if ($query->num_rows() == 0) {
            $query = $this->db->query("SELECT LPAD(round(rand() * 10000000),8,0) as myCode");
        }
        return $query->row()->myCode;
    }
    function get_employee_wise_acc($data)
    {
        $sql = $this->db->query("SELECT emp.firstname as emp_name,emp.emp_code,emp.mobile,
	    IFNULL(tot.tot_acc,0) as total_acc,IFNULL(act.tot_acc,0) as active_acc,IFNULL(c.tot_acc,0) as closed_acc,
	    br.name as branch_name,emp.id_employee
        FROM employee emp 
        LEFT join branch br ON br.id_branch=emp.login_branches
        LEFT JOIN (SELECT COUNT(sa.id_scheme_account) as tot_acc,sa.id_employee
                  FROM scheme_account sa 
                  WHERE sa.scheme_acc_number is NOT NULL
                  GROUP by sa.id_employee) as tot ON tot.id_employee=emp.id_employee
        LEFT JOIN (SELECT COUNT(sa.id_scheme_account) as tot_acc,sa.id_employee
                  FROM scheme_account sa 
                  WHERE sa.active=1 AND sa.scheme_acc_number is NOT NULL AND sa.is_closed=0
                  and (date(sa.start_date) BETWEEN '" . date('Y-m-d', strtotime($data['from_date'])) . "' AND '" . date('Y-m-d', strtotime($data['to_date'])) . "') 
                  GROUP by sa.id_employee) as act ON act.id_employee=emp.id_employee
        LEFT JOIN (SELECT COUNT(sa.id_scheme_account) as tot_acc,sa.id_employee
                  FROM scheme_account sa 
                  WHERE sa.active=0 AND sa.scheme_acc_number is NOT NULL AND sa.is_closed=1
                  and (date(sa.closing_date) BETWEEN '" . date('Y-m-d', strtotime($data['from_date'])) . "' AND '" . date('Y-m-d', strtotime($data['to_date'])) . "') 
                  GROUP by sa.id_employee) as c ON c.id_employee=emp.id_employee
        where emp.active=1
        ORDER BY emp.id_employee ASC");
        //print_r($this->db->Last_query());exit;
        return $sql->result_array();
    }
    function get_ret_settings($settings)
    {
        $data = $this->db->query("SELECT value FROM ret_settings where name='" . $settings . "'");
        return $data->row()->value;
    }
    function get_paid_details($id_payment)
    {
        $sql = $this->db->query("SELECT * FROM payment WHERE id_payment=" . $id_payment . "");
        $pay_details = $sql->row_array();
        $acc_details = $this->getAccDetails($pay_details['id_scheme_account']);
        if ($acc_details['id_payment'] == $id_payment) {
            return array('status' => TRUE, 'id_scheme_account' => $acc_details['id_scheme_account']);
        } else {
            return array('status' => FALSE, 'id_scheme_account' => '');
        }
    }
    function get_JoinedBenefitsDetails($id_scheme_account)
    {
        $sql = $this->db->query("SELECT * FROM `wallet_transaction` WHERE id_sch_ac=" . $id_scheme_account . " AND incentive_type=1");
        if ($sql->num_rows() > 0) {
            $return_data = array('status' => TRUE, 'wallet_details' => $sql->row_array());
        } else {
            $return_data = array('status' => FALSE);
        }
        return $return_data;
    }
    function getAccDetails($id_scheme_account)
    {
        $sql = $this->db->query("SELECT p.id_payment,p.id_scheme_account,sa.id_employee
        FROM payment p
        LEFT JOIN scheme_account sa ON sa.id_scheme_account=p.id_scheme_account
        WHERE p.id_scheme_account=" . $id_scheme_account . " and p.payment_status=1 ORDER by p.id_payment ASC LIMIT 1");
        return $sql->row_array();
    }
    function get_acc_invoiceData($id)
    {
        $acc = array();
        $query_invoice = $this->db->query("SELECT sa.id_scheme_account as id_scheme_account, s.code,
							CONCAT(s.code,'-',IFNULL(sa.scheme_acc_number,'Not Allocated')) as scheme_acc_number,
							sa.account_name,	if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,	
							c.mobile as mobile
							FROM scheme_account sa								
								LEFT JOIN payment p ON p.id_scheme_account = sa.id_scheme_account
										LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
										LEFT JOIN customer c ON c.id_customer = sa.id_customer
									WHERE sa.id_scheme_account = " . $id . " group by id_scheme_account");
        if ($query_invoice->num_rows() > 0) {
            foreach ($query_invoice->result() as $row) {
                $acc[] = array(
                    'id_scheme_account' => $row->id_scheme_account,
                    'scheme_acc_number' => $row->scheme_acc_number,
                    'account_name' => $row->account_name,
                    'mobile' => $row->mobile,
                    'code' => $row->code,
                    'name' => $row->name
                );
            }
        }
        //print_r($this->db->last_query());exit;	
        return $acc;
    }
    function getScheme_Opening_blc_details($id_scheme, $from_date, $id_branch)
    {
        $op_date = date('Y-m-d', (strtotime('-1 day', strtotime($from_date))));
        $return_data = array();
        $sql = $this->db->query("select IFNULL(sum(p.payment_amount-IFNULL(s.firstPayDisc_value,0)),0) as today_collection_amt,IFNULL(SUM(s.firstPayDisc_value),0) as today_bonus_amt,
        IFNULL(SUM(p.metal_weight),0) as today_collection_wgt,s.scheme_name,sa.id_scheme
        FROM payment p
        join company compy 
        left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
        Left Join branch b On (b.id_branch=p.id_branch) 
        left join scheme s on(sa.id_scheme=s.id_scheme) 
        Left Join payment_mode pm on (p.payment_mode=pm.id_mode)	
        left join sch_classify sch on(s.id_classification=sch.id_classification)
        Where date(p.date_payment)<'" . $op_date . "'  And p.payment_status=1
        " . ($id_scheme != '' && $id_scheme > 0 ? " and sa.id_scheme=" . $id_scheme . "" : '') . "
        " . ($id_branch != '' && $id_branch > 0 ? " and p.id_branch=" . $id_branch . "" : '') . "
        ");
        //echo "<pre>";print_r($this->db->last_query());exit;
        $return_data['collection'] = $sql->row_array();
        $prrvious_blc = $this->db->query("SELECT IFNULL(SUM(sa.balance_amount),0) as balance_amount,IFNULL(SUM(sa.balance_weight),0) as balance_weight
        FROM scheme_account sa 
        WHERE sa.is_opening=1
        and date(sa.start_date)<'" . $op_date . "'
        " . ($id_scheme != '' && $id_scheme > 0 ? " and sa.id_scheme=" . $id_scheme . "" : '') . "
        " . ($id_branch != '' && $id_branch > 0 ? " and sa.id_branch=" . $id_branch . "" : '') . "");
        $return_data['previous_blc'] = $prrvious_blc->row_array();
        $closing = $this->db->query("SELECT IFNULL(SUM(sa.closing_add_chgs),0) as closing_add_chgs,IFNULL(SUM(sa.closing_balance),0) as closing_balance,
        s.scheme_type,s.scheme_name,IFNULL(SUM(sa.closing_paid_amt),0) as closing_paid_amt,IFNULL(SUM(sa.closing_benefits),0) as closing_benefits,IFNULL(SUM(sa.closing_deductions),0) as closing_deductions
        FROM scheme_account sa 
        LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme
        WHERE sa.is_closed=1 AND date(sa.closing_date)<'" . $op_date . "' 
        " . ($id_scheme != '' && $id_scheme > 0 ? " and sa.id_scheme=" . $id_scheme . "" : '') . "
        " . ($id_branch != '' && $id_branch > 0 ? " and sa.Closing_id_branch=" . $id_branch . "" : '') . "");
        //print_r($this->db->last_query());exit;
        $return_data['closed'] = $closing->row_array();
        return $return_data;
    }
    function get_today_collection_details($from_date, $to_date, $id_scheme, $id_branch)
    {
        $return_data = array();
        $sql = $this->db->query("select IFNULL(sum(p.payment_amount-IFNULL(s.firstPayDisc_value,0)),0) as today_collection_amt,IFNULL(SUM(s.firstPayDisc_value),0) as today_bonus_amt,
        IFNULL(SUM(p.metal_weight),0) as today_collection_wgt,s.scheme_name,sa.id_scheme
        FROM payment p
        join company compy 
        left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
        Left Join branch b On (b.id_branch=p.id_branch) 
        left join scheme s on(sa.id_scheme=s.id_scheme) 
        Left Join payment_mode pm on (p.payment_mode=pm.id_mode)	
        left join sch_classify sch on(s.id_classification=sch.id_classification)
        Where (date(p.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') And p.payment_status=1
        " . ($id_scheme != '' && $id_scheme > 0 ? " and sa.id_scheme=" . $id_scheme . "" : '') . "
        " . ($id_branch != '' && $id_branch > 0 ? " and p.id_branch=" . $id_branch . "" : '') . "
        ");
        //echo "<pre>";print_r($this->db->last_query());exit;
        $return_data['collection'] = $sql->row_array();
        $prrvious_blc = $this->db->query("SELECT IFNULL(SUM(sa.balance_amount),0) as balance_amount,IFNULL(SUM(sa.balance_weight),0) as balance_weight
        FROM scheme_account sa 
        WHERE sa.is_opening=1
        and (date(sa.start_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
        " . ($id_scheme != '' && $id_scheme > 0 ? " and sa.id_scheme=" . $id_scheme . "" : '') . "
        " . ($id_branch != '' && $id_branch > 0 ? " and sa.id_branch=" . $id_branch . "" : '') . "");
        $return_data['previous_blc'] = $prrvious_blc->row_array();
        $closing = $this->db->query("SELECT IFNULL(SUM(sa.closing_add_chgs),0) as closing_add_chgs,IFNULL(SUM(sa.closing_balance),0) as closing_balance,
        s.scheme_type,s.scheme_name,IFNULL(SUM(sa.closing_paid_amt),0) as closing_paid_amt,IFNULL(SUM(sa.closing_benefits),0) as closing_benefits,IFNULL(SUM(sa.closing_deductions),0) as closing_deductions
        FROM scheme_account sa 
        LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme
        WHERE sa.is_closed=1 AND (date(sa.closing_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
        " . ($id_scheme != '' && $id_scheme > 0 ? " and sa.id_scheme=" . $id_scheme . "" : '') . "
        " . ($id_branch != '' && $id_branch > 0 ? " and sa.Closing_id_branch=" . $id_branch . "" : '') . "");
        //print_r($this->db->last_query());exit;
        $return_data['closed'] = $closing->row_array();
        return $return_data;
    }
    function get_payModes()
    {
        $sql = "SELECT * FROM payment_mode where show_in_pay = 1 ORDER BY sort_order";
        return $this->db->query($sql)->result_array();
    }
    function get_advance_details($id_customer)
    {
        $data = $this->db->query("SELECT (ir.amount-IFNULL(advance.amount,0)-IFNULL(advance_adjusted.amount,0)-IFNULL(refund.refund_amount,0))as amount,ir.id_issue_receipt,ir.bill_no
        from ret_issue_receipt ir
        left join (select sum(u.utilized_amt) as amount,ir.id_issue_receipt 
                    from ret_issue_receipt as ir 
                    left JOIN ret_advance_utilized as u on u.id_issue_receipt=ir.id_issue_receipt 
                    LEFT JOIN ret_billing bill on bill.bill_id=u.bill_id
                    where bill.bill_status=1
                    GROUP by ir.id_issue_receipt) as advance on advance.id_issue_receipt=ir.id_issue_receipt
         left join (select sum(adj.adjusted_amt) as amount,adj.receipt_for 
                    FROM ret_issue_receipt_advance_adj adj
                    LEFT JOIN ret_issue_receipt ir ON ir.id_issue_receipt=adj.id_issue_receipt
                    where ir.bill_status=1
                    GROUP by adj.receipt_for) as advance_adjusted on advance_adjusted.receipt_for=ir.id_issue_receipt
        LEFT JOIN (select a.refund_receipt,IFNULL(SUM(a.refund_amount),0) as refund_amount
                   From ret_advance_refund a
                   LEFT JOIN ret_issue_receipt r on r.id_issue_receipt=a.id_issue_receipt
                   Where r.bill_status=1
                   group by a.refund_receipt) as refund on refund.refund_receipt=ir.id_issue_receipt
        where ir.id_customer=" . $id_customer . " and ir.type=2 and ir.bill_status=1 AND (ir.receipt_type=2 or ir.receipt_type=3 or ir.receipt_type=4)
        group by ir.id_issue_receipt
        Having amount>0");
        //print_r($this->db->last_query());exit;
        return $data->result_array();
    }
    function get_payment_device_details()
    {
        $sql = $this->db->query("SELECT * FROM `ret_bill_pay_device` WHERE status=1");
        return $sql->result_array();
    }
    function getPendpayment_Data_old($previousDay, $currentDay, $id_branch, $id_pg)
    {
        $now = date('Y-m-d H:i:s');
        //p.date_payment >= '".$previousDay." 00:00:00' AND p.date_payment <= '".$currentDay." 23:59:59' 
        $sql = $this->db->query("SELECT p.ref_trans_id as txn_ids,payment_status,sum(p.payment_amount) as payment_amount,p.pay_email as email,c.mobile,p.date_payment FROM payment p
                                left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
                                left join customer c on c.id_customer=sa.id_customer
                                WHERE " . ($id_pg > 0 ? ' p.id_payGateway =' . $id_pg . ' and ' : '') . " 
                                " . ($id_branch > 0 ? ' p.id_branch =' . $id_branch . ' and ' : '') . " 
                                p.date_payment >= '" . $previousDay . " 00:00:00' AND p.date_payment <= '" . $currentDay . " 23:59:59' 
                               AND p.date_payment <= DATE_SUB('" . date('Y-m-d H:i:s') . "', INTERVAL 1 HOUR)
                                And (p.payment_status=2 OR p.payment_status=3 OR p.payment_status=4 OR p.payment_status=7) 
                                AND is_gateway_verified = 0 and p.id_payGateway > 0
                                GROUP BY ref_trans_id limit 100");
        return $sql->result_array();
    }
    function getPendpayment_Data($previousDay, $currentDay, $id_branch, $id_pg)
    {
        /*$previousDay = "2025-01-11";
        $currentDay = "2025-01-11";*/
        $sql = $this->db->query(
            "SELECT 
                                    p.ref_trans_id as txn_ids,payment_status,p.payment_ref_number as order_id,p.payment_amount,p.pay_email as email,c.mobile  
                                FROM payment p
                                left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
                                left join customer c on c.id_customer=sa.id_customer 
                                WHERE " . ($id_pg > 0 ? ' p.id_payGateway =' . $id_pg . ' and ' : '') . " 
                                " . ($id_branch > 0 ? ' p.id_branch =' . $id_branch . ' and ' : '') . "
                                p.date_payment >= '" . $previousDay . " 00:00:00' AND p.date_payment <= '" . $currentDay . " 23:59:59'
                                AND p.date_payment <= DATE_SUB('" . date('Y-m-d H:i:s') . "', INTERVAL 30 MINUTE) 
                                And (p.payment_status=3 OR p.payment_status=7 OR p.payment_status=4) and p.ref_trans_id is not null
                                group by p.ref_trans_id order by p.id_payment desc LIMIT 50"
        );
        // echo $this->db->last_query();exit;
        return $sql->result_array();
    }
    public function cusexist($data, $id_field, $table)
    {
        $this->db->from($table);
        $this->db->where($id_field, $data);
        $query = $this->db->get();
        return $query->row_array();
    }
    public function updateDatacus($data, $id_field, $id_value, $table)
    {
        $this->db->where($id_field, $id_value);
        $result = $this->db->update($table, $data);
        if ($this->db->affected_rows() > 0) {
            return true;  // Row actually updated
        } else {
            return false; // Either no change or update failed
        }
    }
    public function updData($data, $id_field, $id_value, $table)
    {
        $edit_flag = 0;
        $this->db->where($id_field, $id_value);
        $edit_flag = $this->db->update($table, $data);
        return ($edit_flag == 1 ? $id_value : 0);
    }
    // Payment Online/offline collection // HH
    function payments_on_off_collection_list($date)
    {
        $date_type = $this->input->post('date_type');
        $added_by = $this->input->post('added_by');
        $sql_1 = "select  p.added_by,s.code,
					sum(p.payment_amount) as payment_amount,s.gst_type, s.gst,IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
					COUNT(CASE WHEN  (p.receipt_no is not null  || p.receipt_no is null ) and p.payment_status=1 THEN 1 END) as receipt,
					compy.gst_number,cs.gst_setting,p.payment_type,
					if(p.payment_mode='CC','CC',p.payment_mode)as payment_mode,
					if(p.payment_mode='UPI','UPI',p.payment_mode)as payment_mode,
					if(p.payment_mode='DC','DC',p.payment_mode)as payment_mode,
					if(p.payment_type='Cash Free' || p.payment_type='Manual','Online',p.payment_type)as payment_type,
					IF(s.gst_type=0,(p.payment_amount-(p.payment_amount*(100/(100+s.gst))))/2,((p.payment_amount*(s.gst/100))/2)) as sgst,
					IF(s.gst_type=0,(p.payment_amount-(p.payment_amount*(100/(100+s.gst))))/2,((p.payment_amount*(s.gst/100))/2)) as cgst
					FROM sch_classify sc
					 join company compy
					 join chit_settings cs
					LEFT JOIN scheme s ON (sc.id_classification = s.id_classification)
					  LEFT JOIN scheme_account sa ON (s.id_scheme = sa.id_scheme)
					  LEFT JOIN payment p ON (sa.id_scheme_account = p.id_scheme_account)
					  Left Join branch b On (b.id_branch=p.id_branch)
					  LEFT JOIN postdate_payment pp ON (sa.id_scheme_account = pp.id_scheme_account)
						WHERE sc.active=1 AND (p.payment_status=1 or pp.payment_status=1)
							and date(" . ($date_type != '' ? ($date_type == 2 ? "p.custom_entry_date" : "p.date_payment") : "p.date_payment") . ")='$date'
							" . ($added_by != '' ? ($added_by == 0 ? " and payment_type='offline' and p.added_by=" . $added_by . "" : " and(p.added_by=1 or p.added_by=2 or p.added_by=0) and payment_type!='offline'") : '') . " 
						GROUP BY p.payment_mode";
        //print_r($sql_1);exit;
        //return $payments;
        $payment = $this->db->query($sql_1);
        return $payment->result_array();
    }
    function getPayIds($txnid)
    {
        $sql = "Select p.metal_weight,sa.firstPayment_wgt,s.firstPayment_as_wgt,
        sa.ref_no,flexible_sch_type,sa.id_customer,firstPayment_amt,s.firstPayamt_as_payamt,s.firstPayamt_maxpayable,p.id_payment,sa.id_scheme_account,sa.scheme_acc_number,sa.id_scheme,cs.schemeacc_no_set,cs.receipt_no_set,cs.scheme_wise_receipt,p.ref_trans_id,cs.edit_custom_entry_date,
		p.payment_ref_number,p.gst_type,IFNULL(p.gst_amount,0) as gst_amount,IFNULL(p.discountAmt,0) as discountAmt,IFNULL(p.actual_trans_amt,0) as actual_trans_amt,
    	cs.custom_entry_date,p.payment_amount,s.one_time_premium,sa.id_branch as branch,cs.allow_referral,cs.gent_clientid,s.firstPayamt_maxpayable,is_lucky_draw,s.firstPayment_as_wgt,p.redeemed_amount,wa.id_wallet_account,s.sync_scheme_code
    			 From payment p
    			 left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
    			 left join scheme s on s.id_scheme=sa.id_scheme
    			 LEFT JOIN wallet_account wa ON wa.id_customer=sa.id_customer
    			 join chit_settings cs
    			 Where p.ref_trans_id='" . $txnid . "'";
        return $this->db->query($sql)->result_array();
    }
    function updPayModeBRefTranID($data, $ref_tran_id)
    {
        $this->db->where('ref_trans_id', $ref_tran_id);
        $status = $this->db->update('payment', $data);
        return $status;
    }
    /*	function get_payment_device_details()
    {
        $sql=$this->db->query("SELECT * FROM `ret_bill_pay_device` WHERE status=1");
        return $sql->result_array();
    }*/
    function get_bank_acc_details()
    {
        $sql = $this->db->query("SELECT concat(short_code,' ',acc_number) as acc_number,address,id_bank
            FROM bank
            WHERE acc_number is NOT null");
        return $sql->result_array();
    }
    // Scheme source wise report  --- scheme wise payment details report with mode wise + online & showroom collection report   --> START 
    function sheme_payment_list_daterange($from_date, $to_date, $id_classfication, $id_scheme, $pay_mode, $id_branch, $mode, $metal_date_type)
    {
        $company_settings = $this->session->userdata('company_settings'); // New Code 05-12-2022
        $branch_settings = $this->session->userdata('branch_settings');
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $date_type = $this->input->post('date_type');
        $cus_branch = $this->input->post('cus_branch');
        $acc_type = $this->input->post('acc_type');
        $return_data = array();
        $mode = $this->input->post('mode');
        $id_employee = $this->input->post('id_employee');
        $report_type = $this->input->post('report_type');  //0-common,1-schemeWise,2-AreaWise
        //print_r($_POST);exit;
        $from_dt = date('Y-m-d', strtotime($from_date));
        $to_dt = date('Y-m-d', strtotime($to_date));
        $date_col = ($date_type == 2) ? "p.custom_entry_date" : "p.date_payment";
        $sql = $this->db->query("SELECT IFNULL(v.village_name,'No Area Updated') as village_name,IFNULL(Date_format(p.custom_entry_date,'%d-%m%-%Y'),'-') as custom_entry_date,p.id_payment,sa.id_scheme_account,c.mobile, 
                                        concat(if(chit.scheme_wise_receipt=4 or chit.scheme_wise_receipt=5,
                                        ifnull(concat(p.receipt_year,'-'),''),''),p.receipt_no) as old_receipt_no,
                                        CONCAT(c.firstname,' ',IFNULL(c.lastname,'')) as cusname, IFNULL(sa.account_name,'-') as name,
                                        if(chit.scheme_wise_acc_no=3,if(sa.scheme_acc_number is not null,concat(IFNULL(b.short_name,''),s.code,'-',sa.scheme_acc_number),concat(IFNULL(b.short_name,''),s.code,'-NotAllocated')),CONCAT(s.code,' - ',sa.scheme_acc_number)) as old_scheme_acc_number1,IFNULL(sa.old_scheme_acc_number,'-') as old_scheme_acc_number,
                                        IFNULL(sa.scheme_acc_number,'NOT ALLOCATED') as scheme_acc_number,
                                        IFNULL(sa.start_year,'') as start_year,
                                        chit.scheme_wise_acc_no,
                                        ifnull(pmd.payment_ref_number,ifnull(pmd.cheque_no,'-')) as ref_no,
                                    chit.scheme_wise_receipt,
                                    IFNULL(p.receipt_no, '') as receipt_no,
                                    acc_br.short_name as acc_branch,
                                        s.is_lucky_draw,
                                        IFNULL(sa.group_code,'-') as group_code,
                                        s.code,s.id_metal,
                                        IFNULL((select IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ COUNT(Distinct Date_Format(date_payment,'%Y%m')), if(s.scheme_type = 1 or s.scheme_type=3, COUNT(Distinct Date_Format(date_payment,'%Y%m')), SUM(no_of_dues))) from payment where id_scheme_account=sa.id_scheme_account and payment_status=1 group by id_scheme_account),0)as old2_paid_installments,     
                                        IFNULL((select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 AND s.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=sa.id_scheme_account group by pay.id_scheme_account),0) as newpaid_installments,
                                        IFNULL(Date_format(p.date_payment,'%d-%m%-%Y %H:%i'),'-') as date_payment,
                                        IFNULL(CONCAT(pmd.payment_mode,IF(p.payment_mode = 'MULTI',CONCAT(' - ',p.payment_mode),'')),'-') as oldpayment_mode,
                                        IFNULL(p.metal_rate,'0') as metal_rate,IF(p.metal_weight!='0' && p.metal_weight!='' ,p.metal_weight,'0') as tot_metal_weight, IF(p.metal_weight!='0' && p.metal_weight!='' ,(pmd.payment_amount / p.metal_rate),'0') as metal_weight1, 
                                        IF(p.metal_weight!='0' && p.metal_weight!='' ,p.metal_weight,'0') as metal_weight2,
                                        IF(p.payment_mode='FP','0',ifnull(pmd.payment_amount,'0.00')) as payment_amount1,
										ifnull(pmd.payment_amount,'0.00') as payment_amount,
                                        b.name as pay_branch,if(p.added_by=0,'Admin',if(p.added_by=1,'Web App',if(p.added_by=2,'Mobile App',if(p.added_by=3,'Collection App',if(p.added_by=4,'Retail',if(p.added_by=5,'Sync',if(p.added_by=6,'Import','-'))))))) as payment_through,s.scheme_name,IFNULL(s.firstPayDisc_value,0) as discountAmt,
                                        IF(p.remark != '' AND p.remark is not null , p.remark,'-') as remarks,p.added_by,
                                        IFNULL(if(p.added_by = 0, if(pmd.payment_mode='FP','Free payment', if(pmd.payment_mode = 'NB' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' ,CONCAT( IF(pmd.payment_mode = 'NB' && pmd.NB_type = 3,'UPI',pm.mode_name),'-', IFNULL(IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)')),if(pmd.NB_type = 1,'RTGS',if(pmd.NB_type = 2,'IMPS',if(pmd.NB_type = 3,'UPI',if(pmd.NB_type = 4,'NEFT','')))))),pm.mode_name ) ) ,pm.mode_name),p.payment_mode) as payment_mode,
                                        if(sa.is_closed=0 and sa.active=1,'Active',if(sa.is_closed=1 and sa.active=0,'Closed','')) as acc_status,
                                        IFNULL((select concat(IFNULL(e.firstname,''),' ',IFNULL(e.lastname,''),' ',IFNULL(e.emp_code,'')) from employee e left join payment pp on pp.id_employee=e.id_employee where pp.id_payment=p.id_payment and pp.payment_status=1 ),'-') as paid_employee,sa.fixed_wgt,
                                        s.scheme_type,s.flexible_sch_type,s.gst,s.gst_type,s.wgt_convert,
										p.metal_rate, p.is_editing_enabled, Date_Format(p.metalrate_edit_date, '%d-%m-%Y %r') as metalrate_edit_date ,
										CASE 
										WHEN pmd.payment_mode = 'NB'
										     THEN pmd.net_banking_date
										WHEN pmd.payment_mode = 'CHQ'
											THEN pmd.cheque_date
										ELSE
											pmd.payment_date
										END as custom_payment_date,
                                        (ifnull(sa.total_paid_ins,0)) as paid_installments 					
                                FROM payment_mode_details pmd
                                JOIN payment p ON p.id_payment = pmd.id_payment
                                JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
                                JOIN customer c ON c.id_customer = sa.id_customer
                                JOIN scheme s ON s.id_scheme = sa.id_scheme
                                JOIN branch b ON b.id_branch = p.id_branch
                                LEFT JOIN payment_mode pm ON pm.short_code = pmd.payment_mode
                                LEFT JOIN bank bk ON bk.id_bank = pmd.id_bank
                                LEFT JOIN village v ON v.id_village = c.id_village
                                LEFT JOIN ret_bill_pay_device dev ON dev.id_device = pmd.id_pay_device
                                JOIN chit_settings chit ON 1=1
                                LEFT JOIN branch acc_br ON acc_br.id_branch = sa.id_branch
                                LEFT JOIN employee pe ON pe.id_employee = p.id_employee
                                LEFT JOIN (
                                    SELECT 
                                        id_scheme_account,
                                        COUNT(DISTINCT DATE_FORMAT(date_payment, '%Y%m')) as cnt_months,
                                        SUM(no_of_dues) as total_dues
                                    FROM payment
                                    WHERE payment_status = 1
                                    GROUP BY id_scheme_account
                                ) as pay_agg ON pay_agg.id_scheme_account = sa.id_scheme_account
                                " . ($company_settings == 1 ? "LEFT JOIN company compy ON compy.id_company = c.id_company" : "") . "
                                WHERE p.payment_status = 1 AND pmd.payment_status = 1 AND pmd.is_active = 1
                                AND " . $date_col . " >= '" . $from_dt . " 00:00:00' AND " . $date_col . " <= '" . $to_dt . " 23:59:59'
                                " . ($uid != 1 ? ($branchWiseLogin == 1 ? ($branch != '' ? " and (p.id_branch=" . $branch . " or b.show_to_all=1)" : '') : '') : '') . " 
                                " . ($id_classfication != '' ? " and s.id_classification=" . $id_classfication . "" : '') . "
                                " . ($id_scheme != '' && $id_scheme != '0' ? " and s.id_scheme=" . $id_scheme . "" : '') . "
                                " . ($pay_mode != '' ? " and p.added_by=" . $pay_mode . "" : '') . "
                                " . ($id_branch != '' && $id_branch > 0 ? " and p.id_branch IN (" . $id_branch . ")" : '') . "
                                " . ($mode != '' ? ($mode == 'NB' ? " and pmd.payment_mode='NB' and pmd.NB_type != 3 " : ($mode == 'UPI' ? " and (pmd.payment_mode='UPI' OR pmd.NB_type = 3) " : " and pmd.payment_mode='" . $mode . "'")) : '') . "
                                " . ($acc_type == 1 ? " and sa.active = 1 and sa.is_closed = 0 and s.active = 1" : '') . "
                                " . ($acc_type == 2 ? " and sa.active = 0 and sa.is_closed = 1 and s.active = 1" : '') . "
                                " . ($id_employee != '' && $id_employee != null ? " and p.id_employee=" . $id_employee . "" : '') . "
								" . ($metal_date_type == 0 ? " and p.is_editing_enabled = 0" : " and p.is_editing_enabled = 1") . "
                                group by pmd.id_pay_mode_details
                                ORDER BY p.date_payment asc,s.code asc      ");
        $result = [];
        // 		print_r($this->db->last_query());exit;
        $payment = $sql->result_array();
        //echo "<pre>";print_r($payment);exit;
        if ($sql->num_rows() > 0) {
            foreach ($payment as $rcpt) {
                $rcpt['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $rcpt['id_scheme_account']);
                $rcpt['receipt_no'] = $this->customer_model->format_accRcptNo('Receipt', $rcpt['id_payment']);
                if ($rcpt['scheme_type'] == 0 || $rcpt['scheme_type'] == 3 && ($rcpt['flexible_sch_type'] == 1 || $rcpt['flexible_sch_type'] == 6 || ($rcpt['flexible_sch_type'] == 2 && $rcpt['wgt_convert'] == 2))) {
                    $is_weight = 0;
                } elseif ($rcpt['scheme_type'] == 1 || $rcpt['scheme_type'] == 2 || ($rcpt['scheme_type'] == 3 && (($rcpt['flexible_sch_type'] == 2 && ($rcpt['wgt_convert'] == 0 || $rcpt['wgt_convert'] == 1)) || $rcpt['flexible_sch_type'] == 3 || $rcpt['flexible_sch_type'] == 4 || $rcpt['flexible_sch_type'] == 5 || $rcpt['flexible_sch_type'] == 7 || $rcpt['flexible_sch_type'] == 8))) {
                    if (($rcpt['flexible_sch_type'] == 2 && ($rcpt['wgt_convert'] == 0 || $rcpt['wgt_convert'] == 1)) || $rcpt['flexible_sch_type'] != 2) {
                        $is_weight = 1;
                    } elseif ($rcpt['flexible_sch_type'] == 2 && $rcpt['wgt_convert'] == 2) {
                        $is_weight = 0;
                    } else {
                        $is_weight = 1;
                    }
                }
                if ($is_weight == 1) {
                    // $rcpt['metal_weight'] = ($rcpt['payment_amount'] * (100 / (100 + $rcpt['gst']))) / $rcpt['metal_rate'];
                    if ($rcpt['added_by'] == 6 && $rcpt['metal_rate']) { // Sync Payment
                        $rcpt['metal_weight'] = $rcpt['tot_metal_weight'];
                    } else {
                        $rcpt['metal_weight'] = (!empty($rcpt['metal_rate']) && $rcpt['metal_rate'] > 0) ? ($rcpt['payment_amount'] * (100 / (100 + $rcpt['gst']))) / $rcpt['metal_rate'] : 0;
                    }
                } else {
                    if ($rcpt['gst_type'] == 0) {
                        // inclusive gst
                        $rcpt['metal_weight'] = $rcpt['metal_weight2'];
                    } else if ($rcpt['gst_type'] == 1) {
                        // exclusive gst
                        $rcpt['metal_weight'] = (!empty($rcpt['metal_rate']) && $rcpt['metal_rate'] > 0) ? ($rcpt['payment_amount'] * (100 / (100 + $rcpt['gst']))) / $rcpt['metal_rate'] : 0;
                    }
                }
                $result[] = $rcpt;
            }
        }
        foreach ($result as $r) {
            if ($report_type == 1) { //scheme wise
                $return_data[$r['scheme_name']][] = $r;
            } elseif ($report_type == 2) { //area wise
                $return_data[$r['village_name']][] = $r;
            } elseif ($report_type == 0) { //common
                $return_data['Payment'][] = $r;
            } else {
                $return_data[$r['scheme_name']][] = $r;
            }
        }
        return $return_data;
    }
    function get_Scheme_Payment_ModeWiseummaryDetails($from_date, $to_date, $id_classfication, $id_scheme, $pay_mode, $id_branch, $mode, $metal_date_type)
    {
        $branch_settings = $this->session->userdata('branch_settings');
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $result = array();
        $sql_pay_mode = "SELECT @a:=@a+1 as sno,sum(pmd.payment_amount) as received_amt, s.gst_type, s.gst,compy.gst_number,cs.gst_setting, IF(s.gst_type=0,(pmd.payment_amount-(pmd.payment_amount*(100/(100+s.gst))))/2,((pmd.payment_amount*(s.gst/100))/2)) as sgst, IF(s.gst_type=0,(pmd.payment_amount-(pmd.payment_amount*(100/(100+s.gst))))/2,((pmd.payment_amount*(s.gst/100))/2)) as cgst, if(pmd.payment_mode='FP','Free payment',pm.mode_name)as mode_name,pmd.payment_mode 
    FROM payment p 
    join chit_settings cs 
    join company compy 
    left join payment_mode_details pmd on(pmd.id_payment=p.id_payment) 
    left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account) 										left join branch b on(b.id_branch=p.id_branch)					
    left join scheme s on(sa.id_scheme=s.id_scheme) 
    Left Join payment_mode pm on (pmd.payment_mode=pm.short_code) 
    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
    Where (date(p.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
    " . ($uid != 1 ? ($branchWiseLogin == 1 ? ($branch != '' ? " and (p.id_branch=" . $branch . " or b.show_to_all=1)" : '') : '') : '') . " 
    " . ($id_classfication != '' ? " and s.id_classification=" . $id_classfication . "" : '') . "
    " . ($id_scheme != '' && $id_scheme != '0' ? " and s.id_scheme=" . $id_scheme . "" : '') . "
    " . ($pay_mode != '' ? " and p.added_by=" . $pay_mode . "" : '') . "
    " . ($id_branch != '' && $id_branch > 0 ? " and p.id_branch IN (" . $id_branch . ")" : '') . "
    " . ($mode != '' ? " and p.payment_mode='" . $mode . "'" : '') . "
	" . ($metal_date_type == 0 ? " and p.is_editing_enabled = 0" : " and p.is_editing_enabled = 1") . "
    And p.payment_status=1
    group by pmd.payment_mode ORDER BY p.date_payment DESC" . ($limit != NULL ? " LIMIT " . $limit . " OFFSET " . $limit : " ");
        // print_r($sql_pay_mode);exit;
        $payments = $this->db->query($sql_pay_mode)->result_array();
        return $payments;
    }
    function payment_summary_modewise_data($from_date = "", $to_date = "", $id_classfication = "", $id_scheme = "", $pay_mode = "", $id_branch = "", $mode = "", $edit_custom_entry_date = "", $metal_date_type = "")
    {
        //  print_r($_POST);exit;
        $branch_settings = $this->session->userdata('branch_settings');
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $mode = $this->input->post('mode');
        $acc_type = $this->input->post('acc_type');
        $date_type = $this->input->post('date_type');
        $id_employee = $this->input->post('id_employee');
        // Optimized dates for indexing
        $from_dt = date('Y-m-d 00:00:00', strtotime($from_date));
        $to_dt = date('Y-m-d 23:59:59', strtotime($to_date));
        $date_col_field = ($date_type != '' ? ($date_type == 2 ? "p.custom_entry_date" : "p.date_payment") : "p.date_payment");
        //print_r($acc_type);exit;
        $sql1 = "SELECT  (SUM(pmd.payment_amount)-sum(IFNULL(s.firstPayDisc_value,0))) as offline_amt,
    if(pmd.payment_mode='FP','Free payment', IF(p.payment_mode = 'MULTI',CONCAT(p.payment_mode,' - ',pm.mode_name),pm.mode_name)) as old1_mode_name, 
    IFNULL(if(pmd.payment_mode='FP','Free payment', IF(p.payment_mode = 'MULTI',CONCAT(p.payment_mode,' - ',IF(pmd.payment_mode = 'NB' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' , CONCAT(pmd.payment_mode,'-',IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)'))),pm.mode_name)),IF(pmd.payment_mode = 'NB' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' , CONCAT(pmd.payment_mode,'-',IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)'))),pm.mode_name))),p.payment_mode) as multiseparate_mode_name, 
    IFNULL(if(pmd.payment_mode='FP','Free payment', IF(p.payment_mode = 'MULTI',CONCAT(p.payment_mode,' - ',IF(pmd.payment_mode = 'NB' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' , CONCAT( IF(pmd.payment_mode = 'NB' && pmd.NB_type = 3,'UPI',pm.mode_name),'-',IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)'))),pm.mode_name)),IF(pmd.payment_mode = 'NB' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' , CONCAT( IF(pmd.payment_mode = 'NB' && pmd.NB_type = 3,'UPI',pm.mode_name),'-',IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)'))),pm.mode_name))),p.payment_mode) as multiwithBankDevNbtype_mode_name,
    IFNULL( if(pmd.payment_mode='FP','Free payment', if(pmd.payment_mode = 'NB' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' ,CONCAT( IF(pmd.payment_mode = 'NB' && pmd.NB_type = 3,'UPI',pm.mode_name),'-', IFNULL(IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)')),if(pmd.NB_type = 1,'RTGS',if(pmd.NB_type = 2,'IMPS',if(pmd.NB_type = 3,'UPI',if(pmd.NB_type = 4,'NEFT','')))))),pm.mode_name ) ) ,p.payment_mode) as mode_name,
    pmd.payment_mode as payment_mode 
    FROM payment_mode_details pmd 
    join chit_settings cs 
    LEFT JOIN payment p ON (p.id_payment = pmd.id_payment AND p.payment_status = 1)
    Left Join payment_mode pm on (pm.short_code = pmd.payment_mode) 
    left join scheme_account sa on(sa.id_scheme_account=p.id_scheme_account)
    left join scheme s on(sa.id_scheme=s.id_scheme)
    left join branch b on(b.id_branch=p.id_branch)
    left join bank bk on(bk.id_bank=pmd.id_bank)
    left join ret_bill_pay_device dev on(dev.id_device = pmd.id_pay_device)
    Where p.payment_status=1   and (pmd.payment_status=1 and pmd.is_active=1)
    and (" . $date_col_field . " >= '" . $from_dt . "' AND " . $date_col_field . " <= '" . $to_dt . "')
    " . ($uid != 1 ? ($branchWiseLogin == 1 ? ($branch != '' ? " and (p.id_branch=" . $branch . " or b.show_to_all=1)" : '') : '') : '') . " 
    " . ($id_classfication != '' ? " and s.id_classification=" . $id_classfication . "" : '') . "
    " . ($mode != '' ? ($mode == 'NB' ? " and pmd.payment_mode='NB' and pmd.NB_type != 3 " : ($mode == 'UPI' ? " and (pmd.payment_mode  in ('NB','UPI') AND pmd.NB_type = 3) " : " and pmd.payment_mode='" . $mode . "'")) : '') . "
    " . ($id_scheme != '' && $id_scheme != '0' ? " and s.id_scheme=" . $id_scheme . "" : '') . "
    " . ($pay_mode != '' ? (($pay_mode == 0) ? " and p.added_by=" . $pay_mode . "" : " ") : "AND p.added_by IN (0,6)") . "
    " . ($id_branch != '' && $id_branch > 0 ? " and p.id_branch IN (" . $id_branch . ")" : '') . "
    " . ($acc_type == 1 ? " and sa.active = 1 and sa.is_closed = 0 and s.active = 1" : '') . "
    " . ($acc_type == 2 ? " and sa.active = 0 and sa.is_closed = 1 and s.active = 1" : '') . "
    " . ($id_employee != '' && $id_employee != null ? " and p.id_employee=" . $id_employee . "" : '') . "
    " . ($metal_date_type == 0 ? " and p.is_editing_enabled = 0" : " and p.is_editing_enabled = 1") . "
    group by pmd.payment_mode,pmd.id_bank,pmd.id_pay_device,pmd.NB_type		order by pm.mode_name ASC";
        // 		echo $sql1;exit;
        $mode = $this->input->post('mode');
        $acc_type = $this->input->post('acc_type');
        $sql2 = "SELECT (SUM(pmd.payment_amount)-sum(IFNULL(s.firstPayDisc_value,0))) as online_amt, 
    if(pmd.payment_mode='FP','Free payment', IF(p.payment_mode = 'MULTI',CONCAT(p.payment_mode,' - ',pm.mode_name),pm.mode_name)) as mode_name,
    pmd.payment_mode as payment_mode
    FROM payment_mode_details pmd 
    join chit_settings cs 
    LEFT JOIN payment p ON (p.id_payment = pmd.id_payment AND p.payment_status = 1)
    Left Join payment_mode pm on (pm.short_code = pmd.payment_mode) 
    left join scheme_account sa on(sa.id_scheme_account=p.id_scheme_account)
    left join scheme s on(sa.id_scheme=s.id_scheme)
    left join branch b on(b.id_branch=p.id_branch)
    left join bank bk on(bk.id_bank=pmd.id_bank)
    left join ret_bill_pay_device dev on(dev.id_device = pmd.id_pay_device)
    Where p.payment_status=1   and (pmd.payment_status=1 and pmd.is_active=1) and
    (" . $date_col_field . " >= '" . $from_dt . "' AND " . $date_col_field . " <= '" . $to_dt . "')
    " . ($uid != 1 ? ($branchWiseLogin == 1 ? ($branch != '' ? " and (p.id_branch=" . $branch . " or b.show_to_all=1)" : '') : '') : '') . " 
    " . ($id_classfication != '' ? " and s.id_classification=" . $id_classfication . "" : '') . "
    " . ($id_scheme != '' && $id_scheme != '0' ? " and s.id_scheme=" . $id_scheme . "" : '') . "
    " . ($pay_mode != '' ? (($pay_mode == 1 || $pay_mode == 2) ? " and p.added_by=" . $pay_mode . "" : "AND p.added_by IN (1,2)") : "AND p.added_by IN (1,2)") . "
    " . ($id_branch != '' && $id_branch > 0 ? " and p.id_branch IN(" . $id_branch . ")" : '') . "
    " . ($mode != '' ? "and pmd.payment_mode='" . $mode . "'" : '') . "
    " . ($acc_type == 1 ? " and sa.active = 1 and sa.is_closed = 0 and s.active = 1" : '') . "
    " . ($acc_type == 2 ? " and sa.active = 0 and sa.is_closed = 1 and s.active = 1" : '') . "
    " . ($id_employee != '' && $id_employee != null ? " and p.id_employee=" . $id_employee . "" : '') . "
    " . ($metal_date_type == 0 ? " and p.is_editing_enabled = 0" : " and p.is_editing_enabled = 1") . "
    group by pmd.payment_mode,pmd.id_bank,pmd.id_pay_device,pmd.NB_type		order by pm.mode_name ASC";
        //print_r($mode);
        //   print_r($sql1);exit;
        $mode = $this->input->post('mode');
        $acc_type = $this->input->post('acc_type');
        $sql3 = "SELECT  (SUM(pmd.payment_amount)-sum(IFNULL(s.firstPayDisc_value,0))) as admin_app_amt, 
    if(pmd.payment_mode='FP','Free payment', IF(p.payment_mode = 'MULTI',CONCAT(p.payment_mode,' - ',pm.mode_name),pm.mode_name)) as old1_mode_name, 
    IFNULL(if(pmd.payment_mode='FP','Free payment', IF(p.payment_mode = 'MULTI',CONCAT(p.payment_mode,' - ',IF(pmd.payment_mode = 'NB' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' , CONCAT(pmd.payment_mode,'-',IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)'))),pm.mode_name)),IF(pmd.payment_mode = 'NB' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' , CONCAT(pmd.payment_mode,'-',IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)'))),pm.mode_name))),p.payment_mode) as multiseparate_mode_name, 
    IFNULL(if(pmd.payment_mode='FP','Free payment', IF(p.payment_mode = 'MULTI',CONCAT(p.payment_mode,' - ',IF(pmd.payment_mode = 'NB' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' , CONCAT( IF(pmd.payment_mode = 'NB' && pmd.NB_type = 3,'UPI',pm.mode_name),'-',IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)'))),pm.mode_name)),IF(pmd.payment_mode = 'NB' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' , CONCAT( IF(pmd.payment_mode = 'NB' && pmd.NB_type = 3,'UPI',pm.mode_name),'-',IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)'))),pm.mode_name))),p.payment_mode) as multiwithBankDevNbtype_mode_name,
    IFNULL( if(pmd.payment_mode='FP','Free payment', if(pmd.payment_mode = 'NB' || pmd.payment_mode = 'CC' || pmd.payment_mode = 'DC' ,CONCAT( IF(pmd.payment_mode = 'NB' && pmd.NB_type = 3,'UPI',pm.mode_name),'-', IFNULL(IFNULL(CONCAT(bk.short_code,'(B)'),CONCAT(dev.device_name,'(D)')),if(pmd.NB_type = 1,'RTGS',if(pmd.NB_type = 2,'IMPS',if(pmd.NB_type = 3,'UPI',if(pmd.NB_type = 4,'NEFT','')))))),pm.mode_name ) ) ,p.payment_mode) as mode_name,
    pmd.payment_mode as payment_mode 
    FROM payment_mode_details pmd 
    join chit_settings cs 
    LEFT JOIN payment p ON (p.id_payment = pmd.id_payment AND p.payment_status = 1)
    Left Join payment_mode pm on (pm.short_code = pmd.payment_mode) 
    left join scheme_account sa on(sa.id_scheme_account=p.id_scheme_account)
    left join scheme s on(sa.id_scheme=s.id_scheme)
    left join branch b on(b.id_branch=p.id_branch)
    left join bank bk on(bk.id_bank=pmd.id_bank)
    left join ret_bill_pay_device dev on(dev.id_device = pmd.id_pay_device)
    Where p.payment_status=1   and (pmd.payment_status=1 and pmd.is_active=1)
    and (" . $date_col_field . " >= '" . $from_dt . "' AND " . $date_col_field . " <= '" . $to_dt . "')
    " . ($uid != 1 ? ($branchWiseLogin == 1 ? ($branch != '' ? " and (p.id_branch=" . $branch . " or b.show_to_all=1)" : '') : '') : '') . " 
    " . ($id_classfication != '' ? " and s.id_classification=" . $id_classfication . "" : '') . "
    " . ($mode != '' ? ($mode == 'NB' ? " and pmd.payment_mode='NB' and pmd.NB_type != 3 " : ($mode == 'UPI' ? " and (pmd.payment_mode  in ('NB','UPI') AND pmd.NB_type = 3) " : " and pmd.payment_mode='" . $mode . "'")) : '') . "
    " . ($id_scheme != '' && $id_scheme != '0' ? " and s.id_scheme=" . $id_scheme . "" : '') . "
    " . ($pay_mode != '' ? ($pay_mode == 3 ? " and p.added_by=" . $pay_mode . "" : " ") : "AND p.added_by IN (3)") . "
    " . ($id_branch != '' && $id_branch > 0 ? " and p.id_branch IN(" . $id_branch . ")" : '') . "
    " . ($acc_type == 1 ? " and sa.active = 1 and sa.is_closed = 0 and s.active = 1" : '') . "
    " . ($acc_type == 2 ? " and sa.active = 0 and sa.is_closed = 1 and s.active = 1" : '') . "
    " . ($id_employee != '' && $id_employee != null ? " and p.id_employee=" . $id_employee . "" : '') . "
    " . ($metal_date_type == 0 ? " and p.is_editing_enabled = 0" : " and p.is_editing_enabled = 1") . "
    group by pmd.payment_mode,pmd.id_bank,pmd.id_pay_device,pmd.NB_type		order by pm.mode_name ASC";
        //print_r($sql3);exit;
        if ($pay_mode == 0 && $pay_mode != '') {
            $result['offline'] = $this->db->query($sql1)->result_array();
        } elseif ($pay_mode == 1 || $pay_mode == 2) {
            $result['online'] = $this->db->query($sql2)->result_array();
        } elseif ($pay_mode == 3) {
            $result['admin_app'] = $this->db->query($sql3)->result_array();
        } else {
            $result['offline'] = $this->db->query($sql1)->result_array();
            // print_r($this->db->last_query());exit;
            $result['online'] = $this->db->query($sql2)->result_array();
            $result['admin_app'] = $this->db->query($sql3)->result_array();
        }
        //print_r($result);exit;
        return $result;
    }
    function ajax_getPayModeList()
    {
        $sql = $this->db->query("SELECT id_mode,mode_name,short_code FROM `payment_mode` where status = 1");
        return $sql->result_array();
    }
    function get_count_mode($from_date = "", $to_date = "", $id_classfication = "", $id_scheme = "", $pay_mode = "", $id_branch = "")
    {
        $branch_settings = $this->session->userdata('branch_settings');
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $sql = "SELECT sum(p.payment_amount)as payment_amount,count(p.id_payment) as payment_count,if(p.added_by=0,'offline',if(p.added_by=1 || p.added_by=2 ,'online',if(p.added_by=3,'admin_app',''))) as payment_through
    FROM payment p 
    join chit_settings cs 
    Left Join payment_mode pm on (p.payment_mode=pm.short_code)
    left join branch b on(b.id_branch=p.id_branch)					
    left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account) 
    left join scheme s on(sa.id_scheme=s.id_scheme) 
    Where (date(p.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
    " . ($uid != 1 ? ($branchWiseLogin == 1 ? ($branch != '' ? " and (p.id_branch=" . $branch . " or b.show_to_all=1)" : '') : '') : '') . " 
    " . ($id_classfication != '' ? " and s.id_classification=" . $id_classfication . "" : '') . "
    " . ($id_scheme != '' ? " and s.id_scheme=" . $id_scheme . "" : '') . "
    " . ($pay_mode != '' ? " and p.added_by=" . $pay_mode . "" : " ") . "
    " . ($id_branch != '' && $id_branch > 0 ? " and p.id_branch=" . $id_branch . "" : '') . "
    " . ($mode != '' ? " and p.payment_mode='" . $mode . "'" : '') . "
    And p.payment_status=1 group by p.added_by ";
        $data = $this->db->query($sql);
        // print_r($sql);exit;
        return $data->result_array();
    }
    // Scheme source wise report  --- scheme wise payment details report with mode wise + online & showroom collection report   --> END
    // Agent transaction starts 
    function get_agent_refdata($id_scheme_account, $pay_id)
    {
        $sql = ("SELECT sa.agent_code,sa.id_agent,sa.id_customer as cus_loyal_cus_id,is_refferal_by,sa.id_scheme,p.payment_amount,
					IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight ,
					COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0),
					if(s.scheme_type = 1 and s.min_weight != s.max_weight or s.scheme_type=3 , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
					as paid_installments
					     FROM scheme_account sa
					    left join scheme s on (s.id_scheme=sa.id_scheme)
					    left join payment p on (sa.id_scheme_account=p.id_scheme_account) 
						where sa.id_scheme_account=" . $id_scheme_account . "  and  p.payment_status=1 group by sa.id_scheme_account");
        //	print_r($sql);exit;
        $result = $this->db->query($sql)->row_array();
        return $result;
    }
    function get_agentBenefits($id_scheme, $amount, $ins)
    {
        $current_ins = $ins;
        $sql = $this->db->query("SELECT if(ab.benefit_type=0,((" . $amount . "*ab.benefit_value)/100),ab.benefit_value) as cash_point FROM `scheme_agent_benefit` ab  
         WHERE ab.id_scheme = " . $id_scheme . " AND ab.installment_from >= " . $current_ins . " AND ab.installment_to <=" . $current_ins . " ");
        if ($sql->num_rows() > 0) {
            return $sql->row_array();
        } else
            return 0;
    }
    function updateAgentCash($id_agent, $new_point)
    {
        $status = $this->db->query("UPDATE agent set cash_reward = (cash_reward+" . $new_point . ") where id_agent=" . $id_agent);
        return $status;
    }
    function updateDebitCash($id_agent, $new_point)
    {
        $status = $this->db->query("UPDATE agent set cash_reward = (cash_reward-" . $new_point . ") where id_agent=" . $id_agent);
        return $status;
    }
    function insert_agent_transaction($data)
    {
        $status = $this->db->insert('ly_customer_loyalty_transaction', $data);
        return $status;
    }
    //Agent ends
    //credit incentive starts
    function get_Incentivedata($id_scheme, $id_scheme_account, $type, $id_payment, $credit_for)   // 22-10
    {
        $sql = $this->db->query("SELECT * FROM scheme_incentive_settings where credit_to = " . $type . " and id_scheme=" . $id_scheme . " AND credit_for = '" . $credit_for . "'");
        //print_r($this->db->last_query());exit;
        $data = array();
        if ($sql->num_rows() > 0) {
            $sql1 = $this->db->query("SELECT sa.agent_code,sa.id_agent,sa.id_customer as cus_loyal_cus_id,sa.is_refferal_by,sa.id_scheme,p.payment_amount,p.due_type,
					(select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), 
					if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 AND s.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) 
					from payment pay where pay.payment_status=1 and pay.id_scheme_account=sa.id_scheme_account group by pay.id_scheme_account) as paid_installments,date(p.date_payment) as payment_date
					     FROM scheme_account sa
					    left join scheme s on (s.id_scheme=sa.id_scheme)
					    left join payment p on (sa.id_scheme_account=p.id_scheme_account) 
						where sa.id_scheme_account=" . $id_scheme_account . "  and  p.payment_status=1 and p.id_payment=" . $id_payment . " group by sa.id_scheme_account");
            $acc_data = $sql1->row_array();
            //echo $this->db->last_query();exit;
            $ref_data = $sql->result_array();
            $credit_remark = '';
            if ($acc_data['payment_amount'] != '' && $acc_data['payment_amount'] > 0) {
                foreach ($ref_data as $ref) {
                    //benefit in sch join - type1 (applicable for Agent and Employee)
                    if (($ref['credit_for'] == 0 || $ref['credit_for'] == 1) && ($ref['from_range'] <= $acc_data['paid_installments'] && $ref['to_range'] >= $acc_data['paid_installments'])) {
                        //calc % and amt based on settings
                        $cash_point = $this->calcIncentiveAmt($ref['credit_type'], $acc_data['payment_amount'], $ref['credit_value']);
                        $credit_remark = 'New Scheme Join';
                        $data[] = array('referal_amount' => $cash_point, 'credit_for' => $ref['credit_for'], 'id_customer' => $acc_data['cus_loyal_cus_id'], 'credit_remark' => $credit_remark, 'id_payment' => $id_payment, 'is_refferal_by' => $acc_data['is_refferal_by']);
                    } else if ($ref['credit_for'] == 3) { //credit benefit based on no of days - only for Agent credit in Collection App
                        $month_first_day = date('Y-m-01');
                        $no_of_days = $this->dateDiff($month_first_day, $acc_data['payment_date']);
                        if ($no_of_days == 0) {
                            $no_of_days = 1;
                        } else {
                            $no_of_days = $no_of_days;
                        }
                        if ($ref['from_range'] <= $no_of_days && $ref['to_range'] >= $no_of_days) {
                            //calc % and amt based on settings
                            $cash_point = $this->calcIncentiveAmt($ref['credit_type'], $acc_data['payment_amount'], $ref['credit_value']);
                            $credit_remark = 'Credits between date ' . $ref['from_range'] . ' to ' . $ref['to_range'];
                            $data[] = array('referal_amount' => $cash_point, 'credit_for' => $ref['credit_for'], 'id_customer' => $acc_data['cus_loyal_cus_id'], 'credit_remark' => $credit_remark, 'id_payment' => $id_payment, 'is_refferal_by' => $acc_data['is_refferal_by']);
                        }
                    } else if ($ref['credit_for'] == 2) // date wise credits ( Sunday Collection benefits only for Collection App)
                    {
                        $nameOfDay = date('l', strtotime($acc_data['payment_date']));
                        if ($nameOfDay == $ref['from_range'] || $nameOfDay == $ref['to_range']) {
                            $cash_point = $this->calcIncentiveAmt($ref['credit_type'], $acc_data['payment_amount'], $ref['credit_value']);
                            $credit_remark = $nameOfDay . ' Collection';
                            $data[] = array('referal_amount' => $cash_point, 'credit_for' => $ref['credit_for'], 'id_customer' => $acc_data['cus_loyal_cus_id'], 'credit_remark' => $credit_remark, 'id_payment' => $id_payment, 'is_refferal_by' => $acc_data['is_refferal_by']);
                        }
                    }
                }
                return $data;
            } else {
                return $data;
            }
        } else {
            return $data;
        }
    }
    function dateDiff($date1, $date2)
    {
        $date1_ts = strtotime($date1);
        $date2_ts = strtotime($date2);
        $diff = $date2_ts - $date1_ts;
        return round($diff / 86400);
    }
    function calcIncentiveAmt($type, $pay_amt, $ref_val)
    {
        $cash_point = 0;
        if ($type == 1) {
            $cash_point = ($pay_amt * $ref_val) / 100;
        } else {
            $cash_point = $ref_val;
        }
        return $cash_point;
    }
    function checkReferalExist($id_payment, $id_sch_acc)
    {
        $sql = $this->db->query("SELECT id_scheme_account,id_payment from ly_customer_loyalty_transaction where id_scheme_account = " . $id_sch_acc . " and id_payment=" . $id_payment);
        if ($sql->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    function checkCreditTransExist($id_scheme_account, $id_payment)
    {
        $r = "SELECT id_sch_ac,id_payment from wallet_transaction where id_sch_ac = " . $id_scheme_account . " and id_payment=" . $id_payment;
        $sql = $this->db->query($r);
        if ($sql->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    function get_empRefExist_datas($id_scheme_account)
    {
        $sql = $this->db->query("SELECT referal_code,is_refferal_by from scheme_account where referal_code != '' and is_refferal_by = 0 and id_scheme_account=" . $id_scheme_account);
        if ($sql->num_rows() > 0) {
            $data = $sql->row_array();
            $referEmpData = $this->db->query("SELECT wa.id_customer,wa.id_wallet_account,mobile,c.firstname as cusname from customer c LEFT JOIN wallet_account wa ON wa.id_customer=c.id_customer where mobile ='" . trim($data['referal_code']) . "'");
            //print_r($this->db->last_query());exit;
            //$cus_id = $referEmpData->row_array();
            //$sql1 = $this->db->query("SELECT referal_code,id_scheme_account,id_customer from scheme_account where id_customer=".$cus_id);
            if ($referEmpData->num_rows() > 0) {
                return $referEmpData->row_array();
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
    //end
    // update gift status as issued when payment status is success...
    function get_gifts_by_schId($id_scheme_account)
    {
        $sql = $this->db->query("SELECT * from gift_issued where type = 1 and id_scheme_account=" . $id_scheme_account);
        if ($sql->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    function upd_gift_status($data, $id_scheme_account)
    {
        $this->db->where('id_scheme_account', $id_scheme_account);
        $status = $this->db->update('gift_issued', $data);
        return $status;
    }
    // Metal rate back date entry esakki-20250729
    function edit_payment($id)
    {
        $pay = $this->db->query("SELECT sch.id_customer,p.id_payment,p.type,p.id_scheme_account,p.date_payment,p.installment,p.payment_type,p.payment_status,p.payment_mode,p.payment_amount,p.metal_rate,p.metal_weight,p.payment_ref_number,p.added_by,p.remark,s.scheme_type,s.flexible_sch_type,(SELECT pm.id_pay_mode_details FROM payment_mode_details pm WHERE pm.id_payment = p.id_payment and pm.is_active=1  ORDER BY pm.id_pay_mode_details DESC limit 1) as id_pay_mode_details,CONCAT(s.code,'-',IFNULL(sch.scheme_acc_number,'Not Allocated')) as chit_number,s.disable_pay_amt,s.disable_pay,p.gst_amount,p.is_editing_enabled,Date_Format(p.metalrate_edit_date, '%d-%m-%Y %r') as metalrate_edit_date 
	    	FROM payment p
            LEFT JOIN scheme_account sch on p.id_scheme_account=sch.id_scheme_account
			LEFT JOIN scheme s on sch.id_scheme=s.id_scheme WHERE p.id_payment = $id GROUP BY p.id_payment");
        $result['payData'] = $pay->result_array();
        //esakki 18-09
        //restrict CASH payments based on limit settings
        $disable_acc_payments = '';
        $csh_payments = 0;
        if ($result['payData'][0]['disable_pay'] == 1 && $result['payData'][0]['disable_pay_amt'] > 0) {
            $res = $this->db->query("SELECT sum(payment_amount) as total_csh from payment where payment_mode = 'CSH' and id_scheme_account =" . $result['payData'][0]['id_scheme_account']);
            $csh_payments = $res->row()->total_csh;
            if ($csh_payments >= $result['payData'][0]['disable_pay_amt']) {
                $result['payData'][0]['disable_acc_payments'] = 'Y';
                $result['payData'][0]['allow_cash_limit'] = 0;
            } else {
                $result['payData'][0]['disable_acc_payments'] = 'N';
                $result['payData'][0]['allow_cash_limit'] = abs($result['payData'][0]['disable_pay_amt'] - $csh_payments);
            }
        }
        //$mode = $this->db->query("SELECT * FROM `payment_mode_details` where payment_status = 1 and id_payment = $id");
        $mode = $this->db->query("SELECT pmd.* FROM payment_mode_details pmd left join payment p on pmd.id_payment=p.id_payment where (p.payment_status = pmd.payment_status and pmd.is_active=1) and pmd.id_payment = $id ");
        //	$result['modeData'] = $mode->result_array();
        foreach ($mode->result_array() as $mode) {
            $pay_mode = $mode['payment_mode'];
            if ($mode['payment_mode'] == 'CC' || $mode['payment_mode'] == 'DC') {
                $result['modeData']['Card'][] = array('payment_mode' => $mode['payment_mode'], 'card_type' => $mode['card_type'], 'card_no' => $mode['card_no'], 'card_holder' => $mode['card_holder'], 'payment_amount' => $mode['payment_amount'], 'payment_ref_number' => $mode['payment_ref_number'], 'id_pay_device' => $mode['id_pay_device']);
                if ($pay_mode == 'CC') {
                    $result['total'][$pay_mode] += $mode['payment_amount'];
                } else if ($pay_mode == 'DC') {
                    $result['total'][$pay_mode] += $mode['payment_amount'];
                }
            }
            /*	if($mode['payment_mode'] == 'DC'){
                $result['modeData'][$pay_mode][]= array('payment_mode' => $mode['payment_mode'], 'card_no' => $mode['card_no'], 'card_holder' => $mode['card_holder'], 'payment_amount' => $mode['payment_amount']); 
                $result['total'][$pay_mode]	+= 	$mode['payment_amount'];
            }  */
            if ($mode['payment_mode'] == 'NB') {
                $result['modeData'][$pay_mode][] = array('payment_mode' => $mode['payment_mode'], 'payment_ref_number' => $mode['payment_ref_number'], 'payment_amount' => $mode['payment_amount'], 'id_pay_device' => $mode['id_pay_device'], 'NB_type' => $mode['NB_type'], 'net_banking_date' => $mode['net_banking_date'], 'id_bank' => $mode['id_bank']);
                $result['total'][$pay_mode] += $mode['payment_amount'];
            }
            if ($mode['payment_mode'] == 'CHQ') {
                $result['modeData'][$pay_mode][] = array('payment_mode' => $mode['payment_mode'], 'bank_IFSC' => $mode['bank_IFSC'], 'bank_acc_no' => $mode['bank_acc_no'], 'bank_branch' => $mode['bank_branch'], 'bank_name' => $mode['bank_name'], 'cheque_no' => $mode['cheque_no'], 'payment_amount' => $mode['payment_amount'], 'cheque_date' => $mode['cheque_date']);
                $result['total'][$pay_mode] += $mode['payment_amount'];
            }
            if ($mode['payment_mode'] == 'CSH') {
                $result['modeData'][$pay_mode][] = array('payment_amount' => $mode['payment_amount']);
                $result['total'][$pay_mode] += $mode['payment_amount'];
            }
            if ($mode['payment_mode'] == 'ADV_ADJ') {
                $result['modeData'][$pay_mode][] = array('payment_amount' => $mode['payment_amount'], 'adv_receipt_no' => $mode['adv_receipt_no']);
                $result['total'][$pay_mode] += $mode['payment_amount'];
            }
            //voucher starts
            if ($mode['payment_mode'] == 'VCH') {
                $result['modeData'][$pay_mode][] = array('payment_mode' => $mode['payment_mode'], 'payment_amount' => $mode['payment_amount'], 'card_no' => $mode['card_no']);
                $result['total'][$pay_mode] += $mode['payment_amount'];
            }
            //voucher ends
        }
        return $result;
    }
    function deleteUtilized($id_pay)
    {
        $this->db->where("id_payment", $id_pay);
        $status = $this->db->delete('ret_advance_utilized');
        return array('status' => $status, 'DeleteID' => $id_pay);
    }
    function getWarehouse($id)
    {
        $sql = $this->db->query("SELECT warehouse from branch where id_branch=" . $id);
        return $sql->row()->warehouse;
    }
    // Lucky Draw scheme group data
    function updateGroupCode($id_scheme_account)
    {
        $sql = $this->db->query("SELECT sch.is_lucky_draw,sa.id_scheme_account,sch.code,sa.group_code,sch.id_scheme, sa.id_branch,max_members
							FROM scheme_account sa  
							LEFT JOIN scheme sch ON sch.id_scheme = sa.id_scheme 
							join chit_settings cs
							WHERE  id_scheme_account =" . $id_scheme_account);
        $accData = $sql->row_array();
        $accData = $sql->row_array();
        if ($sql->num_rows() > 0) {
            if (strlen($accData['group_code']) == 0 && $accData['is_lucky_draw'] == 1) {
                $max_members = $accData['max_members'];
                $id_scheme = $accData['id_scheme'];
                //$id_branch = $accData['id_branch'];
                $id_branch = '';
                $id_scheme_account = $accData['id_scheme_account'];
                $group_code = $accData['code']; // Scheme Master Code
                // scheme_group - status => 0 - Upcoming, 1 - Active, 2 - Reached Limit, 3 - Group closed
                // Get active group
                $sql_1 = $this->db->query("SELECT id_scheme_group,group_code_param_1,group_code_param_2,group_code,group_code_suffix FROM `scheme_group` where " . ($id_branch > 0 ? ' id_branch = ' . $id_branch . ' and' : '') . " status = 1 and id_scheme = " . $id_scheme);
                $count_sql = $this->db->query("SELECT COUNT(id_scheme_group)+1 as group_count FROM `scheme_group` where " . ($id_branch > 0 ? ' id_branch = ' . $id_branch . ' and' : '') . " id_scheme = " . $id_scheme);
                //echo $this->db->last_query();exit;
                $count = $count_sql->row_array();
                if ($sql_1->num_rows() > 0) {
                    $active_group = $sql_1->row_array();
                    // Get group members count
                    $sql_2 = $this->db->query("SELECT count(id_scheme_account) as accounts FROM `scheme_account` where" . ($id_branch > 0 ? ' id_branch = ' . $id_branch . ' and' : '') . " id_scheme = " . $id_scheme . " and group_code = '" . $active_group['group_code'] . "' and scheme_acc_number is not null and scheme_acc_number != '' ");
                    $accounts = $sql_2->row()->accounts;
                    if ($accounts >= $max_members) { // Reached group limit, update status of current group and create new scheme group
                        // Update current group status as 2
                        $updData = array(
                            "status" => 2,  // Reached Limit
                            "last_update" => date("Y-m-d H:i:s")
                        );
                        $this->db->where('id_scheme_group', $active_group['id_scheme_group']);
                        $upd_status = $this->db->update("scheme_group", $updData);
                        // Create new scheme group
                        $group_code_suffix = ++$active_group['group_code_suffix'];
                        $group_code_param_1 = $active_group['group_code_param_1'];
                        $group_code_param_2 = $active_group['group_code_param_2'];
                        //$group_code = $group_code_param_1.''.$group_code_param_2.''.$group_code_suffix;
                        $group_code = $group_code_param_1 . '-' . $count['group_count'];
                        $insData = array(
                            "id_scheme" => $id_scheme,
                            "id_branch" => $id_branch,
                            "group_code" => $group_code,
                            "group_code_param_1" => $group_code_param_1,
                            "group_code_param_2" => $group_code_param_2,
                            "group_code_suffix" => $group_code_suffix,
                            "status" => 1,
                            "added_by" => 1,
                            "date_add" => date("Y-m-d H:i:s")
                        );
                        //	print_r($insData);exit;
                        $ins = $this->insertData($insData, "scheme_group");
                    } else if ($accounts < $max_members) {
                        $id_scheme_group = $active_group['id_scheme_group'];
                        $group_code = $active_group['group_code'];
                    }
                } else {             // Create new scheme group 
                    $insData = array(
                        "id_scheme" => $id_scheme,
                        "id_branch" => $id_branch,
                        "group_code" => $group_code . '-' . $count['group_count'],
                        "group_code_param_1" => $group_code,
                        "group_code_param_2" => NULL,
                        "group_code_suffix" => 1,
                        "status" => 1,
                        "added_by" => 1,
                        "date_add" => date("Y-m-d H:i:s")
                    );
                    $group_code = $group_code . '-' . $count['group_count'];
                    $ins = $this->insertData($insData, "scheme_group");
                }
                // Update group code in scheme account table
                if (strlen($group_code) > 0) {
                    $updAccData = array(
                        "group_code" => $group_code,
                        "date_upd" => date("Y-m-d H:i:s")
                    );
                    $this->db->where('id_scheme_account', $id_scheme_account);
                    $status = $this->db->update("scheme_account", $updAccData);
                    return $status;
                } else {
                    return FALSE;
                }
            } else {
                return TRUE;
            }
        } else {
            return TRUE;
        }
    }
    // created by RK - 14/12/2022
    // for getting giftOTP_exp from chit_settings table
    function gift_expotp()
    {
        $sql = "Select giftOTP_exp from chit_settings where id_chit_settings = 1";
        return $this->db->query($sql)->row()->giftOTP_exp;
    }
    /* account and payment edit block starts */
    function getPaymentDataByID($id_payment)
    {
        $sql = $this->db->query("SELECT * from payment where id_payment=" . $id_payment);
        //print_r($sql->row_array());exit;
        return $sql->row_array();
    }
    function getPaymentModeDetailsDataByID($id_payment)
    {
        $sql = $this->db->query("SELECT * from payment_mode_details where id_payment=" . $id_payment . " and is_active=1");
        //print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function getSchAccByID($id_scheme_account)
    {
        $sql = $this->db->query("SELECT sa.id_scheme_account,sa.id_customer,sa.account_name,sa.scheme_acc_number,c.mobile,
        sa.start_date, sa.maturity_date, s.total_installments, s.installment_cycle, s.ins_days_duration, s.maturity_days,
        DATE_FORMAT(
            IFNULL(sa.maturity_date,
                IF(s.maturity_days IS NOT NULL AND s.maturity_days > 0,
                    DATE_ADD(sa.start_date, INTERVAL s.maturity_days DAY),
                    IF(
                        s.installment_cycle = 0,
                        DATE_ADD(sa.start_date, INTERVAL s.total_installments MONTH),
                        IF(
                            s.installment_cycle = 1,
                            DATE_ADD(sa.start_date, INTERVAL s.total_installments DAY),
                            IF(
                                s.installment_cycle = 2,
                                DATE_ADD(sa.start_date, INTERVAL (s.ins_days_duration * s.total_installments) DAY),
                                NULL
                            )
                        )
                    )
                )
            ),
            '%Y-%m-%d'
        ) AS display_maturity_date
        from scheme_account sa
        left join customer c on c.id_customer=sa.id_customer
        left join scheme s on s.id_scheme=sa.id_scheme
        where sa.id_scheme_account=" . $id_scheme_account);
        return $sql->row_array();
    }
    function calcMaturityDate($start_date, $id_scheme_account)
    {
        $sql = $this->db->query("SELECT 
        DATE_FORMAT(
            IF(s.maturity_days IS NOT NULL AND s.maturity_days > 0,
                DATE_ADD('" . $start_date . "', INTERVAL s.maturity_days DAY),
                IF(
                    s.installment_cycle = 0,
                    DATE_ADD('" . $start_date . "', INTERVAL s.total_installments MONTH),
                    IF(
                        s.installment_cycle = 1,
                        DATE_ADD('" . $start_date . "', INTERVAL s.total_installments DAY),
                        IF(
                            s.installment_cycle = 2,
                            DATE_ADD('" . $start_date . "', INTERVAL (s.ins_days_duration * s.total_installments) DAY),
                            NULL
                        )
                    )
                )
            ),
            '%Y-%m-%d'
        ) AS maturity_date
        FROM scheme_account sa
        LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
        WHERE sa.id_scheme_account = " . $id_scheme_account);
        return $sql->row()->maturity_date;
    }
    function updatePaymentdata($postdata)
    {
        $upd_data = array();
        $updmode_data = array();
        //getting data from payment table for postdata -> id_payment
        $pay_data = $this->getPaymentDataByID($postdata['id_payment']);
        $this->db->trans_begin();
        if ($pay_data['id_scheme_account'] != $postdata['id_scheme_account']) {
            $sql = $this->db->query("SELECT id_scheme_account from scheme_account where id_scheme_account=" . $postdata['id_scheme_account']);
            if ($sql->num_rows() == 1) {
                $upd_data['id_scheme_account'] = $postdata['id_scheme_account'];
            } else {
                return array("status" => FALSE, "msg" => "Not a Valid Scheme Account");
            }
        } else {
            $upd_data['id_scheme_account'] = $pay_data['id_scheme_account'];
        }
        $upd_data['date_payment'] = $postdata['date_payment'] != '' && $postdata['date_payment'] != NULL && $pay_data['added_by'] != 2 ? $postdata['date_payment'] : $pay_data['date_payment'];
        $upd_data['metal_rate'] = $postdata['metal_rate'] != '' && $postdata['metal_rate'] != NULL && $postdata['metal_rate'] != $pay_data['metal_rate'] ? $postdata['metal_rate'] : $pay_data['metal_rate'];
        $upd_data['metal_weight'] = $postdata['metal_weight'] != '' && $postdata['metal_weight'] != NULL && $postdata['metal_weight'] != $pay_data['metal_weight'] ? $postdata['metal_weight'] : $pay_data['metal_weight'];
        $upd_data['receipt_no'] = $postdata['receipt_no'] != '' && $postdata['receipt_no'] != NULL && $postdata['receipt_no'] != $pay_data['receipt_no'] ? $postdata['receipt_no'] : $pay_data['receipt_no'];
        $upd_data['payment_status'] = $postdata['payment_status'] != '' && $postdata['payment_status'] != NULL && $postdata['payment_status'] != $pay_data['payment_status'] && $pay_data['added_by'] != 2 ? $postdata['payment_status'] : $pay_data['payment_status'];
        
        // --- due_date / due_date_to calculation based on scheme installment_cycle ---
        $target_sch_acc = $upd_data['id_scheme_account'];
        $target_date_payment = date('Y-m-d', strtotime($upd_data['date_payment']));
        $date_changed = ($target_date_payment != date('Y-m-d', strtotime($pay_data['date_payment'])));
        $acc_changed = ($target_sch_acc != $pay_data['id_scheme_account']);
        
        // Recalculate due dates if date or account changed
        if ($date_changed || $acc_changed) {
            $sch_settings = $this->db->query("SELECT s.installment_cycle, s.ins_days_duration, 
                s.payment_chances, sa.start_date, s.is_digi, s.interest, s.id_metal, 
                sa.id_branch, sa.id_scheme, sa.id_customer, s.scheme_type, s.flexible_sch_type
                FROM scheme_account sa 
                LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme 
                WHERE sa.id_scheme_account = " . $target_sch_acc);
            
            if ($sch_settings->num_rows() > 0) {
                $sch = $sch_settings->row();
                
                // --- Restrict payment date before scheme account start_date ---
                $acc_start_date = date('Y-m-d', strtotime($sch->start_date));
                if ($target_date_payment < $acc_start_date) {
                    $this->db->trans_rollback();
                    return array("status" => FALSE, "msg" => "Payment date (" . $target_date_payment . ") cannot be before the scheme account start date (" . $acc_start_date . ").");
                }
                
                // --- Duplicate payment validation (only for single payment schemes) ---
                // payment_chances: 0 = single payment per cycle, 1 = multiple (no limit check needed)
                $payment_chances = intval($sch->payment_chances);
                
                if ($payment_chances == 0) {
                    if ($sch->installment_cycle == 1 || $sch->installment_cycle == 3) {
                        // Daily scheme — check same exact date for successful payments
                        $existing_count = $this->db->query("SELECT COUNT(*) as cnt FROM payment 
                            WHERE id_scheme_account = " . $target_sch_acc . " 
                            AND DATE(date_payment) = '" . $target_date_payment . "' 
                            AND payment_status = 1 
                            AND id_payment != " . $postdata['id_payment'])->row()->cnt;
                        
                        if ($existing_count > 0) {
                            $this->db->trans_rollback();
                            return array("status" => FALSE, "msg" => "A successful payment already exists on " . $target_date_payment . " for this scheme account. Only single payment per day is allowed.");
                        }
                    } else if ($sch->installment_cycle == 0) {
                        // Monthly scheme — check same month for successful payments
                        $existing_count = $this->db->query("SELECT COUNT(*) as cnt FROM payment 
                            WHERE id_scheme_account = " . $target_sch_acc . " 
                            AND DATE_FORMAT(date_payment, '%Y-%m') = DATE_FORMAT('" . $target_date_payment . "', '%Y-%m') 
                            AND payment_status = 1 
                            AND id_payment != " . $postdata['id_payment'])->row()->cnt;
                        
                        if ($existing_count > 0) {
                            $this->db->trans_rollback();
                            return array("status" => FALSE, "msg" => "A successful payment already exists in " . date('F Y', strtotime($target_date_payment)) . " for this scheme account. Only single payment per month is allowed.");
                        }
                    } else if ($sch->installment_cycle == 2) {
                        // Custom days duration — check within the same cycle range for successful payments
                        $start_date = date('Y-m-d', strtotime($sch->start_date));
                        $days_duration = intval($sch->ins_days_duration);
                        if ($days_duration > 0) {
                            $days_diff = (strtotime($target_date_payment) - strtotime($start_date)) / 86400;
                            $cycle_index = floor($days_diff / $days_duration);
                            $cycle_start = date('Y-m-d', strtotime($start_date . ' + ' . ($cycle_index * $days_duration) . ' days'));
                            $cycle_end = date('Y-m-d', strtotime($cycle_start . ' + ' . ($days_duration - 1) . ' days'));
                            
                            $existing_count = $this->db->query("SELECT COUNT(*) as cnt FROM payment 
                                WHERE id_scheme_account = " . $target_sch_acc . " 
                                AND DATE(date_payment) BETWEEN '" . $cycle_start . "' AND '" . $cycle_end . "' 
                                AND payment_status = 1 
                                AND id_payment != " . $postdata['id_payment'])->row()->cnt;
                            
                            if ($existing_count > 0) {
                                $this->db->trans_rollback();
                                return array("status" => FALSE, "msg" => "A successful payment already exists in cycle (" . $cycle_start . " to " . $cycle_end . ") for this scheme account. Only single payment per cycle is allowed.");
                            }
                        }
                    }
                }
                // payment_chances == 1 (multiple) — no restriction, allow editing freely
                
                // --- Calculate due_date and due_date_to based on installment_cycle ---
                if ($sch->installment_cycle == 1 || $sch->installment_cycle == 3) {
                    // Daily scheme: due_date = due_date_to = payment date
                    $upd_data['due_date'] = $target_date_payment;
                    $upd_data['due_date_to'] = $target_date_payment;
                } else if ($sch->installment_cycle == 0) {
                    // Monthly scheme: due_date = first day of month, due_date_to = last day of month
                    $upd_data['due_date'] = date('Y-m-01', strtotime($target_date_payment));
                    $upd_data['due_date_to'] = date('Y-m-t', strtotime($target_date_payment));
                } else if ($sch->installment_cycle == 2) {
                    // Custom days duration cycle: calculate based on start_date and ins_days_duration
                    $start_date = date('Y-m-d', strtotime($sch->start_date));
                    $days_duration = intval($sch->ins_days_duration);
                    if ($days_duration > 0) {
                        $days_diff = (strtotime($target_date_payment) - strtotime($start_date)) / 86400;
                        $cycle_index = floor($days_diff / $days_duration);
                        $cycle_start = date('Y-m-d', strtotime($start_date . ' + ' . ($cycle_index * $days_duration) . ' days'));
                        $cycle_end = date('Y-m-d', strtotime($cycle_start . ' + ' . ($days_duration - 1) . ' days'));
                        $upd_data['due_date'] = $cycle_start;
                        $upd_data['due_date_to'] = $cycle_end;
                    }
                }
                // installment_cycle == 4 (custom payable) — skip due_date recalculation as it depends on slab data
                
                // --- Digi scheme benefit recalculation ---
                if ($sch->is_digi == 1 && $sch->interest == 1) {
                    // Calculate date_difference (day number from start_date)
                    $acc_start = date('Y-m-d', strtotime($sch->start_date));
                    $date_diff = max(1, (strtotime($target_date_payment) - strtotime($acc_start)) / 86400);
                    if ($target_date_payment == $acc_start) {
                        $date_diff = 1;
                    }
                    
                    // Query benefit chart for the matching slab
                    $benefit_data = $this->db->query("SELECT interest_type, interest_value, commodity 
                        FROM scheme_benefit_deduct_settings 
                        WHERE ('" . $date_diff . "' BETWEEN installment_from AND installment_to) 
                        AND id_scheme = " . $sch->id_scheme)->result_array();
                    
                    if (!empty($benefit_data)) {
                        $payment_amount = floatval($pay_data['payment_amount']);
                        $metal_rate = floatval($upd_data['metal_rate'] > 0 ? $upd_data['metal_rate'] : $pay_data['metal_rate']);
                        $metal_wgt = floatval($upd_data['metal_weight'] > 0 ? $upd_data['metal_weight'] : $pay_data['metal_weight']);
                        
                        foreach ($benefit_data as $benefit) {
                            if ($benefit['commodity'] == $sch->id_metal) {
                                // Primary benefit (same metal as scheme)
                                if ($benefit['interest_type'] == 0) {
                                    // Percentage benefit
                                    $upd_data['saved_benefit_amt'] = $payment_amount * ($benefit['interest_value'] / 100);
                                } else {
                                    // Fixed amount benefit
                                    $upd_data['saved_benefit_amt'] = $benefit['interest_value'];
                                }
                                $upd_data['saved_benefits'] = ($metal_rate > 0 ? formatMetalWeight($upd_data['saved_benefit_amt'] / $metal_rate) : 0);
                                $upd_data['benefit_value'] = $benefit['interest_value'];
                                $upd_data['benefit_type'] = $benefit['interest_type'];
                            } else {
                                // Other commodity benefit
                                $other_metal_rate = $this->get_metalrate_by_branch($sch->id_branch, $benefit['commodity'], '');
                                if ($benefit['interest_type'] == 0) {
                                    $upd_data['dg_other_benefit_wgt'] = $metal_wgt * ($benefit['interest_value'] / 100);
                                    $upd_data['dg_other_benefit_amt'] = $upd_data['dg_other_benefit_wgt'] * $other_metal_rate;
                                } else {
                                    $upd_data['dg_other_benefit_amt'] = $benefit['interest_value'];
                                    $upd_data['dg_other_benefit_wgt'] = ($other_metal_rate > 0 ? $upd_data['dg_other_benefit_amt'] / $other_metal_rate : 0);
                                }
                                $upd_data['dg_other_benefit_wgt'] = number_format(floor(floatval($upd_data['dg_other_benefit_wgt']) * 1000) / 1000, 3, '.', '');
                            }
                        }
                    } else {
                        // No matching slab found — clear benefits
                        $upd_data['saved_benefits'] = 0;
                        $upd_data['saved_benefit_amt'] = 0;
                        $upd_data['benefit_value'] = 0;
                        $upd_data['benefit_type'] = 0;
                        $upd_data['dg_other_benefit_wgt'] = NULL;
                        $upd_data['dg_other_benefit_amt'] = NULL;
                    }
                } else if ($sch->is_digi != 1 && $acc_changed) {
                    // Account changed from digi to non-digi — clear benefit fields
                    $upd_data['saved_benefits'] = 0;
                    $upd_data['saved_benefit_amt'] = 0;
                    $upd_data['benefit_value'] = 0;
                    $upd_data['benefit_type'] = 0;
                    $upd_data['dg_other_benefit_wgt'] = NULL;
                    $upd_data['dg_other_benefit_amt'] = NULL;
                }
            }
        }
        
        // --- Installment number (due_monthyear) update based on start_date ---
        if (($date_changed || $acc_changed) && isset($sch)) {
            // Calculate installment number directly from start_date and installment_cycle
            // (get_due_date uses first_payment_date which can be unreliable in edit context)
            $new_installment = 0;
            $ins_start = date('Y-m-d', strtotime($sch->start_date));
            
            if ($sch->installment_cycle == 1 || $sch->installment_cycle == 3) {
                // Daily scheme: installment = days from start + 1
                $new_installment = max(1, intval((strtotime($target_date_payment) - strtotime($ins_start)) / 86400) + 1);
            } else if ($sch->installment_cycle == 0) {
                // Monthly scheme: installment = month diff + 1
                $new_installment = max(1, intval((date('Y', strtotime($target_date_payment)) - date('Y', strtotime($ins_start))) * 12 
                    + (date('m', strtotime($target_date_payment)) - date('m', strtotime($ins_start)))) + 1);
            } else if ($sch->installment_cycle == 2) {
                // Custom days duration: installment = cycle index + 1
                $days_duration = intval($sch->ins_days_duration);
                if ($days_duration > 0) {
                    $days_diff = (strtotime($target_date_payment) - strtotime($ins_start)) / 86400;
                    $new_installment = max(1, intval(floor($days_diff / $days_duration)) + 1);
                }
            }
            
            if ($new_installment > 0) {
                // Only check duplicate slots for single-payment-per-cycle schemes
                if (isset($payment_chances) && $payment_chances == 0) {
                    // Use due_date from upd_data (already calculated above) for duplicate check
                    $check_due_date = isset($upd_data['due_date']) ? $upd_data['due_date'] : $pay_data['due_date'];
                    $dup_check = $this->db->query("SELECT id_payment FROM payment 
                        WHERE id_scheme_account = " . $target_sch_acc . " 
                        AND due_date = '" . $check_due_date . "' 
                        AND payment_status = 1 
                        AND id_payment != " . $postdata['id_payment'])->row();
                    
                    if (!empty($dup_check)) {
                        $this->db->trans_rollback();
                        return array("status" => FALSE, "msg" => "Installment #" . $new_installment . " (due: " . $check_due_date . ") already has a payment (ID: " . $dup_check->id_payment . ") for this account.");
                    }
                }
                
                $upd_data['installment'] = $new_installment;
                $upd_data['due_monthyear'] = $new_installment;
            }
        }
        
        // --- No-change detection: compare upd_data vs existing pay_data ---
        $has_changes = false;
        $compare_fields = array('id_scheme_account', 'date_payment', 'metal_rate', 'metal_weight', 'receipt_no', 'payment_status');
        foreach ($compare_fields as $field) {
            if (isset($upd_data[$field])) {
                $old_val = isset($pay_data[$field]) ? trim($pay_data[$field]) : '';
                $new_val = trim($upd_data[$field]);
                // Normalize date comparison
                if ($field == 'date_payment') {
                    $old_val = date('Y-m-d', strtotime($old_val));
                    $new_val = date('Y-m-d', strtotime($new_val));
                }
                // Normalize numeric comparison
                if (in_array($field, array('metal_rate', 'metal_weight'))) {
                    $old_val = floatval($old_val);
                    $new_val = floatval($new_val);
                }
                if ($old_val != $new_val) {
                    $has_changes = true;
                    break;
                }
            }
        }
        // Also check benefit fields if they were recalculated
        if (!$has_changes && isset($upd_data['saved_benefits'])) {
            if (floatval($upd_data['saved_benefits']) != floatval($pay_data['saved_benefits'])) {
                $has_changes = true;
            }
        }
        if (!$has_changes && isset($upd_data['installment'])) {
            if ($upd_data['installment'] != $pay_data['installment']) {
                $has_changes = true;
            }
        }
        
        if (!$has_changes) {
            $this->db->trans_rollback();
            return array("status" => TRUE, "msg" => "No changes detected.", "no_change" => true);
        }
        
        $temp = array();
        //updating payment table for postdata -> id_payment
        $result = $this->updData($upd_data, 'id_payment', $postdata['id_payment'], 'payment');
        if ($result > 0) {
            //getting data from payment_mode_details for postdata->id_payment
            $paymodedetails = $this->getPaymentModeDetailsDataByID($postdata['id_payment']);
            $update_pay = array(
                'is_active' => 0,
                'updated_by' => $this->session->userdata('uid'),
                "updated_time" => date('Y-m-d H:i:s')
            );
            //updating the old entry of postdata->id_payment with is_active =0
            $updated = $this->update_modestatus_data($update_pay, $postdata['id_payment']);
            //adding new entry to payment_mode_details
            if ($paymodedetails != null) {
                $new_mode_entry = [];
                foreach ($paymodedetails as $pmode) {
                    foreach ($pmode as $key => $value) {
                        if ($key != id_pay_mode_details) {
                            if ($key == 'payment_status') {
                                $temp[$key] = $postdata['payment_status'];
                            } else if ($key == 'payment_date') {
                                $temp[$key] = $postdata['date_payment'];
                            } else if ($key == 'created_by') {
                                $temp[$key] = $this->session->userdata('uid');
                            } else if ($key == 'created_time') {
                                $temp[$key] = date("Y-m-d H:i:s");
                            } else {
                                $temp[$key] = $value;
                            }
                        }
                    }
                    $new_mode_entry[] = $temp;
                }
                $modeInsert = $this->insertBatchData($new_mode_entry, 'payment_mode_details');
            }
        }
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return array("status" => TRUE, "msg" => "Payment details Updated Successfully");
        } else {
            $this->db->trans_rollback();
            return array("status" => FALSE, "msg" => "Unable to proceed your request");
        }
        /* if($result > 0)
        {
            return array("status" => TRUE,"msg" => "Payment details Updated Successfully");
        }
        else
        {
            return array("status" => FALSE,"msg" => "Unable to proceed your request");
        }*/
    }
    /*account and payment edit block ends*/
    /*ends*/
    function update_dueMonYear($id_scheme_account)
    {
        $get_dueData = $this->db->query("SELECT p.id_payment,p.id_scheme_account,date(p.date_payment), p.due_type, 
        IF(Date_Format(date(p.date_payment),'%Y-%m') != @prev_paid_date && due_type != 'AD', @months:=0, @months:=@months+1) as m,
        if(due_type = 'AD',  Date_Format((date_add(date(p.date_payment), INTERVAL @months month)),'%Y-%m')  ,  if(due_type = 'PD',null,Date_Format(date(p.date_payment),'%Y-%m'))) as paidmonth,
        @prev_paid_date := Date_Format(date(p.date_payment),'%Y-%m')
        FROM payment p 
         join (SELECT @months:= 0, @prev_paid_date := '') months 
         where p.id_scheme_account = " . $id_scheme_account . " 
         order by p.id_scheme_account,p.date_payment ASC")->result_array();
        if (sizeof($get_dueData) > 0) {
            foreach ($get_dueData as $due) {
                $updData = array("due_monthyear" => $due['paidmonth']);
                $this->db->where('id_payment', $due['id_payment']);
                $status = $this->db->update("payment", $updData);
            }
        } else {
            $status = 1;
        }
        return $status;
    }
    //function added by Durga (Gopal task) 15.05.2023 starts here
    function getPayDetails($id_scheme_account)
    {
        $sql = $this->db->query("SELECT s.is_topup_scheme,p.id_payment,p.id_scheme_account,sa.id_employee
        FROM payment p
        LEFT JOIN scheme_account sa ON sa.id_scheme_account=p.id_scheme_account
        LEFT JOIN scheme s ON s.id_scheme=sa.id_scheme
        WHERE p.id_scheme_account=" . $id_scheme_account . " and p.payment_status=1 ORDER by p.id_payment ASC ");
        // print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    //function added by Durga (Gopal task) 15.05.2023 ends here
    function getPayData($txnid)
    {
        $sql = "Select sa.ref_no,flexible_sch_type,sa.id_customer,firstPayment_amt,s.firstPayamt_as_payamt,s.firstPayamt_maxpayable,p.id_payment,sa.id_scheme_account,sa.scheme_acc_number,sa.id_scheme,cs.schemeacc_no_set,cs.receipt_no_set,cs.scheme_wise_receipt,p.ref_trans_id,cs.edit_custom_entry_date,
    	cs.custom_entry_date,p.payment_amount,s.one_time_premium,sa.id_branch as branch,cs.allow_referral,cs.gent_clientid,s.firstPayamt_maxpayable,is_lucky_draw,c.mobile,p.pay_email as email,sum(p.payment_amount) as pay_amt
    			 From payment p
    			 left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
    			 left join customer c on c.id_customer= sa.id_customer
    			 left join scheme s on s.id_scheme=sa.id_scheme
    			 join chit_settings cs
    			 Where p.ref_trans_id='" . $txnid . "'";
        return $this->db->query($sql)->result_array();
    }
    function update_receipt($id, $data)
    {
        $this->db->where('id_payment', $id);
        $status = $this->db->update('payment', $data);
        return $status;
    }
    //Scheme wise mode wise report starts here
    // Function get_group_modewise_list added by Durga 29-06-2023
    function get_group_modewise_list($from_date, $to_date, $id = "")  // esakki 11-11
    {
        $branch_settings = $this->session->userdata('branch_settings');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $id_company = $this->session->userdata('id_company');
        $company_settings = $this->session->userdata('company_settings');
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
        } else {
            $id_branch = '';
        }
        $sql = "SELECT @a:=@a+1 as sno,
		p.id_payment,
		pmd.id_payment as pmdid,
		p.payment_amount,
		IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment,
		date(pmd.payment_date) as pmddate,s.code,s.scheme_name, 
		IFNULL(Date_format(p.custom_entry_date,'%d-%m%-%Y'),'-') as entry_Date,
		cs.edit_custom_entry_date, 
		SUM(pmd.payment_amount) as payment_amounts, 
		sum(p.metal_weight) as total_weight, 
			IFNULL(sum(
				Case When pmd.payment_mode='CSH' and (p.added_by=0 || p.added_by=6)
			then pmd.payment_amount End),0) as offline_cash, 
			IFNULL(sum(
				Case When (pmd.payment_mode='CC'|| pmd.payment_mode='DC') and (p.added_by=0 || p.added_by=6)
			then pmd.payment_amount End),0) as offline_card, 
			IFNULL(sum(
				Case When (pmd.payment_mode='CHQ') and (p.added_by=0 || p.added_by=6)
			then pmd.payment_amount End),0) as offline_cheque, 
			IFNULL(sum(
				Case When (pmd.payment_mode='NB' and pmd.NB_type != 3) and (p.added_by=0 || p.added_by=6)
			then pmd.payment_amount End),0) as offline_nb, 
			IFNULL(sum(
				Case When (pmd.payment_mode='NB' and pmd.NB_type = 3) and (p.added_by=0 || p.added_by=6)
			then pmd.payment_amount End),0) as offline_upi, 
			IFNULL(sum(
				Case When (pmd.payment_mode!='UPI' && pmd.payment_mode!='NB' && pmd.payment_mode!='CHQ' && pmd.payment_mode!='CSH' && pmd.payment_mode!='CC' && pmd.payment_mode!='DC') and (p.added_by=0 || p.added_by=6)
			then pmd.payment_amount End),0) as offline_wallet, 
			IFNULL(sum(Case When pmd.payment_mode='CSH' and (p.added_by=1 || p.added_by=2 || p.added_by=3 || p.added_by=4 || p.added_by=5) Then pmd.payment_amount End),0) as online_cash, 
			IFNULL(sum(Case When (pmd.payment_mode='CC' || pmd.payment_mode='DC')  and (p.added_by=1 || p.added_by=2 || p.added_by=3 || p.added_by=4 || p.added_by=5) Then pmd.payment_amount End),0) as online_card, 
			IFNULL(sum(Case When (pmd.payment_mode='CHQ')  and (p.added_by=1 || p.added_by=2 || p.added_by=3 || p.added_by=4 || p.added_by=5) Then pmd.payment_amount End),0) as online_cheque, 
			IFNULL(sum(Case When (pmd.payment_mode='NB' and pmd.NB_type != 3)  and (p.added_by=1 || p.added_by=2 || p.added_by=3 || p.added_by=4 || p.added_by=5) Then pmd.payment_amount End),0) as online_nb, 
			IFNULL(sum(Case When (pmd.payment_mode='UPI' and p.id_payGateway > 0)  and (p.added_by=1 || p.added_by=2 || p.added_by=3 || p.added_by=4 || p.added_by=5) Then pmd.payment_amount End),0) as online_upi, 
			IFNULL(sum(Case When (pmd.payment_mode!='UPI' && pmd.payment_mode!='NB' && pmd.payment_mode!='CHQ' && pmd.payment_mode!='CSH' && pmd.payment_mode!='CC' && pmd.payment_mode!='DC')  and (p.added_by=1 || p.added_by=2 || p.added_by=3 || p.added_by=4 || p.added_by=5) Then pmd.payment_amount End),0) as online_wallet 
			FROM payment p 
			join chit_settings cs 
			join (SELECT @a:= 0) a 
			left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account) 
			Left Join branch b On (b.id_branch=p.id_branch) 
			left join scheme s on(sa.id_scheme=s.id_scheme) 
			left JOIN payment_mode_details pmd on (p.id_payment=pmd.id_payment)
			Where  (date(p.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
		" . ($id != '' ? "  And s.id_scheme=" . $id : '') . "
            " . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . "
		And p.payment_status=1 and (pmd.payment_status=1 and pmd.is_active=1)   " . ($uid != 1 ? ($branch_settings == 1 ? ($id_branch != '' && $id_branch != 0 ? "and p.id_branch IN(" . $id_branch . ")" : ($branch != '' ? "and(p.id_branch IN(" . $branch . ") or b.show_to_all=2)" : '')) : '') : ($id_branch != '' && $id_branch != 0 ? "and p.id_branch IN(" . $id_branch . ")" : '')) . "
			group by date(pmd.payment_date),sa.id_scheme order by sa.id_scheme";
        //print_r($sql);exit;
        $payment = $this->db->query($sql);
        $pay_details = $payment->result_array();
        foreach ($pay_details as $r) {
            $return_data[$r['scheme_name']][] = $r;
        }
        return $return_data;
    }
    //Scheme wise mode wise report ends here
    function getAllsubPayments($id)
    {
        $subPay = $this->db->query("SELECT * FROM payment_mode_details where id_payment = " . $id . "and is_active=1")->result_array();
        return $subPay;
    }
    function getOrderIds($txnid)
    {
        $sql = "Select sa.id_customer,firstPayment_amt,s.code as group_code,s.sync_scheme_code,sa.id_branch as branch,cs.scheme_wise_acc_no,cs.gent_clientid,firstPayamt_as_payamt,s.firstPayamt_maxpayable,p.id_payment,sa.id_scheme_account,sa.scheme_acc_number,sa.id_scheme,cs.schemeacc_no_set,cs.receipt_no_set,cs.scheme_wise_receipt,p.ref_trans_id,cs.edit_custom_entry_date,sa.custom_entry_date,p.payment_amount,flexible_sch_type,p.id_branch,s.is_lucky_draw, s.max_members, s.code,s.one_time_premium,p.id_transaction,p.offline_tran_uniqueid,b.warehouse,
	        p.payment_type,p.payment_mode,cs.allow_referral,s.agent_refferal,s.agent_credit_type,s.emp_refferal,sa.referal_code
			 From payment p
			 left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
			 LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
			 LEFT JOIN branch b ON b.id_branch = p.id_branch
			 join chit_settings cs
			 Where p.payment_ref_number='" . $txnid . "'";
        //print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
    function getRazorOrderid($txnid)
    {
        $sql = $this->db->query("select payment_ref_number from payment where payment_ref_number is not null and ref_trans_id='" . $txnid . "' group by payment_ref_number");
        //echo $this->db->last_query();exit;
        return $sql->row()->payment_ref_number;
    }
    function get_scheme_acc_data()
    {
        $sql = $this->db->query("SELECT sa.id_scheme_account,sa.scheme_acc_number
                                FROM scheme_account sa
                                LEFT JOIN payment p on (p.id_scheme_account = sa.id_scheme_account)
                                where p.payment_status = 1 and p.due_monthyear is null
                                GROUP BY p.id_scheme_account
                                limit 10000
                    ")->result_array();
        return $sql;
    }
    //function for updating in payment_mode_details table
    public function update_modestatus_data($data, $id)
    {
        $edit_flag = 0;
        $this->db->where("id_payment = " . $id . " AND is_active = 1");
        $edit_flag = $this->db->update('payment_mode_details', $data);
        //print_r($this->db->last_query());exit;
        return ($edit_flag == 1 ? $id_value : 0);
    }
    //   Member Report starts here
    function get_joined_through()
    {
        $data = array(
            array(
                'value' => 'all',
                'text' => 'All'
            ),
            array(
                'value' => 0,
                'text' => 'Web App'
            ),
            array(
                'value' => 1,
                'text' => 'Admin'
            ),
            array(
                'value' => 2,
                'text' => 'Mobile App'
            ),
            array(
                'value' => 3,
                'text' => 'Collection App'
            )
        );
        return $data;
    }
    function search_area($term = '')
    {
        $sql = "SELECT id_village, village_name FROM village";
        if (!empty($term)) {
            $safe_term = $this->db->escape_like_str($term);
            $sql .= " WHERE village_name LIKE '%" . $safe_term . "%'";
        }
        $sql .= " ORDER BY village_name ASC LIMIT 50";
        return $this->db->query($sql)->result_array();
    }
    function get_city()
    {
        $sql = "SELECT * from city";
        return $this->db->query($sql)->result_array();
    }
    function getMemberReport()
    {
        // IFNULL((select sum(payment_amount) from payment where id_scheme_account=sa.id_scheme_account and payment_status=1 group by MONTH(date_payment) order by date_payment asc limit 1),0) as first_installment_amount
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $report_type = $this->input->post('report_type');
        $account_type = $this->input->post('account_type');
        $id_scheme = $this->input->post('id_scheme');
        $id_branch = $this->input->post('id_branch');
        $id_village = $this->input->post('id_village');
        $added_by = $this->input->post('added_by');
        $id_village = $this->input->post('id_village');
        $emp_code = $this->input->post('emp_code');
        $login_branch = $this->session->userdata('id_branch');
        $id_company = $this->session->userdata('id_company');
        $company_settings = $this->session->userdata('company_settings');
        $sql = "SELECT cls.classification_name,
		sa.id_scheme_account,
		s.id_scheme,
		s.scheme_name,
		s.code,
		IFNULL(sa.account_name,'-') as acc_name,
		IFNULL(sa.scheme_acc_number,'NOT ALLOCATED') as scheme_acc_number,
		IFNULL(Date_format(sa.start_date,'%d-%m%-%Y'),'-') as start_date,
		sa.added_by,
		sa.id_branch,
		c.mobile,
		concat(c.firstname,' ',if(c.lastname!='' && c.lastname is not null,c.lastname,'')) as cus_name,
		IFNULL(b.name,'-') as joined_branch,
		IFNULL(Date_format(c.date_add,'%d-%m%-%Y'),'-') as cus_reg_date,
		concat(IFNULL(addr.address1,''),IFNULL(addr.address2,''),IFNULL(addr.address3,'')) as address,
		IFNULL(city.name,'') as city_name,
		IFNULL(sub_scheme_table.paid_installments,0) as paid_installments,
		IFNULL(concat(emp.firstname,' ',if(emp.lastname!='' && emp.lastname is not null,emp.lastname,'')),'-') as login_employee,
		IFNULL((select concat(IFNULL(e.firstname,''),' ',IFNULL(e.lastname,''),'-',IFNULL(e.emp_code,'')) from employee e left join scheme_account ssa on ssa.referal_code=e.emp_code WHERE " . ($id_company != '' && $company_settings == 1 ? " c.id_company = e.id_company and" : '') . " ssa.id_scheme_account=sa.id_scheme_account and ssa.referal_code is not null and ssa.referal_code!='' and ssa.is_refferal_by is not null and ssa.is_refferal_by=1),'-') as referred_employee,
		sa.is_closed,
		IFNULL(addr.address1,'') as address1,
		IFNULL(addr.address2,'') as address2,
		IFNULL(addr.address3,'') as address3,
		IFNULL(vill.village_name,'') as area,
		IFNULL(state.name,'') as state,
		IFNULL(addr.pincode,'') as pincode,
		IFNULL(country.name,'') as country,
		IFNULL((select payment_amount from payment where id_scheme_account=sa.id_scheme_account and payment_status=1 order by date_payment asc limit 1),0) as oldfirst_installment_amount,
        IFNULL((select payment_amount from payment where id_scheme_account=sa.id_scheme_account and payment_status=1 group by YEAR(date_payment), MONTH(date_payment) order by date_payment asc limit 1),0) as first_installment_amount
		FROM scheme_account sa
		left join scheme s on sa.id_scheme=s.id_scheme   
		left join (select IFNULL(IF(ssa.is_opening=1,IFNULL(ssa.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 AND sc.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) as paid_installments,
		 ssa.id_scheme_account 
		 from scheme_account ssa 
		 left join payment pay on ssa.id_scheme_account=pay.id_scheme_account 
		 left join scheme sc on ssa.id_scheme=sc.id_scheme
		 where pay.payment_status=1 and pay.id_scheme_account=ssa.id_scheme_account group by pay.id_scheme_account )sub_scheme_table
		 on sub_scheme_table.id_scheme_account=sa.id_scheme_account
		left join branch b on b.id_branch=sa.id_branch
		left join customer c on sa.id_customer=c.id_customer
		left join employee emp on sa.id_employee=emp.id_employee
		left join village vill on vill.id_village = c.id_village
		left join address addr on c.id_address=addr.id_address
		left join city city on city.id_city=addr.id_city
		left join state state on state.id_state=addr.id_state
		left join country country on country.id_country=addr.id_country
		join chit_settings cs
		left join sch_classify cls on (cls.id_classification = s.id_classification)
		where s.active=1 
		 " . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . " 
		" . (!empty($id_scheme) ? " and sa.id_scheme=" . $id_scheme : '') . "
		" . (!empty($id_village) ? " and c.id_village=" . $id_village : '') . "
		" . (!empty($emp_code) ? " and sa.referal_code=" . $emp_code : '') . "
		";
        if (!empty($login_branch)) {
            $sql = $sql . " and sa.id_branch=" . $login_branch . "";
        } else if (!empty($id_branch)) {
            $sql = $sql . " and sa.id_branch=" . $id_branch . "";
        }
        //added by checking 
        if ($added_by != null && $added_by != 'all') {
            if ($added_by == 1) {
                $sql = $sql . " and (sa.added_by=1 or sa.added_by=4 or sa.added_by=5 or sa.added_by=6)";
            } else {
                $sql = $sql . " and sa.added_by=" . $added_by;
            }
        }
        //if live member report is selected only single payment data has to be fetched
        if ($report_type == 2) {
            if ($account_type == 1) {
                $sql = $sql . " and sub_scheme_table.paid_installments>=1 and sa.active=1 ";
            } else if ($account_type == 2) {
                $sql = $sql . " and sub_scheme_table.paid_installments>=1 and sa.is_closed=1 ";
            } else {
                $sql = $sql . " and sub_scheme_table.paid_installments>=1";
            }
            if ($from_date != '' && $to_date != '') {
                $sql = $sql . " and date(start_date) between '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "'";
            }
        } else {
            $sql = $sql . " and sa.active=1 and sub_scheme_table.paid_installments>0";
        }
        $sql = $sql . " group by sa.id_scheme_account 
		order by s.id_scheme";
        // print_r($sql);exit;
        $result = [];
        $schemeCounts = [];
        $query_data = $this->db->query($sql);
        $account = $query_data->result_array();
        //getting joined through count
        $count_joined = $this->get_joined_through_count($account);
        if ($query_data->num_rows() > 0) {
            //getting formatted account number and filter accounts schemewise
            foreach ($account as $rcpt) {
                //if account and receipt number is not formatted based on settings ,pls ignore this line
                $rcpt['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $rcpt['id_scheme_account']);
                //accounts are filtered schemewise 
                $return_data[$rcpt['classification_name']][$rcpt['scheme_name']][] = $rcpt;
            }
            //getting accountcounts of each scheme
            foreach ($return_data as $classification_name => $subArray) {
                foreach ($subArray as $key => $value) {
                    $count = count($value);
                    //$schemeCounts[$classification_name][$key] = $count;
                    $schemeCounts[$classification_name][$key] = $value;
                    $detailed_data[$key] = $value;
                }
            }
        }
        $member_data['detailed_data'] = $detailed_data;
        $member_data['scheme_count_data'] = $schemeCounts;
        $member_data['joined_through_count'] = $count_joined;
        //print_r($schemeCounts);exit;
        //print_r($result);exit;
        return $member_data;
    }
    //function to get count of joined through (each mode individual count)
    function get_joined_through_count($account)
    {
        $admin_app = 0;
        $web_app = 0;
        $mobile_app = 0;
        $collection_app = 0;
        $retail_app = 0;
        $sync = 0;
        $import = 0;
        foreach ($account as $acc) {
            switch ($acc['added_by']) {
                case 0:
                    $web_app++;
                    break;
                case 1:
                    $admin_app++;
                    break;
                case 2:
                    $mobile_app++;
                    break;
                case 3:
                    $collection_app++;
                    break;
                case 4:
                    $admin_app++;
                    break;
                case 5:
                    $admin_app++;
                    break;
                case 6:
                    $admin_app++;
                    break;
            }
        }
        $mode_count[] = array(
            'ADMIN' => $admin_app,
            'WEB APP' => $web_app,
            'MOBILE APP' => $mobile_app,
            'COLLECTION APP' => $collection_app,
            //	'RETAIL APP'=>$retail_app,
            //	'SYNC'=>$sync,
            //	'IMPORT'=>$import,
        );
        //print_r($mode_count);exit;
        return $mode_count;
    }
    //   Member Report ends here
    //RHR schemes : by 30 days payment cycle with advance , pending flow.. also has benefits for pre closed and one month bonus maturity closed ....20-09-2023 #AB....
    function get_due_date($due_type, $date_payment, $id_scheme_account)
    {
        $result = [];
        $where = '';
        $sch = $this->get_scheme_details($id_scheme_account);
        if (sizeof($sch) == 0) {
            return [];
        }
        $now = date('Y-m-d');
        $date_payment = date('Y-m-d', strtotime($date_payment));
        $first_payment_date = (!empty($sch['first_payment_date']) ? $sch['first_payment_date'] : $date_payment);
        if ($sch['maturity_type'] != 2 && ($now > $sch['maturity_date'] || $now > $sch['due_cycle_end']) && $due_type == 'current_range' && $sch['total_paid_ins'] > 0) {
            $sch['total_installments'] = max(1, intval($sch['till_days_ins']) - 1);
        }
        // Ensure total_installments is always positive to avoid invalid LIMIT clause
        if (empty($sch['total_installments']) || $sch['total_installments'] < 1) {
            $sch['total_installments'] = 1;
        }
        $c_wh = "and  dt.due_date_from NOT IN (SELECT p.due_date from payment p where p.payment_status = 1 and p.due_date is not null and p.id_scheme_account = sa.id_scheme_account) limit 1";
        if ($due_type == 'ND' || $due_type == '') {
            $where = "and  date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.due_date_to " . $c_wh . " ";
        } else if ($due_type == 'AD') {
            $where = "and dt.due_date_from >= date('" . $date_payment . "')  " . $c_wh . " ";
        } else if ($due_type == 'PD') {
            $where = "and dt.due_date_from <= date('" . $date_payment . "') " . $c_wh . " ";
        } else if ($due_type == 'allow_pay') {
            $where = "and  dt.due_date_from NOT IN (SELECT p.due_date from payment p where p.payment_status = 1 and p.due_date is not null and p.id_scheme_account = sa.id_scheme_account)";
        } else if ($due_type == 'MND') {
            $where = "and '" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to ";
        } else if ($sch['maturity_type'] != 2 && ($now <= $sch['maturity_date'] || $sch['total_paid_ins'] == 0) && $due_type == 'current_range') {
            $where = "and  date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.due_date_to";
        } else if ($sch['maturity_type'] != 2 && $now > $sch['maturity_date'] && $due_type == 'current_range' && $sch['total_paid_ins'] > 0) {
            if($sch['installment_cycle'] == 1 || $sch['installment_cycle'] == 3 || $sch['installment_cycle'] == 2){
                $date_condition = "DATE_FORMAT(p.date_payment,'%Y-%m-%d') = date_format('" . $date_payment . "','%Y-%m-%d')";
            }else if($sch['installment_cycle'] == 0){
                $date_condition = "DATE_FORMAT(p.date_payment,'%Y-%m') = date_format('" . $date_payment . "','%Y-%m')";
            }else{
                $date_condition = "1";
            }
            $where = "AND EXISTS (
                                SELECT 1
                                FROM payment p
                                WHERE p.payment_status = 1
                                  AND p.due_date IS NOT NULL
                                  AND p.id_scheme_account = sa.id_scheme_account
                                  AND ".$date_condition."
                                  AND p.due_date BETWEEN dt.due_date_from AND dt.due_date_to
                            )";
        }else {
            $where = $c_wh;
        }
        if ($sch['installment_cycle'] == 2) {  //by days duration cycle
            $days_duration = $sch['ins_days_duration'] - 1;
            $grace_days = $sch['grace_days'] - 1;
            $sql = "SELECT 		if(
    			dt.due_date_to < '" . $date_payment . "' ,
    			'PD',
    			if( '" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to  , 
    				'ND',
    				if(
                       dt.due_date_to >= '" . $date_payment . "'
                        ,'AD','-'
                    )
    			) 
    		) as due_type,
    		dt.installment,dt.due_date_from,dt.due_date_to,dt.grace_date,
    		if((('" . $due_type . "' = 'ND' OR '" . $due_type . "' = '') AND date('" . $date_payment . "') BETWEEN dt.due_date_from and dt.grace_date) OR ('" . $due_type . "' = 'AD')  , '0','1') as is_limit_exceed	
    		FROM scheme_account sa
    		JOIN (SELECT @sno := @sno + 1 as installment,
    			@due_Date_from := if(@sno = 1, '" . $first_payment_date . "',date_add(@pay_date ,INTERVAL " . $sch['ins_days_duration'] . " day )) as due_date_from,
    			@due_Date_to := if(@sno = 1, date_add('" . $first_payment_date . "',INTERVAL " . $days_duration . " day ),date_add(@due_Date_from,INTERVAL " . $days_duration . " day )) as due_date_to,  
    			@grace_date := if(@sno = 1, date_add('" . $first_payment_date . "',INTERVAL " . $grace_days . " day ),date_add(@due_Date_from,INTERVAL " . $grace_days . " day )) as grace_date,
    			@pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from) as due_pay_date
    			FROM access
    			join (SELECT @pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from), @sno := 0 ) as t
    			limit " . $sch['total_installments'] . "
    		) as dt
    		WHERE  sa.id_scheme_account = " . $id_scheme_account . "  " . $where . " ";
            $pay = $this->db->query($sql)->result_array();
        }
        //daily payment cycle and monthly
        else if ($sch['installment_cycle'] == 1 || $sch['installment_cycle'] == 3) {
            $sql = "SELECT 		if(
        		dt.due_date_to < '" . $date_payment . "' ,
        		'PD',
        		if( '" . $date_payment . "' = dt.due_date_from  , 
        			'ND',
        			if(
                       dt.due_date_to > '" . $date_payment . "'
                        ,'AD','-'
                    )
        		) 
        	) as due_type,
        	dt.installment,dt.due_date_from,dt.due_date_to
        	FROM scheme_account sa
        	JOIN (SELECT @sno := @sno + 1 as installment,
        		@due_Date_from := if(@sno = 1, '" . $first_payment_date . "',date_add(@pay_date ,INTERVAL 1 day )) as due_date_from,
        		@due_Date_to := @due_Date_from  as due_date_to,  
        		@pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from) as due_pay_date
        		FROM access
        		join (SELECT @pay_date := if(@sno = 1,'" . $first_payment_date . "',@due_Date_from), @sno := 0 ) as t
        		limit " . $sch['total_installments'] . "
        	) as dt
        	WHERE  sa.id_scheme_account = " . $id_scheme_account . "  " . $where . " ";
            $pay = $this->db->query($sql)->result_array();
        }
        //monthly cycle    
        else if ($sch['installment_cycle'] == 0) {
            $sql = "SELECT 		if(
            			dt.due_date_to < '" . $date_payment . "' ,
            			'PD',
            			if( '" . $date_payment . "' BETWEEN dt.due_date_from AND dt.due_date_to  , 
            				'ND',
            				if(
                               dt.due_date_to >= '" . $date_payment . "'
                                ,'AD','-'
                            )
            			) 
            		) as due_type,
                    dt.installment,
                    dt.due_date_from,
                    dt.due_date_to 
                    FROM scheme_account sa 
                    JOIN (SELECT @sno := @sno + 1 as installment, 
                          @due_Date_from := if(@sno = 1, date_format('" . $first_payment_date . "','%Y-%m-01'),date_add(@pay_date ,INTERVAL 1 month )) as due_date_from, 
                          @due_Date_to := LAST_DAY(@due_Date_from) as due_date_to, 
                          @pay_date := if(@sno = 1,date_format('" . $first_payment_date . "','%Y-%m-01'),@due_Date_from) as due_pay_date 
                          FROM access 
                          join (SELECT @pay_date := if(@sno = 1,date_format('" . $first_payment_date . "','%Y-%m-01'),@due_Date_from), @sno := 0 ) as t limit " . $sch['total_installments'] . " 
                         ) as dt 
                    WHERE sa.id_scheme_account = " . $id_scheme_account . "  " . $where . " ";
            $pay = $this->db->query($sql)->result_array();
        }
        //custom payable cycle
        else if ($sch['installment_cycle'] == 4) {
            if ($due_type == 'custom') {
                $where = "and  mas.estimated_paid_amount > " . $sch['total_paid_amount'];
            } else {
                if ($sch['topup_store_slab'] == 1) {  //fully completed slab
                    $where = "and  mas.estimated_paid_amount <= " . $sch['total_paid_amount'] . " ORDER BY mas.installment DESC LIMIT 1";
                } else {  //current slab
                    $where = "and  mas.estimated_paid_amount >= " . $sch['total_paid_amount'] . " ORDER BY mas.installment ASC LIMIT 1";
                }
            }
            $sql = "SELECT mas.range_from,mas.range_to,mas.payable_value,mas.payable_type,mas.installment,mas.due_date_from as due_date_from,
                        mas.due_date_to as due_date_to,ROUND(mas.estimated_paid_amount) as estimated_paid_amount,
                        sa.topup_amount,
                        ROUND((mas.estimated_paid_amount - " . $sch['total_paid_amount'] . ")) as minimum_payable_amount,
                        ROUND((sa.topup_amount - " . $sch['total_paid_amount'] . ")) as maximum_payable_amount,
                        if('" . $now . "' >= mas.due_date_from && '" . $now . "' <= mas.due_date_to , 'ND' ,if('" . $now . "' < mas.due_date_from , 'AD', if('" . $now . "' > mas.due_date_to , 'PD', '')) ) as due_type
                        FROM scheme_account sa
                        JOIN (SELECT  cp.range_from, cp.range_to, cp.payable_value, cp.payable_type,
                                @sno := @sno + 1 as installment, 
                                if(@sno = 1, '" . $first_payment_date . "',date_add(date(sa.start_date) ,INTERVAL cp.range_from -1 day )) as due_date_from, 
                                if(@sno = 1, date_add('" . $first_payment_date . "' ,INTERVAL cp.range_to -1 day ),date_add(date(sa.start_date) ,INTERVAL cp.range_to -1 day )) as due_date_to, 
                                (sa.topup_amount * (cp.payable_value/100)) as estimated_paid_amount
                                FROM `scheme_custom_payable_settings` cp
                                LEFT JOIN scheme s on s.id_scheme = cp.id_scheme
                                LEFT JOIN scheme_account sa on sa.id_scheme = s.id_scheme
                                JOIN (SELECT @sno := 0) as dt
                                WHERE sa.id_scheme_account = " . $id_scheme_account . " and cp.range_status = 1
                                order by cp.range_from ASC) mas
                        Where sa.id_scheme_account = " . $id_scheme_account . " " . $where . " ";
            $pay = $this->db->query($sql)->result_array();
        }
        if ($due_type == 'allow_pay') {
            foreach ($pay as $p) {
                $grouped_dues[$p['due_type']][] = $p;
            }
            foreach ($grouped_dues as $key => $gd) {
                $result[] = array('due_name' => $key, 'dues_count' => sizeof($gd));
            }
        } else {
            $result = $pay;
        }
        return $result;
    }
    function getSchemeData($id_sch_acc)
    {
        $sql = $this->db->query("SELECT s.maturity_type,s.maturity_days,s.interest,s.apply_benefit_by_chart,sa.id_scheme,
            Date_format(sa.start_date,'%Y-%m%-%d') as start_date,sa.maturity_date,
            			date_add(Date_format(sa.start_date,'%Y-%m%-%d'),INTERVAL s.maturity_days DAY) as calc_maturity_date,
            			s.installment_cycle,s.total_installments,s.ins_days_duration
            from scheme_account sa
            left join scheme s on s.id_scheme=sa.id_scheme
            where sa.id_scheme_account=" . $id_sch_acc);
        return $sql->row_array();
    }
    //RHR schemes ends
    function get_cusreff_report_by_range($from_date, $to_date)
    {
        $sql = " SELECT c.id_customer,concat(c.firstname,' ',c.lastname) as name,c.mobile,c.mobile as cus_referalcode,COUNT(sa.referal_code) as refferal_count,wa.id_wallet_account,wa.id_customer,wa.wallet_acc_number,SUM(wt.value) as benifits
            FROM wallet_transaction wt
            LEFT JOIN`wallet_account` wa  on (wa.id_wallet_account = wt.id_wallet_account)
            LEFT JOIN customer c on (c.id_customer = wa.id_customer)
            LEFT JOIN scheme_account sa on (sa.referal_code = c.mobile)
            WHERE wt.type = 0 and wt.transaction_type = 0 and wa.id_customer is not null and sa.is_refferal_by = 0 and 
            ( date(sa.start_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
            group by wa.id_wallet_account";
        //	print_r($sql);exit;
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    function get_cus_ref_success()
    {
        // $sql = " SELECT c.id_customer,concat(c.firstname,' ',c.lastname) as name,c.mobile,c.mobile as cus_referalcode,COUNT(sa.referal_code) as refferal_count,wa.id_wallet_account,wa.id_customer,wa.wallet_acc_number,SUM(wt.value) as benifits
        //     FROM wallet_transaction wt
        //     LEFT JOIN`wallet_account` wa  on (wa.id_wallet_account = wt.id_wallet_account)
        //     LEFT JOIN customer c on (c.id_customer = wa.id_customer)
        //     LEFT JOIN scheme_account sa on (sa.referal_code = c.mobile)
        //     WHERE wt.type = 0 and wt.transaction_type = 0 and wa.id_customer is not null and sa.is_refferal_by = 0
        //     group by wa.id_wallet_account";
        $sql = " SELECT c.id_customer,concat(c.firstname,' ',IFNULL(c.lastname,'')) as name,c.mobile,c.mobile as cus_referalcode,COUNT(sa.referal_code) as refferal_count,wa.id_wallet_account,wa.id_customer,wa.wallet_acc_number,SUM(wt.value) as benifits
            FROM wallet_transaction wt
            LEFT JOIN`wallet_account` wa  on (wa.id_wallet_account = wt.id_wallet_account)
            LEFT JOIN customer c on (c.id_customer = wa.id_customer)
            LEFT JOIN (SELECT referal_code, COUNT(*) AS refferal_count FROM scheme_account WHERE is_refferal_by = 0 GROUP BY referal_code) sa ON sa.referal_code = c.mobile
            WHERE wt.type = 0 and wt.transaction_type = 0 and wa.id_customer is not null
            group by wa.id_wallet_account";
        //print_r($sql);exit;
        $payments = $this->db->query($sql);
        return $payments->result_array();
    }
    //function to get ref_number from pmd table
    function get_ref_num()
    {
        $value = $this->input->post('value');
        $result = 0;
        if ($value != '') {
            $sql = "SELECT IFNULL(sum(payment_amount),0) as payment_amount FROM `payment_mode_details` where payment_ref_number='" . $value . "' and payment_status=1 and is_active=1";
            $result = $this->db->query($sql);
            return $result->row_array();
        } else {
            return $result;
        }
    }
    //general advance reports starts here
    function general_advance_list()
    {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $mode = $this->input->post('mode');
        $id_scheme = $this->input->post('id_scheme');
        $added_by = $this->input->post('added_by');
        $login_branch = $this->session->userdata('id_branch');
        if (!empty($login_branch)) {
            $id_branch = $login_branch;
        } else {
            $id_branch = $this->input->post('id_branch');
        }
        $sql = "SELECT @a:=@a+1 as sno,
		IFNULL(gap.id_adv_payment,'-') as id_adv_payment,
		(select count(id_adv_payment) from general_advance_payment where id_scheme_account=gap.id_scheme_account and payment_status=1) as installment,
		 IFNULL(gapd.id_pay_mode_details,'-') as id_adv_mode,
		IFNULL(Date_format(gap.custom_entry_date,'%d-%m%-%Y'),'-') as custom_entry_date,
		IFNULL(Date_format(gap.date_payment,'%d-%m%-%Y'),'-') as date_payment,
		IFNULL(sa.group_code,'-') as group_code,
		 CONCAT(c.firstname,' ',IFNULL(c.lastname,'')) as cusname, 
		 IFNULL(sa.account_name,'-') as name,
		 IFNULL(sa.scheme_acc_number,'NOT ALLOCATED') as scheme_acc_number,
		 IFNULL(c.mobile,'-') as mobile,
		 sa.id_scheme_account,
		 IFNULL(gap.receipt_no,'-') as receipt_no,
		 IFNULL(s.code,'-') as sch_code,
		 IFNULL(gapd.payment_mode,'-') as mode,
		 IFNULL(gapd.payment_amount,0) as payment_amount,
		 IFNULL(gap.id_branch,'-') as costcenter,
		 IFNULL(gap.added_by,'-') as paid_through,
		 IFNULL(if(sa.active=1,'Active','Closed'),'-') as status,
		 IFNULL(gap.remark,'-') as remark,
		 IFNULL(b.name,'-') as branch,
		 concat(IFNULL(e.emp_code,''),' ',IFNULL(e.firstname,'')) as emp_name, 
		gap.id_employee,
		IFNULL(gap.metal_rate,'-') as metal_rate,
		IF(gap.metal_weight!='0' && gap.metal_weight!='' ,(gapd.payment_amount / gap.metal_rate),'0') as old_metal_weight,
				round(IF(gap.metal_weight > 0 ,(gap.metal_weight / (select count(id_pay_mode_details) from general_advance_mode_detail where id_adv_payment = gap.id_adv_payment and payment_status = 1) ),'0'),3) as metal_weight,
		IFNULL(gap.payment_ref_number,IFNULL(gap.card_no,'-')) as payment_ref_number,
		psm.payment_status as payment_status,
		IFNULL(gap.payment_type,'-') as pay_type, IFNULL(gap.id_transaction,'-') as transcation_id
		from general_advance_mode_detail gapd
		left join general_advance_payment gap on gap.id_adv_payment=gapd.id_adv_payment
		left join scheme_account sa on sa.id_scheme_account=gap.id_scheme_account
		left join scheme s on s.id_scheme=sa.id_scheme
		left join customer c on c.id_customer=sa.id_customer
		left join branch b on b.id_branch=gap.id_branch
		left join employee e on e.id_employee=gap.id_employee
		Left Join payment_status_message psm On (gap.payment_status=psm.id_status_msg)
		join (SELECT @a:= 0) a
		where
		(date(gap.date_payment) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
		" . (!empty($id_branch) ? " and gap.id_branch=" . $id_branch . "" : '') . "
		" . (!empty($id_scheme) ? " and sa.id_scheme=" . $id_scheme . "" : '') . "
		" . (!empty($mode) ? " and gapd.payment_mode='" . $mode . "'" : '') . "
		" . (!empty($added_by) ? " and gap.added_by=" . $added_by . "" : '') . "
		 and gap.payment_status=1 and gapd.is_active=1 and gapd.payment_status=1";
        //print_r($sql);exit;
        $result_array = [];
        $result = $this->db->query($sql);
        if ($result->num_rows() > 0) {
            foreach ($result->result_array() as $rcpt) {
                $rcpt['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $rcpt['id_scheme_account']);
                $rcpt['receipt_no'] = $this->customer_model->format_accRcptNo('General_Receipt', $rcpt['id_adv_payment']);
                $result_array[] = $rcpt;
            }
        }
        return $result_array;
    }
    //general advance reports ends here
    function general_advance_list_byid()
    {
        $login_branch = $this->session->userdata('id_branch');
        if (!empty($login_branch)) {
            $id_branch = $login_branch;
        }
        $id = $this->input->post('id');
        $sql = "select @a:=@a+1 as sno,
		IFNULL(p.payment_amount,0) as payment_amount,
		IFNULL(p.id_scheme_account,'-') as id_scheme_account,
		IFNULL(p.payment_mode,'-') as payment_mode,
		IFNULL(p.metal_rate,'-') as metal_rate,
		IFNULL(p.metal_weight,0) as metal_weight,
		IFNULL(p.receipt_no,'-') as receipt_no,
		IFNULL(Date_format(p.date_payment,'%d-%m%-%Y %H:%i:%s'),'-') as date_payment,
		IFNULL(psm.payment_status,'-') as payment_status
		from general_advance_payment p 
		join (SELECT @a:= 0) a
		Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg)
		where id_scheme_account=" . $id . "";
        $result = $this->db->query($sql);
        return $result->result_array();
    }
    //monthly chit report starts
    function monthly_report_data()
    {
        $login_branch = $this->session->userdata('id_branch');
        if (!empty($login_branch)) {
            $id_branch = $login_branch;
        } else {
            $id_branch = $this->input->post('id_branch');
        }
        $id_scheme = $this->input->post('id_scheme');
        $month = $this->input->post('month');
        $year = $this->input->post('year');
        $sql = "SELECT @a:=@a+1 as sno, 
		IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_payment, 
		date(pmd.payment_date) as pmddate,
		s.scheme_name,
		IFNULL(Date_format(p.custom_entry_date,'%d-%m%-%Y'),'-') as entry_Date,
		cs.edit_custom_entry_date,
		IFNULL(SUM(pmd.payment_amount),0) as payment_amounts,
		IFNULL(sum(p.metal_weight),0) as total_weight,
		IFNULL(sum( Case When pmd.payment_mode='CSH' then pmd.payment_amount End),0) as cash,
		IFNULL(sum( Case When (pmd.payment_mode='CC'|| pmd.payment_mode='DC') then pmd.payment_amount End),0) as card, IFNULL(sum( Case When (pmd.payment_mode='CHQ') then pmd.payment_amount End),0) as cheque, 
		IFNULL(sum( Case When (pmd.payment_mode='NB') then pmd.payment_amount End),0) as nb,
		IFNULL(sum( Case When (pmd.payment_mode='UPI') then pmd.payment_amount End),0) as upi,
		IFNULL(sum( Case When (pmd.payment_mode!='UPI' && pmd.payment_mode!='NB' && pmd.payment_mode!='CHQ' && pmd.payment_mode!='CSH' && pmd.payment_mode!='CC' && pmd.payment_mode!='DC')  then pmd.payment_amount End),0) as wallet
		FROM payment p 
		join chit_settings cs 
		join (SELECT @a:= 0) a 
		left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account) 
		Left Join branch b On (b.id_branch=p.id_branch) 
		left join scheme s on(sa.id_scheme=s.id_scheme) 
		left JOIN payment_mode_details pmd on (p.id_payment=pmd.id_payment) 
		Where month(p.date_payment)='" . $month . "' 
		and year(p.date_payment)='" . $year . "' 
		" . (!empty($id_branch) ? " and sa.id_branch=" . $id_branch . "" : '') . "
		" . (!empty($id_scheme) ? " and sa.id_scheme=" . $id_scheme . "" : '') . "
		And p.payment_status=1 and (pmd.payment_status=1) 
		group by date(p.date_payment) order by p.date_payment
		";
        //print_r($sql);exit;
        $result = $this->db->query($sql);
        return $result->result_array();
    }
    //monthly chit report ends
    //maturity reports starts here
    function maturity_report_data()
    {
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $login_branch = $this->session->userdata('id_branch');
        if (!empty($login_branch)) {
            $id_branch = $login_branch;
        } else {
            $id_branch = $this->input->post('id_branch');
        }
        $id_scheme = $this->input->post('id_scheme');
        $id_employee = $this->input->post('emp_code');
        $account = [];
        $query = "select total_installments,scheme_name,id_scheme, installment_cycle from scheme";
        $res = $this->db->query($query);
        $scheme_data = $res->result_array();
        //print_r($scheme_data);exit;
        $s_no = 1;
        foreach ($scheme_data as $scheme) {
            $tot_ins = $scheme['total_installments'];
            // Code updated by Karthikai kumaran 05/11/2025 for maturity date range calculation based on installment cycle
            // calculating mat date range based on installment cycle
            // For days
            if ($scheme['installment_cycle'] == 1) {
                $mat_from_date = date('Y-m-d', strtotime($from_date . ' - ' . $tot_ins . ' day'));
                $mat_to_date = date('Y-m-d', strtotime($to_date . ' - ' . $tot_ins . ' day'));
                $matuityDate = "DAY";
            } else { // monthly and others
                $mat_from_date = date('Y-m-d', strtotime($from_date . ' - ' . $tot_ins . ' month'));
                $mat_to_date = date('Y-m-d', strtotime($to_date . ' - ' . $tot_ins . ' month'));
                $matuityDate = "MONTH";
            }
            // print_r($from_date . "===" . $to_date . "\n");
            // print_r($mat_from_date . "===" . $mat_to_date);
            // if($scheme['scheme_name']=='SSR AMOUNT(A) - 500')
            // {
            // 	echo "start date between " .$mat_from_date." and ".$mat_to_date." ".$scheme['scheme_name'] ;exit;
            // }
            $sql = "SELECT @a:=@a+1 as sno,sa.id_scheme_account as id_scheme_account,
			IFNULL(sa.scheme_acc_number,'NOT ALLOCATED') as scheme_acc_number1,
			IFNULL(CONCAT(sa.start_year,'-',s.code,'-',sa.scheme_acc_number),'-') as scheme_acc_number,
			IFNULL(sa.account_name,'-') as account_name,
			IFNULL(c.firstname,'') as firstname,
			IFNULL(c.lastname,'') as lastname,
			IFNULL(c.mobile,'-') as mobile,
			IFNULL(s.code,'-') as scheme_code,
			IFNULL(s.scheme_name,'-') as scheme_name,
			IFNULL(Date_format(sa.start_date,'%d-%m%-%Y'),'-') as start_date,
            date_format(IFNULL(sa.maturity_date,DATE_ADD(date(sa.start_date), INTERVAL s.maturity_days DAY)),'%d-%m%-%Y') as maturity_date,
			IFNULL((SELECT sum(payment_amount) from payment where payment_status=1 and id_scheme_account=sa.id_scheme_account),0) as payment_amount,
			IFNULL((SELECT sum(metal_weight) from payment where payment_status=1 and id_scheme_account=sa.id_scheme_account),0) as payment_weight,
			IFNULL(s.total_installments,'-') as total_ins,
			sa.added_by,
			-- IFNULL((select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or s.scheme_type=3, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=sa.id_scheme_account group by pay.id_scheme_account),'0')
			-- as paid_installments,
            ifnull(sa.total_paid_ins,0) as paid_installments,
			IFNULL(b.name,'-') as branch
			from scheme_account sa
			left join scheme s on s.id_scheme=sa.id_scheme
			join (SELECT @a:= 0) a 
			left join customer c on c.id_customer=sa.id_customer
			left join branch b on b.id_branch=sa.id_branch
			Where 
			(date(sa.start_date) BETWEEN '" . date('Y-m-d', strtotime($mat_from_date)) . "' AND '" . date('Y-m-d', strtotime($mat_to_date)) . "') and sa.id_scheme=" . $scheme['id_scheme'] . "
			" . (!empty($id_branch) ? " and sa.id_branch=" . $id_branch . "" : '') . "
			" . (!empty($id_scheme) ? " and sa.id_scheme=" . $id_scheme . "" : '') . "
			AND sa.is_closed=0 and sa.active=1 and sa.total_paid_ins > 0 
			";
            //print_r($sql);exit;
            $result = $this->db->query($sql);
            //print_r($result);exit;
            $temp_data = $result->result_array();
            //	print_r($temp_data);exit;
            if (!empty($temp_data)) {
                foreach ($temp_data as $rcpt) {
                    $rcpt['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $rcpt['id_scheme_account']);
                    $rcpt['s_no'] = $s_no;
                    $s_no++;
                    $account[] = $rcpt;
                }
            }
        }
        //echo "<pre>";print_r($account);exit;
        return $account;
    }
    //maturity reports ends here
    function get_scheme_cash_total($id_scheme_acc, $id_branch)
    {
        $dCData = $this->getBranchDayClosingData($id_branch);
        $data = $this->db->query("SELECT SUM(p.payment_amount) as paid_amount, IFNULL(SUM(cp.cash_pay),0) + IFNULL(SUM(chit_adv_adj_cash.chit_cash_adv_adj),0) as cash_pay, sa.id_scheme_account
		FROM payment p
		LEFT JOIN (SELECT SUM(IFNULL(pmd.payment_amount,0)) AS cash_pay, pmd.id_payment FROM `payment_mode_details` AS pmd LEFT JOIN payment p ON p.id_payment = pmd.id_payment WHERE pmd.payment_mode = 'CSH' AND pmd.payment_status = 1 AND DATE(p.custom_entry_date) != DATE('" . $dCData['entry_date'] . "') GROUP BY pmd.id_payment) AS cp ON cp.id_payment = p.id_payment
		LEFT JOIN (
				SELECT IFNULL(SUM(cash_utilized_amt), 0) AS chit_cash_adv_adj, p.id_payment 
				FROM `ret_advance_utilized` au 
				LEFT JOIN ret_issue_receipt r ON r.id_issue_receipt = au.id_issue_receipt
				LEFT JOIN payment p ON p.id_payment = au.id_payment 
				WHERE au.adjusted_for = 2 AND DATE(p.custom_entry_date) != DATE('" . $dCData['entry_date'] . "') AND au.id_payment IS NOT NULL
				GROUP BY p.id_payment
		) AS chit_adv_adj_cash ON chit_adv_adj_cash.id_payment = p.id_payment
		left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
		left join scheme s on s.id_scheme=sa.id_scheme
		WHERE p.payment_status=1 AND sa.id_scheme_account = " . $id_scheme_acc . " GROUP BY p.id_scheme_account");
        //echo $this->db->last_query();exit;
        return $data->row_array();
    }
    function getPaidInsData($id_scheme_account)
    {
        $date = date('Y-m-d');
        $account = array('paid_installments' => 0);
        $acc = $this->db->query("select sa.id_scheme_account,sa.start_date,s.total_installments,s.installment_cycle, s.ins_days_duration,sa.maturity_date,sa.lapse_date,
	                        IF(sa.is_opening=1,
                              IFNULL((sa.paid_installments + COUNT(Distinct Date_Format(pay.date_payment,'%Y%m'))),0),
                               if(s.installment_cycle = 0,
                                  IFNULL(IF(s.min_chance=s.max_chance,COUNT(pay.id_payment),COUNT(Distinct Date_Format(pay.date_payment,'%Y%m'))),0),
                                  if(s.installment_cycle = 1,
                                	  IFNULL(IF(s.min_chance=s.max_chance,COUNT(pay.id_payment),COUNT(Distinct Date_Format(pay.date_payment,'%Y%m%d'))),0),
                                	  if(s.installment_cycle = 2,
                                		IFNULL(COUNT( Date_Format(pay.due_date,'%Y%m')),0),
                                		IFNULL(COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')),0)
                                	  )
                                  )
                               )
                            ) as old_paid_installments,
                            if(s.is_topup_scheme = 1,
                                IFNULL(MAX(pay.installment),0) ,
                                -- --NULL(COUNT(Distinct pay.installment),0) ,
                                IFNULL(
                                IF(sa.is_opening=1,
                                IFNULL((sa.paid_installments + COUNT(Distinct Date_Format(pay.date_payment,'%Y%m'))),0),
                                if(s.installment_cycle = 0,
                                    IFNULL(IF(s.min_chance=s.max_chance,COUNT(pay.id_payment),COUNT(Distinct Date_Format(pay.date_payment,'%Y%m'))),0),
                                    if(s.installment_cycle = 1,
                                        IFNULL(IF(s.min_chance=s.max_chance,COUNT(pay.id_payment),COUNT(Distinct Date_Format(pay.date_payment,'%Y%m%d'))),0),
                                        if(s.installment_cycle = 2,
                                            IFNULL(COUNT( Date_Format(pay.due_date,'%Y%m')),0),
                                            IFNULL(COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')),0)
                                        )
                                    )
                                )
                                ),0)  
                            ) as paid_installments,
                            if(s.installment_cycle = 0,
                               PERIOD_DIFF(date_format('" . $date . "','%Y%m'),date_format(sa.start_date,'%Y%m'))+1,
                               if(s.installment_cycle = 1,
                                  DATEDIFF(date_format('" . $date . "','%Y%m%d'),date_format(sa.start_date,'%Y%m%d'))+1,
                                  if(s.installment_cycle = 2,
                                  CEIL((DATEDIFF(date_format('" . $date . "','%Y%m%d'),date_format(sa.start_date,'%Y%m%d'))+1) /s.ins_days_duration),'-'
                                    )
                                 )
                              ) 
                              as ins_till_date
                    from payment pay 
					left join scheme_account  sa on sa.id_scheme_account = pay.id_scheme_account
					left join scheme s on s.id_scheme = sa.id_scheme
                    where pay.payment_status=1 and sa.id_scheme_account = " . $id_scheme_account . "
                    group by sa.id_scheme_account")->row_array();
        if (sizeof($acc) > 0) {
            $account = $acc;
        }
        return $account;
    }
    // esakki 17-09
    function get_chq_num()
    {
        $value = $this->input->post('value');
        $result = 0;
        if ($value != '') {
            $sql = "SELECT IFNULL(sum(payment_amount),0) as payment_amount FROM `payment_mode_details` where cheque_no ='" . $value . "' and payment_status=1 and is_active=1";
            $result = $this->db->query($sql);
            return $result->row_array();
        } else {
            return $result;
        }
    }
    // Cashfree refund functions - Starts
    function getRefundPayData($txnid)
    {
        $sql = "Select id_payment, id_scheme_account, refund_id, actual_trans_amt, ref_trans_id
		        From payment 
		        Where ref_trans_id='" . $txnid . "'";
        return $this->db->query($sql)->result_array();
    }
    function getPendingRefunds($pg_code, $branch_id)
    {
        $sql = "SELECT id_pg,param_1,param_2,param_3,param_4,pg_code,api_url from gateway where is_default=1 and active=1 and  pg_code=" . $pg_code . " " . ($branch_id != '' ? "and id_branch=" . $branch_id . "" : '') . "";
        $result['gateway_info'] = $this->db->query($sql)->row_array();
        $sql1 = "SELECT id_payment, id_scheme_account, refund_id, ref_trans_id from payment where payment_status=10 and id_payGateway=" . $result['gateway_info']['id_pg'] . " group by ref_trans_id";
        $result['data'] = $this->db->query($sql1)->result_array();
        return $result;
    }
    function getGatewayData($id_gateway)
    {
        $sql = "SELECT id_pg,param_1,param_2,param_3,param_4,pg_code,api_url from gateway where id_pg=" . $id_gateway;
        return $this->db->query($sql)->row_array();
    }
    function getLastRefundStatus($refund_id)
    {
        $sql = $this->db->query("Select refund_status From payment_refund_log Where refund_id='" . $refund_id . "' ORDER by id_pay_refund_log DESC LIMIT 1");
        if ($sql->num_rows() == 1) {
            return $sql->row('refund_status');
        } else {
            return NULL;
        }
    }
    // Cashfree refund functions - Ends
    /*RENEWAL REPORT STARTS..*/
    function renewalLive_arlData()
    {
        $response = [];
        $from = $this->input->post('from_date');
        $to = $this->input->post('to_date');
        $ref_code = $this->input->post('ref_employee');
        $id_customer = $this->input->post('id_customer');
        $renew_type = $this->input->post('renew_type');
        $id_branch = $this->input->post('branch');
        $id_scheme = $this->input->post('scheme');
        $groupBy = $this->input->post('group_by'); // 1- referred employee wise , 2- closed customer wise
        if ($groupBy == '1') {
            $wh_refCode = "and sa.referal_code is not null and sa.referal_code != '' ";
            $wh_group = "sa.referal_code";
            $actual = "and actual.referal_code = e.emp_code";
            $renewal = "and renewal.referal_code = e.emp_code";
        } else {
            $wh_refCode = "";
            $wh_group = "";
            $actual = "";
            $renewal = "";
        }
        $data = $this->db->query("SELECT e.id_employee,e.emp_code as ref_code,e.firstname as refered_emp_name,
                                    c.id_customer,c.mobile,c.firstname as cus_name,
                                    ifnull(actual.aCounts,0) as actual_ct, ifnull(actual.aChits,'-') as actual_chits,actual.close_dates, ifnull(renewal.rCounts,0) as renewal_ct, ifnull(renewal.rChits,'-') as renewal_chits,
                                    ifnull(live.lCounts,0) as live_ct, ifnull(live.lChits,'-') as live_chits ,
                                    other.oCounts as other_ct,other.oChits as other_chits,other.referal_code,
                                    display.all_closedates
                                From scheme_account sa 
                                left join customer c on (c.id_customer = sa.id_customer) 
                                left join employee e on (e.emp_code = sa.referal_code) 
                                join (select @all_closedates := '') as a
                                LEFT JOIN (select @all_closedates :=GROUP_CONCAT(date_format(sa.closing_date,'%Y%m')) ,sa.id_customer, GROUP_CONCAT(date_format(sa.closing_date,'%Y%m')) as all_closedates
                                			from scheme_account sa
                                			where sa.is_closed = 1 and sa.active = 0 " . $wh_refCode . "
                                			" . ($from != '' && $to != '' ? "and sa.closing_date between '" . $from . " 00:00:00' AND '" . $to . " 23:59:59'" : '') . "
                                            group by sa.id_customer
                                ) as display ON (display.id_customer = c.id_customer)
                                left join (SELECT sa.id_customer, sa.referal_code,count(sa.id_scheme_account) as aCounts, GROUP_CONCAT(CONCAT(s.code,'-',sa.scheme_acc_number) SEPARATOR ', ') as aChits,
                                                GROUP_CONCAT(date_format(closing_date,'%Y%m')) as close_dates 
                                            from scheme_account sa 
                                            left join branch b on (b.id_branch = sa.id_branch) 
                                            left join scheme s on (s.id_scheme = sa.id_scheme) 
                                            where sa.is_closed = 1 and sa.active = 0 " . $wh_refCode . "
                                            " . ($from != '' && $to != '' ? "and sa.closing_date between '" . $from . " 00:00:00' AND '" . $to . " 23:59:59'" : '') . "
                                            group by " . $wh_group . " sa.id_customer
                                ) as actual ON (actual.id_customer = display.id_customer " . $actual . ")
                                left join (SELECT sa.id_customer, sa.referal_code,count(sa.id_scheme_account) as rCounts,GROUP_CONCAT(CONCAT(s.code,'-',sa.scheme_acc_number) SEPARATOR ', ') as rChits, GROUP_CONCAT(date_format(start_date,'%Y%m')) as start_date 
                                           from scheme_account sa 
                                           left join branch b on (b.id_branch = sa.id_branch) 
                                           left join scheme s on (s.id_scheme = sa.id_scheme) 
                                           where sa.is_closed = 0 and sa.active = 1 " . $wh_refCode . "
                                           and find_in_set(date_format(sa.start_date,'%Y%m'),@all_closedates)
                                           group by " . $wh_group . " sa.id_customer
                                ) as renewal on (renewal.id_customer = display.id_customer " . $renewal . ") 
                                left join (SELECT sa.id_customer, sa.referal_code,count(sa.id_scheme_account) as lCounts,GROUP_CONCAT(CONCAT(s.code,'-',sa.scheme_acc_number) SEPARATOR ', ') as lChits 
                                            from scheme_account sa 
                                            left join branch b on (b.id_branch = sa.id_branch) 
                                            left join scheme s on (s.id_scheme = sa.id_scheme) 
                                            where sa.is_closed = 0 and sa.active = 1 
                                            group by sa.id_customer
                                ) as live on (live.id_customer = actual.id_customer) 
                                LEFT JOIN (SELECT sa.id_customer, GROUP_CONCAT(sa.referal_code) as referal_code,count(sa.id_scheme_account) as oCounts,GROUP_CONCAT(sa.scheme_acc_number) as oChits,
                                            date_format(start_date,'%Y%m') as start_date 
                                            from scheme_account sa 
                                            where sa.is_closed = 0 and sa.active = 1  " . $wh_refCode . "
                                            group by " . $wh_group . " sa.id_scheme_account
                                ) as other ON (other.id_customer = c.id_customer and FIND_IN_SET(other.start_date,actual.close_dates) and actual.referal_code != other.referal_code)
                                where sa.id_customer is not null  " . $wh_refCode . "
                                " . ($ref_code != '' && $ref_code != null ? "and e.emp_code ='" . $ref_code . "'" : '') . "
                                " . ($renew_type == '1' ? "and renewal.rCounts > 0" : ($renew_type == '2' ? "and renewal.rCounts is null " : "and (actual.aCounts > 0 or renewal.rCounts > 0) ")) . "
                                " . ($id_branch != '' && $id_branch != 0 ? " AND   sa.id_branch = '" . $id_branch . "' " : '') . "
                                " . ($id_scheme != '' ? "AND sa.id_scheme = '" . $id_scheme . "' " : '') . "
                                group by " . $wh_group . " c.id_customer  
                                ORDER BY `e`.`id_employee` ASC 
	                        ")->result_array();
        if (sizeof($data) > 0) {
            foreach ($data as $r) {
                if ($groupBy == '1') {   // 1- referred employee wise , 2- closed customer wise
                    $response[$r['refered_emp_name']][] = $r;
                } else {
                    $response[''][] = $r;
                }
            }
        }
        return $response;
        /*-- left join (SELECT sa.id_customer, sa.referal_code,count(sa.id_scheme_account) as rCounts,GROUP_CONCAT(CONCAT(b.short_name,'-',s.code,'-',sa.scheme_acc_number) SEPARATOR ', ') as rChits,
                                            GROUP_CONCAT(date_format(start_date,'%Y%m')) as start_date 
                                            from scheme_account sa 
                                            left join branch b on (b.id_branch = sa.id_branch) 
                                            left join scheme s on (s.id_scheme = sa.id_scheme) 
                                            where sa.is_closed = 0 and sa.active = 1  ".$wh_refCode."
                                            group by ".$wh_group." sa.id_customer 
                                ) as renewal on (renewal.id_customer = c.id_customer ".$renewal." and FIND_IN_SET(renewal.start_date,display.all_closedates)  ) */
    }
    /*RENEWAL REPORT ENDS..*/
    function benifit($id, $pay_array)
    {
        // print_r($pay_array['payment_status']);exit;
        if ($pay_array['payment_status'] == 1) {
            $sql = $this->db->query("UPDATE wallet_transaction SET transaction_type = 0 where id_payment = " . $id . "");
            // print_r($sql);exit;
        } else {
            $sql = $this->db->query("UPDATE wallet_transaction SET transaction_type = 1 where id_payment = " . $id . "");
            // print_r($sql);exit;
        }
        return true;
    }
    /** Maturity Type : 4
     * Fixed Flexible by lapse 
     * Calculate lapse date & maturity date for first payment and store against scheme account
     * Revise the maturity date on pending dues that account has 
     * Extend the maturity date based on installment cycle
     * First calculation : add total installments
     * Further payments : add if pending dues available
     * Installment cycle : 0 (Monthly cycle) => Added no of pending dues as MONTHS
     * 		EX: 2 PD means; +2 MONTHS
     * Installment cycle : 1 (Daily cycle) => Added no of pending dues as DAYS
     * 		EX: 2 PD means; +2 DAYS
     * Installment cycle : 2 (Days duration pay) => Added no of pending dues as DAYS
     * 		EX: 2 PD & Days(Installment Cycle) = 7 days means; 
     * 		(2PD * 7) = +14 DAYS
     * calculate_maturityLapse_Date() - to store maturity & lapse date first time and maturity date revisal... 
     * getPaidInsData() - to get paid ins & must be paid till date based on installment cycle 
     * Coded On : 20-08-2024; Coded By: Abi
     */
    function calculate_maturityLapse_Date($id_scheme_account)
    {
        $unpaid = 0;
        $status = TRUE;
        //Get scheme settings by id_scheme_account...
        $sch = $this->getSchemeData($id_scheme_account);
        //Get paid installments..
        $acc = $this->getPaidInsData($id_scheme_account);
        $start_date = $acc['start_date'];
        $paid_ins = $acc['paid_installments'];
        $must_paid = $acc['ins_till_date'];
        //total ins to be added for lapse...
        if ($sch['installment_cycle'] == 0) {
            $ld_ins = $sch['total_installments'];  //ex: for monthly scheme; 11 months
            $period = 'months';
        } else if ($sch['installment_cycle'] == 1) {
            $ld_ins = $sch['total_installments'];   //ex: for daily scheme; 180 days
            $period = 'days';
        } else if ($sch['installment_cycle'] == 2) {
            $ld_ins = $sch['total_installments'] * $sch['ins_days_duration'];   //ex: for weekly scheme; 52 * 7 = 364 days ie., 52 weeks
            $period = 'days';
        }
        //calculate unpaid ins...
        if ($must_paid >= $sch['total_installments']) {
            $unpaid = ($sch['total_installments'] - $paid_ins);
        } else if ($must_paid < $sch['total_installments']) {
            if ($must_paid > $paid_ins) {
                $unpaid = ($must_paid - $paid_ins);
            }
        }
        //update dates...
        if ($paid_ins > 0 && $unpaid > 0) {
            if ($sch['installment_cycle'] == 0) {
                $md_ins = $unpaid + $sch['total_installments'];
                $period = 'months';     //ex: for monthly scheme; 2 unpaid months
            } else if ($acc['installment_cycle'] == 1) {
                $md_ins = $unpaid + $sch['total_installments'];
                $period = 'days';       //ex: for days scheme; 2 unpaid days
            } else if ($acc['installment_cycle'] == 2) {
                $md_ins = ($unpaid + $sch['total_installments']) * $acc['ins_days_duration'];
                $period = 'days';       //ex: for weekly scheme; 2 unpaid weeks 2*7 = 14days
            }
            if ($md_ins > 0) {
                if ($period == 'days') {
                    $revised_maturityDate = date('Y-m-d', strtotime($start_date . '+' . $md_ins . ' ' . $period));
                } else {
                    $revised_maturityDate = date('Y-m-01', strtotime($start_date . '+' . $md_ins . ' ' . $period));
                }
                if ($period == 'days') {
                    $maturity_date = date('Y-m-d', strtotime($start_date . ' + ' . $ld_ins . ' ' . $period));
                } else {
                    $maturity_date = date('Y-m-01', strtotime($start_date . ' + ' . $ld_ins . ' ' . $period));
                }
                $updAcc = array('maturity_date' => $revised_maturityDate, 'lapse_date' => $maturity_date);
                $status = $this->updateAtData($updAcc, 'id_scheme_account', $id_scheme_account, 'scheme_account');
                /*echo '<pre>';print_r($this->db->last_query());
                echo '<pre>';print_r($maturity_date);exit;*/
            }
        } else {
            if ($period == 'days') {
                $maturity_date = date('Y-m-d', strtotime($start_date . ' + ' . $ld_ins . ' ' . $period));
            } else {
                $maturity_date = date('Y-m-01', strtotime($start_date . ' + ' . $ld_ins . ' ' . $period));
            }
            $updAcc = array(
                'maturity_date' => $maturity_date,
                'lapse_date' => $maturity_date
            );
            $status = $this->updateAtData($updAcc, 'id_scheme_account', $id_scheme_account, 'scheme_account');
        }
        //update paid installments against account...
        //$updAcc['total_paid_ins'] = $acc['paid_installments'];// echo '<pre>';print_r($updAcc);exit;
        return $status;
    }
    function get_digi_benefit($res)
    {
        $sql_int = $this->db->query("SELECT sbd.interest_type,sbd.interest_value, IF(sbd.interest_type = 0,'%','INR') as int_symbol,sbd.commodity,m.metal   
				FROM `scheme_benefit_deduct_settings` sbd
				left join metal m on m.id_metal = sbd.commodity 
				WHERE ('" . $res['date_difference'] . "' BETWEEN sbd.installment_from AND sbd.installment_to) AND sbd.id_scheme=" . $res['id_scheme'] . "
    			");
        return $sql_int->result_array();
    }
    function get_CusKycData($id_customer)
    {
        $sql = $this->db->query("select kyc_type,number,name,status,
								LPAD(RIGHT(number, 4), CHAR_LENGTH(number), 'X') AS masked_doc_number,
								if(kyc_type = 2,'PAN',if(kyc_type = 3, 'AADHAR',if(kyc_type = 1,'BANK','-'))) as doc_type
								from kyc where status != 3 and id_customer = " . $id_customer);
        $data = $sql->result_array();
        return $data;
    }
    function upd_maturityDate($payment)
    {
        $result = TRUE;
        if ($payment['maturity_type'] > 0 && $payment['maturity_days'] != null && $payment['maturity_days'] > 0) {
            $maturity_data = array('maturity_date' => date('Y-m-d', strtotime("+" . $payment['maturity_days'] . " days", strtotime($payment['start_date']))));
            $result = $this->updData($maturity_data, 'id_scheme_account', $payment['id_scheme_account'], 'scheme_account');
        }
        return $result;
    }
    function getBranchwiseSettings()
    {
        $sql = $this->db->query("select branchwise_scheme,is_branchwise_cus_reg
								from chit_settings");
        return $sql->row_array();
    }
    function getBranchData($data)
    {
        $sql = "
            SELECT 
                IFNULL(prev_cus.id_branch, 0) AS prev_cus_branch,
                IFNULL(sa.id_branch, 0) AS acc_branch,
                IFNULL(new_cus.id_branch, 0) AS cus_branch
            FROM scheme_account sa
            LEFT JOIN customer prev_cus 
                ON prev_cus.id_customer = sa.id_customer
            LEFT JOIN customer new_cus 
                ON new_cus.mobile = ?
            WHERE sa.id_scheme_account = ?
        ";
        $query = $this->db->query($sql, [
            $data['mobile'],
            $data['id_scheme_account']
        ]);
        $row = $query->row_array();
        return [
            "prev_cus_branch" => $row['prev_cus_branch'] ?? 0,
            "acc_branch" => $row['acc_branch'] ?? 0,
            "cus_branch" => $row['cus_branch'] ?? 0
        ];
    }
}
