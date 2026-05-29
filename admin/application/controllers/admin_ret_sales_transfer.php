<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Admin_ret_sales_transfer extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->model('ret_sales_transfer_model');
		$this->load->model('admin_settings_model');
		$this->load->model('ret_billing_model');
		$this->load->model("log_model");

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
	/**
	 * EDA Functions Starts
	 */
	public function sales_transfer($type = "", $id = "")
	{
		$model = "ret_sales_transfer_model";
		switch ($type) {
			case 'list':
				$data['main_content'] = "sales_transfer/list";
				$this->load->view('layout/template', $data);
				break;
			case 'add':
				$data['fin_year'] = $this->$model->get_FinancialYear();
				$data['sales_transfer_download'] = $this->$model->getSettigsByName('sales_transfer_download');
				$data['is_metal_for_billing'] = $this->$model->getSettigsByName('is_metal_for_billing');
				$data['main_content'] = "sales_transfer/sales_trasnfer";
				$this->load->view('layout/template', $data);
				break;
			case 'ret_add':
				$data['fin_year'] = $this->$model->get_FinancialYear();
				$data['sales_transfer_download'] = $this->$model->getSettigsByName('sales_transfer_download');
				$data['is_metal_for_billing'] = $this->$model->getSettigsByName('is_metal_for_billing');
				$data['main_content'] = "sales_transfer/sales_ret_transfer";
				$this->load->view('layout/template', $data);
				break;
			case 'sales_trans_tag':
				$data = $this->$model->get_sales_transfer_tag_details($_POST);
				echo json_encode($data);
				break;
			case 'sales_trans_approval_tag':
				$data = $this->$model->get_sales_trans_approval_tag($_POST);
				echo json_encode($data);
				break;
			case 'sales_return_trans_tag':
				$data = $this->$model->get_sales_return_trans_req_tag($_POST);
				echo json_encode($data);
				break;
			case 'sales_return_trans_approval_tag':
				$data = $this->$model->get_sales_return_trans_approval_tag($_POST);
				echo json_encode($data);
				break;

			case 'getTagsByFilter':
				$data = $this->$model->fetchTagsByFilter_scan($_POST);
				echo json_encode($data);
				break;

			case 'getReturnTagsByFilter':
				$data = $this->$model->fetchReturnTagsByFilter_scan($_POST);
				echo json_encode($data);
				break;

			// === Deemed Sales Transfer — New Item Type Endpoints ===
			case 'getNonTaggedStock':
				$data = $this->$model->get_non_tagged_stock($_POST);
				echo json_encode($data);
				break;

			case 'getOldGoldItems':
				$data = $this->$model->get_old_gold_items($_POST);
				echo json_encode($data);
				break;

			case 'getSalesReturnItems':
				$data = $this->$model->get_sales_return_items($_POST);
				echo json_encode($data);
				break;

			case 'getPartlySoldItems':
				$data = $this->$model->get_partly_sold_items($_POST);
				echo json_encode($data);
				break;

			// === Phase 2: List View + OG Detail Endpoints ===
			case 'getSalesTransferList':
				$list = $this->$model->get_sales_transfer_list($_POST);
				$profile = $this->admin_settings_model->profileDB("get", $this->session->userdata('profile'));
				$access = $this->admin_settings_model->get_access('admin_ret_sales_transfer/sales_transfer/list');
				echo json_encode(array(
					'list' => $list,
					'access' => $access,
					'profile' => $profile
				));
				break;

			case 'getOldGoldDetail':
				$data = $this->$model->get_old_gold_detail($this->input->post('og_id'));
				echo json_encode($data);
				break;

			// === Phase 3: In-Transit Dashboard & Print ===
			case 'in_transit':
				$data['main_content'] = "sales_transfer/in_transit";
				$this->load->view('layout/template', $data);
				break;

			case 'getInTransitList':
				$list = $this->$model->get_in_transit_list($_POST);
				$kpi  = $this->$model->get_in_transit_kpi($_POST);
				echo json_encode(array('list' => $list, 'kpi' => $kpi));
				break;

			case 'getInTransitBranches':
				$branches = $this->$model->get_active_branches();
				echo json_encode(array('branches' => $branches));
				break;

			case 'print':
				$data['print_data'] = $this->$model->get_transfer_print_data($id);
				$data['main_content'] = "sales_transfer/print";
				$this->load->view('sales_transfer/print', $data);
				break;
		}
	}


	function create_sales_transfer()
	{

		// echo "<pre>";print_r($_POST);exit;
		$model = "ret_billing_model";
		$sales_trans_model = "ret_sales_transfer_model";

		$return_data            = [];
		$from_branch            = $this->input->post('from_brn');
		$to_brn                 = $this->input->post('to_brn');
		$tot_bill_amount        = $this->input->post('tot_bill_amount');
		$req_data               = $this->input->post('req_data');
		$form_secret            = $this->input->post('form_secret');
		$id_metal               = ($this->input->post('id_metal') != '' ? $this->input->post('id_metal') : '');
		$remark                 = $this->input->post('remark');
		$salesTransType         = ($this->input->post('salesTransType') != '' ? $this->input->post('salesTransType') : 1);

		// For Purchase Items (type=3), salesTransType comes per-item from the form
		$isPurchaseItems = (intval($salesTransType) == 3);

		// === Backend Validations ===
		if (empty($req_data) || !is_array($req_data)) {
			echo json_encode(array('status' => false, 'message' => 'No items selected for transfer.'));
			return;
		}
		if (empty($from_branch) || empty($to_brn)) {
			echo json_encode(array('status' => false, 'message' => 'Please select both From and To branches.'));
			return;
		}
		if ($from_branch == $to_brn) {
			echo json_encode(array('status' => false, 'message' => 'Cannot transfer to the same branch.'));
			return;
		}

		// Form Secret — prevent double submit (matches billing pattern)
		if ($this->session->userdata('SALES_TRANS_FORM_SECRET')) {
			if (strcasecmp($form_secret, $this->session->userdata('SALES_TRANS_FORM_SECRET')) !== 0) {
				echo json_encode(array('status' => false, 'message' => 'Duplicate submission detected. Please refresh and try again.'));
				return;
			}
		}

		$metal_rate                 = $this->$model->get_branchwise_rate($from_branch);
		$from_branch_details = $this->$sales_trans_model->get_branch_details($from_branch);
		$to_branch_details   = $this->$sales_trans_model->get_branch_details($to_brn);

		// V-02: GST Entity Validation — block same-GSTIN transfers
		$from_gst = isset($from_branch_details['gst_number']) ? trim($from_branch_details['gst_number']) : '';
		$to_gst   = isset($to_branch_details['gst_number']) ? trim($to_branch_details['gst_number']) : '';
		if ($from_gst !== '' && $to_gst !== '' && $from_gst === $to_gst) {
			echo json_encode(array('status' => false, 'message' => 'Cannot create Sales Transfer between branches with same GSTIN (' . $from_gst . '). Use Branch Transfer instead.'));
			return;
		}

		$ismetalReq  = $this->$sales_trans_model->getSettigsByName('is_metal_for_billing');
		if ($ismetalReq) {
			$metal_details       = $this->$sales_trans_model->get_metal_details($id_metal);
		}


		$tot_bill_amount = 0;
		$bill_no     = $this->$model->code_number_generator($from_branch, $id_metal, 1);
		$dCData      = $this->admin_settings_model->getBranchDayClosingData($from_branch);
		$fin_year    = $this->$model->get_FinancialYear();
		$bill_date   = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
		$ref_no      = $this->$model->generateRefNo($from_branch, 'sales_ref_no', $id_metal, 1);

		$data = array(
			'bill_no'		    => ($ismetalReq ? $metal_details['metal_code'] . '-' . $bill_no : $bill_no),
			'metal_type'		=> ($id_metal != '' ? $id_metal : NULL),
			'sales_ref_no'		=> $ref_no,
			'fin_year_code'		=> $fin_year['fin_year_code'],
			'bill_type'		    => 13, //Sales Trasnfer
			'tot_bill_amount'	=> 0,
			'bill_date'	        => $bill_date,
			'created_time'	    => date("Y-m-d H:i:s"),
			'created_by'        => $this->session->userdata('uid'),
			'id_branch'         => $from_branch,
			'goldrate_22ct' 	=> $metal_rate['goldrate_22ct'],
			'silverrate_1gm' 	=> $metal_rate['silverrate_1gm'],
			'remark'   	        => ($remark != '' ? $remark : NULL),
			'billing_for'       => 3,
			'from_branch'       => $from_branch,
			'to_branch'         => $to_brn,
			'is_credit'         => 1,
			'credit_status'     => 2,
			'bill_status'       => 1,
			'form_secret'	    => $form_secret,
		);
		$this->db->trans_begin();
		$insId = $this->$model->insertData($data, 'ret_billing');
		if ($insId) {
			foreach ($req_data as $items) {

				// Resolve per-item salesTransType (must be first — other validations depend on it)
				$itemSalesTransType = $isPurchaseItems ? (isset($items['salesTransType']) ? intval($items['salesTransType']) : 3) : intval($salesTransType);

				// V-10: Purchase Items — auto-calculate rate_per_grm = item_cost / gross_wt
				if ($isPurchaseItems && (!isset($items['rate_per_grm']) || floatval($items['rate_per_grm']) <= 0)) {
					$pi_gross = floatval(isset($items['gross_wt']) ? $items['gross_wt'] : 0);
					$pi_cost  = floatval(isset($items['item_cost']) ? $items['item_cost'] : 0);
					if ($pi_cost > 0 && $pi_gross > 0) {
						$items['rate_per_grm'] = round($pi_cost / $pi_gross, 2);
					} else if ($pi_gross > 0) {
						// Partly Sold / zero-cost items: use branch metal rate as fallback
						$fallback_rate = floatval($metal_rate['goldrate_22ct']);
						if (isset($items['tag_id']) && $items['tag_id'] > 0) {
							$tag_metal = $this->db->query("SELECT cat.id_metal FROM ret_taging t LEFT JOIN ret_product_master p ON p.pro_id = t.product_id LEFT JOIN ret_category cat ON cat.id_ret_category = p.cat_id WHERE t.tag_id = " . $this->db->escape($items['tag_id']))->row();
							if ($tag_metal && $tag_metal->id_metal == 2) {
								$fallback_rate = floatval($metal_rate['silverrate_1gm']);
							}
						}
						$items['rate_per_grm'] = $fallback_rate;
					} else {
						$items['rate_per_grm'] = 0;
					}
				}

				// V-05: Rate Per Gram must be > 0
				$rate_val = floatval(isset($items['rate_per_grm']) ? $items['rate_per_grm'] : 0);
				if ($rate_val <= 0) {
					$this->db->trans_rollback();
					echo json_encode(array('status' => false, 'message' => 'Rate per gram must be greater than 0.'));
					return;
				}

				// V-04: Tag status pre-check — tag must be available (status=0) for Tagged items only
				// Sales Return (4) tags have status=6, Partly Sold (5) tags have status=1 — both are expected
				if ($itemSalesTransType == 1 && isset($items['tag_id']) && $items['tag_id'] > 0) {
					$tag_check = $this->db->query("SELECT tag_status FROM ret_taging WHERE tag_id = " . $this->db->escape($items['tag_id']))->row();
					if (!$tag_check || $tag_check->tag_status != 0) {
						$this->db->trans_rollback();
						$cur_status = $tag_check ? $tag_check->tag_status : 'N/A';
						echo json_encode(array('status' => false, 'message' => 'Tag #' . $items['tag_id'] . ' is not available for transfer (current status: ' . $cur_status . ').'));
						return;
					}
				}

				// V-10: Old Metal — gross_wt must be > 0 (prevents division by zero in rate calc)
				if ($itemSalesTransType == 3) {
					$og_gross = floatval(isset($items['gross_wt']) ? $items['gross_wt'] : 0);
					if ($og_gross <= 0) {
						$this->db->trans_rollback();
						echo json_encode(array('status' => false, 'message' => 'Old Metal gross weight must be greater than 0.'));
						return;
					}
				}

				// V-11: Partly Sold — transfer weight must not exceed available tag weight
				if ($itemSalesTransType == 5 && isset($items['tag_id']) && $items['tag_id'] > 0) {
					$ps_tag = $this->db->query("SELECT gross_wt FROM ret_taging WHERE tag_id = " . $this->db->escape($items['tag_id']))->row();
					if ($ps_tag && floatval($items['gross_wt']) > floatval($ps_tag->gross_wt)) {
						$this->db->trans_rollback();
						echo json_encode(array('status' => false, 'message' => 'Partial transfer weight (' . $items['gross_wt'] . 'g) exceeds available tag weight (' . $ps_tag->gross_wt . 'g).'));
						return;
					}
				}

				$total_igst = 0;
				$total_sgst = 0;
				$total_cgst = 0;
				if (isset($items['calc_type']) && $items['calc_type'] == 2) {
					$taxable_amt = ($items['piece'] * $items['rate_per_grm']);
				} else {
					$taxable_amt = ($items['gross_wt'] * $items['rate_per_grm']);
				}

				$tax_amount  = (($taxable_amt * 3) / 100);
				$item_cost   = ($taxable_amt + $tax_amount);

				if ($from_branch_details['id_country'] == $to_branch_details['id_country']) {
					if ($from_branch_details['id_state'] == $to_branch_details['id_state']) {
						$total_sgst = number_format($tax_amount / 2, 2, ".", "");
						$total_cgst = number_format($tax_amount / 2, 2, ".", "");
					} else {
						$total_igst = $tax_amount;
					}
				} else {
					$total_igst = $tax_amount;
				}
				$tot_bill_amount += $item_cost;
				// Resolve product_id from tag for purchase items (SR/PS have tags)
				$resolved_product_id = isset($items['product_id']) ? $items['product_id'] : 0;
				$resolved_design_id  = isset($items['design_id']) ? $items['design_id'] : 0;
				if ($isPurchaseItems && (!$resolved_product_id || $resolved_product_id == 0) && isset($items['tag_id']) && $items['tag_id'] > 0) {
					$tag_prod = $this->db->query("SELECT product_id, design_id FROM ret_taging WHERE tag_id = " . $this->db->escape($items['tag_id']))->row();
					if ($tag_prod) {
						$resolved_product_id = $tag_prod->product_id;
						$resolved_design_id  = $tag_prod->design_id;
					}
				}

				$arrayBillSales = array(
					'bill_id'       => $insId,
					'bill_type' 	=> 2,
					'product_id' 	=> $resolved_product_id,
					'design_id' 	=> $resolved_design_id,
					'tag_id'		=> isset($items['tag_id']) ? $items['tag_id'] : 0,
					'purity' 		=> isset($items['purity']) ? $items['purity'] : 0,
					'piece' 		=> isset($items['piece']) ? $items['piece'] : 1,
					'less_wt' 		=> isset($items['less_wt']) ? $items['less_wt'] : 0,
					'net_wt' 		=> isset($items['net_wt']) ? $items['net_wt'] : 0,
					'gross_wt' 		=> $items['gross_wt'],
					'calculation_based_on' => isset($items['calculation_based_on']) ? $items['calculation_based_on'] : 1,
					'item_cost' 	=> $item_cost,
					'total_igst' 	=> $total_igst,
					'total_sgst' 	=> $total_sgst,
					'total_cgst' 	=> $total_cgst,
					'item_total_tax' => $tax_amount,
					'rate_per_grm'  => $items['rate_per_grm'],
					'item_type'     => isset($items['item_type']) ? $items['item_type'] : 0,
					'salesTransType' => $itemSalesTransType,
				);
				$tagInsert = $this->$model->insertData($arrayBillSales, 'ret_bill_details');

				// Type-specific flag updates
				if ($tagInsert) {
					switch ($itemSalesTransType) {
						case 1: // Tagged
							$this->$model->updateData(array('tag_status' => 4), 'tag_id', $items['tag_id'], 'ret_taging');
							$tag_log = array(
								'tag_id'	  => $items['tag_id'],
								'date'		  => $bill_date,
								'status'	  => 11,
								'from_branch' => $from_branch,
								'to_branch'	  => NULL,
								'created_on'  => date("Y-m-d H:i:s"),
								'created_by'  => $this->session->userdata('uid'),
							);
							$this->$model->insertData($tag_log, 'ret_taging_status_log');
							break;

						case 2: // Non-Tagged — lock stock row
							if (isset($items['nt_stock_id'])) {
								$this->$sales_trans_model->lock_nt_stock($items['nt_stock_id'], $insId);
							}
							break;

						case 3: // Old Gold — lock purchase row
							if (isset($items['og_purchase_id'])) {
								$this->$sales_trans_model->lock_og_item($items['og_purchase_id'], $insId);
							}
							break;

						case 4: // Sales Return — lock tag with status 12
							if (isset($items['tag_id']) && $items['tag_id'] > 0) {
								$this->$model->updateData(array('tag_status' => 4), 'tag_id', $items['tag_id'], 'ret_taging');
								$tag_log = array(
									'tag_id'	  => $items['tag_id'],
									'date'		  => $bill_date,
									'status'	  => 12,
									'from_branch' => $from_branch,
									'to_branch'	  => NULL,
									'created_on'  => date("Y-m-d H:i:s"),
									'created_by'  => $this->session->userdata('uid'),
								);
								$this->$model->insertData($tag_log, 'ret_taging_status_log');
							}
							break;

						case 5: // Partly Sold — lock tag with status 13
							if (isset($items['tag_id']) && $items['tag_id'] > 0) {
								$this->$model->updateData(array('tag_status' => 4), 'tag_id', $items['tag_id'], 'ret_taging');
								$tag_log = array(
									'tag_id'	  => $items['tag_id'],
									'date'		  => $bill_date,
									'status'	  => 13,
									'from_branch' => $from_branch,
									'to_branch'	  => NULL,
									'created_on'  => date("Y-m-d H:i:s"),
									'created_by'  => $this->session->userdata('uid'),
								);
								$this->$model->insertData($tag_log, 'ret_taging_status_log');
							}
							break;
					}
				}
			}

			$this->$model->updateData(array('tot_bill_amount' => $tot_bill_amount), 'bill_id', $insId, 'ret_billing');
		}

		if ($this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
			$this->session->unset_userdata('SALES_TRANS_FORM_SECRET');
			// Audit log: Sales Transfer created
			$this->$sales_trans_model->log_transfer_action($insId, 'create', $this->session->userdata('uid'), json_encode(array('bill_type' => 13, 'salesTransType' => $salesTransType, 'from' => $from_branch, 'to' => $to_brn, 'items' => count($req_data), 'amount' => $tot_bill_amount)));
			// Auto-generate IRN for deemed Sales Transfer
			ob_start();
			@generateEinvoice($insId);
			$irn_output = ob_get_clean();
			if (!empty($irn_output)) {
				log_message('info', 'IRN Preview (ST bill_id=' . $insId . '): ' . $irn_output);
			}
			$return_data = array('status' => TRUE, 'id' => $insId, 'message' => 'Sales Trasnfer Added Successfully..');
		} else {
			$this->db->trans_rollback();
			$return_data = array('status' => FALSE, 'id' => '', 'message' => 'Unable to proceed the requested process..');
		}
		echo json_encode($return_data);
	}


	function update_sales_transfer_request()
	{
		// print_r($_POST);exit;
		$model = "ret_billing_model";
		$sales_trans_model = "ret_sales_transfer_model";
		$from_branch            = $this->input->post('from_brn');
		$to_brn                 = $this->input->post('to_brn');
		$req_data               = $this->input->post('req_data');
		$form_secret            = $this->input->post('form_secret');

		$dCData = $this->admin_settings_model->getAllBranchDCData();
		$bill_date   = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

		$fb_entry_date = NULL;
		$tb_entry_date = NULL;
		foreach ($dCData as $dayClose) {
			if ($from_branch == $dayClose['id_branch']) {
				$fb_entry_date = $dayClose['entry_date'];
			}
			if ($to_brn == $dayClose['id_branch']) {
				$tb_entry_date = $dayClose['entry_date'];
			}
		}

		if (strtotime($tb_entry_date) < strtotime($fb_entry_date)) {
			$this->db->trans_rollback();
			$result = array('message' => 'Check day closing in to branch. From branch : ' . $fb_entry_date . ' To Branch : ' . $tb_entry_date, 'class' => 'danger', 'title' => 'Branch Transfer Approval');
			echo json_encode($result);
			exit;
		}

		foreach ($req_data as $billSale) {
			$this->db->trans_begin();

			// Mark dispatch bill as downloaded
			$status = $this->$model->updateData(array(
				"download_date" => $tb_entry_date,
				"download_by"   => $this->session->userdata('uid')
			), 'bill_id', $billSale['bill_id'], 'ret_billing');

			if ($status) {
				// Get all line items with their salesTransType
				$bill_details = $this->$sales_trans_model->get_transfer_bill_details_with_type($billSale['bill_id']);

				// Group items by salesTransType for batch processing
				$has_nt_items = false;
				foreach ($bill_details as $item) {
					$stt = intval($item['salesTransType']);

					switch ($stt) {
						case 1: // Tagged — move tag to receiving branch
							$this->$model->updateData(array("tag_status" => 0, 'current_branch' => $to_brn), 'tag_id', $item['tag_id'], 'ret_taging');
							$tag_log = array(
								"tag_id"		=> $item['tag_id'],
								"status"		=> 0,
								"from_branch"	=> $from_branch,
								"to_branch"		=> $to_brn,
								"created_by"	=> $this->session->userdata('uid'),
								"created_on"	=> date('Y-m-d H:i:s'),
								"date"			=> $tb_entry_date,
							);
							$this->$model->insertData($tag_log, 'ret_taging_status_log');
							break;

						case 2: // Non-Tagged — handled in batch below
							$has_nt_items = true;
							break;

						case 3: // Old Gold — update current_branch at receiving branch
							if ($item['old_metal_sale_id'] > 0) {
								$this->$model->updateData(array(
									'current_branch' => $to_brn,
								), 'old_metal_sale_id', $item['old_metal_sale_id'], 'ret_bill_old_metal_sale_details');
								// Insert purchase items log for old metal process
								$this->$model->insertData(array(
									'old_metal_sale_id' => $item['old_metal_sale_id'],
									'bill_id'      => $billSale['bill_id'],
									'date'         => $tb_entry_date,
									'status'       => 1,
									'item_type'    => 1,
									'from_branch'  => $from_branch,
									'to_branch'    => $to_brn,
									'created_by'   => $this->session->userdata('uid'),
									'created_on'   => date('Y-m-d H:i:s'),
								), 'ret_purchase_items_log');
							}
							break;

						case 4: // Sales Return — move tag to receiving branch (pending retagging)
							if ($item['tag_id'] > 0) {
								$this->$model->updateData(array("tag_status" => 6, 'current_branch' => $to_brn, 'is_return' => 0, 'trans_to_acc_stock' => 1), 'tag_id', $item['tag_id'], 'ret_taging');
								$tag_log = array(
									"tag_id"		=> $item['tag_id'],
									"status"		=> 6,
									"from_branch"	=> $from_branch,
									"to_branch"		=> $to_brn,
									"created_by"	=> $this->session->userdata('uid'),
									"created_on"	=> date('Y-m-d H:i:s'),
									"date"			=> $tb_entry_date,
								);
								$this->$model->insertData($tag_log, 'ret_taging_status_log');
							}
							break;

						case 5: // Partly Sold — move tag to receiving branch (pending retagging)
							if ($item['tag_id'] > 0) {
								$this->$model->updateData(array("tag_status" => 6, 'current_branch' => $to_brn, 'is_partial' => 0, 'trans_to_acc_stock' => 1), 'tag_id', $item['tag_id'], 'ret_taging');
								$tag_log = array(
									"tag_id"		=> $item['tag_id'],
									"status"		=> 6,
									"from_branch"	=> $from_branch,
									"to_branch"		=> $to_brn,
									"created_by"	=> $this->session->userdata('uid'),
									"created_on"	=> date('Y-m-d H:i:s'),
									"date"			=> $tb_entry_date,
								);
								$this->$model->insertData($tag_log, 'ret_taging_status_log');
							}
							break;
					}
				}

				// Batch process NT stock transfer
				if ($has_nt_items) {
					$this->$sales_trans_model->unlock_nt_stock_for_download($billSale['bill_id'], $to_brn);
				}

			}
		}

		if ($this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
			// Audit log: Sales Transfer downloaded
			$this->$sales_trans_model->log_transfer_action($billSale['bill_id'], 'download', $this->session->userdata('uid'), json_encode(array('bill_type' => 13, 'to_branch' => $to_brn)));
			$result = array('status' => TRUE, 'id' => $billSale['bill_id'], 'message' => 'Sales Transfer Downloaded Successfully..');
		} else {
			$this->db->trans_rollback();
			$result = array('status' => FALSE, 'id' => '', 'message' => 'Unable to proceed the requested process..');
		}
		echo json_encode($result);
	}







	function create_sales_ret_transfer()
	{
		$model = "ret_billing_model";
		$sales_trans_model = "ret_sales_transfer_model";

		$return_data            = [];
		$from_branch            = $this->input->post('from_brn');
		$to_brn                 = $this->input->post('to_brn');
		$req_data               = $this->input->post('req_data');
		$form_secret            = $this->input->post('form_secret');
		$id_metal               = ($this->input->post('id_metal') != '' ? $this->input->post('id_metal') : '');
		$remark                 = $this->input->post('remark');
		$against_bill_no        = $this->input->post('against_bill_no');

		// SRT uses per-item salesTransType from the original ST bill
		// Top-level salesTransType is only a fallback for backward compatibility

		// === Backend Validations ===
		if (empty($req_data) || !is_array($req_data)) {
			echo json_encode(array('status' => false, 'message' => 'No items selected for transfer.'));
			return;
		}
		if (empty($from_branch) || empty($to_brn)) {
			echo json_encode(array('status' => false, 'message' => 'Please select both From and To branches.'));
			return;
		}
		if ($from_branch == $to_brn) {
			echo json_encode(array('status' => false, 'message' => 'Cannot transfer to the same branch.'));
			return;
		}

		// Form Secret — prevent double submit (matches billing pattern)
		if ($this->session->userdata('SALES_RET_TRANS_FORM_SECRET')) {
			if (strcasecmp($form_secret, $this->session->userdata('SALES_RET_TRANS_FORM_SECRET')) !== 0) {
				echo json_encode(array('status' => false, 'message' => 'Duplicate submission detected. Please refresh and try again.'));
				return;
			}
		}

		$metal_rate             = $this->$model->get_branchwise_rate($from_branch);
		$from_branch_details    = $this->$sales_trans_model->get_branch_details($from_branch);
		$to_branch_details      = $this->$sales_trans_model->get_branch_details($to_brn);

		// V-02: GST Entity Validation — block same-GSTIN transfers
		$from_gst = isset($from_branch_details['gst_number']) ? trim($from_branch_details['gst_number']) : '';
		$to_gst   = isset($to_branch_details['gst_number']) ? trim($to_branch_details['gst_number']) : '';
		if ($from_gst !== '' && $to_gst !== '' && $from_gst === $to_gst) {
			echo json_encode(array('status' => false, 'message' => 'Cannot create Sales Return Transfer between branches with same GSTIN (' . $from_gst . '). Use Branch Transfer instead.'));
			return;
		}

		$ismetalReq             = $this->$sales_trans_model->getSettigsByName('is_metal_for_billing');
		if ($ismetalReq && $id_metal) {
			$metal_details = $this->$sales_trans_model->get_metal_details($id_metal);
		}

		$tot_bill_amount = 0;
		$bill_no     = $this->$model->code_number_generator($from_branch, $id_metal, 1);
		$dCData      = $this->admin_settings_model->getBranchDayClosingData($from_branch);
		$fin_year    = $this->$model->get_FinancialYear();
		$bill_date   = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
		$ref_no      = $this->$model->generateRefNo($from_branch, 's_ret_refno', $id_metal, 1);

		$data = array(
			'bill_no'           => ($ismetalReq && isset($metal_details) ? $metal_details['metal_code'] . '-' . $bill_no : $bill_no),
			'metal_type'        => ($id_metal != '' ? $id_metal : NULL),
			's_ret_refno'       => $ref_no,
			'sales_ref_no'      => (!empty($against_bill_no) ? $against_bill_no : NULL),
			'fin_year_code'     => $fin_year['fin_year_code'],
			'bill_type'         => 14, // Sales Return Transfer
			'tot_bill_amount'   => 0,
			'bill_date'         => $bill_date,
			'created_time'      => date("Y-m-d H:i:s"),
			'created_by'        => $this->session->userdata('uid'),
			'id_branch'         => $from_branch,
			'goldrate_22ct'     => $metal_rate['goldrate_22ct'],
			'silverrate_1gm'    => $metal_rate['silverrate_1gm'],
			'remark'            => ($remark != '' ? $remark : 'SALES RETURN TRANSFER'),
			'billing_for'       => 3,
			'from_branch'       => $from_branch,
			'to_branch'         => $to_brn,
			'is_credit'         => 1,
			'credit_status'     => 2,
			'bill_status'       => 1,
			'form_secret'       => $form_secret,
		);
		$this->db->trans_begin();
		$insId = $this->$model->insertData($data, 'ret_billing');
		if ($insId) {
			foreach ($req_data as $items) {

				// Resolve per-item salesTransType — each item carries its own type from the original ST bill
				$itemSalesTransType = isset($items['salesTransType']) ? intval($items['salesTransType']) : 1;

				// V-05: Rate Per Gram — for Tagged items, ensure we have a valid rate
				// "Not Against Bill" flow may not send rate_per_grm — derive from existing bill_details
				if ($itemSalesTransType == 1) {
					$rate_val = floatval(isset($items['rate_per_grm']) ? $items['rate_per_grm'] : 0);
					if ($rate_val <= 0 && isset($items['tag_id']) && $items['tag_id'] > 0) {
						// Lookup from existing ST bill_details for this tag
						$tag_rate = $this->db->query("SELECT rate_per_grm, calculation_based_on, gross_wt, net_wt, piece, product_id, design_id, purity FROM ret_bill_details WHERE tag_id = " . $this->db->escape($items['tag_id']) . " ORDER BY bill_det_id DESC LIMIT 1")->row();
						if ($tag_rate) {
							$items['rate_per_grm'] = $tag_rate->rate_per_grm;
							$items['calc_type'] = $tag_rate->calculation_based_on;
							$items['gross_wt'] = isset($items['gross_wt']) && $items['gross_wt'] > 0 ? $items['gross_wt'] : $tag_rate->gross_wt;
							$items['net_wt'] = isset($items['net_wt']) && $items['net_wt'] > 0 ? $items['net_wt'] : $tag_rate->net_wt;
							$items['piece'] = isset($items['piece']) && $items['piece'] > 0 ? $items['piece'] : $tag_rate->piece;
							$items['product_id'] = isset($items['product_id']) && $items['product_id'] > 0 ? $items['product_id'] : $tag_rate->product_id;
							$items['design_id'] = isset($items['design_id']) && $items['design_id'] > 0 ? $items['design_id'] : $tag_rate->design_id;
							$items['purity'] = isset($items['purity']) && $items['purity'] > 0 ? $items['purity'] : $tag_rate->purity;
							$rate_val = floatval($tag_rate->rate_per_grm);
						}
					}
					if ($rate_val <= 0) {
						$this->db->trans_rollback();
						echo json_encode(array('status' => false, 'message' => 'Rate per gram must be greater than 0.'));
						return;
					}
				}

				// V-04: Tag status pre-check — for SRT, tag must be ST-dispatched (status=6) or available (status=0)
				if (in_array($itemSalesTransType, [1, 4, 5]) && isset($items['tag_id']) && $items['tag_id'] > 0) {
					$tag_check = $this->db->query("SELECT tag_status FROM ret_taging WHERE tag_id = " . $this->db->escape($items['tag_id']))->row();
					if (!$tag_check || !in_array($tag_check->tag_status, [0, 6])) {
						$this->db->trans_rollback();
						$cur_status = $tag_check ? $tag_check->tag_status : 'N/A';
						echo json_encode(array('status' => false, 'message' => 'Tag #' . $items['tag_id'] . ' is not available for return transfer (current status: ' . $cur_status . ').'));
						return;
					}
				}

				// V-10: Old Metal — gross_wt must be > 0
				if ($itemSalesTransType == 3) {
					$og_gross = floatval(isset($items['gross_wt']) ? $items['gross_wt'] : 0);
					if ($og_gross <= 0) {
						$this->db->trans_rollback();
						echo json_encode(array('status' => false, 'message' => 'Old Metal gross weight must be greater than 0.'));
						return;
					}
				}

				// V-11: Partly Sold — transfer weight must not exceed available
				if ($itemSalesTransType == 5 && isset($items['tag_id']) && $items['tag_id'] > 0) {
					$ps_tag = $this->db->query("SELECT gross_wt FROM ret_taging WHERE tag_id = " . $this->db->escape($items['tag_id']))->row();
					if ($ps_tag && floatval($items['gross_wt']) > floatval($ps_tag->gross_wt)) {
						$this->db->trans_rollback();
						echo json_encode(array('status' => false, 'message' => 'Partial return weight (' . $items['gross_wt'] . 'g) exceeds available tag weight (' . $ps_tag->gross_wt . 'g).'));
						return;
					}
				}

				$total_igst = 0;
				$total_sgst = 0;
				$total_cgst = 0;

				// Non-Tagged/OG/SR/PS: use pre-calculated item_cost; Tagged: calculate from rate_per_grm
				if (in_array($itemSalesTransType, [2, 3, 4, 5])) {
					$taxable_amt = floatval(isset($items['item_cost']) ? $items['item_cost'] : 0);
					$rate_per_grm_val = (floatval($items['net_wt']) > 0) ? ($taxable_amt / floatval($items['net_wt'])) : 0;
				} else {
					$rate_per_grm_val = floatval($items['rate_per_grm']);
					if (isset($items['calc_type']) && $items['calc_type'] == 2) {
						$taxable_amt = ($items['piece'] * $items['rate_per_grm']);
					} else {
						$taxable_amt = ($items['gross_wt'] * $items['rate_per_grm']);
					}
				}
				$tax_amount  = (($taxable_amt * 3) / 100);
				$item_cost   = ($taxable_amt + $tax_amount);

				if ($from_branch_details['id_country'] == $to_branch_details['id_country']) {
					if ($from_branch_details['id_state'] == $to_branch_details['id_state']) {
						$total_sgst = number_format($tax_amount / 2, 2, ".", "");
						$total_cgst = number_format($tax_amount / 2, 2, ".", "");
					} else {
						$total_igst = $tax_amount;
					}
				} else {
					$total_igst = $tax_amount;
				}
				$tot_bill_amount += $item_cost;

				$arrayBillSales = array(
					'bill_id'       => $insId,
					'bill_type'     => 2,
					'product_id'    => isset($items['product_id']) ? $items['product_id'] : 0,
					'design_id'     => isset($items['design_id']) ? $items['design_id'] : 0,
					'tag_id'        => isset($items['tag_id']) ? $items['tag_id'] : 0,
					'purity'        => isset($items['purity']) ? $items['purity'] : 0,
					'piece'         => isset($items['piece']) ? $items['piece'] : 1,
					'less_wt'       => isset($items['less_wt']) ? $items['less_wt'] : 0,
					'net_wt'        => isset($items['net_wt']) ? $items['net_wt'] : 0,
					'gross_wt'      => $items['gross_wt'],
					'calculation_based_on' => isset($items['calculation_based_on']) ? $items['calculation_based_on'] : 1,
					'item_cost'     => $item_cost,
					'total_igst'    => $total_igst,
					'total_sgst'    => $total_sgst,
					'total_cgst'    => $total_cgst,
					'item_total_tax' => $tax_amount,
					'rate_per_grm'  => $rate_per_grm_val,
					'item_type'     => isset($items['item_type']) ? $items['item_type'] : 0,
					'salesTransType' => $itemSalesTransType,
				);
				$tagInsert = $this->$model->insertData($arrayBillSales, 'ret_bill_details');

				// Type-specific inventory lock (same pattern as Sales Transfer)
				if ($tagInsert) {
					switch ($itemSalesTransType) {
						case 1: // Tagged
							$this->$model->updateData(array('tag_status' => 4), 'tag_id', $items['tag_id'], 'ret_taging');
							$this->$model->insertData(array(
								'tag_id' => $items['tag_id'], 'date' => $bill_date, 'status' => 12,
								'from_branch' => $from_branch, 'to_branch' => NULL,
								'created_on' => date("Y-m-d H:i:s"), 'created_by' => $this->session->userdata('uid'),
							), 'ret_taging_status_log');
							break;
						case 2: // Non-Tagged
							if (isset($items['nt_stock_id'])) {
								$this->$sales_trans_model->lock_nt_stock($items['nt_stock_id'], $insId);
							}
							break;
						case 3: // Old Gold
							if (isset($items['og_purchase_id'])) {
								$this->$sales_trans_model->lock_og_item($items['og_purchase_id'], $insId);
							}
							break;
						case 4: // Sales Return
							if (isset($items['tag_id']) && $items['tag_id'] > 0) {
								$this->$model->updateData(array('tag_status' => 4), 'tag_id', $items['tag_id'], 'ret_taging');
								$this->$model->insertData(array(
									'tag_id' => $items['tag_id'], 'date' => $bill_date, 'status' => 12,
									'from_branch' => $from_branch, 'to_branch' => NULL,
									'created_on' => date("Y-m-d H:i:s"), 'created_by' => $this->session->userdata('uid'),
								), 'ret_taging_status_log');
							}
							break;
						case 5: // Partly Sold
							if (isset($items['tag_id']) && $items['tag_id'] > 0) {
								$this->$model->updateData(array('tag_status' => 4), 'tag_id', $items['tag_id'], 'ret_taging');
								$this->$model->insertData(array(
									'tag_id' => $items['tag_id'], 'date' => $bill_date, 'status' => 13,
									'from_branch' => $from_branch, 'to_branch' => NULL,
									'created_on' => date("Y-m-d H:i:s"), 'created_by' => $this->session->userdata('uid'),
								), 'ret_taging_status_log');
							}
							break;
					}
				}
			}
			// Store as negative for return transfer
			$this->$model->updateData(array('tot_bill_amount' => '-' . number_format($tot_bill_amount, 2, '.', '')), 'bill_id', $insId, 'ret_billing');
		}

		if ($this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
			$this->session->unset_userdata('SALES_RET_TRANS_FORM_SECRET');
			// Audit log: Sales Return Transfer created
			$this->$sales_trans_model->log_transfer_action($insId, 'create', $this->session->userdata('uid'), json_encode(array('bill_type' => 14, 'salesTransType' => 'mixed', 'from' => $from_branch, 'to' => $to_brn, 'items' => count($req_data), 'amount' => '-' . $tot_bill_amount)));
			// Auto-generate IRN (Credit Note) for Sales Return Transfer
			ob_start();
			@generateEinvoice($insId);
			$irn_output = ob_get_clean();
			if (!empty($irn_output)) {
				log_message('info', 'IRN Preview (SRT bill_id=' . $insId . '): ' . $irn_output);
			}
			$return_data = array('status' => TRUE, 'id' => $insId, 'message' => 'Sales Return Transfer Added Successfully..');
		} else {
			$this->db->trans_rollback();
			$return_data = array('status' => FALSE, 'id' => '', 'message' => 'Unable to proceed the requested process..');
		}
		echo json_encode($return_data);
	}


	function update_sales_ret_transfer()
	{
		$model = "ret_billing_model";
		$sales_trans_model = "ret_sales_transfer_model";
		$from_branch            = $this->input->post('from_brn');
		$to_brn                 = $this->input->post('to_brn');
		$req_data               = $this->input->post('req_data');
		$form_secret            = $this->input->post('form_secret');

		$dCData = $this->admin_settings_model->getAllBranchDCData();
		$fb_entry_date = NULL;
		$tb_entry_date = NULL;
		foreach ($dCData as $dayClose) {
			if ($from_branch == $dayClose['id_branch']) {
				$fb_entry_date = $dayClose['entry_date'];
			}
			if ($to_brn == $dayClose['id_branch']) {
				$tb_entry_date = $dayClose['entry_date'];
			}
		}

		if (strtotime($tb_entry_date) < strtotime($fb_entry_date)) {
			$result = array('status' => FALSE, 'message' => 'Check day closing. From: ' . $fb_entry_date . ' To: ' . $tb_entry_date);
			echo json_encode($result);
			exit;
		}

		foreach ($req_data as $billSale) {
			$this->db->trans_begin();

			$status = $this->$model->updateData(array(
				"download_date" => $tb_entry_date,
				"download_by"   => $this->session->userdata('uid')
			), 'bill_id', $billSale['bill_id'], 'ret_billing');

			if ($status) {
				$bill_details = $this->$sales_trans_model->get_transfer_bill_details_with_type($billSale['bill_id']);
				$has_nt_items = false;

				foreach ($bill_details as $item) {
					$stt = intval($item['salesTransType']);
					switch ($stt) {
						case 1: // Tagged
							$this->$model->updateData(array("tag_status" => 0, 'current_branch' => $to_brn), 'tag_id', $item['tag_id'], 'ret_taging');
							$this->$model->insertData(array(
								"tag_id" => $item['tag_id'], "status" => 0,
								"from_branch" => $from_branch, "to_branch" => $to_brn,
								"created_by" => $this->session->userdata('uid'),
								"created_on" => date('Y-m-d H:i:s'), "date" => $tb_entry_date,
							), 'ret_taging_status_log');
							break;
						case 2:
							$has_nt_items = true;
							break;
						case 3: // Old Gold — update current_branch at receiving branch
							if ($item['old_metal_sale_id'] > 0) {
								$this->$model->updateData(array(
									'current_branch' => $to_brn,
								), 'old_metal_sale_id', $item['old_metal_sale_id'], 'ret_bill_old_metal_sale_details');
								$this->$model->insertData(array(
									'old_metal_sale_id' => $item['old_metal_sale_id'],
									'bill_id'      => $billSale['bill_id'],
									'date'         => $tb_entry_date,
									'status'       => 1,
									'item_type'    => 1,
									'from_branch'  => $from_branch,
									'to_branch'    => $to_brn,
									'created_by'   => $this->session->userdata('uid'),
									'created_on'   => date('Y-m-d H:i:s'),
								), 'ret_purchase_items_log');
							}
							break;
						case 4: // Sales Return — pending retagging
							if ($item['tag_id'] > 0) {
								$this->$model->updateData(array("tag_status" => 6, 'current_branch' => $to_brn, 'is_return' => 0, 'trans_to_acc_stock' => 1), 'tag_id', $item['tag_id'], 'ret_taging');
								$this->$model->insertData(array(
									"tag_id" => $item['tag_id'], "status" => 6,
									"from_branch" => $from_branch, "to_branch" => $to_brn,
									"created_by" => $this->session->userdata('uid'),
									"created_on" => date('Y-m-d H:i:s'), "date" => $tb_entry_date,
								), 'ret_taging_status_log');
							}
							break;
						case 5: // Partly Sold — pending retagging
							if ($item['tag_id'] > 0) {
								$this->$model->updateData(array("tag_status" => 6, 'current_branch' => $to_brn, 'is_partial' => 0, 'trans_to_acc_stock' => 1), 'tag_id', $item['tag_id'], 'ret_taging');
								$this->$model->insertData(array(
									"tag_id" => $item['tag_id'], "status" => 6,
									"from_branch" => $from_branch, "to_branch" => $to_brn,
									"created_by" => $this->session->userdata('uid'),
									"created_on" => date('Y-m-d H:i:s'), "date" => $tb_entry_date,
								), 'ret_taging_status_log');
							}
							break;
					}
				}
				if ($has_nt_items) {
					$this->$sales_trans_model->unlock_nt_stock_for_download($billSale['bill_id'], $to_brn);
				}
				// Auto-generate Purchase Invoice
				$this->create_purchase_invoice_from_transfer($billSale['bill_id'], $from_branch, $to_brn, $tb_entry_date, $bill_details);
			}
		}

		if ($this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
			// Audit log: Sales Return Transfer downloaded
			$this->$sales_trans_model->log_transfer_action($billSale['bill_id'], 'download', $this->session->userdata('uid'), json_encode(array('bill_type' => 14, 'to_branch' => $to_brn)));
			$result = array('status' => TRUE, 'id' => $billSale['bill_id'], 'message' => 'Sales Return Transfer Downloaded & PI Generated Successfully..');
		} else {
			$this->db->trans_rollback();
			$result = array('status' => FALSE, 'id' => '', 'message' => 'Unable to proceed the requested process..');
		}
		echo json_encode($result);
	}


	/**
	 * Cancel a Sales Transfer or Sales Return Transfer bill with full inventory reversal
	 * Handles both dispatched-only (unlock) and downloaded (reverse move + cancel PI) scenarios
	 * 
	 * POST params: bill_id, cancel_reason
	 */
	function cancel_sales_transfer()
	{
		$model = "ret_billing_model";
		$sales_trans_model = "ret_sales_transfer_model";

		$bill_id       = $this->input->post('bill_id');
		$cancel_reason = $this->input->post('cancel_reason');

		if (!$bill_id) {
			echo json_encode(array('status' => FALSE, 'message' => 'Invalid bill ID'));
			return;
		}

		// Get the bill header
		$bill = $this->$sales_trans_model->get_bill_header($bill_id);
		if (!$bill) {
			echo json_encode(array('status' => FALSE, 'message' => 'Bill not found'));
			return;
		}

		if ($bill['bill_status'] == 2 || $bill['bill_status'] == 4) {
			echo json_encode(array('status' => FALSE, 'message' => 'Bill is already cancelled'));
			return;
		}

		$is_downloaded = (!empty($bill['download_date']));
		$from_branch   = $bill['from_branch'];
		$to_branch     = $bill['to_branch'];
		$bill_type     = intval($bill['bill_type']); // 13=ST, 14=SRT

		// Get all line items with type info
		$bill_details = $this->$sales_trans_model->get_transfer_bill_details_with_type($bill_id);

		$this->db->trans_begin();

		// 1. Mark bill as cancelled
		$upd_data = array(
			'bill_status'    => 2,
			'cancel_reason'  => ($cancel_reason != '' ? $cancel_reason : 'Cancelled'),
			'cancelled_date' => date("Y-m-d H:i:s"),
			'updated_time'   => date("Y-m-d H:i:s"),
			'updated_by'     => $this->session->userdata('uid'),
		);
		$this->$model->updateData($upd_data, 'bill_id', $bill_id, 'ret_billing');

		// 2. Reverse inventory for each line item based on salesTransType
		foreach ($bill_details as $item) {
			$stt = intval($item['salesTransType']);

			if ($is_downloaded) {
				// === DOWNLOADED: Reverse the tag/stock MOVE ===
				switch ($stt) {
					case 1: // Tagged — move tag back to sending branch
						if ($item['tag_id'] > 0) {
							$this->$model->updateData(
								array('tag_status' => 0, 'current_branch' => $from_branch),
								'tag_id', $item['tag_id'], 'ret_taging'
							);
							$this->$model->insertData(array(
								'tag_id' => $item['tag_id'], 'status' => 6,
								'from_branch' => $to_branch, 'to_branch' => $from_branch,
								'created_on' => date('Y-m-d H:i:s'),
								'created_by' => $this->session->userdata('uid'),
								'date' => date('Y-m-d'),
							), 'ret_taging_status_log');
						}
						break;

					case 2: // Non-Tagged — reverse the stock transfer
						$this->$sales_trans_model->reverse_nt_stock_transfer($bill_id, $from_branch);
						break;

					case 3: // Old Gold — unlock the OG item
						$this->$sales_trans_model->unlock_og_item_by_bill($bill_id);
						break;

					case 4: // Sales Return — move tag back, restore is_return flag
						if ($item['tag_id'] > 0) {
							$this->$model->updateData(
								array('tag_status' => 0, 'current_branch' => $from_branch, 'is_return' => 1),
								'tag_id', $item['tag_id'], 'ret_taging'
							);
							$this->$model->insertData(array(
								'tag_id' => $item['tag_id'], 'status' => 6,
								'from_branch' => $to_branch, 'to_branch' => $from_branch,
								'created_on' => date('Y-m-d H:i:s'),
								'created_by' => $this->session->userdata('uid'),
								'date' => date('Y-m-d'),
							), 'ret_taging_status_log');
						}
						break;

					case 5: // Partly Sold — move tag back, restore is_partial flag
						if ($item['tag_id'] > 0) {
							$this->$model->updateData(
								array('tag_status' => 0, 'current_branch' => $from_branch, 'is_partial' => 1),
								'tag_id', $item['tag_id'], 'ret_taging'
							);
							$this->$model->insertData(array(
								'tag_id' => $item['tag_id'], 'status' => 6,
								'from_branch' => $to_branch, 'to_branch' => $from_branch,
								'created_on' => date('Y-m-d H:i:s'),
								'created_by' => $this->session->userdata('uid'),
								'date' => date('Y-m-d'),
							), 'ret_taging_status_log');
						}
						break;
				}

				// Cancel the auto-generated Purchase Invoice (bill_type=15)
				$this->$sales_trans_model->cancel_linked_purchase_invoice($bill_id);

			} else {
				// === DISPATCHED ONLY: Just unlock the locked inventory ===
				switch ($stt) {
					case 1: // Tagged — restore tag_status to 0
						if ($item['tag_id'] > 0) {
							$this->$model->updateData(
								array('tag_status' => 0),
								'tag_id', $item['tag_id'], 'ret_taging'
							);
							$this->$model->insertData(array(
								'tag_id' => $item['tag_id'], 'status' => 6,
								'from_branch' => $from_branch, 'to_branch' => NULL,
								'created_on' => date('Y-m-d H:i:s'),
								'created_by' => $this->session->userdata('uid'),
								'date' => date('Y-m-d'),
							), 'ret_taging_status_log');
						}
						break;

					case 2: // Non-Tagged — unlock the locked row
						$this->$sales_trans_model->unlock_nt_stock_by_bill($bill_id);
						break;

					case 3: // Old Gold — unlock the locked OG item
						$this->$sales_trans_model->unlock_og_item_by_bill($bill_id);
						break;

					case 4: // Sales Return — restore tag_status to 0
					case 5: // Partly Sold — restore tag_status to 0
						if ($item['tag_id'] > 0) {
							$this->$model->updateData(
								array('tag_status' => 0),
								'tag_id', $item['tag_id'], 'ret_taging'
							);
							$this->$model->insertData(array(
								'tag_id' => $item['tag_id'], 'status' => 6,
								'from_branch' => $from_branch, 'to_branch' => NULL,
								'created_on' => date('Y-m-d H:i:s'),
								'created_by' => $this->session->userdata('uid'),
								'date' => date('Y-m-d'),
							), 'ret_taging_status_log');
						}
						break;
				}
			}
		}

		if ($this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
			// Audit log: Transfer cancelled
			$this->$sales_trans_model->log_transfer_action($bill_id, 'cancel', $this->session->userdata('uid'), json_encode(array('cancel_remark' => $cancel_remark)));
			$result = array('status' => TRUE, 'message' => 'Transfer cancelled and inventory reversed successfully.');
		} else {
			$this->db->trans_rollback();
			$result = array('status' => FALSE, 'message' => 'Unable to cancel. Please try again.');
		}
		echo json_encode($result);
	}


	function update_TagScan()
	{


		//print_r($_POST);exit;
		$model = "ret_sales_transfer_model";

		$from_branch            = $this->input->post('from_brn');
		$to_brn                 = $this->input->post('to_brn');
		$bill_id               = $this->input->post('bill_id');
		$tag_code            = $this->input->post('tag_code');
		$tag_id             = $this->input->post('tag_id');
		$actual_pcs       =  $this->input->post('actual_pcs');

		$dCData = $this->admin_settings_model->getAllBranchDCData();
		$bill_date   = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

		$fb_entry_date = NULL;
		$tb_entry_date = NULL;
		foreach ($dCData as $dayClose) {

			if ($from_branch == $dayClose['id_branch']) { // From Branch
				$fb_entry_date = $dayClose['entry_date'];
			}
			if ($to_brn == $dayClose['id_branch']) { // To Branch
				$tb_entry_date = $dayClose['entry_date'];
			}
		}
		//print_r($tb_entry_date);exit;
		if (strtotime($tb_entry_date) < strtotime($fb_entry_date)) {
			$this->db->trans_rollback();
			$result = array('message' => 'Check day closing in to branch. From branch : ' . $fb_entry_date . ' To Branch : ' . $tb_entry_date, 'class' => 'danger', 'title' => 'Branch Transfer Approval');
			echo json_encode($result);
			exit;
		}

		$this->$model->updateData(array("tag_status" => 0, 'current_branch' => $to_brn), 'tag_id', $tag_id, 'ret_taging');
		//print_r($this->db->last_query());exit;

		$tag_log = array(
			"tag_id"		=> $tag_id,
			"status"		=> 0,
			"from_branch"	=> $from_branch,
			"to_branch"		=> $to_brn,
			"created_by"	=> $this->session->userdata('uid'),
			"created_on"	=> date('Y-m-d H:i:s'),
			"date"			=> $tb_entry_date,
			//'form_secret'   => $form_secret,
		);

		$this->$model->insertData($tag_log, 'ret_taging_status_log');

		$tagDet = $this->$model->get_TagBilledPcs($bill_id);

		//print_r($tagDet);exit;

		if ($actual_pcs == $tagDet) {
			$this->$model->updateData(array("download_date" => $tb_entry_date, "download_by" => $this->session->userdata('uid')), 'bill_id', $bill_id, 'ret_billing');

			//print_r($this->db->last_query());exit;

			$status = 'completed';
		} else {
			$status = 'Tag Updated Successfully';
		}

		echo json_encode($status);
	}





	function update_ret_TagScan()
	{

		//print_r($_POST);exit;
		$model = "ret_sales_transfer_model";

		$from_branch      =   $this->input->post('from_brn');
		$to_brn           =   $this->input->post('to_brn');
		$bill_id          = $this->input->post('bill_id');
		$tag_code            = $this->input->post('tag_code');
		$tag_id             = $this->input->post('tag_id');
		$actual_pcs       =  $this->input->post('actual_pcs');
		$ref_bill_id     =  $this->input->post('ref_bill_id');

		$dCData = $this->admin_settings_model->getAllBranchDCData();
		$bill_date   = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

		$fb_entry_date = NULL;
		$tb_entry_date = NULL;
		foreach ($dCData as $dayClose) {

			if ($from_branch == $dayClose['id_branch']) { // From Branch
				$fb_entry_date = $dayClose['entry_date'];
			}
			if ($to_brn == $dayClose['id_branch']) { // To Branch
				$tb_entry_date = $dayClose['entry_date'];
			}
		}
		//print_r($tb_entry_date);exit;
		if (strtotime($tb_entry_date) < strtotime($fb_entry_date)) {
			$this->db->trans_rollback();
			$result = array('message' => 'Check day closing in to branch. From branch : ' . $fb_entry_date . ' To Branch : ' . $tb_entry_date, 'class' => 'danger', 'title' => 'Branch Transfer Approval');
			echo json_encode($result);
			exit;
		}

		$this->$model->updateData(array("tag_status" => 0, 'current_branch' => $to_brn), 'tag_id', $tag_id, 'ret_taging');
		//print_r($this->db->last_query());exit;

		$tag_log = array(
			"tag_id"		=> $tag_id,
			"status"		=> 0,
			"from_branch"	=> $from_branch,
			"to_branch"		=> $to_brn,
			"created_by"	=> $this->session->userdata('uid'),
			"created_on"	=> date('Y-m-d H:i:s'),
			"date"			=> $tb_entry_date,
			//'form_secret'   => $form_secret,
		);

		$this->$model->insertData($tag_log, 'ret_taging_status_log');

		$tagDet = $this->$model->get_TagBilledPcs($ref_bill_id);

		//print_r($tagDet);exit;

		if ($actual_pcs == $tagDet) {
			$this->$model->updateData(array("download_date" => $tb_entry_date, "download_by" => $this->session->userdata('uid')), 'bill_id', $bill_id, 'ret_billing');

			//print_r($this->db->last_query());exit;

			$status = 'completed';
		} else {
			$status = 'Tag Updated Successfully';
		}

		echo json_encode($status);
	}

	/**
	 * Get Purchase Items (OG + SR + PS combined) for Sales Transfer dispatch
	 * Mirrors admin_ret_brntransfer::get_purchase_items()
	 */
	function get_purchase_items()
	{
		$data = $this->ret_sales_transfer_model->get_st_purchase_items($this->input->post());
		echo json_encode($data);
	}
}
