<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_usersms extends CI_Controller

{

	

	const SMS_MODEL	="admin_usersms_model";

	const CUS_MODEL	="customer_model";

	const VIEW ='sms/';

	const GRP ='sms/group/';

	const SMS_VIEW ='sms/smsService/';
	
	const SET_VIEW ='settings/module/';
	
	const NOTI_VIEW ='notification/';

	const SET_MODEL = 'admin_settings_model';

	const EMAIL_MODEL = 'email_model';

	const ADM_MODEL = 'chitadmin_model';
	
	const LOG_MODEL = "log_model";

	const RET_SET_VIEW ='settings/retail_setting/';

		function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model(self::SMS_MODEL);

		$this->load->model(self::SET_MODEL);

		$this->load->model(self::CUS_MODEL);

		$this->load->model(self::EMAIL_MODEL);

		$this->load->model(self::ADM_MODEL);
		$this->load->model('sms_model');
        $this->load->model(self::LOG_MODEL);
        $this->id_log =  $this->session->userdata('id_log');
		if(!$this->session->userdata('is_logged'))

		{

			redirect('admin/login');

		}	

	}

		

	public function index()

	{	

		$model_name=self::SMS_MODEL;

		$this->load->model($model_name);				

		$data['db_error_msg'] ="";

		$data['main_content'] = self::VIEW."usersms" ;

		$this->load->view('layout/template', $data);	

		

	}

	function open_entry_form($type="",$id="")

	{		

		$model_name=self::SMS_MODEL;

		$this->load->model($model_name);				

		$data['db_error_msg'] ="";

		$data['main_content'] = self::VIEW."usersms" ;

		$this->load->view('layout/template', $data);

	}

	

	function send_sms()

	{	

		$mobile=$_POST["group_id"];

		$msg=$_POST["send_type"];

		$model = self::ADM_MODEL;
 
		if($this->config->item('sms_gateway') == '1'){
			$sendSMS = $this->sms_model->sendSMS_MSG91($mobile,$msg);		
		}
		elseif($this->config->item('sms_gateway') == '2'){
			$sendSMS = $this->sms_model->sendSMS_Nettyfish($mobile,$msg,'promo');	
		}
		elseif($this->config->item('sms_gateway') == '3')
    	{
           $sendSMS =  $this->sms_model->sendSMS_SpearUC($mobile,$msg,'','');	
        }
        elseif($this->config->item('sms_gateway') == '4')
    	{
           $sendSMS =  $this->sms_model->sendSMS_Asterixt($mobile,$msg,'','');	
        }
        elseif($this->config->item('sms_gateway') == '5')
    	{
           $sendSMS =  $this->sms_model->sendSMS_Qikberry($mobile,$msg,'','');	
        }

		echo ($sendSMS==TRUE?"success":"failed"); 

	}

//selected bassed to group_message 	

//all customer
	function sendsms_allcustomer(){
		$data = $_POST;		
		$models = self::ADM_MODEL;
		$model = self::SMS_MODEL;
		$message = $data['message'];		
		
			$this->db->trans_begin();	
			$mobi_no=$this->$model->get_allcustomersms_list();
				foreach($mobi_no as $mobile)
				{
					
					if($this->config->item('sms_gateway') == '1'){
						$sendSMS = $this->sms_model->sendSMS_MSG91($mobile,$message);		
					}
					elseif($this->config->item('sms_gateway') == '2'){
						$sendSMS = $this->sms_model->sendSMS_Nettyfish($mobile,$message,'promo');	
					}
					elseif($this->config->item('sms_gateway') == '3')
                	{
                        	$sendSMS = $this->sms_model->sendSMS_SpearUC($mobile,$message,'','');	
                    }
                    elseif($this->config->item('sms_gateway') == '4')
                	{
                       $sendSMS =  $this->sms_model->sendSMS_Asterixt($mobile,$message,'','');	
                    }
                    elseif($this->config->item('sms_gateway') == '5')
                	{
                       $sendSMS =  $this->sms_model->sendSMS_Qikberry($mobile,$message,'','');	
                    }
					
				}
			if($this->db->trans_status()===TRUE)
				{
					$this->db->trans_commit();	
					echo ($sendSMS==TRUE?"success":"failed");
				}
	}
	
	
//selected customer


	function get_selectcustomer_list()
	{		
        $model = self::SMS_MODEL;
		$id_branch=$this->input->post('id_branch');
		$data['group']=$this->$model->get_selectcustomersms_data($id_branch);
	    echo json_encode($data);
	}

	
	function sendsms_selectcustomer(){
		
		$data = $_POST;		
		$models = self::ADM_MODEL;
		$model = self::SMS_MODEL;
		$message = $data['message'];
		$mobi_no = $data['customer'];
			$this->db->trans_begin();
				foreach($mobi_no as $mobile)
				{
					if($this->config->item('sms_gateway') == '1'){
						$sendSMS = $this->sms_model->sendSMS_MSG91($mobile,$message);		
					}
					elseif($this->config->item('sms_gateway') == '2'){
						$sendSMS = $this->sms_model->sendSMS_Nettyfish($mobile,$message,'promo');	
					}
					elseif($this->config->item('sms_gateway') == '3')
                	{
                        	$sendSMS = $this->sms_model->sendSMS_SpearUC($mobile,$message,'','');	
                    }
                    elseif($this->config->item('sms_gateway') == '4')
                	{
                       $sendSMS =  $this->sms_model->sendSMS_Asterixt($mobile,$message,'','');	
                    }
                    elseif($this->config->item('sms_gateway') == '5')
                	{
                       $sendSMS =  $this->sms_model->sendSMS_Qikberry($mobile,$message,'','');	
                    }
				}
			if($this->db->trans_status()===TRUE)
				{
					$this->db->trans_commit();	
					echo ($sendSMS==TRUE?"success":"failed");
				}
	}
//selected bassed to group_message 


//selected bassed to group_email


// all customer

	function sendemail_allcustomer()
	{
		$mail =$_POST;
		$model=self::EMAIL_MODEL;
		$models = self::SMS_MODEL;
		$this->db->trans_begin();	
		$email_id=$this->$models->get_allcustomeremail_list();

			foreach($email_id as $email)
			{ 
				$sendEmail = $this->$model->send_email($email,$mail['subject'],$mail['message']);
			}
		if($this->db->trans_status()===TRUE)
		{
				$this->db->trans_commit();	
		 echo ($sendEmail?"success":"failed");	
		}
	}

// selected customer

	function sendemail_selectedcustomer()
	{
		$mail =$_POST;
		$model=self::EMAIL_MODEL;
		$models = self::SMS_MODEL;
		$this->db->trans_begin();
		$email_id=$mail['customer'];
			foreach( $email_id as $email)
			{ 
				$sendEmail = $this->$model->send_email($email,$mail['subject'],$mail['message']);
			}
		if($this->db->trans_status()===TRUE)
		{
				$this->db->trans_commit();	
		 echo ($sendEmail?"success":"failed");	
		}
	}







//selected bassed to group_email

	function send_group_sms()

	{

		$data = $_POST;

		$mobile  = implode(",",$data['customer']);

		$message = $data['message'];

		$model = self::ADM_MODEL;		
		
		if($this->config->item('sms_gateway') == '1'){
			$sendSMS = $this->sms_model->sendSMS_MSG91($mobile,$message);		
		}
		elseif($this->config->item('sms_gateway') == '2'){
			$sendSMS = $this->sms_model->sendSMS_Nettyfish($mobile,$message,'promo');	
		}
		elseif($this->config->item('sms_gateway') == '3')
        {
        	$sendSMS = $this->sms_model->sendSMS_SpearUC($mobile,$message,'','');	
        }
        elseif($this->config->item('sms_gateway') == '4')
    	{
           $sendSMS =  $this->sms_model->sendSMS_Asterixt($mobile,$message,'','');	
        }
        elseif($this->config->item('sms_gateway') == '5')
    	{
           $sendSMS =  $this->sms_model->sendSMS_Qikberry($mobile,$message,'','');	
        }

		echo ($sendSMS==TRUE?"success":"failed");

		

	}

	function send_email()

	{

		$mail = $this->input->post('mail');

		$model=self::EMAIL_MODEL;

		if($mail['message']!=NULL){

			$sendEmail = $this->$model->send_email($mail['to'],$mail['subject'],$mail['message']);	

			if($sendEmail)

			{

			   	$this->session->set_flashdata('chit_alert',array('message'=>'Mail send successfully','class'=>'success','title'=>'Send Mail','icon'=>'check'));

			}

			else

			{

				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Send Mail Error','icon'=>'remove'));

			}

		}

		else{

			$this->session->set_flashdata('chit_alert',array('message'=>'Please enter Message to send.','class'=>'warning','title'=>'Warning','icon'=>'warning'));

		}

		

		redirect('email/compose');

		

	}

	

	function send_group_email()

	{

		$mail =$_POST;

		$model=self::EMAIL_MODEL;

        if($mail['customer']!=null){

			 foreach($mail['customer'] as $email)

		        {

		        	$sendEmail = $this->$model->send_email($email,$mail['subject'],$mail['message']);		

				}

		}

       

		echo ($sendEmail?"success":"failed");

	}

	

	//Group sms/Mail

	function open_listingform($db_error_msg="")

	{		

		$data["db_error_msg"] = $db_error_msg;

		$data['main_content'] = self::GRP."group_sms_list" ;

		$this->load->view('layout/template', $data);

		

	}	

	

	function compose_view($db_error_msg="")

	{		

		$data["db_error_msg"] = $db_error_msg;

		$data['main_content'] = self::VIEW."email" ;

		$this->load->view('layout/template', $data);

		

	}	

	

	function compose_group_view($db_error_msg="")

	{		

		$data["db_error_msg"] = $db_error_msg;

		$data['main_content'] = self::GRP."email" ;

		$this->load->view('layout/template', $data);

		

	}

	

	

	

	//Group sms/Mail form

	public function open_group_form($type="",$id="")

	{

		$model=	self::SMS_MODEL;

		$this->load->model($model);	

		switch($type)

		{

			case 'Add':

				$data['group']					=	$this->$model->empty_record();

				$data['main_content']			=   self::GRP."group_form" ;

				$this->load->view('layout/template', $data);

			   break;

			

			

		}

	}



   public function open_group_post($type="",$id="")

	{

		$model= self::SMS_MODEL;

		switch($type)

		{

			case 'Add':

			          $grp_data=$this->input->post("group");

			         

			          $info=array(	

								'id_scheme'			=>(isset($grp_data['id_scheme'])?$grp_data['id_scheme']:NULL),

								'header'			=>(isset($grp_data['header'])?$grp_data['header']:NULL),

								'desc'				=>(isset($grp_data['desc'])?$grp_data['desc']:NULL),

								'footer'			=>(isset($grp_data['footer'])?$grp_data['footer']:NULL),

								'email'				=>(isset($grp_data['email'])?$grp_data['email']:NULL),

								'sms'				=>(isset($grp_data['sms'])?$grp_data['sms']:NULL),

								

								'date'				=> date("Y-m-d H:i:s")

						);

			              

				    $this->db->trans_begin();

				    $this->$model->insert($info);

				  

				   if( $this->db->trans_status()===TRUE)

				   {

					  $this->db->trans_commit();

					  $this->session->set_flashdata('info', array('message' => 'Scheme created successfully','class' => 'success','title'=>'Create Scheme'));

	                  

	                  redirect('sms/group_smsmail_list');

				   }			  

				   else

				   {

				   	 $this->db->trans_rollback();

				   	 $this->session->set_flashdata('info', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Create Scheme'));

				   }

				

				break;

				

		}

	}	

	

	

	public function ajax_get_schemes()

	{

		

		$model=self::SMS_MODEL;

		

		$schemes=$this->$model->get_schemes();

		echo json_encode($schemes);

	}
	public function ajax_get_schemes_list($id="")

	{

		

		$model=self::SMS_MODEL;

		

		$schemes=$this->$model->get_schemes_name($id);

		echo json_encode($schemes);

	}

	

	public function ajax_get_scheme($id)

	{

		$group=$this->scheme_business($id);

	

		echo json_encode($group);

		

	}

	

	

	

	public function scheme_business($id)

	{

	   $group=array();

	   $model=self::SMS_MODEL;

	  

		$data['group']=$this->$model->get_customers_by_scheme($id);

		

		return $data;

	}

	

	

	

	function open_notification_entry_form() {			

		$fv['db_error_msg'] ="";

		$this->load->view($this->notification_entry_form,$fv);



	}

	

	function create_pushnotification(){

		$model_name="usersms_settings_model";

		$this->load->model($model_name);

		$registerids = array();

		$registerids = $this->usersms_settings_model->getnotificationids();

		

		// API access key from Google API's Console

		define( 'API_ACCESS_KEY', 'AIzaSyDmg3stIRiM6l1W81wR9P1a8l5N33gnS10' );

		$registrationIds = $registerids;

		// prep the bundle

		$msg = array

		(

			'message' 	=> $_POST['message'],

			'title'		=> $_POST['title'],

			'subtitle'	=> 'From SLN Bullion',

			'tickerText'	=> 'Ticker text here...Ticker text here...Ticker text here',

			'vibrate'	=> 1,

			'sound'		=> 1,

			'largeIcon'	=> 'large_icon',

			'smallIcon'	=> 'small_icon'

		);

		$fields = array

		(

			'registration_ids' 	=> $registrationIds,

			'data'			=> $msg

		);

		 

		$headers = array

		(

			'Authorization: key=' . API_ACCESS_KEY,

			'Content-Type: application/json'

		);

		 

		$ch = curl_init();

		curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );

		curl_setopt( $ch,CURLOPT_POST, true );

		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

		$result = curl_exec($ch );

		curl_close( $ch );

		echo $result;

		

	}

	

		
//sendSMS login details 
 function send_login()
 {
 	$model=self::CUS_MODEL;
 	$cus=$this->input->post('id_customer');
 	$total= count($cus);
 	$sms_status =0;
 	$logins=array();
 	$total=array();

 	if($total>0)
 	{
 		$this->load->model($model); 
		foreach($cus as $cusid)
		{
			
        	$cusData=$this->$model->get_login_detail($cusid);
			//print_r($cusData); 
        	$sms_status=$this->send_login_sms($cusData['mobile']);
        		//print_r($sms_status);exit;
	        $total[]=$sms_status;
		}
	
	}                
	  if	($sms_status== 1 ){
	      $msg = 'Login details SMS has been sent to '.count($total).(count($total)>1?' customers':' customer');
	    $this->session->set_flashdata('chit_alert',array('message'=>$msg,'class'=>'success','title'=>'Send Login'));
        echo $msg;    
 }  
 
 else{
	 
	echo "Can't able to sent Login details SMS. kindly enable sms service";
 }
 }
 function send_login_sms($mobile)
	{
		$serviceID=11;
		$sms_models= self::SMS_MODEL;	
		$model = self::ADM_MODEL;		
		$id =$mobile;
		$data =$this->$sms_models->get_SMS_data($serviceID,$id);
		$mobile_number =$data['mobile'];
		$message = $data['message'];
		
		if($data['serv_sms'] == 1){ 
    		if($this->config->item('sms_gateway') == '1'){
    			$sms_data = $this->sms_model->sendSMS_MSG91($mobile_number,$message);		
    		}
    		elseif($this->config->item('sms_gateway') == '2'){
    			$sms_data = $this->sms_model->sendSMS_Nettyfish($mobile_number,$message,'trans');
    		}
    		elseif($this->config->item('sms_gateway') == '3')
            {
                $sms_data = $this->sms_model->sendSMS_SpearUC($mobile_number,$message,'','');	
            }
            elseif($this->config->item('sms_gateway') == '4')
        	{
               $sms_data =  $this->sms_model->sendSMS_Asterixt($mobile_number,$message,'','');	
            }
            elseif($this->config->item('sms_gateway') == '5')
        	{
               $sms_data =  $this->sms_model->sendSMS_Qikberry($mobile_number,$message,'','');	
            }
		}
		
		return $sms_data?1:0;
	}
	
	
   //sendEmail login details
 function send_login_email()
 {
 	$model=self::CUS_MODEL;
 	$cus=$this->input->post('id_customer');
 	$total= count($cus);
 	$mail_status =0;
 	$logins=array();
 	$total=array();
 	if($total>0)
 	{
 		$this->load->model($model); 
		foreach($cus as $cusid)
		{
        	$cusData=$this->$model->get_login_detail($cusid);
			//print_r($cusData); 
			$mail_status=$this->send_mail($cusData);
	        $total[]=$mail_status;
		}
		
	}                
	if($mail_status == 1){	
		$status=1;

	   $msg= 'Login details E-Mail has been sent to '.count($total).(count($total)>1?' customers':' customer');  
}	   
else{
	$status=0;

	$msg= "Can't able to sent E-Mail Login details . kindly enable sms service";
 }
 echo json_encode($status);

 }
		
 function send_mail($cusData)
	{	
	  	$ser_model = self::SET_MODEL;
	  	$company = $this->$ser_model->get_company();
	    $mail_model=self::EMAIL_MODEL;
		$email	=  $cusData['email'];
		if($email!= '')
		{
			$data['schData'] = $cusData;
			$data['name'] = $cusData['name'];
			$data['company'] = $company;
			$data['type'] = 1;
			$to = $email;
			$subject = "Reg- ".$company['company_name']." saving scheme Login details";
			$message = $this->load->view('include/emailAccount',$data,true);
			$sendEmail = $this->$mail_model->send_email($to,$subject,$message);
			return TRUE;
		}
		
	}
	
//Notification form
		
	function notification_form($type="",$id="")
	{
		$model=self::SMS_MODEL;
		$admin_model=self::SET_MODEL;
		switch ($type)

		{

			case 'List':
				
					$data['general']= $this->$admin_model->settingsDB('get',1);
					
			   	  	$data['main_content'] =  self::NOTI_VIEW.'list';

				  	$this->load->view('layout/template', $data);
					
				  break;

		    case 'Add':

				  $data['notification']	=  $this->$model->get_noti_empty_record();
				  
			   	  $data['main_content'] =  self::NOTI_VIEW.'form';

			   	  $data['type'] =  1;

				  $this->load->view('layout/template', $data);

				  break;

			case 'Edit':

				  $data['notification']	=	$this->$model->get_noti_entry_record($id);					
			 				//echo "<pre>";print_r($data);echo "</pre>";exit;

					
				  $data['type'] =  2;

				  if($id == 1)

				  {				  	

				  	 $data['service_list'] = array(

									array(

									'value'     	=> 'time',

								   	'text'			=> 'time'

									),array(

									'value'     	=> 'rate',

								   	'text'			=> 'rate'

									),array(

									'value'     	=> 'silver',

								   	'text'			=> 'silver'

									),
									array(

									'value'     	=> 'mjdmaGold',

								   	'text'			=> 'mjdmaGold'

									),
									array(

									'value'     	=> 'mjdmasilver',

								   	'text'			=> 'mjdmasilver'

									),
									
									array(

									'value'     	=> 'goldRate24',

								   	'text'			=> 'goldRate24'

									),
									array(

									'value'     	=> 'goldRate18',

								   	'text'			=> 'goldRate18'

									),
									
									array(

									'value'     	=> 'platinum',

								   	'text'			=> 'platinum'

									)
							    );

							    

				  }

				   elseif($id == 8 || $id == 7 || $id == 6 || $id == 5 )

				  {

				   $data['service_list'] = array(

				   					array(

									'value'     	=> 'firstname',

								   	'text'			=> 'firstname'

									),
									array(

									'value'     	=> 'cus_name',

								   	'text'			=> 'cus_name'

									),
									array(

									'value'     	=> 'cmp_name',

								   	'text'			=> 'cmp_name'

									),
									array(

									'value'     	=> 'cmp_code',

								   	'text'			=> 'cmp_code'

									)

							    );	

				  } 
				 elseif($id == 9 )

				  {

				   $data['service_list'] = array(

				   					array(

									'value'     	=> 'tgoldrate_22ct',

								   	'text'			=> 'tgoldrate_22ct'

									)
							    );	

				  } elseif($id == 10)

				  {

				   $data['service_list'] = array(

				   					array(

									'value'     	=> 'tgoldrate_22ct',

								   	'text'			=> 'tgoldrate_22ct'

									)
							    );	

				  }
				  
				  elseif($id == 11)

				  {

				   $data['service_list'] = array(

				   					array(

									'value'     	=> 'tgoldrate_22ct',

								   	'text'			=> 'tgoldrate_22ct'

									),array(

									'value'     	=> 'ygoldrate_22ct',

								   	'text'			=> 'ygoldrate_22ct'

									)
							    );	

				  }
				  else

				  {

				   $data['service_list'] = array(

				   					array(

									'value'     	=> 'payable',

								   	'text'			=> 'Payable'

									),array(

									'value'     	=> 'sch_name',

								   	'text'			=> 'Scheme name'

									),array(

									'value'     	=> 'due_date',

								   	'text'			=> 'Due Date'

									),array(

									'value'     	=> 'ac_no',

								   	'text'			=> 'Scheme a/c no'

									)

							    );	

				  } 

				 

			   	  $data['main_content'] =  self::NOTI_VIEW.'form';

				  $this->load->view('layout/template', $data);

				  break;
			case 'Delete':
				  $model=self::SMS_MODEL;
			  	  $this->$model->delete_notification($id);
					 redirect('notification/list');  
					 
				  break;
			default :

				 $set_model=self::SET_MODEL;	

				  $data['notification']=$this->$model->get_notification_services();
					
				  echo json_encode($data);

			break;

				

		}
		
		
	}

	

	//SMS Settings form

	function sms_service_form($type="",$id="")

	{

		$model=self::SMS_MODEL;

		$cus_model=self::CUS_MODEL;

		switch ($type)

		{

			case 'List':

			   	  $data['main_content'] =  self::SMS_VIEW.'list';

				  $this->load->view('layout/template', $data);

				  break;

		    case 'Add':

				  $data['sms']			=  $this->$model->get_empty_record();

			   	  $data['main_content'] =  self::SMS_VIEW.'form';

			   	  $data['type'] =  1;

				  $this->load->view('layout/template', $data);

				  break;

			case 'Edit':

				  $data['sms']	=	$this->$model->get_entry_record($id);

				  $data['type'] =  2;

				  if($id == 1 || $id == 11)

				  {

				  	

				  	 $data['service_list'] = array(

									array(

									'value'     	=> 'cus_name',

								   	'text'			=> 'Customer Name'

									),

									array(

									'value'     	=> 'fname',

								   	'text'			=> 'First name'

									),

									array(

									'value'     	=> 'lname',

								   	'text'			=> 'Last Name'

									),

									array(

									'value'     	=> 'mobile',

								   	'text'			=> 'Mobile'

									),

									array(

									'value'     	=> 'email',

								   	'text'			=> 'Email'

									),

									array(

									'value'     	=> 'passwd',

								   	'text'			=> 'Password'

									),

									array(

									'value'     	=> 'userId',

								   	'text'			=> 'User Id'

									),

									array(

									'value'     	=> 'cmp_name',

								   	'text'			=> 'Company name'

									),array(

									'value'     	=> 'cmp_ph',

								   	'text'			=> 'Company Phone'

									),array(

									'value'     	=> 'website',

								   	'text'			=> 'Company Website'

									)

							    );

							    

				  }

				  elseif($id == 2 || $id == 12 || $id==13 )

				  {

				  	

				   $data['service_list'] = array(

				   					array(

									'value'     	=> 'cus_name',

								   	'text'			=> 'Customer name'

									),  array(

									'value'     	=> 'fname',

								   	'text'			=> 'First name'

									),  array(

									'value'     	=> 'lname',

								   	'text'			=> 'Last name'

									),	array(

									'value'     	=> 'ac_name',

								   	'text'			=> 'A/c name'

									),	array(

									'value'     	=> 'sch_name',

								   	'text'			=> 'Scheme Name'

									),array(

									'value'     	=> 'sch_code',

								   	'text'			=> 'Scheme Code'

									),array(

									'value'     	=> 'sch_type',

								   	'text'			=> 'Scheme Type'

									),	array(

									'value'     	=> 'acc_no',

								   	'text'			=> 'Scheme a/c number'

									),	array(

									'value'     	=> 'payable',

								   	'text'			=> 'Payable'

									),	array(

									'value'     	=> 'metal_weight',

								   	'text'			=> 'Metal Weight'

									),	array(

									'value'     	=> 'total_installments',

								   	'text'			=> 'Total_installments'

									),	array(

									'value'     	=> 'start_date',

								   	'text'			=> 'Joining date'

									),	array(

									'value'     	=> 'cmp_name',

								   	'text'			=> 'Company name'

									),array(

									'value'     	=> 'cmp_ph',

								   	'text'			=> 'Company Phone'

									)

							    );

				  }

				  elseif($id==14 || $id==15 )

				  {

				   $data['service_list'] = array( 

				   					array(

									'value'     	=> 'cus_name',

								   	'text'			=> 'Customer name'

									),  array(

									'value'     	=> 'fname',

								   	'text'			=> 'First name'

									),  array(

									'value'     	=> 'lname',

								   	'text'			=> 'Last name'

									),	array(

									'value'     	=> 'ac_name',

								   	'text'			=> 'A/c name'

									),	array(

									'value'     	=> 'sch_name',

								   	'text'			=> 'Scheme Name'

									),array(

									'value'     	=> 'sch_code',

								   	'text'			=> 'Scheme Code'

									),	array(

									'value'     	=> 'acc_no',

								   	'text'			=> 'Scheme a/c number'

									),	array(

									'value'     	=> 'metal_weight',

								   	'text'			=> 'Metal Weight'

									),	array(

									'value'     	=> 'cmp_name',

								   	'text'			=> 'Company name'

									),array(

									'value'     	=> 'cmp_ph',

								   	'text'			=> 'Company Phone'

									),array(

									'value'     	=> 'ref_code',

								   	'text'			=> 'Referral code'

									)

							    );

							    }

				  elseif( $id == 3 || $id == 7)

				  {

				   $data['service_list'] = array(

				   					array(

									'value'     	=> 'cus_name',

								   	'text'			=> 'Customer name'

									),  array(

									'value'     	=> 'fname',

								   	'text'			=> 'First name'

									),  array(

									'value'     	=> 'lname',

								   	'text'			=> 'Last name'

									),	array(

									'value'     	=> 'ac_name',

								   	'text'			=> 'A/c name'

									),	array(

									'value'     	=> 'sch_name',

								   	'text'			=> 'Scheme name'

									),array(

									'value'     	=> 'sch_code',

								   	'text'			=> 'Scheme Code'

									),	array(

									'value'     	=> 'acc_no',

								   	'text'			=> 'Scheme a/c number'

									),	array(

									'value'     	=> 'pay_amt',

								   	'text'			=> 'Payment Amount'

									),	array(

									'value'     	=> 'metal_weight',

								   	'text'			=> 'Metal Weight'

									),	array(
									
									'value'     	=> 'dues',
								   	
								   	'text'			=> 'No of Dues'
									
									),array(

									'value'     	=> 'txn_id',

								   	'text'			=> 'Transaction Id'

									),	array(

									'value'     	=> 'date_pay',

								   	'text'			=> 'Payment Date'

									),	array(

									'value'     	=> 'pay_mode',

								   	'text'			=> 'Payment Mode'

									),	array(

									'value'     	=> 'status',

								   	'text'			=> 'Status '

									),	array(

									'value'     	=> 'cmp_name',

								   	'text'			=> 'Company name'

									),array(

									'value'     	=> 'cmp_ph',

								   	'text'			=> 'Company Phone'

									),array(

										'value'     => 'paid_installments',
	
										'text'		=> 'Paid installments'
	
									),array(

										'value'     => 'metal_rate',
	
										'text'		=> 'metal_rate'
	
									),array(

										'value'     => 'total_amount',
	
										'text'		=> 'Total Paid Amount'
	
									),
									array(

										'value'     => 'tot_weight',
	
										'text'		=> 'Total Paid Weight'
	
									),
									array(

										'value'     => 'cmp_name_sms',
	
										'text'		=> 'comp_name_in_sms'
	
									)

							    );	

				  } 

				  elseif( $id == 4)

				  {

				   $data['service_list'] = array(

				   					array(

									'value'     	=> 'cus_name',

								   	'text'			=> 'Customer name'

									),  array(

									'value'     	=> 'fname',

								   	'text'			=> 'First name'

									),  array(

									'value'     	=> 'lname',

								   	'text'			=> 'Last name'

									),	array(

									'value'     	=> 'ac_name',

								   	'text'			=> 'A/c name'

									),	array(

									'value'     	=> 'sch_name',

								   	'text'			=> 'Scheme name'

									),array(

									'value'     	=> 'sch_code',

								   	'text'			=> 'Scheme Code'

									),	array(

									'value'     	=> 'acc_no',

								   	'text'			=> 'Scheme a/c number'

									),	array(

									'value'     	=> 'closing_date',

								   	'text'			=> 'Closed on'

									),	array(

									'value'     	=> 'paid_installments',

								   	'text'			=> 'Paid installments'

									),	array(

									'value'     	=> 'curr_symb',

								   	'text'			=> 'Currency Symbol'

									),	array(

									'value'     	=> 'closing_blc',

								   	'text'			=> 'Closing Balance'

									),	array(

									'value'     	=> 'closed_by',

								   	'text'			=> 'Closed By '

									),array(

									'value'     	=> 'cmp_ph',

								   	'text'			=> 'Company Phone'

									),array(

									'value'     	=> 'cmp_name',

								   	'text'			=> 'Company name'

									)

							    );	

				  }

				   elseif( $id == 5 || $id == 6)

				  {

				   $data['service_list'] = array(

				   					array(

									'value'     	=> 'cus_name',

								   	'text'			=> 'Customer name'

									),  array(

									'value'     	=> 'fname',

								   	'text'			=> 'First name'

									),  array(

									'value'     	=> 'lname',

								   	'text'			=> 'Last name'

									),	array(

									'value'     	=> 'ac_name',

								   	'text'			=> 'A/c name'

									),	array(

									'value'     	=> 'sch_name',

								   	'text'			=> 'Scheme name'

									),array(

									'value'     	=> 'sch_code',

								   	'text'			=> 'Scheme Code'

									),	array(

									'value'     	=> 'acc_no',

								   	'text'			=> 'Scheme a/c number'

									),	array(

									'value'     	=> 'pay_amt',

								   	'text'			=> 'Payment Amount'

									),	array(

									'value'     	=> 'txn_id',

								   	'text'			=> 'Transaction Id'

									),	array(

									'value'     	=> 'date_pay',

								   	'text'			=> 'Payment Date'

									),	array(

									'value'     	=> 'pay_mode',

								   	'text'			=> 'Payment Mode'

									),	array(

									'value'     	=> 'charges',

								   	'text'			=> 'Charges'

									),	array(

									'value'     	=> 'chq_date',

								   	'text'			=> 'Cheque Date'

									),	array(

									'value'     	=> 'chq_no',

								   	'text'			=> 'Cheque No.'

									),	array(

									'value'     	=> 'status',

								   	'text'			=> 'Status'

									),	array(

									'value'     	=> 'cmp_name',

								   	'text'			=> 'Company name'

									),array(

									'value'     	=> 'cmp_ph',

								   	'text'			=> 'Company Phone'

									)

							    );	

				  }  

				  elseif( $id == 8 || $id == 9)

				  {

				   $data['service_list'] = array(

				   					array(

									'value'     	=> 'cus_name',

								   	'text'			=> 'Customer name'

									),  array(

									'value'     	=> 'fname',

								   	'text'			=> 'First name'

									),  array(

									'value'     	=> 'lname',

								   	'text'			=> 'Last name'

									),array(

									'value'     	=> 'wlt_acc_no',

								   	'text'			=> 'wallet a/c number'

									),array(

									'value'     	=> 'issued_date',

								   	'text'			=> 'Issued Date'

									),array(

									'value'     	=> 'issues',

								   	'text'			=> 'Issues'

									),array(

									'value'     	=> 'redeem',

								   	'text'			=> 'Redeem'

									),array(

									'value'     	=> 'blc',

								   	'text'			=> 'Available Balance'

									),array(

									'value'     	=> 'remark',

								   	'text'			=> 'remark'

									),array(

									'value'     	=> 'cmp_name',

								   	'text'			=> 'Company name'

									),array(

									'value'     	=> 'cmp_ph',

								   	'text'			=> 'Company Phone'

									)

							    );	

				  }else if($id == 16){
					  
					  $data['service_list'] = array(
					  
							array(

									'value'     	=> 'cmp_name',

								   	'text'			=> 'Company name'

									), array(

									'value'     	=> 'fname',

								   	'text'			=> 'First name'

									),  array(

									'value'     	=> 'lname',

								   	'text'			=> 'Last name'
					  
					                 ),array(

									'value'     	=> 'amount',

								   	'text'			=> 'Benefit amount'

									)
								);
					  
					  
				    } 
				    else if($id==21)
				    {
				        
				        $data['service_list'] = array(

									array(

									'value'     	=> 'time',

								   	'text'			=> 'time'

									),array(

									'value'     	=> 'rate',

								   	'text'			=> 'rate'

									),array(

									'value'     	=> 'silver',

								   	'text'			=> 'silver'

									),
									array(

									'value'     	=> 'mjdmaGold',

								   	'text'			=> 'mjdmaGold'

									),
									array(

									'value'     	=> 'mjdmasilver',

								   	'text'			=> 'mjdmasilver'

									),
									
									array(

									'value'     	=> 'goldRate24',

								   	'text'			=> 'goldRate24'

									),
									array(

									'value'     	=> 'goldRate18',

								   	'text'			=> 'goldRate18'

									),
									
									array(

									'value'     	=> 'platinum',

								   	'text'			=> 'platinum'

									)
							    );

				    }
				    
				    		else if($id==23)
				    {
				        
				        $data['service_list'] = array(

									array(

									'value'     	=> 'otp',

								   	'text'			=> 'OTP'
									),
									array(

									'value'     	=> 'loginOTP_exp',

								   	'text'			=> 'OTP Expiry Time'
									),
									array(

										'value'     	=> 'email',
	
										   'text'			=> 'Company Email'
	
										),array(
	
										'value'     	=> 'cmp_name',
	
										   'text'			=> 'Company name'
	
										),array(
	
										'value'     	=> 'cmp_ph',
	
										   'text'			=> 'Company Phone'
	
										),array(
	
										'value'     	=> 'website',
	
										   'text'			=> 'Company Website'
	
										),array(
	
										'value'     	=> 'mobile',
	
										   'text'			=> 'Company mobile'
	
										),
									
								);
					}

				   else

				  {

				   $data['service_list'] = array(

				   					array(

									'value'     	=> 'email',

								   	'text'			=> 'Company Email'

									),array(

									'value'     	=> 'cmp_name',

								   	'text'			=> 'Company name'

									),array(

									'value'     	=> 'cmp_ph',

								   	'text'			=> 'Company Phone'

									),array(

									'value'     	=> 'website',

								   	'text'			=> 'Company Website'

									),array(

									'value'     	=> 'mobile',

								   	'text'			=> 'Company mobile'

									)

							    );	

				  } 

				 

			   	  $data['main_content'] =  self::SMS_VIEW.'form';

				  $this->load->view('layout/template', $data);

				  break;
			case 'Delete':
				  $model=self::SMS_MODEL;
				  $log_model     = self::LOG_MODEL;
			  	  $this->$model->delete_service($id);
			  	   $log_data = array(
                            'id_log'     => $this->id_log,
                            'event_date' => date("Y-m-d H:i:s"),
                            'module'     => 'Service',
                            'operation'  => 'Delete',
                            'record'     => $id,  
                            'remark'     => 'Service Deleted successfully'
                            );
                            // print_r($log_data );exit;
                            $this->$log_model->log_detail('insert','',$log_data);
					 redirect('sms/service/list');  
					 
				  break;
			default :

				 $set_model=self::SET_MODEL;

			  	  $data['access'] = $this->$set_model->get_access('sms/service/list');

				  $data['sms']=$this->$model->get_sms_services();

				  echo json_encode($data);

			break;

				

		}	

	}

	public function sms_service_post($type="",$id="")

	{

		$model=self::SMS_MODEL;
		
		$log_model = self::LOG_MODEL;

		switch($type)

		{

			case 'Save':

			       $sms=$this->input->post('sms');

			       $sms_data=array(

			       		

			       			'id_services'		=>  (isset($sms['id_services'])?$sms['id_services']: NULL), 
			       			
			       			'dlt_te_id'		=>	(isset($sms['dlt_te_id'])?$sms['dlt_te_id']: NULL),
			       			'template_name'		=>	(isset($sms['template_name'])?$sms['template_name']: NULL),

			       			'serv_name'		=>  (isset($sms['serv_name'])?$sms['serv_name']: NULL), 

			       			'sms_msg' 			=>  (isset($sms['sms_msg'])?$sms['sms_msg']: NULL), 

							'sms_footer'		=>	(isset($sms['sms_footer'])?$sms['sms_footer']: NULL),
							
							'send_sms_on'		=>	(isset($sms['send_sms_on'])?$sms['send_sms_on']: NULL),
							
							'send_daily_from'		=>	(isset($sms['send_daily_from'])?$sms['send_daily_from']: NULL)

					   );

			             $this->db->trans_begin();

			             $sms_id=  $this->$model->insert_service($sms_data);//print_r($sms_data);exit;

			             if($this->db->trans_status()===TRUE)

			             {
			                 
			                  $log_data = array(                                                                
                                'id_log'     => $this->id_log,
                                'event_date' => date("Y-m-d H:i:s"),
                                'module'     => 'Service',
                                'operation'  => 'Add',
                                'record'     => $sms['id_services'],  
                                'remark'     => 'Service added successfully'
                                );
                                // print_r($log_data );exit;
                                $this->$log_model->log_detail('insert','',$log_data);

						 	$this->db->trans_commit();

						 	$this->session->set_flashdata('chit_alert',array('message'=>'Services record added successfully','class'=>'success','title'=>'Sms Service'));

						 	redirect('sms/service/list');

						 }

						 else

						 {

						 	 $this->db->trans_rollback();						 	

						 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Sms Service'));

						 	redirect('sms/service/list');

						 }

			       

				break;

				

			case 'Update':

			       $sms=$this->input->post('sms');

			       $sms_data=array(

			       		

			       			'id_services'		=>  (isset($sms['id_services'])?$sms['id_services']: NULL), 
			       			
			       			'dlt_te_id'		    =>	(isset($sms['dlt_te_id'])?$sms['dlt_te_id']: NULL),
			       			'template_name'		=>	(isset($sms['template_name'])?$sms['template_name']: NULL),

			       			'serv_name'			=>  (isset($sms['serv_name'])?$sms['serv_name']: NULL), 

			       			'sms_msg' 			=>  (isset($sms['sms_msg'])?$sms['sms_msg']: NULL), 

							'sms_footer'		=>	(isset($sms['sms_footer'])?$sms['sms_footer']: NULL),
							
							'send_sms_on'		=>	(isset($sms['send_sms_on'])?$sms['send_sms_on']: NULL),
							
							'send_daily_from'	=>	(isset($sms['send_daily_from'])?$sms['send_daily_from']: NULL)

						   );

			             $this->db->trans_begin();

			             $sms_id=  $this->$model->update_service($sms_data);
                        //print_r($this->db->last_query());exit;
			             if($this->db->trans_status()===TRUE)

			             {
			                 //scheme log details
                                $log_data = array(                                                                
                                'id_log'     => $this->id_log,
                                'event_date' => date("Y-m-d H:i:s"),
                                'module'     => 'Service',
                                'operation'  => 'Edit',
                                'record'     => $id,  
                                'remark'     => 'Service edited successfully'
                                );
        
                                // print_r($log_data );exit;
                                $this->$log_model->log_detail('insert','',$log_data);

						 	$this->db->trans_commit();

						 	$this->session->set_flashdata('chit_alert',array('message'=>'Services record edited successfully','class'=>'success','title'=>'Sms Service'));

						 	redirect('sms/service/list');

						 }

						 else

						 {

						 	 $this->db->trans_rollback();						 	

						 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Sms Service'));

						 	redirect('sms/service/list');

						 }

			       

				break;

				}

	}

public function notification_service_post($type="",$id="")

	{

		$model=self::SMS_MODEL;

		switch($type)

		{

			case 'Save':

			       $notification=$this->input->post('notification');

			       $notification_data=array(

			       		

			       			'id_notification'		=>  (isset($notification['id_notification'])?$notification['id_notification']: NULL), 

			       			'noti_name'		=>  (isset($notification['noti_name'])?$notification['noti_name']: NULL), 

			       			'noti_msg' 			=>  (isset($notification['noti_msg'])?$notification['noti_msg']: NULL), 

							'noti_footer'		=>	(isset($notification['noti_footer'])?$notification['noti_footer']: NULL),

							'send_notif_on'		=>	(isset($notification['send_notif_on'])?$notification['send_notif_on']: NULL),
							
							'send_daily_from'		=>	(isset($notification['send_daily_from'])?$notification['send_daily_from']: NULL)

					   );

			             $this->db->trans_begin();

			            $not= $this->$model->insert_notification($notification_data);
			            
			             

			             if($this->db->trans_status()===TRUE)

			             {

						 	$this->db->trans_commit();

						 	$this->session->set_flashdata('chit_alert',array('message'=>'Notification record added successfully','class'=>'success','title'=>'Notification Service'));

						 	redirect('notification/list');

						 }

						 else

						{

						 	 $this->db->trans_rollback();						 	

						 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Notification Service'));

						 	redirect('notification/list');

						 }

			       

				break;

				

			case 'Update':

			       $notification=$this->input->post('notification');

			       $notification_data=array(

			       		

			       			'id_notification'		=>  (isset($notification['id_notification'])?$notification['id_notification']: NULL), 

			       			'noti_name'		=>  (isset($notification['noti_name'])?$notification['noti_name']: NULL), 

			       			'noti_msg' 			=>  (isset($notification['noti_msg'])?$notification['noti_msg']: NULL), 

							'noti_footer'		=>	(isset($notification['noti_footer'])?$notification['noti_footer']: NULL),
							
							'send_notif_on'		=>	(isset($notification['send_notif_on'])?$notification['send_notif_on']: NULL),
							
							'send_daily_from'		=>	(isset($notification['send_daily_from'])?$notification['send_daily_from']: NULL)

					   );
					  

			             $this->db->trans_begin();

			             $this->$model->update_notification($notification_data);
              //print_r($this->db->last_query());exit;
			           
			             if($this->db->trans_status()===TRUE)

			             {

						 	$this->db->trans_commit();

						 	$this->session->set_flashdata('chit_alert',array('message'=>'Notification record edited successfully','class'=>'success','title'=>'Notification Service'));

							redirect('notification/list');

						 }

						 else

						 {

						 	 $this->db->trans_rollback();						 	

						 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Notification Service'));

						 	redirect('notification/list');

						 }

			       

				break;

				}

	}

	//to update notification enable/disable

	function notification_status($status,$id)

	{

		$data = array('noti_sub' => $status);

		$model=self::SMS_MODEL;

		$result = $this->$model->update_notification_status($data,$id);

		if($result)

		{

			$this->session->set_flashdata('chit_alert',array('message'=>'Services status updated as '.($status ? 'active' : 'inactive').' successfully.','class'=>'success','title'=>'Services Status'));			

		}	

		else

		{

			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Sservices Status'));

		}	

		redirect('notification/list');

	}	

	//to update services enable/disable

	function services_status($type,$status,$id)

	{

		

		if($type == 'email')

		{

			$data = array('serv_email' => $status);

		}

	    else if($type =='sms'){

			$data = array('serv_sms' => $status);

		}
		else{

			$data = array('serv_whatsapp' => $status);

		}
		

		$model=self::SMS_MODEL;

		$result = $this->$model->update_sms_status($data,$id);

		if($result)

		{

			$this->session->set_flashdata('chit_alert',array('message'=>'Services status updated as '.($status ? 'active' : 'inactive').' successfully.','class'=>'success','title'=>'Services Status'));			

		}	

		else

		{

			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Sservices Status'));

		}	

		redirect('sms/service/list');

	}

	
	//to update Notification enable/disable

	function notification_on_off($status)

	{

		if($status == '1')

		{

			$data = array('allow_notification' => '1');

		}

		else{

			$data = array('allow_notification' => '0');		
	
		}
		
		
		$model=self::SMS_MODEL;

		 $result = $this->$model->notification_on_off($data); 
		if($result)

		{
 
			$this->session->set_flashdata('chit_alert',array('message'=>'Notification service '.($status==1 ? 'enabled' : 'disabled').' successfully.','class'=>'success','title'=>'Notification Status'));	 	

		}	

		else

		{
 
			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Notification Status')); 

		}	
echo 1;
		//redirect('notification/list');

	}
	
	//notification form
	
	function send_notificationform()
	{
		
		
		$data['main_content'] = self::NOTI_VIEW."send_notification" ;

		$this->load->view('layout/template', $data);		
		
	}
	
	
	function due_notification()
	{
		$model = self::SMS_MODEL;
		 
		$sch_name= $this->$model->get_scheme_name();	
		
		$data = $this->$model->get_duecustomer('MS');	
		$group=$this->send_duenotificationsms($data,$sch_name);
		
		$data = $this->$model->get_duecustomer('MT');		 
		$group=$this->send_duenotificationsms($data,$sch_name);
		
		$data= $this->$model->get_duecustomer('Y');
		$group=$this->send_duenotificationsms($data,$sch_name);
		
		$data = $this->$model->get_duecustomer('T');  
		$group=$this->send_duenotificationsms($data,$sch_name);
	
     	$data	= $this->$model->get_duecustomer('PT');		 
		$group=$this->send_duenotificationsms($data,$sch_name);
		
      	$data = $this->$model->get_duecustomer('PS'); 
		$group=$this->send_duenotificationsms($data,$sch_name);
	
	}
	
	function send_duenotificationsms($schem_data,$scheme)
	{
		$model = self::SMS_MODEL;
		
		foreach($scheme as $sch_name)
		{	
			foreach($schem_data as $notification)
			{		
				
			if($sch_name == $notification['scheme_name'])
				{
					$sch_data= array(
						'id_customer'	=>  (isset($notification ['id_customer'])?$notification['id_customer']: NULL), 	
						'sch_name' 	=>  (isset($notification['scheme_name'])?$notification['scheme_name']:NULL), 
						'payable' 	=>  (isset($notification['payable'])?$notification['payable']:NULL)
						
									);	
					$data['header']	='due_alert';
					$data['footer']	='---';					
					$data['message']=$this->$model->getnotificationtext($sch_data);
					$data['token']= $this->$model->getnotification_id($sch_data['id_customer']);					
					$status = $this->send_alert_notification($data);
				}
								
			}
			
			
		}
		
	
	}
	 
function set_image($filename)
 {
 	$data=array();
    $model=self::SET_MODEL;
	 if($_FILES['notification_img']['name'])
   	 { 
   	 	$path='assets/img/notification/';
	    if (!is_dir($path)) {
		  mkdir($path, 0777, TRUE);
		}
		/*else{
			$file = $path.$id.".jpg" ;
			chmod($path,0777);
	        unlink($file);
		}*/

   	 	$img=$_FILES['notification_img']['tmp_name'];
   	 	$imgpath='assets/img/notification/'.$filename;
	 	$upload=$this->upload_img('noti_img',$imgpath,$img);
	 } 
 }

  function upload_img( $outputImage,$dst, $img)
	{	
	if (($img_info = getimagesize($img)) === FALSE)
	{
		// die("Image not found or not an image");
		return false;
	}

	$width = $img_info[0];
	$height = $img_info[1];
	switch ($img_info[2]) {
	  case IMAGETYPE_GIF  : $src = imagecreatefromgif($img);
	  						$tmp = imagecreatetruecolor($width, $height);
	  						$kek=imagecolorallocate($tmp, 255, 255, 255);
				      		imagefill($tmp,0,0,$kek);
	  						break;

	  case IMAGETYPE_JPEG : $src = imagecreatefromjpeg($img); 
	  						$tmp = imagecreatetruecolor($width, $height);
	 						break;

	  case IMAGETYPE_PNG  : $src = imagecreatefrompng($img);
						    $tmp = imagecreatetruecolor($width, $height);
	  						$kek=imagecolorallocate($tmp, 255, 255, 255);
				     		imagefill($tmp,0,0,$kek);
				     		break;

	  default : //die("Unknown filetype");	

	  return false;

	  }		
	  imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
	  imagejpeg($tmp, $dst, 60);
	}
	
	
	// to send general ,offers ,new arrivals notification 
	function notidata_gen_single()
	{
		
		$status = false;
		$model = self::SMS_MODEL;
		$data=$this->input->post('notidata');
		
		$send_notif =$this->$model->check_noti_settings();
		
		if(isset($_FILES['notification_img']['name']))	
	        {
	           $filename = uniqid('IMG'.time()).".jpg";	
			   $this->set_image($filename);
			   $imgpath = $this->config->item('base_url').'/assets/img/notification/'.$filename;
			   $data['noti_img'] = $imgpath;
			}
		if($send_notif == 1 ){
			$data['token']=$this->admin_usersms_model->getnotificationids();
			//echo "<pre>";print_r($data['token']);echo "</pre>";exit;
			$i =1;
			foreach ($data['token']  as $row){
							if(sizeof($row['token'])>0){
								$arraycontent=array('token'=>$row['token'],
													'notification_service'=>$data['notification_service'],
													'header'=>$data['header'],
													'message'=>$data['message'],
													'mobile'=>$row['mobile'],
													'footer'=>$data['footer'],						
													'noti_img'=>$data['noti_img']						
								);
							//	echo "<pre>";print_r($arraycontent);echo "</pre>";exit;
								$res = $this->send_singlealert_notification($arraycontent);
								$result['noti'][$i]=$res;
								
								$i++;							
							}
							else{				
								$this->session->set_flashdata('chit_alert',array('message'=>'No customers to send notification','class'=>'danger','title'=>'Notification'));
							}
						}
					//		print_r($result);exit;
			$this->session->set_flashdata('chit_alert',array('message'=>'Notification sent successfuly','class'=>'success','title'=>'Notification'));			
				
			
		}
		else{
			
			$this->session->set_flashdata('chit_alert',array('message'=>'Enable notification to send notification','class'=>'danger','title'=>'Notification'));
		}
		redirect('send/sendnotification');
		
	}
	
	function notidata_gen()
	{		
		$status = false;
		$model = self::SMS_MODEL;
		$data=$this->input->post('notidata'); 
		$chitsettings = $this->admin_settings_model->settingsDB("get",1,"");
		// print_r($data['id_branch']);exit;
		$data['noti_img'] = "";
		
		if($data['notification_service'] > 0){
			$send_notif =$this->admin_settings_model->canSendNoti($data['notification_service']);	  // for notifi HH//
		}else{
			$send_notif =$this->$model->check_noti_settings();
		}
		
		
		if(isset($_FILES['notification_img']['name']) && $_FILES['notification_img']['name'] != '')	
        {
           $filename = uniqid('IMG'.time()).".jpg";	
		   $this->set_image($filename);
		   $imgpath = $this->config->item('base_url').'assets/img/notification/'.$filename;
		   $data['noti_img'] = $imgpath;
		}
		/*if($send_notif == 1 ){
			$data['token']=$this->admin_usersms_model->getnotificationids();
			//echo "<pre>";print_r($data['token']);echo "</pre>";exit;
			$i =1;
			foreach ($data['token']  as $row){
							if(sizeof($row['token'])>0){
								$arraycontent=array('token'=>$row['token'],
													'notification_service'=>$data['notification_service'],
													'header'=>$data['header'],
													'message'=>$data['message'],
													'mobile'=>$row['mobile'],
													'footer'=>$data['footer'],						
													'noti_img'=>$data['noti_img']						
								);
							//	echo "<pre>";print_r($arraycontent);echo "</pre>";exit;
								$res = $this->send_singlealert_notification($arraycontent);
								$result['noti'][$i]=$res;
								
								$i++;							
							}
							else{				
								$this->session->set_flashdata('chit_alert',array('message'=>'No customers to send notification','class'=>'danger','title'=>'Notification'));
							}
						}
					//		print_r($result);exit;
			$this->session->set_flashdata('chit_alert',array('message'=>'Notification sent successfuly','class'=>'success','title'=>'Notification'));			
				
			
		}*/
		if($send_notif == 1 ){ 				 
			if(sizeof($data)>0){
				$notification_service = $data['notification_service'];
				if($notification_service== '2'){
				       //echo $this->db->last_query();
					$targetUrl='#/app/offers';
				}
				else if($notification_service== '3'){
				    $targetUrl='#/app/newarrivals';
				}
				else if($notification_service== '4' || $notification_service== '5' || $notification_service== '6'){
				    $targetUrl='#/app/paydues';
				}
				else{
					$targetUrl='#/app/notification';
				}
			/*	$arraycontent=array('notification_service'=>$data['notification_service'],
									'header'=>$data['header'],
									'message'=>$data['message'],
									'mobile'=>'',
									'footer'=>$data['footer'],						
									'noti_img'=>$data['noti_img'],
									'id_branch'=>$data['id_branch'],	
									'targetUrl'=>$targetUrl	
				); HH*/
				// "All Branch" posts an empty / 0 id_branch. The branch-wise query
				// would then filter on c.id_branch=0 and match nobody, so fall
				// through to the broadcast dispatcher, which is exactly what an
				// all-branches send means.
				$data['id_branch'] = (isset($data['id_branch']) ? trim((string) $data['id_branch']) : '');
				$send_branchwise = ($chitsettings['is_branchwise_cus_reg'] == 1
									&& $data['id_branch'] !== ''
									&& strtolower($data['id_branch']) !== 'all'
									&& (int) $data['id_branch'] > 0);

				if($send_branchwise)    // based on the branch settings to showed branch filter && sent notifi(offers/new arrivals/general) in admin//HH
        		    {


        		         $cusData = $this->$model->get_sendnotifi_cusBranch($data['id_branch'],"");

        		         // One send per device -- accumulate the recipient counts
        		         // instead of keeping only the last customer's response.
        		         $sent_count = 0;
        		         	foreach($cusData as $branch){
        		         	     $arraycontent = array( 'notification_service'=>$data['notification_service'],
									'header'=>$data['header'],
									'message'=>$data['message'],
									'mobile'=>'',
									'footer'=>$data['footer'],
									'noti_img'=>$data['noti_img'],
									'id_branch'=>$data['id_branch'],
									'targetUrl'=>$targetUrl,
        		         	         'token'     => $branch['token']);
        		         	   // print_r($arraycontent);exit;
                $res = $this->send_singlealert_send_notification($arraycontent);
                $one = json_decode($res);
                if (isset($one->recipients)) { $sent_count += (int) $one->recipients; }
        		    }
        		         if (empty($cusData)) {
        		             log_message('error', 'send_notification: branch '.$data['id_branch'].' has no notification-enabled device for the active provider.');
        		         }
        		         $res = json_encode(array('id' => '', 'recipients' => $sent_count));
        		    }
        		    else
        		    {
        		        $arraycontent=array('notification_service'=>$data['notification_service'],
									'header'=>$data['header'],
									'message'=>$data['message'],
									'mobile'=>'',
									'footer'=>$data['footer'],						
									'noti_img'=>$data['noti_img'],
									'id_branch'=>$data['id_branch'],	
									'targetUrl'=>$targetUrl	
									
				);
        		        $res = $this->sendPushNotificationToAll($arraycontent);
        		    }
                $result = json_decode($res);
                $recipients = (isset($result->recipients) ? (int) $result->recipients : 0);
                if (isset($arraycontent)) {
                    $this->$model->insert_sent_notification($arraycontent);
                }
                // Do not report success when nothing was delivered -- that is the
                // symptom that hides an unusable-token problem.
                if ($recipients > 0) {
                    $this->session->set_flashdata('chit_alert',array('message'=>'Notification sent successfuly. Receipts : '.$recipients,'class'=>'success','title'=>'Notification'));
                } else {
                    $reason = '';
                    if (isset($result->error)) {
                        $reason = ' ('.$result->error.')';
                    } elseif (isset($result->fcm_response->error)) {
                        $reason = ' ('.$result->fcm_response->error.')';
                    }
                    $this->session->set_flashdata('chit_alert',array('message'=>'Notification was not delivered to any device'.$reason.'. Check that the customers have a valid push token for the active provider.','class'=>'danger','title'=>'Notification'));
                }
                
			
			}
			else{				
				$this->session->set_flashdata('chit_alert',array('message'=>'No customers to send notification','class'=>'danger','title'=>'Notification'));
			}
		}
		else{
			
			$this->session->set_flashdata('chit_alert',array('message'=>'Enable notification to send notification','class'=>'danger','title'=>'Notification'));
		}
		redirect('send/sendnotification');
		
	}
	
	function sendPushNotificationToAll($alertdetails = array())
	{
		$this->load->helper('push_notification');
		if (notify_platform_is('fcm')) {
			return $this->sendPushNotificationToAll_fcm($alertdetails);
		}
		return $this->sendPushNotificationToAll_onesignal($alertdetails);
	}

	function sendPushNotificationToAll_fcm($alertdetails = array())
	{
		$this->load->library('fcm_service');
		return $this->fcm_service->sendToAll(
			(isset($alertdetails['header']) ? $alertdetails['header'] : ''),
			(isset($alertdetails['message']) ? $alertdetails['message'] : ''),
			array(
				'targetUrl'    => (isset($alertdetails['targetUrl']) ? $alertdetails['targetUrl'] : '#/app/notification'),
				'noti_service' => (isset($alertdetails['notification_service']) ? $alertdetails['notification_service'] : ''),
				'mobile'       => ''
			),
			(isset($alertdetails['noti_img']) ? $alertdetails['noti_img'] : '')
		);
	}

	function sendPushNotificationToAll_onesignal($alertdetails = array())
	{
		$this->load->helper('push_notification');
		$onesignal = notify_cred_for('onesignal');
		$content = array(
		"en" => $alertdetails['message']
		);

		$targetUrl='#/app/notification';

		$fields = array(
		'app_id' => $onesignal['id'],
		'included_segments' => array('All'), // All
		'contents' => $content,
		'headings' => array("en" => $alertdetails['header']),
		'subtitle' => array("en" => $alertdetails['footer']),
		'data' => array('targetUrl'=>$targetUrl,'noti_service'=>$alertdetails['notification_service'],'mobile'=>''),
		'big_picture' =>(isset($alertdetails['noti_img'])?$alertdetails['noti_img']:" ")
		);
		
		$auth_key = $onesignal['key'];
		$fields = json_encode($fields);

		 $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
		  'Authorization: Basic '.$auth_key));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		// Without timeouts one stalled connection hangs the page forever.
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);

		$response = curl_exec($ch);
		curl_close($ch); 
		return $response;
	}
	
	
	function send_singlealert_send_notification($alertdetails = array())
	{
		$this->load->helper('push_notification');
		if (notify_platform_is('fcm')) {
			return $this->send_singlealert_send_notification_fcm($alertdetails);
		}
		return $this->send_singlealert_send_notification_onesignal($alertdetails);
	}

	function send_singlealert_send_notification_fcm($alertdetails = array())
	{
		$this->load->library('fcm_service');
		return $this->fcm_service->sendToTokens(
			(isset($alertdetails['token']) ? (array) $alertdetails['token'] : array()),
			(isset($alertdetails['header']) ? $alertdetails['header'] : ''),
			(isset($alertdetails['message']) ? $alertdetails['message'] : ''),
			array(
				'targetUrl'    => (isset($alertdetails['targetUrl']) ? $alertdetails['targetUrl'] : '#/app/notification'),
				'noti_service' => (isset($alertdetails['notification_service']) ? $alertdetails['notification_service'] : '')
			),
			(isset($alertdetails['noti_img']) ? $alertdetails['noti_img'] : '')
		);
	}

	function send_singlealert_send_notification_onesignal($alertdetails = array())
	{
		$this->load->helper('push_notification');
		$onesignal = notify_cred_for('onesignal');
		$registrationIds =array();
		// Accept either one token or a batch -- OneSignal takes many player ids per request.
		$registrationIds = (isset($alertdetails['token']) && is_array($alertdetails['token'])
		    ? array_values($alertdetails['token'])
		    : array(isset($alertdetails['token']) ? $alertdetails['token'] : ''));
		$content = array(
		"en" => $alertdetails['message']
		);

		$targetUrl='#/app/notification';
		$fields = array(
		'app_id' => $onesignal['id'],
		'include_player_ids' => $registrationIds,
		'contents' => $content,
		'headings' => array("en" => $alertdetails['header']),
		'data' => array('targetUrl'=>$targetUrl,'noti_service'=>$alertdetails['notification_service']),
		'big_picture' => (isset($alertdetails['noti_img'])?$alertdetails['noti_img']:" ")
		);
	
		$auth_key = $onesignal['key'];
		$fields = json_encode($fields);
			
		 $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
		  'Authorization: Basic '.$auth_key));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		// Without timeouts one stalled connection hangs the page forever.
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		$response = curl_exec($ch);
		//print_r($response);exit;
		curl_close($ch);  
	return $response;
	}
	
	function send_singlealert_notification($alertdetails = array())
	{
		$this->load->helper('push_notification');
		if (notify_platform_is('fcm')) {
			return $this->send_singlealert_notification_fcm($alertdetails);
		}
		return $this->send_singlealert_notification_onesignal($alertdetails);
	}

	function send_singlealert_notification_fcm($alertdetails = array())
	{
		$this->load->library('fcm_service');
		$noti_service = (isset($alertdetails['notification_service']) ? $alertdetails['notification_service'] : '');
		if ($noti_service == '2') {
			$targetUrl = '#/app/offers';
		} else if ($noti_service == '3') {
			$targetUrl = '#/app/newarrivals';
		} else if ($noti_service == '4' || $noti_service == '5' || $noti_service == '6') {
			$targetUrl = '#/app/paydues';
		} else {
			$targetUrl = '#/app/notification';
		}
		return $this->fcm_service->sendToTokens(
			(isset($alertdetails['token']) ? (array) $alertdetails['token'] : array()),
			(isset($alertdetails['header']) ? $alertdetails['header'] : ''),
			(isset($alertdetails['message']) ? $alertdetails['message'] : ''),
			array(
				'targetUrl'    => $targetUrl,
				'noti_service' => $noti_service,
				'mobile'       => (isset($alertdetails['mobile']) ? $alertdetails['mobile'] : '')
			),
			(isset($alertdetails['noti_img']) ? $alertdetails['noti_img'] : '')
		);
	}

	function send_singlealert_notification_onesignal($alertdetails = array())
	{
		$this->load->helper('push_notification');

		$onesignal = notify_cred_for('onesignal');

		$registrationIds =array();

		// Accept either one token or a batch -- OneSignal takes many player ids per request.
		$registrationIds = (isset($alertdetails['token']) && is_array($alertdetails['token'])
		    ? array_values($alertdetails['token'])
		    : array(isset($alertdetails['token']) ? $alertdetails['token'] : ''));

		$content = array(
		"en" => $alertdetails['message']
		);

		if($alertdetails['notification_service']== '2'){
			$targetUrl='#/app/offers';
		}
		else if($alertdetails['notification_service']== '3'){
			$targetUrl='#/app/newarrivals';
		}
		else if($alertdetails['notification_service']== '4' || $alertdetails['notification_service']== '5' || $alertdetails['notification_service']== '6'){
				$targetUrl='#/app/paydues';
		}
		else{
			$targetUrl='#/app/notification';
		}
			
		$fields = array(
		'app_id' => $onesignal['id'],
		'include_player_ids' => $registrationIds,
		'contents' => $content,
		'headings' => array("en" => $alertdetails['header']),
		'subtitle' => array("en" => $alertdetails['footer']),
		'data' => array('targetUrl'=>$targetUrl,'noti_service'=>$alertdetails['notification_service'],'mobile'=>$alertdetails['mobile']),
		'big_picture' => (isset($alertdetails['noti_img'])?$alertdetails['noti_img']:" ")
		);
	//echo "<pre>";print_r($fields);echo "</pre>";
		$auth_key = $onesignal['key'];
		$fields = json_encode($fields);
			
		 $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
		  'Authorization: Basic '.$auth_key));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		// Without timeouts one stalled connection hangs the page forever.
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);

		$response = curl_exec($ch);
		//echo "<pre>";print_r($response);echo "</pre>";exit;
		/*curl_close($ch); 
		print_r($response);*/
	return $response;
	}
	
//catlog module//HH	
	
 function catlog_module_form($type,$id="")

	{
	    
	    
		$model=self::SMS_MODEL;

		$cus_model=self::CUS_MODEL;

		switch ($type)

		{

			case 'list':
              $data['main_content'] =  self::SET_VIEW.'list';
	  
	 

	   $this->load->view('layout/template', $data);

			  break;

		    case 'Add':

				  $data['module']			=  $this->$model->get_empty_records();

			   	  $data['main_content'] =  self::SET_VIEW.'form';

			   	  //$data['type'] =  1;

				  $this->load->view('layout/template', $data);

				  break;
				  
		    case 'Edit':

				  $data['module']	=	$this->$model->get_entry_records($id);

			        $data['main_content'] =  self::SET_VIEW.'form';
			        
			      //$data['type'] =  2;

				  $this->load->view('layout/template', $data);

				  break;
				  
			case 'Delete':
			    
				  $model=self::SMS_MODEL;
				  
			  	  $this->$model->delete_module($id);
			  	  
				redirect('settings/module/list');  
					 
				  break;
				  
			default :

				 $set_model=self::SET_MODEL;

			  	  $data['access'] = $this->$set_model->get_access('settings/module/list');//

				  $data['module']=$this->$model->get_modules();

				  echo json_encode($data);

			break;

				

		}	

	}
		
		
	//to update module enable/disable //

	function module_status($type,$status,$id)

	{

		
        //print_r($status);exit:
		if($type == 'm_active')

		{

			$data = array('m_active' => $status);

		}

		else if($type == 'm_web'){

			

			$data = array('m_web' => $status);

		}
		else {

			

			$data = array('m_app' => $status);

		}
	

		

		$model=self::SMS_MODEL;

		$result = $this->$model->update_module_status($data,$id);

		if($result)

		{

			$this->session->set_flashdata('chit_alert',array('message'=>'Modules status updated as '.($status ? 'active' : 'inactive').' successfully.','class'=>'success','title'=>'Modules Status'));			

		}	

		else

		{

			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Modules Status'));

		}	

		redirect('settings/module/list');

	}


	public function catlog_module_post($type,$id="")

	{

		$model=self::SMS_MODEL;

		switch($type)

		{

			case 'Save':

			       $module=$this->input->post('module');

			       $module_data=array(

			       		

			       			'id_module'		=>  (isset($module['id_module'])?$module['id_module']: NULL), 

			       			'm_name'		=>  (isset($module['m_name'])?$module['m_name']: NULL),
			       			
			       			'm_code'		=>  (isset($module['m_code'])?$module['m_code']: NULL)

					   );

			             $this->db->trans_begin();

			             $module_id=  $this->$model->insert_module($module_data);
			             //print_r($module_data);exit;

			             if($this->db->trans_status()===TRUE)

			             {

						 	$this->db->trans_commit();

						 	$this->session->set_flashdata('chit_alert',array('message'=>'Module record added successfully','class'=>'success','title'=>'Module'));

						 	redirect('settings/module/list');

						 }

						 else

						 {

						 	 $this->db->trans_rollback();						 	

						 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Module'));

						 	redirect('settings/module/list');

						 }

			       

				break;

				

			case 'Update':

			       $module=$this->input->post('module');

			       $module_data=array(

			       		

			       			'id_module'		=>  (isset($module['id_module'])?$module['id_module']: NULL), 

			       			'm_name'			=>  (isset($module['m_name'])?$module['m_name']: NULL),
			       			
			       			'm_code'		=>  (isset($module['m_code'])?$module['m_code']: NULL)

			       		

						   );

			             $this->db->trans_begin();

			             $module_id=  $this->$model->update_module($module_id);

			             if($this->db->trans_status()===TRUE)

			             {

						 	$this->db->trans_commit();

						 	$this->session->set_flashdata('chit_alert',array('message'=>'Module record edited successfully','class'=>'success','title'=>'Module'));

						 	redirect('settings/module/list');

						 }

						 else

						 {

						 	 $this->db->trans_rollback();						 	

						 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Module'));

						 	redirect('settings/module/list');

						 }

			       

				break;

				}

	}


	function ret_settings_form($type,$id="")
	{
		$model=self::SMS_MODEL;
		switch ($type)
		{
			case 'list':
              $data['main_content'] =  self::RET_SET_VIEW.'list';
	   $this->load->view('layout/template', $data);
			  break;
		    case 'Add':
				  $data['retail_setting']			=  $this->$model->get_empty_recordss();
			   	  $data['main_content'] =  self::RET_SET_VIEW.'form';
			   	  //$data['type'] =  1;
				  $this->load->view('layout/template', $data);
				  break;
		    case 'Edit':
				  $data['retail_setting']	=	$this->$model->get_entry_recordss($id);
			        $data['main_content'] =  self::RET_SET_VIEW.'form';
			      //$data['type'] =  2;
				  $this->load->view('layout/template', $data);
				  break;
			case 'Delete':
				  $model=self::SMS_MODEL;
			  	  $this->$model->delete_ret_settings($id);
				redirect('settings/retail_setting/list');  
				  break;
			default :
				 $set_model=self::SET_MODEL;
			  	  $data['access'] = $this->$set_model->get_access('settings/retail_setting/list');
				  $data['retail_setting']=$this->$model->get_ret_settings();
				  echo json_encode($data);
			break;
		}	
	}
public function ret_settings_post($type,$id="")
	{
		$model=self::SMS_MODEL;
		switch($type)
		{
			case 'Save':
			       $retail_setting=$this->input->post('retail_setting');
			       $retail_setting_data=array(
			       			'id_ret_settings'		=>  (isset($retail_setting['id_ret_settings'])?$retail_setting['id_ret_settings']: NULL), 
			       			'name'		            =>  (isset($retail_setting['name'])?$retail_setting['name']: NULL),
			       			'value'		            =>  (isset($retail_setting['value'])?$retail_setting['value']: NULL),
			       			'description'		    =>  (isset($retail_setting['description'])?$retail_setting['description']: NULL),
			       			'created_by'            => $this->session->userdata('uid'),
			       			'created_on' 	     => date("Y-m-d H:i:s")
					   );
			             $this->db->trans_begin();
			             $retail_setting_id=  $this->$model->insert_ret_settings($retail_setting_data);
			             //print_r($retail_setting_id);exit;
			             if($this->db->trans_status()===TRUE)
			             {
						 	$this->db->trans_commit();
						 	$this->session->set_flashdata('chit_alert',array('message'=>'Retail Settings record added successfully','class'=>'success','title'=>'Retail Settings'));
						 	redirect('settings/retail_setting/list');
						 }
						 else
						 {
						 	 $this->db->trans_rollback();						 	
						 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Retail Settings'));
						 	redirect('settings/retail_setting/list');
						 }
				break;
			case 'Update':
			       $retail_setting=$this->input->post('retail_setting');
			       $retail_setting_data=array(
			       			'id_ret_settings'		=>  (isset($retail_setting['id_ret_settings'])?$retail_setting['id_ret_settings']: NULL), 
			       			'name'		            =>  (isset($retail_setting['name'])?$retail_setting['name']: NULL),
			       			'value'		            =>  (isset($retail_setting['value'])?$retail_setting['value']: NULL),
			       			'description'		    =>  (isset($retail_setting['description'])?$retail_setting['description']: NULL),
			       			'updated_by'            => $this->session->userdata('uid'),
			       			'updated_on' 	     => date("Y-m-d H:i:s")
						   );
                       //print_r($retail_setting_data);exit;
			             $this->db->trans_begin();
			             $retail_setting_id=  $this->$model->update_ret_settings($retail_setting_data);
                             // print_r($this->db->last_query());exit;
			             if($this->db->trans_status()===TRUE)
			             {
						 	$this->db->trans_commit();
						 	$this->session->set_flashdata('chit_alert',array('message'=>'Retail Settings record edited successfully','class'=>'success','title'=>'Retail Settings'));
						 	redirect('settings/retail_setting/list');
						 }
						 else
						 {
						 	 $this->db->trans_rollback();						 	
						 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Retail Settings'));
						 	redirect('settings/retail_setting/list');
						 }
				break;
				}
	}
	// Retail setting//


//catlog module//
	
	function test(){
	    $mobile = 7010198473;
	    $msg    = "Hi Your OTP  For Duplicate Tag is :  {#var#} Will expire within {#var#}. REGARDS, LOGIMAX TECHNOLOGIES PRIVATE LIMITED.";
	    $sendSMS =  $this->sms_model->sendWA_Qikberry($mobile,$msg,'','1207172404444093203');
	}

	

}	

?>