<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

use Dompdf\Dompdf;

class Admin_ret_brntransfer extends CI_Controller
{
	const IMG_PATH  = 'assets/img/';
	const SERV_MODEL = "admin_usersms_model";
	const SETT_MOD	= "admin_settings_model";
	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->model('ret_brntransfer_model');
		$this->load->model('sms_model');
		$this->load->model('log_model');
		$this->load->model('ret_billing_model');
		$this->load->model(self::SETT_MOD);
		$this->load->model(self::SERV_MODEL);
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
	public function branch_transfer($type = "", $id = "", $s_type = "", $print_type = "")
	{
		$model = "ret_brntransfer_model";
		$SETT_MOD = self::SETT_MOD;
		switch ($type) {
			case 'approval_list':
				$data['other_issue_branch'] = $this->$model->getSettigsByName('other_issue_branch');
				$data['head_office_branch'] = $this->$model->get_headoffice_branch();
				$data['branch_transfer_download'] = $this->$model->getSettigsByName('branch_transfer_download');
				$data['is_otp_required_for_approval'] = $this->$model->getSettigsByName('is_otp_required_for_approval');
				$data['main_content'] = "branch_transfer/approval_list";
				$data['profile'] = $this->$model->get_profile_details($this->session->userdata('profile'));
				$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_brntransfer/branch_transfer/approval_list');

				$this->load->view('layout/template', $data);
				break;
			case 'list':
				$data['main_content'] = "branch_transfer/list";
				$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_brntransfer/branch_transfer/list');
				$this->load->view('layout/template', $data);
				break;
			case 'add':
				$data['other_issue_branch'] = $this->$model->getSettigsByName('other_issue_branch');
				$data['head_office_branch'] = $this->$model->get_headoffice_branch();
				$data['is_otp_required_for_approval'] = $this->$model->getSettigsByName('is_otp_required_for_approval');
				$data['main_content'] = "branch_transfer/form";
				$data['profile'] = $this->$model->get_profile_details($this->session->userdata('profile'));
				$this->load->view('layout/template', $data);
				break;
			case 'getTagsByFilter_scan':
				$data = $this->$model->fetchTagsByFilter_scan($this->input->post());
				echo json_encode($data);
				break;
			case 'download_pending':
				$list = $this->$model->get_download_data($this->input->post());
				$access = $this->admin_settings_model->get_access('admin_ret_brntransfer/branch_transfer/approval_list');
				$data = array(
					'list' => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
			case "save":
				//echo "<pre>";print_r($this->input->post());exit;
				$form_secret = $this->input->post('form_secret');
				$is_eda = $this->input->post('is_eda');
				$trans_code = $this->$model->trans_code_generator($is_eda);
				$success = 0;
				$failed = 0;
				$allow_submit = FALSE;
				if ($this->session->userdata('FORM_SECRET')) {
					if (strcasecmp($form_secret, ($this->session->userdata('FORM_SECRET'))) === 0) {
						$allow_submit = TRUE;
					}
				}
				if ($allow_submit) {
					$data = array(
						'branch_trans_code'		=> $trans_code,
						'transfer_from_branch'	=> (($this->input->post('transfer_from') !== NULL) ? $this->input->post('transfer_from') : NULL),
						'transfer_to_branch'	=> (($this->input->post('transfer_to') !== NULL) ? $this->input->post('transfer_to') : NULL),
						'transfer_item_type'	=> (($this->input->post('item_tag_type') !== NULL) ? $this->input->post('item_tag_type') : NULL),
						'pieces'				=> (($this->input->post('pieces') !== NULL) ? $this->input->post('pieces') : 0),
						'grs_wt'				=> (($this->input->post('grs_wt') !== NULL) ? $this->input->post('grs_wt') : 0),
						'net_wt'				=> (($this->input->post('net_wt') !== NULL) ? $this->input->post('net_wt') : 0),
						'is_other_issue'		=> (($this->input->post('isOtherIssue') !== NULL) ? $this->input->post('isOtherIssue') : 0),
						'create_by'				=> $this->session->userdata('uid'),
						'created_time'			=> date('Y-m-d H:i:s'),
						'is_eda'                => $is_eda,
						'remark'                => (($this->input->post('remark') !== NULL) ? $this->input->post('remark') : NULL),
						'form_secret'           => $form_secret
					);
					if ($this->input->post('item_tag_type') == 2) { // Non Tagged
						$this->db->trans_begin();

						$branch_transfer_id = $this->$model->insertData($data, 'ret_branch_transfer');
						//  print_r($this->db->last_query());exit;
						foreach ($this->input->post('trans_data') as $nt_data) {
							//  print_r($this->db->last_query());exit;
							if ($branch_transfer_id != '') {
								$item['transfer_id']       = $branch_transfer_id;
								$item['id_lot_inward_detail']	= (isset($nt_data['id_lot_inward_detail']) ? ($nt_data['id_lot_inward_detail'] == '' ? NULL : $nt_data['id_lot_inward_detail']) : NULL);
								$item['id_nontag_item']	= (isset($nt_data['id_nontag_item']) && $nt_data['id_nontag_item'] != '' ? $nt_data['id_nontag_item'] : NULL);
								$item['id_nontag_receipt']	= (isset($nt_data['id_nontag_receipt']) ? $nt_data['id_nontag_receipt'] : NULL);
								$item['pieces']	= (isset($nt_data['pieces']) ? $nt_data['pieces'] : 0);
								$item['grs_wt']	= (isset($nt_data['grs_wt']) ? $nt_data['grs_wt'] : 0);
								$item['net_wt']	= (isset($nt_data['net_wt']) ? $nt_data['net_wt'] : 0);
								$item['created_by'] = $this->session->userdata('uid');
								$item['created_date'] = date('Y-m-d H:i:s');

								$status = $this->$model->insertData($item, 'ret_brch_transfer_non_tag_items');

								$pieces =  (($this->input->post('nt_pieces') !== NULL) ? $this->input->post('nt_pieces') : 0);
								$grs_wt =  (($this->input->post('nt_grs_wt') !== NULL) ? $this->input->post('nt_grs_wt') : 0);
								$net_wt =  (($this->input->post('nt_net_wt') !== NULL) ? $this->input->post('nt_net_wt') : 0);
								$id_nontag_receipt = $item['id_nontag_receipt'];
							}
							//  print_r($this->db->last_query());exit;
							$this->$model->updateData(array('pieces' => $pieces, 'grs_wt' => $grs_wt, 'net_wt' => $net_wt, 'id_nontag_receipt' => $id_nontag_receipt), 'branch_transfer_id', $branch_transfer_id, 'ret_branch_transfer');
							if ($status) {
								$success = $success + 1;
							}
						}
					}
					// if($this->input->post('item_tag_type') == 2){ // Non Tagged
					// 	$this->db->trans_begin();
					// 	foreach($this->input->post('trans_data') as $nt_data){
					// 		$data['id_lot_inward_detail']	= (isset($nt_data['id_lot_inward_detail']) ? ($nt_data['id_lot_inward_detail'] == '' ? NULL :$nt_data['id_lot_inward_detail']) : NULL);
					// 		$data['id_nontag_item']	= (isset($nt_data['id_nontag_item']) && $nt_data['id_nontag_item'] != '' ? $nt_data['id_nontag_item'] : NULL);
					// 		$data['id_nontag_receipt']	= (isset($nt_data['id_nontag_receipt']) ? $nt_data['id_nontag_receipt'] : NULL);
					// 		$data['pieces']	= (isset($nt_data['pieces']) ? $nt_data['pieces'] : 0);
					// 		$data['grs_wt']	= (isset($nt_data['grs_wt']) ? $nt_data['grs_wt'] : 0);
					// 		$data['net_wt']	= (isset($nt_data['net_wt']) ? $nt_data['net_wt'] : 0);
					// 		$status = $this->$model->insertData($data,'ret_branch_transfer');
					// 		if($status){
					// 			$success = $success+1;
					// 		}
					// 	}
					// }
					if ($this->input->post('item_tag_type') == 1) { // Tagged
						$this->db->trans_begin();
						$data['id_lot_inward_detail'] = (($this->input->post('id_lot_inward_detail') !== NULL) ? $this->input->post('id_lot_inward_detail') : NULL);
						$branch_transfer_id = $this->$model->insertData($data, 'ret_branch_transfer');
						if ($branch_transfer_id > 0 && ($this->input->post('trans_data') !== NULL)) {
							if (sizeof($this->input->post('trans_data')) > 0) {
								$total_tag_pcs = 0;
								foreach ($this->input->post('trans_data') as $tag_data) {
									if ($tag_data['tag_id'] > 0) {
										$tagDet = $this->$model->get_tag_details($tag_data['tag_id']);
										$total_tag_pcs += $tag_data['piece'];
										$items = array(
											'transfer_id'	=> $branch_transfer_id,
											'tag_id'		=> $tag_data['tag_id'],
											'tag_remark'    => ($tag_data['tag_remark'] != '' ? $tag_data['tag_remark'] : NULL),
											'id_lot_inward_detail'		=> $tag_data['id_lot_inward_detail'],
											'id_section'		    => ($tagDet['id_section'] != '' ? $tagDet['id_section'] : NULL),
										);
										$status = $this->$model->insertData($items, 'ret_brch_transfer_tag_items');
										if ($status) {
											$success = $success + 1;
										}
									} else {
										$failed++;
									}
								}
							}
						}
					}
					if ($this->input->post('item_tag_type') == 3) {
						$this->db->trans_begin();
						$branch_transfer_id = $this->$model->insertData($data, 'ret_branch_transfer');
						foreach ($this->input->post('trans_data') as $pur_items) {
							$items = array(
								'transfer_id'       => $branch_transfer_id,
								'item_type'         => $pur_items['item_type'],
								'old_metal_sale_id' => ($pur_items['item_type'] == 1 ? $pur_items['trans_id'] : NULL), //item_type 1-Old Metal,2-Sales Return ,3-Partly Sale
								'tag_id'            => ($pur_items['tag_id'] != '' ? $pur_items['tag_id'] : NULL),
								'sold_bill_det_id'  => ($pur_items['bill_det_id'] != '' ? $pur_items['bill_det_id'] : NULL),
								'is_non_tag'        => ($pur_items['is_non_tag'] != '' ? $pur_items['is_non_tag'] : 0),
								'gross_wt'          => $pur_items['gross_wt'],
								'net_wt'            => $pur_items['net_wt'],
							);
							$status = $this->$model->insertData($items, 'ret_brch_transfer_old_metal');
							//print_r($this->db->last_query());exit;
							if ($status) {
								$success = $success + 1;
							}
						}
					} else if ($this->input->post('item_tag_type') == 4) // Packaging Items
					{
						$this->db->trans_begin();
						$branch_transfer_id = $this->$model->insertData($data, 'ret_branch_transfer');
						foreach ($this->input->post('trans_data') as $pack_items) {
							$items = array(
								'branch_transfer_id' => $branch_transfer_id,
								'id_other_inv_item' => $pack_items['id_other_item'],
								'no_of_pcs'         => $pack_items['no_of_pcs'],
							);
							$status = $this->$model->insertData($items, 'ret_branch_transfer_other_inventory');
							//print_r($this->db->last_query());exit;
							if ($status) {
								$success = $success + 1;
							}
						}
					} else if ($this->input->post('item_tag_type') == 5) { // Order
						$this->db->trans_begin();
						$fb_entry_date = NULL;
						$dCData = $this->admin_settings_model->getAllBranchDCData();
						foreach ($dCData as $dayClose) {
							if ($data['transfer_from_branch'] == $dayClose['id_branch']) { // From Branch
								$fb_entry_date = $dayClose['entry_date'];
							}
						}
						if (sizeof($this->input->post('trans_data')) > 0) {
							$insId = $this->$model->insertData($data, 'ret_branch_transfer');
							foreach ($this->input->post('trans_data') as $req_data) {
								if ($insId) {
									$insData = array(
										'status'			=> 1,
										'branch_transfer_id' => $insId,
										'date'			=> $fb_entry_date,
										'id_orderdetails' => $req_data['id_orderdetails'],
										'from_branch'	=> $this->input->post('transfer_from'),
										'to_branch'		=> $this->input->post('transfer_to'),
										'created_on'		=> date('Y-m-d H:i:s'),
										'created_by'		=> $this->session->userdata('uid'),
									);
									$status = $this->$model->insertData($insData, 'ret_bt_order_log');
								}
							}
						}
					}
					if ($this->input->post('item_tag_type') == 1) {
						if ($total_tag_pcs == $this->input->post('pieces')) {
							$allow_submit = TRUE;
						} else {
							$allow_submit = FALSE;
							$result = array("status" => 0);
							$this->session->set_flashdata('chit_alert', array('message' => 'Unable to Add Some Tags..Please Try Again', 'class' => 'danger', 'title' => 'Add to Transfer'));
						}
					}
					if ($allow_submit) {
						if ($this->db->trans_status() === TRUE) {
							$log_data = array(
								'id_log'        => $this->session->userdata('id_log'),
								'event_date'	=> date("Y-m-d H:i:s"),
								'module'      	=> 'BT Entry',
								'operation'   	=> 'Add',
								'record'        => $trans_code,
								'remark'       	=> "Trans code : " . $trans_code . " " . $this->input->post('item_tag_type') == 1 ? "Tags added to Branch Transfer" : "Non-Tag items added to Branch Transfer"
							);
							$this->log_model->log_detail('insert', '', $log_data);
							$this->db->trans_commit();
							$this->session->set_flashdata('chit_alert', array('message' => ($success > 0 ? $success . ' ' : '') . 'Records added successfully. ' . ($failed > 0 ? $failed . ' records failed to add in Branch Transfer' : ''), 'class' => 'success', 'title' => 'Add to Transfer'));
							$result = array("status" => 1, "trans_code" => $trans_code, "s_type" => $data['transfer_item_type']);
						} else {
							// BRN-103: Log DB errors server-side only — never expose last_query()/error_message() to client
							log_message('error', 'BRN-103 [save BT] DB error: ' . $this->db->_error_message() . ' | Query: ' . $this->db->last_query());
							$this->db->trans_rollback();
							$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Add to Transfer'));
							$result['status'] = 0;
						}
					}
				} else {
					$result = array("status" => 0);
					$this->session->set_flashdata('chit_alert', array('message' => 'Form Already Submitted', 'class' => 'danger', 'title' => 'Add to Transfer'));
				}
				echo json_encode($result);
				break;
			case 'updateStatus':
				// print_r($this->input->post());exit;
				$allow_submit = false;
				$form_secret = $this->input->post('form_secret');
				if ($this->session->userdata('FORM_SECRET')) {
					if (strcasecmp($form_secret, ($this->session->userdata('FORM_SECRET'))) === 0) {
						$allow_submit = TRUE;
					}
				}
				if ($allow_submit) {
					$this->db->trans_begin();
					$success = 0;
					$dCData = $this->admin_settings_model->getAllBranchDCData();
					$date = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date'] . ' ' . date("H:i:s"));
					// approval_type - 1 => Transit Approval, 2 => Stock Download
					$data = array(
						'status'			=> ($this->input->post('approval_type') == 2 ? 4 : 2),
						'updated_time'		=> date('Y-m-d H:i:s'),
					);
					if ($this->input->post('approval_type') == 1) {
						$data['approved_by'] = $this->session->userdata('uid');
						$data['approved_datetime'] = $date;
					} else {
						$data['downloaded_by'] = $this->session->userdata('uid');
						$data['dwnload_datetime'] = $date;
					}
					foreach ($this->input->post('trans_ids') as $trans) {
						$fb_entry_date = NULL;
						$tb_entry_date = NULL;
						foreach ($dCData as $dayClose) {
							if ($trans['from_branch'] == $dayClose['id_branch']) { // From Branch
								$fb_entry_date = $dayClose['entry_date'];
							}
							if ($trans['to_branch'] == $dayClose['id_branch']) { // To Branch
								$tb_entry_date = $dayClose['entry_date'];
							}
						}
						if (strtotime($tb_entry_date) < strtotime($fb_entry_date) && $this->input->post('approval_type') == 2) {
							$this->db->trans_rollback();
							$result = array('message' => 'Check day closing in to branch. From branch : ' . $fb_entry_date . ' To Branch : ' . $tb_entry_date, 'class' => 'danger', 'title' => 'Branch Transfer Approval');
							echo json_encode($result);
							exit;
						}
						if ($this->input->post('approval_type') == 1) {
							$date = ($fb_entry_date == date("Y-m-d") ? date("Y-m-d H:i:s") : $fb_entry_date . ' ' . date("H:i:s"));
							$data['approved_datetime'] = $date;
						} else {
							$date = ($tb_entry_date == date("Y-m-d") ? date("Y-m-d H:i:s") : $tb_entry_date . ' ' . date("H:i:s"));
							$data['dwnload_datetime'] = $date;
						}
						$status = $this->$model->updateData($data, 'branch_transfer_id', $trans['trans_id'], 'ret_branch_transfer');
						if ($this->input->post('trans_type') == 1) { // Tagged
							$btTags = $this->$model->getBTtags($trans['trans_id'], $this->input->post('approval_type'));
							foreach ($btTags as $tag) {
								if ($this->input->post('approval_type') == 1) { // Transit Approval
									$tag_data = array(
										'current_branch'	=> $trans['to_branch'],
										'tag_status'		=> 4,
										'updated_time'		=> date('Y-m-d H:i:s'),
									);
								} else if ($this->input->post('approval_type') == 2) { // Stock Download
									$tag_data = array(
										'current_branch'	=> $trans['to_branch'],
										'updated_time'		=> date('Y-m-d H:i:s'),
									);
									if ($this->input->post('is_other_issue') == 1) {
										$tag_data['tag_status']	= 3;
									} else {
										$tag_data['tag_status']	= 0;
									}
									$dwn_date = array(
										'download_date'	=> $tb_entry_date,
										'download_by'	=> $this->session->userdata('uid'),
										'created_on'	=> date('Y-m-d H:i:s'),
									);
									$this->$model->updateDatamulti($dwn_date, (array('transfer_id' => $trans['trans_id'])), 'ret_brch_transfer_tag_items');
								}
								$this->$model->updateData($tag_data, 'tag_id', $tag['tag_id'], 'ret_taging');
								$tag_log = array(
									"tag_id"		=> $tag['tag_id'],
									"status"		=> $tag_data['tag_status'],
									"from_branch"	=> ($tag_data['tag_status'] == 3 ? NULL : $trans['from_branch']),
									"to_branch"		=> $trans['to_branch'],
									"issuspensestock" => 0,
									"created_by"	=> $this->session->userdata('uid'),
									"created_on"	=> date('Y-m-d H:i:s'),
									"date"			=> $date,
									"form_secret"	=> $form_secret
								);
								$this->$model->insertData($tag_log, 'ret_taging_status_log');
								if ($tag['id_section'] != '') {
									$sect_log = array(
										"tag_id"		        => $tag['tag_id'],
										"status"		        => ($tag_data['tag_status'] == 3 ? 3 : 0),
										"from_branch"	        => ($tag_data['tag_status'] == 3 ? NULL : $trans['from_branch']),
										"to_branch"		        => $trans['to_branch'],
										"issuspensestock"       => 0,
										"created_by"	        => $this->session->userdata('uid'),
										"created_on"	        => date('Y-m-d H:i:s'),
										"date"	                => $date,
										"from_section"          => ($this->input->post('approval_type') == 1 ? $tag['id_section'] : NULL),
										"to_section"            => ($this->input->post('approval_type') == 2 ? $tag['id_section'] : NULL),
										"form_secret"	        => $form_secret
									);
									$this->$model->insertData($sect_log, 'ret_section_tag_status_log');
								}
							}
						}
						// else if($this->input->post('trans_type') == 2) // Non-Tagged
						// {
						//     if($this->input->post('approval_type') == 1){ // Transit Approval
						//         // Insert log record
						// 		$nontag_log = array(
						// 						'product'		=> $trans['id_product'],
						// 						'design'		=> $trans['id_design'],
						// 						'id_sub_design'	=> $trans['id_sub_design'],
						// 						'no_of_piece'	=> $trans['no_of_piece'],
						// 						'gross_wt'		=> $trans['gross_wt'],
						// 						'net_wt'		=> $trans['net_wt'],
						// 						"status"		=> 4,
						// 						"from_branch"	=> $trans['from_branch'],
						// 						"to_branch"		=> $trans['to_branch'],
						// 						"created_by"	=> $this->session->userdata('uid'),
						// 						"created_on"	=> date('Y-m-d H:i:s'),
						// 						"date"			=> $date
						// 						);
						// 		$this->$model->insertData($nontag_log,'ret_nontag_item_log');
						// 		$section_nontag_log = array(
						// 			'product'		=> $trans['id_product'],
						// 			'design'		=> $trans['id_design'],
						// 			'id_sub_design' => $trans['id_sub_design'],
						// 			'no_of_piece'	=> $trans['no_of_piece'],
						// 			'gross_wt'		=> $trans['gross_wt'],
						// 			'net_wt'		=> $trans['net_wt'],
						// 			"status"		=> 4,
						// 			"from_branch"	=> $trans['from_branch'],
						// 			"to_branch"		=> $trans['to_branch'],
						// 			"from_section"	=> $trans['id_section'],
						// 			"to_section"    => NULL,
						// 			"created_by"	=> $this->session->userdata('uid'),
						// 			"created_on"	=> date('Y-m-d H:i:s'),
						// 			"date"			=> $date
						// 			);
						// 			$this->$model->insertData($section_nontag_log,'ret_section_nontag_item_log');
						// 		//print_r($this->db->last_query());exit;
						//     }
						//     else if($this->input->post('approval_type') == 2){ // Stock Download
						//         // Check if product and design exist in NT Table
						// 		$isExist = $this->$model->checkNonTagItemExist($trans);
						// 		if($isExist['status'] == TRUE){ // UPDATE
						// 			$nt_data = array(
						// 							'gross_wt'		=> $trans['gross_wt'],
						// 							'net_wt'		=> $trans['net_wt'],
						// 							'no_of_piece'	=> $trans['no_of_piece'],
						// 							'updated_by'	=> $this->session->userdata('uid'),
						// 							'updated_on'	=> date('Y-m-d H:i:s'),
						// 						);
						// 			if($trans['id_nontag_item'] != ''){ // If BT is from ret_nontag_item Table
						// 				// Deduct in `FROM BRANCH` STOCK
						// 				$nt_data['id_nontag_item'] = $trans['id_nontag_item'];
						// 				$this->$model->updateNTData($nt_data,'-');
						// 			}
						// 			if($this->input->post('is_other_issue')==0)
						// 			{
						// 			    // Add in `To BRANCH` STOCK
						// 				$nt_data['id_nontag_item'] = $isExist['id_nontag_item'];
						// 				$nt_status = $this->$model->updateNTData($nt_data,'+');
						// 			}
						// 		}else{ // INSERT
						// 			$nt_data = array(
						// 							'branch'		=> $trans['to_branch'],
						// 							'product'		=> $trans['id_product'],
						// 							'design'		=> $trans['id_design'],
						// 							'id_sub_design'	=> $trans['id_sub_design'],
						// 							'id_section'	=> $trans['id_section'],
						// 							'no_of_piece'	=> $trans['no_of_piece'],
						// 							'gross_wt'		=> $trans['gross_wt'],
						// 							'net_wt'		=> $trans['net_wt'],
						// 							'created_by'	=> $this->session->userdata('uid'),
						// 							'created_on'	=> date('Y-m-d H:i:s'),
						// 						);
						// 			if($trans['id_nontag_item'] != ''){ // NOT Head Office
						// 				// Deduct in `FROM BRANCH` STOCK [Only if FROM BRANCH is not Head Office]
						// 				$nt_data['id_nontag_item'] = $trans['id_nontag_item'];
						// 				$nt_data['updated_by'] = $this->session->userdata('uid');
						// 				$nt_data['updated_on'] = date('Y-m-d H:i:s');
						// 				$this->$model->updateNTData($nt_data,'-');
						// 				unset($nt_data['id_nontag_item']);
						// 			}
						// 			$nt_status = $this->$model->insertData($nt_data,'ret_nontag_item');
						// 		}
						//         if( $this->input->post('is_other_issue') == 1){
						// 			$ntlog_status	= 3;
						// 		}else{
						// 			$ntlog_status	= 0;
						// 		}
						//         $nontag_log = array(
						// 						'product'		=> $trans['id_product'],
						// 						'design'		=> $trans['id_design'],
						// 						'id_sub_design'	=> $trans['id_sub_design'],
						// 						'no_of_piece'	=> $trans['no_of_piece'],
						// 						'gross_wt'		=> $trans['gross_wt'],
						// 						'net_wt'		=> $trans['net_wt'],
						// 						"status"		=> $ntlog_status,
						// 						"from_branch"	=> ($ntlog_status==3 ? NULL :$trans['from_branch']),
						// 						"to_branch"		=> $trans['to_branch'],
						// 						"created_by"	=> $this->session->userdata('uid'),
						// 						"created_on"	=> date('Y-m-d H:i:s'),
						// 						"date"			=> $date
						// 						);
						// 		$this->$model->insertData($nontag_log,'ret_nontag_item_log');
						// 		$section_nontag_log = array(
						// 			'product'		=> $trans['id_product'],
						// 			'design'		=> $trans['id_design'],
						// 			'id_sub_design' => $trans['id_sub_design'],
						// 			'no_of_piece'	=> $trans['no_of_piece'],
						// 			'gross_wt'		=> $trans['gross_wt'],
						// 			'net_wt'		=> $trans['net_wt'],
						// 			"status"		=> $ntlog_status,
						// 			"from_branch"	=> ($ntlog_status==3 ? NULL :$trans['from_branch']),
						// 			"to_branch"		=> $trans['to_branch'],
						// 			"from_section"	=> ($ntlog_status==3 ? NULL :$trans['id_section']),
						// 			"to_section"    => $trans['id_section'],
						// 			"created_by"	=> $this->session->userdata('uid'),
						// 			"created_on"	=> date('Y-m-d H:i:s'),
						// 			"date"			=> $date
						// 			);
						// 			$this->$model->insertData($section_nontag_log,'ret_section_nontag_item_log');
						//     }
						// }
						else if ($this->input->post('trans_type') == 2) // Non-Tagged
						{
							// print_r($trans);exit;
							if ($this->input->post('approval_type') == 1) { // Transit Approval
								// Insert log record
								$btnonTags = $this->$model->getBTnontags($trans['trans_id']);
								// print_r($trans);exit;
								foreach ($btnonTags as $ntag) {

									$nontag_log = array(
										'product'		=> $ntag['product'],
										'design'		=> $ntag['design'],
										'id_sub_design'	=> $ntag['id_sub_design'],
										'no_of_piece'	=> $ntag['no_of_piece'],
										'gross_wt'		=> $ntag['gross_wt'],
										'net_wt'		=> $ntag['net_wt'],
										"status"		=> 4,
										"from_branch"	=> $trans['from_branch'],
										"to_branch"		=> $trans['to_branch'],
										"created_by"	=> $this->session->userdata('uid'),
										"created_on"	=> date('Y-m-d H:i:s'),
										"date"			=> $date
									);
									$this->$model->insertData($nontag_log, 'ret_nontag_item_log');

									$section_nontag_log = array(
										'product'		=> $ntag['product'],
										'design'		=> $ntag['design'],
										'id_sub_design' => $ntag['id_sub_design'],
										'no_of_piece'	=> $ntag['no_of_piece'],
										'gross_wt'		=> $ntag['gross_wt'],
										'net_wt'		=> $ntag['net_wt'],
										"status"		=> 4,
										"from_branch"	=> $trans['from_branch'],
										"to_branch"		=> $trans['to_branch'],
										"from_section"	=> $ntag['id_section'],
										"to_section"    => NULL,
										"created_by"	=> $this->session->userdata('uid'),
										"created_on"	=> date('Y-m-d H:i:s'),
										"date"			=> $date
									);
									$this->$model->insertData($section_nontag_log, 'ret_section_nontag_item_log');
								}
								//print_r($this->db->last_query());exit;
							} else if ($this->input->post('approval_type') == 2) { // Stock Download 
								// Check if product and design exist in NT Table
								$btnonTags = $this->$model->getBTnontags($trans['trans_id']);
								foreach ($btnonTags as $ntag) {
									//  print_r($ntag);exit;
									$isExist = $this->$model->checkNonTagItemExist($ntag, $trans['to_branch']);
									if ($isExist['status'] == TRUE) { // UPDATE 
										$nt_data = array(
											'gross_wt'		=> $ntag['gross_wt'],
											'net_wt'		=> $ntag['net_wt'],
											'no_of_piece'	=> $ntag['no_of_piece'],
											'updated_by'	=> $this->session->userdata('uid'),
											'updated_on'	=> date('Y-m-d H:i:s'),
										);
										if ($ntag['id_nontag_item'] != '') { // If BT is from ret_nontag_item Table
											// Deduct in `FROM BRANCH` STOCK 
											$nt_data['id_nontag_item'] = $ntag['id_nontag_item'];
											$this->$model->updateNTData($nt_data, '-');
										}
										if ($this->input->post('is_other_issue') == 0) {
											// Add in `To BRANCH` STOCK 
											$nt_data['id_nontag_item'] = $isExist['id_nontag_item'];
											$nt_status = $this->$model->updateNTData($nt_data, '+');
										}
									} else { // INSERT
										$nt_data = array(
											'branch'		=> $trans['to_branch'],
											'product'		=> $ntag['product'],
											'design'		=> $ntag['design'],
											'id_sub_design'	=> $ntag['id_sub_design'],
											'id_section'	=> $ntag['id_section'],
											'no_of_piece'	=> $ntag['no_of_piece'],
											'gross_wt'		=> $ntag['gross_wt'],
											'net_wt'		=> $ntag['net_wt'],
											'created_by'	=> $this->session->userdata('uid'),
											'created_on'	=> date('Y-m-d H:i:s'),
										);
										if ($ntag['id_nontag_item'] != '') { // NOT Head Office
											// Deduct in `FROM BRANCH` STOCK [Only if FROM BRANCH is not Head Office]
											$nt_data['id_nontag_item'] = $ntag['id_nontag_item'];
											$nt_data['updated_by'] = $this->session->userdata('uid');
											$nt_data['updated_on'] = date('Y-m-d H:i:s');
											$this->$model->updateNTData($nt_data, '-');
											unset($nt_data['id_nontag_item']);
										}
										$nt_status = $this->$model->insertData($nt_data, 'ret_nontag_item');
									}

									if ($this->input->post('is_other_issue') == 1) {
										$ntlog_status	= 3;
									} else {
										$ntlog_status	= 0;
									}
									$nontag_log = array(
										'product'		=> $ntag['product'],
										'design'		=> $ntag['design'],
										'id_sub_design'	=> $ntag['id_sub_design'],
										'no_of_piece'	=> $ntag['no_of_piece'],
										'gross_wt'		=> $ntag['gross_wt'],
										'net_wt'		=> $ntag['net_wt'],
										"status"		=> $ntlog_status,
										"from_branch"	=> ($ntlog_status == 3 ? NULL : $trans['from_branch']),
										"to_branch"		=> $trans['to_branch'],
										"created_by"	=> $this->session->userdata('uid'),
										"created_on"	=> date('Y-m-d H:i:s'),
										"date"			=> $date
									);
									$this->$model->insertData($nontag_log, 'ret_nontag_item_log');

									$section_nontag_log = array(
										'product'		=> $ntag['product'],
										'design'		=> $ntag['design'],
										'id_sub_design' => $ntag['id_sub_design'],
										'no_of_piece'	=> $ntag['no_of_piece'],
										'gross_wt'		=> $ntag['gross_wt'],
										'net_wt'		=> $ntag['net_wt'],
										"status"		=> $ntlog_status,
										"from_branch"	=> ($ntlog_status == 3 ? NULL : $trans['from_branch']),
										"to_branch"		=> $trans['to_branch'],
										"from_section"	=> ($ntlog_status == 3 ? NULL : $trans['id_section']),
										"to_section"    => $ntag['id_section'],
										"created_by"	=> $this->session->userdata('uid'),
										"created_on"	=> date('Y-m-d H:i:s'),
										"date"			=> $date
									);
									$this->$model->insertData($section_nontag_log, 'ret_section_nontag_item_log');
								}
							}
						} else if ($this->input->post('trans_type') == 3) {
							$btOldMetals = $this->$model->getBTOldMetalDetails($trans['trans_id']);
							$bt_sales_ret = $this->$model->get_salesreturn_items($trans['trans_id']);
							$bt_partly_sale = $this->$model->get_partlysale_items($trans['trans_id']);
							//print_r($btOldMetals);exit;
							foreach ($btOldMetals as $items) {
								if ($this->input->post('approval_type') == 1) {
									//Insert to Old Metal Log
									$old_metal_log = array(
										'old_metal_sale_id' => $items['old_metal_sale_id'],
										'date'			   => $date,
										'item_type'		    => 1,
										'status'		   => 2,	// Inward
										'from_branch'	   => $trans['from_branch'],
										'to_branch'	       => NULL,
										"created_by"	   => $this->session->userdata('uid'),
										"created_on"	   => date('Y-m-d H:i:s'),
									);
									$this->$model->insertData($old_metal_log, 'ret_purchase_items_log');
									//Insert to Old Metal Log
								} else if ($this->input->post('approval_type') == 2) // Stock Download
								{
									$this->$model->updateData(array('current_branch' => $trans['to_branch'], 'is_transferred' => 1), 'old_metal_sale_id', $items['old_metal_sale_id'], 'ret_bill_old_metal_sale_details');
									//Insert to Old Metal Log
									$old_metal_log = array(
										'old_metal_sale_id' => $items['old_metal_sale_id'],
										'date'			   => $date,
										'status'		   => 1,	// Inward
										'from_branch'      => NULL,
										'item_type'		   => 1,
										'to_branch'	       => $trans['to_branch'],
										"created_by"	   => $this->session->userdata('uid'),
										"created_on"	   => date('Y-m-d H:i:s'),
									);
									$this->$model->insertData($old_metal_log, 'ret_purchase_items_log');
									//Insert to Old Metal Log
								}
							}
							//sales return items
							foreach ($bt_sales_ret as $items) {
								if ($this->input->post('approval_type') == 1) {
									if ($items['is_non_tag'] == 0) {
										$this->$model->updateData(array('trans_to_acc_stock' => 1), 'tag_id', $items['tag_id'], 'ret_taging');
										//Insert to Old Metal Log
										$old_metal_log = array(
											'tag_id'          => $items['tag_id'],
											'date'			   => $date,
											'status'		   => 2,	// Inward
											'from_branch'	   => $trans['from_branch'],
											'to_branch'	       => NULL,
											'item_type'	   => 2,
											"created_by"	   => $this->session->userdata('uid'),
											"created_on"	   => date('Y-m-d H:i:s'),
										);
										$this->$model->insertData($old_metal_log, 'ret_purchase_items_log');
									} else {
										$this->$model->updateData(array('transferred_to_acc_stock' => 1), 'bill_det_id', $items['sold_bill_det_id'], 'ret_bill_details');
										$item_log = array(
											'sold_bill_det_id' => $items['sold_bill_det_id'],
											'gross_wt'         => $items['gross_wt'],
											'net_wt'           => $items['net_wt'],
											'date'			   => $date,
											'status'		   => 2,	// intransit
											'from_branch'	   => $trans['from_branch'],
											'to_branch'	       => NULL,
											'item_type'	       => 9, //Non Tag Sales return
											"created_by"	   => $this->session->userdata('uid'),
											"created_on"	   => date('Y-m-d H:i:s'),
										);
										$this->$model->insertData($item_log, 'ret_purchase_items_log');
									}
								} else if ($this->input->post('approval_type') == 2) // Stock Download
								{
									if ($items['is_non_tag'] == 0) {
										//Insert to Old Metal Log
										$old_metal_log = array(
											'tag_id'           => $items['tag_id'],
											'date'			   => $date,
											'status'		   => 1,	// Inward
											'from_branch'      => NULL,
											'item_type'	       => 2,
											'to_branch'	       => $trans['to_branch'],
											"created_by"	   => $this->session->userdata('uid'),
											"created_on"	   => date('Y-m-d H:i:s'),
										);
										$status = $this->$model->insertData($old_metal_log, 'ret_purchase_items_log');
										if ($status) {
											$this->$model->updateData(array('current_branch' => $trans['to_branch']), 'tag_id', $items['tag_id'], 'ret_taging');
										}
									} else {
										$this->$model->updateData(array('current_branch' => $trans['to_branch']), 'bill_det_id', $items['sold_bill_det_id'], 'ret_bill_details');
										$item_log = array(
											'sold_bill_det_id' => $items['sold_bill_det_id'],
											'gross_wt'         => $items['gross_wt'],
											'net_wt'           => $items['net_wt'],
											'date'			   => $date,
											'status'		   => 1,	// intransit
											'to_branch'	       => $trans['from_branch'],
											'from_branch'	   => NULL,
											'item_type'	       => 9, //Non Tag Sales return
											"created_by"	   => $this->session->userdata('uid'),
											"created_on"	   => date('Y-m-d H:i:s'),
										);
										$this->$model->insertData($item_log, 'ret_purchase_items_log');
									}
								}
							}
							//Partly sales items
							foreach ($bt_partly_sale as $items) {
								if ($this->input->post('approval_type') == 1) {
									$this->$model->updateData(array('trans_to_acc_stock' => 1), 'tag_id', $items['tag_id'], 'ret_taging');
									//Insert to Old Metal Log
									$partly_sale_log = array(
										'sold_bill_det_id' => $items['sold_bill_det_id'],
										'gross_wt'         => $items['gross_wt'],
										'net_wt'           => $items['net_wt'],
										'tag_id'           => $items['tag_id'],
										'date'			   => $date,
										'status'		   => 2,	// intransit
										'from_branch'	   => $trans['from_branch'],
										'to_branch'	       => NULL,
										'item_type'	   => 3,
										"created_by"	   => $this->session->userdata('uid'),
										"created_on"	   => date('Y-m-d H:i:s'),
									);
									$this->$model->insertData($partly_sale_log, 'ret_purchase_items_log');
								} else if ($this->input->post('approval_type') == 2) // Stock Download
								{
									//Insert to Old Metal Log
									$partly_sale_log = array(
										'sold_bill_det_id' => $items['sold_bill_det_id'],
										'gross_wt'         => $items['gross_wt'],
										'net_wt'           => $items['net_wt'],
										'tag_id'           => $items['tag_id'],
										'date'			   => $date,
										'status'		   => 1,	// Inward
										'from_branch'      => NULL,
										'item_type'	       => 3,
										'to_branch'	       => $trans['to_branch'],
										"created_by"	   => $this->session->userdata('uid'),
										"created_on"	   => date('Y-m-d H:i:s'),
									);
									$status = $this->$model->insertData($partly_sale_log, 'ret_purchase_items_log');
									if ($status) {
										$this->$model->updateData(array('current_branch' => $trans['to_branch']), 'tag_id', $items['tag_id'], 'ret_taging');
									}
								}
							}
						} else if ($this->input->post('trans_type') == 4) {
							// Packaging Items
							$pack_items = $this->$model->get_packaging_items($trans['trans_id']);
							foreach ($pack_items as $items) {
								if ($this->input->post('approval_type') == 1) {
									$total_amount = 0;
									$inventoryItem = $this->$model->get_InventoryCategory($items['id_other_inv_item']);
									$itemDetails    = $this->$model->get_other_inventory_purchase_items_details($items['id_other_inv_item'], $trans['from_branch'], $inventoryItem['issue_preference'], $items['no_of_pcs']);
									// PAT-INV-001: Validate actual available stock before processing
									if(count($itemDetails) < $items['no_of_pcs']) {
										$this->db->trans_rollback();
										$result = array('message'=>'Insufficient stock for item. Available: '.count($itemDetails).', Requested: '.$items['no_of_pcs'],'class'=>'danger','title'=>'Branch Transfer Approval');
										echo json_encode($result);
										exit;
									}
									foreach ($itemDetails as $itemDet) {
										$total_amount += $itemDet['amount'];
										$updData = array('status' => 4);
										$this->$model->updateData($updData, 'pur_item_detail_id', $itemDet['pur_item_detail_id'], 'ret_other_inventory_purchase_items_details');
									}
									$logData = array(
										'item_id'      => $items['id_other_inv_item'],
										'no_of_pieces' => count($itemDetails),
										'date'         => $date,
										'status'       => 4,
										'amount'       => $total_amount,
										'from_branch'  => $trans['from_branch'],
										'to_branch'	   => NULL,
										'created_on'   => date("Y-m-d H:i:s"),
										'created_by'   => $this->session->userdata('uid')
									);
									$this->$model->insertData($logData, 'ret_other_inventory_purchase_items_log');
									//print_r($this->db->last_query());exit;
								} else {
									$total_amount = 0;
									$inventoryItem = $this->$model->get_InventoryCategory($items['id_other_inv_item']);
									$itemDetails    = $this->$model->get_other_inventory_download_pending_details($items['id_other_inv_item'], $trans['from_branch'], $inventoryItem['issue_preference'], $items['no_of_pcs']);
									// PAT-INV-001: Validate actual in-transit stock before processing
									if(count($itemDetails) < $items['no_of_pcs']) {
										$this->db->trans_rollback();
										$result = array('message'=>'Insufficient in-transit stock for item. Available: '.count($itemDetails).', Requested: '.$items['no_of_pcs'],'class'=>'danger','title'=>'Branch Transfer Approval');
										echo json_encode($result);
										exit;
									}
									foreach ($itemDetails as $itemDet) {
										$total_amount += $itemDet['amount'];
										$updData = array('status' => 0, 'current_branch' => $trans['to_branch']);
										$this->$model->updateData($updData, 'pur_item_detail_id', $itemDet['pur_item_detail_id'], 'ret_other_inventory_purchase_items_details');
									}
									$logData = array(
										'item_id'      => $items['id_other_inv_item'],
										'no_of_pieces' => count($itemDetails),
										'date'         => $date,
										'status'       => 0,
										'amount'       => $total_amount,
										'to_branch'    => $trans['to_branch'],
										'from_branch'  => NULL,
										'created_on'   => date("Y-m-d H:i:s"),
										'created_by'   => $this->session->userdata('uid')
									);
									$this->$model->insertData($logData, 'ret_other_inventory_purchase_items_log');
								}
							}
						} else if ($this->input->post('trans_type') == 5) //Repair Orders
						{
							$order_log = array(
								"branch_transfer_id" => $trans['trans_id'],
								"status"			=> ($this->input->post('approval_type') == 1 ? 2 : 3),
								"id_orderdetails"	=> $trans['id_orderdetails'],
								"from_branch"		=> $trans['from_branch'],
								"to_branch"			=> $trans['to_branch'],
								"created_by"		=> $this->session->userdata('uid'),
								"created_on"		=> date('Y-m-d H:i:s'),
								"date"				=> $date
							);
							$this->$model->insertData($order_log, 'ret_bt_order_log');

							$tagDetails = $this->$model->get_repair_order_tag_details($trans['id_orderdetails'], $trans['from_branch'], $trans['to_branch'], $this->input->post('approval_type'));
							if ($tagDetails['tag_id'] == '') {
								$order_data = array('current_branch' => $trans['to_branch']);
								$this->$model->updateData($order_data, 'id_orderdetails', $trans['id_orderdetails'], 'customerorderdetails');
							}
							if ($tagDetails['tag_id'] != '') {
								if ($this->input->post('approval_type') == 1) { // Transit Approval
									$tag_data = array(
										'current_branch'	=> $trans['to_branch'],
										'tag_status'		=> 4,
										'updated_time'		=> date('Y-m-d H:i:s'),
									);
									$order_data = array('current_branch' => $trans['to_branch']);
									$this->$model->updateData($order_data, 'id_orderdetails', $trans['id_orderdetails'], 'customerorderdetails');
								} else if ($this->input->post('approval_type') == 2) { // Stock Download
									$tag_data = array(
										'tag_status'		=> 0,
										'current_branch'	=> $trans['to_branch'],
										'updated_time'		=> date('Y-m-d H:i:s'),
									);
									$order_data = array('current_branch' => $trans['to_branch']);
									$this->$model->updateData($order_data, 'id_orderdetails', $trans['id_orderdetails'], 'customerorderdetails');
								}
								$this->$model->updateData($tag_data, 'tag_id', $tagDetails['tag_id'], 'ret_taging');
								$tag_log = array(
									"tag_id"		=> $tagDetails['tag_id'],
									"status"		=> $tag_data['tag_status'],
									"from_branch"	=> ($tag_data['tag_status'] == 3 ? NULL : $trans['from_branch']),
									"to_branch"		=> $trans['to_branch'],
									"issuspensestock" => 0,
									"created_by"	=> $this->session->userdata('uid'),
									"created_on"	=> date('Y-m-d H:i:s'),
									"date"			=> $date,
									"form_secret"	=> $form_secret
								);
								$this->$model->insertData($tag_log, 'ret_taging_status_log');
								if ($tagDetails['id_section'] != '') {
									$sect_log = array(
										"tag_id"		        => $tagDetails['tag_id'],
										"status"		        => $tag_data['tag_status'],
										"from_branch"	        => $trans['from_branch'],
										"to_branch"		        => $trans['to_branch'],
										"issuspensestock"       => 0,
										"created_by"	        => $this->session->userdata('uid'),
										"created_on"	        => date('Y-m-d H:i:s'),
										"date"	                => $date,
										"from_section"          => ($this->input->post('approval_type') == 1 ? $tagDetails['id_section'] : NULL),
										"to_section"            => ($this->input->post('approval_type') == 2 ? $tagDetails['id_section'] : NULL),
										"form_secret"	        => $form_secret
									);
									$this->$model->insertData($sect_log, 'ret_section_tag_status_log');
								}
							}
						}
						if ($status) {
							$success = $success + 1;
						}
					}
					if ($this->db->trans_status() === TRUE) {
						$log_data = array(
							'id_log'        => $this->session->userdata('id_log'),
							'event_date'	=> date("Y-m-d H:i:s"),
							'module'      	=> 'BT Approval',
							'operation'   	=> 'Add',
							'record'        => NULL,
							'remark'       	=> $this->input->post('approval_type') == 1 ? "Status updated as In-Transit" : "Stock downloaded Successfully"
						);
						$this->log_model->log_detail('insert', '', $log_data);
						$this->db->trans_commit();
						$this->session->set_flashdata('chit_alert', array('icon' => "icon fa fa-close", 'message' => 'Records updated successfully', 'class' => 'success', 'title' => 'Branch Transfer Approval'));
						$result = array('message' => ($success > 0 ? $success . ' ' : '') . 'Records updated successfully', 'class' => 'success', 'title' => 'Branch Transfer Approval');
					} else {
						// BRN-103: Log DB errors server-side only — never expose last_query()/error_message() to client
						log_message('error', 'BRN-103 [updateStatus] DB error: ' . $this->db->_error_message() . ' | Query: ' . $this->db->last_query());
						$this->db->trans_rollback();
						$this->session->set_flashdata('chit_alert', array('icon' => "icon fa fa-close", 'message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Branch Transfer Approval'));
						$result = array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Branch Transfer Approval');
					}
				} else {
					$result = array("status" => 0);
					$this->session->set_flashdata('chit_alert', array('message' => 'Form Already Submitted', 'class' => 'danger', 'title' => 'Add to Transfer'));
				}
				echo json_encode($result);
				break;
			case 'update_TagsByFilter_scan':
				$allow_submit = false;
				$form_secret = $this->input->post('form_secret');
				if ($this->session->userdata('FORM_SECRET')) {
					if (strcasecmp($form_secret, ($this->session->userdata('FORM_SECRET'))) === 0) {
						$allow_submit = TRUE;
					}
				}
				if ($allow_submit) {
					$bt_completed = false;
					$dCData = $this->admin_settings_model->getAllBranchDCData();
					$status = $this->$model->get_scan_tag_status($this->input->post('branch_trans_code'), $this->input->post('tag_code'));
					if ($status['download_date'] == '' && $status['tag_status'] == 4) {
						$fb_entry_date = NULL;
						$tb_entry_date = NULL;
						foreach ($dCData as $dayClose) {
							if ($this->input->post('from_branch') == $dayClose['id_branch']) { // From Branch
								$fb_entry_date = $dayClose['entry_date'];
							}
							if ($this->input->post('to_branch') == $dayClose['id_branch']) { // To Branch
								$tb_entry_date = $dayClose['entry_date'];
							}
						}
						if (strtotime($tb_entry_date) < strtotime($fb_entry_date) && $this->input->post('approval_type') == 2) {
							$result = array('message' => 'Check day closing in to branch. From branch : ' . $fb_entry_date . ' To Branch : ' . $tb_entry_date, 'class' => 'danger', 'title' => 'Branch Transfer Approval');
							echo json_encode($result);
							exit;
						}
						if ($this->input->post('trans_type') == 1) { // Tagged
							if ($this->input->post('approval_type') == 1) {
								$date = ($fb_entry_date == date("Y-m-d") ? date("Y-m-d H:i:s") : $fb_entry_date . ' ' . date("H:i:s"));
								$data['approved_datetime'] = $date;
							} else {
								$date = ($tb_entry_date == date("Y-m-d") ? date("Y-m-d H:i:s") : $tb_entry_date . ' ' . date("H:i:s"));
								$data['dwnload_datetime'] = $date;
							}
							if ($this->input->post('approval_type') == 2) { // Stock Download
								$tag_data = array(
									'current_branch'	=> $this->input->post('to_branch'),
									'updated_time'		=> date('Y-m-d H:i:s'),
								);
								if ($this->input->post('is_other_issue') == 1) {
									$tag_data['tag_status']	= 3;
								} else {
									$tag_data['tag_status']	= 0;
								}
								$dwn_date = array(
									'download_date'	=> $tb_entry_date,
									'download_by'	=> $this->session->userdata('uid'),
									'created_on'	=> date('Y-m-d H:i:s'),
								);
								$this->db->trans_begin();
								$tag_update_status = $this->$model->updateData($tag_data, 'tag_id', $status['tag_id'], 'ret_taging');
								if ($tag_update_status) {
									$this->$model->updateDatamulti($dwn_date, (array('transfer_id' => $status['trans_id'], 'tag_id' => $status['tag_id'])), 'ret_brch_transfer_tag_items');
									$tag_log = array(
										"tag_id"		=> $status['tag_id'],
										"status"		=> $tag_data['tag_status'],
										"from_branch"   => ($tag_data['tag_status'] == 3 ? NULL : $this->input->post('from_branch')),
										"to_branch"		=> $this->input->post('to_branch'),
										"created_by"	=> $this->session->userdata('uid'),
										"created_on"	=> date('Y-m-d H:i:s'),
										"date"			=> $date,
										"form_secret"   => $form_secret
									);
									$ins_id = $this->$model->insertData($tag_log, 'ret_taging_status_log');
									if ($status['id_section'] != '') {
										$sect_log = array(
											"tag_id"		=> $status['tag_id'],
											"status"		=> 0,
											"from_branch"	=> ($tag_data['tag_status'] == 3 ? NULL : $this->input->post('from_branch')),
											"to_branch"		=>  $this->input->post('to_branch'),
											"created_by"	        => $this->session->userdata('uid'),
											"created_on"	        => date('Y-m-d H:i:s'),
											"date"	                => $date,
											"from_section"          => NULL,
											"to_section"            => $status['id_section'],
											"form_secret"   => $form_secret
										);
										$this->$model->insertData($sect_log, 'ret_section_tag_status_log');
									}
									$btDetail = $this->$model->getBTDetail($status['trans_id']);
									//echo"<pre>"; print_r($btDetail);exit;
									if ($btDetail['pieces'] == ($btDetail['downd_pcs'])) {
										$update_data = array(
											'status' => 4,
											'dwnload_datetime' =>  $date
										);
										$done_dnload = $this->$model->updateData($update_data, 'branch_transfer_id', $status['trans_id'], 'ret_branch_transfer');
										$bt_completed = true;
									}
								}
							}
						}
						if ($this->db->trans_status() === TRUE) {
							$log_data = array(
								'id_log'        => $this->session->userdata('id_log'),
								'event_date'	=> date("Y-m-d H:i:s"),
								'module'      	=> 'BT Download',
								'operation'   	=> 'Add',
								'record'        => NULL,
								'remark'       	=> "Stock downloaded Successfully"
							);
							$this->log_model->log_detail('insert', '', $log_data);
							$this->db->trans_commit();
							$result = array('message' => 'Records updated successfully', 'status' => true, 'bt_status' => $bt_completed, "tag_details" => $status);
						} else {
							$this->db->trans_rollback();
							$result = array('message' => 'Unable to proceed the requested process', 'status' => false);
						}
					} else {
						$result = array('status' => false, 'message' => 'Tag already Downloaded..');
					}
					echo json_encode($result);
				} else {
					$result = array("status" => 0);
					$this->session->set_flashdata('chit_alert', array('message' => 'Form Already Submitted', 'class' => 'danger', 'title' => 'Add to Transfer'));
				}
				break;
			case 'getLotsByBranch':
				$data = $this->$model->getLotsByFilter($this->input->post());
				echo json_encode($data);
				break;
			case 'getDesignByFilter':
				$data = $this->$model->getDesignByFilter($this->input->post());
				//print_r($this->db->last_query());exit;
				echo json_encode($data);
				break;
			case 'getTagsByFilter':
				$data = $this->$model->fetchTagsByFilter($this->input->post());
				echo json_encode($data);
				break;
			case 'getEstiTagsByFilter':
				$data = $this->$model->fetchEstiTagsByFilter($this->input->post());
				echo json_encode($data);
				break;
			case 'getNonTaggedItem':
				$data = $this->$model->fetchNonTaggedItems($this->input->post());
				echo json_encode($data);
				break;
			case 'getNonTaggedReceiptedItem':
				$data = $this->$model->fetchNonTaggedReceiptedItems($this->input->post());
				echo json_encode($data);
				break;
			case 'getRepairOrderDetails':
				$data = $this->$model->getRepairOrderDetails($this->input->post());
				echo json_encode($data);
				break;
			case 'print':
				$trans_code = $id;
				$this->load->model('admin_settings_model');
				$data['type'] = $print_type; //1- Summary Print ,2-Detailed Print
				$data['btrans'] = $this->$model->getBTransData($trans_code, $s_type, $print_type);
				$data['btrans_summary'] = ($s_type == 1 ? $this->$model->getBTransDataSummary($trans_code, $s_type, $print_type) : []);
				$data['purchase_item_details'] = $this->$model->get_purchase_items_details($trans_code, $s_type, $print_type);
				// $data['comp_details'] = $this->admin_settings_model->get_company();
				// echo "<pre>";print_r($data);exit;
				/*if($this->session->userdata('branch_settings') == 1){
						 $data['comp_details'] = $this->admin_settings_model->get_branchcompany($data['records'][0]['id_branch']);
					}else{
						 $data['comp_details'] = $this->admin_settings_model->get_company();
					} */
				// echo "<pre>";print_r($data);exit;
				$data['comp_details'] = $this->admin_settings_model->get_company();
				//create PDF receipt
				$this->load->helper(array('dompdf', 'file'));
				$dompdf = new DOMPDF();
				// $html = $this->load->view('branch_transfer/bt_category_print', $data, true);
				// exit;
				//  echo "<pre>";print_r($print_type);exit;
				if ($print_type == 3) {
					
						$html = $this->load->view('branch_transfer/bt_category_print', $data, true);
					
				} else {
					
					$html = $this->load->view('branch_transfer/print', $data, true);
					if ($this->session->userdata('uid') == '1') {
						// echo $html;exit;
					}
				}
				$dompdf->load_html($html);
				
				$dompdf->set_paper("a4", "portriat");
				
				$dompdf->render();
				
				while(ob_get_level()) ob_end_clean();
				$dompdf->stream("btrans.pdf", array('Attachment' => 0));
				// exit;
				break;
			case 'approval_pending':
				$list = $this->$model->getApprovalListing($this->input->post());
				$access = $this->admin_settings_model->get_access('admin_ret_brntransfer/branch_transfer/list');
				$data = array(
					'list' => $list,
					'access' => $access
				);
				echo json_encode($data);
				break;
			default:
				$from_date = $this->input->post('from_date');
				$to_date = $this->input->post('to_date');
				$list = $this->$model->get_ajaxBranchTransferlist($from_date, $to_date);
				$profile = $this->$model->get_profile_details($this->session->userdata('profile'));
				$data = array('list' => $list, 'profile' => $profile);
				echo json_encode($data);
				break;
		}
	}
	function verify_otp()
	{
		$model	=	"ret_tag_model";
		$post_otp	 = $this->input->post('otp');
		$session_otp = $this->session->userdata('bt_approval_otp');
		if ($session_otp == $post_otp) {
			if (time() >= $this->session->userdata('bt_approval_otp_exp')) {
				$this->session->unset_userdata('bt_approval_otp');
				$this->session->unset_userdata('bt_approval_otp_exp');
				$status = array('status' => false, 'msg' => 'OTP has been expired');
			} else {
				$status = array('status' => true, 'msg' => 'OTP Verified successfully.Proceed Approval.');
			}
		} else {
			$status	= array('status' => false, 'msg' => 'Please Enter Valid OTP');
		}
		echo json_encode($status);
	}
	function send_otp()
	{
		//$branch		= ($this->session->userdata('id_branch') == '' || $this->session->userdata('id_branch') == 0 ? 1 : $this->session->userdata('id_branch'));
		$branch		= $this->input->post('id_branch');
		$mobile     = $this->ret_brntransfer_model->get_verifMobNo($branch);
		$service = $this->admin_settings_model->get_service_by_code('bt_trans');
		$sent_otp	= '';
		if ($mobile) {
			$this->db->trans_begin();
			$this->session->unset_userdata("bt_approval_otp");
			$this->session->unset_userdata("bt_approval_otp_exp");
			$OTP = mt_rand(100001, 999999);
			$sent_otp .= $OTP;
			$this->session->set_userdata('bt_approval_otp', $sent_otp);
			$this->session->set_userdata('bt_approval_otp_exp', time() + 60);
			$message = "Hi, Your OTP For Branch Transfer Approval is :" . $OTP . ". Will expire within 1 minute.";
			$otp_gen_time = date("Y-m-d H:i:s");
			$insData = array(
				'mobile'		=>	$mobile,
				'otp_code'		=>	$OTP,
				'otp_gen_time'	=>	date("Y-m-d H:i:s"),
				'module'		=>	'Branch Transfer Approval',
				'send_resend'	=>	1,
				'id_emp'		=>	$this->session->userdata('uid')
			);
			$insId = $this->ret_brntransfer_model->insertData($insData, 'otp');
			if ($insId) {
				$send_sms = $this->send_sms($mobile, $message, $service['dlt_te_id']);
				//$this->admin_usersms_model->send_whatsApp_message($mobile,$message);
				//$send_sms = TRUE;
				if ($send_sms) {
					$this->db->trans_commit();
					$status = array('status' => true, 'msg' => 'OTP sent Successfully', 'OTP' => '');
				} else {
					$this->db->trans_rollback();
					$status = array('status' => false, 'msg' => 'Unabe To Send Try Again');
				}
			} else {
				$status = array('status' => false, 'msg' => 'Unabe To Send Try Again');
			}
		} else {
			$status = array('status' => false, 'msg' => 'Mobile number is empty');
		}
		echo json_encode($status);
	}
	function send_sms($mobile, $message, $dlt_te_id)
	{
		if ($this->config->item('sms_gateway') == '1') {
			$status = $this->sms_model->sendSMS_MSG91($mobile, $message, "", $dlt_te_id);
		} elseif ($this->config->item('sms_gateway') == '2') {
			$status = $this->sms_model->sendSMS_Nettyfish($mobile, $message, 'trans');
		}
		return $status;
	}
	function bt_get_branches()
	{
		$branches = $this->ret_brntransfer_model->getBTBranches();
		echo json_encode($branches);
	}
	/*function nonTagStock($type="",$id="",$s_type=""){
		$model = "ret_brntransfer_model";
		switch($type)
		{
			case 'updateStock':
					if($this->db->trans_status() === TRUE)
					{
						$this->db->trans_commit();
						$this->session->set_flashdata('chit_alert',array('message'=> ($success >0?$success.' ':'').'Records added successfully','class'=>'success','title'=>'Add to Transfer'));
						$result = array( "status" => 1, "trans_code" => $trans_code, "s_type" => $data['transfer_item_type']);
					}
					else
					{
						$this->db->trans_rollback();
						$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Add to Transfer'));
						$result['status'] = 0;
					}
					echo json_encode($result);
				break;
			case 'get':
					$list = $this->$model->getApprovalListing($this->input->post());
				  	$access = $this->admin_settings_model->get_access('admin_ret_brntransfer/branch_transfer/list');
			        $data = array(
			        					'list' => $list,
										'access' => $access
			        			 );
					echo json_encode($data);
				break;
			default:
					$data['main_content'] = "branch_transfer/non_tag/form";
					$this->load->view('layout/template', $data);
				break;
		}
	}*/
	function update_branch_transfer_cancel()
	{
		$model = "ret_brntransfer_model";
		$reqdata   = $this->input->post('req_data');
		$this->db->trans_begin();
		foreach ($reqdata as $trans) {
			$data = array(
				'status'  	  => 3,
				'updated_time' => date("Y-m-d H:i:s"),
				'updated_by'   => $this->session->userdata('uid')
			);
			$status = $this->$model->updateData($data, 'branch_transfer_id', $trans['branch_transfer_id'], 'ret_branch_transfer');
			if ($status) {
				$log_data = array(
					'id_log'        => $this->session->userdata('id_log'),
					'event_date'    =>  date("Y-m-d H:i:s"),
					'module'        => 'Branch Trasnfer',
					'operation'     => 'Cancel',
					'record'        =>  $trans['branch_transfer_id'],
					'remark'        => 'Reject Branch Trasnfer'
				);
				$this->log_model->log_detail('insert', '', $log_data);
			}
		}
		if ($this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
			$this->session->set_flashdata('chit_alert', array('message' => 'Branch Transfer Rejected successfully.', 'class' => 'success', 'title' => 'Branch Trasnfer'));
		} else {
			$this->db->trans_rollback();
			$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Branch Trasnfer'));
		}
	}
	//Other issue otp
	function send_other_issue_otp()
	{
		$model = "ret_brntransfer_model";
		$data = $this->$model->getSettigsByName('otp_approval_nos');
		$mobile_num  = array(explode(',', $data));
		$sent_otp = '';
		//print_r($mobile_num);exit;
		foreach ($mobile_num[0] as $mobile) {
			if ($mobile) {
				$this->db->trans_begin();
				$this->session->unset_userdata("others_issue_otp");
				$OTP = mt_rand(100001, 999999);
				$sent_otp .= $OTP . ',';
				$this->session->set_userdata('others_issue_otp', $sent_otp);
				$this->session->set_userdata('others_issue_otp_exp', time() + 60);
				$message = "Hi Your OTP  For Other Issue is :  " . $OTP . " Will expire within 1 minute.";
				$otp_gen_time = date("Y-m-d H:i:s");
				$insData = array(
					'mobile' => $mobile,
					'otp_code' => $OTP,
					'otp_gen_time' => date("Y-m-d H:i:s"),
					'module' => 'Duplicate Tag Edit',
					'id_emp' => $this->session->userdata('uid')
				);
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
	function verify_other_issue_otp()
	{
		$model = "ret_brntransfer_model";
		$post_otp = $this->input->post('otp');
		$session_otp = $this->session->userdata('others_issue_otp');
		$otp = array(explode(',', $session_otp));
		// BRN-102 fix: trans_begin() moved to wrap only the DB write (updateData),
		// not the session-check logic. All failure paths now call trans_rollback().
		if ($post_otp != '') {
			foreach ($otp[0] as $OTP) {
				if ($OTP == $post_otp) {
					if (time() >= $this->session->userdata('others_issue_otp_exp')) {
						$this->session->unset_userdata('others_issue_otp');
						$this->session->unset_userdata('others_issue_otp_exp');
						$status = array('status' => false, 'msg' => 'OTP has been expired');
					} else {
						// BRN-102: transaction wraps the DB write only
						$this->db->trans_begin();
						$updData = array('is_verified' => 1, 'verified_time' => date("Y-m-d H:i:s"));
						$updStatus = $this->$model->updateData($updData, 'otp_code', $post_otp, 'otp');
						if ($this->db->trans_status() === FALSE || !$updStatus) {
							$this->db->trans_rollback();
							$status = array('status' => false, 'msg' => 'Unable to verify OTP. Please try again.');
						} else {
							$this->db->trans_commit();
							$status = array('status' => true, 'msg' => 'OTP Verified Successfully.');
						}
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
	//Other issue otp
	//Old Metal Transfer Details
	function get_purchase_items()
	{
		$data = $this->ret_brntransfer_model->get_purchase_items($this->input->post());
		echo json_encode($data);
	}
	//Old Metal Transfer Details
	function getNonTagReceiptedLots()
	{
		$model = "ret_brntransfer_model";
		$data = $this->$model->getNonTagReceiptedLots($this->input->post());
		echo json_encode($data);
	}
}
