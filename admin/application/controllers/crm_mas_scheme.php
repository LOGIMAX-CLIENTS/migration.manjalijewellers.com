<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Crm_mas_scheme extends CI_Controller
{
	const SCH_VIEW ="master/crm_scheme/";
	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
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
		$this->id_log =  $this->session->userdata('id_log');
		// Load Models
		$this->load->model("curd_model");
		$this->load->model("crm_mas_scheme_model");
		$this->load->model("admin_settings_model");
	}
	
	public function index(){
		$this->sch_list();
	}
	
	public function sch_list($msg="")
	{
		$data['message']=$msg;
		$data['main_content'] = self::SCH_VIEW.'list';
	    $this->load->view('layout/template', $data);
	}
	
	public function ajax_scheme_list()
	{
		$access= $this->admin_settings_model->get_access('scheme');
		$items=$this->crm_mas_scheme_model->get_schemes_list();	
		$scheme = array(
							'access' => $access,
							'data'   => $items
						);  
		echo json_encode($scheme);
	}  
	
	public function sch_form($type="",$id="")
	{
		$model= "crm_mas_scheme_model";
		$set_model="admin_settings_model";
		$pay_model = "payment_model";
		switch($type)
		{
			case 'Add': 			
				
				$limit= $this->$set_model->limitDB('get','1');
				$discount= $this->$set_model->discount_db('get','1');
				$count= $this->$model->scheme_count();
				$gst_data=$this->$set_model->get_gstsettings();
				//print_r($count);exit;
				if($limit['limit_sch']==1)
				{
					if($count < $limit['sch_max_count'])
					{
					 	$data['sch'] = $this->$model->empty_record();
					 	$data['discount'] = $discount;
						$data['gst'] = $gst_data;
	   					$data['main_content'] = self::SCH_VIEW."form" ;
	   					$this->load->view('layout/template', $data);
					}else
					{
					 	$this->session->set_flashdata('sch_info', array('message' => 'Scheme creation limit exceeded, Kindly contact Super Admin...','class' => 'danger','title'=>'Scheme creation'));
					 	redirect('scheme');
					}
				}else
				{
				 	$data['sch']=$this->$model->empty_record();
				 	$data['discount'] = $discount;
					$data['gst'] = $gst_data;
   					$data['main_content'] = self::SCH_VIEW."form" ;
   					$this->load->view('layout/template', $data);
				} 
				break;
				case 'Edit':
							
				break;
		}
	}
}
?>