<?php if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Wallet_model extends CI_Model
{
	const CUS_TABLE			= "customer";
	const EMP_TABLE			= "employee";
	const WALLSET_TABLE 	= "wallet_settings";
	const WALLACC_TABLE 	= "wallet_account";
	const WALTRAN_TABLE 	= "wallet_transaction";
	const WALCATE_TABLE 	= "wallet_category";
	const WALCATESETT_TABLE = "wallet_category_settings";
	
		
	 function get_customers($type)
    {
		$sql = "select id_customer as id,concat(firstname,' ',if(lastname!=NULL,lastname,'')) as name,mobile
			    from  ".self::CUS_TABLE." 
				where  active=1 " 
				.($type=='add'? ' and   id_customer not in (select id_customer from '.self::WALLACC_TABLE.' wa where wa.active=1) ':'').
				" order by name ";
		$customers=$this->db->query($sql);
		return $customers->result_array();
	}
	
	
	 function get_employee($type)
    {
		$sql = "select id_employee as id,concat(firstname,' ',if(lastname!=NULL,lastname,'')) as name,mobile
			    from  ".self::EMP_TABLE." 
				where  active=1 " 
				.($type=='add'? ' and   id_employee not in (select idemployee from '.self::WALLACC_TABLE.' wa where wa.active=1 and idemployee is not null) ':'').
				" order by name ";
		$customers=$this->db->query($sql);
		return $customers->result_array();
	}
	function get_wallet_setting()
	{
		$sql="Select *
				From wallet_settings
				Where id_wallet = (select max(id_wallet) from wallet_settings where active=1)";
		$setting=$this->db->query($sql);
		return $setting->row_array();		
	}
		
	function wallet_settingDB($type="",$id="",$wallet_array="")
	{
		switch($type)
		{
			case 'get':
			
			      $sql = "Select
						       id_wallet,
						       name,
						       if(type=0,'Rupee','Point') as`type`,
						       currency,
						       `value`,
						       Date_Format(effective_date,'%d-%m-%Y') as effective_date,
						       effect_previous,
						       value,
						       active
						From  wallet_settings ".($id!=null? 'Where id_wallet='.$id:'');
				  $r = $this->db->query($sql);	
				  if($id!=NULL)
				  {
				  	return $r->row_array(); //for single row
				  }	
				  else
				  {
				  	return $r->result_array(); //for multiple rows
				  }
				
			 break;
			 case 'get_max':
			       $sql = "Select
						       id_wallet,
						       name,
						       if(type=0,'Currency','Point') as`type`,
						       currency,
						       `value`,
						       Date_Format(effective_date,'%d-%m-%Y') as effective_date,
						       effect_previous,
						       value,
						       active
						   From  wallet_settings 
						   Where active=1 
						   Order BY id_wallet DESC
						   Limit 1 
						   ";
				  $r = $this->db->query($sql)->row_array();
			 
			    break;
			case 'insert': //insert operation
		                $status = $this->db->insert(self::WALLSET_TABLE,$wallet_array);
 						return	array('status'=>$status,'insertID'=>($status == TRUE ? $this->db->insert_id():''));
			      break; 
			case 'update': //update operation
						 $this->db->where("id_wallet",$id);
			             $status = $this->db->update(self::WALLSET_TABLE,$wallet_array);
					     return	array('status' => $status, 'updateID' => $id);     			
			      break;      
			case 'delete':
				   $this->db->where("id_wallet",$id);
		           $status = $this->db->delete(self::WALLSET_TABLE);
				   return	array('status' => $status, 'deleteID' => $id);  	
			      break;      
			 
			default: //empty record
				  $wallet =array(
		  							'id_wallet'       => NULL,
		  							'name'        	  => NULL,
		  							'type'      	  => 1,
		  							'currency'  	  => NULL,
		  							'value'    	  => NULL,
		  							'effective_date'  => date("d-m-Y"),
		  							'effect_previous' => 0, 
		  							'active'          => 1 
		  							
		  					   );	
			      return $wallet;
		}
	}
	
	
	function get_wallet_acc_number()
	{
	  $query = $this->db->query("SELECT LPAD(round(rand() * 10000000),8,0) as myCode
								FROM `".self::WALLACC_TABLE."`
								HAVING myCode NOT IN (SELECT wallet_acc_number FROM `".self::WALLACC_TABLE."`) limit 0,1");
		if($query->num_rows()==0){
			$query = $this->db->query("SELECT LPAD(round(rand() * 10000000),8,0) as myCode");
		}
		return $query->row()->myCode;
	}

	
	function wallet_accountDB($type="",$id="",$wallet_array="")
	{
		switch($type)
		{
			case 'get':
			
			      $sql = "Select wa.id_wallet_account, c.id_customer,wa.idemployee, if(c.id_customer is not null ,Concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')),
					if(wa.idemployee is not null,Concat(emp.firstname,' ',if(emp.lastname!=NULL,emp.lastname,'')),'')) as name,
					if(c.id_customer is not null ,c.mobile,if(wa.idemployee is not null,emp.mobile,'')) as mobile,
					if(c.id_customer is not null ,c.email,if(wa.idemployee is not null,emp.email,'')) as email,
					Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_name,
					wa.wallet_acc_number, Date_Format(wa.issued_date,'%d-%m-%Y') as issued_date, wa.remark, wa.active,
					SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) as issues, SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END) as redeem,
					 (SUM(CASE WHEN wt.transaction_type=0 THEN wt.`value` ELSE 0 END) - SUM(CASE WHEN wt.transaction_type=1 THEN wt.`value` ELSE 0 END)) as balance,if(wa.id_customer,'CUS','EMP')as type
					 From wallet_account wa
					Left Join customer c on (wa.id_customer=c.id_customer)
					Left Join employee e on (wa.id_employee=e.id_employee)
					Left Join employee emp on (wa.idemployee=emp.id_employee)
					Left Join wallet_transaction wt on (wa.id_wallet_account=wt.id_wallet_account)
					".($id!=null? 'Where wa.id_wallet_account='.$id:'')." 
								Group By wa.id_wallet_account";
								
				  $r = $this->db->query($sql);	
				  if($id!=NULL)
				  {
				  	return $r->row_array(); //for single row
				  }	
				  else
				  {
				  	return $r->result_array(); //for multiple rows
				  }
				
			 break;
			case 'insert': //insert operation
		                $status = $this->db->insert(self::WALLACC_TABLE,$wallet_array);
 						return	array('status'=>$status,'insertID'=>($status == TRUE ? $this->db->insert_id():''));
			      break; 
			case 'update': //update operation
						 $this->db->where("id_wallet_account",$id);
			             $status = $this->db->update(self::WALLACC_TABLE,$wallet_array);
					     return	array('status' => $status, 'updateID' => $id);     			
			      break;    
			case 'delete':
				   $this->db->where("id_wallet_account",$id);
		           $status = $this->db->delete(self::WALLACC_TABLE);
				   return	array('status' => $status, 'deleteID' => $id);  	
			      break;        
			 
			default: //empty record
				  $wallet =array(
		  							'id_wallet_account'       => NULL,
		  							'id_customer'        	  => NULL,
		  							'idemployee'        	  => NULL,									
		  							'wallet_acc_number'  	  => $this->get_wallet_acc_number(),
		  							'issued_date'			  => date("d-m-Y"),
		  							'remark' 				  => NULL,
		  							'active' 				  => 1
		  					   );	
			      return $wallet;
		}
	}
	
	function wallet_transactionDB_by_range($from_date="",$to_date="",$issue_type="")
	{
		
		$sql = "Select
							  wt.id_wallet_transaction,
							  wt.id_wallet_account,
							  c.id_customer,emp.id_employee,							  
							  if(c.id_customer is not null ,Concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')),
							if(wa.idemployee is not null,Concat(emp.firstname,' ',if(emp.lastname!=NULL,emp.lastname,'')),'')) as name,
							if(c.id_customer is not null ,c.mobile,if(wa.idemployee is not null,emp.mobile,'')) as mobile,
							if(c.id_customer is not null ,c.email,if(wa.idemployee is not null,emp.email,'')) as email,
							  Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_name,
							  wa.wallet_acc_number,
							  wa.issued_date,
							  Date_Format(wt.date_transaction,'%d-%m-%Y') as date_transaction,
							  wt.transaction_type,
							  wt.value,
							  wt.description,
							  wa.active,if(wa.id_customer,'CUS','EMP')as type
						From wallet_transaction wt
						Left Join wallet_account wa on (wt.id_wallet_account=wa.id_wallet_account)
						Left Join customer c on (wa.id_customer=c.id_customer)
						Left Join employee emp on (wa.idemployee=emp.id_employee)
						Left Join employee e on (wa.id_employee=e.id_employee)
						".($from_date!=''&& $to_date!=''?' Where(date(wt.date_transaction) BETWEEN "'.date('Y-m-d',strtotime($from_date)).'" AND "'.date('Y-m-d',strtotime($to_date)).'"  '.($issue_type!='' ? " AND wt.transaction_type=".$issue_type :'').')':'')."";
						
			return $this->db->query($sql)->result_array();
	}
	
	
	function wallet_transactionDB($type="",$id="",$wallet_array="")
	{
		switch($type)
		{
			case 'get':
			
			      $sql = "Select
							  wt.id_wallet_transaction,
							  wt.id_wallet_account,
							  c.id_customer,emp.id_employee,							  
							  if(c.id_customer is not null ,Concat(c.firstname,' ',if(c.lastname!=NULL,c.lastname,'')),
							if(wa.idemployee is not null,Concat(emp.firstname,' ',if(emp.lastname!=NULL,emp.lastname,'')),'')) as name,
							if(c.id_customer is not null ,c.mobile,if(wa.idemployee is not null,emp.mobile,'')) as mobile,
							if(c.id_customer is not null ,c.email,if(wa.idemployee is not null,emp.email,'')) as email,
							  Concat(e.firstname,' ',if(e.lastname!=NULL,e.lastname,'')) as emp_name,
							  wa.wallet_acc_number,
							  wa.issued_date,
							  Date_Format(wt.date_transaction,'%d-%m-%Y') as date_transaction,
							  wt.transaction_type,
							  wt.value,
							  wt.description,
							  wa.active,if(wa.id_customer,'CUS','EMP')as type
						From wallet_transaction wt
						Left Join wallet_account wa on (wt.id_wallet_account=wa.id_wallet_account)
						Left Join customer c on (wa.id_customer=c.id_customer)
						Left Join employee emp on (wa.idemployee=emp.id_employee)
						Left Join employee e on (wa.id_employee=e.id_employee) ".($id!=null? 'Where id_wallet_transaction='.$id:'');
				  $r = $this->db->query($sql);	
				  if($id!=NULL)
				  {
				  	return $r->row_array(); //for single row
				  }	
				  else
				  {
				  	return $r->result_array(); //for multiple rows
				  }
				
			 break;
			case 'insert': //insert operation
		                $status = $this->db->insert(self::WALTRAN_TABLE,$wallet_array);
 						return	array('status'=>$status,'insertID'=>($status == TRUE ? $this->db->insert_id():''));
			      break; 
			case 'update': //update operation
						 $this->db->where("id_wallet_transaction",$id);
			             $status = $this->db->update(self::WALTRAN_TABLE,$wallet_array);
					     return	array('status' => $status, 'updateID' => $id);     			
			      break;  
			case 'delete':
				   $this->db->where("id_wallet_transaction",$id);
		           $status = $this->db->delete(self::WALTRAN_TABLE);
				   return	array('status' => $status, 'DeleteID' => $id);  	
			      break;          
			 
			default: //empty record
				  $wallet =array(
		  							'id_wallet_transaction'   => NULL,
		  							'id_wallet_account'       => NULL,
		  							'date_transaction'       => NULL,
		  							'transaction_type'  	  => NULL,
		  							'value'			          => 0,
		  							'description' 			  => NULL, 
		  							'active'                  => 1 
		  					   );	
			      return $wallet;
		}
	}
	
	
	// wallet category // 
	
	function walletcategory_settingDB($type="",$id="",$wallet_array="")
	{
		switch($type)
		{
			case 'get':
			
			      $sql = "SELECT w.id_wallet_category, w.code, w.name,Date_Format(w.date_add,'%d-%m-%Y') as date_add,w.date_upd,w.active  FROM wallet_category w ".($id!=null? 'Where w.id_wallet_category='.$id:'');
				  $r = $this->db->query($sql);	
				  if($id!=NULL)
				  {
				  	return $r->row_array(); //for single row
				  }	
				  else
				  {
				  	return $r->result_array(); //for multiple rows
				  }
				
			 break;
			 case 'get_max':
			       $sql = "SELECT * FROM wallet_category w 
						   Where w.active=1 
						   Order BY w.id_wallet_category DESC
						   Limit 1 
						   ";
				  $r = $this->db->query($sql)->row_array();
			 
			    break;
			case 'insert': //insert operation
		                $status = $this->db->insert(self::WALCATE_TABLE,$wallet_array);
 						return	array('status'=>$status,'insertID'=>($status == TRUE ? $this->db->insert_id():''));
			      break; 
			case 'update': //update operation
						 $this->db->where("id_wallet_category",$id);
			             $status = $this->db->update(self::WALCATE_TABLE,$wallet_array);
					     return	array('status' => $status, 'updateID' => $id);     			
			      break;      
			case 'delete':
				   $this->db->where("id_wallet_category",$id);
		           $status = $this->db->delete(self::WALCATE_TABLE);
				   return	array('status' => $status, 'deleteID' => $id);  	
			      break;      
			 
			default: //empty record
				  $wallet_category =array(
		  							'id_wallet_category'   => NULL,
		  							'code'        	       => NULL,
		  							'name'      	       => NULL,
		  							'date_add'  	       => date("d-m-Y"),
		  							'date_upd'    	       => NULL,
		  							'active'               => 1
		  					   );	
			      return $wallet_category;
		}
	}
	
	public function walletcategory($data,$id)

    {    	

    	$edit_flag=0;

    	$this->db->where('id_wcat_settings',$id); 

		$wallcate=$this->db->update(self::WALCATE_TABLE,$data);		

		return $wallcate;

	}
	
	
	
	// wallet category settingsDB // 
	
	function wallet_category_settingsDB($type="",$id="",$wallet_array="")
	{
		switch($type)
		{
			case 'get':
			
			      $sql = "SELECT w.id_wcat_settings,
						IFNULL(w.value,0)as value,IFNULL(w.point,'') as point,
						 IFNULL(w.redeem_percent,'')as redeem_percent,concat(wc.code,' - ',wc.name) as name,Date_Format(w.date_add,'%d-%m-%Y') as date_add,
						w.remark, w.last_update, w.active,c.currency_symbol
						FROM wallet_category_settings w
						join chit_settings c
						Left Join wallet_category wc on (w.id_category=wc.id_wallet_category)".($id!=null? 'Where w.id_wcat_settings='.$id:'');
				  $r = $this->db->query($sql);	
				  if($id!=NULL)
				  {
				  	return $r->row_array(); //for single row
				  }	
				  else
				  {
				  	return $r->result_array(); //for multiple rows
				  }
				
			 break;
			 case 'get_max':
			       $sql = "SELECT w.id_wcat_settings,
						IFNULL(w.value,0)as value,IFNULL(w.point,'') as point,
						 IFNULL(w.redeem_percent,'')as redeem_percent,wc.code,wc.name,Date_Format(w.date_add,'%d-%m-%Y') as date_add,
						w.remark, w.last_update, w.active,c.currency_symbol
						FROM wallet_category_settings w
						join chit_settings c
						Left Join wallet_category wc on (w.id_category=wc.id_wallet_category)
						Order BY w.id_wcat_settings DESC Limit 1  ";
				  $r = $this->db->query($sql)->row_array();
			 
			    break;
			case 'insert': //insert operation
		                $status = $this->db->insert(self::WALCATESETT_TABLE,$wallet_array);
 						return	array('status'=>$status,'insertID'=>($status == TRUE ? $this->db->insert_id():''));
			      break; 
			case 'update': //update operation
						 $this->db->where("id_wcat_settings",$id);
			             $status = $this->db->update(self::WALCATESETT_TABLE,$wallet_array);
					     return	array('status' => $status, 'updateID' => $id);     			
			      break;      
			case 'delete':
				   $this->db->where("id_wcat_settings",$id);
		           $status = $this->db->delete(self::WALCATESETT_TABLE);
				   return	array('status' => $status, 'deleteID' => $id);  	
			      break;      
			 
			default: //empty record
				  $wallet_category =array(
		  							'id_wcat_settings'     => NULL,
		  							'id_category'          => NULL,
		  							'point'       => NULL,
		  							'redeem_percent'       => NULL,
		  							'value'                => 0,
		  							'remark'               => NULL,
		  							'date_add'  	       => date("d-m-Y"),
		  							'last_update'          => NULL,
		  							'active'               => 1
		  					   );	
			      return $wallet_category;
		}
	}
	
	public function walletcategory_setting_status($data,$id)
    {    	

    	$edit_flag=0;

    	$this->db->where('id_wcat_settings',$id); 

		$wallcate=$this->db->update(self::WALCATESETT_TABLE,$data);		

		return $wallcate;

	}
	
	
    function insChitwallet($id_wal_ac,$mobile,$id_customer)
	{
		$redeem_updated = [];
		$sql = $this->db->query("select date_format(iwt.entry_date,'%d-%m-%Y') as bill_date,iwd.trans_points,iwt.actual_redeemed,iwt.bill_no,category_code,trans_type from inter_wallet_trans	 iwt
		LEFT JOIN  inter_wallet_trans_detail iwd on iwd.id_inter_wallet_trans = iwt.id_inter_wallet_trans
		where mobile=".$mobile);
    	if($sql->num_rows() > 0){
		    foreach($sql->result_array() as $record){ 
		    	$b_date = date_create($record['bill_date']);
                $bill_date = date_format($b_date,"Y-m-d H:i:s");
    		        if($record['actual_redeemed'] > 0 ){
    		        	$debitdata = array('id_wallet_account'  => $id_wal_ac,
                						  'date_add' 	=> date('Y-m-d H:i:s'),
                						  'date_transaction' 	=> $bill_date,
                						  'transaction_type'	=> 1, // debit
                						  'value'				=> $record['actual_redeemed'],
                						  'ref_no'              => $record['bill_no'].'-'.$record['category_code'],
                						  'description'			=> 'Debited for bill no '.$record['bill_no'].' on '.$record['bill_date'],
                						  );
    		        	if(sizeof($redeem_updated) > 0){
    		        		$alreadyUpdated = 0;
    		        		foreach($redeem_updated as $k=>$v){
								if($k == $record['bill_no']){
									$alreadyUpdated = 1;
								}
							}	
							if($alreadyUpdated == 0){
								$this->db->insert('wallet_transaction',$debitdata);
    				    		$redeem_updated[$record['bill_no']]=1;
							}
						}else{
    				    	$this->db->insert('wallet_transaction',$debitdata);
    				    	$redeem_updated[$record['bill_no']]=1;
						}
    		              
    		        } 
    		        if($record['trans_type'] == 1 && $record['trans_points'] >0){
    		        	$data = array('id_wallet_account'   => $id_wal_ac,
            						  'date_add' 	=> date('Y-m-d H:i:s'),
                					  'date_transaction' 	=> $bill_date,
            						  'transaction_type'	=> ($record['trans_type'] == 1 ? 0 :1),
            						  'value'				=> $record['trans_points'],
            						  'ref_no'              => $record['bill_no'].'-'.$record['category_code'],
            						  'description'			=> 'Credited for bill no. '.$record['bill_no'].' on '.$record['bill_date'],
            						  );
            						  
        			    $status = $this->db->insert('wallet_transaction',$data);
    		        }
        			
        			// Update Customer ID in inter_wallet_account
        			$this->db->where('mobile',$mobile);
        			$this->db->update('inter_wallet_account',array('id_customer' => $id_customer));
		    }
		
		}
		$sql->free_result();
		
		$tmp_redeem_updated_1 = [];
		// To insert data from temp table
		$tmp_table_1 = $this->db->query("select date_format(iwt.entry_date,'%d-%m-%Y') as bill_date,iwd.trans_points,iwt.actual_redeemed,category_code,iwt.bill_no,trans_type 
		from inter_wallet_trans_tmp_2	 iwt
		LEFT JOIN  inter_walTransDetail_tmp_1 iwd on iwd.id_inter_wallet_trans = iwt.id_inter_waltrans_tmp
		where mobile=".$mobile);
    	if($tmp_table_1->num_rows() > 0){
		    foreach($tmp_table_1->result_array() as $record){ 
		    	$b_date = date_create($record['bill_date']);
                $bill_date = date_format($b_date,"Y-m-d H:i:s");
    		        if($record['actual_redeemed'] > 0){
    		            $debitdata = array('id_wallet_account'  => $id_wal_ac,
                						  'date_add' 			=> date('Y-m-d H:i:s'),
                						  'date_transaction' 	=> $bill_date,
                						  'transaction_type'	=> 1, // debit
                						  'value'				=> $record['actual_redeemed'],
                						  'ref_no'              => $record['bill_no'].'-'.$record['category_code'],
                						  'description'			=> 'Debited for bill no '.$record['bill_no'].' on '.$record['bill_date'],
                						  );  
    				    if(sizeof($tmp_redeem_updated_1) > 0){
    		        		$talreadyUpdated = 0;
    		        		foreach($tmp_redeem_updated_1 as $k=>$v){
								if($k == $record['bill_no']){
									$talreadyUpdated = 1;
								}
							}	
							if($talreadyUpdated == 0){
								$this->db->insert('wallet_transaction',$debitdata);
    				    		$tmp_redeem_updated_1[$record['bill_no']]=1;
							}
						}else{
    				    	$this->db->insert('wallet_transaction',$debitdata);
    				    	$tmp_redeem_updated_1[$record['bill_no']]=1;
						}   
    		        } 
    		        if($record['trans_type'] == 1 && $record['trans_points'] >0){
    		        	$data = array('id_wallet_account'   => $id_wal_ac,
            						  'date_add' 			=> date('Y-m-d H:i:s'),
                					  'date_transaction' 	=> $bill_date,
            						  'transaction_type'	=> ($record['trans_type'] == 1 ? 0 :1),
            						  'value'				=> $record['trans_points'],
            						  'ref_no'              => $record['bill_no'].'-'.$record['category_code'],
            						  'description'			=> 'Credited for bill no. '.$record['bill_no'].' on '.$record['bill_date'],
            						  );
            						  
        			    $status = $this->db->insert('wallet_transaction',$data);
    		        }
        			
        			// Update Customer ID in inter_wallet_account
        			$this->db->where('mobile',$mobile);
        			$this->db->update('inter_wallet_account',array('id_customer' => $id_customer));
		    }
		
		}
		$tmp_table_1->free_result();
		
		$tmp_redeem_updated = [];
		// To insert data from temp table
		$tmp_table = $this->db->query("select date_format(iwt.entry_date,'%d-%m-%Y') as bill_date,iwd.trans_points,iwt.actual_redeemed,category_code,iwt.bill_no,trans_type from inter_wallet_trans_tmp	 iwt
		LEFT JOIN  inter_walTransDetail_tmp iwd on iwd.id_inter_wallet_trans = iwt.id_inter_waltrans_tmp
		where mobile=".$mobile);
    	if($tmp_table->num_rows() > 0){
		    foreach($tmp_table->result_array() as $record){ 
		    	$b_date = date_create($record['bill_date']);
                $bill_date = date_format($b_date,"Y-m-d H:i:s");
    		        if($record['actual_redeemed'] > 0){
    		            $debitdata = array('id_wallet_account'  => $id_wal_ac,
                						  'date_add' 			=> date('Y-m-d H:i:s'),
                						  'date_transaction' 	=> $bill_date,
                						  'transaction_type'	=> 1, // debit
                						  'value'				=> $record['actual_redeemed'],
                						  'ref_no'              => $record['bill_no'].'-'.$record['category_code'],
                						  'description'			=> 'Debited for bill no '.$record['bill_no'].' on '.$record['bill_date'],
                						  );  
    				    if(sizeof($tmp_redeem_updated) > 0){
    		        		$talreadyUpdated = 0;
    		        		foreach($tmp_redeem_updated as $k=>$v){
								if($k == $record['bill_no']){
									$talreadyUpdated = 1;
								}
							}	
							if($talreadyUpdated == 0){
								$this->db->insert('wallet_transaction',$debitdata);
    				    		$tmp_redeem_updated[$record['bill_no']]=1;
							}
						}else{
    				    	$this->db->insert('wallet_transaction',$debitdata);
    				    	$tmp_redeem_updated[$record['bill_no']]=1;
						}   
    		        } 
    		        if($record['trans_type'] == 1 && $record['trans_points'] >0){
    		        	$data = array('id_wallet_account'   => $id_wal_ac,
            						  'date_add' 			=> date('Y-m-d H:i:s'),
                					  'date_transaction' 	=> $bill_date,
            						  'transaction_type'	=> ($record['trans_type'] == 1 ? 0 :1),
            						  'value'				=> $record['trans_points'],
            						  'ref_no'              => $record['bill_no'].'-'.$record['category_code'],
            						  'description'			=> 'Credited for bill no. '.$record['bill_no'].' on '.$record['bill_date'],
            						  );
            						  
        			    $status = $this->db->insert('wallet_transaction',$data);
    		        }
        			
        			// Update Customer ID in inter_wallet_account
        			$this->db->where('mobile',$mobile);
        			$this->db->update('inter_wallet_account',array('id_customer' => $id_customer));
		    }
		
		}
		return TRUE;
		
	}
	
	
	
}

?>