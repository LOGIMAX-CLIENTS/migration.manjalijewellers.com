<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Services_model extends CI_Model
{
	
	function __construct()
    {
        parent::__construct();
    }
    
    public function insertData($data,$table)
    {
    	$insert_flag = 0;
		$insert_flag = $this->db->insert($table, $data);
		return ($insert_flag == 1 ? $this->db->insert_id(): 0);
	}
	
	function daily_collection($type="",$date="",$data="",$id_branch="")
	{
	    
	     $company_settings=$this->session->userdata('company_settings');
		 $id_company=$this->session->userdata('id_company');
		 switch($type){
		 	
			case 'get' :
			    if($id_branch == ""){
			        $sql = "select date,b.id_branch,today_collection_amt,today_collection_wgt,today_weight,amtSchClosedAmt,wgtSchClosedAmt,wgtSchClosedWgt,
			        today_cancelled_amt,today_cancelled_wgt,weight_cancelled,closing_balance_amt,closing_balance_wgt,closing_weight,b.name as branch_name,
			        (closing_balance_amt-today_collection_amt) as opening_blc_amt,(closing_balance_wgt-today_collection_wgt) as opening_blc_wgt,(closing_weight-today_weight) as opening_weight
			        from daily_collection dc
			        Left join branch b on dc.id_branch=b.id_branch
			        where date='".$date."'".($id_company!=0 && $company_settings == 1? " and b.id_company=".$id_company."" :'')."";	 
    				$res = $this->db->query($sql);
    				return $res->result_array();	
			    }else{
			        $sql = "select * from daily_collection dc 
			        Left join branch b on dc.id_branch=b.id_branch
			        where date='".$date."' and dc.id_branch=".$id_branch."".($id_company!=0 && $company_settings == 1? " and b.id_company=".$id_company."" :'')."";					
    				$res = $this->db->query($sql);
    				return $res->row_array();	
			    }
						
			break;
		 	
		 	case 'insert' :				
				$status = $this->db->insert('daily_collection',$data);
				//echo $this->db->last_query();exit;
				return $status;			
			break;
		 
		 }
		 	
	}
	
    function getTodaySummaryBranchWise($date,$id_branch){ // Branch and scheme type wise
		// collection
		$collSQL = "select  
		 if((s.scheme_type=0 || (s.scheme_type=3 && (s.flexible_sch_type = 1 || s.flexible_sch_type = 5))),IFNULL(SUM(p.payment_amount),0),0)   as today_collection_amt,
 
         if((s.scheme_type=1 || s.scheme_type=2 || (s.scheme_type=3 && (s.flexible_sch_type = 2 || s.flexible_sch_type = 3 || s.flexible_sch_type = 4))),IFNULL(SUM(p.payment_amount),0),0)   as today_collection_wgt,
         if((s.scheme_type=1 || s.scheme_type=2 || (s.scheme_type=3 && (s.flexible_sch_type = 2 || s.flexible_sch_type = 3 || s.flexible_sch_type = 4))),IFNULL(SUM(p.metal_weight),0),0)   as today_weight		
		
		from payment p
		LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account) 
		LEFT JOIN payment_status ps ON (ps.id_payment=p.id_payment) 
		LEFT JOIN scheme s ON (sa.id_scheme = s.id_scheme) 
		LEFT JOIN branch b ON (b.id_branch = p.id_branch) 
		WHERE p.id_scheme_account IS NOT NULL AND date(p.date_payment) BETWEEN '$date' AND '$date' and p.id_branch=".$id_branch." and (p.payment_status=1 ) group by sa.id_scheme_account and sa.id_branch";
	
		$collection = $this->db->query($collSQL);		
		$res['collection'] = $collection->row_array();
		$collection->free_result();
		
		// Closed
		$closedSQL = "select  
		if((s.scheme_type=0 || (s.scheme_type=3 && (s.flexible_sch_type = 1 || s.flexible_sch_type = 5))),IFNULL(SUM(sa.closing_balance),0),0)   as amtSchClosedAmt,
 
        if((s.scheme_type=1 || s.scheme_type=2 || (s.scheme_type=3 && (s.flexible_sch_type = 2 || s.flexible_sch_type = 3 || s.flexible_sch_type = 4))),IFNULL(SUM(sa.closing_amount),0),0)   as wgtSchClosedAmt,
                 
        if((s.scheme_type=1 || s.scheme_type=2 || (s.scheme_type=3 && (s.flexible_sch_type = 2 || s.flexible_sch_type = 3 || s.flexible_sch_type = 4))),IFNULL(SUM(sa.closing_weight),0),0)   as wgtSchClosedWgt
		from  scheme_account sa  
		LEFT JOIN scheme s ON (sa.id_scheme = s.id_scheme)  
		LEFT JOIN employee e ON (e.id_employee = sa.employee_closed)  
		WHERE sa.is_closed=1 and sa.active=0 AND date(sa.closing_date) BETWEEN '$date' AND '$date' and e.id_branch=".$id_branch."  group by sa.id_scheme_account and sa.id_branch";
		$closed = $this->db->query($closedSQL);
		$res['closed'] = $closed->row_array();
		$closed->free_result(); 
		
		// canceled
		$canceledSQL = "select  
		if((s.scheme_type=0 || (s.scheme_type=3 && (s.flexible_sch_type = 1 || s.flexible_sch_type = 5))),IFNULL(SUM(p.payment_amount),0),0)   as today_cancelled_amt,
 
        if((s.scheme_type=1 || s.scheme_type=2 || (s.scheme_type=3 && (s.flexible_sch_type = 2 || s.flexible_sch_type = 3 || s.flexible_sch_type = 4))),IFNULL(SUM(p.payment_amount),0),0)   as today_cancelled_wgt,
                 
        if((s.scheme_type=1 || s.scheme_type=2 || (s.scheme_type=3 && (s.flexible_sch_type = 2 || s.flexible_sch_type = 3 || s.flexible_sch_type = 4))),IFNULL(SUM(p.metal_weight),0),0)   as weight_cancelled
        
        from  payment p
		LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account) 
		LEFT JOIN scheme s ON (sa.id_scheme = s.id_scheme) 
		WHERE p.id_scheme_account IS NOT NULL AND receipt_no IS NOT NULL AND date(p.date_payment) BETWEEN '$date' AND '$date' and p.id_branch=".$id_branch." and  p.payment_status=4  group by sa.id_scheme_account and sa.id_branch";
		$canceled = $this->db->query($canceledSQL);		
		$res['canceled'] = $canceled->row_array();
		
		$canceled->free_result(); 
		return $res;
	}
	
	function getTodaySummary($date){ // scheme type wise
		$sql = "select cs.currency_symbol,if(s.scheme_type=0,'Amount',if(s.scheme_type=1,'Weight',if(s.scheme_type=3,'FLEXIBLE_AMOUNT','Amount To Weight')))as scheme_type, 
		SUM(CASE WHEN Date_format(p.date_payment,'%Y-%m-%d') ='$date' and p.payment_status=1 and s.scheme_type=0 THEN p.payment_amount END) as today_collection_amt, 

		SUM(CASE WHEN Date_format(p.date_payment,'%Y-%m-%d') ='$date' and p.payment_status=1 and (s.scheme_type=2 or s.scheme_type=1) THEN p.payment_amount END) as today_collection_wgt, 

		SUM(CASE WHEN Date_format(p.date_payment,'%Y-%m-%d') ='$date' and p.payment_status=1 and (s.scheme_type=2 or s.scheme_type=1) THEN p.metal_weight END) as today_weight, 

		IFNULL(SUM(CASE WHEN Date_format(sa.closing_date,'%Y-%m-%d') ='$date' and sa.is_closed=1 and s.scheme_type=0 THEN sa.closing_balance END),0) as amtSchClosedAmt,

		IFNULL(SUM(CASE WHEN Date_format(sa.closing_date,'%Y-%m-%d') ='$date' and sa.is_closed=1 and (s.scheme_type=2 or s.scheme_type=1) THEN sa.closing_balance END),0) as wgtSchClosedAmt,

		IFNULL(SUM(CASE WHEN Date_format(sa.closing_date,'%Y-%m-%d') ='$date' and sa.is_closed=1 and (s.scheme_type=2 or s.scheme_type=1) THEN sa.closing_weight END),0) as wgtSchClosedWgt,

		IFNULL(SUM(CASE WHEN ps.date_upd ='$date' and ps.id_status_msg=4 and s.scheme_type=0 THEN p.payment_amount END),0) as today_cancelled_amt,

		IFNULL(SUM(CASE WHEN ps.date_upd ='$date' and ps.id_status_msg=4 and (s.scheme_type=2 or s.scheme_type=1) THEN p.payment_amount END),0) as today_cancelled_wgt,

		IFNULL(SUM(CASE WHEN ps.date_upd ='$date' and ps.id_status_msg=4 and  (s.scheme_type=2 or s.scheme_type=1) THEN p.metal_weight END),0) as weight_cancelled

		from payment p
		LEFT JOIN scheme_account sa ON (sa.id_scheme_account = p.id_scheme_account) 
		LEFT JOIN payment_status ps ON (ps.id_payment=p.id_payment) 
		LEFT JOIN scheme s ON (sa.id_scheme = s.id_scheme)  
		join chit_settings cs
		WHERE p.id_scheme_account IS NOT NULL AND Date_format(p.date_payment,'%Y-%m-%d')='$date' and (p.payment_status=1 ) group by s.scheme_type";
		$res = $this->db->query($sql);
		return $res->result_array();
	}
	
	function allBranches(){
	    
	    $company_settings=$this->session->userdata('company_settings');
		$id_company=$this->session->userdata('id_company');
			
	    $sql="Select b.id_branch, b.name as branch_name from branch b where b.active= 1 ".($id_company!=0 && $company_settings == 1? " and b.id_company=".$id_company."" :'')."";
		$r=$this->db->query($sql);
		return $r->result_array();
	}
	
	// Start tempToMain
	function getIwalTrans_temp($id_br,$bill_dt){
		$result = array();
	    $sql="Select * from inter_wallet_trans_tmp where id_branch=".$id_br." and date(entry_date)='".$bill_dt."' limit 400";
		$r = $this->db->query($sql);
		if($r->num_rows() > 0){
			$result = $r->result_array();
		}
		return $result;
	}
	
	function getIwalTrans_main($tran){
		$result = array();
	    $sql="Select * from inter_wallet_trans where id_branch=".$tran['id_branch']." and bill_no='".$tran['bill_no']."' limit 1";
		$r = $this->db->query($sql);
		if($r->num_rows() == 1){
			$result = array('action'=>'Update','trans'=>$r->row_array());
		}else{
			$result = array('action'=>'Add');
		}
		return $result;
	}
	
	function getIwalTranDetail_tmp($id){
		$result = array();
	    $sql="Select * from inter_walTransDetail_tmp where id_inter_wallet_trans=".$id;
		$r = $this->db->query($sql);
		if($r->num_rows() > 0){
			$result = $r->result_array();
		}
		return $result;
	}
	
	function getIwalTranDetail_main($id){
		$result = array();
	    $sql="Select * from inter_wallet_trans_detail where id_inter_wallet_trans=".$id;
		$r = $this->db->query($sql); 
		if($r->num_rows() > 0){
			$result = array('action'=>'Update','trans'=>$r->result_array());
		}else{
			$result = array('action'=>'Add');
		}
		return $result;
	}
	
	function insertTransinMain($data){
		$result['status'] = FALSE;
		$insData = array('record_type'	=> $data['record_type'],
						'mobile'		=>$data['mobile'],
						'id_branch'		=>$data['id_branch'],
						'trans_type'	=>$data['trans_type'],
						'bill_no'		=>$data['bill_no'],
						'entry_date'	=>$data['entry_date'],
						'last_update'	=>$data['last_update'],
						'date_add'		=>$data['date_add'],
						'record_to'		=>$data['record_to'],
						'is_modified'	=>$data['is_modified'],
						'is_transferred'=>$data['is_transferred'],
						'transfer_date'	=>$data['transfer_date'],
						'use_points'	=>$data['use_points'],
						'redeem_req_pts'=>$data['redeem_req_pts'],
						'actual_redeemed'=>$data['actual_redeemed'],
						'bill_availWalPt'=>$data['bill_availWalPt']
				);
		$status = $this->db->insert('inter_wallet_trans',$insData);
		if($status){
			$result = array('status'=>TRUE,'id_inter_wallet_trans'=>$this->db->insert_id());
		}
		return $result;
	}
	
	function insertTransDetailInMain($data,$id_inter_walTran){
		$insData = array( 		
						'id_inter_wallet_trans' => $id_inter_walTran,
						'category_code'		=> $data['category_code'],
						'amount'			=> $data['amount'],
						'trans_points'		=> $data['trans_points'],
						'id_wcat_settings'	=> $data['id_wcat_settings'],
						'allowed_redeem '	=> $data['allowed_redeem'],
						'remark'			=> $data['remark'],
						'date_add'			=> $data['date_add'],
						'last_update'		=> $data['last_update']
					);
		$status = $this->db->insert('inter_wallet_trans_detail',$insData);
		return $status;
	}
	
	function delTransAndDetail($id_inter_walTran){  
		$status = FALSE;
        $this->db->where('id_inter_wallet_trans', $id_inter_walTran);
        $child= $this->db->delete('inter_walTransDetail_tmp');  
        if($child)
        {
		  $this->db->where('id_inter_waltrans_tmp', $id_inter_walTran);
          $status= $this->db->delete('inter_wallet_trans_tmp');
		}

		return $status;
	}
	
	function updTransin_main($data,$id){
		$this->db->where('id_inter_wallet_trans',$id); 
		$status = $this->db->update('inter_wallet_trans',$data);
		return $status;
	}
	
	function updateTransDetail_main($data,$id)
	{
		$this->db->where('id_inter_waltransdetail',$id); 
		 $status = $this->db->update('inter_wallet_trans_detail',$data);
		return $status;
	}
	
	// End tempToMain
	
	// start Reset Wallet Transaction	
	function getWalletAccounts($id_wal_ac){
		$result = array();
		if($id_wal_ac == NULL){
			$sql="Select id_wallet_account,wa.id_customer,c.mobile
	    from wallet_account wa 
	    LEFT JOIN customer c on wa.id_customer=c.id_customer
	    where c.id_customer is not null order by id_wallet_account asc limit 200";
		}else{
			$sql="Select id_wallet_account,wa.id_customer,c.mobile
	    from wallet_account wa 
	    LEFT JOIN customer c on wa.id_customer=c.id_customer
	    where c.id_customer is not null and id_wallet_account>".$id_wal_ac." order by id_wallet_account asc limit 200";
		}
	    
		$r = $this->db->query($sql);
		if($r->num_rows() > 0){
			$result = $r->result_array();
		}
		return $result;
	}
	
	function getTempData(){
	    $result = array(); 
		$sql="Select distinct(entry_date),count(*) as records,id_branch from inter_wallet_trans_tmp group by  id_branch,date(entry_date)";
	 
		$r = $this->db->query($sql);
		if($r->num_rows() > 0){
			$result = $r->result_array();
		}
		return $result;
	}
	
	/*function insChitwallet($id_wal_ac,$mobile,$id_customer)
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
		
	}*/
	
	// End Reset Wallet Transaction	
	
	
	// Maturity Date update functions
	
	function isPaid($paidByMonth,$date){
    	foreach($paidByMonth as $p){
    		if($date == $p['date_payment']){
    			return true;
    		}
    	}
    	return false;
    }
    
	function updMaturityDate(){
	    // Update Maturity Date in scheme_account table if maturity date is flexible
		if($record->is_fixed_maturity == 0){ 
			if(sizeof($paidByMonth) == 0){
			    $paid_sql = $this->db->query("SELECT date(date_payment) as date_payment,sum(metal_weight) as paid_wgt,sum(payment_amount) as paid_amt FROM `payment` WHERE ( payment_status=1 or payment_status=2 ) and id_scheme_account=".$record->id_scheme_account." GROUP BY YEAR(date_payment), MONTH(date_payment)");
			    $paidByMonth = $paid_sql->result_array();
			    $skipped_months = 0;
                 
                for($i = 0; $i >= 0 ;$i++){
                	$date = date('Y-m-d', strtotime("+".$i." months", strtotime($record->start_date)));
                	$Ym = date('Y-m', strtotime("+".$i." months", strtotime($record->start_date)));
                	if($Ym != date("Y-m")){
                		$isPaid = $this->isPaid($paidByMonth,$date);
                		$skipped_months = $skipped_months + ($isPaid ? 0 : 1);
                		//echo $Ym."--".date("Y-m")."--".$skipped_months."<br/>";
                	}
                	else if($Ym == date("Y-m")){ // Quit Loop
                		$i = -2;
                	}
                }
                
                $maturity = date('Y-m-d', strtotime("+".($record->total_installments+$skipped_months)." months", strtotime($record->start_date)));
                echo $maturity;exit;
			    $updData = array( "maturity_date" => $maturity, "date_upd" => date("Y-m-d") );
				$this->db->where('id_scheme_account',$record->id_scheme_account); 
 				$this->db->update("scheme_account",$updData);
			}
		}
	    
	}
		
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
     
     public function get_SMS_data($service_id, $id)
     {
		//Declaration of variables
		$params = [];
        $template_name = "";
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
					  cmp.phone as cmp_ph
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
					  cmp.phone as cmp_ph
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
					  cmp.phone as cmp_ph
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
					  cmp.company_name as cmp_name, cmp.phone as cmp_ph,cmp.comp_name_in_sms as cmp_name_sms,
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
					    c.firstname as fname,c.lastname as lname,c.email,cmp.company_name as cmp_name,IFNULL(c.mobile,'') as ref_code, 
					    c.mobile from customer c
					     join company cmp
					     where c.mobile='".$id."'");
			 	} 
			 	else if($service_id == 16){
						  $resultset=$this->db->query("SELECT if(sa.is_refferal_by=0 && chit.cusplan_type=1,if(ws.type=0,ws.value,((s.amount*ws.value)/100)),
				            if(sa.is_refferal_by=0 && s.cus_refferal=1 && chit.cusplan_type=0,s.cus_refferal_value,if(sa.is_refferal_by=1 && chit.empplan_type=1,if(ws.type=0,ws.value,((s.amount*ws.value)/100)),
				              if(sa.is_refferal_by=1 && s.emp_refferal=1 && chit.empplan_type=0,s.emp_refferal_value,'')))) as amount,
							ref.mobile,ref.firstname as fname,comp.company_name as cmp_name					
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
								  cmp.phone as cmp_ph
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

    function deleteNoPayAcc($from_date,$to_date){
		$deleted = 0;
		$deletedIds = [];
		$acc_sql = $this->db->query("select * from scheme_account where scheme_acc_number is null and active=1 and is_registered=0 and date(date_add) between '".$from_date."' and '".$to_date."'");
		echo $this->db->last_query()." <br/>";
		    echo $acc_sql->num_rows();
		if($acc_sql->num_rows() > 0){
			foreach($acc_sql->result_array() as $acc){
				$pay_sql = $this->db->query("select * from payment where id_scheme_account=".$acc['id_scheme_account']." order by timestamp(date_payment) desc");
				if($pay_sql->num_rows() == 0){ // Has no payment records
				    $this->insertData($acc,"deleted_scheme_account");
					$this->db->where('id_scheme_account', $acc['id_scheme_account']);
          			$status = $this->db->delete('scheme_account');
          			if($status){
          			    $deleted++;
          			    array_push($deletedIds,$acc['id_scheme_account']);
          			}
          			 echo "<pre>".$acc['id_scheme_account']." Has no payment records <br/>";
				}else{ 
				    $payments = $pay_sql->result_array();
				    $hasValidPay = false;
				    $i = 0;
				    foreach($payments as $pay){
				        // check for any valid payments
				        if($pay['payment_status'] <=2 || $pay['payment_status'] == 6 || $pay['payment_status'] == 8 ){ // Success, refund,default
				            $hasValidPay = true;
				            break;
				        }else {
				            if($i == 0){ // Check whether last payment is 1 month older
				                $lastmonth = strtotime("-30 days");
				                if( strtotime($pay['date_payment']) > $lastmonth ){
				                    $hasValidPay = true;
									break;
				                }
				            }
				        }
				    }
				    if($hasValidPay == false){ // No valid payments, delete payment and account
				        $this->db->trans_begin();
						foreach($payments as $pay){
				            $this->insertData($pay,"deleted_payment");
				        }
				        $this->db->where('id_scheme_account', $acc['id_scheme_account']);
						$del_acc = $this->db->delete('payment');
						if($del_acc){
						    $this->insertData($acc,"deleted_scheme_account");
							$this->db->where('id_scheme_account', $acc['id_scheme_account']);
							$del_pay = $this->db->delete('scheme_account');
							if($this->db->trans_status() === TRUE){
								$this->db->trans_commit();
								$deleted++;
								array_push($deletedIds,$acc['id_scheme_account']);
							}else{
								$this->db->trans_rollback();
							}
						}
						echo "<pre>".$acc['id_scheme_account']." deleted ";var_dump($hasValidPay)." <br/>";
				    }
				}
			}
		}else{
		    echo "No records to delete";
		}
		return array("deleted_acc" => $deleted, "deletedIds" => $deletedIds);
	}
	
	function deleteInvalidPay($from_date,$to_date){
		$deleted = 0;
		$deletedIds = [];
		$pay_sql = $this->db->query("select * from payment where (payment_status = 3 OR payment_status = 4 OR payment_status = 7) and is_offline = 0 and date(date_add) between '".$from_date."' and '".$to_date."'");
	    $payments = $pay_sql->result_array();
	    $i = 0;
	    foreach($payments as $pay){
	        $this->db->trans_begin();
            $this->insertData($pay,"deleted_payment");
            $this->db->where('id_payment', $pay['id_payment']);
			$del_acc = $this->db->delete('payment');
			if($this->db->trans_status() === TRUE){
				$this->db->trans_commit();
				$deleted++;
				array_push($deletedIds,$pay['id_payment']);
			}else{
				$this->db->trans_rollback();
			}
        }
		echo $this->db->_error_message();
		return array("deleted_acc" => $deleted, "deletedIds" => $deletedIds, "error" => $this->db->_error_message());
	}
	
	function checkServiceCode($serv_code)   //AB
	{
		$email = 0;
		$sms   = 0;
		$query = $this->db->get_where('services',array('serv_code' => $serv_code));
		if($query->num_rows() > 0)
		{
			$row = $query->row();
			$email = $row->serv_email;
			$sms   = $row->serv_sms;
			$whatsapp   = $row->serv_whatsapp;
		}
		$data = array("email" => $email, "sms" => $sms, "whatsapp" => $whatsapp);
		
		return $data;
	}
	
	

	
}
?>