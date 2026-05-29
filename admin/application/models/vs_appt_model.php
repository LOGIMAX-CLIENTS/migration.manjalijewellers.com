 <?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Vs_appt_model extends CI_Model
{       
	function __construct()
    {
        parent::__construct();
    } 
    
    // Default CURD functions
	public function insertData($data,$table)
    {
    	$insert_flag = 0;
		$insert_flag = $this->db->insert($table,$data);
		//print_r($this->db->last_query());exit;
		return ($insert_flag == 1 ? $this->db->insert_id(): 0);
	}
	
	public function updateData($data,$id_field,$id_value,$table)
    {    
	    $edit_flag = 0;
	    $this->db->where($id_field,$id_value);
		$edit_flag = $this->db->update($table,$data);
		return ($edit_flag==1?$id_value:0);
	}	
	 
	public function deleteData($id_field,$id_value,$table)
    {
        $this->db->where($id_field, $id_value);
        $status= $this->db->delete($table); 
		return $status;
	} 
	
     
	function approvalApptRequests($data)
	{  
		 $sql=("SELECT 
					req.name, req.mobile,id_appt_request,
					if(pref_category =1,'Gold',if(pref_category =2,'Silver',if(pref_category =3,'Platinum',if(pref_category =4,'Diamond','')))) as pref_category,
					if(email is null or email ='','-',email) as email, 
					if(whats_app_no is null or whats_app_no ='','-',whats_app_no) as whats_app_no, 
					if(pref_item is null or pref_item ='','-',pref_item) as pref_item, 
					if(location is null or location ='','-',location) as location,
					if(description is null or description ='','-',description) as description,
					if(reject_reason is null or reject_reason ='','-',reject_reason) as reject_reason,
					if(customer_feedback is null or customer_feedback ='','-',customer_feedback) as customer_feedback,
					if(customer_feedback is null or customer_feedback ='',1,0) as get_feedback,
					IF(req.status = 0, 'Open', IF(req.status = 1, 'Alloted', IF(req.status = 2, 'Rejected', IF(req.status = 3, 'Completed', IF(req.status = 4, 'Closed', ''))))) as status,
					IFNULL(Date_format(req.created_on,'%d-%m%-%Y'),'-') as created_on,
					CONCAT(DATE_FORMAT(s.slot_date,'%d-%m-%Y'),' ',CONCAT(TIME_FORMAT(s.slot_time_from, '%h:%i %p'),' - ',TIME_FORMAT(s.slot_time_to, '%h:%i %p'))) as pref_slot,
					
					preferred_slot,
					(IFNULL(s.allowed_booking,0) - IFNULL(allot.alloted_slots,0)) as available_slots
				FROM appt_request req
				LEFT JOIN appt_slots s ON s.id_appointment_slot = req.preferred_slot
				LEFT JOIN (SELECT 
								count(alloted_slot) as alloted_slots,alloted_slot
							FROM `appt_request`  r 
							GROUP BY alloted_slot) allot ON allot.alloted_slot = req.preferred_slot
				WHERE req.status = 0 and unix_timestamp(concat(s.slot_date,' ',slot_time_from)) >=unix_timestamp('".date('Y-m-d H:i:s')."')
				GROUP BY id_appt_request "
				); 
		 $res = $this->db->query($sql)->result_array();
		 return $res;
	}
	
	function apptRequestsByFilter($data)
	{  
		 $sql=("SELECT 
					req.name, req.mobile,req.id_appt_request,ea.alloted_emp as alloted_emp,ifnull(req.remarks,'') as  remarks,req.status as sts_code,
					IF(alloted_emp = 1, 'Logimax', IF(alloted_emp = 2, 'RADHA', IF(alloted_emp = 3, 'Sankar', '-'))) as alloted_emp,
					if(pref_category =1,'Gold',if(pref_category =2,'Silver',if(pref_category =3,'Platinum',if(pref_category =4,'Diamond','')))) as pref_category,
					if(email is null or email ='','-',email) as email, 
					if(whats_app_no is null or whats_app_no ='','-',whats_app_no) as whats_app_no, 
					if(pref_item is null or pref_item ='','-',pref_item) as pref_item, 
					if(location is null or location ='','-',location) as location,
					if(description is null or description ='','-',description) as description,
					if(reject_reason is null or reject_reason ='','-',reject_reason) as reject_reason,
					if(customer_feedback is null or customer_feedback ='','-',customer_feedback) as customer_feedback,
					if(customer_feedback is null or customer_feedback ='',1,0) as get_feedback,
					IF(req.status = 0, 'Open', IF(req.status = 1, 'Alloted', IF(req.status = 2, 'Rejected', IF(req.status = 3, 'Completed', IF(req.status = 4, 'Closed', ''))))) as status,
					IFNULL(req.created_on,'-') as created_on,
					IFNULL(req.prefered_lang,'-') as prefered_lang,
					(SELECT CONCAT(CONCAT(DATE_FORMAT(s.slot_date,'%d-%m-%Y'),'[',CONCAT(TIME_FORMAT(s.slot_time_from, '%h:%i %p'),' - ',TIME_FORMAT(s.slot_time_to, '%h:%i %p'))),']') as pref_slot FROM appt_slots s where s.id_appointment_slot = req.preferred_slot) as pref_slot,
					(SELECT CONCAT(DATE_FORMAT(sl.slot_date,'%m/%d/%Y'),' ',sl.slot_time_from) as pref_slot FROM appt_slots sl where sl.id_appointment_slot = req.alloted_slot) as alloted_slot,
					IFNULL((select id_appt_request from appt_request where alloted_slot=req.preferred_slot),0) as is_available,
					CONCAT(req.booking_date,' ',req.booking_time) as scheduled_time
				FROM appt_request req 
				   LEFT JOIN appt_emp_allot ea ON ea.id_appt_request=req.id_appt_request
				".($data['status'] != '' || $data['from_date'] != ''? 'WHERE': '')."
				".($data['status'] != '' ? ' req.status='.$data['status'].' AND ': '')."
				".($data['from_date'] != '' ? '  date(req.created_on) BETWEEN "'.date('Y-m-d',strtotime($data['from_date'])).'" AND "'.date('Y-m-d',strtotime($data['to_date'])).'"' : '')." group by req.id_appt_request"
				); 
			//	print_r($sql);exit;
		 $res = $this->db->query($sql)->result_array();

		 return $res;
	}
	
    function emp_available($id_appt_request,$preferred_slot)
    {
       $sql="SELECT 
	         id_employee,firstname,lastname FROM employee WHERE id_employee NOT IN (SELECT alloted_emp 
	       FROM `appt_request` req 
		       LEFT JOIN appt_emp_allot ea ON ea.id_appt_request=req.id_appt_request 
	       WHERE alloted_slot=".$preferred_slot.")";
	    return $this->db->query($sql)->result_array();
    }
     
    function get_appts_req($id_appt_request)
	{
		$this->db->where('id_appt_request',$id_appt_request);
	    $r = $this->db->get("appt_request");
		if($r->num_rows == 1)
		{
			$result = $r->row_array();
			return $result;
		}
		else
		{
			return array('status'=>2,'msg'=>'Invalid');
		}
	} 
	
	function get_emp($id_appt_request,$preferred_slot,$id_employee)
	{
		$this->db->where('alloted_slot',$preferred_slot);
	    $data = $this->emp_available($id_appt_request,$preferred_slot);
		return $data;
	}
	 
	function add_appt_req($data)
	{
		$status = $this->db->insert("appt_emp_allot",$data);
		return $status;
 	}
 	
 	function getActiveSlots()
	{
		$sql = "SELECT 
		         	id_appointment_slot, slot_no, DATE_FORMAT(slot_date,'%d-%m-%Y') as slot_date,DATE_FORMAT(date_add,'%d-%m-%Y %H:%i:%s') as date_add, allowed_booking, created_by,TIME_FORMAT(slot_time_from, '%h:%i %p') as slot_time_from,TIME_FORMAT(slot_time_to, '%h:%i %p') as slot_time_to,slot_time_from as time_from,slot_time_to as time_to,
		         	(
		        		SELECT count(id_appt_request) as slot_used from appt_request r 
							WHERE r.preferred_slot = s.id_appointment_slot OR r.alloted_slot = s.id_appointment_slot 
						GROUP BY s.id_appointment_slot
					) as userbookings
		        FROM appt_slots s 
		        WHERE (unix_timestamp(slot_date) >= unix_timestamp('".date('Y-m-d')."'))
	         "; 
	      //  echo $sql;
	    return $this->db->query($sql)->result_array();
 	} 
 	
	function isSlotExist($data)
	{
		$sql = $this->db->query("SELECT 
		         	id_appointment_slot
		        FROM appt_slots
		        WHERE (slot_no=".$data['slot_no']." AND slot_date='".$data['slot_date']."') OR (slot_no=".$data['slot_no']." AND slot_date='".$data['slot_date']."' AND slot_time_from='".$data['slot_time_from']."' AND slot_time_to='".$data['slot_time_to']."' )
	         ");
	    if($sql->num_rows > 0){
			return TRUE;
		}else{
			return FALSE;
		} 
 	} 
	 function update_app_reqStatus($data){

	  
		$this->db->where("id_appt_request", $data['id_app_req']);
		$parent = $this->db->update("appt_request",array('status'=>$data['filtered_req_status'],'remarks' => $data['app_reqremarks']));
		if($parent){
 
		 if($parent){
			 $insdata = array(
						  'id_employee'       => $this->session->userdata('uid'),
						  'remark'        => $data['app_reqremarks'],
						  'status'   => $data['filtered_req_status'],
						  'id_appt_request'        => $data['id_app_req'],
						  'event_date'          => date('Y-m-d H:i:s')
						  );
			 $status = $this->db->insert("appt_request_log",$insdata);
		 }
		 return	$status;
 
		}else{
 
		 return false;
		}
	 }
	
}
?>