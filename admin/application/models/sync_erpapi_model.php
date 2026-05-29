<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Sync_erpapi_model extends CI_Model
{
    
	function __construct()
    {      
        parent::__construct();
    }
    
    function insertData($data,$table){
	    $status = $this->db->insert($table,$data);
	    return $status;
	}
	
	function checkref($refno,$table)
	{
		$sql = "SELECT ref_no FROM $table WHERE ref_no='$refno'";
		return ($this->db->query($sql)->num_rows() >0 ? TRUE:FALSE);	
	}
	
	//check client id already exists 
	function checkCusRegClientId($client_id)
	{
		$sql = "SELECT * FROM customer_reg WHERE clientid = '$client_id'";
		$r = $this->db->query($sql);
		if($r->num_rows >= 1)
		{
			return TRUE;
		}
	}
	
	function updInterTables($cusRegData,$updTrans,$idSchAc)
	{
	    $stat = FALSE ;
	    $trans = FALSE;
		$this->db->where('ref_no',$cusRegData['ref_no']);
		$cus = $this->db->update("customer_reg",$cusRegData);
		if($cus){
		    $trans = $this->updTransTable($updTrans);
		}
		if($trans){
		    $this->db->where('id_scheme_account',$idSchAc);
    	    $stat = $this->db->update("transaction",array('client_id' => $cusRegData['clientid']));
		}
		return $stat;
	}
	
	function updTransTable($updTrans)
	{
	    $this->db->where('ref_no',$updTrans['ref_no']);
	    $trans = $this->db->update("transaction",$updTrans);
		return $trans;
	}
	
	
	function updateCusRegData($data)
	{
		$this->db->where('clientid',$data['clientid']);
		 $status = $this->db->update("customer_reg",$data);
		return ($this->db->affected_rows() >0 ?TRUE:FALSE);
	}
	
	
	function updateData($data,$branch_id,$table)
	{
		$this->db->where('clientid',$data['clientid']);
		 if($branch_id == ''){
		    $this->db->where('id_branch',null);
		 }else{
		     $this->db->where('id_branch',$branch_id);
		 }
		 $status = $this->db->update($table,$data);
		return ($this->db->affected_rows() >0 ?TRUE:FALSE);
	}
	
	function update_closed_ac($data,$clientId,$id_customer_reg) 
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
			                    'is_modified'    => 'N',
            					"date_upd"		 => date('Y-m-d'),
            					'transfer_date'	 => date('Y-m-d'));
    		    $this->db->where('ref_no',$data['payment_ref_number']);
        		$status = $this->db->update('transaction',$trans);
			}
			return $status;
		}		
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
	
	function updatePaymentStatus($data){ 
	    
	    //$sql = $this->db->update("update transaction set payment_status = 4, remark =  remark.'Reversal receipt no '".$trans['remarks']);
	     $sql = "update payment set date_upd ='".date('Y-m-d H:i:s')."', payment_status = '".$data['payment_status']."', remark = concat(remark,'".$data['remark']."' ) where payment_status != '8' and receipt_no='".$data['receipt_no']."'";
         $this->db->query($sql);
         if($this->db->affected_rows()>0)
         {
        
				$trans = array( 'is_transferred' => 'Y',
			                    'is_modified'    => 0,
            					"date_upd"		 => date('Y-m-d'),
            					'transfer_date'	 => date('Y-m-d'));
    		    $this->db->where('ref_no',$data['payment_ref_number']);
        		$status = $this->db->update('transaction',$trans);
            
         }
         else
         {
             $status =false;
         }
         return $status;
	     
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
		
	
	// new api related sync functions
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
	
	function getAllBranch(){  
		$sql = "SELECT warehouse,expo_warehouse,id_branch from branch where active=1"; 
		$r = $this->db->query($sql);
		return $r->result_array();
	} 
	
	// Service Functions
	function getcustomerByStatus($trans_status,$record_to,$tran_date=""){	
     
	    $sql = "SELECT 
	        c.id_customer_reg,c.cus_ref_no,c.clientid,c.id_branch,c.warehouse,c.record_to,c.is_modified,c.reg_date,c.maturity_date,c.salutation,c.ac_name,c.firstname,c.lastname,
	        c.address1,c.address2,c.address3,c.city,c.state,c.pincode,c.phone,c.mobile,c.email,c.dt_of_birth,c.wed_date,c.new_customer,c.ref_no,c.id_scheme_account,c.account_name,
	        c.sync_scheme_code,c.group_code,c.scheme_ac_no,c.paid_installments,c.is_closed,c.closed_by,c.closing_date,c.closing_amount,c.closing_weight,c.closing_add_chgs,
	        c.additional_benefits,c.remark_close,c.is_transferred,c.transfer_date,c.date_update,c.date_add,c.custom_entry_date,c.is_registered_online,c.nominee,c.nominee_mobile,
	        c.rate_fixed_in,c.fixed_metal_rate,c.fixed_wgt,c.fixed_rate_on,c.firstPayment_amt,c.one_time_premium,c.is_online_cus,ref_comp_id
	    FROM customer_reg c
	    LEFT JOIN branch b on b.warehouse = c.warehouse
	    WHERE record_to='$record_to' and is_transferred='$trans_status'"; 
	    
	    if($tran_date != ""){
			$sql = $sql." and date(transfer_date)='".$tran_date."'";
		} 
		return $this->db->query($sql)->result_array();
    }
    
    function getTransactionByTranStatus($trans_status,$record_to,$tran_date="")
	{
	    $sql = "SELECT * FROM transaction WHERE record_to='$record_to' and is_transferred='$trans_status'"; 
	    if($tran_date != ""){
			$sql = $sql." and date(transfer_date)='".$tran_date."'";
		}
	    return $this->db->query($sql)->result_array();
	}
	
	function getCusRegData($clientId){	 
	    $sql = "SELECT 
	        c.warehouse,c.firstname,c.lastname,c.mobile,c.cus_ref_no,c.id_branch,c.sync_scheme_code,c.scheme_ac_no,b.ref_comp_id
	        FROM customer_reg c
	    LEFT JOIN branch b on b.warehouse = c.warehouse
	    WHERE clientid='".$clientId."'"; 
		return $this->db->query($sql)->row_array();
    }
    
    function update_cancel_Payment($ref_no,$warehouse,$updTrans)
	{
	    $sql="select * from transaction where receipt_no='".$ref_no."' and  warehouse='".$warehouse."' and payment_status!=8";
	    $result=$this->db->query($sql)->row_array();
	    if($result)
	    {
    	    if($result['payment_status']!=8)
    	    {
        	    $this->db->where('receipt_no',$ref_no);
        	    $this->db->where('warehouse',$warehouse);
        	    $this->db->where('payment_status',1);
        	    $trans = $this->db->update("transaction",$updTrans);
        	    if($this->db->affected_rows()>0)
        	    {
        	         $msg=array('status'=>true,'affected_rows'=>$this->db->affected_rows(),'msg'=>'Payment Returned Successfully');
        	    }
        	    else
        	    {
        	        $msg=array('status'=>false,'affected_rows'=>$this->db->affected_rows(),'msg'=>'Already Payment is Returned');
        	    }
    	    }
    	    else
    	    {
    	        $msg=array('status'=>false,'msg'=>'Payment is Defaulter Payment');
    	    }
	    }
	    else
	    {
	        $msg=array('status'=>false,'msg'=>'No records in Transcation');
	    }
		return $msg;
	}
	
	function isdefaulterExist($data)
	{
	  
	    $pay_data="select * from transaction p where p.client_id='".$data['client_id']."' and p.payment_status=8 and p.due_month=".$data['due_month']." and p.due_year=".$data['due_year']."";
        
        $records = $this->db->query($pay_data);
         if($records->num_rows()>0)
         {
             $msg=array('status'=>true,'msg'=>'Defaulter Already Exists');
         }
         else
         {
              $msg=array('status'=>false,'msg'=>'');
         }
         	return $msg;
	}
	
}
?>