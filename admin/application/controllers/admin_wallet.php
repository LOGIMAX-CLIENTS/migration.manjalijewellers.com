<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_wallet extends CI_Controller {
	
	const WALL_MODEL = "Wallet_model";
	const CUS_MODEL = "Customer_model";
	const SET_MODEL = "Admin_settings_model";
	const SMS_MODEL = "admin_usersms_model";
	const ADM_MODEL = "chitadmin_model";
	const LOG_MODEL = "log_model";
	const MAIL_MODEL = "email_model";
		
	public function __construct()
    {
        parent::__construct();
        if(!$this->session->userdata('is_logged'))
        {
			redirect('admin/login');
		}
		$this->load->model(self::WALL_MODEL);
		$this->load->model(self::ADM_MODEL);
		$this->load->model(self::SET_MODEL);
		$this->load->model(self::SMS_MODEL);
		$this->load->model(self::LOG_MODEL);
		$this->load->model(self::MAIL_MODEL);
		$this->id_log =  $this->session->userdata('id_log');
	}
	
	//get unallocated customers
	function ajax_get_customers($type)
	{
		$model_name=self::WALL_MODEL;
		$data['customers']=$this->$model_name->get_customers($type);
		$data['setting']=$this->$model_name->get_wallet_setting();
		echo json_encode($data);
	}	

		
	function wallet_setting($type="",$id="")
	{
		$model = self::WALL_MODEL;
		$set_model = self::SET_MODEL;
		$log_model = self::LOG_MODEL;
		switch($type){
			case "List":
					
			    $data['wallet']=$this->$model->wallet_settingDB('get',($id!=NULL?$id:''));    
				$data['main_content'] = "master/wallet/list" ;
	 			$this->load->view('layout/template', $data);   
				break;
			case "View":
			      if($id!=NULL)
			      {
				  	$data['wallet'] = $this->$model->wallet_settingDB('get',$id);
				  }
				  else
				  {
				  	$data['wallet'] = $this->$model->wallet_settingDB();				     	
				  }
				  $data['main_content'] = "master/wallet/form" ;
	 			  $this->load->view('layout/template', $data);  
				break;
			case "Save":
			       //get form values
			       $wallet=$this->input->post('wallet');
			       
			       //formatting form values
			       $insertData=array( 
			                            'name' 			 => (isset($wallet['name']) && $wallet['name']!=''? $wallet['name']:NULL),
			                            'type' 			 => (isset($wallet['type']) && $wallet['type']!=''? $wallet['type']:1),
			                            'currency' 		 => (isset($wallet['currency']) && $wallet['currency']!=''? $wallet['currency']:0),
			                            'value' 		 => (isset($wallet['value']) && $wallet['value']!=''? $wallet['value']:0),
			                            'effective_date' => (isset($wallet['effective_date']) && $wallet['effective_date']!=''? date('Y-m-d H:i:s',strtotime(str_replace("/","-",$wallet['effective_date']))):NULL),
			                            'effect_previous' => (isset($wallet['effect_previous']) && $wallet['effect_previous']!=''? $wallet['effect_previous']:0),
			                            'active'		  => (isset($wallet['active']) && $wallet['active']!=''? $wallet['active']:0)			                         );
			           
			           
			       //inserting data                  
			       $status = $this->$model->wallet_settingDB("insert","",$insertData);
			       
			         if($status['status'])
					{
						$log_data = array(
											'id_log'     => $this->id_log,
											'event_date' => date("Y-m-d H:i:s"),
											'module'     => 'Wallet Setting',
											'operation'  => 'Add',
											'record'     => $status['insertID'],  
											'remark'     => 'Wallet plan added successfully'
										 );
										 
						$this->$log_model->log_detail('insert','',$log_data);				 
						$this->session->set_flashdata('chit_alert', array('message' => 'Wallet plan added successfully','class' => 'success','title'=>'Wallet Master'));
					}else{
					    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Wallet Master'));
					}
					redirect('wallet/master/list');                  
			      break;
			      
			  case "Update":
			        //get form values
			       $wallet=$this->input->post('wallet');
			       
			       //formatting form values
			      $updateData=array( 
			                             'name' 			 => (isset($wallet['name']) && $wallet['name']!=''? $wallet['name']:NULL),
			                            'type' 			 => (isset($wallet['type']) && $wallet['type']!=''? $wallet['type']:1),
			                            'currency' 		 => (isset($wallet['currency']) && $wallet['currency']!=''? $wallet['currency']:0),
			                            'value' 		 => (isset($wallet['value']) && $wallet['value']!=''? $wallet['value']:0),
			                            'effective_date' => (isset($wallet['effective_date']) && $wallet['effective_date']!=''? date('Y-m-d',strtotime(str_replace("/","-",$wallet['effective_date']))):NULL),
			                            'effect_previous' => (isset($wallet['effect_previous']) && $wallet['effect_previous']!=''? $wallet['effect_previous']:0),
			                            'active'		  => (isset($wallet['active']) && $wallet['active']!=''? $wallet['active']:0)
			                         );
			                         
			       //update data                  
			       $status = $this->$model->wallet_settingDB("update",$id,$updateData);
			       
			         if($status)
					{
						$log_data = array(
											'id_log'     => $this->id_log,
											'event_date' => date("Y-m-d H:i:s"),
											'module'     => 'Wallet Setting',
											'operation'  => 'Edit',
											'record'     => $status['updateID'],  
											'remark'     => 'Wallet plan added successfully'
										 );
										 
						$this->$log_model->log_detail('insert','',$log_data);	
						$this->session->set_flashdata('chit_alert', array('message' => 'Wallet plan update successfully','class' => 'success','title'=>'Wallet Master'));
					}else{
					    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Wallet Master'));
					}
					redirect('wallet/master/list');                    
			           
			      break; 
			   case 'Delete':
			 	      $status = $this->$model->wallet_settingDB("delete",$id);
				         if($status)
						{
								$log_data = array(
												'id_log'     => $this->id_log,
												'event_date' => date("Y-m-d H:i:s"),
												'module'     => 'Wallet Setting',
												'operation'  => 'Delete',
												'record'     => $status['deleteID'],  
												'remark'     => 'Wallet plan deleted successfully'
											 );
										 
						     $this->$log_model->log_detail('insert','',$log_data);	
							 $this->session->set_flashdata('chit_alert', array('message' => 'Wallet plan deleted successfully','class' => 'success','title'=>'Wallet Master'));
						}else{
								  $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Wallet Master'));
						}
						redirect('wallet/master/list');  
		 	      break;              	
			
			default:
			     $data['wallet'] = $this->$model->wallet_settingDB('get',($id!=NULL ? $id:''));
			       $data['access'] = $this->$set_model->get_access('wallet/master/list');
			       echo json_encode($data);
				break;
		}
		
	}
	
	function wallet_account($type="",$id="")
	{
		$model = self::WALL_MODEL;
		$set_model = self::SET_MODEL;
		$sms_model =	self::SMS_MODEL;
		$log_model =	self::LOG_MODEL;
		$mail_model=self::MAIL_MODEL;
		switch($type){
			case "List":
			    $data['plan']  = $this->$model->wallet_settingDB('get');					
			    $data['wallet']=$this->$model->wallet_accountDB('get',($id!=NULL?$id:''));    
				$data['main_content'] = "wallet_account/list" ;
	 			$this->load->view('layout/template', $data);   
				break;
			case "View":
			      if($id!=NULL)
			      {
				  	$data['wallet'] = $this->$model->wallet_accountDB('get',$id);
				  }
				  else
				  {
				  	$data['wallet'] = $this->$model->wallet_accountDB();				     	
				  }
				  $data['main_content'] = "wallet_account/form" ;
	 			  $this->load->view('layout/template', $data);  
				break;
			case "Save":
			       //get form values
			       $wallet=$this->input->post('wallet');
			      
			       //formatting form values
			       $insertData=array( 
			                           'id_customer' 	 => (isset($wallet['id_customer']) && $wallet['id_customer']!=''? $wallet['id_customer']:NULL),
									   'idemployee' 	 => (isset($wallet['idemployee']) && $wallet['idemployee']!=''? $wallet['idemployee']:NULL),
			                           'id_employee' 	=>  $this->session->userdata('uid'),
			                           'wallet_acc_number' 			 => (isset($wallet['wallet_acc_number']) && $wallet['wallet_acc_number']!=''? $wallet['wallet_acc_number']:NULL),
			                           'issued_date' 		 => (isset($wallet['issued_date']) && $wallet['issued_date']!=''? date('Y-m-d',strtotime(str_replace("/","-",$wallet['issued_date']))):NULL),
			                           'remark' 		 => (isset($wallet['remark']) && $wallet['remark']!=''? $wallet['remark']:NULL),
			                           'active'		  => (isset($wallet['active']) && $wallet['active']!=''? $wallet['active']:0)			                        
			                         );
			           
			           
			       //inserting data                  
			       $status = $this->$model->wallet_accountDB("insert","",$insertData);
			       $wallAcc = $this->$model->wallet_accountDB("get",$status['insertID']);
			      
			         if($status)
					{
						    $log_data = array(
												'id_log'     => $this->id_log,
												'event_date' => date("Y-m-d H:i:s"),
												'module'     => 'Wallet Account',
												'operation'  => 'Add',
												'record'     => $status['insertID'],  
												'remark'     => 'Wallet Account added successfully'
											 );
										 
						     $this->$log_model->log_detail('insert','',$log_data);
						
						 $serviceID = 8;
			      		 $service =  $this->$set_model->get_service($serviceID);
			       		 $company = $this->$set_model->get_company();
						
							if($service['serv_sms'] == 1)
							{
								$id =$status['insertID'];
								$data =$this->$sms_model->get_SMS_data($serviceID,$id);
								$mobile =$data['mobile'];
								$message = $data['message'];
								$this->send_sms($mobile,$message);
							}
							
							 $email	=   $wallAcc['email'];
							 if($service['serv_email'] == 1  && $email!= '')
								{
									$data['walData'] = $wallAcc;
									$data['company'] = $company;
									$data['type'] = 1;
									$to = $email;
									$subject = "Reg- ".$company['company_name']." saving scheme wallet account creation.";
									$message = $this->load->view('include/emailWallet',$data,true);
									$sendEmail = $this->$mail_model->send_email($to,$subject,$message);
									
								}
							$this->session->set_flashdata('chit_alert', array('message' => 'Wallet account added successfully','class' => 'success','title'=>'Wallet Account'));
					}else{
					    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Wallet Account'));
					}
					redirect('wallet/account/list');                  
			      break;
			      
			  case "Update":
			        //get form values
			       $wallet=$this->input->post('wallet');
			       
			       //formatting form values
			      $updateData=array( 
			                           'id_customer' 	 => (isset($wallet['id_customer']) && $wallet['id_customer']!=''? $wallet['id_customer']:NULL),
			                           'id_employee' 	=>  $this->session->userdata('uid'),
			                           'wallet_acc_number' 			 => (isset($wallet['wallet_acc_number']) && $wallet['wallet_acc_number']!=''? $wallet['wallet_acc_number']:NULL),
			                           'issued_date' 		 => (isset($wallet['issued_date']) && $wallet['issued_date']!=''? date('Y-m-d',strtotime(str_replace("/","-",$wallet['issued_date']))):NULL),
			                           'remark' 		 => (isset($wallet['remark']) && $wallet['remark']!=''? $wallet['remark']:NULL),
			                           'active'		  => (isset($wallet['active']) && $wallet['active']!=''? $wallet['active']:0)			                       
			                         );
			                         
			       //update data                  
			       $status = $this->$model->wallet_accountDB("update",$id,$updateData);
			       
			         if($status)
					{
						 $log_data = array(
												'id_log'     => $this->id_log,
												'event_date' => date("Y-m-d H:i:s"),
												'module'     => 'Wallet Account',
												'operation'  => 'Edit',
												'record'     => $status['updateID'],  
												'remark'     => 'Wallet Account update successfully'
											 );
										 
						$this->$log_model->log_detail('insert','',$log_data);
						
						$this->session->set_flashdata('chit_alert', array('message' => 'Wallet account update successfully','class' => 'success','title'=>'Wallet Account'));
					}else{
					    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Wallet Account'));
					}
					redirect('wallet/account/list');                    
			           
			      break;       
			   case 'Delete':
			 	      $status = $this->$model->wallet_accountDB("delete",$id);
				         if($status)
						{
							 $log_data = array(
												'id_log'     => $this->id_log,
												'event_date' => date("Y-m-d H:i:s"),
												'module'     => 'Wallet Account',
												'operation'  => 'Delete',
												'record'     => $status['deleteID'],  
												'remark'     => 'Wallet Account deleted successfully'
											 );
										 
						    $this->$log_model->log_detail('insert','',$log_data);
						
							 $this->session->set_flashdata('chit_alert', array('message' => 'Wallet account deleted successfully','class' => 'success','title'=>'Wallet Account'));
						}else{
								  $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Wallet Account'));
						}
						redirect('wallet/account/list');  
		 	      break;     	
			
			default:
			      $data['wallet'] = $this->$model->wallet_accountDB('get',($id!=NULL ? $id:''));
			       $data['setting'] = $this->$model->get_wallet_setting();
				   $data['access'] = $this->$set_model->get_access('wallet/account/list');
			       echo json_encode($data);
				break;
		}
		
	}	
	
	
	function wallet_transaction($type="",$id="")
	{
		
		$model = self::WALL_MODEL;
		$set_model = self::SET_MODEL;
		$sms_model = self::SMS_MODEL;
		$log_model = self::LOG_MODEL;
		$mail_model=self::MAIL_MODEL;
		switch($type){
			case "List":					
			    $data['wallet']=$this->$model->wallet_accountDB('get',($id!=NULL?$id:''));    
				$data['main_content'] = "wallet_transaction/list" ;
	 			$this->load->view('layout/template', $data);   
				break;
			case "View":
			      if($id!=NULL)
			      {
				  	$data['wallet'] = $this->$model->wallet_transactionDB('get',$id);
					$data['main_content'] = "wallet_transaction/edit" ;
				  }
				  else
				  {
				  	$data['wallet'] = $this->$model->wallet_transactionDB();
					$data['main_content'] = "wallet_transaction/form" ;	
				  }
				 
	 			  $this->load->view('layout/template', $data);  
				break;
			case "Save":
			       //get form values
			       $wallet=$this->input->post('wallet');
			   
			       //formatting form values
			       $insertData=array( 
			                            'id_wallet_account' => (isset($wallet['id_wallet_account']) && $wallet['id_wallet_account']!=''? $wallet['id_wallet_account']:NULL),
			                            'id_employee' 	=>  $this->session->userdata('uid'),
			                            'transaction_type' 	=> (isset($wallet['transaction_type']) && $wallet['transaction_type']!=''? $wallet['transaction_type']:1),
			                            'value' 		    => (isset($wallet['value']) && $wallet['value']!=''? $wallet['value']:0),
			                            'date_transaction'  => (isset($wallet['date_transaction']) && $wallet['date_transaction']!=''? date('Y-m-d H:i:s',strtotime(str_replace("/","-",$wallet['date_transaction']))):NULL),
			                            'description'       => (isset($wallet['description']) && $wallet['description']!=''? $wallet['description']:NULL)
			                         );
			           
			           
			       //inserting data                  
			       $status = $this->$model->wallet_transactionDB("insert","",$insertData);
			       $wallTrans= $this->$model->wallet_transactionDB("get",$status['insertID']);
			       
			         if($status)
					{	
					     $log_data = array(
												'id_log'     => $this->id_log,
												'event_date' => date("Y-m-d H:i:s"),
												'module'     => 'Wallet Transaction',
												'operation'  => 'Add',
												'record'     => $status['insertID'],  
												'remark'     => 'Wallet Transaction added successfully'
											 );
										 
						    $this->$log_model->log_detail('insert','',$log_data);
					
					
						 $serviceID = 9;
			      		 $service =  $this->$set_model->get_service($serviceID);
			      		 $company = $this->$set_model->get_company();
							if($service['serv_sms'] == 1)
							{
								$id = $insertData['id_wallet_account'];
								$data =$this->$sms_model->get_SMS_data($serviceID,$id);
								$mobile =$data['mobile'];
								$message = $data['message'];
								$this->send_sms($mobile,$message);
							}
							
							 $email	=   $wallTrans['email'];
							 if($service['serv_email'] == 1  && $email!= '')
								{
									$data['walData'] = $wallTrans;
									$data['company'] = $company;
									$data['type'] = 2;
									$to = $email;
									$subject = "Reg- ".$company['company_name']." saving scheme wallet transaction";
									$message = $this->load->view('include/emailWallet',$data,true);
									$sendEmail = $this->$mail_model->send_email($to,$subject,$message);
									
								}
							$this->session->set_flashdata('chit_alert', array('message' => 'Wallet transaction added successfully','class' => 'success','title'=>'Wallet Transaction'));
					}else{
					    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Wallet Transaction'));
					}
					redirect('wallet/transaction/list');                  
			      break;
			      
			  case "Update":
			        //get form values
			       $wallet=$this->input->post('wallet');
			   
			    
			       //formatting form values
			      $updateData=array(   
			                            'id_wallet_account' => (isset($wallet['id_wallet_account']) && $wallet['id_wallet_account']!=''? $wallet['id_wallet_account']:NULL),
			                            'id_employee' 	=>  $this->session->userdata('uid'),
			                            'transaction_type' 	=> (isset($wallet['transaction_type']) && $wallet['transaction_type']!=''? $wallet['transaction_type']:1),
			                            'value' 		    => (isset($wallet['value']) && $wallet['value']!=''? $wallet['value']:0),
			                            'date_transaction'  => (isset($wallet['date_transaction']) && $wallet['date_transaction']!=''? date('Y-m-d H:i:s',strtotime(str_replace("/","-",$wallet['date_transaction']))):NULL),
			                            'description'       => (isset($wallet['description']) && $wallet['description']!=''? $wallet['description']:NULL)          
			                         );
			                         
			       //update data                  
			       $status = $this->$model->wallet_transactionDB("update",$id,$updateData);
			       
			         if($status)
					{
						 $log_data = array(
												'id_log'     => $this->id_log,
												'event_date' => date("Y-m-d H:i:s"),
												'module'     => 'Wallet Transaction',
												'operation'  => 'Edit',
												'record'     => $status['updateID'],  
												'remark'     => 'Wallet Transaction updated successfully'
											 );
										 
						    $this->$log_model->log_detail('insert','',$log_data);
						
						/* $serviceID = 9;
			      		 $service =  $this->$set_model->get_service($serviceID);
							if($service['serv_sms'] == 1)
							{
								$id =$status['updateID'];
								$data =$this->$sms_model->get_SMS_data($serviceID,$id);
								$mobile =$data['mobile'];
								$message = $data['message'];
								$this->send_sms($mobile,$message);
								
							}*/
							
						$this->session->set_flashdata('chit_alert', array('message' => 'Wallet transaction update successfully','class' => 'success','title'=>'Wallet Transaction'));
					}else{
					    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Wallet Transaction'));
					}
					redirect('wallet/transaction/list');                    
			           
			      break; 
			 case 'Delete':
				 	      $status = $this->$model->wallet_transactionDB("delete",$id);
					         if($status)
							{
								$log_data = array(
												'id_log'     => $this->id_log,
												'event_date' => date("Y-m-d H:i:s"),
												'module'     => 'Wallet Transaction',
												'operation'  => 'Delete',
												'record'     => $status['deleteID'],  
												'remark'     => 'Wallet Transaction deleted successfully'
											 );
										 
						         $this->$log_model->log_detail('insert','',$log_data);
								 $this->session->set_flashdata('chit_alert', array('message' => 'Wallet transaction deleted successfully','class' => 'success','title'=>'Wallet Transaction'));
							}else{
									  $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Wallet Transaction'));
							}
							redirect('wallet/transaction/list');  
			 	break;          	
			
			default:
					
					$from_date=$this->input->post('from_date');
					$to_date=$this->input->post('to_date');
					$type=$this->input->post('type');
					
					
					
			        $data['wallet'] = $this->$model->wallet_transactionDB_by_range($from_date,$to_date,$type);				 					
			       $data['setting']=$this->$model->get_wallet_setting();				   				   
				     $data['access'] = $this->$set_model->get_access('wallet/transaction/list');
			       echo json_encode($data);
				break;
		}
		
	}
	
	function send_sms($mobile,$message)
	{	
		
		$model = self::ADM_MODEL;
		$otp_promotion=1;		 
		if($this->config->item('sms_gateway') == '1'){
			 $this->sms_model->sendSMS_MSG91($mobile,$message,$otp_promotion);		
		}
		elseif($this->config->item('sms_gateway') == '2'){
			 $this->sms_model->sendSMS_Nettyfish($mobile,$message,'promo');	
		}
		elseif($this->config->item('sms_gateway') == '3'){
			 $this->sms_model->sendSMS_SpearUC($mobile,$message,'');	
		}
		elseif($this->config->item('sms_gateway') == '4'){
			$this->sms_model->sendSMS_Asterixt($mobile,$message,'');
		}
		elseif($this->config->item('sms_gateway') == '5'){
			$this->sms_model->sendSMS_Qikberry($mobile,$message,'');
		}
	}	
	
	// referral code employee //
	
	
	function ajax_get_employee($type)
	{
		$model_name=self::WALL_MODEL;
		$data['employee']=$this->$model_name->get_employee($type);
		$data['setting']=$this->$model_name->get_wallet_setting();
		echo json_encode($data);
	}
	
	
    // wallet catogery list //


	function wallet_category($type="",$id="")
	{
		
		$model = self::WALL_MODEL;
		$set_model = self::SET_MODEL;
		$log_model = self::LOG_MODEL;
		switch($type){
			case "List":
					
			    $data['wallet']=$this->$model->walletcategory_settingDB('get',($id!=NULL?$id:''));    
				$data['main_content'] = "master/wallet_category/list" ;
	 			$this->load->view('layout/template', $data);   
				break;
			case "View":
			      if($id!=NULL)
			      {
				  	$data['wallet'] = $this->$model->walletcategory_settingDB('get',$id);
				  }
				  else
				  {
				  	$data['wallet'] = $this->$model->walletcategory_settingDB();				     	
				  }
				  $data['main_content'] = "master/wallet_category/form" ;
	 			  $this->load->view('layout/template', $data);  
				break;
			case "Save":
			       //get form values
			       $wallet=$this->input->post('wallet');
			       
			       //formatting form values
			       $insertData=array( 
			                            'code' 			 => (isset($wallet['code']) && $wallet['code']!=''? $wallet['code']:NULL),
			                            'name' 			 => (isset($wallet['name']) && $wallet['name']!=''? $wallet['name']:NULL),
			                            'date_add'       => (isset($wallet['date_add']) && $wallet['date_add']!=''? date('Y-m-d',strtotime(str_replace("/","-",$wallet['date_add']))):NULL),
			                            'date_upd'       => date("Y-m-d H:i:s"),
			                            'active'		 => (isset($wallet['active']) && $wallet['active']!=''? $wallet['active']:0)
									);
			           
			           
			       //inserting data                  
			        $status = $this->$model->walletcategory_settingDB("insert","",$insertData);
			       
			         if($status['status'])
					{
						$log_data = array(
											'id_log'     => $this->id_log,
											'event_date' => date("Y-m-d H:i:s"),
											'module'     => 'Wallet Category',
											'operation'  => 'Add',
											'record'     => $status['insertID'],  
											'remark'     => 'Wallet Category added successfully'
										 );
										 
										 
							  $insdata = array(							  
							  'id_category' => $status['insertID'],							  
							  'date_add'       => (isset($wallet['date_add']) && $wallet['date_add']!=''? date('Y-m-d',strtotime(str_replace("/","-",$wallet['date_add']))):NULL));
								
							 $this->$model->wallet_category_settingsDB("insert","",$insdata);	
										 
						$this->$log_model->log_detail('insert','',$log_data);				 
						$this->session->set_flashdata('chit_alert', array('message' => 'Wallet Category successfully','class' => 'success','title'=>' Wallet Category'));
					}else{
					    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Wallet Category'));
					}
					redirect('wallet/category/list');                  
			      break;
			      
			  case "Update":
			        //get form values
			       $wallet=$this->input->post('wallet');
				 
			       //formatting form values
			      $updateData=array( 
			                            'code' 			 => (isset($wallet['code']) && $wallet['code']!=''? $wallet['code']:NULL),
			                            'name' 			 => (isset($wallet['name']) && $wallet['name']!=''? $wallet['name']:NULL),
			                            'date_add'       => (isset($wallet['date_add']) && $wallet['date_add']!=''? date('Y-m-d',strtotime(str_replace("/","-",$wallet['date_add']))):NULL),
			                            'date_upd'       => date("Y-m-d H:i:s"),
			                            'active'		 => (isset($wallet['active']) && $wallet['active']!=''? $wallet['active']:0)
			                         );
			                         
			       //update data                  
			       $status = $this->$model->walletcategory_settingDB("update",$id,$updateData);
			       
			         if($status)
					{
						$log_data = array(
											'id_log'     => $this->id_log,
											'event_date' => date("Y-m-d H:i:s"),
											'module'     => 'Wallet Setting',
											'operation'  => 'Edit',
											'record'     => $status['updateID'],  
											'remark'     => 'Wallet Category Edit successfully'
										 );
										 
						$this->$log_model->log_detail('insert','',$log_data);	
						$this->session->set_flashdata('chit_alert', array('message' => 'Wallet Category Update successfully','class' => 'success','title'=>'Wallet Category'));
					}else{
					    $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Wallet Category'));
					}
					redirect('wallet/category/list');                    
			           
			      break; 
			   case 'Delete':			   
			 	      $status = $this->$model->walletcategory_settingDB("delete",$id);
				         if($status)
						{
								$log_data = array(
												'id_log'     => $this->id_log,
												'event_date' => date("Y-m-d H:i:s"),
												'module'     => 'Wallet Category Setting',
												'operation'  => 'Delete',
												'record'     => $status['deleteID'],  
												'remark'     => 'Wallet Category Delete successfully'
											 );
										 
						     $this->$log_model->log_detail('insert','',$log_data);	
							 $this->session->set_flashdata('chit_alert', array('message' => 'Wallet plan deleted successfully','class' => 'danger','title'=>'Wallet Master'));
						}else{
								  $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Wallet Master'));
						}
						redirect('wallet/category/list');     
		 	      break;              	
			
			default:
			     $data['wallet'] = $this->$model->walletcategory_settingDB('get',($id!=NULL ? $id:''));
			       $data['access'] = $this->$set_model->get_access('wallet/category/list');
			       echo json_encode($data);
				break;
		}
		
	}
	
	
	function walletcategory_status($status,$id)
	{
		$data = array('active' => $status);
		$model=self::WALL_MODEL;
		$status = $this->$model->walletcategory($data,$id);
		if($status)
		{
			$this->session->set_flashdata('chit_alert',array('message'=>'Wallet Category status updated as '.($status ? 'active' : 'inactive').' successfully.','class'=>'success','title'=>'Wallet Category Status'));			
		}	
		else
		{
			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Wallet Category Status'));
		}	
		redirect('wallet/category/list');
	}

	function wallet_category_settings($type="",$id="")
	{
		
		$model = self::WALL_MODEL;
		$set_model = self::SET_MODEL;
		$log_model = self::LOG_MODEL;
		
		switch($type){
			case "List":
					
			    $data['wallet']=$this->$model->wallet_category_settingsDB('get',($id!=NULL?$id:''));    
				$data['main_content'] = "master/walletcategory_setting/list" ;
				$data['access']= $this->$set_model->get_access('wallet/category/setting/list');
				$this->load->view('layout/template', $data);   
				break;
			case "View":
			      if($id!=NULL)
			      {
				  	$data['wallet'] = $this->$model->wallet_category_settingsDB('get',$id);
				  }
				  else
				  {
				  	$data['wallet'] = $this->$model->wallet_category_settingsDB();				     	
				  }
				  $data['main_content'] = "master/walletcategory_setting/form" ;
	 			  $this->load->view('layout/template', $data);  
				break;			      
			  case "Update":
			        //get form values
			               $data=$this->input->post('wallet_categoryset');				  
						   $total_records=array();
				  
							if(!empty($data) && count($data)>0 && $data!=NULL)
							{
								$this->db->trans_begin();
								
								foreach($data as $wallet){
									
								 $id=$wallet['id_wcat_settings'];
									
								 $updateData=array( 
											'value' 			 => (isset($wallet['value']) && $wallet['value']!=''? $wallet['value']:NULL),
											'point' 	 => (isset($wallet['point']) && $wallet['point']!=''? $wallet['point']:NULL),
											'redeem_percent' 	 => (isset($wallet['redeem_percent']) && $wallet['redeem_percent']!=''? $wallet['redeem_percent']:NULL),
											'remark' 	 => (isset($wallet['remark']) && $wallet['remark']!=''? $wallet['remark']:NULL),
											'last_update'       => date("Y-m-d H:i:s")
										    );
								    $status = $this->$model->wallet_category_settingsDB("update",$id,$updateData);
									$total_records[]=$status;
								}   
			       //update data                  
			       	$total_rows	 = (isset($total_records)?sizeof($total_records):0);   
			         if($this->db->trans_status()===TRUE && $total_rows>0)
					 {
					   $this->db->trans_commit();
				
						$log_data = array(
											'id_log'     => $this->id_log,
											'event_date' => date("Y-m-d H:i:s"),
											'module'     => 'Wallet Category Setting',
											'operation'  => 'Update',
											'record'     =>  count($total_rows),  
											'remark'     => 'Wallet Category Setting Update successfully'
										 );
										 
						$this->$log_model->log_detail('insert','',$log_data);	
						$this->session->set_flashdata('chit_alert', array('message' => 'Wallet Category Setting Update successfully','class' => 'success','title'=>'Wallet Category'));
					}else{
						 $this->db->trans_rollback();
						$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation','class' => 'danger','title'=>'Wallet Category'));
					 }
				   }else {
						  $this->session->set_flashdata('chit_alert',array('message'=> 'Unable to proceed the requested operation...','class'=>'danger','title'=>'Wallet Category Setting '));
					      }
					  redirect('wallet/category/setting/list');
			      break; 
				 default:
			     $data['wallet'] = $this->$model->wallet_category_settingsDB('get',($id!=NULL ? $id:''));
			     $data['access'] = $this->$set_model->get_access('wallet/category/list');
			     echo json_encode($data);
				break;
		}
		
	}	
	
	
	function wallet_categorysett_status($status,$id)
	{
		$data = array('active' => $status);
		$model=self::WALL_MODEL;
		$status = $this->$model->walletcategory_setting_status($data,$id);
		if($status)
		{
			$this->session->set_flashdata('chit_alert',array('message'=>'Wallet Category status updated as '.($status ? 'active' : 'inactive').' successfully.','class'=>'success','title'=>'Wallet Category  Setting Status'));
			redirect('wallet/category/setting/list');   	
		}	
		else
		{
			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Wallet Category Status'));
			redirect('wallet/category/setting/list');   
		}
	}
	
	

	
}

?>