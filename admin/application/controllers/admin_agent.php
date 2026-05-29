<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_agent extends CI_Controller
{
    const AGENT_MODEL = 'agent_model';
	const LOG_MODEL = "log_model";
	const MAIL_MODEL = "email_model";
	const AGENT_VIEW  = "agent/";
	const SET_MODEL = 'admin_settings_model';
	function __construct()   
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->model(self::AGENT_MODEL);
		$this->load->model(self::LOG_MODEL);
		$this->load->model(self::MAIL_MODEL);
		$this->load->model("sms_model");
		$this->employee =  $this->session->userdata('uid');
		$this->company = $this->admin_settings_model->get_company();
		$this->branch_settings =  $this->session->userdata('branch_settings');
		if(!$this->session->userdata('is_logged'))
		{
			redirect('admin/login');
		}	
		$this->id_log =  $this->session->userdata('id_log');
	}
	
	
	function index(){
	    $this->agent_list();
	}
	
	function agent_settlement_list()
	{
		 $data['main_content'] = self::AGENT_VIEW."verify_settlement" ;
		 $this->load->view('layout/template', $data); 
	}
	
	function ajax_onlineSettlements()
	{      
	      	$model =	self::AGENT_MODEL;
		 
		  		if(!empty($_POST['from_date']))
		  	{
				$range['from_date']  = $this->input->post('from_date');
				$range['to_date']  = $this->input->post('to_date');
				$range['type']  = $this->input->post('type');
				$range['limit']  = $this->input->post('limit');
				$date_type  = $this->input->post('date_type');
				$range['settle']  = $this->input->post('settle');
				$data['data']=$this->$model->approval_range($range['from_date'],$range['to_date'],$range['limit'],$date_type,$range['settle']);
			}
			else
			{
               $range['settle']  = $this->input->post('settle');
				//$data['data']=$this->$model->onlineApprovals('',$range['settle']);
			}
					echo json_encode($data);
	}
	
	
	public function agent_report($type,$msg = "")
	{
		$model        	= self::AGENT_MODEL;
		$setting_model 	= self::SET_MODEL;
		if($type == "list")
		{
			$data['main_content'] = "agent/report/referral_report";
			$data['message']      = $msg;
			$this->load->view('layout/template', $data);
		}
		else if($type == "list_ajax"){
			$listData   	= $this->$model->get_agents($_POST);
			$success 		= true;
			$message 		= "Record retrived successfully";
			$access 		= $this->$setting_model->get_access('admin_agent/agent_report/list');
			$resultArr 		= array(
								'list' 		=> $listData,
								'access'	=> $access,
								"success" 	=> $success, 
								"message" 	=> $message, 
								"title" 	=> "", 
								"class" 	=> "",
								); 
			echo json_encode($resultArr);
		}
		else if($type == "summary")
		{
			$data['main_content'] = "agent/report/summary";
			$data['message']      = $msg;
			$this->load->view('layout/template', $data);
		}
		else if($type == "ajax"){
			$listData   	= $this->$model->get_agent_details($_POST);
			$success 		= true;
			$message 		= "Record retrived successfully";
			$access 		= $this->$setting_model->get_access('admin_agent/agent_report/summary');
			$resultArr 		= array(
								'list' 		=> $listData,
								'access'	=> $access,
								"success" 	=> $success, 
								"message" 	=> $message, 
								"title" 	=> "", 
								"class" 	=> "",
								); 
			echo json_encode($resultArr);
		}
		else if($type == "updprofile"){
		    $postData = $_POST['data'];
		    $data   = array(
		                    'preferred_mode'     => $postData['payment_mode'],
		                    'bank_account_number'   => $postData['bank_acc_no'],
		                    'ifsc_code'         => $postData['bank_ifsc'],
		                    'bank_name'         => $postData['bank_name'],
		                    'date_upd' 	        => date("Y-m-d H:i:s")
		                    );
		                   // print_r($data);exit;
			$status = $this->$model->updateData($data,'id_agent',$postData['id_agent'],'agent');
			//print_r($this->db->last_query());exit;
			if($status){
			    $resultArr 		= array(
								"message" 	=> "Profile updated successfully", 
								"status" 	=> true
								); 
			}else{
			    $resultArr 		= array(
								"message" 	=> "Error in updating profile", 
								"status" 	=> false
								); 
			}
			
			echo json_encode($resultArr);
		}
	} 
	
	
	function update_status() 
	{
		$model      = self::AGENT_MODEL;
		
		$status   = $this->input->post('settle_status');
		$ids    = $this->input->post('settle_id');
		$transData  = array();
		if(!empty($ids) && count($ids)>0 && $status!=NULL)
		{
			$pay_status = array('request_status'=>$status); 
			$ischkref=FALSE;
			foreach($ids as $id_settle)
			{
				$update	= $this->$model->update_settlement_status($id_settle,$pay_status);
			}
			$this->session->set_flashdata('chit_alert',array('message'=> count($ids).' Settlement record updated as successfully...','class'=>'success','title'=>'Settlement Approval'));	
		}
		else
		{
			$this->session->set_flashdata('chit_alert',array('message'=> 'Unable to proceed the requested operation...','class'=>'danger','title'=>'Settlement Approval'));
		}	
	}
	
	
	public function agent_list($msg="")
	{
		$model_name=self::AGENT_MODEL;
		
		$data['message']=$msg;

		$data['main_content'] = self::AGENT_VIEW.'list';
		$data['entry_date']=$this->admin_settings_model->settingsDB('get','','');

	    $this->load->view('layout/template', $data);
	}
	
	function agent_status($status,$id)
	{
		$data = array('active' => $status);
		$model=self::AGENT_MODEL;
		$status = $this->$model->update_agent_only($data,$id);
		if($status)
		{
			$this->session->set_flashdata('chit_alert',array('message'=>'Customer status updated as '.($status ? 'active' : 'inactive').' successfully.','class'=>'success','title'=>'Customer Status'));			
		}	
		else
		{
			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Customer Status'));
		}	
		redirect('agent');
	}
	
	public function ajax_agents()
	{
		$model_name=self::AGENT_MODEL;
		$set_model=self::SET_MODEL;
		$data['access'] = $this->$set_model->get_access('agent');
		$range['from_date']  = $this->input->post('from_date');
		$range['to_date']  = $this->input->post('to_date');
		$range['id_branch']  = $this->input->post('id_branch');
		$range['id_village']  = $this->input->post('id_village');
		
		
		if($range['from_date']!='' && $range['to_date']!='')
		{
			$data['agent']=$this->$model_name->get_agents_by_date($range['from_date'],$range['to_date'],$range['id_branch']);
			//print_r($data);exit;
		}
		else
		{
			$data['agent']=$this->$model_name->get_all_agents('',$range['id_branch'],$range['id_village']); 
		}

		
		echo json_encode($data);
	}
	
	public function agent_form($type="",$id="")
	{
		$model=self::AGENT_MODEL;
		switch($type)
		{
			case 'Add':
				$set_model=self::SET_MODEL;
				$limit= $this->$set_model->limitDB('get','1');
				$count= $this->$model->agent_count();
						//print_r($count);exit;
				if($limit['limit_cust']==1){
					if($count < $limit['cust_max_count']){
						$data['agent']= $this->$model->empty_record();
						/*$data['customer']['cus_img_path']	= self::CUS_IMG_PATH."../default.png";
						$data['customer']['pan_path']		= self::CUS_IMG_PATH."../no_image.png";
						$data['customer']['voterid_path']	= self::CUS_IMG_PATH."../no_image.png";
						$data['customer']['rationcard_path'] = self::CUS_IMG_PATH."../no_image.png";*/
						$data['agent'] ['image' ]       =NULL; 
						$data['agent'] ['age' ]     		=NULL; 
						$data['main_content'] = self::AGENT_VIEW.'form' ;
						$this->load->view('layout/template', $data);
					}else{
						$this->session->set_flashdata('chit_alert',array('message'=>'Agent creation limit exceeded, Kindly contact Super Admin...','class'=>'danger','title'=>'Agent creation'));
						redirect('agent');
					}
				}else{
					$data['agent']= $this->$model->empty_record();
					/*$data['agent']['cus_img_path']	= self::CUS_IMG_PATH."../default.png";
					$data['agent']['pan_path']		= self::CUS_IMG_PATH."../no_image.png";
					$data['agent']['voterid_path']	= self::CUS_IMG_PATH."../no_image.png";
					$data['agent']['rationcard_path'] = self::CUS_IMG_PATH."../no_image.png";  */
					$data['agent'] ['image' ]       =NULL;
					$data['agent'] ['age' ]     		=NULL; 
					$data['main_content'] = self::AGENT_VIEW."form" ;
					$this->load->view('layout/template', $data);
				}

			break;
			case 'Edit':
				   $cus= $this->$model->get_agent($id);
				 //echo "<pre>";print_r($cus);echo "</pre>";exit; 
				   
				   $data['agent']= array(
					   			'id_agent'		=>  (isset($cus['id_agent'])?$cus['id_agent']: NULL), 
					   			'firstname'			=>  (isset($cus['firstname'])?$cus['firstname']: NULL), 
				       			'lastname' 			=>  (isset($cus['lastname'])?$cus['lastname']: NULL), 
								'id_branch'		=>  (isset($cus['id_branch'])?$cus['id_branch']: NULL), 
								'religion'		=>  (isset($cus['religion'])?$cus['religion']: NULL), 
                                'id_village'		=>  (isset($cus['id_village'])?$cus['id_village']: NULL),
								'date_of_birth'		=>	(isset($cus['date_of_birth']) && $cus['date_of_birth'] != '' ? date('d/m/Y',strtotime(str_replace("/","-",$cus['date_of_birth']))): NULL), 	
								'date_of_wed'		=>	(isset($cus['date_of_wed']) && $cus['date_of_wed'] != '' ? date('d/m/Y',strtotime(str_replace("/","-",$cus['date_of_wed']))): NULL), 
								'email'				=>	(isset($cus['email'])?$cus['email']: NULL), 
								'gender'			=>	(isset($cus['gender'])?$cus['gender']: NULL),
								'mobile'			=>	(isset($cus['mobile'])?$cus['mobile']: NULL), 
								'phone'				=>	(isset($cus['phone'])?$cus['phone']: NULL),
								'title'				=>	(isset($cus['title'])?$cus['title']: NULL), 
								'passwd'			=>	(isset($cus['passwd'])?$cus['passwd']: NULL), 
								'active'			=>	(isset($cus['active'])?$cus['active']: 0),
								'id_country'		=>	(isset($cus['id_country'])?$cus['id_country']:0),
							    'id_state' 			=>	(isset($cus['id_state'])?$cus['id_state']:0),
								'id_city'			=>	(isset($cus['id_city'])?$cus['id_city']:0),			
								'company_name'		=>	(isset($cus['company_name'])?$cus['company_name']:NULL),
								'address1'			=>	(isset($cus['address1'])?$cus['address1']:NULL),
								'address2'			=>	(isset($cus['address2'])?$cus['address2']:NULL),
								'address3'			=>	(isset($cus['address3'])?$cus['address3']:NULL),
								'pincode'			=>	(isset($cus['pincode'])?$cus['pincode']:NULL),
								'image'           =>  (isset($cus['image'])?$cus['image']:NULL)
					);
					//print_r($data);exit;
			       $data['main_content'] = self::AGENT_VIEW."form" ;
				   $this->load->view('layout/template', $data);
				break;
			
		}
	}
	
		//db transactions
    public function agent_post($type="",$id="")
	{

		$model=self::AGENT_MODEL;
		$setmodel=self::SET_MODEL;
		switch($type)
		{
			case 'Add':
			       $cus = $this->input->post('customer');
				   $filename = $_FILES['cus_img']['name'];
				   $imgpath = base_url().'assets/img/agent/'.$filename;
				   //print_r($imgpath);exit;  // Array ( [firstname] => Abinaya [lastname] => desc [mobile] => 09944366316 [email] => abi@gmail.com [phone] => 9632587410 [passwd] => 123123123 [address1] => ecdcedcfe [address2] => edcdcvdvc [address3] => fcdcdcvdvd [id_village] => [pincode] => 641021 [religion] => [customer_img] => [gender] => 1 [date_of_birth] => [date_of_wed] => [nominee_name] => [nominee_relationship] => [nominee_mobile] => [company_name] => [gst_number] => [pan] => [voterid] => [rationcard] => [pan_img] => [voter_img] => [ration_img] => [comments] => )
			       $cus_data = array(
			       		'info'=>array(
			       			'firstname'			=>  (isset($cus['firstname'])?ucfirst($cus['firstname']): NULL), 
			       			'lastname' 			=>  (isset($cus['lastname'])?ucfirst($cus['lastname']): NULL),
							'id_branch'	    	=>  (isset($cus['id_branch'])?$cus['id_branch']: NULL), 
							'id_village'		=>  ($cus['id_village']!='' ?$cus['id_village'] :NULL), 
							'date_of_birth'		=>	(isset($cus['date_of_birth']) && $cus['date_of_birth']!=''? date('Y-m-d',strtotime(str_replace("/","-",$cus['date_of_birth']))): NULL), 	
							'date_of_wed'		=>	(isset($cus['date_of_wed']) && $cus['date_of_wed']!=''? date('Y-m-d',strtotime(str_replace("/","-",$cus['date_of_wed']))): NULL),  
							///'gst_number'		=>	(isset($cus['gst_number'])?$cus['gst_number']: NULL), 
							'email'				=>	(isset($cus['email'])?$cus['email']: NULL), 
							'mobile'			=>	(isset($cus['mobile'])?$cus['mobile']: NULL),
							'agent_code'			=>	(isset($cus['mobile'])?$cus['mobile']: NULL),
							'gender'			=>	(isset($cus['gender'])?$cus['gender']: '-1'),
							'phone'				=>	(isset($cus['phone'])?$cus['phone']: NULL), 
							//'image'			=>	 $imgpath, 	
							//'comments'			=>	(isset($cus['comments'])?$cus['comments']: NULL), 	
							'passwd'			=>	(isset($cus['passwd'])?$this->$model->__encrypt($cus['passwd']): NULL), 
							'active'			=>	(isset($cus['active'])?$cus['active']: 1),
							'date_add'			=>   date("Y-m-d H:i:s") ,
							'added_by'			=> 1
			       		),
			       		
			       		'address'				=>array(
			       			'id_country'		=>	(isset($cus['country'])?$cus['country']:NULL),
							'id_state' 			=>	(isset($cus['state'])?$cus['state']:NULL),
							'id_city'			=>	(isset($cus['city'])?$cus['city']:NULL),					
							'company_name'		=>	(isset($cus['company_name'])?$cus['company_name']:NULL),
							'address1'			=>	(isset($cus['address1'])?$cus['address1']:NULL),
							'address2'			=>	(isset($cus['address2'])?$cus['address2']:NULL),
							'address3'			=>	(isset($cus['address3'])?$cus['address3']:NULL),
							'pincode'			=>	(isset($cus['pincode'])?$cus['pincode']:NULL),	
							'active'			=>	(isset($cus['active'])?$cus['active']: 0),				'date_add'			=>	date("Y-m-d H:i:s")
			       		) 
			       );
			       

                   $this->db->trans_begin();
                   $cus_id=  $this->$model->insert_agent($cus_data);

				   if($this->db->trans_status()===TRUE)
	               {
				 	 $this->db->trans_commit();
				 	
				 	 $this->session->set_flashdata('chit_alert',array('message'=>'Agent record added successfully','class'=>'success','title'=>'Create Agent'));
				 	 redirect('agent');
				   }
				   else
				   {
                        $this->db->trans_rollback();						 	
                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Create Agent'));
                        redirect('agent');
				   }
				break;
			case 'Edit':
				 $cus=$this->input->post('customer');
		         $pwd_check=$this->$model->check_password($id,$cus['passwd']); 
			     $cus_data = array(
			       		'info' => array(
			       			'firstname'			=>  (isset($cus['firstname'])?$cus['firstname']: NULL), 
			       			'lastname' 			=>  (isset($cus['lastname'])?$cus['lastname']: NULL), 
							'id_branch'		=>  (isset($cus['id_branch'])?$cus['id_branch']: NULL), 
							'religion'		=>  (isset($cus['religion'])?$cus['religion']: NULL), 
						    'id_village'		=>  ($cus['id_village']!='' ?$cus['id_village'] :NULL), 
							'date_of_birth'		=>	(isset($cus['date_of_birth']) && $cus['date_of_birth']!=''? date('Y-m-d',strtotime(str_replace("/","-",$cus['date_of_birth']))): NULL), 	
							'date_of_wed'		=>	(isset($cus['date_of_wed']) && $cus['date_of_wed']!=''? date('Y-m-d',strtotime(str_replace("/","-",$cus['date_of_wed']))): NULL),  
							'email'				=>	(isset($cus['email'])?$cus['email']: NULL), 
							'mobile'			=>	(isset($cus['mobile'])?$cus['mobile']: NULL), 
							'agent_code'			=>	(isset($cus['mobile'])?$cus['mobile']: NULL),
							'gender'			=>	(isset($cus['gender'])?$cus['gender']: '-1'), 
							'phone'				=>	(isset($cus['phone'])?$cus['phone']: NULL),
							'passwd'			=>	(isset($cus['passwd'])?($pwd_check == TRUE ? $cus['passwd']:$this->$model->__encrypt($cus['passwd'])): NULL), 
							'active'			=>	(isset($cus['active'])?$cus['active']: 0),
							'date_upd'			=>   date("Y-m-d H:i:s")
			       		),
			       		
							'address'			=> array(
			       			'id_country'		=>	(isset($cus['country'])?$cus['country']:NULL),
							'id_state' 			=>	(isset($cus['state'])?$cus['state']:NULL),
							'id_city'			=>	(isset($cus['city'])?$cus['city']:NULL),
							'company_name'		=>	(isset($cus['company_name'])?$cus['company_name']:NULL),
							'address1'			=>	(isset($cus['address1'])?$cus['address1']:NULL),
							'address2'			=>	(isset($cus['address2'])?$cus['address2']:NULL),
							'address3'			=>	(isset($cus['address3'])?$cus['address3']:NULL),
							'pincode'			=>	(isset($cus['pincode'])?$cus['pincode']:NULL),	
							'active'			=>	(isset($cus['active'])?$cus['active']: 0),							
							'date_upd'			=>	date("Y-m-d H:i:s")
			       		)
			       );
			              
                    $this->db->trans_begin();
                    
                    $cus_id = $this->$model->update_agent($cus_data,$id);
                   // print_r($cus_id);exit;
                    if($this->db->trans_status()===TRUE)
                    {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('chit_alert',array('message'=>'Agent record modified successfully','class'=>'success','title'=>'Edit Agent'));
                        redirect('agent');
                    }
                    else
                    {
                        $this->db->trans_rollback();						 	
                        $this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Edit Agent'));
                        redirect('agent');
                    }
				break;
			case 'Delete':
				$this->db->trans_begin();
				$this->$model->delete_agent($id);
				if( $this->db->trans_status()===TRUE)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('chit_alert', array('message' => 'Agent deleted successfully','class' => 'success','title'=>'Delete Agent'));
				}else{
					$this->db->trans_rollback();
					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Delete Agent'));
				}
				redirect('agent');	
			break;
			
		}
	}
	
	
	// Influencer/Referrer Settlement
	public function agent_settlement($type,$msg = "")
	{
		$model        	= self::AGENT_MODEL;
		$setting_model 	= self::SET_MODEL;
		if($type == "list")
		{
			$data['main_content'] = "agent/settlement/list";
			$data['message']      = $msg;
			$this->load->view('layout/template', $data);
		}
		else if($type == "ajax"){
			$listData   = $this->$model->getAgent_records($_POST);
			$success 		= true;
			$message 		= "Record retrived successfully";
			$access 		= $this->$setting_model->get_access('agent/influencer_settlement/list');
			$ly_settings = $this->$model->get_loyalty_settings();
			$resultArr = array(
							'list' 	=> $listData,
							'access'=> $access,
							'minium_settlement_amount'=> $ly_settings['influ_minimum_amt_required_to_settle'],
							'maximum_settlement_amount' => $ly_settings['influ_settle_amt_max_percent']
							);
			$result = array("success" => $success, "message" => $message, "title" => "", "class" => "", "data" => $resultArr);
			echo json_encode($result);
		}
		else if($type =="bulk_settlement")
		{
			$addData = $_POST['settlement_records'];
			$this->db->trans_begin();
			$ly_settings = $this->$model->get_loyalty_settings();
			$resultArr = array();
			$check = 0;
			foreach($addData  as $key => $val)
	 		{
	 		    if($val['settlement_pts'] >= $ly_settings['influ_minimum_amt_required_to_settle'] && $val['settlement_pts'] <= $ly_settings['influ_settle_amt_max_percent'])
	 		    {
					$arrayTag = array(
								'id_agent' 	             => $val['id_agent'], 
								'settlement_date' 	     => date("Y-m-d H:i:s"),  
								'settlement_pts'         => $val['settlement_pts'], 
								'pts_type' 		         => $val['pts_type'], 
								'created_on'	         => date("Y-m-d  H:i:s"),
							    'settlement_branch'      => (isset($_POST['settlement_branch']) ? $_POST['settlement_branch']:0),
							    'settlement_created_by'  => $this->  session->userdata('uid'),
							    'settlement_approved_by' => $this->session->userdata('uid')
							);  
	 				 $insId = $this->$model->insertData($arrayTag,'ly_influencer_settlement');
	 				 
                     if($insId)
					 {
						$transcation_details = $this->$model->InfReferalPerson_PaymentDetails($val['id_agent']);
						$settlement_pts = $val['settlement_pts'];
						foreach($transcation_details as $trans)
						{
						    if($settlement_pts > 0){
                                                if($trans['unsettled_cash_pts'] < $settlement_pts)
                                                {
                                                    $unsettled_cash_pts = 0;
                                                    $debited_pts =   $trans['unsettled_cash_pts'];
                                                    $status = 2;
                                                }else if($trans['unsettled_cash_pts'] >= $settlement_pts)
                                                {
                                                    $unsettled_cash_pts =   ($trans['unsettled_cash_pts'] - $settlement_pts);
                                                    $debited_pts = $settlement_pts;
                                                    $status = 3;
                                                }
                                                $data  = array(
                                                                'unsettled_cash_pts'  => $unsettled_cash_pts,
                                                                'status'      => $status,
                                                                'date_upd'       => date("Y-m-d H:i:s"));
                                                //echo "<pre>"; print_r($data); 
                                                $status  = $this->$model->updateData($data,'id_cus_loyal_tran',$trans['id_cus_loyal_tran'],'ly_customer_loyalty_transaction');
                                                if($status){
                                                  $settlement_pts = $settlement_pts - $debited_pts;
                                                    //echo "BLC : ".$settlement_pts."<br/>";
                                                }
                                            }
						}
						$agent_cash_pts = $this->$model->getCashReward($val['id_agent']);
						$cash_reward = abs($agent_cash_pts['cash_reward'] - $val['settlement_pts']);
						$this->$model->updateData(array('cash_reward'=> $cash_reward),'id_agent',$val['id_agent'],'agent');
					 }
	 		    }
	 		    else{
	 		       
							$check = 1;
				    //redirect('agent/agent_settlement/list');
				    //$this->session->set_flashdata('chit_alert', array('message' => 'Kindly Check Amount in records','class' => 'danger','title'=>'Agent Settlement'));
	 		    }
		    }
		    
			if($this->db->trans_status()===TRUE && $check == 0)
			{
			    $this->db->trans_commit();
			    $resultArr 		= array(
								"message" 	=> "Settlement successfull..", 
								"status" 	=> true
								); 
			}			  
			else if($check == 1)
			{
			    $this->db->trans_rollback();
			     $resultArr 		= array(
								"message" 	=> "Kindly Check Amount in records", 
								"status" 	=> false
								); 
			
			}else{
			    $this->db->trans_rollback();
			    $resultArr 		= array(
								"message" 	=> "Unable to proceed", 
								"status" 	=> false
								); 
			}
			
			echo json_encode($resultArr);
		}
		else if($type =="settlement_amount")
		{
			//$_POST['settlement_pts'] = $this->$model->get_loyalty_settings();
			$this->db->trans_begin();
			
					$arrayTag = array(
								'id_agent' 	        => $_POST['id_agent'], 
								'settlement_date' 	     => date("Y-m-d H:i:s"),  
								'settlement_pts'         => $_POST['settlement_pts'], 
								'pts_type' 		         => 1, 
								'created_on'	         => date("Y-m-d  H:i:s"),
							    'settlement_branch'      => (isset($_POST['settlement_branch']) ? $_POST['settlement_branch']:''),
							    'settlement_created_by'  => $this->session->userdata('uid'),
							    'settlement_approved_by' => $this->session->userdata('uid'),
							    'utr_number'      => (isset($_POST['utr_no']) ? $_POST['utr_no']:NULL),
							    'bank_acc_no'      => (isset($_POST['bank_account_number']) ? $_POST['bank_account_number']:NULL),
							    'ifsc_code'      => (isset($_POST['ifsc_code']) ? $_POST['ifsc_code']:NULL),
							    'acc_holder_name'      => (isset($_POST['bank_name']) ? $_POST['bank_name']:NULL)
							);  
	 				 $insId = $this->$model->insertData($arrayTag,'ly_influencer_settlement');
                     if($insId)
					 {
						$transcation_details = $this->$model->InfReferalPerson_PaymentDetails($_POST['id_agent']);
						                $settlement_pts = $_POST['settlement_pts'];
                						foreach($transcation_details as $trans)
                                        {
                                            if($settlement_pts > 0){
                                                if($trans['unsettled_cash_pts'] < $settlement_pts)
                                                {
                                                    $unsettled_cash_pts = 0;
                                                    $debited_pts =   $trans['unsettled_cash_pts'];
                                                    $status = 2;
                                                }else if($trans['unsettled_cash_pts'] >= $settlement_pts)
                                                {
                                                    $unsettled_cash_pts =   ($trans['unsettled_cash_pts'] - $settlement_pts);
                                                    $debited_pts = $settlement_pts;
                                                    $status = 3;
                                                }
                                                $data  = array(
                                                                'unsettled_cash_pts'  => $unsettled_cash_pts,
                                                                'status'      => $status,
                                                                'date_upd'       => date("Y-m-d H:i:s"));
                                                //echo "<pre>"; print_r($data); 
                                                $status  = $this->$model->updateData($data,'id_cus_loyal_tran',$trans['id_cus_loyal_tran'],'ly_customer_loyalty_transaction');
                                                if($status){
                                                  $settlement_pts = $settlement_pts - $debited_pts;
                                                    //echo "BLC : ".$settlement_pts."<br/>";
                                                }
                                            }
                                        }
						$agent_cash_pts = $this->$model->getCashReward($_POST['id_agent']);
						$cash_reward = abs($agent_cash_pts['cash_reward'] - $_POST['settlement_pts']);
						$this->$model->updateData(array('cash_reward'=> $cash_reward),'id_agent',$_POST['id_agent'],'agent');
					 }
		    
			if($this->db->trans_status()===TRUE)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('chit_alert', array('message' => 'Agent Amount Settlement successfully','class' => 'success','title'=>'Agent Settlement'));	  
			}			  
			else
			{
				$this->db->trans_rollback();
				echo $this->db->last_query();
				echo $this->db->_error_message();exit;
				$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Agent Settlement'));
			}
		}
		
	}
	
	
	function ajax_getAgents()
	{
			$model=	self::AGENT_MODEL;
			$id_branch=$this->input->post('id_branch');
			$data['employee']=$this->$model->get_active_agents($id_branch);
			echo json_encode($data);
	}
	
	function getAgentBankDetails()
	{
	   $model=	self::AGENT_MODEL;
		$data['agent'] = $this->$model->get_agentBankData($_GET['id_agent']);
		echo json_encode($data);
	}
	
	function agent_referral_account($referal_code)
	{
		$model=	self::AGENT_MODEL;
		$data['accounts']  = $this->$model->agent_referral_account($referal_code);
		$data['main_content'] = self::AGENT_VIEW.'agent_refferal_report';
        $this->load->view('layout/template', $data);
	}
	
	 
}	
?>