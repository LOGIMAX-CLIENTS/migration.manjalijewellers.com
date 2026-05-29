<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Chitapi_model extends CI_Model
{
    const TAB_CUS     = "customer";
	const TAB_ACC     = "scheme_account";
	const TAB_SCH     = "scheme";
	const TAB_PAY     = "payment";
	
	function __construct()
    {      
        parent::__construct();
    }
    
	
	function  getPayment()
	{
		
		$sql = "SELECT
				  Date_Format(p.date_payment,'%Y-%m-%d') as trans_date ,
				  sa.ref_no as client_id,IFNULL(sa.id_branch,NULL)as id_branch,
				  IFNULL(p.discountAmt,'0.00') as discountAmt,
				  sa.group_code as group_code,
				  sa.msno as msno,
				  s.code as scheme_code,
				  p.payment_amount as amount,
				  p.metal_weight as weight,
				  p.payment_mode,
				  p.bank_name,
				  p.bank_branch as  branch_name,
				  s.id_metal as metal,
				  p.card_no    as card_no,
				  p.payment_ref_number as approval_no,
				  p.id_payment as ref_no,
				  sa.is_new as new_customer
				FROM
					".self::TAB_PAY." p
				LEFT JOIN ".self::TAB_ACC." sa ON (p.id_scheme_account = sa.id_scheme_account)
				LEFT JOIN ".self::TAB_CUS." c ON (sa.id_customer = c.id_customer)
				LEFT JOIN ".self::TAB_SCH." s ON (sa.id_scheme = s.id_scheme)";
		
        $r = $this->db->query($sql);	
        return  $r->result_array();		
		
	}
	
	function  getPaymentByID($id_payment)
	{
		
		$sql = "SELECT
				 Date_Format(p.date_payment,'%Y-%m-%d') as trans_date ,IFNULL(NULL,Date_Format(p.custom_entry_date,'%Y-%m-%d')) as custom_entry_date ,
				 if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
				  c.mobile as mobile,IFNULL(sa.id_branch,NULL)as id_branch,
				  IFNULL(sa.ref_no,'')as client_id,
				  if(payment_mode = 'FP', IFNULL(p.payment_amount,'0.00') , IFNULL(p.discountAmt,'0.00')) as discountAmt,
				  IFNULL(sa.scheme_acc_number,NULL) as msno,
				  if(cs.has_lucky_draw=1,ifnull(sa.group_code,''),ifnull(s.code,'')) as group_code,
				  IFNULL(s.code,'') as scheme_code,
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
				  IF(sa.scheme_acc_number!='','N',IF(s.free_payment = 1 , if(p.payment_mode = 'FP',sa.is_new,'N') , sa.is_new)) as new_customer,
				  IFNULL(p.metal_rate,'') as rate
				FROM
					".self::TAB_PAY." p
				LEFT JOIN ".self::TAB_ACC." sa ON (p.id_scheme_account = sa.id_scheme_account)
				LEFT JOIN ".self::TAB_CUS." c ON (sa.id_customer = c.id_customer)
				LEFT JOIN ".self::TAB_SCH." s ON (sa.id_scheme = s.id_scheme)
				join chit_settings cs
				WHERE p.id_payment = '$id_payment'";
		
        $r = $this->db->query($sql);	
        return  $r->result_array();		
		
	}
	
		
	function getCustomer()
	{
		$sql = "SELECT
					   c.id_customer,
					   Date_Format(sa.start_date,'%Y-%m-%d')  as reg_date,
					   c.title as salutation, c.initials,
					   if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
					   a.address1 as address1,a.address2 as address2,a.address3 as address3,ct.name as city,a.pincode,s.name as state,
					   c.phone,c.mobile,c.email,
					   Date_Format(c.date_of_birth,'%Y-%m-%d') as dt_of_birth,
					   Date_Format(date_of_wed,'%Y-%m-%d') as wed_date,sa.id_scheme_account as ref_no
				FROM
							  customer c
				LEFT JOIN address a ON(c.id_customer=a.id_customer)
				LEFT JOIN country cy ON (a.id_country=cy.id_country)
				LEFT JOIN state s ON (a.id_state=s.id_state)
				LEFT JOIN city ct ON (a.id_city=ct.id_city)
				LEFT JOIN scheme_account sa ON (c.id_customer = sa.id_customer)
				";
		    
			$r = $this->db->query($sql);	
        return  $r->result_array();				
	}	
	
	function getCustomerByID($id_scheme_account,$id_payment)
	{
		$sql = "SELECT
					   sa.id_scheme_account as  id_scheme_account,IFNULL(sa.id_branch,NULL)as id_branch,
		               Date_Format(sa.start_date,'%Y-%m-%d')  as reg_date,IFNULL(sa.pan_no,NULL) as pan_no,
					   c.title as salutation, c.initials,
					   if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
					   a.address1 as address1,a.address2 as address2,a.address3 as address3,ct.name as city,a.pincode,s.name as state,
					   c.phone,c.mobile,c.email,
					   Date_Format(c.date_of_birth,'%Y-%m-%d') as dt_of_birth,
					   Date_Format(date_of_wed,'%Y-%m-%d') as wed_date,
					   (SELECT IF(sa.scheme_acc_number!='','N',IF(sh.free_payment = 1 , if(p.payment_mode = 'FP',sa.is_new,'N') , sa.is_new)) from  payment p WHERE p.id_payment=".$id_payment.") as  new_customer
					   
				FROM
							  customer c
				LEFT JOIN address a ON(c.id_customer=a.id_customer)
				LEFT JOIN country cy ON (a.id_country=cy.id_country)
				LEFT JOIN state s ON (a.id_state=s.id_state)
				LEFT JOIN city ct ON (a.id_city=ct.id_city)
				LEFT JOIN scheme_account sa ON (c.id_customer = sa.id_customer) 
				LEFT JOIN scheme sh ON (sa.id_scheme = sh.id_scheme)
				WHERE sa.id_scheme_account='$id_scheme_account'";
		    
			$r = $this->db->query($sql);	
        return  $r->result_array();				
	}
	
	//common_db functions for T.Nagar branch (id_branch - 1)
	
	/** select records **/
	
	// to get all customer reg records 	
	function getCustomerRegAll($branch)
	{
	    if($branch == ''){
	       	$sql = "SELECT * FROM jil_customer_reg WHERE id_branch is null";
	    }else{
	        $sql = "SELECT * FROM jil_customer_reg WHERE id_branch='$branch'";
	    }
	
		return $this->db->query($sql)->result_array();
	}	
	
	// to get customer reg records by transfer date
	function getCustomerRegByTransferDate($date)
	{
		$sql = "SELECT * FROM jil_customer_reg where transfer_date = '$date'";
		return $this->db->query($sql)->result_array();
	}	
	
	// to get customer reg records by transfer status
	function getCustomerRegByTranStatus($status,$branch)
	{
	     if($branch == ''){
	         	$sql = "SELECT * FROM jil_customer_reg where id_branch is null and transfer_jil = '$status'";
	     }else{
	         	$sql = "SELECT * FROM jil_customer_reg where id_branch='$branch' and transfer_jil = '$status'";
	     }
	
		return $this->db->query($sql)->result_array();
	}
	
	//to get customer reg records by new or existing
	function getCustomerRegByType($status)
	{
		$sql = "SELECT * FROM jil_customer_reg where transfer_jil = '$status'";
		return $this->db->query($sql)->result_array();
	}
	
	//to get all transaction records
	function getTransactionAll()
	{
		$sql = "SELECT * FROM jil_transaction  where";
		return $this->db->query($sql)->result_array();
	}		
	
	//to get transaction record by id
	function getTransactionByID($id_transaction)
	{
		$sql = "SELECT * FROM jil_transaction WHERE id_transaction='$id_transaction'";
		return $this->db->query($sql)->row_array();
	}		
	
	//to get transaction record by clientid
	function getTransactionByClientID($client_id)
	{
		$sql = "SELECT * FROM jil_transaction WHERE client_id='$client_id'";
		return $this->db->query($sql)->result_array();
	}	
		
	//to get transaction record by transaction date
	function getTransactionByTransDate($trans_date)
	{
		$sql = "SELECT * FROM jil_transaction WHERE transfer_date='$trans_date'";
		return $this->db->query($sql)->result_array();
	}		
	
	//to get transaction record by customer type
	function getTransactionByType($Type)
	{
		$sql = "SELECT * FROM jil_transaction WHERE jil_new_customer='$type'";
		return $this->db->query($sql)->result_array();
	}	
	
	//to get transaction record by transferred date
	function getTransactionByTransferDate($trans_date)
	{
		$sql = "SELECT * FROM jil_transaction WHERE transfer_date='$trans_date'";
		return $this->db->query($sql)->result_array();
	}	
		
	//to get transaction record by transferred status
	function getTransactionByTranStatus($trans_status,$id_branch)
	{
	    if($id_branch == ''){
	        	$sql = "SELECT * FROM jil_transaction WHERE id_branch is null and transfer_jil='$trans_status'";
	    }else{
	       	$sql = "SELECT * FROM jil_transaction WHERE id_branch='$id_branch' and transfer_jil='$trans_status'"; 
	    }
	
		return $this->db->query($sql)->result_array();
	}	
 
    //check transaction already exists 
	function checkTransExists($trans_date,$approval_no,$ref_no)
	{
		$sql = "SELECT * FROM jil_transaction WHERE ref_no = '$ref_no'";
		$r = $this->db->query($sql);
		if($r->num_rows >= 1)
		{
			return TRUE;
		}
	}	
	
	//to get all new customer records
	function getNewCustomerAll()
	{
		$sql = "SELECT * FROM jil_new_customer";
		return $this->db->query($sql)->result_array();
	}		
		
	//to get new customer records by ref_no
	function getNewCustomerByRef($ref_no)
	{
		$sql = "SELECT * FROM jil_new_customer WHERE ref_no = '$ref_no'";
		return $this->db->query($sql)->row_array();
	}	
	//to get new customer records by trans_status
	function getNewCustomerByTranStatus($status)
	{
		$sql = "SELECT * FROM jil_new_customer WHERE transfer = '$status'";
		$r = $this->db->query($sql)->result_array();
		return $r;
	}	
	
	//to get new customer records by trans_date
	function getNewCustomerByTransDate($date)
	{
		$sql = "SELECT * FROM jil_new_customer WHERE transfer_date = '$date'";
		return $this->db->query($sql)->result_array();
	}	
	
	//check new customer existing
	function checkNewCustomer($data)
	{
		$common_db = $this->load->database('common_db',true);
		$common_db->select('ref_no,mobile,clientid');
		$common_db->where('mobile',$data['mobile']);
		$common_db->where('clientid',$data['clientid']);
		$common_db->where('id_branch','1');
		$r = $common_db->get('jil_new_customer');
	  
	  if($r->num_rows()>0)
	  {
		  return TRUE;
	  }
      	  
	}
	
	//Insert and update operations
	
	//insert customer registration
	function insert_CustomerReg($data)
	{
	    $data['id_branch'] = ($data['id_branch'] == '' ||  $data['id_branch'] == 0? NULL : $data['id_branch']);
		if($this->db->insert('jil_customer_reg',$data))
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
	
	
	//update customer registration	
	function update_CustomerReg($data,$id)
	{
		$this->db->where('id_customer_reg',$id); 
		$status = $this->db->update('jil_customer_reg',$data);
		return $status;
	}
	
  	function update_CustomerRegByRange($upperlimit,$lowerlimit,$data)
	{
		$sql = "UPDATE jil_customer_reg SET transfer_jil = '".$data['transfer_jil']."', transfer_date = '".$data['transfer_date']."'   WHERE id_customer_reg  BETWEEN '$lowerlimit' AND '$upperlimit'";
		$status = $this->db->query($sql);
		return $status;
	}
	
	
	function insert_transaction($data)
	{
	    $data['msno'] = ($data['msno'] == ''? NULL : $data['msno']);
		$data['id_branch'] = ($data['id_branch'] == '' || $data['id_branch'] == 0? NULL : $data['id_branch']);
		$status = $this->db->insert('jil_transaction',$data);
		return $status;
	}
		
	function update_transaction($data,$id)
	{
		$this->db->where('id_transaction',$id); 
		$status = $this->db->update('jil_transaction',$data);
		return $status;
	}	
	
	function updateTransactionByRange($upperlimit,$lowerlimit,$data)
	{
		$sql = "UPDATE jil_transaction SET transfer_jil = '".$data['transfer_jil']."', transfer_date = '".$data['transfer_date']."'   WHERE id_transaction BETWEEN '$lowerlimit' AND '$upperlimit'";
		$status = $this->db->query($sql);
		return $status;
	}
	
	function insert_newCustomer($data)
	{		
		$status = $this->db->insert('jil_new_customer',$data);
		return $status;
	}
	
	function update_newCustomer($data,$id)
	{
		 $this->db->where('id_new_customer',$id); 
		$status =  $this->db->update('jil_new_customer',$data);
		return $status;
	}
	
	
	//insert offline payments records
	function insert_offlinePayment($data)
	{
		if($this->db->insert('jil_offline_payments',$data))
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
	
	//get offline payment records which are not updated in payment table
	function getofflinePaymentsbyStatus($status)
	{
		$sql = "SELECT * FROM jil_offline_payments WHERE is_trans_completed='$status' limit 1500";
		return $this->db->query($sql)->result_array();	
	}
	
	
	function getIdschemeAC($data)
	{
		 if($data['id_branch'] == ''){
		     $sql = "SELECT sa.id_scheme_account as  id_scheme_account FROM customer c
				LEFT JOIN scheme_account sa ON (c.id_customer = sa.id_customer)
				WHERE sa.scheme_acc_number='".$data['scheme_ac_number']."' AND sa.ref_no='".$data['clientid']."' AND sa.id_branch is null";
		 }
		else{
		$sql = "SELECT sa.id_scheme_account as  id_scheme_account FROM customer c
				LEFT JOIN scheme_account sa ON (c.id_customer = sa.id_customer)
				WHERE sa.scheme_acc_number='".$data['scheme_ac_number']."' AND sa.ref_no='".$data['clientid']."' AND sa.id_branch='".$data['id_branch']."'";
		}		
				/*$sql = "SELECT sa.id_scheme_account as  id_scheme_account FROM customer c
				LEFT JOIN scheme_account sa ON (c.id_customer = sa.id_customer)
				WHERE sa.scheme_acc_number='".$data['scheme_ac_number']."' AND sa.id_branch='".$data['id_branch']."' AND c.mobile='".$data['mobile']."'";*/
				
			$r = $this->db->query($sql);
		
			if( $r->num_rows()==1){
				return  $r->row()->id_scheme_account;	
			}	
			else{
				return FALSE;
			}
       	
	}
	
	//insert offline payment into payment table
	/*function insertPayment($data) //05-09-2017 
	{
		$data['is_offline'] = 1;
		$status = $this->db->insert('payment',$data);
		if($status){			
			$this->updateschAc($data['id_scheme_account']);
			$this->update_offlinePay($data['payment_ref_number']);
		}
		return $status;
	}*/
	
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
					//$this->updateschAc($data['id_scheme_account']); // no need 
					$this->update_offlinePay($data['payment_ref_number']); 
				}
			return $status;
		}		
	}
	  
	//update cancelled payments (Receipt Cancel, Cheque Return, PDC Cancel ) in payment table 
	/*function updatePayment($data) //05-09-2017 
	{
		$data['is_offline'] =1;
		$this->db->where(array('receipt_jil' => $data['receipt_jil'], 'payment_ref_number' => $data['payment_ref_number']));
		//$this->db->where('payment_ref_number',$data['payment_ref_number']);
		$status = $this->db->update('payment',$data);
		if($status){
			$this->updateschAc($data['id_scheme_account']);
			
			$this->update_offlinePay($data['payment_ref_number']);
		}
		return $status;
	}*/
	function updatePayment($data) 
	{
		$pay_ref_no = $data['payment_ref_number'];
		$sql = $this->db->query("select * from payment where payment_ref_number='$pay_ref_no'");
			
		if($sql->num_rows() > 1){
			return FALSE;
		}
		else{
			$data['is_offline'] = 1;
			$this->db->where(array('receipt_no' => $data['receipt_jil'], 'payment_ref_number' => $data['payment_ref_number']));
			$status = $this->db->update('payment',$data);
			if($status){
				//$this->updateschAc($data['id_scheme_account']);
				$this->update_offlinePay($data['payment_ref_number']);
			}
			return $status;
		}		
	}
	//update scheme account flag
	function updateschAc($id)
	{
		$data = array('is_existingPay_updated' => 1);
		$this->db->where('id_scheme_account',$id);
		$status = $this->db->update('scheme_account',$data);
		return $status;
	}
	
	function update_offlinePay($brefno)
	{
		$data = array('is_trans_completed' => 'Y');
		$this->db->where('brefno',$brefno); 
		$status = $this->db->update('jil_offline_payments',$data);
		return $status;
	}
	function checkBref($brefno)
	{
		$sql = "SELECT brefno FROM jil_offline_payments WHERE brefno='$brefno'";
		return ($this->db->query($sql)->num_rows() >0 ? TRUE:FALSE);	
	}
	function getUpdatedClientID()
	{
		$common_db = $this->load->database('common_db',true);
		$sql = "SELECT group_concat(clientid) FROM jil_offline_payments";
		return $common_db->query($sql)->result_array();	
	}  	
	
	function deletePayandCus($id_payment){
		$status = FALSE;
		$this->db->where('ref_no',$id_payment);
		$delcus = $this->db->delete('jil_customer_reg');
		if($delcus){
			$this->db->where('ref_no',$id_payment);
			$status = $this->db->delete('jil_transaction');
		}
		
		return $status;
		
	}  	
	
    function revertPayment($id_payment) 
	{
		 $this->db->where('id_payment',$id_payment);
		 $status = $this->db->update('payment',array('payment_status'=>2));	
		return $status;
	}
	
	function insert_chitClosingData($data)
	{
		$status = $this->db->insert('offline_chit_closing',$data);
		return $status;
	}
	
	//check customer reg already exists 
	function checkCusRegExists($id_scheme_account = "",$ref_no)
	{
		if($id_scheme_account == ""){
			$sql = "SELECT id_scheme_account FROM customer_reg WHERE ref_no = '$ref_no'";
		}else{
			$sql = "SELECT id_scheme_account FROM customer_reg WHERE ref_no = '$ref_no' and id_scheme_account = '$id_scheme_account'";
		}
		
		$r = $this->db->query($sql);
		if($r->num_rows >= 1)
		{
			$status = array('status' => TRUE,
			                'id_scheme_account' => $r->row()->id_scheme_account);
		}else{
			$status = array('status' => FALSE,
			                'id_scheme_account' => '');
		}
		return $status;
	}
}
?>