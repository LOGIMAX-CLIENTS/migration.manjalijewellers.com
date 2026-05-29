<?php
require(APPPATH.'libraries/REST_Controller.php');
class chitapi extends REST_Controller
{
	const MOD_API = "chitapi_model";
	
	function __construct()
	{
		parent::__construct();
		$this->load->model(self::MOD_API);
		ini_set('date.timezone', 'Asia/Calcutta');
		
		$this->branch = NULL;
				
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
    
   function validateclosingRecords($data)
	{  
	  $error=NULL;
	  $status = TRUE;
       
		  $isEmpty = $this->isArrayEmpty($data);
			if(!$isEmpty['status'])
			{
			   if(!$this->validateDate($data['closing_date']))
				{
					$status = FALSE;
					$error.= "closing_date has invalid date format ".$data['closing_date'].", valid date format (YYYY-MM-DD). "; 
				}				
			}
			else
			{
				$status = FALSE;
				$error = $isEmpty['errors']; 
			}	
	   
        
       return array('status' => $status,'error' => $error);		
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
			   
			   if(!$this->isValidStatus($data['transfer_jil']))
				{
					$status = FALSE;
					$error.= "transfer_jil has invalid value ".$data['transfer_jil'].". "; 
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
			   if(!$this->validateDate($data['transfer_date']))
				{
					$status = FALSE;
					$error.= "transfer_date has invalid date format ".$data['transfer_date'].", valid date format (YYYY-MM-DD). "; 
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
			   
			   if(!$this->isValidStatus($data['transfer']))
				{
					$status = FALSE;
					$error = "transfer has invalid value ".$data['transfer']; 
				}
				
			
				 					
			}
			else
			{
				$status = FALSE;
				$error = $isEmpty['errors']; 
			}	
	   
        
       return array('status' => $status,'error' => $error);		
	}
	   
	
	public function transactions_post()
	{
		$credentials = file_get_contents('php://input');
		$login = json_decode($credentials);
		
		$key = array('username' => $login->login->username,
		              'passwd' => $login->login->passwd); 
		if($this->checklogin($key))
		{		
	      
			$model = self::MOD_API;
			$transactions = $this->$model->getTransactionAll();
			
			foreach($transactions as $transaction)
			{
				$customerPaidAmount=($transaction["amount"]-$transaction["discountAmt"]);
				
				$trans[]= array(	"id_transaction" => (int)    $transaction["id_transaction"],
									"trans_date" 	 => (string) $transaction["trans_date"],
									"custom_entry_date"=> (string) $transaction["custom_entry_date"],
									"client_id" 	 => (string) $transaction["client_id"],
									"group_code" 	 => (string) $transaction["group_code" ],
									"msno" 			 => (string) $transaction["msno" ],
									"scheme_code" 	 => (string) $transaction["scheme_code" ],
									"amount" 		 => number_format((float)$transaction["amount"], 2, '.', ''),
									"weight" 		 => number_format((float)$transaction["weight"], 3, '.', '') ,
									"rate" 			 => number_format((float)$transaction["rate"], 3, '.', '') ,
									"metal" 		 => (int)    $transaction["metal"],
									"payment_mode" 	 => (string) $transaction["payment_mode"],
									"bank_name" 	 => (string) $transaction["bank_name"],
									"branch_name" 	 => (string) $transaction["branch_name"],
									"card_no" 		 => (string) $transaction["card_no" ],
									"approval_no" 	 => (string) $transaction["approval_no"],
									"ref_no" 		 => (int)    $transaction["ref_no"],
									"transfer_jil" 	 =>(string)  $transaction["transfer_jil" ],
									"transfer_date"  => (string) $transaction["transfer_date"],
									"DiscountedAmt"  => number_format((float)$transaction["discountAmt"], 2, '.', ''),
									"CustomerPaidAmount"  => number_format((float)$customerPaidAmount, 2, '.', ''),
									"new_customer" 	 => (string) $transaction["new_customer"]);
								
			}
			
		   
			if($trans)
			{
				$this->response($trans, 200);
			}	 
			else
			{
					$this->response(array('message' => 'No transaction data'), 200);
			}
		}
   		else
		{
			$this->response('Invalid creddentials!', 401);
		}
	}	
	//To get transactions by status
	public function transactionsByStatus_post()
	{
		$credentials = json_decode(file_get_contents('php://input'));
		
		$login = $credentials;
		$branches = $this->config->item('id_branch');
		
		$branch = $this->branch;
		/* $branches = $this->config->item('id_branch');
		foreach($branches as $key => $value){
			if($key == $login->id_branch){
				$branch = $value;
			}			
		} */			
	
		$key = array('username' => $login->username,
		              'passwd' => $login->passwd); 
		              
		if($this->checklogin($key))
		{		
			$status = $login->status;
			$model = self::MOD_API;
			$transactions = $this->$model->getTransactionByTranStatus($status,$branch);
			foreach($transactions as $transaction)
			{
				$customerPaidAmount=($transaction["amount"]-$transaction["discountAmt"]);
				
				$trans[]= array(	"id_transaction" => (int) $transaction["id_transaction"],
									"trans_date" 	 => (string) $transaction["trans_date"],
									"custom_entry_date" 	 => (string) $transaction["custom_entry_date"],
									"client_id" 	 => (string)$transaction["client_id"],
									"group_code" 	 => (string)$transaction["group_code" ],
									"msno" 			 => (string)$transaction["msno" ],
									"scheme_code" 	 => (string)$transaction["scheme_code" ],
									"amount" 		 => number_format((float)$transaction["amount"], 2, '.', ''),
									"weight" 		 => number_format((float)$transaction["weight"], 3, '.', '') ,
									"rate" 			 => number_format((float)$transaction["rate"], 3, '.', '') ,
									"metal" 		 => (int)  $transaction["metal"],
									"payment_mode" 	 => (string)$transaction["payment_mode"],
									"bank_name" 	 => (string)$transaction["bank_name"],
									"branch_name" 	 => (string)$transaction["branch_name"], // bank branch name
									"card_no" 		 => (string)$transaction["card_no" ],
									"approval_no" 	 => (string)$transaction["approval_no"],
									"id_branch" 	 => (string)$transaction["id_branch"],
									"ref_no" 		 => (int)$transaction["ref_no"],
									"transfer_jil" 	 => (string) $transaction["transfer_jil" ],
									"transfer_date"  => (string)$transaction["transfer_date"],
									"DiscountedAmt"  => number_format((float)$transaction["discountAmt"], 2, '.', ''),
									"CustomerPaidAmount"  => number_format((float)$customerPaidAmount, 2, '.', ''),
									"new_customer" 	 => (string)$transaction["new_customer"]);
								
			}
			
			if($trans)
			{
			    $result = array_unique($trans, SORT_REGULAR);
				$this->response($result, 200);
			}	 
			else
			{
				$this->response(array('message' => 'No transaction data'), 200);
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}	
	}
	
	
	
	//update transaction single record
	
	public function updateTransaction_post()
	{
		
		$transaction = json_decode(file_get_contents('php://input'));
		
		$branch = $this->branch;
		/* $branches = $this->config->item('id_branch');
		foreach($branches as $key => $value){
			if($key == $transaction->login->id_branch){
				$branch = $value;
			}			
		} */
		
		$key = array('username' => $transaction->login->username,
		              'passwd' => $transaction->login->passwd); 
					  
		if($this->checklogin($key))
		{	
			$model = self::MOD_API;
			$trans = (array)$transaction->data;
		
			$valid =$this->validateRecords($trans);
		
			if($valid['status'])
			{
					$data = array('transfer_jil'  => $trans['transfer_jil'],
						          'transfer_date' => $trans['transfer_date']); 						  
				    $id   = $trans['id_transaction']; 
		
					$status = $this->$model->update_transaction($data,$id);
				
			
					 if($status === FALSE)
					{
						
							$r = array('id_transaction' => $trans['id_transaction'],
									   'isSucceeded'	=> FALSE,
									   'result' 		=> 'Unable to proceed the requested operation');
					}         
					else
					{
						$r = array('id_transaction' => $trans['id_transaction'],
								   'isSucceeded'	=> TRUE,
								   'result' 		=> 'Updated Successfully');
						$this->response($r,200);
						
					} 		
								
			}
            else
            {
				$r = array(	'id_transaction' => $trans['id_transaction'],
							'isSucceeded' 	 => FALSE,
							"result" 		 => $valid['error']);
				$this->response($r,401);
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
	    $login =  $trans_data; 
	   
	    $branch = $this->branch;
	   /*  $branches = $this->config->item('id_branch');
		foreach($branches as $key => $value){
			if($key == $login->id_branch){
				$branch = $value;
			}			
		} */
        
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
					$trans =array( 'id_transaction'	 => $tran->id_transaction,
					               'transfer_jil'	 => $tran->transfer_jil,
					               'transfer_date'	 => $tran->transfer_date);
								   
					$valid =$this->validateRecords($trans);
					
						if($valid['status'])
						{
								$data = array('transfer_jil'  => strtoupper($trans['transfer_jil']),
											  'transfer_date' => $trans['transfer_date']); 						  
								$id   = $trans['id_transaction']; 
					          
								$status = $this->$model->update_transaction($data,$id);
							  
							 
						 
								 if($status ===FALSE)
								{
									$r[] = array('id_transaction' 	=> $trans['id_transaction'],
												 'isSucceeded' 		=> FALSE,
												 'result' 			=> 'Unable to proceed the requested operation');
									//$this->response($r,200);
									
								}         
								else
								{
									$r[] = array('id_transaction' => $trans['id_transaction'],
												 'isSucceeded'    => TRUE,
												 'result' 		  => 'Updated Successfully');								
									//$this->response('', 404);
								} 		
						}
						else
						{
							$r[] = array(	'id_transaction' => $trans['id_transaction'],
											'isSucceeded' 	 => FALSE,
											"result" 		 =>$valid['error']);
							//$this->response($r,401);
						} 
				}
				$this->response($r,200);
			}	
			else
			{
				$this->response(array('message' => 'No data to proceed the requested operation!'), 200);
			}
		
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}			
	}	
	
	//To update transactions by range
	
	public function updateTransactionsByRange_post()
	{
		$transaction = json_decode(file_get_contents('php://input'));
		
		$branch = $this->branch;
		/* $branches = $this->config->item('id_branch');
		foreach($branches as $key => $value){
			if($key ==  $transaction->login->id_branch){
				$branch = $value;
			}			
		} */
		
		$key = array('username' => $transaction->username,
		              'passwd'  => $transaction->passwd); 
					  
		if($this->checklogin($key))
		{	
			$model = self::MOD_API;
			
			$trans   =  $transaction->data;
			
			$upperlimit    = $trans->upperlimit;
			$lowerlimit    = $trans->lowerlimit;
			$trans_data    = array(  "transfer_jil" => $trans->transfer_jil,
									 "transfer_date" => $trans->transfer_date
								  );
			$status = $this->$model->updateTransactionByRange($upperlimit,$lowerlimit,$trans_data);
			 if($status === FALSE)
			{
			  $this->response(array('message' => 'Unable to proceed the requested operation'), 200);
			}
			 
			else
			{
				$this->response('Transactions updated successfully.',200);
			} 		
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}	
	}		
	
	
	 
	//Registration
	
	public function registrations_post()
	{
		$credentials = json_decode(file_get_contents('php://input'));
		$login = $credentials;
		
		$branch = $this->branch;
		/* $branches = $this->config->item('id_branch');
		foreach($branches as $key => $value){
			if($key ==   $login->id_branch){
				$branch = $value;
			}			
		} */
		
		$key = array('username' => $login->login->username,
		             'passwd' 	=> $login->login->passwd);
		if($this->checklogin($key))
		{		
			$model = self::MOD_API;
		
			$registrations = $this->$model->getCustomerRegAll($branch);
			foreach($registrations as $reg)
			{
				$reg_records[]=array("id_customer_reg" => (int)    $reg["id_customer_reg"],
									"reg_date"		 => (string) $reg["reg_date"],
									"salutation"     => (string) $reg["salutation"],
									"initials"		 => (string) $reg["initials"] ,
									"name"			 =>	(string) $reg["name"],
									"doorno"		 =>	(string) $reg["doorno"],
									"address1"		 =>	(string) $reg["address1"],
									"address2"		 =>	(string) $reg["address2"],
									"address3"	 	 =>	(string) $reg["address3"],
									"city"			 =>	(string) $reg["city"],
									"state"			 =>	(string) $reg["state"],
									"pincode"		 =>	(string) $reg["pincode"],
									"phone"			 =>	(string) $reg["phone"],
									"mobile"		 =>	(string) $reg["mobile"],
									"email"			 =>	(string) $reg["email"],
									"dt_of_birth"	 =>	(string) $reg["dt_of_birth"],
									"wed_date"		 =>	(string) $reg["wed_date"],
									"ref_no"		 =>	(string) $reg["ref_no"],
									"transfer_jil"	 =>	(string) $reg["transfer_jil"],
									"transfer_date"	 =>	(string) $reg["transfer_date"]	,
									"id_branch"	 	 =>	(string) $reg["id_branch"]	,
									"pan_no"	     =>	(string) $reg["pan_no"]	,
									"new_customer"	 =>	(string) $reg["new_customer"]);
			}
			if($reg_records)
			{
				$this->response($reg_records, 200);
			}
	 
			else
			{
				$this->response(array('message' => 'Unable to proceed the requested operation'), 200);
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}		
	}	
	
	//To get registrations by status
	
	public function registrationsByStatus_post()
	{
		$credentials = json_decode(file_get_contents('php://input'));
		$login = $credentials;
		
		$branch = $this->branch;
		/* $branches = $this->config->item('id_branch');
		foreach($branches as $key => $value){
			if($key ==   $login->id_branch){
				$branch = $value;
			}			
		} */
	
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
					  
		if($this->checklogin($key))
		{		
	      $status = $login->status;  
		  
			$model = self::MOD_API;
			$registrations = $this->$model->getCustomerRegByTranStatus($status,$branch);
			
			foreach($registrations as $reg)
			{
				$reg_records[]=array("id_customer_reg" => (int)  $reg["id_customer_reg"],
									"reg_date"		 => (string) $reg["reg_date"],
									"salutation"     => (string) $reg["salutation"],
									"initials"		 => (string) $reg["initials"] ,
									"name"			 =>	(string) $reg["name"],
									"doorno"		 =>	(string) $reg["doorno"],
									"address1"		 =>	(string) $reg["address1"],
									"address2"		 =>	(string) $reg["address2"],
									"address3"	 	 =>	(string) $reg["address3"],
									"city"			 =>	(string) $reg["city"],
									"state"			 =>	(string) $reg["state"],
									"pincode"		 =>	(string) $reg["pincode"],
									"phone"			 =>	(string) $reg["phone"],
									"mobile"		 =>	(string) $reg["mobile"],
									"email"			 =>	(string) $reg["email"],
									"dt_of_birth"	 =>	(string) $reg["dt_of_birth"],
									"wed_date"		 =>	(string) $reg["wed_date"],
									"ref_no"		 =>	(string) $reg["ref_no"],
									"transfer_jil"	 =>	(string) $reg["transfer_jil"],
									"id_branch"	 	 =>	(string) $reg["id_branch"]	,
									"transfer_date"	 =>	(string) $reg["transfer_date"]	,
									"pan_no"	     =>	(string) $reg["pan_no"]	,
									"new_customer"	 =>	(string) $reg["new_customer"]);
			}
			if($reg_records)
			{
				$result = array_unique($reg_records, SORT_REGULAR);
				$this->response($result, 200);
			}
	 
			else
			{
			$this->response(array('message' => 'No records found'), 200);
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}		
	}
	
	//To get registrations by date
	public function registrationsByDate_get()
	{
		$model = self::MOD_API;
		$trans_date = $this->post('transfer_date'); 
		$registrations = $this->$model->getCustomerRegByTransferDate($trans_date);
		if($registrations)
        {
            $this->response($registrations, 200);
        }
 
        else
        {
             $this->response(array('message' => 'Unable to proceed the requested operation'), 200);
        }
	}
	
	
	//update registration single record
	public function updateRegistration_post()
	{
		
		$registrations = json_decode(file_get_contents('php://input'));
		$login = $registrations;
		
		$branch = $this->branch;
		/* $branches = $this->config->item('id_branch');
		foreach($branches as $key => $value){
			if($key ==   $login->id_branch){
				$branch = $value;
			}			
		} */
		
		$key = array('username' => $login->login->username,
		             'passwd' => $login->login->passwd); 		  
		if($this->checklogin($key))
		{	
		    $model = self::MOD_API;
		
			$reg =  (array) $registrations->data;
			$data = array('transfer_jil'  => $reg['transfer_jil'],
						  'transfer_date' => $reg['transfer_date']); 
							  
			
		    $valid = $this->validateRecords($data);
		    if($valid['status'])
			{		
					 
				$id  = $reg['id_customer_reg']; 
				$status = $this->$model->update_CustomerReg($data,$id);			
			 
				if($status === FALSE)
				{
					$r = array('id_customer_reg' =>$reg['id_customer_reg'],
							   'isSucceeded' => FALSE,
							   'result' => 'Unable to proceed the request operation');
						$this->response($r,200);
				}
				 
				else
				{
					$r = array('id_customer_reg' =>$reg['id_customer_reg'],
							   'isSucceeded' => TRUE,
							   'result' => 'Updated Successfully');
						$this->response($r,200);
				} 
			}
            else
            {
				$r = array(	'id_customer_reg' =>$reg['id_customer_reg'],
							'isSucceeded' => FALSE,
							"error" =>$valid['error']);
				$this->response($r,401);
			} 
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}		
		
	}
	
	//update bulk Registrations 
	public function updateRegistrations_post()
	{
		$registrations = json_decode(file_get_contents('php://input'));	
		$login = $registrations->login;
		
		$branch = $this->branch;
		/* $branches = $this->config->item('id_branch');
		foreach($branches as $key => $value){
			if($key ==   $login->id_branch){
				$branch = $value;
			}			
		} */
		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
					  
		if($this->checklogin($key))
		{	
			$model = self::MOD_API;
			$regs = $registrations->data;
			$records = 0;
			if($regs)
			{
				$records = count($regs);
			
					foreach($regs as $reg)
					{
			
						$reg  = array('id_customer_reg' => $reg->id_customer_reg ,
						                'transfer_jil'  => $reg->transfer_jil,
										'transfer_date' => $reg->transfer_date); 
							
					    $valid =$this->validateRecords($reg);
						if($valid['status'])
						{
							$id    = $reg['id_customer_reg'] ; 		
							$data  = array('transfer_jil'  => $reg['transfer_jil'],
										   'transfer_date' => $reg['transfer_date']); 
										   
										   
							$status = $this->$model->update_CustomerReg($data,$id);
							if($status ===FALSE)
								{
									$r[] = array('id_customer_reg' =>$reg['id_customer_reg'],
											   'isSucceeded' => FALSE,
											   'result' => 'Unable to proceed the requested operation');																		
								}         
								else
								{
									$r[] = array('id_customer_reg' =>$reg['id_customer_reg'],
										   'isSucceeded' => TRUE,
										   'result' => 'Updated Successfully');
								} 	
							
						}
						else
						{
							$r[] = array('id_customer_reg' =>$reg['id_customer_reg'],
										 'isSucceeded' => FALSE,
										 "result" =>$valid['error']);
							//$this->response($r,401);
						} 	
					}
						$this->response($r,200);
			}	
			else
			{
				$this->response(array('message' => 'No data to proceed the requested operation'),200);
			} 				
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}		
	}	
	
	//update Registrations By Range
	public function updateRegistrationsByRange_post()
	{
		
		$registrations = json_decode(file_get_contents('php://input'));	   
		$login = $registrations;
		
		$branch = $this->branch;
		/* $branches = $this->config->item('id_branch');
		foreach($branches as $key => $value){
			if($key ==   $login->id_branch){
				$branch = $value;
			}			
		} */
		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
					  
		if($this->checklogin($key))
		{	
			$model = self::MOD_API;
			
			$reg = $registrations->data;
		
		
			$upperlimit    = $reg->upperlimit;
			$lowerlimit    = $reg->lowerlimit;
			$trans_data    = array("transfer_date"=>$reg->transfer_date,
									"transfer_jil"=>	$reg->transfer_jil);
						
			
			$status = $this->$model->update_CustomerRegByRange($upperlimit,$lowerlimit,$trans_data);
			 if($status === FALSE)
			{
				$this->response(array('message' => 'Unable to proceed the requested operation'), 200);
			}
			 
			else
			{
				$this->response('Registration records updated successfully', 202);
			} 		
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}	
	}		
	
	
	
	
	//New Customer
	public function newCustomers_post()	{
		
		$customers = json_decode(file_get_contents('php://input'));	
		$login = $customers;
		
		$branch = $this->branch;
		/* $branches = $this->config->item('id_branch');
		foreach($branches as $key => $value){
			if($key ==   $login->id_branch){
				$branch = $value;
			}			
		} */
		
		$key = array('username' => $login->username,
		             'passwd' 	=> $login->passwd); 
					  
		if($this->checklogin($key))
		{	
			$model = self::MOD_API;
			$records = $this->$model->getNewCustomerAll();
			foreach($records as $cus)
			{
				$cus_records[] =array(  "id_new_customer" 	=> (int) $cus["id_new_customer"],
										"ref_no" 			=> (string) $cus["ref_no"],
										"mobile" 			=> (string) $cus["mobile"],
										"clientid" 			=> (string) $cus["clientid"],
										"group_code" 		=> (string) $cus["group_code"],
										"msno" 				=> (string) $cus["msno"],
										"transfer" 			=> (string) $cus["transfer"],
										"id_branch"			=> (string) $cus["id_branch"],
										"transfer_date" 	=> (string) $cus["transfer_date"]);
			}
			if($cus_records)
			{
				$this->response($cus_records,200);
			}
			 
			else
			{
				$this->response(array('message' => 'No new customer records'), 200);
			} 
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}	
	}
	
	public function IsNewCustomerExist($data)
	{
		$model = self::MOD_API;
		return $this->$model->checkNewCustomer($data);
	}
		
	//insert new customer single record	
	
	public function insertNewCustomer_post()
    {
		$customers = json_decode(file_get_contents('php://input'));
		
		$login = $customers;
		
		$branch = $this->branch;
		$branches = $this->config->item('id_branch');
		/* foreach($branches as $key => $value){
			if($key ==   $login->id_branch){
				$branch = $value;
			}			
		} */
		
		$key = array('username'		=> $login->login->username,
		             'passwd' 		=> $login->login->passwd); 
		if($this->checklogin($key))
		{	
	   
			$model = self::MOD_API;
			$customer = (array)$customers->data;
			
			if($customer)
			{
				$check = array(	'ref_no' 	 	=> $customer['ref_no'],	
								'mobile' 	 	=> $customer['mobile'],	
								'clientid' 	 	=> $customer['clientid'],	
								'transfer' 	 	=> $customer['transfer']);
				$valid = $this->validateRegistrations($check);		
				   if($valid['status'])
				   {
						 $data = array(	'ref_no' 	 	=> $customer['ref_no'],	
										'mobile' 	 	=> $customer['mobile'],	
										'clientid' 	 	=> $customer['clientid'],		
										'group_code' 	=> $customer['group_code'],	
										'msno' 		 	=> $customer['msno'],	
										'new_customer' 	=> $customer['new_customer'],	
										'receipt_jil' 	=> $customer['receipt_jil'],
										'transfer' 	 	=> $customer['transfer'],
										'id_branch'		=> $branch,									
										);
							
							
					 /* $isExists = $this->IsNewCustomerExist($data);
					  if(!$isExists)
					  { */
						  $status = $this->$model->insert_newCustomer($data);
							if($status)
							{
								$r = array('clientid' 	=> $data['clientid'],
								           'ref_no'		=> $data['ref_no'],
										   'isSucceeded'=> True,
										   'result' 	=> 'Inserted successfully');									
								$this->response($r, 200);
							}
							
				 /*	}   			  
					  else
					  {
						  $r = array('clientid' =>$data['clientid'],
									 'ref_no' => $data['ref_no'], 				   
									 'isSucceeded' => FALSE,
									 'result' => 'Client id '.$data['clientid'].' and Ref.No '.$data['ref_no'].' already exist');
						  $this->response($r, 404);
					  }	  	*/
			     
				   }
				   else
				   {
					     $r = array('clientid'    =>$check['clientid'],
						            'ref_no' 	  => $data['ref_no'], 				
									'isSucceeded' => FALSE,
									'result' => $valid['errors']);
						  $this->response($r, 200);
				   }	   
			   	
			}	
			else
			{
				$this->response(array('message' => 'No data to proceed the requested operation'), 200);
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}	
	}	
	
   //insert all new customers
   
  	public function insertNewCustomers_post()
	{
		$newcustomer = json_decode(file_get_contents('php://input'));
		$login = $newcustomer->login;
		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
		
		$branch = $this->branch;
		/* $branches = $this->config->item('id_branch');
		foreach($branches as $key => $value){
			if($key ==   $login->id_branch){
				$branch = $value;
			}			
		} */
					  
		if($this->checklogin($key))
		{		   
			$model = self::MOD_API;				
			$customers = $newcustomer->data;
			$records = 0;
			$invalid = 0;
			if($customers)
			{
				$total_records = count($customers);
				
				foreach($customers as $customer)
				{
					$data = array(	
									'ref_no' 	 	=> $customer->ref_no,	
									'mobile' 	 	=> $customer->mobile,	
									'clientid' 	 	=> $customer->clientid,		
									'group_code' 	=> $customer->group_code,	
									'msno' 		 	=> $customer->msno,	
									'new_customer'  => $customer->new_customer,	
								    'receipt_jil' 	=> $customer->receipt_jil,	
									'transfer' 	 	=> $customer->transfer,
									'id_branch'		=> $branch
									//,'transfer_date' => $customer->transfer_date	
								);
								
					$check = array(	'ref_no' 	 	=> $data['ref_no'],	
									'mobile' 	 	=> $data['mobile'],	
									'clientid' 	 	=> $data['clientid'],
			                        'receipt_jil' 	=> $data['receipt_jil'],	
									'transfer' 	 	=> $data['transfer']);
				    $valid =$this->validateRegistrations($check);
                     				 
					
					if($valid['status'])
					{	
						/* $isExists = $this->IsNewCustomerExist($data); 
						if(!$isExists)
						{		*/
						  	
						  $status  = $this->$model->insert_newCustomer($data);	
							
						   if($status ===FALSE )	
						   {
							    $r[] = array('clientid'		 => $data['clientid'],
								             'ref_no'		 => $data['ref_no'], 				
											 'isSucceeded'	 => FALSE,
											 'result' 		 => 'Unable to proceed the requested operation');	
						   }
                           else
                           {							   
							   $r[] = array('clientid' 		=> $data['clientid'],
							                'ref_no'		=> $data['ref_no'], 				
											'isSucceeded'	=> TRUE,
											'result' 		=> 'Inserted successfully');	
						   }				   						
					/*	}	
						else
						{
							 $r[] = array('clientid' =>$data['clientid'],
							             'ref_no' => $data['ref_no'], 				  
										'isSucceeded' => FALSE,
										'result' => 'clientid '.$data['clientid'].' already exist');
							
						}	
						*/	
					}
                    else
                    {
						$r[] = array('clientid' 	=> $data['clientid'],
					                 'ref_no' 		=> $data['ref_no'], 				
									 'isSucceeded'  => FALSE,
									 "result" 		=> $valid['error']);
					}
                }
				  $this->response($r,200);
			}	
						
			else
			{
				$this->response(array('message' => 'No records to proceed the requested operation'), 200);
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}		
	}
	
	 //insert bulk offline payments --- for testing
	public function testinsertOfflinePayment_post()
	{
		//$offlinePayments = json_decode(file_get_contents('php://input'));
		$offlinePayments = (object) $_POST;
		
		$login = $offlinePayments;
			
		$key = array('username' => $login['username'],
		             'passwd' => $login['passwd']); 
		            
				
		if($this->checklogin($key))
		{		   
			$model = self::MOD_API;
				
			$payments = $offlinePayments->data; 
			$records = 0;
			$invalid = 0;
			if($payments)
			{
				$total_records = count($payments);
				
				foreach($payments as $payment)
				{
					
					
					$data = array(	'ac_name' 			=> $payment['ac_name'],	
									'brefno'			=> 'JIL'-$payment['brefno'],
									'clientid' 		 	=> $payment['clientid'],	
									'date_payment'		=> $payment['date_payment'],
									'discountAmt' 		=> $payment['discountAmt'],
									'id_branch' 		=> ($payment['id_branch'] == 'TN' ? '1' :'2'),					
									'id_metal' 		 	=> ($payment['id_metal'] == 'G' ? '1' : ($payment['id_metal'] == 'S' ? '2':'3')),	
									'is_trans_completed'=> 'N',
									'metal_rate' 		=> $payment['metal_rate'],
									'metal_weight'	 	=> $payment['metal_weight'],	
									'mobile' 		 	=> $payment['mobile'],
									'payment_amount' 	=> $payment['payment_amount'],		
									'payment_mode' 		=> $payment['payment_mode'],	
									'receipt_jil'	 	=> $payment['receipt_jil'],
									'remark' 			=> $payment['remark'],	
									'scheme_ac_number' 	=> $payment['scheme_ac_number'],
									'status'			=> $payment['status'],									
									'instalment'		=> $payment['instalment'],
									'transfer_date'		=> $payment['transfer_date']									
								);
								
					$check = array( 'id_branch' 		=> $data['id_branch'],
									'brefno' 			=> $data['brefno'],
									'scheme_ac_number' 	=> $data['scheme_ac_number'],
									'transfer_date'		=> $data['transfer_date'],
									'payment_amount' 	=> $data['payment_amount'],
									'instalment'		=> $payment['instalment'],
									'date_payment' 	 	=> $data['date_payment']);
									
				   $valid =$this->validatePayRec($check); 
					
					if($valid['status'])
					{	
						$isBrefExists = $this->$model->checkBref($data['brefno']);	
						if(!$isBrefExists)
						{
						  $status  = $this->$model->insert_offlinePayment($data);	
						   if($status ===FALSE )	
						   {
							    $r[] = array('clientid' 		=> $data['clientid'],
											 'brefno'			=> $data['brefno'],
											 
											 'date_payment' 	=> $data['date_payment'],											 
											 'receipt_jil' 		=> $data['receipt_jil'],
											 'isSucceeded'		=> FALSE,
											 'result'			=> 'Unable to proceed the requested operation');	
						   }
                           else
                           {							   
							   $r[] = array('clientid' 		=> $data['clientid'],
											'brefno'			=> $data['brefno'], 
									 
										    'date_payment' 	=> $data['date_payment'],											 
											'receipt_jil' 		=> $data['receipt_jil'],
											'isSucceeded'		=> TRUE,
											'result' 			=> 'Inserted successfully');	
						   }	
						}
						else
                        {							   
							   $r[] = array('clientid' 		=> $data['clientid'],
											'brefno'			=> $data['brefno'],
									 
											'date_payment' 		=> $data['date_payment'],											 
											'receipt_jil' 		=> $data['receipt_jil'],			
											'isSucceeded'		=> FALSE,
											'result' 			=> 'brefno already exist');	
						}	
					}
                    else
                    {
						$r[] = array('clientid' 		=> $data['clientid'],
									 'brefno'			=> $data['brefno'],
									 
									 'date_payment' 	=> $data['date_payment'],											 
									 'receipt_jil' 		=> $data['receipt_jil'],
									 'isSucceeded' 		=> FALSE,
									 "result" 			=> $valid['error']);
					}
                }
                
				 // $this->insertTransInPayment();
				  $this->response($r,200);
			}	
						
			else
			{
			$this->response(array('message' => 'No records to proceed the requested operation'), 200);
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}		
	}
	
	public function insertTransInPayment(){
		//get offline payment records which are not updated in payment table
	   $model = self::MOD_API;
	   $getPayments  = $this->$model->getofflinePaymentsbyStatus('N');
	
	   foreach($getPayments as $payData)
		{	
		 
		   $id_sch_ac = $this->$model->getIdschemeAC($payData);
		   
		   if($id_sch_ac){
			if(!empty($payData['payment_mode'])){
				 $expArray = explode('-',$payData['payment_mode']);
				 $pay_mode = $expArray[0];
				 }
				 else{
				 	$pay_mode ='Offline';
				 }
			     $isCancelled = (substr($payData['payment_amount'],0,1)== '-' ? TRUE :FALSE);
			    // echo $isCancelled;
			   
			if(!$isCancelled){
				$pay_array = array ( "id_scheme_account" => $id_sch_ac,
			   						"date_payment" 		=> $payData['date_payment'],
			   						"id_metal" 			=> $payData['id_metal'],
			   						"metal_rate" 		=> $payData['metal_rate'],
			   						"payment_amount"	=> $payData['payment_amount'],
			   						"metal_weight" 		=> $payData['metal_weight'],
			   						"payment_mode" 		=> $pay_mode,
			   						"payment_status" 	=> 1,
			   						"receipt_jil" 		=> $payData['receipt_jil'],
			   						"remark" 			=> $payData['remark'],
			   						"discountAmt"		=> $payData['discountAmt'],
			   						"payment_ref_number"=> $payData['brefno']
			    					);	
			    $insPayment  = $this->$model->insertPayment($pay_array);
			  
			   //   echo $insPayment;exit;
			}
			else{
				//update if offline record is with cancelled status
				$upd_array = array ( "payment_status" 	=> 2,
			   						"receipt_jil" 		=> $payData['receipt_jil'],
			   						"remark" 			=> $payData['remark'],
			   						"date_upd" 			=> date('Y-m-d H:i:s'),
			   						"payment_ref_number"=> $payData['brefno']
			    					);	
			    $updPayment  = $this->$model->updatePayment($upd_array);
			
			}
		   }
	   }	
	   return true;
	
	}
	
	//insert bulk offline payments 
	
	public function insertOfflinePayment_post()
	{
	
		$offlinePayments = json_decode(file_get_contents('php://input'));
		//$offlinePayments = (object) $_POST;
	    $login = $offlinePayments->login;
				
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
					  
		if($this->checklogin($key))
		{		   
			$model = self::MOD_API;
				
			$payments = $offlinePayments->data;
			$records = 0;
			$invalid = 0;
			if($payments)
			{
				$total_records = count($payments);
				
				foreach($payments as $payment)
				{
					$branch = $this->branch;
					/* $branches = $this->config->item('id_branch');
					foreach($branches as $key => $value){
						if($key ==  $payment->id_branch){
							$branch = $value;
						}			
					} */
					
					$data = array(	'ac_name' 			=> $payment->ac_name,	
									'brefno'			=> 'JIL-'.$payment->brefno,
									'clientid' 		 	=> $payment->clientid,	
									'date_payment'		=> $payment->date_payment,
									'discountAmt' 		=> $payment->discountAmt,
									'id_branch' 		=> $branch,					
									'id_metal' 		 	=> ($payment->id_metal == 'G' ? '1' : ($payment->id_metal == 'S' ? '2':'3')),	
									'is_trans_completed'=> 'N',
									'metal_rate' 		=> $payment->metal_rate,
									'metal_weight'	 	=> $payment->metal_weight,	
									'mobile' 		 	=> $payment->mobile,
									'payment_amount' 	=> $payment->payment_amount,		
									'payment_mode' 		=> $payment->payment_mode,	
									'receipt_jil'	 	=> $payment->receipt_jil,
									'remark' 			=> $payment->remark,	
									'scheme_ac_number' 	=> $payment->scheme_ac_number,
									'status'			=> $payment->status,
									
									'instalment'		=> $payment->instalment,
									'transfer_date'		=> $payment->transfer_date									
								);
					if($this->config->item('no_branch') == 1){
					    $check = array( 'brefno' 			=> $data['brefno'],
									'scheme_ac_number' 	=> $data['scheme_ac_number'],
									'transfer_date'		=> $data['transfer_date'],
									'payment_amount' 	=> $data['payment_amount'],
									'instalment'		=> $data['instalment'],
									'date_payment' 	 	=> $data['date_payment']);
					}else{
					    $check = array( 'id_branch' 		=> $data['id_branch'],
									'brefno' 			=> $data['brefno'],
									'scheme_ac_number' 	=> $data['scheme_ac_number'],
									'transfer_date'		=> $data['transfer_date'],
									'payment_amount' 	=> $data['payment_amount'],
									'instalment'		=> $data['instalment'],
									'date_payment' 	 	=> $data['date_payment']);
					}
					
									
				   $valid =$this->validatePayRec($check); 
					
					if($valid['status'])
					{	
						$isBrefExists = $this->$model->checkBref($data['brefno']);	
						if(!$isBrefExists)
						{
						  $status  = $this->$model->insert_offlinePayment($data);	
						   if($status ===FALSE )	
						   {
							    $r[] = array('clientid' 		=> $data['clientid'],
											 'brefno'			=> $data['brefno'],
											 
											 'date_payment' 	=> $data['date_payment'],
											 
											 'receipt_jil' 		=> $data['receipt_jil'],
											 'isSucceeded'		=> FALSE,
											 'result'			=> 'Unable to proceed the requested operation');	
						   }
                           else
                           {							   
							   $r[] = array('clientid' 		=> $data['clientid'],
											'brefno'			=> $data['brefno'], 			
											'date_payment' 	 	=> $data['date_payment'],
											
											'receipt_jil' 		=> $data['receipt_jil'],
											 
											'isSucceeded'		=> TRUE,
											'result' 			=> 'Inserted successfully');	
						   }	
						}
						else
                        {							   
							   $r[] = array('clientid' 		=> $data['clientid'],
											'brefno'			=> $data['brefno'], 
											
											'date_payment' 	 	=> $data['date_payment'],	
											
											'receipt_jil' 		=> $data['receipt_jil'],		
											'isSucceeded'		=> FALSE,
											'result' 			=> 'brefno already exist');	
						}	
					}
                    else
                    {
                    
						$r[] = array('clientid' 		=> $data['clientid'],
									 'brefno'			=> $data['brefno'],
									 
									'date_payment' 	 	=> $data['date_payment'],	
									
									'receipt_jil' 		=> $data['receipt_jil'],
									 'isSucceeded' 		=> FALSE,
									 "result" 			=> $valid['error']);
					}
                }
 				//  $this->insertTransInPayment(); // to insert offline transactions in payment table(main db)
				  $this->response($r,200);
			}	
						
			else
			{
			$this->response(array('message' => 'No records to proceed the requested operation'), 200);
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}		
	}
	
	public function insertClosedAccData_post()
	{
		$chitData = json_decode(file_get_contents('php://input'));
		$login = $chitData;
		
		$branch = $this->branch;
		/* $branches = $this->config->item('id_branch');
		foreach($branches as $key => $value){
			if($key ==   $login->id_branch){
				$branch = $value;
			}			
		} */
		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
					  
		if($this->checklogin($key))
		{		   
			$model = self::MOD_API;				
			$closed_schemes = $chitData->data;
			$records = 0;
			$invalid = 0;
			if($closed_schemes)
			{
				$total_records = count($closed_schemes);
				
				foreach($closed_schemes as $closed_scheme)
				{
					$data = array(	
									'clientid' 	 		=> $closed_scheme->clientid,		
									'group_code' 		=> $closed_scheme->group_code,	
									'scheme_acc_no' 	=> $closed_scheme->scheme_acc_no,
									'remark' 			=> $closed_scheme->remark,
									'closing_date' 		=> $closed_scheme->closing_date,
									'closing_balance' 	=> $closed_scheme->closing_balance,
									'closing_weight'  	=> $closed_scheme->closing_weight,
									'add_charges' 	  	=> $closed_scheme->add_charges,
									'additional_benefits' => $closed_scheme->additional_benefits,
									'closed_by' 	  	=> $closed_scheme->closed_by,
									'rep_name'		  	=> $closed_scheme->rep_name,
									'rep_mobile'	  	=> $closed_scheme->rep_mobile
									//,'transfer_date' => $closed_scheme->transfer_date	
								);
					if($branch != ""){
						$data['id_branch'] = $branch;
					}	
					$check = array(	'closing_balance' 	=> $data['closing_balance'],	
									'clientid' 	 		=> $data['clientid'],	
									'scheme_acc_no' 	=> $data['scheme_acc_no'],	
									'group_code' 	 	=> $data['group_code'],	
									'closing_date' 	 	=> $data['closing_date']);
				    $valid =$this->validateclosingRecords($check);
                     				 
					
					if($valid['status'])
					{	
						/* $isExists = $this->IsNewCustomerExist($data); 
						if(!$isExists)
						{		*/
						  	
						  $status  = $this->$model->insert_chitClosingData($data);	
							
						   if($status ===FALSE )	
						   {
							    $r[] = array('clientid'		 => $data['clientid'],			
											 'isSucceeded'	 => FALSE,
											 'result' 		 => 'Invalid Data ,unable to proceed the requested operation');	
						   }
                           else
                           {							   
							   $r[] = array('clientid' 		=> $data['clientid'],			
											'isSucceeded'	=> TRUE,
											'result' 		=> 'Inserted successfully');	
						   }	
					}
                    else
                    {
						$r[] = array('clientid' 	=> $data['clientid'],			
									 'isSucceeded'  => FALSE,
									 "result" 		=> $valid['error']);
					}
                }
				  $this->response($r,200);
			}	
						
			else
			{
				$this->response(array('message' => 'No records to proceed the requested operation'), 200);
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}		
	}
	
	
	
}
?>