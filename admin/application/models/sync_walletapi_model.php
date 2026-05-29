<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Sync_walletapi_model extends CI_Model
{
   
	function __construct()
    {      
        parent::__construct();
    }
    
    // start of new api fn ---KVP----    
    
     /* API FUNCTIONS STARTS - STRICTLY FOR API ONLY */
    function insertData($data,$table){
	    $status = $this->db->insert($table,$data);
	    return	array('status'=>$status,'insertID'=>($status == TRUE ? $this->db->insert_id():''));
	}
	
	function updateData($data,$tran,$table)
	{
		$this->db->where('bill_no',$tran['bill_no']);
		 if($tran['id_branch'] == ''){
		    $this->db->where('id_branch',null);
		 }else{
		     $this->db->where('id_branch',$tran['id_branch']);
		 }
		 $status = $this->db->update($table,$data); 
		return $status;
	}
	
	function updateTransDetailData($data,$id)
	{
		$this->db->where('id_inter_waltransdetail',$id); 
		 $status = $this->db->update('inter_wallet_trans_detail',$data);
		return $status;
	}
	
	// #*# Table changed from inter_wallet_trans_detail to inter_walTransDetail_tmp 
	function updateTransDetailDataTmp($data,$id)
	{
		$this->db->where('id_tmp_waldetails',$id); 
		 $status = $this->db->update('inter_walTransDetail_tmp',$data);
		return $status;
	}
	
	function updateTransData($data,$id)
	{
		$this->db->where('id_inter_waltrans_tmp',$id); 
		 $status = $this->db->update('inter_wallet_trans_tmp',$data);
		return $status;
	}
	
	function updInterWalletAcc($data)
	{
		$this->db->where('mobile',$data['mobile']); 
		$status = $this->db->update('inter_wallet_account',array('available_points'=>$data['available_points'],'last_update'=>date('Y-m-d H:i:s')));
		return $status;
	}
	
	function updwallet($trans,$mobile)
	{
	    $sql = $this->db->query("select c.id_customer,id_wallet_account from customer  c left join wallet_account wa on wa.id_customer = c.id_customer where mobile=".$mobile);
		if($sql->num_rows() > 0){
			$id_wallet_ac = $sql->row('id_wallet_account');
			$b_date = (isset($trans['bill_date'])?date_create($record['bill_date']):date_create(date('Y-m-d H:i:s')));
            $bill_date = date_format($b_date,"Y-m-d H:i:s");
		    if( $id_wallet_ac == NULL){
	        	$insertData = array( 
        					   'id_customer' 	   =>  $sql->row('id_customer'), 
        					   'wallet_acc_number' => $this->get_wallet_acc_number(),
        					   'issued_date' 	   => date('y-m-d H:i:s'),
        					   'remark' 		   => "Credits",
        					   'active'		       => 1	                        
                               );
                $sql->free_result();
		        $createWalAc = $this->insertData($insertData,'wallet_account');
		        $id_wallet_ac = $createWalAc['insertID']; 
		    } 
		    if(isset($trans['debit_points'])){
		        if($trans['debit_points'] > 0){
		            $debitdata = array('id_wallet_account'   => $id_wallet_ac,
						  'date_add' 	        => date('Y-m-d H:i:s'),
						  'date_transaction'    =>$bill_date,
						  'transaction_type'	=> 1, // debit
						  'value'				=> $trans['debit_points'],
						  'ref_no'              => $trans['bill_no'].'-'.$trans['category_code'],
						  'description'			=> 'Debited for bill no '.$trans['bill_no'].' '.(isset($trans['bill_date']) ? 'on'.' '.$trans['bill_date'] :'') ,
						  ); 
						 
				    $this->db->insert('wallet_transaction',$debitdata);   
		        }
		    }
		  // print_r($trans);exit;
			$data = array('id_wallet_account'   => $id_wallet_ac,
						  'date_add' 	        => date('Y-m-d H:i:s'),
						  'date_transaction'    =>$bill_date,
						  'transaction_type'	=> ($trans['trans_type'] == 1 ? 0 : 1),
						  'value'				=> $trans['trans_points'],
						  'ref_no'              => $trans['bill_no'].'-'.$trans['category_code'],
						  'description'			=> 'Credited for bill no '.$trans['bill_no'].' '.(isset($trans['bill_date']) ? 'on'.' '.$trans['bill_date'] :'') ,
						  ); 
						 
			$status = $this->db->insert('wallet_transaction',$data);         
			//var_dump($status);exit; 
			return $status;
		}else{
			return TRUE;
		}
	}
	
	function updInterwalletTables($trans,$mobile)
	{
	    $sql = $this->db->query("select c.id_customer,id_wallet_account from customer  c left join wallet_account wa on wa.id_customer = c.id_customer where mobile=".$mobile);
		if($sql->num_rows() > 0){
			$id_wallet_ac = $sql->row('id_wallet_account');
			$b_date = (isset($trans['bill_date'])?date_create($record['bill_date']):date_create(date('Y-m-d H:i:s')));
			$bill_date = date_format($b_date,"Y-m-d H:i:s");
		    if( $id_wallet_ac == NULL){
	        	$insertData = array( 
        					   'id_customer' 	   =>  $sql->row('id_customer'), 
        					   'wallet_acc_number' => $this->get_wallet_acc_number(),
        					   'issued_date' 	   => date('y-m-d H:i:s'),
        					   'remark' 		   => "Credits",
        					   'active'		       => 1	                        
                               );
                $sql->free_result();
		        $createWalAc = $this->insertData($insertData,'wallet_account');
		        $id_wallet_ac = $createWalAc['insertID']; 
		        if(isset($trans['debit_points'])){
    		        if($trans['debit_points'] > 0){
    		            $debitdata = array('id_wallet_account'   => $id_wallet_ac,
    						  'date_add' 	        => date('Y-m-d H:i:s'),
						      'date_transaction'    =>$bill_date,
    						  'transaction_type'	=> 1, // debit
    						  'value'				=> $trans['debit_points'],
    						  'ref_no'              => $trans['bill_no'].'-'.$trans['category_code'],
    						  'description'			=> 'Debited for for bill no '.$trans['bill_no'].' '.(isset($trans['bill_date']) ? 'on'.' '.$trans['bill_date'] :'') ,
    						  ); 
    						 
    				    $this->db->insert('wallet_transaction',$debitdata);   
    		        }
    		    } 
    			$data = array('id_wallet_account'   => $id_wallet_ac,
    						  'date_add' 	        => date('Y-m-d H:i:s'),
						      'date_transaction'    =>$bill_date,
    						  'transaction_type'	=> ($trans['trans_type'] == 1 ? 0 : 1),
    						  'value'				=> $trans['trans_points'],
    						  'ref_no'              => $trans['bill_no'].'-'.$trans['category_code'],
    						  'description'			=> 'Credited for bill no '.$trans['bill_no'].' '.(isset($trans['bill_date']) ? 'on'.' '.$trans['bill_date'] :'') ,
    						  ); 
    						 
    			$status = $this->db->insert('wallet_transaction',$data);   
    			return $status;
		    }else{ 
		        if(isset($trans['debit_points'])){
    		        if($trans['debit_points'] > 0){
    		            $debitdata = array('id_wallet_account'   => $id_wallet_ac,
						      'date_transaction'    =>$bill_date,
    						  'value'				=> $trans['debit_points'],
    						  'description'			=> 'Debited for for bill no '.$trans['bill_no'].' '.(isset($trans['bill_date']) ? 'on'.' '.$trans['bill_date'] :'') ,
    						  ); 
    					$this->db->where('transaction_type',1);
    					$this->db->where('ref_no',$trans['bill_no'].'-'.$trans['category_code']);
    					$this->db->where('id_wallet_account',$id_wallet_ac);
    				    $this->db->update('wallet_transaction',$debitdata);   
    		        }
    		    } 
    			$data = array('id_wallet_account'   => $id_wallet_ac,
						      'date_transaction'    =>$bill_date,
    						  'value'				=> $trans['trans_points'],
    						  'description'			=> 'Credited for bill no '.$trans['bill_no'].' '.(isset($trans['bill_date']) ? 'on'.' '.$trans['bill_date'] :'') ,
    						  ); 
    						 
    			$this->db->where('transaction_type',0);
				$this->db->where('ref_no',$trans['bill_no'].'-'.$trans['category_code']);
				$this->db->where('id_wallet_account',$id_wallet_ac);
			    $status = $this->db->update('wallet_transaction',$data);   
    			return $status;
		    } 
		    
		}else{
			return TRUE;
		}
	}
	
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
	
	function getInterWalletCustomer($mobile){
		$sql = $this->db->query("SELECT * FROM  inter_wallet_account WHERE mobile=".$mobile);
		if($sql->num_rows() > 0){
			return array('status'=>true,'data' =>$sql->row_array());
		}else{
			$cus = $this->db->query("SELECT id_customer FROM  customer WHERE mobile=".$mobile);
			return array('status'=>false,'data' =>'','cusData'=>$cus->row_array());
		}
	}
	
	
	
	
	
	// functions for online read and update
    function getNewWalletTransByStatus($trans_status,$record_to){	
        $sql = "SELECT  wt.mobile,`use_points`,`bill_no`,`id_branch`,`category_code`,`amount`,`trans_type`,`redeem_req_pts`,`record_to`,`is_modified`,`is_transferred`,`available_points` FROM inter_wallet_trans wt 
        LEFT JOIN inter_wallet_account wa on wa.mobile= wt.mobile
        WHERE record_to='$record_to' and is_transferred='$trans_status' limit 500"; 
		return $this->db->query($sql)->result_array();
    }
    
    function getWcategorySettings_old($cat_code = ""){	
    	if($cat_code == ""){
			$sql = $this->db->query("SELECT id_wcat_settings,`value`,`point`,`id_category`,`redeem_percent`,ws.`active`,`code`,ws.`active` FROM wallet_category_settings ws 
        		LEFT JOIN wallet_category wc on  wc.id_wallet_category = ws.id_category and wc.active=1
        WHERE ws.active=1"); 
        	return $sql->result_array();
		}else{
			$sql = $this->db->query("SELECT id_wcat_settings,`value`,`point`,`id_category`,`redeem_percent`,ws.`active`,`code`,ws.`active` FROM wallet_category_settings ws 
        		LEFT JOIN wallet_category wc on  wc.id_wallet_category = ws.id_category and wc.active=1
        WHERE ws.active=1 and wc.code='".$cat_code."'"); 
        	return $sql->row_array();
		}
      //  echo $this->db->last_query();
	
    }
    
    function getWcategorySettings($cat_code = ""){	
    	if($cat_code == ""){
			$sql = $this->db->query("SELECT id_wcat_settings,`value`,`point`,`id_category`,`redeem_percent`,ws.`active`,`code`,ws.`active` FROM wallet_category_settings ws 
        		LEFT JOIN wallet_category wc on  wc.id_wallet_category = ws.id_category and wc.active=1
        WHERE ws.active=1"); 
        	return $sql->result_array();
		}else{
			$sql = $this->db->query("SELECT id_wcat_settings,`value`,`point`,`id_category`,`redeem_percent`,ws.`active`,`code`,ws.`active` FROM wallet_category_settings ws 
        		LEFT JOIN wallet_category wc on  wc.id_wallet_category = ws.id_category and wc.active=1
        WHERE ws.active=1 and wc.code='".$cat_code."'"); 
        	return $sql->row_array();
		}
      //  echo $this->db->last_query();
	
    }
    
    
    function getUpdatedWalletTransByStatus($trans_status,$id_branch,$record_to){	
        if($id_branch == ''){
	        $sql = "SELECT * FROM  inter_wallet_trans WHERE id_branch is null and record_to='$record_to' and is_modified=1 and is_transferred='$trans_status'";
	    }else{
	       	$sql = "SELECT * FROM  inter_wallet_trans WHERE id_branch='$id_branch' and  is_modified=1 and record_to='$record_to' and is_transferred='$trans_status'"; 
	    }
	
		return $this->db->query($sql)->result_array();
    }
    
    function getWalletTransByStatus($trans_status,$id_branch,$record_to){	
        if($id_branch == ''){
	        $sql = "SELECT * FROM  inter_wallet_trans WHERE id_branch is null and record_to='$record_to' and is_transferred='$trans_status'";
	    }else{
	       	$sql = "SELECT * FROM  inter_wallet_trans WHERE id_branch='$id_branch' and record_to='$record_to' and is_transferred='$trans_status'"; 
	    }
	
		return $this->db->query($sql)->result_array();
    }
    
    function getWalTrans($data){	 
        if($data['id_branch'] == ''){
	       $sql = $this->db->query("SELECT * FROM  inter_wallet_trans_tmp WHERE id_branch is null and mobile=".$data['mobile']." and bill_no='".$data['bill_no']."' order by id_inter_waltrans_tmp asc limit 1");
	    }else{
	       	$sql = $this->db->query("SELECT * FROM  inter_wallet_trans_tmp WHERE id_branch=".$data['id_branch']." and mobile=".$data['mobile']." and bill_no='".$data['bill_no']."' order by id_inter_waltrans_tmp asc limit 1"); 
	    } 
	    if($sql->num_rows() > 0){
			return	array('status'=>TRUE,'tData'=>$sql->row_array());
		}else{
			return	array('status'=>false,'tData'=> NULL);
		}
		
    }
    
    function getSyncWalletData($id_branch){	
        if($id_branch >= 12){
            $sql = "SELECT * FROM  inter_sync_wallet WHERE  branch_".$id_branch."= 0 limit 250"; // edited    branch_".$id_branch."= 0
        }else{
            $sql = "SELECT * FROM  inter_sync_wallet WHERE  branch_".$id_branch."= 2 limit 250"; // edited    branch_".$id_branch."= 0
        }
    	
		return $this->db->query($sql)->result_array();
    }
	 
	function getSyncWalletById($mobile){	
        $sql = "SELECT * FROM  inter_sync_wallet WHERE mobile=".$mobile;
		return $this->db->query($sql)->row_array();
    }
	
	function updateSyncWal($data){	
        $this->db->where('id_inter_sync_wallet',$data['id_inter_sync_wallet']); 
		$status = $this->db->update('inter_sync_wallet',$data);
		return $status;
    }
    
    // #*# Table changed from inter_wallet_trans_detail to inter_walTransDetail_tmp 
    function isWalDetailExist($data){
        $sql = $this->db->query("SELECT * FROM  inter_walTransDetail_tmp WHERE category_code='".$data['category_code']."' and id_inter_wallet_trans='".$data['id_inter_wallet_trans']."'"); 
        if($sql->num_rows() > 0){
			return	array('status'=>TRUE,'data'=>$sql->row_array());
		}else{
			return	array('status'=>false,'data'=> NULL);
		}
    }
	
	function updateWalsmsSettings($sentSMS){	 
		$status = $this->db->query('UPDATE inter_wallet_smsSettings SET sent_sms = sent_sms +'.$sentSMS.'
			WHERE active=1'); 
		return $status;
    }
	
	
	}
?>