<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_import_export extends CI_Controller 
{
	const VIEW_FOLDER = 'settings/';
	const MAS_VIEW = 'master/';
	const DATA_FILE_PATH= "assets/upload/data/";
	const CUS_FILE_PATH = "assets/upload/customer/";
	const SET_MODEL  = "admin_settings_model";
	const SCH_MODEL  = "scheme_model";
	const CUS_MODEL  = "customer_model";
	const ACC_MODEL  = "account_model";
	const PAY_MODEL  = "payment_model";
	const REJ_PATH	 = 'assets/upload/rejected/';
	const ADM_MODEL = "chitadmin_model";
	const EXPORT_PATH="assets/export/";
	const SMS_MODEL = "admin_usersms_model";
	const LOG_MODEL = "log_model";
	const MAIL_MODEL = "email_model";
	
	public function __construct()
    {
        parent::__construct();
         if(!$this->session->userdata('is_logged'))
        {
			redirect('admin/login');
		}
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->id_employee =  $this->session->userdata('uid');
		$this->id_log =  $this->session->userdata('id_log');
		$this->load->library('excel');
        $this->load->model('admin_settings_model');
    	$this->load->model(self::ADM_MODEL);
    	$this->load->model(self::SMS_MODEL);
    	$this->load->model(self::LOG_MODEL);
    	$this->load->model(self::ACC_MODEL);
    	$this->load->model(self::MAIL_MODEL);
    }
    //encrypt
	public function __encrypt($str)
	{
		return base64_encode($str);		
	}	
//function to show form import 

   function import_acc_form()
   {
   	  $data['main_content'] = self::VIEW_FOLDER.'import/import_account';
	  $this->load->view('layout/template', $data);
   } 
  
 //function to show list import  
   function import_list($lower="",$higher="")
   {
   	  $data['first_id'] = $lower;
   	  $data['last_id']  = $higher;
   	  $data['main_content'] =  self::VIEW_FOLDER.'import/import_list';
	  $this->load->view('layout/template', $data);
   }
   
   //to load import list  
   function ajax_import_list($lower,$higher)
   {
	  $model = self::ACC_MODEL;
	  $data['imported'] = $this->$model->get_accounts_range($lower,$higher);
	  echo json_encode($data);
   }
   
 //sendSMS login details 
 function send_login()
 {
 	$model=self::CUS_MODEL;
 	$cus=$this->input->post('account_id');
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
        	$sms_status=$this->send_login_sms($cusData['mobile']);
	        $total[]=$sms_status;
		}
	
	}                
	   echo 'Login details SMS has been sent to '.count($total).(count($total)>1?' customers':' customer');    
 }  
   //sendEmail login details
 function send_login_email()
 {
 	$model=self::CUS_MODEL;
 	$cus=$this->input->post('account_id');
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
			$mail_status=$this->send_mail($cusData);
	        $total[]=$mail_status;
		}
		
	}                
	   echo 'Login details E-Mail has been sent to '.count($total).(count($total)>1?' customers':' customer');    
 }  
 
 function send_mail($cusData)
	{	
	  	$ser_model = self::SET_MODEL;
	  	$company = $this->$ser_model->get_company();
	    $mail_model=self::MAIL_MODEL;
		$email	=  $cusData['email'];
		if($email!= '')
		{
			$data['schData'] = $cusData;
			$data['company'] = $company;
			$data['type'] = 1;
			$to = $email;
			$subject = "Reg- ".$company['company_name']." saving scheme Login details";
			$message = $this->load->view('include/emailAccount',$data,true);
			$sendEmail = $this->$mail_model->send_email($to,$subject,$message);
			return TRUE;
		}
		
	}
		
   
//function to import data   

	function import_account()
	{
		
		$import		= $this->input->post('import');	
		$is_heading	= (isset($import['is_heading'])?$import['is_heading']:0);
		
		   if ($_FILES && $_FILES['import_file']["tmp_name"] !="") 	
		 {
		 			 	
		    $model=self::SET_MODEL;
		 	$pathToUpload=self::DATA_FILE_PATH;
		    $filename=trim(date('Ymd_His').'_data'.'.xlsx');
		 
			if($this->upload_data($filename,$pathToUpload))
			{
				
				$imp_data=$this->$model->import_excel($pathToUpload,$filename,$is_heading);
				   
				if(!empty($imp_data))
				{
					$data=array(
							"xl_data"	 => $imp_data	
						);
						
						  
					$this->parse_scheme_account($data);	
				}
                else
                {
					$this->session->set_flashdata('chit_alert', array('message' => 'No data to import','class' => 'danger','title'=>'Import Data'));
					 redirect('settings/import/account');
				}					
						
			}
			else
			{
				
				$this->session->set_flashdata('chit_alert', array('message' => 'failed reading upload files','class' => 'danger','title'=>'Import Data'));
	                  
	            redirect('settings/import/account');
				
			}

		 	
		 }
		 else
		 {
				$this->session->set_flashdata('chit_alert', array('message' => 'File not selected','class' => 'success','title'=>'Import Data'));
	                  
	            redirect('settings/import/account');
			
		 }
		 
	}	
	
	function is_mobile_exists($mobile)
	{
		
		if($mobile!=NULL)
		{
			$model=self::CUS_MODEL;
			$this->load->model($model);
			$available=$this->$model->mobile_available_import($mobile);
			
			//print_r($available);exit;
		
			return $available;
		}
		else
		{
			return FALSE;
		}
		
	}
   
    function customer_already_exist($mobile,$email)
    {
    	$is_exists=FALSE;
    	$model=self::CUS_MODEL;
    	
		$this->load->model($model);
	    if($mobile!=NULL)
		{
			$is_exists=$this->$model->mobile_available($mobile);
		}		
		
		if($email!=NULL)
		{
			$is_exists=$this->$model->email_available($email);
		}
		return $is_exists == TRUE ? TRUE : FALSE;
	}
	
	//to remove duplicate values
	function super_unique($array,$key)
	{

	   $temp_array = array();
	   foreach ($array as &$v) {
	   	
	       if (!isset($temp_array[$v[$key]]))
	       $temp_array[$v[$key]] =& $v;
	   }
	  $array = array_values($temp_array);
	   return $array;
	}
	
	
	function parse_scheme_account($data)
	{
		$import_data=array();
		$count_ins_acc=array();
		$count_ins_cus=array();
		$log_model = self::LOG_MODEL;
		$cus_mod = self::CUS_MODEL;
		$this->load->model($cus_mod);
		$acc_model=self::ACC_MODEL;
		$flag=FALSE;
		
		//send the data in an array format
		foreach((array)$data['xl_data']  as $row)
		{
			
			  $records[]=array(
			   	 'firstname'				=>$row['A'],
			   	 'lastname'					=>$row['B'],
			   	 'mobile'					=>$row['C'],
			   	 'email'					=>$row['D'],
			   	 'pan'						=>$row['E'],
			   	 'ref_no'					=>$row['F'],
			   	 'account_name'				=>$row['G'],
			   	 'scheme_code'				=>$row['H'],
			   	 'start_date'				=>$row['I'],
			   	 'paid_installments'		=>$row['J'],
			   	 'balance_amount'		    =>$row['K'],
			   	 'balance_weight'		    =>$row['L'],
			   	 'last_paid_weight'			=>$row['M'],
			   	 'last_paid_chances'		=>$row['N'],
			   	 'last_paid_date'			=>$row['O'],
			   	 'is_new'					=>$row['P']
			   	// 'firstPayment_amt'			=>$row['Q']
			   	 	 	
			   );
			   
		}
		
      foreach($records as $record)
       {		
 
       if (!$this->emptyArray($record))
        {
      	 $is_cus_exists = $this->is_mobile_exists($record['mobile']);  
      	 //print_r($is_cus_exists);exit;
      	 
		 $scheme_id			= $this->get_scheme_id($record['scheme_code']);
		 $is_ref_exists =1;
		 if($scheme_id){
      	 	$is_ref_exists = $this->is_refno_exists($record['ref_no'],$scheme_id);
		 }
		 $id_customer   = $this->getCustomerByMobile($record['mobile']);
		  
			//if mobile & email not registered already and refno not exists
			if($id_customer == 0 && $is_cus_exists !=1   &&  $is_ref_exists !=1  && $record['ref_no']!=null && $record['last_paid_date']!=null && $record['mobile']!=null && $scheme_id!=NULL && $scheme_id>0)
			{
			$custype=(($record['is_new']=='Yes' || $record['is_new']=='yes')?'Y':'N');
				$import_data["customer"]=array(	     
						 'firstname'	 	=> $record['firstname'],
						 'lastname'	 		=> $record['lastname'],
					   	 'mobile'			=> $record['mobile'],
					   	 'email'			=> $record['email'],
					   	 'pan'				=> $record['pan'],
					   	 'passwd'			=> $this->__encrypt($this->generate_password()),
					   	 'date_add'			=> date("Y-m-d H:i:s"),
					   	 'profile_complete' => 0,
					   	 'id_employee'		=> $this->session->userdata('uid'),
					   	 'active'			=> 1
					);
				//insert customer details	
				$schAc_number=str_pad($record['ref_no'], 5, '0', STR_PAD_LEFT);
				$cus_id = $this->$cus_mod->insert_imported_customer($import_data["customer"]);	
				$import_data["scheme_account"]=array(
						'ref_no'			=> $record['ref_no'],
						'scheme_acc_number'	=> $schAc_number,
				   	 	'id_scheme'    		=> $scheme_id,
				   	 	'account_name' 		=> $record['account_name']==""?$record['firstname']:$record['account_name'],
				   		'paid_installments'	=> $record['paid_installments'],
				   	 	'balance_amount'	=> $record['balance_amount'],
				   	 	'balance_weight'	=> $record['balance_weight'],
				   		'last_paid_chances'	=> $record['last_paid_chances'],		   	 	
				   		'last_paid_weight'	=> $record['last_paid_weight'],
				   	 	'last_paid_date'	=> $this->excel_to_PHP($record['last_paid_date']),
				   	 	'start_date'		=> $this->excel_to_PHP($record['start_date']),
				   	 	'is_opening'		=> 1,
				   	 	'active'			=> 1,
				   	 	'is_closed'			=> 0,
				   	 	'employee_approved' => $this->session->userdata('uid'),
				   	 	'date_add'			=> date("Y-m-d H:i:s"),
				   	 	'id_customer'		=> ($cus_id>0 ? $cus_id:0),
					   	'is_new'			=> $custype,
					   	'firstPayment_amt'  => $record['firstPayment_amt'],
		   		);
		   			  $id=$import_data["scheme_account"]['id_customer'];
					  if($scheme_id!=NULL && $scheme_id>0)
					  {
					  	
				   		$ins_acc=$this->insert_scheme_account($import_data["scheme_account"]);
				   		$cus_data=	$this->$acc_model->get_customer_acc($ins_acc);
						if($ins_acc>0)
						{
							$count_ins_acc[]=$ins_acc;
							$count_ins_cus[]=$id;
							$this->account_join_message($ins_acc,$cus_data);
						}
						else
						{
							 $import_data["invalid"][]=$import_data["scheme_account"];
						}
					 }
					
		   		
			}
			else if($is_cus_exists ==1 && $is_ref_exists !=1 &&  $record['ref_no']!=null && $record['mobile']!=null && $record['last_paid_date']!=null )
			{			
				$custype=(($record['is_new']=='Yes' || $record['is_new']=='yes')?'Y':'N');
				$schAc_number=str_pad($record['ref_no'], 5, '0', STR_PAD_LEFT);
				
				 $import_data["scheme_account"]=array(
						'ref_no'			=> $record['ref_no'],
						'scheme_acc_number'	=> $schAc_number,
				   	 	'id_scheme'    		=> $scheme_id,
				   	 	'account_name' 		=> $record['account_name']==""?$record['firstname']:$record['account_name'],
				   		'paid_installments'	=> $record['paid_installments'],
				   	 	'balance_amount'	=> $record['balance_amount'],
				   	 	'balance_weight'	=> $record['balance_weight'],
				   		'last_paid_chances'	=> $record['last_paid_chances'],		   	 	
				   		'last_paid_weight'	=> $record['last_paid_weight'],
				   	 	'last_paid_date'	=> $this->excel_to_PHP($record['last_paid_date']),
				   	 	'start_date'		=> $this->excel_to_PHP($record['start_date']),
				   	 	'is_opening'		=> 1,
				   	 	'active'			=> 1,
				   	 	'is_closed'			=> 0,
				   	 	'employee_approved' => $this->session->userdata('uid'),
				   	 	'date_add'			=> date("Y-m-d H:i:s"),
				   	 	'id_customer'		=> $id_customer,
					   	'is_new'			=> $custype,
					   	'firstPayment_amt'  => $record['firstPayment_amt'],
		   		);
		   	
				$ins_acc=$this->insert_scheme_account($import_data["scheme_account"]);
				$cus_data=	$this->$acc_model->get_customer_acc($ins_acc);
				if($ins_acc>0 && $id_customer!=null &&  $id_customer>0)
				{
					$count_ins_acc[]=$ins_acc;
					$this->account_join_message($ins_acc,$cus_data);
				}
				else
				{
					 $import_data["invalid"][]=$import_data["valid"];
				}
			}
			else
			{	
					
				 $import_data["invalid"][]=array(
			   	 	 'firstname'				=>$record['firstname'],
			   	 	 'lastname'				    =>$record['lastname'],
				   	 'mobile'					=>$record['mobile'],
				   	 'email'					=>$record['email'],
				   	 'pan'						=>$record['pan'],
				   	 'ref_no'					=>$record['ref_no'],
				   	 'account_name'				=>$record['account_name'],
				   	 'scheme_code'				=>$record['scheme_code'],
				   	 'start_date'				=>$this->excel_to_PHP($record['start_date']),
				   	 'paid_installments'	    =>$record['paid_installments'],
				   	 'balance_amount'			=>$record['balance_amount'],
				   	 'balance_weight'			=>$record['balance_weight'],		   	 	
				   	 'last_paid_weight'			=>$record['last_paid_weight'],
				   	 'last_paid_chances'		=>$record['last_paid_chances'],		   	 	
				   	 'last_paid_date'			=>$this->excel_to_PHP($record['last_paid_date']),	   	 	
				   	 'is_new'					=>$record['is_new']	 	,
					 'firstPayment_amt'         => $record['firstPayment_amt'],
		   		);
			}
		}
	}
	   $valid_rows	 = (isset($count_ins_acc)?sizeof($count_ins_acc):0);
	   $invalid_rows = (isset($import_data["invalid"])?sizeof($import_data["invalid"]):0);
	   $totalrows	 = $valid_rows + $invalid_rows;
 		if(!empty($import_data["invalid"]))
		   {
			//export data header
			 $data["header"][0]=array(
				   	 'firstname'				=> 'First Name',
				   	 'lastname'					=> 'Last Name',
				   	 'mobile'					=> 'Mobile',
				   	 'email'					=> 'E-Mail',
				   	 'pan'						=> 'Pan',
				   	 'ref_no'					=> 'Ref No',
				   	 'account_name'				=> 'A/c Name',
				   	 'scheme_code'				=> 'Scheme Code',
				   	 'start_date'				=> 'Start Date'	,
				   	 'paid_installments'		=> 'No of Paid Installments',
				   	 'balance_amount'			=> 'Total Paid Amount',
				   	 'balance_weight'			=> 'Total Paid Weight',
				   	 'last_paid_weight'			=> 'Last Paid Weight',
				   	 'last_paid_chances'		=> 'Last Paid Chances',
				   	 'last_paid_date'			=> 'Last Paid Date',
				   	 'is_new'					=> 'Is New Customer',
					 'firstPayment_amt'         => "First Ins paid amount"
				   	 	
				   );	
			 $data['export']= array_merge($data["header"],$import_data["invalid"]);
			 $this->export_XLfile($data['export'],"rejected");
			 }
			else{
			 $data["header"][0]=array(
				   	 'firstname'				=> 'First Name',
				   	 'lastname'					=> 'Last Name',
				   	 'mobile'					=> 'Mobile',
				   	 'email'					=> 'E-Mail',
				   	 'pan'						=> 'Pan',
				   	 'ref_no'					=> 'Ref No',
				   	 'account_name'				=> 'A/c Name',
				   	 'scheme_code'				=> 'Scheme Code',
				   	 'start_date'				=> 'Start Date'	,
				   	 'paid_installments'		=> 'No of Paid Installments',
				   	 'balance_amount'			=> 'Total Paid Amount',
				   	 'balance_weight'			=> 'Total Paid Weight',
				   	 'last_paid_weight'			=> 'Last Paid Weight',
				   	 'last_paid_chances'		=> 'Last Paid Chances',
				   	 'last_paid_date'			=> 'Last Paid Date',
				   	 'is_new'					=> 'Is New Customer',
					 'firstPayment_amt'         => "First Ins paid amount"
				   	 	
				   );	
			 $data['export']= $data["header"];
			 $this->export_XLfile($data['export'],"rejected");
			}
		
		 if($count_ins_acc != null)
		   {
			  $acc_id =$count_ins_acc;
					//update import log
					$import=array(
						'total'		  => $totalrows,
						'imported'	  => $valid_rows,
						'failed' 	  => $invalid_rows,
						'firstrecord' => reset($acc_id),
						'lastrecord'  => end($acc_id),
						'id_employee' => $this->session->userdata('uid'),
						'import_date' => date('Y-m-d H:i:s')
					);
								
				    $this->update_import_log($import);
				    //for log report
				      $log_data = array(
											'id_log'     => $this->id_log,
											'event_date' => date("Y-m-d H:i:s"),
											'module'     => 'Scheme Account Import',
											'operation'  => 'Import',
											'record'     =>  '0',  
											'remark'     => 'Imported '.$valid_rows.' of '.$totalrows. ' Records'
										 );
										 
						$this->$log_model->log_detail('insert','',$log_data);
						$first_id= reset($count_ins_acc);
						$last_id= end($count_ins_acc);
						$this->session->set_flashdata('chit_alert',array('message'=> $valid_rows.' scheme accounts created successfully through import..','class'=>'success','title'=>'Import Data'));
					 	redirect('settings/import/list/'.$first_id.'/'.$last_id);	
				
		   }
		  else{
			  	$log_data = array(
									'id_log'     => $this->id_log,
									'event_date' => date("Y-m-d H:i:s"),
									'module'     => 'Scheme Account Import',
									'operation'  => 'Import',
									'record'     => '0',  
									'remark'     => 'Imported '.$valid_rows.' of '.$totalrows. ' Records'
								 );
				$this->$log_model->log_detail('insert','',$log_data);
			 	$this->session->set_flashdata('chit_alert',array('message'=>'Invalid records!Please check the records in Excel..','class'=>'danger','title'=>'Import Data')); 
			 	redirect('settings/import/account');
		}   
			   
    }
			      
		
	//convert excel date to php date
	function excel_to_PHP($dateValue = 0) 
	{
		
	    $myExcelBaseDate = 25569;
	    //  Adjust for the spurious 29-Feb-1900 (Day 60)
	    if ($dateValue < 60) {
	        --$myExcelBaseDate;
	    }

	    // Perform conversion
	    if ($dateValue >= 1) {
	        $utcDays = $dateValue - $myExcelBaseDate;
	        $returnValue = round($utcDays * 86400);
	        if (($returnValue <= PHP_INT_MAX) && ($returnValue >= -PHP_INT_MAX)) {
	            $returnValue = (integer) $returnValue;
	        }
	    } else {
	        $hours = round($dateValue * 24);
	        $mins = round($dateValue * 1440) - round($hours * 60);
	        $secs = round($dateValue * 86400) - round($hours * 3600) - round($mins * 60);
	        $returnValue = (integer) gmmktime($hours, $mins, $secs);
	    }

	    // Return
	    return date("Y-m-d H:i:s",$returnValue);
	}   
	
	//  function get scheme id
	function get_scheme_id($scheme_code)
	{
		$model=self::SCH_MODEL;
		$this->load->model($model);
		$id_scheme=$this->$model->get_scheme_id($scheme_code);
		return ($id_scheme!=NULL?$id_scheme:0);
	}
	
	function get_weight_scheme_id($type,$scheme_code)
	{
		$model=self::SCH_MODEL;
		$this->load->model($model);
		$id_scheme=$this->$model->get_weight_scheme_id($type,$scheme_code);
		return ($id_scheme!=NULL?$id_scheme:0);
	}
	
  
	function insert_scheme_account($data)
	{
		$model=self::ACC_MODEL;
		$this->load->model($model);
		$id=$this->$model->import_insert_account($data);
		return ($id>0 && $id!=NULL?$id:0);
	}
	
	function send_login_sms($mobile)
	{
		$serviceID=11;
		$sms_model= self::SMS_MODEL;	
		$id =$mobile;
		$data =$this->$sms_model->get_SMS_data($serviceID,$id);
		$mobile_number =$data['mobile'];
		$message = $data['message'];
		$result=$this->send_sms($mobile_number,$message,'','');
		return $result?1:0;
	}
	
	function send_sms($mobile_number,$message)
	{
		$model = self::ADM_MODEL;
		$this->$model->send_sms($mobile_number,$message,'','');
	}
	
	//scheme joining sms and email
	function account_join_message($id,$data)
	{	
	    $ser_model = self::SET_MODEL;
	    $mail_model=self::MAIL_MODEL;
	    $sms_model= self::SMS_MODEL;
	    $serviceID = 2;
	    $service = $this->$ser_model->get_service($serviceID);
	    $company = $this->$ser_model->get_company();
		$email	=  $data['email'];
		
		if($service['serv_email'] == 1  && $email!= '')
		{
			$data['schData'] = $data;
			$data['company'] = $company;
			$data['type'] = 1;
			$to = $email;
			$subject = "Reg- ".$company['company_name']." saving scheme account joining";
			$message = $this->load->view('include/emailscheme',$data,true);
			$sendEmail = $this->$mail_model->send_email($to,$subject,$message);
		}
		if($service['serv_sms'] == 1)
		{	
			$data =$this->$sms_model->get_SMS_data($serviceID,$id);
			$mobile_number =$data['mobile'];
			$message = $data['message'];
			$this->send_sms($mobile_number,$message,'','');
		}
	}
	
	function update_import_log($data)
	{
		$model=self::SET_MODEL;
		$status=$this->$model->insert_import_log($data);
		return $status;
	}

	function generate_password($length = 8)
	{
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $string = '';

	    for ($i = 0; $i < $length; $i++) {
	        $string .= $characters[mt_rand(0, strlen($characters) - 1)];
	    }
	    return $string;
	}

	
	function upload_data($filename,$pathToUpload)
	{
		if ($_FILES && $_FILES['import_file']["tmp_name"] !="") 
		{
			
			if (!is_dir($pathToUpload)) {
		    	mkdir($pathToUpload, 0777, TRUE);			
			}
			 $files = glob(self::DATA_FILE_PATH.'*'); // get all file names
				foreach($files as $file){ // iterate files
			  if(is_file($file))
			    unlink($file); // delete file
			}
			//echo $pathToUpload.$filename;
			$config['upload_path'] = $pathToUpload ;
			$config['allowed_types'] = 'xlsx|xls';
			$config['file_name'] = $filename;
			$config['overwrite'] = TRUE;
			$this->upload->initialize($config);
			if (!$this->upload->do_upload('import_file'))
			{
				$error = array('error' => $this->upload->display_errors());
              
               
					return false;
			}
		
			else		 
			{
				
				  return true;
			}
			
	  }   
	}

  function export_to_excel()
  {
  	$model=self::PAY_MODEL;
  	$this->load->model($model); 
  	  	
  	$status	=	$this->input->post('pay_status');
  	$from	=	$this->input->post('from_date');
  	$to		=	$this->input->post('to_date');
            
	    if($from!=NULL and $to!=NULL)
	     {
		 	$payments=$this->$model->ajax_get_payments($status,date("Y-m-d",strtotime($from)),date("Y-m-d",strtotime($to)));
		 }
		 else
		 {
		 	
		 	$payments=$this->$model->ajax_get_payments($status,date("Y-m-d",strtotime($from)));
		 }
		 
		 
		  if(!empty($payments))
		  {
		  	
		  	//adding heading
		  	 $data[]=array( 'id_payment' => 'id',
            'ref_no' 		=> 'unique_code',
            'code' 	 		=> 'chit_code',
            'name' 	 		=> 'name',
            'mobile' 		=> 'mobile',
            'date' 	 		=> 'date',
            'payment_amount'=> 'amount',
            'payment_mode' 	=> 'payment_mode',
            'bank_acc_no' 	=> 'account_number',
            'bank_name' 	=> 'bank',
            'bank_branch' 	=> 'branch',
            'bank_IFSC' 	=> 'IFSC',
            'bank_charges' 	=> 'charges',
            'trans_id' 		=> 'trans_id',
            'payment_status'=> 'status');
            
		  	 foreach($payments as $payment)
		   	  {
			  	$data[] = array( 'id_payment' => $payment['id_payment'],
				            'ref_no' 		  => ($payment['ref_no']!=NULL?$payment['ref_no']:"-"),
				            'code'            => ($payment['code']!=NULL?$payment['code']:"-"),
				            'name'            => ($payment['name']!=NULL?$payment['name']:"-"),
				            'mobile'          => ($payment['mobile']!=NULL?$payment['mobile']:"-"),
				            'date'            => ($payment['date']!=NULL?$payment['date']:"-"),
				            'payment_amount'  => ($payment['payment_amount']!=NULL?$payment['payment_amount']:"-"),
				            'payment_mode'    => ($payment['payment_mode']!=NULL?$payment['payment_mode']:"-"),
				            'bank_acc_no'     => ($payment['bank_acc_no']!=NULL?$payment['bank_acc_no']:"-"),
				            'bank_name'       => ($payment['bank_name']!=NULL?$payment['bank_name']:"-"),
				            'bank_branch'     => ($payment['bank_branch']!=NULL?$payment['bank_branch']:"-"),
				            'bank_IFSC'       => ($payment['bank_IFSC']!=NULL?$payment['bank_IFSC']:"-"),
				            'bank_charges'    => ($payment['bank_charges']!=NULL?$payment['bank_charges']:"-"),
				            'trans_id'        => ($payment['trans_id']!=NULL?$payment['trans_id']:"-"),
				            'payment_status'  => ($payment['payment_status']!=NULL?$payment['payment_status']:"-"));
			  }
			
			
			 //export data 
			 $this->export_XLfile($data,'payment'); 
			 
			 $this->session->set_flashdata('chit_alert',array('message'=>'Payment exported successfully','class'=>'success','title'=>'Export Data'));
			 
			 redirect('settings/export');
			  
		  }
	
		
		$this->session->set_flashdata('chit_alert',array('message'=>'Export failed! No data to export..','class'=>'danger','title'=>'Export Data'));
			 $data['main_content'] = self::VIEW_FOLDER.'export/export_data';
	         $this->load->view('layout/template', $data);
		 
  }
  
  function export_XLfile($data,$file)
  {
  	  	
		// Create new PHPExcel object
        $objPHPExcel = new PHPExcel();
        
        // Fill worksheet from values in array
        $objPHPExcel->getActiveSheet()->fromArray($data, null, 'A1');
        
        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Members');
        
        // Set AutoSize for name and email fields
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
 
 
        // Save Excel 2007 file
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
       /* $objWriter->save($file);
        
       if (file_exists($file)) 
       {
          return TRUE;
       }*/
       ob_end_clean();
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment; filename=$file.xlsx");
		header("Cache-Control: max-age=0");
		if (!is_dir(self::REJ_PATH)) {
		    mkdir(self::REJ_PATH, 0777, TRUE);
		}
		
		$path=self::REJ_PATH."rejected.xlsx";
		if (file_exists($path)) 
	       {
	       	 chmod(self::REJ_PATH,0777);
	         unlink($path);
	       }
	     $objWriter->save($path);
	      return TRUE;
	    
  }
 
  function export_form()
  {
  	 $data['main_content'] = self::VIEW_FOLDER.'export/export_data';
	 $this->load->view('layout/template', $data);
  }
  
 
  
  function getCustomerByMobile($mobile)
  {
  	$model=self::CUS_MODEL;
	$this->load->model($model);
    $id_customer=$this->$model->getCustomerByMobile($mobile);   
    return ($id_customer?$id_customer:0);    
  }  
  
  function getSchemeAccountByCustomer($id)
  {
  	  $model=self::ACC_MODEL;
	  $this->load->model($model);
      $id_scheme_account=$this->$model->getSchemeAccountByCustomerID($id);
    return $id_scheme_account;    
  }
  
  function is_refno_exists($ref_no,$sch_id)
  {
  	  $model=self::ACC_MODEL;
	  $this->load->model($model);
      return $this->$model->is_refno_exists_import($ref_no,$sch_id);
  }
  
 
  
  function account_list()
  {
  	$model=	self::ACC_MODEL;
  	$this->load->model($model);
	//$data['accounts']=$this->$model->get_all_account();
  	$data['main_content'] = self::VIEW_FOLDER.'export/export_account';
	$this->load->view('layout/template', $data);
  }
  
function export_account()
  {
    $model=	self::ACC_MODEL;
  	$this->load->model($model);
  	
  	$filter_by	=	$this->input->post('filter_by');
  	$from	=	date('Y-m-d',strtotime($this->input->post('from_date')));
  	$to		=	date('Y-m-d',strtotime($this->input->post('to_date')));
  	
  	if($from!=null && $to!=null)
  	{
			$data['accounts']=$this->$model->get_export_data($filter_by,$from,$to);
	}
	else
	{
			$data['accounts']= $this->$model->get_export_data($filter_by,$from);
	}
	
	$header[0]=array(
		 'id_scheme_account' => 'ID',
            'ref_no' => "Unique Number",            
            'name'   => "Customer Name",
            'mobile' =>"Mobile",   			
            'start_date' => "Start Date",
            'code' => "Scheme Code",            
            'date_add' =>"Created On"
	);

	 $export_data=$header+$data['accounts'];
	
		if(!empty($data['accounts']))
	{
		
		$this->session->set_flashdata('chit_alert',array('message'=>'Data exported successfully','class'=>'success','title'=>'Export Accounts'));
		$this->export_XLfile($export_data,'export_account');
	}
	else{
		$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Export Accounts'));
	}
	
  
  
  	$data['main_content'] = self::VIEW_FOLDER.'export/export_account';
	$this->load->view('layout/template', $data);
	
	
  }
  	
  	//To download rejected list
	public function upload()
	 {
		//load the download helper
		$this->load->helper('download');
		$data = file_get_contents(self::REJ_PATH."rejected.xlsx");
		$name = 'rejected.xlsx';
		force_download($name, $data);
	}
    //to check array
	public function emptyArray($array)
	 {
		  $empty = TRUE;
		  if (is_array($array)) {
		    foreach ($array as $value) {
		      if (!$this->emptyArray($value)) {
		        $empty = FALSE;
		      }
		    }
		  }
		  elseif (!empty($array)) {
		    $empty = FALSE;
		  }
		  return $empty;
	}
	
	function import_customer_form()
   {
   	  $data['main_content'] = self::VIEW_FOLDER.'import/import_customer';
	  $this->load->view('layout/template', $data);
   } 
   
   //function to import customer data   

	function import_customer()
	{
		
		$import		= $this->input->post('import');	
		$is_heading	= (isset($import['is_heading'])?$import['is_heading']:0);
		
		   if ($_FILES && $_FILES['import_file']["tmp_name"] !="") 	
		 {
		 			 	
		    $model=self::SET_MODEL;
		 	$pathToUpload=self::CUS_FILE_PATH;
		    $filename=trim(date('Ymd_His').'_data'.'.xlsx');
		 
			if($this->upload_data($filename,$pathToUpload))
			{
				
				$imp_data=$this->$model->import_excel($pathToUpload,$filename,$is_heading);
				   
				if(!empty($imp_data))
				{
					$data=array(
							"xl_data"	 => $imp_data	
						);
						
						  
					$this->parse_customer_data($data);	
				}
                else
                {
					$this->session->set_flashdata('chit_alert', array('message' => 'No data to import','class' => 'danger','title'=>'Import Data'));
					 redirect('settings/import/customer');
				}					
						
			}
			else
			{
				
				$this->session->set_flashdata('chit_alert', array('message' => 'failed reading upload files','class' => 'danger','title'=>'Import Data'));
	                  
	            redirect('settings/import/customer');
				
			}

		 	
		 }
		 else
		 {
				$this->session->set_flashdata('chit_alert', array('message' => 'File not selected','class' => 'success','title'=>'Import Data'));
	                  
	            redirect('settings/import/account');
			
		 }
		 
	}	
	
	function parse_customer_data($data)
	{
		$import_data=array();
		$count_ins_acc=array();
		$count_ins_cus=array();
		$log_model = self::LOG_MODEL;
		$cus_mod = self::CUS_MODEL;
		$this->load->model($cus_mod);
		$acc_model=self::ACC_MODEL;
		$flag=FALSE;
		
		//send the data in an array format
		foreach((array)$data['xl_data']  as $row)
		{
			
			  $records[]=array(
			   	 'firstname'				=>$row['A'],
			   	 'lastname'					=>$row['B'],
			   	 'mobile'					=>$row['C'],
			   	 'email'					=>$row['D'],
			   	 'pan'						=>$row['E'],
			   	 'address1'					=>$row['F'],
			   	 'address2'				    =>$row['G'],
			   	 'address3'				    =>$row['H'],
			   	 'pincode'				    =>$row['I'],
			   	 'city'		                =>$row['J'],
			   	 'state'		            =>$row['K'],
			   	 'country'		            =>$row['L'],
			   	 'nominee_name'			    =>$row['M'],
			   	 'nominee_relationship'		=>$row['N'],
			   	 'nominee_mobile'			=>$row['O'],
				 'nominee_address1'			=>$row['P'],
				 'nominee_address2'			=>$row['Q'],
			   	 'is_new'					=>$row['R'],
			   	 'id_branch'				=>$row['S']
			   	 	 	
			   );
			   
		}
		
       foreach($records as $record)
       {		
        
        $address =  str_replace("_x000D_", "", $record['address1']);
        if (!$this->emptyArray($record))
        {
            $is_cus_exists = $this->is_mobile_exists($record['mobile']);  
            $id_state = $this->$cus_mod->get_state_id($record['state']);
            $id_city = $this->$cus_mod->get_city_id($record['city']);
			//if mobile & email not registered already and refno not exists
			if($is_cus_exists !=TRUE && !empty($record['mobile']) && strlen($record['mobile']) == 10  )
			{
			    $custype=(($record['is_new']=='Yes' || $record['is_new']=='yes')?'Y':'N');
				$import_data["customer"]=array(	     
						 'firstname'	 	=> $record['firstname'],
						 'lastname'	 		=> $record['lastname'],
					   	 'mobile'			=> $record['mobile'],
					   	 'email'			=> $record['email'],
					   	 'pan'				=> $record['pan'],
					   	 'passwd'			=> $this->__encrypt($record['mobile']),
					   	 'date_add'			=> date("Y-m-d H:i:s"),
					   	 'profile_complete' => 0,
					   	 'nominee_name'     => $record['nominee_name'],
					   	 'nominee_relationship' => $record['nominee_relationship'],
					   	 'nominee_mobile'   => $record['nominee_mobile'],
					   	 'id_employee'		=> $this->session->userdata('uid'),
					   	 'id_branch'        => $record['id_branch'],
					   	 'active'			=> 1,
					   	 'added_by'         => 6
					);
				$import_data["address"]=array(	     
						 'address1'	 	=> $address,
						 'address2'	 		=> $record['address2'],
					   	 'address3'			=> $record['address3'],
					   	 'pincode'			=> $record['pincode'],
					   	 'date_add'			=> date("Y-m-d H:i:s"),
					   	 'id_state' =>  $id_state,
					   	 'id_country' => 101,
					   	 'id_city'    => $id_city,
					   	 'id_employee'		=> $this->session->userdata('uid')
					   	 
					);
				//insert customer details	
				$cus_id = $this->$cus_mod->insert_imported_customer($import_data);	
					
		   		
			}else {
                $cus_id   = $this->getCustomerByMobile($record['mobile']);
			    if($cus_id > 0){
                    $import_data["address"] = array(	     
                                                'address1'	 	=> $address,
                                                'address2'	 	=> $record['address2'],
                                                'address3'		=> $record['address3'],
                                                'pincode'		=> $record['pincode'],
                                                'date_add'		=> date("Y-m-d H:i:s"),
                                                'id_state'      => $id_state,
                                                'id_country'    => 101,
                                                'id_city'       => $id_city,
                                                'id_employee'	=> $this->session->userdata('uid')
				                            );
					$id_address = $this->$cus_mod->getAddressId($cus_id);
					if($id_address > 0)
					{
			             $this->$cus_mod->update_address($cus_id,$import_data["address"]);
					}
					else{
					    $this->$cus_mod->insert_address($import_data["address"]);
					}
			    }         
			}
			
			
		}
	
        if($cus_id>0 && $cus_id!=null)
		{
			$count_ins_acc[]=$cus_id;
		}
		else
		{
			 $import_data["invalid"][]=$import_data["valid"];
		}
       }
	   $valid_rows	 = (isset($count_ins_acc)?sizeof($count_ins_acc):0);
	   $invalid_rows = (isset($import_data["invalid"])?sizeof($import_data["invalid"]):0);
	   $totalrows	 = $valid_rows + $invalid_rows;
 	
		
	    if($count_ins_acc != null)
	    {
	        $acc_id =$count_ins_acc;
			//update import log
			$import=array(
				'total'		  => $totalrows,
				'imported'	  => $valid_rows,
				'failed' 	  => $invalid_rows,
				'firstrecord' => reset($acc_id),
				'lastrecord'  => end($acc_id),
				'id_employee' => $this->session->userdata('uid'),
				'import_date' => date('Y-m-d H:i:s')
			);
						
		    $this->update_import_log($import);
		    //for log report
	        $log_data = array(
								'id_log'     => $this->id_log,
								'event_date' => date("Y-m-d H:i:s"),
								'module'     => 'Customer Data Import',
								'operation'  => 'Import',
								'record'     =>  $totalrows,  
								'remark'     => 'Imported '.$valid_rows.' of '.$totalrows. ' Records'
							 );
							 
			$this->$log_model->log_detail('insert','',$log_data);
			$first_id= reset($count_ins_acc);
			$last_id= end($count_ins_acc);
			$this->session->set_flashdata('chit_alert',array('message'=> $valid_rows.' Customers created successfully through import..','class'=>'success','title'=>'Import Data'));
		 	redirect('settings/import/customer_list/'.$first_id.'/'.$last_id);	
			
	    }
		else{
			  	$log_data = array(
									'id_log'     => $this->id_log,
									'event_date' => date("Y-m-d H:i:s"),
									'module'     => 'Customer Import',
									'operation'  => 'Import',
									'record'     => '0',  
									'remark'     => 'Imported '.$valid_rows.' of '.$totalrows. ' Records'
								 );
				$this->$log_model->log_detail('insert','',$log_data);
			 	$this->session->set_flashdata('chit_alert',array('message'=>'Invalid records!Please check the records in Excel..','class'=>'danger','title'=>'Import Data')); 
			 	redirect('settings/import/customer');
		}   
			   
    }
    
    //function to show list import  
   function import_customer_list($lower="",$higher="")
   {
   	  $data['first_id'] = $lower;
   	  $data['last_id']  = $higher;
   	  $data['main_content'] =  self::VIEW_FOLDER.'import/import_customer_list';
	  $this->load->view('layout/template', $data);
   }
   
   //to load import list  
   function ajax_import_customer_list($lower,$higher)
   {
	  $this->load->model('customer_model');
	  $data['imported'] = $this->customer_model->get_customer_range($lower,$higher);
	  echo json_encode($data);
   }
  
  	
   		
}
?>