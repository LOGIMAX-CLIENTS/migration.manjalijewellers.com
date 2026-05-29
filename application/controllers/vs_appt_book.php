<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Vs_appt_book extends CI_Controller {
	const VIEW_FOLDER = 'video_shopping/';
	public function __construct()
    {
        parent::__construct();
		ini_set('date.timezone', 'Asia/Kolkata');
	    $this->load->model('login_model');
		$this->load->model('vs_model'); 
		$this->load->model('sms_model');
		$this->load->model('email_model');
        $this->comp 	= $this->login_model->company_details();
        $this->m_mode 	= $this->login_model->site_mode();
        if( $this->m_mode['maintenance_mode'] == 1) {
        	$this->maintenance();
	    } 
    }    
	
	function vs_appt_form()
	{	     
		$cusData = array('name' => '', 'mobile' => '' ,'email' => '');
		if($this->session->userdata('mobile') != '' || $this->session->userdata('vs_mob_verified') == 1){ 
			$settings = $this->vs_model->getSettings(); 
			if($settings['m_active'] == 1){ // 1 - Enable
				$available_slots = $this->vs_model->getAvailableSlots();
				if($this->session->userdata('is_logged_in') == TRUE){
					$cusData = $this->vs_model->getCusByMob($this->session->userdata('mobile'));
				}
				$data['content']['slots'] = $available_slots; 
				$data['content']['cusData'] = $cusData; 
				$pageType = array('page' => 'vs_appt','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
	            $data['header_data'] = $pageType;
				$data['footer_data'] = $pageType;
				$data['fileName'] = self::VIEW_FOLDER.'vs_appt'; 
				$this->load->view('layout/template', $data);
			}else{ 
				$pageType = array('page' => 'vs_appt','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
	            $data['header_data'] = $pageType;
				$data['footer_data'] = $pageType;
				$data['fileName'] = "<div>Video Shopping Appointment not available.</div>";
				$this->load->view('layout/template', $data);
			}
		}else{
			$pageType = array('page' => 'vs_appt','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
			$data['content'] = array(); 
            $data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['fileName'] = self::VIEW_FOLDER.'vs_register'; 
			$this->load->view('layout/template', $data);
		} 
	}
	
	public function vs_appt()
	{ 
		$data = $_POST['vs_appt_form'];
		if(strlen($data['mobile']) == 10){
			$settings = $this->vs_model->getSettings(); 
			$insData = array('name'         => $data['name'],
							'email'       	=> (isset($data['email']) ?$data['email']:NULL),
							'mobile'        => (isset($data['mobile']) ?$data['mobile']:NULL),
							'location'      => (isset($data['location']) ?$data['location']:NULL),
							'whats_app_no'  => (isset($data['whats_app_no']) ?$data['whats_app_no']:NULL),
							'pref_category' => (isset($data['pref_category']) ?$data['pref_category']:NULL),
							'pref_item'  	=> (isset($data['pref_item']) ?$data['pref_item']:NULL),
							'preferred_slot'=> (isset($data['pref_slot']) ?$data['pref_slot']:NULL),
							'description'   => (isset($data['description']) ?$data['description']:NULL),
							'created_on'    => date('Y-m-d H:i:s')
							);
			$this->db->trans_begin(); 
			$alreadyRequested = $this->vs_model->isPrevRequested($data['pref_slot'],$data['mobile']);
			if($alreadyRequested){
				$this->session->set_flashdata('errMsg','You have already requested this slot..');	
				redirect("/vs_appt_book/vs_appt_form");
			}else{
				$isAvail = $this->vs_model->isSlotAvailable($data['pref_slot']);
				if($isAvail){ 
					if($settings['appt_auto_assign'] == 1){
						$insData['status'] = 1;
						$insData['alloted_slot'] = (isset($data['pref_slot']) ?$data['pref_slot']:NULL);
						$msg = 'Video Shopping Appointment has been placed successfully..';
						$remark = 'Appointment Created';
					}else{
						$insData['status'] = 0;
						$msg = 'Your request for Video Shopping Appointment has been placed successfully.. We will contact you shortly.';
						$remark = 'Appointment Requested';
					}
					$ins = $this->vs_model->insertData($insData,'appt_request');  
					$logdata = array(  
					    "id_appt_request"  => $ins['insertID'],
					    "status"	       => $insData['status'], // Closed
					    "event_date"       => date('Y-m-d H:i:s'),
					    "id_employee"      => NULL,
					    "remark"      	   => $remark
					   ); 
					$this->vs_model->insertData($logdata,'appt_request_log'); 
				}else{
					$this->session->set_flashdata('errMsg','Sorry!! Requested slot not available, please choose someother slot..');	
					redirect("/vs_appt_book/vs_appt_form");
				}
				if($this->db->trans_status() === TRUE){
					$this->db->trans_commit();  
					/* Send Alert to Customer SMS,Email */
					$cusServData = $this->sms_model->SMS_dataByServCode('VS_REQST',$ins['insertID']);  
					if($cusServData['serv_sms'] == 1 && isset($data['mobile']) && $data['mobile'] != ''){ 
						if($this->config->item('sms_gateway') == '1'){
			    		    $this->sms_model->sendSMS_MSG91($data['mobile'],$cusServData['message'],'',$cusServData['dlt_te_id']);
			    		}
			    		elseif($this->config->item('sms_gateway') == '2'){
			    	        $this->sms_model->sendSMS_Nettyfish($data['mobile'],$cusServData['message'],'trans');
			    		} 
			    		elseif($this->config->item('sms_gateway') == '3'){
        					 $this->sms_model->sendSMS_SpearUC($data['mobile'],$cusServData['message'],'',$cusServData['dlt_te_id']);
        				}
        				elseif($this->config->item('sms_gateway') == '4'){
        					$this->sms_model->sendSMS_Asterixt($data['mobile'],$cusServData['message'],'',$cusServData['dlt_te_id']);
        				}
        				elseif($this->config->item('sms_gateway') == '5'){
        					$this->sms_model->sendSMS_Qikberry($data['mobile'],$cusServData['message'],'',$cusServData['dlt_te_id']);
        				}
					}
					if($cusServData['serv_email'] == 1 && isset($data['email']) && $data['email'] != ''){
						$to = $data['email'];
						$subject = "Reg - ".$this->comp['company_name']." video shopping appointment";
						$data['mailData'] = $cusServData['cus_data'];
						$data['type'] = 1;
						$data['company_details'] = $this->comp;
						$message = $this->load->view('include/vs_appt',$data,true);
						$sendEmail = $this->email_model->send_email($to,$subject,$message,"",""); 
					}
					/* Send Alert to Admin SMS,Email */
					if($settings['vs_send_sms_to'] != '' && strlen($settings['vs_send_sms_to']) == 10){
						$adm_msg = "New VS appointment received.";
						if($this->config->item('sms_gateway') == '1'){
			    		    $this->sms_model->sendSMS_MSG91($settings['vs_send_sms_to'],$adm_msg,'','');		
			    		}
			    		elseif($this->config->item('sms_gateway') == '2'){
			    	        $this->sms_model->sendSMS_Nettyfish($settings['vs_send_sms_to'],$adm_msg,'trans');	
			    		}
			    		elseif($this->config->item('sms_gateway') == '3'){
        					 $this->sms_model->sendSMS_SpearUC($settings['vs_send_sms_to'],$adm_msg,'','');
        				}
        				elseif($this->config->item('sms_gateway') == '4'){
        					$this->sms_model->sendSMS_Asterixt($settings['vs_send_sms_to'],$adm_msg,'','');
        				}
        				elseif($this->config->item('sms_gateway') == '5'){
        					$this->sms_model->sendSMS_Qikberry($settings['vs_send_sms_to'],$adm_msg,'','');
        				}
					} 
					if($settings['vs_send_mail_to'] != ''){
						$to = $settings['vs_send_mail_to'];
						$subject = "Reg - ".$this->comp['company_name']." video shopping appointment";
						$data['mailData'] = $cusServData['cus_data'];	
						$data['type'] = 1;
						$data['company_details'] = $this->comp;
						$message = $this->load->view('include/vs_appt',$data,true); 
						$sendEmail = $this->email_model->send_email($to,$subject,$message,"",""); 
					}
					$this->session->set_flashdata('successMsg',$msg);	
				}else{
					$this->db->trans_rollback(); 
					$this->session->set_flashdata('errMsg','Unable to proceed your request. Please contact admin..');	
				}
			} 
		}else{
			$this->session->set_flashdata('errMsg','Enter 10 digit mobile number..');	
		}
		/*echo "<pre>"; print_r($insData);
		echo $this->db->last_query();exit;*/ 
		redirect("/vs_appt_book/vs_appt_form");
	}	
	
	function update_feedback()
	{	      
		
		$data = array(  'customer_feedback' => $_POST['customer_feedback'],
						'updated_on'    	=> date('Y-m-d H:i:s'),
						'updated_by'    	=> NULL,
						"status"	        => 4 // Closed
					  );
		$this->db->trans_begin();
		$this->vs_model->updateData($data,'id_appt_request',$_POST['id_appt_request'],'appt_request'); 
		$logdata = array(  
				    "id_appt_request"  => $_POST['id_appt_request'],
				    "status"	       => 4, // Closed
				    "event_date"       => date('Y-m-d H:i:s'),
				    "id_employee"      => NULL,
				    "remark"      	   => "Feedback Given."
				   ); 
		$this->vs_model->insertData($logdata,'appt_request_log'); 
		if($this->db->trans_status() === TRUE){
			$this->db->trans_commit();
			$this->session->set_flashdata('successMsg',"Feedback updated successfully..");	
		}else{
			$this->db->trans_rollback();
			$this->session->set_flashdata('errMsg','Unable to update feedback..');	
		}
		echo 1;
	}
	
	function vs_appt_list()
	{	      
		$data['content'] = array();
		$mobile = ($this->session->userdata('mobile') != '' ? $this->session->userdata('mobile'):($this->session->userdata('vs_mobile') != '' ? $this->session->userdata('vs_mobile') :''));
		if($mobile != ''){ 
			$data['content']['appts'] = $this->vs_model->getUserVSAppts($mobile);
		} 
		$pageType = array('page' => 'vs_appt','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
        $data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER.'vs_appt_list'; 
		$this->load->view('layout/template', $data); 
	}
	
	function vs_appt_detail($id_appt_req)
	{	      
		$data['content'] = array();
		$mobile = ($this->session->userdata('mobile') != '' ? $this->session->userdata('mobile'):($this->session->userdata('vs_mobile') != '' ? $this->session->userdata('vs_mobile') :''));
		if($mobile != ''){ 
			$data['content']['appts'] = $this->vs_model->getApptDetail($id_appt_req); 
		}  
		$pageType = array('page' => 'vs_appt','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
        $data['header_data'] = $pageType;
		$data['footer_data'] = $pageType;
		$data['fileName'] = self::VIEW_FOLDER.'vs_appt_detail'; 
		$this->load->view('layout/template', $data); 
	}
	
	public function generateOTP($mobile)
	{
		   
	   if(strlen(trim($mobile)) == 10)
	   { 
			$this->session->unset_userdata("VS_OTP");
			$OTP = mt_rand(100000, 999999);
			$this->session->set_userdata('VS_OTP',$OTP);  
			$this->session->set_userdata('vs_mobile',$mobile); 
			$this->session->set_userdata('vs_mob_verified',0); 
			$message = $OTP." is the verification code from ".$this->comp['company_name']." .Please use this code to verify your mobile number";
			if($this->config->item('sms_gateway') == '1'){
			    $this->sms_model->sendSMS_MSG91($mobile,$message,'','');		
			}
			elseif($this->config->item('sms_gateway') == '2'){
		        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');	
			} 
			elseif($this->config->item('sms_gateway') == '3'){
				 $this->sms_model->sendSMS_SpearUC($mobile,$message,'','');
			}
			elseif($this->config->item('sms_gateway') == '4'){
				$this->sms_model->sendSMS_Asterixt($mobile,$message,'','');
			}
			elseif($this->config->item('sms_gateway') == '5'){
				$this->sms_model->sendSMS_Qikberry($mobile,$message,'','');
			}
			echo 1; 
		}
		else
			echo 2;
	}
	
	public function verifyOTP()
	{ 
		if(strlen(trim($this->input->post('otp'))) == 6)
		{			 		
			if($this->session->userdata('VS_OTP') == $this->input->post('otp'))
			{			    
				$this->session->unset_userdata('VS_OTP');  
				$this->session->set_userdata('vs_mob_verified',1);
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 0;
		}
	}

	function createLog(){
		$dir = 'log/'.date('Y-m-d');
		if (!is_dir($dir)) {
		    mkdir($dir, 0777, TRUE);
		}
		$log_path = $dir.'/erp_service.txt';
		echo $log_path;
		$data = "\n --".date('Y-m-d H:i:s')."-- \n : Rolled Back ..";
        file_put_contents($log_path,$data,FILE_APPEND | LOCK_EX);
		
	}

}