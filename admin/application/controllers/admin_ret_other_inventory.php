<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(APPPATH . 'libraries/dompdf/autoload.inc.php');

use Dompdf\Dompdf;

class Admin_ret_other_inventory extends CI_Controller
{
	const VIEW_FOLDER = 'other_inventory/';
	const OTHER_INVENTORY_MODEL = 'ret_other_inventory_model';
	const SETT_MOD = 'admin_settings_model';
	const IMG_PATH  = 'assets/img/';
	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->model(self::OTHER_INVENTORY_MODEL);
		$this->load->model("admin_settings_model");
		$this->load->model("log_model");
		$this->load->model("admin_settings_model");
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
	function set_image_other($id, $skuid)
	{
		// print($id);exit;
		$model = self::OTHER_INVENTORY_MODEL;
		if ($_FILES['other']['name']['other_item_img']) {
			$path = 'assets/img/other_inventory/' . $skuid;
			if (!is_dir($path)) {
				mkdir($path, 0777, TRUE);
			}
			$img = $_FILES['other']['tmp_name']['other_item_img'];
			$filename = time() . ".jpg";
			$imgpath = $path . '/' . $filename;
			$upload = $this->upload_img('image', $imgpath, $img);
			$data['item_image'] = $filename;
			//print_r($data['image']);exit;
			//$this->$model->updateData("update",$id['ID'],$data);
			$id = $this->$model->updateData($data, 'id_other_item', $id, 'ret_other_inventory_item');
		}
	}
	// function upload_img( $outputImage,$dst, $img)
	// {	
	// if (($img_info = getimagesize($img)) === FALSE)
	// {
	// 	// die("Image not found or not an image");
	// 	return false;
	// }
	// $width = $img_info[0];
	// $height = $img_info[1];
	// switch ($img_info[2]) {
	//   case IMAGETYPE_GIF  : $src = imagecreatefromgif($img);
	//   						$tmp = imagecreatetruecolor($width, $height);
	//   						$kek=imagecolorallocate($tmp, 255, 255, 255);
	// 			      		imagefill($tmp,0,0,$kek);
	//   						break;
	//   case IMAGETYPE_JPEG : $src = imagecreatefromjpeg($img); 
	//   						$tmp = imagecreatetruecolor($width, $height);
	//  						break;
	//   case IMAGETYPE_PNG  : $src = imagecreatefrompng($img);
	// 					    $tmp = imagecreatetruecolor($width, $height);
	//   						$kek=imagecolorallocate($tmp, 255, 255, 255);
	// 			     		imagefill($tmp,0,0,$kek);
	// 			     		break;
	//   default : //die("Unknown filetype");	
	//   return false;
	//   }		
	//   imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
	//   imagejpeg($tmp, $dst, 60);
	// }
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
	//Item Type
	public function other_inventory($type = "", $id = "")
	{
		$model = self::OTHER_INVENTORY_MODEL;
		switch ($type) {
			case "add":
				$data['main_content'] = "other_inventory/form";
				$this->load->view('layout/template', $data);
				break;
			case 'list':
				$data['main_content'] = "other_inventory/list";
				$this->load->view('layout/template', $data);
				break;
			case 'save':
				//  print_r($_POST);exit;
				$addData = $_POST['other'];
				$pieces = $_POST['pieces'];
				// var_dump(sizeof($pieces) > 0);exit;
				//  print_r($pieces.'hi');exit;
				$data = array(
					'name'            => strtoupper($addData['name']),
					'id_inv_size'     => ($addData['id_size'] != '' ? $addData['id_size'] : NULL),
					'stock_id_uom'    => ($addData['id_uom'] != '' ? $addData['id_uom'] : NULL),
					'item_for'        => $addData['item_for'],
					'issue_to'        => $addData['issue_to'],
					'issue_preference' => $addData['issue_preference'],
					'unit_price'     => $addData['unit_price'],
					'created_on'	    => date("Y-m-d H:i:s"),
					'created_by'      => $this->session->userdata('uid'),
					
				);
				$this->db->trans_begin();
				
				$id_other_item = $this->$model->insertData($data, 'ret_other_inventory_item');
				if ($id_other_item) {
					if (sizeof($pieces) > 0) {
						foreach ($pieces as $items) {
							$insdata = array(
								'id_branch'	            => $items['id_branch'],
								'id_other_item'	        => $id_other_item,
								'max_pcs'	            => $items['max_pcs'],
								'min_pcs'	            => $items['min_pcs'],
							);
							$this->$model->insertData($insdata, 'ret_other_inventory_reorder_settings');
						}
					}
					$this->$model->update_other_inventory(array('sku_id' => $id_other_item), $id_other_item, 'id_other_item', 'ret_other_inventory_item');
				}
				//Adding scheme map for gift code starts
				if ($id_other_item) {
					// $selected_scheme=$_POST['scheme_select'];
					$selected_schemes = $_POST['scheme_select'];
					$quantity = $_POST['quantity'];
					$fromins = $_POST['tenurefrom'];
					$toins = $_POST['tenureto'];
					// getting number of rows of scheme table 
					$table_length = $this->input->post('table_length');
					if ($table_length != '') {
						for ($i = 0; $i <= $table_length; $i++) {
							foreach ($selected_schemes[$i] as $selectedSchemeId) {
								if (!empty($selectedSchemeId) && !empty($quantity[$i])) {
									$data = array(
										'id_other_item'        => $id_other_item,
										'id_scheme'            => $selectedSchemeId,
										'item_issue_limit'     => $quantity[$i],
										'date_add'             => date("Y-m-d H:i:s"),
										'from_ins'             => $fromins[$i],
										'to_ins'               => $toins[$i],
										'created_by'           => $this->session->userdata('uid'),
									);
									$insert_gift_map = $this->$model->insertData($data, 'gift_mapping');
									// print_r($this->db->last_query());exit;
								}
							}
						}
						// for($i=1;$i<=$table_length;$i++)
						// {
						// 	if($selected_scheme[$i]!='' && $quantity[$i]!='')
						// 	{
						// 		$data=array(
						// 			'id_other_item '		=> $id_other_item,
						// 			'id_scheme '           	=> $selected_scheme[$i],
						// 			'item_issue_limit '    	=> $quantity[$i],
						// 			'date_add'         	   	=> date("Y-m-d H:i:s"),
						// 			'from_ins'              => $fromins[$i],
						// 			'to_ins'                => $toins[$i],  
						// 			'created_by'         	=> $this->session->userdata('uid'),
						// 			);
						// 		$insert_gift_map = $this->$model->insertData($data,'gift_mapping');
						// 	}
						// }
					}
				}
				$addData['sku_id'] = $id_other_item;
				//Adding scheme map for gift code ends
				// print_r($_FILES['other']);exit;
				if (isset($_FILES['other']['name']['other_item_img'])) {
					// print_r($id_other_item);exit;
					if ($id_other_item > 0) {
						// print_r($addData);exit;
						
						$this->set_image_other($id_other_item, $addData['sku_id']);
					}
				}
				$this->load->library('phpqrcode/qrlib');
				$SERVERFILEPATH = 'other_product_qrcode';
				if (!is_dir($SERVERFILEPATH)) {
					mkdir($SERVERFILEPATH, 0777, TRUE);
				}

				// print_r($addData['sku_id']);exit;
				$folder = $SERVERFILEPATH;
				$file_name1 = time() . $addData['sku_id'] . ".png";
				$src = time() . $addData['sku_id'];
				$file_name = $SERVERFILEPATH . '/' . $file_name1;
				// print_r($file_name);exit;
				QRcode::png($addData['sku_id'], $file_name);
				$item = $this->$model->update_other_inventory(array('qr_image' => $src), $id_other_item, 'id_other_item', 'ret_other_inventory_item');
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$this->session->set_flashdata('chit_alert', array('message' => 'Item added successfully', 'class' => 'success', 'title' => 'New Item'));
				} else {
					$this->db->trans_rollback();
					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'New Item'));
				}
				redirect('admin_ret_other_inventory/other_inventory/list');
				break;
			case "edit":
				$data['other']              = $this->$model->get_other_inventory_records($id);
				$data['reorder_details']    = $this->$model->get_inv_item_reorder_details($id);
				//  echo "<pre>";print_r($data);exit;
				//Adding scheme map for gift code starts
				$data['chit_gift']    		= $this->$model->get_inv_chit_gift($id);
				//Adding scheme map for gift code ends
				$data['main_content'] = "other_inventory/form";
				$this->load->view('layout/template', $data);
				break;
			case 'delete':
				$this->db->trans_begin();
				$this->$model->deleteData('id_other_item', $id, 'ret_other_inventory_item');
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$this->session->set_flashdata('chit_alert', array('message' => 'Item deleted successfully', 'class' => 'success', 'title' => 'Other Item'));
				} else {
					$this->db->trans_rollback();
					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Other Item'));
				}
				redirect('admin_ret_other_inventory/other_inventory/list');
				break;
			case "update":
				$addData = $_POST['other'];
				$pieces = $_POST['pieces'];
				$inv_data = $this->$model->get_other_inventory_records($id);
				$data = array(
					'name'            => strtoupper($addData['name']),
					'id_inv_size'    => ($addData['id_size'] != '' ? $addData['id_size'] : NULL),
					'item_for'        => $addData['item_for'],
					'issue_preference' => $addData['issue_preference'],
					'updated_on'	    => date("Y-m-d H:i:s"),
					'unit_price'     => $addData['unit_price'],
					'updated_by'    => $this->session->userdata('uid')
				);
				$this->db->trans_begin();
				$item = $this->$model->update_other_inventory($data, $id, 'id_other_item', 'ret_other_inventory_item');
				if ($item) {
					if (sizeof($pieces) > 0) {
						$this->$model->deleteData('id_other_item', $id, 'ret_other_inventory_reorder_settings');
						foreach ($pieces as $items) {
							$insdata = array(
								'id_branch'	            => $items['id_branch'],
								'id_other_item'	        => $id,
								'max_pcs'	            => $items['max_pcs'],
								'min_pcs'	            => $items['min_pcs'],
							);
							$this->$model->insertData($insdata, 'ret_other_inventory_reorder_settings');
							//print_r($this->db->last_query());exit;
						}
					}
				}
				//Adding scheme map for gift code starts
				$selected_schemes = $_POST['scheme_select'];
				$quantity = $_POST['quantity'];
				$table_length = $this->input->post('table_length');
				$fromins = $_POST['tenurefrom'];
				$toins = $_POST['tenureto'];
				$delete_data = $this->$model->delete_gift_map_data($id);
				// if($table_length>0)
				// {
				// 	for($i=1;$i<=$table_length;$i++)
				// 	{
				// 		if($selected_scheme[$i]!='' && $quantity[$i]!='')
				// 		{
				// 			$data=array(
				// 				'id_other_item'		   => $id,
				// 				'id_scheme'           	=> $selected_scheme[$i],
				// 				'item_issue_limit'    	=> $quantity[$i],
				// 				'from_ins'              => $fromins[$i],
				// 				'to_ins'               => $toins[$i],  
				// 				'date_upd'         	   	=> date("Y-m-d H:i:s"),
				// 				'updated_by'         	=> $this->session->userdata('uid'),
				// 				);
				// 				$insert_gift_map = $this->$model->insertData($data,'gift_mapping');
				// 		}
				// 	}
				// }
				if ($table_length != '') {
					for ($i = 0; $i <= $table_length; $i++) {
						foreach ($selected_schemes[$i] as $selectedSchemeId) {
							if (!empty($selectedSchemeId) && !empty($quantity[$i])) {
								$data = array(
									'id_other_item'        =>  $id,
									'id_scheme'            => $selectedSchemeId,
									'item_issue_limit'     => $quantity[$i],
									'date_add'             => date("Y-m-d H:i:s"),
									'from_ins'             => $fromins[$i],
									'to_ins'               => $toins[$i],
									'created_by'           => $this->session->userdata('uid'),
								);
								// print_r($data);exit;
								$insert_gift_map = $this->$model->insertData($data, 'gift_mapping');
								// print_r($this->db->last_query());exit;
							}
						}
					}
				}
				//Adding scheme map for gift code ends
				if (isset($_FILES['other']['name']['other_item_img']) && $_FILES['other']['name']['other_item_img'] != '') {
					if ($id > 0) {
						
						$this->set_image_other($item, $inv_data['sku_id']);
					}
				}
				$this->load->library('phpqrcode/qrlib');
				$SERVERFILEPATH = 'other_product_qrcode';
				if (!is_dir($SERVERFILEPATH)) {
					mkdir($SERVERFILEPATH, 0777, TRUE);
				}
				$folder = $SERVERFILEPATH;
				$file_name1 = time() . $addData['sku_id'] . ".png";
				$src = time() . $addData['sku_id'];
				$file_name = $SERVERFILEPATH . '/' . $file_name1;
				// print_r($file_name);exit;
				QRcode::png($addData['sku_id'], $file_name);
				$item = $this->$model->update_other_inventory(array('qr_image' => $src), $id, 'id_other_item', 'ret_other_inventory_item');
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$this->session->set_flashdata('chit_alert', array('message' => 'Item modified successfully', 'class' => 'success', 'title' => 'Selected Item'));
				} else {
					$this->db->trans_rollback();
					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Selected Item'));
				}
				redirect('admin_ret_other_inventory/other_inventory/list');
				break;
			case 'print_qrcode':
				$addData = $this->$model->get_other_inventory_records($id);
				$this->load->library('phpqrcode/qrlib');
				$SERVERFILEPATH = 'other_product_qrcode/' . $addData['sku_id'];
				if (!is_dir($SERVERFILEPATH)) {
					mkdir($SERVERFILEPATH, 0777, TRUE);
				}
				$folder = $SERVERFILEPATH;
				$file_name1 = time() . $addData['sku_id'] . ".jpg";
				$file_name = $SERVERFILEPATH . '/' . $file_name1;
				QRcode::png($addData['sku_id'], $file_name);  //Passing QR data
				$src['img'][] = array(
					'sku_id'     => $addData['sku_id'],
					'name'      => $addData['name'],
					'src'        => $this->config->item('base_url') . "other_product_qrcode" . '/' . $addData['sku_id'] . '/' . $file_name1
				);
				// echo "<pre>"; print_r($src);exit;
				$html1 = $this->load->view('other_inventory/item_qrcode', $src, true);
				// print_r($html1);exit;
				$html = preg_replace('/>\s+</', "><", $html1); //Remove Blank page
				$this->load->helper(array('dompdf', 'file'));
				$dompdf = new DOMPDF();
				$dompdf->load_html($html);
				//$customPaper = array(0,0,220,111);
				//$customPaper = array(0,0,50,45);
				$dompdf->set_paper("portriat");
				$dompdf->render();
				while(ob_get_level()) ob_end_clean();
				$dompdf->stream("other.pdf", array('Attachment' => 0));
				break;
			case 'active_skuid':
				$data = $this->$model->getActiveskuid($_POST['searchTxt'], $_POST['searchField']);
				echo json_encode($data);
				break;
			default:
				$SETT_MOD = self::SETT_MOD;
				$list = $this->$model->ajax_get_other_inventory();
				$access = $this->$SETT_MOD->get_access('admin_ret_other_inventory/other_inventory/list');
				$data = array(
					'list' => $list,
					'access' => $access
				);
				echo json_encode($data);
		}
	}
	function check_sku_id()
	{
		$sku_id = $this->input->post('sku_id');
		$model_name = self::OTHER_INVENTORY_MODEL;
		$available = $this->$model_name->skuid_available($sku_id);
		if ($available) {
			echo 1;
		} else {
			echo 0;
		}
	}
	//Category
	public function inventory_category($type = "", $id = "")
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$SETT_MOD = "admin_settings_model";
		switch ($type) {
			case "add":
				$data['main_content'] = "other_inventory/category/form";
				$this->load->view('layout/template', $data);
				break;
			case 'list':
				$data['main_content'] = "other_inventory/category/list";
				$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_other_inventory/inventory_category/list');
				$this->load->view('layout/template', $data);
				break;
			case 'save':

				$addData = $_POST['item'];
				$data = array(
					'name'                 =>  $addData['name'],
					'outward_type'         =>  $addData['outward_type'],
					'asbillable'           =>  $addData['as_bill'],
					'expirydatevalidate'   =>  $addData['exp_date'],
					'reorderlevel'         =>  $addData['reorder_level'],
					'created_on'	         =>  date("Y-m-d H:i:s"),
					'created_by'           => $this->session->userdata('uid')
				);
				$this->db->trans_begin();
				$id_other_item_type = $this->$model->insertData($data, 'ret_other_inventory_item_type');
				//print_r($this->db->last_query());exit;
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$this->session->set_flashdata('chit_alert', array('message' => 'Category added successfully', 'class' => 'success', 'title' => 'Category'));
				} else {
					$this->db->trans_rollback();
					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Category'));
				}
				redirect('admin_ret_other_inventory/inventory_category/list');
				break;
			case "edit":
				$data['item'] = $this->$model->get_other_item_records($id);
				//echo "<pre>";print_r($data);exit;
				$data['main_content'] = "other_inventory/category/form";
				$this->load->view('layout/template', $data);
				break;
			case 'delete':
				$this->db->trans_begin();
				$this->$model->deleteData('id_other_item_type', $id, 'ret_other_inventory_item_type');
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$this->session->set_flashdata('chit_alert', array('message' => 'Item deleted successfully', 'class' => 'success', 'title' => 'Other Item'));
				} else {
					$this->db->trans_rollback();
					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Other Item'));
				}
				redirect('admin_ret_other_inventory/inventory_category/list');
				break;
			case "update":
				$addData = $_POST['item'];
				$data = array(
					'name'                 =>	$addData['name'],
					'outward_type'         =>	$addData['outward_type'],
					'asbillable'     	    =>	$addData['as_bill'],
					'expirydatevalidate'   =>	$addData['exp_date'],
					'reorderlevel' 	    =>	$addData['reorder_level'],
					'updated_on'	    =>  date("Y-m-d H:i:s"),
					'updated_by'           =>  $this->session->userdata('uid')
				);
				$this->db->trans_begin();
				$this->$model->update_otheritem($data, $id, 'id_other_item_type', 'ret_other_inventory_item_type');
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$this->session->set_flashdata('chit_alert', array('message' => 'Item modified successfully', 'class' => 'success', 'title' => 'Selected Item'));
				} else {
					$this->db->trans_rollback();
					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Selected Item'));
				}
				redirect('admin_ret_other_inventory/inventory_category/list');
				break;
			case 'active_itemname':
				$data = $this->$model->getActiveItemname();
				echo json_encode($data);
				break;
			default:
				$SETT_MOD = self::SETT_MOD;
				$list = $this->$model->ajax_getotheritem();
				$access = $this->$SETT_MOD->get_access('admin_ret_other_inventory/inventory_category/list');
				$data = array(
					'list' => $list,
					'access' => $access
				);
				echo json_encode($data);
		}
	}
	function otheritem_status($status, $id)
	{
		$data = array('status' => $status);
		$model = self::OTHER_INVENTORY_MODEL;
		$updstatus = $this->$model->update_otheritem($data, $id);
		if ($updstatus) {
			$this->session->set_flashdata('chit_alert', array('message' => 'Item status updated as ' . ($status == 1 ? 'Active' : 'Inactive') . ' successfully.', 'class' => 'success', 'title' => 'Item  Status'));
		} else {
			$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Item  Status'));
		}
		redirect('admin_ret_other_inventory/inventory_category/list');
	}
	function get_inventory_category()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_inventory_category();
		echo json_encode($data);
	}
	//Category
	//Purhcase Entry
	public function purchase_entry($type = "", $id = "")
	{
		$model = self::OTHER_INVENTORY_MODEL;
		switch ($type) {
			case "add":
				$data['comp_details'] = $this->admin_settings_model->getCompanyDetails("");
				$data['main_content'] = "other_inventory/purchase/form";
				$this->load->view('layout/template', $data);
				break;
			case 'list':
				$data['main_content'] = "other_inventory/purchase/list";
				$this->load->view('layout/template', $data);
				break;
			case 'save':
				$responseData = array();
				$addData = $_POST['purchase'];
				$order_items = $_POST['order_items_array'];
				//  print_r($order_items);exit;
				$p_ImgData  = json_decode(rawurldecode($this->input->post('tag_img')));
				// echo "<pre>"; print_r($p_ImgData);exit;
				$branch      = $this->$model->get_headOffice();
				$dCData      = $this->admin_settings_model->getBranchDayClosingData($branch['id_branch']);
				$entry_date  = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
				$ref_no = $this->$model->generatePurNo();
				$insData = array(
					'otr_inven_pur_supplier'    => $addData['id_karigar'],
					'entry_date'                => $entry_date,
					'supplier_order_ref_no'     => $addData['sup_refno'],
					'otr_inven_pur_order_ref'   => $ref_no,
					'supplier_bill_date'        => ($addData['sup_billdate'] != '' ? $addData['sup_billdate'] : date("Y-m-d")),
					'otr_inven_pur_created_on'  => date("Y-m-d H:i:s"),
					'otr_inven_pur_created_by'  => $this->session->userdata('uid')
				);
				$this->db->trans_begin();
				$insId = $this->$model->insertData($insData, 'ret_other_inventory_purchase');
				// print_r($this->db->last_query());exit;
				if ($insId) {
					if (!empty($order_items)) {
						foreach ($order_items as $item) {
							$itemData = array(
								'otr_inven_pur_id'   => $insId,
								'inv_pur_itm_itemid' => $item['item_id'],
								'inv_pur_itm_qty'    => $item['quantity'],
								'inv_pur_itm_rate'   => $item['rate'],
								'inv_pur_itm_total'  => $item['gst_amount'] != '' ? $item['gst_amount'] : 0,
								'inv_pur_itm_gst'    => $item['tax_amount'] != '' ? $item['tax_amount'] : 0,
								'gst_amount'         => $item['pur_gst_amount'] != '' ? $item['pur_gst_amount'] : 0,
							);
							//    print_r($gst);
							//    print_r($itemData);exit;
							$item_InsId = $this->$model->insertData($itemData, 'ret_other_inventory_purchase_items');
							if ($item_InsId) {
								//Updating Log
								//    $inventory_category=$this->$model->get_InventoryCategory($item['item_id']);
								//echo "<pre>";print_r($inventory_category);exit;
								//    $logData=array(
								//         'item_id'      =>$item['item_id'],
								//         'no_of_pieces' =>$item['quantity'],
								//         'amount'       =>$item['gst_amount'],
								//         'date'         =>$entry_date,
								//         'status'       =>0,
								//         'from_branch'  =>NULL,
								//         'to_branch'    =>$branch['id_branch'],
								//         'created_on'   =>date("Y-m-d H:i:s"),
								//         'created_by'   =>$this->session->userdata('uid')
								//     );
								//     $this->$model->insertData($logData,'ret_other_inventory_purchase_items_log');
								//echo "<pre>";print_r($this->db->last_query());exit;
								//Updating Log
							}
						}
					}
					if (sizeof($p_ImgData) > 0) {
						$_FILES['precious'] = array();
						foreach ($p_ImgData as $precious) {
							$imgFile = $this->base64ToFile($precious->src);
							$_FILES['precious'][] = $imgFile;
						}
						$img_arr = array();
						if (!empty($_FILES['precious'])) {
							$folder = self::IMG_PATH . "purchase_entry/";
							if (!is_dir($folder)) {
								mkdir($folder, 0777, TRUE);
							}
							foreach ($_FILES['precious'] as $file_key => $file_val) {
								if ($file_val['tmp_name']) {
									$img_name = $insId . "_" . mt_rand(100001, 999999) . ".jpg";
									$path = $folder . "/" . $img_name;
									$result = $this->upload_img('image', $path, $file_val['tmp_name']);
									if ($result) {
										$arrayimg_tag = array(
											'otr_inven_pur_id'   => $insId,
											'image'    => $img_name,
											'date_add' => date("Y-m-d H:i:s"),
										);
										$img_arr[] = $arrayimg_tag;
										$insImageId = $this->$model->insertData($arrayimg_tag, 'ret_other_inventory_purchase_images');
									}
								}
							}
						}
					}
					if ($this->db->trans_status() === TRUE) {
						$this->db->trans_commit();
						$log_data = array(
							'id_log'        => $this->session->userdata('id_log'),
							'event_date'	=> date("Y-m-d H:i:s"),
							'module'      	=> 'Other Inventory',
							'operation'   	=> 'Add',
							'record'        =>  NULL,
							'remark'       	=> 'Other Inventory.'
						);
						$this->log_model->log_detail('insert', '', $log_data);
						$responseData = array('status' => true, 'otr_inv_pur_id' => $insId, 'message' => 'Purchase Entry Added successfully');
						$this->session->set_flashdata('chit_alert', array('message' => 'Purchase Entry Added successfully', 'class' => 'success', 'title' => 'Other Inventory'));
					} else {
						$this->db->trans_rollback();
						$responseData = array('status' => false, 'message' => 'Unable to proceed the requested process');
						$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Other Inventory'));
					}
				} else {
					$responseData = array('status' => false, 'message' => 'Unable to Add Other Inventory Purchase');
				}
				echo json_encode($responseData);
				break;
			case "edit":
				$data['item'] = $this->$model->get_other_item_records($id);
				$data['main_content'] = "other_inventory/category/form";
				$this->load->view('layout/template', $data);
				break;
			case 'cancel_purchase_entry':
				$updData = array(
					'purchase_bill_status'  => 2,
					'cancel_reason'     => $_POST['cancel_reason'],
					'otr_inven_pur_updated_on'        => date("Y-m-d H:i:s"),
					'otr_inven_pur_updated_by'        => $this->session->userdata('uid')
				);
				// print_r($updData);exit;
				$this->db->trans_begin();
				$this->$model->updateData($updData, 'otr_inven_pur_id', $_POST['otr_inven_pur_id'], 'ret_other_inventory_purchase');
				// print_r($this->db->last_query());exit;
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$this->session->set_flashdata('chit_alert', array('message' => 'Purchase Entry Cancelled Successfully', 'class' => 'success', 'title' => 'Purchase Entry'));
					$return_data = array('status' => TRUE, 'message' => 'Order Instructions Added Successfully..');
				} else {
					echo $this->db->last_query();
					exit;
					$this->db->trans_rollback();
					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Purchase Entry'));
					$return_data = array('status' => FALSE, 'message' => 'Unable to proceed the requested process');
				}
				echo json_encode($return_data);
				break;
			case 'purchase_details':
				$data['pur_item'] = $this->$model->getPurchaseDet($id);
				$data['pur_item_details'] = $this->$model->get_purchase_item_det($id);
				$data['pur_gst_details'] = $this->$model->get_purchase_gst_det($id);
				$data['comp_details'] = $this->admin_settings_model->getCompanyDetails("");
				// echo "<pre>";print_r($data);exit;
				$html = $this->load->view(self::VIEW_FOLDER . 'purchase/purchase_entry', $data, true);
				echo $html;
				exit;
				break;
			default:
				$SETT_MOD = self::SETT_MOD;
				$list = $this->$model->ajax_getPurchaseEntrylist($_POST);
				$access = $this->$SETT_MOD->get_access('admin_ret_other_inventory/purchase_entry/list');
				$data = array(
					'list' => $list,
					'access' => $access
				);
				echo json_encode($data);
		}
	}
	function get_img_by_item_id()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_inv_purchase_images($_POST['item_id']);
		echo json_encode($data);
	}
	public function product_details($type = "", $id = "")
	{
		$model = self::OTHER_INVENTORY_MODEL;
		switch ($type) {
			case "add":
				$data['main_content'] = "other_inventory/product/form";
				$this->load->view('layout/template', $data);
				break;
			case 'list':
				$data['main_content'] = "other_inventory/product/list";
				$this->load->view('layout/template', $data);
				break;
			case 'save':
				$order_items = json_decode($_POST['order_items'], true);
				$branch      = $this->$model->get_headOffice();
				$dCData      = $this->admin_settings_model->getBranchDayClosingData($branch['id_branch']);
				$entry_date  = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
				$id_branch = $this->session->userdata('id_branch');
				$timestamp = time();
				foreach ($order_items as $item) {
					for ($i = 1; $i <= $item['pieces']; $i++) {
						$lastTagCode    = $this->$model->getlastrefno();
						$item_ref_no    = $this->generaterefCode($lastTagCode);
						$itemDetail = array(
							'inv_pur_itm_id'            => $item['inv_pur_itm_id'],
							'other_invnetory_item_id'   => $item['itemid'],
							'amount'                    => $item['rate'],
							'piece'                     => 1,
							'item_ref_no'               => $item['itemid'] . '-' . $item_ref_no,
							'current_branch'            => $branch['id_branch'],
							'ref_no'                    => $timestamp,
							'status'                    => 0,
						);
						$insId = $this->$model->insertData($itemDetail, 'ret_other_inventory_purchase_items_details');
						if ($insId) {
							$logData = array(
								'item_id'      => $item['itemid'],
								'no_of_pieces' => 1,
								'amount'       => $item['rate'],
								'date'         => $entry_date,
								'status'       => 0,
								'from_branch'  => NULL,
								'to_branch'    => $branch['id_branch'],
								'created_on'   => date("Y-m-d H:i:s"),
								'created_by'   => $this->session->userdata('uid')
							);
							$this->$model->insertData($logData, 'ret_other_inventory_purchase_items_log');
							//Updating Log
							$ref_no = $this->$model->get_ref_no_details($insId);
							//  print_r($this->db->last_query());exit;
						}
					}
				}
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$log_data = array(
						'id_log'        => $this->session->userdata('id_log'),
						'event_date'	=> date("Y-m-d H:i:s"),
						'module'      	=> 'Other Inventory',
						'operation'   	=> 'Add',
						'record'        =>  NULL,
						'remark'       	=> 'Other Inventory.'
					);
					$this->log_model->log_detail('insert', '', $log_data);
					$responseData = array('status' => true, 'ref_no' => $ref_no, 'message' => 'Product Details Added successfully');
					$this->session->set_flashdata('chit_alert', array('message' => 'Product Details Added successfully', 'class' => 'success', 'title' => 'Other Inventory'));
				} else {
					$this->db->trans_rollback();
					$responseData = array('status' => false, 'message' => 'Unable to proceed the requested process');
					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Other Inventory'));
				}
				echo json_encode($responseData);
				break;
			default:
				$SETT_MOD = self::SETT_MOD;
				$list = $this->$model->ajax_getProductlist($_POST);
				$access = $this->$SETT_MOD->get_access('admin_ret_other_inventory/product_details/list');
				$data = array(
					'list' => $list,
					'access' => $access
				);
				echo json_encode($data);
		}
	}
	function generaterefCode($lastTagCode)
	{
		$tagCode        = $lastTagCode; // Saple : 1-A12345 or 1-12345
		$code_det       = explode('-', $tagCode);
		$alpha_char		= '';
		// Split Alphabet and Serial number
		if (preg_match('/[A-Z]+\K/', $code_det[1])) {
			$str_split = preg_split('/[A-Z]+\K/', $code_det[1]);
			$tag_number = $str_split[1];
			$alpha_char = $str_split[0];
		} else {
			$tag_number = $code_det[1];
		}
		//  Increment Number
		if ($tag_number != NULL && $tag_number != '' && $tag_number != 99999) {
			$number = (int) $tag_number;
			$number++;
			$code_number = str_pad($number, 5, '0', STR_PAD_LEFT);
		} else {
			$code_number = str_pad('1', 5, '0', STR_PAD_LEFT);
		}
		//  Increment Alphabet if reached 99999
		if ($tag_number == 99999) {
			if ($alpha_char == '') {
				$alpha_char = 'A';
			} else {
				$alpha_char = ++$alpha_char;
			}
		}
		return $alpha_char . '' . $code_number;
	}
	public function stock_details($type = "", $id = "")
	{
		$model = self::OTHER_INVENTORY_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = "other_inventory/report/stock_report";
				$this->load->view('layout/template', $data);
				break;
			default:
				$data = $this->$model->other_inventory_stock($_POST);
				echo json_encode($data);
		}
	}
	function get_other_inventory_item()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_other_inventory_item();
		echo json_encode($data);
	}
	function get_other_inventory_product()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_other_inventory_product_det($_POST);
		echo json_encode($data);
	}
	function get_pro_detail_list()
	{
		$data['main_content'] = "other_inventory/product/pro_details";
		$this->load->view('layout/template', $data);
	}
	function product_tag_detail()
	{
		$SETT_MOD = self::SETT_MOD;
		$model = self::OTHER_INVENTORY_MODEL;
		$other_item_id  = $this->input->post('other_inv_item');
		$list = $this->$model->get_other_inventory_product($other_item_id);
		$access = $this->$SETT_MOD->get_access('admin_ret_other_inventory/product_details/list');
		$data = array(
			'list'  => $list,
			'access' => $access
		);
		echo json_encode($data);
	}
	function get_other_inventory_ref_no()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_other_inventory_ref_no();
		echo json_encode($data);
	}
	function get_other_inventory_details()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_other_inventory_details($_POST);
		echo json_encode($data);
	}
	function get_supplier()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_supplier();
		echo json_encode($data);
	}
	public function issue_item($type = "", $id = "")
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$SETT_MOD = "admin_settings_model";
		switch ($type) {
			case 'list':
				$data['main_content'] = "other_inventory/issue/list";
				$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_other_inventory/issue_item/list');
				$this->load->view('layout/template', $data);
				break;
			case 'add':
				$data['main_content'] = "other_inventory/issue/form";
				$this->load->view('layout/template', $data);
				break;
			case 'save':
				$addData        = $_POST['issue'];
				$id_other_item = $addData['id_other_item'];
				$responseData   = array();
				$dCData      = $this->admin_settings_model->getBranchDayClosingData($addData['id_branch']);
				$entry_date  = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);
				$insData = array(
					'id_other_item'     => $id_other_item,
					'issue_form'        => 2,
					'issue_date'        => $entry_date,
					'bill_id'           => $addData['bill_id'],
					'no_of_pieces'      => $addData['total_pcs'],
					'id_branch'         => $addData['id_branch'],
					'remarks'           => $addData['remarks'],
					'created_on'        => date("Y-m-d H:i:s"),
					'created_by'        => $this->session->userdata('uid')
				);
				//Adding scheme map for gift code starts
				if ($id_other_item) {
					$selected_scheme = $_POST['scheme_select'];
					$quantity = $_POST['quantity'];
					// getting number of rows of scheme table 
					$table_length = $this->input->post('table_length');
					if ($table_length > 0) {
						for ($i = 1; $i <= $table_length; $i++) {
							if ($selected_scheme[$i] != '' && $quantity[$i] != '') {
								$data = array(
									'id_other_item '		=> $id_other_item,
									'id_scheme '           	=> $selected_scheme[$i],
									'item_issue_limit '    	=> $quantity[$i],
									'date_add'         	   	=> date("Y-m-d H:i:s"),
									'created_by'         	=> $this->session->userdata('uid'),
								);
								$insert_gift_map = $this->$model->insertData($data, 'gift_mapping');
							}
						}
					}
				}
				//Adding scheme map for gift code ends
				$this->db->trans_begin();
				$insId = $this->$model->insertData($insData, 'ret_other_invnetory_issue');
				//print_r($this->db->last_query());exit;
				if ($insId) {
					$inventoryItem = $this->$model->get_InventoryCategory($addData['id_other_item']);
					$itemDetails    = $this->$model->get_other_inventory_purchase_items_details($addData['id_other_item'], $addData['id_branch'], $inventoryItem['issue_preference'], $addData['total_pcs']);
					$total_amount = 0;
					foreach ($itemDetails as $items) {
						$total_amount += $items['amount'];
						$updData = array(
							'id_inventory_issue' => $insId,
							'status' => 1
						);
						$this->$model->updateData($updData, 'pur_item_detail_id', $items['pur_item_detail_id'], 'ret_other_inventory_purchase_items_details');
						//print_r($this->db->last_query());exit;
					}
					$logData = array(
						'item_id'      => $addData['id_other_item'],
						'no_of_pieces' => count($itemDetails), // PAT-INV-001: use actual processed count
						'amount'       => $total_amount,
						'date'         => $entry_date,
						'status'       => 1,
						'from_branch'  => $addData['id_branch'],
						'to_branch'    => NULL,
						'id_inventory_issue' => $insId,
						'created_on'   => date("Y-m-d H:i:s"),
						'created_by'   => $this->session->userdata('uid')
					);
					$this->$model->insertData($logData, 'ret_other_inventory_purchase_items_log');
					if ($this->db->trans_status() === TRUE) {
						$this->db->trans_commit();
						$this->session->set_flashdata('chit_alert', array('message' => 'Item Issued successfully', 'class' => 'success', 'title' => 'Item Issue'));
						$responseData = array('status' => TRUE, 'message' => 'Item Issued successfully');
					} else {
						$this->db->trans_rollback();
						$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Item Issue'));
						$responseData = array('status' => false, 'message' => 'Unable to proceed the requested process');
					}
				} else {
					$responseData = array('status' => TRUE, 'message' => 'Unable to Issue Gift Items.');
				}
				echo json_encode($responseData);
				break;
			default:
				$data = $this->$model->get_OtherInventoryIssueDetails($_POST);
				echo json_encode($data);
		}
	}
	//Purhcase Entry
	function get_bill_details()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_bill_details($_POST);
		echo json_encode($data);
	}
	function get_invnetory_item()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_invnetory_item($_POST);
		echo json_encode($data);
	}
	function get_customer()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_customer();
		echo json_encode($data);
	}
	//size master
	public function item_size($type = "", $id = "")
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$SETT_MOD = "admin_settings_model";
		switch ($type) {
			case 'list':
				$data['main_content'] = "other_inventory/size_list";
				$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_other_inventory/item_size/list');
				$this->load->view('layout/template', $data);
				break;
			case 'edit':
				$data = $this->$model->get_packaging_size($id);
				echo json_encode($data);
				break;
			case 'save':
				$size_name        = $_POST['size_name'];
				$responseData   = array();
				$insData = array(
					'size_name'  => $size_name,
					'created_on' => date("Y-m-d H:i:s"),
					'created_by' => $this->session->userdata('uid')
				);
				$this->db->trans_begin();
				$insId = $this->$model->insertData($insData, 'ret_other_inventory_size');
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$this->session->set_flashdata('chit_alert', array('message' => 'Item Size Added successfully', 'class' => 'success', 'title' => 'Item Size'));
					$responseData = array('status' => TRUE, 'message' => 'Item Size successfully');
				} else {
					$this->db->trans_rollback();
					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Item Size'));
					$responseData = array('status' => false, 'message' => 'Unable to proceed the requested process');
				}
				echo json_encode($responseData);
				break;
			case 'get_ActivePackagingItemSize':
				$data = $this->$model->get_ActivePackagingItemSize();
				echo json_encode($data);
				break;
			case 'update':
				$size_name        = $_POST['size_name'];
				$id_inv_size      = $_POST['id_inv_size'];
				$responseData   = array();
				$updData = array(
					'size_name'  => $size_name,
					'updated_on' => date("Y-m-d H:i:s"),
					'updated_by' => $this->session->userdata('uid')
				);
				$this->db->trans_begin();
				$this->$model->updateData($updData, 'id_inv_size', $id_inv_size, 'ret_other_inventory_size');
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$this->session->set_flashdata('chit_alert', array('message' => 'Item Size Updated successfully', 'class' => 'success', 'title' => 'Item Size'));
					$responseData = array('status' => TRUE, 'message' => 'Item Size Updated successfully');
				} else {
					$this->db->trans_rollback();
					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested process', 'class' => 'danger', 'title' => 'Item Size'));
					$responseData = array('status' => false, 'message' => 'Unable to proceed the requested process');
				}
				echo json_encode($responseData);
				break;
			case 'delete':
				$this->db->trans_begin();
				$this->$model->deleteData('id_inv_size', $id, 'ret_other_inventory_size');
				if ($this->db->trans_status() === TRUE) {
					$this->db->trans_commit();
					$this->session->set_flashdata('chit_alert', array('message' => 'Item Size deleted successfully', 'class' => 'success', 'title' => 'Other Item Size'));
				} else {
					$this->db->trans_rollback();
					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request', 'class' => 'danger', 'title' => 'Other Item Size'));
				}
				redirect('admin_ret_other_inventory/item_size/list');
				break;
			default:
				$SETT_MOD = self::SETT_MOD;
				$list = $this->$model->ajax_getOtherInventorySizeList($_POST);
				$access = $this->$SETT_MOD->get_access('admin_ret_other_inventory/item_size/list');
				$data = array(
					'list' => $list,
					'access' => $access
				);
				echo json_encode($data);
		}
	}
	function packaging_item_size_status($status, $id)
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$updStatus = $this->$model->updateData(array('status' => $status), 'id_inv_size', $id, 'ret_other_inventory_size');
		if ($updStatus) {
			$this->session->set_flashdata('chit_alert', array('message' => 'Size updated as ' . ($status ? 'active' : 'inactive') . ' successfully.', 'class' => 'success', 'title' => 'Item Size'));
		} else {
			$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed the requested operation', 'class' => 'danger', 'title' => 'Item Size'));
		}
		redirect('admin_ret_other_inventory/item_size/list');
	}
	//size master
	//Available Stock
	public function available_stock($type = "", $id = "")
	{
		$model = self::OTHER_INVENTORY_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = "other_inventory/report/available_stock";
				$this->load->view('layout/template', $data);
				break;
			default:
				$data = $this->$model->get_AvailableStockDetails($_POST);
				echo json_encode($data);
		}
	}
	public function get_ActiveCategory()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_ActiveCategory();
		echo json_encode($data);
	}
	//Available Stock
	//Product Mapping
	public function product_mapping($type = "", $id = "")
	{
		$model = self::OTHER_INVENTORY_MODEL;
		switch ($type) {
			case 'list':
				$SETT_MOD = self::SETT_MOD;
				$data['main_content'] = "other_inventory/product_mapping";
				$data['access']=$this->$SETT_MOD->get_access('admin_ret_other_inventory/product_mapping/list');
				$this->load->view('layout/template', $data);
				break;
			default:
				$SETT_MOD = self::SETT_MOD;
				$list = $this->$model->get_item_mapping_details($_POST);
				$access = $this->$SETT_MOD->get_access('admin_ret_other_inventory/product_mapping/list');
				$data = array(
					'list' => $list,
					'access' => $access
				);
				echo json_encode($data);
		}
	}
	function delete_product_mapping()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$reqdata = $this->input->post('req_data');
		foreach ($reqdata as $items) {
			$this->db->trans_begin();
			$this->$model->deleteData('inv_des_id', $items['inv_des_id'], 'ret_other_inventory_product_link');
		}
		if ($this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
			$status = array('status' => true, 'msg' => 'Product Mapped Deleted successfully');
		} else {
			$this->db->trans_rollback();
			$status = array('status' => false, 'msg' => 'Unable to Proceed Your Request');
		}
		echo json_encode($status);
	}
	function update_product_mapping()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$products     = $this->input->post('id_product');
		$id_other_item      = $this->input->post('id_other_item');
		//print_r($products);exit;
		if ($products[0] == 0) {
			$product = $this->$model->get_ActiveProduct();
			foreach ($product as $val) {
				if ($this->$model->check_other_inv_products_maping($val['pro_id'], $id_other_item)) {
					$insdata = array(
						'inv_pro_id'          => $val['pro_id'],
						'inv_des_otheritemid' => $id_other_item,
						'inv_des_created_by'  => $this->session->userdata('uid'),
						'inv_des_created_on'  => date("Y-m-d H:i:s"),
					);
					$this->db->trans_begin();
					$this->$model->insertData($insdata, 'ret_other_inventory_product_link');
				}
			}
		} else {
			foreach ($products as $pro_id) {
				if ($this->$model->check_other_inv_products_maping($pro_id, $id_other_item)) {
					$insdata = array(
						'inv_pro_id'          => $pro_id,
						'inv_des_otheritemid' => $id_other_item,
						'inv_des_created_by'  => $this->session->userdata('uid'),
						'inv_des_created_on'  => date("Y-m-d H:i:s"),
					);
					$this->db->trans_begin();
					$this->$model->insertData($insdata, 'ret_other_inventory_product_link');
				}
			}
		}
		if ($this->db->trans_status() === TRUE) {
			$this->db->trans_commit();
			$status = array('status' => true, 'msg' => 'Product Mapped successfully');
		} else {
			$this->db->trans_rollback();
			$status = array('status' => false, 'msg' => 'Unable to Proceed Your Request');
		}
		echo json_encode($status);
	}
	function get_productMappedDetails()
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_productMappedDetails($_POST['id_branch']);
		echo json_encode($data);
	}
	//Product Mapping
	//Reporder Report
	public function reorder_report($type = "", $id = "")
	{
		$model = self::OTHER_INVENTORY_MODEL;
		switch ($type) {
			case 'list':
				$data['main_content'] = "other_inventory/report/reorder";
				$this->load->view('layout/template', $data);
				break;
			default:
				$SETT_MOD = self::SETT_MOD;
				$list = $this->$model->get_reorder_report($_POST);
				$access = $this->$SETT_MOD->get_access('admin_ret_other_inventory/reorder_report/list');
				$data = array(
					'list' => $list,
					'access' => $access
				);
				echo json_encode($data);
		}
	}
	//Reporder Report
	/*function other_inventory_print($ref_no){
		$model=self::OTHER_INVENTORY_MODEL;
		$ret = array();
		$data = $this->$model->get_other_inventory_print($ref_no);
		$this->load->library('phpqrcode/qrlib');
		$SERVERFILEPATH = 'other_inventory_qrcode';
		
		if (!is_dir($SERVERFILEPATH)) {
			mkdir($SERVERFILEPATH, 0777, TRUE);
			
		}
		foreach($data as $d){
			$qrfilename = $d['item_ref_no'];
			$file_name = $SERVERFILEPATH.'/'.$qrfilename. ".png";
		
			QRcode::png($qrfilename, $file_name);
			$d['qrcode'] =  $this->config->item('base_url').$file_name;
			$ret['data'][]=$d;
		 }
		$html = $this->load->view('other_inventory/print/qr_print', $ret,true);
		
		// echo "<pre>";print_r($ret);exit;
		
		echo $html;exit;
	}
	function product_other_inventory_print($id){
		$model=self::OTHER_INVENTORY_MODEL;
		$ret = array();
		$data = $this->$model->get_product_other_inventory_print($id);
		$this->load->library('phpqrcode/qrlib');
		$SERVERFILEPATH = 'other_inventory_qrcode';
		
		if (!is_dir($SERVERFILEPATH)) {
			mkdir($SERVERFILEPATH, 0777, TRUE);
			
		}
		foreach($data as $d){
			$qrfilename = $d['item_ref_no'];
			$file_name = $SERVERFILEPATH.'/'.$qrfilename. ".png";
		
			QRcode::png($qrfilename, $file_name);
			$d['qrcode'] =  $this->config->item('base_url').$file_name;
			$ret['data'][]=$d;
		
		
		}
		$html = $this->load->view('other_inventory/print/qr_print', $ret,true);
		
		// echo "<pre>";print_r($ret);exit;
		
		echo $html;exit;
	}*/
	function other_inventory_print($ref_no)
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_other_inventory_print($ref_no);
		$content = "";
		$i = 1;
		$firstTag = 1;
		$secTag = 2;
		$thirdTag = 3;
		$totalcount = count($data);
		$tagprintCode = "";
		foreach ($data as $d) {
			$no = 1;
			if ($i == $firstTag) {
				$no = 1;
				$firstTag += 3;
			} elseif ($i == $secTag) {
				$no = 2;
				$secTag += 3;
			} elseif ($i == $thirdTag) {
				$no = 3;
				$thirdTag += 3;
			}
			$tagprintCode = $tagprintCode . ($this->get_tag_code($d, $no));
			if ($no == 3 || $i == $totalcount) {
				$printCode = $this->get_printer_code($tagprintCode);
				if ($i > 3) {
					$content = $content . "\r\n";
					$content = $content . $printCode;
				} else {
					$content = $printCode;
				}
			}
			$i++;
		}
		$content = ($content);
		if ($content != "") {
			$fileName =  uniqid() . '_lmx' . '.prn';
			$this->downloadFile($content, $fileName);
		}
	}
	function product_other_inventory_print($id)
	{
		$model = self::OTHER_INVENTORY_MODEL;
		$data = $this->$model->get_product_other_inventory_print($id);
		$content = "";
		$i = 1;
		$firstTag = 1;
		$secTag = 2;
		$thirdTag = 3;
		$totalcount = count($data);
		$tagprintCode = "";
		foreach ($data as $d) {
			$no = 1;
			if ($i == $firstTag) {
				$no = 1;
				$firstTag += 3;
			} elseif ($i == $secTag) {
				$no = 2;
				$secTag += 3;
			} elseif ($i == $thirdTag) {
				$no = 3;
				$thirdTag += 3;
			}
			$tagprintCode = $tagprintCode . ($this->get_tag_code($d, $no));
			if ($no == 3 || $i == $totalcount) {
				$printCode = $this->get_printer_code($tagprintCode);
				if ($i > 3) {
					$content = $content . "\r\n";
					$content = $content . $printCode;
				} else {
					$content = $printCode;
				}
				$tagprintCode = "";
			}
			$i++;
		}
		$content = ($content);
		if ($content != "") {
			$fileName =  uniqid() . '_lmx' . '.prn';
			$this->downloadFile($content, $fileName);
		}
	}
	// Function to download a file
	function downloadFile($content, $filename)
	{
		// Set appropriate headers for the file download
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header("Pragma: no-cache");
		// Output the file content
		echo $content;
	}
	function get_tag_code($data, $no)
	{
		$tag_printer_code = "";
		if ($no == 1) {
			$tag_printer_code = '^FT170,90^A0N,35,35^FH\^FD' . $data['pro_name'] . '^FS' . "\r\n" .
				'^FT170,140^A0N,35,35^FH\^FD' . $data['otr_inven_pur_supplier'] . '^FS' . "\r\n" .
				'^FT170,190^A0N,35,35^FH\^FD' . $data['item_ref_no'] . '^FS' . "\r\n" .
				'^FT40,190^BQN,5,5' . "\r\n" .
				'^FDMA,' . $data['item_ref_no'] . '^FS' . "\r\n";
		} else if ($no == 2) {
			$tag_printer_code = '^FT590,90^A0N,35,35^FH\^FD' . $data['pro_name'] . '^FS' . "\r\n" .
				'^FT590,140^A0N,35,35^FH\^FD' . $data['otr_inven_pur_supplier'] . '^FS' . "\r\n" .
				'^FT590,190^A0N,35,35^FH\^FD' . $data['item_ref_no'] . '^FS' . "\r\n" .
				'^FT460,190^BQN,5,5' . "\r\n" .
				'^FDMA,' . $data['item_ref_no'] . '^FS' . "\r\n";
		} else if ($no == 3) {
			$tag_printer_code = '^FT1000,90^A0N,35,35^FH\^FD' . $data['pro_name'] . '^FS' . "\r\n" .
				'^FT1000,140^A0N,35,35^FH\^FD' . $data['otr_inven_pur_supplier'] . '^FS' . "\r\n" .
				'^FT1000,190^A0N,35,35^FH\^FD' . $data['item_ref_no'] . '^FS' . "\r\n" .
				'^FT870,190^BQN,5,5' . "\r\n" .
				'^FDMA,' . $data['item_ref_no'] . '^FS' . "\r\n";
		}
		return $tag_printer_code;
	}
	function get_printer_code($tagprintcode)
	{
		$printer_code = 'CT~~CD,~CC^~CT~' . "\r\n" .
			'^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR4,4~SD27^JUS^LRN^CI0^XZ' . "\r\n" .
			'^XA' . "\r\n" .
			'^MMT' . "\r\n" .
			'^PW1600' . "\r\n" .
			'^LL101' . "\r\n" .
			'^LS0' . "\r\n" .
			$tagprintcode .
			'^PQ1^XZ' . "\r\n" .
			'^XZ' . "\r\n";
		return $printer_code;
	}
	function set_image($id, $img_path, $file)
	{
		if ($_FILES[$file]['name']) {
			$img = $_FILES[$file]['tmp_name'];
			$status = $this->upload_img($file, $img_path, $img);
			return $status;
		}
	}
	// function upload_img( $outputImage,$dst, $img)
	// {	
	// 	if (($img_info = getimagesize($img)) === FALSE)
	// 	{
	// 		// die("Image not found or not an image");
	// 		return false;
	// 	}
	// 	$width = $img_info[0];
	// 	$height = $img_info[1];
	// 	switch ($img_info[2]) {
	// 	  case IMAGETYPE_GIF  : $src = imagecreatefromgif($img);
	// 	  						$tmp = imagecreatetruecolor($width, $height);
	// 	  						$kek=imagecolorallocate($tmp, 255, 255, 255);
	// 				      		imagefill($tmp,0,0,$kek);
	// 	  						break;
	// 	  case IMAGETYPE_JPEG : $src = imagecreatefromjpeg($img); 
	// 	  						$tmp = imagecreatetruecolor($width, $height);
	// 	 						break;
	// 	  case IMAGETYPE_PNG  : $src = imagecreatefrompng($img);
	// 						    $tmp = imagecreatetruecolor($width, $height);
	// 	  						$kek=imagecolorallocate($tmp, 255, 255, 255);
	// 				     		imagefill($tmp,0,0,$kek);
	// 				     		break;
	// 	  default : //die("Unknown filetype");	
	// 	  return false;
	// 	}		
	// 	imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
	// 	$res = imagejpeg($tmp, $dst); 
	// 	return $res;
	// }
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
	public function imgTobase64($path)
	{
		$type = pathinfo($path, PATHINFO_EXTENSION);
		$data = file_get_contents($path);
		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		return $base64;
	}
	function isEmptySetDefault($value, $default)
	{
		if ($value != '' &&  $value != NULL &&  $value != 'null' && $value != 'undefined') {
			return $value;
		} else {
			return $default;
		}
	}

	  // Method to get all size names from the database
	  public function get_all_sizes() {
		$model = self::OTHER_INVENTORY_MODEL;
		$sizes = $this->$model->get_all_sizes();
		// print_r($sizes);exit;
		echo json_encode($sizes);
	}
	
	function CheckIsNameDuplicate(){

		$model = 'ret_other_inventory_model';

		// print_r($_POST['name']);exit;

		$data = $this->$model->CheckIsNameDuplicate($_POST['name']);

		// print_r($data);exit;
		echo json_encode($data);
	}
}
