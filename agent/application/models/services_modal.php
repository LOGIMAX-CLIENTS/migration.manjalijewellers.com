<?php
class Services_modal extends CI_Model {
	public function __construct() {
		$this->load->database();
	}
	
	function offers()   // based on the branch settings to showed Offers //HH
	{
	    $id_branch = $this->session->userdata('id_branch');	
	    $branch_settings=$this->session->userdata('branch_settings');
        $is_branchwise_cus_reg=$this->session->userdata('is_branchwise_cus_reg');
	    if($branch_settings==1 && $is_branchwise_cus_reg==1)        // after login showed to showed cus branch Offers //
	    {
		if( $id_branch !='' )
		{
			$offers=$this->db->query("SELECT  o.id_offer,o.name,o.offer_img_path,o.offer_content FROM offers o where active=1 and type=0 and o.id_branch=".$id_branch);
		}
	    }
	    else   // before login showed to showed common Offers //
	    {
	       $offers=$this->db->query("SELECT  o.id_offer,o.name,o.offer_img_path,o.offer_content FROM offers o where active=1 and type=0");  
	    }
		return $offers->result_array();	
	}	
	
	function newarrivals()  //expiry date && Price show based on sett && based on the branch settings to showed Newarrivals //HH
	{
		$id_branch = $this->session->userdata('id_branch');	
	    $branch_settings=$this->session->userdata('branch_settings');
        $is_branchwise_cus_reg=$this->session->userdata('is_branchwise_cus_reg');
	    if($branch_settings==1 && $is_branchwise_cus_reg==1)        // after login showed to showed cus branch Newarrivals //
	    {
		if( $id_branch !='' )
		{
			$newarrivals=$this->db->query("SELECT n.id_new_arrivals,n.expiry_date, n.name, n.new_arrivals_content, n.new_arrivals_img_path FROM new_arrivals n
		where active=1   and (show_rate=1 or show_rate=0) and new_type=1 and id_branch=".$id_branch." and  expiry_date > NOW() ORDER BY expiry_date ASC");
		}
	    }
	    else     // before login showed to showed common Newarrivals //
	    {
	       $newarrivals=$this->db->query("SELECT n.id_new_arrivals,n.expiry_date, n.name, n.new_arrivals_content, n.new_arrivals_img_path FROM new_arrivals n
		where active=1   and show_rate=1 or show_rate=0 and new_type=1  and  expiry_date > NOW() ORDER BY expiry_date ASC ");  
	    }
		return $newarrivals->result_array();
	}
	function gift_artical()
	{
		$sql = "SELECT n.id_new_arrivals, n.name, n.new_arrivals_content, n.new_arrivals_img_path,n.gift_type FROM new_arrivals n
		where active=1 and gift_type=1";
		return $this->db->query($sql)->result_array();
	} 
	function gift()
	{
		$sql = "SELECT n.gift_type FROM new_arrivals n";
		return $this->db->query($sql)->row_array();	
	}
	function get_data()
	{
		$sql = "SELECT cs.allow_referral FROM chit_settings cs";
		return $this->db->query($sql)->row_array();	
	}
	function offer_details($id)
	{
		$sql = "SELECT o.name,o.offer_img_path,o.offer_content FROM offers o where o.active=1 aND o.id_offer=".$id;
		return $this->db->query($sql)->row_array();
	}	
	function newarrival_detail($id)  //expiry date && Price show based on sett//hh
	{
		$sql = "SELECT * from new_arrivals where active=1 AND  new_type=1 and expiry_date > NOW() and show_rate=1 or show_rate=0 AND id_new_arrivals=".$id;
		//print_r($sql);exit;
		return $this->db->query($sql)->row_array();
	}  
	function gift_artical_detail($id)
	{
		$sql = "SELECT * from new_arrivals where active=1 AND gift_type=1 and show_rate=1 AND id_new_arrivals=".$id;
		return $this->db->query($sql)->row_array();
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
			$dlt_te_id  = $row->dlt_te_id;
		}
		$data = array("email" => $email, "sms" => $sms, "dlt_te_id" => $dlt_te_id);
		return $data;
	}
//Promotion sms and otp setting
	public function sms_info()
	{
		   $sql=$this->db->query("Select id_sms_api, sms_sender_id, sms_url FROM sms_api_settings"); 
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
	public function get_SMS_data($service_id, $id)
     {
		//Declaration of variables
		$message ="";
		$sms_msg = "";
		$sms_footer = "";
		$customer_data = array();
		if($service_id == 1)
		{
			$resultset=$this->db->query("Select
		   c.id_customer, if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,
					  IFNULL(c.firstname,' ') as fname,
					  IFNULL(c.lastname,' ') as lname,
					  c.date_of_birth,c.date_of_wed,
		   a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,
		   c.phone,c.mobile,c.email,c.nominee_name,c.nominee_relationship,c.nominee_mobile,cmp.website,
		   c.cus_img,c.pan,c.pan_proof,c.voterid,c.voterid_proof,c.rationcard,c.rationcard_proof,a.id_country,a.id_city,a.id_state,c.id_employee,c.mobile as userId,
   	c.comments,c.username,c.passwd,c.is_new,c.active,c.`date_add`,c.`date_upd`, cmp.`company_name` as cmp_name,
					  cmp.phone as cmp_ph,cmp.comp_name_in_sms as cmp_name_sms 
			From
			  customer c
			left join address a on(c.id_customer=a.id_customer)
			left join country cy on (a.id_country=cy.id_country)
			left join state s on (a.id_state=s.id_state)
			left join city ct on (a.id_city=ct.id_city)
			join company cmp
			join chit_settings cs
			where c.id_customer=".$id);
		}
		else if($service_id == 2)
		{
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
					 if(cs.has_lucky_draw=1,concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Transcation Pending')),concat(ifnull(s.code,''),' ',ifnull(sa.scheme_acc_number,'Transcation Pending')))as acc_no,
					  sa.`account_name` as ac_name,
					  DATE_FORMAT(sa.`start_date`,'%d-%m-%Y') as start_date,
					  sa.`is_opening`,
					  IFNULL(0,sa.`paid_installments`) as paid_installments,
					  if(s.scheme_type=0,(CONCAT(cs.currency_symbol,' ',(IFNULL(0,sa.`balance_amount`)))),(CONCAT((IFNULL(0,sa.`balance_weight`)),' g'))) as closing_blc,
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
					  cmp.`company_name` as cmp_name,
					  cmp.phone as cmp_ph,cmp.comp_name_in_sms as cmp_name_sms 
				FROM scheme_account sa
				LEFT JOIN customer c   ON (sa.id_customer = c.id_customer)
				LEFT JOIN address a    ON (sa.id_customer = a.id_customer)
				LEFT JOIN scheme s     ON (sa.id_scheme = s.id_scheme)
				LEFT JOIN country ctry ON (a.id_country = ctry.id_country)
				LEFT JOIN state st     ON (a.id_country = st.id_state)
				LEFT JOIN city ct      ON (a.id_city = ct.id_city)
				join company cmp
				join chit_settings cs
				WHERE sa.id_scheme_account = '".$id."'");
				}
			else if($service_id == 12)
			{
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
					  if(s.`scheme_type`=0,'Amount','Weight') as scheme_type,
					  s.`scheme_name` as sch_name,
					  s.`code` as sch_code,
					  if(s.scheme_type=0,(concat(cs.currency_symbol,' ',s.amount)),concat('Max ', s.max_weight, 'g /month')) as payable,
					  s.`total_installments`,
					if(cs.has_lucky_draw=1,concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Not allocated')),concat(ifnull(s.code,''),' ',ifnull(sa.scheme_acc_number,'Not allocated')))as acc_no,
					  sa.`account_name` as ac_name,
					  DATE_FORMAT(sa.`start_date`,'%d-%m-%Y') as start_date,
					  sa.`is_opening`,
					  IFNULL(0,sa.`paid_installments`) as paid_installments,
					  if(s.scheme_type=0,(CONCAT(cs.currency_symbol,' ',(IFNULL(0,sa.`balance_amount`)))),(CONCAT((IFNULL(0,sa.`balance_weight`)),' g'))) as closing_blc,
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
					  cmp.`company_name` as cmp_name,
					  cmp.phone as cmp_ph,cmp.comp_name_in_sms as cmp_name_sms 
				FROM scheme_account sa
				LEFT JOIN customer c   ON (sa.id_customer = c.id_customer)
				LEFT JOIN address a    ON (sa.id_customer = a.id_customer)
				LEFT JOIN scheme s     ON (sa.id_scheme = s.id_scheme)
				LEFT JOIN country ctry ON (a.id_country = ctry.id_country)
				LEFT JOIN state st     ON (a.id_country = st.id_state)
				LEFT JOIN city ct      ON (a.id_city = ct.id_city)
				join company cmp
				join chit_settings cs
				WHERE sa.scheme_acc_number = '".$id."'");
				}		
		else if($service_id == 3 || $service_id == 7)
		{
			$resultset = $this->db->query("SELECT
					  p.id_payment,
					  sa.account_name as ac_name,
					  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,
					  c.firstname as fname,
					   IFNULL(c.lastname,' ') as lname,
					   c.email,
					  c.mobile,
					  if(cs.has_lucky_draw=1,concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Not allocated')),concat(ifnull(s.code,''),' ',ifnull(sa.scheme_acc_number,'Not allocated')))as acc_no,
					  s.code,
					  p.id_employee,
                      if(e.lastname is null,e.firstname,concat(e.firstname,' ',e.lastname)) as employee, 
					  if(s.scheme_type=1,'Weight','Amount') as scheme_type,
					  CONCAT(cs.currency_symbol,' ',IFNULL(p.payment_amount,'-')) as pay_amt,
					  p.metal_rate,
					  IFNULL(p.metal_weight, '-') as metal_weight,
					  IFNULL(Date_format(p.date_payment,'%d-%m%-%Y'),'-') as date_pay,
			          p.payment_type,
					  p.payment_mode as pay_mode,
					  if(cs.has_lucky_draw=1,concat(ifnull(sa.group_code,''),' ',ifnull(sa.scheme_acc_number,'Not allocated')),concat(ifnull(s.code,''),' ',ifnull(sa.scheme_acc_number,'Not allocated')))as acc_no,
					  IFNULL(p.bank_name,'-')as bank_name,
					  IFNULL(p.bank_IFSC,'-') as bank_IFSC,
					  IFNULL(p.bank_branch,'-') as bank_branch,
					  IFNULL(p.id_transaction,'-') as txn_id,
					  IFNULL(p.payu_id,'-') as payu_id ,
					  IFNULL(p.card_no,'-') as card_no,
					  psm.payment_status as status,
					  p.payment_status as id_status,
					  psm.color as status_color,
					  IFNULL(p.payment_ref_number,'-') as payment_ref_number,
					  IFNULL(p.remark,'-') as remark,
					  cmp.company_name as cmp_name, cmp.phone as cmp_ph,cmp.comp_name_in_sms as cmp_name_sms 
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
			    }
			     else if($service_id == 10)
			     {
				 		$resultset=$this->db->query("Select  id_customer,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,
					  c.firstname as fname,c.lastname as lname,c.email,
					  c.mobile, from customer c  where c.mobile='".$id."'");
				} 
				else if($service_id == 15 || $service_id == 19)
			     {
				 		$resultset=$this->db->query("Select  c.id_customer,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name,
					    c.firstname as fname,c.lastname as lname,c.email,cmp.company_name as cmp_name,IFNULL(c.mobile,'') as ref_code, c.mobile ,cmp.comp_name_in_sms as cmp_name_sms 
					    from customer c
					     join company cmp
					     where c.mobile='".$id."'");
			 	} 
			 	else if($service_id == 16){
						  $resultset=$this->db->query("SELECT if(sa.is_refferal_by=0 && chit.cusplan_type=1,if(ws.type=0,ws.value,((s.amount*ws.value)/100)),
				            if(sa.is_refferal_by=0 && s.cus_refferal=1 && chit.cusplan_type=0,s.cus_refferal_value,if(sa.is_refferal_by=1 && chit.empplan_type=1,if(ws.type=0,ws.value,((s.amount*ws.value)/100)),
				              if(sa.is_refferal_by=1 && s.emp_refferal=1 && chit.empplan_type=0,s.emp_refferal_value,'')))) as amount,
							ref.mobile,ref.firstname as fname,comp.company_name as cmp_name	,comp.comp_name_in_sms as cmp_name_sms 				
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
				else if($service_id == 8){
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
								   cmp.company_name as cmp_name,
								  cmp.phone as cmp_ph,cmp.comp_name_in_sms as cmp_name_sms
							From wallet_account wa
								Left Join customer c on (wa.id_customer=c.id_customer)
								Left Join employee e on (wa.id_employee=e.id_employee)
								Left Join wallet_transaction wt on (wa.id_wallet_account=wt.id_wallet_account)
								join company cmp
			 					join chit_settings cs 
			 					Where wa.id_wallet_account='".$id."'");
				}
				foreach($resultset->result() as $row)
				{
					$customer_data = $row;
					$mobile=$row->mobile;
				}
			$resultset = $this->db->query("SELECT sms_msg, sms_footer,serv_sms,serv_email from services where id_services = '".$service_id."'");
				foreach($resultset->result() as $row)
				{
				$serv_sms = $row->serv_sms;
				$serv_email = $row->serv_email;
					$sms_msg = $row->sms_msg;
					$sms_footer = $row->sms_footer;
				}
			$resultset->free_result();
			//Generating Message content
			$field_name = explode('@@', $sms_msg);	
			for($i=1; $i < count($field_name); $i+=2) 
			{
                $field =  $field_name[$i];
				if(isset($customer_data->$field)) 
				{ 
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
	return (array('message'=>$sms_msg,'mobile'=>$mobile,'serv_email'=>$serv_email,'serv_sms'=>$serv_sms));
	}
/*-- Coded by ARVK --*/	
	function limitDB($type="",$id="",$set_array="")
   	{
   	    switch($type)
		{
			case 'get': 
	   	        if($id!=NULL)
	   	        {
					$sql="Select 
								id_limit, 
								limit_cust, 
								cust_max_count, limit_sch, 
								sch_max_count,
								limit_branch,
								branch_max_count,
								limit_sch_acc,
								sch_acc_max_count
						  From limit_settings
						  Where id_limit=".$id;
	   	 		    if($this->db->query($sql)->num_rows()>0){
						return $this->db->query($sql)->row_array();
					}else{
						$data = array(	
											'id_limit'    	 		=> NULL, 
											'limit_cust'    	 	=> 0, 
											'cust_max_count'    	=> 0,
											'limit_sch'  	 		=> 0,
											'sch_max_count'  		=> 0,
											'limit_branch'  	 	=> 0,
											'branch_max_count'  	=> 0,
											'limit_sch_acc'  	 	=> 0,
											'sch_acc_max_count'  	=> 0
						   			 );
						$status = $this->limitDB('insert','',$data);
						return $data;
					}
				}
				else
				{
					$sql="Select 
								id_limit, 
								limit_cust, 
								cust_max_count, limit_sch, 
								sch_max_count,
								limit_branch,
								branch_max_count,
								limit_sch_acc,
								sch_acc_max_count
						  From limit_settings";
	   	 		    return $this->db->query($sql)->result_array();
				}
	   	   	 break;
			default: 
				return array(	
								'id_limit'    	 		=> NULL, 
								'limit_cust'    	 	=> 0, 
								'cust_max_count'    	=> 0,
								'limit_sch'  	 		=> 0,
								'sch_max_count'  		=> 0,
								'limit_branch'  	 	=> 0,
								'branch_max_count'  	=> 0,
								'limit_sch_acc'  	 	=> 0,
								'sch_acc_max_count'  	=> 0
   			 );
   			break; 
		}	  
   }
	function customer_count()
	{
		$sql = "SELECT id_customer FROM customer";
		return $this->db->query($sql)->num_rows();
	}	
 /*-- / Coded by ARVK --*/	
	function get_wallet_acc_number()
	{
	  $query = $this->db->query("SELECT LPAD(round(rand() * 10000000),8,0) as myCode
								FROM `wallet_account`
								HAVING myCode NOT IN (SELECT wallet_acc_number FROM `wallet_account`) limit 0,1");
		if($query->num_rows()==0){
			$query = $this->db->query("SELECT LPAD(round(rand() * 10000000),8,0) as myCode");
		}
		return $query->row()->myCode;
	}
	function walletacc_insert($wallet_array){
	$status = $this->db->insert('wallet_account',$wallet_array);
	return	array('status'=>$status,'insertID'=>($status == TRUE ? $this->db->insert_id():''));
	}
	function get_walletacc($id){
		$sql = "Select
			  wa.id_wallet_account,
			  c.id_customer,
			  Concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')) as name,
			  c.mobile,c.email,
			  Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_name,
			  wa.wallet_acc_number,
			  Date_Format(wa.issued_date,'%d-%m-%Y') as issued_date,
			  wa.remark,
			  wa.active,
			  SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) as  issues,
			  SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END) as redeem,
			  (SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) -   SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END)) as balance
		From wallet_account wa
			Left Join customer c on (wa.id_customer=c.id_customer)
			Left Join employee e on (wa.id_employee=e.id_employee)
			Left Join wallet_transaction wt on (wa.id_wallet_account=wt.id_wallet_account) 
			".($id!=null? 'Where wa.id_wallet_account='.$id:'')." 
			Group By wa.id_wallet_account ";
        $r = $this->db->query($sql);	
        if($id!=NULL)
        {
          return $r->row_array(); //for single row
        }	
    }
    
    function insProduct_enquiry($data)  
    {
		$status = $this->db->insert('product_enquiry',$data);
		return array('status' => $status, 'insertID' => $this->db->insert_id());
	}
	function get_products($id_product)
   {
	   $sql="select * from products where id_product=".$id_product."";
	   $result=  $this->db->query($sql)->row_array();
		return $result;   	
   }
   
	function get_compare_plans()
	{
		$sql = "SELECT cs.compare_plan_img FROM chit_settings cs";
		//print_r($sql);exit;
		return $this->db->query($sql)->row_array();	
	}
	
	function get_SMS_OTPdata($service_id,$customer_data){
        $sms_msg = "";
        $sql = $this->db->query("Select c.id_company,c.company_name as cmp_name,c.short_code,c.pincode,c.mobile,c.phone as cmp_ph,c.email,c.website,c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,cy.name as country,cs.currency_symbol,cs.currency_name
				from company c
				left join country cy on (c.id_country=cy.id_country)
				left join state s on (c.id_state=s.id_state)
				left join city ct on (c.id_city=ct.id_city) 
				join chit_settings cs");
        $cust_data = $sql->row_array(); 
        if($sql->num_rows() == 1){ 
            $customer_data['cmp_name']      = $cust_data['cmp_name'];
            $customer_data['short_code']    = $cust_data['short_code'];
            $customer_data['cmp_ph']        = $cust_data['cmp_ph'];
            $customer_data['mobile']        = $cust_data['mobile'];
            $customer_data['email']         = $cust_data['email'];
            $customer_data['currency_symbol'] = $cust_data['currency_symbol'];
        }
        $resultset = $this->db->query("SELECT sms_msg, sms_footer from services where id_services = '".$service_id."'");
		foreach($resultset->result() as $row)
		{
			$sms_msg = $row->sms_msg;
			$sms_footer = $row->sms_footer;
		}
		$resultset->free_result();
		//Generating Message content
		$field_name = explode('@@', $sms_msg);
		for($i=1; $i < count($field_name); $i+=2) 
    	{
    		$field =  $field_name[$i];
    		if(isset($customer_data[$field]))
    		{  
    		    $sms_msg = str_replace("@@".$field."@@",$customer_data[$field],$sms_msg);
    		}	
    	}
		$field_name_footer = explode('@@', $sms_footer);	
		for($i=1; $i < count($field_name_footer); $i+=2)
		 {
			if(isset($customer_data[$field_name_footer[$i]]))
			 { 
				$sms_footer = str_replace("@@".$field_name_footer[$i]."@@",$customer_data[$field_name_footer[$i]],$sms_footer);					
			}	
		}
		$sms_msg .= " ".$sms_footer;
		return $sms_msg;
    }
    function send_whatsApp_message($mobile,$message)
    {
        $whatsappurl = $this->config->item("whatsappurl");
        $instanceid = $this->config->item("whats-instanceid");
        $whatsappurl = $whatsappurl."sendText?token=".$instanceid."&phone=91".$mobile."&message=".urlencode($message);
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $whatsappurl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Basic cHJlY2lzZXRyYTpIaXJoTmwxMA==",
                "cache-control: no-cache",
                "content-type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl); 
        
        if($err)
        {
            return false;
        }
        else
        {
            $res = json_decode($response);
//            echo "<pre>";print_r($res);
            return $res->message;
        }
    }
}
?>