<?php
if( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_ret_supp_catalog extends CI_Controller
{

	const SUPPIMG_PATH = 'assets/img/supplier/';

	function __construct()
	{
		parent::__construct();
		ini_set('date.timezone', 'Asia/Calcutta');
		$this->load->model('ret_supp_catalog_model');

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
	public function index()
	{	
	}	
	/**
	* Supplier Catalog Functions Starts
	*/
	public function supplier_catalog($type="",$id="") {
		
		$model = "ret_supp_catalog_model";
		
		switch($type) {
		
			case 'list':
				
				$data['main_content'] = "order/supplier_catalog/list" ;
				
				$this->load->view('layout/template', $data);
				
				break;
			case "add":
				
				$data['main_content'] = "order/supplier_catalog/form";

				$data['supp'] = $this->$model->get_empty_record();
				
				$this->load->view('layout/template', $data);
				
				break;
			
			case "edit":
			
				$data['main_content'] = "order/supplier_catalog/form" ;

				$data['id_supp_catalogue'] = $id;

				/*foreach($data['supp']['weightRange']  as $key => $item ) {

	
					$data['supp']['weightRange'][$key]['images_details']	= $this->getSupplierCal_images($item['images_details']);
		
				}*/
				/*echo "<pre>";
				print_r($data);
				echo "</pre>";
				exit;*/

				$this->load->view('layout/template', $data);

				break;

			case "get_data_by_id":

				$data = array();

				if($id > 0) {

					$data = $this->$model->ajax_getSupplierCatList($id)[0];

				}

				echo json_encode($data);

				break;
			
			case "save":
					
				$datetime = date("Y-m-d H:i:s");

				$save_status = false;

				$message = "";

				$supp_cat_img = self::SUPPIMG_PATH;

				/*echo "<pre>";
				print_r($_POST);
				echo "</pre>";exit;*/

				$design_code 	= $this->$model->get_designcode();

				$product_id 	= $_POST['product_id'];
				$design_id 		= $_POST['design_id'];
				$id_sub_design 	= $_POST['subdesign_id'];
				$status			= $_POST['status'];
				$uid			= $this->session->userdata('uid');

				$insArray = array(
					'ctl_datetime'	=>	$datetime,
					'product_id'	=>	$product_id,
					'design_id'		=>	$design_id,
					'id_sub_design'	=>	$id_sub_design,
					'status'		=>	$status,
					'design_code'	=>	$design_code,
					'created_on'	=>	$datetime,
					'created_by'	=>	$uid
				);

				$this->db->trans_begin();

				$insertId = $this->$model->insertData($insArray,'ret_supp_catalogue');

				if($insertId > 0) {

					$items = $_POST['item'];

					$_FILES['supp_cat_image']=array();

					$weight 			= trim($_POST['weight']);
					$from_weight 		= trim($_POST['from_weight']);
					$to_weight 			= trim($_POST['to_weight']);
					$purity 			= trim(trim($_POST['purity']),",");
					$size 				= trim(trim($_POST['size']),",");
					$wastage 			= $_POST['wastage'];
					$display_va 		= $_POST['display_va'];
					$mc_value 			= $_POST['mc_value'];
					$mc_type 			= $_POST['mc_type'];
					$display_mc 		= $_POST['display_mc'];
					$delivery_duration 	= $_POST['delivery_duration'];
					$display_duration 	= $_POST['display_duration'];
					$karigar 			= trim(trim($_POST['karigar']),",");

					$insArray = array(
						'id_supp_catalogue'		=>	$insertId,
						'weight'				=>	$weight,
						'from_weight'			=>	$from_weight,
						'to_weight'				=>	$to_weight,
						'purity'				=>	$purity,
						'size'					=>	$size,
						'wastage'				=>	$wastage,
						'mc_value'				=>	$mc_value,
						'mc_type'				=>	$mc_type,
						'delivery_duration'		=>	$delivery_duration,
						'karigar'				=>	$karigar,
						'display_va'			=>  $display_va,
						'display_mc'			=>  $display_mc,
						'display_duration'		=>	$display_duration,
						'calculation_based_on'	=>	2
					);

					$insertId_W =$this->$model->insertData($insArray,'ret_supp_catalogue_weight');

					$image_uploaded = true;

					$uploaded_err_msg = "";

					foreach($_POST['images'] as $key => $imgs) {

						$is_default = $imgs['is_default'];

						$base64Img = $imgs['value'];

						$imgDecoded = base64_decode($base64Img);

						$file_type = $this->getImageMimeType($imgDecoded);

						$allowed_types = array('jpg', 'jpeg', 'png');

						if (!in_array($file_type, $allowed_types)) {
						
							$uploaded_err_msg =  "Invalid file type. Allowed types are: " . implode(',', $allowed_types);

							$image_uploaded = false;
					
							break; 
						
						} else { 

							$new_file_name = uniqid() . '.' . $file_type;

							$insArray = array(
									
								'id_supp_catalogue'	=>	$insertId,

								'image'				=>	$new_file_name,
							
								'date_add'			=>	$datetime,
							
								'is_default'		=>	$is_default

							);

							$suppinsertId = $this->$model->insertData($insArray,'supp_catalogue_images');

							if($suppinsertId > 0) {
		
								$image_name = $suppinsertId."-".$new_file_name;

								//$save_image = file_put_contents($file, $imgDecoded);

								$save_image = $this->save_image($supp_cat_img, $image_name, $imgDecoded);

								if(!$save_image) {

									$image_uploaded = false;

									$uploaded_err_msg =  "Error occured in uploading images. Please try again.";

								}
							
							} else {

								$image_uploaded = false;

								$uploaded_err_msg =  "Error occured in saving images. Please try again.";

								break;

							}

						}

					}

				}

				if($this->db->trans_status()==TRUE) {

					if($image_uploaded == true) {

						$this->db->trans_commit();

						$log_data = array(

							'id_log'        => $this->session->userdata('id_log'),
	
							'event_date'    => date("Y-m-d H:i:s"),
	
							'module'        => 'Supplier catalogue',
	
							'operation'     => 'Add',
	
							'record'        =>  $insertId,
	
							'remark'        => 'Supplier catalogue added successfully'
	
							);
	
						$this->log_model->log_detail('insert','',$log_data);

						$save_status = true;

						$message = "Supplier catalogue added successfully";

					} else {

						$this->db->trans_rollback();

						$message = $uploaded_err_msg;

					}

					

				} else {
				
					$this->db->trans_rollback();

					$message = "Error occured. Please try again later.";

				}

				$return_msg = array(

					"status" => $save_status,
				
					"message" => $message,

					"supp_cat_id" => $insertId,

					"design_code" => $design_code
				);

				echo json_encode($return_msg);

				break;

			case "update":
	
				$datetime = date("Y-m-d H:i:s");

				$save_status = false;

				$message = "";

				$supp_cat_img = self::SUPPIMG_PATH;

				/*echo "<pre>";
				print_r($_POST);
				echo "</pre>";exit;*/

				$design_code 	= $_POST['design_code'];

				$id_supp_catalogue 	= $_POST['id_supp_catalogue'];
				$product_id 		= $_POST['product_id'];
				$design_id 			= $_POST['design_id'];
				$id_sub_design 		= $_POST['subdesign_id'];
				$status				= $_POST['status'];
				$uid				= $this->session->userdata('uid');

				$updArray = array(
					'ctl_datetime'	=>	$datetime,
					'product_id'	=>	$product_id,
					'design_id'		=>	$design_id,
					'id_sub_design'	=>	$id_sub_design,
					'status'		=>	$status,
					'updated_on'	=>	$datetime,
					'updated_by'	=>	$uid
				);

				$this->db->trans_begin();

				$updateId = $this->$model->updateData($updArray, 'id_supp_catalogue', $id_supp_catalogue, 'ret_supp_catalogue');

				if($updateId > 0) {

					$weight 			= trim($_POST['weight']);
					$from_weight 		= trim($_POST['from_weight']);
					$to_weight 			= trim($_POST['to_weight']);
					$purity 			= trim(trim($_POST['purity']),",");
					$size 				= trim(trim($_POST['size']),",");
					$wastage 			= $_POST['wastage'];
					$display_va 		= $_POST['display_va'];
					$mc_value 			= $_POST['mc_value'];
					$mc_type 			= $_POST['mc_type'];
					$display_mc 		= $_POST['display_mc'];
					$delivery_duration 	= $_POST['delivery_duration'];
					$display_duration 	= $_POST['display_duration'];
					$karigar 			= trim(trim($_POST['karigar']),",");

					$insArray = array(
						'weight'				=>	$weight,
						'from_weight'			=>	$from_weight,
						'to_weight'				=>	$to_weight,
						'purity'				=>	$purity,
						'size'					=>	$size,
						'wastage'				=>	$wastage,
						'mc_value'				=>	$mc_value,
						'mc_type'				=>	$mc_type,
						'delivery_duration'		=>	$delivery_duration,
						'karigar'				=>	$karigar,
						'display_va'			=>  $display_va,
						'display_mc'			=>  $display_mc,
						'display_duration'		=>	$display_duration,
						'calculation_based_on'	=>	2
					);

					$insertId_W =$this->$model->updateData($insArray, 'id_supp_catalogue', $updateId,'ret_supp_catalogue_weight');

					$delImages = $this->$model->get_supp_cat_images($updateId);

					foreach($delImages as $delImgs) {

						$img_path = $supp_cat_img.$delImgs['id_supp_cat_img']."-".$delImgs['image'];

						unlink($img_path);

					}

					$this->$model->deleteData("id_supp_catalogue", $updateId, 'supp_catalogue_images');

					$image_uploaded = true;

					$uploaded_err_msg = "";

					foreach($_POST['images'] as $key => $imgs) {

						$is_default = $imgs['is_default'];

						$base64Img = $imgs['value'];

						$imgDecoded = base64_decode($base64Img);

						$file_type = $this->getImageMimeType($imgDecoded);

						$allowed_types = array('jpg', 'jpeg', 'png');

						if (!in_array($file_type, $allowed_types)) {
						
							$uploaded_err_msg =  "Invalid file type. Allowed types are: " . implode(',', $allowed_types);

							$image_uploaded = false;
					
							break; 
						
						} else { 

							$new_file_name = uniqid() . '.' . $file_type;

							$insArray = array(
									
								'id_supp_catalogue'	=>	$updateId,

								'image'				=>	$new_file_name,
							
								'date_add'			=>	$datetime,
							
								'is_default'		=>	$is_default

							);

							$suppinsertId = $this->$model->insertData($insArray,'supp_catalogue_images');

							if($suppinsertId > 0) {
		
								$image_name = $suppinsertId."-".$new_file_name;

								//$save_image = file_put_contents($file, $imgDecoded);

								$save_image = $this->save_image($supp_cat_img, $image_name, $imgDecoded);

								if(!$save_image) {

									$image_uploaded = false;

									$uploaded_err_msg =  "Error occured in uploading images. Please try again.";

								}
							
							} else {

								$image_uploaded = false;

								$uploaded_err_msg =  "Error occured in saving images. Please try again.";

								break;

							}

						}

					}

				}

				if($this->db->trans_status()==TRUE) {

					if($image_uploaded == true) {

						$this->db->trans_commit();

						$log_data = array(

                            'id_log'        => $this->session->userdata('id_log'),

                            'event_date'    => date("Y-m-d H:i:s"),

                            'module'        => 'Supplier catalogue',

                            'operation'     => 'Update',

                            'record'        =>  $updateId,

                            'remark'        => ' Supplier catalogueUpdated successfully'

                            );

                      $this->log_model->log_detail('insert','',$log_data);

						$save_status = true;

						$message = "Supplier catalogue added successfully";

					} else {

						$this->db->trans_rollback();

						$message = $uploaded_err_msg;

					}

				} else {
				
					$this->db->trans_rollback();

					$message = "Error occured. Please try again later.";

				}

				$return_msg = array(

					"status" => $save_status,
				
					"message" => $message,

					"supp_cat_id" => $updateId,

					"design_code" => $design_code
				);

				echo json_encode($return_msg);

				break;

			case 'delete' :

				$this->db->trans_begin();

				$imgpath = self::SUPPIMG_PATH;

				$delImages = $this->$model->get_supp_cat_images($id);

				$this->$model->deleteData('id_supp_catalogue',$id,'ret_supp_catalogue_weight');

				$this->$model->deleteData('id_supp_catalogue',$id,'ret_supp_catalogue');

				$this->$model->deleteData("id_supp_catalogue", $id, 'supp_catalogue_images');
				
				if( $this->db->trans_status()===TRUE) {

					foreach($delImages as $delImgs) {

						$img_path = $imgpath.$delImgs['id_supp_cat_img']."-".$delImgs['image'];

						unlink($img_path);

					}

					$this->db->trans_commit();

					$log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'    => date("Y-m-d H:i:s"),

						'module'        => 'Supplier catalogue',

						'operation'     => 'Delete',

						'record'        =>  $id,

						'remark'        => 'Supplier catalogue deleted successfully'

						);
						
				    $this->log_model->log_detail('insert','',$log_data);
					
					$this->session->set_flashdata('chit_alert', array('message' => 'Supplier catalogue deleted successfully','class' => 'success','title'=>'Settings'));	

				} else {
					
					$this->db->trans_rollback();

					$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Settings'));
				}

				redirect('admin_ret_supp_catalog/supplier_catalog/list');
				
				break;

			default: 
				
				$list = $this->$model->ajax_getSupplierCatList("","list");	 
				
				$access = $this->admin_settings_model->get_access('admin_ret_supp_catalog/supplier_catalog/list');
				
				$data = array(
								'list'  => $list,
								'access'=> $access
							);  
				
				echo json_encode($data);
		}
	}

	function save_image($imgpath, $imgname, $image) {

        if (!is_dir($imgpath)) {

        	mkdir($imgpath, 0777, TRUE);

        }

		$imgpath = $imgpath."/".$imgname;

		if(!file_exists($imgpath)) {

			file_put_contents($imgpath, $image);

			//$this->upload_img($imgpath,$tmpname);

			return true;

		} else {

			return true;

		}

	}

	function upload_img($dst, $img)
	{	

		if (($img_info = getimagesize($img)) === FALSE)
		{
			// die("Image not found or not an image");
			return false;
		}
		$width = $img_info[0];
		$height = $img_info[1];
		
		switch ($img_info[2]) {
		  case IMAGETYPE_GIF  : $src = imagecreatefromgif($img);
		  						$tmp = imagecreatetruecolor($width, $height);
		  						$kek=imagecolorallocate($tmp, 255, 255, 255);
					      		imagefill($tmp,0,0,$kek);
		  						break;
		  case IMAGETYPE_JPEG : $src = imagecreatefromjpeg($img); 
		  						$tmp = imagecreatetruecolor($width, $height);
		 						break;
		  case IMAGETYPE_PNG  : $src = imagecreatefrompng($img);
							    $tmp = imagecreatetruecolor($width, $height);
		  						$kek=imagecolorallocate($tmp, 255, 255, 255);
					     		imagefill($tmp,0,0,$kek);
					     		break;
		  default : //die("Unknown filetype");	
		  return false;
		  }		
		 
		  imagecopyresampled($tmp, $src, 0, 0, 0, 0, $width, $height, $width, $height);
		  imagejpeg($tmp, $dst);
		 
		  return true;
	}

	public function upload_defaultImg() {

		$model = "ret_supp_catalog_model";

		$id_supp_cat_img = $_POST["id_supp_cat_img"];

		$id_supp_cat = $_POST["id_supp_cat"];

		$is_default = $_POST["is_default"];

		$message = "";

		$status = false;

		if($id_supp_cat_img > 0 && ($is_default == 1 || $is_default == 0) && $id_supp_cat > 0) {

			$this->db->trans_begin();

			$updArray = array(

				"is_default" => 0

			);

			$updateId = $this->$model->updateData($updArray, 'id_supp_catalogue', $id_supp_cat, 'supp_catalogue_images');

			$updArray = array(

				"is_default" => $is_default

			);

			$updateId = $this->$model->updateData($updArray, 'id_supp_cat_img', $id_supp_cat_img, 'supp_catalogue_images');

			if($this->db->trans_status() == TRUE) {

				$this->db->trans_commit();

				$message = "Default image updated successfully";

				$status = true;

			} else {

				$this->db->trans_rollback();

				$message = "Error occured in updating default image. Please try again.";

			}

		} else {

			$message = "Required fields are missing";

		}

		$return_arr = array("status" => $status, "message" => $message);

		echo json_encode($return_arr);

	}

	public function delete_image() {

		$model = "ret_supp_catalog_model";

		$id_supp_cat_img = $_POST["id_supp_cat_img"];

		$img_name = $_POST["img_name"];

		$message = "";

		$status = false;

		$supp_cat_img = self::SUPPIMG_PATH;

		if($id_supp_cat_img > 0 && $img_name != "") {

			if($this->$model->deleteData("id_supp_cat_img", $id_supp_cat_img, 'supp_catalogue_images')) {

				$imgpath = $supp_cat_img."/".$id_supp_cat_img."-".$img_name;

				unlink($imgpath);

				$message = "Image deleted successfully";

				$status = true;

			} else {

				$message = "Error occured in deleting image. Please try again.";

				$status = false;

			}

		} else {

			$message = "Required fields are missing";

			$status = false;

		}

		$return_arr = array("status" => $status, "message" => $message);

		echo json_encode($return_arr);

	}

	public function get_catgory_id(){
		$model = "ret_supp_catalog_model";
        $pro_id=$this->input->post('pro_id');
		$data=$this->$model->get_catgory_id($pro_id);
		echo json_encode($data);
	}
	function profile_status($status,$id)
	{
		$data = array('status' => $status);
		$model="ret_supp_catalog_model";
		$status = $this->$model->updateData($data,'id_supp_catalogue',$id,'ret_supp_catalogue');
		if($status)
		{
			$this->session->set_flashdata('chit_alert',array('message'=>' Status updated as '.($status ? 'Active' : 'Inactive').' successfully.','class'=>'success','title'=>'Supplier Catalogue'));			
		}	
		else
		{
			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Supplier Catalogue'));
		}	
	//   echo	$this->db->last_query();exit;
		redirect('admin_ret_supp_catalog/supplier_catalog/list');
	}	

	function get_ActiveProducts()
	{
	    $model="ret_supp_catalog_model";
         $data=$this->$model->get_ActiveProducts($_POST);
         echo json_encode($data);
	}
	
	function get_Activedesign()
    {
    	$model="ret_supp_catalog_model";
    	$id_product=$this->input->post('id_product');
        $data=$this->$model->get_Activedesign($id_product);
    	echo json_encode($data);
    }
    
    function get_ActiveSubDesign()
    {
    	$model="ret_supp_catalog_model";
        $data=$this->$model->get_ActiveSubDesign($_POST);
    	echo json_encode($data);
    }
	public function base64ToFile($imgBase64){
	//	print_r($imgBase64);
		$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imgBase64)); // might not work on some systems, specify your temp path if system temp dir is not writeable
		$temp_file_path = tempnam(sys_get_temp_dir(), 'tempimg');
		file_put_contents($temp_file_path, $data);
		$image_info = getimagesize($temp_file_path); 
		
		$imgFile = array(
		     'name' => uniqid().'.'.preg_replace('!\w+/!', '', $image_info['mime']),
		     'tmp_name' => $temp_file_path,
		     'size'  => filesize($temp_file_path),
		     'error' => UPLOAD_ERR_OK,
		     'type'  => $image_info['mime'],
		);
		return $imgFile;
	}	

	function getSupplierCal_images($datas)
    {
		foreach($datas as $item) {

	   
		$path = 'assets/img/supplier/'.$item['id_supp_cat_img'].'-'.$item['image'] ;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
		//$base=base64_encode($data);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
		$item['src']=$base64;
		 $return_data[]=$item;
		}
		return $return_data;
    }

	function getBytesFromHexString($hexdata)
	{
	for($count = 0; $count < strlen($hexdata); $count+=2)
		$bytes[] = chr(hexdec(substr($hexdata, $count, 2)));
		
	return implode($bytes);
	}

	function getImageMimeType($imagedata)
	{
		$imagemimetypes = array( 
			"jpeg" => "FFD8", 
			"png" => "89504E470D0A1A0A", 
			"gif" => "474946",
			"bmp" => "424D", 
			"tiff" => "4949",
			"tiff" => "4D4D"
		);
		
		foreach ($imagemimetypes as $mime => $hexbytes)
		{
			$bytes = $this->getBytesFromHexString($hexbytes);
			if (substr($imagedata, 0, strlen($bytes)) == $bytes)
			return $mime;
		}
		
		return NULL;
	}
}	
?>