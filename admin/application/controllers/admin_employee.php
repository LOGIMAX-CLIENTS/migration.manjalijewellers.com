<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

class Admin_employee extends CI_Controller

{

	const VIEW_FOLDER = 'master/';

	const SET_MODEL = "Admin_settings_model";

	const EMP_MODEL	="employee_model";

	const EMP_VIEW ="master/employee/";

	const EMP_IMG_PATH ='assets/img/employee/';

	const EMP_IMG= 'employee.jpg';

	const DEF_EMP_IMG_PATH = 'assets/img/default.png/';



	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model(self::EMP_MODEL);

		$this->load->model(self::SET_MODEL);
		
		$this->load->model("log_model");
		
		$this->load->model("wallet_model");

		$this->load->library('encrypt');

		 if(!$this->session->userdata('is_logged'))

        {

			redirect('admin/login');

		}

	}



	public function index()

	{

	    $data['main_content'] = self::VIEW_FOLDER.'blank';

        $this->load->view('layout/template', $data);	   

	}

	

	 public function ajax_get_emp_list()

	{

		$set_model= self::SET_MODEL;

		$access= $this->$set_model->get_access('employee');

		$model=	self::EMP_MODEL;

		$items=$this->$model->get_list_data();	

		$employee = array(

							'access' => $access,

							'data'   => $items

						);  

		echo json_encode($employee);

	}  



/** Employee Master **/



	public function get_dept()

	{

		$model=self::EMP_MODEL;	

		$this->load->model($model);

		$data= $this->$model->get_dept();

	   	

		echo $data;

	}

	

	public function get_designation()

	{

		$model=self::EMP_MODEL;	

		$this->load->model($model);

		$data= $this->$model->get_designation();

	   	

		echo $data;

	}

	

	public function isUserAvailable()

	{

			$username=$this->input->post('username');	

			$id_employee =$this->input->post('id_employee');	

			

			$model_name=self::EMP_MODEL;	

			if($id_employee)

			{

				$available = $this->$model_name->check_username($username,$id_employee);

			}	

			else

			{

				$available = $this->$model_name->check_username($username);

			}	

			print_r($available);exit;

			if($available)

			{

				

				echo TRUE;	

			}

			else

			{

				echo FALSE;

			}

			



			

	}

		

	/** Employee master process starts here **/

	

	

	/**

	* To show employee list 

	* 

	*/

	

	public function emp_list($msg="")

	{

		$model_name=self::EMP_MODEL;	

		$this->load->model($model_name);		

		$data['message']=$msg;				
        $data['device_enable_count']=$this->$model_name->get_enabled_device_count();
		$data['chit_data']=$this->$model_name->get_chit_settings_data();
		$emp_data=$this->$model_name->get_list_data();

	

		if($emp_data)

		{

			$data['employees']=$emp_data;

		}

		

		

		$data['main_content'] = self::EMP_VIEW.'list';

	    $this->load->view('layout/template', $data);

	}

	

	/**

	* 

	* @param $model - employee model name

	* @param $process_type - 

	* @param $id

	* 

	* @return view

	*/	

	

   	

	public function emp_form($process_type="",$id="")

	{

		$model=self::EMP_MODEL;

		

	 	switch ($process_type)

	 	{

			case 'Add':

				   

				   $this->load->model($model);

				   $data['emp']=$this->$model->get_empty_record();

				   $data['emp']['emp_img_path']	= self:: DEF_EMP_IMG_PATH;

				    $data['emp'] ['emp_img' ]       =NULL;

				   $data['main_content'] = self::EMP_VIEW."form" ;

				   $this->load->view('layout/template', $data);

				break;

			

			case 'Edit':

				   $this->load->model($model);

				   $emp_info=$this->$model->get_emp_record($id);
                //    print_r($emp_info);exit;
				   $emp_address=$this->$model->get_emp_address($id);

					$id_branch=$this->session->userdata('id_branch');
					$uid=$this->session->userdata('uid');

					/* if($id_branch=='' && $uid!=1 && $emp_info['id_employee']!=1)
					{
						$emp_info['passwd'] = $this->$model->__decrypt($emp_info['passwd']);
					} */

					if($id_branch=='')
					{
						$emp_info['passwd'] = $this->$model->__decrypt($emp_info['passwd']);
					}


					
					$login_branches=explode(',',$emp_info['login_branches']);
					$log_branches=(json_encode($login_branches));


				   $data['emp']=array(

				   			'id_employee'	=> (isset($emp_info['id_employee'])?$emp_info['id_employee']:0) ,

							'id_profile' 	=> (isset($emp_info['id_profile'])?$emp_info['id_profile']:0),

							'firstname'		=> (isset($emp_info['firstname'])?$emp_info['firstname']:NULL),

							'lastname' 		=> (isset($emp_info['lastname'])?$emp_info['lastname']:NULL),

							'date_of_birth' => (!empty($emp_info['date_of_birth']) && $emp_info['date_of_birth'] != '0000-00-00' && $emp_info['date_of_birth'] != '0000-00-00 00:00:00' && strtotime($emp_info['date_of_birth']) > 0 ? date('Y-m-d', strtotime($emp_info['date_of_birth'])) : ''),

							'emp_code'		=> (isset($emp_info['emp_code'])?$emp_info['emp_code']:NULL),

							'dept'			=> (isset($emp_info['dept'])?$emp_info['dept']:0),

							'designation'	=> (isset($emp_info['designation'])?$emp_info['designation']:0),
							
							'active'	 => (isset($emp_info['active'])?$emp_info['active']:0),	

							'id_branch'	=> (isset($emp_info['id_branch'])?$emp_info['id_branch']:0),
							
							'login_branches'	=> (isset($emp_info['login_branches'])?$emp_info['login_branches']:NULL),

							'date_of_join'	=> (!empty($emp_info['date_of_join']) && $emp_info['date_of_join'] != '0000-00-00' && $emp_info['date_of_join'] != '0000-00-00 00:00:00' && strtotime($emp_info['date_of_join']) > 0 ? date('d-m-Y', strtotime($emp_info['date_of_join'])) : ''),

							'email'			=> (isset($emp_info['email'])?$emp_info['email']:NULL),

							'mobile'		=> (isset($emp_info['mobile'])?$emp_info['mobile']:NULL),

							'phone'			=> (isset($emp_info['phone'])?$emp_info['phone']:NULL),

							'username'		=> (isset($emp_info['username'])?$emp_info['username']:NULL),

							'passwd'		=> (isset($emp_info['passwd'])?$emp_info['passwd']:NULL),

							'comments'		=> (isset($emp_info['comments'])?$emp_info['comments']:NULL),

							'id_country' 	=> (isset($emp_address['id_country'])?$emp_address['id_country']:0),

							'id_state'		=> (isset($emp_address['id_state'])?$emp_address['id_state']:0),

							'id_city'		=> (isset($emp_address['id_city'])?$emp_address['id_city']:0),

							'address1'		=> (isset($emp_address['address1'])?$emp_address['address1']:NULL),

							'address2'		=> (isset($emp_address['address2'])?$emp_address['address2']:NULL),

							'address3'		=> (isset($emp_address['address3'])?$emp_address['address3']:NULL),

							'pincode'		=> (isset($emp_address['pincode'])?$emp_address['pincode']:NULL),

							/*'emp_img_path'  => (isset($emp_info['emp_img']) && $emp_info['emp_img'] != NULL ? $emp_info['emp_img']: self::DEF_EMP_IMG_PATH)*/



				   );
				   

				    if(is_dir(self::EMP_IMG_PATH.$id))

				   {

				   	  

						$emp_img=self::EMP_IMG_PATH.$id."/".self:: EMP_IMG;

				   	    if(file_exists($emp_img))

				   	    {

							$data['emp']['emp_img_path']		= $emp_img;

						}

				   		 

				   }

				   $data['designation']=$this->$model->get_design();

			       $data['main_content'] = self::EMP_VIEW."form" ;

				   $this->load->view('layout/template', $data);

				 

				break;

			

		}

	}

	





	public function emp_post($process_type="",$id="")

	{

		$model=self::EMP_MODEL;
		$admin_set_model='admin_settings_model';

		$this->load->model($model);

		

	 	switch ($process_type)

	 	{

			case 'Add': //Add process

			        $emp_data=$this->input->post('emp');

					if (empty($emp_data['emp_code'])) {
						$this->session->set_flashdata('empErr', 'Employee code is required');
						$this->session->set_flashdata('old_emp', $emp_data);
						redirect('employee/add');
						exit;
					} elseif (strlen(trim($emp_data['emp_code'])) < 3) {
						$this->session->set_flashdata('empErr', 'Employee code should be at least 3 characters');
						$this->session->set_flashdata('old_emp', $emp_data);
						redirect('employee/add');
						exit;
					}
                //   print_r($emp_data); exit; 

					$basepwd = $this->$model->__encrypt($emp_data['passwd']);
            
					$pwd = password_hash(trim($emp_data['passwd']), PASSWORD_DEFAULT);

			        $emp['info']=array('lastname'=>$emp_data['lastname'],

								'firstname'=>$emp_data['firstname'],

								'date_of_birth'=> (!empty($emp_data['date_of_birth']) && strtotime($emp_data['date_of_birth']) > 0 ? date("Y-m-d H:i:s", strtotime($emp_data['date_of_birth'])) : NULL),

								'emp_code'=>$emp_data['emp_code'],

								'dept'=>$emp_data['dept'],

								'designation'=>$emp_data['designation'],
								
								'active'	=>	(isset($emp_data['active'])?$emp_data['active']: 0),		

								'id_branch'      => (isset($emp_data['id_branch'])?$emp_data['id_branch']:NULL),
								
								'login_branches'	=> (isset($emp_data['login_branches'])?$emp_data['login_branches']:0),

								'date_of_join'=> (!empty($emp_data['date_of_join']) && strtotime($emp_data['date_of_join']) > 0 ? date("Y-m-d H:i:s", strtotime($emp_data['date_of_join'])) : NULL),

								'email'=>$emp_data['email'],

								'mobile'=>$emp_data['mobile'],

								'phone'=>$emp_data['phone'],

								'username'=>$emp_data['username'],
								
								'passwd' => $basepwd,

								'pwd_hash'  => $pwd,

								'image'=>NULL,

								//'active'=>1,

								'date_add'=>date("Y-m-d H:i:s"),																
								
								'id_profile' 	=> (isset($emp_data['id_profile'])?$emp_data['id_profile']:0)

							);
							// print_r($emp); exit;
			        $emp['address']=array('id_country' =>$emp_data['country'],

									'id_state' =>$emp_data['state'],

									'id_city'=>$emp_data['city'],

									

									'address1'=>$emp_data['address1'],

									'address2'=>$emp_data['address2'],

									'address3'=>$emp_data['address3'],

									'pincode'=>$emp_data['pincode'],	

									//'active'=>1,					

									'date_add'=>date("Y-m-d H:i:s")

							);

					$data=array($emp['info'],$emp['address']);	

				

				    $emp_id=$this->$model->insert_employee($data);

					
                    $data=$this->$admin_set_model->settingsDB('get',1,'');
					//  print_r($data);exit;
					if($data['emp_wallet_account_type']==1 && $emp_id!=''){

						$emp_id=$this->emp_wallet_acc($emp_id);

					}



					  if(isset($_FILES['emp_img']['name']))

				            {					   

								if( $emp_id > 0)

								{

										$this->set_image($emp_id);

										

								}

							}
							
							
					if($emp_id)
					{
					    $empSettings=array(
                        'id_employee'   =>$emp_id,
                        'access_time_from'=>'00:00:01',
                        'access_time_to'=>'23:59:58',
                        'created_on'    =>date("Y-m-d H:i:s"),
						'created_by'    => $this->session->userdata('uid')
                        );
                        $this->$model->insertData($empSettings,'employee_settings');
					}

				    if($this->db->trans_status()===TRUE)

				    {

				    	

						$this->session->set_flashdata('chit_info', array('message' => 'Record added successfully','class' => 'success','title'=>'Add Employee Record'));

	                  

	                  redirect('employee');

					}

				break;

			

			case 'Edit': //Update process

			            $emp_data=$this->input->post('emp');

						$basepwd = $this->$model->__encrypt($emp_data['passwd']);

						$pwd_check=$this->$model->check_password($id,$emp_data['passwd']);
						
						$pwd = password_hash($emp_data['passwd'], PASSWORD_DEFAULT);

			            $emp['info']=array('id_employee'	=> $emp_data['id_employee'],

			                    'lastname'					=> $emp_data['lastname'],

								'firstname'					=> $emp_data['firstname'],

								'date_of_birth'				=> (!empty($emp_data['date_of_birth']) && strtotime($emp_data['date_of_birth']) > 0 ? date("Y-m-d H:i:s", strtotime($emp_data['date_of_birth'])) : NULL),

								'emp_code'					=> $emp_data['emp_code'],

								'dept'						=> $emp_data['dept'],

								'designation'				=> $emp_data['designation'],
								
								'active'			        =>($emp_data['active']?$emp_data['active']: 0),	

								'id_branch'     			 =>($emp_data['id_branch']==''?NULL:$emp_data['id_branch']),
								
								'login_branches'	=> (isset($emp_data['login_branches'])?$emp_data['login_branches']:0),

								'date_of_join'				=> (!empty($emp_data['date_of_join']) && strtotime($emp_data['date_of_join']) > 0 ? date("Y-m-d H:i:s", strtotime($emp_data['date_of_join'])) : NULL),

								'email'						=> $emp_data['email'],

								'mobile'					=> $emp_data['mobile'],

								'phone'						=> $emp_data['phone'],

								'username'					=> $emp_data['username'],
								
							
								'passwd'					=> ($pwd_check == true ? $emp_data['passwd']:$basepwd),
								
								'pwd_hash'  => $pwd,

								'image'=>NULL,

								//'active'=>1,

								'date_upd'=>date("Y-m-d H:i:s"),
								
								'id_profile' 	=> (isset($emp_data['id_profile'])?$emp_data['id_profile']:0)

							);
							
							
					if(!empty($emp_data['passwd'])){
					     $emp['info']['pwd_hash']  = $pwd;  
					}

			        $emp['address']=array('id_country' => $emp_data['country'],

									'id_state'         => $emp_data['state'],

									'id_city'          => $emp_data['city'],

									

									'address1'         => $emp_data['address1'],

									'address2'         => $emp_data['address2'],

									'address3'         => $emp_data['address3'],

									'pincode'          => $emp_data['pincode'],	

									//'active'           => 1,					

									'date_upd'         => date("Y-m-d H:i:s")

							);

					$data=array($emp['info'],$emp['address']);	

//print_r($data);exit;
				    $status=$this->$model->update_employee($data);

				    $emp_id= $emp['info']['id_employee'];

				    if($status)

				    {

				    	

				    	 if(isset($_FILES['emp_img']['name']))

			            {					   

							if( $emp_id>0)

							{

									$this->set_image($emp_id);

							}

						}

						

						$this->session->set_flashdata('chit_info', array('message' => 'Record updated successfully','class' => 'success','title'=>'Modify Employee Record'));

	                  

	                  redirect('employee');

					}	

					

				break;

			

			case 'Delete':

			 			 $status=$this->$model->delete_emp_record($id);

				   if($status)

				    {

				    	 //Remove image and its folder

							

							  if(is_dir(self::EMP_IMG_PATH.$id))

							  {

								   $this->rrmdir(self::EMP_IMG_PATH.$id);

							  }	

						$this->session->set_flashdata('chit_info', array('message' => 'Record deleted successfully','class' => 'success','title'=>'Delete Employee Record'));

	                  

	                  redirect('employee');

					}	

				    

				break;

		}

	}

	function check_mobile()
	{	 			
		$mobile=$this->input->post('mobile');	
		$id_employee =$this->input->post('id_employee');	
		
		$model_name=self::EMP_MODEL;	
		
		if($id_employee)
		{
			$available=$this->$model_name->mobile_available($mobile,$id_employee);
		}	
		else
		{
			$available=$this->$model_name->mobile_available($mobile);
		}	
		
		if($available)
		{
			
			echo TRUE;	
		}
		else
		{
			echo FALSE;
		}
	}

function rrmdir($path) {

     // Open the source directory to read in files

        $i = new DirectoryIterator($path);

        foreach($i as $f) {

            if($f->isFile()) {

                unlink($f->getRealPath());

            } else if(!$f->isDot() && $f->isDir()) {

                rrmdir($f->getRealPath());

            }

        }

        rmdir($path);

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

	  case IMAGETYPE_GIF  : $src = imagecreatefromgif($img);  break;

	  case IMAGETYPE_JPEG : $src = imagecreatefromjpeg($img); break;

	  case IMAGETYPE_PNG  : $src = imagecreatefrompng($img); break;

	  default : //die("Unknown filetype");	

	  return false;

	  }

	  $tmp = imagecreatetruecolor($width, $height);

	 

	imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);

	imagejpeg($tmp, $dst);



	}



function set_image($id)

 {



 	$data=array();

    $img_path=self::EMP_IMG_PATH."/".$id;

		

	if (!is_dir($img_path)) {

		    mkdir($img_path, 0777, TRUE);

			

		}

   	 if($_FILES['emp_img']['name'])

   	 {   

      //  $ext = pathinfo($_FILES['emp_img']['name'], PATHINFO_EXTENSION);

        $img=$_FILES['emp_img']['tmp_name'];

        $path=self::EMP_IMG_PATH.$id."/".self::EMP_IMG;

		$filename = self::EMP_IMG.".jpg";	 	

	 	$this->upload_img('emp_img',$path,$img);	

	 	//$data['emp_img']= $filename;	

	 } 

 }

 /** End of Employee **/
 
 public function checkempcode()

	{

			$emp_code=$this->input->post('emp_code');	

			$id_employee = $this->input->post('id_employee');			

			$model_name=self::EMP_MODEL;	

			if($id_employee)

			{

				$available = $this->$model_name->check_empcode($emp_code,$id_employee);

			}	

			else

			{

				$available = $this->$model_name->check_empcode($emp_code);

			}	

		//	print_r($available);exit;

			if($available)

			{

				

				echo TRUE;	

			}

			else

			{

				echo FALSE;

			}
	}
 //employee Active/Inactive Options hh//	
 
 
 function get_employee()
 {
     $sql="select * from employee";
     $data=$this->db->query($sql)->result_array();	
     echo json_encode($data);
 }
	
function employee_status($status,$id)
	{
		$data = array('active' => $status);
		$model=self::EMP_MODEL;
		$status = $this->$model->update_employee_only($data,$id);
		if($status)
		{
			$this->session->set_flashdata('chit_alert',array('message'=>'Employee status updated as '.($status ? 'active' : 'inactive').' successfully.','class'=>'success','title'=>'Employee Status'));			
		}	
		else
		{
			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Employee Status'));
		}	
		redirect('employee');
	}
	
	//login Branches
	 public function branchname_list()
	{
		$model_name=self::EMP_MODEL;
		$data['branch']=$this->$model_name->branchname_list();
		echo json_encode($data);
	}
	
	public function employee_settings($type="",$id="",$status=""){
		$model_name=self::EMP_MODEL;
		switch($type)
		{
			case 'delete':
						 $this->db->trans_begin();
						 $this->$model_name->deleteData('id_emp_sett',$id,'employee_settings');
				           if( $this->db->trans_status()===TRUE)
						    {
						      $this->db->trans_commit();
							  $this->session->set_flashdata('chit_alert', array('message' => 'Disc limit deleted successfully','class' => 'success','title'=>'Delete Disc limit'));	
							  echo 1;
							}			  
						   else
						   {
							 $this->db->trans_rollback();
							 $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Delete Disc limit'));
							 echo 0;
						   }
						 	redirect('admin_employee/employee_settings/list');
				break;
			case 'empset_list':								
					$data = $this->$model_name->get_empset_lst();
					echo json_encode($data);
				break;
			case 'active_employee':
					$data = $this->$model_name->getActiveEmployee();  
					echo json_encode($data);
					break;
			case 'updateAccessTimeAll':
					$this->db->trans_begin(); 
					$updatedata = array(
						'access_time_from'  => $_POST['access_time_from'],
						'access_time_to'    => $_POST['access_time_to'],
						'updated_on'		=> date("Y-m-d H:i:s"),
						'updated_by'		=> $this->session->userdata('uid')
					);
					$this->$model_name->updEmpAccessTime($updatedata);	
					if($this->db->trans_status() == TRUE){		
						$this->db->trans_commit();			
						$log_data = array(
							'id_log'      => $this->session->userdata('id_log'),
							'event_date'  => date("Y-m-d H:i:s"),
							'module'      => 'Employee Settings',
							'operation'   => 'Update',
							'record'      => 'All',  
							'remark'      => 'Access time updated for all employees successfully'
						);
						$this->log_model->log_detail('insert','',$log_data); 
						$res = array("status" => TRUE, "message"=> "Updated Successfully" ,"class"=>"success","title"=>"Employee Settings");			
						echo json_encode($res);
					}else{
						$res = array("status" => FALSE, "message"=> "Sorry!! Unable to update your request.." ,"class"=>"failed","title"=>"Employee Settings");			
						echo json_encode($res);
					} 
					break;
			case 'list':
						$data['bulk_wastage_discount'] = $this->$model_name->get_ret_settings('bulk_wastage_discount');
						// echo"<pre>";print_r($data['bulk_wastage_discount']);exit;
						$data['dia_disc'] =$this->$model_name->get_dia_disc();
						$data['main_content'] = "master/employee/emp_set_list" ;
						$this->load->view('layout/template', $data);
						break;
			default:
						$set_model= self::SET_MODEL;
				     	$range['from_date']	= $this->input->post('from_date');
				        $range['to_date']	= $this->input->post('to_date');
						$id_employee	    = $this->input->post('id_employee');
					  	$emp_set 		    = $this->$model_name->ajax_emp_setting($range['from_date'],$range['to_date'],$id_employee);	
					  	$access 	        = $this->$set_model->get_access('admin_employee/employee_settings/list');
				        $data 		        = array(
				        					  'emp_set' => $emp_set,
											  'access'  => $access
				        				      );									  
						echo json_encode($data);
		}
	}
	function update_emp_data()
	{
		$model = self::EMP_MODEL;
		$id_employee = $this->input->post('id_employee');
		$reqdata   = $this->input->post('req_data'); 
		$success = 0;
		$failed = 0;
		if(!empty($reqdata) && sizeof($reqdata)>0 )
		{
			foreach($reqdata as $data){
				$this->db->trans_begin();
				if($data['create_new'] == 1){ //Add New emp settings
					$insdata = array(
							'id_employee'           =>$id_employee,
							//'id_emp_sett'           =>$data['id_emp_sett'],
							'disc_limit_type'       =>($data['disc_limit_type']!='' ?$data['disc_limit_type'] :NULL),
							'disc_limit'            =>($data['disc_limit']!='' ?$data['disc_limit'] :NULL),
							'max_silver_tol'            =>($data['max_silver_tol']!='' ?$data['max_silver_tol'] :50.00),
							'min_silver_tol'            =>($data['min_silver_tol']!='' ?$data['min_silver_tol'] :50.00),
							'max_gold_tol'            =>($data['max_gold_tol']!='' ?$data['max_gold_tol'] :50.00),
							'min_gold_tol'            =>($data['min_gold_tol']!='' ?$data['min_gold_tol'] :50.00),
							'allowed_old_met_pur'   =>($data['allowed_old_met_pur']!='' ?$data['allowed_old_met_pur'] :NULL),
							'bulk_wast_disc_limit'    =>($data['blk_wast_disc']!='' ?$data['blk_wast_disc'] :0),
							'dia_disc_limit'            =>($data['dia_disc_lmt']!='' ?$data['dia_disc_lmt'] :0),
							'allow_day_close'       =>$data['allow_day_close'],
							'allow_manual_rate'     =>$data['allow_manual_rate'],
							'access_time_from'      =>$data['access_time_from'],
							'access_time_to'       	=>$data['access_time_to'],
							'otp_dis_approval'      =>$data['otp_dis_approval'],
							'created_on'            =>date("Y-m-d H:i:s"),
							'created_by'            => $this->session->userdata('uid')
						);
					$insId = $this->$model->insertData($insdata,'employee_settings');
				// 	print_r($this->db->last_query());exit;
					if($this->db->trans_status() == TRUE){		
						$this->db->trans_commit();			
						$log_data = array(
							'id_log'      => $this->session->userdata('id_log'),
							'event_date'  => date("Y-m-d H:i:s"),
							'module'      => 'Employee Settings',
							'operation'   => 'Add',
							'record'      => $insId,  
							'remark'      => 'Record added successfully'
						);
						$this->log_model->log_detail('insert','',$log_data);
						$success++;
					}else{
						$this->db->trans_rollback();
						$failed++;
					}
				}
				else{ // Update Setings
					$updatedata=array(
						'disc_limit_type'	=> ($data['disc_limit_type']!='' ? $data['disc_limit_type']:NULL),
						'disc_limit'		=> ($data['disc_limit']!='' ? $data['disc_limit']:NULL),
						'max_silver_tol'            =>($data['max_silver_tol']!='' ?$data['max_silver_tol'] :50.00),
						'min_silver_tol'            =>($data['min_silver_tol']!='' ?$data['min_silver_tol'] :50.00),
						'max_gold_tol'            =>($data['max_gold_tol']!='' ?$data['max_gold_tol'] :50.00),
						'min_gold_tol'            =>($data['min_gold_tol']!='' ?$data['min_gold_tol'] :50.00),
						'allowed_old_met_pur'=>($data['allowed_old_met_pur']!='' ? $data['allowed_old_met_pur']:NULL),
						'bulk_wast_disc_limit'    =>($data['blk_wast_disc']!='' ?$data['blk_wast_disc'] :0),
						'dia_disc_limit'          =>($data['dia_disc_lmt']!='' ?$data['dia_disc_lmt'] :0),
						'allow_day_close'   => ($data['allow_day_close']?$data['allow_day_close']:0),
						'allow_manual_rate'   => ($data['allow_manual_rate']?$data['allow_manual_rate']:0),
						'otp_dis_approval'  => ($data['otp_dis_approval']?$data['otp_dis_approval']:0),
						'access_time_from'  => $data['access_time_from'],
						'access_time_to'    => $data['access_time_to'],
						'updated_on'		=> date("Y-m-d H:i:s"),
						'updated_by'		=> $this->session->userdata('uid')
					);
					$upd_emp = $this->$model->updateData($updatedata,'id_emp_sett',$data['id_emp_sett'],'employee_settings');	
					if($this->db->trans_status() == TRUE){		
						$this->db->trans_commit();			
						$log_data = array(
							'id_log'      => $this->session->userdata('id_log'),
							'event_date'  => date("Y-m-d H:i:s"),
							'module'      => 'Employee Settings',
							'operation'   => 'Update',
							'record'      => $data['id_emp_sett'],  
							'remark'      => 'Record edited successfully'
						);
						$this->log_model->log_detail('insert','',$log_data);
						$success++;
					}else{
						$this->db->trans_rollback();
						$failed++;
					}
				}
			}
		}			
		$res = array("message"=> "Updated ".$success." records successfully.".($failed > 0 ? $failed.' records failed..':'') ,"class"=>"success","title"=>"Employee Settings");		
		echo json_encode($res);  
	}
	
 //employee Active/Inactive Options hh//	
	
		  /* function update_referrals(){
			
			$model_name=self::EMP_MODEL;
			
			$empids=$this->$model_name->get_employee_records();
			$tot=count($empids);
			$count_ins_acc=array();
			foreach($empids as $ids){
				
				if($ids['id_employee']!='1'){
				$status=$this->$model_name->update_scheme_acc($ids['id_employee'],$ids['mobile']);				
				$count_ins_acc[]=$status;
				}
				
				
			}
			$total=(isset($count_ins_acc)?sizeof($count_ins_acc):0);
			if($tot>0 && $total>0){
				
			echo "<pre>";print_r($tot);echo "</pre>";
			echo "<pre>";print_r($total);echo "</pre>";exit;
			
				
			}
		 } */
		 
		  //lines added by Durga starts here 18.07.2023
		 function enable_device()
		 {
			$model=self::EMP_MODEL;
			
			$device_details = $_POST['enable_device'];

			$id_employee = $device_details['id_employee'];
				
			$app_type = $device_details['app_type'];
					
			$emp_device = $device_details['uuid'];
					
			$device_status = $device_details['enable_device'];
					
			$existing_device_data = $this->$model->get_employee_name_byid($device_details['id_employee'], $emp_device);

						$dir = 'log/device_log'.date('d-m-Y');
						
						if (!is_dir($dir)) {
						
							mkdir($dir, 0777, TRUE);
						
						}
						
						$log_path = $dir.'/device_log'.date('d-m-Y').'.txt';
						
			$device_logdata = "\n Existing --".date('Y-m-d H:i:s')." -- : ".json_encode($existing_device_data)."\n CurrentPost -- ".date('Y-m-d H:i:s')." -- : id_employee:".json_encode($device_details);
						
						file_put_contents($log_path,$device_logdata,FILE_APPEND | LOCK_EX); 
						
			$status = "";

			$message = "";
						
			$allow_update = false;

			$this->db->trans_begin();

			if($app_type == 1) {

				if($device_status == 1) {

					$chit_data=$this->$model->get_chit_settings_data();
				
					$device_enabledcount=$this->$model->get_enabled_device_count();
				
					if($device_enabledcount < $chit_data['chitCollectionEmpCount']) {

						$allow_update = true;
					
					} else {
					
						$allow_update = false;
					
					}
					
				} else {
					
					$allow_update = true;
					
				}

			} else if($app_type == 2) {

				if($device_status == 1) {

				$this->load->model("ret_estimation_model");

					$retail_setting = $this->ret_estimation_model->get_ret_settings('estimation_app_devices_count');

				$device_enabledcount = $this->$model->get_enabled_device_count(1);

				if($device_enabledcount < $retail_setting['value']) {

						$allow_update = true;

					} else {

						$allow_update = false;
						
					}
				
				} else {
				
					$allow_update = true;
				
				}

			}

			if($allow_update) {
	
				$this->$model->update_emp_device_status($emp_device, $device_status);

			if($this->db->trans_status() === TRUE) {

				$this->db->trans_commit();

					$status = "success";

					$message = "Device Enable Status Updated Successfully.";

				} else {

					$this->db->trans_rollback();

					$status = "danger";

					$message = "Error occured. Unable to proceed the requested operation.";

				}

			} else {

				$status = "danger";

				$message = "Device Enable limit reached.";

			}

			$this->session->set_flashdata('chit_info',array('message' => $message, 'class' => $status,'title' => 'Employee Device Status'));

			redirect('employee');
		 }
		 function get_emp_name_list()
		 {
			$model=self::EMP_MODEL;
			$empdata=$this->$model->get_emp_name_list();
			echo json_encode($empdata);
		 }
		 function get_device_list()
		 {
			$model=self::EMP_MODEL;
			$devicedata=$this->$model->get_device_list();
			echo json_encode($devicedata);
		 }
		 function get_employee_name_byid()
		 {
			$model=self::EMP_MODEL;
			$empdata=$this->$model->get_employee_name_byid();
			echo json_encode($empdata);
		 }
		 function emp_wallet_acc($emp_id){

			

			$wallmodel   = 'wallet_model';


	        $wallet_acc_no =  $this->$wallmodel->get_wallet_acc_number();				


			$insertData=array( 

						   'id_customer' 	   => NULL,

						   'idemployee' 	   =>  $emp_id,

						   'wallet_acc_number' => (isset($wallet_acc_no)?$wallet_acc_no:NULL),

						   'issued_date' 	   => date('y-m-d H:i:s'),

						   'remark' 		    => "Credits",

						   'active'		        => 1	                        

                           );

			           
			       $status = $this->$wallmodel->wallet_accountDB("insert","",$insertData);

		 }

        //lines added by Durga ends here 18.07.2023
		function get_employee_devices() {

			$model=self::EMP_MODEL;
	
			$emp_id = $this->input->post('id_employee');	
	
			$app_type = $this->input->post('app_type');	
			
			$empdata=$this->$model->get_employee_devices($emp_id, $app_type);
			
			echo json_encode($empdata);
	
		}

}



?>