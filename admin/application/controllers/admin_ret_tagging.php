<?php

if( ! defined('BASEPATH')) exit('No direct script access allowed');

use Dompdf\Dompdf;

class Admin_ret_tagging extends CI_Controller

{

	const IMG_PATH  = 'assets/img/';

	const PROD_PATH  = 'assets/img/products/';

	const SERV_MODEL = "admin_usersms_model";

	const SETT_MOD	= "admin_settings_model";

	const DIGITMAP = array(
		'0' => 'J', '1' => 'Z', '2' => 'X', '3' => 'C', '4' => 'V',
		'5' => 'B', '6' => 'N', '7' => 'M', '8' => 'L', '9' => 'K'
	);

	const REVERSEMAP = array(
		'J' => '0', 'Z' => '1', 'X' => '2', 'C' => '3', 'V' => '4',
		'B' => '5', 'N' => '6', 'M' => '7', 'L' => '8', 'K' => '9'
	);

	function __construct()

	{

		parent::__construct();

		ini_set('date.timezone', 'Asia/Calcutta');

		$this->load->model('ret_tag_model');

		$this->load->model('admin_settings_model');

		$this->load->model(self::SETT_MOD);

		$this->load->model("sms_model");

		$this->load->model("log_model");

		$this->load->model("ret_brntransfer_model");

		$this->load->model('ret_billing_model');

		$this->load->model('ret_catalog_model');

		$this->load->model('ret_metal_process_model');

		$this->load->model(self::SERV_MODEL);

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

	function set_image($id,$img_path,$file)

	{

	 	if($_FILES[$file]['name'])

	   	 {

	        $img = $_FILES[$file]['tmp_name'];

			$status = $this->upload_img($file,$img_path,$img);

			return $status;

		 }

	}

    function upload_img( $outputImage,$dst, $img)

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

		$res = imagejpeg($tmp, $dst);

		return $res;

	}

	function rrmdir($path) {

     // Open the source directory to read in files

        $i = new DirectoryIterator($path);

        foreach($i as $f) {

            if($f->isFile()) {

                unlink($f->getRealPath());

            } else if(!$f->isDot() && $f->isDir()) {

                rrmdir($f->getRealPath());

            }

        }

        rmdir($path);

	}

	function remove_img($file,$id) {

		$path = self::PROD_PATH.$id."/".$file ;

		chmod(self::PROD_PATH.$id,0777);

		unlink($path);

		$model=self::CAT_MODEL;

		$status = $this->$model->delete_prodimage($file);

		if($status){

			echo "Picture removed successfully";

		}

	}

	public function base64ToFile($imgBase64){

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

	public function imgTobase64($path)

	{

		$type = pathinfo($path, PATHINFO_EXTENSION);

		$data = file_get_contents($path);

		$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

		return $base64;

	}

	/**

	* Tagging Functions Starts

	*/

	public function tagging($type="",$id="", $tag_print_id=""){

		$model = "ret_tag_model";

		$SETT_MOD = self::SETT_MOD;


		switch($type)

		{

			case 'add':

						$data['tag_prints'] = $tag_print_id;

						$data['tagging']= $this->$model->get_empty_record();

						$data['product_division']= $this->$model->getProductDivision();

						$data['uom']= $this->$model->getUOMDetails();

						$data['main_content'] = "tagging/form" ;

						$this->load->view('layout/template', $data);

			break;

			case 'list':

						$data['main_content'] = "tagging/tag_list" ;

						$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_tagging/tagging/list');

						$this->load->view('layout/template', $data);

						break;

		    case 'duplicate_print':

						$data['main_content'] = "tagging/re_print" ;

						$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_tagging/tagging/duplicate_print');


						$this->load->view('layout/template', $data);

			break;

			case 'tag_scan':

						$data['main_content'] = "tagging/tag_scan" ;

						$this->load->view('layout/template', $data);

			break;

			case 'tag_mark':

						$data['main_content'] = "tagging/tag_mark" ;

						$this->load->view('layout/template', $data);

			break;

			case 'tag_edit':

						$data['main_content'] = "tagging/tag_edit" ;

						$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_tagging/tagging/tag_edit');


						$this->load->view('layout/template', $data);

			break;

			case "tag_link":

			        $data['tagging']		= $this->$model->get_empty_record();

					$data['main_content'] = "tagging/tag_link" ;

		 			$this->load->view('layout/template', $data);

			break;

			case "tag_unlink":

				$profile    = $this->admin_settings_model->profileDB("get",$this->session->userdata('profile'));
				$data['order_unlink_otp']    = $profile['order_unlink_otp'];
                $data['main_content'] = "tagging/tag_unlink" ;
                $this->load->view('layout/template', $data);

            break;

			case "save":

						// echo "<pre>";

						// print_r(var_dump($_POST));

						// echo "</pre>";

						// exit;

						$addData = $_POST['lt_item'];

						//echo "<pre>"; print_r($addData); echo "</pre>"; exit;

						$tag_insert_id = '';

						$id_bt = NULL;

						$piece = 0;

						$gross_wt = 0;

						$less_wt = 0;

						$net_wt = 0;

						$trans_code = '';

						$toBranch = $addData['to_branch'];

					    $ref_no = time()."-".$this->session->userdata('uid');

						$is_lot_completed = 0;

						$dCData = $this->admin_settings_model->getBranchDayClosingData($addData['id_branch']);

						$tag_datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

						/*echo "<pre>";

						print_r($_POST);

						echo "<pre>";exit;*/

						//$fin_year=$this->$model->get_financialyear_by_status();

						//$tag_datetime= date('Y-m-d',strtotime(str_replace("/","-",$addData['tag_datetime'])));

						$this->db->trans_begin();

						foreach($addData['lot_no'] as $k => $v)

						{

							$piece 		= $piece + (isset($addData['no_of_piece'][$k]) ? $addData['no_of_piece'][$k] : 0);

							$gross_wt 	= $gross_wt + (isset($addData['gross_wt'][$k]) ? $addData['gross_wt'][$k] : 0);

							$less_wt 	= $less_wt + (isset($addData['less_wt'][$k]) ? $addData['less_wt'][$k] : 0);

							$net_wt 	= $net_wt + (isset($addData['net_wt'][$k]) ? $addData['net_wt'][$k] : 0);

						}

						// Add to Branch Transfer

						if($addData['id_branch'] != $addData['to_branch'] && $addData['to_branch'] != ""){

							$this->load->model("ret_brntransfer_model");

							$trans_code = $this->ret_brntransfer_model->trans_code_generator();

							$bt_data = array(

								'branch_trans_code'		=> $trans_code,

								'transfer_from_branch'	=> (isset($addData['id_branch']) ? $addData['id_branch'] : NULL),

								'transfer_to_branch'	=> (isset($addData['to_branch']) ? $addData['to_branch'] : NULL),

								'transfer_item_type'	=> 1,

								'pieces'				=> $piece,

								'grs_wt'				=> $gross_wt,

								'net_wt'				=> $net_wt,

								'added_type'			=> 2,

								'create_by'				=> $this->session->userdata('uid'),

								'created_time'			=> date('Y-m-d H:i:s'),

								'id_lot_inward_detail' 	=> (isset($_POST['id_lot_inward_detail']) ? $_POST['id_lot_inward_detail'] : 0)

							  );

							$id_bt = $this->$model->insertData($bt_data,'ret_branch_transfer');

						}

	 					foreach($addData['lot_no'] as $key => $val)

	 					{

	 					    //$ref_no=$this->$model->getLotRefNo($addData['lot_no'][$key]);

		 					//$tag_code = $this->$model->code_number_generator();

		 					$_FILES=[];

							$arrayTag = array(

								//'tag_code' 			=> $addData['product_short_code'][$key]."-".$tag_code,

								'current_branch' 	=> $addData['id_branch'],

								'id_branch' 		=> $addData['id_branch'],

								'cost_center' 		=> $addData['id_branch'],

								'tag_lot_id' 		=> $addData['lot_no'][$key],

								'product_id' 		=> $addData['lot_product'][$key],

								'design_id' 		=> $addData['lot_id_design'][$key],

								'id_sub_design' 	=> $addData['lot_id_sub_design'][$key],

								'design_for' 		=> 0,//$addData['design_for'][$key],

								'purity' 			=> $addData['purity'][$key],

								'id_orderdetails'=>(isset($addData['id_orderdetails'][$key]) ?($addData['id_orderdetails'][$key]!='' ? $addData['id_orderdetails'][$key]:NULL) :NULL),

								'id_lot_inward_detail' => $addData['id_lot_inward_detail'][$key],

								'size' 				=> ($addData['size'][$key]!='' ? ($addData['size'][$key]!='null' ? $addData['size'][$key]:NULL):NULL),

								'piece' 			=> (($addData['no_of_piece'][$key]!='') ? $addData['no_of_piece'][$key]:NULL),

								'gross_wt' 			=> (($addData['gross_wt'][$key]!='') ? $addData['gross_wt'][$key]:NULL),

								'uom_gross_wt' 		=> (($addData['gwt_uom_id'][$key]!='' && $addData['tag_cat_type'][$key] == 3) ? $addData['gwt_uom_id'][$key]:NULL),

								'less_wt' 			=> (($addData['less_wt'][$key]!='') ? $addData['less_wt'][$key]:NULL),

								'net_wt' 			=> (($addData['net_wt'][$key]!='') ? $addData['net_wt'][$key]:NULL),

								'calculation_based_on' => $addData['calculation_based_on'][$key],

								'retail_max_wastage_percent' => (($addData['wastage_percentage'][$key]!='') ? $addData['wastage_percentage'][$key]:NULL),

								'tag_mc_type' 		=> (($addData['id_mc_type'][$key]!='') ? $addData['id_mc_type'][$key]:NULL),

								'tag_mc_value' 	    => (($addData['making_charge'][$key]!='') ? $addData['making_charge'][$key]:0),

								'sell_rate' 		=> (($addData['sell_rate'][$key]!='') ? $addData['sell_rate'][$key]:NULL),

								'item_rate' 		=> (($addData['adjusted_item_rate'][$key]!='') ? $addData['adjusted_item_rate'][$key]:NULL),

								'sales_value' 		=> (($addData['sale_value'][$key]!='') ? $addData['sale_value'][$key]:NULL),

								'hu_id'             => (($addData['huid'][$key]!='') ? $addData['huid'][$key]:NULL),

								'hu_id2'            => (($addData['huid2'][$key]!='') ? $addData['huid2'][$key]:NULL),

								'image'				=> ((strlen($precious_imgs) > 0)?$precious_imgs : NULL),

								'tag_datetime'	  	=> $tag_datetime,

								'created_time'	  	=> date("Y-m-d H:i:s"),

								'created_by'      	=> $this->session->userdata('uid'),

								'ref_no'      		=> $ref_no,

								"cert_no"			=> (($addData['cert_no'][$key]!='') ? $addData['cert_no'][$key]:NULL),

								"cert_img"			=> $cert_img,

								'manufacture_code' 	=> $addData['manufacture_code'][$key],

								'style_code' 		=> $addData['style_code'][$key],

								'remarks' 			=> $addData['remarks'][$key],

								'narration' 		=> $addData['narration'][$key],

								'tag_purchase_cost'	=> isset($addData['tag_purchase_cost'][$key]) && trim($addData['tag_purchase_cost'][$key]) != "" ? $addData['tag_purchase_cost'][$key] : 0,

								'tag_type' 			=> !empty($addData['is_suspense_stock'][$key]) ? $addData['is_suspense_stock'][$key] : 0,

								'cat_type'        	=> $this->isEmptySetDefault($addData['tag_cat_type'][$key],NULL),

								'stone_calculation_based_on' => $addData['stone_calculation_based_on'][$key],

								'old_tag_id'        => $addData['remarks'][$key],

								'product_division' 	=> isset($addData['tag_product_division'][$key]) && trim($addData['tag_product_division'][$key]) != "" ? $addData['tag_product_division'][$key] : NULL,

								'id_section'       =>  (($addData['tag_section'][$key]!='' && $addData['tag_section'][$key]!=0 && $addData['tag_section'][$key]!='null') ? $addData['tag_section'][$key]:NULL),

								'tag_split_emp'=>(isset($addData['emp_select'][$key]) ?($addData['emp_select'][$key]!='' ? $addData['emp_select'][$key]:NULL) :NULL),

								'quality_id'        => $this->isEmptySetDefault($addData['quality_id'][$key],NULL),

								'lot_purchase_touch' => (isset($addData['lot_purchase_touch'][$key]) ? ($addData['lot_purchase_touch'][$key]!='' ? $addData['lot_purchase_touch'][$key]:NULL):NULL),

								'purchase_touch' => (isset($addData['purchase_touch'][$key]) ? ($addData['purchase_touch'][$key]!='' ? $addData['purchase_touch'][$key]:NULL):NULL),

								'lot_rate_calc_type' => (isset($addData['lot_rate_calc_type'][$key]) ? ($addData['lot_rate_calc_type'][$key]!='' ? $addData['lot_rate_calc_type'][$key]:NULL):NULL),

								'lot_rate' => (isset($addData['lot_rate'][$key]) ? ($addData['lot_rate'][$key]!='' ? $addData['lot_rate'][$key]:NULL):NULL),

								'lot_calc_type' => (isset($addData['lot_calc_type'][$key]) ? ($addData['lot_calc_type'][$key]!='' ? $addData['lot_calc_type'][$key]:NULL):NULL),

								/* 'lot_making_charge' => (isset($addData['lot_making_charge'][$key]) ? ($addData['lot_making_charge'][$key]!='' ? $addData['lot_making_charge'][$key]:NULL):NULL),

								'lot_mc_type' => (isset($addData['lot_mc_type'][$key]) ? ($addData['lot_mc_type'][$key]!='' ? $addData['lot_mc_type'][$key]:NULL):NULL),

								'lot_wastage_percentage' => (isset($addData['lot_wastage_percentage'][$key]) ? ($addData['lot_wastage_percentage'][$key]!='' ? $addData['lot_wastage_percentage'][$key]:NULL):NULL), */

								'lot_making_charge' => (isset($addData['pur_making_charge'][$key]) ? ($addData['pur_making_charge'][$key] != '' ? $addData['pur_making_charge'][$key] : NULL) : NULL),

								'lot_mc_type' => (isset($addData['pur_id_mc_type'][$key]) ? ($addData['pur_id_mc_type'][$key] != '' ? $addData['pur_id_mc_type'][$key] : NULL) : NULL),

								'lot_wastage_percentage' => (isset($addData['pur_wastage_percentage'][$key]) ? ($addData['pur_wastage_percentage'][$key] != '' ? $addData['pur_wastage_percentage'][$key] : NULL) : NULL),

								'is_new_arrival' 	=> isset($addData['is_new_arrival'][$key]) && trim($addData['is_new_arrival'][$key]) != "" ? $addData['is_new_arrival'][$key] : 0

						 	);

						//echo "<pre>"; print_r($arrayTag);exit;

	 					$insId = $this->$model->insertData($arrayTag,'ret_taging');

						$tag_insert_id = $insId;

						$tag_code='';

	 					//print_r($this->db->last_query());exit;

	 					if($insId)

	 					{

	 					    if($addData['tag_section'][$key]!=0 && $addData['tag_section'][$key]!='null')

                            {

                            	/*Insert into ret_section_tag_status_log table while creating new tag */

                            	$sectag_data=array(

                            		'tag_id'	  =>$insId,

                            		'date'		  =>$tag_datetime,

                            		'status'	  =>0,

                            		'from_branch'     =>NULL,

                            		'to_branch'	  =>$addData['id_branch'],

                            		'created_on'      =>date("Y-m-d H:i:s"),

                            		'created_by'      =>$this->session->userdata('uid'),

                            		'issuspensestock' => $arrayTag['tag_type'],

                            		'from_section'    => NULL,

                            		'to_section'      => $addData['tag_section'][$key],

                            	);

                            	$this->$model->insertData($sectag_data,'ret_section_tag_status_log');

                            	/*Insert into ret_section_tag_status_log table while creating new tag */

                            }

							//image upload

	 					    $tag_image_copy       = ($addData['tag_img_copy'][$key]);

                            $tag_image_isdefault  = ($addData['tag_img_default'][$key]);

                            $p_ImgData            = json_decode(rawurldecode($addData['tag_img'][$key]));

                            if(sizeof($p_ImgData) > 0){

                                $_FILES['precious']    =  array();

                                foreach($p_ImgData as $precious){

                                    $imgFile = $this->base64ToFile($precious->src);

                                    $_FILES['precious'][] = $imgFile;

                                }

                            }

                            if($tag_image_copy == 0) {  // No Copy Images

                               $img_arr = array();

                                if(!empty($_FILES)) {

                                        $folder =  self::IMG_PATH."tag/";

                                        if (!is_dir($folder)) {

                                            mkdir($folder, 0777, TRUE);

                                        }

                                        if(isset($_FILES['precious'])){

                                            $precious_imgs = "";

                                            // Image Table Update

                                            $arrayimg_tag   = array();

                                            foreach($_FILES['precious'] as $file_key => $file_val){

                                                $is_default  = 0;

                                                if($tag_image_isdefault == $file_key)

                                                {

                                                   $is_default  = 1;

                                                }

                                                if($file_val['tmp_name'])

                                                {

                                                    $img_name =  $insId."_". mt_rand(100001,999999).".jpg";

                                                    $path = $folder."/".$img_name;

                                                    $result = $this->upload_img('image',$path,$file_val['tmp_name']);

                                                    if($result){

                                                        $precious_imgs = $img_name;

                                                        $arrayimg_tag = array(

                                                         'tag_id'   => $insId,

                                                         'image'    => $img_name,

                                                         'date_add' => date("Y-m-d  H:i:s"),

                                                         'date_add' => date("Y-m-d  H:i:s"),

                                                         'is_default' => $is_default

                                                        );

                                                    $img_arr[]   = $arrayimg_tag;

                                                      $insImageId = $this->$model->insertData($arrayimg_tag,'ret_taging_images');

                                                    }

                                                }

                                            }

                                        }

                                    }

                            }

                            else if($tag_image_copy == 1) // Copy Images In Clone Without Change

                            {

                                // Image Table Update

                                  $arrayimg_tag   = array();

                                  foreach($img_arr as $tag_val)

                                  {

                                     $arrayimg_tag = array(

                                                         'tag_id'   => $insId,

                                                         'image'    => $tag_val['image'],

                                                         'date_add' => date("Y-m-d  H:i:s"),

                                                         'date_add' => date("Y-m-d  H:i:s"),

                                                         'is_default' =>   $tag_val['is_default']

                                                        );

                                      $insImageId   = $this->$model->insertData($arrayimg_tag,'ret_taging_images');

                                  }

                            }

                            else if($tag_image_copy == 2) // Copy Images But Change Images Add Operation OR Remove Option

                            {

                                if(!empty($_FILES)) {

                                        $folder =  self::IMG_PATH."tag/";

                                        if (!is_dir($folder)) {

                                            mkdir($folder, 0777, TRUE);

                                        }

                                        if(isset($_FILES['precious'])){

                                            $precious_imgs = "";

                                            // Image Table Update

                                            $arrayimg_tag   = array();

                                            foreach($_FILES['precious'] as $file_key => $file_val){

                                                $is_default  = 0;

                                                if($tag_image_isdefault == $file_key)

                                                {

                                                   $is_default  = 1;

                                                }

                                                if($file_val['tmp_name'])

                                                {

                                                    $img_name =  $insId."_". mt_rand(100001,999999).".jpg";

                                                    $path = $folder."/".$img_name;

                                                    $result = $this->upload_img('image',$path,$file_val['tmp_name']);

                                                    if($result){

                                                        $precious_imgs = $img_name;

                                                        $arrayimg_tag = array(

                                                         'tag_id'   => $insId,

                                                         'image'    => $img_name,

                                                         'date_add' => date("Y-m-d  H:i:s"),

                                                         'date_add' => date("Y-m-d  H:i:s"),

                                                         'is_default' => $is_default

                                                        );

                                                      $insImageId = $this->$model->insertData($arrayimg_tag,'ret_taging_images');

                                                    }

                                                }

                                            }

                                        }

                                    }

                            }//exit;

	 					    //image upload

	 					    $log_data=array(

	 					    	'tag_id'	  =>$insId,

	 					    	'date'		  =>$tag_datetime,

	 					    	'status'	  =>0,

	 					    	'from_branch' =>NULL,

	 					    	'to_branch'	  =>$addData['id_branch'],

	 					    	'created_on'  =>date("Y-m-d H:i:s"),

								'created_by'  =>$this->session->userdata('uid'),

								'issuspensestock' => $arrayTag['tag_type']

	 					    );

	 					    $this->$model->insertData($log_data,'ret_taging_status_log');

	 					    //$code_number=str_pad($insId, 5, '0', STR_PAD_LEFT);

	 					    //Tag Code Generation

							$catArr = $this->$model->get_category_from_productid($arrayTag['product_id']);

							$catcode = $catArr['cat_code'];

							$product_short_code = $catArr['product_short_code'];

							$curr_year = date("y");

	 					    $tagCode = $this->$model->getlastTagCode($arrayTag['product_id']);

	 					    $tag_no  = $this->generateTagCode($tagCode);

							//$tag_code=$catcode.$curr_year.$product_short_code.'-'.$tag_no;

							$tag_code=$product_short_code.'-'.$tag_no;

	 						$this->$model->updateData(array('tag_code'=>$tag_code),'tag_id',$insId,'ret_taging');

	 						//Tag Code Generation

	 						//Taggig code log data

	 						$tag_log_data[]=array('last_tag_code'=>$tagCode,'new_tag_code'=>$tag_no,'tag_id'=>$insId,'date_time'=>date("Y-m-d H:i:s"),'ip_address'=>$this->session->userdata('ip_address'));

	 						// Add to BT items

	 						if($id_bt != NULL){

								$items = array(

										'transfer_id'	=> $id_bt,

										'tag_id'		=> $insId,

										'id_lot_inward_detail'	=> $addData['id_lot_inward_detail'][$key],

										);

								$this->$model->insertData($items,'ret_brch_transfer_tag_items');

							}

	 						// Stone Details

		 					if($addData['stone_details'][$key])

							{

								$stone_details=json_decode($addData['stone_details'][$key],true);

								foreach($stone_details as $stone)

								{

									$stone_data=array(

													'tag_id'                =>$insId,

													'pieces'                =>$stone['stone_pcs'],

													'wt'                    =>$stone['stone_wt'],

													'stone_id'              =>$stone['stone_id'],

													'uom_id'                =>$stone['stone_uom_id'],

													'amount'                =>$stone['stone_price'],

													'rate_per_gram'         =>$stone['stone_rate'],

													'is_apply_in_lwt'       =>$stone['show_in_lwt'],

													'stone_cal_type'        =>$stone['stone_cal_type'],

													'stone_quality_id'      =>$stone['stone_quality_id'],

													'pur_rate'      =>$stone['stn_pur_rate'],

													'pur_cost'      =>$stone['stn_pur_cost'],

													);

									$stoneInsert = $this->$model->insertData($stone_data,'ret_taging_stone');

								}

							}

							if($addData['huid_details'][$key])

							{

								$huid_details=json_decode($addData['huid_details'][$key],true);

								foreach($huid_details as $huid)

								{

									$huid_data=array(

													'tag_id'                =>$insId,

													'huid'                  =>$huid['huid'],

													'created_on'            =>date("Y-m-d H:i:s"),

													'created_by'            =>$this->session->userdata('uid'),

													);

									$huidInsert = $this->$model->insertData($huid_data,'ret_taging_huid');

								}

							}

								//update lot table if tag is completed

							$total_tag=$this->$model->get_tagged_details($addData['id_lot_inward_detail'][$key]);

							if($total_tag['total_pieces']==$total_tag['tagged_pieces'])

							{

							   $lot['tag_status']=1;

							   $tagInsert= $this->$model->updateData($lot,'id_lot_inward_detail',$addData['id_lot_inward_detail'][$key],'ret_lot_inwards_detail');

							   $is_lot_completed = 1;

							}

							if($addData['charges'][$key])

							{

								$charges = (array)json_decode($addData['charges'][$key], true);

								foreach($charges as $charge)

								{

									$charge_data=array(

													'tag_id'                =>$insId,

													'charge_id'             =>$charge['charge_id'],

													'charge_value'          =>$charge['charge_value'],

													);

									$this->$model->insertData($charge_data,'ret_taging_charges');

								}

							}

							if($addData['attributes'][$key])

							{

								$attributes = (array)json_decode($addData['attributes'][$key], true);

								foreach($attributes as $attrs)

								{

									if($attrs['attr_id'] > 0 && $attrs['attr_val_id'] > 0) {

										$attr_data	=	array(

															'id_tagging'    =>	$insId,

															'attr_id'     	=>	$attrs['attr_id'],

															'attr_val_id' 	=>	$attrs['attr_val_id'],

															'created_on'  	=>	date("Y-m-d H:i:s"),

															'created_by'  	=>	$this->session->userdata('uid')

														);

										$this->$model->insertData($attr_data,'ret_tagging_attributes');

									}

								}

							}

							if($addData['othermetals'][$key])

							{

								$othermetals = (array)json_decode($addData['othermetals'][$key], true);

								foreach($othermetals as $othrs)

								{

									$oth_metal_data	=	array(

															'tag_other_itm_tag_id'     =>	$insId,

															'tag_other_itm_metal_id'   =>	$othrs['id_metal'],

															'tag_other_itm_pur_id' 		=>	$othrs['id_purity'],

															'tag_other_itm_grs_weight'	=>	$othrs['nwt'],

															'tag_other_itm_wastage' 	=>	$othrs['wastage_perc'],

															'tag_other_itm_uom'     	=>	1,

															'tag_other_itm_cal_type' 	=>	$othrs['calc_type'],

															'tag_other_itm_mc' 			=>	$othrs['making_charge'],

															'tag_other_itm_rate' 		=>	$othrs['rate_per_gram'],

															'tag_other_itm_pcs'     	=>	1,

															'tag_other_itm_amount' 		=>	$othrs['amount'],

														);

									$this->$model->insertData($oth_metal_data,'ret_tag_other_metals');

								}

							}

						}

					}

/*echo "<pre>";print_r($_POST);

echo "<pre>";print_r($lot_no);exit;*/

					if($this->db->trans_status()===TRUE)

					{

					    $log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'Tag',

                        	'operation'   	=> 'Add',

                        	'record'        => $ref_no,

                        	'remark'       	=> 'Tags created successfully.Ref no is'.$ref_no.'.'

                        );

                        $this->log_model->log_detail('insert','',$log_data);

						$this->db->trans_commit();

						$log_path = 'tagging_log/'.date("Y-m-d").'/'.$this->session->userdata('uid');

                        if (!is_dir($log_path))

                        {

                            mkdir($log_path, 0777, TRUE);

                        }

						 $log_path = $log_path.'/post_data.txt';

						$log_detail=json_encode($tag_log_data);

						file_put_contents($log_path,$log_detail,FILE_APPEND | LOCK_EX);

						$responseData=array('status'=>true,'message'=>'Tagging added successfully','trans_code'=>$trans_code,'ref_no'=>$ref_no,'to_branch'=>$toBranch,'tag_id'=>$insId,'tag_code'=>$tag_code, 'is_lot_completed' => $is_lot_completed);

						$this->session->set_flashdata('chit_alert',array('message'=>'Tagging added successfully','class'=>'success','title'=>'Add Tagging'));

					}

					else

					{

						echo $this->db->last_query();

						echo $this->db->_error_message();exit;

						$this->db->trans_rollback();

						$responseData=array('status'=>false,'message'=>'Unable to proceed the requested process','trans_code'=>'','ref_no'=>'','to_branch'=>'');

						$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Add Tagging'));

					}

					echo json_encode($responseData);

					/*if($trans_code != '') {

						if(count($addData['lot_no']) == 1 && $tag_insert_id != '')

							redirect('admin_ret_tagging/tagging/add/0/'.$tag_insert_id);

						else

						  	redirect('admin_ret_tagging/bt_tag_list/'.$trans_code.'/'.$ref_no.'/'.$toBranch);

					} else {

						//redirect('admin_ret_tagging/tagging/list');

						redirect('admin_ret_tagging/tagging/add/0/'.$tag_insert_id);

					}*/

				break;

			case "save_bulk_tag":

					/*echo "<pre>";

					print_r($_POST);

					echo "</pre>";

					exit;*/

					$addData = $_POST['lt_item'];

					$tag_bulk=$addData['bulk_tag'];

					for($i=0;$i<$tag_bulk;$i++){

					//echo "<pre>"; print_r($addData); echo "</pre>"; exit;

					$tag_insert_id = '';

					$id_bt = NULL;

					$piece = 0;

					$gross_wt = 0;

					$less_wt = 0;

					$net_wt = 0;

					$trans_code = '';

					$toBranch = $addData['to_branch'];

					$ref_no = time()."-".$this->session->userdata('uid');

					$is_lot_completed = 0;

					$dCData = $this->admin_settings_model->getBranchDayClosingData($addData['id_branch']);

					$tag_datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

					/*echo "<pre>";

					print_r($_POST);

					echo "<pre>";exit;*/

					//$fin_year=$this->$model->get_financialyear_by_status();

					//$tag_datetime= date('Y-m-d',strtotime(str_replace("/","-",$addData['tag_datetime'])));

					$this->db->trans_begin();

					foreach($addData['lot_no'] as $k => $v)

					{

						$piece 		= $piece + (isset($addData['no_of_piece'][$k]) ? $addData['no_of_piece'][$k] : 0);

						$gross_wt 	= $gross_wt + (isset($addData['gross_wt'][$k]) ? $addData['gross_wt'][$k] : 0);

						$less_wt 	= $less_wt + (isset($addData['less_wt'][$k]) ? $addData['less_wt'][$k] : 0);

						$net_wt 	= $net_wt + (isset($addData['net_wt'][$k]) ? $addData['net_wt'][$k] : 0);

					}

					// Add to Branch Transfer

					if($addData['id_branch'] != $addData['to_branch'] && $addData['to_branch'] != ""){

						$this->load->model("ret_brntransfer_model");

						$trans_code = $this->ret_brntransfer_model->trans_code_generator();

						$bt_data = array(

							'branch_trans_code'		=> $trans_code,

							'transfer_from_branch'	=> (isset($addData['id_branch']) ? $addData['id_branch'] : NULL),

							'transfer_to_branch'	=> (isset($addData['to_branch']) ? $addData['to_branch'] : NULL),

							'transfer_item_type'	=> 1,

							'pieces'				=> $piece,

							'grs_wt'				=> $gross_wt,

							'net_wt'				=> $net_wt,

							'added_type'			=> 2,

							'create_by'				=> $this->session->userdata('uid'),

							'created_time'			=> date('Y-m-d H:i:s'),

							'id_lot_inward_detail' 	=> (isset($_POST['id_lot_inward_detail']) ? $_POST['id_lot_inward_detail'] : 0)

						  );

						$id_bt = $this->$model->insertData($bt_data,'ret_branch_transfer');

					}

					 foreach($addData['lot_no'] as $key => $val)

					 {

						 //$ref_no=$this->$model->getLotRefNo($addData['lot_no'][$key]);

						 //$tag_code = $this->$model->code_number_generator();

						 $_FILES=[];

						$arrayTag = array(

							//'tag_code' 			=> $addData['product_short_code'][$key]."-".$tag_code,

							'current_branch' 	=> $addData['id_branch'],

							'id_branch' 		=> $addData['id_branch'],

							'cost_center' 		=> $addData['id_branch'],

							'tag_lot_id' 		=> $addData['lot_no'][$key],

							'product_id' 		=> $addData['lot_product'][$key],

							'design_id' 		=> $addData['lot_id_design'][$key],

							'id_sub_design' 	=> $addData['lot_id_sub_design'][$key],

							'design_for' 		=> 0,//$addData['design_for'][$key],

							'purity' 			=> $addData['purity'][$key],

							'id_orderdetails'=>(isset($addData['id_orderdetails'][$key]) ?($addData['id_orderdetails'][$key]!='' ? $addData['id_orderdetails'][$key]:NULL) :NULL),

							'id_lot_inward_detail' => $addData['id_lot_inward_detail'][$key],

							'size' 				=> ($addData['size'][$key]!='' ? ($addData['size'][$key]!='null' ? $addData['size'][$key]:NULL):NULL),

							'piece' 			=> (($addData['no_of_piece'][$key]!='') ? $addData['no_of_piece'][$key]:NULL),

							'gross_wt' 			=> (($addData['gross_wt'][$key]!='') ? $addData['gross_wt'][$key]:NULL),

							'uom_gross_wt' 		=> (($addData['gwt_uom_id'][$key]!='' && $addData['tag_cat_type'][$key] == 3) ? $addData['gwt_uom_id'][$key]:NULL),

							'less_wt' 			=> (($addData['less_wt'][$key]!='') ? $addData['less_wt'][$key]:NULL),

							'net_wt' 			=> (($addData['net_wt'][$key]!='') ? $addData['net_wt'][$key]:NULL),

							'calculation_based_on' => $addData['calculation_based_on'][$key],

							'retail_max_wastage_percent' => (($addData['wastage_percentage'][$key]!='') ? $addData['wastage_percentage'][$key]:NULL),

							'tag_mc_type' 		=> (($addData['id_mc_type'][$key]!='') ? $addData['id_mc_type'][$key]:NULL),

							'tag_mc_value' 	    => (($addData['making_charge'][$key]!='') ? $addData['making_charge'][$key]:0),

							'sell_rate' 		=> (($addData['sell_rate'][$key]!='') ? $addData['sell_rate'][$key]:NULL),

							'item_rate' 		=> (($addData['adjusted_item_rate'][$key]!='') ? $addData['adjusted_item_rate'][$key]:NULL),

							'sales_value' 		=> (($addData['sale_value'][$key]!='') ? $addData['sale_value'][$key]:NULL),

							'hu_id'             => (($addData['huid'][$key]!='') ? $addData['huid'][$key]:NULL),

							'hu_id2'            => (($addData['huid2'][$key]!='') ? $addData['huid2'][$key]:NULL),

							'image'				=> ((strlen($precious_imgs) > 0)?$precious_imgs : NULL),

							'tag_datetime'	  	=> $tag_datetime,

							'created_time'	  	=> date("Y-m-d H:i:s"),

							'created_by'      	=> $this->session->userdata('uid'),

							'ref_no'      		=> $ref_no,

							"cert_no"			=> (($addData['cert_no'][$key]!='') ? $addData['cert_no'][$key]:NULL),

							"cert_img"			=> $cert_img,

							'manufacture_code' 	=> $addData['manufacture_code'][$key],

							'style_code' 		=> $addData['style_code'][$key],

							'narration' 		=>(($addData['narration'][$key]!='') ? $addData['narration'][$key]:NULL),

							'remarks' 			=>(($addData['remarks'][$key]!='') ? $addData['remarks'][$key]:NULL),

							'tag_purchase_cost'	=> isset($addData['tag_purchase_cost'][$key]) && trim($addData['tag_purchase_cost'][$key]) != "" ? $addData['tag_purchase_cost'][$key] : 0,

							'tag_type' 			=> !empty($addData['is_suspense_stock'][$key]) ? $addData['is_suspense_stock'][$key] : 0,

							'cat_type'        	=> $addData['tag_cat_type'][$key],

							'stone_calculation_based_on' => $addData['stone_calculation_based_on'][$key],

							'old_tag_id'        =>(($addData['remarks'][$key]!='') ? $addData['remarks'][$key]:NULL),

							'product_division' 	=> isset($addData['tag_product_division'][$key]) && trim($addData['tag_product_division'][$key]) != "" ? $addData['tag_product_division'][$key] : NULL,

							'id_section'       =>  (($addData['tag_section'][$key]!='' && $addData['tag_section'][$key]!=0) ? $addData['tag_section'][$key]:NULL),

							'is_new_arrival' 	=> isset($addData['is_new_arrival'][$key]) && trim($addData['is_new_arrival'][$key]) != "" ? $addData['is_new_arrival'][$key] : 0,

							'tag_purchase_cost'	=> isset($addData['tag_purchase_cost'][$key]) && trim($addData['tag_purchase_cost'][$key]) != "" ? $addData['tag_purchase_cost'][$key] : 0,

							'lot_purchase_touch' => (isset($addData['lot_purchase_touch'][$key]) ? ($addData['lot_purchase_touch'][$key]!='' ? $addData['lot_purchase_touch'][$key]:NULL):NULL),

							'purchase_touch' => (isset($addData['purchase_touch'][$key]) ? ($addData['purchase_touch'][$key]!='' ? $addData['purchase_touch'][$key]:NULL):NULL),

							'lot_rate_calc_type' => (isset($addData['lot_rate_calc_type'][$key]) ? ($addData['lot_rate_calc_type'][$key]!='' ? $addData['lot_rate_calc_type'][$key]:NULL):NULL),

							'lot_rate' => (isset($addData['lot_rate'][$key]) ? ($addData['lot_rate'][$key]!='' ? $addData['lot_rate'][$key]:NULL):NULL),

							'lot_calc_type' => (isset($addData['lot_calc_type'][$key]) ? ($addData['lot_calc_type'][$key]!='' ? $addData['lot_calc_type'][$key]:NULL):NULL),

							'lot_making_charge' => (isset($addData['lot_making_charge'][$key]) ? ($addData['lot_making_charge'][$key]!='' ? $addData['lot_making_charge'][$key]:NULL):NULL),

							'lot_mc_type' => (isset($addData['lot_mc_type'][$key]) ? ($addData['lot_mc_type'][$key]!='' ? $addData['lot_mc_type'][$key]:NULL):NULL),

							'lot_wastage_percentage' => (isset($addData['lot_wastage_percentage'][$key]) ? ($addData['lot_wastage_percentage'][$key]!='' ? $addData['lot_wastage_percentage'][$key]:NULL):NULL),

						);

					//echo "<pre>"; print_r($arrayTag);exit;

					 $insId = $this->$model->insertData($arrayTag,'ret_taging');

					$tag_insert_id = $insId;

					$tag_code='';

					 //print_r($this->db->last_query());exit;

					 if($insId)

					 {

						//image upload

						 $tag_image_copy       = ($addData['tag_img_copy'][$key]);

						$tag_image_isdefault  = ($addData['tag_img_default'][$key]);

						$p_ImgData            = json_decode(rawurldecode($addData['tag_img'][$key]));

						if(sizeof($p_ImgData) > 0){

							$_FILES['precious']    =  array();

							foreach($p_ImgData as $precious){

								$imgFile = $this->base64ToFile($precious->src);

								$_FILES['precious'][] = $imgFile;

							}

						}

						if($tag_image_copy == 0) {  // No Copy Images

						   $img_arr = array();

							if(!empty($_FILES)) {

									$folder =  self::IMG_PATH."tag/";

									if (!is_dir($folder)) {

										mkdir($folder, 0777, TRUE);

									}

									if(isset($_FILES['precious'])){

										$precious_imgs = "";

										// Image Table Update

										$arrayimg_tag   = array();

										foreach($_FILES['precious'] as $file_key => $file_val){

											$is_default  = 0;

											if($tag_image_isdefault == $file_key)

											{

											   $is_default  = 1;

											}

											if($file_val['tmp_name'])

											{

												$img_name =  $insId."_". mt_rand(100001,999999).".jpg";

												$path = $folder."/".$img_name;

												$result = $this->upload_img('image',$path,$file_val['tmp_name']);

												if($result){

													$precious_imgs = $img_name;

													$arrayimg_tag = array(

													 'tag_id'   => $insId,

													 'image'    => $img_name,

													 'date_add' => date("Y-m-d  H:i:s"),

													 'date_add' => date("Y-m-d  H:i:s"),

													 'is_default' => $is_default

													);

												$img_arr[]   = $arrayimg_tag;

												  $insImageId = $this->$model->insertData($arrayimg_tag,'ret_taging_images');

												}

											}

										}

									}

								}

						}

						else if($tag_image_copy == 1) // Copy Images In Clone Without Change

						{

							// Image Table Update

							  $arrayimg_tag   = array();

							  foreach($img_arr as $tag_val)

							  {

								 $arrayimg_tag = array(

													 'tag_id'   => $insId,

													 'image'    => $tag_val['image'],

													 'date_add' => date("Y-m-d  H:i:s"),

													 'date_add' => date("Y-m-d  H:i:s"),

													 'is_default' =>   $tag_val['is_default']

													);

								  $insImageId   = $this->$model->insertData($arrayimg_tag,'ret_taging_images');

							  }

						}

						else if($tag_image_copy == 2) // Copy Images But Change Images Add Operation OR Remove Option

						{

							if(!empty($_FILES)) {

									$folder =  self::IMG_PATH."tag/";

									if (!is_dir($folder)) {

										mkdir($folder, 0777, TRUE);

									}

									if(isset($_FILES['precious'])){

										$precious_imgs = "";

										// Image Table Update

										$arrayimg_tag   = array();

										foreach($_FILES['precious'] as $file_key => $file_val){

											$is_default  = 0;

											if($tag_image_isdefault == $file_key)

											{

											   $is_default  = 1;

											}

											if($file_val['tmp_name'])

											{

												$img_name =  $insId."_". mt_rand(100001,999999).".jpg";

												$path = $folder."/".$img_name;

												$result = $this->upload_img('image',$path,$file_val['tmp_name']);

												if($result){

													$precious_imgs = $img_name;

													$arrayimg_tag = array(

													 'tag_id'   => $insId,

													 'image'    => $img_name,

													 'date_add' => date("Y-m-d  H:i:s"),

													 'date_add' => date("Y-m-d  H:i:s"),

													 'is_default' => $is_default

													);

												  $insImageId = $this->$model->insertData($arrayimg_tag,'ret_taging_images');

												}

											}

										}

									}

								}

						}//exit;

						 //image upload

						 $log_data=array(

							 'tag_id'	  =>$insId,

							 'date'		  =>$tag_datetime,

							 'status'	  =>0,

							 'from_branch' =>NULL,

							 'to_branch'	  =>$addData['id_branch'],

							 'created_on'  =>date("Y-m-d H:i:s"),

							'created_by'  =>$this->session->userdata('uid'),

							'issuspensestock' => $arrayTag['tag_type']

						 );

						 $this->$model->insertData($log_data,'ret_taging_status_log');

						if($addData['tag_section'][$key]!='')

                        {

                        	/*Insert into ret_section_tag_status_log table while creating new tag */

                        	$sectag_data=array(

                        		'tag_id'	        => $insId,

                        		'date'		        => $tag_datetime,

                        		'status'	        => 0,

                        		'from_branch'       => NULL,

                        		'to_branch'	        => $addData['id_branch'],

                        		'created_on'        => date("Y-m-d H:i:s"),

                        		'created_by'        => $this->session->userdata('uid'),

                        		'issuspensestock'   => $arrayTag['tag_type'],

                        		'from_section'      => NULL,

                        		'to_section'        => $addData['tag_section'][$key],

                        	);

                        	$this->$model->insertData($sectag_data,'ret_section_tag_status_log');

                        	/*Insert into ret_section_tag_status_log table while creating new tag */

                        }

						 //$code_number=str_pad($insId, 5, '0', STR_PAD_LEFT);

						 //Tag Code Generation

						$catArr = $this->$model->get_category_from_productid($arrayTag['product_id']);

						$catcode = $catArr['cat_code'];

						$product_short_code = $catArr['product_short_code'];

						$curr_year = date("y");

						 $tagCode = $this->$model->getlastTagCode($arrayTag['product_id']);

						 $tag_no  = $this->generateTagCode($tagCode);

						//$tag_code=$catcode.$curr_year.$product_short_code.'-'.$tag_no;

						$tag_code=$product_short_code.'-'.$tag_no;

						 $this->$model->updateData(array('tag_code'=>$tag_code),'tag_id',$insId,'ret_taging');

						 //Tag Code Generation

						 //Taggig code log data

						 $tag_log_data[]=array('last_tag_code'=>$tagCode,'new_tag_code'=>$tag_no,'tag_id'=>$insId,'date_time'=>date("Y-m-d H:i:s"),'ip_address'=>$this->session->userdata('ip_address'));

						 // Add to BT items

						 if($id_bt != NULL){

							$items = array(

									'transfer_id'	=> $id_bt,

									'tag_id'		=> $insId,

									'id_lot_inward_detail'	=> $addData['id_lot_inward_detail'][$key],

									);

							$this->$model->insertData($items,'ret_brch_transfer_tag_items');

						}

						 // Stone Details

						 if($addData['stone_details'][$key])

						{

							$stone_details=json_decode($addData['stone_details'][$key],true);

							foreach($stone_details as $stone)

							{

								$stone_data=array(

												'tag_id'                =>$insId,

												'pieces'                =>$stone['stone_pcs'],

												'wt'                    =>$stone['stone_wt'],

												'stone_id'              =>$stone['stone_id'],

												'uom_id'                =>$stone['stone_uom_id'],

												'amount'                =>$stone['stone_price'],

												'rate_per_gram'         =>$stone['stone_rate'],

												'is_apply_in_lwt'       =>$stone['show_in_lwt'],

												'stone_cal_type'        =>$stone['stone_cal_type'],

												'pur_rate'      		=>$stone['stn_pur_rate'],

												'pur_cost'      		=>$stone['stn_pur_cost'],

												);

								$stoneInsert = $this->$model->insertData($stone_data,'ret_taging_stone');

							}

						}

							//update lot table if tag is completed

						$total_tag=$this->$model->get_tagged_details($addData['id_lot_inward_detail'][$key]);

						if($total_tag['total_pieces']==$total_tag['tagged_pieces'])

						{

						   $lot['tag_status']=1;

						   $tagInsert= $this->$model->updateData($lot,'id_lot_inward_detail',$addData['id_lot_inward_detail'][$key],'ret_lot_inwards_detail');

						   $is_lot_completed = 1;

						}

						if($addData['charges'][$key])

						{

							$charges = (array)json_decode($addData['charges'][$key], true);

							foreach($charges as $charge)

							{

								$charge_data=array(

												'tag_id'                =>$insId,

												'charge_id'             =>$charge['charge_id'],

												'charge_value'          =>$charge['charge_value'],

												);

								$this->$model->insertData($charge_data,'ret_taging_charges');

							}

						}

						if($addData['attributes'][$key])

						{

							$attributes = (array)json_decode($addData['attributes'][$key], true);

							foreach($attributes as $attrs)

							{

								if($attrs['attr_id'] > 0 && $attrs['attr_val_id'] > 0) {

									$attr_data	=	array(

														'id_tagging'    =>	$insId,

														'attr_id'     	=>	$attrs['attr_id'],

														'attr_val_id' 	=>	$attrs['attr_val_id'],

														'created_on'  	=>	date("Y-m-d H:i:s"),

														'created_by'  	=>	$this->session->userdata('uid')

													);

									$this->$model->insertData($attr_data,'ret_tagging_attributes');

								}

							}

						}

						if($addData['othermetals'][$key])

						{

							$othermetals = (array)json_decode($addData['othermetals'][$key], true);

							foreach($othermetals as $othrs)

							{

								$oth_metal_data	=	array(

														'tag_other_itm_tag_id'     =>	$insId,

														'tag_other_itm_metal_id'   =>	$othrs['id_metal'],

														'tag_other_itm_pur_id' 		=>	$othrs['id_purity'],

														'tag_other_itm_grs_weight'	=>	$othrs['nwt'],

														'tag_other_itm_wastage' 	=>	$othrs['wastage_perc'],

														'tag_other_itm_uom'     	=>	1,

														'tag_other_itm_cal_type' 	=>	$othrs['calc_type'],

														'tag_other_itm_mc' 			=>	$othrs['making_charge'],

														'tag_other_itm_rate' 		=>	$othrs['rate_per_gram'],

														'tag_other_itm_pcs'     	=>	1,

														'tag_other_itm_amount' 		=>	$othrs['amount'],

													);

								$this->$model->insertData($oth_metal_data,'ret_tag_other_metals');

							}

						}

					}

				}

                /*echo "<pre>";print_r($_POST);

                echo "<pre>";print_r($lot_no);exit;*/

				if($this->db->trans_status()===TRUE)

				{

					$log_data = array(

						'id_log'        => $this->session->userdata('id_log'),

						'event_date'	=> date("Y-m-d H:i:s"),

						'module'      	=> 'Tag',

						'operation'   	=> 'Add',

						'record'        => $ref_no,

						'remark'       	=> 'Tags created successfully.Ref no is'.$ref_no.'.'

					);

					$this->log_model->log_detail('insert','',$log_data);

					$this->db->trans_commit();

					$log_path = 'tagging_log/'.date("Y-m-d").'/'.$this->session->userdata('uid');

					if (!is_dir($log_path))

					{

						mkdir($log_path, 0777, TRUE);

					}

					 $log_path = $log_path.'/post_data.txt';

					$log_detail=json_encode($tag_log_data);

					file_put_contents($log_path,$log_detail,FILE_APPEND | LOCK_EX);

					//Generate TagQR Code

					$tags[]=$insId;

					//Generate TagQR Code

					//$returnData[]=$this->$model->get_tag_details_bulk($insId);

					$status=true;

					$message='Tagging added successfully';

					$responseData[]=array('status'=>true,'message'=>'Tagging added successfully','trans_code'=>$trans_code,'ref_no'=>$ref_no,'to_branch'=>$toBranch,'tag_id'=>$insId,'tag_code'=>$tag_code, 'is_lot_completed' => $is_lot_completed);

					$this->session->set_flashdata('chit_alert',array('message'=>'Tagging added successfully','class'=>'success','title'=>'Add Tagging'));

				}

				else

				{

					$status=false;

					$message='Unable to proceed the requested process';

					echo $this->db->last_query();

					echo $this->db->_error_message();exit;

					$this->db->trans_rollback();

					$responseData[]=array('status'=>false,'message'=>'Unable to proceed the requested process','trans_code'=>'','ref_no'=>'','to_branch'=>'');

					$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Add Tagging'));

				}

			     }

				 $returnData['status']=$status;

				 $returnData['message']=$message;

				 $returnData['tagging']=$responseData;

				echo json_encode($returnData);

			break;

			case "edit":

		 			$data['tagging'] = $this->$model->get_entry_records($id);

		 			if($data['tagging']['image']!='')

		 			{

		 				$images=explode('#',$data['tagging']['image']);

			 			foreach($images as $image)

			 			{

			 					$path=$this->config->item('base_url').'/assets/img/tag/'.$image;

			 					$data['tagging']['img_source'][]['src']=$this->imgTobase64($path);

			 			}

		 			}

		 			else

		 			{

		 				$data['tagging']['img_source']='';

		 			}

		 			$data['tagging']['lot_recv_branch'] = $this->$model->get_ret_settings('lot_recv_branch');

		 			$data['tag_balance']=$this->$model->get_balance_details($data['tagging']['id_lot_inward_detail'],$data['tagging']['design_id']);

		 			$data['tax_percentage']=$this->$model->get_tax_percentage($data['tagging']['tag_lot_id']);

		 			$data['metal_rate']=$this->$model->get_branchwise_rate($data['tagging']['lot_recv_branch']);

		 			$data['stone_details']=$this->$model->get_stone_details($data['tagging']['tag_id']);

					//$data['uom']= $this->$model->getUOMDetails();

					 /*echo "<pre>";

						print_r($data);

						echo "<pre>";exit; */

					//$data['main_content'] = "tagging/form" ;

		 			//$this->load->view('layout/template', $data);

						echo json_encode($data);

				break;

			case 'delete':

					$this->db->trans_begin();

					$data = array('tag_status'  =>2,

					'updated_time'=>date("Y-m-d H:i:s"),

					'updated_by'  =>$this->session->userdata('uid')

					);

					$tag_details=$this->$model->get_entry_records($id);

					$updID= $this->$model->updateData($data,'tag_id',$id,'ret_taging');

					if($updID)

					{

						$log_data=array(

	 					    	'tag_id'	  =>$id,

	 					    	'date'		  =>date("Y-m-d H:i:s"),

	 					    	'status'	  =>2,

	 					    	'from_branch' =>$tag_details['current_branch'],

	 					    	'to_branch'	  =>$tag_details['current_branch'],

	 					    	'created_on'  =>date("Y-m-d H:i:s"),

								'created_by'  =>$this->session->userdata('uid'),

								'issuspensestock'   =>$tag_details['tag_type']

	 					    );

	 					    $this->$model->insertData($log_data,'ret_taging_status_log');

	 					    $sect_logData = array(

                            	'tag_id'	  =>$id,

                            	'date'		  =>date("Y-m-d H:i:s"),

                            	'status'	  =>2,

                            	'from_branch'     =>$tag_details['current_branch'],

                            	'to_branch'	  =>$tag_details['current_branch'],

                            	'from_section'    => $tag_details['id_section'],

                            	'to_section'      => $tag_details['id_section'],

                            	'created_on'      =>date("Y-m-d H:i:s"),

                            	'created_by'      =>$this->session->userdata('uid'),

                            	'issuspensestock'   =>$tag_details['tag_type']

                            );

                            $this->$model->insertData($sect_logData,'ret_section_tag_status_log');

					}

					//$this->$model->deleteData('tag_id',$id,'ret_taging');

					if( $this->db->trans_status()===TRUE)

					{

					    $log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'Tag',

                        	'operation'   	=> 'Delete',

                        	'record'        => $id,

                        	'remark'       	=> 'Tag deleted successfully'

                        );

                        $this->log_model->log_detail('insert','',$log_data);

						$this->db->trans_commit();

						$this->session->set_flashdata('chit_alert', array('message' => 'Tag deleted successfully','class' => 'success','title'=>'Delete Tag'));

					}

					else

					{

						$this->db->trans_rollback();

						$this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Delete Tag'));

					}

					redirect('admin_ret_tagging/tagging/list');

				break;

			case "update":

	 				$addData = $_POST['tagging'];

	 				//$fin_year=$this->$model->get_financialyear_by_status();

					//$tag_code=$this->$model->code_number_generator();

					$this->db->trans_begin();

					$addData['tag_datetime'] = date('Y-m-d',strtotime(str_replace("/","-",$addData['tag_datetime'])));

					$data=array(

						 //  'tag_code'	=> (isset($fin_year['fin_year_code']) ?$tag_code.'-'.$fin_year['fin_year_code'] : NULL ),

							'tag_datetime'	=> (isset($addData['tag_datetime']) ? $addData['tag_datetime'] : NULL ),

							'tag_type'				=> (isset($addData['tag_type']) ? $addData['tag_type'] :NULL ),

							'counter'				=> (isset($addData['counter']) ? $addData['counter'] :NULL ),

							'tag_lot_id'		=> (isset($addData['tag_lot_id']) ? $addData['tag_lot_id'] :NULL ),

							'design_id'		=> (isset($addData['design_id']) ? $addData['design_id'] :NULL ),

							'cost_center'		=> (isset($addData['cost_center']) ? $addData['cost_center'] :NULL ),

							'purity'			=> (isset($addData['purity']) ? $addData['purity'] :NULL ),

							'size'				=> (isset($addData['size']) ? $addData['size'] :NULL ),

							'uom'			=> (isset($addData['uom']) ? $addData['uom'] :NULL ),

							'piece'		=> (isset($addData['piece']) ? $addData['piece'] :NULL ),

							'less_wt'			=> (isset($addData['less_wt']) ? $addData['less_wt'] :NULL ),

							'net_wt'			=> (isset($addData['net_wt']) ? $addData['net_wt'] :NULL ),

							'gross_wt'	=> (isset($addData['gross_wt']) ? $addData['gross_wt'] :NULL ),

							'uom_gross_wt' 		=> (isset($addData['gwt_uom_id'])  && $addData['tag_cat_type'] == 3 ? $addData['gwt_uom_id']:NULL),

							'calculation_based_on'				=> (isset($addData['calculation_based_on']) ? $addData['calculation_based_on']: NULL),

							'retail_max_wastage_percent'			=> (isset($addData['retail_max_wastage_percent']) ? $addData['retail_max_wastage_percent'] :NULL ),

							'tag_mc_type'				=> (isset($addData['tag_mc_type']) ? $addData['tag_mc_type'] :NULL ),

							'tag_mc_value'				=> (isset($addData['tag_mc_value']) ? $addData['tag_mc_value'] :NULL ),

							'halmarking'			=> (isset($addData['halmarking']) ? $addData['halmarking'] :NULL ),

							'sell_rate' 		=> (($addData['sell_rate']!='') ? $addData['sell_rate']:NULL),

							'round_off' 		=> (($addData['round_off']!='') ? $addData['round_off']:NULL),

							'sales_value'				=> (isset($addData['sales_value']) ? $addData['sales_value'] :NULL ),

//							'tax_group_id'			=> (isset($addData['tax_group_id']) ? $addData['tax_group_id'] :NULL ),

							'current_counter'		=> (isset($addData['current_counter']) ? $addData['current_counter'] :0 ),

							'retail_max_mc'				=> (isset($addData['retail_max_mc']) ? $addData['retail_max_mc'] :NULL ),

							'updated_time'	  		=> date("Y-m-d H:i:s"),

							'created_by'      		=> (isset($addData['created_by']) ? $addData['created_by'] :0 ),

							'current_branch'      	=> (isset($addData['current_branch']) ? $addData['current_branch'] :NULL),

							'id_branch'      	=> (isset($addData['current_branch']) ? $addData['current_branch'] :NULL),

							'updated_by'      		=> $this->session->userdata('uid')

						);

					  $updID= $this->$model->updateData($data,'tag_id',$id,'ret_taging');

					if($updID)

					{

						$tagStone = $_POST['tagstone'];

						if(isset($tagStone)){

							$arrayTagStones = array();

							foreach($tagStone['stone_id'] as $key => $val){

								$arrayTagStones= array('stone_id' => $tagStone['stone_id'][$key], 'pieces' => $tagStone['pcs'][$key], 'wt' => $tagStone['weight'][$key], 'uom_id' => $tagStone['uom_id'][$key], 'amount' => $tagStone['amount'][$key]);

							$upd= $this->$model->updateData($arrayTagStones,'tag_id',$id,'ret_taging_stone');

							}

						}

						$tagMaterials = $_POST['tagmaterials'];

						if(isset($tagMaterials)){

							$arrayTagMaterials = array();

							foreach($tagMaterials['material_id'] as $key => $val){

								$arrayTagMaterials= array('material_id' => $tagMaterials['material_id'][$key], 'wt' => $tagMaterials['weight'][$key], 'price' => $tagMaterials['amount'][$key]);

								$upd_another= $this->$model->updateData($arrayTagMaterials,'tag_id',$id,'ret_taging_other_materials');

							}

						}

						//update lot table if tag is completed

						$total_tag=$this->$model->get_tagged_details($addData['tag_lot_id']);

						if($total_tag['no_of_piece']==$total_tag['tagged_pieces'])

						{

						   $lot['tag_status']=1;

						   $upd_data= $this->$model->updateData($lot,'lot_no',$addData['tag_lot_id'],'ret_lot_inwards');

						}

						else

						{

								$lot['tag_status']=0;

						   		$upd_data= $this->$model->updateData($lot,'lot_no',$addData['tag_lot_id'],'ret_lot_inwards');

						}

					}

					if($this->db->trans_status()===TRUE)

					{

					    $log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'Tag',

                        	'operation'   	=> 'Edit',

                        	'record'        => $id,

                        	'remark'       	=> 'Tag modified successfully'

                        );

                        $this->log_model->log_detail('insert','',$log_data);

						$this->db->trans_commit();

						$this->session->set_flashdata('chit_alert',array('message'=>'Tagging modified successfully','class'=>'success','title'=>'Edit Tagging'));

					}

					else

					{

						$this->db->trans_rollback();

						$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Edit Tagging'));

 					}

 					redirect('admin_ret_tagging/tagging/list');

				break;

			case 'tag_update':

			            $addData = $_POST['lt_item'];

			            $tag_id  =$_POST['tag_id'];

			            $responseData=array();

						foreach($addData['lot_no'] as $key => $val)

			            {

							if($addData['cert_image'][$key] != "")

								$cert_img = uniqid().".jpg";

							else

								$cert_img = "";

			                 $arrayTag = array(

                                'id_branch' 		=> $addData['id_branch'],

                                'cost_center' 		=> $addData['id_branch'],

                                'tag_lot_id' 		=> $addData['lot_no'][$key],

                                //'product_id' 		=> $addData['lot_product'][$key],

                                'design_id' 		=> $addData['lot_id_design'][$key],

                                'id_sub_design' 	=> $addData['lot_id_sub_design'][$key],

                                'design_for' 		=> 0,//$addData['design_for'][$key],

                                'purity' 			=> $addData['purity'][$key],

                                'id_orderdetails'=>(isset($addData['id_orderdetails'][$key]) ?($addData['id_orderdetails'][$key]!='' ? $addData['id_orderdetails'][$key]:NULL) :NULL),

                                'id_lot_inward_detail' => $addData['id_lot_inward_detail'][$key],

                                'size' 				=> ($addData['size'][$key]!='' ? ($addData['size'][$key]!='null' ? $addData['size'][$key]:NULL):NULL),

                                'piece' 			=> (($addData['no_of_piece'][$key]!='') ? $addData['no_of_piece'][$key]:NULL),

                                'gross_wt' 			=> (($addData['gross_wt'][$key]!='') ? $addData['gross_wt'][$key]:NULL),

								'uom_gross_wt' 		=> (($addData['gwt_uom_id'][$key]!='')  && $addData['tag_cat_type'][$key] == 3 ? $addData['gwt_uom_id'][$key]:NULL),

                                'less_wt' 			=> (($addData['less_wt'][$key]!='') ? $addData['less_wt'][$key]:NULL),

                                'net_wt' 			=> (($addData['net_wt'][$key]!='') ? $addData['net_wt'][$key]:NULL),

                                'purchase_touch'    => (($addData['purchase_touch'][$key]!='') ? $addData['purchase_touch'][$key]:NULL),

                                'calculation_based_on' => $addData['calculation_based_on'][$key],

                                'retail_max_wastage_percent' => (($addData['wastage_percentage'][$key]!='') ? $addData['wastage_percentage'][$key]:NULL),

                                'tag_mc_type' 		=> (($addData['id_mc_type'][$key]!='') ? $addData['id_mc_type'][$key]:NULL),

                                'tag_mc_value' 	    => (($addData['making_charge'][$key]!='') ? $addData['making_charge'][$key]:0),

                                'sell_rate' 		=> (($addData['sell_rate'][$key]!='') ? $addData['sell_rate'][$key]:NULL),

                                'item_rate' 		=> (($addData['adjusted_item_rate'][$key]!='') ? $addData['adjusted_item_rate'][$key]:NULL),

                                'sales_value' 		=> (($addData['sale_value'][$key]!='') ? $addData['sale_value'][$key]:NULL),

                                'hu_id'             => (($addData['huid'][$key]!='') ? $addData['huid'][$key]:NULL),

								'hu_id2'            => (($addData['huid2'][$key]!='') ? $addData['huid2'][$key]:NULL),

                                //'image'				=> ((strlen($precious_imgs) > 0)?$precious_imgs : NULL),

                                //'tag_datetime'	  	=> $tag_datetime,

								"cert_no"			=> (($addData['cert_no'][$key]!='') ? $addData['cert_no'][$key]:NULL),

								"cert_img"			=> $cert_img,

								'manufacture_code' 			=> $addData['manufacture_code'][$key],

								'style_code' 				=> $addData['style_code'][$key],

								'remarks' 					=> $addData['remarks'][$key],

								'narration' 				=> $addData['narration'][$key],

								'tag_purchase_cost'	=> isset($addData['tag_purchase_cost'][$key]) && trim($addData['tag_purchase_cost'][$key]) != "" ? $addData['tag_purchase_cost'][$key] : 0,

								'cat_type'        	=> $addData['tag_cat_type'][$key],

								'stone_calculation_based_on' => $addData['stone_calculation_based_on'][$key],

                                'updated_time'	  	=> date("Y-m-d H:i:s"),

                                'updated_by'      	=> $this->session->userdata('uid'),

                                'product_division' 	=> isset($addData['tag_product_division'][$key]) && trim($addData['tag_product_division'][$key]) != "" ? $addData['tag_product_division'][$key] : NULL,

                                'id_section'       =>  (($addData['tag_section'][$key]!='' && $addData['tag_section'][$key]!=0) ? $addData['tag_section'][$key]:NULL),

								'quality_id' => (isset($addData['quality_id'][$key]) ? ($addData['quality_id'][$key]!='' ? $addData['quality_id'][$key]:NULL):NULL),

								'is_new_arrival' 	=> isset($addData['is_new_arrival'][$key]) && trim($addData['is_new_arrival'][$key]) != "" ? $addData['is_new_arrival'][$key] : 0,

								'lot_making_charge' => (isset($addData['pur_making_charge'][$key]) ? ($addData['pur_making_charge'][$key] != '' ? $addData['pur_making_charge'][$key] : NULL) : NULL),

								'lot_mc_type' => (isset($addData['pur_id_mc_type'][$key]) ? ($addData['pur_id_mc_type'][$key] != '' ? $addData['pur_id_mc_type'][$key] : NULL) : NULL),

								'lot_wastage_percentage' => (isset($addData['pur_wastage_percentage'][$key]) ? ($addData['pur_wastage_percentage'][$key] != '' ? $addData['pur_wastage_percentage'][$key] : NULL) : NULL),

                                );

                                $this->db->trans_begin();

                                $status=$this->$model->updateData($arrayTag,'tag_id',$tag_id,'ret_taging');

                                if($status)

                                {

                                    /*Section Update in ret_section_tag_status_log table while tag update*/

                                    	$sect_data['to_section'] = $addData['tag_section'][$key];

                                    	$sect_status = $this->$model->updateData($sect_data,'tag_id',$tag_id,'ret_section_tag_status_log');

                                    /*Section Update in ret_section_tag_status_log table while tag update*/

									$dir_certimg = $this->config->item('tag_cert_img_path').$tag_id."/";

									array_map('unlink', glob("$dir_certimg/*.*"));

									$base64_img = $addData['cert_image'][$key];

									if($base64_img != "") {

										if (!file_exists($dir_certimg)) {

											mkdir($dir_certimg, 0777, true);

										}

										$path = $dir_certimg.$cert_img;

										$this->save_base64_image($base64_img, $path);

									}

									// Stone Details

									$this->$model->deleteData('tag_id',$tag_id,'ret_taging_stone');

									if($addData['stone_details'][$key])

									{

										$stone_details=json_decode($addData['stone_details'][$key],true);

										foreach($stone_details as $stone)

										{

											$stone_data=array(

															'tag_id'                =>$tag_id,

															'pieces'                =>$stone['stone_pcs'],

															'wt'                    =>$stone['stone_wt'],

															'stone_id'              =>$stone['stone_id'],

															'uom_id'                =>$stone['stone_uom_id'],

															'amount'                =>$stone['stone_price'],

															'rate_per_gram'         =>$stone['stone_rate'],

															'is_apply_in_lwt'       =>$stone['show_in_lwt'],

															'pur_rate'      	 	=>$stone['stn_pur_rate'],

															'pur_cost'       		=>$stone['stn_pur_cost'],

															);

											$stoneInsert = $this->$model->insertData($stone_data,'ret_taging_stone');

										}

									}

									//update lot table if tag is completed

									$total_tag=$this->$model->get_tagged_details($addData['id_lot_inward_detail'][$key]);

									if($total_tag['total_pieces']==$total_tag['tagged_pieces'])

									{

										$lot['tag_status']=1;

										$tagInsert= $this->$model->updateData($lot,'id_lot_inward_detail',$addData['id_lot_inward_detail'][$key],'ret_lot_inwards_detail');

									}

									$this->$model->deleteData('tag_id',$tag_id,'ret_taging_charges');

									if($addData['charges'][$key])

									{

										$charges = (array)json_decode($addData['charges'][$key], true);

										foreach($charges as $charge)

										{

											$charge_data=array(

															'tag_id'                =>$tag_id,

															'charge_id'             =>$charge['charge_id'],

															'charge_value'          =>$charge['charge_value'],

															);

											$this->$model->insertData($charge_data,'ret_taging_charges');

										}

									}

									$this->$model->deleteData('id_tagging',$tag_id,'ret_tagging_attributes');

									if($addData['attributes'][$key])

									{

										$attributes = (array)json_decode($addData['attributes'][$key], true);

										foreach($attributes as $attrs)

										{

											if($attrs['attr_id'] > 0 && $attrs['attr_val_id'] > 0) {

												$attr_data	=	array(

																	'id_tagging'    =>	$tag_id,

																	'attr_id'     	=>	$attrs['attr_id'],

																	'attr_val_id' 	=>	$attrs['attr_val_id'],

																	'created_on'  	=>	date("Y-m-d H:i:s"),

																	'created_by'  	=>	$this->session->userdata('uid')

																);

												$this->$model->insertData($attr_data,'ret_tagging_attributes');

											}

										}

									}

									$this->$model->deleteData('tag_other_itm_tag_id',$tag_id,'ret_tag_other_metals');

									if($addData['othermetals'][$key])

									{

										$othermetals = (array)json_decode($addData['othermetals'][$key], true);

										foreach($othermetals as $othrs)

										{

											$oth_metal_data	=	array(

												'tag_other_itm_tag_id'     =>	$tag_id,

												'tag_other_itm_metal_id'   =>	$othrs['id_metal'],

												'tag_other_itm_pur_id' 		=>	$othrs['id_purity'],

												'tag_other_itm_grs_weight'	=>	$othrs['nwt'],

												'tag_other_itm_wastage' 	=>	$othrs['wastage_perc'],

												'tag_other_itm_uom'     	=>	1,

												'tag_other_itm_cal_type' 	=>	$othrs['calc_type'],

												'tag_other_itm_mc' 			=>	$othrs['making_charge'],

												'tag_other_itm_rate' 		=>	$othrs['rate_per_gram'],

												'tag_other_itm_pcs'     	=>	1,

												'tag_other_itm_amount' 		=>	$othrs['amount'],

											);

											$this->$model->insertData($oth_metal_data,'ret_tag_other_metals');

										}

									}

                                }

			            }

			            if($this->db->trans_status()===TRUE)

    					{

    					    $log_data = array(

                            	'id_log'        => $this->session->userdata('id_log'),

                            	'event_date'	=> date("Y-m-d H:i:s"),

                            	'module'      	=> 'Tag',

                            	'operation'   	=> 'Add',

                            	'record'        => $tag_id,

                            	'remark'       	=> 'Tags Updated successfully.Ref no is'.$tag_id.'.'

                            );

                            $this->log_model->log_detail('insert','',$log_data);

    						$this->db->trans_commit();

							$tagArr = $this->$model->get_tag_details_by_tag_id($tag_id);

							$tag_code = $tagArr['tag_code'];

    						$responseData=array('status'=>true,'message'=>'Tagging Updated successfully','trans_code'=>'','ref_no'=>$ref_no,'to_branch'=>'','tag_id'=>$tag_id,'tag_code'=>$tag_code);

    						$this->session->set_flashdata('chit_alert',array('message'=>'Tagging Updated successfully','class'=>'success','title'=>'Edit Tagging'));

    					}

    					else

    					{

    						$this->db->trans_rollback();

    						$responseData=array('status'=>false,'message'=>'Unable to proceed the requested process','trans_code'=>'','ref_no'=>'','to_branch'=>'');

    						$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Edit Tagging'));

    					}

					echo json_encode($responseData);

			break;

			case 'update_status':

						$data = array('status' => $status);

						$status = $this->$model->updateData($id,$data);

						if($status)

						{

							$this->session->set_flashdata('chit_alert',array('message'=>'floor status updated as '.($status ? 'active' : 'inactive').' successfully.','class'=>'success','title'=>'floor  Status'));

						}

						else

						{

							$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'floor  Status'));

						}

						redirect('admin_ret_lot/lot_inward/list');

			break;

			case 'generate_barcode':

				$tags = explode(',',$_GET['tag']);

				if(count($tags) > 0) {

					$tag_items = array();

					foreach($tags as $tag)

					{

						if($tag > 0) {

							$tag_items[] = $this->get_tagArray($tag);

						}

					}

					if(count($tag_items) > 0) {

						$this->generate_printer_code($tag_items);

					}

				}

			break;

			/*case 'generate_all_qrcode':

						//$tagging=json_decode($_GET['tag'],true);

							if($_GET['lot_id']!='')

			 		    	{

    			 		    	$tagging=$this->$model->getTagDetailsby_lot($_GET['lot_id']);

    			 		        //Update No of Print Taken

    			 		        foreach($tagging as $data)

    			 		        {

									$min_details = $this->ret_billing_model->get_mc_va_limit($data['product_id'], $data['design_id'], $data['id_sub_design'], $data['gross_wt']);

        			 		        $print_taken=$data['tot_print_taken'];

        							$print_taken++;

        							$this->$model->updateData(array('tot_print_taken'=>$print_taken),'tag_id',$data['tag_id'],'ret_taging');

        							if($data['tag_mark']==1)

                                    {

                                         $this->$model->updateData(array('is_green_tag_printed'=>1),'tag_id',$tag,'ret_taging');

                                    }else{

                                        $this->$model->updateData(array('is_green_tag_printed'=>0),'tag_id',$tag,'ret_taging');

                                    }

        							$insData=array(

        							'tag_id'	 =>$data['tag_id'],

        							'id_employee'=> $this->session->userdata('uid'),

        							'print_date' => date('Y-m-d H:i:s'),

        							);

        							$this->$model->insertData($insData,'ret_tag_duplicate_copy');

        							//Update No of Print Taken

									//Get Charge Code

									$charge_code = '';

									$charges=$this->$model->getTagCharges($data['tag_id'], 1);

									//echo "<pre>"; print_r($charges);exit;

									foreach($charges as $charge)

									{

										$charge_code = $charge['code_charge'];

									}

        			 		    	$this->load->library('phpqrcode/qrlib');

        							$SERVERFILEPATH = 'qrcode/'.$data['tag_code'];

        							if (!is_dir($SERVERFILEPATH)) {

        								mkdir($SERVERFILEPATH, 0777, TRUE);

        							}

        							$code = time().$data['tag_code'];

        							$text1= substr($code, 0,9);

        							$folder = $SERVERFILEPATH;

        							$file_name1 = time().$data['tag_code']. ".png";

        							$file_name = $SERVERFILEPATH.'/'.$file_name1;

        						    QRcode::png($data['tag_code'],$file_name);  //Passing QR data

        							$src['img'][]=array(

            			 			 'code'         =>$code,

            			 			 'product_name' =>$data['product_name'],

            			 			 'design_name'  =>$data['design_name'],

								     'sub_design_name' =>$data['sub_design_name'],

            			 			 'product_id'   =>$data['product_short_code'],

            			 			 'gross_wt'     =>$data['gross_wt'],

									 'net_wt'       =>$data['net_wt'],

            			 			 'tag_id'       =>$data['tag_id'],

            			 			 'tag_code'     =>$data['tag_code'],

            			 			 'size'         =>$data['size'],

            			 			 'code_karigar' =>$data['code_karigar'],

            			 			 'short_code'   =>$data['short_code'],

            			 			 'sales_value'  =>$data['sales_value'],

            			 			 'sell_rate'         =>$data['sell_rate'],

            			 			 'sales_mode'   =>$data['sales_mode'],

									 'metal_type'   =>$data['metal_type'],

            			 			 'tag_mark'     =>$data['tag_mark'],

            			 			 'mc_cal_type'  =>$data['mc_cal_type'],

    			 			        'tag_mc_value'  =>$data['tag_mc_value'],

    			 			        'stn_amount'        =>$data['stn_amount'],

									'stn_pieces'        =>$data['stn_pieces'],

    			 			        'stn_wt'            =>$data['stn_wt'],

									'dia_amount'        =>$data['dia_amount'],

									'dia_pieces'        =>$data['dia_pieces'],

									'dia_wt'        	=>$data['dia_wt'],

									'dia_uom_name'      =>$data['dia_uom_name'],

									'dia_uom_short_code'=>$data['dia_uom_short_code'],

    			 			        'hu_id'        	 	=>$data['hu_id'],

									'stuom_short_code'  =>$data['stuom_short_code'],

									'charge_code'		=>$charge_code,

									'tag_type'          =>$data['tag_type'],

									'piece'             =>$data['piece'],

    			 			        'retail_max_wastage_percent'  =>$data['retail_max_wastage_percent'],

    			 			        'product_division'  =>$data['product_division'],

									 'min_details'		=>$min_details,

									 'display_purity'	=>$data['display_purity'],

            			 			 'src'          =>$this->config->item('base_url')."qrcode/".$data['tag_code'].'/'.$file_name1

            			 			 );

    							}

							}

			 		    //echo "<pre>"; print_r($src);exit;

			 		    $html1 = $this->load->view('qrcode/tag_qrcode', $src,true);

			 		    $html = preg_replace('/>\s+</', "><", $html1); //Remove Blank page

			 			$this->load->helper(array('dompdf', 'file'));

						$dompdf = new DOMPDF();

						$dompdf->load_html($html);

						//$customPaper = array(0,0,220,111);

						//$customPaper = array(0,0,50,45);

						$dompdf->set_paper("portriat" );

						$dompdf->render();

						while(ob_get_level()) ob_end_clean();
						$dompdf->stream("Tag.pdf",array('Attachment'=>0));

				break;*/

			case "bulk_edit":

					$data['uom']= $this->$model->getUOMDetails();

					$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_tagging/tagging/bulk_edit');

					$data['main_content'] = "tagging/bulk_edit" ;

		 			$this->load->view('layout/template', $data);

				break;

		}

	}

	function bt_tag_list($transCode,$ref_no,$toBranch){

		$fData = array( 'ref_no' => $ref_no, 'toBranch' => $toBranch);

		$data['list'] = $this->ret_tag_model->ajax_getTaggingList('','','',$fData,'');

		$data['trans_code'] = $transCode;

		$data['main_content'] = "tagging/bt_tag_list" ;

		$this->load->view('layout/template', $data);

	}

	public function updateTag()

	{

		$model = "ret_tag_model";

		$est_stones_item=(isset($_POST['est_stones_item'])  ? $_POST['est_stones_item']:'');

		//Image Upload

		$img_source=$this->input->post('img_source');

		$precious_imgs = "";

				$p_ImgData = json_decode($img_source);

			if(sizeof($p_ImgData) > 0){

				foreach($p_ImgData as $precious){

					$imgFile = $this->base64ToFile($precious->src);

					$_FILES['precious'][] = $imgFile;

				}

			}

		if(!empty($_FILES)){

					$img_arr = array();

						$folder =  self::IMG_PATH."tag/";

						if (!is_dir($folder)) {

							mkdir($folder, 0777, TRUE);

						}

						if(isset($_FILES['precious'])){

							$precious_imgs = "";

							foreach($_FILES['precious'] as $file_key => $file_val){

								if($file_val['tmp_name'])

								{

									// unlink($folder."/".$product['image']);

									$img_name =  "t_". mt_rand(120,1230).".jpg";

									$path = $folder."/".$img_name;

									$result = $this->upload_img('image',$path,$file_val['tmp_name']);

									if($result){

										$precious_imgs = strlen($precious_imgs) > 0 ? $precious_imgs."#".$img_name : $img_name;

									}

								}

						}

				}

			}

		//Image Upload

		$calculation_based_on=$this->input->post('calculation_based_on');

		$gross_wt			=$this->input->post('gross_wt');

		$less_wt			=$this->input->post('less_wt');

		$net_wt				=$this->input->post('net_wt');

		$size 				=$this->input->post('size');

		$pieces 			=$this->input->post('pieces');

		$sale_value 		=$this->input->post('sale_value');

		$tag_id 			=$this->input->post('tag_id');

		$tag_mc_value 		=$this->input->post('tag_mc_value');

		$retail_max_wastage_percent 			=$this->input->post('retail_max_wastage_percent');

		$this->db->trans_begin();

		$updateData=array(

			'calculation_based_on'=>$calculation_based_on,

			'gross_wt'						=>$gross_wt,

			'less_wt'						=>($less_wt!='' ? $less_wt:NULL),

			'net_wt'						=>($net_wt!='' ? $net_wt:NULL),

			'size'							=>($size!='' ? $size:NULL),

			'piece'							=>($pieces!='' ? $pieces:NULL),

			'tag_mc_value'					=>($tag_mc_value!='' ? $tag_mc_value:''),

			'retail_max_wastage_percent'	=> ($retail_max_wastage_percent!='' ? $retail_max_wastage_percent:''),

			'sales_value'					=>($sale_value!='' ? $sale_value:0),

			'image'							=>($precious_imgs!='' ? $precious_imgs:NULL),

			'updated_time'					=> date("Y-m-d H:i:s"),

			'updated_by'    				=> $this->session->userdata('uid')

		);

		$updID= $this->$model->updateData($updateData,'tag_id',$tag_id,'ret_taging');

		if($updID)

		{

			if($est_stones_item)

			{

				$this->$model->deleteData('tag_id', $tag_id, 'ret_taging_stone');

				foreach($est_stones_item['stone_id'] as $key => $val)

				{

				$stone_data=array(

				'tag_id'=>$updID,

				'pieces'=>$est_stones_item['stone_pcs'][$key],

				'wt'=>$est_stones_item['stone_wt'][$key],

				'stone_id'=>$est_stones_item['stone_id'][$key],

				'amount'=>$est_stones_item['stone_price'][$key],

				);

				$stoneInsert = $this->$model->insertData($stone_data,'ret_taging_stone');

				}

			}

		}

		if($this->db->trans_status()===TRUE)

		{

		    $log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'Tag',

                        	'operation'   	=> 'Edit',

                        	'record'        => $tag_id,

                        	'remark'       	=> 'Tag modified successfully'

                        );

            $this->log_model->log_detail('insert','',$log_data);

			$this->db->trans_commit();

			$this->session->set_flashdata('chit_alert',array('message'=>'Tagging modified successfully','class'=>'success','title'=>'Edit Tagging'));

			$data=array('status'=>true,'msg'=>'Tagging modified successfully');

		}

		else

		{

			$this->db->trans_rollback();

			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested process','class'=>'danger','title'=>'Edit Tagging'));

			$data=array('status'=>false,'msg'=>'Unable to proceed the requested process');

		}

		echo json_encode($data);

	}

	function generateTagsByRefNo($ref_no,$type,$tag_from="")

	{

	    //$type = new , duplicate

	    if($ref_no!='')

		{

			$model = "ret_tag_model";

			$tags = $this->$model->getTagByRefNo($ref_no);

			foreach($tags as $data)

			{

	            // Print Images Data Settings

				//$data_shown_status	=	$this->$model->get_ret_settings('print_img_in_tag');

	            //Update No of Print Taken

	            $print_taken = $data['tot_print_taken'];

	            $print_taken=$print_taken+1;

	            $this->$model->updateData(array('tot_print_taken'=>$print_taken),'tag_id',$data['tag_id'],'ret_taging');

	            /*$insData = array(

					            'tag_id'	 =>$data['tag_id'],

					            'id_employee'=> $this->session->userdata('uid'),

					            'print_date' => date('Y-m-d H:i:s'),

				            );

	            $this->$model->insertData($insData,'ret_tag_duplicate_copy');*/

	            $this->load->library('phpqrcode/qrlib');

	            $SERVERFILEPATH = 'qrcode/'.$data['tag_code'];

	            if (!is_dir($SERVERFILEPATH)) {

	            	mkdir($SERVERFILEPATH, 0777, TRUE);

	            }

	            $code = time().$data['tag_code'];

	            $text1= substr($code, 0,9);

	            $folder = $SERVERFILEPATH;

	            $file_name1 = time().$data['tag_code']. ".png";

	            $file_name = $SERVERFILEPATH.'/'.$file_name1;

	            QRcode::png($data['tag_code'],$file_name);  //Passing QR data

	            //Get Charge Code

					$charge_code = '';

					$charges=$this->$model->getTagCharges($data['tag_id'], 1);

					//echo "<pre>"; print_r($charges);exit;

					foreach($charges as $charge)

					{

						$charge_code = $charge['code_charge'];

					}

	           $src['img'][]=array(

                                    'code'              =>$code,

                                    'product_name'      =>$data['product_name'],

                                    'design_name'       =>$data['design_name'],

									'sub_design_name'   =>$data['sub_design_name'],

                                    'product_id'        =>$data['product_short_code'],

                                    'gross_wt'          =>$data['gross_wt'],

									'net_wt'         	=>$data['net_wt'],

                                    'tag_id'            =>$data['tag_id'],

                                    'tag_code'          =>$this->format_tag_code($data['tag_code']),

                                    'size'              =>$data['size'],

                                    'code_karigar'      =>$data['code_karigar'],

                                    'short_code'        =>$data['short_code'],

                                    'sales_value'       =>$data['sales_value'],

                                    'sell_rate'         =>$data['sell_rate'],

    			 			        'sales_mode'        =>$data['sales_mode'],

									'metal_type'		=>$data['metal_type'],

    			 			        'tag_mark'          =>$data['tag_mark'],

    			 			        'mc_cal_type'       =>$data['tag_mc_type'],

    			 			        'tag_mc_value'      =>$data['tag_mc_value'],

    			 			        'stn_amount'        =>$data['stn_amount'],

									'stn_pieces'        =>$data['stn_pieces'],

    			 			        'stn_wt'            =>$data['stn_wt'],

    			 			        'stuom'             =>$data['stuom'],

									'dia_amount'        =>$data['dia_amount'],

									'dia_pieces'        =>$data['dia_pieces'],

									'dia_wt'        	=>$data['dia_wt'],

									'dia_uom_name'      =>$data['dia_uom_name'],

									'dia_uom_short_code'=>$data['dia_uom_short_code'],

    			 			        'hu_id'             =>$data['hu_id'],

									'stuom_short_code'  =>$data['stuom_short_code'],

									'charge_code'		=>$charge_code,

									'tag_type'          =>$data['tag_type'],

    			 			         'retail_max_wastage_percent'  =>$data['retail_max_wastage_percent'],

    			 			         'product_division'  =>$data['product_division'],

									 'display_purity'	=>$data['display_purity'],

                                    'src'               =>$this->config->item('base_url')."qrcode/".$data['tag_code'].'/'.$file_name1

                                    );

	    	}

    	}

		$show_tag_img = $this->$model->get_ret_settings('print_img_in_tag');

		    $html1 = $this->load->view('qrcode/tag_qrcode', $src,true);

		    $html = preg_replace('/>\s+</', "><", $html1); //Remove Blank page

		    //echo "<pre>";print_r($src['img']);exit;

			$this->load->helper(array('dompdf', 'file'));

			$dompdf = new DOMPDF();

			$dompdf->load_html($html);

			//$customPaper = array(0,0,220,111);

			    //$customPaper = array(0,0,50,45);

			$dompdf->set_paper("portriat" );

			$dompdf->render();

			while(ob_get_level()) ob_end_clean();
			$dompdf->stream("Tag.pdf",array('Attachment'=>0));

	}

	public function get_tag_types(){

		$model = "ret_tag_model";

		$data = $this->$model->getAvailabletags();

		echo json_encode($data);

	}

	public function get_tag_purities(){

		$model = "ret_tag_model";

		$data = $this->$model->getAvailablePurities();

		echo json_encode($data);

	}

	public function get_lot_ids(){

		$model = "ret_tag_model";

		$include_closed = $this->input->get('include_closed') == '1' ? true : false;

		$id_branch = $this->input->get('id_branch');

		$id_branch = ($id_branch != '' && $id_branch != null) ? $id_branch : null;

		$data = $this->$model->getAvailableLots($include_closed, $id_branch);

		echo json_encode($data);

	}

	public function getDesignNosBySearch(){

		$model = "ret_tag_model";

		$data = $this->$model->getAvailableDesigns($_POST['searchTxt']);

		echo json_encode($data);

	}

	public function getDesignDetails(){

		$model = "ret_tag_model";

		$data = $this->$model->getDesignDetails($_POST['designId']);

		echo json_encode($data);

	}

	public function getDesignPurityByDesignId(){

		$model = "ret_tag_model";

		$data = $this->$model->getDesignPurityByDesignId($_POST['designId']);

		echo json_encode($data);

	}

	public function getDesignStonesByDesignId(){

		$model = "ret_tag_model";

		$data = $this->$model->getDesignStonesByDesignId($_POST['designId']);

		echo json_encode($data);

	}

	public function getTagStoneByTagId(){

		$model = "ret_tag_model";

		$data = $this->$model->getTagStoneByTagId($_POST['tagId']);

		echo json_encode($data);

	}

	public function getTagMaterialByTagId(){

		$model = "ret_tag_model";

		$data = $this->$model->getTagMaterialByTagId($_POST['tagId']);

		echo json_encode($data);

	}

	public function getDesignMaterialsByDesignId(){

		$model = "ret_tag_model";

		$data = $this->$model->getDesignMaterialsByDesignId($_POST['designId']);

		echo json_encode($data);

	}

	public function getAvailableTaxGroups(){

		$model = "ret_tag_model";

		$data = $this->$model->getAvailableTaxGroups();

		echo json_encode($data);

	}

	public function getAvailableTaxGroupItems(){

		$model = "ret_tag_model";

		$data = $this->$model->getAvailableTaxGroupItems($_POST['taxGroupId']);

		echo json_encode($data);

	}

	public function getStoneItems(){

		$model = "ret_tag_model";

		$data = $this->$model->getAvailableStones();

		echo json_encode($data);

	}

	public function getOtherCharges(){

		$model = "ret_tag_model";

		$data = $this->$model->getChargesDetails();

		echo json_encode($data);

	}

	public function getStoneTypes(){

		$model = "ret_tag_model";

		$data = $this->$model->getAvailableStoneTypes();

		echo json_encode($data);

	}

	public function get_ActiveUOM(){

		$model = "ret_tag_model";

		$data = $this->$model->get_ActiveUOM();

		echo json_encode($data);

	}

	public function getAvailableMaterials(){

		$model = "ret_tag_model";

		$data = $this->$model->getAvailableMaterials();

		echo json_encode($data);

	}

	function get_metal_rates_by_branch()

	{

		$model="ret_tag_model";

		$id_branch=$this->input->post('id_branch');

		$data=$this->$model->get_branchwise_rate($id_branch);

		echo json_encode($data);

	}

    //Bulk Edit

	function get_tag_number()

	{

			$model="ret_tag_model";

			$tag_lot_id=$this->input->post('tag_lot_id');

			$id_branch=$this->input->post('id_branch');

			$tag_id=$this->input->post('tag_id');

			$tag_format = $this->$model->get_ret_settings('tag_format');

			if($tag_format == 2) {
				$tag_id = $this->convert_numeric_to_alpha($tag_id);
			}

			$data=$this->$model->get_tag_numbr($tag_lot_id,$id_branch,$tag_id);

			echo json_encode($data);

	}

	function get_prod_by_tagno()

	{

			$model="ret_tag_model";

			$tag_lot_id=$this->input->post('tag_lot_id');

			$prod_name=$this->input->post('prod_name');

			$data=$this->$model->get_prod_by_tagno($tag_lot_id,$prod_name);

			echo json_encode($data);

	}

	function get_tag_details()

	{

			$data['purities'] = array();

			$tag_code = $this->input->post('tag_code');

			$old_tag_code = $this->input->post('old_tag_code');

			$id_product = $this->input->post('lot_product');

			$bulk_edit_options = $this->input->post('bulk_edit_options');

			$model="ret_tag_model";

			$data['tag_details']=$this->$model->get_tag_details();

			if($bulk_edit_options == '5') {

				$data['purities']=$this->$model->get_all_purities_for_product($tag_code, $id_product);

			} else if($bulk_edit_options == '8') {

				$data['sizes']=$this->$model->get_ActiveSize($id_product, $tag_code);

			}

			echo json_encode($data);

	}

    function update_tagging_data()

	{

	        // echo "<pre>";print_r($_POST);exit;

			$model="ret_tag_model";

			$current_datetime = date("Y-m-d H:i:s");

			$service                    = $this->admin_settings_model->get_service_by_code('tag_edit');

			$retail_max_wastage_percent =$this->input->post('retail_max_wastage_percent');

			$design_id                  =$this->input->post('design_id');

			$reqdata                    =$this->input->post('req_data');

			$post_otp                   =$this->input->post('tag_otp');

			$session_otp                =$this->session->userdata('tagging_otp');

			$p_ImgData                  = json_decode(rawurldecode($this->input->post('tag_img')));

            $allow_submit               = true;

			$otp                        = array(explode(',',$session_otp));

			$tag_ids['record']='';

			$stone_details 		= json_decode($reqdata['stone_details'],true);

			// $huid_details 	   	= json_decode($reqdata['huid'],true);

			$huid_details 	   	= $reqdata['huid'];

			//print_r($stone_details);exit;


    		  if($allow_submit)

    		  {

    		    $this->db->trans_begin();

				$type = 0;

				$id_branch			= $reqdata['id_branch'];

				$bulk_edit_options 	= $reqdata['bulk_edit_options'];

				$id_mc_type 	   	= $reqdata['id_mc_type'];

				$tag_mc_value 		= $reqdata['tag_mc_value'];

				$wastage 			= $reqdata['retail_max_wastage_percent'];

				$piece 				= $reqdata['piece'];

				$gross_wt 			= $reqdata['gross_wt'];

				$net_wt 			= $reqdata['net_wt'];

				$less_wt 			= $reqdata['less_wt'];

				$update_in_lot 		= $reqdata['update_in_lot'];

				$purity 			= $reqdata['purity'];

				$mrp_price 			= $reqdata['mrp_price'];

				$size 				= $reqdata['size'];

				$attribute_type 	= $reqdata['attribute_type'];

				$attributes 		= $reqdata['attributes'];

				$charges			= $reqdata['blk_charges'];

				$charge_type		= $reqdata['charge_type'];

				$hu_id				= trim($reqdata['blk_huid']);

				$hu_id2				= trim($reqdata['blk_huid2']);

				$blk_old_tag		= trim($reqdata['blk_old_tag']);

				$stone_details 		= json_decode($reqdata['stone_details'],true);

				$purchase_cost		= $reqdata['purchase_cost'];

				$lot_wastage_percentage		= $reqdata['lot_wastage_percentage'];

				$lot_making_charge		= $reqdata['lot_making_charge'];

				$lot_mc_type		= $reqdata['lot_mc_type'];

				$lot_calc_type		= $reqdata['lot_calc_type'];

				$lot_purchase_touch		= $reqdata['lot_purchase_touch'];


				$purchase_touch		= $reqdata['purchase_touch'];

				$lot_rate_calc_type		= $reqdata['lot_rate_calc_type'];

				$lot_rate		= $reqdata['lot_rate'];

				$to_branch     =$reqdata['to_branch'];

				// $huid_details 	   	= json_decode($reqdata['huid'],true);

                $huid_details 	   	= $reqdata['huid'];

				$calc_type = $reqdata['calc_type'];

				$id_design = $reqdata['id_design'];

				$id_sub_design = $reqdata['id_sub_design'];

				$narration = $reqdata['narration'];

				$employee= $this->session->userdata('uid');

				$edit_field = $bulk_edit_options == 1 ? "MC" : ($bulk_edit_options == 2 ? "VA" : ($bulk_edit_options == 3 ? "Gross Wt" : ($bulk_edit_options == 4 ? "Pcs" : ($bulk_edit_options == 5 ? "Purity" : ($bulk_edit_options == 6 ? "MRP" : ($bulk_edit_options == 7 ? "Attributes" : ($bulk_edit_options == 8 ? "Size" : ($bulk_edit_options == 9 ? "Image Add" : ($bulk_edit_options == 10 ? "Charges" : ($bulk_edit_options == 11 ? "HUID" : ($bulk_edit_options == 12 ? "Old Tag Id" : ($bulk_edit_options == 13 ? "Calc Type" : ($bulk_edit_options == 14 ? "Purchase Cost" : ($bulk_edit_options == 15 ? "Branch" : ($bulk_edit_options == 16 ? "Design & Sub Design" : (""))))))))))))))));

				foreach($reqdata['tags'] as $data)

				{

					$previous_log_detail = array();

					$updated_log_detail = array();

					$tagId = $data['tag_id'];

					$tag_ids['record'].=$tagId.',';

					$tag_data = $this->$model->getTagDetails($tagId);

					if($bulk_edit_options == 1)

                    {

                    	$upd_data=array(

                    		'sales_value'               =>$data['sales_value'],

                    		'tag_mc_type'               =>$id_mc_type,

                    		'tag_mc_value'              =>$tag_mc_value,

                    		'updated_time'              =>$current_datetime,

                    		'updated_by'                =>$employee,

                    	);

                    	$tag_update_id=$this->$model->updateData($upd_data,'tag_id',$tagId,'ret_taging');

                    	$update_status = $tag_update_id > 0 ? true : false;

                    	if(!$update_status) {

                    		break;

						}

						$previous_log_detail[] = array(

							'sales value' => $tag_data['sales_value'],

							'mc type' => $tag_data['tag_mc_type'] == 1 ? 'per pcs' : ($tag_data['tag_mc_type'] == 2 ? 'per grm' : '% on price'),

							'mc value'=> $tag_data['tag_mc_value'],

						);

						$updated_log_detail[] = array('sales value' => $upd_data['sales_value'], 'mc type' => $upd_data['tag_mc_type'] == 1 ? 'per pcs' : ($upd_data['tag_mc_type'] == 2 ? 'per grm' : '% on price'), 'mc value' => $upd_data['tag_mc_value']);

                    }

                    else if($bulk_edit_options == 2)

                    {

                    	$upd_data=array(

                    		'sales_value'               =>$data['sales_value'],

                    		'retail_max_wastage_percent'=>$wastage,

                    		'updated_time'              =>$current_datetime,

                    		'updated_by'                =>$employee,

                    	);

                    	$tag_update_id=$this->$model->updateData($upd_data,'tag_id',$tagId,'ret_taging');

                    	$update_status = $tag_update_id > 0 ? true : false;

                    	if(!$update_status) {

                    		break;

						}

						$previous_log_detail[] = array(

							'sales value' => $tag_data['sales_value'],

							'wastage perc' => $tag_data['retail_max_wastage_percent'],

						);

						$updated_log_detail[] = array('sales value' => $upd_data['sales_value'], 'wastage perc' => $upd_data['retail_max_wastage_percent']);

                    }

                    else if($bulk_edit_options == 3)

                    {

						$this->$model->deleteData('tag_id',$tagId,'ret_taging_stone');
						if(sizeof($stone_details) > 0) {


							$stone_updated_log = array();

							foreach($stone_details as $stone) {

								$stone_data=array(

									'tag_id'                =>$tagId,

									'pieces'                =>$stone['pieces'],

									'wt'                    =>$stone['wt'],

									'stone_id'              =>$stone['stone_id'],

									'uom_id'                =>$stone['uom_id'],

									'amount'                =>$stone['amount'],

									'rate_per_gram'         =>$stone['rate_per_gram'],

									'is_apply_in_lwt'       =>$stone['is_apply_in_lwt'],

									'stone_cal_type'        =>$stone['stone_cal_type'],

									'stone_quality_id'      =>$stone['stone_quality_id'],

								);

								$stoneInsert = $this->$model->insertData($stone_data,'ret_taging_stone');

								$stone_updated_log[]  = $stone_data;

							}

						}

						//	print_r($this->db->last_query());exit;

                    	$upd_data=array(

                    		'sales_value'               =>$data['sales_value'],

                    		'gross_wt'					=>$gross_wt,

                    		'net_wt'					=>$net_wt,

                    		'less_wt'					=>$less_wt,

                    		'updated_time'              =>$current_datetime,

                    		'updated_by'                =>$employee,

                    	);

                    	$where_array = array('tag_id' => $tagId);

                    	$tag_update_id = $this->$model->updateRecord($upd_data, $where_array,'ret_taging');

                    	$update_status = $tag_update_id > 0 ? true : false;

                    	if(!$update_status) {

                    		break;

						}

						$previous_log_detail[] = array(

							'sales value' => $tag_data['sales_value'],

							'gross wt' => $tag_data['gross_wt'],

							'net wt' => $tag_data['net_wt'],

							'less wt' => $tag_data['less_wt'],

							'stone'	  => $tag_data['stone_details']

						);

						$updated_log_detail[] = array('sales value' => $upd_data['sales_value'], 'gross wt' => $upd_data['gross_wt'], 'net wt' => $upd_data['net_wt'], 'less wt' => $upd_data['less_wt'], 'stone' => $stone_updated_log);

                    }

                    else if($bulk_edit_options == 4)

                    {

                    	$upd_data=array(

                    		'sales_value'               =>$data['sales_value'],

                    		'piece'						=>$piece,

                    		'updated_time'              =>$current_datetime,

                    		'updated_by'                =>$employee,

                    	);

                    	$where_array = array('tag_id' => $tagId);

                    	$tag_update_id = $this->$model->updateRecord($upd_data, $where_array,'ret_taging');

                    	$update_status = $tag_update_id > 0 ? true : false;

                    	if(!$update_status) {

                    		break;

						}

						$previous_log_detail[] = array(

							'sales value' => $tag_data['sales_value'],

							'piece' 	  => $tag_data['piece'],

						);

						$updated_log_detail[] = array('sales value' => $upd_data['sales_value'], 'piece' => $upd_data['piece']);

                    }  else if($bulk_edit_options == 5) {

						$purity_name = "";

						if($purity > 0) {

							$get_purity = $this->ret_catalog_model->get_purity($purity);

							$purity_name = $get_purity['purity'];

						}

						$upd_data=array(

							'sales_value'  =>	$data['sales_value'],

							'purity'       =>	$purity,

							'updated_time' =>	$current_datetime,

							'updated_by'   =>	$employee,

						);

						$tag_update_id=$this->$model->updateData($upd_data,'tag_id',$tagId,'ret_taging');

						$update_status = $tag_update_id > 0 ? true : false;

						if(!$update_status) {

							break;

						}

						$previous_log_detail[] = array(

							'sales value' => $tag_data['sales_value'],

							'purity' => $tag_data['purity'],

							'purity name' => $tag_data['purity_name']

						);

						$updated_log_detail[] = array('sales value' =>$tag_data['sales_value'], 'purity' => $upd_data['purity'], 'purity name' => $purity_name);

					}

                    else if($bulk_edit_options == 6)

                    {

                    	$is_mrp = $this->$model->check_is_mrp($tagId);

                    	if($is_mrp) {

							$gross_wt = $tag_data['gross_wt'];

							$tax_details = $this->get_tax_details();

							$sell_rate  = $mrp_price;

							$total_tax_rate = 0;

							if($tag_data['calculation_based_on'] == 4) {

								$sell_rate  = $sell_rate * $tag_data['gross_wt'];

							}

							if($tag_data['tax_type'] == 2) {

								if(count($tax_details) > 0){

									// Tax Calculation

									$base_value_tax = round($this->calculate_base_value_tax($sell_rate, $tag_data['tgrp_id'], $tax_details),2);

									$base_value_amt = round(($sell_rate + $base_value_tax),2);

									$arrived_value_tax = round(($this->calculate_arrived_value_tax($base_value_amt, $tag_data['tgrp_id'], $tax_details)),2);

									$arrived_value_amt = round($base_value_amt + $arrived_value_tax, 2);

									$total_tax_rate = round($base_value_tax + $arrived_value_tax, 2);

								}

							}

							$sales_value = round(($sell_rate + $total_tax_rate), 2);

							$upd_data=array(

							'sell_rate'                 =>$mrp_price,

							'sales_value'               =>$sales_value,

							'updated_time'              =>$current_datetime,

							'updated_by'                =>$employee,

							);

                        	$tag_update_id=$this->$model->updateData($upd_data,'tag_id',$tagId,'ret_taging');

							// print_r($this->db->last_query());exit;

                        	$update_status = $tag_update_id > 0 ? true : false;

                    	}

                    	else

                    	{

                    	    $update_status = false;

                    	}

                    	if(!$update_status) {

                    		break;

						}

						$previous_log_detail[] = array(

							'sell rate' => $tag_data['sell_rate'],

							'sales value' => $tag_data['sales_value']

						);

						$updated_log_detail[] = array('sell rate' => $upd_data['sell_rate'], 'sales value' => $upd_data['sales_value']);

                    }

					else if($bulk_edit_options == 7)

					{

						$previous_tag_attributes = $this->$model->getTagAttributes($tagId);

						if($attribute_type == 1)

						{

							foreach($attributes as $attrkey => $attrval)

							{

								$curr_tag_attrs = $this->$model->getTagAttributes($tagId, $attrval['attr_name']);

								$total_count_attrs = count($curr_tag_attrs);

								if($total_count_attrs > 0) {

									foreach($curr_tag_attrs as $curr_attrs) {

										$update_status = $this->$model->deleteData('attr_tag_id',$curr_attrs['attr_tag_id'],'ret_tagging_attributes');

										if(!$update_status)

											break 3;

									}

								}

								$tag_attrs = array(

									"id_tagging"			=>	$tagId,

									"attr_id"  				=>  $attrval['attr_name'],

									"attr_val_id"    		=>  $attrval['attr_value'],

									'created_on'	  		=> 	$current_datetime,

									'created_by'      		=> 	$this->session->userdata('uid')

								);

								$attr_tag_id = $this->$model->insertData($tag_attrs,'ret_tagging_attributes');

								//print_r($this->db->last_query());exit;

								$update_status = $attr_tag_id > 0 ? true : false;

								if(!$update_status) {

									break 2;

								}

							}

						}

						else if($attribute_type == 2)

						{

							$update_status = $this->$model->deleteData('id_tagging',$tagId,'ret_tagging_attributes');

							if(!$update_status) {

								break;

							}

						}

						$previous_log_detail[] = array(

							'attributes'	=> $previous_tag_attributes,

						);

						$updated_log_detail[] = array('attribute type' => $attribute_type, 'attributes' => $attributes);

					} else if($bulk_edit_options == 8)  {

						$id_size = trim($size) == "" ? NULL : $size;

						$size_name = "";

						if($id_size > 0) {

							$get_size = $this->ret_catalog_model->get_size($id_size);

							$size_name = $get_size['value'];

						}

						$upd_data=array(

							'size'               =>$id_size,

							'updated_time'       =>$current_datetime,

							'updated_by'         =>$employee,

						);

						$tag_update_id=$this->$model->updateData($upd_data,'tag_id',$tagId,'ret_taging');

						$update_status = $tag_update_id > 0 ? true : false;

						if(!$update_status) {

							break;

						}

						$previous_log_detail[] = array(

							'id size' => $tag_data['id_size'],

							'size' => $tag_data['size']

						);

						$updated_log_detail[] = array('id size' => $upd_data['size'], 'size' => $size_name);

					} else if($bulk_edit_options == 10) {

						$charge_old = $this->$model->getTagCharges($tagId);

						if($charge_type == 1) {

							foreach($charges as $charge) {

								$charge_data=array(

									'tag_id'                =>$tagId,

									'charge_id'             =>$charge['charge_id'],

									'charge_value'          =>$charge['charge_value'],

								);

								$charge_tag_id = $this->$model->insertData($charge_data,'ret_taging_charges');

								$update_status = $charge_tag_id > 0 ? true : false;

								if(!$update_status) {

									break 2;

								}

							}

						} else if($charge_type == 2) {

							$update_status = $this->$model->deleteData('tag_id',$tagId,'ret_taging_charges');

							if(!$update_status) {

								break;

							}

						}

						$previous_log_detail[] = array(

							'charges' => $charge_old

						);

						$updated_log_detail[] =  array('charge type' => $charge_type, 'charges' => $charges);

					} else if($bulk_edit_options == 11) {

						$upd_data['hu_id'] = $hu_id;

						$upd_data['hu_id2'] = $hu_id2;

						$huid_old = $this->$model->getTagHuid($tagId);

                        if(sizeof($huid_details) > 0)

						{

							$this->$model->deleteData('tag_id',$tagId,'ret_taging_huid');

							foreach($huid_details as $huid)

							{

								// echo"<pre>";print_r($huid);exit;

								$huid_data=array(

												'tag_id'                =>$tagId,

												'huid'                =>$huid['huid'],

												'updated_on'            =>$current_datetime,

												'updated_by'            =>$this->session->userdata('uid'),

												);

								$huidInsert = $this->$model->insertData($huid_data,'ret_taging_huid');

							}

						}

						$tag_update_id=$this->$model->updateData($upd_data,'tag_id',$tagId,'ret_taging');

						$update_status = $tag_update_id > 0 ? true : false;

						if(!$update_status) {

							break;

						}

						$previous_log_detail[] = array(

							'huid'	=> $tag_data['hu_id'],

							'huid2'=> $tag_data['hu_id2'],

							'huid details' => $huid_old

						);

						$updated_log_detail[] = array('huid' => $upd_data['hu_id'], 'huid2' => $upd_data['hu_id2'], 'huid details' => $huid_details);

					} else if($bulk_edit_options == 12) {

						$upd_data['old_tag_id'] = $blk_old_tag;

						$tag_update_id=$this->$model->updateData($upd_data,'tag_id',$tagId,'ret_taging');

						$update_status = $tag_update_id > 0 ? true : false;

						if(!$update_status) {

							break;

						}

						$previous_log_detail[] = array(

							'old tag id' => $tag_data['old_tag_id'],

						);

						$updated_log_detail[] =  array('old tag id' => $upd_data['old_tag_id']);

					}else if($bulk_edit_options == 14)  //Updated calculation type

                    {


					$lot_rate_calc_type = $data['lot_rate_calc_type'];

					$lot_rate = $data['lot_rate'];

					$tag_purchase_cost = $data['tag_purchase_cost'];

					$lot_purchase_touch = $data['lot_purchase_touch'];

					$purchase_touch = $data['purchase_touch'];

					$lot_wastage_percentage = $data['lot_wastage_percentage'];

					$lot_making_charge = $data['lot_making_charge'];

					$lot_mc_type = $data['lot_mc_type'];

					$lot_calc_type = $data['lot_calc_type'];

					$tag_purchase_tax = $data['tag_purchase_tax'];

					$tag_purchase_taxable = $data['tag_purchase_taxable'];

					$stone_details = $data['stone_details'];

					$tagId = $data['tag_id'];

					$sales_value = $data['sales_value'];

					$net_wt = $data['net_wt'];

					$less_wt = $data['less_wt'];

					$gross_wt = $data['gross_wt'];





					$upd_data=array(



						'net_wt'      =>$net_wt,



						'less_wt'      =>$less_wt,



						'sales_value'      =>$sales_value,



						'lot_rate_calc_type'      =>$lot_rate_calc_type,



						'lot_rate'               =>$lot_rate,



						'tag_purchase_cost'      =>$tag_purchase_cost,



						'lot_purchase_touch'      =>$lot_purchase_touch,


						'purchase_touch'      =>$purchase_touch,



						'lot_wastage_percentage'      =>$lot_wastage_percentage,



						'lot_making_charge'      =>$lot_making_charge,



						'lot_mc_type'      =>$lot_mc_type,



						'lot_calc_type'      =>$lot_calc_type,



						'tag_purchase_tax'      =>$tag_purchase_tax,



						'tag_purchase_taxable'      =>$tag_purchase_taxable,



						'updated_time'              =>date("Y-m-d H:i:s"),



						'updated_by'                =>$employee,



					);



					$where_array = array('tag_id' => $tagId);



					$tag_update_id = $this->$model->updateRecord($upd_data, $where_array,'ret_taging');



					$update_status = $tag_update_id > 0 ? true : false;



					//print_r($upd_data);exit;



					$this->$model->deleteData('tag_id',$tagId,'ret_taging_stone');



					if(sizeof($stone_details) > 0)

					{



						foreach($stone_details as $stone)

						{

							$stone_data=array(

											'tag_id'                =>$tagId,

											'pieces'                =>$stone['stone_pcs'],

											'wt'                    =>$stone['stone_wt'],

											'stone_id'              =>$stone['stone_id'],

											'uom_id'                =>$stone['stone_uom_id'],

											'amount'                =>$stone['stone_price'],

											'rate_per_gram'         =>$stone['stone_rate'],

											'is_apply_in_lwt'       =>$stone['show_in_lwt'],

											'stone_cal_type'        =>$stone['stone_cal_type'],

											'pur_rate'       		=>$stone['pur_rate'],

											'pur_cost'     			=>$stone['pur_cost'],

											);



							$stoneInsert = $this->$model->insertData($stone_data,'ret_taging_stone');

							$stone_updated_log[]  = $stone_data;

						}

					}

					$previous_log_detail[] = array(

						'lot rate calc type' => $tag_data['lot_rate_calc_type'],

						'lot rate' => $tag_data['lot_rate'],

						'tag purchase cost' => $tag_data['tag_purchase_cost'],

						'lot purchase touch' => $tag_data['lot_purchase_touch'],

						'purchase touch' => $tag_data['purchase_touch'],

						'lot wastage percentage' => $tag_data['lot_wastage_percentage'],

						'lot making charge' => $tag_data['lot_making_charge'],

						'lot mc type' => $tag_data['lot_mc_type'],

						'lot calc type' => $tag_data['lot_calc_type'],

						'net wt' => $tag_data['net_wt'],


						'less wt' => $tag_data['less_wt'],

						'sale value' => $tag_data['sales_value'],

						'tag purchase taxable' => $tag_data['tag_purchase_taxable'],

						'tag purchase tax' => $tag_data['tag_purchase_tax'],

						'lot calc type' => $tag_data['lot_calc_type'],

						'stone'	  => $tag_data['stone_details']


					);

					$updated_log_detail[] =  array(


					'net wt'      =>$net_wt,



					'less wt'      =>$less_wt,



					'sales value'      =>$sales_value,



					'lot rate calc type'      =>$lot_rate_calc_type,



					'lot rate'               =>$lot_rate,



					'tag purchase cost'      =>$tag_purchase_cost,



					'lot purchase touch'      =>$lot_purchase_touch,


					'purchase touch'      =>$purchase_touch,



					'lot wastage percentage'      =>$lot_wastage_percentage,



					'lot making charge'      =>$lot_making_charge,



					'lot mc type'      =>$lot_mc_type,



					'lot calc type'      =>$lot_calc_type,



					'tag purchase tax'      =>$tag_purchase_tax,



					'tag purchase taxable'      =>$tag_purchase_taxable,

					'stone' => $stone_updated_log

				);

				}

					else if($bulk_edit_options == 15)  //Upget_tag_detailsdated to branch

					{

						if(!empty($to_branch)){

							$upd_data=array(

								'tagged_to_branch'         =>$to_branch,

								'updated_time'              =>$current_datetime,

								'updated_by'                =>$employee,

							);

						//    print_r($upd_data);exit;

							$where_array = array('tag_id' => $tagId);

							$tag_update_id = $this->$model->updateRecord($upd_data, $where_array,'ret_taging');

							$update_status = $tag_update_id > 0 ? true : false;

							if(!$update_status) {

								break;

							}

							$previous_log_detail[] = array(

								'branch' => $tag_data['tagged_to_branch'],

							);

							$updated_log_detail[] =  array('branch' => $upd_data['tagged_to_branch']);


						}

					}

					else if($bulk_edit_options == 13)  //Updated Purchase Cost

                    {

                        if(!empty($calc_type)){

                        	$upd_data=array(

                        		'calculation_based_on'      =>$calc_type,

                        		'updated_time'              =>$current_datetime,

                        		'updated_by'                =>$employee,

                        	);

                        	$where_array = array('tag_id' => $tagId);

                        	$tag_update_id = $this->$model->updateRecord($upd_data, $where_array,'ret_taging');

                        	$update_status = $tag_update_id > 0 ? true : false;

                        	if(!$update_status) {

                        		break;

							}

							$previous_log_detail[] = array(

								'calc type' => $tag_data['calculation_based_on'],

							);

							$updated_log_detail[] =  array('calc type' => $upd_data['calculation_based_on']);

                        }

                    }else if($bulk_edit_options == 9)
					{

						$old_img_data = $this->$model->get_img_by_id($tagId);

						$new_img_data = array();

						if(sizeof($p_ImgData) > 0)

						{

								$_FILES['precious']    =  array();

								foreach($p_ImgData as $precious){

									//print_r($precious);exit;

									$imgFile = $this->base64ToFile($precious->src);

									$_FILES['precious'][] = $imgFile;

								}

							   $img_arr = array();

								if(!empty($_FILES))

								{

										$img_true=$this->$model->checkImageAvail($tagId);

										//print_r($img_true);exit;

										$folder =  self::IMG_PATH."tag/";

										if (!is_dir($folder)) {

											mkdir($folder, 0777, TRUE);

										}

										if(isset($_FILES['precious'])){

											$precious_imgs = "";

											$arrayimg_tag   = array();

											$set_zero = array('is_default'=>0);

											$this->$model->updateData($set_zero, 'tag_id', $tagId, 'ret_taging_images');

											$image_inc = 1;

											foreach($_FILES['precious'] as $file_key => $file_val){

												if($image_inc == 1) {

													$is_default = 1;

												} else {

													$is_default = 0;

												}

												if($file_val['tmp_name'])

												{

													$img_name =  $tagId."_". mt_rand(100001,999999).".jpg";

													$path = $folder."/".$img_name;

													$result = $this->upload_img('image',$path,$file_val['tmp_name']);

													if($result){

														$precious_imgs = $img_name;

														$arrayimg_tag = array(

														 'tag_id'   => $tagId,

														 'image'    => $img_name,

														 'date_add' => $current_datetime,

														 'is_default' => $is_default

														);

														//print_r($arrayimg_tag);exit;

														$img_arr[]   = $arrayimg_tag;

													  	$insImageId = $this->$model->insertData($arrayimg_tag,'ret_taging_images');

													  	$update_status = $insImageId > 0 ? true : false;

														if(!$update_status) {

															break;

														}

														$new_img_data[] = array('image' => $img_name, 'is_default' => $is_default);

													}

												}

												$image_inc++;

											}

										}

								}

						}

						$previous_log_detail[] = array(

							'image_add' => $old_img_data,

						);

						$updated_log_detail[] =  array('image_add' => $new_img_data);

					} else if($bulk_edit_options == 16) {

						$upd_data['design_id'] = $id_design;

						$upd_data['id_sub_design'] = $id_sub_design;

						$tag_update_id=$this->$model->updateData($upd_data,'tag_id',$tagId,'ret_taging');

						$update_status = $tag_update_id > 0 ? true : false;

						if(!$update_status) {

							break;

						}

						$previous_log_detail[] = array(

							'design id' => $tag_data['design_id'],

							'sub design id' => $tag_data['id_sub_design'],

							'design name' => $tag_data['design_name'],

							'sub design name' => $tag_data['sub_design_name'],

						);

						$design_name_new = "";

						$sub_design_name_new = "";

						if($upd_data['design_id'] > 0) {

							$des_details = $this->ret_catalog_model->get_ret_design($upd_data['design_id']);

							$design_name_new = $des_details['design_name'];

						}

						if($upd_data['id_sub_design'] > 0) {

							$des_details = $this->ret_catalog_model->get_sub_design($upd_data['id_sub_design']);

							$sub_design_name_new = $des_details['sub_design_name'];

						}

						$updated_log_detail[] =  array('design id' => $upd_data['design_id'], 'sub design id' => $upd_data['id_sub_design'],'design name' => $design_name_new, 'sub design name' => $sub_design_name_new);

					}

					else if($bulk_edit_options == 17) {

						$upd_data['narration'] = $narration;

						// print_r($upd_data);exit;

						$tag_update_narration=$this->$model->updateData($upd_data,'tag_id',$tagId,'ret_taging');

						// print_r($tag_update_narration);exit;

						$update_status = $tag_update_narration > 0 ? true : false;

						if(!$update_status) {

							break;

						}

						$previous_log_detail[] = array(

							'tag narration' => $tag_data['narration'],

						);

						$updated_log_detail[] =  array('tag narration' => $upd_data['narration']);

					}

					$log_data = array(

                        	'id_log'        => $this->session->userdata('id_log'),

                        	'event_date'	=> date("Y-m-d H:i:s"),

                        	'module'      	=> 'Tag',

                        	'operation'   	=> 'Bulk Tag Edit',

                        	'record'        => $tagId,

                        	'remark'       	=> 'Bulk Tag Edit Updated Successfully'

                        );

                    $this->log_model->log_detail('insert','',$log_data);

					/*echo "<pre";
					print_r($previous_log_detail);
					print_r($updated_log_detail);
					exit;*/

					$bulk_update_log = array(

						'edit_datetime' => $current_datetime,

						'edit_tag'		=> $tagId,

						'edit_branch_id'=> $reqdata['id_branch'],

						'edit_emp_id'	=> $employee,

						'edit_field'	=> $edit_field,

						'previous_values' => json_encode($previous_log_detail),

						'updated_values'  => json_encode($updated_log_detail)
					);

					$this->$model->insertData($bulk_update_log,'ret_bulk_tag_edit_log');

				}
				if($update_status)

				{

					$remark = 'Tags Modified Successfully. OTP : '.$post_otp;

					$message = 'Tags Modified Successfully';

					$this->db->trans_commit();



					if($post_otp!='')

					{

					     $update_otp=$this->$model->updateData($tag_ids,'otp_code',$post_otp,'otp');

					}

					$status=array('status'=>true,'msg'=>$message);

				}

				else

				{

					$this->db->trans_rollback();

					$status=array('status'=>false,'msg'=>'Unable to Proceed Your Request');

				}

    		  }

		  	echo json_encode($status);

	}

	function admin_approval()

	{

		$model="ret_tag_model";

		$data           =$this->$model->get_ret_settings('otp_approval_nos');

		$mobile_num     = array(explode(',',$data));

		$sent_otp='';

		$service = $this->admin_settings_model->get_service_by_code('tag_edit');

		if($service['serv_sms']==1)

		{

    		foreach($mobile_num[0] as $mobile)

    		{

    				if($mobile)

    				{

    					 $this->db->trans_begin();

    					$this->session->unset_userdata("tagging_otp");

    					$OTP = mt_rand(100001,999999);

    					$sent_otp.=$OTP.',';

    					$this->session->set_userdata('tagging_otp',$sent_otp);

    					$this->session->set_userdata('tagging_otp_exp',time()+60);

                        $message="Hi Your OTP  For Tag Editing is :  ".$OTP." Will expire within 1 minute";

    					$otp_gen_time = date("Y-m-d H:i:s");

    					$insData=array(

    					'mobile'=>$mobile,

    					'otp_code'=>$OTP,

    					'otp_gen_time'=>date("Y-m-d H:i:s"),

    					'module'=>'Bulk Tag Edit',

    					'id_emp'=>$this->session->userdata('uid')

    					);

    					$insId = $this->$model->insertData($insData,'otp');

    						 if($insId)

    						 {

    						 	$this->send_sms($mobile,$message,$service['dlt_te_id']);

    						 	$status=array('status'=>true,'msg'=>'OTP sent Successfully','sms_req'=>$service['serv_sms']);

    						 }else{

    						     $status=array('status'=>false,'msg'=>'Unabe To Send Try Again');

    						 }

    				}

    		}

	    }

	    else

	    {

	        $status=array('status'=>true,'sms_req'=>$service['serv_sms']);

	    }

		echo json_encode($status);

	}

	function resendotp()

	{

		$model="ret_tag_model";

		$data           =$this->$model->get_ret_settings('otp_approval_nos');

		$mobile_num     = array(explode(',',$data));

		$sent_otp='';

		foreach($mobile_num[0] as $mobile)

		{

				if($mobile)

				{

					$this->db->trans_begin();

					$this->session->unset_userdata("tagging_otp");

					$this->session->unset_userdata("tagging_otp_exp");

					$OTP = mt_rand(100001,999999);

					$sent_otp.=$OTP.',';

					$this->session->set_userdata('tagging_otp',$sent_otp);

					$this->session->set_userdata('tagging_otp_exp',time()+60);

					$message="Hi Your OTP  For Tag Editing is :  ".$OTP." Will expire within 1 minute.";

					$otp_gen_time = date("Y-m-d H:i:s");

					$insData=array(

					'mobile'=>$mobile,

					'otp_code'=>$OTP,

					'otp_gen_time'=>date("Y-m-d H:i:s"),

					'module'=>'Bulk Tag Edit',

					'send_resend'=>1,

					'id_emp'=>$this->session->userdata('uid')

					);

					$insId = $this->$model->insertData($insData,'otp');

						 if($insId)

						 {

						 	$this->send_sms($mobile,$message);

						 }

				}

		}

	 if($insId)

	  	{

	  			$this->db->trans_commit();

	  			$status=array('status'=>true,'msg'=>'OTP sent Successfully');

	  	}

	  	else

	  	{

	  			$this->db->trans_rollback();

	  			$status=array('status'=>false,'msg'=>'Unabe To Send Try Again');

	  	}

		echo json_encode($status);

	}

	function send_sms($mobile,$message,$dlt_te_id)

	{

		if($this->config->item('sms_gateway') == '1'){

		    $this->sms_model->sendSMS_MSG91($mobile,$message,"",$dlt_te_id);

		}

		elseif($this->config->item('sms_gateway') == '2'){

	        $this->sms_model->sendSMS_Nettyfish($mobile,$message,'trans');

		}

	}

	function get_lot_inward_details()

	{

		$model="ret_tag_model";

		$lot_no=$this->input->post('lot_no');

		$lot_product=$this->input->post('lot_product');

		$lot_id_design=$this->input->post('lot_id_design');

		$lot_split=$this->input->post('is_split');

		$id_employee=$this->input->post('id_employee');

		$data=$this->$model->get_lot_inward_details($lot_no,$lot_product,$lot_id_design,$lot_split,$id_employee);

		echo json_encode($data);

	}

	function get_lot_products()

	{

		$model="ret_tag_model";

		$lot_no=$this->input->post('lot_no');

		$searchTxt=$this->input->post('searchTxt');

		$data=$this->$model->get_lot_products($lot_no,$searchTxt);

		echo json_encode($data);

	}

	function get_lot_split_products()

	{

		$model="ret_tag_model";

		$lot_no=$this->input->post('lot_no');

		$id_employee=$this->input->post('id_employee');

		$data=$this->$model->get_lot_split_products($lot_no,$id_employee);

		echo json_encode($data);

	}

	function get_lot_designs()

	{

		$model="ret_tag_model";

		$lot_no=$this->input->post('lot_no');

		$lot_product=$this->input->post('lot_product');

		$searchTxt=$this->input->post('searchTxt');

		$data=$this->$model->get_lot_designs($lot_no,$lot_product,$searchTxt);

		echo json_encode($data);

	}

	function get_tagging_details()

	{

	   $model="ret_tag_model";

	   $range['from_date']  = $this->input->post('from_date');

	   $range['to_date']  = $this->input->post('to_date');

	   $range['lot_no'] = $this->input->post('lot_no');

	   $range['po_ref_no'] = $this->input->post('po_ref_no');

	   $range['id_karigar'] = $this->input->post('id_karigar');

	   $data=$this->$model->get_tagging_details($range['from_date'],$range['to_date'],$range['lot_no'],$range['po_ref_no'],$range['id_karigar']);

	   echo json_encode($data);

	}

	function get_tag_detail_list()

	{

		$data['main_content'] = "tagging/list" ;

		$this->load->view('layout/template', $data);

	}

	function lot_tag_detail()

	{

	     $model="ret_tag_model";

        $range['from_date']  = $this->input->post('from_date');

        $range['to_date']   = $this->input->post('to_date');

        $tag_lot_id         =$this->input->post('tag_lot_id');

        $id_employee        =$this->input->post('id_employee');

        $list = $this->$model->ajax_getTaggingList($range['from_date'],$range['to_date'],$tag_lot_id,'',$id_employee);

        $access = $this->admin_settings_model->get_access('admin_ret_tagging/tagging/list');

        $data = array(

        'list'  => $list,

        'access'=> $access

        );

        echo json_encode($data);

	}

	//Get products filtered by lot number (for duplicate tagging page)

	function get_products_by_lot()
	{
		$model = "ret_tag_model";
		$lot_no = $this->input->post('lot_no');
		$id_branch = $this->input->post('id_branch');
		$products = $this->$model->get_products_by_lot($lot_no, $id_branch);
		echo json_encode($products);
	}

	//Duplicate

	function get_duplicate_tag()

	{

		$model="ret_tag_model";

		$data=$_POST;

		$tag=$this->$model->get_duplicate_tag($data);

		echo json_encode($tag);

	}

	function send_tag_otp()

	{

		$model="ret_tag_model";

		$data           =$this->$model->get_ret_settings('otp_approval_nos');

		$mobile_num     = array(explode(',',$data));

		$sent_otp='';

		$service = $this->admin_settings_model->get_service_by_code('duplicate_tag');

    	 if($service['serv_sms']==1)

    	 {

    		foreach($mobile_num[0] as $mobile)

    		{

    			if($mobile)

    			{

    				$this->db->trans_begin();

    				$this->session->unset_userdata("tagging_otp");

    				$OTP = mt_rand(100001,999999);

    				$sent_otp.=$OTP.',';

    				$this->session->set_userdata('tagging_otp',$sent_otp);

    				$this->session->set_userdata('tagging_otp_exp',time()+60);

    				$message="Hi Your OTP  For Duplicate Tag is :  ".$OTP." Will expire within 60Sec.";

    				$otp_gen_time = date("Y-m-d H:i:s");

    				$insData=array(

    				'mobile'=>$mobile,

    				'otp_code'=>$OTP,

    				'otp_gen_time'=>date("Y-m-d H:i:s"),

    				'module'=>'Duplicate Tag Edit',

    				'id_emp'=>$this->session->userdata('uid')

    				);

    				$insId = $this->$model->insertData($insData,'otp');

    				 if($insId)

    				 {

    				 	$this->send_sms($mobile,$message,$service['dlt_te_id']);

    				 }

    			}

    		}

    		if($insId)

    		 {

    		     $this->db->trans_commit();

    		     $status=array('status'=>true,'msg'=>'OTP sent Successfully','sms_req'=>$service['serv_sms']);

    		 }else{

    		      $this->db->trans_rollback();

    		     $status=array('status'=>false,'msg'=>'Unabe To Send Try Again');

    		 }

	    }

	    else

	    {

	        $status=array('status'=>true,'sms_req'=>$service['serv_sms']);

	    }

		echo json_encode($status);

	}

	function verify_otp()

	{

		$model="ret_tag_model";

		$post_otp=$this->input->post('otp');

		$session_otp=$this->session->userdata('tagging_otp');

		$otp = array(explode(',',$session_otp));

		$this->db->trans_begin();

		if($post_otp!='')

		{

    		foreach($otp[0] as $OTP)

    		{

    			if($OTP==$post_otp)

    			{

    				if(time() >= $this->session->userdata('tagging_otp_exp'))

    				{

    					$this->session->unset_userdata('tagging_otp');

    					$this->session->unset_userdata('tagging_otp_exp');

    					$status=array('status'=>false,'msg'=>'OTP has been expired');

    				}

    				else

    				{

    					$this->db->trans_commit();

    					$updData=array('is_verified'=>1,'verified_time'=>date("Y-m-d H:i:s"));

    					$updStatus=$this->$model->updateData($updData,'otp_code',$post_otp,'otp');

    					$status=array('status'=>true,'msg'=>'OTP Verified Successfully.');

    				}

    				break;

    			}

    			else

    			{

    				$status=array('status'=>false,'msg'=>'Please Enter Valid OTP');

    			}

    	    }

	   }else{

	       $status=array('status'=>false,'msg'=>'Please Enter Valid OTP');

	   }

	  	echo json_encode($status);

	}

	//Duplicate

	//Tag scan

	function get_tag_scan_details()

	{

		$model="ret_tag_model";

		$tag_id=$this->input->post('tag_id');

		$print_taken=$this->input->post('print_taken');

		$tag_details=$this->$model->get_entry_records($tag_id);

		$status=$this->$model->get_scanned_details($tag_id);

		$id_branch=$this->session->userdata('id_branch');

		//$id_branch=1;

		if($status)

		{

			$this->db->trans_begin();

			if($this->session->userdata('branch_settings')==1)

			{

				if($tag_details['current_branch']==$id_branch)

				{

					$insData=array(

					'tag_id'	=>$tag_id,

					'id_branch' => ($id_branch!='' ? $id_branch:NULL),

					'date_add'	=> date("Y-m-d H:i:s"),

					'created_by'=> $this->session->userdata('uid'),

					);

					$insId=$this->$model->insertData($insData,'ret_tag_scanned');

					if($insId)

					{

						$data=array('status'=>TRUE,'tag_details'=>$tag_details,'msg'=>'Tag Scanned Successfully.');

					}else{

						$data=array('status'=>FALSE,'msg'=>'Unable To Proceed Your Request');

					}

				}

				else{

					$data=array('status'=>FALSE,'msg'=>'Invalid Branch');

				}

			}

			else

			{

				$insData=array(

				'tag_id'	=>$tag_id,

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

		if($this->db->trans_status()===TRUE)

		{

			$this->db->trans_commit();

			$log_data = array(

				'id_log'        => $this->session->userdata('id_log'),

				'event_date'    => date("Y-m-d H:i:s"),

				'module'        => 'Tag Scan',

				'operation'     => 'Add',

				'record'        =>  $insId,

				'remark'        => 'Tag Scanned successfully'

				);

				$this->log_model->log_detail('insert','',$log_data);

		}else{

			$this->db->trans_rollback();

		}

		echo json_encode($data);

	}

	//Tag scan

	function get_order_details()

	{

		$model="ret_tag_model";

		$data=$this->$model->get_order_details($_POST['lot_no']);

		echo json_encode($data);

	}

	function get_tag_marking()

	{

		$model="ret_tag_model";

		$responseData=array();

		if($_POST['est_no']=='')

		{

		    $data=$this->$model->get_tag_marking($_POST);

		    $responseData=array('status'=>true,'data'=>$data);

		}

		else if($_POST['est_no']!='')

		{

	    	if($this->session->userdata('branch_settings')==1 && ($_POST['id_branch']!='' && $_POST['id_branch']!=0))

	    	{

	    	    $data=$this->$model->getEstTaglist($_POST);

	    	    $responseData=array('status'=>true,'data'=>$data);

	    	}

	    	else if($this->session->userdata('branch_settings')==1 && ($_POST['id_branch']=='' || $_POST['id_branch']==0))

	    	{

	    	   $responseData=array('status'=>false,'message'=>'Invalid Branch..');

	    	}

		}

		echo json_encode($responseData);

	}

	function update_tag_mark($status,$id)

	{

		$model="ret_tag_model";

		$this->db->trans_begin();

		$data = array('tag_mark'  		=> $status,

					   'green_tag_date' 	=> date("Y-m-d H:i:s"),

					   'green_tag_marked_by' 	=> $this->session->userdata('uid')

					);

		$status = $this->$model->updateData($data,'tag_id',$id,'ret_taging');

		if($this->db->trans_status()===TRUE)

         {

		 	$this->db->trans_commit();

			 $log_data = array(

				'id_log'        => $this->session->userdata('id_log'),

				'event_date'    => date("Y-m-d H:i:s"),

				'module'        => 'Tagging',

				'operation'     => 'Add',

				'record'        =>  $status ,

				'remark'        => 'Tag Mark updated successfully'

				);

				$this->log_model->log_detail('insert','',$log_data);

			$this->session->set_flashdata('chit_alert',array('message'=>'Tag Mark updated successfully.','class'=>'success','title'=>'Tagging'));

		}

		else

		{

			$this->db->trans_rollback();

			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Tagging'));

		}

		redirect('admin_ret_tagging/tagging/tag_mark');

	}

	function get_tag_edit_det()

	{

		$model="ret_tag_model";

		$data=$this->$model->get_tag_edit_det($_POST);

		echo json_encode($data);

	}

	function update_tag()

	{

		$model="ret_tag_model";

		$reqdata   = $this->input->post('req_data');

		$id_design   = $this->input->post('id_design');

		$id_sub_design   = $this->input->post('id_sub_design');

		$id_size   = $this->input->post('id_size');

		$old_tag_id   = $this->input->post('old_tag_id');

		$this->db->trans_begin();

		foreach($reqdata as $tag)

		{

		    $data = array(

		               'design_id'  	=> ($id_design!='' ? $id_design:$tag['id_design']),

		               'size'  	        => ($id_size!='' ? ($id_size!='' ? $id_size:NULL):($tag['id_size']!='' ?$tag['id_size'] :NULL)),

		               'id_sub_design'  => ($id_sub_design!='' ? ($id_sub_design!='' ? $id_sub_design:NULL):($tag['id_sub_design']!='' ?$tag['id_sub_design'] :NULL)),

		               'old_tag_id'     =>($old_tag_id !='' ? ($old_tag_id !='' ? $old_tag_id :NULL):($tag['old_tag_id']!='' ?$tag['old_tag_id'] :NULL)),

		               'remarks'        =>($old_tag_id !='' ? ($old_tag_id !='' ? $old_tag_id :NULL):($tag['old_tag_id']!='' ?$tag['old_tag_id'] :NULL)),

					   'updated_time' 	=> date("Y-m-d H:i:s"),

					   'updated_by' 	=> $this->session->userdata('uid')

					);

			$status = $this->$model->updateData($data,'tag_id',$tag['tag_id'],'ret_taging');

			//print_r($this->db->last_query());exit;

			if($status)

			{

			    $log_data = array(

                            'id_log'        => $this->session->userdata('id_log'),

                            'event_date'    => date("Y-m-d H:i:s"),

                            'module'        => 'Tagging',

                            'operation'     => 'Update',

                            'record'        =>  $tag['tag_id'],

                            'remark'        => 'Tag Updated successfully'

                            );

                $this->log_model->log_detail('insert','',$log_data);

			}

		}

		if($this->db->trans_status()===TRUE)

         {

		 	$this->db->trans_commit();


			$this->session->set_flashdata('chit_alert',array('message'=>'Tag  updated successfully.','class'=>'success','title'=>'Tagging'));

		}

		else

		{

			$this->db->trans_rollback();

			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Tagging'));

		}

	}

	function get_employee()

	{

		$model="ret_tag_model";

		$data=$this->$model->get_employee();

		echo json_encode($data);

	}

	function get_ActiveSize()

	{

		$model="ret_tag_model";

		$data=$this->$model->get_ActiveSize($_POST['id_product']);

		echo json_encode($data);

	}

	//Order link

	public function getTaggingBySearch(){

		$model = "ret_tag_model";

		$data = $this->$model->getTaggingBySearch($_POST['searchTxt'],$_POST['id_branch'],$_POST['old_tag_id']);

		echo json_encode($data);

	}

	public function getOrdersBySearch()

	{

	    $model = "ret_tag_model";

		$data = $this->$model->getOrdersBySearch($_POST['searchTxt'],$_POST['id_branch'],$_POST['id_product'],$_POST['id_design'],$_POST['fin_year_code']);

		echo json_encode($data);

	}

	function getOrderDetailBySearch()

	{

	    $model = "ret_tag_model";

		$data = $this->$model->getOrderDetailBySearch($_POST['id_customerorder'],$_POST['id_product'],$_POST['id_design']);

		echo json_encode($data);

	}

		function update_order_link()

	{

	    $model="ret_tag_model";

	    $set_model="admin_settings_model";

		$reqdata   = $this->input->post('req_data');

		// print_r($reqdata);exit;

		$this->db->trans_begin();

		foreach($reqdata as $tag)

		{

		    $data = array(

		               'id_orderdetails'=> $tag['id_orderdetails'],

					    'old_tag_id'    => $tag['old_tag_id'],

					   'updated_time' 	=> date("Y-m-d H:i:s"),

					   'updated_by' 	=> $this->session->userdata('uid')

					);




			$status = $this->$model->updateData($data,'tag_id',$tag['tag_id'],'ret_taging');

			// print_r($this->db->last_query());exit;

			if($tag['order_type'] == 3){

				$completed_weight = $tag['weight'] + $tag['tag_weight'];

				$this->$model->updateData(array('orderstatus'=>4,'completed_weight'=>$completed_weight,'deliverydate'=> date("Y-m-d H:i:s")),'id_orderdetails',$tag['id_orderdetails'],'customerorderdetails');

				$this->$model->updateData(array('orderstatus'=>4,'deliveredon'=> date("Y-m-d H:i:s")),'id_order',$tag['id_orderdetails'],'joborder');

			}else{
				$this->$model->updateData(array('orderstatus'=>4,'deliverydate'=> date("Y-m-d H:i:s")),'id_orderdetails',$tag['id_orderdetails'],'customerorderdetails');

				$this->$model->updateData(array('orderstatus'=>4,'deliveredon'=> date("Y-m-d H:i:s")),'id_order',$tag['id_orderdetails'],'joborder');
			}

			if($status)

			{

			    $service = $this->$set_model->get_service_by_code('CUS_ORD_DEL');

			    if($service['serv_whatsapp'] == 1)

				{

				    $sms_data=$this->admin_usersms_model->Get_service_code_sms('CUS_ORD_DEL',$tag['id_orderdetails'],'');

					if($sms_data['mobile']!='')

					{

					    $whatsapp=$this->admin_usersms_model->send_whatsApp_message($sms_data['mobile'],$sms_data['message']);

					}

				}

			    $log_data = array(

                            'id_log'        => $this->session->userdata('id_log'),

                            'event_date'    => date("Y-m-d H:i:s"),

                            'module'        => 'Tagging',

                            'operation'     => 'Update',

                            'record'        =>  $tag['tag_id'],

                            'remark'        => 'Order Link Updated successfully'

                            );

                $this->log_model->log_detail('insert','',$log_data);

			}

		}

		if($this->db->trans_status()===TRUE)

         {

		 	$this->db->trans_commit();

			$this->session->set_flashdata('chit_alert',array('message'=>'Order  Linked successfully.','class'=>'success','title'=>'Tagging'));

		}

		else

		{

			$this->db->trans_rollback();

			$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed the requested operation','class'=>'danger','title'=>'Tagging'));

		}

	}

	//Order link

	//Generate Tag Code

	function generateTagCode($lastTagCode){

		$model = "ret_tag_model";
		$tag_format = $this->$model->get_ret_settings('tag_format');
		$maps = $this->_get_tag_maps();
		$map_letters = implode('', array_values($maps['digit']));
		
		if($tag_format == 2 || preg_match('/['.preg_quote($map_letters, '/').']/', $lastTagCode)) {
			$tagCode = $this->convert_alpha_to_numeric($lastTagCode);
		} else {
			$tagCode = $lastTagCode;
		}

        $code_det       = explode('-',$tagCode);

        $alpha_char		='';

        // Split Alphabet and Serial number

        if(preg_match('/[A-Z]+\K/',$code_det[1]))

        {

        	$str_split = preg_split('/[A-Z]+\K/',$code_det[1]);

        	$tag_number=$str_split[1];

        	$alpha_char=$str_split[0];

        }else{

        	$tag_number=$code_det[1];

        }

        //  Increment Number

        if($tag_number!=NULL && $tag_number!='' && $tag_number!=99999)

        {

        	$number = (int) $tag_number;

        	$number++;

        	$code_number=str_pad($number, 5, '0', STR_PAD_LEFT);

        }

        else

        {

        	$code_number=str_pad('1', 5, '0', STR_PAD_LEFT);

        }

        //  Increment Alphabet if reached 99999

        if($tag_number==99999)

        {

        	if($alpha_char == '')

        	{

        		$alpha_char='A';

        	}else{

        		$alpha_char=++$alpha_char;

        	}

        }

		if($tag_format == 2) {
			return $this->convert_numeric_to_alpha($alpha_char.''.$code_number);
		}

        return $alpha_char.''.$code_number;

	}

	//Generate Tag Code


	private function _get_tag_maps() {
		$model = "ret_tag_model";
		$map_str = $this->$model->get_ret_settings('tag_digit_map');
		
		if (empty($map_str) || strlen($map_str) < 10) {
			return array(
				'digit' => self::DIGITMAP,
				'reverse' => self::REVERSEMAP
			);
		}

		$digit_map = array();
		$reverse_map = array();
		for ($i = 0; $i < 10; $i++) {
			$digit_map[(string)$i] = $map_str[$i];
			$reverse_map[$map_str[$i]] = (string)$i;
		}

		return array(
			'digit' => $digit_map,
			'reverse' => $reverse_map
		);
	}

	private function convert_numeric_to_alpha($input) {
		if ($input === null || $input === '') return $input;
		$maps = $this->_get_tag_maps();
		return strtr($input, $maps['digit']);
	}

	private function convert_alpha_to_numeric($input) {
		if ($input === null || $input === '') return $input;
		$maps = $this->_get_tag_maps();
		return strtr($input, $maps['reverse']);
	}

	private function format_tag_code($tag_code) {
		$model = "ret_tag_model";
		$tag_format = $this->$model->get_ret_settings('tag_format');
		if($tag_format == 2) {
			return $this->convert_numeric_to_alpha($tag_code);
		}
		return $tag_code;
	}

	public function validate_huid()

	{

	    $model = "ret_tag_model";

		$sku_id   = $this->input->post('sku_id');

		$data = $this->$model->validate_huid($sku_id);

        if(count($data) > 0)

		{

			$validate = true;

		}else{

			$validate = false;

		}

		echo json_encode($validate);

	}

	function generate_tagqrcode($tag)

	{

		if($tag > 0) {

			$tag_items[] = $this->get_tagArray($tag);

			$this->generate_printer_code($tag_items);

		}

	}

	function generate_retagqrcode($id_process)

	{

	    $model = "ret_tag_model";

		$tags = $this->$model->get_reTagDetails($id_process);

		if(count($tags) > 0) {

			$tag_items = array();

			foreach($tags as $tag) {

				$tag_id = $tag['tag_id'];

				if($tag_id > 0) {

					$tag_items[] = $this->get_tagArray($tag_id);

				}

			}

			if(count($tag_items) > 0) {

				$this->generate_printer_code($tag_items);

			}

		}

	}

		//GET DESIGN PRODUCTS

	function get_active_design_products()

	{

	     $model="ret_tag_model";

	     $data=$this->$model->get_active_design_products($_POST);

	     echo json_encode($data);

	}

	function get_ActiveSubDesingns()

	{

	     $model="ret_tag_model";

	     $data=$this->$model->get_ActiveSubDesingns($_POST);

	     echo json_encode($data);

	}

	//GET DESIGN PRODUCTS

	//V.A and M.C Settings

	function get_wastage_settings_details()

	{

	     $model="ret_tag_model";

	     $data=$this->$model->get_wastage_settings_details();

	     echo json_encode($data);

	}

	//V.A and M.C Settings

	//Attributes for tagging

	function get_attributes_from_subdesign()

	{

		$model="ret_catalog_model";

		$product_id 	= $_POST['product_id'];

		$design_id 		= $_POST['design_id'];

		$subdesign_id 	= $_POST['subdesign_id'];

	    $data=$this->$model->get_attributes_from_subdesign($product_id, $design_id, $subdesign_id);

	    echo json_encode($data);

	}

	function get_product_charges()

	{

		$prod_id = $_POST['prod_id'];

		$model="ret_catalog_model";

		$data = $this->$model->get_product_charges($prod_id);

		echo json_encode($data);

	}

	function get_tag_charges($tag_id)

	{

		$model="ret_tag_model";

		$data = $this->$model->getTagCharges($tag_id);

		echo json_encode($data);

	}

	function get_tag_attributes($tag_id)

	{

		$model="ret_tag_model";

		$data = $this->$model->getTagAttributes($tag_id);

		echo json_encode($data);

	}

	function save_base64_image($base64_string, $path)

	{

		$data = str_replace('data:image/png;base64,', '', $base64_string);

		$data = str_replace(' ', '+', $data);

		$data = base64_decode($data);

		$success = file_put_contents($path, $data);

		$data = base64_decode($data);

		$source_img = imagecreatefromstring($data);

		$rotated_img = imagerotate($source_img, 90, 0);

		$imageSave = imagejpeg($rotated_img, $path, 10);

		imagedestroy($source_img);

		return true;

	}

	function get_rate_from_metal_and_purity($metal_id, $purity_id) {

		$model="ret_tag_model";

		$rate = $this->$model->get_rate_from_metal_and_purity($metal_id, $purity_id);

		if($rate > 0) {

			$responseData =	array('status'=>true, 'msg'=>'Metal rate retrived successfully', 'metal_rate' => $rate);

		} else {

			$err_msg = $this->db->_error_message();

			$responseData =	array('status'=>false, 'msg'=>'Unable to proceed the requested process. '.$err_msg, 'metal_rate' => $rate);

		}

		echo json_encode($responseData);

	}

	function delete_tag_attribute($attr_tag_id)

	{

		$model="ret_tag_model";

		$status = $this->$model->deleteData('attr_tag_id',$attr_tag_id,'ret_tagging_attributes');

		if($status == 1)

			$msg = "Attribute deleted successfully";

		else

			$msg = "Error occured. Please try again";

		$responseData =	array('status'=> $status == 1 ? TRUE : FALSE, 'msg'=> $msg);

		echo json_encode($responseData);

	}

	//Get PO details to arrive purchase cost

	function get_po_details()

	{

	    $po_filter_data = $_POST;

	    $model      = "ret_tag_model";

	    $response_data = $this->$model->getPoDetailsforPC($po_filter_data); // Get PO details to calculate purchase cost

	    echo json_encode($response_data);

	}

	//Branch Transfer

	function add_to_transfer_tag()

	{

	    $model      = "ret_tag_model";

	    $reqData    = $_POST['req_data'];

	    $tot_pcs    = $_POST['tot_pcs'];

	    $gross_wt   = $_POST['total_gwt'];

	    $net_wt     = $_POST['total_nwt'];

	    $responseData = [];

	    $trans_code = $this->ret_brntransfer_model->trans_code_generator();

    	$bt_data = array(

    		'branch_trans_code'		=> $trans_code,

    		'transfer_from_branch'	=> (isset($_POST['id_branch']) ? $_POST['id_branch'] : NULL),

    		'transfer_to_branch'	=> (isset($_POST['to_branch']) ? $_POST['to_branch'] : NULL),

    		'transfer_item_type'	=> 1,

    		'pieces'				=> $tot_pcs,

    		'grs_wt'				=> $gross_wt,

    		'net_wt'				=> $net_wt,

    		'added_type'			=> 2,

    		'create_by'				=> $this->session->userdata('uid'),

    		'created_time'			=> date('Y-m-d H:i:s'),

    	  );

    	$this->db->trans_begin();

    	$id_bt = $this->$model->insertData($bt_data,'ret_branch_transfer');

	    if($id_bt)

	    {

	        foreach($reqData as $tag_data){

			    if($tag_data['tag_id'] > 0){

					$items = array(

								'transfer_id'	=> $id_bt,

								'tag_id'		=> $tag_data['tag_id'],

								'id_lot_inward_detail'	=> $tag_data['id_lot_inward_detail'],

								);

					$status = $this->$model->insertData($items,'ret_brch_transfer_tag_items');

			    }

			}

	    }

	    if($this->db->trans_status()===TRUE)

		{

		   $this->db->trans_commit();

		   $log_data = array(

			'id_log'        => $this->session->userdata('id_log'),

			'event_date'    => date("Y-m-d H:i:s"),

			'module'        => 'Branch Transfer',

			'operation'     => 'Add',

			'record'        =>  $id_bt,

			'remark'        => 'Branch Transfer added successfully'

			);

			$this->log_model->log_detail('insert','',$log_data);

		   $responseData=array('status'=>true,'message'=>'Branch Transfer added successfully','trans_code'=>$trans_code);

		}

		else

		{

			/*echo $this->db->last_query();

			echo $this->db->_error_message();exit;*/

			$this->db->trans_rollback();

			$responseData=array('status'=>false,'message'=>'Unable to proceed the requested process');

		}

	   echo json_encode($responseData);

	}

	//Branch Transfer

	//Re-Tagging

	function retagging($type="")

	{

	    $model = "ret_tag_model";

		$SETT_MOD = self::SETT_MOD;

		switch($type)

		{

			case 'list':

					$data['main_content'] = "tagging/retagging_list" ;

					$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_tagging/retagging/list');

        			$this->load->view('layout/template', $data);

				break;

			case 'add':

					$data['main_content'] = "tagging/retagging_form" ;

					// Resolve branch from controller safely - never hardcode in view
					// Try Head Office first (is_ho=1), fallback to session branch
					$ho = $this->ret_tag_model->get_headOffice();
					if(!empty($ho) && isset($ho['id_branch']) && $ho['id_branch'] != '')
					{
						$data['default_id_branch'] = $ho['id_branch'];
					}
					else
					{
						// is_ho not configured - fallback to session branch
						$data['default_id_branch'] = $this->session->userdata('id_branch');
					}

					// Pass fin_year list for ST retagging filter
					$fin_sql = $this->db->query("SELECT fin_year_code,fin_status,fin_year_name FROM ret_financial_year");
					$data['fin_year'] = $fin_sql->result_array();

        			$this->load->view('layout/template', $data);

			break;

			case 'partly_sale':

			    $data=$this->$model->get_partly_sale_details($_POST);

			    echo json_encode($data);

			break;

			case 'old_metal':

			    $data=$this->$model->get_old_metal_details($_POST);

			    echo json_encode($data);

			break;

			case 'process_list':

			    $data=$this->$model->get_stock_process_details($_POST);

			    echo json_encode($data);

			break;

			case 'non_tag_details':

			    $data=$this->$model->get_non_tag_details($_POST);

			    echo json_encode($data);

			break;

			case 'non_tag_other_issue':

				$data=$this->$model->get_non_tag_otherissue_details($_POST);

				echo json_encode($data);

			break;

			case 'ajax':

			    if($_POST['report_type']==1)

			    {

			        $data=$this->$model->get_retagging_details($_POST);

			    }else{

			        $data=$this->$model->get_other_issue_retagging_details($_POST);

			    }



			    echo json_encode($data);

			break;

			// ========== Sales Transfer (ST) Retagging Endpoints ==========
			case 'st_ajax':
				$data = $this->$model->get_st_retagging_details($_POST);
				echo json_encode($data);
			break;

			case 'st_old_metal':
				$data = $this->$model->get_st_old_metal_details($_POST);
				echo json_encode($data);
			break;

			case 'st_partly_sale':
				$data = $this->$model->get_st_partly_sale_details($_POST);
				echo json_encode($data);
			break;

		}

    }



	function create_retag()

    {



		//$report_type 1-SALES RETURN , 3-PARTLY SALE , 4-OLD METAL

		//$tag_process 1-RE-TAG , 2-OTHER ISSUE , 4-NON TAG , 5-ACC STOCK

		$model="ret_tag_model";

		$reqdata        = $this->input->post('req_data');

		// Always use Head Office branch for lot creation — consistent with
		// Purchase Lot (get_headOffice) and Old Metal Process (get_branch_details)
		$ho             = $this->ret_tag_model->get_headOffice();
		$id_branch      = (!empty($ho) && !empty($ho['id_branch'])) ? $ho['id_branch'] : $this->input->post('id_branch');

		// Inject resolved id_branch into $_POST so generateRetaglot and
		// generateRetagNontaglot also receive Head Office — not the frontend value
		$_POST['id_branch'] = $id_branch;

		$tag_process    = $this->input->post('tag_process');

		$report_type    = $this->input->post('report_type');

		$id_product     = $this->input->post('id_product');

		$id_design      = $this->input->post('id_design');

		$id_sub_design  = $this->input->post('id_sub_design');

		$id_karigar     = $this->input->post('id_karigar');

		$id_category    = $this->input->post('id_category');

		$id_purity      = $this->input->post('id_purity');

		$id_section     = $this->input->post('id_section');

		$remark      = $this->input->post('remark');

		$total_pcs      = $this->input->post('total_pcs');

		$total_gross_wt   = $this->input->post('total_gross_wt');

		$total_net_wt        = $this->input->post('total_net_wt');

		$total_dia_wt        = $this->input->post('total_dia_wt');

		$total_item_purity   = $this->input->post('total_item_purity');

		$total_amount   = $this->input->post('total_amount');

		$avg_purity_per   = $this->input->post('avg_purity_per');

		//echo "<pre>";print_r($_POST);exit;

		$tag_id='';

		$dCData = $this->admin_settings_model->getBranchDayClosingData($id_branch);

		$tag_datetime = ($dCData['entry_date'] == date("Y-m-d") ? date("Y-m-d H:i:s") : $dCData['entry_date']);

		if($tag_process==6) // Add to pocket
		{
			//echo"<pre>";print_r($_POST);exit;
			$pocket_no = $this->ret_metal_process_model->code_number_generator();   //Bill Number Generate
			//print_r($pocket_no);exit;
			$insData=array(
				'date' 		 => $tag_datetime,
				'id_branch'       =>$id_branch,
				'trans_type' => 1,
				'pocket_no'  => $pocket_no,
				'piece'	    =>$total_pcs,
				'gross_wt'	=>$total_gross_wt,
				'net_wt'	=>$total_net_wt,
				'dia_wt'    =>$total_dia_wt,
				'avg_purity'=>$avg_purity_per,
				'total_purity'=>$total_item_purity,
				'amount'    =>$total_amount,
				'created_by'=>$this->session->userdata('uid'),
				'created_on' => date("Y-m-d H:i:s"),
			);
			$this->db->trans_begin();
			$insId = $this->$model->insertData($insData,'ret_old_metal_pocket');

			foreach($reqdata as $val)
			{
				$metal_details=array(
					'id_metal_pocket'	=>$insId,
					'id_metal_type'		=>(isset($val['id_old_metal_type']) ? $val['id_old_metal_type']:NULL),
					'old_metal_sale_id' =>(isset($val['old_metal_sale_id']) ? $val['old_metal_sale_id']:NULL),
					'id_category'       =>(isset($val['cat_id'])?$val['cat_id']:NULL),
					'tag_id'            =>(isset($val['tag_id']) ? $val['tag_id']:NULL),
					'type'			    =>($report_type==1 ? 2 : ($report_type==4 ? 1 : 3)),//1-Old Metal,2-Sales Return
					'piece'			    =>$val['piece'],
					'gross_wt'			=>$val['gross_wt'],
					'net_wt'			=>$val['net_wt'],
					'item_cost'			=>$val['item_cost'],
					'purity'			=>$val['purity'],
					'diawt'			    =>$val['diawt'],
					'rate_per_gram'     =>$val['rate_per_gram'],
				);
				$status=$this->$model->insertData($metal_details,'ret_old_metal_pocket_details');
				if($status)
				{
					$this->$model->updateData(array('is_pocketed'=>1),'old_metal_sale_id',$val['old_metal_sale_id'], 'ret_bill_old_metal_sale_details');
					$stone_details = (array)json_decode($val['stone_details'],true);

					if(sizeof($stone_details)>0)
					{
						foreach($stone_details as $stn)
						{
							$acc_stone_data = array(
								'id_pocket_details'    => $status,
								'stone_id'              => $stn['stone_id'],
								'stone_pcs'             => $stn['stone_pcs'],
								'stone_wt'              => $stn['stone_wt'],
							);
							$accInsert = $this->$model->insertData($acc_stone_data,'ret_old_metal_pocket_stone_details');

						}

					}

				}
			}
			if($this->db->trans_status()===TRUE)
			{
				$this->db->trans_commit();

				$log_data = array(

					'id_log'        => $this->session->userdata('id_log'),

					'event_date'    => date("Y-m-d H:i:s"),

					'module'        => 'Retagging',

					'operation'     => 'Add',

					'record'        =>  $insId,

					'remark'        => 'Pocket added successfully'

					);

				$this->log_model->log_detail('insert','',$log_data);

				$responseData=array('status'=>TRUE,'message'=>'Pocket Created Successfully!');
			}
			else
			{
				$this->db->trans_rollback();
				$responseData=array('status'=>false,'message'=>'Unable to Proceed Your Request');
			}
		}

		else
		{

			$tag_process_data=array(

						'date_add'        =>date("Y-m-d H:i:s"),

						'id_branch'       =>$id_branch,

						'type'            =>$report_type,

						'process_for'     =>$tag_process,

						'id_ret_category' =>(isset($id_category) ? ($id_category!='' ? $id_category :NULL) :NULL),

						'id_ret_product'  =>(isset($id_product) ? ($id_product!='' ? $id_product :NULL) :NULL),

						'id_design'       =>(isset($id_design) ? ($id_design!='' ? $id_design :NULL) :NULL),

						'id_sub_design'   =>(isset($id_sub_design) ? ($id_sub_design!='' ? $id_sub_design :NULL) :NULL),

						'id_purity'       =>(isset($id_purity) ? ($id_purity!='' ? $id_purity :NULL) :NULL),

						'id_karigar'       =>(isset($id_karigar) ? ($id_karigar!='' ? $id_karigar :NULL) :NULL),

						'created_on'      =>date("Y-m-d H:i:s"),

						'created_by'      =>$this->session->userdata('uid'),

						);

			$this->db->trans_begin();

			$process_id = $this->$model->insertData($tag_process_data,'ret_acc_stock_process');

			if($process_id)

			{



				foreach($reqdata as $key => $tag)

				{

					$acc_process_details = array(

												'id_process'        => $process_id,

												'ref_no'            => ($report_type==4 ? $tag['old_metal_sale_id'] : ($report_type==5 ? $tag['bill_det_id'] : ($report_type==7 ? $tag['bt_trans_id'] : $tag['tag_id']))),

												'piece'             => $tag['piece'],

												'gross_wt'          => $tag['gross_wt'],

												'net_wt'            => $tag['net_wt'],

												'less_wt'           => (isset($tag['less_wt']) ? $tag['less_wt'] : 0),

												'narration'         => $tag['narration'],

												);

					$id_process_details = $this->$model->insertData($acc_process_details,'ret_acc_stock_process_details');



					$stone_details = (array)json_decode($tag['stone_details'],true);



					if(sizeof($stone_details)>0)

					{

						foreach($stone_details as $stn)

						{

							$acc_stone_data = array(

								'id_process_details'    => $id_process_details,

								'stone_id'              => $stn['stone_id'],

								'stone_pcs'             => $stn['stone_pcs'],

								'stone_wt'              => $stn['stone_wt'],

							);

							$accInsert = $this->$model->insertData($acc_stone_data,'ret_acc_stock_process_stone_details');

						}

					}

				}



				if($report_type==1)

				{

						if($tag_process==1)

						{

							$process_status = $this->generateRetaglot($reqdata,$process_id,$_POST);

						}

						else if($tag_process==4)

						{

							$process_status = $this->generateRetagNontaglot($reqdata,$process_id,$_POST);

						}

						else if($tag_process==2)

						{

							$process_status = array('status'=>true,'message'=>'Other Issue Successfuly..');

						}

				}

				else if($report_type==3)

				{

						if($tag_process==1)

						{

							$process_status = $this->generateRetaglot($reqdata,$process_id,$_POST);

						}

						else if($tag_process==4)

						{

							$process_status = $this->generateRetagNontaglot($reqdata,$process_id,$_POST);

						}

						else if($tag_process==2 && $id_process_details)

						{

							$process_status = array('status'=>true,'message'=>'Other Issue Successfuly..');

						}

				}

				else if($report_type==4)

				{

						if($tag_process==1)

						{

							$process_status = $this->generateRetaglot($reqdata,$process_id,$_POST);

						}

						else if($tag_process==4)

						{

							$process_status = $this->generateRetagNontaglot($reqdata,$process_id,$_POST);

						}

						else if($tag_process==2)

						{

							$process_status = array('status'=>true,'message'=>'Other Issue Successfuly..');

						}

				}

				else if($report_type==6)

				{

						if($tag_process==1)

						{

							$process_status = $this->generateRetaglot($reqdata,$process_id,$_POST);

						}

						else if($tag_process==4)

						{

							$process_status = $this->generateRetagNontaglot($reqdata,$process_id,$_POST);

						}

						else if($tag_process==2)

						{

							$process_status = array('status'=>true,'message'=>'Other Issue Successfuly..');

						}

				}

				else if($report_type==5)
				{
					if($tag_process==1)
					{
						$process_status = $this->generateRetaglot($reqdata,$process_id,$_POST);
					}
					else if($tag_process==4)
					{
						$process_status = $this->generateRetagNontaglot($reqdata,$process_id,$_POST);
					}
					else if($tag_process==2)
					{
						$process_status = array('status'=>true,'message'=>'Other Issue Successfuly..');
					}
				}

				else if($report_type==7)
				{
					if($tag_process==1)
					{
						$process_status = $this->generateRetaglot($reqdata,$process_id,$_POST);
					}
					else if($tag_process==4)
					{
						$process_status = $this->generateRetagNontaglot($reqdata,$process_id,$_POST);
					}
					else if($tag_process==2)
					{
						$process_status = array('status'=>true,'message'=>'Other Issue Successfuly..');
					}
				}



				/*else if($report_type==5 && $tag_process==4) //NON TAG SALES RETURN TO NON TAG STOCK

				{

					foreach($reqdata as $tag)

					{

							$this->$model->insertData(array('id_process'=>$process_id,'ref_no'=>$tag['bill_det_id'],'gross_wt' => $tag['gross_wt'],'net_wt'=> $tag['net_wt'],'piece'=>$tag['piece']),'ret_acc_stock_process_details');



							$this->$model->updateData(array('acc_stock_process'=>1),'bill_det_id',$tag['bill_det_id'],'ret_bill_details');



							$existData=array('id_product'=>$id_product,'id_design'=>$id_design,'id_sub_design'=>$id_sub_design,'id_branch'=>$id_branch);



							$isExist = $this->$model->checkNonTagItemExist($existData);



							if($isExist['status'] == TRUE)

							{



								$nt_data = array(

								'id_nontag_item'=> $isExist['id_nontag_item'],

								'gross_wt'      => $tag['gross_wt'],

								'net_wt'        => $tag['net_wt'],

								'no_of_piece'	=> $tag['piece'],

								'updated_by'	=> $this->session->userdata('uid'),

								'updated_on'	=> date('Y-m-d H:i:s'),

								);

								$this->$model->updateNTData($nt_data,'+');



								$non_tag_data=array(

									'product'	    => $id_product,

									'design'	    => $id_design,

									'id_sub_design'	=> $id_sub_design,

									'gross_wt'      => $tag['gross_wt'],

									'net_wt'        => $tag['net_wt'],

									'from_branch'	=> NULL,

									'to_branch'	    => $id_branch,

									'status'	    => 0,

									'no_of_piece'	=> $tag['piece'],

									'date'          => $tag_datetime,

									'created_on'    => date("Y-m-d H:i:s"),

									'created_by'    =>  $this->session->userdata('uid')

								);

								$this->$model->insertData($non_tag_data,'ret_nontag_item_log');

							}

							else

							{

								$nt_data=array(

									'branch'	    => $id_branch,

									'product'	    => $id_product,

									'design'	    => $id_design,

									'id_sub_design'	=> $id_sub_design,

									'gross_wt'      => $tag['gross_wt'],

									'net_wt'        => $tag['net_wt'],

									'no_of_piece'	=> $tag['piece'],

									'created_on'    => date("Y-m-d H:i:s"),

									'created_by'    => $this->session->userdata('uid')

								);

								$this->$model->insertData($nt_data,'ret_nontag_item');



								$non_tag_data=array(

								'product'	    => $id_product,

								'design'	    => $id_design,

								'id_sub_design'	=> $id_sub_design,

								'gross_wt'      => $tag['gross_wt'],

								'net_wt'        => $tag['net_wt'],

								'from_branch'	=> NULL,

								'to_branch'	    => $id_branch,

								'status'	    => 0,

								'no_of_piece'	=> $tag['piece'],

								'date'          => $tag_datetime,

								'created_on'    => date("Y-m-d H:i:s"),

								'created_by'    =>  $this->session->userdata('uid')

								);

								$this->$model->insertData($non_tag_data,'ret_nontag_item_log');



							}

					}

				}*/

			}





			if($this->db->trans_status()===TRUE && $process_status['status']==TRUE)

			{

				$this->db->trans_commit();


				$log_data = array(

					'id_log'        => $this->session->userdata('id_log'),

					'event_date'    => date("Y-m-d H:i:s"),

					'module'        => 'Retagging',

					'operation'     => 'Add',

					'record'        => $process_id,

					'remark'        => $process_status['message']

					);

				$this->log_model->log_detail('insert','',$log_data);

				$responseData=array('status'=>TRUE,'message'=>$process_status['message'],'id_process'=>$process_id);

				// redirect('admin_ret_tagging/retagging/list');

			}

			else

			{

				$this->db->trans_rollback();

				$responseData=array('status'=>false,'message'=>'Unable to Proceed Your Request');

			}

		}

        echo json_encode($responseData);

    }



    function generateRetaglot($reqdata,$process_id,$data)

    {

        $returnData = [];

        $model = "ret_tag_model";

        $total_grs_weight=0;

		$total_net_weight = 0;

		$total_pcs  = 0;

		$total_less_wt  = 0;



		$id_branch      = $data['id_branch'];

		$id_category    = $data['id_category'];

		$id_purity      = $data['id_purity'];

		$id_karigar     = $data['id_karigar'];

		$id_product     = $data['id_product'];

		$id_design      = $data['id_design'];

		$id_sub_design  = $data['id_sub_design'];

		$remark         = $data['remark'];



		foreach($reqdata as $key => $tag)

		{

			$total_grs_weight+=$tag['gross_wt'];

			$total_net_weight+=$tag['net_wt'];

			$total_less_wt+=$tag['less_wt'];

			$total_pcs+=$tag['piece'];

			$stone_details = (array)json_decode($tag['stone_details'],true);

		}

		if($total_grs_weight>0 || $total_pcs > 0)

		{

			$Lotdata = array(

				'lot_date'				=> date("Y-m-d H:i:s"),

				'lot_from'				=> 6,

				'lot_type'				=> 1,

				'id_acc_process'		=> $process_id,

				'lot_received_at'		=> $id_branch,

				'created_branch'		=> $id_branch,

			    'id_category'			=> $id_category,

				'id_purity'				=> $id_purity,

				'gold_smith'		    => $id_karigar,

				'narration'      		=> $remark,

				'created_on'	  		=> date("Y-m-d H:i:s"),

				'created_by'      		=> $this->session->userdata('uid')

			);

			$this->db->trans_begin();

			$lotId = $this->$model->insertData($Lotdata,'ret_lot_inwards');




			if($lotId)

			{

			   $item_details=array(

			       'lot_no'             =>$lotId,

			       'lot_id_category'    =>$id_category,

			       'lot_id_purity'      =>$id_purity,

			       'lot_product'        =>$id_product,

				   'lot_id_design'      =>$id_design,

				   'id_sub_design'      =>$id_sub_design,

			       'gold_smith'         =>$id_karigar,

			       'no_of_piece'        =>$total_pcs,

			       'gross_wt'           =>$total_grs_weight,

			       'net_wt'             =>$total_net_weight,

			       'less_wt'            =>$total_less_wt,
			       'tax_percentage'     =>0);

			   $lotInwId = $this->$model->insertData($item_details,'ret_lot_inwards_detail');



			    if(sizeof($stone_details)>0)

				{

					foreach($stone_details as $stn)

					{

						$stone_data=array(

							'id_lot_inward_detail'  => $lotInwId,

							'stone_id'              => $stn['stone_id'],

							'uom_id'                => $stn['uom_id'],

							'stone_pcs'             => $stn['stone_pcs'],

							'stone_wt'              => $stn['stone_wt'],

						);

						$stoneInsert = $this->$model->insertData($stone_data,'ret_lot_inwards_stone_detail');



					}

				}

			}else{

			    $returnData = array('status'=>false,'message'=>'Unable to generate lot');

			}

		}

		if($this->db->trans_status()===TRUE)

        {

            $this->db->trans_commit();

			$log_data = array(

				'id_log'        => $this->session->userdata('id_log'),

				'event_date'    => date("Y-m-d H:i:s"),

				'module'        => 'Retagging',

				'operation'     => 'Add',

				'record'        =>  $lotId,

				'remark'        => 'Lot Generated successfully'

				);

			$this->log_model->log_detail('insert','',$log_data);

            $returnData = array('status'=>true,'message'=>'Lot Generated Successfully..');

        }

        else

        {

            $this->db->trans_rollback();

            $returnData = array('status'=>false,'message'=>'Unable to generate lot');

        }

		return $returnData;

    }



    function generateRetagNontaglot($reqdata,$process_id,$data)

    {

        $returnData = [];

        $model = "ret_tag_model";

        $total_grs_weight=0;

		$total_net_weight = 0;

		$total_pcs  = 0;

		$total_less_wt  = 0;



		$id_branch      = $data['id_branch'];

		$id_category    = $data['id_category'];

		$id_purity      = $data['id_purity'];

		$id_karigar     = $data['id_karigar'];

		$id_product     = $data['id_product'];

		$id_design      = $data['id_design'];

		$id_sub_design  = $data['id_sub_design'];

		$remark          = $data['remark'];

		$id_section      = $data['id_section'];

		foreach($reqdata as $key => $tag)

		{

			$total_grs_weight+=$tag['gross_wt'];

			$total_net_weight+=$tag['net_wt'];

			$total_less_wt+=$tag['less_wt'];

			$total_pcs+=$tag['piece'];

			$stone_details = (array)json_decode($tag['stone_details'],true);

		}

		if($total_grs_weight>0)

		{

			$Lotdata = array(

				'lot_date'				=> date("Y-m-d H:i:s"),

				'lot_from'				=> 6,

				'lot_type'				=> 1,

				'stock_type'		    => 2,

				'id_acc_process'		=> $process_id,

				'lot_received_at'		=> $id_branch,

				'created_branch'		=> $id_branch,

			    'id_category'			=> $id_category,

				'id_purity'				=> $id_purity,

				'gold_smith'		    => $id_karigar,

				'narration'      		=> $remark,

				'created_on'	  		=> date("Y-m-d H:i:s"),

				'created_by'      		=> $this->session->userdata('uid')

			);

			$this->db->trans_begin();

			$lotId = $this->$model->insertData($Lotdata,'ret_lot_inwards');



			if($lotId)

			{

			   $item_details=array(

			       'lot_no'             =>$lotId,

			       'lot_id_category'    =>$id_category,

			       'lot_id_purity'      =>$id_purity,

			       'lot_product'        =>$id_product,

				   'lot_id_design'      =>$id_design,

				   'id_sub_design'      =>$id_sub_design,

				   'id_section'          =>$id_section,

			       'gold_smith'         =>$id_karigar,

			       'no_of_piece'        =>$total_pcs,

			       'gross_wt'           =>$total_grs_weight,

			       'net_wt'             =>$total_net_weight,

			       'less_wt'            =>$total_less_wt,
			       'tax_percentage'     =>0);

			   $lotInwId = $this->$model->insertData($item_details,'ret_lot_inwards_detail');



			    if(sizeof($stone_details)>0)

				{

					foreach($stone_details as $stn)

					{

						$stone_data=array(

							'id_lot_inward_detail'  => $lotInwId,

							'stone_id'              => $stn['stone_id'],

							'uom_id'                => $stn['uom_id'],

							'stone_pcs'             => $stn['stone_pcs'],

							'stone_wt'              => $stn['stone_wt'],

						);

						$stoneInsert = $this->$model->insertData($stone_data,'ret_lot_inwards_stone_detail');



					}

				}



				$existData=array('id_product'=>$id_product,'id_design'=>$id_design,'id_sub_design'=>$id_sub_design,'id_branch'=>$id_branch);

                $isExist = $this->$model->checkNonTagItemExist($existData);



                if($isExist['status'] == TRUE)

                {

                    $nt_data = array(

                    'id_nontag_item'   => $isExist['id_nontag_item'],

                    'gross_wt'         => $tag['gross_wt'],

                    'net_wt'           => $tag['net_wt'],

                    'no_of_piece'      => 0,

                    'updated_by'	   => $this->session->userdata('uid'),

                    'updated_on'	   => date('Y-m-d H:i:s'),

                    );

                    $this->$model->updateNTData($nt_data,'+');



                    $non_tag_data=array(

                    'product'	    => $id_product,

                    'design'	    => $id_design,

                    'id_sub_design'	=> $id_sub_design,

                    'gross_wt'      => $tag['gross_wt'],

                    'net_wt'        => $tag['net_wt'],

                    'from_branch'	=> NULL,

                    'to_branch'	    => $id_branch,

                    'status'	    => 0,

                    'no_of_piece'   => 0,

                    'date'          => $tag_datetime,

                    'created_on'    => date("Y-m-d H:i:s"),

                    'created_by'    => $this->session->userdata('uid')

                    );

                    $this->$model->insertData($non_tag_data,'ret_nontag_item_log');

                }else{

                    $nt_data=array(

                        'branch'	    => $id_branch,

                        'product'	    => $id_product,

                        'design'	    => $id_design,

                        'id_sub_design'	=> $id_sub_design,

                        'no_of_piece'   => 0,

                        'gross_wt'      => $tag['gross_wt'],

                        'net_wt'        => $tag['net_wt'],

                        'created_on'    => date("Y-m-d H:i:s"),

                        'created_by'    => $this->session->userdata('uid')

                    );

                    $this->$model->insertData($nt_data,'ret_nontag_item');



                    $non_tag_data=array(

                    'product'	    => $id_product,

                    'design'	    => $id_design,

                    'id_sub_design'	=> $id_sub_design,

                    'gross_wt'      =>$tag['gross_wt'],

                    'net_wt'        =>$tag['net_wt'],

                    'from_branch'	=> NULL,

                    'to_branch'	    => $id_branch,

                    'status'	    => 0,

                    'no_of_piece'	=> 0,

                    'date'          => $tag_datetime,

                    'created_on'    => date("Y-m-d H:i:s"),

                    'created_by'    =>  $this->session->userdata('uid')

                    );

                    $this->$model->insertData($non_tag_data,'ret_nontag_item_log');

                }



			}else{

			    $returnData = array('status'=>false,'message'=>'Unable to generate lot');

			}

		}

		if($this->db->trans_status()===TRUE)

        {

            $this->db->trans_commit();

			$log_data = array(

				'id_log'        => $this->session->userdata('id_log'),

				'event_date'    => date("Y-m-d H:i:s"),

				'module'        => 'Retagging',

				'operation'     => 'Add',

				'record'        =>  $lotId,

				'remark'        => 'Lot Generated successfully'

				);

			$this->log_model->log_detail('insert','',$log_data);

            $returnData = array('status'=>true,'message'=>'Lot Generated Successfully..');

        }

        else

        {

            $this->db->trans_rollback();

            $returnData = array('status'=>false,'message'=>'Unable to generate lot');

        }

		return $returnData;

    }



    //Re-Tagging

    // collection mapping

    function get_ActiveCollection()

    {

        $model = "ret_tag_model";

        $data=$this->$model->get_ActiveCollection();

		echo json_encode($data);

    }

    function collection_mapping($type="",$id="")

	{

	    $model = "ret_tag_model";

		$SETT_MOD = self::SETT_MOD;

		switch($type)

		{

			case 'list':

					$data['main_content'] = "tagging/collection/list" ;

					$data['access'] = $this->$SETT_MOD ->get_access('admin_ret_tagging/collection_mapping/list');

        			$this->load->view('layout/template', $data);

			break;

			case 'add':

					$data['main_content'] = "tagging/collection/form" ;

        			$this->load->view('layout/template', $data);

			break;

			case 'cancel':

                $this->db->trans_begin();

                $status = $this->$model->updateData(array('status'=>2,'updated_by'=>$this->session->userdata('uid'),'updated_on'=>date("Y-m-d H:i:s")),'id_tag_mapping',$id,'ret_tag_collection_mapping');

                if($status)

                {

                    $tag_details = $this->$model->get_collection_mapping_det($id);

                    foreach($tag_details as $tag)

                    {

                        $this->$model->updateData(array('is_colection_tag'=>0),'tag_id',$tag['tag_id'],'ret_taging');

                    }

                }

                if( $this->db->trans_status()===TRUE)

                {

                $this->db->trans_commit();

				$log_data = array(

					'id_log'        => $this->session->userdata('id_log'),

					'event_date'    => date("Y-m-d H:i:s"),

					'module'        => 'Collection Mapping',

					'operation'     => 'Cancel',

					'record'        =>  $status,

					'remark'        => 'Collection Mapping Cancelled successfully'

					);

					$this->log_model->log_detail('insert','',$log_data);

                $this->session->set_flashdata('chit_alert', array('message' => 'Mapping Cancelled successfully','class' => 'success','title'=>'Collection Mapping'));

                }

                else

                {

                $this->db->trans_rollback();

                $this->session->set_flashdata('chit_alert', array('message' => 'Unable to proceed your request','class' => 'danger','title'=>'Collection Mapping'));

                }

                redirect('admin_ret_tagging/collection_mapping/list');

			break;

			case 'ajax':

			    $list = $this->$model->get_collection_mapping_list($_POST);

			  	$access = $this->admin_settings_model->get_access('admin_ret_tagging/collection_mapping/list');

		        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;

		}

    }

    function create_tag_collection()

    {

        $responseData=array();

        $model     = "ret_tag_model";

        $req_data  = $_POST['req_data'];

        $total_pcs = $_POST['total_pcs'];

        $tot_gwt   = $_POST['tot_gwt'];

        $tot_nwt   = $_POST['tot_nwt'];

        $id_collection   = $_POST['id_collection'];

        $ref_no          = $this->$model->generateRefNo();

        $insData=array(

        'id_collection_master'=>$id_collection,

        'ref_no'            =>$ref_no,

        'total_pcs'         =>$total_pcs,

        'total_gwt'         =>$tot_gwt,

        'total_nwt'         =>$tot_nwt,

        'date_add'	  	    =>date("Y-m-d H:i:s"),

        'created_on'	  	=>date("Y-m-d H:i:s"),

        'created_by'      	=>$this->session->userdata('uid'),

        );

        $this->db->trans_begin();

    	$insId = $this->$model->insertData($insData,'ret_tag_collection_mapping');

    	if($insId)

    	{

    	    foreach($req_data as $tag)

    	    {

    	        $tagDetails=array(

                    'id_tag_mapping'=>$insId,

                    'tag_id'        =>$tag['tag_id'],

                    );

                $status = $this->$model->insertData($tagDetails,'ret_tag_collection_mapping_details');

                if($status)

                {

                    $this->$model->updateData(array('is_colection_tag'=>1),'tag_id',$tag['tag_id'],'ret_taging');

                }

    	    }

    	}

    	if($this->db->trans_status()===TRUE)

        {

            $this->db->trans_commit();

			$log_data = array(

				'id_log'        => $this->session->userdata('id_log'),

				'event_date'    => date("Y-m-d H:i:s"),

				'module'        => 'Collection Mapping',

				'operation'     => 'Add',

				'record'        =>  $insId,

				'remark'        => 'Collection Mapping added successfully'

				);

			$this->log_model->log_detail('insert','',$log_data);

            $responseData=array('status'=>TRUE,'message'=>'Collection Mapped Successfully.');

        }

        else

        {

            $this->db->trans_rollback();

            $responseData=array('status'=>false,'message'=>'Unable to Proceed Your Request');

        }

        echo json_encode($responseData);

    }

    // collection mapping

    public function get_order_linked_tags()

    {

    	$model = "ret_tag_model";

    	$data = $this->$model->get_order_linked_tags($_POST['searchTxt'],$_POST['id_branch'],$_POST['old_tag_id']);

    	echo json_encode($data);

    }

    public function unlink_order_tags(){

	    $model = "ret_tag_model";

		$this->db->trans_begin();

		foreach($_POST['unlink_data'] as $value)

		{

			//print_r($value);exit;

			if($value['id_orderdetails'] != '')

			{

				$update_order_status = array('orderstatus' => 3);

				$order_status = $this->$model->updateData($update_order_status,'id_orderdetails',$value['id_orderdetails'],'customerorderdetails');

		    }

			if($order_status)

			{

				$update_tag_id = array('id_orderdetails' => NULL);

				$order_tag = $this->$model->updateData($update_tag_id,'tag_id',$value['tag_id'],'ret_taging');

			}

			 if($this->db->trans_status()===TRUE)

					{

						$this->db->trans_commit();

						$this->session->set_flashdata('chit_alert',array('message'=>'Tag Unlinked Successfully.','class'=>'success','title'=>'Tagging'));

                        $dataset =1;

					}

					else

					{

						$this->db->trans_rollback();

						$this->session->set_flashdata('chit_alert',array('message'=>'Unable to proceed your request','class'=>'danger','title'=>'Tagging'));

						$dataset =0;

					}

		}

		echo json_encode($dataset);

	}

	//Image Upload

	function update_tag_img_by_id()

    {
		/*echo "<pre>";
		print_r($_POST);exit;*/

		$current_datetime = date("Y-m-d H:i:s");

		$employee= $this->session->userdata('uid');

        $model="ret_tag_model";

		$id_branch=$this->input->post('id_branch');

        $images=$this->input->post('image');

        $tag_id=$this->input->post('tag_id');

		$imgs_previous = $this->$model->get_img_by_id($tag_id);

		$previous_log_detail = array();

		$updated_log_detail = array();

		$deleted_images = array();

		foreach($imgs_previous as $imp) {

			$img_del = true;

			$prev_img = $imp['image'];

			foreach($images as $img) {

				if($prev_img == $img['image']) {

					$img_del = false;

					break;

				}

			}

			if($img_del) {

				$imgfolder =  self::IMG_PATH."tag/";

				$old_img_path = $imgfolder.$prev_img;

				$imgfolder = $imgfolder."deleted/";

				$new_img_path = $imgfolder.$prev_img;

				if (!is_dir($imgfolder)) {

					mkdir($imgfolder, 0777, TRUE);

				}

				rename($old_img_path, $new_img_path);

				$deleted_images[] = array("delimage" => $prev_img);

			}

		}

		$previous_log_detail[] = array(

			'img_update' => $imgs_previous

		);

		$updated_log_detail[] = array(

			'img_update' => $images, 'deleted_image' => $deleted_images

		);

        $status = $this->$model->deleteData_bulk_img($tag_id);

        if($status)

        {

            foreach($images as $key => $value)

            {

                $arrayimg_tag = array(

                    'tag_id'   => $tag_id,

                    'image'    => $value['image'],

                    'date_add' => date("Y-m-d  H:i:s"),

                    'date_add' => date("Y-m-d  H:i:s"),

                    'is_default' =>$value['is_default']

                 );

                $img_arr[]   = $arrayimg_tag;

                $insImageId = $this->$model->insertData($arrayimg_tag,'ret_taging_images');

            }

			$bulk_update_log = array(

				'edit_datetime' => $current_datetime,

				'edit_tag'		=> $tag_id,

				'edit_branch_id'=> $id_branch,

				'edit_emp_id'	=> $employee,

				'edit_field'	=> 'Image Update',

				'previous_values' => json_encode($previous_log_detail),

				'updated_values'  => json_encode($updated_log_detail)
			);

			$this->$model->insertData($bulk_update_log,'ret_bulk_tag_edit_log');

        }

        echo json_encode($insImageId);

    }

    function get_img_by_id()

    {

        $model="ret_tag_model";

        $data = $this->$model->get_img_by_id($_POST['tag_id']);

        echo json_encode($data);

    }

	//Image Upload

	function get_CustomerOrders()

	{

	    $model="ret_tag_model";

	    $data = $this->$model->get_CustomerOrder($_POST);

	    echo json_encode($data);

	}

	function get_customer_order_details()

	{

	    $model="ret_tag_model";

	    $data = $this->$model->get_CustomerOrdersDetails($_POST);

	    echo json_encode($data);

	}

	function get_mc_va_limit()

	{

		$model="ret_tag_model";

		$this->load->model($model);

		$product_id 	= $_POST['product_id'];

		$design_id 		= $_POST['design_id'];

		$subdesign_id 	= $_POST['subdesign_id'];

	    $data=$this->$model->get_mc_va_limit($product_id, $design_id, $subdesign_id);

	    echo json_encode($data);

	}

	function get_old_tag()

    {

        $model="ret_tag_model";

        $data = $this->$model->get_old_tag($_POST['old_tag_id']);

        echo json_encode($data);

    }

    function generate_tagqrcode_bulk($tags)

	{

		$tag_ids_string = urldecode($tags);

		$tags = explode(',', $tag_ids_string);

		if(count($tags) > 0) {

			$tag_items = array();

			foreach($tags as $tag)

			{

				if($tag > 0) {

					$tag_items[] = $this->get_tagArray($tag);

				}

			}

			if(count($tag_items) > 0) {

				$this->generate_printer_code($tag_items);

			}

		}

	}

	function getTaggedLot()

	{

		$model="ret_tag_model";

		$data = $this->$model->getTaggedLot();

	    echo json_encode($data);

	}

	function getTaggedRefNo()

	{

		$model="ret_tag_model";

		$data = $this->$model->getTaggedRefNo();

	    echo json_encode($data);

	}


	function get_section_details()

	{

		$model="ret_tag_model";

		$data = $this->$model->get_section_details();

	    echo json_encode($data);

	}
	function get_tagArray($tag) {

		$model = "ret_tag_model";

		$bill_model = "ret_billing_model";

		$data=$this->$model->getTagDetails($tag);

		$charge_code = '';
		$charge_amount = 0;

		$charges=$this->$model->getTagCharges($tag);

		foreach($charges as $charge) {
			$charge_code .= ($charge_code == '' ? '' : ',') . $charge['code_charge'];
			$charge_amount += (float)$charge['charge_value'];
		}

		//Update No of Print Taken

		$print_taken = $data['tot_print_taken'];

		$print_taken++;

		$this->$model->updateData(array('tot_print_taken'=>$print_taken), 'tag_id', $tag, 'ret_taging');

		if($data['tag_mark']==1) {

			$this->$model->updateData(array('is_green_tag_printed'=>1),'tag_id',$tag,'ret_taging');

		} else {

			$this->$model->updateData(array('is_green_tag_printed'=>0),'tag_id',$tag,'ret_taging');

		}

		$insData=array(

			'tag_id'	 =>$data['tag_id'],

			'id_employee'=> $this->session->userdata('uid'),

			'print_date' => date('Y-m-d H:i:s'),

		);

		//Update No of Print Taken

		$this->$model->insertData($insData,'ret_tag_duplicate_copy');

		$tag_array = array(

			'product_name'      =>$data['product_name'],

			'design_name'       =>$data['design_name'],

			'sub_design_name'   =>$data['sub_design_name'],

			'product_id'        =>$data['product_short_code'],

			'category_name'		=>$data['category_name'],

			'gross_wt'          =>$data['gross_wt'],

			'net_wt'         	=>$data['net_wt'],

			'tag_id'            =>$data['tag_id'],

			'tag_code'          =>$data['tag_code'],

			'size'              =>$data['size'],

			'code_karigar'      =>$data['code_karigar'],
			
			'id_karigar'      =>$data['id_karigar'],
			
			'vendar_code'		=>$data['vendar_code'],

			'short_code'        =>$data['short_code'],

			'sales_value'       =>$data['sales_value'],

			'sales_mode'        =>$data['sales_mode'],

			'sell_rate'         =>$data['sell_rate'],

			'metal_type'		=>$data['metal_type'],

			'metal_id'			=>$data['metal_id'],

			'metal_short_code'	=>$data['metal_short_code'],

			'tag_mark'          =>$data['tag_mark'],

			'mc_cal_type'       =>$data['mc_cal_type'],

			'tag_mc_value'      =>$data['tag_mc_value'],

			'tag_mc_type'       =>$data['tag_mc_type'],

			'stn_amount'        =>$data['stn_amount'],

			'stn_pieces'        =>$data['stn_pieces'],

			'stn_wt'            =>$data['stn_wt'],

			'stone_type'          =>$data['stone_type'],

			'stuom'             =>$data['stuom'],

			'dia_amount'        =>$data['dia_amount'],

			'dia_pieces'        =>$data['dia_pieces'],

			'dia_wt'        	=>$data['dia_wt'],

			'dia_uom_name'      =>$data['dia_uom_name'],

			'dia_uom_short_code'=>$data['dia_uom_short_code'],

			'hu_id'             =>$data['hu_id'],

			'stuom_short_code'  =>$data['stuom_short_code'],

			'charge_code'		=>$charge_code,

			'charge_amount'		=>$charge_amount,

			'tag_type'          =>$data['tag_type'],

			'piece'             =>$data['piece'],

			'stone_code'        =>$data['stone_code'],

			'stone_details'     =>$data['stone_details'],

			'brnc_scode'        =>$data['brnc_scode'],

			'retail_max_wastage_percent'  =>$data['retail_max_wastage_percent'],

			'product_division'  =>$data['product_division'],

			'display_purity'	=>$data['display_purity'],

			'purity_name'	=>$data['purity_name'],

			'purity_description'	=>$data['purity_description'],

			'calculation_based_on' => $data['calculation_based_on'],

			'tax_type' 			=> $data['tax_type'],

			'tgrp_id'			=> $data['tgrp_id'],

			'other_metal_details' => $this->$model->get_other_metals($tag),

			'narration'			=> $data['narration'],

			'ss_code'			=>$data['ss_code']

		);

		return $tag_array;

	}

	function generate_printer_code($data) {

		$model = "ret_tag_model";

		$content = "";

		$i = 1;

		foreach ($data as $s) {

			$printCode = $this->get_printer_code($s);

			if($printCode != "") {

				if($i != 1) {

					$content = $content . "\r\n";

					$content = $content.$printCode;

				} else {

					$content = $printCode;

				}

				$i++;

			}

		}

		$content = ($content);

		if($content != "") {

			$fileName =  uniqid().'_lmx'. '.prn';

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

		ob_clean();

		echo $content;

	}

	function get_printer_code($data) {

		$view_file = 'tagging/print/tag_prn_default'; 
		
		$printer_code = $this->load->view($view_file, array('tag' => $data), true);
		
		return $printer_code;
	}

	function isEmptySetDefault($value,$default){

	    if($value != '' &&  $value != NULL &&  $value != 'null' ){

	        return $value;

	    }else{

	        return $default;

	    }

	}

	function get_prev_huid()

	{

		$model="ret_tag_model";

        $data = $this->$model->get_prev_huid($_POST['huid']);

        echo json_encode($data);

	}

	function send_order_unlink_otp()
	{
		$model="ret_tag_model";
		//$data           = $this->$model->get_ret_settings('otp_approval_nos');
		$tag_id_unlink    			= $_POST['tag_id_unlink'];
		$id_branch        			= $_POST['id_branch'];
		$id_cus_order_unlink        = $_POST['id_cus_order_unlink'];


		// $id_branch = $this->$model->get_tag_detail($tag_id_unlink);
		$data = $this->$model->getBrnachOtpRegMobile($id_branch);
		$mobile_num     = array(explode(',',$data));
		$sent_otp='';
		$comp_details=$this->admin_settings_model->get_company();

		foreach($mobile_num[0] as $mobile)
			{
				if($mobile)
				{
					$this->session->unset_userdata("Orderunlink_otp");
					$OTP = mt_rand(1001,9999);
					$sent_otp.=$OTP.',';
					$this->session->set_userdata('Orderunlink_otp',$sent_otp);
					$this->session->set_userdata('Orderunlink_otp_exp',time()+300);
					$service = $this->admin_settings_model->get_service_by_code('order_unlink_otp');
					$expiry = 5;
					$otp_gen_time = date("Y-m-d H:i:s");
					$insData=array(
						'mobile'        =>$mobile,
						'otp_code'      =>$OTP,
						'otp_gen_time'  =>date("Y-m-d H:i:s"),
						'module'        =>'Order Unlink',
						'id_emp'        =>$this->session->userdata('uid')
					);
					$this->db->trans_begin();
					$insId = $this->$model->insertData($insData,'otp');
					// print_r($insId);exit;
					 if($insId)
					 {
						if($service['serv_whatsapp'] == 1)
							{

									$order_details = $this->$model->get_cus_order_details($id_cus_order_unlink);

									$message="Hi Your OTP For Order Unlink is :".$OTP." Will expire within ".$expiry." minute,Order no:".($order_details['order_no'])." ".($order_details['product'])." REGARDS ".strtoupper($comp_details['company_name']).".";

									$whatsapp=$this->admin_usersms_model->send_whatsApp_message($mobile,$message);
							}
					 }

				}
			}
			if($insId)
			  {
					  $this->db->trans_commit();
					  $status=array('status'=>true,'msg'=>'OTP sent Successfully');
			  }
			  else
			  {
					  $this->db->trans_rollback();
					  $status=array('status'=>false,'msg'=>'Unabe To Send Try Again');
			  }
			echo json_encode($status);
	}

	function verify_order_unlink_otp()
	{
			$model                ="ret_tag_model";
			$post_otp             =$this->input->post('otp');
			$session_otp          =$this->session->userdata('Orderunlink_otp');

			$otp                  = array(explode(',',$session_otp));
			foreach($otp[0] as $OTP)
			{
					if($OTP==$post_otp)
					{
						if(time() >= $this->session->userdata('Orderunlink_otp_exp'))
						{
							$this->session->unset_userdata('Orderunlink_otp');
							$this->session->unset_userdata('Orderunlink_otp_exp');
							$status=array('status'=>false,'msg'=>'OTP has been expired');
						}
						else
						{
							$updData=array(
                                    'is_verified'=>1,
                                    'verified_time'=>date("Y-m-d H:i:s"),
								);
							  $this->db->trans_begin();
							  $update_otp=$this->$model->updateData($updData,'otp_code',$post_otp,'otp');
							  if($update_otp)
							  {
							        $status=array('status'=>true,'msg'=>'OTP Verified Successfully..');
							      	$this->db->trans_commit();
							  }else{
							         $status=array('status'=>false,'msg'=>'Unable to Proceed Your Request..');
							      	$this->db->trans_rollback();
							  }
						}
						break;
					}
					else
					{
						$status=array('status'=>false,'msg'=>'Please Enter Valid OTP');
					}
			}
		  	echo json_encode($status);
	}


	function calculate_base_value_tax($taxcallrate, $taxgroup, $tax_details){

		$totaltax = 0;

		foreach($tax_details as $taxitem) {

			if($taxitem['tgi_tgrpcode'] == $taxgroup) {

				if($taxitem['tgi_calculation'] == 1) {

					if($taxitem['tgi_type'] == 1){

						$totaltax += $taxcallrate * ($taxitem['tax_percentage']/100);

					}else{

						$totaltax -= $taxcallrate * ($taxitem['tax_percentage']/100);

					}

				}

			}

		}

		return $totaltax;

	}

	function calculate_arrived_value_tax($taxcallrate, $taxgroup, $tax_details){

		$totaltax = 0;

		foreach($tax_details as $taxitem) {

			if($taxitem['tgi_tgrpcode'] == $taxgroup) {

				if($taxitem['tgi_calculation'] == 2) {

					if($taxitem['tgi_type'] == 1){

						$totaltax += $taxcallrate * ($taxitem['tax_percentage']/100);

					}else{

						$totaltax -= $taxcallrate * ($taxitem['tax_percentage']/100);

					}

				}

			}

		}

		return $totaltax;

	}

	function get_tax_details() {

		$tax_details = $this->ret_billing_model->getAllTaxgroupItems();

		return $tax_details;

	}

	function bulk_tag_edit_log($type="", $id_branch = "", $emp_id = "", $tag_code = "", $from_date = "", $to_date = "") {

	    $model = "ret_tag_model";

		switch($type)

		{

			case 'list':

					$data['main_content'] = "tagging/bulk_edit_log" ;

        			$this->load->view('layout/template', $data);

			break;

			case 'ajax':

			    $list = $this->$model->get_bulk_tag_edit_log_list($id_branch, $emp_id, $tag_code, $from_date, $to_date);

			  	$access = $this->admin_settings_model->get_access('admin_ret_tagging/bulk_tag_edit_log/list');

		        $data = array(

		        					'list'  => $list,

									'access'=> $access

		        				);

				echo json_encode($data);

			break;

		}

    }


	public function update_purchase_cost()

    {

    	$model = "ret_tag_model";

		$employee= $this->session->userdata('uid');

		$lot_rate_calc_type = $_POST['lot_rate_calc_type'];

		$lot_rate = $_POST['lot_rate'];

		$tag_purchase_cost = $_POST['tag_purchase_cost'];

		$lot_purchase_touch = $_POST['lot_purchase_touch'];

		$lot_wastage_percentage = $_POST['lot_wastage_percentage'];

		$lot_making_charge = $_POST['lot_making_charge'];

		$lot_mc_type = $_POST['lot_mc_type'];

		$lot_calc_type = $_POST['lot_calc_type'];

		$tag_purchase_tax = $_POST['tag_purchase_tax'];

		$tag_purchase_taxable = $_POST['tag_purchase_taxable'];

		$stone_details = $_POST['stone_details'];

		$tagId = $_POST['tag_id'];

		$sales_value = $_POST['sales_value'];

		$net_wt = $_POST['net_wt'];

		$less_wt = $_POST['less_wt'];

		$gross_wt = $_POST['gross_wt'];

		$tag_data = $this->$model->getTagDetails($tagId);





		$upd_data=array(



			'net_wt'      =>$net_wt,



			'less_wt'      =>$less_wt,



			'sales_value'      =>$sales_value,



			'lot_rate_calc_type'      =>$lot_rate_calc_type,



			'lot_rate'               =>$lot_rate,



			'tag_purchase_cost'      =>$tag_purchase_cost,



			'lot_purchase_touch'      =>$lot_purchase_touch,



			'lot_wastage_percentage'      =>$lot_wastage_percentage,



			'lot_making_charge'      =>$lot_making_charge,



			'lot_mc_type'      =>$lot_mc_type,



			'lot_calc_type'      =>$lot_calc_type,



			'tag_purchase_tax'      =>$tag_purchase_tax,



			'tag_purchase_taxable'      =>$tag_purchase_taxable,



			'updated_time'              =>date("Y-m-d H:i:s"),



			'updated_by'                =>$employee,



		);



		//print_r($upd_data);



		$where_array = array('tag_id' => $tagId);



		$this->db->trans_begin();





		$tag_update_id = $this->$model->updateRecord($upd_data, $where_array,'ret_taging');





		$update_status = $tag_update_id > 0 ? true : false;



		$this->$model->deleteData('tag_id',$tagId,'ret_taging_stone');



		if(sizeof($stone_details) > 0)

		{



			foreach($stone_details as $stone)

			{

				$stone_data=array(

								'tag_id'                =>$tagId,

								'pieces'                =>$stone['stone_pcs'],

								'wt'                    =>$stone['stone_wt'],

								'stone_id'              =>$stone['stone_id'],

								'uom_id'                =>$stone['stone_uom_id'],

								'amount'                =>$stone['stone_price'],

								'rate_per_gram'         =>$stone['stone_rate'],

								'is_apply_in_lwt'       =>$stone['show_in_lwt'],

								'stone_cal_type'        =>$stone['stone_cal_type'],

								'pur_rate'       		=>$stone['pur_rate'],

								'pur_cost'     			=>$stone['pur_cost'],

								'stone_quality_id'     	=>$stone['quality_id'],

								);

								//print_r($stone_data);exit;

								$stone_updated_log[]  = $stone_data;

				$stoneInsert = $this->$model->insertData($stone_data,'ret_taging_stone');

			}

		}



		$log_data = array(

			'id_log'        => $this->session->userdata('id_log'),

			'event_date'	=> date("Y-m-d H:i:s"),

			'module'      	=> 'Tag',

			'operation'   	=> 'Bulk Tag Edit',

			'record'        => $tagId,

			'remark'       	=> $remark

		);



	    $this->log_model->log_detail('insert','',$log_data);


		$previous_log_detail[] = array(

			'lot rate calc type' => $tag_data['lot_rate_calc_type'],

			'lot rate' => $tag_data['lot_rate'],

			'tag purchase cost' => $tag_data['tag_purchase_cost'],

			'lot purchase touch' => $tag_data['lot_purchase_touch'],

			'lot wastage percentage' => $tag_data['lot_wastage_percentage'],

			'lot making charge' => $tag_data['lot_making_charge'],

			'lot mc type' => $tag_data['lot_mc_type'],

			'lot calc type' => $tag_data['lot_calc_type'],

			'net wt' => $tag_data['net_wt'],


			'less wt' => $tag_data['less_wt'],

			'sale value' => $tag_data['sales_value'],

			'tag purchase taxable' => $tag_data['tag_purchase_taxable'],

			'tag purchase tax' => $tag_data['tag_purchase_tax'],

			'lot calc type' => $tag_data['lot_calc_type'],

			'stone'	  => $tag_data['stone_details']


		);

		$updated_log_detail[] =  array(


		'net wt'      =>$net_wt,



		'less wt'      =>$less_wt,



		'sales value'      =>$sales_value,



		'lot rate calc type'      =>$lot_rate_calc_type,



		'lot rate'               =>$lot_rate,



		'tag purchase cost'      =>$tag_purchase_cost,



		'lot purchase touch'      =>$lot_purchase_touch,



		'lot wastage percentage'      =>$lot_wastage_percentage,



		'lot making charge'      =>$lot_making_charge,



		'lot mc type'      =>$lot_mc_type,



		'lot calc type'      =>$lot_calc_type,



		'tag purchase tax'      =>$tag_purchase_tax,



		'tag purchase taxable'      =>$tag_purchase_taxable,

		'stone' => $stone_updated_log

	);


	$bulk_update_log = array(

		'edit_datetime' =>  date("Y-m-d H:i:s"),

		'edit_tag'		=> $tagId,

		'edit_branch_id'=>$tag_data['current_branch'],

		'edit_emp_id'	=> $employee,

		'edit_field'	=> "Purchase Cost",

		'previous_values' => json_encode($previous_log_detail),

		'updated_values'  => json_encode($updated_log_detail)
	);

	$this->$model->insertData($bulk_update_log,'ret_bulk_tag_edit_log');



		if($update_status)

		{

			$this->db->trans_commit();



			$message = 'Tags Modified Successfully';



			$status=array('status'=>true,'message'=>$message);





		}else{



			$status=array('status'=>false,'message'=>'Unable to Proceed Your Request');



			$this->db->trans_rollback();

		}



		echo json_encode($status);





    }
	function ret_duplicate_print_log_save()
	{
		// print_r($_POST);exit;
		$model = "ret_tag_model";

		$success = false;

		if (isset($_POST['arrayData'])) {

			$data = json_decode($_POST['arrayData'], true);

			// echo "<pre>"; print_r($data);exit;

			foreach ($data as $dup_tag_print_log) {

				$logData = array(

					'tag_id' 		=> $dup_tag_print_log['tag_id'],

					'printer_name' 	=> $dup_tag_print_log['printer_selected'],

					'id_branch' 	=> $dup_tag_print_log['branchId'],

					'created_by' 	=> $this->session->userdata('uid'),

					'created_on' 	=> date("Y-m-d H:i:s")

				);

				$this->$model->insertData($logData, 'ret_duplicate_print_log');

			}

			$success = true;
			
			// echo "<pre>"; print_r($logData);exit;
		}
		// echo "<pre>"; print_r($data);exit;
		echo json_encode($success);
	}

}

?>