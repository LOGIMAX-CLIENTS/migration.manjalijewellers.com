<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Adminappapi_model extends CI_Model
{ 
    const EMP_IMG_PATH = 'admin/assets/img/employee';
    const CUS_IMG_PATH = 'admin/assets/img/customer';
	function __construct()
    {      
        parent::__construct(); 
        
        $this->load->model("payment_modal");
		
	} 
	
	 function get_values()
    {
		return (array)json_decode(file_get_contents('php://input'));
		
	}
	
	//validate user login
	function isValidLogin($data)
	{
	   
	    $username = $data['username'];
	    $passwd = $this->__encrypt($data['passwd']);
	    $uuid = $data['uuid'];
	    $device_type = $data['device_type'];
		$user_agent =  $_SERVER['HTTP_USER_AGENT'];

		$record = array();
		$sql="Select e.enable_chit_collection,e.id_employee, e.active,'EMP' as login_type,if(e.emp_code is not null AND e.emp_code != '',e.emp_code,NULL) as emp_code,b.name as branch_name,id_profile,e.id_employee,e.firstname,e.lastname,e.mobile,e.email,ifnull(e.login_branches,'0') as id_branch,e.image, e.is_lmx, e.pwd_hash
		From employee e 
		Left join branch b on b.id_branch=e.login_branches
		where e.active = 1 and e.username = '".$username."' LIMIT 1";
		//where  BINARY e.username='".$username."' and  BINARY e.passwd='".$passwd."'";
		  //print_r($sql);exit;
		$result = $this->db->query($sql);
		if($result->num_rows() > 0 && (password_verify($data['passwd'], $result->row()->pwd_hash)))
		{
		    $row = $result->row_array(); 
		    
		  //device login restriction....starts 
		    if($uuid == '1234567890' || $row['is_lmx'] == 1){ //for allowing browser ios apk and LMX login...
    		    $device_uuid_status = TRUE;   
    		    $enable_chit = TRUE;
		    }else{

		       /*  if($uuid != null && $uuid!='' && ($row['device_uuid'] == null || $row['device_uuid'] == '')){
    		        $this->db->where('id_employee',$row['id_employee']);
            		$this->db->update('employee',array('device_uuid' => $uuid));
            		
            		if($this->db->trans_status() && $row['enable_chit_collection'] == 1){
            		   $device_uuid_status = TRUE; 
            		}else{
            		   $device_uuid_status = FALSE; 
            		}
            		
    		    }else if($row['device_uuid'] != null || $row['device_uuid'] != ''){
    		        if($row['device_uuid'] == $uuid  && $row['enable_chit_collection'] == 1){
    		            $device_uuid_status = TRUE;
    		        }else{
    		            $device_uuid_status = FALSE;
    		        }
    		    }   */
    		    
				$qdev = $this->db->query("SELECT device_status FROM employee_devices WHERE emp_id = '".$row['id_employee']."' AND device_uuid = '".$uuid."' AND app_type = 1");

				if($qdev->num_rows() > 0 ) {

					$row_dev = $qdev->row_array();

					if($row_dev['device_status'] == 1) {
						$device_uuid_status = TRUE; 
    		        	$enable_chit = TRUE;
					} else {
						$device_uuid_status = TRUE; 
    		         	$enable_chit = FALSE;
					}

				} else {
					$device_uuid_status = FALSE; 
					$enable_chit = FALSE;
					// Inserting to employee devices if no device ID exists
					$dev_arr = array(
						'device_uuid' => $uuid,
						'device_type' => $device_type,
						'date_add'    => date('Y-m-d H:i:s'),
						'emp_id'	  => $row['id_employee'],
						'app_type'	  => 1,
						'device_info' => $user_agent,
						'device_status' => 0
					  );

					$this->insert_collectionDevices($dev_arr);
				}
    		    
    		    /*if($row['enable_chit_collection'] == 0  && $row['device_uuid'] == $uuid){
    		         $device_uuid_status = TRUE; 
    		         $enable_chit = FALSE;
    		    }else if($row['enable_chit_collection'] == 1 && $row['device_uuid'] == $uuid){
    		        $device_uuid_status = TRUE; 
    		        $enable_chit = TRUE;
    		    }else if($row['enable_chit_collection'] == 1 && $row['device_uuid'] != $uuid){
    		        $device_uuid_status = FALSE; 
    		        $enable_chit = TRUE;
    		    }else{
    		        $device_uuid_status = FALSE; 
    		        $enable_chit = FALSE;
    		    }*/
    		    
    		    
		    }
		    //device ends
			
			$file = self::EMP_IMG_PATH.'/'.$row['id_employee'].'/employee.jpg';
			$img_path = ($row['image'] != null ? (file_exists($file)? $file : null ):null);
			
			$record = array('enable_chit'=> $enable_chit,'device_uuid_status' => $device_uuid_status,'active' => $row['active'],'id_employee' => $row['id_employee'],'branch_name' => $row['branch_name'], 'email' => $row['email'],'id_profile' => $row['id_profile'],'id_branch' => $row['id_branch'], 'lastname' =>ucfirst( $row['lastname']),'firstname' => ucfirst($row['firstname']), 'image' => $img_path,'login_type' => 'EMP','emp_ref_code' => $row['emp_code']);
		    return $record;	
		}else{
		    $agent_sql= $this->db->query("Select b.name as branch_name,a.agent_code,a.id_agent as id_employee,a.firstname,a.lastname,a.mobile,a.email,a.id_branch as id_branch,a.image ,a.active
		    From agent a Left join branch b on b.id_branch=a.id_branch where a.mobile='".$username."' and a.passwd='".$passwd."'");
		    //echo $this->db->last_query();exit;
		    if($agent_sql->num_rows() >0)
		    {
    		    $row = $agent_sql->row_array(); 
    			$file = '';
    			$img_path = ($row['image'] != null ? (file_exists($file)? $file : null ):null);
    			$record = array('id_employee' => $row['id_employee'],'branch_name' => $row['branch_name'], 'email' => $row['email'],'id_profile' => $row['id_profile'],'active' => $row['active'],'id_branch' => $row['id_branch'], 'lastname' =>ucfirst( $row['lastname']),'firstname' => ucfirst($row['firstname']), 'image' => $img_path,'login_type' => 'AGENT', 'agent_code' => $row['agent_code']);
    		    return $record;	
		    }
			else {
                return array('status'=>2,'msg'=>'Invalid password');
		    }
		}
	    		
	}
	
	function send_sms($mobile,$message)
	{		
		$url = $this->sms_data['sms_url'];
		$senderid  = $this->sms_data['sms_sender_id'];
		
	if($this->sms_chk['debit_sms']!=0){
		
		$arr = array("@customer_mobile@" => $mobile,"@message@" => str_replace(array("\n","\r"), '', $message),"@senderid@" => $senderid);
	
		$user_sms_url = strtr($url,$arr);
    	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $user_sms_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
		$result = curl_exec($ch);
		curl_close($ch);
		unset($ch);
		$status=$this->update_otp();
		if($status==1){
		  return TRUE;
		}
		return FALSE;
	}else{
		return FALSE;
		
	}
 }
  function update_otp()
  {
		$query_validate=$this->db->query('UPDATE sms_api_settings SET debit_sms = debit_sms - 1 
				WHERE id_sms_api =1 and debit_sms > 0');  			
	         if($query_validate>0)
			{
				return true;
			}else{
				
				return false;
			}
  }
  
	function company_details()
	{
		$sql = " Select  cs.maintenance_text,cs.maintenance_mode,cs.mob_code as call_prefix,c.id_company,c.whatsapp_no,c.company_name,c.short_code,c.pincode,c.mobile,c.phone,c.email,c.website,c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,cy.name as country,cs.currency_symbol,cs.currency_name,c.phone1,c.mobile1,cs.allow_notification
				from company c
					join chit_settings cs
					left join country cy on (c.id_country=cy.id_country)
					left join state s on (c.id_state=s.id_state)
					left join city ct on (c.id_city=ct.id_city) ";
		$result = $this->db->query($sql);	
		return $result->row_array();
	}
	
	function getallAcitiveschemes(){
	    
	    if($this->session->userdata('branch_settings')==1){
			$id_branch  = $this->input->post('id_branch');
		}else{
		    $id_branch =NULL;
		}
			
	    $sql = "Select  s.scheme_name,s.code,s.id_scheme 
	            from scheme s
				join chit_settings cs
				".($id_branch!=NULL?' left join scheme_branch sb on (sb.id_scheme = s.id_scheme) ':'')."
				where s.active = 1 and s.visible = 1 and is_digi != 1
				".($id_branch!=NULL?' and sb.id_branch in ('.$id_branch.')':'')."
				
        ";
		$result = $this->db->query($sql);	
		return $result->result_array();
	}
	
	
	
	function get_chit_settings()
	{
		$sql="select * from chit_settings";
		$result = $this->db->query($sql);	
		return $result->row_array();
	}
	
	/** Customer functions  **/
    function insert_customer($data)
    {
		  $status = $this->db->insert("customer",$data['info']);        
		  $insertID = $this->db->insert_id();
			if($insertID){
					$data['address']['id_customer']=$insertID;
					$res=$this->db->insert("address",$data['address']);
					if($res){						
						$id_address=$this->db->insert_id();
						$address = array('id_address' => $id_address);
						$this->db->where('id_customer',$insertID); 
						$this->db->update("customer",$address);
						$status = array("status" => true, "insertID" => $insertID);
					}else{
				 	   $status = array("status" => false, "insertID" => '');
				    }				
				}
				else{
					$status = array("status" => false, "insertID" => '');
				  }
		return $status;
	}
	function wallet_accno_generator() 
	{
		$resultset = $this->db->query("SELECT c.wallet_account_type FROM chit_settings c");	
	     if($resultset->num_rows() == 1){
		  return array('wallet_account_type'=>$resultset->row()->wallet_account_type);
	    }	
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
	
	function get_metalrate($id_branch,$is_branchwise_rate)
	{
	    
	    if($is_branchwise_rate==1 && $id_branch!='' && $id_branch!=NULL )
	    {
	        $sql="select * from metal_rates m
	   		left join branch_rate br on m.id_metalrates=br.id_metalrate 
	   		where br.id_branch=".$id_branch." order by  br.id_metalrate desc limit 1";
	   	
	    }
	    else
	    {
	        $sql="select * from metal_rates order by id_metalrates Desc LIMIT 1";
	    }
	    	$result = $this->db->query($sql);	
		    return $result->row_array();
	}
	
	function get_payment_details($id_customer,$id_branch,$id_sch_acc="")
	{  		
	
		$showGCodeInAcNo = $this->config->item('showGCodeInAcNo'); 
		$filename = base_url().'api/rate.txt'; 
	    $data = file_get_contents($filename);
		$result['metal_rates'] = (array) json_decode($data);
	    $schemeAcc = array();
		$sql="Select 
		 s.maturity_type,s.avg_calc_by,
		
		date_format(date_add(date(sa.start_date),interval + (s.total_installments-1) month),'%Y%m') as daily_sch_allow_pay_till,
		
		
		IFNULL((SELECT count(p.id_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1 AND date(p.date_payment) = curdate()),0) as curday_total_paid_count,
		IFNULL((SELECT count(p.id_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1 AND p.due_type = 'AD'),0) as total_adv_paid,
		IFNULL((SELECT count(p.id_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1 AND p.due_type = 'PD'),0) as total_pend_paid,

        s.pay_duration,s.installment_cycle,s.grace_days,
		
		
		s.maturity_type,s.installment_cycle,s.grace_days,IFNULL(sa.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sa.id_branch) as acc_branch,s.code,
		                s.is_lucky_draw,ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,cs.scheme_wise_acc_no,
		                IFNULL(p.receipt_year,'') as receipt_year, (select b.short_name from branch b where b.id_branch = p.id_branch) as payment_branch, s.code,ifnull(p.receipt_no,'') as receipt_no,cs.scheme_wise_receipt,sa.id_scheme_account,
		                
		                			IFNULL((SELECT count(p.id_payment) FROM payment p WHERE p.id_scheme_account = sa.id_scheme_account AND p.payment_status = 1 AND date(p.date_payment) = curdate()),0) as curday_total_paid_count,

		
		        s.disable_pay,s.disable_pay_amt,s.set_as_min_from,s.set_as_max_from,s.no_of_dues as dues_count,s.is_enquiry,
                date_format(CURRENT_DATE(),'%m') as cur_month,if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments, 
                (s.total_installments - COUNT(payment_amount)), 
                ifnull((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) - SUM(p.no_of_dues),if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,s.total_installments,(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))))) 
                as missed_ins,sa.avg_payable,s.avg_calc_ins,p.payment_status,
                PERIOD_DIFF(Date_Format(CURRENT_DATE(),'%Y%m'),Date_Format(sa.start_date,'%Y%m')) as current_pay_ins, 
		    	PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')) as paid_ins,PERIOD_DIFF(Date_Format(sa.maturity_date,'%Y%m'),Date_Format(curdate(),'%Y%m')) as tot_ins,sa.maturity_date as maturity_date,
			    sg.group_code as scheme_group_code, UNIX_TIMESTAMP(Date_Format(sg.start_date,'%Y-%m-%d')) as group_start_date,  UNIX_TIMESTAMP(Date_Format(sg.end_date,'%Y-%m-%d')) as  group_end_date,  cs.has_lucky_draw,cs.get_amt_in_schjoin,otp_price_fixing,fixed_rate_on,
                s.allowSecondPay,s.free_payment,cs.firstPayamt_payable,ifnull(sa.firstPayment_amt,'0') as firstPayment_amt,sa.is_registered,CONCAT(if(".$showGCodeInAcNo."=1,if(has_lucky_draw = 1,sg.group_code,s.code),'') ,' ',ifnull(sa.scheme_acc_number,'Not Allocated')) as chit_number,
			    s.gst_type,s.gst,sa.id_scheme_account,
			    IF(s.discount=1,s.firstPayDisc_value,0.00) as discount_val,s.firstPayDisc_by,s.firstPayDisc,sa.is_new,
			    s.id_scheme,br.id_branch, br.short_name, br.name as branch_name, 
			    c.id_customer,s.min_amount,s.max_amount,s.pay_duration,s.discount_type,s.discount_installment,s.discount,sa.id_branch as sch_join_branch,cs.is_branchwise_rate,
			    IFNULL(sa.account_name,if(c.lastname is null,c.firstname,concat(c.firstname,' ',c.lastname))) as account_name,
			    c.mobile,
			    s.scheme_type,s.maturity_days,sa.maturity_date,s.firstPayamt_as_payamt,s.flexible_sch_type,s.one_time_premium,
			    s.fix_weight,sa.fixed_metal_rate,sa.fixed_wgt,
			    s.code,
			    IFNULL(s.min_chance,0) as min_chance,
			    IFNULL(s.max_chance,0) as max_chance,
			    Format(IFNULL(s.max_weight,0),3) as max_weight, IF(s.max_weight=s.min_weight,'1','0') as wgt_type,
			    Format(IFNULL(s.min_weight,0),3) as min_weight,s.wgt_convert,
			    Date_Format(sa.start_date,'%d-%m-%Y') as start_date,s.flx_denomintion,
			    Date_Format(sa.maturity_date,'%d-%m-%Y') as maturity_date,
			    IF(s.scheme_type=0 OR s.scheme_type=2,TRIM(s.amount),IF(s.scheme_type=1 ,s.max_weight,if(s.scheme_type=3,if(flexible_sch_type = 3 ,  s.max_weight,if(s.firstPayamt_as_payamt=1,sa.firstPayment_amt ,TRIM(s.min_amount))),0))) as payable,
				round(IF(s.scheme_type=0 OR s.scheme_type=2,s.amount,IF(s.scheme_type=1 ,s.max_weight,
				if(s.scheme_type=3 && s.max_amount!='',s.max_amount,0)))) as max_amount,
				 (SELECT m.goldrate_22ct FROM metal_rates m  order by id_metalrates Desc LIMIT 1)as metal_rate,
				s.total_installments,IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),1,0) as  previous_paid,
 				IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or (s.scheme_type=3 and s.payment_chances=1) , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0)as paid_installments,
 cs.branch_settings,
IFNULL(IF(sa.is_opening=1,IFNULL(balance_amount,0)+IFNULL(SUM(p.payment_amount * p.no_of_dues),0),IFNULL(SUM(p.payment_amount * p.no_of_dues),0)) ,0)
  as total_paid_amount,
FORMAT(sum(if(p.gst > 0,if((p.gst_type = 1),0,p.payment_amount-(p.payment_amount*(100/(100+p.gst)))),0)),0) as paid_gst,
IFNULL(IF(sa.is_opening=1,IFNULL(balance_weight,0)+IFNULL(SUM(p.metal_weight),0),IFNULL(SUM(p.metal_weight),0)),0.000)
 as total_paid_weight,
  if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,   (s.total_installments - if(sa.is_opening = 1,(count(DISTINCT((Date_Format(p.date_payment,'%Y%m'))))+sa.paid_installments),count(DISTINCT((Date_Format(p.date_payment,'%Y%m')))))),ifnull(((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))+1) - IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight , COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))),if((PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m'))) > s.total_installments,s.total_installments,(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'), Date_Format(sa.start_date,'%Y%m')))))) as totalunpaid,   
   IFNULL(if(Date_Format(max(p.date_add),'%Y%m') = Date_Format(curdate(),'%Y%m'), (select SUM(ip.no_of_dues) from payment ip where Date_Format(ip.date_payment,'%Y%m') = Date_Format(curdate(),'%Y%m') and sa.id_scheme_account = ip.id_scheme_account),IF(sa.is_opening=1, if(Date_Format(sa.last_paid_date,'%Y%m') = Date_Format(curdate(),'%Y%m'), 1,0),0)),0) as currentmonthpaycount,  
  (select SUM(pay.no_of_dues) from payment pay where pay.id_scheme_account= sa.id_scheme_account and pay.due_type='AD' and (pay.payment_status=1 or pay.payment_status=2)) as currentmonth_adv_paycount,
  (select SUM(pay.no_of_dues) from payment pay where pay.id_scheme_account= sa.id_scheme_account and pay.due_type='PD' and (pay.payment_status=1 or pay.payment_status=2)) as currentmonth_pend_paycount,
IF(s.scheme_type =1 and s.max_weight!=s.min_weight,true,false) as is_flexible_wgt,  
			    round(IFNULL(cp.total_amount,0)) as  current_total_amount,
			    Format(IFNULL(cp.total_weight,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_weight),0) ,3) as  current_total_weight,
			    IFNULL(cp.paid_installment,0)       as  current_paid_installments,
			   			    IFNULL(cp.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0) as  current_chances_used,
							if(s.scheme_type=3 && s.pay_duration=0 ,IFNULL(sp.chance,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0),IFNULL(cp.chances,0) + IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),(sa.last_paid_chances),0)) as  current_chances_pay,
			    s.is_pan_required,
			    IFNULL(Date_Format(max(p.date_payment),'%d-%m-%Y'),IFNULL(IF(sa.is_opening=1,Date_Format(sa.last_paid_date,'%d-%m-%Y'),'')  ,0))                 as last_paid_date,
					IFNULL(PERIOD_DIFF(Date_Format(curdate(),'%Y%m'),Date_Format(max(p.date_add),'%Y%m')),IF(sa.is_opening=1,PERIOD_DIFF(Date_Format(curdate(),'%Y%m'),Date_Format(sa.last_paid_date,'%Y%m')),0)) as last_paid_duration,
			    month(max(p.date_payment)) as last_paid_month,
				IF(sa.is_opening = 1 and s.scheme_type = 0 || s.scheme_type = 2,
				IF(Date_Format(Current_Date(),'%Y%m')=Date_Format(sa.last_paid_date,'%Y%m'),false,true),
				true) AS previous_amount_eligible,
				count(pp.id_scheme_account) as cur_month_pdc,
				s.allow_unpaid,
				if(s.allow_unpaid=1,s.unpaid_months,0) as allow_unpaid_months,
				s.allow_advance,
				if(s.allow_advance=1,s.advance_months,0) as advance_months,
				if(s.allow_preclose=1,preclose_months,0) as allow_preclose_months,s.payment_chances,
				sa.disable_payment,s.charge,s.charge_type,s.charge_head,
				cs.currency_name,
				cs.currency_symbol, s.id_metal, s.firstPayamt_maxpayable,s.get_amt_in_schjoin,s.max_weight,s.min_weight,s.id_purity
			From scheme_account sa
			Left Join scheme s On (sa.id_scheme=s.id_scheme)
			Left Join branch br  On (br.id_branch=sa.id_branch)
			Left Join scheme_group sg On (sa.group_code = sg.group_code )
			Left Join payment p On (sa.id_scheme_account=p.id_scheme_account and (p.payment_status=1 or p.payment_status=2 or p.payment_status=8))
			Left Join customer c On (sa.id_customer=c.id_customer and c.active=1)
			Left Join
			(	Select
				  sa.id_scheme_account,
				  COUNT(Date_Format(p.date_add,'%Y%m')) as paid_installment,
				  COUNT(Date_Format(p.date_add,'%Y%m')) as chances,
				  SUM(p.payment_amount) as total_amount,
				  SUM(p.metal_weight) as total_weight
				From payment p
				Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)
				Where (p.payment_status=1 or p.payment_status=2 ) and  Date_Format(Current_Date(),'%Y%m')=Date_Format(p.date_payment,'%Y%m')
				Group By sa.id_scheme_account
			) cp On (sa.id_scheme_account=cp.id_scheme_account)	
			left join(Select sa.id_scheme_account, COUNT(Distinct Date_Format(p.date_add,'%d%m')) as paid_installment,
					COUNT(Date_Format(p.date_add,'%d%m')) as chance,
					SUM(p.payment_amount) as total_amount,
					SUM(p.metal_weight) as total_weight
					From payment p
					Left Join scheme_account sa on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0)
					Where  (p.payment_status=2 or p.payment_status=1) and  Date_Format(Current_Date(),'%d%m')=Date_Format(p.date_add,'%d%m')
					Group By sa.id_scheme_account)sp on(sa.id_scheme_account=sp.id_scheme_account)
			 Left Join postdate_payment pp On (sa.id_scheme_account=pp.id_scheme_account and (pp.payment_status=2 or pp.payment_status=7) and (Date_Format(pp.date_payment,'%Y%m')=Date_Format(curdate(),'%Y%m')))
				JOIN chit_settings cs 
		Where sa.active=1 and sa.is_closed = 0  and c.id_customer=".$id_customer."  
		".($id_sch_acc!='' ?'and sa.id_scheme_account='.$id_sch_acc.'' :'')."
		
		Group By sa.id_scheme_account";
			
	//	echo $sql;exit;
		$records = $this->db->query($sql);
		
		if($records->num_rows()>0)
		{
			foreach($records->result() as $record)
			{
				
				$current_installments = ($record->current_paid_installments == 0 ? $record->paid_installments+1 : $record->paid_installments+1);
				
				$set_average = 'Y';
				if($record->payment_chances == 1){
				    $set_average = 'N';
				    if(($record->curmonth_total_paidCount != 0 && $record->paid_installments >= $record->avg_calc_ins) || ($record->paid_installments >= $record->avg_calc_ins && $record->curmonth_total_paidCount < $record->max_chance) && $record->avg_calc_ins > 0){
				        $set_average = 'Y';
				    }
				}
				

				if($set_average == 'Y' && (($record->scheme_type == 1 && $record->is_flexible_wgt == 1) || $record->scheme_type == 3)&&  $record->avg_calc_ins > 0){
				    
					
					// Previous Ins == Average calc installment
					
					if($record->avg_calc_by == 0)
					{
					    if(($current_installments > $record->avg_calc_ins || $record->avg_payable > 0) && $record->avg_calc_ins > 0){
					        //30-05-2023 #AB : avg payable not storing if already in 0.000
        					if($record->avg_payable > 0 || ($record->avg_payable !== NULL && $record->avg_payable !== '0' && $record->avg_payable !== '0.000')){
        					    // Already Average calculated, just set the value
        				
        							if($record->scheme_type == 1 && $record->is_flexible_wgt == 1 ){ // Weight - Flexible weight scheme
        								// Set max payable
        							}
        							else if($record->scheme_type == 3 ){
        								if($record->flexible_sch_type == 2){ // Flexible - Amount to weight [amount based]
        									// Set max payable
        									$record->max_amount = $record->avg_payable;
        									//echo $record->max_amount;exit;
        									$record->payable = $record->avg_payable;
        									
        								}
        								elseif($record->flexible_sch_type == 3){ // Flexible - Amount to weight [weight based]
        									$record->max_weight = $record->avg_payable;
        								}						
        							}
        							
        						
        					}else{
        					    // Calculate Average , set the value and updte it in schemme_account table
        							$paid_sql = $this->db->query("SELECT sum(metal_weight) as paid_wgt,sum(payment_amount) as paid_amt 
        							FROM `payment` 
        							WHERE payment_status=1 and id_scheme_account=".$record->id_scheme_account." 
        							GROUP BY YEAR(date_payment), MONTH(date_payment)
        							limit ".$record->avg_calc_ins);
        							$paid_wgt = 0;
        							$paid_amt = 0;
        							$paid = $paid_sql->result_array();
        							
        						//	echo '<pre>';print_r($paid);exit;
        							foreach($paid as $p){
        								$paid_wgt = $paid_wgt + $p['paid_wgt'];
        								$paid_amt = $paid_amt + $p['paid_amt'];
        							}
        							
        						
        							if($record->scheme_type == 1 && $record->is_flexible_wgt == 1 ){ // Weight - Flexible weight scheme
        								// Set max payable
        							}
        							else if($record->scheme_type == 3 ){
        								if($record->flexible_sch_type == 2){ // Flexible - Amount to weight [amount based]
        									// Set max payable
        									$avg_payable = $paid_amt/$record->avg_calc_ins;
        									$record->max_amount = $avg_payable;
        								}
        								elseif($record->flexible_sch_type == 3){ // Flexible - Amount to weight [weight based]
        									$avg_payable = number_format($paid_wgt/$record->avg_calc_ins,3);
        									$record->max_weight = $avg_payable;
        								}						
        							}
        							$updData = array( "avg_payable" => $avg_payable, "date_upd" => date("Y-m-d") );
        							$this->db->where('id_scheme_account',$record->id_scheme_account); 
        		 					$this->db->update("scheme_account",$updData);
        		 					
        		 				//	print_r($this->db->last_query());exit;
        						} 
						
					    }
					}else if($record->avg_calc_by == 1 && $record->avg_calc_ins > 0){
					    //calculate average by scheme joining date
					   
					    $d1 = $record->start_date;
                        $d2 = date("d-m-Y");
                        $avg_months = $record->avg_calc_ins-1;
                        $no_of_months= (int)abs((strtotime($d1) - strtotime($d2))/(60*60*24*30));
                        $join_date = date('Y-m-d',strtotime($record->start_date));
                        $diffDate = date('Y-m-d', strtotime("+".$avg_months." months", strtotime($join_date)));
                        $endDate = date('Y-m-t',strtotime($diffDate));
                         //echo $endDate; exit;
                        if($no_of_months >= $record->avg_calc_ins)
                        {
                                if($record->avg_payable > 0){ // Already Average calculated, just set the value
							
							 if($record->scheme_type == 3 ){
								if($record->flexible_sch_type == 2 || $record->flexible_sch_type == 1){ // Flexible - Amount to weight [amount based]
									// Set max payable
									$record->max_amount = $record->avg_payable;
									//echo $record->max_amount;exit;
									//$record->min_amount = $record->avg_payable;
									$record->payable = $record->avg_payable;
									
								}
								elseif($record->flexible_sch_type == 3){ // Flexible - Amount to weight [weight based]
									$record->max_weight = $record->avg_payable;
								}						
							}
						                }else{ // Calculate Average , set the value and updte it in schemme_account table
							//$paid_sql = $this->db->query("SELECT sum(metal_weight) as paid_wgt,sum(payment_amount) as paid_amt FROM `payment` WHERE payment_status=1 and id_scheme_account=".$record->id_scheme_account." GROUP BY YEAR(date_payment), MONTH(date_payment)");
							$paid_sql = $this->db->query("SELECT sum(metal_weight) as paid_wgt,sum(payment_amount) as paid_amt FROM `payment` WHERE payment_status=1 and id_scheme_account=".$record->id_scheme_account." and date(date_payment) BETWEEN '".$join_date."' and '".$endDate."'");
							//echo $this->db->last_query();exit;
							$paid_wgt = 0;
							$paid_amt = 0;
							$paid = $paid_sql->result_array();
							foreach($paid as $p){
								$paid_wgt = $paid_wgt + $p['paid_wgt'];
								$paid_amt = $paid_amt + $p['paid_amt'];
							}
							
						
							if($record->scheme_type == 1 && $record->is_flexible_wgt == 1 ){ // Weight - Flexible weight scheme
								// Set max payable
							}
							else if($record->scheme_type == 3 ){
								if($record->flexible_sch_type == 2 || $record->flexible_sch_type == 1){ // Flexible - Amount to weight [amount based]
									// Set max payable
									$avg = $paid_amt/$record->avg_calc_ins;
									//if average amount is less than 1st ins amt set 1st ins amount
									$sql = $this->db->query("SELECT SUM(payment_amount) as payment_amount from payment 
									where payment_status=1 and id_scheme_account=".$record->id_scheme_account." 
									GROUP BY YEAR(date_payment), MONTH(date_payment) 
									order by id_payment ASC limit 1");
									$payamt = $sql->row_array();
									if($avg >= $payamt['payment_amount'])
									{
									    $avg_payable = $avg;
									}else{
									    $avg_payable = $payamt['payment_amount'];
									}
									$record->max_amount = $avg_payable;
									
								}
								elseif($record->flexible_sch_type == 3){ // Flexible - Amount to weight [weight based]
									$avg_payable = number_format($paid_wgt/$record->avg_calc_ins,3);
									$record->max_weight = $avg_payable;
								}						
							}
							
							$updData = array( "avg_payable" => $avg_payable, "date_upd" => date("Y-m-d") );
							$this->db->where('id_scheme_account',$record->id_scheme_account); 
		 					$this->db->update("scheme_account",$updData);
						} 
                        }
					    
					}
					
				}
				
				$allowed_due = 0;
				$due_type = '';
				$checkDues = TRUE;
				$allowSecondPay = FALSE;
			//	$metal_rates=$this->get_metalrate($record->sch_join_branch,$record->is_branchwise_rate);//For branchwise rate
				if($record->has_lucky_draw == 1 )
				{ 
					if( $record->group_start_date == NULL && $record->paid_installments > 1)
					{ // block 2nd payment if scheme_group_code is not updated 
						$checkDues = FALSE; 
					}
				    else if($record->group_start_date != NULL)
					{ // block before start date and payment after end date 
						 if($record->group_end_date >= time() && $record->group_start_date <= time() ){
        				 		$checkDues = TRUE;
        				 }else{
        					$checkDues = FALSE;
        				 }
					}
				}
				if($record->maturity_days!=null)
				{
				         $current_date =date("Y-m-d");
				         $maturity_date=$record->maturity_date;
				        if(strtotime($current_date) <= strtotime($maturity_date)) 
                        { 
                             $checkDues=TRUE;
                             	if(($record->missed_ins+$record->paid_installments)<=$record->total_installments)
                				{
                				    $checkDues=TRUE;
                				}else{
                				    $checkDues = FALSE;
                				}
                        }
                        else
                        {
                            $checkDues=FALSE;
                        }
				}
				if($checkDues)
				{
				if($record->paid_installments > 0 || $record->totalunpaid >0){
					if($record->currentmonthpaycount == 0){  // current month not paid (allowed pending due + current due)
						if($record->allow_unpaid == 1){
							if($record->allow_unpaid_months > 0 && ($record->total_installments - $record->paid_installments) >=  $record->allow_unpaid_months && $record->totalunpaid >0){
								if(($record->total_installments - $record->paid_installments) ==  $record->allow_unpaid_months){
									$allowed_due =  $record->allow_unpaid_months ;  
								    $due_type = 'PD'; //  pending
								}
								else{
									$allowed_due =  $record->allow_unpaid_months+1 ;  
								    $due_type = 'PN'; // normal and pending
								}
							}
							else{
							     $allowed_due =  1;
							     $due_type = 'ND'; // normal due
							}
						}
						else{
							// current month not paid (allowed advance due + current due)
							if($record->allow_advance ==1){ // check allow advance
        						if($record->advance_months > 0 && $record->advance_months <= ($record->total_installments - $record->paid_installments)){
        							if(($record->total_installments - $record->paid_installments) ==  $record->advance_months){
        									 $allowed_due =  $record->advance_months;
        									 $due_type = 'AN'; // advance and normal
        								}
        								else{
        									$allowed_due =  $record->advance_months+1 ;  
        								     $due_type = 'AN'; // advance and normal
        								}
        						}
        						else{
        							 $allowed_due =  1;
        							 $due_type = 'ND'; // normal due
        						}
        					}
        					else{
        						 $allowed_due =  1;
        						 $due_type = 'ND'; // normal due
        					}
						}
					}
					else{ 	//current month paid
					    if($record->free_payment == 1 && $record->allowSecondPay == 1 && $record->paid_installments == 1){
							$allowed_due =  1 ;
							$due_type = 'AD'; // adv due
							$allowSecondPay = TRUE;
						}else{
    						if($record->allow_unpaid == 1 && $record->allow_unpaid_months >0 && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months){  
    							// can pay previous pending dues if attempts available 
    							if($record->totalunpaid > $record->allow_unpaid_months){
    								 $allowed_due =  $record->allow_unpaid_months ;
    								 $due_type = 'PD'; // pending due
    							}
    							else{ 
    								 $allowed_due =  $record->totalunpaid;
    								 $due_type = 'PD'; // pending due
    							}
    						}
    						else{  // check allow advance
    						if($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonth_adv_paycount) < $record->advance_months){
    								if(($record->advance_months + $record->paid_installments) <= $record->total_installments){
    									 $allowed_due =  ($record->advance_months - ($record->currentmonth_adv_paycount));
    									 $due_type = 'AD'; // advance due
    								}
    								else{
    									 $allowed_due =  ($record->total_installments - $record->paid_installments);
    									 $due_type = 'AD'; // advance due
    								}
    							}
    							else{ // have to check
    								 $allowed_due =  0;
    								 $due_type = ''; // normal due
    							}
    						}
					    }
					}
				}
				else{  // check allow advance and add due with currect month (allowed advance due + current due)
					if($record->allow_advance ==1){ // check allow advance
						if($record->advance_months > 0 && $record->advance_months <= ($record->total_installments - $record->paid_installments)){
							if(($record->total_installments - $record->paid_installments) ==  $record->advance_months){
									 $allowed_due =  $record->advance_months;
									 $due_type = 'AN'; // advance and normal
								}
								else{
									$allowed_due =  $record->advance_months+1 ;  
								     $due_type = 'AN'; // advance and normal
								}
						}
						else{
							 $allowed_due =  1;
							 $due_type = 'ND'; // normal due
						}
					}
					else{
						 $allowed_due =  1;
						 $due_type = 'ND'; // normal due
					}
				}
			}
			 /*   if($this->config->item('defaulter_payment')==1)
		        {
		            $allowed_due=$record->total_installments-$record->current_pay_ins;
		            if($record->current_paid_installments==0 && $allowed_due>1)
		            {
		                $due_type='AN';
		            }
		            else if($record->current_paid_installments>0  && $allowed_due>1)
		            {
		                 $due_type='AD';
		            }
		        }*/
		    if(!empty($record->maturity_days) && $record->allow_unpaid == 0) // ** Advance Only. No Pending allowed. ** //
            {
                if($record->advance_months > 0){
	                if($record->current_paid_installments == 0 )  // Current month not Paid (Current+Advance)
	                {
		                $allowed_due = $record->total_installments-$record->current_pay_installemnt;
		                $due_type='AN';
	                }
	                else // Current month Paid (Advance)
	                {
		                $allowed_due = $record->total_installments - ($record->current_pay_installemnt+$record->current_paid_installments);
		                $due_type='AD';
	                }
                }
            }
            
            
            
            	if($record->set_as_min_from > 0 && $record->set_as_max_from > 0 && $record->paid_installments > 0){
			        if($record->dues_count > 0 && $record->paid_installments >= $record->set_as_min_from && $record->paid_installments <= $record->set_as_max_from)
			        {
			            
			            $res = $this->db->query("select p.payment_amount,sa.id_scheme_account from payment p 
    			                 left join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
    			                 where p.payment_status=1 and p.id_scheme_account = '".$record->id_scheme_account."' order by id_payment asc limit 1");
    			                 $payamount = $res->row_array();  
    			                 
    			                 if($payamount['payment_amount'] > 0)
    			                 {
    			                    //$record->min_amount = $record->dues_count * $payamount['payment_amount'];
    			                    $record->max_amount = $record->dues_count * $payamount['payment_amount']; 
    			                    $record->min_amount =  $payamount['payment_amount']; 
    			                    if($record->currentmonthpaycount != 0)
    			                    {
        			                    if($record->paid_installments > 0 && $record->currentmonthpaycount == 1)
        			                    {
        			                        $record->current_total_amount = 0;
        			                       
        			                    }else{
        			                        $month_first_day = date('Y-m-01');
        			                        
        			                        $res1 = $this->db->query("select SUM(p.payment_amount) as payment_amount,sa.id_scheme_account from payment p 
        			                        left join scheme_account sa on sa.id_scheme_account = p.id_scheme_account
        			                        where p.payment_status=1 and p.due_type != 'ND' and date(p.date_payment) between '".$month_first_day."' and '".$record->pay_date."' and p.id_scheme_account = '".$record->id_scheme_account."' ");
        			                        //echo $this->db->last_query();exit;
        			                        $amt = $res1->row_array();
        			                        $record->current_total_amount = $amt['payment_amount'];
        			                    }
    			                    }else{
    			                        $record->min_amount = $record->min_amount;
    			                        $record->max_amount = $record->dues_count * $payamount['payment_amount'];
    			                        $record->current_total_amount = 0;
    			                    }
    			                    
                                 
    			                 }else{
    			                      $record->min_amount = $record->min_amount;
    			                      $record->max_amount = $record->max_amount;
    			                 }
			        }
            	}
            
            
            // Allow Pay
		/*	if($record->scheme_type == 3){
			    if($record->flexible_sch_type == 1)
    			        {
    			            $allow_pay  = ($record->disable_payment != 1 && $record->payment_status !=2  && $record->paid_installments <= $record->total_installments ? ($record->flexible_sch_type == 1 ? (($record->current_total_amount >= $record->max_amount || $record->current_chances_used >= $record->max_chance) && $record->paid_installments != 0 ?'N':'Y') : 'N'):'N');
    			        }
    			 else{
				$allow_pay  = ($record->disable_payment != 1 && $record->payment_status !=2  && $record->paid_installments <= $record->total_installments ? ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 3 ? ($record->current_total_weight >= $record->max_weight && $record->current_chances_used >= $record->max_chance ?'N':'Y') : 'N'):'N');
    			 }
    			 }else{
				$allow_pay  = ($record->disable_payment != 1 && ($record->payment_status !=2) ? ($record->cur_month_pdc < 1 ? ($record->paid_installments <= $record->total_installments ?($record->is_flexible_wgt?($record->current_total_weight >= $record->max_weight || $record->current_chances_used >= $record->max_chance ?'N':'Y'):($record->paid_installments <  $record->total_installments ?($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'N')):'N'):'N'):'N');
			} */
			
			if($record->scheme_type == 3){
			    if($record->one_time_premium == 0){
    			         if($record->flexible_sch_type == 1 || $record->flexible_sch_type == 2)
    			        {
    			            $allow_pay  = ($record->disable_payment != 1 && $record->payment_status !=2  && (($record->is_flexible_wgt == 1 && $record->paid_installments <= $record->total_installments) || ($record->is_flexible_wgt == 0 && $record->paid_installments < $record->total_installments)) ? ($record->flexible_sch_type == 1 || $record->flexible_sch_type == 2 ? (($record->current_total_amount >= $record->max_amount || $record->current_chances_used >= $record->max_chance) && $record->paid_installments != 0 ? ($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'Y') : 'N'):'N');
    			        }
    			        elseif($record->flexible_sch_type == 3 || $record->flexible_sch_type == 4 || $record->flexible_sch_type == 8)
    			        {
    			            
    				        $allow_pay  = ($record->disable_payment != 1 && $record->payment_status !=2  && $record->paid_installments <= $record->total_installments ? ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 3 ? ($record->current_total_weight >= $record->max_weight && $record->current_chances_used >= $record->max_chance ? ($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'Y') : 'N'):'N');
    			    
    			        }else{
    			             $allow_pay  = ($record->disable_payment != 1 && $record->payment_status !=2  && $record->paid_installments < $record->total_installments ? ($record->flexible_sch_type == 2 || $record->flexible_sch_type == 3 ? ($record->current_total_weight >= $record->max_weight && $record->current_chances_used >= $record->max_chance ? ($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'Y') : 'N'):'N');

    			        }
			    }else{
			        $allow_pay  = ($record->disable_payment != 1 && $record->payment_status !=2  && $record->paid_installments == 0 && $record->is_enquiry == 0 ? ($record->flexible_sch_type == 1  || $record->flexible_sch_type == 5 ? ($record->current_total_amount >= $record->max_amount && $record->current_chances_used >= $record->max_chance ?'N':'Y') : ($record->current_total_weight >= $record->max_weight && $record->current_chances_used >= $record->max_chance ? 'N' :'Y')):'N');
			    }
			}else{
				$allow_pay  = ($record->disable_payment != 1 && ($record->payment_status !=2) ? ($record->cur_month_pdc < 1 ? ($record->paid_installments < $record->total_installments ?($record->is_flexible_wgt?($record->current_total_weight >= $record->max_weight || $record->current_chances_used >= $record->max_chance ?'N':'Y'):($record->paid_installments <  $record->total_installments ?($record->allow_unpaid == 1  && $record->totalunpaid >0 && ($record->currentmonthpaycount-1) < $record->allow_unpaid_months ?'Y':($record->allow_advance == 1 && $record->advance_months > 0 && ($record->currentmonthpaycount -1) < $record->advance_months ?'Y':($record->currentmonthpaycount == 0 ? 'Y': 'N'))):'N')):'N'):'N'):'N');
			}
			
		
			$disable_acc_payments = '';
			if($record->disable_pay == 1 && $record->disable_pay_amt > 0)
			{
			    $res = $this->db->query("SELECT sum(payment_amount) as total_csh from payment where payment_mode = 'CSH' and id_scheme_account =".$record->id_scheme_account);
			    $csh_payments = $res->row()->total_csh;
			    if($csh_payments >= $record->disable_pay_amt)
			    {
			        $allow_pay = 'N';
			        $disable_acc_payments = 'Y';
			    }
			}
			
			
//	$metal_rate = ($record->id_metal == 1 ? $metal_rates['goldrate_22ct'] : $metal_rates['silverrate_1gm']);

    //For RHR Metal Purity wise rate	
	$metal_rate = $this->get_metalrate_by_branch($record->id_branch,$record->id_metal,$record->id_purity);//For branchwise rate

    
    $payable = (($record->scheme_type==3 && $record->max_amount!=0 &&($record->flexible_sch_type==1 || $record->flexible_sch_type==2 || $record->flexible_sch_type==5) && $record->max_amount!=''?((($record->firstPayamt_maxpayable==1||$record->firstPayamt_as_payamt==1)&&($record->paid_installments>0||$record->get_amt_in_schjoin==1)&&($record->is_registered==1))?round($record->firstPayment_amt) :round($record->min_amount)):($record->scheme_type==3 && ($record->min_weight!=0 || $record->min_weight!='')? ($record->firstPayment_as_wgt==1 && $record->paid_installments>0 && $record->firstpayment_wgt != null ? round($record->firstpayment_wgt * $record->metal_rate,3) :($record->firstPayment_as_wgt==1 && $record->paid_installments == 0 ? round($record->min_weight*$record->metal_rate,3) : round(($record->max_weight - $record->current_total_weight)*$record->metal_rate))) : $record->payable)));
	
    $elig_wgt =	($record->flexible_sch_type == 3 || $record->scheme_type == 1 ? ($record->min_weight == $record->max_weight ? $record->min_weight : ($record->max_weight - $record->current_total_weight)) : ($record->firstPayment_as_wgt==1 && $record->paid_installments==0 ? ($record->max_weight - $record->current_total_weight) : (($record->max_amount / $record->metal_rate )- $record->current_total_weight)));
	


	if($record->get_amt_in_schjoin==1 && ($record->firstPayamt_maxpayable==1||$record->firstPayamt_as_payamt==1) && $record->firstPayment_amt > 0){
	    
	    $eligible_wgt = ($record->firstPayment_amt / $record->metal_rate );
	    
	}else{
	    
	    if($record->scheme_type == 1 || $record->scheme_type == 3 && ($record->flexible_sch_type == 3 || $record->flexible_sch_type == 4  || $record->flexible_sch_type == 8)){   //normal weight scheme,
	
	     $eligible_wgt = ($record->max_weight - $record->current_total_weight);
	     
    	}elseif($record->scheme_type == 2){   //normal amt to wgt
    	
    	    $eligible_wgt = ($record->amount / $metal_rate);
    	    
    	}elseif($record->flexible_sch_type == 5 || $record->flexible_sch_type == 2){
	    
	        $eligible_wgt = (($record->max_amount / $record->metal_rate )- $record->current_total_weight);
	    
	    }else{
	        $eligible_wgt = $elig_wgt;
	    }
    	    
	}	
	
		
/*	if($record->id_scheme_account == 2194){
        echo 'eligible_wgt'.$eligible_wgt;
        print_r($record);
        exit;
	}   */	
				$dates= date('d-m-Y');
				
					//chit number and receipt number based on display format settings starts...
$accNumData = array('is_lucky_draw' => $record->is_lucky_draw,
                    	'scheme_acc_number' => $record->scheme_acc_number,
                    	'scheme_group_code' => $record->scheme_group_code,
                    	'schemeaccNo_displayFrmt' => $record->schemeaccNo_displayFrmt,
                    	'scheme_wise_acc_no' => $record->scheme_wise_acc_no,
                    	'acc_branch' => $record->acc_branch,
                    	'code' => $record->code,
                    	'start_year' => $record->start_year,
						'id_scheme_account'=>$record->id_scheme_account
                    	);
	//ends
	
	//allow pay for daily scheme....
                    
                    
if($record->pay_duration == 0){
   
    if($record->maturity_type == 1 && $record->paid_installments < $record->total_installments){
        $allow_pay = 'Y';
    }else{
        if($record->curday_total_paid_count < $record->max_chance && date('Ym') <= $record->daily_sch_allow_pay_till  ){

            $allow_pay = 'Y';
        }else{
            $allow_pay = 'N';
        }
    }
}

        
        //	print_r($allow_pay);exit;
    //allow pay daily scheme ends....
	
	
	
	//RHR schemes : by 30 days payment cycle with advance , pending flow.. 20-09-2023 #ABI...
                
                	/*
                	1. ALLOW PAY : (Y/N)
                	2. ALLOWED DUES : no of dues count can pay
                	3. DUE TYPE	:
                		allow pay for days duration wise instalment cycle starts...
                		ND- only normal due , allowed_due : 1
                		AD - only advance due, allowed_due : 1
                		PD - only pending due, allowed_due : 1
                		AN - advance + normal, allowed_due : no.of.adv + 1
                		PN - pending + normal, allowed_due : no.of.pend + 1
                		APN - Advance + pending + normal, allowed_due : no.of.adv + no.of.pend + 1
                
                	*/	
                
                	if($record->installment_cycle == 2){
                		//due_type , allowed_dues
                		$date = date('Y-m-d');
                		$paid_normal_due = 0;
                		$paid_advance_due = 0;
                		$paid_pending_due = 0;
                		$paid_due = 0;
                		$remaining_normal_due = 0;
                		$remaining_advance_due = 0;
                		$remaining_pending_due = 0;
                		$remaining_due = 0;
                
                		//take the no of paid dues with due_type customer paid already...
                		$paid_dueData  = $this->db->query("SELECT due_type as due_name, COUNT(due_type) as dues_count FROM payment where payment_status = 1 and id_scheme_account = ".$record->id_scheme_account." group by due_type;")->result_array();
                
                		foreach($paid_dueData  as $due){
                
                			if($due['due_name'] == 'ND'){
                				$paid_normal_due = $due['dues_count'];
                			}else if($due['due_name'] == 'AD'){
                				$paid_advance_due = $due['dues_count'];
                			}else if($due['due_name'] == 'PD'){
                				$paid_pending_due = $due['dues_count'];
                			}else{
                				$paid_due = $due['dues_count'];
                			}
                			
                		}
                
                        
                        
                		//take the no of remaining dues with due_type customer want to pay..
                		$remaining_dueData = $this->payment_modal->get_due_date('allow_pay',$date,$record->id_scheme_account);
                
                		//echo '<pre>';print_r($this->db->last_query());exit;
                
                		foreach($remaining_dueData  as $due){
                
                			if($due['due_name'] == 'ND'){
                				$remaining_normal_due = $due['dues_count'];
                			}else if($due['due_name'] == 'AD'){
                				$remaining_advance_due = $due['dues_count'];
                			}else if($due['due_name'] == 'PD'){
                				$remaining_pending_due = $due['dues_count'];
                			}else{
                				$remaining_due = $due['dues_count'];
                			}
                			
                		}
                
                		//calculate can pay advance dueand pending dues...
                
                		if(($record->allow_advance == 1 && $record->advance_months > 0) || ($record->allow_unpaid == 1 && $record->unpaid_months > 0)){
                			//advance..
                			$sch_advance = $record->advance_months;
                			$cur_advance = abs($sch_advance - $paid_advance_due);
                			$canPay_advance = ($cur_advance > $remaining_advance_due ? $remaining_advance_due : $cur_advance);
                			
                			//pending
                			$sch_unpaid = $record->unpaid_months;
                			$cur_unpaid = abs($sch_unpaid - $paid_pending_due);  //7 - 1 = 6 ,but 5 only should allow becuse remaining
                			$canPay_pending = ($cur_unpaid  > $remaining_pending_due ? $remaining_pending_due : $cur_unpaid );
                
                			if($remaining_normal_due == 0 && $canPay_pending > 0){			//only pending
                				$due_type = 'PD';						
                				$allowed_due = $canPay_pending ;
                			}
                			else if($remaining_normal_due == 0 && $canPay_advance > 0){		//only advance
                				$due_type = 'AD';						
                				$allowed_due = $canPay_advance ;
                			}
                			else if($remaining_normal_due > 0 && $canPay_pending > 0){		//normal + pending
                				$due_type = 'PN';           			
                				$allowed_due = $remaining_normal_due + $canPay_pending;
                			}
                			else if($remaining_normal_due > 0 && $canPay_advance > 0){		//normal + advance
                				$due_type = 'AN';						
                				$allowed_due = $remaining_normal_due + $canPay_advance;
                			}
                
                		}else{
                			$due_type = 'ND';
                			$allowed_due = $remaining_normal_due;
                		}	
                
                		//allow pay , discuss for APN
                
                		if($allowed_due > 0){
                			$allow_pay = 'Y';
                		}else{
                			$allow_pay = 'N';
                		}
                
                	}
                
                //RHR schemes ends : days duration 21-09-2023...
                
    	if($record->avg_payable > 0 && $record->avg_calc_ins > 0 && (($record->avg_calc_by == 1 &&  $no_of_months >= $record->avg_calc_ins) || ($record->avg_calc_by == 0 && $record->paid_installments >= $record->avg_calc_ins))){
    	    $min_amount = $record->avg_payable;
    	    $max_amount = $record->avg_payable;
    	}else{
    	    $min_amount = round(($record->scheme_type==3 && $record->min_amount!=0 && $record->min_amount!='' ? (($record->firstPayamt_payable==1 ||$record->firstPayamt_as_payamt==1 )&&($record->paid_installments>0 || $record->get_amt_in_schjoin==1) ? $record->firstPayment_amt:($record->min_amount)):	($record->scheme_type==3 && $record->min_weight!=0 && $record->min_weight!=''? (($record->max_weight == $record->current_total_weight ? 0 :$record->min_weight)*$metal_rates['goldrate_22ct']) : $record->min_amount)));
    		$max_amount = round(($record->scheme_type==3 && $record->max_amount!=0 && $record->max_amount!='' ? (($record->firstPayamt_payable==1 ||$record->firstPayamt_as_payamt==1 )&&($record->paid_installments>0 ||$record->get_amt_in_schjoin==1) ?  $record->firstPayment_amt:($record->max_amount - str_replace(',', '',$record->current_total_amount))): ($record->scheme_type==3 && $record->max_weight!=0 && $record->max_weight!=''? (($record->max_weight - $record->current_total_weight)*$metal_rates['goldrate_22ct']) : $record->max_amount)));
    	}	
		            
                
                
	
				$schemeAcc[] = array( 
				                    'id_scheme_account'=>$record->id_scheme_account,
				                    'max_weight' 			    =>$record->max_weight,
				                    'min_weight' 			    =>$record->min_weight,
									'gst_type'					=> $record->gst_type,
									'pay_duration' 		        => $record->pay_duration,
									'branch_settings' 		    => $record->branch_settings,
									'min_chance' 		        => $record->min_chance,
									'max_chance' 		        => $record->max_chance, 
                                    'min_amount'                => $min_amount,
									'firstPayment_amt' 		    => $record->firstPayment_amt,
									'firstPayamt_payable' 		=> $record->firstPayamt_payable,
									'flx_denomintion' 		    => $record->flx_denomintion,
									'firstPayamt_as_payamt'     => $record->firstPayamt_as_payamt,
								
								//	'firstPayamt_as_payamt' 		=> ($record->current_paid_installments==0 ? 0 : $record->firstPayamt_as_payamt) ,

									'flexible_sch_type' 		=> $record->flexible_sch_type,
									'get_amt_in_schjoin' 	    => $record->get_amt_in_schjoin,
									'one_time_premium' 		    => $record->one_time_premium,
									'otp_price_fixing' 		    => $record->otp_price_fixing,
									'multiply_value' 		    => 500,
									'fixed_wgt' 		        => $record->fixed_wgt,
									'fixed_rate' 		        => $record->fixed_metal_rate,
									'maturity_date' 		    => $record->maturity_date,
									'fixed_metal_rate' 		    => ($record->fixed_rate_on==NULL ?'NO' :'YES') ,
									'max_amount'                => $max_amount,
									'metal_rate'                => $metal_rate,
									'gst' 						=> $record->gst,
									'paid_gst' 					=> $record->paid_gst,
									'id_branch' 				=> $record->id_branch,
									'short_name' 				=> $record->short_name,
									'branch_name' 				=> $record->branch_name,
									'currentmonthpaycount' 		=> $record->currentmonthpaycount,
									'totalunpaid' 				=> $record->totalunpaid,
									'id_scheme_account' 		=> $record->id_scheme_account,
									'max_wgt_rate' 				=> ($record->is_flexible_wgt == 1?($record->max_weight - $record->current_total_weight):$record->max_weight) * $metal_rates['goldrate_22ct'],
									'charge_head' 				=> $record->charge_head,
									'charge_type' 				=> $record->charge_type,
									'charge' 					=> $record->charge,
									'chit_number' 				=> ($record->chit_number==' Not Allocated' ?$this->config->item('default_acno_label'):$record->chit_number),
								//'chit_number' 				=> $this->mobileapi_model->getAccNoFormat($accNumData),
									'account_name' 				=> $record->account_name,
									'start_date' 				=> $record->start_date,
									'mobile' 					=> $record->mobile,
									'is_flexible_wgt' 	    	=> $record->is_flexible_wgt,
									'currency_symbol' 			=> $record->currency_symbol,
									//'payable'                   => (($record->scheme_type==3 && $record->max_amount!=0 &&($record->flexible_sch_type==1 || $record->flexible_sch_type==2) && $record->max_amount!=''?((($record->firstPayamt_payable==1||$record->firstPayamt_as_payamt==1)&&($record->paid_installments>0||$record->get_amt_in_schjoin==1)||($record->is_registered==1))?round($record->firstPayment_amt) :round($record->max_amount-str_replace(',', '',$record->current_total_amount))):($record->scheme_type==3 && ($record->max_weight!=0 || $record->max_weight!='')? round(($record->max_weight - $record->current_total_weight)*$record->metal_rate) : $record->payable))),
									
									'payable' =>$payable,
									'code' 						=> ($record->has_lucky_draw == 1 ?  $record->scheme_group_code : $record->code),
									'scheme_type' 				=> $record->scheme_type,
									'total_installments'		=> $record->total_installments,
									'paid_installments' 		=> $record->paid_installments,
									'total_paid_amount' 		=> $record->total_paid_amount,
									'total_paid_weight' 		=> $record->total_paid_weight,
									'current_total_amount' 		=> $record->current_total_amount,
									'current_paid_installments'	=> $record->current_paid_installments,
									'current_chances_used' 		=> $record->current_chances_used,
									'current_chances_pay'       => $record->current_chances_pay,
									//'eligible_weight' 		    => ($record->max_weight - $record->current_total_weight),
									'allow_unpaid_months' 		=> $record->allow_unpaid_months,
									'last_paid_duration' 		=> $record->last_paid_duration,
									'last_paid_date' 			=> $record->last_paid_date,
									'current_date' 		        => $dates,
									'last_paid_month' 			=> ($record->last_paid_month!='' || $record->last_paid_month!=NULL ? $record->last_paid_month : 0),
									'is_pan_required' 			=> $record->is_pan_required,
									'wgt_convert' 			    => $record->wgt_convert, 
									'last_transaction'  	    => $this->getLastTransaction($record->id_scheme_account),
									'isPaymentExist' 			=> $this->isPaymentExist($record->id_scheme_account),
									'isPendingStatExist' 		=> $this->isPendingStatExist($record->id_scheme_account),
									'max_weight' 				=> $record->max_weight,
									'current_total_weight' 		=> $record->current_total_weight,
									'previous_amount_eligible' 	=> $record->previous_amount_eligible,
									'cur_month_pdc' 			=> $record->cur_month_pdc,
									'allow_pay' 			    => $allow_pay,
								    'allowed_dues'  			=>($record->is_flexible_wgt == 1 ? 1:$allowed_due),
									'allowPayDisc'              => ($record->discount == 1 ? ($record->discount_type == 0? 'All': $record->discount_installment ) : 0),
									'firstPayDisc' 		        => $record->firstPayDisc,
									'firstPayDisc_by' 	        => $record->firstPayDisc_by,
									'discount_val' 			    => $record->discount_val,
								 	'due_type' 		            => ($record->is_flexible_wgt == 1 ? 'ND':$due_type),
									'max_allowed_limit'         => ($record->is_flexible_wgt == 1 ? 1:$allowed_due),
									'sel_due'                   =>1,   //default selected due
									'pdc_payments'              =>($record->cur_month_pdc > 0 ? $this->get_postdated_payment($record->id_scheme_account) : NULL),
									'id_scheme'    => $record->id_scheme,
									'id_customer'  => $record->id_customer,
										'curr_symb_html' => "&#8377;",
									'eligible_weight' => number_format($eligible_wgt,'3','.','')	
								);
			}			
		        
				  return array('chits' => $schemeAcc);
			
		}		
	  
		
	}
	
	//get last paid entry
	function getLastTransaction($id_scheme_account)
	{
		$sql="Select no_of_dues,payment_amount,due_type,act_amount,payment_status from payment			
			  Where (payment_status=1 Or payment_status=2 Or payment_status=7)	
			         And id_scheme_account='$id_scheme_account'";
		return $this->db->query($sql)->row_array();	         
	}
	
	//to check whether customer has payment entry
	function isPaymentExist($id_scheme_account)
	{
		$sql = "Select
					  sa.id_scheme_account,c.mobile
				From payment p
				Left Join scheme_account sa On (p.id_scheme_account = sa.id_scheme_account)
				Left Join customer c on (sa.id_customer = c.id_customer)
				Where (p.payment_status = 2 or p.payment_status = 1) And sa.id_scheme_account= '".$id_scheme_account."' ";
		
			$records = $this->db->query($sql);
		
		if($records->num_rows()>0)
		{
			return TRUE;
		}else{
			return FALSE;
		}
	}
	//to check whether customer has pending status payment entry
	function isPendingStatExist($id_scheme_account)
	{
		$sql = "Select
					  sa.id_scheme_account,c.mobile
				From payment p
				Left Join scheme_account sa On (p.id_scheme_account = sa.id_scheme_account)
				Left Join customer c on (sa.id_customer = c.id_customer)
				Where (p.payment_status = 7) And sa.id_scheme_account= '".$id_scheme_account."' ";
		
			$records = $this->db->query($sql);
		
		
		if($records->num_rows()>0)
		{
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	//Checking the customer mobile already registered
	function isMobileExists($mobile)
	{
		
		$emp_sql = $this->db->query("SELECT mobile from employee where mobile =".$mobile);
		$agent_sql = $this->db->query("SELECT mobile from agent where mobile =".$mobile);
		$customer = '';
        if($emp_sql->num_rows() > 0)
        {
            $this->db->select('mobile');
		    $this->db->where('mobile', $mobile);
            $customer= $this->db->get("employee");
        }else if($agent_sql->num_rows() > 0)
        {
            $this->db->select('mobile');
		$this->db->where('mobile', $mobile);
            $customer= $this->db->get("agent");
        }
			  
		//echo $this->db->last_query();exit;
		if($emp_sql->num_rows()>0 || $agent_sql->num_rows() > 0)
		{
			return TRUE;
		}else{
		    return FALSE;
		}		
	}
	function clientEmail($id) 
	{
	$resultset = $this->db->query("select email from customer where email='".$id."'");
		return ($resultset->num_rows() > 0 ? TRUE : FALSE);	
		
	}
	function old_get_customerByMobile($mobile,$emp_branch,$br_settings)
	{
		$record = array();
		$sql="Select c.id_customer,c.firstname,c.lastname,c.notification,c.mobile,c.email,c.id_branch,c.cus_img From customer c where c.mobile=".$mobile;
		/*if($br_settings == 1){
			if($emp_branch != NULL){
				$sql = $sql." and c.id_branch=".$emp_branch;
			}
		}*/
		$result = $this->db->query($sql);
		if($result->num_rows() > 0)
		{
				$row = $result->row_array(); 
				$file = self::CUS_IMG_PATH.'/'.$row['id_customer'].'/customer.jpg';
				$img_path = ($row['cus_img'] != null ? (file_exists($file)? $file : null ):null);
				$record = array('mobile' => $row['mobile'],'id_customer' => $row['id_customer'], 'email' => $row['email'],'id_branch' => $row['id_branch'], 'lastname' =>ucfirst( $row['lastname']),'firstname' => ucfirst($row['firstname']), 'cus_img' => $img_path);
		}
		return $record;
	} 

	function get_customerByMobile($mobile,$emp_branch,$br_settings)
	{
	
		$record = array();
		$sql="Select c.id_customer,c.firstname,c.lastname,c.notification,c.mobile,c.email,c.id_branch,c.cus_img,c.allocated_employee,c.id_agent as allocated_agent,c.active From customer c where c.mobile=".$mobile;
		
		
		/*if($br_settings == 1){
			if($emp_branch != NULL){
				$sql = $sql." and c.id_branch=".$emp_branch;
			}
		}*/
		$result = $this->db->query($sql);
		
		if($result->num_rows() > 0)
		{
				$row = $result->row_array();   
				
				$file = self::CUS_IMG_PATH.'/'.$row['id_customer'].'/customer.jpg';
				
				$img_path = ($row['cus_img'] != null ? (file_exists($file)? $file : null ):null);
				
					
				$record = array('active' => $row['active'],'allocated_employee' => $row['allocated_employee'],'allocated_agent' => $row['allocated_agent'],'mobile' => $row['mobile'],'id_customer' => $row['id_customer'], 'email' => $row['email'],'id_branch' => $row['id_branch'], 
				'lastname' =>ucfirst( $row['lastname']),'firstname' => ucfirst($row['firstname']), 'cus_img' => $img_path);
		}
	
		return $record;
	} 
	

		function cusByMobileBranchWise($mobile,$emp_branch,$br_settings)
	{
		$record = array();
		$sql="Select c.active,c.id_customer,c.firstname,c.lastname,c.notification,c.mobile,c.email,c.id_branch,c.cus_img From customer c where  c.mobile='".$mobile."'";
		/*if($br_settings == 1){
			if($emp_branch != NULL){
				$sql = $sql." and c.id_branch=".$emp_branch;
			}
		}*/
		$result = $this->db->query($sql);
		if($result->num_rows() > 0)
		{
				$row = $result->row_array(); 
				$file = self::CUS_IMG_PATH.'/'.$row['id_customer'].'/customer.jpg';
				$img_path = ($row['cus_img'] != null ? (file_exists($file)? $file : null ):null);
				$record = array('active' => $row['active'],'mobile' => $row['mobile'],'id_customer' => $row['id_customer'], 'email' => $row['email'],'id_branch' => $row['id_branch'], 'lastname' =>ucfirst( $row['lastname']),'firstname' => ucfirst($row['firstname']), 'cus_img' => $img_path);
		}
		return $record;
	}  
	
	function getWalletPaymentContent($id_payment){
	    $sql="Select
				  p.id_payment,iwa.available_points,sa.id_branch as branch,sa.id_scheme_account,cs.schemeacc_no_set,sa.id_scheme,cs.receipt_no_set,cs.scheme_wise_receipt,sa.scheme_acc_number,
				  ifnull(iwa.mobile,0) as isAvail,c.email,c.mobile,redeemed_amount,actual_trans_amt,cs.allow_referral,cs.walletIntegration,c.id_customer,cs.wallet_points,cs.wallet_amt_per_points,cs.wallet_balance_type
				From payment p
				Join chit_settings cs
				Left Join scheme_account sa on (p.id_scheme_account=sa.id_scheme_account)
				Left Join customer c on (c.id_customer=sa.id_customer)
				LEFT JOIN inter_wallet_account iwa on iwa.mobile=c.mobile
				Where p.id_payment='".$id_payment."'";
	      return $this->db->query($sql)->row_array();
	}
	
	function getPayGenData($id_payment)
	{
		$sql = "Select p.date_payment,p.payment_mode,p.id_transaction,sa.id_customer,firstPayment_amt,s.code as group_code,s.sync_scheme_code,sa.id_branch as branch,cs.scheme_wise_acc_no,cs.gent_clientid,firstPayamt_as_payamt,s.firstPayamt_maxpayable,p.id_payment,sa.id_scheme_account,sa.scheme_acc_number,sa.id_scheme,cs.schemeacc_no_set,cs.receipt_no_set,cs.scheme_wise_receipt,p.ref_trans_id,cs.edit_custom_entry_date,sa.custom_entry_date,p.payment_amount,flexible_sch_type,p.id_branch,s.is_lucky_draw, s.max_members, s.code,s.one_time_premium,p.id_transaction,p.offline_tran_uniqueid,b.warehouse,
	            p.payment_type,p.payment_mode,cs.allow_referral,s.agent_refferal,s.agent_credit_type,s.emp_refferal,sa.referal_code,s.rate_fix_by,s.rate_select,sa.fixed_wgt,p.due_type,p.receipt_no,p.payment_status,s.firstPayment_as_wgt
	            From payment p
				 left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
			        LEFT JOIN scheme s ON s.id_scheme = sa.id_scheme
			        LEFT JOIN branch b ON b.id_branch = p.id_branch
			        join chit_settings cs
				 Where p.id_payment=".$id_payment;
				//echo $sql;exit;
		return $this->db->query($sql)->row_array();	
	}
	
	function get_paymenthistory($mobile,$id_employee)
	{
		$records = array();
		$query_scheme = $this->db->query("select IFNULL(pay.receipt_year,'') as receipt_year, (select b.short_name from branch b where b.id_branch = pay.id_branch) as payment_branch, sch.code,ifnull(pay.receipt_no,'') as receipt_no,cs.scheme_wise_receipt,sa.id_scheme_account,sa.id_scheme_account,

IFNULL(sa.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sa.id_branch) as acc_branch,sch.code, id_payment, DATE_FORMAT(date_payment,'%d-%m-%Y') AS date_payment, metal_rate, payment_amount, metal_weight,pay.receipt_no,pay.add_charges,if(pay.payment_type = 'Payu checkout',(payment_amount+ifnull(add_charges,0.00)), payment_amount) as total_amt,sch.charge_head,pay.gst,pay.gst_type,br.id_branch, br.short_name, br.name as branch_name,cs.branch_settings,IFNULL(sa.account_name,'-') as account_name,sa.id_scheme_account,pay.id_employee,
		(select name from branch b where b.id_branch =cus.id_branch) as cus_branch_name,
									 if(payment_mode='CC','Credit Card',if(payment_mode='NB','Net Banking', 
									  if(payment_mode='CD','Cheque or DD',if(payment_mode='CO','Cash Pick Up',
									  if(payment_mode='OP','Other',if(payment_mode='DC','Debit Card', if(payment_mode='FP','Enrollment Offer',if(payment_mode='CSH','Cash',if(payment_mode='DC','Debit Card',payment_mode))) )))))) as oldpayment_mode,
									  
									  IFNULL(id_transaction,'-') as id_transaction, if(payment_status = 1, 'Success',if(payment_status = 2, 'Yet to Approve',if(payment_status = 5, 'Returned',if(payment_status = 6, 'Refund',if(payment_status = 7, 'Pending',if(payment_status = 3, 'Failed',if(payment_status = 4, 'Cancelled','')))))))
									  as payment_status ,sch.code , IFNULL(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number ,ref_no AS client_id, scheme_name,'&#8377;' as curr_symb_html,cs.currency_symbol,pay.payment_type,
										if(scheme_type = 0,'Amount Scheme',IF(scheme_type=1,'Weight Scheme','Amt to Wgt scheme')) as scheme_type,date(pay.entry_date) as entry_date,
										
										sa.group_code as scheme_group_code,cs.has_lucky_draw,date_format(date_payment,'%d-%m-%Y %r') as pay_date,
										IFNULL(v.village_name,'') as cus_village_name,

(select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(sch.scheme_type = 1 and sch.min_weight != sch.max_weight, COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues)),0), if(sch.scheme_type = 1 and sch.min_weight != sch.max_weight or sch.scheme_type=3, COUNT(Distinct Date_Format(p.date_payment,'%Y%m')), sum(p.no_of_dues))) ,0) from payment p where p.payment_status=1 and p.id_scheme_account=pay.id_scheme_account group by p.id_scheme_account) as paid_installments,

pm.mode_name as payment_mode


										FROM payment as pay
										left join scheme_account AS sa on sa.id_scheme_account = pay.id_scheme_account
										Left Join branch br  On (br.id_branch=sa.id_branch)
										left join scheme as sch on sch.id_scheme = sa.id_scheme
										left join customer as cus on  cus.id_customer = sa.id_customer
										LEFT JOIN village v on v.id_village=cus.id_village
										left join payment_mode pm on pm.short_code = pay.payment_mode
										join chit_settings cs
										WHERE sa.is_closed=0 and cus.mobile=".$mobile." ORDER By id_payment DESC"); 
										
		//				print_r($this->db->last_query());exit;				
										
			if($query_scheme->num_rows() > 0)
			{
				foreach($query_scheme->result() as $row)
				{
					
			/*Add GST GST Amount = ( Original Cost * GST% ) / 100 Net Price = Original Cost + GST Amount
			Remove GST GST Amount = Original Cost - ( Original Cost * ( 100 / ( 100 + GST% ) ) ) Net Price = Original Cost - GST Amount */
			
			$paid_gst = 0.00;
			$add_gst = 0.00;
			$ins_no = "";
			$allow_print = 0;
			
			if($row->payment_status == 'Yet to Approve' || $row->payment_status == 'Success'){	
				$ins_no = $this->getInsNo($row->id_payment,$row->id_scheme_account);
				if($row->id_employee == $id_employee || $row->entry_date == date('Y-m-d')){
					$allow_print = 1;
				}				
			} 		
				
			if($row->gst > 0){				
				if($row->gst_type == 1){				
					$paid_gst = $row->payment_amount*($row->gst/100);	
					$add_gst = $paid_gst;			
				}
				else{
					$paid_gst = $row->payment_amount-($row->payment_amount*(100/(100+$row->gst)));	
				}
			}
			
			
				//chit number and receipt number based on display format settings starts...
            $accNumData = array('is_lucky_draw' => $row->is_lucky_draw,
                                	'scheme_acc_number' => $row->scheme_acc_number,
                                	'scheme_group_code' => $row->scheme_group_code,
                                	'schemeaccNo_displayFrmt' => $row->schemeaccNo_displayFrmt,
                                	'scheme_wise_acc_no' => $row->scheme_wise_acc_no,
                                	'acc_branch' => $row->acc_branch,
                                	'code' => $row->code,
                                	'start_year' => $row->start_year,
            						'id_scheme_account'=>$row->id_scheme_account
                                	);
            	//ends	
            	
            	 $rcptNumData      = array('receipt_year' => $row->receipt_year,
                    	'payment_branch' => $row->payment_branch,
                    	'receiptNo_displayFrmt' => $row->receiptNo_displayFrmt,
                    	'scheme_wise_receipt' => $row->scheme_wise_receipt,
                    	'receipt_no' => $row->receipt_no,
                    	'id_payment'=>$row->id_payment,
						'id_scheme_account'=> $row->id_scheme_account
                    	);
            	
            	 $records[] = array('paid_installments'=>$row->paid_installments,'pay_date'=>$row->pay_date,'cus_village_name'=>$row->cus_village_name,'allow_print' => $allow_print,'paid_due' => $ins_no,'cus_branch_name' => $row->cus_branch_name,'curr_symb_html' => $row->curr_symb_html,'ac_name' => $row->account_name,'id_payment' => $row->id_payment,'date_payment' => $row->date_payment,
            'receipt_no' => $row->receipt_no,
            'metal_rate' => number_format($row->metal_rate,0), 'payment_amount' => number_format($row->payment_amount),'metal_weight' => $row->metal_weight,'payment_mode' => $row->payment_mode,'id_branch' => $row->id_branch,'short_name' => $row->short_name,'branch_name' => $row->branch_name,	'branch_settings' => $row->branch_settings,'id_transaction' => $row->id_transaction,'payment_status' => $row->payment_status,
            'scheme_acc_number' => ($row->is_lucky_draw == 1 ? $row->scheme_group_code.' '.$row->scheme_acc_number : $row->code.'-'.$row->start_year.'-'.$row->scheme_acc_number),
            //'scheme_acc_number' => $this->mobileapi_model->getAccNoFormat($accNumData),
           // 'receipt_no' => $this->mobileapi_model->getRcptNoFormat($rcptNumData),
            'client_id' => $row->client_id,'scheme_name' => $row->scheme_name, 'scheme_type' => $row->scheme_type, 'currency_symbol' => $row->currency_symbol, 'add_charges' => $row->add_charges, 'payment_type' => ($row->payment_type == 'CSH'?'Cash':$row->payment_type), 'total_amt' => number_format(($row->total_amt+$add_gst),'0','.',''), 'charge_head' => $row->charge_head, 'gst' => $row->gst, 'gst_type' => $row->gst_type,'paid_gst'=>number_format($paid_gst,'2','.',''));
    		
            	
    				}
			}
		return $records;
	}
	
	function getInsNo($id_payment,$schId){
		$sql = $this->db->query("select IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')),COUNT(Distinct Date_Format(pay.date_payment,'%Y%m'))) as installment 
			from payment pay 
			left join scheme_account sa on sa.id_scheme_account=pay.id_scheme_account
			where pay.id_scheme_account=".$schId." and id_payment<=".$id_payment." group by pay.id_scheme_account");//echo $this->db->last_query();exit;
		return $sql->row('installment');
	}
	
	function get_branch($emp_branch,$id_profile,$id_scheme='')
	{		
		 $data = $this->get_costcenter();
		 if (empty($id_scheme)) {	
	    if($id_profile == 1 || $id_profile == 2 ){
			$sql = "SELECT * FROM branch b  where  (show_to_all = 1 or show_to_all = 2) and active=1";		
		}else{
			if(gettype($emp_branch) == 'integer'){
				$sql = "SELECT * FROM branch b  where (show_to_all = 1 or show_to_all = 2) and active=1 and id_branch=".$emp_branch;
			}else{
				$sql = "SELECT * FROM branch b  where (show_to_all = 1 or show_to_all = 2) and active=1";
			} 	
		}
		
		$branch = $this->db->query($sql)->result_array();		
		return $branch;		
	} 
	
		
		// SCHEME FILTER
        $branch_ids = $this->db->select('id_branch')
                               ->from('scheme_branch')
                               ->where('id_scheme', $id_scheme)
                               ->get()
                               ->result_array();
    
        if (empty($branch_ids)) {
            return [];
        }
		    
        $branch_ids = array_column($branch_ids, 'id_branch');
        $ids = ($data['branchwise_scheme'] == 1 ? implode(',', $branch_ids):($emp_branch != '' && $emp_branch != 0 ? $emp_branch:''));
		if (!empty($ids)){
			$where = " and b.id_branch IN ($ids)";
		}
		else {
			$where = '';
		}
		$sql = "SELECT id_branch,b.name FROM branch b  where  (show_to_all = 1 or show_to_all = 2) and active=1  and b.id_branch IN ($ids)";
		$branch = $this->db->query($sql)->result_array();	
		return $branch;	
	} 
	
	function get_all_branch()
	{
		
		$sql = "SELECT b.id_branch, b.name, b.active, b.short_name,
		b.id_employee,b.address1, b.address2, b.id_country, b.id_state, 
		b.id_city, b.phone,b.mobile, b.cusromercare, b.pincode,
		b.metal_rate_type,c.branch_settings FROM branch b
		join chit_settings c  ";
		$branch = $this->db->query($sql)->result_array();		
		return $branch;
	}
	
	/*function get_payment_collection($from_date,$To_date,$id_employee,$emp_branch) 
	{
	    if($from_date!='' && $To_date!='' )
    	{
            $sql = "Select 
                        c.firstname as customer,sa.account_name as acc_name,sa.scheme_acc_number,b.name,DATE_FORMAT(p.date_payment,'%d-%m-%Y') AS payment_date,p.payment_amount,ifnull(p.metal_weight,'-') as payment_weight,psm.payment_status,p.payment_type
                    From payment p
                        left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
                        Left Join customer c On (c.id_customer=sa.id_customer)
                        Left Join branch b  On (p.id_branch=b.id_branch)
                        LEFT Join payment_status_message psm ON (p.payment_status=psm.id_status_msg)
                        join chit_settings cs
                    Where  sa.active=1 and p.added_by='3' and p.payment_status='1' and date(p.date_payment) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($To_date))."' and p.id_employee=".$id_employee."  ".($emp_branch!=''?' and p.id_branch ='.$emp_branch:'')." ";
		}
	    else {
	        $sql = "Select 
	                    c.firstname as customer,sa.account_name as acc_name,sa.scheme_acc_number,b.name,DATE_FORMAT(p.date_payment,'%d-%m-%Y') AS payment_date,p.payment_amount,ifnull(p.metal_weight,'-') as payment_weight,psm.payment_status,p.payment_type
				    From payment p
        				 left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
        				 Left Join customer c On (c.id_customer=sa.id_customer)
        				 Left Join branch b  On (p.id_branch=b.id_branch)
        				 LEFT Join payment_status_message psm ON (p.payment_status=psm.id_status_msg)
        				 join chit_settings cs
				    Where  sa.active=1 and p.added_by='3' and p.payment_status='1' and p.id_employee=".$id_employee."  ".($emp_branch!=''?' and p.id_branch ='.$emp_branch:'')." ";
		}
		return $this->db->query($sql)->result_array();
	}*/
	
	
		function get_payment_collection($from_date,$To_date,$id_employee,$login_type,$emp_branch,$payment_mode,$metal_filter) 
	{
	    $emp_branch = 0;
	    
	   // $sql = $this->db->query("ALTER TABLE `partial_payment` ADD `id_branch` INT(5) NULL AFTER `id_scheme_account`");
	    
	   // print_r($sql);exit;
	   
	   $monthly = [];
	   
	   $daily = [];
	   ///'account_type'  => $record->id_metal == 1 ? 'Gold' : 'Silver'

    	$month =  $this->db->query("Select IFNULL(sa.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sa.id_branch) as acc_branch,s.code,
		                s.is_lucky_draw,ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,cs.scheme_wise_acc_no,
		                IFNULL(p.receipt_year,'') as receipt_year, (select b.short_name from branch b where b.id_branch = p.id_branch) as payment_branch, s.code,ifnull(p.receipt_no,'') as receipt_no,cs.scheme_wise_receipt,sa.id_scheme_account,sa.id_scheme_account,
    	
    	    DATE_FORMAT(p.date_payment,'%d-%m-%Y') AS entry_date,
    	
    	    if(s.id_metal = 1, 'Gold','Silver') as account_type,s.id_metal,p.id_payment, p.id_branch,
    	
    	    'p' as tblname,c.firstname as customer,s.code as scheme_code,sa.account_name as acc_name,IFNULL(sa.scheme_acc_number,'Not Allocated') as oldscheme_acc_number,b.name, DATE_FORMAT(p.date_payment,'%d-%m-%Y %r') AS payment_date,ROUND(p.payment_amount,0) as payment_amount,ifnull(p.metal_weight,'-') as payment_weight,psm.payment_status,
			 p.id_employee,p.receipt_no,if(p.payment_mode='CC','Credit Card',if(p.payment_mode='NB','Net Banking', 
			 if(p.payment_mode='CD','Cheque or DD',if(p.payment_mode='CO','Cash Pick Up',
			 if(p.payment_mode='OP','Other',if(p.payment_mode='DC','Debit Card', if(p.payment_mode='FP','Enrollment Offer',if(p.payment_mode='CSH','Cash',p.payment_mode)))))))) as payment_mode,if(p.payment_type='CSH','Cash',p.payment_type) as payment_type ,
			 
			 
			  (SELECT ifnull(SUM(pay.metal_weight),'-')  FROM payment pay WHERE pay.payment_status = 1 and pay.id_scheme_account = sa.id_scheme_account) as total_paid_weight,
    	    
    	    (SELECT ROUND(ifnull(SUM(pay.payment_amount),'-'),0)  FROM payment pay WHERE pay.payment_status = 1 and pay.id_scheme_account = sa.id_scheme_account) as total_paid_amt,
    	    
(select IFNULL(IF(sa.is_opening=1,IFNULL(sa.paid_installments,0)+ IFNULL(if(s.scheme_type = 1 and s.min_weight != s.max_weight, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues)),0), if(s.scheme_type = 1 and s.min_weight != s.max_weight or s.scheme_type=3, COUNT(Distinct Date_Format(pay.date_payment,'%Y%m')), sum(pay.no_of_dues))) ,0) from payment pay where pay.payment_status=1 and pay.id_scheme_account=p.id_scheme_account group by pay.id_scheme_account) as paid_installments
    	    
    	    
			 From payment p
			 left join scheme_account sa on sa.id_scheme_account=p.id_scheme_account
			 left join scheme s on s.id_scheme = sa.id_scheme
			 Left Join customer c On (c.id_customer=sa.id_customer)
			 Left Join branch b  On (p.id_branch=b.id_branch)
			 LEFT Join payment_status_message psm ON (p.payment_status=psm.id_status_msg)
			 LEFT JOIN partial_payment pp ON pp.id_scheme_account=sa.id_scheme_account
			 LEFT JOIN metal m on (m.id_metal = s.id_metal)
			 join chit_settings cs
			 
			 Where  sa.active=1  and p.payment_status = 1 
			 ".($id_employee!='' && $login_type!='' && $login_type=='EMP' ? ' and p.id_employee ='.$id_employee :'and p.id_agent ='.$id_employee)."  
			 ".($emp_branch!='' && $emp_branch!=0?' and p.id_branch ='.$emp_branch:'')."
			  ".($metal_filter!='' && $metal_filter!= null && $metal_filter > 0 ? 'and s.id_metal = '.$metal_filter :'')."
			 ".($payment_mode!=''  && $payment_mode != 'all' ? "and p.payment_mode ='".$payment_mode."'" : '')."
			 ".($from_date!='' && $from_date!=null && $To_date!='' && $To_date!=null  ? "and date(p.date_payment) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($To_date))."' " :'')."
                order by p.date_payment desc
			 ")->result_array();
			  
			  
			 
			 
		$everyday = $this->db->query("Select IFNULL(sa.start_year,'') as start_year,(select b.short_name from branch b where b.id_branch = sa.id_branch) as acc_branch,s.code,
		                s.is_lucky_draw,ifnull(sa.scheme_acc_number,'Not Allocated') as scheme_acc_number,cs.scheme_wise_acc_no,
		                null as receipt_year, (select b.short_name from branch b where b.id_branch = pp.id_branch) as payment_branch, s.code,ifnull(pp.partial_receipt_no,'') as receipt_no,cs.scheme_wise_receipt,sa.id_scheme_account,sa.id_scheme_account, pp.id_branch,
    	    DATE_FORMAT(pp.date_payment,'%d-%m-%Y') AS entry_date,
		if(s.id_metal = 1, 'Gold','Silver') as account_type,s.id_metal,pp.id_partial_payment as id_payment,
		    'pp' as tblname,c.firstname as customer,s.code as scheme_code,sa.account_name as acc_name,IFNULL(sa.scheme_acc_number,'Partial-Not Allocated') as oldscheme_acc_number,b.name, DATE_FORMAT(pp.date_payment,'%d-%m-%Y %r')  AS payment_date,pp.payment_amount,ifnull(pp.metal_weight,'-') as payment_weight,psm.payment_status,
			 pp.id_employee,'' as receipt_no,if(pp.payment_mode='CC','Credit Card',if(pp.payment_mode='NB','Net Banking', 
			 if(pp.payment_mode='CD','Cheque or DD',if(pp.payment_mode='CO','Cash Pick Up',
			 if(pp.payment_mode='OP','Other',if(pp.payment_mode='DC','Debit Card', if(pp.payment_mode='FP','Enrollment Offer',if(pp.payment_mode='CSH','Cash',pp.payment_mode)))))))) as payment_mode,if(pp.payment_mode='CSH','Cash',pp.payment_mode) as payment_type 
			 
			 From scheme_account sa
			 LEFT JOIN partial_payment pp ON pp.id_scheme_account=sa.id_scheme_account
			 left join scheme s on s.id_scheme = sa.id_scheme
			 Left Join customer c On (c.id_customer=sa.id_customer)
			 Left Join branch b  On (b.id_branch=sa.id_branch)
			 LEFT Join payment_status_message psm ON (pp.payment_status=psm.id_status_msg)
			  LEFT JOIN metal m on (m.id_metal = s.id_metal)
			 join chit_settings cs
			 
			 Where  sa.active=1   and pp.payment_status = 1  
			 ".($id_employee!='' && $login_type!='' && $login_type=='EMP' ? ' and pp.id_employee ='.$id_employee :'and pp.id_agent ='.$id_employee)."  
			 ".($emp_branch!='' && $emp_branch!=0?' and pp.id_branch ='.$emp_branch:'')."
			  ".($metal_filter!='' && $metal_filter!= null && $metal_filter > 0 ? 'and s.id_metal = '.$metal_filter :'')."
			 ".($payment_mode!=''  && $payment_mode != 'all' ? "and pp.payment_mode ='".$payment_mode."'" : '')."
			 ".($from_date!='' && $from_date!=null && $To_date!='' && $To_date!=null  ? "and date(pp.date_payment) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".date('Y-m-d',strtotime($To_date))."' " :'')."
			 order by pp.date_payment desc
			 ")->result_array();	
			 //print_r($this->db->last_query());exit;
			 foreach($month as $mp){
	            $monthly[] = $mp;
	         }
	         
	         foreach($everyday as $ed){
	            $daily[] = $ed;
	         }
	         
	         $result = array_merge($monthly, $daily);
	         
	        
	             
	             
	       foreach($result as $row){
	           
	           	  $allow_print = 0;
	           	 
            	
            	if($row['payment_status'] == 'Yet to Approve' || $row['payment_status']== 'Success' || $row['payment_status']== 'Canceled'  ){	
            		$ins_no = $this->getInsNo($row['id_payment'],$row['id_scheme_account']);
            		if($row['id_employee'] == $id_employee || $row['entry_date'] == date('Y-m-d')){
            			$allow_print = 1;
            		}				
            	} 
            	
            	$row['allow_print'] = $allow_print;
   
            		//chit number and receipt number based on display format settings starts...
            $accNumData = array('is_lucky_draw' => $row['is_lucky_draw'],
                                	'scheme_acc_number' => $row['scheme_acc_number'],
                                	'scheme_group_code' => $row['scheme_group_code'],
                                	'schemeaccNo_displayFrmt' => $row['schemeaccNo_displayFrmt'],
                                	'scheme_wise_acc_no' => $row['scheme_wise_acc_no'],
                                	'acc_branch' => $row['acc_branch'],
                                	'code' => $row['code'],
                                	'start_year' => $row['start_year'],
            						'id_scheme_account'=>$row['id_scheme_account']
                                	);
            	//ends	
            	
            	
            	 $rcptNumData      = array('receipt_year' => $row['receipt_year'],
                    	'payment_branch' => $row['payment_branch'],
                    	'receiptNo_displayFrmt' => $row['receiptNo_displayFrmt'],
                    	'scheme_wise_receipt' => $row['scheme_wise_receipt'],
                    	'receipt_no' => $row['receipt_no'],
                    	'id_payment'=>$row['id_payment'],
						'id_scheme_account'=> $row['id_scheme_account']
                    	);
                    	
                    	
                    //	print_r($accNumData);exit;
                    	
                $row['scheme_acc_number'] = $row['code'].'-'.$row['start_year'].'-'.$row['scheme_acc_number'];
               // $row['receipt_no'] = $this->mobileapi_model->getRcptNoFormat($rcptNumData);	
   
            	$res[] = $row;
	       }      
            

		return $res;
	}
	
	
	function get_employee_details($emp_code)
	{
	     $return_data='';
	     $sql=$this->db->query("SELECT * FROM employee WHERE emp_code='".$emp_code."'");
         if($sql->num_rows() == 1)
         {
             $return_data=$sql->row_array();
              return $return_data;
         }
         else{
              return $return_data;
         }
        
	}
	
		function get_entry_records($pay_id)
    {
        $query = $this->db->query("SELECT cs.branch_settings,b.name as branch_name,CONCAT(e.firstname,'-',e.emp_code) as emp_data,e.firstname as emp_name,pay.id_scheme_account as id_scheme_account, DATE_FORMAT(pay.date_payment,'%d-%m-%Y %r') as date_payment, sch.scheme_name as scheme_name, pay.payment_amount as payment_amount,sch_acc.account_name as firstname, cus.lastname as lastname, cus.mobile, addr.address1 as address1,
        cus.email,
        
        ifnull(if(payment_mode='CC','Credit Card',if(payment_mode='NB','Net Banking',if(payment_mode='CD','Cheque or DD',if(payment_mode='CO','Cash Pick Up',if(payment_mode='OP','Other',if(payment_mode='CSH','CASH',if(payment_mode='Wallet','Wallet',if(payment_mode='UPI','UPI',if(payment_mode='DC','Debit Card',payment_mode))))))))),'-') as payment_mode,
        
        
        
        pay.receipt_no,pay.metal_rate,pay.id_branch,pay.id_payment,sch_acc.account_name,cmp.company_name,
        if(pay.payment_status=1,'Success',if(pay.payment_status=2,'Awaiting',if(pay.payment_status=3,'Pending',if(pay.payment_status=4,'Cancelled',if(pay.payment_status=5,'Failed','-'))))) as payment_status,
        (select IFNULL(IF(sch_acc.is_opening=1,IFNULL(sch_acc.paid_installments,0)+ IFNULL(if(sch.scheme_type = 1 and sch.min_weight != sch.max_weight, COUNT( Date_Format(paym.date_payment,'%Y%m')), sum(paym.no_of_dues)),0), if(sch.scheme_type = 1 and sch.min_weight != sch.max_weight or sch.scheme_type=3, COUNT( Date_Format(paym.date_payment,'%Y%m')), sum(paym.no_of_dues))) ,0) from payment paym where paym.payment_status=1 and paym.id_scheme_account=pay.id_scheme_account group by paym.id_scheme_account) as paid_due,if(pay.metal_weight=0,'-',pay.metal_weight) as metal_weight,
        if(pay.receipt_no is null,'',concat(IFNULL(concat(ifnull(concat(pay.receipt_year,'-'),'')),''),pay.receipt_no)) as receipt_no,
		if(cs.has_lucky_draw=1,concat(concat(ifnull(sch_acc.group_code,''),' ',ifnull(sch_acc.scheme_acc_number,'Not Allocated')),' - ',sch.code ),concat(sch.code,' ',ifnull(sch_acc.scheme_acc_number,'Not Allcoated'))) as oldscheme_acc_number,
		CONCAT(sch.code,'-',ifnull(sch_acc.start_year,''),'-',ifnull(sch_acc.scheme_acc_number,'Not Allcoated')) as scheme_acc_number,
		cmp.tollfree1,
		(SELECT if(SUM(py.metal_weight) > 0 , SUM(py.metal_weight) ,'-') as metal_weight FROM payment py WHERE py.payment_status=1 and py.id_scheme_account=pay.id_scheme_account) as acc_weight,
		(SELECT if(SUM(py.payment_amount) > 0 , SUM(py.payment_amount) ,'-') as payment_amount FROM payment py WHERE py.payment_status=1 and py.id_scheme_account=pay.id_scheme_account) as tot_paid_amount,

		sch.scheme_name
        FROM payment as pay 
        LEFT JOIN scheme_account sch_acc ON sch_acc.id_scheme_account = pay.id_scheme_account 
        LEFT JOIN employee e on e.id_employee = pay.id_employee
        LEFT JOIN scheme sch ON sch.id_scheme = sch_acc.id_scheme LEFT JOIN customer as cus ON cus.id_customer = sch_acc.id_customer 
        LEFT JOIN branch b ON (b.id_branch = pay.id_branch)
         join chit_settings cs
        join company cmp LEFT JOIN address as addr ON addr.id_customer = cus.id_customer WHERE pay.payment_status=1 and pay.id_payment = ".$pay_id);
       
       
       
        //echo $this->db->last_query();exit;
		return $query->row_array();
    }
    
    
    function getBranchGateways($branch_id)
	{
	    
   		//$sql="SELECT * from gateway_branchwise where is_default=1 and id_branch=".$branch_id."";
		$data=$this->get_costcenter();
   		$sql="SELECT id_pg,id_branch,pg_name,pg_code,param_1,param_2,param_3,param_4,api_url,type,type,pg_icon,pg_icon,saveCard,saveCard,debitCard,netBanking,creditCard,date_add,is_primary_gateway,description,active from gateway where active=1 and is_default=1 ".($branch_id!='' && ($data['cost_center']==1 || $data['cost_center']==3) ? "and id_branch=".$branch_id."":'')." ";
	
		$result = $this->db->query($sql); 
       if($result->num_rows() > 0){
        foreach( $result->result_array() as $row){
           	   $file =base_url().'admin/assets/img/gateway/'.$row['pg_icon'];
           	   $img_path = ($row['pg_icon'] != null ? (file_exists('admin/assets/img/gateway/'.$row['pg_icon'])? $file : null ):null);
           	$record[] = array( 'pg_name' => $row['pg_name'],'pg_code' => $row['pg_code'],'netBanking' => $row['netBanking'],'is_primary_gateway' => $row['is_primary_gateway'],'active' => $row['active'], 'description' => $row['description'],'id_pg' => $row['id_pg'],'saveCard' => $row['saveCard'],'creditCard' => $row['creditCard'],'debitCard' => $row['debitCard'], 'pg_icon' => $img_path);
        }
       }		
		//echo"<pre>"; print_r($record);exit; echo"<pre>";
       return $record;
   }
   
   function get_costcenter()
  	{
   		$sql="SELECT * from  chit_settings";
		$result=  $this->db->query($sql)->row_array();
		return $result;   	
   }
   
   function get_currency($id_branch)
	{
		$sql = " Select ".$this->config->item('otherprofile_req')." as otherprofile_req ,".$this->config->item('searchbyaccno')."  as searchbyaccno,cs.isOTPReqToLogin as reg_otp_required ,cs.isOTPRegForPayment as payment_otp_required,cs.branchwise_scheme,cs.is_kyc_required,cs.cost_center,c.company_name,cs.currency_symbol,cs.rate_history,cs.show_closed_list,cs.currency_name,cs.allow_notification,cs.reg_existing,cs.regExistingReqOtp,cs.useWalletForChit,allow_catlog ,c.tollfree1 as tollfree,
		cs.allow_referral,cs.has_lucky_draw,cs.enableSilver_rateDisc,cs.enableGoldrateDisc,cs.is_branchwise_cus_reg,cs.branch_settings,'&#8377;' as curr_symb_html,enable_dth,0 as show_invite,1 as allow_shareonly,allow_wallet  
				from company c
					join chit_settings cs ";
		 
		$data=$this->get_chit_settings();
		
		if($data['is_branchwise_rate']==1)
		{
		    if($id_branch!='' && $id_branch!=0)        // branch wise rate view. If you are logged in//HH
		    {
                $sql1="SELECT  b.name as name,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,
                Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime   
                FROM metal_rates m 
                LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates
                left join branch b on b.id_branch=br.id_branch
                ".($id_branch!='' ?" WHERE br.id_branch=".$id_branch."" :'')." ORDER by br.id_metalrate desc LIMIT 1";
                 //print_r($sql1);exit;
		    }
		    else
		    {
		        $sql1="SELECT  b.name as name,m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,
                Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime   
                FROM metal_rates m 
                LEFT JOIN branch_rate br on br.id_metalrate=m.id_metalrates
                left join branch b on b.id_branch=br.id_branch
                WHERE br.id_branch=1 ORDER by br.id_metalrate desc LIMIT 1";     //1st branch rate view. If you are not logged in//HH
                 //print_r($sql1);exit;
		    }
	
				 
		}
		else{
		$sql1="SELECT  m.mjdmagoldrate_22ct,m.goldrate_22ct,m.goldrate_24ct,m.silverrate_1gm,m.silverrate_1kg,m.mjdmasilverrate_1gm,platinum_1g,m.goldrate_18ct,

				Date_format(m.updatetime,'%d-%m%-%Y %h:%i %p')as updatetime   FROM metal_rates m 

				WHERE m.id_metalrates=( SELECT max(m.id_metalrates) FROM metal_rates m )";
		}
		$data = $this->db->query($sql);	
		
		$result['currency']=$data->row_array();
 
		$rate = $this->db->query($sql1);	
		
		$result['metal_rates']=$rate->row_array();
		
		if($result['metal_rates']['silverrate_1gm']==0)
		{
		    $silver="SELECT m.id_metalrates,m.silverrate_1gm FROM metal_rates m WHERE m.silverrate_1gm!='0.00' ORDER by m.id_metalrates DESC LIMIT 1";
		    $silver_rate = $this->db->query($silver)->row_array();
		    $result['metal_rates']['silverrate_1gm']=$silver_rate['silverrate_1gm'];
		}
		if($result['metal_rates']['platinum_1g']==0)
		{
		    $silver="SELECT m.id_metalrates,m.platinum_1g FROM metal_rates m WHERE m.silverrate_1gm!='0.00' ORDER by m.id_metalrates DESC LIMIT 1";
		    $silver_rate = $this->db->query($silver)->row_array();
		    $result['metal_rates']['platinum_1g']=$silver_rate['platinum_1g'];
		}
		$result['reg_custom_fields'] = $this->config->item('app_custom_fields');
		return $result;
	}
	
	function get_company()
   {
   	$sql = " Select  c.id_company,c.company_name,cs.edit_custom_entry_date,c.comp_name_in_sms,c.gst_number,c.short_code,c.pincode,c.mobile,c.whatsapp_no,c.phone,c.email,c.website,c.address1,c.address2,c.id_country,c.id_state,c.id_city,ct.name as city,s.name as state,cy.name as country,cs.currency_symbol,cs.currency_name,cs.mob_code,cs.mob_no_len,c.mail_server,c.mail_password,c.send_through,c.mobile1,c.phone1,c.smtp_user,c.smtp_pass,c.smtp_host,c.server_type,
   	cs.login_branch
	  from company c
					join chit_settings cs
					left join country cy on (c.id_country=cy.id_country)
					left join state s on (c.id_state=s.id_state)
					left join city ct on (c.id_city=ct.id_city)";
		$result = $this->db->query($sql);	//print_r($result->row_array());exit;
		return $result->row_array();
   }
   
   function get_customer_byAcc($code,$year,$acc_no){
       $sql = "SELECT sa.id_customer,c.mobile,sa.id_scheme_account FROM `scheme_account` sa
        left join customer c on (c.id_customer = sa.id_customer)
        left join scheme s on (s.id_scheme = sa.id_scheme)
        where s.code = '$code' AND sa.scheme_acc_number = '$acc_no' AND RIGHT(YEAR(sa.start_date),2) = RIGHT('$year',2) ";
        //print_r($sql);exit;
        $result = $this->db->query($sql);	
		return $result->row_array();
   }
   
   function get_customerByAgent($id_agent)
	{
		$record = array();
		$sql="Select c.id_customer,c.id_branch,c.firstname,c.lastname,c.notification,c.mobile,c.email,c.id_branch,c.cus_img From customer c where c.id_agent=".$id_agent;
	
		$result = $this->db->query($sql);
		if($result->num_rows() > 0)
		{
				$customers = $result->result_array(); 
				foreach($customers as $row)
				{
    				$file = self::CUS_IMG_PATH.'/'.$row['id_customer'].'/customer.jpg';
    				$img_path = ($row['cus_img'] != null ? (file_exists($file)? $file : null ):null);
    				$record[] = array('mobile' => $row['mobile'],'id_customer' => $row['id_customer'], 'email' => $row['email'],'id_branch' => $row['id_branch'], 'lastname' =>ucfirst( $row['lastname']),'firstname' => ucfirst($row['firstname']), 'cus_img' => $img_path,'payments' => $this->get_payment_details($row['id_customer'],$row['id_branch'],''));
				}
		}
		return $record;
	}
	
	function get_activeSchemes($id_branch)
	{
		$data=$this->get_costcenter();
		$file_path = base_url()."admin/assets/img/sch_image"; 
		 $sql = ('SELECT is_enquiry,if(s.logo=null,s.logo ,concat("'.$file_path.'","/",s.logo) ) as sch_logo,if(scheme_type = 1 ,if(max_weight=min_weight,1,0),"a") as type,s.id_scheme,min_amount,max_amount,classification_name as cls_name,s.id_classification,scheme_name,max_weight,min_weight,amount,code,s.description,scheme_type,total_installments,interest,interest_by,interest_value,s.flx_denomintion,
		 s.flexible_sch_type,s.one_time_premium,1 as sel_due,"ND" as due_type,s.payment_chances as max_allowed_limit
		FROM scheme s
		left join sch_classify cls on cls.id_classification = s.id_classification
		'.($data['branchwise_scheme']==1 ? "left join scheme_branch sb on sb.id_scheme=s.id_scheme" :'').'
		where s.active=1 and visible=1 '.($id_branch!=''&& $data['branchwise_scheme']==1 ?'and sb.id_branch in ('.$id_branch.')' :'').' ORDER BY amount ASC');
		//print_r($sql);exit;
		return $this->db->query($sql)->result_array();
	}
	
	function isOfflineMobile($mobile)
	{
	    $res = array();
		$this->db->select('id_customer,mobile');
		$this->db->where('mobile', $mobile);
        
		$customer= $this->db->get("customer");	  
		//echo $this->db->last_query();exit;
		if($customer->num_rows()>0)
		{
		    $data = $customer->row_array();
		    $res = array('status' => TRUE,'id_customer' => $data['id_customer']);
			return $res;
		}			
	}
	
	function getCustomerByMobile($mobile)
	{
	    $sql="SELECT id_customer,reference_no from customer where mobile =".$mobile;
		$result=  $this->db->query($sql)->row_array();
		return $result; 
	}
	
	function monthly_agent_reports($id_employee,$login_type)
	{
	    $from_date = date('Y-m-01');
	    $to_date = date('Y-m-d');//echo $to_date;exit;
	    $data = array();
	    $pending = array();$total = 0;
	    
	    $sql = $this->db->query("SELECT id_customer,mobile from customer where id_agent=".$id_employee);
	    
	    if($sql->num_rows() > 0)
	    {
	        $result = $sql->result_array();
	        
	        foreach($result as $id_cus)
	        {
	            
	                //fetch total payments collected for current month
	                $sql = $this->db->query("SELECT c.firstname as customer,c.mobile,sum(p.payment_amount) as payment_amount from customer c
	                left join scheme_account sa on sa.id_customer=c.id_customer
	                left join payment p on p.id_scheme_account=sa.id_scheme_account
	                where sa.active=1 and p.payment_status=1 and c.id_customer=".$id_cus['id_customer']." and date(p.date_payment) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".$to_date."'");
	                $total_collection[] = $sql->row_array();
	                
	                //fetch pending payments for current month
	               
	                $pending = $this->db->query("SELECT sa.id_scheme_account,sa.scheme_acc_number,c.firstname as customer,c.mobile from scheme_account sa
	                
	                 Left Join (Select sa.id_scheme_account,
                    CASE
                         WHEN sa.is_opening='1' AND p.date_payment is null THEN Date_add(sa.last_paid_date,Interval 1 month)
                         when p.date_payment is null and sa.is_opening='0' then sa.date_add
                    ELSE 
                        Date_add(max(p.date_payment),Interval 1 month)
                    END AS next_due
                    From scheme_account sa
                    Left Join payment p on(p.id_scheme_account=sa.id_scheme_account and sa.active=1 and sa.is_closed=0 and p.payment_status='1')
                    Group By sa.id_scheme_account)d on(d.id_scheme_account=sa.id_scheme_account)
                    LEFT JOIN payment p ON (sa.id_scheme_account = p.id_scheme_account AND p.payment_status = 1)
                    left join customer c on c.id_customer=sa.id_customer
	                where sa.active=1 and sa.id_customer=".$id_cus['id_customer']." and date(d.next_due) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".$to_date."'");
	                $monthly_pending[] = $pending->row_array();
	            
	                //fetch completed payments for current month
	                $sql = $this->db->query("SELECT c.firstname as customer,c.mobile,sum(p.payment_amount) as payment_amount from customer c
	                left join scheme_account sa on sa.id_customer=c.id_customer
	                left join payment p on p.id_scheme_account=sa.id_scheme_account
	                where sa.active=1 and p.payment_status=1 and c.id_customer=".$id_cus['id_customer']." and date(p.date_payment) BETWEEN '".date('Y-m-d',strtotime($from_date))."' AND '".$to_date."'");
	                $completed[] = $sql->row_array();
	                
	               
	        }
	        
	            $data['total'] =   $total_collection;
	            $data['pending'] = $monthly_pending;
	            $data['completed'] = $completed;
	           
	        
	    }
	    
	    return $data;
	    
	   
	}
	
	function customer_reports($id_employee,$login_type)
	{
	   
	    $result = $this->db->query("Select c.id_customer,c.firstname as firstname,c.lastname,c.mobile,c.email,c.id_branch from customer c
        				  ".($id_employee!='' && $login_type!='' && $login_type=='AGENT' ? ' Where c.id_agent ='.$id_employee :'')." ");
        
        if($result->num_rows() > 0)
		{
			$customers = $result->result_array(); 
			foreach($customers as $row)
			{
    			$file = self::CUS_IMG_PATH.'/'.$row['id_customer'].'/customer.jpg';
    			$img_path = ($row['cus_img'] != null ? (file_exists($file)? $file : null ):null);
    			$record[] = array('id_customer' => $row['id_customer'],'firstname' => ucfirst($row['firstname']),'lastname' => $row['lastname'], 'email' => $row['email'],'id_branch' => $row['id_branch'], 'mobile' =>ucfirst( $row['mobile']), 'cus_img' => $img_path);
    		
			}
		}
		
		return $record;
        		
	}
	
	function getCustomerSchAcc($id_customer)
	{
	    $sql = $this->db->query("SELECT id_scheme_account from scheme_account where id_customer=".$id_customer);
	    if($sql->num_rows() > 0)
	    {
	        return $sql->result_array();
	    }
	    else
	    {
	        return FALSE;
	    }
	}
	
	function insertRemarks($data)
	{
	    $status = $this->db->insert("payment_collection_remarks",$data); 
	    return $status;
	}
	
	
	
	
	  function get_terms_and_conditions()
	{
	    $sql="select * from general";
	    return $this->db->query($sql)->result_array();
	}
	
	
	function get_branchesByEmp($id_employee,$id_customer){
	    
	    $cus =[];
	    $emp = [];
	    $chit = $this->db->query("SELECT cs.branch_settings,cs.is_branchwise_cus_reg FROM chit_settings cs")->row_array();
	    $emp = $this->db->query("SELECT * FROM employee WHERE id_employee = ".$id_employee)->row_array();
	    
	    if($id_customer != '' && $id_customer > 0){
	        $cus = $this->db->query("SELECT * FROM customer WHERE id_customer = ".$id_customer)->row_array();
	    }
	    
	    if($chit['branch_settings'] == 1){
            if($chit['is_branchwise_cus_reg'] ==1){
                //  send customer branch in array : [{id_branch:1, name :coimbatore}]      
                
                $branches = $this->db->query("SELECT b.id_branch,b.name FROM branch b WHERE b.active = 1
                                               ".($cus['id_branch'] != '' && $cus['id_branch'] != 0 && $cus['id_branch'] != null ? 'and b.id_branch = '.$cus['id_branch'] : '')." 
                                                ")->result_array();
            }
            else if($chit['is_branchwise_cus_reg'] == 0 ){
                //send employee branches in array : [{id_branch:1, name :coimbatore},{id_branch:2, name :chennai}]        

                $branches = $this->db->query("SELECT b.id_branch,b.name FROM branch b WHERE b.active = 1
                                               ".($emp['login_branches'] != '' && $emp['login_branches'] != 0 && $emp['login_branches'] != null ? 'and b.id_branch IN ('.$emp['login_branches'].')'  : '')." 
                                                ")->result_array();
            }
        }else{
            // send empty brach array : []
            
            $branches = [];
        }
	    
	    return $branches;
	}
	
	
		function get_customerByID($id_customer)
	{
		$sql="Select
		   c.id_customer,c.firstname,c.lastname,c.date_of_birth,c.id_branch,c.title,
		   a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,
		   c.phone,c.mobile,c.email,c.nominee_name,c.nominee_relationship,c.nominee_mobile,
		   c.cus_img,c.pan,c.pan_proof,c.voterid,c.voterid_proof,c.rationcard,c.rationcard_proof,a.id_country,a.id_city,a.id_state,c.id_employee,
   	c.comments,c.username,c.passwd,c.is_new,c.active,c.`date_add`,c.`date_upd`,cy.name as countryname,s.name as statename,ct.name as cityname
			From
			  customer c
			left join ".self::TAB_ADD." a on(c.id_customer=a.id_customer)
			left join ".self::TAB_CY." cy on (a.id_country=cy.id_country)
			left join ".self::TAB_ST." s on (a.id_state=s.id_state)
			left join ".self::TAB_CT." ct on (a.id_city=ct.id_city)
			where c.id_customer='".$id_customer."'";
			
		$result = $this->db->query($sql);	
	    return $result->row_array();
	}
	
	
	function getActivecardBrands($type)
	{
		$this->db->select('*'); 
		$this->db->where('active',1);
		$this->db->where('card_type',$type);
		$res = $this->db->get('card_brand');
		return $res->result_array();
	}
	
	public function __encrypt($str)
	{
		return base64_encode($str);	
	}
		
		
	public function insert_collectionDevices($dev_arr){
	    
	    $sql = $this->db->insert("employee_devices",$dev_arr);        
		$insertID = $this->db->insert_id();
		
		if($insertID){
			$status = array("msg" => 'Device successfully added...',"status" => true, "data" => $dev_arr);
		}else{
			$status = array("msg" => 'Sorry! Unable to proceed your request...',"status" => false, "data" => []);
		}

		return $status;	
	}	 
	
	function get_metalData(){
	    $sql = $this->db->query("SELECT m.id_metal,m.metal as metal_name FROM metal m
                            left join scheme s on (s.id_metal = m.id_metal)
                            where s.active = 1 and m.metal_status = 1
                            group by m.id_metal");
                            
        if($sql->num_rows() > 0)
	    {
	        return $sql->result_array();
	    }
	}
	
			 
	public function updData($data, $id_field, $id_value, $table)
    {    
    	$edit_flag = 0;
    	$this->db->where($id_field, $id_value);
    	$edit_flag = $this->db->update($table,$data);
    	return ($edit_flag==1?$id_value:0);
    }
    
    
    function insert_sch_enquiry($data)
    {
		$status = $this->db->insert(self::SCH_ENQ,$data);
		if($status)
		{
		    	return array('status' => $status, 'insertID' => $this->db->insert_id(),'message'=>'Enquiry Submitted Successfully');
		}
        else{
            return array('status' => 'false', 'message'=>'Unable to Proceed your Request');
        }	
	}
	
		function get_customerProfile($id_customer)
	{
	    $file = base_url().'admin/assets/img/customer/'.$id_customer.'/customer.png';
        $cus_img = (file_exists('admin/assets/img/customer/'.$id_customer.'/customer.png')?  $file."?nocache=".time() : null );
		$sql="Select
		   c.id_customer,c.title,c.firstname,c.lastname,DATE_FORMAT(c.date_of_birth, '%d-%m-%Y') as date_of_birth,DATE_FORMAT(c.date_of_wed, '%d-%m-%Y') as date_of_wed,
		   a.address1,a.address2,a.address3,ct.name as city,a.pincode,s.name as state,cy.name as country,
		   c.phone,c.mobile,c.email,c.nominee_name,c.nominee_relationship,c.nominee_mobile, 
		   ifnull('".$cus_img."',null)  as cus_img,c.pan,c.pan_proof,c.voterid,c.voterid_proof,c.rationcard,c.rationcard_proof,a.id_country,a.id_city,a.id_state,c.id_employee,
   	       c.comments,c.username,c.is_new,c.active,c.`date_add`,c.`date_upd`,cy.name as countryname,s.name as statename,ct.name as cityname,c.notification
			From
			  customer c
			left join ".self::TAB_ADD." a on(c.id_customer=a.id_customer)
			left join ".self::TAB_CY." cy on (a.id_country=cy.id_country)
			left join ".self::TAB_ST." s on (a.id_state=s.id_state)
			left join ".self::TAB_CT." ct on (a.id_city=ct.id_city)
			where c.id_customer='".$id_customer."'";
			$result = $this->db->query($sql)->row_array();
		$result['reg_custom_fields'] = $this->config->item('custom_fields');
		return $result;
	}
	
	 function get_metalrate_by_branch($id_branch,$id_metal,$id_purity)
    {
         $today = date('Y-m-d H:i:s');
        
        $rate_field = '';
        if($id_purity > 0){
            $rf_sql=$this->db->query("SELECT rate_field,market_rate_field FROM `ret_metal_purity_rate` where id_metal=".$id_metal." and id_purity=".$id_purity."");
            if($rf_sql->num_rows() > 0 ){
                $rate_field =  $rf_sql->row("rate_field");
            }
        }
        
        if(!empty($rate_field) && $id_purity > 0 ){
            $rate_field =  $rf_sql->row("rate_field");
        }else if($id_metal > 0){
            $rate_field = ($id_metal == 2  ? "silverrate_1gm" : "goldrate_22ct");
        } 
     //   print_r($rate_field);exit;
        
        if($rate_field !=''){
            $data=$this->get_settings();
            // print_r($data);exit;
    		if($data['is_branchwise_rate'] == 1 && $id_branch != '' && $id_branch != NULL)
    		{
    			$sql="select ".$rate_field." from metal_rates m
    	   		left join branch_rate br on m.id_metalrates=br.id_metalrate 
    	   		where br.id_branch=".$id_branch." order by  br.id_metalrate desc limit 1";
    		    //echo $sql;exit;
    		}
    		else if($data['is_branchwise_rate']==1)
    		{
    			$sql="select ".$rate_field." from metal_rates 
    			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates 
    			where br.status=1";
    		}
    		else
    		{
    			$sql="select ".$rate_field." from metal_rates 
    			left join branch_rate br on br.id_metalrate=metal_rates.id_metalrates order by id_metalrates desc limit 1";
    		}
    		$result = $this->db->query($sql);	//echo $sql;exit;
    		if($result->num_rows() > 0){
               return $result->row($rate_field);
            }else{
               return 0;
            }
        }else{
             return 0;
        }
    
    }
    
    	function get_settings()
	{   
	    $result = array();
		$this->db->select('allow_notification,delete_unpaid,reg_existing,show_closed_list,branch_settings,is_branchwise_rate,regExistingReqOtp');
		$result= $this->db->get('chit_settings');
		return $result->row_array();
	}
	
}