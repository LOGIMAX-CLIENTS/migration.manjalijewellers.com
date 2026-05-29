<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Khimji_services extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model("integration_model");
		ini_set('date.timezone', 'Asia/Calcutta');
        $this->log_dir = '..log/'.date("Y-m-d").'/khimji';
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
	function generateAcNoOrReceiptNo(){	
        $log_path = $this->log_dir.'/'.date("Y-m-d").'.txt'; 
        if(!empty($_POST))
        {
            if($_POST['id_payment'] != '' && $_POST['id_payment'] > 0)
            {
                $payData = $this->integration_model->getPayDataById();
            }
        }
	    $payData = $this->integration_model->getPayData();
		if(sizeof($payData) > 0)
		{
		  //  echo "<pre>";print_r($payData);
			foreach ($payData as $pay)
			{
               $postData = array(
                            "isKycValidationCheck" => false,
    					    "transactionType"=> 2,
    					    "tranUniqueId"	=> $pay['offline_tran_uniqueid'],
    					    "branchCode"	=> $pay['warehouse'],
    					    "paymentDetail"	=> array(
    											"paymentType" 		=> 9,
    											"paymentTypeName" 	=> "Online",
    											"amount" 			=> $pay['payment_amount'],
    											"authorizationNo" 	=> $pay['id_transaction'],
    											"narration" 		=> $pay['payment_type']."-".$pay['payment_mode'],
    											"originalAmt" 		=> $pay['payment_amount'],
    											"transationAmt" 	=> $pay['payment_amount'],
    											"marchantCharges" 	=> 0.00,
    											"cardChargesAmount" => 0.00,
    											"cardChargesPercentage" => 0
    											)
    					);
                $is_new_ac = ($pay['scheme_acc_number'] != "" && $pay['scheme_acc_number'] != NULL ? FALSE : TRUE);
                if(!$is_new_ac){ // Account number already generated
                    $postData["orderNo"] = $pay['scheme_acc_number'];
    		    }
    		   echo "<p style='color:red'>STEP 2 (saveSchemeOrInstallmentDetails): \n POST DATA : </p><pre>";print_r($postData);
               $response = $this->integration_model->khimji_curl('app/v1/saveSchemeOrInstallmentDetails',$postData);
               if($response['status'] == TRUE){
                   	$resData = $response['data']->data; 
                   	if($resData->errorCode == 0){ // $resData->status == TRUE && 
                   		if(isset($resData->result->orderNo)){
    						$acData = array(
    									 'scheme_acc_number'=> $resData->result->orderNo,
    									 'date_upd'			=> date("Y-m-d H:i:s")
    									 );
    						$this->integration_model->updateData($acData,'id_scheme_account',$pay['id_scheme_account'],'scheme_account');
    						$payData = array(
    									 'receipt_no'	=> $resData->result->orderNo,
    									 'date_upd'		=> date("Y-m-d H:i:s")
    									 );
    						$this->integration_model->updateData($payData,'id_payment',$pay['id_payment'],'payment');
    						echo "Updated A/C Number and Receipt Number as ".$resData->result->orderNo;
    		    			return true;						
    					}
    					if(isset($resData->result->installmentNo)){
    						$payData = array(
    									 'receipt_no'	=> $resData->result->installmentNo,
    									 'date_upd'		=> date("Y-m-d H:i:s")
    									 );
    						$this->integration_model->updateData($payData,'id_payment',$pay['id_payment'],'payment');
    						echo "Updated Receipt Number as ".$resData->result->installmentNo;
    				// 		return true;
    					}
                    }
                    else if($resData->errorCode == 1001 && $resData->errorMsg == "Document Already Saved In Padm."){
    					if(isset($resData->result->orderNo)){
    						$payData = array(
    									 'receipt_no'	=> $resData->result->orderNo,
    									 'date_upd'		=> date("Y-m-d H:i:s")
    									 );
    						$this->integration_model->updateData($payData,'id_payment',$pay['id_payment'],'payment');
    						echo "Updated Receipt Number as ".$resData->result->orderNo;
    				// 		return true;
    					}
                    }
                    else if($resData->errorCode == 1001){
    					$payData = array(
									 'offline_error_msg'	=> date("Y-m-d H:i:s")." Acc or Receipt Error : ".$resData->errorMsg,
									 );
						$this->integration_model->updateData($payData,'id_payment',$pay['id_payment'],'payment');
                    }
               }else{
                    $subject = "Khimji - Rate API Error";
                    $message = json_encode($response,true);
                    $this->sendEmail($subject,$message);
               }
                // Write log in case of API call failure 
                $logData = "\n".date('d-m-Y H:i:s')."\n API : app/v1/saveSchemeOrInstallmentDetails \n Post Data : ".json_encode($postData)." \n Response : ".json_encode($response,true);
        	    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
        	    echo "<p style='color:red'>CUSTOMER : ".$pay['mobile']." ".$pay['firstname']." | AC No. : ".$pay['scheme_acc_number'].' </p>';
        	    echo "<p style='color:red'>RESPONSE : <pre>";print_r($response).'  </p>';
			}
		}else{
		   // Write log in case of API call failure 
            $logData = "\n".date('d-m-Y H:i:s')."\n API : app/v1/saveSchemeOrInstallmentDetails - No Pending data available !!";
    	    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
    	    echo "No Pending data available !!";
    	    return true; 
		}
	}
	
		function generateAcNoOrReceiptNoById(){	
        $log_path = $this->log_dir.'/'.date("Y-m-d").'.txt'; 
      
	    $payData = $this->integration_model->getPayDataById();
		if(sizeof($payData) > 0)
		{
		  //  echo "<pre>";print_r($payData);
			foreach ($payData as $pay)
			{
               $postData = array(
                            "isKycValidationCheck" => false,
    					    "transactionType"=> 2,
    					    "tranUniqueId"	=> $pay['offline_tran_uniqueid'],
    					    "branchCode"	=> $pay['warehouse'],
    					    "paymentDetail"	=> array(
    											"paymentType" 		=> 9,
    											"paymentTypeName" 	=> "Online",
    											"amount" 			=> $pay['payment_amount'],
    											"authorizationNo" 	=> $pay['id_transaction'],
    											"narration" 		=> $pay['payment_type']."-".$pay['payment_mode'],
    											"originalAmt" 		=> $pay['payment_amount'],
    											"transationAmt" 	=> $pay['payment_amount'],
    											"marchantCharges" 	=> 0.00,
    											"cardChargesAmount" => 0.00,
    											"cardChargesPercentage" => 0
    											)
    					);
                $is_new_ac = ($pay['scheme_acc_number'] != "" && $pay['scheme_acc_number'] != NULL ? FALSE : TRUE);
                if(!$is_new_ac){ // Account number already generated
                    $postData["orderNo"] = $pay['scheme_acc_number'];
    		    }
    		   //echo "<p style='color:red'>STEP 2 (saveSchemeOrInstallmentDetails): \n POST DATA : </p><pre>";print_r($postData);
    	
               $response = $this->integration_model->khimji_curl('app/v1/saveSchemeOrInstallmentDetails',$postData);
               if($response['status'] == TRUE){
                   	$resData = $response['data']->data; 
                   	if($resData->errorCode == 0){ // $resData->status == TRUE && 
                   		if(isset($resData->result->orderNo)){
    						$acData = array(
    									 'scheme_acc_number'=> $resData->result->orderNo,
    									 'date_upd'			=> date("Y-m-d H:i:s")
    									 );
    						$this->integration_model->updateData($acData,'id_scheme_account',$pay['id_scheme_account'],'scheme_account');
    						$payData = array(
    									 'receipt_no'	=> $resData->result->orderNo,
    									 'date_upd'		=> date("Y-m-d H:i:s")
    									 );
    						$this->integration_model->updateData($payData,'id_payment',$pay['id_payment'],'payment');
    						$return_data = "Updated A/C Number and Receipt Number as ".$resData->result->orderNo;
    						
    		    			//return true;						
    					}
    					if(isset($resData->result->installmentNo)){
    						$payData = array(
    									 'receipt_no'	=> $resData->result->installmentNo,
    									 'date_upd'		=> date("Y-m-d H:i:s")
    									 );
    						$this->integration_model->updateData($payData,'id_payment',$pay['id_payment'],'payment');
    						echo "Updated Receipt Number as ".$resData->result->installmentNo;
    				// 		return true;
    					}
                    }
                    else if($resData->errorCode == 1001 && $resData->errorMsg == "Document Already Saved In Padm."){
    					if(isset($resData->result->orderNo)){
    						$payData = array(
    									 'receipt_no'	=> $resData->result->orderNo,
    									 'date_upd'		=> date("Y-m-d H:i:s")
    									 );
    						$this->integration_model->updateData($payData,'id_payment',$pay['id_payment'],'payment');
    						$return_data= "Updated Receipt Number as ".$resData->result->orderNo;
    				// 		return true;
    					}
                    }
                    else if($resData->errorCode == 1001){
    					$payData = array(
									 'offline_error_msg'	=> date("Y-m-d H:i:s")." Acc or Receipt Error : ".$resData->errorMsg,
									 );
						$this->integration_model->updateData($payData,'id_payment',$pay['id_payment'],'payment');
                    }
               }else{
                    $subject = "Khimji - Rate API Error";
                    $message = json_encode($response,true);
                    $this->sendEmail($subject,$message);
               }
                // Write log in case of API call failure 
                $logData = "\n".date('d-m-Y H:i:s')."\n API : app/v1/saveSchemeOrInstallmentDetails \n Post Data : ".json_encode($postData)." \n Response : ".json_encode($response,true);
        	    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
        	    $return_data =  "CUSTOMER : ".$pay['mobile']." ".$pay['firstname']." | AC No. : ".$pay['scheme_acc_number'].'';
        	    $return_data =  "RESPONSE : <pre>".($response).'  </p>';
			}
		}else{
		   // Write log in case of API call failure 
            $logData = "\n".date('d-m-Y H:i:s')."\n API : app/v1/saveSchemeOrInstallmentDetails - No Pending data available !!";
    	    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
    	    $return_data =  "No Pending data available !!";
    	   // return true; 
		}

		$this->session->set_flashdata('chit_alert',array('message'=> "REQUEST : \n".json_encode($postData)."\n RESPONSE: <pre>".json_encode($response),'class'=>'success','title'=>"Result"));
		echo json_encode(true);
	}
	
	
    
    function regCusInKhimji($id_customer=""){
       $log_path = $this->log_dir.'/'.date("Y-m-d").'.txt'; 
       //Sync Existing Data 	 
       $this->load->model("integration_model"); 
       $cusData = $this->integration_model->getEmptyCusCodeData($id_customer);
       foreach($cusData as $data){
           //echo "<pre>";print_r($data);exit;
           $postData = array(
    						"appCustomerCode"	=> 0 ,
    						"custName" 			=> ucwords($data['firstname']),
    						"mobileNo" 			=> trim($data['mobile']),
    						"emailId" 			=> $data['email'],
    						"preferdBranch" 	=> "",
    						"branchCode" 		=> "",
    						"custdetails" 		=> array(
    						                        'address1'	=> (isset($data['address1'])?$data['address1']:NULL),
							                        'address2'	=> (isset($data['address2'])?$data['address2']:NULL),
    						                        "city"      => isset($data['cityname'])?$data['cityname']:"Coimbatore", 
    						                        "state"     => isset($data['statename'])?$data['statename']:"Tamil Nadu",
    						                        "adharNo"   => NULL
    						                        )
    					);
           $response = $this->integration_model->khimji_curl('registerCustomerWithoutValidateOtp',$postData);
            echo "<pre>";print_r($postData);
           if($response['status'] == 1){
               	$resData = $response['data'];
               	if($resData->status == 1 && $resData->errorCode == 0 && $resData->result[0]->mobileNo == trim($data['mobile'])){
                   $updCus = array(
                        			"pan" 			=> $resData->result[0]->panNo, 
                        			"app_cus_code" 	=> $resData->result[0]->appCustomerCode, 
                        			"reference_no" 	=> $resData->result[0]->customerCode, 
                        			"date_upd" 		=> date('Y-m-d H:i:s')
                    			);
                   $this->integration_model->updateData($updCus,'mobile',$resData->result[0]->mobileNo,'customer');
                   echo "Cus code ".$resData->result[0]->appCustomerCode." Updated for mobile no.".$resData->result[0]->mobileNo;
                }else{
		            $logData = "\n".date('d-m-Y H:i:s')."\n API : registerCustomerWithoutValidateOtp \n PostData : ".json_encode($postData,true)." \n Response : ".json_encode($resData,true);
				    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
				}
            }else{
                $logData = "\n".date('d-m-Y H:i:s')."\n API : registerCustomerWithoutValidateOtp \n PostData : ".json_encode($postData,true)." \n Response : ".json_encode($response,true);
    		    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
            }
            echo "<pre>";print_r($logData);
       }
       if($id_customer > 0){
            redirect('customer');
       }else{
            echo count($cusData)." Records processed..";    
       }
    }
    
	function khimji_curl($api,$postData)
    {
    	$curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->config->item('khimji-baseURL')."".$api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postData),
            // Getting  server response parameters //
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "Content-Type: application/json",
                "X-Key: ".$this->config->item('khimji-X-Key'),
                "Authorization: ".$this->config->item('khimji-Authorization')
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
        	$log_path = $this->log_dir.'/generate_'.date("Y-m-d").'.txt'; 
            $logData = "\n".date('d-m-Y H:i:s')."\n API : ".$api." \n POST : ".json_encode($postData,true)."\n Error : ".json_encode($err,true);
		    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
            return array('status' => FALSE, 'data' => $err);
        } 
        else {
            return array('status' => TRUE, 'data' => json_decode($response));
        }
    }
    function getMetalRate($branchName,$id_branch){
        $date1 = DateTime::createFromFormat('h:i a', date("h:i a"));
        $date2 = DateTime::createFromFormat('h:i a', "08:00 am");
        $date3 = DateTime::createFromFormat('h:i a', "08:00 pm");
        if ($date1 > $date2 && $date1 < $date3)
        {
            $memcache = new Memcache();
            $memcache->connect("127.0.0.1", 11211);
            $log_path = $this->log_dir.'/rate_'.date("Y-m-d").'.txt';
            $this->load->model("integration_model");
            $postData = array(
                            "branchName"	=> $branchName // displayName from khimji branch master
                        );
            $response = $this->integration_model->khimji_curl('user/getLatestMetalRate',$postData);
            //echo "<pre>";print_r($response);
            if($response['status']){
               	$resData = $response['data'];
               	if($resData->status == 1 && $resData->errorCode == 0){
                    $rates = $resData->metalRateDetails;
                    foreach($rates as $rate){
                        $insertData['updatetime'] = date('Y-m-d H:i:s',strtotime(str_replace('/','-',$rate->LastUpdatedDateTime)));
                        /*if($rate->Purity == 75 && $rate->itemType == "Gold")
                        $insertData['goldrate_18ct'] = $rate->SaleRate;
                        if($rate->Purity == 91.6 && $rate->itemType == "Gold")
                        $insertData['goldrate_22ct'] = $rate->SaleRate;
                        if($rate->Purity == 99.9 && $rate->itemType == "Gold")
                        $insertData['goldrate_24ct'] = $rate->SaleRate;*/
                        
                        /*if($rate->Purity == 18 && $rate->itemType == "Gold")
                        $insertData['goldrate_18ct'] = $rate->SaleRate;*/
                        
                        if($rate->Purity == 22 && $rate->itemType == "Gold")
                        $insertData['goldrate_22ct'] = $rate->SaleRate;
                        if($rate->itemType == "Silver")
                        $insertData['silverrate_1gm'] = $rate->SaleRate;
                        
                        /*if($rate->Purity == 24 && $rate->itemType == "Gold")
                        $insertData['goldrate_24ct'] = $rate->SaleRate;
                        if($rate->itemType == "Platinum")
                        $insertData['platinum_1g'] = $rate->SaleRate;*/
                    }
                    $insertData['add_date'] 		= date("Y-m-d H:i:s");
                    $insertData['id_employee'] 		= 0;
                    //echo "<pre>";print_r($insertData);
                    if($insertData['goldrate_22ct'] > 0){
                        
                        $lastRate = json_decode($memcache->get('lastMetalRates-'.$id_branch));
                        $lastUpdDate = date('Y-m-d',strtotime($lastRate->updatetime));
                        echo "Cache lastMetalRates-".$id_branch." <pre>";print_r($lastRate);
                        echo "SIZE : ".sizeof($lastRate)."\n";
                        echo "Rate : ".$lastRate->goldrate_22ct ." == ". $insertData['goldrate_22ct']."\n";
                        echo "lastUpdDate : ".$lastUpdDate."\n";
                        if($lastRate->goldrate_22ct != $insertData['goldrate_22ct'] || sizeof($lastRate) == 0 || $lastUpdDate != date('Y-m-d')){
                            $rateTxt_data = array(
                                                'goldrate_22ct'     => number_format($insertData['goldrate_22ct'],'2','.',''),
                                                'silverrate_1kg'	=> $insertData['silverrate_1kg'],
                                                'silverrate_1gm'	=> $insertData['silverrate_1gm'],
                                                'mjdmagoldrate_22ct'=> number_format($insertData['mjdmagoldrate_22ct'],'2','.',''),
                                                'mjdmasilverrate_1gm'=>$insertData['mjdmasilverrate_1gm'], 
                                                'market_gold_18ct'  => $insertData['market_gold_18ct'], 
                                                'goldrate_18ct'     => $insertData['goldrate_18ct'], 
                                                'updatetime'	    => $metal['updatetime']
                                            );
                            file_put_contents('../api/rate.txt',json_encode($rateTxt_data));
                            $this->db->trans_begin();				 	
                            //inserting rates in DB 
                            $this->load->model("admin_settings_model");
                            $status = $this->admin_settings_model->metal_ratesDB("insert","",$insertData);
                            $branch_info    =   array(								
                									'id_metalrate'		=> ($status['insertID']),								
                									'id_branch'			=> $id_branch,
                									'status'			=> 1,								
                									'date_add'			=> date("Y-m-d H:i:s")															
            									);
							//Before update set previous status to 0
							$this->admin_settings_model->update_metalrate_status(array('status'=> 0),$id_branch);
							$this->admin_settings_model->insert_metalrate($branch_info,'branch_rate'); 
                            if($this->db->trans_status() == TRUE){
                                echo 'lastMetalRates-'.$id_branch;
                                $memcache->set('lastMetalRates-'.$id_branch, json_encode($insertData), 0, 0);
                                $this->admin_settings_model->settingsDB("update",1,array('is_ratenoti_sent'=>2));
                                $this->db->trans_commit();	
                                echo "Rate added Successfully";
                            }
                        }else{
                            echo "No change in rate.. ".$lastRate->goldrate_22ct ." == ". $insertData['goldrate_22ct'];
                        }
                    }else{
                        echo "Gold 22K missing";
                    }
               	}else{
               	    echo "<pre>";print_r($resData);
               	}
                $logData = "\n".date('d-m-Y H:i:s')."\n POST : ".json_encode($postData,true)."\n Error : ".json_encode($response,true);
    		    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
            }else{
                echo $this->db->last_query();
                echo $this->db->_error_message();
                $subject = "Khimji - Rate API Error";
                $message = json_encode($response,true)." POST DATA : ".json_encode($postData,true);
                $this->sendEmail($subject,$message);
            }
        }else{
            echo "Time :".date("h:i a");
        }
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
	
	function generateTranUniqueIdManually($id_payment){
	    $sql = $this->db->query("SELECT c.reference_no, nominee_name, nominee_relationship,nominee_address1,nominee_address2,nominee_mobile,
	                                    sync_scheme_code,sa.scheme_acc_number,e.firstname as emp_name,sa.referal_code,
	                                    p.payment_amount
	                              FROM payment p  
	                                LEFT JOIN scheme_account sa on sa.id_scheme_account = p.id_scheme_account
	                                LEFT JOIN customer c on sa.id_customer = c.id_customer
	                                LEFT JOIN scheme s on s.id_scheme = sa.id_scheme
	                                LEFT JOIN employee e on sa.referal_code = e.emp_code
	                              WHERE receipt_no is null and p.offline_tran_uniqueid is null and p.id_payment =".$id_payment." group by id_payment");
    	$chit = $sql->row_array();
    // 	echo $this->db->last_query();
    // 	echo "<pre>";print_r($chit);exit;
    	if($sql->num_rows() == 1){
        	$postData = array(
                        	"isKycValidationCheck" => false,
                        	"customerCode"  => $chit['reference_no'],
                        	"transactionType"=> 1,
                        	"schemeCode"    => $chit['sync_scheme_code'],
                        	"amount"        => $chit['payment_amount'],
                        	"date"          => date("Y-m-d H:i:s"),
                        	"narration"     => "Requested from Web app"
                        );
            $is_new_ac = ($chit['scheme_acc_number'] != "" && $chit['scheme_acc_number'] != NULL ? FALSE : TRUE);            
            if($is_new_ac){ // 1st Installment 
            	$postData["action"] = 1;
            	$postData["narration"]   =  $chit['nominee_address1'].",".$chit['nominee_address2'];
                $postData["narration2"]  =  $chit['nominee_relationship'];//"Nominee Relation"
                $postData["narration3"]  =  $chit['nominee_name']; //"Nominee Name"
                $postData["narration4"]  =  "";//Nominee DOB"
                $postData["narration5"]  =  $chit['nominee_mobile']; //"Nominee MobNo"
                $postData["salesmanName"]=  $chit['emp_name'];
                $postData["employeeId"]  =  $chit['referal_code'];
                $postData["nominee"] = array(
                                    		"nomineeName" => $chit['nominee_name'],
                                    		"relation" => $chit['nominee_relationship'],
                                    		"address1" => $chit['nominee_address1'],
                                    		"address2" => $chit['nominee_address2']
                                    	   );
    	    }else{
    	    	$postData["action"] = 2;
    	        $postData["orderNo"] = $chit['scheme_acc_number'];
    	    }
           $response = $this->integration_model->khimji_curl('app/v1/saveSchemeOrInstallmentDetails',$postData);
           //echo "<pre>";print_r($postData);
           //echo "<pre>";print_r($response);exit;
           if($response['status'] == TRUE){
           	$resData = $response['data']->data;
           	if($resData->status == TRUE && $resData->errorCode == 0){
                $pData = array(
                    		 'offline_tran_uniqueid'=> $resData->result->tranUniqueId,
                    		 'date_upd'			    => date("Y-m-d H:i:s")
                		 );
                $this->integration_model->updateData($pData,'id_payment',$id_payment,'payment');
                //return $resData->result->tranUniqueId;
                $id = "Trans Unique Id".$resData->result->tranUniqueId;
            }
            else if($resData->errorCode == 1001){
				$payData = array(
							 'offline_error_msg'	=> date("Y-m-d H:i:s")." ID Gen Error : ".$resData->errorMsg,
							 );
				$this->integration_model->updateData($payData,'id_payment',$id_payment,'payment');
				$id = "ID Gen Error :".$resData->errorMsg;
            }
            
            
       }else{
           $id = "Unable to Proceed your request";
       }
           	//echo "Error : ";
    		
    		
    	}
    	
    	   echo "Response : ";
    	    echo "<pre>".$id_payment;
    	    echo "<pre>".$id;
    	
    	
    }
}