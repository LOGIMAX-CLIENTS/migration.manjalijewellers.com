<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class Admin_ret_reports extends CI_Controller
{

	const VIEW_FOLDER = 'ret_reports/';

	const RET_REPORT_MODEL = 'ret_reports_model';

	const DATA_FILE_PATH = "assets/upload/old_tag_files/";

	const SETT_MOD		= "admin_settings_model";

	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model(self::RET_REPORT_MODEL);

		$this->load->model("admin_settings_model");

		$this->load->model("ret_catalog_model");

		$this->load->model(self::SETT_MOD);

		$this->load->model("log_model");

		$this->load->model("ret_purchase_order_model");

		if (!$this->session->userdata('is_logged')) {

			redirect('admin/login');
		} elseif ($this->session->userdata('access_time_from') != NULL && $this->session->userdata('access_time_from') != "") {

			$now = time();

			$from = $this->session->userdata('access_time_from');

			$to = $this->session->userdata('access_time_to');

			$allowedAccess = ($now > $from && $now < $to) ? TRUE : FALSE;

			if ($allowedAccess == FALSE) {

				$this->session->set_flashdata('login_errMsg', 'Exceeded allowed access time!!');

				redirect('chit_admin/logout');
			}
		}
	}



	function index()
	{
	}



	function old_metal_purchase($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'old_metal_purchase';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getOldMetalPurchases($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/old_metal_purchase/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function stock_age($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'stock_age_analysis';

				$this->load->view('layout/template', $data);

				break;

			case 'tag_list':

				$data['main_content'] = self::VIEW_FOLDER . 'stock_age_tag_list';

				$this->load->view('layout/template', $data);

				break;

			case 'tagging':

				$data = $this->$model->get_stock_age_tag($_POST);

				echo json_encode($data);

				break;

			case 'dynamic':

				$data['main_content'] = self::VIEW_FOLDER . 'stock_age_dynamic';

				$this->load->view('layout/template', $data);

				break;

			case 'get_dynamic_age_list':

				$list = $this->$model->getDynamicStockAgeDetails($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/stock_age/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;

			case 'ajax':

				$list = $this->$model->getStockAgeDetails($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/stock_age/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function update_green_tag()

	{

		$model =	self::RET_REPORT_MODEL;

		$reqdata   = $this->input->post('req_data');

		$this->db->trans_begin();

		foreach ($reqdata as $tag) {

			$data = array(
				'tag_mark' => $tag['req_status'],
				'green_tag_date' => ($tag['req_status'] == 1 ? date("Y-m-d H:i:s") : NULL),
				'green_tag_marked_by' => ($tag['req_status'] == 1 ? $this->session->userdata('uid') : NULL)
			);
			if ($tag['req_status'] == 0) {
				$data['unmark_by'] = $this->session->userdata('uid');
				$data['unmark_date'] = date("Y-m-d H:i:s");
			}

			$status = $this->$model->updateData($data, 'tag_id', $tag['tag_id'], 'ret_taging');
			//print_r($this->db->last_query());exit;


		}

		if ($this->db->trans_status() === TRUE) {

			$log_data = array(

				'id_log'        => $this->session->userdata('id_log'),

				'event_date'	=> date("Y-m-d H:i:s"),

				'module'      	=> 'Tag',

				'operation'   	=> 'Green Tag',

				'record'        => sizeof($reqdata),

				'remark'       	=> 'Green Tag ' . ($tag['req_status'] == 1 ? 'Marked' : 'UnMarked') . ' Successfully'

			);

			$this->log_model->log_detail('insert', '', $log_data);

			$this->db->trans_commit();

			$this->session->set_flashdata('chit_alert', array('message' => 'Tag Updated successfully.', 'class' => 'success', 'title' => 'Tagging'));
		} else {

			$this->db->trans_rollback();

			$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Tagging'));
		}
	}



	function lot_wise($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'lot_wise';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getLotwiseSoldPending($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/lot_wise/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function lottagvault($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'lottagvault';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getLotwiseTaggedVault($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/lottagvault/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}





	function partly_sold($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'partly_sold';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getPartlySold($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/partly_sold/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);



				echo json_encode($data);

				break;
		}
	}



	function cash_abstract($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'cash_abstract';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':



				$list = $this->$model->getBillDetails($_POST);

				//echo "<pre>"; print_r($list);exit;

				$access = $this->admin_settings_model->get_access('admin_ret_reports/cash_abstract/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);



				echo json_encode($data);

				break;
		}
	}



	function generate_cash_abstract($id_branch, $from_date, $to_date)

	{



		$model =	self::RET_REPORT_MODEL;



		$data['billing'] = $this->$model->getBillDetails($from_date, $to_date, $id_branch);

		$data['category'] = $this->$model->getallCategory();

		$data['comp_details'] = $this->$model->getCompanyDetails($id_branch);

		//echo "<pre>"; print_r($data);exit;

		$this->load->helper(array('dompdf', 'file'));



		$dompdf = new DOMPDF();



		$html = $this->load->view('ret_reports/print/cash_abstract', $data, true);



		$dompdf->load_html($html);



		$dompdf->set_paper("a4", "portriat");



		$dompdf->render();



		while(ob_get_level()) ob_end_clean();
		$dompdf->stream("Receipt.pdf", array('Attachment' => 0));
	}



	public function export_csv($id_branch, $from_date, $to_date)

	{

		// file name

		$model =	self::RET_REPORT_MODEL;

		$data['billing'] = $this->$model->getBillDetails($from_date, $to_date, $id_branch);

		$data['category'] = $this->$model->getallCategory();

		$data['comp_details'] = $this->$model->getCompanyDetails($id_branch);

		$html = $this->load->view('ret_reports/print/export', $data, true);



		// Put the html into a temporary file

		$tmpfile = time() . '.html';

		$this->load->library('Excel');



		file_put_contents($tmpfile, $html);



		// Read the contents of the file into PHPExcel Reader class

		$reader = new PHPExcel_Reader_HTML;

		$content = $reader->load($tmpfile);



		// Pass to writer and output as needed

		$objWriter = PHPExcel_IOFactory::createWriter($content, 'Excel2007');



		// We'll be outputting an excel file

		header('Content-type: application/vnd.ms-excel');



		header('Content-Disposition: attachment; filename="Cash Abstract.xls"');



		$objWriter->save('php://output');



		// Delete temporary file

		unlink($tmpfile);
	}





	function branch_trans($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'branch_transfer';

				$this->load->view('layout/template', $data);

				break;

			case 'approval_pending':

				$data['main_content'] = self::VIEW_FOLDER . 'approval_pending';

				$this->load->view('layout/template', $data);

				break;

			case 'intransit':

				$data = $this->$model->getIntransitDetails();

				echo json_encode($data);

				break;

			case 'ajax':

				$list = $this->$model->getBranchTransReport($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/branch_trans/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function credit_issued($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'credit_issued';

				$this->load->view('layout/template', $data);

				break;


			case 'ajax':

				$list = $this->$model->getcreditBill($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/credit_issued/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function credit_history($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'credit_history';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getcreditBill_history($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/credit_history/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function advance_history($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'advance_history';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_advance_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/advance_history/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function chit_closing_details($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'chit_closing';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->chit_utilize_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/chit_closing_details/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function chit_redemption($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'chit_redemption';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getChitRedemptionDetails($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/chit_redemption/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function card_payment($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'card_payment';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->card_payment_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/chit_closing_details/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function copy_bill_details($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'copy_bill';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getDulpicate_bill_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/copy_bill_details/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function pan_bill_details($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'pan_bill';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->pan_bill_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/pan_bill_details/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function cancelled_bills($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'cancelled_bills';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getCancelledBills($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/cancelled_bills/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function gst_bills($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'gst_bills';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getGSTBills($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/gst_bills/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	function bill_discount($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'discount_bills';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->discount_bill($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/bill_discount/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	function reorder_items($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'reorder_items';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getReorderitems($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/reorder_items/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	function branchreorder_items($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'branchreorder_items';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getBranchReorderitems($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/branchreorder_items/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	function retagging_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'retagging';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_retagging_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/retagging/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function get_Activedesign()

	{

		$model = self::RET_REPORT_MODEL;

		// $id_product=$this->input->post('id_product');

		$data = $this->$model->get_Activedesign($_POST);

		echo json_encode($data);
	}



	function get_ActiveSubDesign()

	{

		$model = self::RET_REPORT_MODEL;

		$data = $this->$model->get_ActiveSubDesign($_POST);

		echo json_encode($data);
	}


	function get_Active_St_SubDesign()

	{

		$model = self::RET_REPORT_MODEL;

		$data = $this->$model->get_Active_St_SubDesign($_POST);

		echo json_encode($data);
	}


	function get_ActiveNontagProduct()

	{

		$model = self::RET_REPORT_MODEL;

		$data = $this->$model->get_ActiveNontagProduct($_POST);

		echo json_encode($data);
	}



	function get_ActiveProduct()

	{

		$model = self::RET_REPORT_MODEL;

		$data = $this->$model->get_ActiveProduct($_POST);

		echo json_encode($data);
	}



	function get_weight_range()

	{

		$model = self::RET_REPORT_MODEL;

		$data = $this->$model->get_weight_range();

		echo json_encode($data);
	}


	function tag_items_designwise($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'tag_items_designwise';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getTaggeditems($_POST);

				$branchwise = $this->$model->getTaggeditems_branchwise($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/reorder_items/list');

				$data = array(

					'list'          => $list,

					'stock_details'    => $branchwise,

					'access'        => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function tagged_item_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':
				$data['profile'] = $this->$model->get_profile_settings($this->session->userdata('profile'));

				$data['main_content'] = self::VIEW_FOLDER . 'tagged_item';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_Tagged_items($_POST);

				$branchwise = $this->$model->getTaggeditems_branchwise($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/reorder_items/list');

				$data = array(

					'list'          => $list,

					'stock_details'    => $branchwise,

					'access'        => $access

				);

				echo json_encode($data);

				break;
		}
	}

	/*
    function stock_report($type="")

	{

		$model=	self::RET_REPORT_MODEL;

		switch($type)

		{

			case 'list':

					$data['main_content'] = self::VIEW_FOLDER.'stock_report';

        			$this->load->view('layout/template', $data);

				break;

			case 'ajax':

					$list=$this->$model->stock_details($_POST);

					$non_tag_items=$this->$model->stock_balance_nontag($_POST);

				  	$access = $this->admin_settings_model->get_access('admin_ret_reports/stock_report/list');

			        $data = array(

		        					'list'  => $list,

		        					'non_tag_items'=>$non_tag_items,

									'access'=> $access

		        				 );

					echo json_encode($data);

				break;

		}

    }

*/

	function stock_details_v1($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$profile = $this->$model->get_profile_settings($this->session->userdata('profile'));

				$data['main_content'] = self::VIEW_FOLDER . 'stock_details_v1';

				$data['profile'] = $profile;

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':


				//    /* if($_POST['group_by']==1 || $_POST['group_by']==2)
				//     {*/
				//         $list=$this->$model->get_stock_details($_POST);
				//     /*}else{
				//         $list=$this->$model->get_stock_weight_range($_POST);
				//     }*/


				// 	$non_tag_items=$this->$model->get_nontag_stock_details($_POST);

				if ($_POST['list_by'] == 0) {

					$list = $this->$model->get_stock_details_v1($_POST);

					$non_tag_items = $this->$model->get_nontag_stock_details_v1($_POST);

					// print_r($this->db->last_query());exit;

				} elseif ($_POST['list_by'] == 1) {

					$list = $this->$model->get_stock_details_v1($_POST);
					$non_tag_items = [];
				} elseif ($_POST['list_by'] == 2) {
					$list = [];
					$non_tag_items = $this->$model->get_nontag_stock_details_v1($_POST);
				}

				$profile = $this->$model->get_profile_settings($this->session->userdata('profile'));

				$access = $this->admin_settings_model->get_access('admin_ret_reports/stock_report/list');


				$other_ow = $this->$model->other_ow($_POST);

				$showroom_sales = $this->$model->showroom_sales($_POST);

				$branch_outward = $this->$model->branch_outward($_POST);

				$issued_stock = $this->$model->issued_stock($_POST);



				$other_pcs	= 0;

				$other_gwt	= 0;

				$openpcs 	= 0;

				$openwt 	= 0;

				$inwardpcs 	= 0;

				$inwardwt 	= 0;

				$outwardpcs = 0;

				$outwardwt 	= 0;

				$soldpcs	= 0;

				$soldwt		= 0;

				$closepcs 	= 0;

				$closewt 	= 0;

				$close_purewt 	= 0;

				$showroomsalepcs = 0;

				$showroomsalegwt = 0;

				$issuedpcs = 0;

				$issuedwt = 0;



				$lotinw_pcs 	= 0;

				$lotinw_gwt 	= 0;



				$brainw_gwt 	= 0;

				$brainw_pcs 	= 0;





				foreach ($list as $key => $value) {



					foreach ($value as $id => $val) {



						foreach ($val as $skey => $sval) {



							$openpcs += $sval['op_blc_pcs'];

							$openwt  += $sval['op_blc_gwt'];



							/*$inwardpcs += $sval['inw_pcs'];

								$inwardwt  += $sval['inw_gwt'];*/



							$lotinw_pcs += $sval['lotinw_pcs'];

							$lotinw_gwt  += $sval['lotinw_gwt'];



							$brainw_pcs += $sval['brainw_pcs'];

							$brainw_gwt  += $sval['brainw_gwt'];



							$soldpcs	+= $sval['sold_pcs'];

							$soldwt		+= $sval['sold_gwt'];



							$closepcs += ($sval['op_blc_pcs'] + $sval['inw_pcs'] - $sval['sold_pcs'] - $sval['br_out_pcs']);

							$closewt  += ($sval['op_blc_gwt'] + $sval['inw_gwt'] - $sval['sold_gwt'] - $sval['br_out_gwt']);

							$close_purewt += ($sval['pure_wt'] );
						}
					}
				}



				$other_pcs = $other_ow['partly_sold']['tot_pcs'];

				$other_gwt = $other_ow['partly_sold']['tot_gwt'];



				foreach ($branch_outward as $key => $val) {

					$outwardpcs += $val['tot_pcs'];

					$outwardwt += $val['tot_gwt'];
				}



				foreach ($issued_stock as $key => $val) {

					$issuedpcs += $val['tot_pcs'];

					$issuedwt += $val['tot_gwt'];
				}





				$stocksummery = array(
					"openpcs" => $openpcs,

					"openwt" =>  number_format($openwt, 2, '.', ''),

					"otherpcs" => $other_pcs,

					"othergwt" => number_format($other_gwt, 2, '.', ''),

					"showroomsalepcs" => $showroom_sales['piece'],

					"showroomsalegwt" => number_format($showroom_sales['gwt'], 2, '.', ''),

					"inwardpcs" => $inwardpcs,

					"inwardwt" => number_format($inwardwt, 2, '.', ''),

					"brainw_pcs" => $brainw_pcs,

					"brainw_gwt" => number_format($brainw_gwt, 2, '.', ''),

					"lotinw_pcs" => $lotinw_pcs,

					"lotinw_gwt" => number_format($lotinw_gwt, 2, '.', ''),

					"outwardpcs" => $outwardpcs, "outwardwt" => number_format($outwardwt, 2, '.', ''),

					"issued_pcs" => $issuedpcs,

					"issued_wt" => number_format($issuedwt, 2, '.', ''),

					"soldpcs" => $soldpcs, "soldwt" => number_format($soldwt, 2, '.', ''),

					"closepcs" => $closepcs,

					"closewt" => number_format($closewt, 2, '.', ''),

					"close_purewt" => number_format($close_purewt, 2, '.', '')
				);







				$data = array(

					'list'  => $list,

					'profile' => $profile,

					'non_tag_items' => $non_tag_items,

					'stocksummery' => $stocksummery,

					'access' => $access

				);



				echo json_encode($data);

				break;
		}
	}

	function approvalstock_details($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'approvalstock_details';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_approvalstock_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/approvalstock_details/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function stock_checking($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'stock_checking';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->stock_checking($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/stock_checking/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function item_sales($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'item-wise_sales';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->itemwise_sales_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/item_sales/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function other_issue($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'other_issue';

				$profile_settings = $this->$model->get_profile_settings($this->session->userdata('profile'));

				$data['date_visible'] = $profile_settings['pre_date_oi'];

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getOtherIssueList($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/other_issue/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	function scan_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'scan_report';

				$this->load->view('layout/template', $data);

				break;

			case 'scanned_details':

				$data['main_content'] = self::VIEW_FOLDER . 'scanned_details';

				$this->load->view('layout/template', $data);

				break;

			case 'scanned_report':

				$data = $this->$model->get_TagScannedDetails($_POST);

				echo json_encode($data);

				break;

			case 'save':


				$data = array('remark' => $_POST['remark'], 'updated_on' => date("Y-m-d H:i:s"), 'updated_by' => $this->session->userdata('uid'));

				$this->db->trans_begin();

				$status = $this->$model->updateData($data, 'id_tag_scanned', $_POST['id_tag_scanned'], 'ret_tag_scanned');

				$remark = $this->$model->get_tag_description($_POST['id_tag_scanned']);

				// print_r($this->db->last_query());exit;

				//  $insOrder = $this->$model->insertData($insData,'ret_purchase_order_description');

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					$this->session->set_flashdata('chit_alert', array('message' => 'Description Added Successfully', 'class' => 'success', 'title' => 'Description'));

					$return_data = array('status' => TRUE, 'remark' => $remark, 'message' => 'Description Added Successfully..');
				} else {



					echo $this->db->last_query();
					exit;

					$this->db->trans_rollback();

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Description'));

					$return_data = array('status' => FALSE, 'message' => 'Unable to proceed the requested process');
				}

				echo json_encode($return_data);

				break;

			case 'ajax':

				$list = $this->$model->tag_scan_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/scan_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}


	function close_tag_scan()

	{

		$model =	self::RET_REPORT_MODEL;

		$this->db->trans_begin();

		$id_product = $this->input->post('id_product');

		$id_branch = $this->input->post('id_branch');

		$id_section = $this->input->post('id_section');

		// Validate mandatory fields
		if(empty($id_branch) || $id_branch == 0) {
			echo json_encode(array('status' => false, 'msg' => 'Please Select Branch'));
			return;
		}
		if(empty($id_product)) {
			echo json_encode(array('status' => false, 'msg' => 'Please Select Product'));
			return;
		}
		if(empty($id_section) || $id_section == 0) {
			echo json_encode(array('status' => false, 'msg' => 'Please Select Section'));
			return;
		}

		// Get the id_scanned values for the matching scan sessions before closing
		$this->db->select('id_scanned');
		$this->db->from('ret_tag_scan');
		if($id_product != 0) {
			$this->db->where('id_product', $id_product);
		}
		$this->db->where('id_branch', $id_branch);
		$this->db->where('id_section', $id_section);
		$this->db->where('status', 1);
		$scan_sessions = $this->db->get()->result_array();

		// Delete ret_tag_scanned records for the matching scan sessions
		if(!empty($scan_sessions)) {
			$scanned_ids = array_column($scan_sessions, 'id_scanned');
			$this->db->where_in('id_scanned', $scanned_ids);
			$this->db->delete('ret_tag_scanned');
		}

		$scan_status = array('status' => 0, 'id_product' => $id_product, 'id_branch' => $id_branch, 'id_section' => $id_section);

		$this->$model->update_tag_scan('ret_tag_scan', $scan_status);

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$data = array('status' => true, 'msg' => 'Scan Closed Successfully');
		} else {

			$this->db->trans_rollback();

			$data = array('status' => false, 'msg' => 'Unable To Proceed Your Request');
		}

		echo json_encode($data);
	}




	function tag_scan_missing($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'tag_scan_missing';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_tag_scan_missing($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/scan_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	/*function get_tag_scan_details()

	{

		$model=	self::RET_REPORT_MODEL;

		$tag_id=$this->input->post('tag_id');

		$id_product=$this->input->post('id_product');

		$print_taken=$this->input->post('print_taken');

		$tag_details=$this->$model->get_entry_records($tag_id);

		if($tag_details['tag_status']==0)

		{

		    if($id_product==$tag_details['product_id'])

		    {

        		$status=$this->$model->get_scanned_details($tag_details['tag_id']);

        		$id_branch=$this->session->userdata('id_branch');

        		if($status)

        		{

        			$this->db->trans_begin();

        			if($this->session->userdata('branch_settings')==1)

        			{

        				if($tag_details['current_branch']==$id_branch)

        				{

        					$insData=array(

        					'tag_id'	=>$tag_details['tag_id'],

        					'id_branch' => ($id_branch!='' ? $id_branch:NULL),

        					'date_add'	=> date("Y-m-d H:i:s"),

        					'created_by'=> $this->session->userdata('uid'),

        					);

        					$insId=$this->$model->insertData($insData,'ret_tag_scanned');

        				//	print_r($this->db->last_query());exit;

        					if($insId)

        					{

        						$data=array('status'=>TRUE,'tag_details'=>$tag_details,'msg'=>'Tag Scanned Successfully.');

        					}else{

        						$data=array('status'=>FALSE,'msg'=>'Unable To Proceed Your Request');

        					}

        				}

        				else{

        					$data=array('status'=>FALSE,'msg'=>'Invalid Branch Please Contact Your Admin..');



        				}

        			}

        			else

        			{

        				$insData=array(

        				'tag_id'	=>$tag_details['tag_id'],

        				'id_branch' => ($id_branch!='' ? $id_branch:NULL),

        				'date_add'	=> date("Y-m-d H:i:s"),

        				'created_by'=> $this->session->userdata('uid'),

        				);

        				$insId=$this->$model->insertData($insData,'ret_tag_scanned');

        				if($insId)

        				{

        					$data=array('status'=> TRUE,'tag_details'=>$tag_details,'msg'=>'Tag Scanned Successfully.');

        				}

        				else{

        						$data=array('status'=>FALSE,'msg'=>'Unable To Proceed Your Request');

        					}

        			}

        		}

        		else{

        			$data=array('status'=>FALSE,'msg'=>'Tag Already Scanned.');

        		}

		    }else{

		        $data=array('status'=>FALSE,'msg'=>'Please Select Valid Product.');

		    }

	    }

	    else if($tag_details['tag_status']==1){

	        $data=array('status'=>FALSE,'msg'=>'Tag Already Sold.');

	    }

	    else if($tag_details['tag_status']==2){

	        $data=array('status'=>FALSE,'msg'=>'Tag Deleted..');

	    }

	    else if($tag_details['tag_status']==3){

	        $data=array('status'=>FALSE,'msg'=>'Tag other issued..');

	    }else{

	        $data=array('status'=>FALSE,'msg'=>'Tag Deleted For Stock Checking');

	    }



		if($this->db->trans_status()===TRUE)

		{

			$this->db->trans_commit();

		}else{

			$this->db->trans_rollback();

		}

		echo json_encode($data);

	}*/





	function get_tag_scan_details()
	{
		$model =	self::RET_REPORT_MODEL;
		$allow_scan = false;
		$tag_id         = $this->input->post('tag_id');
		$old_tag_id     = $this->input->post('old_tag_id');
		$id_product     = $this->input->post('id_product');
		$design         = $this->input->post('design');
		$sub_design     = $this->input->post('sub_design');
		$id_section     = $this->input->post('id_section');
		$id_size         = $this->input->post('id_size');
		if(is_array($id_product)) { $id_product = !empty($id_product) ? $id_product[0] : ''; }
		if(is_array($design)) { $design = !empty($design) ? $design[0] : ''; }
		if(is_array($sub_design)) { $sub_design = !empty($sub_design) ? $sub_design[0] : ''; }
		if(is_array($id_section)) { $id_section = !empty($id_section) ? $id_section[0] : ''; }
		if(is_array($id_size)) { $id_size = !empty($id_size) ? $id_size[0] : ''; }
		$print_taken    = $this->input->post('print_taken');
		$id_branch      = $this->input->post('id_branch');
		$tag_details    = $this->$model->get_entry_records($tag_id, $old_tag_id, $design, $sub_design, $id_size);
		$this->db->trans_begin();
		if ($id_product == 0) {
			$id_product = $tag_details['product_id'];
		}
		if ($tag_details['tag_status'] == 0) {
			if ($id_product == $tag_details['product_id'] && $id_section == $tag_details['id_section']) {

				$status = $this->$model->get_scanned_details($tag_details['tag_id'], $id_branch);
				if ($status) {
					$scan_prod_status = $this->$model->get_product_scan_details($id_product, $id_section, $id_branch);

					if ($scan_prod_status['status']) {
						if ($this->session->userdata('branch_settings') == 1) {
							if ($tag_details['current_branch'] == $id_branch) {
								$allow_scan = true;
							} else {
								$allow_scan = false;
								$data = array('status' => FALSE, 'msg' => 'Invalid Branch Please Contact Your Admin');
							}
						} else {
							$allow_scan = true;
						}
						if ($allow_scan) {
							//Scan Start and End Time
							$to_time = date("Y-m-d H:i:s", strtotime('+2160 hours'));
							$insData = array(
								'id_product' => $id_product,
								'id_section' => $id_section,
								'from_time'  => date("Y-m-d H:i:s"),
								'to_time'    => $to_time,
								'status'     => 1,
								'id_branch'  => ($id_branch != '' ? $id_branch : NULL),
								'created_by' => $this->session->userdata('uid'),
							);
							$insId = $this->$model->insertData($insData, 'ret_tag_scan');

							if ($insId) {
								$tag_insert = array(
									'tag_id'	=> $tag_details['tag_id'],
									'id_scanned' => $insId,
									'id_branch' => ($id_branch != '' ? $id_branch : NULL),
									'date_add' => $this->get_day_close_date($id_branch),
									'created_by' => $this->session->userdata('uid'),
								);
								$tag_ins = $this->$model->insertData($tag_insert, 'ret_tag_scanned');
								//print_r($this->db->last_query());exit;
								if ($tag_ins) {
									$get_tagging = $this->$model->get_tagging($id_branch, $id_product);
									$data = array('status' => TRUE, 'tag_details' => $tag_details, 'msg' => 'Tag Scanned Successfully.');
								} else {
									$data = array('status' => FALSE, 'msg' => 'Unable To Proceed Your Request');
								}
							}
						}
					} else {
						$to_time = strtotime($scan_prod_status['prod_det']['to_time']);
						if (time() <= $to_time) {
							if ($this->session->userdata('branch_settings') == 1) {
								if ($tag_details['current_branch'] == $id_branch) {
									$allow_scan = true;
								} else {
									$allow_scan = false;
									$data = array('status' => FALSE, 'msg' => 'Invalid Branch Please Contact Your Admin');
								}
							} else {
								$allow_scan = true;
							}
						} else {
							$allow_scan = false;
							$data = array('status' => FALSE, 'msg' => 'Scan Time Exists Please Contact Your Admin.');
							$scan_status = array('status' => 0, 'id_product' => $id_product, 'id_section' => $id_section, 'id_branch' => $id_branch);
							$this->$model->update_tag_scan('ret_tag_scan', $scan_status);
							//print_r($this->db->last_query());exit;
						}

						if ($allow_scan) {
							//Scan Start and End Time
							$tag_insert = array(
								'tag_id'	=> $tag_details['tag_id'],
								'id_scanned' => $scan_prod_status['prod_det']['id_scanned'],
								'id_branch' => ($id_branch != '' ? $id_branch : NULL),
								'date_add' => $this->get_day_close_date($id_branch),
								'created_by' => $this->session->userdata('uid'),
							);
							$tag_ins = $this->$model->insertData($tag_insert, 'ret_tag_scanned');
							//print_r($this->db->last_query());exit;
							if ($tag_ins) {
								$get_tagging = $this->$model->get_tagging($id_branch, $id_product);
								$data = array('status' => TRUE, 'tag_details' => $tag_details, 'msg' => 'Tag Scanned Successfully.');
							} else {
								$data = array('status' => FALSE, 'msg' => 'Unable To Proceed Your Request');
							}
						}
					}
				} else {
					$data = array('status' => FALSE, 'msg' => 'Tag Already Scanned.');
				}
			} else {
				if ($id_product != $tag_details['product_id']) {
					$data = array('status' => FALSE, 'msg' => 'Please Select Valid Product.');
				}
				if ($id_section != $tag_details['id_section']) {
					$data = array('status' => FALSE, 'section' => $tag_details['id_section'], 'msg' => 'Please Select Valid Section.');
				}
				if ($design != $tag_details['design_id']) {
					$data = array('status' => FALSE, 'msg' => 'Please Select Valid Design.');
				}
				if ($sub_design != $tag_details['id_sub_design']) {
					$data = array('status' => FALSE, 'msg' => 'Please Select Valid Sub Design.');
				}
				if ($id_size != $tag_details['id_size']) {
					$data = array('status' => FALSE, 'msg' => 'Please Select Valid Size.');
				}
			}
		} else if ($tag_details['tag_status'] == 1) {
			$data = array('status' => FALSE, 'msg' => 'Tag Already Sold.');
		} else if ($tag_details['tag_status'] == 2) {
			$data = array('status' => FALSE, 'msg' => 'Tag Deleted..');
		} else if ($tag_details['tag_status'] == 3) {
			$data = array('status' => FALSE, 'msg' => 'Tag other issued..');
		} else {
			$data = array('status' => FALSE, 'msg' => 'Tag Deleted For Stock Checking');
		}

		if ($this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
		} else {
			$this->db->trans_rollback();
		}
		echo json_encode($data);
	}

	//Tag scan


	//Tag scan



	function bill_wise_transcation($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'bill_wise_transcation';
				$this->load->view('layout/template', $data);
				break;

			case 'village':
				$data = $this->$model->get_allVillage();
				echo json_encode($data);
				break;

			case 'customer':
				$data = $this->$model->get_allCustomer($_POST);
				echo json_encode($data);
				break;

			case 'ajax':
				$id_branch = $this->input->post('id_branch');

				$bill_wise_type = $this->input->post('bill_wise_type');

				// print_r($bill_wise_type);exit;

				$list = $this->$model->getBillingDetails($_POST);

				if ($bill_wise_type == 0 || $bill_wise_type == 6) {
					$receipt = $this->$model->getreceiptDetails($_POST);
				}
				$access = $this->admin_settings_model->get_access('admin_ret_reports/bill_wise_transcation/list');
				$data = array(
					'list'  => $list,
					'receipt' => $receipt,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}



	function home_bill($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'home_bill';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_home_bill_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/home_bill/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function order_advance($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'order_advance';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_order_advance($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/order_advance/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}





	function est_referral($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'est_referral';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_est_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/est_referral/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function karigar_wise($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'karigar_wise';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_est_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/karigar_wise/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function gift_voucher($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'gift_voucher';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_gift_voucher_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/gift_voucher/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function update_gift_status()

	{

		$model =	self::RET_REPORT_MODEL;

		$this->db->trans_begin();

		$reqdata   = $this->input->post('req_data');

		foreach ($reqdata as $data) {

			$updID = $this->$model->updateData(array('status' => 5), 'id_gift_card', $data['id_gift_card'], 'gift_card');
		}

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$data = array('status' => true, 'msg' => 'Gift Canceled Successfully.');
		} else {

			$this->db->trans_rollback();

			$data = array('status' => false, 'msg' => 'Unable TO Proceed Your Request.');
		}

		echo json_encode($data);
	}



	function order_status($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'order_status';

				$this->load->view('layout/template', $data);

				break;

			case 'order_status':

				$data = $this->$model->order_status_message();

				echo json_encode($data);

				break;

			case 'ajax':

				$list = $this->$model->order_status($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/order_status/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	public function ajax_get_village()

	{

		$model_name = self::RET_REPORT_MODEL;

		$cus_data = $this->$model_name->get_village();

		echo json_encode($cus_data);
	}



	public function get_bill_customer()

	{

		$model_name = self::RET_REPORT_MODEL;

		$cus_data = $this->$model_name->get_bill_customer();

		echo json_encode($cus_data);
	}

	public function get_advance_customer()
	{
		$model_name = self::RET_REPORT_MODEL;
		$cus_data   = $this->$model_name->get_advance_customer();
		echo json_encode($cus_data);
	}



	//Tag History

	function tag_history($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {



			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'tag_history';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_tag_history($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/tag_history/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function getTaggingBySearch()

	{

		$model =	self::RET_REPORT_MODEL;

		$data = $this->$model->getTaggingBySearch($_POST['searchTxt']);

		echo json_encode($data);
	}



	//Tag History



	//Monthly sales search

	function monthly_sales($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {



			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'monthly_sales';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->monthly_sales($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/monthly_sales/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}





	//Monthly sales search



	//Old Metal Profit and Loss

	function old_metal_analyse($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'old_metal_analyse';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getOldMetalAnalyse($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/old_metal_purchase/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function get_old_metal_type()

	{

		$model =	self::RET_REPORT_MODEL;

		$data = $this->$model->get_old_metal_type();

		echo json_encode($data);
	}



	//Old Metal Profit and Loss





	//sales analysis report



	function sales_analysis_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'analysis_report/list';

				$this->load->view('layout/template', $data);

				break;



			case 'chit_details':

				$data['main_content'] = self::VIEW_FOLDER . 'analysis_report/crm_list';

				$this->load->view('layout/template', $data);

				break;



			case 'without_acc_list':

				$data['main_content'] = self::VIEW_FOLDER . 'analysis_report/without_acc';

				$this->load->view('layout/template', $data);

				break;



			case 'product_details':

				$data['main_content'] = self::VIEW_FOLDER . 'analysis_report/product_details';

				$this->load->view('layout/template', $data);

				break;

			case 'product_analysis':

				$data = $this->$model->product_analysis_details($_POST);

				echo json_encode($data);

				break;

			case 'sales_details':

				$data['main_content'] = self::VIEW_FOLDER . 'sales_details';

				$this->load->view('layout/template', $data);

				break;





			case 'crm_analysis_details':

				$data = $this->$model->get_crm_analysis_details($_POST);

				echo json_encode($data);

				break;



			case 'analysis_details':

				$data = $this->$model->get_sales_analysis_details($_POST);

				echo json_encode($data);

				break;



			case 'without_acc_details':

				$data = $this->$model->get_customer_without_acc($_POST);

				echo json_encode($data);

				break;



			case 'sales_analysis_other_city':

				$data = $this->$model->sales_analysis_other_city($_POST);

				echo json_encode($data);

				break;



			case 'ajax':

				$list = $this->$model->sales_analysis_report($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/old_metal_purchase/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function ajax_zone_list()

	{

		$model =	self::RET_REPORT_MODEL;

		$data = $this->$model->getActiveZone($_POST['id_branch']);

		echo json_encode($data);
	}





	//sales analysis report





	//History

	function customer_history($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'customer_history';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$data = $this->$model->get_customer_details($_POST['mobile']);

				echo json_encode($data);

				break;
		}
	}

	//History



	//Unbilled Estimation

	function unbilled_estimation($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'unbilled_estimation';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$data = $this->$model->unbilled_estimation($_POST);

				echo json_encode($data);

				break;
		}
	}

	//Unbilled Estimation



	//karigar wise sales

	function karigar_wise_sales($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {



			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'karigar_wise_sales';



				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$data = $this->$model->karigar_wise_sales($_POST['id_product']);

				echo json_encode($data);

				break;
		}
	}

	//karigar wise sales



	//karigar wise sales

	function lot_history($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {



			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'lot_history';

				$this->load->view('layout/template', $data);

				break;

			case 'lot_details':

				$data['main_content'] = self::VIEW_FOLDER . 'lot_details';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$data = $this->$model->getLotDetails();

				echo json_encode($data);

				break;
		}
	}



	function lot_details()

	{

		$model =	self::RET_REPORT_MODEL;

		$data = $this->$model->getLotWiseSales($_POST['lot_no']);

		echo json_encode($data);
	}

	//karigar wise sales





	//Green Tag Report

	function green_tag($type = "")

	{

		$model =	self::RET_REPORT_MODEL;



		switch ($type) {

			case 'list':

				$data['sales_incentive_green_tag'] = $this->$model->get_ret_settings('sales_incentive_green_tag');

				$data['emp_sales_incentive_gold_perg'] = $gold_per_gram_amt = $this->$model->get_ret_settings('emp_sales_incentive_gold_perg');      //GOld Per Gram Value

				$data['emp_sales_incentive_silver_perg'] = $silver_per_gram_amt = $this->$model->get_ret_settings('emp_sales_incentive_silver_perg'); //Silver Per Gram Value

				$data['main_content'] = self::VIEW_FOLDER . 'green_tag';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$data = $this->$model->getGreenTagDetails($_POST);

				echo json_encode($data);

				break;
		}
	}

	//Green Tag Report



	//Sales Comparision

	function sales_comparision($type = "")

	{

		$model =	self::RET_REPORT_MODEL;



		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'sales_comparision';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$data = $this->$model->getMonthlySalesDetails($_POST['id_product']);

				echo json_encode($data);

				break;
		}
	}

	//Sales Comparision





	//Credit Pending Report

	function credit_pending_14_02_2024($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'credit_pending';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_credit_pending_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/credit_history/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}


	function credit_pending($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'credit_pending';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->get_credit_pending_details($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/credit_history/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}

	function get_influencer()
	{
		$model = self::RET_REPORT_MODEL;

		$data = $this->$model->get_influencer();
		echo json_encode($data);
	}

	//Credit Pending Report



	//Credit Pending Report

	function stock_and_sales_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'stock_and_sales_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->stock_and_sales_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/stock_and_sales_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//Credit Pending Report





	//Incentive Report

	function incentive_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'incentive_report';

				$this->load->view('layout/template', $data);

				break;

			case 'emp_list':

				$data['main_content'] = self::VIEW_FOLDER . 'emp_incentive';

				$this->load->view('layout/template', $data);

				break;

			case 'emp_report':

				$id_account = $_POST['id_wallet_account'];

				$data = $this->$model->incentive_emp_list($id_account);

				echo json_encode($data);

				break;

			case 'ajax':

				$list = $this->$model->get_incentive_report($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/incentive_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function debit_payment()

	{

		$model =	self::RET_REPORT_MODEL;

		$response_data = array();

		$insert_data = $this->input->post('req_data');

		foreach ($insert_data as $value) {

			$Data = array(

				'id_wallet_account' => $value['id_wallet_account'],

				'id_employee' 	    =>  $this->session->userdata('uid'),

				'transaction_type' 	=> 1,

				'type'              => 1,

				'value' 		    => $value['debit_amount'],

				'date_transaction'  => date("Y-m-d H:i:s"),

				'description'       => 'GT Debit Transcation'

			);

			$this->db->trans_begin();

			$status = $this->$model->insertData($Data, 'wallet_transaction');
		}

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$response_data = array('status' => TRUE, 'message' => 'Amount Debited Successfully.');
		} else {

			$this->db->trans_rollback();

			$response_data = array('status' => FALSE, 'message' => 'Unable to proceed Your Request.');
		}

		echo json_encode($response_data);
	}



	//Incentive Report



	//monthly sales comparision

	function monthly_slaes_comparision($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'monthly_comparision_report';

				$this->load->view('layout/template', $data);

				break;

			case 'detailed_list':

				$data['main_content'] = self::VIEW_FOLDER . 'monthly_sales_details';

				$this->load->view('layout/template', $data);

				break;

			case 'sales_details':

				$data = $this->$model->get_monthly_sales_details($_POST);

				echo json_encode($data);

				break;

			case 'ajax':

				$list = $this->$model->getVillageWiseSales($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/old_metal_stock/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	//Telecalling Module

	function telecalling($type = "")

	{
		$SETT_MOD = "admin_settings_model";
		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'telecalling';
				$data['access'] = $this->$SETT_MOD->get_access('admin_ret_reports/telecalling/list');
				$this->load->view('layout/template', $data);

				break;
		}
	}



	function get_telecalling_report()

	{

		$model =	self::RET_REPORT_MODEL;

		$data = $this->$model->get_telecalling_cus_det($_POST);

		echo json_encode($data);
	}



	function feedback_report($type = "", $id = "")

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'feedback_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$data = $this->$model->get_feedbackById($id);

				echo json_encode($data);

				break;
		}
	}



	function get_feedbackReport()

	{

		$model = self::RET_REPORT_MODEL;

		$data = $this->$model->get_feedbackReport();

		echo json_encode($data);
	}



	function update_vip_customer()

	{

		$model =	self::RET_REPORT_MODEL;

		$reqdata   = $this->input->post('req_data');

		$this->db->trans_begin();

		foreach ($reqdata as $cus) {

			$data = array('is_vip' => 1, 'date_upd' => date("Y-m-d H:i:s"));

			$status = $this->$model->updateData($data, 'id_customer', $cus['id_customer'], 'customer');
		}

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$response_data = array('status' => TRUE, 'message' => 'VIP Marked successfully.');
		} else {

			$this->db->trans_rollback();

			$response_data = array('status' => FALSE, 'message' => 'Unable to proceed Your Request.');
		}

		echo json_encode($response_data);
	}





	//Telecalling Module



	// customer edit log STARTS

	function customer_edit_log($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'customer_edit_log';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_customer_edit_log($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/stock_and_sales_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	// customer edit log ENDS





	// GT Return Report STARTS

	function gt_return_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['sales_incentive_green_tag'] = $this->$model->get_ret_settings('sales_incentive_green_tag');

				$data['emp_sales_incentive_gold_perg'] = $gold_per_gram_amt = $this->$model->get_ret_settings('emp_sales_incentive_gold_perg');      //GOld Per Gram Value

				$data['emp_sales_incentive_silver_perg'] = $silver_per_gram_amt = $this->$model->get_ret_settings('emp_sales_incentive_silver_perg'); //Silver Per Gram Value

				$data['main_content'] = self::VIEW_FOLDER . 'gt_return_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax_data':

				$list = $this->$model->get_gt_return_report();

				echo json_encode($list);

				break;
		}
	}

	// GT Return Report ENDS





	//Dashboard Reports

	function dashboard_estimationList()

	{

		$from_date = $this->input->post('from_date');

		$to_date = $this->input->post('to_date');

		$type = $this->input->post('type');

		$id_branch = $this->input->post('id_branch');



		$model =	self::RET_REPORT_MODEL;

		$data['estimate_list'] = $this->$model->dashboard_EstimationList($from_date, $to_date, $type, $id_branch);

		echo json_encode($data);
	}



	function dashboard_estimation()

	{

		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/estimation_list';

		$this->load->view('layout/template', $data);
	}



	function dashboard_salesList()

	{

		$from_date = $this->input->post('from_date');

		$to_date = $this->input->post('to_date');

		$type = $this->input->post('type');

		$id_branch = $this->input->post('id_branch');



		$model =	self::RET_REPORT_MODEL;

		$data['sales_list'] = $this->$model->dashboard_salesList($from_date, $to_date, $type, $id_branch);

		echo json_encode($data);
	}



	function dashboard_sales()

	{

		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/sales_list';

		$this->load->view('layout/template', $data);
	}



	function get_stock_detail_list($type)

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'dashboard/stock_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$from_date = $this->input->post('from_date');

				$to_date = $this->input->post('to_date');

				$id_metal = $this->input->post('id_metal');

				$id_branch = $this->input->post('id_branch');

				$list = $this->$model->get_stock_detail_list($from_date, $to_date, $id_branch, $id_metal);

				$data = array(

					'list'  => $list

				);

				echo json_encode($data);

				break;
		}
	}





	function dashboard_greentagList()

	{

		$from_date = $this->input->post('from_date');

		$to_date = $this->input->post('to_date');

		$id_branch = $this->input->post('id_branch');



		$model =	self::RET_REPORT_MODEL;

		$data['greentag_list'] = $this->$model->dashboard_greentagList($from_date, $to_date, $id_branch);

		echo json_encode($data);
	}



	function dashboard_greentag()

	{

		$model =	self::RET_REPORT_MODEL;

		$data['emp_sales_incentive_gold_perg'] = $gold_per_gram_amt = $this->$model->get_ret_settings('emp_sales_incentive_gold_perg');      //GOld Per Gram Value

		$data['emp_sales_incentive_silver_perg'] = $silver_per_gram_amt = $this->$model->get_ret_settings('emp_sales_incentive_silver_perg'); //Silver Per Gram Value

		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/greentag_list';

		$this->load->view('layout/template', $data);
	}





	function dashboard_oldmetalList()

	{

		$from_date = $this->input->post('from_date');

		$to_date = $this->input->post('to_date');

		$type = $this->input->post('type');

		$id_branch = $this->input->post('id_branch');



		$model =	self::RET_REPORT_MODEL;

		$data['oldmetal_list'] = $this->$model->dashboard_oldmetalList($from_date, $to_date, $type, $id_branch);

		echo json_encode($data);
	}



	function dashboard_oldmetal()

	{

		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/oldmetal_list';

		$this->load->view('layout/template', $data);
	}





	function dashboard_creditsalesList()

	{

		$from_date = $this->input->post('from_date');

		$to_date = $this->input->post('to_date');

		$type = $this->input->post('type');

		$id_branch = $this->input->post('id_branch');



		$model =	self::RET_REPORT_MODEL;

		$data['issrec_list'] = $this->$model->dashboard_creditsalesList($from_date, $to_date, $type, $id_branch);

		echo json_encode($data);
	}

	function dashboard_creditsales()

	{

		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/issrec_list';

		$this->load->view('layout/template', $data);
	}



	function dashboard_giftcardList()

	{

		$from_date = $this->input->post('from_date');

		$to_date = $this->input->post('to_date');

		$type = $this->input->post('type');

		$id_branch = $this->input->post('id_branch');



		$model =	self::RET_REPORT_MODEL;

		$data['giftcard_list'] = $this->$model->dashboard_giftcardList($from_date, $to_date, $type, $id_branch);

		echo json_encode($data);
	}



	function dashboard_giftcard()

	{

		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/giftcard_list';

		$this->load->view('layout/template', $data);
	}



	function dashboard_virtualsalesList()

	{

		$from_date = $this->input->post('from_date');

		$to_date = $this->input->post('to_date');

		$type = $this->input->post('type');

		$id_branch = $this->input->post('id_branch');



		$model =	self::RET_REPORT_MODEL;

		$data['virtual_list'] = $this->$model->dashboard_virtualsalesList($from_date, $to_date, $type, $id_branch);

		echo json_encode($data);
	}

	function dashboard_virtualsales()

	{

		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/virtualsales_list';

		$this->load->view('layout/template', $data);
	}



	function dashboard_salereturnList()

	{

		$from_date = $this->input->post('from_date');

		$to_date = $this->input->post('to_date');

		$id_branch = $this->input->post('id_branch');



		$model =	self::RET_REPORT_MODEL;

		$data['salereturn_list'] = $this->$model->dashboard_salereturnList($from_date, $to_date, $id_branch);

		echo json_encode($data);
	}



	function dashboard_salereturn()

	{

		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/salereturn_list';

		$this->load->view('layout/template', $data);
	}



	function dashboard_lottagList()

	{

		$from_date = $this->input->post('from_date');

		$to_date = $this->input->post('to_date');

		$type = $this->input->post('type');

		$id_branch = $this->input->post('id_branch');



		$model =	self::RET_REPORT_MODEL;

		$data['lottag_list'] = $this->$model->dashboard_taglotList($from_date, $to_date, $type, $id_branch);

		echo json_encode($data);
	}



	function dashboard_lottag()

	{

		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/lottag_list';

		$this->load->view('layout/template', $data);
	}



	function dashboard_customerorderList()

	{

		$from_date = $this->input->post('from_date');

		$to_date = $this->input->post('to_date');

		$type = $this->input->post('type');

		$id_branch = $this->input->post('id_branch');



		$model =	self::RET_REPORT_MODEL;

		$data['customerorder_list'] = $this->$model->dashboard_customerorderList($from_date, $to_date, $type, $id_branch);

		echo json_encode($data);
	}



	function dashboard_customerorder()

	{

		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/customerorder_list';

		$this->load->view('layout/template', $data);
	}



	function dashboard_lotList()

	{

		$from_date = $this->input->post('from_date');

		$to_date = $this->input->post('to_date');

		$type = $this->input->post('type');

		$id_branch = $this->input->post('id_branch');



		$model = self::RET_REPORT_MODEL;

		$data['lot_list'] = $this->$model->dashboard_taglotList($from_date, $to_date, $type, $id_branch);

		echo json_encode($data);
	}



	function dashboard_lot()

	{

		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/lot_list';

		$this->load->view('layout/template', $data);
	}



	function dashboard_tagList()

	{

		$from_date = $this->input->post('from_date');

		$to_date = $this->input->post('to_date');

		$type = $this->input->post('type');

		$id_branch = $this->input->post('id_branch');



		$model = self::RET_REPORT_MODEL;

		$data['tag_list'] = $this->$model->dashboard_taglotList($from_date, $to_date, $type, $id_branch);

		echo json_encode($data);
	}



	function dashboard_tag()

	{

		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/tag_list';

		$this->load->view('layout/template', $data);
	}

	function dashboard_ordermanagement()
	{
		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/ordermanagement_list.php';
		// $data['main_content'] = self::VIEW_FOLDER.'order_status';

		$this->load->view('layout/template', $data);
	}

	function dashboard_ordermangemnetlist()
	{

		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		$type = $this->input->post('type');
		$id_branch = $this->input->post('id_branch');
		$id_product = $this->input->post('id_product');
		$id_design = $this->input->post('id_design');
		$id_sub_design = $this->input->post('id_sub_design');

		$model =	self::RET_REPORT_MODEL;
		$data['order_details'] = $this->$model->dashboard_ordermanagementlist($from_date, $to_date, $type, $id_branch, $id_product, $id_design, $id_sub_design);
		// print_r($data);exit;
		echo json_encode($data);
	}



	//Dashboard Reports







	//Metal Stock Report

	function metal_stock_details($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'metal_stock_report';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_metal_stock_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/old_metal_stock/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function metal_available_stock_details($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'metal_available_stock_details';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_available_metal_stock_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/metal_available_stock_details/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	//Metal Stock Report





	//Purchase bills Report

	function purchase($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'purchasebills_report';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_purchasebills_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/purchase/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//Purchase bills Report



	//GRN bills Report

	function grnbills($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['charges'] = $this->$model->get_charges();

				$data['main_content'] = self::VIEW_FOLDER . 'grnbills_report';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_grnbills_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/grnbills/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//GRN bills Report





	//GRN bills Report

	function purchase_return($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				// $data['charges'] = $this->$model->get_charges();

				$data['main_content'] = self::VIEW_FOLDER . 'purchase_return';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_purchase_return_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/purchase_return/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//GRN bills Report



	//payments



	//payments



	//Purchase QC Report

	function qcstatus($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'purchasebillsqc_report';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_purchasebillsqc_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/qcstatus/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//Purchase QC Report



	//Purchase QC Report

	function pohmstatus($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'purchasebillshm_report';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_purchasebillshm_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/pohmstatus/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//Purchase QC Report



	//Purchase Vs Sales wastage profit report

	function tagwiseprofit($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'tag_wise_wastage_profit';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$id_branch = $this->input->post('id_branch');





				$list = $this->$model->get_wastagewisepandlreport($_POST);





				$access = $this->admin_settings_model->get_access('admin_ret_reports/tagwiseprofit/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	//Purchase Payment report

	function popayments($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'popayments';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$id_branch = $this->input->post('id_branch');



				$list = $this->$model->get_po_payments($_POST);



				$access = $this->admin_settings_model->get_access('admin_ret_reports/popayments/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;

			case 'verify':

				$pay_id = $this->input->post('pay_id');
				$result = $this->$model->verify_po_payment($pay_id);
				echo json_encode(array('status' => $result ? 'success' : 'error'));

				break;
		}
	}

	//Purchase payment report





	//rate fixing

	function rate_fixed($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'rate_fixed';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_rate_fixed_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/rate_fixed/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//rate fixing



	//Unfixing report

	function unfixing_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'unfixing_report';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_rate_unfixed_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/unfixing_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//Unfixing report



	//Approval stock design wise report

	function approvaltag_items_designwise($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'approvaltag_items_designwise';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getApprovalTaggeditems($_POST);

				$branchwise = $this->$model->getapprovalTaggeditems_branchwise($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/approvaltag_items_designwise/list');

				$data = array(

					'list'          => $list,

					'stock_details'    => $branchwise,

					'access'        => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//Approval stock design wise report



	//Customer ledger statement

	function customer_ledger_statement($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'customer_ledger_statement';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getCustomerLedger();

				$access = $this->admin_settings_model->get_access('admin_ret_reports/customer_ledger_statement/list');

				$data = array(

					'list'          => $list,

					'access'        => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//Customer ledger statement



	//Supplier ledger statement

	function supplierledger($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'supplier_ledger_statement';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getSupplierLedger($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/supplierledger/list');

				$data = array(

					'list'          => $list,

					'access'        => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//Supplier ledger statement





	//Smith transaction statement

	function smithtransaction($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'smithtransactions';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getSmithTransactionList($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/smithtransaction/list');

				$data = array(

					'list'          => $list,

					'access'        => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//Smith transaction statement



	//Supplier transaction statement

	function suppliertransaction($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'suppliertransaction';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$metal_wise_data = array();

				$list = $this->$model->getSupplierTransactionList($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/suppliertransaction/list');

				if (!empty($_POST['metal_wise_required'])) {
					$metal_wise_data  = $this->$model->getMetalwiseSupplierTransactionList($_POST);
				}

				$data = array(

					'list'          => $list,

					'access'        => $access,

					'metalwisedata' => $metal_wise_data

				);

				echo json_encode($data);

				break;
		}
	}

	//Supplier transaction statement



	//Supplier approval transaction statement

	function supplier_approval_transaction($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'supplier_approval_transaction';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$metal_wise_data = array();

				$list = $this->$model->getSupplierApprovalTransactionList($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/supplier_approval_transaction/list');

				if (!empty($_POST['metal_wise_required'])) {
					$metal_wise_data  = $this->$model->getMetalwiseApprovalTransactionList($_POST);
				}

				$data = array(

					'list'          => $list,

					'access'        => $access,

					'metalwisedata' => $metal_wise_data

				);

				echo json_encode($data);

				break;
		}
	}

	//Supplier approval transaction statement



	//Smith transaction statement

	function smithalltransactions($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'smithalltransactions';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getSmithAllTransactionList($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/smithalltransactions/list');

				$data = array(

					'list'          => $list,

					'access'        => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//Smith transaction statement







	//old tag import



	function old_sale_report($type = "")

	{

		$model = self::RET_REPORT_MODEL;

		$SETT_MOD = self::SETT_MOD;


		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'old_sale_report';

				$data['access'] = $this->$SETT_MOD->get_access('admin_ret_reports/old_sale_report/list');

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_old_sale_report_report($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/old_sale_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function upload_data($filename, $pathToUpload)

	{

		if ($_FILES && $_FILES['filepath']["tmp_name"] != "") {



			if (!is_dir($pathToUpload)) {

				mkdir($pathToUpload, 0777, TRUE);
			}

			/* $files = glob(self::DATA_FILE_PATH.'*'); // get all file names

				foreach($files as $file){ // iterate files

			  if(is_file($file))

			    unlink($file); // delete file

			}*/

			//echo $pathToUpload.$filename;

			$config['upload_path'] = $pathToUpload;

			$config['allowed_types'] = 'xlsx|xls';

			$config['file_name'] = $filename;

			$config['overwrite'] = TRUE;

			$this->upload->initialize($config);

			if (!$this->upload->do_upload('filepath')) {

				$error = array('error' => $this->upload->display_errors());





				return false;
			} else {



				return true;
			}
		}
	}



	function old_tag_import_report($type = "")

	{

		$model = self::RET_REPORT_MODEL;

		$SETT_MOD = self::SETT_MOD;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'old_tag_import_report';

				$data['access'] = $this->$SETT_MOD->get_access('admin_ret_reports/old_tag_import_report/list');

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_old_tag_import_report($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/old_tag_import_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function file_upload_tags()

	{

		$return_data = array();

		$dir = "assets/upload/old_tag_files/";

		$model = self::RET_REPORT_MODEL;

		$imagePath = $_FILES["filepath"]["name"];

		$ext = pathinfo($imagePath, PATHINFO_EXTENSION);

		$file = basename($imagePath, "." . $ext);

		$pathToUpload = self::DATA_FILE_PATH;

		$filename = trim(date('Ymd_His') . '_tagimportdata' . '.xlsx');

		$this->upload_data($filename, $pathToUpload);

		$imp_data = $this->admin_settings_model->import_excel($pathToUpload, $filename);

		echo "<pre>";

		print_r($imp_data);

		echo "</pre>";
		exit;
	}



	function file_upload_stones()

	{

		$return_data = array();
	}



	function file_upload_old_tags()

	{

		$return_data = array();

		$old_tag_code = [];

		$old_tran_no = [];

		$dataset = [];

		$sold_out_tag = [];

		$dir = "assets/upload/old_tag_files/";

		$model = self::RET_REPORT_MODEL;

		$id_branch = $_POST['id_branch'];

		$imagePath = $_FILES["filepath"]["name"];

		$ext = pathinfo($imagePath, PATHINFO_EXTENSION);

		$file = basename($imagePath, "." . $ext);

		$pathToUpload = self::DATA_FILE_PATH;

		$filename = trim(date('Ymd_His') . '_data' . '.xlsx');

		$this->upload_data($filename, $pathToUpload);

		$imp_data = $this->admin_settings_model->import_excel($pathToUpload, $filename);

		/*echo "<pre>";

			print_r($imp_data);

		    echo "</pre>";exit;*/

		if (!empty($imp_data)) {

			foreach ($imp_data as $key => $value) {



				$key_name_tag_no = array_search('TAGNO', $imp_data[$key]);

				if ($key_name_tag_no != '') {

					break;
				}
			}

			foreach ($imp_data as $key => $value) {

				$key_name_tran_no = array_search('BranchID', $imp_data[$key]);

				if ($key_name_tran_no != '') {

					break;
				}
			}

			foreach ($imp_data as $key => $value) {



				array_push($old_tag_code, $value[$key_name_tag_no]);
			}

			foreach ($imp_data as $key => $value) {

				array_push($old_tran_no, $value[$key_name_tran_no]);
			}



			$unset_key_name = array_search('TAGNO', $old_tag_code);

			$unset_empty = array_search('', $old_tag_code);

			unset($old_tag_code[$unset_key_name]);

			unset($old_tag_code[$unset_empty]);

			$unset_key_name_tran = array_search('BranchID', $old_tran_no);

			foreach (array_filter($old_tag_code) as $i => $tag_no) {

				foreach (array_filter($old_tran_no) as $j => $tran_no) {

					if ($i == $j) {

						$data = array(

							'import_tag_code' =>  $this->check_old_tag_code($tag_no),

							'tran_no'   => $tag_no,

							'import_date'  => date('Y-m-d H:i:s'),

							'import_branch'	=> $tran_no,

							'import_tag_status' => $this->check_old_tag_code_mismatched($tag_no),

							'import_by' => 1
						);

						array_push($dataset, $data);
					}
				}
			}

			//echo '<pre>';

			//print_r(($dataset));exit;



			foreach ($dataset as $value) {

				if(($value['import_tag_code'] != '' || $value['import_tag_code'] != null) && ($value['tran_no'] != null || $value['tran_no'] != '')){
					$insert_data = $value;

					$this->db->trans_begin();

					$insId = $this->$model->insertData($insert_data, 'ret_old_tag_import');

					if ($insId) {

						if ($insert_data['import_tag_status'] == 1) {



							if ($insert_data['import_tag_code'] != '') {

								$update_data = array('sold_from' => 2, 'tag_status' => 1);

								$condtion_data = array('tag_status'  => 0, 'remarks' => '' . $insert_data['import_tag_code'] . '');

								$status = $this->$model->old_updateData($update_data, $condtion_data, 'ret_taging');

								// print_r($this->db->last_query());

								if ($status) {

									$insert_log_data = array(
										'tag_id'  => $this->get_our_tag_code($insert_data['import_tag_code']),

										'status'      => 9,

										'from_branch' => $this->get_current_branch($insert_data['import_tag_code']),

										'to_branch'   => NULL,

										'date'        => $this->get_day_close_date($this->get_current_branch($insert_data['import_tag_code'])),

										'created_on'  => date('Y-m-d H:i:s'),

										'created_by'  => $this->session->userdata("uid")
									);

									$this->$model->insertData($insert_log_data, 'ret_taging_status_log');
								} else {

									$return_data = array('staus' => FALSE, 'message' => 'Unable to Proceed', 'last_query' => $this->db->last_query());
								}
							}
						}
					}
				}
			}

			//exit;

			/*foreach($dataset as $value)

			{

				if($value['import_tag_status']==1)

				{

					array_push($sold_out_tag,$value['import_tag_code']);

				}



			}*/



			/*	foreach($sold_out_tag as $value)

			{

				if($value != '' || $value != null)

				{

				$update_data = array('sold_from' => 2,

				                     'tag_status' => 1);

				$status=$this->$model->updateData($update_data,'old_tag_id',$value,'ret_taging');

				if($status)

				{

				    $insert_log_data = array('tag_id'  => $this->get_our_tag_code($value),

				                     'status'      => 9,

									 'from_branch' => $this->get_current_branch($value),

									 'to_branch'   => NULL,

									 'date'        => $this->get_day_close_date($this->get_current_branch($value)),

									 'created_on'  => date('Y-m-d H:i:s'),

                                     'created_by'  => $this->session->userdata("uid"));

				        $insId=$this->$model->insertData($insert_log_data,'ret_taging_status_log');

				}



				}

			}*/



			if ($this->db->trans_status() === TRUE) {

				$this->db->trans_commit();

				$return_data = array('staus' => true, 'message' => 'File Updated Successfully', 'last_query' => '');
			} else {

				$this->db->trans_rollback();

				$return_data = array('staus' => FALSE, 'message' => 'Unable to Prceed', 'last_query' => '');
			}
		} else {

			$return_data = array('staus' => FALSE, 'message' => 'File Not Uploaded', 'last_query' => '');
		}

		echo json_encode($return_data);
	}



	function file_upload_new_tags()

	{

		$return_data = array();

		$old_tag_code = [];

		$old_tran_no = [];

		$dataset = [];

		$sold_out_tag = [];

		$dir = "assets/upload/old_tag_files/";

		$model = self::RET_REPORT_MODEL;

		$id_branch = $_POST['id_branch'];

		$imagePath = $_FILES["filepath"]["name"];

		$ext = pathinfo($imagePath, PATHINFO_EXTENSION);

		$file = basename($imagePath, "." . $ext);

		$pathToUpload = self::DATA_FILE_PATH;

		$filename = trim(date('Ymd_His') . '_data' . '.xlsx');

		$this->upload_data($filename, $pathToUpload);

		$imp_data = $this->admin_settings_model->import_excel($pathToUpload, $filename);



		if (!empty($imp_data)) {

			foreach ($imp_data as $key => $value) {



				$key_name_tag_no = array_search('TAGNO', $imp_data[$key]);

				if ($key_name_tag_no != '') {

					break;
				}
			}



			foreach ($imp_data as $key => $value) {



				array_push($old_tag_code, $value[$key_name_tag_no]);
			}





			$unset_key_name = array_search('TAGNO', $old_tag_code);

			$unset_empty = array_search('', $old_tag_code);

			unset($old_tag_code[$unset_key_name]);

			unset($old_tag_code[$unset_empty]);

			foreach (array_filter($old_tag_code) as $i => $tag_no) {



				$data = array(

					'import_tag_code' =>  $this->check_old_tag_code($tag_no),

					'tran_no'   => $this->get_our_new_tag_code($tag_no),

					'import_date'  => date('Y-m-d H:i:s'),

					'import_branch'	=> $id_branch,

					'import_tag_status' => 1,

					'import_by' => $this->session->userdata("uid")
				);

				array_push($dataset, $data);
			}



			foreach ($dataset as $value) {

				if(($value['import_tag_code'] != '' || $value['import_tag_code'] != null) && ($value['tran_no'] != null || $value['tran_no'] != '') ){

					$insert_data = $value;

					$this->db->trans_begin();

					$insId = $this->$model->insertData($insert_data, 'ret_old_tag_import');

					if ($insId) {

						if ($insert_data['import_tag_status'] == 1) {



							if ($insert_data['import_tag_code'] != '') {

								$update_data = array('sold_from' => 1, 'tag_status' => 1);

								$condtion_data = array('tag_status'  => 0, 'tag_code' => '' . $insert_data['import_tag_code'] . '');

								$status = $this->$model->old_updateData($update_data, $condtion_data, 'ret_taging');

								//print_r($this->db->last_query());exit;

								if ($status) {

									$insert_log_data = array(
										'tag_id'  => $this->get_our_new_tag_code($insert_data['import_tag_code']),

										'status'      => 9,

										'from_branch' => $this->get_new_tag_current_branch($insert_data['import_tag_code']),

										'to_branch'   => NULL,

										'date'        => $this->get_day_close_date($this->get_new_tag_current_branch($insert_data['import_tag_code'])),

										'created_on'  => date('Y-m-d H:i:s'),

										'created_by'  => $this->session->userdata("uid")
									);

									$this->$model->insertData($insert_log_data, 'ret_taging_status_log');
								} else {

									$return_data = array('staus' => FALSE, 'message' => 'Unable to Proceed', 'last_query' => $this->db->last_query());
								}
							}
						}
					}
				}
			}





			if ($this->db->trans_status() === TRUE) {

				$this->db->trans_commit();

				$return_data = array('staus' => true, 'message' => 'File Updated Successfully', 'last_query' => '');
			} else {

				$this->db->trans_rollback();

				$return_data = array('staus' => FALSE, 'message' => 'Unable to Prceed', 'last_query' => '');
			}
		} else {

			$return_data = array('staus' => FALSE, 'message' => 'File Not Uploaded', 'last_query' => '');
		}

		echo json_encode($return_data);
	}



	function check_old_tag_code($old_tag_code)

	{

		$model = self::RET_REPORT_MODEL;

		$list = $this->$model->check_old_tag_code($old_tag_code);

		if ($list >= 1) {

			return '';
		} else {

			return $old_tag_code;
		}
	}



	function check_old_tag_code_mismatched($old_tag_code)

	{

		$model = self::RET_REPORT_MODEL;

		$list = $this->$model->check_old_tag_code_mismatched($old_tag_code);

		if ($list >= 1) {

			return 1;
		} else {

			return 2;
		}
	}

	function get_current_branch($old_tag_code)

	{

		$model = self::RET_REPORT_MODEL;

		$list = $this->$model->get_current_branch($old_tag_code);

		return $list;
	}

	function get_new_tag_current_branch($old_tag_code)

	{

		$model = self::RET_REPORT_MODEL;

		$list = $this->$model->get_new_tag_current_branch($old_tag_code);

		return $list;
	}

	function get_our_tag_code($old_tag_code)

	{

		$model = self::RET_REPORT_MODEL;

		$list = $this->$model->get_our_tag_code($old_tag_code);

		return $list;
	}

	function get_our_new_tag_code($old_tag_code)

	{

		$model = self::RET_REPORT_MODEL;

		$list = $this->$model->get_our_new_tag_code($old_tag_code);

		return $list;
	}

	function get_day_close_date($id_branch)

	{

		$model = self::RET_REPORT_MODEL;

		$list = $this->$model->get_day_close_date($id_branch);

		return $list;
	}



	//old tag import





	//Advance Details Report

	public function advance_total_details($type = '')

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'advance_details_report';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$data = $this->$model->customerAdvanceReport($_POST);

				echo json_encode($data);

				break;



			case 'mobileBySearch':

				$data = $this->$model->get_mobileNumber($_POST['mob_no']);

				echo json_encode($data);

				break;
		}
	}

	//Advance Details Report





	//Sales Return Report

	public function sales_return($type = '')

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'sales_return';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_sales_return($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/sales_return/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//Sales Return Report





	function dashboard_btList()

	{

		$from_date = $this->input->post('from_date');

		$to_date = $this->input->post('to_date');

		$type = $this->input->post('type');

		$id_branch = $this->input->post('id_branch');

		$id_product = $this->input->post('id_product');



		$model = self::RET_REPORT_MODEL;

		$data['bt_list'] = $this->$model->dashboard_btList($from_date, $to_date, $type, $id_branch, $id_product);

		echo json_encode($data);
	}



	function dashboard_branchtransfer()

	{

		$data['main_content'] = self::VIEW_FOLDER . '/dashboard/branch_transfer_list';

		$this->load->view('layout/template', $data);
	}





	public function tag_stone($type = '')

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'tag_stone';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_tagged_stone($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/tag_stone/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	public function active_tagcategory()

	{

		$model = self::RET_REPORT_MODEL;

		$data = $this->$model->get_tagcategory($_POST);

		echo json_encode($data);
	}





	function acc_stock_details($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'acc_stock_details';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_acc_stock_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/acc_stock_details/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function retag_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'retag_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_retag_report_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/retag_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}





	function purchase_itemwise($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'purchaseitem_wise';

				$this->load->view('layout/template', $data);

				break;



			case 'stn_details':

				$data = $this->$model->get_po_stone_details($_POST['po_item_id']);

				echo json_encode($data);

				break;

			case 'ajax':

				$list = $this->$model->get_PurchaseItemwise($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/purchase_itemwise/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function stock_rotation($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'stock_rotation';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_stock_rotation_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/purchase_itemwise/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}





	function sales_import($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'tally_import/sales_import';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_sales_import($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/sales_import/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}





	function purchase_import($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'tally_import/purchase_import';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_purchase_import_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/purchase_import/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}





	function payment_mode_import($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'tally_import/payment_mode_import';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_payment_mode_import($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/payment_mode_import/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}
	function bookstocks($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/bookstocks_report';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->get_categorywise_bookstocks_report($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/bookstocks/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}


	//ACCOUNTS REPORTS

	function gst_abstract($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/gst_abstract';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list['B2C'] = array();

				$list['B2B'] = array();

				$list['repair'] = array();

				$list['overseas'] = array();

				if ($_POST['report_type'] == 0) {

					$list['B2C'] = $this->$model->get_gst_abstract_details($_POST, '1'); //B2C Bills

					$list['B2B'] = $this->$model->get_gst_abstract_details($_POST, '2'); //B2B Bills

					$list['repair'] = $this->$model->getRepairCharges($_POST); //repair charges

					$list['overseas'] = $this->$model->get_gst_abstract_overseas_details($_POST); //Overseas Bills

					$list['sales_transfer'] = $this->$model->get_gst_abstract_details($_POST, '3');
				} else if ($_POST['report_type'] == 1) {

					$list['B2C'] = $this->$model->get_gst_abstract_details($_POST, '1'); //B2C Bills

				} else {

					$list['B2B'] = $this->$model->get_gst_abstract_details($_POST, '2'); //B2B Bills

				}



				$access = $this->admin_settings_model->get_access('admin_ret_reports/gst_abstract/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}





	function sales_return_abstract($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/sales_return_abstract';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_sals_return_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/sales_return_abstract/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function card_collection_report($type = "")

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/card_collection_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_card_collection_report($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/card_collection_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function cheque_collection_report($type = "")

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/cheque_collection_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_cheque_collection_report($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/cheque_collection_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}





	function netbanking_collection_report_15_02_2024($type = "")

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/netbanking_collection_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->getNetbankingCollectionReport($_POST['dt_range'], $_POST['id_branch'], $_POST['nb_type'], $_POST['nb_source_type'], $_POST['id_bank'], $_POST['id_device']);

				$data = array(

					'list' => $list

				);

				echo json_encode($data);

				break;
		}
	}


	function netbanking_collection_report($type = "")
	{
		$model = self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/netbanking_collection_report';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->getNetbankingCollectionReport($_POST['from_date'], $_POST['to_date'], $_POST['id_branch'], $_POST['nb_type'], $_POST['nb_source_type'], $_POST['id_bank'], $_POST['id_device'],$_POST['sch_code']);
				$data = array(
					'list' => $list
				);
				// echo "<pre>";print_r($data);exit;
				echo json_encode($data);
				break;
		}
	}





	function advance_receipt_report($type = "")

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/advance_receipt_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':



				$list = $this->$model->get_advance_receipt_report($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/advance_receipt_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function categorywise_bt_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/categorywise_bt_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_categorywise_bt_report($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/sales_discount_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	function categorywise_stock_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/categorywise_stock_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_categorywise_stock_report($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/categorywise_bt_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//ACCOUNTS REPORTS





	function item_sales_detail($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'detail_item-wise_sales';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->itemwise_sales_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/item_sales/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function get_ActiveDevicename()

	{

		$model = self::RET_REPORT_MODEL;

		$data = $this->$model->get_ActiveDevicename();

		echo json_encode($data);
	}


	function get_ActiveBankname()
	{
		$model = self::RET_REPORT_MODEL;
		$data = $this->$model->get_ActiveBankname();
		echo json_encode($data);
	}





	function pay_device($type = "")

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'pay_device';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_pay_device_bills($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/device_pay/list');

				$data = array(

					'list' => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}









	function weight_range_sales($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'weight_range_wise_sales';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_stock_rotation_sales_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/purchase_itemwise/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}







	//staff incentive report

	function staff_incentive_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'staff_incentive_report';

				$this->load->view('layout/template', $data);

				break;



			case 'detailed':

				$data['main_content'] = self::VIEW_FOLDER . 'staff_incentive_detailed_report';

				$this->load->view('layout/template', $data);

				break;



			case 'account_details':

				$data = $this->$model->get_employee_referred_acc_details($_POST);

				echo json_encode($data);

				break;



			case 'ajax':

				$list = $this->$model->get_staff_chit_incentive_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/staff_incentive_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//staff incentive report

	function karigar_metal_issue($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'karigar_metal_issue';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_karigar_metal_issue($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/karigar_metal_issue/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}





	function customer_detail($type = "")

	{

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'customer_detail';

				$this->load->view('layout/template', $data);

				break;
		}
	}

	function get_customer_details_report()

	{



		$model =	self::RET_REPORT_MODEL;

		$data = $this->$model->get_cus_details($_POST);

		echo json_encode($data);
	}







	function day_closing_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'day_closing_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->day_closing_report($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/sales_discount_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	function daytransactions($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/day_transaction_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$from_date = $this->input->post('from_date');

				$to_date = $this->input->post('to_date');

				$list = $this->$model->day_transactions_report($_POST);

				$paymodes = array();

				$pay_available      = 0;

				$receipt_available  = 0;

				$pay_cash   = 0;

				$pay_card   = 0;

				$pay_paytm  = 0;

				$pay_cheque = 0;

				$pay_nb     = 0;

				$pay_cashfree = 0;

				$total_payments = 0;



				$receipt_cash       = 0;

				$receipt_card       = 0;

				$receipt_paytm      = 0;

				$receipt_cheque     = 0;

				$receipt_nb         = 0;

				$receipt_cashfree   = 0;



				$total_receipts = 0;



				foreach ($list as $pkey => $pval) {

					if ($pval['reporttype'] == 1) { // For Receipts

						$receipt_available = 1;

						$receipt_cash   += $pval['total_cash'];

						$receipt_card   += $pval['total_card'];

						$receipt_paytm  += $pval['total_paytm'];

						$receipt_cheque += $pval['total_cheque'];

						$receipt_nb     += $pval['total_nb'];

						$receipt_cashfree     += $pval['total_cashfree'];



						$total_receipts += $pval['total_cash'] + $pval['total_card'] + $pval['total_paytm'] + $pval['total_cheque'] + $pval['total_nb'] + $pval['total_cashfree'];
					} else if ($pval['reporttype'] == 2) { // For Payments

						$pay_available = 1;

						$pay_cash   += $pval['total_cash'];

						$pay_card   += $pval['total_card'];

						$pay_paytm  += $pval['total_paytm'];

						$pay_cheque += $pval['total_cheque'];

						$pay_nb     += $pval['total_nb'];

						$pay_cashfree     += $pval['total_cashfree'];



						$total_payments += $pval['total_cash'] + $pval['total_card'] + $pval['total_paytm'] + $pval['total_cheque'] + $pval['total_nb'] + $pval['total_cashfree'];
					}
				}

				setlocale(LC_MONETARY, 'en_IN');

				$paymodes["receipts"] = array(

					"total_cash"        =>$this->moneyFormatIndia((float)$receipt_cash, 2, '.', ''),

					"total_card"        =>$this->moneyFormatIndia((float)$receipt_card, 2, '.', ''),

					"total_paytm"       =>$this->moneyFormatIndia((float)$receipt_paytm, 2, '.', ''),

					"total_cheque"      =>$this->moneyFormatIndia((float)$receipt_cheque, 2, '.', ''),

					"total_nb"          =>$this->moneyFormatIndia((float)$receipt_nb, 2, '.', ''),

					"total_cashfree"    =>$this->moneyFormatIndia((float)$receipt_cashfree, 2, '.', ''),

					"total_receipts"    =>$this->moneyFormatIndia((float)$total_receipts, 2, '.', ''),

				);



				$paymodes["payments"] = array(

					"total_cash"        =>$this->moneyFormatIndia((float)$pay_cash, 2, '.', ''),

					"total_card"        =>$this->moneyFormatIndia((float)$pay_card, 2, '.', ''),

					"total_paytm"       =>$this->moneyFormatIndia((float)$pay_paytm, 2, '.', ''),

					"total_cheque"      =>$this->moneyFormatIndia((float)$pay_cheque, 2, '.', ''),

					"total_nb"          =>$this->moneyFormatIndia((float)$pay_nb, 2, '.', ''),

					"total_cashfree"    =>$this->moneyFormatIndia((float)$pay_cashfree, 2, '.', ''),

					"total_payments"    =>$this->moneyFormatIndia((float)$total_payments, 2, '.', ''),

				);





				$access = $this->admin_settings_model->get_access('admin_ret_reports/daytransactions/list');

				$data = array(

					'list'      => $list,

					'access'    => $access,

					'paymodes'  => $paymodes

				);

				echo json_encode($data);

				break;
		}
	}



	function deposit_report($type)
	{



		$model =	self::RET_REPORT_MODEL;



		switch ($type) {

			case 'list':



				$data['main_content'] = self::VIEW_FOLDER . 'cash_deposit_report';



				$this->load->view('layout/template', $data);



				break;



			case 'ajax':



				$list = array();



				$depositList = array();



				$from_date = $this->input->post('from_date');



				$to_date = $this->input->post('to_date');



				$type = $this->input->post('type');



				$branch = $this->input->post('branch');



				$date_wise = $this->$model->getall_cashamt_by_deposit_date($from_date, $to_date, $branch);



				$deposits = $this->ret_catalog_model->get_deposit(0, "", "", "", "", $type, $branch);





				/*echo "<pre>";

				print_r($date_wise);

				print_r($deposits);

				echo "</pre>";

				exit;*/



				$closing = 0;



				$previous_deposited = 0;



				$final_closing_amt = 0;



				$dep_count = count($deposits);



				$i = 1;



				foreach ($deposits as $dep) {



					$dep_id = $dep['dep_id'];



					$billing_payment = 0;



					$iss_rcp_payment = 0;



					$chit_pay = 0;



					if ($type == 1) {



						foreach ($date_wise['billpay'] as $bp) {



							if ($bp['dep_id'] == $dep_id) {



								$billing_payment = $bp['cash_amt'];



								break;
							}
						}



						foreach ($date_wise['irpay'] as $ir) {



							if ($ir['dep_id'] == $dep_id) {



								$iss_rcp_payment = $ir['cash_amt'];



								break;
							}
						}
					} else if ($type == 2) {



						foreach ($date_wise['chit'] as $chit) {



							if ($chit['dep_id'] == $dep_id) {



								$chit_pay = $chit['cash_amt'];



								break;
							}
						}
					}



					$total_cash_pay = $billing_payment + $iss_rcp_payment + $chit_pay;



					$previous_deposited = $dep['dep_cur_balance'] - $dep['dep_amount'];



					$opening = $closing;



					$cash_inw = $total_cash_pay - $previous_deposited - $closing;



					$closing = $opening + $cash_inw - $dep['dep_amount'];



					$dep['opening']  = $this->moneyFormatIndia(number_format((float)($opening), 2, '.', ''));



					$dep['cash_inw'] = $this->moneyFormatIndia(number_format((float)($cash_inw), 2, '.', ''));



					$dep['closing']  = $this->moneyFormatIndia(number_format((float)($closing), 2, '.', ''));



					$dep['dep_amount']  = $this->moneyFormatIndia(number_format((float)($dep['dep_amount']), 2, '.', ''));



					$depositList[] = $dep;



					if ($i == $dep_count) {



						$final_closing_amt = $closing;
					}



					$i++;
				}



				$final_closing_amt = $this->moneyFormatIndia(number_format((float)($final_closing_amt), 2, '.', ''));



				$from_date = date("d-m-Y", strtotime($from_date));



				$to_date = date("d-m-Y", strtotime($to_date));



				$from_date = strtotime($from_date);



				$to_date = strtotime($to_date);



				foreach ($depositList as $depdata) {



					$depdate = date("d-m-Y", strtotime($depdata['dep_date']));



					$dep_date = strtotime($depdate);



					if ($dep_date >= $from_date && $dep_date <= $to_date) {



						$list[] = $depdata;
					}
				}



				$access = $this->admin_settings_model->get_access('admin_ret_reports/deposit_report/list');



				$data = array(

					'deposits'  => $list,

					'closing'	=> $final_closing_amt,

					'access'    => $access

				);



				echo json_encode($data);



				break;
		}
	}



	function moneyFormatIndia($num)
	{



		return preg_replace("/(\d+?)(?=(\d\d)+(\d)(?!\d))(\.\d+)?/i", "$1,", $num);
	}





	function supplier_contract_wise($type = '')
	{

		$model =	self::RET_REPORT_MODEL;



		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'supplier_contract_wise';

				$this->load->view('layout/template', $data);

				break;

			case 'stone_details':

				$data = $this->$model->get_supplier_contract_stone_details($_POST);

				echo json_encode($data);

				break;

			case 'ajax':



				$list = $this->$model->get_supplier_contract_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/supplier_contract_wise');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);



				break;
		}
	}





	function weight_rage_report($type = "")

	{



		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'weight_rage_report ';



				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_weight_rage_report($_POST);

				$data = array(

					'list' => $list

				);

				echo json_encode($data);

				break;
		}
	}



	function employee_wise_tag($type = "")

	{



		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'emp_wise_tag';



				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->get_employee_wise_tag($_POST);

				$data = array(

					'list' => $list

				);

				echo json_encode($data);

				break;
		}
	}





	function customer_analysis($type = "")

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'customer_analysis';

				$this->load->view('layout/template', $data);

				break;



			case 'update_vip_customer':



				$reqdata   = $this->input->post('req_data');

				//    print_r($reqdata);exit;

				$this->db->trans_begin();

				foreach ($reqdata as $cus) {

					$data = array('is_vip' => 1, 'date_upd' => date("Y-m-d H:i:s"));

					$status = $this->$model->updateData($data, 'id_customer', $cus['id_customer'], 'customer');
				}



				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					$response_data = array('status' => TRUE, 'message' => 'VIP Marked successfully.');
				} else {

					$this->db->trans_rollback();

					$response_data = array('status' => FALSE, 'message' => 'Unable to proceed Your Request.');
				}

				echo json_encode($response_data);

				break;



			case 'ajax':

				//$filters=$this->input->post('filter_condtions');

				$data = $this->$model->get_customer_analysis($_POST);

				echo json_encode($data);

				break;
		}
	}



	// SectionWise Stock In&Out details//



	function section_stock_inout($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'section_stock_details';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				// echo "<pre>";print_r($_POST);exit;
				//$list=$this->$model->get_section_stock_inout_details($_POST);
				$list = $this->$model->get_section_wise_stock_inout_details($_POST);

				$non_tag_items = $this->$model->get_nontag_section_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/stock_report/list');

				$data = array(

					'list'  => $list,

					'non_tag_items' => $non_tag_items,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	// SectionWise Stock In&Out details//








	// Stock Details Print (PDF) //
	function stock_details_print()
	{
		$model = self::RET_REPORT_MODEL;
		$view = self::VIEW_FOLDER . 'product_stock_print';
		$post = $_POST;

		$dt_range = isset($post['dt_range']) ? trim($post['dt_range']) : '';
		if ($dt_range == '') {
			echo json_encode(array("error" => "Date range is required"));
			return;
		}

		$parts = explode(' - ', $dt_range);
		if (count($parts) >= 2) {
			$from_str = trim($parts[0]);
			$to_str = trim($parts[1]);
		} else {
			$parts = preg_split('/\s*-\s*/', $dt_range, 2);
			$from_str = trim($parts[0]);
			$to_str = isset($parts[1]) ? trim($parts[1]) : $from_str;
		}

		$from_str = str_replace('-', '/', $from_str);
		$to_str = str_replace('-', '/', $to_str);
		$_POST['dt_range'] = $from_str . ' - ' . $to_str;
		$post['dt_range'] = $_POST['dt_range'];

		if (!isset($post['id_metal']) || !is_array($post['id_metal'])) $post['id_metal'] = [0];
		if (!isset($post['id_category']) || !is_array($post['id_category'])) $post['id_category'] = [0];
		if (!isset($post['id_section']) || !is_array($post['id_section'])) $post['id_section'] = [0];
		if (!isset($post['pro_id'])) $post['pro_id'] = '';
		if (!isset($post['des_id'])) $post['des_id'] = '';
		if (!isset($post['subdes_id'])) $post['subdes_id'] = '';
		if (!isset($post['GroupBy'])) $post['GroupBy'] = '1';

		try {
			$data['comp_details'] = $this->$model->getCompanyDetails('');
			$data['branch_details'] = $this->admin_settings_model->getCompanyDetails($post['id_branch']);

			$list = $this->$model->get_section_wise_stock_inout_details($post);
			$item_details = [];
			if (is_array($list)) {
				foreach ($list as $section_name => $section_items) {
					foreach ($section_items as $items) {
						foreach ($items as $product) {
							$op_pcs = floatval($product['op_blc_pcs']);
							$op_gwt = floatval($product['op_blc_gwt']);
							$closing_pcs = $op_pcs + floatval($product['inw_pcs']) - (floatval($product['sold_pcs']) + floatval($product['br_out_pcs']));
							$closing_gwt = $op_gwt + floatval($product['inw_gwt']) - (floatval($product['sold_gwt']) + floatval($product['br_out_gwt']));
							$product_label = isset($product['product_name']) ? $product['product_name'] : $product['section_name'];
							$item_details[] = [
								'section_name' => isset($product['section_name']) ? $product['section_name'] : $section_name,
								'product_name' => $product_label,
								'op_piece'     => $op_pcs,
								'op_gross_wt'  => $op_gwt,
								'cl_piece'     => $closing_pcs,
								'cl_gross_wt'  => $closing_gwt
							];
						}
					}
				}
			}
			$data['list'] = $item_details;

			$nontag_list = $this->$model->get_nontag_section_details($post);
			$non_tag_items = [];
			if (is_array($nontag_list)) {
				foreach ($nontag_list as $section_name => $level1) {
					foreach ($level1 as $level2) {                  // auto-index level
						foreach ($level2 as $product) {             // actual row
							$op_pcs = floatval($product['op_blc_pcs']);
							$op_gwt = floatval($product['op_blc_gwt']);
							$closing_pcs = $op_pcs + floatval($product['inw_pcs']) - (floatval($product['sales_pcs']) + floatval($product['br_out_pcs']));
							$closing_gwt = $op_gwt + floatval($product['inw_gwt']) - (floatval($product['sales_gwt']) + floatval($product['br_out_gwt']));
							$product_label = isset($product['product_name']) ? $product['product_name'] : $section_name;
							$non_tag_items[] = [
								'section_name' => isset($product['section_name']) ? $product['section_name'] : $section_name,
								'product_name' => $product_label,
								'op_piece'     => $op_pcs,
								'op_gross_wt'  => $op_gwt,
								'cl_piece'     => $closing_pcs,
								'cl_gross_wt'  => $closing_gwt
							];
						}
					}
				}
			}
			$data['non_tag_items'] = $non_tag_items;

			$this->load->helper(array('dompdf', 'file'));
			$dompdf = new Dompdf\Dompdf();
			$html = $this->load->view($view, $data, true);
			$dompdf->loadHtml($html);
			$dompdf->setPaper("a4", "portrait");
			$dompdf->render();

			$filename = time() . '.pdf';
			$output = $dompdf->output();
			file_put_contents('assets/stock_pdf/' . $filename, $output);

			$data = array("file_name" => $filename);
			echo json_encode($data);
		} catch (\Throwable $e) {
			echo json_encode(array("error" => $e->getMessage()));
		}
	}
	// Stock Details Print (PDF) //
	function tobe_history($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'tobe_history';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = $this->$model->getTobe_history($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/tobe_history/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	function tobe_pending($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'tobe_pending';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->get_tobe_pending_details($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/credit_history/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}



	function stock_reserve_order($type = "")
	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'reserve_order';

				$this->load->view('layout/template', $data);





				break;



			case 'ajax':

				$list = $this->$model->get_reserve_order($_POST);

				$data = array(

					'list' => $list

				);

				echo json_encode($data);

				break;
		}
	}



	function get_customer_reserveOrders()

	{

		$model = "ret_reports_model";

		$data = $this->$model->get_customer_reserveOrders($_POST);

		echo json_encode($data);
	}

	public function getCustomersBySearch()
	{

		$model = "ret_reports_model";

		$data = $this->$model->getAvailableCustomers($_POST['searchTxt']);

		echo json_encode($data);
	}





	function dashboard_contractprice($type = "")

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . '/dashboard/contract_price_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':



				$list = $this->$model->get_contract_pricing($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/dashboard_contractprice/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function lot_merge_report($type = "")

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'lot_merge_report';

				$this->load->view('layout/template', $data);

				break;

			case 'getlotMergeNos':

				$data = $this->$model->getlotMergeNos();

				echo json_encode($data);

				break;

			case 'ajax':

				$data = $this->$model->get_lot_merge_details($_POST);

				echo json_encode($data);

				break;
		}
	}



	function lot_split_report($type = "")

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'lot_split_report';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$data = $this->$model->get_lot_split_details($_POST);

				echo json_encode($data);

				break;
		}
	}



	public function advance_transfer_details($type = "")
	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'advance_transfer_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_advance_transfer($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/advance_transfer_details/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}





	function item_delivery_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'item_delivery_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_DeliveryListDetails($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/item_delivery_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function gstr1_sales($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/gstr1_sales_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_gstr1_sales_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/gstr1_sales/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}





	function gstr2_purchase($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/gstr2_purchase_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_gstr2_purchase_details($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/gstr2_purchase/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function active_category()

	{

		$model =	self::RET_REPORT_MODEL;

		$data = $this->$model->getActiveCategorymtr($_POST);

		echo json_encode($data);
	}

function active_sch_code()

	{

		$model =	self::RET_REPORT_MODEL;

		$data = $this->$model->active_sch_code();

		echo json_encode($data);
	}


	function old_metal_pl_report($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'old_metal_p&l';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_OldMetal_ProfitLoss_details($_POST);

				$metal_rates = $this->$model->get_metal_rates();

				$access = $this->admin_settings_model->get_access('admin_ret_reports/old_metal_purchase/list');

				$data = array(

					'list'  => $list,

					'metal_rate' => $metal_rates,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function section_transfer($type = '')

	{

		$model = "ret_reports_model";

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'section_transfer_report';

				$this->load->view('layout/template', $data);

				break;



			case 'ajax':

				$list = [];

				$non_tag_items = [];

				if ($_POST['sec_rpt_filter_by'] == 1) {
					$list = $this->$model->get_section_transfer_Details($_POST);
				} else if ($_POST['sec_rpt_filter_by'] == 2) {
					$non_tag_items = $this->$model->get_section_transfer_non_tag_Details($_POST);
				}

				$access = $this->admin_settings_model->get_access('admin_ret_reports/section_transfer/list');

				$data = array(

					'list'  => $list,

					'non_tag_list' => $non_tag_items,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}



	function get_po_ref_nos()

	{

		$model =	self::RET_REPORT_MODEL;

		$data = $this->$model->get_po_ref_nos();

		echo json_encode($data);
	}



	//Stock Issue Report

	function stock_issue_report($type = "")

	{

		$model =  self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'stock_issue_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_stock_issue_report($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/stock_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//Stock Issue Report


	function tagging_history($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {

			case 'list':
				$profile = $this->$model->get_profile_settings($this->session->userdata('profile'));
				$data['main_content'] = self::VIEW_FOLDER . 'tag_history_form';
				$data['profile'] = $profile;
				$this->load->view('layout/template', $data);
				break;

			case 'ajax':
				$list = $this->$model->get_tag_history($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/tagging_history/list');
				$data = array(
					'list'  => $list,
					'profile' => $profile,
					'access' => $access
				);

				echo json_encode($data);
				break;
		}
	}

	function get_img_by_tag_id()
	{
		$model = self::RET_REPORT_MODEL;
		$data = $this->$model->get_history_images($_POST['tag_id']);
		echo json_encode($data);
	}

	function gst_abstract_with_return($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'gst_abstract_with_return';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list['B2C'] = array();
				$list['B2B'] = array();
				$list['repair'] = array();
				$list['overseas'] = array();
				if ($_POST['report_type'] == 0) {
					$list['B2C'] = $this->$model->get_gst_abstract_with_return_details($_POST, '1'); //B2C Bills
					$list['B2B'] = $this->$model->get_gst_abstract_with_return_details($_POST, '2'); //B2B Bills
					//$list['repair']=$this->$model->getRepairCharges($_POST);//repair charges
					$list['overseas'] = $this->$model->get_gst_abstract_with_return_overseas_details($_POST); //Overseas Bills
					$list['sales_transfer'] = $this->$model->get_gst_abstract_with_return_details($_POST, '3');
				} else if ($_POST['report_type'] == 1) {
					$list['B2C'] = $this->$model->get_gst_abstract_with_return_details($_POST, '1'); //B2C Bills
				} else {
					$list['B2B'] = $this->$model->get_gst_abstract_with_return_details($_POST, '2'); //B2B Bills
				}

				$access = $this->admin_settings_model->get_access('admin_ret_reports/gst_abstract_with_return/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}
	/*Customer analysis Start here*/
	function customer_sales_analysis($type = "")
	{
		$model = self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'customer_sales_analysis';
				$this->load->view('layout/template', $data);
				break;

			case 'update_vip_customer':

				$reqdata   = $this->input->post('req_data');
				//  print_r($reqdata);exit;
				$this->db->trans_begin();
				foreach ($reqdata as $cus) {
					$data = array('is_vip' => 1, 'date_upd' => date("Y-m-d H:i:s"));
					$status = $this->$model->updateData($data, 'id_customer', $cus['id_customer'], 'customer');
				}

				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$response_data = array('status' => TRUE, 'message' => 'VIP Marked successfully.');
				} else {
					$this->db->trans_rollback();
					$response_data = array('status' => FALSE, 'message' => 'Unable to proceed Your Request.');
				}
				echo json_encode($response_data);
				break;

			case 'get_ActiveProductList':
				$data = $this->$model->get_ActiveProduct($_POST);
				echo json_encode($data);
				break;

			case 'ajax':
				$data = $this->$model->customer_sales_analysis($_POST);
				echo json_encode($data);
				break;
		}
	}


	/*Customer analysis end here*/

	function sales_transfer($type = "")
	{
		$model = self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'sales_transfer';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->getSalesTransReport($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/sales_transfer/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}


	function sales_transfer_report($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'sales_transfer_report';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->get_sales_transfer_details($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/sales_transfer_report/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}


	function karigar_wise_sales_detail($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'karigarwise_sale_detail';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				// print_r($_POST);exit;
				$data = $this->$model->get_karigar_wise_sales_detail($_POST);
				echo json_encode($data);
				break;
		}
	}

	function stock_age_analysis($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'stockage_analysis_dynamic';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->get_stock_age_analysis($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/stockage_analysis_dynamic/list');

				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}

	function irn_report($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'irn_report';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->irn_details($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/Irn_report/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}

	//Reorder reports

	function reorder_product()

	{
		$model =	self::RET_REPORT_MODEL;

		$data['comp_details'] = $this->admin_settings_model->get_company(1);

		$reorder_product_details = $this->$model->get_reorder_product($_POST['id_product']);

		// if ($id_branch == 0) {
		// 	$id_branch = "";
		// }
		if ($reorder_product_details > 0) {

			foreach ($reorder_product_details as $prod) {
				$stock_details = $this->$model->get_reorder_details(array('id_product' => $prod['pro_id'], 'id_branch' => $_POST['id_branch'], 'report_type' => $_POST['report_type'], 'item_type' => $prod['reorder_based_on'], 'available_stock_type' => $_POST['available_stock_types']));

				$prod['weight_details'] = $this->$model->get_weight_range_details($prod['pro_id']);

				$prod['size_details'] = $this->$model->get_product_size_details($prod['pro_id']);
				// print_r($this->db->last_query());exit;

				if (sizeof($stock_details) > 0) {
					$prod['stock_details'] =  $stock_details;

					$data['product_details'][] = $prod;

					$data['stock_details'] =  $stock_details;
				}

				// print_r($stock_details);exit;

			}
		} else {

			echo 0;
		}


		$data['report_type'] = $_POST['report_type'];

		$data['item_type'] = $_POST['item_type'];

		//echo "<pre>";print_r($data);exit;

		echo json_encode($data);
	}

	function reorder_print($id_product, $id_branch, $report_type, $item_type, $available_stock_type, $id_design, $id_sub_design, $id_section)
	{
		$model =	self::RET_REPORT_MODEL;

		$data['comp_details'] = $this->admin_settings_model->get_company(1);

		$reorder_product_details = $this->$model->get_reorder_product($id_product);

		//    print_r($reorder_product_details);exit;

		if ($id_branch == 0) {

			$id_branch = "";
		}
		if ($reorder_product_details > 0) {

			foreach ($reorder_product_details as $prod) {
				$stock_details = $this->$model->get_reorder_details(array(
					'id_product' => $prod['pro_id'], 'id_branch' => $id_branch, 'report_type' => $report_type, 'item_type' => $prod['reorder_based_on'], 'available_stock_type' => $available_stock_type,

					'id_design' => $id_design, 'id_sub_design' => $id_sub_design, 'id_section' => $id_section
				));

				$prod['weight_details'] = $this->$model->get_weight_range_details($prod['pro_id']);

				$prod['size_details'] = $this->$model->get_product_size_details($prod['pro_id']);
				// print_r($this->db->last_query());exit;

				if (sizeof($stock_details) > 0) {
					$prod['stock_details'] =  $stock_details;

					$data['product_details'][] = $prod;
				}

				// print_r($stock_details);exit;

			}
		} else {

			echo 0;
		}



		$data['report_type'] = $report_type;

		$data['item_type'] = $item_type;

		//echo "<pre>";print_r($data);exit;

		$html = $this->load->view('ret_reports/reorder_print', $data, true);

		echo $html;
		exit;
	}


	function reorder_report($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'reorder_report';

				$this->load->view('layout/template', $data);

				break;

			case 'print':

				$data['comp_details'] = $this->admin_settings_model->get_company(1);

				$data['stock_details'] = $this->$model->get_reorder_details(array('id_product' => $_POST['id_product'], 'report_type' => $_POST['report_type'], 'item_type' => $_POST['item_type']));

				$data['weight_details'] = $this->$model->get_weight_range_details($_POST['id_product']);

				$data['size_details'] = $this->$model->get_product_size_details($_POST['id_product']);

				// echo "<pre>";print_r($data);exit;
				$this->load->helper(array('dompdf', 'file'));

				$dompdf = new DOMPDF();

				$html = $this->load->view('ret_reports/reorder_print', $data, true);

				$dompdf->load_html($html);

				$dompdf->set_paper('A4', "portriat");

				$dompdf->render();

				while(ob_get_level()) ob_end_clean();
				$dompdf->stream("VendorAck.pdf", array('Attachment' => 0));
				break;


			case 'ajax':
				$list = $this->$model->get_reorder_details($_POST);
				$weight_details = $this->$model->get_weight_range_details($_POST['id_product']);
				$size_details = $this->$model->get_product_size_details($_POST['id_product']);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/reorder_report/list');
				$data = array(
					'list'  => $list,
					'access' => $access,
					'weight_details' => $weight_details,
					'size_details' => $size_details,
				);
				echo json_encode($data);
				break;
		}
	}

	//Reorder reports


	function inventory_turnover($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'inventory_turnover';
				$this->load->view('layout/template', $data);
				break;

			case 'ajax':
				$list = $this->$model->get_inventory_turnover_details($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/inventory_turnover/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}

	function stock_ratio_avail($type = "")
	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'stock_ratio_availability';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$data = $this->$model->get_stock_ratio_availability($_POST);

				echo json_encode($data);

				break;
		}
	}

	function get_product_size($id_product = "")
	{

		$model =	self::RET_REPORT_MODEL;

		$data = $this->$model->get_product_size($id_product);

		echo json_encode($data);
	}

	function repair_gst_abstract($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/repair_gst_abstract';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->get_repair_gst_abstract_details($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/repair_gst_abstract/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}

	function employee_sales_referal_and_sales_return($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'est_referal_and_sales_return';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				if (isset($_POST['select_view']) && $_POST['select_view'] == 2) {
					$this->load->model('ret_emp_incentive_model');
					
					// Parse dates from dt_range for the incentive model
					$date_from = '';
					$date_to = '';
					if (!empty($_POST['dt_range'])) {
						$dateRange = explode('-', $_POST['dt_range']);
						$date_from = date('Y-m-d', strtotime(str_replace('/', '-', trim($dateRange[0]))));
						$date_to = date('Y-m-d', strtotime(str_replace('/', '-', trim($dateRange[1]))));
					}
					
					$filters = $_POST;
					$filters['date_from'] = $date_from;
					$filters['date_to'] = $date_to;

					if ($_POST['id_report_type'] == 1) { // Detailed
						$raw_list = $this->ret_emp_incentive_model->get_incentive_report($filters);
						$list = array();
						foreach ($raw_list as $item) {
							$list[$item['emp_name']][] = $item;
						}
					} else { // Summary
						$raw_list = $this->ret_emp_incentive_model->get_incentive_summary($filters);
						$id_branch = isset($_POST['branch_select']) ? intval($_POST['branch_select']) : 
									(isset($_POST['branch_filter']) ? intval($_POST['branch_filter']) : 0);
						$list = array();
						foreach ($raw_list as $item) {
							// Get total already given from payment log
							$total_given = $this->ret_emp_incentive_model->get_total_given(
								$item['id_employee'], $date_from, $date_to, $id_branch
							);
							$item['total_given'] = $total_given;
							$item['balance'] = floatval($item['total_incentive']) - $total_given;
							$list[] = $item;
						}
					}
				} else {
					$list = $this->$model->get_est_details_and_sales_returns($_POST);
				}

				$access = $this->admin_settings_model->get_access('admin_ret_reports/est_referral/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}

	/**
	 * Save partial incentive payment
	 */
	function save_incentive_payment()
	{
		$this->load->model('ret_emp_incentive_model');
		$user_id = $this->session->userdata('uid');

		$data = array(
			'id_employee'     => intval($_POST['id_employee']),
			'id_branch'       => isset($_POST['id_branch']) ? intval($_POST['id_branch']) : 0,
			'date_from'       => $_POST['date_from'],
			'date_to'         => $_POST['date_to'],
			'original_amount' => floatval($_POST['original_amount']),
			'given_amount'    => floatval($_POST['given_amount']),
			'remarks'         => isset($_POST['remarks']) ? $_POST['remarks'] : ''
		);

		$id = $this->ret_emp_incentive_model->save_payment_log($data, $user_id);

		echo json_encode(array(
			'status' => $id > 0 ? 'success' : 'error',
			'id'     => $id
		));
	}

	/**
	 * Get incentive payment log for a specific employee
	 */
	function get_incentive_payment_log()
	{
		$this->load->model('ret_emp_incentive_model');

		$id_employee = intval($_POST['id_employee']);
		$date_from   = $_POST['date_from'];
		$date_to     = $_POST['date_to'];
		$id_branch   = isset($_POST['id_branch']) ? intval($_POST['id_branch']) : 0;

		$logs = $this->ret_emp_incentive_model->get_payment_logs($id_employee, $date_from, $date_to, $id_branch);
		$total_given = $this->ret_emp_incentive_model->get_total_given($id_employee, $date_from, $date_to, $id_branch);

		echo json_encode(array(
			'logs'        => $logs,
			'total_given' => $total_given
		));
	}

	/**
	 * Delete a payment log entry (soft delete)
	 */
	function delete_incentive_payment()
	{
		$this->load->model('ret_emp_incentive_model');
		$id = intval($_POST['id']);
		$result = $this->ret_emp_incentive_model->delete_payment_log($id);

		echo json_encode(array(
			'status' => $result ? 'success' : 'error'
		));
	}


	function branch_vault_report($type = "")
	{
		$model = self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'branch_vault_report';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->branch_vault_report($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/branch_vault_report/list');
				$data = array(
					'list' => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}

	function detailed_day_transaction_report($type = "")
	{
		
		$model = self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'detailed_day_transaction_report';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->detailed_day_transaction_report($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/detailed_day_transaction_report/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}



	//Receipts ledger statement view for Tally
	function receipts_ledger_statement($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'receipts_ledger_statement';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				//$list = $this->$model->getReceiptsAllLedger($_POST);
				if ($_POST['reqledger_type'] == 1) {
					$list = $this->$model->getReceiptsAllLedger($_POST);
				} else {
					$list = $this->$model->getAllPaymentLedger($_POST);
				}
				$access = $this->admin_settings_model->get_access('admin_ret_reports/receipts_ledger_statement/list');
				//print_r($list);exit;
				$data = array(
					'list'          => $list,
					'access'        => $access
				);
				echo json_encode($data);
				break;
		}
	}
	//Receipts ledger statement


	function cash_book($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'cash_book';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':

				$list = $this->$model->get_cash_book_details($_POST);
				//echo "<pre>"; print_r($list);exit;
				$access = $this->admin_settings_model->get_access('admin_ret_reports/cash_book/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);

				echo json_encode($data);
				break;
		}
	}

	function categorywise_day_transaction($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'categorywise_day_transaction';
				$this->load->view('layout/template', $data);
				break;

			case 'ajax':
				$list = $this->$model->get_categorywise_day_transaction($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/section_transfer/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}

	function repair_order($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'repair_order_report';
				$this->load->view('layout/template', $data);
				break;

			case 'ajax':
				$list = $this->$model->get_repair_order_details($_POST);
				$access = $this->admin_settings_model->get_access('admin_ret_reports/repair_order/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}

	function ho_daily_stock_book($type)
	{
		$model =	self::RET_REPORT_MODEL;

		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'ho_daily_stock_book';
				$this->load->view('layout/template', $data);
				break;

			case 'ajax':
				if ($_POST['dt_range'] != '') {
					$dateRange = explode('-', $_POST['dt_range']);
					$from = str_replace('/', '-', $dateRange[0]);
					$to = str_replace('/', '-', $dateRange[1]);
					$d1 = date_create($from);
					$d2 = date_create($to);
					$_POST['from_date'] = date_format($d1, "Y-m-d");
					$_POST['to_date'] = date_format($d2, "Y-m-d");
				}

				$data = array(
					"from_date" => $_POST['from_date'],
					"to_date" => $_POST['to_date'],
					"id_metal" => $_POST['id_metal'],
					"id_branch" => ''
				);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/ho_daily_stock_book/list');

				$hovault = $this->ret_purchase_order_model->get_headoffice_valut_report($_POST);
				$btvault = $this->ret_purchase_order_model->get_retagging_details($data);
				$tag_items = $this->$model->get_section_wise_stock_inout_details($_POST);
				$taginward = $this->$model->get_SectionTag_InwardOutward_Details($_POST, 1); // 1 - > Inward
				$tagoutward = $this->$model->get_SectionTag_InwardOutward_Details($_POST, 2); // 2 -> Outward
				$non_tag_items = $this->$model->get_nontag_section_details($_POST);
				$nontaginward = $this->$model->get_SectionNonTag_InwardOutward_Details($_POST, 1); // 1 -> Inward
				$nontagoutward = $this->$model->get_SectionNonTag_InwardOutward_Details($_POST, 2); // 2 -> Outward
				$profitloss = $this->$model->getLotwiseTaggedVault($_POST);
				$purchasereturn = $this->$model->get_PurchaseReturnItems($_POST);
				$metalissue = $this->$model->get_MetalIssueItems($_POST);


				/*Head office Vault Opening , Inward & Closing*/
				$ho_vault_opening_grswt = 0;
				$ho_vault_opening_nwt = 0;
				$ho_vault_opening_diawt = 0;
				$ho_vault_opening_grmwt = 0;
				$ho_vault_opening_ctwt = 0;

				$ho_vault_inward_grswt = 0;
				$ho_vault_inward_nwt = 0;
				$ho_vault_inward_diawt = 0;
				$ho_vault_inward_grmwt = 0;
				$ho_vault_inward_ctwt = 0;

				$ho_vault_closing_grswt = 0;
				$ho_vault_closing_nwt = 0;
				$ho_vault_closing_diawt = 0;
				$ho_vault_closing_grm_wt = 0;
				$ho_vault_closing_ctwt = 0;

				foreach ($hovault as $val) {
					$ho_vault_opening_grswt += $val['blc_gwt'];
					$ho_vault_opening_nwt += $val['blc_nwt'];
					$ho_vault_opening_diawt += $val['blc_diawt'];

					$ho_vault_inward_grswt += $val['inw_gwt'];
					$ho_vault_inward_nwt += $val['inw_nwt'];
					$ho_vault_inward_diawt += $val['inw_diawt'];

					$ho_vault_closing_grswt += $val['closing_gwt'];
					$ho_vault_closing_nwt += $val['closing_nwt'];
					$ho_vault_closing_diawt += $val['closing_diawt'];
				}
				/*Head office Vault Opening & Inward*/



				/*BT vault (Retag) Opening , Inward & Closing*/
				$bt_vault_opening_grswt = 0;
				$bt_vault_opening_nwt = 0;
				$bt_vault_opening_diawt = 0;
				$bt_vault_opening_grmwt = 0;
				$bt_vault_opening_ctwt = 0;

				$bt_vault_inward_grswt = 0;
				$bt_vault_inward_nwt = 0;
				$bt_vault_inward_diawt = 0;
				$bt_vault_inward_grmwt = 0;
				$bt_vault_inward_ctwt = 0;

				$bt_vault_pocket_grswt = 0;
				$bt_vault_pocket_nwt = 0;
				$bt_vault_pocket_diawt = 0;
				$bt_vault_pocket_grmwt = 0;
				$bt_vault_pocket_ctwt = 0;


				$bt_vault_closing_grswt = 0;
				$bt_vault_closing_nwt = 0;
				$bt_vault_closing_diawt = 0;
				$bt_vault_closing_grm_wt = 0;
				$bt_vault_closing_ctwt = 0;


				foreach ($btvault as $val) {
					$bt_vault_opening_grswt += $val['op_blc_gwt'];
					$bt_vault_opening_nwt += $val['op_blc_nwt'];
					$bt_vault_opening_diawt += $val['op_blc_diawt'];

					$bt_vault_inward_grswt += $val['inw_gwt'];
					$bt_vault_inward_nwt += $val['inw_nwt'];
					$bt_vault_inward_diawt += $val['inw_diawt'];

					$bt_vault_closing_grswt += $val['closing_gwt'];
					$bt_vault_closing_nwt += $val['closing_nwt'];
					$bt_vault_closing_diawt += $val['closing_diawt'];

					$bt_vault_pocket_grswt += $val['pkt_gwt'];
					$bt_vault_pocket_nwt += $val['pkt_nwt'];
					$bt_vault_pocket_diawt += $val['pkt_diawt'];
				}
				/*BT vault (Retag) Opening & Inward*/


				/*section wise (Tag) */
				$sect_tag_opening_grswt = 0;
				$sect_tag_opening_nwt = 0;
				$sect_tag_opening_diawt = 0;
				$sect_tag_opening_grmwt = 0;
				$sect_tag_opening_ctwt = 0;

				$sect_tag_inward_grswt = 0;
				$sect_tag_inward_nwt = 0;
				$sect_tag_inward_diawt = 0;
				$sect_tag_inward_grmwt = 0;
				$sect_tag_inward_ctwt = 0;

				$sect_tag_outward_grswt = 0;
				$sect_tag_outward_nwt = 0;
				$sect_tag_outward_diawt = 0;
				$sect_tag_outward_grmwt = 0;
				$sect_tag_outward_ctwt = 0;


				$sect_tag_closing_grswt = 0;
				$sect_tag_closing_nwt = 0;
				$sect_tag_closing_diawt = 0;
				$sect_tag_closing_grmwt = 0;
				$sect_tag_closing_ctwt = 0;

				foreach ($tag_items as $sect) {
					foreach ($sect as $prod) {
						foreach ($prod as $des) {
							foreach ($des as $val) {
								$sect_tag_opening_grswt += $val['op_blc_gwt'];
								$sect_tag_opening_nwt += $val['op_blc_nwt'];
								$sect_tag_opening_diawt += $val['op_blc_diawt'];
								$sect_tag_opening_grmwt += $val['op_grm_blc_wt'];
								$sect_tag_opening_ctwt += $val['op_ct_blc_wt'];

								$sect_tag_closing_grswt += ($val['op_blc_gwt'] + $val['inw_gwt'] - $val['sold_gwt'] - $val['br_out_gwt'] - $val['sect_out_gwt'] - $val['ret_gwt'] - $val['iss_gwt']);
								$sect_tag_closing_nwt += ($val['op_blc_nwt'] + $val['inw_nwt'] - $val['sold_nwt'] - $val['br_out_nwt'] - $val['sect_out_nwt'] - $val['ret_nwt'] - $val['iss_nwt']);
								$sect_tag_closing_diawt += ($val['op_blc_diawt'] + $val['inw_diawt'] - $val['sold_diawt'] - $val['br_out_diawt'] - $val['sect_out_diawt'] - $val['ret_diawt'] - $val['iss_diawt']);
								$sect_tag_closing_grmwt += ($val['op_grm_blc_wt'] + $val['grm_inw_wt'] - $val['sold_grm_wt'] - $val['grm_br_out_wt'] - $val['grm_sect_out_wt'] - $val['grm_ret_wt'] - $val['grm_iss_wt']);
								$sect_tag_closing_ctwt += ($val['op_ct_blc_wt'] + $val['ct_inw_wt'] - $val['sold_ct_wt'] - $val['ct_br_out_wt'] - $val['ct_sect_out_wt'] - $val['ct_ret_wt'] - $val['ct_iss_wt']);
							}
						}
					}
				}

				foreach ($taginward as $val) {
					if ($val['stone_type'] == 0) // For Ornaments Product
					{
						$sect_tag_inward_grswt += $val['gross_wt'];
						$sect_tag_inward_nwt += $val['net_wt'];
						$sect_tag_inward_diawt += $val['diawt'];
					} else if ($val['stone_type'] == 1 && $val['uom'] != 6) // For Stone (Grm) product
					{
						$sect_tag_inward_grmwt += $val['gross_wt'];
					} else if ($val['stone_type'] == 1 && $val['uom'] == 6) // For Stone (Carat) product
					{
						$sect_tag_inward_ctwt += $val['gross_wt'];
					} else // For Diamond product
					{
						$sect_tag_inward_diawt += $val['diawt'];
					}
				}


				foreach ($tagoutward as $val) {
					if ($val['stone_type'] == 0) // For Ornaments Product
					{
						$sect_tag_outward_grswt += $val['gross_wt'];
						$sect_tag_outward_nwt += $val['net_wt'];
						$sect_tag_outward_diawt += $val['diawt'];
					} else if ($val['stone_type'] == 1 && $val['uom'] != 6) // For Stone (Grm) product
					{
						$sect_tag_outward_grmwt += $val['gross_wt'];
					} else if ($val['stone_type'] == 1 && $val['uom'] == 6) // For Stone (Carat) product
					{
						$sect_tag_outward_ctwt += $val['gross_wt'];
					} else // For Diamond product
					{
						$sect_tag_outward_diawt += $val['gross_wt'];
					}
				}
				/*section wise report (Tag)*/


				/*Section Wise (NonTag)*/
				$sect_nontag_opening_grswt = 0;
				$sect_nontag_opening_nwt = 0;

				$sect_nontag_inward_grswt = $nontaginward['grswt'];
				$sect_nontag_inward_nwt = $nontaginward['net_wt'];

				$sect_nontag_outward_grswt = $nontagoutward['grswt'];
				$sect_nontag_outward_nwt = $nontagoutward['net_wt'];

				$sect_nontag_closing_grswt = 0;
				$sect_nontag_closing_nwt = 0;


				foreach ($non_tag_items as $sect) {
					foreach ($sect as $prod) {
						foreach ($prod as $des) {
							foreach ($des as $val) {
								$sect_nontag_opening_grswt += $val['op_blc_gwt'];
								$sect_nontag_opening_nwt += $val['op_blc_nwt'];

								$sect_nontag_closing_grswt += ($val['op_blc_gwt'] + $val['inw_gwt'] - $val['sales_gwt'] - $val['br_out_gwt'] - $val['pur_ret_gwt'] - $val['kar_iss_gwt']);
								$sect_nontag_closing_nwt += ($val['op_blc_nwt'] + $val['inw_nwt'] - $val['sales_nwt'] - $val['br_out_nwt'] - $val['kar_iss_nwt']);
							}
						}
					}
				}
				/*Section Wise (NonTag)*/


				/*Purchase return Items*/
				$purret_grswt = 0;
				$purret_nwt = 0;
				$purret_diawt = 0;
				$purret_grmwt = 0;
				$purret_ctwt = 0;

				foreach ($purchasereturn as $val) {

					$purret_grswt += $val['ret_gwt'];
					$purret_nwt += $val['ret_nwt'];
					$purret_diawt += $val['ret_diawt'];
				}
				/*Purchase return Items*/

				/*Karigar Metal Issue*/
				$metissue_grswt = 0;
				$metissue_nwt = 0;
				$metissue_diawt = 0;
				$metissue_grmwt = 0;
				$metissue_ctwt = 0;

				foreach ($metalissue as $val) {
					if ($val['stone_type'] != 2) {
						$metissue_grswt += $val['issue_wt'];
						$metissue_nwt += $val['issue_wt'];
					} else {
						$metissue_diawt += $val['issue_wt'];
					}
				}
				/*Karigar Metal Issue*/

				/*Profit & Loss*/
				$profit_grswt = 0;
				$profit_nwt = 0;
				$profit_diawt = 0;
				$profit_grmwt = 0;
				$profit_ctwt = 0;

				$loss_grswt = 0;
				$loss_nwt = 0;
				$loss_diawt = 0;
				$loss_grmwt = 0;
				$loss_ctwt = 0;

				foreach ($profitloss as $val) {

					$bal_grswt = ($val['lotgrswt'] - $val['taggrswt'] - $val['recgrswt'] - $val['lmgrswt']);
					$bal_nwt = ($val['lotnetwt'] - $val['tagnetwt'] - $val['recnetwt'] - $val['lmnetwt']);
					$bal_diawt = ($val['lotdiawt'] - $val['tagdiawt'] - $val['recdiawt'] - $val['lmdiawt']);

					$profit_grswt -= ($bal_grswt < 0 ? $bal_grswt : 0);
					$profit_nwt -= ($bal_nwt < 0 ? $bal_nwt : 0);
					$profit_diawt -= ($bal_diawt < 0 ? $bal_diawt : 0);

					$loss_grswt += ($bal_grswt > 0 ? $bal_grswt : 0);
					$loss_nwt += ($bal_nwt > 0 ? $bal_nwt : 0);
					$loss_diawt += ($bal_diawt > 0 ? $bal_diawt : 0);
				}
				/*Profit & Loss*/

				$ho_stock_details = array(
					'hovaultopengrswt'    => number_format($ho_vault_opening_grswt, 3, '.', ''),
					'hovaultopennwt'      => number_format($ho_vault_opening_nwt, 3, '.', ''),
					'hovaultopendiawt'    => number_format($ho_vault_opening_diawt, 3, '.', ''),
					'hovaultopengrmwt'    => number_format(0, 3, '.', ''),
					'hovaultopenctwt'     => number_format(0, 3, '.', ''),
					'hovaultinwgrswt'     => number_format($ho_vault_inward_grswt, 3, '.', ''),
					'hovaultinwnwt'       => number_format($ho_vault_inward_nwt, 3, '.', ''),
					'hovaultinwdiawt'     => number_format($ho_vault_inward_diawt, 3, '.', ''),
					'hovaultinwgrmwt'     => number_format(0, 3, '.', ''),
					'hovaultinwctwt'      => number_format(0, 3, '.', ''),

					'btvaultopengrswt'    => number_format($bt_vault_opening_grswt, 3, '.', ''),
					'btvaultopennwt'      => number_format($bt_vault_opening_nwt, 3, '.', ''),
					'btvaultopendiawt'    => number_format($bt_vault_opening_diawt, 3, '.', ''),
					'btvaultopengrmwt'    => number_format(0, 3, '.', ''),
					'btvaultopenctwt'     => number_format(0, 3, '.', ''),
					'btvaultinwgrswt'     => number_format($bt_vault_inward_grswt, 3, '.', ''),
					'btvaultinwnwt'       => number_format($bt_vault_inward_nwt, 3, '.', ''),
					'btvaultinwdiawt'     => number_format($bt_vault_inward_diawt, 3, '.', ''),
					'btvaultinwgrmwt'     => number_format(0, 3, '.', ''),
					'btvaultinwctwt'      => number_format(0, 3, '.', ''),

					'sectopengrswt'       => number_format($sect_tag_opening_grswt + $sect_nontag_opening_grswt, 3, '.', ''),
					'sectopennwt'         => number_format($sect_tag_opening_nwt + $sect_nontag_opening_nwt, 3, '.', ''),
					'sectopendiawt'       => number_format($sect_tag_opening_diawt, 3, '.', ''),
					'sectopengrmwt'       => number_format($sect_tag_opening_grmwt, 3, '.', ''),
					'sectopenctwt'        => number_format($sect_tag_opening_ctwt, 3, '.', ''),

					'sectinwgrswt'        => number_format($sect_tag_inward_grswt + $sect_nontag_inward_grswt, 3, '.', ''),
					'sectinwnwt'          => number_format($sect_tag_inward_nwt + $sect_nontag_inward_nwt, 3, '.', ''),
					'sectinwdiawt'        => number_format($sect_tag_inward_diawt, 3, '.', ''),
					'sectinwgrmwt'        => number_format($sect_tag_inward_grmwt, 3, '.', ''),
					'sectinwctwt'         => number_format($sect_tag_inward_ctwt, 3, '.', ''),

					'sectoutgrswt'        => number_format($sect_tag_outward_grswt + $sect_nontag_outward_grswt, 3, '.', ''),
					'sectoutnwt'          => number_format($sect_tag_outward_nwt + $sect_nontag_outward_grswt, 3, '.', ''),
					'sectoutdiawt'        => number_format($sect_tag_outward_diawt, 3, '.', ''),
					'sectoutgrmwt'        => number_format($sect_tag_outward_grmwt, 3, '.', ''),
					'sectoutctwt'         => number_format($sect_tag_outward_ctwt, 3, '.', ''),

					'purretgrswt'         => number_format($purret_grswt, 3, '.', ''),
					'purretnwt'           => number_format($purret_nwt, 3, '.', ''),
					'purretdiawt'         => number_format($purret_diawt, 3, '.', ''),
					'purretgrmwt'         => number_format($purret_grmwt, 3, '.', ''),
					'purretctwt'          => number_format($purret_ctwt, 3, '.', ''),

					'issuegrswt'          => number_format($metissue_grswt, 3, '.', ''),
					'issuenwt'            => number_format($metissue_nwt, 3, '.', ''),
					'issuediawt'          => number_format($metissue_diawt, 3, '.', ''),
					'issuegrmwt'          => number_format($metissue_grmwt, 3, '.', ''),
					'issuectwt'           => number_format($metissue_ctwt, 3, '.', ''),

					'pocketgrswt'          => number_format($bt_vault_pocket_grswt, 3, '.', ''),
					'pocketnwt'            => number_format($bt_vault_pocket_nwt, 3, '.', ''),
					'pocketdiawt'          => number_format($bt_vault_pocket_diawt, 3, '.', ''),
					'pocketgrmwt'          => number_format($bt_vault_pocket_grmwt, 3, '.', ''),
					'pocketctwt'           => number_format($bt_vault_pocket_ctwt, 3, '.', ''),

					'profitgrswt'         => number_format($profit_grswt, 3, '.', ''),
					'profitnwt'           => number_format($profit_nwt, 3, '.', ''),
					'profitdiawt'         => number_format($profit_diawt, 3, '.', ''),
					'profitgrmwt'         => number_format($profit_grmwt, 3, '.', ''),
					'profitctwt'          => number_format($profit_ctwt, 3, '.', ''),

					'lossgrswt'           => number_format($loss_grswt, 3, '.', ''),
					'lossnwt'             => number_format($loss_nwt, 3, '.', ''),
					'lossdiawt'           => number_format($loss_diawt, 3, '.', ''),
					'lossgrmwt'           => number_format($loss_grmwt, 3, '.', ''),
					'lossctwt'            => number_format($loss_ctwt, 3, '.', ''),

					'hovaultclosingrswt'  => number_format($ho_vault_closing_grswt, 3, '.', ''),
					'hovaultclosingnwt'   => number_format($ho_vault_closing_nwt, 3, '.', ''),
					'hovaultclosingdiawt' => number_format($ho_vault_closing_diawt, 3, '.', ''),
					'hovaultclosinggrmwt' => number_format(0, 3, '.', ''),
					'hovaultclosingctwt'  => number_format(0, 3, '.', ''),

					'btvaultclosingrswt'  => number_format($bt_vault_closing_grswt, 3, '.', ''),
					'btvaultclosingnwt'   => number_format($bt_vault_closing_nwt, 3, '.', ''),
					'btvaultclosingdiawt' => number_format($bt_vault_closing_diawt, 3, '.', ''),
					'btvaultclosinggrmwt' => number_format(0, 3, '.', ''),
					'btvaultclosingctwt'  => number_format(0, 3, '.', ''),

					'sectionclosinggrswt' => number_format($sect_tag_closing_grswt + $sect_nontag_closing_grswt, 3, '.', ''),
					'sectionclosingnwt'   => number_format($sect_tag_closing_nwt + $sect_nontag_closing_nwt, 3, '.', ''),
					'sectionclosingdiawt' => number_format($sect_tag_closing_diawt, 3, '.', ''),
					'sectionclosinggrmwt' => number_format($sect_tag_closing_grmwt, 3, '.', ''),
					'sectionclosingctwt'  => number_format($sect_tag_closing_ctwt, 3, '.', ''),

				);

				$data = array(
					'list'          => $list,
					'stock_details' => $ho_stock_details,
					'access'        => $access
				);
				echo json_encode($data);
				break;
		}
	}
	// Duplicate tag print log
function duplicate_tag_print_log($type="")
{

	$model = self::RET_REPORT_MODEL;

	switch ($type) {

		case 'list':

			$data['main_content'] = self::VIEW_FOLDER . 'duplicate_tag_print_log';

			$this->load->view('layout/template', $data);

			break;

		case 'ajax':

			$data = $_POST;

			$log = $this->$model->duplicate_tag_print_log_list($data);

			echo json_encode($log);

			break;
	}
}

	// function cash_book_detail($type = "")
	// {
	// 	$model =	self::RET_REPORT_MODEL;
	// 	switch ($type) {
	// 		case 'list':
	// 			$data['main_content'] = self::VIEW_FOLDER . 'cash_book_detail';
	// 			$this->load->view('layout/template', $data);
	// 			break;
	// 		case 'ajax':
	// 			// echo "<pre>"; print_r($_POST); exit;
	// 			$list = $this->$model->get_cash_book_detailed($_POST);
	// 			// echo "<pre>"; print_r($list);exit;
				
	// 			$data = array(
	// 				'list'  => $list
					
	// 			);

	// 			echo json_encode($data);
	// 			break;
	// 	}
	// }

	function cash_book_detail($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'cash_book_detail';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				// echo "<pre>"; print_r($_POST); exit;
				$list = $this->$model->get_cash_book_detailed($_POST);
				// echo "<pre>"; print_r($list);exit;
				
				$data = array(
					'list'  => $list
					
				);

				echo json_encode($data);
				break;
		}
	}
	function stock_details_v2($type = "")

	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$profile = $this->$model->get_profile_settings($this->session->userdata('profile'));

				$data['main_content'] = self::VIEW_FOLDER . 'stock_details_v2';

				$data['profile'] = $profile;

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':


				//    /* if($_POST['group_by']==1 || $_POST['group_by']==2)
				//     {*/
				//         $list=$this->$model->get_stock_details($_POST);
				//     /*}else{
				//         $list=$this->$model->get_stock_weight_range($_POST);
				//     }*/


				// 	$non_tag_items=$this->$model->get_nontag_stock_details($_POST);

				if ($_POST['list_by'] == 0) {

					$list = $this->$model->get_stock_details_v2($_POST);

					$non_tag_items = $this->$model->get_nontag_stock_details_v2($_POST);

					// print_r($this->db->last_query());exit;

				} elseif ($_POST['list_by'] == 1) {

					$list = $this->$model->get_stock_details_v2($_POST);
					$non_tag_items = [];
				} elseif ($_POST['list_by'] == 2) {
					$list = [];
					$non_tag_items = $this->$model->get_nontag_stock_details_v2($_POST);
				}

				$profile = $this->$model->get_profile_settings($this->session->userdata('profile'));

				$access = $this->admin_settings_model->get_access('admin_ret_reports/stock_report/list');


				$other_ow = $this->$model->other_ow($_POST);

				$showroom_sales = $this->$model->showroom_sales($_POST);

				$branch_outward = $this->$model->branch_outward($_POST);

				$issued_stock = $this->$model->issued_stock($_POST);



				$other_pcs	= 0;

				$other_gwt	= 0;

				$openpcs 	= 0;

				$openwt 	= 0;

				$inwardpcs 	= 0;

				$inwardwt 	= 0;

				$outwardpcs = 0;

				$outwardwt 	= 0;

				$soldpcs	= 0;

				$soldwt		= 0;

				$closepcs 	= 0;

				$closewt 	= 0;

				$showroomsalepcs = 0;

				$showroomsalegwt = 0;

				$issuedpcs = 0;

				$issuedwt = 0;



				$lotinw_pcs 	= 0;

				$lotinw_gwt 	= 0;



				$brainw_gwt 	= 0;

				$brainw_pcs 	= 0;





				foreach ($list as $key => $value) {



					foreach ($value as $id => $val) {



						foreach ($val as $skey => $sval) {



							$openpcs += $sval['op_blc_pcs'];

							$openwt  += $sval['op_blc_gwt'];



							/*$inwardpcs += $sval['inw_pcs'];

								$inwardwt  += $sval['inw_gwt'];*/



							$lotinw_pcs += $sval['lotinw_pcs'];

							$lotinw_gwt  += $sval['lotinw_gwt'];



							$brainw_pcs += $sval['brainw_pcs'];

							$brainw_gwt  += $sval['brainw_gwt'];



							$soldpcs	+= $sval['sold_pcs'];

							$soldwt		+= $sval['sold_gwt'];



							$closepcs += ($sval['op_blc_pcs'] + $sval['inw_pcs'] - $sval['sold_pcs'] - $sval['br_out_pcs']);

							$closewt  += ($sval['op_blc_gwt'] + $sval['inw_gwt'] - $sval['sold_gwt'] - $sval['br_out_gwt']);
						}
					}
				}



				$other_pcs = $other_ow['partly_sold']['tot_pcs'];

				$other_gwt = $other_ow['partly_sold']['tot_gwt'];



				foreach ($branch_outward as $key => $val) {

					$outwardpcs += $val['tot_pcs'];

					$outwardwt += $val['tot_gwt'];
				}



				foreach ($issued_stock as $key => $val) {

					$issuedpcs += $val['tot_pcs'];

					$issuedwt += $val['tot_gwt'];
				}





				$stocksummery = array(
					"openpcs" => $openpcs,

					"openwt" =>  number_format($openwt, 2, '.', ''),

					"otherpcs" => $other_pcs,

					"othergwt" => number_format($other_gwt, 2, '.', ''),

					"showroomsalepcs" => $showroom_sales['piece'],

					"showroomsalegwt" => number_format($showroom_sales['gwt'], 2, '.', ''),

					"inwardpcs" => $inwardpcs,

					"inwardwt" => number_format($inwardwt, 2, '.', ''),

					"brainw_pcs" => $brainw_pcs,

					"brainw_gwt" => number_format($brainw_gwt, 2, '.', ''),

					"lotinw_pcs" => $lotinw_pcs,

					"lotinw_gwt" => number_format($lotinw_gwt, 2, '.', ''),

					"outwardpcs" => $outwardpcs, "outwardwt" => number_format($outwardwt, 2, '.', ''),

					"issued_pcs" => $issuedpcs,

					"issued_wt" => number_format($issuedwt, 2, '.', ''),

					"soldpcs" => $soldpcs, "soldwt" => number_format($soldwt, 2, '.', ''),

					"closepcs" => $closepcs,

					"closewt" => number_format($closewt, 2, '.', '')
				);







				$data = array(

					'list'  => $list,

					'profile' => $profile,

					'non_tag_items' => $non_tag_items,

					'stocksummery' => $stocksummery,

					'access' => $access

				);



				echo json_encode($data);

				break;
		}
	}

	public function get_account_head_list(){

	    $model=self::RET_REPORT_MODEL;

		$list=$this->$model->get_account_head_list();

		// echo"<pre>";print_r($list);exit;

		echo json_encode($list);

	}

	function petty_cash_report($type = "")

	{

		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				// echo "<h1>Hi</h1>"exit;

				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/petty_cash_report';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$account_head = $_POST['account_head'];

				$from_date = $_POST['from_date'];

				$to_date = $_POST['to_date'];

				$id_branch = $_POST['id_branch'];

				// print_r($_POST);exit;

				$list = $this->$model->get_petty_cash_report($account_head,$from_date,$to_date,$id_branch);

				// echo"<pre>";print_r($list);exit;

				$access = $this->admin_settings_model->get_access('admin_ret_reports/petty_cash_report/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	//Day InOut CashBook Report

	function day_inout_cashbook($type = "")
	{
		$model =	self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'cash_book';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->get_day_inout_cashbook_details($_POST);
				// echo "<pre>"; print_r($list);exit;
				$access = $this->admin_settings_model->get_access('admin_ret_reports/day_inout_cashbook/list');
				$data = array(
					'list'  => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
		}
	}
	
	//Day InOut CashBook Report	

	function gst_r1($type="") {

		$model=	self::RET_REPORT_MODEL;

		$access = $this->admin_settings_model->get_access('admin_ret_reports/gst_r1/list');

		switch($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER.'accounts_reports/gst_r1_report';
				$this->load->view('layout/template', $data);

			break;

			case 'b2b':

				$list=$this->$model->get_gst_r1_b2b($_POST);

				$data = array(
					'list'  => $list,
					'access'=> $access
				);

				echo json_encode($data);

			break;

			case 'b2cs_others':

				$list=$this->$model->get_gst_r1_b2c($_POST);

				$data = array(
					'list'  => $list,
					'access'=> $access
				);

				echo json_encode($data);

			break;

			case 'b2cl':

				$list=$this->$model->get_gst_r1_b2cl($_POST);

				$data = array(
					'list'  => $list,
					'access'=> $access
				);

				echo json_encode($data);

			break;

			case 'b2b_return':

				$list=$this->$model->get_gst_r1_return($_POST, 'b2b');

				$data = array(
					'list'  => $list,
					'access'=> $access
				);

				echo json_encode($data);

			break;

			case 'b2c_return':

				$list=$this->$model->get_gst_r1_return($_POST, 'b2c');

				$data = array(
					'list'  => $list,
					'access'=> $access
				);

				echo json_encode($data);

			break;

			case 'hsn_summary':

				$list=$this->$model->get_gst_r1_hsn_summary($_POST);

				$data = array(
					'list'  => $list,
					'access'=> $access
				);

				echo json_encode($data);

			break;

			case 'export':

				$_POST['report'] = 2;				

				$list = $this->$model->get_gst_abstract_overseas_details($_POST);

				if(count($list) > 0) {

					$list = array_shift($list);

				}

				/*echo "<pre>";
				print_r($list);
				exit;*/

				$data = array(
					'list'  => $list,
					'access'=> $access
				);

				echo json_encode($data);

			break;
			
			case 'doc_issued_smry':

				// print_r($_POST);exit;

				$list=$this->$model->get_gst_r1_doc_issued_smry($_POST);

				$data = array(
					'list'  => $list,
					'access'=> $access
				);

				echo json_encode($data);

			break;
		}

	}

	function sales_purchase($type="")
	{   
		$model=	self::RET_REPORT_MODEL;
		switch($type)
		{
			case 'list': 
					$data['main_content'] = self::VIEW_FOLDER.'sales_purchase';
        			$this->load->view('layout/template', $data);
				break;
			case 'village':
				$data=$this->$model->get_allVillage(); 
				echo json_encode($data);
			break;
			case 'customer':
				$data=$this->$model->get_allCustomer($_POST); 
				echo json_encode($data);
			break;
			case 'ajax': 
			        $id_branch=$this->input->post('id_branch');
			        
				
					$list=$this->$model->getGroupWiseBilling($_POST); 
					
				
				  	$access = $this->admin_settings_model->get_access('admin_ret_reports/sales_purchase/list');
			        $data = array(
		        					'list'  => $list,
									'access'=> $access
		        				 );  
					echo json_encode($data);
			break;

		}
    }


	function gst_abstract_v1($type="")

	{   

		$model=	self::RET_REPORT_MODEL;

		switch($type)

		{

			case 'list': 

					$data['main_content'] = self::VIEW_FOLDER.'accounts_reports/gst_abstract_v1';

        			$this->load->view('layout/template', $data);

				break;

			case 'ajax': 

			        $list['unregistered_sales']=array();

			        $list['registered_sales_bill_wise']=array();

					$list['unregistered_purchase']=array();

					$list['registered_purchase_bill_wise']=array();

					$list['unregistered_return']=array();

					$list['registered_return_bill_wise']=array();

					// print_r($_POST);exit;

			        if($_POST['report_type']==0)

			        {
			            $list['unregistered_sales']=$this->$model->get_gst_abstract_details_v1($_POST,'1'); //Unregistered Sales Bills

						$list['registered_sales_bill_wise']=$this->$model->get_gst_abstract_details_v1($_POST,'2'); //Registered Sales Bills

						$list['unregistered_purchase']=$this->$model->get_gst_abstract_details_v1($_POST,'3'); //Unregistered Purchase Bills

						$list['registered_purchase_bill_wise']=$this->$model->get_gst_abstract_details_v1($_POST,'4'); //Registered Purchase Bills

						$list['unregistered_return']=$this->$model->get_gst_abstract_details_v1($_POST,'5'); //Unregistered Return Bills

						$list['registered_return_bill_wise']=$this->$model->get_gst_abstract_details_v1($_POST,'6'); //Registered Return Bills

						$list['unregistered_sales_bill_wise'] = [];

						$list['unregistered_purchase_bill_wise'] = [];

						$list['unregistered_return_bill_wise'] = [];

						$list['registered_sales_cat_wise'] = [];

						$list['registered_purchase_cat_wise'] = [];

						$list['registered_return_cat_wise'] = [];



			        }else if($_POST['report_type']==1)

			        {

			            $list['unregistered_sales']=$this->$model->get_gst_abstract_details_v1($_POST,'1'); //Unregistered Bills

			            $list['unregistered_sales_bill_wise']=$this->$model->get_gst_abstract_details_v1($_POST,'7'); //Unregistered bill wise Bills

						$list['unregistered_purchase']=$this->$model->get_gst_abstract_details_v1($_POST,'3'); //Unregistered Purchase Bills

						$list['unregistered_purchase_bill_wise']=$this->$model->get_gst_abstract_details_v1($_POST,'9'); //Unregistered bill wise Purchase Bills

						$list['unregistered_return']=$this->$model->get_gst_abstract_details_v1($_POST,'5'); //Unregistered Return Bills

						$list['unregistered_return_bill_wise']=$this->$model->get_gst_abstract_details_v1($_POST,'11'); //Unregistered bill wise Return Bills
						
						$list['registered_sales_cat_wise'] = [];

						$list['registered_purchase_cat_wise'] = [];

						$list['registered_return_cat_wise'] = [];


			        }else{

			            $list['registered_sales_bill_wise']=$this->$model->get_gst_abstract_details_v1($_POST,'2'); //Registered Bills
	
			            $list['registered_sales_cat_wise']=$this->$model->get_gst_abstract_details_v1($_POST,'8'); //Registered cat_wise Bills

						$list['registered_purchase_bill_wise']=$this->$model->get_gst_abstract_details_v1($_POST,'4'); //Registered  Purchase Bills

						$list['registered_purchase_cat_wise']=$this->$model->get_gst_abstract_details_v1($_POST,'10'); //Registered  cat_wise Purchase Bills

						$list['registered_return_bill_wise']=$this->$model->get_gst_abstract_details_v1($_POST,'6'); //Registered Return Bills

						$list['registered_return_cat_wise']=$this->$model->get_gst_abstract_details_v1($_POST,'12'); //Registered  cat_wise Return Bills

						$list['unregistered_sales_bill_wise'] = [];

						$list['unregistered_purchase_bill_wise'] = [];

						$list['unregistered_return_bill_wise'] = [];


			        }

					

				  	$access = $this->admin_settings_model->get_access('admin_ret_reports/gst_abstract_v1/list');

			        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				 );  

					echo json_encode($data);

				break;

		}
	}

	function gst_abstract_bills($type="")

	{   

		$model=	self::RET_REPORT_MODEL;

		switch($type)

		{

			case 'list': 

					$data['main_content'] = self::VIEW_FOLDER.'accounts_reports/gst_abstract_bills';

        			$this->load->view('layout/template', $data);

				break;

			case 'ajax': 

			        // $list['unregistered_sales']=array();

			        // $list['registered_sales_bill_wise']=array();

					// $list['unregistered_purchase']=array();

					// $list['registered_purchase_bill_wise']=array();

					// $list['unregistered_return']=array();

					// $list['registered_return_bill_wise']=array();

			        // if($_POST['report_type']==0)

			        // {
			        //     $list['unregistered_sales']=$this->$model->get_gst_abstract_details($_POST,'1'); //Unregistered Sales Bills

					// 	$list['registered_sales_bill_wise']=$this->$model->get_gst_abstract_details($_POST,'2'); //Registered Sales Bills

					// 	$list['unregistered_purchase']=$this->$model->get_gst_abstract_details($_POST,'3'); //Unregistered Purchase Bills

					// 	$list['registered_purchase_bill_wise']=$this->$model->get_gst_abstract_details($_POST,'4'); //Registered Purchase Bills

					// 	$list['unregistered_return']=$this->$model->get_gst_abstract_details($_POST,'5'); //Unregistered Return Bills

					// 	$list['registered_return_bill_wise']=$this->$model->get_gst_abstract_details($_POST,'6'); //Registered Return Bills

			        // }else if($_POST['report_type']==1)

			        // {

			        //     $list['unregistered_sales']=$this->$model->get_gst_abstract_details($_POST,'1'); //Unregistered Bills

					// 	$list['unregistered_purchase']=$this->$model->get_gst_abstract_details($_POST,'3'); //Unregistered Purchase Bills

					// 	$list['unregistered_return']=$this->$model->get_gst_abstract_details($_POST,'5'); //Unregistered Return Bills

			        // }else{

			        //     $list['registered_sales_bill_wise']=$this->$model->get_gst_abstract_details($_POST,'2'); //Registered Bills

					// 	$list['registered_purchase_bill_wise']=$this->$model->get_gst_abstract_details($_POST,'4'); //Registered Purchase Bills

					// 	$list['registered_return_bill_wise']=$this->$model->get_gst_abstract_details($_POST,'6'); //Registered Return Bills

			        // }

					// echo '<pre>'; print_r($_POST); exit;

					$list['sales']=array();
			        $list['purchase']=array();
			        $list['sales_return']=array();

					$list['sales']=$this->$model->get_gst_abstract_details_bills($_POST,'11'); //Unregistered Bills
					$list['purchase']=$this->$model->get_gst_abstract_details_bills($_POST,'12'); //Unregistered Bills
					$list['sales_return']=$this->$model->get_gst_abstract_details_bills($_POST,'13'); //Unregistered Bills


					if($_POST['report_type']==0){
						$data_list = $list;
				   }else if($_POST['report_type']==1){

					$data_list['sales']['unregisteredAbove1Lakh'] = $list['sales']['unregisteredAbove1Lakh'];
					$data_list['sales']['unregisteredBelow1Lakh'] = $list['sales']['unregisteredBelow1Lakh'];
					$data_list['sales_return']['unregisteredAbove1Lakh'] = $list['sales_return']['unregisteredAbove1Lakh'];
					$data_list['sales_return']['unregisteredBelow1Lakh'] = $list['sales_return']['unregisteredBelow1Lakh'];
					$data_list['purchase']['unregistered_purchase'] = $list['purchase']['unregistered_purchase'];
					

				   }else{
					
					$data_list['sales']['registered'] = $list['sales']['registered'];
					$data_list['purchase']['registered'] = $list['purchase']['registered'];
					$data_list['sales_return']['registered'] = $list['sales_return']['registered'];
					
				   }

				   	

					

				  	$access = $this->admin_settings_model->get_access('admin_ret_reports/gst_abstract_bills/list');

			        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				 );  

					echo json_encode($data);

				break;

		}
	}

	function get_ActiveLedger()
	{
		$model = self::RET_REPORT_MODEL;
		$data = $this->$model->get_ActiveLedger();
		echo json_encode($data);
	}

	function ledger_report($type = "")
	{
		$model = self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/bank_ledger_report';
				$this->load->view('layout/template', $data);
				break;
			case 'ajax':
				$list = $this->$model->getLedgerReportData($_POST);
				$data = array(
					'list' => $list
				);
				echo json_encode($data);
				break;
		}
	}

	function supplier_unified_transaction($type = "")
	{

		$model =	self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':

				$data['main_content'] = self::VIEW_FOLDER . 'supplier_unified_transaction';

				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$metal_wise_data = array();

				$list = $this->$model->getSmithCombinedLedger($_POST);

				// $list2 = $this->$model->getSupplierapprovalTransactionList($_POST);

				// $list = array_merge($list1,$list2);

				$access = $this->admin_settings_model->get_access('admin_ret_reports/supplier_approval_transaction/list');

				if (!empty($_POST['metal_wise_required'])) {
					$metal_wise_data  = $this->$model->getMetalwiseApprovalTransactionList($_POST);
				}

				$data = array(

					'list'          => $list,

					'access'        => $access,

					'metalwisedata' => $metal_wise_data

				);

				echo json_encode($data);

				break;
		}
	}
	// ====== NAVRATNA V3 GST ABSTRACT ======
	function gst_abstract_v3($type = "")
	{
		$model = self::RET_REPORT_MODEL;

		switch ($type) {

			case 'list':
				$data['main_content'] = self::VIEW_FOLDER . 'accounts_reports/gst_abstract_v3';
				$this->load->view('layout/template', $data);
				break;

			case 'ajax':
				$list['B2C'] = array();
				$list['B2B'] = array();
				$list['repair'] = array();
				$list['overseas'] = array();

				$report_type = isset($_POST['report_type']) ? intval($_POST['report_type']) : 0;

				if ($report_type == 0) {
					$list['B2C'] = $this->$model->get_gst_abstract_v3_details($_POST, '1'); //B2C Bills
					$list['B2B'] = $this->$model->get_gst_abstract_v3_details($_POST, '2'); //B2B Bills
					$list['repair'] = $this->$model->getRepairCharges($_POST); //repair charges
					$list['overseas'] = $this->$model->get_gst_abstract_overseas_details($_POST); //Overseas Bills
					$list['sales_transfer'] = $this->$model->get_gst_abstract_v3_details($_POST, '3');
				} else if ($report_type == 1) {
					$list['B2C'] = $this->$model->get_gst_abstract_v3_details($_POST, '1'); //B2C Bills
				} else {
					$list['B2B'] = $this->$model->get_gst_abstract_v3_details($_POST, '2'); //B2B Bills
				}

				$access = $this->admin_settings_model->get_access('admin_ret_reports/gst_abstract_v3/list');

				$data = array(
					'list'   => $list,
					'access' => $access
				);

				echo json_encode($data);
				break;
		}
	}
	// ====== END NAVRATNA V3 GST ABSTRACT ======
	

	function get_metal_list()
	{
		$model = self::RET_REPORT_MODEL;
		$metals = $this->$model->get_metals();
		$html = '<option value="0">All Metal</option>';
		foreach ($metals as $m) {
			$html .= '<option value="' . $m['id_metal'] . '">' . $m['metal_name'] . '</option>';
		}
		echo $html;
	}

	function get_employee_list()
	{
		$model = self::RET_REPORT_MODEL;
		$employees = $this->$model->get_employees();
		$html = '<option value="0">All Employee</option>';
		foreach ($employees as $e) {
			$html .= '<option value="' . $e['id_employee'] . '">' . $e['firstname'] . '</option>';
		}
		echo $html;
	}

	function employee_product_report($type = "")
	{
		$model = self::RET_REPORT_MODEL;

		switch ($type) {


			case 'list':

				// Pass stock_audit_by setting to view
				try {
					$data['stock_audit_by'] = $this->$model->get_ret_settings('stock_audit_by');
				} catch (Exception $e) {
					$data['stock_audit_by'] = 1;
				}
				if (empty($data['stock_audit_by'])) $data['stock_audit_by'] = 1;

				// Pass print_date/print_branch URL params to view (from Physical Stock Inspect page)
				$data['print_date'] = $this->input->get('print_date') ? $this->input->get('print_date') : '';
				$data['print_branch'] = $this->input->get('print_branch') ? $this->input->get('print_branch') : '';

				$data['main_content'] = self::VIEW_FOLDER . 'employee_product_stock_report';

				$this->load->view('layout/template', $data);

				break;

			case 'section_ajax':
				// Stock report: product-wise or section-wise based on stock_audit_by setting
				$stock_audit_by = isset($_POST['stock_audit_by']) ? $_POST['stock_audit_by'] : '1';
				if ($stock_audit_by == '1') {
					// Product-wise: flat list grouped by product only
					$section_data = $this->$model->get_product_only_stock_report($_POST);
				} else {
					// Section-wise: grouped by section then product
					$section_data = $this->$model->get_section_product_stock_report($_POST);
				}
				$access = $this->admin_settings_model->get_access('admin_ret_reports/stock_report/list');
				$data = array(
					'section_list' => $section_data,
					'access' => $access
				);
				echo json_encode($data);
				break;

			case 'entry_log_ajax':
				$log_data = $this->$model->get_employee_entry_log($_POST);
				echo json_encode(array('entry_log' => $log_data));
				break;

			case 'ajax':


				if ($_POST['list_by'] == 0 || $_POST['list_by'] == 4) {

					$list = $this->$model->get_product_stock_details($_POST);

					$non_tag_items = $this->$model->get_product_nontag_stock_details($_POST);
				} elseif ($_POST['list_by'] == 1) {

					$list = $this->$model->get_product_stock_details($_POST);

					$non_tag_items = [];
				} elseif ($_POST['list_by'] == 2) {
					$list = [];

					$non_tag_items = $this->$model->get_product_nontag_stock_details($_POST);
				}

				$access = $this->admin_settings_model->get_access('admin_ret_reports/stock_report/list');

				$other_ow = $this->$model->other_ow($_POST);

				$showroom_sales = $this->$model->showroom_sales($_POST);

				$branch_outward = $this->$model->branch_outward($_POST);

				$issued_stock = $this->$model->issued_stock($_POST);

				$other_pcs	= 0;

				$other_gwt	= 0;

				$openpcs 	= 0;

				$openwt 	= 0;

				$inwardpcs 	= 0;

				$inwardwt 	= 0;

				$outwardpcs = 0;

				$outwardwt 	= 0;

				$soldpcs	= 0;

				$soldwt		= 0;

				$closepcs 	= 0;

				$closewt 	= 0;

				$showroomsalepcs = 0;

				$showroomsalegwt = 0;

				$issuedpcs = 0;

				$issuedwt = 0;

				$lotinw_pcs 	= 0;

				$lotinw_gwt 	= 0;

				$brainw_gwt 	= 0;

				$brainw_pcs 	= 0;

				foreach ($list as $key => $value) {


					foreach ($value as $id => $val) {



						foreach ($val as $skey => $sval) {


							$openpcs += $sval['op_blc_pcs'];

							$openwt  += $sval['op_blc_gwt'];

							$lotinw_pcs += $sval['lotinw_pcs'];

							$lotinw_gwt  += $sval['lotinw_gwt'];



							$brainw_pcs += $sval['brainw_pcs'];

							$brainw_gwt  += $sval['brainw_gwt'];



							$soldpcs	+= $sval['sold_pcs'];

							$soldwt		+= $sval['sold_gwt'];



							$closepcs += ($sval['op_blc_pcs'] + $sval['inw_pcs'] - $sval['sold_pcs'] - $sval['br_out_pcs']);

							$closewt  += ($sval['op_blc_gwt'] + $sval['inw_gwt'] - $sval['sold_gwt'] - $sval['br_out_gwt']);
						}
					}
				}



				$other_pcs = $other_ow['partly_sold']['tot_pcs'];

				$other_gwt = $other_ow['partly_sold']['tot_gwt'];



				foreach ($branch_outward as $key => $val) {

					$outwardpcs += $val['tot_pcs'];

					$outwardwt += $val['tot_gwt'];
				}



				foreach ($issued_stock as $key => $val) {

					$issuedpcs += $val['tot_pcs'];

					$issuedwt += $val['tot_gwt'];
				}




				$stocksummery = array(
					"openpcs" => $openpcs,

					"openwt" =>  number_format($openwt, 2, '.', ''),

					"otherpcs" => $other_pcs,

					"othergwt" => number_format($other_gwt, 2, '.', ''),

					"showroomsalepcs" => $showroom_sales['piece'],

					"showroomsalegwt" => number_format($showroom_sales['gwt'], 2, '.', ''),

					"inwardpcs" => $inwardpcs,

					"inwardwt" => number_format($inwardwt, 2, '.', ''),

					"brainw_pcs" => $brainw_pcs,

					"brainw_gwt" => number_format($brainw_gwt, 2, '.', ''),

					"lotinw_pcs" => $lotinw_pcs,

					"lotinw_gwt" => number_format($lotinw_gwt, 2, '.', ''),

					"outwardpcs" => $outwardpcs,
					"outwardwt" => number_format($outwardwt, 2, '.', ''),

					"issued_pcs" => $issuedpcs,

					"issued_wt" => number_format($issuedwt, 2, '.', ''),

					"soldpcs" => $soldpcs,
					"soldwt" => number_format($soldwt, 2, '.', ''),

					"closepcs" => $closepcs,

					"closewt" => number_format($closewt, 2, '.', '')
				);




				$data = array(

					'list'  => $list,

					'non_tag_items' => $non_tag_items,

					'stocksummery' => $stocksummery,

					'access' => $access

				);



				echo json_encode($data);

				break;
		}
	}

	function update_stock_product()
	{

		$model = self::RET_REPORT_MODEL;

		$product = $this->input->post();

		$data = $this->$model->update_product_status($product);

		if ($data) {
			echo json_encode(array('status' => 'success', 'message' => 'Update successful.'));
		} else {
			echo json_encode(array('status' => 'error', 'message' => 'Update failed.'));
		}
	}

	function physical_stock_inspect_report($type = "")
	{
		$model = self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				try {
					$data['stock_audit_by'] = $this->$model->get_ret_settings('stock_audit_by');
				} catch (Exception $e) {
					$data['stock_audit_by'] = 1;
				}
				if (empty($data['stock_audit_by'])) $data['stock_audit_by'] = 1;
				$data['main_content'] = self::VIEW_FOLDER . 'physical_stock_inspect_report';
				$this->load->view('layout/template', $data);
				break;

			case 'ajax':
				$list = $this->$model->get_stock_audit_daily_status($_POST);
				echo json_encode(array('list' => $list));
				break;

			case 'complete_audit':
				$audit_date = $this->input->post('audit_date');
				$id_branch = $this->input->post('id_branch');
				$completed_by = $this->session->userdata('uid');
				if (empty($audit_date) || empty($id_branch)) {
					echo json_encode(array('status' => 'error', 'message' => 'Missing date or branch.'));
					break;
				}
				$result = $this->$model->mark_stock_audit_complete($audit_date, $id_branch, $completed_by);
				if ($result) {
					echo json_encode(array('status' => 'success', 'message' => 'Stock audit marked as Completed.'));
				} else {
					echo json_encode(array('status' => 'error', 'message' => 'Failed to update status.'));
				}
				break;

			case 'check_status':
				$audit_date = $this->input->post('audit_date');
				$id_branch = $this->input->post('id_branch');
				if (empty($audit_date) || empty($id_branch)) {
					echo json_encode(array('status' => 'no_entry'));
					break;
				}
				$status_data = $this->$model->get_stock_audit_status_for_date($audit_date, $id_branch);
				echo json_encode($status_data);
				break;

		case 'print':
			// Server-side print: fetch data and render standalone print view
			$print_date = $this->input->get('date');
			$id_branch = $this->input->get('id_branch');
			$auto_print = $this->input->get('auto_print');

			// Determine stock_audit_by setting
			try {
				$stock_audit_by = $this->$model->get_ret_settings('stock_audit_by');
			} catch (Exception $e) {
				$stock_audit_by = 1;
			}
			if (empty($stock_audit_by)) $stock_audit_by = 1;

			// Build POST-like data array for the model (reuse existing model methods)
			$post_data = array(
				'dt_range' => $print_date, // single date like "23/04/2026"
				'id_branch' => $id_branch,
				'stock_audit_by' => $stock_audit_by,
				'list_by' => '0', // ALL
				'id_metal' => '',
				'id_employee' => ''
			);

			// Fetch data using same model methods as employee product report
			if ($stock_audit_by == '1') {
				$section_list = $this->$model->get_product_only_stock_report($post_data);
			} else {
				$section_list = $this->$model->get_section_product_stock_report($post_data);
			}

			// Get company name
			$comp = $this->admin_settings_model->get_company(1);
			$data['company_name'] = isset($comp['company_name']) ? $comp['company_name'] : '';

			// Get branch name
			$branch_row = $this->db->select('name')->where('id_branch', intval($id_branch))->get('branch')->row_array();
			$data['branch_name'] = isset($branch_row['name']) ? $branch_row['name'] : '';

			// Get logged-in employee name for "Printed By"
			$uid = $this->session->userdata('uid');
			$emp_row = $this->db->select('firstname')->where('id_employee', intval($uid))->get('employee')->row_array();
			$data['printed_by'] = isset($emp_row['firstname']) ? $emp_row['firstname'] : '';

			$data['section_list'] = $section_list;
			$data['report_type'] = $stock_audit_by;
			$data['report_date'] = $print_date;
			$data['auto_print'] = $auto_print;

			$this->load->view('ret_reports/print/employee_stock_inspect_print', $data);
			break;
		}
	}


	/**
	 * AJAX endpoint: returns company/branch header data for DataTable print
	 */
	public function get_print_header()
	{
		$id_branch = $this->input->post('id_branch');
		$this->load->model('ret_catalog_model');
		$company = $this->ret_catalog_model->getCompanyDetails($id_branch);

		$address_line = '';
		$location_line = '';
		$gst = '';
		$company_name = '';
		$branch_name = '';

		if (!empty($company)) {
			$company_name = isset($company['company_name']) ? $company['company_name'] : '';
			$branch_name  = isset($company['name']) ? $company['name'] : (isset($company['branch_name']) ? $company['branch_name'] : '');
			$gst          = isset($company['gst_number']) ? $company['gst_number'] : '';
			$parts = array();
			if (!empty($company['address1'])) $parts[] = $company['address1'];
			if (!empty($company['address2'])) $parts[] = $company['address2'];
			$address_line = implode(', ', $parts);
			$loc = array();
			if (!empty($company['city'])) $loc[] = $company['city'];
			if (!empty($company['state'])) $loc[] = $company['state'];
			if (!empty($company['pincode'])) $loc[] = $company['pincode'];
			$location_line = implode(', ', $loc);
		}

		// Printed by
		$uid = $this->session->userdata('uid');
		$printed_by = '';
		if ($uid) {
			$emp_row = $this->db->select('firstname')->where('id_employee', intval($uid))->get('employee')->row_array();
			$printed_by = isset($emp_row['firstname']) ? $emp_row['firstname'] : '';
		}

		echo json_encode(array(
			'company_name' => $company_name,
			'address_line' => $address_line,
			'location_line' => $location_line,
			'gst_number' => $gst,
			'branch_name' => $branch_name,
			'printed_by' => $printed_by
		));
	}

	public function customer_feedbacks($type = "")
	{
		$model    = self::RET_REPORT_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = "ret_reports/feedback";
				$data['feedback_header'] = $this->$model->get_feedback_active_header();
				$data['branch_list'] = $this->db->query("SELECT id_branch, name FROM branch WHERE active = 1 ORDER BY name")->result_array();
				$data['login_branch'] = $this->session->userdata('id_branch');
				$this->load->view('layout/template', $data);
				break;

			case 'ajax':
				$list = $this->$model->get_feedback_list($_POST);
				$header = $this->$model->get_feedback_active_header();

				echo json_encode([
					'headers' => $header,
					'data'    => $list
				]);
				break;
		}
	}

}