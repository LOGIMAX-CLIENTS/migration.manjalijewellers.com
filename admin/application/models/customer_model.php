<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Customer_model extends CI_Model
{
    const TABLE_NAME = "customer";
    const ADDRESS_TABLE = "address";
    function __construct()
    {
        parent::__construct();
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
    // Customer List chked&updtd emp login branchwise  data show//HH
// code by jothika on 14.7.2025 [getting data using mobile number]
	function get_all_customers($limit = "", $id_branch = '', $id_village = '', $mobile = '')
    {
        $branch_settings = $this->session->userdata('branch_settings');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $limit = "";
		$sql = "Select 
        c.id_customer,concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')) as name,
        c.mobile,
        ifnull(b.name,'-') as branch,
         SUM(IF(sa.active = 1 AND sa.is_closed = 0, 1, 0)) AS active_accounts,
        SUM(IF(sa.active = 0 AND sa.is_closed = 1, 1, 0)) AS closed_accounts,
        SUM(IF(sa.active = 0 AND sa.is_closed = 0, 1, 0)) AS inactive_accounts,
       if(c.active = 1, 'Active', 'Inactive') as cus_status,
       IFNULL(Date_Format(c.date_add, '%d-%m-%Y'),'-') as date_add,
       c.profile_complete,
       if(c.added_by = 0, 'WebApp', (if(c.added_by = 1, 'Admin', (if(c.added_by = 2, 'Mobile App', (if(c.added_by = 3, 'Collection App', (if(c.added_by = 4, 'Retail App', (if(c.added_by = 5, 'Sync', (if(c.added_by = 6, 'Import', '-'))))))))))))) as added_by,
       CONCAT(IFNULL(a.address1, '-'), ', ',IFNULL(a.address2, '-'), ', ',IFNULL(a.address3, '-')) AS address,
       ifnull(ct.name,'-') as city,ifnull(s.name,'-') as state,ifnull(cy.name,'-') as country,IFNULL(a.pincode,'-') as pincode,
        IFNULL(v.village_name,'') as village_name,
        IFNULL(Date_Format(c.date_of_birth,'%d-%m-%Y'),'-') AS date_of_birth,
        FLOOR(IFNULL(DATEDIFF(CURRENT_DATE, c.date_of_birth) / 365, '-')) AS age,
        IFNULL(Date_Format(c.date_of_wed,'%d-%m-%Y'),'-') AS date_of_wed,
		if(c.gender = 0,'MALE',if(c.gender = 1,'FEMALE',if(c.gender = 3,'Other','-'))) as gender,
        IFNULL(c.aadharid,'-') AS aadharid ,IFNULL(c.pan,'-') AS pan ,IFNULL(c.email,'-') AS email,
        IFNULL(c.religion,'') as religion,
        IFNULL(Date_format(c.custom_entry_date,'%d-%m-%Y'),'-') as custom_entry_date,
        IFNULL((SELECT CONCAT(em.firstname, ' ', IF(em.emp_code IS NOT NULL, em.emp_code, '')) 
            FROM employee em 
            WHERE em.id_employee = c.allocated_employee 
            LIMIT 1), 
            '-') AS employee_allocated,
            ifnull(CONCAT(ag.firstname,' ',IFNULL(ag.lastname,''),' - ',ag.agent_code),'-') as agent_name,
		   ifnull(c.nominee_name,'-') as nominee_name,
           ifnull(c.nominee_relationship, '-') as nominee_relationship,
           ifnull(c.nominee_mobile,'-') as nominee_mobile 
			From customer c
			left join address a on(c.id_customer=a.id_customer)
			left join agent ag ON (ag.id_agent = c.id_agent)
			left join country cy on (a.id_country=cy.id_country)
			left join state s on (a.id_state=s.id_state)
			left join city ct on (a.id_city=ct.id_city)
			left join village v on (v.id_village = c.id_village)
			left join kyc ky on (ky.id_customer=c.id_customer)
            left join scheme_account sa on (c.id_customer=sa.id_customer)
            left join branch b on b.id_branch = c.id_branch 
	   where 1 
	   " . ($uid != 1 && $uid != 2 ? ($branch_settings == 1 ? ($id_branch != '' && $id_branch != 0 ? "and c.id_branch=" . $id_branch . "" : ($branch != '' ? "and(c.id_branch=" . $branch . " or b.show_to_all=2)" : '')) : '') : ($id_branch != '' && $id_branch != 0 ? "and c.id_branch=" . $id_branch . "" : '')) . "
	    " . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . "
	  Group by c.id_customer";
	//   Order By c.id_customer Desc " . ($limit != NULL ? " LIMIT " . $limit : " ");
        // print_r($sql);exit;
        return $customers = $this->db->query($sql)->result_array();
    }
    function get_customers_by_date($from_date, $to_date, $id_branch)
    {
        $date_type = $this->input->post('date_type');
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $id_village = $this->input->post('id_village');
        $id_religion = $this->input->post('id_religion');
        $sql = "Select IFNULL(v.village_name,'') as village_name,CONCAT(ag.firstname,' ',IFNULL(ag.lastname,''),' - ',ag.agent_code) as agent_name,ky.id_customer as kyc,
		   c.id_customer,concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')) as name,c.date_of_birth,c.date_of_wed,c.added_by,
		   a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,
		   c.phone,c.mobile,c.email,c.nominee_name,c.nominee_relationship,c.nominee_mobile,IFNULL(Date_Format(c.date_of_birth,'%d-%m-%Y'),'-') AS date_of_birth,IFNULL(Date_Format(c.date_of_wed,'%d-%m-%Y'),'-') AS date_of_wed,
		   if(c.gender = 0,'MALE',if(c.gender = 1,'FEMALE',if(c.gender = 3,'Other',''))) as gender,
		   IFNULL(c.aadharid,'-') AS aadharid ,IFNULL(c.pan,'-') AS pan ,IFNULL(c.email,'-') AS email,IFNULL(a.pincode,'-') AS pincode,FLOOR(IFNULL(DATEDIFF(CURRENT_DATE, c.date_of_birth) / 365, '-')) AS age,
		   c.cus_img,c.pan,c.pan_proof,c.voterid,c.voterid_proof,c.rationcard,c.rationcard_proof,a.id_country,a.id_city,a.id_state,c.id_employee,
       count(sa.id_scheme_account) as accounts,
   	c.comments,c.username,c.passwd,c.is_new,c.active,c.profile_complete,c.`date_add`,c.`date_upd`,IFNULL(Date_format(c.custom_entry_date,'%d-%m%-%Y'),'') as custom_entry_date,
   IFNULL(c.religion,'') as religion,c.id_branch,IFNULL(
        (SELECT CONCAT(em.firstname, ' ', IF(em.emp_code IS NOT NULL, em.emp_code, '')) 
         FROM employee em 
         WHERE em.id_employee = c.allocated_employee 
         LIMIT 1), 
        '-') AS employee_allocated
			From
			  customer c
			left join address a on(c.id_customer=a.id_customer)
			left join agent ag ON (ag.id_agent = c.id_agent)
			left join country cy on (a.id_country=cy.id_country)
			left join state s on (a.id_state=s.id_state)
			left join city ct on (a.id_city=ct.id_city)
			left join village v on (v.id_village = c.id_village)
				left join kyc ky on (ky.id_customer=c.id_customer)
      left join scheme_account sa on (c.id_customer=sa.id_customer and sa.active=1 and sa.is_closed=0)
        Where (date(" . ($date_type != '' ? ($date_type == 2 ? "c.custom_entry_date" : "c.date_add") : "c.date_add") . ") BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "') " . ($id_branch != '' ? " and c.id_branch=" . $id_branch . "" : '') . "
        " . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . "
        " . ($id_village != '' && $id_village > 0 ? " and c.id_village='" . $id_village . "'" : '') . "
        " . ($id_religion != '' && $id_religion > 0 ? " and c.religion='" . $id_religion . "'" : '') . "
	  Group by c.id_customer";
        // print_r($sql);exit;
        return $customers = $this->db->query($sql)->result_array();
    }
    function ajax_get_unallocated_customers()
    {
        $sql = "select id_customer as id,concat(firstname,' ',if(lastname!=NULL,lastname,'')) as name,mobile
			    from  " . self::TABLE_NAME . "
				where  active=1
				and   id_customer not in (select id_customer from scheme_account sa where active=1 or is_closed=1)
				order by name";
        $customers = $this->db->query($sql);
        return $customers->result_array();
    }
    function ajax_get_all_customers()
    {
        $customers = $this->db->query("select c.id_customer as id,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,c.mobile,c.phone
		from customer c
		where c.active=1 ");
        return $customers->result_array();
    }
    function ajax_get_customers($param)
    {
        $customers = $this->db->query("select c.id_customer as value,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,c.mobile as location, if(c.cus_img = '','../../default_icon.png',CONCAT(c.cus_img,'.jpg')) as avator from customer c where c.active=1 and c.firstname LIKE '$param%' OR c.lastname LIKE '$param%' OR c.mobile LIKE '$param%'");
        return $customers->result_array();
    }
    function get_customer($id)
    {
        $customers = $this->db->query("Select
		   c.id_customer,c.firstname,c.lastname,c.date_of_birth,c.date_of_wed,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
		   a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,
		   c.phone,c.mobile,c.email,c.nominee_name,c.nominee_relationship,c.nominee_mobile,c.id_branch,
		   c.cus_img,c.pan,c.pan_proof,c.voterid,c.voterid_proof,c.rationcard,c.rationcard_proof,a.id_country,a.id_city,a.id_state,c.id_employee,
		   (Select count(id_scheme_account) as accounts from scheme_account where id_customer=1 and active=1 and is_closed=0) as accounts,
   	c.comments,c.username,c.passwd,c.is_new,c.active,c.`date_add`,c.`date_upd`
			From
			  customer c
			left join address a on(c.id_customer=a.id_customer)
			left join country cy on (a.id_country=cy.id_country)
			left join state s on (a.id_state=s.id_state)
			left join city ct on (a.id_city=ct.id_city)
			where c.active=1 and c.id_customer=" . $id);
        //print_r($this->db->last_query());exit;
        return $customers->row_array();
    }
    function get_cust($id)
    {
        $result = array();
        //field taken for kyc tab in customer master.... updatedOn : 21/06/2023 By: ABI
        $customers = $this->db->query("Select  c.languages_known,c.marital_status,c.title,c.aadharid,c.pan,c.driving_license_no,ifnull(c.pan_ImgName,'noimage') as pan_ImgName, ifnull(c.aadhar_ImgName,'noimage') as aadhar_ImgName,
		ifnull(c.dl_ImgName,'noimage') as dl_ImgName,ifnull(c.pp_ImgName,'noimage') as pp_ImgName,c.nominee_pan,
           c.aadharid,c.passport_no,
		   c.id_profession,c.id_customer,c.firstname,c.lastname,c.date_of_birth,c.date_of_wed,c.gst_number,a.company_name,c.id_branch,c.religion,
		   a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,c.id_village,v.post_office,v.taluk,
		   c.phone,c.mobile,c.email,c.nominee_name,c.nominee_relationship,c.nominee_mobile,c.is_cus_synced,
		   c.cus_img,c.pan,c.pan_proof,c.voterid,c.voterid_proof,c.rationcard,c.rationcard_proof,a.id_country,a.id_city,a.id_state,c.id_employee,c.gender,
   	       c.comments,c.username,c.passwd,c.is_new,c.active,c.`date_add`,c.`date_upd`,c.cus_type,c.fin_year_code,c.opening_balance_amount,c.nominee_address1, c.nominee_address2,
           c.is_vip
			From
			  customer c
			left join address a on(c.id_customer=a.id_customer)
			left join country cy on (a.id_country=cy.id_country)
			left join state s on (a.id_state=s.id_state)
			left join city ct on (a.id_city=ct.id_city)
			left join village v on (v.id_village=c.id_village)
			where c.id_customer='" . $id . "'");
            $result = $customers->row_array();
			$cus_kyc = $this->get_kyc_byid($id,2);
			if(sizeof($cus_kyc) > 0){
				$result['pan'] = $cus_kyc[0]['number'];
			}
        return $result;
    }
    function GetFinancialYear()
    {
        $sql = $this->db->query("SELECT fin_year_code,fin_status,fin_year_name From ret_financial_year");
        return $sql->result_array();
    }
    public function get_cus_address($id)
    {
        $this->db->select('id_country,id_state,id_city,address,address1,pincode');
        $this->db->where('id_customer', $id);
        $list_data = $this->db->get(self::ADDRESS_TABLE);
        return $list_data->row_array();
    }
    function empty_record()
    {
        $data = array(
            'id_customer' => NULL,
            'id_village' => NULL,
            'id_branch' => NULL,
            'lastname' => NULL,
            'firstname' => NULL,
            'date_of_birth' => NULL,
            'date_of_wed' => NULL,
            'id_employee' => 0,
            'address1' => NULL,
            'address2' => NULL,
            'address3' => NULL,
            'pincode' => NULL,
            'post_office' => NULL,
            'taluk' => NULL,
            'email' => NULL,
            'gender' => -1,
            'age' => NULL,
            'mobile' => NULL,
            'phone' => NULL,
            'nominee_name' => NULL,
            'nominee_relationship' => NULL,
            'nominee_mobile' => NULL,
            'pan' => NULL,
            'pan_proof' => NULL,
            'voterid' => NULL,
            'voterid_proof' => NULL,
            'rationcard' => NULL,
            'rationcard_proof' => NULL,
            'comments' => NULL,
            'username' => NULL,
            'passwd' => NULL,
            'gst_number' => null,
            'company_name' => null,
            'religion' => null,
            'active' => 1,
            'is_cus_synced' => 0,
            'cus_type' => 1,
            'aadharid' => null,
            'fin_year_code' => $this->GetFinancialYear(),
            'opening_balance_amount' => 0.00,
            'marital_status' => 0,
            'languages_known' => NULL,
        );
        $branch_data = $this->get_branch_details();
        return array_merge($data, $branch_data);
    }
    public function insert_customer($data)
    {
        $cus_id = 0;
        $insert_flag = 0;
        $cus_info = $this->db->insert(self::TABLE_NAME, $data['info']);
        if ($cus_info) {
            $cus_id = $this->db->insert_id();
            $data['address']['id_customer'] = $cus_id;
            $insert_flag = $this->db->insert(self::ADDRESS_TABLE, $data['address']);
            if ($insert_flag) {
                $id_address = $this->db->insert_id();
                $address = array('id_address' => $id_address);
                $this->db->where('id_customer', $cus_id);
                $this->db->update(self::TABLE_NAME, $address);
            }
        }
        return ($insert_flag == 1 ? $cus_id : 0);
    }
    //to insert imported customer
    public function insert_imported_customer($data)
    {
        $cus_id = 0;
        $insert_flag = 0;
        $insert_flag = $this->db->insert(self::TABLE_NAME, $data);
        $cus_id = $this->db->insert_id();
        /*if($cus_info)
        {
            $data['address']['id_customer']=$cus_id;
            $insert_flag=$this->db->insert(self::ADDRESS_TABLE,$data['address']);
        } */
        return ($insert_flag == 1 ? $cus_id : 0);
    }
    public function insert_customer_only($data)
    {
        $status = $this->db->insert(self::TABLE_NAME, $data);
        $id = $this->db->insert_id();
        return $id;
    }
    public function username_available($username)
    {
        $this->db->select('username');
        $this->db->where('username', $username);
        $user = $this->db->get(self::TABLE_NAME);
        return ($user->num_rows() > 0 ? TRUE : FALSE);
    }
    public function update_customer($data, $id)
    {
        $edit_flag = 0;
        $this->db->where('id_customer', $id);
        $cus_info = $this->db->update(self::TABLE_NAME, $data['info']);
        //	echo $this->db->last_query();exit;
        if ($id) {
            $qry = $this->db->query('select id_customer from address where id_customer=' . $id);
            if ($qry->num_rows() > 0) {
                $this->db->where('id_customer', $id);
                $edit_flag = $this->db->update(self::ADDRESS_TABLE, $data['address']);
            } else {
                $data['address']['id_customer'] = $id;
                $edit_flag = $this->db->insert(self::ADDRESS_TABLE, $data['address']);
            }
            //echo $this->db->last_query();exit;
        }
        return ($edit_flag == 1 ? $id : 0);
    }
    public function update_customer_only($data, $id)
    {
        $edit_flag = 0;
        $this->db->where('id_customer', $id);
        $cus_info = $this->db->update(self::TABLE_NAME, $data);
        return $cus_info;
    }
    public function update_images($id, $data)
    {
        $this->db->where('id_customer', $id);
        $cus = $this->db->update(self::TABLE_NAME, $data);
        return $cus;
    }
    function check_pay_records($id)
    {
        $sql = "select p.* from customer c
                LEFT JOIN scheme_account sa on (c.id_customer = sa.id_customer)
				LEFT JOIN payment p on (sa.id_scheme_account = p.id_scheme_account)
				where p.payment_status <> -1 AND c.id_customer = " . $id;
        $status = $this->db->query($sql);
        if ($status->num_rows() > 0) {
            return TRUE;
        }
    }
    function check_acc_records($id)
    {
        $sql = "select * from scheme_account where id_customer = " . $id;
        $status = $this->db->query($sql);
        if ($status->num_rows() > 0) {
            return TRUE;
        }
    }
    /*-- Coded by ARVK --*/
    function customer_count()
    {
        $sql = "SELECT id_customer FROM customer";
        return $this->db->query($sql)->num_rows();
    }
    /*-- / Coded by ARVK --*/
    public function delete_customer($id)
    {
        $this->db->where('id_customer', $id);
        $child = $this->db->delete(self::ADDRESS_TABLE);
        if ($child) {
            $this->db->where('id_customer', $id);
            $status = $this->db->delete(self::TABLE_NAME);
            $this->db->where('id_customer', $id);
            $wal_ac = $this->db->delete('wallet_account');
        }
        return $status;
    }

    public function check_customer_dependencies($id)
    {
        $dependencies = array();
        // Check Scheme Accounts
        $sql_acc = "SELECT id_scheme_account FROM scheme_account WHERE id_customer = " . $id . " AND active = 1 AND is_closed = 0";
        if ($this->db->query($sql_acc)->num_rows() > 0) {
            $dependencies[] = "Active Scheme Accounts";
        }
        // Check Bills
        $sql_bill = "SELECT bill_id FROM ret_billing WHERE bill_cus_id = " . $id . " AND bill_status <> -1";
        if ($this->db->query($sql_bill)->num_rows() > 0) {
            $dependencies[] = "Billing Records";
        }
        // Check Orders
        $sql_order = "SELECT id_customerorder FROM customerorder WHERE order_to = " . $id . " AND order_status <> -1";
        if ($this->db->query($sql_order)->num_rows() > 0) {
            $dependencies[] = "Customer Orders";
        }
        // Check Estimations
        $sql_esti = "SELECT estimation_id FROM ret_estimation WHERE cus_id = " . $id;
        if ($this->db->query($sql_esti)->num_rows() > 0) {
            $dependencies[] = "Estimation Records";
        }
        // Check Gift Voucher / Cards
        $sql_gift = "SELECT id_gift_card FROM gift_card WHERE purchased_by = " . $id;
        if ($this->db->query($sql_gift)->num_rows() > 0) {
            $dependencies[] = "Gift Voucher / Card Purchases";
        }
        return $dependencies;
    }
    public function getCustomerByMobile($mobile)
    {
        $this->db->select('id_customer');
        $this->db->where('mobile', $mobile);
        $customer = $this->db->get(self::TABLE_NAME);
        if ($customer->num_rows() == 1) {
            return $customer->row()->id_customer;
        } else {
            return 0;
        }
    }
    public function get_login_detail($id)
    {
        $cust = $this->db->query("Select c.firstname as name,c.passwd,c.mobile,c.email
			From
			  customer c
			where c.id_customer=" . $id);
        $loginData = $cust->row_array();
        $cus = array('name' => $loginData['name'], 'email' => $loginData['email'], 'passwd' => ($this->__decrypt($loginData['passwd'])), 'mobile' => $loginData['mobile']);
        return $cus;
    }
    public function mobile_available($mobile, $id_customer = "")
    {
        $id_company = $this->session->userdata('id_company');
        $company_settings = $this->session->userdata('company_settings');
        $this->db->select('mobile');
        $this->db->where('mobile', $mobile);
        if ($id_customer) {
            $this->db->where('id_customer <>', $id_customer);
        }
        if ($id_company != 0 && $company_settings == 1) {
            $this->db->where('id_company', $id_company);
        }
        $status = $this->db->get(self::TABLE_NAME);
        if ($status->num_rows() > 0) {
            return TRUE;
        }
    }
    public function email_available($email, $id_customer = "")
    {
        $id_company = $this->session->userdata('id_company');
        $company_settings = $this->session->userdata('company_settings');
        $this->db->select('email');
        $this->db->where('email', $email);
        if ($id_customer) {
            $this->db->where('id_customer <>', $id_customer);
        }
        if ($id_company != 0 && $company_settings == 1) {
            $this->db->where('id_company', $id_company);
        }
        $status = $this->db->get(self::TABLE_NAME);
        if ($status->num_rows() > 0) {
            return TRUE;
        }
    }
    function ajax_get_customers_list($param)
    {
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        $customers = $this->db->query("select c.id_customer, IFNULL(c.mobile,'')as mobile,c.firstname,c.lastname
				from  customer c
									where c.active=1
									" . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . "
									and (c.firstname LIKE '$param%'  OR c.lastname LIKE '$param%' OR c.mobile LIKE '$param%')");
        //print_r($this->db->last_query());exit;
        return $customers->result_array();
    }
    public function check_password($id_customer, $pswd)
    {
        $this->db->select('passwd');
        $this->db->where('id_customer', $id_customer);
        $this->db->where('passwd', $pswd);
        $status = $this->db->get(self::TABLE_NAME);
        if ($status->num_rows() > 0) {
            return TRUE;
        }
    }
    function get_customer_by_mobile($mobile)
    {
        $id_company = $this->session->userdata('id_company');
        $company_settings = $this->session->userdata('company_settings');
        $customers = "Select c.mobile,c.id_customer	From customer c where  c.mobile=" . $mobile . "" . ($id_company != 0 && $company_settings == 1 ? " and c.id_company=" . $id_company . "" : '') . "";
        $records = $this->db->query($customers);
        if ($records->num_rows() > 0) {
            return array('result' => true, 'cus' => $records->row_array());
        } else {
            return array('result' => true, 'cus' => []);
        }
    }
    function get_village()
    {
        $sql = "Select *From village";
        return $data = $this->db->query($sql)->row_array();
    }
    /**
     * Sync Customer accounts On Registration by Mobile Number
     * Scheme Accounts and payments
     */
    function getBranchCode($id_branch)
    {
        $sql = "Select short_name from branch where id_branch=" . $id_branch;
        $data = $this->db->query($sql);
        return $data->row()->short_name;
    }
    function get_entrydate($id)
    {
        $sql = "SELECT entry_date as custom_entry_date,cs.edit_custom_entry_date FROM ret_day_closing
		join chit_settings cs
		" . ($id != '' ? "where id_branch=" . $id . " " : '') . "";
        //print_r($sql);exit;
        return $this->db->query($sql)->row_array();
    }
    function get_customer_range($lower, $upper)
    {
        $accounts = $this->db->query("select
							  c.id_customer,concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,c.email,a.address1,a.address2,c.mobile
							from customer c
							left join address a on (a.id_customer=c.id_customer)
							where c.id_customer Between " . $lower . " and " . $upper . "
							");
        return $accounts->result_array();
    }
    public function getAddressId($id_customer)
    {
        $this->db->select('id_address');
        $this->db->where('id_customer', $id_customer);
        $customer = $this->db->get('address');
        if ($customer->num_rows() == 1) {
            return $customer->row()->id_address;
        } else {
            return 0;
        }
    }
    public function insert_address($data)
    {
        $status = $this->db->insert('address', $data);
        $id = $this->db->insert_id();
        return $id;
    }
    function Searchcustomer($SearchTxt)
    {
        $data = $this->db->query("SELECT c.id_customer as value, concat(c.firstname,'-',c.mobile) as label,c.id_village,v.village_name,if(c.is_vip=1,'Yes','No') as vip,c.firstname,c.lastname,
        a.id_country,a.id_city,a.id_state,c.date_of_birth,c.date_of_wed,c.added_by,a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,c.email,c.gender,
        c.send_promo_sms,c.religion
        FROM customer c
        left join address a on(c.id_customer=a.id_customer)
        left join village v on v.id_village=c.id_village
        left join country cy on (a.id_country=cy.id_country)
		left join state s on (a.id_state=s.id_state)
		left join city ct on (a.id_city=ct.id_city)
        WHERE username like '%" . $SearchTxt . "%' OR mobile like '%" . $SearchTxt . "%' OR firstname like '%" . $SearchTxt . "%'");
        return $data->result_array();
    }
    /*public function update_customer($data,$id)
    {
        $edit_flag=0;
        $this->db->where('id_customer',$id);
        $cus_info=$this->db->update(self::TABLE_NAME,$data['info']);
            $qry = $this->db->query('select id_customer from address where id_customer='.$id);
            if($qry->num_rows() > 0){
                $this->db->where('id_customer',$id);
                $edit_flag = $this->db->update(self::ADDRESS_TABLE,$data['address']);
            }
            else{
                $data['address']['id_customer']=$id;
                $edit_flag = $this->db->insert(self::ADDRESS_TABLE,$data['address']);
            }
            //echo $this->db->last_query();exit;
        return ($edit_flag==1?$id:0);
    }*/
    function getAllActiveAgents()
    {
        $sql = "Select id_agent,CONCAT(firstname,' ',IFNULL(lastname,''),' - ',agent_code) as agent_data,agent_code From agent where active = 1";
        return $data = $this->db->query($sql)->result_array();
    }
    public function allocate_agent($data, $id)
    {
        $cus_info = 0;
        $this->db->where('id_customer', $id);
        $cus_info = $this->db->update(self::TABLE_NAME, $data);
        return ($cus_info == 1 ? 1 : 0);
    }
    //manual import model functions......
    function isCustomerExist($mobile, $id_branch)
    {
        $sql = $this->db->query("SELECT id_customer FROM customer WHERE mobile=" . $mobile);
        if ($sql->num_rows() == 1) {
            return $sql->row('id_customer');
        } else {
            return 0;
        }
    }
    public function update_address($id, $data)
    {
        $this->db->where('id_customer', $id);
        $cus = $this->db->update('address', $data);
        return $cus;
    }
    function get_state_id($name)
    {
        $sql = $this->db->query("SELECT id_state FROM state WHERE name = '.$name.'");
        if ($sql->num_rows() == 1) {
            return $sql->row('id_state');
        } else {
            return 0;
        }
    }
    function get_city_id($name)
    {
        $sql = $this->db->query("SELECT id_city FROM city WHERE name = '.$name.'");
        if ($sql->num_rows() == 1) {
            return $sql->row('id_city');
        } else {
            return 0;
        }
    }
    function insExisAcByMobile($data)
    {
        $result = array();
        if ($data['branch_code'] != NULL && $data['branch_code'] != "") {
            $resultset = $this->db->query("select *,if(month(reg_date) = 1 || month(reg_date) = 2 || month(reg_date) = 3 , DATE_FORMAT(reg_date, '%y') -1 ,DATE_FORMAT(reg_date, '%y')) as start_year
										 from customer_reg where record_to=2 and is_registered_online=0 and is_closed=0 and branch_code='" . $data['branch_code'] . "' and mobile=" . $data['mobile']);
        } else if ($data['id_branch'] != '' || $data['id_branch'] != NULL) {
            $resultset = $this->db->query("select *,if(month(reg_date) = 1 || month(reg_date) = 2 || month(reg_date) = 3 , DATE_FORMAT(reg_date, '%y') -1 ,DATE_FORMAT(reg_date, '%y')) as start_year
											from customer_reg where record_to=2 and is_registered_online=0 and is_closed=0 and id_branch='" . $data['id_branch'] . "' and mobile=" . $data['mobile']);
        } else {
            $resultset = $this->db->query("select *,if(month(reg_date) = 1 || month(reg_date) = 2 || month(reg_date) = 3 , DATE_FORMAT(reg_date, '%y') -1 ,DATE_FORMAT(reg_date, '%y')) as start_year
											from customer_reg where record_to=2 and is_registered_online=0 and is_closed=0 and mobile=" . $data['mobile']);
        }
        if ($resultset->num_rows() > 0) {
            $records = array();
            foreach ($resultset->result() as $row) {
                $data['sync_scheme_code'] = $row->sync_scheme_code;
                $data['client_id'] = $row->clientid;
                $id_scheme = $this->getschId($data);
                $sql = $this->db->query("SELECT * FROM scheme_account WHERE ref_no='" . $row->clientid . "'");
                $existing_sch = $sql->row_array();
                if ($sql->num_rows() > 0) {
                    $data['id_sch_ac'] = $existing_sch['id_scheme_account'];
                    $result[] = $data;
                } else {
                    if ($id_scheme > 0) {
                        $records = array(
                            'id_customer' => $data['id_customer'],
                            'id_scheme' => $id_scheme,
                            'scheme_acc_number' => $row->scheme_ac_no,
                            'ref_no' => $row->clientid,
                            'account_name' => $row->account_name,
                            'group_code' => $row->group_code,
                            'start_date' => $row->reg_date,
                            'maturity_date' => $row->maturity_date,
                            'is_new' => $row->new_customer,
                            'firstPayment_amt' => $row->firstPayment_amt,
                            'firstpayment_wgt' => $row->firstpayment_wgt,
                            'fixed_rate_on' => $row->fixed_rate_on,
                            'fixed_metal_rate' => $row->fixed_metal_rate,
                            'fixed_wgt' => $row->fixed_wgt,
                            'id_branch' => ($row->id_branch > 0 ? $row->id_branch : $data['id_branch']),
                            'date_add' => date("Y-m-d H:i:s"),
                            'is_registered' => 1,
                            'active' => 1,
                            'added_by' => 6,   //import
                            'start_year' => $row->start_year
                        );
                        $status = $this->db->insert('scheme_account', $records);
                        //echo '$updateCus-true';exit;
                        if ($status) {
                            $data['id_sch_ac'] = $this->db->insert_id();
                            $result[] = $data;
                        }
                    }
                }
            }
        }
        return $result;
    }
    function getschId($data)
    {
        $branchwise_scheme = 0;
        $settings = $this->db->query("select branchwise_scheme from chit_settings");
        if ($settings->num_rows() > 0) {
            $branchwise_scheme = $settings->row()->branchwise_scheme;
        }
        if ($branchwise_scheme == 1 && ($data['id_branch'] != '' || $data['id_branch'] != NULL)) {
            $result = $this->db->query("SELECT s.id_scheme
                                       FROM `scheme` s
                                        LEFT JOIN scheme_branch sb ON sb.id_scheme = s.id_scheme
                                       WHERE sync_scheme_code='" . $data['sync_scheme_code'] . "' AND sb.id_branch='" . $data['id_branch'] . "'"
            );
        } else {
            $result = $this->db->query("select id_scheme from scheme where sync_scheme_code='" . $data['sync_scheme_code'] . "'");
        }
        if ($result->num_rows() > 0) {
            return $result->row()->id_scheme;
        } else {
            return null;
        }
    }
    function syncPayData($ac_data)
    {
        //echo "<pre>";print_r($ac_data);
        $succeedIds = array();
        $no_records = 0;
        foreach ($ac_data as $data) {
            //$resultset = $this->db->query("select * from transaction where is_transferred='N' and record_to=2 and client_id='".$data['client_id']."'");
            $resultset = $this->db->query("select *,if(month(payment_date) = 1 || month(payment_date) = 2 || month(payment_date) = 3 , DATE_FORMAT(payment_date, '%y') -1 ,DATE_FORMAT(payment_date, '%y')) as receipt_year
			                                from transaction
			                                where is_transferred='N' and record_to=2 and client_id='" . $data['client_id'] . "'");
            $i = 1;
            if ($resultset->num_rows() > 0) {
                $records = array();
                foreach ($resultset->result() as $row) {
                    if ($row->is_modified == 0) {
                        $records = array(
                            'id_scheme_account' => $data['id_sch_ac'],
                            'metal_rate' => $row->rate,
                            'receipt_no' => $row->receipt_no,
                            'metal_weight' => $row->weight,
                            'payment_amount' => $row->amount,
                            'payment_ref_number' => $row->ref_no,
                            'actual_trans_amt' => $row->amount,
                            'date_payment' => $row->payment_date,
                            'date_add' => $row->payment_date,
                            'id_branch' => ($row->id_branch > 0 ? $row->id_branch : $data['id_branch']),
                            'custom_entry_date' => $row->custom_entry_date,
                            'payment_status' => 1,
                            'payment_mode' => $row->payment_mode,
                            'payment_status' => $row->payment_status,
                            //	'dues' =>$row->NO_OF_INSTAL,
                            'payment_type' => 'Offline',
                            'is_offline' => 1,
                            'due_type' => $row->due_type,
                            'due_month' => $row->due_month,
                            'due_year' => $row->due_year,
                            'added_by' => 6, //import
                            'installment' => $row->installment_no,
                            'discountAmt' => (!empty($row->discountAmt) ? $row->discountAmt : 0.00),
                            'receipt_year' => $row->receipt_year
                        );
                        $status = $this->db->insert('payment', $records);
                    } elseif ($row->is_modified == 1) {
                        //update if offline record is with cancelled status
                        $upd_array = array(
                            "payment_status" => 4,
                            'receipt_no' => $row->receipt_no,
                            "remark" => $row->remarks,
                            "date_upd" => date('Y-m-d H:i:s'),
                            "payment_ref_number" => $row->ref_no
                        );
                        $status = $this->db->insert('payment', $upd_array);
                    }
                    /*echo $this->db->_error_message();
                   echo $this->db->last_query();exit; */
                    if ($status) {
                        $succeedIds[] = $row->id_transaction;
                    }
                    $i++;
                }
            } else {
                $no_records += 1;
                // There wont be any reords for plan 2 & 3 schemes so by default set valuse as 1.
            }
        }
        return array('succeedIds' => $succeedIds, 'no_records' => $no_records);
    }
    function updateInterTableStatus($data, $payData)
    {
        $transtatus = false;
        foreach ($data as $accdata) {
            $arrdata = array("id_scheme_account" => $accdata['id_sch_ac'], "is_registered_online" => 1, "is_modified" => 0, "transfer_date" => date('Y-m-d H:i:s'), 'is_transferred' => 'Y');
            $this->db->where('clientid', $accdata['client_id']);
            //$this->db->where('id_branch',$accdata['id_branch']);
            $accstatus = $this->db->update('customer_reg', $arrdata);
            if ($accstatus) {
                foreach ($payData as $data) {
                    $upddata = array("is_modified" => 0, "transfer_date" => date('Y-m-d H:i:s'), 'is_transferred' => 'Y');
                    $this->db->where('id_transaction', $data);
                    $transtatus = $this->db->update('transaction', $upddata);
                }
            }
        }
        return $transtatus;
    }
    //manual import model functions ends......
    //Search BY Acc.no
    function ajax_get_scheme_account_list($scheme_acc_number = "", $is_old_sch_acc_no = '')
    {
        $branch_settings = $this->session->userdata('branch_settings');
        $branch = $this->session->userdata('id_branch');
        $uid = $this->session->userdata('uid');
        $company_settings = $this->session->userdata('company_settings');
        $id_company = $this->session->userdata('id_company');
        if (strlen($scheme_acc_number) >= 1 && empty($is_old_sch_acc_no)  ) {
            $searchSchemeAcc = "sa.scheme_acc_number = '" . $scheme_acc_number . "'";
        }else if (strlen($scheme_acc_number) >= 1 && !empty($is_old_sch_acc_no)  ) {
             $searchSchemeAcc = "sa.old_scheme_acc_number LIKE '" . $scheme_acc_number . "%'";
        }
        else {
            $searchSchemeAcc = "sa.scheme_acc_number LIKE '" . $scheme_acc_number . "%'";
        }
       
        $sql = $this->db->query("SELECT concat(c.firstname,' ',IFNULL (c.lastname,'')) as cus_name,sa.account_name as name,c.id_customer,c.mobile,s.code  as scheme_code, sa.id_scheme_account ,if(cs.has_lucky_draw=1 && s.is_lucky_draw = 1,concat(IFNULL(sa.group_code,s.code),'-',sa.scheme_acc_number),concat(s.code,'-',sa.scheme_acc_number))as oldscheme_acc_number,sa.old_scheme_acc_number
		FROM `scheme_account` sa
		LEFT JOIN scheme s ON  s.id_scheme= sa.id_scheme
		LEFT JOIN  customer c   ON  c.id_customer= sa.id_customer
		left join branch b on (b.id_branch = sa.id_branch)
		JOIN chit_settings cs
		WHERE
		sa.active = 1 and sa.is_closed=0
		" . ($id_company != '' && $company_settings == 1 ? " and c.id_company='" . $id_company . "'" : '') . "
		and " . $searchSchemeAcc . " " . ($uid != 1 && $uid != 2 ? ($branch_settings == 1 ? ($id_branch != '' && $id_branch != 0 ? "and sa.id_branch=" . $id_branch . "" : ($branch != '' ? "and(sa.id_branch=" . $branch . " or b.show_to_all=2)" : '')) : '') : ($id_branch != '' && $id_branch != 0 ? "and sa.id_branch=" . $id_branch . "" : '')) . "
        Order by sa.scheme_acc_number ASC");
        // print_r($this->db->last_query());exit;
        $result = [];
        $customer = $sql->result_array();
        if ($sql->num_rows() > 0) {
            foreach ($customer as $rcpt) {
                $rcpt['scheme_acc_number'] = $this->format_accRcptNo('Account', $rcpt['id_scheme_account']);
                //$rcpt['receipt_no'] = $this->customer_model->format_accRcptNo('Receipt',$rcpt['id_payment']);
                $result[] = $rcpt;
            }
        }
        return $result;
    }
    /* account number and receipt number generating functions based on display format settings.... this is called where ever the scheme_acc_number and receipt_no displayed...
          Display format settings....
          0 - Only Account Number
          1 - Based on Acc No Generating Settings
          2 - Customized
          Select options for customized display settings
          (1) Branch Code       - BrCode
          (2) Scheme Code       - SchCode
          (3) Group Code        - GrpCode
          (4) Financial Year    - FinYr
          (5) Scheme Account No - AccNo
          (6) Receipt No        - RcptNo
          schemeacc_no_set : 0- automatic, 1- manual, 2- Sync Tool, 3- Auto Sync
          receipt_no_set : 0- manual, 1- Automatic, 2- sync Tool, 3- Auto Sync
    Date Added : 18/06/2023  Added By : AB*/
    function format_accRcptNo($type, $id)
    {
        /* Format account and receipt no based on settings
            Type : Account (for account num format), Receipt  (for receipt num format)
         */
        $set = $this->get_AccRcpt_DisplaySettings();      //get the settings
        switch ($type) {
            case 'Account':
                $accFrmt = $this->accountFrmt($set, $id);
                return $accFrmt;
                break;
            case 'Receipt':
                $rcptFrmt = $this->receiptFrmt($set, $id);
                return $rcptFrmt;
                break;
        }
    }
    /*function accountFrmt($set,$id){
        $acc = $this->get_acc_Data($id);      //get account data needed
        if($set['schemeacc_no_set'] == 0 && $set['schemeaccNo_displayFrmt'] != 0){
             if($set['schemeaccNo_displayFrmt'] == 1){
                 if($set['scheme_wise_acc_no'] == 0){   //Common
                     $accFrmt = $acc['sch_AccNo'];
                 }else if($set['scheme_wise_acc_no'] == 1){  //Common with branch wise
                    $accFrmt = $acc['branch_code'].'-'.$acc['sch_AccNo'];
                 }else if($set['scheme_wise_acc_no'] == 2){   //Scheme Wise
                     $accFrmt = $acc['scheme_code'].'-'.$acc['sch_AccNo'];
                 }else if($set['scheme_wise_acc_no'] == 3){  //Scheme wise With Branch Wise
                     $accFrmt = $acc['scheme_code'].$acc['branch_code'].'-'.$acc['sch_AccNo'];
                 }else if($set['scheme_wise_acc_no'] == 4){   //Financial Year Wise
                     $accFrmt = $acc['start_year'].'-'.$acc['sch_AccNo'];
                 }else if($set['scheme_wise_acc_no'] == 5){   //Financial Year with Scheme Wise
                     $accFrmt = $acc['start_year'].$acc['scheme_code'].'-'.$acc['sch_AccNo'];
                 }else if($set['scheme_wise_acc_no'] == 6){   //Financial Year with Scheme & Branch Wise
                     $accFrmt = $acc['start_year'].$acc['scheme_code'].$acc['branch_code'].'-'.$acc['sch_AccNo'];
                 }
             }else if($set['schemeaccNo_displayFrmt'] == 2){
                 $accFrmt = 'customer account format';
             }
        }else{
            $accFrmt =  $acc['sch_AccNo'];
        }
     return $accFrmt;
    }*/
    //function accountFrmt replaced by Durga 26-06-2023
    function accountFrmt($set, $id)
    {
        $acc = $this->get_acc_Data($id);      //get account data needed
        if( $this->config->item('sch_AccNo_padding') > 0){
            $acc['sch_AccNo'] = str_pad($acc['sch_AccNo'], $this->config->item('sch_AccNo_padding'), '0', STR_PAD_LEFT);
        }
        
        if ($acc['is_lucky_draw'] == 1) {
            $accFrmt = $acc['group_code'] . '-' . $acc['sch_AccNo'];
        } else {
            if ($set['schemeacc_no_set'] == 0 && $set['schemeaccNo_displayFrmt'] != 0) {
                if ($set['schemeaccNo_displayFrmt'] == 1) {
                    if ($set['scheme_wise_acc_no'] == 0) {   //Common
                        $accFrmt = $acc['sch_AccNo'];
                    } else if ($set['scheme_wise_acc_no'] == 1) {  //Common with branch wise
                        $accFrmt = $acc['branch_code'] . '-' . $acc['sch_AccNo'];
                    } else if ($set['scheme_wise_acc_no'] == 2) {   //Scheme Wise
                        $accFrmt = $acc['scheme_code'] . '-' . $acc['sch_AccNo'];
                    } else if ($set['scheme_wise_acc_no'] == 3) {  //Scheme wise With Branch Wise
                        $accFrmt = $acc['scheme_code'] . $acc['branch_code'] . '-' . $acc['sch_AccNo'];
                    } else if ($set['scheme_wise_acc_no'] == 4) {   //Financial Year Wise
                        $accFrmt = $acc['start_year'] . '-' . $acc['sch_AccNo'];
                    } else if ($set['scheme_wise_acc_no'] == 5) {   //Financial Year with Scheme Wise
                        $accFrmt = $acc['start_year'] . $acc['scheme_code'] . '-' . $acc['sch_AccNo'];
                    } else if ($set['scheme_wise_acc_no'] == 6) {   //Financial Year with Scheme & Branch Wise
                        $accFrmt = $acc['start_year'] . $acc['scheme_code'] . $acc['branch_code'] . '-' . $acc['sch_AccNo'];
                    }
                } else if ($set['schemeaccNo_displayFrmt'] == 2) {
                    $acc_frmt_fromdb = $this->getFormatFromDB();
                    $acc = $this->get_acc_Data($id);
                    $frmt_short_code = [];
                    if ($acc_frmt_fromdb['custom_AccDisplayFrmt'] != '' && $acc_frmt_fromdb['custom_AccDisplayFrmt'] != null) {
                        $field_name = explode('@@', $acc_frmt_fromdb['custom_AccDisplayFrmt']);
                        for ($i = 1; $i < count($field_name); $i += 2) {
                            $frmt_short_code[] = $field_name[$i];
                        }
                        $accFrmt = $this->getFormatedNumber($frmt_short_code, $acc);
                    } else {
                        $accFrmt = $acc['sch_AccNo'];
                    }
                }
            } else {
                $accFrmt = $acc['sch_AccNo'];
            }
        }
        return $accFrmt;
    }
    /*function receiptFrmt($set,$id){
        $rcpt = $this->get_receipt_Data($id);      //get receipt data needed
        if($rcpt['receipt_no'] == '-'){
            $rcptFrmt =  $rcpt['receipt_no'];
        }else{
        if($set['receipt_no_set'] == 1 && $set['receiptNo_displayFrmt'] != 0){
         if($set['receiptNo_displayFrmt'] == 1){
             if($set['scheme_wise_receipt'] == 1){   //Common
                 $rcptFrmt = $rcpt['receipt_no'];
             }else if($set['scheme_wise_receipt'] == 2){  //Branch-wise
                 $rcptFrmt = $rcpt['branch_code'].'-'.$rcpt['receipt_no'];
             }else if($set['scheme_wise_receipt'] == 3){   //scheme-wise
                 $rcptFrmt = $rcpt['scheme_code'].'-'.$rcpt['receipt_no'];
             }else if($set['scheme_wise_receipt'] == 4){  //scheme with Branch-wise
                 $rcptFrmt = $rcpt['scheme_code'].$rcpt['branch_code'].'-'.$rcpt['receipt_no'];
             }else if($set['scheme_wise_receipt'] == 5){   // Financial Year-wise
                 $rcptFrmt = $rcpt['receipt_year'].'-'.$rcpt['receipt_no'];
             }else if($set['scheme_wise_receipt'] == 6){   //Financial Year with Scheme & Branch wise
                 $rcptFrmt = $rcpt['receipt_year'].$rcpt['scheme_code'].$rcpt['branch_code'].'-'.$rcpt['receipt_no'];
             }
         }else if($set['receiptNo_displayFrmt'] == 2){
             $rcptFrmt = 'custom receipt format';
         }
         }else{
             $rcptFrmt =  $rcpt['receipt_no'];
         }
        }
       return $rcptFrmt;
    }*/
    //function receiptFrmt replaced by Durga 26-06-2023
    function receiptFrmt($set, $id)
    {
        $rcpt = $this->get_receipt_Data($id);      //get receipt data needed
        if( $this->config->item('receipt_no_padding') > 0){
            $rcpt['receipt_no'] = str_pad($rcpt['receipt_no'], $this->config->item('receipt_no_padding'), '0', STR_PAD_LEFT);
        }    
        if ($rcpt['receipt_no'] == '-') {
            $rcptFrmt = $rcpt['receipt_no'];
        } else {
            if ($set['receipt_no_set'] == 1 && $set['receiptNo_displayFrmt'] != 0) {
                if ($set['receiptNo_displayFrmt'] == 1) {
                    if ($set['scheme_wise_receipt'] == 1) {   //Common
                        $rcptFrmt = $rcpt['receipt_no'];
                    } else if ($set['scheme_wise_receipt'] == 2) {  //Branch-wise
                        $rcptFrmt = $rcpt['branch_code'] . '-' . $rcpt['receipt_no'];
                    } else if ($set['scheme_wise_receipt'] == 3) {   //scheme-wise
                        $rcptFrmt = $rcpt['scheme_code'] . '-' . $rcpt['receipt_no'];
                    } else if ($set['scheme_wise_receipt'] == 4) {  //scheme with Branch-wise
                        $rcptFrmt = $rcpt['scheme_code'] . $rcpt['branch_code'] . '-' . $rcpt['receipt_no'];
                    } else if ($set['scheme_wise_receipt'] == 5) {   // Financial Year-wise
                        $rcptFrmt = $rcpt['receipt_year'] . '-' . $rcpt['receipt_no'];
                    } else if ($set['scheme_wise_receipt'] == 6) {   //Financial Year with Scheme & Branch wise
                        $rcptFrmt = $rcpt['receipt_year'] . $rcpt['scheme_code'] . $rcpt['branch_code'] . '-' . $rcpt['receipt_no'];
                    } else if ($set['scheme_wise_receipt'] == 6) {   //Financial Year with Branch wise
                        $rcptFrmt = $rcpt['receipt_year'] . $rcpt['branch_code'] . '-' . $rcpt['receipt_no'];
                    }
                } else if ($set['receiptNo_displayFrmt'] == 2) {
                    $rcpt_frmt_fromdb = $this->getFormatFromDB();
                    $rcpt = $this->get_receipt_Data($id);
                    $frmt_short_code = [];
                    if ($rcpt_frmt_fromdb['custom_ReceiptDisplayFrmt'] != '' && $rcpt_frmt_fromdb['custom_ReceiptDisplayFrmt'] != null) {
                        if ($rcpt['receipt_no'] != '' && $rcpt['receipt_no'] != null && $rcpt['receipt_no'] != 0 && $rcpt['receipt_no'] != '-') {
                            $field_name = explode('@@', $rcpt_frmt_fromdb['custom_ReceiptDisplayFrmt']);
                            for ($i = 1; $i < count($field_name); $i += 2) {
                                $frmt_short_code[] = $field_name[$i];
                            }
                            $rcptFrmt = $this->getFormatedNumber($frmt_short_code, $rcpt);
                        } else {
                            $rcptFrmt = '-';
                        }
                    } else {
                        if ($rcpt['receipt_no'] != '' && $rcpt['receipt_no'] != null && $rcpt['receipt_no'] != 0 && $rcpt['receipt_no'] != '-') {
                            $rcptFrmt = $rcpt['receipt_no'];
                        } else {
                            $rcptFrmt = '-';
                        }
                    }
                }
            } else {
                if ($rcpt['receipt_no'] != '' && $rcpt['receipt_no'] != null && $rcpt['receipt_no'] !== 0 && $rcpt['receipt_no'] != '-') {
                    $rcptFrmt = $rcpt['receipt_no'];
                } else {
                    $rcptFrmt = '-';
                }
            }
        }
        return $rcptFrmt;
    }
    function get_AccRcpt_DisplaySettings()
    {
        /* get account number and receipt number settings from chit settings table...   */
        $sql = "SELECT cs.scheme_wise_receipt,cs.scheme_wise_acc_no,cs.schemeaccNo_displayFrmt,cs.receiptNo_displayFrmt,cs.custom_AccDisplayFrmt,cs.custom_ReceiptDisplayFrmt,cs.enable_old_sch_acc_no,
	            cs.receipt_no_set,cs.schemeacc_no_set,cs.group_wise_receipt
                FROM `chit_settings` cs";
        return $this->db->query($sql)->row_array();
    }
    function get_acc_Data($id)
    {
        /* get necessary data of scheme account by id... */
        // Guard: prevent broken SQL when $id is null/empty (e.g., orphaned payment records)
        if (empty($id) && $id !== 0) {
            return array('sch_AccNo' => 'Not Allocated', 'start_year' => '', 'branch_code' => '', 'code' => '', 'group_code' => '', 'oldscheme_code' => '', 'is_lucky_draw' => 0, 'scheme_code' => '', 'cmp_code' => '');
        }
        $sql = "SELECT IFNULL(sa.scheme_acc_number,'Not Allocated') as sch_AccNo,IFNULL(sa.start_year,'') as start_year,b.short_name as branch_code,s.code,IFNULL(sa.group_code,'') as group_code,
	                if(s.is_lucky_draw = 1, CONCAT(s.code,'(',ifnull(sa.group_code,''),')') ,s.code) as oldscheme_code,s.is_lucky_draw,s.code as scheme_code,c.short_code as cmp_code
                FROM scheme_account sa
                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                LEFT JOIN branch b ON (b.id_branch = sa.id_branch)
                 join company c
                WHERE sa.id_scheme_account = " . $id;
        return $this->db->query($sql)->row_array();
    }
    function get_receipt_Data($id)
    {
        /* get necessary data of payment by id... */
        // Guard: prevent broken SQL when $id is null/empty
        if (empty($id) && $id !== 0) {
            return array('receipt_no' => '-', 'receipt_year' => '', 'branch_code' => '', 'code' => '', 'group_code' => '', 'scheme_code' => '', 'cmp_code' => '');
        }
        $sql = "SELECT IFNULL(p.receipt_no,'-') as receipt_no,IFNULL(p.receipt_year,'') as receipt_year,b.short_name as branch_code,s.code,IFNULL(sa.group_code,'') as group_code,
	                if(s.is_lucky_draw = 1 && cs.group_wise_receipt = 1 , CONCAT(s.code,'(',ifnull(sa.group_code,''),')') ,s.code) as scheme_code,c.short_code as cmp_code
                FROM payment p
                LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account)
                LEFT JOIN scheme s ON (s.id_scheme = sa.id_scheme)
                LEFT JOIN branch b ON (b.id_branch = p.id_branch)
                JOIN chit_settings cs
                 join company c
                WHERE p.id_payment = " . $id;
        return $this->db->query($sql)->row_array();
    }
    public function updData($data, $id_field, $id_value, $table)
    {
        $edit_flag = 0;
        $this->db->where($id_field, $id_value);
        $edit_flag = $this->db->update($table, $data);
        return ($edit_flag == 1 ? $id_value : 0);
    }
    // Added by Durga for customized account and receipt number settings starts here 26-06-2023
    function getFormatFromDB()
    {
        $sql = "SELECT custom_AccDisplayFrmt,custom_ReceiptDisplayFrmt,receiptNo_displayFrmt,schemeaccNo_displayFrmt from chit_settings where id_chit_settings=1";
        return $this->db->query($sql)->row_array();
    }
    function getFormatedNumber($frmt_short_code, $acc)
    {
        if( $this->config->item('receipt_no_padding') > 0){
            $acc['receipt_no'] = str_pad($acc['receipt_no'], $this->config->item('receipt_no_padding'), '0', STR_PAD_LEFT);
        }
        if( $this->config->item('sch_AccNo_padding') > 0){
            $acc['sch_AccNo'] = str_pad($acc['sch_AccNo'], $this->config->item('sch_AccNo_padding'), '0', STR_PAD_LEFT);
        }
        if ($frmt_short_code != '' && $frmt_short_code != null) {
            $finalFormat = '';
            foreach ($frmt_short_code as $code) {
                switch ($code) {
                    case 'br_code':
                        $finalFormat .= $acc['branch_code'];
                        break;
                     case 'cmp_code':
                        $finalFormat .= $acc['cmp_code'];
                        break;
                    case 'acc_num':
                        $finalFormat .= $acc['sch_AccNo'];
                        break;
                    case 'sch_code':
                        $finalFormat .= $acc['scheme_code'];
                        break;
                    /*case 'grp_code':
                        $finalFormat.=$acc['group_code'];
                        break;*/
                    case 'grp_code':
                        if ($acc['group_code'] != null && $acc['group_code'] != '') {
                            $finalFormat .= $acc['group_code'];
                        } else {
                            $lastCharacter = substr($finalFormat, -1);
                            if ($lastCharacter == '-') {
                                $finalFormat = substr($finalFormat, 0, -1);
                            }
                        }
                        break;
                    case 'fin_yr':
                        $finalFormat .= $acc['start_year'];
                        break;
                    case 'rcpt_yr':
                        $finalFormat .= $acc['receipt_year'];
                        break;
                    case 'rcpt_num':
                        $finalFormat .= $acc['receipt_no'];
                        break;
                    case 'hyphen':
                        $finalFormat .= '-';
                        break;
                    case 'space':
                        $finalFormat .= ' ';
                        break;
                }
            }
        }
        //$finalFormat=substr($finalFormat, 1);
        return $finalFormat;
    }
    // Added by Durga for customized account and receipt number settings ends here 26-06-2023
    function mobile_available_import($mobile)
    {
        $this->db->select('mobile');
        $this->db->where('mobile', $mobile);
        if ($id_customer) {
            $this->db->where('id_customer <>', $id_customer);
        }
        $status = $this->db->get('customer');
        if ($status->num_rows() > 0) {
            $result = 1;
        } else {
            $result = 0;
        }
        return $result;
    }
    //kyc table functions starts
    public function insert_kyc($data)
    {
        $status = $this->db->insert('kyc', $data);
        $id = $this->db->insert_id();
        return $id;
    }
    public function updkycData($kyc_ins_data, $id, $type)
    {
        $this->db->where('id_customer', $id)->where('kyc_type', $type);
        $this->db->update('kyc', $kyc_ins_data);
        //	print_r($this->db->last_query());exit;
    }
    public function updkycDatabk($kyc_ins_data, $id, $type, $acc)
    {
        $this->db->where('id_customer', $id)->where('kyc_type', $type)->where('number',$acc);
        $res = $this->db->update('kyc', $kyc_ins_data);
        return $res;
        //	print_r($this->db->last_query());exit;
    }


    public function kyc_exists($cus_id, $kyc_type, $account_number) {
        $this->db->from('kyc');
        $this->db->where('id_customer', $cus_id);
        $this->db->where('kyc_type', $kyc_type);
        $this->db->where('number', $account_number);
        return $this->db->count_all_results() > 0;
    }

   public function update_kyc($id, $data) {
        $this->db->where('id_kyc', $id);
       
        $this->db->update('kyc', $data);
    }


    public function get_kyc_byid($id, $type = '')
    {
        $sql = "select * from kyc where id_customer=" . $id;
        if ($type != '') {
            $sql = $sql . " and kyc_type=" . $type;
        }
        $res = $this->db->query($sql);
        return $res->result_array();
    }
    //kyc table functions ends
    public function insertData($data, $table)
    {
        $insert_flag = 0;
        $insert_flag = $this->db->insert($table, $data);
        return ($insert_flag == 1 ? $this->db->insert_id() : 0);
    }
    function getVillageZone($id_zone)
    {
        $sql = $this->db->query("SELECT z.id_zone,z.name,b.name as branch_name,z.id_branch
	    from village_zone z
	    left join branch b on b.id_branch=z.id_branch
	    where id_zone=" . $id_zone . "");
        return $sql->row_array();
    }
    public function deleteData($id_field, $id_value, $table)
    {
        $this->db->where($id_field, $id_value);
        $status = $this->db->delete($table);
        return $status;
    }
    public function updateData($data, $id_field, $id_value, $table)
    {
        $edit_flag = 0;
        $this->db->where($id_field, $id_value);
        $edit_flag = $this->db->update($table, $data);
        return ($edit_flag == 1 ? $id_value : 0);
    }
    //Master zone

    function getAllActiveEmployee(){
        $sql="Select id_employee,CONCAT(firstname,' ',IFNULL(lastname,''),' - ',emp_code) as employee_data,emp_code From employee where active = 1";
		return $data = $this->db->query($sql)->result_array();	
    }

    public function allocate_employee($data,$id)
    {    	
	    $cus_info=0;

    	$this->db->where('id_customer',$id); 

		$cus_info=$this->db->update(self::TABLE_NAME,$data);		

		return ($cus_info==1? 1 : 0);

    }
    
    function get_ajaxzone_list()
    {
        $sql = $this->db->query("SELECT z.id_zone,z.name,b.name as branch_name
	    from village_zone z
	    left join branch b on b.id_branch=z.id_branch");
        return $sql->result_array();
    }
    function get_cus()
    {
        $sql = $this->db->query("SELECT c.id_customer,c.mobile
	    from customer c where mobile is not null and c.id_customer not in (select id_customer from wallet_account) ");
        return $sql->result_array();
    }
    function get_branch_details()
    {
        // var_dump(!empty($this->session->userdata('id_branch')));exit;
        // echo "<pre>";print_r($this->session->userdata);exit;
        if ($this->session->userdata('branch_settings') == 1) {
            $sql = $this->db->query("SELECT b.id_branch,b.id_city,b.id_country,b.id_state FROM branch b where 1 " . (!empty($this->session->userdata('id_branch')) ? "and  b.id_branch =" . $this->session->userdata('id_branch') : "and b.is_ho=1"));
            return $sql->row_array();
        } else {
            return ['id_city' => 0, 'id_state' => 0, 'id_country' => 0];
        }
    }

    public function get_kyc_report_list($from_date = '', $to_date = '', $status = '', $type = '', $list_type = 1, $mobile = '', $id_customer = 0)
    {
        $sql = "SELECT k.id_kyc, k.id_customer, k.kyc_type, k.number, k.name, k.bank_ifsc, k.bank_branch,
                    k.status, k.dob, k.emp_verified_by, k.verification_type, k.added_by,
                    k.date_upd, k.date_add, k.img_url, k.back_img_url, k.document_url, k.cheque_img, k.cheque_doc_url,
                    CONCAT(c.firstname,' ',IFNULL(c.lastname,'')) as cus_name,
                    c.mobile, c.nominee_name,
                    IFNULL(CONCAT(e.firstname,' ',IFNULL(e.lastname,'')),'-') as verified_by_name
                FROM customer_kyc k
                LEFT JOIN customer c ON (c.id_customer = k.id_customer)
                LEFT JOIN employee e ON (e.id_employee = k.emp_verified_by)
                WHERE 1 ";

        if ($from_date != '' && $to_date != '') {
            $sql .= " AND DATE(k.date_add) BETWEEN '" . date('Y-m-d', strtotime($from_date)) . "' AND '" . date('Y-m-d', strtotime($to_date)) . "'";
        }
        if ($status !== '' && $status != 4) {
            $sql .= " AND k.status = " . intval($status);
        }
        if ($type != '' && $type != 0) {
            $sql .= " AND k.kyc_type = " . intval($type);
        }
        if ($list_type == 2) {
            $sql .= " AND IFNULL(k.is_nominee, 0) = 1";
        } else {
            $sql .= " AND IFNULL(k.is_nominee, 0) = 0";
        }
        if ($mobile != '') {
            $sql .= " AND c.mobile LIKE '%" . $this->db->escape_like_str($mobile) . "%'";
        }
        if ($id_customer > 0) {
            $sql .= " AND k.id_customer = " . intval($id_customer);
        }
        $sql .= " ORDER BY k.id_kyc DESC";
        // print_r($sql);exit;
        return $this->db->query($sql)->result_array();
    }
}
?>