<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class ChitScheme extends CI_Controller {
	const VIEW_FOLDER = 'chitscheme/';
	const API_MODEL   = 'chitapi_model';
	const SERV_MODEL  = 'services_modal';
	public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model');
        $this->m_mode=$this->login_model->site_mode();
        if( $this->m_mode['maintenance_mode'] == 1) {
         	redirect("/user/maintenance");
	    }
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		ini_set('date.timezone', 'Asia/Kolkata');
		$this->load->model('scheme_modal');
		$this->load->model('email_model');
		$this->load->model('registration_model');
		$this->load->model('dashboard_modal');
		$this->load->model('services_modal');
		$this->load->model('sms_model');
		$this->load->model('payment_modal');
		$this->my_schemes = $this->dashboard_modal->countSchemes();
		$this->scheme_status = $this->scheme_modal->scheme_status();
		//$this->scheme_status = $this->scheme_modal->scheme_status();
		$this->reg_existing = $this->scheme_modal->regExistingScheme();
		$this->comp = $this->login_model->company_details();
	    $this->sms_data = $this->services_modal->sms_info();	
		$this->sms_chk = $this->services_modal->otp_smsavilable();
		$this->branch_settings =  $this->session->userdata('branch_settings');
		$this->log_dir = 'log/'.date("Y-m-d");
    	if (!is_dir($this->log_dir)) {
            mkdir($this->log_dir, 0777, TRUE); 
        }
    }
	public function index()
	{
	    if($this->scheme_status != 0)
		{
			redirect("/paymt");
		}
		else  
		{
			$this->	schemes();
		}	
	}
	public function schemes()
	{
		$scheme = $this->scheme_modal->empty_record();
		$chitSettings   = $this->scheme_modal->getChitSettings();
		/*$schCommodity 	= array();
		if($chitSettings['is_multi_commodity'] == 1){				
        	$schCommodity = $this->scheme_modal->getSchCommodity();
		}*/		
		$data['content'] = $scheme;
		$data['is_multi_commodity'] = $chitSettings['is_multi_commodity'];
		//$data['schCommodity'] = $schCommodity;
		$data['reg_existing'] = $this->reg_existing;
		$pageType = array('page' => 'schemes','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER.'schemes';
		//echo "<pre>";print_r($this->session->userdata('cus_id'));exit;
		$kyc = $this->scheme_modal->get_kyc_details();
		if($kyc['is_kyc_required']== 1){
        	$this->session->set_userdata('acc_name',$kyc['bank']['name']);			
		}
		$this->load->view('layout/template', $data);
	}
	

	//metal filter IN my scheme page//HH
    public function metal_report()
	{
		$id_metal  = $this->input->post('id_metal');
		$metal= $this->scheme_modal->get_scheme_detail($id_metal);
		echo json_encode($metal);
	}
	public function scheme_report()
	{
	    //Sync Existing Data					
   	    if($this->config->item("integrationType") == 3 || $this->config->item("autoSyncExisting") == 1){
		  $syncData = $this->sync_existing_data($this->session->userdata('mobile'),$this->session->userdata('cus_id'), $this->session->userdata('id_branch'),'myschemes');
	    } 
		$schemes		= $this->scheme_modal->get_scheme_detail('');
		$chitSettings   = $this->scheme_modal->getChitSettings();
		$schCommodity 	= array();
		/*if($chitSettings['is_multi_commodity'] == 1){				
        	$schCommodity = $this->scheme_modal->getSchCommodity();
		}*/	
		$schBranches 	= array();
		if($chitSettings['is_branchwise_cus_reg'] == 0 && $chitSettings['branch_settings'] == 1 && $chitSettings['branchwise_scheme'] == 1){
        	$schBranches = $this->scheme_modal->getSchBranches();
		}
		else if($chitSettings['is_branchwise_cus_reg'] == 0 && $chitSettings['branch_settings'] == 1 && $chitSettings['branchwise_scheme'] == 0){
			$branches = $this->scheme_modal->get_branch();
    		foreach($branches as $branch){
				$schBranches[] = array(
							         "cus_sch_ac" 	=> 0,
							         "name" 		=> $branch['name'],
							         "id_branch" 	=> $branch['id_branch'],
     								);
			}
		}
		$pageType = array('page' => 'dashboard','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);		
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content'] = array(
								'schemes'			=> $schemes,
								'delete_unpaid' 	=> $chitSettings['delete_unpaid'],
								'auto_debit' 		=> $chitSettings['auto_debit'],
								//'schCommodity' 		=> $schCommodity,
								'schBranches' 		=> $schBranches,
								'is_multi_commodity'=> $chitSettings['is_multi_commodity'],
								'branch_settings'	=> $chitSettings['branch_settings'],
								'is_branchwise_cus_reg' => $chitSettings['is_branchwise_cus_reg']
							);
		$data['fileName'] = self::VIEW_FOLDER.'my_schemes';
		$this->load->view('layout/template', $data);
	}
	
	function sync_existing_data($mobile,$id_customer,$id_branch,$page)
	{   
	    if($id_customer > 0 && strlen($mobile) == 10 ){
	       if (!is_dir($this->log_dir.'/existing')) {
	            mkdir($this->log_dir.'/existing', 0777, true);
	       }
	       $log_path = $this->log_dir.'/existing/'.date("Y-m-d").'.txt'; 
	       $allow_sync = false;
	       $last_sync_time = $this->registration_model->getLastSyncTime($mobile);
	       if(!empty($last_sync_time)){
	           $fifteen_min_earlier = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) - (15 * 60));
	           if(strtotime($last_sync_time) <= strtotime($fifteen_min_earlier)){
	               $allow_sync = true;
	           }else{
	               $allow_sync = false;
	           }
	       }else{
	           $allow_sync = true;
	       }
	       $this->registration_model->updateLastSyncTime($id_customer);
		   
	       if($allow_sync){
	    	   $data['id_customer'] = $id_customer;  
	    	   $data['id_branch'] = $id_branch;  
	    	   $data['branch_code'] = ($id_branch > 0 && $this->config->item("integrationType") == 4 ? $this->registration_model->getBranchCode($id_branch) : NULL);  
	    	   $data['branchWise'] = 0;  
	    	   $data['mobile'] = $mobile;  
	    	   $data['added_by'] = 2;
	    	   $res = $this->registration_model->insExisAcByMobile($data);  
	    	   $TESTRes = array("status" => "On ENTER", "e" => $this->db->_error_message() ,"q" => $this->db->last_query(), "res" => $res, "data" => $data);
	    	   $logData = "\n".date('d-m-Y H:i:s')."\n API : chitscheme \n Response : ".json_encode($TESTRes,true);
	    	   file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
	    	   if(sizeof($res) > 0)
	    	   {
	    	   		$payData = $this->registration_model->syncPayData($res);  
	    	   	    if((sizeof($payData['succeedIds']) > 0 || $payData['no_records'] > 0) && $this->db->trans_status() === TRUE){
	    				$status = $this->registration_model->updateInterTableStatus($res,$payData['succeedIds']);
	    				if($status === TRUE && $this->db->trans_status() === TRUE)
	    				{
	    				    $this->db->trans_commit();
	    					/*echo $this->db->_error_message();
	    					echo $this->db->last_query();*/
	    					return array("status" => TRUE, "msg" => "Purchase Plan registered successfully"); 
	    				}
	    				else{
	    				    $this->db->trans_rollback();
	    				    $response = array("status" => FALSE, "e" => $this->db->_error_message() ,"q" => $this->db->last_query() ,"msg" => "Error in updating intermediate tables");
	    				    $logData = "\n".date('d-m-Y H:i:s')."\n API : chitscheme \n Response : ".json_encode($response,true);
	                        file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
	    					return $response;
	    				}
	    			}
	    			else
	    			{
	    			    $response = array("status" => FALSE, "e" => $this->db->_error_message() ,"q" => $this->db->last_query() ,"msg" => "Error in updating payment tables");
	    			    $logData = "\n".date('d-m-Y H:i:s')."\n API : chitscheme \n Response : ".json_encode($response,true);
	    			    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
	    				$this->db->trans_rollback();
	    				return $response;
	    			}
	    	   }
	    	   else
	    	   {
	    	        $response = array("status" => FALSE, "id_customer" => $id_customer ,"mobile" => $mobile , "branch_code" => $data['branch_code'], "msg" => "No records to update in scheme account tables");
	    	        if($this->db->trans_status() === TRUE)
	    		    {
	    				$this->db->trans_commit();
	    		    }else{
	    		        echo $this->db->_error_message();
	    		        $this->db->trans_rollback();
	    		    }
	    		    $logData = "\n".date('d-m-Y H:i:s')."\n API : chitscheme \n Response : ".json_encode($response,true);
	    		    file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
	    	   		return $response;
	    	   } 
	    	}else{
	    	    $logData = "\n".date('d-m-Y H:i:s')."\n API : chitscheme \n sync called less than 15 min. CUS ID : ".$id_customer." | ".$mobile." | BRN ID :".$id_branch." | ".$page;
	    		file_put_contents($log_path,$logData,FILE_APPEND | LOCK_EX);
	    		return $logData;
	    	}
	    }else{
	        return array("status" => FALSE, "msg" => "Invalid customer data");
	    }
	}
	
	function scheme_account_report($id_scheme_account)
	{
		$this->load->model("scheme_modal");
		$account['gift'] = $this->scheme_modal->get_gift($id_scheme_account);
		$account['prize'] = $this->scheme_modal->get_prize($id_scheme_account); // HH
		$account['customer'] = $this->scheme_modal->get_account_details($id_scheme_account);
	    $account['customer']['metal_rate']=$this->payment_modal->getMetalRate($account['customer']['id_branch']);
		$account['payment'] = $this->scheme_modal->get_payment_detail($id_scheme_account);
		$account['details'] = $this->scheme_modal->get_details($id_scheme_account);

		$data['account']=$account;
		$data['content'] = $account;
	  	$pageType = array('page' => 'dashboard','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER.'scheme_acc_details';
		//echo "<pre>"; print_r($data['account']['details']); echo "<pre>";exit;
	    $this->load->view('layout/template', $data);
	}
	public function get_scheme($schemeType="")
	{
		echo $this->scheme_modal->get_scheme($schemeType);
	}	
/*Coded by ARVK*/
	public function get_classifications()
	{
		echo json_encode($this->scheme_modal->get_classifications());
	}
	public function get_classification($id)
	{
		echo json_encode($this->scheme_modal->get_classification($id));
	}
/* / Coded by ARVK*/
	public function get_avail_schemes()
	{
        echo json_encode($this->scheme_modal->getSchemes());
	}
	public function get_schemes()
	{
		$schemes = $this->scheme_modal->get_schemes();
		echo json_encode($schemes);
	}
	
	public function join_scheme()
	{
		$this->db->trans_begin();
		$scheme = $this->scheme_modal->join_scheme();
	
		if($scheme['is_enquiry']==0)
		{
			if($scheme['sch_data']['free_payment']==1 )
			{	
				$pay_insert_data = $this->free_payment_data($scheme['sch_data'],$scheme['insertID']);
				if($scheme['sch_data']['receipt_no_set']==1){
					//$this->db->query('LOCK TABLES payment WRITE');
				    $pay_insert_data['receipt_no'] = $this->payment_modal->generate_receipt_no($this->input->post('schemeID'),$scheme['id_branch']);
				}
				$pay_add_status = $this->payment_modal->addPayment($pay_insert_data);
				//$this->db->query('UNLOCK TABLES');
				$scheme_acc_no=$this->scheme_modal->accno_generatorset();
				if($scheme_acc_no['status']==1 && $scheme_acc_no['schemeacc_no_set']==0)
				{ 
					//$this->db->query('LOCK TABLES scheme_account WRITE');
					$scheme_acc_number=$this->payment_modal->account_number_generator($this->input->post('schemeID'),$scheme['id_branch'],'');
					if($scheme_acc_number!=NULL)
					{
						$updateData['scheme_acc_number']=$scheme_acc_number;
					}
					$updSchAc = $this->scheme_modal->update_account($updateData,$scheme['insertID']);
					//$this->db->query('UNLOCK TABLES');
			    }				
			}
			
			$serv_model = self ::SERV_MODEL;
			if($this->db->trans_status()===TRUE)
			{
				$this->db->trans_commit();
				$serviceID = 2;
				$service = $this->services_modal->checkService($serviceID);
				$company = $this->login_model->company_details();
				$schData = $this->scheme_modal->getJoinedScheme($scheme['insertID']);
				if($service['sms'] == 1)
				{
					$id=$scheme['insertID'];
					$data =$this->$serv_model->get_SMS_data($serviceID,$id);
					$mobile =$data['mobile'];
					$message = $data['message'];
					if($this->config->item('sms_gateway') == '1'){
		    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
		    		}
		    		elseif($this->config->item('sms_gateway') == '2'){
		    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
		    		}
		    		elseif($this->config->item('sms_gateway') == '3')
                    {
                    	$this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);	
                    }
                    elseif($this->config->item('sms_gateway') == '4')
                    {
                    	$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);	
                    }
                    elseif($this->config->item('sms_gateway') == '5')
                    {
                    	$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);	
                    }
				}
                if($service['serv_whatsapp'] == 1)
    			{
                	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                    $this->sms_model->send_whatsApp_message($mobile,$smsData);
                }
				if($service['email'] == 1 && isset($schData[0]['email']) && $schData[0]['email'] != '')
				{
					$data['schData'] = $schData[0];
					$data['company'] =$this->comp;
					$data['type'] = 1;
					$to = $schData[0]['email'];
					$subject = "Reg.  ".$this->comp['company_name']." scheme joining";
					$message = $this->load->view('include/emailScheme',$data,true);
					$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
				}
				if($scheme['sch_data']['free_payment']==1  )
				{
					$this->session->set_flashdata('successMsg','As Free Installment offer, 1st installment of your scheme credited successfully. Kindly pay your 2nd installment');
				}
				else
				{
					$this->session->set_flashdata('successMsg','Please proceed for the payment.');
				}
				redirect("/paymt");
			}
			else 
			{
				$this->db->trans_rollback();
				$serviceID = 10;
				$service = $this->services_modal->checkService($serviceID);
				$schData = $this->registration_model->get_cusData($this->session->userdata('username'));
				if($service['sms'] == 1)
				{
					$id = $this->session->userdata('username');
					$data =$this->$serv_model->get_SMS_data($serviceID,$id);
					$mobile=$id;
					if($this->config->item('sms_gateway') == '1'){
		    		    $this->sms_model->sendSMS_MSG91($mobile,$data['message'],'',$service['dlt_te_id']);		
		    		}
		    		elseif($this->config->item('sms_gateway') == '2'){
		    	        $this->sms_model->sendSMS_Nettyfish($mobile,$data['message'],'trans');	
		    		}
		    		elseif($this->config->item('sms_gateway') == '3')
	            	{
	                    $this->sms_model->sendSMS_SpearUC($mobile,$data['message'],'',$service['dlt_te_id']);	
	                }
	                elseif($this->config->item('sms_gateway') == '4'){
    					$this->sms_model->sendSMS_Asterixt($mobile,$data['message'],'',$service['dlt_te_id']);	
    				}
    				elseif($this->config->item('sms_gateway') == '5'){
    					$this->sms_model->sendSMS_Qikberry($mobile,$data['message'],'',$service['dlt_te_id']);	
    				}
				}
				
                if($service['serv_whatsapp'] == 1)
    			{
                	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                    $this->sms_model->send_whatsApp_message($mobile,$smsData);
                }
				if($service['email'] == 1 && isset($schData[0]['email']) && $schData[0]['email'] != '')
				{
					$data['schData'] = $schData[0];
					$data['type'] = -1;
					$to = $schData[0]['email'];
					$subject = "Reg. ".$this->comp['company_name']." scheme joining";
					$message = $this->load->view('include/emailScheme',$data,true);
					$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
				}
				$this->session->set_flashdata('errMsg','Error in joining the scheme.Please contact Customer care or try again later.');
				redirect("/chitscheme");
			}
    	}
    	else
    	{
			$this->session->set_flashdata('successMsg','Your Enquiry Details Submited Successfully.');
			$to = $this->comp['email']; 
			$data['type'] = 3;
			$data['name']=$scheme['name'];
			$data['amount']=$scheme['intresred_amt'];
			$data['weight']=$scheme['interseted_weight'].' '.'Grams';
			$cc =  'karthik@vikashinfosolutions.com';
			$data['company'] =$this->comp;
			$subject = "Reg.  ".$this->comp['company_name']." scheme Enquiry";
			$message = $this->load->view('include/emailScheme',$data,true);
			$sendEmail = $this->email_model->send_email($to,$subject,$message,$cc,"");
			redirect("/chitscheme/schemes");
    	}
	}
/* Coded by ARVK*/
	function free_payment_data($sch_data,$sch_acc_id)
	{
	    $sche_acc=$this->scheme_modal->get_acc($sch_acc_id);  // id_metal besed to taken silver/gold rate//HH
	    $metal_rate = $this->payment_modal->getMetalRate();
	    $rate_field = NULL;
        $metalRate  = NULL;
        $sql = $this->db->query("SELECT rate_field,market_rate_field FROM `ret_metal_purity_rate` where id_metal=".$sche_acc['id_metal']." and id_purity=1");
         //print_r($this->db->last_query());exit;
         if($sql->num_rows() == 1){
         $metalfields = $sql->row_array();
         $rate_field  = $metalfields['rate_field'];
         $metalRate   = ( $rate_field == null ? null : $metal_rate[$rate_field] );
         //print_r($metalRate);exit;
        }
		
		
		$gold_rate = number_format((float)$metal_rate['goldrate_22ct'], 2, '.', '');
		$gst_amt = 0;
		if($sch_data['gst'] > 0){
			$gst_amt = $sch_data['amount']*($sch_data['gst']/100); 
			if($sch_data['gst_type'] == 0){
				$converted_wgt = number_format((float)(($sch_data['amount']-$gst_amt)/$gold_rate), 3, '.', '');
			}
			else{
				$converted_wgt = number_format((float)($sch_data['amount']/$gold_rate), 3, '.', '');
			}
		}
		else{
			$converted_wgt = number_format((float)($sch_data['amount']/$gold_rate), 3, '.', '');
		}
		$fxd_wgt = $sch_data['max_weight'];
		//print_r($sche_acc);exit;
		$insertData = array(
								"gst"	 			 => $sch_data['gst'],
								"gst_type"	 		 => $sch_data['gst_type'],
								"id_scheme_account"	 => $sch_acc_id,
								"id_employee"	 	 => NULL,
								"date_payment" 		 => date('Y-m-d H:i:s'),
								"payment_type" 	     => "Cost free payment", 
								"payment_mode" 	     => "FP", 
								"act_amount" 	     => $sch_data['amount'], 								
								"payment_amount" 	 => $sch_data['amount'], 
								"due_type" 	         => 'D', 
								"no_of_dues" 	     => '1', 								
							//	"metal_rate"         => ($sche_acc['id_metal']==1? $metal_rate['goldrate_22ct'] :$metal_rate['silverrate_1gm']),
							     'metal_rate'            => $metalRate,
								"metal_weight"       => ($sch_data['scheme_type']==2 ? $converted_wgt : ($sch_data['scheme_type']==1 ? $fxd_wgt : 0.000)),
								"remark"             => "Paid by ".$sch_data['company_name'],
								"installment"        => 1, // only for 1st ins free
								"payment_status"     => ($sch_data['approvalReqForFP'] == 1 ? 2 :1),
								"id_branch"          => ($sche_acc['id_branch']? $sche_acc['id_branch'] :NULL),
								"added_by"           =>1
							);
							//print_r($insertData);exit;
					return 	$insertData;	
	}
		
	function close_scheme()
	{
		$schemeClose = $this->scheme_modal->get_closeScheme();
		$data['content'] = $schemeClose;
		$pageType = array('page' => 'closeScheme','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER.'close_scheme';
		$this->load->view('layout/template', $data);
	}
	function delete_account($id_scheme_account)
	{
			$payid=$this->scheme_modal->get_payment($id_scheme_account);
			$status = $this->scheme_modal->delete_payment($payid['id_payment']);	
			$status = $this->scheme_modal->delete_scheme_account($id_scheme_account); 
		  if($status['status']=='1')
		  {
		  	$this->session->set_flashdata('successMsg','Your scheme account deleted successfully');
		  	redirect('/chitscheme/scheme_report');
		  }
		  else
		  {
		  	$this->session->set_flashdata('successMsg','Unable to proceed your request');
		  	redirect('/chitscheme/scheme_report');
		  }
	}
	/* Join into exisitng account details.*/
	function join_existing_byacc()
	{
	   $scheme_acc  = array("group_code" => (isset($_POST['group_code']) ? $_POST['group_code'] : $_POST['scheme_code']),
		                    "scheme_acc_number" => ($_POST['scheme_acc_number']!=''? $_POST['scheme_acc_number']:''),
		                    "account_name" => ($_POST['account_name']!=''? $_POST['account_name']:''),
		                     "pan_no" => ($_POST['exis_pan_no']!=''? $_POST['exis_pan_no']:''),
							"id_branch" => (isset($_POST['id_branch']) ? ($_POST['id_branch']==''? NULL:$_POST['id_branch']):NULL)
							);
		if($_POST['regExistingReqOtp'] == 1)
		{
		    $acc = $this->scheme_modal->isAccExist($scheme_acc);
			if($acc['status']){				
				$sendotp = $this->generateOTP($acc['mobile'],$acc['email'],$acc['name']);	
				if($sendotp){
					//Join type 1-> By account details 2-> By Mobile number
					$data = array('join_type' => 1, 'acc_data' =>$scheme_acc); 
					$this->session->set_userdata($data);							
					echo json_encode(array('success' => TRUE, 'message' => 'OTP has been sent to registered mobile number.'));									
				}
			}
			else
			  {
			  	$this->session->set_flashdata('successMsg','Unable to proceed your request');
			  	echo json_encode(array('success' => FALSE, 'message' => $acc['msg']));
			  }
	  	}
	}
	/* Join into exisitng account by using existing registered mobile number*/
	function join_existing_bymob($mobno){
		$chit['scheme_mob_number'] = $mobno;
		$chit['id_branch']=$this->session->userdata('id_branch');
	    $acc = $this->scheme_modal->isAccExist_bymobile($chit);
		if($acc['status']){
			$sendotp = $this->generateOTP($acc['mobile']);
						if($sendotp){
							//Join type 1-> By account details 2-> By Mobile number
							$data = array('join_type' => 2, 'mobile_no' => $mobno); 
							$this->session->set_userdata($data);
							echo json_encode(array('success' => TRUE, 'message' => 'OTP has been sent to registered mobile number.'));									
						}
		}
		else
		  {
		  	$this->session->set_flashdata('successMsg','Unable to proceed your request');
		  	echo json_encode(array('success' => FALSE, 'message' => $acc['msg']));
		  }
	}
	function join_existing()
	{	
	 	 $chit  = array(	"id_scheme_group"=>($_POST['id_scheme_group']!=''? $_POST['id_scheme_group']:''),
               "id_scheme" => ($_POST['id_scheme']!=''? $_POST['id_scheme']:''),
               "scheme_acc_number" => ($_POST['scheme_acc_number']!=''? $_POST['scheme_acc_number']:''),
               "account_name" => ($_POST['account_name']!=''? $_POST['account_name']:''),
               "group_code" => (isset($_POST['group_code'])? $_POST['group_code']:NULL),
				"id_branch" => (isset($_POST['id_branch']) ? ($_POST['id_branch']==''? NULL:$_POST['id_branch']):NULL));
	    $acc = $this->scheme_modal->verify_existing($chit);	 
	    if($acc == FALSE)
	    {
    	 	$scheme_acc  = array(   "id_scheme" => ($chit['id_scheme']!=''?$chit['id_scheme']:0),
    	 							"id_scheme_group" => ($chit['id_scheme_group']!=''?$chit['id_scheme_group']:0),
				                	"id_customer" => $this->session->userdata('cus_id'),
				                	"scheme_acc_number" => ($chit['scheme_acc_number']!=''? $chit['scheme_acc_number']:NULL),
				                	"ac_name" => ($chit['account_name']!=''?$chit['account_name']:NULL),
				                	"pan_no" => ($chit['pan_no']!=''?strtoupper($chit['pan_no']):''),
				                	"id_branch" => ($chit['id_branch']!=''?$chit['id_branch']:NULL),
				                	"id_employee" => NULL,
				                	"added_by" => 0,
				                	"date_add" => date('Y-m-d H:i:s'),
				                	"status" => 0 // processing
				             	);
		  $status =	$this->scheme_modal->join_existing($scheme_acc);    
		 // echo $this->db->last_query();exit;
		  if($status['status'])
		  {
		  	$this->session->set_flashdata('successMsg','Successfully Registered, kindly wait till your account activation..'); 
		  	redirect('/chitscheme/exisRegReq');
		  }
		  else
		  {
		  	$this->session->set_flashdata('successMsg','Unable to proceed your request..');
		  	redirect('/chitscheme/schemes');
		  }
	  }
	  else
	  {
	  	if($acc['table'] == 'scheme_account'){
			$msg = 'Your account already exist, Please contact customer care to proceed your request';
			$this->session->set_flashdata('successMsg',$msg);
		}else{
			$msg = 'You have already sent request for this account... Please wait until we verify your account. Check dashboard for status.';
			$this->session->set_flashdata('errMsg',$msg);
		}
		  	redirect('/chitscheme/schemes');
	  }
	}
	function join_existing_old()
	{
		$chit  = array("group_code" => ($_POST['group_code']!=''? $_POST['group_code']:''),
		                    "scheme_acc_number" => ($_POST['scheme_acc_number']!=''? $_POST['scheme_acc_number']:''),
		                    "account_name" => ($_POST['account_name']!=''? $_POST['account_name']:''),
		                     "pan_no" => ($_POST['exis_pan_no']!=''? $_POST['exis_pan_no']:''),
							"id_branch" => (isset($_POST['id_branch']) ? ($_POST['id_branch']==''? NULL:$_POST['id_branch']):NULL));
	    $acc = $this->scheme_modal->verify_existing($chit);
	    $id_scheme = $this->scheme_modal->getschId($chit);
	    if(!$acc)
	    {
			$scheme_acc  = array("id_scheme" => ($id_scheme!=NULL?$id_scheme:0),
				                    "id_customer" => $this->session->userdata('cus_id'),
				                    "scheme_acc_number" => ($chit['scheme_acc_number']!=''? $chit['scheme_acc_number']:''),
									"id_branch" => (isset($_POST['id_branch']) ? ($_POST['id_branch']==''? NULL:$_POST['id_branch']):NULL),
				                    "account_name" => ($chit['account_name']!=''?ucfirst($chit['account_name']):''),
				                     "pan_no" => ($chit['exis_pan_no']!=''?ucfirst($chit['exis_pan_no']):''),
				                    "start_date" => date('Y-m-d H:i:s'),
				                    "date_add" => date('Y-m-d H:i:s'),"is_new" => 'N', "active" => 1);
		  $status =	$this->scheme_modal->join_existing($scheme_acc);
		  if($status)
		  {
				$serviceID = 12;
				$service = $this->services_modal->checkService($serviceID);
				$company = $this->login_model->company_details();
				$customer = $this->registration_model->get_cusData($this->session->userdata('username'));
				$this->db->trans_begin();
				if($service['sms'] == 1)
				{
					$id=$scheme_acc['scheme_acc_number'];
					$data =$this->services_modal->get_SMS_data($serviceID,$id);
					$mobile =$data['mobile'];
					$message = $data['message'];
					if($this->config->item('sms_gateway') == '1'){
		    		    $this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);		
		    		}
		    		elseif($this->config->item('sms_gateway') == '2'){
		    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
		    		}
		    		elseif($this->config->item('sms_gateway') == '3')
                	{
                        $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);	
                    }
                    elseif($this->config->item('sms_gateway') == '4')
                    {
                    	$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);	
                    }
                    elseif($this->config->item('sms_gateway') == '5')
                    {
                    	$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);	
                    }
				}
				if($service['serv_whatsapp'] == 1)
    			{
                	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                    $this->sms_model->send_whatsApp_message($mobile,$smsData);
                }
				if($service['email'] == 1 && isset($customer[0]['email']) && $customer[0]['email'] != '')
				{
					$data['schData'] = $customer[0];
					$data['accData'] = $scheme_acc;
					$data['company'] =$this->comp;
					$data['type'] = 2;
					$to = $customer[0]['email'];
					$subject = "Reg.  ".$this->comp['company_name']." existing scheme registration";
					$message = $this->load->view('include/emailScheme',$data,true);
					$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
				}
		  	$this->session->set_flashdata('successMsg','Your Existing savings scheme registered successfully');
		  	redirect('/paymt');
		  }
		  else
		  {
		  	$this->session->set_flashdata('successMsg','Unable to proceed your request');
		  	redirect('/chitscheme/schemes');
		  }
	  }
	  else
	  {
		  	$this->session->set_flashdata('successMsg','Your account already exist in other number, Please contact customer care to proceed your request');
		  	redirect('/chitscheme/schemes');
	  }
	}
	public function generateOTP($mobile,$email="",$name="")
	{
		   if(strlen(trim($mobile)) == 10)
		   {
					$this->session->unset_userdata("OTP");
					$OTP = mt_rand(100000, 999999);
					$this->session->set_userdata('OTP',$OTP);
					$this->session->set_userdata('rate_fixing_otp_exp',time()+10);
					$message = $OTP." is the verification code from ".$this->comp['company_name']." saving scheme.Please use this code to verify your mobile number";
					if($this->config->item('sms_gateway') == '1'){
    		    		$this->sms_model->sendSMS_MSG91($mobile,$message,'','');		
		    		}
		    		elseif($this->config->item('sms_gateway') == '2'){
		    	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
		    		}
		    		elseif($this->config->item('sms_gateway') == '3')
                	{
                        $this->sms_model->sendSMS_SpearUC($mobile,$message,'','');	
                    }
                    elseif($this->config->item('sms_gateway') == '4')
                    {
                    	$this->sms_model->sendSMS_Asterixt($mobile,$message,'','');	
                    }
                    elseif($this->config->item('sms_gateway') == '5')
                    {
                    	$this->sms_model->sendSMS_Qikberry($mobile,$message,'','');	
                    }
		    		if($service['serv_whatsapp'] == 1)
        			{
                    	$smsData = ["message" => $message, "template_name" => $service['template_name'], "params" => $data['params']];
                        $this->sms_model->send_whatsApp_message($mobile,$smsData);
                    }
				//	$this->send_mail($mobile,$email,$name,$OTP);
					return TRUE;	
			}
			else
			{
			    	return FALSE;
			}
			
	}
public function send_mail($mobile,$email="",$name="",$OTP="")
	{
						 $to = $email;	
						$data['otp']=$OTP;
						$data['type'] = 20;
						$data['company_details'] = $this->comp;
						$data['name'] = $name;
						$subject = "Reg: ".$this->comp['company_name']." Existing OTP Verification";
						$message = $this->load->view('include/emailAccount',$data,true);
						$this->load->model('email_model');
						$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
						return TRUE;
	}
	function req_closeScheme()
	{
			$closeSch = $this->scheme_modal->close_scheme();
			if($closeSch)
			{
				if($this->input->post('remark_close'))
					$this->session->set_flashdata('successMsg','Your request submitted to admin');
				else
					$this->session->set_flashdata('successMsg','Your request cancelled.');
				redirect('/chitscheme/close_scheme');
			}
			else
			{
				if($this->input->post('remark_close'))
					$this->session->set_flashdata('errMsg','Error in submitting your request. Please try again later');
				else
					$this->session->set_flashdata('errMsg','Error in cancelling. Please try again later');
				redirect('/chitscheme/close_scheme');
			}
	}
//Promotion sms and otp setting
	function send_sms($mobile,$message)
	{
				$url = $this->sms_data['sms_url'];
				$senderid  = $this->sms_data['sms_sender_id'];
	   if(($this->sms_chk['debit_sms']!=0 )){
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
		return FALSE;}
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
 //Promotion sms and otp setting
	public function submit_schreg_bymobile_otp()
	{
		if($this->session->userdata('OTP') == $this->input->post('otp'))
			{
				$this->session->unset_userdata('OTP');
				$this->db->trans_begin();
	   			$data['id_branch'] = $this->session->userdata('id_branch');
	   			$data['scheme_mob_number'] = $this->session->userdata('mobile_no');
				$data['id_customer'] = $this->session->userdata('cus_id');
	   		    $res = $this->scheme_modal->insertExisAccData_bymobileno($data);
			    if($res['status'])
			    {
			   		$payData = $this->scheme_modal->insert_paymentData_bymobile($res['data']);
			   		if($payData['status']){
						$update = $this->scheme_modal->updateOfflineData_bymobile($res['data']);
				   		if($this->db->trans_status()===TRUE)
						{
							$this->db->trans_commit();
					   		$this->session->set_flashdata('successMsg','Scheme account registered successfully.');
							redirect("/chitscheme/schemes");
				   		}
				   		else{
							$this->db->trans_rollback();
							$this->session->set_flashdata('successMsg','Unable to proceed your request,contact customer care.');
							redirect("/chitscheme/schemes");
						}
					}
					 else
					   {
					   		$this->db->trans_rollback();
							$this->session->set_flashdata('successMsg','Unable to proceed your request,contact customer care.');
							redirect("/chitscheme/schemes");
					   }
			   }
			   	else
				{
					$this->session->set_flashdata('successMsg','Unable to proceed your request,contact customer care.');
					redirect("/chitscheme/schemes");
				}
			}
	}
	public function submit_schregOtp()
	{
			if($this->session->userdata('OTP') == $this->input->post('otp'))
			{
				$this->session->unset_userdata('OTP');
				$this->db->trans_begin();
				$data = $this->session->userdata('acc_data');
				$data['id_customer'] = $this->session->userdata('cus_id');
				//print_r($data);
	   		    $res = $this->scheme_modal->insertExisAccData($data);
	  // 	  print_r($res);
			   if($res['status'])
			   {
			   		$payData = $this->scheme_modal->insert_paymentData($res['data'],$res['insertID']);
			   	    if($payData['status']){
					  $this->scheme_modal->updateInterTableStatus($res['data'],$res['insertID'],$payData['suceedIds']);
				   		if($this->db->trans_status()===TRUE)
						{
							$this->db->trans_commit();
					   		$this->session->set_flashdata('successMsg','Scheme account registered successfully.');
							redirect("/chitscheme/schemes");
				   		}
				   		else{
							$this->db->trans_rollback();
							$this->session->set_flashdata('successMsg','Unable to proceed your request,contact customer care.');
							redirect("/chitscheme/schemes");
						}
					}
					 else
					   {
					   		$this->db->trans_rollback();
							$this->session->set_flashdata('successMsg','Unable to proceed your request,contact customer care..');
							redirect("/chitscheme/schemes");
					   }
			   }
			   	else
				{
					$this->session->set_flashdata('successMsg','Unable to proceed your request,contact customer care...');
					redirect("/chitscheme/schemes");
				}
			}	
			else
			{
				$this->session->set_flashdata('successMsg','Unable to proceed your request,contact customer care....');
				redirect("/chitscheme/schemes");
			}
	}
	
	//closed accounts listing based on tab[branches] selection  //HH
	public function closed_accounts()
	{
		$closed = $this->scheme_modal->get_closed_account();
		$chitSettings   = $this->scheme_modal->getChitSettings();
		$branches 	= array();
		    $active_tab 	= NULL;
		    if($chitSettings['branch_settings'] == 1){				
	        	$branches = $this->payment_modal->branchesData();
			}
		
		$data = array('closed' => $closed);
		$pageType = array('page' => 'dashboard','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);		
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
	//	$data['content']	 = $closed;
		
		$data['content'] = array(
								'closed'			=> $closed,
								'branches' 		=> $branches,
								'is_multi_commodity'=> $chitSettings['is_multi_commodity'],
								'is_branchwise_cus_reg'=> $chitSettings['is_branchwise_cus_reg'],
								'branchwise_scheme'=> $chitSettings['branchwise_scheme'],
								'branch_settings'=> $chitSettings['branch_settings']
							);
		
		$data['fileName'] = self::VIEW_FOLDER.'closed_accounts';
		$this->load->view('layout/template', $data);
	}
 	/* public function referralcode_check()
	   {
		 $code=$this->input->post('referal_code');		 
		 $usercode=$this->session->userdata('username');		 
	     if($code!=''&& $usercode!=$code){
			 $status = $this->scheme_modal->checkreferral_code($code);
			 if($status){
			 echo 1;
			}else{
		   echo 0;
		    } 
		 }else{
		   echo 0;
		 } 
	   } */
	    public function referralcode_check()
	   {
		 $code=$this->input->post('referal_code');		 
		 $usercode=$this->session->userdata('username');
		 $cus_id=$this->session->userdata('cus_id');
	     if($code!=''&& $usercode!=$code)
	     {
			 $data = $this->scheme_modal->checkreferral_code($code,$cus_id);
			echo json_encode($data);
		 }
		 else
		 {
		   $data=array('status'=>false ,'msg'=>'Invalid Referral Code');
		  	 echo json_encode($data);
		 } 
	   } 
// branch name list 

public function get_metal()
{
$data=$this->scheme_modal->get_metal();
	echo json_encode($data);
} 
public function get_branch()
{
$data=$this->scheme_modal->get_branch();
	echo json_encode($data);
} 
public function getSchJoinBranches($id_scheme)
{
    $data=$this->scheme_modal->getSchJoinBranches($id_scheme);
	echo json_encode($data);
}

public function get_branch_settings()
{
$data=$this->scheme_modal->get_branch_settings();
	echo json_encode($data);
}
// branch name list 
// otp and promotion
function update_smscount()
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
// otp and promotion
	public function exisRegReq()
	{
			$exisRegReq = $this->dashboard_modal->get_exisRegReq();
			$dboardData = array('exisRegReq' => $exisRegReq);
			$pageType = array('page' => 'dashboard','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['content'] = $dboardData;
			$data['fileName'] = self::VIEW_FOLDER.'existingRegRequests';
			$this->load->view('layout/template', $data);
	}
	public function get_groups()
	{
        $id_scheme = $this->input->post('id_scheme');
        $id_branch = (!empty($this->session->userdata('id_branch'))?$this->session->userdata('id_branch'):0);
		$scheme = $this->scheme_modal->get_groups($id_scheme,$id_branch);
		echo json_encode($scheme);
	}
	/* function name(){
		$schData = $this->scheme_modal->getJoinedScheme(8);
				echo "<pre>";print_r($schData);echo "</pre>";exit;
				if($schData[0]
					$data['schData'] = $schData[0];
					$data['company'] =$this->comp;
					$data['type'] = 1;
					$to = $schData[0]['email'];
					$subject = "Reg.  ".$this->comp['company_name']." scheme joining";
					$message = $this->load->view('include/emailScheme',$data,true);
					echo "<pre>";print_r($message);echo "</pre>";exit; 
					$sendEmail = $this->email_model->send_email($to,$subject,$message,"","");
	} */
	//Catalog list for site
	public function parentcategory()
	{
		$data=$this->CategoryList();
		echo json_encode($data);
	}
	public function CategoryList()
	{
		$currnet = 1;
		$current_class = ($currnet == 1) ? "class='dropdown-submenu'" : '';
		$list = "";
		$sql = $this->db->query('SELECT id_category, categoryname  as name,id_parent FROM category WHERE id_parent = 1 limit 1');
		$parent = $sql->result_array();			
		$mainlist = "<ul class='dropdown-menu'>";		
		foreach ($parent as $pval){
			$mainlist .= $this->CategoryTree($list,$pval,$append = 0, $currnet);
		}
		$mainlist .= "</ul>";
		return $mainlist;
	}
	public function CategoryTree($list,$parent,$append, $currnet)
	{
		$parent_detalis = $this->hasChild($parent['id_category']);
		//$current_class = ($parent_detalis>0) ? "class='dropdown-submenu'" :'';
		$list = '<li class="dropdown-submenu dropdown-reverse" id='.$parent['id_category'].'><a href="collection.php?id_category='.$parent['id_category'].'" class="dropdown-item dropdown-toggle">'.$parent['name'].'<i class="menu-arrow"></i></a>';
		if ($parent_detalis) // check if the id has a child
		{
			$append++; // this is our basis on what level is the category e.g. (child1,child2,child3)
			$sql = $this->db->query("SELECT id_category, categoryname  as name,id_parent FROM category WHERE id_parent = ".$parent['id_category']);
			$child = $sql->result_array();
			if(count($child)>0) {
			$list .= "<ul class='dropdown-menu child child".$append." '>";
			foreach ($child as $chval){
				$list .= $this->CategoryTree($list,$chval,$append, $currnet);
			}			
			$list .= "</ul>";
			}
		}
		$list .='</li>';
		return $list;
	}
	public function hasChild($parent_id)
	{
		$sql = $this->db->query("SELECT COUNT(*) as count FROM category WHERE id_parent = ".$parent_id);
		return $sql->num_rows();
	}
	public function get_category_list()
{
		$id_category = $this->input->post('id_category');
		$product_records = array();
		$data = array();
		// Passing Category Id based Product records
		$sql = "SELECT id_product,pro.id_category as id_category,productname as product_name, ifnull(pro.description, '') as description, if(pro.active = 1,'Active','Inactive') as active,proimage,weight,size,type,price,code,purity
			 FROM products pro where pro.id_category='".$id_category."' and pro.active=1";
		$products_detalis = $this->db->query($sql)->result_array();;
		if(count($products_detalis)>0) {
				foreach($products_detalis as $pro) {
				$product_records[]= array(
				'id_product'=>$pro['id_product'],
				'id_category'=>$pro['id_category'],
				'product_name'=>$pro['product_name'],
				'proimage'=>$pro['proimage'],
				'description'=>$pro['description']
				);
			}
		} 
		// Parent to child based Products lists
		$sql = "SELECT id_category, catimage as image ,categoryname as category_name,id_parent, ifnull(description, '') as description, if(active = 1,'Active','Inactive') as active
		FROM category cat where id_parent='".$id_category."' and cat.active=1";
		$category = $this->db->query($sql)->result_array();
		if(count($category) > 0) {
			foreach($category as $cat)
			{
					$sql = "SELECT id_product,pro.id_category as id_category,productname as product_name, ifnull(pro.description, '') as description, if(pro.active = 1,'Active','Inactive') as active,proimage,weight,size,type,price,code,purity
					 FROM products pro where pro.id_category='".$cat['id_category']."' and pro.active=1";
					$products = $this->db->query($sql)->result_array();
					if(count($products) >0 ) {
					 foreach($products as $pro) {
							$product_records[]= array(
							'id_product'=>$pro['id_product'],
							'id_category'=>$pro['id_category'],
							'product_name'=>$pro['product_name'],
							'proimage'=>$pro['proimage'],
							'description'=>$pro['description']
							);
					}	
				}
           $data=$this->get_parent_product_list($product_records,$cat,$append,$currnet);
	   } 
  }
   if(sizeof($data)>0)
		   {
			  echo json_encode($data);
		   }else{
			   echo json_encode($product_records);
		   }
}
function  get_parent_product_list($product_records,$cat,$append,$currnet)
{
 /* 
    $current_class = ($currnet == $parent['id_category']) ? "class='jstree-open'  data-jstree='{'disabled' : true,'opened' :true, 'selected' :true}'" : '';
		//$current_class ="";
		$list = '<li '.$current_class.' id='.$parent['id_category'].'>'.$parent['name'];
		if ($this->hasChild($parent['id_category'])) // check if the id has a child
		{
			$append++; // this is our basis on what level is the category e.g. (child1,child2,child3)
			$list .= "<ul class='child child".$append." '>";
			$sql = $this->db->query("SELECT id_category, categoryname  as name,id_parent FROM category WHERE id_parent = ".$parent['id_category']);
			$child = $sql->result_array();
			foreach ($child as $chval){
				$list .= $this->CategoryTree($list,$chval,$append, $currnet);
			}			
			$list .= "</ul>";
		}
		$list .='</li>';
		return $list; */
 /* echo "<pre>";
  print_r($cat['id_category']);
  echo "</pre>";
  echo "<br />";*/
	$sql = "SELECT id_category, catimage as image ,categoryname as category_name,id_parent, ifnull(description, '') as description, if(active = 1,'Active','Inactive') as active
	FROM category cat where id_parent='".$cat['id_category']."' and cat.active=1";
    $category = $this->db->query($sql)->result_array();
    if(count($category) > 0) {
			foreach($category as $cat)
			{
                    $append++;
					$sql = "SELECT id_product,pro.id_category as id_category,productname as product_name, ifnull(pro.description, '') as description, if(pro.active = 1,'Active','Inactive') as active,proimage,weight,size,type,price,code,purity
					 FROM products pro where pro.id_category='".$cat['id_category']."' and pro.active=1";
					$products = $this->db->query($sql)->result_array();
					if(count($products) >0 ) {
					 foreach($products as $pro) {
							$product_records[]= array(
							'id_product'=>$pro['id_product'],
							'id_category'=>$pro['id_category'],
							'product_name'=>$pro['product_name'],
							'proimage'=>$pro['proimage'],
							'description'=>$pro['description']
							);
					}	
				}
               $this->get_parent_product_list($product_records,$cat,$append,$currnet);
           }
    }
 return $product_records;
}
	public function ajax_getproducts()
	{ 
		$model = self::SERV_MODEL;
		$id_product = $this->input->post('id_product');	
		$data = $this->$model->get_products($id_product);  
		echo json_encode($data);
	}
	public function product_enquriy()
    {	   
        $model = self::SERV_MODEL;
        $insData = array('mobile'       =>$this->input->post('mobile'),
                         'first_name'   =>$this->input->post('first_name'),
                         'last_name'    => $this->input->post('last_name'),
                         'email'  	    => $this->input->post('email'),
                         'mobile'  	    => $this->input->post('mobile'),
                         'id_product'  	    => $this->input->post('id_product'),
                         'date_add'     => date('Y-m-d H:i:s'),
                         'message'  	=> $this->input->post('message'));
        $result = $this->$model->insProduct_enquiry($insData);
        if($result['status'] == true){
           // $to = $this->comp['email']; 
            $to = "karthik@vikashinfosolutions.com"; 
            //$bcc ="pavithra@vikashinfosolutions.com";	
            $bcc="";
            $subject = "Reg - ".$this->comp['company_name']." customer enquiry"; 
            $message = $this->load->view('include/emailEnquiry',$insData,true); 
            $sendEmail = $this->email_model->send_email($to,$subject,$message,$bcc,"");
			$data=array('status' => true ,'msg' => 'Thanks for your Enquiry');
            echo json_encode($data);
        } else {
			$data=array('status' => false ,'msg' => 'Please try after sometime');
            echo json_encode($data);
        } 
    }
    
	//Catalog list for site
	//GG bank pan and aadhar details  view
	function kyc_form()
	{
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		else
		{
			
			$kyc = $this->scheme_modal->get_kyc_details();
            //print_r($kyc);
			$data = array('kyc' => $kyc);
			
			$pageType = array('page' => 'kyc_form','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);

			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['content'] = $data;
			$data['fileName'] = self::VIEW_FOLDER.'kyc_form';
			$this->load->view('layout/template', $data);
		}
	}
	function kyc_details(){
		$cus_id = $this->session->userdata('cus_id');
		$approval_type = "Auto";
	
		if($_POST['type']=='add'){  
			if($_POST['form_type']==1){
				$bank = $_POST;
				$kyc_detail = array(
					'id_customer'    	 => $cus_id,
					'kyc_type'    	 	 => $bank['form_type'],	
					'number'    	 	 => (isset($bank['bank_acc_no'])?$bank['bank_acc_no']:NULL),
//					'name'    	 		 => (isset($bank['name'])?$bank['name']:NULL),
//					'bank_branch'    	 => (isset($bank['bank_branch'])?$bank['bank_branch']:NULL),
					'bank_ifsc'    		 => (isset($bank['ifsc'])?$bank['ifsc']:NULL),
					'date_add'			 =>  date('Y-m-d H:i:s')
					);
				if($approval_type == "Auto"){
					$response = $this->zoopapiCurl("verify-bank",array("Account"=>$bank['bank_acc_no'],"IFSC"=>$bank['ifsc']));
					if(isset($response->statusCode)){ 
						$data['status'] = FALSE; 
						$data['msg'] = $response->message;
						$data['kyc_status'] = 0 ;
					}
					else{
						if($response->transaction_status == 1){
							if($response->data->Status == "VERIFIED"){
								$kyc_detail['status'] = 2;
								$kyc_detail['name'] = $response->data->BeneName;
								$kyc_detail['verification_type'] = 2; // 1-Manual,2-Auto
								$data = $this->scheme_modal->kyc_insert($kyc_detail); 
								if($data['status'] == TRUE){
									$this->load->model("mobileapi_model");
									$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
									$data['msg'] = "Bank Account verified successfully";
								} 
							}else{
								$data['kyc_status'] = 0 ;
								$data['status'] = FALSE; 
								$data['msg'] = "Invalid Bank Details "; 
							}
						}elseif($response->transaction_status == 2){
							$data['status'] = FALSE; 
							$data['kyc_status'] = 0 ;
							if(isset($response->data->Remark)){
								$data['msg'] = $response->data->Remark; 
							}else{
								$data['msg'] = $response->response_message;
							}
							
						}
						
					}	
				}else{
				    $kyc_detail['verification_type'] = 1; // 1-Manual,2-Auto
					$data = $this->scheme_modal->kyc_insert($kyc_detail);
					if($data['status'] == TRUE){
						$this->load->model("mobileapi_model");
						$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
						$data['msg'] = "Bank Account details submitted successfully. Verification in progress. Keep in touch with us.";
					}else{
						$data['kyc_status'] = 0 ;
						$data['status'] = FALSE; 
						$data['msg'] = "Invalid Bank Details "; 
					}  
				}
			}else if($_POST['form_type']==2){
				$pan = $_POST;
				$kyc_detail = array(
					'id_customer'    	 => $cus_id,
					'kyc_type'    	 	 => $pan['form_type'],	
					'number'    	 	 => (isset($pan['pan_no'])?$pan['pan_no']:NULL),
					'name'    	 		 => (isset($pan['pan_card_name'])?$pan['pan_card_name']:NULL),
					'date_add'			 =>  date('Y-m-d H:i:s')
					);
				if($approval_type == "Auto"){
					$response = $this->zoopapiCurl("pan",array("pan" => $pan['pan_no']));
					if(isset($response->statusCode)){
						$data['status'] = FALSE; 
						$data['msg'] = $response->message;
						$data['kyc_status'] = 0 ;
					}
					elseif($response->response_code == 1){
						$res = $response->data[0];
						if($res->pan_status == "VALID"){
							$kyc_detail['name'] = $res->first_name.' '.$res->last_name;
							$kyc_detail['status'] = 2;
							$kyc_detail['verification_type'] = 2; // 1-Manual,2-Auto
							$data = $this->scheme_modal->kyc_insert($kyc_detail); 
							if($data['status'] == TRUE){
								$this->load->model("mobileapi_model");
								$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
								$data['msg'] = "PAN Card verified successfully";
							} 
						}else{
							$data['kyc_status'] = 0 ;
							$data['status'] = FALSE; 
							$data['msg'] = "Invalid PAN Details "; 
						}
					} 
				}else{
				    $kyc_detail['verification_type'] = 1; // 1-Manual,2-Auto
					$data = $this->scheme_modal->kyc_insert($kyc_detail);
					if($data['status'] == TRUE){
						$this->load->model("mobileapi_model");
						$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
						$data['msg'] = "PAN Card details submitted successfully. Verification in progress. Keep in touch with us.";
					}else{
						$data['kyc_status'] = 0 ;
						$data['status'] = FALSE; 
						$data['msg'] = "Invalid PAN Card Details "; 
					} 
				}
			}else if($_POST['form_type']==3) // Aadhaar
			{
				$aadhar = $_POST; 
				$kyc_detail = array(
					'id_customer'    	 => $cus_id,
					'kyc_type'    	 	 => $aadhar['form_type'],	
					'number'    	 	 => (isset($aadhar['aadhar_number'])?$aadhar['aadhar_number']:NULL),
					'name'    	 		 => (isset($aadhar['aadhar_cardname'])?$aadhar['aadhar_cardname']:NULL),
					'dob'    	 		 => (isset($aadhar['dob'])?date("Y-m-d",strtotime($aadhar['dob'])):NULL),
					'date_add'			 =>  date('Y-m-d H:i:s')
					);  
				if($approval_type == "Auto"){
					$response = $this->zoopapiCurl("extract-aadhaar-data",array( "mode" => "pdf","file" => $aadhar['file'],"password" => $aadhar['password'],"purpose" => 'For Purchase Plan KYC verification',"request_consent" => 'Y'));
					
					if(isset($response->statusCode)){
						$data['status'] = FALSE; 
						$data['msg'] = $response->message;
						$data['kyc_status'] = 0 ;
					}
					elseif($response->transaction_status == 5){
						$res = $response->result->BasicInfo; 
						$date = str_replace('/', '-', $res->DOB);
                        $dob = date('Y-m-d', strtotime($date));
						$kyc_detail['name'] = $res->Name;
						$kyc_detail['number'] = $response->result->AadhaarInfo;
						$kyc_detail['dob'] = $dob;
						$kyc_detail['status'] = 2;
						$kyc_detail['verification_type'] = 2; // 1-Manual,2-Auto
						$data = $this->scheme_modal->kyc_insert($kyc_detail); 
						if($data['status'] == TRUE){
							$this->load->model("mobileapi_model");
							$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
							$data['msg'] = "Aadhaar Details verified successfully";
						} 
					}else{
						$data['kyc_status'] = 0 ;
						$data['status'] = FALSE; 
						$data['msg'] = "There was some issue, please try after sometime .."; 
					}
				}else{
				    $kyc_detail['verification_type'] = 1; // 1-Manual,2-Auto
					$data = $this->scheme_modal->kyc_insert($kyc_detail);
					if($data['status'] == TRUE){
						$this->load->model("mobileapi_model");
						$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
						$data['msg'] = "Aadhaar details submitted successfully. Verification in progress. Keep in touch with us. ";
					}else{
						$data['kyc_status'] = 0 ;
						$data['status'] = FALSE; 
						$data['msg'] = "Invalid Aadhaar Details "; 
					} 
				}
			}
			else if($_POST['form_type'] == 4){ // Driving Licence
			    $dl = $_POST;
				$kyc_detail = array(
					'id_customer'    	 => $cus_id,
					'kyc_type'    	 	 => $dl['form_type'],	
					'number'    	 	 => (isset($dl['dl_number'])?$dl['dl_number']:NULL),
					'dob'    	 		 => (isset($dl['dob'])?date("Y-m-d",strtotime($dl['dob'])):NULL),
					'date_add'			 =>  date('Y-m-d H:i:s')
					);
				if($approval_type == "Auto"){
				    /*$date = date_create($kyc_detail['dob']);
                    $dob = date_format($date,"d-m-Y");*/
                    $postData = array("dl_no" => $kyc_detail['number'],"consent" => "Y" , "consent_text" => "For Purchase Plan KYC verification");
					$response = $this->zoopapiCurl("verify-dl-advance/v2",$postData);
					if(isset($response->statusCode)){
					    $data['kyc_status'] = 0;
						$data['status'] = FALSE; 
						$data['msg'] = $response->message;
					}
					else{
						if($response->transaction_status == 1){
							if($response->response_msg == "Success"){
								$kyc_detail['status'] = 2;
								$kyc_detail['number'] = $response->result->dlNumber;
								$kyc_detail['dob'] = date("Y-m-d",strtotime($response->result->dob));
								$kyc_detail['address'] = $response->result->address[0]->completeAddress;
								$kyc_detail['verification_type'] = 2; // 1-Manual,2-Auto
								$data = $this->scheme_modal->kyc_insert($kyc_detail); 
								if($data['status'] == TRUE){
									$this->load->model("mobileapi_model");
									$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
									$data['msg'] = "Driving Licence verified successfully";
								} 
							}else{
								$data['kyc_status'] = 0;
								$data['status'] = FALSE; 
								$data['msg'] = "Invalid Driving Licence "; 
							}
						}elseif($response->transaction_status == 0){
							$data['kyc_status'] = 0;
							$data['status'] = FALSE; 
							if(isset($response->error_message)){
								$data['msg'] = $response->data->error_message; 
							}else{
								$data['msg'] = $response->response_msg;
							}
						}
						
					}
				}else{
				    $kyc_detail['verification_type'] = 1; // 1-Manual,2-Auto
					$data = $this->scheme_modal->kyc_insert($kyc_detail); 
				//	echo $this->db->_error_message();
					if($data['status'] == TRUE){
						$this->load->model("mobileapi_model");
						$data['kyc_status'] = $this->mobileapi_model->checkCusKYC($cus_id); 
						$data['msg'] = "Driving Licence details submitted successfully. Verification in progress. Keep in touch with us. ";
					}else{
						$data['kyc_status'] = 0 ;
						$data['status'] = FALSE; 
						$data['msg'] = "Invalid Driving Licence Details .."; 
					} 
				} 
			}
			if($data['kyc_status'] == 1 && $approval_type == "Auto"){
			  $this->session->set_flashdata('successMsg',$data['msg']);
			}
			$data['approval_type'] = $approval_type;
			echo json_encode($data);
		}
		else{	// EDIT													  
			if($_POST['form_type']==1){
				$bank = $_POST;
				$kyc_detail = array(
				'id_customer'    	 => $cus_id,
				'kyc_type'    	 	 => $bank['form_type'],	
				'number'    	 	 => (isset($bank['bank_acc_no'])?$bank['bank_acc_no']:NULL),
				'name'    	 		 => (isset($bank['name'])?$bank['name']:NULL),
				'bank_branch'    	 => (isset($bank['bank_branch'])?$bank['bank_branch']:NULL),
				'bank_ifsc'    		 => (isset($bank['ifsc'])?$bank['ifsc']:NULL),
				'last_update'		 =>  date('Y-m-d H:i:s'),
				'status'			=> 0
				);
			}else if($_POST['form_type']==2){
			$pan = $_POST;
				$kyc_detail = array(
				'number'    	 	 => (isset($pan['pan_no'])?$pan['pan_no']:NULL),
				'name'    	 		 => (isset($pan['pan_card_name'])?$pan['pan_card_name']:NULL),
				'last_update'		 =>  date('Y-m-d H:i:s'),
				'status'			=> 0
				);
			}else if($_POST['form_type']==3)
			{
				$aadhar = $_POST;
				$kyc_detail = array(	
				'number'    	 	 => (isset($aadhar['aadhar_number'])?$aadhar['aadhar_number']:NULL),
				'name'    	 		 => (isset($aadhar['aadhar_cardname'])?$aadhar['aadhar_cardname']:NULL),
				'dob'    	 		 => (isset($aadhar['dob'])?date("Y-m-d",strtotime($aadhar['dob'])):NULL),
				'last_update'		 =>  date('Y-m-d H:i:s'),
				'status'			=> 0
				);
			}
			else if($_POST['form_type']==4)
			{
				$dl = $_POST;
				$kyc_detail = array(	
				'number'    	 	 => (isset($dl['dl_number'])?$dl['dl_number']:NULL),
				'dob'    	 		 => (isset($dl['dob'])?date("Y-m-d",strtotime($dl['dob'])):NULL),
				'last_update'		 =>  date('Y-m-d H:i:s'),
				'status'			=> 0
				);
			}
			$data=$this->scheme_modal->kyc_update($kyc_detail,$cus_id,$_POST['form_type']);
			echo json_encode($data);
		}
	}
	
	function verify_pan(){
		if($this->config->item('zoop_enabled') == 0){
			$data['status'] = TRUE; 
			$data['msg'] = "PAN verified";
		}else{ 
			$pan = $_POST;
			$response = $this->zoopapiCurl("pan",array("pan" => $pan['pan_no'],"consent" => "Y","consent_text" => "I agree to validate my PAN Number"));
			//$response = $this->zoopapiCurl("pan-lite",array("pan" => $pan['pan_no'],"consent" => "Y","consent_text" => "I agree to validate my PAN Number"));
			if(isset($response->statusCode)){
				$data['status'] = FALSE; 
				$data['msg'] = $response->message;
			}
			elseif($response->response_code == 101){
				$res = $response->data;
				if($res->pan_status == "VALID"){
					$data['status'] = TRUE;
					$data['msg'] = $res->pan_status; 
				}else{
					$data['status'] = FALSE; 
					$data['msg'] = "Invalid PAN Details "; 
				}
			}else{
			   $data['status'] = FALSE; 
					$data['msg'] = "Invalid PAN Number "; 
			} 
		}
		echo json_encode($data);
	}
	
	/**
	* 	ZOOP API CURL Call
	* 	Documentation : https://docs.aadhaarapi.com/?version=latest
	* 
	* @return
	*/
		
	
	function zoopapiCurl($api,$postData){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $this->config->item('zoop_url')."".$api,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 60,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => json_encode($postData),
		  CURLOPT_HTTPHEADER => array(
		    "Content-Type: application/json",
		    "cache-control: no-cache",
		    "qt_agency_id: ".$this->config->item('agency_id'),
		    "qt_api_key: ".$this->config->item('api_key')
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;exit;
		} else {
		  return json_decode($response);
		}
	}
	
	function get_kyc_details(){
		$kyc = $this->scheme_modal->get_kyc_details();
		echo $kyc;
	}
	//GG bank pan and aadhar details view
	
	function rates_history()
	{
	    $LMX_ratehistory = array();
    	$EJ_ratehistory  = array();
    	
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('To_date');
	    if(strtotime($from_date) < strtotime('01/14/2020') && strtotime($to_date) < strtotime('01/14/2020') ){
    		$EJ_ratehistory =$this->login_model->rate_history('ej');
	    }
	    elseif(strtotime($from_date) < strtotime('01/14/2020') && strtotime($to_date) >= strtotime('01/14/2020') ){
    		$LMX_ratehistory = $this->login_model->rate_history('lmx');
    		$EJ_ratehistory = $this->login_model->rate_history('ej');
    		    	//	echo $this->db->last_query();exit;

	    }
	    elseif(strtotime($from_date) >= strtotime('01/14/2020')){
    		$LMX_ratehistory = $this->login_model->rate_history('lmx');
	    }
	     
		$goldrate_22ct  = array();
		$silverrate_1gm = array();
		$platinum_1g    = array();
        $result         = array(); 
        
        if(sizeof($EJ_ratehistory)>0)
        {
            foreach($EJ_ratehistory as $row)
            {
                if($row['goldrate_22ct']>0)
                {
                    $goldrate_22ct[]=array(
                    'updatetime'=>$row['updatetime'],
                    'goldrate_22ct'=>$row['goldrate_22ct']
                    );
                }
            }
            foreach($EJ_ratehistory as $row)
            {
                if($row['silverrate_1gm']>0)
                {
                    $silverrate_1gm[]=array(
                    'silverrate_1gm'=>$row['silverrate_1gm']
                    );
                }  
            }
            foreach($EJ_ratehistory as $row)
            {
                         if($row['platinum_1g']>0)
                         {
                               $platinum_1g[]=array(
                                'platinum_1g'=>$row['platinum_1g']
                                );
                         }
            }
              $gold_rate_length=count($goldrate_22ct);
              for($i=0;$i<$gold_rate_length;$i++)
              {
                 if(isset($platinum_1g[$i]['platinum_1g']))
                 {
                     $previous_platinum=$platinum_1g[$i]['platinum_1g'];
                 }
                  $result[$i]['updatetime'] = $goldrate_22ct[$i]['updatetime'] ;
                  $result[$i]['goldrate_22ct'] = $goldrate_22ct[$i]['goldrate_22ct'] ;
                  $result[$i]['silverrate_1gm'] = $silverrate_1gm[$i]['silverrate_1gm'];
                  $result[$i]['platinum_1g'] = (isset($platinum_1g[$i]['platinum_1g']) ? $platinum_1g[$i]['platinum_1g']:$previous_platinum); 
              } 
        } 
        
        $response = array_merge($LMX_ratehistory,$result);
        echo json_encode($response);
        //echo json_encode($LMX_ratehistory);
	}
	
	function ratehistory()
	{
		//$data['content'] = $data;
	//echo ('data');exit;
		$data['ratehistory'] = $this->login_model->rate_history();
	  	$pageType = array('page' => 'rate_history','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER.'rate_history';
		$data['content'] = $data;
		$this->load->view('layout/template', $data);
	} 
	 
	function rateFixing_otp()
	{
	    $id_customer = $this->session->userdata('cus_id');
	    $cus_data=$this->registration_model->get_cusData_by_ID($id_customer);
	    if($cus_data)
	    {
	       if(strlen(trim($cus_data[0]['mobile'])) == 10)
		   {
                $mobile=$cus_data[0]['mobile'];
                $this->session->unset_userdata("OTP");
                $OTP = mt_rand(100000, 999999);
                $this->session->set_userdata('OTP',$OTP);
                $this->session->set_userdata('rate_fixing_otp_exp',time()+100);
                $serviceID = 23;
			    $service = $this->services_modal->checkService($serviceID);
				$sms_data = array(
                				"fname" => "",
                				"otp"      => $OTP
                				);  
                $message = $this->services_modal->get_SMS_OTPdata($serviceID,$sms_data);
				//$message = $OTP." is the verification code from ".$this->comp['company_name'].".Please use this code to fix rate.";
				if($this->config->item('sms_gateway') == '1'){
		    		$this->sms_model->sendSMS_MSG91($mobile,$message,'',$service['dlt_te_id']);			
                }
                elseif($this->config->item('sms_gateway') == '2'){
                $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
                }
                elseif($this->config->item('sms_gateway') == '3')
            	{
                    $this->sms_model->sendSMS_SpearUC($mobile,$message,'',$service['dlt_te_id']);	
                }
                elseif($this->config->item('sms_gateway') == '4')
                {
                	$this->sms_model->sendSMS_Asterixt($mobile,$message,'',$service['dlt_te_id']);	
                }
                elseif($this->config->item('sms_gateway') == '5')
                {
                	$this->sms_model->sendSMS_Qikberry($mobile,$message,'',$service['dlt_te_id']);	
                }
                $data=array('success'=>true,'msg'=>'Your OTP sent successfully');	
			}
	        else
	        {
	             $data=array('success'=>false,'msg'=>'Unable To Send OTP');
	        }
	        echo json_encode($data);
	    }
	}
	
	// To Get Bearer Token for ERP api call
	
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
          return false;
        } else {
            $result =  json_decode($response);
            return $result->access_token; 
        }
	}
	
	function submit_ratefix()
	{
	    if($this->session->userdata('OTP') == $_POST['otp'])
			{
			    if(time() >= $this->session->userdata('rate_fixing_otp_exp'))
					{
						$this->session->unset_userdata('rate_fixing_otp_exp');
						$data=array('success'=>false ,'msg'=>'OTP has been expired');
						$this->session->unset_userdata('OTP');
					}else
					{
					    $this->load->model("mobileapi_model");
					    $settings = $this->scheme_modal->get_settings();
					    //$rate = $this->mobileapi_model->getGold22ct($settings['is_branchwise_rate'],$_POST['id_branch']);
					    $rate = $_POST["metal_rate"];
					    if($this->config->item("integrationType") == 3){
            				if($rate != 0 && !empty($_POST["sch_ac_no"])){
    	                        $bearer = $this->getBearerToken();
    	                        if($bearer){
    	                            $postData = array((array("SchemeAccountNo" => trim($_POST["sch_ac_no"]),"MetalRate" => $rate,"FixRequestFrom" => 1))); 
            					
                					$curl = curl_init();   
                				    curl_setopt_array($curl, array(
                				    CURLOPT_URL => $this->config->item('erp_baseURL')."RateFixing",
                				    CURLOPT_RETURNTRANSFER => true,
                				    CURLOPT_ENCODING => "",
                				    CURLOPT_MAXREDIRS => 10,
                				    CURLOPT_TIMEOUT => 300,
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
                				   
                				   if ($err) {
                				    	//echo "cURL Error #:" . $err;
                				    	$data =  array('is_valid' => TRUE, 'success' => FALSE, 'msg' => 'Sorry for the inconvenience, we are unable to fix rate at the moment. Try again later.'.$err);
                				   } else {
                				   		$res = json_decode($response);
                				   		if(gettype($res) == "array"){
                				   			if($res[0]->Flag == TRUE){
                								$updData = array(
                				   							"fixed_wgt"          => $res[0]->BookedWeight,
                				   							"fixed_metal_rate"   => $res[0]->MetalRate,
                				   						    "rate_fixed_in"      => $res[0]->FixRequestFrom,
                				   							"fixed_rate_on"      => date("Y-m-d H:i:s", strtotime($res[0]->BookedDate))
                				   						); 
                				   				$status = $this->mobileapi_model->updFixedRate($updData,$_POST['id_scheme_account']);
                				   				if($status){
                									$data = array('is_valid' => TRUE,'success' => TRUE, 'msg' => $res[0]->Status);
                								}else{
                									$data = array('is_valid' => TRUE,'success' => FALSE, 'msg' => 'Sorry for the inconvenience, we are unable to fix rate at the moment. Try again later..');
                								}				   				
                							}else{
                								$data = array('is_valid' => TRUE,'success' => FALSE,'msg' => $res[0]->Status);
                							}							
                						}else{ 
                							$data = array('is_valid' => TRUE,'success' => FALSE, 'msg' => 'Sorry for the inconvenience, we are unable to fix rate at the moment. Try again later...'.$response);
                						}
                				   }
    	                        }else{
    	                            $data =  array('is_valid' => FALSE,'success' => FALSE, 'msg' => 'Sorry for the inconvenience, we are unable to fix rate at the moment. Try again later....');
    	                        }
            					
            				}
            				else{ 
            					$data =  array('is_valid' => FALSE,'success' => FALSE, 'msg' => 'Sorry for the inconvenience, we are unable to fix rate at the moment. Try again later....');
            				}
    					}
            		    else{ // Rate Fixed by LMX
            		    //print_r($rate);exit;
                			if($rate != 0){
                			    $isRateFixed = $this->mobileapi_model->isRateFixed($_POST['id_scheme_account']);
                			    if($isRateFixed['status'] == 0){
                    		        $updData = array(
                	   							"fixed_wgt" => $isRateFixed['firstPayment_amt']/$rate,
                	   							"firstPayment_amt" =>$isRateFixed['firstPayment_amt'],
                	   							"fixed_metal_rate" => $rate,
                	   							"rate_fixed_in" => 1,
                	   							"fixed_rate_on" => date("Y-m-d H:i:s")
                	   						); 
                	   					//print_r($updData);exit; 
                	   				$status = $this->mobileapi_model->updFixedRate($updData,$_POST['id_scheme_account']);
                	   				if($status){
                						$data = array('is_valid' => TRUE,'success' => TRUE, 'msg' => "Rate fixed successfully");
                					}else{
                						$data = array('is_valid' => TRUE,'success' => FALSE, 'msg' => 'Sorry for the inconvenience, we are unable to fix rate at the moment. Try again later..');
                					}
                			    }else{
                			        $data = array('is_valid' => TRUE,'success' => TRUE, 'msg' => "Rate already fixed !!");
                			    }
                			}else{ 
            					$data =  array('is_valid' => FALSE,'success' => FALSE, 'msg' => 'Sorry for the inconvenience, we are unable to fix rate at the moment. Try again later....');
            				}
            		    }
        				
					    $this->session->unset_userdata('OTP');
					}
			}else{
			    $data=array('success'=>false,'msg'=>'Invalid OTP');
			}
			 echo json_encode($data);
	}
	
	//customer name by default in account name while joining scheme//HH
        /*public function get_cusname()
        {
        $data=$this->scheme_modal->get_cusname();
        	echo json_encode($data);
        }	*/
   //customer name by default in account name while joining scheme//
   
   // compare plans img get from asserts/img //HH
   	public function compare_plan()
	{
		$records = $this->services_modal->get_compare_plans();	
		$pageType = array('page' => 'dashboard','currency_symbol'=>$this->comp['currency_symbol'],'scheme_status' =>  $this->scheme_status,'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);		
		$data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['content']	 = $records;
		$data['fileName'] = self::VIEW_FOLDER.'compare_plans';
		$this->load->view('layout/template', $data);
	}
	
	
	/* Cashfree Auto-Debit Functions */
	public function cf_subscription($type,$id_sch_ac)
	{
		// $type => 1 - Subscribe, 2 - UnSubscribe
		$auth_status = array(
							'INITIALIZED'	=> 1,
							'BANK_APPROVAL_PENDING' => 2,
							'ACTIVE'		=> 3,
							'ON_HOLD'		=> 4,
							'CANCELLED'    	=> 5,
							'COMPLETED'    	=> 6
						  );
		switch($type)
		{
			case '1': // Subscribe
				$planDetail		=	$this->scheme_modal->get_plan_detail($id_sch_ac);
				$subscriptionId	=	uniqid(time());
				$this->session->set_userdata('CF_subscriptionId',$subscriptionId);
				if(sizeof($planDetail) > 0){
					if($planDetail['id_auto_debit_subscription'] > 0 ){ 
						$this->session->set_flashdata('errMsg','Already subscription created, Kindly check the authorization status...');
						redirect("/chitscheme/scheme_account_report/".$id_sch_ac);
					}else{
						$pendingIns	= $planDetail['total_installments']-$planDetail['paid_installments'];
						if($pendingIns > 0){
							$expires_on = date('Y-m-d H:i:s', strtotime("+".($pendingIns)." months", strtotime(date('Y-m-d H:i:s'))));
							$gen_email 	=  $this->random_strings(8).'@gmail.com';
							$insSubscription = array(
													"id_scheme_account"		=>	$id_sch_ac,
													"subscription_id"		=>	$subscriptionId,
													"plan_id"				=>	$planDetail['sync_scheme_code'],
													"first_charge_delay"	=>	1,
													"expires_on"			=>	$expires_on,
													"status"				=>	0,
													"created_on"			=>	date('Y-m-d H:i:s'),
													"added_by"				=>	0,  // Web App
												);
							$ins = $this->scheme_modal->insertData($insSubscription,'auto_debit_subscription');					
							if($ins['status']){
								$subscriptionData = array(
													"subscriptionId"	=>	$subscriptionId,
													"planId"			=>	$planDetail['sync_scheme_code'],
													"customerName"		=>	$planDetail['cus_name'],
													"customerEmail"		=>	!empty($planDetail['email']) ? $planDetail['email'] : $gen_email,
													"customerPhone"		=>	$planDetail['mobile'],
													"firstChargeDelay"	=>	1,
													"authAmount"		=>	1,
													"expiresOn"			=>	$expires_on,
													"returnUrl"			=>	$this->config->item('base_url').'index.php/cf_autodebit/autoDebitRURL/W/'.$id_sch_ac
												);
				                $res = $this->cf_curl('',$subscriptionData,$planDetail);
				                if ($res['status'] == FALSE) {
				                   $this->session->set_flashdata('errMsg','Error in processing request...');
								   redirect("/chitscheme/scheme_report");
				                } 
				                else { 
				                	$response = $res['result']; 
				                    //echo "<pre>";print_r($response);exit;
				                    if($response->status == "OK"){ // OK - Api call success  - ERROR Failed
				                    	$updSubscription = array(
																"message"			=>	$response->message,
																"sub_reference_id"	=>	$response->subReferenceId,
																"auth_status"		=>	$auth_status[$response->subStatus],
																"auth_link"			=>	$response->authLink,
																"status"			=>	1,
																"last_update"		=>	date('Y-m-d H:i:s')
																);
										$upd = $this->scheme_modal->updateData($updSubscription,'id_auto_debit_subscription',$ins['insertID'],'auto_debit_subscription');
										$updSchAc = array(
														"auto_debit_status"	=>	$auth_status[$response->subStatus],
														"date_upd"			=>	date('Y-m-d H:i:s')
														);
										$this->scheme_modal->updateData($updSchAc,'id_scheme_account',$id_sch_ac,'scheme_account');											
										if($upd > 0){
											$this->session->set_flashdata('successMsg','Subscription created successfully.. Kindly do the authorization process by clicking the Authorize button below to complete the Subscription..');
											redirect("/chitscheme/scheme_account_report/".$id_sch_ac);
										}else{
											$this->session->set_flashdata('errMsg','Error in subscriptiion process, kindly contact admin...');
											redirect("/chitscheme/scheme_report");
										}
										
				                    }else{
										$this->session->set_flashdata('errMsg','Error in subscriptiion process, try again later..');
										redirect("/chitscheme/scheme_report");
									}
				                }
							}
							else
							{
								$this->session->set_flashdata('errMsg','Error in subscriptiion process, try again later...');
								redirect("/chitscheme/scheme_report");
							}						
						} else{
							$this->session->set_flashdata('errMsg','You have already paid your installments...');
							redirect("/chitscheme/scheme_report");
						}
					}
				}else{
					$this->session->set_flashdata('errMsg','No record found...');
					redirect("/chitscheme/scheme_report");
				}
			break;
			case '2': // UnSubscribe
				$planDetail	= $this->scheme_modal->get_subscriptionData($id_sch_ac);
				$post_data	= array("subReferenceId" => $planDetail['sub_reference_id']);
				$res 		= $this->cf_curl($planDetail['sub_reference_id'].'/cancel',$post_data,$planDetail);
                if ($res['status'] == FALSE) {
                   $this->session->set_flashdata('errMsg','Error in processing request...');
				   redirect("/chitscheme/scheme_report");
                } 
                else { 
                	$response = $res['result'];
                    //echo "<pre>";print_r($response);exit;
                    if($response->status == "OK"){ // OK - Api call success  - ERROR Failed
                    	$updSubscription = array(
												"message"			=>	$response->message,
												"auth_status"		=>	5,
												"status"			=>	0,
												"last_update"		=>	date('Y-m-d H:i:s')
												);
						$upd = $this->scheme_modal->updateData($updSubscription,'id_auto_debit_subscription',$planDetail['id_auto_debit_subscription'],'auto_debit_subscription');
						$updSchAc = array(
										"auto_debit_status"	=>	5,
										"date_upd"			=>	date('Y-m-d H:i:s')
										);
						$this->scheme_modal->updateData($updSchAc,'id_scheme_account',$id_sch_ac,'scheme_account');											
						if($upd > 0){
							$this->session->set_flashdata('successMsg','You are successfully unsubscribed from cashfree auto-debit process..');
							redirect("/chitscheme/scheme_account_report/".$id_sch_ac);
						}else{
							$this->session->set_flashdata('errMsg','Error in unsubscriptiion process, kindly contact admin...');
							redirect("/chitscheme/scheme_report");
						}
						
                    }else{
						$this->session->set_flashdata('errMsg','Error in unsubscriptiion process, try again later...');
						redirect("/chitscheme/scheme_report");
					}
                }
			break;
		}
	}
    
    function cf_curl($subsc_api,$postData,$gateway)
    {
    	$curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $gateway['api_url'].'api/v2/subscriptions/'.$subsc_api,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($postData),
            // Getting  server response parameters //
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
                "X-Client-Id: ".$gateway['param_3'],
                "X-Client-Secret:".$gateway['param_1']
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return array('status' => FALSE, 'result' => $err);
        } 
        else {
        	return array('status' => TRUE, 'result' => json_decode($response));
        }	
    }	
    	
    function random_strings($length)
    {
         $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
         return substr(str_shuffle($str_result), 0, $length);
    }
    
	
	/* .Cashfree Auto-Debit Functions */
	
	public function getRatesByJoin()
	{
		$start_date = $this->input->post('start_date');	
		$data = $this->scheme_modal->get_ratesByJoin($start_date);  
		echo json_encode($data);
	}
	
    public function check_agent_refcode(){
        //print_r($_POST);exit;
		 $code=$this->input->post('agent_code');		 
	     if($code!='')
	     {
			$data = $this->scheme_modal->check_agent_refcode($code);
			echo json_encode($data);
		 }else{
		   $data=array('status'=>false ,'msg'=>'Enter Agent referral code.');
		  	 echo json_encode($data);
		 } 
	   } 

    
    
}