<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Videoshopping_appt extends CI_Controller
{
	const SLOT_MODEL	=  "vs_appt_model";
	const SLOT_VIEW	    =  'vs_appointment/';
	const SETT_MOD	= "admin_settings_model";
	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		
		if(!$this->session->userdata('is_logged'))
		{
			redirect('admin/login');
		}
		$this->load->model(self::SLOT_MODEL);
		$this->load->model("sms_model");
		$this->load->model("email_model");
		$this->load->model(self::SETT_MOD);
	}
	 
    function appt_slots()
	{
		$model = self::SLOT_MODEL;
		$SETT_MOD = self::SETT_MOD; 
		$data['main_content'] = self::SLOT_VIEW.'slot_creation';
		$data['access'] = $this->$SETT_MOD ->get_access('videoshopping_appt/appt_slots');
		$this->load->view('layout/template', $data); 
	}
	
	function ajax_available_slots(){
		$model = self::SLOT_MODEL;
		$this->load->model('admin_settings_model');
		$data['slots'] = $this->$model->getActiveSlots();
		$data['access']= $this->admin_settings_model->get_access('videoshopping_appt/appt_slots');
		echo json_encode($data);
	}
	
	public function delete_slot($id_appt_slot)
    {
        $model = self::SLOT_MODEL; 
        $status = $this->$model->deleteData('id_appointment_slot',$id_appt_slot,'appt_slots'); 
        if($status)
		{
			 $this->session->set_flashdata('chit_alert_slots', array('message' => 'Slot deleted sucessfully','class' => 'success','title'=>'Delete Slot'));
		}else{
				  $this->session->set_flashdata('chit_alert_slots', array('message' => 'Unable to delete','class' => 'danger','title'=>'Delete Slot'));
		}
		redirect('videoshopping_appt/appt_slots');
    }
    
    public function create_slot()
    {
        $model = self::SLOT_MODEL;
        $slotsdata = $this->input->post('slot_data') ;
        $insRecord = 0;	
        $rejected = 0;	
        foreach($slotsdata as $data){
            $insArrdata = array(
                "slot_no"			=> $data['slot_no'],
                "slot_date"		    => $data['slot_date'],
                "slot_time_from"	=> $data['slot_time_from'],
                "slot_time_to"		=> $data['slot_time_to'],
                "allowed_booking" 	=> $data['allowed_booking'],
                "created_by"        => $this->session->userdata('uid'),
                "date_add"          => date('Y-m-d H:i:s')
            ); 
			$isExist = $this->$model->isSlotExist($insArrdata); 
			if(!$isExist){
				$ins_id = $this->$model->insertData($insArrdata,'appt_slots');
				if($ins_id > 0){
					$insRecord = $insRecord+1;
				}else{
					$rejected = $rejected+1;
				}
			}else{
				$rejected = $rejected+1;
			}
      	}
	    if($insRecord >0){
            $title = "Slot Creation";
			$class = "success";
			$msg = $insRecord.' Slot(s) created';
			if($rejected >0){
				$msg = $msg.' And '.$rejected.' slot(s) already existing..' ;
			}
		}
		elseif($rejected >0){
			$title = "Slot Creation";
			$class = "error";
			$msg = $rejected.' slots already existing..' ;
		} 
		/*echo $this->db->last_query();exit;*/
		$this->session->set_flashdata('chit_alert',array('message'=> $msg,'class'=>$class,'title'=>$title));
		echo json_encode($insRecord);	
    }
     
	public function check_alloted_slot()
	{
		$model = self::SLOT_MODEL;
		$data= $this->$model->check_alloted_slot();
		echo json_encode($data);
	} 
	
	// All Appts Requests
	function get_appt_request()
	{
		$SETT_MOD = self::SETT_MOD; 
		$data['main_content'] = self::SLOT_VIEW.'appt_requests';
		$data['access'] = $this->$SETT_MOD ->get_access('videoshopping_appt/get_appt_request');		
		$this->load->view('layout/template', $data); 
	}
	
	function ajax_appt_requests()
	{
		$model = self::SLOT_MODEL; 
		$data = $this->$model->apptRequestsByFilter($_POST);  
		echo json_encode($data);
	}
	
	// New Appts Requests
	function appt_approval_list()
	{
		$data['main_content'] = self::SLOT_VIEW.'new_appt_req';
		$this->load->view('layout/template', $data); 
	}
	
	function approvalApptRequests()
	{
		$model = self::SLOT_MODEL; 
		$data = $this->$model->approvalApptRequests($_POST);  
		echo json_encode($data);
	}
	
	public function emp_available()
	{
		$model = self::SLOT_MODEL;
		$preferred_slot  = $this->input->post('preferred_slot');
		$id_appt_request = $this->input->post('id_appt_request');
		$data = $this->$model->emp_available($id_appt_request,$preferred_slot);
		echo json_encode($data);
	}
	
	public function appt_feedback_added()
	{
		$model = self::SLOT_MODEL; 
		$id_appt_request = $this->input->post('id_appt_request'); 		
		$data = array(  
				    "customer_feedback" => $this->input->post('customer_feedback'),
				    "status"	        => 4, // Closed
				    "updated_on"        => date('Y-m-d H:i:s'),
				    "updated_by"       	=> $this->session->userdata('uid')
				   ); 
		$apptfeed = $this->$model->updateData($data,'id_appt_request',$id_appt_request,'appt_request');  
		$logdata = array(  
				    "id_appt_request"  => $id_appt_request,
				    "status"	       => 4, // Closed
				    "event_date"       => date('Y-m-d H:i:s'),
				    "id_employee"      => $this->session->userdata('uid'),
				    "remark"      	   => "Feedback Collected."
				   ); 
		$this->$model->insertData($logdata,'appt_request_log'); 
			
		if($this->db->trans_status() === TRUE){
			$this->db->trans_commit();  
			//Send SMS
			echo 1;
		}else{
			$this->db->trans_rollback(); 
			echo 0;	
		} 
	}
	
	public function appt_req_reject()
	{
		$model = self::SLOT_MODEL; 
		$id_appt_request = $this->input->post('id_appt_request');
		$this->db->trans_begin();  
		$data = array(  
		    "reject_reason"    => $this->input->post('reject_reason'),
		    "status"	       => 2, // Rejected
		    "updated_on"       => date('Y-m-d H:i:s'),
		    "updated_by"       => $this->session->userdata('uid'),
		   ); 
		$apptreq_rej = $this->$model->updateData($data,'id_appt_request',$id_appt_request,'appt_request');  		$logdata = array(  
				    "id_appt_request"  => $id_appt_request,
				    "status"	       => 2, // Rejected
				    "event_date"       => date('Y-m-d H:i:s'),
				    "id_employee"      => $this->session->userdata('uid'),
				    "remark"      	   => "Appointment Rejected."
				   ); 
		$this->$model->insertData($logdata,'appt_request_log'); 
			
		if($this->db->trans_status() === TRUE){
			$this->db->trans_commit();  
			/* Send Alert to Customer SMS,Email */
			$cusServData = $this->sms_model->SMS_dataByServCode('VS_STATUS',$id_appt_request);  
			if($cusServData['serv_sms'] == 1 &&  strlen($cusServData['mobile']) == 10){ 
				if($this->config->item('sms_gateway') == '1'){
	    		    $this->sms_model->sendSMS_MSG91($cusServData['mobile'],$cusServData['message'],'','');
	    		}
	    		elseif($this->config->item('sms_gateway') == '2'){
	    	        $this->sms_model->sendSMS_Nettyfish($cusServData['mobile'],$cusServData['message'],'trans');
	    		} 
			}
			if($cusServData['serv_email'] == 1 && isset($cusServData['cus_data']['email']) && $cusServData['cus_data']['email'] != ''){
				$to = $cusServData['cus_data']['email'];
				$subject = "Reg - ".$this->comp['company_name']." video shopping appointment";
				$data['mailData'] = $cusServData['cus_data'];
				$data['type'] = 1;
				$data['company_details'] = $this->comp;
				$message = $this->load->view('include/vs_appt',$data,true);
				$sendEmail = $this->email_model->send_email($to,$subject,$message,"",""); 
			} 
			echo 1;
		}else{
			$this->db->trans_rollback(); 
			echo 0;	
		} 
	}
	
	public function allocate_appt()
	{
		$model =	self::SLOT_MODEL;
		$this->db->trans_begin();  
		$data = array(  
		    "status"	       => 1, // Alloted
		    "updated_on"       => date('Y-m-d H:i:s'),
			"alloted_slot" 	   => $_POST['slot'],
		    "updated_by"       => $this->session->userdata('uid'),
		   ); 
		$upd = $this->$model->updateData($data,'id_appt_request',$_POST['id_appt_request'],'appt_request');  
		if($upd){
			foreach($_POST['data'] as $data){
				$alloc_emp = array(  
				    "id_appt_request" => $_POST['id_appt_request'],
					"alloted_emp"     => $data,
				    "created_on"      => date('Y-m-d H:i:s') 
				   );  
				$status = $this->$model->insertData($alloc_emp,'appt_emp_allot'); 	
			}
			
			$logdata = array(  
			    "id_appt_request"  => $_POST['id_appt_request'],
			    "status"	       => 1, // Alloted
			    "event_date"       => date('Y-m-d H:i:s'),
			    "id_employee"      => $this->session->userdata('uid'),
			    "remark"      	   => "Appointment fixed & Employee allocated."
			   ); 
			$this->$model->insertData($logdata,'appt_request_log');  
		}
		 		
		if($this->db->trans_status() === TRUE){
			$this->db->trans_commit(); 
			/* Send Alert to Customer SMS,Email */
			$cusServData = $this->sms_model->SMS_dataByServCode('VS_STATUS',$_POST['id_appt_request']);  
			if($cusServData['serv_sms'] == 1 &&  strlen($cusServData['mobile']) == 10){ 
				if($this->config->item('sms_gateway') == '1'){
	    		    $this->sms_model->sendSMS_MSG91($cusServData['mobile'],$cusServData['message'],'','');
	    		}
	    		elseif($this->config->item('sms_gateway') == '2'){
	    	        $this->sms_model->sendSMS_Nettyfish($cusServData['mobile'],$cusServData['message'],'trans');
	    		} 
			} 
			if($cusServData['serv_email'] == 1 && isset($cusServData['cus_data']['email']) && $cusServData['cus_data']['email'] != ''){
				$to = $cusServData['cus_data']['email'];
				$subject = "Reg - ".$this->comp['company_name']." video shopping appointment";
				$edata['mailData'] = $cusServData['cus_data'];
				$edata['type'] = 1;
				$edata['company_details'] = $this->comp;
				$message = $this->load->view('include/vs_appt',$edata,true);
				$sendEmail = $this->email_model->send_email($to,$subject,$message,"",""); 
			}
			echo 1;
		}else{
			$this->db->trans_rollback(); 
			echo 0;	
		}
	}
 	
 	public function appt_completed()
	{
		$model =	self::SLOT_MODEL;
		$success = 0;
		foreach($_POST['data'] as $id_appt_request){
			$this->db->trans_begin();  
			$data = array(  
			    "status"	       => 3, // Completed
			    "updated_on"       => date('Y-m-d H:i:s'),
			    "updated_by"       => $this->session->userdata('uid'),
			   ); 
			$upd = $this->$model->updateData($data,'id_appt_request',$id_appt_request,'appt_request'); 
			if($upd){
				$logdata = array(  
				    "id_appt_request"  => $id_appt_request,
				    "status"	       => 3, // Completed
				    "event_date"       => date('Y-m-d H:i:s'),
				    "id_employee"      => $this->session->userdata('uid'),
				    "remark"      	   => "Appointment completed."
				   ); 
				$status = $this->$model->insertData($logdata,'appt_request_log');
			}  
			if($this->db->trans_status() === TRUE){
				$this->db->trans_commit();  
				$success = $success+1;
				/* Send Alert to Customer SMS,Email */
				$cusServData = $this->sms_model->SMS_dataByServCode('VS_STATUS',$id_appt_request);  
				if($cusServData['serv_sms'] == 1 &&  strlen($cusServData['mobile']) == 10){ 
					if($this->config->item('sms_gateway') == '1'){
		    		    $this->sms_model->sendSMS_MSG91($cusServData['mobile'],$cusServData['message'],'','');
		    		}
		    		elseif($this->config->item('sms_gateway') == '2'){
		    	        $this->sms_model->sendSMS_Nettyfish($cusServData['mobile'],$cusServData['message'],'trans');
		    		} 
				}
				if($cusServData['serv_email'] == 1 && isset($cusServData['cus_data']['email']) && $cusServData['cus_data']['email'] != ''){
					$to = $cusServData['cus_data']['email'];
					$subject = "Reg - ".$this->comp['company_name']." video shopping appointment";
					$data['mailData'] = $cusServData['cus_data'];
					$data['type'] = 1;
					$data['company_details'] = $this->comp;
					$message = $this->load->view('include/vs_appt',$data,true);
					$sendEmail = $this->email_model->send_email($to,$subject,$message,"",""); 
				} 
			}else{
				$this->db->trans_rollback();  
			} 
		} 
		echo $success;	 
	}
   	
   	public function update_slot()
	{
		$model =	self::SLOT_MODEL;
		$this->db->trans_begin();  
		$data = array(  
				"slot_no"			=> $_POST['slot_no'],
                "slot_date"		    => $_POST['slot_date'],
                "slot_time_from"	=> $_POST['slot_time_from'],
                "slot_time_to"		=> $_POST['slot_time_to'],
                "allowed_booking" 	=> $_POST['allowed_booking'],
			    "date_upd"       	=> date('Y-m-d H:i:s'),
			    "updated_by"        => $this->session->userdata('uid'),
			   ); 
		$upd = $this->$model->updateData($data,'id_appointment_slot',$_POST['id_appointment_slot'],'appt_slots');
		if($this->db->trans_status() === TRUE){
			$this->db->trans_commit(); 
			echo 1;
		}else{
			$this->db->trans_rollback(); 
			echo 0;	
		}
	}

	public function updateappreq_status($type=""){	
	 	
		$model =	self::SLOT_MODEL;
		switch($type)
	   {
			case 'UpdateStatus':
			$data = $this->$model->update_app_reqStatus($_POST); 
			echo json_encode($data);
			break;
	   
		}
	
	
	}
   
}
?>