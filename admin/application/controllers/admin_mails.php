<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_mails extends CI_Controller {
	const MAIL_VIEW = 'mails/';
	const ACC_MODEL = "account_model";
	const ADM_MODEL = "chitadmin_model";
	public function __construct()
    {
        parent::__construct();
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
	
	public function closing_request()
	{
		$model=	self::ACC_MODEL;
		$this->load->model($model);
		$data['accounts']=$this->$model->get_closing_request();
	    $data['main_content'] = self::MAIL_VIEW.'closing_request';
        $this->load->view('layout/template', $data);
	}	
	
	public function joining_request()
	{
		$model=	self::ADM_MODEL;
		$this->load->model($model);
		$data['requests']=$this->$model->get_join_requests();
	    $data['main_content'] = self::MAIL_VIEW.'joining_request';
        $this->load->view('layout/template', $data);
	}
	
	public function update_joining_status($status="",$id="")
	{
		$model=	self::ADM_MODEL;
		$this->load->model($model);
		$data=array('status'=>$status);
		 $this->db->trans_begin();
		   $this->$model->update_enquiry($data,$id);
		  
		   if( $this->db->trans_status()===TRUE)
		   {
			  $this->db->trans_commit();
			  $this->session->set_flashdata('chit_alert', array('message' => 'Enquiry status updated successfully','class' => 'success','title'=>'Enquiry'));
              
              redirect('mail/joining');
		   }			  
		   else
		   {
		   	 $this->db->trans_rollback();
		   	 $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Enquiry'));
		   	 redirect('mail/joining');
		   }
	}
}	
?>