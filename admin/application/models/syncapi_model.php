<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Syncapi_model extends CI_Model
{
    const TAB_CUS     = "customer";
	const TAB_ACC     = "scheme_account";
	const TAB_SCH     = "scheme";
	const TAB_PAY     = "payment";
	
	function __construct()
    {      
        parent::__construct();
    }
    
    // start of new api fn ---KVP----    
    
    
    /* API FUNCTIONS STARTS - STRICTLY FOR API ONLY */
    function checkRevertByStatus($trans_status,$id_branch){	
        if($id_branch == ''){
	        $sql = "SELECT * FROM revert_approve_log WHERE id_branch is null and is_transferred='$trans_status'";
	    }else{
	       	$sql = "SELECT * FROM revert_approve_log WHERE id_branch='$id_branch' and is_transferred='$trans_status'"; 
	    }
	
		return $this->db->query($sql)->result_array();
    }
    
    /*function getcustomerByStatus($trans_status,$id_branch,$record_to,$tran_date=""){	
        if($id_branch == ''){
	        $sql = "SELECT * FROM customer_reg WHERE id_branch is null and record_to='$record_to' and is_transferred='$trans_status'";
	    }else{
	       	$sql = "SELECT * FROM customer_reg WHERE id_branch='$id_branch' and record_to='$record_to' and is_transferred='$trans_status'"; 
	    }
	    if($tran_date != ""){
			$sql = $sql." and transfer_date='".$tran_date."'";
		} 
		return $this->db->query($sql)->result_array();
    }*/
    
    function getcustomerByStatus($trans_status,$id_branch,$record_to,$tran_date=""){	
        if($id_branch == ''){
	        $sql = "SELECT * FROM customer_reg WHERE id_branch is null and record_to='$record_to' and is_transferred='$trans_status'";
	    }elseif($id_branch == -1){ // No branch filter
	       	$sql = "SELECT * FROM customer_reg WHERE record_to='$record_to' and is_transferred='$trans_status'"; 
	    }else{
	       	$sql = "SELECT * FROM customer_reg WHERE id_branch='$id_branch' and record_to='$record_to' and is_transferred='$trans_status'"; 
	    }
	    if($tran_date != ""){
			$sql = $sql." and transfer_date='".$tran_date."' limit 1000";
		}
	    //echo $sql;exit;
		return $this->db->query($sql)->result_array();
    } 
    
	function get_acc_data()
	{
		$sql = "SELECT ref_no FROM transaction WHERE is_transferred = 'N' AND record_to = 2 ORDER BY id_transaction DESC LIMIT 10";

		$r = $this->db->query($sql);
		return $r->result_array();
	}
    
   /* function getTransactionByTranStatus($trans_status,$id_branch,$record_to,$tran_date="")
	{
	    if($id_branch == ''){
	        $sql = "SELECT  t.id_transaction, t.client_id,t.receipt_no, t.mobile, t.id_branch, t.warehouse, t.record_to, t.payment_date, t.due_month, t.due_year, t.custom_entry_date, t.amount, t.weight, t.rate, t.metal, t.payment_mode, t.bank_name, t.branch_name, t.card_no, t.ref_no, t.pay_trans_id, t.payment_ref_number, t.paid_through, t.is_transferred, t.is_modified, t.transfer_date, t.new_customer, t.id_scheme_account, t.discountAmt, t.payment_status, t.payment_type, t.due_type, t.receipt_no, t.date_add, t.date_upd, t.installment_no, t.remarks, t.gst, t.gst_type, t.emp_code, t.id_drawee,
                    d.account_no as drawee_ac_no,ifsc_code as drawee_ifsc,b.bank_name as drawee_bank
                    FROM transaction t
                        LEFT JOIN drawee_account d on d.id_drawee = t.id_drawee
                        LEFT JOIN bank b on b.id_bank = d.id_bank 
	                WHERE id_branch is null and record_to='$record_to' and is_transferred='$trans_status'";
	    }else{
	       	$sql = "SELECT  t.id_transaction, t.client_id,t.receipt_no, t.mobile, t.id_branch, t.warehouse, t.record_to, t.payment_date, t.due_month, t.due_year, t.custom_entry_date, t.amount, t.weight, t.rate, t.metal, t.payment_mode, t.bank_name, t.branch_name, t.card_no, t.ref_no, t.pay_trans_id, t.payment_ref_number, t.paid_through, t.is_transferred, t.is_modified, t.transfer_date, t.new_customer, t.id_scheme_account, t.discountAmt, t.payment_status, t.payment_type, t.due_type, t.receipt_no, t.date_add, t.date_upd, t.installment_no, t.remarks, t.gst, t.gst_type, t.emp_code, t.id_drawee,
                    d.account_no as drawee_ac_no,ifsc_code as drawee_ifsc,b.bank_name as drawee_bank
                    FROM transaction t
                        LEFT JOIN drawee_account d on d.id_drawee = t.id_drawee
                        LEFT JOIN bank b on b.id_bank = d.id_bank 
                    WHERE id_branch='$id_branch' and record_to='$record_to' and is_transferred='$trans_status'"; 
	    }
		if($tran_date != ""){
			$sql = $sql." and transfer_date='".$tran_date."'";
		}
		//print_r($sql);exit;
		return $this->db->query($sql)->result_array();
	}*/
	
/*	function getTransactionByTranStatus($trans_status,$id_branch,$record_to,$tran_date="")
	{
	    if($id_branch == ''){
	        $sql = "SELECT 
	        			t.id_transaction, t.client_id,t.receipt_no, t.mobile, t.id_branch, t.warehouse, t.record_to, t.payment_date, t.due_month, t.due_year, t.custom_entry_date, t.amount, t.weight, t.rate, t.metal, t.payment_mode, t.bank_name, t.branch_name, t.card_no, t.ref_no, t.pay_trans_id, t.payment_ref_number, t.paid_through, t.is_transferred, t.is_modified, t.transfer_date, t.new_customer, t.id_scheme_account, t.discountAmt, t.payment_status, t.payment_type, t.due_type, t.receipt_no, t.date_add, t.date_upd, t.installment_no, t.remarks, t.gst, t.gst_type, t.emp_code, t.id_drawee,
                    d.branch as drawee_ac_branch,d.account_name as drawee_ac_name,d.account_no as drawee_ac_no,d.ifsc_code as drawee_ifsc,b.bank_name as drawee_bank 
                    FROM transaction t
                        LEFT JOIN drawee_account d on d.id_drawee = t.id_drawee
                        LEFT JOIN bank b on b.id_bank = d.id_bank 
	         		WHERE id_branch is null and record_to='$record_to' and is_transferred='$trans_status'";
	    }elseif($id_branch == -1){ // No branch filter
	        $sql = "SELECT 
			        	t.id_transaction, t.client_id,t.receipt_no, t.mobile, t.id_branch, t.warehouse, t.record_to, t.payment_date, t.due_month, t.due_year, t.custom_entry_date, t.amount, t.weight, t.rate, t.metal, t.payment_mode, t.bank_name, t.branch_name, t.card_no, t.ref_no, t.pay_trans_id, t.payment_ref_number, t.paid_through, t.is_transferred, t.is_modified, t.transfer_date, t.new_customer, t.id_scheme_account, t.discountAmt, t.payment_status, t.payment_type, t.due_type, t.receipt_no, t.date_add, t.date_upd, t.installment_no, t.remarks, t.gst, t.gst_type, t.emp_code, t.id_drawee,
		                    d.branch as drawee_ac_branch,d.account_name as drawee_ac_name,d.account_no as drawee_ac_no,d.ifsc_code as drawee_ifsc,b.bank_name as drawee_bank 
                    FROM transaction t
                        LEFT JOIN drawee_account d on d.id_drawee = t.id_drawee
                        LEFT JOIN bank b on b.id_bank = d.id_bank
	         		WHERE record_to='$record_to' and is_transferred='$trans_status'"; 
	    }
	    else{
	       	$sql = "SELECT 
	       				t.id_transaction, t.client_id,t.receipt_no, t.mobile, t.id_branch, t.warehouse, t.record_to, t.payment_date, t.due_month, t.due_year, t.custom_entry_date, t.amount, t.weight, t.rate, t.metal, t.payment_mode, t.bank_name, t.branch_name, t.card_no, t.ref_no, t.pay_trans_id, t.payment_ref_number, t.paid_through, t.is_transferred, t.is_modified, t.transfer_date, t.new_customer, t.id_scheme_account, t.discountAmt, t.payment_status, t.payment_type, t.due_type, t.receipt_no, t.date_add, t.date_upd, t.installment_no, t.remarks, t.gst, t.gst_type, t.emp_code, t.id_drawee,
		                d.branch as drawee_ac_branch,d.account_name as drawee_ac_name,d.account_no as drawee_ac_no,d.ifsc_code as drawee_ifsc,b.bank_name as drawee_bank 
                    FROM transaction t
                        LEFT JOIN drawee_account d on d.id_drawee = t.id_drawee
                        LEFT JOIN bank b on b.id_bank = d.id_bank 
                    WHERE id_branch='$id_branch' and record_to='$record_to' and is_transferred='$trans_status'"; 
	    }
		if($tran_date != ""){
			$sql = $sql." and transfer_date='".$tran_date."'  limit 1000";
		}
	//	echo $sql;exit;
		return $this->db->query($sql)->result_array();
	} */
	
	function getTransactionByTranStatus($trans_status, $id_branch, $record_to, $tran_date = "")
	{
		$sql = "
			SELECT 
				t.id_transaction, t.client_id, t.receipt_no, t.mobile,t.id_branch, t.warehouse, t.record_to, t.payment_date,t.due_month, t.due_year, t.custom_entry_date,t.amount, t.weight, t.rate, t.metal,t.payment_mode, t.bank_name, t.branch_name,t.card_no, t.ref_no, t.pay_trans_id,t.payment_ref_number, t.paid_through,t.is_transferred, t.is_modified, t.transfer_date,t.new_customer, t.id_scheme_account, t.discountAmt,t.payment_status, t.payment_type, t.due_type,t.date_add, t.date_upd, t.installment_no,t.remarks, t.gst, t.gst_type, t.emp_code,t.id_drawee,d.branch AS drawee_ac_branch,d.account_name AS drawee_ac_name,d.account_no AS drawee_ac_no,d.ifsc_code AS drawee_ifsc,b.bank_name AS drawee_bank
			FROM transaction t
			LEFT JOIN drawee_account d ON d.id_drawee = t.id_drawee
			LEFT JOIN bank b ON b.id_bank = d.id_bank
			WHERE t.record_to = ?
			AND t.is_transferred = ?
		";

		$params = [$record_to, $trans_status];

		if ($id_branch === '') {
			$sql .= " AND t.id_branch IS NULL ";
		} elseif ($id_branch != -1) {
			$sql .= " AND t.id_branch = ? ";
			$params[] = $id_branch;
		}

		if ($tran_date != "") {
			$sql .= " AND t.transfer_date = ? ";
			$params[] = $tran_date;
		}

		$sql .= " ORDER BY t.id_transaction DESC LIMIT 1000 ";

		return $this->db->query($sql, $params)->result_array();
	}

	/* function getRegisteredAccTransactions($trans_status,$id_branch,$record_to,$tran_date="")
	{
	    if($id_branch == ''){
	        $sql = "SELECT 
	        			t.id_transaction, t.client_id,t.receipt_no, t.mobile, t.id_branch, t.warehouse, t.record_to, t.payment_date, t.due_month, t.due_year, t.custom_entry_date, t.amount, t.weight, t.rate, t.metal, t.payment_mode, t.bank_name, t.branch_name, t.card_no, t.ref_no, t.pay_trans_id, t.payment_ref_number, t.paid_through, t.is_transferred, t.is_modified, t.transfer_date, t.new_customer, cr.id_scheme_account, t.discountAmt, t.payment_status, t.payment_type, t.due_type, t.receipt_no, t.date_add, t.date_upd, t.installment_no, t.remarks, t.gst, t.gst_type, t.emp_code, t.id_drawee,
                    d.branch as drawee_ac_branch,d.account_name as drawee_ac_name,d.account_no as drawee_ac_no,d.ifsc_code as drawee_ifsc,b.bank_name as drawee_bank 
                    FROM transaction t
                        LEFT JOIN drawee_account d on d.id_drawee = t.id_drawee
                        LEFT JOIN bank b on b.id_bank = d.id_bank 
                        LEFT JOIN customer_reg cr on cr.clientid=t.client_id  
            	    WHERE (cr.is_registered_online > 0 or t.is_modified=1) AND
	         		t.id_branch is null and t.record_to='$record_to' and t.is_transferred='$trans_status'";
	    }elseif($id_branch == -1){ // No branch filter
	        $sql = "SELECT 
			        	t.id_transaction, t.client_id,t.receipt_no, t.mobile, t.id_branch, t.warehouse, t.record_to, t.payment_date, t.due_month, t.due_year, t.custom_entry_date, t.amount, t.weight, t.rate, t.metal, t.payment_mode, t.bank_name, t.branch_name, t.card_no, t.ref_no, t.pay_trans_id, t.payment_ref_number, t.paid_through, t.is_transferred, t.is_modified, t.transfer_date, t.new_customer, cr.id_scheme_account, t.discountAmt, t.payment_status, t.payment_type, t.due_type, t.receipt_no, t.date_add, t.date_upd, t.installment_no, t.remarks, t.gst, t.gst_type, t.emp_code, t.id_drawee,
		                    d.branch as drawee_ac_branch,d.account_name as drawee_ac_name,d.account_no as drawee_ac_no,d.ifsc_code as drawee_ifsc,b.bank_name as drawee_bank 
                    FROM transaction t
                        LEFT JOIN drawee_account d on d.id_drawee = t.id_drawee
                        LEFT JOIN bank b on b.id_bank = d.id_bank
	         		LEFT JOIN customer_reg cr on cr.clientid=t.client_id  
            	    WHERE (cr.is_registered_online > 0 or t.is_modified=1) AND t.record_to='$record_to' and t.is_transferred='$trans_status'"; 
	    }
	    else{
	       	$sql = "SELECT 
	       				t.id_transaction, t.client_id,t.receipt_no, t.mobile, t.id_branch, t.warehouse, t.record_to, t.payment_date, t.due_month, t.due_year, t.custom_entry_date, t.amount, t.weight, t.rate, t.metal, t.payment_mode, t.bank_name, t.branch_name, t.card_no, t.ref_no, t.pay_trans_id, t.payment_ref_number, t.paid_through, t.is_transferred, t.is_modified, t.transfer_date, t.new_customer, cr.id_scheme_account, t.discountAmt, t.payment_status, t.payment_type, t.due_type, t.receipt_no, t.date_add, t.date_upd, t.installment_no, t.remarks, t.gst, t.gst_type, t.emp_code, t.id_drawee,
		                d.branch as drawee_ac_branch,d.account_name as drawee_ac_name,d.account_no as drawee_ac_no,d.ifsc_code as drawee_ifsc,b.bank_name as drawee_bank 
                    FROM transaction t
                        LEFT JOIN drawee_account d on d.id_drawee = t.id_drawee
                        LEFT JOIN bank b on b.id_bank = d.id_bank 
                    LEFT JOIN customer_reg cr on cr.clientid=t.client_id  
            	    WHERE (cr.is_registered_online > 0 or t.is_modified=1) AND t.id_branch='$id_branch' and t.record_to='$record_to' and t.is_transferred='$trans_status'"; 
	    }
		if($tran_date != ""){
			$sql = $sql." and t.transfer_date='".$tran_date."'  limit 1000";
		}
		return $this->db->query($sql)->result_array();
	} */

	function getRegisteredAccTransactions($trans_status, $id_branch, $record_to, $tran_date = "")
	{
		$sql = "SELECT t.id_transaction, t.client_id, t.receipt_no, t.mobile,t.id_branch, t.warehouse, t.record_to, t.payment_date,t.due_month, t.due_year, t.custom_entry_date,t.amount, t.weight, t.rate, t.metal,t.payment_mode, t.bank_name, t.branch_name,t.card_no, t.ref_no, t.pay_trans_id,t.payment_ref_number, t.paid_through,t.is_transferred,t.saved_benefits_wgt,t.saved_benefit_amt,t.benefit_value,t.benefit_type, t.is_modified, t.transfer_date,t.new_customer,cr.id_scheme_account, t.discountAmt,t.payment_status, t.payment_type, t.due_type,t.date_add, t.date_upd, t.installment_no,t.remarks, t.gst, t.gst_type, t.emp_code,t.id_drawee,d.branch AS drawee_ac_branch,d.account_name AS drawee_ac_name,d.account_no AS drawee_ac_no,d.ifsc_code AS drawee_ifsc,b.bank_name AS drawee_bank
			FROM transaction t
			LEFT JOIN drawee_account d ON d.id_drawee = t.id_drawee
			LEFT JOIN bank b ON b.id_bank = d.id_bank
			LEFT JOIN customer_reg cr ON cr.clientid = t.client_id
			WHERE t.record_to = ?
			AND t.is_transferred = ?
			AND (
					 EXISTS (
						SELECT 1
						FROM customer_reg cr
						WHERE cr.clientid = t.client_id
						AND cr.is_registered_online > 0
					)
			)
		";

		$params = [$record_to, $trans_status];

		if ($id_branch === '') {
			$sql .= " AND t.id_branch IS NULL ";
		} elseif ($id_branch != -1) {
			$sql .= " AND t.id_branch = ? ";
			$params[] = $id_branch;
		}

		if ($tran_date != "") {
			$sql .= " AND t.transfer_date = ? ";
			$params[] = $tran_date;
		}

		$sql .= " ORDER BY t.id_transaction LIMIT 1000";
		
		return $this->db->query($sql, $params)->result_array();
	}

	
	function updateData($data,$branch_id,$table)
	{
		$this->db->where('ref_no',$data['ref_no']);
		 if($branch_id == ''){
		    $this->db->where('id_branch',null);
		 }else{
		     $this->db->where('id_branch',$branch_id);
		 }
		 $status = $this->db->update($table,$data);
		return $status;
	}
	
	function updateRegData($data,$branch_id,$table)
	{
		$this->db->where('clientid',$data['clientid']);
		 if($branch_id == ''){
		    $this->db->where('id_branch',null);
		 }else{
		     $this->db->where('id_branch',$branch_id);
		 }
		 $status = $this->db->update($table,$data);
		return $status;
	}
	
	function insertData($data,$table){
	    $status = $this->db->insert($table,$data);
	    //print_r($this->db->last_query());exit;
	    return $status;
	}
	
	function checkref($refno,$table)
	{
		$sql = "SELECT ref_no FROM $table WHERE ref_no='$refno'";
		return ($this->db->query($sql)->num_rows() >0 ? TRUE:FALSE);	
	}
	
	// new api related sync functions
	function checkClientID($id_scheme_account,$client_id="")
	{		
	    if($id_scheme_account == "" && $client_id != ""){
	        $sql = "select id_scheme_account,ref_no,scheme_acc_number,id_scheme from scheme_account where ref_no = '$client_id'";
	    }else{
	        $sql = "select id_scheme_account,ref_no,scheme_acc_number,id_scheme from scheme_account where id_scheme_account = ".$id_scheme_account;
	    }
		
		$account = $this->db->query($sql);	
		
		if($account->num_rows()>0 && ($account->row()->ref_no != '' || $account->row()->scheme_acc_number != null))
		{
			return array("status" => TRUE, "client_id" => $account->row()->ref_no,'id_scheme_account'=>$account->row()->id_scheme_account,'id_scheme'=>$account->row()->id_scheme );
		}
		else
		{
			return array("status" => FALSE);
		}	

	}

	function updatedirPayment($data,$pay_id) 
	{
		$sql = $this->db->query("select * from payment where id_payment='$pay_id'");
			
		if($sql->num_rows() > 1){
			return FALSE;
		}
		else{  

			$this->db->where('id_payment',$pay_id);
			$status = $this->db->update('payment',$data);
		
			if($status){
				$trans = array( 'is_transferred' => 'Y',
			                    'is_modified'    => 0,
								'receipt_no'     => $data['receipt_no'],
            					"date_upd"		 => date('Y-m-d'),
            					'transfer_date'	 => date('Y-m-d'));
    		    $this->db->where('ref_no',$pay_id);
        		$status = $this->db->update('transaction',$trans);
			}
			return $status;
		}		
	}

	function insertPayment($data)
	{
		$data['is_offline'] = 1;
		$pay_ref_no = $data['payment_ref_number'];
		
		// Scoped check for offline duplicate prevention
		$sql = $this->db->query("SELECT id_payment FROM payment WHERE is_offline=1 AND payment_ref_number='$pay_ref_no'");
			
		if ($sql->num_rows() > 0) {
			$trans = array(
				'is_transferred' => 'Y',
				"date_upd"		 => date('Y-m-d'),
				'transfer_date'	 => date('Y-m-d')
			);
    		$this->db->where('ref_no', $data['payment_ref_number']);
    		$this->db->where('record_to', 2);
        	$this->db->update('transaction', $trans);
			return array(
				'status' => FALSE,
				'remark' => 'ALREADY_SYNCED',
				'msg'    => 'Payment already exists in payment table for ref: ' . $pay_ref_no,
				'id'     => $sql->row()->id_payment,
			);
		} else {
			$status = $this->db->insert('payment', $data);
			$insert_id = $this->db->insert_id();
			if ($status) {	
			    $trans = array(
			    	'is_transferred' => 'Y',
					"date_upd"		 => date('Y-m-d'),
					'transfer_date'	 => date('Y-m-d')
				);
    		    $this->db->where('ref_no', $data['payment_ref_number']);
    		    $this->db->where('record_to', 2);
        		$status = $this->db->update('transaction', $trans);
				return array(
					'status' => TRUE,
					'remark' => 'INSERTED',
					'msg'    => 'New offline payment inserted',
					'id'     => $insert_id,
				);
			}
			return array(
				'status' => FALSE,
				'remark' => 'INSERT_FAILED',
				'msg'    => 'DB insert failed: ' . $this->db->_error_message(),
				'id'     => 0,
			);
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
	
	function update_account($data,$id_scheme_account,$id_customer_reg) 
	{
		$this->db->where('id_scheme_account',$id_scheme_account);
		$status = $this->db->update('scheme_account',$data);
		if($status){
			$cus_reg = array( 'is_transferred' => 'Y',
		                    'is_modified'    => 0,
							'clientid'   => $data['ref_no'],
							'scheme_ac_no'   => $data['scheme_acc_number'],
        					"date_update"		 => date('Y-m-d'),
        					'transfer_date'	 => date('Y-m-d'));
		    $this->db->where('id_customer_reg',$id_customer_reg);
    		$status = $this->db->update('customer_reg',$cus_reg);
		}
	//	echo $this->db->last_query();
		return $status;
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
	
	/* ------ API FUNCTIONS ENDS - STRICTLY FOR API ONLY -----------*/
	
	
	/* CURD FUNCTIONS FOR SYNC - STARTS */
	
	function revert_status($table,$data,$ref_no){
        $this->db->where('ref_no',$ref_no);
		 $status = $this->db->update($table,$data);
		return $status;
    }
    
    function deletePayandCus($id_payment){
		$status = FALSE;
		$this->db->where('ref_no',$id_payment);
		$delcus = $this->db->delete('customer_reg');
		if($delcus){
			$this->db->where('ref_no',$id_payment);
			$status = $this->db->delete('transaction');
		}
		
		return $status;
		
	} 
	
	function deleteTrans($id_payment){
		$status = FALSE;
		$this->db->where('ref_no',$id_payment);
		$this->db->where('ref_no',$id_payment);
		$status = $this->db->delete('transaction');
		
		return $status;
		
	}
	
	
	function revertPayment($id_payment) 
	{
		 $this->db->where('id_payment',$id_payment);
		 $status = $this->db->update('payment',array('payment_status'=>2));	
		return $status;
	}
	
	function revert_approve_log($data) 
	{
		$status = $this->db->insert('revert_approve_log',$data);
		return $status;
	}
	
	
	
	//  sa.group_code not need in transaction table so deleted by ranjith //
	
	
	function  getPaymentByID($id_payment)
	{
		
		$sql = "SELECT
				 Date_Format(p.date_payment,'%Y-%m-%d') as payment_date ,Date_Format(p.custom_entry_date,'%Y-%m-%d') as custom_entry_date ,p.ref_trans_id as pay_trans_id,
				  p.id_branch,e.emp_code,c.mobile,
				  payment_ref_number,p.added_by as paid_through,IF(p.receipt_no = '' || p.receipt_no is NULL ,NULL,p.receipt_no) as receipt_no,
				  IFNULL(p.gst,0)as gst,
				  IFNULL(p.gst_type,0)as gst_type,
				  IFNULL(sa.ref_no,'')as client_id,
				  IFNULL(p.discountAmt,'0.00') as discountAmt,
				  IFNULL(p.payment_amount,'0.00') as amount,
				  IFNULL(p.saved_benefit_amt,'0.00') as saved_benefit_amt,
				  IFNULL(p.metal_weight,'0.00') as weight,
				  IFNULL(p.saved_benefits,'0.000') as saved_benefits_wgt,
				  IFNULL(p.payment_mode,'') as payment_mode,
				  IFNULL(p.payment_status,'') as payment_status,
				  IFNULL(p.benefit_value,'') as benefit_value,
				  IFNULL(p.benefit_type,'') as benefit_type,
				  IFNULL(p.bank_name,'') as bank_name,
				  IFNULL(p.bank_branch,'') as  branch_name,
				  IFNULL(s.id_metal,'') as metal,
				  IFNULL(p.card_no,'')    as card_no,
				  IFNULL(sa.id_scheme_account,'') as id_scheme_account,
				  IFNULL(p.id_payment,'') as ref_no,
				  IF(sa.scheme_acc_number!='','N',sa.is_new) as new_customer,s.is_digi,
				  IFNULL(p.metal_rate,'') as rate,p.remark as remarks,
				  b.warehouse
				FROM
					".self::TAB_PAY." p
				LEFT JOIN ".self::TAB_ACC." sa ON (p.id_scheme_account = sa.id_scheme_account)
				LEFT JOIN ".self::TAB_CUS." c ON (sa.id_customer = c.id_customer)
				LEFT JOIN employee e ON e.id_employee = p.id_employee
				LEFT JOIN ".self::TAB_SCH." s ON (sa.id_scheme = s.id_scheme)
				LEFT JOIN branch b ON (b.id_branch = sa.id_branch)
				WHERE p.id_payment = '$id_payment'";
	//	print_r($sql);exit;
        $r = $this->db->query($sql);	
        return  $r->result_array();		
		
	}
	//check transaction already exists 
	function old_checkTransExists($ref_no)
	{
		$sql = "SELECT * FROM transaction WHERE ref_no = '$ref_no'";
		$r = $this->db->query($sql);
		if($r->num_rows >= 1)
		{
			return TRUE;
		}
	}

	function checkTransExists($ref_no)
	{

		$sql = "SELECT * FROM transaction WHERE ref_no = '$ref_no'";
		$r = $this->db->query($sql);
		if($r->num_rows >= 1)
		{
			$status = array('status' => TRUE,
			                'id_scheme_account' => $r->row()->id_scheme_account,
							'id_transaction' => $r->row()->id_transaction,
							'clientid' => $r->row()->client_id,
							'record_to' => $r->row()->record_to,
							'is_transferred' => $r->row()->is_transferred);
		}else{
			$status = array('status' => FALSE,
			                'id_scheme_account' => '');
		}
		return $status;
	}



	
	function checkTransCount($ref_no)
	{
		$sql = "SELECT count(id_transaction) FROM transaction WHERE ref_no = '$ref_no'";
		$r = $this->db->query($sql);
		if($r->num_rows >= 1)
		{
			return TRUE;
		}
	}
	
	//check customer reg already exists 
	function checkCusRegExists($id_scheme_account = "",$ref_no)
	{
		
		if($id_scheme_account == ""){
			$sql = "SELECT id_scheme_account,id_customer_reg,clientid,is_transferred FROM customer_reg WHERE ref_no = ".$ref_no;
		}else{
			$sql = "SELECT id_scheme_account,id_customer_reg,clientid,is_transferred FROM customer_reg WHERE id_scheme_account = '$id_scheme_account'";
		} 
		$r = $this->db->query($sql);
		if($r->num_rows >= 1)
		{
			$status = array('status' => TRUE,
			                'id_scheme_account' => $r->row()->id_scheme_account,
							'id_customer_reg' => $r->row()->id_customer_reg,
							'clientid' => $r->row()->clientid,
							'is_transferred' => $r->row()->is_transferred);
		}else{
			$status = array('status' => FALSE,
			                'id_scheme_account' => '');
		}
		return $status;
	}

	function old_checkCusRegExists($id_scheme_account = "",$ref_no)
	{
		
		if($id_scheme_account == ""){
			$sql = "SELECT id_scheme_account FROM customer_reg WHERE ref_no = ".$ref_no;
		}else{
			$sql = "SELECT id_scheme_account FROM customer_reg WHERE id_scheme_account = '$id_scheme_account'";
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
	
	function insert_transaction($data)
	{
		$remove_keys = [
			'id_scheme', 'scheme_name', 'scheme_amount', 'sync_scheme_code',
			'id_customer', 'scheme_acc_number', 'ref_trans_id', 'metal_rate'
		];

		// Remove unwanted keys safely
		$data = array_diff_key($data, array_flip($remove_keys));
		$status = $this->db->insert('transaction', $data);

		return $status;
	}
	
	//insert customer registration
	function insert_CustomerReg($data)
	{
		if($this->db->insert('customer_reg',$data))
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
	function getCustomerByID($id_scheme_account)
	{
		$sql = "SELECT
		               IF(ref_no = '' || ref_no is NULL ,'',ref_no) as clientid,
					   sa.id_scheme_account as  id_scheme_account,sa.id_branch,maturity_date,
		               Date_Format(sa.start_date,'%Y-%m-%d')  as reg_date,sh.sync_scheme_code,sh.code as group_code,
					   c.title as salutation,sa.account_name as ac_name,c.firstname,c.lastname,sa.group_code,sa.scheme_acc_number as scheme_ac_no, 
					   a.address1 as address1,a.address2 as address2,a.address3 as address3,ct.name as city,a.pincode,s.name as state,cy.name as country,
					   c.phone,c.mobile,c.email,sh.is_digi,
					   Date_Format(c.date_of_birth,'%Y-%m-%d') as dt_of_birth,
					   Date_Format(date_of_wed,'%Y-%m-%d') as wed_date,
					   IF(sa.scheme_acc_number!='','N',sa.is_new) as new_customer,c.reference_no as cus_ref_no
				FROM
							  customer c
				LEFT JOIN address a ON(c.id_customer=a.id_customer)
				LEFT JOIN country cy ON (a.id_country=cy.id_country)
				LEFT JOIN state s ON (a.id_state=s.id_state)
				LEFT JOIN city ct ON (a.id_city=ct.id_city)
				LEFT JOIN scheme_account sa ON (c.id_customer = sa.id_customer)
				LEFT JOIN scheme sh ON (sh.id_scheme = sa.id_scheme)
				WHERE sa.id_scheme_account='$id_scheme_account'";
		    
			$r = $this->db->query($sql);
		//	echo "<pre>";print_r($r->result_array());echo "</pre>";
        return  $r->result_array();				
	}

	function getCustomerDet($id_scheme_account)
	{
		$sql = "SELECT c.id_customer, IFNULL(c.nominee_mobile,'-')AS nomineeMobile, IFNULL(cy.name,'-') AS city, IFNULL(ad.pincode,'-') AS pincode, IFNULL(st.name,'-') AS state, IFNULL(cn.name,'-') AS country, s.total_installments,s.max_weight,s.maturity_installment,s.maturity_days,s.closing_maturity_days,s.maturity_type,IFNULL(v.village_name,'-') AS area, sa.id_scheme FROM customer c 
		LEFT JOIN scheme_account sa ON c.id_customer = sa.id_customer 
		LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme 
		LEFT JOIN address ad ON ad.id_customer = c.id_customer 
		LEFT JOIN village v ON c.id_village = v.id_village 
		LEFT JOIN city cy ON ad.id_city = cy.id_city 
		LEFT JOIN state st ON ad.id_state = st.id_state 
		LEFT JOIN country cn ON ad.id_country = cn.id_country 
		WHERE sa.id_scheme_account = '$id_scheme_account'";

		$r = $this->db->query($sql);
		// print_r($this->db->last_query()); exit;

		return $r->result_array();
	}
	

	
	/*  ------------ CURD FUNCTIONS FOR SYNC - STARTS -------------  */
	
	
	
	
	// new api fn ---KVP----------------------------------------------END----------------------------------------
	
	
	
	function  getPayment()
	{
		
		$sql = "SELECT
				  Date_Format(p.date_payment,'%Y-%m-%d') as trans_date ,Date_Format(p.custom_entry_date,'%Y-%m-%d') as custom_entry_date ,
				  sa.ref_no as client_id,sa.id_branch,
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
	
	/*function  getPaymentByID($id_payment)
	{
		
		$sql = "SELECT
				 Date_Format(p.date_payment,'%Y-%m-%d') as trans_date ,
				 if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
				  c.mobile as mobile,sa.id_branch,
				  IFNULL(sa.ref_no,'')as client_id,
				  IFNULL(p.discountAmt,'0.00') as discountAmt,
				  IFNULL(sa.scheme_acc_number,NULL) as msno,
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
				  IF(sa.scheme_acc_number!='','N',sa.is_new) as new_customer,
				  IFNULL(p.metal_rate,'') as rate
				FROM
					".self::TAB_PAY." p
				LEFT JOIN ".self::TAB_ACC." sa ON (p.id_scheme_account = sa.id_scheme_account)
				LEFT JOIN ".self::TAB_CUS." c ON (sa.id_customer = c.id_customer)
				LEFT JOIN ".self::TAB_SCH." s ON (sa.id_scheme = s.id_scheme)
				WHERE p.id_payment = '$id_payment'";
		
        $r = $this->db->query($sql);	
        return  $r->result_array();		
		
	}*/
	
		
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
					   Date_Format(date_of_wed,'%Y-%m-%d') as wed_date,sa.id_scheme_account as ref_no,
					   sa.is_new as new_customer
				FROM
							  customer c
				LEFT JOIN address a ON(c.id_customer=a.id_customer)
				LEFT JOIN country cy ON (a.id_country=cy.id_country)
				LEFT JOIN state s ON (a.id_state=s.id_state)
				LEFT JOIN city ct ON (a.id_city=ct.id_city)
				LEFT JOIN scheme_account sa ON (c.id_customer = sa.id_customer)";
		    
			$r = $this->db->query($sql);	
        return  $r->result_array();				
	}	
	
/*	function getCustomerByID($id_scheme_account)
	{
		$sql = "SELECT
					   sa.id_scheme_account as  id_scheme_account,sa.id_branch,
		               Date_Format(sa.start_date,'%Y-%m-%d')  as reg_date,
					   c.title as salutation, c.initials,
					   if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname)) as name,
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
				WHERE sa.id_scheme_account='$id_scheme_account'";
		    
			$r = $this->db->query($sql);	
        return  $r->result_array();				
	}*/
	
	//common_db functions for T.Nagar branch (id_branch - 1)
	
	/** select records **/
	
	// to get all customer reg records 	
	function getCustomerRegAll($branch)
	{
	    if($branch == ''){
	       	$sql = "SELECT * FROM customer_reg WHERE id_branch is null";
	    }else{
	        $sql = "SELECT * FROM customer_reg WHERE id_branch='$branch'";
	    }
	
		return $this->db->query($sql)->result_array();
	}	
	
	// to get customer reg records by transfer date
	function getCustomerRegByTransferDate($date)
	{
		$sql = "SELECT * FROM customer_reg where transfer_date = '$date'";
		return $this->db->query($sql)->result_array();
	}	
	
	// to get customer reg records by transfer status
	function getCustomerRegByTranStatus($status,$branch)
	{
	     if($branch == ''){
	         	$sql = "SELECT * FROM customer_reg where id_branch is null and transfer_jil = '$status'";
	     }else{
	         	$sql = "SELECT * FROM customer_reg where id_branch='$branch' and transfer_jil = '$status'";
	     }
	
		return $this->db->query($sql)->result_array();
	}
	
	//to get customer reg records by new or existing
	function getCustomerRegByType($status)
	{
		$sql = "SELECT * FROM customer_reg where transfer_jil = '$status'";
		return $this->db->query($sql)->result_array();
	}
	
	//to get all transaction records
	function getTransactionAll()
	{
		$sql = "SELECT * FROM transaction  where";
		return $this->db->query($sql)->result_array();
	}		
	
	//to get transaction record by id
	function getTransactionByID($id_transaction)
	{
		$sql = "SELECT * FROM transaction WHERE id_transaction='$id_transaction'";
		return $this->db->query($sql)->row_array();
	}		
	
	//to get transaction record by clientid
	function getTransactionByClientID($client_id)
	{
		$sql = "SELECT * FROM transaction WHERE client_id='$client_id'";
		return $this->db->query($sql)->result_array();
	}	
		
	//to get transaction record by transaction date
	function getTransactionByTransDate($trans_date)
	{
		$sql = "SELECT * FROM transaction WHERE transfer_date='$trans_date'";
		return $this->db->query($sql)->result_array();
	}		
	
	//to get transaction record by customer type
	function getTransactionByType($Type)
	{
		$sql = "SELECT * FROM transaction WHERE new_customer='$type'";
		return $this->db->query($sql)->result_array();
	}	
	
	//to get transaction record by transferred date
	function getTransactionByTransferDate($trans_date)
	{
		$sql = "SELECT * FROM transaction WHERE transfer_date='$trans_date'";
		return $this->db->query($sql)->result_array();
	}	

	function getPayIDdet($id_payment)
	{
		$sql = "SELECT sa.id_scheme, sa.id_scheme_account,sa.scheme_acc_number, s.scheme_name,sa.group_code,p.installment, s.sync_scheme_code, sa.id_customer, concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')) as customername,p.added_by, cr.clientid FROM payment p
		LEFT JOIN scheme_account sa ON p.id_scheme_account = sa.id_scheme_account
		left JOIN scheme s ON sa.id_scheme = s.id_scheme
		LEFT JOIN customer c ON sa.id_customer = c.id_customer
		LEFT JOIN customer_reg cr ON cr.id_scheme_account = sa.id_scheme_account
		WHERE p.id_payment = ? ";

		$r = $this->db->query($sql,array($id_payment));

		return $r->result_array();
	}
		
	//to get transaction record by transferred status
/*	function getTransactionByTranStatus($trans_status,$id_branch)
	{
	    if($id_branch == ''){
	        	$sql = "SELECT * FROM transaction WHERE id_branch is null and transfer_jil='$trans_status'";
	    }else{
	       	$sql = "SELECT * FROM transaction WHERE id_branch='$id_branch' and transfer_jil='$trans_status'"; 
	    }
	
		return $this->db->query($sql)->result_array();
	}	*/
 
    /*//check transaction already exists 
	function checkTransExists($trans_date,$approval_no,$ref_no)
	{
		$sql = "SELECT * FROM transaction WHERE ref_no = '$ref_no'";
		$r = $this->db->query($sql);
		if($r->num_rows >= 1)
		{
			return TRUE;
		}
	}	*/
	
	//to get all new customer records
	function getNewCustomerAll()
	{
		$sql = "SELECT * FROM new_customer";
		return $this->db->query($sql)->result_array();
	}		
		
	//to get new customer records by ref_no
	function getNewCustomerByRef($ref_no)
	{
		$sql = "SELECT * FROM new_customer WHERE ref_no = '$ref_no'";
		return $this->db->query($sql)->row_array();
	}	
	//to get new customer records by trans_status
	function getNewCustomerByTranStatus($status)
	{
		$sql = "SELECT * FROM new_customer WHERE transfer = '$status'";
		$r = $this->db->query($sql)->result_array();
		return $r;
	}	
	
	//to get new customer records by trans_date
	function getNewCustomerByTransDate($date)
	{
		$sql = "SELECT * FROM new_customer WHERE transfer_date = '$date'";
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
		$r = $common_db->get('new_customer');
	  
	  if($r->num_rows()>0)
	  {
		  return TRUE;
	  }
      	  
	}
	
	//Insert and update operations
	
	/*//insert customer registration
	function insert_CustomerReg($data)
	{
		if($this->db->insert('customer_reg',$data))
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
	}*/
	
	
	//update customer registration	
	function update_CustomerReg($data,$id)
	{
		$this->db->where('id_customer_reg',$id); 
		$status = $this->db->update('customer_reg',$data);
		return $status;
	}
	
	function getCustomerRegbyID($id_scheme_account)
	{
		 
	        $sql = "SELECT * FROM customer_reg WHERE id_scheme_account ='$id_scheme_account'";
	
		return $this->db->query($sql)->result_array();
	}

	//update customer registration by scheme a/c id
	function update_CusRegByIdSchAcc($data,$idschac)
	{
		$this->db->where('id_customer_reg',$idschac); 
		$status = $this->db->update('customer_reg',$data);
		return $status;
	}
	
	
  	function update_CustomerRegByRange($upperlimit,$lowerlimit,$data)
	{
		$sql = "UPDATE customer_reg SET transfer_jil = '".$data['transfer_jil']."', transfer_date = '".$data['transfer_date']."'   WHERE id_customer_reg  BETWEEN '$lowerlimit' AND '$upperlimit'";
		$status = $this->db->query($sql);
		return $status;
	}
	
	
	/*function insert_transaction($data)
	{
		$data['msno'] = ($data['msno'] == ''? NULL : $data['msno']);
		$status = $this->db->insert('transaction',$data);
		return $status;
	}*/
		
	function update_transaction($data,$id)
	{
		$this->db->where('id_transaction',$id); 
		$status = $this->db->update('transaction',$data);
		return $status;
	}
	
	function updateTransactionByRange($upperlimit,$lowerlimit,$data)
	{
		$sql = "UPDATE transaction SET transfer_jil = '".$data['transfer_jil']."', transfer_date = '".$data['transfer_date']."'   WHERE id_transaction BETWEEN '$lowerlimit' AND '$upperlimit'";
		$status = $this->db->query($sql);
		return $status;
	}
	
	function insert_newCustomer($data)
	{		
		$status = $this->db->insert('new_customer',$data);
		return $status;
	}
	
	function update_newCustomer($data,$id)
	{
		 $this->db->where('id_new_customer',$id); 
		$status =  $this->db->update('new_customer',$data);
		return $status;
	}
	
	
	//insert offline payments records
	function insert_offlinePayment($data)
	{
		if($this->db->insert('offline_payments',$data))
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
		$sql = "SELECT * FROM offline_payments WHERE is_trans_completed='$status' limit 1500";
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
	
	/*function insertPayment($data)
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
	}*/
	  
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
	/*function updatePayment($data) 
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
	}*/
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
		$status = $this->db->update('offline_payments',$data);
		return $status;
	}
	function checkBref($brefno)
	{
		$sql = "SELECT brefno FROM offline_payments WHERE brefno='$brefno'";
		return ($this->db->query($sql)->num_rows() >0 ? TRUE:FALSE);	
	}
	function getUpdatedClientID()
	{
		$common_db = $this->load->database('common_db',true);
		$sql = "SELECT group_concat(clientid) FROM offline_payments";
		return $common_db->query($sql)->result_array();	
	}  	
	
/*	function deletePayandCus($id_payment){
		$status = FALSE;
		$this->db->where('ref_no',$id_payment);
		$delcus = $this->db->delete('customer_reg');
		if($delcus){
			$this->db->where('ref_no',$id_payment);
			$status = $this->db->delete('transaction');
		}
		
		return $status;
		
	} */ 
	
	function updPayStatusInTrans($data){
	    $trans = array( 'is_transferred' => 'N',
	                    'is_modified'    => 1,
	                    'record_to'      => 1,
	                    'payment_status' => $data['payment_status'],
    					"date_upd"		 => date('Y-m-d'),
    					'transfer_date'	 => date('Y-m-d')
    				  );
	    $this->db->where('ref_no',$data['id_payment']);
		$status = $this->db->update('transaction',$trans);
		return $status;
	}
	
	function getCusRegData($clientId){	 
	    $sql = "SELECT firstname,lastname,mobile,cus_ref_no,id_branch FROM customer_reg WHERE clientid='".$clientId."'"; 
		return $this->db->query($sql)->row_array();
    } 
    
    function getrate_branchwise($id_branch){	
        if($id_branch == ''){
	        $sql = "SELECT  m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,
				Date_format(m.updatetime,'%Y-%m%-%d %h:%i %s')as date   FROM metal_rates m 
				WHERE m.id_metalrates=( SELECT max(m.id_metalrates) FROM metal_rates m ) ";
				//print_r($sql);exit;
	    }else{
	       	$sql = "SELECT  b.name as name,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,
                Date_format(m.updatetime,'%Y-%m%-%d %h:%i %s')as date ,br.id_branch  
                FROM metal_rates m 
                LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates
                left join branch b on b.id_branch=br.id_branch
                ".($id_branch!='' ?" WHERE br.id_branch=".$id_branch."" :'')."  ORDER by br.id_metalrate desc LIMIT 1"; 
	  // print_r($sql);exit;
	    }
	    
		return $this->db->query($sql)->result_array();
    }
    
    function isCusRegRecordExist($data){
        $resultArr = array();
        $this->db->select('clientid,id_branch,ac_name,mobile,ref_no');
        $this->db->from('customer_reg'); 
        $this->db->where('clientid', $data['clientid']);
        $this->db->where('id_branch', $data['id_branch']);
        $this->db->where('ac_name', $data['ac_name']);
        $this->db->where('mobile', $data['mobile']);
        $this->db->where('ref_no', $data['ref_no']);
        	
        $query = $this->db->get();
        if($query) 
        {
            if ( $query->num_rows() > 0 )
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $err_message = $this->db->_error_message();
            throw new Exception("Database Error occured.".$err_message); 
        }
    }
    
    
    function isPayRecordExist($data){
        $resultArr = array();
        $this->db->select('ref_no,id_branch,receipt_no');
        $this->db->from('transaction'); 
        $this->db->where('ref_no', $data['ref_no']);
        $this->db->where('id_branch', $data['id_branch']);
        $this->db->where('receipt_no', $data['receipt_no']);
        	
        $query = $this->db->get();
        if($query) 
        {
            if ( $query->num_rows() > 0 )
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $err_message = $this->db->_error_message();
            throw new Exception("Database Error occured.".$err_message); 
        }
    }

	function check_receipt($id_payment, $id_scheme_account){
		$sql = "SELECT cus.id_customer_reg, sch.scheme_acc_number,
					   t.id_transaction, t.receipt_no
				FROM scheme_account sch
				JOIN customer_reg cus ON cus.id_scheme_account = sch.id_scheme_account
				JOIN transaction t ON t.id_scheme_account = sch.id_scheme_account
				WHERE sch.id_scheme_account = '" . $id_scheme_account . "'
				  AND t.ref_no = '" . $id_payment . "'";
		return $this->db->query($sql)->row_array();
	}
    
}
?>