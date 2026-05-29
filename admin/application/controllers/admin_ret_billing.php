<?php

if (! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/dompdf/autoload.inc.php');

use Dompdf\Dompdf;

class Admin_ret_billing extends CI_Controller

{
	const VIEW_FOLDER = 'ret_purchase/';

	const IMG_PATH  = 'assets/img/';

	const CUS_IMG_PATH = 'assets/img/customer';

	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model('ret_billing_model');

		$this->load->model('ret_purchase_order_model');

		$this->load->model('admin_settings_model');

		$this->load->model("sms_model");

		$this->load->model("admin_usersms_model");

		$this->load->model("log_model");

		$this->load->model("payment_model");

		$this->load->model("account_model");

		$this->load->model('ret_order_model');

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

	public function index() {}

	public function base64ToFile($imgBase64)
	{

		$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgBase64)); // might not work on some systems, specify your temp path if system temp dir is not writeable

		$temp_file_path = tempnam(sys_get_temp_dir(), 'tempimg');

		file_put_contents($temp_file_path, $data);

		$image_info = getimagesize($temp_file_path);

		$imgFile = array(

			'name' => uniqid() . '.' . preg_replace('!\w+/!', '', $image_info['mime']),

			'tmp_name' => $temp_file_path,

			'size'  => filesize($temp_file_path),

			'error' => UPLOAD_ERR_OK,

			'type'  => $image_info['mime'],

		);

		return $imgFile;
	}

	function set_image($id, $img_path, $file, $path)

	{

		if (!is_dir($path)) {

			mkdir($path, 0777, TRUE);
		}

		if ($_FILES[$file]['name']) {

			$img = $_FILES[$file]['tmp_name'];

			$status = $this->upload_img($file, $img_path, $img);

			return $status;
		}
	}

	function upload_img($outputImage, $dst, $img)

	{
		// If GD is not loaded by the web server (Apache may use a different php.ini than CLI),
		// fall back to a plain file copy so the image is still saved.
		if (!function_exists('imagecreatefromjpeg')) {
			// Determine the source file path
			$src_file = isset($_FILES[$outputImage]['tmp_name']) ? $_FILES[$outputImage]['tmp_name'] : $img;
			if ($src_file && is_uploaded_file($src_file)) {
				return move_uploaded_file($src_file, $dst);
			}
			return copy($img, $dst);
		}

		if (($img_info = getimagesize($img)) === FALSE) {

			// die("Image not found or not an image");

			return false;
		}

		$width = $img_info[0];

		$height = $img_info[1];

		switch ($img_info[2]) {

			case IMAGETYPE_GIF:
				$src = imagecreatefromgif($img);

				$tmp = imagecreatetruecolor($width, $height);

				$kek = imagecolorallocate($tmp, 255, 255, 255);

				imagefill($tmp, 0, 0, $kek);

				break;

			case IMAGETYPE_JPEG:
				$src = imagecreatefromjpeg($img);

				$tmp = imagecreatetruecolor($width, $height);

				break;

			case IMAGETYPE_PNG:
				$src = imagecreatefrompng($img);

				$tmp = imagecreatetruecolor($width, $height);

				$kek = imagecolorallocate($tmp, 255, 255, 255);

				imagefill($tmp, 0, 0, $kek);

				break;

			default: //die("Unknown filetype");

				return false;
		}

		imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);

		$res = imagejpeg($tmp, $dst);

		return $res;

	}

	function rrmdir($path)
	{

		// Open the source directory to read in files

		$i = new DirectoryIterator($path);

		foreach ($i as $f) {

			if ($f->isFile()) {

				unlink($f->getRealPath());
			} else if (!$f->isDot() && $f->isDir()) {

				rrmdir($f->getRealPath());
			}
		}

		rmdir($path);
	}

	function remove_img($file, $id)
	{

		$path = self::PROD_PATH . $id . "/" . $file;

		chmod(self::PROD_PATH . $id, 0777);

		unlink($path);

		$model = self::CAT_MODEL;

		$status = $this->$model->delete_prodimage($file);

		if ($status) {

			echo "Picture removed successfully";
		}
	}

	/**

	 * Billing Functions Starts

	 */

	public function billing($type = "", $id = "", $billno = "")
	{

		$model = "ret_billing_model";

		$pur_model = "ret_purchase_order_model";

		$set_model = "admin_settings_model";

		$sms_model = "admin_usersms_model";

		$ordermodel = "ret_order_model";

		$data['type']	= $type;

		switch ($type) {

			case 'add':

				$profile                                    = $this->admin_settings_model->profileDB("get", $this->session->userdata('profile'));

				$data['otp_settings']                       =  $this->$model->get_otp_profile_settings($this->session->userdata('profile'));

				$data['billing']		                    = $this->$model->get_empty_record();

				$data['billing']['credit_collection_disc_otp']    = $profile['credit_collection_disc_otp'];

				$data['billing']['credit_sales_otp_req']    = $profile['credit_sales_otp_req'];

				$data['billing']['bill_disc_approval_type']    = $profile['bill_disc_approval_type'];

				$data['billing']['credit_sales_approval_type']    = $profile['credit_sales_approval_type'];

	
				// BIL-CLT02: Three granular discount limit toggles (1=enforce, 0=bypass)
				$data['billing']['enable_emp_disc_limit']      = $this->$model->get_ret_settings('enable_emp_disc_limit');
				$data['billing']['enable_mc_va_disc_limit']    = $this->$model->get_ret_settings('enable_mc_va_disc_limit');
				$data['billing']['enable_disc_blw_metal_rate'] = $this->$model->get_ret_settings('enable_disc_blw_metal_rate');


				$data['billing']['is_direct_bill_required'] = $this->$model->get_ret_settings('is_direct_bill_required');

				$data['billing']['chit_rate_calculation_type']     = $this->$model->get_ret_settings('chit_rate_calculation_type');

				$data['billing']['insurance_amount'] = $this->$model->get_ret_settings('insurance_amount');
				
				$data['emp_setting']	        = $this->$model->get_employee_settings($this->session->userdata('uid'));

				$data['profile_setting']		= $this->admin_settings_model->profileDB('get', $data['emp_setting']['id_profile']);

				$data['bill_other_item']                    = array("item_details" => array(), "old_matel_details" => array(), "stone_details" => array(), "other_material_details" => array(), "voucher_details" => array(), "chit_details" => array(), "advance_details" => array());

				$data['uom']		                        = $this->$model->getUOMDetails();

				$data['settings']		= $this->$model->get_retSettings();

				//print_r($this->session->all_userdata());exit;

				$data['main_content'] = "billing/form";

				$data['access'] = $this->$set_model->get_access('admin_ret_billing/billing/add');


				$this->load->view('layout/template', $data);

				//$this->load->view('layout/common_customer_modal');

				$this->load->view('layout/common_customerslider');

				break;

			case 'list':

				$data['main_content'] = "billing/list";

				$this->load->view('layout/template', $data);

				break;

			case 'approvallist':

				$data['main_content'] = "billing/approvallist";

				$data['access'] = $this->$set_model->get_access('admin_ret_billing/billing/approvallist');


				$this->load->view('layout/template', $data);

				break;

			case "split_save":

				// echo "<pre>";print_r($_POST);exit;

				$addData                    = $_POST['billing'];

				$sale_details               = $_POST['split_sale'];

				$allow_submit               = TRUE;

				$dCData                     = $this->admin_settings_model->getBranchDayClosingData($addData['id_branch']);

				$fin_year                   = $this->$model->get_FinancialYear();

				$billSale                   = (isset($_POST['split_sale']) ? $_POST['split_sale'] : '');

				$form_secret                = isset($addData["form_secret"]) ? $addData["form_secret"] : '';

				$dCData                     = $this->admin_settings_model->getBranchDayClosingData($addData['id_branch']);

				$bill_date                  = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);



				$metal_rate                 = $this->$model->get_branchwise_rate($addData['id_branch']);

				$bill_split_ref_id			= time();

				// echo "<pre>";print_r($_POST);exit;

				$this->db->trans_begin();

				if (!empty($sale_details)) {

					foreach ($billSale['is_est_details'] as $key => $val) {

						$tag_id     = ($billSale['tag'][$key] != '' ? $billSale['tag'][$key] : '');
						if ($tag_id != '') {
							$tag_status = $this->$model->get_tag_status($tag_id); {

								if ($tag_status['tag_status'] == 0) {

									$allow_submit = TRUE;
								}
							}
						}
					}

					if ($this->session->userdata('FORM_SECRET')) {

						if (strcasecmp($form_secret, ($this->session->userdata('FORM_SECRET'))) === 0) {

							if ($allow_submit) {

								if ($addData['isMetal'] == 1) {

									$metal_details  = $this->$model->get_metal_details($addData['metal_type']);
								}

								$serviceID = 26;

								if (sizeof($dCData) > 0) {

									foreach ($billSale['is_est_details'] as $key => $val) {

										$bill_no = $this->$model->code_number_generator($addData['id_branch'], $addData['metal_type'], $addData['is_eda']);   //Bill Number Generate

										$data = array(

											'bill_no'		    => ($addData['isMetal'] == 1 ? $metal_details['metal_code'] . '-' . $bill_no : $bill_no),

											'fin_year_code'		=> $fin_year['fin_year_code'],

											'bill_type'		    => $addData['bill_type'],

											'round_off_amt'		=> $billSale['round_of_amt'][$key],

											'goldrate_22ct'		=> $addData['goldrate_22ct'],

											'silverrate_1gm'	=> $addData['silverrate_1gm'],

											'goldrate_14ct'	    => $addData['goldrate_14ct'],

											'goldrate_9ct'	    => $addData['goldrate_9ct'],

											'goldrate_18ct'	    => ($metal_rate['goldrate_18ct'] != '' ? $metal_rate['goldrate_18ct'] : 0),

											'form_secret'	    => $addData['form_secret'] . '_' . $billSale['tag'][$key] . '_' . $key,

											'metal_type'	    => ($addData['metal_type'] != '' ? $addData['metal_type'] : NULL),

											'pan_no'		    => (!empty($billSale['pan_no'][$key]) ? $billSale['pan_no'][$key] : NULL),

											'aadhar_no'		    => (!empty($billSale['aadhar_no'][$key]) ? $billSale['aadhar_no'][$key] : NULL),

											'bill_cus_id'   	=> $billSale['id_customer'][$key],

											'customer_name'      => ($billSale['customer_name'][$key] ? ucfirst($billSale['customer_name'][$key]) : NULL),

											'tot_discount'	    => $billSale['discount'][$key],

											'tot_bill_amount'	=> $billSale['billamount'][$key],

											'tot_amt_received'	=> $billSale['split_recd_amount'][$key],

											'is_credit'	        => $billSale['is_credit'][$key],

											'credit_status'		=> (!empty($billSale['is_credit'][$key] && $billSale['is_credit'][$key] == 1) ? 2 : 1),

											'credit_due_date'	=> (!empty($billSale['credit_due_date'][$key]) ? ($billSale['is_credit'][$key] == 1 ? implode('-', array_reverse(explode('-',$billSale['credit_due_date'][$key]))) : NULL) : NULL),

											'bill_date'	        => $bill_date,

											'created_time'	    => date("Y-m-d H:i:s"),

											'created_by'        => $this->session->userdata('uid'),

											'counter_id'        => ($this->session->userdata('counter_id') != '' ? $this->session->userdata('counter_id') : NULL),

											'id_branch'         => $addData['id_branch'],

											'id_delivery'       => $addData['id_delivery'],

											'remark'   	        => (!empty($addData['remark']) ? $addData['remark'] : NULL),

											'credit_disc_amt'	=> (!empty($addData['credit_discount_amt']) ? $addData['credit_discount_amt'] : 0),

											'billing_for'       => $addData['billing_for'],

											'id_cmp_emp'        => (!empty($addData['id_cmp_emp']) ? $addData['id_cmp_emp'] : NULL),

											'make_as_advance'   => ($addData['make_as_advance'] != NULL ? $addData['make_as_advance'] : 0),

											'advance_deposit'   => ($addData['advance_deposit'] != NULL ? $addData['advance_deposit'] : 0),

											'tcs_tax_amt'       => (!empty($addData['tcs_tax_amt']) ? $addData['tcs_tax_amt'] : 0),

											'tcs_tax_per'       => ($addData['tcs_percent'] > 0 ? $addData['tcs_percent'] : 0),

											'tds_percent'       => ($addData['tds_percent'] > 0 ? $addData['tds_percent'] : 0),

											'tds_tax_amt'       => ($addData['tds_tax_value'] > 0 ? $addData['tds_tax_value'] : 0),

											'delivered_at' 		=> $addData['delivered_at'],

											'delivery_address_type' => $addData['delivery_address_type'],

											'is_eda'            => $addData['is_eda'],

											'id_employee'       => ($addData['id_employee'] != '' ? $addData['id_employee'] : NULL),

											'credit_ret_amt'    => ($addData['credit_ret_amt'] > 0 ? $addData['credit_ret_amt'] : ($addData['credit_ret_amt'] * -1)),

											'credit_due_amt'    => ($addData['credit_due_amt'] > 0 ? $addData['credit_due_amt'] : 0),

											'is_to_be'          => (!empty($addData['is_to_be']) ? $addData['is_to_be'] : 0),

											'eda_tax_calc'      => ($addData['is_eda_tax_calc'] != '' ? $addData['is_eda_tax_calc'] : 0),

											'is_bill_split'		=> 1,

											'bill_split_ref_id' => $bill_split_ref_id,

										);

										$insId = $this->$model->insertData($data, 'ret_billing');

										if ($insId) {

											if ($billSale['pan_no'][$key] != '' || $billSale['dl_no'][$key] != '' || $billSale['pp_no'][$key] != '') {
												$this->$model->updateData(
													array(
														'pan'                   => ($billSale['pan_no'][$key] != '' ? strtoupper($billSale['pan_no'][$key]) : NULL),
														'aadharid'              => ($billSale['aadhar_no'][$key] != '' ? strtoupper($billSale['aadhar_no'][$key]) : NULL),
														'driving_license_no'    => ($billSale['dl_no'][$key] != '' ? strtoupper($billSale['dl_no'][$key]) : NULL),
														'passport_no'           => ($billSale['pp_no'][$key] != '' ? strtoupper($billSale['pp_no'][$key]) : NULL)
													),
													'id_customer',
													$billSale['id_customer'][$key],
													'customer'
												);
											}

											$payment_details = json_decode($billSale['split_payment_details'][$key], true);

											foreach ($payment_details as $pay) {

												$cheque_deposit_date = ($pay['cheque_deposit_date'] != '' ? date_create($pay['cheque_deposit_date']) : NULL);

												$cheque_date = ($pay['cheque_deposit_date'] != '' ? date_format($cheque_deposit_date, "Y-m-d") : NULL);

												$arrayCashPay = array(

													'bill_id'           => $insId,

													'payment_amount'    => $pay['recd_amt'],

													'payment_mode'      => ($pay['payment_mode'] == 'CSH' ? 'Cash' : $pay['payment_mode']),

													'id_pay_device' 	=> ($pay['device_type'] != '' ? $pay['device_type'] : NULL),

													'card_type'     	=> ($pay['card_name'] != '' ? $pay['card_name'] : NULL),

													'card_no'			=> (($pay['payment_mode'] == 'CC' || $pay['payment_mode'] == 'DC' ? ($pay['ref_no'] != '' ? $pay['ref_no'] : NULL) : NULL)),

													'payment_ref_number' => ($pay['payment_mode'] == 'CC' || $pay['payment_mode'] == 'DC' ? $pay['approval_no'] : (($pay['ref_no'] != '') ? $pay['ref_no'] : NULL)),

													'NB_type'			=> ($pay['net_bank_type'] != '' ? $pay['net_bank_type'] : NULL),

													'id_bank'			=> ($pay['bankname'] != '' ? $pay['bankname'] : NULL),

													'cheque_no'			=> ($pay['payment_mode'] == 'CHQ' ? $pay['ref_no'] : NULL),

													'cheque_date'		=> $cheque_date,

													'type'              => 1,

													'payment_for'	    => 1,

													'payment_status'    => 1,

													'payment_date'		=> date("Y-m-d H:i:s"),

													'created_time'	    => date("Y-m-d H:i:s"),

													'created_by'	    => $this->session->userdata('uid')

												);

												$cashPayInsert = $this->$model->insertData($arrayCashPay, 'ret_billing_payment');
											}



											$arrayBillSales = array(

												'bill_id' => $insId,

												'esti_item_id'  => (isset($billSale['est_itm_id'][$key]) ? ($billSale['est_itm_id'][$key] != '' ? $billSale['est_itm_id'][$key] : NULL) : NULL),

												'item_type' 	=> ($billSale['itemtype'][$key] != '' ? $billSale['itemtype'][$key] : NULL),

												'bill_type' 	=> $billSale['is_est_details'][$key],

												'total_cgst' 	=> $billSale['total_cgst'][$key],

												'total_sgst' 	=> $billSale['total_sgst'][$key],

												'total_igst' 	=> $billSale['total_igst'][$key],

												'product_id' 	=> ($billSale['product'][$key] != '' ? $billSale['product'][$key] : NULL),

												'design_id' 	=> ($billSale['design'][$key] != '' ? $billSale['design'][$key] : NULL),

												'id_sub_design' => ($billSale['id_sub_design'][$key] != '' ? $billSale['id_sub_design'][$key] : NULL),

												'tag_id'		=> ($billSale['tag'][$key] != '' ? $billSale['tag'][$key] : NULL),

												'quantity' 		=> 1,

												'purity' 		=> ($billSale['purity'][$key] != '' ? $billSale['purity'][$key] : NULL),

												'size' 			=> ($billSale['size'][$key] != '' ? $billSale['size'][$key] : NULL),

												'uom' 			=> ($billSale['uom'][$key] != '' ?  $billSale['uom'][$key] : NULL),

												'piece' 		=> ($billSale['pcs'][$key] != '' ? $billSale['pcs'][$key] : NULL),

												'less_wt' 		=> ($billSale['less'][$key] != '' ? $billSale['less'][$key] : NULL),

												'net_wt' 		=> $billSale['net'][$key],

												'gross_wt' 		=> $billSale['gross'][$key],

												'pure_wt' 		=> $billSale['pure_wt'][$key],

												'calculation_based_on' => $billSale['calltype'][$key],

												'wastage_percent' => ($billSale['wastage'][$key] !== '' && $billSale['wastage'][$key] !== null) ? $billSale['wastage'][$key] : 0,

												'mc_value' 		=> ($billSale['mc'][$key] != '' ? $billSale['mc'][$key] : 0),

												'mc_type' 		=> ($billSale['bill_mctype'][$key] != '' ? $billSale['bill_mctype'][$key] : NULL),

												'item_cost' 	=> $billSale['total_sales_amount'][$key],

												'item_total_tax' => $billSale['item_total_tax'][$key],

												'tax_group_id'  => $billSale['taxgroup'][$key],

												'bill_discount'  => $billSale['discount'][$key],

												'rate_per_grm'  => $billSale['per_grm'][$key],

												'is_partial_sale'   => ($billSale['is_partial'][$key] != '' ? $billSale['is_partial'][$key] : 0),

												'mc_discount'       => ($billSale['mc_discount'][$key] != '' ? $billSale['mc_discount'][$key] : 0),

												'wastage_discount'  => ($billSale['wastage_discount'][$key] != '' ? $billSale['wastage_discount'][$key] : 0),

												'item_blc_discount'  => ($billSale['item_blc_discount'][$key] != '' ? $billSale['item_blc_discount'][$key] : 0),

												'bill_discount_type' => $addData['bill_discount_type'],

												'id_collecion_maping_det' => ($billSale['id_collecion_maping_det'][$key] != '' ? $billSale['id_collecion_maping_det'][$key] : 0),

												'is_non_tag'    => !empty($billSale['is_non_tag'][$key]) ? $billSale['is_non_tag'][$key] : 0,

												'id_orderdetails' => (isset($billSale['id_orderdetails'][$key]) ? ($billSale['id_orderdetails'][$key] != '' ? $billSale['id_orderdetails'][$key] : NULL) : NULL),

												'round_of_amt'	=> ($billSale['round_of_amt'][$key] != '' ? $billSale['round_of_amt'][$key] : 0),

												'id_section'	=> ($billSale['id_section'][$key] != '' ? $billSale['id_section'][$key] : 0),

												'item_emp_id'	=> ($billSale['item_emp_id'][$key] != '' ? $billSale['item_emp_id'][$key] : 0),
											);

											if (!empty($arrayBillSales)) {


												$tagInsert = $this->$model->insertData($arrayBillSales, 'ret_bill_details');

												if ($tagInsert) {
													$metal_type = ($addData['metal_type'] != '' && $addData['metal_type'] != null ? $addData['metal_type'] : '');
													$ref_no = $this->$model->generateRefNo($addData['id_branch'], 'sales_ref_no', $metal_type, $addData['is_eda']);

													$this->$model->updateData(array('sales_ref_no' => $ref_no), 'bill_id', $insId, 'ret_billing');

													if ($billSale['stone_details'][$key]) {

														$stone_details = json_decode($billSale['stone_details'][$key], true);

														foreach ($stone_details as $stone) {

															$stone_data = array(

																'bill_id'        => $insId,

																'bill_det_id'   => $tagInsert,

																'pieces'        => $stone['stone_pcs'],

																'wt'            => $stone['stone_wt'],

																'stone_id'      => $stone['stone_id'],

																'price'         => $stone['stone_price'],

																'uom_id'        => $stone['uom_id'],

																//'certification_price'=>($stone['certification_cost']!='' ?$stone['certification_cost']:NULL),

																'item_type'     => 1, //Sale item,

																'is_apply_in_lwt' => $stone['is_apply_in_lwt'],

																'stone_cal_type' => $stone['stone_cal_type'],

																'rate_per_gram'  => $stone['rate_per_gram'],

															);

															$stoneInsert = $this->$model->insertData($stone_data, 'ret_billing_item_stones');

															//print_r($this->db->last_query());exit;

														}
													}

													if ($billSale['other_metal_details'][$key]) {

														$other_metal_details = json_decode($billSale['other_metal_details'][$key], true);

														foreach ($other_metal_details as $other_metal_det) {

															$est_other_metals = array(

																'bill_det_id'               => $tagInsert,

																'tag_other_itm_metal_id'    => $other_metal_det['tag_other_itm_metal_id'],

																'tag_other_itm_pur_id'      => $other_metal_det['tag_other_itm_pur_id'],

																'tag_other_itm_grs_weight'  => $other_metal_det['tag_other_itm_grs_weight'],

																'tag_other_itm_wastage'     => $other_metal_det['tag_other_itm_wastage'],

																'tag_other_itm_uom'         => $other_metal_det['tag_other_itm_uom'],

																'tag_other_itm_cal_type'    => $other_metal_det['tag_other_itm_cal_type'],

																'tag_other_itm_mc'          => $other_metal_det['tag_other_itm_mc'],

																'tag_other_itm_rate'        => $other_metal_det['tag_other_itm_rate'],

																'tag_other_itm_pcs'         => $other_metal_det['tag_other_itm_pcs'],

																'tag_other_itm_amount'      => $other_metal_det['tag_other_itm_amount'],

															);

															$this->$model->insertData($est_other_metals, 'ret_bill_other_metals');
														}
													}



													if (isset($billSale['est_itm_id'][$key]) && $billSale['est_itm_id'][$key] != '') {

														//Update Estimation Items by est_itm_id

														$this->$model->updateData(array('purchase_status' => 1, 'bil_detail_id' => $tagInsert), 'est_item_id', (isset($billSale['est_itm_id'][$key]) ? $billSale['est_itm_id'][$key] : ''), 'ret_estimation_items');

														$est_details = $this->$model->get_sale_est_details($billSale['est_itm_id'][$key]);



														if ($est_details['esti_id'] != '') {

															$this->$model->updateData(array('estbillid' => $insId), 'estimation_id', $est_details['esti_id'], 'ret_estimation');
														}
													}

													if ($billSale['tag'][$key] != '') {

														//Update Estimation Items by est_itm_id

														//$this->$model->updateData(array('purchase_status'=>1,'bil_detail_id'=>$tagInsert),'tag_id',(isset($billSale['tag'][$key])? $billSale['tag'][$key]:''), 'ret_estimation_items');



														$this->$model->updateData(array('tag_status' => 1), 'tag_id', $billSale['tag'][$key], 'ret_taging');



														if ($billSale['itemtype'][$key] == 0) {

															$this->$model->updateData(array('is_partial' => $billSale['is_partial'][$key]), 'tag_id', $billSale['tag'][$key], 'ret_taging');
														}

														//Update Tag Log status

														$form_secret = 	$addData['form_secret'] . '_' . $billSale['tag'][$key] . '_' . $key;

														$tag_log = array(

															'tag_id'	  => $billSale['tag'][$key],

															'date'		  => $bill_date,

															'status'	  => 1,

															'from_branch' => $addData['id_branch'],

															'to_branch'	  => NULL,

															'form_secret'   => $form_secret,

															'issuspensestock' => $addData['bill_type'] == 15 ? 1 : 0,

															'created_on'  => date("Y-m-d H:i:s"),

															'created_by'  => $this->session->userdata('uid'),

														);



														$this->$model->insertData($tag_log, 'ret_taging_status_log');



														$tag_status = $this->$model->get_tag_status($billSale['tag'][$key]);

														if ($tag_status['id_section'] != null && $tag_status['id_section'] != '') {

															$secttag_log = array(

																'tag_id'	        => $billSale['tag'][$key],

																'date'		        => $bill_date,

																'status'	        => 1,

																'from_branch'       => $addData['id_branch'],

																'to_branch'	        => NULL,

																'from_section'      => $tag_status['id_section'],

																'form_secret'       => $form_secret,

																'to_section'        => NULL,

																'issuspensestock'   => $addData['bill_type'] == 15 ? 1 : 0,

																'created_on'        => date("Y-m-d H:i:s"),

																'created_by'        => $this->session->userdata('uid'),

															);

															$this->$model->insertData($secttag_log, 'ret_section_tag_status_log');
														}
													}

													//stock maintaince

													if ($billSale['is_non_tag'][$key] == 1) {

														$existData = array('id_section' => $billSale['id_section'][$key], 'id_product' => $billSale['product'][$key], 'id_design' => $billSale['design'][$key], 'id_sub_design' => $billSale['id_sub_design'][$key], 'id_branch' => $addData['id_branch']);

														$isExist = $this->$model->checkNonTagItemExist($existData);

														if ($isExist['status'] == TRUE) {

															$nt_data = array(

																'id_nontag_item' => $isExist['id_nontag_item'],

																'no_of_piece'	=> ($billSale['pcs'][$key] != '' ? $billSale['pcs'][$key] : 0),

																'gross_wt'		=> ($billSale['gross'][$key] > 0 ? $billSale['gross'][$key] : 0),

																'net_wt'		=> $billSale['net'][$key],

																'less_wt'		=> $billSale['less'][$key],

																'updated_by'	=> $this->session->userdata('uid'),

																'updated_on'	=> date('Y-m-d H:i:s'),

															);

															$this->$model->updateNTData($nt_data, '-');

															$non_tag_data = array(

																'from_branch'	=> $addData['id_branch'],

																'to_branch'	    => NULL,

																'no_of_piece'   => ($billSale['pcs'][$key] != '' ? $billSale['pcs'][$key] : NULL),

																'less_wt' 		=> ($billSale['less'][$key] != '' ? $billSale['less'][$key] : NULL),

																'net_wt' 		=> $billSale['net'][$key],

																'gross_wt' 		=> ($billSale['gross'][$key] > 0 ? $billSale['gross'][$key] : 0),

																'product'		=> $billSale['product'][$key],

																'design'		=> ($billSale['design'][$key] != '' ? $billSale['design'][$key] : NULL),

																'id_sub_design'	=> ($billSale['id_sub_design'][$key] != '' ? $billSale['id_sub_design'][$key] : NULL),

																'date'  	    => $bill_date,

																'created_on'  	=> date("Y-m-d H:i:s"),

																'created_by'   	=> $this->session->userdata('uid'),

																'status'   		=> 1,

																'bill_id'       => $insId

															);

															$this->$model->insertData($non_tag_data, 'ret_nontag_item_log');

															if ($billSale['id_section'][$key] != '') {

																$section_non_tag_data = array(

																	'from_branch'	=> $addData['id_branch'],

																	'to_branch'	    => NULL,

																	'from_section'	=> $billSale['id_section'][$key],

																	'to_section'	=> NULL,

																	'no_of_piece'   => ($billSale['pcs'][$key] != '' ? $billSale['pcs'][$key] : NULL),

																	'less_wt' 		=> ($billSale['less'][$key] != '' ? $billSale['less'][$key] : NULL),

																	'net_wt' 		=> $billSale['net'][$key],

																	'gross_wt' 		=> ($billSale['gross'][$key] != '' ? $billSale['gross'][$key] : 0),

																	'product'		=> $billSale['product'][$key],

																	'design'		=> ($billSale['design'][$key] != '' ? $billSale['design'][$key] : NULL),

																	'id_sub_design'	=> ($billSale['id_sub_design'][$key] != '' ? $billSale['id_sub_design'][$key] : NULL),

																	'date'  	    => $bill_date,

																	'created_on'  	=> date("Y-m-d H:i:s"),

																	'created_by'   	=> $this->session->userdata('uid'),

																	'status'   		=> 1,

																	'bill_id'       => $insId

																);

																$this->$model->insertData($section_non_tag_data, 'ret_section_nontag_item_log');
															}
														}
													}

													//stock maintaince

													//Advance Adjusement

													$advance_adj_details = json_decode($billSale['cus_advance_details'][$key], true);

													//print_r($advance_adj_details);exit;s

													if (sizeof($advance_adj_details) > 0) {

														foreach ($advance_adj_details as $obj) {

															if ($obj['is_receipt_select'] == 1) {

																$data_adv_amount    = array(

																	'id_issue_receipt'  => $obj['id_issue_receipt'],

																	'bill_id'           => $insId,

																	'utilized_amt'      => $obj['adj_amount'],

																	'cash_utilized_amt' => $obj['cash_pay']

																);

																$insId_adv_amount = $this->$model->insertData($data_adv_amount, 'ret_advance_utilized');
															}
														}
													}

													//Advance Adjusement

													$old_metal_details = json_decode($billSale['old_metal_details'][$key], true);

													// $arrayPurchaseBill =[];

													if (sizeof($old_metal_details) > 0) {

														//Update Ref No

														$ref_no = $this->$model->generateRefNo($addData['id_branch'], 'pur_ref_no', $addData['metal_type'], $addData['is_eda']);

														$this->$model->updateData(array('pur_ref_no' => $ref_no), 'bill_id', $insId, 'ret_billing');

														//Update Ref No

														foreach ($old_metal_details as $billPurchase) {

															$old_metal_amount += $billPurchase['billamount'];

															$old_metal_weight += ($billPurchase['net'] * ($billPurchase['purity']) / 91.6);

															$arrayPurchaseBill = array(

																'bill_id'                   => $insId,

																'current_branch'            => $addData['id_branch'],

																'metal_type'                => $billPurchase['metal_type'],

																'item_type'                 => $billPurchase['itemtype'],

																'id_old_metal_category'    => $billPurchase['old_metal_category'],

																'id_old_metal_type'    => $billPurchase['old_metal_type'],

																'esti_old_metal_sale_id'    => $billPurchase['est_old_itm_id'],

																'piece'                     => empty($billPurchase['piece']) ? $billPurchase['pcs'][$key] : $billPurchase['piece'][$key],

																'gross_wt'                  => $billPurchase['gross'],

																'stone_wt'                  => $billPurchase['stone_wt'],

																'dust_wt'                   => $billPurchase['dust_wt'],

																'net_wt'                    => $billPurchase['net'],

																'wast_wt'                   => $billPurchase['wastage_wt'],

																'wastage_percent'           => ($billPurchase['wastage'] !== '' && $billPurchase['wastage'] !== null) ? $billPurchase['wastage'] : 0,

																'rate'                      => $billPurchase['billamount'],

																'rate_per_grm'              => $billPurchase['rate_per_grm'],

																'touch'                     => ($billPurchase['touch'] != '' ? $billPurchase['touch'] : 0),

																'purity'                    => ($billPurchase['purity'] != '' ? $billPurchase['purity'] : 0),

																'old_metal_rate'            => $this->$model->getOldMetalRate($billPurchase['metal_type']),

																'bill_discount'             => empty($billPurchase['discount']) ? 0 : $billPurchase['discount'],
																'id_employee'               => (!empty($billPurchase['id_employee']) ? $billPurchase['id_employee'] : NULL)
															);

															$oldMetal = $this->$model->insertData($arrayPurchaseBill, 'ret_bill_old_metal_sale_details');

															//	print_r($arrayPurchaseBill);exit;
															//print_r($this->db->last_query());exit;
															if ($oldMetal) {

																$this->$model->updateData(array('purchase_status' => 1, 'bill_id' => $insId), 'old_metal_sale_id', $billPurchase['est_old_itm_id'], 'ret_estimation_old_metal_sale_details');

																$est_details = $this->$model->get_old_metal_est_details($billPurchase['est_old_itm_id']);
																if ($est_details['est_id'] != '') {
																	$this->$model->updateData(array('estbillid' => $insId), 'estimation_id', $est_details['est_id'], 'ret_estimation');
																}

																if ($billPurchase['stone_details']) {
																	$stone_details = json_decode($billPurchase['stone_details'], true);
																	foreach ($stone_details as $stone) {
																		$stone_data = array(
																			'bill_id'        => $insId,
																			'old_metal_sale_id' => $oldMetal,
																			'pieces'        => $stone['stone_pcs'],
																			'wt'            => $stone['stone_wt'],
																			'stone_id'      => $stone['stone_id'],
																			'price'         => $stone['stone_price'],
																			'uom_id'        => $stone['uom_id'],
																			'item_type'     => 2 //Purchase item
																		);
																		$stoneInsert = $this->$model->insertData($stone_data, 'ret_billing_item_stones');
																	}
																}
															}
														}
													}
												}
											}
										}
									}
								}
							} else {

								$return_data = array('status' => FALSE, 'id' => '');

								$this->session->set_flashdata('chit_alert', array('message' => 'Kindly Check The Tag or Estimation No', 'class' => 'danger', 'title' => 'Add Billing'));
							}
						} else {

							$return_data = array('status' => FALSE, 'id' => '');

							$this->session->set_flashdata('chit_alert', array('message' => 'Unable To Proceed Your Request.Invalid Form Submit', 'class' => 'danger', 'title' => 'Add Billing'));
						}
					} else {

						$return_data = array('status' => FALSE, 'id' => '');

						$this->session->set_flashdata('chit_alert', array('message' => 'Form Already Submitted', 'class' => 'danger', 'title' => 'Add Billing'));
					}

					if ($this->db->trans_status() === TRUE) {


						$this->db->trans_commit();

						$log_data = array(

							'id_log'        => $this->session->userdata('id_log'),

							'event_date'    => date("Y-m-d H:i:s"),

							'module'        => 'Billing',

							'operation'     => 'Add',

							'record'        =>  $insId,

							'remark'        => 'Record added successfully'

						);

						$this->log_model->log_detail('insert', '', $log_data);

						$return_data = array('status' => TRUE, 'id' => $insId);
					} else {

						$this->db->trans_rollback();

						$return_data = array('status' => FALSE, 'id' => '');

						echo $this->db->_error_message() . "<br/>";

						echo $this->db->last_query();
						exit;

						$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Billing'));
					}
				} else {

					$return_data = array('status' => FALSE, 'id' => '');

					$this->session->set_flashdata('chit_alert', array('message' => 'No Records Found..', 'class' => 'danger', 'title' => 'Add Billing'));
				}

				echo json_encode($return_data);

				break;

			case "save":

				// 1-Sales, 2-Sales&Purchase, 3-Sales,purchase&Return, 4-Purchase, 5-Order Advance, 6-Advance,7-Sales Return,8-Credit Collection,9-Order Delivery,10-Chit Pre Close

				$addData = $_POST['billing'];

				// echo "<pre>";print_r($_POST);exit;

				$allow_submit = TRUE;

				$dCData                     = $this->admin_settings_model->getBranchDayClosingData($addData['id_branch']);

				$fin_year                   = $this->$model->get_FinancialYear();

				$bill_is_eda				= $addData['is_eda'];

				$billSale                   = (isset($_POST['sale']) ? $_POST['sale'] : '');

				$supplier_sales_bill        = (isset($_POST['supplier_sales_bill']) ? $_POST['supplier_sales_bill'] : '');

				$supplierMetalDetilas       = (isset($_POST['metal_details']) ? $_POST['metal_details'] : '');

				$billPurchase               = (isset($_POST['purchase']) ? $_POST['purchase'] : '');

				$repair_orders              =  (isset($_POST['order']) ? $_POST['order'] : '');

				$form_secret                = isset($addData["form_secret"]) ? $addData["form_secret"] : '';

				if (!empty($billSale)) {

					foreach ($billSale['is_est_details'] as $key => $val) {

						$est_itm_id = (isset($billSale['est_itm_id'][$key]) ? ($billSale['est_itm_id'][$key] != '' ? $billSale['est_itm_id'][$key] : '') : '');

						$tag_id     = ($billSale['tag'][$key] != '' ? $billSale['tag'][$key] : '');

						if ($tag_id != '') {

							$tag_status = $this->$model->get_tag_status($tag_id);

							if ($tag_status['is_partial'] == 0) {

								if ($tag_status['tag_status'] == 0) {

									$allow_submit = TRUE;
								} else {

									$allow_submit = FALSE;

									break;
								}
							} else if ($tag_status['is_partial'] == 1) {

								$allow_submit = TRUE;
							}
						}

						if ($est_itm_id != '') {

							$est_status = $this->$model->get_esti_status($est_itm_id);

							if ($est_status['purchase_status'] != '' && $est_status['purchase_status'] == 0) {

								$allow_submit = TRUE;
							} else {

								$allow_submit = FALSE;

								break;
							}
						}

						if ($billSale['billamount'][$key] > 0 && $billSale['item_total_tax'][$key] <= 0 && ($billing['is_eda'] == 1)) {

							$allow_submit = FALSE;

							break;
						}
					}
				}

				if (!empty($billPurchase)) {

					foreach ($billPurchase['is_est_details'] as $key => $val) {

						$est_old_itm_id = $billPurchase['est_old_itm_id'][$key];

						if ($est_old_itm_id) {

							$old_est_status = $this->$model->get_old_esti_status($est_old_itm_id);

							if ($est_status['purchase_status'] == 0) {

								$allow_submit = TRUE;
							} else {

								$allow_submit = FALSE;

								break;
							}
						}
					}
				}

				if (!empty($supplier_sales_bill)) {

					$tagged_item_list = json_decode($addData['returntaggeditemlist'], true);

					foreach ($tagged_item_list as $val) {

						$tag_status = $this->$model->get_tag_status($val['tag_id']);

						if ($tag_status['tag_status'] == 0) {

							$allow_submit = TRUE;
						} else {

							$allow_submit = FALSE;

							break;
						}
					}
				}

				if ($this->session->userdata('FORM_SECRET')) {

					if (strcasecmp($form_secret, ($this->session->userdata('FORM_SECRET'))) === 0) {

						if ($allow_submit) {

							if ($addData['isMetal'] == 1) {

								$metal_details  = $this->$model->get_metal_details($addData['metal_type']);
							}

							$serviceID = 26;

							if (sizeof($dCData) > 0) {

								$chit_details		        = json_decode($addData['chit_uti'], true);

								$voucher_details	        = json_decode($addData['vocuher'], true);

								$card_pay_details	        = json_decode($addData['card_pay'], true);

								$adv_adj			        = json_decode($addData['adv_adj'], true);

								$adv_adj_details            =  $adv_adj[0];

								$cheque_details	            = json_decode($addData['chq_pay'], true);

								$net_banking_details        = json_decode($addData['net_bank_pay'], true);

								$order_adv_adj_details		= json_decode($addData['order_adv_adj'], true);

								$chit_deposit_acc_details	= json_decode($addData['chit_deposit_details'], true);

								$bill_no                    = $this->$model->code_number_generator($addData['id_branch'], $addData['metal_type'], $addData['is_eda']);   //Bill Number Generate



								$ref_bill_id                = ($addData['bill_type'] == 8 ? (!empty($addData['ret_bill_id']) ? $addData['ret_bill_id'] : NULL) : NULL);

								$bill_date                  = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

								$metal_rate                 = $this->$model->get_branchwise_rate($addData['id_branch']);

								$data = array(

									'bill_no'		    => ($addData['isMetal'] == 1 ? $metal_details['metal_code'] . '-' . $bill_no : $bill_no),

									'fin_year_code'		=> $fin_year['fin_year_code'],

									'ref_bill_id'	    => $ref_bill_id,

									'bill_type'		    => $addData['bill_type'],

									'round_off_amt'		=> $addData['round_off'],

									'goldrate_22ct'		=> $addData['goldrate_22ct'],

									'silverrate_1gm'	=> $addData['silverrate_1gm'],

									'goldrate_14ct'	    => $addData['goldrate_14ct'],

									'goldrate_9ct'	    => $addData['goldrate_9ct'],

									'goldrate_18ct'	    => $metal_rate['goldrate_18ct'],

									'form_secret'	    => $addData['form_secret'],

									'metal_type'	    => (!empty($addData['metal_type']) ? $addData['metal_type'] : 0 ), 

									'handling_charges'	=> ($addData['handling_charges'] != '' ? $addData['handling_charges'] : 0),

									'return_charges'	=> ($addData['return_charges'] != '' ? $addData['return_charges'] : 0),

									'pan_no'		    => (!empty($addData['pan_no']) ? $addData['pan_no'] : NULL),

									'aadhar_no'		    => (!empty($addData['aadhar_no']) ? $addData['aadhar_no'] : NULL),

									'bill_cus_id'   	=> (!empty($addData['bill_cus_id']) ? $addData['bill_cus_id'] : NULL),

									'tot_discount'	    => (!empty($addData['discount']) ? $addData['discount'] : 0),

									'tot_bill_amount'	=> (!empty($addData['total_cost']) ? $addData['total_cost'] : 0),

									'tot_amt_received'	=> (!empty($addData['tot_amt_received']) ? $addData['tot_amt_received'] : 0),

									'is_credit'			=> (!empty($addData['is_credit']) ? $addData['is_credit'] : 0),

									'credit_status'		=> (!empty($addData['is_credit'] && $addData['is_credit'] == 1) ? 2 : 1),

									// 'credit_due_date'	=> (!empty($addData['credit_due_date']) ? ($addData['is_credit'] == 1 ? $addData['credit_due_date'] : NULL) : NULL),

									'credit_due_date'	=> (!empty($addData['credit_due_date'][$key]) ? ($addData['is_credit'][$key] == 1 ? implode('-', array_reverse(explode('-',$addData['credit_due_date']))): NULL ) : NULL ),

									'credit_reference'   => (!empty($addData['credit_reference']) ? ($addData['is_credit'] == 1 ? $addData['credit_reference'] : NULL) : NULL),

									'bill_date'	        => $bill_date,

									'created_time'	    => date("Y-m-d H:i:s"),

									'created_by'        => $this->session->userdata('uid'),

									'counter_id'        => ($this->session->userdata('counter_id') != '' ? $this->session->userdata('counter_id') : NULL),

									'id_branch'         => $addData['id_branch'],

									'id_delivery'       => $addData['id_delivery'],

									'remark'   	        => (!empty($addData['remark']) ? $addData['remark'] : NULL),

									'credit_disc_amt'	=> (!empty($addData['credit_discount_amt']) ? $addData['credit_discount_amt'] : 0),

									'billing_for'       => $addData['billing_for'],

									'id_cmp_emp'        => (!empty($addData['id_cmp_emp']) ? $addData['id_cmp_emp'] : NULL),

									'make_as_advance'   => ($addData['make_as_advance'] != NULL ? $addData['make_as_advance'] : 0),

									'advance_deposit'   => ($addData['advance_deposit'] != NULL ? $addData['advance_deposit'] : 0),

									'tcs_tax_amt'       => (!empty($addData['tcs_tax_amt']) ? $addData['tcs_tax_amt'] : 0),

									'tcs_tax_per'       => ($addData['tcs_percent'] > 0 ? $addData['tcs_percent'] : 0),

									'tds_percent'       => ($addData['tds_percent'] > 0 ? $addData['tds_percent'] : 0),

									'tds_tax_amt'       => ($addData['tds_tax_value'] > 0 ? $addData['tds_tax_value'] : 0),

									'delivered_at' 		=> $addData['delivered_at'],

									'delivery_address_type' => $addData['delivery_address_type'],

									'is_eda'            => $addData['is_eda'],

									'id_employee'       => ($addData['id_employee'] != '' ? $addData['id_employee'] : NULL),

									'credit_ret_amt'    => ($addData['credit_ret_amt'] > 0 ? $addData['credit_ret_amt'] : ($addData['credit_ret_amt'] * -1)),

									'credit_due_amt'    => ($addData['credit_due_amt'] > 0 ? $addData['credit_due_amt'] : 0),

									'is_to_be'          => (!empty($addData['is_to_be']) ? $addData['is_to_be'] : 0),

									'eda_tax_calc'      => $addData['is_eda_tax_calc'],

									'customer_name'      => ($addData['customer_name'] ? ucfirst($addData['customer_name']) : NULL),

									'gst_number'		=> (!empty($addData['gst_number']) ? $addData['gst_number'] : NULL),

									'is_otp_approved'       => $addData['is_otp_approved'],

									'otp_approved_by'       => ($addData['otp_approved_by'] != "" ? $addData['otp_approved_by'] : NULL),

									'disc_approved_id'       => ($addData['disc_approved_id'] != "" ? $addData['disc_approved_id'] : NULL),

									'disc_approved_by'       => ($addData['disc_approved_by'] != "" ? $addData['disc_approved_by'] : NULL),

									'credit_approved_id'       => ($addData['credit_approved_id'] != "" ? $addData['credit_approved_id'] : NULL),

									'credit_approved_by'       => ($addData['credit_approved_by'] != "" ? $addData['credit_approved_by'] : NULL),

									'id_employee'				=> ($addData['id_employee'] != "" ? $addData['id_employee'] : NULL),


								);

								$this->db->trans_begin();

								$insId = $this->$model->insertData($data, 'ret_billing');

								// 	print_r($this->db->last_query());exit;

								if ($insId) {

									//Update Ref No

									if ($addData['bill_type'] == 8) {

										$credit_coll_refno = $this->$model->generateRefNo($addData['id_branch'], 'credit_coll_refno', '', $addData['is_eda']);

										$this->$model->updateData(array('credit_coll_refno' => $credit_coll_refno), 'bill_id', $insId, 'ret_billing');
									}

									if ($addData['bill_type'] == 10) {

										$chit_preclose_refno = $this->$model->generateRefNo($addData['id_branch'], 'chit_preclose_refno', '', $addData['is_eda']);

										$this->$model->updateData(array('chit_preclose_refno' => $chit_preclose_refno), 'bill_id', $insId, 'ret_billing');
									}

									//Update Ref No

									//DELIVERY ADDRESS DETAILS

									//echo "<pre>";print_r($addData);exit;

									if ($addData['delivery_address_type'] == 1) {

										$delivery_address = $this->$model->get_customer_reg_add($addData['bill_cus_id']);

										$addressData = array(

											'bill_id'	    => $insId,

											'id_customer'   => $addData['bill_cus_id'],

											'id_country'    => ($delivery_address['id_country'] != '' ? strtoupper($delivery_address['id_country']) : NULL),

											'id_state'      => ($delivery_address['id_state'] != '' ? strtoupper($delivery_address['id_state']) : NULL),

											'id_city'       => ($delivery_address['id_city'] != '' ? strtoupper($delivery_address['id_city']) : NULL),

											'address1'      => ($delivery_address['address1'] != '' ? strtoupper($delivery_address['address1']) : NULL),

											'address2'      => ($delivery_address['address2'] != '' ? strtoupper($delivery_address['address2']) : NULL),

											'address3'      => ($delivery_address['address3'] != '' ? strtoupper($delivery_address['address3']) : NULL),

											'pincode'       => ($delivery_address['pincode'] != '' ? strtoupper($delivery_address['pincode']) : NULL),

										);
									} else if ($addData['delivery_address_type'] == 0) {

										//echo "<pre>";print_r($addData);exit;

										if (isset($addData['id_delivery_address'])) {

											$delivery_address = $this->$model->getCusDelivery_address($addData['id_delivery_address']);

											$addressData = array(

												'bill_id'	    => $insId,

												'id_customer'   => $addData['bill_cus_id'],

												'id_country'    => ($delivery_address['id_country'] != '' ? strtoupper($delivery_address['id_country']) : NULL),

												'id_state'      => ($delivery_address['id_state'] != '' ? strtoupper($delivery_address['id_state']) : NULL),

												'id_city'       => ($delivery_address['id_city'] != '' ? strtoupper($delivery_address['id_city']) : NULL),

												'address1'      => ($delivery_address['address1'] != '' ? strtoupper($delivery_address['address1']) : NULL),

												'address2'      => ($delivery_address['address2'] != '' ? strtoupper($delivery_address['address2']) : NULL),

												'address3'      => ($delivery_address['address3'] != '' ? strtoupper($delivery_address['address3']) : NULL),

												'pincode'       => ($delivery_address['pincode'] != '' ? strtoupper($delivery_address['pincode']) : NULL),

											);
										} else {

											$addressData = array(

												'bill_id'	    => $insId,

												'id_customer'   => $addData['bill_cus_id'],

												'id_country'    => ($addData['del_country'] != '' ? strtoupper($addData['del_country']) : NULL),

												'id_state'      => ($addData['del_state'] != '' ? strtoupper($addData['del_state']) : NULL),

												'id_city'       => ($addData['del_city'] != '' ? strtoupper($addData['del_city']) : NULL),

												'address1'      => ($addData['del_address1'] != '' ? strtoupper($addData['del_address1']) : NULL),

												'address2'      => ($addData['del_address2'] != '' ? strtoupper($addData['del_address2']) : NULL),

												'address3'      => ($addData['del_address3'] != '' ? strtoupper($addData['del_address3']) : NULL),

												'pincode'       => ($addData['del_pincode'] != '' ? strtoupper($addData['del_pincode']) : NULL),

											);

											$cusAddressData = array(

												'address_name'  => $addData['del_address_name'],

												'id_customer'   => $addData['bill_cus_id'],

												'id_country'    => ($addData['del_country'] != '' ? strtoupper($addData['del_country']) : NULL),

												'id_state'      => ($addData['del_state'] != '' ? strtoupper($addData['del_state']) : NULL),

												'id_city'       => ($addData['del_city'] != '' ? strtoupper($addData['del_city']) : NULL),

												'address1'      => ($addData['del_address1'] != '' ? strtoupper($addData['del_address1']) : NULL),

												'address2'      => ($addData['del_address2'] != '' ? strtoupper($addData['del_address2']) : NULL),

												'address3'      => ($addData['del_address3'] != '' ? strtoupper($addData['del_address3']) : NULL),

												'pincode'       => ($addData['del_pincode'] != '' ? strtoupper($addData['del_pincode']) : NULL),

											);

											$this->$model->insertData($cusAddressData, 'customer_delivery_address');
										}
									}

									$this->$model->insertData($addressData, 'ret_bill_delivery');

									//DELIVERY ADDRESS DETAILS

									if ($addData['pan_no'] != '' || $addData['dl_no'] != '' || $addData['pp_no'] != '' || $addData['aadhar_no'] != '' ) {

										$this->$model->updateData(

											array(

												'pan' => (($addData['pan_no']) != 'undefined' && ($addData['pan_no']) != 'UNDEFINED' && ($addData['pan_no']) != '' ? strtoupper($addData['pan_no']): ''),

												'aadharid' => (($addData['aadhar_no']) != 'undefined' && ($addData['aadhar_no']) != 'UNDEFINED' && ($addData['aadhar_no']) != '' ?strtoupper($addData['aadhar_no']) : ''),

												'driving_license_no' => (($addData['dl_no']) != 'undefined' && ($addData['dl_no']) != 'UNDEFINED' && ($addData['dl_no']) != '' ? strtoupper($addData['dl_no']) : ''),

												'passport_no' => (($addData['pp_no']) != 'undefined' && ($addData['pp_no']) != 'UNDEFINED' && ($addData['pp_no']) != '' ?  strtoupper($addData['pp_no']) : '')
											),

											'id_customer',
											$addData['bill_cus_id'],
											'customer'
										);
									}

									//Gift voucher details

									if ($addData['gift_voucher_amt'] > 0) {

										$customer = $this->$model->get_customer($addData['bill_cus_id']);

										$ret_settings = $this->$model->get_empty_record();

										$code = substr(strtoupper($customer['firstname']), 0, 4) . mt_rand(1001, 9999);

										$gift_card_data = array(

											'id_branch'              => $addData['id_branch'],

											'bill_id'                => $insId,

											'code'                   => $code,

											'weight'                 => ($addData['gift_type'] == 2 || $addData['gift_type'] == 4 ? $addData['gift_voucher_amt'] : 0),

											'amount'                 => ($addData['gift_type'] != 2 && $addData['gift_type'] != 4 ? $addData['gift_voucher_amt'] : 0),

											'id_set_gift_voucher'    => $addData['id_set_gift_voucher'],

											'date_add'               => date("Y-m-d"),

											'valid_from'             => date("Y-m-d"),

											'valid_to'               => date("Y-m-d", strtotime($addData['validity_days'] . 'days')),

											'purchased_by'           => $addData['bill_cus_id'],

											'free_card'              => 1,

											'status'                 => 0,

											'type'                   => 2,

											'gift_for'               => 2,  //Customer

											'remark'                 => 'SALE GIFT ISSUED',  //Customer

											'emp_created'            => $this->session->userdata('uid'),

										);

										$this->$model->insertData($gift_card_data, 'gift_card');

										//print_r($this->db->last_query());exit;

									}

									//Gift voucher details

									//Pan Images

									$p_ImgData = json_decode($addData['pan_img']);

									if (sizeof($p_ImgData) > 0) {

										foreach ($p_ImgData as $precious) {

											$imgFile = $this->base64ToFile($precious->src);

											$_FILES['pan_img'][] = $imgFile;
										}
									}

									if (isset($_FILES['pan_img'])) {

										$pan_imgs       = "";

										$folder         =  self::IMG_PATH . "billing/" . $bill_no;

										$cus_pan_folder =  self::CUS_IMG_PATH . '/' . $addData['bill_cus_id'];

										if (!is_dir($folder)) {

											mkdir($folder, 0777, TRUE);
										}

										if (!is_dir($cus_pan_folder)) {

											mkdir($cus_pan_folder, 0777, TRUE);
										}

										foreach ($_FILES['pan_img'] as $file_key => $file_val) {

											if ($file_val['tmp_name']) {

												// unlink($folder."/".$product['image']);

												$img_name       =  "P_" . mt_rand(120, 1230) . ".jpg";

												$cus_pan_path   =   $cus_pan_folder . "/" . 'pan.jpg';

												$path           =   $folder . "/" . $img_name;

												$result = $this->upload_img('image', $path, $file_val['tmp_name']);

												$this->upload_img('image', $cus_pan_path, $file_val['tmp_name']);

												if ($result) {

													$pan_imgs = strlen($pan_imgs) > 0 ? $pan_imgs . "#" . $img_name : $img_name;
												}
											}
										}

										$this->$model->updateData(array('pan_image' => $pan_imgs), 'bill_id', $insId, 'ret_billing');

										$this->$model->updateData(array('pan_proof' => $pan_imgs), 'id_customer', $addData['bill_cus_id'], 'customer');
									}

									// Return Bill

									if ($addData['bill_type'] == 3 || $addData['bill_type'] == 7) {

										//Update Ref No

										$ref_no = $this->$model->generateRefNo($addData['id_branch'], 's_ret_refno', $addData['metal_type'], $addData['is_eda']);

										// $this->$model->updateData(array('s_ret_refno' => $ref_no), 'bill_id', $insId, 'ret_billing');

										$ret_round_off_amt = $_POST['sales_return'][0]['return_round_off_amt'];

										$this->$model->updateData(array('s_ret_refno' => $ref_no, 'return_round_off_amt' => $ret_round_off_amt ), 'bill_id', $insId, 'ret_billing');

										foreach ($_POST['sales_return'] as $return_detail) {

											// Update Bill Return status

											$this->$model->updateData(array("return_status" => 1), 'bill_id', $ref_bill_id, 'ret_billing');

											$updBillDetail = array(

												"status" 				=> 2,

												"current_branch"        => $addData['id_branch'],

												"sales_return_discount" => ($return_detail['sale_ret_disc_amt'] != '' ? $return_detail['sale_ret_disc_amt'] : NULL),

												"return_item_cost" 		=> $return_detail['sale_ret_amt']

											);

											$this->$model->updateData($updBillDetail, 'bill_det_id', $return_detail['bill_det_id'], 'ret_bill_details');

											//Update return bill details

											$upd_ret_data = array(

												'bill_id'           => $insId,

												'ret_bill_id'       => $return_detail['bill_id'],

												'ret_bill_det_id'   => $return_detail['bill_det_id'],

												'ret_cash_paid'     => $return_detail['return_cash_paid'],

											);

											$this->$model->insertData($upd_ret_data, 'ret_bill_return_details');

											// Reverse Esti Status for Returned item Bill

											$updEstiRet = array(

												"purchase_status" 	=> 2, // 1-Purchased,2-Returned

												"bil_detail_id" 	=> $ref_bill_id

											);
											// echo "<pre>";print_r($return_detail['est_itm_id']);exit;
											if($return_detail['est_itm_id']!='null' && $return_detail['est_itm_id']!=""){
												$returnBillStatus = $this->$model->updateData($updEstiRet, 'est_item_id', $return_detail['est_itm_id'], 'ret_estimation_items');
											}

											// Reverse Tag Status for Returned item Bill

											$this->$model->updateData(array("tag_status" => 6), 'tag_id', $return_detail['tag'], 'ret_taging');

											//gift voucher

											$this->$model->get_gift_issue_details($return_detail['bill_id']); //Issued Voucher Cancel

											//Wallet Debit Transcation

											if ($return_detail['tag'] != '') {

												$tag_Details = $this->$model->getWalletTransTagDetails($return_detail['tag']);

												if ($tag_Details['ref_no'] != '') {

													$WalletinsData = array(

														'id_wallet_account' => $tag_Details['id_wallet_account'],

														'transaction_type' => 1,

														'type'             => 1,

														'bill_id'          => $insId,

														'ref_no'           => $tag_Details['ref_no'],

														'value'            => $tag_Details['value'],

														'id_employee'      => $this->session->userdata('uid'),

														'description'      => 'Green Tag Sales Incentive Debit',

														'date_transaction' => date("Y-m-d H:i:s"),

														'date_add'	       => date("Y-m-d H:i:s"),

													);

													$this->$model->insertData($WalletinsData, 'wallet_transaction');
												}
											}

											//Wallet Transcation Debit

											//Insert into Purchase Item Log Table

											$salesReturnLog = array(

												'bill_id'     => $insId,

												'tag_id'      => $return_detail['tag'],

												'from_branch' => NULL,

												'to_branch'   => $addData['id_branch'],

												'status'      => 1, //Inward

												'item_type'   => 2, // Sales Return

												'date'        => $bill_date,

												'created_on'  => date("Y-m-d H:i:s"),

												'created_by'  => $this->session->userdata('uid'),

											);

											$this->$model->insertData($salesReturnLog, 'ret_purchase_items_log');
										}
									}

									//Amount Advance

									if ($addData['bill_type'] == 5 && $addData['tot_amt_received']) {

										$metal_rate = $this->$model->get_branchwise_rate($addData['id_branch']);

										$store_as = (isset($addData['sale_store_as']) ? $addData['sale_store_as'] : 1);

										$arrayAdv = array(

											'bill_id'           => $insId,

											'advance_weight'    => ($store_as == 2 ? ($addData['tot_amt_received'] / $addData['goldrate_22ct']) : 0),

											'advance_amount'    => $addData['tot_amt_received'],

											'advance_type'      => 1,

											'rate_per_gram'     => $addData['goldrate_22ct'],

											'received_amount'   => $addData['tot_amt_received'],

											'store_as'          => $store_as,

											'rate_calc'         => (isset($addData['rate_calc']) ? $addData['rate_calc'] : NULL),

											'order_no'          => (!empty($addData['filter_order_no']) ? $addData['filter_order_no'] : NULL),

											'id_customerorder'  => $addData['id_customerorder'],

											'advance_date'		=> date("Y-m-d H:i:s"),

											'created_time'		=> date("Y-m-d H:i:s"),

											'created_by'    	=> $this->session->userdata('uid')

										);

										//echo"<pre>"; print_r($arrayAdv);exit;

										$advInsId = $this->$model->insertData($arrayAdv, 'ret_billing_advance');

										if ($advInsId) {

											$service = $this->$set_model->get_service_by_code('CUS_ORD');

											if ($service['serv_whatsapp'] == 1) {

												$sms_data = $this->admin_usersms_model->Get_service_code_sms('CUS_ORD', $addData['id_customerorder'], '');

												if ($sms_data['mobile'] != '') {

													$whatsapp = $this->admin_usersms_model->send_whatsApp_message($sms_data['mobile'], $sms_data['message']);
												}
											}
										}

										//Update Ref No

										$ref_no = $this->$model->generateRefNo($addData['id_branch'], 'order_adv_ref_no', '', $addData['is_eda']);

										$this->$model->updateData(array('order_adv_ref_no' => $ref_no), 'bill_id', $insId, 'ret_billing');

										//Update Ref No

									}									

									if ($addData['make_as_advance'] == 1) {

										if ($addData['bill_type'] != 0 && $addData['make_as_advance'] != '') {

											if ($addData['bill_type'] == 4 && $addData['make_as_advance'] == 1) {

												$deposit_type = 1;
											}

											if ($addData['bill_type'] == 7 && $addData['make_as_advance'] == 1) {

												$deposit_type = 2;
											}
										}

										$bill_no = $this->$model->bill_no_generate($addData['id_branch'], $addData['is_eda']);

										$bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

										$wallet = $this->$model->get_retWallet_details($addData['bill_cus_id']);



										$metal_rate = $this->$model->get_branchwise_rate($addData['id_branch']);

										if ($wallet['status']) {

											$weight = 0;

											$updStatus = $this->$model->updateWalletData(array('amount' => $addData['advance_deposit'], 'weight' => $weight, 'id_customer' => $addData['bill_cus_id']), '+');

											if ($updStatus) {

												$insWallet = array(

													'id_ret_wallet'		=> $wallet['id_ret_wallet'],

													'deposit_bill_id'	=> $insId,

													'amount'			=> $addData['advance_deposit'],

													'transaction_type'	=> 0,

													'created_by' 		=> $this->session->userdata('uid'),

													'created_on' 		=> date("Y-m-d H:i:s"),

													'remarks'	 		=> 'Billing Advace Deposit Amount'

												);

												$this->$model->insertData($insWallet, 'ret_wallet_transcation');
											}

											if ($addData['store_as'] == 1) { //store as weight

												foreach ($billPurchase['net'] as $key => $pur_item) {

													$bill_no = $this->$model->bill_no_generate($addData['id_branch'], $addData['is_eda']);



													$advance = array(

														'fin_year_code' => $fin_year['fin_year_code'],



														'bill_no'	    => $bill_no,



														'deposit_bill_id' => $insId,



														'bill_date'     => $bill_date,



														'type'          => 2,



														'is_eda'        => $addData['is_eda'],



														'id_branch'     => $addData['id_branch'],



														// 'amount'        => $addData['advance_deposit'],

														'weight'			=> $billPurchase['net'][$key],

														'receipt_metal'	=>	$billPurchase['metal_type'][$key], // 1 - gold, 2 - silver

														'receipt_as'		=>	2, //receipt as weight

														'store_receipt_as'	=>	2,

														'rate_calc'			=>	$billPurchase['metal_type'][$key],



														'receipt_type'    => 3, // sale return advance



														'id_customer'   => ($addData['bill_cus_id'] != '' ? $addData['bill_cus_id'] : NULL),



														'id_employee'   => $this->session->userdata('uid'),



														'id_acc_head'   => ($addData['id_acc_head'] != '' ? $addData['id_acc_head'] : NULL),



														'narration'	    => ($addData['narration'] != '' ? $addData['narration'] : ''),



														'deposit_type'  => $deposit_type,



														'created_by'    => $this->session->userdata('uid'),



														'counter_id'    => ($this->session->userdata('counter_id') != '' ? $this->session->userdata('counter_id') : NULL),



														'created_on'    => date("Y-m-d H:i:s"),



													);

													// echo "<pre>";print_r($advance);exit;

													$this->$model->insertData($advance, 'ret_issue_receipt');
												}
											} else { //store as amount

												$bill_no = $this->$model->bill_no_generate($addData['id_branch'], $addData['is_eda']);

												$advance = array(

													'fin_year_code' => $fin_year['fin_year_code'],



													'bill_no'	    => $bill_no,



													'deposit_bill_id' => $insId,



													'bill_date'     => $bill_date,



													'type'          => 2,



													'is_eda'        => $addData['is_eda'],



													'id_branch'     => $addData['id_branch'],



													'amount'        => $addData['advance_deposit'],



													'receipt_type'    => 3, // sale return advance



													'id_customer'   => ($addData['bill_cus_id'] != '' ? $addData['bill_cus_id'] : NULL),



													'id_employee'   => $this->session->userdata('uid'),



													'id_acc_head'   => ($addData['id_acc_head'] != '' ? $addData['id_acc_head'] : NULL),



													'narration'	    => ($addData['narration'] != '' ? $addData['narration'] : ''),



													'deposit_type'  => $deposit_type,



													'created_by'    => $this->session->userdata('uid'),



													'counter_id'    => ($this->session->userdata('counter_id') != '' ? $this->session->userdata('counter_id') : NULL),



													'created_on'    => date("Y-m-d H:i:s"),



												);



												$this->$model->insertData($advance, 'ret_issue_receipt');
											}
										} else {

											$wallet_acc = array(

												'id_customer'   => $addData['bill_cus_id'],

												'amount'        => $addData['advance_deposit'],

												'created_by'    => $this->session->userdata('uid'),

												'created_time'  => date("Y-m-d H:i:s")

											);

											$insWalletAcc = $this->$model->insertData($wallet_acc, 'ret_wallet');

											if ($insWalletAcc) {

												$insWallet = array(

													'id_ret_wallet'	    => $insWalletAcc,

													'deposit_bill_id'	=> $insId,

													'amount'			=> $addData['advance_deposit'],

													'transaction_type'	=> 0,

													'created_by' 		=> $this->session->userdata('uid'),

													'created_on' 		=> date("Y-m-d H:i:s"),

													'remarks'	 		=> 'Billing Advace Deposit Amount'

												);

												$this->$model->insertData($insWallet, 'ret_wallet_transcation');
											}

											if ($addData['bill_type'] == 4 && $addData['make_as_advance'] == 1) {

												$deposit_type = 1;
											}

											if ($addData['bill_type'] == 7 && $addData['make_as_advance'] == 1) {

												$deposit_type = 2;
											}

											if ($addData['store_as'] == 1) { //store as weight

												foreach ($billPurchase['net'] as $key => $pur_item) {

													$bill_no = $this->$model->bill_no_generate($addData['id_branch'], $addData['is_eda']);

													$advance = array(

														'fin_year_code' => $fin_year['fin_year_code'],



														'bill_no'	    => $bill_no,



														'deposit_bill_id' => $insId,



														'bill_date'     => $bill_date,



														'type'          => 2,



														'is_eda'        => $addData['is_eda'],



														'id_branch'     => $addData['id_branch'],



														// 'amount'        => $addData['advance_deposit'],

														'weight'			=> $billPurchase['net'][$key],

														'receipt_metal'		=>	$billPurchase['metal_type'][$key], // 1 - gold, 2 - silver

														'receipt_as'		=>	2, //stored as weight

														'store_receipt_as'	=>	2,

														'rate_calc'			=>	$billPurchase['metal_type'][$key],



														'receipt_type'    => 3, // sale return advance



														'id_customer'   => ($addData['bill_cus_id'] != '' ? $addData['bill_cus_id'] : NULL),



														'id_employee'   => $this->session->userdata('uid'),



														'id_acc_head'   => ($addData['id_acc_head'] != '' ? $addData['id_acc_head'] : NULL),



														'narration'	    => ($addData['narration'] != '' ? $addData['narration'] : ''),



														'deposit_type'  => $deposit_type,



														'created_by'    => $this->session->userdata('uid'),



														'counter_id'    => ($this->session->userdata('counter_id') != '' ? $this->session->userdata('counter_id') : NULL),



														'created_on'    => date("Y-m-d H:i:s"),



													);

													$this->$model->insertData($advance, 'ret_issue_receipt');
												}
											} else { //store as amount

												$advance = array(

													'fin_year_code' => $fin_year['fin_year_code'],



													'bill_no'	    => $bill_no,



													'bill_date'     => $bill_date,



													'is_eda'        => $addData['is_eda'],



													'deposit_bill_id' => $insId,



													'type'          => 2,



													'id_branch'     => $addData['id_branch'],



													'amount'        => $addData['advance_deposit'],



													'receipt_type'    => 3, // sale return advance



													'deposit_type'  => $deposit_type,



													'id_customer'   => ($addData['bill_cus_id'] != '' ? $addData['bill_cus_id'] : NULL),



													'id_employee'   => $this->session->userdata('uid'),



													'id_acc_head'   => ($addData['id_acc_head'] != '' ? $addData['id_acc_head'] : NULL),



													'narration'	    => ($addData['narration'] != '' ? $addData['narration'] : ''),



													'created_by'    => $this->session->userdata('uid'),



													'counter_id'    => ($this->session->userdata('counter_id') != '' ? $this->session->userdata('counter_id') : NULL),



													'created_on'    => date("Y-m-d H:i:s"),



												);



												$this->$model->insertData($advance, 'ret_issue_receipt');
											}
										}
									}

									if ($addData['benifits'] > 0) {

										$arrayCashPay = array(

											'bill_id'           => $insId,

											'payment_amount'    => '-' . $addData['benifits'],

											'payment_mode'      => 'Cash',

											'type'              => 2,

											'payment_for'	    => ($addData['bill_type'] == 6 ? 2 : ($addData['pay_to_cus'] > 0 ? 3 : 1)),

											'payment_status'    => 1,

											'payment_date'		=> date("Y-m-d H:i:s"),

											'created_time'	    => date("Y-m-d H:i:s"),

											'created_by'	    => $this->session->userdata('uid')

										);

										if (!empty($arrayCashPay)) {

											$cashPayInsert = $this->$model->insertData($arrayCashPay, 'ret_billing_payment');
										}
									}

									if ($addData['cash_payment'] > 0) {

										$arrayCashPay = array(

											'bill_id'           => $insId,

											'payment_amount'    => ($addData['pay_to_cus'] > 0 ? '-' . $addData['cash_payment'] : $addData['cash_payment']),

											'payment_mode'      => 'Cash',

											'type'              => ($addData['pay_to_cus'] > 0 ? 2 : ($addData['pay_to_cus'] > 0 ? 3 : 1)),

											'payment_for'	    => ($addData['bill_type'] == 6 ? 2 : ($addData['pay_to_cus'] > 0 ? 3 : 1)),

											'payment_status'    => 1,

											'payment_date'		=> date("Y-m-d H:i:s"),

											'created_time'	    => date("Y-m-d H:i:s"),

											'created_by'	    => $this->session->userdata('uid')

										);

										if (!empty($arrayCashPay)) {

											$cashPayInsert = $this->$model->insertData($arrayCashPay, 'ret_billing_payment');
										}
									}

									//chit Payment

									if (sizeof($chit_details) > 0) {

										foreach ($chit_details as $chit_uti) {

											$arrayChit[] = array(

												'bill_id'                   => $insId,

												'scheme_account_id'         => $chit_uti['scheme_account_id'],

												'utilized_amt'              => $chit_uti['utl_amount'],

												'closing_weight'            => ($chit_uti['closing_weight'] != '' ? $chit_uti['closing_weight'] : 0),

												'wastage_per'               => ($chit_uti['wastage_per'] != '' ? $chit_uti['wastage_per'] : 0),

												'savings_in_wastage'        => ($chit_uti['savings_in_wastage'] != '' ? $chit_uti['savings_in_wastage'] : 0),

												'mc_value'                  => ($chit_uti['mc_value'] != '' ? $chit_uti['mc_value'] : 0),

												'savings_in_making_charge'  => ($chit_uti['savings_in_making_charge'] != '' ? $chit_uti['savings_in_making_charge'] : 0),

												'rate_per_gram'  => ($chit_uti['rate_per_gram'] != '' ? $chit_uti['rate_per_gram'] : 0),

											);
										}

										if (!empty($arrayChit)) {

											$chitInsert = $this->$model->insertBatchData($arrayChit, 'ret_billing_chit_utilization');

											if ($chitInsert) {

												foreach ($chit_details as $chit_uti) {

													$updData = array('is_utilized' => 1, 'utilized_type' => ($addData['bill_type'] == 10 ? 1 : 2));

													$updID = $this->$model->updateData($updData, 'id_scheme_account', $chit_uti['scheme_account_id'], 'scheme_account');
												}
											}
										}
									}

									//Gift Voucher

									if (sizeof($voucher_details) > 0) {

										foreach ($voucher_details as $voucher) {

											$arrayVoucher = array('voucher_no' => $voucher['id_gift_card'], 'bill_id' => $insId, 'gift_voucher_amt' => $voucher['gift_voucher_amt']);

											if (!empty($arrayVoucher)) {

												$voucerPayInsert = $this->$model->insertData($arrayVoucher, 'ret_billing_gift_voucher_details');

												if ($voucerPayInsert) {

													$giftUti = array(

														'adjusted_bill_id'  => $voucerPayInsert,

														'redeemed_by'       => $addData['bill_cus_id'],

														'redeemed_on'       => date("Y-m-d H:i:s"),

														'redeem_type'       => 1,

														'status'            => 2,

													);

													$this->$model->updateData($giftUti, 'id_gift_card', $voucher['id_gift_card'], 'gift_card');
												}

												//print_r($this->db->last_query());exit;

											}
										}
									}

									if (sizeof($card_pay_details) > 0) {

										foreach ($card_pay_details as $card_pay) {

											$arrayCardPay[] = array(

												'bill_id'		=> $insId,

												'card_type'     => $card_pay['card_name'],

												'payment_amount' => $card_pay['card_amt'],

												'id_pay_device' => ($card_pay['id_device'] != '' ? $card_pay['id_device'] : NULL),

												'payment_for'	=> ($addData['bill_type'] == 6 ? 2 : 1),

												'payment_status' => 1,

												'payment_date'		=> date("Y-m-d H:i:s"),

												'payment_mode'	=> ($card_pay['card_type'] == 1 ? 'CC' : 'DC'),

												'card_no'		=> ($card_pay['card_no'] != '' ? $card_pay['card_no'] : NULL),

												'payment_ref_number' => ($card_pay['ref_no'] != '' ? $card_pay['ref_no'] : NULL),

												'card_date' => (isset($card_pay['card_date']) && $card_pay['card_date']!='' ? implode('-', array_reverse(explode('-', $card_pay['card_date']))) : NULL),

												'created_time'	=> date("Y-m-d H:i:s"),

												'created_by'	=> $this->session->userdata('uid')

											);
										}

										if (!empty($arrayCardPay)) {

											$cardPayInsert = $this->$model->insertBatchData($arrayCardPay, 'ret_billing_payment');
										}

										// POS FK Linkage: Link POS transactions to saved bill
										// When POS pay was on a NEW bill, pos_req_bill_id was 0/NULL
										// Now that bill is saved ($insId), update ret_pos_requests
										// SAFE: Skip if POS module is disabled or tables don't exist
										if ($this->_isPOSModuleEnabled()) {
										$cusId = isset($addData['bill_cus_id']) ? intval($addData['bill_cus_id']) : 0;
										if ($cusId > 0) {
											foreach ($card_pay_details as $cp) {
												// Prefer pos_ref_id (original POS txn ref) over ref_no (may be overwritten to UTR/approval code)
												$posRefId = isset($cp['pos_ref_id']) ? trim($cp['pos_ref_id']) : '';
												$refNo = isset($cp['ref_no']) ? trim($cp['ref_no']) : '';
												$lookupRef = !empty($posRefId) ? $posRefId : $refNo;
												if (!empty($lookupRef)) {
													$posTxn = $this->db->query(
														"SELECT pos_req_id FROM ret_pos_requests WHERE pos_res_ref_id = ? AND pos_req_bill_cusid = ? AND pos_req_status = 1 ORDER BY pos_req_id DESC LIMIT 1",
														array($lookupRef, $cusId)
													)->row_array();
													if ($posTxn) {
														$this->db->where('pos_req_id', $posTxn['pos_req_id']);
														$this->db->update('ret_pos_requests', array('pos_req_bill_id' => $insId));
														// Also update ret_billing_payment with pos_req_id (reverse FK)
														$this->db->where('bill_id', $insId);
														$this->db->where('payment_ref_number', $refNo);
														$this->db->update('ret_billing_payment', array('pos_req_id' => $posTxn['pos_req_id']));
														log_message('info', 'POS FK Linkage: Card bill_id='.$insId.' linked to pos_req_id='.$posTxn['pos_req_id'].' via ref='.$lookupRef);
													}
												}
											}
										}
										} // end POS module check
									}

									if (sizeof($cheque_details) > 0) {

										foreach ($cheque_details as $chq_pay) {

											$cheque_deposit_date = ($chq_pay['cheque_date'] != '' ? implode('-', array_reverse(explode('-', $chq_pay['cheque_date']))):date("Y-m-d"));

											// $cheque_date = ($chq_pay['cheque_date'] != '' ? date_format($cheque_deposit_date, "Y-m-d") : NULL);

											$arraychqPay[] = array(

												'bill_id'		=> $insId,

												'payment_amount' => ($addData['pay_to_cus'] > 0 ? '-' . $chq_pay['payment_amount'] : $chq_pay['payment_amount']),

												'payment_for'	=> ($addData['bill_type'] == 6 ? 2 : 1),

												'type'          => ($addData['pay_to_cus'] > 0 ? 2 : 1),

												'payment_status' => 1,

												'payment_date'		=> date("Y-m-d H:i:s"),

												'cheque_date'		=> ($chq_pay['cheque_date'] != '' ? $cheque_deposit_date : NULL),

												'payment_mode'	=> 'CHQ',

												'cheque_no'		=> ($chq_pay['cheque_no'] != '' ? $chq_pay['cheque_no'] : NULL),

												'bank_name'		=> ($chq_pay['bank_name'] != '' ? $chq_pay['bank_name'] : NULL),

												'bank_branch'	=> ($chq_pay['bank_branch'] != '' ? $chq_pay['bank_branch'] : NULL),

												'id_bank'	    => ($chq_pay['id_bank'] != '' ? $chq_pay['id_bank'] : NULL),

												'created_time'	=> date("Y-m-d H:i:s"),

												'created_by'	=> $this->session->userdata('uid')

											);
										}

										if (!empty($arraychqPay)) {

											$chqPayInsert = $this->$model->insertBatchData($arraychqPay, 'ret_billing_payment');
										}
									}

									if (sizeof($net_banking_details) > 0) {

										foreach ($net_banking_details as $nb_pay) {

											$arrayNBPay[] = array(

												'bill_id'		    => $insId,

												'payment_amount'    => ($addData['pay_to_cus'] > 0 ? '-' . $nb_pay['amount'] : $nb_pay['amount']),

												'payment_for'	    => ($addData['bill_type'] == 6 ? 2 : ($addData['pay_to_cus'] > 0 ? 3 : 1)),

												'id_pay_device'     => ($nb_pay['id_device'] != '' ? $nb_pay['id_device'] : NULL),

												'payment_status'    => 1,

												'type'              => ($addData['pay_to_cus'] > 0 ? 2 : 1),

												'payment_date'		=> date("Y-m-d H:i:s"),

												'payment_mode'	    => 'NB',

												'id_bank'           => ($nb_pay['id_bank'] != '' ? $nb_pay['id_bank'] : NULL),

												'payment_ref_number' => ($nb_pay['ref_no'] != '' ? $nb_pay['ref_no'] : NULL),

												'NB_type'           => ($nb_pay['nb_type'] != '' ? $nb_pay['nb_type'] : NULL),

												'net_banking_date'  =>($nb_pay['nb_date']!='' ? implode('-', array_reverse(explode('-', $nb_pay['nb_date']))):date("Y-m-d")),

												'created_time'	    => date("Y-m-d H:i:s"),

												'created_by'	    => $this->session->userdata('uid')

											);
										}

										if (!empty($arrayNBPay)) {

											$NbPayInsert = $this->$model->insertBatchData($arrayNBPay, 'ret_billing_payment');
										}

										// POS FK Linkage for UPI/NB: Link POS transactions to saved bill
										// SAFE: Skip if POS module is disabled or tables don't exist
										if ($this->_isPOSModuleEnabled()) {
										$cusId_nb = isset($addData['bill_cus_id']) ? intval($addData['bill_cus_id']) : 0;
										if ($cusId_nb > 0) {
											foreach ($net_banking_details as $nb) {
												$nbPosRefId = isset($nb['pos_ref_id']) ? trim($nb['pos_ref_id']) : '';
												$nbRefNo = isset($nb['ref_no']) ? trim($nb['ref_no']) : '';
												$nbLookupRef = !empty($nbPosRefId) ? $nbPosRefId : $nbRefNo;
												if (!empty($nbLookupRef)) {
													$nbPosTxn = $this->db->query(
														"SELECT pos_req_id FROM ret_pos_requests WHERE pos_res_ref_id = ? AND pos_req_bill_cusid = ? AND pos_req_status = 1 ORDER BY pos_req_id DESC LIMIT 1",
														array($nbLookupRef, $cusId_nb)
													)->row_array();
													if ($nbPosTxn) {
														$this->db->where('pos_req_id', $nbPosTxn['pos_req_id']);
														$this->db->update('ret_pos_requests', array('pos_req_bill_id' => $insId));
														$this->db->where('bill_id', $insId);
														$this->db->where('payment_ref_number', $nbRefNo);
														$this->db->update('ret_billing_payment', array('pos_req_id' => $nbPosTxn['pos_req_id']));
														log_message('info', 'POS FK Linkage: NB/UPI bill_id='.$insId.' linked to pos_req_id='.$nbPosTxn['pos_req_id'].' via ref='.$nbLookupRef);
													}
												}
											}
										}
									}
									} // end POS module check for NB/UPI

									// Order advance adjustment
									// echo "<pre>";print_r($order_adv_adj_details);exit;
									
									if (sizeof($order_adv_adj_details) > 0 && $addData['bill_type'] == 9) {

										foreach ($order_adv_adj_details as $order) {

											if ($order['is_checked'] == 1) {


												$order_advance_detail = $this->$model->get_order_advance_details($order['bill_adv_id']);

												$adjusted_advance = $order_advance_detail['adjusted_advance'] + $order['adj_advance'];


												$data_adv_amount    = array(

													'bill_adv_id'  => $order['bill_adv_id'],

													'bill_id'           => $insId,

													'utilized_amt'      => $order['adj_advance'],

													'adv_utilized_type' => 2

												);

												$insId_adv_amount = $this->$model->insertData($data_adv_amount, 'ret_advance_utilized');


												if ($adjusted_advance == $order_advance_detail['paid_advance']) {



													$this->$model->updateData(array('adjusted_amount' => $adjusted_advance, 'is_adavnce_adjusted' => 1, 'adjusted_bill_id' => $insId, 'updated_time'	=> date("Y-m-d H:i:s"), 'updated_by'	=> $this->session->userdata('uid')), 'bill_adv_id', $order['bill_adv_id'], 'ret_billing_advance');
												} else {


													$this->$model->updateData(array('adjusted_amount' => $adjusted_advance, 'updated_time'	=> date("Y-m-d H:i:s"), 'updated_by'	=> $this->session->userdata('uid')), 'bill_adv_id', $order['bill_adv_id'], 'ret_billing_advance');
												}
											}
										}
									}

									//Advance Adjusement

									$advan_amout = $_POST['adv']['advance_muliple_receipt'][0];

									if (count($advan_amout) > 0) {

										$advance_amount_adj = json_decode($advan_amout);

										$advance_amt = 0;

										foreach ($advance_amount_adj as $obj) {

											$advance_amt += $obj->adj_amount;

											$id_ret_wallet      = $obj->id_ret_wallet;

											$data_adv_amount    = array(

												'id_issue_receipt'  => $obj->id_issue_receipt,

												'bill_id'           => $insId,

												'utilized_amt'      => $obj->adj_amount,



												'adj_weight'		=> $obj->advance_weight,

												'rate_per_gram'		=> $obj->rate_per_gram,

												'cash_utilized_amt' => $obj->cash_pay,

											);

											$insId_adv_amount = $this->$model->insertData($data_adv_amount, 'ret_advance_utilized');

											if ($obj->refund_amount > 0) {

												$issue_bill_no = $this->$model->bill_no_generate($addData['id_branch'], $addData['is_eda']);

												$IssueData = array(

													'fin_year_code' => $fin_year['fin_year_code'],

													'bill_no'	    => $issue_bill_no,

													'bill_date'     => $bill_date,

													'is_eda'        => $addData['is_eda'],

													'type'          => 1, // Issue

													'id_branch'     => $addData['id_branch'],

													'amount'        => $obj->refund_amount,

													'issue_to'      => 2, // customer

													'issue_type'    => 3, // advance refund

													'id_customer'   => $addData['bill_cus_id'],

													'id_employee'   => $this->session->userdata('uid'),

													'narration'	    => 'Advance Amount Refund From Billing',

													'created_by'    => $this->session->userdata('uid'),

													'counter_id'    => ($this->session->userdata('counter_id') != '' ? $this->session->userdata('counter_id') : NULL),

													'refno'         => NULL,

													'ref_bill_id'   => $insId,

													'created_on'    => date("Y-m-d H:i:s"),

												);

												$IssueinsId = $this->$model->insertData($IssueData, 'ret_issue_receipt');

												if ($IssueinsId) {

													$refundData = array(

														'id_issue_receipt' => $IssueinsId,

														'refund_receipt'   => $obj->id_issue_receipt,

														'refund_amount'    => $obj->refund_amount,

													);

													$this->$model->insertData($refundData, 'ret_advance_refund');

													$pay_data = array(

														'id_issue_rcpt'	=> $IssueinsId,

														'payment_amount' => $obj->refund_amount,

														'payment_mode'	=> ($obj->pay_mode == 'CSH' ? 'Cash' : ($obj->pay_mode == 'CHQ' ? 'CHQ' : '')),

														'payment_status' => 1,

														'type'			=> 2,

														'payment_type'	=> 'Manual',

														'payment_date'	=> date("Y-m-d H:i:s"),

														'created_time'	=> date("Y-m-d H:i:s"),

														'created_by'	=> $this->session->userdata('uid')

													);

													$this->$model->insertData($pay_data, 'ret_issue_rcpt_payment');
												}
											}
										}

										if ($insId_adv_amount) {

											$this->$model->updateWalletData(array('amount' => $advance_amt, 'weight' => 0, 'id_customer' => $addData['bill_cus_id']), '-');
										}
									}

									//Advance Adjusement

									//SUPPLIER BILL SALES

									if (!empty($supplierMetalDetilas)) {

										$arrayBillDetails = array();

										$ref_no = $this->$model->generateRefNo($addData['id_branch'], 'sales_ref_no', $addData['metal_type'], $addData['is_eda']);

										$this->$model->updateData(array('sales_ref_no' => $ref_no), 'bill_id', $insId, 'ret_billing');

										foreach ($supplierMetalDetilas['id_product'] as $key => $val) {

											$purity_details = $this->$model->get_purity_details($supplierMetalDetilas['purity'][$key]);

											$arrayBillDetails = array(

												'bill_id' => $insId,

												'product_id' 	=> ($supplierMetalDetilas['id_product'][$key] != '' ? $supplierMetalDetilas['id_product'][$key] : NULL),

												'design_id' 	=> ($supplierMetalDetilas['id_design'][$key] != '' ? $supplierMetalDetilas['id_design'][$key] : NULL),

												'id_sub_design' => ($supplierMetalDetilas['id_sub_design'][$key] != '' ? $supplierMetalDetilas['id_sub_design'][$key] : NULL),

												'purity' 	    => $purity_details['id_purity'],

												'piece' 		=> ($supplierMetalDetilas['pcs'][$key] != '' ? $supplierMetalDetilas['pcs'][$key] : NULL),

												'gross_wt' 		=> $supplierMetalDetilas['weight'][$key], //gross wt

												'net_wt' 		=> $supplierMetalDetilas['weight'][$key], //gross wt

												'rate_per_grm' 	=> $supplierMetalDetilas['rate_per_gram'][$key],

												'total_cgst' 	=> $supplierMetalDetilas['item_total_cgst'][$key],

												'total_sgst' 	=> $supplierMetalDetilas['item_total_sgst'][$key],

												'total_igst' 	=> $supplierMetalDetilas['item_total_igst'][$key],

												'item_total_tax' => $supplierMetalDetilas['item_total_tax'][$key],

												'item_cost' 	=> $supplierMetalDetilas['item_cost'][$key],

												'is_non_tag'    => 1,

											);

											$billDetailStatus = $this->$model->insertData($arrayBillDetails, 'ret_bill_details');

											//UPDATE INTO PURCHASE ITEM STOCK SUMMARY

											$existData = array('id_product' => $supplierMetalDetilas['id_product'][$key], 'id_sub_design' => $supplierMetalDetilas['id_sub_design'][$key], 'design' => $supplierMetalDetilas['id_design'][$key], 'id_branch' => $addData['id_branch']);

											$isExist = $this->$model->checkNonTagItemExist($existData);

											if ($isExist['status'] == TRUE) {

												$nt_data = array(

													'id_nontag_item' => $isExist['id_nontag_item'],

													'gross_wt'		=> $supplierMetalDetilas['weight'][$key],

													'net_wt'		=> $supplierMetalDetilas['weight'][$key],

													'no_of_piece'   => $supplierMetalDetilas['pcs'][$key],

													'updated_by'	=> $this->session->userdata('uid'),

													'updated_on'	=> date('Y-m-d H:i:s'),

												);

												$this->$model->updateNTData($nt_data, '-');

												$non_tag_data = array(

													'product'	    => $supplierMetalDetilas['id_product'][$key],

													'design'	    => $supplierMetalDetilas['id_design'][$key],

													'id_sub_design'	=> $supplierMetalDetilas['id_sub_design'][$key],

													'gross_wt'		=> $supplierMetalDetilas['weight'][$key],

													'net_wt'		=> $supplierMetalDetilas['weight'][$key],

													'no_of_piece'   => $supplierMetalDetilas['pcs'][$key],

													'from_branch'	=> $addData['id_branch'],

													'to_branch'	    => NULL,

													'bill_id'	    => $insId,

													'status'	    => 1,

													'date'          => $bill_date,

													'created_on'    => date("Y-m-d H:i:s"),

													'created_by'    =>  $this->session->userdata('uid')

												);

												$this->$model->insertData($non_tag_data, 'ret_nontag_item_log');
											}

											//UPDATE INTO PURCHASE ITEM STOCK SUMMARY

										}
									}

									//SUPPLIER BILL SALES

									//Sales Items

									if (!empty($billSale)) {

										$arrayBillSales = array();

										//Update Ref No

										if ($addData['bill_type'] == 15) { // For Approval Bill

											$ref_no =   $this->$model->generateRefNo($addData['id_branch'], 'approval_ref_no', $addData['metal_type'], $addData['is_eda']);

											$this->$model->updateData(array('approval_ref_no' => $ref_no), 'bill_id', $insId, 'ret_billing');
										} else {

											$ref_no =   $this->$model->generateRefNo($addData['id_branch'], 'sales_ref_no', $addData['metal_type'], $addData['is_eda']);

											$this->$model->updateData(array('sales_ref_no' => $ref_no), 'bill_id', $insId, 'ret_billing');
										}
										$tot_ins_payable = 0;
										foreach ($billSale['is_est_details'] as $key => $val) {
											$tot_ins_payable+=$billSale['billamount'][$key];
											$arrayBillSales = array(

												'bill_id' => $insId,

												'esti_item_id'  => (isset($billSale['est_itm_id'][$key]) ? ($billSale['est_itm_id'][$key] != '' ? $billSale['est_itm_id'][$key] : NULL) : NULL),

												'item_type' 	=> ($billSale['itemtype'][$key] != '' ? $billSale['itemtype'][$key] : NULL),

												'bill_type' 	=> $billSale['is_est_details'][$key],

												'total_cgst' 	=> $billSale['total_cgst'][$key],

												'total_sgst' 	=> $billSale['total_sgst'][$key],

												'total_igst' 	=> $billSale['total_igst'][$key],

												'id_section' 	=> ($billSale['id_section'][$key] != '' ? $billSale['id_section'][$key] : NULL),

												'product_id' 	=> ($billSale['product'][$key] != '' ? $billSale['product'][$key] : NULL),

												'design_id' 	=> ($billSale['design'][$key] != '' ? $billSale['design'][$key] : NULL),

												'id_sub_design' => ($billSale['id_sub_design'][$key] != '' ? $billSale['id_sub_design'][$key] : NULL),

												'tag_id'		=> ($billSale['tag'][$key] != '' ? $billSale['tag'][$key] : NULL),

												'quantity' 		=> 1,

												'purity' 		=> ($billSale['purity'][$key] != '' ? $billSale['purity'][$key] : NULL),

												'size' 			=> ($billSale['size'][$key] != '' ? $billSale['size'][$key] : NULL),

												'uom' 			=> ($billSale['uom'][$key] != '' ?  $billSale['uom'][$key] : NULL),

												'piece' 		=> ($billSale['pcs'][$key] != '' ? $billSale['pcs'][$key] : NULL),

												'less_wt' 		=> ($billSale['less'][$key] != '' ? $billSale['less'][$key] : NULL),

												'net_wt' 		=> $billSale['net'][$key],

												'gross_wt' 		=> $billSale['gross'][$key],
												
												'pure_wt' 		=> $billSale['pure_wt'][$key],

												'calculation_based_on' => $billSale['calltype'][$key],

												'wastage_percent' => ($billSale['wastage'][$key] !== '' && $billSale['wastage'][$key] !== null) ? $billSale['wastage'][$key] : 0,

												'mc_value' 		=> ($billSale['mc'][$key] != '' ? $billSale['mc'][$key] : 0),

												'mc_type' 		=> ($billSale['bill_mctype'][$key] != '' ? $billSale['bill_mctype'][$key] : NULL),

												'item_cost' 	=> $billSale['billamount'][$key],

												'item_total_tax' => $billSale['item_total_tax'][$key],

												'tax_group_id'  => $billSale['taxgroup'][$key],

												'bill_discount'  => $billSale['discount'][$key],
												
												// 'rate_per_grm'  => $billSale['pure_rate'][$key],

												'rate_per_grm' 	=> $bill_is_eda == 2 ? $billSale['pure_rate'][$key] : $billSale['per_grm'][$key],


												'is_partial_sale'   => ($billSale['is_partial'][$key] != '' ? $billSale['is_partial'][$key] : 0),

												'mc_discount'       => ($billSale['mc_discount'][$key] != '' ? $billSale['mc_discount'][$key] : 0),

												'wastage_discount'  => ($billSale['wastage_discount'][$key] != '' ? $billSale['wastage_discount'][$key] : 0),

												'item_blc_discount'  => ($billSale['item_blc_discount'][$key] != '' ? $billSale['item_blc_discount'][$key] : 0),

												'item_emp_id'  => ($billSale['item_emp_id'][$key] != '' ? $billSale['item_emp_id'][$key] : 0),

												'bill_discount_type' => $addData['bill_discount_type'],

												'id_collecion_maping_det' => ($billSale['id_collecion_maping_det'][$key] != '' ? $billSale['id_collecion_maping_det'][$key] : 0),

												'is_non_tag'    => !empty($billSale['is_non_tag'][$key]) ? $billSale['is_non_tag'][$key] : 0,

												'id_orderdetails' => (isset($billSale['id_orderdetails'][$key]) ? ($billSale['id_orderdetails'][$key] != '' ? $billSale['id_orderdetails'][$key] : NULL) : NULL),

												'is_delivered' 	=> $billSale['is_delivered'][$key],

												'show_huid'  => ($billSale['show_huid'][$key]),

												'huid'  => ($billSale['huid'][$key] ? $billSale['huid'][$key] : NULL)

											);

											if (!empty($arrayBillSales)) {

												$tagInsert = $this->$model->insertData($arrayBillSales, 'ret_bill_details');

												if ($tagInsert) {

													if ($billSale['charges_details'][$key]) {

														$charges = (array)json_decode($billSale['charges_details'][$key], true);

														// print_r($charges);exit;


														foreach ($charges as $charge) {

															$charge_data = array(

																'bill_det_id'           => $tagInsert,

																'id_charge'             => ($charge['charge_id'] != '' ? $charge['charge_id'] : 0),

																'amount'               => $charge['charge_value'],

															);

															$this->$model->insertData($charge_data, 'ret_bill_other_charges');
														}
													}


													//Sales Incentive

													if (isset($billSale['tag'][$key]) && $billSale['tag'][$key] != '' && $billSale['est_itm_id'][$key] != '') {

														$sales_incetive = $this->$model->get_ret_settings('sales_incentive_green_tag');  //Is incentive is enabled

														if ($sales_incetive == 1) {

															$tag_details = $this->$model->getTagDetails($billSale['tag'][$key], $billSale['est_itm_id'][$key]);

															if (!empty($tag_details)) {

																if ($tag_details['id_metal'] == 1)  // Gold

																{

																	$gold_per_gram_amt = $this->$model->get_ret_settings('emp_sales_incentive_gold_perg');      //GOld Per Gram Value

																	$wallet_amt = $billSale['net'][$key] * $gold_per_gram_amt;
																} else if ($tag_details['id_metal'] == 2) //Silver

																{

																	$silver_per_gram_amt = $this->$model->get_ret_settings('emp_sales_incentive_silver_perg'); //Silver Per Gram Value

																	$wallet_amt = $billSale['net'][$key] * $silver_per_gram_amt;
																}

																if ($wallet_amt > 0) {

																	$wallet_acc = $this->$model->get_wallet_account($tag_details['id_employee']); // Check Wallet Acc Exists

																	if ($wallet_acc['status']) {

																		$WalletinsData = array(

																			'id_wallet_account' => $wallet_acc['id_wallet_account'],

																			'transaction_type' => 0, //0-Credit,2-Debit

																			'type'             => 1, //Retail

																			'bill_id'          => $insId,

																			'ref_no'           => $billSale['tag'][$key],

																			'value'            => $wallet_amt,

																			'description'      => 'Green Tag Sales Incentive',

																			'date_transaction' => date("Y-m-d H:i:s"),

																			'id_employee'      => $this->session->userdata('uid'),

																			'date_add'	       => date("Y-m-d H:i:s"),

																		);

																		$this->$model->insertData($WalletinsData, 'wallet_transaction');
																	} else {

																		$wallet_acc_no =  $this->$model->get_wallet_acc_number();

																		$walletAcc = array(

																			'idemployee' 	   => $tag_details['id_employee'],

																			'id_employee' 	   => $this->session->userdata('uid'),

																			'wallet_acc_number' => $wallet_acc_no,

																			'issued_date' 	   => date('y-m-d H:i:s'),

																			'remark' 		   => "Credits",

																			'active'		   => 1

																		);

																		$id_wallet_acc = $this->$model->insertData($walletAcc, 'wallet_account');

																		// print_r($this->db->last_query());exit;

																		if ($id_wallet_acc) {

																			$WalletinsData = array(

																				'id_wallet_account' => $id_wallet_acc,

																				'transaction_type' => 0, //0-Credit,2-Debit

																				'type'             => 1, //Retail

																				'bill_id'          => $insId,

																				'ref_no'           => $billSale['tag'][$key],

																				'value'            => $wallet_amt,

																				'description'      => 'Green Tag Sales Incentive',

																				'date_transaction' => date("Y-m-d H:i:s"),

																				'id_employee'      => $this->session->userdata('uid'),

																				'date_add'	       => date("Y-m-d H:i:s"),

																			);

																			$this->$model->insertData($WalletinsData, 'wallet_transaction');
																		}
																	}
																}
															}
														}
													}

													//Sales Incentive

													//New Rule-Based Sales Incentive Engine
													if ($tagInsert) {
														$this->load->model('ret_emp_incentive_model');
														$inc_model = 'ret_emp_incentive_model';

														// Determine selling employee
														$selling_emp_id = 0;
														if (!empty($billSale['item_emp_id'][$key])) {
															$selling_emp_id = intval($billSale['item_emp_id'][$key]);
														}

														// Only proceed if we have a selling employee
														if ($selling_emp_id > 0) {
															$inc_product_id     = !empty($billSale['product'][$key]) ? intval($billSale['product'][$key]) : 0;
															$inc_design_id      = !empty($billSale['design'][$key]) ? intval($billSale['design'][$key]) : 0;
															$inc_sub_design_id  = !empty($billSale['id_sub_design'][$key]) ? intval($billSale['id_sub_design'][$key]) : 0;
															$inc_category_id    = $inc_product_id > 0 ? $this->$inc_model->get_category_for_product($inc_product_id) : 0;
															$inc_branch_id      = intval($addData['id_branch']);

															$inc_rule = $this->$inc_model->find_matching_rule(
																$inc_branch_id, $inc_category_id, $inc_product_id, $inc_design_id, $inc_sub_design_id
															);

															if (!empty($inc_rule)) {
																$inc_net_wt       = floatval($billSale['net'][$key]);
																$inc_stone_carat  = $this->$inc_model->get_stone_carat_weight($tagInsert);
																$inc_item_cost    = floatval($billSale['billamount'][$key]);

																$inc_result = $this->$inc_model->calculate_incentive($inc_rule, $inc_net_wt, $inc_stone_carat, $inc_item_cost);

																if ($inc_result['amount'] > 0) {
																	// Log to incentive log table
																	$inc_log_data = array(
																		'bill_id'          => $insId,
																		'bill_det_id'      => $tagInsert,
																		'id_employee'      => $selling_emp_id,
																		'config_id'        => $inc_rule['id'],
																		'id_branch'        => $inc_branch_id,
																		'id_category'      => $inc_category_id > 0 ? $inc_category_id : NULL,
																		'id_product'       => $inc_product_id > 0 ? $inc_product_id : NULL,
																		'id_design'        => $inc_design_id > 0 ? $inc_design_id : NULL,
																		'id_sub_design'    => $inc_sub_design_id > 0 ? $inc_sub_design_id : NULL,
																		'calc_basis'       => $inc_rule['calc_basis'],
																		'base_qty'         => $inc_result['base_qty'],
																		'incentive_rate'   => $inc_result['rate'],
																		'incentive_amount' => $inc_result['amount'],
																		'bill_date'        => $bill_date,
																		'created_on'       => date("Y-m-d H:i:s")
																	);
																	$this->$inc_model->log_incentive($inc_log_data);

																	// Credit to employee wallet
																	$inc_wallet_acc = $this->$model->get_wallet_account($selling_emp_id);

																	if ($inc_wallet_acc['status']) {
																		$inc_wallet_data = array(
																			'id_wallet_account' => $inc_wallet_acc['id_wallet_account'],
																			'transaction_type'  => 0,
																			'type'              => 1,
																			'bill_id'           => $insId,
																			'ref_no'            => !empty($billSale['tag'][$key]) ? $billSale['tag'][$key] : $tagInsert,
																			'value'             => $inc_result['amount'],
																			'description'       => 'Emp Sales Incentive (Config #' . $inc_rule['id'] . ')',
																			'date_transaction'  => date("Y-m-d H:i:s"),
																			'id_employee'       => $this->session->userdata('uid'),
																			'date_add'          => date("Y-m-d H:i:s"),
																		);
																		$this->$model->insertData($inc_wallet_data, 'wallet_transaction');
																	} else {
																		$inc_wallet_acc_no = $this->$model->get_wallet_acc_number();
																		$inc_wallet_acc_data = array(
																			'idemployee'        => $selling_emp_id,
																			'id_employee'       => $this->session->userdata('uid'),
																			'wallet_acc_number' => $inc_wallet_acc_no,
																			'issued_date'       => date('y-m-d H:i:s'),
																			'remark'            => "Credits",
																			'active'            => 1
																		);
																		$inc_new_wallet_id = $this->$model->insertData($inc_wallet_acc_data, 'wallet_account');
																		if ($inc_new_wallet_id) {
																			$inc_wallet_data = array(
																				'id_wallet_account' => $inc_new_wallet_id,
																				'transaction_type'  => 0,
																				'type'              => 1,
																				'bill_id'           => $insId,
																				'ref_no'            => !empty($billSale['tag'][$key]) ? $billSale['tag'][$key] : $tagInsert,
																				'value'             => $inc_result['amount'],
																				'description'       => 'Emp Sales Incentive (Config #' . $inc_rule['id'] . ')',
																				'date_transaction'  => date("Y-m-d H:i:s"),
																				'id_employee'       => $this->session->userdata('uid'),
																				'date_add'          => date("Y-m-d H:i:s"),
																			);
																			$this->$model->insertData($inc_wallet_data, 'wallet_transaction');
																		}
																	}
																}
															}
														}
													}
													//End New Rule-Based Sales Incentive Engine

													//Partly sale

													$status = $this->$model->get_partial_sale_det($billSale['tag'][$key]);

													if ($billSale['is_partial'][$key] == 1 || ($billSale['itemtype'][$key] == 2 && $billSale['tag'][$key] != '')) {

														$tag = $this->$model->get_tag_details($billSale['tag'][$key]);

														$blc_gross_wt = $tag['gross_wt'] - ($tag['sold_gwt'] + $billSale['gross'][$key]);

														$blc_net_wt = ($tag['net_wt'] - ($tag['sold_nwt'] + $billSale['net'][$key]));

														$partly_data = array(

															'tag_id'		=> $billSale['tag'][$key],

															'sold_bill_det_id' => $tagInsert,

															'product'		=> $billSale['product'][$key],

															'design'		=> ($billSale['design'][$key] != '' ? $billSale['design'][$key] : NULL),

															'sold_gross_wt'	=> $billSale['gross'][$key],

															'sold_less_wt'	=> ($billSale['less'][$key] != '' ? $billSale['less'][$key] : NULL),

															'sold_net_wt'	=> $billSale['net'][$key],

															'blc_gross_wt'	=> $blc_gross_wt,

															'blc_less_wt'	=> 0,

															'blc_net_wt'	=> $blc_net_wt,

															'created_on'  	=> date("Y-m-d H:i:s"),

															'created_by'   	=> $this->session->userdata('uid'),

															'status'   		=> ($blc_net_wt == 0 ? 0 : 1),

														);

														$this->$model->insertData($partly_data, 'ret_partlysold');

														//Insert into Purchase Item Log Table

														$partlySaleLog = array(

															'sold_bill_det_id' => $tagInsert,

															'tag_id'      => $billSale['tag'][$key],

															'gross_wt'    => $blc_gross_wt,

															'net_wt'      => $billSale['net'][$key],

															'from_branch' => NULL,

															'to_branch'   => $addData['id_branch'],

															'status'      => 1,

															'item_type'   => 3, // Partly Sale

															'date'        => $bill_date,

															'created_on'  => date("Y-m-d H:i:s"),

															'created_by'  => $this->session->userdata('uid'),

														);

														$this->$model->insertData($partlySaleLog, 'ret_purchase_items_log');
													}

													//Partly sale

													//stock maintaince

													if ($billSale['is_non_tag'][$key] == 1) {

														$existData = array('id_section' => $billSale['id_section'][$key], 'id_product' => $billSale['product'][$key], 'id_design' => $billSale['design'][$key], 'id_sub_design' => $billSale['id_sub_design'][$key], 'id_branch' => $addData['id_branch']);

														$isExist = $this->$model->checkNonTagItemExist($existData);

														if ($isExist['status'] == TRUE) {

															$nt_data = array(

																'id_nontag_item' => $isExist['id_nontag_item'],

																'no_of_piece'	=> ($billSale['pcs'][$key] != '' ? $billSale['pcs'][$key] : 0),

																'gross_wt'		=> ($billSale['gross'][$key] > 0 ? $billSale['gross'][$key] : 0),

																'net_wt'		=> $billSale['net'][$key],

																'less_wt'		=> $billSale['less'][$key],

																'updated_by'	=> $this->session->userdata('uid'),

																'updated_on'	=> date('Y-m-d H:i:s'),

															);

															$this->$model->updateNTData($nt_data, '-');

															$non_tag_data = array(

																'from_branch'	=> $addData['id_branch'],

																'to_branch'	    => NULL,

																'no_of_piece'   => ($billSale['pcs'][$key] != '' ? $billSale['pcs'][$key] : NULL),

																'less_wt' 		=> ($billSale['less'][$key] != '' ? $billSale['less'][$key] : NULL),

																'net_wt' 		=> $billSale['net'][$key],

																'gross_wt' 		=> ($billSale['gross'][$key] > 0 ? $billSale['gross'][$key] : 0),

																'product'		=> $billSale['product'][$key],

																'design'		=> ($billSale['design'][$key] != '' ? $billSale['design'][$key] : NULL),

																'id_sub_design'	=> ($billSale['id_sub_design'][$key] != '' ? $billSale['id_sub_design'][$key] : NULL),

																'date'  	    => $bill_date,

																'created_on'  	=> date("Y-m-d H:i:s"),

																'created_by'   	=> $this->session->userdata('uid'),

																'status'   		=> 1,

																'bill_id'       => $insId

															);

															$this->$model->insertData($non_tag_data, 'ret_nontag_item_log');

															if ($billSale['id_section'][$key] != '') {

																$section_non_tag_data = array(

																	'from_branch'	=> $addData['id_branch'],

																	'to_branch'	    => NULL,

																	'from_section'	=> $billSale['id_section'][$key],

																	'to_section'	=> NULL,

																	'no_of_piece'   => ($billSale['pcs'][$key] != '' ? $billSale['pcs'][$key] : NULL),

																	'less_wt' 		=> ($billSale['less'][$key] != '' ? $billSale['less'][$key] : NULL),

																	'net_wt' 		=> $billSale['net'][$key],

																	'gross_wt' 		=> ($billSale['gross'][$key] != '' ? $billSale['gross'][$key] : 0),

																	'product'		=> $billSale['product'][$key],

																	'design'		=> ($billSale['design'][$key] != '' ? $billSale['design'][$key] : NULL),

																	'id_sub_design'	=> ($billSale['id_sub_design'][$key] != '' ? $billSale['id_sub_design'][$key] : NULL),

																	'date'  	    => $bill_date,

																	'created_on'  	=> date("Y-m-d H:i:s"),

																	'created_by'   	=> $this->session->userdata('uid'),

																	'status'   		=> 1,

																	'bill_id'       => $insId

																);

																$this->$model->insertData($section_non_tag_data, 'ret_section_nontag_item_log');
															}
														}
													}

													//stock maintaince

													// echo "<pre>";print_r($billSale['stone_details'][$key]);exit;

													if ($billSale['stone_details'][$key]) {

														$stone_details = json_decode($billSale['stone_details'][$key], true);

														foreach ($stone_details as $stone) {

															$stone_data = array(

																'bill_id'        => $insId,

																'bill_det_id'   => $tagInsert,

																'pieces'        => $stone['stone_pcs'],

																'wt'            => $stone['stone_wt'],

																'stone_id'      => $stone['stone_id'],

																'price'         => $stone['stone_price'],

																'uom_id'        => $stone['uom_id'],

																//'certification_price'=>($stone['certification_cost']!='' ?$stone['certification_cost']:NULL),

																'item_type'     => 1, //Sale item,

																'is_apply_in_lwt' => $stone['is_apply_in_lwt'],

																'stone_cal_type' => $stone['stone_cal_type'],

																'rate_per_gram'  => $stone['rate_per_gram'],

															);

															$stoneInsert = $this->$model->insertData($stone_data, 'ret_billing_item_stones');

															//print_r($this->db->last_query());exit;

														}
													}

													if ($billSale['other_metal_details'][$key]) {

														$other_metal_details = json_decode($billSale['other_metal_details'][$key], true);

														foreach ($other_metal_details as $other_metal_det) {

															$est_other_metals = array(

																'bill_det_id'               => $tagInsert,

																'tag_other_itm_metal_id'    => $other_metal_det['tag_other_itm_metal_id'],

																'tag_other_itm_pur_id'      => $other_metal_det['tag_other_itm_pur_id'],

																'tag_other_itm_grs_weight'  => $other_metal_det['tag_other_itm_grs_weight'],

																'tag_other_itm_wastage'     => $other_metal_det['tag_other_itm_wastage'],

																'tag_other_itm_uom'         => $other_metal_det['tag_other_itm_uom'],

																'tag_other_itm_cal_type'    => $other_metal_det['tag_other_itm_cal_type'],

																'tag_other_itm_mc'          => $other_metal_det['tag_other_itm_mc'],

																'tag_other_itm_rate'        => $other_metal_det['tag_other_itm_rate'],

																'tag_other_itm_pcs'         => $other_metal_det['tag_other_itm_pcs'],

																'tag_other_itm_amount'      => $other_metal_det['tag_other_itm_amount'],

															);

															$this->$model->insertData($est_other_metals, 'ret_bill_other_metals');
														}
													}

													if (isset($billSale['est_itm_id'][$key]) && $billSale['est_itm_id'][$key] != '') {

														//Update Estimation Items by est_itm_id

														$this->$model->updateData(array('purchase_status' => 1, 'bil_detail_id' => $tagInsert), 'est_item_id', (isset($billSale['est_itm_id'][$key]) ? $billSale['est_itm_id'][$key] : ''), 'ret_estimation_items');

														$est_details = $this->$model->get_sale_est_details($billSale['est_itm_id'][$key]);

														if ($est_details['esti_id'] != '') {

															$this->$model->updateData(array('estbillid' => $insId), 'estimation_id', $est_details['esti_id'], 'ret_estimation');
														}
													}

													if ($billSale['tag'][$key] != '') {

														//Update Estimation Items by est_itm_id

														//$this->$model->updateData(array('purchase_status'=>1,'bil_detail_id'=>$tagInsert),'tag_id',(isset($billSale['tag'][$key])? $billSale['tag'][$key]:''), 'ret_estimation_items');

														$this->$model->updateData(array('tag_status' => 1), 'tag_id', $billSale['tag'][$key], 'ret_taging');

														if ($billSale['itemtype'][$key] == 0 && $billSale['is_partial'][$key] != '') {

															$this->$model->updateData(array('is_partial' => $billSale['is_partial'][$key]), 'tag_id', $billSale['tag'][$key], 'ret_taging');
														}

														//Update Tag Log status
														if ($billSale['itemtype'][$key] == 0) {
															$tag_log = array(

																'tag_id'	  => $billSale['tag'][$key],

																'date'		  => $bill_date,

																'status'	  => 1,

																'from_branch' => $addData['id_branch'],

																'to_branch'	  => NULL,

																'form_secret'   => $form_secret . '_' . $billSale['est_itm_id'][$key],

																'issuspensestock' => $addData['bill_type'] == 15 ? 1 : 0,

																'created_on'  => date("Y-m-d H:i:s"),

																'created_by'  => $this->session->userdata('uid'),

															);

															$this->$model->insertData($tag_log, 'ret_taging_status_log');

															$tag_status = $this->$model->get_tag_status($billSale['tag'][$key]);

															if ($tag_status['id_section'] != null && $tag_status['id_section'] != '') {

																$secttag_log = array(

																	'tag_id'	        => $billSale['tag'][$key],

																	'date'		        => $bill_date,

																	'status'	        => 1,

																	'from_branch'       => $addData['id_branch'],

																	'to_branch'	        => NULL,

																	'from_section'      => NULL,

																	'form_secret'   => $form_secret . '_' . $billSale['est_itm_id'][$key],

																	'to_section'        => $tag_status['id_section'],

																	'issuspensestock'   => $addData['bill_type'] == 15 ? 1 : 0,

																	'created_on'        => date("Y-m-d H:i:s"),

																	'created_by'        => $this->session->userdata('uid'),

																);

																$this->$model->insertData($secttag_log, 'ret_section_tag_status_log');
															}
														}
													}

													$order_no = (isset($billSale['order_no'][$key]) ? $billSale['order_no'][$key] : '');

													/*
            											if(sizeof($order_adv_adj_details)>0)

            											{

            											    foreach($order_adv_adj_details as $order)

            											    {

            											        if($order['order_no']!='')

            											        {

            											            $this->$model->updateData(array('is_adavnce_adjusted'=>1,'adjusted_bill_id'=>$insId,'updated_time'	=> date("Y-m-d H:i:s"),'updated_by'	=> $this->session->userdata('uid')),'bill_adv_id',$order['bill_adv_id'], 'ret_billing_advance');

            											        }

            											    }

            											}

														*/




													$id_orderdetails = (isset($billSale['id_orderdetails'][$key]) ? ($billSale['id_orderdetails'][$key] != '' ? $billSale['id_orderdetails'][$key] : '') : '');

													if ($id_orderdetails != '') {

														$this->$model->updateData(array('orderstatus' => 5, 'delivered_date' => date("Y-m-d H:i:s")), 'id_orderdetails', $billSale['id_orderdetails'][$key], 'customerorderdetails');
													}
													// print_r($billSale['id_section']);exit;
													if ($billSale['itemtype'][$key] == 2 && $billSale['id_section'][$key] != '') {

														$section_item = array(

															'id_branch'	      => $addData['id_branch'],

															'id_section'      => $billSale['id_section'][$key],

															'id_product'      => $billSale['product'][$key],

															'no_of_piece'     => $billSale['pcs'][$key],

															'gross_wt'	      => $billSale['gross'][$key],

															'net_wt'		  => $billSale['net'][$key],

															'created_by'	  => $this->session->userdata('uid'),

															'created_on'	  => date('Y-m-d H:i:s'),

															'updated_by'	  => $this->session->userdata('uid'),

															'updated_on'	 => date('Y-m-d H:i:s'),

														);

														$isExists = $this->$model->checkSectionItemExist($section_item);

														if ($isExists['status']) {

															$section_item = array(

																'id_branch'	  => $addData['id_branch'],

																'id_section'    => $billSale['id_section'][$key],

																'id_product'    => $billSale['product'][$key],

																'no_of_piece'   => $billSale['pcs'][$key],

																'gross_wt'	  => $billSale['gross'][$key],

																'net_wt'		  => $billSale['net'][$key],

																'created_by'	  => $this->session->userdata('uid'),

																'created_on'	  => date('Y-m-d H:i:s'),

																'updated_by'	  => $this->session->userdata('uid'),

																'updated_on'	  => date('Y-m-d H:i:s'),

															);

															if ($isExists['id_hometag_item'] != '') {

																$section_item['id_hometag_item'] = $isExists['id_hometag_item'];

																$nt_status = $this->$model->updatesecNTData($section_item, '+');
															}
														} else {

															$section_item = array(

																'id_branch'	  => $addData['id_branch'],

																'id_section'    => $billSale['id_section'][$key],

																'id_product'    => $billSale['product'][$key],

																'no_of_piece'   => $billSale['pcs'][$key],

																'gross_wt'	  => ($billSale['gross'][$key] != '' ? $billSale['gross'][$key] : 0),

																'net_wt'		  => $billSale['net'][$key],

																'created_by'	  => $this->session->userdata('uid')[$key],

																'created_on'	  => date('Y-m-d H:i:s'),

																'updated_by'	  => $this->session->userdata('uid'),

																'updated_on'	  => date('Y-m-d H:i:s'),

															);

															$nt_status = $this->$model->insertData($section_item, 'ret_home_section_item');
														}

														$section_item_log = array(

															'id_product'		  => $billSale['product'][$key],

															'no_of_piece'	      => $billSale['pcs'][$key],

															'gross_wt'	      => ($billSale['gross'][$key] != '' ? $billSale['gross'][$key] : 0),

															'net_wt'		      => $billSale['net'][$key],

															'less_wt'           => $billSale['less'][$key],

															"status"		      => 1,

															'from_branch'       => $addData['id_branch'],

															'to_branch'         => NULL,

															"from_section"      => ($billSale['id_section'][$key] != '' ? $billSale['id_section'][$key] : NULL),

															"to_section"        => NULL,

															"created_by"	      => $this->session->userdata('uid'),

															"created_on"        => date('Y-m-d H:i:s'),

															"date"		      => $bill_date,

														);

														$this->$model->insertData($section_item_log, 'ret_home_section_item_log');

														//   print_r($this->db->last_query());exit;

													}
												}
											}
										}

										/*$service = $this->$set_model->get_service($serviceID);

                    							if($service['serv_sms'] == 1)

                    							{

                    	        						$cus_details=$this->$model->get_customer($addData['bill_cus_id']);

                    	        						if($cus_details['mobile'])

                    	        						{

                        	        						$sms_data =$this->$sms_model->get_SMS_data($serviceID,$insId);

                        	        						$message=$sms_data['message'];

                    	        						    $sms=$this->send_sms($sms_data['mobile'],$message,$service['dlt_te_id']);

                    	        						}

                    							}

                    							if($service['serv_whatsapp'] == 1)

                    							{

                    	        						$cus_details=$this->$model->get_customer($addData['bill_cus_id']);

                    	        						if($cus_details['mobile'])

                    	        						{

                        	        						$sms_data =$this->$sms_model->get_SMS_data($serviceID,$insId);

                        	        						$message=$sms_data['message'];

                    	        						    $whatsapp=$this->admin_usersms_model->send_whatsApp_message($cus_details['mobile'],$message);

                    	        						}

                    							}*/

										$service = $this->$set_model->get_service_by_code('BILLING');

										if ($service['serv_whatsapp'] == 1 || $service['serv_sms'] == 1) {

											$sms_data = $this->admin_usersms_model->Get_service_code_sms('BILLING', $insId, '');

											if ($sms_data['mobile'] != '') {

												$whatsapp = $this->send_sms($sms_data['mobile'], $sms_data['message'], $service['dlt_te_id']);
											}
										}
									}

									//Purchase Items

									if (!empty($billPurchase)) {

										//Update Ref No

										$ref_no = $this->$model->generateRefNo($addData['id_branch'], 'pur_ref_no', $addData['metal_type'], $addData['is_eda']);

										$this->$model->updateData(array('pur_ref_no' => $ref_no), 'bill_id', $insId, 'ret_billing');

										//Update Ref No

										$arrayPurchaseBill = array();

										$old_metal_weight = 0;

										$old_metal_amount = 0;

										foreach ($billPurchase['is_est_details'] as $key => $val) {

											if ($billPurchase['is_est_details'][$key] == 1) {

												$old_metal_amount += $billPurchase['billamount'][$key];

												$old_metal_weight += ($billPurchase['net'][$key] * ($billPurchase['purity'][$key]) / 91.6);

												$arrayPurchaseBill = array(

													'bill_id'                   => $insId,

													'current_branch'            => $addData['id_branch'],

													'metal_type'                => $billPurchase['metal_type'][$key],

													'id_old_metal_type'			=> ($billPurchase['id_old_metal_type'][$key] ? $billPurchase['id_old_metal_type'][$key] : NULL),

													'id_old_metal_category'		=> ($billPurchase['id_old_metal_category'][$key] ? $billPurchase['id_old_metal_category'][$key] : NULL),

													'item_type'                 => $billPurchase['itemtype'][$key],

													'esti_old_metal_sale_id'    => ($billPurchase['est_old_itm_id'][$key] ? $billPurchase['est_old_itm_id'][$key] : NULL),

													'piece'                     => empty($billPurchase['piece'][$key]) ? $billPurchase['pcs'][$key] : $billPurchase['piece'][$key],

													'gross_wt'                  => $billPurchase['gross'][$key],

													'stone_wt'                  => $billPurchase['stone_wt'][$key],

													'dust_wt'                   => $billPurchase['dust_wt'][$key],

													'net_wt'                    => $billPurchase['net'][$key],

													'wast_wt'                   => $billPurchase['wastage_wt'][$key],

													'wastage_percent'           => ($billPurchase['wastage'][$key] !== '' && $billPurchase['wastage'][$key] !== null) ? $billPurchase['wastage'][$key] : 0,

													'rate'                      => $billPurchase['billamount'][$key],

													'rate_per_grm'              => $billPurchase['rate_per_grm'][$key],

													'touch'                     => ($billPurchase['touch'][$key] != '' ? $billPurchase['touch'][$key] : 0),

													'purity'                    => ($billPurchase['purity'][$key] != '' ? $billPurchase['purity'][$key] : 0),

													'old_metal_rate'            => $this->$model->getOldMetalRate($billPurchase['metal_type'][$key]),

													'bill_discount'             => empty($billPurchase['discount'][$key]) ? 0 : $billPurchase['discount'][$key],
													'id_employee'               => (!empty($billPurchase['id_employee'][$key]) ? $billPurchase['id_employee'][$key] : NULL)
												);
											}

											if (!empty($arrayPurchaseBill)) {

												$oldMetal = $this->$model->insertData($arrayPurchaseBill, 'ret_bill_old_metal_sale_details');

												if ($oldMetal) {

													$pur_store_as = (isset($addData['pur_store_as']) ? $addData['pur_store_as'] : 1);

													if ($addData['bill_type'] == 5) {

														$arrayAdv = array(

															'bill_id'           => $insId,

															'advance_type'      => 2,

															'old_metal_sale_id' => $oldMetal,

															//'rate_per_gram'     => $billPurchase['rate_per_grm'][$key],

															'rate_per_gram'     => $addData['goldrate_22ct'],

															'advance_weight'    => ($pur_store_as == 2 ? $billPurchase['net'][$key] : 0),

															'advance_amount'    => ($pur_store_as == 1 ? ($billPurchase['billamount'][$key]) : 0),

															'received_weight'   => $billPurchase['net'][$key],

															'store_as'          => $pur_store_as,

															'order_no'          => (!empty($addData['filter_order_no']) ? $addData['filter_order_no'] : NULL),

															'id_customerorder'  => $addData['id_customerorder'],

															'advance_date'		=> date("Y-m-d H:i:s"),

															'created_time'		=> date("Y-m-d H:i:s"),

															'created_by'    	=> $this->session->userdata('uid')

														);

														$advInsId = $this->$model->insertData($arrayAdv, 'ret_billing_advance');
													}

													if ($billPurchase['stone_details'][$key]) {

														$stone_details = json_decode($billPurchase['stone_details'][$key], true);

														foreach ($stone_details as $stone) {

															$stone_data = array(

																'bill_id'        => $insId,

																'old_metal_sale_id' => $oldMetal,

																'pieces'        => $stone['stone_pcs'],

																'wt'            => $stone['stone_wt'],

																'stone_id'      => $stone['stone_id'],

																'price'         => $stone['stone_price'],

																'rate_per_gram' => $stone['rate_per_gram'],

																'uom_id'        => $stone['uom_id'],

																'item_type'     => 2 //Purchase item

															);

															$stoneInsert = $this->$model->insertData($stone_data, 'ret_billing_item_stones');
														}
													}

													//Update Estimation Items

													if ($billPurchase['est_old_itm_id'][$key]) {

														$this->$model->updateData(array('purchase_status' => 1, 'bill_id' => $insId), 'old_metal_sale_id', $billPurchase['est_old_itm_id'][$key], 'ret_estimation_old_metal_sale_details');

														$est_details = $this->$model->get_old_metal_est_details($billPurchase['est_old_itm_id'][$key]);

														if ($est_details['est_id'] != '') {

															$this->$model->updateData(array('estbillid' => $insId), 'estimation_id', $est_details['est_id'], 'ret_estimation');
														}
													}

													//Insert into Old Metal Log Table

													$old_metal_log = array(

														'old_metal_sale_id' => $oldMetal,

														'from_branch'      => NULL,

														'to_branch'        => $addData['id_branch'],

														'status'           => 1,

														'item_type'        => 1, // Old Metal

														'date'             => $bill_date,

														'created_on'       => date("Y-m-d H:i:s"),

														'created_by'      => $this->session->userdata('uid'),

													);

													$this->$model->insertData($old_metal_log, 'ret_purchase_items_log');
												}
											}
										}

										//check is sold already, (for split bill)

										$sold_status = $this->ret_billing_model->

										$service = $this->$set_model->get_service($serviceID);

										if ($service['serv_sms'] == 1) {

											$cus_details = $this->$model->get_customer($addData['bill_cus_id']);

											if ($cus_details['mobile']) {

												$sms_data = $this->$sms_model->get_SMS_data($serviceID, $insId);

												$this->send_sms($sms_data['mobile'], $sms_data['message'], '');
											}
										}

										if ($addData['make_as_advance'] == 2) {

											if (sizeof($chit_deposit_acc_details) > 0) {

												foreach ($chit_deposit_acc_details as $acc) {

													if ($old_metal_weight > 0) {

														if ($this->payment_model->get_rptnosettings() == 1) {

															$receipt_no = $this->generate_receipt_no($acc['id_scheme'], $addData['id_branch']);
														} else {

															$receipt_no = NULL;
														}

														$pay_array = array(

															'id_scheme_account'	  =>  $acc['id_scheme_account'],

															'id_employee' 		  =>  $this->session->userdata('uid'),

															'date_payment'        =>  date('Y-m-d H:i:s'),

															'id_branch' 		  =>  $addData['id_branch'],

															'due_type'      	  =>  'ND',

															'payment_mode' 		  =>  'OG',

															'payment_status'      =>  1,

															'receipt_no'		  =>  $receipt_no,

															'gst'			      =>  0,

															'added_by'			  =>  0,

															'date_add'            =>  date('Y-m-d H:i:s'),

															'date_upd'			  =>  date('Y-m-d H:i:s'),

															'approval_date'		  =>  date('Y-m-d H:i:s'),

															"payment_amount"      =>  number_format($old_metal_amount, 2, '.', ''),

															"metal_weight"        =>  number_format(($old_metal_amount / $addData['goldrate_22ct']), 3, '.', '')

														);

														$payId = $this->$model->insertData($pay_array, 'payment');

														// print_r($payId);exit;

														if ($payId) {

															$payment_mode_detail = array(

																'id_payment'        => $payId,

																'payment_amount'    => number_format($old_metal_amount, 2, '.', ''),

																'payment_mode'      => 'OG',

																'payment_status'    => 1,

																'payment_date'		=> date("Y-m-d H:i:s"),

																'created_time'	    => date("Y-m-d H:i:s"),

																'created_by'	    => $this->session->userdata('uid')

															);

															$this->$model->insertData($payment_mode_detail, 'payment_mode_details');

															$old_metal_data = array('id_payment' => $payId, 'bill_id' => $insId);

															$acdata = $this->payment_model->isAcnoAvailable($acc['id_scheme_account']);

															$scheme_acc_no = $this->$set_model->accno_generatorset();

															// scheme account number generate  based on one more settings Integ Auto//hh

															if (($scheme_acc_no['status'] == 1 && ($scheme_acc_no['schemeacc_no_set'] == 0 || $scheme_acc_no['schemeacc_no_set'] == 3))) {

																$scheme_acc_number = $this->account_model->account_number_generator($acc['id_scheme'], $addData['id_branch']);

																if ($scheme_acc_number != NULL) {

																	$updateData['scheme_acc_number'] = $scheme_acc_number;

																	$updateData['fixed_wgt'] = number_format(($old_metal_amount / $addData['goldrate_22ct']), 3, '.', '');

																	if ($acc['id_scheme_account'] > 0) {

																		$updSchAc = $this->account_model->update_account($updateData, $acc['id_scheme_account']);
																	}
																}
															}

															$old_metal_insert = $this->$model->insertData($old_metal_data, 'payment_old_metal');
														}
													}
												}
											}
										}
									}

									if ($addData['bill_type'] == 5 ) {

										// ---------- FIXED / UNFIXED ORDER RATE LOGIC START ----------

										// print_r($addData['id_customerorder']);exit;

										if (!empty($addData['id_customerorder'])) {

											$this->update_order_rate_type($addData['id_customerorder'], $addData['id_branch'], '');
										}

										// ---------- FIXED / UNFIXED ORDER RATE LOGIC END ----------
									}

									//Update Credit Status

									if ($addData['bill_type'] == 8) {

										$credit_pay_amount  = $this->$model->get_credit_collection_details($ref_bill_id);

										$bill_details       = $this->$model->get_BillAmount($ref_bill_id);

										// Use >= instead of == so that when old metal amount exceeds the credit due amount
										// (overpayment scenario), the original bill is still correctly marked as Paid (credit_status=1).
										if ($credit_pay_amount >= ($bill_details['tot_bill_amount'] - $bill_details['tot_amt_received'])) {

											$updCredit = array("credit_status" 	=> 1);

											$this->$model->updateData($updCredit, 'bill_id', $ref_bill_id, 'ret_billing');
										}
									} else if ($addData['bill_type'] == 7 && !empty($addData['ret_bill_id'])) {
										$original_bill_id = $addData['ret_bill_id'];
										
										// Retrieve current bill details including is_credit status
										$bill_details = $this->$model->get_BillAmount($original_bill_id);
										
										// Check if the reference bill was originally a credit bill
										if ($bill_details['is_credit'] == 1) {
											
											// 1. Get total paid via collections (type 8)
											$paid_amount = $this->$model->get_credit_pay_amount($original_bill_id);
											$old_metal_amount = $this->$model->get_credit_old_metal_amount($original_bill_id, 8);
											$total_collections = $paid_amount + $old_metal_amount;
											
											// 2. Get total returned amount from model
											$total_returned = $this->$model->get_total_returned_amount_by_bill($original_bill_id);
											
											// Calculate balance (subtract round_off_amt so a full return always zeroes out)
											$balance = $bill_details['tot_bill_amount'] - $bill_details['tot_amt_received'] - $total_collections - $total_returned - floatval($bill_details['round_off_amt']);
											
											// If balance is 0 or less, mark paid (1), else pending (2)
											$new_status = ($balance <= 0) ? 1 : 2;
											$updCredit = array("credit_status" => $new_status);
											$this->$model->updateData($updCredit, 'bill_id', $original_bill_id, 'ret_billing');
										}
									}

									//Repair Orders Update

									if (!empty($repair_orders)) {

										//echo "<pre>";print_r($repair_orders);exit;

										$repairOrderArray = array();

										//Update Ref No

										$ref_no = $this->$model->generateRefNo($addData['id_branch'], 'repair_del_ref_no', '', $addData['is_eda']);

										$this->$model->updateData(array('repair_del_ref_no' => $ref_no), 'bill_id', $insId, 'ret_billing');

										if ($addData['is_against_order'] == 2) {

											$order_from     = $addData['id_branch'];

											$order_no       = $this->$ordermodel->generateOrderNo($order_from, 3);

											$dCData         = $this->admin_settings_model->getBranchDayClosingData($addData['order_from']);

											$order_datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

											$order = array(

												'fin_year_code'     => $fin_year['fin_year_code'],

												'order_type'		=> 3,

												'order_from'		=> $order_from,

												'order_no'          => $order_no,

												'order_for'			=> 2,

												'order_date'		=> $order_datetime,

												'order_to'			=> (!empty($addData['bill_cus_id']) ? $addData['bill_cus_id'] : NULL),

												'createdon'         => date("Y-m-d H:i:s"),

												'order_taken_by'    => $this->session->userdata('uid')

											);

											$insOrder = $this->$model->insertData($order, 'customerorder');

											//print_r($this->db->last_query());exit;

										}

										foreach ($repair_orders['is_est_details'] as $key => $val) {

											$i = 1;

											if ($repair_orders['is_est_details'][$key] == 1) {

												if ($addData['is_against_order'] == 2) {

													$orderDetails = array(

														'orderno'			=> $order_no . "-" . $i,

														'id_customerorder'	=> $insOrder,

														'ortertype'		    => 3,

														'id_product'		=> $repair_orders['product'][$key],

														'id_repair_master'	=> $repair_orders['repair'][$key],

														'totalitems'		=> $repair_orders['piece'][$key],

														'weight'			=> $repair_orders['completed_weight'][$key],

														'completed_weight'	=> $repair_orders['completed_weight'][$key],

														'current_branch'	=> $order_from,

														'orderstatus'	    => 5,

														'rate'	            => $repair_orders['amount'][$key],

														'id_employee'       => $this->session->userdata('uid'),

													);

													$id_orderdetails = $this->$model->insertData($orderDetails, 'customerorderdetails');

													//print_r($this->db->last_query());exit;

													$i++;
												} else {

													$id_orderdetails = $repair_orders['id_orderdetails'][$key];
												}

												$repairOrderArray = array(

													'orderstatus'		    => 5,

													'bill_id'			    => $insId,

													'total_cgst'			=> $repair_orders['cgst'][$key],

													'total_sgst'			=> $repair_orders['sgst'][$key],

													'total_igst'			=> (($repair_orders['igst'][$key] != '') ? $repair_orders['igst'][$key] : NULL),

													'repair_percent'		=> $repair_orders['repair_percent'][$key],

													'repair_tot_tax'		=> $repair_orders['repair_tot_tax'][$key],
												);

												$status = $this->$model->updateData($repairOrderArray, 'id_orderdetails', $id_orderdetails, 'customerorderdetails');
											}
										}
									}

									//Repair Orders Update

									//echo "<pre>";print_r($supplier_sales_bill);exit;

									if (!empty($supplier_sales_bill)) {

										$ref_no = $this->$model->generateRefNo($addData['id_branch'], 'sales_ref_no', $addData['metal_type'], 1);

										$this->$model->updateData(array('sales_ref_no' => $ref_no), 'bill_id', $insId, 'ret_billing');

										foreach ($supplier_sales_bill['catid'] as $key => $val) {

											$arraySupplierBillSales = array(

												'bill_id'               => $insId,

												'pur_ret_cat_id'        => $supplier_sales_bill['catid'][$key],

												'pur_ret_cat_pcs'       => $supplier_sales_bill['pcs'][$key],

												'pur_ret_cat_gwt'       => $supplier_sales_bill['gross_wt'][$key],

												'pur_ret_cat_leswt'     => $supplier_sales_bill['less_wt'][$key],

												'pur_ret_cat_netwt'     => $supplier_sales_bill['net_wt'][$key],

												'pur_ret_rate'          => $supplier_sales_bill['rate_per_gram'][$key],

												'pur_ret_tax_rate'      => $supplier_sales_bill['pur_ret_tax_rate'][$key],

												'pur_ret_tax_value'     => $supplier_sales_bill['total_tax'][$key],

												'pur_ret_cgst'          => $supplier_sales_bill['cgst_cost'][$key],

												'pur_ret_sgst'          => $supplier_sales_bill['sgst_cost'][$key],

												'pur_ret_igst'          => $supplier_sales_bill['igst_cost'][$key],

												'pur_ret_item_cost'     => $supplier_sales_bill['amount'][$key],

												'calc_type'             => $supplier_sales_bill['caltype'][$key],

											);

											$id_orderdetails = $this->$model->insertData($arraySupplierBillSales, 'ret_bill_supplier_sales_details');
										}

										$tagged_item_list = json_decode($addData['returntaggeditemlist'], true);

										$nontagreturnitemlist = json_decode($addData['nontagreturnitemlist'], true);

										if (sizeof($tagged_item_list) > 0) {

											foreach ($tagged_item_list as $val) {

												$tag = $this->$model->get_tag_status($val['tag_id']);

												$tag_list = array(

													'bill_id'       => $insId,

													'product_id'    => $tag['product_id'],

													'design_id'     => $tag['design_id'],

													'id_sub_design' => $tag['id_sub_design'],

													'purity'        => $tag['purity'],

													'piece'         => $val['piece'],

													'gross_wt'      => $val['gross_wt'],

													'net_wt'        => $val['net_wt'],

													'tag_id'        => $val['tag_id'],

												);

												$this->$model->insertData($tag_list, 'ret_bill_details');

												$this->$model->updateData(array('tag_status' => 1), 'tag_id', $val['tag_id'], 'ret_taging');

												//Update Tag Log status

												$tag_log = array(

													'tag_id'	  => $val['tag_id'],

													'date'		  => $bill_date,

													'form_secret'   => $form_secret,

													'status'	  => 1,

													'from_branch' => $addData['id_branch'],

													'to_branch'	  => NULL,

													'issuspensestock' => $addData['bill_type'] == 15 ? 1 : 0,

													'created_on'  => date("Y-m-d H:i:s"),

													'created_by'  => $this->session->userdata('uid'),

												);

												$this->$model->insertData($tag_log, 'ret_taging_status_log');

												$tag_status = $this->$model->get_tag_status($billSale['tag'][$key]);

												if ($tag_status['id_section'] != null && $tag_status['id_section'] != '') {

													$secttag_log = array(

														'tag_id'	        => $val['tag_id'],

														'date'		        => $bill_date,

														'form_secret'       => $form_secret,

														'status'	        => 1,

														'from_branch'       => $addData['id_branch'],

														'to_branch'	        => NULL,

														'from_section'      => NULL,

														'to_section'        => $tag_status['id_section'],

														'issuspensestock'   => $addData['bill_type'] == 15 ? 1 : 0,

														'created_on'        => date("Y-m-d H:i:s"),

														'created_by'        => $this->session->userdata('uid'),

													);

													$this->$model->insertData($secttag_log, 'ret_section_tag_status_log');
												}
											}
										}

										if (sizeof($nontagreturnitemlist) > 0) {

											foreach ($nontagreturnitemlist as $val) {

												$non_tag_list = array(

													'bill_id'       => $insId,

													'product_id'    => $val['id_product'],

													'design_id'     => $val['id_design'],

													'id_sub_design' => $val['id_sub_design'],

													'piece'         => $val['piece'],

													'gross_wt'      => $val['gross_wt'],

													'net_wt'        => $val['net_wt'],

													'tax_group_id'  => $val['tgrp_id'],

												);

												$this->$model->insertData($non_tag_list, 'ret_bill_details');

												$existData = array('id_product' => $val['id_product'], 'id_design' => $val['id_design'], 'id_sub_design' => $val['id_sub_design'], 'id_branch' => $addData['id_branch']);

												$isExist = $this->$model->checkNonTagItemExist($existData);

												if ($isExist['status'] == TRUE) {

													$nt_data = array(

														'id_nontag_item' => $isExist['id_nontag_item'],

														'no_of_piece'	=> ($val['piece'] != '' ? $val['piece'] : 0),

														'gross_wt'		=> $val['gross_wt'],

														'net_wt'		=> $val['net_wt'],

														'less_wt'		=> 0,

														'updated_by'	=> $this->session->userdata('uid'),

														'updated_on'	=> date('Y-m-d H:i:s'),

													);

													$this->$model->updateNTData($nt_data, '-');

													$non_tag_data = array(

														'from_branch'	=> $addData['id_branch'],

														'to_branch'	    => NULL,

														'no_of_piece'   => $val['piece'],

														'less_wt' 		=> 0,

														'net_wt' 		=> $val['net_wt'],

														'gross_wt' 		=> $val['gross_wt'],

														'product'		=> $val['id_product'],

														'design'		=> $val['id_design'],

														'date'  	    => $bill_date,

														'created_on'  	=> date("Y-m-d H:i:s"),

														'created_by'   	=> $this->session->userdata('uid'),

														'status'   		=> 1,

														'bill_id'       => $insId

													);

													$this->$model->insertData($non_tag_data, 'ret_nontag_item_log');
												}
											}
										}
									}

									//Update Credit Status

								}



								if ($this->db->trans_status() === TRUE) {

									$this->db->trans_commit();

									$est_oth_inv = (isset($_POST['est_oth_inv']) ? $_POST['est_oth_inv'] : '');

									if (!empty($est_oth_inv)) {

										foreach ($est_oth_inv['id_other_item'] as $key => $val) {

											$insData = array(

												'id_other_item'     => $est_oth_inv['id_other_item'][$key],

												'issue_form'        => 1,

												'issue_date'        => $bill_date,

												'bill_id'           => $insId,

												'no_of_pieces'      => $est_oth_inv['no_of_pcs'][$key],

												'id_branch'         => $addData['id_branch'],

												'created_on'        => date("Y-m-d H:i:s"),

												'created_by'        => $this->session->userdata('uid')

											);

											$otherInvIssue = $this->$model->insertData($insData, 'ret_other_invnetory_issue');

											if ($otherInvIssue) {

												$inventoryItem = $this->$model->get_InventoryCategory($est_oth_inv['id_other_item'][$key]);

												$itemDetails    = $this->$model->get_other_inventory_purchase_items_details($est_oth_inv['id_other_item'][$key], $addData['id_branch'], $inventoryItem['issue_preference'], $est_oth_inv['no_of_pcs'][$key]);

												foreach ($itemDetails as $items) {

													$updData = array(

														'id_inventory_issue' => $otherInvIssue,

														'status'            => 1

													);

													$this->$model->updateData($updData, 'pur_item_detail_id', $items['pur_item_detail_id'], 'ret_other_inventory_purchase_items_details');
												}

												$logData = array(

													'item_id'      => $est_oth_inv['id_other_item'][$key],

													'no_of_pieces' => count($itemDetails), // PAT-INV-001: use actual processed count

													'amount'       => 0,

													'date'         => $bill_date,

													'status'       => 1,

													'from_branch'  => $addData['id_branch'],

													'to_branch'    => NULL,

													'created_on'   => date("Y-m-d H:i:s"),

													'created_by'   => $this->session->userdata('uid')

												);

												$this->$model->insertData($logData, 'ret_other_inventory_purchase_items_log');
											}
										}
									}


									$insurance = 0;

									if($insId && $addData['is_eda']==1 && $addData['is_insured'] == 1)  //EDA & Insured Check
									{
									 	$ins_no = $this->$model->code_insurance_number_generator($addData['id_branch'], $addData['metal_type'], $addData['is_eda']);   //Bill Number Generate

										// insurance
										$logData = array(
											'bill_id'       => $insId,
											'insurance_no'   => "INS".$ins_no,
											'insurance_amount'     => $tot_ins_payable,
											'policy_from' => date('Y-m-d', strtotime($addData['insurance_from_date'])),
											'policy_to'   => date('Y-m-d', strtotime($addData['insurance_to_date'])),
											'created_at'    => date("Y-m-d H:i:s"),
										);
										$insurance = 1;
										$this->$model->insertData($logData, 'ret_insurance');
									}



									//Update roundoff amount by fetching the billing details information and update in billing table

									$billDetails = $this->$model->getBillDetailsData($insId);

									if (!empty($billDetails)) {

										$total_amount = $billDetails['itemwithtax'];

										$final_cost     = number_format($total_amount, 0, '.', '');

										$round_val      = number_format(($final_cost - $total_amount), 2, '.', '');

										$this->$model->updateData(array('round_off_amt' => $round_val), 'bill_id', $insId, 'ret_billing');
									}

									$log_data = array(

										'id_log'        => $this->session->userdata('id_log'),

										'event_date'    => date("Y-m-d H:i:s"),

										'module'        => 'Billing',

										'operation'     => 'Add',

										'record'        =>  $insId,

										'remark'        => 'Record added successfully'

									);

									$this->log_model->log_detail('insert', '', $log_data);

									$return_data = array('status' => TRUE, 'id' => $insId, 'print_type' => (!empty($billSale)  ? 1 : 2)); //1-Normal,2-Thermal

									/*	$log_path = 'billing_log/'.$insId.'/';

                                        if (!is_dir($log_path))

                                        {

                                            mkdir($log_path, 0777, TRUE);

                                        }

            							 $log_path = $log_path.'/post_data.txt';

            							 $post_data=array(

            							                    'general_details'=>$addData,

            							                    'sales_ddetails'=>$billSale,

            							                    'purchase_details'=>$billPurchase

            							                );

            							$log_detail=json_encode($post_data);

            							file_put_contents($log_path,$log_detail,FILE_APPEND | LOCK_EX);*/

									$this->session->set_flashdata('chit_alert', array('message' => 'Billing added successfully', 'class' => 'success', 'title' => 'Add Billing'));

									//$this->session->unset_userdata('FORM_SECRET');

								} else {

									$this->db->trans_rollback();

									$return_data = array('status' => FALSE, 'id' => '');

									echo $this->db->_error_message() . "<br/>";

									echo $this->db->last_query();
									exit;

									$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Billing'));
								}

								echo json_encode($return_data);
							} else {

								$return_data = array('status' => FALSE, 'id' => '');

								$this->session->set_flashdata('chit_alert', array('message' => 'Kindly update Day closing data to add Bill', 'class' => 'danger', 'title' => 'Add Billing'));

								echo json_encode($return_data);
							}
						} else {

							$return_data = array('status' => FALSE, 'id' => '');

							$this->session->set_flashdata('chit_alert', array('message' => 'Kindly Check The Tag or Estimation No', 'class' => 'danger', 'title' => 'Add Billing'));

							echo json_encode($return_data);
						}

						//$this->session->unset_userdata('FORM_SECRET');

					} else {

						$return_data = array('status' => FALSE, 'id' => '');

						$this->session->set_flashdata('chit_alert', array('message' => 'Unable To Proceed Your Request.Invalid Form Submit', 'class' => 'danger', 'title' => 'Add Billing'));

						echo json_encode($return_data);
					}
				} else {

					$return_data = array('status' => FALSE, 'id' => '');

					$this->session->set_flashdata('chit_alert', array('message' => 'Form Already Submitted', 'class' => 'danger', 'title' => 'Add Billing'));

					echo json_encode($return_data);
				}

				break;

			case "edit":

				//$data['billing'] = $this->$model->get_entry_records($id);

				//$data['uom']= $this->$model->getUOMDetails();

				$data['billing'] = $this->$model->getBillingDetails($id, $type);

				$data['payment'] = $this->$model->getPaymentDetails($id);

				$data['est_other_item'] = $this->$model->getOtherEstimateItemsDetails($id);

				//echo "<pre>"; print_r($data);exit;

				$data['main_content'] = "billing/form";

				$this->load->view('layout/template', $data);

				break;

			case 'delete':

				$this->db->trans_begin();

				$this->$model->deleteData('estimation_id', $id, 'ret_estimation');

				$this->$model->deleteData('est_id', $id, 'ret_est_gift_voucher_details');

				$this->$model->deleteData('est_id', $id, 'ret_est_chit_utilization');

				$this->$model->deleteData('esti_id', $id, 'ret_estimation_items');

				$this->$model->deleteData('est_id', $id, 'ret_estimation_item_stones');

				$this->$model->deleteData('est_id', $id, 'ret_estimation_item_other_materials');

				$this->$model->deleteData('est_id', $id, 'ret_estimation_old_metal_sale_details');

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					$this->session->set_flashdata('chit_alert', array('message' => 'Billing deleted successfully', 'class' => 'success', 'title' => 'Delete Billing'));
				} else {

					$this->db->trans_rollback();

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Delete Billing'));
				}

				redirect('admin_ret_billing/billing/list');

				break;

			case "update":

				$updateData = $_POST['billing'];

				//echo "<pre>"; print_r($_POST);exit;

				$ref_bill_id = ($updateData['bill_type'] == 3 || $updateData['bill_type'] == 7 ? (!empty($updateData['ret_bill_id']) ? $updateData['ret_bill_id'] : NULL) : NULL);

				$data = array(

					'ref_bill_id'	=> (!empty($updateData['ret_bill_id']) ? $updateData['ret_bill_id'] : NULL),

					'bill_type'		=> $updateData['bill_type'],

					'pan_no'		=> (!empty($updateData['pan_no']) ? $updateData['pan_no'] : NULL),

					'bill_date'		=> (!empty($updateData['bill_date']) ? $updateData['bill_date'] : NULL),

					'bill_cus_id'	=> (!empty($updateData['bill_cus_id']) ? $updateData['bill_cus_id'] : NULL),

					'tot_discount'	=> (!empty($updateData['discount']) ? $updateData['discount'] : 0),

					'tot_bill_amount'	=> (!empty($updateData['total_cost']) ? $updateData['total_cost'] : 0),

					'tot_amt_received'	=> (!empty($updateData['tot_amt_received']) ? $updateData['tot_amt_received'] : 0),

					'is_credit'			=> (!empty($updateData['is_credit']) ? $updateData['is_credit'] : 0),

					'credit_status'		=> (!empty($updateData['is_credit'] && $updateData['is_credit'] == 1) ? 2 : 1),

					'credit_due_date'	 => (!empty($updateData['credit_due_date']) ? ($updateData['is_credit'] == 1 ? implode('-', array_reverse(explode('-',$updateData['credit_due_date']))) : NULL) : NULL),

					'updated_time'	     => date("Y-m-d H:i:s"),

					'approved_by'       => $this->session->userdata('uid'),

					'id_branch'         => $updateData['id_branch'],

					'is_to_be'          => (!empty($updateData['is_to_be']) ? $updateData['is_to_be'] : 0),

				);

				$this->db->trans_begin();

				$update_status = $this->$model->updateData($data, 'bill_id', $id, 'ret_billing');

				// print_r($this->db->last_query());exit;

				if ($update_status) {

					if ($updateData['cash_payment'] > 0) {

						$arrayCashPay = array(

							'payment_amount' => $updateData['cash_payment'],

							'payment_mode' => 'Cash',

							'payment_for'		=> ($updateData['bill_type'] == 6 ? 2 : 1),

							'payment_status' => 1,

							'payment_date'		=> (!empty($updateData['bill_date']) ? $updateData['bill_date'] : NULL),

							'updated_time'	=> date("Y-m-d H:i:s"),

							'updated_by'	=> $this->session->userdata('uid')

						);

						if (!empty($arrayCashPay)) {

							$pay_upadate = $this->$model->updateData($arrayCashPay, 'bill_id', $id, 'ret_billing_payment');
						}
					}

					$billSale = (isset($_POST['sale']) ? $_POST['sale'] : '');

					//echo "<pre>"; print_r($billSale);exit;

					if (!empty($billSale)) {

						$arrayBillSales = array();

						foreach ($billSale['is_est_details'] as $key => $val) {

							$arrayBillSales = array(

								'bill_id' => $id,

								'esti_item_id'  => (isset($billSale['est_itm_id'][$key]) ? $billSale['est_itm_id'][$key] : NULL),

								'item_type' 	=> $billSale['itemtype'][$key],

								'bill_type' 	=> $billSale['is_est_details'][$key],

								'product_id' 	=> ($billSale['product'][$key] != '' ? $billSale['product'][$key] : NULL),

								'design_id' 	=> ($billSale['design'][$key] != '' ? $billSale['design'][$key] : NULL),

								'tag_id'		=> ($billSale['tag'][$key] != '' ? $billSale['tag'][$key] : NULL),

								'quantity' 		=> 1,

								'purity' 		=> ($billSale['purity'][$key] != '' ? $billSale['purity'][$key] : NULL),

								'size' 			=> ($billSale['size'][$key] != '' ? $billSale['size'][$key] : NULL),

								'uom' 			=> ($billSale['uom'][$key] != '' ?  $billSale['uom'][$key] : NULL),

								'piece' 		=> $billSale['pcs'][$key],

								'less_wt' 		=> ($billSale['less'][$key] != '' ? $billSale['less'][$key] : NULL),

								'net_wt' 		=> $billSale['net'][$key],

								'gross_wt' 		=> $billSale['gross'][$key],

								'pure_wt' 		=> $billSale['pure_wt'][$key],

								'calculation_based_on' => $billSale['calltype'][$key],

								'wastage_percent' => ($billSale['wastage'][$key] !== '' && $billSale['wastage'][$key] !== null) ? $billSale['wastage'][$key] : 0,

								'mc_value' 		=> $billSale['mc'][$key],

								'mc_type' 		=> $billSale['bill_mctype'][$key],

								'item_cost' 	=> $billSale['billamount'][$key],

								'item_total_tax' => $billSale['item_total_tax'][$key],

								'tax_group_id'  => $billSale['taxgroup'][$key],

								'bill_discount' => empty($billSale['discount'][$key]) ? 0 : $billSale['discount'][$key],

								'rate_per_grm'  => $billSale['per_grm'][$key],

								'is_partial_sale' => $billSale['is_partial'][$key]

							);

							if (!empty($arrayBillSales)) {

								if (isset($billSale['bill_det_id'][$key]) && $billSale['bill_det_id'][$key] != '') {

									$update_status = $this->$model->updateData($arrayBillSales, 'bill_det_id', $billSale['bill_det_id'][$key], 'ret_bill_details');

									//print_r($this->db->last_query());exit;

								} else {

									$tagInsert = $this->$model->insertData($arrayBillSales, 'ret_bill_details');

									if ($billSale['stone_details'][$key]) {

										$stone_details = json_decode($billSale['stone_details'][$key], true);

										foreach ($stone_details as $stone) {

											$stone_data = array(

												'bill_id'        => $id,

												'bill_det_id'   => $tagInsert,

												'pieces'        => $stone['stone_pcs'],

												'wt'            => $stone['stone_wt'],

												'stone_id'      => $stone['stone_id'],

												'price'         => $stone['stone_price'],

												'item_type'     => 1 //Sale item

											);

											$stoneInsert = $this->$model->insertData($stone_data, 'ret_billing_item_stones');
										}
									}

									if (isset($billSale['est_itm_id'][$key]) && !empty($billSale['est_itm_id'][$key])) {

										//Update Estimation Items by est_itm_id

										$this->$model->updateData(array('purchase_status' => 1, 'bil_detail_id' => $tagInsert), 'est_item_id', (isset($billSale['est_itm_id'][$key]) ? $billSale['est_itm_id'][$key] : ''), 'ret_estimation_items');
									}

									if (isset($billSale['tag'][$key])) {

										//Update Estimation Items by est_itm_id

										$this->$model->updateData(array('purchase_status' => 1, 'bil_detail_id' => $tagInsert), 'tag_id', (isset($billSale['tag'][$key]) ? $billSale['tag'][$key] : ''), 'ret_estimation_items');

										$this->$model->updateData(array('tag_status' => 1), 'tag_id', $billSale['tag'][$key], 'ret_taging');
									}

									if ($addData['filter_order_no'] != '') {

										//Update Advance Adj By Order No

										$this->$model->updateData(array('is_adavnce_adjusted' => 1, 'adjusted_bill_id' => $insId, 'updated_time'	=> date("Y-m-d H:i:s"), 'updated_by'	=> $this->session->userdata('uid')), 'order_no', $addData['filter_order_no'], 'ret_billing_advance');
									}
								}
							}
						}
					}

					$billPurchase = (isset($_POST['purchase']) ? $_POST['purchase'] : '');

					//echo "<pre>"; print_r($billPurchase);exit;

					if (!empty($billPurchase)) {

						$arrayPurchaseBill = array();

						foreach ($billPurchase['is_est_details'] as $key => $val) {

							if ($billPurchase['is_est_details'][$key] == 1) {

								$arrayPurchaseBill = array(

									'bill_id' => $id,

									'metal_type' => $billPurchase['metal_type'][$key],

									'item_type' => $billPurchase['itemtype'][$key],

									'est_id' => $billPurchase['estid'][$key],

									'gross_wt' => $billPurchase['gross'][$key],

									'stone_wt' => $billPurchase['stone_wt'][$key],

									'dust_wt' => $billPurchase['dust_wt'][$key],

									'wastage_percent' => ($billPurchase['wastage'][$key] !== '' && $billPurchase['wastage'][$key] !== null) ? $billPurchase['wastage'][$key] : 0,

									'rate' => $billPurchase['billamount'][$key],

									'rate_per_grm' => $billPurchase['rate_per_grm'][$key],

									'bill_discount' => empty($billPurchase['discount'][$key]) ? 0 : $billPurchase['discount'][$key],
									'id_employee'   => (!empty($billPurchase['id_employee'][$key]) ? $billPurchase['id_employee'][$key] : NULL)

								);
							}

							if (!empty($arrayPurchaseBill)) {

								if (isset($billPurchase['old_metal_sale_id'][$key]) && ($billPurchase['old_metal_sale_id'][$key] != '')) {

									$update_status = $this->$model->updateData($arrayPurchaseBill, 'old_metal_sale_id', $billPurchase['old_metal_sale_id'][$key], 'ret_bill_old_metal_sale_details');

									//Update Estimation Items

									$this->$model->updateData(array('purchase_status' => 1, 'bill_id' => $id), 'old_metal_sale_id', $billPurchase['est_itm_id'][$key], 'ret_estimation_old_metal_sale_details');
								} else {

									$oldMetal = $this->$model->insertData($arrayPurchaseBill, 'ret_bill_old_metal_sale_details');

									if ($oldMetal) {

										if ($billPurchase['stone_details'][$key]) {

											$stone_details = json_decode($billPurchase['stone_details'][$key], true);

											foreach ($stone_details as $stone) {

												$stone_data = array(

													'bill_id'        => $id,

													'old_metal_sale_id' => $oldMetal,

													'pieces'        => $stone['stone_pcs'],

													'wt'            => $stone['stone_wt'],

													'stone_id'      => $stone['stone_id'],

													'price'         => $stone['stone_price'],

													'item_type'     => 2 //Purchase item

												);

												$stoneInsert = $this->$model->insertData($stone_data, 'ret_billing_item_stones');
											}
										}

										//Update Estimation Items

										$this->$model->updateData(array('purchase_status' => 1, 'bill_id' => $id), 'old_metal_sale_id', $billPurchase['est_itm_id'][$key], 'ret_estimation_old_metal_sale_details');
									}
								}
							}
						}
					}
				}

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					$this->session->set_flashdata('chit_alert', array('message' => 'Billing updated successfully', 'class' => 'success', 'title' => 'Update Billing'));

					redirect('admin_ret_billing/billing/list');
				} else {

					$this->db->trans_rollback();

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Update Billing'));

					redirect('admin_ret_billing/billing/list');
				}

				break;

			case 'cancell':

				$upd_data = array(

					"bill_status"	=> 2,

					'updated_time'	=> date("Y-m-d H:i:s"),

					'cancelled_date' => date("Y-m-d H:i:s"),

					'updated_by'	=> $this->session->userdata('uid')

				);

				$this->db->trans_begin();

				$status = $this->$model->updateData($upd_data, 'bill_id', $id, 'ret_billing');

				if ($status) {

					$bill_detail = $this->$model->get_bill_detail($id);

					foreach ($bill_detail as $bill) {

						//Estimation

						$updData = array('purchase_status' => 0, 'bil_detail_id' => NULL);

						$this->$model->updateData($updData, 'bil_detail_id', $bill['bill_det_id'], 'ret_estimation_items');

						//Estimation Old Items

						$oldUpdata = array('purchase_status' => 0, 'bill_id' => NULL);

						$this->$model->updateData($oldUpdata, 'bill_id', $id, 'ret_estimation_old_metal_sale_details');

						if ($bill['tag_id'] != '') {

							$this->$model->updateData(array('tag_status' => 6, 'updated_time' => date("Y-m-d H:i:s"), 'updated_by' => $this->session->userdata('uid')), 'tag_id', $bill['tag_id'], 'ret_taging');

							$log_data = array(

								'tag_id'	  => $bill['tag_id'],

								'date'		  => date("Y-m-d H:i:s"),

								'status'	  => 6,

								'from_branch' => NULL,

								'to_branch'	  => $bill['id_branch'],

								'created_on'  => date("Y-m-d H:i:s"),

								'created_by'  => $this->session->userdata('uid'),

							);

							$this->$model->insertData($log_data, 'ret_taging_status_log'); //Update Tag lot status

							$tag_status = $this->$model->get_tag_status($bill['tag_id']);

							if ($tag_status['id_section'] != null && $tag_status['id_section'] != '') {

								$secttag_log = array(

									'tag_id'	        => $bill['tag_id'],

									'date'		        => date("Y-m-d H:i:s"),

									'status'	        => 6,

									'from_branch'       => NULL,

									'to_branch'	        => $bill['id_branch'],

									'from_section'      => NULL,

									'to_section'        => $tag_status['id_section'],

									'created_on'        => date("Y-m-d H:i:s"),

									'created_by'        => $this->session->userdata('uid'),

								);

								$this->$model->insertData($secttag_log, 'ret_section_tag_status_log');
							}
						}

						//stock maintaince

						$existData = array('id_product' => $bill['product_id'], 'id_design' => $bill['design_id'], 'id_branch' => $bill['id_branch']);

						$isExist = $this->$model->checkNonTagItemExist($existData);

						if ($isExist['status'] == TRUE) {

							$nt_data = array(

								'id_nontag_item' => $isExist['id_nontag_item'],

								'no_of_piece'   => $bill['no_of_piece'],

								'gross_wt'		=> $bill['gross_wt'],

								'net_wt'		=> $bill['net_wt'],

								'updated_by'	=> $this->session->userdata('uid'),

								'updated_on'	=> date('Y-m-d H:i:s'),

							);

							$this->$model->updateNTData($nt_data, '+');
						}

						//stock maintaince

					}
				}

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					$this->session->set_flashdata('chit_alert', array('message' => 'Bill No.' . $billno . ' cancelled successfully', 'class' => 'success', 'title' => 'Cancell Bill'));
				} else {

					$this->db->trans_rollback();

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Cancell Bill'));
				}

				redirect('admin_ret_billing/billing/list');

				break;

			case 'ajaxapprovallist':

				$list = $this->$model->ajax_getApprovalBillingList($_POST);

				$profile = $this->admin_settings_model->profileDB("get", $this->session->userdata('profile'));

				$access = $this->admin_settings_model->get_access('admin_ret_billing/billing/approvallist');

				$data = array(

					'list'  => $list,

					'access' => $access,

					'profile' => $profile

				);

				echo json_encode($data);

				break;

			default:

				$list = $this->$model->ajax_getBillingList($_POST);

				$profile = $this->admin_settings_model->profileDB("get", $this->session->userdata('profile'));

				$access = $this->admin_settings_model->get_access('admin_ret_billing/billing/list');

				$data = array(

					'list'  => $list,

					'access' => $access,

					'profile' => $profile

				);

				echo json_encode($data);
		}
	}

	public function createNewCustomer()
	{

		$model = "ret_billing_model";

		if (!empty($_POST['cusName']) && !empty($_POST['cusMobile']) && !empty($_POST['cusBranch'])) {

			$data = $this->$model->createNewCustomer($_POST['cusName'], $_POST['cusMobile'], $_POST['cusBranch'], $_POST['id_village'], $_POST['id_country'], $_POST['id_state'], $_POST['id_city'], $_POST['address1'], $_POST['address2'], $_POST['address3'], $_POST['pincode'], ($_POST['mail'] != '' ? $_POST['mail'] : null), $_POST['cus_type'], $_POST['pan_no'], $_POST['aadharid'], ($_POST['gst_no'] ? $_POST['gst_no'] : null), $_POST['title'], $_POST['id_profession'], $_POST['gender'], $_POST['date_of_birth'], $_POST['date_of_wed'], $_POST['dl_no'], $_POST['pp_no'], $_POST['is_vip']);

			if (isset($_FILES['cust_img']['name']) && $data['success'] == true) {

				// $data['response'] can be array (on success) or stdClass (on duplicate mobile)
				$id_cus = is_object($data['response'])
					? $data['response']->id_customer
					: (isset($data['response']['id_customer']) ? $data['response']['id_customer'] : null);

				if ($id_cus) {
					$img_path = "assets/img/customer/" . $id_cus . "/customer.jpg";
					$path     = "assets/img/customer/" . $id_cus;

					if ($this->set_image($id_cus, $img_path, 'cust_img', $path)) {
						$data['image_upload'] = $img_path;
					}
				}
			}

			echo json_encode($data);

		} else {

			echo json_encode(array("success" => FALSE, "response" => array(), "message" => "Please fill all the required fields"));
		}
	}

	public function updateNewCustomer()
	{

		$model = "ret_billing_model";

		if (!empty($_POST['cusName']) && !empty($_POST['cusMobile']) && !empty($_POST['cusBranch'])) {

			//die;

			$data = $this->$model->updateNewCustomer($_POST['id_customer'], $_POST['cusName'], $_POST['cusMobile'], $_POST['cusBranch'], $_POST['id_village'], $_POST['id_country'], $_POST['id_state'], $_POST['id_city'], $_POST['address1'], $_POST['address2'], $_POST['address3'], $_POST['pincode'], ($_POST['mail'] != '' ? $_POST['mail'] : ''), $_POST['cus_type'], $_POST['pan_no'], $_POST['aadharid'], ($_POST['gst_no'] ? $_POST['gst_no'] : null), $_POST['title'], $_POST['id_profession'], $_POST['gender'], $_POST['date_of_birth'], $_POST['date_of_wed'], $_POST['dl_no'], $_POST['pp_no'], $_POST['is_vip']);

			if (isset($_FILES['cust_img']['name'])) {

				$img_path = "assets/img/customer/" . $_POST['id_customer'] . "/customer.jpg";

				$path = "assets/img/customer/" . $_POST['id_customer'];

				if ($this->set_image($_POST['id_customer'], $img_path, 'cust_img', $path)) {

					$data['image_upload'] = $img_path;
				}
			}

			echo json_encode($data);
		} else {

			echo json_encode(array("success" => FALSE, "response" => array(), "message" => "Please fill all the required fields"));
		}
	}

	/*public function getEstimationDetails(){

		$model = "ret_billing_model";

		if((!empty($_POST['estId']) || !empty($_POST['tag_code']) || !empty($_POST['order_no']) || !empty($_POST['old_tag_id'])) && !empty($_POST['billType'])){

			$data = $this->$model->getEstimationDetails($_POST['estId'], $_POST['billType'], $_POST['id_branch'], $_POST['order_no'],$_POST['fin_year'],$_POST['metal_type'],$_POST['tag_code'] , $_POST['old_tag_id']);

			if(sizeof($data['item_details'])>0 || sizeof($data['old_matel_details'])>0 || sizeof($data['order_details'])>0  )

			{

				echo json_encode(array('success' => TRUE, 'message' => 'Records reterived successfully.', 'responsedata' => $data));

			}

			else

			{

                if ($_POST['estId'] != '') {
					$status = $this->$model->is_estno_already_billed($_POST['estId'], $_POST['id_branch']);
					if ($status != 1) {
						echo json_encode(array('success' => FALSE, 'message' => 'No record found for given details...'));
					} else {
						echo json_encode(array('success' => False, 'message' => 'Estimation Number Already Billed'));
					}
				} else {
					echo json_encode(array('success' => FALSE, 'message' => 'No record found for given details'));
				}

			}

		}else{

			echo json_encode(array("success" => FALSE, "response" => array(), "message" => "Please fill all the required fields"));

		}

	}*/
	public function getEstimationDetails()
	{
		$model = "ret_billing_model";
		if ((!empty($_POST['estId']) || !empty($_POST['tag_code']) || !empty($_POST['order_no']) || !empty($_POST['old_tag_id']))) {
			// BIL-CLT05 FIX: Reset orphaned estimation items from cancelled bills
			// before loading, so all items are available for re-billing
			if (!empty($_POST['estId'])) {
				$this->$model->resetOrphanedEstimationItems($_POST['estId'], $_POST['id_branch']);
			}
			$data = $this->$model->getEstimationDetails($_POST['estId'], $_POST['billType'], $_POST['id_branch'], $_POST['order_no'], $_POST['fin_year'], $_POST['metal_type'], $_POST['tag_code'],$_POST['old_tag_id']);
			//echo "<pre>";print_r($data);exit;
			if (sizeof($data['item_details']) > 0 || sizeof($data['old_matel_details']) > 0 || sizeof($data['order_details']) > 0) {
				echo json_encode(array('success' => TRUE, 'message' => 'Records reterived successfully.', 'responsedata' => $data));
			} else {
				if ($_POST['estId'] != '') {
					$status = $this->$model->is_estno_already_billed($_POST['estId'], $_POST['id_branch']);
					if ($status != 1) {
						echo json_encode(array('success' => FALSE, 'message' => 'No record found for given details...'));
					} else {
						echo json_encode(array('success' => False, 'message' => 'Estimation Number Already Billed'));
					}
				} else {
					echo json_encode(array('success' => FALSE, 'message' => 'No record found for given details'));
				}
			}
		} else {
			echo json_encode(array("success" => FALSE, "response" => array(), "message" => "Please fill all the required fields"));
		}
	}


	public function getEstimationDetailsTags()
	{

		$model = "ret_billing_model";

		if ((!empty($_POST['estId']) || !empty($_POST['order_no'])) && !empty($_POST['billType'])) {

			$data = $this->$model->getEstimationDetailsTags($_POST['estId'], $_POST['billType'], $_POST['id_branch'], $_POST['order_no'], $_POST['fin_year'], $_POST['metal_type']);

			if (sizeof($data['item_details']) > 0) {

				echo json_encode(array('success' => TRUE, 'message' => 'Records reterived successfully.', 'responsedata' => $data));
			} else {

				echo json_encode(array('success' => FALSE, 'message' => 'No record found for given details'));
			}
		} else {

			echo json_encode(array("success" => FALSE, "response" => array(), "message" => "Please fill all the required fields"));
		}
	}

	public function getAllTaxgroupItems()
	{

		$model = "ret_billing_model";

		$data = $this->$model->getAllTaxgroupItems();

		echo json_encode($data);
	}

	public function getCustomersBySearch()
	{

		$model = "ret_billing_model";

		$data = $this->$model->getAvailableCustomers($_POST['searchTxt']);

		echo json_encode($data);
	}

	public function getTaggingBySearch()
	{

		$model = "ret_billing_model";

		$data = $this->$model->getTaggingBySearch($_POST['tagId']);

		echo json_encode($data);
	}

	public function getProductBySearch()
	{

		$model = "ret_billing_model";

		$data = $this->$model->getProductBySearch($_POST['searchTxt']);

		echo json_encode($data);
	}

	public function getProductDesignBySearch()
	{

		$model = "ret_billing_model";

		$data = $this->$model->getProductDesignBySearch($_POST['searchTxt'], $_POST['ProCode']);

		echo json_encode($data);
	}

	public function getMetalTypes()
	{

		$model = "ret_billing_model";

		$data = $this->$model->getMetalTypes();

		echo json_encode($data);
	}

	//chit acc

	public function get_scheme_accounts()

	{

		$model = "ret_billing_model";

		$searchTxt = $this->input->post('searchTxt');

		$id_customer = (isset($_POST['id_customer']) ? $_POST['id_customer'] : '');

		$id_scheme_acc = $this->input->post('id_scheme_account');

		$id_branch = $this->input->post('id_branch');

		$data = $this->$model->get_closed_accounts($searchTxt, $id_customer, $id_scheme_acc, $id_branch);

		echo json_encode($data);
	}

	//chit acc

	//Advance Adj

	public function get_advance_details()

	{

		$model = "ret_billing_model";

		$bill_cus_id = $this->input->post('bill_cus_id');

		$data = $this->$model->get_advance_details($bill_cus_id, $_POST['is_eda'], $_POST['id_branch']);

		echo json_encode($data);
	}

	//Advance Adj

	public function getBillDetails()

	{

		$model = "ret_billing_model";

		if (!empty($_POST['billNo']) && !empty($_POST['billType'])) {

			$data = $this->$model->getBillData($_POST['billNo'], $_POST['billType'], $_POST['id_branch'], $_POST['fin_year'], $_POST['metal_type'], $_POST['is_eda'], $_POST['id_customer']);

			if (sizeof($data['item_details']) > 0) {

				echo json_encode(array('success' => TRUE, 'message' => 'Records reterived successfully.', 'responsedata' => $data));
			} else {

				echo json_encode(array('success' => FALSE, 'message' => 'No record found for given details'));
			}
		} else {

			echo json_encode(array("success" => FALSE, "response" => array(), "message" => "Please fill all the required fields"));
		}
	}

	public function get_return_Bill_details()

	{

		$model = "ret_billing_model";

		if (!empty($_POST['billNo']) && !empty($_POST['billType'])) {

			$data = $this->$model->getreturnBillData($_POST['billNo'], $_POST['billType'], $_POST['id_branch']);

			if (!empty($data)) {

				echo json_encode(array('success' => TRUE, 'message' => 'Records reterived successfully.', 'responsedata' => $data));
			} else {

				echo json_encode(array('success' => FALSE, 'message' => 'No record found for given details'));
			}
		} else {

			echo json_encode(array("success" => FALSE, "response" => array(), "message" => "Please fill all the required fields"));
		}
	}

	public function getBillingDetails()

	{

		$model = "ret_billing_model";

		if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {

			$data = $this->$model->getBilling_details($_POST['from_date'], $_POST['to_date'], $_POST['id_branch'], $_POST['bill_cus_id'], $_POST['bill_type']);

			if (!empty($data)) {

				echo json_encode(array('success' => TRUE, 'message' => 'Records reterived successfully.', 'responsedata' => $data));
			} else {

				echo json_encode(array('success' => FALSE, 'message' => 'No record found for given details'));
			}
		} else {

			echo json_encode(array("success" => FALSE, "response" => array(), "message" => "Please fill all the required fields"));
		}
	}

	public function getCreditBillDetails()

	{

		$model = "ret_billing_model";

		if (!empty($_POST['billNo']) && !empty($_POST['billType'])) {
			$metal_type = isset($_POST['metal_type']) ? $_POST['metal_type'] : '';
			$data = $this->$model->getCreditBillDetails($_POST['billNo'], $_POST['billType'], $_POST['id_branch'], $_POST['fin_year'], $_POST['is_eda'], $metal_type);

			if (sizeof($data['bill_details']) > 0) {
				if (isset($data['bill_details']['id_metal']) && !empty($metal_type)) {
					if ($data['bill_details']['id_metal'] != $metal_type) {
						echo json_encode(array('success' => FALSE, 'message' => 'The selected bill belongs to a different metal type.'));
						return;
					}
				}
				echo json_encode(array('success' => TRUE, 'message' => 'Records reterived successfully.', 'responsedata' => $data));
			} else {

				echo json_encode(array('success' => FALSE, 'message' => 'No record found for given details'));
			}
		} else {

			echo json_encode(array("success" => FALSE, "response" => array(), "message" => "Please fill all the required fields"));
		}
	}

	function sendotp()

	{

		$model = "ret_billing_model";

		$mobile_num     = $this->input->post('mobile');

		$send_resend     = $this->input->post('send_resend');

		$sent_otp = '';

		if ($mobile_num != '') {

			$this->db->trans_begin();

			$this->session->unset_userdata("bill_chit_otp");

			$this->session->unset_userdata("bill_chit_otp_exp");

			$OTP = mt_rand(100001, 999999);

			$this->session->set_userdata('bill_chit_otp', $OTP);

			$this->session->set_userdata('bill_chit_otp_exp', time() + 60);

			$message = "Hi Your OTP  For Chit Billing is :  " . $OTP . " Will expire within 1 minute.";

			$otp_gen_time = date("Y-m-d H:i:s");

			$insData = array(

				'mobile' => $mobile_num,

				'otp_code' => $OTP,

				'otp_gen_time' => date("Y-m-d H:i:s"),

				'module' => 'Billing Chit Utilization',

				'send_resend' => $send_resend,

				'id_emp' => $this->session->userdata('uid')

			);

			$insId = $this->$model->insertData($insData, 'otp');
		}

		if ($insId) {

			$this->db->trans_commit();

			//$this->send_sms($mobile_num,$message);

			$status = array('status' => true, 'msg' => 'OTP sent Successfully', 'otp' => $OTP);
		} else {

			$this->db->trans_rollback();

			$status = array('status' => false, 'msg' => 'Unabe To Send Try Again');
		}

		echo json_encode($status);
	}

	function update_otp()

	{

		$model = "ret_billing_model";

		$user_otp = $this->input->post('user_otp');

		$otp = $this->session->userdata('bill_chit_otp');

		if ($otp == $user_otp) {

			if (time() >= $this->session->userdata('bill_chit_otp_exp')) {

				$this->session->unset_userdata('bill_chit_otp');

				$this->session->unset_userdata('bill_chit_otp_exp');

				$data = array('status' => false, 'msg' => 'OTP has been expired');
			} else {

				$updData = array('is_verified' => 1, 'verified_time' => date("Y-m-d H:i:s"));

				$update_otp = $this->$model->updateData($updData, 'otp_code', $user_otp, 'otp');

				$data = array('status' => true, 'msg' => 'OTP Verified Successfully');
			}
		} else {

			$data = array('status' => false, 'msg' => 'Please Enter Valid OTP');
		}

		echo json_encode($data);
	}

	function send_sms($mobile, $message, $dlt_te_id)

	{

		if ($this->config->item('sms_gateway') == '1') {

			$this->sms_model->sendSMS_MSG91($mobile, $message, '', $dlt_te_id);
		} elseif ($this->config->item('sms_gateway') == '2') {

			$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
		}
	}

// In admin_ret_billing controller
function billing_invoice($id, $type = "")
{
    $this->load->helper('receipt');
    $model = "ret_billing_model";
    
    // Get common data
    $data = get_receipt_data($id, $type, false); // false = not softcopy
    
    $bill_type = $this->$model->get_ret_settings('bill_format');
    
    // QR Code generation for admin preview
    $data['qrfilename'] = generate_qr_code($data['billing']['bill_id']);
    $data['billing']['app_qrcode'] = $this->config->item('base_url') . "mobile_app_qrcode/skj_app_qrcode.png";
    $data['billing']['playstore'] = $this->config->item('base_url') . "mobile_app_qrcode/playstore.png";
    
    // Print tracking (only for admin preview)
    $print_taken = $data['billing']['print_taken'];
    if ($print_taken == 0) {
        $print_taken++;
        $this->$model->updateData(array('print_taken' => $print_taken), 'bill_id', $id, 'ret_billing');
    } else {
        $this->$model->insertData(array('bill_id' => $id, 'id_employee' => $this->session->userdata('uid'), 'print_date' => date("Y-m-d H:i:s")), 'ret_bill_duplicate_copy');
    }
    
    $this->load->helper(array('dompdf', 'file'));
    $this->load->model('print_template_model');
    
    // Load the dedicated template helper (tpl_map_billing_data_to_template)
    // receipt_helper is already loaded above for get_receipt_data() / generate_qr_code()
    $this->load->helper('template_receipt');
    
    // Try to render using the dynamic print template
    $branch_id = isset($data['billing']['id_branch']) ? $data['billing']['id_branch'] : null;
    $mapped_data = tpl_map_billing_data_to_template($data);
    
    // Merge the full raw ret_billing data with the flattened mapped data
    // Elevate inner arrays (billing, comp_details) to the root so users can use {{bill_no}} directly without dot notation.
    $combined_data = $data;
    if (isset($data['billing']) && is_array($data['billing'])) {
        $combined_data = array_merge($combined_data, $data['billing']);
    }
    if (isset($data['comp_details']) && is_array($data['comp_details'])) {
        $combined_data = array_merge($combined_data, $data['comp_details']);
    }
    
    // Merge mapped data last so it takes precedence
    $combined_data = array_merge($combined_data, $mapped_data);
    
    
    // Ensure conditional image blocks in templates are preserved
    // (e.g., QR codes embedded as base64 in template HTML)
    $combined_data['qr_code_image'] = true;
    
    // Use bill_type from billing data (which corresponds to IDs 1-15)
    $bill_type_id = isset($data['billing']['bill_type']) ? $data['billing']['bill_type'] : 1; 
    
    
    // Check if template-based printing is enabled
    $template_based = $this->$model->get_ret_settings('template_based');
    
    if ($template_based == 3) {
        // V2 Konva Canvas template — try Konva rendering first
        $this->load->helper('konva_receipt');
        $konva_tpl = find_konva_template($bill_type_id, $branch_id);
        if ($konva_tpl) {
            $rendered = render_konva_template($konva_tpl['id_template'], $combined_data);
            if ($rendered !== false && !empty(trim(strip_tags($rendered)))) {
                echo $rendered;
                exit;
            }
        }
        // Konva template not found or rendered empty — fall through to HTML template
    }
    
    if ($template_based == 1 || $template_based == 3) {
        // Use designer template (template_html from print_templates table)
        $template_html = $this->print_template_model->render($bill_type_id, $combined_data, $branch_id);
        
        if ($template_html !== false && !empty(trim(strip_tags($template_html)))) {
            echo $template_html;
            exit;
        }
    }
    
    // Fallback to legacy view
    if ($bill_type == 0) {
        $html = $this->load->view('billing/print/receipt_billing', $data, true);
    } else {
        $html = $this->load->view('billing/print/bill_format_2', $data, true);
    }
    
    echo $html;
    exit;
}

	function repair_order_thermal_print($id)

	{

		$model = "ret_billing_model";

		$type = "";

		$data['billing'] = $this->$model->getBillingDetails($id, $type);

		$data['payment'] = $this->$model->getPaymentDetails($id);

		$data['metal_rate'] = $this->$model->getBillingMetalrate($data['billing']['id_branch'], $data['billing']['bill_date']);

		$data['repair_details'] = $this->$model->get_repair_item_details($id);

		$data['comp_details'] = $this->$model->getCompanyDetails($data['billing']['id_branch']);

		$data['settings']		= $this->$model->get_retSettings();

		$data['receiptDetails'] =  $this->$model->get_billing_advance_details($id);

		//echo "<pre>"; print_r($data); echo "</pre>";exit;

		$print_taken = $data['billing']['print_taken'];

		if ($print_taken == 0) {

			$print_taken++;

			$this->$model->updateData(array('print_taken' => $print_taken), 'bill_id', $id, 'ret_billing');
		} else {

			$this->$model->insertData(array('bill_id' => $id, 'id_employee' => $this->session->userdata('uid'), 'print_date' => date("Y-m-d H:i:s")), 'ret_bill_duplicate_copy');
		}

		$this->load->helper(array('dompdf', 'file'));

		$dompdf = new DOMPDF();

		$html = $this->load->view('billing/print/thermal_print', $data, true);

		$dompdf->load_html($html);

		$dompdf->set_paper("a4", "portriat");

		$dompdf->render();

		while(ob_get_level()) ob_end_clean();
		$dompdf->stream("Receipt.pdf", array('Attachment' => 0));
	}

	//issue and receipt

	public function issue($type = "", $id = "", $billno = "")

	{
		$model = "ret_billing_model";

		$SETT_MOD = "admin_settings_model";

		switch ($type) {

			case 'add':

				$data['settings']		= $this->$model->get_retSettings();

				$data['main_content'] = "billing/issueReceipt/issueForm";

				$this->load->view('layout/template', $data);

				break;

			case 'list':

				$data['main_content'] = "billing/issueReceipt/issueList";

				$data['access'] = $this->$SETT_MOD->get_access('admin_ret_billing/issue/list');


				$this->load->view('layout/template', $data);

				break;

				/*case 'save':

				$addData=$_POST['issue'];

				$payment=$_POST['payment'];

				$card_pay_details	= json_decode($payment['card_pay'],true);

				$cheque_details	    = json_decode($payment['chq_pay'],true);

				$net_banking_details = json_decode($payment['net_bank_pay'],true);

				//echo "<pre>"; print_r($addData);exit;

				$bill_no = $this->$model->bill_no_generate($addData['id_branch']);

			    $dCData = $this->admin_settings_model->getBranchDayClosingData($addData['id_branch']);

				$bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

				$fin_year  = $this->$model->get_FinancialYear();

				$insData=array(

				    'bill_no'	        =>$bill_no,

				    'fin_year_code'     => $fin_year['fin_year_code'],

				    'bill_date'         =>$bill_date,

					'type'              =>1,

					'id_branch'         => $addData['id_branch'],

					'mobile'            =>$addData['mobile'],

					'name'              =>$addData['barrower_name'],

					'amount'            =>$addData['amount'],

					'issue_to'          =>$addData['issue_to'],

					'issue_type'        =>$addData['issue_type'],

					'id_customer'       =>($addData['id_customer']!='' ? $addData['id_customer'] :NULL),

				    'id_employee'       =>$this->session->userdata('uid'),

					'id_acc_head'       =>($addData['id_acc_head']!='' ? $addData['id_acc_head'] :NULL),

					'narration'	        =>($addData['narration']!='' ?$addData['narration'] :NULL),

					'created_by'        =>$this->session->userdata('uid'),

					'counter_id'        => ($this->session->userdata('counter_id')!='' ? $this->session->userdata('counter_id'):NULL),

					'created_on'        => date("Y-m-d H:i:s"),

					);

				$this->db->trans_begin();

				$updData=$_POST['payment'];

				 //echo "<pre>"; print_r($updData);exit;

	 			$insId = $this->$model->insertData($insData,'ret_issue_receipt');

	 			//print_r($this->db->last_query());exit;

	 			if($insId)

	 			{

	 				$updData=$_POST['payment'];

	 				$pay_data=array(

	 					'id_issue_rcpt'	=>$insId,

	 					'payment_amount'=>$updData['cash_payment'],

	 					'payment_mode'	=>'Cash',

	 					'payment_status'=>1,

	 					'type'			=>2,

	 					'payment_type'	=>'Manual',

						'payment_date'	=>date("Y-m-d H:i:s"),

						'created_time'	=> date("Y-m-d H:i:s"),

						'created_by'	=> $this->session->userdata('uid')

	 				);

	 				$this->$model->insertData($pay_data,'ret_issue_rcpt_payment');

	 				if(sizeof($card_pay_details)>0)

	 					{

	 						foreach($card_pay_details as $card_pay)

		 					{

								$arrayCardPay[]=array(

								'id_issue_rcpt'	=>$insId,

								'payment_amount'=>$card_pay['card_amt'],

								'payment_status'=>1,

								'type'			=>2,

								'payment_date'	=>date("Y-m-d H:i:s"),

								'payment_mode'	=>($card_pay['card_type']==1 ?'CC':'DC'),

								'card_no'		=>($card_pay['card_no']!='' ? $card_pay['card_no']:NULL),

								'created_time'	=> date("Y-m-d H:i:s"),

								'created_by'	=> $this->session->userdata('uid')

								);

	 						}

		 						if(!empty($arrayCardPay)){

									$cardPayInsert = $this->$model->insertBatchData($arrayCardPay,'ret_issue_rcpt_payment');

								}

	 					}

	 					if(sizeof($cheque_details)>0)

	 					{

	 						foreach($cheque_details as $chq_pay)

		 					{

								$arraychqPay[]=array(

									'id_issue_rcpt'	=>$insId,

									'payment_amount'=>$chq_pay['payment_amount'],

									'payment_status'=>1,

									'type'			=>2,

									'payment_date'	=>date("Y-m-d H:i:s"),

									'cheque_date'	=>date("Y-m-d H:i:s"),

									'payment_mode'	=>'CHQ',

									'cheque_no'		=>($chq_pay['cheque_no']!='' ? $chq_pay['cheque_no']:NULL),

									'bank_name'		=>($chq_pay['bank_name']!='' ? $chq_pay['bank_name']:NULL),

									'bank_branch'	=>($chq_pay['bank_branch']!='' ? $chq_pay['bank_branch']:NULL),

									'created_time'	=> date("Y-m-d H:i:s"),

									'created_by'	=> $this->session->userdata('uid')

								);

	 						}

		 						if(!empty($arraychqPay)){

									$chqPayInsert = $this->$model->insertBatchData($arraychqPay,'ret_issue_rcpt_payment');

								}

	 					}

	 					if(sizeof($net_banking_details)>0)

	 					{

	 						foreach($net_banking_details as $nb_pay)

		 					{

								$arrayNBPay[]=array(

									'id_issue_rcpt'	=>$insId,

									'payment_amount'=>$nb_pay['amount'],

									'payment_status'=>1,

									'type'			=>2,

									'payment_date'	=>date("Y-m-d H:i:s"),

									'payment_mode'	=>'NB',

									'payment_ref_number'=>($nb_pay['ref_no']!='' ? $nb_pay['ref_no']:NULL),

									'NB_type'       =>($nb_pay['nb_type']!='' ? $nb_pay['nb_type']:NULL),

									'created_time'	=> date("Y-m-d H:i:s"),

									'created_by'	=> $this->session->userdata('uid')

								);

	 						}

		 						if(!empty($arrayNBPay)){

									$NbPayInsert = $this->$model->insertBatchData($arrayNBPay,'ret_issue_rcpt_payment');

								}

	 					}

	 					if($addData['issue_type']==3)

	 					{

	 					    $insWallet=array(

    						'id_ret_wallet'		=>$addData['id_ret_wallet'],

    						'id_issue_receipt'	=>$insId,

    						'amount'			=>$addData['amount'],

    						'transaction_type'	=>1,

    						'created_by' 		=>$this->session->userdata('uid'),

    						'created_on' 		=> date("Y-m-d H:i:s"),

    						'remarks'	 		=>'Advace Refund Amount'

    						 );

    						$this->$model->insertData($insWallet,'ret_wallet_transcation');

	 					}

	 			}

				if($this->db->trans_status()===TRUE)

					{

						$this->db->trans_commit();

						$this->session->set_flashdata('chit_alert',array('message'=>'Issue Given successfully','class'=>'success','title'=>'Add Issue'));

					}

					else

					{

						$this->db->trans_rollback();

						echo $this->db->_error_message()."<br/>";

						echo $this->db->last_query();exit;

						$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Add Issue'));

					}

					redirect('admin_ret_billing/issue/list');

			break;*/

			case 'save':

				$addData = $_POST['issue'];

				// print_r($addData);exit;

				$payment = $_POST['payment'];

				$acc_head_details	= json_decode($payment['acc_head_details'], true);

				$card_pay_details	= json_decode($payment['card_pay'], true);

				$cheque_details	    = json_decode($payment['chq_pay'], true);

				$net_banking_details = json_decode($payment['net_bank_pay'], true);

				$multiple_receipt = json_decode($addData['multiple_receipt_id'], true);

				// echo "<pre>"; print_r($multiple_receipt);exit;

				$bill_no = $this->$model->bill_no_generate($addData['id_branch'], $addData['is_eda']);

				$dCData = $this->admin_settings_model->getBranchDayClosingData($addData['id_branch'], $addData['is_eda']);

				$bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

				$fin_year       = $this->$model->get_FinancialYear();

				$insData = array(

					'fin_year_code' => $fin_year['fin_year_code'],

					'bill_no'	    => $bill_no,

					'bill_date'     => $bill_date,

					'type'          => 1,

					'id_branch'     => $addData['id_branch'],

					'emp_id'        => $addData['emp_id'],

					'mobile'        => $addData['mobile'],

					'name'          => $addData['barrower_name'],

					'amount'        => $addData['amount'],

					'issue_to'      => $addData['issue_to'],

					'issue_type'    => $addData['issue_type'],

					'is_eda'        => $addData['is_eda'],

					'id_customer'   => ($addData['id_customer'] != '' ? $addData['id_customer'] : NULL),

					'id_employee'   => ($addData['id_employee'] != '' ? $addData['id_employee'] : NULL),

					'id_karigar'   => ($addData['id_karigar'] != '' ? $addData['id_karigar'] : NULL),

					'id_acc_head'   => ($addData['id_acc_head'] != '' ? $addData['id_acc_head'] : NULL),

					'narration'	    => ($addData['narration'] != '' ? $addData['narration'] : NULL),

					'created_by'    => $this->session->userdata('uid'),

					'counter_id'    => ($this->session->userdata('counter_id') != '' ? $this->session->userdata('counter_id') : NULL),

					'refno'         => !empty($addData['refno']) ? $addData['refno'] : NULL,

					'created_on'    => date("Y-m-d H:i:s"),

				);

				$this->db->trans_begin();

				$updData = $_POST['payment'];

	 			$insId = $this->$model->insertData($insData,'ret_issue_receipt');

				if ($insId) {

					if ($addData['pan_no'] != '' || $addData['aadhar_no'] != '' || $addData['driving_license_no'] != '' || $addData['pp_no'] != '') {

						$this->$model->updateData(array('pan'           		=>($addData['pan_no'] != '' && $addData['pan_no'] != "UNDEFINED" ? strtoupper($addData['pan_no']) : NULL),
														'aadharid'      		=> ($addData['aadhar_no'] != '' && $addData['aadhar_no'] != "UNDEFINED" ? strtoupper($addData['aadhar_no']) : NULL),
														'driving_license_no'    => ($addData['driving_license_no'] != '' && $addData['driving_license_no'] != "UNDEFINED"? strtoupper($addData['driving_license_no']) : NULL),
														'passport_no'      		=> ($addData['pp_no'] != '' && $addData['driving_license_no'] != "UNDEFINED" ? strtoupper($addData['pp_no']) : NULL), 
													),
												'id_customer', $addData['id_customer'], 'customer');
					}
				}

				if (sizeof($multiple_receipt) > 0) {

					foreach ($multiple_receipt as $refund) {

						$refundData = array(

							'id_issue_receipt' => $insId,

							'refund_receipt'   => $refund['id_issue_receipt'],

							'refund_amount'    => $refund['amount'],

						);

						$this->$model->insertData($refundData, 'ret_advance_refund');
					}
				}

				if ($insId) {

					$updData = $_POST['payment'];

					if ($updData['cash_payment'] != '') {

						$pay_data = array(

							'id_issue_rcpt'	=> $insId,

							'payment_amount' => $updData['cash_payment'],

							'payment_mode'	=> 'Cash',

							'payment_status' => 1,

							'type'			=> 2,

							'payment_type'	=> 'Manual',

							'payment_date'	=> date("Y-m-d H:i:s"),

							'created_time'	=> date("Y-m-d H:i:s"),

							'created_by'	=> $this->session->userdata('uid')

						);

						$this->$model->insertData($pay_data, 'ret_issue_rcpt_payment');
					}

					if (sizeof($card_pay_details) > 0) {

						foreach ($card_pay_details as $card_pay) {

							$arrayCardPay[] = array(

								'id_issue_rcpt'	=> $insId,

								'card_type'     => $card_pay['card_name'],

								'payment_amount' => $card_pay['card_amt'],

								'payment_status' => 1,

								'type'			=> 2,

								'payment_date'	=> date("Y-m-d H:i:s"),

								'payment_mode'	=> ($card_pay['card_type'] == 1 ? 'CC' : 'DC'),

								'card_no'		=> ($card_pay['card_no'] != '' ? $card_pay['card_no'] : NULL),

								'card_no'		=> ($card_pay['ref_no'] != '' ? $card_pay['ref_no'] : NULL),

								'created_time'	=> date("Y-m-d H:i:s"),

								'created_by'	=> $this->session->userdata('uid')

							);
						}

						if (!empty($arrayCardPay)) {

							$cardPayInsert = $this->$model->insertBatchData($arrayCardPay, 'ret_issue_rcpt_payment');
						}
					}

					if (sizeof($cheque_details) > 0) {

						foreach ($cheque_details as $chq_pay) {

							$cheque_deposit_date = ($chq_pay['cheque_date'] != '' ? date_create($chq_pay['cheque_date']) : NULL);

							$cheque_date = ($chq_pay['cheque_date'] != '' ? date_format($cheque_deposit_date, "Y-m-d") : NULL);

							$arraychqPay[] = array(

								'id_issue_rcpt'	=> $insId,

								'payment_amount' => $chq_pay['payment_amount'],

								'payment_status' => 1,

								'type'			=> 2,

								'payment_date'	=> date("Y-m-d H:i:s"),

								'cheque_date'		=> ($chq_pay['cheque_date'] != '' ? $cheque_date : NULL),

								'payment_mode'	=> 'CHQ',

								'cheque_no'		=> ($chq_pay['cheque_no'] != '' ? $chq_pay['cheque_no'] : NULL),

								'bank_name'		=> ($chq_pay['bank_name'] != '' ? $chq_pay['bank_name'] : NULL),

								'bank_branch'	=> ($chq_pay['bank_branch'] != '' ? $chq_pay['bank_branch'] : NULL),

								'id_bank'	=> ($chq_pay['id_bank'] != '' ? $chq_pay['id_bank'] : NULL),

								'created_time'	=> date("Y-m-d H:i:s"),

								'created_by'	=> $this->session->userdata('uid')

							);
						}

						if (!empty($arraychqPay)) {

							$chqPayInsert = $this->$model->insertBatchData($arraychqPay, 'ret_issue_rcpt_payment');
						}
					}

					if (sizeof($net_banking_details) > 0) {

						foreach ($net_banking_details as $nb_pay) {

							$arrayNBPay[] = array(

								'id_issue_rcpt'	    => $insId,

								'payment_amount'    => $nb_pay['amount'],

								'payment_status'    => 1,

								'type'			    => 2,

								'payment_date'	    => date("Y-m-d H:i:s"),

								'payment_mode'	    => 'NB',

								'payment_ref_number' => ($nb_pay['ref_no'] != '' ? $nb_pay['ref_no'] : NULL),

								'NB_type'           => ($nb_pay['nb_type'] != '' ? $nb_pay['nb_type'] : NULL),

								'net_banking_date'  => ($nb_pay['nb_date']!='' ? implode('-', array_reverse(explode('-', $nb_pay['nb_date']))):date("Y-m-d")),

								'id_bank'           => ($nb_pay['id_bank'] != '' && $nb_pay['id_bank'] != null ? $nb_pay['id_bank'] : NULL),

								'id_pay_device'     => ($nb_pay['id_device'] != '' && $nb_pay['id_device'] != null ? $nb_pay['id_device'] : NULL),

								'created_time'	    => date("Y-m-d H:i:s"),

								'created_by'	    => $this->session->userdata('uid')

							);
						}

						if (!empty($arrayNBPay)) {

							$NbPayInsert = $this->$model->insertBatchData($arrayNBPay, 'ret_issue_rcpt_payment');
						}
					}


					if (sizeof($acc_head_details) > 0) {

						foreach ($acc_head_details as $acc_head) {

							$arrayAcc[] = array(

								'id_issue_receipt'	    => $insId,

								'id_account_head'      => ($acc_head['id_account_head'] != '' ? $acc_head['id_account_head'] : NULL),

								'amount'               => ($acc_head['amount'] != '' ? $acc_head['amount'] : NULL),


							);
						}

						if (!empty($arrayAcc)) {

							$AccInsert = $this->$model->insertBatchData($arrayAcc, 'ret_issue_expense_details');

							// print_r($this->db->last_query());exit;

						}
					}


					if ($addData['issue_type'] == 3) {

						$this->$model->updateWalletData(array('amount' => $addData['amount'], 'weight' => 0, 'id_customer' => $addData['id_customer']), '-');
					}

					// echo "<pre>";print_r($multiple_receipt);exit;

					foreach ($multiple_receipt as $val) {

						if ($addData['issue_type'] == 3) {

							$insWallet = array(

								'id_ret_wallet'		=> $addData['id_ret_wallet'],

								'id_issue_receipt'	=> $val['id_issue_receipt'],

								'amount'			=> $val['amount'],

								'transaction_type'	=> 1,

								'created_by' 		=> $this->session->userdata('uid'),

								'created_on' 		=> date("Y-m-d H:i:s"),

								'remarks'	 		=> 'Advace Refund Amount'

							);

							$this->$model->insertData($insWallet, 'ret_wallet_transcation');
						}

						// echo "<pre>"; print_r($insWallet);

					}

					// exit;

				}

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					$this->session->set_flashdata('chit_alert', array('message' => 'Issue Given successfully', 'class' => 'success', 'title' => 'Add Issue'));
				} else {

					$this->db->trans_rollback();

					echo $this->db->_error_message() . "<br/>";

					echo $this->db->last_query();
					exit;

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Issue'));
				}
				if(!empty($cheque_details) && $addData['issue_type'] == 6){

					$cheque_url = base_url() . 'index.php/admin_ret_billing/issue/print_cheque/' . $insId;
					echo "<script>

						window.open('$cheque_url', '_blank');

					</script>";
				}

				$url = base_url() . 'index.php/admin_ret_billing/issue/issue_print/' . $insId;

					echo "<script>

						window.open('$url', '_blank');

						window.location.href = '" . base_url('index.php/admin_ret_billing/issue/list') . "';
						
					</script>";
					
				// redirect('admin_ret_billing/issue/list');

				break;

			case 'issue_print':

				$model = "ret_billing_model";

				$data['issue'] = $this->$model->get_issue_details($id);

				$data['comp_details'] = $this->$model->getCompanyDetails($data['issue']['id_branch']);

				$data['metal_rate'] = $this->$model->get_branchwise_rate($data['issue']['id_branch']);

				$data['payment'] = $this->$model->get_receipt_payment($id);

				$data['receipt_adv_details'] = $this->$model->get_receipt_advance_details($id);

				// ── Template-based rendering ──
				$ir_template_enabled = false;
				$ir_setting_query = $this->db->query("SELECT value FROM ret_settings WHERE name = 'issue_receipt_template_based' LIMIT 1");
				if ($ir_setting_query && $ir_setting_query->num_rows() > 0) {
					$ir_template_enabled = ($ir_setting_query->row()->value == 1);
				}

				if ($ir_template_enabled) {
					$this->load->helper('receipt');
					$this->load->model('print_template_model');
					$mapped_data = map_issue_receipt_data_to_template($data);

					$combined_data = $data;
					if (isset($data['issue']) && is_array($data['issue'])) {
						$combined_data = array_merge($combined_data, $data['issue']);
					}
					if (isset($data['comp_details']) && is_array($data['comp_details'])) {
						$combined_data = array_merge($combined_data, $data['comp_details']);
					}
					$combined_data = array_merge($combined_data, $mapped_data);

					$branch_id = isset($data['issue']['id_branch']) ? $data['issue']['id_branch'] : null;
					$template_html = $this->print_template_model->render(17, $combined_data, $branch_id);

					if ($template_html !== false && !empty(trim(strip_tags($template_html)))) {
						echo $template_html;
						exit;
					}
				}

				// Fallback to legacy view
				$this->load->helper(array('dompdf', 'file'));

				$dompdf = new DOMPDF();

				$html = $this->load->view('billing/issueReceipt/print/issue', $data, true);

				echo $html;
				exit;

				break;

			case 'cancel':

				$model = "ret_billing_model";

				// ── IR Cancel Validation ──
				$ir_id = $this->input->post('id_issue_receipt');
				$validation = $this->$model->validate_ir_cancel($ir_id, 'Issue');
				if ($validation['status'] === FALSE) {
					echo json_encode($validation);
					break;
				}

				$data = array(

					'bill_status' => 2,

					'updated_by'	=> $this->session->userdata('uid'),

					'updated_on'	=> date('Y-m-d H:i:s')
				);

				$this->db->trans_begin();

				$status = $this->$model->updateData($data, 'id_issue_receipt', $ir_id, 'ret_issue_receipt');

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					$return_data = array('status' => TRUE, 'message' => 'Payment Cancelled Successfully');
				} else {

					$this->db->trans_rollback();

					$return_data = array('status' => FALSE, 'message' => 'Unable to proceed the requested process');

					echo $this->db->_error_message() . "<br/>";

					echo $this->db->last_query();
					exit;

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Cancel Payment'));
				}

				echo json_encode($return_data);

				break;

			case 'petty_cash':

				$data = $this->$model->get_petty_cash_issue_amt($_POST);

				echo json_encode($data);

				break;
			case 'petty_cash_emp':

				$data = $this->$model->get_petty_cash_emp($_POST);

				echo json_encode($data);

				break;

			case 'print_cheque':

				$model = "ret_billing_model";

				// print_r($id);

				$data['issue'] = $this->$model->get_issue_details($id);

				$data['comp_details'] = $this->$model->getCompanyDetails($data['issue']['id_branch']);

				$data['metal_rate'] = $this->$model->get_branchwise_rate($data['issue']['id_branch']);

				$data['payment'] = $this->$model->get_receipt_payment($id);
				
				foreach($data['payment'] as &$p){
					if($p['payment_mode'] == 'CHQ'){
						$p['cheque_date'] = date('d-m-Y', strtotime($p['cheque_date']));
					}
				}
				
				$data['paymentdetails']['paydetails']['karigar'] = $data['issue']['name'];

				$data['receipt_adv_details'] = $this->$model->get_receipt_advance_details($id);		

				// echo"<pre>";print_r($data);exit;

        		$this->load->helper(array('dompdf', 'file'));

        	        $dompdf = new DOMPDF();

        			$html = $this->load->view(self::VIEW_FOLDER.'print/cheque', $data,true);

        			$dompdf->load_html($html);

        			// Custom paper size for cheque: approx 8 x 3.5 inches
					// 8 inches = 576 points. 3.5 inches = 252 points.
					$customPaper = array(0,0,576,252); 

        			$dompdf->set_paper($customPaper, "portrait" );

        			$dompdf->render();

        			while(ob_get_level()) ob_end_clean();
        			$dompdf->stream("Cheque.pdf",array('Attachment'=>0));

			break;


			// -------------------------------------------------------
			// TODO-06: Payment Mode Edit — Issue / Receipt
			// -------------------------------------------------------
			case 'paymentmode_edit':

				// Reuse Issue list edit permission — no separate menu entry needed
				$access = $this->$SETT_MOD->get_access('admin_ret_billing/issue/list');

				if (empty($access['edit'])) {
					$this->session->set_flashdata('chit_alert', array(
						'message' => 'You do not have permission to edit payment details.',
						'class'   => 'danger',
						'title'   => 'Access Denied'
					));
					redirect('admin_ret_billing/issue/list');
					break;
				}

				$data['billing']      = $this->$model->getFinancialYear();
				$data['main_content'] = 'billing/issueReceipt/payment_edit';
				$data['access']       = $access;

				$this->load->view('layout/template', $data);

			break;

			default:

				$list = $this->$model->ajax_getIssuetist($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_billing/receipt/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);
		}
	}
	// ---------------------------------------------------------------
	// TODO-07: AJAX — Fetch Issue/Receipt payment details for edit UI
	// Route: POST admin_ret_billing/ajax_get_issue_rcpt_payment
	// ---------------------------------------------------------------
	public function ajax_get_issue_rcpt_payment()
	{
		$model         = 'ret_billing_model';
		$id_issue_rcpt = (int) $this->input->post('id_issue_rcpt');
		$bill_no       = $this->input->post('bill_no');
		$fin_year      = $this->input->post('fin_year');

		// Support lookup by bill_no + fin_year if ID not provided
		if ($id_issue_rcpt <= 0 && !empty($bill_no)) {
			$id_issue_rcpt = (int) $this->$model->get_id_by_issue_billno($bill_no, $fin_year);
		}

		if ($id_issue_rcpt <= 0) {
			echo json_encode(array('success' => FALSE, 'message' => 'Invalid voucher ID.'));
			return;
		}

		$result = $this->$model->get_issue_rcpt_payment_details($id_issue_rcpt);

		if (empty($result['header'])) {
			echo json_encode(array('success' => FALSE, 'message' => 'Voucher not found.'));
			return;
		}

		echo json_encode(array(
			'success'  => TRUE,
			'header'   => $result['header'],
			'payments' => $result['payments'],
		));
	}

	// ---------------------------------------------------------------
	// TODO-08: AJAX — Update Issue/Receipt payment mode details
	// Route: POST admin_ret_billing/ajax_update_issue_rcpt_payment
	// ---------------------------------------------------------------
	public function ajax_update_issue_rcpt_payment()
	{
		$model = 'ret_billing_model';

		try {
			// --- 1. Input validation ---
			$id_issue_rcpt = (int) $this->input->post('id_issue_rcpt');
			$updated_by    = (int) $this->session->userdata('uid');

			if ($id_issue_rcpt <= 0) {
				echo json_encode(array('success' => FALSE, 'message' => 'Invalid voucher ID.'));
				return;
			}

			// --- 2. Load voucher ---
			$voucher_data = $this->$model->get_issue_rcpt_payment_details($id_issue_rcpt);
			$header       = isset($voucher_data['header']) ? $voucher_data['header'] : array();

			if (empty($header)) {
				echo json_encode(array('success' => FALSE, 'message' => 'Voucher not found.'));
				return;
			}

			// --- 3. Parse inputs ---
			$cash         = (float) $this->input->post('cash_payment');
			$card_pay     = json_decode($this->input->post('card_pay'),     true);
			$chq_pay      = json_decode($this->input->post('chq_pay'),      true);
			$net_bank_pay = json_decode($this->input->post('net_bank_pay'), true);

			if (!is_array($card_pay))     $card_pay     = array();
			if (!is_array($chq_pay))      $chq_pay      = array();
			if (!is_array($net_bank_pay)) $net_bank_pay = array();

			// --- 4. Guards ---

			// Guard 1: Cash cannot be negative
			if ($cash < 0) {
				echo json_encode(array('success' => FALSE, 'message' => 'Cash amount cannot be negative.'));
				return;
			}

			// Guard 2: Voucher must be active (not cancelled / returned)
			if ((int)$header['bill_status'] !== 1) {
				echo json_encode(array('success' => FALSE, 'message' => 'This voucher is not active and cannot be edited.'));
				return;
			}

			// Guard 3: Filter out zero/negative payment rows
			$card_pay     = array_values(array_filter($card_pay,     function($r){ return isset($r['card_amt'])       && (float)$r['card_amt']       > 0; }));
			$chq_pay      = array_values(array_filter($chq_pay,      function($r){ return isset($r['payment_amount']) && (float)$r['payment_amount'] > 0; }));
			$net_bank_pay = array_values(array_filter($net_bank_pay, function($r){ return isset($r['amount'])         && (float)$r['amount']         > 0; }));

			// Guard 4: At least one payment must exist
			if ($cash <= 0 && empty($card_pay) && empty($chq_pay) && empty($net_bank_pay)) {
				echo json_encode(array('success' => FALSE, 'message' => 'No valid payment entries found. Please enter at least one payment.'));
				return;
			}

			// Guard 5: Total must match voucher amount (epsilon 0.01)
			$card_total  = array_sum(array_column($card_pay,     'card_amt'));
			$chq_total   = array_sum(array_column($chq_pay,      'payment_amount'));
			$nb_total    = array_sum(array_column($net_bank_pay, 'amount'));
			$new_total   = round($cash + $card_total + $chq_total + $nb_total, 2);
			$voucher_amt = round((float) $header['amount'], 2);

			if (abs($new_total - $voucher_amt) > 0.01) {
				echo json_encode(array(
					'success' => FALSE,
					'message' => 'Payment total (₹' . number_format($new_total, 2) . ') does not match voucher amount (₹' . number_format($voucher_amt, 2) . '). Please recheck.'
				));
				return;
			}

			// --- 5. Build old/new summary for logging ---
			$old_parts = array();
			$payments  = isset($voucher_data['payments']) ? $voucher_data['payments'] : array();
			foreach ($payments as $op) {
				$old_parts[] = $op['payment_mode'] . ':' . $op['payment_amount'];
			}
			$new_parts = array();
			if ($cash > 0) $new_parts[] = 'Cash:' . $cash;
			foreach ($card_pay     as $c) { if (!empty($c['card_amt']))       $new_parts[] = ($c['card_type'] == 1 ? 'CC' : 'DC') . ':' . $c['card_amt']; }
			foreach ($chq_pay      as $c) { if (!empty($c['payment_amount'])) $new_parts[] = 'CHQ:' . $c['payment_amount']; }
			foreach ($net_bank_pay as $n) { if (!empty($n['amount']))         $new_parts[] = 'NB:'  . $n['amount']; }

			// --- 6. Execute update in transaction ---
			$payment_data = array(
				'cash'         => $cash,
				'cards'        => $card_pay,
				'cheques'      => $chq_pay,
				'net_bankings' => $net_bank_pay,
			);

			$this->db->trans_begin();
			$this->$model->update_issue_rcpt_payment($id_issue_rcpt, $payment_data, $updated_by);

			if ($this->db->trans_status() === FALSE) {
				$this->db->trans_rollback();
				log_message('error', '[IR_PAY_EDIT] Transaction failed for id_issue_rcpt=' . $id_issue_rcpt
					. ' | old=' . implode(',', $old_parts) . ' | new=' . implode(',', $new_parts));
				echo json_encode(array('success' => FALSE, 'message' => 'Failed to update payment. Please try again.'));
				return;
			}

			$this->db->trans_commit();

			// --- 7. Activity log (optional — skip if table missing) ---
			if ($this->db->table_exists('ret_activity_log')) {
				$this->$model->insertData(array(
					'module'       => 'Issue/Receipt Payment Edit',
					'action'       => 'UPDATE',
					'reference_id' => $id_issue_rcpt,
					'old_value'    => implode(', ', $old_parts),
					'new_value'    => implode(', ', $new_parts),
					'created_by'   => $updated_by,
					'created_on'   => date('Y-m-d H:i:s'),
				), 'ret_activity_log');
			}

			log_message('info', '[IR_PAY_EDIT] Success for id_issue_rcpt=' . $id_issue_rcpt
				. ' | old=' . implode(',', $old_parts) . ' | new=' . implode(',', $new_parts)
				. ' | by user=' . $updated_by);

			echo json_encode(array('success' => TRUE, 'message' => 'Payment details updated successfully.'));

		} catch (Exception $e) {
			// Rollback if transaction is active
			if ($this->db->trans_status() !== NULL) {
				$this->db->trans_rollback();
			}
			log_message('error', '[IR_PAY_EDIT] Exception: ' . $e->getMessage()
				. ' | id_issue_rcpt=' . (isset($id_issue_rcpt) ? $id_issue_rcpt : 'N/A')
				. ' | trace=' . $e->getTraceAsString());
			echo json_encode(array('success' => FALSE, 'message' => 'An unexpected error occurred. Please try again.'));
		}
	}

	public function receipt($type = "", $id = "", $billno = "")

	{

		$model = "ret_billing_model";

		$SETT_MOD = "admin_settings_model";

		switch ($type) {

			case 'add':

				$data['settings']		= $this->$model->get_retSettings();
				// Fetch day closing date for pre-populating receipt_date
				$dCData = $this->admin_settings_model->getBranchDayClosingData($this->session->userdata('id_branch'));
				$data['day_close_date'] = (!empty($dCData['entry_date']) ? date('d-m-Y', strtotime($dCData['entry_date'])) : date('d-m-Y'));

				$data['main_content'] = "billing/issueReceipt/receiptForm";

				$this->load->view('layout/template', $data);

				//$this->load->view('layout/common_customer_modal');

				$this->load->view('layout/common_customerslider');

				break;

			case 'list':

				$data['main_content'] = "billing/issueReceipt/receiptList";

				$data['access'] = $this->$SETT_MOD->get_access('admin_ret_billing/receipt/list');


				$this->load->view('layout/template', $data);

				break;

			// -------------------------------------------------------
			// Payment Mode Edit — Receipt
			// -------------------------------------------------------
			case 'paymentmode_edit':

				// Reuse receipt list edit permission — no separate menu entry needed
				$access = $this->$SETT_MOD->get_access('admin_ret_billing/receipt/list');

				if (empty($access['edit'])) {
					$this->session->set_flashdata('chit_alert', array(
						'message' => 'You do not have permission to edit payment details.',
						'class'   => 'danger',
						'title'   => 'Access Denied'
					));
					redirect('admin_ret_billing/receipt/list');
					break;
				}

				$data['billing']      = $this->$model->getFinancialYear();
				$data['main_content'] = 'billing/issueReceipt/payment_edit';
				$data['access']       = $access;

				$this->load->view('layout/template', $data);

			break;

			case 'credit_bill':

				$searchTxt = $this->input->post('searchTxt');

				$id_branch = $this->input->post('id_branch');

				$data = $this->$model->getCreditBill($searchTxt, $id_branch);

				echo json_encode($data);

				break;

			case 'save':

				$addData    = $_POST['receipt'];

				$payment    = $_POST['payment'];

				$card_pay_details	= json_decode($payment['card_pay'], true);

				$cheque_details	    = json_decode($payment['chq_pay'], true);

				$net_banking_details = json_decode($payment['net_bank_pay'], true);

				$purchase           = $_POST['purchase'];

				$form_secret = $_POST['form_secret'];

				$allow_submit = false;

				if ($this->session->userdata('FORM_SECRET')) {

					if (strcasecmp($form_secret, ($this->session->userdata('FORM_SECRET'))) === 0) {

						$allow_submit = TRUE;
					}
				}

				if ($allow_submit) {

					/*echo "<pre>"; print_r($addData);	echo "</pre>";

        				echo "<pre>"; print_r($payment);	echo "</pre>";

        				exit;*/

					$amount = 0;

					$weight = 0;

					$metal_rate = $this->$model->get_branchwise_rate($addData['id_branch']);

					$bill_no = $this->$model->bill_no_generate($addData['id_branch'], $addData['is_eda']);

					$dCData = $this->admin_settings_model->getBranchDayClosingData($addData['id_branch']);

					$bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

					$receipt_date = ($addData['receipt_date'] != '' ? date("Y-m-d", strtotime($addData['receipt_date'])) . " " . date("H:i:s") : $bill_date);

					$fin_year       = $this->$model->get_FinancialYear();

					if ($addData['amount'] > 0) {

						if ($addData['store_receipt_as'] == 1) {

							$amount = $addData['amount'];

							//$amount = $payment['tot_amt_received'];

						} else if ($addData['store_receipt_as'] == 2) {

							if ($addData['rate_calc'] == 1) {

								$weight = $addData['amount'] / $metal_rate['goldrate_22ct'];
							} else {

								$weight = $addData['amount'] / $metal_rate['silverrate_1gm'];
							}
						}
					}

					if ($addData['weight'] > 0) {

						if ($addData['store_receipt_as'] == 2) {

							$weight = $addData['weight'];
						} else if ($addData['store_receipt_as'] == 1) {

							/*if($addData['rate_calc']==1)

        						{

        							$amount=$addData['weight']*$metal_rate['goldrate_22ct'];

        						}else{

        							$amount=$addData['weight']*$metal_rate['silverrate_1gm'];

        						}*/

							$amount = $addData['amount'];
						}
					}

					$insData = array(

						'type'			=> 2,

						'amount'		=> $amount,

						'weight'		=> $weight,

						'bill_no'		=> $bill_no,

						'receipt_type'	=> $addData['receipt_type'],

						'receipt_to'	=> ($addData['receipt_to'] != '' && $addData['receipt_type'] == 8 ? $addData['receipt_to'] : NULL),

						'id_branch'		=> $addData['id_branch'],

						'emp_id'	    => ($addData['emp_id'] != '' ? $addData['emp_id'] : NULL),

						'id_customer'	=> ($addData['id_customer'] != '' ? $addData['id_customer'] : NULL),

						'id_employee'	=> ($addData['id_employee'] != '' ? $addData['id_employee'] : NULL),

						'id_karigar'	=> ($addData['id_karigar'] != '' ? $addData['id_karigar'] : NULL),

						'rate_per_gram'	=> ($addData['rate_calc'] == 1 ? $metal_rate['goldrate_22ct'] : $metal_rate['silverrate_1gm']),

						'rate_calc'		=> $addData['rate_calc'],

						'receipt_as'	=> $addData['receipt_as'],

						'is_eda'        => $addData['is_eda'],

						'store_receipt_as' => $addData['store_receipt_as'],

						'pan_no'		=> (!empty($addData['pan_no']) ? $addData['pan_no'] : NULL),

						'narration'	 	=> ($addData['narration'] != '' ? $addData['narration'] : NULL),

						'created_by' 	=> $this->session->userdata('uid'),

						'counter_id'    => ($this->session->userdata('counter_id') != '' ? $this->session->userdata('counter_id') : NULL),

						'created_on' 	=> date("Y-m-d H:i:s"),

						'bill_date' 	=> $receipt_date,

						'fin_year_code' => $fin_year['fin_year_code'],

						'receipt_for'   => (!empty($addData['receipt_ref_id']) ? $addData['receipt_ref_id'] : NULL),

						'form_secret'   => $form_secret,

						'goldrate_22ct' => $addData['goldrate_22ct'],

						'goldrate_18ct' => $addData['goldrate_18ct'],

						'silverrate_1gm' => $addData['silverrate_1gm']
						

					);

					// echo "<pre>"; print_r($insData);exit;

					$this->db->trans_begin();

					$insId = $this->$model->insertData($insData, 'ret_issue_receipt');

					//print_r($this->db->last_query());exit;

					if ($insId) {

						if ($addData['pan_no'] != '' || $addData['aadhar_no'] != '' || $addData['driving_license_no'] != '' || $addData['pp_no'] != '') {

							$this->$model->updateData(array('pan'           		=>($addData['pan_no'] != '' ? strtoupper($addData['pan_no']) : NULL),
															'aadharid'      		=> ($addData['aadhar_no'] != '' ? strtoupper($addData['aadhar_no']) : NULL),
															'driving_license_no'    => ($addData['driving_license_no'] != '' ? strtoupper($addData['driving_license_no']) : NULL),
															'passport_no'      		=> ($addData['pp_no'] != '' ? strtoupper($addData['pp_no']) : NULL), 
														),
													'id_customer', $addData['id_customer'], 'customer');
						}

						//Update Credit status

						if ($addData['receipt_type'] == 1 || $addData['receipt_type'] == 8) {

							$multiple_receipt = json_decode($addData['multiple_receipt_id'], true);

							if (sizeof($multiple_receipt) > 0) {

								foreach ($multiple_receipt as $val) {

									$creditDetails = array(

										'id_issue_receipt'	=> $insId,

										'receipt_for'		=> $val['id_issue_receipt'],

										'received_amount'	=> $val['payable_amount'],

										'discount_amt'	    => $val['discount_amt'],

									);

									$creditStatus = $this->$model->insertData($creditDetails, 'ret_issue_credit_collection_details');

									if ($creditStatus) {

										$balance_amout = ($val['issue_amt'] - $val['paid_amt'] + $val['payable_amount'] + $val['discount_amt']);

										if ($balance_amout == 0) {

											$this->$model->updateData(array('is_collect' => 1), 'id_issue_receipt', $val['id_issue_receipt'], 'ret_issue_receipt');
										}

										if ($addData['id_customer']  && ($val['payable_amount'] > 0)) {

											$wallet = $this->$model->get_retWallet_details($addData['id_customer']);

											if ($wallet['status']) {

												$this->$model->updateWalletData(array('amount' => $val['payable_amount'] + $val['discount_amt'], 'weight' => 0, 'id_customer' => $addData['id_customer']), '+');

												$insWallet = array(

													'id_ret_wallet'		=> $wallet['id_ret_wallet'],

													'id_issue_receipt'	=> $insId,

													'amount'			=> ($val['payable_amount'] + $val['discount_amt']),

													'transaction_type'	=> 0,

													'created_by' 		=> $this->session->userdata('uid'),

													'created_on' 		=> date("Y-m-d H:i:s"),

													'remarks'	 		=> 'CREDIT COLLECTION AMOUNT'

												);

												$this->$model->insertData($insWallet, 'ret_wallet_transcation');

												//echo "<pre>"; print_r($metal_rate);exit;

											}
										} else {

											$return_data = array("status" => FALSE);

											$this->session->set_flashdata('chit_alert', array('message' => 'Received amount cannot be zero', 'class' => 'danger', 'title' => 'Add Receipt'));
												
										}
									}
								}
							}

							//Advance Adjusement

							$advance_adj_details = json_decode($addData['advance_muliple_receipt'], true);

							//print_r($advance_adj_details);exit;

							if (sizeof($advance_adj_details) > 0) {

								foreach ($advance_adj_details as $obj) {

									$advance_amt += $obj->adj_amount;

									$id_ret_wallet      = $obj->id_ret_wallet;

									$data_adv_amount    = array(

										'id_issue_receipt' => $insId,

										'receipt_for'      => $obj['id_issue_receipt'],

										'adjusted_amt'     => $obj['adj_amount']

									);

									$insId_adv_amount = $this->$model->insertData($data_adv_amount, 'ret_issue_receipt_advance_adj');

									//print_r($this->db->last_query());exit;

								}

								if ($insId_adv_amount) {

									//$this->$model->updateWalletData(array('amount'=>$advance_amt,'weight'=>0,'id_customer'=>$addData['id_customer']),'-');

								}
							}

							//Advance Adjusement

						}

						//Update Credit status

						//Pan Images

						$p_ImgData = json_decode($addData['pan_img']);

						if (sizeof($p_ImgData) > 0) {

							foreach ($p_ImgData as $precious) {

								$imgFile = $this->base64ToFile($precious->src);

								$_FILES['pan_img'][] = $imgFile;
							}
						}

						if (isset($_FILES['pan_img'])) {

							$pan_imgs = "";

							$folder =  self::IMG_PATH . "billing/IssueAndReceipt/" . $insId;

							$cus_pan_folder =  self::CUS_IMG_PATH . '/' . $addData['id_customer'];

							if (!is_dir($folder)) {

								mkdir($folder, 0777, TRUE);
							}

							if (!is_dir($cus_pan_folder)) {

								mkdir($cus_pan_folder, 0777, TRUE);
							}

							foreach ($_FILES['pan_img'] as $file_key => $file_val) {

								if ($file_val['tmp_name']) {

									// unlink($folder."/".$product['image']);

									$img_name       =   "P_" . mt_rand(120, 1230) . ".jpg";

									$cus_pan_path   =   $cus_pan_folder . "/" . 'pan.jpg';

									$path           =   $folder . "/" . $img_name;

									$result         =   $this->upload_img('image', $path, $file_val['tmp_name']);

									$this->upload_img('image', $cus_pan_path, $file_val['tmp_name']);

									if ($result) {

										$pan_imgs = strlen($pan_imgs) > 0 ? $pan_imgs . "#" . $img_name : $img_name;
									}
								}
							}

							$this->$model->updateData(array('pan_image' => $pan_imgs), 'id_issue_receipt', $insId, 'ret_issue_receipt');

							$this->$model->updateData(array('pan_proof' => $pan_imgs), 'id_customer', $addData['id_customer'], 'customer');
						}

						//pan images

						//Insert and Update ret wallet

						if ($addData['receipt_type'] == 2 && $addData['id_customer'] != '') {

							$wallet = $this->$model->get_retWallet_details($addData['id_customer']);

							if ($wallet['status']) {

								$this->$model->updateWalletData(array('amount' => $amount, 'weight' => $weight, 'id_customer' => $addData['id_customer']), '+');

								$insWallet = array(

									'id_ret_wallet'		=> $wallet['id_ret_wallet'],

									'id_issue_receipt'	=> $insId,

									'amount'			=> $amount,

									'weight'			=> $weight,

									'transaction_type'	=> 0,

									'created_by' 		=> $this->session->userdata('uid'),

									'created_on' 		=> date("Y-m-d H:i:s"),

									'remarks'	 		=> 'Billing Advace Amount'

								);

								$this->$model->insertData($insWallet, 'ret_wallet_transcation');

								//echo "<pre>"; print_r($metal_rate);exit;

							} else {

								$wallet_acc = array(

									'id_customer' => $addData['id_customer'],

									'amount' => $amount,

									'weight' => $weight,

									'created_by' => $this->session->userdata('uid'),

									'created_time' => date("Y-m-d H:i:s")

								);

								$insWalletAcc = $this->$model->insertData($wallet_acc, 'ret_wallet');

								if ($insWalletAcc) {

									$insWallet = array(

										'id_ret_wallet'	=> $insWalletAcc,

										'id_issue_receipt'	=> $insId,

										'amount'			=> $amount,

										'weight'			=> $weight,

										'transaction_type'	=> 0,

										'created_by' 		=> $this->session->userdata('uid'),

										'created_on' 		=> date("Y-m-d H:i:s"),

										'remarks'	 		=> 'Billing Advace Amount'

									);

									$this->$model->insertData($insWallet, 'ret_wallet_transcation');
								}
							}
						}

						//print_r($this->db->last_query());exit;

						//insert and update ret wallet

						$updData = $_POST['payment'];

						// if (sizeof($updData['cash_payment']) > 0) {

						if (($updData['cash_payment']) != 0 && $updData['cash_payment'] != NULL) {

							$pay_data = array(

								'id_issue_rcpt'	=> $insId,

								'payment_amount' => $updData['cash_payment'],

								'payment_mode'	=> 'Cash',

								'payment_status' => 1,

								'type'			=> 1,

								'payment_type'	=> 'Manual',

								'payment_date'	=> date("Y-m-d H:i:s"),

								'created_time'	=> date("Y-m-d H:i:s"),

								'created_by'	=> $this->session->userdata('uid')

							);

							$this->$model->insertData($pay_data, 'ret_issue_rcpt_payment');
						}

						if (sizeof($card_pay_details) > 0) {

							foreach ($card_pay_details as $card_pay) {

								$arrayCardPay[] = array(

									'id_issue_rcpt'		=> $insId,

									'card_type'         => $card_pay['card_name'],

									'payment_amount'    => $card_pay['card_amt'],

									'id_pay_device'     => ($card_pay['id_device'] != '' ? $card_pay['id_device'] : NULL),

									'payment_status'    => 1,

									'payment_date'		=> date("Y-m-d H:i:s"),

									'payment_mode'	    => ($card_pay['card_type'] == 1 ? 'CC' : 'DC'),

									'card_no'		    => ($card_pay['card_no'] != '' ? $card_pay['card_no'] : NULL),

									'payment_ref_number' => ($card_pay['ref_no'] != '' ? $card_pay['ref_no'] : NULL),


									'card_date' => (isset($card_pay['card_date']) && $card_pay['card_date']!='' ? implode('-', array_reverse(explode('-', $card_pay['card_date']))) : NULL),
									'created_time'	    => date("Y-m-d H:i:s"),

									'created_by'	    => $this->session->userdata('uid')

								);
							}

							if (!empty($arrayCardPay)) {

								$cardPayInsert = $this->$model->insertBatchData($arrayCardPay, 'ret_issue_rcpt_payment');
							}
						}

						if (sizeof($cheque_details) > 0) {

							foreach ($cheque_details as $chq_pay) {

								$cheque_deposit_date = ($chq_pay['cheque_date'] != '' ? date_create($chq_pay['cheque_date']) : NULL);

								$cheque_date = ($chq_pay['cheque_date'] != '' ? date_format($cheque_deposit_date, "Y-m-d") : NULL);

								$arraychqPay[] = array(

									'id_issue_rcpt'		=> $insId,

									'payment_amount' => $chq_pay['payment_amount'],

									'payment_status' => 1,

									'payment_date'		=> date("Y-m-d H:i:s"),

									'cheque_date'		=> ($chq_pay['cheque_date'] != '' ? $cheque_date : NULL),

									'payment_mode'	=> 'CHQ',

									'cheque_no'		=> ($chq_pay['cheque_no'] != '' ? $chq_pay['cheque_no'] : NULL),

									'bank_name'		=> ($chq_pay['bank_name'] != '' ? $chq_pay['bank_name'] : NULL),

									'bank_branch'	=> ($chq_pay['bank_branch'] != '' ? $chq_pay['bank_branch'] : NULL),

									'created_time'	=> date("Y-m-d H:i:s"),

									'created_by'	=> $this->session->userdata('uid')

								);
							}

							if (!empty($arraychqPay)) {

								$chqPayInsert = $this->$model->insertBatchData($arraychqPay, 'ret_issue_rcpt_payment');
							}
						}

						if (sizeof($net_banking_details) > 0) {

							foreach ($net_banking_details as $nb_pay) {

								$arrayNBPay[] = array(

									'id_issue_rcpt'		=> $insId,

									'payment_amount'    => $nb_pay['amount'],

									'payment_status'    => 1,

									'payment_date'		=> date("Y-m-d H:i:s"),

									'payment_mode'	    => 'NB',

									'id_pay_device'     => ($nb_pay['id_device'] != '' ? $nb_pay['id_device'] : NULL),

									'payment_ref_number' => ($nb_pay['ref_no'] != '' ? $nb_pay['ref_no'] : NULL),

									'NB_type'           => ($nb_pay['nb_type'] != '' ? $nb_pay['nb_type'] : NULL),

									'net_banking_date'  => ($nb_pay['nb_date']!='' ? implode('-', array_reverse(explode('-', $nb_pay['nb_date']))):date("Y-m-d")),

									'id_bank'           => ($nb_pay['id_bank'] != '' && $nb_pay['id_bank'] != null ? $nb_pay['id_bank'] : NULL),

									'created_time'	    => date("Y-m-d H:i:s"),

									'created_by'	    => $this->session->userdata('uid')

								);
							}

							if (!empty($arrayNBPay)) {

								$NbPayInsert = $this->$model->insertBatchData($arrayNBPay, 'ret_issue_rcpt_payment');

								//print_r($this->db->last_query());exit;

							}
						}

						$billPurchase = (isset($_POST['purchase']) ? $_POST['purchase'] : '');

						//echo "<pre>"; print_r($billPurchase);exit;

						if (!empty($billPurchase)) {

							$arrayPurchaseBill = array();

							foreach ($billPurchase['esti_detail_id'] as $key => $val) {

								$arrayPurchaseBill = array(

									'id_issue_receipt' => $insId,

									'purpose' => $billPurchase['purpose'][$key],

									'metal_type' => $billPurchase['id_metal'][$key],

									//'item_type' => $billPurchase['item_type'][$key],

									'item_type' => 1,

									'esti_detail_id' => $billPurchase['esti_detail_id'][$key],

									'gross_wt' => $billPurchase['gross_wt'][$key],

									'stone_wt' => $billPurchase['stone_wt'][$key],

									'dust_wt' => $billPurchase['dust_wt'][$key],

									'net_wt' => $billPurchase['net_wt'][$key],

									'wastage_percent' => ($billPurchase['wastage_percent'][$key] !== '' && $billPurchase['wastage_percent'][$key] !== null) ? $billPurchase['wastage_percent'][$key] : 0,

									'wast_wt' => $billPurchase['wastage_wt'][$key],

									'rate' => $billPurchase['amount'][$key],

									'rate_per_grm' => $billPurchase['rate_per_gram'][$key]
								);

								if (!empty($arrayPurchaseBill)) {

									$oldMetal = $this->$model->insertData($arrayPurchaseBill, 'ret_receipt_wgt_detail');

									if ($oldMetal) {

										$oldUpdata = array('purchase_status' => 1, 'bill_id' => NULL);

										$this->$model->updateData($oldUpdata, 'old_metal_sale_id', $billPurchase['esti_detail_id'][$key], 'ret_estimation_old_metal_sale_details');
									}
								}
							}
						}

						$billSales = (isset($_POST['estsales']) ? $_POST['estsales'] : '');

						//echo "<pre>"; print_r($billSales);exit;

						if (!empty($billSales)) {

							$arraySalesBill = array();

							foreach ($billSales['esti_detail_id'] as $key => $val) {

								$arraySalesBill = array(

									'adv_rcpt_issue_receipt_id' => $insId,

									'adv_rcpt_tagid'            => $billSales['tag_id'][$key],

									'adv_rcpt_esti_detail_id'   => $billSales['esti_detail_id'][$key]

								);

								if (!empty($arraySalesBill)) {

									$estitagsadv = $this->$model->insertData($arraySalesBill, 'ret_adv_receipt_tags');

									if ($billSales['tag_id'][$key] != '') {

										$this->$model->updateData(array('tag_status' => 11, 'updated_time' => date("Y-m-d H:i:s"), 'updated_by' => $this->session->userdata('uid')), 'tag_id', $billSales['tag_id'][$key], 'ret_taging');

										$log_data = array(

											'tag_id'	          => $billSales['tag_id'][$key],

											'date'		          => $bill_date,

											'status'	          => 1,

											'issuspensestock'	  => 1,

											'from_branch'         => $addData['id_branch'],

											'to_branch'           => NULL,

											'created_on'          => date("Y-m-d H:i:s"),

											'created_by'          => $this->session->userdata('uid'),

										);

										$this->$model->insertData($log_data, 'ret_taging_status_log'); //Update Tag lot status

										$tag_status = $this->$model->get_tag_status($billSales['tag_id'][$key]);

										if ($tag_status['id_section'] != null && $tag_status['id_section'] != '') {

											$secttag_log = array(

												'tag_id'	          => $billSales['tag_id'][$key],

												'date'		          => $bill_date,

												'status'	          => 1,

												'issuspensestock'	  => 1,

												'from_branch'         => $addData['id_branch'],

												'to_branch'           => NULL,

												'from_section'        => NULL,

												'to_section'          => $tag_status['id_section'],

												'issuspensestock'     => $addData['bill_type'] == 15 ? 1 : 0,

												'created_on'          => date("Y-m-d H:i:s"),

												'created_by'          => $this->session->userdata('uid'),

											);

											$this->$model->insertData($secttag_log, 'ret_section_tag_status_log');
										}
									}
								}
							}
						}

						if ($addData['receipt_as'] == 2) {

							if (!empty($purchase)) {

								$arrayEstBill = array();

								foreach ($purchase['old_metal_sale_id'] as $key => $val) {

									$arrayEstBill = array(

										'id_issue_receipt'     => $insId,

										'est_old_metal_sale_id' => $purchase['old_metal_sale_id'][$key],

									);

									if (!empty($arrayEstBill)) {

										$esti_wt_adv = $this->$model->insertData($arrayEstBill, 'ret_adv_receipt_weight');

										if ($esti_wt_adv) {

											$this->$model->updateData(array('purchase_status' => 3), 'old_metal_sale_id', $purchase['old_metal_sale_id'][$key], 'ret_estimation_old_metal_sale_details');

											$old_metal_log = array(

												'id_issue_receipt' => $insId,

												'from_branch'      => NULL,

												'to_branch'        => $addData['id_branch'],

												'status'           => 1,

												'item_type'        => 7, // Old Metal

												'date'             => $bill_date,

												'created_on'       => date("Y-m-d H:i:s"),

												'created_by'      => $this->session->userdata('uid'),

											);

											$this->$model->insertData($old_metal_log, 'ret_purchase_items_log');
										}
									}
								}
							}
						}
					}

					if ($this->db->trans_status() === TRUE) {

						$this->db->trans_commit();

						$return_data = array('status' => TRUE, 'id' => $insId);

						$this->session->set_flashdata('chit_alert', array('message' => 'Receipt  successfully', 'class' => 'success', 'title' => 'Add Receipt'));
					} else {

						$this->db->trans_rollback();

						$return_data = array('status' => FALSE, 'id' => '');

						echo $this->db->_error_message() . "<br/>";

						echo $this->db->last_query();
						exit;

						$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Receipt'));
					}
				} else {

					$return_data = array("status" => FALSE);

					$this->session->set_flashdata('chit_alert', array('message' => 'Form Already Submitted', 'class' => 'danger', 'title' => 'Add Receipt'));
				}

				echo json_encode($return_data);

				//redirect('admin_ret_billing/receipt/list');

				break;

			case 'receipt_print':

				$model = "ret_billing_model";

				$data['issue'] = $this->$model->get_receipt_details($id);

				$data['comp_details'] = $this->$model->getCompanyDetails($data['issue']['id_branch']);

				$data['payment'] = $this->$model->get_receipt_payment($id);

				$data['metal_rate'] = $this->$model->get_branchwise_rate($data['issue']['id_branch']);

				$data['advance_adj_details'] = $this->$model->get_receipt_advance_adj_details($id);

				$data['billing_adv_adj'] = $this->$model->get_billing_adj_details($id);

				$data['deposit_type_bill_no'] = $this->$model->get_deposit_type_bill_no($id);

				// ── Template-based rendering ──
				$ir_template_enabled = false;
				$ir_setting_query = $this->db->query("SELECT value FROM ret_settings WHERE name = 'issue_receipt_template_based' LIMIT 1");
				if ($ir_setting_query && $ir_setting_query->num_rows() > 0) {
					$ir_template_enabled = ($ir_setting_query->row()->value == 1);
				}

				if ($ir_template_enabled) {
					$this->load->helper('receipt');
					$this->load->model('print_template_model');
					$mapped_data = map_issue_receipt_data_to_template($data);

					$combined_data = $data;
					if (isset($data['issue']) && is_array($data['issue'])) {
						$combined_data = array_merge($combined_data, $data['issue']);
					}
					if (isset($data['comp_details']) && is_array($data['comp_details'])) {
						$combined_data = array_merge($combined_data, $data['comp_details']);
					}
					$combined_data = array_merge($combined_data, $mapped_data);

					$branch_id = isset($data['issue']['id_branch']) ? $data['issue']['id_branch'] : null;
					$template_html = $this->print_template_model->render(17, $combined_data, $branch_id);

					if ($template_html !== false && !empty(trim(strip_tags($template_html)))) {
						echo $template_html;
						exit;
					}
				}

				// Fallback to legacy view
				$html = $this->load->view('billing/issueReceipt/print/issue', $data, true);

				echo $html;
				exit;

				break;

			case 'cancel':

				$model = "ret_billing_model";

				// ── IR Cancel Validation ──
				$ir_id = $this->input->post('id_issue_receipt');
				$validation = $this->$model->validate_ir_cancel($ir_id, 'Receipt');
				if ($validation['status'] === FALSE) {
					echo json_encode($validation);
					break;
				}

				$data = array(

					'bill_status' => 2,

					'updated_by'	=> $this->session->userdata('uid'),

					'updated_on'	=> date('Y-m-d H:i:s')
				);

				$this->db->trans_begin();

				$status = $this->$model->updateData($data, 'id_issue_receipt', $ir_id, 'ret_issue_receipt');

				$receipt_det = $this->$model->get_receipt_details($ir_id);

				if ($receipt_det['rct_type'] == 2) // Update in Wallet

				{

					$this->$model->updateWalletData(array('amount' => $receipt_det['amount'], 'weight' => 0, 'id_customer' => $receipt_det['id_customer']), '-');
				}

				if ($receipt_det['rct_type'] == 2 && $receipt_det['rct_as'] == 2) // For Weight Advance

				{

					$advDetails = $this->$model->get_est_adv_details($ir_id);

					foreach ($advDetails as $adv) {

						$this->$model->updateData(array('purchase_status' => 0), 'old_metal_sale_id', $adv['est_old_metal_sale_id'], 'ret_estimation_old_metal_sale_details');
					}
				}

				$advance_tag_details = $this->$model->get_est_adv_tag_details($ir_id);

				if (sizeof($advance_tag_details) > 0) {

					foreach ($advance_tag_details as $adv) {

						$this->$model->updateData(array('purchase_status' => 0, 'bil_detail_id' => NULL), 'est_item_id', $adv['adv_rcpt_esti_detail_id'], 'ret_estimation_items');

						$this->$model->updateData(array('tag_status' => 0, 'updated_time' => date("Y-m-d H:i:s"), 'updated_by' => $this->session->userdata('uid')), 'tag_id', $adv['adv_rcpt_tagid'], 'ret_taging');
					}
				}

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					$return_data = array('status' => TRUE, 'message' => 'Receipt Cancelled Successfully');
				} else {

					$this->db->trans_rollback();

					$return_data = array('status' => FALSE, 'message' => 'Unable to proceed the requested process');

					echo $this->db->_error_message() . "<br/>";

					echo $this->db->last_query();
					exit;

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Receipt'));
				}

				echo json_encode($return_data);

				break;

			default:

				$list = $this->$model->ajax_getReceiptlist($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_billing/receipt/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				// echo "<pre>"; print_r($data); exit;

				echo json_encode($data);
		}
	}

	public function get_account_head()

	{

		$model = "ret_billing_model";

		$data = $this->$model->get_account_head();

		echo json_encode($data);
	}
	/*
 	public function get_borrower()

 	{

 		$model= "ret_billing_model";

 		$id_branch=$this->input->post('id_branch');

 		$issue_to=$this->input->post('issue_to');

 		$SearchTxt=$this->input->post('searchTxt');

 		$is_eda=$this->input->post('is_eda');

		$data =$this->$model->get_borrower_details($SearchTxt,$id_branch,$issue_to,$is_eda);

		echo json_encode($data);

 	}
*/

	public function get_borrower()

	{

		$model = "ret_billing_model";

		$id_branch = $this->input->post('id_branch');

		$issue_to = $this->input->post('issue_to');

		$SearchTxt = $this->input->post('searchTxt');

		$is_eda = $this->input->post('is_eda');

		$receipt_to = $this->input->post('receipt_to');

		$receipt_type = $this->input->post('receipt_type');

		$data = $this->$model->get_borrower_details($SearchTxt, $id_branch, $issue_to, $receipt_to, $receipt_type, $is_eda);

		echo json_encode($data);
	}
	public function get_customer_advance_details()

	{

		$model = "ret_billing_model";

		$id_customer = $this->input->post('id_customer');

		$is_eda = $this->input->post('is_eda');

		$data = $this->$model->get_receipt_refund($id_customer, $is_eda);

		echo json_encode($data);
	}

	//issue and receipt

	function cancel_bill()

	{

		$type = "";

		$model = "ret_billing_model";

		$remarks = $this->input->post('remarks');

		$bill_id = $this->input->post('bill_id');

		$upd_data = array(

			"bill_status"	=> 2,

			'updated_time'	=> date("Y-m-d H:i:s"),

			'cancelled_date' => date("Y-m-d H:i:s"),

			'cancel_reason' => $remarks,

			'updated_by'	=> $this->session->userdata('uid')

		);

		$this->db->trans_begin();

		$status = $this->$model->updateData($upd_data, 'bill_id', $bill_id, 'ret_billing');
		
		if ($status) {

			//Receipt Revert

			$irUpdata = array('bill_status' => 2);

			$this->$model->updateData($irUpdata, 'deposit_bill_id', $bill_id, 'ret_issue_receipt');

			//Receipt Revert

			$bill_detail = $this->$model->get_bill_detail($bill_id);

			foreach ($bill_detail as $bill) {

				//  $dCData = $this->admin_settings_model->getBranchDayClosingData($bill['id_branch']);

				//  $bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);



				$dCData = $this->admin_settings_model->getPreviousDateStatuslog($bill['bill_id']);

				$bill_date = ($dCData['bill_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['bill_date']);

				// Estimation
				$this->$model->updateData(array('estbillid' => NULL), 'estbillid', $bill['bill_id'], 'ret_estimation');



				$updData = array('purchase_status' => 0, 'bil_detail_id' => NULL);

				$this->$model->updateData($updData, 'bil_detail_id', $bill['bill_det_id'], 'ret_estimation_items');

				//print_r($this->db->last_query());exit;

				if ($bill['tag_id'] != '' && $bill['item_type'] == 0) {

					$this->$model->updateData(array('tag_status' => 0, 'updated_time' => date("Y-m-d H:i:s"), 'updated_by' => $this->session->userdata('uid')), 'tag_id', $bill['tag_id'], 'ret_taging');

					$log_data = array(

						'tag_id'	  => $bill['tag_id'],

						'date'		  => $bill_date,

						'status'	  => 6,

						'from_branch' => $bill['id_branch'],

						'to_branch'   => $bill['current_branch'],

						'created_on'  => date("Y-m-d H:i:s"),

						'created_by'  => $this->session->userdata('uid'),

					);

					$this->$model->insertData($log_data, 'ret_taging_status_log'); //Update Tag lot status

					$tag_status = $this->$model->get_tag_status($bill['tag_id']);

					if ($tag_status['id_section'] != null && $tag_status['id_section'] != '') {

						$secttag_log = array(

							'tag_id'	          => $bill['tag_id'],

							'date'		          => $bill_date,

							'status'	          => 6,

							'from_branch'         => $bill['id_branch'],

							'to_branch'           => $bill['current_branch'],

							'from_section'        => $tag_status['id_section'],

							'to_section'          => $tag_status['id_section'],

							'created_on'          => date("Y-m-d H:i:s"),

							'created_by'          => $this->session->userdata('uid'),

						);

						$this->$model->insertData($secttag_log, 'ret_section_tag_status_log');
					}
				}

				//stock maintaince

				$existData = array('id_product' => $bill['product_id'], 'id_design' => $bill['design_id'], 'id_sub_design' => $bill['id_sub_design'], 'id_section' => $bill['id_section'], 'id_branch' => $bill['id_branch']);

				$isExist = $this->$model->checkNonTagItemExist($existData);

				if ($isExist['status'] == TRUE) {

					$nt_data = array(

						'id_nontag_item' => $isExist['id_nontag_item'],

						'no_of_piece'   => ($bill['no_of_piece'] != ''  && $bill['no_of_piece'] != null ? $bill['no_of_piece'] : 0),

						'gross_wt'		=> $bill['gross_wt'],

						'net_wt'		=> $bill['net_wt'],

						'updated_by'	=> $this->session->userdata('uid'),

						'updated_on'	=> date('Y-m-d H:i:s'),

					);

					$this->$model->updateNTData($nt_data, '+');

					$non_tag_data = array(

						'from_branch'	=> NULL,

						'to_branch'	    => $bill['id_branch'],

						'no_of_piece'   => $bill['no_of_piece'],

						'net_wt' 		=> $bill['net_wt'],

						'gross_wt' 		=> $bill['gross_wt'],

						'product'		=> $bill['product_id'],

						'design'		=> $bill['design_id'],

						'date'  	    => $bill_date,

						'created_on'  	=> date("Y-m-d H:i:s"),

						'created_by'   	=> $this->session->userdata('uid'),

						'status'   		=> 6,

						'bill_id'       => $bill_id

					);

					$this->$model->insertData($non_tag_data, 'ret_nontag_item_log');
				}

				//stock maintaince

			}

			$oldUpdata = array('purchase_status' => 0, 'bill_id' => NULL);

			$this->$model->updateData($oldUpdata, 'bill_id', $bill_id, 'ret_estimation_old_metal_sale_details');

			$ret_bill_details = $this->$model->ret_bill_return_details($bill_id);

			foreach ($ret_bill_details as $items) {

				$retUpdata = array('status' => 1);

				$this->$model->updateData($retUpdata, 'bill_det_id', $items['ret_bill_det_id'], 'ret_bill_details');
			}

			//gift voucher

			$this->$model->get_gift_issue_details($bill_id); //Issued Voucher Cancel

			$this->$model->get_redeem_details($bill_id); //Redeemed Voucher Cancel

			//gift voucher

			//Chit Utilized Revert

			$chit_details = $this->$model->getChitUtilized($bill_id);

			foreach ($chit_details as $chit) {

				$chitUpdData = array('is_utilized' => 0, 'utilized_type' => NULL);

				$this->$model->updateData($chitUpdData, 'id_scheme_account', $chit['scheme_account_id'], 'scheme_account');
			}

			//Wallet Debit Transcation

			$tag_Details = $this->$model->getWalletTransDetails($bill_id);

			//print_r($this->db->last_query());exit;

			foreach ($tag_Details as $items) {

				$WalletinsData = array(

					'id_wallet_account' => $items['id_wallet_account'],

					'transaction_type' => 1,

					'type'             => 1,

					'bill_id'          => $items['bill_id'],

					'ref_no'           => $items['ref_no'],

					'value'            => $items['value'],

					'id_employee'      => $this->session->userdata('uid'),

					'description'      => 'Green Tag Sales Incentive Debit',

					'date_transaction' => date("Y-m-d H:i:s"),

					'date_add'	       => date("Y-m-d H:i:s"),

				);

				$this->$model->insertData($WalletinsData, 'wallet_transaction');
			}

			//Wallet Debit Transcation

			//CHIT DEPOSIT REVERT

			$bill_details = $this->$model->getBillingDetails($bill_id, $type);

			if ($bill_details['make_as_advance'] == 2) {

				$PayDetails = $this->$model->getChitPayDetails($bill_id);

				if ($PayDetails['id_payment'] != '') {

					$this->$model->updateData(array('payment_status' => 4), 'id_payment', $PayDetails['id_payment'], 'payment');
				}
			}

			//CHIT DEPOSIT REVERT

			//REPAIR ORDER REVERT

			$billing = $this->$model->getBillingDetails($bill_id, $type);

			if ($billing['bill_type'] == 11) // repair bill type

			{

				$this->$model->updateData(array('orderstatus' => 4), 'bill_id', $bill_id, 'customerorderdetails');

				//print_r($this->db->last_query());exit;

			}

			//REPAIR ORDER REVERT

			//CUSTOMER ORDER REVERT

			// $cus_order_details = $this->$model->get_order_id_details($bill_id);

			// if (sizeof($cus_order_details) > 0) {

			// 	foreach ($cus_order_details as $ord) {

			// 		$this->$model->updateData(array('orderstatus' => 4), 'id_orderdetails', $ord['id_orderdetails'], 'customerorderdetails');
			// 	}

			// 	$this->$model->updateData(array('is_adavnce_adjusted' => 0), 'adjusted_bill_id', $bill_id, 'ret_billing_advance');
			// }

			if($billing['bill_type'] == 9){

									$cus_order_details = $this->$model->get_order_id_details($bill_id);
				
			// echo "<pre>";print_r($cus_order_details); exit;
		
			if (sizeof($cus_order_details) > 0) {
					
						foreach ($cus_order_details as $ord) {
						
							$this->$model->updateData(array('orderstatus' => 4), 'id_orderdetails', $ord['id_orderdetails'], 'customerorderdetails');
						}
					
						$order_adv_adj_details = $this->$model->get_ord_adv_adj($bill_id);
					
						// print_r($this->db->last_query());exit;
					
						foreach ($order_adv_adj_details as $order) {
						
							if (!empty($order['bill_adv_id']) && $order['bill_adv_id'] != "") {
								
								$order_advance_detail = $this->$model->get_order_advance_details($order['bill_adv_id']);
								
								if ($order_advance_detail) {
									
									$balance_advance_amt = $order_advance_detail['adjusted_advance'] - $order['utilized_amt'];
								
									$this->$model->updateData(array('is_adavnce_adjusted' => 0, 'adjusted_amount' => $balance_advance_amt, 'adjusted_bill_id' => NULL ), 'bill_adv_id', $order['bill_adv_id'], 'ret_billing_advance');
								}
							}
						}
					}
			}

			//CUSTOMER ORDER REVERT

			// Credit Collection Cancel
			

			if ($billing['bill_type'] == 8) {

				$this->$model->updateData(array('credit_status' => 2), 'bill_id', $billing['ref_bill_id'], 'ret_billing');

				//print_r($this->db->last_query());exit;

			

			} else if ($billing['bill_type'] == 7 && !empty($billing['ref_bill_id'])) {

				// Sales Return Cancel against a Credit Bill Ã¢â‚¬â€ recalculate the reference bill's remaining balance
				$cancel_original_bill_id = $billing['ref_bill_id'];
				$cancel_bill_details = $this->$model->get_BillAmount($cancel_original_bill_id);

				if ($cancel_bill_details['is_credit'] == 1) {

					// Get all collections paid so far
					$cancel_paid_amount 		= $this->$model->get_credit_pay_amount($cancel_original_bill_id);
					$cancel_old_metal_amount 	= $this->$model->get_credit_old_metal_amount($cancel_original_bill_id, 8);
					$cancel_total_collections 	= $cancel_paid_amount + $cancel_old_metal_amount;

					// Get remaining returned amount (excluding this cancelled return which is now bill_status=0)
					$cancel_total_returned 		= $this->$model->get_total_returned_amount_by_bill($cancel_original_bill_id);

					// Recalculate balance (subtract round_off_amt so a full return always zeroes out)
					$cancel_balance = $cancel_bill_details['tot_bill_amount'] - $cancel_bill_details['tot_amt_received'] - $cancel_total_collections - $cancel_total_returned - floatval($cancel_bill_details['round_off_amt']);

					// Set status accordingly
					$cancel_new_status = ($cancel_balance <= 0) ? 1 : 2;
					$this->$model->updateData(array('credit_status' => $cancel_new_status), 'bill_id', $cancel_original_bill_id, 'ret_billing');
				}
			}

			//UPDATING RATE TYPE FOR ORDER DETAILS

			if($billing['bill_type'] == 5) {

				$adv_order_details = $this->$model->get_adv_order_details($bill_id, $billing['id_branch']);

				// echo"<pre>";print_r($adv_order_details['id_customerorder']);exit;	

				foreach($adv_order_details as $adv_order){

					$this->update_order_rate_type($adv_order['id_customerorder'],$billing['id_branch'],$bill_id);

				}

			}

			//UPDATING RATE TYPE FOR ORDER DETAILS

			// Credit Collection Cancel

		}

		// Other Inventory Stock Revert

		// Fetch bill details for other inventory items
		$bill_detail_other_inv = $this->$model->get_bill_detail_other_inv($bill_id);

		if (!empty($bill_detail_other_inv)) {
			foreach ($bill_detail_other_inv as $other_inv) {
				// Prepare data to update inventory item details
				$updData = array(
					'id_inventory_issue' => NULL,
					'status' => 0
				);

				// Update the inventory item details
				$this->$model->updateData(
					$updData,
					'id_inventory_issue',
					$other_inv['id_inventory_issue'],
					'ret_other_inventory_purchase_items_details'
				);
			}

			// Delete entries from inventory issue table
			$status = $this->$model->deleteData('id_inventory_issue', $other_inv['id_inventory_issue'], 'ret_other_inventory_purchase_items_log');
			$status = $this->$model->deleteData('bill_id', $bill_id, 'ret_other_invnetory_issue');
		}

		$this->db->trans_complete(); // Complete transaction

		// // Check transaction status
		// if ($this->db->trans_status() === FALSE) {
		// 	$this->session->set_flashdata('error', 'Failed to update or delete records.');
		// } else {
		// 	$this->session->set_flashdata('success', 'Records updated and deleted successfully.');
		// }


		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$return_data = array('status' => TRUE);

			$this->session->set_flashdata('chit_alert', array('message' => 'Bill No cancelled successfully', 'class' => 'success', 'title' => 'Cancel Bill'));
		} else {

			$this->db->trans_rollback();

			$return_data = array('status' => false);

			$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Cancel Bill'));
		}

		echo json_encode($return_data);

		//redirect('admin_ret_billing/billing/list');

	}

	function get_branch_details()

	{

		$model = "ret_billing_model";

		$id_branch = $this->input->post('id_branch');

		$data = $this->$model->get_branch_details($id_branch);

		echo json_encode($data);
	}

	function getVoucherDetails()

	{

		$model = "ret_billing_model";

		$id_branch = $this->input->post('id_branch');

		$id_cus = $this->input->post('bill_cus_id');

		$code = $this->input->post('searchTxt');

		$data = $this->$model->getVoucherDetails($id_branch, $id_cus, $code);

		echo json_encode($data);
	}

	function getGiftProducts()

	{

		$model = "ret_billing_model";

		$data = $this->$model->CheckProductAvailability($_POST['id_set_gift_voucher']);

		echo json_encode($data);
	}

	function GiftRedeemProduct()

	{

		$model = "ret_billing_model";

		$data = $this->$model->CheckRedeemProduct($_POST['id_set_gift_voucher']);

		echo json_encode($data);
	}

	function GeneralGiftRedeemProduct()

	{

		$model = "ret_billing_model";

		$data = $this->$model->GeneralGiftRedeemProduct($_POST['id_gift_voucher']);

		echo json_encode($data);
	}

	//Business Customers

	public function getSearchCompanyUsers()
	{

		$model = "ret_billing_model";

		$data = $this->$model->getSearchCompanyUsers($_POST['searchTxt'], $_POST['id_customer']);

		echo json_encode($data);
	}

	public function addNewCompanyUsers()

	{

		$model = "ret_billing_model";

		$data = $this->$model->addNewCompanyUsers($_POST);

		echo json_encode($data);
	}

	public function getCompanyPurchaseAmount()

	{

		$model = "ret_billing_model";

		$data = $this->$model->getCompanyPurchaseAmount($_POST['id_customer']);

		echo json_encode($data);
	}

	//Business Customers

	function get_one_time_pre_weight_scheme()

	{

		$model = "ret_billing_model";

		$data = $this->$model->get_one_time_pre_weight_scheme();

		echo json_encode($data);
	}

	function get_customer_weight_scheme_details()

	{

		$model = "ret_billing_model";

		$data = $this->$model->get_customer_weight_scheme_details($_POST);

		echo json_encode($data);
	}

	function generate_receipt_no($id_scheme, $branch)

	{

		$model =	"payment_model";

		$rcpt_no = "";

		$rcpt = $this->$model->get_receipt_no($id_scheme, $branch);

		if ($rcpt != NULL) {

			if ($this->config->item('receipTcode') != '') {          // based on the config settings to removed comp shortcode front of recp num //HH

				$temp = explode($this->company['short_code'], $rcpt);

				if (isset($temp)) {

					$number = (int) $temp[1];

					$number++;

					$rcpt_no = $this->company['short_code'] . str_pad($number, 7, '0', STR_PAD_LEFT);

					//print_r($rcpt_no);exit;

				}
			} else {

				$number = (int) $rcpt;

				$number++;

				$rcpt_no = str_pad($number, 7, '0', STR_PAD_LEFT);

				//print_r($rcpt_no);exit;

			}
		} else {

			if ($this->config->item('receipTcode') != '') {

				$rcpt_no = $this->company['short_code'] . "000001";
			} else {

				$rcpt_no = "000001";
			}
		}

		//print_r($rcpt_no);exit;

		return $rcpt_no;
	}

	//bank account details

	function get_bank_acc_details()

	{

		$model = "ret_billing_model";

		$data = $this->$model->get_bank_acc_details();

		echo json_encode($data);
	}

	function get_payment_device_details()

	{

		$model = "ret_billing_model";

		$data = $this->$model->get_payment_device_details();

		echo json_encode($data);
	}

	//bank account details

	function get_customer_address()

	{

		$model = "ret_billing_model";

		$data['registered_address'] = $this->$model->get_customer_reg_add($_POST['id_customer']);

		echo json_encode($data);
	}

	function get_mydelivery_address()

	{

		$model = "ret_billing_model";

		$data['delivered_address'] = $this->$model->get_mydelivery_address($_POST['id_customer']);

		echo json_encode($data);
	}

	function update_mydelivery_address()

	{

		$model = "ret_billing_model";

		$addData = $_POST;

		$insData = array(

			'id_customer'   => $addData['id_customer'],

			'id_country'    => $addData['id_country'],

			'id_state'      => $addData['id_state'],

			'id_city'       => $addData['id_city'],

			'address1'      => ($addData['address1'] != '' ? strtoupper($addData['address1']) : NULL),

			'address2'      => ($addData['address2'] != '' ? strtoupper($addData['address2']) : NULL),

			'address3'      => ($addData['address3'] != '' ? strtoupper($addData['address3']) : NULL),

			'pincode'       => ($addData['pincode'] != '' ? $addData['pincode'] : NULL),

			'address_name'  => strtoupper($addData['del_address_name']),

			'created_on'    => date("Y-m-d H:i:s"),

			'created_by'    => $this->session->userdata('uid'),

		);

		$this->db->trans_begin();

		$this->$model->insertData($insData, 'customer_delivery_address');

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$responseData = array('status' => TRUE, 'msg' => 'Address Added Successfully');
		} else {

			$this->db->trans_rollback();

			$responseData = array('status' => FALSE, 'msg' => 'Unable to Proceed Your Request.');
		}

		echo json_encode($responseData);
	}

	public function getCustomersindRecords()

	{

		$model = "ret_billing_model";

		$data = $this->$model->getAvailableIndCustomers($_POST['cus_id']);

		$path = "assets/img/customer/" . $_POST['cus_id'];

		if (is_dir($path)) {

			$path = "assets/img/customer/" . $_POST['cus_id'] . "/customer.jpg";

			$data[0]['img_path'] = base_url($path);
		} else {

			$path = "assets/img/default.png";

			$data[0]['img_path'] = base_url($path);
		}

		echo json_encode($data);
	}

	//BILL EDIT

	function paymentmode_edit($type = "")

	{

		$model = "ret_billing_model";

		switch ($type) {

			case 'list':

				$data['billing']		= $this->$model->get_empty_record();

				$data['main_content'] = "billing/paymentmode_edit";

				$this->load->view('layout/template', $data);

				break;

			case 'active_bill_list':

				$model = "ret_billing_model";

				$bill_no = $this->input->post('bill_no');

				$branch = $this->input->post('branch');

				$fin_year = $this->input->post('fin_year');

				$data = $this->$model->get_active_bill_list($bill_no, $branch, $fin_year);



				echo json_encode($data);

				break;

			case 'update':

				$model = 'ret_billing_model';
				$bill_id = (int) $this->input->post('bill_id');
				$update_data = array();

				if ($bill_id <= 0) {
					echo json_encode(array('status' => FALSE, 'message' => 'Invalid bill ID.'));
					break;
				}

				if (isset($_POST['customer_name'])) {
					$update_data['customer_name'] = $this->input->post('customer_name');
				}

				if (isset($_POST['id_employee'])) {
					$update_data['id_employee'] = (int) $this->input->post('id_employee');
				}

				if (isset($_POST['pan_no'])) {
					$update_data['pan_no'] = $this->input->post('pan_no');
				}

				if (isset($_POST['gst_no'])) {
					$update_data['gst_number'] = $this->input->post('gst_no');
				}

				if (isset($_POST['aadhaar_no'])) {
					$update_data['aadhar_no'] = $this->input->post('aadhaar_no');
				}

				if (isset($_POST['billing_for'])) {
					$billing_for = (int) $this->input->post('billing_for');
					// B2B to B2C: block if E-Invoice (IRN) already generated
					if ($billing_for == 1) {
						$irn_check = $this->db->select('cusdel_irn')->where('bill_id', $bill_id)->get('ret_billing')->row();
						if (!empty($irn_check->cusdel_irn)) {
							echo json_encode(array('status' => FALSE, 'message' => 'Cannot convert to B2C. E-Invoice (IRN) already generated for this bill.'));
							break;
						}
					}
					$update_data['billing_for'] = $billing_for;
				}

				if (!empty($update_data)) {

					$this->$model->updateData($update_data, 'bill_id', $bill_id, 'ret_billing');

					$responseData = array(
						'status'	=> True,
						'message'	=> 'Record Updated Successfully'
					);
				} else {

					$responseData = array(
						'status'	=> False,
						'message'	=> 'Record Not Updated'
					);
				}

				echo json_encode($responseData);

				break;

			case 'get_bill_items':

				$model = 'ret_billing_model';
				$bill_id = (int) $this->input->post('bill_id');

				if ($bill_id <= 0) {
					echo json_encode(array('status' => FALSE, 'message' => 'Invalid bill ID.'));
					break;
				}

				$items = $this->db->query("
					SELECT d.bill_det_id, d.gross_wt, d.item_emp_id,
						   IFNULL(p.product_name, 'N/A') AS product_name,
						   IFNULL(e.firstname, '') AS emp_name
					FROM ret_bill_details d
					LEFT JOIN ret_product_master p ON p.pro_id = d.product_id
					LEFT JOIN employee e ON e.id_employee = d.item_emp_id
					WHERE d.bill_id = " . $bill_id . "
					ORDER BY d.bill_det_id ASC
				")->result_array();

				echo json_encode(array('status' => TRUE, 'items' => $items));
				break;

			case 'update_item_employee':

				$model = 'ret_billing_model';
				$bill_det_id = (int) $this->input->post('bill_det_id');
				$item_emp_id = (int) $this->input->post('item_emp_id');

				if ($bill_det_id <= 0) {
					echo json_encode(array('status' => FALSE, 'message' => 'Invalid bill detail ID.'));
					break;
				}

				$this->$model->updateData(
					array('item_emp_id' => ($item_emp_id > 0 ? $item_emp_id : NULL)),
					'bill_det_id',
					$bill_det_id,
					'ret_bill_details'
				);

				echo json_encode(array('status' => TRUE, 'message' => 'Item employee updated successfully.'));
				break;

			case 'save':

				$data = false;
				$bill_id = (int) $this->input->post('bill_id');
				$payment_date = $this->input->post('payment_date');
				$created_by = (int) $this->input->post('created_by');
				$cash_pay = $this->input->post('cash_pay');
				$card_payment = $this->input->post('card_payment');
				$chq_payment = $this->input->post('chq_payment');
				$nb_payment = $this->input->post('nb_payment');

				if (!is_array($card_payment)) $card_payment = array();
				if (!is_array($chq_payment))  $chq_payment  = array();
				if (!is_array($nb_payment))   $nb_payment   = array();

				$check = $this->$model->get_active_bill($bill_id);

				if ($check > 0) {

					$this->db->trans_begin();

					$status = $this->$model->deleteData('bill_id', $bill_id, 'ret_billing_payment');

					// $update_data = array(
					// 	'customer_name' => $_POST['customer_name'],
					// );

					//  $this->$model->updateData($update_data,'bill_id',$_POST['bill_id'],'ret_billing');

					//echo $this->db->last_query();exit;

					if ($status) {

						if ($cash_pay) {

							$fmt_pay_date = ($payment_date != '' ? implode('-', array_reverse(explode('-', $payment_date))) : NULL);
							$arrayCashPay = array(
								'bill_id'		=> $bill_id,
								'payment_amount'    => $cash_pay ? $cash_pay : 0,
								'payment_mode'      => 'Cash',
								'type'              => $cash_pay < 0 ? 2 : 1,
								'payment_for'	    => 1,
								'payment_status'    => 1,
								'payment_date'		=> $fmt_pay_date,
								'created_time'	    => $fmt_pay_date,
								'created_by'	    => $created_by,
								'updated_time'  	=> date('Y-m-d H:i:s'),
								'updated_by'   	=> $this->session->userdata('uid')
							);

							$cashPayInsert = $this->$model->insertData($arrayCashPay, 'ret_billing_payment');
						}

						if (count($chq_payment) > 0) {
							$arraychqPay = array();
							$fmt_pay_date = ($payment_date != '' ? implode('-', array_reverse(explode('-', $payment_date))) : NULL);
							foreach ($chq_payment as $value) {
								$arraychqPay[] = array(
									'bill_id'		=> $bill_id,
									'payment_amount' => $value['payment_amount'],
									'payment_for'	=> 1,
									'payment_status' => 1,
									'type' => $value['payment_amount'] < 0 ? 2 : 1,
									'payment_date'		=> $fmt_pay_date,
									'cheque_date'		=> date('Y-m-d', strtotime($value['cheque_date'])) . ' ' . date('H:i:s'),
									'payment_mode'	=> 'CHQ',
									'cheque_no'		=> $value['cheque_no'],
									'id_bank'		=> ($value['id_bank'] != '' ? $value['id_bank'] : NULL),
									'created_time'	    => $fmt_pay_date,
									'created_by'	    => $created_by,
									'updated_time'  	=> date('Y-m-d H:i:s'),
									'updated_by'   	=> $this->session->userdata('uid')
								);
							}
							if (!empty($arraychqPay)) {
								$chqPayInsert = $this->$model->insertBatchData($arraychqPay, 'ret_billing_payment');
							}
						}

						if (count($nb_payment) > 0) {
							$arrayNBPay = array();
							$fmt_pay_date = ($payment_date != '' ? implode('-', array_reverse(explode('-', $payment_date))) : NULL);
							foreach ($nb_payment as $value) {
								$arrayNBPay[] = array(
									'bill_id'		=> $bill_id,
									'payment_amount' => $value['amount'],
									'payment_for'	=> 1,
									'payment_status' => 1,
									'type' => $value['amount'] < 0 ? 2 : 1,
									'payment_date'		=> $fmt_pay_date,
									'payment_mode'	=> 'NB',
									'payment_ref_number' => $value['ref_no'],
									'NB_type'       => $value['nb_type'],
									'id_pay_device'		=> $value['id_device'] != '' ? $value['id_device'] : NULL,
									'id_bank'		=> ($value['id_bank'] != '' ? $value['id_bank'] : NULL),
									'created_time'	    => $fmt_pay_date,
									'created_by'	    => $created_by,
									'updated_time'  	=> date('Y-m-d H:i:s'),
									'updated_by'   	=> $this->session->userdata('uid'),
									'net_banking_date'	=> $value['nbdate'] != '' ? implode('-', array_reverse(explode('-', $value['nbdate']))) : NULL,
								);
							}
							if (!empty($arrayNBPay)) {
								$NbPayInsert = $this->$model->insertBatchData($arrayNBPay, 'ret_billing_payment');
							}
						}

						if (count($card_payment) > 0) {
							$arrayCardPay = array();
							$fmt_pay_date = ($payment_date != '' ? implode('-', array_reverse(explode('-', $payment_date))) : date('Y-m-d H:i:s'));
							foreach ($card_payment as $value) {
								$arrayCardPay[] = array(
									'bill_id'		=> $bill_id,
									'payment_amount' => $value['card_amt'],
									'payment_for'	=> 1,
									'payment_status' => 1,
									'type' => $value['card_amt'] < 0 ? 2 : 1,
									'payment_date'		=> $fmt_pay_date,
									'payment_mode'	=> ($value['card_type'] == 1 ? 'CC' : 'DC'),
									'card_no'		=> $value['card_no'],
									'id_bank'		=> ($value['id_bank'] != '' ? $value['id_bank'] : NULL),
									'id_pay_device'		=> $value['id_device'] != '' ? $value['id_device'] : NULL,
									'payment_ref_number' => $value['ref_no'],
									'card_date' => (isset($value['card_date']) && $value['card_date'] != '' ? implode('-', array_reverse(explode('-', $value['card_date']))) : NULL),
									'created_time'	    => $fmt_pay_date,
									'created_by'	    => $created_by,
									'updated_time'  	=> date('Y-m-d H:i:s'),
									'updated_by'   	=> $this->session->userdata('uid')
								);
							}
							if (!empty($arrayCardPay)) {
								$cardPayInsert = $this->$model->insertBatchData($arrayCardPay, 'ret_billing_payment');
							}
						}

						$data = true;
					}
				} else {

					$responseData = array('status' => FALSE, 'message' => 'No Record Found..');
				}

				if ($this->db->trans_status() === TRUE) {

					// echo "sd";exit;

					$this->db->trans_commit();

					$this->session->set_flashdata('chit_alert', array('message' => 'Billing Edited successfully', 'class' => 'success', 'title' => 'Billing'));

					$responseData = array('status' => TRUE, 'message' => 'Billing Edited successfully');
				} else {

					$this->db->trans_rollback();

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Billing'));

					$responseData = array('status' => FALSE, 'message' => 'Unable to proceed your request');
				}

				// redirect('admin_ret_billing/billing/list');

				echo json_encode($responseData);

				break;
		}
	}

	//BILL EDIT

	//credit issue details

	function get_customer_credit_details()

	{

		$model = "ret_billing_model";

		$data = $this->$model->get_customer_credit_details($_POST);

		echo json_encode($data);
	}

	//credit issue details

	//bill cancel otp

	function send_bill_cancel_otp()

	{

		$model = "ret_billing_model";

		//$data           = $this->$model->get_ret_settings('otp_approval_nos');

		$data = $this->$model->getBrnachOtpRegMobile($_POST['id_branch']);

		$mobile_num     = array(explode(',', $data));

		$sent_otp = '';

		$comp_details = $this->admin_settings_model->get_company();

		foreach ($mobile_num[0] as $mobile) {

			if ($mobile) {

				$this->session->unset_userdata("billcancel_otp");

				$OTP = mt_rand(100001, 999999);

				$sent_otp .= $OTP . ',';

				$this->session->set_userdata('billcancel_otp', $sent_otp);

				$this->session->set_userdata('billcancel_otp_exp', time() + 60);

				//$service = $this->admin_settings_model->get_service_by_code('BILL_DISC');

				$expiry = 1;

				$message = "Hi Your OTP For Bill Cancel is : " . $OTP . " Will expire within " . $expiry . " minute." . strtoupper($comp_details['company_name']) . ".";

				$otp_gen_time = date("Y-m-d H:i:s");

				$insData = array(

					'mobile'        => $mobile,

					'otp_code'      => $OTP,

					'otp_gen_time'  => date("Y-m-d H:i:s"),

					'module'        => 'Bill Cancellation Approval',

					'id_emp'        => $this->session->userdata('uid')

				);

				$this->db->trans_begin();

				$insId = $this->$model->insertData($insData, 'otp');

				if ($insId) {

					$this->send_sms($mobile, $message, '');
				}
			}
		}

		if ($insId) {

			$this->db->trans_commit();

			$status = array('status' => true, 'msg' => 'OTP sent Successfully');
		} else {

			$this->db->trans_rollback();

			$status = array('status' => false, 'msg' => 'Unabe To Send Try Again');
		}

		echo json_encode($status);
	}

	function verify_otp_for_billcancel()

	{

		$model                = "ret_billing_model";

		$post_otp             = $this->input->post('otp');

		$session_otp          = $this->session->userdata('billcancel_otp');

		$otp                  = array(explode(',', $session_otp));

		foreach ($otp[0] as $OTP) {

			if ($OTP == $post_otp) {

				if (time() >= $this->session->userdata('billcancel_otp_exp')) {

					$this->session->unset_userdata('billcancel_otp');

					$this->session->unset_userdata('billcancel_otp_exp');

					$status = array('status' => false, 'msg' => 'OTP has been expired');
				} else {

					$updData = array(

						'is_verified' => 1,

						'verified_time' => date("Y-m-d H:i:s"),

					);

					$this->db->trans_begin();

					$update_otp = $this->$model->updateData($updData, 'otp_code', $post_otp, 'otp');

					if ($update_otp) {

						$status = array('status' => true, 'msg' => 'OTP Verified Successfully..');

						$this->db->trans_commit();
					} else {

						$status = array('status' => false, 'msg' => 'Unable to Proceed Your Request..');

						$this->db->trans_rollback();
					}
				}

				break;
			} else {

				$status = array('status' => false, 'msg' => 'Please Enter Valid OTP');
			}
		}

		echo json_encode($status);
	}

	//bill cancel otp

	//Discount otp

	function admin_approval()

	{

		$model = "ret_billing_model";

		//$data           = $this->$model->get_ret_settings('otp_approval_nos');

		$data = $this->$model->getBrnachOtpRegMobile($_POST['id_branch']);

		$mobile_num     = array(explode(',', $data));

		$sent_otp = '';

		$comp_details = $this->admin_settings_model->get_company();

		foreach ($mobile_num[0] as $mobile) {

			if ($mobile) {

				$this->session->unset_userdata("discount_otp");

				$OTP = mt_rand(100001, 999999);

				$sent_otp .= $OTP . ',';

				$this->session->set_userdata('discount_otp', $sent_otp);

				$this->session->set_userdata('discount_otp_exp', time() + 60);

				$service = $this->admin_settings_model->get_service_by_code('BILL_DISC');

				$expiry = 1;

				$message = "Hi Your OTP For Billing Discount Approval : " . $OTP . " Will expire within " . $expiry . " minute." . strtoupper($comp_details['company_name']) . ".";

				$otp_gen_time = date("Y-m-d H:i:s");

				$insData = array(

					'mobile'        => $mobile,

					'otp_code'      => $OTP,

					'otp_gen_time'  => date("Y-m-d H:i:s"),

					'module'        => 'Billing Discount Approval',

					'id_emp'        => $this->session->userdata('uid')

				);

				$this->db->trans_begin();

				$insId = $this->$model->insertData($insData, 'otp');

				if ($insId) {

					$this->send_sms(9486528828, $message, $service['dlt_te_id']);
				}
			}
		}



		if ($insId) {

			$this->db->trans_commit();

			$status = array('status' => true, 'msg' => 'OTP sent Successfully' . $OTP);
		} else {

			$this->db->trans_rollback();

			$status = array('status' => false, 'msg' => 'Unabe To Send Try Again');
		}

		echo json_encode($status);
	}

	function verify_otp()

	{

		$model                = "ret_billing_model";

		$post_otp             = $this->input->post('otp');

		$session_otp          = $this->session->userdata('discount_otp');

		$otp                  = array(explode(',', $session_otp));

		foreach ($otp[0] as $OTP) {

			if ($OTP == $post_otp) {

				if (time() >= $this->session->userdata('discount_otp_exp')) {

					$this->session->unset_userdata('discount_otp');

					$this->session->unset_userdata('discount_otp_exp');

					$status = array('status' => false, 'msg' => 'OTP has been expired');
				} else {

					$updData = array(

						'is_verified' => 1,

						'verified_time' => date("Y-m-d H:i:s"),

					);

					$this->db->trans_begin();

					$update_otp = $this->$model->updateData($updData, 'otp_code', $post_otp, 'otp');

					if ($update_otp) {

						$status = array('status' => true, 'msg' => 'OTP Verified Successfully..');

						$this->db->trans_commit();
					} else {

						$status = array('status' => false, 'msg' => 'Unable to Proceed Your Request..');

						$this->db->trans_rollback();
					}
				}

				break;
			} else {

				$status = array('status' => false, 'msg' => 'Please Enter Valid OTP');
			}
		}

		echo json_encode($status);
	}

	//Discount otp

	public function send_credit_bill_otp()

	{

		$model = "ret_billing_model";

		//$data           = $this->$model->get_ret_settings('otp_approval_nos');

		$data = $this->$model->getBrnachOtpRegMobile($_POST['id_branch']);

		$mobile_num     = array(explode(',', $data));

		$sent_otp = '';

		$comp_details = $this->admin_settings_model->get_company();

		foreach ($mobile_num[0] as $mobile) {

			if ($mobile) {

				$this->session->unset_userdata("credit_bill_otp");

				$this->session->unset_userdata("credit_bill_otp_exp");

				$OTP = mt_rand(100001, 999999);

				$sent_otp .= $OTP . ',';

				$this->session->set_userdata('credit_bill_otp', $sent_otp);

				$this->session->set_userdata('credit_bill_otp_exp', time() + 60);

				//$service = $this->admin_settings_model->get_service_by_code('BILL_DISC');

				$expiry = 1;

				$message = "Hi Your OTP For credit Bill sales Approval is : " . $OTP . " Will expire within " . $expiry . " minute." . strtoupper($comp_details['company_name']) . ".";

				$otp_gen_time = date("Y-m-d H:i:s");

				$insData = array(

					'mobile'        => $mobile,

					'otp_code'      => $OTP,

					'otp_gen_time'  => date("Y-m-d H:i:s"),

					'module'        => 'Bill Cancellation Approval',

					'id_emp'        => $this->session->userdata('uid')

				);

				$this->db->trans_begin();

				$insId = $this->$model->insertData($insData, 'otp');

				if ($insId) {

					$this->send_sms($mobile, $message, '');
				}
			}
		}

		if ($insId) {

			$this->db->trans_commit();

			$status = array('status' => true, 'msg' => 'OTP sent Successfully');
		} else {

			$this->db->trans_rollback();

			$status = array('status' => false, 'msg' => 'Unabe To Send Try Again');
		}

		echo json_encode($status);
	}

	function verify_credit_otp()

	{

		$model                = "ret_billing_model";

		$post_otp             = $this->input->post('otp');

		$session_otp          = $this->session->userdata('credit_bill_otp');

		$otp                  = array(explode(',', $session_otp));

		foreach ($otp[0] as $OTP) {

			if ($OTP == $post_otp) {

				if (time() >= $this->session->userdata('credit_bill_otp_exp')) {

					$this->session->unset_userdata('credit_bill_otp');

					$this->session->unset_userdata('credit_bill_otp_exp');

					$status = array('status' => false, 'msg' => 'OTP has been expired');
				} else {

					$updData = array(

						'is_verified' => 1,

						'verified_time' => date("Y-m-d H:i:s"),

					);

					$this->db->trans_begin();

					$update_otp = $this->$model->updateData($updData, 'otp_code', $post_otp, 'otp');

					if ($update_otp) {

						$status = array('status' => true, 'msg' => 'OTP Verified Successfully..');

						$this->db->trans_commit();
					} else {

						$status = array('status' => false, 'msg' => 'Unable to Proceed Your Request..');

						$this->db->trans_rollback();
					}
				}

				break;
			} else {

				$status = array('status' => false, 'msg' => 'Please Enter Valid OTP');
			}
		}

		echo json_encode($status);
	}

	function getBranchDayClosingData()

	{

		$model = "ret_billing_model";

		$data = $this->$model->getBranchDayClosingData($_POST['id_branch']);

		echo json_encode($data);
	}

	public function service_bill($type = "", $id = "", $billno = "")

	{

		$model = "ret_billing_model";

		switch ($type) {

			case 'list':

				$data['main_content'] = "billing/service_bill/list";

				$this->load->view('layout/template', $data);

				break;

			case 'add':

				$profile                                    = $this->admin_settings_model->profileDB("get", $this->session->userdata('profile'));

				$data['billing']		                    = $this->$model->get_empty_record();

				$data['billing']['credit_sales_otp_req']    = $profile['credit_sales_otp_req'];

				$data['bill_other_item']                    = array("item_details" => array(), "old_matel_details" => array(), "stone_details" => array(), "other_material_details" => array(), "voucher_details" => array(), "chit_details" => array(), "advance_details" => array());

				$data['uom']		                        = $this->$model->getUOMDetails();

				//print_r($this->session->all_userdata());exit;

				$data['main_content'] = "billing/service_bill/form";

				//echo "<pre>"; print_r($data);exit;

				$this->load->view('layout/template', $data);

				break;

			case 'save':

				$addData            = $_POST['billing'];

				$allow_submit       = TRUE;

				$dCData             = $this->admin_settings_model->getBranchDayClosingData($addData['id_branch']);

				$fin_year           = $this->$model->get_FinancialYear();

				$item_details       = (isset($_POST['order']) ? $_POST['order'] : '');

				$form_secret        = isset($addData["form_secret"]) ? $addData["form_secret"] : '';

				if ($this->session->userdata('FORM_SECRET')) {

					if (strcasecmp($form_secret, ($this->session->userdata('FORM_SECRET'))) != 0) {

						$allow_submit = FALSE;

						$return_data = array('status' => FALSE, 'message' => 'Invalid Form Submit.');

						$this->session->set_flashdata('chit_alert', array('message' => 'Invalid Form Submit.', 'class' => 'danger', 'title' => 'Add Billing'));
					}

					if ($allow_submit) {

						if (sizeof($dCData) > 0) {

							$cheque_details	            = json_decode($addData['chq_pay'], true);

							$net_banking_details        = json_decode($addData['net_bank_pay'], true);

							$card_pay_details	        = json_decode($addData['card_pay'], true);

							$bill_no                    = $this->$model->service_bill_number_generator($addData['id_branch']);   //Bill Number Generate

							$bill_date                  = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

							$metal_rate                 = $this->$model->get_branchwise_rate($addData['id_branch']);

							$data = array(

								'bill_no'		        => $bill_no,

								'fin_year_code'		    => $fin_year['fin_year_code'],

								'form_secret'	        => $addData['form_secret'],

								'id_customer'   	    => (!empty($addData['bill_cus_id']) ? $addData['bill_cus_id'] : 0),

								'total_bill_amount'	    => (!empty($addData['total_cost']) ? $addData['total_cost'] : 0),

								'total_amount_received'	=> (!empty($addData['tot_amt_received']) ? $addData['tot_amt_received'] : 0),

								'bill_date'	            => $bill_date,

								'created_on'	        => date("Y-m-d H:i:s"),

								'created_by'            => $this->session->userdata('uid'),

								'counter_id'            => ($this->session->userdata('counter_id') != '' ? $this->session->userdata('counter_id') : NULL),

								'id_branch'             => $addData['id_branch'],

							);

							$this->db->trans_begin();

							$insId = $this->$model->insertData($data, 'ret_service_bill');

							if ($insId) {

								if (!empty($item_details)) {

									$arrayBillSales = array();

									foreach ($item_details['is_est_details'] as $key => $val) {

										$arrayBillSales = array(

											'id_service_bill' => $insId,

											'id_service'      => $item_details['repair'][$key],

											'id_product'      => $item_details['product'][$key],

											'piece'           => $item_details['piece'][$key],

											'weight'          => $item_details['completed_weight'][$key],

											'total_cgst'      => $item_details['cgst'][$key],

											'total_sgst'      => $item_details['sgst'][$key],

											'total_igst'      => $item_details['igst'][$key],

											'item_total_tax'  => $item_details['repair_tot_tax'][$key],

											'item_total_cost' => $item_details['amount'][$key],

											'tax_percentage'  => $item_details['repair_percent'][$key],

										);

										$billDetId = $this->$model->insertData($arrayBillSales, 'ret_service_bill_details');
									}
								}

								if ($addData['cash_payment'] > 0) {

									$arrayCashPay = array(

										'id_service_bill'   => $insId,

										'payment_amount'    => $addData['cash_payment'],

										'payment_mode'      => 'Cash',

										'type'              => ($addData['pay_to_cus'] > 0 ? 2 : ($addData['pay_to_cus'] > 0 ? 3 : 1)),

										'payment_for'	    => ($addData['bill_type'] == 6 ? 2 : ($addData['pay_to_cus'] > 0 ? 3 : 1)),

										'payment_status'    => 1,

										'payment_date'		=> date("Y-m-d H:i:s"),

										'created_time'	    => date("Y-m-d H:i:s"),

										'created_by'	    => $this->session->userdata('uid')

									);

									if (!empty($arrayCashPay)) {

										$cashPayInsert = $this->$model->insertData($arrayCashPay, 'ret_service_bill_payment');
									}
								}

								if (sizeof($card_pay_details) > 0) {

									foreach ($card_pay_details as $card_pay) {

										$arrayCardPay[] = array(

											'id_service_bill' => $insId,

											'card_type'     => $card_pay['card_name'],

											'payment_amount' => $card_pay['card_amt'],

											'id_pay_device' => ($card_pay['id_device'] != '' ? $card_pay['id_device'] : NULL),

											'payment_status' => 1,

											'payment_date'	=> date("Y-m-d H:i:s"),

											'payment_mode'	=> ($card_pay['card_type'] == 1 ? 'CC' : 'DC'),

											'card_no'		=> ($card_pay['card_no'] != '' ? $card_pay['card_no'] : NULL),

											'payment_ref_number' => ($card_pay['ref_no'] != '' ? $card_pay['ref_no'] : NULL),


											'card_date' => (isset($card_pay['card_date']) && $card_pay['card_date']!='' ? implode('-', array_reverse(explode('-', $card_pay['card_date']))) : NULL),
											'created_time'	=> date("Y-m-d H:i:s"),

											'created_by'	=> $this->session->userdata('uid')

										);
									}

									if (!empty($arrayCardPay)) {

										$cardPayInsert = $this->$model->insertBatchData($arrayCardPay, 'ret_service_bill_payment');
									}
								}

								if (sizeof($cheque_details) > 0) {

									foreach ($cheque_details as $chq_pay) {

										$cheque_deposit_date = ($chq_pay['cheque_date'] != '' ? date_create($chq_pay['cheque_date']) : NULL);

										$cheque_date = ($chq_pay['cheque_date'] != '' ? date_format($cheque_deposit_date, "Y-m-d") : NULL);

										$arraychqPay[] = array(

											'id_service_bill'   => $insId,

											'payment_amount'    => $chq_pay['payment_amount'],

											'payment_status'    => 1,

											'payment_date'		=> date("Y-m-d H:i:s"),

											'cheque_date'		=> ($chq_pay['cheque_date'] != '' ? $cheque_date : NULL),

											'payment_mode'	    => 'CHQ',

											'cheque_no'		    => ($chq_pay['cheque_no'] != '' ? $chq_pay['cheque_no'] : NULL),

											'bank_name'		    => ($chq_pay['bank_name'] != '' ? $chq_pay['bank_name'] : NULL),

											'bank_branch'	    => ($chq_pay['bank_branch'] != '' ? $chq_pay['bank_branch'] : NULL),

											'created_time'	    => date("Y-m-d H:i:s"),

											'created_by'	    => $this->session->userdata('uid')

										);
									}

									if (!empty($arraychqPay)) {

										$chqPayInsert = $this->$model->insertBatchData($arraychqPay, 'ret_service_bill_payment');
									}
								}

								if (sizeof($net_banking_details) > 0) {

									foreach ($net_banking_details as $nb_pay) {

										$arrayNBPay[] = array(

											'id_service_bill'	=> $insId,

											'payment_amount'    => $nb_pay['amount'],

											'id_pay_device'     => ($nb_pay['id_device'] != '' ? $nb_pay['id_device'] : NULL),

											'payment_status'    => 1,

											'payment_date'		=> date("Y-m-d H:i:s"),

											'payment_mode'	    => 'NB',

											'id_bank'           => ($nb_pay['id_bank'] != '' ? $nb_pay['id_bank'] : NULL),

											'payment_ref_number' => ($nb_pay['ref_no'] != '' ? $nb_pay['ref_no'] : NULL),

											'NB_type'           => ($nb_pay['nb_type'] != '' ? $nb_pay['nb_type'] : NULL),

											'net_banking_date'  => ($nb_pay['nb_date'] != '' ? $nb_pay['nb_date'] : date("Y-m-d")),

											'created_time'	    => date("Y-m-d H:i:s"),

											'created_by'	    => $this->session->userdata('uid')

										);
									}

									if (!empty($arrayNBPay)) {

										$NbPayInsert = $this->$model->insertBatchData($arrayNBPay, 'ret_service_bill_payment');
									}
								}
							}
						}

						if ($this->db->trans_status() === TRUE) {

							$this->db->trans_commit();

							$log_data = array(

								'id_log'        => $this->session->userdata('id_log'),

								'event_date'    => date("Y-m-d H:i:s"),

								'module'        => 'Billing',

								'operation'     => 'Add',

								'record'        =>  $insId,

								'remark'        => 'Record added successfully'

							);

							$this->log_model->log_detail('insert', '', $log_data);

							$return_data = array('status' => TRUE, 'id' => $insId);

							$this->session->set_flashdata('chit_alert', array('message' => 'Billing added successfully', 'class' => 'success', 'title' => 'Add Billing'));

							//$this->session->unset_userdata('FORM_SECRET');

						} else {

							$this->db->trans_rollback();

							$return_data = array('status' => FALSE, 'id' => '');

							echo $this->db->_error_message() . "<br/>";

							echo $this->db->last_query();
							exit;

							$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Billing'));
						}
					}
				} else {

					$return_data = array('status' => FALSE, 'message' => 'Unbale to Set the Form Secret Value.Plase Try Again');
				}

				echo json_encode($return_data);

				break;

			default:

				$list = $this->$model->ajax_getServiceBillList($_POST);

				$profile = $this->admin_settings_model->profileDB("get", $this->session->userdata('profile'));

				$access = $this->admin_settings_model->get_access('admin_ret_billing/service_bill/list');

				$data = array(

					'list'  => $list,

					'access' => $access,

					'profile' => $profile

				);

				echo json_encode($data);
		}
	}

	function service_bill_invoice($id)

	{

		$model = "ret_billing_model";

		$data['billing'] = $this->$model->getServiceBillingDetails($id);

		$data['payment'] = $this->$model->getServiceBillPaymentDetails($id);

		$data['item_details'] = $this->$model->getServiceBillItemDetails($id);

		$data['comp_details'] = $this->$model->getCompanyDetails($data['billing']['id_branch']);

		$data['metal_rate'] = $this->$model->get_branchwise_rate($data['billing']['id_branch']);

		$data['settings']		= $this->$model->get_retSettings();

		//echo "<pre>";print_r($data);exit;

		$this->load->helper(array('dompdf', 'file'));

		$dompdf = new DOMPDF();

		$html = $this->load->view('billing/service_bill/receipt_billing', $data, true);

		$dompdf->load_html($html);

		$dompdf->set_paper("a4", "portriat");

		$dompdf->render();

		while(ob_get_level()) ob_end_clean();
		$dompdf->stream("Receipt.pdf", array('Attachment' => 0));
	}

	function viewdb($id = '')
	{

		$model = "ret_billing_model";

		$data['data'] = $this->$model->viewdb($id);

		$this->load->view('billing/print/viewdb', $data);
	}

	function cancel_service_bill()

	{

		$model   = "ret_billing_model";

		$bill_id = $_POST['bill_id'];

		$remarks = $_POST['remarks'];

		$upd_data = array(

			"bill_status"	=> 2,

			'updated_on'	=> date("Y-m-d H:i:s"),

			'cancelled_date' => date("Y-m-d H:i:s"),

			'cancel_reason' => $remarks,

			'cancelled_by'	=> $this->session->userdata('uid')

		);

		$this->db->trans_begin();

		$status = $this->$model->updateData($upd_data, 'id_service_bill', $bill_id, 'ret_service_bill');

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$return_data = array('status' => TRUE);

			$this->session->set_flashdata('chit_alert', array('message' => 'Bill No cancelled successfully', 'class' => 'success', 'title' => 'Cancel Bill'));
		} else {

			$this->db->trans_rollback();

			$return_data = array('status' => false);

			$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Cancel Bill'));
		}

		echo json_encode($return_data);
	}

	function order_place()

	{

		$model          = "ret_order_model";

		$billing_model          = "ret_billing_model";

		$fin_year       = $this->$model->get_FinancialYear();

		$req_data       = $_POST['req_data'];

		$karigar_details    = [];

		foreach ($req_data as $r) {

			$karigar_details[$r['id_karigar']][] = $r;
		}

		foreach ($karigar_details as $id_karigar => $tag_details) {

			$pur_no = $this->$model->generatePurNo();

			$total_pcs = 0;

			$total_weight = 0;

			$order = array(

				'fin_year_code'     => $fin_year['fin_year_code'],

				'pur_no'            => $pur_no,

				'order_status'		=> 3,

				'order_type'		=> 1,

				'order_pcs'			=> 0,

				'order_approx_wt'	=> 0,

				'order_for'			=> 1,

				'is_against_approval_stock'			=> 1,

				'id_karigar'		=> $id_karigar,

				'order_date'		=> date("Y-m-d H:i:s"),

				'createdon'         => date("Y-m-d H:i:s"),

				'order_taken_by'    => $this->session->userdata('uid')

			);

			$this->db->trans_begin();

			$insOrder = $this->$model->insertData($order, 'customerorder');

			foreach ($tag_details as $val) {

				$tag_det = $this->$billing_model->get_approval_tag_details($val['tag_id']);

				$total_pcs += $tag_det['piece'];

				$total_weight += $tag_det['gross_wt'];

				$orderDetails = array(

					'id_customerorder'	=> $insOrder,

					'approval_tagid'	=> $val['tag_id'],

					'orderstatus'		=> 3,

					'id_weight_range'	=> NULL,

					'id_product'		=> (!empty($tag_det['product_id']) ? $tag_det['product_id'] : NULL),

					'design_no'			=> (!empty($tag_det['design_id']) ? $tag_det['design_id'] : NULL),

					'id_sub_design'		=> (!empty($tag_det['id_sub_design']) ? $tag_det['id_sub_design'] : NULL),

					'totalitems'		=> (!empty($tag_det['piece']) ? $tag_det['piece'] : NULL),

					'weight'		    => (!empty($tag_det['gross_wt']) ? $tag_det['gross_wt'] : NULL),

					'size'				=> (!empty($tag_det['size']) ? $tag_det['size'] : NULL),

					'smith_due_date'	=> NULL,

					'order_date'		=> date("Y-m-d H:i:s"),

					'id_employee'       => $this->session->userdata('uid'),

				);

				$insOrderdet = $this->$model->insertData($orderDetails, 'customerorderdetails');
			}

			$this->$model->updateData(array('order_pcs' => $total_pcs, 'order_approx_wt' => $total_weight), 'id_customerorder', $insOrder, 'customerorder');
		}

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$this->session->set_flashdata('chit_alert', array('message' => 'Order Placed Successfully', 'class' => 'success', 'title' => 'Order'));

			$response_data = array('status' => TRUE, 'msg' => 'Order Placed Successfully..');
		} else {

			//echo $this->db->_error_message();

			echo $this->db->last_query();
			exit;

			$this->db->trans_rollback();

			$this->session->set_flashdata('chit_alert', array('message' => 'Unable to Proceed Your Request..', 'class' => 'danger', 'title' => 'Order'));

			$response_data = array('status' => FALSE, 'msg' => 'Unable to Proceed Your Request..');
		}

		echo json_encode($response_data);
	}

	function update_branch()

	{

		$billing_model          = "ret_billing_model";

		$req_data       = $_POST['req_data'];

		$karigar_details    = [];

		$ho              =  $this->$billing_model->get_headOffice();

		foreach ($req_data as $val) {

			$tag_det = $this->$billing_model->get_approval_tag_details($val['tag_id']);

			if ($tag_det['tag_status'] == 11) {

				$dCData = $this->admin_settings_model->getBranchDayClosingData($tag_det['current_branch']);

				$bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

				$this->$billing_model->updateData(array('tag_status' => 0, 'is_approval_stock_converted' => 1, 'app_stk_converted_date' => date("Y-m-d H:i:s"), 'app_stk_converted_by' => $this->session->userdata('uid'), 'updated_time' => date("Y-m-d H:i:s"), 'updated_by' => $this->session->userdata('uid')), 'tag_id', $val['tag_id'], 'ret_taging');

				$ho_log_data = array(

					'tag_id'	  => $val['tag_id'],

					'date'		  => $bill_date,

					'status'	  => 0,

					'from_branch' => NULL,

					'to_branch'   => $ho['id_branch'],

					'created_on'  => date("Y-m-d H:i:s"),

					'created_by'  => $this->session->userdata('uid'),

				);

				$this->$billing_model->insertData($ho_log_data, 'ret_taging_status_log'); //Update Tag lot status

				if ($ho['id_branch'] != $tag_det['current_branch']) {

					$ho_log_data = array(

						'tag_id'	  => $val['tag_id'],

						'date'		  => $bill_date,

						'status'	  => 4,

						'from_branch' => $ho['id_branch'],

						'to_branch'   => $tag_det['current_branch'],

						'created_on'  => date("Y-m-d H:i:s"),

						'created_by'  => $this->session->userdata('uid'),

					);

					$this->$billing_model->insertData($ho_log_data, 'ret_taging_status_log'); //Update Tag lot status

					$branch_log_data = array(

						'tag_id'	  => $val['tag_id'],

						'date'		  => $bill_date,

						'status'	  => 0,

						'from_branch' => $ho['id_branch'],

						'to_branch'   => $tag_det['current_branch'],

						'created_on'  => date("Y-m-d H:i:s"),

						'created_by'  => $this->session->userdata('uid'),

					);

					$this->$billing_model->insertData($branch_log_data, 'ret_taging_status_log'); //Update Tag lot status

				}

				$tag_status = $this->$model->get_tag_status($val['tag_id']);

				if ($tag_status['id_section'] != null && $tag_status['id_section'] != '') {

					$secttag_log = array(

						'tag_id'	          => $val['tag_id'],

						'date'		          => $bill_date,

						'status'	          => 0,

						'from_branch'         => 1,

						'to_branch'           => $tag_det['current_branch'],

						'from_section'        => NULL,

						'to_section'          => $tag_status['id_section'],

						'created_on'          => date("Y-m-d H:i:s"),

						'created_by'          => $this->session->userdata('uid'),

					);

					$this->$model->insertData($secttag_log, 'ret_section_tag_status_log');
				}
			}
		}

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$this->session->set_flashdata('chit_alert', array('message' => 'Tag Status Changed Successfully', 'class' => 'success', 'title' => 'Order'));

			$response_data = array('status' => TRUE, 'msg' => 'Tag Status Changed Successfull..');
		} else {

			//echo $this->db->_error_message();

			echo $this->db->last_query();
			exit;

			$this->db->trans_rollback();

			$this->session->set_flashdata('chit_alert', array('message' => 'Unable to Proceed Your Request..', 'class' => 'danger', 'title' => 'Order'));

			$response_data = array('status' => FALSE, 'msg' => 'Unable to Proceed Your Request..');
		}

		echo json_encode($response_data);
	}

	public function getCustomerDet()
	{

		$model = "ret_billing_model";

		$data = $this->$model->getCustomerDet($_POST['id_branch'], $_POST['id_customer']);

		echo json_encode($data);
	}

	public function getCreditPending()

	{

		$model = "ret_billing_model";

		$data = $this->$model->getCreditPending($_POST);

		echo json_encode($data);
	}

	public function getCustomerSalesDetails()

	{

		$model = "ret_billing_model";

		$data = $this->$model->getCustomerSalesDetails($_POST);

		echo json_encode($data);
	}

	public function get_payModes()

	{

		$model = "ret_billing_model";

		$data = $this->$model->get_payModes($_POST);

		echo json_encode($data);
	}

	public function bill_split($type = "", $id = "")

	{

		$model = "ret_billing_model";

		switch ($type) {

			case 'list':

				$data['billing']		                    = $this->$model->get_empty_record();

				$data['billing']['credit_sales_otp_req']    = $profile['credit_sales_otp_req'];

				$data['bill_other_item']                    = array("item_details" => array(), "old_matel_details" => array(), "stone_details" => array(), "other_material_details" => array(), "voucher_details" => array(), "chit_details" => array(), "advance_details" => array());

				$data['uom']		                        = $this->$model->getUOMDetails();

				$data['main_content'] = "billing/billsplit";

				$this->load->view('layout/template', $data);

				$this->load->view('layout/common_customerslider');

				break;

			case 'esti_details':

				$data = $this->$model->get_est_split_details($_POST);

				echo json_encode($data);

				break;
		}
	}

	public function advance_transfer($type = '')
	{

		$model = "ret_billing_model";

		switch ($type) {

			case 'list':

				$data['main_content'] = "billing/advance_transfer/list";

				$this->load->view('layout/template', $data);

				break;

			case 'add':

				$data['otp_settings']		= $this->$model->get_ret_settings('advance_transfer_otp');

				$data['settings']			= $this->$model->get_retSettings();

				$data['main_content'] = "billing/advance_transfer/form";

				$this->load->view('layout/template', $data);

				break;

			case 'save':

				$bill_no = $this->$model->bill_no_generate($_POST['id_branch'], $_POST['is_eda']);

				$fin_year = $this->$model->get_FinancialYear();

				$dCData = $this->admin_settings_model->getBranchDayClosingData($_POST['id_branch']);

				$bill_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

				$transfer_amount = json_decode($_POST['transfer_amount'], true);

				$data = array(

					'fin_year_code' => $fin_year['fin_year_code'],

					'bill_no'		=> $bill_no,

					'type'			=> 2,

					'bill_date'     => $bill_date,

					'amount'		=> $_POST['tot_transfer_amount'],

					'id_branch'		=> ($_POST['id_branch'] != "" ? $_POST['id_branch'] : NULL),

					'receipt_type'	=> 7,

					'id_customer'	=> ($_POST['to_cus_id'] != '' ? $_POST['to_cus_id'] : NULL),

					'is_eda'	=>  $_POST['is_eda'],

					'created_by' 	=> $this->session->userdata('uid'),

					'counter_id'    => ($this->session->userdata('counter_id') != '' ? $this->session->userdata('counter_id') : NULL),

					'created_on' 	=> date("Y-m-d H:i:s"),

				);

				$insId = $this->$model->insertData($data, 'ret_issue_receipt');

				if ($insId) {

					foreach ($transfer_amount as $data) {

						$data = array(

							'id_issue_receipt'		 => $insId,

							'transfer_amount'        => $data['transfer_amount'],

							'transfer_cash_amt'      => $data['cash_pay'],

							'transfer_receipt_id'    => $data['adv_trans_id_issue_receipt'],

							'otp'      				 => $data['adv_trans_otp'] != '' ? $data['adv_trans_otp'] : NULL,

						);

						$this->db->trans_begin();

						$data = $this->$model->insertData($data, 'ret_advance_transfer');


						if ($this->db->trans_status() === TRUE) {

							$this->db->trans_commit();

							$return_data = array('status' => TRUE, 'id' => $insId);
						} else {

							$this->db->trans_rollback();

							$return_data = array('status' => FALSE, 'message' => 'Unable to proceed the requested process');

							echo $this->db->_error_message() . "<br/>";

							// echo $this->db->last_query();exit;

						}
					}

					echo json_encode($return_data);
				}

				break;
		}
	}


	function adtrnssendotp()

	{

		$model = "ret_billing_model";

		$mobile_num     = $this->input->post('mobile');

		$send_resend     = $this->input->post('send_resend');

		$sent_otp = '';

		if ($mobile_num != '') {

			$this->db->trans_begin();

			$this->session->unset_userdata("advc_trns_otp");

			$this->session->unset_userdata("advc_trns_otp_exp");

			$OTP = mt_rand(100001, 999999);

			$this->session->set_userdata('advc_trns_otp', $OTP);

			$this->session->set_userdata('advc_trns_otp_exp', time() + 60);

			$message = "Hi Your OTP  For Advance Transfer is :  " . $OTP . " Will expire within 1 minute.";

			$otp_gen_time = date("Y-m-d H:i:s");

			$insData = array(

				'mobile' => $mobile_num,

				'otp_code' => $OTP,

				'otp_gen_time' => date("Y-m-d H:i:s"),

				'module' => 'Advance Transfer',

				'send_resend' => $send_resend,

				'id_emp' => $this->session->userdata('uid')

			);

			$insId = $this->$model->insertData($insData, 'otp');
		}

		if ($insId) {

			$this->db->trans_commit();

			$this->ad_trans_send_sms($mobile_num, $message);

			$status = array('status' => true, 'msg' => 'OTP sent Successfully');
		} else {

			$this->db->trans_rollback();

			$status = array('status' => false, 'msg' => 'Unabe To Send Try Again');
		}

		echo json_encode($status);
	}

	function verify_advance_transfer_otp()

	{

		$model = "ret_billing_model";

		$post_otp = $this->input->post('otp');

		$session_otp = $this->session->userdata('advc_trns_otp');

		$otp = array(explode(',', $session_otp));

		$this->db->trans_begin();

		if ($post_otp != '') {

			foreach ($otp[0] as $OTP) {

				if ($OTP == $post_otp) {

					if (time() >= $this->session->userdata('advc_trns_otp_exp')) {

						$this->session->unset_userdata('advc_trns_otp');

						$this->session->unset_userdata('advc_trns_otp_exp');

						$status = array('status' => false, 'msg' => 'OTP has been expired');
					} else {

						$this->db->trans_commit();

						$updData = array('is_verified' => 1, 'verified_time' => date("Y-m-d H:i:s"));

						$updStatus = $this->$model->updateData($updData, 'otp_code', $post_otp, 'otp');

						$status = array('status' => true, 'msg' => 'OTP Verified Successfully.', 'verified_otp' => $post_otp);
					}

					break;
				} else {

					$status = array('status' => false, 'msg' => 'Please Enter Valid OTP');
				}
			}
		} else {

			$status = array('status' => false, 'msg' => 'Please Enter Valid OTP');
		}

		echo json_encode($status);
	}

	function ad_trans_send_sms($mobile, $message, $dlt_te_id = '')

	{

		if ($this->config->item('sms_gateway') == '1') {

			$this->sms_model->sendSMS_MSG91($mobile, $message, '', $dlt_te_id);
		} elseif ($this->config->item('sms_gateway') == '2') {

			$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
		}
	}



	function item_delivery($type = "")

	{

		$model = "ret_billing_model";

		$SETT_MOD = "admin_settings_model";

		switch ($type) {

			case 'list':

				$data['main_content'] = "billing/item_delivery";

				$data['access'] = $this->$SETT_MOD->get_access('admin_ret_billing/item_delivery/list');


				$this->load->view('layout/template', $data);

				break;

			case 'ajax':

				$list = $this->$model->get_DeliveryList($_POST);

				$access = $this->admin_settings_model->get_access('admin_ret_billing/item_delivery/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);

				break;
		}
	}

	function update_delivery_status()

	{

		$model = "ret_billing_model";

		$this->db->trans_begin();

		$reqdata   = $this->input->post('req_data');

		foreach ($reqdata as $data) {

			$dCData = $this->admin_settings_model->getBranchDayClosingData($data['id_branch']);

			$deliver_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

			$updID = $this->$model->updateData(array('is_delivered' => 2, 'delivered_date' => $deliver_date, 'delivered_by' => $this->session->userdata('uid')), 'bill_det_id', $data['bill_det_id'], 'ret_bill_details');
		}

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$data = array('status' => true, 'msg' => 'Delivery Status Updated Successfully.');
		} else {

			$this->db->trans_rollback();

			$data = array('status' => false, 'msg' => 'Unable TO Proceed Your Request.');
		}

		echo json_encode($data);
	}

	//E-invoicing â€” delegates to common_helper.php IRN engine

	function generateEinvoice($billId)
	{
		// Call the global helper function (from helpers/lmx/functions/common_helper.php)
		\generateEinvoice($billId);
	}

	function einvoiceimagecreate()
	{
		$model = "ret_billing_model";
		$irnlists = $this->$model->geteinvoiceirndetails();
		foreach ($irnlists as $ikey => $ival) {
			base64_to_jpeg_irn($ival['qrcodeimage'], $ival['cusdel_irn'] . ".jpg");
		}
	}

	//E-invoice end process here


	//Bill No Format Settings

	public function bill_number_format($type = "")

	{

		$model = "ret_billing_model";

		$set_model = "admin_settings_model";

		switch ($type) {

			case 'list':

				$data['billing']		= $this->$model->get_empty_record();

				$data['main_content'] = "billing/bill_number_format/list";

				$this->load->view('layout/template', $data);

				break;

			case 'add':

				$data['exists']			=  $this->$model->get_data();


				// echo "<pre>";print_r($data['exists']);exit;

				//SALES

				$data['sale_format'] = array(

					array(

						'value'  		=> "-@@short_code@@",

						"text"   		=> "SA"

					),

					array(

						'value'  		=> "-@@branch_code@@",

						"text"   		=> "Branch Code"

					),

					array(

						'value'     	=> '-@@metal_code@@',

						'text'			=> 'Metal Short Code'

					),

					array(

						'value'     	=> '-@@bill_no@@',

						'text'			=> 'Bill No'

					),

					array(

						'value'     	=> '@@fin_year@@',

						'text'			=> 'Financial Year'

					),

				);

				//SALES AND PURCHASE

				$data['sale_and_purchase_format'] = array(

					array(

						'value'  		=> "-@@short_code@@",

						"text"   		=> "SP"

					),

					array(

						'value' 		=> "-@@branch_code@@",

						"text"   		=> "Branch Code"

					),

					array(

						'value'     	=> '-@@metal_code@@',

						'text'			=> 'Metal Code'

					),

					array(

						'value'     	=> '-@@bill_no@@',

						'text'			=> 'Bill No'

					),

					array(

						'value'     	=> '-@@fin_year@@',

						'text'			=> 'Financial Year'

					),

				);

				//SALES AND RETURN

				$data['sale_and_return_format'] = array(

					array(

						'value'  		=> "-@@short_code@@",

						"text"   		=> "SR"

					),

					array(

						'value'         => "-@@branch_code@@",

						"text"          => "Branch Code"

					),

					array(

						'value'     	=> '-@@metal_code@@',

						'text'			=> 'Metal Code'

					),

					array(

						'value'     	=> '-@@bill_no@@',

						'text'			=> 'Bill No'

					),

					array(

						'value'     	=> '-@@fin_year@@',

						'text'			=> 'Financial Year'

					),

				);

				// PURCHASE

				$data['purchase_format'] = array(

					array(

						'value'        => "-@@short_code@@",

						"text"         => "PU"

					),

					array(

						'value'        => "-@@branch_code@@",

						"text"         => "Branch Code"

					),

					array(

						'value'     	=> '-@@metal_code@@',

						'text'			=> 'Metal Code'

					),

					array(

						'value'     	=> '-@@bill_no@@',

						'text'			=> 'Bill No'

					),

					array(

						'value'     	=> '-@@fin_year@@',

						'text'			=> 'Financial Year'

					),

				);

				//ORDER ADVANCE

				$data['ord_adv_format'] = array(

					array(

						'value'		  	=> "-@@short_code@@",

						"text"  		=> "OA"

					),

					array(

						'value' 		=> "-@@branch_code@@",

						"text"  		=> "Branch Code"

					),

					array(

						'value'     	=> '-@@metal_code@@',

						'text'			=> 'Metal Code'

					),

					array(

						'value'     	=> '-@@bill_no@@',

						'text'			=> 'Bill No'

					),

					array(

						'value'     	=> '-@@fin_year@@',

						'text'			=> 'Financial Year'

					),

				);

				//SALES RETURN

				$data['sale_return_format'] = array(

					array(

						'value'  		=> "-@@short_code@@",

						"text"  		=> "SR"

					),

					array(

						'value'  		=> "-@@branch_code@@",

						"text"   		=> "Branch Code"

					),

					array(

						'value'     	=> '-@@metal_code@@',

						'text'			=> 'Metal Code'

					),

					array(

						'value'     	=> '-@@bill_no@@',

						'text'			=> 'Bill No'

					),

					array(

						'value'     	=> '-@@fin_year@@',

						'text'			=> 'Financial Year'

					),

				);

				//CREDIT COLLECTION

				$data['credit_collection_format'] = array(

					array(

						'value'         => "-@@short_code@@",

						"text"          => "CC"

					),

					array(

						'value'         => "-@@branch_code@@",

						"text"          => "Branch Code"

					),

					array(

						'value'     	=> '-@@metal_code@@',

						'text'			=> 'Metal Code'

					),

					array(

						'value'     	=> '-@@bill_no@@',

						'text'			=> 'Bill No'

					),

					array(

						'value'     	=> '-@@fin_year@@',

						'text'			=> 'Financial Year'

					),

				);

				//Chit Pre Close

				$data['chit_format'] = array(

					array(

						'value'         => "-@@short_code@@",

						"text"          => "CH"

					),

					array(

						'value'         => "-@@branch_code@@",

						"text"          => "Branch Code"

					),

					array(

						'value'     	=> '-@@metal_code@@',

						'text'			=> 'Metal Code'

					),

					array(

						'value'     	=> '-@@bill_no@@',

						'text'			=> 'Bill No'

					),

					array(

						'value'     	=> '-@@fin_year@@',

						'text'			=> 'Financial Year'

					),

				);

				//Order Delivery

				$data['order_delivery'] = array(

					array(

						'value'         => "-@@short_code@@",

						"text"          => "Ord_del"

					),

					array(

						'value'         => "-@@branch_code@@",

						"text"          => "Branch Code"

					),

					array(

						'value'     	=> '-@@metal_code@@',

						'text'			=> 'Metal Code'

					),

					array(

						'value'     	=> '-@@bill_no@@',

						'text'			=> 'Bill No'

					),

					array(

						'value'     	=> '-@@fin_year@@',

						'text'			=> 'Financial Year'

					),

				);

				//Repair Order Delivery

				$data['repair_order_delivery'] = array(

					array(

						'value'         => "-@@short_code@@",

						"text"          => "RE"

					),

					array(

						'value'         => "-@@branch_code@@",

						"text"          => "Branch Code"

					),

					array(

						'value'     	=> '-@@metal_code@@',

						'text'			=> 'Metal Code'

					),

					array(

						'value'     	=> '-@@bill_no@@',

						'text'			=> 'Bill No'

					),

					array(

						'value'     	=> '-@@fin_year@@',

						'text'			=> 'Financial Year'

					),

				);

				$data['main_content'] =  "billing/bill_number_format/form";

				$data['access'] = $this->$set_model->get_access('admin_ret_billing/bill_number_format/add');


				// $data['type'] =  1;

				$this->load->view('layout/template', $data);

				break;

			case 'save':

				$data = $this->input->post('content');

				foreach ($data as $val) {

					$data = array(

						'bill_type'		        => ($val['bill_type']),

						'bill_no_format'		=> ($val['text']),

						'created_on'             =>  date("Y-m-d H:i:s"),

					);

					$this->$model->insertData($data, 'bill_no_format');
				}

				break;

			case 'update':

				$data = $this->input->post('content');

				//print_r($_POST);exit;

				foreach ($data as $val) {

					$updatedata = array(

						'bill_type'		    => ($val['bill_type']),

						'bill_no_format'		=> ($val['text']),

						'created_on'         =>  date("Y-m-d H:i:s"),

					);

					$this->db->trans_begin();

					$this->$model->updateData($updatedata, 'bill_type', $val['bill_type'], 'bill_no_format');
				}

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					$this->session->set_flashdata('chit_alert', array('message' => 'Bill Format Updated successfully', 'class' => 'success', 'title' => 'Bill Format'));

					redirect('admin_ret_billing/bill_number_format/add');
				} else {

					$this->db->trans_rollback();

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Bill Format'));

					redirect('admin_ret_billing/bill_number_format/add');
				}

				break;

			default:

				$list = $this->$model->ajax_get_billno();

				$access = $this->admin_settings_model->get_access('admin_ret_billing/bill_no_format/list');

				$data = array(

					'list'  => $list,

					'access' => $access,

				);

				echo json_encode($data);
		}
	}

	//Bill No Format Settings



	function get_customer_tcs_percent()

	{

		$model = "ret_billing_model";

		$id_customer = $_POST['id_customer'];



		$fin_year = $this->$model->get_FinancialYear();

		$data['tax_per'] = $this->$model->get_ret_settings('tcs_tax_per');

		$data['settings'] = $this->$model->get_ret_settings('customer_sales_limit');

		$data['tcs_details'] = $this->$model->get_customer_wise_tcs_percent($id_customer, $fin_year['fin_year_code']);

		// print_r($this->db->last_query());exit;

		echo json_encode($data);
	}



	function bill_payment_details()

	{

		$model = "ret_billing_model";

		$data = $this->$model->getCustomerpaymentDetails($_POST['id_customer'], $_POST['id_branch']);

		echo json_encode($data);
	}

	function get_tax_group_from_billing()
	{

		$return_data = $this->ret_billing_model->get_tax_group_from_billing();

		echo json_encode($return_data);
	}

	function order_adtrnssendotp()

	{

		$model = "ret_billing_model";

		$mobile_num     = $this->input->post('mobile');

		$send_resend     = $this->input->post('send_resend');

		$sent_otp = '';

		if ($mobile_num != '') {

			$this->db->trans_begin();

			$this->session->unset_userdata("advc_trns_otp");

			$this->session->unset_userdata("advc_trns_otp_exp");

			$OTP = mt_rand(100001, 999999);

			$this->session->set_userdata('advc_trns_otp', $OTP);

			$this->session->set_userdata('advc_trns_otp_exp', time() + 60);

			$message = "Hi Your OTP  For Order delievery is :  " . $OTP . " Will expire within 1 minute.";

			$otp_gen_time = date("Y-m-d H:i:s");

			$insData = array(

				'mobile' => $mobile_num,

				'otp_code' => $OTP,

				'otp_gen_time' => date("Y-m-d H:i:s"),

				'module' => 'Order Delievery',

				'send_resend' => $send_resend,

				'id_emp' => $this->session->userdata('uid')

			);

			$insId = $this->$model->insertData($insData, 'otp');
		}

		if ($insId) {

			$this->db->trans_commit();

			$this->order_ad_trans_send_sms($mobile_num, $message);

			$status = array('OTP' => $OTP, 'status' => true, 'msg' => 'OTP sent Successfully');
		} else {

			$this->db->trans_rollback();

			$status = array('status' => false, 'msg' => 'Unabe To Send Try Again');
		}

		echo json_encode($status);
	}


	function order_verify_otp()

	{

		$model = "ret_billing_model";

		$post_otp = $this->input->post('otp');

		$session_otp = $this->session->userdata('advc_trns_otp');

		$otp = array(explode(',', $session_otp));

		$this->db->trans_begin();

		if ($post_otp != '') {

			foreach ($otp[0] as $OTP) {

				if ($OTP == $post_otp) {

					if (time() >= $this->session->userdata('advc_trns_otp_exp')) {

						$this->session->unset_userdata('advc_trns_otp');

						$this->session->unset_userdata('advc_trns_otp_exp');

						$status = array('status' => false, 'msg' => 'OTP has been expired');
					} else {

						$this->db->trans_commit();

						$updData = array('is_verified' => 1, 'verified_time' => date("Y-m-d H:i:s"));

						$updStatus = $this->$model->updateData($updData, 'otp_code', $post_otp, 'otp');

						$status = array('status' => true, 'msg' => 'OTP Verified Successfully.', 'verified_otp' => $post_otp);
					}

					break;
				} else {

					$status = array('status' => false, 'msg' => 'Please Enter Valid OTP');
				}
			}
		} else {

			$status = array('status' => false, 'msg' => 'Please Enter Valid OTP');
		}

		echo json_encode($status);
	}

	function order_ad_trans_send_sms($mobile, $message, $dlt_te_id = '')

	{

		if ($this->config->item('sms_gateway') == '1') {

			$this->sms_model->sendSMS_MSG91($mobile, $message, '', $dlt_te_id);
		} elseif ($this->config->item('sms_gateway') == '2') {

			$this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
		}
	}

	function order_delievery_sendotp()

	{

		$model = "ret_billing_model";

		$mobile_num     = $this->input->post('mobile');

		$send_resend     = $this->input->post('send_resend');

		$sent_otp = '';

		if ($mobile_num != '') {

			$this->db->trans_begin();

			$this->session->unset_userdata("order_delievery_otp");

			$this->session->unset_userdata("order_delievery_otp_exp");

			$OTP = mt_rand(100001, 999999);

			$this->session->set_userdata('order_delievery_otp', $OTP);

			$this->session->set_userdata('order_delievery_otp_exp', time() + 60);

			$message = "Hi Your OTP  For Order delievery is :  " . $OTP . " Will expire within 1 minute.";

			$otp_gen_time = date("Y-m-d H:i:s");

			$insData = array(

				'mobile' => $mobile_num,

				'otp_code' => $OTP,

				'otp_gen_time' => date("Y-m-d H:i:s"),

				'module' => 'Order Delievery',

				'send_resend' => $send_resend,

				'id_emp' => $this->session->userdata('uid')

			);

			$insId = $this->$model->insertData($insData, 'otp');
		}

		if ($insId) {

			$this->db->trans_commit();

			$this->order_ad_trans_send_sms($mobile_num, $message);

			$status = array('OTP' => $OTP, 'status' => true, 'msg' => 'OTP sent Successfully');
			//   'OTP' => $OTP,

		} else {

			$this->db->trans_rollback();

			$status = array('status' => false, 'msg' => 'Unabe To Send Try Again');
		}

		echo json_encode($status);
	}


	function order_delievery_verify_otp()

	{

		$model = "ret_billing_model";

		$post_otp = $this->input->post('otp');

		$session_otp = $this->session->userdata('order_delievery_otp');

		$otp = array(explode(',', $session_otp));

		$this->db->trans_begin();

		if ($post_otp != '') {

			foreach ($otp[0] as $OTP) {

				if ($OTP == $post_otp) {

					if (time() >= $this->session->userdata('order_delievery_otp_exp')) {

						$this->session->unset_userdata('order_delievery_otp');

						$this->session->unset_userdata('order_delievery_otp_exp');

						$status = array('status' => false, 'msg' => 'OTP has been expired');
					} else {

						$this->db->trans_commit();

						$updData = array('is_verified' => 1, 'verified_time' => date("Y-m-d H:i:s"));

						$updStatus = $this->$model->updateData($updData, 'otp_code', $post_otp, 'otp');

						$status = array('status' => true, 'msg' => 'OTP Verified Successfully.', 'verified_otp' => $post_otp);
					}

					break;
				} else {

					$status = array('status' => false, 'msg' => 'Please Enter Valid OTP');
				}
			}
		} else {

			$status = array('status' => false, 'msg' => 'Please Enter Valid OTP');
		}

		echo json_encode($status);
	}


	function credit_coll_disc_admin_approval()
	{
		$model = "ret_billing_model";
		//$data           = $this->$model->get_ret_settings('otp_approval_nos');
		$data = $this->$model->getBrnachOtpRegMobile($_POST['id_branch']);
		$mobile_num     = array(explode(',', $data));
		$sent_otp = '';
		$comp_details = $this->admin_settings_model->get_company();
		foreach ($mobile_num[0] as $mobile) {
			if ($mobile) {
				$this->session->unset_userdata("cc_discount_otp");
				$OTP = mt_rand(100001, 999999);
				$sent_otp .= $OTP . ',';
				$this->session->set_userdata('cc_discount_otp', $sent_otp);
				$this->session->set_userdata('cc_discount_otp_exp', time() + 50);

				$service = $this->admin_settings_model->get_service_by_code('credit_col_disc_otp');

				$expiry = 5;
				$message = "Hi Your OTP For Credit Collection Discount Approval : " . $OTP . " Will expire within " . $expiry . " minute, REGARDS " . strtoupper($comp_details['company_name']) . ".";
				//print_r($message);exit;
				$otp_gen_time = date("Y-m-d H:i:s");
				$insData = array(
					'mobile'        => $mobile,
					'otp_code'      => $OTP,
					'otp_gen_time'  => date("Y-m-d H:i:s"),
					'module'        => 'Credit Collection Discount Approval',
					'id_emp'        => $this->session->userdata('uid')
				);
				$this->db->trans_begin();
				$insId = $this->$model->insertData($insData, 'otp');
				if ($insId) {
					if ($service['serv_whatsapp'] == 1) {
						$whatsapp = $this->admin_usersms_model->send_whatsApp_message($mobile, $message);
					}
				}
			}
		}
		if ($insId) {
			$this->db->trans_commit();
			$status = array('status' => true, 'msg' => 'OTP sent Successfully', 'otp' => $sent_otp);
		} else {
			$this->db->trans_rollback();
			$status = array('status' => false, 'msg' => 'Unabe To Send Try Again');
		}
		echo json_encode($status);
	}



	function verify_credit_coll_disc_otp()
	{
		$model                = "ret_billing_model";
		$post_otp             = $this->input->post('otp');
		$session_otp          = $this->session->userdata('cc_discount_otp');
		$otp                  = array(explode(',', $session_otp));
		foreach ($otp[0] as $OTP) {
			if ($OTP == $post_otp) {
				if (time() >= $this->session->userdata('cc_discount_otp_exp')) {
					$this->session->unset_userdata('cc_discount_otp');
					$this->session->unset_userdata('cc_discount_otp_exp');
					$status = array('status' => false, 'msg' => 'OTP has been expired');
				} else {
					$updData = array(
						'is_verified' => 1,
						'verified_time' => date("Y-m-d H:i:s"),
					);
					$this->db->trans_begin();
					$update_otp = $this->$model->updateData($updData, 'otp_code', $post_otp, 'otp');
					if ($update_otp) {
						$status = array('status' => true, 'msg' => 'OTP Verified Successfully..');
						$this->db->trans_commit();
					} else {
						$status = array('status' => false, 'msg' => 'Unable to Proceed Your Request..');
						$this->db->trans_rollback();
					}
				}
				break;
			} else {
				$status = array('status' => false, 'msg' => 'Please Enter Valid OTP');
			}
		}
		echo json_encode($status);
	}
	//Discount otp




	function getactivesize()
	{
		$model = "ret_billing_model";
		$data = $this->$model->getactivesize($_POST['id_product']);
		echo json_encode($data);
	}


	function mobile_approval_request()
	{
		if ($_POST != '') {

			$item_details = $_POST['item_details'];
			$bill_cus_id = $_POST['bill_cus_id'];
			$bill_cus_name = $_POST['bill_cus_name'];
			$id_branch = $_POST['id_branch'];
			$id_emp = $_POST['id_emp'];
			$bill_discount = $_POST['disc_amt'];
			$approval_type = $_POST['OTP_aprvl_type'];
			$ApprovalMessage = $_POST['ApprovalMessage'];
			$total_bill_amt = $_POST['total_bill_amt'];

			$data = [
				'apprl_bill_discount' => $bill_discount,
				'apprl_type' => $approval_type,
				'apprl_requested_by' => $id_emp,
				'apprl_disp_message' => $ApprovalMessage,
				'apprl_tot_bill_amount' => $total_bill_amt,
				'apprl_cus_id' => $bill_cus_id,
				'items' => $item_details,

			];

			$this->$model->insertData($data, 'ret_admin_approval_status');
		}
	}

	function create_pushnotification()
	{
		$content = array(
			"en" => 'Message From Logimax'
		);
		$hashes_array = array();
		$fields = array(
			'app_id' => $this->config->item('app_id'),
			'included_segments' => array('All'),
			'data' => array(
				"nav" => "1"
			),
			'headings' => array("en" => $this->config->item('notification_title')),
			'subtitle' => array("en" => $this->config->item('notification_subtitle')),
			'contents' => array("en" => 'Message From Logimax'),
			'web_buttons' => $hashes_array
		);
		$fields = json_encode($fields);


		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json; charset=utf-8',
			'Authorization: Basic ' . $this->config->item('onesingalapi')
		));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response = curl_exec($ch);
		// var_dump($response);exit;
		curl_close($ch);
	}


	function get_prev_ref_no()

	{

		$model = "ret_billing_model";

		$data = $this->$model->get_prev_ref_no($_POST['ref_no']);

		echo json_encode($data);
	}

	function close_issue_receipt()
	{



		$model = "ret_billing_model";
		$data = $this->input->post('req_data');

		foreach ($data as $val) {

			$updatedata = array(

				'is_closed'		    =>  1,

			);

			$this->db->trans_begin();

			$this->$model->updateData($updatedata, 'id_issue_receipt', $val['id_issue_receipt'], 'ret_issue_receipt');

			if ($this->db->trans_status() === TRUE) {

				$this->db->trans_commit();

				$return_data = array('message' => 'Issue  Closed successfully', 'class' => 'success', 'title' => 'Issue');
			} else {

				$this->db->trans_rollback();

				$return_data = array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Issue');
			}

			// print_r($returndata); exit;


		}

		echo json_encode($return_data);
	}

	public function validate_huid()

	{

		$model = "ret_billing_model";

		$data['status'] = $this->$model->validate_huid($_POST['huid'], $_POST['tag_id']);

		//$response_data=array('status'=>TRUE,'msg'=>'Tag Status Changed Successfull..');

		echo json_encode($data);
	}


	/* cash collection denomination */
	function cash_collection($type = "", $id = "")

	{

		$model = "ret_billing_model";

		$SETT_MOD = "admin_settings_model";

		switch ($type) {

			case 'add':

				$data['denomination']		= $this->$model->get_denomination();

				$data['main_content'] = "billing/cash_collection/cash_collection";

				// echo "<pre>";print_r($data);exit;

				$this->load->view('layout/template', $data);

				break;

			case 'list':

				$data['main_content'] = 'billing/cash_collection/list';

				$data['access'] = $this->$SETT_MOD->get_access('admin_ret_billing/cash_collection/list');


				$this->load->view('layout/template', $data);

				break;

			case 'ajax_list':

				$list = $this->$model->ajax_getCashCollectionList($_POST);

				$profile = $this->admin_settings_model->profileDB("get", $this->session->userdata('profile'));

				$access = $this->admin_settings_model->get_access('admin_ret_billing/cash_collection/list');

				$data = array(
					'list'  => $list,
					'access' => $access,
					'profile' => $profile
				);

				echo json_encode($data);

				break;


			case 'print':

				$data['denomination']			= $this->$model->getDenomination($id);

				$data['denomination_details']	= $this->$model->get_cashCollectionDetails($id);

				$data['comp_details']			= $this->$model->getCompanyDetails();

				// echo "<pre>";print_r($data);exit;

				$html = $this->load->view('billing/cash_collection/print', $data, true);

				echo $html;
				exit;

				break;

			case 'save':

				// echo "<pre>";print_r($_POST['cash']);exit;

				$addData = $_POST['cash'];
				$denomination = $_POST['cash']['denomination'];

				$col_date = ($addData['coll_date'] != '' ? date_create($addData['coll_date']) : NULL);
				$coll_format_date = ($addData['coll_date'] != '' ? date_format($col_date, "Y-m-d") : NULL);

				$id_branch = ($addData['id_branch'] ? $addData['id_branch'] : NULL);
				$id_counter = ($addData['id_counter'] ? $addData['id_counter'] : 0);
				$cash_type = ($addData['cash_type'] ? $addData['cash_type'] : NULL);
				$opening_balance = ($addData['cash_opening_balance'] ? $addData['cash_opening_balance'] : 0);
				$cash_on_hand = ($addData['total_denomination_amount'] ? $addData['total_denomination_amount'] : 0);
				$sales_amount = ($addData['cash_received'] ? $addData['cash_received'] : 0);
				$coll_date = $coll_format_date;
				$total_amount = ($sales_amount + $opening_balance);


				if (count($addData) > 0) {

					$this->db->trans_begin();

					$data = array(
						'date' 				=> $coll_date,
						'branch_id'			=> $id_branch,
						'opening_balance'	=> $opening_balance,
						'cash_on_hand'		=> $cash_on_hand,
						'counter_id'		=> $id_counter,
						'cash_type'			=> $cash_type,
						'sales_amount'		=> $sales_amount,
						'total_amount'		=> $total_amount,
						'created_by'		=> $this->session->userdata('uid'),
						'created_at'  		=> date("Y-m-d H:i:s"),
					);

					// echo "<pre>";print_r($data);exit;


					$insId = $this->$model->insertData($data, 'ret_cash_collection');

					if (count($denomination) > 0) {

						$value_details = $denomination['value'];
						$id_details = $denomination['id'];
						$cash_details = $denomination['cash_value'];

						foreach ($value_details as $key => $value) {

							if ($value != '') {
								$denomination_array = array(
									'cash_collection_id' => $insId,
									'denomination_id'	=> $id_details[$key],
									'value'				=> $value_details[$key],
									'amount'			=> ($value_details[$key] * $cash_details[$key])
								);
								$this->$model->insertData($denomination_array, 'ret_cash_collection_details');
							}
						}
					}
				} else {

					$responseData = array('status' => FALSE, 'message' => 'No Record Found..');
				}

				if ($this->db->trans_status() === TRUE) {

					$this->db->trans_commit();

					$this->session->set_flashdata('chit_alert', array('message' => 'Cash Collection Added successfully', 'class' => 'success', 'title' => 'Billing'));

					$responseData = array('status' => TRUE, 'message' => 'Cash Collection Added successfully');
				} else {

					$this->db->trans_rollback();

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Billing'));

					$responseData = array('status' => FALSE, 'message' => 'Unable to proceed your request');
				}

				echo json_encode($responseData);

				break;

			case 'ajax':

				$list = $this->$model->ajax_getCashCollection($_POST);

				$profile = $this->admin_settings_model->profileDB("get", $this->session->userdata('profile'));

				$access = $this->admin_settings_model->get_access('admin_ret_billing/cash_collection/list');

				$data = array(
					'list'  => $list,
					'access' => $access,
					'profile' => $profile
				);

				echo json_encode($data);


				break;
		}
	}
	/* cash collection denomination */

	function get_home_bill_sectionBranchwise()
	{

		$model = 'ret_billing_model';

		$data = $this->$model->get_homebill_counters($_POST);

		echo json_encode($data);
	}

	function billing_insurance($id)
	{
		$model = "ret_billing_model";

		// Initial data setup
		$data = [
			'billing'    => $this->$model->getBillingDetails($id),
		];

		// Load related details
		$billing = $data['billing'];
		$branchId = $billing['id_branch'];
		$billDate = $billing['bill_date'];
		$billType = $billing['bill_type'];

		$data['comp_details']   = $this->$model->getCompanyDetails($branchId);
		$data['insurance']     = $this->$model->getInsuranceDetails($id);

		$html = $this->load->view('billing/print/insured_bill', $data, true);
		echo $html;
	}

	 function bank_ledger_transfer($type = 'list') {
        $model = "ret_billing_model";
        $mode = isset($_POST['mode']) ? $_POST['mode'] : ''; // Start of AJAX handling
		// print_r($_POST['mode']);

        if ($mode == 'get_ledgers') {
            $ledgers = $this->$model->get_all_ledgers();
            echo json_encode(['status' => true, 'data' => $ledgers]);
        } elseif ($mode == 'get_balance') {
            $id_ledger = $_POST['id_ledger'];
            $balance = $this->$model->get_ledger_current_balance($id_ledger);
            echo json_encode(['status' => true, 'balance' => $balance]);
        } elseif ($mode == 'get_transfer_list') {
            $list = $this->$model->get_ledger_transfer_list();
            echo json_encode(['status' => true, 'list' => $list]);
        } elseif ($mode == 'transfer') {
            $from_ledger = $_POST['from_ledger'];
            $to_ledger = isset($_POST['to_ledger']) ? $_POST['to_ledger'] : 0;
            $amount = $_POST['amount'];
            $narration = $_POST['narration'];
            $transfer_type = isset($_POST['transfer_type']) ? $_POST['transfer_type'] : 1;
            $transaction_type = isset($_POST['transaction_type']) ? $_POST['transaction_type'] : '';
            
            // Construct data array in controller as per logic
            $insert_data = [
                'from_ledger_id' => !empty($from_ledger) ? $from_ledger : NULL,
                'to_ledger_id'   => (!empty($to_ledger) && $transfer_type == 1) ? $to_ledger : NULL,
                'amount'         => !empty($amount) ? $amount : 0,
                'narration'      => !empty($narration) ? $narration : '',
                'transfer_type'  => $transfer_type,
                'transaction_type' => ($transfer_type == 2 ?  $transaction_type : ''),
                'created_by'     => $this->session->userdata('uid'),
                'transfer_date'  => date('Y-m-d H:i:s')
            ];

            // Server-side validation
            $current_balance = $this->$model->get_ledger_current_balance($from_ledger);
            
            // Validation Logic
            if ($transfer_type == 1) { // Transfer
                 if ($current_balance <= 0) {
                     echo json_encode(['status' => false, 'message' => 'Insufficient balance in From Ledger.']);
                     return;
                 }
                 if ($amount > $current_balance) {
                     echo json_encode(['status' => false, 'message' => 'Transfer amount exceeds current balance.']);
                     return;
                 }
            } elseif ($transfer_type == 2 && $transaction_type == '2') { // Manual Debit
                 if ($current_balance <= 0) {
                     echo json_encode(['status' => false, 'message' => 'Insufficient balance for Debit.']);
                     return;
                 }
                 if ($amount > $current_balance) {
                     echo json_encode(['status' => false, 'message' => 'Debit amount exceeds current balance.']);
                     return;
                 }
            } 
            // Manual Credit does not need balance check

            $result = $this->$model->process_ledger_transfer($insert_data);
            
            if ($result) {
                echo json_encode(['status' => true, 'message' => 'Transfer successful!','redirect' => base_url('index.php/admin_ret_billing/bank_ledger_transfer/list')]);
            } else {
                echo json_encode(['status' => false, 'message' => 'Transfer failed. Please try again.']);
            }
			
        } else {
            // Page Loading Logic
            if ($type == 'add') {
                $data['main_content'] = 'billing/ledger_transfer_form';
                $this->load->view('layout/template', $data);
            } else {
                // List View
                $data['main_content'] = 'billing/ledger_transfer_list';
                $this->load->view('layout/template', $data);
            }
        }
    }

	function update_order_rate_type($id_customerorder, $id_branch,$adv_bill_id = ''){

		$model = "ret_billing_model";

		$order = $this->$model->get_customer_order_details($id_customerorder,$id_branch);

		$order_amount = floatval($order['order_amount']);  // total order amount

		$total_advance = $this->$model->get_total_advance_for_order($id_customerorder,$adv_bill_id); // total advance amount

		$percentage = 0;
		if ($order_amount > 0) {
			$percentage = ($total_advance / $order_amount) * 100;
		}

		// print_r( "Percentage: " . $percentage);
		// print_r( "Order Amount: " . $order_amount);
		// print_r( "Total Advance: " . $total_advance);exit;

		$rate_type = ($percentage >= 80) ? 1 : 2;  

		$this->$model->update_customer_order_rate($id_customerorder, $rate_type);

	}

	// ============================================
	// POS INTEGRATION Ã¢â‚¬â€ Multi-Provider [BIL-NR01]
	// ============================================

	/**
	 * Get POS device list (called from JS on page load)
	 * Same endpoint as before Ã¢â‚¬â€ backward compatible
	 */
	function getposdevicelists(){
        // Module check â€” return empty if POS is disabled
        if(!$this->_isPOSModuleEnabled()){
            echo json_encode(array());
            return;
        }
        $model = "ret_billing_model";
        $poslists = $this->$model->getPOSDeviceList();
        // Gap #9 Fix: Strip sensitive credentials Ã¢â‚¬â€ only send safe fields to browser
        $safeLists = array();
        foreach($poslists as $d){
            $safeLists[] = array(
                'id_device'     => $d['id_device'],
                'dispname'      => $d['dispname'],
                'devicetype'    => $d['devicetype'],
                'is_default'    => $d['is_default'],
                'provider_code' => isset($d['provider_code']) ? $d['provider_code'] : '',
                'provider_name' => isset($d['provider_name']) ? $d['provider_name'] : '',
            );
        }
        echo json_encode($safeLists);
    }

	// ------------------------------------------
	// GENERIC ROUTER Ã¢â‚¬â€ Routes to correct handler
	// ------------------------------------------

	/**
	 * Check if POS module is enabled
	 */
	private function _isPOSModuleEnabled(){
	    $oldDebug = $this->db->db_debug;
	    $this->db->db_debug = FALSE;
	    $result = $this->db->query("SELECT m_web, m_active FROM modules WHERE m_code = 'POS' LIMIT 1");
	    $this->db->db_debug = $oldDebug;
	    if(!$result) return false;
	    $mod = $result->row_array();
	    return ($mod && isset($mod['m_web']) && $mod['m_web'] == 1 && isset($mod['m_active']) && $mod['m_active'] == 1);
	}

	/**
	 * INIT PAYMENT Ã¢â‚¬â€ generic router
	 * JS calls this with payTransData (deviceId, amount, cusid, paytype, seqno)
	 * Routes to correct provider handler based on device's provider_code
	 */
    function UploadBilledTransaction(){
        $model = "ret_billing_model";
        
        // Module check Ã¢â‚¬â€ reject if POS is disabled
        if(!$this->_isPOSModuleEnabled()){
            echo json_encode(array("responsecode" => -1, "resmessage" => "POS module is disabled for this branch", "refid" => null));
            return;
        }
        
        // Gap #2 Fix: Auth check Ã¢â‚¬â€ must be logged in
        if(!$this->session->userdata('uid')){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Unauthorized: Please login", "refid" => null));
            return;
        }
        
        // Gap #3 Fix: Use CI input instead of raw $_POST
        $addData    = $this->input->post('payTransData');
        if(!$addData || !isset($addData['deviceId'])){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Invalid request data", "refid" => null));
            return;
        }
        
        // Gap #1 Fix: Server-side amount validation
        $amount = isset($addData['amount']) ? floatval($addData['amount']) : 0;
        if($amount <= 0){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Invalid amount: must be greater than 0", "refid" => null));
            return;
        }
        
        $posDetails = $this->$model->getPOSDeviceDetails($addData['deviceId']);
        
        // Gap #8 Fix: Idempotency check Ã¢â‚¬â€ if same key was already used, return existing result
        // Note: gracefully skips if idempotency_key column doesn't exist yet (migration not run)
        if(!empty($addData['idempotency_key'])){
            $oldDebug = $this->db->db_debug;
            $this->db->db_debug = FALSE;
            $idemKey = $this->db->escape_str($addData['idempotency_key']);
            $idemResult = $this->db->query("SELECT pos_req_id, pos_res_ref_id, pos_req_status FROM ret_pos_requests WHERE idempotency_key = '$idemKey' LIMIT 1");
            $this->db->db_debug = $oldDebug;
            if($idemResult && $existing = $idemResult->row_array()){
                log_message('info', 'POS Idempotency hit: key='.$idemKey.' existing_ref='.$existing['pos_res_ref_id']);
                echo json_encode(array(
                    "responsecode" => 0,
                    "resmessage" => "Payment already initiated (idempotency)",
                    "refid" => $existing['pos_res_ref_id']
                ));
                return;
            }
        }
        
        // Fix 2: Duplicate payment protection Ã¢â‚¬â€ block if INIT/PENDING transaction exists
        $existingTxn = $this->$model->getActivePOSTransaction($addData['cusid']);
        if($existingTxn) {
            echo json_encode(array(
                "responsecode" => -2,
                "resmessage" => "A payment is already in progress (Ref: ".$existingTxn['pos_res_ref_id'].")",
                "refid" => null
            ));
            return;
        }
        
        // Get provider code Ã¢â‚¬â€ fallback to 'pinelabs' for devices without provider set
        $providerCode = !empty($posDetails['provider_code']) ? $posDetails['provider_code'] : 'pinelabs';
        
        // Dynamic provider loading via Plugin Architecture
        require_once(APPPATH . 'libraries/POS_Provider_Loader.php');
        $loader = new POS_Provider_Loader();
        $provider = $loader->getProvider($providerCode);
        
        if($provider){
            $result = $provider->initPayment($addData, $posDetails, $this);
			
            echo json_encode($result);
            return;
        }
        
        // Fallback: unknown provider
        echo json_encode(array("responsecode" => -1, "resmessage" => "Unknown POS provider: $providerCode. Available: " . implode(', ', $loader->getRegisteredProviders()), "refid" => null));
        return;
    }

	/**
	 * CHECK STATUS Ã¢â‚¬â€ generic router
	 */
    function getTransactionStatus(){
        $model = "ret_billing_model";
        
        // Gap #2 Fix: Auth check
        if(!$this->session->userdata('uid')){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Unauthorized", "transdata" => null));
            return;
        }
        
        // Gap #3 Fix: Use CI input instead of raw $_POST
        $addData = $this->input->post('payTransData');
        if(!$addData || !isset($addData['deviceId'])){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Invalid request data", "transdata" => null));
            return;
        }
        $posDetails = $this->$model->getPOSDeviceDetails($addData['deviceId']);
        
        // Gap #14 Fix: Log status check for audit trail
        if(!empty($addData['refcode'])){
            log_message('info', 'POS Status Check: refId='.$addData['refcode'].' by uid='.$this->session->userdata('uid'));
            $oldDebug = $this->db->db_debug; $this->db->db_debug = FALSE;
            $this->db->query("UPDATE ret_pos_requests SET pos_last_checked_by = ?, pos_last_checked_at = NOW() WHERE pos_res_ref_id = ?", array($this->session->userdata('uid'), $addData['refcode']));
            $this->db->db_debug = $oldDebug;
        }
        
        $providerCode = !empty($posDetails['provider_code']) ? $posDetails['provider_code'] : 'pinelabs';
        
        // Dynamic provider loading
        require_once(APPPATH . 'libraries/POS_Provider_Loader.php');
        $loader = new POS_Provider_Loader();
        $provider = $loader->getProvider($providerCode);
        
        if($provider){
            $result = $provider->checkStatus($addData, $posDetails, $this);
            echo json_encode($result);
            return;
        }
        
        echo json_encode(array("responsecode" => -1, "resmessage" => "Unknown POS provider", "transdata" => null));
        return;
    }

	/**
	 * CANCEL PAYMENT Ã¢â‚¬â€ generic router
	 */
    function cancelTransactionRequest(){
        $model = "ret_billing_model";
        
        // Gap #2 Fix: Auth check
        if(!$this->session->userdata('uid')){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Unauthorized"));
            return;
        }
        
        // Gap #3 Fix: Use CI input instead of raw $_POST
        $addData = $this->input->post('payTransData');
        if(!$addData || !isset($addData['deviceId'])){
            echo json_encode(array("responsecode" => -1, "resmessage" => "Invalid request data"));
            return;
        }
        $posDetails = $this->$model->getPOSDeviceDetails($addData['deviceId']);
        
        // Fix 5: Block cancel if transaction already completed (SUCCESS)
        if(!empty($addData['refcode'])) {
            $txn = $this->$model->getPOSTransactionByRef($addData['refcode']);
            if($txn && $txn['pos_req_status'] == 1) {
                echo json_encode(array(
                    "responsecode" => -1,
                    "resmessage" => "Cannot cancel a completed transaction"
                ));
                return;
            }
        }
        
        // Gap #14 Fix: Log cancel action for audit trail
        if(!empty($addData['refcode'])){
            log_message('info', 'POS Cancel Request: refId='.$addData['refcode'].' by uid='.$this->session->userdata('uid'));
            $oldDebug = $this->db->db_debug; $this->db->db_debug = FALSE;
            $this->db->query("UPDATE ret_pos_requests SET pos_cancelled_by = ?, pos_cancelled_at = NOW() WHERE pos_res_ref_id = ?", array($this->session->userdata('uid'), $addData['refcode']));
            $this->db->db_debug = $oldDebug;
        }
        
        $providerCode = !empty($posDetails['provider_code']) ? $posDetails['provider_code'] : 'pinelabs';
        
        // Dynamic provider loading
        require_once(APPPATH . 'libraries/POS_Provider_Loader.php');
        $loader = new POS_Provider_Loader();
        $provider = $loader->getProvider($providerCode);
        
        if($provider){
            $result = $provider->cancelPayment($addData, $posDetails, $this);
            echo json_encode($result);
            return;
        }
        
        echo json_encode(array("responsecode" => -1, "resmessage" => "Unknown POS provider", "transdata" => null));
        return;
    }

	// ------------------------------------------
	// PINE LABS HANDLER Ã¢â‚¬â€ Existing code refactored
	// ------------------------------------------

	/**
	 * Pine Labs Ã¢â‚¬â€ Init Payment (UploadBilledTransaction)
	 */
    private function pinelabs_init($addData, $posDetails){
        $model  = "ret_billing_model";
        $cusmob = $this->$model->getcusLastMobile($addData['cusid']);
        $transno = $addData['cusid']."_".random_int(100000, 999999)."_".$cusmob;
        
        log_message('info', 'POS Pine Labs Init: cusid='.$addData['cusid'].' amount='.($addData['amount']*100).' transno='.$transno.' device='.$posDetails['id_device']);
        
        // DB record
        $paydevicedata = array(
            "pos_trans_no"       => $transno,  
            "pos_store_pos_code" => $posDetails['poscode'],
            "pos_req_amount"     => $addData['amount'] * 100,  
            "pos_usr_id"         => $this->session->userdata('uid'),  
            "pos_mer_id"         => $posDetails['merchantid'], 
            "pos_imie"           => $posDetails['imei'],
            "pos_req_createdby"  => $this->session->userdata('uid'), 
            "pos_req_bill_cusid" => $addData['cusid'],
            "pos_req_bill_id"    => isset($addData['billid']) ? intval($addData['billid']) : null,
            "id_provider"        => $posDetails['id_provider'],
            "pos_req_payload"    => json_encode($requestData),
        );
        
        // Pine Labs API payload
        $requestData = array(
            "TransactionNumber"  => $transno,   
            "SequenceNumber"     => $addData['seqno'],                            
            "AllowedPaymentMode" => $addData['paytype'],                              
            "MerchantStorePosCode" => $posDetails['poscode'],
            "Amount"       => $addData['amount'] * 100,                                     
            "UserID"       => $this->session->userdata('uid'),                 
            "MerchantID"   => $posDetails['merchantid'],                                
            "SecurityToken"=> $posDetails['securitytoken'],
            "IMEI"         => $posDetails['imei'],
            "AutoCancelDurationInMinutes" => 2
        );
        
        // Update payload in DB record now that $requestData is built
        $paydevicedata['pos_req_payload'] = json_encode($requestData);
        
        // Get API URL from DB (UAT or Live based on provider toggle)
        $apiUrl = $this->$model->getPOSApiUrl($posDetails, 'init');
        // Fallback to config for backward compatibility
        if(empty($apiUrl)) $apiUrl = $this->config->item('pos_api_request_url');
        
        $posrequest = $this->postcurlPOSRequests($requestData, $apiUrl);
        
        if($posrequest['ResponseCode'] == 0){
            $paydevicedata['pos_res_ref_id'] = $posrequest['PlutusTransactionReferenceID'];
            $paydevicedata['pos_res_payload'] = json_encode($posrequest);
            $insId = $this->$model->insertData($paydevicedata, 'ret_pos_requests');
            log_message('info', 'POS Pine Labs Init SUCCESS: refId='.$posrequest['PlutusTransactionReferenceID'].' dbId='.$insId);
            echo json_encode(array("responsecode" => $posrequest['ResponseCode'], "resmessage" => $posrequest['ResponseMessage'], "refid" => $posrequest['PlutusTransactionReferenceID']));
        }else{
            $paydevicedata['pos_req_status'] = 3; // FAILED
            $paydevicedata['pos_res_payload'] = json_encode($posrequest);
            $this->$model->insertData($paydevicedata, 'ret_pos_requests');
            log_message('error', 'POS Pine Labs Init FAILED: code='.$posrequest['ResponseCode'].' msg='.$posrequest['ResponseMessage']);
            echo json_encode(array("responsecode" => $posrequest['ResponseCode'], "resmessage" => $posrequest['ResponseMessage'], "refid" => isset($posrequest['PlutusTransactionReferenceID']) ? $posrequest['PlutusTransactionReferenceID'] : null));
        }
    }

	/**
	 * Pine Labs Ã¢â‚¬â€ Check Status (GetCloudBasedTxnStatus)
	 */
    private function pinelabs_status($addData, $posDetails){
        $model = "ret_billing_model";
        
        log_message('info', 'POS Pine Labs Status: refId='.$addData['refcode'].' device='.$posDetails['id_device']);
        
        $requestData = array(
            "MerchantID"    => $posDetails['merchantid'],                                
            "SecurityToken" => $posDetails['securitytoken'],
            "IMEI"          => $posDetails['imei'],
            "MerchantStorePosCode"           => $posDetails['poscode'],
            "PlutusTransactionReferenceID"   => $addData['refcode'],
        );
        
        $apiUrl = $this->$model->getPOSApiUrl($posDetails, 'status');
        if(empty($apiUrl)) $apiUrl = $this->config->item('pos_api_trans_status_url');
                    
        $posrequest = $this->postcurlPOSRequests($requestData, $apiUrl);
        
        if($posrequest['ResponseCode'] == 0){
            $this->$model->updateData(array('pos_res_trans_data'=> $posrequest['TransactionData'], 'pos_req_status'=> 1, 'pos_res_payload'=> json_encode($posrequest)), 'pos_res_ref_id', $addData['refcode'], 'ret_pos_requests');
            log_message('info', 'POS Pine Labs Status SUCCESS: refId='.$addData['refcode'].' data='.$posrequest['TransactionData']);
        } else {
            log_message('warning', 'POS Pine Labs Status: refId='.$addData['refcode'].' code='.$posrequest['ResponseCode'].' msg='.$posrequest['ResponseMessage']);
        }
        
        echo json_encode(array("responsecode" => $posrequest['ResponseCode'], "resmessage" => $posrequest['ResponseMessage'], "transdata" => $posrequest['TransactionData']));
    }

	/**
	 * Pine Labs Ã¢â‚¬â€ Cancel Transaction
	 */
    private function pinelabs_cancel($addData, $posDetails){
        $model = "ret_billing_model";
        
        log_message('info', 'POS Pine Labs Cancel: refId='.$addData['refcode'].' amount='.$addData['amount'].' device='.$posDetails['id_device']);
        
        $requestData = array(
            "MerchantID"    => $posDetails['merchantid'],                                
            "SecurityToken" => $posDetails['securitytoken'],
            "IMEI"          => $posDetails['imei'],
            "MerchantStorePosCode"           => $posDetails['poscode'],
            "PlutusTransactionReferenceID"   => $addData['refcode'],
            "Amount"        => $addData['amount'],
        );
        
        $apiUrl = $this->$model->getPOSApiUrl($posDetails, 'cancel');
        if(empty($apiUrl)) $apiUrl = $this->config->item('pos_api_request_cancel_url');
                    
        $posrequest = $this->postcurlPOSRequests($requestData, $apiUrl);
        
        if($posrequest['ResponseCode'] == 0){
            $this->$model->updateData(array('pos_req_status'=> 2, 'pos_res_payload'=> json_encode($posrequest)), 'pos_res_ref_id', $addData['refcode'], 'ret_pos_requests');
            log_message('info', 'POS Pine Labs Cancel SUCCESS: refId='.$addData['refcode']);
        } else {
            log_message('error', 'POS Pine Labs Cancel FAILED: refId='.$addData['refcode'].' code='.$posrequest['ResponseCode'].' msg='.$posrequest['ResponseMessage']);
        }
        
        echo json_encode(array("responsecode" => $posrequest['ResponseCode'], "resmessage" => $posrequest['ResponseMessage'], "transdata" => $posrequest['TransactionData']));
    }

	// ------------------------------------------
	// PHONEPE DQR HANDLER Ã¢â‚¬â€ Phase 3
	// ------------------------------------------

	/**
	 * PhonePe DQR Ã¢â‚¬â€ Init Payment (generate QR code)
	 * Returns qrdata (QR string) to display on billing screen
	 */
    private function phonepe_dqr_init($addData, $posDetails){
        $model = "ret_billing_model";
        
        // Generate unique transaction ID
        $transno = 'TX' . $addData['cusid'] . '_' . time() . '_' . random_int(1000, 9999);
        
        // ===== MOCK MODE: if no salt_key configured, return mock QR =====
        if(empty($posDetails['salt_key'])){
            $amountRs = intval($addData['amount']);
            $mockQR = 'upi://pay?pa=test.merchant@phonepe&pn=TestMerchant&am=' . $amountRs . '&tr=' . $transno . '&cu=INR&mc=1234';
            
            // Store mock transaction in DB
            $paydevicedata = array(
                'pos_trans_no'       => $transno,
                'pos_store_pos_code' => isset($posDetails['store_id']) ? $posDetails['store_id'] : 'MOCK',
                'pos_req_amount'     => $amountRs * 100,
                'pos_usr_id'         => $this->session->userdata('uid'),
                'pos_mer_id'         => isset($posDetails['merchantid']) ? $posDetails['merchantid'] : 'MOCK',
                'pos_req_createdby'  => $this->session->userdata('uid'),
                'pos_req_bill_cusid' => $addData['cusid'],
                'pos_req_bill_id'    => isset($addData['billid']) ? intval($addData['billid']) : null,
                'id_provider'        => $posDetails['id_provider'],
                'pos_req_payload'    => json_encode(array('mock' => true, 'amount' => $amountRs)),
                'pos_res_ref_id'     => $transno,
                'pos_qr_string'      => $mockQR,
                'pos_res_trans_data' => json_encode(array('mock' => true, 'created_at' => time())),
            );
            $this->$model->insertData($paydevicedata, 'ret_pos_requests');
            
            echo json_encode(array(
                'responsecode' => 0,
                'resmessage'   => 'QR generated successfully',
                'refid'        => $transno,
                'qrdata'       => $mockQR,
                'provider'     => 'phonepe_dqr'
            ));
            return;
        }
        // ===== END MOCK MODE =====
        
        // Get provider info for URLs
        $provider = $this->$model->getPOSProviderById($posDetails['id_provider']);
        $apiUrl   = $this->$model->getPOSApiUrl($posDetails, 'init');
        
        // Determine the API endpoint path for X-VERIFY
        $apiEndpoint = '/v3/qr/init';
        
        // Build PhonePe payload (before base64)
        $payloadArray = array(
            'merchantId'      => $posDetails['merchantid'],
            'transactionId'   => $transno,
            'merchantOrderId' => $transno,
            'amount'          => intval($addData['amount']) * 100, // convert to paise
            'storeId'         => $posDetails['store_id'],
            'terminalId'      => $posDetails['terminal_id'],
            'expiresIn'       => 180 // 3 minutes
        );
        
        // Base64 encode the payload
        $base64Payload = base64_encode(json_encode($payloadArray));
        
        // Build X-VERIFY: SHA256(base64Payload + apiEndpoint + saltKey) + "###" + saltIndex
        $saltKey   = $posDetails['salt_key'];
        $saltIndex = !empty($posDetails['salt_index']) ? $posDetails['salt_index'] : '1';
        $hash      = hash('sha256', $base64Payload . $apiEndpoint . $saltKey);
        $xVerify   = $hash . '###' . $saltIndex;
        
        // Build headers
        $headers = array(
            'Content-Type: application/json',
            'X-VERIFY: ' . $xVerify,
        );
        if(!empty($posDetails['provider_id'])){
            $headers[] = 'X-PROVIDER-ID: ' . $posDetails['provider_id'];
        }
        if(!empty($posDetails['callback_url'])){
            $headers[] = 'X-CALLBACK-URL: ' . $posDetails['callback_url'];
        }
        
        // HTTP body = { "request": "<base64>" }
        $httpBody = json_encode(array('request' => $base64Payload));
        
        // Call PhonePe API
        $response = $this->phonepeCurlRequest($apiUrl, 'POST', $httpBody, $headers);
        
        // Store in DB
        $paydevicedata = array(
            'pos_trans_no'       => $transno,
            'pos_store_pos_code' => $posDetails['store_id'],
            'pos_req_amount'     => intval($addData['amount']) * 100,
            'pos_usr_id'         => $this->session->userdata('uid'),
            'pos_mer_id'         => $posDetails['merchantid'],
            'pos_req_createdby'  => $this->session->userdata('uid'),
            'pos_req_bill_cusid' => $addData['cusid'],
            'pos_req_bill_id'    => isset($addData['billid']) ? intval($addData['billid']) : null,
            'id_provider'        => $posDetails['id_provider'],
            'pos_req_payload'    => json_encode($phonepePayload),
        );
        
        if($response && isset($response['success']) && $response['success'] === true){
            // DQR Init success Ã¢â‚¬â€ store QR string
            $qrString = isset($response['data']['qrString']) ? $response['data']['qrString'] : '';
            $paydevicedata['pos_res_ref_id']  = $transno; // PhonePe uses transactionId as ref
            $paydevicedata['pos_qr_string']   = $qrString;
            $this->$model->insertData($paydevicedata, 'ret_pos_requests');
            
            echo json_encode(array(
                'responsecode' => 0,
                'resmessage'   => $response['message'],
                'refid'        => $transno,
                'qrdata'       => $qrString,
                'provider'     => 'phonepe_dqr'
            ));
        } else {
            $errMsg = isset($response['message']) ? $response['message'] : 'PhonePe DQR Init failed';
            $errCode = isset($response['code']) ? $response['code'] : 'UNKNOWN_ERROR';
            $paydevicedata['pos_res_trans_data'] = json_encode($response);
            $this->$model->insertData($paydevicedata, 'ret_pos_requests');
            
            echo json_encode(array(
                'responsecode' => -1,
                'resmessage'   => $errMsg . ' [' . $errCode . ']',
                'refid'        => null,
                'qrdata'       => null,
                'provider'     => 'phonepe_dqr'
            ));
        }
    }

	/**
	 * PhonePe DQR Ã¢â‚¬â€ Check Status
	 * GET https://host/v3/transaction/{merchantId}/{transactionId}/status
	 */
    private function phonepe_dqr_status($addData, $posDetails){
        $model = "ret_billing_model";
        
        $merchantId    = $posDetails['merchantid'];
        $transactionId = $addData['refcode'];
        
        // ===== MOCK MODE: simulate PENDING Ã¢â€ â€™ COMPLETED after 15 seconds =====
        if(empty($posDetails['salt_key'])){
            // Get stored mock transaction to check elapsed time
            $this->db->where('pos_res_ref_id', $transactionId);
            $mockTxn = $this->db->get('ret_pos_requests')->row_array();
            
            $elapsed = 999; // default: complete immediately
            if($mockTxn && !empty($mockTxn['pos_res_trans_data'])){
                $mockData = json_decode($mockTxn['pos_res_trans_data'], true);
                if(isset($mockData['mock']) && isset($mockData['created_at'])){
                    $elapsed = time() - $mockData['created_at'];
                }
            }
            
            if($elapsed < 15){
                // Still pending Ã¢â‚¬â€ simulate customer hasn't scanned yet
                echo json_encode(array(
                    'responsecode' => 2,
                    'resmessage'   => 'Payment pending Ã¢â‚¬â€ waiting for customer to scan QR...',
                    'transdata'    => null,
                    'provider'     => 'phonepe_dqr',
                    'paymentState' => 'PAYMENT_PENDING'
                ));
            } else {
                // 15+ seconds elapsed Ã¢â‚¬â€ simulate COMPLETED
                $mockUtr = 'MOCK' . date('YmdHis') . random_int(1000,9999);
                $this->$model->updateData(
                    array('pos_req_status' => 1, 'pos_utr' => $mockUtr),
                    'pos_res_ref_id', $transactionId, 'ret_pos_requests'
                );
                
                $transdata = array(
                    array('Tag' => 'Payment State', 'Value' => 'COMPLETED'),
                    array('Tag' => 'Provider Ref', 'Value' => 'MOCK_' . $transactionId),
                    array('Tag' => 'UTR', 'Value' => $mockUtr),
                    array('Tag' => 'Amount', 'Value' => isset($mockTxn['pos_req_amount']) ? ($mockTxn['pos_req_amount']/100) : '0'),
                    array('Tag' => 'Payment Mode', 'Value' => 'UPI'),
                );
                
                echo json_encode(array(
                    'responsecode' => 0,
                    'resmessage'   => 'Payment successful!',
                    'transdata'    => $transdata,
                    'provider'     => 'phonepe_dqr',
                    'paymentState' => 'COMPLETED',
                    'utr'          => $mockUtr
                ));
            }
            return;
        }
        // ===== END MOCK MODE =====
        
        // Build status URL: replace {merchantId} and {transactionId} in template URL
        $statusUrlTemplate = $this->$model->getPOSApiUrl($posDetails, 'status');
        $statusUrl = str_replace(
            array('{merchantId}', '{transactionId}'),
            array($merchantId, $transactionId),
            $statusUrlTemplate
        );
        
        // X-VERIFY for status: SHA256("" + apiEndpoint + saltKey) Ã¢â‚¬â€ no payload for GET
        $apiEndpoint = '/v3/transaction/' . $merchantId . '/' . $transactionId . '/status';
        $saltKey   = $posDetails['salt_key'];
        $saltIndex = !empty($posDetails['salt_index']) ? $posDetails['salt_index'] : '1';
        $hash      = hash('sha256', $apiEndpoint . $saltKey);
        $xVerify   = $hash . '###' . $saltIndex;
        
        $headers = array(
            'Content-Type: application/json',
            'X-VERIFY: ' . $xVerify,
        );
        if(!empty($posDetails['provider_id'])){
            $headers[] = 'X-PROVIDER-ID: ' . $posDetails['provider_id'];
        }
        
        // GET request Ã¢â‚¬â€ no body
        $response = $this->phonepeCurlRequest($statusUrl, 'GET', null, $headers);
        
        if($response && isset($response['success']) && $response['success'] === true){
            // Map PhonePe response to our format
            $paymentState = isset($response['data']['paymentState']) ? $response['data']['paymentState'] : '';
            $utr = '';
            if(isset($response['data']['paymentModes']) && is_array($response['data']['paymentModes'])){
                foreach($response['data']['paymentModes'] as $pm){
                    if(isset($pm['utr'])) $utr = $pm['utr'];
                }
            }
            
            // Update DB with response
            $updateData = array(
                'pos_res_trans_data' => json_encode($response['data']),
                'pos_utr'           => $utr,
            );
            if($paymentState == 'COMPLETED'){
                $updateData['pos_req_status'] = 1; // success
            }
            $this->$model->updateData($updateData, 'pos_res_ref_id', $transactionId, 'ret_pos_requests');
            
            // Build transdata array in Pine Labs compatible format for JS
            $transdata = array(
                array('Tag' => 'Payment State', 'Value' => $paymentState),
                array('Tag' => 'Provider Ref', 'Value' => isset($response['data']['providerReferenceId']) ? $response['data']['providerReferenceId'] : ''),
                array('Tag' => 'UTR', 'Value' => $utr),
                array('Tag' => 'Amount', 'Value' => isset($response['data']['amount']) ? ($response['data']['amount']/100) : ''),
            );
            if(isset($response['data']['paymentModes'])){
                foreach($response['data']['paymentModes'] as $pm){
                    $transdata[] = array('Tag' => 'Payment Mode', 'Value' => $pm['mode']);
                }
            }
            
            echo json_encode(array(
                'responsecode' => 0,
                'resmessage'   => $response['message'],
                'transdata'    => $transdata,
                'provider'     => 'phonepe_dqr',
                'paymentState' => $paymentState,
                'utr'          => $utr
            ));
        } else {
            $code = isset($response['code']) ? $response['code'] : 'UNKNOWN';
            $msg  = isset($response['message']) ? $response['message'] : 'Status check failed';
            
            // PAYMENT_PENDING is not an error Ã¢â‚¬â€ keep polling
            $respCode = ($code == 'PAYMENT_PENDING') ? 2 : -1;
            
            echo json_encode(array(
                'responsecode' => $respCode,
                'resmessage'   => $msg . ' [' . $code . ']',
                'transdata'    => null,
                'provider'     => 'phonepe_dqr',
                'paymentState' => $code
            ));
        }
    }

	/**
	 * PhonePe DQR Ã¢â‚¬â€ Cancel Payment
	 * POST https://host/v3/charge/{merchantId}/{transactionId}/cancel
	 */
    private function phonepe_dqr_cancel($addData, $posDetails){
        $model = "ret_billing_model";
        
        $merchantId    = $posDetails['merchantid'];
        $transactionId = $addData['refcode'];
        
        $cancelUrlTemplate = $this->$model->getPOSApiUrl($posDetails, 'cancel');
        $cancelUrl = str_replace(
            array('{merchantId}', '{transactionId}'),
            array($merchantId, $transactionId),
            $cancelUrlTemplate
        );
        
        // X-VERIFY for cancel: SHA256("" + apiEndpoint + saltKey)
        $apiEndpoint = '/v3/charge/' . $merchantId . '/' . $transactionId . '/cancel';
        $saltKey   = $posDetails['salt_key'];
        $saltIndex = !empty($posDetails['salt_index']) ? $posDetails['salt_index'] : '1';
        $hash      = hash('sha256', $apiEndpoint . $saltKey);
        $xVerify   = $hash . '###' . $saltIndex;
        
        $headers = array(
            'Content-Type: application/json',
            'X-VERIFY: ' . $xVerify,
        );
        if(!empty($posDetails['provider_id'])){
            $headers[] = 'X-PROVIDER-ID: ' . $posDetails['provider_id'];
        }
        
        $response = $this->phonepeCurlRequest($cancelUrl, 'POST', '{}', $headers);
        
        if($response && isset($response['success']) && $response['success'] === true){
            $this->$model->updateData(array('pos_req_status' => 2), 'pos_res_ref_id', $transactionId, 'ret_pos_requests');
            echo json_encode(array('responsecode' => 0, 'resmessage' => 'Payment cancelled', 'transdata' => null, 'provider' => 'phonepe_dqr'));
        } else {
            $code = isset($response['code']) ? $response['code'] : '';
            $msg  = isset($response['message']) ? $response['message'] : 'Cancel failed';
            echo json_encode(array('responsecode' => -1, 'resmessage' => $msg . ' [' . $code . ']', 'transdata' => null, 'provider' => 'phonepe_dqr'));
        }
    }

	// ------------------------------------------
	// PHONEPE IEDC HANDLER Ã¢â‚¬â€ Phase 4
	// ------------------------------------------

	/**
	 * PhonePe IEDC Ã¢â‚¬â€ Init Payment (push to EDC terminal)
	 * POST /v1/edc/transaction/init
	 * Unlike DQR, the terminal displays card swipe + QR options to customer
	 */
    private function phonepe_edc_init($addData, $posDetails){
        $model = "ret_billing_model";
        
        // Generate unique transaction ID
        $transno = 'TXEDC' . $addData['cusid'] . '_' . time() . '_' . random_int(1000, 9999);
        
        $apiUrl = $this->$model->getPOSApiUrl($posDetails, 'init');
        
        // ===== MOCK/DEMO MODE: if no salt_key, simulate EDC terminal response =====
        if(empty($posDetails['salt_key'])){
            $amountRs = intval($addData['amount']);
            
            // Store mock transaction in DB
            $paydevicedata = array(
                'pos_trans_no'       => $transno,
                'pos_store_pos_code' => isset($posDetails['store_id']) ? $posDetails['store_id'] : 'DEMO',
                'pos_req_amount'     => $amountRs * 100,
                'pos_usr_id'         => $this->session->userdata('uid'),
                'pos_mer_id'         => isset($posDetails['merchantid']) ? $posDetails['merchantid'] : 'DEMO',
                'pos_req_createdby'  => $this->session->userdata('uid'),
                'pos_req_bill_cusid' => $addData['cusid'],
                'pos_req_bill_id'    => isset($addData['billid']) ? intval($addData['billid']) : null,
                'id_provider'        => $posDetails['id_provider'],
                'pos_req_payload'    => json_encode(array('demo_mode' => true, 'amount' => $amountRs)),
                'pos_res_ref_id'     => $transno,
                'pos_res_trans_data' => json_encode(array('demo_mode' => true, 'created_at' => time())),
            );
            $this->$model->insertData($paydevicedata, 'ret_pos_requests');
            
            echo json_encode(array(
                'responsecode' => 0,
                'resmessage'   => 'Payment sent to EDC terminal Ã¢â‚¬â€ awaiting customer action',
                'refid'        => $transno,
                'provider'     => 'phonepe_iedc'
            ));
            return;
        }
        // ===== END MOCK/DEMO MODE =====
        
        // API endpoint path for X-VERIFY
        $apiEndpoint = '/v1/edc/transaction/init';
        
        // Build IEDC payload
        $payloadArray = array(
            'merchantId'      => $posDetails['merchantid'],
            'storeId'         => $posDetails['store_id'],
            'orderId'         => $transno,
            'terminalId'      => $posDetails['terminal_id'],
            'transactionId'   => $transno,
            'amount'          => intval($addData['amount']) * 100, // paise
            'paymentModes'    => array('CARD', 'DQR'), // terminal shows both card swipe + QR
            'timeAllowedForHandoverToTerminalSeconds' => 60,
            'integrationMappingType' => 'ONE_TO_ONE'
        );
        
        $base64Payload = base64_encode(json_encode($payloadArray));
        
        // X-VERIFY
        $saltKey   = $posDetails['salt_key'];
        $saltIndex = !empty($posDetails['salt_index']) ? $posDetails['salt_index'] : '1';
        $hash      = hash('sha256', $base64Payload . $apiEndpoint . $saltKey);
        $xVerify   = $hash . '###' . $saltIndex;
        
        $headers = array(
            'Content-Type: application/json',
            'X-VERIFY: ' . $xVerify,
        );
        if(!empty($posDetails['provider_id'])){
            $headers[] = 'X-PROVIDER-ID: ' . $posDetails['provider_id'];
        }
        if(!empty($posDetails['callback_url'])){
            $headers[] = 'X-CALLBACK-URL: ' . $posDetails['callback_url'];
        }
        
        $httpBody = json_encode(array('request' => $base64Payload));
        $response = $this->phonepeCurlRequest($apiUrl, 'POST', $httpBody, $headers);
        
        // Store in DB
        $paydevicedata = array(
            'pos_trans_no'       => $transno,
            'pos_store_pos_code' => $posDetails['store_id'],
            'pos_req_amount'     => intval($addData['amount']) * 100,
            'pos_usr_id'         => $this->session->userdata('uid'),
            'pos_mer_id'         => $posDetails['merchantid'],
            'pos_req_createdby'  => $this->session->userdata('uid'),
            'pos_req_bill_cusid' => $addData['cusid'],
            'pos_req_bill_id'    => isset($addData['billid']) ? intval($addData['billid']) : null,
            'id_provider'        => $posDetails['id_provider'],
            'pos_req_payload'    => json_encode($payloadArray),
        );
        
        if($response && isset($response['success']) && $response['success'] === true){
            $paydevicedata['pos_res_ref_id'] = $transno;
            $this->$model->insertData($paydevicedata, 'ret_pos_requests');
            
            echo json_encode(array(
                'responsecode' => 0,
                'resmessage'   => isset($response['message']) ? $response['message'] : 'Payment pushed to terminal',
                'refid'        => $transno,
                'provider'     => 'phonepe_iedc'
            ));
        } else {
            $errMsg  = isset($response['message']) ? $response['message'] : 'IEDC Init failed';
            $errCode = isset($response['code']) ? $response['code'] : 'UNKNOWN_ERROR';
            $paydevicedata['pos_res_trans_data'] = json_encode($response);
            $this->$model->insertData($paydevicedata, 'ret_pos_requests');
            
            echo json_encode(array(
                'responsecode' => -1,
                'resmessage'   => $errMsg . ' [' . $errCode . ']',
                'refid'        => null,
                'provider'     => 'phonepe_iedc'
            ));
        }
    }

	/**
	 * PhonePe IEDC Ã¢â‚¬â€ Check Status
	 * Same as DQR status: GET /v3/transaction/{merchantId}/{transactionId}/status
	 */
    private function phonepe_edc_status($addData, $posDetails){
        $model = "ret_billing_model";
        
        $merchantId    = $posDetails['merchantid'];
        $transactionId = $addData['refcode'];
        
        // ===== MOCK/DEMO MODE =====
        if(empty($posDetails['salt_key'])){
            $this->db->where('pos_res_ref_id', $transactionId);
            $mockTxn = $this->db->get('ret_pos_requests')->row_array();
            
            $elapsed = 999;
            if($mockTxn && !empty($mockTxn['pos_res_trans_data'])){
                $mockData = json_decode($mockTxn['pos_res_trans_data'], true);
                if(isset($mockData['demo_mode']) && isset($mockData['created_at'])){
                    $elapsed = time() - $mockData['created_at'];
                }
            }
            
            if($elapsed < 10){
                echo json_encode(array(
                    'responsecode' => 2,
                    'resmessage'   => 'Waiting for customer to complete payment on terminal...',
                    'transdata'    => null,
                    'provider'     => 'phonepe_iedc',
                    'paymentState' => 'PAYMENT_PENDING'
                ));
            } else {
                $mockUtr = 'UTR' . date('YmdHis') . random_int(1000,9999);
                $mockApproval = 'AP' . random_int(100000, 999999);
                $this->$model->updateData(
                    array('pos_req_status' => 1, 'pos_utr' => $mockUtr),
                    'pos_res_ref_id', $transactionId, 'ret_pos_requests'
                );
                
                $transdata = array(
                    array('Tag' => 'Payment State', 'Value' => 'COMPLETED'),
                    array('Tag' => 'Card Number', 'Value' => 'XXXX XXXX XXXX 4242'),
                    array('Tag' => 'Card Type', 'Value' => 'VISA DEBIT'),
                    array('Tag' => 'Approval No', 'Value' => $mockApproval),
                    array('Tag' => 'UTR', 'Value' => $mockUtr),
                    array('Tag' => 'Amount', 'Value' => isset($mockTxn['pos_req_amount']) ? ($mockTxn['pos_req_amount']/100) : '0'),
                    array('Tag' => 'Payment Mode', 'Value' => 'DEBIT_CARD'),
                );
                
                echo json_encode(array(
                    'responsecode' => 0,
                    'resmessage'   => 'Payment successful!',
                    'transdata'    => $transdata,
                    'provider'     => 'phonepe_iedc',
                    'paymentState' => 'COMPLETED',
                    'utr'          => $mockUtr
                ));
            }
            return;
        }
        // ===== END MOCK/DEMO MODE =====
        
        $statusUrlTemplate = $this->$model->getPOSApiUrl($posDetails, 'status');
        $statusUrl = str_replace(
            array('{merchantId}', '{transactionId}'),
            array($merchantId, $transactionId),
            $statusUrlTemplate
        );
        
        $apiEndpoint = '/v1/edc/transaction/' . $transactionId . '/status';
        $saltKey   = $posDetails['salt_key'];
        $saltIndex = !empty($posDetails['salt_index']) ? $posDetails['salt_index'] : '1';
        $hash      = hash('sha256', $apiEndpoint . $saltKey);
        $xVerify   = $hash . '###' . $saltIndex;
        
        $headers = array(
            'Content-Type: application/json',
            'X-VERIFY: ' . $xVerify,
        );
        if(!empty($posDetails['provider_id'])){
            $headers[] = 'X-PROVIDER-ID: ' . $posDetails['provider_id'];
        }
        
        $response = $this->phonepeCurlRequest($statusUrl, 'GET', null, $headers);
        
        if($response && isset($response['success']) && $response['success'] === true){
            $paymentState = isset($response['data']['paymentState']) ? $response['data']['paymentState'] : '';
            $utr = '';
            if(isset($response['data']['paymentModes']) && is_array($response['data']['paymentModes'])){
                foreach($response['data']['paymentModes'] as $pm){
                    if(isset($pm['utr'])) $utr = $pm['utr'];
                }
            }
            
            $updateData = array(
                'pos_res_trans_data' => json_encode($response['data']),
                'pos_utr'           => $utr,
            );
            if($paymentState == 'COMPLETED'){
                $updateData['pos_req_status'] = 1;
            }
            $this->$model->updateData($updateData, 'pos_res_ref_id', $transactionId, 'ret_pos_requests');
            
            // Build transdata in Pine Labs format
            $transdata = array(
                array('Tag' => 'Payment State', 'Value' => $paymentState),
                array('Tag' => 'Provider Ref', 'Value' => isset($response['data']['providerReferenceId']) ? $response['data']['providerReferenceId'] : ''),
                array('Tag' => 'UTR', 'Value' => $utr),
                array('Tag' => 'Amount', 'Value' => isset($response['data']['amount']) ? ($response['data']['amount']/100) : ''),
            );
            if(isset($response['data']['paymentModes'])){
                foreach($response['data']['paymentModes'] as $pm){
                    $transdata[] = array('Tag' => 'Payment Mode', 'Value' => $pm['mode']);
                }
            }
            
            echo json_encode(array(
                'responsecode' => 0,
                'resmessage'   => $response['message'],
                'transdata'    => $transdata,
                'provider'     => 'phonepe_iedc',
                'paymentState' => $paymentState
            ));
        } else {
            $code = isset($response['code']) ? $response['code'] : 'UNKNOWN';
            $msg  = isset($response['message']) ? $response['message'] : 'Status check failed';
            $respCode = ($code == 'PAYMENT_PENDING') ? 2 : -1;
            
            echo json_encode(array(
                'responsecode' => $respCode,
                'resmessage'   => $msg . ' [' . $code . ']',
                'transdata'    => null,
                'provider'     => 'phonepe_iedc',
                'paymentState' => $code
            ));
        }
    }

	/**
	 * PhonePe IEDC Ã¢â‚¬â€ Cancel
	 * Same cancel endpoint as DQR
	 */
    private function phonepe_edc_cancel($addData, $posDetails){
        $model = "ret_billing_model";
        
        $merchantId    = $posDetails['merchantid'];
        $transactionId = $addData['refcode'];
        
        // ===== MOCK/DEMO MODE =====
        if(empty($posDetails['salt_key'])){
            $this->$model->updateData(array('pos_req_status' => 2), 'pos_res_ref_id', $transactionId, 'ret_pos_requests');
            echo json_encode(array('responsecode' => 0, 'resmessage' => 'Payment cancelled successfully', 'transdata' => null, 'provider' => 'phonepe_iedc'));
            return;
        }
        // ===== END MOCK/DEMO MODE =====
        
        $cancelUrlTemplate = $this->$model->getPOSApiUrl($posDetails, 'cancel');
        $cancelUrl = str_replace(
            array('{merchantId}', '{transactionId}'),
            array($merchantId, $transactionId),
            $cancelUrlTemplate
        );
        
        $apiEndpoint = '/v3/charge/' . $merchantId . '/' . $transactionId . '/cancel';
        $saltKey   = $posDetails['salt_key'];
        $saltIndex = !empty($posDetails['salt_index']) ? $posDetails['salt_index'] : '1';
        $hash      = hash('sha256', $apiEndpoint . $saltKey);
        $xVerify   = $hash . '###' . $saltIndex;
        
        $headers = array(
            'Content-Type: application/json',
            'X-VERIFY: ' . $xVerify,
        );
        if(!empty($posDetails['provider_id'])){
            $headers[] = 'X-PROVIDER-ID: ' . $posDetails['provider_id'];
        }
        
        $response = $this->phonepeCurlRequest($cancelUrl, 'POST', '{}', $headers);
        
        if($response && isset($response['success']) && $response['success'] === true){
            $this->$model->updateData(array('pos_req_status' => 2), 'pos_res_ref_id', $transactionId, 'ret_pos_requests');
            echo json_encode(array('responsecode' => 0, 'resmessage' => 'Payment cancelled', 'transdata' => null, 'provider' => 'phonepe_iedc'));
        } else {
            $code = isset($response['code']) ? $response['code'] : '';
            $msg  = isset($response['message']) ? $response['message'] : 'Cancel failed';
            echo json_encode(array('responsecode' => -1, 'resmessage' => $msg . ' [' . $code . ']', 'transdata' => null, 'provider' => 'phonepe_iedc'));
        }
    }

	// ------------------------------------------
	// CURL HELPER Ã¢â‚¬â€ Shared across providers
	// ------------------------------------------

    /**
     * Generic POST cURL request for POS APIs
     * Used by Pine Labs (plain JSON). PhonePe will use its own method with custom headers.
     */
    function postcurlPOSRequests($postData, $requrl){
        
           $curl = curl_init();
            
            curl_setopt_array($curl, array(
              CURLOPT_URL => $requrl,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => json_encode($postData),
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            
            return json_decode($response, true); 
    }

    /**
     * PhonePe cURL request Ã¢â‚¬â€ supports GET/POST with custom headers (X-VERIFY etc)
     * @param string $url     Full API URL
     * @param string $method  'GET' or 'POST'
     * @param string $body    JSON body (null for GET)
     * @param array  $headers Array of header strings
     * @return array|null     Decoded JSON response
     */
    private function phonepeCurlRequest($url, $method = 'POST', $body = null, $headers = array()){
        $curl = curl_init();
        
        $options = array(
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30, // 30 second timeout
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
        );
        
        if($method == 'POST' && $body !== null){
            $options[CURLOPT_POSTFIELDS] = $body;
        }
        
        curl_setopt_array($curl, $options);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $err      = curl_error($curl);
        curl_close($curl);
        
        if($err){
            log_message('error', 'PhonePe cURL Error: ' . $err . ' | URL: ' . $url);
            return array('success' => false, 'code' => 'CURL_ERROR', 'message' => $err);
        }
        
        $decoded = json_decode($response, true);
        if($decoded === null){
            log_message('error', 'PhonePe invalid JSON response: ' . substr($response, 0, 500) . ' | HTTP: ' . $httpCode);
            return array('success' => false, 'code' => 'INVALID_RESPONSE', 'message' => 'Invalid response from PhonePe');
        }
        
        return $decoded;
    }

    /**
     * PhonePe S2S Callback Ã¢â‚¬â€ Public endpoint
     * PhonePe POSTs here when payment is completed
     * URL: admin_ret_billing/phonepeCallback (register with PhonePe or pass as X-CALLBACK-URL)
     */
    function phonepeCallback(){
        $model = "ret_billing_model";
        
        // Read raw POST body
        $rawBody = file_get_contents('php://input');
        $data = json_decode($rawBody, true);
        
        if(!$data || !isset($data['response'])){
            http_response_code(400);
            echo json_encode(array('status' => 'error', 'message' => 'Invalid callback data'));
            return;
        }
        
        // Fix 3: Verify X-VERIFY signature to prevent fake callbacks
        $xVerifyHeader = isset($_SERVER['HTTP_X_VERIFY']) ? $_SERVER['HTTP_X_VERIFY'] : '';
        $saltKey = $this->$model->getPhonePeSaltKey();
        
        if(!empty($saltKey)) {
            if(empty($xVerifyHeader)) {
                log_message('error', 'PhonePe Callback: Missing X-VERIFY header');
                http_response_code(401);
                echo json_encode(array('status' => 'error', 'message' => 'Missing signature'));
                return;
            }
            // Reconstruct hash: SHA256(base64Response + callbackEndpoint + saltKey) + "###1"
            $callbackPath = '/index.php/admin_ret_billing/phonepeCallback';
            $expectedHash = hash('sha256', $data['response'] . $callbackPath . $saltKey) . '###1';
            
            if($xVerifyHeader !== $expectedHash) {
                log_message('error', 'PhonePe Callback: X-VERIFY mismatch. Expected='.$expectedHash.' Got='.$xVerifyHeader);
                http_response_code(401);
                echo json_encode(array('status' => 'error', 'message' => 'Invalid signature'));
                return;
            }
            log_message('info', 'PhonePe Callback: X-VERIFY signature verified successfully');
        } else {
            log_message('warning', 'PhonePe Callback: No salt key configured Ã¢â‚¬â€ skipping signature verification');
        }
        
        // Decode the base64 response
        $responsePayload = json_decode(base64_decode($data['response']), true);
        
        if(!$responsePayload || !isset($responsePayload['data']['transactionId'])){
            http_response_code(400);
            echo json_encode(array('status' => 'error', 'message' => 'Invalid response payload'));
            return;
        }
        
        $transactionId = $responsePayload['data']['transactionId'];
        $merchantId    = $responsePayload['data']['merchantId'];
        $paymentState  = isset($responsePayload['data']['paymentState']) ? $responsePayload['data']['paymentState'] : '';
        
        // Extract UTR if available
        $utr = '';
        if(isset($responsePayload['data']['paymentModes']) && is_array($responsePayload['data']['paymentModes'])){
            foreach($responsePayload['data']['paymentModes'] as $pm){
                if(isset($pm['utr'])) $utr = $pm['utr'];
            }
        }
        
        // Update the transaction in DB
        $updateData = array(
            'pos_callback_data'  => $rawBody,
            'pos_res_trans_data' => json_encode($responsePayload['data']),
            'pos_utr'            => $utr,
        );
        
        if($paymentState == 'COMPLETED'){
            $updateData['pos_req_status'] = 1;
        } elseif($paymentState == 'FAILED' || $paymentState == 'DECLINED'){
            $updateData['pos_req_status'] = 3;
        }
        
        $this->$model->updateData($updateData, 'pos_res_ref_id', $transactionId, 'ret_pos_requests');
        
        // PhonePe requires 200 OK response
        http_response_code(200);
        echo json_encode(array('status' => 'success'));
        
        log_message('info', 'PhonePe Callback: txn=' . $transactionId . ' state=' . $paymentState . ' utr=' . $utr);
    }

	// ------------------------------------------
	// POS SETTINGS PAGE Ã¢â‚¬â€ CRUD [BIL-NR01]
	// ------------------------------------------

	/**
	 * POS Settings page Ã¢â‚¬â€ loads provider + device lists
	 * URL: admin_ret_billing/posSettings
	 */
	function posSettings(){
	    $model = "ret_billing_model";
	    $data['providers'] = $this->$model->getAllPOSProviders();
	    $data['devices']   = $this->$model->getAllPOSDevicesWithProvider();
	    $this->load->view('layout/header', $data);
	    $this->load->view('billing/pos_settings', $data);
	    $this->load->view('layout/footer');
	}

	/** AJAX: Get single device for edit modal */
	function getPOSDeviceById(){
	    $model = "ret_billing_model";
	    $device = $this->$model->getPOSDeviceById($this->input->post('id_device'));
	    echo json_encode($device);
	}

	/** AJAX: Save new device */
	function savePOSDevice(){
	    $model = "ret_billing_model";
	    $data = array(
	        'dispname'      => $this->input->post('dispname'),
	        'id_provider'   => $this->input->post('id_provider'),
	        'merchantid'    => $this->input->post('merchantid'),
	        'devicetype'    => $this->input->post('devicetype'),
	        'is_default'    => $this->input->post('is_default'),
	        'securitytoken' => $this->input->post('securitytoken'),
	        'imei'          => $this->input->post('imei'),
	        'poscode'       => $this->input->post('poscode'),
	        'salt_key'      => $this->input->post('salt_key'),
	        'salt_index'    => $this->input->post('salt_index'),
	        'provider_id'   => $this->input->post('provider_id'),
	        'store_id'      => $this->input->post('store_id'),
	        'terminal_id'   => $this->input->post('terminal_id'),
	        'callback_url'  => $this->input->post('callback_url'),
	        'is_active'     => 1,
	    );
	    $id = $this->$model->savePOSDevice($data);
	    echo json_encode(array('status' => 'success', 'message' => 'Device saved successfully', 'id' => $id));
	}

	/** AJAX: Update existing device */
	function updatePOSDevice(){
	    $model = "ret_billing_model";
	    $deviceId = $this->input->post('id_device');
	    $data = array(
	        'dispname'      => $this->input->post('dispname'),
	        'id_provider'   => $this->input->post('id_provider'),
	        'merchantid'    => $this->input->post('merchantid'),
	        'devicetype'    => $this->input->post('devicetype'),
	        'is_default'    => $this->input->post('is_default'),
	        'securitytoken' => $this->input->post('securitytoken'),
	        'imei'          => $this->input->post('imei'),
	        'poscode'       => $this->input->post('poscode'),
	        'salt_key'      => $this->input->post('salt_key'),
	        'salt_index'    => $this->input->post('salt_index'),
	        'provider_id'   => $this->input->post('provider_id'),
	        'store_id'      => $this->input->post('store_id'),
	        'terminal_id'   => $this->input->post('terminal_id'),
	        'callback_url'  => $this->input->post('callback_url'),
	    );
	    $this->$model->updatePOSDevice($deviceId, $data);
	    echo json_encode(array('status' => 'success', 'message' => 'Device updated successfully'));
	}

	/** AJAX: Soft-delete device */
	function deletePOSDevice(){
	    $model = "ret_billing_model";
	    $this->$model->deletePOSDevice($this->input->post('id_device'));
	    echo json_encode(array('status' => 'success', 'message' => 'Device deactivated'));
	}

	/** AJAX: Set device as default */
	function setDefaultPOSDevice(){
	    $model = "ret_billing_model";
	    $this->$model->setDefaultPOSDevice($this->input->post('id_device'));
	    echo json_encode(array('status' => 'success', 'message' => 'Default device updated'));
	}

	/** AJAX: Toggle provider UAT/Live */
	function toggleProviderEnv(){
	    $model = "ret_billing_model";
	    $this->$model->toggleProviderEnv(
	        $this->input->post('id_provider'),
	        $this->input->post('is_env_live')
	    );
	    echo json_encode(array('status' => 'success', 'message' => 'Environment toggled'));
	}

	/** AJAX: Get provider details for URL viewer */
	function getPOSProviderById(){
	    $model = "ret_billing_model";
	    $provider = $this->$model->getPOSProviderById($this->input->post('id_provider'));
	    echo json_encode($provider);
	}

	/** AJAX: Save new provider */
	function savePOSProvider(){
	    $model = "ret_billing_model";
	    $data = array(
	        'provider_name'     => $this->input->post('provider_name'),
	        'provider_code'     => $this->input->post('provider_code'),
	        'auth_type'         => $this->input->post('auth_type'),
	        'api_url_uat_init'  => $this->input->post('api_url_uat_init'),
	        'api_url_uat_status'=> $this->input->post('api_url_uat_status'),
	        'api_url_uat_cancel'=> $this->input->post('api_url_uat_cancel'),
	        'api_url_live_init' => $this->input->post('api_url_live_init'),
	        'api_url_live_status'=> $this->input->post('api_url_live_status'),
	        'api_url_live_cancel'=> $this->input->post('api_url_live_cancel'),
	        'is_env_live'       => $this->input->post('is_env_live') ? 1 : 0,
	        'has_qr_display'    => $this->input->post('has_qr_display') ? 1 : 0,
	        'has_callback'      => $this->input->post('has_callback') ? 1 : 0,
	        'is_active'         => $this->input->post('is_active') ? 1 : 0,
	    );
	    $id = $this->$model->savePOSProvider($data);
	    echo json_encode(array('status' => 'success', 'message' => 'Provider saved', 'id' => $id));
	}

	/** AJAX: Update existing provider */
	function updatePOSProvider(){
	    $model = "ret_billing_model";
	    $providerId = $this->input->post('id_provider');
	    $data = array(
	        'provider_name'     => $this->input->post('provider_name'),
	        'provider_code'     => $this->input->post('provider_code'),
	        'auth_type'         => $this->input->post('auth_type'),
	        'api_url_uat_init'  => $this->input->post('api_url_uat_init'),
	        'api_url_uat_status'=> $this->input->post('api_url_uat_status'),
	        'api_url_uat_cancel'=> $this->input->post('api_url_uat_cancel'),
	        'api_url_live_init' => $this->input->post('api_url_live_init'),
	        'api_url_live_status'=> $this->input->post('api_url_live_status'),
	        'api_url_live_cancel'=> $this->input->post('api_url_live_cancel'),
	        'is_env_live'       => $this->input->post('is_env_live') ? 1 : 0,
	        'has_qr_display'    => $this->input->post('has_qr_display') ? 1 : 0,
	        'has_callback'      => $this->input->post('has_callback') ? 1 : 0,
	        'is_active'         => $this->input->post('is_active') ? 1 : 0,
	    );
	    $this->$model->updatePOSProvider($providerId, $data);
	    echo json_encode(array('status' => 'success', 'message' => 'Provider updated'));
	}

	// ------------------------------------------
	// POS TRANSACTION LOG Ã¢â‚¬â€ Phase 5
	// ------------------------------------------

	/**
	 * POS Transaction Log page Ã¢â‚¬â€ view all POS payment requests
	 * URL: admin_ret_billing/posTransactions
	 */
	function posTransactions(){
	    $model = "ret_billing_model";
	    $data['transactions'] = $this->$model->getAllPOSTransactions();
	    $this->load->view('layout/header', $data);
	    $this->load->view('billing/pos_transactions', $data);
	    $this->load->view('layout/footer');
	}

	/** AJAX: Get transaction detail for modal */
	function getPOSTransactionDetail(){
	    $model = "ret_billing_model";
	    $id = $this->input->post('id');
	    $txn = $this->$model->getPOSTransactionById($id);
	    echo json_encode($txn);
	}

	/**
	 * Fix #26: Session keep-alive endpoint
	 * Called by JS every 10 minutes while POS payments are active
	 * to prevent session timeout during long payment flows.
	 */
	function keepAlive(){
	    echo json_encode(array('status' => 'ok', 'time' => date('Y-m-d H:i:s')));
	}

	/**
	 * Fix #31: Settlement reconciliation Ã¢â‚¬â€ AJAX endpoint
	 * Returns daily totals grouped by provider and status
	 */
	function posSettlementSummary(){
	    if(!$this->session->userdata('uid')){
	        echo json_encode(array('status' => false, 'message' => 'Unauthorized'));
	        return;
	    }
	    try {
	        $model = "ret_billing_model";
	        $fromDate = $this->input->post('from_date') ? date('Y-m-d', strtotime($this->input->post('from_date'))) : date('Y-m-d');
	        $toDate   = $this->input->post('to_date')   ? date('Y-m-d', strtotime($this->input->post('to_date')))   : date('Y-m-d');
	        
	        $data = $this->$model->getPOSSettlementSummary($fromDate, $toDate);
	        
	        // Build summary totals
	        $totals = array('total_count' => 0, 'success_count' => 0, 'failed_count' => 0, 'pending_count' => 0, 'cancelled_count' => 0,
	                        'total_amount' => 0, 'success_amount' => 0, 'failed_amount' => 0, 'pending_amount' => 0);
	        foreach($data as $row){
	            $count  = intval($row['txn_count']);
	            $amount = floatval($row['total_paise']) / 100;
	            $totals['total_count']  += $count;
	            $totals['total_amount'] += $amount;
	            switch(intval($row['pos_req_status'])){
	                case 1: $totals['success_count']   += $count; $totals['success_amount']   += $amount; break;
	                case 2: $totals['cancelled_count']  += $count; break;
	                case 3: $totals['failed_count']     += $count; $totals['failed_amount']    += $amount; break;
	                default: $totals['pending_count']   += $count; $totals['pending_amount']   += $amount; break;
	            }
	        }
	        
	        echo json_encode(array('status' => true, 'breakdown' => $data, 'totals' => $totals, 'from_date' => $fromDate, 'to_date' => $toDate));
	    } catch(Exception $e) {
	        log_message('error', 'posSettlementSummary error: '.$e->getMessage());
	        echo json_encode(array('status' => false, 'message' => 'Server error: '.$e->getMessage()));
	    }
	}

	/**
	 * Fix #35: Audit trail Ã¢â‚¬â€ AJAX endpoint
	 * Returns all POS actions with user names for display
	 */
	function posAuditTrail(){
	    if(!$this->session->userdata('uid')){
	        echo json_encode(array('status' => false, 'message' => 'Unauthorized'));
	        return;
	    }
	    try {
	        $model = "ret_billing_model";
	        $fromDate = $this->input->post('from_date') ? date('Y-m-d', strtotime($this->input->post('from_date'))) : date('Y-m-d');
	        $toDate   = $this->input->post('to_date')   ? date('Y-m-d', strtotime($this->input->post('to_date')))   : date('Y-m-d');
	        
	        $data = $this->$model->getPOSAuditTrail($fromDate, $toDate);
	        echo json_encode(array('status' => true, 'data' => $data));
	    } catch(Exception $e) {
	        log_message('error', 'posAuditTrail error: '.$e->getMessage());
	        echo json_encode(array('status' => false, 'message' => 'Server error: '.$e->getMessage()));
	    }
	}

	// END POS INTEGRATION

	// ========== CASH ADJUSTMENT ==========

	public function cash_adjustment($type = "")
	{
		$model = "ret_billing_model";
		$set_model = "admin_settings_model";

		switch ($type) {
			case 'list':
				$data['main_content'] = "billing/cash_adjustment";
				$data['access'] = $this->$set_model->get_access('admin_ret_billing/cash_adjustment/list');
				$this->load->view('layout/template', $data);
				break;
		}
	}


	public function cash_adjustment_save()
	{
		$model = "ret_billing_model";

		$id_branch = $this->input->post('id_branch');
		$amount    = $this->input->post('amount');
		$adj_type  = $this->input->post('adj_type');
		$narration = $this->input->post('narration');

		if (empty($id_branch) || empty($amount) || empty($adj_type) || empty($narration)) {
			echo json_encode(array('status' => false, 'message' => 'All fields are required'));
			return;
		}

		if (!is_numeric($amount) || $amount <= 0) {
			echo json_encode(array('status' => false, 'message' => 'Amount must be a positive number'));
			return;
		}

		if (!in_array($adj_type, array('1', '2'))) {
			echo json_encode(array('status' => false, 'message' => 'Invalid adjustment type'));
			return;
		}

		$dCData   = $this->admin_settings_model->getBranchDayClosingData($id_branch);
		$adj_date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d") : $dCData['entry_date']);

		$data = array(
			'id_branch'  => $id_branch,
			'amount'     => $amount,
			'adj_type'   => $adj_type,
			'narration'  => trim($narration),
			'adj_date'   => $adj_date,
			'status'     => 1,
			'created_by' => $this->session->userdata('uid'),
			'created_at' => date('Y-m-d H:i:s')
		);

		$insId = $this->$model->insertData($data, 'ret_cash_adjustment');

		if ($insId) {
			echo json_encode(array('status' => true, 'message' => 'Cash adjustment saved successfully', 'id' => $insId));
		} else {
			echo json_encode(array('status' => false, 'message' => 'Failed to save cash adjustment'));
		}
	}
	public function cash_adjustment_list()
	{
		$id_branch = $this->session->userdata('id_branch');

		$branch_filter = '';
		if ($id_branch != 0 && $id_branch != '') {
			$branch_filter = " AND ca.id_branch = " . (int)$id_branch;
		} else {
			$filter_branch = $this->input->post('filter_branch');
			if (!empty($filter_branch)) {
				$branch_filter = " AND ca.id_branch = " . (int)$filter_branch;
			}
		}

		$date_filter = '';
		$from_date = $this->input->post('from_date');
		$to_date = $this->input->post('to_date');
		if (!empty($from_date)) {
			$dt = DateTime::createFromFormat('Y-m-d', $from_date) ?: DateTime::createFromFormat('d-m-Y', $from_date);
			if ($dt) $date_filter .= " AND ca.adj_date >= '" . $dt->format('Y-m-d') . "'";
		}
		if (!empty($to_date)) {
			$dt = DateTime::createFromFormat('Y-m-d', $to_date) ?: DateTime::createFromFormat('d-m-Y', $to_date);
			if ($dt) $date_filter .= " AND ca.adj_date <= '" . $dt->format('Y-m-d') . "'";
		}

		$sql = $this->db->query("
			SELECT ca.id_cash_adj, ca.id_branch, ca.amount, ca.adj_type,
				   ca.narration, DATE_FORMAT(ca.adj_date, '%d-%m-%Y') as adj_date_fmt,
				   ca.adj_date, ca.status,
				   CONCAT(e.firstname, ' ', IFNULL(e.lastname, '')) as created_by_name,
				   b.name as branch_name,
				   IF(ca.adj_date = IFNULL(dc.entry_date, CURDATE()) AND ca.status = 1, 1, 0) as can_cancel
			FROM ret_cash_adjustment ca
			LEFT JOIN employee e ON e.id_employee = ca.created_by
			LEFT JOIN branch b ON b.id_branch = ca.id_branch
			LEFT JOIN ret_day_closing dc ON dc.id_branch = ca.id_branch
			WHERE 1=1 {$branch_filter} {$date_filter}
			ORDER BY ca.id_cash_adj DESC
		");

		$list = $sql->result_array();

		echo json_encode(array('status' => true, 'list' => $list));
	}
	public function cash_adjustment_cancel($id = "")
	{
		$model = "ret_billing_model";

		if (empty($id)) {
			echo json_encode(array('status' => false, 'message' => 'Invalid entry ID'));
			return;
		}

		$entry = $this->db->query("SELECT * FROM ret_cash_adjustment WHERE id_cash_adj = ?", array($id))->row_array();

		if (empty($entry)) {
			echo json_encode(array('status' => false, 'message' => 'Entry not found'));
			return;
		}

		if ($entry['status'] == 2) {
			echo json_encode(array('status' => false, 'message' => 'Entry is already cancelled'));
			return;
		}

		$dCData = $this->admin_settings_model->getBranchDayClosingData($entry['id_branch']);
		$current_business_date = !empty($dCData['entry_date']) ? $dCData['entry_date'] : date('Y-m-d');
		if ($entry['adj_date'] != $current_business_date) {
			echo json_encode(array('status' => false, 'message' => 'Cancellation is only allowed on the current business day'));
			return;
		}

		$updateData = array(
			'status'       => 2,
			'cancelled_by' => $this->session->userdata('uid'),
			'cancelled_at' => date('Y-m-d H:i:s')
		);

		$result = $this->$model->updateData($updateData, 'id_cash_adj', $id, 'ret_cash_adjustment');

		if ($result) {
			echo json_encode(array('status' => true, 'message' => 'Cash adjustment cancelled successfully'));
		} else {
			echo json_encode(array('status' => false, 'message' => 'Failed to cancel cash adjustment'));
		}
	}

	// ========== END CASH ADJUSTMENT ==========
}

