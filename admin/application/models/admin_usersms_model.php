<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_usersms_model extends CI_Model
{
	
	const SER_TABLE = 'services';
	const ACC_TABLE = "scheme_account";
	const CUS_TABLE	= "customer";
	const SCH_TABLE	= "scheme";
	const ADD_TABLE	= "address";
	const NOTI_TABLE	= "notification";
	const CHIT_TABLE	= "chit_settings";
	const MOD_TABLE	=  "modules";
	const RET_SET_TABLE = "ret_settings";
	
	
	function __construct()
    {
        parent::__construct();
    }
	
	//decrypt
	public function __decrypt($str)
	{
		return base64_decode($str);		
	}
	
	function getnotificationids()
	{
		$sql = $this->db->query("SELECT r.token as token ,c.mobile
									from registered_devices r 
									LEFT JOIN customer c on (c.id_customer=r.id_customer) 
									where c.notification = 1;");
		
		/*$token = array_map(function ($value) {
					return  $value['token'];
					}, $sql->result_array());*/
					$data =$sql->result_array();
		
		return $data;
	}
	
	//Group sms/mail
	public function empty_record() 										//Fetch listing record
	{	
		$data=array(
				
				'id_scheme'			=> 0,
				'group_name'		=> NULL,
				'header'	        => NULL,
				'desc'				=> NULL,
				'footer'			=> NULL,
				'email'				=> 0,	
				'sms'				=> 0,
				'active'			=> 0,	
				'is_greetings'		=> 0,	
				'serv_group_date'	=> NULL
		);
		return $data;
}
	
	public function get_schemes()
	{
			$this->db->select('id_scheme,scheme_name as name');
			$this->db->where('active','1')  ;
			$data=$this->db->get(self::SCH_TABLE);
		    return $data->result_array();
	}
	
	
	public function get_schemes_name($id="")
	{
		
			$sql="SELECT s.id_scheme,s.scheme_name as name 
				 FROM scheme_branch sb
					LEFT JOIN scheme s on (s.id_scheme=sb.id_scheme)
						where active=1 and visible=1 and id_branch=".$id."";
						$data =	$this->db->query($sql);
		                return $data->result_array();
	}
	
	public function get_noti_empty_record() 										//Fetch listing record
	{	
		$data=array(
				
				'id_notification'		=> 0,
				'noti_name'			=> NULL,
				'noti_msg'	        => NULL,
				'noti_footer'		=> NULL, 
				'send_notif_on'		=> NULL,                          //Remainder Notif //HH
				'send_daily_from'	=> NULL
		);
		return $data;
	}
	
	public function get_noti_entry_record($id) 						//Fetch entry record
	{	
	//Build contents query
	$query="select id_notification, noti_name, noti_msg, noti_footer,send_notif_on,send_daily_from
			from notification 
			
			where id_notification =".$id;
	//print_r($query);exit;
	
	$result_set=$this->db->query($query);			
	foreach ($result_set->result() as $row)
	{
		
		$records['id_notification'] = $row->id_notification;
		$records['noti_name'] 	= $row->noti_name;			
		$records['noti_msg']		= $row->noti_msg;
		$records['noti_footer']	= $row->noti_footer;
		$records['send_notif_on']	= $row->send_notif_on;          //Remainder Notif //HH
		$records['send_daily_from']	= $row->send_daily_from;
	}		
	return $records;

	}
	
	function get_notification_services()
   	 {
		 $sql = "Select * from notification";
		return $group=$this->db->query($sql)->result_array();
		}
	
	
	function get_customers_by_scheme($id)
    {
		
		if($this->session->userdata('filerbybranch')==0)
		{

			$sql = "Select
			  c.id_customer ,concat(firstname,' ',if(lastname!=NULL,lastname,'')) as name,sh.id_scheme,
			   c.phone,c.mobile,c.email,
			count(sa.id_scheme_account) as accounts,
			c.username,c.active,c.`date_add`,c.`date_upd`
				From
				  customer c
				left join address a on(c.id_customer=a.id_customer)
				left join country cy on (a.id_country=cy.id_country)
				left join state s on (a.id_state=s.id_state)
				left join city ct on (a.id_city=ct.id_city)

		  left join scheme_account sa on (c.id_customer=sa.id_customer and sa.active=1 and sa.is_closed=0)
		   left join scheme sh on (sh.id_scheme=sa.id_scheme)
			where sh.id_scheme=".$id." and c.active=1	
			Group by c.id_customer"; 
		}
		else{
			
			$sql = "Select
		  c.id_customer,concat(firstname,' ',if(lastname!=NULL,lastname,'')) as name,sh.id_scheme,
		   c.phone,c.mobile,c.email,
		count(sa.id_scheme_account) as accounts,
		c.username,c.active,c.`date_add`,c.`date_upd`
			From
			  customer c
			left join address a on(c.id_customer=a.id_customer)
			left join country cy on (a.id_country=cy.id_country)
			left join state s on (a.id_state=s.id_state)
			left join city ct on (a.id_city=ct.id_city)

      left join scheme_account sa on (c.id_customer=sa.id_customer and sa.active=1 and sa.is_closed=0)
       left join scheme sh on (sh.id_scheme=sa.id_scheme)
		where c.id_branch=".$this->session->userdata('id_branch')." and sh.id_scheme=".$id." and c.active=1
		   Group by c.id_customer";
		}
		return $group=$this->db->query($sql)->result_array();
	}
	
	//catlog module//HH
	
	 function get_modules()
   	 {
		 $sql = "Select * from modules";
		return $group=$this->db->query($sql)->result_array();
		}
	public function get_empty_records() 										//Fetch listing record
	{	
		$data=array(
				
				'id_module'		=> 0,
				'm_name	'			=> NULL,
				'm_app'	        => NULL,
				'm_web'		=> NULL,
		      'm_active'  	=> NULL
		);
		return $data;
	}
	public function get_entry_records($id) 						//Fetch entry record
	{	
	//Build contents query
	 //print_r($query);exit;
	$query="select id_module, m_name	, m_app,m_web,m_active
			from modules 
			
			where id_module =".$id;
	$result_set=$this->db->query($query);			
	foreach ($result_set->result() as $row)
	{
		
		$records['id_module'] = $row->id_module;
		$records['m_name'] 	= $row->m_name;			
		$records['m_app']		= $row->m_app;
		$records['m_web']	= $row->m_web;
        $records['m_active'] =$row->m_active;
       //print_r($this->db->last_query($records));

	}		
	return $records;

	}	
	
	
        public function delete_module($id)
	{
		$this->db->where('id_module',$id);  
		$status=$this->db->delete(self::MOD_TABLE);
		return $status;
	}	
		
      public function update_module_status($data,$id)
    {    	
    	$edit_flag=0;
    	$this->db->where('id_module',$id); 
		$m_info=$this->db->update(self::MOD_TABLE,$data);		
		return $m_info;
	}


        public function insert_module($data)
	{
			
			$this->db->insert(self::MOD_TABLE, $data);						
    }
    
     public function update_module($data)
	{
			$this->db->where('id_module',$data['id_module']); 
			$this->db->update(self::MOD_TABLE, $data);						
    }
    

//catlog module//
	

	 function get_sms_services()
   	 {
		 $sql = "Select * from services";
		return $group=$this->db->query($sql)->result_array();
		}
	public function get_empty_record() 										//Fetch listing record
	{	
		$data=array(
				
				'id_services'		=> 0,
				'dlt_te_id'		=> NULL, 
				'serv_name'			=> NULL,
				'sms_msg'	        => NULL,
				'sms_footer'		=> NULL,
				'send_sms_on'		=> NULL,                           //Remainder SMS //HH
				'send_daily_from'	=> NULL
		);
		return $data;
	}
	public function get_entry_record($id) 						//Fetch entry record
	{	
	//Build contents query
	$query="select id_services, template_name,dlt_te_id,serv_name, sms_msg,sms_footer,send_sms_on,send_daily_from
			from services 
			
			where id_services =".$id;
	$result_set=$this->db->query($query);			
	foreach ($result_set->result() as $row)
	{
		
		$records['id_services'] = $row->id_services;
		$records['dlt_te_id']	= $row->dlt_te_id;
		$records['serv_name'] 	= $row->serv_name;			
		$records['sms_msg']		= $row->sms_msg;
		$records['sms_footer']	= $row->sms_footer;
		$records['send_sms_on']	= $row->send_sms_on;                   //Remainder SMS //HH
		$records['send_daily_from']	= $row->send_daily_from;
		$records['template_name']	= $row->template_name;
	}		
	return $records;

	}
	 public function insert_service($data)
	{
			
			$this->db->insert(self::SER_TABLE, $data);						
    }
    
    public function update_service($data)
	{
			$this->db->where('id_services',$data['id_services']); 
			$this->db->update(self::SER_TABLE, $data);						
    }
    
    
    public function insert_notification($data)
	{
			
			$this->db->insert(self::NOTI_TABLE, $data);	
			
    }
    
    public function update_notification($data)
	{
			$this->db->where('id_notification',$data['id_notification']); 
			$this->db->update(self::NOTI_TABLE, $data);						
    }
    
    
    function Get_service_code_sms($service_code, $id="",$attachement_url="")
    {
        //Declaration of variables
        $message ="";
        $sms_msg = "";
        $sms_footer = "";
        $customer_data = array();
        if($service_code=='KAR_ALOC')
        {
            $resultset = $this->db->query("SELECT SUM(d.totalitems) as order_pcs,SUM(d.weight) as order_weight,k.firstname as karigar_name,IFNULL(k.email,'') as email,
    	    p.product_name,des.design_name,concat(s.value,' ',s.name) as size,IFNULL(k.contactno1,'') as mobile,cus.pur_no,date_format(cus.order_date,'%d-%m-%Y') as order_date,
    	    emp.firstname as emp_name,cmp.company_name as cmp_name
            FROM customerorder cus 
            LEFT JOIN customerorderdetails d ON d.id_customerorder=cus.id_customerorder
            LEFT JOIN joborder j ON j.id_order=d.id_orderdetails
            LEFT JOIN ret_karigar k ON k.id_karigar=j.id_vendor
            LEFT JOIN ret_product_master p ON p.pro_id=d.id_product
            LEFT JOIN ret_design_master des ON des.design_no=d.design_no
            LEFT JOIN ret_size s ON s.id_size=d.size
            LEFT JOIN employee emp on emp.id_employee=cus.order_taken_by
            JOIN company cmp
            WHERE cus.order_for=2  AND cus.id_customerorder=".$id."
            group by d.id_product,d.design_no");
            //print_r($this->db->last_query());exit;
        }
        else if($service_code=='CUS_ORD_DEL')
        {
             $resultset = $this->db->query("SELECT c.order_no,b.name as branch_name,cmp.company_name as cmp_name,cus.mobile,b.mobile as contact_no
            FROM customerorderdetails d 
            LEFT JOIN customerorder c ON c.id_customerorder=d.id_customerorder
            LEFT JOIN customer cus ON cus.id_customer=c.order_to
            LEFT JOIN branch b ON b.id_branch=c.order_from
            JOIN company cmp
            WHERE d.id_orderdetails=".$id." AND c.order_for=2 GROUP by c.id_customerorder");
           // print_r($this->db->last_query());exit;
        }
        else if($service_code=='CUS_ORD')
        {
             $resultset = $this->db->query("SELECT c.order_no,b.name as branch_name,cmp.company_name as cmp_name,cus.mobile,b.mobile as contact_no
            FROM customerorderdetails d 
            LEFT JOIN customerorder c ON c.id_customerorder=d.id_customerorder
            LEFT JOIN customer cus ON cus.id_customer=c.order_to
            LEFT JOIN branch b ON b.id_branch=c.order_from
            JOIN company cmp
            WHERE c.id_customerorder=".$id." AND c.order_for=2 GROUP by c.id_customerorder");
           // print_r($this->db->last_query());exit;
        }
        else if($service_code=='BDAY_WISH')
        {
            $resultset = $this->db->query("SELECT CONCAT(c.firstname,' ',IFNULL(c.lastname,''))  as fname,c.mobile,cmp.company_name as cmp_name,cmp.comp_name_in_sms
            FROM customer c 
            JOIN company cmp
            WHERE c.id_customer=".$id." ");
        }
        else if($service_code=='WED_WISH')
        {
            $resultset = $this->db->query("SELECT CONCAT(c.firstname,' ',IFNULL(c.lastname,''))  as fname,c.mobile,cmp.company_name as cmp_name,cmp.comp_name_in_sms
            FROM customer c 
            JOIN company cmp
            WHERE c.id_customer=".$id." ");
        }
        else if($service_code == 'BILLING')
		{
		    $resultset=$this->db->query("SELECT c.mobile,cmp.company_name as cmp_name,br.name as branch_name
            FROM ret_billing b
            LEFT JOIN customer c ON c.id_customer=b.bill_cus_id
            LEFT JOIN branch br ON br.id_branch=b.id_branch
            JOIN company cmp
            WHERE b.bill_id=".$id."");
		}else if($service_code == 'WALLET_REMIN'){
		    
		    $resultset = $this->db->query("SELECT c.mobile,
             SUM(CASE WHEN wt.transaction_type = 0 THEN wt.value ELSE -wt.value END) AS balamt 
             FROM customer c LEFT JOIN wallet_account wa ON c.id_customer = wa.id_customer 
             LEFT JOIN wallet_transaction wt ON wa.id_wallet_account = wt.id_wallet_account
             where   c.id_customer=".$id);
		    
		}
       
        foreach($resultset->result() as $row)
        {
             if($attachement_url!='')
            {
                $row->attachement_url = $attachement_url;
            }
            $customer_data = $row;
            $mobile=$row->mobile;
        }
        $resultset = $this->db->query("SELECT serv_sms,serv_email,sms_msg, sms_footer,dlt_te_id from services where serv_code = '".$service_code."'");
        foreach($resultset->result() as $row)
        {
            $serv_sms = $row->serv_sms;
            $serv_email = $row->serv_email;
            $sms_msg = $row->sms_msg;
            $sms_footer = $row->sms_footer;
            $dlt_te_id = $row->dlt_te_id;    //AB
        }
        $resultset->free_result();
        //Generating Message content
        $field_name = explode('@@', $sms_msg);
        for($i=1; $i < count($field_name); $i+=2) 
        {
            $field =  $field_name[$i];
            if(isset($customer_data->$field))
            { 
                $content = strtolower($customer_data->$field);
                $content = ucwords($content);
                $sms_msg = str_replace("@@".$field."@@",$content,$sms_msg);
            }	
        }
        $field_name_footer = explode('@@', $sms_footer);	
        for($i=1; $i < count($field_name_footer); $i+=2)
        {
            if(isset($customer_data->$field_name_footer[$i]))
            { 
                $sms_footer = str_replace("@@".$field_name_footer[$i]."@@",$customer_data->$field_name_footer[$i],$sms_footer);					
            }	
        }
        $sms_msg .= " ".$sms_footer;
        return (array('message'=>$sms_msg,'mobile'=>$mobile,'serv_sms'=>$serv_sms,'serv_email'=>$serv_email,'dlt_te_id'=>$dlt_te_id));
    }
    
    
    
    
    function get_SMS_data($service_id,$serv_code, $id="")
     {
		//Declaration of variables
		$params = [];
        $template_name = "";
		$message ="";
		$sms_msg = "";
		$sms_footer = "";
		$customer_data = array();
		if($service_id ==2 || $service_id ==1 || $service_id ==4 || $service_id ==13 || $service_id ==31  ){
			$resultset = $this->db->query("SELECT
					  sa.`id_scheme_account`,
					  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,
					  IFNULL(c.firstname,' ') as fname,
					  IFNULL(c.lastname,' ') as lname,
					  a.address1,
					  a.address2,
					  a.address3,
					  c.email,
					  ct.name as city,
					  a.pincode as pincode,
					  st.name as state,
					  ctry.name as country,
					  c.mobile as mobile,
					  c.passwd as passwd,
					  sa.id_customer,
					  if(s.`scheme_type`=0,'Amount','Weight') as sch_type,
					  s.`scheme_name` as sch_name,
					  s.`code` as sch_code,
					  if(s.scheme_type=0,(concat(cs.currency_symbol,' ',s.amount)),concat('Max ', s.max_weight, 'g /month')) as payable,
					  s.`total_installments`,
					  if(cs.has_lucky_draw=1,concat(ifnull(sa.group_code,''),'',ifnull(sa.scheme_acc_number,'Not allocated')),concat(ifnull(s.code,''),' ',ifnull(sa.scheme_acc_number,'Not allocated')))as acc_no,
					  
					  sa.`account_name` as ac_name,
					  DATE_FORMAT(sa.`start_date`,'%d-%m-%Y') as start_date,
					  sa.`is_opening`,
					  IFNULL(0,sa.`paid_installments`) as paid_installments,
					 if(s.scheme_type=0 ,CONCAT(cs.currency_symbol,' ',sa.closing_balance),CONCAT(sa.closing_balance,' ',' g')) as closing_blc,
					  IFNULL(0,sa.`balance_amount`) as balance_amount,
					  IFNULL(0,sa.`balance_weight`) as balance_weight,
					  sa.`last_paid_weight`,
					  sa.`last_paid_chances`,
					  DATE_FORMAT(sa.`last_paid_date`,'%d-%m-%Y') as last_paid_date,
					  DATE_FORMAT(sa.`closing_date`,'%d-%m-%Y') as closing_date,
					  sa.`remark_open`,
					  sa.`remark_close`,
					  sa.`active` as account_status,
					  sa.`is_closed` ,
					  sa.`date_add` as created,
					  sa.`date_upd` as last_modified,
					  sa.`employee_approved`,
					  sa.`employee_closed`,
					  if(sa.`closed_by`=0,'Self',CONCAT('Representative(',sa.rep_name,')')) as closed_by,
					  cmp.`company_name` as cmp_name,cmp.comp_name_in_sms as cmp_name_sms,
					  cmp.phone as cmp_ph,cs.currency_symbol as curr_symb
				FROM ".self::ACC_TABLE." sa
				LEFT JOIN ".self::CUS_TABLE." c   ON (sa.id_customer = c.id_customer)
				LEFT JOIN ".self::ADD_TABLE." a    ON (sa.id_customer = a.id_customer)
				LEFT JOIN ".self::SCH_TABLE." s     ON (sa.id_scheme = s.id_scheme)
				LEFT JOIN country ctry ON (a.id_country = ctry.id_country)
				LEFT JOIN state st     ON (a.id_country = st.id_state)
				LEFT JOIN city ct      ON (a.id_city = ct.id_city)
				join company cmp
				join chit_settings cs
				WHERE sa.id_scheme_account = '".$id."'");
				}
				
			
				else if($service_id == 3 || $serv_code == 'SCH_CMP' || $service_id == 7  || $service_id ==14)
				{
					$resultset = $this->db->query("SELECT
					  p.id_payment,
					  sa.account_name as ac_name,
					  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,
					  c.firstname as fname,
					  IFNULL(c.lastname,' ') as lname,c.email,
					  c.mobile,
					if(cs.has_lucky_draw=1,concat(ifnull(sa.group_code,''),'',ifnull(sa.scheme_acc_number,'Not allocated')),concat(ifnull(s.code,''),' ',ifnull(sa.scheme_acc_number,'Not allocated')))as acc_no,
					s.code as sch_code,s.scheme_name as sch_name,
					  p.id_employee,
                      if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee, 
					  if(s.scheme_type=1,'Weight','Amount') as scheme_type,
					  if(p.payment_type='Manual',CONCAT(cs.currency_symbol,' ',IFNULL(p.payment_amount,'-')),CONCAT(cs.currency_symbol,' ',IFNULL(p.act_amount,'-'))) as pay_amt,
					  if(p.payment_type='Manual',CONCAT(cs.currency_symbol,' ',IFNULL(p.act_amount,'-')),CONCAT(cs.currency_symbol,' ',IFNULL(p.act_amount,'-'))) as act_amt,
					  p.metal_rate,
					   IFNULL(CONCAT(p.metal_weight,' ','g'), '-') as metal_weight,
					  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_pay,
			          p.payment_type,
					  p.payment_mode as pay_mode,
					  CONCAT(cs.currency_symbol,' ',IFNULL(p.add_charges,'0.00')) as charges,
					  IFNULL(Date_format(p.cheque_date,'%d-%m%-%Y'),'-') as chq_date,
					  IFNULL(p.cheque_no,'-') as chq_no,
					  IFNULL(p.bank_acc_no,'-') as bank_acc_no,
					  IFNULL(p.bank_name,'-')as bank_name,
					  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
					  IFNULL(p.bank_branch,'-') as bank_branch,
					  IFNULL(p.id_transaction,'-') as txn_id,
					  IFNULL(p.payu_id,'-') as payu_id ,
					  IFNULL(p.card_no,'-') as card_no,
					  psm.payment_status as status,
					  p.payment_status as pay_status,p.no_of_dues as dues,
					  psm.color as status_color,
					  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
					  IFNULL(p.remark,'-') as remark,
					  cmp.company_name as cmp_name,cmp.comp_name_in_sms as cmp_name_sms,
					  cmp.phone as cmp_ph,
					   IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 and s.payment_chances=1) , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as paid_installments,
					  IFNULL((select sum(payment_amount) from payment where id_scheme_account=p.id_scheme_account and payment_status=1),0) as total_amount,
					  IFNULL((select concat(IFNULL(sum(metal_weight),0),' g') from payment where id_scheme_account=p.id_scheme_account and payment_status=1),0) as tot_weight
				FROM payment p
				left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
				Left Join employee e On (e.id_employee=p.id_employee)
				Left Join customer c on (sa.id_customer=c.id_customer)
				left join scheme s on(sa.id_scheme=s.id_scheme)
			    Left Join payment_mode pm on (p.payment_mode=pm.id_mode)		
			    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg) 
			    join company cmp
			    join chit_settings cs
			    Where p.id_payment='".$id."'");
			    //print_r($this->db->last_query());exit;
				}
				else if($service_id == 6  || $service_id == 5 )
				{
					$resultset = $this->db->query("SELECT
					  p.id_post_payment,
					  sa.account_name as ac_name,
					  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,
					  c.firstname as fname,
					  IFNULL(c.lastname,' ') as lname,c.email,
					  c.mobile,
					 if(cs.has_lucky_draw=1,concat(ifnull(sa.group_code,''),'',ifnull(sa.scheme_acc_number,'Not allocated')),concat(ifnull(s.code,''),' ',ifnull(sa.scheme_acc_number,'Not allocated')))as acc_no,
					  s.code as sch_code,
					  p.id_employee,
                      if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee, 
					  if(s.scheme_type=1,'Weight','Amount') as scheme_type,
					  CONCAT(cs.currency_symbol,' ',IFNULL(p.amount,'-')) as pay_amt,
					  p.metal_rate,
					  IFNULL(CONCAT(p.weight,' ','g'), '-') as metal_weight,
					  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_pay,
					  IFNULL(Date_format(p.date_presented,'%d-%m%-%Y'),'-') as date_presented,
					  IFNULL(Date_format(p.date_add,'%d-%m%-%Y'),'-') as date_add,
					  p.pay_mode as pay_mode,p.id_drawee,
					  CONCAT(cs.currency_symbol,' ',IFNULL(p.charges,'0.00')) as charges,
					  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as chq_date,
					  IFNULL(p.cheque_no,'-') as chq_no,
					  IFNULL(p.payee_acc_no,'-') as bank_acc_no,
					  IFNULL(p.payee_bank,'-')as bank_name,
					  IFNULL(p.payee_ifsc,'-') as bank_IFSC,
					  IFNULL(p.payee_branch,'-') as bank_branch,
					  psm.payment_status as status,
					  psm.color as status_color,
					  cmp.company_name as cmp_name,cmp.comp_name_in_sms as cmp_name_sms,
					  cmp.phone as cmp_ph
				FROM postdate_payment p
				left join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account)
				Left Join employee e On (e.id_employee=p.id_employee)
				Left Join customer c on (sa.id_customer=c.id_customer)
				left join scheme s on(sa.id_scheme=s.id_scheme)
			    Left Join payment_mode pm on (p.pay_mode=pm.id_mode)		
			    Left Join payment_status_message psm On (p.payment_status=psm.id_status_msg) 
			    join company cmp
			    join chit_settings cs
			    Where p.id_post_payment='".$id."'");
				}
				else if($service_id == 8 || $service_id == 9 )
				{
					$resultset = $this->db->query("Select
								  wa.id_wallet_account as id_wlt_acc,
								  c.id_customer, wt.id_wallet_transaction,
								  Concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')) as cus_name,
								  c.firstname as fname,
								  IFNULL(c.lastname,' ') as lname,c.email,
								  c.mobile,
								  Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_name,
								  wa.wallet_acc_number as wlt_acc_no,
								  Date_Format(wa.issued_date,'%d-%m-%Y') as issued_date,
								  wa.remark,
								  wa.active,
								  SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) as  issues,
								  SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END) as redeem,
								  (SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) -   SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END)) as blc,
								   cmp.company_name as cmp_name,cmp.comp_name_in_sms as cmp_name_sms,
								  cmp.phone as cmp_ph
							From wallet_account wa
								Left Join customer c on (wa.id_customer=c.id_customer)
								Left Join employee e on (wa.id_employee=e.id_employee)
								Left Join wallet_transaction wt on (wa.id_wallet_account=wt.id_wallet_account)
								join company cmp
			 					join chit_settings cs 
			 					Where wa.id_wallet_account='".$id."'");
					
				}
				
				else if($service_id == 11)
					{
						$resultset=$this->db->query("Select
					   c.id_customer, if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,
								  IFNULL(c.firstname,' ') as fname,
								  IFNULL(c.lastname,' ') as lname,
								  c.date_of_birth,c.date_of_wed,
					   a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,c.phone,c.mobile,c.email,c.nominee_name,c.nominee_relationship,c.nominee_mobile,cmp.website,c.cus_img,c.pan,c.pan_proof,c.voterid,c.voterid_proof,c.rationcard,c.rationcard_proof,a.id_country,a.id_city,a.id_state,c.id_employee,c.mobile as userId,
			   	c.comments,c.username,from_base64(c.passwd) as passwd,c.is_new,c.active,c.`date_add`,c.`date_upd`, cmp.`company_name` as cmp_name,
								  cmp.phone as cmp_ph,cmp.comp_name_in_sms as cmp_name_sms
						From
						  customer c
						left join address a on(c.id_customer=a.id_customer)
						left join country cy on (a.id_country=cy.id_country)
						left join state s on (a.id_state=s.id_state)
						left join city ct on (a.id_city=ct.id_city)
						join company cmp
						join chit_settings cs
						where c.mobile=".$id);
					}else if($service_id == 16){
											
						  $resultset=$this->db->query("SELECT if(sa.is_refferal_by=0 && chit.cusplan_type=1,if(ws.type=0,ws.value,((s.amount*ws.value)/100)),
				            if(sa.is_refferal_by=0 && s.cus_refferal=1 && chit.cusplan_type=0,s.cus_refferal_value,if(sa.is_refferal_by=1 && chit.empplan_type=1,if(ws.type=0,ws.value,((s.amount*ws.value)/100)),
				              if(sa.is_refferal_by=1 && s.emp_refferal=1 && chit.empplan_type=0,s.emp_refferal_value,'')))) as amount,
							ref.mobile,ref.firstname as fname,comp.company_name as cmp_name,comp.comp_name_in_sms as cmp_name_sms			
							FROM scheme_account sa
							left join scheme s on (sa.id_scheme =s.id_scheme)
							left join customer c on (sa.id_customer=c.id_customer)
							left join(SELECT w.id_customer,w.id_wallet_account,
                            if(c.id_customer is not null ,Concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')),
							if(w.idemployee is not null,Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')),'')) as firstname,
                            if(c.id_customer is not null ,c.mobile,if(w.idemployee is not null,e.mobile,'')) as mobile
							FROM wallet_account w  
							left join customer c on (w.id_customer= c.id_customer )
                            left join employee e on (w.idemployee=e.id_employee)         
                             where  w.active=1
							) ref on ref.mobile= sa.referal_code					
							join wallet_settings ws
							join chit_settings chit
							join company comp
							where sa.id_scheme_account=".$id." and ws.active=1 and (ws.id_wallet=1 || ws.id_wallet=2)");	
					}
					
				else
				{
					$resultset = $this->db->query(" Select  c.id_company,c.company_name as cmp_name,c.comp_name_in_sms,c.short_code,c.pincode,c.mobile,c.phone as cmp_ph,c.email,c.website,c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,cy.name as country,cs.currency_symbol,cs.currency_name
				from company c
					join chit_settings cs
					left join country cy on (c.id_country=cy.id_country)
					left join state s on (c.id_state=s.id_state)
					left join city ct on (c.id_city=ct.id_city) ");
				}
	//echo this->db->last_query();exit;
				foreach($resultset->result() as $row)
				{
					$customer_data = $row;
					$mobile=$row->mobile;
				}
				
				$resultset = $this->db->query("SELECT sms_msg, sms_footer,serv_sms,serv_email,serv_whatsapp, template_name, dlt_te_id from services where id_services = '".$service_id."'");
				foreach($resultset->result() as $row)
				{
    				$serv_sms = $row->serv_sms;
    				$serv_email = $row->serv_email;
					$sms_msg = $row->sms_msg;
					$sms_footer = $row->sms_footer;
					$serv_whatsapp = $row->serv_whatsapp;
					$template_name = $row->template_name;
					$dlt_te_id =  $row->dlt_te_id;
				}
			$resultset->free_result();
			
			//Generating Message content
			$field_name = explode('@@', $sms_msg);
			
			for($i=1; $i < count($field_name); $i+=2) 
        	{
        		$field =  $field_name[$i];
        		if(isset($customer_data->$field))
        		{ 
        		   $params[] = [ "type" => "text", "text" => $customer_data->$field ];
        		$sms_msg = str_replace("@@".$field."@@",$customer_data->$field,$sms_msg);
        		}	
        	}
			$field_name_footer = explode('@@', $sms_footer);	
			for($i=1; $i < count($field_name_footer); $i+=2)
			 {
				if(isset($customer_data->$field_name_footer[$i]))
				 { 
					$sms_footer = str_replace("@@".$field_name_footer[$i]."@@",$customer_data->$field_name_footer[$i],$sms_footer);					
				}	
			}
			$sms_msg .= " ".$sms_footer;
			
	return (array('message'=>$sms_msg,'mobile'=>$mobile,'serv_email'=>$serv_email,'serv_sms'=>$serv_sms,'serv_whatsapp'=>$serv_whatsapp,"params" => $params, "template_name" => $template_name, "dlt_te_id" => $dlt_te_id));
	}
	
	public function update_sms_status($data,$id)
    {    	
    	$edit_flag=0;
    	$this->db->where('id_services',$id); 
		$serv_info=$this->db->update(self::SER_TABLE,$data);		
		return $serv_info;
	}

public function notification_on_off($data)
    {    	
    	$edit_flag=0; 
		$noti_on_off=$this->db->update("chit_settings",$data);	 
		return $noti_on_off;
	}

public function update_notification_status($data,$id)
    {    	
    	$edit_flag=0;
    	$this->db->where('id_notification',$id); 
		$noti_info=$this->db->update(self::NOTI_TABLE,$data);		
		return $noti_info;
	}
	
// Promotion sms and otp setting
	
	public function sms_info()
	{
		   $sql=$this->db->query('SELECT s.id_sms_api, s.sms_sender_id, s.sms_url FROM sms_api_settings s'); 
		   return $sql->row_array();
	}
	public function promotion_info()
	{
		   $sql=$this->db->query('SELECT * FROM promotion_api_settings p'); 
		   return $sql->row_array();
	}
	public function promotion_smsavilable()
	{
		   $sql=$this->db->query('SELECT p.debit_promotion FROM promotion_api_settings p'); 
		   return $sql->row_array();
	}
	public function otp_smsavilable()
	{
		   $sql=$this->db->query('SELECT s.debit_sms FROM sms_api_settings s'); 
		   return $sql->row_array();
	} 
	
//Promotion sms and otp setting
	
	
	public function delete_service($id)
	{
		$this->db->where('id_services',$id);  
		$status=$this->db->delete(self::SER_TABLE);
		return $status;
	}	
	
	public function delete_notification($id)
	{
		$this->db->where('id_notification',$id);  
		$status=$this->db->delete(self::NOTI_TABLE);
		return $status;
	}	
	
	function checkService($serviceID)

	{

		$email = 0;

		$sms   = 0;

		$query = $this->db->get_where('services',array('id_services' => $serviceID));

		if($query->num_rows() > 0)

		{

			$row = $query->row();

			$email = $row->serv_email;

			$sms   = $row->serv_sms;

		}

		$data = array("email" => $email, "sms" => $sms);

		return $data;

	}
	
	function get_scheme_name()
	{	
		$sql = $this->db->query("SELECT s.scheme_name as sch_name  FROM scheme s");
		
		
		$sch_name = array_map(function ($value) {
					return  $value['sch_name'];
					}, $sql->result_array());
		
		return $sch_name;
			
	}
	
	function get_duecustomer($filterBy)
	{	 
	 $result=0;	
		$sql="Select
				sa.id_customer,scheme_acc_number as acc_no,s.scheme_name,
				 if(s.scheme_type=1,CONCAT(s.max_weight,' gm'), CONCAT('INR ',s.amount))as payable,
				 if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight','Amount to Weight'))as scheme_type,c.mobile
					From scheme_account sa
						JOIN chit_settings cs
							Left Join
								(Select
								sa.id_scheme_account,
							CASE
								 WHEN sa.is_opening='1' AND p.date_payment is null THEN Date_add(sa.last_paid_date,Interval 1 month)
								 when p.date_payment is null and sa.is_opening='0' then sa.date_add
							ELSE Date_add(max(p.date_payment),Interval 1 month)
						END AS next_due
							From scheme_account sa
								Left Join payment p on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0 and p.payment_status='1')
								Group By sa.id_scheme_account)d on(d.id_scheme_account=sa.id_scheme_account)
								LEFT JOIN payment p ON (sa.id_scheme_account = p.id_scheme_account AND p.payment_status = 1)
								LEFT JOIN customer c ON (sa.id_customer = c.id_customer)
								LEFT JOIN scheme s ON (sa.id_scheme = s.id_scheme)";							
								
	switch($filterBy)
	{

					case 'MS': //-7three day

					 $sql=$sql." where DATE_FORMAT(d.next_due, '%m-%d') = DATE_FORMAT(CURDATE()-7, '%m-%d')and s.active =1";   

								break;
								
					case 'MT': //-3sthree day

						 $sql=$sql."  where DATE_FORMAT(d.next_due, '%m-%d') = DATE_FORMAT(CURDATE()-3, '%m-%d')and s.active =1";   

					break;
								
					case 'Y': //yesterday

						 $sql=$sql."  where DATE_FORMAT(d.next_due, '%m-%d') = DATE_FORMAT(CURDATE()-1, '%m-%d')and s.active =1";   

					break;												
								
								

				case 'T': //Today

						 $sql=$sql."  where DATE_FORMAT(d.next_due, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')";   

					break;

				case 'PT': //+3three day

						  $sql=$sql." where DATE_FORMAT(d.next_due, '%m-%d') = DATE_FORMAT(CURDATE()+3, '%m-%d')and s.active =1";   

					break;

				case 'PS': //+7three day

						  $sql=$sql."  where DATE_FORMAT(d.next_due, '%m-%d') = DATE_FORMAT(CURDATE()+7, '%m-%d') and s.active =1 ";   

					break;	
																	
						}
											
							
			 $result=$this->db->query($sql);
			return $result->result_array();
			
			}
			
	function getnotificationtext($sch_data)
	{
	
		$resultset = $this->db->query("SELECT noti_msg FROM notification  where id_notification='7'");
		foreach($resultset->result() as $row)
			{
				$sms_msg = $row->noti_msg;		
				
			}
		$resultset->free_result();	
		
		$field_name = explode('@@', $sms_msg);
		
				for($i=1; $i < count($field_name); $i+=2) 
				{
					
						if(isset($sch_data)) 
						{
						    $field =  $field_name[$i];
							$sms_msg = str_replace("@@".$field."@@",($field=='sch_name'?$sch_data['sch_name']:$sch_data['payable']),$sms_msg);	
																		
						}					
					
						
				}			
													
						
	
		return $sms_msg;
	}
	function getnotification_id($id)
	{
		 $sql = $this->db->query("SELECT r.token as token 
									from registered_devices r								
									where r.id_customer =".$id."");
		
		$token = array_map(function ($value) {
					return  $value['token'];
					}, $sql->result_array());
		
		return $token;
		
	} 
	function get_notiData($id_notification)
     {
		//Declaration of variables
		$message ="";
		$noti_msg = "";
		$noti_footer = "";
		$customer_data = array();
		
		
				if($id_notification == 1)
				{	
					
					$rate_token = $this->db->query("SELECT r.token as token,c.mobile from registered_devices r LEFT JOIN customer c on (c.id_customer=r.id_customer) where c.notification = 1");					
					$token = array_column($rate_token->result_array(),'token');
					
					$resultset = $this->db->query("SELECT goldrate_22ct as rate, silverrate_1gm as silver, DATE_FORMAT(updatetime,'%H:%i:%s') as time FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1");
				}
				else if($id_notification == 5)
				{
					$birthday_token = $this->db->query("SELECT r.token as token from registered_devices r LEFT JOIN customer c on (c.id_customer=r.id_customer) where c.notification = 1");					
					$token = array_column($birthday_token->result_array(),'token');
					
					$sql = $this->db->query("SELECT r.token as token from registered_devices r LEFT JOIN customer c on (c.id_customer=r.id_customer) where c.notification = 1;");
				}
				else if($id_notification == 6)
				{
					
				}
				foreach($resultset->result() as $row)
				{
					$customer_data = $row;
				}
				
			$resultset = $this->db->query("SELECT noti_name,noti_name, noti_footer,noti_msg from notification where id_notification = '".$id_notification."'");
				foreach($resultset->result() as $row)
				{
					$noti_msg = $row->noti_msg;
					$noti_footer = $row->noti_footer;
					$noti_header=$row->noti_name;
				}
			$resultset->free_result();
			
			//Generating Message content
			$field_name = explode('@@', $noti_msg);	
					
			for($i=1; $i < count($field_name); $i+=2) 
			{	
			    $field =  $field_name[$i];
				if(isset($customer_data->$field)) 
				{
					$noti_msg = str_replace("@@".$field."@@",$customer_data->$field,$noti_msg);					
				}	
			}
			$field_name_footer = explode('@@', $noti_footer);	
			for($i=1; $i < count($field_name_footer); $i+=2)
			 {
				if(isset($customer_data->$field_name_footer[$i]))
				 { 
					$noti_footer = str_replace("@@".$field_name_footer[$i]."@@",$customer_data->$field_name_footer[$i],$noti_footer);					
				}	
			}
		
	return (array('message'=>$noti_msg,'token'=>$token,'header'=>$noti_header,'footer'=>$noti_footer));
		}
		
	function get_cusnotiData($id_notification)
     {
		//Declaration of variables
		$message ="";
		$noti_msg = "";
		$noti_footer = "";
		$msg = "";
		$customer_data = array();
		$data = array();
		 
		/*if($id_notification == 4)//Due Unpaid for current month till date
			{
			  $resultset = $this->db->query("Select
          sa.id_scheme_account,
					c.id_customer,r.uuid,c.mobile,
					CONCAT(s.code,'-',IFNULL(sa.scheme_acc_number,'Not Allocated')) as ac_no,s.noti_msg,sa.id_scheme,
					if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
					s.scheme_name as sch_name,
					IF(s.scheme_type=0,s.amount,IF(s.scheme_type=1,s.max_weight,if(cs.firstPayamt_as_payamt=1 ,sa.firstPayment_amt,concat('max',s.max_amount)))) as payable,
					IFNULL(if(Date_Format(max(p.date_add),'%Y%m') = Date_Format(curdate(),'%Y%m'), SUM(p.no_of_dues),IF(sa.is_opening=1, if(Date_Format(sa.last_paid_date,'%Y%m') = Date_Format(curdate(),'%Y%m'), 1,0),0)),0) as currentpaycount,
					count(pp.id_scheme_account) as cur_month_pdc,
					cs.currency_symbol
				From scheme_account sa
				Left Join scheme s On (sa.id_scheme=s.id_scheme)
				Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=2 or p.payment_status=1))
				Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)
        LEFT JOIN registered_devices r ON (r.id_customer = c.id_customer)
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
				 Left Join postdate_payment pp On (sa.id_scheme_account=pp.id_scheme_account and (pp.payment_status=2 or pp.payment_status=7) and (Date_Format(pp.date_payment,'%Y%m')=Date_Format(curdate(),'%Y%m')))	
				 join chit_settings cs
				Where s.one_time_premium = 0 and sa.active=1 and sa.is_closed = 0 and c.notification =1 and cs.allow_notification=1 and r.token is not null
				Group By sa.id_scheme_account");	
			//$data = $resultset->result_array();
			//print_r($this->db->last_query());exit;
				if($resultset->num_rows() > 0)
				{
					foreach($resultset->result() as $row)
					{
						if($row->currentpaycount <= 0 && $row->cur_month_pdc <= 0){
							$data[] = array(
							           
									   'id_scheme_account'  => $row->id_scheme_account,
									   'ac_no'				=> $row->ac_no, 
									    'payable'			=> $row->payable,
									   'id_customer'		=> $row->id_customer,
									   'token'				=> $row->uuid,
									   'sch_name'			=> $row->sch_name,
									   'mobile'				=> $row->mobile,
									   'name'				=> $row->name,
									   'currency_symbol'		=> $row->currency_symbol,
									   'noti_msg'           =>$row->noti_msg,
									   'id_scheme'           =>$row->id_scheme
									);
						}
						
					}
				}
			}*/
			 if($id_notification == 4)//Due Unpaid for current month till date
			{
			  $resultset = $this->db->query("Select

          sa.id_scheme_account,
					c.id_customer,r.uuid,c.mobile,
					CONCAT(s.code,'-',IFNULL(sa.scheme_acc_number,'Not Allocated')) as ac_no,s.noti_msg,sa.id_scheme,
					if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
					s.scheme_name as sch_name,
					IF(s.scheme_type=0,s.amount,IF(s.scheme_type=1,CONCAT(s.max_weight,'g /month'),if(s.firstPayamt_as_payamt=1 ,sa.firstPayment_amt,s.amount))) as payable,
					
	
					IFNULL(if(Date_Format(max(p.date_add),'%Y%m') = Date_Format(curdate(),'%Y%m'), SUM(p.no_of_dues),IF(sa.is_opening=1, if(Date_Format(sa.last_paid_date,'%Y%m') = Date_Format(curdate(),'%Y%m'), 1,0),0)),0) as currentpaycount,
					count(pp.id_scheme_account) as cur_month_pdc,
					cs.currency_symbol

				From scheme_account sa

				Left Join scheme s On (sa.id_scheme=s.id_scheme)

				Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=2 or p.payment_status=1))

				Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)

        LEFT JOIN registered_devices r ON (r.id_customer = c.id_customer)

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

				 Left Join postdate_payment pp On (sa.id_scheme_account=pp.id_scheme_account and (pp.payment_status=2 or pp.payment_status=7) and (Date_Format(pp.date_payment,'%Y%m')=Date_Format(curdate(),'%Y%m')))	

				 join chit_settings cs

				Where sa.active=1 and sa.is_closed = 0 and c.notification =1 and cs.allow_notification=1 and r.token is not null

				Group By sa.id_scheme_account");	
			//$data = $resultset->result_array();
			//print_r($this->db->last_query());exit;
				if($resultset->num_rows() > 0)
				{
					foreach($resultset->result() as $row)
					{
						if($row->currentpaycount <= 0 && $row->cur_month_pdc <= 0){
							$data[] = array(
							           
									   'id_scheme_account'  => $row->id_scheme_account,
									   'ac_no'				=> $row->ac_no, 
									    'payable'			=> $row->payable,
									   'id_customer'		=> $row->id_customer,
									   'token'				=> $row->uuid,
									   'sch_name'			=> $row->sch_name,
									   'mobile'				=> $row->mobile,
									   'name'				=> $row->name,
									   'currency_symbol'		=> $row->currency_symbol,
									   'noti_msg'           =>$row->noti_msg,
									   'id_scheme'           =>$row->id_scheme,
									   'currentpaycount'    => $row->currentpaycount
									);
						}
						
					}
				}
			}
			 else if($id_notification == 7)
			{
					/* $resultset = $this->db->query("SELECT cmp.company_name as cmp_name,cmp.short_code as cmp_code,c.firstname,CONCAT(c.firstname,' ',c.lastname) as cus_name,c.mobile,r.token as token from registered_devices r LEFT JOIN customer c on (c.id_customer=r.id_customer) join company cmp where c.notification = 1 and date_of_birth=curdate() and r.token is not null");					
					$data = $resultset->result_array(); */
					
					$resultset=$this->db->query("SELECT cmp.company_name as cmp_name,cmp.short_code as cmp_code,c.firstname,re.token as token,CONCAT(c.firstname,' ',c.lastname) as cus_name,c.mobile from customer c join company cmp left join (SELECT reg.token, reg.id_customer FROM registered_devices reg left join customer cus on cus.id_customer=reg.id_customer where cus.notification = 1 and cus.date_of_birth=curdate() group by cus.id_customer) re On  re.id_customer=c.id_customer where c.notification = 1 and c.date_of_birth=curdate() group by c.id_customer");
				    $data=$resultset->result_array();
					
					
					
			}
			else if($id_notification == 1){
				$resultset = $this->db->query("SELECT cmp.company_name as cmp_name,cmp.short_code as cmp_cod,silverrate_1gm as silver, goldrate_24ct as goldRate24,goldrate_18ct as goldRate18,platinum_1g as platinum,goldrate_22ct as rate,mjdmagoldrate_22ct as mjdmarate,mjdmasilverrate_1gm as mjdmasilver, Date_format(updatetime,'%d-%m%-%Y %h:%i %p') as time
				FROM metal_rates 
				join company cmp
				ORDER BY id_metalrates DESC LIMIT 1");					
					 $data = $resultset->result_array();
			}
			$resultset = $this->db->query("SELECT noti_name,noti_name, noti_footer,noti_msg from notification where id_notification = '".$id_notification."'");
				foreach($resultset->result() as $row)
				{
					$noti_msg = $row->noti_msg;
					$noti_footer = $row->noti_footer;
					$noti_header=$row->noti_name;
				}
			$resultset->free_result();
			
			$field_name_footer = explode('@@', $noti_footer);	
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
    			//For notification content
    			
    			if($this->config->item('due_noti_content')==1)
    			{
    			    	$msgContent =$noti_msg;
    			}else{
    			    $msgContent =$row['noti_msg'];
    			}
    			for($i=1; $i < count($field_name); $i+=2) 
    			{	
    			    
    			   
    			    $field =  $field_name[$i];
    			     
    				if(isset($customer_data[$field])) 
    				{
    					$msgContent = str_replace("@@".$field."@@",$customer_data[$field],$msgContent);	
    					
    					$data[$a]['message']=$msgContent;
    				}
    			}	
    			unset($msgContent);
    			$a++;
    		
    		}
    //	echo"<pre>";print_r($data);exit;
	 return (array('data'=>$data,'header'=>$noti_header,'footer'=>$noti_footer));
	}
	
	
	function getnotiData($id_notification)
     {
		//Declaration of variables
		$message ="";
		$noti_msg = "";
		$noti_footer = "";
		$msg = "";
		$customer_data = array();	
				
			
				$resultset = $this->db->query("SELECT cmp.company_name as cmp_name,cmp.short_code as cmp_code,c.firstname,CONCAT(c.firstname,' ',c.lastname) as cus_name,c.mobile,r.token as token ,
				(SELECT silverrate_1gm as silverrate_1gm FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1) as silver,
				(SELECT goldrate_22ct as rate FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1) as rate,
				(SELECT updatetime as updatetime FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1) as time
				from registered_devices r 
				LEFT JOIN customer c on (c.id_customer=r.id_customer) 
				join company cmp
				 where c.notification = 1  and r.token is not null");					
				$data = $resultset->result_array();
				
				$minRate = $this->db->query("SELECT min(goldrate_22ct) as min_rate,updatetime FROM metal_rates");					
				$mRate = $this->db->query("SELECT min(goldrate_22ct) as min_rate FROM metal_rates");					
				$data = $resultset->result_array();
				
				
				$resultset = $this->db->query("SELECT noti_name,noti_name, noti_footer,noti_msg from notification where id_notification = '".$id_notification."'");
				foreach($resultset->result() as $row)
				{
					$noti_msg = $row->noti_msg;
					$noti_footer = $row->noti_footer;
					$noti_header=$row->noti_name;
				}
				$resultset->free_result();
				
				$field_name_footer = explode('@@', $noti_footer);	
				for($i=1; $i < count($field_name_footer); $i+=2)
				 {
					if(isset($customer_data->$field_name_footer[$i]))
					 { 
						$noti_footer = str_replace("@@".$field_name_footer[$i]."@@",$customer_data->$field_name_footer[$i],$noti_footer);					
					}	
				}
				
				
						
	 return (array('data'=>$data,'header'=>$noti_header,'footer'=>$noti_footer,'message'=>$noti_msg));
	}
	
		
	public function check_noti_settings()
	{
		  $sql="Select * From chit_settings Where id_chit_settings=1";
		// print_r($sql);exit;
		  $data = $this->db->query($sql);
		return $data->row()->allow_notification;
	}
	
	public function get_noti_settings($noti_id)
	{
		  $sql="Select send_notif_on,send_daily_from From notification  Where id_notification=".$noti_id;
		  $data = $this->db->query($sql);
		return $data->row_array();
	}
	
	// to get lowest rate content
	function get_noticontent($sch_data)
	{
	
		$resultset = $this->db->query("SELECT noti_msg FROM notification  where id_notification=".$sch_data['notification_service']);
		foreach($resultset->result() as $row)
			{
				
				$sms_msg = $row->noti_msg;		
				
			}
		$resultset->free_result();	
		
		$field_name = explode('@@', $sms_msg);
		
				for($i=1; $i < count($field_name); $i+=2) 
				{
					
						if(isset($sch_data)) 
						{
						    $field =  $field_name[$i];
							$sms_msg = str_replace("@@".$field."@@",($field=='tgoldrate_22ct'?$sch_data['tgoldrate_22ct']:$sch_data['ygoldrate_22ct']),$sms_msg);	
																		
						}
				}	
		return $sms_msg;
	}
	
	function metalrate_gold($filterBy)
	{	 
	 $result=0;	
	 
	//	$sql="";							
								
	switch($filterBy)
	{

					
					case 'T': //today
						 $sql="SELECT updatetime,goldrate_22ct as goldrate_22ct FROM metal_rates WHERE Date(`updatetime`) = CURDATE()";   

					break;
					case 'Y': //yesterday
						 $sql="SELECT updatetime,min(goldrate_22ct) as goldrate_22ct FROM metal_rates WHERE Date(`updatetime`) = CURDATE() - INTERVAL 1 DAY";   

					break;
					
					case 'TM': //this month
						  $sql="SELECT updatetime,min(goldrate_22ct) as goldrate_22ct FROM metal_rates  WHERE month(`updatetime`) = month(CURDATE())";   
					break;	
					case 'ALL': // all month

						 $sql="SELECT updatetime,min(goldrate_22ct) as goldrate_22ct FROM metal_rates";

					break;											
		}			
			     $result=$this->db->query($sql);
			//  echo $sql;echo "<br/>";
			//   print_r($result->row_array());echo "<br/>";
			 
			return $result->row_array();
			
	}
		function get_noti($sch_data)
	{
		$sql="SELECT n.id_notification, n.noti_name,n.noti_footer FROM notification n where n.id_notification=".$sch_data;
	
		$result=$this->db->query($sql);
			return $result->row_array();
			
	}
	
	function getDevicetokens()
	{
		$sql = $this->db->query("SELECT r.token as token ,c.mobile
									from registered_devices r 
									LEFT JOIN customer c on (c.id_customer=r.id_customer) 
									where c.notification = 1;");
		
		$token = array_map(function ($value) {
					return  $value['token'];
					}, $sql->result_array());
					$data =$sql->result_array();
		
		return $data;
	}
	
	
 /* select bassed to group_message  */
	
// all customer group_message
	 function get_allcustomersms_list()
	{
		$sql = $this->db->query("SELECT c.id_customer, c.mobile, c.email  FROM customer c where c.active=1");
		
		$mobile = array_map(function ($value) {
					return  $value['mobile'];
					}, $sql->result_array());
		
		return $mobile;
		
		
	} 
// selected customer group_message
	function get_selectcustomersms_data(){
		
		$resultset = $this->db->query("SELECT IF(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,c.id_customer, c.mobile, c.email FROM customer c where c.active=1 ".($id_branch!='' ? " and c.id_branch=".$id_branch."" :'')." ");
		$data = $resultset->result_array();
		return $data;
	}
	
	
	


// all customer group_mail

function get_allcustomeremail_list()
	{
		$sql = $this->db->query("SELECT c.id_customer, c.mobile, c.email  FROM customer c where  c.active=1");
		
		$email = array_map(function ($value) {
					return  $value['email'];
					}, $sql->result_array());
		
		return $email;
		
		
	}
// selected customer group_message

/* selected bassed to group_message  */



	function get_metalnotiContent($id_notification)
	     {
			//Declaration of variables
			$message ="";
			$noti_msg = "";
			$noti_footer = "";
			$msg = "";
			$customer_data = array();
			
				 if($id_notification == 1){
					$resultset = $this->db->query("SELECT cmp.company_name as cmp_name,cmp.short_code as cmp_code,  
					(SELECT updatetime FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1) as time,
					(SELECT goldrate_22ct as rate FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1) as rate,
					(SELECT mjdmagoldrate_22ct as mjdmarate FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1) as mjdmarate,
					(SELECT silverrate_1gm as silver FROM metal_rates ORDER BY id_metalrates DESC LIMIT 1) as silver
					from company cmp"); 				
						$data = $resultset->result_array();
				}
					
				$resultset = $this->db->query("SELECT noti_name,noti_name, noti_footer,noti_msg from notification where id_notification = '".$id_notification."'");
					foreach($resultset->result() as $row)
					{
						$noti_msg = $row->noti_msg;
						$noti_footer = $row->noti_footer;
						$noti_header=$row->noti_name;
					}
				$resultset->free_result();
				
				$field_name_footer = explode('@@', $noti_footer);	
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
					    $field =  $field_name[$i];
						if(isset($customer_data[$field])) 
						{
							$msgContent = str_replace("@@".$field."@@",$customer_data[$field],$msgContent);					
							$data[$a]['message']=$msgContent;
						}
					}	
					unset($msgContent);
					$a++;
				}
			 return (array('data'=>$data,'header'=>$noti_header,'footer'=>$noti_footer));
		}
		
		function insert_sent_notification($data){ 
		  
		    $insDta = array('noti_service'=> isset($data['notification_service'])?$data['notification_service']:0,
							'noti_title'  => isset($data['header'])?$data['header']:NULL,
							'noti_content'=> isset($data['message'])?$data['message']:NULL,  						
							'noti_img'    => isset($data['noti_img']) ?$data['noti_img']:NULL ,
							'id_customer' => isset($data['id_customer']) ?$data['id_customer']:0 ,
							'targetUrl' => isset($data['targetUrl']) ?$data['targetUrl']:NULL ,
							'id_branch'     =>  (isset($data['id_branch'])?$data['id_branch']: NULL),
							'date_add'    => date('Y-m-d H:i:s')
					        );
		    $result = $this->db->insert('sent_notifications', $insDta);	
		     //print_r($this->db->last_query());exit;
		    return $result;
		}
		
/*	function get_services($id)
   	 {
		 $sql = "Select * from notification where id_notification=".$id."";
		return $group=$this->db->query($sql)->row_array();
		}*/

	 // send due sms by service call //HH  
	public function get_sms_settings($id_services)
	{
		  $sql="Select send_sms_on,dlt_te_id,send_daily_from From services  Where id_services=".$id_services;
		 // print_r($sql);exit;
		  $data = $this->db->query($sql);
		return $data->row_array();
	}
	
	function get_SMS_due($service_id)     //based on scheme master settings- allow_due_alert=1 OR allow_due_alert=0 to send the due alert sms for cus //HH
	 {
	     
	   $message ="";
		$sms_msg = "";
		$sms_footer = "";
		$msg = "";
		$customer_data = array();
		$data = array();
		if($service_id == 22){     
			  $resultset = $this->db->query("Select

          sa.id_scheme_account,
					c.id_customer,c.mobile,ser.sms_msg as sms_msg,ser.serv_email,ser.serv_sms,ser.sms_footer,ser.id_services,
					CONCAT(s.code,'-',IFNULL(sa.scheme_acc_number,'Not Allocated')) as ac_no,s.allow_due_alert,
					if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
					s.scheme_name as sch_name,
					IF(s.scheme_type=1,CONCAT(s.max_weight,'g /month'),IF(s.scheme_type=0,s.amount,'')) as payable,
	               
					IFNULL(if(Date_Format(max(p.date_add),'%Y%m') = Date_Format(curdate(),'%Y%m'), SUM(p.no_of_dues),IF(sa.is_opening=1, if(Date_Format(sa.last_paid_date,'%Y%m') = Date_Format(curdate(),'%Y%m'), 1,0),0)),0) as currentpaycount,
					count(pp.id_scheme_account) as cur_month_pdc,cmp.comp_name_in_sms as cmp_name_sms,
					cs.currency_symbol

				From scheme_account sa

				Left Join scheme s On (sa.id_scheme=s.id_scheme)

				Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=2 or p.payment_status=1))

				Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)

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

				 Left Join postdate_payment pp On (sa.id_scheme_account=pp.id_scheme_account and (pp.payment_status=2 or pp.payment_status=7) and (Date_Format(pp.date_payment,'%Y%m')=Date_Format(curdate(),'%Y%m')))	

				 join chit_settings cs
				 join services ser
				 join company cmp

				Where sa.active=1 and (s.allow_due_alert= 1 or s.allow_due_alert= 0) and sa.is_closed = 0 and id_services = '".$service_id."'

				Group By sa.id_scheme_account");	
			   //print_r($this->db->last_query());exit;
			 if($resultset->num_rows() > 0)
				{
					foreach($resultset->result() as $row)
					{
						if($row->currentpaycount <= 0 ){
							$data[] = array(
									   'id_scheme_account'  => $row->id_scheme_account,
									   'id_customer'		=> $row->id_customer,
									    'ac_no'				=> $row->ac_no, 
									   'sch_name'			=> $row->sch_name,
									   'mobile'				=> $row->mobile,
									   'name'				=> $row->name,
									   'payable'			=> $row->payable,
									   'currency_symbol'	=> $row->currency_symbol,
									   'cmp_name_sms'	=> $row->cmp_name_sms,
									   'currentpaycount'    => $row->currentpaycount
									);
						//print_r($row);exit;
						    
						}
						
					}
				
				}
			      }
			    
			foreach($resultset->result() as $row)
				{
					$sms_msg = $row->sms_msg;
					$sms_footer = $row->sms_footer;
					
				}
			$resultset->free_result();
			
			$field_name_footer = explode('@@', $sms_footer);	
			for($i=1; $i < count($field_name_footer); $i+=2)
			 {
				if(isset($customer_data->$field_name_footer[$i]))
				 { 
					$sms_footer = str_replace("@@".$field_name_footer[$i]."@@",$customer_data->$field_name_footer[$i],$sms_footer);					
				}	
			}
					
			//Generating Message content
			$field_name = explode('@@', $sms_msg);	
			
			$a=0;
    		foreach($data as $row)
    		{
    			$customer_data = $row;
    			//For notification content
    			
    		  	$msgContent =$sms_msg;
    		
    			for($i=1; $i < count($field_name); $i+=2) 
    			{	
    			    
    			   
    			    $field =  $field_name[$i];
    			     
    				if(isset($customer_data[$field])) 
    				{
    					$msgContent = str_replace("@@".$field."@@",$customer_data[$field],$msgContent);	
    					
    					$data[$a]['message']=$msgContent;
    				}
    			}	
    			unset($msgContent);
    			$a++;
    		
    		}
                 //  echo"<pre>";print_r($data);exit;
	 return (array('data'=>$data,'footer'=>$sms_footer));
		
    }
		 
     // send due sms by service call // 
        
        
        
        function get_account($id_branch)
        {
            	$resultset = $this->db->query("select r.goldrate_22ct,sa.id_scheme_account,r.id_metalrates,br.id_metalrate,sa.id_branch,sa.id_customer,c.mobile 
                                                from scheme_account sa
                                                left join branch_rate br on br.id_branch=sa.id_branch
                                                left join metal_rates r on r.id_metalrates=br.id_metalrate
                                                left join customer c on c.id_customer=sa.id_customer
                                                where br.status=1 ".($id_branch != '' ? 'and sa.id_branch IN ('.$id_branch.')' : '')."
                                                group by sa.id_branch,sa.id_customer"); 
                                                
				return $resultset->result_array();
        }
        
        function getBranches(){
            $resultset = $this->db->query("select id_branch from branch");
			return $resultset->result_array();
        }
        
        function get_cusBranchRate($id_branch,$types)
        {
        	$resultset = $this->db->query("select 
											rd.uuid as token,b.name as branch,c.id_branch,c.id_customer,
											r.goldrate_22ct as rate,r.silverrate_1gm as silver,goldrate_22ct as rate,mjdmagoldrate_22ct as mjdmarate,mjdmasilverrate_1gm as mjdmasilver,
											goldrate_24ct as goldRate24,goldrate_18ct as goldRate18,platinum_1g as platinum,
											Date_format(r.updatetime,'%h:%i %p')as time,cmp.company_name as cmp_name,b.metal_rate_type
										FROM customer c
										LEFT JOIN branch b on b.id_branch=c.id_branch
										left join (
												SELECT  br.id_branch,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,
												m.updatetime
												FROM metal_rates m 
												LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates and br.id_branch=".$id_branch."
												ORDER by br.id_metalrate desc LIMIT 1
												) r on r.id_branch=b.id_branch
										left join registered_devices rd on rd.id_customer=c.id_customer
										join company cmp
										WHERE c.id_branch=".$id_branch);
										//print_r($this->db->last_query());exit;
			return $resultset->result_array();
        }
        
    function get_metal_rateby_branch($id_customer)
    {
        $resultset = $this->db->query("SELECT rd.uuid as token,b.name as branch,sa.id_branch,r.id_metalrates,br.id_metalrate,sa.id_customer,r.goldrate_22ct as rate,r.silverrate_1gm as silver,goldrate_22ct as rate,mjdmagoldrate_22ct as mjdmarate,mjdmasilverrate_1gm as mjdmasilver,
                                        goldrate_24ct as goldRate24,goldrate_18ct as goldRate18,platinum_1g as platinum,
                                        Date_format(r.add_date,'%h:%i %p')as time,cmp.company_name as cmp_name,b.metal_rate_type
                                        FROM metal_rates r
                                        LEFT join branch_rate br on br.id_metalrate=r.id_metalrates
                                        LEFT join scheme_account sa on sa.id_branch=br.id_branch
                                        LEFT JOIN branch b on b.id_branch=sa.id_branch
                                        left join registered_devices rd on rd.id_customer=sa.id_customer
                                        join company cmp
                                        WHERE uuid is not null and uuid !='' and br.status=1 and rd.token IS NOT NULL and sa.id_customer=".$id_customer." GROUP by sa.id_branch"); 				
			//print_r($this->db->last_query());exit;
			return $resultset->result_array();
    }
    
    function get_sendnotifi_cusBranch($id_branch,$types)
        {
        	$resultset = $this->db->query("select 
											rd.uuid as token,c.id_branch,c.id_customer
										
										FROM customer c
										LEFT JOIN branch b on b.id_branch=c.id_branch
										
										left join registered_devices rd on rd.id_customer=c.id_customer
										
										WHERE c.id_branch=".$id_branch);
									//	print_r($this->db->last_query());exit;
			return $resultset->result_array();
        }
        
    function deleteNoPayments_Acc($months)
	{
	    // Delete all accounts which has no payment entries for past $months [Number of months]. Get all accounts without payments and then validate the month.
		$sql = "SELECT 
		            sa.id_scheme_account,date(sa.start_date) as start_date,date(Date_add(sa.start_date, INTERVAL+ 2 MONTH)) as no_pay 
		            FROM scheme_account sa 
		                LEFT JOIN (SELECT p.id_scheme_account FROM payment p WHERE p.id_scheme_account is NULL) pay ON pay.id_scheme_account = sa.id_scheme_account 
		            WHERE date(Date_add(sa.start_date, INTERVAL+ ".$months." MONTH)) <= CURRENT_DATE AND sa.paid_installments=0 AND sa.is_closed=0 AND sa.is_new='Y' AND sa.active=1 AND pay.id_scheme_account is NULL";
		//echo $sql."<br/>";
		$inactive_acc = $this->db->query($sql);	 
		
		if($inactive_acc->num_rows()>0)
		{
		  foreach($inactive_acc->result_array() as $record){  
		      /* echo "<pre>";
		       print_r($inactive_acc->result_array());
		        echo "</pre>";exit;*/
	
	     $status = $this->db->delete(self::ACC_TABLE,array('id_scheme_account'=>$record['id_scheme_account']));			       
	 	
		//print_r($this->db->last_query());exit;
			return "Has ".$inactive_acc->num_rows()." Accounts Deleted";
		  }
		}else{
		    return "No accounts found";
		}
		
	}

	  // Retail Settings //HH
	  function get_ret_settings()
	  {
  $sql = "Select * from ret_settings";
  return $retail=$this->db->query($sql)->result_array();
  }
  public function get_empty_recordss() //Fetch listing record
  {
  $data=array(
  
  'id_ret_settings' => 0,
  'name'        => NULL,
  'value'                => NULL,
  'description'    => NULL,
		 'created_by'          => NULL,
		 'updated_by'            =>NULL
  );
  return $data;
  }
  public function get_entry_recordss($id) //Fetch entry record
  {
  
  //print_r($query);exit;
  $query="select id_ret_settings, name, value,description,created_by,updated_by
  from ret_settings where id_ret_settings =".$id;
  $result_set=$this->db->query($query);
  
  foreach ($result_set->result() as $row)
  {
  
  $records['id_ret_settings'] = $row->id_ret_settings;
  $records['name']        = $row->name;
  $records['value']    = $row->value;
  $records['description']    = $row->description;
		  $records['created_by']      =$row->created_by;
		  $records['updated_by']      =$row->updated_by;
		 
		 //print_r($this->db->last_query($records));
  
  }
  return $records;
  
  }
  
  
		  public function delete_ret_settings($id)
  {
  $this->db->where('id_ret_settings',$id);  
  $status=$this->db->delete(self::RET_SET_TABLE);
  return $status;
  }
  
	   
		  public function insert_ret_settings($data)
  {
  
  $this->db->insert(self::RET_SET_TABLE, $data);
	  }
	 
	   public function update_ret_settings($data)
  {
  $this->db->where('id_ret_settings',$data['id_ret_settings']);
  
  $this->db->update(self::RET_SET_TABLE, $data);
  
	  } 
	  
	  
	  
	function send_whatsApp_message($mobile,$message,$attachement_url="",$file_name="") 
    {
    	//$whatsappdata = array("phone" => "91".$mobile, "body" => $message);
    	
    	//$whatsappdata = array("phone" => "919095563264", "body" => "http://www.africau.edu/images/default/sample.pdf", "filename" => "sample.pdf", "caption" => "Text under the file. When sending an image сan be used with mentionedPhones");

    	$whatsappdata = array("phone" => "91".$mobile, "body" => ($attachement_url!='' ? $attachement_url:$message), "filename" => ($attachement_url!='' ? $file_name:''), "caption" => ($attachement_url!='' ? $message:''));

    	if($attachement_url!='')
    	{
    	    $whatsappurl = $this->config->item("whatsappfileurl");
    	}else
    	{
    	    $whatsappurl = $this->config->item("whatsappurl");
    	}

    	$curl = curl_init();
    	curl_setopt_array($curl, array(
    	 CURLOPT_URL => $whatsappurl,
    	 CURLOPT_RETURNTRANSFER => true,
    	 CURLOPT_ENCODING => "",
    	 CURLOPT_MAXREDIRS => 10,
    	 CURLOPT_TIMEOUT => 30,
    	 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    	 CURLOPT_CUSTOMREQUEST => "POST",
    	 CURLOPT_POSTFIELDS => json_encode($whatsappdata),
    	 CURLOPT_SSL_VERIFYPEER => FALSE,
    	 CURLOPT_HTTPHEADER => array(
    	"authorization: Basic cHJlY2lzZXRyYTpIaXJoTmwxMA==",
    	"cache-control: no-cache",
    	"content-type: application/json"
    	 ),
    	));
    	
    
    	$response = curl_exec($curl);
    	//echo "<pre>"; print_r($response);exit;
    	$err = curl_error($curl);
    	curl_close($curl);
    	if($err){
    		return false;
    	}else{
    		$res = json_decode($response);
    		return $res->message;
    	}  
    }
    
    function get_customer_wishes_data(){   //AB
        $data = $this->db->query("SELECT c.mobile,c.id_customer,date(c.date_of_birth) as date_of_birth,DATE_FORMAT(date(date_of_birth), '%M%D') as bday, 
                                date(c.date_of_wed) as date_of_wed,DATE_FORMAT(date(date_of_wed), '%M%D') as wedday,DATE_FORMAT(CURDATE(), '%M%D') as curday, 
                                CONCAT(c.firstname,' ',IFNULL(c.lastname,'')) as cus_name,
                                if(DATE_FORMAT(date(date_of_birth), '%M%D') = DATE_FORMAT(CURDATE(), '%M%D'),'1','0') as send_bday,
                                if(DATE_FORMAT(date(date_of_wed), '%M%D') = DATE_FORMAT(CURDATE(), '%M%D'),'1','0') as send_wedday
                                FROM customer c 
                                WHERE c.active = 1 AND c.mobile is not null AND c.mobile RLIKE '[0-9]{10}' 
                                AND (date_of_birth is not null OR date_of_wed is not null) 
                                AND (DATE_FORMAT(date(date_of_birth), '%M%D') = DATE_FORMAT(CURDATE(), '%M%D') OR DATE_FORMAT(date(date_of_wed), '%M%D') = DATE_FORMAT(CURDATE(), '%M%D'))")->result_array();
                                
        return $data;                        
    }
    
    function get_cus_wallet_data(){
        
		$data = $this->db->query("SELECT c.id_customer,wt.id_wallet_transaction,
	   SUM(CASE WHEN wt.transaction_type = 0 THEN wt.value ELSE -wt.value END) AS balance_amount 
		FROM customer c LEFT JOIN wallet_account wa ON c.id_customer = wa.id_customer 
		LEFT JOIN wallet_transaction wt ON wa.id_wallet_account = wt.id_wallet_account
	   GROUP BY  c.id_customer   having balance_amount  > 0;")->result_array();
							   
	   return $data;        
	   
   }
}
?>