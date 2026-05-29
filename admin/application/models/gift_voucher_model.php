<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gift_voucher_model extends CI_Model

{

	function __construct()

    {

        parent::__construct();

    }

    // General Functions

    public function insertData($data,$table)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert($table, $data);

		return ($insert_flag == 1 ? $this->db->insert_id(): 0);

	}

	public function insertBatchData($data,$table)

    {

    	$insert_flag = 0;

		$insert_flag = $this->db->insert_batch($table, $data);

		if ($this->db->affected_rows() > 0){

			return TRUE;

		}else{

			return FALSE;

		}

	}

	public function updateData($data, $id_field, $id_value, $table)

    {    

	    $edit_flag = 0;

	    $this->db->where($id_field, $id_value);

		$edit_flag = $this->db->update($table,$data);

		return ($edit_flag==1?$id_value:0);

	}	 

	public function deleteData($id_field,$id_value,$table)

    {

        $this->db->where($id_field, $id_value);

        $status= $this->db->delete($table); 

		return $status;

	}
	
	function get_payModes()

    {

		$sql = "SELECT * FROM payment_mode where show_in_pay = 1 ORDER BY sort_order";

		return $this->db->query($sql)->result_array();	

	} 
	
	function get_currentBranchName($branch_id){

		$branch_name = "";

		$branch_query = $this->db->query("SELECT id_branch, name FROM branch WHERE id_branch = $branch_id");

		if($branch_query->num_rows() > 0){

			$branch_name = $branch_query->row()->name;

		}

		return $branch_name;

	}
	
	
	function getCompanyDetails($id_branch)

	{

		if($id_branch=='')

		{

			$sql = $this->db->query("Select  c.id_company,c.company_name,c.gst_number,c.short_code,c.pincode,c.mobile,c.whatsapp_no,c.phone,c.email,c.website,c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,cy.name as country,cs.currency_symbol,cs.currency_name,cs.mob_code,cs.mob_no_len,c.mail_server,c.mail_password,c.send_through,c.mobile1,c.phone1,c.smtp_user,c.smtp_pass,c.smtp_host,c.server_type,cs.login_branch,
			s.state_code

			from company c

			join chit_settings cs

			left join country cy on (c.id_country=cy.id_country)

			left join state s on (c.id_state=s.id_state)

			left join city ct on (c.id_city=ct.id_city)");

		}

		else

		{

			$sql=$this->db->query("select b.name,b.address1,b.address2,c.company_name,

				cy.name as country,ct.name as city,s.name as state,b.pincode,s.id_state,s.state_code

				from branch b

				join company c

				left join country cy on (b.id_country=cy.id_country)

				left join state s on (b.id_state=s.id_state)

				left join city ct on (b.id_city=ct.id_city)");

		}

		$result = $sql->row_array();

		return $result;

	}

	
	function get_ret_settings($settings)
	{
		$data=$this->db->query("SELECT value FROM ret_settings where name='".$settings."'"); 
		return $data->row()->value;
	}

	
	public function get_gift_voucher($id_gift_voucher)
	{
	    $sql=$this->db->query("SELECT * FROM ret_gift_voucher_master 
	    WHERE status=1 ".($id_gift_voucher!='' ? " and id_gift_voucher=".$id_gift_voucher."" :'')."");
	    if($id_gift_voucher!='')
	    {
	        return $sql->row_array();
	    }else{
	        return $sql->result_array();
	    }
	    
	}
	
	function getAvailableCustomers($SearchTxt){
		$data = $this->db->query("SELECT c.id_customer as value, concat(c.firstname,'-',c.mobile) as label,c.id_village,v.village_name,if(c.is_vip=1,'Yes','No') as vip,
			(select count(sa.id_scheme_account) from scheme_account sa left join customer cus on cus.id_customer=sa.id_customer) as accounts
			FROM customer c
			left join village v on v.id_village=c.id_village
			WHERE username like '%".$SearchTxt."%' OR mobile like '%".$SearchTxt."%' OR firstname like '%".$SearchTxt."%'"); 
		return $data->result_array();
	}
	
	function getAvailableEmployees($SearchTxt)
	{
	    	$data = $this->db->query("SELECT  e.id_employee as value, concat(e.firstname,'-',e.emp_code) as label
            FROM employee e
			WHERE e.firstname like '%".$SearchTxt."%' OR e.mobile like '%".$SearchTxt."%' OR e.emp_code like '%".$SearchTxt."%'"); 
		return $data->result_array();
	}
	
	function get_customer($id_customer)
	{
	    $sql=$this->db->query("select firstname from customer where id_customer=".$id_customer."");
	    return $sql->row()->firstname;
	}
	
	function get_employee($id_employee)
	{
	    $sql=$this->db->query("select firstname from employee where id_employee=".$id_employee."");
	    return $sql->row()->firstname;
	}
	
	function get_receipt_details($id_gift_card)
	{
	    $sql=$this->db->query("SELECT g.id_gift_card,g.id_branch,date_format(g.date_add,'%d-%m-%Y') as date_add,g.amount,g.code,
        if(g.gift_for=1,e.firstname,c.firstname) as cus_name,IFNULL(b.name,'') as branch_name,if(g.gift_for=1,e.mobile,c.mobile) as mobile,g.free_card,
        date_format(g.valid_to,'%d-%m-%Y') as valid_to,ifnull(emp.emp_code,'') as emp_code,g.id_gift_voucher,m.voucher_type,IFNULL(m.description,'') as description,
        mt.metal,m.utilize_for,m.name,c.firstname
        FROM gift_card g
        LEFT JOIN customer c ON c.id_customer=g.purchased_by
        LEFT JOIN employee e ON e.id_employee=g.purchased_by
        LEFT JOIN employee emp ON emp.id_employee=g.emp_created
        LEFT JOIN branch b on b.id_branch=g.id_branch
        LEFT JOIN ret_gift_voucher_master m on m.id_gift_voucher=g.id_gift_voucher
        LEFT JOIN metal mt on mt.id_metal=m.utilize_for
        WHERE g.id_gift_card=".$id_gift_card."");
        return $sql->row_array();
	}
	
    function getPromotionalVouchers($ref_no)
	{
	    $sql=$this->db->query("SELECT g.id_gift_card,g.id_branch,date_format(g.date_add,'%d-%m-%Y') as date_add,g.amount,g.percentage,g.code,
        if(g.gift_for=1,e.firstname,c.firstname) as name,IFNULL(b.name,'') as branch_name,if(g.gift_for=1,e.mobile,c.mobile) as mobile,g.free_card,
        date_format(g.valid_to,'%d-%m-%Y') as valid_to,ifnull(emp.emp_code,'') as emp_code,g.id_gift_voucher,m.voucher_type,IFNULL(m.description,'') as description,
        mt.metal,m.utilize_for,m.name,g.credit_type
        FROM gift_card g
        LEFT JOIN customer c ON c.id_customer=g.purchased_by
        LEFT JOIN employee e ON e.id_employee=g.purchased_by
        LEFT JOIN employee emp ON emp.id_employee=g.emp_created
        LEFT JOIN branch b on b.id_branch=g.id_branch
        LEFT JOIN ret_gift_voucher_master m on m.id_gift_voucher=g.id_gift_voucher
        LEFT JOIN metal mt on mt.id_metal=m.utilize_for
        WHERE g.ref_no=".$ref_no."");
        return $sql->result_array();
	}
	
	function get_receipt_payment($id_gift_card)
	{
	    $sql=$this->db->query("select sum(p.amount) as payment_amt,p.payment_mode from gift_card_payment p 
	    where p.id_gift_card=".$id_gift_card."
	    group by p.payment_mode");
	    return $sql->result_array();
	}
	
	function get_branchwise_rate($id_branch)

	{

		$is_branchwise_rate=$this->session->userdata('is_branchwise_rate');

		if($id_branch!='' && $id_branch!=0 && $is_branchwise_rate==1)

		{

		    $sql="SELECT  b.name as name,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,

		    Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime   

		    FROM metal_rates m 

		    LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates

		    left join branch b on b.id_branch=br.id_branch

		    ".($id_branch!='' ?" WHERE br.id_branch=".$id_branch."" :'')." ORDER by br.id_metalrate desc LIMIT 1";

		}

		else

		{

		    $sql="SELECT  m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,

		    Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime   

		    FROM metal_rates m 

		    ORDER by m.id_metalrates desc LIMIT 1";

		}

		return $this->db->query($sql)->row_array();

	}
	
	
	    
    /*Gift Voucher functions here */ 	
  
   	 function ajax_get_gift_master()
    {
		$id_gift_voucher = $this->db->query("SELECT * FROM ret_gift_voucher_master ORDER BY id_gift_voucher desc");
		return $id_gift_voucher->result_array();
	}

	 function gift_voucher($id_gift_voucher)
    {
		$id_gift_voucher = $this->db->query("select * FROM ret_gift_voucher_master where id_gift_voucher=".$id_gift_voucher);
		return $id_gift_voucher->row_array();
	}
	
 
	/*End of Gift Voucher functions*/
	
	
	
	  /*Gift Voucher Settings functions here */ 
	
	function get_empty_record()
	{
	    $data=array(
	               'id_set_gift_voucher'=>NULL,
	               'gift_type'          =>NULL,
	               'credit_value'       =>NULL,
	               'sale_value'         =>NULL,
	               'utilize_for'        =>NULL,
	               'validity_days'      =>NULL,
	               'metal'              =>NULL,
	               );
	   return $data;
	}
	
	function get_prod_settings($id,$pro_id)
	{
	    $sql = $this->db->query("SELECT * FROM ret_gift_issue_redeem_prod WHERE id_prod_set = $id and id_product=".$pro_id."");
		if($sql->num_rows() > 0)
		{
		    return TRUE;
		}else{
		    return FALSE;
		}
	}
	
    function ajax_gift_settings()
    {
        $sql=$this->db->query("SELECT s.id_set_gift_voucher,if(s.gift_type=1,'Amount to Amount',if(s.gift_type=2,'Amount to Weight',if(s.gift_type=3,'Weight to Amount','Weight to Weight'))) as gift_type,
        s.credit_value,status,s.sale_value,IFNULL(s.id_branch,'') as id_branch,b.name as branch_name,is_default
        from ret_bill_gift_voucher_settings s
        LEFT JOIN branch b on b.id_branch=s.id_branch");
        return $sql->result_array();
    }
    
    function get_gift_settings($id)
    {
		$sql = $this->db->query("select * from ret_bill_gift_voucher_settings 
		where id_set_gift_voucher=".$id);
		return $sql->row_array();
	}
	
	function CheckProductAvailability($id)
	{
	    $sql=$this->db->query("select s.id_gift_voucher,s.id_product,s.issue,s.utilize,p.product_name,c.name
	    from ret_gift_issue_redeem_prod s
	    LEFT JOIN ret_product_master p on p.pro_id=s.id_product
	    LEFT JOIN ret_category c ON c.id_ret_category=p.cat_id
	    where id_gift_voucher=".$id);
	    //print_r($this->db->last_query());exit;
	    return $sql->result_array();
	}
    
    function update_gift_settings($id_branch)
    {
        $edit_flag = 0;
        if($id_branch!='')
        {
            $this->db->where('id_branch', $id_branch);
        }
        $edit_flag = $this->db->update('ret_bill_gift_voucher_settings',array('is_default'=>0));
        return TRUE;
    }
	
	public function getActiveMetal()
	{
	    $sql=$this->db->query("SELECT m.metal,m.id_metal FROM metal m WHERE m.metal_status=1");
	    return $sql->result_array();
	}
	/*End of Gift Voucher Settings functions*/
	
	public function get_Activeproduct()
	{
	    $sql=$this->db->query("SELECT p.product_name,p.pro_id,c.name
	    FROM ret_product_master p 
	    LEFT JOIN ret_category c on c.id_ret_category=p.cat_id
	    WHERE p.product_status=1");
	    //print_r($this->db->last_query());exit;
	    return $sql->result_array();
	}
	
	public function getGiftCreditType($id){
	    $sql=$this->db->query("SELECT credit_type FROM ret_gift_voucher_master WHERE id_gift_voucher=".$id."");
	   // print_r($this->db->last_query());exit;
	    return $sql->row()->credit_type;
	    
	    
	}


}

?>