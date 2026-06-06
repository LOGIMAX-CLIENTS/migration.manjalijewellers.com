<?php
require(APPPATH.'libraries/REST_Controller.php');
class sync_api extends REST_Controller
{
	const MOD_API = "syncapi_model";
	
	function __construct()
	{
		parent::__construct();
		$this->load->model(self::MOD_API);
		ini_set('date.timezone', 'Asia/Calcutta');
				
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
			   if(!$this->validateDate($data['transfer_date']))
				{
					$status = FALSE;
					$error.= "transfer_date has invalid date format ".$data['transfer_date'].", valid date format (YYYY-MM-DD). "; 
				}
			   
			   if(!$this->isValidStatus($data['is_transferred']))
				{
					$status = FALSE;
					$error.= "is_transferred has invalid value ".$data['is_transferred'].". "; 
				}
				 					
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
				 					
			}
			else
			{
				$status = FALSE;
				$error = $isEmpty['errors']; 
			}	
	   
        
       return array('status' => $status,'error' => $error);		
	}
	
	
	//To get transactions by status
	public function transactionsByStatus_post()
	{
		$credentials = json_decode(file_get_contents('php://input'));
		$login = $credentials->login;
		
		$branch = $login->id_branch;
	/*	$branches = $this->config->item('id_branch');
		foreach($branches as $key => $value){
			if($key == $login->id_branch){
				$branch = $value;
			}			
		}	*/		
	
		$key = array('username' => $login->username,
		              'passwd' => $login->passwd); 
		              
		if($this->checklogin($key))
		{		
			$status = $login->status;
			$model = self::MOD_API;
			$record_to = 1;
			$transactions = $this->$model->getTransactionByTranStatus($status,$branch,$record_to);
			foreach($transactions as $transaction)
			{
				$customerPaidAmount=($transaction["amount"]-$transaction["discountAmt"]);

				$trans[]= array(	"id_transaction" => (int) $transaction["id_transaction"],
				                    "branch_code" 	 => (string) $transaction["branch_code"],
				                    "record_to" 	 => (string) $transaction["record_to"],
				                    "custom_entry_date" => (string) $transaction["custom_entry_date"],
									"payment_date" 	 => (string) $transaction["payment_date"],
									"client_id" 	 => (string)$transaction["client_id"],    //new scheme clientid paased Based on the settings//HH
									"id_scheme_account" => (int)$transaction["id_scheme_account"],
									"receipt_no"        => (string) $transaction["receipt_no"],      // Receipt no passed Based on the settings//
									//"group_code" 	 => (string)$transaction["group_code" ],
								//	"scheme_ac_no" 	 => (string)$transaction["scheme_ac_no" ],
									//"scheme_code" 	 => (string)$transaction["scheme_code" ],
									"amount" 		 => number_format((float)$transaction["amount"], 2, '.', ''),
									"weight" 		 => number_format((float)$transaction["weight"], 3, '.', '') ,
									"rate" 			 => number_format((float)$transaction["rate"], 3, '.', '') ,
									"metal" 		 => (int)  $transaction["metal"],
									"paid_through"   => (string)$transaction["paid_through"],
									"payment_mode" 	 => (string)$transaction["payment_mode"],
									"payment_type" 	 => (string)$transaction["payment_type"],
									"payment_status" => (string)$transaction["payment_status"],
									"due_type"       => (string)$transaction["due_type"],
									"bank_name" 	 => (string)$transaction["bank_name"],
									"branch_name" 	 => (string)$transaction["branch_name"], // bank branch name
									"card_no" 		 => (string)$transaction["card_no" ],
									"installment_no" => (string)$transaction["installment_no"],
                                    /*"draweeAcNo"     => (string)$transaction["drawee_ac_no"],
                                    "draweeBank"     => (string)$transaction["drawee_bank"], 
                                    "draweeIFSC"     => (string)$transaction["drawee_ifsc"],*/
                                    "drawee_ac_no"   => (string)$transaction["drawee_ac_no"],
                                    "drawee_bank"    => (string)$transaction["drawee_bank"], 
                                    "drawee_ifsc"    => (string)$transaction["drawee_ifsc"],
                                    "drawee_ac_name" => (string)$transaction["drawee_ac_name"],
                                    "drawee_ac_branch" => (string)$transaction["drawee_ac_branch"],
									"id_branch" 	 => (string)$transaction["id_branch"],
									"ref_no" 		 => (int)$transaction["ref_no"],
									"payment_ref_number" => (string)$transaction["payment_ref_number"],
									"is_transferred" => (string) $transaction["is_transferred" ],
									"is_modified"    => (int) $transaction["is_modified" ],
									"transfer_date"  => (string)$transaction["transfer_date"],
									"discountAmt"  => number_format((float)$transaction["discountAmt"], 2, '.', ''),
									"CustomerPaidAmount"  => number_format((float)$customerPaidAmount, 2, '.', ''),
									"gst" 			 => number_format((float)$transaction["gst"], 2, '.', '') ,
									"gst_type" 		 => (int) $transaction["gst_type" ],
									"new_customer" 	 => (string)$transaction["new_customer"],
									"emp_code" 	 	 => (string)$transaction["emp_code"],
									"responseData" => 1  // for sync reference
									);
									//print_r($trans);exit;
			}
			
			if($trans)
			{
			    $this->response($trans, 200);
			}	 
			else
			{
				$msg[] = array('message' => 'No transaction data!','responseData'=>0); // 'status'=>0 - to indicate response has no data
				$this->response($msg, 200); 
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}	
	}	
	
	
	//To update multiple transactions (bulk)
	
	public function updateTransactions_post()
	{
		$trans_data = json_decode(file_get_contents('php://input'));
	    $login =  $trans_data->login; 
	    $updType = "";
	    $branch = $trans_data->login->id_branch;
        
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
			      if($updType == 'R'){
			          /* To update transaction as read */
			          $trans = array( 	'ref_no'	 	 => $tran->refPaymentNo,
                    					'is_transferred' => $tran->is_transferred,
                    					"date_upd"		    => $tran->transfer_date,
                    					'transfer_date'	 => $tran->transfer_date);
                      $valid = $this->validateRecords($trans);
			      }
			      else if($updType == 'M'){
			          /* To update transaction receipt */
                      $trans = array( 	'ref_no'	 	 => $tran->refPaymentNo,
                    					'is_transferred' => 'N',
                    					'client_id'      => $tran->client_id,
                    					"date_upd"		    => $tran->transfer_date,
                    					'is_modified'	 => 1,
                    					'record_to'		 => 2,  // 1 - Offline , 2 - Online
                    					'receipt_no'	 => $tran->receipt_no,
                    					'payment_status' => $tran->payment_status,
                    					'transfer_date'	 => $tran->transfer_date);
                    					
                      $valid = $this->validateRecords($trans);
			      }
			      else{
			         $valid['status'] = false ; 
			         $valid['error'] = "Invalid type";
			      }

					
					
						if($valid['status'])
						{
								$status = $this->$model->updateData($trans,$branch,'transaction');
						 
								 if($status ===FALSE)
								{
									$r[] = array('refPaymentNo' 	=> $trans['ref_no'],
												 'isSucceeded' 		=> FALSE,
												 "responseData" => 1,  // for sync reference
												 'result' 			=> 'Unable to proceed the requested operation');
									//$this->response($r,200);
									
								}         
								else
								{
									$r[] = array('refPaymentNo'   => $trans['ref_no'],
												 'isSucceeded'    => TRUE,
												 "responseData" => 1,  // for sync reference
												// 'query'         => $this->db->last_query(),
												 'result' 		  => 'Updated Successfully');								
									//$this->response('', 404);
								} 		
						}
						else
						{
							$r[] = array(	'refPaymentNo'   => $tran->refPaymentNo,
											'isSucceeded' 	 => FALSE,
											"responseData" => 1,  // for sync reference
											"result" 		 =>$valid['error']);
							//$this->response($r,401);
						} 
				}
				$this->response($r,200);
			}	
			else
			{
			    $msg[] = array('message' => 'No data to proceed the requested operation!','responseData' => 0);
				$this->response($msg, 200); 
			}
		
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}			
	}
	
	
	
	public function insertTransactions_post()
	{
	
		$transactions = json_decode(file_get_contents('php://input'));
		//$offlinePayments = (object) $_POST;
		$login = $transactions->login;
		$branch = $login->id_branch;		
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
				
				foreach($payments as $transaction)
				{
					$data = array(	
                    				"record_to" 	 =>  2, //1 - offline , 2 - online
                    				'transfer_date'	 => date('Y-m-d'),
                    				"payment_date" 	 =>  $transaction->payment_date,
                    				"custom_entry_date"=>  (isset($transaction->custom_entry_date) ? $transaction->custom_entry_date : NULL), 
                    				"client_id" 	 =>  $transaction->client_id,
                    				"amount" 		 =>  $transaction->amount,
                    				"weight" 		 =>  $transaction->weight,
                    				"rate" 			 =>  $transaction->rate,
                    				"metal" 		 => $transaction->metal,
                    				"payment_mode" 	 => $transaction->payment_mode,
                    				"payment_type" 	 => 2, // offline
                    				"payment_status" => $transaction->payment_status,
                    				"due_type"       => $transaction->due_type,
                    				"bank_name" 	 => $transaction->bank_name,
                    				"branch_name" 	 => $transaction->branch_name, // bank branch name
                    				"card_no" 		 => $transaction->card_no,
                    				"installment_no" => $transaction->installment_no,
                    				"id_branch" 	 => ($branch == 0 ? NULL:$branch),
                    				"branch_code" 	 => $transaction->branch_code,
                    				"ref_no" 		 => $transaction->ref_no,
                    				"payment_ref_number" => $transaction->payment_ref_number,
                    				"is_transferred" => 'N',
                    				"is_modified"    => 0,
                    				"discountAmt"    => $transaction->discountAmt,
                    				"gst" 			 => $transaction->gst,
                    				"gst_type" 		 => $transaction->gst_type,
                    				"receipt_no"     => $transaction->receipt_no,
                    				"days"              => $transaction->days,
                    				"saved_benefits_wgt" => $transaction->saved_benefits,
                    				"saved_benefit_amt" => $transaction->saved_benefit_amt,
                    				"benefit_value"     => $transaction->benefit_value,
                    				"benefit_type"      => $transaction->benefit_type,
                    				"is_digi"           => $transaction->is_digi,
                    				"new_customer" 	 => 'N'					
                    			);
					if($this->config->item('no_branch') == 1){
					    $check = array( 'ref_no' 			=> $data['ref_no'],
									'amount' 	        => $data['amount'],
									'client_id' 	       => $data['client_id'],
									'installment_no'	=> $data['installment_no'],
									'payment_date' 	 	=> $data['payment_date']);
					}else{
					    $check = array( 'id_branch' 	=> $branch,
					                'client_id' 	       => $data['client_id'],
									'ref_no' 			=> $data['ref_no'],
									'amount' 	        => $data['amount'],
									'installment_no'	=> $data['installment_no'],
									'payment_date' 	 	=> $data['payment_date']);
					}
					
									
				   $valid =$this->validatePayRec($check); 
					
					/*if($valid['status'])
					{	
						$isRefExists = $this->$model->checkref($data['ref_no'],'transaction');	
						if(!$isRefExists)
						{
						  $status  = $this->$model->insertData($data,'transaction');	
						   if($status ===FALSE )	
						   {
							    $r[] = array('client_id' 		=> $data['client_id'],
											 'ref_no'			=> $data['ref_no'],
											 'payment_date' 	=> $data['payment_date'],
											 'receipt_no' 		=> $data['receipt_no'],
											 'isSucceeded'		=> FALSE,
											 "responseData" => 1,  // for sync reference
											 'result'			=> 'Unable to proceed the requested operation');	
						   }
                           else
                           {							   
							   $r[] = array('client_id' 		=> $data['client_id'],
											 'ref_no'			=> $data['ref_no'],
											 'payment_date' 	=> $data['payment_date'],
											 'receipt_no' 		=> $data['receipt_no'],
											 'isSucceeded'		=> TRUE,
											 "responseData" => 1,  // for sync reference
											 'result' 			=> 'Inserted successfully');	
						   }	
						}
						else
                        {							   
							   $r[] = array('client_id' 		=> $data['client_id'],
											 'ref_no'			=> $data['ref_no'],
											 'payment_date' 	=> $data['payment_date'],
											 'receipt_no' 		=> $data['receipt_no'],		
											 'isSucceeded'		=> FALSE,
											 "responseData" => 1,  // for sync reference
											 'result' 			=> 'ref_no already exist');	
						}	
					}
                    else
                    {
                    
						$r[] = array('client_id' 		=> $data['client_id'],
									 'ref_no'			=> $data['ref_no'],
									 'payment_date' 	=> $data['payment_date'],
									 'receipt_no' 		=> $data['receipt_no'],
									 'isSucceeded' 		=> FALSE,
									 "responseData" => 1,  // for sync reference
									 "result" 			=> $valid['error']);
					}*/
					
					if($valid['status'])
					{	
						$isRefExists = $this->$model->checkref($data['ref_no'],'transaction');	
						$isValid = true;
						$isPayRecordExist = false;
						if($isRefExists){
					        $isPayRecordExist = $this->$model->isPayRecordExist($data);
						    if($isPayRecordExist){
    							$isValid = false;
						    }else{
						        $isValid = true;
						    }
						}
						if($isValid)
						{
						  $status  = $this->$model->insertData($data,'transaction');	
						   if($status ===FALSE )	
						   {
							    $r[] = array('client_id' 		=> $data['client_id'],
											 'ref_no'			=> $data['ref_no'],
											 'payment_date' 	=> $data['payment_date'],
											 'receipt_no' 		=> $data['receipt_no'],
											 'isSucceeded'		=> FALSE,
											 "responseData" => 1,  // for sync reference
											 "error" => $this->db->_error_message(),
											 'result'			=> 'Unable to proceed the requested operation');	
						   }
                           else
                           {							   
							   $r[] = array('client_id' 		=> $data['client_id'],
											 'ref_no'			=> $data['ref_no'],
											 'payment_date' 	=> $data['payment_date'],
											 'receipt_no' 		=> $data['receipt_no'],
											 'isSucceeded'		=> TRUE,
											 "responseData" => 1,  // for sync reference
											 "insID"            => $this->db->insert_id(),
											 'result' 			=> 'Inserted successfully..');	
						   }	
						}
						else
                        {	
                            if($isPayRecordExist){
                                $r[] = array('client_id' 		=> $data['client_id'],
											 'ref_no'			=> $data['ref_no'],
											 'payment_date' 	=> $data['payment_date'],
											 'receipt_no' 		=> $data['receipt_no'],
											 'isSucceeded'		=> TRUE,
											 "responseData" => 1,  // for sync reference
											 'result' 			=> 'Transaction already inserted');	
                            }else{
							   $r[] = array('client_id' 		=> $data['client_id'],
											 'ref_no'			=> $data['ref_no'],
											 'payment_date' 	=> $data['payment_date'],
											 'receipt_no' 		=> $data['receipt_no'],		
											 'isSucceeded'		=> FALSE,
											 "responseData" => 1,  // for sync reference
											 'result' 			=> 'ref_no already exist');	
                            }
						}	
					}
                    else
                    {
                    
						$r[] = array('client_id' 		=> $data['client_id'],
									 'ref_no'			=> $data['ref_no'],
									 'payment_date' 	=> $data['payment_date'],
									 'receipt_no' 		=> $data['receipt_no'],
									 'isSucceeded' 		=> FALSE,
									 "responseData" => 1,  // for sync reference
									 "result" 			=> $valid['error']);
					}
					
                }
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
	
	//To get customers by status
	public function customersByStatus_post()
	{
		$credentials = json_decode(file_get_contents('php://input'));
		$login = $credentials->login;
		$branch = $login->id_branch;
	
		$key = array('username' => $login->username,
		              'passwd' => $login->passwd); 
		              
		if($this->checklogin($key))
		{		
			$status = $login->status;
			$model = self::MOD_API;
			$record_to = 1;
			$trans = array();
			$customers = $this->$model->getcustomerByStatus($status,$branch,$record_to);
			foreach($customers as $cus)
			{  
				$trans[]= array(	"clientid"       => (string)$cus["clientid"],   // new scheme clientid paased Based on the settings//HH
									"custom_entry_date" => (string) ($cus["custom_entry_date"] != NULL ? date_format(date_create($cus["custom_entry_date"]),"Y-m-d"):NULL),
				                    "sync_scheme_code" => (string)$cus["sync_scheme_code"],
                                    "id_branch"      => (string)$cus["id_branch"],
                                    "branch_code"    => (string)$cus["branch_code"],
                                    "is_modified"    => (string)$cus["is_modified"],
                                    "reg_date"		 => (string) $cus["reg_date"],
									"salutation"     => (string) $cus["salutation"],
									"account_name"	 =>	(string) $cus["ac_name"],
									"firstname"		 =>	(string) $cus["firstname"],
									"lastname"		 =>	(string) $cus["lastname"],
									"address1"		 =>	(string) $cus["address1"],
									"address2"		 =>	(string) $cus["address2"],
									"address3"	 	 =>	(string) $cus["address3"],
									"city"			 =>	(string) $cus["city"],
									"state"			 =>	(string) $cus["state"],
									"country"         =>'India',
									"pincode"		 =>	(string) $cus["pincode"],
									"phone"			 =>	(string) $cus["phone"],
									"mobile"		 =>	(string) $cus["mobile"],
									"email"			 =>	(string) $cus["email"],
									"dt_of_birth"	 =>	(string) $cus["dt_of_birth"],
									"wed_date"		 =>	(string) $cus["wed_date"],
									"ref_no"		 =>	(string) $cus["ref_no"],
                                    "new_customer"   => (string) $cus["new_customer"],
                                    "cus_ref_no"     => (string) $cus["cus_ref_no"],
                                    "id_scheme_account" => (string)$cus["id_scheme_account"],
                                    "account_name"      => (string)$cus["account_name"],
                                    "group_code"        => (string)$cus["group_code"],         // new group code paased Based on the settings//HH
                                    "scheme_ac_no"      => (string)$cus["scheme_ac_no"],         // new scheme acc no paased//
                                    "paid_installments" => (string)$cus["paid_installments"],
                                    "is_closed"         => (string)$cus["is_closed"],
                                    "closed_by"         => (string)$cus["closed_by"],
                                    "closing_date"      => (string)$cus["closing_date"],
                                    "closing_amount"    => number_format((float)$cus["closing_amount"], 2, '.', ''),
                                    "closing_weight"    => number_format((float)$cus["closing_weight"], 3, '.', ''),
                                    "closing_add_chgs"  => number_format((float)$cus["closing_add_chgs"], 2, '.', ''),
                                    "additional_benefits"=> number_format((float)$cus["additional_benefits"], 2, '.', ''),
                                    "remark_close"      => (string)$cus["remark_close"],
                                    "responseData" => 1,  // for sync reference
                                    );
			}
			
			if($trans)
			{ 
				$this->response($trans, 200);
			}	 
			else
			{
			    $msg[] = array('message' => 'No customer registration records found!',"responseData" => 0);
				$this->response($msg, 200); 
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}	
	}
	
	//To update multiple customers (bulk)
	
	public function updateCustomers_post()
	{
		$trans_data = json_decode(file_get_contents('php://input'));
	    $login =  $trans_data->login; 
	    $updType = "";
	    $branch = $trans_data->login->id_branch;
        
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
				{//$this->response($tran,200);
				  
				  $updType = $tran->type;
				  $valid = array();
			      if($updType == 'R'){
			          /* To update customer data as read */
			          $trans = array( 	'ref_no'	 	 => $tran->refPaymentNo,
                    					'is_transferred' => $tran->is_transferred,
                    					"date_update"		 => $tran->transfer_date,
                    					'transfer_date'	 => $tran->transfer_date);
                      $valid = $this->validateRecords($trans);
			      }
			      else if($updType == 'M'){
			          /* To update customer scheme account data */
                      $trans = array( 	'ref_no'	 	    => $tran->refPaymentNo,
                    					'is_transferred'    => 'N',
                    					'is_modified'	    => 1,
                    					"date_update"		    => $tran->transfer_date,
                    					'record_to'		    => 2,  // 1 - Offline , 2 - Online
                    					'clientid'	        => $tran->clientid,
                    					'group_code'        => $tran->group_code,
                    					'scheme_ac_no'      => $tran->scheme_ac_no,
                    					'is_closed'         => $tran->is_closed,
                    					'closed_by'         => $tran->closed_by,
                    					'closing_date'      => $tran->closing_date,
                    					'closing_amount'    => $tran->closing_amount,
                    					'closing_weight'    => $tran->closing_weight,
                    					'closing_add_chgs'  => $tran->closing_add_chgs,
                    					'additional_benefits'=> $tran->additional_benefits,
                    					'remark_close'      => $tran->remark_close,
                    					'transfer_date'	    => $tran->transfer_date);
                       $chkdata = array( 	'ref_no'	 	    => $tran->refPaymentNo,
                    					'is_transferred'    => 'N',
                    					'is_modified'	    => 1,
                    					'clientid'	        => $tran->clientid,
                    					'group_code'        => $tran->group_code,
                    					'scheme_ac_no'      => $tran->scheme_ac_no,
                    					'is_closed'         => $tran->is_closed,
                    					'transfer_date'	    => $tran->transfer_date);
                      $valid = $this->validateRecords($chkdata);
			      }
			      else{
			         $valid['status'] = false ; 
			         $valid['error'] = "Invalid type";
			      }

					
					
						if($valid['status'])
						{
								$status = $this->$model->updateRegData($trans,$branch,'customer_reg');
								 if($status ===FALSE)
								{
									$r[] = array('refPaymentNo' 	=> $trans['ref_no'],
												 'isSucceeded' 		=> FALSE,
												 "responseData" => 1,  // for sync reference
												 'result' 			=> 'Unable to proceed the requested operation');
									//$this->response($r,200);
									
								}         
								else
								{
									$r[] = array('refPaymentNo'   => $trans['ref_no'],
												 'isSucceeded'    => TRUE,
												 "responseData" => 1,  // for sync reference
												 'result' 		  => 'Updated Successfully');								
									//$this->response('', 404);
								} 		
						}
						else
						{
							$r[] = array(	'refPaymentNo'   => $tran->refPaymentNo,
											'isSucceeded' 	 => FALSE,
											"responseData" => 1,  // for sync reference
											"result" 		 =>$valid['error']);
							//$this->response($r,401);
						} 
				}
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
	
	
	//To get insert new scheme account
	public function insertCustomers_post()
	{
		$registrations = json_decode(file_get_contents('php://input'));
		//$offlinePayments = (object) $_POST;
		$login = $registrations->login;
		$branch = $login->id_branch;		
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
				
				foreach($customers as $cus)
				{
				    $data = array(	
                    				"record_to" 	 =>  2, //1 - offline , 2 - online
                    				"custom_entry_date"=>  (isset($cus->custom_entry_date) ? $cus->custom_entry_date : NULL), 
                    				'transfer_date'	 => date('Y-m-d'),
                    				"clientid"       => $cus->clientid,	
                    				"sync_scheme_code" => $cus->sync_scheme_code, 
                                    "reg_date"		 => $cus->reg_date,
									"salutation"     => $cus->salutation, 
									"account_name"	 =>	$cus->account_name,
									"firstname"		 =>	$cus->firstname,
									"lastname"		 =>	$cus->lastname,
									"address1"		 =>	$cus->address1,
									"address2"		 =>	$cus->address2,
									"address3"	 	 =>	$cus->address3,
									"city"			 =>	$cus->city,
									"state"			 =>	$cus->state,
									"country"         =>'India',
									"pincode"		 =>	$cus->pincode,
									"phone"			 =>	$cus->phone,
									"mobile"		 =>	$cus->mobile,
									"email"			 =>	$cus->email,
									"dt_of_birth"	 =>	$cus->dt_of_birth,
									"wed_date"		 =>	$cus->wed_date,
                                    "new_customer"   => 'N',
                                    "is_transferred" => 'N',
                    				"is_modified"    => 0,
                                    "ac_name"      => $cus->account_name,
                                    "group_code"        => $cus->group_code,
                                    "scheme_ac_no"      => $cus->scheme_ac_no,
                                    "is_closed"         => ($cus->is_closed != "" && $cus->is_closed != null ? $cus->is_closed : 0),
                                    "closed_by"         => $cus->closed_by,
                                    "closing_date"      => $cus->closing_date,
                                    "closing_amount"    => ($cus->is_digi == 1 ? $cus->maturity_amount : $cus->closing_amount),
                                    "closing_weight"    => ($cus->is_digi == 1 ? $cus->maturity_weight : $cus->closing_weight),
                                    "closing_add_chgs"  => $cus->closing_add_chgs,
                                    "additional_benefits"=> $cus->additional_benefits,
                                    "remark_close"      => $cus->remark_close,
                                    "ref_no"            => $cus->ref_no,
                                    "is_digi"           => $cus->is_digi,
                                    "maturity_date"     => $cus->maturity_date,
                                    "id_branch" 	    => ($branch == 0 ? NULL:$branch),
                                    "branch_code"       => $cus->branch_code,
                    			);
					
					    $check = array( 'ref_no' 	   => $data['ref_no'],
									'clientid' 	       => $data['clientid'],
									'mobile' 	       => $data['mobile'],
									'reg_date'         => $data['reg_date'],
									'firstname'        => $data['firstname'],
									'group_code' 	   => $data['group_code'],
									'sync_scheme_code' => $data['sync_scheme_code'],
									'scheme_ac_no' 	   => $data['scheme_ac_no'],);
									
					if($this->config->item('no_branch') != 1){
					    $check['id_branch'] 	= $branch;
					}
					
									
				   $valid =$this->validateRegistrations($check); 
					
					/*if($valid['status'])
					{	
						$isRefExists = $this->$model->checkref($data['ref_no'],'customer_reg');	
						$isClientidExist = $this->$model->checkCusRegClientId($data['clientid']);
						if(!$isRefExists)
						{
						    if(!$isClientidExist){
						        $status  = $this->$model->insertData($data,'customer_reg');	
    						   if($status ===FALSE )	
    						   {
    							    $r[] = array('clientid' 		=> $data['clientid'],
    											 'ref_no'			=> $data['ref_no'],
    											 'isSucceeded'		=> FALSE,
    											 "responseData" => 1,  // for sync reference
    											 'result'			=> 'Unable to proceed the requested operation');	
    						   }
                               else
                               {							   
    							   $r[] = array('clientid' 		=> $data['clientid'],
    											 'ref_no'			=> $data['ref_no'],
    											 'isSucceeded'		=> TRUE,
    											 "responseData" => 1,  // for sync reference
    											 'result' 			=> 'Inserted successfully');	
    						   }
						    }
						    else
                            {							   
							   $r[] = array('clientid' 		=> $data['clientid'],
											 'ref_no'			=> $data['ref_no'],
											 'isSucceeded'		=> FALSE,
											 "responseData" => 1,  // for sync reference
											 'result' 			=> 'clientid already exist');	
						    }
						   	
						}
						else
                        {							   
							   $r[] = array('clientid' 		=> $data['clientid'],
											 'ref_no'			=> $data['ref_no'],	
											 'isSucceeded'		=> FALSE,
											 "responseData" => 1,  // for sync reference
											 'result' 			=> 'ref_no already exist');	
						}	
					}
                    else
                    {
                    
						$r[] = array('clientid' 		=> $data['clientid'],
									 'ref_no'			=> $data['ref_no'],
									 'isSucceeded' 		=> FALSE,
									 "responseData" => 1,  // for sync reference
									 "result" 			=> $valid['error']);
					}*/
					
					if($valid['status'])
					{	
						$isRefExists = $this->$model->checkref($data['ref_no'],'customer_reg');	
						$isClientidExist = $this->$model->checkCusRegClientId($data['clientid']);
						$isValid = true;
						$isCusRegRecordExist = false;
						if($isRefExists || $isClientidExist){
						    if($isClientidExist){
						        $isCusRegRecordExist = $this->$model->isCusRegRecordExist($data);
    						    if($isCusRegRecordExist){
        							$isValid = false;
    						    }else{
    						        $isValid = true;
    						    }
						    }
						}
					    if($isValid){
					       $status  = $this->$model->insertData($data,'customer_reg');	
						   if($status ===FALSE )	
						   {
							    $r[] = array('clientid' 		=> $data['clientid'],
											 'ref_no'			=> $data['ref_no'],
											 'isSucceeded'		=> FALSE,
											 "responseData" => 1,  // for sync reference
											 "error" => $this->db->_error_message(),
											 'result'			=> 'Unable to proceed the requested operation');	
						   }
                           else
                           {							   
							   $r[] = array('clientid' 		=> $data['clientid'],
											 'ref_no'			=> $data['ref_no'],
											 'isSucceeded'		=> TRUE,
											 "responseData" => 1,  // for sync reference
											 "InsId"            => $this->db->insert_id(),
											 'result' 			=> 'Inserted successfully..');	
						   }
					    }
					    else
                        {	
                           
                           if($isCusRegRecordExist){    
							   $r[] = array('clientid' 		=> $data['clientid'],
											 'ref_no'			=> $data['ref_no'],
											 'isSucceeded'		=> TRUE,
											 "responseData" => 1,  // for sync reference
											 'result' 			=> 'Customer account already Inserted');	
                           }
                           else if($isRefExists){    
							   $r[] = array('clientid' 		=> $data['clientid'],
									 'ref_no'			=> $data['ref_no'],
									 'isSucceeded'		=> FALSE,
									 "responseData"     => 1,  // for sync reference
									 "result"           => 'ref_no already exist'
									 );	
                           }
                           else if($isClientidExist){    
							   $r[] = array('clientid' 		=> $data['clientid'],
									 'ref_no'			=> $data['ref_no'],
									 'isSucceeded'		=> FALSE,
									 "responseData"     => 1,  // for sync reference
									 "result"           => 'clientid already exist'
									 );	
                           }
						   
					    }	
					}
                    else
                    {
                    
						$r[] = array('clientid' 		=> $data['clientid'],
									 'ref_no'			=> $data['ref_no'],
									 'isSucceeded' 		=> FALSE,
									 "responseData" => 1,  // for sync reference
									 "result" 			=> $valid['error']);
					}
					
					
                }
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
	
	//To get transactions by status
	public function checkRevertByStatus_post()
	{
		$credentials = json_decode(file_get_contents('php://input'));
		$login = $credentials->login;
		$branch = $login->id_branch;
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
		              
		if($this->checklogin($key))
		{		
			$status = $login->status;
			$model = self::MOD_API;
			$record_to = 1;
			$resultData = $this->$model->checkRevertByStatus($status,$branch);
			foreach($resultData as $result)
			{
				$revertRecord[]= array(	"id_revert_log"     => (int) $result["id_revert_log"],
                                        "is_reverted"       => (string) $result["is_reverted"],
                                        "is_transferred"    => (string) $result["is_transferred"],
                                        "delete_customer_reg"=> (string) $result["delete_customer_reg"], // whether to delete customer reg data or not
                                        "id_branch"         => (string) $result["id_branch"],
                                        "ref_no"            => (string) $result["ref_no"],
                                        "clientid"          => (string) $result["clientid"],
    									"responseData"      => 1  // for sync reference
    								  );
			}
			
			if($revertRecord)
			{
			    $this->response($revertRecord, 200);
			}	 
			else
			{
				$msg[] = array('message' => 'No data to revert!','responseData'=>0); // 'status'=>0 - to indicate response has no data
				$this->response($msg, 200); 
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}	
	}	
	
	
	//To update multiple transactions (bulk)
	
	public function updateReverted_post()
	{
		$trans_data = json_decode(file_get_contents('php://input'));
	    $login =  $trans_data->login; 
	    $updType = "";
	    $branch = $trans_data->login->id_branch;
        
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
				  $valid = array();
			      
			          /* To update transaction receipt */
                      $trans = array( 	'id_revert_log'	 => $tran->id_revert_log,
                    					'is_transferred' => 'Y',
                    					'transfer_date'	 => $tran->transfer_date);
                    					
                      $valid = $this->validateRecords($trans);
					
						if($valid['status'])
						{
								$status = $this->$model->updateData($trans,$branch,'revert_approve_log');
						 
								 if($status ===FALSE)
								{
									$r[] = array('id_revert_log' 	=> $trans['id_revert_log'],
												 'isSucceeded' 		=> FALSE,
												 "responseData" => 1,  // for sync reference
												 'result' 			=> 'Unable to proceed the requested operation');
									//$this->response($r,200);
									
								}         
								else
								{
									$r[] = array('id_revert_log'   => $trans['id_revert_log'],
												 'isSucceeded'    => TRUE,
												 "responseData" => 1,  // for sync reference
												// 'query'         => $this->db->last_query(),
												 'result' 		  => 'Updated Successfully');								
									//$this->response('', 404);
								} 		
						}
						else
						{
							$r[] = array(	'id_revert_log'   => $tran->id_revert_log,
											'isSucceeded' 	 => FALSE,
											"responseData" => 1,  // for sync reference
											"result" 		 =>$valid['error']);
							//$this->response($r,401);
						} 
				}
				$this->response($r,200);
			}	
			else
			{
			    $msg[] = array('message' => 'No data to proceed the requested operation!','responseData' => 0);
				$this->response($msg, 200); 
			}
		
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}			
	}
	
	function uploadOfflineData_post(){
	    $folder_path = 'assets/offline_data/';
	    $file_path = $folder_path."".date('Y-m-d'); 
		if (!is_dir($file_path)) { // Check and create Directory
			mkdir($file_path, 0777, TRUE);
		} 
        $log_path    = $file_path."/log.txt"; 
        
	    if(strpos($_FILES['file']['name'], 'NEW_ACC') !== false || strpos($_FILES['file']['name'], 'CLS_ACC') !== false || strpos($_FILES['file']['name'], 'NEW_PAY') !== false || strpos($_FILES['file']['name'], 'CNCL_PAY') !== false) // Word Found
		{
		    $this->load->helper(array('form','url'));
            
		    if(file_exists($file_path."/".$_FILES['file']['name'])) // Check whether file already exist
	   	    {
				$log_title = "Error";
                $result = array('status' => false,'msg' => "File already exist !!", 'file' => $_FILES );
			}
			else{
			    $config['encrypt_name'] = FALSE;
                $config['upload_path'] =  $folder_path.''.date('Y-m-d');
                $config['allowed_types'] = '*';
                $config['max_size']    = 0;  // set upload limit, set 0 for no limit
         
                // load upload library with custom config settings
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
         
                // if upload failed , display errors
                if (!$this->upload->do_upload('file'))
                { 
                    $result = array('status' => false, 'msg' => $this->upload->display_errors(), 'file' => $_FILES);
                    $log_title = "Upload Error";
                }
                else
                {
                    $upload = $this->upload->data();
                    $log_title = "Success";
                    $result = array('status' => true, 'msg' => 'Uploaded successfully!');
                }
			}
		}
		else{ 
		    $log_title = "Error";
            $result = array('status' => false,'msg' => "Invalid File Name", 'file' => $_FILES );
        }
        // Log Data
        $data = "\n".date('Y-m-d H:i:s')." ".$log_title." : ".json_encode($result,true);
	    file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX); 
        $this->response($result, 200);
	}
	
	
	//Rate api for sync tool branch wise //HH
		public function rate_branchwise_post()
	{
		$credentials = json_decode(file_get_contents('php://input'));
		$login = $credentials->login;
		$branch = $login->id_branch;
//	print_r($branch);exit;
		$key = array('username' => $login->username,
		              'passwd' => $login->passwd); 
		              
		if($this->checklogin($key))
		{		
			
			$model = self::MOD_API;
			//$rate_branchwise = array();
			$rate_branchwise = $this->$model->getrate_branchwise($branch);
		
			
			if($rate_branchwise)
			{ 
				$this->response($rate_branchwise, 200);
			}	 
			else
			{
			    $msg[] = array('message' => 'No Metal rates records found!',"responseData" => 0);
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