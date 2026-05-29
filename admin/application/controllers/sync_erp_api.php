<?php
require(APPPATH.'libraries/REST_Controller.php');
class Sync_erp_api extends REST_Controller
{
	const MOD_API = "sync_erpapi_model";
	
	function __construct()
	{
		parent::__construct();
		$this->load->model(self::MOD_API);
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->log_dir = '..log/'.date("Y-m-d");
    	if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0777, TRUE); 
        }		
	}
	
	
	   
   //check valid login
	function checklogin($data)
	{ 
		$access = $this->config->item('rest_valid_logins');
		$key = $access[$data['username']];
		if($key)
		{
			return ($data['passwd'] == $key ? TRUE : FALSE);
		}	
		else
		{
			return FALSE;
		}	
		
	}
	
	//validation functions
	
	//to validate empty value
	function isNullValue($data){
    return (!isset($data) || trim($data)==='');
    }
	
	//To check array contains empty value
	function isArrayEmpty($data)
	{ $errors='';
	  $status=FALSE; 
		foreach($data as $key=>$value) 
		{
			if($this->isNullValue($value))
			{
				$errors.=  " ".$key." contains null or empty value." ;
				$status = TRUE;
			}	
		}
    return array('status' => $status,'errors' => $errors);
    }
	
	//To validate date
	function validateDate($date)
   {
	  
		if(preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches)) 
	  {
	   if(checkdate($matches[2], $matches[3], $matches[1]))
		{ 
		 return true;
		}
	  }
   }
   
   //To check status field
   function isValidStatus($value)
   {
	   $len = strlen($value);
	   if( is_string($value) && $len == 1 )
	   {
	   	  if($value == 'Y' || $value == 'N')
		   return TRUE;
	   }   
   }
   
   //validate transaction  and registration data while update
   
    function validateRecords($data)
	{  
	  $error=NULL;
	  $status = TRUE;
       
		  $isEmpty = $this->isArrayEmpty($data);
			if(!$isEmpty['status'])
			{
			    $status = TRUE;
				 					
			}
			else
			{
				$status = FALSE;
				$error = $isEmpty['errors']; 
			}	
	   
        
       return array('status' => $status,'error' => $error);		
	}    
	// validate payment record
    function validatePayRec($data)
	{  
	  $error=NULL;
	  $status = TRUE;
       
		  $isEmpty = $this->isArrayEmpty($data);
			if(!$isEmpty['status'])
			{
			   if(!$this->validateDate($data['payment_date']))
				{
					$status = FALSE;
					$error.= "payment_date has invalid date format ".$data['payment_date'].", valid date format (YYYY-MM-DD). "; 
				}
			   	if($data['ref_no'] > 0)
				{
					$status = FALSE;
					$error.= "ref_no has invalid value ".$data['ref_no']; 
				}			 					
			}
			else
			{
				$status = FALSE;
				$error = $isEmpty['errors']; 
			}	
	   
        
       return array('status' => $status,'error' => $error);		
	}
	function validateRegistrations($data)
	{  
	  $error=NULL;
	  $status = TRUE;
       
		  $isEmpty = $this->isArrayEmpty($data);
		
			if(!$isEmpty['status'])
			{
               
			   if(!preg_match('/^\d{10}$/',$data['mobile']))
				{
					$status = FALSE;
					$error = "Invalid mobile number ".$data['mobile']; 
				}
				if(!$this->validateDate($data['start_date']))
				{
					$status = FALSE;
					$error.= "start_date has invalid date format ".$data['start_date'].", valid date format (YYYY-MM-DD). "; 
				}
				if(!$this->validateDate($data['maturity_date']))
				{
					$status = FALSE;
					$error.= "maturity_date has invalid date format ".$data['maturity_date'].", valid date format (YYYY-MM-DD). "; 
				}
				 					
			}
			else
			{
				$status = FALSE;
				$error = $isEmpty['errors']; 
			}	
	   
        
       return array('status' => $status,'error' => $error);		
	}
	
	function findBranchId($array,$searchVal) {
        foreach ($array as $k => $val) { 
            if ($val['warehouse'] == $searchVal || $val['expo_warehouse'] == $searchVal) {
               return $k;
            }
        }
        return -1;
    }
	
	//To get insert new scheme account
	public function insertCusSchemes_post()
	{
		$registrations = json_decode(file_get_contents('php://input'));
		//$offlinePayments = (object) $_POST;
		$login = $registrations->login; 
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
		
		if($this->checklogin($key))
		{		   
			$model = self::MOD_API;
			$customers = $registrations->data;
			$records = 0;
			$invalid = 0;
			if($customers)
			{
				$total_records = count($customers);
				$branches = $this->$model->getAllBranch();
				foreach($customers as $cus)
				{			
					$br_key = $this->findBranchId($branches,$cus->warehouse);
					if($br_key >= 0){
			            $branch = $branches[$br_key]['id_branch'];			            
			            $maturity_date= date("Y-m-d", strtotime($cus->maturity_date));
		                $start_date = date("Y-m-d", strtotime($cus->start_date));
		                if($cus->fixed_rate_on != ''){
							$fixed_rate_on = date("Y-m-d", strtotime($cus->fixed_rate_on));
						}else{
							$fixed_rate_on = NULL;
						}
		                
					    $data = array(	
	                				"record_to" 	 =>  2, //1 - offline , 2 - online
	                				'transfer_date'	 => date('Y-m-d'),
	                				"date_add"       => date('Y-m-d H:i:s'),
	                				"clientid"       => $cus->SchemeAccountNo,
	                				"cus_ref_no"     => $cus->cus_acno,
	                				"sync_scheme_code" => $cus->SchemeCode, 
	                                "reg_date"		 => $start_date,
	                                "maturity_date"	 => $maturity_date,
									"ac_name"		 =>	$cus->account_name,
									"firstname"		 =>	$cus->firstname,
									"lastname"		 =>	$cus->lastname,
									"mobile"		 =>	$cus->mobile,
	                                "account_name"   => $cus->account_name,
	                                "group_code"     => $cus->SchemeCode,
	                                "scheme_ac_no"   => $cus->SchemeAccountNo,
	                                "ref_no"         => $cus->ref_no,
	                                "is_transferred" => 'N',
	                				"is_modified"    => 0,
	                                "id_branch" 	 => ($branch == 0 ? NULL:$branch),
	                                "warehouse" 		  =>  $cus->warehouse,
	                                "rate_fixed_in"       =>  ($cus->fixed_wgt == NULL || $cus->fixed_wgt == '' ? NULL : $cus->FixRequestFrom),
	                                "fixed_rate_on" 	  =>  $fixed_rate_on,
	                                "fixed_wgt" 		  =>  $cus->fixed_wgt,
	                                "fixed_metal_rate" 	  =>  $cus->fixed_metal_rate,
	                                "firstPayment_amt" 	  =>  $cus->payable,  // First installment payable
	                                "one_time_premium" 	  =>  $cus->onetimepremium,
	                                "is_online_cus" 	  =>  ($cus->app_flag == 3 ? 1 : 0),
									/*"custom_entry_date"=>  (isset($cus->custom_entry_date) ? $cus->custom_entry_date : NULL), 
	                				"salutation"     => $cus->salutation, 
									"email"		 	 =>	$cus->email,
									"address1"	 	 =>	$cus->address1,
									"address2"		 =>	$cus->address2,
									"address3"	 	 =>	$cus->address3,
									"city"			 =>	$cus->city,
									"state"			 =>	$cus->state,
									"pincode"		 =>	$cus->pincode,
									"phone"			 =>	$cus->phone, 
									"dt_of_birth"	 =>	$cus->dt_of_birth,
									"wed_date"		 =>	$cus->wed_date,
	                                "new_customer"   => 'N', 
	                                "is_closed"         => $cus->is_closed,
	                                "closed_by"         => $cus->closed_by,
	                                "closing_date"      => $cus->closing_date,
	                                "closing_amount"    => $cus->closing_amount,
	                                "closing_weight"    => $cus->closing_weight,
	                                "closing_add_chgs"  => $cus->closing_add_chgs,
	                                "additional_benefits"=> $cus->additional_benefits,
	                                "remark_close"      => $cus->remark_close,*/
	                    			);
						
					  //print_r($data);exit; 
						    $check = array( 'ref_no' 	   => $data['ref_no'],
										'cus_acno' 	       => $data['cus_ref_no'],
										'payable' 	       => $data['firstPayment_amt'],
										'mobile' 	       => $data['mobile'],
										'start_date'       => $data['reg_date'],
										'maturity_date'    => $data['maturity_date'],
										'firstname'        => $data['firstname'],
										'onetimepremium'   => $data['one_time_premium'], 
										'SchemeCode' 	   => $data['group_code'], 
										'SchemeAccountNo'  => $data['scheme_ac_no']);
										
						if($this->config->item('no_branch') != 1){
						    $check['id_branch'] 	= $branch;
						}
												
					    $valid =$this->validateRegistrations($check); 
						
						if($valid['status'])
						{	
							$isRefExists = $this->$model->checkref($data['ref_no'],'customer_reg');	
							$isClientidExist = $this->$model->checkCusRegClientId($data['clientid']);
							if(!$isRefExists)
							{
							    if(!$isClientidExist){
							        $status  = $this->$model->insertData($data,'customer_reg');	
	    						   if($status ===FALSE )	
	    						   {
	    							    $r[] = array('SchemeAccountNo' 	=> $data['scheme_ac_no'],
	    											 'isSucceeded'		=> FALSE,
	    											 "err"              => $this->db->_error_message(),
	    											 "q"              => $this->db->last_query(),
	    											 'result'			=> 'Error in inserting record,check post data');	
	    						   }
	                               else
	                               {							   
	    							   $r[] = array('SchemeAccountNo' 	=> $data['scheme_ac_no'],
	    											 'isSucceeded'		=> TRUE,
	    											 'result' 			=> 'Inserted successfully');	
	    						   }
							    }
							    else
	                            {							   
								   $r[] = array('SchemeAccountNo' 	=> $data['scheme_ac_no'],
												 'isSucceeded'		=> FALSE,
												 'result' 			=> 'SchemeAccountNo already exist');	
							    }							   	
							}
							else
	                        {							   
								   $r[] = array('ref_no' 	=> $data['ref_no'],
												 'isSucceeded'		=> FALSE,
												 'result' 			=> 'ref_no already exist');	
							}	
						}
	                    else
	                    {	                    
							$r[] = array('SchemeAccountNo' 	=> $data['scheme_ac_no'],
										 'isSucceeded' 		=> FALSE,
										 "result" 			=> $valid['error']);
						}
			        }else{
						$r[] = array('SchemeAccountNo' 	=> $cus->SchemeAccountNo,
									 'warehouse' 		=> $cus->warehouse,
									 'isSucceeded'		=> FALSE,
									 'result' 			=> 'Warehouse not available');	
					}	                
                }
				if (!is_dir($this->log_dir.'/jewelone')) {
		            mkdir($this->log_dir.'/jewelone', 0777, true);
		        }
		        $log_path = $this->log_dir.'/jewelone/erpToLmx_InsCus.txt';
				$data = "\n".date('Y-m-d H:i:s')." \n Payload : ".json_encode($customers,true)." \n Response : ".json_encode($r,true);
				file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX);
				
				$this->response($r,200);
			}							
			else
			{
    			$msg[] = array('message'=>'No records to proceed the requested operation!',"responseData" => 0);
				$this->response($msg, 200); 
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}	
	}   
	
	// To update multiple customer scheme account (bulk)	
	public function updateCusSchemes_post()
	{
		$trans_data = json_decode(file_get_contents('php://input'));
	    $login =  $trans_data->login; 
	    $updType = ""; 
        
        $transactions = $trans_data->data;
		
		$key = array('username' => $trans_data->login->username,
		              'passwd' => $trans_data->login->passwd); 					  
		if($this->checklogin($key))
		{	
			$model = self::MOD_API;
			$records = 0;
			
			if($transactions)
			{
				$records = count($transactions);
				foreach($transactions as $tran)
				{	 
				  $updType = $tran->type;
				  $valid = array();
				  // To update customer scheme account data
				  if($tran->type == "c"){ // Update closing data
				  
					if($tran->closing_date != ''){
						$closing_date = date("Y-m-d H:i:s", strtotime($tran->closing_date));
					}else{
						$closing_date = NULL;
					}
	                  $trans = array( 	'is_transferred'    => 'N',
	                					'is_modified'	    => 1,
	                					"date_update"		=> date('Y-m-d H:i:s'),
	                					'record_to'		    => 2,  // 1 - Offline , 2 - Online
	                					'clientid'	        => $tran->SchemeAccountNo, 
	                					'scheme_ac_no'      => $tran->SchemeAccountNo,
	                					'is_closed'         => $tran->is_closed,
	                					'closed_by'         => 0, //ERP Not having this
	                					'closing_date'      => $closing_date,
	                					'closing_amount'    => $tran->closing_amount,
	                					'closing_weight'    => $tran->closing_weight,
	                					/* ERP Not having this
	                					'closing_add_chgs'  => $tran->closing_add_chgs,
	                					'additional_benefits'=> $tran->additional_benefits,
	                					'remark_close'      => $tran->remark_close,*/
	                					'transfer_date'	    => date('Y-m-d')
	                					);
	                   $chkdata = array('is_transferred'    => 'N',
	                					'is_modified'	    => 1, 
	                					'SchemeAccountNo'   => $tran->SchemeAccountNo,
	                					'is_closed'         => $tran->is_closed,
	                					'closing_amount'    => $tran->closing_amount,
	                					'closing_weight'    => $tran->closing_weight,
	                					'transfer_date'	    => date('Y-m-d')
	                					); 
				  }
				  else if($tran->type == "f"){ // Update fixing data 
				   if($tran->fixed_rate_on != ''){
						$fixed_rate_on = date("Y-m-d", strtotime($tran->fixed_rate_on));
					}else{
						$fixed_rate_on = NULL;
					}
					$trans = array( 	
								'is_transferred'    => 'N',
								'is_modified'	    => 1,
								"date_update"		=> date('Y-m-d H:i:s'),
								'record_to'		    => 2,  // 1 - Offline , 2 - Online
								'clientid'	        => $tran->SchemeAccountNo, 
								'scheme_ac_no'      => $tran->SchemeAccountNo,
								'rate_fixed_in'     => $tran->FixRequestFrom,
								'fixed_rate_on'     => $fixed_rate_on,
								'fixed_wgt'      	=> $tran->fixed_wgt,
								'fixed_metal_rate'  => $tran->fixed_metal_rate,
								'transfer_date'	    => date('Y-m-d')
								);
					$chkdata = array(
								'is_transferred'    => 'N',
								'is_modified'	    => 1, 
								'rate_fixed_in'     => $tran->FixRequestFrom,
								'SchemeAccountNo'   => $tran->SchemeAccountNo,
								'fixed_rate_on'     => $tran->fixed_rate_on,
								'fixed_wgt'    		=> $tran->fixed_wgt,
								'fixed_metal_rate'  => $tran->fixed_metal_rate,
								'transfer_date'	    => date('Y-m-d')
								);
				  }
		          
		          $valid = $this->validateRecords($chkdata);
					if($valid['status'])
					{
						$status = $this->$model->updateCusRegData($trans); 
						if($status === FALSE)
						{
							$r[] = array('SchemeAccountNo' 	=> $trans['scheme_ac_no'],
										 'isSucceeded' 		=> FALSE, 
										  //"err"              => $this->db->_error_message(),
	    								 //  "q"              => $this->db->last_query(),
										 'result' 			=> 'Error in update.Kindly check data.');
						}         
						else
						{
							$r[] = array('SchemeAccountNo'=> $trans['scheme_ac_no'],
										 'isSucceeded'    => TRUE,
										 'result' 		  => 'Updated Successfully');
						} 		
					}
					else
					{
						$r[] = array(	'SchemeAccountNo'=> $tran->SchemeAccountNo,
										'isSucceeded' 	 => FALSE,
										"result" 		 =>$valid['error']
										);
					} 
				}
				if (!is_dir($this->log_dir.'/jewelone')) {
		            mkdir($this->log_dir.'/jewelone', 0777, true);
		        }
		        $log_path = $this->log_dir.'/jewelone/erpToLmx_UpdAcc.txt';
				$data = "\n".date('Y-m-d H:i:s')." \n Payload : ".json_encode($transactions,true)." \n Response : ".json_encode($r,true);
				file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX);
				$this->response($r,200);
			}	
			else
			{
			    $msg[] = array('message' => 'No records to proceed the requested operation!',"responseData" => 0);
				$this->response($msg, 200); 
			}
		
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}			
	}
	
	public function insertPayments_post()
	{
	
		$transactions = json_decode(file_get_contents('php://input'));
		//$offlinePayments = (object) $_POST;
		$login = $transactions->login; 		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
		
		if($this->checklogin($key))
		{		   
			$model = self::MOD_API;
				
			$payments = $transactions->data;
			$records = 0;
			$invalid = 0;
			$allow_insert=true;
			if($payments)
			{
				$total_records = count($payments);
				$branches = $this->$model->getAllBranch();	
				
				foreach($payments as $transaction)
				{	
					$br_key = $this->findBranchId($branches,$transaction->warehouse);
					if($br_key >= 0){
			            $branch = $branches[$br_key]['id_branch'];
			            if($transaction->payment_date != ''){
							$payment_date = date("Y-m-d", strtotime($transaction->payment_date));
							$due_type = ( ($transaction->due_month.'-'.$transaction->due_year) ==  date("m-Y", strtotime($transaction->payment_date)) ?  'ND' : 'AD' ); 
						}else{
							$payment_date = NULL;
							$due_type = 'ND';
						}
					
						$data = array(	
	                    				"record_to" 	 =>  2, //1 - offline , 2 - online
	                    				'transfer_date'	 => date('Y-m-d'),
	                    				"date_add"       => date('Y-m-d H:i:s'),
	                    				"payment_date" 	 =>  $payment_date,
	                    				"client_id" 	 =>  $transaction->SchemeAccountNo,
	                    				"amount" 		 =>  $transaction->amount,
	                    				"weight" 		 =>  $transaction->weight,
	                    				"rate" 			 => $transaction->rate,
	                    				"metal" 		 => 1,
	                    				"payment_mode" 	 => $transaction->payment_mode,
	                    				"payment_status" => $transaction->payment_status,
	                    				"discountAmt"    => $transaction->discountAmt,
	                    				"receipt_no"     => $transaction->receipt_no,
	                    				"id_branch" 	 => ($branch == 0 ? NULL:$branch),
	                    				"warehouse" 	 => $transaction->warehouse,
	                    				"ref_no" 		 => "OF-".$transaction->ref_no,
	                    				"is_transferred" => 'N',
	                    				"is_modified"    => 0,
	                    				"payment_type" 	 => 2, // offline  
	                    				"remarks" 		 => $transaction->remarks,
	                    				"due_type" 	     => $due_type,
	                    				"due_month" 	 => $transaction->due_month,
	                    				"due_year" 		 => $transaction->due_year,
	                    				/*"bank_name" 	 => $transaction->bank_name,
	                    				"branch_name" 	 => $transaction->branch_name, // bank branch name
	                    				"card_no" 		 => $transaction->card_no,
	                    				"installment_no" => $transaction->installment_no,
	                    				"new_customer" => 'N'	
	                    				"gst" 			 => $transaction->gst,
	                    				"gst_type" 		 => $transaction->gst_type,
	                    				"custom_entry_date"=>  (isset($transaction->custom_entry_date) ? $transaction->custom_entry_date : NULL), */
	                    								
	                    			);
						if($this->config->item('no_branch') == 1){
						    $check = array( 'ref_no' 		=> $data['ref_no'],
										'amount' 	        => $data['amount'],
										'SchemeAccountNo' 	=> $data['client_id'],
										'payment_date' 	 	=> $data['payment_date']);
						}else{
						    $check = array( 'warehouse' 	=> $data['warehouse'],
						                'SchemeAccountNo' 	=> $data['client_id'],
						                'due_month' 	 	=> $data['due_month'],
						                'due_year' 	 	    => $data['due_year'],
										'ref_no' 			=> $data['ref_no'],
										'amount' 	        => $data['amount'],
										'payment_date' 	 	=> $data['payment_date']);
						}
						
										
					   $valid = $this->validatePayRec($check); 
						
						if($valid['status'])
						{	
							$isRefExists = $this->$model->checkref($data['ref_no'],'transaction');	
						
							if(!$isRefExists)
							{
								/*if($transaction->no_of_ins > 1){
									for($i = 1; $i <= $transaction->no_of_ins ; $i++){
										if($i == 1){
											$data['due_type'] = 'ND';
											$data['amount'] = $transaction->amount/$transaction->no_of_ins; 
										}else{
											$data['due_type'] = 'AD';
											$data['amount'] = $transaction->amount/$transaction->no_of_ins; 
										}										
										$status  = $this->$model->insertData($data,'transaction');
									}
								}else{
									$data['due_type'] = 'ND'; 
									$status  = $this->$model->insertData($data,'transaction');
								}*/
								
							 	if($transaction->payment_status == 8)
								{
						        	$defaluterExist = $this->$model->isdefaulterExist($data);
						        	
    								if($defaluterExist['status'] == false) // Defaulter not exist
    								{
    								    $allow_insert = true;
    								}
    								else
    								{
    								    $allow_insert = false;
    								}
								} 
            					if($allow_insert == true)
            					{
                                    $status  = $this->$model->insertData($data,'transaction');
                                    if($status ===FALSE )	
                                    {
                                        $r[] = array('SchemeAccountNo'  => $data['client_id'],
                                                     'ref_no'           => $transaction->ref_no,
                                    				 'payment_date' 	=> $data['payment_date'],
                                    				 'receipt_no' 		=> $data['receipt_no'],
                                    				 'isSucceeded'		=> FALSE,
                                    				// "err"              => $this->db->_error_message(),
                                    				// "q"                => $this->db->last_query(),
                                    				 'result'			=> 'Error in inserting payment.Check post data.');	
                                    }
                                    else
                                    {							   
                                       $r[] = array('SchemeAccountNo' 	=> $data['client_id'],
                                                    'ref_no'           => $transaction->ref_no,
                                    				 'payment_date' 	=> $data['payment_date'],
                                    				 'receipt_no' 		=> $data['receipt_no'],
                                    				 'isSucceeded'		=> TRUE,
                                    				 'result' 			=> 'Inserted successfully');	
                                    }
            					}
            					else
            					{
            					      $r[] = array('SchemeAccountNo'	=> $data['client_id'],
            					                    'ref_no'           => $transaction->ref_no,
    												 'payment_date' 	=> $data['payment_date'],
    												 'receipt_no' 		=> $data['receipt_no'],		
    												 'isSucceeded'		=> TRUE,
    												 'result' 			=> 'Defaulter Already Exists');	
            					}
							}
							else
	                        {							   
								   $r[] = array('SchemeAccountNo'	=> $data['client_id'],
								                'ref_no'           => $transaction->ref_no,
												 'payment_date' 	=> $data['payment_date'],
												 'receipt_no' 		=> $data['receipt_no'],		
												 'isSucceeded'		=> FALSE,
												 'result' 			=> 'ref_no already exist');	
							}	
						}
	                    else
	                    {
	                    
							$r[] = array('SchemeAccountNo'	=> $data['client_id'],
							             'ref_no'           => $transaction->ref_no,
										 'payment_date' 	=> $data['payment_date'],
										 'receipt_no' 		=> $data['receipt_no'],
										 'isSucceeded' 		=> FALSE,
										 "result" 			=> $valid['error']);
						}
			        }else{
						
					}
                }
				if (!is_dir($this->log_dir.'/jewelone')) {
		            mkdir($this->log_dir.'/jewelone', 0777, true);
		        }
		        $log_path = $this->log_dir.'/jewelone/erpToLmx_InsPay.txt';
				$data = "\n".date('Y-m-d H:i:s')." \n Payload : ".json_encode($payments,true)." \n Response : ".json_encode($r,true);
				file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX);
				$this->response($r,200);
			}	
						
			else
			{
			    $msg[] = array('message' => 'No records to proceed the requested operation!',"responseData" => 0);
				$this->response($msg, 200); 
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}		
	} 
	
	public function cancelPayments_post()
	{
	
		$transactions = json_decode(file_get_contents('php://input'));
		//$offlinePayments = (object) $_POST;
		$login = $transactions->login; 		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
		
		if($this->checklogin($key))
		{		   
			$model = self::MOD_API;
				
			$payments = $transactions->data;
			$records = 0;
			$invalid = 0;
			if($payments)
			{
				$total_records = count($payments);
				$branches = $this->$model->getAllBranch();	
				
				foreach($payments as $transaction)
				{	
				    $data=array(
				                'payment_status' => 4,
				                'record_to'      => 2,
				                'is_transferred' => 'N',
			                    'is_modified'    => 1,
            					"date_upd"		 => date('Y-m-d'),
            					'transfer_date'	 => date('Y-m-d'),
            					'remarks'        =>'Reversal receipt no'.$transaction->receipt_no
				                );
				    $transcation_table=$this->$model->update_cancel_Payment($transaction->ref_no,$transaction->warehouse,$data);
				    if($transcation_table['status']==true)
				    {
				        $msg[] = array('isSucceeded'=>TRUE,'message' =>  $transcation_table['msg'],'receipt_no'=>$transaction->receipt_no);
				    }
				    else
				    {
				        $msg[] = array('isSucceeded'=>FALSE,'message' => $transcation_table['msg'],'receipt_no'=>$transaction->receipt_no);
				    }
                }
                if (!is_dir($this->log_dir.'/jewelone')) {
		            mkdir($this->log_dir.'/jewelone', 0777, true);
		        }
		        $log_path = $this->log_dir.'/jewelone/erpToLmx_cancelPay.txt';
				$data = "\n".date('Y-m-d H:i:s')." \n Payload : ".json_encode($payments,true)." \n Response : ".json_encode($msg,true);
				file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX);
				
				  $this->response($msg,200);
			}	
						
			else
			{
			    $msg[] = array('message' => 'No records to proceed the requested operation!',"responseData" => 0);
				$this->response($msg, 200); 
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}		
	} 
	
}
?>