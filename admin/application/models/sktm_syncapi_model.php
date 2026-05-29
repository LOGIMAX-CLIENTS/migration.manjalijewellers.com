<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Sktm_syncapi_model extends CI_Model
{
    const TAB_CUS     = "customer";
	const TAB_ACC     = "scheme_account";
	const TAB_SCH     = "scheme";
	const TAB_PAY     = "payment";
	
	function __construct()
    {      
        parent::__construct();
    }
    
    function getTransactionByStatus($trans_status,$id_branch,$record_to)
	{
	    $today = date("Y-m-d");
	    if($id_branch == ''){
	        $sql = "SELECT * FROM transaction WHERE record_to='$record_to' and is_transferred='$trans_status' and date(date_add) =  '".$today."'";
	    }else{
	       	$sql = "SELECT * FROM transaction WHERE id_branch='$id_branch' and record_to='$record_to' and is_transferred='$trans_status' and date(date_add) = '".$today."'"; 
	    }
	
		return $this->db->query($sql)->result_array();
	}
	
    function getcustomerByStatus($trans_status,$id_branch,$record_to){	
        if($id_branch == ''){
	        $sql = "SELECT * FROM customer_reg WHERE is_modified=1 and is_registered_online>=1 and record_to=".$record_to." and is_transferred='$trans_status'  limit 1500";
	    }else{
	       	$sql = "SELECT * FROM customer_reg WHERE id_branch='$id_branch' and record_to=".$record_to." and is_transferred='$trans_status' limit 1500"; 
	    }
	
		return $this->db->query($sql)->result_array();
    }
	
	
	function update_account($data,$id_scheme_account,$id_customer_reg) 
	{
		$this->db->where('id_scheme_account',$id_scheme_account);
		$status = $this->db->update('scheme_account',$data);
		if($status){
			$cus_reg = array( 'is_transferred' => 'Y',
		                    'is_modified'    => 0,
        					"date_update"		 => date('Y-m-d'),
        					'transfer_date'	 => date('Y-m-d'));
		    $this->db->where('id_customer_reg',$id_customer_reg);
    		$status = $this->db->update('customer_reg',$cus_reg);
		}
	//	echo $this->db->last_query();
		return $status;
	}
	
	function update_accountByClientId($data,$clientId,$id_customer_reg) 
	{
		$this->db->where('ref_no',$clientId);
		$status = $this->db->update('scheme_account',$data);
		if($status){
			$cus_reg = array( 'is_transferred' => 'Y',
		                    'is_modified'    => 0,
        					"date_update"		 => date('Y-m-d'),
        					'transfer_date'	 => date('Y-m-d'));
		    $this->db->where('id_customer_reg',$id_customer_reg);
    		$status = $this->db->update('customer_reg',$cus_reg);
		}
	//	echo $this->db->last_query();
		return $status;
	}
	
	function updatePayment($data,$payType,$id_sch_ac,$payDt) 
	{
		$pay_ref_no = $data['payment_ref_number'];
		$sql = $this->db->query("select * from payment where payment_ref_number='$pay_ref_no'");
			
		if($sql->num_rows() > 1){
			return FALSE;
		}
		else{
		    if($payType == 1 && $data['payment_status'] == 1){
		        $this->db->where(array('id_payment' => $data['payment_ref_number'],'id_scheme_account'=>$id_sch_ac));
		    }else{
		        $this->db->where(array('id_scheme_account'=>$id_sch_ac,'date_payment'=>$payDt));
		    }
		    
			
			$status = $this->db->update('payment',$data);
			
			if($status){
				$trans = array( 'is_transferred' => 'Y',
			                    'is_modified'    => 0,
            					"date_upd"		 => date('Y-m-d'),
            					'transfer_date'	 => date('Y-m-d'));
    		    $this->db->where('ref_no',$data['payment_ref_number']);
        		$status = $this->db->update('transaction',$trans);
			}
			return $status;
		}		
	}
	
	function checkClientID($id_scheme_account="",$client_id="")
	{		
	    if($id_scheme_account == "" && $client_id != ""){
	        $sql = "select id_scheme_account,ref_no from scheme_account where ref_no = '$client_id'";
	    }else{
	        $sql = "select id_scheme_account,ref_no from scheme_account where id_scheme_account = ".$id_scheme_account;
	    }
		
		$account = $this->db->query($sql);	
		if($account->num_rows()>0 && $account->row()->ref_no != '')
		{
			return array("status" => TRUE, "client_id" => $account->row()->ref_no,'id_scheme_account'=>$account->row()->id_scheme_account );
		}
		else
		{
			return array("status" => FALSE);
		}	

	}
	function insertPayment($data)
	{
		$data['is_offline'] = 1;
		$pay_ref_no = $data['payment_ref_number'];
		$sql = $this->db->query("select * from payment where payment_ref_number='$pay_ref_no'");
			
		if($sql->num_rows() > 0){
			return FALSE;
		}
		else{
			$status = $this->db->insert('payment',$data);
			if($status){	
			    $trans = array( 'is_transferred' => 'Y',
			                    'is_modified'    => 0,
            					"date_upd"		 => date('Y-m-d'),
            					'transfer_date'	 => date('Y-m-d'));
    		    $this->db->where('ref_no',$data['payment_ref_number']);
        		$status = $this->db->update('transaction',$trans);
			}
			return $status;
		}		
	}
	
	function get_branchid($branch_code)
    {	
        $sql = "SELECT id_branch FROM branch where short_name=".$branch_code;
		return $this->db->query($sql)->row()->id_branch; 
    }
    
 
	function  getPaymentByID($id_payment)
	{
		
		$sql = "SELECT
				 Date_Format(p.date_payment,'%Y-%m-%d') as date_paid ,
				 if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
				  c.mobile as mobile,sa.id_branch,b.short_name as branch,s.scheme_type,
				  IFNULL(sa.scheme_acc_number,NULL) as scheme_acc_number,
				  IFNULL(p.payment_amount,'0.00') as amount,
				  IFNULL(p.metal_weight,'0.00') as weight,
				  IFNULL(p.payment_mode,'') as payment_mode,
				  IFNULL(sa.id_scheme_account,'') as id_scheme_account,
				  IFNULL(p.id_payment,'') as ref_no,p.id_payment,
				  IFNULL(p.metal_rate,'') as rate
				FROM
					".self::TAB_PAY." p
				LEFT JOIN ".self::TAB_ACC." sa ON (p.id_scheme_account = sa.id_scheme_account)
				LEFT JOIN branch b ON (b.id_branch = sa.id_branch)
				LEFT JOIN ".self::TAB_CUS." c ON (sa.id_customer = c.id_customer)
				LEFT JOIN ".self::TAB_SCH." s ON (sa.id_scheme = s.id_scheme)
				WHERE p.id_payment = '$id_payment'";
		
        $r = $this->db->query($sql);	
        return  $r->row_array();		
		
	}
	
	 //check transaction already exists 
	function checkTransExists($paid_date,$ref_no)
	{
		$common_db = $this->load->database('common_db',true);
		$sql = "SELECT * FROM transaction WHERE  date_paid = '$paid_date' AND ref_no = '$ref_no'";
		$r = $common_db->query($sql);
		if($r->num_rows==1)
		{
			return TRUE;
		}
	}
	
	
	function get_cusRegData($id)
	{
		$common_db = $this->load->database('common_db',true);
		$sql = "SELECT client_id,ref_no,id_scheme_account FROM customer_reg WHERE id_customer_reg = '$id'";
		$r = $common_db->query($sql);
		if($r->num_rows>=1)
		{
			return $r->row_array();
		}
	}
	
	function get_transData($id)
	{
		$common_db = $this->load->database('common_db',true);
		$sql = "SELECT ref_no FROM transaction WHERE id_transaction = '$id'";
		$r = $common_db->query($sql);
		if($r->num_rows>=1)
		{
			return $r->row()->ref_no;
		}
	}
		
	//insert transaction if not available
	function insert_transaction($data)
	{
		/*$data['msno'] = ($data['msno'] == ''? NULL : $data['msno']);*/
		$common_db = $this->load->database('common_db',true);
		$status = $common_db->insert('transaction',$data);
		return $status;
	}
		
	 //check Registration already exists 
	function checkRegExists($id_scheme_account)
	{
		$common_db = $this->load->database('common_db',true);
		$sql = "SELECT * FROM customer_reg WHERE id_scheme_account = '$id_scheme_account'";
		$r = $common_db->query($sql);
		if($r->num_rows==1)
		{
			return TRUE;
		}
	}
	
	 //check is new Registration 
	function isNewReg($id_scheme_account)
	{
		$sql = "SELECT scheme_acc_number FROM scheme_account WHERE id_scheme_account = '$id_scheme_account'";
		$r = $this->db->query($sql);
		$sch_ac_num = $r->row()->scheme_acc_number;
		
		 $schac_data = explode('-',$sch_ac_num);	
	  	 return (isset($schac_data[1]) && $schac_data[1] != ''?True:False);
	}	
	
	function getCustomerByID($id_scheme_account)
	{
		$sql = "SELECT
					   sa.id_scheme_account as  id_scheme_account,sa.id_branch,
		               Date_Format(sa.start_date,'%Y-%m-%d')  as reg_date,b.short_name as branch,sc.scheme_type,sc.amount,
				  IFNULL(sa.scheme_acc_number,NULL) as scheme_acc_number,
					   sa.account_name as name,
					   a.address1 as address1,a.address2 as address2,a.address3 as address3,ct.name as city,a.pincode,s.name as state,
					   c.phone,c.mobile,c.email,
					   Date_Format(c.date_of_birth,'%Y-%m-%d') as dt_of_birth,
					   Date_Format(date_of_wed,'%Y-%m-%d') as wed_date,
					   IF(sa.scheme_acc_number!='','N',sa.is_new) as new_customer
				FROM
							  customer c
				LEFT JOIN address a ON(c.id_customer=a.id_customer)
				LEFT JOIN country cy ON (a.id_country=cy.id_country)
				LEFT JOIN state s ON (a.id_state=s.id_state)
				LEFT JOIN city ct ON (a.id_city=ct.id_city)
				LEFT JOIN scheme_account sa ON (c.id_customer = sa.id_customer)
				LEFT JOIN scheme sc ON (sc.id_scheme = sa.id_scheme)
				LEFT JOIN branch b ON (b.id_branch = sa.id_branch)
				WHERE sa.id_scheme_account='$id_scheme_account'";
		    
			$r = $this->db->query($sql);	
        return  $r->row_array();				
	}
	
	//insert customer registration
	function insert_CustomerReg($data)
	{
		$common_db = $this->load->database('common_db',true);
		if($common_db->insert('customer_reg',$data))
		{
			$status = array('status' => TRUE,
			                'insertID' => $this->db->insert_id());
		}
        else
        {
			$status = array('status' => FALSE,
			                'insertID' => '');
		}			
		
		return $status;
	}
	
	
	
	// to get all customer reg records by branch	
	function getCustomerRegAll($branch)
	{
		$common_db = $this->load->database('common_db',true);
		$sql = "SELECT * FROM customer_reg WHERE branch='$branch'";
		return $common_db->query($sql)->result_array();
	}	
	
	// to get customer reg records by transfer status
	function getCustomerRegByTranStatus($status,$branch)
	{
		$common_db = $this->load->database('common_db',true);
		$sql = "SELECT * FROM customer_reg where branch='$branch' and is_transferred = '$status'";
		return $common_db->query($sql)->result_array();
	}
	
	//update customer registration	
	function update_CustomerReg($data,$id,$branch)
	{
		$common_db = $this->load->database('common_db',true);
		$common_db->where('id_customer_reg',$id); 
		$common_db->where('branch',$branch); 
		$status=$common_db->update('customer_reg',$data);
		return $status;
	}
	
	//to get all transaction records
	function getTransactionAll($branch)
	{
		$common_db = $this->load->database('common_db',true);
		$sql = "SELECT * FROM transaction  where branch='$branch'";
		return $common_db->query($sql)->result_array();
	}		
	
	
	//to get transaction record by transferred status
	function getTransactionByTranStatus($trans_status,$branch)
	{
		$common_db = $this->load->database('common_db',true);
		$sql = "SELECT * FROM transaction WHERE  branch='$branch' and is_transferred='$trans_status'";
		return $common_db->query($sql)->result_array();
	}
	
	function update_transaction($data,$id,$branch)
	{
		$common_db = $this->load->database('common_db',true);
		$common_db->where('id_transaction',$id); 
		$common_db->where('branch',$branch); 
		$status = $common_db->update('transaction',$data);
		return $status;
	}
	
	function update_transactionsByrefno($data,$id)
	{
		$common_db = $this->load->database('common_db',true);
		$common_db->where('ref_no',$id); 
		$status = $common_db->update('transaction',$data);
		return $status;
	}
	
	function update_schemeAccNo($data,$id)
	{
		$ac_data=array("scheme_acc_number" => $data['group_name']."-".$data['group_cus_no'],
		               "ref_no" => $data['ref_no'],
					   "date_upd" => date('Y-m-d H:i:s'));
		$this->db->where('id_scheme_account',$id); 
		$status = $this->db->update('scheme_account',$ac_data);
		return $status;
	}
	function update_paymentData($rno,$id)
	{
		$ac_data=array("receipt_no" => $rno,
					   "date_upd" => date('Y-m-d H:i:s'));
		$this->db->where('id_payment',$id); 
		$status = $this->db->update('payment',$ac_data);
		return $status;
	}
	function update_schemeAccount($data,$id)
	{
		$this->db->where('id_scheme_account',$id); 
		$status = $this->db->update('scheme_account',$data);
		return $status;
	}
		
		  	
}

?>