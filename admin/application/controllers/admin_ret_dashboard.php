<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Admin_ret_dashboard extends CI_Controller {

	const VIEW_FOLDER = 'ret_dashboard/';

	const RET_DAS_MODEL = 'ret_dashboard_model';

	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model(self::RET_DAS_MODEL);

		$this->load->model('ret_reports_model');

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



	function index(){



	}





    function get_EstimationStatus()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['dash_estmation'] = $this->$model->get_dashboard_estimation($from_date, $to_date,$id_branch);

		//print_r($this->db->last_query());exit;

		echo json_encode($data);

	}



	function get_BillingStatus()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['dash_billing'] = $this->$model->get_dashboard_billings($from_date, $to_date,$id_branch);

		$data['mrp'] = $this->$model->get_dashboard_billings_mrp($from_date, $to_date,$id_branch);

		$data['diamond'] = $this->$model->get_dashboard_billings_dia($from_date,$to_date,$id_branch);

		echo json_encode($data);

	}



	function get_stock_details()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['stock_details_dashboard'] = $this->$model->get_stock_details_dashboard($from_date, $to_date,$id_branch);

		echo json_encode($data);

	}



	 function get_GreentagSalesDetails()

	 {

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['dash_greentag'] = $this->$model->get_dashboard_greentag_det($from_date, $to_date,$id_branch);

		echo json_encode($data);

	 }



	function get_VitrualTagStatus()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['dash_virturaltag_details'] = $this->$model->get_dashboard_virturaltag_details($from_date, $to_date,$id_branch);

		echo json_encode($data);

	}



	function get_SalesReturnDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['dash_salesreturn_details'] = $this->$model->get_dashboard_salesreturn_det($from_date, $to_date,$id_branch);

		echo json_encode($data);

	}





	function get_old_metal_purchase()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_old_metal_purchase'] = $this->$model->get_dashboard_old_metal_purchase($from_date, $to_date,$id_branch);

		echo json_encode($data);

	}



	function get_CreditSalesDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_credeit_sales'] = $this->$model->get_dashboard_credit_sales($from_date, $to_date,$id_branch);

		echo json_encode($data);

	}





	function get_GiftVoucherDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_gift_vouchers'] = $this->$model->get_dashboard_gift_vouchers($from_date, $to_date,$id_branch);

		echo json_encode($data);

	}



	function get_BillClassficationDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_bills_clasfication'] = $this->$model->get_dashboard_bills_clasfications($from_date, $to_date,$id_branch);

		echo json_encode($data);

	}



	function get_BranchTransferDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_approval_pendings'] = $this->$model->get_dashboard_approval_pendings($from_date, $to_date,$id_branch);

		echo json_encode($data);

	}



    function get_lot_tag_details()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_lot_tag_details'] = $this->$model->get_dashboard_lot_tag_details($from_date, $to_date , $id_branch);

		echo json_encode($data);

	}



	function get_OrderDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_orders_details'] = $this->$model->get_dashboard_orders_details($from_date, $to_date,$id_branch);

		echo json_encode($data);

	}





	function get_StockDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_stock_details']=$this->$model->AvailableStockDetails($from_date,$to_date,$id_branch);

		echo json_encode($data);

	}



	function get_silver_StockDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_stock_details']=$this->$model->Available_SilverStockDetails($from_date,$to_date,$id_branch);

		echo json_encode($data);

	}





	function get_ReorderDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_reorder_items']=$this->$model->getReorderItems($id_branch);

		echo json_encode($data);

	}



	function get_KarigarOrderDetails()

	{

		$model=	self::RET_DAS_MODEL;

	    $data['karigar_orders']['today_delivered'] = $this->$model->karigar_orders('T');

		$data['karigar_orders']['today_pending'] = $this->$model->karigar_orders('TODDY_PENDING');

      	$data['karigar_orders']['tm_delivery'] = $this->$model->karigar_orders('TM');

      	$data['karigar_orders']['over_due_orders'] = $this->$model->karigar_orders('OVER_DUE');

      	$data['karigar_orders']['work_in_progress'] = $this->$model->karigar_orders('WIP');

		echo json_encode($data);

	}



	function get_customerOrderDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$id_branch	= $this->input->post('id_branch');

		$data['customer_order']=$this->$model->customer_orders($_POST['from_date'],$_POST['to_date'],$id_branch);

		echo json_encode($data);

	}



	function get_MetalStockDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$id_branch	= $this->input->post('id_branch');

	    $data['tag_details']=$this->$model->TagStockMetalWise($id_branch);

	    $data['Nontag_details']=$this->$model->NonTagStockMetalWise($id_branch);

		echo json_encode($data);

	}



	function get_CustomerDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_customer_details'] = $this->$model->get_dashboard_customer_details($from_date, $to_date);

		echo json_encode($data);

	}



	function get_RecentBillDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_bills_details'] = $this->$model->get_dashboard_bills_details($from_date, $to_date,$id_branch);

		echo json_encode($data);

	}


/*
	function get_cash_abstract_details()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_cash_abstarct_details'] = $this->$model->get_dashboard_cash_abstarct_details($from_date, $to_date,$id_branch);

		echo json_encode($data);

	} */





	function getEstimationDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $data['dash_estimation_details'] = $this->$model->get_dashboard_estimation_details($from_date, $to_date,$id_branch);

		echo json_encode($data);

	}



	function get_retail_dashboard_details()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$data['dash_estmation'] = $this->$model->get_dashboard_estimation($from_date, $to_date);

		$data['dash_billing'] = $this->$model->get_dashboard_billings($from_date, $to_date);

		$data['dash_old_metal_purchase'] = $this->$model->get_dashboard_old_metal_purchase($from_date, $to_date);

		$data['dash_credeit_sales'] = $this->$model->get_dashboard_credit_sales($from_date, $to_date);

		$data['dash_gift_vouchers'] = $this->$model->get_dashboard_gift_vouchers($from_date, $to_date);

		$data['dash_bills_clasfication'] = $this->$model->get_dashboard_bills_clasfications($from_date, $to_date);

		$data['dash_approval_pendings'] = $this->$model->get_dashboard_approval_pendings($from_date, $to_date);

		$data['dash_lot_tag_details'] = $this->$model->get_dashboard_lot_tag_details($from_date, $to_date);

		$data['dash_orders_details'] = $this->$model->get_dashboard_orders_details($from_date, $to_date);

		$data['dash_customer_details'] = $this->$model->get_dashboard_customer_details($from_date, $to_date);

		$data['dash_bills_details'] = $this->$model->get_dashboard_bills_details($from_date, $to_date);

		$data['dash_estimation_details'] = $this->$model->get_dashboard_estimation_details($from_date, $to_date);

		$data['dash_cash_abstarct_details'] = $this->$model->get_dashboard_cash_abstarct_details($from_date, $to_date);

		$data['dash_virturaltag_details'] = $this->$model->get_dashboard_virturaltag_details($from_date, $to_date);



		$data['stock_metal_details']=$this->$model->StockMetalWise();

		$data['karigar_orders']['today_delivered'] = $this->$model->karigar_orders('T');

		$data['karigar_orders']['today_pending'] = $this->$model->karigar_orders('TODDY_PENDING');

      	$data['karigar_orders']['tm_delivery'] = $this->$model->karigar_orders('TM');

      	$data['karigar_orders']['over_due_orders'] = $this->$model->karigar_orders('OVER_DUE');

      	$data['karigar_orders']['work_in_progress'] = $this->$model->karigar_orders('WIP');



     	$data['customer_orders']['today_delivered'] = $this->$model->customer_orders('T');

      	$data['customer_orders']['today_pending'] = $this->$model->customer_orders('T');

      	$data['customer_orders']['tm_ready_for_delivery'] = $this->$model->customer_orders('TM');

      	$data['customer_orders']['tm_pending'] = $this->$model->customer_orders('TMRW_PENDING');

      	$data['customer_orders']['over_due_orders'] = $this->$model->customer_orders('OVER_DUE');

      	$data['customer_orders']['work_in_progress'] = $this->$model->customer_orders('WIP');



      	$data['dash_stock_details']=$this->$model->AvailableStockDetails($from_date,$to_date);



      	$data['dash_reorder_items']=$this->$model->getReorderItems();



		$data['estimation'] =$this->$model->get_estimation($from_date,$to_date);

		$data['billing'] =$this->$model->get_billing($from_date,$to_date);

		echo json_encode($data);



	}


/*
	function get_SaleBill_details()

	{

		$model      = self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$branch     = $this->$model->allBranches();

		foreach($branch as $br)

		{

			//Sales Report On Category Wise

			$items  = $this->$model->get_saleBillRecords($br['id_branch'],$from_date,$to_date);

			$bill[] = array(

							'id_branch'                   => $br['id_branch'],

							'branch_name'                 => $br['branch_name'],

							'branchwise_sales_details'	  => $items

						);

			//Sales Report On Payment Wise

			$pay_items  = $this->$model->get_paymentBillRecords($br['id_branch'],$from_date,$to_date);

			$payment_bill[] = array(

							'id_branch'                   => $br['id_branch'],

							'branch_name'                 => $br['branch_name'],

							'paymentwise_sales_details'	  => $pay_items

						);

    		// Sales Report On Metal Wise

			$metal_items  = $this->$model->get_metalBillRecords($br['id_branch'],$from_date,$to_date);

			$metal_bill[] = array(

							'id_branch'                   => $br['id_branch'],

							'branch_name'                 => $br['branch_name'],

							'metalwise_sales_details'	  => $metal_items

						);

		}

		$data['categorywise_records']   = $bill;

		$data['paymentwise_records']    = $payment_bill;

		$data['metalwise_records']      = $metal_bill;

		echo json_encode($data);

	}
*/

	function get_SaleBill_details()

	{

		$model      = self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$branch     = $this->$model->allBranches();

		/* foreach($branch as $br)

		{

			//Sales Report On Category Wise

			$items  = $this->$model->get_saleBillRecords($br['id_branch'],$from_date,$to_date);

			$bill[] = array(

							'id_branch'                   => $br['id_branch'],

							'branch_name'                 => $br['branch_name'],

							'branchwise_sales_details'	  => $items

						);

			//Sales Report On Payment Wise

			$pay_items  = $this->$model->get_paymentBillRecords($br['id_branch'],$from_date,$to_date);

			$payment_bill[] = array(

							'id_branch'                   => $br['id_branch'],

							'branch_name'                 => $br['branch_name'],

							'paymentwise_sales_details'	  => $pay_items

						);

			// Sales Report On Metal Wise

			$metal_items  = $this->$model->get_metalBillRecords($br['id_branch'],$from_date,$to_date);

			$metal_bill[] = array(

							'id_branch'                   => $br['id_branch'],

							'branch_name'                 => $br['branch_name'],

							'metalwise_sales_details'	  => $metal_items

						);

		} */


		$id_branch	= ($this->input->post('id_branch')=='' ? 0 : $this->input->post('id_branch'));

		$data['payment_summary'] = $this->$model->get_paymentBillRecords($id_branch,$from_date,$to_date,1); //1-all branch


		if($id_branch==0){
			foreach($branch as $br)

			{

				//Sales Report On Category Wise

				$items  = $this->$model->get_saleBillRecords($br['id_branch'],$from_date,$to_date);

				$bill[] = array(

								'id_branch'                   => $br['id_branch'],

								'branch_name'                 => $br['branch_name'],

								'branchwise_sales_details'	  => $items

							);

				//Sales Report On Payment Wise

				$pay_items  = $this->$model->get_paymentBillRecords($br['id_branch'],$from_date,$to_date);

				$payment_bill[] = array(

								'id_branch'                   => $br['id_branch'],

								'branch_name'                 => $br['branch_name'],

								'paymentwise_sales_details'	  => $pay_items

							);

				// Sales Report On Metal Wise

				$metal_items  = $this->$model->get_metalBillRecords($br['id_branch'],$from_date,$to_date);

				$metal_bill[] = array(

								'id_branch'                   => $br['id_branch'],

								'branch_name'                 => $br['branch_name'],

								'metalwise_sales_details'	  => $metal_items

							);

			}

			$category_wise_sumary  = $this->$model->get_saleBillRecords('',$from_date,$to_date);


			$metal_wise_sumary  = $this->$model->get_metalBillRecords('',$from_date,$to_date);


		}else{
			//Sales Report On Category Wise

				$items  = $this->$model->get_saleBillRecords($id_branch,$from_date,$to_date);

				//print_r($items);exit;

				$bill[] = array(

								'id_branch'                   => $id_branch,

								'branch_name'                 => $items[0]['branch_name'],

								'branchwise_sales_details'	  => $items

							);

				//Sales Report On Payment Wise

				$pay_items  = $this->$model->get_paymentBillRecords($id_branch,$from_date,$to_date);

				$payment_bill[] = array(

								'id_branch'                   => $id_branch,

								'branch_name'                 => $pay_items[0]['branch_name'],

								'paymentwise_sales_details'	  => $pay_items

							);

				// Sales Report On Metal Wise

				$metal_items  = $this->$model->get_metalBillRecords($id_branch,$from_date,$to_date);

				$metal_bill[] = array(

								'id_branch'                   => $id_branch,

								'branch_name'                 => $metal_items[0]['branch_name'],

								'metalwise_sales_details'	  => $metal_items

							);

				$category_wise_sumary  = $this->$model->get_saleBillRecords($id_branch,$from_date,$to_date);


				$metal_wise_sumary  = $this->$model->get_metalBillRecords($id_branch,$from_date,$to_date);
		}

		$data['categorywise_records']   = $bill;

		$data['paymentwise_records']    = $payment_bill;

		$data['metalwise_records']      = $metal_bill;

		$data['category_wise_summary']   = $category_wise_sumary;

		$data['metal_wise_summary']   = $metal_wise_sumary;

		echo json_encode($data);

	}


	function get_order_data()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date = $this->input->post('from_date');

		$to_date   = $this->input->post('to_date');

		$data['tot_catalog'] =$this->$model->total_order('tot_catalog');

		$data['tot_custom'] =$this->$model->total_order('tot_custom');

		$data['tot_repair'] =$this->$model->total_order('tot_repair');

		$branch = $this->$model->allBranches();

		foreach($branch as $br)

		{

			$order_data = $this->$model->get_order_data($br['id_branch'],$from_date,$to_date);

			$order[] = array(

							'catalog' 	            => $order_data['catalog']['catalog'],

							'custom'		        => $order_data['cus']['custom'],

							'repair'		        => $order_data['repair']['repair'],

							'id_branch'             => $br['id_branch'],

							'branch_name'           => $br['branch_name']

							);

		}

		$data['order'] = $order;

		echo json_encode($data);



	}



	function get_estimation()

	{

	    $data['main_content'] = self::VIEW_FOLDER.'reports/live_estimation';

        $this->load->view('layout/template', $data);

    }



    function get_estimation_details()

    {

    	$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch   =$this->input->post('id_branch');

		$type   	=$this->input->post('type');

		$data=array();

		$estimation=$this->$model->get_estimation_details($from_date,$to_date,$id_branch);

		foreach($estimation as $est)

		{

				$data[]=array(

							'estimation_id'		=>$est['estimation_id'],

							'discount'	   		=>number_format(($type!=2 ? $est['discount']:0),2),

							'gift_voucher_amt'	=>$est['gift_voucher_amt'],

							'cus_id'			=>$est['cus_id'],

							'item_type'			=>$est['item_type'],

							'cus_name'			=>$est['cus_name'],

							'chit_amt'			=>number_format(($type==0 || $type==2 ? $est['chit_amt']:0),2),

							'item_cost'			=>number_format(($type==0 || $type==1 ? $est['item_cost'] :0),2),

							'pur_wt'			=>number_format(($type==0 || $type==1 ? $est['pur_wt'] :0),2),

							'sales_amt'			=>number_format(($type==0 || $type==2 ? $est['sales_amt'] :0),2),

							'sales_wt'			=>number_format(($type==0 || $type==2 ? $est['sales_wt'] :0),2),

							'total_cost'		=>number_format(($type==0 ? $est['total_cost'] :($type==1 ?$est['item_cost']-$est['discount']:$est['sales_amt']+$est['chit_amt'])),2),

							);

		}

		echo json_encode($data);

    }



    function ajax_lot_data()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date = $this->input->post('from_date');

		$to_date   = $this->input->post('to_date');

		$data['gross_wt']=$this->$model->get_lot_data('get_total_gross_wt',$from_date,$to_date);

		$data['net_wt']=$this->$model->get_lot_data('get_total_net_wt',$from_date,$to_date);



		$branch = $this->$model->allBranches();

		foreach($branch as $br)

		{

			$lot_data = $this->$model->lot_branchwise_data($br['id_branch'],$from_date,$to_date);

			$lot[] = array(

							'lots' 	            => $lot_data['count']['lots'],

							'net_weight' 	    => $lot_data['count']['net_weight'],

							'grs_wt' 	        => $lot_data['count']['grs_wt'],

							'id_branch'         => $br['id_branch'],

							'branch_name'       => $br['branch_name']

							);

		}

		$data['lot'] = $lot;

		echo json_encode($data);

	}

	function ajax_tag_data()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date = $this->input->post('from_date');

		$to_date   = $this->input->post('to_date');

		$data['grs_wt']=$this->$model->get_tag_data('get_total_gross_wt',$from_date,$to_date);

		$data['nt_wt']=$this->$model->get_tag_data('get_total_net_wt',$from_date,$to_date);

		$branch = $this->$model->allBranches();

		foreach($branch as $br)

		{

			$tag_data = $this->$model->tag_branchwise_data($br['id_branch'],$from_date,$to_date);

			$tag[] = array(

							'tags' 	            => $tag_data['total']['tags'],

							'nt' 	            => $tag_data['total']['nt'],

							'gt' 	            => $tag_data['total']['gt'],

							'id_branch'         => $br['id_branch'],

							'branch_name'       => $br['branch_name']

							);

		}

		$data['tag'] = $tag;

		echo json_encode($data);

	}



	function get_sales_details()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$branch = $this->$model->allBranches();

		foreach($branch as $br)

		{

			$items=$this->$model->get_saleBill($br['id_branch'],$from_date,$to_date);



			$bill[]=array(

							'id_branch'   => $br['id_branch'],

							'branch_name' => $br['branch_name'],

							 'billing'	  =>$items['billing']

						);

		}

		$data['sales_details']=$bill;

		echo json_encode($data);



	}



	function get_MetalBill_details()

	{

		$model=	self::RET_DAS_MODEL;

		$branch = $this->$model->allBranches();

		$data['sales_details']=$this->$model->metal_bill_details();

		echo json_encode($data);



	}





	//credit history tab

	function get_CreditDetils()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['bill_credit_details']=$this->$model->get_CreditDetils($from_date,$to_date,$id_branch);

		echo json_encode($data);



	}





	function get_PendingDueDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$data['pending_details']=$this->$model->get_due_pending_details();

		echo json_encode($data);



	}



	//credit history tab



	//Stock and Branch Transfer



	function get_metal_stock_details()

	{

	    $model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['stock_details']=$this->$model->get_stock_category_details($from_date,$to_date,$id_branch);

		echo json_encode($data);

	}



	function get_branch_transfer_details()

	{

	    $model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['branch_transfer_details']=$this->$model->get_branch_transfer_details();

		echo json_encode($data);

	}



	//Stock and Branch Transfer

	// Sales Transfer In-Transit Dashboard
	function get_sales_transfer_in_transit()
	{
		$this->load->model('ret_sales_transfer_model');
		$data['in_transit'] = $this->ret_sales_transfer_model->get_in_transit_list();
		$kpi = $this->ret_sales_transfer_model->get_in_transit_kpi();
		$data['kpi'] = $kpi;
		echo json_encode($data);
	}





	//Area Wise Sales and New Customer

	function get_new_customer()

	{

	    $model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['new_customers']=$this->$model->get_new_customer($from_date,$to_date,$id_branch);

		echo json_encode($data);

	}

	//Area Wise Sales and New Customer





	//Order Management

	function get_customer_order_details()

	{

	    $model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$order_type=2;

		$data['cus_orders']=$this->$model->get_order_details($from_date,$to_date,$order_type,$id_branch);

		$data['cus_orders_details']=$this->$model->get_customer_order_details($from_date,$to_date);

		echo json_encode($data);

	}



	function get_karigar_order_details()

	{

	    $model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$order_type=1;

		$data['karigar_orders']=$this->$model->get_order_details($from_date,$to_date,$order_type,$id_branch);

		echo json_encode($data);

	}



	//Order Management





		function get_saleschart_details()

	{

	    $model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$profit_amt=0;

		$purchase_cost=0;

		$total_sales_amt=0;

		$profit_margin=0;





		//Estimation Details

		$estimation=$this->$model->getEstimationDetails($from_date,$to_date,$id_branch);

		$green_tag=$this->$model->get_green_tag_sales($from_date,$to_date,$id_branch);

		$credit_details=$this->$model->get_creditDetilas($from_date,$to_date,$id_branch);

		$cus_details=$this->$model->get_customer_visit($from_date,$to_date,$id_branch);



		//echo "<pre>"; print_r($cus_details);exit;

		//Estimation Details



		$data['sales_summary']['total_sales_amount']=$this->$model->get_sales_summary($from_date,$to_date,$id_branch);



		$data['sales_summary']['total_est']=$estimation['created'];

		$data['sales_summary']['total_est_billed']=$estimation['sold'];

		$data['sales_summary']['total_est_unbilled']=$estimation['unsold'];



		$data['sales_summary']['total_sales_ret']=$estimation['tot_tag_ret'];

		$data['sales_summary']['total_sales_ret_per']=number_format((($estimation['tot_tag_ret']/$estimation['tot_tag_sales'])*100),2,'.','');



		$data['sales_summary']['total_sales_tag']=$estimation['tot_tag_sales'];



		$data['sales_summary']['total_billing_per']=number_format((($estimation['sold']/$estimation['created'])*100),2,'.','');



		$data['sales_summary']['total_green_tag']=$green_tag['green_tag_sales'];

		$data['sales_summary']['total_green_tag_amt']=$green_tag['tot_green_tag_amt'];



		$data['sales_summary']['total_credit_issued']=$credit_details['tot_due_amount'];

		$data['sales_summary']['total_credit_received']=$credit_details['creditreceived'];



		$data['sales_summary']['total_new_cus']=$cus_details['new_cus'];

		$data['sales_summary']['total_old_cus']=$cus_details['old_cus'];



		$data['sales_summary']['total_profit_amt']=number_format($profit_amt,2,'.','');

		$data['sales_summary']['total_profit_margin']=number_format($profit_margin,2,'.','');



		//Branch wise sales

		$data['sales_details']['sales_by_branch']=$this->$model->get_branchwise_sales($from_date,$to_date,$id_branch);

		$data['sales_details']['sales_by_pay_mode']=$this->$model->get_modewise_sales($from_date,$to_date,$id_branch);

		//$data['sales_details']['sales_by_product']=$this->$model->get_product_wise_sales($from_date,$to_date,$id_branch);





		echo json_encode($data);



	}





	function get_stockchart_details()

	{

	    $model=	self::RET_DAS_MODEL;

	    $from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');



	    $branch     = $this->$model->allBranches();

		foreach($branch as $br)

		{

		    $stock_details=$this->$model->get_branch_stock_details($from_date,$to_date,$br['id_branch']);

		    if($stock_details['available_pcs']>0)

		    {

		        $data['stock_by_branch'][]=$stock_details;

		    }



		}

		//$data['stock_by_product']=$this->$model->get_product_stock_details();

		$data['branch_transfer_details']=$this->$model->get_branch_transfer_details();

	     echo json_encode($data);

	}


	function get_cash_deposit_details_branchwise()
	{
		$model =	self::RET_DAS_MODEL;

		$branch     = $this->$model->get_all_branch_store();	

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$result_data = array();

		foreach ($branch as $brkey => $br) {

			$val = $this->$model->get_deposit_details_branchwise($from_date, $to_date, $br['id_branch']);
			$val['opening'] = $this->$model->get_previous_date_closing($br['id_branch'], $from_date);
			$val['id_branch'] = $br['id_branch'];
			$val['branch_name'] = $br['name'];
			$result_data[$brkey] = $val;
		}

		echo json_encode($result_data);
	}



	function get_approval()

	{

		$model=	self::RET_DAS_MODEL;

		$data['status']=$this->$model->approve_contract_price();

		$data['branch_status']=$this->$model->approve_branch_transfer();

		$data['download_status']=$this->$model->approve_branch_download();

		echo json_encode($data);

	}





	function get_contract_approval()

	{

		$model=	self::RET_DAS_MODEL;

		$data['approval_status']=$this->$model->approval_contract_price();

		//print_r($this->db->last_query());exit;

		echo json_encode($data);

	}

	function gross_profit_report($type="")
	{
		$model=	self::RET_DAS_MODEL;
		switch($type)
		{
			case 'list':
					$data['main_content'] = self::VIEW_FOLDER.'gp_report';
        			$this->load->view('layout/template', $data);
				break;
			case 'ajax':
					$list=$this->$model->get_gross_profit_details($_POST);
				  	$access = $this->admin_settings_model->get_access('admin_ret_reports/old_metal_purchase/list');
			        $data = array(
		        					'list'  => $list,
									'access'=> $access
		        				 );
					echo json_encode($data);
				break;
		}
	}




	function get_pendingorderDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['dash_pendingorder'] = $this->$model->get_pendingorderdetails($from_date, $to_date,$id_branch);

		//print_r($this->db->last_query());exit;

		echo json_encode($data);

	}


	function get_wiporderDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['dash_wiporder'] = $this->$model->get_wiporderDetails($from_date, $to_date,$id_branch);

		//print_r($this->db->last_query());exit;

		echo json_encode($data);

	}

	function get_dreadyorderDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['dash_drorder'] = $this->$model->get_dreadyorderDetails($from_date, $to_date,$id_branch);

		//print_r($this->db->last_query());exit;

		echo json_encode($data);

	}

	function get_deliveredorderDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['dash_deliveredorder'] = $this->$model->get_deliveredorderDetails($from_date, $to_date,$id_branch);

		//print_r($this->db->last_query());exit;

		echo json_encode($data);

	}


	function get_karigarreminderDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['dash_karreminderdetails'] = $this->$model->get_karigarreminderDetails($from_date, $to_date,$id_branch);

		//print_r($this->db->last_query());exit;

		echo json_encode($data);

	}


	function get_karigaroverdueDetails()

	{

		$model=	self::RET_DAS_MODEL;

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

		$data['dash_kar_overdue_details'] = $this->$model->get_karigaroverdueDetails($from_date, $to_date,$id_branch);

		//print_r($this->db->last_query());exit;

		echo json_encode($data);

	}




	function get_cash_abstract_details()

	{

		$model=	'ret_reports_model';

		$from_date	= $this->input->post('from_date');

		$to_date	= $this->input->post('to_date');

		$id_branch	= $this->input->post('id_branch');

	    $list = $this->$model->getBillDetails($_POST);

		$data['dash_cash_abstarct_details'] = array(

			'sales_amount' => 0,

			'cash' => 0,

			'advance_receipt' => 0,

			'chituti' => 0,

			'card' => 0,

			'chq_recd' => 0,

			'chq_issue' => 0,

			'chq' => 0,

			'credit_receipt' => 0,

			'credit_sale' => 0,

			'handlingcharge' => 0,

			'giftvoucher' => 0,

			'nb' => 0,

			'purchase_amount' => 0,

			'paymodes_total' => 0,

			'roundoff' => 0,

			'sales_return' => 0,

			'sales_return_total_tax_amount' => 0,

			'sales_total_tax_amount' => 0,

			'trans_total' => 0,

			'orderadj' => 0 ,

			'chitcuspaid' => 0,

			'chitben' => 0,

			'chit_payment_details' => []

		);
		$sales_taxable_amount = 0;
		$total_tax = 0;
		$salesArray= $list['item_details'];

		foreach($salesArray as $category){
          foreach($category as $row){
			$sales_taxable_amount  += $row['item_cost'] - $row['item_total_tax'];
			$total_tax  += $row['item_total_tax'];
		  }
		}


		$data['dash_cash_abstarct_details']['sales_amount'] = $sales_taxable_amount;

		$data['dash_cash_abstarct_details']['sales_total_tax_amount'] = $total_tax ;

		$return_sales_taxable_amount = 0;
		$return_total_tax = 0;
		$return_total_amount = 0;
		$returnArray= $list['return_details'];

		foreach($returnArray as $category){
			foreach($category as $row){
			  $return_sales_taxable_amount  += $row['item_cost'] - $row['item_total_tax'];
			  $return_total_tax  += $row['item_total_tax'];
			  $return_total_amount  += $row['item_cost'];
			}
		  }


		$data['dash_cash_abstarct_details']['sales_return'] = $return_sales_taxable_amount;

		$data['dash_cash_abstarct_details']['sales_return_total_tax_amount'] = $return_total_tax ;


		$purchase_amount = 0;
		$purchaseArray= $list['old_matel_details'];

		foreach($purchaseArray as $metal){
			foreach($metal as $row){
			  $purchase_amount  += $row['amount'];
			}
		  }



		$Adv_amount = 0;
		$AdvArray= $list['advance_detals'];

		foreach($AdvArray as $bill){
			$Adv_amount  += $bill['advance_amount'];
		}

		$getAdv_amount = 0;
		$GenAdvArray= $list['general_adv_details'];

		foreach($GenAdvArray as $bill){
			$getAdv_amount  += $bill['amount'];
		}



		$data['dash_cash_abstarct_details']['purchase_amount'] = $purchase_amount;

		$data['dash_cash_abstarct_details']['advance_receipt'] =$Adv_amount + $getAdv_amount ;

		$data['dash_cash_abstarct_details']['advance_receipt_ar'] =$GenAdvArray ;

		$data['dash_cash_abstarct_details']['advance_receipt_ary'] =$AdvArray ;

		$data['dash_cash_abstarct_details']['advance_deposit'] = $list['advance_deposit']['advance_deposit_amt'];

		$adv_refund_amount = 0;
		$adv_refundArray= $list['adv_refund'];

		foreach($adv_refundArray as $metal){

			  $adv_refund_amount  += $metal['payment_amount'];

		}

		$data['dash_cash_abstarct_details']['adv_refund'] =  $adv_refund_amount ;


		$credit_amount = 0;

		$creditArray= $list['credit_details'];

		foreach($creditArray as $bill){

			$credit_amount  += $bill['tot_amt_received'];

	    }

		$creditgenArray= $list['general_credit_collection'];

		foreach($creditgenArray as $bill){

			$credit_amount  += $bill['amount'];

	    }

		$data['dash_cash_abstarct_details']['credit_receipt'] =  $credit_amount;



		$due_amount = 0;

		$dueArray= $list['due_details'];

		foreach($dueArray as $bill){

			$due_amount  += $bill['due_amt'];

	    }

		$data['dash_cash_abstarct_details']['credit_sale'] =  $due_amount;

		$data['dash_cash_abstarct_details']['other_expense'] = $list['other_expense']['tot_amount'];

		$data['dash_cash_abstarct_details']['handling_charge'] = $list['bill_det']['handling_charges'];

		$total_sales_amt = $sales_taxable_amount +  $total_tax  + $credit_amount + $Adv_amount + $getAdv_amount  + $list['advance_deposit']['advance_deposit_amt'] + $list['bill_det']['handling_charges'] + $list['repair_order_delivered']['amount'] - $purchase_amount - $return_total_amount - $list['other_expense']['tot_amount'] -  $due_amount - $adv_refund_amount ;

		$data['dash_cash_abstarct_details']['trans_total'] = $total_sales_amt ;

		$paymentArray= $list['payment_details'];

		$cash_payment = 0;

		$card_payment = 0;

		$cheque_payment = 0;

		$cheque_credit = 0;

		$cheque_debit = 0;

		$online_payment = 0;

		foreach($paymentArray as $pay){

			if($pay['payment_mode'] == 'Cash' ){
				$cash_payment += $pay['payment_amount'];
			}

			if($pay['payment_mode'] == 'CC' || $pay['payment_mode'] == 'DC'  ){
				$card_payment += $pay['payment_amount'];
			}

		  	if($pay['payment_mode'] == 'CHQ' ){
				$cheque_payment += $pay['payment_amount'];
                if($pay['type'] == 1){
					$cheque_credit += $pay['payment_amount'];
				}elseif($pay['type'] == 2) {
					$cheque_debit -= $pay['payment_amount'];
				}
			}
			if($pay['payment_mode'] == 'NB' ){
				$online_payment += $pay['payment_amount'];
			}
	    }

		$GenpaymentArray= $list['general_pay'];

		$gen_cash_payment = 0;

		$gen_card_payment = 0;

		$gen_cheque_payment = 0;

		$gen_cheque_credit = 0;

		$gen_cheque_debit = 0;

		$gen_online_payment = 0;

		foreach($GenpaymentArray as $pay){

			if($pay['payment_mode'] == 'Cash' ){

				if($pay['transcation_type'] == 1){
					$gen_cash_payment += $pay['payment_amount'];
				}elseif($pay['transcation_type'] == 2) {
					$gen_cash_payment -= $pay['payment_amount'];
				}

			}

			if($pay['payment_mode'] == 'CC' || $pay['payment_mode'] == 'DC'  ){

				if($pay['transcation_type'] == 1){
					$gen_card_payment += $pay['payment_amount'];
				}elseif($pay['transcation_type'] == 2) {
					$gen_card_payment -= $pay['payment_amount'];
				}

			}

		  	if($pay['payment_mode'] == 'CHQ' ){
				//$gen_cheque_payment += $pay['payment_amount'];
                if($pay['transcation_type'] == 1){
					$gen_cheque_payment += $pay['payment_amount'];
					$gen_cheque_credit += $pay['payment_amount'];
				}elseif($pay['transcation_type'] == 2) {
					$gen_cheque_payment -= $pay['payment_amount'];
					$gen_cheque_debit += $pay['payment_amount'];
				}
			}
			if($pay['payment_mode'] == 'NB' ){

				if($pay['transcation_type'] == 1){
					$gen_online_payment += $pay['payment_amount'];
				}elseif($pay['transcation_type'] == 2) {
					$gen_online_payment -= $pay['payment_amount'];
				}
			}
	    }

		$data['dash_cash_abstarct_details']['cash'] =  $cash_payment + $gen_cash_payment;

		$data['dash_cash_abstarct_details']['card'] =  $card_payment + $gen_card_payment;


		$data['dash_cash_abstarct_details']['chq_recd'] = $cheque_credit + $gen_cheque_credit;

		$data['dash_cash_abstarct_details']['chq_issue'] =  $cheque_debit + $gen_cheque_debit;

		$data['dash_cash_abstarct_details']['nb'] =  $online_payment + $gen_online_payment;

		$data['dash_cash_abstarct_details']['advadj'] =  $list['advance_adjusted']['adj_amt'];

		$closing_amount=0;

		$cus_paid_amount=0 ;

		$benifit_amount=0;

		$ChitArray= $list['chit_details'];

		foreach($ChitArray as $bill){

			$closing_amount  += $bill['utilized_amt'];

			$cus_paid_amount += $bill['payment_amount'];

			$benifit_amount += $bill['utilized_amt'] - $bill['payment_amount'];


	    }

		$data['dash_cash_abstarct_details']['chituti'] = $closing_amount;

		$data['dash_cash_abstarct_details']['chitben'] = $benifit_amount;

		$data['dash_cash_abstarct_details']['chitcuspaid'] = $cus_paid_amount;

		$voucher_amt = 0;

		$VoucherArray= $list['voucher_details'];

		foreach($VoucherArray as $bill){

			$voucher_amt  += $bill['gift_voucher_amt'];
	    }

		$data['dash_cash_abstarct_details']['giftvoucher'] = $voucher_amt;

		$order_adj_amt = 0;

		$OrderAdjArray= $list['order_adj'];

		foreach($OrderAdjArray as $bill){

			$order_adj_amt  += $bill['advance_amount'];
	    }

		$data['dash_cash_abstarct_details']['orderadj'] = $order_adj_amt;



		$data['dash_cash_abstarct_details']['roundoff'] =  $list['bill_det']['round_off_amt'];

		$paymodes_total = 0;

		if($list['bill_det']['round_off_amt'] >=0 ){

	    	$paymodes_total = $cash_payment + $gen_cash_payment +  $card_payment + $gen_card_payment +  $online_payment + $gen_online_payment + $cheque_payment + $gen_cheque_payment + $list['advance_adjusted']['adj_amt'] +  $closing_amount + $order_adj_amt + $voucher_amt -  $list['bill_det']['round_off_amt'];

	    } else{

			$paymodes_total = $cash_payment + $gen_cash_payment +  $card_payment + $gen_card_payment +  $online_payment + $gen_online_payment + $cheque_payment + $gen_cheque_payment + $list['advance_adjusted']['adj_amt'] +  $closing_amount + $order_adj_amt + $voucher_amt - ($list['bill_det']['round_off_amt'] * -1);

		}

		$data['dash_cash_abstarct_details']['paymodes_total'] = $paymodes_total;


		$data['dash_cash_abstarct_details']['chit_payment_details'] = $list['chit_credit_collection'];

		//$data['list']= $list ;



		//PAYMENTS









		echo json_encode($data);

	}




	function get_LedgerBalanceAlert()
	{
		$model = self::RET_DAS_MODEL;
		$id_branch = ($this->input->post('id_branch') == '' ? 0 : $this->input->post('id_branch'));
		
		// 1. Get ledgers that need checking (those with min_balance > 0)
		$ledgers = $this->$model->getLedgerBalanceAlertData();
		
		$alert_data = [];
		$today = date('d-m-Y');
		
		foreach ($ledgers as $ledger) {
			$id_ledger = $ledger['id_ledger'];
			$min_balance = (float)$ledger['min_balance'];
			
			// 2. Prepare post data for getLedgerReportData
			$post_data = [
				'id_ledger' => $id_ledger,
				'from_date' => date('d-m-Y', strtotime($ledger['opening_date'])), 
				'to_date'   => $today,
				'id_branch' => $id_branch
			];
			
			// 3. Get ledger report data (current balance calculation)
			$report_data = $this->ret_reports_model->getLedgerReportData($post_data);
			
			$current_balance = 0;
			if (!empty($report_data)) {
				// The getLedgerReportData returns grouped data by date.
				// The running balance is calculated within the model.
				// We need to find the final running balance from the last entry.
				$last_date = array_key_last($report_data);
				$last_entries = $report_data[$last_date];
				
				// Re-calculating final balance based on logic in getLedgerReportData
				// because it's not explicitly returned in a single "current_balance" field.
				// Based on model code: $entries['opening'] is starting for that day.
				$running = (float)$last_entries['opening'];
				foreach ($last_entries as $k => $row) {
					if (is_int($k)) {
						$running += (float)($row['Credit'] ?? 0);
						$running -= (float)($row['Debit'] ?? 0);
					}
				}
				$current_balance = $running;
			} else {
				// If no transactions, use opening balance
				$current_balance = (float)$ledger['opening_balance'];
			}
			
			$alert_data[] = [
				'ledger_id'       => $id_ledger,
				'ledger_name'     => $ledger['ledger_name'],
				'min_balance'     => $min_balance,
				'current_balance' => $current_balance
			];
		}
		
		echo json_encode(['ledger_alerts' => $alert_data]);
	}

}

?>