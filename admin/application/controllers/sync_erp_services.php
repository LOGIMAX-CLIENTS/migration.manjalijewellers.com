<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sync_erp_services extends CI_Controller

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
	
	function findArrayIndex($array, $searchKey, $searchVal) {
        foreach ($array as $k => $val) { 
            if ($val[$searchKey] == $searchVal) {
               return $k;
            }
        }
        return -1;
    }
	
	//Test API to get POST DATA to EJ
	
	public function sendNewSchAndPay()
	{ 
	    $status = 'N';
	    $record_to = 1;
		$model = self::MOD_API;
		$postData = array();
		$pay = array();
		$customers = $this->$model->getcustomerByStatus($status,$record_to);
		$transactions = $this->$model->getTransactionByTranStatus($status,$record_to);
		$i = 0; 
		foreach($customers as $cus)
		{  
		    // Customer Data
		    /*if($cus["cus_ref_no"] == NULL){*/
	    	$cusData  = array(
            			    	"CustName"      => (string) $cus["firstname"].''.(!empty($cus["lastname"])?' '.$cus["lastname"] : ''),
                    			"MobileNo"      => (string) $cus["mobile"],
                    			"Emailid"       => (string) $cus["email"],
                    			"warehouse"     => (string) $cus["warehouse"],
                    			"CompanyId"     => (string) $cus["ref_comp_id"],
                    			"Nominee"       => (string) $cus["nominee"],
                    			"NomineePhno"   => (string) $cus["nominee_mobile"],
    						    "PayRefNo"     	=> (int) $cus["ref_no"], 
    						    "SchAcRefNo"    => (int) $cus["id_scheme_account"], 
                    			// *"BranchId"  => (string) $cus["id_branch"]
    	                    ); 
		    /*}*/
		    if($cus["cus_ref_no"] != NULL){
		        $cusData['CustAccount'] = (string) $cus["cus_ref_no"];
		        /*$cusData = array(
            			    	"CustName"      => (string) $cus["firstname"].''.(!empty($cus["lastname"])?' '.$cus["lastname"] : ''),
                    			"MobileNo"      => (string) $cus["mobile"],
                    			"CustAccount"   => (string) $cus["cus_ref_no"],
                    			"warehouse"     => (string) $cus["warehouse"]
    	                    ); */
		    }  
		    // Scheme Account Data
		    if($cus["clientid"] == NULL || $cus["scheme_ac_no"] == NULL || $cus["group_code"] == NULL){
    	    	$sch = array(	
                           //* "cus_reg_id"	    =>	(string) $cus["id_customer_reg"], 
                            "SchemeRegDate"		=>  (string) $cus["reg_date"],
                            "Maturitydate"		=>  (string) $cus["maturity_date"], 
                            "SchemeCode"        =>  (string) $cus["sync_scheme_code"]
                         ); 
		    }
		    elseif($cus["clientid"] != NULL ){
		        $sch = array(	
                       //* "cus_reg_id"	    =>	(string) $cus["id_customer_reg"], 
                        "SchemeAccountNo"   =>  (string) $cus["scheme_ac_no"], 
                        "SchemeCode"        =>  (string) $cus["sync_scheme_code"]
                     ); 
		    } 
		    //Payment Data
		    foreach($transactions as $transaction)
			{
		        if($cus["id_scheme_account"] == $transaction['id_scheme_account']){
		        	$customerPaidAmount=($transaction["amount"]-$transaction["discountAmt"]); 
		        	$mode = $transaction["payment_mode"];
		        	$rate = ($transaction["weight"] > 0 ? number_format((float) $transaction["rate"], 3, '.', '') : NULL);
    				$pay = array(
								//"SchemeAccountNo" 	=> (string) $transaction["client_id"],
								"TransDate" 	    => (string) $transaction["payment_date"],
								"AMOUNT" 		    => number_format((float)$transaction["amount"], 2, '.', ''),
								"EMIAmt"            => number_format((float)$transaction["amount"], 2, '.', ''),
								"discountAmt"       => number_format((float)$transaction["discountAmt"], 2, '.', ''),
								"CustomerPaidAmount"=> number_format((float)$customerPaidAmount, 2, '.', ''),
								"Weight" 		    => number_format((float)$transaction["weight"], 3, '.', '') ,
								"MetalRate" 		=> $rate ,
								"pay_trans_id" 	    => (string)$transaction["pay_trans_id"],
								"PAYMENTMODE" 	    => (string)($mode == 'CC' ? 'PMG' :($mode == 'CSH' ? 'CASH' : "PMG")), // PMG - Payment Gateway
								"DueType"           => (string)($transaction["due_type"] == 'ND' ? 'Current':($transaction["due_type"] == 'AD' ? 'Advance':($transaction["due_type"] == 'PD' ? 'Pending':'')) ),
								"NoOfMonth"			=> 1
                                //*"id_scheme_account" => (string) $transaction["id_scheme_account"],
							);
			    }
			} 
			if(sizeof($pay) > 0){
			    $postData[] = array_merge($cusData,$sch,$pay);		 
			}
			   
		}
		
		if (!is_dir($this->log_dir.'/jewelone')) {
            mkdir($this->log_dir.'/jewelone', 0777, true);
        }
        $log_path = $this->log_dir.'/jewelone/erp_service.txt';
		if($postData)
		{ 
		    //echo json_encode($postData);exit;
			$sendData = $this->erpCurl($postData,"Scheme"); 
		 
			if($sendData['status']){
				foreach($sendData['data'] as $r){
					// UPDATE Read Status in customer_reg and transaction
					$find_key = $this->findArrayIndex($postData,'PayRefNo',$r->PayRefNo); 
			        if($find_key >= 0){
			            $schData = $postData[$find_key];
			        } 
					$updCusRegData = array("ref_no" => $r->PayRefNo, "cus_ref_no" => $r->CustAccount, "scheme_ac_no" => $r->SchemeAccountNo ,"clientid" => $r->SchemeAccountNo, "is_modified" => 1, "record_to" => 2, "is_transferred" => 'N',"transfer_date" => date("Y-m-d")) ;
					$updTrans = array("receipt_no" => $r->CashAdvanceNo, "ref_no" => $r->PayRefNo,"client_id" => $r->SchemeAccountNo, "is_modified" => 1, "record_to" => 2, "is_transferred" => 'N',"transfer_date" => date("Y-m-d"));
					$this->db->trans_begin();
					$this->$model->updInterTables($updCusRegData,$updTrans,$schData['SchAcRefNo']);
					
					    echo '\n'.$this->db->last_query();
					if($this->db->trans_status() == TRUE){
					    $this->db->trans_commit();
					}else{
					    $this->db->trans_rollback();
					    echo "Rolled Back ..".print_r($r);
            		    $data = "\n --".date('Y-m-d H:i:s')."-- \n : Rolled Back ..".print_r($r);
            			file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX);
					}
				}
			}
			$data = "\n SP --".date('Y-m-d H:i:s')." -- : ".json_encode($sendData);
			file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX); 
			if($sendData['status'] == FALSE){ 
				$this->sendEmail("Jewelone sendNewSchAndPay Service Call Error",$data);
			}
			header('Content-Type: application/json');
			echo json_encode($sendData);
		}	 
		else
		{
		    $data = "\n --".date('Y-m-d H:i:s')." -- : No new a/c to send offline.";
			file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX); 
			echo "No payment records to send offline.";
		}
		
	}
	
	
	public function sendNewPay()
	{					
	    $status = 'N';
	    $record_to = 1;
		$model = self::MOD_API;
		$postData = array();
		$transactions = $this->$model->getTransactionByTranStatus($status,$record_to); 
		$i = 0;
		//Payment Data
	    foreach($transactions as $transaction)
		{ 
			if(!empty($transaction['client_id'])){ 
				$cus = $this->$model->getCusRegData($transaction["client_id"]);   
			
				if(sizeof($cus) > 0){
					$customerPaidAmount = ($transaction["amount"]-$transaction["discountAmt"]); 
					$mode = $transaction["payment_mode"];
					$pay = array();
					$rate = ($transaction["weight"] > 0 ? number_format((float) $transaction["rate"], 3, '.', '') : NULL);
					$pay = array(
								"PayRefNo"     		=> (int) $transaction["ref_no"], 
								"SchemeAccountNo" 	=> (string) $transaction["client_id"],
								"TransDate" 	    => (string) $transaction["payment_date"],
								"AMOUNT" 		    => number_format((float)$transaction["amount"], 2, '.', ''),
								"EMIAmt"            => number_format((float)$transaction["amount"], 2, '.', ''),
								"discountAmt"       => number_format((float)$transaction["discountAmt"], 2, '.', ''),
								"CustomerPaidAmount"=> number_format((float)$customerPaidAmount, 2, '.', ''),
								"Weight" 		    => number_format((float)$transaction["weight"], 3, '.', '') ,
								"MetalRate" 		=> $rate ,
								"pay_trans_id" 	    => (string)$transaction["pay_trans_id"],
								"PAYMENTMODE" 	    => (string)($mode == 'CC' ? 'PMG' :($mode == 'CSH' ? 'CASH' : "PMG")), // PMG - Payment Gateway
								"DueType"           => (string)($transaction["due_type"] == 'ND' ? 'Current':($transaction["due_type"] == 'AD' ? 'Advance':($transaction["due_type"] == 'PD' ? 'Pending':'')) ),
								"NoOfMonth"			=> 1
					                //*"id_scheme_account" => (string) $transaction["id_scheme_account"],
							); 
					 
					// Customer Data
					$cusData = array();
					if($cus["cus_ref_no"] != NULL){
			        	$cusData = array(
		            			    	"CustName"      => (string) $cus["firstname"].''.(!empty($cus["lastname"])?' '.$cus["lastname"] : ''),
		                    			"MobileNo"      => (string) $cus["mobile"],
		                    			"CustAccount"   => (string) $cus["cus_ref_no"],
		                    			"warehouse"     => (string) $cus["warehouse"],
		                    			"CompanyId"     => (string) $cus["ref_comp_id"],
		                    			);
			    	}  
			    	// Scheme Account Data
			    	$sch = array();
				    if($transaction["client_id"] != NULL ){
				        $sch = array(	
		                           //* "cus_reg_id"	    =>	(string) $cus["id_customer_reg"], 
		    	                        "SchemeAccountNo"   =>  (string) $cus["scheme_ac_no"], 
		    	                        "SchemeCode"        =>  (string) $cus["sync_scheme_code"]
		    	                     ); 
				    } 	     
					$postData[] = array_merge($cusData,$sch,$pay);
				}
				
			}
		}
		
		if (!is_dir($this->log_dir.'/jewelone')) {
            mkdir($this->log_dir.'/jewelone', 0777, true);
        }
        $log_path = $this->log_dir.'/jewelone/erp_service.txt';
		if(sizeof($postData) > 0)
		{  
		    $data = "\n P --".date('Y-m-d H:i:s')." -- Request : ".json_encode($postData);
			file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX); 
			$sendData = $this->erpCurl($postData,"SchemePayment");
			if($sendData['status']){
				foreach($sendData['data'] as $r){
					// UPDATE Read Status in transaction
					$updTrans = array("receipt_no" => $r->CashAdvanceNo, "ref_no" => $r->PayRefNo,"client_id" => $r->SchemeAccountNo, "is_modified" => 1, "record_to" => 2, "is_transferred" => 'N',"transfer_date" => date("Y-m-d"));
					$this->db->trans_begin();
					$this->$model->updTransTable($updTrans);
					if($this->db->trans_status() == TRUE){
					    $this->db->trans_commit();
					}else{
					    $this->db->trans_rollback();
					    $query['query'] = $this->db->last_query();
					    $query['error'] = $this->db->_error_message();
					    echo "Rolled Back ..".print_r($r);
            		    $data = "\n --".date('Y-m-d H:i:s')."-- : Rolled Back ..".json_encode($r).". Query :".json_encode($query);
            			file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX); 
            			$this->sendEmail("Jewelone sendNewPay Rolled Back",$data);
					}
				}
			}
			$data = "\n P --".date('Y-m-d H:i:s')." -- : ".json_encode($sendData);
			file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX); 
			if($sendData['status'] == FALSE){ 
				$this->sendEmail("Jewelone sendNewPay Service Call Error",$data);
			}
			echo json_encode($sendData);
		}	 
		else
		{
		    $data = "\n --".date('Y-m-d H:i:s')." -- : No payment records to send offline."; 
			file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX); 
			echo "No payment records to send offline.";
		}	
		header('Content-Type: application/json'); 
		echo sizeof($postData); 
	}
	
	
	function erpCurl($postData,$api){ 
	    $bearer = $this->getBearerToken();
		$curl = curl_init();   
	    curl_setopt_array($curl, array(
	    CURLOPT_URL => $this->config->item('erp_baseURL')."".$api,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => "",
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 0,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POSTFIELDS => json_encode($postData),
	    CURLOPT_HTTPHEADER => array(
	      //"Authorization: Basic ".$this->config->item('erpAuthKey'),
	      "Authorization: Bearer ".$bearer,
	      "Content-Type: application/json",
	      "cache-control: no-cache"
	    ),
	   ));
	   
	   $response = curl_exec($curl);
	   $err = curl_error($curl); 
	   curl_close($curl);
	   
	   if (!is_dir($this->log_dir.'/jewelone')) {
            mkdir($this->log_dir.'/jewelone', 0777, true);
        }
        $log_path = $this->log_dir.'/jewelone/erp_service.txt';
	   if ($err) { 
	    	//echo "cURL Error #:" . $err;
			$data = "\n ERROR : --".date('Y-m-d H:i:s')." -- Error in CURL CALL !! \n ".$err;
			file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX); 
			//echo "Error in CURL CALL !!";
			return array("status" => FALSE, "data" => $err);
	   } else {
	   		$res = json_decode($response);
	   		if(gettype($res) == "array"){
				$data = "\n --".date('Y-m-d H:i:s')."-- \n [REQUEST] ".json_encode($postData)." [RESPONSE] ".json_encode($res);
	   			return array("status" => TRUE, "data" => $res);							
			}else{ 
				$data = "\n --".date('Y-m-d H:i:s')."-- \n Response : ".$response;
				return array("status" => FALSE, "data" => $response);
			}
			file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX); 
	   }
	}
    
    // update Bearer Token for ERP api call
	
	public function getBearerToken(){
        $curl = curl_init();
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->config->item('erp_baseURL')."loginRequest",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 300,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "UserName=".$this->config->item('ejUserName')."&Password=".$this->config->item('ejPassword')."&grant_type=password",
          CURLOPT_HTTPHEADER => array(
            "Content-Type: application/x-www-form-urlencoded",
            "cache-control: no-cache"
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
            $result =  json_decode($response);
            return $result->access_token; 
        }
	}

	// to update scheme ac and payments
	function update_client()
	{   
		$api_model = self::MOD_API;  
		$record_to = 2; // 2 - Online 
		$trans_date = date('Y-m-d');
		$acc_id=""; 
        $acc_rec = 0;
        $trans_rec = 0;
        $records = 0;
        $pay_id="";
		$cus_reg_data = $this->$api_model->getcustomerByStatus('N',$record_to,$trans_date);  
		if($cus_reg_data)
        {
           $records += count($cus_reg_data);
           foreach($cus_reg_data as $client)
		   {
               // is_registered_online -> 0 - No, 1- Yes , 2 - online record
		       if($client['is_modified'] == 1 && $client['is_registered_online'] >= 1){
		       		if($client['clientid'] != null){
						$isClientID =  $this->$api_model->checkClientID("",$client['clientid']);
		           
	    		       if($isClientID['status']){
	    		             if($client['closing_date'] != ''){
        						$closing_date = date("Y-m-d H:i:s", strtotime($client['closing_date']));
        					 }else{
        						$closing_date = NULL;
        					 }
        					 if($client['fixed_rate_on'] != ''){
        						$fixed_rate_on = date("Y-m-d H:i:s", strtotime($client['fixed_rate_on']));
        					 }else{
        						$fixed_rate_on = NULL;
        					 }
	    					 $acc_data = array(
            	    					    'fixed_rate_on'     => $fixed_rate_on,
            	    					    'rate_fixed_in'     => $client['rate_fixed_in'],
            								'fixed_wgt'      	=> $client['fixed_wgt'],
            								'fixed_metal_rate'  => $client['fixed_metal_rate'],
	    									'closed_by'         => $client['closed_by'],
	                    					'closing_date'      => $closing_date,
	                    					'closing_amount'    => $client['closing_amount'],
	                    					'closing_balance'    =>($client['closing_weight']==NULL || $client['closing_weight']==0? $client['closing_amount']: $client['closing_weight']),
	                    					'closing_weight'    => $client['closing_weight'],
	                    					'closing_add_chgs'  => $client['closing_add_chgs'],
	                    					'additional_benefits'=> $client['additional_benefits'],
	                    					'remark_close'      => $client['remark_close'],
	                    					'is_closed'         => $client['is_closed'],
	                    					'active'            => ($client['is_closed'] == 1 ? 0:1),
	                                        'date_upd'	        => date("Y-m-d H:i:s")								
	        							);
	        						//	print_r($acc_data);exit;
	        				 $acc_status = $this->$api_model->update_closed_ac($acc_data,$client['clientid'],$client['id_customer_reg']);
	    		       }else{
						  $acc_data = array(
	    								'scheme_acc_number'       => $client['scheme_ac_no'],
	    								'ref_no'                  => $client['clientid'],
	                                    'date_upd'	              => date("Y-m-d H:i:s")								
	    							    );
	    				  $acc_status = $this->$api_model->update_account($acc_data,$client['id_scheme_account'],$client['id_customer_reg']);
					   }
					} 	 
    				if($acc_status)
    				{
    					$acc_rec +=1;				
    				    $acc_id .=$client['id_scheme_account'].'|';
    				    $inter_data = array('is_transferred' => 'Y', 'is_modified'=>'N','transfer_date' => date('Y-m-d'),'clientid'=>$client['clientid'] );
				        $this->$api_model->updateCusRegData($inter_data);
    				}
		       }
		   }
        } 
        $trans_data = $this->$api_model->getTransactionByTranStatus('N',$record_to,$trans_date); 
		if($trans_data)
        {
		  $records += count($trans_data);
		  foreach($trans_data as $trans)
		  { 
		     // payment_type -> 1- Online , 2 - Offine
		     if($trans['payment_type'] == 1){ 
		         // to update online record
		         // check whether scheme a/c data updated
		         $isClientID =  $this->$api_model->checkClientID($trans['id_scheme_account'],"");
    	         if($isClientID['status'] &&  $trans['is_modified'] == 1 && $trans['payment_status'] == 1){
    	            $trans_data = array('receipt_no' =>  $trans['receipt_no'], 
    	                                'payment_ref_number' =>  $trans['ref_no'],
    	                                "payment_status" 	=> 1,
    								    'date_upd'	 => date("Y-m-d H:i:s"));
    				$updPayment  = $this->$api_model->updatePayment($trans_data,$trans['payment_type'],$trans['id_scheme_account'],$trans['payment_date']);
    				$trans_rec += 1;
				    $pay_id .=$trans['ref_no'].'|';
    	         }else{
    	             if( $trans['is_modified'] == 1){
        			      //update if offline record is with cancelled status
            				$upd_array = array ( "payment_status" 	=> 4,
            			   						"receipt_no" 		=> $trans['receipt_no'],
            			   						"remark" 			=> $trans['remarks'],
            			   						"date_upd" 			=> date('Y-m-d H:i:s'),
            			   						"payment_ref_number"=> $trans['ref_no']
            			    					);	
            
            			    $updPayment  = $this->$api_model->updatePaymentStatus($upd_array);
            				if($updPayment){
        						$trans_rec += 1;
    			                $pay_id .=$trans['ref_no'].'|';
            				}  
        			  }  
    			 }
		     }else if($trans['payment_type'] == 2 && ($trans['client_id'] != null || $trans['client_id'] != '')){
		         // to update offline record
		         $isClientID =  $this->$api_model->checkClientID("",$trans['client_id']);
		         if($isClientID['status']){
		             if(($trans['payment_status'] == 1 || $trans['payment_status'] == 8) && $trans['is_modified'] == 0){
        				$pay_array = array ( "id_scheme_account" => $isClientID['id_scheme_account'],
        			   						"id_branch" 		=> $trans['id_branch'],
        			   						"date_payment" 		=> $trans['payment_date'],
        			   						"date_add" 			=> $trans['payment_date'],
//            			   						"id_metal" 			=> $trans['id_metal'],
        			   						"metal_rate" 		=> $trans['rate'],
        			   						"payment_amount"	=> $trans['amount'],
        			   						"actual_trans_amt"	=> $trans['amount'],
        			   						"metal_weight" 		=> $trans['weight'],
        			   						"payment_mode" 		=> $trans['payment_mode'],
        			   						"payment_status" 	=> $trans['payment_status'],
        			   						"payment_type" 		=> "Offline",
        			   						"due_type"          => $trans['due_type'],
        			   						"due_month"         => $trans['due_month'],
        			   						"due_year"          => $trans['due_year'],
        			   						"installment" 		=> $trans['installment_no'],
        			   						"receipt_no" 		=> $trans['receipt_no'],
        			   						"remark" 			=> $trans['remarks'],
        			   						"discountAmt"		=> !empty($trans['discountAmt']) ? $trans['discountAmt'] : 0,
        			   						"payment_ref_number"=> $trans['ref_no'],
        									"date_upd" 			=> date('Y-m-d H:i:s')
        			    					);	
        
        			    $insPayment  = $this->$api_model->insertPayment($pay_array);
        			    if($insPayment){					
    						$trans_rec += 1;
			                $pay_id .=$trans['ref_no'].'|';
        				}
        			}else{
        			    if( $trans['is_modified'] == 1){
        			      //update if offline record is with cancelled status
            				$upd_array = array ( "payment_status" 	=> 4,
            			   						"receipt_no" 		=> $trans['receipt_no'],
            			   						"remark" 			=> $trans['remarks'],
            			   						"date_upd" 			=> date('Y-m-d H:i:s'),
            			   						"payment_ref_number"=> $trans['ref_no']
            			    					);	
            
            			    $updPayment  = $this->$api_model->updatePaymentStatus($upd_array);
            				if($updPayment){
        						$trans_rec += 1;
    			                $pay_id .=$trans['ref_no'].'|';
            				}  
        			    }
        				
        
        			}
    			
		         }
    	           
    	      }
		         
		   }			  
		}
		 
		if($acc_id != '' || $pay_id != ''){
			$remark = array("acc" => $acc_id,	"pay" => $pay_id);
			$sync_data = array(
								"total_records"   => $records,
								"scheme_accounts" => $acc_rec,
								"payments"		  => $trans_rec,	
								"sync_date"		  => date('Y-m-d H:i:s'),	
								"remark"          => json_encode($remark)
							); 
			$this->load->model('account_model');
			$this->account_model->insert_sync($sync_data);
		
			$res = array('message' => 'Total '.$records.' records .Updated '.$acc_rec.' scheme accounts and '.$trans_rec.' payments records. ','class' => 'success','title'=>'Update Client Details');
		}
        else
        {
			$res = array('message' => 'No updates to proceed','class' => 'danger','title'=>'Update Client Details');
		}
		echo json_encode($res);
	}
	
	function sendEmail($subject,$message){
		$this->load->model('email_model');
		$bcc = "pavithra@vikashinfosolutions.com";
		$sendEmail = $this->php_mail('support@logimaxindia.com',$subject,$message,'',$bcc);
		//echo 1;exit;
		return true;
	}
	
	public function php_mail($email_to,$email_subject,$email_message,$email_cc,$email_bcc,$attachment="") 		{ 
		 $config = array();
                $config['useragent']     = "CodeIgniter";
                $config['mailpath']      = "/usr/bin/sendmail"; // or "/usr/sbin/sendmail"
                $config['protocol']      = "smtp";
                $config['smtp_host']     = "localhost";
                $config['smtp_port']     = "25";
                $config['mailtype']		 = 'html';
                $config['charset'] 		 = 'utf-8';
                $config['newline'] 		 = "\r\n";
                $config['wordwrap']		 = TRUE;
                $this->load->library('email');
                $this->email->initialize($config);
                $this->email->from('noreply@logimax.co.in', 'Emerald');
                $this->email->to($email_to); 
				if($email_cc!="")
				{
					$this->email->cc($email_cc); 
				}
             
     			if($email_bcc!="")
				{
                   $this->email->bcc($email_bcc); 
			    }
                $this->email->subject($email_subject);       
            	$this->email->message($email_message);
            	   
           return $this->email->send();           
			 
	}
	
	

}