<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . 'libraries/dompdf/autoload.inc.php');

use Dompdf\Dompdf;

class Admin_ret_estimation extends CI_Controller

{

	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model('ret_estimation_model');

		$this->load->model('admin_settings_model');

		$this->load->model("log_model");

		$this->load->model('ret_billing_model');

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

	public function index()

	{
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

	 * Estimation Functions Starts

	 */

	function get_purities()

	{

		$model = "ret_estimation_model";

		$data = $this->$model->get_Active_Purity($_POST);

		echo json_encode($data);
	}

	public function estimation($type = "", $id = "")
	{

		$model = "ret_estimation_model";

		$SETT_MOD = "admin_settings_model";

		switch ($type) {

			case 'add':
				$data['profile']                 = $this->$model->get_profile_settings($this->session->userdata('profile'));
				$data['estimation']		        = $this->$model->get_empty_record();

				$data['min_old_gold_rate']      = $this->$model->get_ret_settings('min_old_gold_rate');

				$data['min_old_silver_rate']    = $this->$model->get_ret_settings('min_old_silver_rate');

				$data['max_old_gold_rate']      = $this->$model->get_ret_settings('max_old_gold_rate');

				$data['max_old_silver_rate']    = $this->$model->get_ret_settings('max_old_silver_rate');

				$data['max_cash_allowed']       = $this->$model->get_ret_settings('max_cash_allowed');

				$data['weightschemecaltype']    = $this->$model->get_ret_settings('weightschemecaltype');

				$data['weight_scheme_closure_type'] = $this->$model->get_ret_settings('weight_scheme_closure_type');

				$data['est_emp_select_req']     = $this->$model->get_ret_settings('est_emp_select_req');

				$data['est_old_metal_remarks_req']     = $this->$model->get_ret_settings('est_old_metal_remarks_req');

				$data['bulk_wastage_discount']     = $this->$model->get_ret_settings('bulk_wastage_discount');

				$data['wastage_rate_type']     = $this->$model->get_ret_settings('wastage_rate_type');

				$data['enable_sales_return_estimations']     = $this->$model->get_ret_settings('enable_sales_return_estimations');

				$data['chit_rate_calculation_type']     = $this->$model->get_ret_settings('chit_rate_calculation_type');

				// print_r($data);exit;
				$data['stn_disc_per']     = $this->$model->get_stone_disc();

				$data['tag_split_weight_type']     = $this->$model->get_ret_settings('tag_split_weight_type');

				$data['emp_setting']	        = $this->$model->get_employee_settings($this->session->userdata('uid'));
				
				$metal_types = $this->$model->getMetalTypes();
				$data['gold_metal_id'] = '';
				$data['silver_metal_id'] = '';
				foreach ($metal_types as $mt) {
					if (strtolower($mt['metal']) == 'gold') {
						$data['gold_metal_id'] = $mt['id_metal'];
					} else if (strtolower($mt['metal']) == 'silver') {
						$data['silver_metal_id'] = $mt['id_metal'];
					}
				}

				$data['profile_setting']		= $this->admin_settings_model->profileDB('get', $data['emp_setting']['id_profile']);

				$data['est_other_item']         = array("item_details" => array(), "old_matel_details" => array(), "stone_details" => array(), "other_material_details" => array(), "voucher_details" => array(), "chit_details" => array());

				$data['uom']		            = $this->$model->getUOMDetails();

				$data['main_content']           = "estimation/form";

				$data['access'] = $this->$SETT_MOD->get_access('admin_ret_estimation/estimation/add');

				$this->load->view('layout/template', $data);

				//$this->load->view('layout/common_customer_modal');

				$this->load->view('layout/common_customerslider');

				break;

			case 'list':

				$data['main_content'] = "estimation/list";

				$data['access'] = $this->$SETT_MOD->get_access('admin_ret_estimation/estimation/list');

				$this->load->view('layout/template', $data);

				break;


			case 'profile':

				$profile = $this->$model->get_profile_settings($this->session->userdata('profile'));

				echo json_encode($profile);

				break;

			case "save":

				// echo "<pre>"; print_r($_POST);exit;

				$addData = $_POST['estimation'];

				$dCData = $this->admin_settings_model->getBranchDayClosingData($addData['id_branch']);

				$fin_year       = $this->$model->get_FinancialYear();

				$allow_submit = TRUE;

				$estTag = (isset($_POST['est_tag']) ? $_POST['est_tag'] : '');

				if (!empty($estTag)) {

					foreach ($estTag['tag_id'] as $key => $val) {

						if (empty($estTag['tax_price'][$key])) {
							$allow_submit = FALSE;
						}
					}
				}

				if (sizeof($dCData) > 0 && $allow_submit) {

					$estimation_datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date'] . ' ' . date("H:i:s"));

					$data = array(

						'estimation_datetime'	=> $estimation_datetime,

						'fin_year_code'		    => $fin_year['fin_year_code'],

						'esti_no'				=> $this->$model->generateEstiNo($dCData['entry_date'], $addData['id_branch']),

						'esti_for'				=> (isset($addData['esti_for']) ? $addData['esti_for'] : 1),

						'cus_id'				=> (!empty($addData['cus_id']) ? $addData['cus_id'] : NULL),

						'has_converted_order'   => 0,

						// 'discount'				=> (!empty($addData['discount']) ? $addData['discount'] : 0),

						'disc_per'				=> (!empty($addData['discount']) ? $addData['discount'] : 0.00),

						'bulk_was_disc_per'		=> (!empty($addData['blk_discount']) ? $addData['blk_discount'] : 0.00),

						'gift_voucher_amt'		=> (!empty($addData['gift_voucher_amt']) ? $addData['gift_voucher_amt'] : 0),

						'total_cost'			=> (!empty($addData['total_cost']) ? $addData['total_cost'] : 0),

						'est_date'	  		    => date($estimation_datetime),

						'created_time'	  		=> date("Y-m-d H:i:s"),

						'created_by'      		=> $addData['created_by'],

						'id_branch'      		=> $addData['id_branch'],

						'is_eda'                => $addData['is_eda'],

						'goldrate_22ct'         => $addData['goldrate_22ct'],

						'silverrate_1gm'        => $addData['silverrate_1gm'],

						'manual_rate'        => $addData['manual_rate'],

						'goldrate_14ct'        => $addData['goldrate_14ct'],

						'goldrate_18ct'        => $addData['goldrate_18ct'],
						
						'goldrate_9ct'        => $addData['goldrate_9ct'],
					);

					$this->db->trans_begin();

					$insId = $this->$model->insertData($data, 'ret_estimation');



					if ($insId) {

						// $estTag = (isset($_POST['est_tag']) ? $_POST['est_tag'] : '');

						//echo "<pre>"; print_r($estTag);exit;

						if (!empty($estTag)) {

							$arrayEstTags = array();

							foreach ($estTag['tag_id'] as $key => $val) {

								$arrayEstTags = array(

									'esti_id'              => $insId,

									'tag_id'               => $estTag['tag_id'][$key],

									'item_type'            => $estTag['item_type'][$key],

									'product_id'           => $estTag['pro_id'][$key],

									'design_id'            => ($estTag['design_id'][$key] != '' ? $estTag['design_id'][$key] : NULL),

									'id_sub_design'        => ($estTag['id_sub_design'][$key] != '' ? $estTag['id_sub_design'][$key] : NULL),

									'purity'               => ($estTag['purity'][$key] != '' ? $estTag['purity'][$key] : NULL),

									'size'                 => ($estTag['size'][$key] != '' ? $estTag['size'][$key] : NULL),

									'piece'                => ($estTag['piece'][$key] != '' ? $estTag['piece'][$key] : NULL),

									'less_wt'              => (!empty($estTag['lwt'][$key]) ? $estTag['lwt'][$key] : NULL),

									'net_wt'               => ($estTag['nwt'][$key] != '' ? $estTag['nwt'][$key] : NULL),

									'gross_wt'             => (isset($estTag['gwt'][$key]) ? (!empty($estTag['gwt'][$key]) ? $estTag['gwt'][$key] : NULL) : (!empty($estTag['cur_gwt'][$key]) ? $estTag['cur_gwt'][$key] : NULL)),

									'calculation_based_on' => $estTag['caltype'][$key],

									'wastage_percent'      => ($estTag['wastage'][$key] != '' ? $estTag['wastage'][$key] : NULL),

									'mc_value'             => ($estTag['mc'][$key] != '' ? $estTag['mc'][$key] : NULL),

									'mc_type'              => ($estTag['id_mc_type'][$key] != '' ? $estTag['id_mc_type'][$key] : NULL),

									'id_collecion_maping_det' => ($estTag['id_mapping_details'][$key] != '' ? $estTag['id_mapping_details'][$key] : NULL),

									'item_cost'            => $estTag['cost'][$key],
									'pure_wt'              => (!empty($estTag['pure_wt'][$key]) ? $estTag['pure_wt'][$key] : NULL),
									'pure_rate'            => (!empty($estTag['pure_rate'][$key]) ? $estTag['pure_rate'][$key] : NULL),

									'item_total_tax'       => !empty($estTag['tax_price'][$key]) ? $estTag['tax_price'][$key] : 0,

									'market_rate_cost'     => !empty($estTag['market_rate_cost'][$key]) ? $estTag['market_rate_cost'][$key] : 0,

									'market_rate_tax'      => !empty($estTag['market_rate_tax'][$key]) ? $estTag['market_rate_tax'][$key] : 0,

									'is_partial'           => ($estTag['is_partial'][$key] != '' ? $estTag['is_partial'][$key] : 0),

									'id_orderdetails'      => ($estTag['id_orderdetails'][$key] != '' ? $estTag['id_orderdetails'][$key] : NULL),

									'orderno'              => ($estTag['orderno'][$key] != '' ? $estTag['orderno'][$key] : NULL),

									'est_rate_per_grm'     => !empty($estTag['est_rate_per_grm'][$key]) ? $estTag['est_rate_per_grm'][$key] : 0,

									'esti_purchase_cost'   => ($estTag['purchase_cost'][$key] != '' ? $estTag['purchase_cost'][$key] : NULL),

									'discount'             => !empty($estTag['discount_amount'][$key]) ? $estTag['discount_amount'][$key] : 0,

									'item_emp_id'          => ($estTag['item_emp_id'][$key] != '' ? $estTag['item_emp_id'][$key] : $addData['created_by']),

									'isTagsplitted'        => ($estTag['isTagsplitted'][$key] != '' && $estTag['isTagsplitted'][$key] != 'undefined' ? $estTag['isTagsplitted'][$key] : 0),

									'max_va_per'             => !empty($estTag['max_va_per'][$key]) ? $estTag['max_va_per'][$key] : NULL,

									'wastage_slab_id'             => !empty($estTag['id_wastage_slab'][$key]) ? $estTag['id_wastage_slab'][$key] : NULL,

									'wast_slab_value'             => !empty($estTag['wast_slab_val'][$key]) ? $estTag['wast_slab_val'][$key] : NULL,

									'tag_blk_disc'             => !empty($estTag['blk_disc_apld'][$key]) ? $estTag['blk_disc_apld'][$key] : NULL,

									'max_mc'             => !empty($estTag['max_mc'][$key]) ? $estTag['max_mc'][$key] : NULL,

									'act_wast_per'             => !empty($estTag['act_va_per'][$key]) ? $estTag['act_va_per'][$key] : 0,

									'stone_amount'             => !empty($estTag['stone_amt'][$key]) ? $estTag['stone_amt'][$key] : 0.00,

									'diamond_amount'             => !empty($estTag['diamond_amt'][$key]) ? $estTag['diamond_amt'][$key] : 0.00,

									'is_split_row'        => ($estTag['is_split_row'][$key] != '' && $estTag['is_split_row'][$key] != 'undefined' ? $estTag['is_split_row'][$key] : 0),

								);

								//echo"<pre>"; print_r($arrayEstTags);exit;

								if (!empty($arrayEstTags)) {

									$tagInsert = $this->$model->insertData($arrayEstTags, 'ret_estimation_items');

									//print_r($this->db->last_query());exit;

									//Estimation stone details

									if ($estTag['stone_details'][$key]) {

										$stone_details = json_decode($estTag['stone_details'][$key], true);

										foreach ($stone_details as $stone) {

											$stone_data = array(

												'est_id'        => $insId,

												'est_item_id'   => $tagInsert,

												'pieces'        => $stone['stone_pcs'],

												'wt'            => $stone['stone_wt'],

												'stone_id'      => $stone['stone_id'],

												'price'         => $stone['stone_price'],

												'is_apply_in_lwt' => $stone['show_in_lwt'],

												'stone_cal_type' => $stone['stone_cal_type'],

												'rate_per_gram'  => $stone['stone_rate'],

												'uom_id'         => $stone['stone_uom_id'],

												'quality_id'     => (!empty($stone['quality_id']) ? $stone['quality_id'] : NULL),

												'max_stn_amt'     => $stone['max_stn_price'],

											);



											$stoneInsert = $this->$model->insertData($stone_data, 'ret_estimation_item_stones');
										}
									}

									// TAG MERGE
									// print_r($estTag['child_tag_details'][$key]);
									// print_r($_POST);exit;

									if ($estTag['child_tag_details'][$key]) {

										$child_tag_details = json_decode($estTag['child_tag_details'][$key], true);

										foreach ($child_tag_details as $childtag) {

											$childEstTags = array(
												'esti_id'              => $insId,
												'tag_id'               => $childtag['tag_id'],
												'item_type'            => 0,
												'product_id'           => $childtag['pro_id'],
												'design_id'            => ($childtag['design_id'] != '' ? $childtag['design_id'] : NULL),
												'id_sub_design'        => ($childtag['id_sub_design'] != '' ? $childtag['id_sub_design'] : NULL),
												'purity'               => ($childtag['purity'] != '' ? $childtag['purity'] : NULL),
												'size'                 => ($childtag['size'] != '' ? $childtag['size'] : NULL),
												'piece'                => ($childtag['piece'] != '' ? $childtag['piece'] : NULL),
												'less_wt'              => (!empty($childtag['lwt']) ? $childtag['lwt'] : NULL),
												'net_wt'               => ($childtag['nwt'] != '' ? $childtag['nwt'] : NULL),
												'gross_wt'             => (isset($childtag['gwt']) ? (!empty($childtag['gwt']) ? $childtag['gwt'] : NULL) : (!empty($childtag['cur_gwt']) ? $childtag['cur_gwt'] : NULL)),
												'calculation_based_on' => $childtag['caltype'],
												'wastage_percent'      => ($childtag['wastage'] != '' ? $childtag['wastage'] : NULL),
												'mc_value'             => ($childtag['mc'] != '' ? $childtag['mc'] : NULL),
												'mc_type'              => ($childtag['id_mc_type'] != '' ? $childtag['id_mc_type'] : NULL),
												'id_collecion_maping_det' => ($childtag['id_mapping_details'] != '' ? $childtag['id_mapping_details'] : NULL),
												'item_cost'            => $childtag['cost'],
												'item_total_tax'       => !empty($childtag['tax_price']) ? $childtag['tax_price'] : 0,
												'market_rate_cost'     => !empty($childtag['market_rate_cost']) ? $childtag['market_rate_cost'] : 0,
												'market_rate_tax'      => !empty($childtag['market_rate_tax']) ? $childtag['market_rate_tax'] : 0,
												'is_partial'           => ($childtag['is_partial'] != '' ? $childtag['is_partial'] : 0),
												'id_orderdetails'      => ($childtag['id_orderdetails'] != '' ? $childtag['id_orderdetails'] : NULL),
												'orderno'              => ($childtag['orderno'] != '' ? $childtag['orderno'] : NULL),
												'est_rate_per_grm'     => !empty($childtag['est_rate_per_grm']) ? $childtag['est_rate_per_grm'] : 0,
												'esti_purchase_cost'   => ($childtag['purchase_cost'] != '' ? $childtag['purchase_cost'] : NULL),
												'discount'             => !empty($childtag['discount_amount']) ? $childtag['discount_amount'] : 0,
												'item_emp_id'          => ($childtag['item_emp_id'] != '' ? $childtag['item_emp_id'] : $addData['created_by']),
												'id_section'           => ($childtag['id_section'] != '' ? $childtag['id_section'] : NULL),
												'istag_merged'         => 2,

											);





											if (!empty($childEstTags)) {

												$childinsd = $this->$model->insertData($childEstTags, 'ret_estimation_items');
												// print_r($this->db->last_query());exit;

												// print_r($childinsd);exit;
												if ($childinsd) {

													$insertData = array(
														'est_item_id' => $tagInsert,
														'ref_est_item_id' => $childinsd
													);
													$this->$model->insertData($insertData, 'ret_est_tag_merge ');

													$update_data =  array(
														'istag_merged' => 1
													);
													$this->$model->updateData($update_data, 'est_item_id', $tagInsert, 'ret_estimation_items');
												}

												if (!empty($childtag['stone_details'][$key])) {

													if ($childtag['stone_details'][$key]) {

														foreach ($childtag['stone_details'] as $stone) {
															$stone_data = array(
																'est_id'        => $insId,
																'est_item_id'   => $childinsd,
																'pieces'        => $stone['stone_pcs'],
																'wt'            => $stone['stone_wt'],
																'stone_id'      => $stone['stone_id'],
																'price'         => $stone['stone_price'],
																'is_apply_in_lwt' => $stone['show_in_lwt'],
																'stone_cal_type' => $stone['stone_cal_type'],
																'rate_per_gram'  => $stone['stone_rate'],
																'uom_id'         => $stone['stone_uom_id'],
															);


															$stoneInsert = $this->$model->insertData($stone_data, 'ret_estimation_item_stones');
														}
													}
												}
											}
										}
									}


									if ($estTag['other_metal_details'][$key]) {

										$other_metal_details = json_decode($estTag['other_metal_details'][$key], true);

										foreach ($other_metal_details as $stone) {

											$est_other_metals = array(

												'est_item_id'               => $tagInsert,

												'tag_other_itm_metal_id'    => $stone['tag_other_itm_metal_id'],

												'tag_other_itm_pur_id'      => $stone['tag_other_itm_pur_id'],

												'tag_other_itm_grs_weight'  => $stone['tag_other_itm_grs_weight'],

												'tag_other_itm_wastage'     => $stone['tag_other_itm_wastage'],

												'tag_other_itm_uom'         => $stone['tag_other_itm_uom'],

												'tag_other_itm_cal_type'    => $stone['tag_other_itm_cal_type'],

												'tag_other_itm_mc'          => $stone['tag_other_itm_mc'],

												'tag_other_itm_rate'        => $stone['tag_other_itm_rate'],

												'tag_other_itm_pcs'         => $stone['tag_other_itm_pcs'],

												'tag_other_itm_amount'      => $stone['tag_other_itm_amount'],

											);



											$this->$model->insertData($est_other_metals, 'ret_est_other_metals');
										}
									}





									$charges_details = $this->$model->get_charges($estTag['tag_id'][$key]);



									if (sizeof($charges_details) > 0 && $estTag['charge_value'][$key] != 0) {

										foreach ($charges_details as $charge) {

											$charge_data = array(

												'est_item_id'  => $tagInsert,

												'id_charge'    => $charge['charge_id'],

												'amount'       => $charge['charge_value'],

											);

											$this->$model->insertData($charge_data, 'ret_estimation_other_charges');

											//print_r($this->db->last_query());exit;

										}
									}



									//print_r($this->db->last_query());exit;

								}
							}
						}



						$order = (isset($_POST['order']) ? $_POST['order'] : '');

						//echo"<pre>"; print_r($order);exit;

						if (!empty($order)) {

							$arrayEstOrders = array();

							foreach ($order['orderno'] as $key => $val) {

								$arrayEstOrders[] = array(

									'esti_id'              => $insId,

									'orderno'               => $order['orderno'][$key],

									'item_type'            => 3,

									'product_id'           => $order['id_product'][$key],

									'purity'               => $order['id_purity'][$key],

									'size'                 => $order['size'][$key],

									'piece'                => $order['totalitems'][$key],

									'gross_wt'             => $order['weight'][$key],

									'net_wt'               => $order['weight'][$key],

									'wastage_percent'      => $order['wast_percent'][$key],

									'mc_value'             => $order['mc'][$key],

									'item_cost'            => $order['item_cost'][$key],

									'market_rate_cost'     => $order['market_rate_cost'][$key],

									'market_rate_tax'      => $order['market_rate_tax'][$key],

									'item_total_tax'       => $order['tax_price'][$key],

								);



								$this->$model->updateData(array('est_id' => $insId), 'order_no', $order['orderno'][$key], 'customerorder');
							}

							if (!empty($arrayEstOrders)) {

								$orderInsert = $this->$model->insertBatchData($arrayEstOrders, 'ret_estimation_items');
							}
						}



						$estCatalog = (isset($_POST['est_catalog']) ? $_POST['est_catalog'] : '');

						if (!empty($estCatalog)) {

							$arrayEstCatalog = array();

							foreach ($estCatalog['pro_id'] as $key => $val) {

								$arrayEstCatalog = array(

									'esti_id'              => $insId,

									'design_id'            => ($estCatalog['des_id'][$key] != '' ? $estCatalog['des_id'][$key] : NULL),

									'item_type'            => 1,

									'id_section'           => ($estCatalog['id_section'][$key] != '' ? $estCatalog['id_section'][$key] : ''),

									'product_id'           => ($estCatalog['pro_id'][$key] != '' ? $estCatalog['pro_id'][$key] : ''),

									'id_sub_design'        => ($estCatalog['id_sub_design'][$key] != '' ? $estCatalog['id_sub_design'][$key] : NULL),

									'purity'               => $estCatalog['purity'][$key],

									'size'                 => ($estCatalog['size'][$key] != '' ? $estCatalog['size'][$key] : NULL),

									'piece'                => ($estCatalog['pcs'][$key] != '' ? $estCatalog['pcs'][$key] : NULL),

									'less_wt'              => ($estCatalog['lwt'][$key] != '' ? $estCatalog['lwt'][$key] : NULL),

									'net_wt'               => ($estCatalog['nwt'][$key] != '' ? $estCatalog['nwt'][$key] : NULL),

									'gross_wt'             => ($estCatalog['gwt'][$key] != '' ? $estCatalog['gwt'][$key] : NULL),

									'mc_type'              => ($estCatalog['id_mc_type'][$key] != '' ?  $estCatalog['id_mc_type'][$key] : 1),

									'calculation_based_on' => ($estCatalog['calculation_based_on'][$key] != '' ? $estCatalog['calculation_based_on'][$key] : NULL),

									'wastage_percent'      => ($estCatalog['wastage'][$key] != '' ? $estCatalog['wastage'][$key] : NULL),

									'mc_value'             => ($estCatalog['mc'][$key] != '' ? $estCatalog['mc'][$key] : NULL),

									'item_cost'            => $estCatalog['amount'][$key],
									'pure_wt'              => (!empty($estCatalog['pure_wt'][$key]) ? $estCatalog['pure_wt'][$key] : NULL),
									'pure_rate'            => (!empty($estCatalog['pure_rate'][$key]) ? $estCatalog['pure_rate'][$key] : NULL),

									'is_non_tag'           => ($estCatalog['is_non_tag'][$key] != '' ? $estCatalog['is_non_tag'][$key] : 0),

									'item_total_tax'      => $estCatalog['tax_price'][$key],

									'market_rate_cost'    => $estCatalog['market_rate_cost'][$key],

									'market_rate_tax'     => $estCatalog['market_rate_tax'][$key],

									'est_rate_per_grm'    => !empty($estCatalog['est_rate_per_grm'][$key]) ? $estCatalog['est_rate_per_grm'][$key] : 0,

									'item_emp_id'		 =>($estCatalog['item_emp_id'][$key]!='' ? $estCatalog['item_emp_id'][$key]:NULL),

									'max_va_per'           => ($estCatalog['max_va_per'][$key] != '' ? $estCatalog['max_va_per'][$key] : NULL),

									'wastage_slab_id'         => !empty($estCatalog['id_wastage_slab'][$key]) ? $estCatalog['id_wastage_slab'][$key] : NULL,

									'wast_slab_value'         => !empty($estCatalog['wast_slab_val'][$key]) ? $estCatalog['wast_slab_val'][$key] : NULL,

									'max_mc'             => !empty($estCatalog['max_mc'][$key]) ? $estCatalog['max_mc'][$key] : NULL,

									'act_wast_per'             => !empty($estCatalog['act_va_per'][$key]) ? $estCatalog['act_va_per'][$key] : 0,

								);

								if (!empty($arrayEstCatalog)) {

									$tagInsert = $this->$model->insertData($arrayEstCatalog, 'ret_estimation_items');

									// print_r($this->db->last_query());exit;

									if ($estCatalog['stone_details'][$key]) {

										$stone_details = json_decode($estCatalog['stone_details'][$key], true);

										foreach ($stone_details as $stone) {

											$stone_data = array(

												'est_id'        => $insId,

												'est_item_id'   => $tagInsert,

												'pieces'        => $stone['stone_pcs'],

												'wt'            => $stone['stone_wt'],

												'stone_id'      => $stone['stone_id'],

												'price'         => $stone['stone_price'],

												'rate_per_gram'  => $stone['stone_rate'],

												'is_apply_in_lwt' => $stone['show_in_lwt'],

												'stone_cal_type'  => $stone['stone_cal_type'],

											);



											$stoneInsert = $this->$model->insertData($stone_data, 'ret_estimation_item_stones');
										}
									}


									if ($estCatalog['charges_details'][$key]) {

										$charges_details = json_decode($estCatalog['charges_details'][$key], true);

										foreach ($charges_details as $charge) {

											$charge_data = array(

												'est_item_id'  => $tagInsert,

												'id_charge'    => $charge['id_charge'],

												'amount'       => $charge['value_charge'],

											);

											$stoneInsert = $this->$model->insertData($charge_data, 'ret_estimation_other_charges');

											// print_r($this->db->last_query());exit;

										}
									}

									if ($estCatalog['material_details'][$key]) {

										$material_details = json_decode($estCatalog['material_details'][$key], true);

										foreach ($material_details as $material) {

											$material_data = array(

												'est_id'        => $insId,

												'est_item_id'   => $tagInsert,

												'wt'            => $material['material_wt'],

												'material_id'   => $material['material_id'],

												'price'         => $material['material_price']

											);

											$materialInsert = $this->$model->insertData($material_data, 'ret_estimation_item_other_materials');
										}
									}
								}
							}
						}

						$estCustom = (isset($_POST['est_custom']) ?  $_POST['est_custom'] : '');

						if (!empty($estCustom)) {

							$arrayEstCustom = array();

							foreach ($estCustom['pro_id'] as $key => $val) {

								if ($estCustom['charges_details'][$key]) {

									$charges_details = json_decode($estCustom['charges_details'][$key], true);

									foreach ($charges_details as $charge) {

										$id_charge = $charge['id_charge'];
									}
								}

								$arrayEstCustom = array(

									'esti_id'               => $insId,

									'item_type'             => 2,

									'id_section'            =>  $this->isEmptySetDefault($estCustom['id_section'][$key], NULL),

									'design_id'             => ($estCustom['des_id'][$key] != '' ? $estCustom['des_id'][$key] : NULL),

									'id_sub_design'         => ($estCustom['id_sub_design'][$key] != '' ? $estCustom['id_sub_design'][$key] : NULL),

									'product_id'            => $estCustom['pro_id'][$key],

									'tag_id'                => ($estCustom['tag_id'][$key] != '' ? $estCustom['tag_id'][$key] : NULL),

									'purity'                => ($estCustom['purity'][$key] != '' ? $estCustom['purity'][$key] : NULL),

									'size'                  => ($estCustom['size'][$key] != '' ? $estCustom['size'][$key] : NULL),

									'piece'                 => ($estCustom['pcs'][$key] != '' ? $estCustom['pcs'][$key] : NULL),

									'less_wt'               => ($estCustom['lwt'][$key] != '' ? $estCustom['lwt'][$key] : NULL),

									'net_wt'                => ($estCustom['nwt'][$key] != '' ? $estCustom['nwt'][$key] : NULL),

									'mc_type'               => ($estCustom['id_mc_type'][$key] != '' ?  $estCustom['id_mc_type'][$key] : 1),

									'gross_wt'              => ($estCustom['gwt'][$key] != '' ? $estCustom['gwt'][$key] : NULL),

									'calculation_based_on'  => ($estCustom['calculation_based_on'][$key] != '' ? $estCustom['calculation_based_on'][$key] : NULL),

									'wastage_percent'       => ($estCustom['wastage'][$key] != '' ? $estCustom['wastage'][$key] : NULL),

									'mc_value'              => ($estCustom['mc'][$key] != '' ? $estCustom['mc'][$key] : NULL),

									'item_cost'             => $estCustom['amount'][$key],
									'pure_wt'               => (!empty($estCustom['pure_wt'][$key]) ? $estCustom['pure_wt'][$key] : NULL),
									'pure_rate'             => (!empty($estCustom['pure_rate'][$key]) ? $estCustom['pure_rate'][$key] : NULL),

									'item_total_tax'        => !empty($estCustom['tax_price'][$key]) ? $estCustom['tax_price'][$key] : 0,

									'market_rate_cost'      => !empty($estCustom['market_rate_cost'][$key]) ? $estCustom['market_rate_cost'][$key] : 0,

									'market_rate_tax'       => !empty($estCustom['market_rate_tax'][$key]) ? $estCustom['market_rate_cost'][$key] : 0,

									'est_rate_per_grm'     => !empty($estCustom['est_rate_per_grm'][$key]) ? $estCustom['est_rate_per_grm'][$key] : 0,

									'id_division'           => !empty($id_charge) ? $id_charge : 0,

									'item_emp_id'          => ($estCustom['item_emp_id'][$key]!='' ? $estCustom['item_emp_id'][$key]:NULL),

									'max_va_per'              => ($estCustom['max_va_per'][$key] != '' ? $estCustom['max_va_per'][$key] : NULL),

									'wastage_slab_id'             => !empty($estCustom['id_wastage_slab'][$key]) ? $estCustom['id_wastage_slab'][$key] : NULL,

									'wast_slab_value'             => !empty($estCustom['wast_slab_val'][$key]) ? $estCustom['wast_slab_val'][$key] : NULL,

									'max_mc'             => !empty($estCustom['max_mc'][$key]) ? $estCustom['max_mc'][$key] : NULL,

									'act_wast_per'             => !empty($estCustom['act_va_per'][$key]) ? $estCustom['act_va_per'][$key] : 0,

									'stone_amount'             => !empty($estCustom['stone_amt'][$key]) ? $estCustom['stone_amt'][$key] : 0.00,

									'diamond_amount'             => !empty($estCustom['diamond_amt'][$key]) ? $estCustom['diamond_amt'][$key] : 0.00,

								);

								if (!empty($arrayEstCustom)) {

									$tagInsert = $this->$model->insertData($arrayEstCustom, 'ret_estimation_items');

									//print_r($this->db->last_query());exit;

									if ($estCustom['stone_details'][$key]) {

										$stone_details = json_decode($estCustom['stone_details'][$key], true);

										foreach ($stone_details as $stone) {

											$stone_data = array(

												'est_id'        => $insId,

												'est_item_id'   => $tagInsert,

												'pieces'        => $stone['stone_pcs'],

												'wt'            => $stone['stone_wt'],

												'stone_id'      => $stone['stone_id'],

												'price'         => $stone['stone_price'],

												'rate_per_gram'  => $stone['stone_rate'],

												'stone_cal_type'  => $stone['stone_cal_type'],

												'is_apply_in_lwt' => $stone['show_in_lwt'],

												'uom_id'        => $stone['stone_uom_id'],

												'max_stn_amt'     => $stone['max_stn_price'],

												'quality_id'     =>  (!empty($stone['quality_id']) ? $stone['quality_id'] : NULL),

											);

											$stoneInsert = $this->$model->insertData($stone_data, 'ret_estimation_item_stones');
										}
									}

									if ($estCustom['material_details'][$key]) {

										$material_details = json_decode($estCustom['material_details'][$key], true);

										foreach ($material_details as $material) {

											$material_data = array(

												'est_id'        => $insId,

												'est_item_id'   => $tagInsert,

												'wt'            => $material['material_wt'],

												'material_id'   => $material['material_id'],

												'price'         => $material['material_price']

											);

											$materialInsert = $this->$model->insertData($material_data, 'ret_estimation_item_other_materials');
										}
									}



									/*if($estCustom['charges_details'][$key])

                                            	{

                                            		$charges_details=json_decode($estCustom['charges_details'][$key],true);

                                            		foreach($charges_details as $charge)

                                            		{

                                            		$charge_data=array(

                                            					'est_item_id'  =>$tagInsert,

                                            					'id_charge'    =>$charge['id_charge'],

                                            					'amount'       =>$charge['value_charge'],

                                            				);

                                            			$stoneInsert = $this->$model->insertData($charge_data,'ret_estimation_other_charges');

                                            			//print_r($this->db->last_query());exit;

                                            		}

                                            	}*/
								}
							}
						}

						$estOldmatel = (isset($_POST['est_oldmatel']) ? $_POST['est_oldmatel'] : '');

						//echo "<pre>"; print_r($estOldmatel);exit;

						if (!empty($estOldmatel)) {

							$arrayOldMatel = array();

							foreach ($estOldmatel['id_category'] as $key => $val) {

								$arrayOldMatel = array(

									'est_id'            => $insId,

									'id_category'       => $estOldmatel['id_category'][$key],

									'id_old_metal_type' => ($estOldmatel['id_old_metal_type'][$key] != '' ? $estOldmatel['id_old_metal_type'][$key] : NULL),

									'id_old_metal_category' => ($estOldmatel['id_old_metal_category'][$key] != '' ? $estOldmatel['id_old_metal_category'][$key] : NULL),

									'purpose'           => ($estOldmatel['id_purpose'][$key] != '' ? $estOldmatel['id_purpose'][$key] : 2),

									'piece'             => $estOldmatel['pcs'][$key],

									'gross_wt'          => $estOldmatel['gwt'][$key],

									'dust_wt'           => (!empty($estOldmatel['dwt'][$key]) ? $estOldmatel['dwt'][$key] : NULL),

									'stone_wt'          => (!empty($estOldmatel['swt'][$key]) ? $estOldmatel['swt'][$key] : NULL),

									'purity'            => (!empty($estOldmatel['purity'][$key]) ? $estOldmatel['purity'][$key] : NULL),

									'touch'            => (!empty($estOldmatel['touch'][$key]) ? $estOldmatel['touch'][$key] : NULL),

									'net_wt'            => $estOldmatel['nwt'][$key],

									'wastage_percent'   => (!empty($estOldmatel['wastage'][$key]) ? $estOldmatel['wastage'][$key] : NULL),

									'wastage_wt'        => (!empty($estOldmatel['wastage_wt'][$key]) ? $estOldmatel['wastage_wt'][$key] : NULL),

									'remark'            => (!empty($estOldmatel['old_metal_remarks'][$key]) ? $estOldmatel['old_metal_remarks'][$key] : NULL),

									'rate_per_gram'     => $estOldmatel['rate'][$key],
									'old_metal_prod_id' => (!empty($estOldmatel['old_metal_prod_id'][$key]) ? $estOldmatel['old_metal_prod_id'][$key] : NULL),
									'amount'            => $estOldmatel['amount'][$key]
								);

								if (!empty($arrayOldMatel)) {

									$tagInsert = $this->$model->insertData($arrayOldMatel, 'ret_estimation_old_metal_sale_details');

									//print_r($tagInsert);exit;

									if ($estOldmatel['stone_details'][$key]) {



										$stone_details = json_decode($estOldmatel['stone_details'][$key], true);



										foreach ($stone_details as $stone) {

											$stone_data = array(

												'est_id'                => $insId,

												'est_old_metal_sale_id' => $tagInsert,

												'pieces'                => $stone['stone_pcs'],

												'wt'                    => $stone['stone_wt'],

												'stone_id'              => $stone['stone_id'],

												'price'                 => $stone['stone_price'],

												'is_apply_in_lwt'       => $stone['show_in_lwt'],

												'stone_cal_type'        => $stone['old_stone_cal_type'],

												'rate_per_gram'         => $stone['old_stone_rate'],

												'uom_id'                => $stone['stone_uom_id'],

											);

											$stoneInsert = $this->$model->insertData($stone_data, 'ret_esti_old_metal_stone_details');

											//	print_r($this->db->last_query());exit;

										}
									}
								}
							}
						}





						$est_oth_inv = (isset($_POST['est_oth_inv']) ? $_POST['est_oth_inv'] : '');

						if (!empty($est_oth_inv)) {

							foreach ($est_oth_inv['id_other_item'] as $key => $val) {

								$otherInvData = array(

									'esti_id'        => $insId,

									'id_other_item'  => $est_oth_inv['id_other_item'][$key],

									'no_of_piece'    => $est_oth_inv['no_of_pcs'][$key],

								);

								$tagInsert = $this->$model->insertData($otherInvData, 'ret_estimation_other_inventory_issue');
							}
						}

						$estGiftVoucher = (isset($_POST['gift_voucher']) ? $_POST['gift_voucher'] : '');

						if (!empty($estGiftVoucher)) {

							$arrayGiftVoucher = array();

							foreach ($estGiftVoucher['voucher_no'] as $key => $val) {

								$arrayGiftVoucher[] = array('est_id' => $insId, 'voucher_no' => $estGiftVoucher['voucher_no'][$key], 'gift_voucher_details' => NULL, 'gift_voucher_amt' => $estGiftVoucher['gift_voucher_amt'][$key]);
							}

							if (!empty($arrayGiftVoucher)) {

								$tagInsert = $this->$model->insertBatchData($arrayMaterials, 'ret_est_gift_voucher_details');
							}
						}

						$estChit = (isset($_POST['chit_uti']) ? $_POST['chit_uti'] : '');

						if (!empty($estChit)) {

							$arrayChit = array();

							foreach ($estChit['scheme_account_id'] as $key => $val) {

								$arrayChit[] = array(

									'est_id'                    => $insId,

									'scheme_account_id'         => $estChit['id_scheme_account'][$key],

									'utl_amount'                => $estChit['chit_amt'][$key],

									'closing_weight'            => ($estChit['closing_weight'][$key] != '' ? $estChit['closing_weight'][$key] : 0),

									'wastage_per'               => ($estChit['wastage_per'][$key] != '' ? $estChit['wastage_per'][$key] : 0),

									'savings_in_wastage'        => ($estChit['savings_in_wastage'][$key] != '' ? $estChit['savings_in_wastage'][$key] : 0),

									'mc_value'                  => ($estChit['mc_value'][$key] != '' ? $estChit['mc_value'][$key] : 0),

									'savings_in_making_charge'  => ($estChit['savings_in_mcvalue'][$key] != '' ? $estChit['savings_in_mcvalue'][$key] : 0),
									'rate_per_gram'  			=> ($estChit['rate_per_gram'][$key] != '' ? $estChit['rate_per_gram'][$key] : 0),

								);
							}

							if (!empty($arrayChit)) {

								$tagInsert = $this->$model->insertBatchData($arrayChit, 'ret_est_chit_utilization');
							}
						}
					}

					$estSR = (isset($_POST['sales_ret_uti']) ? $_POST['sales_ret_uti'] : '');

					if (!empty($estSR)) {

						$arraySR = array();

						$estimation_datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date'] . ' ' . date("H:i:s"));

						foreach ($estSR['ret_bill_id'] as $key => $val) {

							$bill_id = isset($estSR['ret_bill_id'][$key]) ? $estSR['ret_bill_id'][$key] : '';

							$bill_det_id_str = isset($estSR['ret_bill_det_id'][$key]) ? $estSR['ret_bill_det_id'][$key] : '';

							$bill_det_ids = array_filter(explode(',', $bill_det_id_str));

							// print_r($bill_det_ids);exit;

							foreach ($bill_det_ids as $bill_det_id) {

								$arraySR[] = array(

									'est_id'      => $insId,
									'bill_id'     => $bill_id,
									'bill_det_id' => $bill_det_id,
									'esti_date' => $estimation_datetime,

								);
							}
						}

						if (!empty($arraySR)) {

							$tagInsert = $this->$model->insertBatchData($arraySR, 'ret_est_sales_return_utilization');
						}
					}


					if ($this->db->trans_status() === TRUE) {

						$return_data['status'] = true;

						$this->db->trans_commit();



						$log_data = array(

							'id_log'        => $this->session->userdata('id_log'),

							'event_date'    => date("Y-m-d H:i:s"),

							'module'        => 'Estimation',

							'operation'     => 'Add',

							'record'        =>  $insId,

							'remark'        => 'Record added successfully'

						);

						$this->log_model->log_detail('insert', '', $log_data);



						$this->session->set_flashdata('chit_alert', array('message' => 'Estimation added successfully', 'class' => 'success', 'title' => 'Add Estimation'));
					} else {

						$this->db->trans_rollback();

						$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add Estimation'));
					}

					$return_data['type'] = 1;

					$return_data['id'] = $insId;

					echo json_encode($return_data);
				} else {

					if (!$allow_submit) {
						$this->session->set_flashdata('chit_alert', array('message' => 'GST information are wrong to add estimation', 'class' => 'danger', 'title' => 'Add Estimation'));
					} else {
						$this->session->set_flashdata('chit_alert', array('message' => 'Kindly update Day closing data to add estimation', 'class' => 'danger', 'title' => 'Add Estimation'));
					}
					$return_data['status'] = false;

					$return_data['type'] = 1;

					echo json_encode($return_data);
				}

				break;

			case "edit":

				$data['profile']                 = $this->$model->get_profile_settings($this->session->userdata('profile'));

				$data['min_old_gold_rate']      = $this->$model->get_ret_settings('min_old_gold_rate');

				$data['min_old_silver_rate']    = $this->$model->get_ret_settings('min_old_silver_rate');

				$data['max_old_gold_rate']      = $this->$model->get_ret_settings('max_old_gold_rate');

				$data['max_old_silver_rate']    = $this->$model->get_ret_settings('max_old_silver_rate');

				$data['max_cash_allowed']       = $this->$model->get_ret_settings('max_cash_allowed');

				$data['weightschemecaltype']    = $this->$model->get_ret_settings('weightschemecaltype');

				$data['weight_scheme_closure_type'] = $this->$model->get_ret_settings('weight_scheme_closure_type');

				$data['est_emp_select_req']     = $this->$model->get_ret_settings('est_emp_select_req');

				$data['est_old_metal_remarks_req']     = $this->$model->get_ret_settings('est_old_metal_remarks_req');

				$data['bulk_wastage_discount']     = $this->$model->get_ret_settings('bulk_wastage_discount');

				$data['wastage_rate_type']     = $this->$model->get_ret_settings('wastage_rate_type');

				$data['chit_rate_calculation_type']     = $this->$model->get_ret_settings('chit_rate_calculation_type');

				$data['emp_setting']	        = $this->$model->get_employee_settings($this->session->userdata('uid'));

				$data['profile_setting']		= $this->admin_settings_model->profileDB('get', $data['emp_setting']['id_profile']);

				$data['stn_disc_per']     = $this->$model->get_stone_disc();

				$data['est_other_item']         = array("item_details" => array(), "old_matel_details" => array(), "stone_details" => array(), "other_material_details" => array(), "voucher_details" => array(), "chit_details" => array());

				$data['uom']		            = $this->$model->getUOMDetails();

				$data['estimation'] = $this->$model->get_entry_records($id);

				$data['estimation']['employee'] = $this->$model->get_employee($data['estimation']['id_branch']);

				$data['uom'] = $this->$model->getUOMDetails();

				$data['est_other_item'] = $this->$model->getOtherEstimateItemsDetails($id);

				$data['emp_setting']	        = $this->$model->get_employee_settings($this->session->userdata('uid'));

				$metal_types = $this->$model->getMetalTypes();
				$data['gold_metal_id'] = '';
				$data['silver_metal_id'] = '';
				foreach ($metal_types as $mt) {
					if (strtolower($mt['metal']) == 'gold') {
						$data['gold_metal_id'] = $mt['id_metal'];
					} else if (strtolower($mt['metal']) == 'silver') {
						$data['silver_metal_id'] = $mt['id_metal'];
					}
				}


				//echo "<pre>"; print_r($data);exit;

				$data['main_content'] = "estimation/form";



				$this->load->view('layout/template', $data);

				$this->load->view('layout/common_customerslider');

				break;



			case "est_edit":

				$data['estimation'] = $this->$model->get_entry_records($id);

				$data['estimation']['employee'] = $this->$model->get_employee($data['estimation']['id_branch']);

				$data['uom'] = $this->$model->getUOMDetails();

				$data['est_other_item'] = $this->$model->getOtherEstimateItemsDetails($id);

				$data['tag_details'] = $this->$model->get_est_tag_details($id, $data['estimation']['isTagsplitted']);

				

				$data['non_tag_details'] = $this->$model->est_non_tag_items($id);

				$data['est_home_bill'] = $this->$model->est_home_bill($id);

				$data['chit_details'] = $this->$model->get_chit_details($id);

				$data['old_metal'] = $this->$model->old_Metal($id);

				echo json_encode($data);

				break;



			case 'delete':

				$this->db->trans_begin();

				$this->$model->deleteData('est_id', $id, 'ret_est_gift_voucher_details');

				$this->$model->deleteData('est_id', $id, 'ret_est_chit_utilization');

				$this->$model->deleteData('esti_id', $id, 'ret_estimation_items');

				$this->$model->deleteData('est_id', $id, 'ret_estimation_item_stones');

				$this->$model->deleteData('est_id', $id, 'ret_estimation_item_other_materials');

				$this->$model->deleteData('est_id', $id, 'ret_estimation_old_metal_sale_details');

				$this->$model->deleteData('estimation_id', $id, 'ret_estimation');

				if ($this->db->trans_status() === TRUE) {

					$log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'Estimation',

						'operation'     => 'Delete',

						'record'        =>  $id,

						'remark'        => 'Record Deleted successfully'

					);

					$this->log_model->log_detail('insert', '', $log_data);

					$this->db->trans_commit();

					$this->session->set_flashdata('chit_alert', array('message' => 'Estimation deleted successfully', 'class' => 'success', 'title' => 'Delete Estimation'));
				} else {

					$this->db->trans_rollback();

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Delete Estimation'));
				}

				redirect('admin_ret_estimation/estimation/list');

				break;

			case "update":

				$updateData = $_POST['estimation'];


				$estimation = $this->$model->get_entry_records($id);

				if ($estimation['estbillid'] == '') {

					//$updateData['estimation_datetime'] = date('Y-m-d',strtotime(str_replace("/","-",$updateData['estimation_datetime'])));

					$data = array(

						'esti_for'				=> (!empty($updateData['esti_for']) ? $updateData['esti_for'] : NULL),

						'cus_id'				=> (!empty($updateData['cus_id']) ? $updateData['cus_id'] : NULL),

						// 'discount'			=> (!empty($updateData['discount']) ? $updateData['discount'] : 0),

						'disc_per'				=> (!empty($updateData['discount']) ? $updateData['discount'] : 0.00),

						'bulk_was_disc_per'		=> (!empty($updateData['blk_discount']) ? $updateData['blk_discount'] : 0.00),

						'gift_voucher_amt'		=> (!empty($updateData['gift_voucher_amt']) ? $updateData['gift_voucher_amt'] : 0),

						'total_cost'		    => (!empty($updateData['total_cost']) ? $updateData['total_cost'] : 0),

						'created_by'      		=> $updateData['created_by'],

						'manual_rate'      		=> $updateData['manual_rate'],

					);

					$this->db->trans_begin();

					$update_status = $this->$model->updateData($data, 'estimation_id', $id, 'ret_estimation');



					if ($update_status) {



						$this->$model->deleteData('est_id', $id, 'ret_est_gift_voucher_details');



						$estCatalog = (isset($_POST['est_catalog']) ? $_POST['est_catalog'] : '');

						$estTag = (isset($_POST['est_tag']) ? $_POST['est_tag'] : '');

						$estCustom = (isset($_POST['est_custom']) ?  $_POST['est_custom'] : '');

						$estOldmatel = (isset($_POST['est_oldmatel']) ? $_POST['est_oldmatel'] : '');

						$estChit = (isset($_POST['chit_uti']) ? $_POST['chit_uti'] : '');





						$this->$model->deleteData('esti_id', $id, 'ret_estimation_items');

						$this->$model->deleteData('est_id', $id, 'ret_estimation_item_stones');

						$this->$model->deleteData('est_id', $id, 'ret_estimation_item_other_materials');

						$this->$model->deleteData('est_id', $id, 'ret_estimation_old_metal_sale_details');

						$this->$model->deleteData('est_id', $id, 'ret_esti_old_metal_stone_details');

						$this->$model->deleteData('est_id', $id, 'ret_est_chit_utilization');









						//echo "<pre>"; print_r($estTag);exit;

						if (!empty($estTag)) {

							$arrayEstTags = array();

							foreach ($estTag['tag_id'] as $key => $val) {

								$arrayEstTags = array(

									'esti_id'              => $id,

									'tag_id'               => $estTag['tag_id'][$key],

									'item_type'            => 0,

									'product_id'           => $estTag['pro_id'][$key],

									'design_id'            => ($estTag['design_id'][$key] != '' ? $estTag['design_id'][$key] : NULL),

									'id_sub_design'        => ($estTag['id_sub_design'][$key] != '' ? $estTag['id_sub_design'][$key] : NULL),

									'purity'               => ($estTag['purity'][$key] != '' ? $estTag['purity'][$key] : NULL),

									'size'                 => ($estTag['size'][$key] != '' ? $estTag['size'][$key] : NULL),

									'piece'                => ($estTag['piece'][$key] != '' ? $estTag['piece'][$key] : NULL),

									'less_wt'              => (!empty($estTag['lwt'][$key]) ? $estTag['lwt'][$key] : NULL),

									'net_wt'               => ($estTag['nwt'][$key] != '' ? $estTag['nwt'][$key] : NULL),

									'gross_wt'             => (isset($estTag['gwt'][$key]) ? (!empty($estTag['gwt'][$key]) ? $estTag['gwt'][$key] : NULL) : (!empty($estTag['cur_gwt'][$key]) ? $estTag['cur_gwt'][$key] : NULL)),

									'calculation_based_on' => $estTag['caltype'][$key],

									'wastage_percent'      => ($estTag['wastage'][$key] != '' ? $estTag['wastage'][$key] : NULL),

									'mc_value'             => ($estTag['mc'][$key] != '' ? $estTag['mc'][$key] : NULL),

									'mc_type'              => ($estTag['id_mc_type'][$key] != '' ? $estTag['id_mc_type'][$key] : NULL),

									'id_collecion_maping_det' => ($estTag['id_mapping_details'][$key] != '' ? $estTag['id_mapping_details'][$key] : NULL),

									'item_cost'            => $estTag['cost'][$key],
									'pure_wt'              => (!empty($estTag['pure_wt'][$key]) ? $estTag['pure_wt'][$key] : NULL),
									'pure_rate'            => (!empty($estTag['pure_rate'][$key]) ? $estTag['pure_rate'][$key] : NULL),

									'item_total_tax'       => !empty($estTag['tax_price'][$key]) ? $estTag['tax_price'][$key] : 0,

									'market_rate_cost'     => !empty($estTag['market_rate_cost'][$key]) ? $estTag['market_rate_cost'][$key] : 0,

									'market_rate_tax'      => !empty($estTag['market_rate_tax'][$key]) ? $estTag['market_rate_tax'][$key] : 0,

									'is_partial'           => ($estTag['is_partial'][$key] != '' ? $estTag['is_partial'][$key] : 0),

									'id_orderdetails'      => ($estTag['id_orderdetails'][$key] != '' ? $estTag['id_orderdetails'][$key] : NULL),

									'orderno'              => ($estTag['orderno'][$key] != '' ? $estTag['orderno'][$key] : NULL),

									'est_rate_per_grm'     => !empty($estTag['est_rate_per_grm'][$key]) ? $estTag['est_rate_per_grm'][$key] : 0,

									'esti_purchase_cost'   => ($estTag['purchase_cost'][$key] != '' ? $estTag['purchase_cost'][$key] : NULL),

									'discount'             => !empty($estTag['discount_amount'][$key]) ? $estTag['discount_amount'][$key] : 0,

									'item_emp_id'          => ($estTag['item_emp_id'][$key] != '' ? $estTag['item_emp_id'][$key] : $updateData['created_by']),

									'isTagsplitted'        => ($estTag['isTagsplitted'][$key] != '' && $estTag['isTagsplitted'][$key] != 'undefined' ? $estTag['isTagsplitted'][$key] : 0),

									'max_va_per'             => !empty($estTag['max_va_per'][$key]) ? $estTag['max_va_per'][$key] : NULL,

									'wastage_slab_id'             => !empty($estTag['id_wastage_slab'][$key]) ? $estTag['id_wastage_slab'][$key] : NULL,

									'wast_slab_value'             => !empty($estTag['wast_slab_val'][$key]) ? $estTag['wast_slab_val'][$key] : NULL,

									'tag_blk_disc'             => !empty($estTag['blk_disc_apld'][$key]) ? $estTag['blk_disc_apld'][$key] : NULL,

									'act_wast_per'             => !empty($estTag['act_va_per'][$key]) ? $estTag['act_va_per'][$key] : 0,

									'stone_amount'             => !empty($estTag['stone_amt'][$key]) ? $estTag['stone_amt'][$key] : 0.00,

									'diamond_amount'             => !empty($estTag['diamond_amt'][$key]) ? $estTag['diamond_amt'][$key] : 0.00,

									'max_mc'             => !empty($estTag['max_mc'][$key]) ? $estTag['max_mc'][$key] : NULL,

									'is_split_row'        => ($estTag['is_split_row'][$key] != '' && $estTag['is_split_row'][$key] != 'undefined' ? $estTag['is_split_row'][$key] : 0),


								);

								//echo"<pre>"; print_r($arrayEstTags);exit;

								if (!empty($arrayEstTags)) {

									$tagInsert = $this->$model->insertData($arrayEstTags, 'ret_estimation_items');

									//print_r($this->db->last_query());exit;

									//Estimation stone details

									if ($estTag['stone_details'][$key]) {

										$stone_details = json_decode($estTag['stone_details'][$key], true);

										foreach ($stone_details as $stone) {

											$stone_data = array(

												'est_id'        => $id,

												'est_item_id'   => $tagInsert,

												'pieces'        => $stone['stone_pcs'],

												'wt'            => $stone['stone_wt'],

												'stone_id'      => $stone['stone_id'],

												'price'         => $stone['stone_price'],

												'is_apply_in_lwt' => $stone['show_in_lwt'],

												'stone_cal_type' => $stone['stone_cal_type'],

												'rate_per_gram'  => $stone['stone_rate'],

												'uom_id'         => $stone['stone_uom_id'],

												'max_stn_amt'     => $stone['max_stn_price'],

											);



											$stoneInsert = $this->$model->insertData($stone_data, 'ret_estimation_item_stones');
										}
									}



									if ($estTag['child_tag_details'][$key]) {

										$child_tag_details = json_decode($estTag['child_tag_details'][$key], true);

										foreach ($child_tag_details as $childtag) {

											$childEstTags = array(
												'esti_id'              => $id,
												'tag_id'               => $childtag['tag_id'],
												'item_type'            => 0,
												'product_id'           => $childtag['pro_id'],
												'design_id'            => ($childtag['design_id'] != '' ? $childtag['design_id'] : NULL),
												'id_sub_design'        => ($childtag['id_sub_design'] != '' ? $childtag['id_sub_design'] : NULL),
												'purity'               => ($childtag['purity'] != '' ? $childtag['purity'] : NULL),
												'size'                 => ($childtag['size'] != '' ? $childtag['size'] : NULL),
												'piece'                => ($childtag['piece'] != '' ? $childtag['piece'] : NULL),
												'less_wt'              => (!empty($childtag['lwt']) ? $childtag['lwt'] : NULL),
												'net_wt'               => ($childtag['nwt'] != '' ? $childtag['nwt'] : NULL),
												'gross_wt'             => (isset($childtag['gwt']) ? (!empty($childtag['gwt']) ? $childtag['gwt'] : NULL) : (!empty($childtag['cur_gwt']) ? $childtag['cur_gwt'] : NULL)),
												'calculation_based_on' => $childtag['caltype'],
												'wastage_percent'      => ($childtag['wastage'] != '' ? $childtag['wastage'] : NULL),
												'mc_value'             => ($childtag['mc'] != '' ? $childtag['mc'] : NULL),
												'mc_type'              => ($childtag['id_mc_type'] != '' ? $childtag['id_mc_type'] : NULL),
												'id_collecion_maping_det' => ($childtag['id_mapping_details'] != '' ? $childtag['id_mapping_details'] : NULL),
												'item_cost'            => $childtag['cost'],
												'item_total_tax'       => !empty($childtag['tax_price']) ? $childtag['tax_price'] : 0,
												'market_rate_cost'     => !empty($childtag['market_rate_cost']) ? $childtag['market_rate_cost'] : 0,
												'market_rate_tax'      => !empty($childtag['market_rate_tax']) ? $childtag['market_rate_tax'] : 0,
												'is_partial'           => ($childtag['is_partial'] != '' ? $childtag['is_partial'] : 0),
												'id_orderdetails'      => ($childtag['id_orderdetails'] != '' ? $childtag['id_orderdetails'] : NULL),
												'orderno'              => ($childtag['orderno'] != '' ? $childtag['orderno'] : NULL),
												'est_rate_per_grm'     => !empty($childtag['est_rate_per_grm']) ? $childtag['est_rate_per_grm'] : 0,
												'esti_purchase_cost'   => ($childtag['purchase_cost'] != '' ? $childtag['purchase_cost'] : NULL),
												'discount'             => !empty($childtag['discount_amount']) ? $childtag['discount_amount'] : 0,
												'item_emp_id'          => ($childtag['item_emp_id'] != '' ? $childtag['item_emp_id'] : $updateData['created_by']),
												'id_section'          => ($childtag['id_section'] != '' ? $childtag['id_section'] : NULL),
												'istag_merged'          => 2,

											);



											if (!empty($childEstTags)) {
												$childinsd = $this->$model->insertData($childEstTags, 'ret_estimation_items');
												if ($childinsd) {

													$insertData = array(
														'est_item_id' => $tagInsert,
														'ref_est_item_id' => $childinsd
													);
													$this->$model->insertData($insertData, 'ret_est_tag_merge ');

													$update_data =  array(
														'istag_merged' => 1
													);
													$this->$model->updateData($update_data, 'est_item_id', $tagInsert, 'ret_estimation_items');
												}

												if (!empty($childtag['stone_details'][$key])) {

													if ($childtag['stone_details'][$key]) {

														foreach ($childtag['stone_details'] as $stone) {
															$stone_data = array(
																'est_id'        => $id,
																'est_item_id'   => $childinsd,
																'pieces'        => $stone['stone_pcs'],
																'wt'            => $stone['stone_wt'],
																'stone_id'      => $stone['stone_id'],
																'price'         => $stone['stone_price'],
																'is_apply_in_lwt' => $stone['show_in_lwt'],
																'stone_cal_type' => $stone['stone_cal_type'],
																'rate_per_gram'  => $stone['stone_rate'],
																'uom_id'         => $stone['stone_uom_id'],
															);


															$stoneInsert = $this->$model->insertData($stone_data, 'ret_estimation_item_stones');
														}
													}
												}
											}
										}
									}



									if ($estTag['other_metal_details'][$key]) {

										$other_metal_details = json_decode($estTag['other_metal_details'][$key], true);

										foreach ($other_metal_details as $stone) {

											$est_other_metals = array(

												'est_item_id'               => $tagInsert,

												'tag_other_itm_metal_id'    => $stone['tag_other_itm_metal_id'],

												'tag_other_itm_pur_id'      => $stone['tag_other_itm_pur_id'],

												'tag_other_itm_grs_weight'  => $stone['tag_other_itm_grs_weight'],

												'tag_other_itm_wastage'     => $stone['tag_other_itm_wastage'],

												'tag_other_itm_uom'         => $stone['tag_other_itm_uom'],

												'tag_other_itm_cal_type'    => $stone['tag_other_itm_cal_type'],

												'tag_other_itm_mc'          => $stone['tag_other_itm_mc'],

												'tag_other_itm_rate'        => $stone['tag_other_itm_rate'],

												'tag_other_itm_pcs'         => $stone['tag_other_itm_pcs'],

												'tag_other_itm_amount'      => $stone['tag_other_itm_amount'],

											);



											$this->$model->insertData($est_other_metals, 'ret_est_other_metals');
										}
									}





									$charges_details = $this->$model->get_charges($estTag['tag_id'][$key]);



									if (sizeof($charges_details) > 0) {

										foreach ($charges_details as $charge) {

											$charge_data = array(

												'est_item_id'  => $tagInsert,

												'id_charge'    => $charge['charge_id'],

												'amount'       => $charge['charge_value'],

											);

											$this->$model->insertData($charge_data, 'ret_estimation_other_charges');

											//print_r($this->db->last_query());exit;

										}
									}



									//print_r($this->db->last_query());exit;

								}
							}
						}









						if (!empty($estCatalog)) {

							$arrayEstCatalog = array();

							foreach ($estCatalog['pro_id'] as $key => $val) {

								$arrayEstCatalog = array(

									'esti_id' => $id,

									'design_id'            => ($estCatalog['des_id'][$key] != '' ? $estCatalog['des_id'][$key] : NULL),

									'item_type'            => 1,

									'id_section'           => ($estCatalog['id_section'][$key] != '' ? $estCatalog['id_section'][$key] : ''),

									'product_id'           => ($estCatalog['pro_id'][$key] != '' ? $estCatalog['pro_id'][$key] : ''),

									'id_sub_design'         => ($estCatalog['id_sub_design'][$key] != '' ? $estCatalog['id_sub_design'][$key] : NULL),

									'purity'               => $estCatalog['purity'][$key],

									'size'                 => ($estCatalog['size'][$key] != '' ? $estCatalog['size'][$key] : NULL),

									'piece'                => ($estCatalog['pcs'][$key] != '' ? $estCatalog['pcs'][$key] : NULL),

									'less_wt'              => ($estCatalog['lwt'][$key] != '' ? $estCatalog['lwt'][$key] : NULL),

									'net_wt'               => ($estCatalog['nwt'][$key] != '' ? $estCatalog['nwt'][$key] : NULL),

									'gross_wt'             => ($estCatalog['gwt'][$key] != '' ? $estCatalog['gwt'][$key] : NULL),

									'mc_type'              => ($estCatalog['id_mc_type'][$key] != '' ?  $estCatalog['id_mc_type'][$key] : 1),

									'calculation_based_on' => ($estCatalog['calculation_based_on'][$key] != '' ? $estCatalog['calculation_based_on'][$key] : NULL),

									'wastage_percent'      => ($estCatalog['wastage'][$key] != '' ? $estCatalog['wastage'][$key] : NULL),

									'mc_value'             => ($estCatalog['mc'][$key] != '' ? $estCatalog['mc'][$key] : NULL),

									'item_cost'            => $estCatalog['amount'][$key],
									'pure_wt'              => (!empty($estCatalog['pure_wt'][$key]) ? $estCatalog['pure_wt'][$key] : NULL),
									'pure_rate'            => (!empty($estCatalog['pure_rate'][$key]) ? $estCatalog['pure_rate'][$key] : NULL),

									'is_non_tag'           => ($estCatalog['is_non_tag'][$key] != '' ? $estCatalog['is_non_tag'][$key] : 0),

									'item_total_tax'      => $estCatalog['tax_price'][$key],

									'market_rate_cost'    => $estCatalog['market_rate_cost'][$key],

									'market_rate_tax'     => $estCatalog['market_rate_tax'][$key],

									'item_emp_id'		 =>($estCatalog['item_emp_id'][$key]!='' ? $estCatalog['item_emp_id'][$key]:NULL),

									'est_rate_per_grm'    => !empty($estCatalog['est_rate_per_grm'][$key]) ? $estCatalog['est_rate_per_grm'][$key] : 0,

									'max_va_per'             => !empty($estCatalog['max_va_per'][$key]) ? $estCatalog['max_va_per'][$key] : NULL,

									'wastage_slab_id'             => !empty($estCatalog['id_wastage_slab'][$key]) ? $estCatalog['id_wastage_slab'][$key] : NULL,

									'wast_slab_value'             => !empty($estCatalog['wast_slab_val'][$key]) ? $estCatalog['wast_slab_val'][$key] : NULL,

									'act_wast_per'             => !empty($estCatalog['act_va_per'][$key]) ? $estCatalog['act_va_per'][$key] : 0,

									'max_mc'             => !empty($estCatalog['max_mc'][$key]) ? $estCatalog['max_mc'][$key] : NULL,


								);

								//    print_r($this->db->last_query());exit;

								if (!empty($arrayEstCatalog)) {

									$tagInsert = $this->$model->insertData($arrayEstCatalog, 'ret_estimation_items');

									//print_r($this->db->last_query());exit;

									if ($estCatalog['stone_details'][$key]) {

										$stone_details = json_decode($estCatalog['stone_details'][$key], true);

										foreach ($stone_details as $stone) {

											$stone_data = array(

												'est_id'        => $id,

												'est_item_id'   => $tagInsert,

												'pieces'        => $stone['stone_pcs'],

												'wt'            => $stone['stone_wt'],

												'stone_id'      => $stone['stone_id'],

												'price'         => $stone['stone_price'],

												'rate_per_gram' => $stone['stone_rate'],

												'is_apply_in_lwt' => $stone['show_in_lwt'],

												'stone_cal_type' => $stone['stone_cal_type'],

											);



											$stoneInsert = $this->$model->insertData($stone_data, 'ret_estimation_item_stones');
										}
									}

									if ($estCatalog['material_details'][$key]) {

										$material_details = json_decode($estCatalog['material_details'][$key], true);

										foreach ($material_details as $material) {

											$material_data = array(

												'est_id'        => $id,

												'est_item_id'   => $tagInsert,

												'wt'            => $material['material_wt'],

												'material_id'   => $material['material_id'],

												'price'         => $material['material_price']

											);

											$materialInsert = $this->$model->insertData($material_data, 'ret_estimation_item_other_materials');
										}
									}
								}
							}
						}



						if (!empty($estCustom)) {

							$arrayEstCustom = array();

							foreach ($estCustom['pro_id'] as $key => $val) {

								if ($estCustom['charges_details'][$key]) {

									$charges_details = json_decode($estCustom['charges_details'][$key], true);

									foreach ($charges_details as $charge) {

										$id_charge = $charge['id_charge'];
									}
								}

								$arrayEstCustom = array(

									'esti_id'               => $id,

									'item_type'             => 2,

									'id_section'            => ($estCustom['id_section'][$key] != '' ? $estCustom['id_section'][$key] : ''),

									'design_id'             => ($estCustom['des_id'][$key] != '' ? $estCustom['des_id'][$key] : NULL),

									'id_sub_design'         => ($estCustom['id_sub_design'][$key] != '' ? $estCustom['id_sub_design'][$key] : NULL),

									'product_id'            => $estCustom['pro_id'][$key],

									'tag_id'                => ($estCustom['tag_id'][$key] != '' ? $estCustom['tag_id'][$key] : NULL),

									'purity'                => ($estCustom['purity'][$key] != '' ? $estCustom['purity'][$key] : NULL),

									'size'                  => ($estCustom['size'][$key] != '' ? $estCustom['size'][$key] : NULL),

									'piece'                 => ($estCustom['pcs'][$key] != '' ? $estCustom['pcs'][$key] : NULL),

									'less_wt'               => ($estCustom['lwt'][$key] != '' ? $estCustom['lwt'][$key] : NULL),

									'net_wt'                => ($estCustom['nwt'][$key] != '' ? $estCustom['nwt'][$key] : NULL),

									'mc_type'               => ($estCustom['id_mc_type'][$key] != '' ?  $estCustom['id_mc_type'][$key] : 1),

									'gross_wt'              => ($estCustom['gwt'][$key] != '' ? $estCustom['gwt'][$key] : NULL),

									'calculation_based_on'  => ($estCustom['calculation_based_on'][$key] != '' ? $estCustom['calculation_based_on'][$key] : NULL),

									'wastage_percent'       => ($estCustom['wastage'][$key] != '' ? $estCustom['wastage'][$key] : NULL),

									'mc_value'              => ($estCustom['mc'][$key] != '' ? $estCustom['mc'][$key] : NULL),

									'item_cost'             => $estCustom['amount'][$key],
									'pure_wt'               => (!empty($estCustom['pure_wt'][$key]) ? $estCustom['pure_wt'][$key] : NULL),
									'pure_rate'             => (!empty($estCustom['pure_rate'][$key]) ? $estCustom['pure_rate'][$key] : NULL),

									'item_total_tax'        => !empty($estCustom['tax_price'][$key]) ? $estCustom['tax_price'][$key] : 0,

									'market_rate_cost'      => !empty($estCustom['market_rate_cost'][$key]) ? $estCustom['market_rate_cost'][$key] : 0,

									'market_rate_tax'       => !empty($estCustom['market_rate_tax'][$key]) ? $estCustom['market_rate_cost'][$key] : 0,

									'est_rate_per_grm'      => !empty($estCustom['est_rate_per_grm'][$key]) ? $estCustom['est_rate_per_grm'][$key] : 0,

									'item_emp_id'          => ($estCustom['item_emp_id'][$key]!='' ? $estCustom['item_emp_id'][$key]:NULL),

									'id_division'           => !empty($id_charge) ? $id_charge : 0,

									'max_va_per'             => !empty($estCustom['max_va_per'][$key]) ? $estCustom['max_va_per'][$key] : NULL,

									'wastage_slab_id'             => !empty($estCustom['id_wastage_slab'][$key]) ? $estCustom['id_wastage_slab'][$key] : NULL,

									'wast_slab_value'             => !empty($estCustom['wast_slab_val'][$key]) ? $estCustom['wast_slab_val'][$key] : NULL,
                  
									'act_wast_per'             => !empty($estCustom['act_va_per'][$key]) ? $estCustom['act_va_per'][$key] : 0,

									'stone_amount'             => !empty($estCustom['stone_amt'][$key]) ? $estCustom['stone_amt'][$key] : 0.00,

									'diamond_amount'             => !empty($estCustom['tot_dia_amt'][$key]) ? $estCustom['tot_dia_amt'][$key] : 0.00,

									'max_mc'             => !empty($estCustom['max_mc'][$key]) ? $estCustom['max_mc'][$key] : NULL,


								);

								if (!empty($arrayEstCustom)) {

									$tagInsert = $this->$model->insertData($arrayEstCustom, 'ret_estimation_items');

									//print_r($this->db->last_query());exit;

									if ($estCustom['stone_details'][$key]) {

										$stone_details = json_decode($estCustom['stone_details'][$key], true);

										foreach ($stone_details as $stone) {

											$stone_data = array(

												'est_id'        => $id,

												'est_item_id'   => $tagInsert,

												'pieces'        => $stone['stone_pcs'],

												'wt'            => $stone['stone_wt'],

												'stone_id'      => $stone['stone_id'],

												'price'         => $stone['stone_price'],

												'rate_per_gram'  => $stone['stone_rate'],

												'stone_cal_type'  => $stone['stone_cal_type'],

												'is_apply_in_lwt' => $stone['show_in_lwt'],

												'uom_id'        => $stone['stone_uom_id'],

												'max_stn_amt'     => $stone['max_stn_price'],

											);

											$stoneInsert = $this->$model->insertData($stone_data, 'ret_estimation_item_stones');
										}
									}

									if ($estCustom['material_details'][$key]) {

										$material_details = json_decode($estCustom['material_details'][$key], true);

										foreach ($material_details as $material) {

											$material_data = array(

												'est_id'        => $id,

												'est_item_id'   => $tagInsert,

												'wt'            => $material['material_wt'],

												'material_id'   => $material['material_id'],

												'price'         => $material['material_price']

											);

											$materialInsert = $this->$model->insertData($material_data, 'ret_estimation_item_other_materials');
										}
									}



									if ($estCustom['charges_details'][$key]) {

										$charges_details = json_decode($estCustom['charges_details'][$key], true);

										foreach ($charges_details as $charge) {

											$charge_data = array(

												'est_item_id'  => $tagInsert,

												'id_charge'    => $charge['id_charge'],

												'amount'       => $charge['value_charge'],

											);

											$stoneInsert = $this->$model->insertData($charge_data, 'ret_estimation_other_charges');

											//print_r($this->db->last_query());exit;

										}
									}
								}
							}
						}

						//OLD METAL



						//echo "<pre>"; print_r($estOldmatel);exit;

						if (!empty($estOldmatel)) {

							$arrayOldMatel = array();

							foreach ($estOldmatel['id_category'] as $key => $val) {

								$arrayOldMatel = array(

									'est_id'             => $id,

									'id_category'        => $estOldmatel['id_category'][$key],

									'id_old_metal_type'  => ($estOldmatel['id_old_metal_type'][$key] != '' ? $estOldmatel['id_old_metal_type'][$key] : NULL),

									'id_old_metal_category' => ($estOldmatel['id_old_metal_category'][$key] != '' ? $estOldmatel['id_old_metal_category'][$key] : NULL),

									'purpose'           => ($estOldmatel['id_purpose'][$key] != '' ? $estOldmatel['id_purpose'][$key] : 2),

									'piece'             => $estOldmatel['pcs'][$key],

									'gross_wt'          => $estOldmatel['gwt'][$key],

									'dust_wt'           => (!empty($estOldmatel['dwt'][$key]) ? $estOldmatel['dwt'][$key] : NULL),

									'stone_wt'          => (!empty($estOldmatel['swt'][$key]) ? $estOldmatel['swt'][$key] : NULL),

									'purity'            => (!empty($estOldmatel['purity'][$key]) ? $estOldmatel['purity'][$key] : NULL),

									'touch'            => (!empty($estOldmatel['touch'][$key]) ? $estOldmatel['touch'][$key] : NULL),

									'net_wt'            => $estOldmatel['nwt'][$key],

									'wastage_percent'   => (!empty($estOldmatel['wastage'][$key]) ? $estOldmatel['wastage'][$key] : NULL),

									'wastage_wt'        => (!empty($estOldmatel['wastage_wt'][$key]) ? $estOldmatel['wastage_wt'][$key] : NULL),

									'remark'            => (!empty($estOldmatel['old_metal_remarks'][$key]) ? $estOldmatel['old_metal_remarks'][$key] : NULL),
									'rate_per_gram'     => $estOldmatel['rate'][$key],
									'old_metal_prod_id' => $estOldmatel['old_metal_prod_id'][$key],
									'amount'            => $estOldmatel['amount'][$key]
								);

								if (!empty($arrayOldMatel)) {

									$tagInsert = $this->$model->insertData($arrayOldMatel, 'ret_estimation_old_metal_sale_details');

									//print_r($this->db->last_query());exit;

									if ($estOldmatel['stone_details'][$key]) {



										$stone_details = json_decode($estOldmatel['stone_details'][$key], true);



										foreach ($stone_details as $stone) {

											$stone_data = array(

												'est_id'                => $id,

												'est_old_metal_sale_id' => $tagInsert,

												'pieces'                => $stone['stone_pcs'],

												'wt'                    => $stone['stone_wt'],

												'stone_id'              => $stone['stone_id'],

												'price'                 => $stone['stone_price'],

												'is_apply_in_lwt'       => $stone['show_in_lwt'],

												'stone_cal_type'        => $stone['old_stone_cal_type'],

												'rate_per_gram'         => $stone['old_stone_rate'],

												'uom_id'                => $stone['stone_uom_id'],

											);

											$stoneInsert = $this->$model->insertData($stone_data, 'ret_esti_old_metal_stone_details');

											//	print_r($this->db->last_query());exit;

										}
									}
								}
							}
						}

						$est_oth_inv = (isset($_POST['est_oth_inv']) ? $_POST['est_oth_inv'] : '');

						if (!empty($est_oth_inv)) {

							foreach ($est_oth_inv['id_other_item'] as $key => $val) {

								$otherInvData = array(

									'esti_id'        => $id,

									'id_other_item'  => $est_oth_inv['id_other_item'][$key],

									'no_of_piece'    => $est_oth_inv['no_of_pcs'][$key],

								);

								$tagInsert = $this->$model->insertData($otherInvData, 'ret_estimation_other_inventory_issue');
							}
						}

						$estGiftVoucher = (isset($_POST['gift_voucher']) ? $_POST['gift_voucher'] : '');

						if (!empty($estGiftVoucher)) {

							$arrayGiftVoucher = array();

							foreach ($estGiftVoucher['voucher_no'] as $key => $val) {

								$arrayGiftVoucher[] = array('est_id' => $id, 'voucher_no' => $estGiftVoucher['voucher_no'][$key], 'gift_voucher_details' => NULL, 'gift_voucher_amt' => $estGiftVoucher['gift_voucher_amt'][$key]);
							}

							if (!empty($arrayGiftVoucher)) {

								$tagInsert = $this->$model->insertBatchData($arrayMaterials, 'ret_est_gift_voucher_details');
							}
						}



						if (!empty($estChit)) {

							$arrayChit = array();

							foreach ($estChit['scheme_account_id'] as $key => $val) {

								$arrayChit[] = array(

									'est_id'                    => $id,

									'scheme_account_id'         => $estChit['id_scheme_account'][$key],

									'utl_amount'                => $estChit['chit_amt'][$key],

									'closing_weight'            => ($estChit['closing_weight'][$key] != '' ? $estChit['closing_weight'][$key] : 0),

									'wastage_per'               => ($estChit['wastage_per'][$key] != '' ? $estChit['wastage_per'][$key] : 0),

									'savings_in_wastage'        => ($estChit['savings_in_wastage'][$key] != '' ? $estChit['savings_in_wastage'][$key] : 0),

									'mc_value'                  => ($estChit['mc_value'][$key] != '' ? $estChit['mc_value'][$key] : 0),

									'savings_in_making_charge'  => ($estChit['savings_in_mcvalue'][$key] != '' ? $estChit['savings_in_mcvalue'][$key] : 0),

								);
							}

							if (!empty($arrayChit)) {

								$tagInsert = $this->$model->insertBatchData($arrayChit, 'ret_est_chit_utilization');
							}
						}
					}



					if ($this->db->trans_status() === TRUE) {

						$log_data = array(

							'id_log'        => $this->session->userdata('id_log'),

							'event_date'    => date("Y-m-d H:i:s"),

							'module'        => 'Estimation',

							'operation'     => 'Edit',

							'record'        =>  $id,

							'remark'        => 'Record Updated successfully'

						);

						$this->log_model->log_detail('insert', '', $log_data);

						$this->db->trans_commit();

						$this->session->set_flashdata('chit_alert', array('message' => 'Estimation updated successfully', 'class' => 'success', 'title' => 'Update Estimation'));

						$return_data['status'] = TRUE;

						//redirect('admin_ret_estimation/estimation/list');

					} else {

						$this->db->trans_rollback();

						$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Update Estimation'));

						$return_data['status'] = FALSE;

						//redirect('admin_ret_estimation/estimation/list');

					}

					$return_data['type'] = 1;

					$return_data['id'] = $id;
				} else {
					$return_data['status'] = FALSE;

					$this->session->set_flashdata('chit_alert', array('message' => 'This Estimation is Already Billed', 'class' => 'danger', 'title' => 'Update Estimation'));
				}

				echo json_encode($return_data);

				break;

			case 'update_status':

				$data = array('status' => $status);

				$status = $this->$model->updateData($id, $data);

				if ($status) {

					$this->session->set_flashdata('chit_alert', array('message' => 'floor status updated as ' . ($status ? 'active' : 'inactive') . ' successfully.', 'class' => 'success', 'title' => 'floor  Status'));
				} else {

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'floor  Status'));
				}

				redirect('admin_ret_lot/lot_inward/list');

			default:

				$id_branch  = $this->input->post('id_branch');

				$from_date  = $this->input->post('from_date');

				$to_date    = $this->input->post('to_date');

				$list = $this->$model->ajax_getEstimationList($id_branch, $from_date, $to_date);

				$access = $this->admin_settings_model->get_access('admin_ret_estimation/estimation/list');

				$data = array(

					'list'  => $list,

					'access' => $access

				);

				echo json_encode($data);
		}
	}



	public function createNewCustomer()

	{

		$model = "ret_estimation_model";

		if (!empty($_POST['cusName']) && !empty($_POST['cusMobile']) && !empty($_POST['cusBranch'])) {

			$data = $this->$model->createNewCustomer($_POST['cusName'], $_POST['cusMobile'], $_POST['mail'], $_POST['cusBranch'], $_POST['id_village'], $_POST['cus_type'], $_POST['id_country'], $_POST['id_state'], $_POST['id_city'], ($_POST['address1'] ? $_POST['address1'] : null), ($_POST['address2'] ? $_POST['address2'] : null), ($_POST['address3'] ? $_POST['address3'] : null), $_POST['pincode'], ($_POST['gst_no'] ? $_POST['gst_no'] : null), $_POST['title'], $_POST['id_profession'], $_POST['gender'], $_POST['date_of_birth'], $_POST['date_of_wed'], $_POST['pan_no'], $_POST['aadharid'], $_POST['dl_no'], $_POST['pp_no']);

			if (isset($_FILES['cust_img']['name'])) {

				$img_path = "assets/img/customer/" . $data['response']['id_customer'] . "/customer.jpg";

				$path = "assets/img/customer/" . $data['response']['id_customer'];

				if ($this->set_image($data['response']['id_customer'], $img_path, 'cust_img', $path)) {

					$data['image_upload'] = $img_path;
				}
			} else if (!empty($_POST['customer_img'])) {

				$CusImg = $_POST['customer_img'];

				$CusImgData = json_decode($CusImg);

				if (sizeof($CusImgData) > 0) {

					$_FILES['precious'] = array();

					foreach ($CusImgData as $key => $precious) {

						$imgFile = $this->base64ToFile($precious->src);

						$_FILES['precious'][] = $imgFile;
					}
				}

				if (!empty($CusImgData)) {

					$folder =  "assets/img/customer/" . $data['response']['id_customer'];



					if (!is_dir($folder)) {

						mkdir($folder, 0777, TRUE);
					}

					if (isset($_FILES['precious'])) {

						foreach ($_FILES['precious'] as $file_key => $file_val) {

							if ($file_val['name']) {

								$img_name = 'customer.jpg';

								$path = $folder . "/" . $img_name;

								$result = $this->upload_img('image', $path, $file_val['tmp_name']);

								$data['image_upload'] = $img_name;
							}
						}
					}
				}
			}





			echo json_encode($data);
		} else {

			echo json_encode(array("success" => FALSE, "response" => array(), "message" => "Please fill all the required fields"));
		}
	}



	public function getCustomersBySearch()
	{

		$model = "ret_estimation_model";

		$billing_for = (isset($_POST['billing_for']) ? $_POST['billing_for'] : '');

		$esti_for = (isset($_POST['esti_for']) ? $_POST['esti_for'] : '');

		$data = $this->$model->getAvailableCustomers($_POST['searchTxt'], $esti_for, $billing_for);

		echo json_encode($data);
	}

	public function getTaggingBySearch()
	{

		$model = "ret_estimation_model";

		$data = $this->$model->getTaggingBySearch($_POST['searchTxt'], $_POST['searchField'], $_POST['id_branch']);

		echo json_encode($data);
	}



	public function getTaggingScanBySearch()
	{

		$model = "ret_estimation_model";

		//print_r($_POST);exit;

		$data = $this->$model->getTaggingScanBySearch($_POST['searchTxt'], $_POST['searchField'], $_POST['id_branch'], $_POST['order_no']);

		// print_r($data);exit;

		$adv_details = $this->$model->advance_details_order_no($_POST['order_no'], $_POST['id_branch'], $_POST['fin_year']);

		$tag_reserve = $this->$model->tag_reserve_check($_POST['searchTxt']);

		$response = array('status' => TRUE, 'message' => 'Data Reterived successfully', 'responseData' => $data, "adv_details" => $adv_details, "tag_reserve" => $tag_reserve);


		echo json_encode($response);
	}



	public function getTaggingSearchByCollection()
	{

		$model = "ret_estimation_model";

		$data = $this->$model->getTaggingSearchByCollection($_POST);

		echo json_encode($data);
	}



	public function getPartialTagSearch()
	{

		$model = "ret_estimation_model";

		$data = $this->$model->getPartialTagSearch($_POST['searchTxt'], $_POST['searchField'], $_POST['id_branch']);

		echo json_encode($data);
	}

	public function getPartialTagSearch_old()
	{

		$model = "ret_estimation_model";

		$data = $this->$model->getPartialTagSearch($_POST['searchTxt'], $_POST['searchField'], $_POST['id_branch']);

		echo json_encode($data);
	}

	public function get_order_details()

	{

		$model = "ret_estimation_model";

		$data = $this->$model->order_details($_POST['searchTxt']);

		echo json_encode($data);
	}



	function get_ActiveProduct()

	{

		$model = "ret_estimation_model";

		$data = $this->$model->get_ActiveProduct();

		echo json_encode($data);
	}



	public function getOrderBySearch()

	{

		$model = "ret_estimation_model";



		$response = array();



		$data = $this->$model->getOrderBySearch($_POST['searchTxt'], $_POST['id_branch'], $_POST['fin_year']);



		if ($data) {

			$total_pcs = $this->$model->get_order_details($_POST['searchTxt'], $_POST['id_branch'], $_POST['fin_year']);



			$adv_details = $this->$model->advance_details_order_no($_POST['searchTxt'], $_POST['id_branch'], $_POST['fin_year']);



			$tagged_pcs = 0;



			foreach ($data as $value) {

				$tagged_pcs += $value['piece'];
			}

			if ($tagged_pcs == $total_pcs) {

				$response = array('status' => TRUE, 'message' => 'Data Reterived successfully', 'responseData' => $data, "adv_details" => $adv_details);
			} else {

				$response = array('status' => FALSE, 'message' => 'Order Item Missing', 'responseData' => []);
			}
		} else {

			$response = array('status' => FALSE, 'message' => 'No Record Found', 'responseData' => []);
		}

		echo json_encode($response);
	}

	public function getProductBySearch()
	{

		$model = "ret_estimation_model";

		$CatCode = (isset($_POST['cat_id']) ? $_POST['cat_id'] : '');

		$is_non_tag = (isset($_POST['is_non_tag']) ? $_POST['is_non_tag'] : '');

		$id_branch = (isset($_POST['id_branch']) ? $_POST['id_branch'] : '');

		$pro_id = (isset($_POST['pro_id']) ? $_POST['pro_id'] : '');

		$sratchText = (isset($_POST['searchTxt']) ? $_POST['searchTxt'] : '');

		$data = $this->$model->getProductBySearch($sratchText, $CatCode, $is_non_tag, $id_branch, $pro_id);

		echo json_encode($data);
	}



	/*public function get_non_tag_stock(){

		$model = "ret_estimation_model";

		$id_branch=$_POST['id_branch'];

		$cat_design=$_POST['id_design'];

		$cat_sub_design=$_POST['id_sub_design'];

		$data=$this->$model->getdesigndetails($cat_design,$cat_sub_design);

        echo json_encode($data);

	}*/





	public function getCustomProductBySearch()
	{

		$model = "ret_estimation_model";

		$CatCode = (isset($_POST['cat_id']) ? $_POST['cat_id'] : '');

		$data = $this->$model->getCustomProductBySearch($_POST['searchTxt'], $CatCode, $_POST['pro_id']);

		echo json_encode($data);
	}



	function getNonTagproducts()

	{

		$model = "ret_estimation_model";

		$data = $this->$model->getNonTagproducts();

		echo json_encode($data);
	}



	public function getProductDesignBySearch()
	{

		$model = "ret_estimation_model";

		$searchTxt = $_POST['searchTxt'];

		$id_branch = $_POST['id_branch'];



		$is_non_tag = $_POST['is_non_tag'];

		$ProCode = (isset($_POST['ProCode']) ? $_POST['ProCode'] : '');

		$data = $this->$model->getProductDesignBySearch($searchTxt, $ProCode, $id_branch, $is_non_tag);

		echo json_encode($data);
	}



	public function getMetalTypes()
	{

		$model = "ret_estimation_model";

		$data = $this->$model->getMetalTypes();

		echo json_encode($data);
	}

	public function getNonTagLots()
	{

		$model = "ret_estimation_model";

		$data = $this->$model->getNonTagLots($_POST['searchTxt'], $_POST['id_branch']);

		echo json_encode($data);
	}

	//chit acc

	public function get_scheme_accounts()

	{

		$model = "ret_estimation_model";

		$searchTxt = $this->input->post('searchTxt');

		$data = $this->$model->get_closed_accounts($searchTxt);

		echo json_encode($data);
	}

	//chit acc

	public function get_stone_details()

	{

		$model = "ret_estimation_model";

		$est_item_id = $this->input->post('est_item_id');

		$data = $this->$model->get_stone_details($est_item_id);

		echo json_encode($data);
	}

	public function get_old_metal_stone_details()

	{

		$model = "ret_estimation_model";

		$est_old_metal_sale_id = $this->input->post('est_old_metal_sale_id');

		$data = $this->$model->get_old_metal_stone_details($est_old_metal_sale_id);

		echo json_encode($data);
	}

	public function get_other_material_details()

	{

		$model = "ret_estimation_model";

		$est_item_id = $this->input->post('est_item_id');

		$data = $this->$model->get_other_material_details($est_item_id);

		echo json_encode($data);
	}

	public function get_old_metal_rate()

	{

		$model = "ret_estimation_model";

		$id_metal = $this->input->post('id_metal');

		$data = $this->$model->get_old_metal_rate($id_metal);

		echo json_encode($data);
	}

	public function get_all_old_metal_rates()

	{

		$model = "ret_estimation_model";

		$data = $this->$model->get_all_old_metal_rates();

		echo json_encode($data);
	}

	/*public function generate_invoice($est_id)

	{

		$model="ret_estimation_model";

		$data['estimation'] = $this->$model->get_entry_records($est_id);

		$data['est_other_item'] = $this->$model->getOtherEstimateItemsDetails($est_id);

		$this->load->helper(array('dompdf', 'file'));

		$this->load->helper(array('dompdf', 'file'));

	        $dompdf = new DOMPDF();

			$html = $this->load->view('include/estimation_thermal', $data,true);

		    $dompdf->load_html($html);

			$customPaper = array(0,0,40,80);

			$dompdf->set_paper($customPaper, "portriat" );

			$dompdf->render();

			while(ob_get_level()) ob_end_clean();
			$dompdf->stream("receipt1.pdf",array('Attachment'=>0));

	}*/



	public function generate_invoice($est_id)

	{

		$model = "ret_estimation_model";

		$bill_model = "ret_billing_model";

		$data['estimation'] = $this->$model->get_entry_records($est_id);

		$data['estimation']['app_qrcode'] = $this->config->item('base_url') . "mobile_app_qrcode/skj_app_qrcode.png";

		$data['estimation']['playstore'] = $this->config->item('base_url') . "mobile_app_qrcode/playstore.png";

		$data['est_other_item'] = $this->$model->getOtherEstimateItemsDetails($est_id);

		$data['metal_rates'] = $this->$model->get_branchwise_rate($data['estimation']['id_branch']);

		$data['comp_details'] = $this->$bill_model->getCompanyDetails($data['estimation']['id_branch']);
		/*QR code Generation*/
		$this->load->library('phpqrcode/qrlib');

		$SERVERFILEPATH = 'esti_qrcode/' . $data['estimation']['estimation_id'];

		if (!is_dir($SERVERFILEPATH)) {

			mkdir($SERVERFILEPATH, 0777, TRUE);
		}

		$folder = $SERVERFILEPATH;

		$file_name1 = $data['estimation']['estimation_id'] . ".png";

		$file_name = $SERVERFILEPATH . '/' . $file_name1;

		QRcode::png($data['estimation']['esti_no'], $file_name);  //Passing QR data

		$data['filename'] = base_url('esti_qrcode/' . $data['estimation']['estimation_id'] . '/' . $file_name1);

		
		$html = $this->load->view('estimation/print/est_print_2', $data, true);



		// print_r($html);exit;

		$this->load->helper(array('dompdf', 'file'));

		$dompdf = new DOMPDF();

		$dompdf->load_html($html);

		$dompdf->set_paper('A4', "portriat");

		$dompdf->render();

		while(ob_get_level()) ob_end_clean();
		$dompdf->stream("receipt1.pdf", array('Attachment' => 0));
	}



	public function generate_brief_copy($est_id)

	{

		$model = "ret_estimation_model";

		$data['estimation'] = $this->$model->get_entry_records($est_id);

		$data['est_other_item'] = $this->$model->getOtherEstimateItemsDetails($est_id);

		$data['metal_rates'] = $this->$model->get_branchwise_rate($data['estimation']['id_branch']);

		//echo "<pre>"; print_r($data);exit;

		$this->load->helper(array('dompdf', 'file'));

		$dompdf = new DOMPDF();

		$html = $this->load->view('estimation/print/brief_copy', $data, true);

		$dompdf->load_html($html);

		$dompdf->set_paper('A4', "portriat");

		$dompdf->render();

		$page_count = $dompdf->get_canvas()->get_page_number();

		//echo "<pre>"; print_r($page_count);exit;

		if ($page_count > 1) {

			$dompdf = new DOMPDF();

			$dompdf->load_html($html);

			//$height=150*$page_count;

			$customPaper = array(0, 0, 90, '');

			$dompdf->set_paper($customPaper, "portriat");

			$dompdf->render();
		}

		while(ob_get_level()) ob_end_clean();
		$dompdf->stream("receipt1.pdf", array('Attachment' => 0));
	}



	function get_employee()

	{

		$model = "ret_estimation_model";

		$id_branch = $this->input->post('id_branch');

		$data = $this->$model->get_employee($id_branch);

		echo json_encode($data);
	}



	function get_partial_details()

	{

		$model = "ret_estimation_model";

		$data = $this->$model->get_partial_details();

		echo json_encode($data);
	}



	public function ajax_get_village()

	{

		$model = "ret_estimation_model";

		$cus_data = $this->$model->get_village();

		echo json_encode($cus_data);
	}



	public function get_customer()

	{

		$model = "ret_estimation_model";

		$id_customer = $this->input->post('id_customer');

		$cus_data = $this->$model->get_customer($id_customer);

		$path = "assets/img/customer/" . $id_customer;

		if (is_dir($path)) {

			$path = "assets/img/customer/" . $id_customer . "/customer.jpg";

			$cus_data['img_path'] = base_url($path);
		} else {

			$path = "assets/img/default.png";

			$cus_data['img_path'] = base_url($path);
		}

		echo json_encode($cus_data);
	}



	public function updateCustomer()
	{

		$model = "ret_estimation_model";

		if (!empty($_POST['cusName']) && !empty($_POST['cusMobile']) && !empty($_POST['id_customer']) && !empty($_POST['cusBranch'])) {

			$data = $this->$model->updateCustomer($_POST['id_customer'], $_POST['cusName'], $_POST['cusMobile'], $_POST['mail'], $_POST['cusBranch'], $_POST['cus_type'], $_POST['id_country'], $_POST['id_state'], $_POST['id_city'], ($_POST['address1'] ? $_POST['address1'] : null), ($_POST['address2'] ? $_POST['address2'] : null), ($_POST['address3'] ? $_POST['address3'] : null), $_POST['pincode'], ($_POST['gst_no'] ? $_POST['gst_no'] : null), $_POST['title'], $_POST['id_profession'], $_POST['gender'], $_POST['date_of_birth'], $_POST['date_of_wed'], $_POST['id_village'], $_POST['pan_no'], $_POST['aadharid'], $_POST['dl_no'], $_POST['pp_no']);

			if (isset($_FILES['cust_img']['name'])) {

				$img_path = "assets/img/customer/" . $data['response']['id_customer'] . "/customer.jpg";

				$path = "assets/img/customer/" . $data['response']['id_customer'];

				if ($this->set_image($data['response']['id_customer'], $img_path, 'cust_img', $path)) {

					$data['image_upload'] = $img_path;
				}
			} else if (!empty($_POST['customer_img'])) {

				$CusImg = $_POST['customer_img'];

				$CusImgData = json_decode($CusImg);

				if (sizeof($CusImgData) > 0) {

					$_FILES['precious'] = array();

					foreach ($CusImgData as $key => $precious) {

						$imgFile = $this->base64ToFile($precious->src);

						$_FILES['precious'][] = $imgFile;
					}
				}

				if (!empty($CusImgData)) {

					$folder =  "assets/img/customer/" . $data['response']['id_customer'];



					if (!is_dir($folder)) {

						mkdir($folder, 0777, TRUE);
					}

					if (isset($_FILES['precious'])) {

						foreach ($_FILES['precious'] as $file_key => $file_val) {

							if ($file_val['name']) {

								$img_name = 'customer.jpg';

								$path = $folder . "/" . $img_name;

								$result = $this->upload_img('image', $path, $file_val['tmp_name']);

								$data['image_upload'] = $img_name;
							}
						}
					}
				}
			}



			echo json_encode($data);
		} else {

			echo json_encode(array("success" => FALSE, "response" => array(), "message" => "Please fill all the required fields"));
		}
	}



	function get_old_metal_type()

	{

		$model = "ret_estimation_model";

		$data = $this->$model->get_old_metal_type($_POST['id_metal']);

		echo json_encode($data);
	}

	function get_old_metal_types()

	{

		$model = "ret_estimation_model";

		$data = $this->$model->get_old_metal_types();

		echo json_encode($data);
	}

	function get_old_metal_category()

	{

		$model = "ret_estimation_model";

		$data = $this->$model->get_old_metal_category();

		echo json_encode($data);
	}

	function get_metal_purity_rate()

	{

		$model = "ret_estimation_model";

		$data = $this->$model->get_metal_purity_rate($_POST);

		echo json_encode($data);
	}





	public function getCustomerDet()
	{

		$model = "ret_estimation_model";

		$data = $this->$model->getCustomerDet('', $_POST['id_customer']);

		echo json_encode($data);
	}



	public function getProductSubDesignBySearch()
	{

		$model = "ret_estimation_model";

		$searchTxt = $_POST['searchTxt'];

		$id_branch = $_POST['id_branch'];

		$ProCode = (isset($_POST['ProCode']) ? $_POST['ProCode'] : '');

		$data = $this->$model->getProductSubDesignBySearch($searchTxt, $ProCode, $id_branch);

		echo json_encode($data);
	}



	function get_purity_rate()

	{

		$model = "ret_estimation_model";

		$data = $this->$model->get_purity_rate();

		echo json_encode($data);
	}

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







	function cancel_order_tag()

	{

		$model = "ret_estimation_model";

		$remarks = $this->input->post('remarks');

		$id_orderdetails = $this->input->post('id_order');

		$upd_data = array(

			"order_cancelled_date" => date("Y-m-d H:i:s"),

			"cancelreason"   => $remarks

		);

		$this->db->trans_begin();

		$status = $this->$model->updateData($upd_data, 'id_orderdetails', $id_orderdetails, 'customerorderdetails');

		if ($status) {

			$tagged_order_detail = $this->$model->get_taggedorder_details($id_orderdetails);

			foreach ($tagged_order_detail as $tagged_od) {

				$updData = array('id_orderdetails' => null);

				$this->$model->updateData($updData, 'id_orderdetails', $tagged_od['id_orderdetails'], 'ret_taging');
			}
		}

		if ($this->db->trans_status() === TRUE) {

			$this->db->trans_commit();

			$return_data = array('status' => TRUE);
		} else {

			$this->db->trans_commit();

			$return_data = array('status' => FALSE);
		}

		echo json_encode($return_data);
	}



	public function get_non_tag_stock()

	{

		$model = "ret_estimation_model";

		$data = $this->$model->get_non_tag_stock_details($_POST);

		echo json_encode($data);
	}



	function get_tag_status_details()

	{

		$model = "ret_estimation_model";

		$data = $this->$model->get_tag_status_details($_POST);

		if (sizeof($data) > 0) {

			$returndata = array('result' => $data, 'message' => 'Tag is ' . $data['status']);
		} else {

			$returndata = array('result' => [], 'message' => 'Invalid Tag');
		}

		echo json_encode($returndata);
	}

	public function get_tag_img_by_id()
	{
		$model = "ret_estimation_model";
		$tag_id = $this->input->post('tag_id');
		$data = $this->$model->getTagImageDetails($tag_id);
		echo json_encode($data);
	}

	function isEmptySetDefault($value, $default)
	{

		if ($value != '' &&  $value != NULL &&  $value != 'null') {
			return $value;
		} else {
			return $default;
		}
	}


	function get_village_by_pincode()
	{
		$model = "ret_estimation_model";
		$data = $this->$model->get_village_by_pincode($_POST['pin_code']);
		echo json_encode($data);
	}

	function old_get_village()
	{
		$model = "ret_estimation_model";
		$response_data = array();
		$Data = array(
			'village_name'       => $this->input->post('village_name'),
			'pincode'            => $this->input->post('pincode'),
			'status'             => 1,
			// 'is_default' 	     => 0,
			'date_add'           => date("Y-m-d H:i:s"),
		);
		$this->db->trans_begin();
		$insid = $this->$model->insertData($Data, 'village');

		if ($this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
			$response_data = array('status' => TRUE, 'message' => 'Village Added Successfully.', 'ins_id' => $insid);
		} else {
			$this->db->trans_rollback();
			$response_data = array('status' => FALSE, 'message' => 'Unable to proceed Your Request.');
		}
		echo json_encode($response_data);
	}
	
	function get_village() {
        $model = "ret_estimation_model";
        $response_data = array();
        
        $village_name = $this->input->post('village_name');
        $pincode = $this->input->post('pincode');
        $village_name_LC = strtolower($village_name);
        
        $this->db->select('*');
        $this->db->from('village');
        $this->db->where('LOWER(village_name)', $village_name_LC);
		$this->db->where('pincode', $pincode);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $existing_village = $query->row_array();
            $response_data = array('status' => FALSE, 'message' => 'Village already exists.', 'existing_village' => $existing_village);
        } else {
            $Data = array(
                'village_name' => $village_name,
                'pincode' => $pincode,
                'status' => 1,
                'date_add' => date("Y-m-d H:i:s"),
            );
            
            $this->db->trans_begin();
            $insid = $this->$model->insertData($Data, 'village');
    
            if ($this->db->trans_status() === TRUE) {
                $this->db->trans_commit();
                $response_data = array('status' => TRUE, 'message' => 'Village Added Successfully.', 'ins_id' => $insid);
            } else {
                $this->db->trans_rollback();
                $response_data = array('status' => FALSE, 'message' => 'Unable to proceed with your request.');
            }
        }
        
        echo json_encode($response_data);
    }

	function get_sectionBranchwise()
	{

		$model = "ret_estimation_model";

		$data = $this->$model->get_sectionBranchwise($_POST);

		echo json_encode($data);
	}

	function pan_available()
	{
		// echo "<pre>";print_r($_POST);exit;

		$model = "ret_estimation_model";

		$data = $this->$model->pan_available($_POST['pan_number'], $_POST['id_customer']);

		echo json_encode($data);
	}

	function gst_available()
	{

		$model = "ret_estimation_model";

		$data = $this->$model->gst_available($_POST['gst_number'], $_POST['id_customer']);

		echo json_encode($data);
	}

	function aadhar_available()
	{
		$model = "ret_estimation_model";

		// print_r($_POST);exit;

		$data = $this->$model->aadhar_available($_POST['aadhar_number'], $_POST['id_customer']);

		echo json_encode($data);
	}
	function passport_available()
	{
		$model = "ret_estimation_model";

		$data = $this->$model->passport_available($_POST['passport_no'], $_POST['id_customer']);

		echo json_encode($data);
	}
	function dl_available()
	{
		$model = "ret_estimation_model";

		$data = $this->$model->dl_available($_POST['dl_no'], $_POST['id_customer']);

		echo json_encode($data);
	}
	public function get_old_metal_Product()
	{
		$model = "ret_estimation_model";
		$data=$this->$model->get_old_metal_Product($_POST);
        echo json_encode($data);
	}
	public function get_va_range(){
		$model = "ret_estimation_model";
		// print_r($_POST['id_metal']);exit;
		$data  = $this->$model->get_va_range($_POST['id_metal']);
		// print_r($data);exit;
		echo json_encode($data);
	}

	public function getFinancialYr(){

		$bill_model = "ret_billing_model";

		$data  = $this->$bill_model->GetFinancialYear();

		echo json_encode($data);
	}
}
