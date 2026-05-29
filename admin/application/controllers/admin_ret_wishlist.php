<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_ret_wishlist extends CI_Controller

{

	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model('ret_wishlist_model');

		$this->load->model('admin_settings_model');

		$this->load->model("log_model");

			

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


	/**

	* Wishlist Functions Starts

	*/

	public function wishlist($type="",$id="") {

		$model = "ret_wishlist_model";

		switch($type) {

			case 'list':

				$data['main_content'] = "wishlist/list" ;

				$this->load->view('layout/template', $data);

				break;

			case 'supplier':

				$data['main_content'] = "wishlist/supplier" ;

				$this->load->view('layout/template', $data);

				break;

			case 'factsheet':

				$data['main_content'] = "wishlist/factsheet" ;

				$this->load->view('layout/template', $data);

				break;

			case 'enquiry':

				$data['main_content'] = "wishlist/enquiry" ;

				$this->load->view('layout/template', $data);

				break;

			case 'ajax_getwishlist':

				$type = $_POST['type'];

				//$id_branch  = $this->input->post('id_branch');

				$from_date = $_POST['from_date'];

				$to_date = $_POST['to_date'];

				$branch = $_POST['branch'];

				$employee = $_POST['employee'];

				$status = $_POST['status'];
				
				$list = $this->$model->get_wishlist_data($type, $from_date, $to_date, "", $status, $branch, $employee);	 
				
				$access = $this->admin_settings_model->get_access('admin_ret_wishlist/wishlist/list');
				
				$data = array(
								'list'  => $list,
								'access'=> $access
							);  
				
				echo json_encode($data);

				break;

		}

	}

	function followup_submit() {

		$datetime = date("Y-m-d H:i:s");

		$message = "";

		$title = "";

		$model = "ret_wishlist_model";

		/*echo "<pre>";
		print_r($_POST);
		exit;*/

		$id_wishlist_enq = $_POST['id_wishlist_enq'];

		$followup_type = $_POST['followup_type'];

		$followup_date = trim($_POST['followup_date']);

		$followup_remarks = trim($_POST['followup_remarks']);

		$type 	= trim($_POST['form_type']);

		$followup_employee = $this->session->userdata('uid');

		if($followup_type == 1) {

			$title = "Add Follow Up";

			$message = "Follow Up added successfully";

		} else if($followup_type == 2) {

			$title = "Convert Order";

			$message = "Convert Order updated successfully.";

		} else if($followup_type == 3) {

			$title = "Close Wishlist";

			$message = "Close Wishlist updated successfully.";

		} 

		if($id_wishlist_enq > 0 && $followup_type > 0 && $followup_date != "" && $followup_remarks != "") {

			if($followup_type == 1) {

				$ins_array = array(

					"id_wishlist_enq" => $id_wishlist_enq,
		
					"followup_date"   => date("Y-m-d", strtotime($followup_date)),

					"followup_remarks" => $followup_remarks,

					"followup_employee" => $followup_employee
		
				);

				$this->$model->insertData($ins_array,'ret_wishlist_enquiry_followup');

			} else if($followup_type == 2) {

				$upd_array = array(

					"status"		=> 2,
		
					"close_date"   => date("Y-m-d", strtotime($followup_date)),

					"remarks" => $followup_remarks,

					"close_employee" => $followup_employee,

					"updated_on"	=> $datetime,

					"updated_by"	=> $followup_employee
		
				);

				$this->$model->updateData($upd_array, 'id_wishlist', $id_wishlist_enq, 'ret_wishlist_enquiry');

			} else if($followup_type == 3) {

				$upd_array = array(

					"status"		=> 3,
		
					"close_date"   => date("Y-m-d", strtotime($followup_date)),

					"remarks" => $followup_remarks,

					"close_employee" => $followup_employee,

					"updated_on"	=> $datetime,

					"updated_by"	=> $followup_employee
		
				);

				$this->$model->updateData($upd_array, 'id_wishlist', $id_wishlist_enq, 'ret_wishlist_enquiry');

			}

			if($this->db->trans_status()===TRUE) {

				$this->db->trans_commit();

				$this->session->set_flashdata('chit_alert',array('message'=> $message ,'class'=>'success','title'=> $title));
			
			} else {

				$this->db->trans_rollback();
			
				$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=> $title));

			}

		} else {
			
			$this->session->set_flashdata('chit_alert',array('message'=>'Required fields are missing!','class'=>'danger','title'=> $title));
		}

		if($type == 1) {

			redirect('admin_ret_wishlist/wishlist/list');

		} else if($type == 2) {
			
			redirect('admin_ret_wishlist/wishlist/supplier');

		} else if($type == 3) {
			
			redirect('admin_ret_wishlist/wishlist/factsheet');

		} else if($type == 4) {
			
			redirect('admin_ret_wishlist/wishlist/enquiry');

		} else {

			redirect('admin_ret_wishlist/wishlist/list');

		}
	}

}	

?>