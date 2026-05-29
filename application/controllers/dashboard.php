<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard extends CI_Controller {
	 
	const VIEW_FOLDER = 'chitscheme/';
	public function __construct()
    {
        parent::__construct();
        
        $this->load->model('login_model');
       
         $this->m_mode=$this->login_model->site_mode();
        if( $this->m_mode['maintenance_mode'] == 1) {
        	redirect("user/maintenance");
	    }
        
		if(!$this->session->userdata('username'))
		{
			redirect("/user/login");
		}
		$this->load->model('registration_model');
		$this->load->model('dashboard_modal');
		$this->load->model('scheme_modal');
		$this->load->model('services_modal');
		$this->scheme_status = $this->scheme_modal->scheme_status();
		$this->comp = $this->login_model->company_details();
		ini_set('date.timezone', 'Asia/Kolkata');
		//$this->load->model('chitfund_model');
    }
	public function index()
	{  
			$customer 		= $this->registration_model->customer_detail();
			$payHistory 	= $this->dashboard_modal->get_lastPaid();
			$schemes  		= $this->dashboard_modal->countSchemes();
			$total_amount	= $this->dashboard_modal->sumPayments();
			$payments		= $this->dashboard_modal->total_payments();
			$wallets  		= $this->dashboard_modal->countWallets();
			$wallet_balance = $this->dashboard_modal->wallet_balance();
			
			$dues  			= $this->countDues();
			$pdc    		= $this->dashboard_modal->get_pdcs();
			$currency  		= $this->dashboard_modal->get_currency();
			$closedAcc		= $this->dashboard_modal->total_closed_acc();					
			$exisRegReq     = $this->dashboard_modal->get_exisRegReq();
			$custComplaints = $this->scheme_modal->get_complints();
			$custDth        = $this->scheme_modal->get_dth();
			
			$chitSettings   = $this->scheme_modal->getChitSettings();					
			$reg_existing   = $chitSettings['reg_existing'];						
			$regExistingReqOtp  = $chitSettings['regExistingReqOtp'];
			$showClosed		= $chitSettings['show_closed_list'];
			$showCoinEnq	= $chitSettings['enable_coin_enq'];
			$compare_plan_img= $chitSettings['compare_plan_img'];
			
			$dboardData = array(
									'customer'      => $customer['profile'],
								    'profile_stat'  => (isset($customer['profile_stat'])?$customer['profile_stat']:''),
								    'payHistory'    => $payHistory,
								    'scheme_status' => $this->scheme_status,
								    'schemes'		=> $schemes,'payments'=>$payments,
								    'total_amount'	=> $total_amount,
								    'wallets'		=> $wallets,
								    'wallet_balance'=> $wallet_balance,
								    'dues'			=> $dues,
								    'pdc'			=> $pdc,
									'currency'		=> $currency,
									'showClosed'	=> $showClosed,
									'showCoinEnq'	=> $showCoinEnq,
									'closedAcc'		=> $closedAcc,	
									'reg_existing'	=> $reg_existing,
									'regExistingReqOtp'	=> $regExistingReqOtp,
									'exisRegReq'	=> count($exisRegReq),
									'allow_wallet'  => $this->comp['allow_wallet'],
									'custComplaints'  =>$custComplaints,
									'custDth'         =>$custDth,									
								    'compare_plan_img'		=> $compare_plan_img,
							    );
							    
			$pageType = array('page' => 'dashboard','currency_symbol'=>$this->comp['currency_symbol'],'company_name'=>$this->comp['company_name'],'mobile'=>$this->comp['mobile'],'phone'=>$this->comp['phone'],'email'=>$this->comp['email']);
			
			$data['header_data'] = $pageType;
			$data['footer_data'] = $pageType;
			$data['content'] 	 = $dboardData;
			$data['fileName']    = self::VIEW_FOLDER.'dashboard';
			$this->load->view('layout/template', $data);
	}
	
	function countDues()
	{
		$dues =0;
		$this->load->model('payment_modal');
		$payrec = $this->payment_modal->get_payment_details($this->session->userdata('username'));
		if(isset($payrec))
		{
			foreach($payrec['chits'] as $pay)
			{
				if($pay['allow_pay']=='Y')
				{
					$dues++;
				}
			}
		}
		
		return $dues;
	}
	
	public function getChitSummary($chitID)
	{
		echo $this->dashboard_modal->getChitSummary($chitID);
	}
}