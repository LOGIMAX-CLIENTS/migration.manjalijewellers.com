<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Intra_sync_services extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->model("intra_sync_services_model");
	}
	
	/**
	 * Sync offline ac & payment :: Case - Customers registered online but accounts & its payments not synced during registration/login/paydues page visit 
	 * STEP 1 : Create a dummy table and Insert unsynced records.  unsync_ac_registered_cus
	 * STEP 2 : Sync existing data by passing cus mobile, cus id, cus branch
	 * STEP 3 : Update the status in unsync_ac_registered_cus
	 * STEP 4 : Check for the pending and analyse further.
	 * 
	 * Execution steps :
        1. Prepare Table : [Execute this once]
        https://sktmonline.com/sktmsavingscheme/admin/index.php/intra_sync_services/prepareUnsyncRecords
        
        2. Sync Records : [Per sync 50 records will be taken, execute this url until u get 0/50 ]
        https://sktmonline.com/sktmsavingscheme/admin/index.php/intra_sync_services/syncRecords
	 */ 
	 
	 function prepareUnsyncRecords(){
        // Customers registered online but offline a/c data not synced :
        $unsyncRec = $this->intra_sync_services_model->prepareUnsyncRecords();
        print_r($unsyncRec);
	 }
	
	function syncRecords()
    {
        $sql = $this->db->query("SELECT mobile,id_customer,cus_id_branch  FROM `unsync_ac_registered_cus` WHERE is_synced = 'N' limit 25");
        if(sizeof($sql->result_array()) >0)
        {
            foreach($sql->result_array() as $row)
            {
                $syncData = $this->sync_existing_data($row['mobile'],$row['id_customer'],$row['cus_id_branch']);
    		    echo "<pre>";print_r($syncData);
    		    $update = $this->db->query("UPDATE unsync_ac_registered_cus set is_synced='Y',date_update='".date('Y-m-d H:i:s')."' where id_customer=".$row['id_customer']);
    		    if($update)
    		    {
    		        $synced++;
    		    }
            }
        }else{
            echo "No records";
        }
        echo "Synced -".$synced."/".$sql->num_rows();
    }
    
    function sync_existing_data($mobile,$id_customer,$id_branch)
	{   
	   $this->load->model("intra_sync_services_model");
	   $data['id_customer'] = $id_customer;  
	   $data['id_branch'] = $id_branch; 
	   $data['branch_code'] = $this->intra_sync_services_model->getBrCode($id_branch);// echo $data['branch_code'];exit;
	   $data['branchWise'] = 0;  
	   $data['mobile'] = $mobile;  
	   $res = $this->intra_sync_services_model->insExisAcByMobile($data);  
	   if(sizeof($res) > 0)
	   {
	        $this->db->trans_begin();
	   		$payData = $this->intra_sync_services_model->syncPayData($res);  
	   		//echo $this->db->last_query();echo $this->db->_error_message();exit;
	   	    if(sizeof($payData['succeedIds']) > 0 || $payData['no_records'] > 0){
				$status = $this->intra_sync_services_model->updateInterTableStatus($res,$payData['succeedIds']);
				if($this->db->trans_status() === TRUE)
				{
				    $this->db->trans_commit();
					return array("status" => TRUE, "mobile" => $mobile, "msg" => "Purchase Plan registered successfully"); 
				}
				else{
				    echo "Error - Contact developer";
				    echo $this->db->last_query();
				    echo $this->db->_error_message();exit;
				    $this->db->trans_rollback();
					return array("status" => FALSE, "mobile" => $mobile, "msg" => "Error in updating intermediate tables");
				}
			}
			else
			{
				return array("status" => FALSE, "mobile" => $mobile, "msg" => "Error in updating payment tables");
			}
	   }
	   else
	   {
	   		return array("status" => FALSE, "mobile" => $mobile, "msg" => "No records to update in scheme account tables");
	   } 
	}
	
	function syncTransactions(){  
		$api_model = "sktm_syncapi_model";  
		$acc_model = "account_model";
		//$record_date = ( $type == "yesterday" ? date('Y-m-d', strtotime(date("Y-m-d") .' -1 day')) : date("Y-m-d") );
		$record_to = 2; // 2 - Online 
		$branch_id = null;
		$acc_id=""; 
        $acc_rec = 0;
        $trans_rec = 0;
        $records = 0;
        $pay_id="";
        $rejected_pay_id = "";
        $rejected_acc_id = "";
       
        $trans_data = $this->$api_model->getUnSyncedTrans();  
		if($trans_data)
        {
		  $records += count($trans_data);
		  foreach($trans_data as $trans)
		  {
		     // payment_type -> 1- Online , 2 - Offine
		     if($trans['payment_type'] == 1 ){ 
		         // to update online record
		         // check whether scheme a/c data updated
		         $isClientID =  $this->$api_model->checkClientID($trans['id_scheme_account'],""); 
    	         if($isClientID['status'] &&  $trans['is_modified'] == 1 && $trans['payment_status'] == 1){
    	            $this->db->trans_begin();
    	            $trans_data = array('receipt_no' =>  $trans['receipt_no'], 
    	                                'payment_ref_number' =>  $trans['ref_no'],
    	                                "payment_status" 	=> 1,
    								    'date_upd'	 => date("Y-m-d H:i:s"));
    				$updPayment  = $this->$api_model->updatePayment($trans_data,$trans['payment_type'],$trans['id_scheme_account'],$trans['payment_date']);
    				$trans_rec += 1;
    				if( $this->db->trans_status() == TRUE){
                      $this->db->trans_commit();
                      $trans_rec += 1;
		              $pay_id .=$trans['ref_no'].'|';
                    }else{
                      $this->db->trans_rollback();
                      $rejected_pay_id .=$trans['ref_no'].'|';
                    }
    	         } 
		     }else if($trans['payment_type'] == 2 && ($trans['client_id'] != null || $trans['client_id'] != '')){
		         // to update offline record
		         $isClientID =  $this->$api_model->checkClientID("",$trans['client_id']); 
    		         if($isClientID['status']){
    		             $this->db->trans_begin();
    		             if($trans['payment_status'] == 1){
    		                $id_branch = $this->$api_model->get_branchid($trans['branch_code']);
            				$pay_array = array ( "id_scheme_account" => $isClientID['id_scheme_account'],
            			   						"date_payment" 		=> $trans['payment_date'],
            			   					//	"id_metal" 			=> $trans['metal'],
            			   						"metal_rate" 		=> $trans['rate'],
            			   						"payment_amount"	=> $trans['amount'],
            			   						"metal_weight" 		=> $trans['weight'],
            			   						"payment_mode" 		=> $trans['payment_mode'],
            			   						"payment_status" 	=> 1,
            			   						"payment_type" 		=> "Offline",
            			   						"due_type"          => $trans['due_type'],
            			   						"installment" 		=> $trans['installment_no'],
            			   						"receipt_no" 		=> $trans['receipt_no'],
            			   						"remark" 			=> $trans['remarks'],
            			   						"discountAmt"		=> $trans['discountAmt'],
            			   						"payment_ref_number"=> $trans['ref_no'],
            									"date_upd" 			=> date('Y-m-d H:i:s'),
            									"id_branch"         => $id_branch
            			    					);	
            			    $insPayment  = $this->$api_model->insertPayment($pay_array); 
            			}else{
            				//update if offline record is with cancelled status
            				$upd_array = array ( "payment_status" 	=> 2,
            			   						"receipt_no" 		=> $trans['receipt_no'],
            			   						"remark" 			=> $trans['remarks'],
            			   						"date_upd" 			=> date('Y-m-d H:i:s'),
            			   						"payment_ref_number"=> $trans['ref_no']
            			    					);	
            
            			    $updPayment  = $this->$api_model->updatePayment($upd_array,$trans['payment_type'],$isClientID['id_scheme_account'],$trans['payment_date']);
                           
            			}
            			
            			if( $this->db->trans_status() == TRUE){
                          $this->db->trans_commit();
                          $trans_rec += 1;
			              $pay_id .=$trans['ref_no'].'|';
                        }else{
                          "Rollback!!";
                          $this->db->trans_rollback();
                          $this->db->_error_message();exit;
                          $rejected_pay_id .=$trans['ref_no'].'|';
                        }
        			
    		         }
    	         }
		         
		     }	
		      
		  }
		   
 
		if($acc_id > 0 || $pay_id > 0 || $rejected_acc_id != "" ||  $rejected_pay_id != ""){
		  $remark = array("acc" => $acc_id,	"pay" => $pay_id, "ac_error" => $rejected_acc_id , "pay_error" => $rejected_pay_id);
		  $sync_data = array(
								"total_records"   => $records,
								"scheme_accounts" => $acc_rec,
								"payments"		  => $trans_rec,	
								"sync_date"		  => date('Y-m-d H:i:s'),	
								"remark"          => json_encode($remark)
							);  
							
	
		  $this->$acc_model->insert_sync($sync_data);
		  $result =  array('message' => 'Total '.$records.' records affected '.$acc_rec.' scheme accounts and '.$trans_rec.' payments records. Error Records : Account = '.$rejected_acc_id.' Payment ='.$rejected_pay_id,'class' => 'success','title'=>'Update Client Details');

		}
        else
        {
		  $result = array('message' => 'No records to proceed ','class' => 'info','title'=>'Update Client Details');

		} 	
		echo "<pre>";print_r($result);
	}
}
?>