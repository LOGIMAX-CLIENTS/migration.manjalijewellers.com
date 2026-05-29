<?php
class Vs_model extends CI_Model {
	public function __construct() {
		$this->load->database();
	}
	
	function insertData($insData,$table)
    {
		$status = $this->db->insert($table,$insData); 
		return array('status' => $status, 'insertID' => $this->db->insert_id());
	}
	
	public function updateData($data,$id_field,$id_value,$table)
    {    
	    $edit_flag = 0;
	    $this->db->where($id_field,$id_value);
		$edit_flag = $this->db->update($table,$data);
		return ($edit_flag==1?$id_value:0);
	}
	
	function getSettings()
    {
		$sql = $this->db->query("SELECT m_active,m_web,m_app,appt_auto_assign,vs_send_sms_to,vs_send_mail_to FROM modules 
		 join chit_settings
		 where m_code='VS'"); 
		return $sql->row_array();
	} 
	
	function getAvailableSlots()
    {
    	
    	$result = array();
		$sql = $this->db->query("SELECT 
									id_appointment_slot,s.slot_no,DATE_FORMAT(slot_date,'%d-%m-%Y') as slot_date,allowed_booking,IFNULL(alloted_slots,0) as alloted_slots,TIME_FORMAT(slot_time_from, '%h:%i %p') as time_from,TIME_FORMAT(slot_time_to, '%h:%i %p') as time_to,
									IF(alloted_slots IS NULL,allowed_booking,(allowed_booking-alloted_slots)) as available_slots
								FROM appt_slots s
									LEFT JOIN 
									(
										SELECT 
											count(alloted_slot) as alloted_slots,alloted_slot as slot_no
										FROM `appt_request` 
										WHERE alloted_slot IS NOT NULL 
										GROUP BY alloted_slot
									) alloted
									ON alloted.slot_no = s.id_appointment_slot
								WHERE (unix_timestamp(concat(slot_date,' ',slot_time_from)) >= unix_timestamp('".date('Y-m-d H:i:s')."'))
								ORDER BY slot_date,slot_time_to
								");  			
		$res = $sql->result_array();
		foreach($res as $r){
			if($r['available_slots'] > 0){
			    $result[$r['slot_date']][] = $r;
		    }
		}
		return $result;
	}
	
	function isSlotAvailable($slot_id)
    {
    	$result = array();
		$sql = $this->db->query("SELECT 
									s.slot_no,id_appointment_slot,IF(alloted_slots IS NULL,allowed_booking,(allowed_booking-alloted_slots)) as available_slots
								FROM appt_slots s
									LEFT JOIN 
									(
										SELECT 
											count(alloted_slot) as alloted_slots,alloted_slot as slot_no
										FROM `appt_request` 
										WHERE alloted_slot IS NOT NULL 
										GROUP BY alloted_slot
									) alloted
									ON alloted.slot_no = s.id_appointment_slot
								WHERE  id_appointment_slot = ".$slot_id
								); 	 			
		$res = $sql->result_array();
		if($sql->num_rows() > 0){
			if($res[0]['available_slots']>0){
				return TRUE;
			}else{
				return FALSE;
			}			
		}else{
			return FALSE;
		} 
	}
	
	function isPrevRequested($slot_id,$mobile)
    {
    	$result = array();
		$sql = $this->db->query("SELECT preferred_slot,mobile
									FROM `appt_request`
								WHERE status != 2 AND preferred_slot = ".$slot_id." AND mobile = ".$mobile
								);  			
		$res = $sql->result_array();
		if($sql->num_rows() > 0){
			return TRUE;
		}else{
			return FALSE;
		} 
	}
	
	function getUserVSAppts($mobile)
    {
    	$result = array();
		$sql = $this->db->query("SELECT 
									req.name, req.mobile,id_appt_request,
									IF(req.status = 0, 'Yet To Confirm', IF(req.status = 1, 'Confirmed', IF(req.status = 2, 'Rejected', IF(req.status = 3, 'Completed', IF(req.status = 4, 'Closed', ''))))) as status_msg,status,
									IFNULL(Date_format(req.created_on,'%d %b% %Y'),'-') as created_on,
									(SELECT CONCAT(CONCAT(DATE_FORMAT(s.slot_date,'%d %b %Y'),' [',CONCAT(TIME_FORMAT(s.slot_time_from, '%h:%i %p'),' - ',TIME_FORMAT(s.slot_time_to, '%h:%i %p'))),']') as pref_slot FROM appt_slots s where s.id_appointment_slot = req.preferred_slot) as pref_slot,
									(SELECT CONCAT(CONCAT(DATE_FORMAT(sl.slot_date,'%d %b %Y'),' [',CONCAT(TIME_FORMAT(sl.slot_time_from, '%h:%i %p'),' - ',TIME_FORMAT(sl.slot_time_to, '%h:%i %p'))),']') as pref_slot FROM appt_slots sl where sl.id_appointment_slot = req.alloted_slot) as alloted_slot
								FROM appt_request req
								WHERE mobile=".$mobile." Order by id_appt_request DESC"
								);  			
		$res = $sql->result_array();
		return $res; 
	}
	
	function getApptDetail($id)
    {
    	$result = array();
		$sql = $this->db->query("SELECT 
									req.name, req.mobile,id_appt_request,
									if(pref_category =1,'Gold',if(pref_category =2,'Silver',if(pref_category =3,'Platinum',if(pref_category =4,'Diamond','')))) as pref_category,
									if(email is null or email ='','-',email) as email, 
									if(whats_app_no is null or whats_app_no ='','-',whats_app_no) as whats_app_no, 
									if(pref_item is null or pref_item ='','-',pref_item) as pref_item, 
									if(location is null or location ='','-',location) as location,
									if(description is null or description ='','-',description) as description,
									if(reject_reason is null or reject_reason ='','-',reject_reason) as reject_reason,
									if(customer_feedback is null or customer_feedback ='','-',customer_feedback) as customer_feedback,
									if(customer_feedback is null or customer_feedback ='',0,1) as show_feedback,
									if(req.status = 3 AND (customer_feedback is null or customer_feedback =''),1,0) as get_feedback,
									IF(req.status = 0, 'Yet To Confirm', IF(req.status = 1, 'Confirmed', IF(req.status = 2, 'Rejected', IF(req.status = 3, 'Completed', IF(req.status = 4, 'Closed', ''))))) as status_msg,status,
									IFNULL(Date_format(req.created_on,'%d %b% %Y'),'-') as created_on,
									(SELECT CONCAT(CONCAT(DATE_FORMAT(s.slot_date,'%d %b %Y'),' [',CONCAT(TIME_FORMAT(s.slot_time_from, '%h:%i %p'),' - ',TIME_FORMAT(s.slot_time_to, '%h:%i %p'))),']') as pref_slot FROM appt_slots s where s.id_appointment_slot = req.preferred_slot) as pref_slot,
									(SELECT CONCAT(CONCAT(DATE_FORMAT(sl.slot_date,'%d %b %Y'),' [',CONCAT(TIME_FORMAT(sl.slot_time_from, '%h:%i %p'),' - ',TIME_FORMAT(sl.slot_time_to, '%h:%i %p'))),']') as pref_slot FROM appt_slots sl where sl.id_appointment_slot = req.alloted_slot) as alloted_slot
								FROM appt_request req
								WHERE id_appt_request=".$id
								);  			
		$res['req'] = $sql->row_array();
		//$res['detail'] = $sql->result_array();
		return $res; 
	}
	
	function getCusByMob($mobile)
	{
		$sql = $this->db->query("
			SELECT 
				CONCAT(firstname,' ',if(lastname!=NULL,lastname,'')) as name,c.mobile,c.email
			FROM customer c 
			WHERE c.active=1 AND c.mobile =".$mobile 
		); 
	    return $sql->row_array(); 
 	} 
	
	
}	
?>