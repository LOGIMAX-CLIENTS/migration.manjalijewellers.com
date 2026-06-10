<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Account_model extends CI_Model
{
    const ACC_TABLE = "scheme_account";
    const CUSREG_TABLE = "customer_reg";
    const TRANS_TABLE = "transaction";
    const CUS_TABLE = "customer";
    const SCH_TABLE = "scheme";
    const PAY_TABLE = "payment";
    const REG_TABLE = "registration";
    const ADD_TABLE = "address";
    const SYNC_TABLE = "sync_log";
    const OTP_TABLE = "otp";
    const ISSU_TABLE = "gift_issued";
    const SCHGROUP_TABLE = "scheme_group";
    const BRANCH = "branch";
    function __construct()
    {
        parent::__construct();
        $this->load->model("customer_model");
        $this->load->model('payment_model');
        $this->load->helper('metal_wgt_digit');
        $this->log_dir = 'log/' . date("Y-m-d");
        if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0777, TRUE);
        }
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
    // public function updateData($data, $id_field, $id_value, $table)
    // {
    // 	$edit_flag = 0;
    // 	$this->db->where($id_field, $id_value);
    // 	$edit_flag = $this->db->update($table, $data);
    // 	return ($edit_flag == 1 ? $id_value : 0);
    // }
    function account_empty_record()
    {
        $data = array(
            'id_scheme_account' => 0,
            'mobile' => "",
            'acc_number' => "",
            'id_scheme' => 0,
            'id_customer' => 0,
            'scheme_acc_number' => NULL,
            'customer' => NULL,
            'account_name' => NULL,
            'cus_name' => NULL,
            'ref_no' => NULL,
            'scheme_type' => NULL,
            'paid_installments' => 0,
            'is_opening' => 0,
            'balance_amount' => '0.00',
            'balance_weight' => '0.000',
            'last_paid_weight' => '0.000',
            'last_paid_chances' => 0,
            'last_paid_date' => NULL,
            'start_date' => date('d-m-Y'),
            'maturity_date' => NULL,
            'employee_approved' => 0,
            'active' => 1,
            'disable_payment' => 0,
            'is_new' => 'Y',
            'is_refferal_by' => 1,
            'referal_code' => NULL,
            'remark_open' => NULL,
            'show_gift_article' => 0,
            'id_branch' => NULL,
            'id_employee' => NULL,
            'firstPayment_amt' => NULL,
            'get_amt_in_schjoin' => NULL,
            'maturity_type' => NULL,
            'total_installments' => NULL,
            'acc_number' => NULL,
            'mobile' => NULL,
            'has_gift' => 1,
            'pan_no' => NULL,
            'aadhaar_no' => NULL,
            'duplicate_passbook_issued' => 2,
            //	'get_amt_in_schjoin'=>$this->get_amt_in_schjoinsettings()
        );
        return $data;
    }
    public function get_maturity_days($id)
    {
        $sql = "SELECT maturity_days FROM scheme WHERE id_scheme =$id";
        $data = $this->db->query($sql);
        return $data->row()->maturity_days;
    }
    function getActiveAccounts($id = "")
    {
        if ($id != NULL) {
            $sql = "Select
					  sa.id_scheme_account,sa.scheme_acc_number,IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,sa.ref_no,sa.account_name,sa.start_date,c.is_new,
					  s.scheme_name,sa.is_new,s.code,if(s.scheme_type=0,'Amount','Weight')as scheme_type,s.total_installments,s.max_chance,s.max_weight,s.amount,c.mobile,if(sa.active =1,'Active','Inactive') as active,sa.date_add
					From
					  " . self::ACC_TABLE . " sa
					Left Join " . self::CUS_TABLE . " c On (sa.id_customer=c.id_customer)
					Left Join " . self::SCHGROUP_TABLE . " sg On (sa.id_scheme=sg.id_scheme)
					Left Join " . self::SCH_TABLE . " s On (s.id_scheme=sa.id_scheme)
					Where  (sa.active=1 And sa.is_closed=0 And c.active =1) And sa.id_scheme_account=" . $id;
            return $this->db->query($sql)->row_array();
        } else {
            $sql = "Select
					  sa.id_scheme_account,sa.scheme_acc_number,IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,sa.ref_no,sa.account_name,sa.start_date,c.is_new,
					  		sg.end_date as end_date,
					  s.scheme_name,sa.is_new,s.code,if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,s.total_installments,s.max_chance,s.max_weight,s.amount,c.mobile,if(sa.active =1,'Active','Inactive') as active,sa.date_add
					From
					  " . self::ACC_TABLE . " sa
					Left Join " . self::CUS_TABLE . " c On (sa.id_customer=c.id_customer)
					Left Join " . self::SCH_TABLE . " s On (s.id_scheme=sa.id_scheme)
					Where  sa.active=1 And sa.is_closed=0 And c.active =1";
            return $this->db->query($sql)->result_array();
        }
    }
    // function getAmountSchemeAccounts($id="")
    // {
    // 	if($id!=NULL)
    // 	{
    // 		$sql="Select
    // 				  sa.id_scheme_account,IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,sa.ref_no,sa.account_name,sa.start_date,c.is_new,
    // 				  s.scheme_name,sa.is_new,s.code,if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,s.total_installments,s.max_chance,s.max_weight,s.amount,c.mobile,if(sa.active =1,'Active','Inactive') as active,sa.date_add
    // 				From
    // 				  ".self::ACC_TABLE." sa
    // 				Left Join ".self::CUS_TABLE." c On (sa.id_customer=c.id_customer)
    // 				Left Join ".self::SCH_TABLE." s On (s.id_scheme=sa.id_scheme)
    // 				Where  (sa.active=1 And sa.is_closed=0 And c.active =1) And (s.scheme_type=0 or s.scheme_type=1 or s.scheme_type=2 or s.scheme_type=3 ) And sa.id_scheme_account=".$id;
    // 				return $this->db->query($sql)->row_array();
    // 	}
    // 	else
    // 	{
    // 		$sql="Select
    // 				  sa.id_scheme_account,IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,sa.ref_no,sa.account_name,sa.start_date,c.is_new,
    // 				  s.scheme_name,sa.is_new,s.code,if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,s.total_installments,s.max_chance,s.max_weight,s.amount,c.mobile,if(sa.active =1,'Active','Inactive') as active,sa.date_add
    // 				From
    // 				  ".self::ACC_TABLE." sa
    // 				Left Join ".self::CUS_TABLE." c On (sa.id_customer=c.id_customer)
    // 				Left Join ".self::SCH_TABLE." s On (s.id_scheme=sa.id_scheme)
    // 				Where  sa.active=1 And sa.is_closed=0 And c.active =1 And (s.scheme_type=0 or s.scheme_type=1 or s.scheme_type=2 or s.scheme_type=3 )";
    // 					return $this->db->query($sql)->result_array();
    // 	}
    // }
    function getAmountSchemeAccounts($id = "")
    {
        $id_scheme = $this->input->post('id_scheme');
        //old code 05-12-2022 if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,concat(concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Not Allocated')),' - ',s.code ),concat(s.code,' ',ifnull(sa.scheme_acc_number,'Not Allcoated')))as scheme_acc_number,
        //New code 05-12-2022 if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,concat(concat(ifnull(sa.group_code,''),'-',ifnull(sa.start_year,''),ifnull(sa.scheme_acc_number,'Not Allocated')),' - ',s.code ),concat(s.code,' ',ifnull(sa.start_year,''),ifnull(sa.scheme_acc_number,'Not Allcoated')))as scheme_acc_number,
        if ($id != NULL) {
            $sql = "Select
					  sa.id_scheme_account,
					 if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,concat(concat(ifnull(sa.group_code,''),'-',ifnull(sa.start_year,''),ifnull(sa.scheme_acc_number,'Not Allocated')),' - ',s.code ),concat(s.code,' ',ifnull(sa.start_year,''),ifnull(sa.scheme_acc_number,'Not Allcoated')))as scheme_acc_number,
					  IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) asname,sa.ref_no,sa.account_name,c.is_new,sg.group_code,c.mobile,
					  s.scheme_name,sa.is_new,s.code,if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,s.total_installments,s.max_chance,s.max_weight,s.amount,c.mobile,if(sa.active =1,'Active','Inactive') as active,sa.date_add
					From
					  " . self::ACC_TABLE . " sa
					Left Join " . self::CUS_TABLE . " c On (sa.id_customer=c.id_customer)
						Left Join " . self::SCHGROUP_TABLE . " sg On (sa.id_scheme=sg.id_scheme)
					Left Join " . self::SCH_TABLE . " s On (s.id_scheme=sa.id_scheme)
					Join chit_settings cs
					Where  (sa.active=1 And sa.is_closed=0 And c.active =1) And (s.scheme_type=0 or s.scheme_type=1 or s.scheme_type=2 or s.scheme_type=3 ) And sa.id_scheme_account=" . $id;
            return $this->db->query($sql)->row_array();
        } else {
            //old code 05-12-2022 if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,concat(concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Not Allocated')),' - ',s.code ),concat(s.code,' ',ifnull(sa.scheme_acc_number,'Not Allcoated')))as scheme_acc_number,
            //New code 05-12-2022 if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,concat(concat(ifnull(sa.group_code,''),'-',ifnull(sa.start_year,''),'-',ifnull(sa.scheme_acc_number,'Not Allocated')),'-',s.code ),concat(s.code,'-',ifnull(sa.start_year,''),'-',ifnull(sa.scheme_acc_number,'Not Allcoated')))as scheme_acc_number,
            $branchWiseLogin = $this->session->userdata('branchWiseLogin');
            $is_branchwise_cus_reg = $this->session->userdata('is_branchwise_cus_reg');
            $id_branch = $this->session->userdata('id_branch');
            $uid = $this->session->userdata('uid');
            $sql = "Select
					  sa.id_scheme_account,
					 if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,concat(concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Not Allocated')),' - ',s.code ),concat(s.code,' ',ifnull(sa.scheme_acc_number,'Not Allcoated')))as scheme_acc_number,
					  IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,sa.ref_no,sa.account_name,sa.start_date,sg.group_code,sg.end_date,c.is_new,c.mobile,
					  s.scheme_name,sa.is_new,s.code,if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,s.total_installments,s.max_chance,s.max_weight,s.amount,c.mobile,if(sa.active =1,'Active','Inactive') as active,sa.date_add
					From
					  " . self::ACC_TABLE . " sa
					Left Join " . self::CUS_TABLE . " c On (sa.id_customer=c.id_customer)
						Left Join " . self::SCHGROUP_TABLE . " sg On (sa.id_scheme=sg.id_scheme)
					Left Join " . self::SCH_TABLE . " s On (s.id_scheme=sa.id_scheme)
						left join " . self::BRANCH . " b on (b.id_branch=sa.id_branch)
					Join chit_settings cs
					Where  sa.active=1 And sa.is_closed=0 " . ($id_scheme != '' ? "and sa.id_scheme=" . $id_scheme . "" : '') . " And c.active =1 And  (s.scheme_type=0 or s.scheme_type=1 or s.scheme_type=2 or s.scheme_type=3 )
					" . ($uid != 1 ? ($branchWiseLogin == 1 || $is_branchwise_cus_reg ? ($id_branch != '' ? "and b.id_branch=" . $id_branch . " or b.show_to_all=1" : '') : '') : '') . "
					";
            return $this->db->query($sql)->result_array();
        }
    }
    function set_registration_record($id_customer, $id_scheme, $id_register)
    {
        $data = array(
            'id_register' => $id_register,
            'id_scheme_account' => 0,
            'id_scheme' => $id_scheme,
            'id_customer' => $id_customer,
            'account_name' => NULL,
            'ref_no' => NULL,
            'paid_installments' => 0,
            'start_date' => date('d-m-Y'),
            'employee_approved' => 0,
            'remark_open' => NULL
        );
        return $data;
    }
    /*  //Generate 10 digit Account number random
    function account_number_generator()
    {
      $query = $this->db->query("SELECT LPAD(round(rand() * 1000000000),10,0) as myCode
                                FROM scheme_account
                                HAVING myCode NOT IN (SELECT scheme_acc_number FROM scheme_account) limit 0,1");
        if($query->num_rows()==0){
            $query = $this->db->query("SELECT LPAD(round(rand() * 1000000000),10,0) as myCode");
        }
        return $query->row()->myCode;
    }*/
    //Generate account number
    function account_number_generator($id_scheme, $branch, $ac_group_code)
    {
        $lastno = $this->get_schAccount_no($id_scheme, $branch, $ac_group_code);
        //print_r($this->db->last_query());exit;
        if ($lastno != NULL) {
            $number = (int) $lastno;
            $number++;
            $schAc_number = str_pad($number, 5, '0', STR_PAD_LEFT);
            ;
            //print_r($schAc_number);exit;
            return $schAc_number;
        } else {
            $schAc_number = str_pad('1', 5, '0', STR_PAD_LEFT);
            ;
            return $schAc_number;
        }
    }
    function get_schAccount_no($id_scheme, $branch, $ac_group_code)
    {
        /*
            scheme_wise_acc_no settings done by HH
            0 - Common,
            1 - Common with branch wise,
            2 - Scheme-wise,
            3 - Scheme-wise with branch wise
            For value 2,3 if lucky draw is enabled in schemes means have to generate group wise account number
        */
        $data = $this->get_settings();
        $id_company = $this->session->userdata('id_company');
        $company_settings = $this->session->userdata('company_settings');
        //group wise for lucky draw scheme....
        $sql_lucky = $this->db->query("SELECT is_lucky_draw,max_members FROM scheme WHERE id_scheme=" . $id_scheme);
        $luckyDraw = $sql_lucky->row_array();
        if ($luckyDraw['is_lucky_draw'] == 1 && $luckyDraw['max_members'] > 0) {
            $sqlGrp = $this->db->query("SELECT group_code FROM scheme_group WHERE status = 1 and id_branch = " . $branch . "  and id_scheme=" . $id_scheme);
            $grpCode = $sqlGrp->row()->group_code;
        } else {
            $grpCode = "";
        }
        if ($data['scheme_wise_acc_no'] == 4 || $data['scheme_wise_acc_no'] == 5 || $data['scheme_wise_acc_no'] == 6) {
            $res = $this->db->query("SELECT date(fin_year_from) as fin_date FROM `ret_financial_year` where fin_status = 1");
            $financial_year = $res->row()->fin_date;
        }
        $lg_data1 = "";
        /*   $this->db->query('LOCK TABLES scheme_account WRITE, customer WRITE, scheme WRITE');
        $lg_data1 = "\n CP scheme_account,customer,scheme table locked at --".date('d-m-Y H:i:s');   */
        $log_path = $this->log_dir . '/manual/create_payment_' . date("Y-m-d") . '.txt';
        file_put_contents($log_path, $lg_data1, FILE_APPEND | LOCK_EX);
        if ($data['branch_settings'] == 1) { // Branch Enabled
            if ($data['scheme_wise_acc_no'] == 1 && $branch > 0) { // 1 - Common with branch wise,
                $sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account
                 left join customer on customer.id_customer= scheme_account.id_customer
                 WHERE scheme_account.id_branch=" . $branch . "
                 " . ($grpCode != null && $grpCode != '' ? "AND scheme_account.group_code='" . $grpCode . "'" : '') . "
                 " . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " ORDER BY id_scheme_account DESC ";
            } else if ($data['scheme_wise_acc_no'] == 2) { // 2 - Scheme-wise
                $sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account
                left join customer on customer.id_customer= scheme_account.id_customer
                WHERE scheme_account.id_scheme=" . $id_scheme . "
                " . ($grpCode != null && $grpCode != '' ? "AND scheme_account.group_code='" . $grpCode . "'" : '') . "
                " . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " ORDER BY id_scheme_account DESC ";
                //print_r($sql);exit;
            } else if ($data['scheme_wise_acc_no'] == 3) { // 3 - Scheme-wise with branch wise
                $sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account
                left join customer on customer.id_customer= scheme_account.id_customer
                WHERE scheme_account.id_scheme=" . $id_scheme . " AND scheme_account.id_branch=" . $branch . "
                " . ($grpCode != null && $grpCode != '' ? "AND scheme_account.group_code='" . $grpCode . "'" : '') . "
                " . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " ORDER BY id_scheme_account DESC ";
                //print_r($sql);exit;
            } else if ($data['scheme_wise_acc_no'] == 4) {
                $sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account
                left join customer on customer.id_customer= scheme_account.id_customer
                WHERE date(start_date) BETWEEN '" . $financial_year . "' AND DATE(CURDATE())
                " . ($grpCode != null && $grpCode != '' ? "AND scheme_account.group_code='" . $grpCode . "'" : '') . "
                " . ($id_company != 0 && $company_settings == 1 ? " AND customer.id_company=" . $id_company . "" : '') . " ORDER BY id_scheme_account DESC ";
            } else if ($data['scheme_wise_acc_no'] == 5) // financial year with scheme wise
            {
                $sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account
                left join customer on customer.id_customer= scheme_account.id_customer
                WHERE scheme_account.id_scheme=" . $id_scheme . " and date(scheme_account.start_date) BETWEEN '" . $financial_year . "' AND DATE(CURDATE())
                " . ($grpCode != null && $grpCode != '' ? "AND scheme_account.group_code='" . $grpCode . "'" : '') . "
                " . ($id_company != 0 && $company_settings == 1 ? " AND customer.id_company=" . $id_company . "" : '') . " ORDER BY id_scheme_account DESC ";
            } else if ($data['scheme_wise_acc_no'] == 6) // financial year with scheme & branch wise
            {
                $sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account
                left join customer on customer.id_customer= scheme_account.id_customer
                WHERE scheme_account.id_scheme=" . $id_scheme . " and date(scheme_account.start_date) BETWEEN '" . $financial_year . "' AND DATE(CURDATE()) and scheme_account.id_branch=" . $branch . "
                " . ($grpCode != null && $grpCode != '' ? "AND scheme_account.group_code='" . $grpCode . "'" : '') . "
                " . ($id_company != 0 && $company_settings == 1 ? " AND customer.id_company=" . $id_company . "" : '') . " ORDER BY id_scheme_account DESC ";
                //print_r($sql);exit;
            } else { // If other cases fails,generate common account number
                $sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account
                left join customer on customer.id_customer= scheme_account.id_customer
                " . ($grpCode != null && $grpCode != '' ? "WHERE scheme_account.group_code='" . $grpCode . "'" : 'WHERE scheme_account.added_by != 6') . "
                " . ($id_company != 0 && $company_settings == 1 && $grpCode != null && $grpCode != '' ? "AND customer.id_company=" . $id_company . "" : "") . "
                ORDER BY id_scheme_account DESC ";
            }
        } else {
            if ($data['scheme_wise_acc_no'] == 0) { // 0 - Common
                $sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account
                left join customer on customer.id_customer= scheme_account.id_customer
                " . ($grpCode != null && $grpCode != '' ? "WHERE scheme_account.group_code='" . $grpCode . "'" : 'WHERE scheme_account.added_by != 6') . "
                " . ($id_company != 0 && $company_settings == 1 && $grpCode != null && $grpCode != '' ? "AND customer.id_company=" . $id_company . "" : "") . "
                ORDER BY id_scheme_account DESC ";
            } else if ($data['scheme_wise_acc_no'] == 2) { // 2 - Scheme-wise
                $sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account
                left join customer on customer.id_customer= scheme_account.id_customer
                WHERE scheme_account.id_scheme=" . $id_scheme . "
                " . ($grpCode != null && $grpCode != '' ? "and scheme_account.group_code='" . $grpCode . "'" : '') . "
                " . ($id_company != 0 && $company_settings == 1 ? "and customer.id_company=" . $id_company . "" : '') . " ORDER BY id_scheme_account DESC ";
            } else if ($data['scheme_wise_acc_no'] == 4) {
                $sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account
                left join customer on customer.id_customer= scheme_account.id_customer
                WHERE date(start_date) BETWEEN '" . $financial_year . "' AND DATE(CURDATE())
                 " . ($grpCode != null && $grpCode != '' ? "and scheme_account.group_code='" . $grpCode . "'" : '') . "
                " . ($id_company != 0 && $company_settings == 1 ? " AND customer.id_company=" . $id_company . "" : '') . " ORDER BY id_scheme_account DESC ";
            } else if ($data['scheme_wise_acc_no'] == 5) // financial year with scheme wise
            {
                $sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account
                left join customer on customer.id_customer= scheme_account.id_customer
                WHERE scheme_account.id_scheme=" . $id_scheme . " and date(scheme_account.start_date) BETWEEN '" . $financial_year . "' AND DATE(CURDATE())
                 " . ($grpCode != null && $grpCode != '' ? "and scheme_account.group_code='" . $grpCode . "'" : '') . "
                " . ($id_company != 0 && $company_settings == 1 ? " AND customer.id_company=" . $id_company . "" : '') . " ORDER BY id_scheme_account DESC ";
            } else if ($data['scheme_wise_acc_no'] == 6) // financial year with scheme & branch wise
            {
                $sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account
                left join customer on customer.id_customer= scheme_account.id_customer
                WHERE scheme_account.id_scheme=" . $id_scheme . " and date(scheme_account.start_date) BETWEEN '" . $financial_year . "' AND DATE(CURDATE()) and scheme_account.id_branch=" . $branch . "
                " . ($grpCode != null && $grpCode != '' ? "AND scheme_account.group_code='" . $grpCode . "'" : '') . "
                " . ($id_company != 0 && $company_settings == 1 ? " AND customer.id_company=" . $id_company . "" : '') . " ORDER BY id_scheme_account DESC ";
            } else { // If other cases fails,generate common account number
                $sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account
                left join customer on customer.id_customer= scheme_account.id_customer
                " . ($grpCode != null && $grpCode != '' ? "WHERE scheme_account.group_code='" . $grpCode . "'" : 'WHERE scheme_account.added_by != 6') . "
                " . ($id_company != 0 && $company_settings == 1 && $grpCode != null && $grpCode != '' ? "AND customer.id_company=" . $id_company . "" : '') . "
                ORDER BY id_scheme_account DESC ";
            }
        }
        //print_r($this->db->last_query());exit;
        return $this->db->query($sql)->row()->lastSchAcc_no;
    }
    //check reference exists
    function is_refno_exists($ref_no, $schid)
    {
        $this->db->select('ref_no');
        $this->db->where('scheme_acc_number', $ref_no);
        $this->db->where('id_scheme', $schid);
        $status = $this->db->get(self::ACC_TABLE);
        if ($status->num_rows() > 0) {
            return TRUE;
        }
    }
    //get scheme_account by customer
    function is_uniqueCode_exists($id_customer)
    {
        $this->db->select('ref_no');
        $this->db->where('id_customer', $id_customer);
        $status = $this->db->get(self::ACC_TABLE);
        if ($status->num_rows() > 0) {
            if ($status->row()->ref_no == "") {
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }
    function updateUniqueCode($data, $id_customer)
    {
        $this->db->where('id_customer', $id_customer);
        $status = $this->db->update(self::ACC_TABLE, $data);
        return $status;
    }
    //for scheme join sms and mail
    function get_customer_acc($id_scheme_acc)
    {
        $accounts = $this->db->query("select c.id_customer,
							  sc.id_scheme,maturity_type,s.id_scheme_account,s.id_branch as branch,c.id_branch as cus_reg_branch,sc.code, s.group_code as group_code,sc.sync_scheme_code,
							  if(cs.has_lucky_draw=1 && sc.is_lucky_draw = 1,concat(ifnull(s.group_code,''),'',ifnull(s.scheme_acc_number,'Not allocated')),concat(ifnull(sc.code,''),' ',ifnull(s.scheme_acc_number,'Not allocated'))) as scheme_acc_number,
								  IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,s.ref_no,c.firstname,ifnull(s.account_name,IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,s.start_date,c.is_new,c.email,sc.min_amount, sc.max_amount,
							  sc.scheme_type, flexible_sch_type,
							  sc.scheme_name,s.is_new,sc.code,sc.total_installments,sc.max_chance,sc.payment_chances,sc.max_weight,sc.min_weight,
							  sc.amount,c.mobile,if(s.active =1,'Active','Inactive') as active,s.date_add,cs.currency_name,cs.currency_symbol,cs.custom_entry_date,cs.edit_custom_entry_date,s.total_paid_ins 
							from
							  " . self::ACC_TABLE . " s
							left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
							join chit_settings cs
							where  s.is_closed=0 and s.id_scheme_account =" . $id_scheme_acc);
        return $accounts->row_array();
    }
    // for all active and not closed records
    function get_all_account()
    {
        $accounts = $this->db->query("select
							  s.id_scheme_account,s.scheme_acc_number,IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,s.ref_no,s.account_name,s.start_date,c.is_new,c.id_customer,
							  sc.scheme_name,s.is_new,sc.code,if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,sc.total_installments,sc.max_chance,sc.max_weight,sc.amount,c.mobile,if(s.active =1,'Active','Inactive') as active,s.date_add
							from
							  " . self::ACC_TABLE . " s
							left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
							where  s.is_closed=0");
        return $accounts->result_array();
    }
    function get_customer_accounts($id_customer)
    {
        $accounts = $this->db->query("select c.id_customer,
							  s.id_scheme_account,s.scheme_acc_number,IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,s.ref_no,ifnull(s.account_name,IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,s.start_date,c.is_new,
								c.id_branch as cus_ref_branch,s.id_branch as sch_join_branch,
							  sc.scheme_name,s.is_new,sc.code,if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,sc.total_installments,sc.max_chance,sc.max_weight,sc.amount,c.mobile,if(s.active =1,'Active','Inactive') as active,s.date_add
							from
							  " . self::ACC_TABLE . " s
							left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
							where  s.is_closed=0 and c.id_customer =" . $id_customer);
        //print_r($this->db->last_query());exit;
        return $accounts->result_array();
    }
    /*-- Coded by ARVK --*/
    function sch_acc_count()
    {
        $sql = "SELECT id_scheme_account FROM scheme_account";
        return $this->db->query($sql)->num_rows();
    }
    function company_details()
    {
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $sql = "SELECT * FROM company " . ($id_company != '' && $company_settings == 1 ? " WHERE id_company='" . $id_company . "'" : '') . "";
        return $this->db->query($sql)->row_array();
    }
    function otp_insert($data)
    {
        $status = $this->db->insert(self::OTP_TABLE, $data);
        return $status;
    }
    function otp_update($data, $id)
    {
        $this->db->where('id_sch_acc', $id);
        $status = $this->db->update(self::OTP_TABLE, $data);
        return $status;
    }
    function otp_select($id)
    {
        $this->db->select('id_sch_acc');
        $this->db->where('id_sch_acc', $id);
        $status = $this->db->get(self::OTP_TABLE);
        if ($status->num_rows() > 0) {
            return TRUE;
        }
    }
    function otp_code_select($id)
    {
        $this->db->select('*');
        $this->db->where('id_sch_acc', $id);
        $status = $this->db->get(self::OTP_TABLE);
        return $status->row_array();
    }
    /*-- / Coded by ARVK --*/
    function get_all_account_details()
    {
        $accounts = $this->db->query("select
							  s.id_scheme_account,
							  s.scheme_acc_number,
							  IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
							  c.id_customer,
							  s.ref_no,
							  s.account_name,
							   DATE_FORMAT(s.start_date,'%d-%m-%Y') as start_date,
							  c.is_new,
							  sc.scheme_name,
							  s.is_new,
							  sc.code,
							  if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,
							  sc.total_installments,
							  sc.max_chance,
							  sc.max_weight,
							  sc.amount,
							  c.mobile,
							  if(s.active =1,'Active','Inactive') as active,
							  s.date_add,
							  is_opening,
							  IFNULL(cur_pay.curpay_install,0) +  IF(s.is_opening=1, IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(s.last_paid_date,'%Y%m'),1,0),0) AS curpay_install,
							  IFNULL(cur_pay.curpay_amount,0) AS curpay_amount,
							  IFNULL(cur_pay.curpay_weight,0) +  IF(s.is_opening=1 and sc.scheme_type = 1, IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(s.last_paid_date,'%Y%m'),IFNULL(s.last_paid_weight,0),0),0) AS curpay_weight,
							  IFNULL(total_pay.totalpay_install,0) + IF(s.is_opening=1,IFNULL(s.paid_installments,0),0) AS totalpay_install,
							  IFNULL(total_pay.totalpay_amount,0) + IF(s.is_opening=1,IFNULL(s.balance_amount,0),0) AS totalpay_amount,
							  IFNULL(total_pay.totalpay_weight,0) + IF(s.is_opening=1 and sc.scheme_type = 1,IFNULL(balance_weight,0),0) AS totalpay_weight,
							  (IFNULL(cur_pay.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(s.last_paid_date,'%Y%m'),(s.last_paid_chances),0)) as  chances_used
							FROM
							  " . self::ACC_TABLE . " s
							LEFT JOIN " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							LEFT JOIN " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
							LEFT JOIN (SELECT id_scheme_account,IFNULL(COUNT(DISTINCT(DATE_FORMAT(date_payment,'%Y%m'))),0) AS curpay_install, SUM(IFNULL(payment_amount,0)) AS curpay_amount, SUM(IFNULL(metal_weight,0)) AS curpay_weight,IFNULL(COUNT(DATE_FORMAT(date_payment,'%Y%m')),0) AS chances FROM " . self::PAY_TABLE . " WHERE (payment_status = 0 OR payment_status = 1) AND DATE_FORMAT(date_payment,'%Y%m') = DATE_FORMAT(CURDATE(),'%Y%m') GROUP BY id_scheme_account) AS cur_pay ON cur_pay.id_scheme_account = s.id_scheme_account
							LEFT JOIN (SELECT id_scheme_account,COUNT(DISTINCT(DATE_FORMAT(date_payment,'%Y%m'))) AS totalpay_install, SUM(IFNULL(payment_amount,0)) AS totalpay_amount, SUM(IFNULL(metal_weight,0)) AS totalpay_weight FROM " . self::PAY_TABLE . " WHERE (payment_status = 0 OR payment_status = 1) GROUP BY id_scheme_account) AS total_pay ON total_pay.id_scheme_account = s.id_scheme_account
							where  s.is_closed=0
							GROUP BY s.id_scheme_account
							");
        return $accounts->result_array();
    }
    function get_export_data($filter = "", $from_date = "", $to_date = "")
    {
        $sql = "select
							  s.id_scheme_account,s.ref_no,concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,c.mobile,s.start_date,sc.code,s.date_add
							from
							  " . self::ACC_TABLE . " s
							left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
							where s.active=1 and s.is_closed=0 ";
        switch ($filter) {
            case 0:
                if ($from_date != NULL and $to_date != NULL) {
                    $sql = $sql . " AND (date(s.date_add) BETWEEN '" . $from_date . "' AND '" . $to_date . "') ";
                } else {
                    $sql = $sql . " And date(s.date_add) ='" . $from_date . "'";
                }
                break;
            case 1:
                if ($from_date != NULL and $to_date != NULL) {
                    $sql = $sql . " AND (date(s.date_add)  BETWEEN '" . $from_date . "' AND '" . $to_date . "') And s.ref_no IS Not Null";
                } else {
                    $sql = $sql . " And date(s.date_add) ='" . $from_date . "' And  s.ref_no IS Not Null";
                }
                break;
            case 2:
                if ($from_date != NULL and $to_date != NULL) {
                    $sql = $sql . " AND (date(s.date_add)  BETWEEN '" . $from_date . "' AND '" . $to_date . "') And s.ref_no IS NULL";
                } else {
                    $sql = $sql . " And date(s.date_add) ='" . $from_date . "' And s.ref_no IS NULL";
                }
                break;
        }
        return $this->db->query($sql)->result_array();
    }
    //for getting closing request from customer
    function get_closing_request()
    {
        $accounts = $this->db->query("select
							  s.id_scheme_account,s.scheme_acc_number,concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,s.ref_no,s.account_name,s.start_date,sc.one_time_premium,
							  sc.scheme_name,sc.code,if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,sc.total_installments,sc.max_chance,sc.amount,c.mobile,
							  (if(s.paid_installments is null,0,s.paid_installments) + if(count(distinct month(p.date_payment)) is null,0,count(distinct month(p.date_payment)))) as paid_installments,
      (if(sc.total_installments is null,0,sc.total_installments) -     (if(s.paid_installments is null,0,s.paid_installments) + if(count(distinct month(p.date_payment)) is null,0,count(distinct month(p.date_payment))))) as pending_installments,
       sc.max_chance,sc.amount,c.mobile,
       if(sc.scheme_type=0,(if(s.balance_amount IS NULL,0,s.balance_amount))+if(sum(p.payment_amount) is null,0,sum(p.payment_amount)),'0.00') as closing_amount,
             if(sc.scheme_type=0,(if(s.balance_amount IS NULL,0,s.balance_amount))+if(sum(p.payment_amount) is null,0,sum(p.payment_amount)),(if(s.balance_weight IS NULL,0,s.balance_weight))+if(sum(p.metal_weight) is null,0,sum(p.metal_weight))) as closing_balance,
       remark_close
							from
							  " . self::ACC_TABLE . " s
							left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
							left join " . self::PAY_TABLE . " p on (s.id_scheme_account=p.id_scheme_account)
							where s.req_close=1 and s.active=1 and s.is_closed=0
							group by s.id_scheme_account ");
        return $accounts->result_array();
    }
    /* -- Coded by ARVK -- */
    //for single closed account detail
    //sa.deduction,sa.id_branch, updated code for query staring 05-12-2022
    function get_closed_account_by_id($id)
    {
        $account = $this->db->query("select
			sa.deduction,sa.id_branch,sa.id_scheme_account,IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,s.total_installments,
			concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,cs.has_lucky_draw,s.is_lucky_draw,IFNULL(sa.group_code,'')as scheme_group_code,
			IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
  as oldpaid_installments,
    IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 and s.payment_chances=1) , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as paid_installments,
		c.mobile,sa.account_name,c.nominee_name,c.nominee_mobile,s.scheme_name,
			 if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight',if(s.scheme_type=3,'FLEXIBLE_AMOUNT','Amount To Weight')))as scheme_type,
			 IFNULL(sa.start_year,'') as start_year,
			cs.scheme_wise_acc_no,
			IFNULL(sa.group_code,'')as scheme_group_code,
			(select br.short_name from branch br where br.id_branch = sa.id_branch) as acc_branch,
			cs.schemeaccNo_displayFrmt,
			s.amount as sch_amt,s.scheme_type as sch_typ,s.code,
			 IFNULL(Date_format(sa.start_date,'%d-%m%-%Y'),'-') as start_date,
			 IFNULL(Date_format(sa.closing_date,'%d-%m%-%Y'),'-') as closing_date,
			 IFNULL(sa.closing_date,'') as closed_date,
			(if(sa.balance_amount IS NULL,0,sa.balance_amount))+if(sum(p.payment_amount) is null,0,sum(p.payment_amount)) as total_paid,
			if(s.interest=1,s.total_interest,'0.00') as interest,if(s.tax=1,s.total_tax,'0.00') as tax,
			 if(sum(p.add_charges)!='',sum(p.add_charges),'-') as bank_chgs,
			sa.closing_add_chgs,IFNULL(sa.additional_benefits,0.00) as additional_benefits,
			if(s.scheme_type=0,CONCAT(cs.currency_symbol,' ',(sa.closing_balance)),sa.closing_balance) as closing_balance,
			if(sa.closed_by=1,sa.rep_name,concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')))as closed_by,
			if(sa.closed_by=1,'Nominee','Self')as closedBy,sa.employee_closed,
				(select concat (e.firstname,' ',if(e.lastname!=Null,e.lastname,''))
				from employee e where id_employee=sa.employee_closed) as emp_name,
			if(sa.remark_close!='',sa.remark_close,'-')as remark_close,
			if(sa.closed_by=1,sa.rep_mobile,c.mobile)as otp_verified_mob,cs.currency_symbol,
			s.emp_incentive_closing,s.id_scheme,s.closing_incentive_based_on,sa.id_employee,s.apply_benefit_min_ins
					from  " . self::ACC_TABLE . " sa
					left join " . self::CUS_TABLE . " c on (sa.id_customer=c.id_customer)
					left join " . self::SCH_TABLE . " s on (sa.id_scheme=s.id_scheme)
					left join " . self::PAY_TABLE . " p on (sa.id_scheme_account=p.id_scheme_account)
					join chit_settings cs
					where sa.active=0 and sa.is_closed=1 and p.payment_status=1 and sa.id_scheme_account=" . $id);
        //print_r($this->db->last_query());exit;
        //return $account->row_array();
        $result = [];
        if ($account->num_rows() > 0) {
            $account = $account->row_array();
            if ($account['id_scheme_account'] != '' && $account['id_scheme_account'] != null) {
                $account['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $account['id_scheme_account']);
                $account['type'] = $account['sch_typ'];
                 if ($account['type'] == 0 || ($account['type'] == 3 && ($account['flexible_sch_type'] == 1 || $account['flexible_sch_type'] == 6 || ($account['flexible_sch_type'] == 2 && $account['wgt_convert'] == 2 || $account['wgt_convert'] == 1) || ($account['flexible_sch_type'] == 5 && $account['wgt_store_as'] == 0)))) {
                    $account['is_weight_scheme'] = '0';    //amount
                } else {
                    $account['is_weight_scheme'] = '1';    //weight
                }

                if($account['is_weight_scheme'] == '1'){
                    $account['closing_balance'] = formatMetalWeight($account['closing_balance']);
                }
            }
            $result = $account;
        }
        return $result;
    }
    /* /-- Coded by ARVK -- */
    //for all closed account
    function get_all_closed_account()
    {
        //IF(s.scheme_acc_number is null,'Not Allocated',concat(sc.code,'-',ifnull(concat(s.start_year,'-'),''),s.scheme_acc_number)) as scheme_acc_number,
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $is_branchwise_cus_reg = $this->session->userdata('is_branchwise_cus_reg');
        $id_branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $group_code = $this->input->post('group_code');
        $this->db->select("IFNULL(s.group_code,'-') as group_code,
		IFNULL(s.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = s.id_branch) as acc_branch,
		sc.code,cs.schemeaccNo_displayFrmt,
		sc.is_lucky_draw,cs.scheme_wise_acc_no,
		s.closing_amount,IFNULL(s.closing_weight,'-') as closing_weight,
							  sc.firstPayDisc_value,s.id_scheme_account,sc.code,IFNULL(s.group_code,'')as scheme_group_code,
							  if(cs.scheme_wise_acc_no=3,IF(s.scheme_acc_number is null,concat(b.short_name,sc.code,'-Not Allocated'),concat(b.short_name,sc.code,'-',s.scheme_acc_number)),IF(s.scheme_acc_number is null,'Not Allocated',concat(sc.code,'-',ifnull(concat(s.start_year,'-'),''),s.scheme_acc_number))) as old_scheme_acc_number1,IFNULL(s.old_scheme_acc_number,'-') as old_scheme_acc_number,
							  cs.scheme_wise_acc_no,
							  cs.has_lucky_draw,
							  concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,s.is_utilized as is_utilized,
                             s.id_branch,b.name as branchname,
                                (SELECT b.name FROM branch b WHERE b.id_branch = s.closing_id_branch) as Closing_id_branch,
							  s.ref_no, s.closing_add_chgs, s.account_name,
							  IFNULL(Date_format(s.start_date,'%d-%m%-%Y'),'-') as start_date,
							  IFNULL(Date_format(s.closing_date,'%d-%m%-%Y'),'-') as closing_date,
                             if(sc.scheme_type=0 or (sc.scheme_type=3 and sc.flexible_sch_type=1),CONCAT(cs.currency_symbol,' ',s.closing_amount),CONCAT(s.closing_balance,' ',' g')) as oldclosing_balance,
					          e.firstname as employee_closed,
                                c.added_by,sc.scheme_name,sc.code,
							  if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight',if(sc.scheme_type=3,'FLEXIBLE_AMOUNT','Amount To Weight')))as scheme_type1,
							  FORMAT(if(sc.scheme_type=1,CONCAT('max ',sc.max_weight,' g/month'),if(sc.scheme_type=3 && sc.max_amount!=0,sc.max_amount,if(sc.scheme_type=3 && sc.max_amount=0,(sc.max_weight*(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)),sc.amount))),2) as amount,sc.total_installments,sc.max_chance,c.mobile,
							  IF(sc.scheme_type=1,sc.max_weight,sc.amount) as total_payamt,sc.free_payment,
                              IFNULL(s.total_paid_ins,0) as paid_installments,
                              s.topup_amount as booking_amt,
                                s.topup_weight as booking_wgt, 
                                s.topup_rate as booking_rate,
                                sum(p.payment_amount) as pay_amount,sum(p.act_amount) as act_amount,s.additional_benefits,s.closing_add_chgs,IFNULL(p.discountAmt,0)as discountAmt,s.closing_add_chgs,sc.show_ins_type,
                                s.closing_balance,sc.scheme_type,sc.flexible_sch_type,cs.currency_symbol,sc.wgt_convert,sc.store_closing_balance,IFNULL(s.closing_benefits,'-') as closing_benefits,
                             (IFNULL(s.additional_benefits,'0') + IFNULL(s.closing_benefits,'0')) as total_benefits,
                             IFNULL(s.additional_benefits,'0') as additional_benefits,p.installment, sc.store_closing_balance , s.store_closing_balance_as,s.add_ben_type", FALSE);
        $this->db->from(self::ACC_TABLE . " s JOIN chit_settings cs");
        $this->db->join("employee e", "e.id_employee = s.employee_closed", 'left');
        $this->db->join(self::CUS_TABLE . " c", "s.id_customer=c.id_customer", 'left');
        $this->db->join(self::SCH_TABLE . " sc", "sc.id_scheme=s.id_scheme", 'left');
        $this->db->join(self::PAY_TABLE . " p", "p.id_scheme_account=s.id_scheme_account", 'left');
        $this->db->join(self::BRANCH . " b", "b.id_branch=s.id_branch", 'left');
        $this->db->where('s.active', 0);
        $this->db->where('s.is_closed', 1);
        if ($id_company != 0 && $company_settings == 1) {
            $this->db->where('c.id_company', $id_company);
        }
        if ($group_code != '' && $group_code != null) {
            $this->db->where('s.group_code', $group_code);
        }
        if ($branchWiseLogin == 1 || $is_branchwise_cus_reg == 1) {
            if ($id_branch != '') {
                $this->db->where("(s.closing_id_branch=$id_branch or b.show_to_all=1)", NULL, FALSE);
            }
        }
        $this->db->group_by("s.id_scheme_account");
        $accounts = $this->db->get();
        //print_r($this->db->last_query());exit;
        $account = $accounts->result_array();
        if ($accounts->num_rows() > 0) {
            foreach ($account as $acc) {
                $acc['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $acc['id_scheme_account']);
                $result[] = $acc;
            }
        }
        return $result;
    }
    // for all active and not closed records chked&updtd emp login branchwise  data show//HH
    function get_all_account_by_range($from_date, $to_date, $date_type)
    {
        //DGS-DCNM -->chit_detail_days
        $branch_settings = $this->session->userdata('branch_settings');
        $is_branchwise_cus_reg = $this->session->userdata('is_branchwise_cus_reg');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $id_customer = $this->input->post('id_customer');
        $id_branch = $this->input->post('id_branch');
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $join_days = $this->input->post('join_days');
        $id_scheme = $this->input->post('id_scheme');
        $date_type = $this->input->post('type');
        $group_code = $this->input->post('group_code');
        $this->db->select("sc.is_lumpSum,s.lump_joined_weight,s.lump_payable_weight,s.issue_self_giftBonus,IFNULL(Date_format(s.custom_entry_date,'%d-%m%-%Y'),'-') as custom_entry_date,
        IFNULL(s.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = s.id_branch) as acc_branch,sc.code,cs.schemeaccNo_displayFrmt,sc.is_lucky_draw,ifnull(s.scheme_acc_number,'Not Allocated') as scheme_acc_number,cs.scheme_wise_acc_no,
        IFNULL(s.pan_no,c.pan) as pan_no,cs.has_lucky_draw,date(DATE_ADD(start_date, INTERVAL sc.maturity_days DAY)) as maturity_days,Date_Format(max(pay.date_payment),'%d-%m-%Y') as last_paid_date,
        IFNULL(s.group_code,'-') as group_code,s.fixed_metal_rate,s.fixed_wgt,s.referal_code,b.name as branch_name,
        if(s.show_gift_article=1,'Issued','Not Issueed')as gift,
        (select IF(g.status = 1,'Issued',IF(g.status = 2,'Deducted','-')) from gift_issued g join scheme_account sca on g.id_scheme_account=sca.id_scheme_account where g.id_scheme_account=s.id_scheme_account and g.type=1 LIMIT 1) as gift_article,
        s.id_scheme_account,
        IF(s.scheme_acc_number is null,'Not Allocated',if(cs.scheme_wise_acc_no=3,s.scheme_acc_number, if(s.start_year is not null,(concat(s.start_year,'-',s.scheme_acc_number)), s.scheme_acc_number))) as old_scheme_acc_number1,IFNULL(s.old_scheme_acc_number,'-') as old_scheme_acc_number,
        cs.scheme_wise_acc_no,b.short_name,
        IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,s.ref_no,s.account_name,DATE_FORMAT(s.start_date,'%d-%m-%Y') as start_date,c.is_new,s.added_by,concat('C','',c.id_customer) as id_customer,cs.schemeacc_no_set,
        IF(sc.scheme_type=0 OR sc.scheme_type=2,TRIM(sc.amount),IF(sc.scheme_type=1 ,sc.max_weight,if(sc.scheme_type=3,if(flexible_sch_type = 3 ,  sc.max_weight,if(sc.firstPayamt_as_payamt=1,s.firstPayment_amt ,TRIM(sc.min_amount))),0))) as payable,sc.firstPayamt_as_payamt,s.firstPayment_amt,sc.firstPayamt_maxpayable,
        sc.scheme_name,if(s.is_new ='Y','New','Existing') as is_new,sc.code,IF(sc.scheme_type=0,'Amount',IF(sc.scheme_type=1,'Weight',if(sc.scheme_type=2,'Amount to Weight','Flexible'))) AS scheme_type,sc.total_installments,sc.max_chance,sc.max_weight,sc.amount,c.mobile,CASE WHEN s.active = 1 THEN 'Active'WHEN s.active = 2 THEN 'under_approval'ELSE 'Inactive'END AS active,s.date_add,cs.currency_symbol,s.active as status,s.date_add,cs.currency_symbol,sc.scheme_type  as scheme_types,sc.one_time_premium,sc.otp_price_fix_type,s.firstPayment_amt,
        IFNULL((select IFNULL(IF(s.is_opening=1,IFNULL(s.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 and sc.payment_chances=1) , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0) from payment p where p.payment_status=1 and p.id_scheme_account=pay.id_scheme_account group by p.id_scheme_account),0) as old_paid_installments,
        cs.edit_custom_entry_date,
        IFNULL(e.firstname,'-')  as emp_name,sc.show_ins_type, IF(sc.scheme_type = 0, 'Amount', IF(sc.scheme_type = 1,'Weight',IF(sc.scheme_type = 2,'Amount to Weight',IF(sc.scheme_type = 3, 'Flexible','-')))) as scheme_type,sc.flexible_sch_type,
        a.agent_code,CONCAT(a.firstname,' ',a.lastname) as agent_name,s.id_scheme,sc.max_amount,sc.chit_detail_days as show_wallet,
        IFNULL((select sum(payment_amount) from general_advance_payment where id_scheme_account=s.id_scheme_account),0) as general_advance,
         s.topup_amount as booking_amt,
            s.topup_weight as booking_wgt, 
            s.topup_rate as booking_rate,
            SUM(CASE WHEN pay.payment_status = 1 THEN pay.payment_amount ELSE 0 END) AS total_paid_amt,
        CASE 
            WHEN sc.is_topup_scheme = 1 
            THEN s.topup_amount - SUM(CASE WHEN pay.payment_status = 1 THEN pay.payment_amount ELSE 0 END)
            ELSE 0 
        END AS balance_amt,
        sc.is_topup_scheme,
        IFNULL(SUM(CASE WHEN pay.payment_status = 1 THEN pay.metal_weight ELSE 0 END),0) AS total_saved_wgt,
        IFNULL(SUM(CASE WHEN pay.payment_status = 1 THEN pay.saved_benefits ELSE 0 END),0) AS total_saved_benefit,
        s.duplicate_passbook_issued,IFNULL((SELECT CONCAT(emp.firstname,' ',emp.lastname) FROM employee emp WHERE emp.id_employee = s.id_employee),'-') as created_by,(s.total_paid_ins) as paid_installments,c.id_customer as id_cus", FALSE);
        $this->db->from(self::ACC_TABLE . " s JOIN chit_settings cs");
        $this->db->join(self::CUS_TABLE . " c", "c.id_customer=s.id_customer", 'left');
        $this->db->join(self::SCH_TABLE . " sc", "sc.id_scheme=s.id_scheme", 'left');
        $this->db->join(self::BRANCH . " b", "b.id_branch=s.id_branch", 'left');
        $this->db->join("employee e", "e.emp_code = s.referal_code AND s.referal_code != ''", 'left');
        $this->db->join("agent a", "a.id_agent = s.id_agent", 'left');
        $this->db->join(self::PAY_TABLE . " pay", "pay.id_scheme_account=s.id_scheme_account  and (pay.payment_status=2 or pay.payment_status=1)", 'left');
        // $this->db->join("chit_settings cs", "1=1");
        $this->db->where('s.is_closed', 0);
        if ($id_customer == '') {
            if ($date_type != '' && $date_type == 2) {
                $date_column = 's.custom_entry_date';
            } else {
                $date_column = 's.start_date';
            }
            $this->db->where("date(" . $date_column . ") BETWEEN " . $this->db->escape(date('Y-m-d', strtotime($from_date))) . " AND " . $this->db->escape(date('Y-m-d', strtotime($to_date))), NULL, FALSE);
        } else {
            $this->db->where("s.id_customer", $id_customer);
        }
        if ($id_company != '' && $id_company != 0 && $company_settings == 1) {
            $this->db->where("c.id_company", $id_company);
        }
        if ($id_scheme != '' && $id_scheme != 0) {
            $this->db->where("s.id_scheme", $id_scheme);
        }
        if ($group_code != '') {
            $this->db->where("s.group_code", $group_code);
        }
        if ($uid != 1) {
            if ($branch_settings == 1 && $id_branch != 0 && $id_branch != '') {
                $this->db->where("s.id_branch", $id_branch);
            }
        } elseif ($id_branch != 0 && $id_branch != '') {
            $this->db->where("s.id_branch", $id_branch);
        }
        $this->db->group_by("s.id_scheme_account");
        $this->db->order_by("s.id_scheme_account", "DESC");
        $r = $this->db->get();
        // print_r($this->db->last_query());exit;
        $result = [];
        $account = $r->result_array();
        if ($r->num_rows() > 0) {
            foreach ($account as $acc) {
                $acc['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $acc['id_scheme_account']);
                if ($acc['is_topup_scheme'] == 1) {
                    // $acc['current_slab'] = 
                    // Here we call a get due date function to get a finished slab percent
                    $currentStab = $this->payment_model->get_due_date("", date('Y-m-d'), $acc['id_scheme_account']);
                    //  print_r($currentStab);
                    $acc['current_slab_percent'] = $currentStab[0]['payable_value'];
                    // Here we call a get due date function to get a due from date for a current slab
                    $currentStab = $this->payment_model->get_due_date("custom", date('Y-m-d'), $acc['id_scheme_account']);
                    if ($currentStab[0]['due_date_from'] <= date('Y-m-d')) {
                        $acc['next_due_date'] = date('d-m-Y');
                    } else {
                        $acc['next_due_date'] = date('d-m-Y', strtotime($currentStab[0]['due_date_from']));
                    }
                } else {
                    $acc['current_slab_percent'] = '-';
                    $acc['next_due_date'] = '-';
                }
                $cus_kyc = $this->customer_model->get_kyc_byid($acc['id_cus'], 2);
                if (sizeof($cus_kyc) > 0) {
                    $acc['pan_no'] = $cus_kyc[0]['number'];
                }
                $result[] = $acc;
            }
        }
        return $result;
    }
    function get_all_closed_accdetails($id)
    {
        $sql = "select
							 s.min_amount,s.max_amount,
							  s.id_scheme_account,IFNULL(s.scheme_acc_number,'Not Allocated') as scheme_acc_number,
							  concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,
							  s.ref_no, s.closing_add_chgs, s.account_name,
							  IFNULL(Date_format(s.start_date,'%d-%m%-%Y'),'-') as start_date,
							  IFNULL(Date_format(s.closing_date,'%d-%m%-%Y'),'-') as closing_date,
							  if(sc.scheme_type=0,CONCAT(cs.currency_symbol,' ',s.closing_balance),CONCAT(s.closing_balance,' ',' g')) as closing_balance,
							  c.added_by,sc.scheme_name,sc.code,
							  if(sc.scheme_type=1,CONCAT('max ',sc.max_weight,' g/month'),sc.amount) as amount,
							  if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,sc.total_installments,sc.max_chance,c.mobile
							from
							  " . self::ACC_TABLE . " s
							left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
							join chit_settings cs
							where s.active=0 and s.is_closed=1 and c.id_customer=" . $id . "
							ORDER by s.id_scheme_account DESC Limit 1 ";
        $account = $this->db->query($sql);
        return $account->row_array();
    }
    function get_all_closed_acccount($id)
    {
        $accounts = $this->db->query("select
							  s.id_scheme_account,IFNULL(s.scheme_acc_number,'Not Allocated') as scheme_acc_number,
							  concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,
							  s.ref_no, s.closing_add_chgs, s.account_name,
							  IFNULL(Date_format(s.start_date,'%d-%m%-%Y'),'-') as start_date,
							  IFNULL(Date_format(s.closing_date,'%d-%m%-%Y'),'-') as closing_date,
							  if(sc.scheme_type=0 or (sc.scheme_type=3 and sc.flexible_sch_type=1),CONCAT(cs.currency_symbol,' ',s.closing_balance),CONCAT(s.closing_balance,' ',' g')) as closing_balance,
							  c.added_by,sc.scheme_name,sc.code,
							  if(sc.scheme_type=1,CONCAT('max ',sc.max_weight,' g/month'),sc.amount) as amount,
							  if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,sc.total_installments,sc.max_chance,c.mobile
							from
							  " . self::ACC_TABLE . " s
							left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
							join chit_settings cs
							where s.active=0 and s.is_closed=1 and c.id_customer=" . $id . "");
        return $accounts->num_rows();
    }
    function get_closed_acc_by_range($from_date, $to_date)
    {
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $group_code = $this->input->post('group_code');
        if ($this->branch_settings == 1) {
            $id_branch = $this->input->post('id_branch');
            //	$close_id_branch  = $this->input->post('close_id_branch');
            //$close_id_branch  = ($this->input->post('close_id_branch') > 0 ? $this->input->post('close_id_branch') > 0 : $this->session->userdata('id_branch'));
            $close_id_branch = ($this->input->post('close_id_branch') > 0 ? $this->input->post('close_id_branch') : $this->session->userdata('id_branch'));
        } else {
            $close_id_branch = '';
            $id_branch = '';
        }
        $id_employee = $this->input->post('id_employee');
        $id_scheme = $this->input->post('id_scheme');
        $is_utilized = $this->input->post('is_utilized');
        $this->db->select("ifnull(s.tot_genadv_amt_paid,'0') as tot_genadv_amt_paid, ifnull(s.tot_genadv_wgt_paid,'0.000') as tot_genadv_wgt_paid,
		ifnull(s.tot_genadv_benefit,'0') as tot_genadv_benefit, ifnull(s.tot_genadv_benefit_wgt,'0.000') as tot_genadv_benefit_wgt,
		IFNULL(s.group_code,'-') as group_code,
				                IFNULL(s.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = s.id_branch) as acc_branch,
								sc.code,cs.schemeaccNo_displayFrmt,
								sc.is_lucky_draw,ifnull(s.scheme_acc_number,'Not Allocated') as scheme_acc_number,cs.scheme_wise_acc_no,
							  s.id_scheme_account,ROUND(s.closing_amount) as closing_amount,IFNULL(s.closing_weight,'-') as closing_weight,
							   if(cs.scheme_wise_acc_no=3,IF(s.scheme_acc_number is null,concat(b.short_name,sc.code,'-Not Allocated'),concat(b.short_name,sc.code,'-',s.scheme_acc_number)),IF(s.scheme_acc_number is null,'Not Allocated',concat(sc.code,'-',ifnull(concat(s.start_year,'-'),''),s.scheme_acc_number))) as old_scheme_acc_number1,IFNULL(s.old_scheme_acc_number,'-') as old_scheme_acc_number,
							  cs.scheme_wise_acc_no,
							  concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,s.ref_no,s.account_name, e.firstname as employee_closed,IFNULL(Date_format(s.start_date,'%d-%m%-%Y'),'-') as start_date,IFNULL(Date_format(s.closing_date,'%d-%m%-%Y'),'-') as closing_date,cs.currency_symbol,sc.scheme_type,sc.flexible_sch_type,if(sc.scheme_type=0 or (sc.scheme_type=3 and sc.flexible_sch_type=1),CONCAT(cs.currency_symbol,' ',ROUND(s.closing_balance,2)),CONCAT(s.closing_balance,' ',' g')) as oldclosing_balance,s.closing_balance,c.added_by,
                              s.is_utilized as is_utilized,
                                 s.id_branch,b.name as branchname,
                                (SELECT b.name FROM branch b WHERE b.id_branch = s.closing_id_branch) as Closing_id_branch,
							  sc.scheme_name,sc.code,if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight','Amount to Weight'))as scheme_type1,sc.total_installments,sc.max_chance,
							  if(sc.amount!=0,sc.amount,'-') as amount,c.mobile,sc.scheme_type as sch_typ, IF(sc.scheme_type=1,sc.max_weight,sc.amount) as total_payamt,
							  IFNULL(s.total_paid_ins,0) as paid_installments,
                              pay.total_payment_amount  as pay_amount,sum(p.act_amount) as act_amount,s.closing_add_chgs,IFNULL(p.discountAmt,0)as discountAmt,s.closing_add_chgs,sc.free_payment,sc.firstPayDisc_value,sc.show_ins_type,
                             g.status as gift_status,sc.cus_deduct_ins, s.closing_interest_val,sc.scheme_type as scheme_type,sc.flexible_sch_type,
                             IFNULL(s.closing_benefits,'-') as closing_benefits,
                             (IFNULL(s.additional_benefits,'0') + IFNULL(s.closing_benefits,'0')) as total_benefits,
                             IFNULL(s.additional_benefits,'0') as additional_benefits,sc.wgt_convert,p.installment, sc.store_closing_balance , s.store_closing_balance_as,s.add_ben_type,
                             s.topup_amount as booking_amt,
                            s.topup_weight as booking_wgt, 
                            s.topup_rate as booking_rate,
							 IFNULL(s.dg_other_benefit_wgt,'0') as dg_other_benefit_wgt", FALSE);
        $this->db->from(self::ACC_TABLE . " s JOIN chit_settings cs");
        $this->db->join("employee e", "e.id_employee = s.employee_closed", 'left');
        $this->db->join(self::CUS_TABLE . " c", "s.id_customer=c.id_customer", 'left');
        $this->db->join(self::SCH_TABLE . " sc", "sc.id_scheme=s.id_scheme", 'left');
        $this->db->join(self::PAY_TABLE . " p", "p.id_scheme_account=s.id_scheme_account", 'left');
        $this->db->join("(SELECT id_scheme_account, SUM(payment_amount) AS total_payment_amount FROM payment where payment_status=1 GROUP BY id_scheme_account) pay", "pay.id_scheme_account = s.id_scheme_account", 'left');
        $this->db->join(self::BRANCH . " b", "b.id_branch=s.id_branch", 'left');
        $this->db->join("(select id_scheme_account,status from gift_issued where status = 1 GROUP BY id_scheme_account) as g", "g.id_scheme_account=s.id_scheme_account", 'left');
        $this->db->where('s.active', 0);
        $this->db->where('s.is_closed', 1);
        $this->db->where("date(s.closing_date) BETWEEN " . $this->db->escape(date('Y-m-d', strtotime($from_date))) . " AND " . $this->db->escape(date('Y-m-d', strtotime($to_date))), NULL, FALSE);
        $this->db->where('p.payment_status', 1);
        if ($id_branch != NULL) {
            $this->db->where('s.id_branch', $id_branch);
        }
        if ($close_id_branch != NULL) {
            $this->db->where('s.Closing_id_branch', $close_id_branch);
        }
        if ($id_company != '' && $company_settings == 1) {
            $this->db->where('c.id_company', $id_company);
        }
        if ($id_employee != '') {
            $this->db->where('s.employee_closed', $id_employee);
        }
        if ($id_scheme != '') {
            $this->db->where('s.id_scheme', $id_scheme);
        }
        if ($group_code != '' && $group_code != null) {
            $this->db->where('s.group_code', $group_code);
        }
        if ($is_utilized != '' && $is_utilized != null) {
            $this->db->where('s.is_utilized', $is_utilized);
        }
        $this->db->group_by("s.id_scheme_account");
        $sql = $this->db->get();
        //print_r($this->db->last_query());exit;
        $result = [];
        $account = $sql->result_array();
        if ($sql->num_rows() > 0) {
            foreach ($account as $acc) {
                $acc['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $acc['id_scheme_account']);
                $result[] = $acc;
            }
        }
        return $result;
    }
    function get_all_scheme_account($mobile)
    {
        //DGS-DCNM -->sc.chit_detail_days as show_wallet
        $branchwiselogin = $this->session->userdata('branchWiseLogin');
        $is_branchwise_cus_reg = $this->session->userdata('is_branchwise_cus_reg');
        $id_branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $accounts = $this->db->query("select s.issue_self_giftBonus,IFNULL(s.pan_no,'-') as pan_no,sc.one_time_premium,sc.otp_price_fix_type,s.firstPayment_amt,sc.rate_select,sc.rate_fix_by,
								sc.code,IFNULL(s.group_code,'')as scheme_group_code,cs.has_lucky_draw, sc.is_lucky_draw,Date_Format(s.start_date,'%Y-%m-%d') as join_date,
							  s.id_scheme_account,IFNULL(s.scheme_acc_number,'Not Allocated') as scheme_acc_number ,IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,s.ref_no,s.account_name,DATE_FORMAT(s.start_date,'%d-%m-%Y') as start_date,c.is_new,s.added_by,concat('C','',c.id_customer) as id_customer,IFNULL(s.old_scheme_acc_number,'-') as old_scheme_acc_number,
							  sc.scheme_name,if(s.is_new ='Y','New','Existing') as is_new,sc.code,if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight',if(sc.scheme_type=3,'FLEXIBLE_AMOUNT','Amount To Weight')))as scheme_type,cs.schemeacc_no_set,sc.flexible_sch_type,
							  FORMAT(if(sc.scheme_type=1,sc.max_weight,if(sc.scheme_type=3 && sc.max_amount!=0,sc.max_amount,if(sc.scheme_type=3 && sc.max_amount=0,(sc.max_weight*(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)),sc.amount))),2) as amount,
							  if(s.show_gift_article=1,'Issued','Not Issueed')as gift_article,
							  sc.scheme_type  as scheme_types,sc.one_time_premium,sc.otp_price_fix_type,s.firstPayment_amt,
							  sc.total_installments,sc.max_chance,sc.max_weight,c.mobile,CASE WHEN s.active = 1 THEN 'Active'WHEN s.active = 2 THEN 'under_approval'ELSE 'Inactive'END AS active,s.date_add,cs.currency_symbol,s.active as status  ,s.date_add,cs.currency_symbol,
							  (select IFNULL(IF(s.is_opening=1,IFNULL(s.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 AND sc.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=s.id_scheme_account group by pay.id_scheme_account) as paid_installments,
							  sc.show_ins_type,sc.scheme_type,sc.flexible_sch_type,sc.chit_detail_days as show_wallet
							from
							  " . self::ACC_TABLE . " s
							left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
							left join " . self::PAY_TABLE . " pay on (pay.id_scheme_account=s.id_scheme_account  and (pay.payment_status=2 or pay.payment_status=1))
							left join branch b on (b.id_branch=s.id_branch)
							join chit_settings cs
							Where s.is_closed=0 and  " . ($uid != 1 ? ($branchwiselogin == 1 || $is_branchwise_cus_reg ? ($id_branch != '' ?
                "and s.id_branch=" . $id_branch . " or b.show_to_all=1 " : '') : '') : ($id_branch != '' ?
                "and s.id_branch=" . $id_branch : '')) . " c.mobile like '" . $mobile . "%'
							group by s.id_scheme_account");
        //print_r($this->db->last_query());exit;
        return $accounts->result_array();
    }
    function get_pay_detail($id)
    {
        $this->db->select('id_scheme_account,account_name,scheme.amount');
        $this->db->join(self::SCH_TABLE, "scheme_account.id_scheme=scheme.id_scheme");
        $this->db->where('id_scheme_account', $id);
        $pay = $this->db->get(self::ACC_TABLE);
        return $pay->row_array();
    }
    function get_account_numbers()
    {
        $this->db->select('id_scheme_account,account_name,ref_no');
        $this->db->where('closed_by');
        $accounts = $this->db->get(self::ACC_TABLE);
        return $accounts->result_array();
    }
    function get_accounts_range($lower, $upper)
    {
        $accounts = $this->db->query("select
							  s.id_scheme_account,s.scheme_acc_number,concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,s.ref_no,s.account_name,s.start_date,c.email,
							  sc.scheme_name,sc.code,if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,sc.total_installments,sc.max_chance,sc.amount,c.mobile,s.id_customer
							from
							  " . self::ACC_TABLE . " s
							left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
							where s.active=1 and s.is_closed=0 and
							id_scheme_account Between " . $lower . " and " . $upper . "
							group by s.id_customer");
        return $accounts->result_array();
    }
    function getSchemeAccountByCustomerID($id_customer)
    {
        $this->db->select('id_scheme_account');
        $this->db->where('id_customer', $id_customer);
        $id_scheme_account = $this->db->get(self::ACC_TABLE);
        if ($id_scheme_account->num_rows() == 1) {
            return $id_scheme_account->row()->id_scheme_account;
        } else {
            return 0;
        }
    }
    function get_close_account($id)
    {
        //DGS-DCNM --> DATEDIFF(CURDATE(),date(sa.start_date)) as date_difference,s.restrict_payment,s.scheme_type as sch_type,
        //(Old code 05-12-2022 sa.scheme_acc_number)
        //New Code 05-12-2022 IFNULL(concat(s.code,'-',sa.start_year,'-',sa.scheme_acc_number),'Not Allocated') as scheme_acc_number,
        //old code 10.05.2023 IFNULL(concat(s.code,'-',sa.start_year,'-',sa.scheme_acc_number),'Not Allocated') as scheme_acc_number,
        //RAHUL 24_11_25
        $account = $this->db->query("select sa.gift_debt_amt,
    	s.allow_general_advance,s.apply_adv_benefit,s.allow_general_advance,
    	s.id_metal,s.id_purity,sa.id_branch,sa.id_scheme,sa.id_scheme_account,s.maturity_type,s.calculation_type,s.installment_cycle,s.ins_days_duration,
		s.grace_days,s.maturity_days,  (SELECT IFNULL(COUNT(py.is_limit_exceed),0) FROM payment py where py.is_limit_exceed = 1 and py.payment_status = 1 and py.id_scheme_account = sa.id_scheme_account ) as is_limit_exceed,
		sa.id_scheme_account,s.id_metal,s.is_digi,s.interest as sch_int_setting,
    	IFNULL(sa.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sa.id_branch) as acc_branch,
		s.code,cs.schemeaccNo_displayFrmt,
		s.is_lucky_draw,ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,cs.scheme_wise_acc_no,
    	SUM(p.payment_amount) as closing_paidAmt, SUM(p.metal_weight) as closing_paidWgt, DATEDIFF(CURDATE(),date(sa.start_date)) as date_difference,s.restrict_payment,s.scheme_type as sch_type,
    	s.flexible_sch_type,s.apply_benefit_by_chart,sa.is_refferal_by,sa.referal_code,
    	sa.id_scheme_account,c.id_customer,concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,
    	sa.additional_benefits,if(sa.closing_add_chgs > 0,sa.closing_add_chgs,0) as closing_add_chgs,sa.closing_weight,sa.closing_balance as closing_amount,s.apply_debit_on_preclose,sa.id_scheme,
    	c.cus_img,sa.ref_no,sa.bonus_percent,
    	if(cs.scheme_wise_acc_no=3,if(sa.scheme_acc_number is not NULL && sa.scheme_acc_number!='',concat(br.short_name,s.code,'-',sa.scheme_acc_number),concat(br.short_name,s.code,'- Not Allocated')),IFNULL(concat(s.code,'-',sa.start_year,'-',sa.scheme_acc_number),'Not Allocated')) as old_scheme_acc_number,
    	sa.account_name,c.nominee_name,c.nominee_mobile,c.firstname,one_time_premium,
    	sa.start_date,s.scheme_name,s.code,c.email,s.min_weight,s.max_weight,
    	if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight',if(scheme_type=2,'Amt to Wgt','FLXEBLE_AMOUNT')))as scheme_type,s.total_installments,s.amount,s.scheme_type as sch_typ,s.wgt_convert,s.apply_benefit_min_ins,
    	ifnull(if(s.interest=1 && s.scheme_type=0,s.total_interest,interest_weight),'0.00') as interest,s.interest_by,
    	if(s.tax=1,s.total_tax,'0.00') as tax,
    	IFNULL((select IFNULL(IF(sa.is_opening=1,IFNULL(sa.total_paid_ins,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 and s.payment_chances=1) , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=p.id_scheme_account group by pay.id_scheme_account),0) as oldpaid_installments,
    	(s.total_installments -IFNULL(IF(sa.is_opening=1,IFNULL(sa.total_paid_ins,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0))
    	as pending_installments,
    	sum(CASE
    	WHEN p.due_type = 'PC' THEN 1
    	ELSE 0
    	END )AS pre_close_payments,s.preclose_benefits,
    	(select count(id_scheme_account) from payment where (payment_status=2 or payment_status=7) and id_scheme_account=sa.id_scheme_account) as unapproved_payment,s.allow_preclose,s.preclose_months,IFNULL((select sum(charges) from postdate_payment where id_scheme_account=sa.id_scheme_account),0.00) as bank_chgs,
    	s.max_chance,s.amount,c.mobile,sa.closing_deductions,
    	sum(p.payment_amount) as closing_amount,
    	flexible_sch_type,if(s.scheme_type=0 or s.scheme_type=3 ,if(s.flexible_sch_type = 3 || (s.flexible_sch_type = 2 && s.wgt_store_as = 1), if(sa.balance_weight IS NULL,0,sa.balance_weight)+if(sum(p.metal_weight) is null,0,sum(p.metal_weight)), if(sa.balance_amount IS NULL,0,sa.balance_amount)+if(s.one_time_premium=1 and s.flexible_sch_type=4,sa.fixed_wgt,sum(p.payment_amount)) ),(if(sa.balance_weight IS NULL,0,sa.balance_weight))+if(sum(p.metal_weight) is null,0,sum(p.metal_weight))) as closing_balance,
    	s.payment_chances,
    	IFNULL(sum(p.add_charges),0.00) as charges,
    	sa.remark_close,(select enable_closing_otp from chit_settings) as enable_closing_otp,(select enable_closing_otp from chit_settings) as currency_symbol,
    	s.firstPayDisc_value,sum(p.payment_amount) as cus_paid_amount,sum(p.metal_weight) as cus_paid_weight,sa.fixed_wgt,
    	s.apply_benefit_min_ins,s.emp_refferal,s.emp_deduct_ins,s.agent_refferal,s.agent_deduct_ins,
sa.maturity_date,sa.id_scheme_account,s.store_closing_balance , 
		IFNULL(SUM(p.saved_benefits),0) as dg_saved_benefit_weight,
		IFNULL(SUM(p.saved_benefit_amt),0) as dg_saved_benefit_amount,
		DATEDIFF(CURDATE(),date(sa.start_date)) joined_date_diff,s.is_digi,s.preclose_type,
		IFNULL(SUM(p.dg_other_benefit_wgt),0) as dg_other_benefit_weight,
		IFNULL(SUM(p.dg_other_benefit_amt),0) as dg_other_benefit_amount,
        IFNULL(sa.total_paid_ins,0) as paid_installments 
    	from  " . self::ACC_TABLE . " sa
    	left join " . self::CUS_TABLE . " c on (c.id_customer=sa.id_customer)
    	left join " . self::SCH_TABLE . " s on (s.id_scheme=sa.id_scheme)
        left join (SELECT id_branch,short_name as short_name FROM branch) br on br.id_branch = sa.id_branch
    	left join " . self::PAY_TABLE . " p on (p.id_scheme_account=sa.id_scheme_account)
    	join chit_settings cs
    	where sa.active=1 and sa.is_closed=0 and p.payment_status=1 and sa.id_scheme_account=" . $id);
        //print_r($this->db->last_query());exit;
        //return $account->row_array();
        $result = [];
        if ($account->num_rows() > 0) {
            $account = $account->row_array();
            $account['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $account['id_scheme_account']);
            $result = $account;
        }
        return $result;
    }
    //Get particular account detail
    function get_account_open($id)
    {
        //concat(if(cs.has_lucky_draw=1,sa.group_code,s.code),' ', IFNULL(sa.scheme_acc_number,'Not Allocated')) as scheme_acc_number,
        $sql = "SELECT sa.disable_payment,sa.duplicate_passbook_issued,gc.code as voucher_code,gc.amount as voucher_value,gc.img_url as voucher_img,sa.issue_self_giftBonus,IFNULL(sa.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sa.id_branch) as acc_branch,
		s.code,cs.schemeaccNo_displayFrmt,sa.id_employee,c.phone,
		s.is_lucky_draw,ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,cs.scheme_wise_acc_no,
		sa.`id_scheme_account`, if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as customer,c.mobile,c.passwd,s.scheme_name,s.scheme_type,sa.`id_scheme`, sa.`id_customer`, sa.show_gift_article,sa.firstPayment_amt,cs.get_amt_in_schjoin,
		sa.`id_scheme_account`, if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as customer, if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,c.mobile,c.passwd,s.scheme_name,s.scheme_type,sa.`id_scheme`, sa.`id_customer`, sa.show_gift_article,sa.firstPayment_amt,cs.get_amt_in_schjoin,
        if(cs.scheme_wise_acc_no=3,if(sa.scheme_acc_number is not NULL,concat(b.short_name,s.code,'-',sa.scheme_acc_number),concat(b.short_name,s.code,'-Not Allocated')),concat(if(cs.has_lucky_draw=1,sa.group_code,s.code),' ', IFNULL(sa.scheme_acc_number,'Not Allocated'))) as old_scheme_acc_number,
		 sa.`id_branch`,
				sa.`is_refferal_by`,sa.`referal_code`,cs.`schemeacc_no_set`,IFNULL(sa.scheme_acc_number,'Not Allocated')as acc_number,
				if(cs.has_lucky_draw=1,sa.group_code,s.code) as code,
		`account_name`, sa.`is_new`,IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or s.scheme_type=3, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0)
  as `paid_installments`,sa.paid_installments as previous_paid,c.email,
        DATE_FORMAT(maturity_date, '%d-%m-%Y') as maturity_date,
		DATE_FORMAT(start_date, '%d-%m-%Y') as start_date, `employee_approved`, `remark_open`
		, `is_opening`,`balance_amount`,`balance_weight`,`last_paid_date`, `last_paid_weight`,`last_paid_chances`,sa.active,s.amount,
		IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,CONCAT('max ',s.max_weight,' g/month'),if(s.scheme_type=3,if(flexible_sch_type = 3 ,  CONCAT('max ',s.max_weight,' g/month'),if(s.firstPayamt_as_payamt=1,sa.firstPayment_amt ,s.min_amount)),0))) as payable,
                    a.address1 as address1,
                    a.address2 as address2,
                    a.address3 as address3,st.name as state,ct.name as city,cy.name as country,a.pincode,s.has_voucher,sa.disable_pay_reason,
                    CONCAT_WS(' ',NULLIF(TRIM(a.address1), ''),NULLIF(TRIM(ct.name), ''),NULLIF(TRIM(a.pincode), '')) AS cus_address,sa.total_paid_ins as total_paid_ins,
                    sa.topup_rate,sa.topup_amount,sa.topup_weight,s.topup_booking_type,s.is_topup_scheme,s.topup_min_value,s.topup_max_value,s.topup_denomination,
                    (select id_payment from payment where id_scheme_account = sa.id_scheme_account and payment_status = 1 order by date_payment ASC limit 1) as firstPayID   
		FROM (`" . self::ACC_TABLE . "` sa)
		JOIN chit_settings cs
		LEFT JOIN customer c ON (sa.id_customer=c.id_customer)
		LEFT JOIN scheme s ON (sa.id_scheme=s.id_scheme)
        left join branch b on b.id_branch=sa.id_branch
		LEFT JOIN " . self::PAY_TABLE . " pay on (pay.id_scheme_account=sa.id_scheme_account  and (pay.payment_status=2 or pay.payment_status=1))
		left join address  a on(a.id_customer=c.id_customer)
        left join country cy on (a.id_country=cy.id_country)
        left join state st on (a.id_state=st.id_state)
        left join city ct on (a.id_city=ct.id_city)
        left join village v on v.id_village=c.id_village
        left join gift_card gc on gc.id_scheme_account=sa.id_scheme_account and gc.status=0
		WHERE sa.`id_scheme_account` =" . $id;
        $account = $this->db->query($sql);
        $result = [];
        if ($account->num_rows() > 0) {
            $account = $account->row_array();
            $account['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $account['id_scheme_account']);
            $result = $account;
        }
        return $result;
        //return $account->row_array();
    }
    function get_account_detail($id_scheme_account)
    {
        //old code 05-12-2022 (select sum(pay.payment_amount) from payment pay where pay.payment_status=1 and pay.id_scheme_account=p.id_scheme_account group by pay.id_scheme_account) as closing_amount,
        //New code cls.classification_name
        //New Code 05-12-2022 ((select sum(pay.payment_amount) from payment pay where pay.payment_status=1 and pay.id_scheme_account=p.id_scheme_account group by pay.id_scheme_account)+(ifnull((if(IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ if(s.scheme_type = 1, COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), SUM(p.no_of_dues)), if(s.scheme_type = 1, COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), SUM(p.no_of_dues))),0)=s.total_installments,if(s.scheme_type=0,if(s.interest=1,s.total_interest,'0.00'),if(s.interest=1,s.interest_weight,'0.000')),0.00)+sa.additional_benefits),'0.00'))-ifnull((sa.closing_add_chgs+ifnull(sum(p.add_charges),'0.00')+if(s.tax=1,s.total_tax,'0.00')),0.00) )as closing_amount,
        //IFNULL(concat(s.code,'-',sa.start_year,'-',sa.scheme_acc_number),'Not Allocated') as scheme_acc_number,
        $sql = $this->db->query("SELECT sa.topup_amount,sa.topup_weight,sa.topup_rate,s.is_topup_scheme, s.topup_booking_type,
        s.is_lumpSum,sa.lump_joined_weight,sa.lump_payable_weight,sa.id_scheme_account,IFNULL(sa.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sa.id_branch) as acc_branch,
		s.code,cs.schemeaccNo_displayFrmt,sa.closing_benefits,sa.closing_weight,
		s.is_lucky_draw,ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,cs.scheme_wise_acc_no,
		            s.min_amount,s.max_amount,IFNULL(sa.group_code,'')as scheme_group_code,
		            c.id_customer,sa.remark_open,sa.is_closed,
		            s.one_time_premium,s.description,cls.classification_name, sa.disable_payment,sa.disable_pay_reason,
					c.cus_img,chit.bill_id,b.bill_no,Date_Format(sa.closing_date,'%d-%m-%Y') as closing_date,
					s.scheme_name,s.code,cs.has_lucky_draw,sa.closing_balance as closing_balance,s.otp_price_fixing,sa.fixed_metal_rate,DATE_FORMAT(sa.fixed_rate_on,'%d-%m-%Y') as fixed_rate_on,sa.firstPayment_amt,IFNULL(sa.fixed_wgt,0) as fixed_wgt,IFNULL(sa.fixed_metal_rate,0) as fixed_metal_rate,
					IFNULL(sa.group_code,'')as group_code,s.code,cs.scheme_wise_acc_no,br.short_name,
					if(cs.has_lucky_draw=1,concat(concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Not Allocated')),' - ',s.code ),concat(s.code,ifnull(sa.scheme_acc_number,'Not Allcoated')))as scheme_acc_number1,
					IFNULL(if(cs.scheme_wise_acc_no=3,sa.scheme_acc_number,concat(s.code,'-',sa.start_year,'-',sa.scheme_acc_number)),'Not Allocated') as old_scheme_acc_number1,IFNULL(sa.old_scheme_acc_number,'-') as old_scheme_acc_number,
					ifnull((sa.closing_add_chgs+ifnull(sum(p.add_charges),'0.00')+if(s.tax=1,s.total_tax,'0.00')),0.00)as deductions,
					ifnull((if(IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ if(s.scheme_type = 1, COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), SUM(p.no_of_dues)), if(s.scheme_type = 1, COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), SUM(p.no_of_dues))),0)=s.total_installments,if(s.scheme_type=0,if(s.interest=1,s.total_interest,'0.00'),if(s.interest=1,s.interest_weight,'0.000')),0.00)+sa.additional_benefits),'0.00') as benefits,
					if(sa.is_closed=1 AND sa.active=0,CONCAT('Closed on',' ',Date_Format(sa.closing_date,'%d-%m-%Y')),'Active')  as status,sa.is_closed,
					sa.account_name,
					ifnull(c.firstname,concat(c.firstname,' ',c.lastname))as customer_name,
					c.mobile,
					DATE_FORMAT(sa.`start_date`,'%d-%m-%Y') as start_date,
                    if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight',if(s.scheme_type=2,'Amount to Weight',if(s.flexible_sch_type=2,'Flexible Amount',IF(s.flexible_sch_type = 3 , 'Flexible Weight','Flexible'))))) as scheme_type,
					s.code as scheme_code,
					s.total_installments,s.max_weight,s.maturity_installment,s.maturity_days,IFNULL(DATE_FORMAT(sa.maturity_date,'%d-%m-%Y'),IF(s.is_digi = 1, Date_Format(DATE_ADD(date(sa.start_date), INTERVAL s.total_installments DAY),'%d-%m-%Y'), Date_Format(DATE_ADD(date(sa.start_date), INTERVAL s.total_installments MONTH),'%d-%m-%Y'))) as maturity_date,
					 IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,CONCAT('max ',s.max_weight,' g/month'),if(s.scheme_type=3,if(flexible_sch_type = 3 ,  CONCAT('max ',s.max_weight,' g/month'),if(s.firstPayamt_as_payamt=1,sa.firstPayment_amt ,s.min_amount)),0))) as payable,
					a.address1 as address1,
					a.address2 as address2,
					a.address3 as address3,st.name as state,ct.name as city,cy.name as country,a.pincode,
					if(sa.balance_amount is null,0,sa.balance_amount) as balance_amount,
					if(sa.balance_weight is null,0,sa.balance_weight) as balance_weight,
					s.scheme_type as type,s.amount,s.flexible_sch_type,((select sum(pay.payment_amount) from payment pay where pay.payment_status=1 and pay.id_scheme_account=p.id_scheme_account group by pay.id_scheme_account)+(ifnull((if(IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ if(s.scheme_type = 1, COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), SUM(p.no_of_dues)), if(s.scheme_type = 1, COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), SUM(p.no_of_dues))),0)=s.total_installments,if(s.scheme_type=0,if(s.interest=1,s.total_interest,'0.00'),if(s.interest=1,s.interest_weight,'0.000')),0.00)+sa.additional_benefits),'0.00'))-ifnull((sa.closing_add_chgs+ifnull(sum(p.add_charges),'0.00')+if(s.tax=1,s.total_tax,'0.00')),0.00) )as closing_amount1,
					cs.currency_name,sa.closing_amount,
                    cs.currency_symbol,
                    sa.paid_installments as ins,
				    IFNULL(v.village_name,'') as village_name,
				    classification_name,flexible_sch_type,s.min_weight,
				    br.branch,br.address1 as brn_address1,br.address2 as brn_address2,br.state as brn_state, br.city as brn_city,br.country as brn_country,br.pincode as brn_pincode,
				    bil.pur_ref_no,p.receipt_no,s.is_digi,s.wgt_convert,s.wgt_store_as,
					ifnull(sa.total_paid_ins,0) as paid_installments,
					p.payment_amount,IF(sa.pan_no IS NULL OR sa.pan_no = '', c.pan, sa.pan_no) AS pan_no,s.show_ins_type,c.email,sa.duplicate_passbook_issued,s.allow_general_advance,s.scheme_type as sch_typ,s.wgt_convert,
					s.id_purity,s.id_metal,sa.add_ben_type,s.installment_cycle,s.closing_maturity_days,s.maturity_type
				from customer c
					left join address  a on(a.id_customer=c.id_customer)
					left join country cy on (a.id_country=cy.id_country)
					left join state st on (a.id_state=st.id_state)
					left join city ct on (a.id_city=ct.id_city)
					left join village v on v.id_village=c.id_village
					left join scheme_account sa on(sa.id_customer=c.id_customer)
					left join scheme s on(s.id_scheme=sa.id_scheme)
					left join sch_classify cls on cls.id_classification = s.id_classification
					left join ret_billing_chit_utilization chit on chit.scheme_account_id=sa.id_scheme_account
                    left JOIN ret_billing b on b.bill_id=chit.bill_id
					left join payment p on(sa.id_scheme_account=p.id_scheme_account and p.payment_status=1)
					left join (
					        SELECT pom.id_payment,bill_no,pur_ref_no from ret_billing bill
					            LEFT JOIN payment_old_metal pom on pom.bill_id = bill.bill_id
					) bil on bil.id_payment = p.id_payment
					left join (
					        SELECT id_branch,brn.name as branch,address1,address2,sta.name as state,cit.name as city,co.name as country,pincode,short_name as short_name
                            FROM `branch` brn
                                left join country co on (brn.id_country=co.id_country)
                                left join state sta on (brn.id_state=sta.id_state)
                                left join city cit on (brn.id_city=cit.id_city)
					) br on br.id_branch = sa.id_branch
					left join ( select sch.id_scheme_account ,
						IFNULL(IF(sch.is_opening=1,IFNULL(sch.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 and sc.payment_chances=1) , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0)as paid_ins
					 From payment pay Left Join scheme_account sch on(pay.id_scheme_account=sch.id_scheme_account)
					Left Join scheme sc on(sc.id_scheme=sch.id_scheme) Where (pay.payment_status=2 or pay.payment_status=1) Group By sch.id_scheme_account) paid on (sa.id_scheme_account=paid.id_scheme_account )
					join chit_settings cs
				WHERE sa.id_scheme_account='$id_scheme_account' group by sa.id_scheme_account");
        // print_r($this->db->last_query()); exit;
        $result = [];
        $account = $sql->result_array();
        /* if ($sql->num_rows() > 0) {
            foreach ($account as $acc) {
                $acc['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $acc['id_scheme_account']);
                if ($acc['sch_type'] == 0 || ($acc['sch_type'] == 3 && ($acc['flexible_sch_type'] == 1 || $acc['flexible_sch_type'] == 6 || ($acc['flexible_sch_type'] == 2 && $acc['wgt_convert'] == 2) || ($acc['flexible_sch_type'] == 5 && $acc['wgt_store_as'] == 0)))) {
                    $acc['is_weight_scheme'] = '0';    //amount
                } else {
                    $acc['is_weight_scheme'] = '1';    //weight
                }
                $result = $acc;
            }
        } */
        if ($sql->num_rows() > 0) {
            foreach ($account as $acc) {
                $acc['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $acc['id_scheme_account']);
                if ($acc['type'] == 0 || ($acc['type'] == 3 && ($acc['flexible_sch_type'] == 1 || $acc['flexible_sch_type'] == 6 || ($acc['flexible_sch_type'] == 2 && $acc['wgt_convert'] == 2 || $acc['wgt_convert'] == 1) || ($acc['flexible_sch_type'] == 5 && $acc['wgt_store_as'] == 0)))) {
                    $acc['is_weight_scheme'] = '0';    //amount
                } else {
                    $acc['is_weight_scheme'] = '1';    //weight
                }
                $result = $acc;
            }
        }
        return $result;
    }
    function get_ac_paid_details($id_scheme_account)
    {
        //old code concat(ifnull(concat(p.receipt_year,'-'),''),p.receipt_no) as receipt_no, commented by Durga 18.05.2023
        $this->db->query("SET @a:=0");
        $sql = $this->db->query("SELECT
		          @a := @a + 1 as ins,
                    IFNULL(sa.start_year,'') as start_year,IFNULL(p.receipt_year,'') as receipt_year,
				(select b.short_name from branch b where b.id_branch = sa.id_branch) as acc_branch,
				(select b.short_name from branch b where b.id_branch = p.id_branch) as payment_branch, b.short_name,
				s.code,cs.schemeaccNo_displayFrmt,cs.receiptNo_displayFrmt,s.is_lucky_draw,IFNULL(sa.scheme_acc_number,'') as scheme_acc_number,
				IFNULL(p.receipt_no,'') as receipt_no,cs.scheme_wise_receipt,cs.scheme_wise_acc_no,cs.scheme_wise_acc_no,
				  p.id_payment,p.gst,p.gst_type,
				  if(cs.scheme_wise_receipt=2,p.receipt_no,concat(ifnull(concat(p.receipt_year,'-'),''),p.receipt_no)) as old_receipt_no,
				  DATE_FORMAT(p.date_payment,'%d-%m-%y') as date_payment,is_print_taken,sa.group_code,
				  p.id_scheme_account,
				  p.metal_rate,
				  (IFNULL(p.payment_amount,0)+IFNULL(p.old_metal_amount,0)) as payment_amount,
				  if(p.added_by=3,p.payment_type,p.payment_mode) as payment_mode,p.metal_weight,if(p.remark = '','-',p.remark) as remark
				FROM payment p
				left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
				left join scheme s on s.id_scheme=sa.id_scheme
				left join branch b on b.id_branch=sa.id_branch
				join chit_settings cs
				 WHERE p.id_scheme_account = " . $id_scheme_account . " AND p.payment_status=1 AND receipt_no is not null");
        $result = [];
        $payment = $sql->result_array();
        // 		print_r($this->db->last_query());exit;
        if ($sql->num_rows() > 0) {
            foreach ($payment as $rcpt) {
                $rcpt['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $rcpt['id_scheme_account']);
                $rcpt['receipt_no'] = $this->customer_model->format_accRcptNo('Receipt', $rcpt['id_payment']);
                $result[] = $rcpt;
            }
        }
        return $result;
    }
    function insert_sync($data)
    {
        $status = $this->db->insert(self::SYNC_TABLE, $data);
        return $status;
    }
    function insert_account($data)  // esakki 11-11
    {
        //$data['scheme_acc_number']=NULL;
        /* Coded by ARVK*/
        $sql_scheme = $this->db->query("select s.id_scheme,s.approvalReqForFP,receipt_no_set, s.free_payment, s.amount, s.scheme_type, s.min_weight, s.max_weight, c.company_name, c.short_code  ,s.gst,s.gst_type
	  			from scheme s join company c
	  			join chit_settings cs
	  			where s.id_scheme=" . $data['id_scheme']);
        $sch_data = $sql_scheme->row_array();
        /* / Coded by ARVK*/
        $flag = $this->db->insert(self::ACC_TABLE, $data);
        $status = array(
            'status' => $flag,
            'sch_data' => $sch_data,
            'insertID' => $this->db->insert_id()
        );
        return $status;
    }
    //returns last insert id
    function import_insert_account($data)
    {
        /*$scheme_acc_number=$this->account_number_generator($data['id_scheme']);
        if($scheme_acc_number!=NULL)
        {
            $data['scheme_acc_number']=$scheme_acc_number;
        }*/
        $status = $this->db->insert(self::ACC_TABLE, $data);
        return ($status ? $this->db->insert_id() : $status);
    }
    function update_account($data, $id)
    {
        $this->db->where('id_scheme_account', $id);
        $status = $this->db->update(self::ACC_TABLE, $data);
        return $status;
    }
    //acc no & clientid upd to cus reg tab//HH
    function update_cusreg($data, $id)
    {
        $this->db->where('id_scheme_account', $id);
        $status = $this->db->update(self::CUSREG_TABLE, array('scheme_ac_no' => $data['scheme_acc_number'], 'group_code' => $data['sync_scheme_code'], 'clientid' => $data['ref_no']));
        //  print_r($this->db->last_query());exit;
        return $status;
    }
    //acc no upd to cus reg tab//
    //Receipt no upd to Trans tab//HH
    function update_trans($data, $id)
    {
        $this->db->where('id_scheme_account', $id);
        $status = $this->db->update(self::TRANS_TABLE, array('client_id' => $data['ref_no']));
        //print_r($this->db->last_query());exit;
        return $status;
    }
    //Receipt no upd to Trans tab//
    function update_reg_status($data, $id)
    {
        $this->db->where('id_register', $id);
        $status = $this->db->update(self::REG_TABLE, $data);
        return $status;
    }
    function delete_account($id)
    {
        $data = $this->check_payment($id);
        if ($data['status'] == 1) {
            $this->backupRecTobeDeleted("id_scheme_account", $id, "scheme_account", "deleted_scheme_account");
            $this->db->where('id_scheme_account', $id);
            $status = $this->db->delete(self::ACC_TABLE);
            //print_r($this->db->last_query());exit;
            $status = array("status" => 1, "msg" => "Scheme account deleted successfully");
        } else {
            $status = array("status" => 0, "msg" => $data['msg']);
        }
        return $status;
    }
    function check_payment($id)
    {
        $query = $this->db->query("SELECT p.id_scheme_account FROM payment p where p.id_scheme_account=" . $id . "");
        if ($query->num_rows() > 0) {
            return array("status" => 0, "msg" => "Unable to delete the account. This account has " . $query->num_rows() . " payment(s).");
        } else {
            return array("status" => 1, "msg" => "");
        }
    }
    //delete associated payments
    function delete_payment($data, $id)
    {
        $this->backupRecTobeDeleted("id_scheme_account", $id, "payment", "deleted_payments");
        $this->db->where('id_scheme_account', $id);
        $status = $this->db->delete(self::PAY_TABLE, $data);
        return $status;
    }
    public function backupRecTobeDeleted($where_field, $value, $from_table, $to_table)
    {
        $this->db->where($where_field, $value);
        $payments = $this->db->get($from_table);
        foreach ($payments->result() as $row) {
            if ($this->db->table_exists($to_table)) {
                // table exists (Your query)
                $this->db->insert($to_table, $row);
            } else {
                // Create table
            }
        }
    }
    function get_registration_details()
    {
        $registration = $this->db->query("select
					  id_register,
					  concat(c.firstname,' ',c.lastname) as name,c.mobile,ct.name as city,
					  s.code,if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,s.amount,
					  r.id_customer,r.id_scheme,date_register,c.profile_complete
					from " . self::REG_TABLE . " r
					left join " . self::CUS_TABLE . " c on (r.id_customer=c.id_customer)
					left join " . self::SCH_TABLE . " s on (r.id_scheme=s.id_scheme)
					left join " . self::ADD_TABLE . " a on (c.id_customer=a.id_customer)
					left join city ct on (a.id_city=ct.id_city)
					where r.is_approved=0");
        return $registration->result_array();
    }
    //to get id_scheme_account by id_payment
    function getSchemeAccountByPayment($id_payment)
    {
        $sql = "Select
				  sa.id_scheme_account
			From payment p
			Left join scheme_account sa On (p.id_scheme_account = sa.id_scheme_account)
			Where p.id_payment='$id_payment';";
        $account = $this->db->query($sql);
        return $account->row_array();
    }
    //ref_no in scheme_account
    function clientid_exists($id_scheme_account)
    {
        $sql = "select ref_no from scheme_account where id_scheme_account = " . $id_scheme_account;
        $account = $this->db->query($sql);
        if ($account->num_rows() > 0 && $account->row()->ref_no != '') {
            return array("status" => TRUE, "client_id" => $account->row()->ref_no);
        } else {
            return array("status" => FALSE);
        }
    }
    /*function branchname_list()
    {
        $id_branch = $this->session->userdata('id_branch');
        if( $id_branch !='' )
        {
            $branch=$this->db->query("SELECT b.name,b.id_branch FROM branch b Where id_branch=".$id_branch);
        }
        else
        {
            $branch=$this->db->query("SELECT b.name,b.id_branch FROM branch b");
        }
        return $branch->result_array();
    }*/
    function branchname_list()
    {
        $id_branch = $this->session->userdata('id_branch');
        $login_emp = $this->session->userdata('uid');
        //$id_branch = 0;
        /* if ($id_branch != '' && $id_branch != 0) {
            $branch = $this->db->query("SELECT b.is_ho,b.name,b.id_branch,b.branch_type,b.id_country,b.id_state,IFNULL(b.id_city,'') as id_city FROM branch b Where b.active=1 and id_branch=" . $id_branch);
        }  */
        /*  if($id_branch != '' && $id_branch != 0) {
             $branch = $this->db->query("SELECT b.is_ho,b.name,b.id_branch,b.branch_type,b.id_country,b.id_state,IFNULL(b.id_city,'') as id_city FROM branch b Where b.active=1 AND FIND_IN_SET(b.id_branch,(SELECT login_branches FROM employee WHERE id_employee = ".$login_emp.")) ");
         } */
        if ($id_branch != '' && $id_branch != 0) {
            $row = $this->db->query("SELECT login_branches FROM employee WHERE id_employee = " . $login_emp)->row_array();
            $lb = trim($row['login_branches']);
            if (empty($lb)) {
                // No filter
                $where = "";
            } else {
                $branches = array_filter(array_map('trim', explode(',', $lb)));
                // If contains 0 → remove filter
                if (in_array('0', $branches)) {
                    $where = "";
                } else {
                    // Apply filter
                    $where = " AND b.id_branch IN (" . implode(",", $branches) . ") ";
                }
            }
            $branch = $this->db->query("SELECT b.is_ho,b.name,b.id_branch,b.branch_type,b.id_country,b.id_state,IFNULL(b.id_city,'') as id_city FROM branch b Where b.active=1 {$where} ");
        } else {
            $branch = $this->db->query("SELECT b.is_ho,b.name,b.id_branch,b.branch_type,b.id_country,b.id_state,IFNULL(b.id_city,'') as id_city FROM branch b Where b.active=1");
        }
        return $branch->result_array();
    }
    function get_rptnosettings()
    {
        $sql = "Select c.receipt_no_set FROM chit_settings c where c.id_chit_settings = 1";
        return $this->db->query($sql)->row()->receipt_no_set;
    }
    function get_accnosettings()
    {
        $sql = "Select c.schemeacc_no_set FROM chit_settings c where c.id_chit_settings = 1";
        return $this->db->query($sql)->row()->schemeacc_no_set;
    }
    function get_amt_in_schjoinsettings()
    {
        $sql = "Select c.get_amt_in_schjoin FROM chit_settings c where c.id_chit_settings = 1";
        return $this->db->query($sql)->row()->get_amt_in_schjoin;
    }
    function get_schemegroupsettings()
    {
        $sql = "Select c.has_lucky_draw FROM chit_settings c where c.id_chit_settings = 1";
        return $this->db->query($sql)->row()->has_lucky_draw;
    }
    function update_schemeaccno($id, $data)
    {
        $this->db->where('id_scheme_account', $id);
        $status = $this->db->update(self::ACC_TABLE, $data);
        return $status;
    }
    // referrals code chk validate //
    function checkreferral_code($mbi)
    {
        $query = $this->db->query("SELECT c.mobile as mobile
									FROM customer c where c.mobile=" . $mbi . "");
        if ($query->num_rows() > 0) {
            return TRUE;
        } else {
            $query = $this->db->query("SELECT e.id_employee as id_employee
								FROM employee e where id_employee=" . $mbi . "");
            if ($query->num_rows() > 0) {
                return TRUE;
            } else {
                return FALSE;
                ;
            }
        }
    }
    /*function available_refcode($data){
     $query=$this->db->query("SELECT c.referal_code
                FROM customer c
                where c.id_customer=".$data['id_customer']." and c.referal_code='".$data['referal_code']."'");
       if($query->num_rows()>0){
           return TRUE;
       }else{
           $this->db->where('id_customer',$data['id_customer']);
         $updaterefcode =  $this->db->update('customer',array('referal_code'=>$data['referal_code']));
          return TRUE;
       }
    }*/
    function available_refcode($data)
    {
        if ($data['is_refferal_by'] == 0 && $data['cus_single'] == 0) {
            $query = $this->db->query("SELECT c.cus_ref_code
				FROM customer c
				where c.id_customer=" . $data['id_customer'] . " and c.cus_ref_code='" . $data['referal_code'] . "'");
            if ($query->num_rows() > 0) {
                return TRUE;
            } else {
                $this->db->where('id_customer', $data['id_customer']);
                $updaterefcode = $this->db->update('customer', array('cus_ref_code' => $data['referal_code']));
                return TRUE;
            }
        } else if ($data['is_refferal_by'] == 1 && $data['emp_single'] == 0) {
            $query = $this->db->query("SELECT c.emp_ref_code
				FROM customer c
				where c.id_customer=" . $data['id_customer'] . " and c.emp_ref_code='" . $data['referal_code'] . "'");
            if ($query->num_rows() > 0) {
                return TRUE;
            } else {
                $this->db->where('id_customer', $data['id_customer']);
                $updaterefcode = $this->db->update('customer', array('emp_ref_code' => $data['referal_code']));
                return TRUE;
            }
        } else if ($data['is_refferal_by'] == 0 || $data['is_refferal_by'] == 1 && ($data['cus_single'] == 1 && $data['emp_single'] == 1)) {
            $this->db->where('id_customer', $data['id_customer']);
            $updaterefcode = $this->db->update('customer', array('cus_ref_code' => $data['cus_ref_code'], 'emp_ref_code' => $data['emp_ref_code']));
            return TRUE;
        }
    }
    public function get_settings()
    {
        $sql = "select * from chit_settings";
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    public function veriflyreferral_code($mbi)
    {
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $referral_code = strlen((string) $mbi);
        $data = $this->get_settings();
        if (preg_match('/^[0-9]{10}+$/', $mbi))   // this line added  by gopal
        {
            $status = $this->db->query("SELECT mobile FROM customer WHERE mobile='" . $mbi . "'");
            if ($status->num_rows() > 0) {
                $status = $this->db->query("SELECT mobile FROM employee WHERE mobile='" . $mbi . "'");
                if ($status->num_rows() > 0 && $data['emp_ref_by'] == 1) {
                    return array("status" => TRUE, 'user' => 'EMP');
                } else {
                    return array("status" => TRUE, 'user' => 'CUS');
                }
            } else {
                $status = $this->db->query("SELECT mobile FROM employee WHERE mobile='" . $mbi . "'");
                if ($status->num_rows() > 0 && $data['emp_ref_by'] == 1) {
                    return array("status" => TRUE, 'user' => 'EMP');
                }
                return array("status" => FALSE);
            }
        } else {
            $status = $this->db->query("SELECT emp_code FROM employee WHERE emp_code='" . $mbi . "'");
            if ($status->num_rows() > 0) {
                return array("status" => TRUE, 'user' => 'EMP');
            }
            return array("status" => FALSE);
        }
        return array("status" => FALSE);
    }
    /* public function check_refcode($ref_code)
    {
            $this->db->select('mobile');
            $this->db->where('mobile',$ref_code);
            $status=$this->db->get('customer');
            if($status->num_rows()>0)
            {
                return TRUE;
            }else{
                $this->db->select('mobile');
                $this->db->where('mobile',$ref_code);
                $status=$this->db->get('employee');
                if($status->num_rows()>0)
                {
                    return TRUE;
                }
            }
            return FALSE;
    }*/
    function check_refcode_old($mbi, $id_customer)
    {
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $isEnteredCodevalid = $this->veriflyreferral_code($mbi);
        if ($isEnteredCodevalid['user'] == 'CUS') {
            $referal_cod = $this->db->query("select c.id_customer,c.cus_ref_code as referal_code,cs.cusbenefitscrt_type,cs.empbenefitscrt_type  from customer c join chit_settings cs where c.id_customer='" . $id_customer . "' " . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . "");
        } else {
            $referal_cod = $this->db->query("select c.id_customer,c.emp_ref_code as referal_code,cs.cusbenefitscrt_type,cs.empbenefitscrt_type  from customer c join chit_settings cs where c.id_customer='" . $id_customer . "' " . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . "");
        }
        $referal_code = $referal_cod->row()->referal_code;
        $empSingle = $referal_cod->row()->empbenefitscrt_type;
        $cusSingle = $referal_cod->row()->cusbenefitscrt_type;
        if ($referal_code == null || $referal_code == "") {
            // $isEnteredCodevalid = check whether entered referral code is valid using verify function
            $isEnteredCodevalid = $this->veriflyreferral_code($mbi);
            if ($isEnteredCodevalid['status'] == true) {
                // 		$result = array('status' => true, 'msg' => 'Valid referal Code' );
                //  start ---- employee name -- 15-12-23--- santhosh
                if ($isEnteredCodevalid['user'] == 'CUS') {    // 22-10
                    $query = $this->db->query("SELECT firstname FROM customer WHERE mobile = " . $mbi);
                } else {
                    $query = $this->db->query("SELECT firstname FROM employee WHERE emp_code = " . $mbi);
                }
                $emp_data = $query->row_array();
                $result = array('status' => true, 'msg' => 'Valid referal Code', 'emp_name' => $emp_data['firstname']);
                // 	start ---- employee name -- 15-12-23--- santhosh
            } else {
                $result = array('status' => false, 'msg' => 'Invalid referal Code');
            }
        } else {
            $checkCusRefCodeType = $this->veriflyreferral_code($referal_code);
            $isEnteredCodevalid = $this->veriflyreferral_code($mbi);
            if ($isEnteredCodevalid['status'] == 0) {
                $result = array('status' => false, 'msg' => 'Invalid referal Code');
            } else {
                if ($checkCusRefCodeType['user'] == 'CUS') {
                    $isEnteredCodevalid = $this->veriflyreferral_code($mbi);
                    if ($isEnteredCodevalid['user'] == 'CUS') {
                        if ($cusSingle == 0) {
                            $result = array('status' => false, 'msg' => 'Referal Code Used');
                        } else {
                            $isEnteredCodevalid = $this->veriflyreferral_code($mbi);
                            if ($isEnteredCodevalid['status'] == true) {
                                $result = array('status' => true, 'msg' => 'Valid referal Code');
                            } else {
                                $result = array('status' => false, 'msg' => 'Invalid referal Code');
                            }
                        }
                    } else if ($isEnteredCodevalid['user'] == 'EMP') {
                        if ($empSingle == 0) {
                            $result = array('status' => false, 'msg' => 'Referal Code Used');
                        } else {
                            $isEnteredCodevalid = $this->veriflyreferral_code($mbi);
                            if ($isEnteredCodevalid['status'] == true) {
                                $result = array('status' => true, 'msg' => 'Valid referal Code');
                            } else {
                                $result = array('status' => false, 'msg' => 'Invalid referal Code');
                            }
                        }
                    }
                } else if ($checkCusRefCodeType['user'] == 'EMP') {
                    $isEnteredCodevalid = $this->veriflyreferral_code($mbi);
                    if ($isEnteredCodevalid['user'] == 'CUS') {
                        if ($cusSingle == 0) {
                            $result = array('status' => false, 'msg' => 'Referal Code Used');
                        } else {
                            $isEnteredCodevalid = $this->veriflyreferral_code($mbi);
                            if ($isEnteredCodevalid['status'] == true) {
                                $result = array('status' => true, 'msg' => 'Valid referal Code');
                            } else {
                                $result = array('status' => false, 'msg' => 'Invalid referal Code');
                            }
                        }
                    } else {
                        if ($empSingle == 0) {
                            $result = array('status' => false, 'msg' => 'Referal Code Used');
                        } else {
                            if ($isEnteredCodevalid['status'] == true) {
                                $result = array('status' => true, 'msg' => 'Valid referal Code');
                            } else {
                                $result = array('status' => false, 'msg' => 'Invalid referal Code');
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }
    function check_refcode($ref_code, $ref_by)
    {
        if ($ref_by == 0) {
            $query = $this->db->query("SELECT mobile as code,firstname as name FROM customer WHERE mobile like '%" . $ref_code . "%'");
        } else if ($ref_by == 1) {
            $query = $this->db->query("SELECT emp_code as code,firstname as name FROM employee WHERE emp_code like '%" . $ref_code . "%'");
        }
        $ref_data = $query->result_array();
        if ($query->num_rows() > 0) {
            $result = array('status' => true, 'msg' => 'Valid referal Code', 'data' => $ref_data);
        } else {
            $result = array('status' => false, 'msg' => 'Invalid referal Code', 'data' => "");
        }
        return $result;
    }
    function get_requests_range($from_date, $to_date, $status, $id_branch)
    {
        $qry = $this->db->query("SELECT schReg.is_opening,c.email,schReg.added_by,id_reg_request,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,sch.id_scheme,
 if(cs.has_lucky_draw = 1 && sch.is_lucky_draw = 1,IFNULL(sg.group_code,''),'') as scheme_group_code ,  sch.is_lucky_draw,cs.has_lucky_draw,schReg.id_scheme_group,c.mobile,if(remark = '','-',remark) as remark,schReg.id_customer,schReg.scheme_acc_number,schReg.ac_name,schReg.id_branch,DATE_FORMAT(schReg.date_add,'%d-%m-%Y') AS date_add,schReg.status,schReg.id_scheme,br.id_branch,br.name as branch_name ,schReg.ac_name AS ac_name ,schReg.pan_no,firstPayamt_maxpayable, IFNULL(schReg.firstPayment_amt,'')as firstPayment_amt,sch.scheme_type, cs.getExisting_balance,schReg.paid_installments,schReg.balance_amount,schReg.balance_weight,schReg.last_paid_weight,schReg.last_paid_chances,IFNULL(schReg.last_paid_date,'')as last_paid_date
from scheme_reg_request schReg
LEFT JOIN scheme AS sch ON sch.id_scheme = schReg.id_scheme
LEFT JOIN branch AS br ON br.id_branch = schReg.id_branch
LEFT JOIN customer c  ON c.id_customer = schReg.id_customer
LEFT JOIN scheme_group sg ON sg.id_scheme_group = schReg.id_scheme_group
JOIN chit_settings cs
WHERE   (date(schReg.date_add) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') " . ($status != 3 ? " and schReg.status=" . $status . "" : '') . " " . ($id_branch != '' ? " and schReg.id_branch=" . $id_branch . "" : '') . "");
        //print_r($this->db->last_query());exit;
        return $qry->result_array();
    }
    /*	function get_requests_range($from_date,$to_date,$status)
    {
        $qry=$this->db->query("SELECT c.email,schReg.added_by,id_reg_request,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,sch.id_scheme,
          if(cs.has_lucky_draw = 1,IFNULL(sg.group_code,''),'') as scheme_group_code , cs.has_lucky_draw,schReg.id_scheme_group,c.mobile,if(remark = '','-',remark) as remark,schReg.id_customer,schReg.scheme_acc_number,schReg.ac_name,schReg.id_branch,DATE_FORMAT(schReg.date_add,'%d-%m-%Y') AS date_add,schReg.status,schReg.id_scheme,br.id_branch,br.name as branch_name ,schReg.ac_name AS ac_name ,schReg.pan_no,firstPayamt_maxpayable, IFNULL(schReg.firstPayment_amt,'')as firstPayment_amt,sch.scheme_type
        from scheme_reg_request schReg
        LEFT JOIN scheme AS sch ON sch.id_scheme = schReg.id_scheme
        LEFT JOIN branch AS br ON br.id_branch = schReg.id_branch
        LEFT JOIN customer c  ON c.id_customer = schReg.id_customer
        LEFT JOIN scheme_group sg ON sg.id_scheme_group = schReg.id_scheme_group
        JOIN chit_settings cs
        WHERE (date(schReg.date_add) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($to_date))."') ".($status!=3 ? " and schReg.status=".$status."":'')." ".($id_branch!='' ? " and schReg.id_branch=".$id_branch."":'')."");
    //	print_r($this->db->last_query());exit;
        return $qry->result_array();
    }*/
    function get_existingSchRequests($status)
    {
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $is_branchwise_cus_reg = $this->session->userdata('is_branchwise_cus_reg');
        $id_branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $qry = $this->db->query("SELECT schReg.is_opening,c.email,schReg.added_by,id_reg_request,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,sch.id_scheme,
		  if(cs.has_lucky_draw = 1 && sch.is_lucky_draw = 1,IFNULL(sg.group_code,''),'') as scheme_group_code,cs.has_lucky_draw, sch.is_lucky_draw, schReg.id_scheme_group,c.mobile,if(remark = '','-',remark) as remark,schReg.id_customer,schReg.scheme_acc_number,schReg.ac_name,schReg.id_branch,DATE_FORMAT(schReg.date_add,'%d-%m-%Y') AS date_add,schReg.status,schReg.id_scheme,br.id_branch,br.name as branch_name ,schReg.ac_name AS ac_name,schReg.pan_no,firstPayamt_maxpayable, IFNULL(schReg.firstPayment_amt,'')as firstPayment_amt,sch.scheme_type,
		  cs.getExisting_balance,schReg.paid_installments,schReg.balance_amount,schReg.balance_weight,schReg.last_paid_weight,schReg.last_paid_chances,IFNULL(schReg.last_paid_date,'')as last_paid_date
		from scheme_reg_request schReg
		LEFT JOIN scheme AS sch ON sch.id_scheme = schReg.id_scheme
		LEFT JOIN branch AS br ON br.id_branch = schReg.id_branch
		LEFT JOIN customer c  ON c.id_customer = schReg.id_customer
		LEFT JOIN scheme_group sg ON sg.id_scheme_group = schReg.id_scheme_group
		JOIN chit_settings cs
		" . ($status != '3' ? "  WHERE schReg.status =" . $status : " " . ($uid != 1 ? ($branchWiseLogin == 1 || $is_branchwise_cus_reg == 1 ? ($id_branch != '' ? " Where br.id_branch=" . $id_branch . " or  br.show_to_all=1 " : '') : '') : '') . " ") . "  ");
        //	print_r($this->db->last_query());exit;
        return $qry->result_array();
    }
    //existingSchRequests//
    function get_existingSchRequests_dashboard($status)
    {
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $id_branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $dashboard_branch = $this->session->userdata('dashboard_branch');
        $qry = $this->db->query("SELECT schReg.id_scheme_group,c.email,schReg.added_by,id_reg_request,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,sch.id_scheme,
             c.mobile,if(remark = '','-',remark) as remark,schReg.id_customer,schReg.scheme_acc_number,schReg.ac_name,schReg.id_branch,DATE_FORMAT(schReg.date_add,'%d-%m-%Y') AS date_add,schReg.status,schReg.id_scheme,br.id_branch,br.name as branch_name ,schReg.ac_name AS ac_name
            from scheme_reg_request schReg
            LEFT JOIN scheme AS sch ON sch.id_scheme = schReg.id_scheme
            LEFT JOIN branch AS br ON br.id_branch = schReg.id_branch
            LEFT JOIN customer c  ON c.id_customer = schReg.id_customer
            LEFT JOIN scheme_group sg ON sg.id_scheme_group = schReg.id_scheme_group
            JOIN chit_settings cs  " . ($dashboard_branch != 0 ? ($status != 3 ? "Where schReg.status=" . $status . " and schReg.id_branch=" . $dashboard_branch : " where schReg.id_branch=" . $dashboard_branch) : ($status != 3 ? "where schReg.status=" . $status : "")) . " ");
        //".($status!=3 ?" Where schReg.status=".$status  : "")." ");
        //($uid!=1 ? ($branchWiseLogin==1 ? ($id_branch!='' ? "Where schReg.id_branch=".$id_branch. " or br.show_to_all=1" :''):''):'')
        //".($dashboard_branch!=0 ? " Where sr.id_branch=".$dashboard_branch :'')."  ".($uid!=1 ? ($branchWiseLogin==1? ($id_branch!='' ?  " and  sr.id_branch=".$id_branch." or b.show_to_all=1 ":''):''):'').""
        //print_r($this->db->last_query());
        $existing_data = 0;
        foreach ($qry->result_array() as $row) {
            $existing_data += 1;
        }
        return $existing_data;
    }
    function get_requests_byBranch($id, $status)
    {
        $qry = $this->db->query("SELECT c.email,schReg.added_by,id_reg_request,schReg.id_scheme_group,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,sch.id_scheme,
		  if(cs.has_lucky_draw = 1 && sch.is_lucky_draw = 1,IFNULL(sg.group_code,''),'') as scheme_group_code ,cs.has_lucky_draw, sch.is_lucky_draw, c.mobile,if(remark = '','-',remark) as remark,schReg.id_customer,schReg.scheme_acc_number,schReg.ac_name,schReg.id_branch,DATE_FORMAT(schReg.date_add,'%d-%m-%Y') AS date_add,schReg.status,schReg.id_scheme,br.id_branch,br.name as branch_name ,schReg.ac_name AS ac_name,firstPayamt_maxpayable, IFNULL(schReg.firstPayment_amt,'')as firstPayment_amt,sch.scheme_type,
		  cs.getExisting_balance,schReg.paid_installments,schReg.balance_amount,schReg.balance_weight,schReg.last_paid_weight,schReg.last_paid_chances,IFNULL(schReg.last_paid_date,'')as last_paid_date
		from scheme_reg_request schReg
		LEFT JOIN scheme AS sch ON sch.id_scheme = schReg.id_scheme
		LEFT JOIN branch AS br ON br.id_branch = schReg.id_branch
		LEFT JOIN customer c  ON c.id_customer = schReg.id_customer
		LEFT JOIN scheme_group sg ON sg.id_scheme_group = schReg.id_scheme_group
		JOIN chit_settings cs
		" . ($status != '3' ? "  WHERE schReg.status =" . $status . " and schReg.id_branch=" . $id : " WHERE schReg.id_branch=" . $id) . " ");
        return $qry->result_array();
    }
    function get_schemes()
    {
        $qry = $this->db->query("SELECT id_scheme,code from scheme");
        return $qry->result_array();
    }
    function updateRequest($data, $id)
    {
        $this->db->where('id_reg_request', $id);
        $status = $this->db->update('scheme_reg_request', $data);
        return $status;
    }
    function getDevicetokens()
    {
        $sql = $this->db->query("SELECT r.token as token ,c.mobile
									from registered_devices r
									LEFT JOIN customer c on (c.id_customer=r.id_customer)
									where c.notification = 1;");
        $token = array_map(function ($value) {
            return $value['token'];
        }, $sql->result_array());
        $data = $sql->result_array();
        return $data;
    }
    function get_notiContent($id_notification)
    {
        //Declaration of variables
        $message = "";
        $noti_msg = "";
        $noti_footer = "";
        $msg = "";
        $customer_data = array();
        $data = array();
        $resultset = $this->db->query("SELECT noti_name,noti_name, noti_footer,noti_msg from notification where id_notification = '" . $id_notification . "'");
        foreach ($resultset->result() as $row) {
            $noti_msg = $row->noti_msg;
            $noti_footer = $row->noti_footer;
            $noti_header = $row->noti_name;
            $data = $row->noti_msg;
        }
        $resultset->free_result();
        /*$field_name_footer = explode('@@', $noti_footer);
            for($i=1; $i < count($field_name_footer); $i+=2)
             {
                if(isset($customer_data->$field_name_footer[$i]))
                 {
                    $noti_footer = str_replace("@@".$field_name_footer[$i]."@@",$customer_data->$field_name_footer[$i],$noti_footer);
                }
            }
            //Generating Message content
            $field_name = explode('@@', $noti_msg);
            $a=0;
        foreach($data as $row)
        {
            $customer_data = $row;
            $msgContent = $noti_msg;
            for($i=1; $i < count($field_name); $i+=2)
            {
                if(isset($customer_data[$field_name[$i]]))
                {
                    $msgContent = str_replace("@@".$field_name[$i]."@@",$customer_data[$field_name[$i]],$msgContent);					$data[$a]['message']=$msgContent;
                }
            }
            unset($msgContent);
            $a++;
        }*/
        return (array('data' => $data, 'header' => $noti_header, 'footer' => $noti_footer));
    }
    function getnotificationids($mobile)
    {
        $sql = $this->db->query("SELECT r.uuid as token ,c.mobile
									from registered_devices r
									LEFT JOIN customer c on (c.id_customer=r.id_customer)
									where mobile=" . $mobile);
        $data = $sql->result_array();
        return $data;
    }
    function checkClientID($id_scheme_account = "", $client_id = "")
    {
        if ($id_scheme_account == "" && $client_id != "") {
            $sql = "select id_scheme_account,ref_no,id_scheme from scheme_account where ref_no = '$client_id'";
        } else {
            $sql = "select id_scheme_account,ref_no,id_scheme from scheme_account where id_scheme_account = " . $id_scheme_account;
        }
        $account = $this->db->query($sql);
        if ($account->num_rows() > 0 && $account->row()->ref_no != '') {
            return array("status" => TRUE, "client_id" => $account->row()->ref_no, 'id_scheme_account' => $account->row()->id_scheme_account, 'id_scheme' => $account->row()->id_scheme);
        } else {
            return array("status" => FALSE);
        }
    }
    function isPaymentExist($data)
    {
        if ($data['id_branch'] == NULL || $data['id_branch'] == '') {
            $sql = $this->db->query("select id_scheme_account from scheme_account where id_scheme =" . $data['id_scheme'] . " and id_customer =" . $data['id_customer'] . " and (id_branch is null or id_branch=0) and scheme_acc_number =" . $data['scheme_acc_number']);
        } else {
            $sql = $this->db->query("select id_scheme_account from scheme_account where id_scheme =" . $data['id_scheme'] . " and id_customer =" . $data['id_customer'] . " and id_branch =" . $data['id_branch'] . " and scheme_acc_number =" . $data['scheme_acc_number']);
        }
        if ($sql->num_rows() > 0) {
            $pay = $this->db->query("select id_payment from payment where id_scheme_account =" . $sql->row('id_scheme_account'));
            if ($pay->num_rows() > 0) {
                return array('status' => true, 'id_scheme_account' => $sql->row('id_scheme_account'));
            } else {
                return array('status' => false, 'id_scheme_account' => $sql->row('id_scheme_account'));
            }
        } else {
            return array('status' => false, 'id_scheme_account' => NULL);
        }
    }
    function deleteAcc($data, $id)
    {
        $this->db->where('id_scheme_account', $id);
        $status = $this->db->delete(self::ACC_TABLE, $data);
        return $status;
    }
    //get_Schemegroup
    /*	function get_schemegroup($id_branch = "")
    {
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $usr_branch=$this->session->userdata('id_branch');
        $sql="SELECT count(sa.id_scheme_account) as grp_acc_count,s.id_scheme_group, s.id_scheme,s.id_branch,b.name as branch_name, s.group_code,sch.code as scheme_code, DATE_FORMAT(s.start_date,'%d-%m-%Y') as start_date, DATE_FORMAT(s.end_date,'%d-%m-%Y') as end_date
        FROM scheme_group s
        left join scheme sch on (sch.id_scheme=s.id_scheme)
        left join scheme_account sa on (sa.group_code=s.group_code)
        left join branch b on (b.id_branch=s.id_branch)
        ".($id_company!='' &&  $company_settings == 1? " where sch.id_company='".$id_company."'":'')."
        ";
        if($id_branch != ''){
           $sql = $sql." and s.id_branch=".$id_branch;
        }else if($usr_branch != '') {
           $sql = $sql." and s.id_branch=".$usr_branch;
        }
        $sql = $sql." GROUP BY sa.group_code";
        //echo $sql;exit;
       return $this->db->query($sql)->result_array();
    } */
    function get_schemegroup($id_branch = "")
    {
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $usr_branch = $this->session->userdata('id_branch');
        $sql = "SELECT
				IFNULL((select count(id_scheme_account) from scheme_account where group_code=s.id_scheme_group),0) as grp_acc_count,
				s.id_scheme_group,
				 IFNULL(s.id_scheme,'-')  AS id_scheme,
				 IFNULL(s.id_branch,'-') AS id_branch,
				 IFNULL(b.name,'-')  as branch_name,
				 IFNULL(s.group_code,'-') AS group_code,
				 IFNULL(sch.code,'-') as scheme_code,
				  IFNULL(DATE_FORMAT(s.start_date,'%d-%m-%Y'),'-') as start_date,
				  IFNULL(DATE_FORMAT(s.end_date,'%d-%m-%Y'),'-') as end_date
				FROM scheme_group s
				left join scheme sch on (sch.id_scheme=s.id_scheme)
				left join branch b on (b.id_branch=s.id_branch)
				" . ($id_company != '' && $company_settings == 1 ? " where sch.id_company='" . $id_company . "'" : '') . "
				";
        if ($id_branch != '' && $id_branch != 0) {
            if ($id_company != '' && $company_settings == 1) {
                $sql = $sql . " and s.id_branch=" . $id_branch;
            } else {
                $sql = $sql . " where s.id_branch=" . $id_branch;
            }
        } else if ($usr_branch != '') {
            if ($id_company != '' && $company_settings == 1) {
                $sql = $sql . " and s.id_branch=" . $usr_branch;
            } else {
                $sql = $sql . " where s.id_branch=" . $usr_branch;
            }
        }
        $sql = $sql . " group by s.id_scheme_group";
        //echo $sql;exit;
        return $this->db->query($sql)->result_array();
    }
    function group_empty()
    {
        $data = array(
            'id_scheme_group' => NULL,
            'id_scheme' => NULL,
            'scheme_code' => NULL,
            'group_code' => NULL,
            'id_branch' => NULL,
            'start_date' => NULL,
            'end_date' => NULL,
            'last_update' => NULL,
            'date_add' => date('d-m-Y')
        );
        return $data;
    }
    function insert_groupaccount($data)
    {
        $status = $this->db->insert(self::SCHGROUP_TABLE, $data);
        //echo $this->db->last_query($status);exit;
        return ($status ? $this->db->insert_id() : $status);
    }
    function get_groupaccount_details($id)
    {
        $sql = "SELECT s.id_scheme_group, s.id_scheme, s.group_code,sch.code as scheme_code,s.id_branch,
		 DATE_FORMAT(s.start_date,'%d-%m-%Y') as start_date, DATE_FORMAT(s.end_date,'%d-%m-%Y') as end_date
		FROM scheme_group s
		left join scheme sch on (sch.id_scheme=s.id_scheme)
		WHERE s.id_scheme_group=" . $id . "";
        $account = $this->db->query($sql);
        return $account->row_array();
    }
    function update_groupaccount($data, $id)
    {
        $this->db->where("id_scheme_group", $id);
        //echo $this->db->last_query($status);exit;
        $status = $this->db->update(self::SCHGROUP_TABLE, $data);
        return array('status' => $status, 'updateID' => $id);
    }
    function get_groups()
    {
        $qry = $this->db->query("SELECT id_scheme_group,group_code from scheme_group");
        return $qry->result_array();
    }
    function code_available($group_code)
    {
        $this->db->select('group_code');
        $this->db->where('group_code', $group_code);
        $status = $this->db->get(self::SCHGROUP_TABLE);
        if ($status->num_rows() > 0) {
            return TRUE;
        }
    }
    function delete_group($data, $id)
    {
        $code = $data['group_code'];
        $this->db->select('group_code');
        $this->db->where('group_code', $code);
        $status = $this->db->get(self::ACC_TABLE);
        // print_r($status); exit;
        if ($status->num_rows() > 0) {
            return FALSE;
        } else {
            $this->db->where('id_scheme_group', $id);
            $status = $this->db->delete(self::SCHGROUP_TABLE);
            return TRUE;
        }
    }
    function get_customerenquiry()
    {
        $sql = "select * FROM cust_enquiry ";
        return $this->db->query($sql)->result_array();
    }
    function get_customerenquiry_by_date($from_date, $to_date)
    {
        $sql = "select * FROM cust_enquiry Where (date(date_add) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')";
        return $this->db->query($sql)->result_array();
    }
    function get_all_scheme_account_list($mobile)
    {
        $accounts = $this->db->query("select IFNULL(s.pan_no,'-') as pan_no,
								sc.code,IFNULL(s.group_code,'')as group_code,IFNULL(s.group_code,'')as scheme_group_code,cs.has_lucky_draw, sc.is_lucky_draw,
								(select br.short_name from branch br where br.id_branch = s.id_branch) as acc_branch,
								IFNULL(s.start_year,'') as start_year,cs.scheme_wise_acc_no,cs.schemeaccNo_displayFrmt,
							  s.id_scheme_account,IFNULL(s.scheme_acc_number,'Not Allocated') as scheme_acc_number ,IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,s.ref_no,s.account_name,DATE_FORMAT(s.start_date,'%d-%m-%Y') as start_date,c.is_new,s.added_by,concat('C','',c.id_customer) as id_customer,
							  sc.scheme_name,if(s.is_new ='Y','New','Existing') as is_new,sc.code,if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight',if(sc.scheme_type=3,'FLEXIBLE_AMOUNT','Amount To Weight')))as scheme_type,cs.schemeacc_no_set,
							  FORMAT(if(sc.scheme_type=1,sc.max_weight,if(sc.scheme_type=3 && sc.max_amount!=0,sc.max_amount,if(sc.scheme_type=3 && sc.max_amount=0,(sc.max_weight*(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)),sc.amount))),2) as amount,
							  if(s.show_gift_article=1,'Issued','Not Issueed')as gift_article,
							  sc.scheme_type  as scheme_types,
							  if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight',if(sc.scheme_type=3 && sc.flexible_sch_type=1,'Flx Amount',if(sc.scheme_type=3 && sc.flexible_sch_type=2,'Flx AmtToWgt[Amt]',if(sc.scheme_type=3 && sc.flexible_sch_type=3,'Flx AmtToWgt[Wgt]',if(sc.scheme_type=3 && sc.flexible_sch_type=4,'Flx Wgt [Wgt]','Amount To Weight'))))))as scheme_type,
							  sc.total_installments,sc.max_chance,sc.max_weight,c.mobile,if(s.active =1,'Active','Inactive') as active,s.date_add,cs.currency_symbol,
							 (select IFNULL(IF(s.is_opening=1,IFNULL(s.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 AND sc.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=s.id_scheme_account group by pay.id_scheme_account) as paid_installments
								from
							  " . self::ACC_TABLE . " s
							left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
							left join " . self::PAY_TABLE . " pay on (pay.id_scheme_account=s.id_scheme_account  and (pay.payment_status=2 or pay.payment_status=1))
							left join branch b on (b.id_branch=s.id_branch)
							join chit_settings cs
							Where s.is_closed=0 and c.mobile like '" . $mobile . "%'
							group by s.id_scheme_account");
        //print_r($this->db->last_query());exit;
        return $accounts->result_array();
    }
    function select_otp($otp)
    {
        $this->db->select('*');
        $this->db->where('otp_code', $otp);
        $status = $this->db->get(self::OTP_TABLE);
        //print_r($this->db->last_query());exit;
        return $status->row_array();
    }
    function otp_update_payment($data, $id)
    {
        $this->db->where('id_otp', $id);
        $status = $this->db->update(self::OTP_TABLE, $data);
        //print_r($this->db->last_query());exit;
        return $status;
    }
    function get_scheme_type_closed_account($id_branch, $from_date, $to_date, $type = "")
    {
        $accounts = $this->db->query("select
							  s.id_scheme_account,sc.code,IFNULL(s.group_code,'')as scheme_group_code,IFNULL(s.scheme_acc_number,'NOT Allocated')as scheme_acc_number,cs.has_lucky_draw,
							  concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,
							  s.ref_no, s.closing_add_chgs, s.account_name,
							  IFNULL(Date_format(s.start_date,'%d-%m%-%Y'),'-') as start_date,
							  IFNULL(Date_format(s.closing_date,'%d-%m%-%Y'),'-') as closing_date,
							  if(sc.scheme_type=0,CONCAT(cs.currency_symbol,' ',s.closing_amount),s.closing_balance) as closing_balance,
							  c.added_by,sc.scheme_name,sc.code,b.name as branch,
							  if(sc.scheme_type=0,'Amount',if(sc.scheme_type=1,'Weight',if(sc.scheme_type=3,'FLEXIBLE_AMOUNT','Amount To Weight')))as scheme_type,
							  FORMAT(if(sc.scheme_type=1,CONCAT('max ',sc.max_weight,' g/month'),if(sc.scheme_type=3 && sc.max_amount!=0,sc.max_amount,if(sc.scheme_type=3 && sc.max_amount=0,(sc.max_weight*(SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)),sc.amount))),2) as amount					,sc.total_installments,sc.max_chance,c.mobile
							from
							  " . self::ACC_TABLE . " s
							left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
							left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
							LEFT JOIN employee e ON (e.id_employee = s.employee_closed)
							left join " . self::BRANCH . " b on (b.id_branch=e.id_branch)
							join chit_settings cs
							where s.active=0 and s.is_closed=1  and  (date(s.closing_date) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "')
							and e.id_branch=" . $id_branch . " " . ($type != '' ? "and sc.scheme_type=" . $type . "" : '') . " ");
        return $accounts->result_array();
    }
    // get_group for new sch join //
    /* function get_group($id_scheme)
        {
            //$sql = "SELECT * FROM scheme_group";
            $sql="SELECT s.id_scheme_group, s.id_scheme, s.group_code,sch.code as scheme_code, DATE_FORMAT(s.start_date,'%d-%m-%Y') as start_date, DATE_FORMAT(s.end_date,'%d-%m-%Y') as end_date FROM scheme_group s left join scheme sch on (sch.id_scheme=s.id_scheme)where s.id_scheme=".$id_scheme;
          //print_r($sql);exit;
            return $this->db->query($sql)->result_array();
         }*/
    //gift count option HH//
    function add_gift($data)
    {
        $status = $this->db->insert(self::ISSU_TABLE, $data);
        //echo $this->db->last_query();
        //echo $this->db->_error_message();
        return $status;
    }
    /*public function get_gift_issued($id)
    {
           $qry = $this->db->query("SELECT id_gift_issued,IF(type = 1,'GIFT','PRIZE') as type,id_scheme_account,gift_desc, firstname as id_employee,date_issued,status as gift_status
            FROM `gift_issued`
            LEFT JOIN employee ON gift_issued.id_employee = employee.id_employee
            where id_scheme_account=".$id);
         //print_r($this->db->last_query());exit;
            return $qry->result_array();
    } */
    public function get_gift_issued($id)
    {
        $qry = $this->db->query("SELECT g.id_gift_issued,IF(g.type = 1,'GIFT','PRIZE') as type,g.id_scheme_account,g.gift_desc, e.firstname as id_employee,IFNULL(g.quantity,'-') as quantity,g.id_gift,
        g.date_issued,IFNULL(gift.quantity,0) as quantity_limit,g.status as gift_status,gift.out_stock,g.barcode
        FROM `gift_issued` g
        LEFT JOIN employee e ON e.id_employee = g.id_employee
        left join gifts gift on gift.id_gift=g.id_gift
        where `g`.`id_scheme_account`=" . $id . " and g.status=1");
        // print_r($this->db->last_query());exit;
        return $qry->result_array();
    }
    public function get_gift_issued_form($id)
    {
        $qry = $this->db->query("SELECT g.id_gift_issued,IF(g.type = 1,'GIFT','PRIZE') as type,g.id_scheme_account,g.gift_desc, e.firstname as id_employee,IFNULL(g.quantity,'-') as quantity,g.id_gift,
        g.date_issued,IFNULL(gift.quantity,0) as quantity_limit,g.status as gift_status,gift.out_stock,g.barcode
        FROM `gift_issued` g
        LEFT JOIN employee e ON e.id_employee = g.id_employee
        left join gifts gift on gift.id_gift=g.id_gift
        where `g`.`id_scheme_account`=" . $id . " and g.status=0");
        // print_r($this->db->last_query());exit;
        return $qry->result_array();
    }
    function getAvailableCustomers($SearchTxt)
    {
        $id_company = $this->session->userdata('id_company');
        $company_settings = $this->session->userdata('company_settings');
        $data = $this->db->query("SELECT c.mobile,c.id_customer as value, c.id_company,concat(c.firstname,'-',c.mobile) as label,c.id_village,v.village_name,if(c.is_vip=1,'Yes','No') as vip,
			(select count(sa.id_scheme_account) from scheme_account sa where sa.id_customer=c.id_customer) as accounts
			FROM customer c
			left join village v on v.id_village=c.id_village
			WHERE (username like '%" . $SearchTxt . "%' OR mobile like '%" . $SearchTxt . "%'
			OR firstname like '%" . $SearchTxt . "%')"
            . ($id_company != '' && $id_company != 0 && $company_settings == 1 ? " and c.id_company=" . $id_company . "" : '') . "");
        return $data->result_array();
    }
    function checkSchemeCloseBeiefits($id_scheme)
    {
        $sql = $this->db->query("SELECT * FROM `emp_closing_incentive` WHERE id_scheme=" . $id_scheme . "");
        return $sql->result_array();
    }
    function getMetalRates()
    {
        $sql = $this->db->query("SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1");
        return $sql->row_array();
    }
    function get_ClosedBenefitsDetails($id_scheme_account)
    {
        $sql = $this->db->query("SELECT * FROM `wallet_transaction` WHERE id_sch_ac=" . $id_scheme_account . "");
        if ($sql->num_rows() > 0) {
            $return_data = array('status' => true, 'wallet_details' => $sql->row_array());
        } else {
            $return_data = array('status' => false);
        }
        return $return_data;
    }
    function get_metal_name()
    {
        $sql = $this->db->query("SELECT * FROM metal");
        return $sql->result_array();
    }
    function get_ratesByJoin($id_scheme_account)
    {
        $today = date('Y-m-d');
        $res = $this->db->query("SELECT  DATE_FORMAT(start_date,'%Y-%m-%d') as start_date from scheme_account where id_scheme_account = " . $id_scheme_account);
        $start_date = $res->row()->start_date;
        $sql = ("SELECT m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct FROM metal_rates m
	    WHERE date(m.add_date) BETWEEN '" . $start_date . "' AND '" . $today . "' ORDER BY goldrate_22ct ASC LIMIT 1");
        return $this->db->query($sql)->row_array();
    }
    function getDiscountByjoin($id_scheme_account, $id_scheme, $start_date)
    {
        //$start_date = '2022-01-01';
        $current_date = date('Y-m-d');
        $count_months = 1 + ((date('Y', strtotime($current_date)) - date('Y', strtotime($start_date))) * 12) + (date('m', strtotime($current_date)) - date('m', strtotime($start_date)));
        $sql = "SELECT * FROM `scheme_benefit_deduct_settings` WHERE installment_from <= '" . $count_months . "' AND installment_to >= '" . $count_months . "' AND id_scheme = " . $id_scheme;
        //print_r($sql);exit;
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            return $result->row_array();
        } else {
            return 0;
        }
    }
    function insert_kyc($data)
    {
        $status = $this->db->insert('kyc', $data);
        return array('status' => $status, 'insertID' => $this->db->insert_id());
    }
    function get_customer_kycDetails($id_customer)
    {
        $kyc = $this->db->query("SELECT kyc_type,number from kyc where status=2 and id_customer = " . $id_customer);
        $kycData = $kyc->result_array();
        foreach ($kycData as $k) {
            if ($k['kyc_type'] == 2) {
                $result['pan_no'] = $k['number'];
                $result['pan_name'] = $k['name'];
            } else if ($k['kyc_type'] == 3) {
                $result['aadhaar_no'] = $k['number'];
                $result['aadhaar_name'] = $k['name'];
            }
        }
        return $result;
    }
    // function get_gifts_name()
    // {
    // 	$branch = $this->session->userdata('id_branch');
    // 	$id_branch = $this->input->post('id_branch');
    // 	$sql = "SELECT * FROM gifts where status=1";
    // 	if ($branch != '' && $branch > 0) {
    // 		$sql = $sql . " and id_branch=" . $branch;
    // 	} else if ($id_branch != '' && $id_branch > 0) {
    // 		$sql = $sql . " and id_branch=" . $id_branch;
    // 	}
    // 	return $this->db->query($sql)->result_array();
    // }
    function get_gifts_name()
    {
        $sql = "SELECT gm.id_other_item as id_gift, item.name as gift_name
                FROM `gift_mapping` gm
                left join ret_other_inventory_item item on (item.id_other_item = gm.id_other_item) where item.name is not null
                group by gm.id_other_item";
        return $this->db->query($sql)->result_array();
    }
    function get_gift_bystock()
    {
        $id_branch = $this->input->get('id_branch');
        $sql = "SELECT * FROM gifts where status=1 and out_stock <= quantity";
        if ($id_branch > 0 && $id_branch != '' && id_branch != null) {
            $sql = $sql . " and id_branch=" . $id_branch;
        }
        return $this->db->query($sql)->result_array();
    }
    function update_gift_master($data, $id)
    {
        $sql = "UPDATE gifts SET out_stock=out_stock+" . $data . " where id_gift=" . $id;
        $status = $this->db->query($sql);
        return $status;
    }
    function get_gift_master_details($id)
    {
        $sql = "select id_gift,quantity from gift_issued where type=1 and status=1 and id_scheme_account=" . $id;
        $gift_data = $this->db->query($sql);
        $count = $gift_data->num_rows();
        $gifts = $gift_data->result_array();
        //print_r($gifts);exit;
        if ($count > 0) {
            foreach ($gifts as $gift) {
                $sql_gift = "UPDATE gifts SET out_stock=out_stock-" . $gift['quantity'] . " where id_gift=" . $gift['id_gift'];
                $res = $this->db->query($sql_gift);
            }
        }
    }
    function getEmpBenefit($id_scheme_account)
    {
        $sql = $this->db->query("SELECT * FROM wallet_transaction where type=0 and transaction_type=0 and id_sch_ac = " . $id_scheme_account);
        if ($sql->num_rows > 0) {
            return $sql->row_array();
        } else {
            return 0;
        }
    }
    function verifyAgentCode($agent_code)
    {
        $status = $this->db->query("SELECT id_agent,agent_code FROM agent WHERE agent_code='" . $agent_code . "'");
        if ($status->num_rows() > 0) {
            return array("status" => TRUE, 'agent' => $status->row_array());
        }
        return array("status" => FALSE);
    }
    function getAgentBenefit($id_scheme_account)
    {
        $sql = $this->db->query("SELECT SUM(unsettled_cash_pts) as cash_pts,id_agent,id_payment,id_scheme_account,cus_loyal_cus_id FROM ly_customer_loyalty_transaction where ly_trans_type=3 and tr_cus_type =4 and id_scheme_account = " . $id_scheme_account);
        if ($sql->num_rows > 0) {
            return $sql->row_array();
        } else {
            return 0;
        }
    }
    function insert_gift_issued($data)
    {
        $status = $this->db->insert('gift_issued', $data);
        return $status;
    }
    function update_gift_issued($data, $id)
    {
        $this->db->where('id_scheme_account', $id);
        $status = $this->db->update('gift_issued', $data);
        return $status;
    }
    function is_agent_exist($agent_code)
    {
        $sql = $this->db->query("select * from agent where agent_code =" . $agent_code);
        if ($sql->num_rows() > 0) {
            $result = array("status" => 1, "msg" => "Agent code exist");
        } else {
            $result = array("status" => 0, "msg" => "Agent code doesnot exists.");
        }
        return $result;
    }
    function get_financialYear()
    {
        $res = $this->db->query("SELECT fin_year_code FROM `ret_financial_year` where fin_status = 1");
        $financial_year = $res->row()->fin_year_code;
        return $financial_year;
    }
    function getCustomerByCode($ref_code)
    {
        $referEmpData = $this->db->query("SELECT id_customer from customer where mobile =" . $ref_code);
        $cus_id = $referEmpData->row()->id_customer;
        $sql1 = $this->db->query("SELECT referal_code,id_scheme_account from scheme_account where referal_code != '' and is_refferal_by = 1 and id_customer=" . $cus_id);
        if ($sql1->num_rows() > 0) {
            return $sql1->row_array();
        } else {
            return FALSE;
        }
    }
    function is_refno_exists_import($ref_no, $sch_id)
    {
        $this->db->select('ref_no');
        $this->db->where('scheme_acc_number', $ref_no);
        $this->db->where('id_scheme', $sch_id);
        $status = $this->db->get('scheme_account');
        if ($status->num_rows() > 0) {
            $result = 1;
        } else {
            $result = 0;
        }
        return $result;
    }
    function get_remark_data($from_date, $to_date)
    {
        $sql = $this->db->query("SELECT ag.id_agent as id_employee,ag.firstname as employee_name,c.mobile,c.firstname as name,
         pr.remark,pr.id_scheme_account,DATE_FORMAT(pr.date_created,'%d-%m-%Y') as date_created ,IFNULL(concat(s.code,' ',sa.scheme_acc_number),'Not Allocated') as scheme_acc_number
         from payment_collection_remarks pr
         left join scheme_account sa on sa.id_scheme_account=pr.id_scheme_account
         left join scheme s on s.id_scheme=sa.id_scheme
         left join customer c on c.id_customer = sa.id_customer
         left join agent ag on ag.id_agent=pr.id_agent
         WHERE date(pr.date_created) BETWEEN '" . $from_date . "' AND '" . $to_date . "'");
        //echo $this->db->last_query();exit;
        return $sql->result_array();
    }
    /*SCHEME WISE OUTSTANDING REPORT STARTS */
    function get_all_scheme_account_by_range()  // esakki 11-11
    {
        //$common_db = $this->load->database('common_db',true);
        //$from_date= $this->input->post('from_date');
        //$to_date= $this->input->post('to_date');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $id_scheme = $this->input->post('id_scheme');
        $id_branch = $this->input->post('id_branch');
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $branch = $this->session->userdata('id_branch');
        $group_code = $this->input->post('id_group');
        $singlefilter = $this->input->post('singlefilter');
        if ($singlefilter != '') {
            $payment_date_filter = date('Y-m-d', strtotime($singlefilter));
        } else {
            $payment_date_filter = '';
        }
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        // Turn caching on
        $this->db->cache_on();
        $sql = $this->db->query("select IFNULL(Date_Format(max(pay.date_payment),'%d-%m-%Y'),'-') as last_paid_date,
		 s.id_scheme_account,
		 IFNULL(s.scheme_acc_number,'NOT ALLOCATED') as scheme_acc_number,
		 IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
		 s.account_name,DATE_FORMAT(s.start_date,'%d-%m-%Y') as start_date,sc.code,IFNULL(s.group_code,'-') as group_code,sc.id_metal,
		  ifnull(v.village_name,'-') as area,
		 sc.scheme_name,IF(sc.scheme_type=0,'Amount',IF(sc.scheme_type=1,'Weight',if(sc.scheme_type=2,'Amount to Weight','Flexible'))) AS scheme_type,
		 sc.amount,c.mobile,if(s.active =1 and s.is_closed = 0,'Live','Closed') as active,cs.currency_symbol,
        IFNULL(s.total_paid_ins,0) as paid_installments,
		 SUM(IFNULL(pay.payment_amount,0)) AS totalpay_amount, 
		 IF(sc.scheme_type = 0, 'Amount', IF(sc.scheme_type = 1,'Weight',IF(sc.scheme_type = 2,'Amount to Weight',IF(sc.scheme_type = 3, 'Flexible','-')))) as scheme_type,sc.flexible_sch_type,
		 SUM(IFNULL(pay.metal_weight,0)) AS total_wgt,
		 if(sc.scheme_type!=0 && sc.flexible_sch_type!=1, SUM(IFNULL(pay.metal_weight,0)),0) AS total_wgt1,
		 if(s.added_by=1,'Admin',if(s.added_by=0,'Web App',if(s.added_by=2,'Mobile App',if(s.added_by=3,'Collection App',if(s.added_by=4,'Retail',if(s.added_by=5,'Sync',if(s.added_by=6,'Import','-'))))))) as joined_thru,
		 ifnull((concat(e.firstname,' - ', e.emp_code)),'-') as joined_emp,IFNULL(adrs.address1,'-') as address1,IFNULL(adrs.address2,'-') as address2,IFNULL(adrs.address3,'-') as address3,
		 IFNULL(adrs.pincode,'-') as pincode,IFNULL(city.name,'-') as city,IFNULL(state.name,'-') as state,
	     date_format(IFNULL(s.maturity_date,DATE_ADD(date(s.start_date), INTERVAL sc.maturity_days DAY)),'%d-%m%-%Y') as maturity_date,
		 IFNULL((select concat(IFNULL(e.firstname,''),' ',IFNULL(e.lastname,''),'-',IFNULL(e.emp_code,'')) from employee e left join scheme_account ssa on ssa.referal_code=e.emp_code WHERE ssa.id_scheme_account=s.id_scheme_account and ssa.referal_code is not null and ssa.referal_code!='' and ssa.is_refferal_by is not null and ssa.is_refferal_by=1 group by ssa.id_scheme_account,e.emp_code),'-') as referred_employee,
         s.topup_amount as booking_amt,
        s.topup_weight as booking_wgt, 
        s.topup_rate as booking_rate,
        SUM(CASE WHEN pay.payment_status = 1 THEN pay.payment_amount ELSE 0 END) AS total_paid_amt,
        CASE 
            WHEN sc.is_topup_scheme = 1 
            THEN s.topup_amount - SUM(CASE WHEN pay.payment_status = 1 THEN pay.payment_amount ELSE 0 END)
            ELSE '-'
        END AS balance_amt,
        sc.is_topup_scheme
		 from
		 " . self::ACC_TABLE . " s
		 left join " . self::CUS_TABLE . " c on (s.id_customer=c.id_customer)
		 left join " . self::SCH_TABLE . " sc on (sc.id_scheme=s.id_scheme)
		 left join " . self::BRANCH . " b on (b.id_branch=s.id_branch)
		 left join employee e on (e.id_employee = s.id_employee)
		 LEFT JOIN (SELECT * FROM address GROUP BY id_customer) adrs ON c.id_customer = adrs.id_customer
		 left join city city on city.id_city=adrs.id_city
		 left join state state on state.id_state=adrs.id_state
		 left join " . self::PAY_TABLE . " pay on (pay.id_scheme_account=s.id_scheme_account  and (pay.payment_status=1))
		 left join village v on v.id_village = c.id_village
		 join chit_settings cs
		 Where s.active=1 and sc.active = 1 
		 " . ($id_branch != '' && $id_branch != 0 && $branch == 0 ? " and s.id_branch=" . $id_branch . "" : '') . "
		 " . ($id_scheme != '' && $id_scheme != 0 ? " and s.id_scheme=" . $id_scheme . "" : '') . "
		 " . ($id_company != '' && $id_company != 0 && $company_settings == 1 ? " and c.id_company=" . $id_company . "" : '') . "
		 " . ($group_code != '' ? " and s.group_code ='" . $group_code . "'" : '') . "
		 " . ($payment_date_filter != '' ? " and date(pay.date_payment) ='" . $payment_date_filter . "'" : '') . "
		 " . ($from_date != '' && $to_date != '' ? " AND DATE(pay.date_payment) BETWEEN '" . $from_date . "' AND '" . $to_date . "'" : '') . "
		 group by s.id_scheme_account");
        $result = [];
        //  echo "<pre>";echo $this->db->last_query();exit;
        // Turn caching off for this one query
        $this->db->cache_off();
        $payment = $sql->result_array();
        // echo "<pre>";print_r($payment);exit;
        if ($sql->num_rows() > 0) {
            $sno = 1;
            foreach ($payment as $rcpt) {
                if ($rcpt['is_topup_scheme'] == 1) {
                    // Here we call a get due date function to get a finished slab percent
                    $currentStab = $this->payment_model->get_due_date("", date('Y-m-d'), $rcpt['id_scheme_account']);
                    $rcpt['current_slab_percent'] = $currentStab[0]['payable_value'];
                    // Here we call a get due date function to get a due from date for a current slab
                    $currentStab = $this->payment_model->get_due_date("custom", date('Y-m-d'), $rcpt['id_scheme_account']);
                    if ($currentStab[0]['due_date_from'] <= date('Y-m-d')) {
                        $rcpt['next_due_date'] = date('d-m-Y');
                    } else {
                        $rcpt['next_due_date'] = date('d-m-Y', strtotime($currentStab[0]['due_date_from']));
                    }
                } else {
                    $rcpt['current_slab_percent'] = '-';
                    $rcpt['next_due_date'] = '-';
                }
                $rcpt['sno'] = $sno;
                $rcpt['scheme_acc_number'] = $this->customer_model->format_accRcptNo('Account', $rcpt['id_scheme_account']);
                $return_data[$rcpt['scheme_name']][] = $rcpt;
                $result = $return_data;
                $sno++;
            }
        }
        /*$result['draw'] = $this->input->post('draw');
        $result['recordsTotal'] = $sql->num_rows();
        $result['recordsFiltered'] = $sql->num_rows();*/
        return $result;
    }
    /*	function scheme_summary_data()
        {
            $modifiedArray = [];  // Create an empty array to store modified values
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $id_scheme  = $this->input->post('id_scheme');
            $id_branch  = $this->input->post('id_branch');
            $company_settings = $this->session->userdata('company_settings');
            $id_company = $this->session->userdata('id_company');
            $branch = $this->session->userdata('id_branch');
            $singledatefilter  = $this->input->post('singlefilter');
            if ($singledatefilter != '') {
                $collectionamt = $this->get_collection_amt($singledatefilter);
                $oldclosedamt = $this->get_oldclosed_amt($singledatefilter);
                $newclosedamt = $this->get_newclosed_amt($singledatefilter);
            }
            if ($from_date != '' && $to_date != '') {
                $collectionamt = $this->get_collection_amt($singledatefilter = "");
                $oldclosedamt = $this->get_oldclosed_amt($singledatefilter = "");
                $newclosedamt = $this->get_newclosed_amt($singledatefilter = "");
            }
            $accounts = $this->db->query("SELECT sc.code,total_pay.date_payment,sc.id_metal, s.id_scheme_account,  sc.id_scheme,sc.scheme_name,COUNT(s.id_scheme_account)as scheme_count,sc.is_lucky_draw,SUM(total_pay.totalpay_amount) as paid_amounts ,
           SUM(total_pay.metal_weight) as oldmetal_weight,
           if(sc.scheme_type!=0 and (sc.flexible_sch_type is null or sc.flexible_sch_type!=1),SUM(IFNULL(total_pay.metal_weight,0)),0) as metal_weight,
           SUM((select IFNULL(IF(s.is_opening=1,IFNULL(s.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 AND sc.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=s.id_scheme_account group by pay.id_scheme_account)) as paid_installments,sc.id_classification,cls.classification_name
           FROM scheme_account s
           LEFT JOIN scheme sc ON sc.id_scheme=s.id_scheme
           left join sch_classify cls on cls.id_classification = sc.id_classification
           LEFT JOIN (
               SELECT p.id_scheme_account,date_payment
               ,SUM(IFNULL(payment_amount,0)) AS totalpay_amount,
               sum(IFNULL(metal_weight,0)) as metal_weight
               FROM payment p Where payment_status=1   " . ($singledatefilter != ''  ? " and date(p.date_payment) <= DATE_SUB('" . $singledatefilter . "',INTERVAL 1 DAY)" : '') . "
               GROUP BY id_scheme_account )
                AS total_pay ON total_pay.id_scheme_account = s.id_scheme_account
           Where s.scheme_acc_number IS NOT NULL and s.active=1 and s.is_closed=0
           " . ($id_branch != '' && $id_branch != 0 && $branch == 0 ? " and s.id_branch=" . $id_branch . "" : '') . "
           " . ($id_scheme != '' && $id_scheme != 0  ? " and s.id_scheme=" . $id_scheme . "" : '') . "
           " . ($id_company != '' && $id_company != 0 && $company_settings == 1 ? " and sc.id_company=" . $id_company . "" : '') . "
           GROUP BY s.id_scheme");
            //   print_r($this->db->last_query());exit;
            // 	$accounts=$this->db->query("SELECT sc.code,total_pay.date_payment,sc.id_metal, s.id_scheme_account,  sc.id_scheme,sc.scheme_name,COUNT(s.id_scheme_account)as scheme_count,sc.is_lucky_draw,SUM(total_pay.totalpay_amount) as paid_amount ,
            // 		SUM(total_pay.metal_weight) as metal_weight,
            // 		if(sc.scheme_type!=0 and sc.flexible_sch_type!=1,SUM(IFNULL(total_pay.metal_weight,0)),0) as metal_weight,
            // 		SUM((select IFNULL(IF(s.is_opening=1,IFNULL(s.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 AND sc.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=s.id_scheme_account group by pay.id_scheme_account)) as paid_installments,sc.id_classification,cls.classification_name
            // 		FROM scheme_account s
            // 		LEFT JOIN scheme sc ON sc.id_scheme=s.id_scheme
            // 		left join sch_classify cls on cls.id_classification = sc.id_classification
            // 		LEFT JOIN (
            // 			SELECT p.id_scheme_account,date_payment
            // 			,SUM(IFNULL(payment_amount,0)) AS totalpay_amount,
            // 			sum(IFNULL(metal_weight,0)) as metal_weight
            // 			FROM payment p Where payment_status=1
            // 			".($singledatefilter!=''  ? " and date(p.date_payment) <= DATE_SUB('".$singledatefilter."',INTERVAL 1 DAY)" :'')."
            //           GROUP BY id_scheme_account )
            // 			 AS total_pay ON total_pay.id_scheme_account = s.id_scheme_account
            // 		Where s.scheme_acc_number IS NOT NULL and s.active=1 and sc.active=1 and s.is_closed=0
            // 		".($id_branch!='' && $id_branch!=0 && $branch==0 ? " and s.id_branch=".$id_branch."":'')."
            // 		".($id_scheme!='' && $id_scheme!=0  ? " and s.id_scheme=".$id_scheme."":'')."
            // 		".($id_company!='' && $id_company!=0 && $company_settings==1 ? " and sc.id_company=".$id_company."":'')."
            // 		".($singledatefilter!=''  ? " and date(total_pay.date_payment) <= DATE_SUB('".$singledatefilter."',INTERVAL 1 DAY)" :'')."
            // 		GROUP BY s.id_scheme");
            $account_data = $accounts->result_array();
            // 		 print_r($this->db->last_query());exit;
            $modifiedArray = [];
            // 		print_r($closedamt);exit;
            foreach ($account_data as $key => $val) {
                $matchFoundCollectionAmt = false;
                $matchFoundClosedAmt = false;
                $matchFoundnewClosedAmt = false;
                foreach ($collectionamt as $collection) {
                    if ($val['id_scheme'] == $collection['id_scheme']) {
                        $val['collection_amount'] = $collection['collectionamt'];
                        $matchFoundCollectionAmt = true;
                        break;
                    }
                }
                foreach ($oldclosedamt as $ca) {
                    if ($val['id_scheme'] == $ca['id_scheme']) {
                        $val['oldclosed_amount'] = $ca['closed_amt'];
                        $matchFoundClosedAmt = true;
                        break;
                    }
                }
                foreach ($newclosedamt as $ca) {
                    if ($val['id_scheme'] == $ca['id_scheme']) {
                        $val['closed_amount'] = $ca['closed_amt'];
                        $matchFoundnewClosedAmt = true;
                        break;
                    }
                }
                if (!$matchFoundCollectionAmt) {
                    $val['collection_amount'] = NULL;
                }
                if (!$matchFoundClosedAmt) {
                    $val['oldclosed_amount'] = NULL;
                }
                if (!$matchFoundnewClosedAmt) {
                    $val['closed_amount'] = NULL;
                }
                // Calculate balance amount
                $paidAmount = floatval($val['paid_amounts']);
                $collectionAmount = floatval($val['collection_amount']);
                $closedAmount = floatval($val['oldclosed_amount']);
                $closedAmountnew = floatval($val['closed_amount']);
                // 		 print_r($paidAmount); echo '<br>'; exit;
                $val['paid_amount']  = abs($paidAmount -  $closedAmount);
                $balanceAmount = $val['paid_amount'] + $collectionAmount - $closedAmountnew;
                $val['balance_amount'] = $balanceAmount;
                $modifiedArray[] = $val;
            }
            // 		echo '<pre>';print_r($modifiedArray);exit;
            return $modifiedArray;
        }
        function get_oldclosed_amt($date = "")
        {
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $id_branch  = $this->input->post('id_branch');
            $company_settings = $this->session->userdata('company_settings');
            $id_company = $this->session->userdata('id_company');
            $branch = $this->session->userdata('id_branch');
            $singledatefilter  = $this->input->post('singlefilter');
            $collection_accounts = $this->db->query("SELECT SUM(p.payment_amount) as closed_amt,sc.id_scheme,sc.is_lucky_draw
            FROM scheme_account s
            LEFT JOIN scheme sc ON sc.id_scheme=s.id_scheme
            left join payment p on p.id_scheme_account=s.id_scheme_account
            Where s.scheme_acc_number IS NOT NULL and s.active=0   and s.is_closed=1
            " . ($id_branch != '' && $id_branch != 0 && $branch == 0 ? " and s.id_branch=" . $id_branch . "" : '') . "
            " . ($id_scheme != '' && $id_scheme != 0  ? " and s.id_scheme=" . $id_scheme . "" : '') . "
            " . ($id_company != '' && $id_company != 0 && $company_settings == 1 ? " and sc.id_company=" . $id_company . "" : '') . "
            " . ($date != '' ? " AND DATE(s.closing_date) <= DATE_SUB('" . $date . "', INTERVAL 1 DAY)" : '') . "
            " . ($from_date != '' && $to_date != '' ? " AND DATE(p.date_payment) BETWEEN '" . $from_date . "' AND '" . $to_date . "'" : '') . "
            GROUP BY s.id_scheme ");
            //print_r($this->db->last_query()) ;exit;
            return $collection_accounts->result_array();
        }
        function get_newclosed_amt($date = "")
        {
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $id_branch  = $this->input->post('id_branch');
            $company_settings = $this->session->userdata('company_settings');
            $id_company = $this->session->userdata('id_company');
            $branch = $this->session->userdata('id_branch');
            $singledatefilter  = $this->input->post('singlefilter');
            $collection_accounts = $this->db->query("SELECT SUM(p.payment_amount) as closed_amt,sc.id_scheme,sc.is_lucky_draw
            FROM scheme_account s
            LEFT JOIN scheme sc ON sc.id_scheme=s.id_scheme
            left join payment p on p.id_scheme_account=s.id_scheme_account
            Where s.scheme_acc_number IS NOT NULL and s.active=0   and s.is_closed=1
            " . ($id_branch != '' && $id_branch != 0 && $branch == 0 ? " and s.id_branch=" . $id_branch . "" : '') . "
            " . ($id_scheme != '' && $id_scheme != 0  ? " and s.id_scheme=" . $id_scheme . "" : '') . "
            " . ($id_company != '' && $id_company != 0 && $company_settings == 1 ? " and sc.id_company=" . $id_company . "" : '') . "
            " . ($date != '' ? " AND DATE(s.closing_date) = '" . $date . "'" : '') . "
            " . ($from_date != '' && $to_date != '' ? " AND DATE(p.date_payment) BETWEEN '" . $from_date . "' AND '" . $to_date . "'" : '') . "
            GROUP BY s.id_scheme ");
            //print_r($this->db->last_query()) ;exit;
            return $collection_accounts->result_array();
        }
        function get_collection_amt($date)
        {
            $from_date = $this->input->post('from_date');
            $to_date = $this->input->post('to_date');
            $id_branch  = $this->input->post('id_branch');
            $company_settings = $this->session->userdata('company_settings');
            $id_company = $this->session->userdata('id_company');
            $branch = $this->session->userdata('id_branch');
            $singledatefilter  = $this->input->post('singlefilter');
            $collection_accounts = $this->db->query("SELECT SUM(p.payment_amount) as collectionamt,sc.id_scheme,sc.is_lucky_draw
            FROM scheme_account s
            LEFT JOIN scheme sc ON sc.id_scheme=s.id_scheme
            left join payment p on p.id_scheme_account=s.id_scheme_account
            Where s.scheme_acc_number IS NOT NULL and s.active=1  and s.is_closed=0 and p.payment_status=1
            " . ($id_branch != '' && $id_branch != 0 && $branch == 0 ? " and s.id_branch=" . $id_branch . "" : '') . "
            " . ($id_scheme != '' && $id_scheme != 0  ? " and s.id_scheme=" . $id_scheme . "" : '') . "
            " . ($id_company != '' && $id_company != 0 && $company_settings == 1 ? " and sc.id_company=" . $id_company . "" : '') . "
            " . ($date != ''  ? " and date(p.date_payment) ='" . $date . "'" : '') . "
            " . ($from_date != '' && $to_date != '' ? " AND DATE(p.date_payment) BETWEEN '" . $from_date . "' AND '" . $to_date . "'" : '') . "
            GROUP BY s.id_scheme ");
            // print_r($this->db->last_query()) ;exit;
            return $collection_accounts->result_array();
        }*/
    function scheme_summary_data()
    {
        $modifiedArray = [];  // Create an empty array to store modified values
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        $id_scheme = $this->input->post('id_scheme');
        $id_branch = $this->input->post('id_branch');
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $branch = $this->session->userdata('id_branch');
        $singledatefilter = $this->input->post('singlefilter');
        /*if(!empty($singledatefilter)){
            $oldcoldatefilter = "and p.date_payment < '".$singledatefilter." 00:00:00'";
            $newcoldatefilter = "and p.date_payment >= '".$singledatefilter." 00:00:00' AND p.date_payment <= '".$singledatefilter." 23:59:59'";
            $oldclsdatefilter = "and sa.closing_date < '".$singledatefilter." 00:00:00'";
            $newclsdatefilter = "and sa.closing_date >= '".$singledatefilter." 00:00:00' AND sa.closing_date <= '".$singledatefilter." 23:59:59'";
            $accfilter = "and ((sa.is_closed = 1 and date(sa.closing_date) < '".$singledatefilter."') or sa.active = 1 )";
        }else if (!empty($from_date) && !empty($to_date)){
            $oldcoldatefilter = "and p.date_payment < '".$from_date." 00:00:00'";
            $newcoldatefilter = "and p.date_payment >= '".$from_date." 00:00:00' AND p.date_payment <= '".$to_date." 23:59:59'";
            $oldclsdatefilter = "and sa.closing_date < '".$from_date." 00:00:00'";
            $newclsdatefilter = "and sa.closing_date >= '".$from_date." 00:00:00' AND sa.closing_date <= '".$to_date." 23:59:59'";
            $accfilter = "and ((sa.is_closed = 1 and date(sa.closing_date) < '".$from_date."') or sa.active = 1 )";
        }*/
        if (!empty($singledatefilter)) {
            $oldcoldatefilter = "and date(p.date_payment) < '" . $singledatefilter . "'";
            $newcoldatefilter = "and date(p.date_payment)  between '" . $singledatefilter . "' AND '" . $singledatefilter . "'";
            $oldclsdatefilter = "and date(sa.closing_date) < '" . $singledatefilter . "'";
            $newclsdatefilter = "and date(sa.closing_date) between '" . $singledatefilter . "' AND  '" . $singledatefilter . "'";
            $accfilter = "and ((sa.is_closed = 1 and date(sa.closing_date) > '" . $singledatefilter . "') or sa.active = 1 )";
            $count_filter = $singledatefilter;
        } else if (!empty($from_date) && !empty($to_date)) {
            $oldcoldatefilter = "and date(p.date_payment)  < '" . $from_date . "'";
            $newcoldatefilter = "and date(p.date_payment)  between  '" . $from_date . "' AND  '" . $to_date . "'";
            $oldclsdatefilter = "and date(sa.closing_date) < '" . $from_date . "'";
            $newclsdatefilter = "and date(sa.closing_date) between '" . $from_date . "' AND  '" . $to_date . "'";
            $accfilter = "and(sa.active = 1 OR (sa.is_closed = 1 and date(sa.closing_date) > '" . $to_date . "') )";
            $count_filter = $to_date;
        } else {
            $oldcoldatefilter = '';
            $newcoldatefilter = '';
            $oldclsdatefilter = '';
            $newclsdatefilter = '';
            $accfilter = '';
            $count_filter = date('Y-m-d');
        }
        $branchfilter = (!empty($id_branch) ? " and sa.id_branch=" . $id_branch . "" : '');
        $schemefilter = (!empty($id_scheme) ? " and sa.id_scheme=" . $id_scheme . "" : '');
        $oldcollectionamt = $this->get_oldcollection_amt($oldcoldatefilter, $branchfilter, $schemefilter);
        $oldclosedamt = $this->get_oldclosed_amt($oldclsdatefilter, $branchfilter, $schemefilter);
        $newcollectionamt = $this->get_newcollection_amt($newcoldatefilter, $branchfilter, $schemefilter);
        $newclosedamt = $this->get_newclosed_amt($newclsdatefilter, $branchfilter, $schemefilter);
        //	print_r($newcollectionamt);exit;
        $sch_acc = $this->db->query("
	   		SELECT sa.id_scheme,count(*) as scheme_count FROM scheme_account sa 
			where 
			DATE(sa.start_date) <= '" . $count_filter . "' 
				AND ( 
					sa.is_closed = 0  OR (
						sa.is_closed = 1 AND date(sa.closing_date) > '" . $count_filter . "' 
					)
				) 
				$branchfilter  $schemefilter 
				GROUP BY sa.id_scheme
					")->result_array();
        $accounts = $this->db->query("SELECT s.code,s.id_metal,s.id_scheme,s.scheme_name,
		                            IFNULL(count(sa.id_scheme_account),0) as scheme_count
                            		FROM scheme s 
                            		right join scheme_account sa on sa.id_scheme = s.id_scheme
                            		left join sch_classify cls on cls.id_classification = s.id_classification
		                            where sa.id_scheme = s.id_scheme 
		                            -- and EXISTS (SELECT p.type FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account and p.payment_status = 1)
		                            $branchfilter  $schemefilter 
                                    -- $accfilter
                                    group by s.id_scheme");
        //print_r($this->db->last_query());exit;
        $account_data = $accounts->result_array();
        foreach ($account_data as $key => $val) {
            $matchFoundoldCollectionAmt = false;
            $matchFoundoldClosedAmt = false;
            $matchFoundnewCollectionAmt = false;
            $matchFoundnewClosedAmt = false;
            $matchFoundscheme_count = false;
            foreach ($sch_acc as $acc) {
                if ($val['id_scheme'] == $acc['id_scheme']) {
                    $val['scheme_count'] = $acc['scheme_count'];
                    $matchFoundscheme_count = true;
                    break;
                }
            }
            foreach ($oldcollectionamt as $oldcollection) {
                if ($val['id_scheme'] == $oldcollection['id_scheme']) {
                    $val['oldcollection_amount'] = $oldcollection['old_collection_amt'];
                    $val['oldcollection_weight'] = $oldcollection['old_collection_wgt'];
                    $val['oldcollection_acc'] = $oldcollection['old_collection_acc'];
                    $matchFoundoldCollectionAmt = true;
                    break;
                }
            }
            foreach ($oldclosedamt as $old_ca) {
                if ($val['id_scheme'] == $old_ca['id_scheme']) {
                    $val['oldclosed_amount'] = $old_ca['old_closed_amt'];
                    $val['oldclosed_weight'] = $old_ca['old_closed_wgt'];
                    $val['oldclosed_acc'] = $old_ca['old_closed_acc'];
                    $matchFoundoldClosedAmt = true;
                    break;
                }
            }
            foreach ($newcollectionamt as $newcollection) {
                if ($val['id_scheme'] == $newcollection['id_scheme']) {
                    $val['newcollection_amount'] = $newcollection['new_collection_amt'];
                    $val['newcollection_weight'] = $newcollection['new_collection_wgt'];
                    $val['newcollection_acc'] = $newcollection['new_collection_acc'];
                    $matchFoundnewCollectionAmt = true;
                    break;
                }
            }
            foreach ($newclosedamt as $new_ca) {
                if ($val['id_scheme'] == $new_ca['id_scheme']) {
                    $val['newclosed_amount'] = $new_ca['new_closed_amt'];
                    $val['newclosed_weight'] = $new_ca['new_closed_wgt'];
                    $val['newclosed_acc'] = $new_ca['new_closed_acc'];
                    $matchFoundnewClosedAmt = true;
                    break;
                }
            }
            if (!$matchFoundoldCollectionAmt) {
                $val['oldcollection_amount'] = 0;
                $val['oldcollection_weight'] = 0;
                $val['oldcollection_acc'] = 0;
            }
            if (!$matchFoundoldClosedAmt) {
                $val['oldclosed_amount'] = 0;
                $val['oldclosed_weight'] = 0;
                $val['oldclosed_acc'] = 0;
            }
            if (!$matchFoundnewCollectionAmt) {
                $val['newcollection_amount'] = 0;
                $val['newcollection_weight'] = 0;
                $val['newcollection_acc'] = 0;
            }
            if (!$matchFoundnewClosedAmt) {
                $val['newclosed_amount'] = 0;
                $val['newclosed_weight'] = 0;
                $val['newclosed_acc'] = 0;
            }
            if (!$matchFoundscheme_count) {
                $val['scheme_count'] = 0;
            }
            $result['code'] = $val['code'];
            //$result['scheme_count'] = ($val['oldcollection_acc'] - $val['oldclosed_acc'])  - $val['newclosed_acc'];
            $result['scheme_count'] = $val['scheme_count'];
            $result['opening_amount'] = $val['oldcollection_amount'] - $val['oldclosed_amount'];
            $result['opening_wgt'] = formatMetalWeight($val['oldcollection_weight'] - $val['oldclosed_weight']);
            $result['current_collection_amt'] = $val['newcollection_amount'];
            $result['current_closed_amt'] = $val['newclosed_amount'];
            $result['balance_amount'] = $result['opening_amount'] + $val['newcollection_amount'] - $val['newclosed_amount'];
            $result['balance_weight'] = formatMetalWeight(($val['oldcollection_weight'] - $val['oldclosed_weight']) + $val['newcollection_weight'] - $val['newclosed_weight']);
            $modifiedArray[] = $result;
        }
        // echo '<pre>';print_r($modifiedArray);exit;
        return $modifiedArray;
    }
    function get_oldcollection_amt($olddatefilter, $branchfilter, $schemefilter)
    {
        $oldcollection = $this->db->query("SELECT SUM(p.payment_amount) as old_collection_amt,SUM(p.metal_weight) as old_collection_wgt,s.id_scheme, count(Distinct sa.id_scheme_account) as old_collection_acc
                                    		from payment p
                                            join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
                                            left join scheme s on s.id_scheme = sa.id_scheme
                                    		Where p.payment_status = 1
                                    		" . $olddatefilter . " " . $branchfilter . " " . $schemefilter . "
                                    		AND DATE(p.date_payment) <= DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                                    		GROUP BY sa.id_scheme ");
        return $oldcollection->result_array();
    }
    function get_oldclosed_amt($olddatefilter, $branchfilter, $schemefilter)
    {
        $oldclosed = $this->db->query("SELECT SUM(p.payment_amount) as old_closed_amt,SUM(p.metal_weight) as old_closed_wgt,s.id_scheme, count(Distinct sa.id_scheme_account) as old_closed_acc
                                    		from payment p
                                            join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
                                            left join scheme s on s.id_scheme = sa.id_scheme
                                    		Where p.payment_status = 1 and sa.is_closed = 1
                                    		" . $olddatefilter . " " . $branchfilter . " " . $schemefilter . "
											AND DATE(sa.closing_date) <= DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                                    		GROUP BY sa.id_scheme ");
        return $oldclosed->result_array();
    }
    function get_newcollection_amt($newdatefilter, $branchfilter, $schemefilter)
    {
        $res = [];
        if (!empty($newdatefilter)) {
            $newcollection = $this->db->query("SELECT SUM(p.payment_amount) as new_collection_amt,SUM(p.metal_weight) as new_collection_wgt,s.id_scheme, count(Distinct sa.id_scheme_account) as new_collection_acc
                                        		from payment p
                                                 join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
                                                left join scheme s on s.id_scheme = sa.id_scheme
                                        		Where p.payment_status = 1
                                        		" . $newdatefilter . " " . $branchfilter . " " . $schemefilter . "
                                        		GROUP BY s.id_scheme ");
            $res = $newcollection->result_array();
        } else {
            $newcollection = $this->db->query("SELECT 
												SUM(p.payment_amount) AS new_collection_amt,
												SUM(p.metal_weight) AS new_collection_wgt,
												s.id_scheme, 
												COUNT(Distinct sa.id_scheme_account) AS new_collection_acc
											FROM payment p
											 JOIN scheme_account sa ON sa.id_scheme_account = p.id_scheme_account
											LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
											WHERE p.payment_status = 1
											AND DATE(p.date_payment) = CURDATE()"
                . $branchfilter . " " . $schemefilter . "
											GROUP BY s.id_scheme
											");
            $res = $newcollection->result_array();
        }
        return $res;
    }
    function get_newclosed_amt($newdatefilter, $branchfilter, $schemefilter)
    {
        $res = [];
        if (!empty($newdatefilter)) {
            $newclosed = $this->db->query("SELECT SUM(p.payment_amount) as new_closed_amt,SUM(p.metal_weight) as new_closed_wgt,s.id_scheme, count(Distinct sa.id_scheme_account) as new_closed_acc
                                        		from payment p
                                                 join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
                                                left join scheme s on s.id_scheme = sa.id_scheme
                                        		Where p.payment_status = 1 and sa.is_closed = 1
                                        		" . $newdatefilter . " " . $branchfilter . " " . $schemefilter . "
                                        		GROUP BY s.id_scheme ");
            // print_r($this->db->last_query());exit;
            $res = $newclosed->result_array();
        } else {
            $newclosed = $this->db->query("SELECT SUM(p.payment_amount) as new_closed_amt,SUM(p.metal_weight) as new_closed_wgt,s.id_scheme, count(sa.id_scheme_account) as new_closed_acc
                                        		from payment p
                                                 join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
                                                left join scheme s on s.id_scheme = sa.id_scheme
                                        		Where p.payment_status = 1 and sa.is_closed = 1
                                        		and date(sa.closing_date) = CURDATE()
												" . $branchfilter . " " . $schemefilter . "
                                        		GROUP BY s.id_scheme 
												");
            $res = $newclosed->result_array();
        }
        // print_r($this->db->last_query());exit;
        // print_r($this->db->last_query());exit;
        return $res;
    }
    function scheme_group_summary_data($id)
    {
        //$from_date= $this->input->post('from_date');
        //$to_date= $this->input->post('to_date');
        $id_branch = $this->input->post('id_branch');
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $branch = $this->session->userdata('id_branch');
        $group_code = $this->input->post('id_group');
        //$is_live=$this->input->post('is_live');
        $accounts = $this->db->query("SELECT sc.code,sc.id_metal,s.group_code,COUNT(s.group_code) as count,SUM(total_pay.totalpay_amount) as paid_amount,
		 SUM(total_pay.metal_weight) as metal_weight,
		 if(sc.scheme_type!=0 and sc.flexible_sch_type!=1,IFNULL(SUM(total_pay.metal_weight,0)),0) as metal_weight,
		 SUM((select IFNULL(IF(s.is_opening=1,IFNULL(s.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight or (sc.scheme_type=3 AND sc.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=s.id_scheme_account group by pay.id_scheme_account)) as paid_installments
		 FROM scheme_account s
		 LEFT JOIN scheme sc ON sc.id_scheme=s.id_scheme
		 LEFT JOIN (SELECT id_scheme_account,SUM(IFNULL(payment_amount,0)) AS totalpay_amount,SUM(IFNULL(metal_weight,0)) as metal_weight FROM payment Where payment_status=1 GROUP BY id_scheme_account ) AS total_pay ON total_pay.id_scheme_account = s.id_scheme_account
		 Where s.scheme_acc_number IS NOT NULL and s.active=1 and s.id_scheme= $id and sc.active=1 and s.group_code IS NOT NULL
		 " . ($id_branch != '' && $id_branch != 0 && $branch == 0 ? " and s.id_branch=" . $id_branch . "" : '') . "
		 " . ($id_company != '' && $id_company != 0 && $company_settings == 1 ? " and sc.id_company=" . $id_company . "" : '') . "
		 " . ($group_code != '' ? " and s.group_code ='" . $group_code . "'" : '') . "
		 GROUP BY s.group_code");
        //print_r($this->db->last_query());exit;
        $account = $accounts->result_array();
        foreach ($account as $r) {
            $return_data[$r['group_code']] = $r;
        }
        return $return_data;
    }
    function is_luckly_draw_scheme($id)
    {
        $accounts = $this->db->query("SELECT s.id_scheme, s.scheme_name, s.code,s.is_lucky_draw
			FROM scheme s where s.visible=1 and s.active=1 and s.id_scheme = $id");
        $account = $accounts->row_array();
        return $account;
    }
    function get_group_scheme_code($id)
    {
        $accounts = $this->db->query("SELECT sg.id_scheme_group,sg.group_code,sg.id_scheme FROM `scheme_group` sg WHERE sg.id_scheme = $id");
        $account = $accounts->result_array();
        return $account;
    }
    /*SCHEME WISE OUTSTANDING REPORT ENDS */
    function delete_gift_issued($id)
    {
        $this->db->where('id_scheme_account', $id);
        $status = $this->db->delete('gift_issued');
        return $status;
    }
    //DCNM-DGS chit report pdf,closing benefits...  Date Add:6/12/2022 Coded:AB
    function get_chit_data($id)
    {
        $sql = $this->db->query("SELECT s.is_digi,COUNT(p.id_payment) as pay_count,sa.id_scheme_account,date(sa.start_date) as join_date,sa.id_branch,s.scheme_name,s.id_scheme,s.restrict_payment,DATEDIFF(CURDATE(),date(sa.start_date)) as date_difference,
		s.total_days_to_pay, DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY) as allow_pay_till, CURDATE() as cur_date, DATE_FORMAT(NOW(),'%d-%m-%Y %H:%i:%s %a') as currentDate_time,
		sa.account_name,sa.scheme_acc_number,CONCAT(c.firstname,' ',IFNULL(c.lastname,'')) as customer_name,c.mobile,s.code as sch_code,b.name as branch_name,
		IFNULL(a.address1,'') as add1,IFNULL(a.address2,'') as add2,IFNULL(a.address3,'') as add3,IFNULL(a.pincode,'') as pincode,ct.name as city,st.name as state,ctry.name as country,s.id_scheme
				FROM scheme_account sa
				LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
				LEFT JOIN payment p ON (p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1)
				LEFT JOIN customer c ON (c.id_customer = sa.id_customer)
				LEFT JOIN branch b ON (b.id_branch = sa.id_branch)
				LEFT JOIN address a ON(a.id_customer = c.id_customer)
				LEFT JOIN city ct ON (ct.id_city = a.id_city)
				LEFT JOIN state st ON (st.id_state = a.id_state)
				LEFT JOIN country ctry ON (ctry.id_country = a.id_country)
				WHERE sa.id_scheme_account = " . $id . " AND sa.active = 1 AND sa.is_closed = 0 ");
        return $sql->row_array();
    }
    function get_chit_int($data)
    {
        $sql = $this->db->query("SELECT interest_type,interest_value, IF(interest_type = 0,'%','INR') as int_symbol
				FROM `scheme_benefit_deduct_settings`
				where id_scheme=" . $data['id_scheme'] . " " . ($data['restrict_payment'] == 1 ? "AND ( " . $data['date_difference'] . " BETWEEN installment_from AND installment_to)" : ""));
        //	print_r($data);exit;
        $res['int'] = $sql->row_array();
        $sql_debit = $this->db->query("SELECT deduction_type ,deduction_value,installment_to FROM `scheme_debit_settings`
	            where " . ($data['is_digi'] == 1 ? "(" . $data['date_difference'] . " BETWEEN installment_from AND installment_to)" : "(installment_from =" . $data['paid_installments'] . " or installment_to =" . $data['paid_installments'] . ")") . "
				and id_scheme=" . $data['id_scheme']);
        $debit = $sql_debit->row_array();
        if ($sql->num_rows > 0) {
            $sql_tot = $this->db->query("SELECT SUM(p.payment_amount) as total_paid,SUM(p.metal_weight) as saved_wgt, ROUND(SUM((p.metal_weight)*(" . $res['int']['interest_value'] . "/100)*((DATEDIFF(CURDATE(),date(p.date_payment)))/365)),3) as total_benefit,CURDATE() as cur_date, CONCAT(" . $res['int']['interest_value'] . ",' %') as interest,COUNT(id_payment) as pay_count,date(sa.start_date) as join_date,DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY) as allow_pay_till,DATEDIFF(CURDATE(),date(sa.start_date)) as date_difference
			FROM `payment` p
			LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
			LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
			WHERE sa.id_scheme_account = " . $data['id_scheme_account'] . " and p.payment_status = 1");
            //print_r($this->db->last_query());exit;
            $res['tot'] = $sql_tot->row_array();
        } else {
            $sql_tot = $this->db->query("SELECT SUM(p.payment_amount) as total_paid,SUM(p.metal_weight) as saved_wgt, '' as total_benefit,CURDATE() as cur_date, '' as interest,COUNT(id_payment) as pay_count,date(sa.start_date) as join_date,DATE_ADD(date(sa.start_date), INTERVAL s.total_days_to_pay DAY) as allow_pay_till,DATEDIFF(CURDATE(),date(sa.start_date)) as date_difference
			FROM `payment` p
			LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
			LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
			WHERE sa.id_scheme_account = " . $data['id_scheme_account'] . " and p.payment_status = 1");
            //print_r($this->db->last_query());exit;
            $res['tot'] = $sql_tot->row_array();
        }
        if ($sql_debit->num_rows > 0) {
            $sql_tot = $this->db->query("SELECT SUM(ROUND((p.metal_weight)*(" . $debit['deduction_value'] . "/100)* ((DATEDIFF(CURDATE(),date(p.date_payment)))/365) ,3)) as preclose_benefit,
             CONCAT(" . $debit['deduction_value'] . ",' %') as preclose_interest,
             CONCAT(date(sa.start_date),' to ',(DATE_ADD(date(sa.start_date), INTERVAL " . $debit['installment_to'] . " DAY))) AS preclose_date
			FROM `payment` p
			LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
			LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
			WHERE sa.id_scheme_account = " . $data['id_scheme_account'] . " and p.payment_status = 1");
            $res['preclose_interest'] = $sql_tot->row()->preclose_interest;
            $res['preclose_benefit'] = $sql_tot->row()->preclose_benefit;
            $res['preclose_date'] = $sql_tot->row()->preclose_date;
        } else {
            $sql_tot = $this->db->query("SELECT '' as preclose_interest, '' as preclose_benefit, '' as preclose_date
			FROM `payment` p
			LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
			LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
			WHERE sa.id_scheme_account = " . $data['id_scheme_account'] . " and p.payment_status = 1");
            $res['preclose_interest'] = $sql_tot->row()->preclose_interest;
            $res['preclose_benefit'] = $sql_tot->row()->preclose_benefit;
            $res['preclose_date'] = $sql_tot->row()->preclose_date;
        }
        return $res;
    }
    function chit_detail_report($data)
    {
        //RHR-new
        //$data = [];
        // print_r($data);exit;
        if ($data['benefit']['interest_value'] > 0) {
            if ($data['account']['calculation_type'] == 1) {   //for digi
                if (sizeof($data['int']) > 0) {
                    $sql_sub = $this->db->query("SELECT p.installment,@a:=@a+1 as sno,date(p.date_payment) as paid_date, p.payment_amount as paid_amt, p.metal_rate, p.metal_weight, (DATEDIFF(CURDATE(),date(p.date_payment))) as days_diff,
				ROUND((p.metal_weight)*(" . $data['int']['interest_value'] . "/100)*(DATEDIFF(CURDATE(),date(p.date_payment))/365),3) as pay_int,p.receipt_no
				FROM `payment` p
				join (SELECT @a:= 0) a
				LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
				WHERE sa.id_scheme_account = " . $data['account']['id_scheme_account'] . " and p.payment_status = 1");
                    $data = $sql_sub->result_array();
                } else {
                    $sql_sub = $this->db->query("SELECT @a:=@a+1 as sno,date(p.date_payment) as paid_date, p.payment_amount as paid_amt, p.metal_rate, p.metal_weight, (DATEDIFF(CURDATE(),date(p.date_payment))) as days_diff,'-' as pay_int,p.receipt_no  
					                            FROM `payment` p 
					                            join (SELECT @a:= 0) a 
					                            LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account) 
					                            WHERE sa.id_scheme_account = " . $data['account']['id_scheme_account'] . " and p.payment_status = 1");
                    $data = $sql_sub->result_array();
                }
            } else if ($data['account']['calculation_type'] == 2) {  //all scheme with fixed maturity
                $sql_sub = $this->db->query("SELECT p.installment,p.payment_amount as paid_amt, p.metal_rate, p.metal_weight, (DATEDIFF(sa.maturity_date,date(p.date_payment))) as days_diff,
										DATE_FORMAT(p.date_payment,'%d-%m-%Y') as paid_date,
										DATE_FORMAT(p.due_date,'%d-%m-%Y') as due_date,
										DATE_FORMAT(p.grace_date,'%d-%m-%Y') as grace_date,
				ROUND(((p.payment_amount)*(" . $data['benefit']['interest_value'] . "/100)*(DATEDIFF(date(sa.maturity_date),date(p.date_payment))/" . $data['account']['maturity_days'] . ")),2) as pay_int,
				p.receipt_no , p.due_type, if(p.is_limit_exceed = 0 ,'N0','YES') as is_limit_exceed
				FROM `payment` p
				join (SELECT @a:= 0) a
				LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
				WHERE sa.id_scheme_account = " . $data['account']['id_scheme_account'] . " and p.payment_status = 1
				order by p.installment asc");
                $data = $sql_sub->result_array();
            } else {  //common schemes
                $sql_sub = $this->db->query("SELECT p.installment,p.payment_amount as paid_amt, p.metal_rate, p.metal_weight, (DATEDIFF(sa.maturity_date,date(p.date_payment))) as days_diff,
										DATE_FORMAT(p.date_payment,'%d-%m-%Y') as paid_date,
										DATE_FORMAT(p.due_date,'%d-%m-%Y') as due_date,
										DATE_FORMAT(p.grace_date,'%d-%m-%Y') as grace_date,
				ROUND(p.payment_amount * (" . $data['benefit']['interest_value'] . "/100),2) as pay_int,
				p.receipt_no , p.due_type, if(p.is_limit_exceed = 0 ,'N0','YES') as is_limit_exceed
				FROM `payment` p
				join (SELECT @a:= 0) a
				LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
				WHERE sa.id_scheme_account = " . $data['account']['id_scheme_account'] . " and p.payment_status = 1
				order by p.installment asc");
                $data = $sql_sub->result_array();
            }
        }
        return $data;
    }
    function getBonusInsAmt($data)
    {
        //RHR
        $getAmt = '0.00';
        $ins_no = $this->db->query("SELECT installment_no FROM `scheme_benefit_deduct_settings`
		where  int_calc_on = 2 and id_scheme=" . $data['id_scheme'] . "  ")->row()->installment_no;
        $first_paid_date = $this->db->query("SELECT MIN(date(date_payment)) as first_paid_date FROM `payment`
		where  payment_status = 1 and id_scheme_account=" . $data['id_scheme_account'] . "   ")->row()->first_paid_date;
        if ($ins_no > 0) {
            $where = " and installment = " . $ins_no . " ";
        }
        /*	else {
                $where = "and date(date_payment) <= date_add('" . $first_paid_date . "',INTERVAL " . $ins_no . " MONTH) LIMIT 1";
            } */
        $getAmt = $this->db->query("SELECT IFNULL(payment_amount,'0.00') as pay_amt FROM payment
		where  payment_status = 1 and id_scheme_account=" . $data['id_scheme_account'] . " " . $where . " ")->row()->pay_amt;
        // 		 print_r($this->db->last_query());exit;
        return $getAmt;
    }
    function getAccBenefitDeduction($data)
    {
        //RHR
        if ($data['calculate_by'] == 1) { //digi
            $where = "and (" . $data['date_difference'] . " BETWEEN installment_from AND installment_to)";
        } else if ($data['calculate_by'] == 2) {  //by maturity
            $where = "and (" . $data['paid_installments'] . " BETWEEN installment_from AND installment_to)";
        } else {   //common
            $where = "and (installment_from =" . $data['paid_installments'] . " or installment_to =" . $data['paid_installments'] . ")";
        }
        $sql = $this->db->query("SELECT interest_type,interest_value,installment_from,installment_to FROM `scheme_benefit_deduct_settings`
					where  interest_by != 2 and int_calc_on = 1 and id_scheme=" . $data['id_scheme'] . " " . $where . " ");
        if ($sql->num_rows() > 0) {
            $data = $sql->row_array();
        } else {
            $data = array('interest_type' => '', 'interest_value' => '');
        }
        return $data;
    }
    function getAccBlcDebitSettings($data)
    {
        //RHR
        if ($data['calculate_by'] == 1) { //digi
            $where = "and (" . $data['date_difference'] . " BETWEEN installment_from AND installment_to)";
        } else if ($data['calculate_by'] == 2) {  //by maturity
            $where = "and (" . $data['paid_installments'] . " BETWEEN installment_from AND installment_to)";
        } else {   //common
            $where = "and (" . $data['paid_installments'] . " BETWEEN installment_from AND installment_to)";
        }
        $sql = $this->db->query("SELECT deduction_type ,deduction_value,installment_from,installment_to FROM `scheme_debit_settings`
				where  id_scheme=" . $data['id_scheme'] . " " . $where . " ");
        if ($sql->num_rows() > 0) {
            $data = $sql->row_array();
        } else {
            $data = array('deduction_type' => '', 'deduction_value' => '');
        }
        return $data;
    }
    function getPaymentData($data)
    {   //RHR
        //echo '<pre>';print_r($data);exit;
        $result = '0';
        $where = '';
        $ins_from = $data['installment_from'] - 1;
        $ins_to = abs($data['installment_to'] - 2);
        if ($data['interest_value'] != '') {
            if ($data['calculate_by'] == 1) {
                $formula = "ROUND(SUM((p.metal_weight)*(" . $data['interest_value'] . "/100)*((DATEDIFF(CURDATE(),date(p.date_payment)))/365)),3)";
            } else if ($data['calculate_by'] == 2) {
                $formula = "ROUND(SUM((p.payment_amount)*(" . $data['interest_value'] . "/100)*((DATEDIFF(date(sa.maturity_date),date(p.date_payment)))/" . $data['maturity_days'] . ")),2)";
            } else {
                $formula = "ROUND(SUM((p.payment_amount)*(" . $data['interest_value'] . "/100)))";
                $where = "group by date(p.date_payment) order by date(p.date_payment) ASC LIMIT $ins_from, $ins_to";
            }
            $res = $this->db->query("SELECT " . $formula . " as total_benefit_paymentwise
								FROM `payment` p
								LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
								WHERE sa.id_scheme_account = " . $data['id_scheme_account'] . " and p.payment_status = 1 " . $where);
            //print_r($this->db->last_query());exit;
            $result = $res->row()->total_benefit_paymentwise;
        } else if ($data['deduction_value'] != '') {
            if ($data['calculate_by'] == 1) {
                $formula = "ROUND(SUM((p.metal_weight)*(" . $data['deduction_value'] . "/100)*((DATEDIFF(CURDATE(),date(p.date_payment)))/365)),3)";
            } else if ($data['calculate_by'] == 2) {
                $formula = "ROUND((SUM((p.payment_amount)*(" . $data['deduction_value'] . "/100)*((DATEDIFF(date(sa.maturity_date),date(p.date_payment)))/" . $data['maturity_days'] . ")),2)";
            } else {
                $formula = "ROUND(SUM((p.payment_amount)*(" . $data['interest_value'] . "/100)))";
                $where = "group by date(p.date_payment) order by date(p.date_payment) ASC LIMIT $ins_from, $ins_to";
            }
            $res = $this->db->query("SELECT  " . $formula . " as benefit_deduction
								FROM `payment` p
								LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
								WHERE sa.id_scheme_account = " . $data['id_scheme_account'] . " and p.payment_status = 1" . $where);
            $result = $res->row()->benefit_deduction;
        }
        return $result;
    }
    //DGS-DCNM ends....
    /* ends */
    //voucher  block starts here
    function insert_gift_card($data)
    {
        $status = $this->db->insert('gift_card', $data);
        return $status;
    }
    function get_gift_card_byschid($id)
    {
        $gift_data = $this->db->query("SELECT * from gift_card where id_scheme_account=" . $id . " and status=0");
        return $gift_data->row_array();
    }
    function update_giftcard($data, $id)
    {
        $this->db->where("id_scheme_account =" . $id . " and status=0");
        //"id_payment = ".$id." AND is_active = 1"
        $status = $this->db->update('gift_card', $data);
        return $status;
    }
    function get_voucher_mode_detail($id)
    {
        $sql = "select sa.id_scheme_account,IFNULL(sum(pmd.payment_amount),0) as payment_amount,p.date_payment,p.payment_mode as payment_table_mode, pmd.payment_mode as mode_detail_mode
		from payment p
		left join payment_mode_details pmd on p.id_payment=pmd.id_payment
		left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
		where p.payment_status=1 and pmd.payment_status=1 and pmd.payment_mode='VCH' and pmd.is_active=1 and p.id_scheme_account=" . $id . " and sa.active=1";
        $voucher_data = $this->db->query($sql);
        return $voucher_data->row_array();
    }
    //voucher block ends here
    //function for getting data for group filter
    function get_group()
    {
        $sql = "select sa.group_code,sa.id_scheme from scheme_account sa left join scheme s on sa.id_scheme=s.id_scheme where s.active=1 and sa.group_code!='' and sa.group_code is not null
		group by sa.group_code";
        //print_r($sql);exit;
        $result = $this->db->query($sql);
        return $result->result_array();
    }
    function is_bonus_available($id_scheme)
    {
        $res = $this->db->query("SELECT installment_no as bonus_available FROM `scheme_benefit_deduct_settings` where id_scheme = " . $id_scheme . " and installment_no > 0")->row_array();
        if (sizeof($res) > 0) {
            $result = 1;
        } else {
            $result = 0;
        }
        return $result;
    }
    function get_generalAdv_BonusData($id_scheme_account, $value, $type, $calc_date_from, $calc_date_to)
    {
        //calculate based on type : 1 - amount , 0 - percent
        if ($type == 0) {
            $formula_amt = "ROUND(( (SUM(ap.payment_amount) / s.total_installments) * (" . $value . "/100) ),2) ";
            $formula_wgt = "ROUND(( (SUM(ap.metal_weight) / s.total_installments) * (" . $value . "/100) ),3) ";
        } else {
            $formula_amt = "ROUND((SUM(ap.payment_amount) + " . $value . "),2) ";
            $formula_wgt = "ROUND((SUM(ap.metal_weight) + " . $value . "),3) ";
        }
        $bonus = $this->db->query("SELECT SUM(ap.payment_amount) as tot_adv_amt, ROUND(SUM(ap.metal_weight),3) as tot_adv_wgt,
                                " . $formula_amt . " as adv_bonus , " . $formula_wgt . " as adv_bonus_wgt
                                FROM `general_advance_payment` ap
                                left join scheme_account sa on (sa.id_scheme_account = ap.id_scheme_account)
                                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                                WHERE ap.id_scheme_account = " . $id_scheme_account . "  and ap.payment_status = 1
                                and EXTRACT( YEAR_MONTH FROM date(ap.date_payment)) BETWEEN EXTRACT( YEAR_MONTH FROM '" . $calc_date_from . "') AND EXTRACT( YEAR_MONTH FROM '" . $calc_date_to . "')
                    ")->row_array();
        return $bonus;
    }
    function set_remarks_byid($scheme_id, $remark_data)
    {
        //print_r($scheme_id);
        $this->db->set('remark_open', $remark_data); //value that used to update column
        $this->db->where('id_scheme_account', $scheme_id); //which row want to upgrade
        $this->db->update('scheme_account');
        return $this->db->affected_rows();
    }
    // coded by Jothika on 16-10-2025 [disable payment block]
    function blk_payment_byid($scheme_id, $stop_pay, $reason)
    {
        $this->db->where('id_scheme_account', $scheme_id);
        $this->db->update(self::ACC_TABLE, array('disable_payment' => $stop_pay, 'disable_pay_reason' => $reason));
        return $this->db->affected_rows();
    }
    // Update scheme Account status---start
    function update_schacc_status($data)
    {
        foreach ($data as $row) {
            $this->db->set('active', $row['active']);
            $this->db->where('id_scheme_account', $row['id_scheme_account']);
            $status = $this->db->update('scheme_account');
        }
        return $status;
    }
    // Update scheme Account status---end
    function is_MCVA_purchaseDiscount_available($data)
    {
        $res = $this->db->query("SELECT interest_value as purchase_discount FROM `scheme_benefit_deduct_settings`
	                            where id_scheme = " . $data['id_scheme'] . " and interest_by = 2
	                            and (" . $data['paid_installments'] . " BETWEEN installment_from AND installment_to )
	                            ")->row()->purchase_discount;
        $result = ($res > 0 ? $res : null);
        return $result;
    }
    /*chit inv Gift module starts...*/
    //Adding scheme map for gift code starts
    function get_gift_account()
    {
        $sql = "SELECT gt.id_scheme_account 
		from gift_issued gt
		left join gift_mapping gm on gm.id_other_item=gt.id_gift
		left join ret_other_inventory_purchase_items ret on ret.inv_pur_itm_itemid=gt.id_gift
		where gt.id_scheme_account=" . $id;
        $result = $this->db->query($sql);
        return $result->result_array();
    }
    public function get_gift_issued_byaccount($id)
    {
        $qry = $this->db->query("SELECT gt.id_gift_issued,IFNULL(Date_format(gt.date_issued,'%d-%m%-%Y %r'),'-') as date_issued,CONCAT(e.firstname,'-',e.emp_code) as issued_employee,
		IFNULL(gt.gift_desc,'-') as gift_name,
		IFNULL((select count(id_scheme_account) from gift_issued where status = 1 and id_scheme_account=gt.id_scheme_account and gift_desc=gt.gift_desc group by id_gift),0) as gift_count,
		IFNULL(gt.gift_amount,0) as gift_value,
		gm.item_issue_limit as assigned_qty
		from gift_issued gt
		LEFT JOIN gift_mapping gm  on  (gm.id_other_item = gt.id_gift)
		left join employee e on (e.id_employee = gt.id_employee)
		where gt.type=1 and gt.status=1 and gt.id_scheme_account=" . $id . " 
		group by gt.id_gift_issued
		");
        $result['gifts'] = $qry->result_array();
        $result['paid_installments'] = $this->get_paid_installments($id);
        return $result;
    }
    public function get_paid_installments($id)
    {
        $qry = $this->db->query("SELECT sa.id_scheme_account,
	                            IFNULL((select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 AND s.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=sa.id_scheme_account group by pay.id_scheme_account),0) as paid_installments
                                FROM scheme_account sa 
                                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                                WHERE sa.id_scheme_account = " . $id . "
	                        ")->row()->paid_installments;
        return $qry;
    }
    public function get_gift_validation()
    {
        $id_scheme_account = $this->input->post('id_scheme_account');
        $ref_gift = $this->input->post('ref_gift');
        $id_gift = $this->input->post('id_gift');
        $id_scheme = $this->input->post('id_scheme');
        $login_branch = $this->input->post('id_branch');
        $sql = "SELECT 
		IFNULL((SELECT count(pur_item_detail_id) FROM ret_other_inventory_purchase_items_details where  other_invnetory_item_id = ret_details.other_invnetory_item_id AND status = 0 " . ($login_branch > 0 ? 'and current_branch=' . $login_branch : '') . "),0)  as available_item_from_stock,
		ret_details.status,
		ret_details.item_ref_no,
		item.id_other_item as id_gift,
		IFNULL(item.name,'-') as gift_name,
		IFNULL(ret.inv_pur_itm_qty,0) as total_quantity,
		IFNULL((select count(id_scheme_account) from gift_issued where status = 1 and id_scheme_account=" . $id_scheme_account . " and id_gift=item.id_other_item group by id_gift),0) as gift_count,
		IFNULL(ret.inv_pur_itm_rate,0) as oldgift_unit_price, 
		IFNULL(item.unit_price,0) as gift_unit_price, 
		IFNULL((select count(id_scheme_account)*ret.inv_pur_itm_rate from gift_issued where status = 1 and id_scheme_account=" . $id_scheme_account . " and id_gift=item.id_other_item group by id_gift),0) as tot_gift_value, 
		IFNULL((select sum(payment_amount) from payment where id_scheme_account=" . $id_scheme_account . " and payment_status=1),0) as payment_amount, 
		gm.id_scheme,
		gm.item_issue_limit 
		from ret_other_inventory_purchase_items_details ret_details
		left join ret_other_inventory_purchase_items ret on ret.inv_pur_itm_id=ret_details.inv_pur_itm_id 
		left join ret_other_inventory_item item on item.id_other_item=ret.inv_pur_itm_itemid
		left join gift_mapping gm on gm.id_other_item=item.id_other_item 
		where gm.id_scheme=" . $id_scheme . "  
		" . ($ref_gift != '' && $ref_gift > 0 ? 'and ret_details.item_ref_no="' . $ref_gift . '"' : '') . "
		" . ($id_gift != '' && $id_gift > 0 ? 'and ret_details.other_invnetory_item_id =' . $id_gift . ' and ret_details.status = 0' : '') . "
		" . ($login_branch > 0 ? 'and ret_details.current_branch=' . $login_branch : '') . "
		";
        //	print_r($sql);exit;
        $result = $this->db->query($sql)->row_array();
        $result['ref_gift'] = $ref_gift;
        return $result;
    }
    // function insertData($data, $table)
    // {
    // 	$status = $this->db->insert($table, $data);
    // 	return $status;
    // }
    //Adding scheme map for gift code ends
    function gifts_from_inv()
    {
        $id_scheme = $this->input->post('id_scheme');
        $id_scheme_account = $this->input->post('id_scheme_account');
        $login_branch = $this->session->userdata('id_branch');
        $id_branch = $this->input->post('id_branch');
        $res = [];
        $sql = "SELECT oi_pid.current_branch,gm.id_other_item,gm.item_issue_limit,IFNULL(gi.issued_qty,0) as issued_gift_qty,oi_item.name,sa.id_scheme,
                (select ROUND(sum(inv_pur_itm_qty),0) FROM ret_other_inventory_purchase_items WHERE inv_pur_itm_itemid = oi_item.id_other_item) as stock_created,
                count(oi_pid.pur_item_detail_id) as available_item_from_stock,
                IFNULL((select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 AND s.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=sa.id_scheme_account group by pay.id_scheme_account),0) as oldpaid_installments,
               ifnull( (select count(py.id_payment) from payment py where py.id_scheme_account = sa.id_scheme_account and py.payment_status = 1) ,0) as paid_count
                FROM scheme_account sa
                LEFT JOIN scheme s on (s.id_scheme = sa.id_scheme)
                left join `gift_mapping` gm on (gm.id_scheme = sa.id_scheme)
                LEFT JOIN ret_other_inventory_item as oi_item ON (oi_item.id_other_item = gm.id_other_item)
                LEFT JOIN ret_other_inventory_purchase_items_details as oi_pid ON (oi_pid.other_invnetory_item_id = oi_item.id_other_item AND oi_pid.status = 0)
                LEFT JOIN (SELECT id_gift,sum(quantity) as issued_qty FROM `gift_issued` where status = 1 and id_scheme_account = " . $id_scheme_account . " group by id_gift) as gi ON (gi.id_gift = gm.id_other_item)
                where  gm.id_scheme =  sa.id_scheme and gm.item_issue_limit > IFNULL(gi.issued_qty,0) and oi_pid.status = 0 
                and sa.id_scheme_account = " . $id_scheme_account . "
                group by oi_pid.current_branch,oi_pid.other_invnetory_item_id";
        //print_r($sql);exit;
        $result = $this->db->query($sql)->result_array();
        if (sizeof($result) > 0) {
            foreach ($result as $r) {
                if ($id_branch == $r['current_branch']) {
                    $res[] = $r;
                }
            }
            if (sizeof($res) > 0) {
                if ($res[0]['paid_count'] > 0) {
                    $response = array('msg' => 'true', 'gifts' => $res);
                } else {
                    $response = array('msg' => 'No success payments done...Unable to issue gifts..', 'gifts' => []);
                }
            } else {
                $response = array('msg' => 'No stock available in this branch', 'gifts' => $res);
            }
        } else {
            $response = array('msg' => 'No Gifts Available To Issue', 'gifts' => $res);
        }
        return $response;
    }
    function get_online_gift_report()
    {
        $scheme_id = $_POST['scheme'];
        $gift_id = $_POST['gift'];
        $to_date = $_POST['to_date'];
        $login_branch = $this->session->userdata('id_branch');
        $id_branch = ($_POST['id_branch'] > 0 ? $_POST['id_branch'] : $login_branch);
        $from_date = $_POST['from_date'];
        $report_type = $_POST['report_type'];
        $branchWiseLogin = $this->session->userdata('branchWiseLogin');
        $return_data = array();
        $sql = $this->db->query(" select  IFNULL((SELECT  concat(e.firstname,' ',IFNULL(e.lastname,''),'-',e.emp_code) from employee e where e.emp_code = sa.referal_code and sa.is_refferal_by = 1  ),'-') as referred_by,
        s.scheme_name,s.total_installments,sa.scheme_acc_number as scheme_acc_num,DATE_FORMAT(sa.start_date, '%d-%m-%Y %H:%i:%s') AS start_date,
        sa.id_scheme_account,c.mobile,  IFNULL(CONCAT(c.firstname,' ',c.lastname),'-') as cus_name,sa.account_name,CONCAT(s.code,'-',sa.scheme_acc_number) as scheme_acc_number ,s.code,s.scheme_name,
        IFNULL((select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 AND s.firstPayamt_as_payamt = 0), COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=sa.id_scheme_account group by pay.id_scheme_account),0) as paid_installments,
        oi_item.name as yet_to_issue_gift_name, 
        ifnull((gm.item_issue_limit - IFNULL(gi.issued_qty,0)),0) as yet_to_issue_gift_qty,
        ifnull(count(oi_pid.pur_item_detail_id),0) as available_qty_from_stock ,
        IFNULL(gi.issued_qty,0) as total_issued_gift_qty,gm.item_issue_limit as total_assigned_qty,sa.id_branch,b.name as branch_name,gm.id_other_item as id_gift,
        b.name as joined_branch_name
  FROM gift_mapping gm 
  LEFT JOIN ret_other_inventory_item as oi_item ON (oi_item.id_other_item = gm.id_other_item) 
  LEFT JOIN ret_other_inventory_purchase_items_details as oi_pid ON (oi_pid.other_invnetory_item_id = oi_item.id_other_item )
  LEFT JOIN scheme_account sa on (sa.id_scheme = gm.id_scheme)
  LEFT JOIN customer c  on (c.id_customer = sa.id_customer)
  LEFT JOIN scheme s on (s.id_scheme = sa.id_scheme)
  left join branch b on (b.id_branch = sa.id_branch)
  LEFT JOIN (SELECT id_scheme_account,id_gift,sum(quantity) as issued_qty FROM gift_issued  where status = 1 group by id_scheme_account,id_gift) as gi ON (gi.id_scheme_account = sa.id_scheme_account and gi.id_gift = gm.id_other_item)
  WHERE 
    sa.scheme_acc_number IS NOT NULL and sa.active = 1 and sa.is_closed = 0 
    AND s.id_scheme IN (gm.id_scheme) 
    AND gm.item_issue_limit > IFNULL(gi.issued_qty, 0)
" . ($branchWiseLogin == 1 ? ($id_branch != '' && $id_branch != 0 ? "  AND (sa.id_branch = " . $id_branch . ")" : '') : '') . "
" . ($scheme_id != 0 ? ($scheme_id != '' ? " AND (s.id_scheme = " . $scheme_id . " )" : '') : '') . "
" . ($gift_id != 0 ? ($gift_id != '' ? " AND (gm.id_other_item  = " . $gift_id . " )" : '') : '') . "
GROUP BY 
    sa.id_scheme_account, gm.id_other_item 
");
        // print_r($this->db->last_query());exit;
        $result = $sql->result_array();
        foreach ($result as $r) {
            if ($r['paid_installments'] > 0) {
                if ($report_type == 1) { //scheme wise
                    $return_data[$r['scheme_name']][] = $r;
                } elseif ($report_type == 2) { //area wise
                    $return_data[$r['yet_to_issue_gift_name']][] = $r;
                }
            }
        }
        return $return_data;
    }
    function giftname_list()
    {
        $sql = $this->db->query("SELECT gm.id_other_item as id_gift,oi_item.name as gift_name
        FROM gift_mapping gm
        LEFT JOIN ret_other_inventory_item as oi_item ON (oi_item.id_other_item = gm.id_other_item)
group by gm.id_other_item;");
        // print_r($this->db->last_query());exit;
        return $sql->result_array();
    }
    function get_assigned_gift_value($id_scheme)
    {
        $assigned_value = $this->db->query("SELECT IFNULL( sum( gm.item_issue_limit * item.unit_price ), 0) as assigned_value
                FROM `gift_mapping` gm
                left join ret_other_inventory_item item on (item.id_other_item = gm.id_other_item)
                where id_scheme = " . $id_scheme)->row()->assigned_value;
        // print_r($this->db->last_query());exit;
        return $assigned_value;
    }
    function get_giftValue($id_scheme_account)
    {
        $sql = $this->db->query("SELECT SUM(gift_amount) as gift_amount FROM `gift_issued` where id_scheme_account = " . $id_scheme_account . " and type = 1 and status = 1");
        return $sql->row()->gift_amount;
    }
    public function get_gift_issued_byID($id)
    {
        $qry = $this->db->query("SELECT * from gift_issued where id_gift_issued=" . $id . " 
		");
        return $qry->result_array();
    }
    public function getGiftByRef()
    {
        $ref = $_POST['ref_gift'];
        $qry = $this->db->query("SELECT other_invnetory_item_id  as id_gift FROM `ret_other_inventory_purchase_items_details`  where item_ref_no = '" . $ref . "'");
        return $qry->row()->id_gift;
    }
    function get_giftDebitSettings($data)
    {
        $sql = $this->db->query("SELECT deduction_by,deduct_in,deduction_type ,deduction_value FROM `scheme_debit_settings` where deduct_in = 1 and (installment_from = " . $data['paid_installments'] . " or installment_to = " . $data['paid_installments'] . ") and id_scheme=" . $data['id_scheme']);
        return $sql->row_array();
    }
    /*chit gift ends*/
    function updateAcc($id_sch_acc)
    {
        $sql = $this->db->query("UPDATE payment SET is_print_taken = 1 where id_scheme_account = " . $id_sch_acc);
        return true;
    }
    function getPurityName($purityId)
    {
        // this function used to get a purity only
        $this->db->select('purity');
        $this->db->from(' ret_purity');
        $this->db->where('id_purity', $purityId);
        $query = $this->db->get();
        $data = $query->row_array();
        // Company Name 
        $this->db->select('company_name');
        $this->db->from('company');
        $query = $this->db->get();
        $companyName = $query->row_array();
        $data = array_merge($data, $companyName);
        // print_r($data);exit;
        // print_r($query);exit;
        return $data;
    }
    function getMetalName($id_metal)
    {
        // print_r($id_metal);exit;
        $this->db->select('metal');
        $this->db->from('metal');
        $this->db->where('id_metal', $id_metal);
        $query = $this->db->get();
        return $query->row_array();
    }
    function paymentModeName($paymentShortCode)
    {
        $this->db->select('mode_name');
        $this->db->from('payment_mode');
        $this->db->where('short_code', $paymentShortCode);
        $query = $this->db->get();
        return $query->row_array();
    }
    function get_company_details()
    {
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $sql = "SELECT c.*,
                co.name as country_name,
                s.name as state_name,
                ci.name as city_name FROM company c
                JOIN country co ON
                    co.id_country = c.id_country
                JOIN state s on 
                    s.id_state = c.id_state
                join city ci on 
                    ci.id_city = c.id_city
                " . ($id_company != '' && $company_settings == 1 ? " WHERE id_company='" . $id_company . "'" : '') . "";
        // print_r($sql);exit;
        return $this->db->query($sql)->row_array();
    }
    function get_referer_bycode($code, $ref_by)
    {
        if ($ref_by == 0) {
            $sql = "select c.id_customer,c.mobile,c.firstname,w.id_wallet_account,w.wallet_acc_number from customer c 
            left join wallet_account w on (c.id_customer=w.id_customer and w.active=1)
            where c.mobile='" . $code . "'";
        } else if ($ref_by == 1) {
            $sql = "select e.id_employee,e.emp_code,e.firstname,w.id_wallet_account,w.wallet_acc_number from employee e 
            left join wallet_account w on (e.id_employee=w.idemployee and w.active=1)
            where e.emp_code='" . $code . "'";
        }
        //print_r($sql);exit;
        $result = $this->db->query($sql);
        return $result->row_array();
    }
    function getwalletdata($id_scheme_account)
    {
        $sql = $this->db->query("SELECT * FROM wallet_transaction where type=0 and id_sch_ac = " . $id_scheme_account);
        if ($sql->num_rows > 0) {
            return $sql->result_array();
        } else {
            return 0;
        }
    }

	function isDigiAcc($id){
		$sql = "SELECT sa.id_customer, s.id_metal from scheme_account sa 
			left join scheme s on s.id_scheme = sa.id_scheme
			where s.is_digi = 1 and sa.id_scheme_account = ? ";

		$res = $this->db->query($sql, $id);
		$row = $res->row_array();

		if(!empty($row)){
			$id_customer = $row['id_customer'];
			$id_metal = $row['id_metal'];
			$q = "select sa.id_customer, sa.id_scheme_account, CONCAT(c.firstname,' ',IFNULL(c.lastname,'')) as customer_name, c.mobile
				from scheme_account sa
				left join customer c on c.id_customer = sa.id_customer
				left join scheme s on s.id_scheme = sa.id_scheme
				where s.is_digi = 1 and s.id_metal = ? and sa.active = 1 and sa.id_customer = ? ";
			
			$res2 = $this->db->query($q, array($id_metal, $id_customer));
			return $res2->row_array();	
		}
		return false;
	}
    
}
