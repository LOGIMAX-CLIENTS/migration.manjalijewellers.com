<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Admin_dashboard extends CI_Controller {



	



	const VIEW_FOLDER = 'dashboard/';



	const DAS_MODEL = 'dashboard_model';

	

	const ACC_MODEL = 'Account_model';



	const EMP_MODEL = 'employee_model';



	const REP_VIEW = 'reports/';

     

    const ACC_VIEW = 'account/scheme_reg/'; //scheme_reg

    

    const SERV_MODEL = 'services_model';

	

	const CUS_IMG_PATH = 'assets/img/customer/';

	const DEF_CUS_IMG_PATH = 'assets/img/default.png/';

	const DEF_IMG_PATH = 'assets/img/no_image.png/';

	const CUS_IMG= 'customer.jpg';

	const PAN_IMG = 'pan.jpg';

	const RATION_IMG = 'rationcard.jpg';

	const VOTERID_IMG = 'voterid.jpg';
	const APK_UPLAOD_PATH = 'master/upload_apk/';
	



	public function __construct()



    {



        parent::__construct();



        if(!$this->session->userdata('is_logged'))



        {



			redirect('admin/login');



		}



		$this->load->model(self::DAS_MODEL);

		

		$this->load->model(self::SERV_MODEL);



		$this->load->model(self::EMP_MODEL);

		

		$this->load->model(self::ACC_MODEL);



	}


	public function dashboard()

	{

        $this->session->unset_userdata('dashboard_branch');

		$id_branch = $this->input->post('id_branch');

		$this->session->set_userdata('dashboard_branch',$id_branch);
		
		$data['one_pending'] = $this-> get_closed('1');

		$data['two_pending'] = $this-> get_closed('2');
		
		$data['renewal'] = $this->get_renewals('renewals');
		
		$data['existing_request'] = $this->get_existing_request();
		
		$data['feedback_count']    = $this->get_feedback();
		
		echo json_encode($data);

	}
	
	public function index(){

		$access = $this->admin_settings_model->get_access('admin/dashboard');

		$dashboard_access=$this->admin_settings_model->get_dashboard_access();

		//print_r($dashboard_access);exit;

		$data['access']=$access['view'];

		$data['dash_access']=$dashboard_access;

        $data['customer']= $this->customer_stat();

	    $data['birthday'] = $this->cus_birthday();           // customer wedding and birthday dates

		$data['wedding'] = $this->cus_wedding_day();              // customer wedding and birthday dates
		
// 		print_r($data);exit;
	
		
	/*	
	
	    18-10-2023 #AB ....donot uncomment this part.... dashboard data will be loaded by ajax call....
	
	    admin_dashboard/dashboard  --> function will be called by ajax for page load, branch filter, date filter
	    
	    admin_dashboard/index      --> customer, birthday, wedding data will be loaded here
	    
	*/
	
	
	/*  $data['one_pending'] = $this-> get_closed('1');

		$data['two_pending'] = $this-> get_closed('2');
		
		$data['renewal'] = $this->get_renewals('renewals');
		
		$data['existing_request'] = $this->get_existing_request(); //get_existing_request

        $data['account']= $this->account_stat();

		$data['scheme_count']    = $this->dashboard_model->get_scheme();

		$data['feedback_count']    = $this->dashboard_model->get_enquiry();

		$data['group_count']    = $this->dashboard_model->get_scheme_group();

		$data['payment'] = $this->payment_stat();
		
		$data['closed'] = $this->get_closed_accounts();

		$data['pdc'] = $this->pdc_stat();

		$data['due'] = $this->due_stat();

		$data['wallets'] = $this->get_total_wallets();

		$data['inter_wallet'] = $this->inter_wallet();

		$data['inter_wallet_accounts'] = $this->inter_wallet_accounts();

		$data['inter_wallet_accounts_woc'] = $this->inter_wallet_accounts_woc(); 

		$data['pay_collection'] = $this->paydatewise_schemecoll_list(date('Y-m-d'));

		*/

		$data['main_content'] = self::VIEW_FOLDER.'dashboard';

     	$dashboard_branch=$this->session->userdata('dashboard_branch');

	    $this->load->view('layout/template', $data);

	}



   

	



	public function customer_stat()



	{



		  $model = self::DAS_MODEL;



		 $data['all_reg']   = $this->$model->reg_stat('ALL');

		



		 

		// $data['wo_acc']   	= $this->$model->cust_wo_acc();

		 

		// $data['wo_pay']   	= $this->$model->acc_wo_pay();



		 $data['today_reg'] = $this->$model->reg_stat('T');



      	 $data['yes_reg']	= $this->$model->reg_stat('Y');



      	 $data['wk_reg']    = $this->$model->reg_stat('TW');



      	 $data['m_reg']     = $this->$model->reg_stat('TM');



         $data['pc_reg']    = $this->$model->reg_stat('PC');



         $data['pi_reg']    = $this->$model->reg_stat('PI');

         

         $data['mob']     = $this->$model->customer_join('MOB');



         $data['web']    = $this->$model->customer_join('WEB');



         $data['admin']    = $this->$model->customer_join('ADMIN');

         $data['collection']    = $this->$model->customer_join('COLLECTION');



         $inc_calc = ( $data['all_reg']!=0 ? (($data['m_reg']/$data['all_reg'])*100):0 );



         $data['increase'] =  number_format($inc_calc,2);



        return $data;  



	}



	



	public function inter_wallet()



	{



    	 $model = self::DAS_MODEL;



		 $data['total_trans']   = $this->$model->inter_wallet('ALL');

		 

		

      	 $data['t_trans'] = $this->$model->inter_wallet('T');



      	 $data['y_trans']	= $this->$model->inter_wallet('Y');



      	 $data['tw_trans']    = $this->$model->inter_wallet('TW');



      	 $data['tm_trans']     = $this->$model->inter_wallet('TM');



      	$data['t_redeem_trans'] = $this->$model->inter_wallet('TR');



      	 $data['y_redeem_trans']	= $this->$model->inter_wallet('YR');



      	 $data['tw_redeem_trans']    = $this->$model->inter_wallet('TWR');



      	 $data['tm_redeem_trans']     = $this->$model->inter_wallet('TMR');

		 

        

		 return $data; 



	}



	public function account_stat()



	{



    	 $model = self::DAS_MODEL;

		



		 $data['all_reg']   = $this->$model->account_stat('ALL');

		 

		 $data['acc_wo_pay']  = $this->$model->acc_wo_pay();



      	 $data['today_reg'] = $this->$model->account_stat('T');



      	 $data['yes_reg']	= $this->$model->account_stat('Y');



      	 $data['wk_reg']    = $this->$model->account_stat('TW');



      	 $data['m_reg']     = $this->$model->account_stat('TM');

		 

		 $data['pc_reg']    = $this->$model->account_stat('PC');



         $data['pi_reg']    = $this->$model->account_stat('PI');



         $data['mob']     = $this->$model->account_join('MOB');



         $data['web']    = $this->$model->account_join('WEB');



         $data['admin']    = $this->$model->account_join('ADMIN');

         

          $data['collection']    = $this->$model->account_join('COLLECTION');



         $inc_calc = ( $data['all_reg']!=0 ? (($data['m_reg']/$data['all_reg'])*100):0 );



         $data['increase'] =  number_format($inc_calc,2);//print_r($data);



		 return $data; 



	}



	

	



	public function payment_stat()



	{







		 $model = self::DAS_MODEL;



	     $data['all_pay_old'] = $this->$model->pay_stat('ALL');





	     $data['all_pay'] = $this->$model->pymt_status('ALL');



      	 $data['today'] = $this->$model->pymt_status('T');



      	 $data['yesterday']   = $this->$model->pymt_status('Y'); 



      	 $data['week']   = $this->$model->pymt_status('TW'); 



      	 $data['month']   = $this->$model->pymt_status('TM'); 

      	 

      	 $data['awaiting']   = $this->$model->awaiting_pymt();

      	  

      	 $data['admin_paid']   = $this->$model->payment_join('ADMIN'); 

      	 

      	 $data['web_paid']   = $this->$model->payment_join('WEB'); 

      	 

      	 $data['mob_paid']   = $this->$model->payment_join('MOB'); 

      	 

      	 $data['collection_paid']   = $this->$model->payment_join('COLLECTION'); 



      	 //$pa_pay   = $this->$model->pay_stat('PA'); 



      	 //$pr_pay   = $this->$model->pay_stat('PR'); 



      	 //$pp_pay   = $this->$model->pay_stat('PP'); 



		 $data['total_paid'] =0;



		 //dues



		 $data['total_unpaid']=0;



		 $data['schemewise']= $this->$model->schemewise_payment();



		



		 foreach ( $data['schemewise'] as $pay)



		 {



			 if($pay['paid'])



			 {



				 $data['total_paid'] += $pay['paid'];



			 }



			 



			 if($pay['unpaid'])



			 {



				 $data['total_unpaid'] += $pay['unpaid'];



			 }



		 }



		 



		 // paid and unpaid percentage



		 $paid_avg   = ( ($data['all_pay']['paid'] + $data['all_pay_old']['unpaid'])!=0 ? (($data['all_pay']['paid']/( $data['all_pay']['paid'] + $data['all_pay_old']['unpaid'])) * 100):0 );



		 



		 $unpaid_avg = (( $data['all_pay']['paid'] +  $data['all_pay_old']['unpaid'])!=0 ? (( $data['all_pay_old']['unpaid']/( $data['all_pay']['paid'] +  $data['all_pay_old']['unpaid'])) * 100):0);



		 



		 $data['paid_avg']   = number_format($paid_avg,2,".","");



		 $data['unpaid_avg'] = number_format($unpaid_avg,2,".","");//echo "<pre>";print_r($data);echo "</pre>";

		 

		 //echo "<pre>"; print_r($data);exit; echo "<pre>";



		 return $data;



		 



	}



	



	public function pdc_stat()



	{



		$model = self::DAS_MODEL;



	     //presentable



	     $data['chq_yp'] = $this->$model->pdc_report('Y','CHQ',7);



	     $data['chq_tp'] = $this->$model->pdc_report('T','CHQ',7);



	     $data['chq_wkp'] = $this->$model->pdc_report('TW','CHQ',7);



	     $data['chq_tmp'] = $this->$model->pdc_report('TM','CHQ',7);	   



	     $data['ecs_yp'] = $this->$model->pdc_report('Y','ECS',7);



	     $data['ecs_tp'] = $this->$model->pdc_report('T','ECS',7);



	     $data['ecs_wkp'] = $this->$model->pdc_report('TW','ECS',7);



	     $data['ecs_tmp'] = $this->$model->pdc_report('TM','ECS',7);



	     $data['chq_tt_prestable'] = $this->$model->pdc_report('TT','CHQ',7);



	     $data['ecs_tt_prestable'] = $this->$model->pdc_report('TT','ECS',7);



	     



	     //presented



	     $data['chq_ys'] = $this->$model->pdc_report('Y','CHQ',2);



	     $data['chq_ts'] = $this->$model->pdc_report('T','CHQ',2);



	     $data['chq_wks'] = $this->$model->pdc_report('TW','CHQ',2);



	     $data['chq_tms'] = $this->$model->pdc_report('TM','CHQ',2);



	     $data['ecs_ys'] = $this->$model->pdc_report('Y','ECS',2);



	     $data['ecs_ts'] = $this->$model->pdc_report('T','ECS',2);



	     $data['ecs_tws'] = $this->$model->pdc_report('TW','ECS',2);



	     $data['ecs_tms'] = $this->$model->pdc_report('TM','ECS',2);



	     $data['chq_tt_prestd'] = $this->$model->pdc_report('TT','CHQ',2);



	     $data['ecs_tt_prestd'] = $this->$model->pdc_report('TT','ECS',2);



	   



      	 return $data;



      	 



	}



	

      public function get_existing_request() //get_existing_request//

  {

	     $model = self::ACC_MODEL;





		$dashboard_model = self::DAS_MODEL;

		

		 $data['reg_existing']   = $this->$dashboard_model->reg_existing();





		 $data['all_reg']   = $this->$dashboard_model->req_stat('ALL');

		 



		 $data['today_reg'] = $this->$dashboard_model->req_stat('T');



      	 $data['yes_reg']	= $this->$dashboard_model->req_stat('Y');



      	 $data['wk_reg']    = $this->$dashboard_model->req_stat('TW');



      	 $data['m_reg']     = $this->$dashboard_model->req_stat('TM');





        



		 $data['total_request'] = $this->$model->get_existingSchRequests_dashboard('3');

		 

		 $data['exiting_processing'] = $this->$model->get_existingSchRequests_dashboard('0');

		 

		 $data['exiting_approved'] = $this->$model->get_existingSchRequests_dashboard('1');

		 

		 $data['exiting_rejected'] = $this->$model->get_existingSchRequests_dashboard('2');



	  $inc_calc = ( $data['all_reg']!=0 ? (($data['m_reg']/$data['all_reg'])*100):0 );



	  $data['increase'] =  number_format($inc_calc,2);

	

	//	print_r($data);exit;

		

      return $data; 

 

 

  }

	//get_existing_request//







public function due_stat()



	{



    	 $model = self::DAS_MODEL;



		 $data['today_due'] = $this->$model->due_stat('T');

		 

		 $data['yesterday_due'] = $this->$model->due_stat('Y');



		$data['week_due']    = $this->$model->due_stat('TW');

      	 

		 $data['month_due']    = $this->$model->due_stat('TM');

		 

		 $data['all_due']    = $this->$model->due_stat('ALL');



      	 //print_r($data);



		 return $data; 



	}





	



	//  detailed reports



	



	public function reg_detail($type)



	{

		//print_r($_POST);exit;

		$model=	self::DAS_MODEL;

		

		$data['print']['company_name'] = $this->session->userdata('company_name');

		

		$data['print']['branch_name'] = $this->session->userdata('branch_name');



		$data['customer']=$this->$model->reg_detail_stat(strtoupper($type));	

		//print_r($data['customer']);exit;	

		$data['main_content'] = self::REP_VIEW.'detailed/customer';



        $this->load->view('layout/template', $data);

	

		



	}

	

	public function customer_detail_bydate()

	{

		$model=	self::DAS_MODEL;

		$from_date=$this->input->post('from_date');

		$to_date=$this->input->post('to_date');

		$data['print']['company_name'] = $this->session->userdata('company_name');

		

		$data['print']['branch_name'] = $this->session->userdata('branch_name');

		$data['customer']=$this->$model->customer_detail_bydate($from_date,$to_date);			

		//print_r($data['customer']);exit;

		//$data['main_content'] = self::REP_VIEW.'detailed/customer';

		echo json_encode($data);

	}

	



	public function acc_detail($type)



	{



		



		$model=	self::DAS_MODEL;



		$data['accounts']=$this->$model->acc_detail_stat(strtoupper($type));			



		$data['main_content'] = self::REP_VIEW.'detailed/account';



        $this->load->view('layout/template', $data);



	}

	

	/*coded by vishnu /*/

	

	

	public function cust_wo_acc_details()		

	{	

      	

		$model=	self::DAS_MODEL;

		

		$data['print']['company_name'] = $this->session->userdata('company_name');

		

		$data['print']['branch_name'] = $this->session->userdata('branch_name');

		

		$data['customer']= $this->$model->cust_wo_acc_details();

		

		$data['main_content'] = self::REP_VIEW.'detailed/customer';



        $this->load->view('layout/template', $data);



	}

	/*coded by vishnu /*/

	

	

	

	public function cust_wo_accounts_details()		



	{	$models=self::ACC_MODEL;

		

		$model=	self::DAS_MODEL;

		

		$this->db->trans_begin();	

		

			$acc_detail= $this->$model->cust_wo_acc_details();



			// echo "<pre>";print_r($acc_detail);echo "</pre>";exit;

			

			$data = array();

			

		if($this->db->trans_status()===TRUE)

		{

			$this->db->trans_commit();	

			

					foreach ($acc_detail as $account)

					{

							

							$result=$this->$models->get_all_closed_accdetails($account['id_customer']);	

							

							$acc_count=$this->$models->get_all_closed_acccount($account['id_customer']);

							

								$data['customer'][]= array(

						

											'id_customer'    => (isset($account['id_customer'])?$account['id_customer']:0),

											'name'    		 => (isset($account['name'])?$account['name']:0),

											'mobile'         => (isset($account['mobile'])?$account['mobile']:0),

											'is_new'    	 => (isset($account['is_new'])?$account['is_new']:0),

											'date_add'       => (isset($account['date_add'])?$account['date_add']:0),

											'reg_by'         => (isset($account['reg_by'])?$account['reg_by']:0),								

											'closing_balance' => (isset($result['closing_balance'])?$result['closing_balance']:(isset($result['amount'])?$result['amount']:'-')),

											'closing_date'    => (isset($result['closing_date'])?$result['closing_date']:'-'),

											'closed_a/c'   	  => (isset($acc_count)?$acc_count:0),

											'profile_complete' => (isset($account['profile_complete'])?$account['profile_complete']:0),

											'active'           => (isset($account['active'])?$account['active']:0)

											);

										

					}

			

			echo json_encode($data);	

		}				



	}



	

	



	

/*Coded by ARVK*/

	

	

	public function acc_wo_pay_details()



	{

		$model=	self::DAS_MODEL;

		

		$data['print']['company_name'] = $this->session->userdata('company_name');

		

		$data['print']['branch_name'] = $this->session->userdata('branch_name');

		

		$data['accounts']	= $this->$model->acc_wo_pay_details();



      	$data['main_content'] = self::REP_VIEW.'detailed/account';



        $this->load->view('layout/template', $data);



	}

	

	public function total_payment_details()



	{

		$model=	self::DAS_MODEL;

		

		$data['accounts']	= $this->$model->total_payment_details();



	   // echo "<pre>"; print_r($data);exit; echo "<pre>";

      	$data['main_content'] = self::REP_VIEW.'detailed/payment';



        $this->load->view('layout/template', $data);



	}



/* / Coded by ARVK*/



	public function closed_acc_detail($type)



	{



		$model=	self::DAS_MODEL;



		$data['closed']=$this->$model->closed_acc_stat(strtoupper($type));			



		$data['main_content'] = self::REP_VIEW.'detailed/closed_account';



        $this->load->view('layout/template', $data);



	}



	



	public function about_to_close($type)



	{



		$model=	self::DAS_MODEL;



		$data['closed']=$this->$model->closed_acc_detail_stat(strtoupper($type));			



		$data['main_content'] = self::REP_VIEW.'detailed/about_to_close';



        $this->load->view('layout/template', $data);



	}



	



	public function pay_detail($type)



	{



		$model=	self::DAS_MODEL;



		$data['accounts']=$this->$model->pay_detail_stat(strtoupper($type));			



		$data['main_content'] = self::REP_VIEW.'detailed/payment';



        $this->load->view('layout/template', $data);

		

		



	}	





	public function inter_wallet_details($type)



	{







		$model=	self::DAS_MODEL;



		

		 $data['accounts']=$this->$model->inter_wallet_detail_stat(strtoupper($type));	



		 // print_r($data);exit;		



		$data['main_content'] = self::REP_VIEW.'detailed/inter_wallet';



        $this->load->view('layout/template', $data);



	}	





	// public function ajax_inter_wallet_details($type,$from_date,$to_date)



	// {





	//  $data['accounts']=$this->$model->inter_wallet_detail_stat(strtoupper($type));	



	//  echo $data;		



	// }	



	public function awaiting_detail()



	{



		$model=	self::DAS_MODEL;



		$data['accounts']=$this->$model->await_detail_stat();			



		$data['main_content'] = self::REP_VIEW.'detailed/payment';



        $this->load->view('layout/template', $data);



	}	



	



	public function paid_unpaid_status($filterBy,$id_scheme="")



	{



		$model=	self::DAS_MODEL;



		$data['accounts'] = $this->$model->paid_unpaid_detail($filterBy,$id_scheme);



		$data['main_content'] = self::REP_VIEW.'detailed/paid_due';//echo "<pre>";print_r($data);echo "</pre>";



        $this->load->view('layout/template', $data);



		



	}



public function due_list($filterBy)



	{



		$model=	self::DAS_MODEL;

		

		$data['print']['company_name']=$this->session->userdata(company_name);

		

		$data['print']['branch_name']=$this->session->userdata(branch_name);



		$data['accounts'] = $this->$model->due_list($filterBy);



		$data['main_content'] = self::REP_VIEW.'detailed/unpaid_due';//echo "<pre>";print_r($data);echo "</pre>";



        $this->load->view('layout/template', $data);



		



	}



	



	public function postdated_pay_detail($filterBy,$mode,$status)



	{



		$data['main_content'] = self::REP_VIEW.'detailed/post_payment';



        $this->load->view('layout/template', $data);



   	}



	



	function ajax_get_ratestat()



	{



		$model=	self::DAS_MODEL;



		$data = $this->$model->rateWeekStat();



		echo json_encode($data);



	}



	



	function get_total_wallets()



	{



		$model=	self::DAS_MODEL;



		return $this->$model->total_wallets();



	}



	



	function get_closed_accounts()



	{



		$model=	self::DAS_MODEL;



		return $this->$model->total_closed_accounts();



	}



		



	function get_closed($type)



	{



		$model=	self::DAS_MODEL;



		return $this->$model-> total_abt_to_cls($type);



	}



		function get_feedback()



	{



		$model=	self::DAS_MODEL;



		return $this->$model->get_enquiry();



	}



	function get_renewals($type)



	{



		$model=	self::DAS_MODEL;



		return $this->$model->renewal_stat(strtoupper($type));



	}



	



	public function get_renewals_list($type)



	{



		$model=	self::DAS_MODEL;



		$data['accounts']=$this->$model->renewal_stat(strtoupper($type));	



		$data['main_content'] = self::REP_VIEW.'detailed/renewal';



        $this->load->view('layout/template', $data);



	}

	

	function inter_wallet_accounts()



	{



		$model=	self::DAS_MODEL;



		$inter_wallet_accounts= $this->$model->inter_wallet_accounts();

		

		return $inter_wallet_accounts;



	}



	function inter_wallet_accounts_woc()



	{



		$model=	self::DAS_MODEL;



		$inter_wallet_accounts_woc= $this->$model->inter_wallet_accounts_woc();

		

		return $inter_wallet_accounts_woc;



	}

	

	public function inter_wallet_accounts_detail()



	{

		

		

		

		$model=	self::DAS_MODEL;

		

		

		$data['accounts']=$this->$model->inter_wallet_accounts_detail();	

		



		$data['main_content'] = self::REP_VIEW.'detailed/inter_wallet_account';



        $this->load->view('layout/template', $data);



	}

	 function inter_wallet_accounts__woc($from_date,$to_date)

	{



		$model=	self::DAS_MODEL;



		$data['accounts']=$this->$model->inter_wallet_woc($from_date,$to_date);	



		$data['main_content'] = self::REP_VIEW.'detailed/inter_wallet_accounts';



        $this->load->view('layout/template', $data);

		

	}

	

	function inter_wallet_accounts__woc_det()

	{

		$model=	self::DAS_MODEL;



		$data['accounts']=$this->$model->inter_wallet_woc();	



		echo json_encode($data);

		

	}

	

	function payment_status()

	{

		$from_date=$this->input->post('from_date');

		$to_date=$this->input->post('to_date');

		$date_type=$this->input->post('date_type');


		$model=	self::DAS_MODEL;

		$data['payment_status']=$this->$model->payment_status($from_date,$to_date,$date_type);



		 $data['admin_paid']   = $this->$model->payment_join_through('ADMIN',$from_date,$to_date,$date_type); 

      	 

      	 $data['web_paid']   = $this->$model->payment_join_through('WEB',$from_date,$to_date,$date_type); 

      	 

      	 $data['mob_paid']   = $this->$model->payment_join_through('MOB',$from_date,$to_date,$date_type); 

      	 

      	 $data['collection_paid']   = $this->$model->payment_join_through('COLLECTION',$from_date,$to_date,$date_type); 





		 echo json_encode($data);

	}







	function account_status()

	{

		$model=	self::DAS_MODEL;



		$from_date=$this->input->post('from_date');



		$to_date=$this->input->post('to_date');

		

		$id_branch=$this->input->post('id_branch');

		$date_type=$this->input->post('date_type');

		$data['account_stat'] = $this->$model->account_status($from_date,$to_date,$date_type);



		$data['acc_wo_pay']  = $this->$model->acc_wo_payment($from_date,$to_date,$date_type);





         $data['mob']     = $this->$model->account_join_list('MOB',$from_date,$to_date,$date_type);



         $data['web']    = $this->$model->account_join_list('WEB',$from_date,$to_date,$date_type);



         $data['admin']    = $this->$model->account_join_list('ADMIN',$from_date,$to_date,$date_type);

         

         $data['collection']    = $this->$model->account_join_list('COLLECTION',$from_date,$to_date,$date_type);



       // print_r($data);exit;



		echo json_encode($data);



	}

	



	function get_payment($id="",$from_date="",$to_date="",$type="")

	{

			

			

		

		$model=	self::DAS_MODEL;

		

		$data['print']['company_name'] = $this->session->userdata('company_name');

		

		$data['print']['branch_name'] = $this->session->userdata('branch_name');

		

		$data['print']['from_date'] = $from_date;

		

		$data['print']['to_date'] = $to_date;

		

		$data['accounts']=$this->$model->get_payment_list($id,$from_date,$to_date,$type);	



		$data['main_content'] = self::REP_VIEW.'detailed/payment';



        $this->load->view('layout/template', $data);



	}



	function get_payment_joined($from_date="",$to_date="",$type="")

	{

		

		$model=	self::DAS_MODEL;

		

		$data['print']['company_name'] = $this->session->userdata('company_name');

		

		$data['print']['branch_name'] = $this->session->userdata('branch_name');

		

		$data['print']['from_date'] = $from_date;

		

		$data['print']['to_date'] = $to_date;



		$data['accounts']=$this->$model->payment_join_through_list($from_date,$to_date,$type);	



		$data['main_content'] = self::REP_VIEW.'detailed/payment';



        $this->load->view('layout/template', $data);



	}



	function get_account($id="",$from_date="",$to_date="",$type="")

	{

		

		$model=	self::DAS_MODEL;

		

		$data['print']['company_name']=$this->session->userdata(company_name);

		

		$data['print']['branch_name']=$this->session->userdata(branch_name);

		

		$data['print']['to_date']=$to_date;

		

		$data['print']['from_date']=$from_date;

		



		$data['accounts']=$this->$model->get_account_list($id,$from_date,$to_date,$type);	







		$data['main_content'] = self::REP_VIEW.'detailed/account';



        $this->load->view('layout/template', $data);



	}

	

	function ajax_get_account()   

	{

	 

	    $id = $_POST['id_branch'];

	    

	    $from_date = $_POST['from_date'];

	    

	    $to_date = $_POST['to_date'];

	    

		$model=	self::DAS_MODEL;



		$data['accounts'] =$this->$model->get_account_list($id,$from_date,$to_date);	



        echo json_encode($data);



		//$data['main_content'] = self::REP_VIEW.'detailed/account';



        //$this->load->view('layout/template', $data);



	}



	function ajax_get_account_joined()

	{

		

		$model=	self::DAS_MODEL;

        

        $from_date = $_POST['from_date'];

	    

	    $to_date = $_POST['to_date'];

	    

	    $type = $_POST['added_by'];

	    

		$data['accounts']=$this->$model->get_account_joined($from_date,$to_date,$type);	



		 echo json_encode($data);



	}





     function ajax_get_payment_joined()

    	{

    		

    		$model=	self::DAS_MODEL;

    	    

    	    $from_date = $_POST['from_date'];

    	    

    	    $to_date = $_POST['to_date'];

    	    

    	    $type = $_POST['added_by'];

    

    		$data['payments'] =$this->$model->payment_join_through_list($from_date,$to_date,$type);	

    

            echo json_encode($data);

    

    	}



function get_account_joined($from_date,$to_date,$type="")

	{

		

		$model=	self::DAS_MODEL;

		

		$data['print']['company_name'] = $this->session->userdata('company_name');

		

		$data['print']['branch_name'] = $this->session->userdata('branch_name');



		$data['print']['from_date'] = $from_date;



		$data['print']['to_date'] = $to_date;



		$data['accounts']=$this->$model->get_account_joined($from_date,$to_date,$type);	



		$data['main_content'] = self::REP_VIEW.'detailed/account';



        $this->load->view('layout/template', $data);



	}



	function inter_wallet_status()

	{

		$from_date=$this->input->post('from_date');

		$to_date=$this->input->post('to_date');



		$model=	self::DAS_MODEL;

		$credit = $this->$model->inter_wallet_credit($from_date,$to_date);

		$debit = $this->$model->inter_wallet_redeem($from_date,$to_date);

		$branch  = $this->$model->allBranches(); 

	   // $creditAndDebit1 = $this->$model->interCreditAndDebit($from_date,$to_date);

	    

        $i = 0;

        foreach($branch as $br){

        	$creditAndDebit[$i]['branch_name'] = $br['branch_name'];

        	$creditAndDebit[$i]['id_branch'] = $br['id_branch'];

        	$creditAndDebit[$i]['credit'] = 0;

        	$creditAndDebit[$i]['debit'] = 0;

        	

        	foreach($credit as $ckey => $cval)

        	{

        		if ( $cval['id_branch'] === $br['id_branch'] )

        			$creditAndDebit[$i]['credit'] = $cval['total']; 

        			$creditAndDebit[$i]['currency_symbol'] = $cval['currency_symbol']; 

        	}

        	foreach($debit as $key => $dval)

        	{

        		if ( $dval['id_branch'] === $br['id_branch'] )

        			$creditAndDebit[$i]['debit'] = $dval['total']; 

        			$creditAndDebit[$i]['currency_symbol'] = $dval['currency_symbol']; 

        	}

        	$i++;

        }

        

//echo "<pre>";print_r($creditAndDebit);

		 echo json_encode($creditAndDebit);

	}



	public function inter_wallet_transcation_details($id_branch="",$from_date="",$to_date="",$type="")



	{







		$model=	self::DAS_MODEL;



		

		 $data['accounts']=$this->$model->inter_wallet_detail($id_branch,$from_date,$to_date,$type);	



		  



		$data['main_content'] = self::REP_VIEW.'detailed/inter_wallet';



        $this->load->view('layout/template', $data);



	}

	

	function ajax_collectionData(){  

		$date = ($this->input->post('date')=="" ? date('Y-m-d'):$this->input->post('date'));  

    	$collection = $this->paydatewise_schemecoll_list($date);

    	$total = 0;

    	/*foreach($data['pay_collection'] as $payment){

    	    $total = $total+$payment['collection']+$payment['opening_bal'];

    	} */

    	$result['pay_collection'] = $collection;

    	//$result['total_collection'] = $total;

    	echo json_encode($result);

    }

	

	function paydatewise_schemecoll_list($filterdate)

	{

		$model=	self::DAS_MODEL;  

		$items = $this->$model->paydatewise_schemecoll($filterdate); 

		return $items;

    }

	function regisert_list()

	{

		/*$from_date=$this->input->post('from_date');

		$to_date=$this->input->post('to_date');*/

		$model=	self::DAS_MODEL;

		

		$data['inter_wallet_accounts']= $this->$model->inter_wallet_accounts();

		$data['inter_wallet_accounts_woc']= $this->$model->inter_wallet_accounts_woc();



		 echo json_encode($data);

	}

	

	function ajax_daily_collection()

	{

	    

	   

		$model=	self::SERV_MODEL;

		$postDate = ($this->input->post('date')=="" ? date('Y-m-d'):$this->input->post('date'));  

    	$branch = $this->$model->allBranches();

		$yesterday = date('Y-m-d', strtotime($postDate .' -1 day'));

		//echo $yesterday;

		if($postDate == date('Y-m-d')){ // If filter date is today

		    $type = 1;

    		 

        	foreach ($branch as $br){

    		    $ydaytoday = $this->$model->daily_collection('get',$yesterday,'',$br['id_branch']);

    		    if(sizeof($ydaytoday) == 0){

    		        $ydaytoday['closing_balance_amt']=0;

    		        $ydaytoday['closing_balance_wgt'] = 0;

    		        $ydaytoday['closing_weight'] = 0;

    		    } 

        	    $today = $this->$model->getTodaySummaryBranchWise(date('Y-m-d'),$br['id_branch']); 

        	    //echo $this->db->last_query();exit;

        	    if(sizeof($today['canceled']) == 0){

					$today['canceled']['today_cancelled_amt'] = 0;

					$today['canceled']['today_cancelled_wgt'] = 0;

					$today['canceled']['weight_cancelled'] = 0;

				}

				if(sizeof($today['collection']) == 0){

					$today['collection']['today_collection_amt'] = 0;

					$today['collection']['today_collection_wgt'] = 0;

					$today['collection']['today_weight'] = 0;

				}

				if(sizeof($today['closed']) == 0){        	    

					$today['closed']['amtSchClosedAmt'] = 0;

					$today['closed']['wgtSchClosedAmt'] = 0;

					$today['closed']['wgtSchClosedWgt'] = 0;

				}

            	//echo $ydaytoday['closing_balance_amt'];

              	$closing_balance_amt =  $ydaytoday['closing_balance_amt'] + $today['collection']['today_collection_amt'] - $today['closed']['amtSchClosedAmt'] - $today['canceled']['today_cancelled_amt'];

        		$closing_balance_wgt =  $ydaytoday['closing_balance_wgt'] + $today['collection']['today_collection_wgt'] - $today['closed']['wgtSchClosedAmt'] - $today['canceled']['today_cancelled_wgt'];

        		$closing_weight =  $ydaytoday['closing_weight'] + $today['collection']['today_weight'] - $today['closed']['wgtSchClosedWgt'] - $today['canceled']['weight_cancelled'];

            

                $collection[] = array('date' 			=> date('Y-m-d'),

    						'today_collection_amt' 	=> $today['collection']['today_collection_amt'],

    						'today_collection_wgt' 	=> $today['collection']['today_collection_wgt'],

    						'today_weight' 			=> $today['collection']['today_weight'],

    						'amtSchClosedAmt'		=> $today['closed']['amtSchClosedAmt'],

    						'wgtSchClosedAmt'		=> $today['closed']['wgtSchClosedAmt'],

    						'wgtSchClosedWgt'		=> $today['closed']['wgtSchClosedWgt'],

    						'today_cancelled_amt'	=> $today['canceled']['today_cancelled_amt'], 

    						'today_cancelled_wgt'	=> $today['canceled']['today_cancelled_wgt'], 

    						'weight_cancelled'		=> $today['canceled']['weight_cancelled'], 

    						'closing_balance_amt'	=> number_format($closing_balance_amt, 2, '.', ''),

    						'closing_balance_wgt'	=> number_format($closing_balance_wgt, 2, '.', ''),

    						'closing_weight'		=> number_format($closing_weight, 2, '.', ''),

    						'date_add'				=> date('Y-m-d H:i:s'),

    						'id_branch'             => $br['id_branch'],

    						'branch_name'           => $br['branch_name'],

    						'opening_blc_amt'       => $ydaytoday['closing_balance_amt'] ,

    						'opening_blc_wgt'       => $ydaytoday['closing_balance_wgt'] ,

    						'opening_weight'        => $ydaytoday['closing_weight'] ,  

    						);  

            }

		}else{ 

		        $collection = $this->$model->daily_collection('get',$postDate,'',''); 

		        //echo $this->db->last_query();exit;

		        $type = 2;

		}

	//	echo $this->db->last_query();exit;

		$result['collection'] = $collection;

		$result['type'] = $type; 

		$result['date']= $postDate; 

        

        echo json_encode($result);

	}

	

	function get_cancelled_payment($id="",$from_date="",$to_date="",$type="")

	{

			

		$model=	self::DAS_MODEL;

		

		$data['print']['company_name'] = $this->session->userdata('company_name');

		

		$data['print']['branch_name'] = $this->session->userdata('branch_name');



		$data['print']['from_date'] = $from_date;



		$data['print']['to_date'] = $to_date;



		$data['accounts']=$this->$model->get_cancelled_payment_list($id,$from_date,$to_date,$type);	



		$data['main_content'] = self::REP_VIEW.'detailed/payment';



        $this->load->view('layout/template', $data);



	}

	

	 function customer_edit($mobile)

	{		





			$model=	self::DAS_MODEL;



			$account_model  =	self::ACC_MODEL;

			

			

			$cus= $this->$model->get_cust($mobile);

			



		



			$id=$cus['id_customer'];



			$data['accounts']= $this->$account_model->get_all_scheme_account_list($mobile);





		 // echo "<pre>";	print_r($data);exit;  echo "<pre>";



				  

				   $age=$this->birthday($cus['date_of_birth']);

				   

				   $data['customer']= array(

					   			'id_customer'		=>  (isset($cus['id_customer'])?$cus['id_customer']: NULL), 

					   			'firstname'			=>  (isset($cus['firstname'])?$cus['firstname']: NULL), 

				       			'lastname' 			=>  (isset($cus['lastname'])?$cus['lastname']: NULL), 

								'date_of_birth'		=>	(isset($cus['date_of_birth']) && $cus['date_of_birth'] != '' ? date('d/m/Y',strtotime(str_replace("/","-",$cus['date_of_birth']))): NULL), 	

								'date_of_wed'		=>	(isset($cus['date_of_wed']) && $cus['date_of_wed'] != '' ? date('d/m/Y',strtotime(str_replace("/","-",$cus['date_of_wed']))): NULL), 

								'gst_number'				=>	(isset($cus['gst_number'])?$cus['gst_number']: NULL), 

								'email'				=>	(isset($cus['email'])?$cus['email']: NULL), 

								'gender'			=>	(isset($cus['gender'])?$cus['gender']: NULL),

								'mobile'			=>	(isset($cus['mobile'])?$cus['mobile']: NULL), 

								'phone'				=>	(isset($cus['phone'])?$cus['phone']: NULL),

								'age'				=>	(isset($age)?$age: NULL),

								'nominee_name'			=>	(isset($cus['nominee_name'])?$cus['nominee_name']: NULL),

				       			

				       			'nominee_relationship'		=>	(isset($cus['nominee_relationship'])?$cus['nominee_relationship']: NULL),

				       			'nominee_mobile'	=>	(isset($cus['nominee_mobile'])?$cus['nominee_mobile']: NULL), 

								'pan'				=>	(isset($cus['pan'])?$cus['pan']: NULL), 	

								'pan_proof'			=>	(isset($cus['pan_proof']) && $cus['pan_proof'] != NULL ? $cus['pan_proof']: self::DEF_IMG_PATH), 

								'voterid'			=>	(isset($cus['voterid'])?$cus['voterid']: NULL), 

								'voterid_proof'		=>	(isset($cus['voterid_proof']) && $cus['voterid_proof'] != NULL ? $cus['voterid_proof']: self::DEF_IMG_PATH), 

								'rationcard'		=>	(isset($cus['rationcard'])?$cus['rationcard']: NULL), 

								'rationcard_proof'	=>	(isset($cus['rationcard_proof']) && $cus['rationcard_proof'] != NULL ?$cus['rationcard_proof']: self::DEF_IMG_PATH), 	

								'comments'			=>	(isset($cus['comments'])?$cus['comments']: NULL), 	

								'username'			=>	(isset($cus['username'])?$cus['username']: NULL), 

								'passwd'			=>	(isset($cus['passwd'])?$cus['passwd']: NULL), 

								'active'			=>	(isset($cus['active'])?$cus['active']: 0),

								'id_country'		=>	(isset($cus['id_country'])?$cus['id_country']:0),

							    'id_state' 			=>	(isset($cus['id_state'])?$cus['id_state']:0),

							'id_city'			=>	(isset($cus['id_city'])?$cus['id_city']:0),			

							'company_name'			=>	(isset($cus['company_name'])?$cus['company_name']:NULL),

							'address1'			=>	(isset($cus['address1'])?$cus['address1']:NULL),

							'address2'			=>	(isset($cus['address2'])?$cus['address2']:NULL),

							'address3'			=>	(isset($cus['address3'])?$cus['address3']:NULL),

							'pincode'			=>	(isset($cus['pincode'])?$cus['pincode']:NULL),

							'cus_img'           =>  (isset($cus['cus_img']) && $cus['cus_img'] != NULL ? $cus['cus_img']: self::DEF_CUS_IMG_PATH)

					);

				   

				   

			         

			   

				   if(is_dir(self::CUS_IMG_PATH.$id))

				   {

				   	  

						$cus_img=self::CUS_IMG_PATH.$id."/".self:: CUS_IMG;

				   	    $pan_img=self::CUS_IMG_PATH.$id."/".self:: PAN_IMG;

				   	    $voter_img=self::CUS_IMG_PATH.$id."/".self:: VOTERID_IMG;

				   	    $ration_img=self::CUS_IMG_PATH.$id."/".self:: RATION_IMG;

				   	    if(file_exists($cus_img))

				   	    {

							$data['customer']['cus_img_path']		= $cus_img;

						}

				   		  

				   		 if(file_exists($pan_img))

				   	    {

							$data['customer']['pan_path']		= $pan_img;

						}

				   		  if(file_exists($voter_img))

				   	    {

							$data['customer']['voterid_path']		= $voter_img;

						}

				   		  if(file_exists($ration_img))

				   	    {

							$data['customer']['rationcard_path']		= $ration_img;

						}

				   		 

				   }



				    $data['main_content'] = self::VIEW_FOLDER."customer_edit" ;

				   $this->load->view('layout/template', $data);

	}

	
	function birthday($birthday) {

	    $age = date_create($birthday)->diff(date_create('today'))->y;

	    return $age;

	}



// filter Date wise cus reg in dashboard//hh	



	function customer_status()

	{

	    	

        $model=	self::DAS_MODEL;

        

		$from_date=$this->input->post('from_date');



		$to_date=$this->input->post('to_date');

		$date_type=$this->input->post('date_type');
         

        

       

        $data['today_reg']     = $this->$model->customer_status($from_date,$to_date,$date_type);

        

         $data['yes_reg']     = $this->$model->customer_status($from_date,$to_date,$date_type);

         

         $data['wk_reg']     = $this->$model->customer_status($from_date,$to_date,$date_type);

        

         $data['m_reg']     = $this->$model->customer_status($from_date,$to_date,$date_type);



         $data['mob']     = $this->$model->customer_join_list('MOB',$from_date,$to_date,$date_type);



         $data['web']    = $this->$model->customer_join_list('WEB',$from_date,$to_date,$date_type);



         $data['admin']    = $this->$model->customer_join_list('ADMIN',$from_date,$to_date,$date_type);

         

          $data['collection']    = $this->$model->customer_join_list('COLLECTION',$from_date,$to_date,$date_type);



        //print_r($data);exit;



		echo json_encode($data);



	   

	}

function get_customer_joined($from_date,$to_date,$type="",$id_branch="")

	{

		

		$model=	self::DAS_MODEL;

		$date_type = $this->input->get('date_type');

		$data['print']['company_name'] = $this->session->userdata('company_name');

		

		$data['print']['branch_name'] = $this->session->userdata('branch_name');



		$data['print']['from_date'] = $from_date;



		$data['print']['to_date'] = $to_date;



		$data['customer']=$this->$model->get_customer_joined($from_date,$to_date,$type,$id_branch,$date_type);	

       

		$data['main_content'] = self::REP_VIEW.'detailed/customer';



        $this->load->view('layout/template', $data);



	}



/*function get_customer($from_date="",$to_date="",$type="")

	{

		

		$model=	self::DAS_MODEL;



		$data['customer']=$this->$model->get_customers_list($from_date,$to_date,$type);	







		$data['main_content'] = self::REP_VIEW.'detailed/customer';



        $this->load->view('layout/template', $data);



	}*/



// filter Date wise cus reg in dashboard//HH	

    

    //entry date updation  in emp branch login wise Except super admin, admin//  HH

    public function dayClose()

	{					  

		$insIds = array(); 

		$this->db->trans_begin();

		

		$id_branch = $this->session->userdata("id_branch");

		//print_r($id_branch);exit;

		

				$updData = array(

						

						"entry_date"	=>	date('Y-m-d',strtotime("+1 days")),

						"updated_on"	=>	date('Y-m-d H:i:s'), 

						); 

			$this->dashboard_model->updateData($updData,'id_branch',$id_branch,'ret_day_closing'); 

				if($this->db->trans_status() === TRUE){

					$this->db->trans_commit();

					$result = array('status'=>TRUE,'message'=>'Entry Date updated successfully');

				}

    		

		echo json_encode($result);

	}

    //entry date updation  in emp branch login wise// 

	    // customer wedding and birthday dates    

   	public function customer_wishes($type,$filterBy)

	{

		$model=	self::DAS_MODEL;

		$data['accounts'] = $this->$model->cus_wishes_list($type,$filterBy);

		//echo "<pre>";print_r($data);echo "</pre>";

		$data['main_content'] = self::REP_VIEW.'cus_wishes_list';//echo "<pre>";print_r($data);echo "</pre>";

        $this->load->view('layout/template', $data);

	}

		function cus_birthday()

	{

	    $model = self::DAS_MODEL;

	    $data['today'] = $this->$model->cus_birthday('T');

	    $data['this_week'] = $this->$model->cus_birthday('TW');

	    $data['tomorrow'] = $this->$model->cus_birthday('TMRW');

	    return $data;  

	}

	public function ajax_customer_wishes()

      {

        

        $model=    self::DAS_MODEL;

        

        $type = $_POST['type'];

        

        $filterBy = $_POST['byfilter'];

        

        $data['accounts'] = $this->$model->cus_wishes_list($type,$filterBy);

        

         echo json_encode($data);

        

        

      }

	

	function cus_wedding_day()

	{

	    $model = self::DAS_MODEL;

	    $data['today'] = $this->$model->cus_wedding_day('T');

	    $data['this_week'] = $this->$model->cus_wedding_day('TW');

	    $data['tomorrow'] = $this->$model->cus_wedding_day('TMRW');

	    return $data;  

	}

	

	function send_customer_wishes()

	{

	    $model=	self::DAS_MODEL;

	    $req_data   = $_POST['req_data'];

	    $type       = $_POST['type'];

	    

	    $responseData=array('status'=>false,'message'=>'Unable to Send');

        foreach($req_data as $cus)

        {

             if($type==1)

             {

                    $service = $this->admin_settings_model->get_service_by_code('BDAY_WISH');

                    

                    if($service['serv_whatsapp'] == 1)

            		{

            		    $sms_data=$this->admin_usersms_model->Get_service_code_sms('BDAY_WISH',$cus['id_customer'],'');

            			if($sms_data['mobile']!='')

            			{

            			    $whatsapp=$this->admin_usersms_model->send_whatsApp_message($sms_data['mobile'],$sms_data['message'],'','');

            			    if($whatsapp)

            			    {

            			        $responseData=array('status'=>TRUE,'message'=>'Message sent successfully');

            			    }

            			}

            		}

             }

             else if($type==2)

             {

                    $service = $this->admin_settings_model->get_service_by_code('WED_WISH');

                    

                    if($service['serv_whatsapp'] == 1)

            		{

            		    $sms_data=$this->admin_usersms_model->Get_service_code_sms('WED_WISH',$cus['id_customer'],'');

            			if($sms_data['mobile']!='')

            			{

            			    $whatsapp=$this->admin_usersms_model->send_whatsApp_message($sms_data['mobile'],$sms_data['message'],'','');

            			    if($whatsapp)

            			    {

            			        $responseData=array('status'=>TRUE,'message'=>'Message sent successfully');

            			    }

            			}

            		}

             }

        }

        echo json_encode($responseData);

	}

 // customer wedding and birthday dates 



 public function customer_detail_view()

 {

	$data['main_content'] = self::REP_VIEW.'detailed/customer';

	$data['customer_by_date']=1;

     $this->load->view('layout/template',$data);

 }



 //Created by Kanaga Durga R starts here 

//function to get customer count between daterange 

 public function customer_count()

 {

	$from_date=$this->input->post(from_date);

	$to_date=$this->input->post(to_date);

	$id_branch=$this->input->post(id_branch);

	$date_type=$this->input->post('date_type');

	$model = self::DAS_MODEL;



	$data['cust_count']  = $this->$model->reg_stat_count($from_date,$to_date,$id_branch,$date_type);

	$data['without_acc_count']=$this->$model->cust_wo_acc_bydate();

	$data['acc_wo_pay_count'] = $this->$model->acc_wo_pay_bydate();

	echo json_encode($data);

 }

 //function to show customer list between daterange 

 public function reg_detail_bydate($from_date,$to_date,$id_branch)

	{

		$model=	self::DAS_MODEL;

		
		$data['print']['company_name'] = $this->session->userdata('company_name');
		

		$data['print']['branch_name'] = $this->session->userdata('branch_name');


		$data['customer']=$this->$model->customer_detail_bydate($from_date,$to_date,$id_branch);	

		//print_r($data['customer']);exit;	

		$data['main_content'] = self::REP_VIEW.'detailed/customer';


        $this->load->view('layout/template', $data);

	}

	public function cust_wo_acc_details_bydate($from_date,$to_date,$id_branch)		

	{	

      	

		$model=	self::DAS_MODEL;

		

		$data['print']['company_name'] = $this->session->userdata('company_name');

		

		$data['print']['branch_name'] = $this->session->userdata('branch_name');

		

		$data['customer']= $this->$model->cust_wo_acc_details_bydate($from_date,$to_date,$id_branch);

		

		$data['main_content'] = self::REP_VIEW.'detailed/customer';



        $this->load->view('layout/template', $data);



	}

	public function acc_wo_pay_details_bydate($from_date,$to_date,$id_branch)



	{

		$model=	self::DAS_MODEL;

		

		$data['print']['company_name'] = $this->session->userdata('company_name');

		

		$data['print']['branch_name'] = $this->session->userdata('branch_name');

		

		$data['accounts']	= $this->$model->acc_wo_pay_details_bydate($from_date,$to_date,$id_branch);



      	$data['main_content'] = self::REP_VIEW.'detailed/account';



        $this->load->view('layout/template', $data);



	}

//Created by Kanaga Durga R ends here 



public function account_bydate()

{

	$model=	self::DAS_MODEL;

	$account=$this->$model->account_bydate();

	//print_r($account->total());exit;

	echo json_encode($account);

}
public function Upload_apk(){

	$data['main_content'] = self::APK_UPLAOD_PATH.'upload_apk';

	$this->load->view('layout/template', $data);
}
public function upload()
{
	$apk_folder = 'assets/adm_app_apk';
	if (!is_dir($apk_folder)) {
		mkdir($apk_folder, 0777, TRUE); 
	}

	$apkFilePath = 'assets/adm_app_apk/collectionapp.apk';

	if (file_exists($apkFilePath)) {

	    $del_file_sta=unlink('assets/adm_app_apk/collectionapp.apk');

	}
        $config['upload_path'] = FCPATH . 'assets/adm_app_apk'; // Specify the folder path
		$config['allowed_types'] = 'apk';
		$config['max_size'] = 100000; // 100MB
		$config['file_name'] = 'collectionapp.apk'; // Set a custom file name
		$this->load->library('upload', $config);
		$this->upload->initialize($config);

    if (!$this->upload->do_upload('apk_file')) {
       
          $error = $this->upload->display_errors();
$file_type = $_FILES['apk_file']['type'];	 
//  print_r($file_type.'sadasd'); exit;
$this->session->set_flashdata('chit_alert',array('message'=>$error,'class'=>'danger','title'=>'Upload Apk'));			
		
    } else {
//   exit('2');/
 $file_type = $_FILES['apk_file']['type'];	 
// print_r($file_type); exit;/
	    $data = $this->upload->data();
        $this->session->set_flashdata('chit_alert',array('message'=>'APK file uploaded successfully!','class'=>'success','title'=>'Upload Apk'));	
      }
			
		redirect('/admin_dashboard/Upload_apk');	

}

//employee wise collection starts
function get_collection_list()

	{
		$model=	self::DAS_MODEL;

		$data['main_content'] = self::REP_VIEW.'detailed/collection_app_list';

        $this->load->view('layout/template', $data);

	}
function ajax_get_collection_list()

	{
		$model=	self::DAS_MODEL;

		$data[accounts]=$this->$model->ajax_get_collection_list();

        echo json_encode($data);

	}
function get_collection_app_details()
	{
		$model=	self::DAS_MODEL;
		
		$data['collection_data']=$this->$model->get_collection_app_details();
		echo json_encode($data);

	}
//employee wise collection ends

// esakki
function getsource_wiserrecord_old(){

	$model=self:: DAS_MODEL;
	$branch = $this->$model->allBranches();
	$collection_data = array();

	$id_branch = $_POST['id_branch'];

	if(empty($id_branch)){
		foreach ($branch as $key =>$br) {

			$opening_bal_amt = $this->$model->getopening_bal_amt($br['id_branch']); 
			$oldclosed_amt = $this->$model->get_oldclosed_amt( $br['id_branch']); 
			$newclosed_amt = $this->$model->get_newclosed_amt( $br['id_branch']); 
			$collectionamt = $this->$model->get_collection_amt( $br['id_branch']); 
			$opening_balance  = $opening_bal_amt - $oldclosed_amt;
	
			$balanceAmount =  ($opening_balance+ $collectionamt) - $newclosed_amt;
			$collection_data[] = array(
				'id_branch'   =>$br['id_branch'],
				'branch_name' =>$br['branch_name'],
				'opening_bal' => $opening_balance,
				'closed_amt' => $newclosed_amt!=null?$newclosed_amt: 0,
				'collectionamt' => $collectionamt!=null ?$collectionamt: 0 ,
				'balanceamt'  =>$balanceAmount
			);
		}
	}else{

			$branch_name = $this->db->query('SELECT name FROM branch WHERE id_branch = '.$id_branch)->row_array();
			// print_r($branch_name['name']);exit;
			$opening_bal_amt = $this->$model->getopening_bal_amt($id_branch); 
			$oldclosed_amt = $this->$model->get_oldclosed_amt( $id_branch); 
			$newclosed_amt = $this->$model->get_newclosed_amt( $id_branch); 
			$collectionamt = $this->$model->get_collection_amt( $id_branch); 
			$opening_balance  = $opening_bal_amt - $oldclosed_amt;
	
			$balanceAmount =  ($opening_balance+ $collectionamt) - $newclosed_amt;
			$collection_data[] = array(
				'id_branch'   =>$id_branch,
				'branch_name' =>$branch_name['name'],
				'opening_bal' => $opening_balance,
				'closed_amt' => $newclosed_amt!=null?$newclosed_amt: 0,
				'collectionamt' => $collectionamt!=null ?$collectionamt: 0 ,
				'balanceamt'  =>$balanceAmount
			);
	}
	
  

	$result['collection'] = $collection_data;

	echo json_encode($result);

}

function getsource_wiserrecord(){

	    $model=self:: DAS_MODEL;
		$branch_settings = $this->session->userdata('branch_settings');
        $collection_data = array();
		$date_type = $this->input->post('date_type');

		if($branch_settings == 1){
			// Branch-wise: loop through all branches
			$branch = $this->$model->allBranches();
			foreach ($branch as $key =>$br) {

				$opening_bal_amt = $this->$model->getopening_bal_amt($br['id_branch']); 
				$oldclosed_amt = $this->$model->get_oldclosed_amt( $br['id_branch']); 
				$newclosed_amt = $this->$model->get_newclosed_amt( $br['id_branch']); 
				$collectionamt = $this->$model->get_collection_amt( $br['id_branch']); 
				$opening_balance  = $opening_bal_amt - $oldclosed_amt;

				$balanceAmount =  ($opening_balance+ $collectionamt) - $newclosed_amt;
				$collection_data[] = array(
					'id_branch'   =>$br['id_branch'],
					'branch_name' =>$br['branch_name'],
					'opening_bal' => $opening_balance,
					'closed_amt' => $newclosed_amt!=null?$newclosed_amt: 0,
					'collectionamt' => $collectionamt!=null ?$collectionamt: 0 ,
					'balanceamt'  =>$balanceAmount
				);
			}
		} else {
			// No branch setting: single aggregate row
			$opening_bal_amt = $this->$model->getopening_bal_amt(0); 
			$oldclosed_amt = $this->$model->get_oldclosed_amt(0); 
			$newclosed_amt = $this->$model->get_newclosed_amt(0); 
			$collectionamt = $this->$model->get_collection_amt(0); 
			$opening_balance  = $opening_bal_amt - $oldclosed_amt;

			$balanceAmount =  ($opening_balance+ $collectionamt) - $newclosed_amt;
			$collection_data[] = array(
				'id_branch'   => 0,
				'branch_name' => 'All',
				'opening_bal' => $opening_balance,
				'closed_amt' => $newclosed_amt!=null?$newclosed_amt: 0,
				'collectionamt' => $collectionamt!=null ?$collectionamt: 0 ,
				'balanceamt'  =>$balanceAmount
			);
		}


		$result['collection'] = $collection_data;
		$result['branch_settings'] = $branch_settings;

        echo json_encode($result);

	}


function schWise_accounts_list(){
	$model=	self::DAS_MODEL;
	$from_date=$this->input->post('from_date');
	$to_date=$this->input->post('to_date');
	$id_branch=$this->input->post('id_branch');
	$date_type=$this->input->post('date_type');
	$data = $this->$model->schWise_accounts($from_date,$to_date,$id_branch,$date_type);     
	echo json_encode($data);
}







}



?>