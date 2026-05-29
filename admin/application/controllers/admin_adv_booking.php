<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_adv_booking extends CI_Controller

{
    const SET_MODEL = 'admin_settings_model';

    const VIEW_FOLDER = 'advance_booking/';

	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

        $this->load->model(self::SET_MODEL);
        
        $this->load->model('payment_model');


		if(!$this->session->userdata('is_logged'))

		{

			redirect('admin/login');

		}

		$access = $this->admin_settings_model->get_access('customer');

		if ($access['view']==0)

		 {

			redirect('admin/dashboard');

		}

	}

		

	public function index(){	


	}

    public function plan_set(){

        $data['main_content'] = self::VIEW_FOLDER.'plan_settings';

        $this->load->view('layout/template', $data);	
		   
    }

	public function plan_list(){

        $data['main_content'] = self::VIEW_FOLDER.'plan_list';

        $this->load->view('layout/template', $data);	
		   
    }
    
    
    
    public function lock_gold_view(){

        $data['main_content'] = self::VIEW_FOLDER.'adv_booking';

        $this->load->view('layout/template', $data);	
		   
    }

	 public function bookings_list()
    {   
    
         $data['message']=$msg;
         $data['main_content'] = self::VIEW_FOLDER.'booking_list';
         $this->load->view('layout/template', $data);
    }

     public function booked_acc_list()
        {   
    
         $data['message']=$msg;
         $data['main_content'] = self::VIEW_FOLDER.'booked_account_list';
         $this->load->view('layout/template', $data);
        }




}
?>	