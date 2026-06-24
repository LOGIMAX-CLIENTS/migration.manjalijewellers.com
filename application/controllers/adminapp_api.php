<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* CRM Admin app api's
*/
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
require(APPPATH.'libraries/REST_Controller.php');
require_once(APPPATH.'libraries/payu.php');
header('Content-Type: application/json');     //#sathy

class Adminapp_api extends REST_Controller
{
	const ADM_MODEL = "adminappapi_model"; 
	const MOD_MOB = "mobileapi_model";
	const SERV_MODEL = 'services_modal';
	const PAY_PATH  = 'assets/payment/';
	const VOUCHER_IMG_PATH = 'admin/assets/img/voucher/';
	const HORIZONTAL_LINE = array(
    'HR_58MM' =>  '================================================',
    'HR2_58MM' => '************************************************',
    'HR3_58MM' => '------------------------------------------------',
    );
	function __construct()
	{
		parent::__construct();
		
		$this->response->format = 'json';
		$this->load->model(self::ADM_MODEL);
		$this->load->model('services_modal');
		$this->load->model('email_model');
		$this->load->model('adminappapi_model');
		$this->load->model('mobileapi_model');
		$this->load->model('payment_modal');
		$this->load->model('scheme_modal');
		$this->load->model('integration_model');
		$this->comp = $this->mobileapi_model->company_details();
		$this->load->model('sms_model');
		ini_set('date.timezone', 'Asia/Calcutta'); 
		$this->payment_status = array(
										'pending'   => 7,
										'awaiting'  => 2,
										'success'   => 1,
										'failure'   => 3,
										'cancel'    => 4
									  );
		
		$this->current_android_version= "1.0.0"; 
		$this->new_android_version = "1.0.1"; 
	}
	
	/**
	* General functions   
	*/	
	function IsNullOrEmptyString($str){
        return (!isset($str) || trim($str) === '');
    }
    //funtion to get post values
    function get_values()
    {
		return (array)json_decode(file_get_contents('php://input'));
		
	}
    
	//array sorting
	function array_sort($array, $on, $order=SORT_ASC){
		$new_array = array();
		$sortable_array = array();
		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}
			switch ($order) {
				case SORT_ASC:
					asort($sortable_array);
					break;
				case SORT_DESC:
					arsort($sortable_array);
					break;
			}
			foreach ($sortable_array as $k => $v) {
				//$new_array[$k] = $array[$k];
				$new_array[] = $array[$k];
			}
		}
		return $new_array;
	}
	
	public function __encrypt($str)
	{
		return base64_encode($str);	
	}
		
	/**
	* CRM Admin app api's
	*/
	
	function getVersion_get()
	{ 
	   	$version['android'] = $this->current_android_version;
	   	$version['new_android_ver'] = $this->new_android_version; 
		$version['comp'] = $this->adminappapi_model->company_details();
		$version['mode'] = $version['comp']['maintenance_mode'];
		$version['text'] = $version['comp']['maintenance_text']; //maintaince text
		$version['msg'] =  "New version available at store."; //New version text   
		$version['showpopup'] = 0; 
		$version['popupimg'] = ""; 
		
		//#sathy
		$version['title'] = "Upgrade NewVersion";  
        $version['playPackage'] = "";
        //#sathy
        
		$this->response($version,200);
	}
	
	function currency_get()

	{

		$model = self::MOD_MOB;
		
		$id_branch = $this->get('id_branch');

		$currency = $this->$model->get_currency($id_branch);

		$this->response($currency, 200);	

	}	
	
	function authenticate_post()
	{
		$model = self::ADM_MODEL;
		$data = $this->get_values(); 
		$res = $this->$model->isValidLogin($data);

		$result = array(
						   'username'   => $data['username'],
						   'is_valid' => (sizeof($res)>0 ? TRUE : FALSE)
						);
	
					
		 if($result['is_valid'] === TRUE && $res['active'] == 1 &&  $res['device_uuid_status'] == TRUE &&  $res['enable_chit'] == TRUE)
		 {
		 	$result['employee']        = $res;
		 	
		 }  else if($res['login_type'] == 'AGENT' && $result['is_valid'] === TRUE){
		   $result['employee']        = $res;
		} else if($result['is_valid'] === FALSE){
		     
		     $result['message'] = 'Invalid Username or Password...';
		     
		 }else if($result['is_valid'] === TRUE && $res['active'] == 0){
		    $result['is_valid'] = FALSE;
		    $result['message'] = 'Your account is currently inactive...Kindly contact administrator..';
		      
		 }	else if($result['is_valid'] === TRUE && $res['enable_chit'] == FALSE){
		    $result['is_valid'] = FALSE;
		    $result['message'] = 'Your account is disabled for chit collection....';
		      
		 }	else if($result['is_valid'] === TRUE && $res['device_uuid_status'] == FALSE ){
		    $result['is_valid'] = FALSE;
		    $result['message'] = 'Invalid device ID....';
		      
		 }
		 
		 
		$id_branch   = (isset($result['employee']['id_branch']) ? $result['employee']['id_branch'] :0);
		$currency = $this->$model->get_currency($id_branch);
		$result['currency'] = $currency;
		$result['username'] = $data['username'];
		
		
		$this->response($result, 200);
	}

	//Register customer	
	function createCustomer_post()
	{
		$model = self::ADM_MODEL;		
		$data = $this->get_values();
		
		    $p_ImgData  = ($data['fileName']);
	        $id = 	mt_rand(100000, 999999);
            $image_base64 =base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $p_ImgData));
            $file = 'assets/img/customer_profile/pro_'.$id.'.png';
            file_put_contents($file, $image_base64);
            //exit; 
            
        $isBranchReq = $this->mobileapi_model->isBranchWiseReg();   // Branch is required For reg if- branch settings enabled //
        if($isBranchReq){
            if($this->IsNullOrEmptyString($data['id_branch'])){
                $result = array( "status" =>FALSE, "msg" => "Branch is required.");
                $this->response($result,200);
            }
        }
		
		$no_deviceData = FALSE;
		$result ='';
		$is_already_reg = $this->isNumberRegistered($data); 
		if($is_already_reg['is_reg']){ // already regiatered
			$res = array("msg"=>$is_already_reg['msg'],"status"=>FALSE);
			$this->response($res,200);
		}
		
		$customer = array(
						'info'=>array(
							'firstname' => ucwords($data['firstname']),
							'lastname'  => ucfirst($data['lastname']),
							'mobile'    => $data['mobile'],
							'email'     => $data['email'],
							'passwd'    => $this->__encrypt($data['mobile']),
							'active'    => 1,
						    'date_add'  => date('Y-m-d H:i:s'),
						    
						    //#sathy	
						    'added_by'  => 3,	// collection app 				  	
							'id_employee' => (isset($data['id_employee']) && $data['login_type'] == 'EMP' ?$data['id_employee']:NULL),
                            'id_agent' => (isset($data['id_employee']) && $data['login_type'] == 'AGENT' ?$data['id_employee']:NULL),
							//#sathy
							
							'id_village' => (isset($data['id_village'])?$data['id_village']:NULL),
							'id_branch' => (isset($data['id_branch'])?$data['id_branch']:NULL),
							'date_of_birth' =>(isset($data['date_of_birth'])?$data['date_of_birth']:NULL),
							'pan' =>(isset($data['pannumber'])?$data['pannumber']:NULL),
							'driving_license_no' =>(isset($data['dlcno'])?$data['dlcno']:NULL),
							'aadharid' =>(isset($data['adharno'])?$data['adharno']:NULL),
							'cus_type' =>(isset($data['custype'])?$data['custype']:1),
							'gender' =>(isset($data['gender'])?$data['gender']:null),
							
							),
							'address'=>array(
								'address1'			=>	(isset($data['address1'])?$data['address1']:NULL),
								'address2'			=>	(isset($data['address2'])?$data['address2']:NULL),
								'id_country'		=>	(isset($data['id_country'])?$data['id_country']:NULL),
								'id_state'			=>	(isset($data['id_state'])?$data['id_state']:NULL),
								'id_city'			=>	(isset($data['id_city'])?$data['id_city']:NULL),
								'pincode'			=>	(isset($data['pincode'])?$data['pincode']:NULL)
							)
						 ); 
						 
						 
		$this->db->trans_begin();
		$status = $this->$model->insert_customer($customer);
		//print_r($this->db->last_query());exit;
		if($this->db->trans_status()==TRUE)
		{
		    
		    
		 //#sathy   
		     //base64 code images aadhar,pan,license..... starts #AB updated on 2-6-2023
		    
	        $aadhar = explode(";base64,", $data['adharnofileName']);
			$aadharimagebase64  = base64_decode($aadhar[1]);
            $img_path = 'admin/assets/img/customer/'.$status['insertID'].'/';
            if (!is_dir($img_path)) {
		        mkdir($img_path, 0777, TRUE); 
	        }
	       
	       //print_r($img_path.'aadhar_'. $status['insertID'] . '.png');exit;
			if(file_put_contents($img_path.'aadhar_'. $status['insertID'] . '.png', $aadharimagebase64)){
			    $updCus = array('aadhar_ImgName' => 'aadhar_'. $status['insertID'] . '.png');
			    $this->$model->updData($updCus,'id_customer',$status['insertID'],'customer');
			}

		    $pan = explode(";base64,", $data['panfileName']);
			$panimagebase64  = base64_decode($pan[1]);
            $img_path = 'admin/assets/img/customer/'.$status['insertID'].'/';
            if (!is_dir($img_path)) {
		        mkdir($img_path, 0777, TRUE); 
	        }
			if(file_put_contents($img_path.'pan_'. $status['insertID'] . '.png', $panimagebase64)){
			    $updCus = array('pan_ImgName' => 'pan_'. $status['insertID'].'.png');
			    $this->$model->updData($updCus,'id_customer',$status['insertID'],'customer');
			}
			
			
			$dl = explode(";base64,", $data['dlfileName']);
			$dlimagebase64  = base64_decode($dl[1]);
            $img_path = 'admin/assets/img/customer/'.$status['insertID'].'/';
            if (!is_dir($img_path)) {
		        mkdir($img_path, 0777, TRUE); 
	        }
			if(file_put_contents($img_path.'dl_'. $status['insertID'] . '.png', $dlimagebase64)){
			    $updCus = array('dl_ImgName' => 'dl_'. $status['insertID'].'.png');
			    $this->$model->updData($updCus,'id_customer',$status['insertID'],'customer');
			}
			
             //base64 code ends...
             
        //#sathy     
		    
		    //Sync Existing Data 	
			if($this->config->item("integrationType") == 5){ // Do customer registration in offline
               $this->load->model("integration_model");
               $postData = array(
        						"appCustomerCode"	=> 0 ,
        						"custName" 			=> ucwords($data['firstname']),
        						"mobileNo" 			=> trim($data['mobile']),
        						"emailId" 			=> $data['email'],
        						"preferdBranch" 	=> "",
        						"branchCode" 		=> "",
        						"custdetails" 		=> array(
        						                        'address1'	=>	(isset($data['address1'])?$data['address1']:NULL),
								                        'address2'	=>	(isset($data['address2'])?$data['address2']:NULL),
        						                        "city"      => isset($data['cityname'])?$data['cityname']:"Coimbatore" , 
        						                        "state"     => isset($data['statename'])?$data['statename']:"Tamil Nadu",
        						                        "adharNo"   => NULL
        						                        )
        					);
               $response = $this->integration_model->khimji_curl('registerCustomerWithoutValidateOtp',$postData);
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
                    }else{
                        if($resData->errorCode == 1001){
        					$updCus = array(
    									 'offline_error_msg'	=> date("Y-m-d H:i:s")." Register : ".$resData->errorMsg,
    									 );
    						$this->integration_model->updateData($updCus,'mobile',$data['mobile'],'customer');
                        }
    					if (!file_exists('log/khimji')) {
    		                mkdir('log/khimji', 0777, true);
    		            }
    		            $log_path = 'log/khimji/'.date("Y-m-d").'.txt';  
    		            $logData = "\n".date('d-m-Y H:i:s')."\n API : registerCustomerWithoutValidateOtp \n PostData : ".json_encode($postData,true)." \n Response : ".json_encode($resData,true);
    				    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
    				}
               }else{
                   $log_path = 'log/khimji/'.date("Y-m-d").'.txt';  
		            $logData = "\n".date('d-m-Y H:i:s')."\n API : registerCustomerWithoutValidateOtp \n PostData : ".json_encode($postData,true)." \n Response : ".json_encode($response,true);
				    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
               }
            }
		   
		   
		
			$wallet_acc =  $this->$model->wallet_accno_generator(); 				   
			if($wallet_acc['wallet_account_type']==1){
				$this->wallet_account_create($status['insertID'],$data['mobile']);
				}
			$id = $status['insertID']; 
		
			if($this->db->trans_status() == TRUE ){
				$this->db->trans_commit();
					$serviceID = 1;
					  $company = $this->$model->company_details();
					  $service = $this->services_modal->checkService($serviceID);
						if($service['sms'] == 1)
						{
							
							$data =$this->services_modal->get_SMS_data($serviceID,$id);
							$mobile =$data['mobile'];
							$message = $data['message'];
							$dlt_te_id = $service['dlt_te_id'];
                            $this->$model->send_sms($mobile,$message,'',$dlt_te_id);
							
						}
						
						if($service['email'] == 1 && $customer['email'] != '')
						{
							$to =$customer['email'];
							$data['name'] = $customer['firstname'];
							$data['mobile'] = $customer['mobile'];
							$data['passwd'] = $this->__decrypt($customer['passwd']);
							$data['company_details']=$company;
							$data['type'] = 3;
							$subject = "Reg: ".$this->comp['company_name']." saving scheme registration";
							$message = $this->load->view('include/emailAccount',$data,true);
							$sendEmail = $this->email_model->send_email($to,$subject,$message);
						}
				$result = array('timestamp' => $data['request_customer_id'],'id_customer' => $id,'status'=> TRUE, 'msg'=>'Your number '.$data['mobile'].' registered successfully.'); 				
			}
			else
			{
				$this->db->trans_rollback();
				$result = array( "status" =>FALSE, "msg" => "Unable to proceed your request,please try again..");			
			}
			
		}
		else
		{
			$result = array( "status" =>FALSE, "msg" => "Unable to proceed your request");			
		}	
		
		$this->response($result,200);		 
	}
	
	//join scheme

	function createAccount_post()
	{
		$model = "mobileapi_model";
		
		$admin_model = self::ADM_MODEL;
		
		$this->load->model($model);
		$data = $this->get_values();		
    	$flag = FALSE;
		$scheme_acc_number  ='';
		$result ='';
		if($data['referal_code'] != '')
		{
		$is_referral_by = 1;
		}else{
		    $is_referral_by = NULL;
		}
        /*$cus = $this->$model->get_customerByID($data['id_customer']); 
        $settings = $this->$model->is_branchwise_cus_reg(); 
		
			if($settings['branch_settings']==1)
			{	
				if($settings['is_branchwise_cus_reg']==1)
				{
					$id_branch  = $cus['id_branch'];

				}
				else if($settings['branchWiseLogin']==1)
				{
					$id_branch=$this->config->item('pay_branchId');
				}
				else
				{
					$id_branch  = (isset($data['id_branch'])?$data['id_branch']:NULL);
				}
				
			}
		else{
			$id_branch =NULL;
		}*/
        $id_branch  = (isset($data['id_branch'])?$data['id_branch']:NULL);
        $start_year = $this->payment_modal->get_financialYear();
         $custom_entry_date = $this->payment_modal->get_entrydate($id_branch);
		$schAcc = array(
						 'id_customer'       => $data['id_customer'],
						 'id_scheme'         => $data['id_scheme'],
						 'start_date'        => date('Y-m-d H:i:s'),
						 //'group_code'        => $data['group_code'],
						 'firstPayment_amt'        => (isset($data['payable'])?$data['payable']:NULL),     
						 'scheme_acc_number' =>	($data['is_new'] == 'N' ? $data['scheme_acc_number'] : NULL),
						 'account_name'      => ucwords($data['account_name']),
						 'is_new'		     => $data['is_new'],
						 'active'            => 1,
						 'date_add'          => date('Y-m-d H:i:s'),
						 'added_by' 		 => 3,
						 'id_branch'         => $id_branch,
						 "is_refferal_by" 	 => $is_referral_by,	
						 'referal_code' 	 => (isset($data['referal_code'])?$data['referal_code']:NULL),
						 'pan_no'		     => ($data['pan_no'] != null ? strtoupper($data['pan_no']): NULL),
						 'start_year'   => $start_year,
						 //'agent_code'   => (isset($data['agentcode'])?$data['agentcode']:NULL),
						// 'id_agent'   => (isset($data['id_agent'])?$data['id_agent']:NULL),
						'custom_entry_date'   =>  ($custom_entry_date['edit_custom_entry_date']==1 ? $custom_entry_date['custom_entry_date']:NULL),
						);	
						
		if(isset($data['is_new']) &&  $data['is_new'] =='N')
		{  
			if($data['regExistingReqOtp'] == 0){
				$schAcc['id_scheme_group'] = (!empty($data['id_scheme_group'])?$data['id_scheme_group']:'');
				$isAccNoExist = $this->scheme_modal->verify_existing($schAcc);
				if(!$isAccNoExist)
				{
					$scheme_acc  = array("id_scheme" => ($data['id_scheme']!=''?$data['id_scheme']:0),
					                	"id_customer" =>  $data['id_customer'],
					                	"scheme_acc_number" => ($data['scheme_acc_number']!=''? $data['scheme_acc_number']:NULL),
					                	"ac_name" => ($data['account_name']!=''?$data['account_name']:NULL),
										 "id_branch" => $id_branch,
					                	"id_employee" => NULL,
					                	'added_by' 		 => 2,
					                	"date_add" => date('Y-m-d H:i:s'),
					                	"id_scheme_group" => (isset($data['id_scheme_group'])?$data['id_scheme_group']:NULL),
					                	'pan_no'		     => ($data['pan_no'] != null ? strtoupper($data['pan_no']): NULL),	
					                	"status" => 0 // processing
					             	);
				  $status =	$this->scheme_modal->join_existing($scheme_acc);  
				  if($status['status'])
				  {
				  	$result = array( "status" =>TRUE, "msg" => 'Kindly wait for your scheme activation, once activated you will be notified');
				  }
				  else
				  {
				  	$result = array( "status" =>FALSE, "msg" => 'Unable to proceed your request..');			
				  }			
				}
				else
				{
					if($isAccNoExist['table'] == 'scheme_account'){
						$msg = 'Your account already exist, Please contact customer care to proceed your request';
					}else{
						$msg = 'You have already sent request for this account... Please wait until we verify your account. Check dashboard for status.';
					}
					$result = array( "status" =>FALSE, "msg" => $msg);			
				}
			}else{
				$result = $this->join_existing_byacc($schAcc);
			}					
			$this->response($result,200);	
					
		}else{ // New account joining
            $this->db->trans_begin();
			
           // if(isset($data['referal_code']))
           if(strlen($data['referal_code']) > 0 && isset($data['referal_code']) && $data['login_type'] == 'EMP')     
			{ 		
			
				if( $data['referal_code']!= $data['mobile'])
				{
					$referral = $this->scheme_modal->checkreferral_code($data['referal_code'],$data['id_customer']);
					
					if($referral['status'] == false)
					{												
						$this->db->trans_rollback();
						$result =array('status'=>FALSE,'msg'=> $referral['msg']);
						$this->response($result,200); 
					}
					else
					{
						$is_referral_by = (strtoupper($referral['is_referral_by']) == 'CUS' ? 0 :(strtoupper($referral['is_referral_by']) == 'EMP'?1:NULL));
					}	
				}
				else
				{
					$result =array('status'=>FALSE,'msg'=>'Invalid Referral code');
						$this->response($result,200); 
				}
			   
			}
			
			if(strlen($data['agentcode']) > 0 && isset($data['agentcode']) && $data['agentcode'] != '')
			{
			    $agent_code = $this->$model->verifyAgentCode($data['agentcode']);
                        if($agent_code['status'] == 1)
                        {
                            $schAcc['id_agent'] = $agent_code['agent']['id_agent'];
                            $schAcc['agent_code'] = $agent_code['agent']['agent_code'];
                        }
                        else
        				{
        					$result =array('status'=>FALSE,'msg'=>'Invalid Agent code');
        						$this->response($result,200); 
        				}
			}
			
            $schAcc['is_refferal_by'] = $is_referral_by;
            
            if(isset($data['referal_code']) && $data['referal_code'] != '')
            {
                $empDetails=$this->$admin_model->get_employee_details($data['referal_code']);
                $schAcc['id_employee']=$empDetails['id_employee'];
            }
            $status = $this->$model->insert_schemeAcc($schAcc);
            //echo $this->db->last_query();exit;
            $flash_msg = '';
            
            
             //base64 code 

			$idfront  = ($data['idfront']);
	     
            $image_base64 =base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $idfront));
		    
            $pan_image_base64 =base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['panfileName']));
            $file = 'assets/img/account_proof/customer_pan/front/idfront_'. $status['insertID'] . '.png';
            file_put_contents($file, $image_base64);
            
            $idback  = ($data['idback']);
	     
            $image_base64 =base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $idback));
		    
            $pan_image_base64 =base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['panfileName']));
            $file = 'assets/img/account_proof/customer_pan/back/idback_'. $status['insertID'] . '.png';
            file_put_contents($file, $image_base64);
            
            //insert gift if available
		
		    if($status['insertID'] && $data['id_gift']){
		        $giftsql = $this->db->query("SELECT * from gifts where id_gift =".$data['id_gift']."")->row_array();
		        $giftData = array('type' => 1,
		                          'status' => 1,
		                          'gift_desc' =>$giftsql['gift_name'],
		                          'id_scheme_account' =>$status['insertID'], 
		                          'id_employee' => $schAcc['id_employee']);
		       $insertgift = $this->$model->insert_gift($giftData);
		       
		        //print_r($this->db->last_query());exit;
		    }
            
            if($status['sch_data']['free_payment'] == 1)
            {
                $pay_insert_data = $this->mobileapi_model->free_payment_data($status['sch_data'],$status['insertID']);
                if($status['sch_data']['receipt_no_set'] == 1){
                    $pay_insert_data['receipt_no'] = $this->generate_receipt_no($data['id_scheme'],$id_branch);
                }
                
                $pay_add_status = $this->payment_modal->addPayment($pay_insert_data);
                $flash_msg = 'As Free Installment offer, 1st installment of your scheme credited successfully. Kindly pay your 2nd installment';  
                
                $scheme_acc_no=$this->$model->accno_generatorset();
                if($scheme_acc_no['status']==1 && $scheme_acc_no['schemeacc_no_set']==0)
                {
                      $scheme_acc_number=$this->payment_modal->account_number_generator($schAcc['id_scheme'],$id_branch,'');
                    if($scheme_acc_number!=NULL)
                    {
                        $updateData['scheme_acc_number']=$scheme_acc_number;
                    }
                    $updSchAc = $this->$model->update_account($updateData,$status['insertID']);
                }
            }
            
            
            /*if scheme have Voucher store voucher data in gift card table- Haritha */
            if($status['insertID'] > 0 && isset($data['voucher_no']) && isset($data['voucher_value']))
            {
                if(($data['voucher_no'] != NULL || $data['voucher_no'] != '') && ($data['voucher_value'] != NULL || $data['voucher_value'] != ''))
                {
                    $voucher_data = array(
                                 'free_card' => 4,
                                 'id_branch' => $id_branch,
                                 'code' => $data['voucher_no'],
                                 'amount' => $data['voucher_value'],
                                 'id_scheme_account' => $status['insertID'],
                                 'date_add' => date('Y-m-d H:i:s'),
                                 'id_pay_gift_card' => 0,
                                 'purchased_by'  =>0
                                );
                    if(isset($data['voucher_img']))
                    {
                        
                        //upload voucher img
                        $parts        = explode(";base64,", $data['voucher_img']);
        				$addressimagebase64  = base64_decode($parts[1]);
        				$sch_acc_id = $status['insertID'];
                        $img_path = self::VOUCHER_IMG_PATH."/".$sch_acc_id;
                        if (!is_dir($img_path)) {
            		        mkdir($img_path, 0777, TRUE); 
            	        }
        				$path = self::VOUCHER_IMG_PATH.$sch_acc_id."/voucher.png";
        				$img_url = base_url().self::VOUCHER_IMG_PATH.$sch_acc_id."/voucher.png";
        				$voucher_data['img_url'] = $img_url;
        				$filename = "voucher.jpg";	
        				$file         = $path;
        				file_put_contents($file, $addressimagebase64);
                    }
                                
                    $this->$model->insert_voucher($voucher_data);
                }
            }
            
            /*ends*/
            
            if($this->db->trans_status()===TRUE)
            {
                $this->db->trans_commit();
                $schData1 = $this->$model->get_schemeaccount_detail($status['insertID']);
                $this->load->model("scheme_modal");
                $schData = $this->scheme_modal->getJoinedScheme($status['insertID']);
                
                $serviceID = 2;
                $service = $this->services_modal->checkService($serviceID);
                
                $company = $this->$model->company_details();	
                
                if($service['sms'] == 1)
                {
                    $id=$status['insertID'];
                    $data =$this->services_modal->get_SMS_data($serviceID,$id);
                    $mobile =$data['mobile'];
                    $message = $data['message'];
                    $dlt_te_id = $service['dlt_te_id'];
                    $this->$model->send_sms($mobile,$message,'',$dlt_te_id);
                    
                }
                
                if($service['email'] == 1 && isset($schData[0]['email']) && $schData[0]['email'] != '')
                {
                    $data['schData'] = $schData[0];
                    $data['company'] =$company;
                    $data['type'] = 1;
                    $to = $schData[0]['email'];
                    $subject = "Reg.  ".$this->comp['company_name']." scheme joining";
                    $message = $this->load->view('include/emailScheme',$data,true);
                    $sendEmail = $this->email_model->send_email($to,$subject,$message,'','','');
                }
                
                $result = array('status'=> TRUE, 'msg'=>'Purchase plan added to your account.','chit' =>$schData1[0],'free_pay' =>$flash_msg); 
            }
            else{
                $this->db->trans_rollback();
                $result = array( "status" =>FALSE, "msg" => "Unable to proceed your request");	
            }	
        }


		$this->response($result,200);		 				
	}
	
	function wallet_account_create($cus_id,$mobile)
	{
	
	$this->load->model('services_modal');
	$this->load->model('email_model');
	$model = self::ADM_MODEL;
	$wallet_acc_no =  $this->services_modal->get_wallet_acc_number();				
	
	$insertData=array( 
				   'id_customer' 	   => (isset($cus_id) && $cus_id!=''? $cus_id:NULL),
				   'id_employee' 	   =>  2,
				   'wallet_acc_number' => (isset($wallet_acc_no)?$wallet_acc_no:NULL),
				   'issued_date' 	   => date('y-m-d H:i:s'),
				   'remark' 		    => "Credits",
				   'active'		        => 1	                        
				   );
			   
			   
		   //inserting data                  
		   $status = $this->services_modal->walletacc_insert($insertData);
		   $wallAcc = $this->services_modal->get_walletacc($status['insertID']);
	//	   $this->$model->insChitwallet($status['insertID'],$mobile,$cus_id);
		   		  
			 if($status)
			{
				 $serviceID = 8;
				 $service =  $this->services_modal->checkService($serviceID);			
					if($service['sms'] == 1)
					{
						$id =$status['insertID'];
						$data =$this->services_modal->get_SMS_data($serviceID,$status['insertID']);
						$mobile =$data['mobile'];
						$message = $data['message'];
						$dlt_te_id = $service['dlt_te_id'];
                        $this->$model->send_sms($mobile,$message,'',$dlt_te_id);
					}
			}
			
			
	
	}
	
	
	//to get customer data
/*	function customerSchemes_post()
	{ 
		$data = $this->get_values(); 
		$model = self::ADM_MODEL; 
		$result['cusSchemes'] = array();
		$result['isValid'] = FALSE;
		$res = $this->$model->get_customerByMobile($data['cusmobile'],$data['emp_branch'],$data['branch_settings']); 
		$result['customer'] = $res; 
		if(sizeof($res)>0){
			$result['isValid'] = TRUE;
			$schemeAcc = $this->$model->get_payment_details($res['id_customer'],$res['id_branch']); 
			$result['cusSchemes'] = $this->array_sort($schemeAcc['chits'], 'allow_pay',SORT_DESC);	
		}
		$this->response($result,200);
	} */
	
	
	
	function customerSchemes_post()
	{ 
		$data = $this->get_values(); 
		
		$model = self::ADM_MODEL; 
		$result['cusSchemes'] = array();
		$result['isValid'] = FALSE;
		
		//Sync Existing Data 	
		if($this->config->item("integrationType") == 5)
		{
		    //$customer = $this->$model->getCustomerByMobile($data['cusmobile']);
		    //$this->getDataFromOffline($customer['id_customer'],$customer['reference_no']);
		}
		$searchbyaccno = $this->config->item('searchbyaccno');
		
	/*	if($searchbyaccno == 1){
		    if($data['schemecode'] != '' && $data['year'] != '' && $data['schemeaccno'] != ''){
    		    $cusdata = $this->$model->get_customer_byAcc($data['schemecode'],$data['year'],$data['schemeaccno']); 
    		    $cus_mobile = $cusdata['mobile'];
    		    $cus_id = $cusdata['id_customer'];
		    } 
		}else if($searchbyaccno == 2){
		    $cus_mobile  = $data['cusmobile']; 
		    $id_sch_acc = '';
		}else if($searchbyaccno == 3){
		    if($data['schemecode'] != '' && $data['year'] != '' && $data['schemeaccno'] != ''){
    		    $cusdata = $this->$model->get_customer_byAcc($data['schemecode'],$data['year'],$data['schemeaccno']); 
    		    $cus_mobile = $cusdata['mobile'];
    		    $cus_id = $cusdata['id_customer'];
    		    $id_sch_acc = $cusdata['id_scheme_account'];
		    }else{
		        $cus_mobile  = $data['cusmobile']; 
		        $id_sch_acc =  '';
		    }
		}   */
		
		
		if($data['schemecode'] != '' && $data['year'] != '' && $data['schemeaccno'] != '' && ($searchbyaccno == 1 || $searchbyaccno == 3)){
		    $cusdata = $this->$model->get_customer_byAcc($data['schemecode'],$data['year'],$data['schemeaccno']); 
		   // print_r($cusdata);exit;
    		    $cus_mobile = $cusdata['mobile'];
    		    $cus_id = $cusdata['id_customer'];
    		    $id_sch_acc = $cusdata['id_scheme_account'];
		}else{
		        $cus_mobile  = $data['cusmobile'];
		        $cusdata = $this->payment_modal->get_customer($data['cusmobile']);
		        $id_sch_acc =  '';
		}   
		
		
			if($cus_mobile != ''){
		    $cus = $this->$model->get_customerByMobile($cus_mobile,$data['emp_branch'],$data['branch_settings']); 
		   
		    /* $result['customer'] = $res;
		    	if(sizeof($res)>0){
        			$result['isValid'] = TRUE;
        			$schemeAcc = $this->mobileapi_model->get_payment_details($res['id_customer'],$res['id_branch'],$id_sch_acc); 
        			 //print_r($schemeAcc);exit;
        			$result['cusSchemes'] = $this->array_sort($schemeAcc['chits'], 'allow_pay',SORT_DESC);	
        			$result['metal'] = $this->$model->get_metalData();
		        } */

			$result['customer'] = $cus;
		if(sizeof($cus)>0){
		
			// Check if employee allocation is enabled in settings
			$emp_alloc_setting = $this->db->query("SELECT employee_allocation FROM chit_settings LIMIT 1")->row();
			$is_allocation_enabled = (!empty($emp_alloc_setting) && intval($emp_alloc_setting->employee_allocation) === 1);

			if(!$is_allocation_enabled){
				// Employee allocation is disabled - return data without allocation check
				if($cus['active'] == 1){
					$result['isValid'] = TRUE;
					$schemeAcc = $this->mobileapi_model->get_payment_details($cus['id_customer'],$cus['id_branch'],$id_sch_acc); 
					$result['cusSchemes'] = $this->array_sort($schemeAcc['chits'], 'allow_pay',SORT_DESC);
					$result['currency'] = $this->mobileapi_model->get_currency($data['emp_branch']);
					$customr_jonedbranch = $this->mobileapi_model->getCusSchejoinedbranch($cusdata['id_customer']);
					if ($result['currency']['currency']['cost_center'] == 3) {
						if (count($result['cusSchemes']) > 0) {
							$result['branches'] = $this->mobileapi_model->branchesData($customr_jonedbranch);
						}
					}
					$result['msg'] = 'Customer data retrieved successfully...';
				}else{
					$result['isValid']= FALSE; 
					$result['msg'] = 'Customer is not active. Kindly contact administrator...';
					$result['cusSchemes'] = [];
				}
			}else{
				// Employee allocation is enabled - check allocation
				if(((!empty($cus['allocated_employee']) && $data['id_employee'] == $cus['allocated_employee'])||(!empty($cus['allocated_agent']) && $data['id_employee'] == $cus['allocated_agent'])) && $cus['active'] == 1){
					//send data true
					$result['isValid'] = TRUE;
					$schemeAcc = $this->mobileapi_model->get_payment_details($cus['id_customer'],$cus['id_branch'],$id_sch_acc); 
					$result['cusSchemes'] = $this->array_sort($schemeAcc['chits'], 'allow_pay',SORT_DESC);

				// 	$cusdata = $this->payment_modal->get_customer($data['cusmobile']);
					$result['currency'] = $this->mobileapi_model->get_currency($data['emp_branch']);
					$customr_jonedbranch = $this->mobileapi_model->getCusSchejoinedbranch($cusdata['id_customer']);
		
					if ($result['currency']['currency']['cost_center'] == 3) {
						if (count($result['cusSchemes']) > 0) {
							$result['branches'] = $this->mobileapi_model->branchesData($customr_jonedbranch);
						}
						// $result['branches'] = $this->$model->branchesData(); 
					}

					$result['msg'] = 'Customer data retrieved successfully...';
				}elseif(((!empty($cus['allocated_employee']) && $data['id_employee'] == $cus['allocated_employee']) || (!empty($cus['allocated_agent']) && $data['id_employee'] == $cus['allocated_agent']) )&& $cus['active'] == 0){
					//inactive false
					$result['isValid']= FALSE; 
					$result['msg'] = 'Customer is not active. Kindly contact administrator...';
					$result['cusSchemes'] = [];
					
				}else{
					//not allocated msg false
					$result['isValid']= FALSE; 
					$result['msg'] = 'Customer not allocated to the employee. Kindly contact administrator...';
					$result['cusSchemes'] = [];
				}
			}
		}else{
			//customer not available false
			$result['isValid']= FALSE;
			$result['msg'] = 'Customer data not available...';
			$result['cusSchemes'] = [];
	        }
		}else{
		   $result['customer'] = [];
		   $result['cusSchemes'] =[];
		   $result['metal'] = [];
		}
		
	    
		$this->response($result,200);
	} 
	
	
	
	
	//to get customer data
	function getCusByMobile_post()
	{ 
		$data = $this->get_values(); 
		$res = array();
		$model = self::ADM_MODEL; 
	    $customer = $this->$model->cusByMobileBranchWise($data['cusmobile'],$data['emp_branch'],$data['branch_settings']); 
	    if(strlen($data['cusmobile']) == 10){
    		if(!empty($customer) && $customer['active'] == 1){
    			$res['status']= TRUE;
    			$res['cus'] = $customer;
    		}else if(!empty($customer) && $customer['active'] == 0){
    			$res['status']= FALSE;
    			$res['cus'] = [];
    			$res['msg'] = "Customer is currently inactive...Kindly contact administrator..";
    		}else{
    		    $res['status']= FALSE;
    		    $res['cus'] = [];
    		    $res['msg'] = "Customer Not Found...";
    		}
	    }else{
	        $res['status']= FALSE;
		    $res['cus'] = [];
		    $res['msg'] = "Please Enter a 10-digit mobile number.";
	    }
		$this->response($res,200);
	}
	
	function getScheme_get()
	{
		$show_referral=true;
		$model = self::MOD_MOB;
		$scheme = $this->$model->get_scheme($this->get('id_scheme'),$this->get('id_customer'));
		$cus_single=$scheme['cusbenefitscrt_type'];
		$emp_single=$scheme['empbenefitscrt_type'];
		$cus_ref_code=$scheme['cus_ref_code'];
		$emp_ref_code=$scheme['emp_ref_code'];
		
		if($cus_single==0 && $emp_single==0)
		{		
			if($cus_ref_code!='' && $emp_ref_code!='')
			{
				
				$show_referral=false;
			}
			else if($cus_ref_code=='' && $emp_ref_code=='')
			{
				
				$show_referral=true;
			}
			else if($cus_ref_code!='' || $emp_ref_code!='')
			{
				$show_referral=true;
			}
			else{
				return $show_referral;
			}
		}	
		
		
		$groups = $this->scheme_modal->get_groups($this->get('id_scheme'));
		$allow_unpaid =$this->scheme_modal->allowUnpaid();	//allow customer not paid single installment		
		$allow_multiple = $this->scheme_modal->allowMultipleChits(); //allow multiple chits for customer		
		
		$allowNewsch = $this->scheme_modal->allowNewscheme_join(); //allow New scheme join to customer 
		$unpaid = $this->scheme_modal->check_unpaid_schemes($this->get('id_customer')); 
		$unClosedAcc = $this->scheme_modal->hasUnclosedAccounts($this->get('id_customer')); 
		$result ='';
		if($allowNewsch == TRUE){
			if($allow_multiple == TRUE )
			{
				if($allow_unpaid == TRUE)
				{
					$allow_join = array('status'=> TRUE);
				}
				else
				{
					if($unpaid == TRUE)
					{
						$allow_join = array('status'=> FALSE, 'msg' => 'You can\'t join now, as you have scheme accounts without single payment, make payments for unpaid before joining new scheme' );
					}
					else
					{
						$allow_join = array('status'=> TRUE);
					}
				}
			}
			else
			{
				if($unClosedAcc == TRUE)
				{
					$allow_join = array('status'=> FALSE, 'msg' => 'You have unclosed chits, kindly contact customer care and close to join new scheme.' );
				}
				else
				{
					$allow_join = array('status'=> TRUE);
				}
			}
			$allow_join = array('status'=> TRUE);
			
		}else
		{
			$allow_join = array('status'=> FALSE, 'msg' => 'Kindly visit our showroom for new scheme enrollment....' ); 
		}
		
		$weights = array();
		$weights_data = $this->$model->get_weights();
		
		foreach($weights_data as $weight)
		{			
			$weights[]=array(
								'weight'    => $weight['weight']	
							);
		}
		$result = array('scheme' => $scheme,'show_referral' => $show_referral,'allow_join' => $allow_join,'weights' => $weights,'groups'=>$groups);
		$this->response($result,200);
	}
	
	function paymentHistory_get()
	{ 
		$model = self::ADM_MODEL;		 
		$payments = $this->$model->get_paymenthistory($this->get('mobile'),$this->get('id_employee'));
		//print_r($payments);exit;
		//PDF url
		if(!empty($payments))
		{
		    foreach($payments as $pay_id)
		    {
		       
					if($pay_id['id_payment'] > 0 && $pay_id['allow_print'] == 1){
						$payment_name = 'E_'.$pay_id['id_payment'].'.pdf';
						if($pay_id['id_branch'] > 0 && $pay_id['id_branch'] != ''){
							$file = base_url().''.self::PAY_PATH.''.$pay_id['id_branch'].'/'.$pay_id['id_payment'].'/'.$payment_name;
						}else{
						    $file = base_url().''.self::PAY_PATH.''.$pay_id['id_payment'].'/'.$pay_id['id_payment'].'/'.$payment_name;
						}
						if($file != ''){
								$pay_id['print_url'] = $file; 
								$pay_id['payment_name'] = $payment_name;
						}else{
								$pay_id['print_url'] = $file; 
								$pay_id['payment_name'] = $payment_name;
						}
						 $pay_id['pay_printable'] =  $this->getPaymentData($pay_id['id_payment']);   
					}
					$pay_history[] = $pay_id;
		    }
		}else{
		    $pay_history = array("status"=> FALSE,"msg" => "No Payments");
		}
		$this->response($pay_history,200);
	}   
	
		function getPaymentData($pay_id)
	{
	    
	    $model = self::ADM_MODEL;
		$payment     = $this->$model->get_entry_records($pay_id);
		$company = $this->$model->get_company();
		$amt_to_words = $this->no_to_words($payment['payment_amount']);

		$paymentstring = "";
		$paymentstring .= "\r\n";
		$paymentstring .= "\x1b\x45\x01   ".$company['company_name']."\x1b\x45\x01\r\n";
		$paymentstring .= "\x1b\x45\x01   ".$company['address1']."\x1b\x45\x01\r\n";
		$paymentstring .= "\x1b\x45\x01   ".$company['city'].' - '.$company['pincode']."\x1b\x45\x01\r\n";
		if($payment['branch_settings'] == 1){
		    $paymentstring .= "BRANCH : ".$payment['branch_name']."\x1b\x45\x01\r\n";
		}
		$paymentstring .= "\x1b\x45\x01 Print Taken On : ".date('d-m-Y h:i:s A')."\x1b\x45\x01\r\n";
		$paymentstring .= "Emp Name : ".$payment['emp_data']."\x1b\x45\x01\r\n";
		$paymentstring .= "\r\n";
        $paymentstring .= "      \x1b\x45\x01 ".$payment['scheme_name']." \x1b\x45\x01 \r\n";
        $paymentstring .= self::HORIZONTAL_LINE['HR3_58MM']."\r\n";
        $paymentstring .= "\x1b\x45\x01A/C Name     ".($payment['account_name']). "\r\n";
		$paymentstring .= "\x1b\x45\x01A/C No       ".($payment['scheme_acc_number'])."\r\n";
		$paymentstring .= "\x1b\x45\x01RECEIPT NO   ". ($payment['receipt_no']). "\r\n";
		$paymentstring .= "\x1b\x45\x01PAID DUE     ". ($payment['paid_due']). "\r\n";
		$paymentstring .= "\x1b\x45\x01PAID MODE    ". ($payment['payment_mode']). "\r\n";
		$paymentstring .= "\x1b\x45\x01PAID WGT     ". ($payment['metal_weight']). " G\r\n";
		$paymentstring .= "\x1b\x45\x01BENEFIT WGT  ". (isset($payment['saved_benefits']) && $payment['saved_benefits'] > 0 ? $payment['saved_benefits'] : '0.000'). " G\r\n";
		$paymentstring .= "\x1b\x45\x01BENEFIT AMT  ". (isset($payment['saved_benefit_amt']) && $payment['saved_benefit_amt'] > 0 ? number_format($payment['saved_benefit_amt'],2,'.','') : '0.00'). "\r\n";
		$paymentstring .= "\x1b\x45\x01MOBILE       ". ($payment['mobile']). "\r\n";
	    $paymentstring .= "\x1b\x45\x01METAL RATE   ". number_format($payment['metal_rate'],2,'.',''). "\r\n";
	    $paymentstring .= "\x1b\x45\x01TOTAL AMT    ". number_format($payment['tot_paid_amount'],2,'.','')."\r\n";
		$paymentstring .= "\x1b\x45\x01TOTAL WGT    ". ($payment['acc_weight']). " G\r\n";
		$paymentstring .= "\x1b\x45\x01PAID DATE    ". substr($payment['date_payment'],0,10)."\r\n";
		//$paymentstring .= "       AREA         :\t". ($payment['address1']). "\r\n";
		$paymentstring .= self::HORIZONTAL_LINE['HR3_58MM']."\r\n";
		$paymentstring .= "\x1b\x45\x01Received with thanks from\x1b\x45\x01\r\n";
		$paymentstring .= "\x1b\x45\x01".$payment['firstname']."\x1b\x45\x01\r\n";
		$paymentstring .= "\x1b\x45\x01\r\n";
		$paymentstring .= "INR \x1b\x45\x01".$payment['payment_amount']."\x1b\x45\x01\r\n";
		$paymentstring .= "\x1b\x45\x01".$amt_to_words."\x1b\x45\x01\r\n";
    	$paymentstring .= "For \x1b\x45\x01".$company['company_name']."\x1b\x45\x01\r\n";
    	$paymentstring .= "\x1b\x45\x01\r\n";
    	$paymentstring .= "                      Signature\r\n";
	   return $paymentstring;
	}
	
    
    
	// branch name list
	public function get_branch_get()
	{		
		$model = self::ADM_MODEL;
		$id_scheme = $this->get('id_scheme')?$this->get('id_scheme'):'';
		$data=$this->$model->get_branch($this->get('emp_branch'),$this->get('id_profile'),$id_scheme);
	    echo json_encode($data);		
	}
	 
	//to check the mobile number already registered

	function isNumberRegistered($data)

	{
 
		$model = self::ADM_MODEL;

		$mobile = $data['mobile'];    

		$email =  $data['email'];    

	    $m_exist =	$this->mobileapi_model->isMobileExists($mobile);	

	    $e_exist =	($email != '' ? $this->$model->clientEmail($email):FALSE);	


	    $limit= $this->services_modal->limitDB('get','1');

		$count= $this->services_modal->customer_count();

		$limit_exceed = 0;

		if($limit['limit_cust']==1 && $count >= $limit['cust_max_count'])

		{

			$limit_exceed = 1;

		}

		$is_reg = ($m_exist ? TRUE : ($e_exist ? TRUE : FALSE));

		$result = array(
							'is_reg' => $is_reg,

							'msg'	 => ($is_reg ? ($m_exist && $e_exist)?"Mobile and Email already registered":($m_exist ?"Mobile already registered":"E-mail already Registered" ): ($limit_exceed == 1 ? 'Temporarily New user registration is unavailable, Kindly contact Customer care...' : FALSE))

						);
						

		return $result;

	}
    
    public function get_all_branch_get()
    {
    	$model = self::ADM_MODEL;
    	$data=$this->$model->get_all_branch();
        echo json_encode($data);
    }
    
    /*function pay_collection_post()
	{ 
		$data = $this->get_values();
		$model = self::ADM_MODEL; 
		
		$result = $this->$model->get_payment_collection($data['fromdate'],$data['todate'],$data['id_employee'],$data['emp_branch']);  
		if(sizeof($result) > 0)
		{
		    $response = array('status' => true,'message' => 'Data retrieved successfully','data' => $result);
		}else{
		    $response = array('status' => false,'message' => 'No Records!','data' => $result);
		}
		$this->response($response, 200);
	}*/
	
	//Adminapp payment -collection report //HH
   function pay_collection_post()
	{ 
		$data = $this->get_values(); 

		$model = self::ADM_MODEL; 
		$from_date = $data['fromdate'];
		$to_date = $data['todate'];

		$result['data'] = $this->$model->get_payment_collection($data['fromdate'],$data['todate'],$data['id_employee'],$data['login_type'],$data['emp_branch'],$data['payment_mode'],$data['id_metal']);  
		//print_r($result['data']);exit;
		$result['metal'] = $this->$model->get_metalData();

	    
	  
	    
	
	    if($result)
		{
		    if(sizeof($result['data']) > 0){
		    foreach($result['data'] as $pay_id)
		    {
		        
		      
					if($pay_id['id_payment'] > 0 && $pay_id['allow_print'] == 1){
						$payment_name = 'E_'.$pay_id['id_payment'].'.pdf';
						if($pay_id['id_branch'] > 0 && $pay_id['id_branch'] != ''){
							$file = base_url().''.self::PAY_PATH.''.$pay_id['id_branch'].'/'.$pay_id['id_payment'].'/'.$payment_name;
						}else{
						    $file = base_url().''.self::PAY_PATH.''.$pay_id['id_payment'].'/'.$pay_id['id_payment'].'/'.$payment_name;
						}
						if($file != ''){
								$pay_id['print_url'] = $file; 
								$pay_id['payment_name'] = $payment_name;
						}else{
								$pay_id['print_url'] = $file; 
								$pay_id['payment_name'] = $payment_name;
						}
						 $pay_id['pay_printable'] =  $this->getPaymentData($pay_id['id_payment']);   
					}
					
				//	print_r($pay_id);exit;
					$pay_history['data'][] = $pay_id;
		    }
		    $pay_history['metal'] = 	$result['metal'] ;
		    $this->response($pay_history, 200);
		    }else{
		        $msg = array('data' => [],'message' => 'No Records!'); 
			    $this->response($msg, 200); 
		    }
		}	 
		else
		{
			$msg = array('data' => [],'message' => 'No Records!'); 
			$this->response($msg, 200); 
		}
	
	}
	
	function checkMobileReg_get()
	{
		$model = self::MOD_MOB;
		$mobile = $this->get('mobile');    
		$login_type = $this->get('login_type');
	    $status =	$this->$model->isMobileExists($mobile);	
	   	$result = array(
							'mobile' => $mobile,
							'is_reg' =>	($status ? $status : FALSE)
				);
		$this->response($result, 200);
	}
	
	function generateOTP_get()
	{			
		$model = self::MOD_MOB;
		$email=$this->get('email');
		$otp   = $this->$model->generateOTP();
		//For Demo or testing Purpose statis OTP :: 11-7-2025
		$otp['otp'] = 123456;
		if($email!=''&& $email!='undefined')
		{
			$to = $email;
			$data['otp']=$otp['otp'];
			$data['type'] = 4;
			$data['company_details'] = $this->comp;
			$mail= $this->$model->forgetUser($this->get('mobile'));
				$data['name'] = $mail['firstname'];
				$subject = "Reg: ".$this->comp['company_name']." Purchase plan";
				$message = $this->load->view('include/emailAccount',$data,true);
				$this->load->model('email_model');
				$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
				if($this->config->item('sms_gateway') == '1'){
        		    $this->sms_model->sendSMS_MSG91($this->get('mobile'),$this->otp_sms($otp),'','1607100000000199488');		
        		}
        		elseif($this->config->item('sms_gateway') == '2'){
        	        $this->sms_model->sendSMS_Nettyfish($this->get('mobile'),$this->otp_sms($otp),'trans');	
        		}
        		elseif($this->config->item('sms_gateway') == '3')
            	{
                    $this->sms_model->sendSMS_SpearUC($this->get('mobile'),$this->otp_sms($otp),'','');	
                }
                elseif($this->config->item('sms_gateway') == '4'){
					$this->sms_model->sendSMS_Asterixt($this->get('mobile'),$this->otp_sms($otp),'','1607100000000199488');		
				}
				elseif($this->config->item('sms_gateway') == '5'){
					$this->sms_model->sendSMS_Qikberry($this->get('mobile'),$this->otp_sms($otp),'','1607100000000199488');		
				}
        		if($service['serv_whatsapp'] == 1)
        		{
        		    $smsData = ["message" => $this->otp_sms($otp), "template_name" => $service['template_name'], "params" => $params];
                    $this->sms_model->send_whatsApp_message($this->get('mobile'),$smsData);
                }
				$this->response($otp,200);		
		}
		else
		{
		$mail= $this->$model->forgetUser($this->get('mobile'));
		$to = $mail['email'];
		$data['otp']=$otp['otp'];
		$data['type'] = 0;
		$data['company_details'] = $this->comp;
		$data['name'] = $mail['firstname'];
		$subject = "Reg: ".$this->comp['company_name']." purchase plan forgot password";
		$message = $this->load->view('include/emailAccount',$data,true);
		$this->load->model('email_model');
		$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
		
		$msg = "Dear ".$mail['firstname'].", Your otp to reset password is ".$otp['otp'].". Regards, ".$this->comp['cmp_name_sms'];
		if($this->config->item('sms_gateway') == '1'){
		    $this->sms_model->sendSMS_MSG91($this->get('mobile'),$this->otp_sms($otp),'','1607100000000199488');		
		}
		elseif($this->config->item('sms_gateway') == '2'){
	        $this->sms_model->sendSMS_Nettyfish($this->get('mobile'),$this->otp_sms($otp),'trans');	
		}
		elseif($this->config->item('sms_gateway') == '3')
    	{
            $this->sms_model->sendSMS_SpearUC($this->get('mobile'),$this->otp_sms($otp),'','1607100000000199488');	
        }
        elseif($this->config->item('sms_gateway') == '4'){
			$this->sms_model->sendSMS_Asterixt($this->get('mobile'),$this->otp_sms($otp),'','1607100000000199488');	
		}
		elseif($this->config->item('sms_gateway') == '5'){
			$this->sms_model->sendSMS_Qikberry($this->get('mobile'),$this->otp_sms($otp),'','1607100000000199488');	
		}
		if($service['serv_whatsapp'] == 1)
		{
		    $smsData = ["message" => $this->otp_sms($otp), "template_name" => $service['template_name'], "params" => $params];
            $this->sms_model->send_whatsApp_message($this->get('mobile'),$smsData);
        }
		$this->response($otp,200);
		}
	}
	
	function check_regOTP_post()
	{
		$data = $this->get_values();	
		$otp_time = date('Y-m-d H:i:s');
		if($data['sysotp'] == $data['userotp'])
		//if(1234 == $data['userotp'])
		{
			if($otp_time <= date('Y-m-d H:i:s',strtotime($data['last_otp_expiry'])))
			{
				$result =  array('is_valid' => TRUE, 'msg' => 'Success');
			}
			else
			{
				$result = array('is_valid' => FALSE, 'msg' => 'Your OTP expired');
			}
		}	
		else
		{
			$result =  array('is_valid' => FALSE, 'msg' => 'Your OTP is invalid');
		}
		$this->response($result,200);
	}
	
	function isNumberRegistered_get()
	{
		$model = self::MOD_MOB;
		$mobile = $this->get('mobile');
		$email = $this->get('email');
	    $m_exist =	$this->$model->isMobileExists($mobile);
	    $e_exist =	( $this->config->item('isCusEmailReq') == 0 ? ($email != '' ? $this->$model->clientEmail($email) : FALSE) : $this->$model->clientEmail($email));
	    $limit= $this->services_modal->limitDB('get','1');
		$count= $this->services_modal->customer_count();
		$limit_exceed = 0;
		if($limit['limit_cust']==1 && $count >= $limit['cust_max_count'])
		{
			$limit_exceed = 1;
		}
		$is_reg = ($m_exist ? TRUE : ($e_exist ? TRUE : FALSE));
		$result = array(
							'mobile' => $mobile,
							'is_reg' => $is_reg,
							'msg'	 => ($is_reg ? ($m_exist && $e_exist)?"Mobile and Email already registered":($m_exist ?"Mobile already registered":"E-mail already Registered" ): ($limit_exceed == 1 ? 'Temporarily New user registration is unavailable, Kindly contact Customer care...' : FALSE))
						);
		$this->response($result, 200);
	}
	
	function otp_sms($otpStr)
	{  
		$model = self::MOD_MOB;
		$company = $this->$model->company_details();
		$expiry = date("d-m-Y H:i:s",strtotime($otpStr['expiry']));
       /* $msg ="Your OTP ".$otpStr['otp']." for ".$this->comp['company_name']." purchase plan is valid till ".$expiry.""; */
       
        //$msg = "Dear valued customer, Your OTP ".$otpStr['otp']." for saving scheme is valid till ".$expiry.". For queries contact customer care ".$company['mobile'].". Regards, SRI KRISHNA NAGAI MALIGAI.";
        $msg ="Dear valued customer, Your OTP ".$otpStr['otp']." for saving scheme is valid till ".$expiry. ". For queries contact customer care ".$company['mobile']." Regards, DHAMU CHETTIAR NAGAI MAALIGAI.";

		return $msg;
	  
	}
	
	
	
	    
     function mobile_payment_post()
	{ 
	   $postData = $this->get_values();
	   $payData = array(
    	                'firstname'     => $postData['firstname'], 
                        'lastname'      => $postData['lastname'],
                        'phone'         => $postData['phone'],
                        'id_branch'     => $postData['id_branch'],
                        'amount'        => $postData['amount'],
                        'redeemed_amount' => $postData['redeemed_amount'],
                        'productinfo'   => $postData['productinfo'],
                        'email'         => $postData['email'],
                        'gateway'       => $postData['gateway'],
                        'pg_code'       => $postData['pg_code'],
                        'pay_arr'       => $postData['pay_arr'],
                        
                        //collection app payment changes...
                        'login_type' => $postData['login_type'],
                        'id_employee' => $postData['id_employee'],
                        'pay_mode' => $postData['pay_mode'],
                        'redeemed_amount' => $postData['redeemed_amount'],
                        
                        
                        
	                );         
	   if(!empty($payData['phone']) && $payData['amount'] > 0)
	   {
		   //get the values posted from mobile in array 
		   $pay_flag = TRUE;
		   $allow_flag = FALSE;
		   $submit_pay = FALSE;
		   $actAmount = (float) $payData['amount'];  // sum of all chit amount
		   $cusData = $this->payment_modal->get_customer($payData['phone']);
		  // $pay_email = (!empty($cusData['email']) && $cusData['email'] != '') ? $cusData['email'] : $cusData['pg_email'];
		  
		 $pay_email = str_replace(' ', '', ((!empty($cusData['email']) && $cusData['email'] != '') ? $cusData['email'] : $cusData['pg_email'])) ;

		   $gateway = ( isset($payData['gateway']) ? ($payData['gateway'] == 'undefined' ? 1 : $payData['gateway']) :1 );
		   $paycred = $this->payment_gateway($cusData['id_branch'],$payData['gateway']);
		    $start_year = $this->payment_modal->get_financialYear();
		    
		    //Collection app offline payments...
		    
		    $idemployee = ($payData['login_type'] == 'EMP' && $payData['id_employee'] > 0 ? $payData['id_employee'] : NULL);
		    $added_through = (isset($payData['id_employee']) && $payData['id_employee'] > 0 ? 3:2); // 2- custmer mobile app, 3 - Admin mobile app
		    $redeemed_amount = $payData['redeemed_amount'] ;
		    
		   //check pay_flag
		   if($pay_flag )
		   {
				//generate txnid
                 $txnid = uniqid(time());
				 $i=1;
				// $sch_payment = json_decode($payData['pay_arr']);
				 $sch_payment =  json_decode(urldecode($payData['pay_arr'])) ;          // decode format for post method....  
				 $udf1= "";
				 $udf2= "";
				 $udf3= "";
				 $productinfo= "";
			 	 $payIds= "";
			 	 
			 //	 print_r($sch_payment);exit;
				 $this->db->trans_begin();
				 foreach ($sch_payment as $pay){	
				   if($cusData['branch_settings']==1)
				   {
					    if($cusData['is_branchwise_cus_reg']==1)
						{
							$id_branch  = $cusData['id_branch'];
						}
						else
						{
							$id_branch  = $pay->id_branch;
							//$id_branch  = $branch['sch_join_branch'];
						}
					}
					else{
						$id_branch = NULL;
					} 
				    //validate amount
				   $chit = $this->payment_modal->get_schemeByChit($pay->udf1);	//print_r($chit);exit;
				   if( $pay->scheme_type == 1)
				   {						   
						  $metal_rate = $this->payment_modal->getMetalRate('');	
						  $gold_rate = (float) $metal_rate['goldrate_22ct'];
						  $amt = $gold_rate * $pay->udf2;
						  $allow_flag =  ($pay->amount >= $amt? TRUE :FALSE);
				   }
				   else
				   {
						$allow_flag =  ($pay->amount >= $chit['amount']? TRUE :TRUE);
				   }
				   if($pay->scheme_type == 2 || ($pay->scheme_type == 3 && ($chit['wgt_convert']==0 || $chit['wgt_store_as']==1))){
				   	    $data = array('metal_rate'=>$pay->udf3,'amount'=>$pay->udf4);
						$metal_wgt = $this->amount_to_weight($data);
				   }
				   else{	
						$metal_wgt = (isset($pay->udf2) && $pay->udf2 !='' ? $pay->udf2 : 0.000);
				   }
				  
        			
        			// GST Calculation    //collection app payment changes
        			$gst_amt = 0;
					$amount = $pay->amount + $pay->discount;
					
	    			if($chit['gst'] > 0 ){
				        $insAmt_withoutDisc = $amount-$pay->discount;
				        
				        if($sch_data['gst_type'] == 0){  // Inclusive  
	                        $gst_removed_amt = $insAmt_withoutDisc*100/(100+$chit['gst']);
	                    	$gst_amt = $insAmt_withoutDisc - $gst_removed_amt;
	                    	$metal_wgt = ($gst_removed_amt+$pay->discount)/$pay->udf3;  
	                    }
	                    else if($sch_data['gst_type'] == 1){ // Exclusive
	                        $amt_with_gst = $insAmt_withoutDisc*((100+$chit['gst'])/100);
	                    	$gst_amt = $amt_with_gst - $insAmt_withoutDisc ;
	                    	$metal_wgt = $amount/$pay->udf3; 
	                    }
	   	            }
	   	            
	   	            
	   	            $discount 	= ($pay->discount >0 ? $pay->discount : 0.00);
        			
        				$due_month	= date("m");
					$month		= date("m");
					$due_year	= date("Y");
					// ND - normal, PN - pending & normal, AN - adv & normal, PD pending due ,AD-adv due
					$dueType = ''; 
					if($pay->due_type == 'PD'){
					    $month  	=  NULL;
        				$due_year   =  NULL;
        				$dueType    = 'PD';
					}
					else if($pay->due_type == 'ND')
					{ 
						$dueData = $this->payment_modal->generateDueDate($pay->udf1,$dueType);
						// set due data
						$month      = $dueData['due_month'];
						$due_year   = $dueData['due_year'];
						$dueType = $pay->due_type;
					}
					else if($pay->due_type == 'AD')
					{
					    $dueType = $pay->due_type;
					    $dueData = $this->payment_modal->generateDueDate($pay->udf1,$dueType);
						// set due data
						$month      = $dueData['due_month'];
						$due_year   = $dueData['due_year'];    									
					}
					else
					{
						$dueType = $pay->due_type;
					} 
				
				    // metal_wgt_decimal = 2 means only 2 decimals are allowed for metal wgt, hence bcdiv() is used to make the weight to 2 decimals and 0 is appended as last digit.
					$decimal = $chit['metal_wgt_decimal'];   
                    $round_off = $chit['metal_wgt_roundoff'] ; 
                    $metal_wgt =  ($round_off == 0 ? bcdiv($metal_wgt,1,$decimal) : $metal_wgt );
        			
        		   if($allow_flag){ 
        		       $custom_entry_date = $this->payment_modal->get_entrydate($id_branch);
        				//set insert data					
        					$insertData = array(
        							"id_scheme_account"	 => (isset($pay->udf1)? $pay->udf1 : NULL ),
									"id_scheme" => (isset($chit['id_scheme']) ? $chit['id_scheme'] : 0),
        							"payment_amount" 	 => (isset($pay->amount)? $pay->amount+($pay->discount==""?0.00:$pay->discount) : NULL ), 
        							"gst" 	    		 => (isset( $chit['gst'])? $chit['gst'] : 0 ),
        							"gst_type" 	    	 => (isset($chit['gst_type'])? $chit['gst_type'] : 0 ),
        							//"payment_type" 	     => (isset($paycred) ? ($paycred['pg_name']) : ''),
        							//"due_type" 	   		 => $pay->due_type,
        							"no_of_dues" 	     => 1,
        							"actual_trans_amt"      => (isset($actAmount) ? $actAmount : 0.00),
        							"date_payment" 		 =>  date('Y-m-d H:i:s'),
        							"metal_rate"         => (isset($pay->udf3) && $pay->udf3 !='' ? $pay->udf3 : 0.00),
        							"metal_weight"       =>  $metal_wgt,
        						//	"id_transaction"     => (isset($txnid) ? $txnid.'-'.$i : NULL),
        							//"ref_trans_id"       => (isset($txnid) ? $txnid : NULL),// to update pay status after trans complete.
        							"remark"             =>  'Paid for '.$pay->udf5.($pay->udf5 >1?'months':'month'),
        							//"added_by"			 =>  2,
        							"add_charges" 	     => (isset($pay->charge) ?$pay->charge : NULL), 
        							"discountAmt"        => ($pay->discount!="" ? $pay->discount : 0.00),
        							"payment_status"     => $this->payment_status['pending'],
        							"id_payGateway"      => (isset($gateway) ? $gateway: 1),
        							'id_branch'          => $id_branch,
        							"receipt_year"            => $start_year,
        					// 		"redeemed_amount"     => (isset($payData['redeemed_amount']) ?$payData['redeemed_amount'] :0.00),
        					// 	    "is_point_credited"   => 1
        							//status - 0 (pending), will change to 1 after approved at backend
        							
        							
        							//collection app offline payments...
									"id_transaction"     => ($gateway == 0 ?NULL:(isset($txnid) ? $txnid.'-'.$i : NULL)),
									"ref_trans_id"       => ($gateway == 0 ?NULL:(isset($txnid) ? $txnid : NULL)),// to update pay status after trans complete.
        							"added_by"			 =>  $added_through,
									"id_employee"        => (isset($payData['id_employee']) && $payData['login_type'] == 'EMP' ?$payData['id_employee'] :NULL),
							 		"id_agent"        => (isset($payData['id_employee']) && $payData['login_type'] == 'AGENT' ?$payData['id_employee'] :NULL),
        							"payment_mode" =>  (isset($payData['pay_mode']) ? $payData['pay_mode'] : NULL),
							 		"card_no"        => (isset($pay->refernceNo) ?$pay->refernceNo : NULL),
							 		"payment_status"     => ($gateway == 0 ? ($added_through== 3 ?$this->payment_status['success']:$this->payment_status['awaiting']):$this->payment_status['pending']),
                                    "payment_type" 	     => ($gateway == 0 ?'Collection':(isset($payData['pg_code']) ? (isset($paycred) ? $paycred['pg_name'] : '') : '') ),
                                    "act_amount"         => (isset($pay->amount)? $pay->amount : NULL ),
                                    "gst_amount"    	 => $gst_amt,
                                    "due_type"           => $dueType,
							 		"due_month"          => $month,
							 		"due_year"           => $due_year,
							 		'custom_entry_date'   =>  ($custom_entry_date['edit_custom_entry_date']==1 ? $custom_entry_date['custom_entry_date']:NULL),
							 		"pay_email"          => $pay_email
							 		
							 		
        						);  
        				$udf1 = $udf1." ".$pay->udf1;
        				$udf2 = $udf2." ".$pay->udf2;
        				$udf3 = $udf3." ".$pay->udf3;
        				$productinfo = $productinfo." ".$pay->chit_number;
	                       //print_r($payData);exit;
        				$payment = $this->payment_modal->addPayment($insertData);
        				
        				$payIds = $payIds.",".$payment['insertID'];                //collection app payment changes
        				
						
        				
        		    /*//RHR scheme starts...		
                    	$dt_pay = date('Y-m-d H:i:s',strtotime(str_replace("/","-",$insertData['date_payment'])));							
                    	$ins_cycle = $this->payment_modal->get_due_date($dueType,$dt_pay,$pay->udf1);  
                    
                    	$cycle_data = array('due_date' 		  =>  (isset($ins_cycle[0]['due_date_from'])?$ins_cycle[0]['due_date_from']: NULL),
                    	'grace_date' 		  =>  (isset($ins_cycle[0]['grace_date'])?$ins_cycle[0]['grace_date']: NULL),
                    	'installment' 		  =>  (isset($ins_cycle[0]['installment'])?$ins_cycle[0]['installment']: NULL),
                    	'is_limit_exceed' 		  =>  (isset($ins_cycle[0]['is_limit_exceed'])?$ins_cycle[0]['is_limit_exceed']: 0), );
                    
                    	$this->payment_modal->updData($cycle_data, 'id_payment', $payment['insertID'], 'payment');
                    //RHR scheme ends....	
*/
        				$i++;
        			}
				}
				if($this->db->trans_status()=== TRUE && $allow_flag)
	            {
				 	$this->db->trans_commit();
				 
				    //collection app offline payments 
				    
				    //$submit_pay = TRUE;
				    
				    if($gateway == 0){ // For admin app
					 	$paymtData['schAc_ids'] = $productinfo;
					 	$paymtData['pay_ids'] = $payIds;
					 	$paymtData['login_type'] = $payData['login_type'];
					 	$paymtData['id_employee'] =  (isset($payData['id_employee']) && $payData['login_type'] == 'EMP' ?$payData['id_employee'] :NULL);
					 	$paymtData['id_agent'] =  (isset($payData['id_employee']) && $payData['login_type'] == 'AGENT' ?$payData['id_employee'] :NULL);
					 	$paymtData['payData'] = $insertData;    
					    $paymtData['payAmount'] = $payData['amount']; 
					 	$this->adminAppSuccess($paymtData);
					 }else{
					 	$submit_pay = TRUE;
					 }
				 
				}
				else{
				 	$this->db->trans_rollback();
					$submit_pay = FALSE;
				}
				
				if($submit_pay)
				{ 
				    $updData = array("last_payment_on" => date("Y-m-d H:i:s"));
			        $this->payment_modal->updData($updData,'id_customer',$cusData['id_customer'],'customer');
                    $secretKey = $paycred['param_1']; //Shared by Cashfree  
                    $appId     = $paycred['param_3'];
					//set data for hash generation
					$data['pay'] =	array (
						'key' 			=> $secretKey, 
						'txnid' 		=> $txnid, 
						'amount' 		=> (isset($payData['amount'])   ? $payData['amount']:''),
						'productinfo'	=> (isset($productinfo)    ? $productinfo :''),
						'firstname' 	=> (isset($cusData['firstname'])? $cusData['firstname'] :''),
						'lastname' 		=> (isset($cusData['lastname']) ? $cusData['lastname']:''),
						'email' 		=> (isset($cusData['email'])    ? $cusData['email']:''), 
						'phone' 		=> (isset($cusData['mobile'])    ? $cusData['mobile'] :''),
						'address1'		=> (isset($cusData['address1']) ? $cusData['address1']:''), 
						'address2'		=> (isset($cusData['address2']) ? $cusData['address2'] :''), 
						'city'			=> (isset($cusData['city']) ? $cusData['city'] :''), 
						'state'			=> (isset($cusData['state']) ? $cusData['state'] : ''), 
						'country'		=> (isset($cusData['country']) ? $cusData['country'] : ''), 
						'zipcode'		=> (isset($cusData['pincode']) ? $cusData['pincode'] :''), 
						'udf1' 			=> (isset($udf1)    ? $udf1 :''),
						'udf2' 			=> (isset($udf2)    ? $udf2 :''),
						'udf3'			=> (isset($udf3)    ? $udf3 :''),
						'udf4' 			=> (isset($payData['udf4'])    ? $payData['udf4'] : ''),
						'udf5' 			=> (isset($payData['udf5'])? $payData['udf5']:'') 
					);
					$gen_email =  $this->random_strings(8).'@gmail.com'; 
					if($payData['pg_code'] == 4){   // cash free 
    					$data['cashfreepay'] =	array (
                            'appId'         => $appId,  //Shared by Cashfree
                            'orderId'       => $txnid,
                            'orderAmount' 	=> (string) $actAmount, 
                            'orderCurrency'	=> 'INR',
                            'orderNote'	    => 'Purchase plan payment from mobile app',
                            'customerName' 	=> (isset($payData['firstname'])? (isset($payData['lastname']) ? $payData['firstname'].' '.$payData['lastname']:$payData['firstname']) :''),
                            'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                            'customerPhone' => (isset($payData['phone'])    ? $payData['phone'] :''),
        					"notifyUrl"     => $this->config->item('base_url')."index.php/services/cashfreeStatusNotify/".$payData['gateway'],
                            "stage"         =>  $paycred['type'] == 0 ? "TEST" : 'PROD',//"TEST/PROD",
                        ); 
                        $token = $this->generateCFtoken($txnid,$actAmount,$data['cashfreepay'],$paycred); 
                		$this->response($token,200);
				    } 
				    else if($payData['pg_code'] == 6){   // Ippo Gateway
    					$data['ippo'] =	array (
                            'publicKey'     => $paycred['param_3'],  //Shared by Ippo gateway
                            'secretKey'     => $paycred['param_1'], //Shared by Ippo gateway
                            'transaction'   =>  $txnid,
                            'orderAmount' 	=>  $actAmount, 
                            'orderCurrency'	=> 'INR',
                            'orderNote'	    => 'Online Money Transaction',
                            'customerName' 	=> (isset($cusData['firstname'])? (isset($cusData['lastname']) ? $cusData['firstname'].' '.$cusData['lastname']:$cusData['firstname']) :''),
                            'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                            'customerPhone' => (isset($cusData['phone'])    ? $cusData['phone'] :''),
        					"returnUrl"     =>  $this->config->item('base_url')."index.php/paymt/ipporesponseURL" 
                        ); 
                        $orderData = $this->generateOrderData($txnid,$actAmount,$data['ippo'],$paycred);
                        $this->response($orderData,200);
                       // $this->load->view('ippo/ippo_payment',$data);
    				}  
    				else if($payData['pg_code'] == 7) // Razor pay
				    {
                        $data['razor'] =	array (
                            'publicKey'     => $paycred['param_3'],  //Shared by Razor gateway
                            'secretKey'     => $paycred['param_1'], //Shared by Razor gateway
                            'transaction'   =>  $txnid,
                            'orderAmount' 	=>  $actAmount, 
                            'orderCurrency'	=> 'INR',
                            'orderNote'	    => 'Online Money Transaction',
                            'customerName' 	=> (isset($cusData['firstname'])? (isset($cusData['lastname']) ? $cusData['firstname'].' '.$cusData['lastname']:$cusData['firstname']) :''),
                            'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                            'customerPhone' => (isset($cusData['phone'])    ? $cusData['phone'] :'')
                        ); 
                        $orderData = $this->generateRazorOrderData($txnid,$actAmount,$data['razor'],$paycred);
                        if($payment['insertID'] > 0)
                        {
                            $orderdet = array("payu_id" => $orderData['order_id']);
                            $this->payment_modal->updData($orderdet,'ref_trans_id',$txnid,'payment');
                        }
                        $this->response($orderData,200);
				    } 
    				else if($payData['pg_code'] == 8) { // Easebuzz gateway
                        $amount = $actAmount.".00";
                        $env = $paycred['type'] == 0 ? "test" : 'prod';
                        $key = $paycred['param_1'];
                        $salt = $paycred['param_3'];
                        $productinfo = 'Online Money Transaction';
                       // $email = !empty($cusData['email']) ? $cusData['email'] : $gen_email;
                       $email = $pay_email;
                        $hash_sequence = trim($key).'|'.trim($txnid).'|'.trim($amount).'|'.trim($productinfo).'|'.trim($cusData['firstname']).'|'.trim($email).'|||||||||||'.trim($salt);
                        $hash_value =  strtolower(hash('sha512',$hash_sequence));
                        $easebuzz = array (
                            'salt'         =>$salt ,  //Shared by Easebuzz gateway
                            'Key'           => $key, //Shared by Easebuzz gateway
                            'txnid'         =>  $txnid,
                            'amount' 	    =>  $amount, 
                            'productinfo'	=> $productinfo,
                            'firstname' 	=> (isset($cusData['firstname'])? (isset($cusData['lastname']) ? $cusData['firstname'].' '.$cusData['lastname']:$cusData['firstname']) :''),
                            'email'         => $email,
                            'phone'         => (isset($cusData['mobile']) ? $cusData['mobile'] :''),
                            'surl'          => $this->config->item('base_url')."index.php/mobile_api/easebuzzSuccessResponse/",
                            'furl'          => $this->config->item('base_url')."index.php/mobile_api/easebuzzFailedResponse/",
                            'hash'          => $hash_value
                        ); 
                        $response['easebuzzData'] = $this->_payment($easebuzz,'',$key,$salt,$env);
                        $response['pay_mode'] = $env=='prod'?'production':'test';
                        $this->response($response,200);
    				}
	            }else{
        	        $result['status'] = false;
                    $result['message'] = 'Unable to proceed your request...';
        	        $this->response($result,200); 
                }
		   }
	    }else{
	        $result['status'] = false;
            $result['message'] = 'Invalid payment request...';
	        $this->response($result,200); 
	    }
	}
	
		
	function adminAppSuccess($paymtData){ 
		$schAc_ids = explode(' ',$paymtData['schAc_ids']);
		unset($schAc_ids[0]); // 0th key will be always empty so delete
		$pay_ids = explode(',',$paymtData['pay_ids']);
		unset($pay_ids[0]); // 0th key will be always empty so delete 
		
		
		$transData = array();
		$this->load->model('adminappapi_model');
		$this->db->trans_begin();
		
        
        $serviceID = 3;
        $service = $this->services_modal->checkService($serviceID); 
       
			
		if(sizeof($pay_ids) > 0)
		{
			foreach ($pay_ids as $pay_id)
			{	 	
			    $pay = $this->adminappapi_model->getPayGenData($pay_id);
				
				// Multi mode payment
				    if($pay['payment_mode']!= NULL)
     				{
     					$arrayPayMode=array(
        								'id_payment'         => $pay['id_payment'],
    							        'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
        								'payment_date'		 => $pay['date_payment'],
        								'created_time'	     => date("Y-m-d H:i:s"),
        								"payment_mode"       => $pay['payment_mode'],
                						"remark"             => "collectionApp-status",
                						"payment_status"     => 1,
                					    );

						if ($pay['due_type'] == 'GA') {
							$arrayPayMode['id_adv_payment'] = $pay['id_payment'];
							$payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'general_advance_mode_detail');
						} else {
							$update_pmd = array(
								'payment_status' => 9, // Cancelled
								"updated_time" => date('Y-m-d H:i:s'),
								"remark" => "Removed while collection app success " . date('Y-m-d H:i:s'),
							);
							$update_existing_pmd = $this->payment_modal->updData($update_pmd, 'id_payment', $pay['id_payment'], 'payment_mode_details');
							$arrayPayMode['id_payment'] = $pay['id_payment'];
							$payModeInsert = $this->payment_modal->insertData($arrayPayMode, 'payment_mode_details');
						}
    					/* if(!empty($arrayPayMode)){
    						$cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details'); 
    					} */
     				}
     				
     				// RHR scheme starts...

                    $dt_pay = date('Y-m-d', strtotime(str_replace("/", "-", $pay['date_payment'])));
                    
                    $ins_cycle = $this->mobileapi_model->get_due_date($pay['due_type'], $dt_pay, $pay['id_scheme_account']);
                    
                  //  print_r($this->db->last_query()); exit;
                    
                    if (sizeof($ins_cycle[0]) > 0) {
                        $cycle_data = array(
                            'due_date'        => isset($ins_cycle[0]['due_date_from']) ? $ins_cycle[0]['due_date_from'] : NULL,
                            'due_date_to'     => isset($ins_cycle[0]['due_date_to']) ? $ins_cycle[0]['due_date_to'] : NULL,
                            'grace_date'      => isset($ins_cycle[0]['grace_date']) ? $ins_cycle[0]['grace_date'] : NULL,
                            'installment'     => isset($ins_cycle[0]['installment']) ? $ins_cycle[0]['installment'] : NULL,
                            'is_limit_exceed' => isset($ins_cycle[0]['is_limit_exceed']) ? $ins_cycle[0]['is_limit_exceed'] : 0,
                        );
                    
                        $this->mobileapi_model->updData($cycle_data, 'id_payment', $pay['id_payment'], 'payment');
                    }
                    
					 $paid = $this->payment_modal->getPaidInsData($pay['id_scheme_account']);
					if (sizeof($paid) > 0) {
						$paid_ins = array('total_paid_ins' => $paid['paid_installments']);
						$this->payment_modal->updData($paid_ins, 'id_scheme_account', $pay['id_scheme_account'], 'scheme_account');
					}
                    
                    // RHR scheme ends...

     				
     			if($pay['one_time_premium']==1 && $pay['rate_fix_by'] == 0 && $pay['rate_select'] == 1 && ($pay['fixed_wgt'] == null || $pay['fixed_wgt'] == 0))
            			{
            			    $metal_rate = $this->payment_modal->getMetalRate('');	
            				$gold_rate = (float) $metal_rate['goldrate_22ct'];
            			    if($gold_rate != 0)
            			    {
            			        $isRateFixed = $this->mobileapi_model->isRateFixed($pay['id_scheme_account']);
                        			    if($isRateFixed['status'] == 0){
                            		        $updData = array(
                        	   							"fixed_wgt" => $pay['payment_amount']/$gold_rate,
                        	   							"firstPayment_amt" => $pay['payment_amount'],
                        	   							"fixed_metal_rate" => $gold_rate,
                        	   							"rate_fixed_in" => 1,
                        	   							"fixed_rate_on" => date("Y-m-d H:i:s")
                        	   						); 
                        	   					//print_r($updData);exit; 
                        	   				$ratestatus = $this->mobileapi_model->updFixedRate($updData,$pay['id_scheme_account']);
                        			    }
            			    }
            			}
            			
            			
            	$approval_type = $this->config->item('auto_pay_approval');
				if(($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable']==1 || $pay['firstPayamt_as_payamt']==1) && ($pay['firstPayment_amt'] == '' ||  $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0))
				{
					if($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "") ){ // Fix First payable as weight
						$fixPayable = array('firstPayment_wgt'  =>  $pay['metal_weight'] );
					}else{
						$fixPayable = array('firstPayment_amt'  =>  $pay['payment_amount'] );
					}
					$status = $this->payment_modal->update_account($fixPayable,$pay['id_scheme_account']);	 
				}		
				
		
			
			
				// Generate receipt number
				if($pay['receipt_no_set'] == 1 && empty($pay['receipt_no']))
				{  
				
					$data['receipt_no'] = $this->generate_receipt_no($pay['id_scheme'],$pay['branch']);
					
					$data['payment_status'] = $this->payment_status['success'];
					$payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'],$data);
				}
				
				// Generate account  number  based on one more settings Integ Auto//hh
				if($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set']==3)
				{
					if(empty($pay['scheme_acc_number']))
					{
						$scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'],$pay['branch'],'');
						
					//	print_r($this->db->last_query());exit;
						if($scheme_acc_number != NULL && $scheme_acc_number !="Not Allocated"){
						$schData['scheme_acc_number'] = $scheme_acc_number;
					}
					if($pay['gent_clientid'] ==1 && $scheme_acc_number != NULL){  
							$cliData = array(
											 "cliID_short_code"	=> $this->config->item('cliIDcode'),
											 "sync_scheme_code"	=> $pay['sync_scheme_code'],
											 "ac_no"			=> $scheme_acc_number
											);											
							$updateData['ref_no'] = $this->payment_modal->generateClientID($cliData);
					}
					if($pay['receipt_no_set'] == 1 && $this->config->item('integrationType') == 2){ 		//upd client id & acc no cus reg table//
							$cliData = array(
											 "cliID_short_code"	=> $this->config->item('cliIDcode'),
											 "sync_scheme_code"	=> $pay['sync_scheme_code'],
											 "ac_no"			=> $scheme_acc_number
											);											
							$schDatacus['ref_no'] = $this->payment_modal->generateClientID($cliData);
							$schDatacus['scheme_acc_number'] = $scheme_acc_number;
						//	$schDatacus['group_code'] =$pay['group_code'];
					}
					$updSchAc = $this->payment_modal->update_account($schData,$pay['id_scheme_account']);
					 //$updcusreg = $this->payment_modal->update_cusreg($schDatacus,$pay['id_scheme_account']); //acc no upd to cus reg tab//HH
					 //$updtrans = $this->payment_modal->update_trans($schDatacus,$pay['id_scheme_account']); //Client Id upd to trans tab//
					}
				}
					
					
				/*Agent and employee incentive starts
				Coded By Haritha 15-9-22
				*/
				
				//agent benefits credit
                if($pay['agent_refferal'] == 1 && $paymtData['login_type'] == 'AGENT')
                {
                    $type=2; //1- employee 2- agent
                    $agent_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'],$pay['id_scheme_account'],$type,$pay['id_payment']);
		          
		            if($agent_refral > 0)
		            {
		                foreach($agent_refral as $ag)
		                {
		                    
        			 	    if($ag['referal_amount'] > 0)
        			 	    {
        			 	        $res = $this->insertAgentIncentive($ag,$pay['id_scheme_account'],$pay['id_payment'],$paymtData['id_agent']);
    			 	         }
		                }
		            }
                }
                //employee benefits credit
                if($pay['emp_refferal'] == 1 && $paymtData['login_type'] == 'EMP')
                {
                    $type=1; //1- employee 2- agent
                    $emp_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'],$pay['id_scheme_account'],$type,$pay['id_payment']);
                    //print_r($emp_refral);exit;
		            if($emp_refral > 0)
		            {
		                //$res = $this->insertEmployeeIncentive($emp_refral,$generic['id_scheme_account'],$status['insertID']);
		                foreach($emp_refral as $emp)
		                {
		                   
        			 	    if($emp['referal_amount'] > 0)
        			 	    {
        			 	        $res = $this->insertEmployeeIncentive($emp,$pay['id_scheme_account'],$pay['id_payment'],$paymtData['id_employee']);
        			 	        if($emp['credit_for'] == 1)
        			 	        {
        			 	            $this->customerIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
        			 	        }
    			 	         }
		                }
		            }
                }
				//ends   
				
				// Insert Data in Intermediate table
				 if($approval_type == 2 || $approval_type == 3 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3))
				 {
					if($this->config->item('integrationType') == 1){
						$this->insert_common_data_jil($pay['id_payment']);
					}else if($this->config->item('integrationType') == 2){
						$this->mobileapi_model->insert_common_data($pay['id_payment']);
					}
				 }  
 
			}   //foreach ends
			
			
		} 
		
	
                    
		if($this->db->trans_status())
	    {	
	    	$serv_model= self::SERV_MODEL;
	    	foreach ($pay_ids as $pay_id)
			{
    	    	if($service['sms'] == 1)
    	    	{
    	    		//$id=$payment['id_payment'];
    	    		$data =$this->$serv_model->get_SMS_data($serviceID,$pay_id);
    	    		$mobile =$data['mobile'];
    	    		$message = $data['message'];
    	    		if($this->config->item('sms_gateway') == '1'){
		    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
		    		}
		    		elseif($this->config->item('sms_gateway') == '2'){
		    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
		    		}
		    		elseif($this->config->item('sms_gateway') == '3'){
					    $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);
    				}
    				elseif($this->config->item('sms_gateway') == '4'){
    					$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);
    				}
    				elseif($this->config->item('sms_gateway') == '5'){
    					$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);
    				}
    	    	}
    	    	if($service['serv_whatsapp'] == 1)
        		{
        		    $smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $params];
                    $this->sms_model->send_whatsApp_message($mobile,$smsData);
                }
    	    	if($service['email'] == 1 && isset($pay['email']) && $pay['email'] != '')
    	    	{ 
    	    		//$invoiceData = $this->payment_modal->get_invoiceDataM(isset($payment['id_payment'])?$payment['id_payment']:'');
    	    		$invoiceData = $this->payment_modal->get_invoiceDataM($pay_id);
    	    		$to = $pay['email'];
    	    		$subject = "Reg - ".$this->comp['company_name']." payment for the purchase plan";
    	    		$data['payData'] = $invoiceData[0];
    	    		$data['type'] = 2;
    	    		$data['company_details'] = $this->comp;
    	    		$message = $this->load->view('include/emailPayment',$data,true);
    	    		$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");	
    	    	}	
			}	
		 	$this->db->trans_commit();
    		$result = $this->adminapp('success',$paymtData['payAmount']);
    		$this->response($result,200);
    		
    	
    	}else{
    	  
		 	$this->db->trans_rollback();
		 	$result = $this->adminapp('failed',$paymtData['payAmount']);
		 	$this->response($result,200);
		}
	}
    function adminapp($type,$amount){
        
        if($type == 'success'){
            $result = array("status" => TRUE, "title" => "Transaction ".$type, "msg" => "Your Payment amount of INR ".$amount." paid successfully. Thanks for your payment with us."); 
     	}else if($type == 'failed'){
            $result = array("status" => FALSE, "title" => "Transaction ".$type, "msg" => "Your Payment amount of INR ".$amount." has been failed. "); 
     	}else{
     	    $result = array("status" => FALSE, "title" => "Transaction ".$type, "msg" => "Your Payment amount of INR ".$amount." has been cancelled."); 
     	}
     	
     	
     	return $result;
    }
	
   
	
	/*easbuzz functions - by haritha */
   function _payment($params, $redirect, $merchant_key, $salt, $env){
        $postedArray = '';
        $URL = '';
        // argument validation
        $argument_validation = $this->_checkArgumentValidation($params, $merchant_key, $salt, $env);
        if (is_array($argument_validation) && $argument_validation['status'] === 0) {
            return $argument_validation;
        }
        // push merchant key into $params array.
        $params['key'] =  $merchant_key;
        // remove white space, htmlentities(converts characters to HTML entities), prepared $postedArray.
        $postedArray = $this->_removeSpaceAndPreparePostArray($params);
        // empty validation
        $empty_validation = $this->_emptyValidation($postedArray, $salt);
        if (is_array($empty_validation) && $empty_validation['status'] === 0) {
            return $empty_validation;
        }
        // check amount should be float or not 
        if (preg_match("/^([\d]+)\.([\d]?[\d])$/", $postedArray['amount'])) {
            $postedArray['amount'] = (float) $postedArray['amount'];
        }
        // type validation
        $type_validation = $this->_typeValidation($postedArray, $salt, $env);
        if ($type_validation !== true) {
            return $type_validation;
        }
        // again amount convert into string
        $diff_amount_string = abs(strlen($params['amount']) - strlen("" . $postedArray['amount'] . ""));
        $diff_amount_string = ($diff_amount_string === 2) ? 1 : 2;
        $postedArray['amount'] = sprintf("%." . $diff_amount_string . "f", $postedArray['amount']);
        // email validation
        $email_validation = $this->_email_validation($postedArray['email']);
        if ($email_validation !== true)
            return $email_validation;
        // get URL based on enviroment like ($env = 'test' or $env = 'prod')
        $URL = $this->_getURL($env);
        // process to start pay
        $pay_result = $this->_pay($postedArray, $redirect, $salt, $URL);
        return $pay_result;
    }
	    function _pay($params_array, $redirect, $salt_key, $url){
        $hash_key = '';
        // generate hash key and push into params array.
        $hash_key = $this->_getHashKey($params_array, $salt_key);
        $params_array['hash'] = $hash_key;
        // call curl_call() for initiate pay link
        $curl_result = $this->_curlCall($url . 'payment/initiateLink', http_build_query($params_array));
        //  print_r($curl_result);
        //  die;
        $accesskey = ($curl_result->status === 1) ? $curl_result->data : null;
        if (empty($accesskey)) {
            return $curl_result;
        } else {
            if ($redirect == true) {
                $curl_result->data = $url . 'pay/' . $accesskey;
            } else {
                $curl_result->data = $accesskey;
                // return $accesskey;
            }
            return $curl_result;
        }
    }
    function _curlCall($url, $params_array){
        // Initializes a new session and return a cURL.
        $cURL = curl_init();
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        // Set multiple options for a cURL transfer.
        curl_setopt_array(
            $cURL,
            array(
                CURLOPT_URL => $url,
                CURLOPT_POSTFIELDS => $params_array,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36',
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            )
        );
        // Perform a cURL session
        $result = curl_exec($cURL);
        // check there is any error or not in curl execution.
        if (curl_errno($cURL)) {
            $cURL_error = curl_error($cURL);
            if (empty($cURL_error))
                $cURL_error = 'Server Error';
            return array(
                'curl_status' => 0,
                'error' => $cURL_error
            );
        }
        $result = trim($result);
        $result_response = json_decode($result);
        return $result_response;
    }
    function _getHashKey($posted, $salt_key){
        $hash_sequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
        // make an array or split into array base on pipe sign.
        $hash_sequence_array = explode('|', $hash_sequence);
        $hash = null;
        // prepare a string based on hash sequence from the $params array.
        foreach ($hash_sequence_array as $value) {
            $hash .= isset($posted[$value]) ? $posted[$value] : '';
            $hash .= '|';
        }
        $hash .= $salt_key;
        #echo $hash;
        #echo " ";
        #echo strtolower(hash('sha512', $hash));
        // generate hash key using hash function(predefine) and return
        return strtolower(hash('sha512', $hash));
    }
    function _getURL($env){
        $url_link = '';
        switch ($env) {
            case 'test':
                $url_link = "https://testpay.easebuzz.in/";
                break;
            case 'prod':
                $url_link = 'https://pay.easebuzz.in/';
                break;
            case 'local':
                $url_link = 'http://localhost:8005/';
                break;
            case 'dev':
                $url_link = 'https://devpay.easebuzz.in/';
                break;
            default:
                $url_link = "https://testpay.easebuzz.in/";
        }
        return $url_link;
    }
    function _checkArgumentValidation($params, $merchant_key, $salt, $env){
        $args = func_get_args();
        $argsc = count($args);
        if ($argsc !== 4) {
            return array(
                'status' => 0,
                'data' => 'Invalid number of arguments.'
            );
        }
        return 1;
    }
    function _email_validation($email){
        $email_regx = "/^([\w\.-]+)@([\w-]+)\.([\w]{2,8})(\.[\w]{2,8})?$/";
        if (!preg_match($email_regx, $email)) {
            return array(
                'status' => 0,
                'data' => 'Email invalid, Please enter valid email.'
            );
        }
        return true;
    }
    function _removeSpaceAndPreparePostArray($params){
        $temp_array = array();
        foreach ($params as $key => $value) {            
                if (array_key_exists($key, $params)  and  !empty($key) ){
                    if($key != "split_payments"){
                        $temp_array[$key] = trim(htmlentities($value, ENT_QUOTES));
                     }else{
                        $temp_array[$key] = trim($value);
                     }
                }            
        }        
        return $temp_array;
    }
    function _emptyValidation($params, $salt){
        $empty_value = false;
        if (empty($params['key']))
            $empty_value = 'Merchant Key';
        if (empty($params['txnid']))
            $empty_value = 'Transaction ID';
        if (empty($params['amount']))
            $empty_value = 'Amount';
        if (empty($params['firstname']))
            $empty_value = 'First Name';
        if (empty($params['email']))
            $empty_value = 'Email';
        if (empty($params['phone']))
            $empty_value = 'Phone';
        if (!empty($params['phone'])){
            if (strlen((string)$params['phone'])!=10){
                $empty_value = 'Phone number must be 10 digit and ';
            }
        }
        if (empty($params['productinfo']))
            $empty_value = 'Product Infomation';
        if (empty($params['surl']))
            $empty_value = 'Success URL';
        if (empty($params['furl']))
            $empty_value = 'Failure URL';
        if (empty($salt))
            $empty_value = 'Merchant Salt Key';
        if ($empty_value !== false) {
            return array(
                'status' => 0,
                'data' => 'Mandatory Parameter ' . $empty_value . ' can not empty'
            );
        }
        return true;
    }
    function _typeValidation($params, $salt, $env){
        $type_value = false;
        if (!is_string($params['key']))
            $type_value = "Merchant Key should be string";
        if (!is_float($params['amount']))
            $type_value = "The amount should float up to two or one decimal.";
        if (!is_string($params['productinfo']))
            $type_value =  "Product Information should be string";
        if (!is_string($params['firstname']))
            $type_value =  "First Name should be string";
        if (!is_string($params['phone']))
            $type_value = "Phone Number should be number";
        if (!is_string($params['email']))
            $type_value = "Email should be string";
        if (!is_string($params['surl']))
            $type_value = "Success URL should be string";
        if (!is_string($params['furl']))
            $type_value = "Failure URL should be string";
        if ($type_value !== false) {
            return array(
                'status' => 0,
                'data' => $type_value
            );
        }
        return true;
    }
   	function easebuzzResponse_post()
	{
	    /* {"status":1,"url":"http:\/\/localhost\/paywitheasebuzz-php-lib-master\/response.php","data":{"name_on_card":"Test","bank_ref_num":"NA","udf3":"",
	    "hash":"18597796bb6944d8a43de378b23a443b1fe38b376f006693e3b0b95d692fb05c5ced14e24d6eb8f00a73605f54520ca1c23ac6be03d860c3ca3b56c3aae70bf3","firstname":"Haritha",
	    "net_amount_debit":"43.0","payment_source":"Easebuzz","surl":"http:\/\/localhost\/paywitheasebuzz-php-lib-master\/response.php",
	    "error_Message":"ResponseCode : BNF (Bin not found)","issuing_bank":"NA","cardCategory":"NA","phone":"9025384947","easepayid":"E230512TQYBDMR",
	    "cardnum":"XXXXXXXXXXXX1111","key":"ZJFVJEL714","udf8":"","unmappedstatus":"NA","PG_TYPE":"NA","addedon":"2023-05-12 07:19:19","cash_back_percentage":"50.0",
	    "status":"failure","card_type":"Credit Card","merchant_logo":"NA","udf6":"","udf10":"","upi_va":"NA","txnid":"333","productinfo":"Laptop","bank_name":"NA",
	    "furl":"http:\/\/localhost\/paywitheasebuzz-php-lib-master\/response.php","udf1":"","amount":"43.0","udf2":"","udf5":"","mode":"CC","udf7":"","error":"ResponseCode : BNF (Bin not found)","udf9":"","bankcode":"NA","deduction_percentage":"1.95","email":"haritha@vikashinfosolutions.com","udf4":""}}*/
        $postData = $this->get_values();     //print_r($postData);exit;
        $trans_id      = $postData['response']->txnid;
        $txStatus      = $postData['response']->status; 
        $orderAmount   = isset($postData['response']->amount) ? $postData['response']->amount : NULL;
        $referenceId   = isset($postData['response']->easepayid) ? $postData['response']->easepayid : NULL;
        $paymentMode   = $postData['response']->mode;
        $txMsg         = "Online Money Transaction";
        $txTime        =  NULL;
        // $computedSignature = base64_encode($hash_hmac); 
        // Write log 
        if (!is_dir($this->log_dir.'/easebuzz')) {
            mkdir($this->log_dir.'/easebuzz', 0777, true);
        }
        $log_path = $this->log_dir.'/easebuzz/mob_response_'.date("Y-m-d").'.txt';
        $ldata = "\n".date('d-m-Y H:i:s')." \n Mobile : ".json_encode($postData,true);
        file_put_contents($log_path,$ldata,FILE_APPEND | LOCK_EX);
        if(!empty($trans_id) && $trans_id != NULL)
        {
    	    $updateData = array( 
    						"payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":($paymentMode == "NA" ? "-":$paymentMode)))),
    						"payu_id"            => $referenceId,
    						"remark"             => $txMsg."[".$txTime."] mbl-status",
    						"payment_ref_number" => $referenceId,
    						"payment_status"     => ($txStatus==="success" ? ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']: $this->payment_status['awaiting']) : ($txStatus==="userCancelled" ? $this->payment_status['cancel'] :($txStatus==="failure" ? $this->payment_status['failure'] : $this->payment_status['pending']) ))
    					    ); 
			$payment = $this->payment_modal->updateGatewayResponse($updateData,$trans_id);   
    		if($payment['status'] == true)
    		{
    		    $serviceID = 0;
    		    $pay_msg = "Your payment has been ".$txStatus;
        		if($txStatus==="success")
        		{
        		    $serviceID = 3; 
        		    $type = 2;
        		    $pay_msg = 'Transaction ID: '.$trans_id.'. Payment amount INR. '.$orderAmount.' is paid successfully. Thanks for your payment with '.$this->comp['company_name'];
        	    }
        		else if($txStatus === "failure")
        		{ 
        		    $serviceID = 7; 
        		    $type = -1;
        		    $pay_msg = 'Your payment has been failed. Please try again. Transaction ID: '.$trans_id;
        		}
        		else if($txStatus === "userCancelled")
        		{ 
        		    $serviceID = 7; 
        		    $type = 3;
        		    $pay_msg = 'Your payment has been cancelled.';
        		}
        		if($serviceID > 0){
        		    if($txStatus==="success")
        		    {   
        		        $payIds = $this->payment_modal->getPayIds($trans_id);
    					if(sizeof($payIds) > 0)
    					{
    						foreach ($payIds as $pay)
    						{
    						    // Multi mode payment
    						    if($updateData['payment_mode']!= NULL)
                 				{
                 					$arrayPayMode=array(
                    								'id_payment'         => $pay['id_payment'],
                							        'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                    								'payment_date'		 => date("Y-m-d H:i:s"),
                    								'created_time'	     => date("Y-m-d H:i:s"),
                    								"payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))),
                            						"remark"             => $txMsg."[".$txTime."] mbl-status",
                            						"payment_ref_number" => $referenceId,
                            						"payment_status"     => 1
                            					    );
                					if(!empty($arrayPayMode)){
                						$cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details'); 
                					}
                 				}
    						    $schData = [];
    						    $cusRegData = [];
    						    $transData = [];
                			 	//13-09-2022 Coded by haritha 
                			 	//employee incentive credits based on installment settings
                                if($pay['referal_code']!='' && $pay['emp_refferal'] == 1)
                                {
                                    $type=1; //1- employee 2- agent
                                    $emp_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'],$pay['id_scheme_account'],$type,$pay['id_payment']);
                		            if($emp_refral > 0)
                		            {
                		                $res = $this->insertEmployeeIncentive($emp_refral,$pay['id_scheme_account'],$pay['id_payment']);
                		                foreach($emp_refral as $emp)
                		                {
                        			 	    if($emp['referal_amount'] > 0)
                        			 	    {
                        			 	        //$res = $this->insertEmployeeIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
                        			 	        if($emp['credit_for'] == 1)
                        			 	        {
                        			 	            $this->customerIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
                        			 	        }
                    			 	         }
                		                }
                		            }
                                }
    							// Generate account  number  
    							if($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set']==3)
    							{
    								if($pay['scheme_acc_number'] == '' ||  $pay['scheme_acc_number'] == null || $pay['scheme_acc_number'] == 0)
    								{
    								    $ac_group_code = NULL;
    									// Lucky draw
    									if($pay['is_lucky_draw'] == 1 ){ // Based on scheme settings 
    										// Update Group code in scheme_account table 
    										$updCode = $this->payment_modal->updateGroupCode($pay); 
    										$ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
    									}
    									// $this->db->query('LOCK TABLES scheme_account WRITE');
    									$scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'],$pay['branch'],$ac_group_code); 
    									if($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0){
    										$schData['scheme_acc_number'] = $scheme_acc_number;
    										$cusRegData['scheme_acc_number'] = $scheme_acc_number;
    									}else{
											// $this->db->query('UNLOCK TABLES');
										}
    								}
    							}
        						// Generate receipt number
    							if($pay['receipt_no_set'] == 1 && ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2) )
    							{ 
    								// $this->db->query('LOCK TABLES payment WRITE');
    								$receipt['receipt_no'] = $this->payment_modal->generate_receipt_no($pay['id_scheme'],$pay['branch']);
    								$payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'],$receipt);
    								// $this->db->query('UNLOCK TABLES');
    							}
    							if($pay['id_scheme_account'] > 0){
        							if(sizeof($schData) > 0){ // Update scheme account
        								$this->payment_modal->update_account($schData,$pay['id_scheme_account']);
        								// $this->db->query('UNLOCK TABLES');
        							}
        							if(sizeof($cusRegData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)){ 
                                        $this->payment_modal->update_cusreg($cusRegData,$pay['id_scheme_account']); // Update Customer reg - Client ID, Ac No
        							}
        							if(sizeof($transData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)){ 
                                        $this->payment_modal->update_trans($transData,$pay['id_scheme_account']); // Update Transaction - Client ID
        							}
    							}
    							//Update First Payment Amount In Scheme Account
    							$approval_type = $this->config->item('auto_pay_approval');
        						if(($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable']==1 || $pay['firstPayamt_as_payamt']==1) && ($pay['firstPayment_amt'] == '' ||  $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0))
        						{
        							if($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "") ){ // Fix First payable as weight
    									$fixPayable = array('firstPayment_wgt'  =>  $pay['metal_weight'] );
    								}else{
    									$fixPayable = array('firstPayment_amt'  =>  $pay['payment_amount'] );
    								}
    								$status = $this->payment_modal->update_account($fixPayable,$pay['id_scheme_account']);	 
        						}
    							if( $this->config->item('auto_pay_approval') == 2 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3))
    							{ 	 
                                    if($this->config->item('integrationType') == 1){
                                        $this->insert_common_data_jil($pay['id_payment']);
                                    }else if($this->config->item('integrationType') == 2){
                                        $this->mobileapi_model->insert_common_data($pay['id_payment']);
                                    }  		            
        		        		}
        		        		
        		        		 $service = $this->services_modal->checkService($serviceID); 
                    			if($service['sms'] == 1)
                    			{
                    				$id=$pay['id_payment'];
                    				$data =$this->services_modal->get_SMS_data($serviceID,$id);
                    				$mobile =$data['mobile'];
                    				$message = $data['message'];
                    				$this->mobileapi_model->send_sms($mobile,$message,'',$service['dlt_te_id']);
                    			}
    						}
    					}
        		    }
        		   
        		}  
			    //redirect('paymt/hdfcTransStatus/success/'.$resData['txnid'].'/'.$resData['amount']);
        	    $response = array("status" => TRUE, "title" => "Transaction ".$txStatus, "msg" => $pay_msg);
            }else{ 
                //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
                $response = array("status" => TRUE, "title" => "Transaction ".$txStatus, "msg" => "Unable to proceed your payment.."); 
            } 
        }else{ 
            //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
            $response = array("status" => TRUE, "title" => "Transaction ".$txStatus, "msg" => "Your payment has been Cancelled");
        }
        $this->response($response,200); 
	}
//ends

	
	// Cashfree SDK API's :: START
	function random_strings($length)
    {
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle($str_result), 0, $length);
    }
    function mobile_payment_get()
	{ 
	 
	   $payData = array(
	                    'id_employee'   => $this->get('id_employee'),
    	                'firstname'     => $this->get('firstname'), 
                        'lastname'      => $this->get('lastname'),
                        'phone'         => $this->get('phone'),
                        'id_branch'     => $this->get('id_branch'),
                        'amount'        => $this->get('amount'),
                        'redeemed_amount' => $this->get('redeemed_amount'),
                        'productinfo'   => $this->get('productinfo'),
                        'email'         => $this->get('email'),
                        'gateway'       => $this->get('gateway'),
                        'pg_code'       => $this->get('pg_code'),
                        'pay_arr'       => $this->get('pay_arr'),
                        'pay_mode'      => $this->get('pay_mode'),
                        'login_type'    => $this->get('login_type')
	                );            
	   if(!empty($payData['phone']))
	   {
		   //get the values posted from mobile in array 
		   $pay_flag = TRUE;
		   $allow_flag = FALSE;
		   $submit_pay = FALSE;
		   $actAmount = (float) $payData['amount'];  // sum of all chit amount
		   $cusData = $this->payment_modal->get_customer($payData['phone']);//print_r($cusData);exit;
		   $gateway = ( isset($payData['gateway']) ? ($payData['gateway'] == 'undefined' ? 1 : $payData['gateway']) :1 );
		   $paycred = $this->payment_gateway($cusData['id_branch'],$payData['gateway']);
		   $start_year = $this->payment_modal->get_financialYear();
		   //check pay_flag
		   if($pay_flag )
		   {
				//generate txnid
                 $txnid = uniqid(time());
				 $i=1;
				 $sch_payment = json_decode($payData['pay_arr']);
				 $udf1= "";
				 $udf2= "";
				 $udf3= "";
				 $productinfo= "";
				// $this->db->trans_begin();	
				foreach ($sch_payment as $pay){	
				   if($cusData['branch_settings']==1)
				   {
					    if($cusData['is_branchwise_cus_reg']==1)
						{
							$id_branch  = $cusData['id_branch'];
						}
						else
						{
							$id_branch  = $pay->id_branch;
							//$id_branch  = $branch['sch_join_branch'];
						}
					}
					else{
						$id_branch = NULL;
					} 
				    //validate amount
				   $chit = $this->payment_modal->get_schemeByChit($pay->udf1);
				   if( $pay->scheme_type == 1)
				   {						   
						  $metal_rate = $this->payment_modal->getMetalRate('');	
						  $gold_rate = (float) $metal_rate['goldrate_22ct'];
						  $amt = $gold_rate * $pay->udf2;
						  $allow_flag =  ($pay->amount >= $amt? TRUE :FALSE);
				   }
				   else
				   {
						$allow_flag =  ($pay->amount >= $chit['amount']? TRUE :FALSE);
				   }
				   if($pay->scheme_type == 2 || (($pay->scheme_type == 3 && $chit['flexible_sch_type'] != 1 )&& $chit['wgt_convert']==0)){
				   	    $data = array('metal_rate'=>$pay->udf3,'amount'=>$pay->udf4);
						$metal_wgt = $this->amount_to_weight($data);
				   }
				   else{	
						$metal_wgt = (isset($pay->udf2) && $pay->udf2 !='' ? $pay->udf2 : 0.000);
				   }
				   
				   //rate fixed at the time of scheme join
			if($chit['one_time_premium']==1 && $chit['rate_fix_by'] == 0 && $chit['rate_select'] == 1)
			{
			    $metal_rate = $this->payment_modal->getMetalRate('');	
				$gold_rate = (float) $metal_rate['goldrate_22ct'];
			    if($gold_rate != 0)
			    {
			        
			        $isRateFixed = $this->mobileapi_model->isRateFixed($pay->udf1);
            			    if($isRateFixed['status'] == 0){
                		        $updData = array(
            	   							"fixed_wgt" => $pay->amount/$gold_rate,
            	   							"firstPayment_amt" => $pay->amount,
            	   							"fixed_metal_rate" => $gold_rate,
            	   							"rate_fixed_in" => 1,
            	   							"fixed_rate_on" => date("Y-m-d H:i:s")
            	   						); 
            	   					//print_r($updData);exit; 
            	   				$ratestatus = $this->mobileapi_model->updFixedRate($updData,$pay->udf1);
            			    }else{
            			        $data = array('is_valid' => TRUE,'success' => TRUE, 'msg' => "Rate already fixed !!");
            			    }
			    }
			}
				   
				   if($allow_flag){
				       $custom_entry_date = $this->payment_modal->get_entrydate($id_branch);
						//set insert data					
							$insertData = array(
									"id_scheme_account"	 => (isset($pay->udf1)? $pay->udf1 : NULL ),
									"payment_amount" 	 => (isset($pay->amount)? $pay->amount+($pay->discount==""?0.00:$pay->discount) : NULL ), 
									"gst" 	    		 => (isset( $chit['gst'])? $chit['gst'] : 0 ),
									"gst_type" 	    	 => (isset($chit['gst_type'])? $chit['gst_type'] : 0 ),
									"payment_type" 	     => (isset($paycred) ? ($paycred['pg_name']) : ''),
									"due_type" 	   		 => $pay->due_type,
									"no_of_dues" 	     => 1,
									"actual_trans_amt"      => (isset($actAmount) ? $actAmount : 0.00),
									"date_payment" 		 =>  date('Y-m-d H:i:s'),
									"metal_rate"         => (isset($pay->udf3) && $pay->udf3 !='' ? $pay->udf3 : 0.00),
									"metal_weight"       =>  $metal_wgt,
									"id_transaction"     => (isset($txnid) ? $txnid.'-'.$i : NULL),
									"ref_trans_id"       => (isset($txnid) ? $txnid : NULL),// to update pay status after trans complete.
									"remark"             =>  'Paid for '.$pay->udf5.($pay->udf5 >1?'months':'month'),
									"added_by"			 =>  2,
									"add_charges" 	     => (isset($pay->charge) ?$pay->charge : NULL), 
									"discountAmt"        => ($pay->discount!="" ? $pay->discount : 0.00),
									"payment_status"     => $this->payment_status['pending'],
									"id_payGateway"      => (isset($gateway) ? $gateway: 1),
									'id_branch'          => $id_branch,
									"payment_mode"       => (isset($payData['pay_mode']) ?$payData['pay_mode'] : NULL),
							 		"id_employee"        => (isset($payData['id_employee']) && $payData['login_type'] == 'EMP' ?$payData['id_employee'] :NULL),
							 		"id_agent"        => (isset($payData['id_employee']) && $payData['login_type'] == 'AGENT' ?$payData['id_employee'] :NULL),
							// 	    "is_point_credited"   => 1
							        "receipt_year"            => $start_year,
							         "card_no"        => (isset($payData['refernceNo']) ?$payData['refernceNo'] : NULL),
							         'custom_entry_date'   =>  ($custom_entry_date['edit_custom_entry_date']==1 ? $custom_entry_date['custom_entry_date']:NULL),
									//status - 0 (pending), will change to 1 after approved at backend
								);  
						$udf1 = $udf1." ".$pay->udf1;
						$udf2 = $udf2." ".$pay->udf2;
						$udf3 = $udf3." ".$pay->udf3;
						$productinfo = $productinfo." ".$pay->chit_number;
						//inserting pay_data before gateway process
						/*echo "<pre>";print_r($insertData);echo "</pre>";	*/
						
						if($this->config->item("integrationType") == 5){ // Generate tranUniqueId
						   $chit['reference_no'] = $cusData['reference_no'];
						   $chit['nominee_address1'] = $cusData['nominee_address1'];
						   $chit['nominee_address2'] = $cusData['nominee_address2'];
						   $chit['nominee_name'] = $cusData['nominee_name'];
						   $chit['nominee_mobile'] = $cusData['nominee_mobile'];
						   $chit['nominee_relationship'] = $cusData['nominee_relationship'];
						   //$chit['referal_code'] = $cusData['id_employee'];
						   
                           $tranUniqueId = $this->generateTranUniqueId($chit,$insertData['payment_amount']);
                           if($tranUniqueId['status'] == false){
                                //$result['resData'] = $tranUniqueId['resData'];
                                $result['status'] = false;
                                $result['message'] = isset($tranUniqueId['resData']->errorMsg) ? $tranUniqueId['resData']->errorMsg :'Unable to proceed payment. Kindly contact customer support...';
                                $this->response($result,200); 
                           }
                           $insertData['offline_tran_uniqueid'] = $tranUniqueId['tranUniqueId'];
                        }
						
						$payment = $this->payment_modal->addPayment($insertData);	
						$i++;
					}
				}
				if($this->db->trans_status()=== TRUE)
	            {
				 	$this->db->trans_commit();
				 	$submit_pay = TRUE;
				}
				else{
				 	$this->db->trans_rollback();
					$submit_pay = FALSE;
				}
				if($submit_pay)
				{ 
				    $updData = array("last_payment_on" => date("Y-m-d H:i:s"));
			        $this->payment_modal->updData($updData,'id_customer',$cusData['id_customer'],'customer');
                    $secretKey = $paycred['param_1']; //Shared by Cashfree  
                    $appId     = $paycred['param_3'];
					//set data for hash generation
					$data['pay'] =	array (
						'key' 			=> $secretKey, 
						'txnid' 		=> $txnid, 
						'amount' 		=> (isset($payData['amount'])   ? $payData['amount']:''),
						'productinfo'	=> (isset($productinfo)    ? $productinfo :''),
						'firstname' 	=> (isset($cusData['firstname'])? $cusData['firstname'] :''),
						'lastname' 		=> (isset($cusData['lastname']) ? $cusData['lastname']:''),
						'email' 		=> (isset($cusData['email'])    ? $cusData['email']:''), 
						'phone' 		=> (isset($cusData['mobile'])    ? $cusData['mobile'] :''),
						'address1'		=> (isset($cusData['address1']) ? $cusData['address1']:''), 
						'address2'		=> (isset($cusData['address2']) ? $cusData['address2'] :''), 
						'city'			=> (isset($cusData['city']) ? $cusData['city'] :''), 
						'state'			=> (isset($cusData['state']) ? $cusData['state'] : ''), 
						'country'		=> (isset($cusData['country']) ? $cusData['country'] : ''), 
						'zipcode'		=> (isset($cusData['pincode']) ? $cusData['pincode'] :''), 
						'udf1' 			=> (isset($udf1)    ? $udf1 :''),
						'udf2' 			=> (isset($udf2)    ? $udf2 :''),
						'udf3'			=> (isset($udf3)    ? $udf3 :''),
						'udf4' 			=> (isset($payData['udf4'])    ? $payData['udf4'] : ''),
						'udf5' 			=> (isset($payData['udf5'])? $payData['udf5']:'') 
					);
					if($payData['pg_code'] == 4){   // cash free 
    					$gen_email =  $this->random_strings(8).'@gmail.com';  
                        $data['cashfreepay'] =	array (
                                                    'appId'         => $appId,  //Shared by Cashfree
                                                    'orderId'       => $txnid,
                                                    'orderAmount' 	=> (string) $actAmount, 
                                                    'orderCurrency'	=> 'INR',
                                                    'orderNote'	    => 'Purchase plan payment from mobile app',
                                                    'customerName' 	=> (isset($payData['firstname'])? (isset($payData['lastname']) ? $payData['firstname'].' '.$payData['lastname']:$payData['firstname']) :''),
                                                    'customerEmail' => !empty($cusData['email']) ? $cusData['email'] : $gen_email,
                                                    'customerPhone' => (isset($payData['phone'])    ? $payData['phone'] :''),
                                					"notifyUrl"     => $this->config->item('base_url')."index.php/services/cashfreeStatusNotify/".$payData['gateway'],
                                                    "stage"         =>  $paycred['type'] == 0 ? "TEST" : 'PROD',//"TEST/PROD",
                                                    "login_type"    => $payData['login_type'],
                                                    "id_employee"   => $payData['id_employee']
                                                ); 
                        // Write log 
                        /*$log_path = 'log/cashfree/mobiletest'.date("Y-m-d").'.txt';
                        $ldata = "\n".date('d-m-Y H:i:s')." \n Mobile : ".json_encode($data['cashfreepay'],true);
                        file_put_contents($log_path,$ldata,FILE_APPEND | LOCK_EX);*/
                        $token = $this->generateCFtoken($txnid,$actAmount,$data['cashfreepay'],$paycred); 
                		$this->response($token,200);
				    } 
		        }
		   }
	    }else{
	        $result['status'] = false;
            $result['message'] = 'Invalid payment request...';
	        $this->response($result,200); 
	    }
	}
	function generateCFtoken($txnid, $orderAmount, $params,$paycred){
	    $secretKey = $paycred['param_1']; //Shared by Cashfree  
        $appId     = $paycred['param_3'];
    	$postData  = array(
						  "orderId" 		=> $txnid,
						  "orderAmount"		=> $orderAmount,
						  "orderCurrency"	=> "INR"
						);
		$curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $paycred['api_url'].'api/v2/cftoken/order',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "Content-Type: application/json",
                "x-client-id: ".$appId,
                "x-client-secret: ".$secretKey
            ),
        ));       
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            //echo "cURL Error #:" . $err;
            $result = array('status' => false, "message" => 'Sorry for inconvenience, we are unable to proceed your request. Kindly contact customer care..');
            return $result;
        } 
        else {  
            $tokenData = json_decode($response);
            if($tokenData->status == 'OK'){
                $gen_email =  $this->random_strings(8).'@gmail.com'; 
                $result['status'] = true;
                $result['params'] = $params;
                $result['params']['tokenData'] = $tokenData->cftoken;
            }
            else if($tokenData->status == 'ERROR'){
                $result['status'] = false;
                $result['message'] = 'Sorry for inconvenience, we are unable to proceed your request. Kindly contact customer care...';
            } 
            return $result;
        }
	}
	public function cf_payment_status_post()
    {
        /*{"paymentMode":"CREDIT_CARD","orderId":"16305780766130a59c7beaf","txTime":"2021-09-02 15:51:38","referenceId":"1043610","type":"CashFreeResponse","txMsg":"Transaction Successful","signature":"MT1j+VSkIjS5hg2zGKt\/xmZjHWoz70dKm4\/w8LK6qbg=","orderAmount":"1000.00","txStatus":"SUCCESS"}
        {"paymentMode":"CREDIT_CARD","orderId":"16305785416130a76de4d2a","txTime":"2021-09-02 15:59:17","referenceId":"1043658","type":"CashFreeResponse","txMsg":"Your transaction has failed.","signature":"QRgBU7lujaC0qc5SMoxPczO4qpWAI1zWJI6CKoinroI=","orderAmount":"1000.00","txStatus":"FAILED"}
        {"orderId":"16305784926130a73c56f7b","type":"CashFreeResponse","txStatus":"CANCELLED"}*/
        $postData = $this->get_values();
        $trans_id      = $postData["orderId"];
        $txStatus      = $postData["txStatus"]; 
        $orderAmount   = isset($postData["orderAmount"]) ? $postData["orderAmount"] : NULL;
        $referenceId   = isset($postData["referenceId"]) ? $postData["referenceId"] : NULL;
        $paymentMode   = isset($postData["paymentMode"]) ? $postData["paymentMode"] : NULL;
        $txMsg         = isset($postData["txMsg"]) ? $postData["txMsg"] : NULL;
        $txTime        = isset($postData["txTime"]) ? $postData["txTime"] : NULL;
        // $computedSignature = base64_encode($hash_hmac); 
        // Write log 
        $log_path = 'log/cashfree/mob_response_'.date("Y-m-d").'.txt';
        $ldata = "\n".date('d-m-Y H:i:s')." \n Mobile : ".json_encode($postData,true);
        file_put_contents($log_path,$ldata,FILE_APPEND | LOCK_EX);
        /* // Signature Verification
        $data = $orderId.$orderAmount.$referenceId.$txStatus.$paymentMode.$txMsg.$txTime;
        $hash_hmac = hash_hmac('sha256', $data, $secretkey, true) ;
        $computedSignature = base64_encode($hash_hmac);
        if ($signature == $computedSignature) {
            // Proceed
        } else {
            // Reject this call
        }
        */ 
        if(!empty($trans_id) && $trans_id != NULL)
        {
            $pay = $this->payment_modal->getWalletPaymentContent($trans_id); 
    	    $updateData = array( 
    						"payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))),
    						"payu_id"            => $referenceId,
    						"remark"             => $txMsg."[".$txTime."] mbl-status",
    						"payment_ref_number" => $referenceId,
    						"payment_status"     => ($txStatus==="SUCCESS" ? ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']: $this->payment_status['awaiting']) : ($txStatus==="CANCELLED" ? $this->payment_status['cancel'] :($txStatus==="FAILED" ? $this->payment_status['failure'] : $this->payment_status['pending']) ))
    					    ); 
			$payment = $this->payment_modal->updateGatewayResponse($updateData,$trans_id);   
    		if($payment['status'] == true)
    		{
    		    $serviceID = 0;
    		    $pay_msg = "Your payment has been ".$txStatus;
        		if($txStatus==="SUCCESS")
        		{
        		    $serviceID = 3; 
        		    $type = 2;
        		    $pay_msg = 'Transaction ID: '.$trans_id.'. Payment amount INR. '.$orderAmount.' is paid successfully. Thanks for your payment with '.$this->comp['company_name'];
        	    }
        		else if($txStatus === "FAILED")
        		{ 
        		    $serviceID = 7; 
        		    $type = -1;
        		    $pay_msg = 'Your payment has been failed. Please try again. Transaction ID: '.$trans_id;
        		}
        		else if($txStatus === "CANCELLED")
        		{ 
        		    $serviceID = 7; 
        		    $type = 3;
        		    $pay_msg = 'Your payment has been cancelled.';
        		}
        		if($serviceID > 0){
        		    if($txStatus==="SUCCESS")
        		    {   
        		        $payIds = $this->payment_modal->getPayIds($trans_id);
    					if(sizeof($payIds) > 0)
    					{
            		        $updData = array("last_payment_on" => NULL);
                            $this->payment_modal->updData($updData,'id_customer',$payIds[0]['id_customer'],'customer'); 
    						foreach ($payIds as $pay)
    						{
    						    
    						    if($this->config->item("integrationType") == 5){ // Generate A/C No and Receipt No
                					$this->generateAcNoOrReceiptNo($pay);
                				}
    						    
    						    if($updateData['payment_mode']!= NULL)
                 				{
                 					$arrayPayMode=array(
                    								'id_payment'         => $pay['id_payment'],
                							        'payment_amount'     => (isset($pay['payment_amount']) ? $pay['payment_amount'] : NULL),
                    								'payment_date'		 => date("Y-m-d H:i:s"),
                    								'created_time'	     => date("Y-m-d H:i:s"),
                    								"payment_mode"       => ($paymentMode== "CREDIT_CARD" ? "CC":($paymentMode == "DEBIT_CARD" ? "DC":($paymentMode == "NET_BANKING" ? "NB":$paymentMode))),
                            						"payu_id"            => $referenceId,
                            						"remark"             => $txMsg."[".$txTime."] mbl-status",
                            						"payment_ref_number" => $referenceId,
                            						"payment_status"     => ($txStatus==="SUCCESS" ? ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2 ? $this->payment_status['success']: $this->payment_status['awaiting']) : ($txStatus==="CANCELLED" ? $this->payment_status['cancel'] :($txStatus==="FAILED" ? $this->payment_status['failure'] : $this->payment_status['pending']) ))
                            					    );
                					if(!empty($arrayPayMode)){
                						$cashPayInsert = $this->payment_modal->insertData($arrayPayMode,'payment_mode_details'); 
                					}
                 				}
    						    $schData = [];
    						    $cusRegData = [];
    						    $transData = [];
    						    /*if($pay['allow_referral'] == 1){
                				    $ref_data	=	$this->payment_modal->get_refdata($pay['id_scheme_account']);
                					$ischkref	=	$this->payment_modal->get_ischkrefamtadd($pay['id_scheme_account']);	
                			 		if($ref_data['ref_benifitadd_ins_type'] == 1 && $ref_data['referal_code'] != '' && ($ref_data['ref_benifitadd_ins'] == $ref_data['paid_installments']) && $ischkref == TRUE){	
                						$this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
                					}else if($ref_data['ref_benifitadd_ins_type'] == 0 && $ref_data['referal_code'] != '' && $ischkref == TRUE){
                						$this->insert_referral_data($ref_data['id_scheme_account'],$ref_data);
                					}
                			 	}*/
                			 	
                			 	/*Agent and employee incentive starts
						Coded By Haritha 15-9-22
						*/
						
						//agent benefits credit
                                if($postData['login_type'] == 'AGENT' && $pay['agent_refferal'] == 1)
                                {
                                    $type=2; //1- employee 2- agent
                                    $agent_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'],$pay['id_scheme_account'],$type,$pay['id_payment']);
                		          
                		            if($agent_refral > 0)
                		            {
                		                foreach($agent_refral as $ag)
                		                {
                		                    
                        			 	    if($ag['referal_amount'] > 0)
                        			 	    {
                        			 	        $res = $this->insertAgentIncentive($ag,$pay['id_scheme_account'],$pay['id_payment'],$postData['id_employee']);
                    			 	         }
                		                }
                		            }
                                }
                            //employee benefits credit
                                if($postData['login_type'] == 'EMP' && $pay['emp_refferal'] == 1)
                                {
                                    $type=1; //1- employee 2- agent
                                    $emp_refral = $this->payment_modal->get_Incentivedata($pay['id_scheme'],$pay['id_scheme_account'],$type,$pay['id_payment']);
                                    //print_r($emp_refral);exit;
                		            if($emp_refral > 0)
                		            {
                		                //$res = $this->insertEmployeeIncentive($emp_refral,$generic['id_scheme_account'],$status['insertID']);
                		                foreach($emp_refral as $emp)
                		                {
                        			 	    if($emp['referal_amount'] > 0)
                        			 	    {
                        			 	        $res = $this->insertEmployeeIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
                        			 	        if($emp['credit_for'] == 1)
                        			 	        {
                        			 	            $this->customerIncentive($emp,$pay['id_scheme_account'],$pay['id_payment']);
                        			 	        }
                    			 	         }
                		                }
                		            }
                                }
						
						//ends
                			 	
    							// Generate account  number  
    							if($pay['schemeacc_no_set'] == 0 || $pay['schemeacc_no_set']==3)
    							{
    								if($pay['scheme_acc_number'] == '' ||  $pay['scheme_acc_number'] == null)
    								{
    								    $ac_group_code = NULL;
    									// Lucky draw
    									if($pay['is_lucky_draw'] == 1 ){ // Based on scheme settings 
    										// Update Group code in scheme_account table 
    										$updCode = $this->payment_modal->updateGroupCode($pay); 
    										$ac_group_code = ($updCode['status'] ? $updCode['group_code'] : NULL);
    									}
    									$scheme_acc_number = $this->payment_modal->account_number_generator($pay['id_scheme'],$pay['branch'],$ac_group_code); 
    									if($scheme_acc_number != NULL && $pay['id_scheme_account'] > 0){
    										$schData['scheme_acc_number'] = $scheme_acc_number;
    										$cusRegData['scheme_acc_number'] = $scheme_acc_number;
    									}
    								}
    							}
    							// Generate Client ID
    							if($pay['gent_clientid'] ==1 && $scheme_acc_number != NULL && empty($pay['ref_no'])){  
    								$cliData = array(
    												 "cliID_short_code"	=> $this->config->item('cliIDcode'),
    												 "sync_scheme_code"	=> $pay['sync_scheme_code'],
    												 "code"	            => $pay['group_code'],
    												 "ac_no"			=> $scheme_acc_number
    												);											
    								$schData['ref_no'] = $this->payment_modal->generateClientID($cliData);
    								$cusRegData['ref_no'] = $schData['ref_no'];
    								$transData['ref_no'] = $schData['ref_no'];
    								$cusRegData['group_code'] =$pay['group_code'];
    							}
        						// Generate receipt number
    							if($pay['receipt_no_set'] == 1 && ($this->config->item('auto_pay_approval') == 1 ||  $this->config->item('auto_pay_approval') == 2) )
    							{ 
    								$receipt['receipt_no'] = $this->generate_receipt_no($pay['id_scheme'],$pay['branch']);
    								$payment['status'] = $this->payment_modal->update_receipt($pay['id_payment'],$receipt);
    							}
    							
    							if($pay['id_scheme_account'] > 0){
        							if(sizeof($schData) > 0){ // Update scheme account
        								$this->payment_modal->update_account($schData,$pay['id_scheme_account']);
        							}
        							if(sizeof($cusRegData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)){ 
                                        $this->payment_modal->update_cusreg($cusRegData,$pay['id_scheme_account']); // Update Customer reg - Client ID, Ac No
        							}
        							if(sizeof($transData) > 0 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3)){ 
                                        $this->payment_modal->update_trans($transData,$pay['id_scheme_account']); // Update Transaction - Client ID
        							}
    							}
    							//Update First Payment Amount In Scheme Account
    							$approval_type = $this->config->item('auto_pay_approval');
        						if(($approval_type == 1 || $approval_type == 2 || $approval_type == 3) && ($pay['firstPayamt_maxpayable']==1 || $pay['firstPayamt_as_payamt']==1) && ($pay['firstPayment_amt'] == '' ||  $pay['firstPayment_amt'] == null || $pay['firstPayment_amt'] == 0))
        						{
        							if($pay['flexible_sch_type'] == 4 && ($pay['firstPayment_wgt'] == null || $pay['firstPayment_wgt'] == "") ){ // Fix First payable as weight
    									$fixPayable = array('firstPayment_wgt'  =>  $pay['metal_weight'] );
    								}else{
    									$fixPayable = array('firstPayment_amt'  =>  $pay['payment_amount'] );
    								}
    								$status = $this->payment_modal->update_account($fixPayable,$pay['id_scheme_account']);	 
        						}
    							if( $this->config->item('auto_pay_approval') == 2 && ($this->config->item('integrationType') == 2 || $this->config->item('integrationType') == 3))
    							{ 	 
                                    if($this->config->item('integrationType') == 1){
                                        $this->insert_common_data_jil($pay['id_payment']);
                                    }else if($this->config->item('integrationType') == 2){
                                        $this->mobileapi_model->insert_common_data($pay['id_payment']);
                                    }  		            
        		        		}
        		        		
        		        		$service = $this->services_modal->checkService($serviceID); 
                    			if($service['sms'] == 1)
                    			{
                    				$id=$pay['id_payment'];
                    				$data =$this->services_modal->get_SMS_data($serviceID,$id);
                    				$mobile =$data['mobile'];
                    				$message = $data['message'];
                    				$this->mobileapi_model->send_sms($mobile,$message,'',$service['dlt_te_id']);
                    			}
    						} //
    					}
        		    }
        		    
        			/*if($service['email'] == 1 && isset($payData['email']) && $payData['email'] != '')
        			{ 
        				$invoiceData = $this->payment_modal->get_invoiceDataM(isset($payment['id_payment'])?$payment['id_payment']:'');
        				$to = $payData['email'];
        				$subject = "Reg - ".$this->comp['company_name']." payment for the saving scheme";
        				$data['payData'] = $invoiceData[0];
        				$data['type'] = $type;
        				$data['company_details'] = $this->comp;
        				$message = $this->load->view('include/emailPayment',$data,true);
        				$sendEmail = $this->email_model->send_email($to,$subject,$message);	
        			} */
        		}  
			    //redirect('paymt/hdfcTransStatus/success/'.$resData['txnid'].'/'.$resData['amount']);
        	    $response = array("status" => TRUE, "title" => "Transaction Completed", "msg" => $pay_msg);
            }else{ 
                //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
                $response = array("status" => TRUE, "title" => "Transaction Completed", "msg" => "Payment amount of INR ".$resData['amount']." paid successfully"); 
            } 
        }else{ 
            //redirect('paymt/hdfcTransStatus/completed/'.$resData['txnid'].'/'.$resData['amount']);
            $response = array("status" => TRUE, "title" => "Transaction Completed", "msg" => "Payment amount of INR ".$resData['amount']." paid successfully");
        }
        $this->response($response,200); 
    }
    // Cashfree SDK API's :: ENDS
    
  function insert_referral_data($id_scheme_account,$referral_data)
	{ 
		$status=FALSE;			
		$serviceID=16;
	
		if($referral_data['referal_code']!='' && $referral_data['is_refferal_by']==1){			
		  $data = $this->payment_modal->get_empreferrals_datas($id_scheme_account);
		}else if($referral_data['referal_code']!='' && $referral_data['is_refferal_by']==0){			
			$data = $this->payment_modal->get_cusreferrals_datas($id_scheme_account);
		}	
		
		if(!empty($data))
		{			
		   
			if($data['referal_code']!='' && $data['referal_value']!=''  &&  $data['id_wallet_account']!=''){
			// insert wallet transaction data //
							$wallet_data = array(
							'id_wallet_account' => $data['id_wallet_account'],
							'id_sch_ac'         => $id_scheme_account,
							'date_transaction' =>  date("Y-m-d H:i:s"),
							'transaction_type' =>  0,
							'value'            => $data['referal_value'],
							'description'      => 'Collection App - Referral Benefits - '.$data['cusname'].' ref no. '.$data['id_scheme_account']
							);
				$status =$this->payment_modal->wallet_transactionDB($wallet_data);
				  if($status)
				  {
				  		// Update credit flag in customer table
					  	/* is_refbenefit_crt = 0 -> already  benefit credited  & 1-> yet to credit benefits' */					 		
					  	if($referral_data['is_refferal_by']==0 && $data['cusbenefitscrt_type']==0 && ($data['schrefbenifit_secadd']==0 || $data['schrefbenifit_secadd']==1) ){
							// customer referal - single  
							$this->payment_modal->update_customer_only(array('is_refbenefit_crt_cus'=>0),$chkreferral['id_customer']);
						}else if($referral_data['is_refferal_by']==0 && $data['cusbenefitscrt_type']==1 && $data['schrefbenifit_secadd']==1){
							// customer referal - multiple  
							$this->payment_modal->update_customer_only(array('is_refbenefit_crt_cus'=>1),$chkreferral['id_customer']);
						}else if($referral_data['is_refferal_by']==1 && $data['empbenefitscrt_type']==0 && ($data['schrefbenifit_secadd']==0 || $data['schrefbenifit_secadd']==1)){	
							 // emp referal - single  					
							$this->payment_modal->update_customer_only(array('is_refbenefit_crt_emp'=>0),$chkreferral['id_customer']);
						}else if($referral_data['is_refferal_by']==1 && $data['empbenefitscrt_type']==1 && $data['schrefbenifit_secadd']==1){	
							// emp referal - single  			
							$this->payment_modal->update_customer_only(array('is_refbenefit_crt_emp'=>1),$chkreferral['id_customer']);
						}
				  } 
				  $service = $this->services_modal->checkService($serviceID);
				  if($service['sms'] == 1 && $data['mobile']){
				       $sms_data =$this->services_modal->get_SMS_data($serviceID,$data['id_scheme_account']); 
                        if($this->config->item('sms_gateway') == '1'){
                            $this->sms_model->sendSMS_MSG91($sms_data['mobile'],$sms_data['message'],'',$service['dlt_te_id']);		
                        }
                        elseif($this->config->item('sms_gateway') == '2'){
                            $this->sms_model->sendSMS_Nettyfish($sms_data['mobile'],$sms_data['message'],'trans');	
                        }
                        elseif($this->config->item('sms_gateway') == '3')
                        {
                            $this->sms_model->sendSMS_SpearUC($sms_data['mobile'],$sms_data['message'],'',$service['dlt_te_id']);	
                        }
                        elseif($this->config->item('sms_gateway') == '4'){
                            $this->sms_model->sendSMS_Asterixt($sms_data['mobile'],$sms_data['message'],'',$service['dlt_te_id']);		
                        }
                        elseif($this->config->item('sms_gateway') == '5'){
                            $this->sms_model->sendSMS_Qikberry($sms_data['mobile'],$sms_data['message'],'',$service['dlt_te_id']);		
                        }
				  }
				 }
		}
	}
	
	public function payment_gateway($id_branch,$id_pg)
	{		
		
		$data = $this->mobileapi_model->getBranchGatewayData($id_branch,$id_pg);  
		return $data;
	}
	
	function getClassificationAll_get()
	{ 
		//$model = self::MOD_MOB;
		$result = $this->mobileapi_model->getClassification();
		$this->response($result,200);
	}
	
	function amount_to_weight($to_pay)
	{
		$converted_metal_wgt = $to_pay['amount']/$to_pay['metal_rate'];
		return $converted_metal_wgt;
	}
	
	function generate_receipt_no($id_scheme,$branch)
	{
		$rcpt_no = "";
		$rcpt = $this->payment_modal->get_receipt_no($id_scheme,$branch);
		if($rcpt!=NULL)
		{
		    if($this->config->item('receipTcode') != ''){          // based on the config settings to removed comp shortcode front of recp num //HH
		  	$temp = explode($this->comp['short_code'],$rcpt);
			 	if(isset($temp))
			 	{
					$number = (int) $temp[1];
					$number++;
					$rcpt_no =$this->comp['short_code'].str_pad($number, 7, '0', STR_PAD_LEFT);
				}
		    }
			else{
			   $number = (int) $rcpt;
                   $number++;
			      $rcpt_no = str_pad($number, 7, '0', STR_PAD_LEFT);
					//print_r($rcpt_no);exit;
			}
		}
		else
		{
		    if($this->config->item('receipTcode') != ''){
			 	$rcpt_no =$this->comp['short_code']."000001";
		    }
		    else{
		        $rcpt_no ="000001";
		    }
		}
		//print_r($rcpt_no);exit;
		return $rcpt_no;
	}
	
	function getAllPaymentGateways_get()
    {  
        $model = self::ADM_MODEL;
		
		$id_customer=$this->get('id_customer');
	    $data=$this->mobileapi_model->get_customerByID($id_customer);
       // $result['pgData'] = $this->$model->getBranchGateways($this->get('emp_branch'));
        $result['pgData'] = [];
        $result['cc']= $this->mobileapi_model->getActivecardBrands(1);
        $result['dc']= $this->mobileapi_model->getActivecardBrands(2);
        $result['msg'] = "";
        
        $this->response($result,200); 
    }
    
    function no_to_words($no="")
 	{
		$nos = explode('.', $no);
		$val1="";
		$val2="";
		$val="";
		
		
		
		if(isset($nos[0]))
		{
			$val1=$this->no_to_words1($nos[0]);
			$val=$val1."Rupees";
		}
		
		if(isset($nos[1]) && $nos[1] != 0)
		{
			$val2=$this->no_to_words1($nos[1]);
			if(isset($val2))
			$val=$val1." Rupees &"." ".$val2." Paisa";
		}
		
		
		return $val;
	}
   	function no_to_words1($nos1="")
 	{
		$words = array('0'=> '' ,'1'=> 'One' ,'2'=> 'Two' ,'3' => 'Three','4' => 'Four','5' => 'Five','6' => 'Six','7' => 'Seven','8' => 'Eight','9' => 'Nine','10' => 'Ten','11' => 'Eleven','12' => 'Twelve','13' => 'Thirteen','14' => 'Fouteen','15' => 'Fifteen','16' => 'Sixteen','17' => 'Seventeen','18' => 'Eighteen','19' => 'Nineteen','20' => 'Twenty','30' => 'Thirty','40' => 'Fourty','50' => 'Fifty','60' => 'Sixty','70' => 'Seventy','80' => 'Eighty','90' => 'Ninty','100' => 'Hundred','1000' => 'Thousand','100000' => 'Lakh','10000000' => 'Crore');
	$nos[0] = $nos1;
	if($nos[0] == 0 )
        return '';
    else {           
			$novalue='';
			$highno=$nos[0];
			$remainno=0;
			$value=100;
			$value1=1000;
			$temp='';   
            while($nos[0]>=100)   
			 { 
                if(($value <= $nos[0]) &&($nos[0]  < $value1))   
				{
                	$novalue=$words["$value"];
                	$highno = (int)($nos[0]/$value);
                	$remainno = $nos[0] % $value;
                	break;
                }
                $value= $value1;
                $value1 = $value * 100;
            }   
            
            
          if(array_key_exists("$highno",$words))
		  {
		     $remain = ($remainno > 0 ? '& '.$this-> no_to_words1($remainno) : '');
			  return $words["$highno"]." ".$novalue." ".$remain;
		  }
          else 
		  {
		     
             $unit=$highno%10;
             $ten =(int)($highno/10)*10; 
             $remain = ($remainno > 0 ? '& '.$this-> no_to_words1($remainno) : '');
             return $words["$ten"]." ".$words["$unit"]." ".$novalue." ".$remain;
          }
		}
	}
	
	/*Agent and Employee Incentive functions 
	  Coded by Haritha */
	  
	  function insertAgentIncentive($data,$id_sch_acc,$id_payment,$id_agent)
    {
        
        $checkRefExist = $this->payment_modal->checkReferalExist($id_payment,$id_sch_acc);
      
        if($checkRefExist == 0)
        {
        
                                    $insert_array = array("ly_trans_type" => 3,
                            			 	                    "cus_loyal_cus_id" => $data['id_customer'],
                            			 	                    "id_agent"   => $id_agent,
                            			 	                    "id_scheme_account" => $id_sch_acc,
                            			 	                    "id_payment"  => $id_payment,
                            			 	                    "cash_point" => $data['referal_amount'],
                            			 	                    "status"    => 1,
                            			 	                    "tr_cus_type" => 4,
                            			 	                    "cr_based_on" => 3,
                            			 	                    "unsettled_cash_pts" => $data['referal_amount'],
                            			 	                    "date_add"       => date('Y-m-d H:i:s'),
                            			 	                    "credit_for"   => $data['credit_remark']
                            			 	                            );
                            			 	     $status = $this->payment_modal->insert_agent_transaction($insert_array);
                            			 	     $this->payment_modal->updateAgentCash($id_agent,$data['referal_amount']);
                            			 	     $ag_data = array("id_agent"   => $id_agent);
                            			 	     $this->payment_modal->updData($ag_data,'id_payment',$id_payment,'payment');
                return $status;
        }
        else{
            return 0;
        }
    }
    
    function insertEmployeeIncentive($refdata,$id_scheme_account,$id_payment)

	{
	    
		$status=FALSE;			
		$chkreferral=$this->payment_modal->get_referral_code($id_scheme_account);
		//print_r($chkreferral);exit;
         $data = array();
        
        		 $checkCreditExist = $this->payment_modal->checkCreditTransExist($id_scheme_account,$id_payment);
        		 if($checkCreditExist == 0)
        		 {
        			if($chkreferral['referal_code']!='' && $chkreferral['is_refferal_by']==1){			
        
        			  $data = $this->payment_modal->get_empreferrals_datas($id_scheme_account);
        
        			}
        		}
        		
               
        		if(!empty($data) && $chkreferral['is_refferal_by']==1 && $checkCreditExist == 0)
        		{			
        		    
        			if($data['referal_code']!='' && $refdata['referal_amount']!=''  &&  $data['id_wallet_account']!='' && $data['id_wallet_account'] > 0){
                           
        			// insert wallet transaction data //
        
        							$wallet_data = array(
        
        							'id_wallet_account' => $data['id_wallet_account'],
        
        							'id_sch_ac'         => $id_scheme_account,
        
        							'date_transaction' =>  date("Y-m-d H:i:s"),
        
        							'id_employee'      =>  $data['idemployee'],
        
        							'transaction_type' =>  0,
        
        							'value'            => $refdata['referal_amount'],
        							
        							'id_payment'      => $id_payment,
        							
        							'credit_for'     => $refdata['credit_remark'],
        
        							'description'      => 'Referral Benefits - '.$data['cusname'].''
        
        							);
        
        						//	echo"<pre>"; print_r($wallet_data);exit;
        
        				$status =$this->payment_modal->wallet_transactionDB($wallet_data);
        				//echo $this->db->last_query();exit;
                        
        
        				 }
        		}
         
		return true;

	}
	
	function customerIncentive($refdata,$id_scheme_account,$id_payment)
	{
	    

	    $chkreferral=$this->payment_modal->get_referral_code($id_scheme_account);
	    //credit customer introduce staff incentive
		if($chkreferral['is_refferal_by']==0)
		{
			// customer referal - multiple  
			$this->payment_modal->update_customer_only(array('is_refbenefit_crt_cus'=>1),$chkreferral['id_customer']);
			//check customer intro staff incentive
			$isEmpRef = $this->payment_modal->get_empRefExist_datas($id_scheme_account);
			
			if($isEmpRef > 0)
			{
			    $data = $this->payment_modal->get_empreferrals_datas($isEmpRef['id_scheme_account']);
			    if(!empty($data))
        		{			
        		    
        			if($data['referal_code']!='' && $refdata['referal_amount']!=''  &&  $data['id_wallet_account']!='' && $data['id_wallet_account'] > 0){
        
        			// insert wallet transaction data //
        
        							$wallet_data = array(
        
        							'id_wallet_account' => $data['id_wallet_account'],
        
        							'id_sch_ac'         => $isEmpRef['id_scheme_account'],
        
        							'date_transaction' =>  date("Y-m-d H:i:s"),
        
        							'id_employee'      =>  $this->session->userdata('uid'),
        
        							'transaction_type' =>  0,
        
        							'value'            => $refdata['referal_amount'],
        							
        							'id_payment'      => $id_payment,
        							
        							'credit_for'     => 'Customer Intro Scheme Incentive',
        
        							'description'      => 'Customer Intro Referral Benefits - '.$data['cusname'].''
        
        							);
        
        						//	echo"<pre>"; print_r($wallet_data);exit;
        
        				$status =$this->payment_modal->wallet_transactionDB('insert','',$wallet_data);
        
        				 }
        		}
			}
		
		}
	}
    
    /*ends*/
    
    function getallAcitiveschemes_get(){
        
        $model = self::ADM_MODEL;
    	$scheme = $this->$model->getallAcitiveschemes($this->get('id_employee'),$this->get('id_branch'));
    	$result = array('scheme' => $scheme);
    	$this->response($result,200);
    }
    
    function syncAgentCustomers_post()
    {
        $model = self::ADM_MODEL;
        $data = $this->get_values();
        $schemeAcc = array();
        if($data['login_type'] == 'AGENT' && ($data['id_employee'] >0 || $data['id_employee'] != ''))
        {
            $result['schemes'] = $this->$model->get_activeSchemes($data['id_branch']);
            $res = $this->$model->get_customerByAgent($data['id_employee']); 
          
            if(!empty($res)){
                foreach($res as $cus)
                {
            		   if(sizeof($cus)>0)
            		   {
            		            $result['customer'][] = $cus;
                    			//$schemeAcc = $this->$model->get_payment_details($cus['id_customer'],$cus['id_branch'],''); 
                    		   // $cusSchemes[] = $this->array_sort($schemeAcc['chits'], 'allow_pay',SORT_DESC);
            		   }
            	
                }
            }else{
        		   $result['customer'] = [];
        		}
           $this->response($result,200);
        }else{
            $result = array('msg' => 'Not a valid Agent');
    	    $this->response($result,200);
        }
       
    }
    
    function syncOfflineCustomers_post()
    {
        $model = self::ADM_MODEL;
        $data = $this->get_values();
        $customers = array();
      
        foreach($data as $customer)
        {
             if($customer->id_customer != '')
             {
                $data = array(
							'firstname' => ucwords($customer->firstname),
							'lastname'  => NULL,
							'mobile'    => $customer->mobile,
							'email'     => $customer->email,
							'passwd'    => $this->__encrypt($customer->mobile),
							'active'    => 1,
						    'date_add'  => date('Y-m-d H:i:s'),
						    'added_by'  => 1,	// admin 						
							'id_employee' => (isset($customer->id_employee)?$customer->id_employee:NULL),
							'id_village' => NULL,
							'id_branch' => (isset($customer->id_branch)?$customer->id_branch:NULL),
							'date_of_birth' =>(isset($customer->date_of_birth)?$customer->date_of_birth:NULL),
							'pan' =>(isset($customer->pannumber)?$customer->pannumber:NULL),
							'driving_license_no' =>(isset($customer->dlcno)?$customer->dlcno:NULL),
							'aadharid' =>(isset($customer->adharno)?$customer->adharno:NULL),
							'cus_type' =>1,
							'address1'			=>	(isset($customer->address1)?$customer->address1:NULL),
							'address2'			=>	(isset($customer->address2)?$customer->address2:NULL),
							'id_country'		=>	(isset($customer->id_country)?$customer->id_country:NULL),
							'id_state'			=>	(isset($customer->id_state)?$customer->id_state:NULL),
							'id_city'			=>	(isset($customer->id_city)?$customer->id_city:NULL),
							'pincode'			=>	(isset($customer->pincode)?$customer->pincode:NULL),
							'request_customer_id' => $customer->id_customer,
							'id_agent' => ($data['login_type'] == 'AGENT' ?$data['id_employee'] :NULL),
							
						 ); 

                        $res[] = $this->insertOfflineCustomer($data);
                        //$customers = $res;
             }
        
        }
        if($res > 0)
        {
             $offline_customers = array('customers' => $res,'status'=> TRUE, 'msg'=>'Data Synced Successfully.');
             
        }else{
            $offline_customers = array('status'=> FALSE, 'msg'=>'Unable to Proceed your request.');
        }
        $this->response($offline_customers,200);
    }
    
    function syncOfflineAccPayments_post()
    {
        $model = self::ADM_MODEL;
        $data = $this->get_values();
        $accounts = array();
        $pay = array();
        
        foreach($data as $cus_schemes)
        {
           
            //if customer join new scheme call create account api
            if($cus_schemes->is_new == 'Y' && $cus_schemes->id_customer != "new")
            {
                $accdata = array(
            	  "id_customer" => $cus_schemes->id_customer,
            	  "mobile" => $cus_schemes->mobile,
            	  "id_scheme" => $cus_schemes->id_scheme,
            	  "group_code"=>  $cus_schemes->group_code,
            	  "scheme_acc_number" =>  "",
            	  "account_name" => $cus_schemes->account_name,
            	  "pan_no" => NULL,
            	  "id_branch" => NULL,
            	  "is_new" => "Y",
            	  "referal_code" =>  "",
            	  "agentcode" => $data["agent_code"],
            	  "id_agent" => ($data['login_type'] == "AGENT" ? $data['id_employee'] :NULL),
            	  "payable" => ""
            	);

                        $data_string = json_encode($accdata);
                    
                        $curl = curl_init('https://retail.logimaxindia.com/etail_v1/index.php/adminapp_api/createAccount');
                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data_string))
                        );
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // Make it so the data coming back is put into a string
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);  // Insert the data
                        // Send the request
                        $result = curl_exec($curl); 
                        curl_close($curl);
                        $res = json_decode($result, true);
                            if($res['chit']['id_scheme_account'] > 0)
                            {
                                $paydata = array(
                        
                            	  "firstname" => $cus_schemes->firstname,
                            	  "lastname" => "",
                            	  "phone" => $cus_schemes->mobile,
                            	  "id_branch" => null,
                            	  "amount" => $cus_schemes->amount,
                            	  "redeemed_amount" => "0",
                            	  "productinfo" => "undefined",
                            	  "email" => $cus_schemes->email,
                            	  "id_employee" => $data['id_employee'],
                            	  "login_type" => $data['login_type'],
                            	  "pg" => "NB",
                            	  "pay_mode" => "CSH",
                            	  "gateway" => "0",
                            	  "pg_code" => "0",
                            	 
                            	 'pay_arr' => [array(
                            	   'scheme_type' => $cus_schemes->scheme_type, 
                            	   'chit_number'=> $cus_schemes->chit_number, 'amount'=>$cus_schemes->amount, 'pay_amt'=>$cus_schemes->amount, 'max_amt'=>50000000, 
                            	   'min_amt'=>500, 'udf1'=> $res['chit']['id_scheme_account'], 
                            	   'udf2' =>"", 'udf3' => $cus_schemes->udf3, 'udf4' => $cus_schemes->amount, 'udf5' => "", 'charge'=> "0.00", 'charge_head' => 'Convenience fees', 
                            	   'due_type' => "ND", 'discount'=>0, 'gst_amt'=>0, 'allowed_dues'=>$cus_schemes->allowed_dues, 'id_branch'=> $cus_schemes->id_branch
                            	  )]
                            	);
        
                                	$payments =  $this->insertOfflinepayments($paydata);
                        	        $pay[] = $payments;
                        	        
                                }
                    	
                    	
            }
                
                if($cus_schemes->udf1 >0)
                {
                    $paydata = array(
                        
                    	  "firstname" => $cus_schemes->firstname,
                    	  "lastname" => "",
                    	  "phone" => $cus_schemes->mobile,
                    	  "id_branch" => null,
                    	  "amount" => $cus_schemes->amount,
                    	  "redeemed_amount" => "0",
                    	  "productinfo" => "undefined",
                    	  "email" => $cus_schemes->email,
                    	  "id_employee" => $data['id_employee'],
                    	  "login_type" =>  $data['login_type'],
                    	  "pg" => "NB",
                    	  "pay_mode" => "CSH",
                    	  "gateway" => "0",
                    	  "pg_code" => "0",
                    	 
                    	 'pay_arr' => [array(
                    	   'scheme_type' => $cus_schemes->scheme_type, 
                    	   'chit_number'=> $cus_schemes->chit_number, 'amount'=>$cus_schemes->amount, 'pay_amt'=>$cus_schemes->amount, 'max_amt'=>50000000, 
                    	   'min_amt'=>500, 'udf1'=> $cus_schemes->udf1, 
                    	   'udf2' =>"", 'udf3' => $cus_schemes->udf3, 'udf4' => $cus_schemes->amount, 'udf5' => "", 'charge'=> "0.00", 'charge_head' => 'Convenience fees', 
                    	   'due_type' => "ND", 'discount'=>0, 'gst_amt'=>0, 'allowed_dues'=>$cus_schemes->allowed_dues, 'id_branch'=> $cus_schemes->id_branch
                    	  )]
                    	);
                          
                            $payments =  $this->insertOfflinepayments($paydata);
                        	$pay[] = $payments;
                }
            
        }
        if($pay > 0)
        {
            $response = array('status'=> TRUE, 'msg'=>'Data Synced Successfully.');
        }else{
            $response = array('status'=> FALSE, 'msg'=>'Unable to Proceed your request.');
        }
         $this->response($response,200);
    }
    
    public function insertOfflinepayments($data)
	{
	    $this->load->model('payment_modal');
		   $payData = $data;
		   $pay_flag = TRUE;
		   $allow_flag = FALSE;
		   $submit_pay = FALSE;
		   $actAmount = (float) $payData['amount'];  // sum of all chit amount
		   $cusData = $this->payment_modal->get_customer($payData['phone']);
		  
		   $gateway = (isset($payData['gateway']) ? $payData['gateway'] :1 );
		   $redeemed_amount = $payData['redeemed_amount'] ;
		   $id_branch = (isset($payData['id_branch']) ? $payData['id_branch']:NULL);
		   // For admin app
		   $id_employee = (isset($payData['id_employee']) && $payData['login_type'] == 'EMP' ? $payData['id_employee']:NULL);
		   $added_through = 3; // 2- custmer mobile app, 3 - Admin mobile app
		
		   if($pay_flag)
		   {
				//generate txnid
				$txnid = uniqid(time());
				$i=1;
				$sch_payment = $payData['pay_arr'];
				//print_r($payData['pay_arr']);exit;
				$udf1= "";
				$udf2= "";
				$udf3= "";
				$productinfo= "";
				$payIds= "";
				$this->db->trans_begin();	
				$start_year = $this->payment_modal->get_financialYear();
				foreach ($sch_payment as $pay){	
			
					$metal_wgt	= NULL;			
					$discount 	= ($pay['discount'] >0 ? $pay['discount'] : 0.00);
					$chit 		= $this->payment_modal->get_schemeByChit($pay['udf1']);
	   				$pay['discount'] = $discount;
					//validate amount
					$metal_rate = $this->payment_modal->getMetalRate($pay['id_branch']); 
					//$branch = $this->payment_modal->get_schjoinbranch($pay->udf1);
                    $rate_fields = $this->payment_modal->getRateFields(1); 
                   
                    $rate_field = sizeof($rate_fields) == 1 ? $rate_fields['rate_field'] : NULL;
                    $rate   = (float) ( $rate_field == null ? null : $metal_rate[$rate_field] );
					//$rate = (float) ($chit['id_metal'] == 1 ? $metal_rate['goldrate_22ct'] : $metal_rate['silverrate_1gm']);               
					// GST Calculation
					$gst_amt = 0;
					$amount = $pay['amount'] + $pay['discount'];
	    			if($chit['gst'] > 0 ){
				        $insAmt_withoutDisc = $amount-$pay['discount'];
				        
				        if($sch_data['gst_type'] == 0){  // Inclusive  
	                        $gst_removed_amt = $insAmt_withoutDisc*100/(100+$chit['gst']);
	                    	$gst_amt = $insAmt_withoutDisc - $gst_removed_amt;
	                    	$metal_wgt = ($gst_removed_amt+$pay['discount'])/$pay['udf3'];  
	                    }
	                    else if($sch_data['gst_type'] == 1){ // Exclusive
	                        $amt_with_gst = $insAmt_withoutDisc*((100+$chit['gst'])/100);
	                    	$gst_amt = $amt_with_gst - $insAmt_withoutDisc ;
	                    	$metal_wgt = $amount/$pay['udf3']; 
	                    }
	   	            }
	   	         
				   if($pay['scheme_type'] == 0 || ($pay['scheme_type'] == 3 && $chit['flexible_sch_type'] == 1)){
				    
                      $allow_flag = (($pay['discount']==""?0.00:$pay['discount'])+$pay['amount'] >= $chit['amount']? TRUE :FALSE);
                     
                   }
                   else if($pay['scheme_type'] == 1){  
                      $amt = $rate * $pay['udf2'];
                      $allow_flag = ((($pay['amount'] >= $amt)&&($rate == (float) $pay['udf3']))? TRUE : FALSE);$metal_wgt = (isset($pay['udf2']) && $pay['udf2'] !='' ? $pay['udf2'] : 0.000);              
                   }
				   else if($pay['scheme_type'] == 2 || ($pay['scheme_type'] == 3 && ($chit['flexible_sch_type'] == 2 ||$chit['flexible_sch_type'] == 3 ) )){                              	 
                      $data = array('metal_rate'=>$pay['udf3'],'amount'=>$pay['udf4']);
                        // print_r($data['metal_rate']);exit; 
                      if($gst_amt == 0){
					  	$metal_wgt = $this->amount_to_weight($data); 
					 
					  }	
                    //  $allow_flag = (($rate == (float) $pay->udf3) ? TRUE :FALSE);
                    $allow_flag = (($data['metal_rate'] == (float) $pay['udf3']) ? TRUE :FALSE);
                   //	print_r($allow_flag);exit;
                   }
                   else{    
                      $metal_wgt = (isset($pay['udf2']) && $pay['udf2'] !='' ? $pay['udf2'] : 0.000);                 
                      $allow_flag= (($rate == (float) $pay['udf3']) ? TRUE :FALSE);
                   }
                    
           
				   if($cusData['branch_settings']==1)
				   {
					    if($cusData['is_branchwise_cus_reg']==1)
						{
							$id_branch  = $cusData['id_branch'];
						}
						else
						{
							$id_branch  = $pay['id_branch'];
							//$id_branch  = $branch['sch_join_branch'];
						}								
					}
					else{
						$id_branch = NULL;
					}
					$due_month	= date("m");
					$month		= date("m");
					$due_year	= date("Y");
					// ND - normal, PN - pending & normal, AN - adv & normal, PD pending due ,AD-adv due
					$dueType = ''; 
						if($pay['due_type'] == 'PD'){
						    $month  	=  NULL;
	        				$due_year   =  NULL;
	        				$dueType    = 'PD';
						}
						else if($pay['due_type'] == 'ND')
						{ 
							$dueData = $this->payment_modal->generateDueDate($pay['udf1'],$dueType);
							// set due data
							$month      = $dueData['due_month'];
							$due_year   = $dueData['due_year'];
							$dueType = $pay['due_type'];
						}
						else if($pay['due_type'] == 'AD')
						{
						    $dueType = $pay['due_type'];
						    $dueData = $this->payment_modal->generateDueDate($pay['udf1'],$dueType);
							// set due data
							$month      = $dueData['due_month'];
							$due_year   = $dueData['due_year'];    									
						}
						else
						{
							$dueType = $pay['due_type'];
						} 
					    // metal_wgt_decimal = 2 means only 2 decimals are allowed for metal wgt, hence bcdiv() is used to make the weight to 2 decimals and 0 is appended as last digit.
						$decimal = $chit['metal_wgt_decimal'];   
                        $round_off = $chit['metal_wgt_roundoff'] ; 
                        $metal_wgt =  ($round_off == 0 ? bcdiv($metal_wgt,1,$decimal) : $metal_wgt );
					 //print_r($allow_flag);exit;
					  
					   if($allow_flag){
							$insertData = array(
							    
						            "custom_entry_date"	 => date('Y-m-d H:i:s'),
									"id_scheme_account"	 => (isset($pay['udf1'])? $pay['udf1'] : NULL ),
									"payment_amount" 	 => (isset($pay['amount'])? $pay['amount']+($pay['discount']==""?0.00:$pay['discount']) : NULL ), 
									"gst" 	    		 => (isset( $chit['gst'])? $chit['gst'] : 0 ),
									"gst_type" 	    	 => (isset($chit['gst_type'])? $chit['gst_type'] : 0 ),
									"gst_amount"    	 => $gst_amt,
									"payment_type" 	     => ($gateway == 0 ?'CSH':(isset($payData['pg_code']) ? ($payData['pg_code'] == 1 ? ($redeemed_amount > 0 ?"Wallet + Payu Checkout":"Payu Checkout"):($payData['pg_code'] == 2 ? "HDFC":($payData['pg_code'] == 4 ? "Cash Free":($payData['pg_code'] == 5 ? "Atom":"Tech Process")))): 1)),
									"no_of_dues" 	     => 1,
									"actual_trans_amt"   => (isset($actAmount) ? $actAmount : 0.00),
									"act_amount"         => (isset($pay['amount'])? $pay['amount'] : NULL ),
									"date_payment" 		 =>  date('Y-m-d H:i:s'),
									"metal_rate"         => (isset($pay['udf3']) && $pay['udf3'] !='' ? $pay['udf3'] : NULL),
									"metal_weight"       =>  $metal_wgt,
									"id_transaction"     => ($gateway == 0 ?NULL:(isset($txnid) ? $txnid.'-'.$i : NULL)),
									"ref_trans_id"       => ($gateway == 0 ?NULL:(isset($txnid) ? $txnid : NULL)),// to update pay status after trans complete.
									"remark"             =>  'Payment for '.$pay['udf5']." Initiated.",
									"added_by"			 =>  $added_through,
									"id_employee"		 =>  $id_employee,
									"add_charges" 	     => (isset($pay['charge']) ?$pay['charge'] : NULL), 
									"discountAmt"        => ($pay['discount']!="" ? $pay['discount'] : 0.00),
									//"payment_status"     => ($gateway == 0 ? ($added_through== 3 ?$this->payment_status['success']:$this->payment_status['awaiting']):$this->payment_status['pending']),
									"payment_status"     => 1,
									"id_payGateway"      => (isset($payData['gateway']) ? $payData['gateway']: 1),
							 		"redeemed_amount"    => (isset($redeemed_amount) ?$redeemed_amount :0.00),
							 		"id_branch"    		 => $id_branch,
							 		"due_type"           => $dueType,
							 		"due_month"          => $month,
							 		"due_year"           => $due_year,
							 		"id_employee"        => (isset($payData['id_employee']) && $payData['login_type'] == 'EMP' ?$payData['id_employee'] :NULL),
							 		"id_agent"        => ($payData['login_type'] == 'AGENT' ?$payData['id_employee'] :NULL),
							 		"receipt_year"            => $start_year,
							 			"payment_mode" =>  (isset($payData['pay_mode']) ?$payData['pay_mode'] : NULL),
							 		"card_no"        => (isset($payData['refernceNo']) ?$payData['refernceNo'] : NULL)
							 		
							// 	    "is_point_credited"   => 1
									//status - 0 (pending), will change to 1 after approved at backend
								);  
								//print_r($insertData);exit;
						$udf1 = $udf1." ".$pay['udf1'];
						$udf2 = $udf2." ".$pay['udf2'];
						$udf3 = $udf3." ".$pay['udf3'];
						$productinfo = $productinfo." ".$pay['udf1'];
			
						//inserting pay_data before gateway process
						//echo "<pre>"; print_r($insertData);exit; 
						$this->db->trans_begin();
                         $payment = $this->payment_modal->addPayment($insertData);	
                        // print_r($payment);exit;
                        $pay_data = $this->adminappapi_model->getPayGenData($payment['insertID']);
					// Generate receipt number
					if($pay_data['receipt_no_set'] == 1)
					{  
						$receipt['receipt_no'] = $this->generate_receipt_no($pay_data['id_scheme'],$pay_data['branch']);
					    $this->payment_modal->update_receipt($pay_data['id_payment'],$receipt);
					}
					
					// Generate account  number  based on one more settings Integ Auto//hh
						if($pay_data['schemeacc_no_set'] == 0 || $pay_data['schemeacc_no_set']==3)
						{
							if($pay_data['scheme_acc_number'] == '' ||  $pay_data['scheme_acc_number'] == null)
							{
								$scheme_acc_number = $this->payment_modal->account_number_generator($pay_data['id_scheme'],$pay_data['branch'],'');
								if($scheme_acc_number != NULL && $scheme_acc_number !="Not Allocated")
								{
								    $schData['scheme_acc_number'] = $scheme_acc_number;
							    }
							
							$updSchAc = $this->payment_modal->update_account($schData,$pay_data['id_scheme_account']);
							
							}
						}
						
						/*Agent and employee incentive starts
						Coded By Haritha 15-9-22
						*/
					
						//agent benefits credit
                                if($pay_data['agent_refferal'] == 1 && $payData['login_type'] == 'AGENT')
                                {
                                    	
                                    $type=2; //1- employee 2- agent
                                    $agent_refral = $this->payment_modal->get_Incentivedata($pay_data['id_scheme'],$pay_data['id_scheme_account'],$type,$pay_data['id_payment']);
                		            if($agent_refral > 0)
                		            {
                		                foreach($agent_refral as $ag)
                		                {
                		                    
                        			 	    if($ag['referal_amount'] > 0)
                        			 	    {
                        			 	        $res = $this->insertAgentIncentive($ag,$pay_data['id_scheme_account'],$pay_data['id_payment'],$payData['id_employee']);
                    			 	         }
                		                }
                		            }
                                }
                            //employee benefits credit
                                if($pay_data['emp_refferal'] == 1 && $payData['login_type'] == 'EMP')
                                {
                                    $type=1; //1- employee 2- agent
                                    $emp_refral = $this->payment_modal->get_Incentivedata($pay_data['id_scheme'],$pay_data['id_scheme_account'],$type,$pay_data['id_payment']);
                                    //print_r($emp_refral);exit;
                		            if($emp_refral > 0)
                		            {
                		                foreach($emp_refral as $emp)
                		                {
                        			 	    if($emp['referal_amount'] > 0)
                        			 	    {
                        			 	        $res = $this->insertEmployeeIncentive($emp,$pay_data['id_scheme_account'],$pay_data['id_payment']);
                        			 	        if($emp['credit_for'] == 1)
                        			 	        {
                        			 	            $this->customerIncentive($emp,$pay_data['id_scheme_account'],$pay_data['id_payment']);
                        			 	        }
                    			 	         }
                		                }
                		            }
                                }
						
						//ends
						 
						$i++;
					}
					
					
				 }
				 
				 	if($this->db->trans_status()=== TRUE)
        	             { 
        				 	$this->db->trans_commit();
        				 	return TRUE;
        				 }
        				 else{
        				 	$this->db->trans_rollback();
        				 	return FALSE;
        				 }
			}
	}
	
function insertOfflineCustomer($data)
	{
		$model = self::ADM_MODEL;		
		//$data = $this->get_values();
		$no_deviceData = FALSE;
		$result ='';
	    $m_exist =	$this->$model->isOfflineMobile($data['mobile']);	
	    $e_exist =	($data['email'] != '' ? $this->$model->clientEmail($data['email']):FALSE);
		if(!$m_exist['status'] && !$e_exist){ // already regiatered

        		$customer = array(
        						'info'=>array(
        							'firstname' => ucwords($data['firstname']),
        							'lastname'  => ucfirst($data['lastname']),
        							'mobile'    => $data['mobile'],
        							'email'     => $data['email'],
        							'passwd'    => $this->__encrypt($data['mobile']),
        							'active'    => 1,
        						    'date_add'  => date('Y-m-d H:i:s'),
        						    'added_by'  => 1,	// admin 						
        							'id_employee' => (isset($data['id_employee'])?$data['id_employee']:NULL),
        							'id_village' => (isset($data['id_village'])?$data['id_village']:NULL),
        							'id_branch' => (isset($data['id_branch'])?$data['id_branch']:NULL),
        							'date_of_birth' =>(isset($data['date_of_birth'])?$data['date_of_birth']:NULL),
        							'pan' =>(isset($data['pannumber'])?$data['pannumber']:NULL),
        							'driving_license_no' =>(isset($data['dlcno'])?$data['dlcno']:NULL),
        							'aadharid' =>(isset($data['adharno'])?$data['adharno']:NULL),
        							'cus_type' =>(isset($data['custype'])?$data['custype']:1),
        							'id_agent' => (isset($data['id_agent'])?$data['id_agent']:NULL),
        							),
        							'address'=>array(
        								'address1'			=>	(isset($data['address1'])?$data['address1']:NULL),
        								'address2'			=>	(isset($data['address2'])?$data['address2']:NULL),
        								'id_country'		=>	(isset($data['id_country'])?$data['id_country']:NULL),
        								'id_state'			=>	(isset($data['id_state'])?$data['id_state']:NULL),
        								'id_city'			=>	(isset($data['id_city'])?$data['id_city']:NULL),
        								'pincode'			=>	(isset($data['pincode'])?$data['pincode']:NULL)
        							)
        						 ); 
        						 
        						 
        		$this->db->trans_begin();
        		$status = $this->$model->insert_customer($customer);
        		//print_r($this->db->last_query());exit;
        		if($this->db->trans_status()==TRUE)
        		{	
        		    
        		    //base64 code 
        		
                    $aadhaar_image_base64 =base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['adharnofileName']));
                    $file = 'assets/img/customer_aadhaar/aadhar_'. $status['insertID'] . '.png';
                    file_put_contents($file, $image_base64);
                    
                    $pan_image_base64 =base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['panfileName']));
                    $file = 'assets/img/customer_pan/pan_'. $status['insertID'] . '.png';
                    file_put_contents($file, $image_base64);
                    
                    $dl_image_base64 =base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data['dlfileName']));
                    $file = 'assets/img/customer_dl/dl_'. $status['insertID'] . '.png';
                    file_put_contents($file, $image_base64);
                    
            		//base64 code  
        		
        			$wallet_acc =  $this->$model->wallet_accno_generator(); 				   
        			if($wallet_acc['wallet_account_type']==1){
        				$this->wallet_account_create($status['insertID'],$data['mobile']);
        				}
        			$id = $status['insertID']; 
        			if($this->db->trans_status() == TRUE ){
        				$this->db->trans_commit();
        					$serviceID = 1;
        					  $company = $this->$model->company_details();
        					  $service = $this->services_modal->checkService($serviceID);
        						if($service['sms'] == 1)
        						{
        							
        							$data =$this->services_modal->get_SMS_data($serviceID,$id);
        							$mobile =$data['mobile'];
        							$message = $data['message'];
        							$dlt_te_id = $service['dlt_te_id'];
                                    $this->$model->send_sms($mobile,$message,'',$dlt_te_id);
        							
        						}
        						
        						if($service['email'] == 1 && $customer['email'] != '')
        						{
        							$to =$customer['email'];
        							$data['name'] = $customer['firstname'];
        							$data['mobile'] = $customer['mobile'];
        							$data['passwd'] = $this->__decrypt($customer['passwd']);
        							$data['company_details']=$company;
        							$data['type'] = 3;
        							$subject = "Reg: ".$this->comp['company_name']." saving scheme registration";
        							$message = $this->load->view('include/emailAccount',$data,true);
        							$sendEmail = $this->email_model->send_email($to,$subject,$message);
        						}
        				$result = array('timestamp' => $data['request_customer_id'],'id_customer' => $id,"status" =>TRUE); 				
        			}
        			else
        			{
        				$this->db->trans_rollback();
        				$result = array( 'timestamp' => $data['request_customer_id'], 'id_customer' => '' ,"status" =>FALSE, "msg" => "Unable to proceed your request,please try again..");			
        			}
        			
        		}
        		else
        		{
        			$result = array('timestamp' => $data['request_customer_id'], 'id_customer' => '',"status" =>FALSE, "msg" => "Unable to proceed your request");			
        		}	
		}
		else{
			$result = array('timestamp' => $data['request_customer_id'],'id_customer' => $m_exist['id_customer'], "status"=>FALSE,"msg" => "Already registered");
		}
		return $result;		 
	}
	
	function generateAcNoOrReceiptNo($pay){		
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
		   
           $response = $this->integration_model->khimji_curl('app/v1/saveSchemeOrInstallmentDetails',$postData);
           //echo "<pre>";print_r($postData);echo "<pre>";print_r($response['data']);exit;
           if($response['status'] == TRUE){
               	$resData = $response['data']->data; 
               	if($resData->errorCode == 0){ // $resData->status == TRUE && 
               		if(isset($resData->result->orderNo)){
						$acData = array(
									 'scheme_acc_number'=> $resData->result->orderNo,
									 'date_upd'			=> date("Y-m-d H:i:s")
									 );
						$this->payment_modal->updData($acData,'id_scheme_account',$pay['id_scheme_account'],'scheme_account');
						$payData = array(
									 'receipt_no'	=> $resData->result->orderNo,
									 'date_upd'		=> date("Y-m-d H:i:s")
									 );
						$this->payment_modal->updData($payData,'id_payment',$pay['id_payment'],'payment');
		    			return true;						
					}
					if(isset($resData->result->installmentNo)){
						$payData = array(
									 'receipt_no'	=> $resData->result->installmentNo,
									 'date_upd'		=> date("Y-m-d H:i:s")
									 );
						$this->payment_modal->updData($payData,'id_payment',$pay['id_payment'],'payment');
						return true;
					}
                }
                else if($resData->errorCode == 1001){
					$payData = array(
								 'offline_error_msg'	=> date("Y-m-d H:i:s")." Acc or Receipt Error : ".$resData->errorMsg,
								 );
					$this->integration_model->updateData($payData,'id_payment',$pay['id_payment'],'payment');
                }
           }
           // Write log in case of API call failure
	       if (!file_exists('log/khimji')) {
	            mkdir('log/khimji', 0777, true);
	        }
	        $log_path = 'log/khimji/'.date("Y-m-d").'.txt';  
	        $logData = "\n".date('d-m-Y H:i:s')."\n MOBILE | API : app/v1/saveSchemeOrInstallmentDetails \n Post : ".json_encode($postData,true)."\n Response : ".json_encode($response,true);
		    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
		    return true;
               
	}
	// .Khimji integration API call functions
	
	/**	* 
	*	Khimji integration API call functions 
	* 	
	*	1. Generate  tranUniqueId
	* 	2. Generate AcountNo Or ReceiptNo
	* 
    */
    
    function manualTUIDgen_get(){
        //date = 2021-11-28 18:23:39
        $amount = 5000;
        $chit = $this->payment_modal->get_schemeByChit(11);
        $tranUniqueId = $this->generateTranUniqueId($chit,5000);
        echo $tranUniqueId;exit;
    }
    
    function generateTranUniqueId($chit,$amount){
        $tranUniqueId = '';
    	$postData = array(
                    	"isKycValidationCheck" => false,
                    	"customerCode"  => $chit['reference_no'],
                    	"transactionType"=> 1,
                    	"schemeCode"    => $chit['sync_scheme_code'],
                    	"amount"        => $amount,
                    	"date"          => date("Y-m-d"),
                    	"narration"     => "Requested from mobile app"
                    );
        $is_new_ac = ($chit['scheme_acc_number'] != "" && $chit['scheme_acc_number'] != NULL ? FALSE : TRUE);            
        if($is_new_ac){ // 1st Installment 
        	$postData["action"] = 1;
            $postData["narration"]   =  $chit['nominee_address1'].",".$chit['nominee_address2'];
            /*$postData["narration2"]  =  $chit['nominee_relationship'];//"Nominee Relation"
            $postData["narration3"]  =  $chit['nominee_name']; //"Nominee Name"
            $postData["narration4"]  =  "";//Nominee DOB"
            $postData["narration5"]  =  $chit['nominee_mobile']; //"Nominee MobNo"*/
            $postData["salesmanName"]=  $chit['emp_name'];
            $postData["employeeId"]  =  $chit['referal_code'];
            $postData["nominee"] = array(
                                		"nomineeName" => $chit['nominee_name'],
                                		"relation" => $chit['nominee_relationship'],
                                		"address1" => $chit['nominee_address1'],
                                		"address2" => $chit['nominee_address2'],
                                		"dateOfBirth" => NULL,
                                        "mobileNo" => $chit['nominee_mobile']
                                	   );
	    }else{
	    	$postData["action"] = 2;
	        $postData["orderNo"] = $chit['scheme_acc_number'];
	    }
       $response = $this->integration_model->khimji_curl('app/v1/saveSchemeOrInstallmentDetails',$postData);
       /*echo "<pre>";print_r($postData);
       echo "<pre>";print_r($response);exit;*/
       // Write log in case of API call failure
        if (!file_exists('log/khimji')) {
            mkdir('log/khimji', 0777, true);
        }
        $log_path = 'log/khimji/'.date("Y-m-d").'.txt';
        $logData = "\n".date('d-m-Y H:i:s')."\n API : app/v1/saveSchemeOrInstallmentDetails \n Post : ".json_encode($postData,true)."\n Response : ".json_encode($response,true);
	    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
        if($response['status'] == TRUE){
           	$resData = $response['data']->data; 
           	if($resData->status == TRUE && $resData->errorCode == 0){
               return array("status" => $resData->status, "tranUniqueId" => $resData->result->tranUniqueId);
            }
            else if($resData->status == FALSE || $resData == null){
                if($resData->errorCode == 1001){
    				$payData = array(
    							 'offline_error_msg'	=> date("Y-m-d H:i:s")." ID Gen Error : ".$resData->errorMsg,
    							 );
    				$this->integration_model->updateData($payData,'id_payment',$pay['id_payment'],'payment');
                }
               return array("status" => $resData->status, "message" => $resData->errorMsg, 'resData' => $resData);
            }
        }
          
	    return $tranUniqueId;
	    /*$this->db->trans_rollback();
		$this->session->set_flashdata('errMsg','Unable to proceed your payment,please try after sometime or contact customer care.');
		redirect("/paymt");*/
    }
    
    
    // Khimji : Fetch user's offline data using ACME api & insert it in our database
    public function getDataFromOffline($id_customer,$cus_reference_no){
    	//$data = $this->get_values();
        $cus_reference_no = $id_customer;
        $id_customer = $cus_reference_no;
        $result = array('khj_sync_status' => 0);
      
        // Block api call
        $result["last_sync_time"] =  date('Y-m-d H:i:s');
        $result["CUS_REF_NO"] =  "CUS REF NO : ".$cus_reference_no;
       // echo json_encode($result);exit;
        
        if($this->config->item("integrationType") == 5 && $cus_reference_no > 0){ 
            if (!file_exists('log/khimji')) {
                mkdir('log/khimji', 0777, true);
            }
            $log_path = 'log/khimji/'.date("Y-m-d").'.txt';      
            $this->load->model("integration_model");
            $postData = array(
                            "customerCode"	=> $cus_reference_no,
                            "uniqueCode" 	=> ""
                        );
            $response = $this->integration_model->khimji_curl('user/getSchemeDetails',$postData);
            if($response['status']){
               	$resData = $response['data'];
               	if($resData->status == 1 && $resData->errorCode == 0){
           
                   foreach($resData->schemes as $data){
                        $acData = $data->schemeDetails;
                        
                        $isAccExist = $this->integration_model->isAccExist($acData);
                        $id_scheme_account = '';
                        $this->db->trans_begin();
                        $cusData = $this->integration_model->getAccInsData($acData,$id_customer);
                   
                        $blockExistAcData = TRUE;
                        if($cusData['id_scheme'] > 0){
                            if($isAccExist['status'] == FALSE){ // Account Not Exist && existing data insert allowed
                                // Insert Scheme Account Data
                                $insAcc = array(
                    						 'id_customer'       => $id_customer,
                    						 'id_scheme'         => $cusData['id_scheme'],
                    						 'start_date'        => date('Y-m-d',strtotime(str_replace('/','-',$acData->joiningDate))),
                    						 'maturity_date'     => date('Y-m-d',strtotime(str_replace('/','-',$acData->maturityDate))),
                    						 'scheme_acc_number' =>	$acData->schemeNo,
                    						 'account_name'      => $cusData['ac_name'],
                    						 'is_new'		     => 'N',
                    						 'active'            => 1,
                    						 'date_add'          => date('Y-m-d H:i:s'),
                    						 'added_by' 		 => 0,
                    						 'id_branch'         => $cusData['id_branch'],
                    					 	 'firstPayment_amt'  => (isset($acData->installmentAmount) ?$acData->installmentAmount:NULL),
                    						 'pan_no'		     => (isset($acData->panNo) ?$acData->panNo:NULL),
                    						); 	
                                $id_scheme_account = $this->integration_model->insertData($insAcc,'scheme_account');
                                if($id_scheme_account > 0){
                                    $result["SchAc_Success"][] = "Created successfully ID : ".$id_scheme_account;
                                }else{
                                	$result["SchAc_Error"][] = $this->db->_error_message();
                                }
                            }else if($isAccExist['status'] == TRUE){
                                $id_scheme_account = $isAccExist['id_scheme_account'];
                                $result["SchAc_Error"][] = "Scheme account Already Exist ID : ".$id_scheme_account." scheme No: ".$acData->schemeNo;
                            }
                            if($id_scheme_account > 0){
                                foreach($data->installmentList as $pay){
                                    $isPayExist = $this->integration_model->isPayExist($pay,$id_customer);
                                    if($isPayExist['status'] == false){
        								$paid_on = explode("/",$pay->date);
        	                            $insPay = array(
        	                						"id_scheme_account"	 => $id_scheme_account,
        	    									"payment_amount" 	 => (isset($pay->amount)? $pay->amount : NULL ), 
        	    									"gst" 	    		 => 0,
        	    									"gst_type" 	    	 => 0,
        	    									"gst_amount"    	 => 0,
        	    									"payment_type" 	     => 'Offline',
        	    									"payment_mode" 	     => 'OFL',
        	    									"is_offline"	     => 1,
        	    									"no_of_dues" 	     => 1,
        	    									"actual_trans_amt"   => (isset($pay->amount)? $pay->amount : NULL ),
        	    									"act_amount"         => (isset($pay->amount)? $pay->amount : NULL ),
        	    									"receipt_no"         => (isset($pay->voucherNo)? $pay->voucherNo : NULL ),
        	    									"date_payment"       => date('Y-m-d',strtotime(str_replace('/','-',$pay->date))),
        	    									"date_add" 		     => date('Y-m-d H:i:s'),
        	    									"added_by"			 =>  0,
        	    									"payment_status"     => 1,
        	    							 		"id_branch"    		 => $cusData['id_branch'],
        	    							 		"due_type"           => 'ND',
        	    							 		"due_month"          => $paid_on[1],
        	    							 		"due_year"           => $paid_on[2]
        	                						);	
        	                            $id_payment = $this->integration_model->insertData($insPay,'payment');
        	                            if($id_payment > 0){
                                            $result["Pay_Success"][] =  "Payment created id : ".$id_payment;
                                        }else{
                                            $result["Pay_Error"][] =  $this->db->_error_message();
                                        }
        							}else{
        							    $result["Pay_Error"][] =   "Payment Already Exist ID : ".$isPayExist['id_payment']." Voucher No: ".$pay->voucherNo."<br/>";
        							}                            
                                    //echo "<pre>";print_r($pay);
                                }
                            }else{
                                $result["SchAc_Error"][] = "Invalid scheme account id";
                            }
                        }else{
                            $result["SchAc_Error"][] = "Invalid scheme code :".$acData->schemeCode;
                        }
                        if($this->db->trans_status() === TRUE){
                            $this->db->trans_commit();
                        }else{
                            $this->db->trans_rollback();
                        }
                   }
                }else{
		            $logData = "\n".date('d-m-Y H:i:s')."\n API : user/getSchemeDetails \n Response : ".json_encode($resData,true)." \n Result : ".json_encode($result,true);
				    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
				}
            }else{
                $logData = "\n".date('d-m-Y H:i:s')."\n API : user/getSchemeDetails (Curl Failed) \n Response : ".json_encode($response,true)." \n Result : ".json_encode($result,true);
                file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
            }
            // Update last sync time
            $updData = array("last_sync_time" => date('Y-m-d H:i:s'));
            $this->integration_model->updateData($updData,'id_customer',$id_customer,'customer');
        	$result["khj_sync_status"] =  1;
        	$result["last_sync_time"] =  $updData['last_sync_time'];
        }
        $result["CUS_REF_NO"] =  "CUS REF NO : ".$cus_reference_no;
        echo json_encode($result);
    }
	               
	// .Khimji : Fetch user's offline data using ACME api & insert it in our database
	// Update Customer
    public function sendCusDataToOffline($id_customer){
        $result = [];
        if($this->config->item("integrationType") == 5 && $id_customer > 0){ 
            if (!file_exists('log/khimji')) {
                mkdir('log/khimji', 0777, true);
            }
            $log_path = 'log/khimji/'.date("Y-m-d").'.txt';      
            $this->load->model("integration_model");
            $cusData = $this->integration_model->getCusData($id_customer);
            $postData = array(
    						"appCustomerCode"	=> $cusData['app_cus_code'] ,
    						"customerCode"		=> $cusData['reference_no'] ,
    						"custName" 			=> $cusData['firstname'],
    						"mobileNo" 			=> $cusData['mobile'],
    						"emailId" 			=> $cusData['email'],
    						"preferdBranch" 	=> "",
    						"branchCode" 		=> "",
    						"custdetails" 		=> array(
    						                            "address1"  => $cusData['address1'] ,
        						                        "address2"  => $cusData['address2'],
        						                        "city"      => $cusData['city'] , 
        						                        "state"     => $cusData['state'], 
        						                        "panNo"     => $cusData['pan'],
        						                        "adharNo"   => $cusData['aadhaar_no']
        						                        )
    					);
            $response = $this->integration_model->khimji_curl('customerAction/2',$postData);
            if($response['status']){
               	$resData = $response['data'];
               	if($resData->status == 1 && $resData->errorCode == 0){
               			
                }else{
		            $logData = "\n".date('d-m-Y H:i:s')."\n API : customerAction/2 \n Post : ".json_encode($postData,true)."\n Response : ".json_encode($resData,true)." \n Result : ".json_encode($result,true);
				    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
				}
            }else{
                $logData = "\n".date('d-m-Y H:i:s')."\n API : customerAction/2 (Curl Failed) \n Post : ".json_encode($postData,true)."\n Response : ".json_encode($response,true)." \n Result : ".json_encode($result,true);
                file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
            }
            // Update last sync time
            $updData = array("last_sync_time" => date('Y-m-d H:i:s'));
            $this->integration_model->updateData($updData,'id_customer',$id_customer,'customer');
            $this->session->set_userdata("khj_sync_status",1); //1 - Synced
        }
        $result["CUS_REF_NO"] =  "CUS REF NO : ".$cus_reference_no;
        return $result;
    }
    
    /*scheme wise customer reports for current month 
    01-12-2022*/
    function customer_ledger_post()
	{ 
		$data = $this->get_values(); 
		//print_r($data);exit;
		$model = self::ADM_MODEL; 
		$from_date = $data['fromdate'];
		$to_date = $data['todate'];
		
		$result['data'] = $this->$model->customer_reports($data['id_employee'],$data['login_type']);  
		
	//	print_r($this->db->last_query());exit;
		if($result)
			{
			    $this->response($result, 200);
			}	 
			else
			{
				$msg = array('message' => 'No Records!'); 
				$this->response($msg, 200); 
			}
	
	}
	
	function customer_ledger_details_post()
	{
	    $data = $this->get_values(); 
		//print_r($data);exit;
		$model = self::ADM_MODEL; 
	
		$result = array();
		$customer_accounts = $this->$model->getCustomerSchAcc($data['id_customer']); 
		
		if($customer_accounts >0)
		{
		    foreach($customer_accounts as $ca)
		    {
		         $result[] = $this->mobileapi_model->chit_scheme_detail($ca['id_scheme_account']);
		    }
		   
		}
		
		if($result > 0)
			{
			    $this->response($result, 200);
			}	 
			else
			{
				$msg = array('message' => 'No Records!'); 
				$this->response($msg, 200); 
			}
	}
	
	function agentWise_monthly_reports_post()
	{
	    $data = $this->get_values(); 
		$model = self::ADM_MODEL; 
		$result = $this->$model->monthly_agent_reports($data['id_employee'],$data['login_type']);  
		
		if($result)
			{
			    $this->response($result, 200);
			}	 
			else
			{
				$msg = array('message' => 'No Records!'); 
				$this->response($msg, 200); 
			}
	}
	
	function pendingMsg_post()
	{
	    $data = $this->get_values(); 
		$model = self::ADM_MODEL;
		$insert_data = array(
		    "id_scheme_account" => $data["id_scheme_account"],
		    "remark" => $data["message"],
		    "entry_made_by" => ($data["login_type"] == 'AGENT' ? 1 : 2),
		    "id_agent" => $data["id_employee"],
		    "id_scheme_account" => $data["id_scheme_account"],
		    "date_created" =>date('Y-m-d H:i:s')
		    );
		$result = $this->$model->insertRemarks($insert_data);
		if($result)
			{
			    $msg = array('status' => TRUE,'message' => 'Remark added Successfully..'); 
			}	 
			else
			{
				$msg = array('status' => FALSE,'message' => 'Unable to proceed your request..'); 
			}
		$this->response($msg, 200);
		
	}


       function terms_and_conditions_get()
	{
	    $model = self::ADM_MODEL;
	    $result= $this->$model->get_terms_and_conditions(); 
		$result['general_terms'] = "For enrolling online purchase plan payment system, a customer should have valid mobile number
        Installments, Duration, Benefits and Terms would be varied in accordance with the plan. To know about each plans kindly visit respective sections
        Online payment status would be updated to the customer, once settlement done from payment gateway and such delay varies based on payment mode and concern banks
        Online payment system users can get the membership card at showroom
        On completion of plan, customer can purchase Jewellery at showroom only
        The company reserves the right to alter, amend, add or delete part or whole of the privileges of the plan without prior notice";
	    $result['about'] = " Every jewel has a tale to tell, so does the jeweller. Leading forefront in the heart of Madurai’s gold bazaar is our Jewelry brand offering fine, contemporary gold jewellery and silverware. Our brand builds on a legacy of over 3 decades of craftmanship with a beautifully designed huge collection of gold and diamond ornaments. Our entire family is passionate about preserving our traditional gold business. Our mission is to provide a personalised experience (or service) to every single customer. Antique and vintage designs are our speciality with over hundreds of on-trend jewels ready to enthral you at first sight.
        Our quality standards are etched in stone. Every single piece of our jewellery conforms with the Indian Standards of gold 916 BIS HALLMARK. 92.6% Sterling silverware and jewellery are also our trademarks. Certified, branded and guaranteed Diamond jewellery that is so vogue is part of our exquisite diamond collection. Our valuations are fair, on the spot and billing is transparent.
        The services we offer match our brand’s reputation by creating incredible experiences like video calling and smart car parking. Bringing you the latest trends from India’s certified best vendors to satiate your desire for fine gold is our greatest strength. We are located in 3 branches in Madurai and currently expanding as an effort to serve you better and to reach out to more jewel lovers out there.";
		$this->response($result,200);
	} 
	
	
	
	function branchesByEmp_post(){
	    $model = self::ADM_MODEL;
	    $data = $this->get_values();
	    
	    
	   
	    $id_employee = $data['id_employee'];
	    $id_customer = $data['id_customer'];
	    
	    $branches = $this->$model->get_branchesByEmp($id_employee,$id_customer);
	    
	    $this->response($branches,200);
	}
	
	function sch_enquiry_post()
    {
        $model = self::MOD_MOB;
		$data = $this->get_values();
		$sch_data=array(
		                'id_customer'     =>$data['id_customer'],
		                'id_scheme'      =>$data['id_scheme'],
		                'intresred_amt' =>(isset($data['intresred_amt']) ?$data['intresred_amt'] :0),
		                'intrested_wgt' =>(isset($data['intrested_wgt']) ?$data['intrested_wgt'] :0),
		                'message'       =>(isset($data['message']) ?$data['message'] :0),
		                'enquiry_date'     => date('Y-m-d H:i:s'),
		                );
		$customer = $this->$model->insert_sch_enquiry($sch_data);
		if($customer['status']==true)
		{
		            $cus_data=$this->$model->get_customerProfile($data['id_customer']);
		            $to = $this->comp['email']; 
    	        	$sch['type'] = 3;
    	        	$sch['name'] =$cus_data['firstname'];
    	        	$sch['amount']=$data['intresred_amt'];
    	        	$data['weight']=$data['interseted_weight'].' '.'Grams';
    	        	$cc =  'abinaya@vikashinfosolutions.com';
    	        	$data['company'] =$this->comp;
					$subject = "Reg.  ".$this->comp['company_name']." scheme Enquiry";
					$message = $this->load->view('include/emailScheme',$sch,true);
					$sendEmail = $this->email_model->send_email($to,$subject,$message,$cc,"");
		}
		 $this->response($customer,200);
    }
   
	function storeCollectionDevices_post(){
	    $model = self::ADM_MODEL;
	    $data = $this->get_values();
	    
	    $dev_arr = array(
	                      'device_uuid' => $data['uuid'],
	                      'device_type' => $data['device_type'],
	                      'date_add'  => date('Y-m-d H:i:s'),
	                    );

        $result = $this->$model->insert_collectionDevices($dev_arr);
	    $this->response($result,200);
	}
   

	
}	
?>