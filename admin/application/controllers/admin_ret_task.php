<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_ret_task extends CI_Controller
{
	
	const TAKS_MODEL	= "ret_task_model";
	const SETT_MOD		= "admin_settings_model";
	const DOC_PATH 		= 'assets/task/documents/';
	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->model(self::TAKS_MODEL);
		$this->load->model(self::SETT_MOD);
		$this->load->helper('download');
		if(!$this->session->userdata('is_logged'))
		{
			redirect('admin/login');
		}	
		elseif($this->session->userdata('access_time_from') != NULL && $this->session->userdata('access_time_from') != "")
		{
			$now = time(); 
			$from = $this->session->userdata('access_time_from'); 
			$to = $this->session->userdata('access_time_to');  
			$allowedAccess = ($now > $from && $now < $to) ? TRUE : FALSE ;
			if($allowedAccess == FALSE){
				$this->session->set_flashdata('login_errMsg','Exceeded allowed access time!!');
				redirect('chit_admin/logout');	
			}			
		}
	}
		
	public function index()
	{	
		
	}

	public function task($type="",$id=""){
		$model=self::TAKS_MODEL;
		switch($type)
		{
			case "save":
					$file_name='';
	 				$task_name=$this->input->post('task_name');
	 				$employee=$this->input->post('id_employee');
	 				$id_profile=$this->input->post('id_profile');
	 				$id_employee=explode(',', $employee);

	 				if(!empty($_FILES['pre_attachement']))
					{
						$doc_path=self::DOC_PATH.'/pre_checklist_attachement';		
						if (!is_dir($doc_path)) 
						{
						mkdir($doc_path, 0777, TRUE);
						}
						$file_name=$this->pre_attachement_upload($_FILES['pre_attachement'],$doc_path);
					}
					foreach($id_employee as $emp)
					{
						if($emp==0)
						{
							$emp_list=$this->$model->get_ActiveEmployee($id_profile);
							foreach($emp_list as $list)
		 					{
		 						$insertData=array(
								 'task_name'		=> $task_name,
								 'task_pre_checklist_attachments'		=> $file_name,
								 'task_assign_to'	=> $list['id_employee'],
								 'task_status'		=> 1,
								 'task_created_on'	=> date("Y-m-d H:i:s"),
	    						 'task_created_by'  => $this->session->userdata('uid'),
							     );
		 						$this->db->trans_begin();
		 						$ins_id=$this->$model->insertData($insertData,'ret_tasks'); 
		 					}
						}else{
							$insertData=array(
								 'task_name'		=> $task_name,
								 'task_pre_checklist_attachments'		=> $file_name,
								 'task_assign_to'	=> $emp,
								 'task_status'		=> 1,
								 'task_created_on'	=> date("Y-m-d H:i:s"),
	    						 'task_created_by'  => $this->session->userdata('uid'),
							     );
								$this->db->trans_begin();
		 						$ins_id=$this->$model->insertData($insertData,'ret_tasks'); 
						}
					}
	 				
	 				
		 			if($this->db->trans_status()===TRUE)
		             {
					 	$this->db->trans_commit();
					 	$this->session->set_flashdata('chit_alert',array('message'=>'New Task added successfully','class'=>'success','title'=>'Add Task'));
					 	$data=array("status"=>true,"message"=>'Task Credted successfully..');
					 }
					 else
					 {
					 	$this->db->trans_rollback();						 	
					 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Add Task'));
					 	$data=array("status"=>false,"message"=>'Unable to proceed the requested process..');
					 }
					 echo json_encode($data);
	 	break; 
					
	 		break;
	 	case "edit":
	 			$data= $this->$model->getTask($id);

	 			echo json_encode($data);
	 	break; 
	 	
	 	case 'delete':
	 		$cancel_reason  =($this->input->post('cancel_remark')!='' ? $this->input->post('cancel_remark'):NULL);
	 		$id_task 		=$this->input->post('id_task');
			$this->db->trans_begin();
			$this->$model->updateData(array('task_status'=>3,'cancel_reason'=>$cancel_reason),'id_task',$id_task,'ret_tasks');
			
			if( $this->db->trans_status()===TRUE)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('chit_alert', array('message' => 'Task Cancelled successfully','class' => 'success','title'=>'Task'));	  
			}			  
			else
			{
				$this->db->trans_rollback();
				$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Task'));
			}
			echo true;
		//redirect('admin_ret_task/task/list');	
				break;
					
	 	case "update":
				$addData=$_POST['task'];
				$this->db->trans_begin();
				if($addData['id_employee']==0)
 				{
 					$employee=$this->$model->get_ActiveEmployee();
 					foreach($employee as $emp)
 					{
 						$updData=array(
						 'task_name'		=> $addData['task_name'],
						 'task_assign_to'	=> $emp['id_employee'],
						 'task_status'		=> 1,
						 'task_created_on'	=> date("Y-m-d H:i:s"),
						 'task_created_by'  => $this->session->userdata('uid'),
					     );
 						$this->$model->updateData($updData,'id_task',$id,'ret_tasks');
 					}
 				}else{
 					$updData=array(
							 'task_name'		=> $addData['task_name'],
							 'task_assign_to'	=> $addData['id_employee'],
							 'task_status'		=> 1,
							 'task_created_on'	=> date("Y-m-d H:i:s"),
    						 'task_created_by'  => $this->session->userdata('uid'),
						     );
 					$this->$model->updateData($updData,'id_task',$id,'ret_tasks');
 				}
				
				if($this->db->trans_status()===TRUE)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('chit_alert',array('message'=>'Task modified successfully','class'=>'success','title'=>'Edit Task'));
					echo 1;
				}
				else
				{
					$this->db->trans_rollback();						 	
					$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Edit Task'));
					echo 0;
				}
	 		break;
	 		
			case 'list':
			
						$data['main_content'] = "ret_task/list" ;
						$this->load->view('layout/template', $data);
					break;
					
			default:
						$SETT_MOD = self::SETT_MOD;
						$id_employee =(isset($_POST['id_employee']) ? $_POST['id_employee']:'');
					  	$data = $this->$model->ajax_getTask($id_employee);	 
					  	$access = $this->$SETT_MOD->get_access('admin_ret_task/task/list');
				        $data = array(
				        					'list' =>$data,
											'access'=>$access
				        				);  
						echo json_encode($data);
		}
	}


	function get_ActiveProfile()
	{
		$model=self::TAKS_MODEL;
		$data=$this->$model->get_ActiveProfile();
		echo json_encode($data);
	}

	function get_ActiveEmployee()
	{
		$model=self::TAKS_MODEL;
		$data=$this->$model->get_employee_details($_POST['id_profile'],$_POST['id_branch']);
		echo json_encode($data);
	}

	function task_attachement_upload($files,$ins_id,$doc_path)
	{
		$file_name='';
		 foreach ($files['name'] as $key => $image) 
		 {
            $_FILES['task_attachement']['name']= $files['name'][$key];
            $_FILES['task_attachement']['type']= $files['type'][$key];
            $_FILES['task_attachement']['tmp_name']= $files['tmp_name'][$key];
            $_FILES['task_attachement']['error']= $files['error'][$key];
            $_FILES['task_attachement']['size']= $files['size'][$key];
            $extension=explode('.',$_FILES['task_attachement']['name']);
	        $new_file_name=time().'.'.$extension[1];
            $config['encrypt_name'] = FALSE;		//Rename the File
	        $config['upload_path'] =  $doc_path; 	// set path to store uploaded files
	        $config['allowed_types'] = '*'; 		// set allowed file types
	        $config['max_size']    = 0; 			// set upload limit, set 0 for no limit
            $config['file_name'] = $new_file_name;
           $this->upload->initialize($config);
            if ($this->upload->do_upload('task_attachement')) 
            {
                $result=$this->upload->data();
                $file_name.=$result['file_name'].'##';
            } 
         }
		return $file_name;
	}


	function pre_attachement_upload($files,$doc_path)
	{
		$file_name='';
		 foreach ($files['name'] as $key => $image) 
		 {
            $_FILES['pre_attachement']['name']= $files['name'][$key];
            $_FILES['pre_attachement']['type']= $files['type'][$key];
            $_FILES['pre_attachement']['tmp_name']= $files['tmp_name'][$key];
            $_FILES['pre_attachement']['error']= $files['error'][$key];
            $_FILES['pre_attachement']['size']= $files['size'][$key];
            $extension=explode('.',$_FILES['pre_attachement']['name']);
	        $new_file_name=time().'.'.$extension[1];
            $config['encrypt_name'] = FALSE;		//Rename the File
	        $config['upload_path'] =  $doc_path; 	// set path to store uploaded files
	        $config['allowed_types'] = '*'; 		// set allowed file types
	        $config['max_size']    = 0; 			// set upload limit, set 0 for no limit
            $config['file_name'] = $new_file_name;

           $this->upload->initialize($config);
            if ($this->upload->do_upload('pre_attachement')) 
            {
                $result=$this->upload->data();
                $file_name.=$result['file_name'].'##';
            } 
         }
		return $file_name;
	}

	function post_attachement_upload($files,$ins_id,$doc_path)
	{
		$file_name='';
		 foreach ($files['name'] as $key => $image) 
		 {
            $_FILES['post_attachement']['name']= $files['name'][$key];
            $_FILES['post_attachement']['type']= $files['type'][$key];
            $_FILES['post_attachement']['tmp_name']= $files['tmp_name'][$key];
            $_FILES['post_attachement']['error']= $files['error'][$key];
            $_FILES['post_attachement']['size']= $files['size'][$key];
            $extension=explode('.',$_FILES['post_attachement']['name']);
	        $new_file_name=time().'.'.$extension[1];
            $config['encrypt_name'] = FALSE;		//Rename the File
	        $config['upload_path'] =  $doc_path; 	// set path to store uploaded files
	        $config['allowed_types'] = '*'; 		// set allowed file types
	        $config['max_size']    = 0; 			// set upload limit, set 0 for no limit
            $config['file_name'] = $new_file_name;
           $this->upload->initialize($config);
            if ($this->upload->do_upload('post_attachement')) 
            {
                $result=$this->upload->data();
                $file_name.=$result['file_name'].'##';
            } 
         }
		return $file_name;
	}

	function update_status()
	{
		$model=self::TAKS_MODEL;
		$file_name 						 ='';
		$id_task 						 =$this->input->post('id_task');
		$task_post_checklist_attachments =$this->input->post('task_post_checklist_attachments');
		$remarks 						 =$this->input->post('remarks');
		if(!empty($_FILES['post_attachement']))
		{
			$doc_path=self::DOC_PATH.'/post_checklist_attachement';		
			if (!is_dir($doc_path)) 
			{
				mkdir($doc_path, 0777, TRUE);
			}
			$file_name.=$this->post_attachement_upload($_FILES['post_attachement'],$id_task,$doc_path);
		}

		$updData=array(
		'task_status'						=> 2,
		'remarks'							=> $remarks,
		'task_post_checklist_attachments' 	=> $file_name,
		'completed_on'						=> date("Y-m-d H:i:s"),
		'task_updated_on'					=> date("Y-m-d H:i:s"),
		'task_updated_by'  					=> $this->session->userdata('uid'),
		);
		$this->db->trans_begin();
		$this->$model->updateData($updData,'id_task',$id_task,'ret_tasks');
		if($this->db->trans_status()===TRUE)
		{
			$this->db->trans_commit();
			$this->session->set_flashdata('chit_alert',array('message'=>'Task Status Updated successfully','class'=>'success','title'=>'Edit Task'));
			$data=array("status"=>true,"message"=>'Task Credted successfully..');
		}
		else
		{
			$this->db->trans_rollback();						 	
			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Edit Task'));
			$data=array("status"=>false,"message"=>'Unable to proceed the requested process..');
		}
		echo json_encode($data);
	}
	
	function remove_directory($path)
	{
		$this->load->helper("file"); // load the helper
		if(delete_files($path, true))
		{
			return true;
		}else{
			return false;
		}
	}

	function download_file($id,$file_name,$type)
	{
		$folder=($type==1 ? 'pre_checklist_attachement' : ($type==2  ? 'post_checklist_attachement' :''));
		$doc_path=self::DOC_PATH.'/'.($folder!='' ? $folder.'/':'').'/'.$file_name;
		$this->load->helper('download');
		$data = file_get_contents($doc_path);
		$name = $file_name;
		force_download($name, $data);
		return TRUE;
	}


	public function notice_board($type="",$id=""){
		$model=self::TAKS_MODEL;
		switch($type)
		{
			case "save":
			        $reminder_date=NULL;
	 				$noticeboard_text 	=$this->input->post('noticeboard_text');
	 				$id_profile 		=$this->input->post('id_profile');
	 				$r_date 			=$this->input->post('reminder_date');
	 				$id_branch 			=$this->input->post('id_branch');
	 				if($r_date!='')
	 				{
						$reminder_date  	= str_replace('/','-',$r_date); 
						$dateRange         = explode('-',$reminder_date);
						$d2  			   =date_create($dateRange[2].'-'.$dateRange[0].'-'.$dateRange[1]);
						$reminder_on  	= date_format($d2,"Y-m-d");
	 				}
			      	
			        $employee 	 	=$this->input->post('employee');
					$insertData=array(
					'noticeboard_text'		=> $noticeboard_text,
					'visible_to'    		=> $id_profile,
					'reminder_on'    		=> $reminder_on,
					'noticeboard_status'    => 1,
					'id_branch'             => $id_branch,
					'created_on'			=> date("Y-m-d H:i:s"),
					'created_by'  			=> $this->session->userdata('uid'),
					);
					$this->db->trans_begin();
            
					$notice_board_ins=$this->$model->insertData($insertData,'ret_noticeboard'); 
					if($notice_board_ins)
					{
						if($id_profile==0)
						{
							$emp_list=$this->$model->get_employee_details($id_profile,$id_branch);
							foreach($emp_list as $list)
		 					{
								$updData=array(
								'id_noticeboard'=> $notice_board_ins,
								'id_employee'	=> $list['id_employee'],
								'is_viewed'    	=> 0,
								);
		 						$ins_id=$this->$model->insertData($updData,'ret_noticeboard_view_details'); 
		 					}
						}
						else
						{
                            if($id_profile!='')
                            {
                                $emp_list=$this->$model->get_employee_details($id_profile,$id_branch);
                                if(sizeof($employee)>0 && $employee!='')
                                {
                                    foreach($employee as $emp)
                                    {
                                        if($emp==0)
        								{
        									foreach($emp_list as $list)
        				 					{
        										$updData=array(
        										'id_noticeboard'		=> $notice_board_ins,
        										'id_employee'		    => $list['id_employee'],
        										'is_viewed'    			=> 0,
        										);
        				 						$ins_id=$this->$model->insertData($updData,'ret_noticeboard_view_details'); 
        				 					}
        								}else{
        								    $updData=array(
        									'id_noticeboard'		=> $notice_board_ins,
        									'id_employee'    		=> $emp,
        									'is_viewed'    			=> 0,
        									);
        									$this->$model->insertData($updData,'ret_noticeboard_view_details'); 
        								}
                                    }
                                }
                                else
                                {
                                    foreach($emp_list as $list)
                                    {
                                        $updData=array(
                                        'id_noticeboard'		=> $notice_board_ins,
                                        'id_employee'		    => $list['id_employee'],
                                        'is_viewed'    			=> 0,
                                        );
                                        $this->$model->insertData($updData,'ret_noticeboard_view_details'); 
                                    }
                                }
                            }
						}
						
					}
		 			if($this->db->trans_status()===TRUE)
		             {
					 	$this->db->trans_commit();
					 	$this->session->set_flashdata('chit_alert',array('message'=>'Information Sent Successfully','class'=>'success','title'=>'Notice Board'));
					 	$data=array("status"=>true,"message"=>'Information Sent Successfully..');
					 }
					 else
					 {
					 	$this->db->trans_rollback();						 	
					 	$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Notice Board'));
					 	$data=array("status"=>false,"message"=>'Unable to proceed the requested process..');
					 }
					 echo json_encode($data);
	 	break; 
					
	 		break;
	 	case "edit":
	 			$data= $this->$model->getNoticeboardDetails($id);
	 			$data['id_employee']=json_encode($this->$model->get_branch_edit($id));
	 			echo json_encode($data);
	 	break; 
	 	
	 	case 'delete':
			$this->db->trans_begin();
			$this->$model->updateData(array('noticeboard_status'=>2),'id_noticeboard',$id,'ret_noticeboard');
			
			if( $this->db->trans_status()===TRUE)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('chit_alert', array('message' => 'Deleted successfully','class' => 'success','title'=>'Notice Board'));	  
			}			  
			else
			{
				$this->db->trans_rollback();
				$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Notice Board'));
			}
		redirect('admin_ret_task/notice_board/list');	
				break;
					
	 	case "update":
				$noticeboard_text 	=$this->input->post('noticeboard_text');
 				$id_profile 		=$this->input->post('id_profile');
 				$r_date 			=$this->input->post('reminder_date');
 				
 				if($r_date!='')
 				{
					$date  				= str_replace('/',$r_date);  
					$d2  				= date_create($date);
					$reminder_date  	= date_format($d2,"Y-m-d");
 				}
			       
 			
 			    $updData=array(
				'noticeboard_text'		=> $noticeboard_text,
				'visible_to'    		=> $id_profile,
				'reminder_on'    		=> $reminder_date,
				'noticeboard_status'    => 1,
				'id_branch'             => $id_branch,
				'created_on'			=> date("Y-m-d H:i:s"),
				'created_by'  			=> $this->session->userdata('uid'),
				);
				
                    $this->db->trans_begin();
                    $status=$this->$model->updateData($updData,'id_noticeboard',$id,'ret_noticeboard');
                   
					if($status)
					{
						if($id_profile==0)
						{
							$emp_list=$this->$model->get_employee_details($id_profile,0);
						    $this->$model->deleteData('id_noticeboard',$id,'ret_noticeboard_view_details');
							foreach($emp_list as $list)
		 					{
								$upd_data=array(
								'id_employee'	=> $list['id_employee'],
								'is_viewed'    	=> 0,
								'id_noticeboard'=>$id,
								);
							    $this->$model->insertData($upd_data,'ret_noticeboard_view_details'); 
		 					}
						}
						else
						{
                            if($id_profile!='')
                            {
                                $emp_list=$this->$model->get_employee_details($id_profile,0);
                                if(sizeof($employee)>0 && $employee!='')
                                {
                                    foreach($employee as $emp)
                                    {
                                        if($emp==0)
        								{
        									foreach($emp_list as $list)
        				 					{
            									$upd_data=array(
                								'id_employee'	=> $list['id_employee'],
                								'is_viewed'    	=> 0,
                								);
                								$this->$model->updateData($upd_data,'id_noticeboard',$id,'ret_noticeboard_view_details');
		 						
        				 					}
        								}else{
        								    $upd_data=array(
            								'id_employee'	=> $list['id_employee'],
            								'is_viewed'    	=> 0,
            								);
            								$this->$model->updateData($upd_data,'id_noticeboard',$id,'ret_noticeboard_view_details');
		 						
        								}
                                    }
                                }
                                else
                                {
                                    foreach($emp_list as $list)
                                    {
                                        $upd_data=array(
        								'id_employee'	=> $list['id_employee'],
        								'is_viewed'    	=> 0,
        								);
        								$this->$model->updateData($upd_data,'id_noticeboard',$id,'ret_noticeboard_view_details');
		 						
                                    }
                                }
                            }
						}
						
					}

				if($this->db->trans_status()===TRUE)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('chit_alert',array('message'=>'Notice Board modified successfully','class'=>'success','title'=>'Notice Board'));
					echo 1;
				}
				else
				{
					$this->db->trans_rollback();						 	
					$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Notice Board'));
					echo 0;
				}
	 		break;
	 		
			case 'list':
			            $updData=array('updated_on'=> date("Y-m-d H:i:s"),'is_viewed'=>1);
		                $this->$model->updateData($updData,'id_employee',$this->session->userdata('uid'),'ret_noticeboard_view_details');
						$data['main_content'] = "notice_board/list" ;
						$this->load->view('layout/template', $data);
					break;
						
			default:
						$SETT_MOD = self::SETT_MOD;
					  	$data = $this->$model->ajax_getNoticeBoard();	 
					  	$access = $this->$SETT_MOD->get_access('admin_ret_task/notice_board/list');
				        $data = array(
				        					'list' =>$data,
											'access'=>$access
				        				);  
						echo json_encode($data);
		}
	}
	
	
	function noticeboard_status($status,$id)
	{
		$model=self::TAKS_MODEL;
		$this->db->trans_begin();
		$this->$model->updateData(array('noticeboard_status' => $status),'id_noticeboard',$id,'ret_noticeboard');
		if($this->db->trans_status()===TRUE)
		{
		    $this->db->trans_commit();
			$this->session->set_flashdata('chit_alert',array('message'=>'status updated as '.($status ? 'active' : 'inactive').' successfully.','class'=>'success','title'=>'Status'));			
		}	
		else
		{
		    $this->db->trans_rollback();
			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Customer Status'));
		}	
		redirect('admin_ret_task/notice_board/list');
	}


	
}	
?>