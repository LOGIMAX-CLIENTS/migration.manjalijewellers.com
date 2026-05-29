<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Chitadmin_model extends CI_Model

{

	const TAB_ENQ ="cust_enquiry";

	const TAB_CHITSET ="chit_settings";

	

	public function __construct() {

		$this->load->model('admin_usersms_model');

		$this->sms_data = $this->admin_usersms_model->sms_info();
		$this->pro_data = $this->admin_usersms_model->promotion_info();
		$this->pro_chk = $this->admin_usersms_model->promotion_smsavilable();
		$this->sms_chk = $this->admin_usersms_model->otp_smsavilable();

	}	

	//encrypt

	public function __encrypt($str)

	{

		return base64_encode($str);		

	}
	
	//decrypt

	public function __decrypt($str)

	{

		return base64_decode($str);		

	}
	
	
	// General Functions
    public function insertData($data,$table)
    {
    	$insert_flag = 0;
		$insert_flag = $this->db->insert($table,$data);
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
	
	function forgetUser($mail,$mble)

	{

	
		$query = $this->db->query('SELECT firstname,mobile,email,passwd FROM employee WHERE email = "'.$mail.'" AND mobile = "'.$mble.'"');

			if($query->num_rows() > 0)

			{

					$row = $query->row();//print_r($row);

					$this->session->unset_userdata("OTP");

					$OTP = mt_rand(1001,9999);

					$this->session->set_userdata('OTP',$OTP);

					

					 $mobile = $row->mobile;

					 $message="	Dear ".$row->firstname.", Your otp to reset password is ".$OTP." ";

					 $this->send_sms($mobile,$message);


					$to = $row->email;

					$data['otp']=$OTP;

					$data['type'] = 0;

					$data['company'] = $this->comp;
					
					$data['name'] = $row->firstname;//print_r($data);

					$subject = "Reg: ".$this->comp['company_name']." Employee forgot password";

					$message = $this->load->view('include/emailAccount',$data,true);

					$this->load->model('email_model');

					$sendEmail = $this->email_model->send_email($to,$subject,$message);

					

			return 1;

				

			}

			else{

			       return 0;

			}

	}
	
	function forgot_pswd_reset($mobile)

	{

		$resultset = $this->db->query("UPDATE employee SET passwd ='".$this->__encrypt($this->input->post('passwd'))."' WHERE  mobile='".$mobile."'");

		if ($this->db->affected_rows() > 0)	

		{

			return 1;

		}

		else	

		{

			return 0;

		}

	}


	function customer_data($mobile)

	{

		$sql = " Select  * from employee where mobile=".$mobile;

		$result = $this->db->query($sql);	

		return $result->row_array();

	}
	
	

	function get_join_requests()

	{

		$this->db->select('id_enquiry,date_enquiry,chit_acc_number,name,mobile,comments,status');

		$r=$this->db->get(self::TAB_ENQ);

		return $r->result_array();

	}

	

	function update_enquiry($data,$id)

	{

		$this->db->where('id_enquiry',$id);

		$r=$this->db->update(self::TAB_ENQ,$data);

		return $r;

	} 

	

	function allow_multiple_chit()

	{

	   $this->db->select('allow_join_multiple');

       $r = $this->db->get(self::TAB_CHITSET);

       return $r->row_array('allow_join_multiple'); 	   

	}

	
	
	//Promotion sms and otp setting

	function send_sms($mobile,$message,$data="")

	{	
	
	
		
		if($data==1){
			$url = $this->pro_data['promotion_url'];
			$senderid  = $this->pro_data['promotion_sender_id'];
			}else{
				
				$url = $this->sms_data['sms_url'];
				$senderid  = $this->sms_data['sms_sender_id'];
			 }
			 
		
		
	if(($this->pro_chk['debit_promotion']!=0 && $data==1)) 
	{
		
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
			
		$status=$this->update_prosms();		
		if($status==1){		
		   return TRUE;
		}
		 return FALSE;
	}
	else if($this->sms_chk['debit_sms']!=0){
		
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
  
  //Promotion sms and otp setting
	
	
// branch settings
	
	
	function branch_settings()

	{

		$sql = " SELECT c.company_settings,c.branch_settings,c.branchWiseLogin,c.isOTPReqToLogin,c.is_branchwise_cus_reg,c.is_branchwise_rate,c.login_branch,c.branchwise_scheme,c.enable_old_sch_acc_no FROM chit_settings c where c.id_chit_settings =1";
		
		$result = $this->db->query($sql);	

		return $result->row_array();

	}
	
	
// branch settings


//Promotion sms and otp setting


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
  
  function update_prosms()
  {
		$query_validate=$this->db->query('UPDATE promotion_api_settings SET debit_promotion = debit_promotion - 1 WHERE id_promotion_api =1 and debit_promotion > 0');  			
	         if($query_validate>0)
			{
				return true;
			}else{
				
				return false;
			}
  } 
	
//Promotion sms and otp setting
	
	
	function prof_wise_loginotp_exp()
	{
		 $sql="Select prof_wise_loginotp_exp from chit_settings where id_chit_settings = 1"; 
		 return $this->db->query($sql)->row()->prof_wise_loginotp_exp;
	}
	
	function get_counter($system_fp_id)
	{
	    $sql=$this->db->query("SELECT c.counter_id,c.floor_id
        FROM ret_branch_floor_counter c
        WHERE c.counter_status=1 AND c.system_fp_id='".$system_fp_id."'");         
         if($sql->num_rows() > 0){
		 	return $sql->row('counter_id');
		 }else{
		 	return NULL;
		 }         
	}
	
	
	function get_device_details($token_id)
	{
	    $return_data=array();
	    $sql=$this->db->query("SELECT * FROM `web_registered_devices` WHERE token_id='".$token_id."' AND status=1");         
         if($sql->num_rows() > 0){
		 	$return_data=array('status'=>TRUE,'result'=>$sql->row_array());
		 }else{
		 	$return_data=array('status'=>FALSE,'result'=>'','message'=>'Invalid Device.Please Contact Your Admin..');
		 } 
		 return $return_data;
	}
	
	function get_profile_settings($id_profile)
	{
	    $sql=$this->db->query("select * from profile where id_profile=".$id_profile."");
	    return $sql->row_array();
	}
	function companyname_list()
	{
	    $company = $this->db->query("SELECT c.company_name,c.id_company FROM company c");
	    return $company->result_array();
	}

}

?>