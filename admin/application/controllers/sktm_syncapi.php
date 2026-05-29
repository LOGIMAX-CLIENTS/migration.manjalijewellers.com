<?php
require(APPPATH.'libraries/REST_Controller.php');
class Sktm_syncapi extends REST_Controller
{
	const MOD_API = "sktm_syncapi_model";
	
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
   
   function validateClosingRecords($data)
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
			    
				if(empty($data['is_closed'])){
					$status = FALSE;
					$error.= "is_closed has invalid value ".$data['is_closed'].". "; 
				}
				if(empty($data['closing_amount'])){
					$status = FALSE;
					$error.= "closing_amount has invalid value ".$data['closing_amount'].". "; 
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
    function validateData($data)
	{  
	  $error=NULL;
	  $status = TRUE;
       
		  $isEmpty = $this->isArrayEmpty($data);
			if(!$isEmpty['status'])
			{

			   if(!$this->validateDate($data['date_update']))
				{
					$status = FALSE;
					$error.= "transfer_date has invalid date format ".$data['transfer_date'].", valid date format (YYYY-MM-DD). "; 
				}
			   if(!$this->validateDate($data['receipt_date']))
				{
					$status = FALSE;
					$error.= "receipt_date has invalid date format ".$data['receipt_date'].", valid date format (YYYY-MM-DD). "; 
				}
			   
			   if(!$this->isValidStatus($data['is_updated']))
				{
					$status = FALSE;
					$error.= "is_updated has invalid value ".$data['is_updated'].". "; 
				}
				if(empty($data['receipt_no'])){
					$status = FALSE;
					$error.= "receipt_no has invalid value ".$data['receipt_no'].". "; 
				}
				 					
			}
			else
			{
				$status = FALSE;
				$error = $isEmpty['errors']; 
			}	
	   
        
       return array('status' => $status,'error' => $error);		
	}    
	function validateregRecords($data)
	{  
	  $error=NULL;
	  $status = TRUE;
       
		  $isEmpty = $this->isArrayEmpty($data);
			if(!$isEmpty['status'])
			{

			   if(!$this->validateDate($data['date_update']))
				{
					$status = FALSE;
					$error.= "transfer_date has invalid date format ".$data['transfer_date'].", valid date format (YYYY-MM-DD). "; 
				}
			   
			   if(!$this->isValidStatus($data['is_updated']))
				{
					$status = FALSE;
					$error.= "is_updated has invalid value ".$data['is_updated'].". "; 
				}
				if(empty($data['group_cus_no'])){
					$status = FALSE;
					$error.= "group_cus_no has invalid value ".$data['group_cus_no'].". "; 
				}
				if(empty($data['group_name'])){
					$status = FALSE;
					$error.= "group_name has invalid value ".$data['group_name'].". "; 
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
		
		$key = array('username' => $login->username,
		              'passwd' => $login->passwd); 
		$branch_code = $login->branch;
		
		if($this->checklogin($key))
		{		
	      
			$model = self::MOD_API;
			$transactions = $this->$model->getTransactionAll($branch_code);
			
			foreach($transactions as $transaction)
			{
				//$customerPaidAmount=($transaction["amount"]-$transaction["discountAmt"]);
				
				$trans[]= array(	"id_transaction" => (int)    $transaction["id_transaction"],
									"entrydate" 	 => (string) $transaction["date_paid"],
									"group_name" 	 => (string) $transaction["group_name"],
									"scheme_type" 	 => (string) $transaction["scheme_type"],	
									"group_cus_no" 	 => (string) $transaction["group_cus_no"],	
									"receipt_no" 	 => (string) $transaction["receipt_no"],
									"receipt_date" 	 => (string) $transaction["receipt_date"],
									"date_paid" 	 => (string) $transaction["date_paid"],
									"branch" 		 => (string) $transaction["branch"],
									"amount" 		 => number_format((float)$transaction["amount"], 2, '.', ''),
									"weight" 		 => number_format((float)$transaction["weight"], 3, '.', '') ,
									"gold_rate" 	 => number_format((float)$transaction["gold_rate"], 3, '.', ''),
									"cash_type" 	 => (string) $transaction["cash_type"],													"ref_no" 		 => (int)    $transaction["ref_no"],
									"is_updated" 	 =>(string)  $transaction["is_updated" ],
									"is_transferred" =>(string)  $transaction["is_transferred" ],
									"transfer_date" 	 => (string) $transaction["transfer_date"],		
									"id_scheme_account"  => (int) $transaction["id_scheme_account"]);
								
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
	public function transactionsByStatus_obj_post()
	{
	
		$credentials = file_get_contents('php://input');
		// $login = json_decode($credentials);
		$login=(object) $_POST;
		$key = array('username' => $login->username,
		              'passwd' => $login->passwd); 
		$branch_code = $login->branch; 
		/*echo $branch_code;
		
		print_r($key);exit;*/

		if($this->checklogin($key))
		{		
			$status = $login->status;
			$model = self::MOD_API;
			$transactions = $this->$model->getTransactionByTranStatus($status,$branch_code);
			foreach($transactions as $transaction)
			{				
				$trans[]= array("id_transaction" => (int)    $transaction["id_transaction"],
								"entrydate" 	 => (string) $transaction["date_paid"],
								"group_name" 	 => (string) $transaction["group_name"],
								"scheme_type" 	 => (string) $transaction["scheme_type"],	
								"group_cus_no" 	 => (string) $transaction["group_cus_no"],	
								"receipt_no" 	 => (string) $transaction["receipt_no"],
								"receipt_date" 	 => (string) $transaction["receipt_date"],	
								"date_paid" 	 => (string) $transaction["date_paid"],
								"branch" 		 => (string) $transaction["branch"],
								"amount" 		 => number_format((float)$transaction["amount"], 2, '.', ''),
								"weight" 		 => number_format((float)$transaction["weight"], 3, '.', '') ,
								"gold_rate" 	 => number_format((float)$transaction["gold_rate"], 3, '.', ''),
								"cash_type" 	 => (string) $transaction["cash_type"],															"ref_no" 		 => (int)    $transaction["ref_no"],
								"is_updated" 	 =>(string)  $transaction["is_updated" ],
								"is_transferred" =>(string)  $transaction["is_transferred" ],
								"transfer_date" 	 => (string) $transaction["transfer_date"],		
								"id_scheme_account"  => (int) $transaction["id_scheme_account"]);
							
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
			$this->response('Invalid credentials!', 401);
		}	
	}
	//To get transactions by status
	public function transactionsByStatus_post()
	{
	
		$credentials = file_get_contents('php://input');
		 $login = json_decode($credentials);
		//$login=(object) $_POST;
		$key = array('username' => $login->username,
		              'passwd' => $login->passwd); 
		$branch_code = $login->branch; 
		if($this->checklogin($key))
		{		
			$status = $login->status;
			$model = self::MOD_API;
			$transactions = $this->$model->getTransactionByTranStatus($status,$branch_code);
			foreach($transactions as $transaction)
			{				
				$trans[]= array("id_transaction" => (int)    $transaction["id_transaction"],
								"entrydate" 	 => (string) $transaction["date_paid"],
								"group_name" 	 => (string) $transaction["group_name"],
								"scheme_type" 	 => (string) $transaction["scheme_type"],	
								"group_cus_no" 	 => (string) $transaction["group_cus_no"],	
								"receipt_no" 	 => (string) $transaction["receipt_no"],
								"receipt_date" 	 => (string) $transaction["receipt_date"],	
								"date_paid" 	 => (string) $transaction["date_paid"],
								"branch" 		 => (string) $transaction["branch"],
								"amount" 		 => number_format((float)$transaction["amount"], 2, '.', ''),
								"weight" 		 => number_format((float)$transaction["weight"], 3, '.', '') ,
								"gold_rate" 	 => number_format((float)$transaction["gold_rate"], 3, '.', ''),
								"cash_type" 	 => (string) $transaction["cash_type"],															"ref_no" 		 => (int)    $transaction["ref_no"],
								"is_updated" 	 =>(string)  $transaction["is_updated" ],
								"is_transferred" =>(string)  $transaction["is_transferred" ],
								"transfer_date" 	 => (string) $transaction["transfer_date"],		
								"id_scheme_account"  => (int) $transaction["id_scheme_account"]);
							
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
			$this->response('Invalid credentials!', 401);
		}	
	}
	
	
	
	//update transaction single record
	public function updateTransaction_post()
	{
		
		$transaction = json_decode(file_get_contents('php://input'));
			
		$key = array('username' => $transaction->login->username,
		              'passwd' => $transaction->login->passwd); 
		$branch_code = $transaction->login->branch;  			  
		if($this->checklogin($key))
		{	
			$model = self::MOD_API;
			$trans = (array)$transaction->data;
		
			$valid =$this->validateRecords($trans);
		
			if($valid['status'])
			{
					$data = array('is_transferred'  => $trans['is_transferred'],
						          'transfer_date' => $trans['transfer_date']); 						  
				    $id   = $trans['id_transaction']; 
		
					$status = $this->$model->update_transaction($data,$id,$branch_code);
				
			
					 if($status === FALSE)
					{
						
							$r = array('id_transaction' =>$trans['id_transaction'],
								   'branch' => $branch_code,
								   'isSucceeded' => FALSE,
								   'result' => 'Unable to proceed the requested operation');
					}         
					else
					{
						$r = array('id_transaction' =>$trans['id_transaction'],
								   'branch' => $branch_code,
								   'isSucceeded' => TRUE,
								   'result' => 'Updated Successfully');
						$this->response($r,200);
						
					} 		
								
			}
            else
            {
				$r = array(	'id_transaction' =>$trans['id_transaction'],
							'$branch_code' => $branch_code,
							'isSucceeded' => FALSE,
							"result" =>$valid['error']);
				$this->response($r,401);
			} 
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}	
	}
	
	//update transaction single record data
	public function updateTransactionData_post()
	{
		
		$transaction = json_decode(file_get_contents('php://input'));
		
		$key = array('username' => $transaction->login->username,
		              'passwd' => $transaction->login->passwd); 
		$branch_code = $transaction->login->branch;  			  
		if($this->checklogin($key))
		{	
			$model = self::MOD_API;
			$trans = (array)$transaction->data;
		
			$valid =$this->validateData($trans);
		
			if($valid['status'])
			{
					$data = array('is_updated'  => $trans['is_updated'],
						          'date_update' => $trans['date_update'],					  
						          'receipt_no' => $trans['receipt_no'],				  
						          'receipt_date' => $trans['receipt_date']);						  
				    $id   = $trans['id_transaction']; 
		
					$status = $this->$model->update_transaction($data,$id,$branch_code);				
			
					 if($status === FALSE)
					{
						
							$r = array('id_transaction' =>$trans['id_transaction'],
								   'isSucceeded' => FALSE,
								   'result' => 'Unable to proceed the requested operation');
					}         
					else
					{
						$id_sch_ac = $this->$model->get_transData($id);	
						
						if($id_sch_ac){
							$updData = $this->$model->update_paymentData($trans['receipt_no'],$id_sch_ac);							}
						$r = array('id_transaction' =>$trans['id_transaction'],
								   'isSucceeded' => TRUE,
								   'result' => 'Updated Successfully');
						$this->response($r,200);
						
					} 		
								
			}
            else
            {
				$r = array(	'id_transaction' =>$trans['id_transaction'],
							'isSucceeded' => FALSE,
							"result" =>$valid['error']);
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
	    $login =  $trans_data->login; 
        $transactions = $trans_data->data;
		
		$key = array('username' => $trans_data->login->username,
		              'passwd' => $trans_data->login->passwd); 					  
		$branch_code = $login->branch;  
		
		if($this->checklogin($key))
		{	
			$model = self::MOD_API;
			$records = 0;
			
			if($transactions)
			{
		
				$records = count($transactions);
				foreach($transactions as $tran)
				{
					$trans =array( 'id_transaction' => $tran->id_transaction,
					               'is_transferred' => $tran->is_transferred,
					               'transfer_date' => $tran->transfer_date);
								   
					$valid =$this->validateRecords($trans);
					
						if($valid['status'])
						{
								$data = array('is_transferred'  => strtoupper($trans['is_transferred']),
											  'transfer_date' => $trans['transfer_date']); 						  
								$id   = $trans['id_transaction']; 
					          
								$status = $this->$model->update_transaction($data,$id,$branch_code);
							  
							 
						 
								 if($status ===FALSE)
								{
									$r[] = array('id_transaction' =>$trans['id_transaction'],
											   'isSucceeded' => FALSE,
											   'result' => 'Unable to proceed the requested operation');
									//$this->response($r,200);
									
								}         
								else
								{
									$r[] = array('id_transaction' =>$trans['id_transaction'],
										   'isSucceeded' => TRUE,
										   'result' => 'Updated Successfully');
									
									//$this->response('', 404);
								} 	
							
											
						}
						else
						{
							$r[] = array(	'id_transaction' =>$trans['id_transaction'],
										'isSucceeded' => FALSE,
										"result" =>$valid['error']);
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
	
	//To update multiple transactions data (bulk)
	public function updateTransactionsData_post()
	{
		$trans_data = json_decode(file_get_contents('php://input'));
	    $login =  $trans_data->login; 
        $transactions = $trans_data->data;
		
		$key = array('username' => $trans_data->login->username,
		              'passwd' => $trans_data->login->passwd); 					  
		$branch_code = $login->branch;  
		
		if($this->checklogin($key))
		{	
			$model = self::MOD_API;
			//$records = 0;
			
			if($transactions)
			{
			
		
		//		$records = count($transactions);
				foreach($transactions as $trans)
				{
					
					/*$tran = array('is_updated'  => $trans['is_updated'],
					          'date_update' => $trans['date_update'],					  
					           'receipt_no' => $trans['receipt_no'],				  
					           'receipt_date' => $trans['receipt_date']); */			   
					$tran = array('is_updated'  => $trans->is_updated,
					          'date_update' => $trans->date_update,					  
					           'receipt_no' => $trans->receipt_no,				  
					           'id_transaction' => $trans->id_transaction,				  
					           'receipt_date' => $trans->receipt_date);  			   
					$valid =$this->validateData($tran);
					
						if($valid['status'])
						{
								
								$id   = $tran['id_transaction']; 
					          
								$status = $this->$model->update_transaction($tran,$id,$branch_code);
							  
							
						 
								 if($status ===FALSE)
								{
									$r[] = array('id_transaction' =>$tran['id_transaction'],
											   'isSucceeded' => FALSE,
											   'result' => 'Unable to proceed the requested operation');
									//$this->response($r,200);
									
								}         
								else
								{
									$id_sch_ac = $this->$model->get_transData($id);	
									if($id_sch_ac){
										$updData = $this->$model->update_paymentData($trans->receipt_no,$id_sch_ac);							}
									$r[] = array('id_transaction' =>$tran['id_transaction'],
										   'isSucceeded' => TRUE,
										   'result' => 'Updated Successfully');
									
									//$this->response('', 404);
								} 	
							
											
						}
						else
						{
							$r[] = array(	'id_transaction' =>$tran['id_transaction'],
										'isSucceeded' => FALSE,
										"result" =>$valid['error']);
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
	/*public function updateTransactionsByRange_post()
	{
		$transaction = json_decode(file_get_contents('php://input'));
	
	
		$key = array('username' => $transaction->login->username,
		              'passwd' => $transaction->login->passwd); 
					  
		if($this->checklogin($key))
		{	
			$model = self::MOD_API;
			
			$trans   =  $transaction->data;
			$upperlimit    = $trans->upperlimit;
			$lowerlimit    = $trans->lowerlimit;
			$trans_data    =array(  "is_transferred" => $trans->is_transferred,
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
	}	*/	
	

	
	 

	//Registration
	public function registrations_post()
	{
		$credentials = file_get_contents('php://input');
		$login = json_decode($credentials);
		
		$key = array('username' => $login->username,
		              'passwd' => $login->passwd); 
		$branch_code = $login->branch;
		
		if($this->checklogin($key))
		{		
			$model = self::MOD_API;
		
			$registrations = $this->$model->getCustomerRegAll($branch_code);
			foreach($registrations as $reg)
			{
				$reg_records[]=array("id_customer_reg"	 => (int)  $reg["id_customer_reg"],
									"entrydate"			 => (string) $reg["entrydate"],
									"name"				 =>	(string) $reg["name"],
									"address1"			 =>	(string) $reg["address1"],
									"address2"			 =>	(string) $reg["address2"],
									"address3"	 		 =>	(string) $reg["address3"],
									"city_name"			 =>	(string) $reg["city_name"],
									"mobile_no"			 =>	(string) $reg["mobie_no"],
									"email"				 =>	(string) $reg["email"],
									"id_scheme_account"	 =>	(string) $reg["id_scheme_account"],
									"ref_no"			 =>	(string) $reg["ref_no"],
									"is_transferred"	 =>	(string) $reg["is_transferred"],
									"branch"			 =>	(string) $reg["branch"]	,
									"mode"				 =>	(string) $reg["mode"],
									"scheme_type"		 =>	(string) $reg["scheme_type"],
									"is_updated"		 =>	(string) $reg["is_updated"]	,
									"amount"			 =>	(string) $reg["amount"]);
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
		$credentials = file_get_contents('php://input');
		$login = json_decode($credentials);
		
	
		$key = array('username' => $login->username,
		              'passwd' => $login->passwd); 
		$branch_code = $login->branch;
					  
		if($this->checklogin($key))
		{		
	      $status = $login->status;  
		  
			$model = self::MOD_API;
			$registrations = $this->$model->getCustomerRegByTranStatus($status,$branch_code);
			
			foreach($registrations as $reg)
			{
				$reg_records[]=array("id_customer_reg"	 => (int)  $reg["id_customer_reg"],
									"entrydate"			 => (string) $reg["entrydate"],
									"name"				 =>	(string) $reg["name"],
									"address1"			 =>	(string) $reg["address1"],
									"address2"			 =>	(string) $reg["address2"],
									"address3"	 		 =>	(string) $reg["address3"],
									"city_name"			 =>	(string) $reg["city_name"],
									"mobile_no"			 =>	(string) $reg["mobie_no"],
									"email"				 =>	(string) $reg["email"],
									"id_scheme_account"	 =>	(string) $reg["id_scheme_account"],
									"ref_no"			 =>	(string) $reg["ref_no"],
									"is_transferred"	 =>	(string) $reg["is_transferred"],
									"branch"			 =>	(string) $reg["branch"]	,
									"mode"				 =>	(string) $reg["mode"],
									"scheme_type"		 =>	(string) $reg["scheme_type"],
									"is_updated"		 =>	(string) $reg["is_updated"]	,
									"amount"			 =>	(string) $reg["amount"]);
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
	
	//To get registrations by date
	/*public function registrationsByDate_get()
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
	}*/
	
	
		//update registration single record
	public function updateRegistration_post()
	{
		
		$registrations = json_decode(file_get_contents('php://input'));
		$login = $registrations->login;
		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
		$branch_code = $login->branch;
					  
		if($this->checklogin($key))
		{	
		    $model = self::MOD_API;
		
			$reg =  (array) $registrations->data;
		   $valid =$this->validateRecords($reg);
		   if($valid['status'])
			{				
				$data = array('is_transferred'  => $reg['is_transferred'],
							  'transfer_date' => $reg['transfer_date']); 
							  
				$id  = $reg['id_customer_reg']; 
				$status = $this->$model->update_CustomerReg($data,$id,$branch_code);
				
			 
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
	
	public function updateRegistrationData_post()
	{
		
		$registrations = json_decode(file_get_contents('php://input'));
		$login = $registrations->login;
		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
		$branch_code = $login->branch;
					  
		if($this->checklogin($key))
		{	
		    $model = self::MOD_API;
		
			$reg =  (array) $registrations->data;
			$validate  = array('is_updated'  => $reg['is_updated'],
				          'date_update' => $reg['date_update'],				  
				          'group_cus_no' => $reg['group_cus_no'],
				          'client_id' => $reg['client_id'],
				          'group_name' => $reg['group_name']); 
		   $valid =$this->validateregRecords($validate);
		   if($valid['status'])
			{				
				$data = array('is_updated'  => $reg['is_updated'],
					          'date_update' => $reg['date_update'],				  
					          'group_cus_no' => $reg['group_cus_no'],
					          'client_id' => $reg['client_id'],
					          'group_name' => $reg['group_name']); 
							  
				$id  = $reg['id_customer_reg']; 
				$status = $this->$model->update_CustomerReg($data,$id,$branch_code);
				
			 
				 if($status === FALSE)
				{
					$r = array('id_customer_reg' =>$reg['id_customer_reg'],
								   'isSucceeded' => FALSE,
								   'result' => 'Unable to proceed the request operation');
						$this->response($r,200);
				}
				 
				else
				{
					$cus = $this->$model->get_cusRegData($reg['id_customer_reg']);
					$trans = array('group_name' => $reg['group_name'],				  
								   'group_cus_no' => $reg['group_cus_no']);	
					$upd_acdata = $this->$model->update_transactionsByrefno($trans,$cus['ref_no']);
					if($cus['id_scheme_account']){
					    $trans['ref_no'] = $reg['client_id'];
						$updData = $this->$model->update_schemeAccNo($trans,$cus['id_scheme_account']);	
					}
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
	
	public function updateRegistrations_post()
	{
		$registrations = json_decode(file_get_contents('php://input'));
	
		$login = $registrations->login;
		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
		$branch_code = $login->branch;			  
		
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
						                'is_transferred'  => $reg->is_transferred,
										'transfer_date' => $reg->transfer_date); 
							
					    $valid =$this->validateRecords($reg);
						if($valid['status'])
						{
							$id    = $reg['id_customer_reg'] ; 		
							$data  = array('is_transferred'  => $reg['is_transferred'],
										   'transfer_date' => $reg['transfer_date']); 
										   
										   
							$status = $this->$model->update_CustomerReg($data,$id,$branch_code);
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
	
	public function updateRegistrationsData_post()
	{
		$registrations = json_decode(file_get_contents('php://input'));
	
		$login = $registrations->login;
		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
		$branch_code = $login->branch;			  
		
		if($this->checklogin($key))
		{	
			$model = self::MOD_API;
			$regs = $registrations->data;
		//	$records = 0;
			if($regs)
			{
				//$records = count($regs);
			
					foreach($regs as $reg)
					{
			
						$reg  = array('id_customer_reg' => $reg->id_customer_reg ,
						                'client_id' => $reg->client_id,
						                'is_updated'  => $reg->is_updated,
						                'group_name'  => $reg->group_name,
						                'group_cus_no'  => $reg->group_cus_no,
										'date_update' => $reg->date_update); 
							
					    $valid =$this->validateregRecords($reg);
						if($valid['status'])
						{
							$id    = $reg['id_customer_reg'] ; 		
						
							$data = array('is_updated'  => $reg['is_updated'],
							               'client_id' => $reg['client_id'],
								          'date_update' => $reg['date_update'],			  
								          'group_name' => $reg['group_name'],				  
								          'group_cus_no' => $reg['group_cus_no']);				   
										   
							$status = $this->$model->update_CustomerReg($data,$id,$branch_code);
							if($status ===FALSE)
								{
									/*$trans = array('group_name' => $reg['group_name'],				  
								       			   'group_cus_no' => $reg['group_cus_no']);	
									$upd_acdata = $this->$model->update_transaction($trans,$ref_no);*/
									$r[] = array('id_customer_reg' =>$reg['id_customer_reg'],
											   'isSucceeded' => FALSE,
											   'result' => 'Unable to proceed the requested operation');																		
								}         
								else
								{
									$cus = $this->$model->get_cusRegData($reg['id_customer_reg']);
									$trans = array('group_name' => $reg['group_name'],
												   'group_cus_no' => $reg['group_cus_no']);	
									$upd_acdata = $this->$model->update_transactionsByrefno($trans,$cus['ref_no']);
									if($cus['id_scheme_account']){
									    $trans['ref_no'] = $reg['client_id'];
										$updData = $this->$model->update_schemeAccNo($trans,$cus['id_scheme_account']);	
									}
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
	
	
	/*public function updateRegistrationsByRange_post()
	{
		
		$registrations = json_decode(file_get_contents('php://input'));
	
		$login = $registrations->login;
		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
		$branch_code = $login->branch;		
					  
		if($this->checklogin($key))
		{	
			$model = self::MOD_API;
			
			$reg = $registrations->data;
		
		
			$upperlimit    = $reg->upperlimit;
			$lowerlimit    = $reg->lowerlimit;
			$trans_data    = array("transfer_date"=>$reg->transfer_date,
									"is_transferred"=>	$reg->is_transferred);
						
			
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
	}*/		
	
	
	
	
	

	
	public function IsNewCustomerExist($data)
	{
		$model = self::MOD_API;
		return $this->$model->checkNewCustomer($data);
	}
	
	// Update closed accounts - single 
	public function updateClosedAccount_post()
	{
		
		$registrations = json_decode(file_get_contents('php://input'));
		$login = $registrations->login;
		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
		$branch_code = $login->branch;
					  
		if($this->checklogin($key))
		{	
		    $model = self::MOD_API;
		
			$reg =  (array) $registrations->data;
			
		    $validate = array('is_closed'          => $reg['is_closed'],
				          'closing_date'    => $reg['closing_date'],
				          'closing_amount'  => $reg['closing_amount'],
				          'id_customer_reg' => $reg['id_customer_reg']
				          ); 
			 $valid = $this->validateClosingRecords($validate);
			 
		   if($valid['status'])
			{	
				$data = array('is_closed'       => $reg['is_closed'],
					          'date_update'     => $reg['date_update'],				  
					          'closed_by'       => $reg['closed_by'],  // '0 - self, 1 - representative'
					          'closing_date'    => $reg['closing_date'],
					          'closing_amount'  => $reg['closing_amount'],
					          'closing_weight'  => $reg['closing_weight'],
					          'closing_add_chgs'=> $reg['closing_add_chgs'],
					          'remark_close'    => $reg['remark_close'],
					          'additional_benefits' => $reg['additional_benefits']); 
							  
				$id  = $reg['id_customer_reg']; 
				$status = $this->$model->update_CustomerReg($data,$id,$branch_code);
				
			 
				 if($status === FALSE)
				{
					$r = array('id_customer_reg' =>$reg['id_customer_reg'],
								   'isSucceeded' => FALSE,
								   'result' => 'Unable to proceed the request operation');
						$this->response($r,200);
				}
				 
				else
				{
					$cus = $this->$model->get_cusRegData($reg['id_customer_reg']); 
					if($cus['id_scheme_account']){
				    	$schData = array('is_closed'        => $reg['is_closed'],
            					          'date_upd'     => $reg['date_update'],				  
            					          'closed_by'       => $reg['closed_by'],  // '0 - self, 1 - representative'
            					          'closing_date'    => $reg['closing_date'],
            					          'closing_balance' => $reg['closing_amount'],
            					          'closing_weight'  => $reg['closing_weight'],
            					          'closing_add_chgs'=> $reg['closing_add_chgs'],
            					          'active'          => ($reg['is_closed'] == 1 ? 0 :1),
            					          'remark_close'    => $reg['remark_close']);
            					          
						$updData = $this->$model->update_schemeAccount($schData,$cus['id_scheme_account']);	
					}
					$r = array('id_customer_reg' =>$reg['id_customer_reg'],
								   'isSucceeded' => TRUE,
								   'result' => "Updated successfully");
						$this->response($r,200);
				} 
			}
            else
            {
				$r = array(	'id_customer_reg' =>$reg['id_customer_reg'],
							'isSucceeded' => FALSE,
							"error" =>$valid['error']);
				$this->response($r,200);
			} 
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}		
		
	}
	
	
	
}

?>