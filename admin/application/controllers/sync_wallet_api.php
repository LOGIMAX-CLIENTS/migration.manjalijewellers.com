<?php
require(APPPATH.'libraries/REST_Controller.php');
class Sync_wallet_api extends REST_Controller
{
	const MOD_API = "sync_walletapi_model";
	const SET_MODEL = 'admin_settings_model';
	const ADM_MODEL = "chitadmin_model";
	
	function __construct()
	{
		parent::__construct();
		$this->load->model(self::MOD_API);
		$this->load->model(self::SET_MODEL);
		$this->load->model(self::ADM_MODEL);
		ini_set('date.timezone', 'Asia/Calcutta');
		
		$this->branch = array(1,2,3,4,5,6,7,8);
				
	}
	
	
	function IsNullOrEmptyString($str){
        return (!isset($str) || trim($str) === '');
    }
    
    function findArrayIdxMulti($array, $sval1, $sval2, $sval3) {
        foreach ($array as $k => $val) { 
            if ($val['mobile'] == $sval1 && $val['bill_no'] == $sval2  && $val['category_code'] == $sval3) {
               return $k;
            }
        }
        return -1;
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
    function validateTransRec($data)
	{  
	  $error=NULL;
	  $status = TRUE;
       
		  $isEmpty = $this->isArrayEmpty($data);
			if(!$isEmpty['status'])
			{
			   if(!$this->validateDate($data['entry_date']))
				{
					$status = FALSE;
					$error.= "entry_date has invalid date format ".$data['entry_date'].", valid date format (YYYY-MM-DD). "; 
				}
			   				 					
			}
			else
			{
				$status = FALSE;
				$error = $isEmpty['errors']; 
			}	
	   
        
       return array('status' => $status,'error' => $error);		
	}
		
	//To get wallet transactions by status
	public function walletTransByStatus_post()
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
			$transactions = $this->$model->getWalletTransByStatus($status,$branch,$record_to);
			foreach($transactions as $transaction)
			{
				$trans[]= array(	"id_inter_wallet_trans" => (int) $transaction["id_inter_wallet_trans"],
									"trans_type" 	 		=> (string)$transaction["trans_type"],
				                  //  "record_to" 	 		=> (string) $transaction["record_to"],
				                    "is_modified" 	 		=> (string) $transaction["is_modified"],
				                    "mobile" 	 	 		=> (string) $transaction["mobile"],
				                    "bill_no" 	 	 		=> (string) $transaction["bill_no"],
				                    "id_branch" 	 		=> (string) $transaction["id_branch"],
				                    "category_code"  		=> (string) $transaction["category_code"],
				                    "amount"  		 		=> (string) $transaction["amount"],
									"trans_points" 	 		=> (string)$transaction["trans_points"],
									"use_points" 	 		=> (string)$transaction["use_points"],
									"redeem_req_pts" 		=> (string)$transaction["redeem_req_pts"],
									"current_wallet_points" => (string)$transaction["current_wallet_points"],
									"allowed_redeem" 		=> (string)$transaction["allowed_redeem"],
									"entry_date" 			=> (string)$transaction["entry_date"],
									"is_transferred" 		=> (string)$transaction["is_transferred"],
									"remark" 				=> (string)$transaction["remark"],
									"responseData" 			=> 1  // for sync reference
									);
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
	
	
	public function getDateTime_get()
	{
	    $trans['date'] = date('Y-m-d H:i:s');
		$this->response($trans, 200);
	}	
	
	
	
    //To update multiple wallet transactions (bulk)
	
	
	public function updateWalletTrans_post()
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
			          $data = array( 	'bill_no'	 	 		=> $tran->bill_no,
                    					'is_transferred' 		=> $tran->is_transferred, 
                    					"last_update"	 		=> date('Y-m-d H:i:s'),
                    					'transfer_date'	 		=> $tran->transfer_date);
                      $valid = $this->validateRecords($data);
			      }
			      else{
			         $valid['status'] = false ; 
			         $valid['error'] = "Invalid type";
			      }

					if($valid['status'])
					{
						$trans['id_branch'] = $branch;
						$trans['bill_no'] = $tran->bill_no;
						$status = $this->$model->updateData($data,$trans,'inter_wallet_trans');
				 
						 if($status ===FALSE)
						{
							$r[] = array('bill_no' 			=> $data['bill_no'],
										 'isSucceeded' 		=> FALSE,
										 "responseData" 	=> 1,  // for sync reference
										 'result' 			=> 'Unable to proceed the requested operation');
							//$this->response($r,200);
							
						}         
						else
						{
							$r[] = array('bill_no'   	  => $data['bill_no'],
										 'isSucceeded'    => TRUE,
										 "responseData"   => 1,  // for sync reference 
										 'result' 		  => 'Updated Successfully');								
							//$this->response('', 404);
						} 		
					}
					else
					{
						$r[] = array(	'bill_no'   	=> $tran->bill_no,
										'isSucceeded' 	=> FALSE,
										"responseData" 	=> 1,  // for sync reference
										"result" 		=>$valid['error']);
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
	
	public function insertWalletTrans_post()
	{
	
		$transactions = json_decode(file_get_contents('php://input'));
		$login = $transactions->login;
		$branch = $login->id_branch;		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd); 
	//	$this->response($transactions->data,200);
		if($this->checklogin($key))
		{		   
			$model = self::MOD_API;
			$wall_trans = $transactions->data;
			$records = 0;
			$invalid = 0;
			if($wall_trans)
			{
				$total_records = count($wall_trans);
				
				foreach($wall_trans as $transaction)
				{
					$transData = array(	
                    				"record_to" 	 =>  2, //1 - offline , 2 - online
                    				"entry_date" 	 =>  $transaction->entry_date,
                    				"bill_no" 	 	 =>  $transaction->bill_no,
                    				"id_branch" 	 =>  ($branch == 0 ? NULL:$branch),
                    				"trans_type"     =>  $transaction->trans_type,
                    				"mobile" 	 	 =>  $transaction->mobile,
                    				"is_transferred" =>  'N',
                    				"is_modified"    =>  0,
                    				"date_add"       =>  date('Y-m-d H:i:s'), 
                    				"record_type"	 =>  2// 1 - offline , 2 - online
                    			);
                    			
                    			print_r($transData);
                    $transDetailData = array(	
                    				"amount" 		 => $transaction->amount,
                    				"category_code"  => $transaction->category_code,
                    				"use_points" 	 => ($transaction->redeem_req_pts > 0 ? 1:0),
                    				"redeem_req_pts" => $transaction->redeem_req_pts,
                    				"date_add"       => date('Y-m-d H:i:s'),
                    				"remark" 		 => $transaction->remark
                    			);
                    
                    					
					if($this->config->item('no_branch') == 1){
					    $check = array( 'category_code' => $transDetailData['category_code'],
									'amount' 	        => $transDetailData['amount'],
									'bill_no'			=> $transData['bill_no'],
									'trans_type'		=> $transData['trans_type'],
									'mobile'			=> $transData['mobile'],
									'entry_date' 	 	=> $transData['entry_date']);
					}else{
					    $check = array( 'id_branch' 	=> $branch,
					                'category_code' 	=> $transDetailData['category_code'],
									'amount' 	        => $transDetailData['amount'],
									'bill_no'			=> $transData['bill_no'],
									'trans_type'		=> $transData['trans_type'],
									'mobile'			=> $transData['mobile'],
									'entry_date' 	 	=> $transData['entry_date']);
					}
									
				    $valid =$this->validateTransRec($check); 
					
					if($valid['status'])
					{	
					   $t_status  = $this->$model->insertData($transData,'inter_wallet_trans');
					   if($t_status['status']){
					       $transDetailData['id_inter_wallet_trans'] = $t_status['insertID'];
					       $td_status  = $this->$model->insertData($transDetailData,'inter_wallet_trans_detail');
					       if($td_status['status']){
    					   	   $wallAccount = $this->$model->getInterWalletCustomer($transData['mobile']);
    					   	   if(!$wallAccount['status']){
    					   	        $ac_data = array(	
                        				"available_points"  => 0,
                        				"date_add"          => date('Y-m-d H:i:s'),
                        				"mobile" 	 	    => $mobile,
                        			);
    						   		$w_status  = $this->$model->insertData($ac_data,'inter_wallet_account');	
    						   }
    						   
    						   $tranData = array(	
                                				"amount" 		  =>  $transaction->amount,
                                				"category_code"   =>  $transaction->category_code,
                                				"trans_type"      =>  $transaction->trans_type,
                                				"use_points" 	  =>  $transaction->use_points,
                                				"redeem_req_pts"  =>  $transaction->redeem_req_pts,
                                				"mobile" 	 	  =>  $transaction->mobile,
                                				"available_points"=>  $wallAccount['data']['available_points'],
                                				"id_branch" 	  =>  ($branch == 0 ? NULL:$branch),
                                			    );
                                			    
    						   $this->calculateCredit($tranData);
    						   if($w_status['status'] === FALSE )	
    						   {
    							    $r[] = array('bill_no' 		=> $transData['bill_no'],
    											 'entry_date' 	=> $transData['entry_date'],
    											 'isSucceeded'	=> FALSE,
    											 "responseData" => 1,  // for sync reference
    											 'result'		=> 'Unable to proceed the requested operation');	
    						   }
    	                       else
    	                       {							   
    							   $r[] = array('bill_no' 		=> $transData['bill_no'],
    											 'entry_date' 	=> $transData['entry_date'],
    											 'isSucceeded'	=> TRUE,
    											 "responseData" => 1,  // for sync reference
    											 'result' 		=> 'Inserted successfully');	
    						   }
    					   }else{
    					   			$r[] = array('bill_no' 		=> $transData['bill_no'],
    											 'entry_date' 	=> $transData['entry_date'],
    											 'isSucceeded'	=> FALSE,
    											 "responseData" => 1,  // for sync reference
    											 'result'		=> 'Unable to proceed the requested operation');
    					   }
					   }else{
					   			$r[] = array('bill_no' 		=> $transData['bill_no'],
											 'entry_date' 	=> $transData['entry_date'],
											 'isSucceeded'	=> FALSE,
											 "responseData" => 1,  // for sync reference
											 'result'		=> 'Unable to proceed the requested operation');
					   }
					   	
					}
                    else
                    {
                    
						$r[] = array('bill_no' 		=> $transData['bill_no'],
									 'entry_date' 	=> $transData['entry_date'],
									 'isSucceeded' 	=> FALSE,
									 "responseData" => 1,  // for sync reference
									 "result" 		=> $valid['error']);
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
	
    /*  KVP -- Wallet Module Starts  */
	// calc wallet balacnce for transaction
	function calculateCredit($tran){
	   $model = self::MOD_API;
	   if($tran){
	   	     $wal_cat_settings = $this->$model->getWcategorySettings($tran['category_code']);
	   	     $this->db->trans_begin();
		    // $wallAccount = $this->$model->getInterWalletCustomer($tran['mobile']); 
    		   if($tran['trans_type'] == 1){
    				if($tran['use_points'] == 1 && $tran['available_points'] >0){
    				    //if($tran['redeem_req_pts'] > 0 && $tran['available_points'] >0){
    					if( $wal_cat_settings[$key]['value'] == 100 ){
    						$allowed_redeem = $tran['available_points'];
    					    if($tran['redeem_req_pts'] != NULL){
    					        $amt = ($tran['amount'] > $tran['redeem_req_pts'] ? ($tran['amount'] - $tran['redeem_req_pts']):$tran['amount'] );
    					    }else{
    					        $amt = ($tran['amount'] > $tran['available_points'] ? ($tran['amount'] - $tran['available_points']):$tran['amount'] );
    					    }
    						
    					}else{
    						
    						$allowed_redeem = $tran['available_points']*($wal_cat_settings[$key]['redeem_percent']/100);
    						if($allowed_redeem < $tran['available_points'] ){
    							$amt = ($tran['amount'] > $allowed_redeem ? ($tran['amount'] - $allowed_redeem):$tran['amount'] );
    						}else{
    						    if($tran['redeem_req_pts'] != NULL){
    							    $amt = ($tran['amount'] > $tran['redeem_req_pts'] ? ($tran['amount'] - $tran['redeem_req_pts']):$tran['amount'] );
    						    }else{
    						         $amt = ($tran['amount'] ? ($tran['amount'] - $allowed_redeem):$tran['amount'] );
    						    }
    						}
    						
    					}
    					
    					$trans_points = ($wal_cat_settings[$key]['point'] * ($amt/$wal_cat_settings[$key]['value']));
    					$current_wallet_points = $trans_points + ($tran['available_points'] - ($tran['redeem_req_pts'] != NULL ? $tran['redeem_req_pts']:$tran['available_points']));
    					$data = array(
    								'record_to' 		=> 1,
    								'trans_type'        => $tran['trans_type'],
    								'is_modified' 		=> 1,
    								'trans_points' 		=> $trans_points,
    								'id_wcat_settings'  => $wal_cat_settings[$key]['id_wcat_settings'],
    								'allowed_redeem' 	=> $allowed_redeem,
    								'current_wallet_points' => $current_wallet_points
    					);
    					 $ac_data = array(	
                    				"available_points"  => $data['current_wallet_points'],
                    				"date_add"       => date('Y-m-d H:i:s'),
                    				"mobile" 	 	 => $tran['mobile'],
                    			);
                    			
    					$status = $this->$model->updateData($data, $tran,'inter_wallet_trans');
    					$updwallet = false;
    					if($status){
    				   	   if($wallAccount['status']){
    				   	   		$w_status  = $this->$model->updInterWalletAcc($ac_data);	
    					   }
    				   	   else{
    					   		$w_status  = $this->$model->insertData($ac_data,'inter_wallet_account');	
    					   }
    						$updwallet = $this->$model->updwallet($data,$tran['mobile']);
    					//	echo $this->db->last_query();
    					}
    					if($updwallet){
    						$r[] = array('bill_no' 		=> $tran['bill_no'],
    									 'id_branch' 	=> $tran['id_branch'],
    									 'isSucceeded' 	=> TRUE,
    									 "responseData" => 1,  // for sync reference
    									 );
    								
    
    						$msg = 'Thank you for purchasing at Saravana Selvarathnam . Your Wallet Balance '.number_format($tran['available_points'],'2','.','').' points. Redeemed '.number_format($tran['redeem_req_pts'],'2','.','').' points. Credit '.number_format($trans_points,'2','.','').' points. New Wallet Balance '.number_format($data['current_wallet_points'],'2','.','').' Points.';
    						$smsData[] = array('mobile' => $tran['mobile'],
    										   'message'=> $msg
    						);
    					}
    		  		}else{
    		  		    $amt = $tran['amount'];
    					$trans_points = ($wal_cat_settings[$key]['point'] * ($amt/$wal_cat_settings[$key]['value']));
    					$data = array(
    								'record_to' 		=> 1,
    								'trans_type'        => $tran['trans_type'],
    								'is_modified' 		=> 1,
    								'trans_points' 		=> $trans_points,
    								'id_wcat_settings'  => $wal_cat_settings[$key]['id_wcat_settings'],
    								'allowed_redeem' 	=> $allowed_redeem,
    								'current_wallet_points' => $trans_points + $tran['available_points'],
    					);
    					 $ac_data = array(	
                    				"available_points"  => $data['current_wallet_points'],
                    				"date_add"       => date('Y-m-d H:i:s'),
                    				"mobile" 	 	 => $tran['mobile'],
                    			);
                    			
    					$status = $this->$model->updateData($data, $tran,'inter_wallet_trans');
    					$updwallet = false;
    					if($status){
    					   $isAvail = $this->$model->getInterWalletCustomer($tran['mobile']);
    					   
    				   	   if($isAvail['status']){
    				   	   		$w_status  = $this->$model->updInterWalletAcc($ac_data);	
    					   }
    				   	   else{
    					   		$w_status  = $this->$model->insertData($ac_data,'inter_wallet_account');	
    					   }
    						$updwallet = $this->$model->updwallet($data,$tran['mobile']);
    					//	echo $this->db->last_query();
    					}
    					if($updwallet){
    						$r[] = array('bill_no' 		=> $tran['bill_no'],
    									 'id_branch' 	=> $tran['id_branch'],
    									 'isSucceeded' 	=> TRUE,
    									 "responseData" => 1,  // for sync reference
    									 );
    								
    
    						$msg = 'Thank you for purchasing at Saravana Selvarathnam . Your Wallet Balance '.number_format($tran['available_points'],'2','.','').' points. Credit '.number_format($trans_points,'2','.','').' points. New Wallet Balance '.number_format($data['current_wallet_points'],'2','.','').' points.';
    						$smsData[] = array('mobile' => $tran['mobile'],
    										   'message'=> $msg
    						);
    					}
    		  		}
    	      }else{
    		  	$r[] = array('bill_no' 		=> $tran['bill_no'],
    		  				 'id_branch' 	=> $tran['id_branch'],
    						 'isSucceeded' 	=> FALSE,
    						 "responseData" => 1,  // for sync reference
    						 );
    		  }
	  	 
	  	  //echo $this->db->last_query();
	  	  if( $this->db->trans_status() === TRUE ){
		  	$this->db->trans_commit();
		  	$this->send_sms($smsData);
		  }else{
		  	$this->db->trans_rollback();
		  	$r[] = array('isSucceeded' 	=> FALSE,
						 "responseData" => 1,  // for sync reference
						 );
		  }
		   $this->response($r, 200); 
	   }
	   $r[] = array('message' 	=> 'No records found',
					"responseData" => 0,  // for sync reference
					);
	   $this->response($r, 200); 
	}
	
    
    /*  KVP -- Wallet Module Ends  */
	
	/*  KVP -- DBF Wallet Module Starts  */
	function send_sms($smsData){
		$set_model = self::SET_MODEL;
		$serviceID = 17;
	  	$service = $this->$set_model->get_service($serviceID);	
	 	if($service['serv_sms'] == 1 )
		{	
			foreach($smsData as $data){
				$mobile =$data['mobile'];
				$message = $data['message'];
				$this->send_sms_queue($data);
			}
			
			// update sms count
			$this->sync_walletapi_model->updateWalsmsSettings(count($smsData));
		}
		return TRUE;		
	} 
	
	
	function send_sms_queue($data)
	{
	     $fields = json_encode($data);
    
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, "http://nammauzhavan.com/api/v1/ssssendsms");
         curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic '.base64_encode("lmx@uzhavan:lmx@2018")
         ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        $response = curl_exec($ch);
        curl_close($ch);
        $data['response']=$response;
        return $data;
    }
	
	// tool dbf
	public function newWalletTrans_old_post()
	{
	
		$transactions = json_decode(file_get_contents('php://input'));
		$login = $transactions->login;
		$branch = $login->id_branch;		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd);  
		             
		if($this->checklogin($key))
		{		   
			$model = self::MOD_API;
			$wall_trans = $transactions->data;
			$records = 0;
			$invalid = 0;
			if($wall_trans)
			{
				$total_records = count($wall_trans); 
				$walletPoints = array();
				$smsData = array();
				$this->db->trans_begin(); 
				foreach($wall_trans as $transaction)
				{
				    $redeem_request_pts = ($this->IsNullOrEmptyString($transaction->redeem_req_pts) == true ? 0 :$transaction->redeem_req_pts);
					$transData = array(	
                    				"record_to" 	 =>  2, //1 - offline , 2 - online
                    				"entry_date" 	 =>  $transaction->entry_date,
                    				"bill_no" 	 	 =>  $transaction->bill_no,
                    				"id_branch" 	 =>  ($branch == 0 ? NULL:$branch),
                    				"trans_type"     =>  1,
                    				"mobile" 	 	 =>  $transaction->mobile,
                    				"is_transferred" =>  'N',
                    				"is_modified"    =>  0,
                    				"date_add"       =>  date('Y-m-d H:i:s'), 
                    				"use_points" 	 => ($redeem_request_pts > 0 ? 1:0),
                    				"redeem_req_pts" => $redeem_request_pts,
                    				'bill_availWalPt'=> 0,
                    				"actual_redeemed"=> 0,
                    				"record_type"	 =>  2// 1 - offline , 2 - online
                    			);
                    			
                    $transDetailData = array(	
                    				"amount" 		 => $transaction->amount,
                    				"category_code"  => $transaction->category_code,
                    				"date_add"       => date('Y-m-d H:i:s'),
                    				"remark" 		 => $transaction->remark
                    			);                    
                    					
					/*if($this->config->item('no_branch') == 1){
					    $check = array( 'category_code' => $transDetailData['category_code'],
									'amount' 	        => $transDetailData['amount'],
									'bill_no'			=> $transData['bill_no'],
									'trans_type'		=> $transData['trans_type'],
									'mobile'			=> $transData['mobile'],
									'entry_date' 	 	=> $transData['entry_date']);
					}else{
					    $check = array( 'id_branch' 	=> $branch,
					                'category_code' 	=> $transDetailData['category_code'],
									'amount' 	        => $transDetailData['amount'],
									'bill_no'			=> $transData['bill_no'],
									'trans_type'		=> $transData['trans_type'],
									'mobile'			=> $transData['mobile'],
									'entry_date' 	 	=> $transData['entry_date']);
					}*/
				   $wallAccount = $this->$model->getInterWalletCustomer($transData['mobile']);	
				   $isBillExist  = $this->$model->getWalTrans($transData);
				//  $this->response($wallAccount);
				   if($isBillExist['status']){
				   		$transDetailData['id_inter_wallet_trans'] = $isBillExist['tData']['id_inter_waltrans_tmp'];
				   		$transData = $isBillExist['tData']; 
				   		$allow = TRUE;
				   }else{
				        if($wallAccount['status']){ 
				            $transData['bill_availWalPt'] = $wallAccount['data']['available_points'];
				        }
				   		$t_status  = $this->$model->insertData($transData,'inter_wallet_trans_tmp');
				   		if($t_status['status']){
				   			$allow = TRUE; 
				   			$transDetailData['id_inter_wallet_trans'] = $t_status['insertID'];
				   		}else{
							$allow = FALSE; 
						}
				   }
				   	    if($allow){
					       $isWalDetailExist = $this->$model->isWalDetailExist($transDetailData);
					       $idInterWalDetail = null;
					       $walDetailData = array();
					       if($isWalDetailExist['status']){
					            // #*#
					            $idInterWalDetail = $isWalDetailExist['data']['id_tmp_waldetails'];
					            $walDetailData = $isWalDetailExist['data'];
					       }else{
					           // #*# Table changed from inter_wallet_trans_detail to inter_walTransDetail_tmp 
					            $td_status = $this->$model->insertData($transDetailData,'inter_walTransDetail_tmp');   
					            $idInterWalDetail = $td_status['insertID'];
					       }
					       
						   $bill = $transData['bill_no']; 
					       if($idInterWalDetail != null){
						   	   $mobile = (string) $transaction->mobile;
						   	   if(!$wallAccount['status']){
						   	        $ac_data = array(	
		                    				"available_points"  => 0,
		                    				"date_add"          => date('Y-m-d H:i:s'),
		                    				"mobile" 	 	    => $mobile,
		                    				"id_customer"       => (isset($wallAccount['cusData']['id_customer'])?$wallAccount['cusData']['id_customer']:NULL)
		                    			);
							   		$w_status  = $this->$model->insertData($ac_data,'inter_wallet_account'); 
							   		$id_inter_wallet_ac = $w_status['insertID'];
							   		if(!isset($walletPoints[$mobile])){
										$walletPoints[$mobile]['wal_pts'] = 0;
										$walletPoints[$mobile]['prev_wal_pts'] = 0;
									    $walletPoints[$mobile]['bill_credit_pts'] = 0;
									    $walletPoints[$mobile]['bill_debit_pts'] = 0;
								    }
							   }else{
							       $id_inter_wallet_ac = $wallAccount['data']['id_inter_wal_ac'];
							       if(!isset($walletPoints[$mobile])){
										$walletPoints[$mobile]['prev_wal_pts'] = $wallAccount['data']['available_points'];
										$walletPoints[$mobile]['wal_pts'] = $wallAccount['data']['available_points'];
									    $walletPoints[$mobile]['bill_credit_pts'] = 0;
									    $walletPoints[$mobile]['bill_debit_pts'] = 0;
									    $walletPoints[$mobile]['mobile'] = $mobile;
								   }
							   }
							   
							   
						       if($wallAccount['data']){
						        $avail_points = ($wallAccount['data']['available_points'] == '' || $wallAccount['data']['available_points'] == NULL ? 0:$wallAccount['data']['available_points']);
							   }else{
							       $avail_points = 0;
							   }
							   
							   $b_date = date_create($transaction->entry_date);
                               $bill_date = date_format($b_date,"d-m-Y");
							   
						   	   $tranData = array(	
		                            				"amount" 		  =>  $transaction->amount,
		                            				"category_code"   =>  $transaction->category_code,
		                            				"trans_type"      =>  $transaction->trans_type,
		                            			    "use_points" 	  =>  ($redeem_request_pts > 0 ? 1:0),
		                            				"redeem_req_pts"  =>  (float) $redeem_request_pts,
		                            				"prev_redeem_req_pts"=> (float) ($isBillExist['status'] ? $transData['redeem_req_pts']:0 ),
		                            				/*"use_points" 	  =>  $transData['use_points'],
		                            				"redeem_req_pts"  =>  (float) $transData['redeem_req_pts'],*/
		                            				"mobile" 	 	  =>  $mobile,
		                            				"available_points"=>  (float) $avail_points,
		                            				"id_branch" 	  =>  ($branch == 0 ? NULL:$branch),
		                            				"bill_no"         =>  $transData['bill_no'],
		                            				"id_inter_wallet_trans" => $transDetailData['id_inter_wallet_trans'],
		                            				"id_inter_wallet_ac" => $id_inter_wallet_ac,
		                            				"id_inter_waltransdetail" => $idInterWalDetail,
		                            				"id_tmp_waldetails" => $idInterWalDetail, // id_tmp_waldetails
		                            				"trans_type"	  =>  1,
		                            				"walDetailData"   => ($isWalDetailExist['status'] ? $walDetailData:NULL),
		                            				"isDuplicateWalDetail" => ($isWalDetailExist['status'] ? 1:0), 
		                            				"actual_redeemed" => (float) $transData['actual_redeemed'],
		                            				"bill_availWalPt"   => (float) $transData['bill_availWalPt'],
		                            				"bill_date"        => $bill_date
		                            			    );
		                      //     $this->response($tranData);   			  
		                      if($id_inter_wallet_ac == NULL || $id_inter_wallet_ac == '' )	
							   {
								    $r[] = array('bill_no' 		=> $transData['bill_no'],
												 'category_code'=> $transaction->category_code,
												 'entry_date' 	=> $transData['entry_date'],
												 'isSucceeded'	=> FALSE,
												 'error'        => $this->db->_error_message()
												 );	
							   }
		                       else
		                       {	 
			                       $calcCredit = $this->calculatePoints($tranData);
			                       if($calcCredit['isSucceed'] == TRUE){ 
								   	$walletPoints[$mobile]['wal_pts'] += $calcCredit['credit_points'];
								   	$walletPoints[$mobile]['wal_pts'] -= $calcCredit['debit_points'];
								   	// if($calcCredit['credit_points'] == 0 && $calcCredit['debit_points'] > 0){
								   	//     $walletPoints[$mobile]['wal_pts'] -= $calcCredit['debit_points'];
								   	// }
								    $walletPoints[$mobile]['bill_credit_pts'] += $calcCredit['credit_points'];
								    $walletPoints[$mobile]['bill_debit_pts'] += $calcCredit['debit_points']; 
								    }else{
						   			$r[] = array('bill_no' 		=> $transData['bill_no'],
						   						 'category_code'=> $transaction->category_code,
												 'isSucceeded'	=> FALSE , 
												 'error'        => $this->db->_error_message()
												 );
						   		   }
								     
							   }
						   }else{
						   			$r[] = array('bill_no' 		=> $transData['bill_no'],
						   						 'category_code'=> $transaction->category_code,
												 'isSucceeded'	=> FALSE ,
											 	 'error'        => $this->db->_error_message()
												 );
						   }
					   }else{
					   			$r[] = array('bill_no' 		=> $transData['bill_no'],
					   						 'category_code'=> $transaction->category_code,
											 'isSucceeded'	=> FALSE,
											 'error'        => $this->db->_error_message()
											);
					   }
				   
                }
                if($this->db->trans_status() === TRUE){
                      $responseData = array();
                	  foreach($walletPoints as $k => $val){ 
						$ac_data = array(	
					    				"available_points"  => $val['wal_pts'],
					    				"date_add"       => date('Y-m-d H:i:s'),
					    				"mobile" 	 	 => $k,
						    			);
						    			
					    $responseData[$k] = array(	
    					    				"wal_pts"  => floor($val['wal_pts']),
    					    				"mobile"   => $k,
						    			);
				// 		$w_status  = $this->$model->updInterWalletAcc($ac_data);
						if($val['bill_debit_pts'] > 0){
							$msg = 'Thank you for purchasing at Saravana Selvarathnam . Your Wallet Balance '.number_format($val['prev_wal_pts'],'2','.','').' points.  Credit '.number_format($val['bill_credit_pts'],'2','.','').' points. Redeemed '.number_format($val['bill_debit_pts'],'2','.','').' points. Updated Wallet Balance '.number_format($val['wal_pts'],'2','.','').' Points.'; 
						}else{
							$msg = 'Thank you for purchasing at Saravana Selvarathnam . Your Wallet Balance '.number_format($val['prev_wal_pts'],'2','.','').' points. Credit '.number_format($val['bill_credit_pts'],'2','.','').' points. Updated Wallet Balance '.number_format($val['wal_pts'],'2','.','').' points.';
						}   
						if( $val['bill_credit_pts'] > 0 ){
							$smsData[] = array('mobile' => $k,
										   		'message'=> $msg
											);
						} 						
						 
						$syncWalData = array(
											"points"  	=> $val['wal_pts'],
						    				"mobile" 	=> $k,
											);
						if($branch != ''){
							foreach($this->branch as $bran){
								if($branch != $bran){
									$syncWalData['branch_'.$bran] = 0; // unsynced
								}else{
								    $syncWalData['branch_'.$bran] = 1; // synced
								}								
							}
						}else{
							$syncWalData['branch'] = 0;
						}
						
						$isExist = $this->$model->getSyncWalletById($k);
						if($isExist){
							$syncWalData['last_update'] = date('Y-m-d H:i:s');
							$syncWalData['id_inter_sync_wallet'] = $isExist['id_inter_sync_wallet']; 
							$this->$model->updateSyncWal($syncWalData);
						}else{
							$syncWalData['date_add'] = date('Y-m-d H:i:s');
							$this->$model->insertData($syncWalData,'inter_sync_wallet');
						}
						
						
						/*$trData = array(
										'record_to' 		=> 1,
										'is_modified' 		=> 1,
										'is_transferred' 	=> 'Y',
									   );
						$status = $this->$model->updateData($trData, array('id_branch' => $transData['id_branch'],'bill_no' => $k ),'inter_wallet_trans');*/
					}
			 
					if($this->db->trans_status() === TRUE){
	                	$this->db->trans_commit(); 
	                	$this->send_sms($smsData);
						$f = array('isSucceeded'	=> TRUE,
									 'result'		=> $responseData,
									 "responseData" => 1,  // for sync reference ,
									 "error"		=> ""
									 );
						$this->response($f,200);
					}else{
						$this->db->trans_rollback();
						$f = array('isSucceeded'	=> FALSE,
									 'result'		=> $r,
									 "responseData" => 0,  // for sync reference
									 'error'        => 'RB 1'
									 );
						$this->response($f,200);
					}
				}else{
					$this->db->trans_rollback();
					$f = array('isSucceeded'	=> FALSE,
								 'result'		=> $r,
								 'error'        => 'RB 2',
								 //'query'        => $this->db->last_query(),
								 "responseData" => 0,  // for sync reference
								 );
					$this->response($f,200);
				}
				  
			}	
						
			else
			{
				$msg[] = array('result' =>'No records to proceed the requested operation!',"responseData" => 0);
				$this->response($msg, 200); 
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}		
	}
	
	//walletPointCalculation
	public function newWalletTrans_post()
	{
	
		$transactions = json_decode(file_get_contents('php://input'));
		$login = $transactions->login;
		$branch = $login->id_branch;		
		$key = array('username' => $login->username,
		             'passwd' => $login->passwd);  
		             
		if($this->checklogin($key))
		{		   
			$model = self::MOD_API;
			$wall_trans = $transactions->data;
			$records = 0;
			$invalid = 0;
			if($wall_trans)
			{
				$total_records = count($wall_trans); 
				$walletPoints = array();
				$smsData = array();
				$this->db->trans_begin(); 
				$walTranArray = [];
				foreach($wall_trans as $v)
				{
				    $walTranKey = -1;
				    if(sizeof($walTranArray)>0){
				        $walTranKey = $this->findArrayIdxMulti($walTranArray,$v->mobile,$v->bill_no,$v->category_code); 
				    }
				    if($walTranKey >= 0){ 
				        $exisWaltData = $walTranArray[$walTranKey];  
				        $redeem_request_pts = ($this->IsNullOrEmptyString($v->redeem_req_pts) == true ? 0 :$v->redeem_req_pts);
				        $walTranArray[$walTranKey]["amount"] = (float) ($exisWaltData['amount'] + (float) $v->amount);
                        $walTranArray[$walTranKey]["redeem_req_pts"] =  (float) ($exisWaltData['redeem_req_pts'] + (float) $redeem_request_pts);
				    }
				    else{
				        $redeem_request_pts = ($this->IsNullOrEmptyString($v->redeem_req_pts) == true ? 0 :$v->redeem_req_pts);
				        $walTranArray[] = array("entry_date"=> $v->entry_date,
                            				    "bill_no"    => $v->bill_no,
                            				    "amount"     => (float) $v->amount,
                            				    "category_code" => $v->category_code,
                            				    "trans_type"    => $v->trans_type,
                            				    "redeem_req_pts"=> (float) $redeem_request_pts,
                            				    "mobile"     => $v->mobile,
                            				    "remark"     => $v->remark
                            				    );
				    }
				    
				}
				foreach($walTranArray as $transaction)
				{
				    $redeem_request_pts = ($this->IsNullOrEmptyString($transaction['redeem_req_pts']) == true ? 0 :$transaction['redeem_req_pts']);
					$transData = array(	
                    				"record_to" 	 =>  2, //1 - offline , 2 - online
                    				"entry_date" 	 =>  $transaction['entry_date'],
                    				"bill_no" 	 	 =>  $transaction['bill_no'],
                    				"id_branch" 	 =>  ($branch == 0 ? NULL:$branch),
                    				"trans_type"     =>  1,
                    				"mobile" 	 	 =>  $transaction['mobile'],
                    				"is_transferred" =>  'N',
                    				"is_modified"    =>  0,
                    				"date_add"       =>  date('Y-m-d H:i:s'), 
                    				"use_points" 	 => ($redeem_request_pts > 0 ? 1:0),
                    				"redeem_req_pts" => $redeem_request_pts,
                    				'bill_availWalPt'=> 0,
                    				"actual_redeemed"=> 0,
                    				"record_type"	 =>  2// 1 - offline , 2 - online
                    			);
                    			
                    $transDetailData = array(	
                    				"amount" 		 => $transaction['amount'],
                    				"category_code"  => $transaction['category_code'],
                    				"date_add"       => date('Y-m-d H:i:s'),
                    				"remark" 		 => $transaction['remark']
                    			);                    
                    					 
				   $wallAccount = $this->$model->getInterWalletCustomer($transData['mobile']);	
				   $isBillExist  = $this->$model->getWalTrans($transData); 
				   if($isBillExist['status']){
				   		$transDetailData['id_inter_wallet_trans'] = $isBillExist['tData']['id_inter_waltrans_tmp'];
				   		$transData = $isBillExist['tData']; 
				   		$allow = TRUE;
				   }else{
				        if($wallAccount['status']){ 
				            $transData['bill_availWalPt'] = $wallAccount['data']['available_points'];
				        }
				   		$t_status  = $this->$model->insertData($transData,'inter_wallet_trans_tmp');
				   		if($t_status['status']){
				   			$allow = TRUE; 
				   			$transDetailData['id_inter_wallet_trans'] = $t_status['insertID'];
				   		}else{
							$allow = FALSE; 
						}
				   }
				   	    if($allow){
					       $isWalDetailExist = $this->$model->isWalDetailExist($transDetailData);
					       $idInterWalDetail = null;
					       $walDetailData = array();
					       if($isWalDetailExist['status']){
					            // #*#
					            $idInterWalDetail = $isWalDetailExist['data']['id_tmp_waldetails'];
					            $walDetailData = $isWalDetailExist['data'];
					       }else{
					           // #*# Table changed from inter_wallet_trans_detail to inter_walTransDetail_tmp 
					            $td_status = $this->$model->insertData($transDetailData,'inter_walTransDetail_tmp');   
					            $idInterWalDetail = $td_status['insertID'];
					       }
					       
						   $bill = $transData['bill_no']; 
					       if($idInterWalDetail != null){
						   	   $mobile = (string) $transaction['mobile'];
						   	   if(!$wallAccount['status']){
						   	        $ac_data = array(	
		                    				"available_points"  => 0,
		                    				"date_add"          => date('Y-m-d H:i:s'),
		                    				"mobile" 	 	    => $mobile,
		                    				"id_customer"       => (isset($wallAccount['cusData']['id_customer'])?$wallAccount['cusData']['id_customer']:NULL)
		                    			);
							   		$w_status  = $this->$model->insertData($ac_data,'inter_wallet_account'); 
							   		$id_inter_wallet_ac = $w_status['insertID'];
							   		if(!isset($walletPoints[$mobile])){
										$walletPoints[$mobile]['wal_pts'] = 0;
										$walletPoints[$mobile]['prev_wal_pts'] = 0;
									    $walletPoints[$mobile]['bill_credit_pts'] = 0;
									    $walletPoints[$mobile]['bill_debit_pts'] = 0;
								    }
							   }else{
							       $id_inter_wallet_ac = $wallAccount['data']['id_inter_wal_ac'];
							       if(!isset($walletPoints[$mobile])){
										$walletPoints[$mobile]['prev_wal_pts'] = $wallAccount['data']['available_points'];
										$walletPoints[$mobile]['wal_pts'] = $wallAccount['data']['available_points'];
									    $walletPoints[$mobile]['bill_credit_pts'] = 0;
									    $walletPoints[$mobile]['bill_debit_pts'] = 0;
									    $walletPoints[$mobile]['mobile'] = $mobile;
								   }
							   }
							   
							   
						       if($wallAccount['data']){
						        $avail_points = ($wallAccount['data']['available_points'] == '' || $wallAccount['data']['available_points'] == NULL ? 0:$wallAccount['data']['available_points']);
							   }else{
							       $avail_points = 0;
							   }
							   
							   $b_date = date_create($transaction['entry_date']);
                               $bill_date = date_format($b_date,"d-m-Y");
							   
						   	   $tranData = array(	
		                            				"amount" 		  =>  $transaction['amount'],
		                            				"category_code"   =>  $transaction['category_code'],
		                            				"trans_type"      =>  $transaction['trans_type'],
		                            			    "use_points" 	  =>  ($redeem_request_pts > 0 ? 1:0),
		                            				"redeem_req_pts"  =>  (float) $redeem_request_pts,
		                            				"prev_redeem_req_pts"=> (float) ($isBillExist['status'] ? $transData['redeem_req_pts']:0 ),
		                            				/*"use_points" 	  =>  $transData['use_points'],
		                            				"redeem_req_pts"  =>  (float) $transData['redeem_req_pts'],*/
		                            				"mobile" 	 	  =>  $mobile,
		                            				"available_points"=>  (float) $avail_points,
		                            				"id_branch" 	  =>  ($branch == 0 ? NULL:$branch),
		                            				"bill_no"         =>  $transData['bill_no'],
		                            				"id_inter_wallet_trans" => $transDetailData['id_inter_wallet_trans'],
		                            				"id_inter_wallet_ac" => $id_inter_wallet_ac,
		                            				"id_inter_waltransdetail" => $idInterWalDetail,
		                            				"id_tmp_waldetails" => $idInterWalDetail, // id_tmp_waldetails
		                            				"trans_type"	  =>  1,
		                            				"walDetailData"   => ($isWalDetailExist['status'] ? $walDetailData:NULL),
		                            				"isDuplicateWalDetail" => ($isWalDetailExist['status'] ? 1:0), 
		                            				"actual_redeemed" => (float) $transData['actual_redeemed'],
		                            				"bill_availWalPt"   => (float) $transData['bill_availWalPt'],
		                            				"bill_date"        => $bill_date
		                            			    );
		                      //     $this->response($tranData);   			  
		                      if($id_inter_wallet_ac == NULL || $id_inter_wallet_ac == '' )	
							   {
								    $r[] = array('bill_no' 		=> $transData['bill_no'],
												 'category_code'=> $transaction['category_code'],
												 'entry_date' 	=> $transData['entry_date'],
												 'isSucceeded'	=> FALSE,
												 'error'        => $this->db->_error_message()
												 );	
							   }
		                       else
		                       {	 
			                       $calcCredit = $this->calculatePoints($tranData);
			                       if($calcCredit['isSucceed'] == TRUE){ 
								   	$walletPoints[$mobile]['wal_pts'] += $calcCredit['credit_points'];
								   	$walletPoints[$mobile]['wal_pts'] -= $calcCredit['debit_points'];
								   	// if($calcCredit['credit_points'] == 0 && $calcCredit['debit_points'] > 0){
								   	//     $walletPoints[$mobile]['wal_pts'] -= $calcCredit['debit_points'];
								   	// }
								    $walletPoints[$mobile]['bill_credit_pts'] += $calcCredit['credit_points'];
								    $walletPoints[$mobile]['bill_debit_pts'] += $calcCredit['debit_points']; 
								    }else{
						   			$r[] = array('bill_no' 		=> $transData['bill_no'],
						   						 'category_code'=> $transaction['category_code'],
												 'isSucceeded'	=> FALSE , 
												 'error'        => $this->db->_error_message()
												 );
						   		   }
								     
							   }
						   }else{
						   			$r[] = array('bill_no' 		=> $transData['bill_no'],
						   						 'category_code'=> $transaction['category_code'],
												 'isSucceeded'	=> FALSE ,
											 	 'error'        => $this->db->_error_message()
												 );
						   }
					   }else{
					   			$r[] = array('bill_no' 		=> $transData['bill_no'],
					   						 'category_code'=> $transaction['category_code'],
											 'isSucceeded'	=> FALSE,
											 'error'        => $this->db->_error_message()
											);
					   }
				   
                }
                if($this->db->trans_status() === TRUE){
                      $responseData = array();
                	  foreach($walletPoints as $k => $val){ 
						$ac_data = array(	
					    				"available_points"  => $val['wal_pts'],
					    				"date_add"       => date('Y-m-d H:i:s'),
					    				"mobile" 	 	 => $k,
						    			);
						    			
					    $responseData[$k] = array(	
    					    				"wal_pts"  => floor($val['wal_pts']),
    					    				"mobile"   => $k,
						    			);
				// 		$w_status  = $this->$model->updInterWalletAcc($ac_data);
						if($val['bill_debit_pts'] > 0){
							$msg = 'Thank you for purchasing at Saravana Selvarathnam . Your Wallet Balance '.number_format($val['prev_wal_pts'],'2','.','').' points.  Credit '.number_format($val['bill_credit_pts'],'2','.','').' points. Redeemed '.number_format($val['bill_debit_pts'],'2','.','').' points. Updated Wallet Balance '.number_format($val['wal_pts'],'2','.','').' Points.'; 
						}else{
							$msg = 'Thank you for purchasing at Saravana Selvarathnam . Your Wallet Balance '.number_format($val['prev_wal_pts'],'2','.','').' points. Credit '.number_format($val['bill_credit_pts'],'2','.','').' points. Updated Wallet Balance '.number_format($val['wal_pts'],'2','.','').' points.';
						}   
						if( $val['bill_credit_pts'] > 0 ){
							$smsData[] = array('mobile' => $k,
										   		'message'=> $msg
											);
						} 						
						 
						$syncWalData = array(
											"points"  	=> $val['wal_pts'],
						    				"mobile" 	=> $k,
											);
						if($branch != ''){
							foreach($this->branch as $bran){
								if($branch != $bran){
									$syncWalData['branch_'.$bran] = 0; // unsynced
								}else{
								    $syncWalData['branch_'.$bran] = 1; // synced
								}								
							}
						}else{
							$syncWalData['branch'] = 0;
						}
						
						$isExist = $this->$model->getSyncWalletById($k);
						if($isExist){
							$syncWalData['last_update'] = date('Y-m-d H:i:s');
							$syncWalData['id_inter_sync_wallet'] = $isExist['id_inter_sync_wallet']; 
							$this->$model->updateSyncWal($syncWalData);
						}else{
							$syncWalData['date_add'] = date('Y-m-d H:i:s');
							$this->$model->insertData($syncWalData,'inter_sync_wallet');
						}
						
						
						/*$trData = array(
										'record_to' 		=> 1,
										'is_modified' 		=> 1,
										'is_transferred' 	=> 'Y',
									   );
						$status = $this->$model->updateData($trData, array('id_branch' => $transData['id_branch'],'bill_no' => $k ),'inter_wallet_trans');*/
					}
			 
					if($this->db->trans_status() === TRUE){
	                	$this->db->trans_commit(); 
	                	$this->send_sms($smsData);
						$f = array('isSucceeded'	=> TRUE,
									 'result'		=> $responseData,
									 "responseData" => 1,  // for sync reference ,
									 "error"		=> ""
									 );
						$this->response($f,200);
					}else{
						$this->db->trans_rollback();
						$f = array('isSucceeded'	=> FALSE,
									 'result'		=> $r,
									 "responseData" => 0,  // for sync reference
									 'error'        => 'RB 1'
									 );
						$this->response($f,200);
					}
				}else{
					$this->db->trans_rollback();
					$f = array('isSucceeded'	=> FALSE,
								 'result'		=> $r,
								 'error'        => 'RB 2',
								 //'query'        => $this->db->last_query(),
								 "responseData" => 0,  // for sync reference
								 );
					$this->response($f,200);
				}
				  
			}	
						
			else
			{
				$msg[] = array('result' =>'No records to proceed the requested operation!',"responseData" => 0);
				$this->response($msg, 200); 
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}		
	}
	
	function calculatePoints($tran){ 
	 $model = self::MOD_API;
	 $r = array();
	 $updData = array(); 
	 $tran['category_code'] = ($tran['category_code'] == '' ? 'NA':$tran['category_code']);
     $wal_cat_settings = $this->$model->getWcategorySettings($tran['category_code']); 
	   if($tran['trans_type'] == 1 && $tran['amount'] > 0){ 
	       $redeem_req_pts = $tran['prev_redeem_req_pts'] + $tran['redeem_req_pts']; 
	       // NOTE :  $tran['bill_availWalPt'] -> points available till previous bill
	        $bill_avail_pt = ($tran['bill_availWalPt'] - $tran['actual_redeemed']); // user can use only $bill_avail_pt not current wallet available points 
	        //$avail_redeem  = ( floor($tran['available_points']) >= floor($bill_avail_pt) ? $bill_avail_pt : 0 ); // No use in $bill_avail_pt since we are not sending any rejection if points avail do redeem
	        $avail_redeem  = $tran['available_points'];
	        $allow_debit = ($tran['redeem_req_pts'] > 0 && $avail_redeem > 0 ? ($tran['actual_redeemed'] == $tran['redeem_req_pts'] ? false :true) : false);
	        
    		
			if($tran['use_points'] == 1  && $avail_redeem >0 && $allow_debit){
			    $debit_points = 0;
			    $allowed_redeem = $tran['amount']*($wal_cat_settings['redeem_percent']/100);
			   
			    $debit_points = ($tran['redeem_req_pts'] > $avail_redeem ? $avail_redeem : $tran['redeem_req_pts'] );
				 //$this->response($debit_points);
		
				//$amt = ($tran['amount'] >= $debit_points ? ($tran['amount'] - $debit_points):$tran['amount'] ); 
				$amt = $tran['amount'];
				$credit_points = ($wal_cat_settings['point'] > 0 ?($wal_cat_settings['point'] * ($amt/$wal_cat_settings['value'])):0);  
				
			    if($tran['isDuplicateWalDetail'] == 1){   // update points (new+old) points
			         $updData = array(
								'trans_points'  	=> $credit_points+$tran['walDetailData']['trans_points'],
								'id_wcat_settings'  => $wal_cat_settings['id_wcat_settings'],
								'allowed_redeem' 	=> $allowed_redeem + $tran['walDetailData']['allowed_redeem'], 
								'amount'            => $tran['amount'] + $tran['walDetailData']['amount'],
								); 
					/*  #*# Table changed from inter_wallet_trans_detail to inter_walTransDetail_tmp 
					$stat = $this->$model->updateTransDetailData($updData,$tran['id_inter_waltransdetail']);  */
					$stat = $this->$model->updateTransDetailDataTmp($updData,$tran['id_tmp_waldetails']);
					$updData['trans_type'] = $tran['trans_type'];
				    $updData['bill_no'] = $tran['bill_no'];
				    $updData['category_code'] = $tran['category_code'];
				    $updData['debit_points'] = $debit_points + $tran['actual_redeemed'];
				    if($updData['trans_points'] > 0 || $updData['debit_points'] > 0){
				        $updData['bill_date']   =  $tran['bill_date']; 
				        $updwallet = $this->$model->updInterwalletTables($updData,$tran['mobile']);  
				        // Log
    				    /*$log_path = '../api/log.txt';
    				    $data = "\n"."".$tran['id_inter_waltransdetail']."*".json_encode(array('insData'=>$updData,'last_qry' => $this->db->last_query()));
    				    file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX);*/
                        // End of log
				    }
					
			    }else{
			            $updData = array(
								'trans_points' 		=> $credit_points,
								'id_wcat_settings'  => $wal_cat_settings['id_wcat_settings'],
								'allowed_redeem' 	=> $allowed_redeem,
							//	'actual_redeemed'   => $debit_points,
								); 
    				 
    					/*  #*# Table changed from inter_wallet_trans_detail to inter_walTransDetail_tmp 
    					$stat = $this->$model->updateTransDetailData($updData,$tran['id_inter_waltransdetail']);  */
    					$stat = $this->$model->updateTransDetailDataTmp($updData,$tran['id_tmp_waldetails']);
    					$updData['trans_type'] = $tran['trans_type'];
    				    $updData['bill_no'] = $tran['bill_no'];
    				    $updData['debit_points'] = $debit_points;
    				    $updData['category_code'] = $tran['category_code'];
    				    if($updData['trans_points'] > 0 || $updData['debit_points'] > 0){
    				        $updData['bill_date']   =  $tran['bill_date']; 
    				        $updwallet = $this->$model->updwallet($updData,$tran['mobile']); 
    				        // Log
        				    /*$log_path = '../api/log.txt';
        				    $data = "\n"."".$tran['id_inter_waltransdetail']."**".json_encode(array('insData'=>$updData,'last_qry' => $this->db->last_query()));
        				    file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX);*/
                            // End of log
    				    }
			    }
			    
			    $tranDt = array( 'actual_redeemed' => (isset($tran['actual_redeemed']) ? ($debit_points+ $tran['actual_redeemed']) : $debit_points),'redeem_req_pts'=>$redeem_req_pts);
				$this->$model->updateTransData($tranDt,$tran['id_inter_wallet_trans']);   
				
				if($updwallet){
					$r = array( 'bill_no' 		=> $tran['bill_no'],
							 	'credit_points' => $credit_points,
							 	'debit_points'  => $debit_points,
							 	'isSucceed'	=> true
							 ); 
				}
	  		}else{
	  		    $amt = $tran['amount'];
				$credit_points = ($wal_cat_settings['point'] > 0 ?($wal_cat_settings['point'] * ($amt/$wal_cat_settings['value'])):0);  
				$allowed_redeem = 0; 
				$debit_points = 0;
				
				if($tran['isDuplicateWalDetail'] == 1){   // update points (new+old) points
			         $updData = array(
								'trans_points' 		=> ($credit_points + $tran['walDetailData']['trans_points']),
								'id_wcat_settings'  => $wal_cat_settings['id_wcat_settings'],
								'allowed_redeem' 	=> $allowed_redeem + $tran['walDetailData']['allowed_redeem'],
							//	'actual_redeemed'   => 0 + $tran['actual_redeemed'],
								'amount'            => $tran['amount'] + $tran['walDetailData']['amount'],
								); 
					/*  #*# Table changed from inter_wallet_trans_detail to inter_walTransDetail_tmp 
					$stat = $this->$model->updateTransDetailData($updData,$tran['id_inter_waltransdetail']);  */
					$stat = $this->$model->updateTransDetailDataTmp($updData,$tran['id_tmp_waldetails']);
					$updData['trans_type'] = $tran['trans_type'];
				    $updData['bill_no'] = $tran['bill_no'];
				    $updData['category_code'] = $tran['category_code'];  
				    if($updData['trans_points'] > 0 ){
				        $updData['bill_date']   =  $tran['bill_date']; 
    				    $updwallet = $this->$model->updInterwalletTables($updData,$tran['mobile']); 
    				    // Log
    				    /*$log_path = '../api/log.txt';
    				    $data = "\n"."".$tran['id_inter_waltransdetail']."***".json_encode(array('insData'=>$updData,'last_qry' => $this->db->last_query()));
    				    file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX);*/
                        // End of log
				    }
    			
			    }else{
			         $updData = array(
								'trans_points' 		=> $credit_points,
								'id_wcat_settings'  => $wal_cat_settings['id_wcat_settings'],
								'allowed_redeem' 	=> $allowed_redeem,
							//	'actual_redeemed'   => 0,
								);
					/*  #*# Table changed from inter_wallet_trans_detail to inter_walTransDetail_tmp 
					$stat = $this->$model->updateTransDetailData($updData,$tran['id_inter_waltransdetail']);  */
					$stat = $this->$model->updateTransDetailDataTmp($updData,$tran['id_tmp_waldetails']);
					$updData['trans_type'] = $tran['trans_type'];
				    $updData['bill_no'] = $tran['bill_no'];
				    $updData['category_code'] = $tran['category_code']; 
				    if($updData['trans_points'] > 0 ){
				        $updData['bill_date']   =  $tran['bill_date']; 
    				    $updwallet = $this->$model->updwallet($updData,$tran['mobile']); 
    				    // Log
    				    /*$log_path = '../api/log.txt';
    				    $data = "\n"."".$tran['id_inter_waltransdetail']."****".json_encode(array('insData'=>$updData,'last_qry' => $this->db->last_query()));
    				    file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX);*/
                        // End of log
				    }
    				
			    }
			   
			    if($tran['use_points'] == 1){
        			$tranDt = array( 'redeem_req_pts'=>$redeem_req_pts);
        			$this->$model->updateTransData($tranDt,$tran['id_inter_wallet_trans']);  
			    }
			    
				// $tranDt = array( 'actual_redeemed' => (isset($tran['actual_redeemed']) ? ($debit_points+ $tran['actual_redeemed']) : $debit_points), 
			 //                    'credit_points'   => (isset($tran['bill_availWalPt']) ? ($credit_points + $tran['bill_availWalPt']) :$credit_points )
			 //                   );
				// $this->$model->updateTransData($tranDt,$tran['id_inter_wallet_trans']);  
				
				if($updwallet){
					$r = array( 'bill_no' 		=> $tran['bill_no'],
							 	'credit_points' => $credit_points,
							 	'debit_points'  => 0,
							 	'isSucceed'	=> true,
							 );  
				}
	  		}
  			if(isset($r['bill_no'])){
  			    $ac_data = array(	
    		    				"available_points"  => $tran['available_points']+($r['credit_points'] - $r['debit_points']),
    		    				"date_add"          => date('Y-m-d H:i:s'),
    		    				"mobile" 	 	    => $tran['mobile'],
			    			);
	  		    $this->$model->updInterWalletAcc($ac_data); 
  			}
      }else{
	  	$r  = array('bill_no' 		=> $tran['bill_no'], 
	  				 'isSucceed'	=> FALSE
				   );
	  } 
	 // echo $this->db->_error_message();exit;
	  return $r;
   
    }
    
    // no need if 1 branch // tool dbf
	public function readSyncWallet_post()
	{
		$credentials = json_decode(file_get_contents('php://input'));
		$login = $credentials->login;
		
		$branch = $login->id_branch;
	
		$key = array('username' => $login->username,
		              'passwd' => $login->passwd); 
		              
		if($this->checklogin($key))
		{	
		    if($branch != ''){
    			$model = self::MOD_API;
    			$transactions = $this->$model->getSyncWalletData($branch);
    			foreach($transactions as $transaction)
    			{
    				$trans[]= array(	"ref_no" => (int) $transaction["id_inter_sync_wallet"],
    									"mobile" => (string) $transaction["mobile"],
    				                    "wal_pts"=> (string)$transaction["points"],
    				                    'responseData'=>1
    								);
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
				$msg[] = array('message' => 'No need to call this api!','responseData'=>0); // 'status'=>0 - to indicate response has no data
				$this->response($msg, 200); 
			}
		}
   		else
		{
			$this->response('Invalid credentials!', 401);
		}	
	}	
	
	// no need if 1 branch // tool dbf
	public function updateSyncWallet_post()  
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
			          $data = array( 	'id_inter_sync_wallet'	=> $tran->ref_no,
                    					"branch_".$branch.""	=> 1,  
                    					"last_update"	 		=> date('Y-m-d H:i:s') 
                    				);
                      $isEmpty = $this->isArrayEmpty($data);
						if(!$isEmpty['status'])
						{
							$valid['status'] = TRUE ; 
						}
			      }
			      else{
			         $valid['status'] = false ; 
			         $valid['error'] = "Invalid type";
			      }

					if($valid['status'])
					{ 
						$status = $this->$model->updateSyncWal($data);
				 
						 if($status ===FALSE)
						{
							$r[] = array('ref_no' 			=> $data['id_inter_sync_wallet'],
										 'isSucceeded' 		=> FALSE,
										 "responseData" 	=> 1,  // for sync reference
										 'result' 			=> 'Unable to proceed the requested operation');
							//$this->response($r,200);
							
						}         
						else
						{
							$r[] = array('ref_no' 		  => $data['id_inter_sync_wallet'],
										 'isSucceeded'    => TRUE,
										 "responseData"   => 1,  // for sync reference 
										 'result' 		  => 'Updated Successfully');								
							//$this->response('', 404);
						} 		
					}
					else
					{
						$r[] = array(	'ref_no' 		=> $data['id_inter_sync_wallet'],
										'isSucceeded' 	=> FALSE,
										"responseData" 	=> 1,  // for sync reference
										"result" 		=>$valid['error']);
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
	
	
	
	
}
?>