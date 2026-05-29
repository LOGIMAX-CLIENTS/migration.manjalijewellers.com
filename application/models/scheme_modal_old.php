<?php

class Scheme_modal extends CI_Model {

	var $table_name = 'scheme';						

	//Initialize table Name



	function empty_record()

	{

		$cusActive = 0;

		$query = $this->db->query('SELECT active FROM customer WHERE mobile="'.$this->session->userdata('username').'"');

		

		if($query->num_rows() > 0)

		{

			foreach($query->result() as $row)

			{

				$cusActive = $row->active;

			}

		}

		

		$query->free_result();

		return array('cusActive' => $cusActive);

	}

	

	function get_schemes()

	{

		$this->db->select("id_scheme,code");

		$this->db->where("active",1);

		$schemes = $this->db->get("scheme");

		return $schemes->result_array();

	}

	

	function get_scheme($schemeType)

	{

		$records = array();

		$isJoined = 0;
			$query_scheme = $this->db->query('SELECT maturity_installment,sch.id_scheme AS id_scheme,scheme_name, scheme_type, description, amount,total_installments, interest, interest_by, interest_value,description, min_weight, max_weight, min_chance, max_chance,schAcc.id_scheme_account AS id_scheme_account,IF((IFNULL(totalpay.paidIns,0) + IFNULL(schAcc.paid_installments,0)) > 0, 1, 0) AS isPaid 
												FROM scheme AS sch

												LEFT JOIN (SELECT id_scheme, id_scheme_account, if(IFNULL(is_opening,0) = 0,0, IFNULL(paid_installments,0)) AS paid_installments FROM scheme_account WHERE id_customer = "'.$this->session->userdata('cus_id').'" AND is_closed != 1 AND active = 1) AS schAcc

												ON schAcc.id_scheme = sch.id_scheme

												LEFT JOIN (SELECT id_scheme_account,IFNULL(COUNT(DISTINCT(DATE_FORMAT(date_payment,"%Y%m"))),0) AS paidIns from payment WHERE payment_status = 1 GROUP BY id_scheme_account) AS totalpay ON totalpay.id_scheme_account = schAcc.id_scheme_account

												WHERE active = 1 ORDER BY id_scheme');

				if($query_scheme->num_rows() > 0)

				{

					foreach($query_scheme->result() as $row)

					{

						if($row->id_scheme_account != '')

						{

							$isJoined = 1; //not joined in any scheme

							$schemeJoined = 1; //for join button

						}

						else

						{

							$schemeJoined = 0;

						}
						$records[] = array('id' => $row->id_scheme, 'name' => $row->scheme_name, 'maturity_installment' => $row->maturity_installment, 'description' => $row->description,'scheme_type' => $row->scheme_type, 'amount' => $row->amount,'total_installments' => $row->total_installments,  'interest' => $row->interest, 'interest_by' => $row->interest_by,'interest_value' => $row->interest_value,'min_weight' => $row->min_weight,'max_weight' => $row->max_weight, 'min_chance' => $row->min_chance,'max_chance' => $row->max_chance,'schemeJoined' => $schemeJoined,'isPaid' => $row->isPaid);
					}

					

				}

			$query_scheme->free_result();

			

			$query_weight = $this->db->query('SELECT weight FROM weight WHERE active = 1');

				if($query_weight->num_rows() > 0)

				{

					foreach($query_weight->result() as $row)

					{

						$weight[] = array('weight' => $row->weight);

					}

				}

			$query_weight->free_result();

			

			$allow_unpaid =$this->allowUnpaid();	//allow customer not paid single installment		

			$allow_multiple = $this->allowMultipleChits(); //allow multiple chits for customer
			
			$unpaid = $this->check_unpaid_schemes($this->session->userdata('cus_id'));

			

			echo json_encode(array('scheme' => $records, 'weight' => $weight,'isJoined' => $isJoined,'unpaid' => $unpaid ,'allow_multiple' =>$allow_multiple,'allow_unpaid' =>$allow_unpaid ));

	}

	/*-- Coded by ARVK --*/			 
	function sch_acc_count()
	{
		$sql = "SELECT id_scheme_account FROM scheme_account";
		return $this->db->query($sql)->num_rows();
	}	
			
	function limit_sch_acc()
	{
						$limit= $this->services_modal->limitDB('get','1');
						$sch_acc_count = $this->sch_acc_count(); //count of total scheme accounts 
						//print_r($sch_acc_count);exit;
						
						if($limit['limit_sch_acc']==1)
						{
							
							if($sch_acc_count < $limit['sch_acc_max_count'])
							{
								return FALSE;
							}else
							{
								return TRUE;
						 	}
							
						}else
						{
							return FALSE;	
						}
	}	
						
	
/* / Coded by ARVK*/
	

	function get_active_schemes()

	{

		$sql = "Select

					  id_scheme,scheme_name,scheme_type,code,id_classification,
					   description,cs.currency_name,cs.currency_symbol,
					   IFNULL(min_chance,0) as min_chance,
					   IF(scheme_type=1,IFNULL(max_chance,0),1) as max_chance,
					   Format(IFNULL(max_weight,0),3) as max_weight,
					   Format(IFNULL(min_weight,0),3) as min_weight,
					   IF(scheme_type=1,max_weight,amount) as payable,
					   IF(scheme_type=0 || scheme_type=2 ,amount * total_installments,'') as total_payable,
					   Format(if(interest_by=0,interest_value,round(amount * (interest_value/100))),2) as interest,
					   total_installments,cmp.company_name

				From

					  scheme

					  join chit_settings cs

					  join company cmp

				Where active=1 and visible=1";

		$schemes = $this->db->query($sql)->result_array();

		

		$query_weight = $this->db->query('SELECT weight,cs.currency_name,cs.currency_symbol FROM weight join chit_settings cs WHERE active = 1 ');

		$weights =$query_weight->result_array();	

		

		$filename = base_url().'api/rate.txt'; 	

	    $data = file_get_contents($filename);

	    $metalrates = (array) json_decode($data);

		 

		$allow_unpaid =$this->allowUnpaid();	//allow customer not paid single installment		

		$allow_multiple = $this->allowMultipleChits(); //allow multiple chits for customer

		$limit_exceeded = $this->limit_sch_acc(); //Scheme acc limit check
		
		$unpaid = $this->check_unpaid_schemes($this->session->userdata('cus_id')); 

		$unClosedAcc = $this->hasUnclosedAccounts($this->session->userdata('cus_id')); 

		

		$allow_join = '';

		if($limit_exceeded == FALSE)
		{			
			

			if($allow_multiple == TRUE )

			{
				if($allow_unpaid == TRUE)
				{
					$allow_join = array('status'=> TRUE);
				}
				else
				{
					if($unpaid == TRUE)
					{
						$allow_join = array('status'=> FALSE, 'msg' => 'You can\'t join now, as you have scheme accounts without single payment, make payments for unpaid before joining new scheme' );
					}
					else
					{
						$allow_join = array('status'=> TRUE);
					}
				}
			}

			else

			{

				if($unClosedAcc == TRUE)

				{

					$allow_join = array('status'=> FALSE, 'msg' => 'You have unclosed chits, kindly contact customer care and close to join new scheme.' );

				}

				else

				{

					$allow_join = array('status'=> TRUE);

				}

			}

			

			$sch = "Select id_scheme,scheme_name,scheme_type,code From scheme Where active=1";

			$schs = $this->db->query($sch)->result();

			$sch_list[]=array('id_scheme'=>" ",'code'=>" ");	

			foreach($schs as $scheme)

			{

				$sch_list[]=array(

					'id_scheme'=> $scheme->id_scheme,

					'code' => $scheme->code

				);

			} 

			

			return array('schemes' => $schemes,'sch_list' => $sch_list,'weights' =>$weights,'goldrate_22ct' => $metalrates['goldrate_22ct'],'allow_join' => $allow_join ); 

		}
	}
	
/*Coded by ARVK*/

	function get_classifications()
	{
		$sql = "SELECT  id_classification, classification_name, description
				FROM sch_classify sc
				WHERE EXISTS (SELECT id_classification
					FROM scheme s
                  	WHERE sc.id_classification = s.id_classification and s.active=1 and s.visible=1)";

		$classifications = $this->db->query($sql)->result_array();
		
		return $classifications;
	}
	
	function get_classification($id)
   {
   	 $this->db->select('id_classification,classification_name,description');
   	 $this->db->where('id_classification',$id);
   	 $data=$this->db->get('sch_classify');
   	 return $data->row_array();
   }

	
	function join_scheme()

	{
			

//		$account_number = $this->account_number_generator($this->input->post('schemeID'));

		$sql = $this->db->query("select  if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as cus_name from customer c

								where c.id_customer=".$this->session->userdata('cus_id')."

		");
		
			$ref_code = array();
			$is_refferal_by = NULL;
			$customer = $sql->row_array('cus_name');
			if($this->input->post('referal_code')!= ''){
				$ref_code = explode('-',$this->input->post('referal_code'));
				$is_refferal_by = (strtoupper($ref_code[0]) == 'CUS' ? 0 :(strtoupper($ref_code[0]) == 'EMP'?1:NULL));
			}
		
		if($this->session->userdata('branch_settings')==1)
			{
				$id_branch  = $this->input->post('id_branch');
				}
			else{
				$id_branch =NULL;
			}

			$scheme_acc  = array("id_scheme" => $this->input->post('schemeID'),

			                    "id_customer" => $this->session->userdata('cus_id'),

			                    "account_name" =>($this->input->post('account_name')!= '' ? ucfirst($this->input->post('account_name')):ucfirst($customer['cus_name'] )),

			                    "scheme_acc_number" => NULL,
			                    "referal_code" =>($this->input->post('referal_code')!= '' ? $this->input->post('referal_code'):NULL ),
			                    "is_refferal_by" => $is_refferal_by,

								"ref_no" => '',
								
								
								"id_branch" => $id_branch,

			                    "start_date" => date('Y-m-d H:i:s'),

			                    "date_add" => date('Y-m-d H:i:s'),"is_new" => 'Y', "active" => 1);

/* Coded by ARVK*/				
				$sql_scheme = $this->db->query("select s.free_payment, s.amount, s.scheme_type, s.min_weight, s.max_weight, c.company_name, c.short_code ,s.gst,s.gst_type
			  										
			  										from scheme s join company c
			  										
			  										where s.id_scheme=".$this->input->post('schemeID'));
			  										
			  	$sch_data = $sql_scheme->row_array();
			  	
			  	$sch_data['id_scheme'] = $this->input->post('schemeID');
/* / Coded by ARVK*/			  	
			if($this->db->insert('scheme_account', $scheme_acc))

			{

				$insertID = $this->db->insert_id();

				$data = array('is_scheme_selected' => 1);

				$this->session->set_userdata($data);

				$status = array("status" => true, "insertID" => $insertID, "sch_data"=>$sch_data);

				

			}

			else

			{

				$status = array("status" => false, "insertID" => '');

			}

			return $status;

	}
	
	
	function update_account($data,$id)
	{
		
		$this->db->where('id_scheme_account',$id);
		$status=$this->db->update('scheme_account',$data);
		
		return $status;
	}

/* Coded by ARVK*/	
	function get_receipt_no()
    {
		$sql = "Select max(receipt_no) as receipt_no
				From payment
				Where payment_status=1";
		return $this->db->query($sql)->row()->receipt_no;		
	}
/* / Coded by ARVK*/

	function join_existing($scheme_acc)

	{

				

			if($this->db->insert('scheme_account', $scheme_acc))

			{

				$insertID = $this->db->insert_id();

				$data = array('is_scheme_selected' => 1);

				$this->session->set_userdata($data);

				$status = array("status" => true, "insertID" => $insertID);

			}

			else

			{

				$status = array("status" => false, "insertID" => '');

			}

			return $status;

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

	

	function account_number_generator($id_scheme)

	{

	  $lastno=$this->get_schAccount_no($id_scheme);

	  if($lastno!=NULL)

		{

		  	$number = (int) $lastno;

			$number++;

    		$schAc_number=str_pad($number, 5, '0', STR_PAD_LEFT);;

    		return $schAc_number;

		}

		else

		{

				

			$schAc_number=str_pad('1', 5, '0', STR_PAD_LEFT);;

    		return $schAc_number;

			 

		}

		

	}

	

	function get_schAccount_no($id_scheme)

    {

		$sql = "SELECT max(scheme_acc_number) as lastSchAcc_no FROM scheme_account where id_scheme=".$id_scheme." ORDER BY id_scheme_account DESC";

		return $this->db->query($sql)->row()->lastSchAcc_no;		

	}

	

	//check reference exists

	function is_refno_exists($ref_no)

	{

		$this->db->select('ref_no');

		$this->db->where('ref_no', $ref_no); 

		$status=$this->db->get(self::ACC_TABLE);

	    

		if($status->num_rows()>0)

		{

			 return TRUE;

		}

	}

	function get_currentScheme()

	{

		$records = array();

		$query_scheme = $this->db->query('SELECT sa.id_scheme_account as scheme_account ,s.id_scheme as id_scheme,scheme_name, scheme_type, description,amount,total_installments, min_weight, max_weight, min_chance,max_chance, interest_by, interest_value, payment_amount, date_payment, id_payment,metal_rate,metal_weight FROM scheme_account as sa LEFT JOIN scheme as s ON s.id_scheme = sa.id_scheme LEFT JOIN customer as cus ON cus.id_customer = sa.id_customer LEFT JOIN payment as p ON p.id_scheme_account = sa.id_scheme_account AND MONTH(date_payment) = MONTH(CURDATE()) WHERE mobile="'.$this->session->userdata('username').'"');

			if($query_scheme->num_rows() > 0)

			{

				foreach($query_scheme->result() as $row)

				{

					$records[] = array('scheme_account' => $row->scheme_account,'id_scheme' => $row->id_scheme, 'scheme_name' => $row->scheme_name,'description' => $row->description,'scheme_type' => $row->scheme_type, 'amount' => $row->amount,'total_installments' => $row->total_installments,'min_weight' => $row->min_weight,'max_weight' => $row->max_weight, 'min_chance' => $row->min_chance,'max_chance' => $row->max_chance, 'interest_by' => $row->interest_by,'interest_value' => $row->interest_value,'payment_amount' => $row->payment_amount,'date_payment' => $row->date_payment,'id_payment' => $row->id_payment,'metal_rate' => $row->metal_rate,'metal_weight' => $row->metal_weight);

				}

			}

		$records = array();

		$query_weight = $this->db->query('SELECT * FROM  weight');

			if($query_weight->num_rows() > 0)

			{

				foreach($query_weight->result() as $row)

				{

					$weight[] = array('id_weight' => $row->id_weight,'weight' => $row->weight);

				}

			}

			

		$query_scheme->free_result();

		return array('scheme' => $records,'weight' => $weight);

	}

	

	function getJoinedScheme($schemeID)

	{

		$records = array();

		$query_scheme = $this->db->query('SELECT sa.id_scheme_account as scheme_account ,s.id_scheme as id_scheme,scheme_name,  ifnull(sa.scheme_acc_number,concat(s.code ,"-","Not Allocated")) as scheme_acc_number, scheme_type,email,firstname,lastname,s.amount,s.max_weight,s.min_weight,s.payment_chances,s.total_installments,sa.account_name,cs.currency_symbol,cs.currency_name

	FROM scheme_account as sa 

	join chit_settings cs

	LEFT JOIN scheme as s ON s.id_scheme = sa.id_scheme LEFT JOIN customer as cus ON cus.id_customer = sa.id_customer WHERE sa.id_scheme_account='.$schemeID);

			if($query_scheme->num_rows() > 0)

			{

				foreach($query_scheme->result() as $row)

				{

					$records[] = array('scheme_account' => $row->scheme_account,'id_scheme' => $row->id_scheme, 'scheme_name' => $row->scheme_name,'scheme_type' => $row->scheme_type,'scheme_acc_number' => $row->scheme_acc_number,'email' => $row->email,'firstname' => $row->firstname,'lastname' => $row->lastname,'amount' => $row->amount,'max_weight' => $row->max_weight,'min_weight' => $row->min_weight,'payment_chances' => $row->payment_chances,'total_installments' => $row->total_installments,'account_name' => $row->account_name,'currency_symbol' => $row->currency_symbol,'currency_name' => $row->currency_name);

				}

			}

		

		$query_scheme->free_result();

		return $records;

	}

	public function get_closeScheme()

	{

		$records = array();

		$query = $this->db->query("SELECT `ref_no` AS `scheme_acc_number`, `scheme_name`, `scheme_account`.`id_scheme_account` AS `id_scheme_account`, `req_close`, `total_installments`, IFNULL(`paid_installments`,0) + IFNULL(`totalpay`.`paidIns`,0) AS `paid_installments` FROM (`scheme_account`) LEFT JOIN `scheme` ON `scheme`.`id_scheme` = `scheme_account`.`id_scheme` LEFT JOIN (SELECT id_scheme_account,COUNT(DISTINCT(DATE_FORMAT(date_payment,'%Y%m'))) AS paidIns from payment WHERE (payment_status = 0 OR payment_status = 1) GROUP BY id_scheme_account) AS totalpay ON `totalpay`.`id_scheme_account` = `scheme_account`.`id_scheme_account` WHERE `is_closed` != 1 AND `scheme_account`.`id_customer` = ".$this->session->userdata('cus_id'));

		if($query->num_rows() > 0)

		{

			foreach($query->result() as $row)

			{

				$records[] =  array('id_scheme_account' => $row->id_scheme_account,'scheme_acc_number' => $row->scheme_acc_number, 'scheme_name' => $row->scheme_name, 'paid_installments' => $row->paid_installments,'total_installments' => $row->total_installments, 'req_close' => $row->req_close);

			}

		}

		return array("schemeClose" =>  $records);

		

	}

	function close_scheme()

	{

		if($this->input->post('remark_close'))

			$updateData = array("remark_close" => $this->input->post('remark_close'),"req_close"=>1);

		else

			$updateData = array("remark_close" => '',"req_close"=>0);

			

		$this->db->where('id_scheme_account', $this->input->post('id_scheme_account'));

		if($this->db->update("scheme_account",$updateData))

		{

			return true;

		}

		else

		{

			return false;

		}

	}

	

	function verify_existing($scheme_acc_number)

	{

		$this->db->select('id_scheme_account,id_customer');

		$this->db->where('scheme_acc_number',$scheme_acc_number);

		$schAcc = $this->db->get('scheme_account');

		if($schAcc->num_rows() > 0)

		{

			return TRUE;

		}	

	}

	

	function scheme_status()

	{

		if($this->session->userdata('cus_id'))

			$cus_id = $this->session->userdata('cus_id');

		else

			$cus_id = 0;

		$scheme_status = 0;

		

		$query_scheme = $this->db->query('SELECT * FROM scheme_account AS schAcc LEFT JOIN payment AS pay ON pay.id_scheme_account = schAcc.id_scheme_account WHERE is_closed = 0 AND id_customer='.$cus_id);

				if($query_scheme->num_rows() > 0)

				{

					$scheme_status = 1;

					foreach($query_scheme->result() as $row)

					{

						if($row->id_payment !== NULL)

						{

							$scheme_status = 2;

						}

					}

				}

			$query_scheme->free_result();

		

		return $scheme_status;

	}

	function no_avail_schemes()

	{

		$records = array();

		$query_scheme = $this->db->query('SELECT id_scheme,code,scheme_name FROM scheme WHERE active  = 1');

				if($query_scheme->num_rows() > 0)

				{

					foreach($query_scheme->result() as $row)

					{

						$records[] = array("id_scheme" => $row->id_scheme,"code" => $row->code,"scheme_name" => $row->scheme_name);

					}

				}

			$query_scheme->free_result();

		

		return array('scheme' => $records);

	}

	

	function check_unpaid_schemes($id_customer)

	{

		$sql="SELECT * FROM scheme_account sa

			  Left Join payment p ON (sa.id_scheme_account=p.id_scheme_account And p.payment_status=1)

			Where sa.id_customer ='$id_customer' and is_closed=0 and p.id_scheme_account IS NULL and sa.paid_installments=0

			GROUP BY id_scheme";

		$unpaid = $this->db->query($sql);	

	

		if($unpaid->num_rows()>0)

		{

			return TRUE;

		}	

	}

	/*

	function check_unpaid_schemes($id_customer)

	{

		$sql="SELECT sa.id_scheme_account,IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),1,0) as  previous_paid FROM scheme_account sa

			  Left Join payment p ON (sa.id_scheme_account=p.id_scheme_account And p.payment_status=1)

			Where sa.id_customer ='$id_customer' and is_closed=0 and p.id_scheme_account IS NULL 

			GROUP BY id_scheme";

		$unpaid = $this->db->query($sql);	

		$result = $unpaid->result_array();	

	

		if($unpaid->num_rows()>0)

		{

			if($result['last_paid_date'] != NULL){

				return ($result['previous_paid'] == 1 ? FALSE : TRUE);

			}

			else{

				return TRUE;	

			}

			

		}	

	}*/

	

	function hasUnclosedAccounts($id_customer)

	{

		$sql="SELECT COUNT(id_scheme_account) as accounts

				FROM scheme_account

				WHERE is_closed=0 and id_customer ='$id_customer'

				GROUP BY id_customer";

		$unpaid = $this->db->query($sql);	

	

		if($unpaid->num_rows()>0)

		{

			return TRUE;

		}	

	}

	

	//to check whether to allow customers to join multiple scheme

	function allowMultipleChits()

	{

		$sql ="Select allow_join_multiple From chit_settings";

		$allow_multiple = $this->db->query($sql);	

		

		if($allow_multiple->row('allow_join_multiple')==1)

		{

			return TRUE;			

		}		

		

	}	

	

	//to check whether to allow customers to join even if any unpaid chit exists

	function allowUnpaid()

	{

		$sql ="Select allow_join_unpaid From chit_settings";

		$allow_multiple = $this->db->query($sql);	

	   if($allow_multiple->row('allow_join_unpaid')==1)

		{

			return TRUE;			

		}

		

	}	

	//to check whether to allow customers to delete unpaid chit exists

	function deleteUnpaid()

	{

		$sql ="Select delete_unpaid From chit_settings";

		$allow_multiple = $this->db->query($sql);	

	   if($allow_multiple->row('delete_unpaid')==1)

		{

			return TRUE;			

		}

		

	}

	

	//to check whether to allow customers to register existing scheme

	function regExistingScheme()

	{

		$sql ="Select reg_existing From chit_settings";

		$allow_multiple = $this->db->query($sql);	

		$result=$this->db->query($sql);	   

		return $result->row('reg_existing');	

	}

	

	//to show closed accounts list to cutomers

	function showClosedAcc()

	{

		$sql ="Select show_closed_list From chit_settings";

		$allow_multiple = $this->db->query($sql);	

		$result=$this->db->query($sql);	   

		return $result->row('show_closed_list');	

	}

	

	function delete_scheme_account($id)

	{

		$this->db->where("id_scheme_account",$id);

		$status = $this->db->delete("scheme_account");

		return	array('status' => $status, 'DeleteID' => $id);

		

	}	
	
	function delete_payment($id)
	{
		
		$this->db->where("id_payment",$id);

		$status = $this->db->delete("payment");

		return	array('status' => $status, 'DeleteID' => $id);

	}	
	function get_payment($id)
	{
		
		$payid=$this->db->query("SELECT p.id_payment FROM payment p where p.id_scheme_account=".$id."");
		return $payid->row_array();
	}	 

	

	function get_scheme_detail()

	{

		$accounts=$this->db->query("Select
										sa.id_scheme_account,
										s.id_scheme,
										c.id_customer,
										IF(c.lastname IS NULL,c.firstname,CONCAT(c.firstname,' ',c.lastname)) customer_name,
										ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,
										IFNULL(sa.account_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,
										c.mobile,cs.currency_name,cs.currency_symbol,
										s.scheme_name,
										IF(s.scheme_type=0,'Amount',IF(s.scheme_type=1,'Weight','Amount to Weight')) AS scheme_type,ifnull(p.add_charges,0.00) as add_charges,
										s.code,
										IFNULL(s.min_chance,0) as min_chance,
										IFNULL(s.max_chance,0) as max_chance,
										Format(IFNULL(s.max_weight,0),3) as max_weight,
										Format(IFNULL(s.min_weight,0),3) as min_weight,
										Date_Format(sa.start_date,'%d-%m-%Y')start_date,
										IF(s.scheme_type=1,s.max_weight,s.amount) as payable,
										s.total_installments,s.amount,
										
										IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)
  as paid_installments,		 
										  
IFNULL(IF(sa.is_opening=1,IFNULL(balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0)
  as total_paid_amount,
  
  FORMAT(sum(if(p.gst > 0,if((p.gst_type = 1),0,p.payment_amount-(p.payment_amount*(100/(100+p.gst)))),0)),0) as paid_gst,
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
										pp.total_pdc as pdc,
										pp.pdc_status as pdc_status,
										(select SUM(CASE
					                                WHEN p.payment_status=1 OR p.payment_status=2  THEN 1
					                                ELSE 0
					                                END) as has_payment
					                      from payment p where p.id_scheme_account=sa.id_scheme_account)as has_payment,
										Date_Format(pp.last_paid_date,'%d-%m-%Y') as last_pdc_date,br.name as branch_name,br.id_branch
									From scheme_account sa
									Left Join scheme s On (sa.id_scheme=s.id_scheme)
									Left Join branch br On (sa.id_branch=br.id_branch)
									Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=1 or p.payment_status = 2))
									Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)
									join chit_settings cs
										Left Join
													(	Select
														  sa.id_scheme_account,
														  COUNT(Distinct Date_Format(p.date_payment,'%Y%m')) as paid_installment,
														  COUNT(Date_Format(p.date_payment,'%Y%m')) as chances,
														  SUM(p.payment_amount) as total_amount,
														  SUM(p.metal_weight) as total_weight
														From payment p
														Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)
														Where p.payment_status=1 and Date_Format(Current_Date(),'%Y%m')=Date_Format(p.date_payment,'%Y%m')
														Group By sa.id_scheme_account
													) cp On (sa.id_scheme_account=cp.id_scheme_account)
										Left Join(Select
													    id_scheme_account,payment_status as pdc_status,
													    Count(id_post_payment) as total_pdc,
													    Max(date_payment) as last_paid_date
													From postdate_payment
													Where payment_status!=1
													Group By id_scheme_account
											      ) pp on (sa.id_scheme_account=pp.id_scheme_account)				
									Where sa.active=1 and sa.is_closed = 0 and sa.id_customer=".$this->session->userdata('cus_id')."
									Group By sa.id_scheme_account");

				

		return $accounts->result_array();

	}

	

	

		

		//report

	  function get_account_details($id_scheme_account)

	{

		$sql="SELECT c.id_customer, c.cus_img, ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_ac, sa.account_name,concat(c.firstname,' ',c.lastname)as name,
			c.mobile, DATE_FORMAT(sa.`start_date`,'%d-%m-%Y') as start_date,
			IF(s.scheme_type=0,'Amount',IF(s.scheme_type=1,'Weight','Amount to Weight')) AS scheme_type, s.code as scheme_code, s.total_installments,
			s.max_weight, IF(s.scheme_type=1 , s.max_weight,s.amount) as payable, if(sa.balance_amount is null,0,sa.balance_amount) as balance_amount,
			 if(sa.balance_weight is null,0,sa.balance_weight) as balance_weight, lg.paid_ins as paid_installments, s.scheme_type as type, cs.currency_name,
			 cs.currency_symbol, sa.paid_installments as ins,a.address1,a.address2,a.address3,st.name as state,ct.name as city,cy.name as country,a.pincode
			 from customer c
				 left join scheme_account sa on(sa.id_customer=c.id_customer)
				 left join scheme s on(s.id_scheme=sa.id_scheme)
				 left join address a on c.id_customer=a.id_customer
				 left join country cy on (a.id_country=cy.id_country)
				 left join state st on (a.id_state=st.id_state)
				 left join city ct on (a.id_city=ct.id_city)
				 left join ( select 
				 		   sch.id_scheme_account ,
						   IFNULL(IF(sch.is_opening=1,IFNULL(sch.paid_installments,0)+ IFNULL(if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight ,COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0),if(sc.scheme_type = 1 and sc.min_weight != sc.max_weight , COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) as paid_ins
						 From payment pay
						   Left Join scheme_account sch on(pay.id_scheme_account=sch.id_scheme_account and sch.active=1 and sch.is_closed=0)
						   Left Join scheme sc on(sc.id_scheme=sch.id_scheme) 
						   Where (pay.payment_status=2 or pay.payment_status=1) 
						   Group By sch.id_scheme_account) lg on (sa.id_scheme_account=lg.id_scheme_account )
					join chit_settings cs
				WHERE sa.id_scheme_account='$id_scheme_account' AND c.id_customer=".$this->session->userdata('cus_id')."";

		$account=$this->db->query($sql);	   

		return $account->row_array();	

	}

	//Amount

	function get_payment_detail($id_scheme_account)

	{

		$sql="Select 

				p.id_payment  as ins_no,

				DATE_FORMAT(p.date_payment,'%d-%m-%Y') as date_payment,

				p.payment_mode as mode,

				s.scheme_type as type,

				if(sa.balance_amount is null,0,sa.balance_amount) as balance_amount,

				p.payment_amount,ifnull(p.add_charges,0.00) as add_charges,

				cs.currency_name,

                cs.currency_symbol,p.gst,p.gst_type ,psm.payment_status as payment_status

			FROM payment p

				left join scheme_account sa on(sa.id_scheme_account=p.id_scheme_account)

				left join scheme s on(s.id_scheme=sa.id_scheme)

				left join customer c on(c.id_customer=sa.id_customer)

				join chit_settings cs
				
				LEFT Join payment_status_message psm ON (p.payment_status=psm.id_status_msg)

			WHERE sa.id_scheme_account = '$id_scheme_account' and (p.payment_status=1 or p.payment_status=2) and s.scheme_type=0";

				$payments=$this->db->query($sql);

				return $payments->result_array();

			

	}

	

	//report weight

	function get_details($id_scheme_account)

	{

		$sql="Select 

				p.id_payment as ins_no,

				DATE_FORMAT(p.date_payment,'%d-%m-%Y') as date_payment,

				p.payment_mode as mode,

				s.scheme_type as type,

				if(sa.balance_amount is null,0,sa.balance_amount) as balance_amount,

				if(sa.balance_weight is null,0,sa.balance_weight) as balance_weight,

				p.payment_amount,

				p.metal_rate,
ifnull(p.add_charges,0.00) as add_charges,
				p.metal_weight,

				cs.currency_name,

                cs.currency_symbol ,p.gst,p.gst_type,psm.payment_status as payment_status

			FROM payment p

				left join scheme_account sa on(sa.id_scheme_account=p.id_scheme_account)

				left join scheme s on(s.id_scheme=sa.id_scheme)

				left join customer c on(c.id_customer=sa.id_customer)
				
				LEFT Join payment_status_message psm ON (p.payment_status=psm.id_status_msg)

					join chit_settings cs

			WHERE sa.id_scheme_account = '$id_scheme_account' and (p.payment_status=1 or p.payment_status=2) and (s.scheme_type=1 or s.scheme_type=2)";

				$payments=$this->db->query($sql);

				return $payments->result_array();

			

	}

	

	//to get closed account by customer

	 function get_closed_account()

	{

		$accounts=$this->db->query("select

		 s.id_scheme_account,s.scheme_acc_number,concat (c.firstname,' ',if(c.lastname!=Null,c.lastname,'')) as name,s.ref_no,s.account_name,Date_Format(s.start_date,'%d-%m-%Y') as start_date,Date_Format(s.closing_date,'%d-%m-%Y') as closing_date,s.closing_balance,

		 sc.scheme_name,sc.code,IF(sc.scheme_type=0,'Amount',IF(sc.scheme_type=1,'Weight','Amount to Weight')) AS scheme_type,sc.total_installments,sc.max_chance,sc.amount,c.mobile

		from

		 scheme_account s

		left join customer c on (s.id_customer=c.id_customer)

		left join scheme sc on (sc.id_scheme=s.id_scheme)

		where s.active=0 and s.is_closed=1 and s.id_customer=".$this->session->userdata('cus_id'));

		if($accounts->num_rows > 0){
		return $accounts->result_array();
		}
		else{
		return false;
		}
	}
	
	function isAccExist_bymobile($data)
	{
		$resultset = $this->db->query("SELECT is_acc_registered, REPLACE(mobile_no,'-','') as mobile_no from chit_customer where is_acc_registered=0 and REPLACE(mobile_no,'-','') ='".$data['scheme_mob_number']."'");

		if($resultset->num_rows() > 0 ){
			 if($resultset->row()->mobile_no != NULL && $resultset->row()->is_acc_registered==0){
				 if(strlen($resultset->row()->mobile_no) == 10){
					return array('status'=>TRUE , 'mobile'=>$resultset->row()->mobile_no,'msg'=>'We will send OTP to mobile number associated with this account');
				 }else{
					 return array('status'=> FALSE,'msg'=>'Please visit our branch to update valid mobile number');
				 }
			 }
			 else if($resultset->row()->is_acc_registered == 1){
			 	return array('status'=> FALSE,'msg'=>'Account already registered');	
			 }
			 else{
			 	return array('status'=> FALSE,'msg'=>'Update mobile number in our branch');	
			 }
		}else{
			 	return array('status'=> FALSE,'msg'=>'No schemes are available for this mobile number');	
		}
	}
	//NOTE : group_cus_no -> a/c no , group_name -> group code
	function isAccExist($data) 
	{
	$resultset = $this->db->query("select is_acc_registered,REPLACE(mobile_no,'-','') as mobile_no from chit_customer where group_cus_no='".$data['scheme_acc_number']."' and group_name='".$data['group_name']."'");

		if($resultset->num_rows() > 0 ){
			 if($resultset->row()->mobile_no != NULL && $resultset->row()->is_acc_registered==0){
				 if(strlen($resultset->row()->mobile_no) == 10){
					return array('status'=>TRUE , 'mobile'=>$resultset->row()->mobile_no,'msg'=>'We will send OTP to mobile number associated with this account');
				 }else{
					 return array('status'=> FALSE,'msg'=>'Please visit our branch to update valid mobile number');
				 }
			 }
			 else if($resultset->row()->is_acc_registered == 1){
			 	return array('status'=> FALSE,'msg'=>'Account already registered');	
			 }
			 else{
			 	return array('status'=> FALSE,'msg'=>'Update mobile number in our branch');	
			 }
		}else{
			 	return array('status'=> FALSE,'msg'=>'Enter valid details');	
			}
	}
	
	function insertExisAccData($data) 
	{
		$resultset = $this->db->query("select * from chit_customer where GROUP_CUS_NO='".$data['scheme_acc_number']."' and GROUP_NAME='".$data['group_name']."'");
		if($resultset->num_rows() == 1 ){
			$records = array();
		foreach($resultset->result() as $row)
			{
				$data['scheme_type']  = $row->scheme_type; 
				$data['amount']       = $row->AMOUNT; 
				$data['group_name']   = $row->GROUP_NAME; 
				
				$records = array( 	'id_customer' 		=> $data['id_customer'],
									'id_scheme'			=> $this->getschId($data),
									'scheme_acc_number' => $row->GROUP_CUS_NO,
									'account_name' 		=> $row->NAME,
									'start_date' 		=> $row->ENTRYDATE,
									'is_new' 			=> $row->is_new,
									'date_add' 			=> date("Y-m-d H:i:s"),
									'is_registered' 	=> 1,
									'active' 			=> 1,
									'is_opening' 		=> $row->is_opening,
									'balance_amount' 	=> $row->balance_amount,
									'balance_weight'	=> $row->balance_weight,
									'last_paid_weight' 	=> $row->last_paid_weight,
									'last_paid_chances'	=> $row->last_paid_chances,
									'last_paid_date' 	=> $row->last_paid_date,
									'paid_installments' => $row->paid_installments,
									'added_by' 			=> 1
								);
								
				$addData=$this->get_cityData($row->CITY_NAME);
				$address = array( 'id_customer' =>$data['id_customer'],
							  'address1' =>$row->ADDRESS1,
							  'address2' =>$row->ADDRESS2,
							  'address3' =>$row->ADDRESS3,
							  'id_city'	 =>$addData['id_city'],
							  'id_state' =>$addData['id_state']
							);
			}
			$sql =$this->db->query("select * from address where id_customer=".$data['id_customer']);
			
			if($sql->num_rows() > 0){
				$this->db->where('id_customer',$data['id_customer']);
				$updateCus = $this->db->update('address',$address);
			} 
			else{				
				$updateCus = $this->db->insert('address',$address);		
			}
			//$updateCus = $this->db->insert('address',$address);	
			
			//echo $this->db->last_query();exit;
		//	$updateCus=TRUE;
			if($updateCus){
				$status = $this->db->insert('scheme_account',$records);
					
						//echo '$updateCus-true';exit;
				return array('status' =>  $status, 'data' => $data,'insertID' => $this->db->insert_id());
			}			
			else{
				
						//echo '$updateCus-false';exit;	
				return array('status' =>  $updateCus);
			}
		}else{
			 	return array('status'=> FALSE,'msg'=>'Unable to proceed your request,try again later or contact customer care');	
			 }
		}
		
		function getschId($data) 
		{
			if($data['scheme_type'] == 1){
				$result = $this->db->query("SELECT id_scheme FROM scheme s where s.code='".$data['group_name']."' and scheme_type='".$data['scheme_type']."'");
			}
			else{
				$result = $this->db->query("SELECT id_scheme FROM scheme s where s.amount='".$data['amount']."' and scheme_type='".$data['scheme_type']."'");
			}
			
			if($result->num_rows() > 0 ){
					return $result->row()->id_scheme;
				}
				else{
						return '';
				}
			
		}
		
		function get_cityData($city) 
		{
			$result = $this->db->query("select id_city,id_state from city where name='".$city."'");	
			return $result->row_array();
		}
		
		function insert_paymentData($data,$id) 
		{
			$resultset = $this->db->query("select * from chit_transaction where group_cus_no='".$data['scheme_acc_number']."' and group_name='".$data['group_name']."'");
			$i = 1;
			if($resultset->num_rows() > 0 ){
				$records = array();
			foreach($resultset->result() as $row)
				{				
					$records = array( 	'id_scheme_account' =>$id,
										'metal_rate'		=>$row->GOLD_RATE,
										'receipt_no' 		=>$row->RECEIPT_NO,
										'metal_weight' 		=>$row->WEIGHT,
										'payment_amount'	=>$row->AMOUNT,
										'date_payment'		=>$row->RECEIPT_DATE,
										'payment_status'	=>1,
										'date_add' 			=>$row->RECEIPT_DATE,
										'payment_mode' 		=>($row->is_free_payment == 1?'FP':$row->CASH_TYPE) ,	
										'dues' 				=>$row->NO_OF_INSTAL,
										'payment_type' 		=>'Manual',
										'is_offline'		=> 1,
										'due_type' 			=>$row->due_type,
										'added_by' 			=>0, //admin
										'is_offline' 		=>1,
										'discountAmt' 		=>$row->discountAmt	
										);
										
					$status = $this->db->insert('payment',$records);
					$i++;
					//echo $this->db->last_query();
				}

				return array('status' =>  $status);
			}
			else{
				 	
				 	return array('status'=> True,'msg'=>'No payment records found');	
				 }
		}
			
			
		function updateOfflineData($data)
		{	
			$arrdata=array("is_acc_registered"=>1);
			$this->db->where('group_name',$data['group_name']); 
			$this->db->where('group_cus_no',$data['scheme_acc_number']); 
			$status= $this->db->update('chit_customer',$arrdata);
			return $status;
		} 
		
		function insertExisAccData_bymobileno($data)
		{
			$resultset = $this->db->query("SELECT * FROM chit_customer WHERE mobile_no='".$data['scheme_mob_number']."' and is_acc_registered=0");
		
			if($resultset->num_rows() > 0 ){
				$response_data = array();
				foreach($resultset->result() as $key =>$row)
				{
					$records = array();
					$data['scheme_type']  = $row->scheme_type; 
					$data['amount']  = $row->AMOUNT; 
					$data['scheme_acc_number'] = $row->GROUP_CUS_NO;
					$data['group_name'] = $row->GROUP_NAME;
					
					$records = array(   'id_customer' 		=>$data['id_customer'],
										'id_scheme' 		=>$this->getschId($data),
										'scheme_acc_number' =>$row->GROUP_CUS_NO,
										'account_name' 		=>$row->NAME,
										'start_date' 		=>$row->ENTRYDATE,
										'is_new' 			=>'N',
										'date_add' 			=>date("Y-m-d H:i:s"),
										'is_registered'		=>1,
										'active'			=>1,
										'is_opening' 		=> $row->is_opening,
										'balance_amount' 	=> $row->balance_amount,
										'balance_weight'	=> $row->balance_weight,
										'last_paid_weight' 	=> $row->last_paid_weight,
										'last_paid_chances'	=> $row->last_paid_chances,
										'last_paid_date' 	=> $row->last_paid_date,
										'paid_installments' => $row->paid_installments,
										'added_by' 			=> 1
									);
					$addData=$this->get_cityData($row->CITY_NAME);
					$address = array( 'id_customer' =>$data['id_customer'],
									  'address1' 	=>$row->ADDRESS1,
									  'address2' 	=>$row->ADDRESS2,
									  'address3' 	=>$row->ADDRESS3,
									  'id_city'	 	=>$addData['id_city'],
									  'id_state'	=>$addData['id_state']
									);
					
					$sql =$this->db->query("SELECT * FROM address WHERE id_customer=".$data['id_customer']);
					if($sql->num_rows() > 0){
						$this->db->where('id_customer',$data['id_customer']);
						$updateCus = $this->db->update('address',$address);
					} 
					else{
						$updateCus = $this->db->insert('address',$address);
					}
					if($updateCus){
						$status = $this->db->insert('scheme_account',$records);
						$data['insertID'] = $this->db->insert_id();
					}
					$response_data[$key] = $data;
				}
				if($updateCus){
					return array('status' =>  $status, 'data' => $response_data);
				}else{
					return array('status' =>  $updateCus);
				}
			}else{
				 return array('status'=> FALSE, 'msg' => 'Unable to proceed your request, try again later or contact customer care');	
			}
		}
		function insert_paymentData_bymobile($data)
		{
			foreach($data as $key => $val){
				$resultset = $this->db->query("SELECT * FROM chit_transaction WHERE group_cus_no='".$val['scheme_acc_number']."' and group_name='".$val['group_name']."'");
				$i = 1;
				if($resultset->num_rows() > 0 ){
					$records = array();
					foreach($resultset->result() as $row)
					{				
					
						$records = array( 	'id_scheme_account' =>$val['insertID'],
											'metal_rate'		=>$row->GOLD_RATE,
											'receipt_no' 		=>$row->RECEIPT_NO,
											'metal_weight' 		=>$row->WEIGHT,
											'payment_amount'	=>$row->AMOUNT,
											'date_payment' 		=>$row->RECEIPT_DATE,
											'payment_status' 	=>1,
											'date_add' 			=>$row->RECEIPT_DATE,
											'payment_mode' 		=>($row->is_free_payment == 1?'FP':$row->CASH_TYPE) ,	
											'dues' 				=>$row->NO_OF_INSTAL,
											'payment_type' 		=>'Manual',
											'is_offline'		=> 1,
											'due_type' 			=>$row->due_type,
											'added_by' 			=>0, //admin
											'is_offline' 		=>1,
											'discountAmt' 		=>$row->discountAmt	
											);
						$status = $this->db->insert('payment',$records);
						$i++;
					}
				}
			}
			if($status){
				return array('status' =>  $status);
			}else{
				return array('status'=> TRUE, 'msg'=>'No payment records found');	
			}
		}
		function updateOfflineData_bymobile($data)
		{
			foreach($data as $key => $val){
				$arrdata=array("is_acc_registered"=>1);
				//$this->db->where('branch',$branch_code); 
				$this->db->where('group_name',$val['group_name']); 
				$this->db->where('group_cus_no',$val['scheme_acc_number']); 
				$status= $this->db->update('chit_customer',$arrdata);
			}
			return $status;
		}
		
		public function check_cusrefcode($cusref_code)
		{
			$this->db->select('id_customer');
			$this->db->where('id_customer',$cusref_code); 
			$status=$this->db->get('customer');
			if($status->num_rows()>0)
			{
				return TRUE;
			}
		}
		
		public function check_emprefcode($emp_code)
		{
			$this->db->select('id_employee');
			$this->db->where('id_employee',$emp_code); 
			$status=$this->db->get('employee');
			if($status->num_rows()>0)
			{
				return TRUE;
			}
		}
		
		
// branch name list 		
		
	function get_branch()
	{
		
		$sql = "SELECT * FROM branch b";
		
		$branch = $this->db->query($sql)->result_array();		
		return $branch;
		
	} 
	
// branch name list 		
		
		
		
		
}

?>